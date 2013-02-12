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
echo'    <a  target="_blank" href="mailto:'; the_author_email(); echo '"> ecrire a l\'encadrant </a>';?>
</div>

<?php
}

// retourne les favoris du grouppe de l'utilisateur courant
function avoir_favoris(){
	
	$mongroupe=get_user_meta(get_current_user_id(),'mongroupe',true);
	$query = "SELECT post_id FROM `wp_postmeta` WHERE  meta_value like '$mongroupe'";
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


function button_postuler(){?>
	<div>
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
        <?php $postid=get_the_ID();?>
	<div style="float: right;">
	<?php
	//echo'  <a  target="_blank" href="mailto:'; the_author_email(); echo '"> ecrire a l\'encadrant </a>';
	?>
	</div>
        <?php if ( !empty($definie) && $definie && in_array($postid,$definie))  : ?>
                 <small style="color: green; float:;">D&eacute;j&agrave; Choisi</small>
        <?php elseif(!empty($mongroupe)&& $mongroupe) : ?>
	
            <form action="<?php echo bloginfo('url')?>/postuler" method="post">
                <input type="hidden" value="<?php the_ID(); ?>" name="postid"/>
                <input type="hidden" value="<?php echo $_SERVER['REQUEST_URI']; ?>" name="precedent"/>
                <input type="submit" value="postuler" style=" height: 19px"/>
            </form>  
        <?php endif;?>

	</div>
	
<?php }

add_filter('the_content', 'postuler');

function postuler($strContent) {
  //Only show at a single post / page the extended (if configurated) view

  if(is_single()) {
	$rtour="";
     return button_postuler().$strContent.contacter();
      
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






add_action( 'send_headers', 'site_router' );
function site_router(){
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
	 }
}

add_filter('show_admin_bar', '__return_false');

//require_once(ABSPATH.'wp-content/themes/twentyeleven/types/sujet.php');


?>