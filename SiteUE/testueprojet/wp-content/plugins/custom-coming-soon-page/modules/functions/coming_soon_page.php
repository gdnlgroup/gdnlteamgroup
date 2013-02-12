<?php

if (nccsf_top('plugin_status') == 'enable') {
    add_action('get_header', 'nccsf_coming_soon_page');
}

function nccsf_coming_soon_page() {
    
    date_default_timezone_set ( get_option('timezone_string') );
    
    $current_user = wp_get_current_user();
    if (is_user_logged_in()) {
        if (current_user_can('manage_options')) {
            echo '';
        } elseif (in_array($current_user->ID, nccsf_top('site_access'))) {
            echo '';
        } else {
            require_once(nccsf_product_info('product_dir') . '/themes/theme.php');
            die();
        }
    } else {
        require_once(nccsf_product_info('product_dir') . '/themes/theme.php');
        die();
    }
}