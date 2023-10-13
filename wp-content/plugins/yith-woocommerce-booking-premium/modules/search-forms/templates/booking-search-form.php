<?php
/**
 * Booking Search Form Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/booking-search-form.php.
 *
 * @var YITH_WCBK_Search_Form $search_form The search form.
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

// Late enqueue scripts.
wp_enqueue_script( 'yith-wcbk-booking-search-form' );

$show_results = $search_form->get_show_results();
$form_method  = 'shop' === $show_results ? 'get' : 'post';
$shop_url     = ! get_option( 'permalink_structure' ) ? get_post_type_archive_link( 'product' ) : get_permalink( wc_get_page_id( 'shop' ) );

$classes = array(
	'yith-wcbk-booking-search-form',
	'yith-wcbk-booking-search-form-' . $search_form->get_id(),
	'yith-wcbk-booking-search-form--' . $search_form->get_layout() . '-layout',
	'show-results-' . $show_results,
);

$category = ! empty( $cat ) ? $cat : false;
if ( 'current' === $category ) {
	$category       = false;
	$current_object = get_queried_object();
	if ( $current_object instanceof WP_Term && 'product_cat' === $current_object->taxonomy ) {
		$category = $current_object->term_id;
	}
}

?>

<?php
/**
 * DO_ACTION: yith_wcbk_booking_before_search_form
 * Hook to render something before the Search Form.
 *
 * @param YITH_WCBK_Search_Form $search_form The search form.
 */
do_action( 'yith_wcbk_booking_before_search_form', $search_form );
?>

<div
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	data-search-form-id="<?php echo esc_attr( $search_form->get_id() ); ?>"
	data-search-form-result="#yith-wcbk-booking-search-form-result-<?php echo esc_attr( $search_form->get_id() ); ?>"
>
	<form method="<?php echo esc_attr( $form_method ); ?>" enctype='multipart/form-data' action="<?php echo esc_url( $shop_url ); ?>" autocomplete="off">
		<input type="hidden" name="yith-wcbk-booking-search" value="search-bookings"/>

		<div class="yith-wcbk-booking-search-form__fields">
			<?php
			foreach ( $search_form->get_fields() as $field_key => $field_data ) {
				if ( 'yes' === $field_data['enabled'] ) {
					/**
					 * DO_ACTION: yith_wcbk_booking_search_form_before_print_field
					 * Hook to render something before printing a field of the Search Form.
					 *
					 * @param string                $field_key   The field key.
					 * @param array                 $field_data  The field data..
					 * @param YITH_WCBK_Search_Form $search_form The search form.
					 */
					do_action( 'yith_wcbk_booking_search_form_before_print_field', $field_key, $field_data, $search_form );

					/**
					 * DO_ACTION: yith_wcbk_booking_search_form_print_field
					 * Hook to render something when printing a field of the Search Form.
					 *
					 * @param string                $field_key   The field key.
					 * @param array                 $field_data  The field data..
					 * @param YITH_WCBK_Search_Form $search_form The search form.
					 */
					do_action( 'yith_wcbk_booking_search_form_print_field', $field_key, $field_data, $search_form );

					/**
					 * DO_ACTION: yith_wcbk_booking_search_form_after_print_field
					 * Hook to render something after printing a field of the Search Form.
					 *
					 * @param string                $field_key   The field key.
					 * @param array                 $field_data  The field data..
					 * @param YITH_WCBK_Search_Form $search_form The search form.
					 */
					do_action( 'yith_wcbk_booking_search_form_after_print_field', $field_key, $field_data, $search_form );
				}
			}
			?>

			<?php
			/**
			 * DO_ACTION: yith_wcbk_booking_search_form_after_print_fields
			 * Hook to render something after the fields rendered in the Search Form.
			 *
			 * @param YITH_WCBK_Search_Form $search_form The search form.
			 */
			do_action( 'yith_wcbk_booking_search_form_after_print_fields', $search_form );
			?>

			<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--submit">
				<div class="yith-wcbk-booking-search-form__row__content">
					<button type="submit" class="button alt yith-wcbk-booking-search-form-submit"><?php echo esc_html( apply_filters( 'yith_wcbk_search_form_submit_label', __( 'Search', 'yith-booking-for-woocommerce' ) ) ); ?></button>
				</div>
			</div>
		</div>

		<?php if ( $category ) : ?>
			<input type="hidden" name="categories" value="<?php echo absint( $category ); ?>">
		<?php endif; ?>
	</form>
</div>

<?php
/**
 * DO_ACTION: yith_wcbk_booking_after_search_form
 * Hook to render something after the Search Form.
 *
 * @param YITH_WCBK_Search_Form $search_form The search form.
 */
do_action( 'yith_wcbk_booking_after_search_form', $search_form );
?>
