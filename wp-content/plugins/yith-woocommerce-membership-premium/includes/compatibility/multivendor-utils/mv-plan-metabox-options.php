<?php
return array(
	'multi-vendor-options'               => array(
		'type'  => 'title',
		'label' => _x( 'Multi Vendor', 'Plan option section title', 'yith-woocommerce-membership' ),
	),
	'_mv-override-multi-vendor-settings' => array(
		'label' => __( 'Override Multi Vendor settings', 'yith-woocommerce-membership' ),
		'type'  => 'onoff',
		'std'   => 'no',
	),

	'_mv-vendors_product_amount_limit' => array(
		'label' => __( 'Enable product amount limit', 'yith-woocommerce-membership' ),
		'desc'  => __( 'Limit product amount for each vendor', 'yith-woocommerce-membership' ),
		'type'  => 'checkbox',
		'std'   => 'no',
		'deps'  => array(
			'id'    => '_mv-override-multi-vendor-settings',
			'value' => 'yes',
		),
	),

	'_mv-vendors_product_amount' => array(
		'label'             => __( 'Product amount limit', 'yith-woocommerce-membership' ),
		'type'              => 'number',
		'std'               => 25,
		'desc'              => __( 'Set a maximum number of products that each vendor can publish', 'yith-woocommerce-membership' ),
		'custom_attributes' => "min='1' step='1'",
		'deps'              => array(
			'id'    => '_mv-override-multi-vendor-settings',
			'value' => 'yes',
		),

	),
);
