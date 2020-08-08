<?php
/**
 * Acknowledgement Email Content
 *
 * @author      StoreApps
 * @version     1.0.1
 * @package     woocommerce-smart-coupons/templates/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $store_credit_label;

if ( ! isset( $email ) ) {
	$email = null;
}

if ( has_action( 'woocommerce_email_header' ) ) {
	do_action( 'woocommerce_email_header', $email_heading, $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	} else {
		woocommerce_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}
}

if ( ! empty( $store_credit_label['singular'] ) && ! empty( $store_credit_label['plural'] ) ) {
	/* translators: 1. Receiver's count 2. Singular/Plural label for store credit(s) 3. Receiver name 4. Receiver details */
	echo esc_html( sprintf( __( 'You have successfully sent %1$d %2$s to %3$s (%4$s)', 'woocommerce-smart-coupons' ), $receiver_count, ucwords( _n( $store_credit_label['singular'], $store_credit_label['plural'], count( $receivers_detail ) ) ), $gift_certificate_receiver_name, implode( ', ', array_unique( $receivers_detail ) ) ) ); // phpcs:ignore
} else {
	/* translators: 1. Receiver's count 2. Gift Card/s 3. Receiver name 4. Receiver details */
	echo esc_html( sprintf( __( 'You have successfully sent %1$d %2$s to %3$s (%4$s)', 'woocommerce-smart-coupons' ), $receiver_count, _n( 'Gift Card', 'Gift Cards', count( $receivers_detail ), 'woocommerce-smart-coupons' ), $gift_certificate_receiver_name, implode( ', ', array_unique( $receivers_detail ) ) ) );
}

if ( has_action( 'woocommerce_email_footer' ) ) {
	do_action( 'woocommerce_email_footer', $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-footer.php' );
	} else {
		woocommerce_get_template( 'emails/email-footer.php' );
	}
}
