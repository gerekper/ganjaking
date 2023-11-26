<?php
/**
 * Product functions
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the product instance.
 *
 * @since 3.0.0
 *
 * @param mixed $the_product Product object or ID.
 * @return WC_Product|false The product object. False on failure.
 */
function wc_instagram_get_product( $the_product ) {
	return ( $the_product instanceof WC_Product ? $the_product : wc_get_product( $the_product ) );
}

/**
 * Gets the product label to use it in a select field.
 *
 * @since 3.0.0
 *
 * @param mixed $the_product Post object or post ID of the product.
 * @param bool  $identifier  Optional. Include the product identifier or not.
 * @return string
 */
function wc_instagram_get_product_choice_label( $the_product, $identifier = false ) {
	$product = wc_instagram_get_product( $the_product );

	if ( ! $product ) {
		return '';
	}

	if ( $identifier ) {
		$title = $product->get_formatted_name();
	} else {
		$title = $product->get_title();

		if ( $product instanceof WC_Product_Variation ) {
			$formatted_attributes = wc_get_formatted_variation( $product, true );

			$title = "{$title} &ndash; {$formatted_attributes}";
		}
	}

	return $title;
}

/**
 * Gets the hashtag for the specified product.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 * @return string|false The product hashtag. False otherwise.
 */
function wc_instagram_get_product_hashtag( $product_id ) {
	return get_post_meta( $product_id, '_instagram_hashtag', true );
}

/**
 * Gets the type of images to display for the specified product.
 *
 * @since 2.2.0
 *
 * @param int $product_id The product ID.
 * @return string
 */
function wc_instagram_get_product_hashtag_images_type( $product_id ) {
	$type = get_post_meta( $product_id, '_instagram_hashtag_images_type', true );

	// Use the global setting.
	if ( ! $type ) {
		$type = wc_instagram_get_setting( 'product_hashtag_images_type', 'recent_top' );
	}

	return $type;
}

/**
 * Gets the 'hashtag images' meta for the specified product.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 * @return array
 */
function wc_instagram_get_product_hashtag_images_meta( $product_id ) {
	$images = get_post_meta( $product_id, '_instagram_hashtag_images', true );

	if ( ! is_array( $images ) ) {
		$images = array();
	}

	return $images;
}

/**
 * Gets the transient name for the specified product and action.
 *
 * @since 2.0.0
 *
 * @param int    $product_id The product ID.
 * @param string $action     The related action.
 * @return string
 */
function wc_instagram_get_product_transient_name( $product_id, $action ) {
	$transient = "wc_instagram_product_{$action}_{$product_id}";

	/**
	 * Filters the transient name for the specified product and action.
	 *
	 * @since 2.0.0
	 *
	 * @param string $transient  The transient name.
	 * @param int    $product_id The product ID.
	 * @param string $action     The related action.
	 */
	return apply_filters( 'wc_instagram_get_product_transient_name', $transient, $product_id, $action );
}

/**
 * Sets the transient for the product hashtag images.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 */
function wc_instagram_set_product_hashtag_images_transient( $product_id ) {
	$transient = wc_instagram_get_product_transient_name( $product_id, 'hashtag_images' );

	$data = array(
		'hashtag' => wc_instagram_get_product_hashtag( $product_id ),
		'count'   => wc_instagram_get_images_number( 'product_hashtag' ),
	);

	/**
	 * Filters the data of the 'product hashtag images' transient.
	 *
	 * @since 2.0.0
	 *
	 * @param array $data The transient data.
	 * @param int $product_id The product ID.
	 */
	$data = apply_filters( 'wc_instagram_product_hashtag_images_transient_data', $data, $product_id );

	set_transient( $transient, $data, wc_instagram_get_transient_expiration_time( 'product_hashtag_images' ) );
}

/**
 * Gets if the product hashtag images are valid or not.
 *
 * True if the current images are valid. False if it's necessary to request new ones.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 * @return bool
 */
function wc_instagram_validate_product_hashtag_images( $product_id ) {
	$valid     = false;
	$transient = get_transient( wc_instagram_get_product_transient_name( $product_id, 'hashtag_images' ) );

	if (
		is_array( $transient ) && isset( $transient['hashtag'] ) && isset( $transient['count'] ) && // Transient not expired.
		wc_instagram_get_product_hashtag( $product_id ) === $transient['hashtag'] && // The hashtag matches.
		$transient['count'] >= wc_instagram_get_images_number( 'product_hashtag' ) // The count used is higher or equal than the current.
	) {
		$valid = true;
	}

	/**
	 * Filters if the product hashtag images are valid or not.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $valid      True if the images are valid. False otherwise.
	 * @param int  $product_id The product ID.
	 */
	return apply_filters( 'wc_instagram_validate_product_hashtag_images', $valid, $product_id );
}

/**
 * Gets the hashtag images for the specified product.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 * @return array
 */
function wc_instagram_get_product_hashtag_images( $product_id ) {
	// It's necessary to update the images.
	if ( ! wc_instagram_validate_product_hashtag_images( $product_id ) ) {
		wc_instagram_update_product_hashtag_images( $product_id );

		wc_instagram_set_product_hashtag_images_transient( $product_id );
	}

	$images = wc_instagram_get_product_hashtag_images_meta( $product_id );

	if ( ! empty( $images ) ) {
		$count = wc_instagram_get_images_number( 'product_hashtag' );

		// Only keep the $count most recent images.
		$min    = min( $count, count( $images ) );
		$images = array_slice( $images, 0, $min );
	}

	/**
	 * Filters the product hashtag images.
	 *
	 * @since 2.0.0
	 *
	 * @param array $images     The product hashtag images.
	 * @param int   $product_id The product ID.
	 */
	return apply_filters( 'wc_instagram_get_product_hashtag_images', $images, $product_id );
}

