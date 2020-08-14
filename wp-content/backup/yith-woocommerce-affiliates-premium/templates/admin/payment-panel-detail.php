<?php
/**
 * Payment Details Admin Panel
 *
 * @author Your Inspiration Themes
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

<form method="post" autocomplete="off">
	<input type="hidden" name="payment_id" id="payment_id" value="<?php echo $payment['ID'] ?>" />
	<input type="hidden" name="payments[]" value="<?php echo $payment['ID'] ?>" />
	<div class="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">

					<!-- Payment Actions -->
					<div id="yith_wcaf_payment_actions" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php _e( 'Payment actions', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">
							<select name="action">
								<option value=""><?php _e( 'Action', 'yith-woocommerce-affiliates' ) ?></option>
								<?php if ( ! empty( $available_payment_actions ) ): ?>
									<?php foreach ( $available_payment_actions as $id => $label ): ?>
										<option value="<?php echo esc_attr( $id ) ?>"><?php echo esc_html( $label ) ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>

							<button class="button" title="<?php _e( 'Apply', 'yith-woocommerce-affiliates' ) ?>">
								<span><?php _e( 'Apply', 'yith-woocommerce-affiliates' ) ?></span></button>
						</div>
					</div>

					<!-- Payment Affiliate -->
					<div id="yith_wcaf_payment_affiliate" class="postbox">
	                    <h3 class="hndle ui-sortable-handle">
	                        <span><?php _e( 'Affiliate', 'yith-woocommerce-affiliates' ) ?></span></h3>

	                    <div class="inside">
						    <div class="referral-user">
							    <div class="referral-avatar">
								    <?php echo get_avatar( $payment['affiliate_id'], 64 ); ?>
							    </div>
							    <div class="referral-info">
								    <h3><a href="<?php echo get_edit_user_link( $payment['affiliate_id'] ) ?>"><?php echo $user_name ?></a></h3>
								    <a href="mailto:<?php echo $user_email?>"><?php echo $user_email?></a>
							    </div>
						    </div>
						    <div class="referral-stats woocommerce_order_items_wrapper">
							    <table class="woocommerce_order_items wp-list-table">
								    <tbody>
								    <tr>
									    <td class="label"><?php _e( 'Earnings:', 'yith-woocommerce-affiliates' ) ?></td>
									    <td class="total"><strong><?php echo wc_price( $payment['affiliate_earnings'] ) ?></strong></td>
								    </tr>
								    <tr>
									    <td class="label"><?php _e( 'Paid:', 'yith-woocommerce-affiliates' ) ?></td>
									    <td class="total"><strong><?php echo wc_price( $payment['affiliate_paid'] ) ?></strong></td>
								    </tr>
								    </tbody>
							    </table>
						    </div>
	                    </div>
	                </div>

					<!-- Payment Notes -->
					<div id="yith_wcaf_payment_notes" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php _e( 'Payment notes', 'yith-woocommerce-affiliates' ) ?></span></h3>

						<div class="inside">
							<ul class="order_notes">

								<?php if ( ! empty( $payment_notes ) ) : ?>
									<?php foreach ( $payment_notes as $note ) : ?>
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
								<li class="no_notes" <?php echo ( ! empty( $payment_notes ) ) ? 'style="display: none;"' : '' ?> ><?php _e( 'There are no notes yet.', 'yith-woocommerce-affiliates' ) ?></li>
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
								<div id="order_data" class="yith-payment panel">
									<h2>
										<?php
										printf( __( 'Payment %s Details', 'yith-woocommerce-affiliates' ), '#' . $payment['ID'] );
										?>
										<a href="<?php echo esc_url( remove_query_arg( 'payment_id' ) ) ?>" class="add-new-h2" id="back_to_payment"><?php _e( 'Back to payment table', 'yith-woocommerce-affiliates' ) ?></a>
									</h2>

									<p class="order_number">
										<?php
										$edit_user_link = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';
										$edit_user_link .= $user_name;
										$edit_user_link .= '</a>';


										printf( _x( 'Paid to %s &#8212; Payment via %s', 'Commission credited to [user]', 'yith-woocommerce-affiliates' ), $edit_user_link, $payment_label );

										if( $payment_trans_key ) {
											printf( _x( '(Trans. key: %s)', 'Payment transaction key', 'yith-woocommerce-affiliates' ), $payment_trans_key );
										}
										?>
									</p>

									<div class="order_data_column_container">
										<div class="order_data_column">

											<h4><?php _e( 'General details', 'yith-woocommerce-affiliates' ) ?></h4>

											<div class="address">
												<p>
													<strong><?php _e( 'Status', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php echo YITH_WCAF_Commission_Handler()->get_readable_status( $payment['status'] ) ?>
												</p>

												<p>
													<strong><?php _e( 'Payment date', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php echo date_i18n( wc_date_format(), strtotime( $payment['created_at'] ) ) ?>
												</p>

												<p>
													<strong><?php _e( 'Completed at', 'yith-woocommerce-affiliates' ) ?>:</strong>
													<?php
													$date   = ! empty( $payment['completed_at'] ) && strpos( $payment['completed_at'], '0000-00-00' ) === false ? $payment['completed_at'] : '';

													if( ! empty( $date ) ):
														$t_time = date_i18n( __( 'Y/m/d g:i:s A' ), mysql2date( 'U', $date ) );
														$h_time = sprintf( __( '%s ago' ), human_time_diff( mysql2date( 'U', $date ) ) );

														echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
													else:
														echo __( 'N/A', 'yith-woocommerce-affiliates' );
													endif;
													?>
												</p>

                                                <?php if( isset( $invoice ) ): ?>
                                                    <strong><?php _e( 'Customer invoice', 'yith-woocommerce-affiliates' ) ?>:</strong>
                                                    <a href="<?php echo YITH_WCAF_Payment_Handler()->get_invoice_publishable_url( $payment['ID'] ) ?>"><?php echo $invoice ?></a>
                                                <?php endif; ?>
											</div>

										</div>
										<div class="order_data_column">

											<h4>
												<?php _e( 'User details', 'yith-woocommerce-affiliates' ) ?>
												<a class="edit_address_button" href="#"><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="<?php _e( 'Edit', 'woocommerce' ); ?>" width="14"></a>
											</h4>

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
												<p>
													<?php
													printf( '<strong>%1$s:</strong>', __( 'Payment Email', 'yith-woocommerce-affiliates' ) );
													printf( '<a href="mailto:%1$s">%1$s</a>', $payment['payment_email'] );
													?>
												</p>
											</div>

											<div class="edit_address">
												<p class="form-field form-field-wide">
													<label for="_payment_email"><?php _e( 'Payment email', 'yith-woocommerce-affiliates' ) ?></label>
													<input type="email" name="_payment_email" id="_payment_email" value="<?php echo $payment['payment_email'] ?>" />
												</p>
												<p class="form-field">
													<input type="submit" name="update_payment_email" class="button button-primary" value="<?php _e( 'Update', 'yith-woocommerce-affiliates' ) ?>" />
												</p>
											</div>

                                            <h4>
	                                            <?php _e( 'Current Invoice Profile', 'yith-woocommerce-affiliates' ) ?>
                                                <a class="edit_address_button" href="#"><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="<?php _e( 'Edit', 'woocommerce' ); ?>" width="14"></a>
                                            </h4>

                                            <div class="address">
                                                <p>
                                                    <?php echo $formatted_invoice_profile ?>
                                                </p>
                                            </div>

                                            <div class="edit_address">
                                                <p class="form-field">
                                                    <label for="from"><?php _e( 'From', 'yith-woocommerce-affiliates' ) ?></label>
                                                    <input type="text" class="date-picker" name="from" id="from" />
                                                </p>
                                                <p class="form-field to-field">
                                                    <label for="to"><?php _e( 'To', 'yith-woocommerce-affiliates' ) ?></label>
                                                    <input type="text" class="date-picker" name="to" id="to" />
                                                </p>
                                                <?php
                                                    if( ! empty( $invoice_fields ) ):
                                                        foreach( $invoice_fields as $field => $label ):
                                                            if( 'type' == $field ):
	                                                            ?>
                                                                <p class="form-field form-field-wide">
                                                                    <label for="type"><?php echo $label ?></label>
                                                                    <select name="type" id="type">
                                                                        <option value="personal" <?php selected( ! isset( $invoice_profile['type'] ) || $invoice_profile['type'] == 'personal' ) ?> ><?php _e( 'Personal', 'yith-woocommerce-affiliates' ) ?></option>
                                                                        <option value="business" <?php selected( isset( $invoice_profile['type'] ) && $invoice_profile['type'] == 'business' ) ?> ><?php _e( 'Business', 'yith-woocommerce-affiliates' ) ?></option>
                                                                    </select>
                                                                </p>
                                                                <?php
                                                            else:
                                                                ?>
                                                                <p class="form-field form-field-wide">
                                                                    <label for="<?php echo $field ?>"><?php echo $label ?></label>
                                                                    <input type="text" name="<?php echo $field ?>" id="<?php echo $field ?>" value="<?php echo isset( $invoice_profile[ $field ] ) ? $invoice_profile[ $field ] : '' ?>" />
                                                                </p>
                                                                <?php
                                                            endif;
                                                        endforeach;
                                                    endif;
                                                ?>

                                                <p class="form-field">
                                                    <input type="submit" class="button button-primary" name="regenerate_invoice" value="<?php _e( 'Regenerate Invoice', 'yith-woocommerce-affiliates' ) ?>" />
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
															'address_1'  => get_user_meta( $user->ID, 'billing_address_1', true ),
															'address_2'  => get_user_meta( $user->ID, 'billing_address_2', true ),
															'city'       => get_user_meta( $user->ID, 'billing_city', true ),
															'state'      => get_user_meta( $user->ID, 'billing_state', true ),
															'postcode'   => get_user_meta( $user->ID, 'billing_postcode', true ),
															'country'    => get_user_meta( $user->ID, 'billing_country', true ),
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
															'first_name' => get_user_meta( $user->ID, 'shipping_first_name', true ),
															'last_name'  => get_user_meta( $user->ID, 'shipping_last_name', true ),
															'company'    => get_user_meta( $user->ID, 'shipping_company', true ),
															'address_1'  => get_user_meta( $user->ID, 'shipping_address_1', true ),
															'address_2'  => get_user_meta( $user->ID, 'shipping_address_2', true ),
															'city'       => get_user_meta( $user->ID, 'shipping_city', true ),
															'state'      => get_user_meta( $user->ID, 'shipping_state', true ),
															'postcode'   => get_user_meta( $user->ID, 'shipping_postcode', true ),
															'country'    => get_user_meta( $user->ID, 'shipping_country', true ),
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
										<th class="commission sortable"><?php _e( 'Commission', 'yith-woocommerce-affiliates' ) ?></th>
										<th class="date sortable" width="10%"><?php _e( 'Date', 'yith-woocommerce-affiliates' ) ?></th>
										<th class="item_cost sortable"><?php _e( 'Cost', 'yith.wcaf' ) ?></th>
										<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
									</tr>
									</thead>

									<?php if( ! empty( $commissions ) ): ?>
										<tbody id="order_line_items">
										<?php
										foreach( $commissions as $commission ):
											$commission_url = esc_url( add_query_arg( array( 'page' => 'yith_wcaf_panel', 'tab' => 'commissions', 'commission_id' => $commission['ID'] ), admin_url( 'admin.php' ) ) );
											$commission_order_url = get_edit_post_link( $commission['order_id'] );
											?>
											<tr class="item Zero Rate" data-commission_id="<?php echo $commission['ID'] ?>">

												<td class="name">
													<?php echo sprintf( '<a href="%s">#%d</a> &#8212; %s <a href="%s">#%d</a>', $commission_url, $commission['ID'], __( 'Order:', 'yith-woocommerce-affiliates' ), $commission_order_url, $commission['order_id'] ) ?>
												</td>

												<td class="date" width="10%">
													<div class="view">
														<?php echo date_i18n( wc_date_format(), strtotime( $commission['created_at'] ) ) ?>
													</div>
												</td>

												<td class="item_cost" width="1%">
													<div class="view">
														<?php echo wc_price( $commission['amount'] ) ?>
													</div>
												</td>

												<td class="line_tax" width="1%"></td>

											</tr>
										<?php endforeach; ?>
										</tbody>
									<?php endif; ?>

								</table>
							</div>

							<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
								<table class="wc-order-totals">
									<tbody>

									<tr>
										<td class="label">Total:</td>
										<td class="total"><?php echo wc_price( $payment['amount'] ) ?></td>
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