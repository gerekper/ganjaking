<?php
/**
 * Compatibility Class
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

/**
 * Compatibility Class
 *
 * @class   YITH_WCBM_Compatibility
 * @since   1.2.8
 */
class YITH_WCBM_Compatibility {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCBM_Compatibility
	 */
	protected static $instance;

	/**
	 * Array of compatibilities
	 *
	 * @var array
	 */
	private $compatibilities;

	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCBM_Compatibility
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->compatibilities = array(
			'dynamic-pricing' => 'Dynamic_Pricing',
			'auctions'        => 'Auctions',
			'themes'          => 'Themes',
			'wpml'            => 'WPML',
		);
		$this->load();
	}

	/**
	 * Load classes
	 */
	private function load() {
		foreach ( $this->compatibilities as $slug => $class_slug ) {
			$filename  = YITH_WCBM_COMPATIBILITY_PATH . '/class-yith-wcbm-' . $slug . '-compatibility.php';
			$classname = 'YITH_WCBM_' . $class_slug . '_Compatibility';

			$var = str_replace( '-', '_', $slug );
			if ( file_exists( $filename ) && $this->has_plugin_or_theme( $slug ) ) {
				require_once $filename;
				if ( class_exists( $classname ) && method_exists( $classname, 'get_instance' ) ) {
					$this->$var = $classname::get_instance();
				}
			}
		}
	}

	/**
	 * Check if user has a plugin
	 *
	 * @param string $slug Plugin or theme slug.
	 *
	 * @return bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	public function has_plugin_or_theme( $slug ) {
		$has = false;
		switch ( $slug ) {
			case 'dynamic-pricing':
				$has = defined( 'YITH_YWDPD_PREMIUM' ) && YITH_YWDPD_PREMIUM && defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '1.1.0', '>=' );
				break;
			case 'auctions':
				$has = defined( 'YITH_WCACT_INIT' ) && YITH_WCACT_INIT && defined( 'YITH_WCACT_VERSION' ) && version_compare( YITH_WCACT_VERSION, '1.0.10', '>=' );
				break;
			case 'themes':
				$has = true;
				break;
			case 'wpml':
				global $sitepress;
				$has = ! ! $sitepress;
		}

		return $has;
	}

	/**
	 * Check if user has a theme active
	 *
	 * @param string $name        Theme name.
	 * @param string $min_version Min version.
	 *
	 * @return bool
	 */
	public function has_theme( $name, $min_version = '' ) {
		$current_theme = wp_get_theme();
		if ( $current_theme ) {
			if ( $current_theme->parent() ) {
				$current_theme = $current_theme->parent();
			}
			$theme_name    = $current_theme->get( 'Name' );
			$theme_version = $current_theme->get( 'Version' );

			return $name === $theme_name && version_compare( $theme_version, $min_version, '>=' );
		}

		return false;
	}
}

/**
 * Unique access to instance of YITH_WCBM_Compatibility class
 *
 * @return YITH_WCBM_Compatibility
 * @since 1.2.8
 */
function yith_wcbm_compatibility() {
	return YITH_WCBM_Compatibility::get_instance();
}
