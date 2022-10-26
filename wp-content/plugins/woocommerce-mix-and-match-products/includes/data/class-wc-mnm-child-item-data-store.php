<?php
/**
 * Child Item Data CRUD.
 *
 * @package WooCommerce Mix and Match Products\DataStores
 * @since    2.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_MNM_Child_Item_Data_Store file.
 */
class WC_MNM_Child_Item_Data_Store {

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete data from the database.
	|
	*/

	/**
	 * Create a new child item in database.
	 *
	 * @param WC_MNM_Child_Item $child_item child item object.
	 */
	public function create( &$child_item ) {

		try {

			wc_transaction_query( 'start' );

			global $wpdb;

			$result = $wpdb->insert(
                "{$wpdb->prefix}wc_mnm_child_items",
                array(
				'product_id'   => $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id(),
				'container_id' => $child_item->get_container_id(),
				'menu_order'   => $child_item->get_menu_order()
                ) 
            );

			if ( false === $result ) {
				throw new Exception( sprintf( esc_html__( 'Mix and Match child item creation failed. Error: %s', 'woocommerce-mix-and-match-products' ), $wpdb->last_error ) );
			}

			$child_item->set_id( $wpdb->insert_id );
			$child_item->apply_changes();

			$this->clear_caches( $child_item );

			wc_transaction_query( 'commit' );

			do_action( 'wc_mnm_new_child_item', $child_item );

		}  catch ( Exception $e ) {
			wc_transaction_query( 'rollback' );
		}

	}

	/**
	 * Read from the database.
	 *
	 * @param WC_MNM_Child_Item $child_item child item object.
	 */
	public function read( &$child_item ) {
		global $wpdb;

		$child_item->set_defaults();

		// Get from cache if available.
		$data = wp_cache_get( 'wc-mnm-child-item-' . $child_item->get_id(), 'wc-mnm-child-items' );

		if ( false === $data ) {
			$data = $wpdb->get_row(
                $wpdb->prepare(
                    "
				SELECT items.child_item_id, 
					CASE
					WHEN p.post_parent > 0 THEN p.post_parent
					ELSE items.product_id 
					END AS product_id,
				CASE
					WHEN p.post_parent > 0 THEN items.product_id
					ELSE 0
					END AS variation_id,
				items.container_id, items.menu_order
				FROM {$wpdb->prefix}wc_mnm_child_items AS items 
				INNER JOIN {$wpdb->prefix}posts as p ON items.product_id = p.ID
				WHERE items.child_item_id = %d",
                    $child_item->get_id()
                ) 
            );
			wp_cache_set( 'wc-mnm-child-item-' . $child_item->get_id(), $data, 'wc-mnm-child-items' );
		}

		if ( ! $data ) {
			throw new Exception( sprintf( esc_html__( 'Invalid Mix and Match child item.', 'woocommerce-mix-and-match-products' ), $wpdb->last_error ) );
		}

		$child_item->set_props(
			array(
				'product_id'   => $data->product_id,
				'variation_id' => $data->variation_id,
				'container_id' => $data->container_id,
				'menu_order'   => $data->menu_order,
			)
		);

		$child_item->set_object_read( true );

	}

	/**
	 * Update data in the database.
	 *
	 * @param WC_MNM_Child_Item $child_item child item object.
	 */
	public function update( &$child_item ) {
		global $wpdb;

		$changes = $child_item->get_changes();

		if ( array_intersect( array( 'menu_order' ), array_keys( $changes ) ) ) {
			$result = $wpdb->update(
                "{$wpdb->prefix}wc_mnm_child_items",
                array(
				'product_id'   => $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id(),
				'container_id' => $child_item->get_container_id(),
				'menu_order'   => $child_item->get_menu_order()
                ),
                array( 'child_item_id' => $child_item->get_id() ) 
            );

			if ( false === $result ) {
				throw new Exception( sprintf( esc_html__( 'Mix and Match child item update failed. Error: %s', 'woocommerce-mix-and-match-products' ), $wpdb->last_error ) );
			}
		}

		$child_item->apply_changes();
		$this->clear_caches( $child_item );

		do_action( 'wc_mnm_update_child_item', $child_item );

	}

	/**
	 * Delete data from the database.
	 *
	 * @param WC_MNM_Child_Item $child_item child item object.
	 */
	public function delete( &$child_item ) {

		if ( $child_item->get_id() ) {
			global $wpdb;
			do_action( 'wc_mnm_before_delete_child_item', $child_item );
			$result = $wpdb->delete( "{$wpdb->prefix}wc_mnm_child_items", array( 'child_item_id' => $child_item->get_id() ) );

			if ( false === $result ) {
				throw new Exception( sprintf( esc_html__( 'Mix and Match Child Item deletion failed. Error: %s', 'woocommerce-mix-and-match-products' ), $wpdb->last_error ) );
			}

			do_action( 'wc_mnm_delete_child_item', $child_item );
			$this->clear_caches( $child_item );
		}

	}

	/**
	 * Get container ID by child item ID.
	 *
	 * @param  int $child_item_id Child Item ID.
	 * @return int
	 */
	public function get_container_id_by_child_item_id( $child_item_id ) {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT container_id FROM {$wpdb->prefix}wc_mnm_child_items WHERE child_item_id = %d",
				$child_item_id
			)
		);
	}

	/**
	 * Clear cache.
	 *
	 * @param WC_MNM_Child_Item $child_item child item object.
	 */
	protected function clear_caches( $child_item ) {
		wp_cache_delete( 'wc-mnm-child-item-' . $child_item->get_id(), 'wc-mnm-child-items' );

		$container_id = $child_item->get_container_id();
		if ( ! $container_id ) {
			$container_id = $this->get_container_id_by_child_item_id( $child_item->get_id() );
		}
		if ( $container_id ) {
			wp_cache_delete( 'wc-mnm-child-items-' . $container_id, 'products' );
		}
	}

}
