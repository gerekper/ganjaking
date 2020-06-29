<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility with `woocommerce-customer-order-csv-export`.
 */
class WC_MS_Customer_Order_Csv_Export {

	/**
	 * Plugin reference.
	 *
	 * @var WC_Ship_Multiple
	 */
	private $wcms;

	/**
	 * Constructor
	 */
	public function __construct( WC_Ship_Multiple $wcms ) {
		$this->wcms = $wcms;

		add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'modify_column_headers' ), 10, 1 );

		add_filter( 'wc_customer_order_export_csv_order_line_item', array( $this, 'sv_wc_csv_export_add_cart_id_to_order_line_item' ), 10, 3 );
		add_filter( 'wc_customer_order_export_csv_order_row_one_row_per_item', array( $this, 'sv_wc_csv_export_add_package_multiple_address' ), 10, 2 );
	}

	/**
	 * Method for adding an additional header for S2MA.
	 *
	 * @param  array $column_headers
	 * @return array
	 *
	 * @since 3.6.0
	 */
	public function modify_column_headers( $column_headers ) {
		$column_headers['wcms'] = __( 'Multiple Shipping', 'wc_shipping_multiple_address' );
		return $column_headers;
	}

	/**
	 * Adds the line item's cart key to the line item data for use by the one row per item
	 * filter.
	 *
	 * @param array $line_item
	 * @param array $item
	 * @param \WC_Product $product
	 * @return array
	 *
	 * @since 3.6.11
	 */

	public function sv_wc_csv_export_add_cart_id_to_order_line_item( $line_item, $item, $product ) {
		$line_item['cart_key'] = wc_get_order_item_meta( $item->get_id(), '_wcms_cart_key', true );

		return $line_item;
	}

	/**
	 * Adds the corresponding package address to each line item.
	 *
	 * @param array $order_data
	 * @param array $item
	 * @return array
	 *
	 * @since 3.6.11
	 */
	public function sv_wc_csv_export_add_package_multiple_address( $order_data, $item ) {
		$order_id           = $order_data[ 'order_id' ];
		$shipping_addresses = get_post_meta( $order_id, '_shipping_addresses' );

		if ( empty( $shipping_addresses[0] ) ) {
			return $order_data;
		}

		$packages  = get_post_meta( $order_id, '_wcms_packages', true );

		$package = $this->find_package( $packages, $item[ 'cart_key' ] );

		$address = wcms_get_address( $package['destination'] );

		$address = implode( '|', array_map( function( $key, $value ) {
			return sprintf( '%s:%s', $key, $value );
		}, array_keys( $address ), $address ) );

		$order_data[ 'wcms' ] = $address;
		return $order_data;
	}

	/**
	 * Helper function to check the export format.
	 *
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator the generator instance
	 * @return bool - true if this is a one row per item format
	 *
	 * @since 3.6.0
	 */
	public function is_one_row( $csv_generator ) {
		$one_row_per_item = false;
		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
			// pre 4.0 compatibility
			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
		} elseif ( isset( $csv_generator->format_definition ) ) {
			// post 4.0 (requires 4.0.3+)
			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}
		return $one_row_per_item;
	}

	/**
	 * Finds the package with the corresponding cart key.
	 *
	 * @param array $packages
	 * @param string $cart_key
	 * @return array
	 *
	 * @since 3.6.11
	 */
	private function find_package( $packages, $cart_key ) {
		foreach( $packages as $package ) {
			if ( array_key_exists( $cart_key, $package[ 'contents' ] ) ) {
				return $package;
			}
		}

		return null;
	}
}
