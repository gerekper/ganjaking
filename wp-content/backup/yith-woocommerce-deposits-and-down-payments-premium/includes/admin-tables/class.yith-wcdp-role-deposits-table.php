<?php
/**
 * User Role Deposit Table class
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

if ( ! class_exists( 'YITH_WCDP_Role_Deposits_Table' ) ) {
	/**
	 * WooCommerce Product Deposits Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Role_Deposits_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCDP_Role_Deposits_Table
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
		 * Print role column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_role( $item ) {
			if ( ! isset( $item['name'] ) || empty( $item['name'] ) ) {
				return '';
			}

			$column = $item['name'];

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
			$column .= sprintf( '<a href="#" class="button button-secondary yith-roles-update-deposit" data-role_slug="%s">%s</a>', $item['slug'], __( 'Update', 'yith-woocommerce-deposits-and-down-payments' ) );
			$column .= ' ';
			$column .= sprintf( '<a href="#" class="button button-secondary yith-roles-delete-deposit" data-role_slug="%s">%s</a>', $item['slug'], __( 'Delete', 'yith-woocommerce-deposits-and-down-payments' ) );

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
				'role'    => __( 'Role', 'yith-woocommerce-deposits-and-down-payments' ),
				'type'    => __( 'Deposit type', 'yith-woocommerce-deposits-and-down-payments' ),
				'value'   => __( 'Deposit value', 'yith-woocommerce-deposits-and-down-payments' ),
				'actions' => __( 'Actions', 'yith-woocommerce-deposits-and-down-payments' )
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
				'role'  => array( 'role_names', false ),
				'type'  => array( 'role_types', true ),
				'value' => array( 'role_values', false )
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

			$registered_deposits = get_option( 'yith_wcdp_user_role_deposits', array() );
			$deposits_items      = array();

			// sets pagination arguments
			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$total_items  = count( $registered_deposits );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			if ( ! empty( $registered_deposits ) ) {
				$role_names  = array();
				$role_types  = array();
				$role_values = array();

				foreach ( $registered_deposits as $role_slug => $details ) {
					$role = get_role( $role_slug );

					$name         = ucfirst( $role->name );
					$role_names[] = $name;

					$role_values[] = $details['value'];

					$role_types[] = $details['type'];

					$new_item = array(
						'slug'  => $role_slug,
						'name'  => $name,
						'type'  => $details['type'],
						'value' => $details['value']
					);

					$deposits_items[] = $new_item;
				}

				$column_order = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array(
					'role_names',
					'role_values',
					'role_types'
				) ) ? $_REQUEST['orderby'] : 'role_names';
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