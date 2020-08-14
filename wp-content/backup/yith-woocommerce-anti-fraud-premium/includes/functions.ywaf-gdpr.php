<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'wp_privacy_personal_data_exporters', 'ywaf_register_anti_fraud_exporter' );
add_filter( 'wp_privacy_personal_data_erasers', 'ywaf_register_anti_fraud_eraser' );

/**
 * Registers the personal data exporter for anti fraud blacklist and paypal verified addresses.
 *
 * @since   1.1.5
 *
 * @param   $exporters
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywaf_register_anti_fraud_exporter( $exporters ) {
	$exporters['ywaf-status'] = array(
		'exporter_friendly_name' => __( 'Anti-Fraud Status', 'yith-woocommerce-anti-fraud' ),
		'callback'               => 'ywaf_anti_fraud_exporter',
	);

	return $exporters;
}

/**
 * Finds and exports personal data associated with an email address for anti fraud blacklist addresses.
 *
 * @since   1.1.5
 *
 * @param   $email_address
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywaf_anti_fraud_exporter( $email_address ) {
	$data_to_export  = array();
	$email_blacklist = get_option( 'ywaf_email_blacklist_list' );
	$paypal_verified = get_option( 'ywaf_paypal_verified' );
	$blacklist       = ( $email_blacklist != '' ) ? explode( ',', $email_blacklist ) : array();
	$paypal_list     = ( $paypal_verified != '' ) ? explode( ',', $paypal_verified ) : array();

	if ( in_array( $email_address, $blacklist ) ) {

		$data_to_export[] = array(
			'group_id'    => 'ywaf_status',
			'group_label' => __( 'Anti-Fraud Status', 'yith-woocommerce-anti-fraud' ),
			'item_id'     => "ywaf-0",
			'data'        => array(
				array(
					'name'  => __( 'Blacklist Status', 'yith-woocommerce-anti-fraud' ),
					'value' => __( 'The email address is blacklisted!', 'yith-woocommerce-anti-fraud' ),
				)
			),
		);

	}

	if ( in_array( $email_address, $paypal_list ) ) {

		$data_to_export[] = array(
			'group_id'    => 'ywaf_status',
			'group_label' => __( 'Anti-Fraud Status', 'yith-woocommerce-anti-fraud' ),
			'item_id'     => "ywaf-1",
			'data'        => array(
				array(
					'name'  => __( 'Paypal Verification', 'yith-woocommerce-anti-fraud' ),
					'value' => __( 'The email address is verified', 'yith-woocommerce-anti-fraud' ),
				)
			),
		);

	}

	return array(
		'data' => $data_to_export,
		'done' => true,
	);
}

/**
 * Registers the personal data eraser for anti fraud blacklist and paypal verified addresses
 *
 * @since   1.1.5
 *
 * @param   $erasers
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywaf_register_anti_fraud_eraser( $erasers ) {
	$erasers['ywaf-status'] = array(
		'eraser_friendly_name' => __( 'Anti-Fraud Status', 'yith-woocommerce-anti-fraud' ),
		'callback'             => 'ywaf_anti_fraud_eraser',
	);

	return $erasers;
}

/**
 * Erases personal data associated with an email address for anti fraud blacklist and paypal verified addresses.
 *
 * @since 1.1.5
 *
 * @param  $email_address
 *
 * @return array
 */
function ywaf_anti_fraud_eraser( $email_address ) {

	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	$items_removed   = 0;
	$email_blacklist = get_option( 'ywaf_email_blacklist_list' );
	$paypal_verified = get_option( 'ywaf_paypal_verified' );
	$blacklist       = ( $email_blacklist != '' ) ? explode( ',', $email_blacklist ) : array();
	$paypal_list     = ( $paypal_verified != '' ) ? explode( ',', $paypal_verified ) : array();

	if ( ( $key = array_search( $email_address, $blacklist ) ) !== false ) {

		unset( $blacklist[ $key ] );
		update_option( 'ywaf_email_blacklist_list', implode( ',', $blacklist ) );
		$items_removed += 1;

	}

	if ( ( $key = array_search( $email_address, $paypal_list ) ) !== false ) {

		unset( $paypal_list[ $key ] );
		update_option( 'ywaf_paypal_verified', implode( ',', $paypal_list ) );
		$items_removed += 1;

	}

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => true,
	);
}

