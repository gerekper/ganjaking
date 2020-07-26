<?php
/**
 * Meta Box: Product Data
 *
 * Updates the Product Data meta box.
 *
 * @package WC_Instagram/Admin/Meta Boxes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Meta_Box_Product_Data.
 */
class WC_Instagram_Meta_Box_Product_Data {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panels' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ), 15 );
		add_action( 'wp_ajax_refresh_google_product_category_metabox_field', array( $this, 'refresh_google_product_category_field' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 3.3.0
	 */
	public function enqueue_scripts() {
		$screen_id = wc_instagram_get_current_screen_id();

		if ( 'product' !== $screen_id ) {
			return;
		}

		$suffix = wc_instagram_get_scripts_suffix();

		wp_enqueue_script( 'wc-instagram-admin-meta-boxes-product', WC_INSTAGRAM_URL . "assets/js/admin/meta-boxes-product{$suffix}.js", array( 'jquery', 'select2' ), WC_INSTAGRAM_VERSION, true );
		wp_localize_script(
			'wc-instagram-admin-meta-boxes-product',
			'wc_instagram_admin_meta_boxes_product_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'refresh_google_product_category_metabox_field' ),
			)
		);
	}

	/**
	 * Adds custom tabs to the product_data meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array $tabs Array of existing tabs.
	 * @return array
	 */
	public function product_data_tabs( $tabs ) {
		$instagram_tab = array(
			'label'    => _x( 'Instagram', 'product data tab', 'woocommerce-instagram' ),
			'target'   => 'instagram_data',
			'class'    => array(),
			'priority' => 35,
		);

		$index = array_search( 'linked_product', array_keys( $tabs ), true );

		// Try to locate the 'Instagram' tab in the correct position.
		if ( false === $index ) {
			$tabs['instagram'] = $instagram_tab;
		} else {
			$tabs = array_merge(
				array_slice( $tabs, 0, $index ),
				array(
					'instagram' => $instagram_tab,
				),
				array_slice( $tabs, $index )
			);
		}

		return $tabs;
	}

	/**
	 * Outputs the custom data panels.
	 *
	 * @since 2.0.0
	 */
	public function product_data_panels() {
		$post_id = get_the_ID();

		$category_id = get_post_meta( $post_id, '_instagram_google_product_category', true );

		if ( '' === $category_id ) {
			$category_id = false;
		}

		include 'views/html-product-data-instagram.php';
	}

	/**
	 * Saves the product data.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_product_data( $post_id ) {
		$hashtag = ( ! empty( $_POST['_instagram_hashtag'] ) ? wc_clean( wp_unslash( $_POST['_instagram_hashtag'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		// Remove invalid characters.
		if ( $hashtag ) {
			$hashtag = str_replace( array( ' ', '#', '@', '$', '%' ), '', $hashtag );
		}

		if ( $hashtag ) {
			$delete_images = ( true === update_post_meta( $post_id, '_instagram_hashtag', $hashtag ) );
		} else {
			$delete_images = delete_post_meta( $post_id, '_instagram_hashtag' );

			delete_post_meta( $post_id, '_instagram_hashtag_images_type' );
		}

		// Only check the images type field if there is a product hashtag.
		if ( $hashtag ) {
			$images_type     = ( ! empty( $_POST['_instagram_hashtag_images_type'] ) ? wc_clean( wp_unslash( $_POST['_instagram_hashtag_images_type'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
			$old_images_type = wc_instagram_get_product_hashtag_images_type( $post_id );

			if ( $images_type ) {
				update_post_meta( $post_id, '_instagram_hashtag_images_type', $images_type );

				// Don't delete the images if the type has changed from the default value to the same value used in the global setting.
				$images_type_modified = ( $old_images_type !== $images_type );
			} else {
				delete_post_meta( $post_id, '_instagram_hashtag_images_type' );

				// Don't delete the images if the type has changed from the same value used in the global setting to the default value.
				$images_type_modified = ( wc_instagram_get_setting( 'product_hashtag_images_type', 'recent_top' ) !== $old_images_type );
			}

			$delete_images = ( $delete_images || $images_type_modified );
		}

		// Delete stored images on change the product hashtag or the images type.
		if ( $delete_images ) {
			wc_instagram_delete_product_hashtag_images( $post_id );
		}

		// Save product properties.
		$props = array( 'brand', 'condition', 'images_option', 'google_product_category' );

		foreach ( $props as $prop ) {
			$key   = "_instagram_{$prop}";
			$value = ( ! empty( $_POST[ $key ] ) ? wc_clean( wp_unslash( $_POST[ $key ] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

			if ( $value ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * Handles AJAX request for refresh_google_product_category_field action.
	 *
	 * @since 3.3.0
	 */
	public function refresh_google_product_category_field() {
		check_ajax_referer( 'refresh_google_product_category_metabox_field' );

		$category_id = ! empty( $_POST['category_id'] ) ? wc_clean( wp_unslash( $_POST['category_id'] ) ) : null;
		$html        = WC_Instagram_Admin_Field_Google_Product_Categories::render( $category_id );

		wp_send_json_success( array( 'output' => $html ) );
	}
}

return new WC_Instagram_Meta_Box_Product_Data();
