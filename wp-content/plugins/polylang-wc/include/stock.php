<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the synchronization of the stock between translations of the same product.
 *
 * @since 0.1
 */
class PLLWC_Stock {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		add_filter( 'woocommerce_update_product_stock_query', array( $this, 'update_product_stock_query' ), 10, 2 ); // Since WC 3.6.
		add_action( 'woocommerce_updated_product_stock', array( $this, 'updated_product_stock' ) ); // Since WC 3.6.

		add_action( 'woocommerce_product_set_stock_status', array( $this, 'set_stock_status' ), 10, 2 );
		add_action( 'woocommerce_variation_set_stock_status', array( $this, 'set_stock_status' ), 10, 2 );

		add_filter( 'woocommerce_query_for_reserved_stock', array( $this, 'query_for_reserved_stock' ), 10, 3 );
	}

	/**
	 * Synchronize the stock across the product translations.
	 *
	 * @since 1.2
	 *
	 * @param string $sql        SQL query used to update the product stock.
	 * @param int    $product_id Product id.
	 * @return string Modified SQL query.
	 */
	public function update_product_stock_query( $sql, $product_id ) {
		global $wpdb;

		$tr_ids = $this->data_store->get_translations( $product_id );

		return $sql = str_replace(
			$wpdb->prepare( 'post_id = %d', $product_id ),
			sprintf( 'post_id IN ( %s )', implode( ',', array_map( 'absint', $tr_ids ) ) ),
			$sql
		);
	}

	/**
	 * Deletes the cache and updates the stock status for all the translations.
	 *
	 * @since 1.2
	 *
	 * @param int $id Product id.
	 * @return void
	 */
	public function updated_product_stock( $id ) {
		foreach ( $this->data_store->get_translations( $id )  as $tr_id ) {
			if ( $tr_id !== $id && $product = wc_get_product( $tr_id ) ) {
				$product_id_with_stock = $product->get_stock_managed_by_id();

				wp_cache_delete( $product_id_with_stock, 'post_meta' );
				$this->data_store->update_lookup_table( $tr_id, 'wc_product_meta_lookup' );
			}
		}
	}

	/**
	 * Synchronizes the stock status across the product translations.
	 *
	 * @since 1.1
	 *
	 * @param int    $id     Product id.
	 * @param string $status Stock status.
	 * @return void
	 */
	public function set_stock_status( $id, $status ) {
		static $avoid_recursion = array();

		// To avoid recursion, we make sure that the couple product id + stock status is set only once.
		if ( empty( $avoid_recursion[ $id ][ $status ] ) ) {
			$tr_ids = $this->data_store->get_translations( $id );

			foreach ( $tr_ids as $tr_id ) {
				if ( $tr_id !== $id ) {
					$avoid_recursion[ $id ][ $status ] = true;
					wc_update_product_stock_status( $tr_id, $status );
				}
			}
		}
	}

	/**
	 * Synchronizes reserve_stock_for_product accross translations
	 *
	 * @since 1.5
	 *
	 * @param string $query            The query for getting reserved stock of a product.
	 * @param int    $product_id       Product ID.
	 * @param int    $exclude_order_id Order to exclude from the results.
	 * @return string
	 */
	public function query_for_reserved_stock( $query, $product_id, $exclude_order_id ) {
		global $wpdb;

		$product_ids = $this->data_store->get_translations( $product_id );

		if ( empty( $product_ids ) ) {
			return $query;
		}

		return sprintf(
			"
			SELECT COALESCE( SUM( stock_table.`stock_quantity` ), 0 ) FROM $wpdb->wc_reserved_stock stock_table
			LEFT JOIN $wpdb->posts posts ON stock_table.`order_id` = posts.ID
			WHERE posts.post_status IN ( 'wc-checkout-draft', 'wc-pending' )
			AND stock_table.`expires` > NOW()
			AND stock_table.`product_id` IN ( %s )
			AND stock_table.`order_id` != %d
			",
			implode( ',', array_map( 'intval', $product_ids ) ),
			(int) $exclude_order_id
		);
	}
}
