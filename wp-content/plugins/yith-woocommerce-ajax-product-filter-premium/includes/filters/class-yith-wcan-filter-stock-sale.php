<?php
/**
 * Stock/Sale filter class
 *
 * Offers method specific to Ajax Order By filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Filters
 * @version 4.16.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Filter_Stock_Sale' ) ) {
	/**
	 * Stock/Sale Filter Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Filter_Stock_Sale extends YITH_WCAN_Filter {

		/**
		 * Filter type
		 *
		 * @var string
		 */
		protected $type = 'stock_sale';

		/**
		 * Checks if filter is relevant to current product selection
		 *
		 * @return bool Whether filter is relevant or not.
		 */
		public function is_relevant() {
			return apply_filters( 'yith_wcan_is_filter_relevant', $this->is_enabled() && $this->has_relevant_filters(), $this );
		}

		/**
		 * Render count for on sale item
		 *
		 * @return string Count template
		 */
		public function render_on_sale_count() {
			$count = $this->get_on_sale_count();

			return $this->render_count( $count );
		}

		/**
		 * Render count for in stock item
		 *
		 * @return string Count template
		 */
		public function render_in_stock_count() {
			$count = $this->get_in_stock_count();

			return $this->render_count( $count );
		}

		/**
		 * Render count for featured item
		 *
		 * @return string Count template
		 */
		public function render_featured_count() {
			$count = $this->get_featured_count();

			return $this->render_count( $count );
		}

		/* === IN STOCK/ON SALE METHODS === */

		/**
		 * Checks whether in stock/on sale filters are relevant to current query or not
		 *
		 * @return bool Result of the test.
		 */
		public function has_relevant_filters() {
			return $this->is_sale_filter_relevant() || $this->is_stock_filter_relevant() || $this->is_featured_filter_relevant();
		}

		/**
		 * Checks whether on sale filter is relevant to current query or not
		 *
		 * @return bool Result of the test.
		 */
		public function is_sale_filter_relevant() {
			return $this->show_sale_filter() && ( $this->get_on_sale_count() || 'hide' !== $this->get_adoptive() );
		}

		/**
		 * Checks whether in stock filter is relevant to current query or not
		 *
		 * @return bool Result of the test.
		 */
		public function is_stock_filter_relevant() {
			return $this->show_stock_filter() && ( $this->get_in_stock_count() || 'hide' !== $this->get_adoptive() );
		}

		/**
		 * Checks whether featured filter is relevant to current query or not
		 *
		 * @return bool Result of the test.
		 */
		public function is_featured_filter_relevant() {
			return $this->show_featured_filter() && ( $this->get_featured_count() || 'hide' !== $this->get_adoptive() );
		}

		/**
		 * Checks whether on sale filter is active for current query
		 *
		 * @return bool Whether on sale filter is currently active
		 */
		public function is_on_sale_active() {
			return YITH_WCAN_Query()->is_sale_only();
		}

		/**
		 * Checks whether in stock filter is active for current query
		 *
		 * @return bool Whether in stock filter is currently active
		 */
		public function is_in_stock_active() {
			return YITH_WCAN_Query()->is_stock_only();
		}

		/**
		 * Checks whether featured filter is active for current query
		 *
		 * @return bool Whether featured filter is currently active
		 */
		public function is_featured_active() {
			return YITH_WCAN_Query()->is_featured_only();
		}

		/**
		 * Retrieves url to filter by on-sale products
		 *
		 * @return string Url to filter by specified parameter.
		 */
		public function get_on_sale_filter_url() {
			$param = array( 'onsale_filter' => 1 );

			if ( $this->is_on_sale_active() ) {
				$url = YITH_WCAN_Query()->get_filter_url( array(), $param );
			} else {
				$url = YITH_WCAN_Query()->get_filter_url( $param );
			}

			return $url;
		}

		/**
		 * Retrieves url to filter by in-stock products
		 *
		 * @return string Url to filter by specified parameter.
		 */
		public function get_in_stock_filter_url() {
			$param = array( 'instock_filter' => 1 );

			if ( $this->is_in_stock_active() ) {
				$url = YITH_WCAN_Query()->get_filter_url( array(), $param );
			} else {
				$url = YITH_WCAN_Query()->get_filter_url( $param );
			}

			return apply_filters('yith_wcan_in_stock_filter_url', $url, $this );
		}

		/**
		 * Retrieves url to filter by featured products
		 *
		 * @return string Url to filter by specified parameter.
		 */
		public function get_featured_filter_url() {
			$param = array( 'featured_filter' => 1 );

			if ( $this->is_featured_active() ) {
				$url = YITH_WCAN_Query()->get_filter_url( array(), $param );
			} else {
				$url = YITH_WCAN_Query()->get_filter_url( $param );
			}

			return $url;
		}

		/**
		 * Returns count of on sale product for current query
		 *
		 * @return int Items count
		 */
		public function get_on_sale_count() {
			return YITH_WCAN_Query()->count_query_relevant_on_sale_products();
		}

		/**
		 * Returns count of in stock product for current query
		 *
		 * @return int Items count
		 */
		public function get_in_stock_count() {
			return YITH_WCAN_Query()->count_query_relevant_in_stock_products();
		}

		/**
		 * Returns count of featured product for current query
		 *
		 * @return int Items count
		 */
		public function get_featured_count() {
			return YITH_WCAN_Query()->count_query_relevant_featured_products();
		}
	}
}
