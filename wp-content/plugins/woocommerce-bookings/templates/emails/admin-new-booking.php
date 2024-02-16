<?php
/**
 * Admin new booking email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/admin-new-booking.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @package WooCommerce_Bookings
 * @version 1.15.54
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && $booking->get_status() === 'pending-confirmation' ) {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'A booking has been made by %s and is awaiting your approval. The details of this booking are as follows:', 'woocommerce-bookings' );
} else {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'A new booking has been made by %s. The details of this booking are as follows:', 'woocommerce-bookings' );
}
?>

<?php
/**
 * Allows users to filter text in email header
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

$booking_order = $booking->get_order();

if ( $booking_order ) {
	$first_name = $booking_order->get_billing_first_name();
	$last_name  = $booking_order->get_billing_last_name();
}

?>

<?php if ( ! empty( $first_name ) && ! empty( $last_name ) ) : ?>
	<p><?php echo esc_html( sprintf( $opening_paragraph, $first_name . ' ' . $last_name ) ); ?></p>
<?php endif; ?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Booked Product', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
			<?php
			wc_get_template(
				'order/admin/booking-display.php',
				array(
					'booking_ids' => array( $booking->get_id() ),
					'only_title'  => true,
				),
				'woocommerce-bookings',
				WC_BOOKINGS_TEMPLATE_PATH
			);
			?>
			</td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking ID', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_id() ); ?></td>
		</tr>
		<?php
		$resource       = $booking->get_resource();
		$resource_label = $booking->get_product()->get_resource_label();

		if ( $booking->has_resources() && $resource ) :
			?>
			<tr>
				<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo esc_html( ( '' !== $resource_label ) ? $resource_label : __( 'Booking Type', 'woocommerce-bookings' ) ); ?></th>
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
			foreach ( $booking->get_persons() as $bid => $qty ) :
				if ( 0 === $qty ) {
					continue;
				}

				$person_type = ( 0 < $bid ) ? get_the_title( $bid ) : __( 'Person(s)', 'woocommerce-bookings' );
				?>
				<tr>
					<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo esc_html( $person_type ); ?></th>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $qty ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<th><?php esc_html_e( 'Customer Information', 'woocommerce-bookings' ); ?></th>
			<td>
				<?php echo wp_kses( $booking_order->get_formatted_billing_address() ? $booking_order->get_formatted_billing_address() : __( 'No billing address set.', 'woocommerce-bookings' ), array( 'br' => array() ) ); ?><br/>
				<?php echo esc_html( is_callable( array( $booking_order, 'get_billing_phone' ) ) ? $booking_order->get_billing_phone() : $booking_order->billing_phone ); ?><br/>
				<?php echo wp_kses_post( make_clickable( sanitize_email( $booking_order->get_billing_email() ? $booking_order->get_billing_email() : $booking_order->billing_email ) ) ); ?>
			</td>
		</tr>
	</tbody>
</table>

<?php if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && $booking->get_status() === 'pending-confirmation' ) : ?>
<p><?php esc_html_e( 'This booking is awaiting your approval. Please check it and inform the customer if the date is available or not.', 'woocommerce-bookings' ); ?></p>
<?php endif; ?>

<p style="margin-top: 10px;">
<?php
$edit_booking_url  = admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' );
$edit_booking_link = sprintf(
	'<a href="%1$s">%2$s</a>',
	esc_url( $edit_booking_url ),
	__( 'Edit booking', 'woocommerce-bookings' )
);

/* translators: 1: a href to booking */
echo wp_kses_post( sprintf( __( 'You can view and edit this booking in the dashboard here: %s', 'woocommerce-bookings' ), $edit_booking_link ) );
?>
</p>

<?php
/**
 * Allows users to filter text in email footer
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_footer', $email );
?>
