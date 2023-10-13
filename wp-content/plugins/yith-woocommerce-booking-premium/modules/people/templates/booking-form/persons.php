<?php
/**
 * Booking form persons template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Modules\People\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! $product->has_people() ) {
	return;
}

?>
<div class="yith-wcbk-form-section-persons-wrapper yith-wcbk-form-section-wrapper">
	<?php
	if ( ! $product->has_people_types_enabled() ) {
		yith_wcbk_get_module_template( 'people', 'booking-form/persons/persons.php', compact( 'product' ), 'single-product/add-to-cart/' );
	} else {
		$person_types = $product->get_enabled_people_types();

		if ( yith_wcbk()->settings->is_people_selector_enabled() ) {
			yith_wcbk_get_module_template( 'people', 'booking-form/persons/people-selector.php', compact( 'person_types', 'product' ), 'single-product/add-to-cart/' );
		} else {
			yith_wcbk_get_module_template( 'people', 'booking-form/persons/person-types.php', compact( 'person_types', 'product' ), 'single-product/add-to-cart/' );
		}
	}
	?>
</div>

