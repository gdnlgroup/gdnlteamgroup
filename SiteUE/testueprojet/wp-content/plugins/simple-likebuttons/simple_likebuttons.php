<?php

/*
Plugin Name: Simple Likebuttons (Facebook, Google+, Twitter)
Plugin URI: http://www.best-plugins.de
Description: Adds a facebook, Google+ and Twitter button to your posts and pages with one click.
Version: 1.2
Author: Maik Balleyer (Biloba IT)
Author URI: http://www.biloba-it.de
License: GPL2

Copyright 2011 Maik Balleyer (Biloba IT)  (email : balleyer@biloba-it.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

include('simple_likebuttons_options.php');

//Add the necessary js scripts
function simple_likebuttons_scripts() {
  wp_register_script('plusone', 'http://apis.google.com/js/plusone.js');
  wp_enqueue_script('plusone');
  wp_register_script('twitter', 'http://platform.twitter.com/widgets.js');
  wp_enqueue_script('twitter');
}

//WP handler
add_action('wp_head', 'add_simple_likebuttons_css');
add_filter('the_content', 'add_simple_likebuttons');
add_action('wp_enqueue_scripts', 'simple_likebuttons_scripts');

if(is_admin()) {
  add_action('admin_menu', 'simple_likebuttons_menu');
}

//Add icons method
function add_simple_likebuttons($strContent) {

  //Only show at a single post / page the extended (if configurated) view
  if(is_single()) {
    return $strContent . getLikeButtonsHtmlCode();
  } 
  else {
    return $strContent ;
	//. getLikeButtonsHtmlCode('small');
  }

}

//Method to add the necessary css file to the html header
function add_simple_likebuttons_css() {
  echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('simple-likebuttons') . '/simple_likebuttons.css" />' . "\n";
}

//The html code for the buttons
function getLikeButtonsHtmlCode($strStyle = null) {

  //Url for the posts / pages
  $strHref = get_permalink();

  //DE / EN supportes languages
  $aryLanguages = array(
    'de-DE' => array('fb' => 'de_DE', 'gp' => 'de', 'tw' => 'de'),
    'en-US' => array('fb' => 'en_US', 'gp' => 'en', 'tw' => 'en'),
  );

  //Get language of blog
  $strLanguage = get_bloginfo('language');

  //Set to default language if blog language is not supported
  if(!array_key_exists($strLanguage, $aryLanguages)) {
    $strLanguage = 'en-US';
  }

  //Load configuration for this plugin
  $aryOptions = get_option('simple-likebuttons-options');
  if($aryOptions === false) {
    $aryOptions['gp_status'] = "1";
    $aryOptions['tw_status'] = "1";
    $aryOptions['fb_status'] = "1";
  }

  //Settings for block-layout
  $aryExtendLayout = array(
    'facebook' => 'data-layout="box_count"',
    'facebook_css' => 'simple_likebuttons_facebook',
    'facebook_width' => '55',
    'googleplus' => 'size="tall"',
    'googleplus_css' => 'simple_likebuttons_googleplus',
    'twitter' => 'data-count="vertical"',
    'twitter_css' => 'simple_likebuttons_twitter'
  );

  //Settings for small-layout
  $arySmallLayout = array(
    'facebook' => 'data-layout="icon_link"',//button_count, icon_link
    'facebook_css' => 'simple_likebuttons_facebook',
    'facebook_width' => '190',
    'googleplus' => 'size="medium" count="true" annotation="inline" width="200"',
    'googleplus_css' => 'simple_likebuttons_googleplus',
    'twitter' => 'data-count="none"',
    'twitter_css' => 'simple_likebuttons_twitter simple_likebuttons_twitter_s'
  );

  if($strStyle !== null) {
    $aryOptions['style'] = $strStyle;
  }

  if(array_key_exists('style', $aryOptions) && $aryOptions['style'] == 'small') {
    $aryLayout = $arySmallLayout;
    $strHtmlCode = '<div class="simple_likebuttons_container_'.$strStyle.'">';
  } else {
    $aryLayout = $aryExtendLayout;
    $strHtmlCode = '<div class="simple_likebuttons_container">';
  }

  if(array_key_exists('gp_status', $aryOptions) && $aryOptions['gp_status'] == '1') {
    $strHtmlCode .= '
      <div class="'.$aryLayout['googleplus_css'].'">
        <g:plusone '.$aryLayout['googleplus'].' href="'.$strHref.'"></g:plusone>
      </div>
    ';
  }

  if(array_key_exists('tw_status', $aryOptions) && $aryOptions['tw_status'] == '1') {
    $strHtmlCode .= '
      <div class="'.$aryLayout['twitter_css'].'">
        <a href="https://twitter.com/share" class="twitter-share-button" '.$aryLayout['twitter'].' data-url="'.$strHref.'" data-lang="'.$aryLanguages[$strLanguage]['tw'].'">Tweet</a>
      </div>
    ';
  }

  if(array_key_exists('fb_status', $aryOptions) && $aryOptions['fb_status'] == '1') {
    $strHtmlCode .= '
      <div class="'.$aryLayout['facebook_css'].'">
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) {return;}
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/'.$aryLanguages[$strLanguage]['fb'].'/all.js#xfbml=1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, "script", "facebook-jssdk"));</script>
<a name="fb_share" class="fb-like" type="icon_link" share_url="'.$strHref.'">
        <div class="fb-like" data-href="'.$strHref.'" data-send="false" '.$aryLayout['facebook'].' data-show-faces="true" data-width="'.$aryLayout['facebook_width'].'"></div>
      </div>
    ';
  }

  $strHtmlCode .= '</div>';

  return $strHtmlCode;
}

?>