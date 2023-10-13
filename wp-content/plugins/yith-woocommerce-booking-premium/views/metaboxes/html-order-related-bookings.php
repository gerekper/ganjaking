<?php
/**
 * Order Related Bookings
 *
 * @var YITH_WCBK_Booking[] $bookings The bookings
 * @var WC_Order            $order    The order
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>

<?php foreach ( $bookings as $booking ) : ?>
	<?php
	$product  = $booking->get_product();
	$services = $booking->get_service_names( true );
	$data     = $booking->get_booking_data_to_display( 'admin' );
	unset( $data['user'], $data['order'], $data['duration'], $data['from'], $data['to'], $data['status'], $data['product'] );
	?>
	<div id="yith-wcbk-order-related-booking-<?php echo esc_attr( $booking->get_id() ); ?>" class="yith-wcbk-order-related-booking yith-wcbk-order-related-booking--<?php echo esc_attr( $booking->get_status() ); ?>-status">
		<div class="yith-wcbk-order-related-booking__heading">
			<h3 class="yith-wcbk-order-related-booking__title">
				<a class="yith-wcbk-order-related-booking__title__booking-link" href="<?php echo esc_url( $booking->get_edit_link() ); ?>"><?php echo esc_html( $booking->get_name() ); ?></a>
				<?php if ( $product ) : ?>
					<?php $product_link = get_edit_post_link( $product->get_id() ); ?>
					&ndash;
					<a class="yith-wcbk-order-related-booking__title__product-link" href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
				<?php endif; ?>
			</h3>
		</div>
		<div class="yith-wcbk-order-related-booking__details">
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__duration">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Duration', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo esc_html( $booking->get_duration_html() ); ?></div>
			</div>
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__dates">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Dates', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo esc_html( sprintf( '%s - %s', $booking->get_formatted_from(), $booking->get_formatted_to() ) ); ?></div>
			</div>
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__status yith-wcbk-order-related-booking__status--<?php echo esc_attr( $booking->get_status() ); ?>">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo esc_html( $booking->get_status_text() ); ?></div>
			</div>

			<?php foreach ( $data as $data_key => $single_data ) : ?>
				<?php
				$data_label = $single_data['label'] ?? '';
				$data_value = $single_data['display'] ?? '';
				?>
				<?php if ( $data_value ) : ?>
					<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__<?php echo esc_attr( $data_key ); ?>">
						<div class="yith-wcbk-order-related-booking__field-label"><?php echo esc_html( $data_label ); ?></div>
						<div class="yith-wcbk-order-related-booking__field-value"><?php echo wp_kses_post( $data_value ); ?></div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>
