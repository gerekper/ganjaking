<?php
/**
 * WooCommerce Bookings status additions to WooCommerce status page.
 *
 * @package WooCommerce/Bookings
 */

/**
 * Logic for displaying on WooCommerce Status page.
 */
class WC_Bookings_WC_Status_Dashboard {

	/**
	 * Constructor.
	 */
	public function __construct() {
			// Adds WC Bookings template overrides section to WC Status.
			add_action( 'woocommerce_system_status_report', array( $this, 'add_template_overrides_panel' ), 10 );
	}

	/**
	 * Display WC Bookings template overrides in theme.
	 */
	public function add_template_overrides_panel() {
		/**
		 * Scan the theme directory for all WC Bookings templates to see if our theme
		 * overrides any of them.
		 */
		$override_files     = array();
		$outdated_templates = false;
		$scan_files         = WC_Admin_Status::scan_template_files( WC_BOOKINGS_TEMPLATE_PATH );
		foreach ( $scan_files as $file ) {
			$located = apply_filters( 'wc_booking_get_template', $file, $file, array(), 'woocommerce-bookings/', WC_BOOKINGS_TEMPLATE_PATH );
			if ( file_exists( $located ) ) {
				$theme_file = $located;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/woocommerce-bookings/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/woocommerce-bookings/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/woocommerce-bookings/' . $file ) ) {
				$theme_file = get_template_directory() . '/woocommerce-bookings/' . $file;
			} else {
				$theme_file = false;
			}

			if ( ! empty( $theme_file ) ) {
				$core_file = $file;
				$core_version  = WC_Admin_Status::get_file_version( WC_BOOKINGS_TEMPLATE_PATH . $core_file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );
				if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
					if ( ! $outdated_templates ) {
						$outdated_templates = true;
					}
				}
				$override_files[] = array(
					'file'         => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
					'version'      => $theme_version,
					'core_version' => $core_version,
				);
			}
		}
		if ( ! empty( $override_files ) ) {
			include_once 'views/html-wc-status-templates-pannel.php';
		}
	}
}
