<?php
/*
 * Common functions.
 */
define('ICL_COMMON_FUNCTIONS', true);
/**
 * Calculates relative path for given file.
 * 
 * @param type $file Absolute path to file
 * @return string Relative path
 */
function icl_get_file_relpath($file) {
    $is_https = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
    $http_protocol = $is_https ? 'https' : 'http';
    $base_root = $http_protocol . '://' . $_SERVER['HTTP_HOST'];
    $base_url = $base_root;
    $dir = rtrim(dirname($file), '\/');
    if ($dir) {
        $base_path = $dir;
        $base_url .= $base_path;
        $base_path .= '/';
    } else {
        $base_path = '/';
    }
    $relpath = $base_root
            . str_replace(
                    str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']))
                    , '', str_replace('\\', '/', dirname($file))
    );
    return $relpath;
}

/**
 * Fix WP's multiarray parsing.
 * 
 * @param type $arg
 * @param type $defaults
 * @return type 
 */
function wpv_parse_args_recursive($arg, $defaults) {
    $temp = false;
    if (isset($arg[0])) {
        $temp = $arg[0];
    } else if (isset($defaults[0])) {
        $temp = $defaults[0];
    }
    $arg = wp_parse_args($arg, $defaults);
    if ($temp) {
        $arg[0] = $temp;
    }
    foreach ($defaults as $default_setting_parent => $default_setting) {
        if (!is_array($default_setting)) {
            if (!isset($arg[$default_setting_parent])) {
                $arg[$default_setting_parent] = $default_setting;
            }
            continue;
        }
        if (!isset($arg[$default_setting_parent])) {
            $arg[$default_setting_parent] = $defaults[$default_setting_parent];
        }
        $arg[$default_setting_parent] = wpv_parse_args_recursive($arg[$default_setting_parent], $defaults[$default_setting_parent]);
    }
    
    return $arg;
}

/**
 * Condition function to evaluate and display given block based on expressions
 * 'args' => arguments for evaluation fields
 * 
 * Supported actions and symbols:
 * 
 * Integer and floating-point numbers
 * Math operators: +, -, *, /
 * Comparison operators: &lt;, &gt;, =, &lt;=, &gt;=, !=
 * Boolean operators: AND, OR, NOT
 * Nested expressions - several levels of brackets
 * Variables defined as shortcode parameters starting with a dollar sign
 * empty() function that checks for blank or non-existing fields
 * 
 * 
 */
