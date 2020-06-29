<?php
/**
 * Additional Fields List for order email
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @context email plain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo strtoupper( esc_html__( 'Additional info', 'yith-woocommerce-checkout-manager' ) ) . "\n\n";

foreach ( $fields as $field ) {
	echo wp_kses_post( $field['label'] ) . ': ' . wp_kses_post( $field['value'] ) . "\n";
}

