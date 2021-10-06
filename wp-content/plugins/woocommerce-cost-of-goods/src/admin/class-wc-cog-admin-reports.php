<?php
/**
 * WooCommerce Cost of Goods
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Admin Reports Class
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Reports {


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// add reports to WC
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_reports' ) );

		// clear report transients when orders are updated
		add_action( 'woocommerce_delete_shop_order_transients', array( $this, 'clear_report_transients' ) );

		// calculate total valuation in batching
		add_action( 'wp_ajax_wc_cog_do_ajax_total_valuation', array( $this, 'calculate_total_valuation' ) );

		// calculate product valuation in batching
		add_action( 'wp_ajax_wc_cog_do_ajax_product_valuation', array( $this, 'calculate_product_valuation' ) );
	}


	/**
	 * Adds a 'Profit' tab with associated reports to the WC admin reports area,
	 * as well as inventory valuation reports under the 'Stock' tab
	 *
	 * @since 2.0.0
	 * @param array $core_reports
	 * @return array
	 */
	public function add_reports( $core_reports ) {

		$profit_reports = array(
			'profit' => array(
				'title'   => __( 'Profit', 'woocommerce-cost-of-goods' ),
				'reports' => array(
					'profit_by_date'     => array(
						'title'       => __( 'Profit by date', 'woocommerce-cost-of-goods' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
					'profit_by_product'  => array(
						'title'       => __( 'Profit by product', 'woocommerce-cost-of-goods' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
					'profit_by_category' => array(
						'title'       => __( 'Profit by category', 'woocommerce-cost-of-goods' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
				),
			),
		);

		$stock_reports = array(
			'product_valuation' => array(
				'title'       => __( 'Product Valuation', 'woocommerce-cost-of-goods' ),
				'description' => '',
				'hide_title'  => false,
				'function'    => array( $this, 'load_report' ),
			),
			'total_valuation' => array(
				'title'       => __( 'Total Valuation', 'woocommerce-cost-of-goods' ),
				'description' => __( 'Total valuation provides the value of all inventory within your store at both the cost of the good, as well as the total value of inventory at the retail price (regular price, or sale price if set). Stock count must be set to be included in this valuation.', 'woocommerce-cost-of-goods' ),
				'hide_title'  => false,
				'function'    => array( $this, 'load_report' ),
			),
		);

		// add Profit reports tab immediately after Orders
		$core_reports = Framework\SV_WC_Helper::array_insert_after( $core_reports, 'orders', $profit_reports );

		// add inventory valuation chart
		if ( isset( $core_reports['stock']['reports'] ) ) {
			$core_reports['stock']['reports'] = array_merge( $core_reports['stock']['reports'], $stock_reports );
		}

		return $core_reports;
	}


	/**
	 * Callback to load and output the given report
	 *
	 * @since 2.0.0
	 * @param string $name report name, as defined in the add_reports() array above
	 */
	public function load_report( $name ) {

		$name     = sanitize_title( $name );
		$filename = sprintf( 'class-wc-cog-admin-report-%s.php', str_replace( '_', '-', $name ) );

		// abstract class first
		require_once( wc_cog()->get_plugin_path() . '/src/admin/reports/abstract-wc-cog-admin-report.php' );

		// then report class
		$report = wc_cog()->load_class( "/src/admin/reports/$filename", 'WC_COG_Admin_Report_' . $name );

		$report->output_report();
	}


	/**
	 * Clear report transients when shop order transients are cleared, e.g. order
	 * update/save, etc. This is also called directly when an order line item cost
	 * is edited manually from the edit order screen.
	 *
	 * @since 2.0.0
	 */
	public function clear_report_transients() {

		foreach ( array( 'date', 'product', 'category' ) as $report ) {

			delete_transient( "wc_cog_admin_report_profit_by_{$report}" );
		}
	}


	/**
	 * Calculate total valuation in batching using ajax.
	 *
	 * @since 2.5.0
	 */
	public function calculate_total_valuation() {

		check_ajax_referer( 'wc-cog-total-valuation', 'security' );

		// if current user can not access WooCommerce, simply exit
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( - 1 );
		}

		$params = array(
			'offset' => isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0,
			'cost'   => isset( $_POST['cost'] ) ? (float) $_POST['cost'] : 0,
			'retail' => isset( $_POST['retail'] ) ? (float) $_POST['retail'] : 0,
			'status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'pending',
		);

		/**
		 * Products per page for batching process.
		 *
		 * @since 2.5.0
		 *
		 * @param int $products_per_page Products to be fetched for batching
		 */
		$products_per_page = apply_filters( 'wc_cost_of_goods_batching_products_per_page', 100 );

		// get products
		$product_ids = new \WP_Query( array(
			'post_type'      => array( 'product', 'product_variation' ),
			'fields'         => 'ids',
			'posts_per_page' => $products_per_page,
			'offset'         => $params['offset'] * $products_per_page,
			'post_status'    => array( 'publish', 'private' ),
			'meta_query'     => array(
				array(
					'key'   => '_manage_stock',
					'value' => 'yes',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'variable' ),
					'operator' => 'NOT IN',
				),
			),
		) );

		$total_cost     = $params['cost'];
		$total_retail   = $params['retail'];
		$found_products = 0;
		$total_products = $product_ids->found_posts;

		if ( ! empty( $product_ids->posts ) ) {

			$found_products = count( $product_ids->posts );

			foreach ( $product_ids->posts as $product_id ) {

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					continue;
				}

				$stock_qty = (int) $product->get_stock_quantity();
				$cost      = (float) \WC_COG_Product::get_cost( $product );
				$price     = (float) $product->get_price();

				$total_cost   += $cost * $stock_qty;
				$total_retail += $price * $stock_qty;
			}
		}

		$percentage = $products_per_page === $found_products && $total_products > 0 ? min( round( ( 100 * $products_per_page * ( $params['offset'] + 1 ) ) / $total_products ), 100 ) : 100;
		$status     = 100 === $percentage ? 'done' : $params['status'];

		$response = array(
			'percentage'  => number_format( $percentage, 2 ),
			'status'      => $status,
			'cost'        => $total_cost,
			'retail'      => $total_retail,
			'cost_html'   => wc_price( $total_cost ),
			'retail_html' => wc_price( $total_retail ),
			'offset'      => $params['offset'] + 1,
		);

		wp_send_json_success( $response );
	}


	/**
	 * Calculate product valuation in batching using ajax.
	 *
	 * @since 2.5.0
	 */
	public function calculate_product_valuation() {

		check_ajax_referer( 'wc-cog-product-valuation', 'security' );

		// if current user can not access WooCommerce, simply exit
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( - 1 );
		}

		$params = array(
			'offset' => isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0,
			'status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'pending',
		);

		/** This filter is documented above */
		$products_per_page = apply_filters( 'wc_cost_of_goods_batching_products_per_page', 100 );

		// get products
		$product_ids = new \WP_Query( array(
			'post_type'      => array( 'product', 'product_variation' ),
			'fields'         => 'ids',
			'posts_per_page' => $products_per_page,
			'offset'         => $params['offset'] * $products_per_page,
			'post_status'    => array( 'publish', 'private' ),
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'   => '_manage_stock',
					'value' => 'yes',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'variable' ),
					'operator' => 'NOT IN',
				),
			),
		) );

		$found_products = 0;
		$total_products = $product_ids->found_posts;
		$product_data = array();

		if ( ! empty( $product_ids->posts ) ) {

			$found_products = count( $product_ids->posts );
			$output         = fopen( 'php://output', 'w' );

			foreach ( $product_ids->posts as $product_id ) {

				$product           = wc_get_product( $product_id );
				$product_data_line = array();

				if ( ! $product ) {
					continue;
				}

				$product_sku = $product->get_sku();

				if ( ! empty( $product_sku ) ) {
					$product_name = $product_sku . ' - ' . $product->get_name();
				}  else {
					$product_name = $product->get_name();
				}

				// Get variation data.
				if ( $product->is_type( 'variation' ) ) {

					$product_name  .= ' ' . wp_kses_post( wc_get_formatted_variation( $product, true ) );
					$product_parent = get_the_title( $product->get_parent_id() );

				} else {

					$product_parent = '-';
				}

				$product_data_line[] = $product_name;
				$product_data_line[] = $product_parent;

				$stock_qty = (float) $product->get_stock_quantity();
				$cost      = (float) \WC_COG_Product::get_cost( $product );
				$price     = (float) $product->get_price();
				$currency  = html_entity_decode( get_woocommerce_currency_symbol() );

				$total_price = $currency . number_format_i18n( $price * $stock_qty, 2 );
				$total_cost  = $currency . number_format_i18n( $cost * $stock_qty, 2 );

				$product_data_line[] = $total_price;
				$product_data_line[] = $total_cost;

				$product_data_line[] = $product->get_stock_quantity();

				if ( $product->is_in_stock() ) {
					$stock_html = __( 'In stock', 'woocommerce-cost-of-goods' );
				} else {
					$stock_html = __( 'Out of stock', 'woocommerce-cost-of-goods' );
				}

				$product_data_line[] = esc_html( apply_filters( 'woocommerce_admin_stock_html', $stock_html, $product ) );

				ob_start();
				fputcsv( $output, $product_data_line );
				$product_data[] = ob_get_contents();
				ob_end_clean();
			}

			fclose( $output );
		}

		$percentage = $products_per_page === $found_products && $total_products > 0 ? absint( min( round( ( 100 * $products_per_page * ( $params['offset'] + 1 ) ) / $total_products ), 100 ) ) : 100;
		$status     = 100 === $percentage ? 'done' : $params['status'];

		$response = array(
			'percentage'   => number_format( $percentage, 2 ),
			'status'       => $status,
			'offset'       => $params['offset'] + 1,
			'product_data' => implode( $product_data ),
		);

		wp_send_json_success( $response );
	}


}
