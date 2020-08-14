<?php
if( !defined( 'ABSPATH' ) )
    exit;

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
    __('{site_title} replaced by website title', 'yith-woocommerce-account-funds'),
    __('{user_funds} replaced by maximum recharge limit per transaction', 'yith-woocommerce-account-funds'),
    __('{customer_email} replaced by customer\'s email address', 'yith-woocommerce-account-funds'),
    __('{customer_name} replaced by customer\'s name','yith-woocommerce-account-funds'),
    __('{button_charging} replaced by "Deposit now" button', 'yith-woocommerce-account-funds')

);

$mail   =   array(

    'email-settings'  =>  array(

        'mail_section_start'    =>  array(
            'name'  =>  __( 'Deposit funds - email settings', 'yith-woocommerce-account-funds' ),
            'type'  =>  'title',
            'id'    =>  'ywf_mail_section_start'
        ),

        'mail_enabled'  => array(
            'name'  => __( 'Enable/Disable', 'yith-woocommerce-account-funds' ),
            'id'    => 'ywf_mail_enabled',
            'type'  => 'checkbox',
            'default'   => 'yes'
        ),

        'mail_type'   =>  array(
            'name'    =>  __('Email type', 'yith-woocommerce-account-funds'),
            'type'    =>  'select',
            'options'    =>  array(
                'html'   =>  __('HTML', 'yith-woocommerce-account-funds'),
                'plain'  =>  __('Plain text', 'yith-woocommerce-account-funds')
            ),
            'std'    =>  'html',
            'default'   =>  'html',
            'id'    =>  'ywf_mail_type'
        ),


        'mail_subject'  =>  array(
            'name'    =>  __( 'Email subject', 'yith-woocommerce-account-funds' ),
            'type'    =>  'text',
            'desc_tip'  =>  $desc_tip,
            'id'      =>  'ywf_mail_subject',
            'std'     =>  $subject_mail,
            'default' =>  $subject_mail,
            'css'  =>  'width:400px'
        ),
        'mail_sender_name' => array(
            'name' => __( 'Email sender', 'yith-woocommerce-account-funds' ),
            'desc' => '',
            'id'   => 'ywf_user_sender_name',
            'type' => 'text',
            'default'  => get_bloginfo( 'name' )
        ),

        'mail_content'  =>  array(
            'name'    =>  __( 'Email content', 'yith-woocommerce-account-funds' ),
            'type'    =>  'textarea',
            'id'      =>  'ywf_mail_content',
            'desc_tip'  =>  $desc_tip,

            'default'   =>  $mail_content,
            'css'   =>  'width:100%; height:300px; resize:none;'
        ),
        'mail_amount_limit' => array(
            'name' => sprintf('%s (%s)',__('User funds threshold','yith-woocommerce-account-funds' ),get_woocommerce_currency_symbol() ),
            'type'   => 'number',
            'custom_attributes' => array(
                'min' => 0,
                'step' => 0.5
            ),
            'default' => 0,
            'id' => 'ywf_email_limit',
            'desc' => __('Set a minimum threshold below which an email is sent to customers to invite them to deposit more funds', 'yith-woocommerce-account-funds'),
            'css' => 'width:80px;'

        ),

        'mail_section_end' =>   array(
            'type'  =>  'sectionend',
            'id'    =>  'ywf_mail_section_end'
        ),




    )

);

return apply_filters( 'yith_funds_mail_settings', $mail );