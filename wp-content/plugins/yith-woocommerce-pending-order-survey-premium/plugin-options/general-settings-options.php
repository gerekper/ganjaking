<?php
if( !defined( 'ABSPATH' ) )
    exit;

$admin_url = admin_url('admin.php');
$params = array(
    'page'  => 'wc-settings',
    'tab'   => 'email',
    'section'   => 'yith_wc_send_pending_order_thanks_email'

);
$url = esc_url( add_query_arg( $params, $admin_url ) );
$thanks_email_url = sprintf('%s <a href="%s" rel="nofollow">%s</a>',__( 'Send an email to users who have answered the survey',
    'yith-woocommerce-pending-order-survey' ) ,$url, __('Click here to customize email content', 'yith-woocommerce-pending-order-survey') );
$setting    =    array(

    'general-settings'  =>  array(

        'section_order_pending_settings'        => array(
             'name'     => __( 'Pending Order Settings', 'yith-woocommerce-pending-order-survey' ),
            'type'      => 'title',
            'id'        => 'ywpos_pending_order_section'
        ),
    	 'include_pending_from' => array(
    	 		'name' => __('Mark "Pending Order" as "Survey Pending Order"','yith-woocommerce-pending-order-survey'),
    	 		'type'	=> 'number',
    	 		'custom_attributes' => array(
    	 				'min' => 0
    	 		),
    	 		'default' => 30,
    	 		'id' => 'ywcpos_include_pending_from',
    	 		'desc' => __('in minutes','yith-woocommerce-pending-order-survey' )
    	 )	,
         'pending_from_cancelled_time_unit'=> array(
              'name'    => __( 'Cut-off time type for pending order', 'yith-woocommerce-pending-order-survey' ),
              'type'    => 'select',
             'desc'  =>  __( 'Unit of time', 'yith-woocommerce-pending-order-survey' ),
             'options' => array(
                 'minutes' => __('Minutes','yith-woocommerce-pending-order-survey'),
                 'hours' => __('Hours','yith-woocommerce-pending-order-survey'),
                 'days' => __('Days','yith-woocommerce-pending-order-survey'), ),

             'id'    =>  'ywcpos_pending_from_cancelled_unit',
             'default'  => 'minutes'
         )   ,
        'pending_from_cancelled_time_value'=> array(
            'name'    => __( 'Cut-off time value for pending order', 'yith-woocommerce-pending-order-survey' ),
            'desc'  =>  __( 'Process pending orders as cancelled after current time. When this limit is reached, the pending order will be
            cancelled. Leave blank to disable.', 'yith-woocommerce-pending-order-survey' ),
            'type'  =>  'number',
            'custom_attributes' =>  array(
                'min'   =>  0,
            ),
            'id'    =>  'ywcpos_pending_from_cancelled_value',
            'default'  => 60
        )   ,

        'pending_order_end' => array(
            'type' => 'sectionend',
            'id'   => 'ywcpos_pending_order_end'
        ),

        'section_general_settings'     => array(
            'name' => __( 'Admin email settings', 'yith-woocommerce-pending-order-survey' ),
            'type' => 'title',
            'id'   => 'ywcpos_section_general'
        ),

        'send_email_after_answer' => array(
            'name' => __( 'Thank-you email', 'yith-woocommerce-pending-order-survey' ),
            'desc'  => $thanks_email_url,
            'type'  => 'checkbox',
            'id' => 'ywcpos_send_email_after',
            'default' => 'no'
        ),

        'section_general_settings_end' => array(
            'type' => 'sectionend',
            'id'   => 'ywcpos_section_general_end'
        ),

        'section_user_email_settings'     => array(
            'name' => __( 'User email settings', 'yith-woocommerce-pending-order-survey' ),
            'type' => 'title',
            'id'   => 'ywcpos_user_section_email'
        ),

        'sender_name' => array(
            'name' => __( 'Email sender name', 'yith-woocommerce-pending-order-survey' ),
            'desc' => '',
            'id'   => 'ywcpos_user_sender_name',
            'type' => 'text',
            'std'  => get_bloginfo( 'name' )
        ),

        'sender_email' => array(
            'name' => __( 'Email sender', 'yith-woocommerce-pending-order-survey' ),
            'desc' => '',
            'id'   => 'ywcpos_user_email_sender',
            'type' => 'text',
            'std'  => get_bloginfo( 'admin_email' )
        ),

        'reply_to' => array(
            'name' => __( 'Reply to:', 'yith-woocommerce-pending-order-survey' ),
            'desc' => '',
            'id'   => 'ywcpos_user_email_reply',
            'type' => 'text',
            'std'  => ''
        ),
        'section_user_email_end_form'=> array(
            'type'              => 'sectionend',
            'id'                => 'ywcpos_user_section_email_end_form'
        ),

        'section_coupon_start' => array(
                'name' => __( 'Coupon settings', 'yith-woocommerce-pending-order-survey' ),
                'type'  => 'title',
                'id'    => 'ywcpos_section_coupon_start'
        ),
        'coupon_prefix' => array(
            'name'    =>  __( 'Coupon prefix', 'yith-woocommerce-pending-order-survey' ),
            'desc'    =>  __( 'Add a 3-character prefix in coupon codes', 'yith-woocommerce-pending-order-survey' ),
            'id'      => 'ywcpos_coupon_prefix',
            'type'    => 'text',
            'default' => 'POS'
        ),

        'section_coupon_end'=> array(
            'type'              => 'sectionend',
            'id'                => 'ywcpos_section_coupon_end'
        ),

    )
);

return apply_filters( 'yith_wc_pending_order_survey_settings', $setting );