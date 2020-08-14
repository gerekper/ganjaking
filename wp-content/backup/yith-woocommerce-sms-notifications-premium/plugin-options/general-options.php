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
	exit;
} // Exit if accessed directly

$log_file_link              = '';
$vendors_can_choose_status  = '';
$vendors_active_sms         = '';
$vendors_active_sms_booking = '';
$vendors_save_note          = '';
$active_send_booking        = '';

if ( defined( 'WC_LOG_HANDLER' ) && 'WC_Log_Handler_DB' === WC_LOG_HANDLER ) {

	$log_file_link = sprintf( '<a href="%s" target="_blank">%s</a>', admin_url( 'admin.php?page=wc-status&tab=logs&source=ywsn' ), esc_html__( 'View Logs', 'yith-woocommerce-sms-notifications' ) );

} else {
	$logs     = WC_Admin_Status::scan_log_files();
	$log_file = '';

	//Check if exists a log file for current month
	foreach ( $logs as $key => $value ) {
		if ( strpos( $value, 'ywsn-' . current_time( 'Y-m-d' ) ) !== false ) {
			$log_file = $key;
		}
	}

	if ( '' === $log_file ) {

		//If not found check if exists a log file for previous month
		foreach ( $logs as $key => $value ) {
			if ( false !== strpos( $value, 'ywsn-' . gmdate( 'Y-m-d', strtotime( '-1 day' ) ) ) ) {
				$log_file = $key; // print key containing searched string
			}
		}
	}

	if ( '' !== $log_file ) {

		$log_file_link = sprintf( '<a href="%s" target="_blank">%s - %s</a>', admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . $log_file ), esc_html__( 'View Log File', 'yith-woocommerce-sms-notifications' ), $log_file );

	}
}

$sms_providers   = include_once( YWSN_DIR . 'plugin-options/providers.php' );
$services_list   = array( 'YWSN_Void_Sender' => esc_html__( 'None', 'yith-woocommerce-sms-notifications' ) );
$services_option = array();

foreach ( $sms_providers as $class => $provider ) {
	$services_list[ $class ] = $provider['name'];
	$services_option         = array_merge( $services_option, $provider['options'] );
}

