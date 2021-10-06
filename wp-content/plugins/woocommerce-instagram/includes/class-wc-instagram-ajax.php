<?php
/**
 * AJAX Event Handlers.
 *
 * @package WC_Instagram/Classes
 * @since   3.4.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_AJAX class.
 */
class WC_Instagram_AJAX {

	/**
	 * Init.
	 *
	 * @since 3.4.6
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in AJAX events.
	 *
	 * @since 3.4.6
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'generate_product_catalog_slug',
			'refresh_google_product_category_field',
			'refresh_google_product_category_metabox_field', // Deprecated.
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_wc_instagram_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Generates the product catalog slug.
	 *
	 * @since 3.4.6
	 */
	public static function generate_product_catalog_slug() {
		// phpcs:disable WordPress.Security.NonceVerification
		$catalog_title = ( ! empty( $_POST['catalog_title'] ) ? wc_clean( wp_unslash( $_POST['catalog_title'] ) ) : '' );

		if ( ! $catalog_title ) {
			wp_send_json_error( array( 'error_code' => 'invalid_arguments' ) );
		}

		$catalog_id = ( isset( $_POST['catalog_id'] ) ? wc_clean( wp_unslash( $_POST['catalog_id'] ) ) : 'new' );
		// phpcs:enable WordPress.Security.NonceVerification

		$exclude = ( 'new' !== $catalog_id ? array( intval( $catalog_id ) ) : array() );

		$slug = wc_instagram_generate_product_catalog_slug( $catalog_title, $exclude );

		wp_send_json_success( array( 'slug' => $slug ) );
	}

	/**
	 * Refreshes the 'google_product_category' field.
	 *
	 * The new POST parameter `selects` supports the following values:
	 * - all:   Fetches all the select fields.
	 * - child: Fetches only a select field with the subcategories.
	 *
	 * @since 3.4.6
	 * @since 3.6.0 Allow fetching only a select field with the subcategories of the specified category.
	 *                  Deprecated POST parameter 'catalog_id'.
	 */
	public static function refresh_google_product_category_field() {
		check_ajax_referer( 'refresh_google_product_category_field' );

		$category_id = ( ! empty( $_POST['category_id'] ) ? wc_clean( wp_unslash( $_POST['category_id'] ) ) : null );
		$selects     = ( ! empty( $_POST['selects'] ) ? wc_clean( wp_unslash( $_POST['selects'] ) ) : 'all' );

		if ( 'child' === $selects ) {
			$html = WC_Instagram_Admin_Field_Google_Product_Category::get_child_selector( $category_id );
		} else {
			$html = WC_Instagram_Admin_Field_Google_Product_Category::get_selectors( $category_id );
		}

		wp_send_json_success( array( 'output' => $html ) );
	}

	/**
	 * Refreshes the 'google_product_category' field in the product data metabox.
	 *
	 * @since 3.4.6
	 * @deprecated 3.6.0
	 */
	public static function refresh_google_product_category_metabox_field() {
		wc_deprecated_function( __FUNCTION__, '3.6.0', 'WC_Instagram_AJAX::refresh_google_product_category_field()' );

		self::refresh_google_product_category_field();
	}
}

WC_Instagram_AJAX::init();
