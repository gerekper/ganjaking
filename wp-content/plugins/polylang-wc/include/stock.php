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

		add_filter( 'woocommerce_query_for_reserved_stock', array( $this, 'query_for_reserved_stock' ), 10, 2 );
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
	 * Synchronizes reserve_stock_for_product accross translations
	 *
	 * @since 1.5
	 * @since 1.8 Removed the 3rd parameter.
	 *
	 * @param string $query      The query to get the reserved stock of a product.
	 * @param int    $product_id Product ID.
	 * @return string
	 */
	public function query_for_reserved_stock( $query, $product_id ) {
		global $wpdb;

		$product_ids = $this->data_store->get_translations( $product_id );

		if ( empty( array_diff( $product_ids, array( $product_id ) ) ) ) {
			// No other translations.
			return $query;
		}

		return str_replace(
			$wpdb->prepare( 'AND stock_table.`product_id` = %d', $product_id ),
			$wpdb->prepare(
				sprintf(
					'AND stock_table.`product_id` IN ( %s )',
					implode( ', ', array_fill( 0, count( $product_ids ), '%d' ) )
				),
				array_map( 'intval', $product_ids )
			),
			$query
		);
	}
}
