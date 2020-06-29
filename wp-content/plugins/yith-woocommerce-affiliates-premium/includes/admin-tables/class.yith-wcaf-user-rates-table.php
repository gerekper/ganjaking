<?php
/**
 * User Rate Table class
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

if ( ! class_exists( 'YITH_WCAF_User_Rates_Table' ) ) {
	/**
	 * WooCommerce User Rate Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_User_Rates_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_User_Rates_Table
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
		 * Print username column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_username( $item ) {
			if ( ! isset( $item['user_login'] ) || empty( $item['user_login'] ) ) {
				return '';
			}

			$column = sprintf( '%s<a href="%s">%s</a>', get_avatar( $item['user_id'], 32 ), get_edit_user_link( $item['user_id'] ), $item['user_login'] );

			return $column;
		}

		/**
		 * Print email column content
		 *
		 * @param $item mixed Item of the row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_email( $item ) {
			if ( ! isset( $item['user_email'] ) || empty( $item['user_email'] ) ) {
				return '';
			}

			$column = sprintf( '<a href="mailto:%s">%s</a>', $item['user_email'], $item['user_email'] );

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

			$column = sprintf( '<input type="number" min="0" max="100" step="any" value="%s"/>', number_format( $item['rate'], 2 ) );

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
			$column .= sprintf( '<a href="#" class="button button-secondary yith-affiliates-update-commission" data-affiliate_id="%s">%s</a>', $item['ID'], __( 'Update', 'yith-woocommerce-affiliates' ) );
			$column .= ' ';
			$column .= sprintf( '<a href="#" class="button button-secondary yith-affiliates-delete-commission" data-affiliate_id="%s">%s</a>', $item['ID'], __( 'Delete', 'yith-woocommerce-affiliates' ) );

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
				'username' => __( 'Username', 'yith-woocommerce-affiliates' ),
				'email'    => __( 'Email', 'yith-woocommerce-affiliates' ),
				'rate'     => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'actions'  => __( 'Actions', 'yith-woocommerce-affiliates' )
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
				'username' => array( 'user_login', true ),
				'email'    => array( 'user_email', false ),
				'rate'     => array( 'rate', false )
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

			// sets pagination arguments
			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$total_items  = YITH_WCAF_Affiliate_Handler()->count_affiliates( array( 'rate' => 'NOT NULL' ) );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$registered_rates = YITH_WCAF_Affiliate_Handler()->get_affiliates( array_merge(
				array( 'rate' => 'NOT NULL' ),
				array(
					'limit'   => $per_page,
					'offset'  => ( ( $current_page - 1 ) * $per_page ),
					'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'rate',
					'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
				)
			) );

			// retrieve data for table
			$this->items = $registered_rates;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}