function wpv_condition($atts) {
	extract(
        shortcode_atts( array('evaluate' => FALSE), $atts )
    );
    
    global $post;
    
    // if in admin, get the post from the URL
    if(is_admin()) {
        // Get post
        if (isset($_GET['post'])) {
            $post_id = (int) $_GET['post'];
        } else if (isset($_POST['post_ID'])) {
            $post_id = (int) $_POST['post_ID'];
        } else {
            $post_id = 0;
        }
        if ($post_id) {
            $post = get_post($post_id);
        }
    }
    
    global $wplogger;

    $logging_string = "Original expression: ". $evaluate;
    
    // evaluate empty() statements for variables
    $empties = preg_match_all("/empty\(\s*\\$(\w+)\s*\)/", $evaluate, $matches);
    
    if($empties && $empties > 0) {
    	for($i = 0; $i < $empties; $i++) {
   		 	$match_var = get_post_meta($post->ID, $atts[$matches[1][$i]], true);
   		 	$is_empty = '1=0';
   		 	
   		 	// mark as empty only nulls and ""  
   		 	if(is_null($match_var) || strlen($match_var) == 0) {
   		 		$is_empty = '1=1';
   		 	}
   		 	
			$evaluate = str_replace($matches[0][$i], $is_empty, $evaluate);
   		 }
    }
    
    // find string variables and evaluate
	$strings_count = preg_match_all('/((\$\w+)|(\'[^\']*\'))\s*([\!<>\=]+)\s*((\$\w+)|(\'[^\']*\'))/', $evaluate, $matches);

	// get all string comparisons - with variables and/or literals
	if($strings_count && $strings_count > 0) {
	    for($i = 0; $i < $strings_count; $i++) {
			
	    	// get both sides and sign
	    	$first_string = $matches[1][$i];
	    	$second_string = $matches[5][$i];
	    	$math_sign =  $matches[4][$i];
	    	
	    	// replace variables with text representation
	    	if(strpos($first_string, '$') === 0) {
	    		$variable_name = substr($first_string, 1); // omit dollar sign
	    		$first_string = get_post_meta($post->ID, $atts[$variable_name], true);
	    	}
	    	if(strpos($second_string, '$') === 0) {
	    		$variable_name = substr($second_string, 1);
	    		$second_string = get_post_meta($post->ID, $atts[$variable_name], true);
	    	}
	    	
	    	// remove single quotes from string literals to get value only
	    	$first_string = (strpos($first_string, '\'') === 0) ? substr($first_string, 1, strlen($first_string) - 2) : $first_string;
	    	$second_string = (strpos($second_string, '\'') === 0) ? substr($second_string, 1, strlen($second_string) - 2) : $second_string; 
	    	
	    	// don't do string comparison if variables are numbers 
	    	if(!(is_numeric($first_string) && is_numeric($second_string))) {
	    		// compare string and return true or false
	    		$compared_str_result = wpv_compare_strings($first_string, $second_string, $math_sign);
	    	
		    	if($compared_str_result) {
					$evaluate = str_replace($matches[0][$i], '1=1', $evaluate);
		    	} else {
		    		$evaluate = str_replace($matches[0][$i], '1=0', $evaluate);
		    	}
	    	}
		}
    }
    
    // find all variable placeholders in expression
    $count = preg_match_all('/\$(\w+)/', $evaluate, $matches);
    
    $logging_string .= "; Variable placeholders: ". var_export($matches[1], true); 
    
    // replace all variables with their values listed as shortcode parameters
    if($count && $count > 0) {
    	// sort array by length desc, fix str_replace incorrect replacement
    	wpv_sort_matches_by_length(&$matches[1]);
    	
	    foreach($matches[1] as $match) {
            $meta = get_post_meta($post->ID, $atts[$match], true);
            if (empty($meta)) {
                $meta = "0";
            }
	    	$evaluate = str_replace('$'.$match, $meta, $evaluate);
	    }
    }
    
    $logging_string .= "; End evaluated expression: ". $evaluate;
    
    $wplogger->log($logging_string, WPLOG_DEBUG);
    // evaluate the prepared expression using the custom eval script
    $result = wpv_evaluate_expression($evaluate);
    
    // return true, false or error string to the conditional caller
    return $result;
}

function wpv_eval_check_syntax($code) {
    return @eval('return true;' . $code);
}

/**
 * 
 * Sort matches array by length so evaluate longest variable names first
 * 
 * Otherwise the str_replace would break a field named $f11 if there is another field named $f1
 * 
 * @param array $matches all variable names
 */
function wpv_sort_matches_by_length($matches) {
	$length = count($matches);
	for($i = 0; $i < $length; $i++) {
		$max = strlen($matches[$i]);
		$max_index = $i;
		
		// find the longest variable
		for($j = $i+1; $j < $length; $j++) {
			if(strlen($matches[$j]) > $max ) {
				$max = $matches[$j];
				$max_index = $j;
			}
		}
		
		// swap
		$temp = $matches[$i];
		$matches[$i] = $matches[$max_index];
		$matches[$max_index] = $temp;
	}
	
}


/**
 * Boolean function for string comparison
 *
 * @param string $first first string to be compared
 * @param string $second second string for comparison
 * 
 * 
 */
function wpv_compare_strings($first, $second, $sign) {
	// get comparison results
	$comparison = strcmp($first, $second);
	
	// verify cases 'less than' and 'less than or equal': <, <=
	if($comparison < 0 && ($sign == '<' || $sign == '<=')) {
		return true;	
	}
	
	// verify cases 'greater than' and 'greater than or equal': >, >=
	if($comparison > 0 && ($sign == '>' || $sign == '>=')) {
		return true;	
	}
	
	// verify equal cases: =, <=, >=
	if($comparison == 0 && ($sign == '=' || $sign == '<=' || $sign == '>=') ) {
		return true;
	}
	
	// verify != case
	if($comparison != 0 && $sign == '!=' ) {
		return true;
	}
	
	// or result is incorrect
	return false;
}

/**
 * 
 * Function that prepares the expression and calls eval()
 * Validates the input for a list of whitechars and handles internal errors if any
 * 
 * @param string $expression the expression to be evaluated 
 */
