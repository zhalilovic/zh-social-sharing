<?php
/**
 * 
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit the script if accessed directly.
}

?>

<div class="wrap zh-social-sharing">
			
	<form action="options.php" method="post">
		
		<?php settings_fields( 'zh-social-sharing-options' ); ?>
		<?php do_settings_sections( 'zh-social-sharing-options' ); ?>
			
		<?php submit_button( esc_html__( 'Save changes', 'zhsocialsharing' ), 'primary', 'zh-submit-button' ); ?>
		
	</form>
	
	<br class="clear" />
	
</div><!-- .wrap -->
 
 