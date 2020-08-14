<?php
if(!defined('ABSPATH')){
	exit;
}
$subject_mail   =   sprintf( '{site_title} %s', __( 'You are running out of funds!', 'yith-woocommerce-account-funds' ) );
$mail_content   =   sprintf( '%s {customer_name},'."\n\n".'%s {site_title} (%s {user_funds}).'."\n\n".'%s {button_charging} %s.'."\n\n".'%s {site_title}',
	__( 'Dear', 'yith-woocommerce-account-funds' ),
	__( 'you are going to run out of funds on', 'yith-woocommerce-account-funds' ),
	__( 'your available funds:', 'yith-woocommerce-account-funds' ),
	__( 'Top up your account at', 'yith-woocommerce-account-funds'),
	__( 'to be able to make new orders quickly and forget about card numbers and codes.', 'yith-woocommerce-account-funds'),
	__( 'Best regards', 'yith-woocommerce-account-funds' )
);

$desc_tip   =   sprintf( '%s<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
	__('You can use the following placeholders', 'yith-woocommerce-account-funds'),
	__('<code>{site_title}</code> replaced by website title', 'yith-woocommerce-account-funds'),
	__('<code>{user_funds}</code> replaced by user\'s current balance', 'yith-woocommerce-account-funds'),
	__('<code>{customer_email}</code> replaced by customer\'s email address', 'yith-woocommerce-account-funds'),
	__('<code>{customer_name}</code> replaced by customer\'s name','yith-woocommerce-account-funds'),
	__('<code>{button_charging}</code> replaced by "Deposit now" button', 'yith-woocommerce-account-funds')

);


$settings            = array(
	'emails-multi-tab-deposit-email' => array(

		'mail_section_start'    =>  array(
			'name'  =>  __( 'Deposit funds', 'yith-woocommerce-account-funds' ),
			'type'  =>  'title',
			'id'    =>  'ywf_mail_section_start'
		),

		'mail_enabled'  => array(
			'name'  => __( 'Enable/Disable', 'yith-woocommerce-account-funds' ),
			'id'    => 'ywf_mail_enabled',
			'yith-type' => 'onoff',
			'type'  => 'yith-field',
			'default'   => 'yes',
			'desc' => __('If enabled, send an email to the customer once the balance is below the threshold', 'yith-woocommerce-account-funds')
		),
		'mail_amount_limit' => array(
			'name' => sprintf('%s (%s)',__('User funds threshold','yith-woocommerce-account-funds' ),get_woocommerce_currency_symbol() ),
			'type'    =>  'yith-field',
			'yith-type'    =>  'number',
			'min'       => 0,
			'step'      => 0.5,
			'default' => 0,
			'id' => 'ywf_email_limit',
			'desc' => __('Set a minimum threshold when an email is sent to customers to invite them to deposit more funds', 'yith-woocommerce-account-funds'),
		),
		'mail_subject'  =>  array(
			'name'    =>  __( 'Email subject', 'yith-woocommerce-account-funds' ),
			'type'    =>  'yith-field',
			'yith-type'    =>  'text',
			'desc'  =>  $desc_tip,
			'id'      =>  'ywf_mail_subject',
			'default' =>  $subject_mail,
		),
		'mail_sender_name' => array(
			'name' => __( 'Email heading', 'yith-woocommerce-account-funds' ),
			'id'   => 'ywf_user_heading',
			'type'    =>  'yith-field',
			'yith-type'    =>  'text',
			'default'  => __('Visit {site_title} to top up your funds', 'yith-woocommerce-account-funds'),
			'desc' => $desc_tip,
		),

		'mail_content'  =>  array(
			'name'    =>  __( 'Email content', 'yith-woocommerce-account-funds' ),
			'type'    =>  'yith-field',
			'yith-type'    =>  'textarea',
			'id'      =>  'ywf_mail_content',
			'desc'  =>  $desc_tip,
			'default'   =>  $mail_content,
			'css'   =>  'width:100%; height:300px; resize:none;'
		),


		'mail_type'   =>  array(
			'name'    =>  __('Email type', 'yith-woocommerce-account-funds'),
			'type'    =>  'yith-field',
			'yith-type'    =>  'select',
			'class' => 'email_type wc-enhanced-select',
			'options'    =>  array(
				'html'   =>  __('HTML', 'yith-woocommerce-account-funds'),
				'plain'  =>  __('Plain text', 'yith-woocommerce-account-funds')
			),
			'default'   =>  'html',
			'id'    =>  'ywf_mail_type'
		),

		'mail_section_end' =>   array(
			'type'  =>  'sectionend',
			'id'    =>  'ywf_mail_section_end'
		),
	)
);

return $settings ;