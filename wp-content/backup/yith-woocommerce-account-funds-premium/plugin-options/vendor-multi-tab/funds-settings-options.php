<?php
if(!defined('ABSPATH')){
	exit;
}

$disable_field_class = defined( 'YITH_WPV_PREMIUM' ) && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '3.5.3', '>=' ) ? '' : 'yith-disabled';

$settings            = array(
	'vendor-multi-tab-funds-settings' => array(
		'vendor_funds_settings_start'           => array(
			'type' => 'title',
			'name' => __( 'Funds Settings', 'yith-woocommerce-account-funds' )
		),
		'vendor_funds_description'             => empty( $disable_field_class ) ? array() :array(
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             => sprintf( '<p class="info-box">%s</p>', __( 'These features are available only with YITH WooCommerce MultiVendor Premium 3.5.3 or greater', 'yith-woocommerce-account-funds' ) )
		),
		'vendor_can_charge_funds' => array(
			'id' => 'ywf_vendor_can_charge',
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'class' => $disable_field_class,
			'name' => __( 'The vendor can charge funds', 'yith-woocommerce-account-funds' ),
			'default' => 'no',
			'desc' => __( 'If enabled, vendor can charge funds like a normal customer', 'yith-woocommerce-account-funds')
		),
		'vendor_can_use_funds' => array(
			'id' => 'ywf_vendor_can_use',
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'class' => $disable_field_class,
			'name' => __( 'The vendor can use funds', 'yith-woocommerce-account-funds' ),
			'default' => 'no',
			'desc' => __( 'If enabled, vendor can use funds to purchase products', 'yith-woocommerce-account-funds')
		),
		'vendor_funds_settings_end' => array(
			'type' => 'sectionend'
		)

	)
);

return $settings;