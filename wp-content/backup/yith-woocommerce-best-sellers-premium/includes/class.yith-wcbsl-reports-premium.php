<?php

/**
 * Reports class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBSL' ) ) {
	exit;
} // Exit if accessed directly

require_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

if ( ! class_exists( 'YITH_WCBSL_Reports_Premium' ) ) {
	/**
	 * Reports class.
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	class YITH_WCBSL_Reports_Premium extends WC_Admin_Report {

		/**
		 * @var bool
		 * @since 1.1.17
		 */
		private static $doing_query = false;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
		}

		/**
		 * Set doing query
		 *
		 * @param bool $doing
		 * @since 1.1.17
		 */
		private static function set_doing_query( $doing ) {
			self::$doing_query = ! ! $doing;
		}

		/**
		 * is doing a query?
		 *
		 * @return bool
		 * @since 1.1.17
		 */
		public static function is_doing_query() {
			return self::$doing_query;
		}

		/**
		 * Check if a product is a Bestseller
		 *
		 * @access public
		 *
		 * @param $prod_id int id of product
		 * @param $range   string the range of bestseller search
		 * @param $args    array args passed to get_best_sellers
		 *
		 * @return bool
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function check_is_bestseller( $prod_id, $range = 'ever', $args = array() ) {
			$bestsellers = $this->get_best_sellers( $range, $args );
			$prod_id     = YITH_WCBSL_WPML_Integration()->get_parent_id( $prod_id );

			$bs_in = array();

			//add best sellers in categories
			$cats = get_the_terms( $prod_id, 'product_cat' );

			$range_args = isset( $args['range_args'] ) ? $args['range_args'] : array();
			$bs_in_cat  = array();
			if ( ! empty( $cats ) ) {
				foreach ( $cats as $cat ) {
					$bs_in_cat_tmp['bs']     = $this->get_best_sellers_in_category( $cat->term_id, $range, $range_args, $args );
					$bs_in_cat_tmp['name']   = $cat->name;
					$bs_in_cat_tmp['cat_id'] = $cat->term_id;
					$bs_in_cat[]             = $bs_in_cat_tmp;
					//$bestsellers = array_merge( $bestsellers, $bs_in_cat_tmp );
				}
			}

			if ( ! empty( $bestsellers ) ) {
				$position = 0;
				foreach ( $bestsellers as $bestseller ) {
					$position ++;
					if ( $bestseller->product_id == $prod_id ) {
						$tmp_array = array(
							'title'    => 'yith_wcbsl_all',
							'position' => $position,
							'cat_id'   => '',
						);
						$bs_in[]   = $tmp_array;
						break;
					}
				}
			}

			if ( ! empty( $bs_in_cat ) ) {
				foreach ( $bs_in_cat as $bs ) {
					$position = 0;
					foreach ( $bs['bs'] as $b ) {
						$position ++;
						if ( $b->product_id == $prod_id ) {
							$tmp_array = array(
								'title'    => $bs['name'],
								'position' => $position,
								'cat_id'   => $bs['cat_id'],
							);
							$bs_in[]   = $tmp_array;
							break;
						}
					}
				}
			}

			return ! empty( $bs_in ) ? $bs_in : false;
		}

		public function get_newest_bestsellers( $range = 'ever', $range_args = array(), $limit = false ) {
			if ( false === $limit ) {
				$limit = YITH_WCBSL()->get_limit();
			}
			$all_best_sellers = $this->get_best_sellers( $range, array( 'range_args' => $range_args, 'limit' => - 1 ) );

			$all_best_sellers_array = array();
			if ( ! empty( $all_best_sellers ) ) {
				foreach ( $all_best_sellers as $bs ) {
					$all_best_sellers_array[ $bs->product_id ] = $bs;
				}
			}

			$args = array(
				'posts_per_page' => - 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'fields'         => 'ids',
			);

			$product_ids        = get_posts( $args );
			$newest_bestsellers = array();

			if ( ! empty( $product_ids ) ) {
				$loop = 0;
				foreach ( $product_ids as $product_id ) {
					if ( isset( $all_best_sellers_array[ $product_id ] ) ) {
						$newest_bestsellers[] = $all_best_sellers_array[ $product_id ];
						$loop ++;
					}
					if ( $loop >= $limit && $limit > 0 ) {
						break;
					}
				}
			}

			return $newest_bestsellers;
		}

		/**
		 * Get all product ids in a category (and its children)
		 *
		 * @param mixed int | array $category_id
		 *
		 * @return array
		 */
		public function get_products_in_category( $category_id ) {
			$term_ids = array();

			if ( is_array( $category_id ) ) {
				foreach ( $category_id as $c_id ) {
					$this_term_id   = get_term_children( $c_id, 'product_cat' );
					$this_term_id[] = $c_id;
					$term_ids       = array_merge( $term_ids, $this_term_id );
				}
				$term_ids = array_unique( $term_ids );
			} else {
				$term_ids   = get_term_children( $category_id, 'product_cat' );
				$term_ids[] = $category_id;
			}

			$product_ids = get_objects_in_term( $term_ids, 'product_cat' );

			return array_unique( $product_ids );
		}


		public function get_best_sellers_in_category( $category_id, $range = 'ever', $range_args = array(), $args = array() ) {
			$all_best_sellers         = $this->get_best_sellers( $range, array( 'range_args' => $range_args, 'limit' => - 1 ) );
			$all_products_in_category = $this->get_products_in_category( $category_id );

			$best_sellers_in_category = array();
			foreach ( $all_best_sellers as $bestseller ) {
				if ( in_array( $bestseller->product_id, $all_products_in_category ) ) {
					$best_sellers_in_category[] = $bestseller;
				}
			}

			$limit = isset( $args['limit'] ) ? $args['limit'] : YITH_WCBSL()->get_limit();

			// return only the first $limit bestsellers
			return $limit > 0 ? array_slice( $best_sellers_in_category, 0, $limit ) : $best_sellers_in_category;
		}

		/**
		 * Get the best sellers
		 *
		 * @param string $range range of date
		 * @param array  $args
		 *
		 * @return array
		 */
		public function get_best_sellers( $range = 'ever', $args = array() ) {
			self::set_doing_query( true );
			$filter_range = false;
			if ( $range != 'ever' ) {
				$range_args = isset( $args['range_args'] ) ? $args['range_args'] : array();
				$this->calculate_current_range( $range, $range_args );
				$filter_range = true;
			}

			$limit = isset( $args['limit'] ) ? $args['limit'] : YITH_WCBSL()->get_limit();

			$order_report_data_array = array(
				'data'         => array(
					'_product_id' => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'product_id',
					),
					'_qty'        => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => 'SUM',
						'name'            => 'order_item_qty',
					),
				),
				'where_meta'   => array(
					array(
						'type'       => 'order_item_meta',
						'meta_key'   => '_line_subtotal',
						'meta_value' => '0',
						'operator'   => '>',
					),
				),
				'order_by'     => 'order_item_qty DESC',
				'group_by'     => 'product_id',
				'limit'        => $limit,
				'query_type'   => 'get_results',
				'filter_range' => $filter_range,
				'order_types'  => wc_get_order_types( 'order-count' ),
				//'debug'        => true,
			);

			if ( $limit == - 1 ) {
				unset( $order_report_data_array['limit'] );
			}

			// create the unique transient name for this request
			//$transient_name = strtolower( get_class( $this ) ) . '_' . $range;
			$transient_name = 'yith_wcbsl_' . $range;
			if ( ! empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					if ( is_array( $value ) ) {
						//$transient_name .= '_' . strtolower( $key );
					} else {
						$transient_name .= '_' . strtolower( $value );
					}
				}
			}

			$best_sellers = get_transient( $transient_name );

			$is_debug = defined( 'YITH_WCBSL_DEBUG' ) ? YITH_WCBSL_DEBUG : false;

			if ( ! $best_sellers || $is_debug ) {
				// delete transient
				delete_transient( strtolower( get_class( $this ) ) );
				// get data
				$best_sellers = $this->get_order_report_data( $order_report_data_array );

				// Filter Existing products
				foreach ( $best_sellers as $key => $bs_product ) {
					$product = wc_get_product( absint( $bs_product->product_id ) );
					if ( ! $product || apply_filters( 'yith_wcbs_remove_best_seller', false, $product, $bs_product ) ) {
						unset( $best_sellers[ $key ] );
					}
				}

				$best_sellers = apply_filters( 'yith_wcbs_get_best_sellers', $best_sellers, $range, $args );

				// set the transient, with expiration one hour
				set_transient( $transient_name, $best_sellers, 3600 );
			}

			self::set_doing_query( false );

			return $best_sellers;
		}


		/**
		 * Get the current range and calculate the start and end dates
		 *
		 * @param string $current_range
		 */
		public function calculate_current_range( $current_range, $args = array() ) {

			switch ( $current_range ) {

				case 'yith_custom' :
					if ( isset( $args['start_date'] ) && isset( $args['end_date'] ) ) {
						$this->start_date = strtotime( sanitize_text_field( $args['start_date'] ) );
						$this->end_date   = strtotime( 'midnight', strtotime( sanitize_text_field( $args['end_date'] ) ) );
					}

					if ( ! $this->end_date ) {
						$this->end_date = current_time( 'timestamp' );
					}

					$interval = 0;
					$min_date = $this->start_date;

					while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
						$interval ++;
					}

					// 3 months max for day view
					if ( $interval > 3 ) {
						$this->chart_groupby = 'month';
					} else {
						$this->chart_groupby = 'day';
					}
					break;

				case 'custom' :
					$this->start_date = strtotime( sanitize_text_field( $_GET['start_date'] ) );
					$this->end_date   = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['end_date'] ) ) );

					if ( ! $this->end_date ) {
						$this->end_date = current_time( 'timestamp' );
					}

					$interval = 0;
					$min_date = $this->start_date;

					while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
						$interval ++;
					}

					// 3 months max for day view
					if ( $interval > 3 ) {
						$this->chart_groupby = 'month';
					} else {
						$this->chart_groupby = 'day';
					}
					break;

				case 'last_year' :
					$this->start_date    = strtotime( date( 'Y-01-01', strtotime( '-1 YEAR', current_time( 'timestamp' ) ) ) );
					$this->end_date      = strtotime( 'midnight', strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) ) );
					$this->chart_groupby = 'month';
					break;

				case 'year' :
					$this->start_date    = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'month';
					break;

				case 'last_month' :
					$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
					$this->start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
					$this->end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
					$this->chart_groupby     = 'day';
					break;

				case 'month' :
					$this->start_date    = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case 'last_yesterday':
					$this->start_date    = strtotime( '-2 days midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( '-2 days midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case 'yesterday':
				case 'last_today':
					$this->start_date    = strtotime( '-1 DAY midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( '-1 DAY midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case 'today':
					$this->start_date    = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case 'last_7day' :
					$this->start_date    = strtotime( '-13 days', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( '-7 days midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case '7day' :
					$this->start_date    = strtotime( '-6 days midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case '3day' :
					$this->start_date    = strtotime( '-2 days midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;

				case '2day' :
					$this->start_date    = strtotime( '-1 days midnight', current_time( 'timestamp' ) );
					$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
					$this->chart_groupby = 'day';
					break;
			}

			// Group by
			switch ( $this->chart_groupby ) {

				case 'day' :
					$this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';
					$this->chart_interval = ceil( max( 0, ( $this->end_date - $this->start_date ) / ( 60 * 60 * 24 ) ) );
					$this->barwidth       = 60 * 60 * 24 * 1000;
					break;

				case 'month' :
					$this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date)';
					$this->chart_interval = 0;
					$min_date             = $this->start_date;

					while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
						$this->chart_interval ++;
					}

					$this->barwidth = 60 * 60 * 24 * 7 * 4 * 1000;
					break;
			}
		}

	}
}