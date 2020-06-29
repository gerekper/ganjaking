<?php
/**
 * Product Rate Table class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Product_Rates_Table' ) ) {
	/**
	 * WooCommerce Product Rate Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Product_Rates_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Product_Rates_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'rate',     //singular name of the listed records
				'plural'   => 'rates',    //plural name of the listed records
				'ajax'     => false        //does this table support ajax?
			) );
		}

		/* === COLUMNS METHODS === */

		/**
		 * Print default column content
		 *
		 * @param $item        mixed Item of the row
		 * @param $column_name string Column name
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return esc_html( $item[ $column_name ] );
			} else {
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Print product column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_product( $item ) {
			if ( ! isset( $item['name'] ) || empty( $item['name'] ) ) {
				return '';
			}

			$product_id = $item['ID'];
			$product    = $item['product'];

			$column = sprintf( '%s<a href="%s">%s</a>', $product->get_image( array(
				32,
				32
			) ), get_edit_post_link( $item['ID'] ), $item['name'] );

			if ( $product->is_type( 'variation' ) ) {
				$column .= sprintf( '<div class="wc-order-item-name"><strong>%s</strong> %s</div>', __( 'Variation ID:', 'yith-woocommerce-affiliates' ), yit_get_product_id( $product ) );
			}

			return apply_filters( 'yith_wcaf_product_column', $column, $product_id, 'rates' );
		}

		/**
		 * Print price column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_price( $item ) {
			if ( ! isset( $item['price'] ) || empty( $item['price'] ) ) {
				return '';
			}

			$column = wc_price( $item['price'] );

			return $column;
		}

		/**
		 * Print rate column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_rate( $item ) {
			if ( ! isset( $item['rate'] ) ) {
				return '';
			}

			$column = sprintf( '<input type="number" min="0" max="100" step="any" value="%s"/>', $item['rate'] );

			return $column;
		}

		/**
		 * Print actions column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_actions( $item ) {
			$column = '';
			$column .= sprintf( '<a href="#" class="button button-secondary yith-products-update-commission" data-product_id="%s">%s</a>', $item['ID'], __( 'Update', 'yith-woocommerce-affiliates' ) );
			$column .= ' ';
			$column .= sprintf( '<a href="#" class="button button-secondary yith-products-delete-commission" data-product_id="%s">%s</a>', $item['ID'], __( 'Delete', 'yith-woocommerce-affiliates' ) );

			return $column;
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 1.0.0
		 */
		public function get_columns() {
			$columns = array(
				'product' => __( 'Product', 'yith-woocommerce-affiliates' ),
				'price'   => __( 'Price', 'yith-woocommerce-affiliates' ),
				'rate'    => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'actions' => __( 'Actions', 'yith-woocommerce-affiliates' )
			);

			return $columns;
		}

		/**
		 * Returns column to be sortable in table
		 *
		 * @return array Array of sortable columns
		 * @since 1.0.0
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'product' => array( 'product_names', false ),
				'price'   => array( 'product_prices', true ),
				'rate'    => array( 'product_rates', false )
			);

			return $sortable_columns;
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function prepare_items() {
			global $wpdb;

			$registered_rates  = get_option( 'yith_wcaf_product_rates' );
			$commissions_items = array();

			// sets pagination arguments
			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$total_items  = is_array( $registered_rates ) ? count( $registered_rates ) : 0;

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			if ( ! empty( $registered_rates ) ) {
				$product_names  = array();
				$product_prices = array();
				$product_rates  = array();

				foreach ( $registered_rates as $product_id => $rate ) {
					$product = wc_get_product( $product_id );

					if ( ! $product ) {
						continue;
					}

					$name            = $product->get_title();
					$product_names[] = $name;

					$price            = $product->get_price();
					$product_prices[] = $price;

					$product_rates[] = $rate;

					$new_item = array(
						'ID'      => $product_id,
						'product' => $product,
						'name'    => $name,
						'price'   => $price,
						'rate'    => $rate
					);

					$commissions_items[] = $new_item;
				}

				$column_order = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array(
					'product_names',
					'product_prices',
					'product_rates'
				) ) ? $_REQUEST['orderby'] : 'product_names';
				$order        = isset( $_REQUEST['order'] ) ? 'SORT_' . strtoupper( $_REQUEST['order'] ) : 'SORT_ASC';

				array_multisort( ${$column_order}, constant( $order ), $commissions_items );
			}

			// retrieve data for table
			$this->items = $commissions_items;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}