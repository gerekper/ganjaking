<?php
if( !defined('ABSPATH')){
	exit;
}

$privacy_settings = array(
	'privacy-settings' => array(

		'privacy_settings_start' => array(

			'name' => __( 'Privacy Settings', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),

		'privacy_enable_user_privacy' => array(
			'name' => __( 'GDPR compliance', 'yith-woocommerce-delivery-date' ),
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'id' => 'ywcdd_user_privacy',
			'desc' => __( 'If checked, you will allow customers to choose whether they want to receive an email for the concerned order' ,'yith-woocommerce-delivery-date'),
			'default' => 'no'
		),

		'privacy_text_user_privacy' => array(
			'name' => __( 'Description', 'yith-woocommerce-delivery-date'),
			'type' => 'yith-field',
			'yith-type' => 'text',
			'id' =>  'ywcdd_user_privacy_description',
			'desc' => __( 'This description will be displayed next to the checkbox', 'yith-woocommerce-delivery-date'),
			'deps' => array(
				'id' => 'ywcdd_user_privacy',
				'value' => 'yes',
				'type' => 'disable'
			),
			'default' => __( 'Yes, I want to receive emails when my order is shipped', 'yith-woocommerce-delivery-date')
		),
		'privacy_settings_end'   => array(
			'type' => 'sectionend'
		)
	)
);

return $privacy_settings;