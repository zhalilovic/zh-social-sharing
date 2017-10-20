<?php
/**
 * Social Sharing plugin for WordPress
 *
 * @package zh-social-sharing
 * @author  Zlatan Halilovic <hello@onioneyethemes.com>
 *
 * Plugin Name: ZH Social Sharing
 * Description: Display selected social networking sharing buttons in posts, pages and custom post types.
 * Version: 1.0.0
 * Author: Zlatan Halilovic
 * Author URI: http://onioneyethemes.com
 * Licence: GPL2
 * Text Domain: zhsocialsharing
 * Domain Path: /lang/
 */

// Exit the script if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Define the plugin's filesystem directory path with a trailing slash.
define( 'ZH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Define the absolute URL of the plugin's directory with a trailing slash.
define( 'ZH_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

// Define the path to a plugin file or directory, relative to the plugins directory.
define( 'ZH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Initialize the plugin.
 */
require_once( 'inc/class-zh-initializer.php' );

/**
 * Set up individual admin settings.
 */
require_once( 'inc/class-zh-settings.php' );

/**
 * Display the social sharing buttons on the front-end.
 */
require_once( 'inc/class-zh-button-renderer.php' );




