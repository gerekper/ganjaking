<?php
/**
 * Commissions Table Prmeium class
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

if ( ! class_exists( 'YITH_WCAF_Commissions_Table_Premium' ) ) {
	/**
	 * WooCommerce Commissions Table Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Commissions_Table_Premium extends YITH_WCAF_Commissions_Table {

		/**
		 * Print column with commission ID
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_id( $item ) {
			$column = sprintf( '<a href="%s"><strong>#%d</strong></a>', esc_url( add_query_arg( 'commission_id', $item['ID'] ) ), $item['ID'] );

			return $column;
		}

		/**
		 * Print column with order commission
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_order( $item ) {
			$column   = '';
			$order_id = $item['order_id'];
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				return '';
			}

			$customer_tip = array();

			if ( $address = $order->get_formatted_billing_address() ) {
				$customer_tip[] = __( 'Billing:', 'yith-woocommerce-affiliates' ) . ' ' . $address . '<br/><br/>';
			}

			if ( $billing_phone = yit_get_prop( $order, 'billing_phone' ) ) {
				$customer_tip[] = __( 'Tel:', 'yith-woocommerce-affiliates' ) . ' ' . $billing_phone;
			}

			$column .= '<div class="tips" data-tip="' . wc_sanitize_tooltip( implode( "<br/>", $customer_tip ) ) . '">';

			if ( $order->get_user_id() ) {
				$user_info = get_userdata( $order->get_user_id() );
			}

			if ( ! empty( $user_info ) ) {

				$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

				if ( $user_info->first_name || $user_info->last_name ) {
					$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
				} else {
					$username .= esc_html( ucfirst( $user_info->display_name ) );
				}

				$username .= '</a>';

			} else {
				$billing_first_name = yit_get_prop( $order, 'billing_first_name' );
				$billing_last_name  = yit_get_prop( $order, 'billing_last_name' );

				if ( $billing_first_name || $billing_last_name ) {
					$username = trim( $billing_first_name . ' ' . $billing_last_name );
				} else {
					$username = __( 'Guest', 'yith-woocommerce-affiliates' );
				}
			}

			$column .= sprintf( _x( '%s by %s', 'Order number by X', 'yith-wcaf' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>', $username );

			if ( $billing_email = yit_get_prop( $order, 'billing_email' ) ) {
				$column .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
			}

			$column .= '</div>';

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
		public function column_user( $item ) {
			$column    = '';
			$user_id   = $item['user_id'];
			$user_data = get_userdata( $user_id );

			if ( ! $user_data ) {
				return '';
			}

			$user_email = $user_data->user_email;

			$username = '';
			if ( $user_data->first_name || $user_data->last_name ) {
				$username .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user_data->display_name ) );
			}

			$column .= sprintf( '%s<a href="%s">%s</a><small class="meta email"><a href="mailto:%s">%s</a></small>', get_avatar( $user_id, 32 ), add_query_arg( '_user_id', $user_id ), $username, $user_email, $user_email );

			return $column;
		}

		/**
		 * Print column with commission product details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_product( $item ) {
			$product_id = $item['product_id'];
			$order_id   = $item['order_id'];
			$order      = wc_get_order( $order_id );

			if ( $order ) {
				$line_items = $order->get_items();
				$line_item  = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : false;

				if ( $line_item ) {
					/**
					 * @var $line_item \WC_Order_Item_Product
					 */
					$product = is_object( $line_item ) ? $line_item->get_product() : $order->get_product_from_item( $line_item );
				}
			} else {
				$product = wc_get_product( $product_id );
			}

			if ( ! isset( $product ) || ! $product ) {
				return '';
			}

			$column = sprintf( '%s<a href="%s">%s</a>', $product->get_image( array(
				32,
				32
			) ), add_query_arg( '_product_id', $product_id ), $product->get_title() );

			if ( $product->is_type( 'variation' ) ) {
				$column .= sprintf( '<div class="wc-order-item-name"><strong>%s</strong> %s</div>', __( 'Variation ID:', 'yith-woocommerce-affiliates' ), yit_get_product_id( $product ) );
			}

			return apply_filters( 'yith_wcaf_product_column', $column, $product_id, 'commissions' );
		}

		/**
		 * Print column with commission category details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_category( $item ) {
			$product_id = $item['product_id'];
			$categories = wp_get_post_terms( $product_id, 'product_cat' );

			if ( empty( $categories ) ) {
				$column = __( 'N/A', 'yith-woocommerce-affiliates' );
			} else {
				$column_items = array();

				foreach ( $categories as $category ) {
					$column_items[] = sprintf( '<a href="%s">%s</a>', get_term_link( $category ), $category->name );
				}

				$column = implode( ' | ', $column_items );
			}

			return apply_filters( 'yith_wcaf_category_column', $column, $product_id, 'commissions' );
		}

		/**
		 * Print column with line item discount
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_line_item_discounts( $item ) {
			$exclude_tax = YITH_WCAF_Commission_Handler()->get_option( 'exclude_tax' );
			$order       = wc_get_order( $item['order_id'] );

			if ( ! $order ) {
				return '';
			}

			$line_items = $order->get_items( 'line_item' );

			if ( ! empty( $items ) ) {
				return '';
			}

			$line_item = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : '';

			if ( ! $line_item ) {
				return '';
			}

			$total    = $order->get_item_total( $line_item, 'yes' != $exclude_tax );
			$subtotal = $order->get_item_subtotal( $line_item, 'yes' != $exclude_tax );
			$discount = ( $total - $subtotal ) * $line_item['qty'];

			$column = '';
			$column .= wc_price( $discount, array(
				'currency' => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency()
			) );

			return $column;
		}

		/**
		 * Print column with line item refunds
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_line_item_refunds( $item ) {
			$order = wc_get_order( $item['order_id'] );

			if ( $order && $order->get_status() == 'refunded' ) {
				$column = $this->column_line_item_total( $item );
			} else {
				$refunds = YITH_WCAF_Commission_Handler()->get_total_commission_refund( $item['ID'] );

				$column = wc_price( ( $item['rate'] != 0 ) ? $refunds / $item['rate'] * 100 : 0, array(
					'currency' => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency()
				) );
			}

			return $column;
		}

		/**
		 * Print column with commission active payments (should be one single element)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_payments( $item ) {
			$column          = '';
			$active_payments = YITH_WCAF_Payment_Handler()->get_commission_payments( $item['ID'], 'active' );

			if ( empty( $active_payments ) ) {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			} else {
				$first = true;
				foreach ( $active_payments as $payment ) {
					if ( ! $first ) {
						$column .= ' | ';
					}

					$payment_url = esc_url( add_query_arg( array(
						'page'       => 'yith_wcaf_panel',
						'tab'        => 'payments',
						'payment_id' => $payment['ID']
					), admin_url( 'admin.php' ) ) );
					$column      .= sprintf( '<a href="%s">#%d</a>', $payment_url, $payment['ID'] );

					$first = false;
				}
			}

			return $column;
		}

		/**
		 * Print column with commission actions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_actions( $item ) {
			$actions         = array();
			$actions['view'] = sprintf( '<a class="button tips view" href="%s" data-tip="%s">%s</a>', esc_url( add_query_arg( 'commission_id', $item['ID'] ) ), __( 'View', 'yith-woocommerce-affiliates' ), __( 'View', 'yith-woocommerce-affiliates' ) );

			$available_status_change = YITH_WCAF_Commission_Handler()->get_available_status_change( $item['ID'] );
			$redirect_to             = urlencode( $_SERVER['REQUEST_URI'] );

			if ( ! empty( $available_status_change ) && 'trash' != $item['status'] ) {
				if ( in_array( 'pending-payment', $available_status_change ) && ! in_array( $item['status'], YITH_WCAF_Commission_Handler()->payment_status ) ) {
					$available_gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();

					$mark_paid_url           = esc_url( add_query_arg( array(
						'action'        => 'yith_wcaf_pay_commission',
						'commission_id' => $item['ID'],
						'redirect_to'   => $redirect_to
					), admin_url( 'admin.php' ) ) );
					$actions['mark_as_paid'] = sprintf( '<a class="button tips completed" href="%s" data-tip="%s">%s</a>', $mark_paid_url, __( 'Mark as paid', 'yith-woocommerce-affiliates' ), __( 'Mark as paid', 'yith-woocommerce-affiliates' ) );

					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $id => $gateway ) {
							$payment_label               = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
							$pay_url                     = esc_url( add_query_arg( array(
								'action'        => 'yith_wcaf_pay_commission',
								'commission_id' => $item['ID'],
								'gateway'       => $id,
								'redirect_to'   => $redirect_to
							), admin_url( 'admin.php' ) ) );
							$actions[ 'pay_via_' . $id ] = sprintf( '<a class="button tips pay pay-via-%s" href="%s" data-tip="%s">%s</a>', $id, $pay_url, $payment_label, $payment_label );
						}
					}
				}

				foreach ( $available_status_change as $status ) {
					if ( in_array( $status, YITH_WCAF_Commission_Handler()->get_dead_status() ) ) {
						continue;
					}

					// avoid direct ( pending-payment -> pending ) status change
					if ( $item['status'] == 'pending-payment' && $status == 'pending' ) {
						continue;
					}

					// avoid "Trash" action button
					if ( $status == 'trash' ) {
						continue;
					}

					$readable_status                   = YITH_WCAF_Commission_Handler()->get_readable_status( $status );
					$switch_status_url                 = esc_url( add_query_arg( array(
						'action'        => 'yith_wcaf_change_commission_status',
						'commission_id' => $item['ID'],
						'status'        => $status,
						'redirect_to'   => $redirect_to
					), admin_url( 'admin.php' ) ) );
					$actions[ 'switch_to_' . $status ] = sprintf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', $status, $switch_status_url, sprintf( __( 'Change status to %s', 'yith-woocommerce-affiliates' ), $readable_status ), $readable_status );
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
				'cb'                  => '<input type="checkbox" />',
				'id'                  => __( 'ID', 'yith-woocommerce-affiliates' ),
				'status'              => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Status', 'yith-woocommerce-affiliates' ), __( 'Status', 'yith-woocommerce-affiliates' ) ),
				'order'               => __( 'Order', 'yith-woocommerce-affiliates' ),
				'user'                => __( 'User', 'yith-woocommerce-affiliates' ),
				'product'             => __( 'Product', 'yith-woocommerce-affiliates' ),
				'category'            => __( 'Category', 'yith-woocommerce-affiliates' ),
				'line_item_total'     => __( 'Total', 'yith-woocommerce-affiliates' ),
				'line_item_discounts' => __( 'Discounts', 'yith-woocommerce-affiliates' ),
				'line_item_refunds'   => __( 'Refunds', 'yith-woocommerce-affiliates' ),
				'rate'                => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'amount'              => __( 'Commission', 'yith-woocommerce-affiliates' ),
				'payments'            => __( 'Payment', 'yith-woocommerce-affiliates' ),
				'date'                => __( 'Date', 'yith-woocommerce-affiliates' ),
				'actions'             => __( 'Actions', 'yith-woocommerce-affiliates' )
			);

			return apply_filters('yith_wcaf_comission_table_columns',$columns);
		}

		/**
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 1.0.0
		 */
		public function get_bulk_actions() {
			$available_gateways = YITH_WCAF_Payment_Handler_Premium()->get_available_gateways();
			$payment_actions    = array();

			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $id => $gateway ) {
					$payment_actions[ 'pay_via_' . $id ] = sprintf( __( 'Pay via %s', 'yith-woocommerce-affiliates' ), $gateway['label'] );
				}
			}

			$actions = array_merge(
				$payment_actions,

				array(
					'pay'                     => __( 'Create a payment manually', 'yith-woocommerce-affiliates' ),
					'switch-to-pending'       => __( 'Change status to pending', 'yith-woocommerce-affiliates' ),
					'switch-to-not-confirmed' => __( 'Change status to not confirmed', 'yith-woocommerce-affiliates' ),
					'switch-to-cancelled'     => __( 'Change status to cancelled', 'yith-woocommerce-affiliates' ),
					'switch-to-refunded'      => __( 'Change status to refunded', 'yith-woocommerce-affiliates' ),
				),

				isset( $_GET['status'] ) && $_GET['status'] == 'trash' ? array(
					'restore' => __( 'Restore', 'yith-woocommerce-affiliates' ),
					'delete'  => __( 'Delete Permanently', 'yith-woocommerce-affiliates' ),
				) : array(),

				! isset( $_GET['status'] ) || $_GET['status'] != 'trash' ? array(
					'move-to-trash' => __( 'Move to Trash', 'yith-woocommerce-affiliates' )
				) : array()
			);

			return $actions;
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
