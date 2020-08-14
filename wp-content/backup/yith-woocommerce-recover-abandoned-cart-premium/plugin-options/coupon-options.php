<?php

$settings = array(

	'coupon' => array(

		'coupon_settings'         => array(
			'name' => esc_html__( 'Coupon Settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_coupon_settings',
		),


		'coupon_prefix'           => array(
			'name'    => esc_html__( 'Coupon Prefix', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'    => esc_html__( 'Add a 3-character prefix ina a coupon code', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'      => 'ywrac_coupon_prefix',
			'type'    => 'text',
			'default' => 'RAC',
		),

		'coupon_delete_after_use' => array(
			'name'    => esc_html__( 'Delete the coupon once used', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'    => '',
			'id'      => 'ywrac_coupon_delete_after_use',
			'type'    => 'checkbox',
			'default' => 'yes',
		),

		'coupon_delete_expired'   => array(
			'name'    => esc_html__( 'Delete expired coupons', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'    => '',
			'id'      => 'ywrac_coupon_delete_expired',
			'type'    => 'checkbox',
			'default' => 'yes',
		),

		'coupon_end_form'         => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_coupon_settings_end_form',
		),


	),

);

return apply_filters( 'yith_ywrac_panel_settings_options', $settings );
