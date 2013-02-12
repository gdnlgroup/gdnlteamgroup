<?php
/*
Plugin Name: plug_Bocoum
Description: L'ensemble des fonctions globales du site.
Version: 0.1
License: GPL
Author:Bocoum Adama
Author URI: http://bocoum.fr/
*/
?>
<?php
remove_action("wp_head", "wp_generator");
add_action('admin_init', 'init_wysiwyg');

function init_wysiwyg()
{
wp_enqueue_script('editor');
add_thickbox();
wp_enqueue_script('media-upload');
add_action('admin_print_footer_scripts', 'wp_tiny_mce', 25);
wp_enqueue_script('quicktags');
}



add_action('right_now_content_table_end', 'En_attente');

function En_attente() {
		$types = 'post';

        if (!post_type_exists(''.$types.'')) {
             return;
        }

        $num_posts = wp_count_posts( ''.$types.'' );

        $nbr_ = 'Post';
	$nbr_s = 'Posts';

        $num = number_format_i18n( $num_posts->publish );
        $text = _n('' . $nbr_ . '', '' . $nbr_s . '', intval($num_posts->publish) );
        if ( current_user_can( 'edit_posts' ) ) {
            $num = "<a href='edit.php?post_type=$types'>$num</a>";
            $text = "<a href='edit.php?post_type=$types'>$text</a>";
        }
        echo '<td class="first b">' . $num . '</td>';
        echo '<td class="t">' . $text . '</td>';

        echo '';

        if ($num_posts->pending > 0) {
            $num = number_format_i18n( $num_posts->pending );
            $text = _n( 'En attente', 'En attentes', intval($num_posts->pending) );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = "<a href='edit.php?post_status=pending&post_type=$types'>$num</a>";
                $text = "<a class=\"waiting\" href='wp-admin/edit.php?post_status=pending&post_type=$types'>$text</a>";
            }
            echo '<td class="first b">' . $num . '</td>';
            echo '<td class="t">' . $text . '</td>';

            echo '';
        }
}


function contacter (){
?>
<div style="text-align:right";>
<?php

global $current_user;
      get_currentuserinfo();

      $current_user->user_login;

//echo'    <a  target="_blank" href="mailto:'; the_author_email(); echo '"> &eacute;crire &agrave; l\'encadrant </a>'
echo'<a  target="_blank" href="messagerie?page=rwpm_send&recipient='.get_the_author_meta('user_login'). '"> &eacute;crire &agrave; l\'encadrant </a>'; ?>

</div>

<?php
}

// retourne les favoris du grouppe de l'utilisateur courant
function avoir_favoris(){
	
	$mongroupe=get_user_meta(get_current_user_id(),'mongroupe',true);
	$query = "SELECT post_id FROM `wp_postmeta` WHERE  meta_key='favoris' and meta_value like '$mongroupe'";
	$result = mysql_query($query);
	$definie=false;
	 
	while ($row = mysql_fetch_assoc($result)){
	       $definie[]=$row[post_id];
	}
	return $definie;
}

//return les posts favoris d'un groupe
function groupe_favoris($nomgroupe){
	$query = "SELECT post_id FROM `wp_postmeta` WHERE meta_key like 'favoris' and meta_value like '$nomgroupe'";
	$result = mysql_query($query);
	$definie=false;
	 
	while ($row = mysql_fetch_assoc($result)){
	       $definie[]=$row[post_id];
	}
	return $definie;
}

//retournes les infos d'un groupe en parametre  
function lesmesbres($mongroupe){
	 
	$blogusers=false;
	//get_user_meta(get_current_user_id(),'mongroupe',true);
	$query = "SELECT user_id FROM `wp_usermeta` WHERE  meta_value like '$mongroupe'";
	$result = mysql_query($query);
	$definie=false;
	
	while ($row = mysql_fetch_assoc($result)){
	       $definie[]=$row[user_id];
	}
	 $blogusers = get_users(array('include'=> $definie),
				'meta_key =mongroupe',
				'meta_value='.$mongroupe
				);
	// $lesmails=$blogusers[user_email];
	
	return $blogusers;
}


