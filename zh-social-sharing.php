<?php
/**
 * Plugin Name: ZH Social Sharing
 * Description: Automatically display selected social network(s) sharing buttons in posts and/or on pages.
 * Version: 1.0.0
 * Author: OnionEye
 * Author URI: http://onioneyethemes.com
 * Licence: GPL2
 * Text Domain: zhsocialsharing
 * Domain Path: /languages/
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit the script if accessed directly.
}

/**
 * Define constants
 */
 
define( 'ZH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZH_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'ZH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require( 'classes/class-zh-initializer.php' );
require( 'classes/class-zh-settings.php' );
require( 'classes/class-zh-button-renderer.php' );





