<?php
/**
 * DB update functions
 *
 * Functions used to upgrade to specific versions of Mix and Match.
 *
 * @package  WooCommerce Mix and Match Products/Update
 * @since    1.2.0
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Batch processing limit.
 *
 * @since 2.0.0
 * @return int
 */
function wc_mnm_update_batch_limit() {
	return (int) apply_filters( 'wc_mnm_update_batch_limit', 20 );
}

/**
 * Data Update for Version 1.2.0.
 *
 * @return bool True to run again, false if completed.
 */
function wc_mnm_update_120_main() {

	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id    = 0;
	$last_product_id = get_option( 'wc_mnm_update_1x2x0_last_product_id', 0 );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT
					DISTINCT P.ID AS product_id
				FROM {$wpdb->posts} AS P
				LEFT JOIN {$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				WHERE
                    ( P.post_type = 'product' ) AND
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_taxonomy_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id        = intval( $container->product_id );
			$priced_individually = get_post_meta( $container_id, '_mnm_per_product_pricing', true );

			// Convert prices to MnM's "base" prices.
			if ( 'yes' === $priced_individually ) {
				$price         = get_post_meta( $container_id, '_base_price', true );
				$regular_price = get_post_meta( $container_id, '_base_regular_price', true );
				$sale_price    = get_post_meta( $container_id, '_base_sale_price', true );
			} else {
				$price         = get_post_meta( $container_id, '_price', true );
				$regular_price = get_post_meta( $container_id, '_regular_price', true );
				$sale_price    = get_post_meta( $container_id, '_sale_price', true );
			}

			update_post_meta( $container_id, '_mnm_base_price', $price );
			update_post_meta( $container_id, '_mnm_base_regular_price', $regular_price );
			update_post_meta( $container_id, '_mnm_base_sale_price', $sale_price );

			delete_post_meta( $container_id, '_base_price' );
			delete_post_meta( $container_id, '_base_regular_price' );
			delete_post_meta( $container_id, '_base_sale_price' );

			// Convert container size to support min/max sizes.
			$min_container_size = get_post_meta( $container_id, '_mnm_container_size', true );
			$max_container_size = get_post_meta( $container_id, '_mnm_max_container_size', true );

			// If a max container size exists, Min/Max plugin was used.
			if ( in_array( '_mnm_max_container_size', get_post_custom_keys( $container_id ) ) ) {

				// Unlimited.
				if ( $min_container_size <= 1 && 0 === $max_container_size ) {
					$min_container_size = 1;
					$max_container_size = '';
				} else {
					$min_container_size = max( $min_container_size, 1 );
					$max_container_size = $max_container_size ? $max_container_size : '';
				}

				// Default MNM plugin.
			} else {

				// Unlimited.
				if ( 0 === $min_container_size ) {
					$min_container_size = 1;
					$max_container_size = '';
					// Fixed Container.
				} else {
					$min_container_size = $min_container_size;
					$max_container_size = $min_container_size;
				}
			}

			update_post_meta( $container_id, '_mnm_min_container_size', $min_container_size );
			update_post_meta( $container_id, '_mnm_max_container_size', $max_container_size );

			delete_post_meta( $container_id, '_mnm_container_size' );
		}

	}

	// Start the run again.
	if ( $container_id ) {
		return update_option( 'wc_mnm_update_1x2x0_last_product_id', $container_id );
	}

	delete_option( 'wc_mnm_update_1x2x0_last_product_id' );
	return false;

}

/**
 * Patch product meta for Version 1.10.0.
 *
 * Unlike 1.3 update, we need to keep track of which IDs have been processed.
 * Because _mnm_data existed prior to update, when over 20 items if we hit the time/memory limit we were infinite looping.
 *
 * @see https://github.com/kathyisawesome/woocommerce-mix-and-match-products/issues/371
 * @return bool True to run again, false if completed.
 */
