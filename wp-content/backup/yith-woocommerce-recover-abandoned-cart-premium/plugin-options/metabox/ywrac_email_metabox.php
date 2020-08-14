<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

return array(
	'label'    => esc_html__( 'Email Settings', 'yith-woocommerce-recover-abandoned-cart' ),
	'pages'    => 'ywrac_email', // or array( 'post-type1', 'post-type2')
	'context'  => 'normal', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(

		'settings' => array(
			'label'  => esc_html__( 'Settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox',
				array(
					'ywrac_email_active'  => array(
						'label' => esc_html__( 'Active', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Choose if activate or deactivate this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					// @since 1.1.0
					   'ywrac_email_type' => array(
						   'label'   => esc_html__( 'Email Type', 'yith-woocommerce-recover-abandoned-cart' ),
						   'desc'    => esc_html__( 'Choose the type for this email', 'yith-woocommerce-recover-abandoned-cart' ),
						   'type'    => 'select',
						   'options' => array(
							   'cart'  => esc_html__( 'Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
							   'order' => esc_html__( 'Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ),
						   ),
						   'std'     => 'abandoned',
					   ),

					'ywrac_email_subject' => array(
						'label' => esc_html__( 'Email Subject', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Choose the subject for this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'text',
						'std'   => '',
					),

					'ywrac_email_auto'    => array(
						'label' => esc_html__( 'Automatic Delivery', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Choose if activate or deactivate automatic delivery for this template', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),

					'ywrac_type_time'     => array(
						'label'   => esc_html__( 'Send after (unit of measure)', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'    => esc_html__( 'Choose the unit of measure of the time that has to pass to send the email (e.g., Send after days)', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'    => 'select',
						'options' => array(
							'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
							'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
							'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
						),
						'std'     => 'hours',
						'deps'    => array(
							'ids'    => '_ywrac_email_auto',
							'values' => 'yes',
						),
					),

					'ywrac_time'          => array(
						'label' => esc_html__( 'Send after (value)', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Set the value of the previous option (e.g., Send after 2)', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywrac_email_auto',
							'values' => 'yes',
						),
					),
				)
			),

		),
	),
);
