<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subject_mail = sprintf( '{site_title} %s', __( 'Your order has been shipped!', 'yith-woocommerce-delivery-date' ) );
$mail_content = sprintf( '%s {customer_name}' . "\n\n" . '%s {order_id} %s.' . "\n\n" . '%s {delivery_date}.' . "\n\n" . '%s {site_title}',
	__( 'Dear', 'yith-woocommerce-delivery-date' ),
	__( 'the order', 'yith-woocommerce-delivery-date' ),
	__( 'has been shipped to carrier', 'yith-woocommerce-delivery-date' ),
	__( 'You should receive your order within', 'yith-woocommerce-delivery-date' ),
	__( 'Best regards', 'yith-woocommerce-delivery-date' )
);

$desc = sprintf( '%s<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
	__( 'You can use the following placeholders', 'yith-woocommerce-delivery-date' ),
	__( '{site_title} replaced by website title', 'yith-woocommerce-delivery-date' ),
	__( '{order_url} replaced by order link', 'yith-woocommerce-delivery-date' ),
	__( '{order_id} replaced by order ID', 'yith-woocommerce-delivery-date' ),
	__( '{order_content} replaced by order content', 'yith-woocommerce-delivery-date' ),
	__( '{customer_email} replaced by customer\'s email address', 'yith-woocommerce-delivery-date' ),
	__( '{customer_name} replaced by customer\'s name', 'yith-woocommerce-delivery-date' ),
	__( '{delivery_date} replaced by delivery date', 'yith-woocommerce-delivery-date' ),
	__( '{delivery_time} replaced by delivery time slot', 'yith-woocommerce-delivery-date' )

);

$mail = array(

	'email-settings' => array(

		'privacy_settings_start' => array(

			'name' => __( 'Privacy Settings', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),

		'privacy_enable_user_privacy' => array(
			'name'      => __( 'GDPR compliance', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywcdd_user_privacy',
			'desc'      => __( 'If checked, you will allow customers to choose whether they want to receive an email for the concerned order', 'yith-woocommerce-delivery-date' ),
			'default'   => 'no'
		),

		'privacy_text_user_privacy' => array(
			'name'      => __( 'Description', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywcdd_user_privacy_description',
			'desc'      => __( 'This description will be displayed next to the checkbox', 'yith-woocommerce-delivery-date' ),
			'deps'      => array(
				'id'    => 'ywcdd_user_privacy',
				'value' => 'yes',
				'type'  => 'disable'
			),
			'default'   => __( 'Yes, I want to receive emails when my order is shipped', 'yith-woocommerce-delivery-date' )
		),
		'privacy_settings_end'      => array(
			'type' => 'sectionend'
		),

		'mail_section_start' => array(
			'name' => __( 'Shipped to carrier - email settings', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),

		'mail_enabled' => array(
			'name'      => __( 'Enable/Disable', 'yith-woocommerce-delivery-date' ),
			'id'        => 'ywcdd_mail_enabled',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes'
		),

		'mail_type' => array(
			'name'      => __( 'Email type', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'html'  => __( 'HTML', 'yith-woocommerce-delivery-date' ),
				'plain' => __( 'Plain text', 'yith-woocommerce-delivery-date' )
			),
			'std'       => 'html',
			'default'   => 'html',
			'id'        => 'ywcdd_mail_type',
			'deps'      => array(
				'id'    => 'ywcdd_mail_enabled',
				'value' => 'yes',
				'type'  => 'disable'
			),

		),


		'mail_subject'     => array(
			'name'      => __( 'Email subject', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => $desc,
			'id'        => 'ywcdd_mail_subject',
			'std'       => $subject_mail,
			'default'   => $subject_mail,
			'css'       => 'width:400px',
			'deps'      => array(
				'id'    => 'ywcdd_mail_enabled',
				'value' => 'yes',
				'type'  => 'disabled'
			),
		),
		'mail_sender_name' => array(
			'name'      => __( 'Email sender', 'yith-woocommerce-delivery-date' ),
			'desc'      => '',
			'id'        => 'ywcdd_user_sender_name',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => get_bloginfo( 'name' ),
			'deps'      => array(
				'id'    => 'ywcdd_mail_enabled',
				'value' => 'yes',
				'type'  => 'disable'
			),
		),

		'mail_content' => array(
			'name'      => __( 'Email content', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywcdd_mail_content',
			'desc'      => $desc,
			'default'   => $mail_content,
			'css'       => 'width:100%; height:300px; resize:none;',
			'deps'      => array(
				'id'    => 'ywcdd_mail_enabled',
				'value' => 'yes',
				'type'  => 'disable'
			),
		),

		'mail_section_end' => array(
			'type' => 'sectionend',
		),
	)

);

return apply_filters( 'yith_delivery_date_mail_settings', $mail );
