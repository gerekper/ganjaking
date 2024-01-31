<?php
/**
 * Processing of smart coupons refund
 *
 * @author      StoreApps
 * @since       5.2.0
 * @version     2.1.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Refund_Process' ) ) {

	/**
	 * Class for handling processes of smart coupons refund
	 */
	class WC_SC_Coupon_Refund_Process {


		/**
		 * Variable to hold instance of WC_SC_Coupon_Refund_Process
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'woocommerce_admin_order_items_after_shipping', array( $this, 'render_used_store_credits_details' ) );
			add_action( 'woocommerce_admin_order_items_after_refunds', array( $this, 'render_refunded_store_credits_details' ) );
			add_filter( 'woocommerce_order_fully_refunded_status', array( $this, 'update_fully_refunded_status' ), 10, 3 );
			add_action( 'wp_ajax_wc_sc_refund_store_credit', array( $this, 'wc_sc_refund_store_credit' ) );
			add_action( 'wp_ajax_wc_sc_revoke_refunded_store_credit', array( $this, 'wc_sc_revoke_refunded_store_credit' ) );
		}

		/**
		 * Get single instance of WC_SC_Coupon_Process
		 *
		 * @return WC_SC_Coupon_Process Singleton object of WC_SC_Coupon_Process
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Render smart coupon UI in order page
		 *
		 * @param int $order_id order id.
		 * @return void html of smart coupon UI
		 */
		public function render_used_store_credits_details( $order_id = 0 ) {
			?>
			<style type="text/css">
				#order_sc_store_credit_line_items tbody tr:first-child td {
					border-top: 8px solid #f8f8f8;
				}
			</style>
			<script type="text/javascript">
				jQuery(function ($) {

					/**
					 * Auto fill and remove - refund store credit coupon amount
					 */
					$(document).on('change', '.sc_auto_fill_refund', function () {
						if ($(this).prop("checked") === true) {
							$('.sc_store_credit').each(function (index, element) {
								let used_sc = $(element).data("order_used_sc");
								let order_used_sc_tax = $(element).data("order_used_sc_tax");
								$(this).find('.refund_used_sc').val(used_sc);
								$(this).find('.refund_used_sc_tax').val(order_used_sc_tax);
							});
						} else {
							$('.refund_used_sc').val(0);
							$('.refund_used_sc_tax').val(0);
						}
					});

					/**
					 * Process store credit refund
					 */
					$('#woocommerce-order-items').on('click', 'button.do-api-refund, button.do-manual-refund', function () {

						var sc_line_item = {};

						$('.sc_store_credit').each(function (index, element) {
							let sc_line_item_data = {};
							let used_coupon_id = $(this).data("used_sc_id");
							let order_id = $(this).data("used_sc_order_id");
							let sc_refund_amount = $(this).find('.refund_used_sc').val();
							let sc_refund_tax_amount = $(this).find('.refund_used_sc_tax').val();
							let order_used_total_amount = $(this).find('.order_used_total_sc_amount').val();
							let order_sc_item_id = $(this).find('.order_sc_item_id').val();
							if (order_sc_item_id !== 0 || order_sc_item_id !== null || order_sc_item_id !== undefined) {
								sc_line_item_data['coupon_id'] = used_coupon_id;
								sc_line_item_data['refund_amount'] = sc_refund_amount;
								sc_line_item_data['order_used_total_amount'] = order_used_total_amount;
								sc_line_item_data['order_sc_item_id'] = order_sc_item_id;
								sc_line_item_data['sc_order_id'] = order_id;
								sc_line_item_data['order_sc_refund_tax_amount'] = sc_refund_tax_amount;
								sc_line_item [order_sc_item_id] = sc_line_item_data;
							}
						});

						let sc_refund_nonce = $('#sc_refund_nonce').val();
						var data = {
							action: 'wc_sc_refund_store_credit',
							line_items: JSON.stringify(sc_line_item, null, ''),
							security: sc_refund_nonce
						};
						$.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'POST',
							data: data
						});
					});

				});

			</script>

			<tbody id="order_sc_store_credit_line_items">
			<?php
			$order       = ( function_exists( 'wc_get_order' ) && ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;
			$order_total = $this->is_callable( $order, 'get_total' ) ? $order->get_total() : 0;
			$order_items = ( is_object( $order ) && $this->is_callable( $order, 'get_items' ) ) ? $order->get_items( 'coupon' ) : array();
			$tax_data    = ( function_exists( 'wc_tax_enabled' ) && wc_tax_enabled() && is_object( $order ) && $this->is_callable( $order, 'get_taxes' ) ) ? $order->get_taxes() : array();

			if ( ! class_exists( 'WC_SC_Order_Fields' ) ) {
				include_once 'class-wc-sc-order-fields.php';
			}
			$sc_order_fields            = WC_SC_Order_Fields::get_instance();
			$total_credit_used_in_order = $sc_order_fields->get_total_credit_used_in_order( $order );

			$i = 1;
			if ( ! empty( $order_items ) ) {
				$is_apply_before_tax        = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );
				$is_readonly                = ( 'yes' !== $is_apply_before_tax ) ? true : false;
				$allow_custom_refund_amount = apply_filters(
					'wc_sc_allow_custom_refund_amount',
					( false === $is_readonly ),
					array(
						'source'    => $this,
						'order_obj' => $order,
					)
				);
				$is_old_sc_order            = $this->is_old_sc_order( $order_id );
				$item_titles                = array_map(
					function( $item ) {
						return ( $this->is_callable( $item, 'get_name' ) ) ? $item->get_name() : '';
					},
					$order_items
				);
				$posts                      = $this->get_post_by_title( $item_titles, OBJECT, 'shop_coupon' );
				foreach ( $order_items as $item_id => $item ) {
                    $order_discount_amount = $sc_refunded_discount = $sc_refunded_discount_tax = $order_discount_tax_amount = 0; // phpcs:ignore
					$coupon_code           = ( $this->is_callable( $item, 'get_name' ) ) ? $item->get_name() : '';
					$sanitized_coupon_code = sanitize_title( $coupon_code ); // The generated string will be checked in an array key to locate post object.
					$coupon_post_obj       = ( ! empty( $posts[ $sanitized_coupon_code ] ) ) ? $posts[ $sanitized_coupon_code ] : null;
					$coupon_id             = isset( $coupon_post_obj->ID ) ? $coupon_post_obj->ID : '';
					$coupon_title          = isset( $coupon_post_obj->post_title ) ? $coupon_post_obj->post_title : '';
					$coupon                = new WC_Coupon( $coupon_id );
					if ( is_a( $coupon, 'WC_Coupon' ) ) {
						if ( $coupon->is_type( 'smart_coupon' ) ) {

							if ( 1 === $i ) {
								?>

								<tr class="sc_store_credit_head refund">
									<td class="">
										<div class="refund" style="display: none; padding-top: 15px;">
											<svg xmlns="http://www.w3.org/2000/svg" style="color: #b5b5b5;" fill="none" viewBox="0 0 40 40" stroke="currentColor" class="w-6 h-6">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
												</path>
											</svg>
										</div>
									</td>
									<td>
										<div class="refund" style="display: none; padding-top: 15px;">
											<div class="view"><?php echo esc_html__( 'Refund to Store Credit', 'woocommerce-smart-coupons' ); ?></div>
										</div>
									</td>
									<td>
									</td>
									<td>
										<input type="hidden" name="sc_refund_nonce" id="sc_refund_nonce" value="<?php echo esc_attr( wp_create_nonce( 'sc_refund_nonce' ) ); ?>">
									</td>
									<td colspan="2">
										<?php if ( true === $is_old_sc_order || 'yes' === $is_apply_before_tax ) { ?>
											<div class="refund" style="display: none; padding-top: 15px;">
												<input type="checkbox" name="sc_auto_fill_refund" class="sc_auto_fill_refund" id="sc_auto_fill_refund">
												<label for="sc_auto_fill_refund"><?php echo esc_html_e( 'Auto-fill refund amount', 'woocommerce-smart-coupons' ); ?></label>
											</div>
										<?php } ?>
									</td>
									<?php
									if ( ! empty( $tax_data ) ) {
										$tax_data_count = count( $tax_data );
										?>
									<td colspan="<?php echo esc_attr( ( $tax_data_count ) ); ?>">
									</td>
										<?php
									}
									?>
								</tr>
								<?php
							}
							$i++;

							if ( is_callable( array( $this, 'get_order_item_meta' ) ) ) {
								$order_discount_amount     = (float) $this->get_order_item_meta( $item_id, 'discount_amount', true );
								$sc_refunded_discount      = (float) $this->get_order_item_meta( $item_id, 'sc_refunded_discount', true );
								$sc_refunded_discount_tax  = (float) $this->get_order_item_meta( $item_id, 'sc_refunded_discount_tax', true );
								$order_discount_tax_amount = (float) $this->get_order_item_meta( $item_id, 'discount_amount_tax', true );
							}

							?>
							<tr class="sc_store_credit" data-order_used_sc="<?php echo esc_attr( $order_discount_amount ); ?>"
								data-used_sc_id="<?php echo esc_attr( $coupon_id ); ?>"
								data-used_sc_order_id="<?php echo esc_attr( $order_id ); ?>"
								data-order_used_sc_tax="<?php echo esc_attr( $order_discount_tax_amount ); ?>">
								<td class="thumb">
									<div>
										<svg xmlns="http://www.w3.org/2000/svg" style="color: #b5b5b5;" fill="none" viewBox="0 0 40 40" stroke="currentColor" class="w-6 h-6">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
											</path>
										</svg>
									</div>
								</td>

								<td class="name">
									<div class="view">
										<?php
										 echo esc_html( $coupon_title ); // phpcs:ignore
										?>
									</div>
								</td>

								<td class="item_cost" width="1%">&nbsp;</td>
								<td class="quantity" width="1%">&nbsp;</td>

								<td class="line_cost" width="1%">
									<div class="view">
										<?php
										echo wp_kses_post(
											wc_price( $order_discount_amount )
										);

										if ( ! empty( $sc_refunded_discount ) ) {
											?>
											<small class="refunded"><?php echo wp_kses_post( wc_price( $sc_refunded_discount ) ); ?></small>
											<?php
										}

										$max_refund_limit = $order_discount_amount - $sc_refunded_discount;
										?>
									</div>
									<div class="edit" style="display: none;">
										<input type="text" name="sc_store_credit_cost[<?php echo esc_attr( $coupon_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $order_discount_amount ); ?>" class="sc_line_total wc_input_price">
									</div>
									<?php if ( $sc_refunded_discount < $order_discount_amount ) { ?>
										<div class="refund" style="display: none;">
											<input type="text" name="refund_sc_store_credit_line_total[<?php echo esc_attr( $coupon_id ); ?>]" placeholder="0" class="refund_used_sc wc_input_price" max="<?php echo esc_attr( $max_refund_limit ); ?>" <?php ( ( false === $allow_custom_refund_amount ) ? esc_attr_e( 'readonly' ) : '' ); ?>>
											<input type="hidden" name="order_used_total_sc_amount[<?php echo esc_attr( $coupon_id ); ?>]" value="<?php echo esc_attr( $order_discount_amount ); ?>" class="order_used_total_sc_amount">
											<input type="hidden" name="order_used_item_id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item_id ); ?>" class="order_sc_item_id">
										</div>
									<?php } ?>
								</td>
								<?php
								if ( ! empty( $tax_data ) ) {
									$tax_data_count = count( $tax_data );
									?>
									<td class="line_tax" width="1%">
										<div class="view">
											<?php
											echo wp_kses_post(
												wc_price( $order_discount_tax_amount )
											);

											if ( ! empty( $sc_refunded_discount_tax ) ) {
												?>
												<small class="refunded"><?php echo wp_kses_post( wc_price( $sc_refunded_discount_tax ) ); ?></small>
												<?php
											}

											$max_refund_tax_limit = $order_discount_tax_amount - $sc_refunded_discount_tax;
											?>
										</div>
										<div class="edit" style="display: none;">
											<input type="text" name="sc_store_credit_cost[<?php echo esc_attr( $coupon_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $order_discount_tax_amount ); ?>" class="sc_line_total wc_input_price">
										</div>
										<?php if ( $sc_refunded_discount_tax < $order_discount_tax_amount ) { ?>
											<div class="refund" style="display: none;">
												<input type="text" name="refund_sc_store_credit_line_total[<?php echo esc_attr( $coupon_id ); ?>]" placeholder="0" class="refund_used_sc_tax wc_input_price" max="<?php echo esc_attr( $max_refund_tax_limit ); ?>" <?php ( ( false === $allow_custom_refund_amount ) ? esc_attr_e( 'readonly' ) : '' ); ?>>
											</div>
										<?php } ?>
									</td>
									<?php if ( $tax_data_count > 1 ) { ?>
										<td colspan="<?php echo esc_attr( ( $tax_data_count - 1 ) ); ?>"></td>
									<?php } ?>
								<?php } ?>
								<td class="wc-order-edit-line-item">
								</td>
							</tr>
							<?php
						}
					}
				}
			}
			?>
			</tbody>
			<?php if ( ! $this->is_old_sc_order( $order_id ) && $total_credit_used_in_order > 0 ) { ?>
			<script type="text/javascript">
				jQuery(function(){
					function wc_sc_reload_refund_amount() {
						var order_total              = '<?php echo esc_html( $order_total ); ?>';
						var refund_amount            = jQuery('#refund_amount').val();
						var wc_sc_refund_line_total = 0;
						var wc_sc_refund_line_tax = 0;
						jQuery('.line_cost .refund .refund_line_total').each(function() {
							wc_sc_refund_line_unit_total = accounting.unformat( jQuery(this).val(), woocommerce_admin.mon_decimal_point );
							wc_sc_refund_line_total += wc_sc_refund_line_unit_total;
						});
						jQuery('.line_tax .refund .refund_line_tax').each(function() {
							wc_sc_refund_line_unit_tax = accounting.unformat( jQuery(this).val(), woocommerce_admin.mon_decimal_point );
							wc_sc_refund_line_tax += wc_sc_refund_line_unit_tax;
						});
						jQuery('.refund_used_sc').each(function(){
							var max_limit = accounting.unformat( jQuery(this).attr('max') );
							wc_sc_refund_line_total = accounting.unformat( wc_sc_refund_line_total, woocommerce_admin.mon_decimal_point );
							var new_refund_amount = Math.min( wc_sc_refund_line_total, max_limit );
							jQuery(this).val( 
								accounting.formatNumber(
									new_refund_amount,
									woocommerce_admin_meta_boxes.currency_format_num_decimals,
									'',
									woocommerce_admin.mon_decimal_point
								)
							);
							wc_sc_refund_line_total -= new_refund_amount;
						});
						jQuery('.refund_used_sc_tax').each(function(){
							var max_limit = accounting.unformat( jQuery(this).attr('max') );
							wc_sc_refund_line_tax = accounting.unformat( wc_sc_refund_line_tax, woocommerce_admin.mon_decimal_point );
							var new_refund_amount = Math.min( wc_sc_refund_line_tax, max_limit );
							jQuery(this).val( 

								accounting.formatNumber(
									new_refund_amount,
									woocommerce_admin_meta_boxes.currency_format_num_decimals,
									'',
									woocommerce_admin.mon_decimal_point
								)
							);
							wc_sc_refund_line_tax -= new_refund_amount;
						});
						refund_amount = wc_sc_refund_line_total + wc_sc_refund_line_tax;
						if ( refund_amount > 0 ) {
							refund_amount = Math.min(refund_amount, order_total);
						}

						jQuery('button.do-api-refund, button.do-manual-refund').find('.wc-order-refund-amount .amount' ).text( accounting.formatMoney( refund_amount, {
							symbol:    woocommerce_admin_meta_boxes.currency_format_symbol,
							decimal:   woocommerce_admin_meta_boxes.currency_format_decimal_sep,
							thousand:  woocommerce_admin_meta_boxes.currency_format_thousand_sep,
							precision: woocommerce_admin_meta_boxes.currency_format_num_decimals,
							format:    woocommerce_admin_meta_boxes.currency_format
						} ) );

						if ( refund_amount <= 0 ) {
							jQuery('#refund_amount').val('');
							jQuery('button.do-api-refund').hide();
						} else {
							jQuery('#refund_amount').val( 
								accounting.formatNumber(
									refund_amount,
									woocommerce_admin_meta_boxes.currency_format_num_decimals,
									'',
									woocommerce_admin.mon_decimal_point
								)
							);
							jQuery('button.do-api-refund').show();
						}
					}
					jQuery(document).on('change', '#refund_amount', function(){
						wc_sc_reload_refund_amount();
					});
				});
			</script>
			<?php } ?>
			<?php
		}

		/**
		 * Refund store credit coupon
		 *
		 * @return void add refund store credit to the coupon
		 */
		public function wc_sc_refund_store_credit() {
			global $woocommerce_smart_coupon;
			$nonce_token = ! empty( $_POST['security'] ) ? $_POST['security'] : '';  // phpcs:ignore
			if ( wp_verify_nonce( wp_unslash( $nonce_token ), 'sc_refund_nonce' ) ) {
				$response   = array();
				$line_items = isset( $_POST['line_items'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_items'] ) ), true ) : array();
				if ( ! empty( $line_items ) ) {
					$order = null;
					foreach ( $line_items as $line_item ) {
                        $total_refunded             = $total_refunded_tax = $order_discount_amount = $refunded_amount = $refunded_tax_amount = $order_discount_tax_amount = 0;  // phpcs:ignore
						$smart_coupon_id            = ( ! empty( $line_item['coupon_id'] ) ) ? $line_item['coupon_id'] : 0;
						$refund_amount              = ( ! empty( $line_item['refund_amount'] ) ) ? wc_format_decimal( $line_item['refund_amount'] ) : 0;
						$item_id                    = ( ! empty( $line_item['order_sc_item_id'] ) ) ? $line_item['order_sc_item_id'] : 0;
						$order_sc_refund_tax_amount = ( ! empty( $line_item['order_sc_refund_tax_amount'] ) ) ? wc_format_decimal( $line_item['order_sc_refund_tax_amount'] ) : 0;
						$order_id                   = ( ! empty( $line_item['sc_order_id'] ) ) ? $line_item['sc_order_id'] : 0;
						$order                      = ( ! is_a( $order, 'WC_Order' ) ) ? wc_get_order( $order_id ) : $order;

						if ( is_callable( array( $this, 'get_order_item_meta' ) ) ) {
							$order_discount_amount     = $this->get_order_item_meta( $item_id, 'discount_amount', true );
							$refunded_amount           = $this->get_order_item_meta( $item_id, 'sc_refunded_discount', true );
							$refunded_tax_amount       = $this->get_order_item_meta( $item_id, 'sc_refunded_discount_tax', true );
							$order_discount_tax_amount = $this->get_order_item_meta( $item_id, 'discount_amount_tax', true );
						}

						if ( floatval( $order_discount_amount ) === floatval( $refunded_amount ) && floatval( $order_discount_tax_amount ) === floatval( $refunded_tax_amount ) ) {
							continue;
						}

						if ( $refunded_amount ) {
							$total_refunded = $refund_amount + $refunded_amount;
						}

						if ( $refunded_tax_amount ) {
							$total_refunded_tax = $order_sc_refund_tax_amount + $refunded_tax_amount;
						}
						if ( $order_discount_amount >= $refund_amount && $order_discount_amount >= $total_refunded && $order_discount_tax_amount >= $order_sc_refund_tax_amount && $order_discount_tax_amount >= $total_refunded_tax && ! empty( $smart_coupon_id ) ) {
							$coupon = new WC_Coupon( $smart_coupon_id );

							if ( is_a( $coupon, 'WC_Coupon' ) ) {
								if ( $this->is_wc_gte_30() ) {
									$discount_type = ( is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
								} else {
									$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
								}
								$coupon_amount = $this->get_amount( $coupon, true, $order );
								if ( 'smart_coupon' === $discount_type && is_numeric( $refund_amount ) && is_numeric( $order_sc_refund_tax_amount ) ) {
									$amount = $coupon_amount + $refund_amount + $order_sc_refund_tax_amount;
									$this->update_post_meta( $smart_coupon_id, 'coupon_amount', $amount, true, $order );
									$user               = ( function_exists( 'get_current_user_id' ) ) ? get_current_user_id() : 0;
									$local_time         = ( function_exists( 'current_datetime' ) ) ? current_datetime() : '';
									$get_timestamp      = ( is_object( $local_time ) && is_callable( array( $local_time, 'getTimestamp' ) ) ) ? $local_time->getTimestamp() : '';
									$get_offset         = ( is_object( $local_time ) && is_callable( array( $local_time, 'getOffset' ) ) ) ? $local_time->getOffset() : '';
									$current_time_stamp = $get_timestamp + $get_offset;

									if ( 0 < $total_refunded ) {
										$refund_amount = $total_refunded;
									}

									if ( 0 < $total_refunded_tax ) {
										$order_sc_refund_tax_amount = $total_refunded_tax;
									}

									if ( is_callable( array( $this, 'update_order_item_meta' ) ) ) {
										$this->update_order_item_meta( $item_id, 'sc_refunded_discount_tax', $order_sc_refund_tax_amount );
										$this->update_order_item_meta( $item_id, 'sc_refunded_discount', $refund_amount );
										$this->update_order_item_meta( $item_id, 'sc_refunded_user_id', $user );
										$this->update_order_item_meta( $item_id, 'sc_refunded_timestamp', $current_time_stamp );
										$this->update_order_item_meta( $item_id, 'sc_refunded_coupon_id', $smart_coupon_id );
										$message = __( 'Successfully updated store credit refund details.', 'woocommerce-smart-coupons' );
									} else {
										$message = __( 'Failed to update store credit refund details.', 'woocommerce-smart-coupons' );
										$woocommerce_smart_coupon->log( 'notice', $message . ' ' . __FILE__ . ' ' . __LINE__ );
									}
									$response['message'] = $message;
								}
							}
						}
					}
				}
				wp_send_json_success( $response );
			} else {
				$response['message'] = __( 'Nonce verification failed for action "wc_sc_refund_store_credit".', 'woocommerce-smart-coupons' );
				wp_send_json_error( $response );
			}
		}

		/**
		 * Render refund store credit UI in order page
		 *
		 * @param int $order_id order id.
		 * @return void revoke refund html
		 */
		public function render_refunded_store_credits_details( $order_id ) {
			global $store_credit_label;
			?>
			<style type="text/css">
				#woocommerce-order-items .wc-order-edit-line-item-actions .delete_wc_sc_refund::before {
					font-family: Dashicons;
					font-weight: 400;
					text-transform: none;
					line-height: 1;
					-webkit-font-smoothing: antialiased;
					text-indent: 0px;
					top: 0px;
					left: 0px;
					width: 100%;
					height: 100%;
					text-align: center;
					content: "";
					position: relative;
					font-variant: normal;
					margin: 0px;
					color: #a00;
				}
			</style>

			<script type="text/javascript">
				jQuery(function ($) {
					/**
					 * Process store credit revoke refund
					 */
					var wc_sc_meta_boxes_order_items = {
						block: function () {
							$('#woocommerce-order-items').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
						},
					};

					$('#woocommerce-order-items').on('click', '.delete_wc_sc_refund', function () {
						var confirm_message = '<?php esc_html_e( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce-smart-coupons' ); ?>';
						if (confirm(confirm_message)) {
							let sc_line_item_data = {};

							let wc_sc_refunded_id = $(this).parents('.wc_sc_refunded').data("wc_sc_refunded_id");
							let order_id = $(this).parents('.wc_sc_refunded').data("wc_sc_order_id");
							let wc_sc_id = $(this).parents('.wc_sc_refunded').data("wc_sc_coupon_id");
							let wc_sc_refunded_amount = $(this).parents('.wc_sc_refunded').data("wc_sc_refunded_amount");
							let wc_sc_refunded_amount_tax = $(this).parents('.wc_sc_refunded').data("wc_sc_refunded_amount_tax");


							if (wc_sc_refunded_id !== 0 || wc_sc_refunded_id !== null || wc_sc_refunded_id !== undefined) {
								sc_line_item_data['coupon_id'] = wc_sc_id;
								sc_line_item_data['refund_amount'] = wc_sc_refunded_amount;
								sc_line_item_data['refund_amount_tax'] = wc_sc_refunded_amount_tax;
								sc_line_item_data['wc_sc_refunded_id'] = wc_sc_refunded_id;
								sc_line_item_data['wc_sc_order_id'] = order_id;
							}
							let sc_revoke_refund_nonce = $('#sc_revoke_refund_nonce').val();
							var data = {
								action: 'wc_sc_revoke_refunded_store_credit',
								line_items: JSON.stringify(sc_line_item_data, null, ''),
								security: sc_revoke_refund_nonce
							};
							wc_sc_meta_boxes_order_items.block();

							$.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'POST',
								data: data,

								success: function (response) {
									if (true === response.success) {
										// Redirect to same page for show the refunded status
										window.location.reload();
									} else {
										console.log(response.data.error);
									}
								}
							});
						}
					})

				});

			</script>
			<?php
			$order = wc_get_order( $order_id );

			$order_items = ( is_object( $order ) && is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items( 'coupon' ) : array();
			$tax_data    = ( function_exists( 'wc_tax_enabled' ) && wc_tax_enabled() && is_object( $order ) && is_callable( array( $order, 'get_taxes' ) ) ) ? $order->get_taxes() : array();
			if ( ! empty( $order_items ) ) {
				$item_titles = array_map(
					function( $item ) {
						return $item->get_name();
					},
					$order_items
				);
				$posts       = $this->get_post_by_title( $item_titles, OBJECT, 'shop_coupon' );
				foreach ( $order_items as $item_id => $item ) {
					$sc_refunded_discount = $sc_refunded_discount_tax = $sc_refunded_user = $sc_refunded_timestamp = 0; // phpcs:ignore
					$coupon_code           = ( $this->is_callable( $item, 'get_name' ) ) ? $item->get_name() : '';
					$sanitized_coupon_code = sanitize_title( $coupon_code ); // The generated string will be checked in an array key to locate post object.
					$coupon_post_obj       = ( ! empty( $posts[ $sanitized_coupon_code ] ) ) ? $posts[ $sanitized_coupon_code ] : null;
					$coupon_id             = isset( $coupon_post_obj->ID ) ? $coupon_post_obj->ID : '';
					$coupon_title          = isset( $coupon_post_obj->post_title ) ? $coupon_post_obj->post_title : '';
					$coupon                = new WC_Coupon( $coupon_id );
					if ( is_callable( array( $this, 'get_order_item_meta' ) ) ) {
						$sc_refunded_discount     = (float) $this->get_order_item_meta( $item_id, 'sc_refunded_discount', true );
						$sc_refunded_discount_tax = (float) $this->get_order_item_meta( $item_id, 'sc_refunded_discount_tax', true );
						$sc_refunded_user         = $this->get_order_item_meta( $item_id, 'sc_refunded_user_id', true );
						$sc_refunded_timestamp    = $this->get_order_item_meta( $item_id, 'sc_refunded_timestamp', true );
					}
					if ( empty( $sc_refunded_timestamp ) ) {
						$sc_refunded_timestamp = time() + $this->wc_timezone_offset();
					}
					$sc_refunded_discount     = empty( $sc_refunded_discount ) ? 0 : $sc_refunded_discount;
					$sc_refunded_discount_tax = empty( $sc_refunded_discount_tax ) ? 0 : $sc_refunded_discount_tax;

					if ( is_a( $coupon, 'WC_Coupon' ) ) {
						if ( $coupon->is_type( 'smart_coupon' ) && ( ! empty( $sc_refunded_discount ) || ! empty( $sc_refunded_discount_tax ) ) ) {
							$who_refunded  = new WP_User( $sc_refunded_user );
							$refunder_id   = isset( $who_refunded->ID ) ? $who_refunded->ID : 0;
							$refunder_name = isset( $who_refunded->display_name ) ? $who_refunded->display_name : '';
							?>
							<input type="hidden" name="sc_revoke_refund_nonce" id="sc_revoke_refund_nonce" value="<?php echo esc_attr( wp_create_nonce( 'sc_revoke_refund_nonce' ) ); ?>">
							<tr class="refund wc_sc_refunded" data-wc_sc_refunded_id="<?php echo esc_attr( $item_id ); ?>"
								data-wc_sc_coupon_id="<?php echo esc_attr( $coupon_id ); ?>"
								data-wc_sc_order_id="<?php echo esc_attr( $order_id ); ?>"
								data-wc_sc_refunded_amount_tax="<?php echo esc_attr( $sc_refunded_discount_tax ); ?>"
								data-wc_sc_refunded_amount="<?php echo esc_attr( $sc_refunded_discount ); ?>">
								<td class="thumb">
									<div></div>
								</td>
								<td class="name">
									<?php
									if ( $who_refunded->exists() ) {
										printf(
											/* translators: 1: refund id 2: refund date 3: username */
											esc_html__( 'Refund %1$s - %2$s by %3$s', 'woocommerce-smart-coupons' ),
											sprintf( '%s - %s', ( ! empty( $store_credit_label['singular'] ) ? esc_html( $store_credit_label['singular'] ) : esc_html__( 'Store Credit', 'woocommerce-smart-coupons' ) ), esc_html( $coupon_title ) ),
											esc_html( $this->format_date( $sc_refunded_timestamp ) ),
											sprintf(
												'<abbr class="refund_by" title="%1$s">%2$s</abbr>',
												/* translators: 1: ID who refunded */
												sprintf( esc_attr__( 'ID: %d', 'woocommerce-smart-coupons' ), absint( $refunder_id ) ),
												esc_html( $refunder_name )
											)
										);
									} else {
										printf(
											/* translators: 1: refund id 2: refund date */
											esc_html__( 'Refund %1$s - %2$s', 'woocommerce-smart-coupons' ),
											sprintf( '%s - %s', ( ! empty( $store_credit_label['singular'] ) ? esc_html( $store_credit_label['singular'] ) : esc_html__( 'Store Credit', 'woocommerce-smart-coupons' ) ), esc_html( $coupon_title ) ),
											esc_html( $this->format_date( $sc_refunded_timestamp ) )
										);
									}
									?>
								</td>

								<td class="item_cost" width="1%">&nbsp;</td>
								<td class="quantity" width="1%">&nbsp;</td>

								<td class="line_cost" width="1%">
									<div class="view">
										<?php

										$total = $sc_refunded_discount + $sc_refunded_discount_tax;

										echo wp_kses_post(
											wc_price( '-' . $total )
										);
										?>
									</div>
								</td>

								<?php if ( ! empty( $tax_data ) ) : ?>
									<td class="line_tax" width="1%"></td>
									<?php
									$tax_data_count = count( $tax_data );
									if ( $tax_data_count > 1 ) {
										?>
									<td colspan="<?php echo esc_attr( $tax_data_count - 1 ); ?>"></td>
									<?php } ?>
								<?php endif; ?>

								<td class="wc-order-edit-line-item">
									<div class="wc-order-edit-line-item-actions">
										<a class="delete_wc_sc_refund" href="#" style=""></a>
									</div>
								</td>
							</tr>
							<?php
						}
					}
				}
			}

		}

		/**
		 * Formatting the date
		 *
		 * @param timestamp $date timestamp for date.
		 * @param string    $format date format.
		 * @return string  date format string
		 */
		protected function format_date( $date, $format = '' ) {
			if ( ! is_int( $date ) ) {
				$date = intval( $date );
			}
			if ( empty( $format ) ) {
				$format = get_option( 'date_format', 'F j, Y' ) . ' ' . get_option( 'time_format', 'g:i a' );
			}
			return gmdate( $format, $date );
		}

		/**
		 * Revoke refund store credit
		 *
		 * @return void remove refund store credit amount to the coupon
		 */
		public function wc_sc_revoke_refunded_store_credit() {
			$nonce_token = ! empty( $_POST['security'] ) ? $_POST['security'] : ''; // phpcs:ignore
			if ( wp_verify_nonce( wp_unslash( $nonce_token ), 'sc_revoke_refund_nonce' ) ) {
				$line_item = ! empty( $_POST['line_items'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_items'] ) ), true ) : array();
				if ( ! empty( $line_item ) ) {
					$smart_coupon_id           = ! empty( $line_item['coupon_id'] ) ? $line_item['coupon_id'] : 0;
					$refunded_amount           = ! empty( $line_item['refund_amount'] ) ? $line_item['refund_amount'] : 0;
					$refunded_amount_tax       = ! empty( $line_item['refund_amount_tax'] ) ? $line_item['refund_amount_tax'] : 0;
					$wc_sc_refunded_id         = ! empty( $line_item['wc_sc_refunded_id'] ) ? $line_item['wc_sc_refunded_id'] : 0;
					$order_id                  = ! empty( $line_item['wc_sc_order_id'] ) ? $line_item['wc_sc_order_id'] : 0;
					$order                     = ( ! is_a( $order, 'WC_Order' ) ) ? wc_get_order( $order_id ) : $order;
					$order_discount_amount     = $this->get_order_item_meta( $wc_sc_refunded_id, 'discount_amount', true );
					$order_discount_tax_amount = $this->get_order_item_meta( $wc_sc_refunded_id, 'discount_amount_tax', true );
					if ( $order_discount_amount >= $refunded_amount && $order_discount_tax_amount >= $refunded_amount_tax && ! empty( $smart_coupon_id ) ) {
						$coupon = new WC_Coupon( $smart_coupon_id );
						if ( is_a( $coupon, 'WC_Coupon' ) ) {
							if ( $this->is_wc_gte_30() ) {
								$discount_type = ( is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
							} else {
								$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							}
							$coupon_amount = $this->get_amount( $coupon, true, $order );
							if ( 'smart_coupon' === $discount_type ) {
								if ( ! is_numeric( $refunded_amount ) ) {
									$refunded_amount = 0;
								}
								if ( ! is_numeric( $refunded_amount_tax ) ) {
									$refunded_amount_tax = 0;
								}
								$refund_amount = $refunded_amount + $refunded_amount_tax;
								$amount        = $coupon_amount - $refund_amount;
								$this->update_post_meta( $smart_coupon_id, 'coupon_amount', $amount, true, $order );
								$user               = ( function_exists( 'get_current_user_id' ) ) ? get_current_user_id() : 0;
								$local_time         = ( function_exists( 'current_datetime' ) ) ? current_datetime() : '';
								$get_timestamp      = ( is_object( $local_time ) && is_callable( array( $local_time, 'getTimestamp' ) ) ) ? $local_time->getTimestamp() : 0;
								$get_offset         = ( is_object( $local_time ) && is_callable( array( $local_time, 'getOffset' ) ) ) ? $local_time->getOffset() : 0;
								$current_time_stamp = $get_timestamp + $get_offset;

								if ( is_callable( array( $this, 'update_order_item_meta' ) ) ) {
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_revoke_refunded_discount', $refunded_amount, true );
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_revoke_refunded_discount_tax', $refunded_amount_tax, true );
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_revoke_refunded_user_id', $user );
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_revoke_refunded_timestamp', $current_time_stamp );
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_revoke_refunded_coupon_id', $smart_coupon_id );

									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_refunded_discount', 0 );
									$this->update_order_item_meta( $wc_sc_refunded_id, 'sc_refunded_discount_tax', 0 );
								}
							}
						}
					}
				}
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}

		/**
		 * Change order status when fully refunded.
		 *
		 * @param string $status order status.
		 * @param number $order_id order id.
		 * @param number $refund_id refund id.
		 * @return false|mixed order status
		 */
		public function update_fully_refunded_status( $status, $order_id, $refund_id ) {
			$order       = wc_get_order( $order_id );
			$order_items = ( is_object( $order ) && is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items( 'coupon' ) : array();
			if ( ! empty( $order_items ) ) {
				$item_titles = array_map(
					function( $item ) {
						return $item->get_name();
					},
					$order_items
				);
				$posts       = $this->get_post_by_title( $item_titles, OBJECT, 'shop_coupon' );
				foreach ( $order_items as $item_id => $item ) {
					$sc_refunded_discount = $sc_refunded_discount_tax = $order_discount_amount = $order_discount_tax_amount = 0; // phpcs:ignore
					$coupon_code           = ( $this->is_callable( $item, 'get_name' ) ) ? $item->get_name() : '';
					$sanitized_coupon_code = sanitize_title( $coupon_code ); // The generated string will be checked in an array key to locate post object.
					$coupon_post_obj       = ( ! empty( $posts[ $sanitized_coupon_code ] ) ) ? $posts[ $sanitized_coupon_code ] : null;
					$coupon_id             = isset( $coupon_post_obj->ID ) ? $coupon_post_obj->ID : '';
					$coupon                = new WC_Coupon( $coupon_id );
					if ( is_callable( array( $this, 'get_order_item_meta' ) ) ) {
						$sc_refunded_discount      = $this->get_order_item_meta( $item_id, 'sc_refunded_discount', true );
						$sc_refunded_discount_tax  = $this->get_order_item_meta( $item_id, 'sc_refunded_discount_tax', true );
						$order_discount_amount     = $this->get_order_item_meta( $item_id, 'discount_amount', true );
						$order_discount_tax_amount = $this->get_order_item_meta( $item_id, 'discount_amount_tax', true );
					}

					if ( is_a( $coupon, 'WC_Coupon' ) ) {
						if ( $coupon->is_type( 'smart_coupon' ) ) {
							$get_order_total      = ( is_object( $order ) && is_callable( array( $order, 'get_total' ) ) ) ? $order->get_total() : 0;
							$refunded_order_total = ( is_object( $order ) && is_callable( array( $order, 'get_total_refunded' ) ) ) ? $order->get_total_refunded() : 0;
							if ( empty( $get_order_total ) && empty( $sc_refunded_discount_tax ) && empty( $order_discount_tax_amount ) && $order_discount_amount !== $sc_refunded_discount ) {
								return false;
							} elseif ( empty( $get_order_total ) && ! empty( $sc_refunded_discount_tax ) && ! empty( $order_discount_tax_amount ) && $order_discount_amount !== $sc_refunded_discount && $sc_refunded_discount_tax !== $order_discount_tax_amount ) {
								return false;
							} elseif ( ! empty( $get_order_total ) && empty( $sc_refunded_discount_tax ) && empty( $order_discount_tax_amount ) && $get_order_total === $refunded_order_total && $order_discount_amount !== $sc_refunded_discount ) {
								return false;
							} elseif ( ! empty( $get_order_total ) && ! empty( $sc_refunded_discount_tax ) && ! empty( $order_discount_tax_amount ) && $get_order_total === $refunded_order_total && $order_discount_amount !== $sc_refunded_discount && $sc_refunded_discount_tax !== $order_discount_tax_amount ) {
								return false;
							} elseif ( empty( $sc_refunded_discount_tax ) && ! empty( $order_discount_tax_amount ) ) {
								return false;
							}
						}
					}
				}
			}
			return $status;
		}
	}
}
WC_SC_Coupon_Refund_Process::get_instance();