/**
 * Updates the hashtag images for the specified product.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 */
function wc_instagram_update_product_hashtag_images( $product_id ) {
	$hashtag = wc_instagram_get_product_hashtag( $product_id );

	if ( ! $hashtag ) {
		return;
	}

	$type  = wc_instagram_get_product_hashtag_images_type( $product_id );
	$count = wc_instagram_get_images_number( 'product_hashtag' );

	$images = wc_instagram_get_hashtag_media(
		$hashtag,
		array(
			'edge'  => ( 'top' === $type ? 'top' : 'recent' ),
			'type'  => 'image',
			'count' => $count,
		)
	);

	if ( ! is_array( $images ) ) {
		$images = array();
	}

	/*
	 * The 'recent-media' edge only returns media objects published within 24 hours of query execution.
	 * We don't want to run out of images. So, if there are not enough images, we merge the new images with the older ones.
	 */
	if ( 'top' !== $type && count( $images ) < $count ) {
		$previous_images = wc_instagram_get_product_hashtag_images_meta( $product_id );

		// Merge the new images with the older ones (New first).
		$images = array_merge( $images, $previous_images );

		// Remove duplicated images.
		$image_ids = array_unique( wp_list_pluck( $images, 'id' ) );
		$images    = array_intersect_key( $images, $image_ids );

		// Only keep the '$count' most recent images.
		$min    = min( $count, count( $images ) );
		$images = array_slice( $images, 0, $min );
	}

	/*
	 * If after fetching the most recent images and merge them with the older ones there are not enough images,
	 * we complete the list with the top images.
	 */
	if ( 'recent_top' === $type && count( $images ) < $count ) {
		$top_images = wc_instagram_get_hashtag_media(
			$hashtag,
			array(
				'edge'    => 'top',
				'type'    => 'image',
				'count'   => ( $count - count( $images ) ),
				'exclude' => wp_list_pluck( $images, 'id' ),
			)
		);

		if ( ! empty( $top_images ) ) {
			$images = array_merge( $images, $top_images );
		}
	}

	// Store the images.
	update_post_meta( $product_id, '_instagram_hashtag_images', $images );
}

/**
 * Deletes the hashtag images for all products.
 *
 * @since 4.3.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_instagram_delete_all_products_hashtag_images() {
	global $wpdb;

	// Delete product hashtag images.
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_instagram_hashtag_images';" );
}

/**
 * Deletes the hashtag images for the specified product.
 *
 * @since 2.0.0
 *
 * @param int $product_id The product ID.
 */
function wc_instagram_delete_product_hashtag_images( $product_id ) {
	// Delete product meta.
	delete_post_meta( $product_id, '_instagram_hashtag_images' );

	// Delete transient.
	delete_transient( wc_instagram_get_product_transient_name( $product_id, 'hashtag_images' ) );
}

/**
 * Clears the transients used for validating the expiration of the product hashtag images.
 *
 * @since 2.2.0
 *
 * @param array $args Optional. Additional arguments.
 */
function wc_instagram_clear_product_hashtag_images_transients( $args = array() ) {
	$query = array(
		'posts_per_page' => -1,
		'post_type'      => 'product',
		'fields'         => 'ids',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_instagram_hashtag_images',
				'compare' => 'EXISTS',
			),
		),
	);

	// Filter by images type.
	if ( isset( $args['images_type'] ) ) {
		// Specific type.
		if ( $args['images_type'] ) {
			$query['meta_query'][] = array(
				'key'   => '_instagram_hashtag_images_type',
				'value' => $args['images_type'],
			);
		} else {
			// Default type (No meta data).
			$query['meta_query'][] = array(
				'key'     => '_instagram_hashtag_images_type',
				'compare' => 'NOT EXISTS',
			);
		}
	}

	$product_ids = get_posts( $query );

	if ( ! empty( $product_ids ) ) {
		foreach ( $product_ids as $product_id ) {
			$transient = wc_instagram_get_product_transient_name( $product_id, 'hashtag_images' );

			delete_transient( $transient );
		}
	}
}
add_action( 'wc_instagram_clear_product_hashtag_images_transients', 'wc_instagram_clear_product_hashtag_images_transients' );

/**
 * Gets the available product conditions.
 *
 * @since 3.1.0
 *
 * @return array
 */
function wc_instagram_get_product_conditions() {
	$conditions = array(
		'new'           => _x( 'New', 'product condition', 'woocommerce-instagram' ),
		'refurbished'   => _x( 'Refurbished', 'product condition', 'woocommerce-instagram' ),
		'used'          => _x( 'Used', 'product condition', 'woocommerce-instagram' ),
		'used_like_new' => _x( 'Used like new', 'product condition', 'woocommerce-instagram' ),
		'used_good'     => _x( 'Used good', 'product condition', 'woocommerce-instagram' ),
		'used_fair'     => _x( 'Used fair', 'product condition', 'woocommerce-instagram' ),
	);

	/**
	 * Filters the available product conditions.
	 *
	 * @since 3.1.0
	 *
	 * @param array $conditions An array with the product conditions.
	 */
	return apply_filters( 'wc_instagram_product_conditions', $conditions );
}
