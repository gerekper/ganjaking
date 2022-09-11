<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * This is the email sent to the administrator when the subscription changes status
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var                    $email
 * @var                    $email_heading
 * @var YWSBS_Subscription $subscription
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email );

$status_list = ywsbs_get_status();
$sbs_status  = isset( $status_list[ $subscription->get_status() ] ) ? $status_list[ $subscription->get_status() ] : $subscription->get_status();
?>
	<p>
		<?php
		// translators: placeholder 1 subscription number 2 new status.
		printf( wp_kses_post( _x( 'The status of subscription %1$s has changed to <strong>%2$s</strong>', 'placeholder 1 subscription number, 2 new status', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->get_number() ), esc_html( $sbs_status ) );
		?>
	</p>

	<h2><a class="link"
			href="<?php echo esc_url( admin_url( 'post.php?post=' . $subscription->get_id() . '&action=edit' ) ); ?>">
		 <?php
			// translators: placeholder subscription number.
			printf( esc_html_x( 'Subscription %s', 'the placeholder is the subscription number', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_number() ) );
			?>
			</a>
		(<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>
		)</h2>


	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
		<tr>
			<th scope="col"
				style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
			<th scope="col"
				style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td scope="col" style="text-align:left;">
				<a href="<?php echo esc_url( get_permalink( $subscription->get_product_id() ) ); ?>"><?php echo wp_kses_post( $subscription->get_product_name() ); ?></a><?php echo ' x ' . esc_html( $subscription->get_quantity() ); ?>
				<?php
				$text_align  = is_rtl() ? 'right' : 'left';
				$margin_side = is_rtl() ? 'left' : 'right';
				$sbs_order   = $subscription->get_order();
				$item        = $sbs_order instanceof WC_Order ? $sbs_order->get_item( $subscription->get( 'order_item_id' ) ) : '';

				if ( $item ) {
					wc_display_item_meta(
						$item,
						array(
							'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
						)
					);
				}

				?>
			</td>

			<td scope="col"
				style="text-align:left;"><?php echo wp_kses_post( wc_price( $subscription->get_line_total(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
		</tr>

		</tbody>
		<tfoot>
		<?php if ( $subscription->get_line_tax() !== 0 ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
				<td><?php echo wp_kses_post( wc_price( $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
			</tr>
		<?php endif ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( wc_price( $subscription->get_line_total() + $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
		</tr>

		<?php
		$subscriptions_shippings = $subscription->get_subscriptions_shippings();
		if ( $subscriptions_shippings ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
				<td>
				<?php
					// translators: placeholder: 1. shipping name 2 and 3. html tags.
					echo wp_kses_post( wc_price( $subscriptions_shippings['cost'], array( 'currency' => $subscription->get_order_currency() ) ) . sprintf( esc_html_x( '%2$s via %1$s%3$s', 'placeholder: 1. shipping name 2 and 3. html tags', 'yith-woocommerce-subscription' ), $subscriptions_shippings['name'], '<small>', '</small>' ) );
				?>
					</td>
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
	<?php if ( $subscription->get_order_ids() ) : ?>
		<ul style="list-style-type: none;padding-left: 0px;margin-bottom: 35px;">
			<?php
			foreach ( $subscription->get_order_ids() as $order_id ) :
				$current_order = wc_get_order( $order_id );
				?>
				<li>
					<?php
					if ( ! $current_order ) {
						echo '<p>';
						// translators: placeholder order id.
						printf( wp_kses_post( _x( 'Order #%d', 'order id', 'yith-woocommerce-subscription' ) ), esc_html( $order_id ) );
						echo '</p>';
						continue;
					}

					$order_date           = $current_order->get_date_created();
					$order_date_formatted = wc_format_datetime( $order_date );
					?>
					<?php printf( wp_kses_post( '<time datetime="%s">%s</time>' ), esc_html( gmdate( 'Y-m-d', strtotime( $order_date ) ) ), esc_html( $order_date_formatted ) ); ?>
					-
					<?php
					// translators: placeholder 1. order admin url, 2. order number.
					printf( wp_kses_post( _x( 'order <a href="%1$s">#%2$d</a>', 'placeholder 1. order admin url, 2. order number', 'yith-woocommerce-subscription' ) ), esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ), esc_html( $current_order->get_order_number() ) );
					?>
					-
					<?php echo wp_kses_post( wc_price( $current_order->get_total(), array( 'currency' => $current_order->get_currency() ) ) ); ?>
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
