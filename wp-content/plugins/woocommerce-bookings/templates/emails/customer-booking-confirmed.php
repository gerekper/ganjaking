<?php
/**
 * Customer booking confirmed email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/customer-booking-confirmed.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<?php if ( $booking->get_order() ) : ?>
	<p>
	<?php
	/* translators: 1: billing first name */
	echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) );
	?>
	</p>
<?php endif; ?>

<p><?php esc_html_e( 'Your booking has been confirmed. The details of your booking are shown below.', 'woocommerce-bookings' ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Booked Product', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_product()->get_title() ); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking ID', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_id() ); ?></td>
		</tr>
		<?php
		$resource = $booking->get_resource();
		$resource_label = $booking->get_product()->get_resource_label();

		if ( $booking->has_resources() && $resource ) :
			?>
			<tr>
				<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo ( '' !== $resource_label ) ? esc_html( $resource_label ) : esc_html__( 'Booking Type', 'woocommerce-bookings' ); ?></th>
				<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $resource->post_title ); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking Start Date', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking End Date', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_end_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
		</tr>
		<?php if ( wc_should_convert_timezone( $booking ) ) : ?>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Time Zone', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( str_replace( '_', ' ', $booking->get_local_timezone() ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( $booking->has_persons() ) : ?>
			<?php
			foreach ( $booking->get_persons() as $id => $qty ) :
				if ( 0 === $qty ) {
					continue;
				}

				$person_type = ( 0 < $id ) ? get_the_title( $id ) : __( 'Person(s)', 'woocommerce-bookings' );
				?>
				<tr>
					<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo esc_html( $person_type ); ?></th>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $qty ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php
$order = $booking->get_order();
if ( $order ) :
	?>

	<?php if ( 'pending' === $order->get_status() ) : ?>
		<p>
		<?php
		/* translators: 1: checkout payment url */
		echo wp_kses_post( sprintf( __( 'To pay for this booking please use the following link: %s', 'woocommerce-bookings' ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . __( 'Pay for booking', 'woocommerce-bookings' ) . '</a>' ) );
		?>
		</p>
	<?php endif; ?>

	<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

	<h2>
	<?php

	$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
	if ( $pre_wc_30 ) {
		$order_date = $order->order_date;
	} else {
		$order_date = $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
	}

	echo esc_html( __( 'Order', 'woocommerce-bookings' ) . ': ' . $order->get_order_number() );
	?>
	(
	<?php
	echo wp_kses_post( sprintf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order_date ) ), date_i18n( wc_bookings_date_format(), strtotime( $order_date ) ) ) );
	?>
	)</h2>

	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
			<tr>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-bookings' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-bookings' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-bookings' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			switch ( $order->get_status() ) {

				case 'completed':
					echo wp_kses_post( $pre_wc_30 ? $order->email_order_items_table( array( 'show_sku' => false ) ) : wc_get_email_order_items( $order, array( 'show_sku' => false ) ) );
					break;

				case 'processing':
				default:
					echo wp_kses_post( $pre_wc_30 ? $order->email_order_items_table( array( 'show_sku' => true ) ) : wc_get_email_order_items( $order, array( 'show_sku' => true ) ) );
					break;
			}
			?>
		</tbody>
		<tfoot>
			<?php
			$totals = $order->get_order_item_totals();
			if ( $totals ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?>
					<tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php
						if ( 1 === $i ) {
							echo 'border-top-width: 4px;';
						}
						?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
						<td style="text-align:left; border: 1px solid #eee; <?php
						if ( 1 === $i ) {
							echo 'border-top-width: 4px;';
						}
						?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

	<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
