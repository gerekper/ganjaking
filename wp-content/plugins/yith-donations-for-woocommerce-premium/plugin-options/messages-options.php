<?php
if( !defined( 'ABSPATH' ) )
    exit;

$min_req    =   sprintf( '%s {min_donation}',
                __( 'Minimum donation allowed is', 'yith-donations-for-woocommerce')
    );

$max_req    =   sprintf( '%s {max_donation}',
    __( 'Maximum donation allowed is', 'yith-donations-for-woocommerce')
);

$messages   =   array(

    'messages'  =>  array(

        'section_message_settings'     => array(
            'name' => __( 'Donation label settings', 'yith-donations-for-woocommerce' ),
            'type' => 'title',
            'id'   => 'ywcds_section_message'
        ),
    'message_for_donation'  =>  array(
        'name'  =>  __('Message attached to your donation', 'yith-donations-for-woocommerce'),
        'type'  => 'text',
        'std'   =>  __('Make a donation', 'yith-donations-for-woocommerce'),
        'default'   =>  __('Make a donation', 'yith-donations-for-woocommerce'),
        'id'    =>  'ywcds_message_for_donation',
        'css'   =>  'width:50%'
    ),

    'message_right_donation'    =>  array(
        'name'  =>  __( 'Thank you message' , 'yith-donations-for-woocommerce' ),
        'type'  =>  'text',
        'std'   =>  __( 'Thanks for your donation', 'yith-donations-for-woocommerce'),
        'default'  =>  __( 'Thanks for your donation', 'yith-donations-for-woocommerce'),
        'id'    =>  'ywcds_message_right_donation',
        'css' =>  'width:50%;'
    ),


    'message_empty_donation'    =>  array(
        'name'  =>  __( 'Text displayed when donation field is empty', 'yith-donations-for-woocommerce' ),
        'std'  =>  __( 'Please enter an amount', 'yith-donations-for-woocommerce' ),
        'default'  =>  __( 'Please enter amount', 'yith-donations-for-woocommerce' ),
        'type'  =>  'text',
        'id'    =>  'ywcds_message_empty_donation',
        'css' =>  'width:50%;'
    ),

    'message_invalid_donation'  =>  array(
        'name'  =>  __( 'Text displayed when donation field is invalid', 'yith-donations-for-woocommerce' ),
        'type'  =>  'text',
        'id'    =>  'ywcds_message_invalid_donation',
        'std'   =>   __('Please enter a valid value', 'yith-donations-for-woocommerce'),
        'default'   =>   __('Please enter a valid value', 'yith-donations-for-woocommerce'),
        'css' =>  'width:50%;'
        ),

        'message_negative_donation' =>  array(
            'name'  =>  __('Text displayed when donation field value is negative', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'id'    =>  'ywcds_message_negative_donation',
            'std'   =>  __( 'Please enter a number greater than 0', 'ywcsd' ),
            'default'   =>  __('Please enter a number greater than 0', 'yith-donations-for-woocommerce'),
            'css'   =>  'width:50%'
        ),
    'message_min_donation'  =>  array(
            'name'  =>  __( 'Text displayed for minimum donation required', 'yith-donations-for-woocommerce' ),
            'type'  =>  'text',
            'id'    =>  'ywcds_message_min_donation',
            'std'   =>   $min_req,
            'default'   =>  $min_req,
            'desc_tip'      =>  __('{min_donation} is replaced with minimum donation required', 'yith-donations-for-woocommerce' ),
            'css' =>  'width:50%;'
        ),

    'message_max_donation'  =>  array(
            'name'  =>  __( 'Text displayed for maximum donation allowed', 'yith-donations-for-woocommerce' ),
            'type'  =>  'text',
            'id'    =>  'ywcds_message_max_donation',
            'std'   =>   $max_req,
            'default'   =>  $max_req,
            'desc_tip'      =>  __('{max_donation} is replaced with maximum donation allowed', 'yith-donations-for-woocommerce' ),
            'css' =>  'width:50%;'
        ),
    'message_obligatory_donation'   =>  array(
            'name'  =>  __( 'Text displayed for compulsory donation', 'yith-donations-for-woocommerce' ),
            'type'  =>  'text',
            'id'    =>  'ywcds_message_obligatory_donation',
            'std'   =>  __( 'Sorry but for this product you must have added a donation first', 'yith-donations-for-woocommerce' ),
            'default'   =>   __( 'Sorry but for this product you must have added a donation first', 'yith-donations-for-woocommerce' ),
        'css' =>  'width:50%;'
    ),

      'section_message_end' => array(
          'type' => 'sectionend',
          'id'   => 'ywcds_section_message_end'
      ),

        'section_widget_text_start'     =>  array(
            'name'  =>  __('Customize text of the labels shown in the Widget "Summary"', 'yith-donations-for-woocommerce'),
            'type'  =>  'title',
            'id'    =>  'ywcds_section_widget_text_start',
            'css' =>  'width:50%;'
        ),

        'widget_text_today' =>  array(
            'name'  =>  __('Today', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>  __( 'Today we have collected', 'yith-donations-for-woocommerce' ),
            'default'   =>  __( 'Today we have collected', 'yith-donations-for-woocommerce' ),
            'id'        =>  'ywcds_widget_text_day',
            'css' =>  'width:50%;'
        ),
        'widget_text_year' =>  array(
            'name'  =>  __('Year', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>   __( 'This year we have collected','yith-donations-for-woocommerce' ),
            'default'   =>  __( 'This year we have collected','yith-donations-for-woocommerce' ),
            'id'        =>  'ywcds_widget_text_year',
            'css' =>  'width:50%;'
        ),

        'widget_text_week' =>  array(
            'name'  =>  __('Last 7 days', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>    __( 'In the last 7 days we have collected', 'yith-donations-for-woocommerce' ),
            'default'   =>   __( 'In the last 7 days we have collected', 'yith-donations-for-woocommerce' ),
            'id'        =>  'ywcds_widget_text_week',
            'css' =>  'width:50%;'
        ),
        'widget_text_month' =>  array(
            'name'  =>  __('Month', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>   __( 'This month we have collected', 'yith-donations-for-woocommerce'),
            'default'   =>  __( 'This month we have collected', 'yith-donations-for-woocommerce'),
            'id'        =>  'ywcds_widget_text_month',
            'css' =>  'width:50%;'
        ),

        'widget_text_last_month' =>  array(
            'name'  =>  __('Last Month', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>   __( 'In the last month we collected', 'yith-donations-for-woocommerce'),
            'default'   =>  __( 'In the last month we collected', 'yith-donations-for-woocommerce'),
            'id'        =>  'ywcds_widget_text_last_month',
            'css' =>  'width:50%;'
        ),
        'widget_text_always' =>  array(
            'name'  =>  __('Always', 'yith-donations-for-woocommerce'),
            'type'  =>  'text',
            'std'   =>  __( 'So far we have collected', 'yith-donations-for-woocommerce'),
            'default'   => __( 'So far we have collected', 'yith-donations-for-woocommerce'),
            'id'        =>  'ywcds_widget_text_always',
            'css' =>  'width:50%;'
        ),
        'section_widget_text_end'     =>  array(
            'type'  =>  'sectionend',
            'id'    =>  'ywcds_section_widget_text_start'
        ),

    )

);


return apply_filters( 'yith_wc_donations_message_settings', $messages );