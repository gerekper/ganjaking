<?php
/**
 * Duration Field in booking form
 *
 * @author        YITH <plugins@yithemes.com>
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/duration.php
 *
 * @var WC_Product_Booking $product
 * @package       YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$default_duration = yith_wcbk_get_query_string_param( 'duration' );

$duration            = $product->get_duration();
$duration_unit       = $product->get_duration_unit();
$is_fixed_blocks     = $product->is_type_fixed_blocks();
$min                 = $product->get_minimum_duration();
$max                 = $product->get_maximum_duration();
$show_duration_field = ! $is_fixed_blocks && $min !== $max;
$duration_number     = $show_duration_field ? 1 : ( $duration * $min );
$duration_label      = yith_wcbk_get_duration_label( $duration_number, $duration_unit, $show_duration_field ? 'unit' : 'duration' );
$duration_label      = apply_filters( 'yith_wcbk_booking_form_dates_duration_label_html', $duration_label, $product );

$duration_attributes = array(
	'step'      => 1,
	'min'       => $min,
	'pattern'   => '[0-9]*',
	'inputmode' => 'numeric',
);

$real_duration_attributes = array(
	'step'      => $duration,
	'min'       => $min * $duration,
	'pattern'   => '[0-9]*',
	'inputmode' => 'numeric',
);

if ( $max > 0 ) {
	$duration_attributes['max']      = $max;
	$real_duration_attributes['max'] = $max * $duration;
}

$field_id      = 'yith-wcbk-booking-duration-' . $product->get_id();
$extra_classes = array(
	'yith-wcbk-form-section-duration--type-' . sanitize_key( $product->get_duration_type() ),
);
if ( ! $show_duration_field ) {
	$extra_classes[] = 'yith-wcbk-form-section-duration--no-field';
}

$extra_classes       = implode( ' ', $extra_classes );
$duration_value      = max( $min, $default_duration );
$duration_value      = $max > 0 ? min( $max, $duration_value ) : $duration_value;
$real_duration_value = $duration_value * $duration;

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-duration <?php echo esc_attr( $extra_classes ); ?>">
	<label for="<?php echo esc_attr( $field_id ); ?>" class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( yith_wcbk_get_label( 'duration' ) ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<?php
		yith_wcbk_print_field(
			array(
				'class'             => 'yith-wcbk-booking-duration',
				'type'              => 'hidden',
				'name'              => 'duration',
				'custom_attributes' => $duration_attributes,
				'value'             => $duration_value,
			)
		);

		if ( $show_duration_field ) {
			yith_wcbk_print_field(
				array(
					'class'             => 'yith-wcbk-booking-real-duration',
					'type'              => 'number',
					'id'                => $field_id,
					'name'              => '',
					'custom_attributes' => $real_duration_attributes,
					'value'             => $real_duration_value,
				)
			);
		}
		?>

		<span class="yith-wcbk-booking-duration__label"><?php echo esc_html( $duration_label ); ?></span>

		<?php
		/**
		 * DO_ACTION: yith_wcbk_booking_form_after_label_duration
		 * Hook to output something after the "duration" label in the booking form.
		 *
		 * @param int    $duration        The duration.
		 * @param string $duration_unit   The duration unit.
		 * @param int    $duration_number The duration number.
		 * @param string $duration_label  The duration label.
		 */
		do_action( 'yith_wcbk_booking_form_after_label_duration', $duration, $duration_unit, $duration_number, $duration_label );
		?>
	</div>
</div>
