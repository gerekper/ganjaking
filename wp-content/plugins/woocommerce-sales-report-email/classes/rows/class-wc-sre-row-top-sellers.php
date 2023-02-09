<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Top_Sellers extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'top-sellers', __( 'Top Sellers', 'woocommerce-sales-report-email' ) );
	}

	/**
	 * Prepare the data
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function prepare() {
		$orders_ids = wc_get_orders(
			array(
				'type'         => 'shop_order',
				'return'       => 'ids',
				'limit'        => -1,
				'status'       => array( 'completed', 'processing', 'on-hold' ),
				'date_created' => $this->get_date_range()->get_start_date()->getTimestamp() . '...' . $this->get_date_range()->get_end_date()->getTimestamp(),
			)
		);

		$top_sellers = array();

		foreach ( $orders_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			$items = $order->get_items();

			foreach ( $items as $item ) {
				$product_id = $item->get_product_id();

				if ( $item->get_variation_id() ) {
					$product_id .= '_' . $item->get_variation_id();
				}

				if ( ! isset( $top_sellers[ $product_id ] ) ) {
					$top_sellers[ $product_id ] = array(
						'name' => $item->get_name(),
						'qty'  => $item->get_quantity(),
					);
				} else {
					$top_sellers[ $product_id ]['qty'] += $item->get_quantity();
				}
			}
		}

		$value = 'n/a';

		if ( ! empty( $top_sellers ) ) {
			usort(
				$top_sellers,
				function ( $a, $b ) {
					if ( $a['qty'] === $b['qty'] ) {
						return 0;
					}
					return ( $a['qty'] < $b['qty'] ) ? 1 : -1;
				}
			);

			// Fill the $value var with products.
			$value       = '';
			$top_sellers = array_slice( $top_sellers, 0, 12 );

			foreach ( $top_sellers as $top_seller ) {
				$value .= esc_html( $top_seller['qty'] ) . 'x : ' . esc_html( $top_seller['name'] ) . '<br/>';
			}
		}

		$this->set_value( $value );
	}

}