function button_postuler($postid){?>
	
	<?php
	/*  $mongroupe=get_user_meta(get_current_user_id(),'mongroupe',true);
          $query = "SELECT post_id FROM `wp_postmeta` WHERE  meta_value like '$mongroupe'";
          $result = mysql_query($query);
	 
	  $i=0;
	   while ($row = mysql_fetch_assoc($result)){
		$definie[]=$row[post_id];
          }
	print_r ($definie);
	$definie=get_user_meta(get_current_user_id(), 'favoris', false);*/
	$definie=avoir_favoris();
	$mongroupe=get_user_meta(get_current_user_id(),'mongroupe',true);
	?>
        
	 
	<?php $retour='<div>';
 	 ?>
        <?php if ( $definie!=false && in_array($postid,$definie))  :
		//var_dump($definie);
                $retour.='<small style="color: green; float:;">D&eacute;j&agrave; Choisi</small>';
        elseif(!empty($mongroupe)&& $mongroupe) :
	$retour.='<form action="'. get_bloginfo("url") .'/postuler"  method="post">
                <input type="hidden" value="'. get_the_ID() .'" name="postid"/>
                <input type="hidden" value="'. $_SERVER['REQUEST_URI'].'" name="precedent"/>
                <input type="submit" value="postuler" style=" height: 25px"/>
            </form>';
         endif;
	$retour.='</div>'; 
	
	return $retour ;?>
<?php }

add_filter('the_content', 'postuler');
function postuler($strContent) {
  //Only show at a single post / page the extended (if configurated) view
	 $p=get_the_ID();
  if('projet'==get_post_type()) {
      return $strContent.contacter().button_postuler($p);
      
  }
  else if (is_home()){
	return $strContent.contacter();
	 
	//return evltruncate($strContent, 500, ' <a href="'.get_permalink().'">...</a>').contacter();
  }else {
	return $strContent;
  } 
}



if ( ! function_exists( 'twentyeleven_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own twentyeleven_posted_on to override in a child theme
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
		get_the_author()
	);
     //  echo'    <a href="mailto:'; the_author_email(); echo '"> Contacter </a>';
}
endif;

function my_script_dumenu() {
	wp_enqueue_script("jquery");
	wp_enqueue_script('mon_script', '/wp-content/plugins/mon_plug/javascript/messcripts.js', array('jquery'), '1.0', 1);
	wp_enqueue_script('wp_jsmenu', '/wp-content/plugins/mon_plug/javascript/functionsmenu.js');
	wp_enqueue_style('wp_jsm', '/wp-content/plugins/mon_plug/javascript/stylemenu.css');
	 
}    
 
add_action('wp_enqueue_scripts', 'my_script_dumenu');


function monplug_register_new_user( $user_login, $user_email, $role) {
    $errors = new WP_Error();

    $sanitized_user_login = sanitize_user( $user_login );
    $user_email = apply_filters( 'user_registration_email', $user_email );

    // Check the username
    if ( $sanitized_user_login == '' ) {
        $errors->add( 'empty_username', __( utf8_encode('<strong>ERROR</strong>: Veuillez choisir un login.') ) );
    } elseif ( !validate_username( $user_login ) ) {
        $errors->add( 'invalid_username', __( utf8_encode('<strong>ERROR</strong>: Ce nom d\utilisateur est invalide car il utilise des caractères illegal Veuillez choisir un de nouveau .') ) );
        $sanitized_user_login = '';
    } elseif ( username_exists( $sanitized_user_login ) ) {
        $errors->add( 'username_exists', __( utf8_encode('<strong>ERROR</strong>: Ce nom d\' utilisateur est déjà utilisé.' )) );
    }

    // Check the e-mail address
    if ( $user_email == '' ) {
        $errors->add( 'empty_email', __( utf8_encode('<strong>ERROR</strong>: Please type your e-mail address.') ) );
    } elseif ( !is_email( $user_email ) ) {
        $errors->add( 'invalid_email', __( utf8_encode('<strong>ERROR</strong>: Cet addresse email  isn&#8217;t correct.') ) );
        $user_email = '';
    } elseif ( email_exists( $user_email ) ) {
        $errors->add( 'email_exists', __( utf8_encode('<strong>ERROR</strong>: Ce mail est déjà utilisé.' )) );
    }

    do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

    $errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

    if ( $errors->get_error_code() )
        return $errors;

    $user_pass = wp_generate_password( 12, false );
    //$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );

    $userdata = array(
        'user_login' => $sanitized_user_login,
        'user_email' => $user_email,
        'user_pass' => $user_pass,
        'role' => $role
    );

    $user_id = wp_insert_user( $userdata );

    if ( !$user_id ) {
        $errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
        return $errors;
    }

    update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

    wp_new_user_notification( $user_id, $user_pass );

    return $user_id;
}




