<?php
/**
 * Booking Search Form Field Persons
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/persons-persons.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$persons = yith_wcbk_get_query_string_param( 'persons' );
$persons = ! ! $persons ? $persons : '';
?>

<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--persons">
	<label class="yith-wcbk-booking-search-form__row__label">
		<?php echo esc_html( yith_wcbk_get_label( 'people' ) ); ?>
	</label>
	<div class="yith-wcbk-booking-search-form__row__content">
		<input type="number" class="yith-wcbk-booking-field" name="persons" min="0" step="1" value="<?php echo esc_attr( $persons ); ?>"/>
	</div>
</div>
