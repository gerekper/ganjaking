<?php
/**
 * Admin ask estimate email
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist_data \YITH_WCWL_Wishlist
 * @var $email_heading string
 * @var $email \WC_Email
 * @var $user_formatted_name string
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __( 'You have received an estimate request from %s. The request is the following:', 'yith-woocommerce-wishlist' ), $user_formatted_name ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_wishlist_table', $wishlist_data );

echo sprintf( __( 'Wishlist: %s', 'yith-woocommerce-wishlist'), $wishlist_data->get_token() ) . "\n";
echo sprintf( __( 'Wishlist link: %s', 'yith-woocommerce-wishlist'), $wishlist_data->get_url() ) . "\n";

echo "\n";

if( $wishlist_data->has_items() ):
    foreach( $wishlist_data->get_items() as $item ):
        $product = $item->get_product();
        echo $product->get_title() . ' | ';
        echo $item->get_quantity();
        echo "\n";
    endforeach;
endif;

echo "\n****************************************************\n\n";

if( ! empty( $additional_notes ) ):
	echo "\n" . __( "Additional info:", 'yith-woocommerce-wishlist' ) . "\n";

	echo  $additional_notes . "\n";

	echo "\n****************************************************\n\n";
endif;

if( ! empty( $additional_data ) ):
	echo "\n" . __( "Additional data:", 'yith-woocommerce-wishlist' ) . "\n";

	foreach( $additional_data as $key => $value ):

		$key = strip_tags( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) );
		$value = strip_tags( $value );

		echo "{$key}: {$value}\n";

	endforeach;

	echo "\n****************************************************\n\n";
endif;

do_action( 'yith_wcwl_email_after_wishlist_table', $wishlist_data );

echo __( 'Customer details', 'yith-woocommerce-wishlist' ) . "\n";

echo __( 'Email:', 'yith-woocommerce-wishlist' ); echo $email->reply_email . "\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );