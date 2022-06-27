<?php
/**
 * Theme Compatibilty
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 * @version  2.0.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Theme_Compatibility Class.
 *
 * Load classes for making Mix and Match compatible with certain themes.
 */
class WC_MNM_Theme_Compatibility {

	/**
	 * Delay Init compatibility classes.
	 */
	public static function init() {

		add_action( 'after_setup_theme', array( __CLASS__, 'add_theme_compat' ) );

	}

	/**
	 * Init compatibility classes.
	 */
	public static function add_theme_compat() {

		if ( is_customize_preview() ) {
			global $wp_customize;
			$current_theme = $wp_customize->theme();
		} else {
			$current_theme = wp_get_theme();
		}

		if ( $current_theme instanceof WP_Theme ) {
			$template = strtolower( $current_theme->template );
			switch( $template ) {
				case 'astra':
					require_once 'theme-modules/class-wc-mnm-astra-compatibility.php';
					break;
				case 'avada':
					require_once 'theme-modules/class-wc-mnm-avada-compatibility.php';
					break;
				case 'flatsome':
					require_once 'theme-modules/class-wc-mnm-flatsome-compatibility.php';
					break;
				case 'oceanwp':
					require_once 'theme-modules/class-wc-mnm-oceanwp-compatibility.php';
					break;
				case 'storefront':
					require_once 'theme-modules/class-wc-mnm-storefront-compatibility.php';
					break;
				case 'woodmart':
						require_once 'theme-modules/class-wc-mnm-woodmart-compatibility.php';
						break;
				case 'x':
					require_once 'theme-modules/class-wc-mnm-x-compatibility.php';
					break;

			}		
		}

	}

}
WC_MNM_Theme_Compatibility::init();
