<?php
/**
 * Subscription details
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current Subscription.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

if ( $subscription->get_user_id() !== get_current_user_id() ) {
	esc_html_e( 'You do not have permissions necessary to access to this page', 'yith-woocommerce-subscription' );
	return;
}
$subscription_id          = $subscription->get_id();
$next_payment_due_date    = ( ! in_array( $subscription->status, array( 'paused', 'cancelled' ), true ) && $subscription->payment_due_date ) ? date_i18n( wc_date_format(), $subscription->payment_due_date ) : '';
$subscription_status_list = ywsbs_get_status();
$status                   = $subscription->get_status(); //phpcs:ignore
$subscription_status      = $subscription_status_list[ $status ];
$subscription_name        = sprintf( '%s - %s', $subscription->get_number(), $subscription->get_product_name() );
$subscription_title       = apply_filters( 'ywsbs_subscription_view_title', sprintf( '%s %s - %s', __( 'Subscription', 'yith-woocommerce-subscription' ), $subscription->get_number(), $subscription->get_product_name() ), $subscription );
$last_billing_date        = $subscription->get_last_billing_date();


?>
<div class="ywsbs-subscription-view-wrap">
	<?php do_action('ywsbs_before_subscription_view', $subscription_id ); ?>
	<div class="ywsbs-back-url"><a
			href="<?php echo esc_url( wc_get_account_endpoint_url( YITH_WC_Subscription::$endpoint ) ); ?>"><?php esc_html_e( '< Back to all subscriptions', 'yith-woocommerce-subscription' ); ?></a>
	</div>
	<h2><?php echo esc_html( $subscription_title ); ?></h2>
	<div class="ywsbs-subscription-info-wrapper">
		<!-- SUBSCRIPTION INFO BOX -->
		<div class="ywsbs-box ywsbs-subscription-info-box subscription-info">
			<h3><?php esc_html_e( 'Subscription info', 'yith-woocommerce-subscription' ); ?></h3>

			<div class="ywsbs-subscription-info-item">
				<strong><?php esc_html_e( 'Plan:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $subscription->get_product_name() ); ?>
				- <?php echo wp_kses_post( YWSBS_Subscription_Helper()->get_formatted_recurring( $subscription, '', true, false ) ); ?>
				<?php do_action( 'ywsbs_after_subscription_plan_info', $subscription ); ?>
			</div>


			<?php if ( $subscription->get_start_date() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Started on:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_start_date() ) ); ?>
				</div>
			<?php endif ?>

			<?php if ( ! empty( $last_billing_date ) ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Last billing:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( wc_format_datetime( $last_billing_date ) ); ?>
				</div>
			<?php endif ?>

			<?php if ( ! in_array( $status, array( 'paused', 'cancelled' ), true ) && $subscription->get_payment_due_date() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Next billing:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_payment_due_date() ) ); ?>
				</div>
			<?php endif ?>

			<?php if ( 'paused' === $status && $subscription->get_expired_pause_date() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Pause expiring on:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_expired_pause_date() ) ); ?>
				</div>
			<?php endif ?>

			<?php if ( $subscription->get_expired_date() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Expiring on:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_expired_date() ) ); ?>
				</div>
			<?php endif ?>

			<?php if ( $subscription->get_end_date() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'End date:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_end_date() ) ); ?>
				</div>
			<?php endif ?>


			<?php if ( $subscription->get_payment_method_title() ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Payment method:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $subscription->get_payment_method_title() ); ?>
				</div>
			<?php endif ?>
			<div class="ywsbs-subscription-info-item status-item">
				<strong><?php esc_html_e( 'Status:', 'yith-woocommerce-subscription' ); ?></strong> <?php printf( '<span class="status %1$s">%2$s</span>', esc_html( $status ), esc_attr( ucfirst( $subscription_status ) ) ); ?>
				<?php do_action( 'ywsbs_after_subscription_status', $subscription ); ?>
			</div>

			<?php if ( $subscription->can_be_resubscribed() ) : ?>
				<div class="ywsbs-subscription-info-item resubscribe">
					<a href="<?php echo esc_url( ywsbs_get_resubscribe_subscription_url( $subscription ) ); ?>"><?php esc_html_e( 'Resubscribe', 'yith-woocommerce-subscription' ); ?></a>
				</div>
			<?php endif; ?>

			<?php do_action( 'ywsbs_before_close_subscription_info_box', $subscription ); ?>
		</div>

		<!-- SUBSCRIPTION REVIEW BOX -->
		<div class="ywsbs-box ywsbs-subscription-info-box subscription-totals">
			<h3><?php esc_html_e( 'Subscription total', 'yith-woocommerce-subscription' ); ?></h3>
			<table class="subscription-review-table">
				<tbody>
				<tr class="item-info">
					<th class="product-name">
						<a href="<?php echo esc_url( get_permalink( $subscription->get_product_id() ) ); ?>"><?php echo esc_html( $subscription->get_product_name() ); ?></a><?php echo esc_html( ' x ' . $subscription->get_quantity() ); ?>
						<?php
						if ( $subscription->variation_id ) {
							yith_ywsbs_get_product_meta( $subscription, $subscription->get_variation() );
						}
						?>
					</th>
					<td class="product-total">
						<div
							class="subscription-price"><?php echo wp_kses_post( YWSBS_Subscription_Helper()->get_formatted_recurring( $subscription ) ); ?></div>
						<?php
						if ( $subscription->get_max_length() > 1 && 'yes' === get_option( 'ywsbs_subscription_total_amount', 'no' ) ) {
							$subscription_total = YWSBS_Subscription_Helper::get_total_subscription_amount( $subscription );
							echo wp_kses_post( $subscription_total );
						}
						?>
					</td>
				</tr>

				</tbody>
				<tfoot>
				<?php
				if ( $subscription->get_line_tax() != 0 ) : //phpcs:ignore
					?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
						<td><?php echo wc_price( $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ); //phpcs:ignore
						?>
							</td>
					</tr>
				<?php endif ?>
				<tr>
					<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
					<td><?php echo wc_price( $subscription->get_line_total() + $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ); //phpcs:ignore ?></td>
				</tr>

				<?php
				$subscriptions_shippings = $subscription->get_subscriptions_shippings();
				if ( ! empty( $subscriptions_shippings ) ) :
					?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
						<td>
						<?php
							// translators:placeholder: 1. shipping name 2 and 3 html tags.
							echo wp_kses_post( wc_price( $subscriptions_shippings['cost'], array( 'currency' => $subscription->get_order_currency() ) ) ) . sprintf( esc_html_x( '%2$s via %1$s%3$s', 'placeholder: 1. shipping name 2 and 3 html tags', 'yith-woocommerce-subscription' ), wp_kses_post( $subscriptions_shippings['name'] ), '<small>', '</small>' );
						?>
							</td>
					</tr>
					<?php
					if ( ! empty( $subscription->get_order_shipping_tax() ) ) :
						?>
						<tr>
							<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
							<td><?php echo wp_kses_post( wc_price( $subscription->get_order_shipping_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
						</tr>
						<?php
					endif;
				endif;
				?>
				<tr>
					<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
					<td><?php echo wp_kses_post( wc_price( $subscription->get_subscription_total(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
				</tr>
				</tfoot>
			</table>

		</div>
	</div>


	<?php
	$order_ids = $subscription->get_order_ids();
	if ( ! empty( $order_ids ) ) :
		?>
		<div class="ywsbs-box ywsbs-subscription-related-orders">
			<h3><?php esc_html_e( 'Related Orders', 'yith-woocommerce-subscription' ); ?></h3>
			<table class="shop_table ywsbs_subscription_table my_account_orders shop_table_responsive">
				<thead>
				<tr>
					<th class="order-ID"><?php esc_html_e( 'Order', 'yith-woocommerce-subscription' ); ?></th>
					<th class="ywsbs-data"><?php esc_html_e( 'Date', 'yith-woocommerce-subscription' ); ?></th>
					<th class="ywsbs-total"><?php esc_html_e( 'Total', 'yith-woocommerce-subscription' ); ?></th>
					<th class="ywsbs-items"><?php esc_html_e( 'Items', 'yith-woocommerce-subscription' ); ?></th>
					<th class="ywsbs-status"><?php esc_html_e( 'Status', 'yith-woocommerce-subscription' ); ?></th>
					<th class="ywsbs_view"></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $order_ids as $order_id ) :
					$order = wc_get_order( $order_id ); //phpcs:ignore
					if ( ! $order ) :
						?>
						<tr>
							<td class="order-number"
								data-title="<?php esc_attr_e( 'Order Number', 'yith-woocommerce-subscription' ); ?>">
								<?php echo esc_html_x( '#', 'hash before order number', 'yith-woocommerce-subscription' ) . esc_html( $order_id ); ?>
							</td>
							<td class="order-date"
								data-title="<?php esc_attr_e( 'Date', 'yith-woocommerce-subscription' ); ?>">-
							</td>

							<td class="order-total"
								data-title="<?php esc_attr_e( 'Total', 'yith-woocommerce-subscription' ); ?>">-
							</td>
							<td class="order-items"
								data-title="<?php esc_attr_e( 'Items', 'yith-woocommerce-subscription' ); ?>"> -
							</td>
							<td class="order-status"
								data-title="<?php esc_attr_e( 'Status', 'yith-woocommerce-subscription' ); ?>">
							<span
								class="status-trash"><?php esc_html_e( 'Deleted', 'yith-woocommerce-subscription' ); ?></span>
							</td>
							<td class="order-actions"></td>
						</tr>
						<?php
						continue;
					endif;
					$item_count            = $order->get_item_count();
					$next_payment_due_date = ( ! in_array( $subscription->get_status(), array( 'paused', 'cancelled' ), true ) && $subscription->get_payment_due_date() ) ? date_i18n( wc_date_format(), $subscription->get_payment_due_date() ) : '';
					$order_date            = $order->get_date_created();
					$order_date_formatted  = wc_format_datetime( $order_date );
					?>
					<tr>
						<td class="order-number"
							data-title="<?php esc_attr_e( 'Order Number', 'yith-woocommerce-subscription' ); ?>">
							<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								<?php echo esc_html_x( '#', 'hash before order number', 'yith-woocommerce-subscription' ) . esc_html( $order->get_order_number() ); ?>
							</a>
						</td>
						<td class="order-date"
							data-title="<?php esc_attr_e( 'Date', 'yith-woocommerce-subscription' ); ?>">
							<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $order_date ) ) ); ?>"
								title="<?php echo esc_attr( $order_date_formatted ); ?>"><?php echo esc_html( $order_date_formatted ); ?></time>
						</td>

						<td class="order-total"
							data-title="<?php esc_attr_e( 'Total', 'yith-woocommerce-subscription' ); ?>">
							<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
						</td>
						<td class="order-items"
							data-title="<?php esc_attr_e( 'Items', 'yith-woocommerce-subscription' ); ?>">
							<?php echo esc_html( $item_count . ' ' . _n( 'item', 'items', $item_count, 'yith-woocommerce-subscription' ) ); ?>

						</td>
						<td class="order-status"
							data-title="<?php esc_attr_e( 'Status', 'yith-woocommerce-subscription' ); ?>">
						<span
							class="status-<?php echo esc_attr( $order->get_status() ); ?>"><?php echo wp_kses_post( wc_get_order_status_name( $order->get_status() ) ); ?></span>
						</td>
						<td class="order-actions">
							<?php
							$actions = array();

							if ( $order->needs_payment() ) {
								$actions['pay'] = array(
									'url'  => $order->get_checkout_payment_url(),
									'name' => __( 'Pay', 'yith-woocommerce-subscription' ),
								);
							}

							if ( in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ), true ) ) {
								$actions['cancel'] = array(
									'url'        => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
									'name'       => __( 'Cancel', 'yith-woocommerce-subscription' ),
									'attributes' => array(
										'data-expired' => $next_payment_due_date,
									),
								);
							}

							$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );

							if ( $actions ) {
								foreach ( $actions as $key => $action ) { //phpcs:ignore
									$attribute_data = '';
									if ( isset( $action['attributes'] ) ) {
										foreach ( $action['attributes'] as $key1 => $attribute ) {
											$attribute_data .= ' ' . $key1 . '="' . $attribute . '"';
										}
									}
									echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '" ' . $attribute_data . '>' . esc_html( $action['name'] ) . '</a>'; //phpcs:ignore
								}
							}
							?>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	<?php endif ?>



	<?php
	$billing_address    = $subscription->get_address_fields( 'billing', true );
	$shipping_address   = $subscription->get_address_fields( 'shipping', true );
	$billing_formatted  = WC()->countries->get_formatted_address( $billing_address );
	$shipping_formatted = WC()->countries->get_formatted_address( $shipping_address );
	?>
	<div class="ywsbs-subscription-info-wrapper">
		<div class="ywsbs-box ywsbs-subscription-info-box billing-info">
			<h3><?php esc_html_e( 'Billing Information', 'yith-woocommerce-subscription' ); ?></h3>
			<?php if ( ! empty( $billing_address['email'] ) ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Email:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $billing_address['email'] ); ?>
				</div>
			<?php endif ?>
			<?php if ( ! empty( $billing_address['phone'] ) ) : ?>
				<div class="ywsbs-subscription-info-item">
					<strong><?php esc_html_e( 'Phone:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $billing_address['phone'] ); ?>
				</div>
			<?php endif ?>
			<div class="ywsbs-subscription-info-item">
				<strong><?php esc_html_e( 'Address:', 'yith-woocommerce-subscription' ); ?></strong>
				<br><?php echo WC()->countries->get_formatted_address( $billing_address ); //phpcs:ignore ?>
			</div>
		</div>

		<?php if ( $subscription->needs_shipping() ) : ?>
			<div class="ywsbs-box ywsbs-subscription-info-box shipping-info">
				<?php if ( $subscription->can_edit_shipping() ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'subscription' => $subscription->id ), wc_get_endpoint_url( 'edit-address', 'shipping' ) ) ); ?>"
						class="edit-subscription-shipping"
						title="<?php esc_html_e( 'Edit the shipping address', 'yith-woocommerce-subscription' ); ?>"><img
							src="<?php echo esc_url( YITH_YWSBS_ASSETS_URL . '/images/edit.svg' ); ?>"/></a>
				<?php endif; ?>
				<h3><?php esc_html_e( 'Shipping Information', 'yith-woocommerce-subscription' ); ?></h3>
				<div class="ywsbs-subscription-info-item">
					<?php if ( $shipping_formatted ) : ?>
						<strong><?php esc_html_e( 'Address:', 'yith-woocommerce-subscription' ); ?></strong>
						<br><?php echo wp_kses_post( $shipping_formatted ); ?>
					<?php else : ?>
						<?php esc_html_e( 'No shipping found for this subscription.', 'yith-woocommerce-subscription' ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		$delivery_settings = $subscription->get( 'delivery_schedules' );

		if ( $delivery_settings ) :
			$show_max = apply_filters( 'ywsbs_delivery_schedules_my_account_show_max', 5 );
			$ds       = YWSBS_Subscription_Delivery_Schedules()->get_delivery_schedules_ordered( $subscription->get_id() );
			if ( $ds ) :
				?>
				<div class="ywsbs-box ywsbs-subscription-info-box delivery-schedules">
					<h3><?php esc_html_e( 'Related Delivery', 'yith-woocommerce-subscription' ); ?></h3>
					<table class="my_account_orders shop_table_responsive">
						<thead>
						<tr>
							<th><?php esc_html_e( 'Shipped on:', 'yith-woocommerce-subscription' ); ?></th>
							<th><?php esc_html_e( 'Delivery Status:', 'yith-woocommerce-subscription' ); ?></th>
							<th><?php esc_html_e( 'Delivery on:', 'yith-woocommerce-subscription' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $ds as $scheduled ) :
							if ( 0 === $show_max-- ) {
								break;
							}
							?>
							<tr>
								<td data-title="<?php esc_html_e( 'Shipped on', 'yith-woocommerce-subscription' ); ?>"><?php echo wp_kses_post( ywsbs_get_formatted_date( $scheduled->scheduled_date, '-' ) ); ?></td>
								<td data-title="<?php esc_html_e( 'Delivery Status', 'yith-woocommerce-subscription' ); ?>"><span
										class="delivery-status <?php echo esc_attr( $scheduled->status ); ?>"><?php echo wp_kses_post( YWSBS_Subscription_Delivery_Schedules()->get_status_label( $scheduled->status ) ); ?></span>
								</td>
								<td data-title="<?php esc_html_e( 'Delivery on', 'yith-woocommerce-subscription' ); ?>"><?php echo wp_kses_post( ywsbs_get_formatted_date( $scheduled->sent_on, '-' ) ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php do_action( 'ywsbs_after_view_subscription', $subscription ); ?>
	</div>

