<?php
/**
 * System Status Report.
 *
 * Adds extra information related to Order Delivery to the system status report.
 *
 * @package WC_OD/Admin
 * @since   1.9.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Admin_System_Status.
 */
class WC_OD_Admin_System_Status {

	/**
	 * Init.
	 *
	 * @since 1.9.4
	 */
	public static function init() {
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'output_content' ) );
	}

	/**
	 * Outputs the Order Delivery content in the system status report.
	 *
	 * @since 1.9.4
	 */
	public static function output_content() {
		$days = array(
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
		);

		$settings = array(
			'min_working_days'         => WC_OD()->settings()->get_setting( 'min_working_days' ),
			'shipping_days'            => WC_OD()->settings()->get_setting( 'shipping_days' ),
			'delivery_ranges'          => WC_OD_Delivery_Ranges::get_ranges(),
			'checkout_delivery_option' => WC_OD()->settings()->get_setting( 'checkout_delivery_option' ),
			'delivery_days'            => wc_od_get_delivery_days(),
			'delivery_fields_option'   => WC_OD()->settings()->get_setting( 'delivery_fields_option' ),
			'max_delivery_days'        => WC_OD()->settings()->get_setting( 'max_delivery_days' ),
			'enable_local_pickup'      => WC_OD()->settings()->get_setting( 'enable_local_pickup' ),
			'limit_order_renewals'     => WC_OD()->settings()->get_setting( 'subscriptions_limit_to_billing_interval' ),
		);

		$choices           = wc_od_get_checkout_location_choices();
		$checkout_location = WC_OD()->settings()->get_setting( 'checkout_location' );

		$settings['checkout_location'] = ( isset( $choices[ $checkout_location ] ) ? $choices[ $checkout_location ] : 'undefined' );
		$settings['delivery_ranges'][] = WC_OD_Delivery_Ranges::get_range( 0 );

		$data = array(
			'days'      => $days,
			'settings'  => $settings,
			'overrides' => self::get_template_overrides(),
		);

		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-settings.php';
		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-shipping-days.php';
		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-delivery-ranges.php';
		include_once dirname( __FILE__ ) . '/views/html-admin-status-report-delivery-days.php';
	}

	/**
	 * Gets the template overrides.
	 *
	 * @since 1.9.4
	 *
	 * @return array
	 */
	protected static function get_template_overrides() {
		$themes_path      = WP_CONTENT_DIR . '/themes/';
		$template_path    = WC_OD_PATH . 'templates/';
		$wc_template_path = WC()->template_path();
		$stylesheet_dir   = get_stylesheet_directory();
		$template_dir     = get_template_directory();
		$locations        = array_unique(
			array(
				$stylesheet_dir . '/',
				$stylesheet_dir . '/' . $wc_template_path,
				$template_dir . '/',
				$template_dir . '/' . $wc_template_path,
			)
		);

		$overrides  = array();
		$scan_files = WC_Admin_Status::scan_template_files( $template_path );

		foreach ( $scan_files as $file ) {
			/** This filter is documented in woocommerce/includes/wc-core-functions.php */
			$located = apply_filters( 'wc_get_template', $file, $file, array(), $wc_template_path, $template_path );

			$theme_file = false;

			if ( file_exists( $located ) ) {
				$theme_file = $located;
			} else {
				foreach ( $locations as $location ) {
					$filename = $location . $file;

					if ( file_exists( $filename ) ) {
						$theme_file = $filename;
						break;
					}
				}
			}

			if ( ! empty( $theme_file ) ) {
				$overrides[] = array(
					'file'         => str_replace( $themes_path, '', $theme_file ),
					'version'      => self::get_file_version( $theme_file ),
					'core_version' => self::get_file_version( $template_path . $file ),
				);
			}
		}

		return $overrides;
	}

	/**
	 * Retrieve metadata from a file. Based on WP Core's get_file_data function.
	 *
	 * @since 1.9.4
	 *
	 * @param string $file File path.
	 * @return string
	 */
	protected static function get_file_version( $file ) {
		if ( ! file_exists( $file ) ) {
			return '';
		}

		$fp = fopen( $file, 'r' ); // @codingStandardsIgnoreLine.

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 ); // @codingStandardsIgnoreLine.

		fclose( $fp ); // @codingStandardsIgnoreLine.

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );

		$version = '';
		$tags    = array( '@version', '@since' );

		foreach ( $tags as $tag ) {
			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $tag, '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
				$version = _cleanup_header_comment( $match[1] );
				break;
			}
		}

		return $version;
	}

	/**
	 * Outputs the HTML content for the boolean value.
	 *
	 * @since 1.9.4
	 *
	 * @param bool $value The bool to format.
	 */
	public static function output_bool_html( $value ) {
		printf(
			'<mark class="%1$s"><span class="dashicons dashicons-%2$s"></span></mark>',
			esc_attr( $value ? 'yes' : 'error' ),
			esc_attr( $value ? 'yes' : 'no-alt' )
		);
	}
}

WC_OD_Admin_System_Status::init();
