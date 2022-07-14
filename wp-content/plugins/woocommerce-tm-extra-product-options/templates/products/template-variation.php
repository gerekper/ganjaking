<?php
/**
 * The template for displaying the product element variation id
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$input_name = $name;
if ( isset( $option ) && isset( $option['_default_value_counter'] ) && '' !== $option['_default_value_counter'] ) {
	$input_name .= '_' . $option['_default_value_counter'];
}
$input_name .= '_variation_id';
?>
<div class="tc-epo-element-product-container-variation-id tm-hidden">
	<input type="hidden" class="product-variation-id" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $variation_id ); ?>">
</div>
