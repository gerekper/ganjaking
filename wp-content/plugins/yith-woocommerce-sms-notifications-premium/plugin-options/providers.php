<?php

if ( ! function_exists( 'ywsn_set_gateway_site_info' ) ) {

	/**
	 * Set gateway site info
	 *
	 * @param   $name     string
	 * @param   $site_url string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_site_info( $name, $site_url ) {
		/* translators: %1$s operator name, %2$s gateway url */
		return sprintf( esc_html__( 'Create your %1$s account on %2$s', 'yith-woocommerce-sms-notifications' ), $name, '<a target="_blank" href="' . $site_url . '">' . $site_url . '</a>' );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_settings_label' ) ) {

	/**
	 * Set gateway settings label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_settings_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s settings', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_username_label' ) ) {

	/**
	 * Set gateway username label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_username_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s Username', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_password_label' ) ) {

	/**
	 * Set gateway password label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_password_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s Password', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_sender_label' ) ) {

	/**
	 * Set gateway sender label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_sender_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s Sender', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_api_label' ) ) {

	/**
	 * Set gateway API label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_api_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s API Key', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

if ( ! function_exists( 'ywsn_set_gateway_secret_label' ) ) {

	/**
	 * Set gateway API Secret label
	 *
	 * @param   $name string
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_set_gateway_secret_label( $name ) {
		/* translators: %s operator name */
		return sprintf( esc_html__( '%s API Secret', 'yith-woocommerce-sms-notifications' ), $name );
	}
}

