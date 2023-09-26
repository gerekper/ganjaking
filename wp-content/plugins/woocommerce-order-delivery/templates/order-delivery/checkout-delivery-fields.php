<?php
/**
 * Checkout delivery fields.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Checkout $checkout Checkout object.
 */

$fields = $checkout->get_checkout_fields( 'delivery' );

foreach ( $fields as $key => $field ) :
	woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
endforeach;