function wc_mnm_update_1x10_product_meta() {

	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id    = 0;
	$last_product_id = get_option( 'wc_mnm_update_1x10_last_product_id', 0 );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT
					DISTINCT P.ID AS product_id
				FROM {$wpdb->posts} AS P
				LEFT JOIN {$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				LEFT JOIN {$wpdb->postmeta} AS PM ON
					PM.post_id = P.ID AND PM.meta_key = '_mnm_data'
				WHERE
                    ( P.post_type = 'product' ) AND
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND
					( PM.meta_value IS NOT NULL ) AND 
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_taxonomy_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id = intval( $container->product_id );

			// Fix contents array.
			$container_data = get_post_meta( $container_id, '_mnm_data', true );

			$new_contents = array();

			if ( is_array( $container_data ) ) {

				foreach ( array_keys( $container_data ) as $id ) {

					$parent_id = wp_get_post_parent_id( $id );

					$new_contents[ $id ]['child_id']     = intval( $id );
					$new_contents[ $id ]['product_id']   = $parent_id > 0 ? $parent_id : $id;
					$new_contents[ $id ]['variation_id'] = $parent_id > 0 ? $id : 0;

				}
			}

			update_post_meta( $container_id, '_mnm_data', $new_contents );

		}

	}

	// Start the run again.
	if ( $container_id ) {
		return update_option( 'wc_mnm_update_1x10_last_product_id', $container_id );
	}

	delete_option( 'wc_mnm_update_1x10_last_product_id' );
	return false;

}


/**
 * Switch container size meta key from translated string.
 *
 * @since  1.10.0
 */
function wc_mnm_update_1x10_order_item_meta() {

	global $wpdb;

	// Update "Container size" meta key.
	$wpdb->update(
		$wpdb->prefix . 'woocommerce_order_itemmeta',
		array( 'meta_key' => 'mnm_container_size' ),
		array( 'meta_key' => __( 'Container size', 'woocommerce-mix-and-match-products' )
		)
	);

	// Update "Part of"  meta key.
	$wpdb->update(
		$wpdb->prefix . 'woocommerce_order_itemmeta',
		array( 'meta_key' => 'mnm_part_of' ),
		array( 'meta_key' => __( 'Part of', 'woocommerce-mix-and-match-products' )
		)
	);

	// Update "Purchased with" meta key.
	$wpdb->update(
		$wpdb->prefix . 'woocommerce_order_itemmeta',
		array( 'meta_key' => 'mnm_purchased_with' ),
		array( 'meta_key' => __( 'Purchased with', 'woocommerce-mix-and-match-products' )
		)
	);

}

/**
 * Update DB Version
 *
 * @since  1.10.0
 * @deperecated 2.0.0
 */
function wc_mnm_update_1x10_db_version() {
	wc_deprecated_function( __FUNCTION__, '2.0.0', 'Database version option update is scheduled, no need to call directly.' );
	return WC_MNM_Install::update_db_version( '1.10.0' );
}

/**
 * Delete old notices.
 */
function wc_mnm_update_2x00_remove_notices() {
	delete_option( 'wc_mnm_maintenance_notices' );
	delete_option( 'wc_mnm_meta_box_notices' );
}

/**
 * Set global layout settings.
 *
 * Unlike 1.3 update, we need to keep track of which IDs have been processed.
 * Because _mnm_data existed prior to update, when over 20 items if we hit the time/memory limit we were infinite looping.
 *
 * see @link: https://github.com/kathyisawesome/woocommerce-mix-and-match-products/issues/371
 *
 * @since 2.0.0
 *
 * @return bool True to run again, false if completed.
 */
