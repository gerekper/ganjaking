<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy_settings = array(
	'privacy-settings' => array(

		'privacy_settings_start' => array(

			'name' => __( 'Privacy Settings', 'yith-woocommerce-pending-order-survey' ),
			'type' => 'title'
		),

		'privacy_enable_user_privacy' => array(
			'name' => __( 'GDPR compliance', 'yith-woocommerce-pending-order-survey' ),
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'id' => 'ywcpos_user_privacy',
			'desc' => __( 'If checked, you will allow customers to choose whether they want to receive a email for the concerned order' ,'yith-woocommerce-pending-order-survey'),
			'default' => 'no'
		),

		'privacy_text_user_privacy' => array(
			'name' => __( 'Description', 'yith-woocommerce-pending-order-survey'),
			'type' => 'yith-field',
			'yith-type' => 'text',
			'id' =>  'ywcpos_user_privacy_description',
			'desc' => __( 'This description will be displayed next to the checkbox', 'yith-woocommerce-pending-order-survey'),
			'deps' => array(
				'id' => 'ywcpos_user_privacy',
				'value' => 'yes',
				'type' => 'disable'
			),
			'default' => __( 'Yes, I want to receive emails for this order', 'yith-woocommerce-pending-order-survey')
		),
		'privacy_settings_end'   => array(
			'type' => 'sectionend'
		)
	)
);

return $privacy_settings;