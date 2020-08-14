<?php

if ( ! defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

$settings = array(

	'settings'  => array(

		'general-options' => array(
			'title' => __( 'General Options', 'yith-woocommerce-best-sellers' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wcbsl-general-options'
		),

		'show-bestseller-badge' => array(
			'id'        => 'yith-wcbsl-show-bestseller-badge',
			'name'      => __( 'Show "Best Seller" badge', 'yith-woocommerce-best-sellers' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Select to show "Best Seller" badge on best-seller products.', 'yith-woocommerce-best-sellers' ),
			'default'   => 'no'
		),

		'general-options-end' => array(
			'type'      => 'sectionend',
			'id'        => 'yith-wcbsl-general-options'
		)
	)
);

return apply_filters( 'yith_wcbsl_panel_settings_options', $settings );