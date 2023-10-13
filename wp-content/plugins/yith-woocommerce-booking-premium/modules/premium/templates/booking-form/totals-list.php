<?php
/**
 * Booking Totals list template.
 *
 * @var array              $totals     array of totals
 * @var string             $price_html the total price of the booking product
 * @var WC_Product_Booking $product    the booking product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-booking-form-totals__list">
	<?php foreach ( $totals as $key => $total ) : ?>
		<?php
		$label         = $total['label'];
		$value         = $total['value'];
		$is_discount   = $value < 0;
		$price         = $total['display'] ?? ( yith_wcbk_get_formatted_price_to_display( $product, $total['value'] ) );
		$extra_classes = 'yith-wcbk-booking-form-total__' . esc_attr( $key );

		$extra_classes .= $is_discount ? ' yith-wcbk-booking-form-total--discount' : '';
		?>
		<div class="yith-wcbk-booking-form-total <?php echo esc_attr( $extra_classes ); ?>">
			<div class="yith-wcbk-booking-form-total__label"><?php echo wp_kses_post( $label ); ?></div>
			<div class="yith-wcbk-booking-form-total__value"><?php echo wp_kses_post( $price ); ?></div>
		</div>
	<?php endforeach; ?>

	<div class="yith-wcbk-booking-form-total  yith-wcbk-booking-form-total--total-price">
		<div class="yith-wcbk-booking-form-total__label"><?php esc_html_e( 'Total', 'yith-booking-for-woocommerce' ); ?></div>
		<div class="yith-wcbk-booking-form-total__value"><?php echo wp_kses_post( $price_html ); ?></div>
	</div>
</div>
