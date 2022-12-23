<?php
/**
 * Photography loop product quantity.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<div class="photography-quantity">

<?php
	woocommerce_quantity_input( array(
		'input_name'  => 'quantity[' . $product->get_id() . ']',
		'input_value' => apply_filters( 'wc_photography_quantity_input_value', 0, $product ),
		'min_value'   => apply_filters( 'wc_photography_quantity_input_min', 0, $product ),
		'max_value'   => apply_filters( 'wc_photography_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
	) );
?>

</div>
