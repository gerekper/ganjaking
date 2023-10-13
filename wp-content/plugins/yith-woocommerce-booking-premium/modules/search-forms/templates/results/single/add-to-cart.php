<?php
/**
 * Booking Search Form Single Result Add to Cart Template
 * shows the single result product
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/single/add-to-cart.php.
 *
 * @var WC_Product_Booking $product
 * @var array              $booking_data
 *
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

global $product;

$add_to_cart_allowed = ! ! $booking_data && ! empty( $booking_data['from'] ) && ( ! empty( $booking_data['to'] ) || ! empty( $booking_data['duration'] ) ) && ( ! $product->has_people_types_enabled() || ! empty( $booking_data['person_types'] ) ) && ! $product->has_time();
$add_to_cart_allowed = apply_filters( 'yith_wcbk_search_form_item_add_to_cart_allowed', $add_to_cart_allowed, $product, $booking_data );

$booking_data_array_for_hidden_fields = array();
foreach ( $booking_data as $booking_data_id => $booking_data_value ) {
	if ( 'person_types' === $booking_data_id && ! $product->has_people_types_enabled() ) {
		continue;
	}

	if ( is_array( $booking_data_value ) ) {
		foreach ( $booking_data_value as $child_booking_data_id => $child_booking_data_value ) {
			$current_id = $booking_data_id . "[{$child_booking_data_id}]";

			$booking_data_array_for_hidden_fields[ $current_id ] = $child_booking_data_value;
		}
	} else {
		$booking_data_array_for_hidden_fields[ $booking_data_id ] = $booking_data_value;
	}
}

?>

<?php if ( $add_to_cart_allowed ) : ?>
	<?php
	$form_name   = "yith_booking_add_to_cart_{$product->get_id()}";
	$form_action = ! $product->is_confirmation_required() ? 'add-to-cart' : 'booking-request-confirmation';
	?>
	<form class="yith-wcbk-search-form-result-product-add-to-cart-form" name="<?php echo esc_attr( $form_name ); ?>" method="post" enctype='multipart/form-data'>

		<input type="hidden" name="<?php echo esc_attr( $form_action ); ?>" value="<?php echo esc_attr( $product->get_id() ); ?>"/>

		<?php foreach ( $booking_data_array_for_hidden_fields as $booking_data_id => $booking_data_value ) : ?>

			<input type="hidden" name="<?php echo esc_attr( $booking_data_id ); ?>" value="<?php echo esc_attr( $booking_data_value ); ?>">

		<?php endforeach; ?>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<a href="#" onclick="document.<?php echo esc_attr( $form_name ); ?>.submit()">
			<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
		</a>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
<?php else : ?>
	<a href="<?php echo esc_url( $product->get_permalink_with_data( $booking_data ) ); ?>">
		<?php echo esc_html( $product->add_to_cart_text() ); ?>
	</a>
<?php endif; ?>
