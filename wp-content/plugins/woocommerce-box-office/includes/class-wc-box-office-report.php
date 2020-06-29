<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Report {

	/**
	 * WC Admin report instance.
	 *
	 * @var WC_Admin_Report
	 */
	private $_report;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_reports_charts', array( $this, 'reports_tab' ) );
	}

	/**
	 * Add 'Ticket' tab in WC Reports.
	 *
	 * @param array $reports Tabs
	 */
	public function reports_tab( $reports ) {
		$reports['tickets'] = array(
			'title'  =>  __( 'Tickets', 'woocommerce-box-office' ),
			'charts' => array(
				array(
					'title'       => __( 'Sales by Product', 'woocommerce-box-office' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'sales_by_product' )
				),
			)
		);
		return $reports;
	}

	public function sales_by_product() {
		global $wpdb;

		$rows = array();
		foreach ( wc_box_office_get_all_ticket_products() as $product ) {
			$sold = $wpdb->get_var(
				$wpdb->prepare(
					"
					SELECT COUNT( DISTINCT( a.ID ) ) FROM $wpdb->posts a
					LEFT JOIN $wpdb->postmeta b ON a.ID = b.post_id
					WHERE
					a.post_type = %s AND
					a.post_status = %s AND
					b.meta_key = %s AND
					b.meta_value = %d
					",
					'event_ticket',
					'publish',
					'_product_id',
					$product->ID
				)
			);

			$product = wc_get_product( $product );
			if ( $product->is_in_stock() ) {
				$stock = '<mark class="instock">' . __( 'In stock', 'woocommerce-box-office' ) . '</mark>';
			} else {
				$stock = '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce-box-office' ) . '</mark>';
			}
			if ( $product->managing_stock() ) {
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) { 
					$stock .= ' &times; ' . $product->get_total_stock();
				} else {
					if ( sizeof( $product->get_children() ) > 0 ) {
						$total_stock = max( 0, $product->get_stock_quantity() );

						foreach ( $product->get_children() as $child_id ) {
							if ( 'yes' === get_post_meta( $child_id, '_manage_stock', true ) ) {
								$stock = get_post_meta( $child_id, '_stock', true );
								$total_stock += max( 0, wc_stock_amount( $stock ) );
							}
						}
					} else {
						$total_stock = $product->get_stock_quantity();
					}

					$stock .= ' &times; ' . wc_stock_amount( $total_stock );
				}
			}

			$total_sales = $this->get_product_total_sales( $product->get_id() );

			$rows[] = array(
				'product_title' => $product->get_title(),
				'product_link'  => admin_url( sprintf( 'post.php?post=%s&action=edit', $product->get_id() ) ),
				'sold'          => absint( $sold ),
				'stock'         => $stock,
				'price'         => $product->get_price_html() ? $product->get_price_html() : '<span class="na">&ndash;</span>',
				'total_sales'   => $total_sales,
			);
		}

		require_once( WCBO()->dir . 'includes/views/admin/report-sales-by-product.php' );
	}

	public function get_product_total_sales( $product_id ) {
		if ( ! $this->_report ) {
			require_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
			$this->_report = new WC_Admin_Report();
		}

		return $this->_report->get_order_report_data( array(
			'data' => array(
				'_line_total' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_amount'
				)
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
					'type'       => 'order_item_meta',
					'meta_key'   => array( '_product_id', '_variation_id' ),
					'meta_value' => $product_id,
					'operator'   => '=',
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => false,
		) );
	}
}
