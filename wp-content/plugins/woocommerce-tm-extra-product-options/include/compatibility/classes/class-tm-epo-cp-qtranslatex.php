<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * qTranslate X 
 * https://wordpress.org/plugins/qtranslate-x/
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_qtranslatex {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ), 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_filter( 'tm_translate', array( $this, 'tm_translate' ), 50, 1 );
		if ( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			add_filter( 'tm_translate', 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage', 51, 1 );
		}
	}

	public function tm_translate( $text = "" ) {
		return $text;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts( $text = "" ) {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			if ( defined( 'QTRANSLATE_FILE' ) ) {
				global $q_config;
				if ( isset( $q_config['enabled_languages'] ) ) {
					wp_enqueue_script( 'themecomplete-comp-q-translate-x-clogic', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-qtranslatex.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
					$args = array(
						'enabled_languages' => $q_config['enabled_languages'],
						'language'          => $q_config['language'],
					);
					wp_localize_script( 'themecomplete-comp-q-translate-x-clogic', 'TMEPOQTRANSLATEXJS', $args );
				}
			}
		}
	}
}


