<?php
ob_start();
#############################################################################
#############################################################################
# PRODUCT INFO
#############################################################################
#############################################################################

function nccsf_product_info($var) {
    global $wpdb;

    $product_info = array(
#######################################################################
        'item_type' => 'Plugin',
        // ITEM INFO
        'item_id' => '1001',
        'item_name' => 'Coming Soon Pages [Free]',
        'item_version' => '2.1.02',
        'item_url' => 'http://www.netchillies.com/coming-soon-pages-free/',
        'pro_version_url' => 'http://www.netchillies.com/coming-soon-pages/',
        // WORDPRESS VARIABLES
        'options_table_name' => $wpdb->prefix . 'nc_coming_soon_free', // UPDATE
        'settings_page_slug' => 'nccsf_plugin_info',
#######################################################################
        // General Links
        'support_url' => 'http://www.netchillies.com/support',
        'contact_url' => 'http://www.netchillies.com/contact',
        'terms_url' => 'http://www.netchillies.com/terms',
    );

    if ($product_info['item_type'] == 'Plugin') {
        
        $extend_url = WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__));
        $extend_url = str_replace('/framework', '', $extend_url);

        $assets_url = $extend_url.'/assets';
        $modules_url = $extend_url.'/modules';
        
        $product_dir = WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__));
        $product_dir = str_replace('/framework', '', $product_dir);
        
        $deactivate_url = site_url('wp-admin/plugins.php');
    } elseif ($product_info['item_type'] == 'Theme') {
        $extend_url = get_template_directory_uri();
        $assets_url = get_template_directory_uri() . '/extend/assets';
        $modules_url = get_template_directory_uri() . '/extend/modules';
        $product_dir = get_template_directory();
        $deactivate_url = site_url('wp-admin/themes.php');
        
    }
    $assets_url = array(
        'product_url' => $extend_url,
        'extend_url' => $extend_url,
        'assets_url' => $assets_url,
        'deactivate_url' => $deactivate_url,
        'modules_url' => $modules_url,
        'product_dir' => $product_dir,
        
    );

    $return = array_merge($product_info, $assets_url);

    return $return[$var];
}

#############################################################################
#############################################################################
#############################################################################
#############################################################################
# REGISTER ADMIN SCRITPS 
#############################################################################

function nccsf_admin_scripts() {

    global $nccsf_admin_menus;

    foreach ($nccsf_admin_menus as $key => $value) {
        $nccsf_admin_pages[] = $value['page_slug'];
    }

    if (is_admin() && in_array($_GET['page'], $nccsf_admin_pages)) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', nccsf_product_info('assets_url') . '/jquery.min.js');
        wp_enqueue_script('jquery');

        // jQuery UI
        wp_enqueue_script('nccsf_jquery_ui', nccsf_product_info('assets_url') . '/jquery-ui/js/jquery-ui-1.8.11.custom.min.js');


        wp_enqueue_script('nccsf-color-picker', nccsf_product_info('assets_url') . '/colorpicker/js/colorpicker.js');

        // FILE UPLOAD
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('nccsf-file-upload', nccsf_product_info('assets_url') . '/file-upload.js');
        wp_enqueue_script('nccsf-file-upload');

        // CLEDITOR - WYSIWYG

        wp_enqueue_script('nccsf-wysiwyg', nccsf_product_info('assets_url') . '/cleditor/jquery.cleditor.min.js');

        wp_enqueue_script('nccsf-jquery-plugins', nccsf_product_info('assets_url') . '/plugins.js');

        wp_enqueue_script('nccsf-admin-js', nccsf_product_info('assets_url') . '/admin.js');
    }
}

#############################################################################
# REGISTER ADMIN STYLES 
#############################################################################

