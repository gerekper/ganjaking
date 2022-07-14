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
			'delete_product_catalog',
			'fetch_product_catalog_file',
			'generate_product_catalog_file',
			'cancel_product_catalog_file',
			'generate_product_catalog_slug',
			'refresh_google_product_category_field',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_wc_instagram_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Deletes a product catalog.
	 *
	 * @since 4.0.0
	 */
	public static function delete_product_catalog() {
		check_ajax_referer( 'wc_instagram_delete_product_catalog' );

		$catalog_id = ( isset( $_POST['catalog_id'] ) ? intval( wp_unslash( $_POST['catalog_id'] ) ) : '' );

		$result = wc_instagram_delete_product_catalog( $catalog_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'error' => esc_html( $result->get_error_message() ) ) );
		}

		wp_send_json_success();
	}

	/**
	 * Fetches the information of a product catalog file.
	 *
	 * @since 4.0.0
	 */
	public static function fetch_product_catalog_file() {
		list( $catalog, $format ) = self::process_catalog_file_action();

		wp_send_json_success(
			array(
				'file' => self::get_file_data( $catalog, $format ),
			)
		);
	}

	/**
	 * Generates the product catalog file.
	 *
	 * @since 4.0.0
	 */
	public static function generate_product_catalog_file() {
		list( $catalog, $format ) = self::process_catalog_file_action();

		if ( ! $catalog->get_file_status( $format ) ) {
			WC_Instagram_Product_Catalogs::generate_catalog_file( $catalog, $format );
		}

		wp_send_json_success(
			array(
				'file' => self::get_file_data( $catalog, $format ),
			)
		);
	}

	/**
	 * Cancels the generation of the product catalog file.
	 *
	 * @since 4.2.0
	 */
	public static function cancel_product_catalog_file() {
		list( $catalog, $format ) = self::process_catalog_file_action();

		if ( in_array( $catalog->get_file_status( $format ), array( 'queued', 'processing' ), true ) ) {
			$catalog->set_file_status( $format, 'canceling' );
		}

		wp_send_json_success(
			array(
				'file' => self::get_file_data( $catalog, $format ),
			)
		);
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
	 * Processes the AJAX request of a product catalog file action.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	protected static function process_catalog_file_action() {
		check_ajax_referer( 'wc_instagram_product_catalog_file_action' );

		$catalog_id = ( isset( $_POST['catalog_id'] ) ? intval( wp_unslash( $_POST['catalog_id'] ) ) : '' );
		$format     = ( isset( $_POST['format'] ) ? wc_clean( wp_unslash( $_POST['format'] ) ) : '' );

		if ( ! $catalog_id || ! $format ) {
			wp_send_json_error( new WP_Error( 'invalid_arguments', __( 'Invalid arguments.', 'woocommerce-instagram' ) ) );
		}

		$catalog = wc_instagram_get_product_catalog( $catalog_id );

		if ( ! $catalog ) {
			wp_send_json_error( new WP_Error( 'not_found', __( 'Product catalog not found.', 'woocommerce-instagram' ) ) );
		}

		return array( $catalog, $format );
	}

	/**
	 * Gets the file data for the specified catalog and format.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param string                       $format          The file format.
	 * @return array
	 */
	protected static function get_file_data( $product_catalog, $format ) {
		$catalog_file = $product_catalog->get_file( $format );

		if ( ! $catalog_file ) {
			return array(
				'status' => '',
			);
		}

		$last_checked = wc_instagram_timestamp_to_datetime( time() );

		$data = array(
			'status'      => $catalog_file->get_status(),
			'lastChecked' => self::get_datetime_data( $last_checked ),
		);

		$last_modified = $catalog_file->get_last_modified();

		if ( $last_modified ) {
			$data['lastModified'] = self::get_datetime_data( $last_modified );
		}

		return $data;
	}

	/**
	 * Gets the datetime data.
	 *
	 * @since 4.2.0
	 *
	 * @param WC_DateTime $datetime Datetime object.
	 * @return array
	 */
	protected static function get_datetime_data( $datetime ) {
		return array(
			'datetime' => $datetime->date( 'c' ),
			'i18n'     => wc_instagram_format_datetime( $datetime ),
		);
	}
}

WC_Instagram_AJAX::init();
