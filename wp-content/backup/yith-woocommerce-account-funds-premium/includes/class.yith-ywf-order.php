<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_YWF_Order' ) ) {

	class YITH_YWF_Order {
		/**
		 * YITH_YWF_Order constructor.
		 */
		public function __construct() {

			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 10, 2 );


			add_action( 'woocommerce_order_status_changed', array( $this, 'manage_order_funds' ), 10, 3 );
			add_action( 'woocommerce_before_pay_action', array( $this, 'fix_total_with_partial_payment'), 20 );
			add_action( 'woocommerce_admin_order_totals_after_tax', array(
				$this,
				'woocommerce_admin_order_totals_show_user_funds'
			) );
			add_action( 'woocommerce_admin_order_totals_after_total', array(
				$this,
				'woocommerce_admin_order_totals_user_funds_available'
			) );
			add_action( 'woocommerce_pre_payment_complete', 'yith_account_funds_clear_session' );
			add_action( 'woocommerce_cart_emptied', 'yith_account_funds_clear_session' );
			add_filter( 'woocommerce_get_order_item_totals', array( $this, 'get_order_fund_item_total' ), 10, 2 );


			add_action( 'woocommerce_create_refund', array( $this, 'check_if_valid_refund' ), 20, 2 );

			add_action( 'woocommerce_refund_deleted', array( $this, 'refund_deleted_order_funds' ), 10, 2 );
			add_filter( 'woocommerce_order_get_total', array( $this, 'show_order_total_include_funds' ), 20, 2 );

			//update order deposit meta
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_deposit_meta' ), 10, 2 );

			//order again
			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'deposit_again' ), 5, 1 );

			add_filter( 'woocommerce_ajax_calc_line_taxes', array( $this, 'remove_deposit_from_items' ), 10, 3 );
			if ( is_admin() ) {

				add_filter( 'views_edit-shop_order', array( $this, 'add_order_deposit_view' ) );
				add_action( 'pre_get_posts', array( $this, 'filter_order_deposit_for_view' ) );
				add_action( 'woocommerce_admin_order_totals_after_total', array(
					$this,
					'add_order_custom_field'
				), 20, 1 );
				add_action( 'add_meta_boxes', array( $this, 'add_order_customer_funds_metabox' ) );
				add_action( 'wp_ajax_add_funds', array( $this, 'refund_funds_in_partial_payments' ) );

			}

		}


		/**
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 *
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function manage_order_funds( $order_id, $old_status, $new_status ) {
			yith_account_funds_clear_session();

			$order = wc_get_order( $order_id );
			if ( ! wp_get_post_parent_id( $order_id ) ) {
				if ( ywf_order_has_deposit( $order ) ) {

					switch ( $new_status ) {

						case 'completed':
							$this->add_deposit_order( $order );
							break;
					}
				} else {

					$funds_order = $order->get_meta( '_order_funds' );

					$funds_order_remove = $order->get_meta( '_order_fund_removed' );

					if ( ! empty( $funds_order ) ) {
						switch ( $new_status ) {

							case 'completed':
							case 'processing':
							case 'pending':
							case 'on-hold':
								$this->add_fund_order( $order, $funds_order_remove, $funds_order );
								break;
							case 'cancelled':
								$this->remove_fund_order( $order, $funds_order_remove, $funds_order );
								break;
						}
					}
				}
			}
		}


		/**
		 * add order fund and decrement user fund
		 *
		 * @param WC_Order $order
		 * @param $has_removed
		 * @param $funds
		 *
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function add_fund_order( $order, $has_removed, $funds ) {

			$order_id            = $order->get_id();
			$customer_id         = $order->get_user_id();
			$customer_fund       = new YITH_YWF_Customer( $customer_id );
			$total_fund_refunded = $order->get_meta( '_order_funds_refunded' );
			$total_fund_refunded = $total_fund_refunded === '' ? 0 : $total_fund_refunded;

			if ( ( empty( $has_removed ) || 'no' === $has_removed ) && ! empty( $funds ) ) {
				$customer_fund->decrement_funds( $funds );
				$funds_show_to_order_currency = apply_filters( 'yith_show_funds_used_into_order_currency', $funds, $order_id );
				$order->update_meta_data( '_order_fund_removed', 'yes' );
				$order->save();

				$order_note = sprintf( __( 'Removed %s funds from customer #%s account', 'yith-woocommorce-funds' ), wc_price( $funds_show_to_order_currency ), $customer_id );
				$order->add_order_note( $order_note );
				$default = apply_filters( 'ywf_add_fund_order_log_arguments', array(
					'user_id'        => $customer_id,
					'order_id'       => $order_id,
					'fund_user'      => $funds - $total_fund_refunded,
					'type_operation' => 'pay'
				) );


				do_action( 'ywf_add_user_log', $default );
			}


		}

		/**
		 * remove order fund and increment user fund
		 *
		 * @param WC_Order $order
		 * @param $has_removed
		 * @param $funds
		 *
		 * @author YITH
		 *
		 */
		public function remove_fund_order( $order, $has_removed, $funds ) {

			$order_id            = $order->get_id();
			$customer_id         = $order->get_user_id();
			$customer_fund       = new YITH_YWF_Customer( $customer_id );
			$total_fund_refunded = $order->get_meta( '_order_funds_refunded' );
			$total_fund_refunded = empty( $total_fund_refunded ) ? 0 : $total_fund_refunded;

			if ( 'yes' === $has_removed && $funds ) {

				$customer_fund->add_funds( $funds );
				$order->update_meta_data( '_order_fund_removed', 'no' );
				$order->save();

				$funds_show_to_order_currency = apply_filters( 'yith_show_funds_used_into_order_currency', $funds, $order_id );

				$order_note = sprintf( __( 'Added %s funds to customer #%s account', 'yith-woocommorce-funds' ), wc_price( $funds_show_to_order_currency ), $customer_id );
				$order->add_order_note( $order_note );

				$default = array(
					'user_id'        => $customer_id,
					'order_id'       => $order_id,
					'fund_user'      => $funds - $total_fund_refunded,
					'type_operation' => 'restore'
				);
				do_action( 'ywf_add_user_log', $default );
			}

		}

		/**
		 * add funds to customer
		 *
		 * @param WC_Order $order
		 *
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function add_deposit_order( $order ) {

			$total    = $this->get_order_deposit_total( $order );
			$order_id = $order->get_id();

			$user_id        = $order->get_user_id();
			$fund_deposited = $order->get_meta( '_fund_deposited' );


			if ( empty( $fund_deposited ) || $fund_deposited == 'no' ) {

				$customer_fund = new YITH_YWF_Customer( $user_id );
				$order->update_meta_data( '_fund_deposited', 'yes' );
				$order->save();
				$order_note = sprintf( __( 'Added %s funds to customer #%s account', 'yith-woocommorce-funds' ), wc_price( $total ), $user_id );
				$order->add_order_note( $order_note );

				$total = apply_filters( 'yith_admin_deposit_funds', $total, $order_id );
				$customer_fund->add_funds( $total );
				$default = array(
					'user_id'        => $user_id,
					'order_id'       => $order_id,
					'fund_user'      => $total,
					'type_operation' => 'deposit'
				);
				do_action( 'ywf_add_user_log', $default );
			}
		}


		/**
		 * @param WC_Order_Refund $refund
		 * @param array $args
		 */
		public function check_if_valid_refund( $refund, $args ) {

			$order_id = $args['order_id'];
			$order    = wc_get_order( $order_id );
			if ( ywf_order_has_deposit( $order ) ) {

				$refund_total = $refund->get_amount();

				$customer_id      = $order->get_user_id();
				$customer         = new YITH_YWF_Customer( $customer_id );
				$funds            = apply_filters( 'yith_show_funds_used_into_order_currency', $customer->get_funds(), $order_id );
				$raw_refund_total = $refund_total;
				$refund_total     = apply_filters( 'yith_admin_deposit_funds', $refund_total, $order_id );

				if ( $refund_total > $funds && $funds > 0 ) {

					$refund->set_amount( $funds );
					$refund->set_total( $funds * - 1 );
				}


				$refund_total                = $refund->get_amount();
				$refund_total_admin_currency = apply_filters( 'yith_admin_order_total', $refund_total, $order_id );

				$refund_total_formatted = wc_price( $refund_total, array( 'currency' => $refund->get_currency() ) );

				$order_note = sprintf( __( 'Removed %s funds from customer #%s account', 'yith-woocommorce-funds' ), $refund_total_formatted, $customer_id );
				$order->add_order_note( $order_note );


				$customer->decrement_funds( $refund_total_admin_currency );
				$default = array(
					'user_id'        => $customer_id,
					'order_id'       => $order_id,
					'fund_user'      => $refund_total_admin_currency,
					'type_operation' => 'remove'
				);
				do_action( 'ywf_add_user_log', $default );

			}
		}

		/**
		 * save fund used in order meta
		 *
		 * @param $order_id
		 * @param $posted
		 *
		 * @author YITH
		 * @since 1.0.0
		 *
		 */
		public function update_order_meta( $order_id, $posted ) {
			if ( $posted['payment_method'] !== 'yith_funds' && isset( WC()->session->ywf_partial_payment ) && WC()->session->ywf_partial_payment == 'yes' && isset( WC()->session->ywf_fund_used ) ) {

				$funds_used = WC()->session->ywf_fund_used;
				$order      = wc_get_order( $order_id );

				if ( ! is_null( $funds_used ) ) {

					$meta_data_update = array(
						'_order_funds'                      => $funds_used,
						'_order_fund_removed'               => 'no',
						'ywf_total_paid_with_other_gateway' => $order->get_total( 'edit' ),
						'ywf_partial_payment'               => 'yes'
					);

					foreach ( $meta_data_update as $meta_key => $meta_value ) {
						$order->update_meta_data( $meta_key, $meta_value );
					}

					$order->save();

				}
			}
		}

		/**
		 * save order deposit meta
		 *
		 * @param $order_id
		 * @param $posted
		 *
		 * @author YITH
		 * @since 1.0.0
		 *
		 */
		public function update_order_deposit_meta( $order_id, $posted ) {

			$order = wc_get_order( $order_id );

			if ( $order->get_item_count() == 1 ) {

				$has_deposit_product = false;

				/**
				 * @var WC_Order_Item $item
				 */
				foreach ( $order->get_items() as $item ) {
					$product_id = wc_get_order_item_meta( $item->get_id(), '_product_id', true );

					$product = wc_get_product( $product_id );

					if ( $product->is_type( 'ywf_deposit' ) ) {
						$has_deposit_product = true;
						break;
					}
				}
				if ( $has_deposit_product && WC()->session->get( 'deposit_amount', false ) ) {

					$meta_data_update = array(
						'_order_has_deposit'    => 'yes',
						'_order_deposit_amount' => WC()->session->get( 'deposit_amount' )
					);

					foreach ( $meta_data_update as $meta_key => $meta_value ) {
						$order->update_meta_data( $meta_key, $meta_value );
					}

					$order->save();
				}
			}

		}


		/**
		 * print custom order details in admin
		 *
		 * @param $order_id
		 *
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function woocommerce_admin_order_totals_show_user_funds( $order_id ) {
			$order              = wc_get_order( $order_id );
			$order_funds        = $order->get_meta( '_order_funds' );
			if ( $order_funds ) {


				?>
                <tr>
                    <td class="label"><?php echo wc_help_tip( __( 'Funds used by the customer to pay for this order.', 'yith-woocommerce-account-funds' ) ); ?><?php _e( 'Funds used', 'yith-woocommerce-account-funds' ); ?>
                    </td>

                    <td width="1%"></td>
                    <td class="total">
						<?php

						?>
						<?php echo $this->get_formatted_order_total( $order ); ?>
                    </td>
                </tr>
				<?php

			}
		}

		public function woocommerce_admin_order_totals_user_funds_available( $order_id ) {

			$order = wc_get_order( $order_id );

			if ( ywf_order_has_deposit( $order ) ) {


				$user_funds   = new YITH_YWF_Customer( $order->get_user_id() );
				$tot_funds_av = apply_filters( 'yith_admin_order_totals_user_available', $user_funds->get_funds(), $order_id );
				?>
                <input type="hidden" class="ywf_available_user_fund" value="<?php echo $tot_funds_av; ?>">
				<?php
			}
		}

		/**
		 * @param WC_Order $order
		 * @param string $tax_display
		 * @param bool $display_refunded
		 *
		 * @return string
		 */
		public function get_formatted_order_total( $order, $tax_display = '', $display_refunded = true ) {

			global $YITH_FUNDS;

			$order_id = $order->get_id();
			$total    = apply_filters( 'yith_show_funds_used_into_order_currency', $order->get_meta( '_order_funds' ), $order_id );

			$currency        = $YITH_FUNDS->is_wc_2_7 ? $order->get_currency() : $order->get_order_currency();
			$formatted_total = wc_price( - $total, array( 'currency' => $currency ) );
			$order_total     = $total;
			$total_refunded  = apply_filters( 'yith_show_funds_used_into_order_currency', $order->get_meta( '_order_funds_refunded' ), $order_id );
			$tax_string      = '';

			// Tax for inclusive prices
			if ( wc_tax_enabled() && 'incl' == $tax_display ) {
				$tax_string_array = array();

				if ( 'itemized' == get_option( 'woocommerce_tax_total_display' ) ) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$tax_amount         = ( $total_refunded && $display_refunded ) ? wc_price( WC_Tax::round( $tax->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ), array( 'currency' => $order->get_order_currency() ) ) : $tax->formatted_amount;
						$tax_string_array[] = sprintf( '%s %s', $tax_amount, $tax->label );
					}
				} else {
					$tax_amount         = ( $total_refunded && $display_refunded ) ? $order->get_total_tax() - $order->get_total_tax_refunded() : $order->get_total_tax();
					$tax_string_array[] = sprintf( '%s %s', wc_price( $tax_amount, array( 'currency' => $currency ) ), WC()->countries->tax_or_vat() );
				}
				if ( ! empty( $tax_string_array ) ) {
					$tax_string = ' ' . sprintf( __( '(Includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) );
				}
			}

			if ( $total_refunded && $display_refunded ) {
				$formatted_total = '<del>' . strip_tags( $formatted_total ) . '</del> <ins>' . wc_price( ( $order_total - $total_refunded ), array( 'currency' => $currency ) ) . $tax_string . '</ins>';
			} else {
				$formatted_total .= $tax_string;
			}

			return apply_filters( 'woocommerce_get_formatted_order_funds_total', $formatted_total, $order );
		}

		/**
		 * return order total with funds
		 *
		 * @param $total
		 * @param WC_Order $order
		 *
		 * @return mixed
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function show_order_total_include_funds( $total, $order ) {

			if ( ywf_order_has_deposit( $order ) ) {
				return $total;
			}
			$order_id = $order->get_id();
			$funds    = apply_filters( 'yith_show_funds_used_into_order_currency', $order->get_meta( '_order_funds' ), $order_id );

			if ( ! empty( $funds ) ) {

				$partial_payment = $order->get_meta( 'ywf_partial_payment' );

				if ( $total == 0 ) {
					return $total + floatval( $funds );
				}
				if ( 'yes' == $partial_payment ) {
					$paid_with_other_gateway = $order->get_meta( 'ywf_total_paid_with_other_gateway' );

					if ( empty( $paid_with_other_gateway ) ) {

						if( !is_callable( $order, 'get_total_fees')){
							$fees = $order->get_fees();
							$total_fees = 0;
							foreach ($fees as $fee ){
								$total_fees+=$fee->get_total();
							}
						}else{
							$total_fees = $order->get_total_fees();
						}
						
						$paid_with_other_gateway = ( $order->get_subtotal() + $total_fees + $order->get_shipping_total() );

						foreach ( $order->get_tax_totals() as $code => $tax_total ) {

							$paid_with_other_gateway += $tax_total->amount;
						}
						$paid_with_other_gateway -= $order->get_total_discount();

						$paid_with_other_gateway -= floatval( $funds );
					}

					return $paid_with_other_gateway ;
				}
			}

			return $total;
		}

		/**
		 * add order amount total filter
		 *
		 * @param $order_id
		 *
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function add_include_order_total_with_fund_filter( $order_id ) {

			add_filter( 'woocommerce_order_amount_total', array( $this, 'show_order_total_include_funds' ), 20, 2 );
		}


		public function remove_order_total_with_fund_filter() {

			remove_filter( 'woocommerce_order_amount_total', array( $this, 'show_order_total_include_funds' ), 20 );
		}


		/**
		 * add order item line into email
		 *
		 * @param array $total_rows
		 * @param WC_Order $order
		 *
		 * @return array
		 * @since 1.0.0
		 *
		 * @author YITH
		 */
		public function get_order_fund_item_total( $total_rows, $order ) {

			$order_id = $order->get_id();
			$fund     = apply_filters( 'yith_show_funds_used_into_order_currency', $order->get_meta( '_order_funds' ), $order_id );

			if ( ! empty( $fund ) ) {

				$currency           = $order->get_currency();
				$is_partial_payment = $order->get_meta( 'ywf_partial_payment' );

				if ( 'yes' == $is_partial_payment ) {

					$payment_method = $order->get_payment_method_title();

					$order_total    = $order->get_total() ;
					$index          = array_search( 'payment_method', array_keys( $total_rows ) );
					$payed_rows     = array(
						'ywf_fund_used'   => array(
							'label' => apply_filters( 'ywf_display_used_funds', __( 'Total with funds:', 'yith-woocommerce-account-funds' ) ),
							'value' => wc_price( $fund, array( 'currency' => $currency ) )
						),
						'ywf_payed_other' => array(
							'label' => sprintf( __( 'Total with %s: ', 'yith-woocommerce-account-funds' ), $payment_method ),
							'value' => wc_price( $order_total, array( 'currency' => $currency ) )
						)
					);

					$total_rows['order_total']['value'] = wc_price( $order_total + $fund, array( 'currency' => $currency ) );



				} else {
					$index = array_search( 'order_total', array_keys( $total_rows ) );

					$payed_rows = array(
						"ywf_funds_used" => array(
							'label' => apply_filters( 'ywf_display_used_funds', __( 'Funds used', 'yith-woocommerce-account-funds' ) ),
							'value' => wc_price( - $fund, array( 'currency' => $currency ) )
						)
					);
				}

				$total_rows = array_slice( $total_rows, 0, $index, true ) + $payed_rows + array_slice( $total_rows, $index, count( $total_rows ) - 1, true );


			}

			return $total_rows;
		}

		public function refund_deleted_order_funds( $refund_id, $order_id ) {

			$order          = wc_get_order( $order_id );
			$payment_method = $order->get_payment_method();
			$customer_id    = $order->get_user_id();


			if ( 'yith_funds' === $payment_method ) {

				$funds_refunded = apply_filters( 'yith_show_funds_used_into_order_currency', $order->get_meta( '_order_funds_refunded' ), $order_id );

				$total_refund = $order->get_total_refunded();
				$how_refund   = wc_format_decimal( $funds_refunded - $total_refund, wc_get_price_decimals() );

				$customer = new YITH_YWF_Customer( $customer_id );

				$how_refund_base_currency   = apply_filters( 'yith_how_refund_base_currency', $how_refund, $order_id );
				$total_refund_base_currency = apply_filters( 'yith_how_refund_base_currency', $total_refund, $order_id );
				$customer->decrement_funds( $how_refund_base_currency );

				$order->update_meta_data( '_order_funds_refunded', $total_refund_base_currency );
				$order->save();
				$order_note = sprintf( __( 'Removed %s funds from customer #%s account', 'yith-woocommorce-funds' ), wc_price( $how_refund ), $order->get_user_id() );
				$order->add_order_note( $order_note );

				$default = array(
					'user_id'        => $customer_id,
					'order_id'       => $order_id,
					'fund_user'      => $how_refund_base_currency,
					'type_operation' => 'pay'
				);


				do_action( 'ywf_add_user_log', $default );

			}
		}

		/**
		 * add custom view in order table
		 *
		 * @param $views
		 *
		 * @return mixed
		 * @author YITH
		 * @since 1.0.0
		 *
		 */
		public
		function add_order_deposit_view(
			$views
		) {

			$tot_order = $this->count_order_deposit();

			if ( $tot_order > 0 ) {
				$filter_url   = esc_url( add_query_arg( array(
					'post_type'         => 'shop_order',
					'ywf_order_deposit' => true
				), admin_url( 'edit.php' ) ) );
				$filter_class = isset( $_GET['ywf_order_deposit'] ) ? 'current' : '';

				$views['ywf_order_deposit'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>', $filter_url, $filter_class, __( 'Deposit', 'yith-woocommerce-account-funds' ), $tot_order );
			}

			return $views;
		}

		/**
		 * customize query
		 * @author YITH
		 * @since 1.0.0
		 */
		public
		function filter_order_deposit_for_view() {

			if ( isset( $_GET['ywf_order_deposit'] ) && $_GET['ywf_order_deposit'] ) {
				add_filter( 'posts_join', array( $this, 'filter_order_join_for_view' ) );
				add_filter( 'posts_where', array( $this, 'filter_order_where_for_view' ) );
			}
		}

		/**
		 * add joins to order view query
		 *
		 * @param $join
		 *
		 * @return string
		 * @author YITH
		 * @since 1.0.0
		 *
		 */
		public
		function filter_order_join_for_view(
			$join
		) {

			global $wpdb;

			$join .= " LEFT JOIN {$wpdb->prefix}postmeta as pm ON {$wpdb->posts}.ID = pm.post_id";

			return $join;
		}

		/**
		 * Add conditions to order view query
		 *
		 * @param $where string Original where query section
		 *
		 * @return string filtered where query section
		 * @author YITH
		 * @since 1.0.0
		 *
		 * @since 1.0.0
		 */
		public
		function filter_order_where_for_view(
			$where
		) {
			global $wpdb;

			$where .= $wpdb->prepare( " AND pm.meta_key = %s AND pm.meta_value = %s", array(
				'_order_has_deposit',
				'yes'
			) );

			return $where;
		}


		/**
		 * count order with deposit
		 * @return int
		 * @since 1.0.0
		 * @author YITH
		 */
		public
		function count_order_deposit() {
			global $wpdb;
			$query  = $wpdb->prepare( "SELECT DISTINCT COUNT(*) FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                                     WHERE {$wpdb->posts}.post_type = %s AND ( {$wpdb->postmeta}.meta_key=%s AND {$wpdb->postmeta}.meta_value = %s )",
				'shop_order', '_order_has_deposit', 'yes' );
			$result = $wpdb->get_var( $query );

			return $result;
		}

		/**
		 * @param WC_Order $order
		 */
		public
		function deposit_again(
			$order
		) {

			if ( ywf_order_has_deposit( $order ) ) {

				remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button', 10 );

				$total = $this->get_order_deposit_total( $order );

				$args = array(
					'text'   => __( 'Deposit again', 'yith-woocommerce-account-funds' ),
					'type'   => 'button',
					'amount' => $total
				);

				echo YITH_YWF_Shortcodes::make_a_deposit_small( $args );

			} else {
				add_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button', 10, 1 );
			}
		}

		/**
		 * @param WC_Order $order
		 *
		 * @return float
		 * @author YITH
		 * @since 1.0.11
		 * Return the order total excluding fees
		 *
		 */
		public
		function get_order_deposit_total(
			$order
		) {

			$total = 0;

			if ( ywf_order_has_deposit( $order ) ) {

				$total = $order->get_meta( '_order_deposit_amount' );
			}

			return $total;
		}

		/**
		 * remove deposit form calculate tax procedure
		 *
		 * @param array $items
		 * @param int $order_id
		 * @param string $country
		 *
		 * @return array
		 * @author YITH
		 * @since 1.0.0
		 *
		 */
		public
		function remove_deposit_from_items(
			$items, $order_id, $country
		) {

			$order = wc_get_order( $order_id );
			if ( ywf_order_has_deposit( $order ) ) {


				global $YITH_FUNDS;
				$order_item_id = $items['order_item_id'];
				foreach ( $order_item_id as $key => $item_id ) {

					$product_id = $YITH_FUNDS->is_wc_2_7 ? wc_get_order_item_meta( $item_id, '_product_id', true ) : $order->get_item_meta( $item_id, '_product_id', true );
					$product    = wc_get_product( $product_id );

					if ( $product->is_type( 'ywf_deposit' ) ) {
						unset( $items['order_item_id'][ $key ] );
						break;
					}

				}
			}

			return $items;
		}


		/**
		 *
		 */
		public function refund_funds_in_partial_payments() {

			if ( isset( $_REQUEST['funds_to_add'] ) ) {

				$fund_to_add_display = $_REQUEST['funds_to_add'];
				$customer_id         = $_REQUEST['customer_id'];
				$order_id            = $_REQUEST['order_id'];
				$order               = wc_get_order( $order_id );
				$customer            = new YITH_YWF_Customer( $customer_id );
				$fund_to_add         = apply_filters( 'yith_admin_deposit_funds', $fund_to_add_display, $order_id );
				$fund_to_add_display = wc_price( $fund_to_add_display, array( 'currency' => $order->get_currency() ) );
				$order_note          = sprintf( __( 'Add %s funds to customer #%s', 'yith-woocommorce-funds' ), $fund_to_add_display, $order->get_user_id() );


				$customer->add_funds( $fund_to_add );
				$default = array(
					'user_id'        => $order->get_user_id(),
					'order_id'       => $order_id,
					'fund_user'      => $fund_to_add,
					'type_operation' => 'admin_op',
					'description'    => sprintf( __( 'Add %s fund in the order #%s', 'yith-woocommerce-account-funds' ), $fund_to_add_display, $order_id )
				);
				do_action( 'ywf_add_user_log', $default );

				wp_send_json( $order_note );

			}
		}

		/**
		 * check if the order is a partial order and add hidden field
		 *
		 * @param int $order_id
		 *
		 * @since 1.3.0
		 * @author YITH
		 */
		public function add_order_custom_field( $order_id ) {

			$order = wc_get_order( $order_id );

			$partial_payment = $order->get_meta( 'ywf_partial_payment' );
			if ( 'yes' == $partial_payment ) {
				?>
                <input type="hidden" id="ywf_partial_payment" value="<?php echo $partial_payment; ?>">
				<?php
			}
		}



		/**
		 * add metabox for customer fund when the order is a partial payment
		 * @author YITH
		 * @since 1.3.0
		 */
		public function add_order_customer_funds_metabox() {

			global $post;

			if ( ! is_null( $post ) && 'shop_order' == get_post_type( $post ) ) {
				$order           = wc_get_order( $post );
				$partial_payment = $order->get_meta( 'ywf_partial_payment' );

				if ( apply_filters( 'yith_account_funds_show_order_metabox', 'yes' == $partial_payment, $partial_payment, $order ) ) {
					add_meta_box( 'yith-wc-order-account-funds-metabox', __( 'Account Funds', 'yith-woocommerce-delivery-date' ), array(
						$this,
						'order_customer_funds_meta_box_content'
					), 'shop_order', 'side', 'low' );
				}
			}
		}

		/**
		 * print metabox
		 * @author YITH
		 * @since 1.3.0
		 */
		public function order_customer_funds_meta_box_content() {
			include_once( 'admin/meta-boxes/html-account-fund-meta-box.php' );
		}

		/**
		 *
		 * @param WC_Order $order
		 */
		public function  fix_total_with_partial_payment( $order ){
			$partial_payment = $order->get_meta( 'ywf_partial_payment' );
			$to_pay_with_other_gateway = $order->get_meta('ywf_total_paid_with_other_gateway');

			if($order->has_status('failed') && 'yes' == $partial_payment && !empty( $to_pay_with_other_gateway ) ){
				$order->set_total($to_pay_with_other_gateway);
			}
		}


	}
}
function YITH_YWF_Order() {
	new YITH_YWF_Order();
}
