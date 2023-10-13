<?php
/**
 * Booking Search Form Field Categories
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/categories.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$booking_tag_args = array(
	'taxonomy'   => 'product_tag',
	'hide_empty' => true,
	'fields'     => 'id=>name',
);

$tags = yith_wcbk()->wp->get_terms( $booking_tag_args );

$searched_tags = yith_wcbk_get_query_string_param( 'tags' );
$searched_tags = ! ! $searched_tags && is_array( $searched_tags ) ? $searched_tags : array();
$searched_tags = array_map( 'absint', $searched_tags );
?>

<?php if ( ! ! $tags ) : ?>
	<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--tags">
		<label class="yith-wcbk-booking-search-form__row__label">
			<?php echo esc_html( apply_filters( 'yith_wcbk_search_form_label_tags', __( 'Tags', 'yith-booking-for-woocommerce' ) ) ); ?>
		</label>
		<div class="yith-wcbk-booking-search-form__row__content">
			<select name="tags[]" class="yith-wcbk-booking-tags yith-wcbk-select2" multiple>
				<?php foreach ( $tags as $tag_id => $tag_name ) : ?>
					<option value="<?php echo esc_attr( $tag_id ); ?>" <?php selected( in_array( $tag_id, $searched_tags, true ) ); ?>><?php echo esc_html( $tag_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>
