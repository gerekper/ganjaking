<?php
/**
 * Template Name: Redsys Add Card
 *
 * @package WooCommerce Redsys Gateway
 * @since 10.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

/*
* Copyright: (C) 2013 - 2024 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Change the order received text
 *
 * @param string $text The text.
 * @param object $order The order.
 */
function redsys_change_order_received_text( $text, $order ) {

	if ( ! empty( $order ) ) {
		$order_id = $order->get_id();

		if ( ! WCRed()->is_paid( $order_id ) && ( 'redsys-pre' !== $order->get_status() ) && WCRed()->is_redsys_order( $order_id, 'redsys' ) && 'yes' === WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$debug = new WC_Logger();
				$debug->add( 'redsys-thankyou-page', 'is redsys & is not paid' );
			}
			$sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'redsys' );
			if ( $sendemailthankyoutxt ) {
					$sendemailthankyoutxt = $sendemailthankyoutxt;
			} else {
				$sendemailthankyoutxt = '<p><b>' . __( 'ATTENTION 1:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid. Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.', 'woocommerce-redsys' ) . '</b></p>';
			}
			redsys_send_warning_email_thankyou_page( $order_id );
			return $text . ' ' . $sendemailthankyoutxt;
		} elseif ( ! WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id, 'redsysbank' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$debug = new WC_Logger();
				$debug->add( 'redsys-thankyou-page', 'is redsysbank & is waiting transfer' );
			}
			$codigoswift  = WCRed()->get_redsys_option( 'codigoswift', 'redsysbank' );
			$iban         = WCRed()->get_redsys_option( 'iban', 'redsysbank' );
			$beneficiario = WCRed()->get_redsys_option( 'beneficiario', 'redsysbank' );
			$text         = $text;
			$text        .= '<p>' . __( 'You must make the bank transfer to the following account:', 'woocommerce-redsys' ) . '</p>';
			$text        .= '<p><b>' . __( 'SWIFT:', 'woocommerce-redsys' ) . '</b> ' . $codigoswift . '</p>';
			$text        .= '<p><b>' . __( 'IBAN:', 'woocommerce-redsys' ) . '</b> ' . $iban . '</p>';
			$text        .= '<p><b>' . __( 'beneficiary:', 'woocommerce-redsys' ) . '</b> ' . $beneficiario . '</p>';
			return $text;
		} elseif ( ! WCRed()->is_paid( $order_id ) && ( 'redsys-pre' !== $order->get_status() ) && WCRed()->is_redsys_order( $order_id, 'redsys' ) && 'yes' === WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$debug = new WC_Logger();
				$debug->add( 'redsys-thankyou-page', 'is preauthorizationsredsys & is not paid' );
			}
			$sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'preauthorizationsredsys' );
			if ( $sendemailthankyoutxt ) {
					$sendemailthankyoutxt = $sendemailthankyoutxt;
			} else {
				$sendemailthankyoutxt = '<p>' . __( '<b>ATTENTION 2:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid. Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.', 'woocommerce-redsys' ) . '</p>';
			}
			redsys_send_warning_email_thankyou_page( $order_id );
			return $text . ' ' . $sendemailthankyoutxt;
		}
		return $text;
	} else {
		return $text;
	}
}
add_filter( 'woocommerce_thankyou_order_received_text', 'redsys_change_order_received_text', 20, 2 );

/**
 * Send email to admin if order is not marked as paid
 *
 * @param int $order_id Order ID.
 */
function redsys_send_warning_email_thankyou_page( $order_id ) {

	$admin_url    = admin_url();
	$url_to_order = WCRed()->get_order_edit_url( $order_id );
	$url_to_woo   = 'https://woocommerce.com/my-account/create-a-ticket/';
	$to           = get_bloginfo( 'admin_email' );
	$subject      = __( 'Possible problem with Redsys notifying the website.', 'woocommerce-redsys' );
	$body         = __( 'We have detected that a user has reached the thank you page from Redsys, but the order is not marked as paid.', 'woocommerce-redsys' );
	$body        .= '<p>' . __( 'This may mean that Redsys has had a problem contacting the website.', 'woocommerce-redsys' ) . '</p>';
	$body        .= '<p>' . sprintf( __( 'The Order number is: %1$s. You can see the order  <a href="%2$s">here</a></p>', 'woocommerce-redsys' ), esc_html( $order_id ), esc_url( $url_to_order ) ) . '</p>'; // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$body        .= '<p>' . __( 'Don\'t forget to check because a customer may have paid for an order and you don\'t know it.</p>', 'woocommerce-redsys' ) . '</p>';
	$body        .= '<p>' . __( 'What steps should I take now?</p>', 'woocommerce-redsys' ) . '</p>';
	$body        .= '<ul>';
	$body        .= '<li>' . __( 'Access Redsys (your bank should have given you the access address)', 'woocommerce-redsys' ) . '</li>';
	$body        .= '<li>' . __( 'Go to Administración > Inicio', 'woocommerce-redsys' ) . '</li>';
	$body        .= '<li>' . __( 'Here you will see all the transactions that have taken place in your terminal.', 'woocommerce-redsys' ) . '</li>';
	$body        .= '<li>' . sprintf( __( 'Now look in the Description column for the one that says "Order %s"', 'woocommerce-redsys' ), esc_html( $order_id ) ) . '</li>'; // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$body        .= '<li>' . __( 'Once located, look in the column "Result operation and code" if in that column it is Authorized and with a number, type "Autorización xxxxxxx", is that there has been a problem on the part of Redsys to notify your site of the payment.', 'woocommerce-redsys' ) . '</li>';
	$body        .= '<li>' . sprintf( __( 'If authorized, go to <a href="%s">here</a> and mark the order as Processing or Completed.', 'woocommerce-redsys' ), $url_to_order ) . '</li>'; // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$body        .= '</ul>';
	$body        .= '<p>' . sprintf( __( 'If this error is repeated in all orders, please contact the developer of the plugin, José Conti, opening a ticket at <a href="%s">WooCommerce.com</a>.</p>', 'woocommerce-redsys' ), $url_to_woo ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$headers      = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $to, $subject, $body, $headers );
	$message = __( '⚠️ We have detected that a user has reached the thank you page from Redsys, but the order is not marked as paid.', 'woocommerce-redsys' ) . ' URL: ' . $url_to_order . ' Order: ' . esc_html( $order_id );
	WCRed()->push( $message );
	return true;
}