function nccsf_admin_styles() {
    wp_enqueue_style('thinkbox');
    echo '<link rel="stylesheet" media="screen" href="' . nccsf_product_info('assets_url') . '/jquery-ui/css/smoothness/jquery-ui-1.8.11.custom.css" />'."\n";
    echo '<link rel="stylesheet" media="screen" href="' . nccsf_product_info('assets_url') . '/colorpicker/css/colorpicker.css" type="text/css" />'."\n";
    echo '<link rel="stylesheet" media="screen" href="' . nccsf_product_info('assets_url') . '/cleditor/jquery.cleditor.css" type="text/css" />'."\n";

    echo '<link rel="stylesheet" media="screen" href="' . nccsf_product_info('assets_url') . '/admin.css" type="text/css" />'."\n";
}

#############################################################################
# GET SAVED OPTION
#############################################################################

function nccsf_top($var) {
    global $wpdb;
    $table = nccsf_product_info('options_table_name');
    $query = $wpdb->get_row("SELECT * FROM $table WHERE option_name = '{$var}'");

    $data = @unserialize($query->option_value);
    if ($data === false) {
        return $query->option_value;
    } else {
        return unserialize($query->option_value);
    }
}

#############################################################################
# DATABASE SETUP 
#############################################################################

function nccsf_install_db() {

    global $wpdb, $nccsf_options;

    // Options Table Setup
    $options_table = nccsf_product_info('options_table_name');

    $sql[] = "
        CREATE TABLE IF NOT EXISTS `{$options_table}` (
            `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `option_name` varchar(64) NOT NULL DEFAULT '',
            `option_value` longtext NOT NULL,
            PRIMARY KEY (`option_id`),
            UNIQUE KEY `option_name` (`option_name`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
        ";
    foreach ($sql as $query) {
        if ($wpdb->query($query)) {
            $return[] = 1;
        } else {
            echo mysql_error();
        }
    }


    # Update Options Table
    //-- Get Options Count
    $option_count = 0;
    foreach ($nccsf_options as $options) {
        foreach ($options as $option) {
            $option_count += 1;
        }
    }

    //- Get DB Count

    $query = $wpdb->get_row("SELECT COUNT(option_id) AS db_count FROM $options_table");

    $db_count = $query->db_count;

    if ($db_count == 0) {

        foreach ($nccsf_options as $options) {
            foreach ($options as $option) {
                
                
                if (is_array($option['odefault'])) {
                    $update_value = serialize($option['odefault']);
                } else {
                    $update_value = $option['odefault'];
                }
                $options_data = array(
                    'option_name' => $option['oid'],
                    'option_value' => $update_value,
                );
                nccsf_insert($options_table, $options_data);
            }
        }
        
    } elseif ($option_count != $db_count) {

        $temp_options_table = $options_table . '_temp';

        $wpdb->query("CREATE TABLE $temp_options_table SELECT * FROM $options_table");
        $wpdb->query("TRUNCATE TABLE $options_table");

        foreach ($nccsf_options as $options) {
            foreach ($options as $option) {
                
                
                if (is_array($option['odefault'])) {
                    $update_value = serialize($option['odefault']);
                } else {
                    $update_value = $option['odefault'];
                }
                $options_data = array(
                    'option_name' => $option['oid'],
                    'option_value' => $update_value,
                );
                nccsf_insert($options_table, $options_data);
            }
        }
        
        $temp_values = $wpdb->get_results("SELECT option_name, option_value FROM $temp_options_table");
        
        foreach($temp_values as $saved_value){
            $wpdb->query("UPDATE $options_table SET option_value = '{$saved_value->option_value}' WHERE option_name = '{$saved_value->option_name}'");
        }
        
        $wpdb->query("DROP TABLE $temp_options_table");
    }
}

################################################################################
# TRIM TEXT
################################################################################

function nccsf_trim_text($text, $cut) {
    if ($cut < strlen($text)) {
        return substr($text, '0', $cut) . '... ';
    } else {
        return substr($text, '0', $cut);
    }
}

################################################################################
# EMAIL VALIDATION
################################################################################

function nccsf_is_valid_email($email) {
    $result = preg_match('/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/', $email);
    if ($result == true) {
        return true;
    } else {
        return false;
    }
}

################################################################################
# Insert Function
################################################################################

function nccsf_insert($table, $data) {
    foreach ($data as $field => $value) {
        $fields[] = '`' . $field . '`';
        $values[] = "'" . mysql_real_escape_string($value) . "'";
    }
    $field_list = join(',', $fields);
    $value_list = join(', ', $values);
    $query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
    mysql_query($query);
    return mysql_insert_id();
}

################################################################################
# UPDATE FUNCTION
################################################################################

function nccsf_update($table, $data, $id_field, $id_value) {
    foreach ($data as $field => $value) {
        $fields[] = sprintf("`%s` = '%s'", $field, mysql_real_escape_string($value));
    }
    $field_list = join(',', $fields);
    $query = sprintf("UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval($id_value));
    mysql_query($query);
}

################################################################################
# Get String based on permalink
################################################################################

function nccsf_string($url) {
    if (strpos($url, '?') > 0) {
        $string = $url . '&';
    } else {
        $string = $url . '?';
    }
    return $string;
}

################################################################################
# GET FULL URL
################################################################################

function nccsf_current_url() {
    $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

################################################################################
# SEND EMAIL
################################################################################

function nccsf_mail($emaildata, $attachments = null) {

    $to = $emaildata['to'];
    $from_name = $emaildata['from_name'];
    $from = $emaildata['from'];
    $subject = $emaildata['subject'];
    $content = $emaildata['message'];

    $headers = "From: {$from_name} <{$from}>" . "\r\n\\";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "Return-Path: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $msg = $content;
    $message = "<html><body>";
    $message .= $msg;
    $message .= "</body></html>";


    $attachments = $emaildata['attachments'];


    if (wp_mail($to, $subject, $message, $headers, $attachments)) {
        return true;
    } else {
        return false;
    }
}

################################################################################
# Get Real IP Address
################################################################################

function nccsf_get_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

#############################################################################
# LOAD MODULES
#############################################################################

function nccsf_load_modules($module_array) {
    if (!empty($module_array)) {
        foreach ($module_array as $file) {
            if (nccsf_product_info('item_type') == 'Plugin') {
                require_once(str_replace('/framework', '', WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__))) . '/modules/' . $file . '.php');
            } elseif (nccsf_product_info('item_type') == 'Theme') {
                require_once(get_template_directory() . '/extend' . '/modules/' . $file . '.php');
            }
        }
    }
}

#############################################################################
# DISPLAY ADMIN OPTIONS
#############################################################################

function nccsf_display_options($options, $submit_button = null) {
    global $wpdb, $nccsf_msg;
    ?>

    <div id="nc-wrap" class="clearfix">
        <form action="" method="post">

            <?php echo '<p>Check out the <a target="_blank" href="' . nccsf_product_info('pro_version_url') . '" title="">Premium Version </a> of this product.</p>'; ?>
            
    <?php
    if (!empty($_POST)) {

        $options_table = nccsf_product_info('options_table_name');

        foreach ($_POST as $key => $value) {

            if (is_array($value)) {
                $update_value = serialize($value);
            } else {
                $update_value = $value;
            }

            $wpdb->query("UPDATE {$options_table} SET option_value = '{$update_value}' WHERE option_name = '{$key}'");
        }

        $fw_message = '<span class="fw-message-success fade">' . __('Settings Updated Successfully!', 'nccsf') . '</span>';

        echo $fw_message;
    }

    // MESSAGES WITHOUT POST
    echo $nccsf_msg;
    ?>


<table width="100%" border="0" cellspacing="0" cellpadding="0">

<?php 
foreach ($options as $option): 
?>


<?php if ($option['otype'] == 'heading'): ?>
<tr id="<?php echo $option['oid']; ?>" class="fw-heading"><td colspan="2"><?php echo $option['odefault']; ?></td></tr>
<?php endif; ?>



<?php if ($option['otype'] == 'sub-heading'): ?>
<tr id="<?php echo $option['oid']; ?>" class="fw-heading"><td colspan="2"><?php echo $option['odefault'] ?></td></tr>
<?php endif; ?>


<?php if ($option['otype'] == 'info'): ?>
<tr class="fw-info-white">
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<span class="fw-info-panel"><?php echo $option['odefault'] ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'info-yellow'): ?>
<tr class="fw-info-row">
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<span class="fw-info-panel"><?php echo $option['odefault'] ?></span>
</td>
</tr>
<?php endif; ?>






<?php if ($option['otype'] == 'text' || $option['otype'] == 'textbox'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<input type="text" name="<?php echo $option['oid']; ?>" value="<?php echo nccsf_top($option['oid']); ?>" /> 
<span class="fw-suffix"><?php echo $option['osuffix'] ?></span>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'textarea'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<textarea rows="5" cols="40" name="<?php echo $option['oid']; ?>"><?php echo nccsf_top($option['oid']); ?></textarea> 
<span class="fw-suffix"><?php echo $option['osuffix'] ?></span>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'wysiwyg'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<textarea class="wysiwyg" rows="5" cols="40" name="<?php echo $option['oid']; ?>"><?php echo nccsf_top($option['oid']); ?></textarea>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'dropdown'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select name="<?php echo $option['oid'] ?>">
<option value=""><?php _e('Please Select', 'nccsf'); ?></option>
<?php
foreach ($option['ovalue'] as $key => $opt):
if ($key == nccsf_top($option['oid'])) {
echo '<option selected value="' . $key . '">' . $opt . '</option>' . "\n";
} else {
echo '<option value="' . $key . '">' . $opt . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'multi-dropdown' || $option['otype'] == 'multi-select' || $option['otype'] == 'multiselect' ||  $option['otype'] == 'multidropdown' ): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select multiple size="4" class="fw-multiselect" name="<?php echo $option['oid'] ?>[]">
<option value=""><?php _e('Please Select', 'nccsf'); ?></option>
<?php
foreach ($option['ovalue'] as $key => $opt):
if (in_array($key, nccsf_top($option['oid']))) {
echo '<option selected value="' . $key . '">' . $opt . '</option>' . "\n";
} else {
echo '<option value="' . $key . '">' . $opt . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'users'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select multiple size="4" class="fw-multiselect" name="<?php echo $option['oid'] ?>[]">
    <option value="none"><?php _e('None', 'nccsf'); ?></option>
<?php
foreach (get_users() as $user):
if (in_array($user->ID, nccsf_top($option['oid']))) {
echo '<option selected value="' . $user->ID . '">' . $user->ID . ' - '. $user->user_login . ' | '. $user->user_email . '</option>' . "\n";
} else {
echo '<option value="' . $user->ID . '">' . $user->ID . ' - '. $user->user_login . ' | '. $user->user_email . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'category'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select name="<?php echo $option['oid'] ?>">
<option value=""><?php _e('Please Select', 'nccsf'); ?></option>
<?php
$args = array('type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category',);
$categories = get_categories($args);
foreach ($categories as $category):
if ($category->term_id == nccsf_top($option['oid'])) {
echo '<option selected value="' . $category->term_id . '">' . $category->cat_name . '</option>' . "\n";
} else {
echo '<option value="' . $category->term_id . '">' . $category->cat_name . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'categories'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select multiple size="4" class="fw-multiselect" name="<?php echo $option['oid'] ?>[]">
<option value=""><?php _e('None', 'nccsf'); ?></option>
<?php
$args = array('type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category',);
$categories = get_categories($args);
foreach ($categories as $category):
if (in_array($category->term_id, nccsf_top($option['oid']))) {
echo '<option selected value="' . $category->term_id . '">' . $category->cat_name . '</option>' . "\n";
} else {
echo '<option value="' . $category->term_id . '">' . $category->cat_name . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'page'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select name="<?php echo $option['oid'] ?>">
<option value=""><?php _e('Please Select', 'nccsf'); ?></option>
<?php
$args = array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'parent' => -1);
$pages = get_pages($args);
foreach ($pages as $pagg):
if ($pagg->ID == nccsf_top($option['oid'])) {
echo '<option selected value="' . $pagg->ID . '">' . $pagg->post_title . '</option>' . "\n";
} else {
echo '<option value="' . $pagg->ID . '">' . $pagg->post_title . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'pages'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select multiple size="4" class="fw-multiselect" name="<?php echo $option['oid'] ?>[]">
<option value="0"><?php _e('None', 'nccsf'); ?></option>
<?php
$args = array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'parent' => -1);
$pages = get_pages($args);
foreach ($pages as $pagg):
if (in_array($pagg->ID, nccsf_top($option['oid']))) {
echo '<option selected value="' . $pagg->ID . '">' . $pagg->post_title . '</option>' . "\n";
} else {
echo '<option value="' . $pagg->ID . '">' . $pagg->post_title . '</option>' . "\n";
}
endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'tag'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select name="<?php echo $option['oid'] ?>">
<option value=""><?php _e('Please Select', 'nccsf'); ?></option>
<?php
$tags = get_tags(array('orderby' => 'name'));
foreach ($tags as $tag):
if ($tag->slug == nccsf_top($option['oid'])) {
echo '<option selected value="' . $tag->slug . '">' . $tag->name . '</option>' . "\n";
} else {
echo '<option value="' . $tag->slug . '">' . $tag->name . '</option>' . "\n";
}

endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'tags'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>
<select multiple size="4" class="fw-multiselect" name="<?php echo $option['oid'] ?>[]">
<option value="0"><?php _e('None', 'nccsf'); ?></option>
<?php
$tags = get_tags(array('orderby' => 'name'));
foreach ($tags as $tag):
if (in_array($tag->slug, nccsf_top($option['oid']))) {
echo '<option selected value="' . $tag->slug . '">' . $tag->name . '</option>' . "\n";
} else {
echo '<option value="' . $tag->slug . '">' . $tag->name . '</option>' . "\n";
}

endforeach;
?>
</select>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'checkbox'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td class="fw-checkboxes">
<span class="fw-info-panel">
<?php
foreach ($option['ovalue'] as $key => $opt):
if (@in_array($key, nccsf_top($option['oid']))) {
echo '<label><input checked="checked" name="' . $option['oid'] . '[]" type="checkbox" value="' . $key . '" /> ' . $opt . ' </label>';
} else {
echo '<label><input name="' . $option['oid'] . '[]" type="checkbox" value="' . $key . '" /> ' . $opt . ' </label>';
}
endforeach;
echo '<input checked name="' . $option['oid'] . '[]" type="checkbox" value="null" class="hidden" />';
?>
</span>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'radio'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td class="fw-checkboxes">
<span class="fw-info-panel">
<?php
foreach ($option['ovalue'] as $key => $opt):
if ($key == nccsf_top($option['oid'])) {
echo '<label><input checked name="' . $option['oid'] . '" type="radio" value="' . $key . '" /> ' . $opt . ' </label>';
} else {
echo '<label><input name="' . $option['oid'] . '" type="radio" value="' . $key . '" /> ' . $opt . ' </label>';
}
endforeach;
?>
</span>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'date'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td class="fw-relative">
<input type="text" name="<?php echo $option['oid']; ?>" class="date-picker" value="<?php echo nccsf_top($option['oid']) ?>" />
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>


<?php if ($option['otype'] == 'upload' || $option['otype'] == 'file'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td class="fw-relative">
<input id="<?php echo 'img-' . rand(4, 9999); ?>" class="upload_image" type="text" name="<?php echo $option['oid']; ?>" value="<?php echo nccsf_top($option['oid']) ?>" />
<input class="upload_image_button button-secondary" type="button" value="<?php _e('Upload Image', 'nccsf'); ?>" />
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>
</td>
</tr>
<?php endif; ?>



<?php if ($option['otype'] == 'color'): ?>
<tr>
<td class="label">
<span><?php echo $option['oname']; ?></span>
</td>
<td>


<input id="<?php echo $option['oid']; ?>_color" name="<?php echo $option['oid']; ?>" class="fw-colorpicker-input" type="text" value="<?php echo nccsf_top($option['oid']) ?>" />
<span id="<?php echo $option['oid']; ?>_preview_color" class="fw_preview_color" style="background-color:#<?php echo nccsf_top($option['oid']); ?>">&nbsp;</span>
<span class="fw-desc"><?php echo $option['oinfo']; ?></span>

<script type="text/javascript">
jQuery('#<?php echo $option['oid']; ?>_color').ColorPicker({
color: '#0000ff',
onShow: function (colpkr) {
jQuery(colpkr).fadeIn(500);
return false;
},
onHide: function (colpkr) {
jQuery(colpkr).fadeOut(500);
return false;
},
onChange: function (hsb, hex, rgb) {
jQuery('#<?php echo $option['oid']; ?>_color').val(hex);
jQuery('#<?php echo $option['oid']; ?>_preview_color').css({"background-color":"#"+hex});
}
});
</script>




</td>
</tr>

<?php endif; ?>

<?php endforeach; ?>

    <?php
    if ($submit_button == null):
        ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input name="save_settings" type="submit" class="button-primary" value="<?php _e('Save Settings', 'nccsf'); ?>" />
                        </td>
                    </tr>
    <?php endif; ?>

            </table>

        </form>
    </div><!-- /nc-wrap -->

    <?php
}

################################################################################
# Uninstall
################################################################################

function nccsf_display_uninstall() {
    global $wpdb;
    ?>

    <div id="nc-wrap" class="clearfix">
        <form action="" method="post">


    <?php
    $uninstall_opt = '_site_' . sha1(nccsf_product_info('item_name'));

    if (isset($_POST['uninstall_db'])) {
        $options_table = nccsf_product_info('options_table_name');
        $wpdb->query("DROP TABLE $options_table");
        update_option($uninstall_opt, 1);

        $fw_message = '<span class="fw-message-success">' . __('Uninstall complete, You can de-activate this product now.', 'nccsf') . '</span>';
        echo $fw_message;
    }

    if (isset($_POST['reinstall_db'])) {
        delete_option($uninstall_opt);
        $location = site_url('wp-admin/admin.php?page=' . nccsf_product_info('settings_page_slug'));
        wp_redirect($location);
    }
    ?>

            <?php
            if (get_option($uninstall_opt) == 1):
                ?>

                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="fw-heading"><td colspan="2">
                <?php
                _e("Re-Install", 'nccsf');
                echo ' &raquo; ' . nccsf_product_info('item_name');
                ?>
                        </td></tr>
                    <tr class="fw-info-white">
                        <td>
                            <?php
                            printf(__('Click Re-Install to activate this %s or <a href="%s" title="">deactivate</a>.', 'nccsf'), nccsf_product_info('item_type'), nccsf_product_info('deactivate_url'));
                            ?>
                        </td>
                    </tr>
                    <tr class="fw-info-white red">
                        <td>
                            <input type="submit" name="reinstall_db" value="<?php _e('Re-Install', 'nccsf') ?>" class="button-secondary" /> &nbsp; 
                            <a href="<?php echo site_url('wp-admin'); ?>" title=""><?php _e('Cancel', 'nccsf') ?></a>
                        </td>
                    </tr>
                </table>

        <?php
    else:
        ?>

                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="fw-heading"><td colspan="2">
                <?php
                _e("Uninstall", 'nccsf');
                echo ' &raquo; ' . nccsf_product_info('item_name');
                ?>
                        </th></tr>
                    <tr class="fw-info-white red">
                        <td>
                            <?php _e('Are you sure? This will remove all saved settings.', 'nccsf') ?>
                        </td>
                    </tr>
                    <tr class="fw-info-white red">
                        <td>
                            <input type="submit" name="uninstall_db" value="<?php _e('Uninstall', 'nccsf') ?>" class="button-secondary fw-delete" /> &nbsp; 
                            <a href="#" title=""><?php _e('Cancel', 'nccsf') ?></a>
                        </td>
                    </tr>
                </table>

    <?php
    endif;
    ?>

        </form>

    </div>

            <?php
        }