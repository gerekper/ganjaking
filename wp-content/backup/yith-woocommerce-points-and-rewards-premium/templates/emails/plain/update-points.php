<?php
/**
 * Plain Template Email
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @version 1.0.0
 * @author  YITH
 *
 * @var string $email_heading
 * @var string $email_content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo esc_html( wp_strip_all_tags( $email_heading . "\n\n" ) );

echo esc_html( wp_strip_all_tags( wptexturize( $email_content ) ) );
echo "\n****************************************************\n\n";

echo esc_html( wp_strip_all_tags( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) );
