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

add_filter( 'woocommerce_privacy_remove_order_personal_data_prop_value', 'ywsn_remove_numbers_from_log', 10, 5 );

function ywsn_remove_numbers_from_log( $anon_value, $prop, $value, $data_type, WC_Order $order ) {

	if ( $prop == 'billing_phone' ) {

		$messages = new YWSN_Messages( $order );
		$phone    = $messages->get_formatted_number( $value, $order->get_billing_country() );

		if ( defined( 'WC_LOG_HANDLER' ) && 'WC_Log_Handler_DB' === WC_LOG_HANDLER ) {

			global $wpdb;

			$wpdb->query( "UPDATE {$wpdb->prefix}woocommerce_log SET message = replace(message, $phone, '************')" );

		} else {
			$logs = WC_Admin_Status::scan_log_files();

			foreach ( $logs as $log ) {
				if ( strpos( $log, 'ywsn-' ) !== false ) {
					$current_log   = file_get_contents( WC_LOG_DIR . $log );
					$file_contents = str_replace( $phone, '************', $current_log );
					file_put_contents( WC_LOG_DIR . $log, $file_contents );
				}
			}
		}

	}

	return $anon_value;

}