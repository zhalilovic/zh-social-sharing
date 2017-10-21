<?php
 
// Exit the script if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
 
if ( ! class_exists( 'ZH_Settings' ) ) :

	/**
	 * Settings Class
	 *
	 * Setup all plugin settings in the admin.
	 *
	 * @class 		ZH_Settings
	 * @version		1.0.0
	 * @author 		Zlatan Halilovic
	 */
	class ZH_Settings {
		
		/**
		 * Default settings data
		 *
		 * @var array
		 */
		private $settings_data = array();

		/**
		 * Settings group name (first argument of the register_setting() function).
		 *
		 * @var string
		 */
		private static $settings_group_name = 'zh-social-sharing-options'; 

		/**
		 * Section of the settings page that contains the option fields.
		 *
		 * @var string
		 */
		private $settings_section = 'zh-main-settings';
		
		/**
		 * Default social networking buttons.
		 *
		 * @var array
		 */
		private static $default_social_networks = array( 'facebook', 'twitter', 'google_plus', 'pinterest', 'linkedin', 'whatsapp' );

		/**
		 * Default order of the social networking buttons.
		 *
		 * @var array
		 */
		private static $default_button_order = array( 'zh-facebook', 'zh-twitter', 'zh-google_plus', 'zh-pinterest', 'zh-linkedin', 'zh-whatsapp' );

		/**
		 * Allows the user to define a custom hex color.
		 *
		 * @var bool
		 */
		private static $default_has_custom_color = false;

		/**
		 * The hex color for the social buttons.
		 *
		 * @var string
		 */
		private static $default_hex_color = '#00c964'; // green

		/**
		 * Default size of the social buttons.
		 *
		 * @var string
		 */
		private static $default_button_size = 'medium';

		/**
		 * Default allowed post types where the buttons will show.
		 *
		 * @var array
		 */
		private static $default_post_types = array( 'post' );

		/**
		 * Sections of singular post/page where the buttons will display.
		 *
		 * @var array
		 */
		private static $default_button_positions  = array( 'after_post_title', 'after_post_content' );

		/**#@+
	     * Setting ID
	     *
	     * @var string
	     */
		const SETTING_ID_SOCIAL_NETWORKS   = 'zh_social_networks';
		const SETTING_ID_BUTTON_ORDER      = 'zh_button_order';
		const SETTING_ID_CUSTOM_COLOR      = 'zh_custom_color';
		const SETTING_ID_HEX_COLOR         = 'zh_hex_color';
		const SETTING_ID_BUTTON_SIZES      = 'zh_button_sizes';
		const SETTING_ID_POST_TYPES        = 'zh_post_types';
		const SETTING_ID_BUTTON_POSITIONS  = 'zh_button_positions';
		/**#@-*/
		
		/**
		 * The single instance of the class.
		 *
		 * @var ZH_Settings
		 */
		private static $instance = null;
		
		/**
		 * Main ZH_Settings Instance.
		 *
		 * Ensures only one instance of ZH_Settings is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @return ZH_Settings - Main instance.
		 */
	    public static function get_instance() {
	  
	        if ( null == self::$instance ) {
	            self::$instance = new self;
	        }
	  
	        return self::$instance;
	  
	    } 
		
		/**
		 * ZH_Settings Constructor.
		 */
		private function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hooks into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'admin_init', array( $this, 'setup_settings' ) );
		}
		
		/**
		 * Sets up all settings for the main options page.
		 *
		 * Uses WordPress' Settings API to add a settings section, register all 
		 * available settings, and save default setting values in the database.
		 *
		 * @see add_settings_section()
		 * @see register_setting()
		 * @see add_settings_field()
		 * @see update_option()
		 * @since 1.0.0
		 *
		 */
		public function setup_settings() {
 			$page = self::$settings_group_name;
 			$plugin_data = get_plugin_data( ZH_PLUGIN_DIR . '/zh-social-sharing.php' ); 
 			
 			// Create a section of a page.
 			add_settings_section( $this->settings_section, $plugin_data['Name'], '', $page ); 
 			
 			$this->set_settings_data(); // Set the default options array.
 			$default_options = $this->get_settings_data(); // Retrieve the default options array.
 			
 			/* 
			 * Register all custom settings, add settings fields to a settings page and section,
			 * and write the default options to the database, if they are not already saved. 
			 */
 			foreach ( $default_options as $option ) {
 				if ( isset( $option['id'] ) ) {
	 				register_setting( self::$settings_group_name, $option['id'], array( $this, $option['sanitize_callback'] ) );	 				
	 				add_settings_field( $option['id'], $option['label'], array( $this, 'output_setting_field' ), $page, $this->settings_section, $option );
	 				
	 				if ( false === get_option( $option['id'] ) ) { // Nothing yet saved
	 					update_option( $option['id'], $option['default'] );
	 				}
 				}
	 			
 			}
		}
		
		/**
		 * Outputs a single setting field on the plugin's settings page.
		 *
		 * @param array $option All meta info related to a single option. 
		 */
		public function output_setting_field( $option ) {
			if ( ! isset( $option['type'] ) ) {
				continue;
			}
			if ( ! isset( $option['label'] ) ) {
				$option['label'] = '';
			}
			if ( ! isset( $option['scrn_text'] ) ) {
				$option['scrn_text'] = '';
			}
			if ( ! isset( $option['desc'] ) ) {
				$option['desc'] = '';
			}
			if ( ! isset( $option['id'] ) ) {
				$option['id'] = '';
			}
			if ( ! isset( $option['default'] ) ) {
				$option['default'] = '';
			}
			if ( ! isset( $option['choices'] ) ) {
				$option['choices'] = '';
			}
			if ( ! isset( $option['default'] ) ) {
				$option['default'] = '';
			}
			
			switch ( $option['type'] ) :
			
				case 'checkbox_multiple':
				?>
					
					<fieldset>
						
						<?php if ( $option['scrn_text'] ) { ?>
							<legend class="screen-reader-text"><span><?php esc_html( $option['scrn_text'] ); ?></span></legend>
						<?php } ?>
						
						<?php 															
						foreach( $option['choices'] as $key => $checkbox_label ) :										
						?>
						
							<label for="<?php echo esc_attr( "zh-{$key}" ); ?>">
								<input id="<?php echo esc_attr( "zh-{$key}" ); ?>" 
									name="<?php echo esc_attr( $option['id'] ); ?>[]" 
									type="checkbox" 
									value="<?php echo esc_attr( $key ); ?>" 
									<?php checked( in_array( $key, (array) get_option( $option['id'] ) ), true ); ?> 
								/>
								<span><?php echo esc_html( $checkbox_label ); ?></span>
							</label>
							
							<br />
						
						<?php endforeach; ?>
					</fieldset>
					
				<?php
					break;
						
				case 'radio': 
				?>	
					
					<fieldset>
						
						<?php if ( $option['scrn_text'] ) { ?>
							<legend class="screen-reader-text"><span><?php esc_html( $option['scrn_text'] ); ?></span></legend>
						<?php } ?>
						
						<?php 															
						foreach( $option['choices'] as $key => $checkbox_label ) :										
						?>
						
							<label for="<?php echo esc_attr( "zh-{$key}" ); ?>">
								<input id="<?php echo esc_attr( "zh-{$key}" ); ?>" 
									name="<?php echo esc_attr( $option['id'] ); ?>[]" 
									type="radio" 
									value="<?php echo esc_attr( $key ); ?>" 
									<?php checked( ( $key == get_option( $option['id'] ) ), true ); ?>
								/>
								<span><?php echo esc_html( $checkbox_label ); ?></span>
							</label>
							
							<br />
						
						<?php endforeach; ?>
					</fieldset>
					
				<?php
					break;
					
				case 'checkbox':
				?>
					
					<label for="<?php echo esc_attr( $option['id'] ); ?>">
						<input id="<?php echo esc_attr( $option['id'] ); ?>" 
							name="<?php echo esc_attr( $option['id'] ); ?>" 
							type="checkbox" 
							value="1" 
							<?php checked( get_option( $option['id'] ), true ); ?>  
						/>
						<span><?php echo esc_html( $option['desc'] ); ?></span>
					</label>
				
				<?php
					break;
					
				case 'sort':
				 	echo ZH_Button_Renderer::output_buttons( 
				 		get_option( $option['id'] ), 
				 		get_option( self::SETTING_ID_HEX_COLOR ), 
				 		get_option( self::SETTING_ID_BUTTON_SIZES ), 
				 		get_option( self::SETTING_ID_CUSTOM_COLOR ), 
				 		NULL,
				 		$option['id'] 
				 	);				 	
				?>
				 	<p><em><?php echo esc_html( $option['desc'] ); ?></em></p>
				<?php
				 						
					break;
					
				case 'color':
				?>
					<input id="<?php echo esc_attr( $option['id'] ); ?>" 
						name="<?php echo esc_attr( $option['id'] ); ?>" 
						type="text" 
						value="<?php echo esc_attr( get_option( $option['id'] ) ); ?>" 
						class="zh-color-field" 
						data-default-color="<?php echo esc_attr( $option['default'] ); ?>" 
					/>
					
					<p><em><?php echo esc_html( $option['desc'] ); ?></em></p>
					
				<?php
					break;
							
				default: 
					break;
			
			endswitch;		
		}

		/*
		|--------------------------------------------------------------------------
		| Sanitization Callbacks.
		|--------------------------------------------------------------------------
		|
		| Methods to sanitize all registered settings.
		*/

		/**
		 * Sanitizes the social networks provided by the user.
		 *
		 * Makes sure that all options for social networking buttons provided are valid,
		 * or else outputs errors to the user on the main settings page, after form submit.
		 *
		 * @param array $selected_options The chosen social networking button links. 
		 * @return array 
		 */	
		public function sanitize_social_networks( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( 
					self::SETTING_ID_SOCIAL_NETWORKS, 
					'zh-no-social-networks', 
					__( 'Settings saved. Please be aware that the sharing buttons will not display on your site (unless a shortcode is used) because you have chosen to disable all of the social networks.', 'zhsocialsharing' ), 
					'updated' 
				);
			} 
			
			$default_options = $this->get_settings_data();
			
			/* 
			 * Check if any of the provided social networks are not present
			 * in the default available choices array. If true, output error to the user.
			 */
			if ( array_diff( $selected_options, array_keys( $default_options['social_networks']['choices'] ) ) ) { 
				add_settings_error( self::SETTING_ID_SOCIAL_NETWORKS, 'invalid-zh-social-networks', __( 'Tampering with the checkbox input values is not allowed!', 'zhsocialsharing' ) );
			}
						
			return $selected_options;
		}
		
		/**
		 * Sanitizes the post types provided by the user.
		 *
		 * Makes sure that all provided post types are valid, or else outputs errors 
		 * to the user on the main settings page, after form submit.
		 *
		 * @param array $selected_options The chosen post types. 
		 * @return array 
		 */	
		public function sanitize_post_types( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( 
					self::SETTING_ID_POST_TYPES, 
					'zh-no-post-types', 
					__( 'Settings saved. Please be aware that the sharing buttons will not display on your site (unless a shortcode is used) because you have chosen to disable all of the available post types.', 'zhsocialsharing' ), 
					'updated'
				);
			} 
			
			$default_options = $this->get_settings_data();
			
			/* 
			 * Check if any of the provided post types are not present
			 * in the default available choices array. If true, output error to the user.
			 */
			if ( array_diff( $selected_options, array_keys( $default_options['post_types']['choices'] ) ) ) {
				add_settings_error( self::SETTING_ID_POST_TYPES, 'invalid-zh-post-types', __( 'Tampering with the checkbox input values is not allowed!', 'zhsocialsharing' ) );
			}
			
			return $selected_options;
		}
		
		/**
		 * Sanitizes the button size provided by the user.
		 *
		 * Makes sure that the button size is valid, or else outputs errors 
		 * to the user on the main settings page, after form submit.
		 *
		 * @param array|string $selected_options The chosen button size. 
		 * @return string
		 */	
		public function sanitize_button_sizes( $selected_options ) {
			$default_options = $this->get_settings_data();
			
			if ( array_diff( (array) $selected_options, array_keys( $default_options['button_sizes']['choices'] ) ) ) {
				// The provided button size is not one of the offered values. Output error.
				add_settings_error( self::SETTING_ID_BUTTON_SIZES, 'invalid-zh-size', __( 'Tampering with the radio input values is not allowed!', 'zhsocialsharing' ) );
			}
			
			if( is_array( $selected_options ) ) {
				$selected_options = implode( ' ', $selected_options ); // Convert the option to string, if array.	
			}
						
			return $selected_options;
		}
		
		/**
		 * Sanitizes the button positions provided by the user.
		 *
		 * Makes sure that all provided positions, where the button can display on a single 
		 * post/page, are valid, or else outputs errors on the main settings page. 
		 *
		 * @param array $selected_options The chosen button positions. 
		 * @return array 
		 */	
		public function sanitize_button_positions( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( 
					self::SETTING_ID_BUTTON_POSITIONS, 
					'zh-no-positions', 
					__( 'Settings saved. Please be aware that the sharing buttons will not display on your site (unless a shortcode is used) because you have chosen to disable all of the available positions where the buttons can display.', 'zhsocialsharing' ),
					'updated' 
				);
			} 
			
			$default_options = $this->get_settings_data();
			
			if ( array_diff( $selected_options, array_keys( $default_options['button_positions']['choices'] ) ) ) {
				// One or all of the provided button positions are not present in the available values array. Output error.
				add_settings_error( self::SETTING_ID_BUTTON_POSITIONS, 'invalid-zh-positions', __( 'Tampering with the checkbox input values is not allowed!', 'zhsocialsharing' ) );
			}
						
			return $selected_options;
		}
		
		/**
		 * Sanitizes a single checkbox.
		 *
		 * @param bool|empty $value The value attribute of a checkbox sent via POST. 
		 * @return bool 
		 */	
		public function sanitize_checkbox( $value ) {
			return ( isset( $value ) ) && ( true == $value || 1 == $value ) ? true : false;			
		}
		
		/**
		 * Sanitizes the order of the social networking buttons.
		 *
		 * Makes sure the provided order of the buttons is valid, or else outputs errors 
		 * to the user on the main settings page, after form submit.
		 *
		 * @param array $settings All social networks, customly ordered for display. 
		 * @return array 
		 */	
		public function sanitize_button_order( $settings ) {
			$button_order_data_set = $this->get_single_option_data_set( self::SETTING_ID_BUTTON_ORDER ); // Get all meta info related to the button order.
			$default_button_order = $button_order_data_set['default']; // Social networks in their default order.  
			$current_button_order = $settings; // Social networks in the user-defined order.
			
			sort( $default_button_order );
			sort( $current_button_order );
			
			if ( $default_button_order !== $current_button_order ) { // Current button order has values that don't match one or all available choices.
				add_settings_error( self::SETTING_ID_BUTTON_ORDER, 'invalid-zh-button-order', __( 'Tampering with the data is not allowed!', 'zhsocialsharing' ) );
				$settings = get_option( self::SETTING_ID_BUTTON_ORDER ); // Set selected options to previously valid ones that are already in the database.	
			}
			
			return $settings;		
		}
		
		/**
		 * Sanitizes the hex color provided by the user.
		 *
		 * @param array $value The chosen hex color value. 
		 * @return array 
		 */	
		public function sanitize_hex_color( $value ) {
			if ( ! $value ) {
				add_settings_error( self::SETTING_ID_HEX_COLOR, 'invalid-zh-empty-color', __( 'The color field can not be empty.', 'zhsocialsharing' ) );
				$value = get_option( self::SETTING_ID_HEX_COLOR ); // Set selected option to previously valid one that was already in the database.
			} 
			
			if ( ! sanitize_hex_color( $value ) ) { // Use WordPress' built-in function for checking the validity of the provided hex color.
				add_settings_error( self::SETTING_ID_HEX_COLOR, 'invalid-zh-hex-color', __( 'Please enter a valid hex color in the color picker setting.', 'zhsocialsharing' ) );
				$value = get_option( self::SETTING_ID_HEX_COLOR );
			}
			
			return $value;			
		}

		/*
		|--------------------------------------------------------------------------
		| Getters.
		|--------------------------------------------------------------------------
		|
		| Methods to retrieve class properties and avoid direct access.
		*/

		/**
		 * Gets the default settings meta data for all options in a single array.
		 *
		 * @return array
		 */
		public function get_settings_data() {
			return $this->settings_data;
		}
		
		/**
		 * Gets the default social networking buttons. 
		 *
		 * @return array
		 */
		public static function get_default_social_networks() {
			return self::$default_social_networks;
		}
		
		/**
		 * Gets the default order of the social buttons. 
		 *
		 * @return array
		 */
		public static function get_default_button_order() {
			return self::$default_button_order;
		}
		
		/**
		 * Gets the default option for determining whether the user can use a custom hex color or not.
		 *
		 * @return bool|int
		 */
		public static function default_has_custom_color() {
			return self::$default_has_custom_color;
		}
		
		/**
		 * Gets the default hex color. 
		 *
		 * @return string
		 */
		public static function get_default_hex_color() {
			return self::$default_hex_color;
		}
		
		/**
		 * Gets the default button size. 
		 *
		 * @return string
		 */
		public static function get_default_button_size() {
			return self::$default_button_size;
		}
		
		/**
		 * Gets the default post types. 
		 *
		 * @return array
		 */
		public static function get_default_post_types() {
			return self::$default_post_types;
		}
		
		/**
		 * Gets the default button positions for the single post/page. 
		 *
		 * @return arrays
		 */
		public static function get_default_button_positions() {
			return self::$default_button_positions;
		}

		/*
		|--------------------------------------------------------------------------
		| Setters.
		|--------------------------------------------------------------------------
		|
		| Methods to set class properties and avoid direct access.
		*/

		/**
		 * Sets and stores the default settings meta data for all options in a single array.
		 */
		public function set_settings_data() {			
			$this->settings_data = array(
				
				'social_networks' => array(
					'label'    	  		=> __( 'Include buttons for:', 'zhsocialsharing' ),
					'scrn_text'   		=> __( 'Which social media buttons should be displayed?', 'zhsocialsharing' ),
					'id'          		=> self::SETTING_ID_SOCIAL_NETWORKS,
					'type'        	    => 'checkbox_multiple',
					'sanitize_callback' => 'sanitize_social_networks',
					'default'     		=> self::$default_social_networks,
					'choices'    	 	=> array(
						'facebook'    => __( 'Facebook', 'zhsocialsharing' ),
						'twitter'     => __( 'Twitter', 'zhsocialsharing' ),
						'google_plus' => __( 'Google+', 'zhsocialsharing' ),
						'pinterest'   => __( 'Pinterest', 'zhsocialsharing' ),
						'linkedin'    => __( 'LinkedIn', 'zhsocialsharing' ),
						'whatsapp'    => __( 'WhatsApp (for mobile browsers only)', 'zhsocialsharing' ),
					),
				),
				
				'button_order' => array(
					'label'    	  		=> __( 'Sharing buttons order:', 'zhsocialsharing' ),
					'desc'   			=> __( 'Drag the social icons to change the order in which they display on your website.', 'zhsocialsharing' ),
					'id'          		=> self::SETTING_ID_BUTTON_ORDER,
					'type'        	    => 'sort',
					'sanitize_callback' => 'sanitize_button_order',
					'default'     		=> self::$default_button_order,
				),
				
				'custom_color' => array(
					'label'    			=> __( 'Custom Color:', 'zhsocialsharing' ),
					'desc'    			=> __( 'Enable custom color', 'zhsocialsharing' ),
					'id'      		 	=> self::SETTING_ID_CUSTOM_COLOR,
					'type'     			=> 'checkbox',
					'sanitize_callback' => 'sanitize_checkbox',
					'default'  			=> self::$default_has_custom_color,
				),
				
				'hex_color' => array(
					'label'    			=> __( 'Choose a custom color for your buttons:', 'zhsocialsharing' ),
					'desc'     			=> __( 'Enable the Custom Color option above to be able to choose custom colors for your buttons.', 'zhsocialsharing' ),
					'id'       			=> self::SETTING_ID_HEX_COLOR ,
					'type'     			=> 'color',
					'sanitize_callback' => 'sanitize_hex_color',
					'default'  			=> self::$default_hex_color,
				),
				
				'button_sizes' => array(
					'label'    			=> __( 'Choose the size in which to display the social buttons:', 'zhsocialsharing' ),
					'id'      			=> self::SETTING_ID_BUTTON_SIZES,
					'type'     			=> 'radio',
					'sanitize_callback' => 'sanitize_button_sizes',
					'default'  			=> self::$default_button_size,
					'choices'  			=> array(
						'small'  => __( 'Small', 'zhsocialsharing' ),
						'medium' => __( 'Medium', 'zhsocialsharing' ),
						'large'  => __( 'Large', 'zhsocialsharing' ),
					),
				),
				
				'post_types' => array(
					'label'   			=> __( 'Show buttons on:', 'zhsocialsharing' ),
					'id'      			=> self::SETTING_ID_POST_TYPES,
					'type'    			=> 'checkbox_multiple',
					'sanitize_callback' => 'sanitize_post_types',
					'default' 			=> self::$default_post_types,
					'choices' 			=> $this->get_post_type_choices(),	
				),
				
				'button_positions' => array(
					'label'    	  		=> __( 'Where to display:', 'zhsocialsharing' ),
					'scrn_text'   		=> __( 'Where on the page should the buttons be displayed?', 'zhsocialsharing' ),
					'id'          		=> self::SETTING_ID_BUTTON_POSITIONS,
					'type'        	    => 'checkbox_multiple',
					'sanitize_callback' => 'sanitize_button_positions',
					'default'     		=> self::$default_button_positions,
					'choices'    	 	=> array(
						'after_post_title'      => __( 'Below the post title', 'zhsocialsharing' ),
						'float_left'            => __( 'Floating on the left area', 'zhsocialsharing' ),
						'after_post_content'    => __( 'After the post content', 'zhsocialsharing' ),
						'inside_featured_image' => __( 'Inside the featured image', 'zhsocialsharing' ),
					),
				),
			);
		}

		/**
		 * Sets the default social networking buttons. 
		 *
		 * @param array $value Social networks.
		 */
		public static function set_default_social_networks( $value ) {
			self::$default_social_networks = $value;
		}

		/**
		 * Sets the default order of the social buttons. 
		 *
		 * @param array $value Ordered social networking buttons.
		 */
		public static function set_default_button_order( $value ) {
			self::$default_button_order = $value;
		}

		/**
		 * Sets the default option for determining whether the user can use a custom hex color or not. 
		 *
		 * @param bool $value
		 */
		public static function set_default_has_custom_color( $value ) {
			self::$default_has_custom_color = $value;
		}

		/**
		 * Sets the default hex color for the buttons. 
		 *
		 * @param string $value
		 */
		public static function set_default_hex_color( $value ) {
			self::$default_hex_color = $value;
		}

		/**
		 * Sets the default size for the social networking buttons. 
		 *
		 * @param string $value
		 */
		public static function set_default_button_size( $value ) {
			self::$default_button_size = $value;
		}

		/**
		 * Sets the default post types where the buttons will display. 
		 *
		 * @param array $value
		 */
		public static function set_default_post_types( $value ) {
			self::$default_post_types = $value;
		}

		/**
		 * Sets the default positions on a single page/post, where the buttons will display. 
		 *
		 * @param array $value
		 */
		public static function set_default_button_positions( $value ) {
			self::$default_button_positions = $value;
		}

		/*
		|--------------------------------------------------------------------------
		| Miscellaneous
		|--------------------------------------------------------------------------
		|
		| Methods to retrieve post type data and default options data.
		*/

		/**
		 * Retrieves all meta info related to a single option.
		 *
		 * @param string $setting_id The ID/Name of an option. 
		 * @return array|string 
		 */	
		public function get_single_option_data_set( $setting_id ) {
			$settings_data = $this->get_settings_data();
			
			foreach($settings_data as $key => $value) {
			    if ( in_array( $setting_id, $value ) ) {
					return $settings_data[$key];					
				}
			}
			
			return 'setting not found';
		}
		
		/**
		 * Retrieves WordPress Posts Types as objects. 
		 *
		 * Retrieves the default 'post' and 'page' Post Types, including all Custom 
		 * Post Types as well.
		 *
		 * @return array
		 */
		public function get_post_types_as_objects() {
			$args = array( 
				'public'   => true,
				'_builtin' => false,
			);	
			$custom_post_types = get_post_types( $args, 'objects' ); 
			$builtin_post_types = array_merge( array( 'post' => get_post_type_object( 'post' ) ), array( 'page' => get_post_type_object( 'page' ) ) ); 
			$post_types = array_merge( $builtin_post_types, $custom_post_types );
			
			return $post_types;
		}
		
		/**
		 * Retrieves all default Post Type choices for the settings page. 
		 *
		 * @return array
		 */
		public function get_post_type_choices() {
			$post_types = $this->get_post_types_as_objects(); 
			$post_type_choices = array();
			
			foreach( $post_types as $post_type ) {
				$post_type_choices[$post_type->name] = $post_type->label;
			}
			
			return $post_type_choices;
		}	
		
	}
	
	ZH_Settings::get_instance();	
	 
endif;