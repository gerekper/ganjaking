<?php
/**
 * Register new status with ID "wc-redsys-pre".
 *
 * @package WooCommerce Redsys Gateway
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Copyright: (C) 2013 - 2024 José Conti
 */
function redsys_register_preauthorized_status() {

	register_post_status(
		'wc-redsys-pre',
		array(
			'label'                     => 'Preauthorized by Redsys',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true, // show count All (12) , Completed (9) , Awaiting shipment (2) ...
			'label_count'               => _n_noop( __( 'Preauthorized <span class="count">(%s)</span>', 'woocommerce-redsys' ), __( 'Preauthorized <span class="count">(%s)</span>', 'woocommerce-redsys' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		)
	);
}
add_action( 'init', 'redsys_register_preauthorized_status' );

/**
 * Copyright: (C) 2013 - 2024 José Conti
 */
function redsys_register_resident_payment_status() {

	register_post_status(
		'wc-redsys-residentp',
		array(
			'label'                     => 'Resident Payment',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true, // show count All (12) , Completed (9) , Awaiting shipment (2) ...
			'label_count'               => _n_noop( __( 'Resident Payment <span class="count">(%s)</span>', 'woocommerce-redsys' ), __( 'Resident Payments <span class="count">(%s)</span>', 'woocommerce-redsys' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		)
	);
}
add_action( 'init', 'redsys_register_resident_payment_status' );

/**
 * Copyright: (C) 2013 - 2024 José Conti
 */
function redsys_register_pending_bank_transfer_payment_status() {

	register_post_status(
		'wc-redsys-pbankt',
		array(
			'label'                     => 'Pending Redsys Bank Transfer',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true, // show count All (12) , Completed (9) , Awaiting shipment (2) ...
			'label_count'               => _n_noop( __( 'Pending Redsys Bank Transfer <span class="count">(%s)</span>', 'woocommerce-redsys' ), __( 'Pending Redsys Bank Transfer <span class="count">(%s)</span>', 'woocommerce-redsys' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		)
	);
}
add_action( 'init', 'redsys_register_pending_bank_transfer_payment_status' );

/**
 * Copyright: (C) 2013 - 2024 José Conti
 */
function redsys_register_pending_payment_status() {

	register_post_status(
		'wc-redsys-wait',
		array(
			'label'                     => 'Pending Redsys Payment',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true, // show count All (12) , Completed (9) , Awaiting shipment (2) ...
			'label_count'               => _n_noop( __( 'Pending Redsys Payment <span class="count">(%s)</span>', 'woocommerce-redsys' ), __( 'Pending Redsys Payment <span class="count">(%s)</span>', 'woocommerce-redsys' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		)
	);
}
add_action( 'init', 'redsys_register_pending_payment_status' );

/**
 * Add registered status to list of WC Order statuses
 *
 * @param array $wc_statuses_arr Array of all order statuses on the website.
 */
function redsys_add_preauthorized_status( $wc_statuses_arr ) {

	$new_statuses_arr = array();

	// add new order status after processing.
	foreach ( $wc_statuses_arr as $id => $label ) {
		$new_statuses_arr[ $id ] = $label;

		if ( 'wc-processing' === $id ) { // after "Completed" status.
			$new_statuses_arr['wc-redsys-pre'] = __( 'Preauthorized', 'woocommerce-redsys' );
		}
	}
	return $new_statuses_arr;
}
add_filter( 'wc_order_statuses', 'redsys_add_preauthorized_status' );

/**
 * Add registered status to list of WC Order statuses
 *
 * @param array $wc_statuses_arr Array of all order statuses on the website.
 */
function redsys_add_resident_payment_status( $wc_statuses_arr ) {

	$new_statuses_arr = array();

	// add new order status after processing.
	foreach ( $wc_statuses_arr as $id => $label ) {
		$new_statuses_arr[ $id ] = $label;

		if ( 'wc-processing' === $id ) { // after "Completed" status.
			$new_statuses_arr['wc-redsys-residentp'] = __( 'Resident Payment', 'woocommerce-redsys' );
		}
	}
	return $new_statuses_arr;
}
add_filter( 'wc_order_statuses', 'redsys_add_resident_payment_status' );

/**
 * Add registered status to list of WC Order statuses
 *
 * @param array $wc_statuses_arr Array of all order statuses on the website.
 */
function redsys_add_pending_bank_transfer_payment_status( $wc_statuses_arr ) {

	$new_statuses_arr = array();

	// add new order status after processing.
	foreach ( $wc_statuses_arr as $id => $label ) {
		$new_statuses_arr[ $id ] = $label;

		if ( 'wc-processing' === $id ) { // after "Completed" status.
			$new_statuses_arr['wc-redsys-pbankt'] = __( 'Pending Redsys Bank Transfer', 'woocommerce-redsys' );
		}
	}
	return $new_statuses_arr;
}
add_filter( 'wc_order_statuses', 'redsys_add_pending_bank_transfer_payment_status' );

/**
 * Add registered status to list of WC Order statuses
 *
 * @param array $wc_statuses_arr Array of all order statuses on the website.
 */
function redsys_add_pending_payment_status( $wc_statuses_arr ) {

	$new_statuses_arr = array();

	// add new order status after processing.
	foreach ( $wc_statuses_arr as $id => $label ) {
		$new_statuses_arr[ $id ] = $label;

		if ( 'wc-processing' === $id ) { // after "Completed" status.
			$new_statuses_arr['wc-redsys-wait'] = __( 'Pending Redsys Payment', 'woocommerce-redsys' );
		}
	}
	return $new_statuses_arr;
}
add_filter( 'wc_order_statuses', 'redsys_add_pending_payment_status' );

/**
 * Add registered status to list of WC Order statuses
 *
 * @param array $statuses Array of all order statuses on the website.
 */
function redsys_woocommerce_valid_order_statuses_for_payment_complete( $statuses ) {
	$statuses[] = 'redsys-wait';
	return $statuses;
}
add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', 'redsys_woocommerce_valid_order_statuses_for_payment_complete', 10, 2 );
