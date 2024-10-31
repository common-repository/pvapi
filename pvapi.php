<?php
/*
Plugin Name: PinkVisual
Plugin URI: http://saguarodigital.com/...
Description: Allows you to easily integrate PinkVisual content into your site using the <a href="http://api.pinkvisual.com/">PinkVisual API</a>.
Author: Saguaro Digital
Version: trunk
Author URI: http://saguarodigital.com/
License: TODO
*/

require_once(dirname(__FILE__)."/PVPlugin.class.php");

// Admin Menu
add_action("admin_menu",array("PVPlugin","init_admin_menu"));

// Media Plugin
add_filter('media_buttons_context', array("PVPlugin","add_media_button"));
add_filter('media_upload_tabs', array("PVPlugin","tab_filter"));
add_action('media_upload_pvapi', array("PVPlugin","render_tab"));  

// AJAX Callbacks
add_action("wp_ajax_pvapi_list_sources",array("PVPlugin","list_sources"));

// Style handling
add_action("wp_print_styles",array("PVPlugin","style"));

// Shortcode handling
add_shortcode("pvapi",array("PVPlugin","shortcode"));
