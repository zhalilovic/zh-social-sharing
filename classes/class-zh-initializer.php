<?php
/**
 * 
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit the script if accessed directly.
}
 
if ( ! class_exists( 'ZH_Initializer' ) ) :

	class ZH_Initializer {
		
		private $settings_page_slug = 'zh-social-sharing';
		private $settings_page_hook_suffix = '';
		
		/** Refers to a single instance of this class. */
		private static $instance = null;
		
		/**
	     * Creates or returns an instance of this class.
	     *
	     * @return  CPA_Theme_Options A single instance of this class.
	     */
	    public static function get_instance() {
	  
	        if ( null == self::$instance ) {
	            self::$instance = new self;
	        }
	  
	        return self::$instance;
	  
	    }
		
		private function __construct() {
			add_action( 'admin_menu', array( $this, 'add_menu_item' ), 10 );	
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_front_end_assets' ) );
			add_filter( 'plugin_action_links_' . ZH_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
		}
		
		public function add_menu_item() {
			$this->settings_page_hook_suffix = add_options_page( 
				esc_html__('ZH Social Sharing Plugin', 'zhsocialsharing'), 
				esc_html__('ZH Social Sharing', 'zhsocialsharing'), 
				'manage_options', 
				$this->settings_page_slug, 
				array( $this, 'display_settings_page' ) 
			);
		}
		
		public function add_settings_link( $links ) {
		    $settings_link = '<a href="' . esc_url( admin_url( '/options-general.php?page=' .  $this->settings_page_slug ) ) . '">' . esc_html__( 'Settings', 'zhsocialsharing' ) . '</a>';
		    array_push( $links, $settings_link );
		  	
		  	return $links;
		}
		
		public function load_admin_assets( $hook_suffix ) {
			if ( $this->settings_page_hook_suffix === $hook_suffix ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'jquery-ui-sortable' );
				wp_enqueue_style( 'zh-admin-option-styles', ZH_PLUGIN_URL . 'css/admin-option-styles.css' );
				
				wp_enqueue_script( 'zh-jquery-custom', ZH_PLUGIN_URL . 'js/jquery.admin.js', array( 'wp-color-picker', 'jquery-ui-sortable' ), false, true );
			}
		}
		
		public function load_front_end_assets() {
			if ( ! is_admin() ) {
				wp_enqueue_style( 'zh-front-end-styles', ZH_PLUGIN_URL . 'css/front-end-styles.css' );
				wp_enqueue_script( 'zh-front-end-script', ZH_PLUGIN_URL . 'js/jquery.front-end.js' );
			}	
		}
		
		public function load_textdomain() {
			
		}
		
		public function display_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}
			
			include ZH_PLUGIN_DIR . 'inc/html-settings-page.php';
		}	
		
	}
	
	ZH_Initializer::get_instance();
 
endif;