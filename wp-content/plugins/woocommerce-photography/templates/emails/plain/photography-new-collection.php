<?php
/**
 * New collection plain email notification.
 *
 * @author  WooThemes
 * @package WC_Photography/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

echo __( 'The following photo collection(s) has been added to your account:', 'woocommerce-photography' ) . "\n\n";

foreach ( $collections as $collection_id => $collection_name ) {
	echo sprintf( '* %s: %s', wc_clean( $collection_name ), esc_url( get_term_link( $collection_id, 'images_collections' ) ) ) . "\n";
}

echo "\n";

echo sprintf( __( 'You can view your collections on the My Account page: %s', 'woocommerce-photography' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) . "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
