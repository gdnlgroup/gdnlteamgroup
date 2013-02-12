<?php
/*
Plugin Name: Utilisateurs
Plugin URI: http://codex.wordpress.org/Adding_Administration_Menus
Description: Permet au responsable de l'UE de g�rer les utilisateurs, qu'ils soient �tudiants, encadrants, ...
Author: Arnaud HIDOUX
Author URI: www.facebook.com/arnaud.hidoux
*/

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

// action function for above hook
function mt_add_pages() {
    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Utilisateurs','gestion-utilisateurs'), __('Utilisateurs','gestion-utilisateurs'), 'edit_users', 'mt-top-level-handle', 'mt_toplevel_page',plugins_url('utilisateurs/images/user.png') );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Ajouter','gestion-utilisateurs'), __('Ajouter','gestion-utilisateurs'), 'edit_users', 'sub-page', 'mt_sublevel_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Importer par CSV','gestion-utilisateurs'), __('Importer par CSV','gestion-utilisateurs'), 'edit_users', 'sub-page2', 'mt_sublevel_page2');
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page() {
    echo "<h2>" . __( 'Test Toplevel', 'gestion-utilisateurs' ) . "</h2>";
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
function mt_sublevel_page() {
    echo "<h2>" . __( 'Test Sublevel', 'gestion-utilisateurs' ) . "</h2>";
	
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt_name = 'mt_favorite_color';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_favorite_color';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'gestion-utilisateurs' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Menu Test Plugin Settings', 'gestion-utilisateurs' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Favorite Color:", 'gestion-utilisateurs' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom Test Toplevel menu
function mt_sublevel_page2() {
    echo "<h2>" . __( 'Test Sublevel2', 'gestion-utilisateurs' ) . "</h2>";
}

?>