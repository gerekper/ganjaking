<?php
/**
 * This is the email sent to the administrator when the subscription changes status
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );
$status     = ywsbs_get_status();
$sbs_status = isset( $status[ $subscription->status ] ) ? $status[ $subscription->status ] : $subscription->status;
?>


	<p><?php printf( wp_kses_post( __( 'The status of subscription #%1$d has changed to <strong>%2$s</strong>', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ), esc_html( $sbs_status ) ); ?></p>

	<h2><a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $subscription->id . '&action=edit' ) ); ?>"><?php printf( esc_html( __( 'Subscription #%s', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ) ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>)</h2>


	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td scope="col" style="text-align:left;">
				<a href="<?php echo esc_url( get_permalink( $subscription->product_id ) ); ?>"><?php echo wp_kses_post( $subscription->product_name ); ?></a><?php echo ' x ' . esc_html( $subscription->quantity ); ?>
				<?php
				$text_align  = is_rtl() ? 'right' : 'left';
				$margin_side = is_rtl() ? 'left' : 'right';
				$order       = wc_get_order( $subscription->order_id );
				$item        = $order->get_item( $subscription->order_item_id );

				wc_display_item_meta(
					$item,
					array(
						'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
					)
				);
				?>
			</td>

			<td scope="col" style="text-align:left;"><?php echo wp_kses_post( wc_price( $subscription->line_total, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
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
					<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->order_shipping_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
				</tr>
				<?php
			endif;
		endif;
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
			<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->subscription_total, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
		</tr>
		</tfoot>
	</table>
<?php if ( ! empty( $subscription->order_ids ) ) : ?>
	<h3><?php esc_html_e( 'Related Orders', 'yith-woocommerce-subscription' ); ?></h3>
	<?php if ( $subscription->order_ids ) : ?>
		<ul style="list-style-type: none;padding-left: 0px;margin-bottom: 35px;">
			<?php
			foreach ( $subscription->order_ids as $order_id ) :
				$order = wc_get_order( $order_id );
				?>
				<li>
				<?php
				if ( ! $order ) {
					printf( wp_kses_post( __( '<p>Order #%d</p>', 'yith-woocommerce-subscription' ) ), esc_html( $order_id ) );
					continue;
				}
				if ( function_exists( 'wc_format_datetime' ) ) {
					$order_date           = $order->get_date_created();
					$order_date_formatted = wc_format_datetime( $order_date );
				} else {
					$order_date           = $order->order_date;
					$order_date_formatted = date_i18n( get_option( 'date_format' ), strtotime( $order_date ) );
				}
				?>
					<?php printf( wp_kses_post( '<time datetime="%s">%s</time>' ), esc_html( date( 'Y-m-d', strtotime( $order_date ) ) ), esc_html( $order_date_formatted ) ); ?> -
					<?php printf( wp_kses_post( __( 'order <a href="%1$s">#%2$d</a>', 'yith-woocommerce-subscription' ) ), esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ), esc_html( $order->get_order_number() ) ); ?> -
					<?php echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

<?php endif ?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>


<?php
	do_action( 'woocommerce_email_footer', $email );
