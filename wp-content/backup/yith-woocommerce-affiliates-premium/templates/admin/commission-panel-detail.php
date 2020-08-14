<?php
/**
 * Commission Details Admin Panel
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
?>

<form method="post" id="post" autocomplete="off">
	<input type="hidden" name="commission_id" id="commission_id" value="<?php echo $commission[ 'ID' ] ?>" />
	<input type="hidden" name="commissions[]" value="<?php echo $commission[ 'ID' ] ?>" />

	<div class="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">

					<!-- Commission Actions -->
					<div id="yith_wcaf_commission_actions" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php _e( 'Commission actions', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">
							<select name="action">
								<option value=""><?php _e( 'Action', 'yith-woocommerce-affiliates' ) ?></option>
								<?php if ( ! empty( $available_commission_actions ) ): ?>
									<?php foreach ( $available_commission_actions as $id => $label ): ?>
										<option value="<?php echo esc_attr( $id ) ?>"><?php echo esc_html( $label ) ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>

							<button class="button" title="<?php _e( 'Apply', 'yith-woocommerce-affiliates' ) ?>">
								<span><?php _e( 'Apply', 'yith-woocommerce-affiliates' ) ?></span></button>
						</div>
					</div>

					<!-- Commission Payments -->
					<div id="yith_wcaf_commission_payments" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php _e( 'Commission payments', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">
							<div class="woocommerce_order_items_wrapper">
								<table class="woocommerce_order_items wp-list-table">
									<thead>
									<tr>
										<th scope="col" class="column-id"><?php _e( 'ID', 'yith-woocommerce-affiliates' ) ?></th>
										<th scope="col" class="column-status">
											<span class="status_head tips" data-tip="<?php _e( 'Status', 'yith-woocommerce-affiliates' ) ?>"><?php _e( 'Status', 'yith-woocommerce-affiliates' ) ?></span>
										</th>
										<th scope="col" class="column-payment-email"><?php _e( 'Email', 'yith-woocommerce-affiliates' ) ?></th>
										<th scope="col" class="column-amount"><?php _e( 'Amount', 'yith-woocommerce-affiliates' ) ?></th>
									</tr>
									</thead>
									<?php
									if ( ! empty( $active_payments ) ):
										foreach ( $active_payments as $payment ):
											$payment_url           = esc_url( add_query_arg( array(
												'page'       => 'yith_wcaf_panel',
												'tab'        => 'payments',
												'payment_id' => $payment[ 'ID' ]
											), admin_url( 'admin.php' ) ) );
											$human_friendly_status = YITH_WCAF_Payment_Handler()->get_readable_status( $payment[ 'status' ] );
											?>
											<tr>
												<td class="column-id">
													<a href="<?php echo $payment_url ?>"><strong>#<?php echo esc_attr( $payment[ 'ID' ] ) ?></strong></a>
												</td>
												<td class="column-status">
													<mark class="<?php echo $payment[ 'status' ] ?> tips" data-tip="<?php echo $human_friendly_status ?>"><?php echo $human_friendly_status ?></mark>
												</td>
												<td class="column-payment-email">
													<a href="mailto:<?php echo esc_attr( $payment[ 'payment_email' ] ) ?>"><?php echo esc_attr( $payment[ 'payment_email' ] ) ?></a>
												</td>
												<td class="column-amount"><?php echo wc_price( $payment[ 'amount' ] ) ?></td>
											</tr>
											<?php
										endforeach;
									else:
										?>
										<tr>
											<td colspan="4"><?php _e( 'No active payments', 'yith-woocommerce-affiliates' ) ?></td>
										</tr>
									<?php endif; ?>

									<?php
									if ( ! empty( $inactive_payments ) ):
										foreach ( $inactive_payments as $payment ):
											$payment_url           = esc_url( add_query_arg( array(
												'page'       => 'yith_wcaf_panel',
												'tab'        => 'payments',
												'payment_id' => $payment[ 'ID' ]
											), admin_url( 'admin.php' ) ) );
											$human_friendly_status = YITH_WCAF_Payment_Handler()->get_readable_status( $payment[ 'status' ] );
											?>
											<tr class="inactive">
												<td class="column-id">
													<a href="<?php echo $payment_url ?>"><strong>#<?php echo esc_attr( $payment[ 'ID' ] ) ?></strong></a>
												</td>
												<td class="column-status">
													<mark class="<?php echo $payment[ 'status' ] ?> tips" data-tip="<?php echo $human_friendly_status ?>"><?php echo $human_friendly_status ?></mark>
												</td>
												<td class="column-payment-email">
													<a href="mailto:<?php echo esc_attr( $payment[ 'payment_email' ] ) ?>"><?php echo esc_attr( $payment[ 'payment_email' ] ) ?></a>
												</td>
												<td class="column-amount"><?php echo wc_price( $payment[ 'amount' ] ) ?></td>
											</tr>
											<?php
										endforeach;
									endif;
									?>
								</table>
							</div>
						</div>
					</div>

					<!-- Commission Notes -->
					<div id="yith_wcaf_commission_notes" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php _e( 'Commission notes', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">
							<ul class="order_notes">

								<?php if ( ! empty( $commission_notes ) ) : ?>
									<?php foreach ( $commission_notes as $note ) : ?>
										<li rel="<?php echo $note[ 'ID' ] ?>" class="note">
											<div class="note_content">
												<p><?php echo $note[ 'note_content' ] ?></p>
											</div>
											<p class="meta">
												<abbr class="exact-date" title="<?php echo $note[ 'note_date' ]; ?>"><?php printf( __( 'added on %1$s at %2$s', 'yith-woocommerce-affiliates' ), date_i18n( wc_date_format(), strtotime( $note[ 'note_date' ] ) ), date_i18n( wc_time_format(), strtotime( $note[ 'note_date' ] ) ) ); ?></abbr>
												<a href="#" class="delete_note"><?php _e( 'Delete note', 'yith-woocommerce-affiliates' ) ?></a>
											</p>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
								<li class="no_notes" <?php echo ( ! empty( $commission_notes ) ) ? 'style="display: none;"' : '' ?> ><?php _e( 'There are no notes yet.', 'yith-woocommerce-affiliates' ) ?></li>
							</ul>
							<div class="add_note">
								<h4><?php _e( 'Add note', 'yith-woocommerce-affiliates' ) ?></h4>

								<p>
									<textarea name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
								</p>

								<p>
									<a href="#" class="add_note button"><?php _e( 'Add', 'yith-woocommerce-affiliates' ) ?></a>
								</p>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">

							<div class="panel-wrap woocommerce">
								<div id="order_data" class="yith-commission panel">
									<h2>
										<?php printf( __( 'Commission %s Details', 'yith-woocommerce-affiliates' ), '#' . $commission[ 'ID' ] ) ?>
										<a href="<?php echo esc_url( remove_query_arg( 'commission_id' ) ) ?>" class="add-new-h2" id="back_to_commission"><?php _e( 'Back to commission table', 'yith-woocommerce-affiliates' ) ?></a>
									</h2>

									<p class="order_number">
										<?php
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
											$billing_last_name = yit_get_prop( $order, 'billing_last_name' );

											if ( $billing_first_name || $billing_last_name ) {
												$username = trim( $billing_first_name . ' ' . $billing_last_name );
											} else {
												$username = __( 'Guest', 'yith-woocommerce-affiliates' );
											}
										}

										$order_info      = sprintf( '<a href="%s">#%d</a>', 'post.php?post=' . absint( yit_get_order_id( $order ) ) . '&action=edit', $order->get_order_number() );
										$wc_order_status = wc_get_order_statuses();

										printf( _x( 'Credited to %s &#8212; Order: %s &#8212; Order status: %s', 'Commission credited to [user]', 'yith-wcaf' ), $username, $order_info, $wc_order_status[ 'wc-' . $order->get_status() ] );
										?>
									</p>

									<div class="order_data_column_container">
										<div class="order_data_column">

											<h4><?php _e( 'General details', 'yith-woocommerce-affiliates' ) ?></h4>

											<div class="address">
												<p>
													<strong><?php _e( 'Status', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php echo YITH_WCAF_Commission_Handler()->get_readable_status( $commission[ 'status' ] ) ?>
												</p>

												<p>
													<strong><?php _e( 'Commission date', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php echo date_i18n( wc_date_format(), strtotime( $commission[ 'created_at' ] ) ) ?>
												</p>

												<p>
													<strong><?php _e( 'Last update', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php
													$date   = ! empty( $commission[ 'last_edit' ] ) && strpos( $commission[ 'last_edit' ], '0000-00-00' ) === FALSE ? $commission[ 'last_edit' ] : $commission[ 'created_at' ];
													$t_time = date_i18n( __( 'Y/m/d g:i:s A' ), mysql2date( 'U', $date ) );
													$h_time = sprintf( __( '%s ago' ), human_time_diff( mysql2date( 'U', $date ) ) );

													echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
													?>
												</p>
											</div>

										</div>
										<div class="order_data_column">

											<h4><?php _e( 'User details', 'yith-woocommerce-affiliates' ) ?></h4>

											<div class="address">
												<p>
													<?php
													if ( ! empty( $user ) ) {
														printf( '<strong>%1$s:</strong>', __( 'Email', 'yith-woocommerce-affiliates' ) );
														printf( '<a href="mailto:%1$s">%1$s</a>', $user->user_email );
													} else {
														echo '<em>' . __( 'User deleted', 'yith-woocommerce-affiliates' ) . '</em>';
													}
													?>
												</p>
											</div>

										</div>
										<?php if ( ! empty( $user ) ) : ?>
											<div class="order_data_column">
												<h4><?php _e( 'Billing information', 'yith-woocommerce-affiliates' ) ?></h4>

												<div class="address">
													<p>
														<?php

														// Formatted Addresses
														$formatted = WC()->countries->get_formatted_address( array(
															'first_name' => $user->first_name,
															'last_name'  => $user->last_name,
															'company'    => $user->billing_company,
															'address_1'  => yit_get_prop( $order, 'billing_address_1' ),
															'address_2'  => yit_get_prop( $order, 'billing_address_2' ),
															'city'       => yit_get_prop( $order, 'billing_city' ),
															'state'      => yit_get_prop( $order, 'billing_state' ),
															'postcode'   => yit_get_prop( $order, 'billing_postcode' ),
															'country'    => yit_get_prop( $order, 'billing_country' ),
														) );

														echo wp_kses( $formatted, array( 'br' => array() ) )
														?>
													</p>
												</div>

												<h4><?php _e( 'Shipping information', 'yith-woocommerce-affiliates' ) ?></h4>

												<div class="address">
													<p>
														<?php

														// Formatted Addresses
														$formatted = WC()->countries->get_formatted_address( array(
															'first_name' => yit_get_prop( $order, 'shipping_first_name' ),
															'last_name'  => yit_get_prop( $order, 'shipping_last_name' ),
															'company'    => yit_get_prop( $order, 'shipping_company' ),
															'address_1'  => yit_get_prop( $order, 'shipping_address_1' ),
															'address_2'  => yit_get_prop( $order, 'shipping_address_2' ),
															'city'       => yit_get_prop( $order, 'shipping_city' ),
															'state'      => yit_get_prop( $order, 'shipping_state' ),
															'postcode'   => yit_get_prop( $order, 'shipping_postcode' ),
															'country'    => yit_get_prop( $order, 'shipping_country' ),
														) );

														echo wp_kses( $formatted, array( 'br' => array() ) )
														?>
													</p>
												</div>
											</div>
										<?php endif; ?>
									</div>

									<div class="clear"></div>

								</div>
							</div>

						</div>
					</div>

					<div id="woocommerce-order-items" class="postbox">
						<h3 class="hndle ui-sortable-handle"><span><?php _e( 'Item data', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">

							<div class="woocommerce_order_items_wrapper wc-order-items-editable">
								<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
									<thead>
									<tr>
										<th class="item sortable" colspan="2"><?php _e( 'Item', 'yith-woocommerce-affiliates' ) ?></th>
										<th class="quantity sortable"><?php _e( 'Qty', 'yith-woocommerce-affiliates' ) ?></th>
										<th class="item_cost sortable"><?php _e( 'Cost', 'yith.wcaf' ) ?></th>
										<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
									</tr>
									</thead>

									<tbody id="order_line_items">
									<tr class="item Zero Rate" data-order_item_id="<?php echo $commission[ 'line_item_id' ] ?>">

										<td class="thumb">
											<?php if ( $product ) :
												$product_id = yit_get_product_id( $product );
												?>
												<a class="wc-order-item-thumbnail" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $product_id ) . '&action=edit' ) ); ?>" class="tips" data-tip="<?php

												echo '<strong>' . __( 'Product ID:', 'yith-woocommerce-affiliates' ) . '</strong> ' . absint( $product_id );

												if ( $item[ 'variation_id' ] && 'product_variation' === get_post_type( $item[ 'variation_id' ] ) ) {
													echo '<br/><strong>' . __( 'Variation ID:', 'yith-woocommerce-affiliates' ) . '</strong> ' . absint( $item[ 'variation_id' ] );
												} elseif ( $item[ 'variation_id' ] ) {
													echo '<br/><strong>' . __( 'Variation ID:', 'yith-woocommerce-affiliates' ) . '</strong> ' . absint( $item[ 'variation_id' ] ) . ' (' . __( 'No longer exists', 'yith-woocommerce-affiliates' ) . ')';
												}

												if ( $product && $product->get_sku() ) {
													echo '<br/><strong>' . __( 'Product SKU:', 'yith-woocommerce-affiliates' ) . '</strong> ' . esc_html( $product->get_sku() );
												}

												?>"><?php echo $product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a>
											<?php else : ?>
												<?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
											<?php endif; ?>
										</td>

										<td class="name">

											<?php echo ( $product && $product->get_sku() ) ? esc_html( $product->get_sku() ) . ' &ndash; ' : ''; ?>

											<?php if ( $product ) : ?>
												<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $product_id ) . '&action=edit' ) ); ?>">
													<?php echo esc_html( $item[ 'name' ] ); ?>
												</a>
											<?php else : ?>
												<?php echo esc_html( $item[ 'name' ] ); ?>
											<?php endif; ?>

											<div class="view">
												<?php
												global $wpdb;
												$metadata = method_exists( $item, 'get_formatted_meta_data' ) ? $item->get_formatted_meta_data() : $order->has_meta( $commission[ 'line_item_id' ] );

												if ( $metadata ) {
													echo '<table cellspacing="0" class="display_meta">';
													foreach ( $metadata as $meta ) {

														$meta_key = is_object( $meta ) ? $meta->key : $meta['meta_key'];
														$meta_value = is_object( $meta ) ? $meta->value : $meta['meta_value'];

														// Skip hidden core fields
														if ( in_array( $meta_key, apply_filters( 'woocommerce_hidden_order_itemmeta', array(
															'_qty',
															'_tax_class',
															'_product_id',
															'_variation_id',
															'_line_subtotal',
															'_line_subtotal_tax',
															'_line_total',
															'_line_tax',
														) ) ) ) {
															continue;
														}

														// Skip serialised meta
														if ( is_serialized( $meta_value ) ) {
															continue;
														}

														// Get attribute data
														if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta_key ) ) ) {
															$term       = get_term_by( 'slug', $meta_value, wc_sanitize_taxonomy_name( $meta_key ) );
															$meta_key   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta_key ) );
															$meta_value = isset( $term->name ) ? $term->name : $meta_value;
														} else {
															$meta_key   = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta_key, $_product ), $meta_key );
														}

														echo '<tr><th>' . wp_kses_post( rawurldecode( $meta_key ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta_value ) ) ) ) . '</td></tr>';
													}
													echo '</table>';
												}
												?>
											</div>
										</td>

										<td class="quantity" width="1%">
											<div class="view">
												<?php
												echo ( isset( $item[ 'qty' ] ) ) ? esc_html( $item[ 'qty' ] ) : '';

												if ( $refunded_qty = $order->get_qty_refunded_for_item( $commission[ 'line_item_id' ] ) ) {
													echo '<small class="refunded">-' . $refunded_qty . '</small>';
												}
												?>
											</div>
										</td>

										<td class="item_cost" width="1%">
											<div class="view">
												<?php
												if ( isset( $item[ 'line_total' ] ) ) {
													if ( isset( $item[ 'line_subtotal' ] ) && $item[ 'line_subtotal' ] != $item[ 'line_total' ] ) {
														echo '<del>' . wc_price( $order->get_item_subtotal( $item, FALSE, TRUE ), array( 'currency' => $order_currency ) ) . '</del> ';
													}
													echo wc_price( $order->get_item_total( $item, FALSE, TRUE ), array( 'currency' => $order_currency ) );
												}
												?>
											</div>
										</td>

										<td class="line_tax" width="1%"></td>
									</tr>
									</tbody>

									<?php if ( ! empty( $refunds ) ): ?>
										<tbody id="order_refunds">
										<?php
										foreach ( $refunds as $refund_id => $amount ) :
											$refund = new WC_Order_Refund( $refund_id );

											if ( $refund_id = yit_get_order_id( $refund ) ):
												?>
												<tr class="refund Zero Rate">
													<td class="thumb">
														<div></div>
													</td>

													<td class="name">
                                                        <?php $refund_date = is_a( $refund, 'WC_Data' ) ? $refund->get_date_created() : $refund->post->post_date ?>
														<?php echo esc_attr__( 'Refund', 'yith-woocommerce-affiliates' ) . ' #' . absint( $refund_id ) . ' - ' . esc_attr( date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), strtotime( $refund_date ) ) ); ?>
														<?php if ( $refund_reason = yit_get_refund_reason( $refund ) ) : ?>
															<p class="description"><?php echo esc_html( $refund_reason ); ?></p>
														<?php endif; ?>
													</td>

													<td class="quantity" width="1%">&nbsp;</td>

													<td class="line_cost" width="1%">
														<div class="view">
															<?php echo wc_price( $amount ) ?>
														</div>
													</td>

													<td class="line_tax" width="1%"></td>
												</tr>
												<?php
											endif;
										endforeach;
										?>
										</tbody>
									<?php endif; ?>

								</table>
							</div>

							<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">

								<?php
								$coupons = $order->get_items( array( 'coupon' ) );
								if ( $coupons ) {
									?>
									<div class="wc-used-coupons">
										<ul class="wc_coupon_list"><?php
											echo '<li><strong>' . __( 'Coupon(s) Used', 'yith-woocommerce-affiliates' ) . '</strong></li>';
											foreach ( $coupons as $item_id => $item ) {
												global $wpdb;
												$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item[ 'name' ] ) );

												$link_before = $link_after = '';
												if ( current_user_can( 'manage_woocommerce' ) ) {
													$link        = $post_id ? esc_url( add_query_arg( array(
														'post'   => $post_id,
														'action' => 'edit'
													), admin_url( 'post.php' ) ) ) : add_query_arg( array(
														's'           => $item[ 'name' ],
														'post_status' => 'all',
														'post_type'   => 'shop_coupon'
													), admin_url( 'edit.php' ) );
													$link_before = '<a href="' . esc_url( $link ) . '" class="tips" data-tip="' . esc_attr( wc_price( $item[ 'discount_amount' ], array( 'currency' => $order_currency ) ) ) . '">';
													$link_after  = '</a>';
												}

												printf( '<li class="code">%s<span>' . esc_html( $item[ 'name' ] ) . '</span>%s</li>', $link_before, $link_after );
											}
											?></ul>
									</div>
									<?php
								}
								?>

								<table class="wc-order-totals">
									<tbody>

									<tr>
										<td class="label"><?php _e( 'Rate', 'yith-woocommerce-affiliates' ) ?>:</td>
										<td class="total"><?php echo number_format( $commission[ 'rate' ], 2 ) ?>%</td>
										<td width="1%"></td>
									</tr>

									<tr>
										<td class="label"><?php _e( 'Commission', 'yith-woocommerce-affiliates' ) ?>:</td>
										<td class="total">
											<?php echo str_replace( array(
												'<span class="amount">',
												'</span>'
											), '', wc_price( $commission[ 'amount' ] + abs( $total_refunded ) ) ) ?>
										</td>
										<td width="1%"></td>
									</tr>

									<?php if ( ! empty( $refunds ) ) : ?>
										<tr>
											<td class="label refunded-total">Refunded:</td>
											<td class="total refunded-total"><?php echo wc_price( $total_refunded ) ?></td>
											<td width="1%"></td>
										</tr>
									<?php endif; ?>

									<tr>
										<td class="label">Total:</td>
										<td class="total"><?php echo wc_price( $commission[ 'amount' ] ) ?></td>
										<td width="1%"></td>
									</tr>

									</tbody>
								</table>
								<div class="clear"></div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>