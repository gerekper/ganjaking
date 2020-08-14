<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


return array(
	'label'    => esc_html__( 'Test Email Template', 'yith-woocommerce-recover-abandoned-cart' ),
	'pages'    => 'ywrac_email', // or array( 'post-type1', 'post-type2')
	'context'  => 'side', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'test' => array(
			'label'  => esc_html__( 'Send a testing email', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox_coupons',
				( isset( $_GET['post'] ) ) ? array(
					'ywrac_email_to_send'     => array(
						'label' => '',
						'desc'  => '',
						'type'  => 'text',
						'std'   => get_bloginfo( 'admin_email' ),
					),

					'ywrac_send_email_button' => array(
						'label' => '',
						'desc'  => sprintf( '<a  class="ywrac-button-sent-email button-primary button-large" data-id="%d" href="#" class="button">%s</a>', $_GET['post'], esc_html__( 'Send email', 'yith-woocommerce-recover-abandoned-cart' ) ),
						'type'  => 'simple-text',
					),


				) : array(
					'ywrac_send_alert' => array(
						'label' => '',
						'desc'  => esc_html__( 'Save the email template before sending a testing email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'simple-text',
					),
				)
			),
		),
	),
);
