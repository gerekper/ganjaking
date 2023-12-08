<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * qTranslate X
 * https://wordpress.org/plugins/qtranslate-x/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_Qtranslatex {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Qtranslatex|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Qtranslatex
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 5 );
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ], 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			add_filter( 'tm_translate', 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage', 51, 1 ); // @phpstan-ignore-line
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			if ( defined( 'QTRANSLATE_FILE' ) ) {
				global $q_config;
				if ( isset( $q_config['enabled_languages'] ) ) {
					wp_enqueue_script( 'themecomplete-comp-q-translate-x-clogic', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-qtranslatex.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
					$args = [
						'enabled_languages' => $q_config['enabled_languages'],
						'language'          => $q_config['language'],
					];
					wp_localize_script( 'themecomplete-comp-q-translate-x-clogic', 'TMEPOQTRANSLATEXJS', $args );
				}
			}
		}
	}
}
