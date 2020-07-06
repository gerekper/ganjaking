<?php
/**
 * Register fields Newsletter
 *
 * @package WC_Newsletter_Subscription/Templates
 * @version 2.9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $fields ) ) {
	foreach ( $fields as $key => $field ) {
		woocommerce_form_field( $key, $field, ( isset( $field['value'] ) ? $field['value'] : null ) );
	}
}
