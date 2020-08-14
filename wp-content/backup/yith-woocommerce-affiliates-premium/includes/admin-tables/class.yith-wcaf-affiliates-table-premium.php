<?php
/**
 * Affiliate Table Premium class
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

if ( ! class_exists( 'YITH_WCAF_Affiliates_Table_Premium' ) ) {
	/**
	 * WooCommerce Affiliates Table Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliates_Table_Premium extends YITH_WCAF_Affiliates_Table {
		/**
		 * Print column with affiliate ID
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_id( $item ) {
			$commission_url = esc_url( add_query_arg( array( 'page'     => 'yith_wcaf_panel',
															 'tab'      => 'commissions',
															 '_user_id' => $item['user_id']
			), admin_url( 'admin.php' ) ) );
			$actions        = array(
				'view_commissions' => sprintf( '<a href="%s">%s</a>', $commission_url, __( 'Commissions', 'yith-woocommerce-affiliates' ) )
			);

			$column = sprintf( '<a href="%s"><strong>#%s</strong></a>%s', esc_url( add_query_arg( 'affiliate_id', $item['ID'] ) ), $item['ID'], $this->row_actions( $actions ) );

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

			if ( ! $user ) {
				return __( 'N/A', 'yith-woocommerce-affiliates' );
			}

			$username = '';
			if ( $user->first_name || $user->last_name ) {
				$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user->display_name ) );
			}

			$column .= sprintf( '%s<a href="%s">%s</a><small class="meta email"><a href="mailto:%s">%s</a></small><small class="meta">%s: %s</small>', get_avatar( $item['user_id'], 32 ), get_edit_user_link( $item['user_id'] ), $username, $user_email, $user_email, __( 'Payment', 'yith-woocommerce-affiliates' ), $payment_email );

			return $column;
		}

		/**
		 * Print column with affiliate refunds (total of refunded commissions)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_refunds( $item ) {
			$column = '';
			$column .= wc_price( $item['refunds'] );

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
			$column .= sprintf( '<a href="%s">%d</a>', esc_url( add_query_arg( array( 'page'     => 'yith_wcaf_panel',
																					  'tab'      => 'clicks',
																					  '_user_id' => $item['user_id']
			), admin_url( 'admin.php' ) ) ), $item['click'] );

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
			$column .= sprintf( '<a href="%s">%d</a>', esc_url( add_query_arg( array( 'page'     => 'yith_wcaf_panel',
																					  'tab'      => 'clicks',
																					  '_user_id' => $item['user_id'],
																					  'status'   => 'converted'
			), admin_url( 'admin.php' ) ) ), $item['conversion'] );

			return $column;
		}

		/**
		 * Print column with affiliate actions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_actions( $item ) {
			$actions         = array();
			$actions['view'] = sprintf( '<a class="button tips view" href="%s" data-tip="%s">%s</a>', get_edit_user_link( $item['user_id'] ), __( 'View', 'yith-woocommerce-affiliates' ), __( 'View', 'yith-woocommerce-affiliates' ) );

			$enabled = $item['enabled'];
			$banned  = $item['banned'];

			$redirect_to = urlencode( $_SERVER['REQUEST_URI'] );

			if ( ! $banned ) {
				// disable button
				if ( $enabled == 0 || $enabled == 1 ) {
					$switch_status_url  = esc_url( add_query_arg( array(
						'action'       => 'yith_wcaf_change_status',
						'affiliate_id' => $item['ID'],
						'status'       => 'disabled',
						'redirect_to'  => $redirect_to
					), admin_url( 'admin.php' ) ) );
					$actions['disable'] = sprintf( '<a class="button tips disable" href="%s" data-tip="%s">%s</a>', $switch_status_url, __( 'Change status to Rejected', 'yith-woocommerce-affiliates' ), __( 'Disable', 'yith-woocommerce-affiliates' ) );
				}

				// enable button
				if ( $enabled == 0 || $enabled == - 1 ) {
					$switch_status_url = esc_url( add_query_arg( array( 'action'       => 'yith_wcaf_change_status',
																		'affiliate_id' => $item['ID'],
																		'status'       => 'enabled',
																		'redirect_to'  => $redirect_to
					), admin_url( 'admin.php' ) ) );
					$actions['enable'] = sprintf( '<a class="button tips enable" href="%s" data-tip="%s">%s</a>', $switch_status_url, __( 'Change status to Active', 'yith-woocommerce-affiliates' ), __( 'Enable', 'yith-woocommerce-affiliates' ) );
				}

				// ban button
				$switch_status_url = esc_url( add_query_arg( array( 'action'       => 'yith_wcaf_change_status',
																	'affiliate_id' => $item['ID'],
																	'status'       => 'banned',
																	'redirect_to'  => $redirect_to
				), admin_url( 'admin.php' ) ) );
				$actions['ban']    = sprintf( '<a class="button tips ban" href="%s" data-tip="%s">%s</a>', $switch_status_url, __( 'Ban affiliate', 'yith-woocommerce-affiliates' ), __( 'Ban', 'yith-woocommerce-affiliates' ) );
			} else {
				// unban button
				$switch_status_url = esc_url( add_query_arg( array( 'action'       => 'yith_wcaf_change_status',
																	'affiliate_id' => $item['ID'],
																	'status'       => 'unbanned',
																	'redirect_to'  => $redirect_to
				), admin_url( 'admin.php' ) ) );
				$actions['unban']  = sprintf( '<a class="button tips unban" href="%s" data-tip="%s">%s</a>', $switch_status_url, __( 'Unban affiliate', 'yith-woocommerce-affiliates' ), __( 'Unban', 'yith-woocommerce-affiliates' ) );
			}

			/**
			 * @since 1.2.4
			 */
			$process_dangling_commissions_url        = esc_url( add_query_arg( array(
				'action'       => 'yith_wcaf_process_dangling_commissions',
				'affiliate_id' => $item['ID'],
				'redirect_to'  => $redirect_to
			), admin_url( 'admin.php' ) ) );
			$actions['process_dangling_commissions'] = sprintf( '<a class="button tips dangling" href="%s" data-tip="%2$s">%2$s</a>', $process_dangling_commissions_url, __( 'Process dangling commissions', 'yith-woocommerce-affiliates' ) );

			if ( YITH_WCAF_Affiliate_Handler_Premium()->has_unpaid_commissions( $item['ID'] ) ) {
				$available_gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();

				$mark_paid_url  = esc_url( add_query_arg( array(
					'action'       => 'yith_wcaf_pay_commissions',
					'affiliate_id' => $item['ID'],
					'redirect_to'  => $redirect_to
				), admin_url( 'admin.php' ) ) );
				$actions['pay'] = sprintf( '<a class="button tips pay" href="%s" data-tip="%s">%s</a>', $mark_paid_url, __( 'Pay Commissions', 'yith-woocommerce-affiliates' ), __( 'Pay Commissions', 'yith-woocommerce-affiliates' ) );

				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $id => $gateway ) {
						$payment_label               = sprintf( __( 'Pay commissions via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
						$pay_url                     = esc_url( add_query_arg( array(
							'action'       => 'yith_wcaf_pay_commissions',
							'affiliate_id' => $item['ID'],
							'gateway'      => $id,
							'redirect_to'  => $redirect_to
						), admin_url( 'admin.php' ) ) );
						$actions[ 'pay_via_' . $id ] = sprintf( '<a class="button tips pay pay-via-%s" href="%s" data-tip="%s">%s</a>', $id, $pay_url, $payment_label, $payment_label );
					}
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
				'cb'         => '<input type="checkbox" />',
				'id'         => __( 'ID', 'yith-woocommerce-affiliates' ),
				'token'      => __( 'Token', 'yith-woocommerce-affiliates' ),
				'status'     => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Approved', 'yith-woocommerce-affiliates' ), __( 'Approved', 'yith-woocommerce-affiliates' ) ),
				'affiliate'  => __( 'Affiliate', 'yith-woocommerce-affiliates' ),
				'rate'       => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'earnings'   => __( 'Earnings', 'yith-woocommerce-affiliates' ),
				'refunds'    => __( 'Refunds', 'yith-woocommerce-affiliates' ),
				'paid'       => __( 'Paid', 'yith-woocommerce-affiliates' ),
				'balance'    => __( 'Balance', 'yith-woocommerce-affiliates' ),
				'click'      => __( 'Click', 'yith-woocommerce-affiliates' ),
				'conversion' => sprintf( '<span class="tips" data-tip="%s">%s</span>', __( 'Number of orders following a click', 'yith-woocommerce-affiliates' ), __( 'Conversion', 'yith-woocommerce-affiliates' ) ),
				'conv_rate'  => __( 'Conv. rate', 'yith-woocommerce-affiliates' ),
				'actions'    => __( 'Actions', 'yith-woocommerce-affiliates' )
			);

			return $columns;
		}

		/**
		 * Print filters for current table
		 *
		 * @param $which string Top / Bottom
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function extra_tablenav( $which ) {
			parent::extra_tablenav( $which );

			submit_button( __( 'Export CSV', 'yith-woocommerce-affiliates' ), 'button', 'export_action', false );
		}
	}
}