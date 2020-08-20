<?php
/**
 * The template for displaying the product element quantity alt for the builder mode
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

$input_id     = uniqid( 'quantity_' );
$input_name   = $name . '_quantity';
$input_value  = isset( $_REQUEST[ $name . '_quantity' ] ) ? $_REQUEST[ $name . '_quantity' ] : $quantity_min;
$input_value  = floatval( $input_value );
$classes      = array('tm-qty-alt', 'tm-bsbb');
$max_value    = floatval( $quantity_max );
$min_value    = floatval( $quantity_min );
$step         = 1;
$inputmode    = apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' );
$product_name = '';

if ( $max_value && $min_value === $max_value && $input_value === $max_value ) {
?><div class="tm-quantity-alt tm-hidden"><div class="quantity"><input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>" name="<?php echo esc_attr( $input_name ); ?>" data-min="<?php echo esc_attr( $min_value ); ?>" data-max="<?php echo esc_attr( $max_value ); ?>" value="<?php echo esc_attr( $min_value ); ?>" /></div><?php if ($mode === "product"){
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-button.php' );
} ?></div><?php
} else {
?><div class="tm-quantity-alt"><div class="quantity"><input type="number" id="<?php echo esc_attr( $input_id ); ?>" class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>" step="<?php echo esc_attr( $step ); ?>" <?php 
if ($min_value!==''){?>
min="<?php echo esc_attr( $min_value ); ?>"<?php
}
if ($max_value){?>
	max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"<?php
}
?> data-min="<?php echo esc_attr( $min_value ); ?>" data-max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php esc_attr_e( 'Qty', 'woocommerce-tm-extra-product-options' ); ?>" size="4" inputmode="<?php echo esc_attr( $inputmode ); ?>" /></div><?php if ($mode === "product"){
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-button.php' );
} ?></div><?php
}
