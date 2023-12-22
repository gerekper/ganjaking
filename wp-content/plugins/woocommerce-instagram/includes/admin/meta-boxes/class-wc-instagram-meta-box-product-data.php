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
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panels' ) );
		add_action( 'woocommerce_after_product_attribute_settings', array( $this, 'product_attribute_settings' ), 10, 2 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ), 15 );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_product' ) );
		add_action( 'wp_ajax_woocommerce_save_attributes', array( $this, 'save_attributes' ), 5 );
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
		include 'views/html-product-data-instagram.php';
	}

	/**
	 * Adds custom settings to the attribute in the product data meta box.
	 *
	 * @since 3.7.0
	 *
	 * @param WC_Product_Attribute $attribute Attribute object.
	 * @param int                  $index     Attribute index.
	 */
	public function product_attribute_settings( $attribute, $index ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$google_pa = '';

		if ( $attribute->get_id() ) {
			$google_pa = WC_Instagram_Attributes::get_meta( $attribute->get_id(), 'google_pa' );
		} else {
			$product_id = ( isset( $_REQUEST['post'] ) ? absint( wp_unslash( $_REQUEST['post'] ) ) : ( isset( $_REQUEST['post_id'] ) ? absint( wp_unslash( $_REQUEST['post_id'] ) ) : 0 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $product_id > 0 ) {
				$attribute_slug            = sanitize_title( $attribute->get_name() );
				$google_product_attributes = get_post_meta( $product_id, '_google_product_attributes', true );
				$google_pa                 = ( isset( $google_product_attributes[ $attribute_slug ] ) ? $google_product_attributes[ $attribute_slug ] : '' );
			}
		}

		// A product attribute without a Google product attribute associated.
		if ( $attribute->get_id() && ! $google_pa ) {
			return;
		}

		include 'views/html-product-attribute-settings.php';
	}

	/**
	 * Saves the product data.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_product_data( $post_id ) {
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

		if ( ! wc_instagram_has_business_account() ) {
			return;
		}

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
	}

	/**
	 * Updates the product before saving it.
	 *
	 * @since 4.4.0
	 *
	 * @param WC_Product $product Product object.
	 */
	public function save_product( $product ) {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! isset( $_POST['attribute_google_pa'], $_POST['attribute_names'] ) ) {
			return;
		}

		$attribute_names     = wc_clean( wp_unslash( $_POST['attribute_names'] ) );
		$attribute_google_pa = wc_clean( wp_unslash( $_POST['attribute_google_pa'] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		$attributes = $this->prepare_google_product_attributes( $attribute_google_pa, $attribute_names );

		$product->update_meta_data( '_google_product_attributes', $attributes );
	}

	/**
	 * Saves product attributes via ajax.
	 *
	 * @since 4.4.0
	 */
	public function save_attributes() {
		check_ajax_referer( 'save-attributes', 'security' );

		if ( ! isset( $_POST['data'], $_POST['post_id'] ) ) {
			return;
		}

		parse_str( wp_unslash( $_POST['data'] ), $data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! isset( $data['attribute_google_pa'], $data['attribute_names'] ) ) {
			return;
		}

		$attribute_names     = wc_clean( $data['attribute_names'] );
		$attribute_google_pa = wc_clean( $data['attribute_google_pa'] );

		$attributes = $this->prepare_google_product_attributes( $attribute_google_pa, $attribute_names );

		update_post_meta( absint( $_POST['post_id'] ), '_google_product_attributes', $attributes );
	}

	/**
	 * Prepares the Google product attributes.
	 *
	 * @since 4.4.0
	 *
	 * @param array $attribute_google_pa An array with the posted Google attributes.
	 * @param array $attribute_names     An array with the posted attribute names.
	 * @return array An array with pairs [attribute_name => google_pa].
	 */
	protected function prepare_google_product_attributes( array $attribute_google_pa, array $attribute_names ) {
		$attributes = array();

		foreach ( $attribute_google_pa as $index => $value ) {
			$attribute_slug = ( isset( $attribute_names[ $index ] ) ? sanitize_title( $attribute_names[ $index ] ) : '' );

			if ( $attribute_slug ) {
				$attributes[ $attribute_slug ] = $value;
			}
		}

		return $attributes;
	}
}

return new WC_Instagram_Meta_Box_Product_Data();
