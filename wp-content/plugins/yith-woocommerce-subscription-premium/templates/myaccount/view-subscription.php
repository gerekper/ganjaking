<?php
/**
 * Subscription details
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

if ( $subscription->user_id != get_current_user_id() ) {
	esc_html_e( 'You do not have permissions necessary to access to this page', 'yith-woocommerce-subscription' );

	return;
}

$next_payment_due_date = ( ! in_array( $subscription->status, array( 'paused', 'cancelled' ) ) && $subscription->payment_due_date ) ? esc_html( wc_date_format(), $subscription->payment_due_date ) : '';
$status                = ywsbs_get_status();
?>
<h2><?php esc_html_e( 'Subscription Detail', 'yith-woocommerce-subscription' ); ?></h2>
<p>
	<strong><?php esc_html_e( 'Recurring: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo wp_kses_post( $subscription->get_formatted_recurring() ); ?>
	<br>
	<strong><?php esc_html_e( 'Status: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $status[ $subscription->status ] ); ?>

	<?php if ( $subscription->start_date ) : ?>
		<br>
		<strong><?php esc_html_e( 'Start date: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->start_date ) ); ?>
	<?php endif ?>

	<?php if ( ! in_array( $subscription->status, array( 'paused', 'cancelled' ) ) && $subscription->payment_due_date ) : ?>
		<br>
		<strong><?php esc_html_e( 'Next Payment Due Date: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->payment_due_date ) ); ?>
	<?php endif ?>

	<?php if ( $subscription->status == 'paused' && $subscription->expired_pause_date ) : ?>
		<br><strong><?php esc_html_e( 'Pause expiry date: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->expired_pause_date ) ); ?>
	<?php endif ?>

	<?php if ( $subscription->expired_date ) : ?>
		<br>
		<strong><?php esc_html_e( 'Subscription expiry date: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->expired_date ) ); ?>
	<?php endif ?>

	<?php if ( $subscription->end_date ) : ?>
		<br>
		<strong><?php esc_html_e( 'End Date: ', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( date_i18n( wc_date_format(), $subscription->end_date ) ); ?>
	<?php endif ?>

</p>

<p>
	<?php if ( $subscription->can_be_paused() && $subscription->status != 'paused' && $subscription->status != 'cancelled' ) : ?>
		<a href="#pause-subscription-modal" class="button pause-subscription-button" data-ywsbs-rel="prettyPhoto"><?php esc_html_e( 'Pause', 'yith-woocommerce-subscription' ); ?></a>
	<?php endif ?>

	<?php if ( $subscription->can_be_resumed() ) : ?>
		<a href="#resume-subscription-modal" class="button resume-subscription-button" data-ywsbs-rel="prettyPhoto"><?php esc_html_e( 'Resume', 'yith-woocommerce-subscription' ); ?></a>
	<?php endif ?>

	<?php if ( $subscription->can_be_cancelled() && 'cancelled' != $subscription->status ) : ?>
		<a href="#cancel-subscription-modal" class="button cancel-subscription-button" data-ywsbs-rel="prettyPhoto" data-expired="<?php echo esc_attr( $next_payment_due_date ); ?>"><?php esc_html_e( 'Cancel', 'yith-woocommerce-subscription' ); ?></a>
	<?php endif ?>

	<?php if ( $subscription->can_be_renewed() ) : ?>
		<a href="#renew-subscription-modal" class="button renew-subscription-button" data-ywsbs-rel="prettyPhoto"><?php esc_html_e( 'Renew', 'yith-woocommerce-subscription' ); ?></a>
	<?php endif ?>

	<?php if ( $subscription->can_edit_shipping() ) : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'subscription' => $subscription->id ), wc_get_endpoint_url( 'edit-address', 'shipping' ) ) ); ?>" class="button change-shipping-subscription-button"><?php esc_html_e( 'Change shipping address', 'yith-woocommerce-subscription' ); ?></a>
	<?php endif ?>
</p>

<?php if ( $variations = $subscription->can_be_switchable() ) : ?>
	<h4><?php esc_html_e( 'Upgrade/Downgrade your subscription', 'yith-woocommerce-subscription' ); ?></h4>
	<p>
		<form>
			<?php wp_nonce_field( 'switch-variation' ); ?>
			<input type="hidden" name="subscription_id" value="<?php echo esc_attr( $subscription->id ); ?>" />
			<select name="switch-variation" id="switch-variation">
				<?php
				foreach ( $variations as $variation ) :
					$show       = 'no';
					$data_price = 0;
					if ( isset( $variation['has_gap_payment'] ) && isset( $variation['gap'] ) && $variation['gap'] > 0 ) {
						$show       = 'yes';
						$data_price = wc_price( $variation['gap'], array( 'currency' => $subscription->order_currency ) );
					}
					?>
					<option value="<?php echo esc_attr( $variation['variation_id'] ); ?>" data-show="<?php echo esc_attr( $show ); ?>" data-price="<?php echo wp_kses_post( $data_price ); ?>" data-simpleprice="<?php echo isset( $variation['gap'] ) ? esc_attr( $variation['gap'] ) : ''; ?>"><?php echo wp_kses_post( ywsbs_get_attribute_name( $variation ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<div class="upgrade-option">
	<p><?php esc_html_e( 'Do you want to upgrade your subscription including also past installments?', 'yith-woocommerce-subscription' ); ?></p>
	<input type="radio" name="pay-gap" value="yes" id="pay-gap-yes"> <label for="pay-gap-yes"><?php esc_html_e( 'Yes, <span class="price"></span> will be added to amount', 'yith-woocommerce-subscription' ); ?> </label><br>
	<input type="radio" name="pay-gap" value="no" id="pay-gap-no" checked> <label for="pay-gap-no"><?php esc_html_e( 'No', 'yith-woocommerce-subscription' ); ?></label>
	<input type="hidden" name="pay-gap-price" id="pay-gap-price" value="" />
	</div>
	<input type="submit" class="ywsbs-button button" value="<?php esc_html_e( 'Switch', 'yith-woocommerce-subscription' ); ?>">
	</form></p>
<?php endif ?>
<table class="shop_table order_details">
	<thead>
	<tr>
		<th class="product-name"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
		<th class="product-total"><?php esc_html_e( 'Total', 'yith-woocommerce-subscription' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr class="order_item">
		<td class="product-name">
			<a href="<?php echo esc_url( get_permalink( $subscription->product_id ) ); ?>"><?php echo wp_kses_post( $subscription->product_name ); ?></a><?php echo ' x ' . esc_html( $subscription->quantity ); ?>
			<?php
			if ( $subscription->variation_id ) {
				yith_ywsbs_get_product_meta( $subscription, $subscription->variation );
			}
			?>
		</td>
		<td class="product-total">
			<?php echo wp_kses_post( wc_price( $subscription->line_total, array( 'currency' => $subscription->order_currency ) ) ); ?>
		</td>
	</tr>

	</tbody>
	<tfoot>
	<?php if ( $subscription->line_tax != 0 ) : ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( wc_price( $subscription->line_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
		</tr>
	<?php endif ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $subscription->line_total + $subscription->line_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
	</tr>

	<?php
	if ( ! empty( $subscription->subscriptions_shippings ) ) :
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( wc_price( $subscription->subscriptions_shippings['cost'], array( 'currency' => $subscription->order_currency ) ) . sprintf( __( '<small> via %s</small>', 'yith-woocommerce-subscription' ), $subscription->subscriptions_shippings['name'] ) ); ?></td>
		</tr>
		<?php
		if ( ! empty( $subscription->order_shipping_tax ) ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
				<td><?php echo wp_kses_post( wc_price( $subscription->order_shipping_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
			</tr>
			<?php
		endif;
	endif;
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $subscription->subscription_total, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
	</tr>
	</tfoot>
</table>
<?php if ( ! empty( $subscription->order_ids ) ) : ?>

	<h2><?php esc_html_e( 'Related Orders', 'yith-woocommerce-subscription' ); ?></h2>
	<table class="shop_table ywsbs_subscription_table my_account_orders shop_table_responsive">
		<thead>
		<tr>
			<th class="order-ID"><?php esc_html_e( 'ID', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-data"><?php esc_html_e( 'Date', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-status"><?php esc_html_e( 'Status', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-total"><?php esc_html_e( 'Total', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs_view"></th>
		</tr>
		</thead>
		<tbody>

		<?php
		foreach ( $subscription->order_ids as $order_id ) :
			$order = wc_get_order( $order_id );
			if ( ! $order ) :
				?>
				<tr>
					<td class="order-number" data-title="<?php esc_attr_e( 'Order Number', 'woocommerce' ); ?>">
						<?php echo  esc_html_x( '#', 'hash before order number', 'yith-woocommerce-subscription' ) . esc_html( $order_id ) ; ?>
					</td>
					<td class="order-date" data-title="<?php esc_attr_e( 'Date', 'yith-woocommerce-subscription' ); ?>">
					</td>
					<td class="order-status" data-title="<?php esc_attr_e( 'Status', 'yith-woocommerce-subscription' ); ?>">
						<?php esc_html_e( 'Deleted', 'yith-woocommerce-subscription' ); ?>
					</td>
					<td class="order-total" data-title="<?php esc_attr_e( 'Total', 'yith-woocommerce-subscription' ); ?>">
					</td>
					<td class="order-actions"></td>
				</tr>
				<?php
				continue;
			endif;
			$item_count            = $order->get_item_count();
			$next_payment_due_date = ( ! in_array( $subscription->status, array( 'paused', 'cancelled' ) ) && $subscription->payment_due_date ) ? date_i18n( wc_date_format(), $subscription->payment_due_date ) : '';
			?>
			<tr>
				<td class="order-number" data-title="<?php esc_attr_e( 'Order Number', 'woocommerce' ); ?>">
					<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
						<?php echo esc_html_x( '#', 'hash before order number', 'yith-woocommerce-subscription' ) . esc_html( $order->get_order_number() ); ?>
					</a>
				</td>
				<td class="order-date" data-title="<?php esc_attr_e( 'Date', 'yith-woocommerce-subscription' ); ?>">
					<?php
					if ( function_exists( 'wc_format_datetime' ) ) {
						$order_date           = $order->get_date_created();
						$order_date_formatted = wc_format_datetime( $order_date );
					} else {
						$order_date           = $order->order_date;
						$order_date_formatted = date_i18n( get_option( 'date_format' ), strtotime( $order_date ) );
					}
					?>

					<time datetime="<?php echo esc_attr( date( 'Y-m-d', strtotime( $order_date ) ) ); ?>" title="<?php echo esc_attr( $order_date_formatted ); ?>"><?php echo wp_kses_post( $order_date_formatted ); ?></time>
				</td>
				<td class="order-status" data-title="<?php esc_attr_e( 'Status', 'yith-woocommerce-subscription' ); ?>" >
					<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
				</td>
				<td class="order-total" data-title="<?php esc_attr_e( 'Total', 'yith-woocommerce-subscription' ); ?>">
					<?php echo sprintf( esc_html( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'yith-woocommerce-subscription' ) ), wp_kses_post( $order->get_formatted_order_total() ), esc_html( $item_count ) ); ?>
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

					if ( in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
						$actions['cancel'] = array(
							'url'        => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
							'name'       => __( 'Cancel', 'yith-woocommerce-subscription' ),
							'attributes' => array(
								'data-expired' => $next_payment_due_date,
							),
						);
					}

					$actions['view'] = array(
						'url'  => $order->get_view_order_url(),
						'name' => __( 'View', 'yith-woocommerce-subscription' ),
					);

					$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );

					if ( $actions ) {
						foreach ( $actions as $key => $action ) {
							$attribute_data = '';
							if ( isset( $action['attributes'] ) ) {
								foreach ( $action['attributes'] as $key1 => $attribute ) {
									$attribute_data .= ' ' . $key1 . '="' . $attribute . '"';
								}
							}
							echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '" ' . $attribute_data . '>' . esc_html( $action['name'] ) . '</a>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
					?>
				</td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<div class="ywsbs-customer-details">
	<header><h2><?php esc_html_e( 'Customer Details', 'yith-woocommerce-subscription' ); ?></h2></header>

	<?php
	$billing_address  = $subscription->get_address_fields( 'billing', true );
	$shipping_address = $subscription->get_address_fields( 'shipping', true );
	?>

	<table class="shop_table shop_table_responsive customer_details">
		<?php if ( $billing_address['email'] ) : ?>
			<tr>
				<th><?php esc_html_e( 'Email:', 'yith-woocommerce-subscription' ); ?></th>
				<td><?php echo esc_html( $billing_address['email'] ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $billing_address['phone'] ) : ?>
			<tr>
				<th><?php esc_html_e( 'Telephone:', 'yith-woocommerce-subscription' ); ?></th>
				<td><?php echo esc_html( $billing_address['phone'] ); ?></td>
			</tr>
		<?php endif; ?>
	</table>

	<div class="col2-set addresses">
		<div class="col-1">
			<header class="title">
				<h3><?php esc_html_e( 'Billing Address', 'yith-woocommerce-subscription' ); ?></h3>
			</header>
			<address>
				<?php echo wp_kses_post( WC()->countries->get_formatted_address( $billing_address ) ); ?>
			</address>
		</div>
		<div class="col-2">
			<header class="title">
				<h3><?php esc_html_e( 'Shipping Address', 'yith-woocommerce-subscription' ); ?></h3>
			</header>
			<address>
				<?php echo wp_kses_post( WC()->countries->get_formatted_address( $shipping_address ) ); ?>
			</address>
		</div>
	</div>
</div>

<!-- SUBSCRIPTION CANCEL POPUP OPENER -->
<div id="cancel-subscription-modal" class="hide-modal">
	<p><?php printf( esc_html( __( 'Do you really want to cancel subscription No. #%s?', 'yith-woocommerce-subscription' ) ), '<span id="modal-subscription-number">' . esc_html( $subscription->id ) . '</span>' ); ?></p>

	<p>
		<a class="ywsbs-button button cancel-quote-modal-button" href="<?php echo esc_url( $subscription->get_change_status_link( 'cancelled' ) ); ?>"><?php esc_html_e( 'Yes, I want to cancel the subscription', 'yith-woocommerce-subscription' ); ?></a>
		<a class="ywsbs-button button close-subscription-modal-button" href="#"><?php esc_html_e( 'Close', 'yith-woocommerce-subscription' ); ?></a>
	</p>
</div>

<div id="pause-subscription-modal" class="hide-modal">
	<p><?php printf( esc_html( __( 'Do you really want to pause subscription No. #%s?', 'yith-woocommerce-subscription' ) ), '<span id="modal-subscription-number">' . esc_html( $subscription->id ) . '</span>' ); ?></p>

	<p>
		<a class="ywsbs-button button pause-subscription-modal-button" href="<?php echo esc_url( $subscription->get_change_status_link( 'paused' ) ); ?>"><?php esc_html_e( 'Yes, I want to pause the subscription', 'yith-woocommerce-subscription' ); ?></a>
		<a class="ywsbs-button button close-subscription-modal-button" href="#"><?php esc_html_e( 'Close', 'yith-woocommerce-subscription' ); ?></a>
	</p>
</div>

<div id="resume-subscription-modal" class="hide-modal">
	<p><?php printf( esc_html( __( 'Do you really want to resume subscription No. #%s?', 'yith-woocommerce-subscription' ) ), '<span id="modal-subscription-number">' . esc_html( $subscription->id ) . '</span>' ); ?></p>

	<p>
		<a class="ywsbs-button button resume-subscription-modal-button" href="<?php echo esc_url( $subscription->get_change_status_link( 'resumed' ) ); ?>"><?php esc_html_e( 'Yes, I want to resume the subscription', 'yith-woocommerce-subscription' ); ?></a>
		<a class="ywsbs-button button close-subscription-modal-button" href="#"><?php esc_html_e( 'Close', 'yith-woocommerce-subscription' ); ?></a>
	</p>
</div>

<div id="renew-subscription-modal" class="hide-modal">
	<p><?php esc_html_e( 'Do you really want to renew the subscription? You will redirect to the checkout to renew it', 'yith-woocommerce-subscription' ); ?></p>

	<p>
		<a class="ywsbs-button button renew-subscription-modal-button" href="<?php echo esc_url( $subscription->get_change_status_link( 'renew' ) ); ?>"><?php esc_html_e( 'Yes, I want to renew the subscription', 'yith-woocommerce-subscription' ); ?></a>
		<a class="ywsbs-button button close-subscription-modal-button" href="#"><?php esc_html_e( 'Close', 'yith-woocommerce-subscription' ); ?></a>
	</p>
</div>
