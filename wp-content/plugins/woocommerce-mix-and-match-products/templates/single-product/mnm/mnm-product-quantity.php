<?php
/**
 * Mix and Match Product Quantity
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-quantity.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.9.4
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

global $product;

$child_id = $mnm_item->get_id();

if ( $product->is_in_stock() && $mnm_item->is_in_stock() ) {
			
	/**
	 * The quantity input name.
	 */
	$input_name = wc_mnm_get_child_input_name( $product->get_id() );

	/**
	 * The quantity input value.
	 */
	$quantity = isset( $_REQUEST[ $input_name ] ) && ! empty ( $_REQUEST[ $input_name ][ $child_id ] ) ? intval( $_REQUEST[ $input_name ][ $child_id ] ) : apply_filters( 'woocommerce_mnm_quantity_input', '', $mnm_item, $product );

	/**
	 * Filter woocommerce_mnm_child_quantity_input_args.
	 *
	 * @param array $args
	 * @param obj WC_Product
	 * @param obj WC_Product_Mix_and_Match
	 */
	$input_args = apply_filters( 'woocommerce_mnm_child_quantity_input_args',
		array(
			'input_name'  => $input_name . '[' . $child_id . ']',
			'input_value' => $quantity,
			'min_value'   => $product->get_child_quantity( 'min', $child_id ),
			'max_value'   => $product->get_child_quantity( 'max', $child_id ),
			'placeholder' => 0,
			'step'        => $product->get_child_quantity( 'step', $child_id ),
			'classes'     => array( 'qty', 'mnm-quantity' ),
		),
		$mnm_item,
		$product );

	woocommerce_quantity_input( $input_args, $mnm_item );

} else {

	/**
	 * Child item availability message.
	 *
	 * @param str $availability
	 * @param obj WC_Product
	 */
	echo apply_filters( 'woocommerce_mnm_availability_html', $product->get_child_availability_html( $child_id ), $mnm_item );
}