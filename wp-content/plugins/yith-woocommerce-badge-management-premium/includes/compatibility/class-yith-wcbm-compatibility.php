<?php
/**
 * Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Compatibility' ) ) {
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
				'membership'      => 'Membership',
				'dynamic-pricing' => 'Dynamic_Pricing',
				'auctions'        => 'Auctions',
				'themes'          => 'Themes',
				'wpml'            => 'WPML',
				'elementor-pro'   => 'Elementor_Pro',
				'multi-vendor'    => 'Multi_Vendor',
			);
			$this->load();
		}

		/**
		 * Load classes
		 */
		private function load() {
			foreach ( $this->compatibilities as $slug => $class_slug ) {
				if ( $this->has_plugin_or_theme( $slug ) ) {
					switch ( $slug ) {
						case 'dynamic-pricing':
							if ( defined( 'YITH_YWDPD_VERSION' ) ) {
								require_once YITH_WCBM_COMPATIBILITY_PATH . '/' . $slug . '/class-yith-wcbm-dynamic-pricing-compatibility-legacy.php';
								if ( version_compare( YITH_YWDPD_VERSION, '3.0.0', '>=' ) ) {
									require_once YITH_WCBM_COMPATIBILITY_PATH . '/' . $slug . '/class-yith-wcbm-dynamic-pricing-compatibility.php';
								}
								yith_wcbm_dynamic_pricing_compatibility();
								continue 2;
							}
							break;
						case 'multi-vendor':
							if ( defined( 'YITH_WPV_VERSION' ) ) {
								require_once YITH_WCBM_COMPATIBILITY_PATH . '/' . $slug . '/class-yith-wcbm-multi-vendor-compatibility-legacy.php';
								if ( version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) ) {
									require_once YITH_WCBM_COMPATIBILITY_PATH . '/' . $slug . '/class-yith-wcbm-multi-vendor-compatibility.php';
								}
								yith_wcbm_multi_vendor_compatibility();
								continue 2;
							}
							break;
					}
				}

				$filename  = '/class-yith-wcbm-' . $slug . '-compatibility.php';
				$classname = 'YITH_WCBM_' . $class_slug . '_Compatibility';

				$var      = str_replace( '-', '_', $slug );
				$filepath = YITH_WCBM_COMPATIBILITY_PATH . $filename;

				if ( ! file_exists( $filepath ) ) {
					$filepath_in_folder = YITH_WCBM_COMPATIBILITY_PATH . '/' . $slug . $filename;
					$filepath           = file_exists( $filepath_in_folder ) ? $filepath_in_folder : false;
				}

				if ( $filepath && $this->has_plugin_or_theme( $slug ) ) {
					require_once $filepath;
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
		 */
		public function has_plugin_or_theme( $slug ) {
			$has = false;
			switch ( $slug ) {
				case 'membership':
					$has = defined( 'YITH_WCMBS' ) && YITH_WCMBS && defined( 'YITH_WCMBS_VERSION' ) && version_compare( YITH_WCMBS_VERSION, '1.4.8', '>=' );
					break;
				case 'dynamic-pricing':
					$has = defined( 'YITH_YWDPD_PREMIUM' ) && YITH_YWDPD_PREMIUM && defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '1.1.0', '>=' );
					break;
				case 'auctions':
					$has = defined( 'YITH_WCACT_INIT' ) && YITH_WCACT_INIT && defined( 'YITH_WCACT_VERSION' ) && version_compare( YITH_WCACT_VERSION, '1.0.10', '>=' );
					break;
				case 'themes':
					$has = true;
					break;
				case 'multi-vendor':
					$has = defined( 'YITH_WPV_VERSION' ) && YITH_WPV_VERSION;
					break;
				case 'wpml':
					global $sitepress;
					$has = ! ! $sitepress;
					break;
				case 'elementor-pro':
					$has = defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '3.5.2', '>=' );
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
