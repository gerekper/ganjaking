<?php
if(!defined('ABSPATH')){
	exit;
}

$settings = array(
		'cron-settings' => array(
				'cron_section_start' => array(
						'name' => __('Cron Settings', 'yith-paypal-adaptive-payments-for-woocommerce'),
						'type' => 'title',
						 
				),
                'cron_type' => array(
                    'name' => __( 'Unit of measurement', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                    'desc' => '',
                    'id'   => 'ywpadp_cron_check_type',
                    'type' => 'select',
                    'options' => array(
                        'minutes' => __('Minutes','yith-paypal-adaptive-payments-for-woocommerce'),
                        'hours' => __('Hours','yith-paypal-adaptive-payments-for-woocommerce'),
                        'days' => __('Days','yith-paypal-adaptive-payments-for-woocommerce'),
                    ),
                    'std'  => 'hours'
                ),
				'cron_check_day' => array(
					'name' => __( 'Cron frequency','yith-paypal-adaptive-payments-for-woocommerce' ),
					'type' => 'number',
					'default' => 12,
                    'min' => 0,
					'id' => 'ywpadp_cron_check_day',
                    'desc' => __( 'Please, check whether your incomplete orders are ready to be paid (only for delayed chained payments).',
                        'yith-paypal-adaptive-payments-for-woocommerce' )
				),
				'cron_section_end' => array(
						'type'=> 'sectionend'
				)
				)
		);

return apply_filters( 'yith_padp_cron_settings', $settings );