<?php
/*
  Plugin Name: Coming Soon Pages [Free]
  Plugin URI: http://www.netchillies.com/coming-soon-pages-free
  Description: This plugin shows a 'Custom Coming Soon' page to all users who are not logged in however, the Site Administrators see the fully functional website with the applied theme and active plugins as well as a fully functional Dashboard.
  Author: NetChillies.com
  Version: 2.1.02
  Author URI: http://www.netchillies.com/
 */
/*  Copyright 2011 NetChillies.com  (email : support@netchillies.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
require_once('framework/framework.php');
require_once('framework/init.php');
#################################################################################
# LOAD MODULES
#################################################################################
$nccsf_modules = array(
        'functions/coming_soon_page'
);

nccsf_load_modules($nccsf_modules);


if (nccsf_product_info('item_type') == 'Plugin') {
#################################################################################
# PLUGIN SETTINGS LINK
#################################################################################

    function nccsf_action_links($links, $file) {
        $pro_version = nccsf_product_info('pro_version_url');
        static $this_plugin;
        if (!$this_plugin)
            $this_plugin = plugin_basename(__FILE__);
        if ($file == $this_plugin) {
            $settings_link = '<a href="' . site_url('wp-admin/admin.php?page=') . nccsf_product_info('settings_page_slug') . '">' . 'Settings' . '</a>';
            if ($pro_version != '') {
                $settings_link .= ' | <a target="_blank" href="' . $pro_version . '">' . 'Pro Version' . '</a>';
            }
            $links = array_merge(array($settings_link), $links);
        }
        return $links;
    }

    add_filter('plugin_action_links', 'nccsf_action_links', 10, 2);
    /** LOCALIZATION ************************************************************* */
    load_plugin_textdomain('nccsf', '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/languages/');
} else {
    /** LOCALIZATION ************************************************************* */
    load_theme_textdomain('nccsf', get_template_directory() . '/languages');
}