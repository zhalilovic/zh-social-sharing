<?php

// Exit the script if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
 
if ( ! class_exists( 'ZH_Button_Renderer' ) ) :

	/**
	 * Button Render Class
	 *
	 * Display the social sharing buttons.
	 *
	 * @class 		ZH_Button_Renderer
	 * @version		1.0.0
	 * @author 		Zlatan Halilovic
	 */
	class ZH_Button_Renderer {
		
		/**
		 * The number of times the_title filter is triggered in the main loop.
		 *
		 * @var int
		 */
		private $did_filter = 0;

		/**
		 * HTML class for the buttons' main ul element, that displays after the post's main title.
		 *
		 * @var string
		 */
		private $buttons_title_class = 'zh-title-buttons';

		/**
		 * HTML class for the buttons' main ul element, that displays in the left-hand side of the browser window.
		 *
		 * @var string
		 */
		private $buttons_floating_class = 'zh-floating-buttons';
		
		/**
		 * ZH_Button_Renderer Constructor.
		 */							
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hooks into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'the_content', array( $this, 'display_buttons_after_the_content' ) );
			add_filter( 'the_title', array( $this, 'display_buttons_after_the_title' ), 10, 2 );
			add_filter( 'post_thumbnail_html', array( $this, 'display_buttons_inside_the_post_thumbnail' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'display_left_floating_buttons' ) );
			add_shortcode( 'zh_social_sharing', array( $this, 'social_sharing_shortcode' ) );
		}
		
		/**
		 * Displays the sharing buttons after the_content.
		 *
		 * @global WP_Post $post
		 * @param string $content 
		 * @return string
		 */
		public function display_buttons_after_the_content( $content ) {
			global $post;
			
			// Render the buttons only on single posts or pages of allowed Post Types and in the main loop.
			if( $this->is_allowed_post_type( $post->ID ) && $this->is_button_position_active( 'after_post_content' ) && is_singular() && is_main_query() && in_the_loop() ) {
				$new_content = self::output_buttons( 
					get_option( ZH_Settings::SETTING_ID_BUTTON_ORDER ), 
					get_option( ZH_Settings::SETTING_ID_HEX_COLOR ), 
					get_option( ZH_Settings::SETTING_ID_BUTTON_SIZES ), 
					get_option( ZH_Settings::SETTING_ID_CUSTOM_COLOR ) 
				);
				$content .= $new_content;	
			}	
			return $content;
		}
		
		/**
		 * Displays the sharing buttons after the main title.
		 *
		 * Renders the buttons only on single posts or pages of allowed Post Types and in the main loop.
		 *
		 * @global WP_Query $wp_query
		 * @param string $title The title of the page or post. 
		 * @param int $id The post ID
		 * @return string
		 */
		public function display_buttons_after_the_title( $title, $id = NULL ) {
		    global $wp_query;

		    if( $id !==  $wp_query->queried_object_id ) { // We are not in the main loop.
		        return $title; 
		    }

			// Render the buttons only on single posts or pages of allowed Post Types and in the main loop.    
			if( $this->is_allowed_post_type( $id ) && $this->is_button_position_active( 'after_post_title' ) && is_singular() && is_main_query() && in_the_loop() ) {
				$this->did_filter++; // Increment the number of the_title filters done.
				
				if ( 1 === $this->did_filter ) { // The first the_title filter in the main loop is usually the main title of the page/post.
					$new_title = self::output_buttons( 
						get_option( ZH_Settings::SETTING_ID_BUTTON_ORDER ), 
						get_option( ZH_Settings::SETTING_ID_HEX_COLOR ), 
						get_option( ZH_Settings::SETTING_ID_BUTTON_SIZES ), 
						get_option( ZH_Settings::SETTING_ID_CUSTOM_COLOR ),
						$this->buttons_title_class
					);
				    $title .= $new_title;
				}
			}
			
			return $title;
		} 
        
        /**
		 * Displays the sharing buttons inside the post thumbnail.
		 *
		 * Renders the buttons only on single posts or pages of allowed Post Types and in the main loop.
		 *
		 * @param string $html Post thumbnail's HTML
		 * @param int $post_id The post ID 
		 * @return string
		 */		
		public function display_buttons_inside_the_post_thumbnail( $html, $post_id ) {
			if ( ! empty( $html ) && $this->is_allowed_post_type( $post_id ) && $this->is_button_position_active( 'inside_featured_image' ) && is_singular() && is_main_query() && in_the_loop() ) {
				$custom_output  = '<div class="zh-social-sharing-thumbnail-wrap">';
				$custom_output .= $html;
				$custom_output .= self::output_buttons( 
					get_option( ZH_Settings::SETTING_ID_BUTTON_ORDER ), 
					get_option( ZH_Settings::SETTING_ID_HEX_COLOR ), 
					get_option( ZH_Settings::SETTING_ID_BUTTON_SIZES ), 
					get_option( ZH_Settings::SETTING_ID_CUSTOM_COLOR ) 
				);
				$custom_output .= '</div>';
				$html = $custom_output;	
			}
		
			return $html;
		}
		
		/**
		 * Displays the sharing buttons on the left-hand side of the browser window.
		 *		 
		 * @global WP_Post $post
		 */	
		public function display_left_floating_buttons() {
			global $post;
			
			if( $this->is_allowed_post_type( $post->ID ) && $this->is_button_position_active( 'float_left' ) && is_singular() ) {
				echo self::output_buttons( 
					get_option( ZH_Settings::SETTING_ID_BUTTON_ORDER ), 
					get_option( ZH_Settings::SETTING_ID_HEX_COLOR ), 
					get_option( ZH_Settings::SETTING_ID_BUTTON_SIZES ), 
					get_option( ZH_Settings::SETTING_ID_CUSTOM_COLOR ),
					$this->buttons_floating_class
				);
			}
		}
		
		/**
		 * Checks to see if the given post type is allowed.
		 *		 
		 * @param id $post_id The post ID
		 * @return bool
		 */	
		public function is_allowed_post_type( $post_id ) {
		    if ( $post_id ) {
				$allowed_post_types = (array) get_option( ZH_Settings::SETTING_ID_POST_TYPES ); 
				
		        if ( in_array( get_post_type( $post_id ), $allowed_post_types ) ) {
		            return true;
		        }
		        else {
			        return false;
		        }
		    } 
		    else {
				return false;
		    }
		}
		
		/**
		 * Checks to see if the given position of the button is active or allowed.
		 *		 
		 * @param string $position The section of the page/post where the buttons can appear.
		 * @return bool
		 */	
		public function is_button_position_active( $position ) {				
	        if ( in_array( $position, (array) get_option( ZH_Settings::SETTING_ID_BUTTON_POSITIONS ) ) ) {
	            return true;
	        }
	        else {
		        return false;
	        }
		}
		
		/**
		 * Runs when the zh_social_sharing shortcode is found.
		 *		 
		 * @param array $atts {
		 *     Optional. An array of shortcode arguments.
		 *
		 *     @type array  $social_networks An array of social networks with a custom order.
		 *                                   Default array('facebook', 'twitter', 'google_plus', 'pinterest', 'linkedin', 'whatsapp'). 
		 *									 Accepts any of the values above.
		 *     @type string $color A hex color for the buttons. Default ''. Accepts any hex color. 
		 *     @type string $size The size of the buttons. Default 'medium'. Accepts 'small', 'medium', and 'large'.
		 * }		 
		 * @return string
		 */
		public function social_sharing_shortcode( $atts ) {			
			extract( shortcode_atts( array(
				'social_networks' => ZH_Settings::get_default_social_networks(),
				'color' 	  	  => '',
				'size'	 		  => ZH_Settings::get_default_button_size(),
			), $atts, 'zh_social_sharing' ));
			
			if ( is_string( $social_networks ) ) {
				$social_networks = preg_replace('/\s+/', '', $social_networks); // Strip all whitespace
				$social_networks = explode( ',', $social_networks ); // Convert the social networking argument to a comma-delimited array
			}	
			
			if ( ! sanitize_hex_color( $color ) ) {
				$color = '';
			}	
			
			$size = sanitize_html_class( preg_replace('/\s+/', '', $size), ZH_Settings::get_default_button_size() ); 	
															
			return $this->output_buttons( $social_networks, $color, $size );
		}
		
		/**
		 * Retrieves the HTML for the social networking sharing buttons.
		 *
		 * Gets the sharing buttons' HTML for either the plugin's settings page or the front-end.
		 *
		 * @global WP_Post $post 
		 * @param array  $social_networks Chosen social networks, defined in the order in which they will display.
		 * @param string $hex_color The color of the buttons.
		 * @param string $button_size The size of the sharing buttons.
		 * @param bool   $colors_allowed Determines whether a custom hex color should be applied to the buttons or not.
		 * @param string|NULL $source_class The class applied to the main ul element.
		 * @param string|NULL $button_order_option_id The option ID for the button order.
		 * @return string
		 */	
		public static function output_buttons( $social_networks, $hex_color, $button_size, $colors_allowed = true, $source_class = NULL, $button_order_option_id = NULL ) {
			ob_start();
			
			global $post;
			
			if ( ! is_admin() ) { 
				// Get current page URL
				$url = urlencode( get_permalink( $post->ID ) );
				
				/* 
				 * Get current page title by using the single_post_title() function
				 * in order not to evoke the_title filter.
				 */
				$the_title = urlencode( html_entity_decode( single_post_title( '', false ), ENT_COMPAT, 'UTF-8' ) ); 
			
				// Get Post Thumbnail for pinterest.
				$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				
				// Construct sharing URLs without using any scripts.
				$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
				$twitterURL = 'https://twitter.com/intent/tweet?text=' . $the_title . '&amp;url=' . $url;
				$googleURL = 'https://plus.google.com/share?url=' . $url;
				$pinterestURL = 'https://pinterest.com/pin/create/button/?url=' . $url . '&amp;media=' . $post_thumbnail[0] . '&amp;description=' . $the_title;
				$linkedinURL = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&amp;title=' . $the_title;
				$whatsappURL = 'whatsapp://send?text=' . $the_title . ' ' . $url;
			}
			
			if ( is_admin() ) { 
				$ul_class = 'zh-sortable';	
			} 
			else { 
				$ul_class = 'zh-social-sharing-buttons';
			}
			
			if ( isset( $source_class ) ) { 
				$ul_class .= ' ' . $source_class;
			}
			
		?>
			<ul class="group <?php echo esc_attr( $ul_class ); ?>">
				
				<?php foreach ( $social_networks as $social_network ) : ?>
								
					<li id="<?php echo esc_attr( $social_network ); ?>" <?php if ( ! wp_is_mobile() && ( $social_network === 'zh-whatsapp' || $social_network === 'whatsapp' ) ) { ?>class="whatsapp-hide-mobile"<?php } ?>>
					
						<?php if ( is_admin() ) : ?>
							<input  
								type='hidden' 
								name="<?php echo esc_attr( $button_order_option_id ); ?>[]" 
								value="<?php echo esc_attr( $social_network ); ?>"
							/>
						<?php endif; ?>
						
						<?php 
						switch ( $social_network ) : 
							
							case 'facebook';
							case 'zh-facebook':
			
						?>
									
							<?php if ( ! is_admin() ) { ?>
							<a href="<?php echo $facebookURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
							<?php } ?>
					
								<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
								
								 	<g>
										<rect id="zh-facebook-back" fill="#3A5A99" width="128" height="128" 
											<?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> 
										/>
										<path id="zh-facebook-facebook" fill="#FFFFFF" d="M95.8838,28.1602H32.1162c-2.1855,0-3.956,1.7705-3.956,3.956v63.7676
											c0,2.1846,1.7705,3.956,3.956,3.956h34.3301V72.082h-9.3408V61.2637h9.3408v-7.9776c0-9.2588,5.6543-14.2998,13.9141-14.2998
											c3.956,0,7.3554,0.2945,8.3466,0.4263v9.6753l-5.7275,0.0024c-4.4922,0-5.3613,2.1348-5.3613,5.2666v6.9068h10.7119L86.9355,72.082
											h-9.3173v27.7578h18.2656c2.1846,0,3.956-1.7714,3.956-3.956V32.1162C99.8398,29.9307,98.0684,28.1602,95.8838,28.1602z" />
									</g>
								</svg>
							
							<?php if ( ! is_admin() ) { ?>
								<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#3A5A99'; ?>">
									<?php echo esc_html__( 'Facebook', 'zh-social-sharing' ); ?>
								</span>
							</a>
							<?php } ?>
							
							<?php 
								break;
							
							case 'twitter':	
							case 'zh-twitter':
							?>
								
								<?php if ( ! is_admin() ) { ?>
								<a href="<?php echo $twitterURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
								<?php } ?>
								
									<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										 
										<g>	 
											<rect id="zh-twitter-back" fill="#55ACEE" width="128" height="128" 
												<?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> 
											/>
											<path id="zh-twitter-twitter" fill="#FFFFFF" d="M99.8398,41.7695c-2.6367,1.17-5.4707,1.96-8.4462,2.3155
												c3.0361-1.8204,5.3681-4.7022,6.4658-8.1358c-2.8408,1.6846-5.9883,2.9092-9.3379,3.5684
												c-2.6826-2.8584-6.5049-4.6436-10.7344-4.6436c-8.1221,0-14.7065,6.584-14.7065,14.7051c0,1.1533,0.1303,2.2754,0.3808,3.3516
												c-12.2221-0.6133-23.0581-6.4678-30.311-15.3653c-1.2661,2.1719-1.9912,4.6983-1.9912,7.3936c0,5.1015,2.5962,9.6025,6.542,12.2402
												c-2.4107-0.0762-4.6783-0.7383-6.6607-1.8398c-0.0014,0.0615-0.0014,0.123-0.0014,0.1855c0,7.125,5.0693,13.0684,11.7968,14.4199
												c-1.2343,0.336-2.5337,0.5157-3.8745,0.5157c-0.9477,0-1.8691-0.0928-2.7671-0.2637c1.8716,5.8418,7.3028,10.0937,13.7378,10.2129
												c-5.0332,3.9443-11.374,6.2949-18.2641,6.2949c-1.187,0-2.3575-0.0693-3.5078-0.2051c6.5083,4.1729,14.2377,6.6065,22.5429,6.6065
												c27.0498,0,41.8418-22.4082,41.8418-41.8418c0-0.6377-0.0146-1.2715-0.0429-1.9024C95.375,47.3086,97.8691,44.7188,99.8398,41.7695
												z"/>
										</g>
									</svg>
									
								<?php if ( ! is_admin() ) { ?>
									<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#55ACEE'; ?>">
										<?php echo esc_html__( 'Twitter', 'zh-social-sharing' ); ?>
									</span>
								</a>
								<?php } ?>
								
							<?php 
								break;
							
							case 'google_plus':
							case 'zh-google_plus':
							?>
								
								<?php if ( ! is_admin() ) { ?>
								<a href="<?php echo $googleURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
								<?php } ?>
								
									<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										 	 
										<g>
											<rect id="zh-google-back" fill="#DC4B3E" width="128" height="128" 
												<?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> 
											/>
											<g id="zh-google-google">
												<g>
													<path fill="#FFFFFF" d="M28.1738,64.0293c-0.4472-11.7427,9.8399-22.5938,21.5938-22.7354
														c5.9912-0.5112,11.8183,1.8145,16.3554,5.6114c-1.8603,2.0425-3.7558,4.0654-5.7744,5.9585
														c-3.9931-2.4253-8.7978-4.273-13.4619-2.6324c-7.5273,2.1426-12.0791,11.0259-9.2988,18.3877
														c2.3066,7.6856,11.6592,11.9043,18.9863,8.6739c3.7969-1.3575,6.294-4.8594,7.3916-8.6211
														c-4.3486-0.086-8.6992-0.0332-13.0478-0.1543c-0.0078-2.5869-0.0225-5.1626-0.0078-7.7505
														c7.2509-0.0108,14.5156-0.0317,21.7783,0.0347c0.4443,6.3369-0.4903,13.123-4.6094,18.2099
														c-5.6445,7.2637-16.0723,9.3946-24.5527,6.5469C34.5215,82.6016,27.9785,73.5332,28.1738,64.0293"/>
													<path fill="#FFFFFF" d="M86.8232,54.2437h6.4698c0.0107,2.1645,0.0312,4.3374,0.042,6.5024
														c2.165,0.021,4.3398,0.0298,6.5048,0.0439v6.4795c-2.165,0.0098-4.3398,0.0215-6.5048,0.0313
														c-0.0186,2.1738-0.0313,4.3388-0.042,6.5137c-2.1641-0.0098-4.3291,0-6.4825,0c-0.0214-2.1749-0.0214-4.3399-0.041-6.502
														c-2.164-0.0215-4.3398-0.0332-6.5009-0.043V60.79c2.1611-0.0141,4.3242-0.0229,6.5009-0.0439
														C86.7803,58.5811,86.8018,56.4082,86.8232,54.2437"/>
												</g>
											</g>
										</g> 
										
									</svg>
									
								<?php if ( ! is_admin() ) { ?>
									<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#DC4B3E'; ?>">
										<?php echo esc_html__( 'Google+', 'zh-social-sharing' ); ?>
									</span>
								</a>
								<?php } ?>
							
							<?php 
								break;
							
							case 'pinterest':
							case 'zh-pinterest':
							?>
								
								<?php if ( ! is_admin() ) { ?>
								<a href="<?php echo $pinterestURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
								<?php } ?>
								
									<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										 
										<rect id="zh-pinterest-back" fill="#BD081C" width="128" height="128" 
											<?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> 
										/>
										<path id="zh-pinterest-pinterest" fill="#FFFFFF" d="M91.7451,51.7451c0,16.1553-8.9746,28.2197-22.2148,28.2197
											c-4.4444,0-8.625-2.4043-10.0601-5.1347c0,0-2.3901,9.4902-2.895,11.3203c-1.7798,6.4648-7.0249,12.9394-7.4302,13.4746
											c-0.2846,0.3652-0.915,0.25-0.98-0.2344c-0.1147-0.8252-1.4497-8.9902,0.125-15.6504c0.7901-3.3398,5.295-22.435,5.295-22.435
											S52.27,58.6753,52.27,54.79c0-6.1025,3.5401-10.6572,7.94-10.6572c3.7451,0,5.5556,2.8096,5.5556,6.1797
											c0,3.7676-2.3955,9.3975-3.6352,14.6128c-1.0352,4.3696,2.1899,7.9302,6.4995,7.9302c7.7998,0,13.0498-10.0205,13.0498-21.8907
											c0-9.0224-6.0742-15.7797-17.1294-15.7797c-12.4854,0-20.2705,9.3149-20.2705,19.7202c0,3.5849,1.06,6.1172,2.7153,8.0722
											c0.7603,0.8999,0.8701,1.2627,0.5947,2.2979c-0.2045,0.7598-0.6494,2.5801-0.8447,3.3047c-0.27,1.04-1.1147,1.415-2.06,1.0303
											c-5.75-2.3506-8.4297-8.6504-8.4297-15.73c0-11.6978,9.8647-25.7202,29.4243-25.7202
											C81.4053,28.1602,91.7451,39.5327,91.7451,51.7451z"/>
									</svg>
									
								<?php if ( ! is_admin() ) { ?>
									<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#BD081C'; ?>">
										<?php echo esc_html__( 'Pinterest', 'zh-social-sharing' ); ?>
									</span>
								</a>
								<?php } ?>
								
							<?php 
								break;
							
							case 'linkedin':	
							case 'zh-linkedin':
							?>
								
								<?php if ( ! is_admin() ) { ?>
								<a href="<?php echo $linkedinURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
								<?php } ?>
								
									<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										 
										<rect id="zh-linkedin-back" fill="#0076B2" width="128" height="128" 
											<?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> 
										/>
										<g id="zh-linkedin-linkedin">
											<path fill="#FFFFFF" d="M29.0752,51.7471h14.8686v47.792H29.0752V51.7471z M36.5137,27.9897c4.7514,0,8.6089,3.8589,8.6089,8.6133
												c0,4.7554-3.8575,8.6143-8.6089,8.6143c-4.7705,0-8.6172-3.8589-8.6172-8.6143C27.8965,31.8486,31.7432,27.9897,36.5137,27.9897"
												/>
											<path fill="#FFFFFF" d="M53.2622,51.7471H67.5v6.5337h0.2041c1.9805-3.7574,6.8272-7.7198,14.0537-7.7198
												c15.0391,0,17.8184,9.896,17.8184,22.7671v26.211H84.7246V76.2979c0-5.542-0.0957-12.6719-7.7187-12.6719
												c-7.7286,0-8.9082,6.04-8.9082,12.2754v23.6377H53.2622V51.7471z"/>
										</g>
									</svg>
									
								<?php if ( ! is_admin() ) { ?>
									<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#0076B2'; ?>">
										<?php echo esc_html__( 'LinkedIn', 'zh-social-sharing' ); ?>
									</span>
								</a>
								<?php } ?>
								
							<?php 
								break;
							
							case 'whatsapp':
							case 'zh-whatsapp':
							?>
								
								<?php if ( ! is_admin() ) { ?>
								<a href="<?php echo $whatsappURL; ?>" class="zh-social-sharing-links zh-<?php echo esc_attr( $button_size ); ?>-button-size group" rel="external nofollow" target="_blank">
								<?php } ?>
								
									<svg version="1.1" id="<?php echo esc_attr( $social_network ); ?>-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 	 viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										 
										<rect id="zh-whatsapp-back" fill="#25D366" width="128" height="128" <?php if ( $colors_allowed && $hex_color ) { ?>style="fill: <?php echo esc_attr( $hex_color ); ?>"<?php } ?> />
										<g id="zh-whatsapp-whatsapp">
											<path fill="#FFFFFF" d="M28.1602,100.0127l5.0634-18.4951c-3.124-5.4121-4.7666-11.5518-4.7646-17.8418
												c0.0078-19.6787,16.0185-35.6885,35.6904-35.6885c9.5479,0.0039,18.5088,3.7207,25.2471,10.4678
												c6.7383,6.7451,10.4473,15.7119,10.4433,25.249C99.832,83.3818,83.8203,99.3936,64.1504,99.3936c-0.001,0,0.001,0,0,0h-0.0156
												c-5.9727-0.002-11.8418-1.501-17.0547-4.3448L28.1602,100.0127z M47.9561,88.5879l1.0839,0.6426
												c4.5538,2.7031,9.7754,4.1328,15.0977,4.1357h0.0127c16.3496,0,29.6572-13.3076,29.6641-29.665
												c0.0029-7.9258-3.0801-15.3789-8.6807-20.9864c-5.6006-5.6064-13.0488-8.6962-20.9717-8.6992
												c-16.3633,0-29.6709,13.3067-29.6767,29.6621c-0.003,5.6045,1.5654,11.0635,4.5351,15.7862l0.7051,1.123l-2.9961,10.9463
												L47.9561,88.5879z"/>
											<path fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M82.1309,72.1904c-0.2237-0.372-0.8174-0.5947-1.709-1.042
												c-0.8926-0.4463-5.2764-2.6035-6.0938-2.9013c-0.8174-0.2969-1.4121-0.4463-2.0068,0.4463
												c-0.5947,0.8935-2.3037,2.9023-2.8233,3.497c-0.5205,0.5948-1.041,0.67-1.9326,0.2237c-0.8916-0.4463-3.7646-1.3887-7.1709-4.4268
												c-2.6513-2.3643-4.4414-5.2852-4.9609-6.1777c-0.5205-0.8926-0.0557-1.375,0.3906-1.8194
												c0.4014-0.4004,0.8916-1.042,1.3379-1.5625c0.4453-0.5205,0.5938-0.8935,0.8916-1.4882c0.2969-0.5948,0.1484-1.1163-0.0742-1.5625
												c-0.2236-0.4463-2.0068-4.836-2.75-6.6211c-0.7236-1.7383-1.459-1.503-2.0059-1.5313c-0.5195-0.0254-1.1152-0.0312-1.7099-0.0312
												c-0.5938,0-1.5606,0.2236-2.378,1.1162c-0.8173,0.8925-3.1211,3.0498-3.1211,7.4394c0,4.3897,3.1954,8.6299,3.6417,9.2246
												c0.4453,0.5957,6.288,9.6035,15.2343,13.4659c2.127,0.9189,3.7881,1.4677,5.084,1.8789c2.1358,0.6787,4.0801,0.583,5.6162,0.3535
												c1.7139-0.2569,5.2764-2.1573,6.0196-4.2403C82.3535,74.3477,82.3535,72.5625,82.1309,72.1904z"/>
										</g>
									</svg>
									
								<?php if ( ! is_admin() ) { ?>
									<span class="zh-social-network-names" style="background: <?php echo ( $colors_allowed && $hex_color ) ? esc_attr( $hex_color ) : '#25D366'; ?>">
										<?php echo esc_html__( 'WhatsApp', 'zh-social-sharing' ); ?>
									</span>
								</a>
								<?php } ?>
							
							<?php 				
								break;
								
							default:
							
								break;
							?>
							
						<?php 
						endswitch; 
						?>
																																				
					</li>
				
				<?php 
				endforeach; 
				?>
				
			</ul>
				
		<?php
			$content = ob_get_clean();

			return $content;
		}	
		
	}
	
	new ZH_Button_Renderer();
		 
endif;