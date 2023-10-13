<?php
/**
 * Booking details PDF Template
 *
 * @var YITH_WCBK_Booking $booking  The booking.
 * @var bool              $is_admin Is admin flag.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$order_id        = apply_filters( 'yith_wcbk_pdf_booking_details_order_id', $booking->get_order_id(), $booking, $is_admin );
$the_order       = ! ! $order_id ? wc_get_order( $order_id ) : false;
$override_props  = array(
	'order_id' => $order_id,
	'order'    => $the_order,
);
$data_to_display = $booking->get_booking_data_to_display( $is_admin ? 'admin' : 'frontend', $override_props );

if ( isset( $data_to_display['user'] ) ) {
	unset( $data_to_display['user'] );
}

?>
<table class="booking-table">
	<?php foreach ( $data_to_display as $data_key => $data ) : ?>
		<?php
		$data_label = $data['label'] ?? '';
		$data_value = $data['display'] ?? '';
		?>
		<?php if ( $data_value ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $data_label ); ?></th>
				<td><?php echo wp_kses_post( $data_value ); ?> </td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>
</table>
