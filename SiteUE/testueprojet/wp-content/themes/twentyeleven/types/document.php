<?php
add_action( 'init', 'codex_custom_document_init' );
function codex_custom_document_init() {
  $labels = array(
    'name' => _x('Documents', 'post type general name'),
    'singular_name' => _x('Document', 'post type singular name'),
    'add_new' => _x('Ajouter', 'document'),
    'add_new_item' => __('Ajouter une nouvelle document'),
    'edit_item' => __('Editer une document'),
    'new_item' => __('Nouvelle document'),
    'all_items' => __('Toutes les documents'),
    'view_item' => __('Voir la document'),
    'search_items' => __('Chercher les documents'),
    'not_found' =>  __('Aucune document trouvée'),
    'not_found_in_trash' => __('Aucune document dans la poubelle'), 
    'parent_item_colon' => '',
    'menu_name' => 'Documents'

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
    'menu_position' => 5,
    'supports' => array( 'title', 'editor', 'author' )
  ); 
  register_post_type('document',$args);
}

//add filter to ensure the text document, or document, is displayed when user updates a document 
add_filter( 'post_updated_messages', 'codex_document_updated_messages' );
function codex_document_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['document'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('document updated. <a href="%s">View document</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('document updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('document restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('document published. <a href="%s">View document</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('document saved.'),
    8 => sprintf( __('document submitted. <a target="_blank" href="%s">Preview document</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('document scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview document</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('document draft updated. <a target="_blank" href="%s">Preview document</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}
?>