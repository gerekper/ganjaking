<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


return array(
	'label'    => esc_html__( 'Coupon Setting', 'yith-woocommerce-recover-abandoned-cart' ),
	'pages'    => 'ywrac_email', // or array( 'post-type1', 'post-type2')
	'context'  => 'normal', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'coupons' => array(
			'label'  => esc_html__( 'Coupons', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox_coupons',
				array(
					'ywrac_coupon_value'    => array(
						'label' => esc_html__( 'Coupon Value', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => '',
					),

					'ywrac_coupon_type'     => array(
						'label'   => esc_html__( 'Coupon type', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'    => '',
						'type'    => 'select',
						'options' => array(
							'percent'    => esc_html__( 'Percentage', 'yith-woocommerce-recover-abandoned-cart' ),
							'fixed_cart' => esc_html__( 'Amount', 'yith-woocommerce-recover-abandoned-cart' ),
						),
						'std'     => 'percent',
					),

					'ywrac_coupon_validity' => array(
						'label' => esc_html__( 'Validity in days', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => '7',
					),

				)
			),
		),
	),
);
