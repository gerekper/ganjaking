<?php
/**
 * YITH WooCommerce Waiting List Mail Template Plain
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) exit; {

} // Exit if accessed directly.

echo esc_html( wp_strip_all_tags( $email_heading ) ) . "\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $email_content ) ) ) . "\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );