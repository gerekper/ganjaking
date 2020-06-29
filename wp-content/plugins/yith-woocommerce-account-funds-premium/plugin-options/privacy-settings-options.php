<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy_settings = array(
	'privacy-settings' => array(

		'privacy_settings_start' => array(

			'name' => __( 'Privacy Settings', 'yith-woocommerce-account-funds' ),
			'type' => 'title'
		),

		'privacy_enable_user_privacy' => array(
			'name' => __( 'GDPR compliance', 'yith-woocommerce-account-funds' ),
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'id' => 'ywf_user_privacy',
			'desc' => __( 'If checked, it allows customers to choose whether to receive emails or not about their funds. This option is visible in \'My Account\' page' ,'yith-woocommerce-account-funds'),
			'default' => 'no'
		),

		'privacy_text_user_privacy' => array(
			'name' => __( 'Description', 'yith-woocommerce-account-funds'),
			'type' => 'yith-field',
			'yith-type' => 'text',
			'id' =>  'ywf_user_privacy_description',
			'desc' => __( 'This description will be displayed next to the checkbox', 'yith-woocommerce-account-funds'),
			'deps' => array(
				'id' => 'ywf_user_privacy',
				'value' => 'yes',
				'type' => 'disable'
			),
			'default' => __( 'Yes, I want to receive emails for my funds', 'yith-woocommerce-account-funds')
		),
		'privacy_settings_end'   => array(
			'type' => 'sectionend'
		)
	)
);

return $privacy_settings;