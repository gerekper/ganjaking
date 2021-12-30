<?php
/**
 * WC_PB_WP_IE_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Import/Export Compatibility.
 * Uses a dedicated '_bundled_items_db_data' meta field to export bundle data using the 'get_data()' method of the WC_Bundled_Item_Data CRUD class.
 * Data is imported again using the WC_Bundled_Item_Data class.
 * Supports import of existing v4 data from post meta.
 *
 * @version 5.0.0
 */
class WC_PB_WP_IE_Compatibility {

	public static function init() {

		// Export bundle data.
		add_filter( 'wxr_export_skip_postmeta', array( __CLASS__, 'wp_export_data' ), 10, 3 );

		if ( class_exists( 'WP_Importer' ) ) {

			// Import bundle data exported using PB v5.
			add_filter( 'wp_import_post_meta', array( __CLASS__, 'wp_import_data' ), 10, 3 );

			// Reassociate bundled items with products on import end.
			add_action( 'import_end', array( __CLASS__, 'wp_import_end' ) );
		}
	}

	/**
	 * Export bundle data using the 'get_data()' method of the WC_Bundled_Item_Data CRUD class.
	 * Data is exported with a hack, when the '_wc_pb_layout_style' meta is exported.
	 *
	 * @param  object  $post
	 * @param  array   $export_columns
	 * @return object
	 */
	public static function wp_export_data( $skip_export, $meta_key, $meta ) {

		global $post;

		// Export serialized data before the '_wc_pb_layout_style' meta.
		if ( $meta_key === '_wc_pb_layout_style' ) {

			$bundled_items = WC_PB_DB::query_bundled_items( array(
				'return'    => 'objects',
				'bundle_id' => $post->ID
			) );

			if ( ! empty( $bundled_items ) ) {
				$data = array();
				foreach ( $bundled_items as $bundled_item ) {
					$data[ $bundled_item->get_id() ] = $bundled_item->get_data();
				}
				$item_data = json_encode( $data );

				?>
				<wp:postmeta>
					<wp:meta_key><?php echo wxr_cdata( '_bundled_items_db_data' ); ?></wp:meta_key>
					<wp:meta_value><?php echo wxr_cdata( $item_data ); ?></wp:meta_value>
				</wp:postmeta>
				<?php
			}
		} elseif ( $meta_key === '_wc_pb_v4_bundle_data' ) {
			$skip_export = true;
		}

		return $skip_export;
	}

	/**
	 * Import json-encoded bundle data using the WC_Bundled_Item_Data CRUD class.
	 *
	 * @param  array  $post_meta
	 * @param  int    $imported_post_id
	 * @param  array  $post
	 * @return void
	 */
	public static function wp_import_data( $post_meta, $imported_post_id, $post ) {

		$bundle_data = false;
		foreach ( $post_meta as $meta_key => $meta_data ) {
			if ( '_bundled_items_db_data' === $meta_data[ 'key' ] ) {
				$bundle_data = json_decode( $meta_data[ 'value' ], true );
				unset( $post_meta[ $meta_key ] );
			}
		}

		if ( ! empty( $bundle_data ) ) {
			foreach ( $bundle_data as $bundled_item_id => $bundled_item_data ) {

				// Create bundled item.
				WC_PB_DB::add_bundled_item( array(
					'bundle_id'  => $imported_post_id,                  // Use the new bundle id.
					'product_id' => $bundled_item_data[ 'product_id' ], // May get modified during import - @see 'wp_import_end().
					'menu_order' => $bundled_item_data[ 'menu_order' ],
					'meta_data'  => $bundled_item_data[ 'meta_data' ],
					'force_add'  => true                                // Bundled product may not exist in the DB yet, but get created later during import.
				) );

			}

			// Flush bundle transients.
			wc_delete_product_transients( $imported_post_id );
		}

		return $post_meta;
	}

	/**
	 * Reassociate bundled item ids with modified bundled product ids on import end.
	 * Also delete the bundled items stock cache.
	 */
	public static function wp_import_end() {
		global $wpdb, $wp_import;

		if ( ! empty( $wp_import ) && ! empty( $wp_import->processed_posts ) ) {

			$processed_products = (array) $wp_import->processed_posts;
			$update_products    = array();

			if ( ! empty( $processed_products ) ) {
				foreach ( $processed_products as $old_id => $new_id ) {
					if ( absint( $old_id ) !== absint( $new_id ) ) {
						$update_products[ $old_id ] = 'WHEN ' . $old_id . ' THEN ' . $new_id;
					}
				}
			}

			if ( ! empty( $update_products ) ) {
				// Reassociate ids.
				$wpdb->query( "
					UPDATE {$wpdb->prefix}woocommerce_bundled_items
					SET product_id = CASE product_id " . implode( ' ', $update_products ) .  " ELSE product_id END
					WHERE product_id IN (" . implode( ',', array_keys( $update_products ) ) . ")
					AND bundle_id IN (" . implode( ',', array_keys( $update_products ) ) . ")
				" );
			}

			WC_PB_DB::bulk_delete_bundled_item_stock_meta();

			$data_store = WC_Data_Store::load( 'product-bundle' );
			$data_store->reset_bundled_items_stock_status();
		}
	}
}

WC_PB_WP_IE_Compatibility::init();
