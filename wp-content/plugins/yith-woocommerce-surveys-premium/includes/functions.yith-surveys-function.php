<?php
if( !defined( 'ABSPATH' ) )
exit;

if( !function_exists( 'ywcsur_is_premium_active') ) {
    /**
     * check if premium version is active
     * @author YIThemes
     * @since 1.0.0
     * @return bool
     */
    function ywcsur_is_premium_active(){

        return defined( 'YITH_WC_SURVEYS_PREMIUM' ) && YITH_WC_SURVEYS_PREMIUM;
    }
}

/**
 * Remove the unused option ywcsur_enable_plugin
 *
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 * @since 1.2.0
 * @return void
 */
if( ! function_exists( 'yith_surveys_update_1_1_1' ) ){
	function yith_surveys_update_1_1_1(){
		$db_version = get_option( 'yith_surveys_db_version', '1.0.0' );

		if( version_compare( $db_version, '1.1.1', '<' ) ){
			delete_option( 'ywcsur_enable_plugin' );
			update_option( 'yith_surveys_db_version', '1.1.1');
		}
	}
}

add_action( 'admin_init', 'yith_surveys_update_1_1_1' );
