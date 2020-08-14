<?php
if( !defined('ABSPATH' ) )
    exit;

$endpoints = array(

    'endpoints-settings' => array(

        'endpoints-section-start' => array(
            'type' => 'title',
            'name' => __('Funds endpoints','yith-woocommerce-account-funds'),

        ),
        'fund-make-a-deposit-endpoint-title' => array(
            'name' => __('"Add funds" endpoint title','yith-woocommerce-account-funds'),
            'type' => 'text',
            'default' => __('Add funds', 'yith-woocommerce-account-funds'),
            'id' => 'ywf_make_a_deposit'
        ),

        'fund-make-a-deposit-endpoint-slug' => array(
	        'name' => __('"Add funds" endpoint slug','yith-woocommerce-account-funds'),
	        'type' => 'text',
	        'default' => 'add-funds',
	        'id' => 'ywf_make_a_deposit_slug'
        ),

        'fund-view-income-expenditure-history-endpoint-title' => array(
            'name' => __('Income/Expenditure History endpoint title', 'yith-woocommerce-account-funds'),
            'type' => 'text',
            'default' =>__('Income/Expenditure History', 'yith-woocommerce-account-funds'),
            'id' => 'ywf_view_income_expenditure_history'
        ),

        'fund-view-income-expenditure-history-endpoint-slug' => array(
	        'name' => __('Income/Expenditure History endpoint slug', 'yith-woocommerce-account-funds'),
	        'type' => 'text',
	        'default' =>'view-history',
	        'id' => 'ywf_view_income_expenditure_history_slug'
        ),
       /* 'redeem_fund_endpoint_title' => array(
        	'id' => 'ywf_redeem_funds',
	        'name' => __('Redeem fund endpoint title', 'yith-woocommerce-account-funds'),
	        'type' => 'yith-field',
	        'yith-type' => 'text',
	        'desc' => __( 'Set the endpoint name for Redeem Funds', 'yith-woocommerce-account-funds'),
	        'default' => _x('Redeem Funds' ,'Endpoint title visible on My Account page', 'yith-woocommerce-account-funds')
        ),
        'redeem_fund_endpoint_slug' => array(
	        'id' => 'ywf_redeem_funds_slug',
	        'name' => __('Redeem fund endpoint slug', 'yith-woocommerce-account-funds'),
	        'type' => 'yith-field',
	        'yith-type' => 'text',
	        'desc' => __( 'Set the endpoint slug for Redeem Funds', 'yith-woocommerce-account-funds'),
	        'default' =>'redeem-funds'
        ),*/
        'endpoint-section-end' => array(
            'type'  => 'sectionend',

        )
    )
);

return apply_filters( 'ywf_endpoints_settings', $endpoints );
