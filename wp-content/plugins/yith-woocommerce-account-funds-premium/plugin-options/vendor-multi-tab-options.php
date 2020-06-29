<?php
if( !defined('ABSPATH')){
	exit;
}



$multi_tab = array(
	'vendor-multi-tab' =>array(
		'vendor-multi-tab-options' => array(
			'type' => 'multi_tab',
			'sub-tabs' => array(
				'vendor-multi-tab-funds-settings' => array(
					'title' => __( 'General Settings', 'yith-woocommerce-account-funds' )
				),

				'vendor-multi-tab-redeem-funds' => array(
					'title' => _x('Redeem Funds','Sub tab title, visible on backend', 'yith-woocommerce-account-funds' )
				)
			)
		)
	)
);

return $multi_tab;