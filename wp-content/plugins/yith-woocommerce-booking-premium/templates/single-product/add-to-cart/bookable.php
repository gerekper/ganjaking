<?php
/** Bookable template.
 *
 * @var WC_Product_Booking $product
 * @var int                $from
 * @var int                $to
 * @var bool               $bookable
 * @var array              $non_available_reasons
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$date_format     = ! $product->has_time() ? wc_date_format() : ( wc_date_format() . ' ' . wc_time_format() );
$from_html       = date_i18n( $date_format, $from );
$to_html         = date_i18n( $date_format, $to );
$bookable_status = $bookable ? 'bookable' : 'not-bookable';

if ( ! apply_filters( 'yith_wcbk_booking_form_message_show_bookable_message', ! $bookable ) ) {
	return;
}
?>

<div class="yith-wcbk-bookable <?php echo esc_attr( $bookable_status ); ?>">
	<?php
	if ( $product->is_full_day() && gmdate( 'Y-m-d', $from ) === gmdate( 'Y-m-d', $to ) ) {
		// translators: 1. bookable or non-bookable status; 2. date.
		$bookable_text = sprintf( __( '<strong>%1$s</strong>: on %2$s', 'yith-booking-for-woocommerce' ), yith_wcbk_get_label( $bookable_status ), $from_html );
	} else {
		// translators: 1. bookable or non-bookable status; 2. start date; 3. end date.
		$bookable_text = sprintf( __( '<strong>%1$s</strong>: from %2$s to %3$s', 'yith-booking-for-woocommerce' ), yith_wcbk_get_label( $bookable_status ), $from_html, $to_html );
	}
	echo wp_kses_post( apply_filters( 'yith_wcbk_booking_form_message_bookable_text', $bookable_text, $bookable, $from, $to, $product ) );

	$non_available_reasons_html = apply_filters( 'yith_wcbk_booking_form_message_bookable_reasons_html', ! ! $non_available_reasons ? ( "<div class='non-available-reason'>" . implode( "</div><div class='non-available-reason'>", $non_available_reasons ) . '</div>' ) : '', $non_available_reasons, $from, $to, $product );
	?>
	<?php if ( $non_available_reasons_html ) : ?>
		<div class="non-available-reasons"><?php echo wp_kses_post( $non_available_reasons_html ); ?></div>
	<?php endif; ?>
</div>
