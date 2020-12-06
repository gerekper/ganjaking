<?php
	
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_change_order_received_text( $text, $order ) {

	if ( ! empty( $order ) ) {
		$order_id = $order->get_id();
		if ( ! WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id, 'redsys' ) && 'yes' === WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' ) ) {
			$sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'redsys' );
			if ( $sendemailthankyoutxt ) {
					$sendemailthankyoutxt = $sendemailthankyoutxt;
			} else {
				$sendemailthankyoutxt = __( '<p><b>ATTENTION:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid. Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.</p>', 'woocommerce-redsys' );
			}
			redsys_send_warning_email_thankyou_page( $order_id );
			return $text . ' ' . $sendemailthankyoutxt;
		} elseif ( ! WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id, 'redsysbank' ) ) {
			$codigoswift  = WCRed()->get_redsys_option( 'codigoswift', 'redsysbank' );
			$iban         = WCRed()->get_redsys_option( 'iban', 'redsysbank' );
			$beneficiario = WCRed()->get_redsys_option( 'beneficiario', 'redsysbank' );
			//$gastos       = WCRed()->get_redsys_option( 'gastos', 'redsysbank' );
			$text         = $text;
			$text        .= '<p>' . __( 'You must make the bank transfer to the following account:', 'woocommerce-redsys' ) . '</p>';
			$text        .= '<p><b>' . __( 'SWIFT:', 'woocommerce-redsys' ) . '</b> ' . $codigoswift . '</p>';
			$text        .= '<p><b>' . __( 'IBAN:', 'woocommerce-redsys' ) . '</b> ' . $iban . '</p>';
			$text        .= '<p><b>' . __( 'beneficiary:', 'woocommerce-redsys' ) . '</b> ' . $beneficiario . '</p>';
			//$text        .= '<p><b>' . __( 'Expenses in charge of:', 'woocommerce-redsys' ) . '</b> ' . $gastos . '</p>';
			return $text;
		} elseif ( ! WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id, 'preauthorizationsredsys' ) && 'yes' === WCRed()->get_redsys_option( 'sendemailthankyou', 'preauthorizationsredsys' ) ) {
			$sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'preauthorizationsredsys' );
			if ( $sendemailthankyoutxt ) {
					$sendemailthankyoutxt = $sendemailthankyoutxt;
			} else {
				$sendemailthankyoutxt = __( '<p><b>ATTENTION:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid. Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.</p>', 'woocommerce-redsys' );
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

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_send_warning_email_thankyou_page( $order_id ) {

	$admin_url    = admin_url();
	$url_to_order = $admin_url . 'post.php?post=' . esc_attr( $order_id ) . '&action=edit';
	$url_to_woo   = 'https://woocommerce.com/my-account/create-a-ticket/';
	$to           = get_bloginfo( 'admin_email' );
	$subject      = __( 'Possible problem with Redsys notifying the website.', 'woocommerce-redsys' );
	$body         = __( 'We have detected that a user has reached the thank you page from Redsys, but the order is not marked as paid.', 'woocommerce-redsys' );
	$body        .= __( '<p>This may mean that Redsys has had a problem contacting the website.</p>', 'woocommerce-redsys' );
	$body        .= sprintf( __( '<p>The Order number is: %s. You can see the order  <a href="%s">here</a></p>', 'woocommerce-redsys' ), esc_html( $order_id ), esc_url( $url_to_order ) );
	$body        .= __('<p>Don\'t forget to check because a customer may have paid for an order and you don\'t know it.</p>', 'woocommerce-redsys' );
	$body        .= __( '<p>What steps should I take now?</p>', 'woocommerce-redsys' );
	$body        .= '<ul>';
	$body        .= __( '<li>Access Redsys (your bank should have given you the access address)</li>', 'woocommerce-redsys' );
	$body        .= __( '<li>Go to Administración > Inicio</li>', 'woocommerce-redsys' );
	$body        .= __( '<li>Here you will see all the transactions that have taken place in your terminal.</li>', 'woocommerce-redsys' );
	$body        .= sprintf( __( '<li>Now look in the Description column for the one that says "Order %s"</li>', 'woocommerce-redsys' ), esc_html( $order_id ) );
	$body        .= __( '<li>Once located, look in the column "Result operation and code" if in that column it is Authorized and with a number, type "Autorización xxxxxxx", is that there has been a problem on the part of Redsys to notify your site of the payment.</li>', 'woocommerce-redsys' );
	$body        .= sprintf( __( '<li>If authorized, go to <a href="%s">here</a> and mark the order as Processing or Completed.</li>', 'woocommerce-redsys' ), $url_to_order );
	$body        .= '</ul>';
	$body        .= sprintf( __( '<p>If this error is repeated in all orders, please contact the developer of the plugin, José Conti, opening a ticket at <a href="%s">WooCommerce.com</a>.</p>', 'woocommerce-redsys' ), $url_to_woo );

	$headers      = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $to, $subject, $body, $headers );

	return true;
}