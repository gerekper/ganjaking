<?php
/**
 * Booking Search Form Field Categories
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/categories.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 *
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$booking_cat_args   = array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'fields'     => 'id=>name',
);
$booking_categories = get_option( 'yith-wcbk-booking-categories', array() );

if ( yith_wcbk()->settings->get_booking_categories_to_show() ) {
	$booking_cat_args['include'] = array_merge( yith_wcbk()->settings->get_booking_categories(), array( 0 ) );
}

$categories = yith_wcbk()->wp->get_terms( $booking_cat_args );

$searched_categories = yith_wcbk_get_query_string_param( 'categories' );
$searched_categories = array_map( 'absint', (array) apply_filters( 'yith_wcbk_searched_categories', ! ! $searched_categories && is_array( $searched_categories ) ? $searched_categories : array(), $search_form ) );

if ( ! ! $categories ) : ?>
	<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--categories">
		<label class="yith-wcbk-booking-search-form__row__label">
			<?php echo esc_html( apply_filters( 'yith_wcbk_search_form_label_categories', __( 'Categories', 'yith-booking-for-woocommerce' ) ) ); ?>
		</label>
		<div class="yith-wcbk-booking-search-form__row__content">
			<select name="categories[]" class="yith-wcbk-booking-categories yith-wcbk-select2" multiple>
				<?php foreach ( $categories as $category_id => $category_name ) : ?>
					<option value="<?php echo esc_attr( $category_id ); ?>" <?php selected( in_array( $category_id, $searched_categories, true ) ); ?>><?php echo esc_html( $category_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>
