<?php
/**
 * The template for displaying the product element quantity alt for the builder mode
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$input_id = uniqid( 'quantity_' );
if ( isset( $_REQUEST['name'] ) || '' === $option['_default_value_counter'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$input_name = $name . '_quantity';
} else {
	$input_name = $name . '_' . $option['_default_value_counter'] . '_quantity';
}
$input_value    = isset( $_REQUEST[ $input_name ] ) ? absint( wp_unslash( $_REQUEST[ $input_name ] ) ) : $quantity_min; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$input_value    = floatval( $input_value );
$input_value_o  = $input_value;
$classes        = [ 'tm-qty-alt', 'tm-bsbb' ];
$max_value      = floatval( $quantity_max );
$max_value_o    = $max_value;
$min_value      = floatval( $quantity_min );
$min_value_o    = $min_value;
$step           = 1;
$inputmode      = apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' );
$product_name   = '';
$total_stock    = $current_product->is_type( 'variable' ) ? null : $current_product->get_stock_quantity();
$allow_quantity = true;
if ( ! $current_product->is_type( 'variable' ) ) {
	if ( ! $current_product->is_in_stock() || ( $current_product->managing_stock() && $total_stock < $min_value ) ) {
		if ( $total_stock > 0 ) {
			$min_value   = $min_value - $total_stock;
			$input_value = $min_value;
		} else {
			$min_value      = $total_stock;
			$input_value    = $total_stock;
			$allow_quantity = false;
		}
	}

	if ( $current_product->managing_stock() && ( $total_stock < $max_value || '' === $quantity_max ) ) {
		$max_value = $total_stock;
	}

	if ( $current_product->managing_stock() && $current_product->backorders_allowed() ) {
		$allow_quantity = true;
		$max_value      = $max_value_o;
		$min_value      = $min_value_o;
		$input_value    = $input_value_o;
		if ( $input_value < 0 ) {
			$input_value = $min_value;
		}
	}
}

if ( $allow_quantity ) {
	if ( $max_value && $min_value === $max_value && $input_value === $max_value ) {

		echo '<div class="tm-quantity-alt tm-hidden">';
		echo '<div class="quantity">';

		$input_args = [
			'nodiv'   => 1,
			'default' => $min_value,
			'type'    => 'hidden',
			'tags'    => [
				'id'       => $input_id,
				'name'     => $input_name,
				'class'    => join( ' ', (array) $classes ),
				'data-min' => $min_value,
				'data-max' => $max_value,
			],
		];
		if ( '' !== $min_value ) {
			$input_args['tags']['min'] = $min_value;
		}
		if ( $max_value ) {
			$input_args['tags']['max'] = ( 0 < $max_value ) ? $max_value : '';
		}
		THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
		echo '</div>';

		if ( 'product' === $mode ) {
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-button.php';
		}

		echo '</div>';

	} else {

		echo '<div class="tm-quantity-alt">';
		echo '<div class="quantity">';

			do_action( 'wc_epo_before_product_quantity_input_field' );

			$input_args = [
				'nodiv'   => 1,
				'default' => $input_value,
				'type'    => 'number',
				'tags'    => [
					'id'        => $input_id,
					'name'      => $input_name,
					'class'     => join( ' ', (array) $classes ),
					'step'      => $step,
					'inputmode' => $inputmode,
					'data-min'  => $min_value,
					'data-max'  => ( 0 < $max_value ) ? $max_value : '',
					'size'      => '4',
					'title'     => esc_html__( 'Qty', 'woocommerce-tm-extra-product-options' ),
				],
			];
			if ( '' !== $min_value ) {
				$input_args['tags']['min'] = $min_value;
			}
			if ( $max_value ) {
				$input_args['tags']['max'] = ( 0 < $max_value ) ? $max_value : '';
			}
			THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );

			do_action( 'wc_epo_after_product_quantity_input_field' );
			echo '</div>';

			if ( 'product' === $mode ) {
				include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-button.php';
			}
			echo '</div>';

	}
}
