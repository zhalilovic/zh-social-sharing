<?php
/**
 * ZH Social Sharing setup
 *
 * @author   Zlatan Halilovic
 * @package  zh-social-sharing
 * @since 	 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
 
if ( ! class_exists( 'ZH_Initializer' ) ) :

	/**
	 * Main ZH Social Sharing Class.
	 *
	 * @class ZH_Initializer
	 * @since 1.0.0
	 */
	final class ZH_Initializer {
		
		/**
		 * The slug name for the settings page.
		 *
		 * @var string
		 */
		private $settings_page_slug = 'zh-social-sharing';

		/**
		 * The settings page's hook_suffix (What add_options_page() returns).
		 *
		 * @var string
		 */
		private $settings_page_hook_suffix = '';
		
		/**
		 * The single instance of the class.
		 *
		 * @var ZH_Initializer
		 */
		private static $instance = null;
		
		/**
		 * Main ZH_Initializer Instance.
		 *
		 * Ensures only one instance of ZH_Initializer is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @return ZH_Initializer - Main instance.
		 */
	    public static function get_instance() {
	  
	        if ( null == self::$instance ) {
	            self::$instance = new self;
	        }
	  
	        return self::$instance;
	  
	    }

	    /**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zhsocialsharing' ), '1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zhsocialsharing' ), '1.0' );
		}
		
		/**
		 * ZH_Initializer Constructor.
		 */
		private function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hooks into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'admin_menu', array( $this, 'add_menu_item' ), 10 );	
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_front_end_assets' ) );
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_filter( 'plugin_action_links_' . ZH_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
		}
		
		/**
		 * Adds an options page to the Settings menu tab.
		 */
		public function add_menu_item() {
			$this->settings_page_hook_suffix = add_options_page( 
				esc_html__('ZH Social Sharing Plugin', 'zhsocialsharing'), 
				esc_html__('ZH Social Sharing', 'zhsocialsharing'), 
				'manage_options', 
				$this->settings_page_slug, 
				array( $this, 'display_settings_page' ) 
			);
		}
		
		/**
		 * Filters the plugin action links.
		 *
		 * Adds a plugin settings link to the Plugins page.
		 *
		 * @param array $links Plugin links.
		 * @return array 
		 */
		public function add_settings_link( $links ) {
		    $settings_link = '<a href="' . esc_url( admin_url( '/options-general.php?page=' .  $this->settings_page_slug ) ) . '">' . esc_html__( 'Settings', 'zhsocialsharing' ) . '</a>';
		    array_push( $links, $settings_link );
		  	
		  	return $links;
		}
		
		/**
		 * Adds scripts and styles for the plugin settings page.
		 *
		 * @param string $hook_suffix The settings page's hook_suffix.
		 */
		public function load_admin_assets( $hook_suffix ) {
			if ( $this->settings_page_hook_suffix === $hook_suffix ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'jquery-ui-sortable' );
				wp_enqueue_style( 'zh-admin-option-styles', ZH_PLUGIN_URL . 'css/admin-option-styles.css' );
				
				wp_enqueue_script( 'zh-jquery-custom', ZH_PLUGIN_URL . 'js/jquery-admin.js', array( 'wp-color-picker', 'jquery-ui-sortable' ), false, true );
			}
		}
		
		/**
		 * Adds scripts and styles on the front-end.
		 */
		public function load_front_end_assets() {
			if ( ! is_admin() ) {
				wp_enqueue_style( 'zh-front-end-styles', ZH_PLUGIN_URL . 'css/front-end-styles.css' );
				wp_enqueue_script( 'zh-front-end-script', ZH_PLUGIN_URL . 'js/jquery-front-end.js' );
			}	
		}
		
		/**
		 * Loads the localization files.
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'zhsocialsharing', false, dirname( ZH_PLUGIN_BASENAME ) . '/lang/' );
		}
		
		/**
		 * Displays the settings page in the admin.
		 */
		public function display_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}
			
			include ZH_PLUGIN_DIR . 'inc/html-settings-page.php';
		}	
		
	}
	
	// Lock and load. Fire.
	ZH_Initializer::get_instance();
 
endif;