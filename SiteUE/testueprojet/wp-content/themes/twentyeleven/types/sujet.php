<?php
add_action( 'init', 'register_sujet' );
function register_sujet() {
  $labels = array(
    'name' =>'Sujet de projet',
    'singular_name' => 'Sujet' ,
    'add_new' => 'Ajouter un sujet',
    'add_new_item' => 'jouter un sujet',
    'edit_item' => 'Modifier sujet',
    'new_item' => 'Nouveau sujet',
    'all_items' =>'Tous les sujets',
    'view_item' =>'Voir le sujet',
    'search_items' => 'Rechercher un sujet',
    'not_found' => 'Aucun sujet publié',
    'not_found_in_trash' => 'Aucun sujet dans le corbeille', 
    'parent_item_colon' => '',
    'menu_name' => 'Sujet'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',//post
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail','custom-fields' )
  ); 
  register_post_type('sujet',$args);
}
?>