<?php
add_action( 'init', 'codex_custom_publication_init' );
function codex_custom_publication_init() {
  $labels = array(
    'name' => _x('Publications', 'post type general name'),
    'singular_name' => _x('Publication', 'post type singular name'),
    'add_new' => _x('Ajouter', 'publication'),
    'add_new_item' => __('Ajouter une nouvelle publication'),
    'edit_item' => __('Editer une publication'),
    'new_item' => __('Nouvelle publication'),
    'all_items' => __('Toutes les publications'),
    'view_item' => __('Voir la publication'),
    'search_items' => __('Chercher les publications'),
    'not_found' =>  __('Aucune publication trouvée'),
    'not_found_in_trash' => __('Aucune publication dans la poubelle'), 
    'parent_item_colon' => '',
    'menu_name' => 'Publications'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => 6,
    'supports' => array( 'title', 'editor', 'author' )
  ); 
  register_post_type('publication',$args);
}

//add filter to ensure the text publication, or publication, is displayed when user updates a publication 
add_filter( 'post_updated_messages', 'codex_publication_updated_messages' );
function codex_publication_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['publication'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('publication updated. <a href="%s">View publication</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('publication updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('publication restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('publication published. <a href="%s">View publication</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('publication saved.'),
    8 => sprintf( __('publication submitted. <a target="_blank" href="%s">Preview publication</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('publication scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview publication</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('publication draft updated. <a target="_blank" href="%s">Preview publication</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for publications
add_action( 'contextual_help', 'codex_add_help_text', 10, 3 );

function codex_add_help_text( $contextual_help, $screen_id, $screen ) { 
  //$contextual_help .= var_dump( $screen ); // use this to help determine $screen->id
  if ( 'publication' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a publication:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct genre such as Mystery, or Historic.') . '</li>' .
      '<li>' . __('Specify the correct writer of the publication.  Remember that the Author module refers to you, the author of this publication review.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the publication review to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-publication' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of publications blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}
?>