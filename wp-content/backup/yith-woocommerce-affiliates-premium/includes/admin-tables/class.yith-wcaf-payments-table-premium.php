<?php
/**
 * Payments Table Premium class
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

if ( ! class_exists( 'YITH_WCAF_Payments_Table_Premium' ) ) {
	/**
	 * WooCommerce Payments Table Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Payments_Table_Premium extends YITH_WCAF_Payments_Table {
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
				'payments',  //Let's simply repurpose the table's singular label
				$item['ID']                //The value of the checkbox should be the record's id
			);
		}

		/**
		 * Print a column with payment ID
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_id( $item ) {
			$column = sprintf( '<a href="%s"><strong>#%d</strong></a>', esc_url( add_query_arg( 'payment_id', $item['ID'] ) ), $item['ID'] );

			return $column;
		}

		/**
		 * Print a column with the date payment was completed (if any), and eventually transaction key
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_completed_at( $item ) {
			$column = '';

			if ( isset( $item['completed_at'] ) ) {
				$transaction_key = isset( $item['transaction_key'] ) ? $item['transaction_key'] : __( 'N/A', 'yith-woocommerce-affiliates' );
				$column          .= sprintf( '%s<small class="meta">%s</small>', date_i18n( wc_date_format(), strtotime( $item['completed_at'] ) ), $transaction_key );
			} else {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			}

			return $column;
		}

		/**
		 * Print a column with the actions available for current payment
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_actions( $item ) {
			$actions     = array();
			$redirect_to = urlencode( $_SERVER['REQUEST_URI'] );

			$actions['view'] = sprintf( '<a class="button tips view" href="%s" data-tip="%s">%s</a>', esc_url( add_query_arg( 'payment_id', $item['ID'] ) ), __( 'View', 'yith-woocommerce-affiliates' ), __( 'View', 'yith-woocommerce-affiliates' ) );

			$gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();

			if ( $item['status'] == 'on-hold' && ! empty( $gateways ) ) {
				foreach ( $gateways as $id => $gateway ) {
					$action_label                = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
					$actions[ 'pay_via_' . $id ] = sprintf( '<a class="button tips pay" href="%s" data-tip="%s">%s</a>', esc_url( add_query_arg( array( 'action'      => 'yith_wcaf_complete_payment',
																																						'payment_id'  => $item['ID'],
																																						'gateway'     => $id,
																																						'redirect_to' => $redirect_to
					), admin_url( 'admin.php' ) ) ), $action_label, $action_label );
				}
			}

			return implode( ' ', $actions );
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 1.0.0
		 */
		public function get_columns() {
			$columns = array(
				'cb'            => '<input type="checkbox" />',
				'status'        => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Status', 'yith-woocommerce-affiliates' ), __( 'Status', 'yith-woocommerce-affiliates' ) ),
				'id'            => __( 'ID', 'yith-woocommerce-affiliates' ),
				'affiliate'     => __( 'Affiliate ', 'yith-woocommerce-affiliates' ),
				'payment_email' => __( 'Payment email', 'yith-woocommerce-affiliates' ),
				'amount'        => __( 'Amount', 'yith-woocommerce-affiliates' ),
				'created_at'    => __( 'Created at', 'yith-woocommerce-affiliates' ),
				'completed_at'  => __( 'Completed at', 'yith-woocommerce-affiliates' ),
				'actions'       => __( 'Actions', 'yith-woocommerce-affiliates' )
			);

			return apply_filters( 'yith_wcaf_payments_table_get_columns', $columns );
		}

		/**
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 1.0.0
		 */
		public function get_bulk_actions() {
			$actions = parent::get_bulk_actions();

			$gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();

			if ( ! empty( $gateways ) ) {
				foreach ( $gateways as $id => $gateway ) {
					$actions[ 'pay_via_' . $id ] = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
				}
			}

			return $actions;
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

			if ( ! empty( $_REQUEST['_affiliate_id'] ) ) {
				$query_arg['affiliate_id'] = $_REQUEST['_affiliate_id'];
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = date( 'Y-m-d 00:00:00', strtotime( $_REQUEST['_from'] ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = date( 'Y-m-d 23:59:59', strtotime( $_REQUEST['_to'] ) );
			}

			return array(
				'all'       => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'all' ) ), $current == 'all' ? 'current' : '', __( 'All', 'yith-woocommerce-affiliates' ), YITH_WCAF_Payment_Handler()->per_status_count( 'all', $query_arg ) ),
				'on-hold'   => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'on-hold' ) ), $current == 'on-hold' ? 'current' : '', __( 'On Hold', 'yith-woocommerce-affiliates' ), YITH_WCAF_Payment_Handler()->per_status_count( 'on-hold', $query_arg ) ),
				'pending'   => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'pending' ) ), $current == 'pending' ? 'current' : '', __( 'Pending', 'yith-woocommerce-affiliates' ), YITH_WCAF_Payment_Handler()->per_status_count( 'pending', $query_arg ) ),
				'completed' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'completed' ) ), $current == 'completed' ? 'current' : '', __( 'Completed', 'yith-woocommerce-affiliates' ), YITH_WCAF_Payment_Handler()->per_status_count( 'completed', $query_arg ) ),
				'cancelled' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'cancelled' ) ), $current == 'cancelled' ? 'current' : '', __( 'Cancelled', 'yith-woocommerce-affiliates' ), YITH_WCAF_Payment_Handler()->per_status_count( 'cancelled', $query_arg ) ),
			);
		}
	}
}