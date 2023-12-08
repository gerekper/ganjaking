<?php
/**
 * The template for displaying the product element hidden quantity for the builder mode
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $name ) && isset( $quantity_min ) && isset( $quantity_max ) ) {
	$input_id   = uniqid( 'quantity_' );
	$input_name = $name . '_quantity';
	if ( isset( $option ) ) {
		if ( '' !== $option['_default_value_counter'] ) {
			$input_name = $name . '_' . $option['_default_value_counter'] . '_quantity';
		}
		$input_value  = isset( $_REQUEST[ $name . '_quantity' ] ) ? absint( stripslashes_deep( $_REQUEST[ $name . '_quantity' ] ) ) : $quantity_min; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$input_value  = floatval( $input_value );
		$classes      = [ 'tm-qty', 'tm-bsbb', 'tm-hidden' ];
		$max_value    = floatval( $quantity_max );
		$min_value    = floatval( $quantity_min );
		$step         = 1;
		$inputmode    = apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' );
		$product_name = '';

		$input_args = [
			'nodiv'   => 1,
			'default' => $input_value,
			'type'    => 'hidden',
			'tags'    => [
				'id'       => $input_id,
				'name'     => $input_name,
				'class'    => join( ' ', (array) $classes ),
				'data-min' => $min_value,
				'data-max' => ( 0 < $max_value ) ? $max_value : '',
			],
		];

		if ( ! ( $max_value && $min_value === $max_value ) ) {
			$input_args['type']              = 'number';
			$input_args['tags']['step']      = $step;
			$input_args['tags']['min']       = $min_value;
			$input_args['tags']['max']       = ( 0 < $max_value ) ? $max_value : '';
			$input_args['tags']['title']     = esc_attr__( 'Qty', 'woocommerce-tm-extra-product-options' );
			$input_args['tags']['inputmode'] = $inputmode;
		}
		?>
		<div class="tm-quantity tm-hidden">
		<?php
		THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
		?>
		</div>
		<?php

	}
}
