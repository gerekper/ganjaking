<?php
/**
 * Category Deposit Table class
 *
 * @todo    check code once WC switches to custom tables
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
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

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Category_Deposits_Table' ) ) {
	/**
	 * WooCommerce Product Deposits Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Category_Deposits_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCDP_Category_Deposits_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'deposit',     //singular name of the listed records
				'plural'   => 'deposits',    //plural name of the listed records
				'ajax'     => false          //does this table support ajax?
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
		public function column_category( $item ) {
			if ( ! isset( $item['name'] ) || empty( $item['name'] ) ) {
				return '';
			}

			if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', WC()->version ), '2.6', '>=' ) && function_exists( 'get_term_meta' ) ) {
				$thumbnail_id = get_term_meta( $item['ID'], 'thumbnail_id', true );
			} else {
				$thumbnail_id = get_metadata( $item['ID'], 'thumbnail_id', true );
			}

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}

			$thumb = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'woocommerce' ) . '" class="wp-post-image" height="32" width="32" />';

			$column = sprintf( '%s<a href="%s">%s</a>', $thumb, get_edit_term_link( $item['ID'], 'product_cat' ), $item['name'] );

			return $column;
		}

		/**
		 * Print deposit type column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_type( $item ) {
			if ( ! isset( $item['type'] ) ) {
				return '';
			}

			$column = '<select>
					       <option value="amount" ' . selected( $item['type'], 'amount', false ) . ' >' . __( 'Amount', 'yith-woocommerce-deposits-and-down-payments' ) . '</option>
					       <option value="rate" ' . selected( $item['type'], 'rate', false ) . ' >' . __( 'Rate', 'yith-woocommerce-deposits-and-down-payments' ) . '</option>
					   </select>';

			return $column;
		}

		/**
		 * Print deposit value column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_value( $item ) {
			if ( ! isset( $item['value'] ) ) {
				return '';
			}

			$column = sprintf( '<input type="number" min="0" max="9999999" step="any" value="%s"/>', $item['value'] );

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
			$column .= sprintf( '<a href="#" class="button button-secondary yith-categories-update-deposit" data-term_id="%s">%s</a>', $item['ID'], __( 'Update', 'yith-woocommerce-deposits-and-down-payments' ) );
			$column .= ' ';
			$column .= sprintf( '<a href="#" class="button button-secondary yith-categories-delete-deposit" data-term_id="%s">%s</a>', $item['ID'], __( 'Delete', 'yith-woocommerce-deposits-and-down-payments' ) );

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
				'category' => __( 'Category', 'yith-woocommerce-deposits-and-down-payments' ),
				'type'     => __( 'Deposit type', 'yith-woocommerce-deposits-and-down-payments' ),
				'value'    => __( 'Deposit value', 'yith-woocommerce-deposits-and-down-payments' ),
				'actions'  => __( 'Actions', 'yith-woocommerce-deposits-and-down-payments' )
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
				'category' => array( 'category_names', false ),
				'type'     => array( 'category_types', true ),
				'value'    => array( 'category_values', false )
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

			$registered_deposits = get_option( 'yith_wcdp_category_deposits' );
			$deposits_items      = array();

			// sets pagination arguments
			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$total_items  = is_array( $registered_deposits ) ? count( $registered_deposits ) : 0;

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			if ( ! empty( $registered_deposits ) ) {
				$category_names  = array();
				$category_types  = array();
				$category_values = array();

				foreach ( $registered_deposits as $term_id => $details ) {
					$term = get_term( $term_id, 'product_cat' );

					$name             = $term->name;
					$category_names[] = $name;

					$category_values[] = $details['value'];

					$category_types[] = $details['type'];

					$new_item = array(
						'ID'    => $term_id,
						'name'  => $name,
						'type'  => $details['type'],
						'value' => $details['value']
					);

					$deposits_items[] = $new_item;
				}

				$column_order = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array(
					'product_names',
					'product_prices',
					'product_values',
					'product_types'
				) ) ? $_REQUEST['orderby'] : 'category_names';
				$order        = isset( $_REQUEST['order'] ) ? 'SORT_' . strtoupper( $_REQUEST['order'] ) : 'SORT_ASC';

				array_multisort( ${$column_order}, constant( $order ), $deposits_items );
			}

			// retrieve data for table
			$this->items = $deposits_items;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}