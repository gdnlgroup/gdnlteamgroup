<?php

require_once('admin_options.php');
add_action('admin_menu', 'nccsf_init');

$nccsf_uninstall_opt = '_site_' . sha1(nccsf_product_info('item_name'));
if (get_option($nccsf_uninstall_opt) == 1) {
    $nccsf_install_text = 'Re-Install';
} else {
    $nccsf_install_text = 'Uninstall';
}

$nccsf_admin_menus = array(
    array(
        'page_type' => 'top_level',
        'page_title' => 'Coming Soon Pages [Free]',
        'menu_title' => 'Coming Soon',
        'page_slug' => nccsf_product_info('settings_page_slug'),
        'permission' => '"manage_opitons"',
        'callback_function' => 'nccsf_item_info',
        'icon_url' => nccsf_product_info('assets_url').'/images/menu_icon.png', // Can be used with top_level only
    ),
    
    
    array(
        'page_type' => 'sub_level',
        'page_title' => 'Plugin Configuration',
        'menu_title' => 'Configuration',
        'page_slug' => 'nccsf_general_settings',
        'permission' => '"manage_opitons"',
        'callback_function' => 'nccsf_configuration',
        'icon_url' => '', // Can be used with top_level only
    ),
    
    array(
        'page_type' => 'sub_level',
        'page_title' => 'Branding',
        'menu_title' => 'Branding',
        'page_slug' => 'nccsf_branding',
        'permission' => '"manage_opitons"',
        'callback_function' => 'nccsf_branding',
        'icon_url' => '', // Can be used with top_level only
    ),
    array(
        'page_type' => 'sub_level',
        'page_title' => 'Module Settings',
        'menu_title' => 'Module Settings',
        'page_slug' => 'nccsf_modules',
        'permission' => '"manage_opitons"',
        'callback_function' => 'nccsf_modules',
        'icon_url' => '', // Can be used with top_level only
    ),    
    
    
    array(
        'page_type' => 'sub_level',
        'page_title' => $nccsf_install_text,
        'menu_title' => $nccsf_install_text,
        'page_slug' => 'nccsf_uninstall',
        'permission' => '"manage_opitons"',
        'callback_function' => 'nccsf_uninstall',
        'icon_url' => '', // Can be used with top_level only
    ),
);



function nccsf_configuration() {
    global $nccsf_options;
    nccsf_display_options($nccsf_options['configuration']);
}

function nccsf_branding() {
    global $nccsf_options;
    nccsf_display_options($nccsf_options['branding']);
}

function nccsf_modules() {
    global $nccsf_options;
    nccsf_display_options($nccsf_options['modules']);
}

function nccsf_item_info() {
    global $nccsf_options;
    nccsf_display_options($nccsf_options['info_page'], 'false');
}

function nccsf_uninstall() {
    nccsf_display_uninstall();
}

######################################################################
# PLUGIN INIT
######################################################################

function nccsf_init() {
    global $nccsf_admin_menus, $nccsf_uninstall_opt;
    foreach ($nccsf_admin_menus as $admin_page) {
        if ($admin_page['page_type'] == 'top_level') {
            $menu_page = add_menu_page($admin_page['page_title'], $admin_page['menu_title'], 'manage_options', $admin_page['page_slug'], $admin_page['callback_function'], $icon_url = $admin_page['icon_url']);
            add_action('admin_print_scripts', 'nccsf_admin_scripts');
            add_action('admin_print_styles', 'nccsf_admin_styles');
        } else {
            $menu_page = add_submenu_page(nccsf_product_info('settings_page_slug'), $admin_page['page_title'], $admin_page['menu_title'], 'manage_options', $admin_page['page_slug'], $admin_page['callback_function']);
            add_action('admin_print_scripts', 'nccsf_admin_scripts');
            add_action('admin_print_styles', 'nccsf_admin_styles');
        }
    }

    // SETUP DATABASE
    if (get_option($nccsf_uninstall_opt) == 1) {

        foreach ($nccsf_admin_menus as $key => $value) {
            if ($_GET['page'] == $value['page_slug']) {
                if ($_GET['page'] != 'nccsf_uninstall') {
                    $location = site_url('wp-admin/admin.php?page=nccsf_uninstall');
                    wp_redirect($location);
                }
            }
        }
    } else {
        add_action('admin_head', 'nccsf_install_db');
    }



    // REMOVE FIRST LINK IN ADMIN PAGES MENU
    add_action('admin_head', 'nccsf_menu_fix');

    function nccsf_menu_fix() {
        echo '<style type="text/css">ul#adminmenu li.toplevel_page_' . nccsf_product_info('settings_page_slug') . ' .wp-first-item{display:none !important;} </style>';
    }

}

######################################################################
# UPGRADE CHECK
######################################################################

if (isset($_GET['nc_upgrade']) && $_GET['nc_upgrade'] == 'available') {
    $nccsf_msg = '<span class="fw-message-info">' . __(sprintf('New version is available to download! <a target="_blank" href="%s" class="button-secondary" title="">Download Now</a>', 'http://www.netchillies.com/my-purchases'), 'nccsf') . '</span>';
} elseif (isset($_GET['nc_upgrade']) && $_GET['nc_upgrade'] == 'na') {
    $nccsf_msg = '<span class="fw-message-success fade">' . __(sprintf('You are using the latest version of %s', nccsf_product_info('item_name'))) . '</span>';
}