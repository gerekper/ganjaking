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

$vendor                     = yith_get_vendor( 'current', 'user' );
$vendor_choose_status       = '';
$vendors_active_sms_booking = '';

if ( get_option( 'ywsn_vendors_can_choose_status' ) === 'yes' ) {
	$vendor_choose_status = array(
		'name'        => esc_html__( 'SMS notifications for the following order status changes', 'yith-woocommerce-sms-notifications' ),
		'type'        => 'yith-field',
		'yith-type'   => 'yith-wc-check-matrix-table',
		'id'          => 'ywsn_sms_active_send_vendor_' . $vendor->id,
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
	);

	if ( ywsn_is_booking_active() ) {
		$vendors_active_sms_booking = array(
			'name'        => esc_html__( 'SMS notifications for the following booking status changes', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-field',
			'yith-type'   => 'yith-wc-check-matrix-table',
			'id'          => 'ywsn_sms_active_send_booking_vendor_' . $vendor->id,
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
		);
	}
}


return array(
	'vendor' => array(

		'ywsn_send_section_title'      => array(
			'name' => esc_html__( 'Sending settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_admin_phone'             => array(
			'name'        => esc_html__( 'Admin phone', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-field',
			'yith-type'   => 'yith-wc-custom-checklist',
			'id'          => 'ywsn_admin_phone_vendor_' . $vendor->id,
			'css'         => 'width: 50%;',
			'desc'        => esc_html__( 'Enter here the phone numbers of the admins who will be notified via SMS. Include country calling codes. You can also specify more than one phone number. Type the number and press Enter to add a new one.', 'yith-woocommerce-sms-notifications' ),
			'placeholder' => esc_html__( 'Type a phone number&hellip;', 'yith-woocommerce-sms-notifications' ),
		),
		'ywsn_sms_active_send'         => $vendor_choose_status,
		'ywsn_sms_active_send_booking' => $vendors_active_sms_booking,
		'ywsn_send_section_end'        => array(
			'type' => 'sectionend',
		),
	),
);