function wc_mnm_update_2x00_customizer_settings() {

	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id      = 0;
	$last_product_id   = get_option( 'wc_mnm_update_2x00_customizer_last_product_id', 0 );

	// Tracking values.
	$layouts     = get_option( 'wc_mnm_layout_tracker', array() );
	$locations   = get_option( 'wc_mnm_add_to_cart_form_location_tracker', array() );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
            "
			SELECT
				DISTINCT P.ID AS product_id
				FROM
					{$wpdb->posts} as P
				JOIN
					{$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				JOIN {$wpdb->postmeta} AS PM
					ON P.ID = PM.post_id
				WHERE
					( P.post_type = 'product' ) AND 
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND 
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_taxonomy_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id = intval( $container->product_id );

			$layout     = get_post_meta( $container_id, '_mnm_layout_style', true );
			$location   = get_post_meta( $container_id, '_mnm_add_to_cart_form_location', true );

			// Keep track of layouts and IDs.
			if ( $layout ) {
				if ( array_key_exists( $layout, $layouts ) && ! in_array( $container_id, $layouts[ $layout ] ) ) {
					$layouts[ $layout ][] = $container_id;
				} else {
					$layouts[ $layout ] = array( $container_id );
				}
			}

			// Keep track of locations and IDs.
			if ( $location ) {
				if ( array_key_exists( $location, $locations ) && ! in_array( $container_id, $locations[ $location ] ) ) {
					$locations[ $location ][] = $container_id;
				} else {
					$locations[ $location ] = array( $container_id );
				}
			}

		}

		// Store values for next batch.

		if ( ! empty( $layouts ) ) {
			update_option( 'wc_mnm_layout_tracker', $layouts );
		}

		if ( ! empty( $locations ) ) {
			update_option( 'wc_mnm_add_to_cart_form_location_tracker', $locations );
		}

		// Start the run again.
		if ( $container_id ) {
			return update_option( 'wc_mnm_update_2x00_customizer_last_product_id', $container_id );
		}

	}

	// Section runs on last iteration of updater.
	$all_ids      = array();
	$override_ids = array();

	// Find the layout applied to the most posts.
	if ( count( $layouts ) > 1 ) {
		$temp = array_map( 'count', $layouts );
		$layout = array_search( max( $temp ), $temp );

		// Grab product IDs of products *not* using global $layout.
		foreach( $layouts as $key => $ids ) {
			$all_ids = array_merge( $all_ids, $ids );
			if ( $layout !== $key ) {
				$override_ids = array_merge( $override_ids, $ids );
			}
		}

	} elseif ( 1 === count( $layouts ) ) {
		$layout = array_key_first( $layouts );
	} else {
		$layout = 'tabular';
	}

	// Find the location applied to the most posts.
	if ( count( $locations ) > 1 ) {
		$temp = array_map( 'count', $locations );
		$location = array_search( max( $temp ), $temp );

		// Grab product IDs of products *not* using global $location.
		foreach( $locations as $key => $ids ) {
			$all_ids = array_merge( $all_ids, $ids );
			if ( $location !== $key ) {
				$override_ids = array_merge( $override_ids, $ids );
			}
		}

	} elseif ( 1 === count( $locations ) ) {
		$location = array_key_first( $locations );
	} else {
		$location = 'default';
	}

	// Prepare product meta query.
	$override_ids = array_unique( $override_ids );
	$global_ids   = array_diff( array_unique( $all_ids ), $override_ids );

	// Update product IDs that are using the global layout.
	if ( ! empty( $global_ids ) ) {
		foreach( $global_ids as $id ) {
			update_post_meta( $id, '_mnm_layout_override', 'no' );
		}
	}

	// Update product IDs that have some kind of override.
	if ( ! empty( $override_ids ) ) {
		foreach( $override_ids as $id ) {
			update_post_meta( $id, '_mnm_layout_override', 'yes' );
		}
	}

	// Store global settings.
	update_option( 'wc_mnm_layout', $layout );
	update_option( 'wc_mnm_add_to_cart_form_location', $location );

	$columns = apply_filters( 'wc_mnm_grid_layout_columns', 3, new WC_Product_Mix_and_Match );

	update_option( 'wc_mnm_number_columns', $columns );

	update_option( 'wc_mnm_display_thumbnail', 'yes' );
	update_option( 'wc_mnm_display_short_description', 'no' );

	// Delete temporary options.
	delete_option( 'wc_mnm_update_2x00_customizer_last_product_id' );
	delete_option( 'wc_mnm_layout_tracker' );
	delete_option( 'wc_mnm_add_to_cart_form_location_tracker' );

	return false;

}

/**
 * Set packing mode.
 *
 * @since 2.0.0
 *
 * @return bool True to run again, false if completed.
 */
