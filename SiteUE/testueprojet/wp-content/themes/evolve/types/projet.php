<?php

global $myposttype;
global $myCustomTypeOptions;
$myposttype='projet'; //id de mon custom post

add_action('init', 'moncustomposttype_init');
function moncustomposttype_init()
{
	global $myposttype;
	
  $labels = array(
    'name' => _x('Projets', 'post type general name'),
    'singular_name' => _x('Projet', 'post type singular name'),
    'add_new' => _x('Ajouter', 'publication'),
    'add_new_item' => __('Ajouter un nouveau projet'),
    'edit_item' => __('Editer un projet'),
    'new_item' => __('Nouveau projet'),
    'all_items' => __('Tous les projets'),
    'view_item' => __('Voir le projet'),
    'search_items' => __('Chercher les projets'),
    'not_found' =>  __('Aucun projet trouvé'),
    'not_found_in_trash' => __('Aucun projet dans la poubelle'), 
    'parent_item_colon' => '',
    'menu_name' => 'Projets'

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
    'menu_position' => 3,
    'supports' => array( 'title' )
  ); 
  register_post_type($myposttype,$args);


	/********** pour les checkbox Ã  choix multiples : le plus simple est de dÃ©clarer une taxonomie hiÃ©rarchique (= catÃ©gorie)  ****************/
	register_taxonomy( 'couleurs', $myposttype, array( 'hierarchical' => true, 'label' => 'couleurs', 'query_var' => true, 'rewrite' => true ) ); 
}	


$myCustomTypeOptions = array (
    array(
		'name' => 'Nombre de participants minimum',
        'desc' => '',
        'id' => $myposttype.'_choix',
        'type' => 'select',
        'options' => array(1,2,3,4,5,6,7,8,9),
        'std' => 2), 
    array(
		'name' => 'Nombre de participants maximum',
        'desc' => '',
        'id' => $myposttype.'_choix',
        'type' => 'select',
        'options' => array(1,2,3,4,5,6,7,8,9),
        'std' => 4), 
	array(
		'name' => 'Modules',
        'desc' => 'Sélectionnez les modules concernés par ce projet',
        'id' => $myposttype.'_case',
        'type' => 'checkbox',
        'std' => '1'),    
	array(
		'name' => 'Langages',
        'desc' => 'Sélectionnez les langages informatiques utilisables dans ce projet',
        'id' => $myposttype.'_case',
        'type' => 'checkbox',
        'std' => '1'), 
     array(
		'name' => 'Descriptif',
        'desc' => '',
        'id' => $myposttype.'_textarea',
        'type' => 'textarea',
        'std' => 'Je donne des détails sur le projet que je soumet...'),
	array(
		'name' => 'Site web',
        'desc' => 'Adresse du site web dédié au projet',
        'id' => $myposttype.'_champtexte',
        'type' => 'text',
        'std' => '')  
);

/************** Ã  la fin du formulaire, ajout des champs dÃ©finis dans $myCustomTypeOptions ***********/
add_action('edit_form_advanced', 'moncustomposttype_form'); 
add_action('save_post', 'moncustomposttype_save'); 
function moncustomposttype_form(){

	global $myposttype;	
	global $myCustomTypeOptions;

	if((isset($_GET['post_type'])) and ($_GET['post_type']==$myposttype)) /* formulaire d'ajout */
	{
		echo '<div class="meta-box-sortables ui-sortable">';
		/* formulaire vide (ou avec les valeurs par dÃ©faut) */
		foreach ($myCustomTypeOptions as $o)
		{
			echo '<div class="postbox">';
			echo '<h3 class="hndle"><span>'.$o['name'].'</span></h3><div class="inside"><label class="screen-reader-text" for="'.$o['id'].'">'.$o['name'].'</label>';
		
			//fonction pour afficher le bon html en fonction du type, dÃ©finie ci-aprÃ¨s
			echo get_champ($o,$o['std']);			
		
			if($o['desc']!='')
				echo '<p>'.$o['desc'].'</p>';
		
			echo '</div></div>';
		}
		echo '</div>';

	}
	else{
		if(!isset($_GET['post_type']))
		{
			if(isset($_GET['post']))
			{
				if(get_post_type($_GET['post'])==$myposttype) /* formulaire de modification */
				{
					$id=$_GET['post'];
				
					/* formulaire prÃ©rempli avec les valeurs prÃ©-existantes */
					echo '<div class="meta-box-sortables ui-sortable">';
					foreach ($myCustomTypeOptions as $o)
					{
						echo '<div class="postbox">';
						echo '<h3 class="hndle"><span>'.$o['name'].'</span></h3><div class="inside"><label class="screen-reader-text" for="'.$o['id'].'">'.$o['name'].'</label>';
					
						echo get_champ($o,get_post_meta($id, $o['id'], true));			
					
						if($o['desc']!='')
							echo '<p>'.$o['desc'].'</p>';
					
						echo '</div></div>';
					}
					echo '</div>';
				}
			}	
		}	
	}
}

function get_champ($o,$val)
{
	switch ($o['type'])
	{
		case 'textarea':
			echo '	<textarea rows="2" cols="40" name="'.$o['id'].'" id="'.$o['id'].'">'.$val.'</textarea>';
			break;
		case 'text':
			echo '	<input type="text" style="width:100%;" name="'.$o['id'].'" id="'.$o['id'].'" value="'.$val.'"/>';
			break;
		case 'checkbox':
			echo '<input type="checkbox" name="'.$o['id'].'" id="'.$o['id'].'" value="1" ';
			if($val==1)
				echo 'checked="checked';
			echo '/><label for="'.$o['id'].'">'.$o['name'].'</label>';
			break;
		case 'select':
			echo '	<select name="'.$o['id'].'" style="width:100%;" id="'.$o['id'].'"><option value="">-</option>';
			foreach($o['options'] as $opt)
			{
				echo '<option value="'.$opt.'"';
				if($opt==$val)
					echo 'selected="selected"';
				echo '>'.$opt.'</option>';
			}
			echo'</select>';
			break;
	}

}
/***************** Lors de la sauvegarde, ajout/modification d'un custom field par champs de $myCustomTypeOptions ***********/
function moncustomposttype_save(){

	global $myposttype;	
	global $myCustomTypeOptions;
	
	if(isset($_POST['post_ID']))
	{
		$id=$_REQUEST['post_ID'];
		if(get_post_type($id)==$myposttype) /* si on Ã©dite bien un post du type $myposttype */
		{
			foreach ($myCustomTypeOptions as $o)
			{
				if(isset($_POST[$o['id']])) {
					update_post_meta($id, $o['id'], $_POST[$o['id']]);
				}
				elseif($o['type']=='checkbox') {
					update_post_meta($id, $o['id'], 0);
				}
			}
		}
	}
}
?>