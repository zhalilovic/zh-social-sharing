<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

/**
 * Loads the ZH_Settings class.
 */
require_once( 'inc/class-zh-settings.php' );

// Delete all options saved by the plugin 
delete_option( ZH_Settings::SETTING_ID_SOCIAL_NETWORKS );
delete_option( ZH_Settings::SETTING_ID_BUTTON_ORDER );
delete_option( ZH_Settings::SETTING_ID_CUSTOM_COLOR );
delete_option( ZH_Settings::SETTING_ID_HEX_COLOR );
delete_option( ZH_Settings::SETTING_ID_BUTTON_SIZES );
delete_option( ZH_Settings::SETTING_ID_POST_TYPES );
delete_option( ZH_Settings::SETTING_ID_BUTTON_POSITIONS );




