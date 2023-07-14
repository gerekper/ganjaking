<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PCSVIS_AJAX_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_woocommerce_csv_import_request', array( $this, 'csv_import_request' ) );
		add_action( 'wp_ajax_woocommerce_csv_import_regenerate_thumbnail', array( $this, 'regenerate_thumbnail' ) );
	}

	/**
	 * Ajax event for importing a CSV
	 */
	public function csv_import_request() {
		define( 'WP_LOAD_IMPORTERS', true );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			exit( -1 );
		}

		if ( ( $_REQUEST['import_page'] ?? '' ) === 'woocommerce_variation_csv' )
			WC_PCSVIS_Importer::variation_importer();
		else
			WC_PCSVIS_Importer::product_importer();
	}

	/**
	 * From regenerate thumbnails plugin
	 */
	public function regenerate_thumbnail() {
		@error_reporting( 0 ); // Don't break the JSON result

		header( 'Content-type: application/json' );
		check_ajax_referer( 'csv-regenerate-thumbnail' );

		$id    = (int) $_REQUEST['id'];
		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
			// translators: %s is image ID.
			die( wp_json_encode( array( 'error' => sprintf( esc_html__( 'Failed resize: %s is an invalid image ID.', 'woocommerce-product-csv-import-suite' ), esc_html( $id ) ) ) ) );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) )
			$this->die_json_error_msg( $image->ID, esc_html__( "Your user account doesn't have permission to resize images", 'woocommerce-product-csv-import-suite' ) );

		$fullsizepath = get_attached_file( $image->ID );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) { // nosemgrep: audit.php.lang.security.file.phar-deserialization
			// translators: %s is file path.
			$this->die_json_error_msg( $image->ID, sprintf( esc_html__( 'The originally uploaded image file cannot be found at %s', 'woocommerce-product-csv-import-suite' ), '<code>' . esc_html( $fullsizepath ) . '</code>' ) );
		}

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) )
			$this->die_json_error_msg( $image->ID, $metadata->get_error_message() );
		if ( empty( $metadata ) )
			$this->die_json_error_msg( $image->ID, esc_html__( 'Unknown failure reason.', 'woocommerce-product-csv-import-suite' ) );

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		// translators: $1: quote character, $2: image title, $3: image ID, $4: number of seconds.
		die( wp_json_encode( array( 'success' => sprintf( esc_html__( '%1$s%2$s%1$s (ID %3$s) was successfully resized in %4$s seconds.', 'woocommerce-product-csv-import-suite' ), '&quot;', esc_html( get_the_title( $image->ID ) ), esc_html( $image->ID ), timer_stop() ) ) ) );
	}

	/**
	 * Die with a JSON formatted error message
	 */
	public function die_json_error_msg( $id, $message ) {
		// translators: $1: quote character, $2: image title, $3: image ID, $4: error message.
		die( wp_json_encode( array( 'error' => sprintf( esc_html__( '%1$s%2$s%1$s (ID %3$s) failed to resize. The error message was: %4$s', 'woocommerce-product-csv-import-suite' ), '&quot;', esc_html( get_the_title( $id ) ), esc_html( $id ), esc_html( $message ) ) ) ) );
	}
}

new WC_PCSVIS_AJAX_Handler();
