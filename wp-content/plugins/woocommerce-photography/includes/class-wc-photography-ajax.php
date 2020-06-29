<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Ajax.
 *
 * @package  WC_Photography/Ajax
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Ajax {

	/**
	 * Initialize the admin.
	 */
	public function __construct() {
		// Search collections.
		add_action( 'wp_ajax_wc_photography_search_collections', array( $this, 'search_collections' ) );

		// Batch upload.
		add_action( 'wp_ajax_wc_photography_batch_upload', array( $this, 'batch_upload' ) );

		// Delete image.
		add_action( 'wp_ajax_wc_photography_delete_image', array( $this, 'delete_image' ) );

		// Save images.
		add_action( 'wp_ajax_wc_photography_save_images', array( $this, 'save_images' ) );

		// Add Collection.
		add_action( 'wp_ajax_wc_photography_add_collection', array( $this, 'add_collection' ) );

		// Change collection visibility on my account page.
		add_action( 'wp_ajax_wc_photography_my_account_edit_visibility', array( $this, 'my_account_edit_visibility' ) );
		add_action( 'wp_ajax_nopriv_wc_photography_my_account_edit_visibility', array( $this, 'my_account_edit_visibility' ) );

		// Clear cache when updating collection.
		add_action( 'wc_photography_ajax_add_collection', 'wc_photography_clear_collection_cache' );
		add_action( 'wc_photography_ajax_edit_visibility', 'wc_photography_clear_collection_cache' );
	}

	/**
	 * Search collections.
	 *
	 * @return string
	 */
	public function search_collections() {
		ob_start();

		check_ajax_referer( 'wc_photography_search_collections_nonce', 'security' );

		$term = wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		$found_collections = array();

		$args = array(
			'hide_empty' => false,
			'search'     => $term,
		);

		$collections_query = get_terms( 'images_collections', $args );

		if ( $collections_query ) {
			foreach ( $collections_query as $collection ) {
				$found_collections[] = array( 'id' => $collection->term_id, 'text' => html_entity_decode( $collection->name ) );
			}
		}

		wp_send_json( $found_collections );
	}

	/**
	 * Batch upload.
	 *
	 * @return string
	 */
	public function batch_upload() {
		ob_start();

		global $wpdb;

		check_ajax_referer( 'wc_photography_batch_upload_nonce', 'security' );

		$image_id = absint( $_POST['image_id'] );
		$sku      = '';

		$collections = array();

		// Get the collections.
		if ( isset( $_POST['collections'] ) ) {
			if ( is_array( $_POST['collections'] ) ) {
				$collections = array_map( 'absint', $_POST['collections'] );
			} elseif ( '' != $_POST['collections'] ) {
				$_collections = explode( ',', $_POST['collections'] );
				$_collections = array_map( 'absint', $_collections );

				foreach ( $_collections as $collection_id ) {
					$collection = get_term( $collection_id, 'images_collections' );
					$collections[ $collection_id ] = $collection->name;
				}
			}
		}

		$image_metadata = wp_get_attachment_metadata( $image_id );
		if ( ! empty( $image_metadata['image_meta']['title'] ) ) {
			$title = $image_metadata['image_meta']['title'];
		} elseif ( ! empty( $collections ) ) {
			$first_collection = current( $collections );

			/* translators: 1: image id 2: first collection name */
			$title = sprintf( __( 'Photography #%1$d from %2$s', 'woocommerce-photography' ), $image_id, $first_collection );
		} else {
			/* translators: 1: image id */
			$title = sprintf( __( 'Photography #%d', 'woocommerce-photography' ), $image_id );
		}

		$args = array(
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_type'    => 'product',
			'post_author'  => get_current_user_id(),
		);

		if ( ! empty( $image_metadata['image_meta']['caption'] ) ) {
			$args['post_content'] = $image_metadata['image_meta']['caption'];
			$args['post_excerpt'] = $image_metadata['image_meta']['caption'];
		}

		$id = wp_insert_post( $args, true );

		if ( is_wp_error( $id ) ) {
			wp_delete_attachment( $image_id, true );
			wp_send_json_error( $id->get_error_message() );
		}

		$default_term = absint( get_option( 'default_product_cat', 0 ) );

		if ( $default_term ) {
			wp_set_post_terms( $id, array( $default_term ), 'product_cat', true );
		}

		// Save the thumbnail and update the attachment.
		set_post_thumbnail( $id, $image_id );
		$wpdb->update( $wpdb->posts, array( 'post_parent' => $id ), array( 'ID' => $image_id ), array( '%d' ), array( '%d' ) );
		update_post_meta( $image_id, '_is_photography_attachment', true );

		// Set the product type.
		wp_set_object_terms( $id, 'photography', 'product_type' );

		// Sku.
		if ( isset( $_POST['sku_pattern'] ) && '' != $_POST['sku_pattern'] ) {
			$sku_pattern = $wpdb->esc_like( $_POST['sku_pattern'] );
			$last_sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_sku' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1", '%' . $sku_pattern . '%' ) );
			$_sku = absint( str_replace( $sku_pattern, '', $last_sku ) );
			$_sku++;
			$sku = $sku_pattern . $_sku;

			add_post_meta( $id, '_sku', $sku );
		}

		// Price.
		$regular_price = isset( $_POST['price'] ) ? wc_format_decimal( $_POST['price'] ) : 0;
		add_post_meta( $id, '_regular_price', $regular_price );
		add_post_meta( $id, '_sale_price', '' );
		add_post_meta( $id, '_sale_price_dates_from', '' );
		add_post_meta( $id, '_sale_price_dates_to', '' );
		add_post_meta( $id, '_price', $regular_price );

		// Set the images_collections taxonomy.
		if ( ! empty( $collections ) ) {
			$_collections = array_keys( $collections );

			wp_set_object_terms( $id, $_collections, 'images_collections' );
		}

		// Default options.
		add_post_meta( $id, '_visibility', 'visible' );
		add_post_meta( $id, '_stock_status', 'instock' );
		add_post_meta( $id, 'total_sales', 0 );
		add_post_meta( $id, '_downloadable', 'no' );
		add_post_meta( $id, '_virtual', 'no' );
		add_post_meta( $id, '_featured', 'no' );
		add_post_meta( $id, '_manage_stock', 'no' );
		add_post_meta( $id, '_backorders', 'no' );
		add_post_meta( $id, '_stock', '' );

		do_action( 'wc_photography_batch_upload', $id, $image_id, $sku, $regular_price, $collections );

		$response = apply_filters( 'wc_photography_batch_upload_response', array(
			'id'              => $id,
			'image_id'        => $image_id,
			'thumbnail'       => wp_get_attachment_thumb_url( $image_id ),
			'collections_ids' => implode( ',', array_keys( $collections ) ),
			'collections'     => $collections,
			'price'           => $regular_price,
			'sku'             => $sku,
		) );

		wp_send_json( $response );
	}

	/**
	 * Delete image.
	 *
	 * @return string
	 */
	public function delete_image() {
		check_ajax_referer( 'wc_photography_delete_image_nonce', 'security' );

		$id       = absint( $_POST['id'] );
		$image_id = get_post_thumbnail_id( $id );

		wp_delete_post( $id, true );
		wp_delete_attachment( $image_id, true );
	}

	/**
	 * Save images.
	 *
	 * @return string
	 */
	public function save_images() {
		check_ajax_referer( 'wc_photography_save_images_nonce', 'security' );

		parse_str( $_POST['images'], $data );
		$images = $data['photography'];

		foreach ( $images as $image_id => $image ) {
			$image_id = absint( $image_id );

			if ( isset( $image['sku'] ) ) {
				update_post_meta( $image_id, '_sku', wc_clean( $image['sku'] ) );
			}

			if ( isset( $image['price'] ) ) {
				$regular_price = ! empty( $image['price'] ) ? wc_format_decimal( $image['price'] ) : 0;

				update_post_meta( $image_id, '_regular_price', $regular_price );
				update_post_meta( $image_id, '_price', $regular_price );
			}

			if ( isset( $image['caption'] ) ) {
				$caption = wp_strip_all_tags( $image['caption'] );

				wp_update_post( array( 'ID' => $image_id, 'post_content' => $caption, 'post_excerpt' => $caption ) );
			}

			$collections = array();

			if ( isset( $image['collections'] ) ) {
				if ( is_array( $image['collections'] ) ) {
					$collections = array_map( 'absint', $image['collections'] );
				} elseif ( '' != $image['collections'] ) {
					$collections = explode( ',', $image['collections'] );
					$collections = array_map( 'absint', $collections );
				}
			}

			wp_set_object_terms( $image_id, $collections, 'images_collections' );

			/**
			 * Allows save custom fields.
			 *
			 * @param int   $image_id Image ID.
			 * @param array $image    Image fields.
			 */
			do_action( 'wc_photography_save_image_ajax', $image_id, $image );
		} // End foreach().
	}

	/**
	 * Add collection.
	 *
	 * @return string
	 */
	public function add_collection() {
		ob_start();

		check_ajax_referer( 'wc_photography_add_collection_nonce', 'security' );

		if ( isset( $_POST['name'] ) && '' != $_POST['name'] ) {
			$term = wp_insert_term( $_POST['name'], 'images_collections' );

			if ( ! is_wp_error( $term ) ) {
				$collection = get_term( $term['term_id'], 'images_collections' );
				$settings   = get_option( 'woocommerce_photography', array() );
				$visibility = isset( $settings['collections_default_visibility'] ) ? sanitize_text_field( $settings['collections_default_visibility'] ) : 'restricted';

				WC_Photography_WC_Compat::update_term_meta( $collection->term_id, 'visibility', $visibility );
				do_action( 'wc_photography_ajax_add_collection', $collection->term_id, $settings, $visibility );

				wp_send_json_success( array( 'id' => $collection->term_id, 'text' => html_entity_decode( $collection->name ) ) );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Edit the colleciton visibility.
	 *
	 * @return string
	 */
	public function my_account_edit_visibility() {
		ob_start();

		check_ajax_referer( 'wc_photography_my_account_edit_visibility_nonce', 'security' );

		if ( isset( $_POST['collection_id'] ) && isset( $_POST['customer'] ) && isset( $_POST['visibility'] ) ) {
			$collection_id = absint( $_POST['collection_id'] );
			$visibility    = wc_clean( $_POST['visibility'] );

			// Test with the current user have access for the collection.
			$user = get_user_by( 'login', wc_clean( $_POST['customer'] ) );

			$user_collections = get_user_meta( $user->ID, '_wc_photography_collections', true );
			$user_collections = is_array( $user_collections ) ? $user_collections : array();
			if ( ! in_array( $collection_id, $user_collections ) ) {
				wp_send_json_error( array( 'id' => 'user_has_no_permissions' ) );
			}

			// Update the collection.
			WC_Photography_WC_Compat::update_term_meta( $collection_id, 'visibility', $visibility );
			do_action( 'wc_photography_ajax_edit_visibility', $collection_id, $visibility, $user );

			wp_send_json_success();
		}

		wp_send_json_error( array( 'id' => 'invalid_params' ) );
	}
}

new WC_Photography_Ajax();
