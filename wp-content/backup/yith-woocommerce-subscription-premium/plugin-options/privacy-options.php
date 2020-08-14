<?php

$settings = array(

	'privacy' => array(

		'privacy_settings'                       => array(
			'name' => __( 'Privacy settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_privacy_settings',
		),

		'erasure_request'                        => array(
			'name'        => __( 'Account erasure requests', 'yith-woocommerce-subscription' ),
			'desc-inline' => __( 'Remove personal data from subscriptions', 'yith-woocommerce-subscription' ),
			'desc'        => sprintf(
				__(
					'When handling an <a href="%s">account erasure request</a>, should personal data within subscriptions be retained or removed?.
<br>Note: All the subscriptions will change the status to cancelled if the personal data will be removed.',
					'yith-woocommerce-subscription'
				),
				esc_url( admin_url( 'tools.php?page=remove_personal_data' ) )
			),
			'id'          => 'ywsbs_erasure_request',
			'type'        => 'yith-field',
			'yith-type'   => 'checkbox',
			'default'     => 'no',
		),

		'section_end_privacy_settings'           => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_end_privacy_settings',
		),

		array(
			'title' => __( 'Personal data retention', 'yith-woocommerce-subscription' ),
			'desc'  => __( 'Choose how long to retain personal data when it\'s no longer needed for processing. Leave the following options blank to retain this data indefinitely.', 'yith-woocommerce-subscription' ),
			'type'  => 'title',
			'id'    => 'ywsbs_personal_data_retention',
		),
		array(
			'title'       => __( 'Retain pending subscriptions', 'yith-woocommerce-subscription' ),
			'desc_tip'    => __( 'Pending subscriptions are unpaid and may have been abandoned by the customer. They will be trashed after the specified duration.', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_trash_pending_subscriptions',
			'type'        => 'relative_date_selector',
			'placeholder' => __( 'N/A', 'yith-woocommerce-subscription' ),
			'default'     => '',
		),
		array(
			'title'       => __( 'Retain cancelled subscriptions', 'yith-woocommerce-subscription' ),
			'desc_tip'    => __( 'Cancelled subscriptions are disable subscriptions that can\'t be reactivated by the customer. They will be trashed after the specified duration.', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_trash_cancelled_subscriptions',
			'type'        => 'relative_date_selector',
			'placeholder' => __( 'N/A', 'yith-woocommerce-subscription' ),
			'default'     => '',
		),

		'section_end_privacy_retention_settings' => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_end_privacy_retention_settings',
		),


	),

);

return apply_filters( 'yith_ywsbs_panel_privacy_settings_options', $settings );
