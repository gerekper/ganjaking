<?php

/**
 * Admin Dashboard Reports
 *
 * Functions used for displaying sales and customer reports in admin.
 *
 * @author      OPMC
 * @category    Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . '/wp-content/plugins/woocommerce/includes/admin/reports/class-wc-admin-report.php');

if ( ! class_exists( 'WC_Dropshipping_Dashboard', false ) ) :

	class WC_Dropshipping_Dashboard {

		private $wcd_query;

		private $wcd_count_listings;

		public function __construct() {
			$this->init();
		}

		public function init() {
			add_action( 'admin_init', array( $this, 'custom_dashboard_style' ) );
		}

		public function custom_dashboard_style() {
			$base_name = explode( '/', plugin_basename( __FILE__ ) );
			wp_register_script( 'add_dropshipping_chart_lib', 	plugins_url(). '/' . $base_name[0] . '/lib/js/chart.js', array(), '2.8.0' );
			wp_register_style( 'add_custom_dashboard_style', 	plugins_url(). '/' . $base_name[0] . '/assets/css/dashboard.css', array(), '1.0.0'  );
			wp_register_script( 'add_custom_dashboard_script', 	plugins_url(). '/' . $base_name[0] . '/assets/js/dashboard.js', array(), '1.0.0'  );
		}

		private function get_all_prod_args() {
			$args = array(
					'post_type'	=> 'product',
					'posts_per_page' => -1,
					'post_status'	=>	'publish',
					'fields' => 'ids'
			);
			return $args;
		}

		private function get_prod_args_with_supplier() {
			$args = array(
					'post_type'	=> 'product',
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
						    'key' => 'supplierid',
						    'compare' => '>',
						    'value' => 0,
						)
					),
					'post_status'	=>	'publish',
					'fields' => 'ids'
			);
			return $args;
		}

		private function get_order_args() {
			$args = array(
					'return' => 'ids',
					'numberposts' => -1
			);
			return $args;
		}

		private function get_dropship_taxo() {
			$args = array(
				'taxonomy' => 'dropship_supplier',
			);

			return $args;
		}

		private function get_current_currency() {
			if ( false !== function_exists( 'get_woocommerce_currency' ) ) {
				return get_woocommerce_currency_symbol();
			}
		}

		public function get_prod_ids() {
			$product_ids = get_posts( $this->get_prod_args_with_supplier() );
			return $product_ids;
		}

		public function count_prod_listings() {
			$products = new WP_Query( $this->get_prod_args_with_supplier() );
			return $products->found_posts;
		}

		public function count_prod_out_stock() {
			$count = 0;
			$product_ids = $this->get_prod_ids();
			foreach( $product_ids as $product_id ) {
				$stock_status = get_post_meta( $product_id, '_stock_status', true );
				if ( 'outofstock' == $stock_status ) {
					$count++;
				}
			}
			return $count;
		}

		public function count_orders() {
			$order_with_supp = array();
			$order_ids = wc_get_orders( $this->get_order_args() );
			$supplier_ids = get_terms( $this->get_dropship_taxo() );
			$total_sales = 0;
			$count_orders = 0;
			$total_profit = 0;
			$projected_profit = 0;

			foreach( $order_ids as $order_id ) {
				foreach( $supplier_ids as $supplier_id ) {
					$supplier_name = get_post_meta( $order_id, 'supplier_'.$supplier_id->term_id );
					if ( !empty( $supplier_name ) ) {
						array_push( $order_with_supp, $order_id );
					}
				}
			}

			$order_with_supp = array_unique( $order_with_supp );

			foreach( $order_with_supp as $order ) {
				$order_total = wc_get_order( $order );
				$total_sales = $order_total->get_total();
				$cost_of_goods = get_post_meta( $order, 'cost_of_goods_total', true );
				$total_tax_price = $order_total->get_total_tax();
				$total_shipping_price = $order_total->get_shipping_total();
				if ( isset( $cost_of_goods ) && is_numeric( $cost_of_goods ) ) {
					$total_profit = abs( $total_sales - $cost_of_goods );
				}
				if ( isset( $total_tax_price ) && is_numeric( $total_tax_price ) ) {
					$total_profit = abs( $total_profit - $total_tax_price );
				}
				if ( isset( $total_shipping_price ) && is_numeric( $total_shipping_price ) ) {
					$total_profit = abs( $total_profit - $total_shipping_price );
				}
				$projected_profit = $projected_profit + $total_profit;
			}

			$count_orders = count( $order_with_supp );

			return array( $count_orders, $projected_profit, $order_with_supp );

		}

		public function get_currency() {
			$currency = $this->get_current_currency();
			return $currency;
		}

		public function get_ali_orders() {
			$order_with_supp = array();
			$order_ids = wc_get_orders( $this->get_order_args() );
			$total_sales = 0;
			$count_orders = 0;

			foreach( $order_ids as $order_id ) {
				$is_ali = get_post_meta( $order_id, 'status_of_aliexpress' );
				if ( !empty( $is_ali ) ) {
					array_push( $order_with_supp, $order_id );
				}
			}

			foreach( $order_with_supp as $order ) {
				$order_total = wc_get_order( $order );
				$total_sales = $total_sales + $order_total->get_total();
			}

			$count_orders = count( array_unique( $order_with_supp ) );

			return array( $count_orders, $total_sales );
		}

		public function get_inprogress_orders() {
			$args = $this->get_order_args();
			$args['post_status'] = 'wc-processing';
			$orders = wc_get_orders( $args );

			return $orders;
		}

		public function get_completed_orders() {
			$args = $this->get_order_args();
			$args['post_status'] = 'completed';
			$orders = wc_get_orders( $args );

			return count( $orders );
		}

		public function get_completed_dropship_orders() {
			$args = $this->get_order_args();
			$args['post_status'] = 'completed';
			$args['fields'] = 'ids';
			$orders = wc_get_orders( $args );

			$order_with_supp = array();
			$supplier_ids = get_terms( $this->get_dropship_taxo() );

			foreach( $orders as $order_id ) {
				foreach( $supplier_ids as $supplier_id ) {
					$supplier_name = get_post_meta( $order_id, 'supplier_'.$supplier_id->term_id );
					if ( !empty( $supplier_name ) ) {
						array_push( $order_with_supp, $order_id );
					}
				}
			}

			if ( isset( $order_with_supp ) && is_array( $order_with_supp ) ) {
				array_unique( $order_with_supp );
			}

			return count( $order_with_supp );
		}

		public function get_pending_orders() {
			$args = $this->get_order_args();
			$args['post_status'] = array( 'wc-pending', 'wc-on-hold' );
			$orders = wc_get_orders( $args );

			return count( $orders );
		}

		public function get_best_selling_prod() {
			$limit = 0;
			$prod_orders_count = array();
			$product_ids = $this->get_prod_ids();
			foreach( $product_ids as $product_id ) {
				$total_prod_orders = get_post_meta( $product_id, 'total_sales', true );
				$product = wc_get_product( $product_id );
				$product_thumb = $product->get_image( 'thumb' );
				$product_name = $product->get_name();
				$product_url = get_permalink( $product_id );
				$prod_orders_count[ $product_id ] = array( intval( $total_prod_orders ), $product_name, $product_thumb, $product_url );
				$limit++;
				if ( $limit == 5 ) {
					break;
				}
			}
			arsort( $prod_orders_count );
			return $prod_orders_count;
		}

		public function get_low_on_stocks_prod() {
			$limit = 0;
			$prod_orders_stocks_count = array();
			$product_ids = $this->get_prod_ids();
			foreach( $product_ids as $product_id ) {
				$total_prod_stocks = get_post_meta( $product_id, '_stock', true );
				$total_prod_stocks_status = get_post_meta( $product_id, '_stock_status', true );
				if ( empty( $total_prod_stocks ) ) {
					if ( 'outofstock' == $total_prod_stocks_status ) {
						$total_prod_stocks = 0;
					} else {
						continue;
					}
				}
				$product = wc_get_product( $product_id );
				$product_thumb = $product->get_image( 'thumb' );
				$product_name = $product->get_name();
				$product_url = get_permalink( $product_id );
				$prod_orders_stocks_count[ $product_id ] = array( intval( $total_prod_stocks ) , $product_name, $product_thumb, $product_url );
				$limit++;
				if ( $limit == 5 ) {
					break;
				}
			}
			array_multisort( $prod_orders_stocks_count );
			return $prod_orders_stocks_count;
		}

		public function get_affiliate_prod() {
			$prod_ids = get_posts( $this->get_all_prod_args() );
			$order_with_aff = array();

			foreach( $prod_ids as $prod_id ) {
				$affiliate_id = get_post_meta( $prod_id, 'product_custom_field_amazon_product_id', true );
				if ( '' !== $affiliate_id ) {
					array_push( $order_with_aff, $prod_id );
				}
			}

			$count_orders = count( array_unique( $order_with_aff ) );

			return $count_orders;
		}

		public function get_week_total_sales() {
			$current_num_days = 7;
			$order_ids = $this->count_orders();
			$order_ids = $order_ids[2];
			$current_date = date( 'd' );
			$current_month = date( 'm' );
			$current_year = date( 'y' );
			$seven_last_days = array();
			$last_month = $current_month - 1;
			$last_month = ( 0 == $last_month ) ? 12 : $last_month;
			$last_month = sprintf("%02d", $last_month);
			$days_last_month = cal_days_in_month( CAL_GREGORIAN, $last_month, $current_year );
			$seven_last_order_ids = array();
			$order_date = array();
			$order_per_day_total = array();
			$last_day_orders_data = array();
			$active_months = array();
			$profit = array();
			for( $i = 0; $i < $current_num_days; ++$i ) {
				if ( $current_date == 1 ) {
					array_push( $seven_last_days, intval( $current_date ) );
					array_push( $active_months, intval( $current_month ) );
					$current_date = $days_last_month;
					$current_month = $last_month;
					continue;
				}
				if ( $current_date == $days_last_month ) {
					array_push( $seven_last_days, intval( $current_date ) );
					array_push( $active_months, intval( $current_month ) );
					$current_date--;
					continue;
				}

				array_push( $seven_last_days, intval( $current_date ) );
				array_push( $active_months, intval( $current_month ) );
				$current_date--;
			}

			$order_per_day_total['last7days'] = $seven_last_days;
			$order_per_day_total['active_months'] = $active_months;

			foreach( $order_ids as $order_id ) {
				$new_current_month = date( 'm' );
				$date_paid = get_post_meta( $order_id, '_date_paid', true );
				$date_paid = intval( $date_paid );
				$date_paid_year = intval( gmdate( 'y', $date_paid ) );
				$date_paid_month = intval( gmdate( 'm', $date_paid ) );
				$date_paid_date = intval( gmdate( 'd', $date_paid ) );
				if ( $current_year == $date_paid_year ) {
					if ( $new_current_month == $date_paid_month || $last_month == $date_paid_month ) {
						if( in_array( $date_paid_date, $seven_last_days, false ) ) {
							if ( empty( $order_date[ $date_paid_date ] ) ) {
								$order_date[ $date_paid_date ] = array();
							}
							array_push( $order_date[$date_paid_date], $order_id );
							array_push( $seven_last_order_ids, $order_date );
						}
					}
				}
			}
			if ( empty( $seven_last_order_ids ) ) {
				return;
			}

			if ( function_exists( 'array_key_last' ) ) {
				$last_key = array_key_last( $seven_last_order_ids );
			} else {
				end($seven_last_order_ids);
				$last_key = key($seven_last_order_ids);
			}

			$seven_last_order_ids = $seven_last_order_ids[ $last_key ];

			foreach ( $seven_last_order_ids as $current_date => $current_day_order_id ) {
				$total_sales_per_day = 0;
				$cost_of_goods_total = 0;
				$total_tax = 0;
				$total_shipping = 0;
				$total_profit = 0;
				foreach ( $current_day_order_id as $key => $value ) {
					$cost_of_goods = get_post_meta( $value, 'cost_of_goods_total', true );
					if ( empty( $cost_of_goods ) ) {
						$cost_of_goods = 0;
					}
					$order_total = wc_get_order( $value );
					$total_sales_per_day = $total_sales_per_day + $order_total->get_total();
					$total_tax_price = $order_total->get_total_tax();
					$total_shipping_price = $order_total->get_shipping_total();
					$cost_of_goods_total = $cost_of_goods_total + $cost_of_goods;
					$total_tax = $total_tax + $total_tax_price;
					$total_shipping = $total_shipping + $total_shipping_price;
				}
				$last_day_orders_data[ $current_date ] = $total_sales_per_day;
				$profit[ $current_date ] = abs( $total_sales_per_day - ( $cost_of_goods_total + $total_shipping + $total_tax ) );
				if ( $last_day_orders_data[ $current_date ] == $profit[ $current_date ] ) {
					$profit[ $current_date ] = 0;
				}
			}

			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders', $order_per_day_total );
			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders_data', $last_day_orders_data );
			wp_localize_script( 'add_custom_dashboard_script', 'profit', $profit );

			$order_per_day_total[ 'currency' ] = $this->get_currency();
			if ( isset( $profit ) && is_array( $profit ) ) {
				$profit = array_sum( $profit );
			}
			$order_per_day_total[ 'profit' ] = $profit;

			return $order_per_day_total;

		}

	}

endif;

 ?>