add_action( 'send_headers', 'site_router' );
function site_router(){
	session_start();
	$root=str_replace('index.php','', $_SERVER['SCRIPT_NAME']);
	$url= str_replace($root,'',$_SERVER['REQUEST_URI']);
	$url=explode('/',$url);
	
        if(count($url) == 1 && $url[0]=='login')
	{
	   // require 'tpl-login.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-login.php');

	    die();
	}else if(count($url) == 1 && $url[0]=='profil')
	{
	    //require 'tpl-profile.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-profile.php');

	    die();
	}else if(count($url) == 1 && $url[0]=='logout')
	{
	    wp_logout();
            header('location:'.$root);
	    die();
	}else if(count($url) == 1 && $url[0]=='register')
	{
	    //require 'tpl-register.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-register.php');

	    die();
	}
        
        else if(count($url) == 1 && $url[0]=='editer'){
		//require 'tpl-editer.php';
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-editer.php');
	    die();
        }
	else if(count($url) == 1 && $url[0]=='proposer'){
		//require 'tpl-editer.php';
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-edition_pending.php');
	    die();
        }
         else if(count($url) == 1 && $url[0]=='inscrition_etudiant'){
            //require'tpl-traitementinscript.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-traitementinscript.php');
            die();
         }
          else if(count($url) == 1 && $url[0]=='postuler'){
           // require'gestionpostulat.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-gestionpostulat.php');

            die();
         }
          else if(count($url) == 1 && $url[0]=='lister'){
          //  require'Listechoisi.php';
	    include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-listechoisi.php');
            die();
         } else if(count($url) == 1 && $url[0]=='attente'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-attente.php');
		die();
	 } else if(count($url) == 1 && $url[0]=='listerattente'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/edit-front.php');
		die();
	 }else if(count($url) == 1 && $url[0]=='register_encadrant'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-register_enca.php');
		die();
	 }else if(count($url) == 1 && $url[0]=='creer_groupe'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-groupe.php');
		die();
	 }else if(count($url) == 1 && $url[0]=='mes_sujets'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tpl-mes_sujets.php');
		die();
	 }
	 else if(count($url) == 1 && $url[0]=='affecter'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/affectation.php');
		die();
	 }
	  else if(count($url) == 1 && $url[0]=='testessai'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/tlp-test_register.php');
		die();
	 } else if(count($url) == 1 && $url[0]=='fslider'){
		include(ABSPATH.'wp-content/plugins/mon_plug/templates/frontslidere.php');
		die();
	 }
}

add_filter('show_admin_bar', '__return_false');

//require_once(ABSPATH.'wp-content/themes/twentyeleven/types/sujet.php');

add_shortcode( 'listerfav', 'listerfav' );
function listerfav() {
	 global $wpdb;
$precedent=$_SERVER['REQUEST_URI'];
  $user= get_current_user_id();
if($user == 0) :
    $_SESSION['context']=$precedent;
    Redirect_('login');
    die();
   else :
       $choisis=avoir_favoris();
       

       ?>
       <div id="primary">
       <?php
        
       
       
       
     if($choisis){
   //   $my_query = new WP_Query(array( 'post__in' => $choisis));
       //var_dump ($my_query);
        $ids = join("','", $choisis);
       $sql = "SELECT ID, post_title, post_name FROM $wpdb->posts
	    WHERE ID IN ('$ids') AND post_type = 'projet'";

	$my_queries = $wpdb->get_results( $sql );
	?>
	
	    <div id="content" role="main">
	    <table style="width: 100%">
	    <th><h5>Titre</h5></th>
         <?php //while ($my_query->have_posts()) :
	    //$my_query->the_post();
	     //var_dump($ids);
	      foreach ($my_queries as $my_query) { ?>
	    
	    <tr>
	    <td>
	    <a href="<?php get_permalink( $my_query->ID )?>" rel="bookmark" title="Permanent Link to <?php get_the_title($my_query->ID); ?>">
	    <?php
	     
	    if ( get_the_title($my_query->ID) ){
	        $title = $my_query->post_title;	  
	        echo $title;
	     }
	     else{
		   _e('Sans titre');
		}
	     ?>
	     </a>
	     </td>
	     <td align="center">
		<?php contacter ();?>
	     </td>
	    </tr>	 
	    <?php } // end of the loop. ?>
	    </table>
	     </div><!-- #content -->
	     <?php }   
	 else { ?> <div>Vous n'avez pas de favoris actuellement</div> <?php } ?>
	 <?php endif;?>
      
       <a href="javascript:history.back()">Retour</a>
</div><!-- #primary -->
<?php	
}

function Redirect_($dest) {
  if (!headers_sent())
    header('Location: ' . $dest);
  else
    echo '<script language="JavaScript">window.location=\'' . $dest . '\'</script>';
}

?>
