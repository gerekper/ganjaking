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
			'refresh_google_product_category_metabox_field',
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
	 * Refreshes the 'google_product_category' field in the product catalog settings form.
	 *
	 * @since 3.4.6
	 */
	public static function refresh_google_product_category_field() {
		check_ajax_referer( 'refresh_google_product_category_field' );

		$catalog_id  = ( ! empty( $_POST['catalog_id'] ) ? wc_clean( wp_unslash( $_POST['catalog_id'] ) ) : 'new' );
		$category_id = ( ! empty( $_POST['category_id'] ) ? wc_clean( wp_unslash( $_POST['category_id'] ) ) : '' );

		$product_catalog = new WC_Instagram_Settings_Product_Catalog( $catalog_id );

		$data          = $product_catalog->get_form_field( 'product_google_category' );
		$data['value'] = $category_id;

		$html = $product_catalog->generate_google_product_category_html( 'product_google_category', $data );

		wp_send_json_success( array( 'output' => $html ) );
	}

	/**
	 * Refreshes the 'google_product_category' field in the product data metabox.
	 *
	 * @since 3.4.6
	 *
	 * @global int $thepostid The current post ID.
	 */
	public static function refresh_google_product_category_metabox_field() {
		global $thepostid;

		check_ajax_referer( 'refresh_google_product_category_metabox_field' );

		$thepostid   = ( ! empty( $_POST['post_id'] ) ? wc_clean( wp_unslash( $_POST['post_id'] ) ) : null );
		$category_id = ( ! empty( $_POST['category_id'] ) ? wc_clean( wp_unslash( $_POST['category_id'] ) ) : null );

		$html = WC_Instagram_Admin_Field_Google_Product_Categories::render( $category_id );

		wp_send_json_success( array( 'output' => $html ) );
	}
}

WC_Instagram_AJAX::init();
