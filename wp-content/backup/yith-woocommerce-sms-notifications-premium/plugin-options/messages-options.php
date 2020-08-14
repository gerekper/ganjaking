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

$part1        = esc_html__( 'In this page you can configure all SMS text messages that customers will receive when any changes to the status of their order is applied.', 'yith-woocommerce-sms-notifications' );
$part2        = esc_html__( 'Allowed placeholders', 'yith-woocommerce-sms-notifications' );
$part3        = esc_html__( 'When you add placeholders, keep in mind that there\'s a 160 characters limit for each message.', 'yith-woocommerce-sms-notifications' );
$placeholders = implode( ', ', array_keys( ywsn_placeholder_reference() ) );
$description  = sprintf( '%1$s <br /><br /> %2$s:<code>%3$s</code><br /><br />%4$s', $part1, $part2, $placeholders, $part3 );

$test_block = array(
	'ywsn_test_section_title' => array(
		'name' => esc_html__( 'SMS Messages', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
		'desc' => $description,
		'id'   => 'ywsn_messages_section_title',
	),
	'ywsn_message_test'       => array(
		'name'      => esc_html__( 'Test message', 'yith-woocommerce-sms-notifications' ),
		'type'      => 'yith-field',
		'yith-type' => 'ywsn-sms-send',
	),
	'ywsn_test_section_end'   => array(
		'type' => 'sectionend',
	),
);

$order_messages = array(
	'ywsn_messages_section_title' => array(
		'name' => esc_html__( 'Order Status SMS', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
		'id'   => 'ywsn_messages_section_title',
	),
	'ywsn_message_admin'          => array(
		'name'              => esc_html__( 'Text message for admin(s)', 'yith-woocommerce-sms-notifications' ),
		'desc'              => esc_html__( 'This is the text message that admin(s) will receive any time the order status is changed', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'textarea',
		'id'                => 'ywsn_message_admin',
		'default'           => esc_html__( '{site_title}: Order #{order_id} switched to {order_status}.', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => 'required',
	),
	'ywsn_message_generic'        => array(
		'name'              => esc_html__( 'Default customer SMS', 'yith-woocommerce-sms-notifications' ),
		'desc'              => esc_html__( 'This is the default message that customers receive each time the status of the order changes and if no other message is specified', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-field',
		'yith-type'         => 'textarea',
		'id'                => 'ywsn_message_generic',
		'default'           => esc_html__( 'Your order #{order_id} on {site_title} is now {order_status}.', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => 'required',
	),
);

foreach ( wc_get_order_statuses() as $key => $label ) {
	$order_messages[ 'ywsn_message_' . $key ] = array(
		'name'      => $label,
		'type'      => 'yith-field',
		'yith-type' => 'textarea',
		'id'        => 'ywsn_message_' . $key,
		/* translators: %s status name */
		'default'   => sprintf( esc_html__( 'Your order #{order_id} on {site_title} is now %s.', 'yith-woocommerce-sms-notifications' ), $label ),
	);
}

$order_messages['ywsn_messages_section_end'] = array(
	'type' => 'sectionend',
);

if ( ywsn_is_booking_active() ) {
	$booking_messages = array(
		'ywsn_messages_booking_section_title' => array(
			'name' => esc_html__( 'Booking Status SMS', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
			'id'   => 'ywsn_messages_booking_section_title',
		),
		'ywsn_message_booking_admin'          => array(
			'name'              => esc_html__( 'Text message for admin(s)', 'yith-woocommerce-sms-notifications' ),
			'desc'              => esc_html__( 'This is the text message that admin(s) will receive every time the booking status changes', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'id'                => 'ywsn_message_booking_admin',
			'default'           => esc_html__( '{site_title}: Booking #{booking_id} switched to {booking_status}.', 'yith-woocommerce-sms-notifications' ),
			'custom_attributes' => 'required',
		),
		'ywsn_message_booking_generic'        => array(
			'name'              => esc_html__( 'Default customer SMS', 'yith-woocommerce-sms-notifications' ),
			'desc'              => esc_html__( 'This is the default message that customers receive each time the status of the booking changes and if no other message is specified', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'id'                => 'ywsn_message_booking_generic',
			'default'           => esc_html__( 'Your booking #{booking_id} on {site_title} is now {booking_status}.', 'yith-woocommerce-sms-notifications' ),
			'custom_attributes' => 'required',
		),
	);

	foreach ( yith_wcbk_get_booking_statuses( true ) as $key => $label ) {
		$booking_messages[ 'ywsn_message_booking_' . $key ] = array(
			'name'      => $label,
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywsn_message_booking_' . $key,
			/* translators: %s status name*/
			'default'   => sprintf( esc_html__( 'Your booking #{booking_id} on {site_title} is now %s.', 'yith-woocommerce-sms-notifications' ), $label ),
		);
	}

	$booking_messages['ywsn_messages_booking_section_end'] = array(
		'type' => 'sectionend',
	);
} else {
	$booking_messages = array();
}

$messages = array_merge( $test_block, $order_messages, $booking_messages );

return array(
	'messages' => $messages,
);
