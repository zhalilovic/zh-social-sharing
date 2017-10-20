<?php
/**
 * Plugin Name: ZH Social Sharing
 * Description: Automatically display selected social networking sharing buttons in posts, pages and custom post types.
 * Version: 1.0.0
 * Author: OnionEye
 * Author URI: http://onioneyethemes.com
 * Licence: GPL2
 *
 * Text Domain: zhsocialsharing
 * Domain Path: /languages/
 * @package ZHSocialSharing
 */

// Exit the script if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Gets the plugin's filesystem directory path with a trailing slash.
 *
 * @since 1.0.0
 * @var string
 */
define( 'ZH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/**
 * Retrieves the absolute URL of the plugin's directory with a trailing slash.
 *
 * @since 1.0.0
 * @var string
 */
define( 'ZH_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
/**
 * Gets the path to a plugin file or directory, relative to the plugins directory.
 *
 * @since 1.0.0
 * @var string
 */
define( 'ZH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


/**
 * Initializes the plugin.
 */
require_once( 'inc/class-zh-initializer.php' );
/**
 * Sets up the individual admin settings.
 */
require_once( 'inc/class-zh-settings.php' );
/**
 * Displays the social sharing buttons on the front-end.
 */
require_once( 'inc/class-zh-button-renderer.php' );




