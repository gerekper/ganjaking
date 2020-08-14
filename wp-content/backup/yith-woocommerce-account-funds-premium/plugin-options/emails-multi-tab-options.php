<?php
if( !defined('ABSPATH')){
	exit;
}

$multi_tab = array(
	'emails-multi-tab' =>array(
		'emails-multi-tab-options' => array(
			'type' => 'multi_tab',
			'sub-tabs' => array(
				'emails-multi-tab-deposit-email' => array(
					'title' => __( 'Deposit', 'yith-woocommerce-account-funds' )
				),

				'emails-multi-tab-edit-funds-email' => array(
					'title' => __('Funds Edited', 'yith-woocommerce-account-funds' )
				),
			/*	'emails-multi-tab-redeem-email' => array(
					'title' => __('Vendor Redeem', 'yith-woocommerce-account-funds')
				)*/
			)
		)
	)
);

return $multi_tab;
