<?php
/**
 * System Status Report.
 *
 * Adds extra information related to Store Credit to the system status report.
 *
 * @package WC_Store_Credit/Admin
 * @since   4.1.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Admin_System_Status.
 */
class WC_Store_Credit_Admin_System_Status {

	/**
	 * Init.
	 *
	 * @since 4.1.1
	 */
	public static function init() {
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'output_content' ) );
	}

	/**
	 * Outputs the Store Credit content in the System Status Report.
	 *
	 * @since 4.1.1
	 */
	public static function output_content() {
		$code_format = get_option( 'wc_store_credit_code_format', '{coupon_code}' );

		$data = array(
			'prices_include_tax' => get_option( 'woocommerce_prices_include_tax', 'no' ),
			'show_my_account'    => get_option( 'wc_store_credit_show_my_account', 'yes' ),
			'delete_after_use'   => get_option( 'wc_store_credit_delete_after_use', 'yes' ),
			'individual_use'     => get_option( 'wc_store_credit_individual_use', 'no' ),
			'inc_tax'            => get_option( 'wc_store_credit_inc_tax', 'no' ),
			'apply_to_shipping'  => get_option( 'wc_store_credit_apply_to_shipping', 'no' ),
			'code_format'        => ( ! empty( $code_format ) ? $code_format : '{coupon_code}' ),
			'overrides'          => self::get_template_overrides(),
		);

		include_once __DIR__ . '/views/html-admin-status-report-settings.php';
	}

	/**
	 * Gets the template overrides.
	 *
	 * @since 4.1.1
	 *
	 * @return array
	 */
	protected static function get_template_overrides() {
		$themes_path      = WP_CONTENT_DIR . '/themes/';
		$template_path    = WC_STORE_CREDIT_PATH . 'templates/';
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
			/** This filter is documented in woocommerce/includes/wc-core-functions.php
			 *
			 * @since 4.1.1
			 */
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
	 * @since 4.1.1
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
}

WC_Store_Credit_Admin_System_Status::init();
