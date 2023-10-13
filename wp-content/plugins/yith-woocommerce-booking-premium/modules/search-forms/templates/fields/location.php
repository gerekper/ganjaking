<?php
/**
 * Booking Search Form Field Location
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/location.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$default_range  = ! empty( $field_data['default_range'] ) ? $field_data['default_range'] : 30;
$default_range  = ! empty( $field_data['default_range'] ) ? $field_data['default_range'] : 30;
$show_range     = 'yes' === ( $field_data['show_range'] ?? 'yes' );
$location       = yith_wcbk_get_query_string_param( 'location' );
$location_range = yith_wcbk_get_query_string_param( 'location_range' );
$location_range = ! ! $location_range ? $location_range : $default_range;

wp_enqueue_script( 'yith-wcbk-google-maps-autocomplete' );

?>

<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--location">
	<label class="yith-wcbk-booking-search-form__row__label">
		<?php echo esc_html( apply_filters( 'yith_wcbk_search_form_label_location', yith_wcbk_get_label( 'location' ) ) ); ?>
	</label>
	<div class="yith-wcbk-booking-search-form__row__content">
		<input type="text" name="location" class="yith-wcbk-booking-location yith-wcbk-booking-field yith-wcbk-google-maps-places-autocomplete" value="<?php echo esc_attr( $location ); ?>"/>
	</div>
</div>

<?php if ( $show_range ) : ?>
	<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--location-range">
		<label class="yith-wcbk-booking-search-form__row__label">
			<?php echo esc_html( yith_wcbk_get_label( 'distance' ) ); ?>
		</label>
		<div class="yith-wcbk-booking-search-form__row__content">
			<input type="number" name="location_range" class="yith-wcbk-booking-location-range yith-wcbk-booking-field" min="0" value="<?php echo esc_attr( $location_range ); ?>"/>
		</div>
	</div>
<?php else : ?>
	<input type="hidden" name="location_range" class="yith-wcbk-booking-location-range" value="<?php echo esc_attr( $location_range ); ?>"/>
<?php endif; ?>