return array(
	'YWSN_Agile_Telecom'   => array(
		'name'    => 'Agile Telecom',
		'options' => array(
			'ywsn_agile_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Agile Telecom' ),
				'type' => 'title',
				'id'   => 'ywsn_agile_telecom_title',
				'desc' => ywsn_set_gateway_site_info( 'Agile Telecom', 'http://www.agiletelecom.com' ),
			),
			'ywsn_agile_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'Agile Telecom' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_agile_user',
				'custom_attributes' => 'required',
			),
			'ywsn_agile_pwd'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Agile Telecom' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_agile_pwd',
				'custom_attributes' => 'required',
			),
			'ywsn_agile_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Bulk_SMS'        => array(
		'name'    => 'Bulk SMS',
		'options' => array(
			'ywsn_bulk_sms_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Bulk SMS' ),
				'type' => 'title',
				'id'   => 'ywsn_bulk_sms_title',
				'desc' => ywsn_set_gateway_site_info( 'Bulk Sms', 'https://www.bulksms.com' ),

			),
			'ywsn_bulk_sms_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'Bulk SMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulk_sms_user',
				'custom_attributes' => 'required',
			),
			'ywsn_bulk_sms_pass'  => array(
				'name'              => ywsn_set_gateway_password_label( 'Bulk SMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulk_sms_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_bulk_sms_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Bulk_SMS_SA'     => array(
		'name'    => 'Bulk SMS (Saudi Arabia)',
		'options' => array(
			'ywsn_bulk_sms_sa_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'Bulk SMS (Saudi Arabia)' ),
				'type' => 'title',
				'id'   => 'ywsn_bulk_sms_sa_title',
				'desc' => ywsn_set_gateway_site_info( 'Bulk Sms (Saudi Arabia)', 'https://www.bulksms-sa.info' ),

			),
			'ywsn_bulk_sms_sa_user'   => array(
				'name'              => ywsn_set_gateway_username_label( 'Bulk SMS (Saudi Arabia)' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulk_sms_sa_user',
				'custom_attributes' => 'required',
			),
			'ywsn_bulk_sms_sa_pass'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Bulk SMS (Saudi Arabia)' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulk_sms_sa_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_bulk_sms_sa_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'Bulk SMS (Saudi Arabia)' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulk_sms_sa_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_bulk_sms_sa_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_BulkSMS_Maroc'   => array(
		'name'    => 'BulkSMS Maroc',
		'options' => array(
			'ywsn_bulksms_maroc_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'BulkSMS Maroc' ),
				'type' => 'title',
				'id'   => 'ywsn_bulksms_maroc_title',
				'desc' => ywsn_set_gateway_site_info(
					'BulkSMS Maroc',
					'https://bulksms.ma/'
				),
			),
			'ywsn_bulksms_maroc_key'   => array(
				'name'              => ywsn_set_gateway_api_label( 'BulkSMS Maroc' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_bulksms_maroc_key',
				'custom_attributes' => 'required',
			),
			'ywsn_bulksms_maroc_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Burst_SMS'       => array(
		'name'    => 'Burst SMS',
		'options' => array(
			'ywsn_burst_sms_title'      => array(
				'name' => ywsn_set_gateway_settings_label( 'Burst SMS' ),
				'type' => 'title',
				'id'   => 'ywsn_burst_sms_title',
				'desc' => ywsn_set_gateway_site_info(
					'Burst SMS',
					'https://www.burstsms.com/'
				),
			),
			'ywsn_burst_sms_api_key'    => array(
				'name'              => ywsn_set_gateway_api_label( 'Burst SMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_burst_sms_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_burst_sms_api_secret' => array(
				'name'              => ywsn_set_gateway_secret_label( 'Burst SMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_burst_sms_api_secret',
				'custom_attributes' => 'required',
			),
			'ywsn_burst_sms_end'        => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_ClickSend'       => array(
		'name'    => 'ClickSend',
		'options' => array(
			'ywsn_clicksend_title'    => array(
				'name' => ywsn_set_gateway_settings_label( 'ClickSend' ),
				'type' => 'title',
				'id'   => 'ywsn_clicksend_title',
				'desc' => ywsn_set_gateway_site_info(
					'ClickSend',
					'https://www.clicksend.com/'
				),
			),
			'ywsn_clicksend_username' => array(
				'name'              => ywsn_set_gateway_username_label( 'ClickSend' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_clicksend_username',
				'custom_attributes' => 'required',
			),
			'ywsn_clicksend_api_key'  => array(
				'name'              => ywsn_set_gateway_api_label( 'ClickSend' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_clicksend_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_clicksend_end'      => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Clockwork'       => array(
		'name'    => 'Clockwork',
		'options' => array(
			'ywsn_clockwork_title'   => array(
				'name' => ywsn_set_gateway_settings_label( 'Clockwork' ),
				'type' => 'title',
				'id'   => 'ywsn_clockwork_title',
				'desc' => ywsn_set_gateway_site_info(
					'Clockwork',
					'https://www.clockworksms.com/'
				),
			),
			'ywsn_clockwork_api_key' => array(
				'name'              => ywsn_set_gateway_api_label( 'Clockwork' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_clockwork_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_clockwork_end'     => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Crystalwebtechs' => array(
		'name'    => 'Crystalwebtechs',
		'options' => array(
			'ywsn_crystalwebtechs_title'               => array(
				'name' => ywsn_set_gateway_settings_label( 'Crystalwebtechs' ),
				'type' => 'title',
				'id'   => 'ywsn_crystalwebtechs_title',
				'desc' => ywsn_set_gateway_site_info(
					'Crystalwebtechs',
					'http://www.crystalwebtechs.com'
				),
			),
			'ywsn_crystalwebtechs_user'                => array(
				'name'              => ywsn_set_gateway_username_label( 'Crystalwebtechs' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_crystalwebtechs_username',
				'custom_attributes' => 'required',
			),
			'ywsn_crystalwebtechs_pass'                => array(
				'name'              => ywsn_set_gateway_password_label( 'Crystalwebtechs' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_crystalwebtechs_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_crystalwebtechs_sender'              => array(
				'name'              => ywsn_set_gateway_sender_label( 'Crystalwebtechs' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_crystalwebtechs_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_crystalwebtechs_sender_channel_type' => array(
				'name'      => esc_html__( 'Crystalwebtechs Sender Channel Type', 'yith-woocommerce-sms-notifications' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'id'        => 'ywsn_crystalwebtechs_sender_channel_type',
				'default'   => 'Trans',
				'options'   => array(
					'Trans' => esc_html__( 'Transactional', 'yith-woocommerce-sms-notifications' ),
					'Promo' => esc_html__( 'Promotional', 'yith-woocommerce-sms-notifications' ),
				),
			),
			'ywsn_crystalwebtechs_route_id'            => array(
				'name'              => esc_html__( 'Crystalwebtechs Route ID', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_crystalwebtechs_route_id',
				'custom_attributes' => 'required',
			),
			'ywsn_crystalwebtechs_end'                 => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Green_Text'      => array(
		'name'    => 'Green Text',
		'options' => array(
			'ywsn_green_text_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Green Text' ),
				'type' => 'title',
				'id'   => 'ywsn_green_text_title',
				'desc' => ywsn_set_gateway_site_info(
					'Green Text',
					'http://www.gntext.com/'
				),
			),
			'ywsn_green_text_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'Green Text' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_green_text_user',
				'custom_attributes' => 'required',
			),
			'ywsn_green_text_pass'  => array(
				'name'              => ywsn_set_gateway_password_label( 'Green Text' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_green_text_pass',
				'css'               => 'width: 50%',
				'custom_attributes' => 'required',
			),
			'ywsn_green_text_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_IntelliSMS'      => array(
		'name'    => 'IntelliSMS',
		'options' => array(
			'ywsn_intellisms_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'IntelliSMS' ),
				'type' => 'title',
				'id'   => 'ywsn_intellisms_title',
				'desc' => ywsn_set_gateway_site_info(
					'IntelliSMS',
					'https://www.intellisms.co.uk/'
				),
			),
			'ywsn_intellisms_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'IntelliSMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_intellisms_user',
				'custom_attributes' => 'required',
			),
			'ywsn_intellisms_pass'  => array(
				'name'              => ywsn_set_gateway_password_label( 'IntelliSMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_intellisms_pass',
				'css'               => 'width: 50%',
				'custom_attributes' => 'required',
			),
			'ywsn_intellisms_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Jazz'            => array(
		'name'    => 'Jazz (Mobilink)',
		'options' => array(
			'ywsn_jazz_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Jazz' ),
				'type' => 'title',
				'id'   => 'ywsn_jazz_title',
				'desc' => ywsn_set_gateway_site_info(
					'Jazz',
					'https://www.jazz.com.pk/business/'
				),
			),
			'ywsn_jazz_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'Jazz CMS Portal' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_jazz_username',
				'custom_attributes' => 'required',
			),
			'ywsn_jazz_pwd'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Jazz CMS Portal' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_jazz_password',
				'custom_attributes' => 'required',
			),
			'ywsn_jazz_mask'  => array(
				'name'              => esc_html__( 'Jazz CMS Portal Mask', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_jazz_mask',
				'custom_attributes' => 'required',
			),
			'ywsn_jazz_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_MessageBird'     => array(
		'name'    => 'MessageBird',
		'options' => array(
			'ywsn_messagebird_title'   => array(
				'name' => ywsn_set_gateway_settings_label( 'MessageBird' ),
				'type' => 'title',
				'id'   => 'ywsn_messagebird_title',
				'desc' => ywsn_set_gateway_site_info(
					'MessageBird',
					'https://www.messagebird.com/'
				),
			),
			'ywsn_messagebird_api_key' => array(
				'name'              => ywsn_set_gateway_api_label( 'MessageBird' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_messagebird_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_messagebird_end'     => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_mNotify'         => array(
		'name'    => 'mNotify',
		'options' => array(
			'ywsn_mnotify_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'mNotify' ),
				'type' => 'title',
				'id'   => 'ywsn_mnotify_title',
				'desc' => ywsn_set_gateway_site_info(
					'mNotify',
					'https://www.mnotify.com/'
				),
			),
			'ywsn_mnotify_key'   => array(
				'name'              => ywsn_set_gateway_api_label( 'mNotify' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_mnotify_key',
				'custom_attributes' => 'required',
			),
			'ywsn_mnotify_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Mobily_WS'       => array(
		'name'    => 'Alfa Cell',
		'options' => array(
			'ywsn_mobily_ws_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'Alfa Cell' ),
				'type' => 'title',
				'id'   => 'ywsn_mobily_ws_title',
				'desc' => ywsn_set_gateway_site_info(
					'Alfa Cell',
					'https://www.alfa-cell.com/'
				),
			),
			'ywsn_mobily_ws_mobile' => array(
				'name'              => ywsn_set_gateway_username_label( 'Alfa Cell' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_mobily_ws_mobile',
				'custom_attributes' => 'required',
			),
			'ywsn_mobily_ws_pass'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Alfa Cell' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_mobily_ws_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_mobily_ws_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'Alfa Cell' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_mobily_ws_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_mobily_ws_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Msg91'           => array(
		'name'    => 'Msg91',
		'options' => array(
			'ywsn_msg91_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Msg91' ),
				'type' => 'title',
				'id'   => 'ywsn_msg91_title',
				'desc' => ywsn_set_gateway_site_info(
					'Msg91',
					'https://msg91.com/'
				),
			),
			'ywsn_msg91_key'   => array(
				'name'              => ywsn_set_gateway_api_label( 'Msg91' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_msg91_key',
				'custom_attributes' => 'required',
			),
			'ywsn_msg91_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Netpowers'       => array(
		'name'    => 'Netpowers',
		'options' => array(
			'ywsn_netpowers_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'Netpowers' ),
				'type' => 'title',
				'id'   => 'ywsn_netpowers_title',
				'desc' => ywsn_set_gateway_site_info( 'Netpowers', 'https://netpowers.net/' ),

			),
			'ywsn_netpowers_user'   => array(
				'name'              => ywsn_set_gateway_username_label( 'Netpowers' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_netpowers_user',
				'custom_attributes' => 'required',
			),
			'ywsn_netpowers_pass'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Netpowers' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_netpowers_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_netpowers_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'Netpowers' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_netpowers_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_netpowers_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Nexmo'           => array(
		'name'    => 'Nexmo',
		'options' => array(
			'ywsn_nexmo_title'      => array(
				'name' => ywsn_set_gateway_settings_label( 'Nexmo' ),
				'type' => 'title',
				'id'   => 'ywsn_nexmo_title',
				'desc' => ywsn_set_gateway_site_info(
					'Nexmo',
					'https://www.nexmo.com/'
				),
			),
			'ywsn_nexmo_api_key'    => array(
				'name'              => ywsn_set_gateway_api_label( 'Nexmo' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_nexmo_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_nexmo_api_secret' => array(
				'name'              => ywsn_set_gateway_secret_label( 'Nexmo' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_nexmo_api_secret',
				'custom_attributes' => 'required',
			),
			'ywsn_nexmo_end'        => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Twilio'          => array(
		'name'    => 'Twilio',
		'options' => array(
			'ywsn_twilio_title'      => array(
				'name' => ywsn_set_gateway_settings_label( 'Twilio' ),
				'type' => 'title',
				'id'   => 'ywsn_twilio_title',
				'desc' => ywsn_set_gateway_site_info(
					'Twilio',
					'https://www.twilio.com'
				),
			),
			'ywsn_twilio_sid'        => array(
				'name'              => esc_html__( 'Twilio Account SID', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_twilio_sid',
				'custom_attributes' => 'required',
			),
			'ywsn_twilio_auth_token' => array(
				'name'              => esc_html__( 'Twilio Auth Token', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_twilio_auth_token',
				'custom_attributes' => 'required',
			),
			'ywsn_twilio_end'        => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Sabeq_Alarabia'  => array(
		'name'    => 'Sabeq Alarabia',
		'options' => array(
			'ywsn_sabeq_alarabia_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'Sabeq Alarabia' ),
				'type' => 'title',
				'id'   => 'ywsn_sabeq_alarabia_title',
				'desc' => ywsn_set_gateway_site_info(
					'Sabeq Alarabia',
					'http://www.sabeq-alarabia.net'
				),
			),
			'ywsn_sabeq_alarabia_user'   => array(
				'name'              => ywsn_set_gateway_username_label( 'Sabeq Alarabia' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sabeq_alarabia_user',
				'custom_attributes' => 'required',
			),
			'ywsn_sabeq_alarabia_pass'   => array(
				'name'              => ywsn_set_gateway_password_label( 'Sabeq Alarabia' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sabeq_alarabia_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_sabeq_alarabia_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'Sabeq Alarabia' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sabeq_alarabia_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_sabeq_alarabia_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SMSAPI'          => array(
		'name'    => 'SMSAPI',
		'options' => array(
			'ywsn_smsapi_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'SMSAPI' ),
				'type' => 'title',
				'id'   => 'ywsn_smsapi_title',
				'desc' => ywsn_set_gateway_site_info(
					'SMSAPI',
					'https://www.smsapi.com/'
				),
			),
			'ywsn_smsapi_token'  => array(
				'name'              => esc_html__( 'SMSAPI Token', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smsapi_token',
				'custom_attributes' => 'required',
			),
			'ywsn_smsapi_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'SMSAPI' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smsapi_sender',
				'custom_attributes' => implode(
					' ',
					array(
						'required',
						'maxlength="11"',
					)
				),
			),
			'ywsn_smsapi_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SmsBroadcast'    => array(
		'name'    => 'SMS Broadcast',
		'options' => array(
			'ywsn_smsbroadcast_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'SMS Broadcast' ),
				'type' => 'title',
				'id'   => 'ywsn_smsbroadcast_title',
				'desc' => ywsn_set_gateway_site_info(
					'SMS Broadcast',
					'https://www.smsbroadcast.com.au/'
				),
			),
			'ywsn_smsbroadcast_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'SMS Broadcast' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smsbroadcast_user',
				'custom_attributes' => 'required',
			),
			'ywsn_smsbroadcast_pass'  => array(
				'name'              => ywsn_set_gateway_password_label( 'SMS Broadcast' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smsbroadcast_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_smsbroadcast_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Smshosting'      => array(
		'name'    => 'Smshosting',
		'options' => array(
			'ywsn_smshosting_title'      => array(
				'name' => ywsn_set_gateway_settings_label( 'Smshosting' ),
				'type' => 'title',
				'id'   => 'ywsn_smshosting_title',
				'desc' => ywsn_set_gateway_site_info(
					'Smshosting',
					'https://www.smshosting.it/'
				),
			),
			'ywsn_smshosting_authkey'    => array(
				'name'              => esc_html__( 'Smshosting AuthKey', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smshosting_authkey',
				'custom_attributes' => 'required',
			),
			'ywsn_smshosting_authsecret' => array(
				'name'              => esc_html__( 'Smshosting AuthSecret', 'yith-woocommerce-sms-notifications' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smshosting_authsecret',
				'custom_attributes' => 'required',
			),
			'ywsn_smshosting_end'        => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SMS_Country'     => array(
		'name'    => 'SMS Country',
		'options' => array(
			'ywsn_sms_country_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'SMS Country' ),
				'type' => 'title',
				'id'   => 'ywsn_sms_country_title',
				'desc' => ywsn_set_gateway_site_info(
					'SMS Country',
					'http://www.smscountry.com'
				),
			),
			'ywsn_sms_country_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'SMS Country' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sms_country_user',
				'custom_attributes' => 'required',
			),
			'ywsn_sms_country_pwd'   => array(
				'name'              => ywsn_set_gateway_password_label( 'SMS Country' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sms_country_pwd',
				'custom_attributes' => 'required',
			),
			'ywsn_sms_country_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SMS_Gateway_Hub' => array(
		'name'    => 'SMS Gateway Hub',
		'options' => array(
			'ywsn_sms_gateway_hub_title'        => array(
				'name' => ywsn_set_gateway_settings_label( 'SMS Gateway' ),
				'type' => 'title',
				'id'   => 'ywsn_sms_gateway_hub_title',
				'desc' => ywsn_set_gateway_site_info(
					'SMS Gateway Hub',
					'http://www.smsgatewayhub.com'
				),
			),
			'ywsn_sms_gateway_hub_api_key'      => array(
				'name'              => ywsn_set_gateway_api_label( 'SMS Gateway Hub' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sms_gateway_hub_api_key',
				'custom_attributes' => 'required',
			),
			'ywsn_sms_gateway_hub_sender'       => array(
				'name'              => ywsn_set_gateway_sender_label( 'SMS Gateway Hub' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sms_gateway_hub_sender',
				'custom_attributes' => implode(
					' ',
					array(
						'required',
						'maxlength="6"',
					)
				),
			),
			'ywsn_sms_gateway_hub_channel_type' => array(
				'name'      => esc_html__( 'SMS Gateway Hub Channel Type', 'yith-woocommerce-sms-notifications' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'id'        => 'ywsn_sms_gateway_hub_channel_type',
				'default'   => '2',
				'options'   => array(
					'1' => esc_html__( 'Promotional', 'yith-woocommerce-sms-notifications' ),
					'2' => esc_html__( 'Transactional', 'yith-woocommerce-sms-notifications' ),
				),
			),
			'ywsn_sms_gateway_hub_end'          => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SMS_Office'      => array(
		'name'    => 'SMS Office',
		'options' => array(
			'ywsn_sms_office_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'SMS Office' ),
				'type' => 'title',
				'id'   => 'ywsn_sms_office_title',
				'desc' => ywsn_set_gateway_site_info(
					'SMS Office',
					'http://smsoffice.ge/'
				),
			),
			'ywsn_sms_office_key'   => array(
				'name'              => ywsn_set_gateway_api_label( 'SMS Office' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_sms_office_key',
				'custom_attributes' => 'required',
			),
			'ywsn_sms_office_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_SmsCyber'        => array(
		'name'    => 'SmsCyber',
		'options' => array(
			'ywsn_smscyber_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'SmsCyber' ),
				'type' => 'title',
				'id'   => 'ywsn_smscyber_title',
				'desc' => ywsn_set_gateway_site_info(
					'SmsCyber',
					'http://bulk.smscyber.com'
				),
			),
			'ywsn_smscyber_user'  => array(
				'name'              => ywsn_set_gateway_username_label( 'SmsCyber' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smscyber_user',
				'custom_attributes' => 'required',
			),
			'ywsn_smscyber_api'   => array(
				'name'              => ywsn_set_gateway_password_label( 'SmsCyber API' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_smscyber_api',
				'custom_attributes' => 'required',
			),
			'ywsn_smscyber_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_ThaiBulkSMS'     => array(
		'name'    => 'ThaiBulkSMS',
		'options' => array(
			'ywsn_thaibulksms_title'    => array(
				'name' => ywsn_set_gateway_settings_label( 'ThaiBulkSMS' ),
				'type' => 'title',
				'id'   => 'ywsn_thaibulksms_title',
				'desc' => ywsn_set_gateway_site_info(
					'ThaiBulkSMS',
					'https://www.thaibulksms.com/'
				),
			),
			'ywsn_thaibulksms_user'     => array(
				'name'              => ywsn_set_gateway_username_label( 'ThaiBulkSMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_thaibulksms_user',
				'custom_attributes' => 'required',
			),
			'ywsn_thaibulksms_pass'     => array(
				'name'              => ywsn_set_gateway_password_label( 'ThaiBulkSMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_thaibulksms_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_thaibulksms_sender'   => array(
				'name'              => ywsn_set_gateway_sender_label( 'ThaiBulkSMS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_thaibulksms_sender',
				'custom_attributes' => implode(
					' ',
					array(
						'required',
						'maxlength="10"',
					)
				),
			),
			'ywsn_thaibulksms_sms_type' => array(
				'name'      => esc_html__( 'ThaiBulkSMS SMS Type', 'yith-woocommerce-sms-notifications' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'id'        => 'ywsn_thaibulksms_sms_type',
				'default'   => 'standard',
				'options'   => array(
					'standard' => esc_html__( 'Standard', 'yith-woocommerce-sms-notifications' ),
					'premium'  => esc_html__( 'Premium', 'yith-woocommerce-sms-notifications' ),
				),
			),
			'ywsn_thaibulksms_end'      => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Trend_Mens'      => array(
		'name'    => 'Trend MENS',
		'options' => array(
			'ywsn_trend_mens_title' => array(
				'name' => ywsn_set_gateway_settings_label( 'Trend MENS' ),
				'type' => 'title',
				'id'   => 'ywsn_trend_mens_title',
				'desc' => ywsn_set_gateway_site_info(
					'Trend MENS',
					'https://www.trendmens.com.br/'
				),
			),
			'ywsn_trend_mens_key'   => array(
				'name'              => ywsn_set_gateway_api_label( 'Trend MENS' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_trend_mens_key',
				'custom_attributes' => 'required',
			),
			'ywsn_trend_mens_end'   => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Uaedes'          => array(
		'name'    => 'UAEDes',
		'options' => array(
			'ywsn_uaedes_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'UAEDes' ),
				'type' => 'title',
				'id'   => 'ywsn_uaedes_title',
				'desc' => ywsn_set_gateway_site_info(
					'UAEDes',
					'http://www.uaedes.ae'
				),
			),
			'ywsn_uaedes_user'   => array(
				'name'              => ywsn_set_gateway_username_label( 'UAEDes' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_uaedes_user',
				'custom_attributes' => 'required',
			),
			'ywsn_uaedes_pass'   => array(
				'name'              => ywsn_set_gateway_password_label( 'UAEDes' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_uaedes_pass',
				'custom_attributes' => 'required',
			),
			'ywsn_uaedes_sender' => array(
				'name'              => ywsn_set_gateway_sender_label( 'UAEDes' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_uaedes_sender',
				'custom_attributes' => 'required',
			),
			'ywsn_uaedes_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
	'YWSN_Unifonic'        => array(
		'name'    => 'Unifonic',
		'options' => array(
			'ywsn_unifonic_title'  => array(
				'name' => ywsn_set_gateway_settings_label( 'Unifonic' ),
				'type' => 'title',
				'id'   => 'ywsn_unifonic_title',
				'desc' => ywsn_set_gateway_site_info(
					'Unifonic',
					'https://www.unifonic.com'
				),
			),
			'ywsn_unifonic_apikey' => array(
				'name'              => ywsn_set_gateway_api_label( 'Unifonic REST' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'id'                => 'ywsn_unifonic_apikey',
				'custom_attributes' => 'required',
			),
			'ywsn_unifonic_end'    => array(
				'type' => 'sectionend',
			),
		),
	),
);