function wc_mnm_update_2x00_packing_mode() {

	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id      = 0;
	$last_product_id   = get_option( 'wc_mnm_update_2x00_packing_mode_last_product_id', 0 );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
            "
			SELECT
				DISTINCT P.ID AS product_id
				FROM
					{$wpdb->posts} as P
				JOIN
					{$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				JOIN {$wpdb->postmeta} AS PM
					ON P.ID = PM.post_id
				WHERE
					( P.post_type = 'product' ) AND 
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND 
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_taxonomy_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id         = intval( $container->product_id );

			$virtual              = get_post_meta( $container_id, '_virtual', true );
			$per_product_shipping = get_post_meta( $container_id, '_mnm_per_product_shipping', true );
			$packing_mode         = 'together';

			if ( 'yes' === $virtual ) {
				$packing_mode = 'yes' === $per_product_shipping ? 'separate' : 'virtual';
			} else {
				$packing_mode = 'yes' === $per_product_shipping ? 'separate_plus' : 'together';
			}

			update_post_meta( $container_id, '_mnm_packing_mode', $packing_mode );
			update_post_meta( $container_id, '_virtual', 'no' );
			delete_post_meta( $container_id, '_mnm_per_product_shipping' );

		}

		// Start the run again.
		if ( $container_id ) {
			return update_option( 'wc_mnm_update_2x00_packing_mode_last_product_id', $container_id );
		}

	}

	// Delete temporary options.
	delete_option( 'wc_mnm_update_2x00_packing_mode_last_product_id' );

	return false;

}

/**
 * Add prefix to mnm_container_size, etc order item meta
 *
 * @since 2.0.0
 */
function wc_mnm_update_2x00_order_item_meta() {
	global $wpdb;

	// Rename the order item meta key.
	$wpdb->update( $wpdb->prefix . 'woocommerce_order_itemmeta', array( 'meta_key' => '_mnm_container_size' ), array( 'meta_key' => 'mnm_container_size' ) );

	// Update "Part of"  meta key.
	$wpdb->update( $wpdb->prefix . 'woocommerce_order_itemmeta', array( 'meta_key' => '_mnm_part_of' ), array( 'meta_key' => 'mnm_part_of' ) );

	// Update "Purchased with" meta key.
	$wpdb->update( $wpdb->prefix . 'woocommerce_order_itemmeta', array( 'meta_key' => '_mnm_purchased_with' ), array( 'meta_key' => 'mnm_purchased_with' ) );

}

/**
 * Add data into new table for 2.0.
 *
 * @since 2.0.0
 * @return bool True to run again, false if completed.
 */
function wc_mnm_update_2x00_custom_tables() {
	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id    = 0;
	$last_product_id = get_option( 'wc_mnm_update_2x00_last_product_id', 0 );
	$existing_ids    = get_option( 'wc_mnm_update_2x00_product_ids', array() );

	// Fetch the MNM products whose children will be copied to the Child Items table
	$containers = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT
					DISTINCT P.ID AS product_id
				FROM
					{$wpdb->posts} AS P
					LEFT JOIN
					-- Fetch only the Mix & Match products
					{$wpdb->term_relationships} AS PRODUCT_TYPE 
						ON PRODUCT_TYPE.object_id = P.ID
					LEFT JOIN
					-- Fetch the contents from the product meta
					{$wpdb->postmeta} AS PM
						ON PM.post_id = P.ID AND PM.meta_key = '_mnm_data'
					LEFT JOIN
					-- Fetch only the MNM products whose child products have not yet been
					-- stored in the Child Items table
					{$wpdb->prefix}wc_mnm_child_items AS CHILD_ITEMS 
						ON CHILD_ITEMS.product_id = P.ID
				WHERE
					( P.post_type = 'product' ) AND
                    ( PRODUCT_TYPE.term_taxonomy_id = %d ) AND
					( P.ID > %d ) AND
					( PM.meta_value IS NOT NULL ) AND
					( CHILD_ITEMS.child_item_id IS NULL)
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id = intval( $container->product_id );

			// Load the child items with the MNM product.
			$container_data = get_post_meta( $container_id, '_mnm_data', true );

			// Skip empty or invalid data.
			if ( empty( $container_data ) || ! is_array( $container_data ) ) {
				continue;
			}

			// Start a transaction, to ensure that the INSERT operation can be rolled back in case of error.
			wc_transaction_query();

			// Prepare the base SQL query
			$SQL = "
			INSERT INTO {$wpdb->prefix}wc_mnm_child_items (product_id, container_id, menu_order)
			VALUES
			";

			try {
				$item_menu_order = 0;
				$insert_rows = array();
				foreach ( array_keys( $container_data ) as $child_item_product_id ) {

					// Test if product ID exists.
					if ( ! array_key_exists( $child_item_product_id, $existing_ids ) ) {
						$existing_ids[$child_item_product_id] = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID=%s", $child_item_product_id ) ); // Comes back null if the product does not exist in DB.
					}

					// Skip any products that don't exist.
					if ( empty( $existing_ids[$child_item_product_id] ) ) {
						continue;
					}

					// Add a row for the INSERT query. This will allow to insert multiple rows at once
					// @link https://dev.mysql.com/doc/refman/8.0/en/insert.html
					$insert_rows[] = $wpdb->prepare( '(%d, %d, %d)', $child_item_product_id, $container_id, $item_menu_order );
					$item_menu_order++;
				}

				// Build the SQL statement to insert all the rows
				$SQL .= implode( ', ', $insert_rows );

				// Add the child items to the child items table
				if ( false === $wpdb->query( $SQL ) ) {
					throw new Exception( sprintf( esc_html__( 'Mix and Match child item database conversion failed for product #%d. Error: %s', 'woocommerce-mix-and-match-products' ), $container_id, $wpdb->last_error ) );
				}

				// Commit the transaction. This will ensure that the items will be saved
				wc_transaction_query( 'commit' );

			}
			catch( Exception $e ) {

				// If anything unexpected happens, roll back the transaction as well
				wc_transaction_query( 'rollback' );

				wc_get_logger()->log( 'error', $e->getMessage(), array( 'source' => 'wc_mnm_db_updates' ) );
			}
		}

		update_option( 'wc_mnm_update_2x00_product_ids', $existing_ids );

	}

	// Start the run again.
	if ( $container_id ) {
		return update_option( 'wc_mnm_update_2x00_last_product_id', $container_id );
	}

	// Set transient for clean up button.
	set_transient( 'wc_mnm_show_2x00_cleanup_legacy_child_meta', 'yes', 25200 );

	delete_option( 'wc_mnm_update_2x00_last_product_id' );
	delete_option( 'wc_mnm_update_2x00_product_ids' );
	return false;
}


