<?php
/**
 * Booking Search Form Field Search
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/search.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$label         = ! empty( $field_data['label'] ) ? call_user_func( '__', $field_data['label'], 'yith-booking-for-woocommerce' ) : __( 'Search', 'yith-booking-for-woocommerce' );
$searched_term = yith_wcbk_get_query_string_param( 's' );
?>

<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--persons">
	<label class="yith-wcbk-booking-search-form__row__label">
		<?php echo esc_html( $label ); ?>
	</label>
	<div class="yith-wcbk-booking-search-form__row__content">
		<input type="text" class="yith-wcbk-booking-field" name="s" value="<?php echo esc_attr( $searched_term ); ?>"/>
	</div>
</div>
