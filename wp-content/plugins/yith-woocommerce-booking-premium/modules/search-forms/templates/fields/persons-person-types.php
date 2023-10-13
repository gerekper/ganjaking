<?php
/**
 * Booking Search Form Field Person Types
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/persons-person-types.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! yith_wcbk_is_people_module_active() ) {
	return;
}

$person_types          = yith_wcbk()->person_type_helper()->get_person_type_ids();
$searched_person_types = yith_wcbk_get_query_string_param( 'person_types' );
$searched_person_types = ! ! $searched_person_types && is_array( $searched_person_types ) ? $searched_person_types : array();
?>

<?php if ( $person_types && is_array( $person_types ) ) : ?>
	<?php foreach ( $person_types as $person_type_id ) : ?>
		<?php
		$quantity = $searched_person_types[ $person_type_id ] ?? '';
		?>

		<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--person-type yith-wcbk-booking-search-form__row--person-type-<?php echo esc_attr( $person_type_id ); ?>">
			<label class="yith-wcbk-booking-search-form__row__label">
				<?php echo esc_html( yith_wcbk_get_person_type_title( $person_type_id ) ); ?>
			</label>
			<div class="yith-wcbk-booking-search-form__row__content">
				<input type="number" class="yith-wcbk-booking-person-types yith-wcbk-booking-field"
						name="person_types[<?php echo esc_attr( $person_type_id ); ?>]" min="0" step="1"
						data-person-type-id="<?php echo esc_attr( $person_type_id ); ?>" value="<?php echo esc_attr( $quantity ); ?>"/>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
