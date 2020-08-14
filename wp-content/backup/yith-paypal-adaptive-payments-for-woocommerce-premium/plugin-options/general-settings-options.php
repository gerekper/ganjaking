<?php
if(!defined('ABSPATH')){
	exit;
}

$settings = array(
		'general-settings' => array(
				'general_section_start' => array(
						'name' => __('Receiver Settings', 'yith-paypal-adaptive-payments-for-woocommerce'),
						'type' => 'title',
						 
				),
				'general_receiver_list' => array(
					'name' => __('Add Receiver','yith-paypal-adaptive-payments-for-woocommerce' ),
					'type' => 'receivers-list',
					'default' => array(),
					'id' => 'yith_receiver',
					'value' => ''
				),
				'general_section_end' => array(
						'type'=> 'sectionend'
				)
				)
		);

return apply_filters( 'yith_padp_general_settings', $settings );