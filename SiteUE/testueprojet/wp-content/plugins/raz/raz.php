<?php
/*
Plugin Name: Remise a zero
Plugin URI: 
Description: Remise � z�ro
Author: Arnaud HIDOUX
Author URI: www.facebook.com/arnaud.hidoux
*/

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_page_raz');

// action function for above hook
function mt_add_page_raz() {
    // Add a new top-level menu (ill-advised):
	//htmlentities()
    add_menu_page(__('Remise � z�ro','gestion-utilisateurs'), __('Remise a zero','gestion-utilisateurs'), 'edit_users', 'mt-top-level-handle_raz', 'mt_toplevel_page_raz',plugins_url('raz/images/cross-button.png') );
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page_raz() {
    echo "<h2>" . __( 'Test Toplevel', 'gestion-utilisateurs' ) . "</h2>";
	
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
?>