/**
 * Update category contents mini-extension data.
 *
 * @since 2.0.0
 */
function wc_mnm_update_2x00_category_contents_meta() {
	global $wpdb;

	// Process all the existing MNM products to extract the children products
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id      = 0;
	$last_product_id   = get_option( 'wc_mnm_update_2x00_category_ids_last_product_id', 0 );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
            "
			SELECT
				DISTINCT P.ID AS product_id
				FROM
					{$wpdb->posts} as P
				JOIN
					{$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				JOIN {$wpdb->postmeta} AS PM
					ON P.ID = PM.post_id
				WHERE
					( P.post_type = 'product' ) AND 
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND 
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id = intval( $container->product_id );

			$source = 'yes' === get_post_meta( $container_id, '_mnm_use_category', true ) ? 'categories' : 'products';

			$new_categories = array();
			$old_categories = get_post_meta( $container_id, '_mnm_product_cat', true );

			if ( is_integer( $old_categories ) ) {

				$new_categories[] = $old_categories;

			} elseif ( is_array( $old_categories ) ) {

				foreach( $old_categories as $slug ) {

					$cat = get_term_by( 'slug', $slug, 'product_cat' );

					if ( $cat instanceof WP_Term ) {
						$new_categories[] = $cat->term_id;
					}

				}

			}

			update_post_meta( $container_id, '_mnm_content_source', $source );
			update_post_meta( $container_id, '_mnm_child_category_ids', $new_categories );

			delete_post_meta( $container_id, '_mnm_use_category' );
			delete_post_meta( $container_id, '_mnm_product_cat' );

		}

		// Start the run again.
		if ( $container_id ) {
			return update_option( 'wc_mnm_update_2x00_category_ids_last_product_id', $container_id );
		}

	}

	// Delete temporary options.
	delete_option( 'wc_mnm_update_2x00_category_ids_last_product_id' );

	return false;

}



/**
 * Remove duplicate meta keys, prefix meta keys
 *
 * @since 2.0.0
 */
