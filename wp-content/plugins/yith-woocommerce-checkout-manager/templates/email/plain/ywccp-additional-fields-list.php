<?php
/**
 * Additional Fields List for order email
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @context email plain
 */

defined( 'YWCCP' ) || exit; // Exit if accessed directly.

echo esc_html( strtoupper( __( 'Additional info', 'yith-woocommerce-checkout-manager' ) ) ) . "\n\n";

foreach ( $fields as $field ) {
	echo wp_kses_post( $field['label'] ) . ': ' . wp_kses_post( $field['value'] ) . "\n";
}