$sms_service = array(
	'ywsn_sms_service_title' => array(
		'name' => esc_html__( 'SMS Service settings', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
		'desc' => $log_file_link,
		'id'   => 'ywsn_sms_service_title',
	),
	'ywsn_sms_gateway'       => array(
		'name'      => esc_html__( 'SMS service enabled', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'id'        => 'ywsn_sms_gateway',
		'options'   => $services_list,
		'default'   => 'YWSN_Void_Sender',
	),
	'ywsn_sms_service_end'   => array(
		'type' => 'sectionend',
	),
);

if ( ywsn_is_multivendor_active() && get_option( 'yith_wpv_vendors_enable_sms' ) === 'yes' ) {
	$vendors_can_choose_status = array(
		'name'      => esc_html__( 'Let the vendors choose which order statuses they want to be informed about via an SMS text.', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywsn_vendors_can_choose_status',
		'default'   => 'yes',
	);
	$vendors_save_note         = array(
		'name'      => esc_html__( 'Save messages sent to vendors in main order notes', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywsn_save_vendor_note',
		'default'   => 'no',
	);
	$vendors_active_sms        = array(
		'name'        => esc_html__( 'Vendors will receive SMS notifications for the following changes to the order status.', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'yith-wc-check-matrix-table',
		'id'          => 'ywsn_vendor_active_sms',
		'main_column' => array(
			'label' => esc_html__( 'Order status', 'yith-woocommerce-sms-notifications' ),
			'rows'  => wc_get_order_statuses(),
		),
		'columns'     => array(
			array(
				'id'    => 'admin',
				'label' => esc_html__( 'Receive SMS', 'yith-woocommerce-sms-notifications' ),
				'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
			),
		),
		'deps'        => array(
			'id'    => 'ywsn_vendors_can_choose_status',
			'value' => 'no',
			'type'  => 'hide-disable',
		),
	);
	if ( ywsn_is_booking_active() ) {
		$vendors_active_sms_booking = array(
			'name'        => esc_html__( 'Vendors will receive SMS notifications for the following changes to the booking status.', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-field',
			'yith-type'   => 'yith-wc-check-matrix-table',
			'id'          => 'ywsn_vendor_active_sms_booking',
			'main_column' => array(
				'label' => esc_html__( 'Booking status', 'yith-woocommerce-sms-notifications' ),
				'rows'  => yith_wcbk_get_booking_statuses( true ),
			),
			'columns'     => array(
				array(
					'id'    => 'admin',
					'label' => esc_html__( 'Receive SMS', 'yith-woocommerce-sms-notifications' ),
					'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
				),
			),
			'deps'        => array(
				'id'    => 'ywsn_vendors_can_choose_status',
				'value' => 'no',
				'type'  => 'hide-disable',
			),
		);
	}
}

if ( ywsn_is_booking_active() ) {
	$active_send_booking = array(
		'name'        => esc_html__( 'SMS notifications for the following booking status changes', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'yith-wc-check-matrix-table',
		'id'          => 'ywsn_sms_active_send_booking',
		'main_column' => array(
			'label' => esc_html__( 'Booking status', 'yith-woocommerce-sms-notifications' ),
			'rows'  => yith_wcbk_get_booking_statuses( true ),
		),
		'columns'     => array(
			array(
				'id'    => 'customer',
				'label' => esc_html__( 'Customer', 'yith-woocommerce-sms-notifications' ),
				'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
			),
			array(
				'id'    => 'admin',
				'label' => esc_html__( 'Admin', 'yith-woocommerce-sms-notifications' ),
				'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
			),
		),
		'default'     => '',
		'value'       => '',
	);
}

$send_section = array(
	'ywsn_send_section_title'        => array(
		'name' => esc_html__( 'Sending settings', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	),
	'ywsn_from_number'               => array(
		'name'              => esc_html__( 'Sender telephone number', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'id'                => 'ywsn_from_number',
		'desc'              => esc_html__( 'Enter the telephone number that should appear as sender', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => implode(
			' ',
			array(
				'required',
				'maxlength=16',
			)
		),
	),
	'ywsn_from_asid'                 => array(
		'name'              => esc_html__( 'Alphanumeric Sender ID', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'id'                => 'ywsn_from_asid',
		'desc'              => esc_html__( 'Alphanumeric sender identifier: enter the text that should appear as sender (this option might not work correctly in some countries, check your country with your SMS service provider you have selected)', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => 'maxlength=11',
	),
	'ywsn_admin_phone'               => array(
		'name'        => esc_html__( 'Admin phone', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'yith-wc-custom-checklist',
		'id'          => 'ywsn_admin_phone',
		'css'         => 'width: 50%;',
		'desc'        => esc_html__( 'Enter here the phone numbers of the admins who will be notified via SMS. Include country calling codes. You can also specify more than one phone number. Type the number and press Enter to add a new one.', 'yith-woocommerce-sms-notifications' ),
		'placeholder' => esc_html__( 'Type a phone number&hellip;', 'yith-woocommerce-sms-notifications' ),
	),
	'ywsn_customer_notification'     => array(
		'name'      => esc_html__( 'Send SMS notifications to customers', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'id'        => 'ywsn_customer_notification',
		'options'   => array(
			'automatic' => esc_html__( 'All customers', 'yith-woocommerce-sms-notifications' ),
			'requested' => esc_html__( 'Only customers who ask for it in checkout', 'yith-woocommerce-sms-notifications' ),
		),
		'class'     => 'ywsn-checkout-option',
		'default'   => 'automatic',
	),
	'ywsn_checkout_checkbox_value'   => array(
		'name'        => esc_html__( 'Selected', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'onoff',
		'id'          => 'ywsn_checkout_checkbox_value',
		'default'     => 'no',
		'desc-inline' => esc_html__( 'Show checkbox selected by default', 'yith-woocommerce-sms-notifications' ),
		'deps'        => array(
			'id'    => 'ywsn_customer_notification',
			'value' => 'requested',
			'type'  => 'disable',
		),
	),
	'ywsn_checkout_checkbox_text'    => array(
		'name'      => esc_html__( 'Checkbox text', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'textarea',
		'id'        => 'ywsn_checkout_checkbox_text',
		'default'   => esc_html__( 'I want to be notified about any changes in the order via SMS', 'yith-woocommerce-sms-notifications' ),
		'deps'      => array(
			'id'    => 'ywsn_customer_notification',
			'value' => 'requested',
			'type'  => 'disable',
		),
	),
	'ywsn_sms_active_send'           => array(
		'name'        => esc_html__( 'SMS notifications for the following order status changes', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'yith-wc-check-matrix-table',
		'id'          => 'ywsn_sms_active_send',
		'main_column' => array(
			'label' => esc_html__( 'Order status', 'yith-woocommerce-sms-notifications' ),
			'rows'  => wc_get_order_statuses(),
		),
		'columns'     => array(
			array(
				'id'    => 'customer',
				'label' => esc_html__( 'Customer', 'yith-woocommerce-sms-notifications' ),
				'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
			),
			array(
				'id'    => 'admin',
				'label' => esc_html__( 'Admin', 'yith-woocommerce-sms-notifications' ),
				'tip'   => esc_html__( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
			),
		),
		'default'     => '',
		'value'       => '',

	),
	'ywsn_sms_active_send_booking'   => $active_send_booking,
	'ywsn_vendors_can_choose_status' => $vendors_can_choose_status,
	'ywsn_vendor_active_sms'         => $vendors_active_sms,
	'ywsn_vendor_active_sms_booking' => $vendors_active_sms_booking,
	'ywsn_save_vendor_note'          => $vendors_save_note,
	'ywsn_send_section_end'          => array(
		'type' => 'sectionend',
	),
);

$url_section = array(
	'ywsn_url_shortening_title' => array(
		'name' => esc_html__( 'URL shortening settings', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	),
	'ywsn_url_shortening'       => array(
		'name'      => esc_html__( 'URL shortening service', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'id'        => 'ywsn_url_shortening',
		'options'   => apply_filters(
			'ywsn_url_shortening_services',
			array(
				'none'  => esc_html__( 'None', 'yith-woocommerce-sms-notifications' ),
				'bitly' => esc_html__( 'bitly', 'yith-woocommerce-sms-notifications' ),
			)
		),
		'default'   => 'none',
	),
	'ywsn_bitly_access_token'   => array(
		'name'              => esc_html__( 'Bitly Access Token', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'id'                => 'ywsn_bitly_access_token',
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywsn_url_shortening',
			'value' => 'bitly',
			'type'  => 'hide-disable',
		),
	),
	'ywsn_url_shortening_end'   => array(
		'type' => 'sectionend',
	),
);

$charset_section = array(
	'ywsn_charsets_title'    => array(
		'name' => esc_html__( 'Charsets & SMS Length', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	),
	'ywsn_active_charsets'   => array(
		'name'      => esc_html__( 'Available Charsets', 'yith-woocommerce-sms-notifications' ),
		'desc'      => esc_html__( 'Select extended charsets if you need them. Please note that the default SMS length will be reduced to 70 characters', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'id'        => 'ywsn_active_charsets',
		'multiple'  => true,
		'options'   => array(
			'cjk'      => esc_html__( 'CJK - Chinese Japanese Korean', 'yith-woocommerce-sms-notifications' ),
			'greek'    => esc_html__( 'Greek', 'yith-woocommerce-sms-notifications' ),
			'cyrillic' => esc_html__( 'Cyrillic', 'yith-woocommerce-sms-notifications' ),
			'armenian' => esc_html__( 'Armenian', 'yith-woocommerce-sms-notifications' ),
			'hebrew'   => esc_html__( 'Hebrew', 'yith-woocommerce-sms-notifications' ),
			'arabic'   => esc_html__( 'Arabic', 'yith-woocommerce-sms-notifications' ),
			'hangul'   => esc_html__( 'Hangul', 'yith-woocommerce-sms-notifications' ),
			'thai'     => esc_html__( 'Thai', 'yith-woocommerce-sms-notifications' ),
			'latinext' => esc_html__( 'Latin Extended', 'yith-woocommerce-sms-notifications' ),
		),
		'default'   => '',
	),
	'ywsn_enable_sms_length' => array(
		'name'      => esc_html__( 'Enable SMS length modification', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywsn_enable_sms_length',
		'default'   => 'no',
	),
	'ywsn_sms_length'        => array(
		'name'              => esc_html__( 'SMS Length', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'id'                => 'ywsn_sms_length',
		'desc'              => esc_html__( 'Enter the maximum SMS length.', 'yith-woocommerce-sms-notifications' ),
		'max'               => 999,
		'custom_attributes' => 'required',
		'deps'              => array(
			'id'    => 'ywsn_enable_sms_length',
			'value' => 'yes',
			'type'  => 'disable',
		),
		'default'           => 160,
	),
	'ywsn_charsets_end'      => array(
		'type' => 'sectionend',
	),
);

return array(
	'general' => array_merge( $sms_service, $services_option, $send_section, $url_section, $charset_section ),
);
