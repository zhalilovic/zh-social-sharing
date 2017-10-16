<?php
/**
 * 
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit the script if accessed directly.
}
 
if ( ! class_exists( 'ZH_Settings' ) ) :

	class ZH_Settings {
		
		private $settings_data = array();
		private static $settings_group_name = 'zh-social-sharing-options'; 
		private $settings_section = 'zh-main-settings';
		
		/** Options names. */
		const SETTING_ID_SOCIAL_NETWORKS  = 'zh_social_networks';
		const SETTING_ID_BUTTON_ORDER     = 'zh_button_order';
		const SETTING_ID_CUSTOM_COLOR     = 'zh_custom_color';
		const SETTING_ID_HEX_COLOR        = 'zh_hex_color';
		const SETTING_ID_BUTTON_SIZES     = 'zh_button_sizes';
		const SETTING_ID_POST_TYPES       = 'zh_post_types';
		const SETTING_ID_BUTTON_POSITIONS = 'zh_button_positions';
		
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
			add_action( 'admin_init', array( $this, 'setup_settings' ) );
		}
		
		public function setup_settings() {
			// Create section of Page
 			$page = self::$settings_group_name;
 			$plugin_data = get_plugin_data( ZH_PLUGIN_DIR . '/zh-social-sharing.php' ); 
 			
 			add_settings_section( $this->settings_section, $plugin_data['Name'], '', $page );
 			
 			$default_options = $this->get_settings_data();
 			
 			foreach ( $default_options as $option ) {
 				if ( isset( $option['id'] ) ) {
	 				register_setting( self::$settings_group_name, $option['id'], array( $this, $option['sanitize_callback'] ) );	 				
	 				add_settings_field( $option['id'], $option['label'], array( $this, 'output_fields' ), $page, $this->settings_section, $option );
	 				
	 				if ( false === get_option( $option['id'] ) ) { // Nothing yet saved
	 					update_option( $option['id'], $option['default'] );
	 				}
 				}
	 			
 			}
		}
		
		public function sanitize_social_networks( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( self::SETTING_ID_SOCIAL_NETWORKS, 'invalid-zh-no-social-networks', __( 'At least one social network needs to be selected for display.', 'zhsocialsharing' ) );
				$selected_options = get_option( self::SETTING_ID_SOCIAL_NETWORKS ); // Set selected options to previously valid ones that are already in the database.
			} 
			
			$social_networks_data = $this->get_single_option_data_set( self::SETTING_ID_SOCIAL_NETWORKS );
			
			if ( ! 'setting not found' ===  $social_networks_data ) {
				
				$all_social_network_names = array_keys( $social_networks_data['choices'] );
				$contains_all_valid_social_networks = ! array_diff( $selected_options, $all_social_network_names );
				
				if ( ! $contains_all_valid_social_networks ) { 
					add_settings_error( self::SETTING_ID_SOCIAL_NETWORKS, 'invalid-zh-social-networks', __( 'Tampering with the data is not allowed!', 'zhsocialsharing' ) );
					$selected_options = get_option( self::SETTING_ID_SOCIAL_NETWORKS );
				}
			}
			
			return $selected_options;
		}
		
		public function sanitize_post_types( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( self::SETTING_ID_POST_TYPES, 'invalid-zh-no-post-types', __( 'At least one Post Type needs to be selected, where the buttons will show.', 'zhsocialsharing' ) );
				$selected_options = get_option( self::SETTING_ID_POST_TYPES ); // Set selected options to previously valid ones that are already in the database.
			} 
			
			$post_types_data = $this->get_single_option_data_set( self::SETTING_ID_POST_TYPES );
			
			if ( ! 'setting not found' ===  $post_types_data ) {
				
				$all_post_type_names = array_keys( $post_types_data['choices'] );
				$contains_all_valid_post_types = ! array_diff( $selected_options, $all_post_type_names );
				
				if ( ! $contains_all_valid_post_types ) { 
					add_settings_error( self::SETTING_ID_POST_TYPES, 'invalid-zh-post-types', __( 'Tampering with the data is not allowed!', 'zhsocialsharing' ) );
					$selected_options = get_option( self::SETTING_ID_POST_TYPES );
				}
			}
			
			return $selected_options;
		}
		
		public function sanitize_button_positions( $selected_options ) {
			if ( ! isset( $selected_options ) ) {
				add_settings_error( self::SETTING_ID_BUTTON_POSITIONS, 'invalid-zh-no-positions', __( 'At least one position needs to be selected for the buttons, which determines where they will be displayed on the page.', 'zhsocialsharing' ) );
				$selected_options = get_option( self::SETTING_ID_BUTTON_POSITIONS ); // Set selected options to previously valid ones that are already in the database.
			} 
			
			$button_positions_data = $this->get_single_option_data_set( self::SETTING_ID_BUTTON_POSITIONS );
			
			if ( ! 'setting not found' ===  $button_positions_data ) {
				
				$all_button_positions = array_keys( $button_positions_data['choices'] );
				$contains_all_valid_button_positions = ! array_diff( $selected_options, $all_button_positions );
				
				if ( ! $contains_all_valid_button_positions ) { 
					add_settings_error( self::SETTING_ID_POST_TYPES, 'invalid-zh-positions', __( 'Tampering with the data is not allowed!', 'zhsocialsharing' ) );
					$selected_options = get_option( self::SETTING_ID_POST_TYPES );
				}
			}
			
			return $selected_options;
		}
		
		public function sanitize_checkbox( $value ) {
			return isset( $value ) ? true : false;			
		}
		
		public function sanitize_button_order( $settings ) {
			$button_order_data_set = $this->get_single_option_data_set( self::SETTING_ID_BUTTON_ORDER );
			$default_button_order = $button_order_data_set['default'];
			$current_button_order = $settings;
			
			sort( $default_button_order );
			sort( $current_button_order );
			
			if ( $default_button_order !== $current_button_order ) { //equal}
				add_settings_error( self::SETTING_ID_BUTTON_ORDER, 'invalid-zh-button-order', __( 'Tampering with the data is not allowed!', 'zhsocialsharing' ) );
				$settings = get_option( self::SETTING_ID_BUTTON_ORDER ); // Set selected options to previously valid ones that are already in the database.	
			}
			
			return $settings;		
		}
		
		public function sanitize_hex_color( $value ) {
			if ( ! $value ) {
				add_settings_error( self::SETTING_ID_HEX_COLOR, 'invalid-zh-empty-color', __( 'The color field can not be empty.', 'zhsocialsharing' ) );
				$value = get_option( self::SETTING_ID_HEX_COLOR ); // Set selected option to previously valid one that was already in the database.
			} 
			
			if ( ! sanitize_hex_color( $value ) ) {
				add_settings_error( self::SETTING_ID_HEX_COLOR, 'invalid-zh-hex-color', __( 'Please enter a valid hex color in the color picker setting.', 'zhsocialsharing' ) );
				$value = get_option( self::SETTING_ID_HEX_COLOR );
			}
			
			return $value;			
		}

		public function get_single_option_data_set( $setting_id ) {
			$settings_data = $this->get_settings_data();
			
			foreach($settings_data as $key => $value) {
			    if ( in_array( $setting_id, $value ) ) {
					return $settings_data[$key];					
				}
			}
			
			return 'setting not found';
		}
		
		public function output_fields( $option ) {
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
									<?php checked( in_array( $key, get_option( $option['id'] ) ), true ); ?> 
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
									<?php checked( in_array( $key, get_option( $option['id'] ) ), true ); ?>
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
							value="<?php echo esc_attr( get_option( $option['id'] ) ); ?>" 
							<?php checked( get_option( $option['id'] ), true ); ?>  
						/>
						<span><?php echo esc_html( $option['desc'] ); ?></span>
					</label>
				
				<?php
					break;
					
				case 'sort':
				 	ZH_Button_Renderer::output_icons( get_option( $option['id'] ), get_option( self::SETTING_ID_CUSTOM_COLOR ), get_option( self::SETTING_ID_HEX_COLOR ), $option['id'] );				 	
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
		
		public function get_settings_data() {			
			$this->settings_data = array(
				
				'social_networks' => array(
					'label'    	  		=> __( 'Include buttons for:', 'zhsocialsharing' ),
					'scrn_text'   		=> __( 'Which social media buttons should be displayed?', 'zhsocialsharing' ),
					'id'          		=> self::SETTING_ID_SOCIAL_NETWORKS,
					'type'        	    => 'checkbox_multiple',
					'sanitize_callback' => 'sanitize_social_networks',
					'default'     		=> array( 'facebook', 'twitter', 'google_plus', 'pinterest', 'linkedin', 'whatsapp' ),
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
					'default'     		=> array( 'zh-facebook', 'zh-twitter', 'zh-google_plus', 'zh-pinterest', 'zh-linkedin', 'zh-whatsapp' ),
				),
				
				'custom_color' => array(
					'label'    			=> __( 'Custom Color:', 'zhsocialsharing' ),
					'desc'    			=> __( 'Enable custom color', 'zhsocialsharing' ),
					'id'      		 	=> self::SETTING_ID_CUSTOM_COLOR,
					'type'     			=> 'checkbox',
					'sanitize_callback' => 'sanitize_checkbox',
					'default'  			=> false,
				),
				
				'hex_color' => array(
					'label'    			=> __( 'Choose a custom color for your buttons:', 'zhsocialsharing' ),
					'desc'     			=> __( 'Enable the Custom Color option above to be able to choose custom colors for your buttons.', 'zhsocialsharing' ),
					'id'       			=> self::SETTING_ID_HEX_COLOR ,
					'type'     			=> 'color',
					'sanitize_callback' => 'sanitize_hex_color',
					'default'  			=> '#00c964',
				),
				
				'button_sizes' => array(
					'label'    			=> __( 'Choose the size in which to display the social buttons:', 'zhsocialsharing' ),
					'id'      			=> self::SETTING_ID_BUTTON_SIZES,
					'type'     			=> 'radio',
					'sanitize_callback' => 'sanitize_button_sizes',
					'default'  			=> array( 'medium' ),
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
					'default' 			=> array( 'post' ),
					'choices' 			=> $this->get_post_type_choices(),	
				),
				
				'button_positions' => array(
					'label'    	  		=> __( 'Where to display:', 'zhsocialsharing' ),
					'scrn_text'   		=> __( 'Where on the page should the buttons be displayed?', 'zhsocialsharing' ),
					'id'          		=> self::SETTING_ID_BUTTON_POSITIONS,
					'type'        	    => 'checkbox_multiple',
					'sanitize_callback' => 'sanitize_button_positions',
					'default'     		=> array( 'after_post_title', 'after_post_content' ),
					'choices'    	 	=> array(
						'after_post_title'      => __( 'Below the post title', 'zhsocialsharing' ),
						'float_left'            => __( 'Floating on the left area', 'zhsocialsharing' ),
						'after_post_content'    => __( 'After the post content', 'zhsocialsharing' ),
						'inside_featured_image' => __( 'Inside the featured image', 'zhsocialsharing' ),
					),
				),

				
			);
			
			return $this->settings_data;
		}
		
		public function get_post_types_as_objects() {
			$args = array( 
				'public' => true,
				'_builtin' => false,
			);	
			$custom_post_types = get_post_types( $args, 'objects' ); 
			$builtin_post_types = array_merge( array( 'post' => get_post_type_object( 'post' ) ), array( 'page' => get_post_type_object( 'page' ) ) );
			$post_types = array_merge( $builtin_post_types, $custom_post_types );
			
			return $post_types;
		}
		
		public function get_post_type_choices() {
			$post_types = $this->get_post_types_as_objects(); 
			$post_type_choices = array();
			
			foreach( $post_types as $post_type ) {
				$post_type_choices[$post_type->name] = $post_type->label;
			}
			
			return $post_type_choices;
		}
		
		public static function display_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}
			
			include ZH_PLUGIN_DIR . 'inc/html-settings-page.php';
		}	
		
	}
	
	ZH_Settings::get_instance();	
	 
endif;