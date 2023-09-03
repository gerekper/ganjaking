<?php
/**
 * Product Bundles DB update functions
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_pb_update_300() {

	global $wpdb;

	// Serialize v2.X postmeta.
	$v2_bundles = $wpdb->get_results( "
		SELECT DISTINCT posts.ID AS bundle_id FROM {$wpdb->posts} AS posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_bundled_ids'
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = '_bundle_data'
		WHERE posts.post_type = 'product'
		AND postmeta.meta_value IS NOT NULL
		AND postmeta2.meta_value IS NULL
	" );

	if ( ! empty( $v2_bundles ) ) {
		foreach ( $v2_bundles as $v2_bundle ) {

			$bundle_id          = $v2_bundle->bundle_id;

			$bundled_item_ids   = get_post_meta( $bundle_id, '_bundled_ids', true );
			$default_attributes = get_post_meta( $bundle_id, '_bundle_defaults', true );
			$allowed_variations = get_post_meta( $bundle_id, '_allowed_variations', true );

			$bundle_data = array();

			foreach ( $bundled_item_ids as $bundled_item_id ) {

				$bundle_data[ $bundled_item_id ] = array();

				$filtered       = get_post_meta( $bundle_id, 'filter_variations_' . $bundled_item_id, true );
				$o_defaults     = get_post_meta( $bundle_id, 'override_defaults_' . $bundled_item_id, true );
				$hide_thumbnail = get_post_meta( $bundle_id, 'hide_thumbnail_' . $bundled_item_id, true );
				$item_o_title   = get_post_meta( $bundle_id, 'override_title_' . $bundled_item_id, true );
				$item_title     = get_post_meta( $bundle_id, 'product_title_' . $bundled_item_id, true );
				$item_o_desc    = get_post_meta( $bundle_id, 'override_description_' . $bundled_item_id, true );
				$item_desc      = get_post_meta( $bundle_id, 'product_description_' . $bundled_item_id, true );
				$item_qty       = get_post_meta( $bundle_id, 'bundle_quantity_' . $bundled_item_id, true );
				$discount       = get_post_meta( $bundle_id, 'bundle_discount_' . $bundled_item_id, true );
				$visibility     = get_post_meta( $bundle_id, 'visibility_' . $bundled_item_id, true );

				$sep = explode( '_', $bundled_item_id );

				$bundle_data[ $bundled_item_id ][ 'product_id' ]        = $sep[0];
				$bundle_data[ $bundled_item_id ][ 'filter_variations' ] = ( $filtered === 'yes' ) ? 'yes' : 'no';

				if ( isset( $allowed_variations[ $bundled_item_id ] ) ) {
					$bundle_data[ $bundled_item_id ][ 'allowed_variations' ] = $allowed_variations[ $bundled_item_id ];
				}

				$bundle_data[ $bundled_item_id ][ 'override_defaults' ] = ( $o_defaults === 'yes' ) ? 'yes' : 'no';

				if ( isset( $default_attributes[ $bundled_item_id ] ) ) {
					$bundle_data[ $bundled_item_id ][ 'bundle_defaults' ] = $default_attributes[ $bundled_item_id ];
				}

				$bundle_data[ $bundled_item_id ][ 'hide_thumbnail' ] = ( $hide_thumbnail === 'yes' ) ? 'yes' : 'no';
				$bundle_data[ $bundled_item_id ][ 'override_title' ] = ( $item_o_title === 'yes' ) ? 'yes' : 'no';

				if ( $item_o_title === 'yes' ) {
					$bundle_data[ $bundled_item_id ][ 'product_title' ] = $item_title;
				}

				$bundle_data[ $bundled_item_id ][ 'override_description' ] = ( $item_o_desc === 'yes' ) ? 'yes' : 'no';

				if ( $item_o_desc === 'yes' ) {
					$bundle_data[ $bundled_item_id ][ 'product_description' ] = $item_desc;
				}

				$bundle_data[ $bundled_item_id ][ 'bundle_quantity' ]          = $item_qty;
				$bundle_data[ $bundled_item_id ][ 'bundle_quantity_max' ]      = $item_qty;
				$bundle_data[ $bundled_item_id ][ 'bundle_discount' ]          = $discount;
				$bundle_data[ $bundled_item_id ][ 'visibility' ]               = ( $visibility === 'hidden' ) ? 'hidden' : 'visible';
				$bundle_data[ $bundled_item_id ][ 'hide_filtered_variations' ] = 'no';
			}

			update_post_meta( $bundle_id, '_bundle_data', $bundle_data );

			$wpdb->query( $wpdb->prepare( "
				DELETE FROM {$wpdb->postmeta}
				WHERE post_id LIKE %s
				AND ( meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE %s
					OR meta_key LIKE ('_bundled_ids')
					OR meta_key LIKE ('_bundle_defaults')
					OR meta_key LIKE ('_allowed_variations')
				)
			", $bundle_id, 'filter_variations_%', 'override_defaults_%', 'bundle_quantity_%', 'bundle_discount_%', 'hide_thumbnail_%', 'override_title_%', 'product_title_%', 'override_description_%', 'product_description_%', 'hide_filtered_variations_%', 'visibility_%' ) );
		}
	}
}

function wc_pb_update_300_db_version() {
	WC_PB_Install::update_db_version( '3.0.0' );
}

function wc_pb_update_500_delete_unused_meta() {

	global $wpdb;

	// Delete unused meta.
	$wpdb->query( "
		DELETE FROM {$wpdb->postmeta}
		WHERE ( meta_key = '_min_bundle_price'
		OR meta_key = '_max_bundle_price' )
	" );
}

function wc_pb_update_500_main( $updater = false ) {

	global $wpdb;

	// Grab post ids to update.
	$bundles = $wpdb->get_results( "
		SELECT DISTINCT posts.ID AS bundle_id FROM {$wpdb->posts} AS posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_bundle_data'
		WHERE posts.post_type = 'product'
		AND postmeta.meta_value IS NOT NULL
	" );


	if ( ! empty( $bundles ) ) {
		foreach ( $bundles as $index => $bundle ) {

			// Make sure we are nowhere close to memory & PHP timeout limits - check state every 20 migrated products.
			if ( is_object( $updater ) ) {
				if ( $index % 20 === 19 ) {
					if ( $updater->time_exceeded() || $updater->memory_exceeded() ) {
						return -1;
					}
				}
			}

			$bundle_id            = (int) $bundle->bundle_id;
			$bundle_data          = get_post_meta( $bundle_id, '_bundle_data', true );
			$priced_individually  = get_post_meta( $bundle_id, '_per_product_pricing_active', true );
			$shipped_individually = get_post_meta( $bundle_id, '_per_product_shipping_active', true );

			if ( ! empty( $bundle_data ) ) {

				// Get product type.

				$product_type = WC_Product_Factory::get_product_type( $bundle_id );

				// Delete existing data left over from previous runs.

				$wpdb->query( $wpdb->prepare( "
					DELETE meta FROM {$wpdb->prefix}woocommerce_bundled_itemmeta AS meta
					INNER JOIN {$wpdb->prefix}woocommerce_bundled_items AS items
					ON meta.bundled_item_id = items.bundled_item_id
					WHERE items.bundle_id = %s
				", $bundle_id ) );

				$wpdb->query( $wpdb->prepare( "
					DELETE FROM {$wpdb->prefix}woocommerce_bundled_items
					WHERE bundle_id = %s
				", $bundle_id ) );

				// Create bundled item DB entries and associated meta.

				$bundled_item_menu_order = 0;

				foreach ( $bundle_data as $bundled_item_data ) {

					// Convert old meta to new.

					$bundle_args = array(
						'priced_individually'  => $priced_individually,
						'shipped_individually' => $shipped_individually
					);

					$v5_meta = wc_pb_update_v4_meta_to_v5( $bundled_item_data, $bundle_args );

					if ( false === $v5_meta ) {
						continue;
					}

					$product_id = (int) $bundled_item_data[ 'product_id' ];

					// Create 'bundled_item' DB entry.

					$args = array(
						'product_id' => $product_id,
						'bundle_id'  => $bundle_id,
						'menu_order' => $bundled_item_menu_order,
						'meta_data'  => $v5_meta
					);

					$bundled_item_menu_order++;

					WC_PB_DB::add_bundled_item( $args );
				}

				// Create layout field.

				if ( '' === get_post_meta( $bundle_id, '_wc_pb_layout_style', true ) ) {
					update_post_meta( $bundle_id, '_wc_pb_layout_style', 'default' );
				}

				// Copy base price to price fields, if applicable.

				if ( 'yes' === $priced_individually && 'bundle' === $product_type ) {

					delete_post_meta( $bundle_id, '_price' );
					delete_post_meta( $bundle_id, '_regular_price' );
					delete_post_meta( $bundle_id, '_sale_price' );

					$base_price         = get_post_meta( $bundle_id, '_base_price', true );
					$base_regular_price = get_post_meta( $bundle_id, '_base_regular_price', true );
					$base_sale_price    = get_post_meta( $bundle_id, '_base_sale_price', true );

					update_post_meta( $bundle_id, '_price', $base_price );
					update_post_meta( $bundle_id, '_regular_price', $base_regular_price );
					update_post_meta( $bundle_id, '_sale_price', $base_sale_price );
				}

				// Delete product transients.
				wc_delete_product_transients( $bundle_id );

				// Rename '_per_product_pricing_active'.
				$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->postmeta}
					SET meta_key = '_wc_pb_v4_per_product_pricing'
					WHERE meta_key = '_per_product_pricing_active'
					AND post_id = %d
				", $bundle_id ) );

				// Rename '_per_product_shipping_active'.
				$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->postmeta}
					SET meta_key = '_wc_pb_v4_per_product_shipping'
					WHERE meta_key = '_per_product_shipping_active'
					AND post_id = %d
				", $bundle_id ) );

				// Rename '_bundle_data'.
				$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->postmeta}
					SET meta_key = '_wc_pb_v4_bundle_data'
					WHERE meta_key = '_bundle_data'
					AND post_id = %d
				", $bundle_id ) );
			}
		}
	}
}

function wc_pb_update_v4_meta_to_v5( $bundled_item_data, $args = array() ) {

	if ( ! isset( $bundled_item_data[ 'product_id' ] ) ) {
		return false;
	}

	// Populate bundled item settings values.

	$quantity_min           = isset( $bundled_item_data[ 'bundle_quantity' ] ) ? absint( $bundled_item_data[ 'bundle_quantity' ] ) : 1;
	$quantity_max           = isset( $bundled_item_data[ 'bundle_quantity_max' ] ) ? $bundled_item_data[ 'bundle_quantity_max' ] : $quantity_min;

	$optional               = ! empty( $bundled_item_data[ 'optional' ] ) && $bundled_item_data[ 'optional' ] === 'yes' ? 'yes' : 'no';
	$discount               = ! empty( $bundled_item_data[ 'bundle_discount' ] ) ? ( double ) $bundled_item_data[ 'bundle_discount' ] : false;
	$hide_thumbnail         = ! empty( $bundled_item_data[ 'hide_thumbnail' ] ) && $bundled_item_data[ 'hide_thumbnail' ] === 'yes' ? 'yes' : 'no';

	$override_title         = ! empty( $bundled_item_data[ 'override_title' ] ) && $bundled_item_data[ 'override_title' ] === 'yes' ? 'yes' : 'no';
	$overridden_title       = $override_title === 'yes' && isset( $bundled_item_data[ 'product_title' ] ) ? $bundled_item_data[ 'product_title' ] : false;

	$override_description   = ! empty( $bundled_item_data[ 'override_description' ] ) && $bundled_item_data[ 'override_description' ] === 'yes' ? 'yes' : 'no';
	$overridden_description = $override_description === 'yes' && isset( $bundled_item_data[ 'product_description' ] ) ? $bundled_item_data[ 'product_description' ] : false;

	$filter_variations      = ! empty( $bundled_item_data[ 'filter_variations' ] ) && $bundled_item_data[ 'filter_variations' ] === 'yes' ? 'yes' : 'no';
	$allowed_variations     = $filter_variations === 'yes' && ! empty( $bundled_item_data[ 'allowed_variations' ] ) && is_array( $bundled_item_data[ 'allowed_variations' ] ) ? $bundled_item_data[ 'allowed_variations' ] : false;

	$override_defaults      = ! empty( $bundled_item_data[ 'override_defaults' ] ) && $bundled_item_data[ 'override_defaults' ] === 'yes' ? 'yes' : 'no';
	$bundle_defaults        = $override_defaults === 'yes' && ! empty( $bundled_item_data[ 'bundle_defaults' ] ) && is_array( $bundled_item_data[ 'bundle_defaults' ] ) ? $bundled_item_data[ 'bundle_defaults' ] : false;


	$visibility = array(
		'product' => 'visible',
		'cart'    => 'visible',
		'order'   => 'visible',
	);

	if ( ! empty( $bundled_item_data[ 'visibility' ] ) ) {
		if ( is_array( $bundled_item_data[ 'visibility' ] ) ) {
			$visibility[ 'product' ] = ! empty( $bundled_item_data[ 'visibility' ][ 'product' ] ) && $bundled_item_data[ 'visibility' ][ 'product' ] === 'hidden' ? 'hidden' : 'visible';
			$visibility[ 'cart' ]    = ! empty( $bundled_item_data[ 'visibility' ][ 'cart' ] ) && $bundled_item_data[ 'visibility' ][ 'cart' ] === 'hidden' ? 'hidden' : 'visible';
			$visibility[ 'order' ]   = ! empty( $bundled_item_data[ 'visibility' ][ 'order' ] ) && $bundled_item_data[ 'visibility' ][ 'order' ] === 'hidden' ? 'hidden' : 'visible';
		} else {
			if ( $bundled_item_data[ 'visibility' ] === 'hidden' ) {
				$visibility[ 'product' ] = 'hidden';
			} elseif ( $bundled_item_data[ 'visibility' ] === 'secret' ) {
				$visibility[ 'product' ] = $visibility[ 'cart' ] = $visibility[ 'order' ] = 'hidden';
			}
		}
	}

	// Values indexed by old field names.

	$default_bundled_item_fields_old = array(
		'bundle_quantity'          => $quantity_min,
		'bundle_quantity_max'      => $quantity_max,
		'override_title'           => $override_title,
		'product_title'            => $overridden_title,
		'override_description'     => $override_description,
		'product_description'      => $overridden_description,
		'optional'                 => $optional,
		'hide_thumbnail'           => $hide_thumbnail,
		'bundle_discount'          => $discount,
		'filter_variations'        => $filter_variations,
		'override_defaults'        => $override_defaults,
		'allowed_variations'       => $allowed_variations,
		'bundle_defaults'          => $bundle_defaults,
		'visibility'               => $visibility,
		'hide_filtered_variations' => '' // deprecated field
	);

	// Values indexed by new field names.

	$default_bundled_item_fields_new = array(
		'quantity_min'                          => $default_bundled_item_fields_old[ 'bundle_quantity' ],
		'quantity_max'                          => $default_bundled_item_fields_old[ 'bundle_quantity_max' ],
		'override_title'                        => $default_bundled_item_fields_old[ 'override_title' ],
		'title'                                 => $default_bundled_item_fields_old[ 'product_title' ],
		'override_description'                  => $default_bundled_item_fields_old[ 'override_description' ],
		'description'                           => $default_bundled_item_fields_old[ 'product_description' ],
		'optional'                              => $default_bundled_item_fields_old[ 'optional' ],
		'hide_thumbnail'                        => $default_bundled_item_fields_old[ 'hide_thumbnail' ],
		'discount'                              => $default_bundled_item_fields_old[ 'bundle_discount' ],
		'override_variations'                   => $default_bundled_item_fields_old[ 'filter_variations' ],
		'override_default_variation_attributes' => $default_bundled_item_fields_old[ 'override_defaults' ],
		'allowed_variations'                    => $default_bundled_item_fields_old[ 'allowed_variations' ],
		'default_variation_attributes'          => $default_bundled_item_fields_old[ 'bundle_defaults' ],
		'single_product_visibility'             => $default_bundled_item_fields_old[ 'visibility' ][ 'product' ],
		'cart_visibility'                       => $default_bundled_item_fields_old[ 'visibility' ][ 'cart' ],
		'order_visibility'                      => $default_bundled_item_fields_old[ 'visibility' ][ 'order' ],
		'single_product_price_visibility'       => 'visible',
		'cart_price_visibility'                 => 'visible',
		'order_price_visibility'                => 'visible',
		'priced_individually'                   => isset( $args[ 'priced_individually' ] ) && 'yes' === $args[ 'priced_individually' ] ? 'yes' : 'no',
		'shipped_individually'                  => isset( $args[ 'shipped_individually' ] ) && 'yes' === $args[ 'shipped_individually' ] ? 'yes' : 'no'
	);

	// Final list of converted meta.

	$reserved_fields   = array_merge( $default_bundled_item_fields_old, $default_bundled_item_fields_new, array( 'product_id' => 1 ) );
	$custom_fields     = array_diff_key( $bundled_item_data, $reserved_fields );
	$bundled_item_meta = array_merge( $default_bundled_item_fields_new, $custom_fields );

	return array_filter( $bundled_item_meta );
}

function wc_pb_update_500_db_version() {
	WC_PB_Install::update_db_version( '5.0.0' );
}

function wc_pb_update_510_main( $updater = false ) {

	global $wpdb;

	$bundle_term = get_term_by( 'slug', 'bundle', 'product_type' );

	if ( $bundle_term ) {

		$bundles = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT posts.ID AS bundle_id FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON ( posts.ID = rel.object_id )
			LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_wc_pb_base_price'
			WHERE rel.term_taxonomy_id = %d
			AND posts.post_type = 'product'
			AND postmeta.meta_value IS NULL
		", $bundle_term->term_taxonomy_id ) );

		if ( ! empty( $bundles ) ) {
			foreach ( $bundles as $index => $bundle ) {

				// Make sure we are nowhere close to memory & PHP timeout limits - check state every 20 migrated products.
				if ( is_object( $updater ) ) {
					if ( $index % 20 === 19 ) {
						if ( $updater->time_exceeded() || $updater->memory_exceeded() ) {
							return -1;
						}
					}
				}

				$bundle_id = (int) $bundle->bundle_id;

				$price         = get_post_meta( $bundle_id, '_price', true );
				$regular_price = get_post_meta( $bundle_id, '_regular_price', true );
				$sale_price    = get_post_meta( $bundle_id, '_sale_price', true );

				update_post_meta( $bundle_id, '_wc_pb_base_price', $price );
				update_post_meta( $bundle_id, '_wc_pb_base_regular_price', $regular_price );
				update_post_meta( $bundle_id, '_wc_pb_base_sale_price', $sale_price );
			}
		}
	}
}

function wc_pb_update_510_delete_unused_meta() {

	global $wpdb;

	// Delete unused meta.
	$wpdb->query( "
		DELETE FROM {$wpdb->postmeta}
		WHERE meta_key = '_wc_sw_min_price'
	" );
}

function wc_pb_update_510_db_version() {
	WC_PB_Install::update_db_version( '5.1.0' );
}
