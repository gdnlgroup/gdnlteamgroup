<?php
/*
  Template Name:Edition
 */

if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {

	// Validation du contenu
	if (isset ($_POST['title'])) {
		$title =  $_POST['title'];
	} else {
		echo 'Merci de mettre un titre';
	}
	if (isset ($_POST['description'])) {
		$description = $_POST['description'];
	} else {
		echo 'Merci d\'entrer du contenu';
	}
	$tags = $_POST['post_tags'];

	// Ajout du contenu
	$post = array(
		'post_title'	=> $title,
		'post_content'	=> $description,
		'post_category'	=> $_POST['cat'],
		'tags_input'	=> $tags,
		'post_status'	=> 'publish',			// Choix: publish, preview, future, pending etc.
		'post_type'		=> $_POST['post_type']
	);
	wp_insert_post($post);	// http://codex.wordpress.org/Function_Reference/wp_insert_post

	wp_redirect( home_url() ); // Redirection vers la page d'accueil ou autre.

} // fin de IF

do_action('wp_insert_post', 'wp_insert_post');
?>
<!-- Formulaire -->
<?php get_header(); ?>
<div id="postbox">
	<form id="new_post" name="new_post" method="post" action="">
		<p><label for="title">Titre</label><br />
		<input type="text" id="title" value="" tabindex="1" size="20" name="title" />
		</p>
		<p><label for="description">Description</label><br />
		<textarea id="description" tabindex="3" name="description" cols="50" rows="6"></textarea>
		</p>
		<p><?php wp_dropdown_categories( 'show_option_none=Catégorie&tab_index=4&taxonomy=category' ); ?></p>
		<p><label for="post_tags">Mots-clefs</label>
			<input type="text" value="" tabindex="5" size="16" name="post_tags" id="post_tags" /></p>
		<p align="right">
                <input type="submit" value="Mettre &agrave; relire" tabindex="6" id="submit" name="submit" /></p>

		<input type="hidden" name="post_type" id="post_type" value="post" /> <!--// "value" est le type de contenu (post, films, decouvertes etc. -->
		<input type="hidden" name="action" value="post" />
		<?php wp_nonce_field( 'new-post' ); ?>
	</form>
</div>
<!--// Formulaire -->

<?php get_footer(); ?>