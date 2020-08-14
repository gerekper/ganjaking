<?php
if(!defined('ABSPATH')){
	exit;
}

$subject_mail   =   sprintf( '%s {log_date}',__( 'The Administrator has changed your funds from', 'yith-woocommerce-account-funds' ) );

$email_content = sprintf('%s {customer_name}'."\n\n".'%s {before_funds} %s {after_funds} %s:'."\n\n".'{change_reason}'."\n\n"."%s {site_title}",
	__('Dear','yith-woocommerce-account-funds'),
	__('the administrator has changed your funds from','yith-woocommerce-account-funds'),
	__('to','yith-woocommerce-account-funds'),
	__('for the following reason','yith-woocommerce-account-funds'),
	__( 'Best regards', 'yith-woocommerce-account-funds' )
);

$desc_tip   =   sprintf( '%s<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
	__('You can use the following placeholders', 'yith-woocommerce-account-funds'),
	__('<code>{site_title}</code> replaced by site title', 'yith-woocommerce-account-funds'),
	__('<code>{before_funds}</code> replaced by user funds before admin adjustments', 'yith-woocommerce-account-funds'),
	__('<code>{after_funds}</code> replaced by user funds after admin adjustments', 'yith-woocommerce-account-funds'),
	__('<code>{customer_email}</code> replaced by customer\'s email address', 'yith-woocommerce-account-funds'),
	__('<code>{customer_name}</code> replaced by customer\'s name','yith-woocommerce-account-funds'),
	__('<code>{log_date}</code> replaced with adjustment date', 'yith-woocommerce-account-funds'),
	__('<code>{change_reason}</code> replaced by adjustment reason', 'yith-woocommerce-account-funds')

);


$settings = array(
	'emails-multi-tab-edit-funds-email' => array(
		'mail_admin_change_fund_section_start'    =>  array(
			'name'  =>  __( 'Funds edited', 'yith-woocommerce-account-funds' ),
			'type'  =>  'title',
		),

		'mail_admin_change_fund_enabled'  => array(
			'name'  => __( 'Enable/Disable', 'yith-woocommerce-account-funds' ),
			'id'    => 'ywf_mail_admin_change_fund_enabled',
			'type' => 'yith-field',
			'yith-type'  => 'onoff',
			'default'   => 'yes',
			'desc' => __('If enabled, send an email to customer as soon the admin change the funds in admin area', 'yith-woocommerce-account-funds')
		),

		'mail_admin_change_fund_subject'  =>  array(
			'name'    =>  __( 'Email Subject', 'yith-woocommerce-account-funds' ),
			'type' => 'yith-field',
			'yith-type'    =>  'text',
			'desc'  =>  $desc_tip,
			'id'      =>  'ywf_mail_admin_change_fund_subject',
			'default' =>  __( 'The Administrator has changed your funds from {log_date}', 'yith-woocommerce-account-funds' ),

		),
		'mail_change_fund_sender_name' => array(
			'name' => __( 'Email heading', 'yith-woocommerce-account-funds' ),
			'desc' => $desc_tip,
			'id'   => 'ywf_user_change_fund_heading',
			'type' => 'yith-field',
			'yith-type' => 'text',
			'default'  => __('Funds edited by admin!', 'yith-woocommerce-account-funds')

		),
		'mail_change_fund_content'  =>  array(
			'name'    =>  __( 'Email Content', 'yith-woocommerce-account-funds' ),
			'type' => 'yith-field',
			'yith-type'    =>  'textarea',
			'id'      =>  'ywf_mail_change_fund_content',
			'desc'  =>  $desc_tip,
			'default'   =>  $email_content,
			'css'   =>  'width:100%; height:300px; resize:none;'
		),
		'mail_admin_change_fund_type'   =>  array(
			'name'    =>  __('Email type', 'yith-woocommerce-account-funds'),
			'type' => 'yith-field',
			'yith-type'    =>  'select',
			'class' => 'email_type wc-enhanced-select',
			'options'    =>  array(
				'html'   =>  __('HTML', 'yith-woocommerce-account-funds'),
				'plain'  =>  __('Plain text', 'yith-woocommerce-account-funds')
			),
			'default'   =>  'html',
			'id'    =>  'ywf_mail_admin_change_fund_type'
		),
		'mail_change_fund_section_end' =>   array(
			'type'  =>  'sectionend',

		),
	)
);

return $settings;