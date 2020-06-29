<?php
if( !defined( 'ABSPATH' ) )
    exit;

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
    __('{site_title} replaced by site title', 'yith-woocommerce-account-funds'),
    __('{before_funds} replaced by user funds before admin adjustments', 'yith-woocommerce-account-funds'),
    __('{after_funds} replaced by user funds after admin adjustments', 'yith-woocommerce-account-funds'),
    __('{customer_email} replaced by customer\'s email address', 'yith-woocommerce-account-funds'),
    __('{customer_name} replaced by customer\'s name','yith-woocommerce-account-funds'),
    __('{log_date} replaced with adjustment date', 'yith-woocommerce-account-funds'),
    __('{change_reason} replaced by adjustment reason', 'yith-woocommerce-account-funds')

);

$mail   =   array(

    'email-advise-settings'  =>  array(

        'mail_admin_change_fund_section_start'    =>  array(
            'name'  =>  __( 'Changes applied to funds - email settings', 'yith-woocommerce-account-funds' ),
            'type'  =>  'title',
        ),

        'mail_admin_change_fund_enabled'  => array(
            'name'  => __( 'Enable/Disable', 'yith-woocommerce-account-funds' ),
            'id'    => 'ywf_mail_admin_change_fund_enabled',
            'type'  => 'checkbox',
            'default'   => 'yes'
        ),

        'mail_admin_change_fund_type'   =>  array(
            'name'    =>  __('Email type', 'yith-woocommerce-account-funds'),
            'type'    =>  'select',
            'options'    =>  array(
                'html'   =>  __('HTML', 'yith-woocommerce-account-funds'),
                'plain'  =>  __('Plain text', 'yith-woocommerce-account-funds')
            ),
            'std'    =>  'html',
            'default'   =>  'html',
            'id'    =>  'ywf_mail_admin_change_fund_type'
        ),

        'mail_admin_change_fund_subject'  =>  array(
            'name'    =>  __( 'Email Subject', 'yith-woocommerce-account-funds' ),
            'type'    =>  'text',
            'desc_tip'  =>  $desc_tip,
            'id'      =>  'ywf_mail_admin_change_fund_subject',
            'default' =>  __( 'The Administrator has changed your funds from {log_date}', 'yith-woocommerce-account-funds' ),
            'css'  =>  'width:80% '
        ),
        'mail_change_fund_sender_name' => array(
            'name' => __( 'Email sender name', 'yith-woocommerce-account-funds' ),
            'desc_tip' => $desc_tip,
            'id'   => 'ywf_user_change_fund_sender_name',
            'type' => 'text',
            'default'  => get_bloginfo( 'name' ),
            'css'  =>  'width:80%'
        ),
        'mail_change_fund_content'  =>  array(
            'name'    =>  __( 'Email Content', 'yith-woocommerce-account-funds' ),
            'type'    =>  'textarea',
            'id'      =>  'ywf_mail_change_fund_content',
            'desc_tip'  =>  $desc_tip,
            'default'   =>  $email_content,
            'css'   =>  'width:100%; height:300px; resize:none;'
        ),
        'mail_change_fund_section_end' =>   array(
            'type'  =>  'sectionend',

        ),
        
    )

);

return apply_filters( 'yith_funds_mail_admin_change_funds_settings', $mail );