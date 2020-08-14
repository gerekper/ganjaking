<?php
if( !defined( 'ABSPATH' ) )
    exit;

$subject_mail   =   sprintf( '{site_title} %s', __( 'Thanks for your feedback!', 'yith-woocommerce-pending-order-survey' ) );
$mail_content   =   sprintf( '%s {customer_name},'."\n\n".'%s:'."\n\n".'{survey}'."\n\n".'%s.'."\n\n".'%s.'."\n\n".'%s,'."\n\n".'{site_title}',
                    __( 'Dear', 'yith-woocommerce-pending-order-survey' ),
                    __( 'thanks for answering this survey', 'yith-woocommerce-pending-order-survey' ),
                    __( 'and for your precious time', 'yith-woocommerce-pending-order-survey' ),
                    __( 'This information is very important for us', 'yith-woocommerce-pending-order-survey'),
                    __( 'Best regards', 'yith-woocommerce-pending-order-survey' )
                );

$desc_tip   =   sprintf( '%s<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('You can use these placeholders', 'yith-woocommerce-pending-order-survey'),
                        __('{site_title} replaced with site title', 'yith-woocommerce-pending-order-survey'),
                        __('{survey} replaced with survey details', 'yith-woocommerce-pending-order-survey'),
                        __('{customer_name} replaced with with customer\'s name', 'yith-woocommerce-pending-order-survey'),
                        __('{customer_email} replaced with customer\'s email address', 'yith-woocommerce-pending-order-survey')
                    );

$mail   =   array(

    'mail'  =>  array(

        'mail_section_start'    =>  array(
            'name'  =>  __( 'Email Settings', 'yith-donations-for-woocommerce' ),
            'type'  =>  'title',
            'id'    =>  'ywcds_mail_section_start'
        ),

        'mail_type'   =>  array(
          'name'    =>  __('Email Type', 'yith-donations-for-woocommerce'),
          'type'    =>  'select',
           'options'    =>  array(
               'html'   =>  __('HTML', 'yith-donations-for-woocommerce'),
               'plain'  =>  __('Plain Text', 'yith-donations-for-woocommerce')
           ),
           'std'    =>  'html',
            'default'   =>  'html',
            'id'    =>  'ywcds_mail_type'
        ),

        'mail_subject'  =>  array(
          'name'    =>  __( 'Email Subject', 'yith-donations-for-woocommerce' ),
          'type'    =>  'text',
          'desc_tip'  =>  $desc_tip,
          'id'      =>  'ywcds_mail_subject',
          'std'     =>  $subject_mail,
          'default' =>  $subject_mail,
          'css'  =>  'width:400px'
        ),

        'mail_content'  =>  array(
          'name'    =>  __( 'Email Content', 'yith-donations-for-woocommerce' ),
          'type'    =>  'textarea',
          'id'      =>  'ywcds_mail_content',
          'desc_tip'  =>  $desc_tip,
          'std'    =>  $mail_content,
          'default'   =>  $mail_content,
          'css'   =>  'width:100%; height:300px; resize:none;'
        ),

      /*  'mail_template' =>  array(
          'name'    =>  __('Email template', 'yith-donations-for-woocommerce'),
          'type'    =>  'select',
          'id'     =>  'ywcds_mail_template',
          'options' =>  array(
              'default' =>  __('WooCommerce Template', 'yith-donations-for-woocommerce'),
              'ywcds_template'  =>  __('YITH Donations Template', 'yith-donations-for-woocommerce'),
            ),
          'default' =>  'default',
          'std'     =>  'default'
         ),
*/
        'mail_enabled'  => array(
            'name'  => __( 'Enable/Disable', 'yith-donations-for-woocommerce' ),
            'id'    => 'ywcds_mail_enabled',
            'type'  => 'checkbox',
            'default'   => 'yes'
        ),

        'mail_section_end' =>   array(
            'type'  =>  'sectionend',
            'id'    =>  'ywcds_mail_section_end'
        )

    )

);

return apply_filters( 'ywcds_mail_settings', $mail );