function wpv_evaluate_expression($expression){
    //Replace AND, OR, ==
    $expression = strtoupper($expression);
    $expression = str_replace("AND", "&&", $expression);
    $expression = str_replace("OR", "||", $expression);
    $expression = str_replace("NOT", "!", $expression);
    $expression = str_replace("=", "==", $expression);
    $expression = str_replace("<==", "<=", $expression);
    $expression = str_replace(">==", ">=", $expression);
    $expression = str_replace("!==", "!=", $expression); // due to the line above
    
    // validate against allowed input characters
	$count = preg_match('/[0-9+-\=\*\/<>&\!\|\s\(\)]+/', $expression, $matches);
	
	// find out if there is full match for the entire expression	
	if($count > 0) {
		if(strlen($matches[0]) == strlen($expression)) {
			 	$valid_eval = wpv_eval_check_syntax("return $expression;");
			 	if($valid_eval) {
			 		return eval("return $expression;");
			 	}
			 	else {
			 		return __("Error while parsing the evaluate expression", 'wpv-views');
			 	}
		}
		else {
			return __("Conditional expression includes illegal characters", 'wpv-views');
		}
	}
	else {
		return __("Correct conditional expression has not been found", 'wpv-views');
	}
	
}

/**
 * class WPV_wpcf_switch_post_from_attr_id
 *
 * This class handles the "id" attribute in a wpv-post-xxxxx shortcode
 * and sets the global $id, $post, and $authordata
 *
 * It also handles types. eg [types field='my-field' id='233']
 *
 * id can be a integer to refer directly to a post
 * id can be $parent to refer to the parent
 *
 * id can also refer to a related post type
 * eg. for a stay the related post types could be guest and room
 * [types field='my-field' id='$guest']
 * [types field='my-field' id='$room']
 */

class WPV_wpcf_switch_post_from_attr_id {

    function __construct($atts){
        $this->found = false;
        
        if (isset($atts['id'])) {

            global $post, $authordata, $id, $WPV_wpcf_post_relationship;                                    
            
            $post_id = 0;
            
            if (strpos($atts['id'], '$') === 0) {
                // Handle the parent if the id is $parent
                if ($atts['id'] == '$parent' && isset($post->post_parent)) {
                    $post_id = $post->post_parent;
                } else {
                    // See if Views has the variable
                    global $WP_Views;
                    if (isset($WP_Views)) {
                        $post_id = $WP_Views->get_variable($atts['id'] . '_id');
                    }
					if ($post_id == 0) {
						// Try the local storage.
						if (isset($WPV_wpcf_post_relationship[$atts['id'] . '_id'])) {
							$post_id = $WPV_wpcf_post_relationship[$atts['id'] . '_id'];
						}
					}
                }
                
            } else {
                $post_id = intval($atts['id']);
            }
            
            if ($post_id > 0) {
            
                $this->found = true;
    
                // save original post 
                $this->post = isset($post) ? clone $post : null;
                if ($authordata) {
                    $this->authordata = clone $authordata;
                } else {
                    $this->authordata = null;
                }
                $this->id = $id;
                
                // set the global post values
                $id = $post_id;
                $post = get_post($id);
				$authordata = new WP_User($post->post_author);
                
            }   
        }
        
    }
    
    function __destruct(){
        if ($this->found) {
            global $post, $authordata, $id;                                    
            
            // restore the global post values.
            $post = isset($this->post) ? clone $this->post : null;
            if ($this->authordata) {
                $authordata = clone $this->authordata;
            } else {
                $authordata = null;
            }
            $id = $this->id;
        }
        
    }
    
}

// Add a filter on the content so that we can record any related posts.
// These can then be used ine id of Types and Views shortcodes
// eg. for a stay we can have
// [types field='my-field' id="$room"] displays my-field from the related room
// [wpv-post-title id="$room"] display the title of the related room

add_filter('the_content', 'WPV_wpcf_record_post_relationship_belongs', 0, 1);

$WPV_wpcf_post_relationship = Array();

function WPV_wpcf_record_post_relationship_belongs($content) {

	global $post, $WPV_wpcf_post_relationship;
    static $related = array();
	
    if (function_exists('wpcf_pr_get_belongs')) {
        
        if (!isset($related[$post->post_type])) {
            $related[$post->post_type] = wpcf_pr_get_belongs($post->post_type);
        }
        if (is_array($related[$post->post_type])) {
            foreach($related[$post->post_type] as $post_type => $data) {
                $related_id = wpcf_pr_post_get_belongs($post->ID, $post_type);
                if ($related_id) {
                    $WPV_wpcf_post_relationship['$' . $post_type . '_id'] = $related_id;
                }
            }
        }
    }
    

	return $content;
}
