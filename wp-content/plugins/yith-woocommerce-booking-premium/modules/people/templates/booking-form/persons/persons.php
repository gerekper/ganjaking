<?php
/**
 * Persons field in booking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/persons/persons.php
 *
 * @var WC_Product_Booking $product The booking product.
 *
 * @package YITH\Booking\Modules\People\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$default_persons   = yith_wcbk_get_query_string_param( 'persons' );
$min               = $product->get_minimum_number_of_people();
$max               = $product->get_maximum_number_of_people();
$custom_attributes = array(
	'step' => 1,
	'min'  => $min,
);

if ( $max > 0 ) {
	$custom_attributes['max'] = $max;
}

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-persons">
	<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( apply_filters( 'yith_wcbk_people_label', yith_wcbk_get_label( 'people' ), $product ) ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'              => 'number',
				'id'                => 'yith-wcbk-booking-persons',
				'name'              => 'persons',
				'custom_attributes' => $custom_attributes,
				'value'             => max( $min, $default_persons ),
				'class'             => 'yith-wcbk-booking-persons yith-wcbk-number-minifield',
			)
		);
		?>
	</div>
</div>
