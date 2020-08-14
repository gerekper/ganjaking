<?php
/**
 * Affiliate Table class
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

if ( ! class_exists( 'YITH_WCAF_Affiliates_Table' ) ) {
	/**
	 * WooCommerce Affiliates Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliates_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Affiliates_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'affiliate',         //singular name of the listed records
				'plural'   => 'affiliates',        //plural name of the listed records
				'ajax'     => false            //does this table support ajax?
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
			if ( isset( $item->$column_name ) ) {
				return esc_html( $item->$column_name );
			} else {
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Print a column with checkbox for bulk actions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				'affiliates',  //Let's simply repurpose the table's singular label
				$item['ID']               //The value of the checkbox should be the record's id
			);
		}

		/**
		 * Print column with affiliate ID
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_id( $item ) {
			$column = sprintf( '<strong>#%s</strong>', $item['ID'] );

			return $column;
		}

		/**
		 * Print column with affiliate token
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_token( $item ) {
			$column = $item['token'];

			return $column;
		}

		/**
		 * Print column with affiliate status (enabled/disabled)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_status( $item ) {
			$banned  = $item['banned'];
			$enabled = $item['enabled'];

			if ( $banned ) {
				$human_friendly_status = __( 'Banned', 'yith-woocommerce-affiliates' );
				$class                 = 'banned';
			} else {
				switch ( $enabled ) {
					case 0:
						$human_friendly_status = __( 'Pending', 'yith-woocommerce-affiliates' );
						$class                 = 'pending';
						break;
					case - 1:
						$human_friendly_status = __( 'Rejected', 'yith-woocommerce-affiliates' );
						$class                 = 'disabled';
						break;
					case 1:
					default:
						$human_friendly_status = __( 'Accepted', 'yith-woocommerce-affiliates' );
						$class                 = 'enabled';
						break;
				}
			}


			$column = sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $human_friendly_status, $human_friendly_status );

			return $column;
		}

		/**
		 * Print column with affiliate user details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_affiliate( $item ) {
			$column = '';

			$user          = get_user_by( 'id', $item['user_id'] );
			$user_email    = $item['user_email'];
			$payment_email = ! empty( $item['payment_email'] ) ? $item['payment_email'] : __( 'N/A', 'yith-woocommerce-affiliates' );

			$username = '';
			if ( $user->first_name || $user->last_name ) {
				$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user->display_name ) );
			}

			$column .= sprintf( '%s%s<small class="meta email">%s</small><small class="meta">%s: %s</small>', get_avatar( $item['user_id'], 32 ), $username, $user_email, __( 'Payment', 'yith-woocommerce-affiliates' ), $payment_email );

			return $column;
		}

		/**
		 * Print column with affiliate custom rate (if any)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_rate( $item ) {
			$column = '';
			if ( $item['rate'] != '' ) {
				$column .= sprintf( '%.2f%%', number_format( round( $item['rate'], 2 ), 2 ) );
			} else {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			}

			return $column;
		}

		/**
		 * Print column with affiliate earnings (total commission earned, including paid and refunded one)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_earnings( $item ) {
			$column = '';
			$column .= wc_price( $item['totals'] );

			return $column;
		}

		/**
		 * Print column with affiliate paid (total of paid commissions)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_paid( $item ) {
			$column = '';
			$column .= wc_price( $item['paid'] );

			return $column;
		}

		/**
		 * Print column with affiliate balance (earnings - refund - paid)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_balance( $item ) {
			$column = '';
			$column .= wc_price( $item['balance'] );

			return $column;
		}

		/**
		 * Print column with affiliate clicks
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_click( $item ) {
			$column = '';
			$column .= $item['click'];

			return $column;
		}

		/**
		 * Print column with affiliate conversions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_conversion( $item ) {
			$column = '';
			$column .= $item['conversion'];

			return $column;
		}

		/**
		 * Print column with affiliate conversion rate ( conversion / clicks * 100 )
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_conv_rate( $item ) {
			$column = '';
			if ( $item['conv_rate'] ) {
				$column .= sprintf( '%.2f%%', number_format( $item['conv_rate'], 2 ) );
			} else {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			}

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
				'cb'         => '<input type="checkbox" />',
				'id'         => __( 'ID', 'yith-woocommerce-affiliates' ),
				'token'      => __( 'Token', 'yith-woocommerce-affiliates' ),
				'status'     => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Approved', 'yith-woocommerce-affiliates' ), __( 'Approved', 'yith-woocommerce-affiliates' ) ),
				'affiliate'  => __( 'Affiliate', 'yith-woocommerce-affiliates' ),
				'rate'       => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'earnings'   => __( 'Earnings', 'yith-woocommerce-affiliates' ),
				'paid'       => __( 'Paid', 'yith-woocommerce-affiliates' ),
				'balance'    => __( 'Balance', 'yith-woocommerce-affiliates' ),
				'click'      => __( 'Click', 'yith-woocommerce-affiliates' ),
				'conversion' => sprintf( '<span class="tips" data-tip="%s">%s</span>', __( 'Number of orders following a click', 'yith-woocommerce-affiliates' ), __( 'Conversion', 'yith-woocommerce-affiliates' ) ),
				'conv_rate'  => __( 'Conv. rate', 'yith-woocommerce-affiliates' ),
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
				'id'         => array( 'ID', true ),
				'token'      => array( 'token', true ),
				'rate'       => array( 'rate', false ),
				'affiliate'  => array( 'user_login', false ),
				'earnings'   => array( 'totals', false ),
				'refunds'    => array( 'refunds', false ),
				'paid'       => array( 'paid', false ),
				'balance'    => array( 'balance', false ),
				'click'      => array( 'click', false ),
				'conversion' => array( 'conversion', false ),
				'conv_rate'  => array( 'conv_rate', false )
			);

			return $sortable_columns;
		}

		/**
		 * Returns hidden columns for current table
		 *
		 * @return mixed Array of hidden columns
		 * @since 1.0.0
		 */
		public function get_hidden_columns() {
			return get_hidden_columns( get_current_screen() );
		}

		/**
		 * Print table views
		 *
		 * @return array Array with available views
		 * @since 1.0.0
		 */
		public function get_views() {
			$current   = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
			$query_arg = array();

			if ( ! empty( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
				$query_arg['s'] = $_REQUEST['s'];
			}

			return array(
				'all'      => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'all' ) ), $current == 'all' ? 'current' : '', __( 'All', 'yith-woocommerce-affiliates' ), YITH_WCAF_Affiliate_Handler()->per_status_count( 'all', $query_arg ) ),
				'waiting'  => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'new' ) ), $current == 'new' ? 'current' : '', __( 'Pending', 'yith-woocommerce-affiliates' ), YITH_WCAF_Affiliate_Handler()->per_status_count( 'new', $query_arg ) ),
				'enabled'  => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'enabled' ) ), $current == 'enabled' ? 'current' : '', __( 'Enabled', 'yith-woocommerce-affiliates' ), YITH_WCAF_Affiliate_Handler()->per_status_count( 'enabled', $query_arg ) ),
				'disabled' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'disabled' ) ), $current == 'disabled' ? 'current' : '', __( 'Disabled', 'yith-woocommerce-affiliates' ), YITH_WCAF_Affiliate_Handler()->per_status_count( 'disabled', $query_arg ) ),
				'banned'   => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'banned' ) ), $current == 'banned' ? 'current' : '', __( 'Banned', 'yith-woocommerce-affiliates' ), YITH_WCAF_Affiliate_Handler()->per_status_count( 'banned', $query_arg ) )
			);
		}

		/**
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 1.0.0
		 */
		public function get_bulk_actions() {
			$actions = array(
				'delete'  => __( 'Delete', 'yith-woocommerce-affiliates' ),
				'enable'  => __( 'Change status to Active', 'yith-woocommerce-affiliates' ),
				'disable' => __( 'Change status to Rejected', 'yith-woocommerce-affiliates' ),
				'ban'     => __( 'Ban Affiliate', 'yith-woocommerce-affiliates' ),
				'unban'   => __( 'Unban Affiliate', 'yith-woocommerce-affiliates' )
			);

			return $actions;
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function prepare_items() {
			global $wpdb;

			$query_arg = array();

			if ( ! empty( $_GET['status'] ) && ! in_array( $_GET['status'], array( 'all', 'banned' ) ) ) {
				$query_arg['enabled'] = $_GET['status'];
				$query_arg['banned']  = 'unbanned';
			} elseif ( ! empty( $_GET['status'] ) && $_GET['status'] == 'banned' ) {
				$query_arg['banned'] = $_GET['status'];
			}

			if ( ! empty( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
				$query_arg['s'] = $_REQUEST['s'];
			}

			// sets pagination arguments
			$per_page     = $this->get_items_per_page( 'edit_affiliates_per_page' );
			$current_page = $this->get_pagenum();
			$total_items  = YITH_WCAF_Affiliate_Handler()->count_affiliates( $query_arg );
			$users        = YITH_WCAF_Affiliate_Handler()->get_affiliates( array_merge(
				array(
					'limit'   => $per_page,
					'offset'  => ( ( $current_page - 1 ) * $per_page ),
					'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'ID',
					'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
				),
				$query_arg
			) );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// retrieve data for table
			$this->items = $users;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}