<?php
/**
 * Affiliate Details Admin Panel
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
	<input type="hidden" name="affiliate_id" id="affiliate_id" value="<?php echo esc_attr( $affiliate['ID'] ); ?>" />
	<input type="hidden" name="affiliates[]" value="<?php echo esc_attr( $affiliate['ID'] ); ?>" />

	<div class="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">

					<!-- Affiliates Actions -->
					<div id="yith_wcaf_affiliate_actions" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php esc_html_e( 'Affiliate actions', 'yith-woocommerce-affiliates' ); ?></span></h3>

						<div class="inside">
							<select name="action">
								<option value=""><?php esc_html_e( 'Action', 'yith-woocommerce-affiliates' ); ?></option>
								<?php if ( ! empty( $available_affiliate_actions ) ) : ?>
									<?php foreach ( $available_affiliate_actions as $id => $label ) : ?>
										<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>

							<button class="button" title="<?php esc_html_e( 'Apply', 'yith-woocommerce-affiliates' ); ?>">
								<span><?php esc_html_e( 'Apply', 'yith-woocommerce-affiliates' ); ?></span>
							</button>
							<hr>
							<button class="button save_affiliate button-primary" name="save" value="<?php esc_attr_e( 'Update', 'yith-woocommerce-affiliates' ); ?>"><?php esc_html_e( 'Update', 'yith-woocommerce-affiliates' ); ?></button>
						</div>
					</div>

					<!-- Link generator -->
					<div id="yith_wcwl_affiliate_url" class="postbox">
						<h3 class="hndle ui-sortable-handle">
							<span><?php esc_html_e( 'Referral link generator', 'yith-wcaf-affiliates' ); ?></span></h3>
						<div class="inside">
							<div class="panel-wrap woocommerce">
								<p class="form form-row">
									<label for="original_url"><?php esc_html_e( 'Page URL', 'yith-woocommerce-affiliates' ); ?></label>
									<input type="url" name="original_url" id="original_url" value="<?php echo esc_attr( $original_url ); ?>" />
								</p>

								<p class="form form-row">
									<label for="generated_url"><?php esc_html_e( 'Referral URL', 'yith-woocommerce-affiliates' ); ?></label>
									<input class="copy-target" readonly="readonly" type="url" name="generated_url" id="generated_url" value="<?php echo esc_attr( $generated_url ); ?>" />
									<?php echo ( ! empty( $generated_url ) ) ? sprintf( '<small>%s <span class="copy-trigger">%s</span> %s</small>', esc_html__( '(Now', 'yith-woocommerce-affiliates' ), esc_html__( 'copy', 'yith-woocommerce-affiliates' ), esc_html__( 'this referral link and share it anywhere)', 'yith-woocommerce-affiliates' ) ) : ''; ?>
								</p>

								<hr>

								<input type="submit" class="generate_link button button-secondary" value="<?php esc_attr_e( 'Generate', 'yith-woocommerce-affiliates' ); ?>" />
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
								<div id="order_data" class="yith-affiliate panel">
									<h2>
										<?php
											// translators: 1. Affiliate username.
											echo sprintf( __( '<b>%s</b> Details', 'yith-woocommerce-affiliates' ), $username ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
										<a href="<?php echo esc_url( remove_query_arg( 'affiliate_id' ) ); ?>" class="add-new-h2" id="back_to_affiliate"><?php esc_html_e( 'Back to affiliates table', 'yith-woocommerce-affiliates' ); ?></a>
									</h2>

									<div class="order_data_column_container">
										<p>
											<strong><?php esc_html_e( 'Referral link:' ); ?></strong>
											<?php echo esc_url( YITH_WCAF()->get_referral_url( $affiliate['token'] ) ); ?>
										</p>
									</div>

									<div class="order_data_column_container affiliate-stats">
										<div class="order_data_column">
											<div class="address">
												<p>
													<strong><?php esc_html_e( 'Earnings:', 'yith-woocommerce-affiliates' ); ?></strong>
													<?php echo wc_price( $affiliate['earnings'] + $affiliate['refunds'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</p>
											</div>
										</div>
										<div class="order_data_column">
											<div class="address">
												<p>
													<strong><?php esc_html_e( 'Refunds:', 'yith-woocommerce-affiliates' ); ?></strong>
													<?php echo wc_price( -1 * $affiliate['refunds'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</p>
											</div>
										</div>
										<div class="order_data_column">
											<div class="address">
												<p>
													<strong><?php esc_html_e( 'Paid:', 'yith-woocommerce-affiliates' ); ?></strong>
													<?php echo wc_price( $affiliate['paid'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</p>
											</div>
										</div>
										<div class="order_data_column">
											<div class="address">
												<p>
													<strong><?php esc_html_e( 'Active balance:', 'yith-woocommerce-affiliates' ); ?></strong>
													<?php echo wc_price( $affiliate['earnings'] - $affiliate['paid'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												</p>
											</div>
										</div>
									</div>

									<div class="clear"></div>

								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-4" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">
							<div class="panel-wrap woocommerce">
								<div id="affiliate_commissions" class="yith-affiliate-commissions panel">

									<?php
									$commissions_url = add_query_arg( array(
										'page' => 'yith_wcaf_panel',
										'tab' => 'commissions',
										'_user_id' => $affiliate['user_id']
									), admin_url( 'admin.php' ) );
									?>

									<h3>
										<?php esc_html_e( 'Last affiliate commissions', 'yith-wcaf-affiliates' ); ?>
										<a href="<?php echo esc_url( $commissions_url ); ?>" class="add-new-h2" id="back_to_commission"><?php esc_html_e( 'See all commissions', 'yith-woocommerce-affiliates' ); ?></a>
									</h3>
									<?php if ( ! empty( $commissions ) ) : ?>
										<div id="woocommerce-order-items">
											<div class="woocommerce_order_items_wrapper">
												<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
													<thead>
													<tr>
														<th><?php esc_html_e( 'ID', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Date', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Status', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Product', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Rate', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Amount', 'yith-woocommerce-affiliates' ); ?></th>
													</tr>
													</thead>
													<tbody>
													<?php foreach ( $commissions as $commission ) : ?>
														<?php $commission_url = add_query_arg( 'commission_id', $commission['ID'], $commissions_url ); ?>
														<tr>
															<td><?php printf( '<a href="%s">#%s</a>', $commission_url, $commission['ID'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
															<td><?php echo date_i18n( wc_date_format(), strtotime( $commission['created_at'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
															<td><?php echo esc_html( YITH_WCAF_Commission_Handler()->get_readable_status( $commission['status'] ) ); ?></td>
															<td><?php echo esc_html( $commission['product_name'] ); ?></td>
															<td><?php echo esc_html( number_format( $commission['rate'], 2 ) ); ?> %</td>
															<td><?php echo wc_price( $commission['amount'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</div>
										</div>
									<?php else : ?>
										<p class="no-items-found"><?php esc_html_e( 'No commission registered yet', 'yith-woocommerce-affiliates' ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-5" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">

							<div class="panel-wrap woocommerce">
								<div id="affiliate_payments" class="yith-affiliate-payments panel">
									<?php
									$payments_url = add_query_arg( array(
										'page' => 'yith_wcaf_panel',
										'tab' => 'payments',
										'_affiliate_id' => $affiliate['ID']
									), admin_url( 'admin.php' ) );
									?>

									<h3>
										<?php esc_html_e( 'Last affiliate payments', 'yith-wcaf-affiliates' ); ?>
										<a href="<?php echo esc_url( $payments_url ); ?>" class="add-new-h2" id="back_to_payment"><?php esc_html_e( 'See all payments', 'yith-woocommerce-affiliates' ); ?></a>
									</h3>
									<?php if ( ! empty( $payments ) ) : ?>
										<div id="woocommerce-order-items">
											<div class="woocommerce_order_items_wrapper">
												<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
													<thead>
													<tr>
														<th><?php esc_html_e( 'ID', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Status', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Amount', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Created at', 'yith-woocommerce-affiliates' ); ?></th>
														<th><?php esc_html_e( 'Completed at', 'yith-woocommerce-affiliates' ); ?></th>
													</tr>
													</thead>
													<tbody>
													<?php foreach ( $payments as $payment ) : ?>

														<?php $payment_url = add_query_arg( 'payment_id', $payment['ID'], $payments_url ); ?>

														<tr>
															<td><?php printf( '<a href="%s">#%s</a>', $payment_url, $payment['ID'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
															<td><?php echo esc_html( YITH_WCAF_Payment_Handler()->get_readable_status( $payment['status'] ) ); ?></td>
															<td><?php echo wc_price( $payment['amount'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
															<td><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $payment['created_at'] ) ) ); ?></td>
															<td><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $payment['completed_at'] ) ) ); ?></td>
														</tr>
													<?php endforeach; ?>
													</tbody>
												</table>
											</div>
										</div>
									<?php else : ?>
										<p class="no-items-found"><?php esc_html_e( 'No payment registered yet', 'yith-woocommerce-affiliates' ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-5" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">

							<div class="panel-wrap woocommerce">
								<div id="affiliate_users" class="yith-affiliate-users panel">
									<h3>
										<?php esc_html_e( 'Affiliate associated users', 'yith-wcaf-affiliates' ); // @since 1.7.1 ?>
									</h3>
									<?php if ( ! empty( $associated_users ) ) : ?>
										<ul class="affiliate-users-list">
											<?php
											foreach ( $associated_users as $user ) :
												$user_id = $user->ID;
												?>
												<li class="affiliate-user">
													<div class="referral-user">
														<div class="referral-avatar">
															<?php echo get_avatar( $user_id, 64 ); ?>
														</div>
														<div class="referral-info">
															<h3><a href="<?php echo esc_url( get_edit_user_link( $user_id ) ); ?>"><?php echo esc_html( $user->user_login ); ?></a></h3>
															<a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_html( $user->user_email ); ?></a>
														</div>
													</div>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php else : ?>
										<p class="no-items-found"><?php esc_html_e( 'No user registered yet', 'yith-woocommerce-affiliates' ); // @since 1.7.1 ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-3" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">

							<div class="panel-wrap woocommerce">
								<div id="affiliate_details" class="yith-affiliate-detail panel">
									<?php YITH_WCAF_Affiliate_Handler()->render_affiliate_extra_fields( $user ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</form>
