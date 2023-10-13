<?php
/**
 * Admin ask estimate email
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Emails
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist_data       \YITH_WCWL_Wishlist
 * @var $email_heading       string
 * @var $email               \WC_Email
 * @var $user_formatted_name string
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

// translators: 1. Customer full name.
echo sprintf( esc_html__( 'You have received an estimate request from %s. The request is the following:', 'yith-woocommerce-wishlist' ), esc_html( $user_formatted_name ) ) . "\n\n";

echo "\n----------------------------------------\n\n";

/**
 * DO_ACTION: yith_wcwl_email_before_wishlist_table
 *
 * Allows to render some content or fire some action before the wishlist in the 'Ask for an estimate' email.
 *
 * @param YITH_WCWL_Wishlist $wishlist_data Wishlist object
 */
do_action( 'yith_wcwl_email_before_wishlist_table', $wishlist_data );

// translators: 1. Wishlist token.
echo sprintf( esc_html__( 'Wishlist: %s', 'yith-woocommerce-wishlist' ), esc_html( $wishlist_data->get_token() ) ) . "\n";
// translators: 1. Url to wishlist page.
echo sprintf( esc_html__( 'Wishlist link: %s', 'yith-woocommerce-wishlist' ), esc_html( $wishlist_data->get_url() ) ) . "\n";

echo "\n";

if ( $wishlist_data->has_items() ) :
	foreach ( $wishlist_data->get_items() as $item ) :
		$product = $item->get_product();
		echo esc_html( wp_strip_all_tags( $product->get_title() ) ) . ' | ';
		echo esc_html( $item->get_quantity() ) . ' | ';
		echo esc_html( wp_strip_all_tags( $item->get_formatted_product_price() ) );
		echo "\n";
	endforeach;
endif;

echo "\n----------------------------------------\n\n";

if ( ! empty( $additional_notes ) ) :
	echo "\n" . esc_html__( 'Additional info:', 'yith-woocommerce-wishlist' ) . "\n\n";

	echo esc_html( wp_strip_all_tags( $additional_notes ) ) . "\n";

	echo "\n----------------------------------------\n\n";
endif;

if ( ! empty( $additional_data ) ) :
	echo "\n" . esc_html__( 'Additional data:', 'yith-woocommerce-wishlist' ) . "\n\n";

	foreach ( $additional_data as $key => $value ) :

		$key   = wp_strip_all_tags( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) );
		$value = wp_strip_all_tags( $value );

		echo esc_html( "{$key}: {$value}" ) . "\n";

	endforeach;

	echo "\n----------------------------------------\n\n";
endif;

/**
 * DO_ACTION: yith_wcwl_email_after_wishlist_table
 *
 * Allows to render some content or fire some action after the wishlist in the 'Ask for an estimate' email.
 *
 * @param YITH_WCWL_Wishlist $wishlist_data Wishlist object
 */
do_action( 'yith_wcwl_email_after_wishlist_table', $wishlist_data );

echo esc_html__( 'Customer details', 'yith-woocommerce-wishlist' ) . "\n\n";

echo esc_html__( 'Email:', 'yith-woocommerce-wishlist' );
echo esc_html( wp_strip_all_tags( $email->reply_email ) ) . "\n";

echo "\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