function wc_mnm_update_2x00_product_meta() {
	global $wpdb;

	$duplicate_keys = array(
		'_max_raw_price'             => '_mnm_max_price',
		'_max_raw_regular_price'     => '_mnm_max_regular_price',
		'_price'                     => '_mnm_base_price',
		'_regular_price'             => '_mnm_base_regular_price',
		'_sale_price'                => '_mnm_base_sale_price',
		'_layout'                    => '_mnm_layout_style',
		'_add_to_cart_form_location' => '_mnm_add_to_cart_form_location',
		'_min_container_size'        => '_mnm_min_container_size',
		'_max_container_size'        => '_mnm_max_container_size',
		'_contents'                  => '_mnm_data',
		'_priced_per_product'        => '_mnm_per_product_pricing',
		'_discount'                  => '_mnm_per_product_discount',
		'_shipped_per_product'       => '_mnm_per_product_shipping',
	);

	// Delete the rogue duplicate keys.
	$delete_keys = array_diff( array_keys( $duplicate_keys ), array( '_price', '_regular_price', '_sale_price' ) );

	$placeholders = implode( ', ', array_fill( 0, count( $delete_keys ), '%s' ) );

	$sql = "
		DELETE
		FROM {$wpdb->prefix}postmeta
		WHERE `meta_key` IN ( {$placeholders} )
	";

	$sql = $wpdb->prepare( $sql, $delete_keys );

	if ( false === $wpdb->query( $sql ) ) {
		wc_get_logger()->log( 'error', 'Mix and Match could not delete duplicate product meta.', array( 'source' => 'wc_mnm_db_updates' ) );
	}

}


/**
 * Delete the old _mnm_data post meta.
 *
 * @since  2.0.0
 */
function  wc_mnm_update_2x00_cleanup_legacy_child_meta() {

	global $wpdb;

	$delete_keys = array(
		'_mnm_data'
	);

	$placeholders = implode( ', ', array_fill( 0, count( $delete_keys ), '%s' ) );

	$sql = "
		DELETE
		FROM {$wpdb->prefix}postmeta
		WHERE `meta_key` IN ( {$placeholders} )
	";

	$sql = $wpdb->prepare( $sql, $delete_keys );

	if ( false === $wpdb->query( $sql ) ) {
		wc_get_logger()->log( 'error', 'Mix and Match could not delete legacy product meta.', array( 'source' => 'wc_mnm_db_updates' ) );
	}

}


/**
 * Remove duplicate meta keys (again)
 *
 * @since 2.2.0
 */
function wc_mnm_update_2x2x0_delete_duplicate_meta() {

	global $wpdb;

	// Process all the existing MNM products to only delete meta for them (as some meta keys are a bit generic)
	$mnm_term = get_term_by( 'slug', 'mix-and-match', 'product_type' );

	if ( false === $mnm_term ) {
		return false;
	}

	// As product $extra_data, woo was saving automatically.
	$delete_keys = array(
		'_min_raw_price',
		'_min_raw_regular_price',
		'_max_raw_price',
		'_max_raw_regular_price',
		'_layout_override',
		'_layout',
		'_add_to_cart_form_location',
		'_min_container_size',
		'_max_container_size',
		'_priced_per_product',
		'_discount',
		'_packing_mode',
		'_weight_cumulative',
		'_content_source',
		'_child_category_ids',
		'_child_items_stock_status',
	);

	// Grab post ids to update, storing the last ID processed, so we know where to start next time.
	$container_id      = 0;
	$last_product_id   = get_option( 'wc_mnm_update_2x2x0_delete_duplicate_meta_last_product_id', 0 );

	$containers = $wpdb->get_results(
		$wpdb->prepare(
            "
			SELECT
				DISTINCT P.ID AS product_id
				FROM
					{$wpdb->posts} as P
				JOIN
					{$wpdb->term_relationships} AS PRODUCT_TYPE
					ON PRODUCT_TYPE.object_id = P.ID
				JOIN {$wpdb->postmeta} AS PM
					ON P.ID = PM.post_id
				WHERE
					( P.post_type = 'product' ) AND 
					( PRODUCT_TYPE.term_taxonomy_id = %d ) AND 
					( P.ID > %d )
				GROUP BY
					P.ID
				ORDER BY
					P.ID
				ASC
				LIMIT %d
			",
			$mnm_term->term_id,
			$last_product_id,
			wc_mnm_update_batch_limit()
		)
	);

	if ( ! empty( $containers ) ) {

		foreach ( $containers as $container ) {

			$container_id = intval( $container->product_id );

			foreach( $delete_keys as $key ) {
				$result = delete_post_meta( $container_id, $key );
			}

		}

		// Start the run again.
		if ( $container_id ) {
			return update_option( 'wc_mnm_update_2x2x0_delete_duplicate_meta_last_product_id', $container_id );
		}

	}

	// Delete temporary options.
	delete_option( 'wc_mnm_update_2x2x0_delete_duplicate_meta_last_product_id' );

	return false;

}