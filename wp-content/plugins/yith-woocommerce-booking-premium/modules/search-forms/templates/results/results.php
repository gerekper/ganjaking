<?php
/**
 * Booking Search Form Results Template
 *
 * Shows booking search form results
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/results.php.
 *
 * @var WP_Query $products        WP Query for products.
 * @var array    $booking_request Booking request.
 * @var int      $current_page    Current page number.
 * @var array    $product_ids     Product IDs.
 *
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;
?>

<?php
/**
 * DO_ACTION: yith_wcbk_booking_before_search_form_results
 * Hook to render something before the Search Form results.
 */
do_action( 'yith_wcbk_booking_before_search_form_results' );
?>

<?php if ( $products->have_posts() ) : ?>

	<ul class="yith-wcbk-search-form-result-products">
		<?php yith_wcbk_get_module_template( 'search-forms', 'results/results-list.php', compact( 'products', 'booking_request' ), 'booking/search-form/' ); ?>
	</ul>

	<?php
	$posts_per_page = apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 );
	$last_page      = $posts_per_page > 0 ? ceil( count( $product_ids ) / $posts_per_page ) : 0;
	if ( $last_page > 1 ) :
		?>

		<div class="yith-wcbk-search-form-results-show-more"
			data-page="<?php echo esc_attr( $current_page ); ?>"
			data-product-ids='<?php echo esc_attr( wp_json_encode( $product_ids ) ); ?>'
			data-booking-request='<?php echo esc_attr( wp_json_encode( $booking_request ) ); ?>'
			data-last-page='<?php echo esc_attr( $last_page ); ?>'
		><?php esc_html_e( 'Show more results...', 'yith-booking-for-woocommerce' ); ?></div>
	<?php endif; ?>

<?php endif; ?>

<?php
/**
 * DO_ACTION: yith_wcbk_booking_after_search_form_results
 * Hook to render something after the Search Form results.
 */
do_action( 'yith_wcbk_booking_after_search_form_results' );
?>
