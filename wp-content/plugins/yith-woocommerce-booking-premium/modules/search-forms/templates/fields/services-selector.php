<?php
/**
 * Booking Search Form Field Services
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/services.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;
?>

<?php
$services          = yith_wcbk_get_services();
$searched_services = yith_wcbk_get_query_string_param( 'services' );
$searched_services = ! ! $searched_services && is_array( $searched_services ) ? $searched_services : array();
$searched_services = array_map( 'absint', $searched_services );

$services = ! ! $services && is_array( $services ) ? $services : array();
$services = array_filter(
	$services,
	function ( $service ) {
		return ! $service->is_hidden_in_search_forms();
	}
);
?>

<?php if ( $services ) : ?>
	<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--services">
		<label class="yith-wcbk-booking-search-form__row__label">
			<?php echo esc_html( yith_wcbk_get_label( 'services' ) ); ?>
		</label>
		<div class="yith-wcbk-booking-search-form__row__content">
			<?php
			$field = array(
				'type'        => 'services-selector',
				'name'        => 'services',
				'placeholder' => __( 'Select services', 'yith-booking-for-woocommerce' ),
				'services'    => $services,
				'value'       => $searched_services,
			);
			yith_wcbk_print_field( $field );
			?>
		</div>
	</div>
<?php endif; ?>
