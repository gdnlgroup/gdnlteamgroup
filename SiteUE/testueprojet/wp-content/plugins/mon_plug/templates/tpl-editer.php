<?php
/*
  Template Name:postul
 */
	$error= false;
if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {
 
	// Validation du contenu
	if (empty ($_POST['title'])) {
		 $error= 'Merci de mettre un titre';
	} else if (empty ($_POST['description'])) {  
		$error= 'Merci d\'entrer du contenu';
	} else  {
		$title =  $_POST['title'];
		$description=  $_POST['description'];
		$description=$description.'<br>';
		$description.='<strong>Taille de l\'equipe</strong> :'.$_POST['taille'].'<br>';
		$description.= '<strong>Langages</strong>  :'.$_POST['langage'];

		$tags = $_POST['post_tags'];

		// Ajout du contenu
		$post = array(
			'post_title'	=> $title,
			'post_content'	=> $description,
			//'post_category'	=> $_POST['cat'],
			'tags_input'	=> $tags,
			'post_status'	=> 'publish',			// Choix: publish, preview, future, pending etc.
			'post_type'		=> $_POST['post_type']
		);
		wp_insert_post($post);	// http://codex.wordpress.org/Function_Reference/wp_insert_post
	
		wp_redirect( home_url() ); // Redirection vers la page d'accueil ou autre.
	} 
	

} // fin de IF

//do_action('wp_insert_post', 'wp_insert_post');
?>
<!-- Formulaire -->
<?php get_header(); ?>
	<?php  if($error) :?>
	<div class="error" style=" color: red;"  >
		<?php echo $error;?> 
	</div>
	<?php  endif ;?>
	<form id="new_post" name="new_post" method="post" action="">
		<p><label for="title"><strong>Titre</strong></label><br />
		<input type="text" id="title" value="<?php if(!empty ($_POST['title'])) echo $_POST['title']; ?>" tabindex="1" size="90" name="title" />
		</p>
		<!--<p><label for="description">Description</label><br />
		<textarea id="description" tabindex="3" name="description" cols="50" rows="6"></textarea>
		</p>-->
		<div id="poststuff" style=" width: 70%; " >
		<label for="title"><strong>Description</strong></label>
		<?php if(!empty ($_POST['description']))
				$content=$_POST['description'];
		       the_editor($content, $id = 'description', $prev_id = 'title', $media_buttons = true, $tab_index = 2)
		?>
		</div>
		
		
		<p style="padding-top: 10px;">
		<label for="taille"><strong>Taille de l'equipe:  </strong></label>
		<select name="taille" id="taille" >
			<option value="indeterminée" >indetermin&eacute;e</option>
			<option value="1" <?php if( !empty($_POST['taille']) && $_POST['taille']=="1" )   echo "selected=\"selected\"";?> >1</option>
			<option value="2" <?php if( !empty($_POST['taille']) && $_POST['taille']=="2" )	echo "selected=\"selected\"";?>>2</option>
			<option value="3" <?php if( !empty($_POST['taille']) && $_POST['taille']=="3" )	echo "selected=\"selected\"";?> >3</option> 
			<option value="4" <?php if( !empty($_POST['taille']) && $_POST['taille']=="4" )	echo "selected=\"selected\"";?>>4</option>
			<option value="5" <?php if( !empty($_POST['taille']) &&$_POST['taille']=="5" )   echo "selected=\"selected\"";?> >5</option>
		</select>
		</p>
		
		<p style="width: 40%"> 
		<label for="langage" ><strong> Langages</strong></label>
		<textarea  id="langage"  tabindex="3" cols="20" rows="3" name="langage" ><?php if(!empty ($_POST['langage'])) echo stripcslashes(htmlspecialchars($_POST['langage'],ENT_QUOTES)); ?></textarea>
		</p>
		
		<p><?php wp_dropdown_categories( 'show_option_none=Categorie&tab_index=4&taxonomy=category' ); ?></p>
		<p><label for="post_tags"><strong>Mots-clefs</strong></label>
			<input type="text" value="" tabindex="5" size="16" name="post_tags" id="post_tags" /></p>
		<p align="center">
                <input type="submit" value="    Publier     " tabindex="5" id="submit" name="submit" /></p>

		<input type="hidden" name="post_type" id="post_type" value="post" /> <!--// "value" est le type de contenu (post, films, decouvertes etc. -->
		<input type="hidden" name="action" value="post" />
		<?php wp_nonce_field( 'new-post' ); ?>
	</form>
 

 
<!--// Formulaire -->

<?php get_footer(); ?>