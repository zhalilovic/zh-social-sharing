<?php
/**
 * Settings page HTML
 *
 * @author   Zlatan Halilovic
 * @package  zh-social-sharing
 * @since 	 1.0.0
 */
 
// Exit the script if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>

<div class="wrap zh-social-sharing">
			
	<form action="options.php" method="post">
		
		<?php settings_fields( 'zh-social-sharing-options' ); ?>
		<?php do_settings_sections( 'zh-social-sharing-options' ); ?>
			
		<?php submit_button( esc_html__( 'Save changes', 'zhsocialsharing' ), 'primary', 'zh-submit-button' ); ?>
		
	</form>
	<br class="clear" />
	<hr />	
	<br class="clear" />
	
	<h3><?php printf( esc_html__( 'Instructions for the %s shortcode', 'zhsocialsharing' ), '[zh_social_sharing]'); ?></h3>	
	<p><?php echo esc_html__( 'Copy and paste one of the following shortcode examples directly into any WordPress post or page to display the sharing buttons.', 'zhsocialsharing' ); ?></p>
	
	<p class="shortcode-example"><span>[zh_social_sharing]</span></p>
	<p class="shortcode-example"><span>[zh_social_sharing social_networks="facebook, twitter, pinterest" size="medium"]</span></p>
	<p class="shortcode-example"><span>[zh_social_sharing social_networks="google_plus, pinterest, facebook, linkedin" color="#376fdf" size="large"]</span></p>	
	<br class="clear" />
	
	<div><?php echo esc_html__( 'The following are the available attributes for this shortcode:', 'zhsocialsharing' ); ?></div>			
	<?php $default_value_str = esc_html__( 'Default value:', 'zhsocialsharing' ); ?>
	
	<table class="form-table">
		<tr valign="top">
			<th scope="row">social_networks</th>
			<td>
				<p><?php echo esc_html__( 'Defines the sharing links. Put any of the six offered social networks here, in the order in which you want them displayed in your content.', 'zhsocialsharing' ); ?></p>	
				<p><?php echo $default_value_str; ?> <em>facebook, twitter, google_plus, pinterest, linkedin, whatsapp</em></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">color</th>
			<td>
				<p><?php echo esc_html__( 'Defines the color of your buttons. If no color is supplied here, the buttons will display in their original, "brand" colors.', 'zhsocialsharing' ); ?></p>
				<p><?php echo $default_value_str; ?> none</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">size</th>
			<td>
				<p><?php echo esc_html__( 'Defines the size of your buttons. Available values are small, medium, and large.', 'zhsocialsharing' ); ?></p>
				<p><?php echo $default_value_str; ?> <em>medium</em></p>
			</td>
		</tr>
	</table>
	<br class="clear" />

</div><!-- .wrap -->
 
 