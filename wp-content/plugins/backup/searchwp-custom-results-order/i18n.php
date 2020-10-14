<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Internationalization handler
 *
 * @since 1.0
 */
class SearchWP_CRO_I18n {

	/**
	 * Strings used throughout
	 *
	 * @var array $strings
	 */
	public static $strings;

	/**
	 * Defines strings used in the UI that require i18n
	 *
	 * @since 1.0
	 */
	private function __construct() {}

	/**
	 * Initializer
	 *
	 * @since 1.0
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( get_called_class(), 'textdomain' ) );

		self::$strings = array(
			'activate' => __( 'Activate', 'searchwpcro' ),
		);
	}

	/**
	 * Establishes the textdomain
	 *
	 * @since 1.0
	 */
	public static function textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'searchwpcro' );
		$mofile = WP_LANG_DIR . '/searchwp-custom-results-order/searchwp-custom-results-order-' . $locale . '.mo';

		if ( file_exists( $mofile ) ) {
			load_textdomain( 'searchwpcro', $mofile );
		} else {
			load_plugin_textdomain( 'searchwpcro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}
}
