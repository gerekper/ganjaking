<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

return array(
	'label'    => __( 'Subscription Details', 'yith-woocommerce-subscription' ),
	'pages'    => 'ywsbs_subscription', // or array( 'post-type1', 'post-type2')
	'context'  => 'normal', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'settings' => array(
			'label'  => __( 'Subscription Schedule', 'yith-woocommerce-cart-messages' ),
			'fields' => apply_filters(
				'ywsbs_metabox',
				array(

					'start_date'       => array(
						'label'   => __( 'Started Date', 'yith-woocommerce-subscription' ),
						'desc'    => __( 'Update Started Date as timestamp', 'yith-woocommerce-subscription' ),
						'private' => false,
						'type'    => 'text',
						'std'     => '',
					),

					'payment_due_date' => array(
						'label'   => __( 'Payment Due Date', 'yith-woocommerce-subscription' ),
						'desc'    => __( 'Update Payment Due Date as timestamp', 'yith-woocommerce-subscription' ),
						'private' => false,
						'type'    => 'text',
						'std'     => '',
					),


					'expired_date'     => array(
						'label'   => __( 'Expired Date', 'yith-woocommerce-subscription' ),
						'desc'    => __( 'Update Expired Date as timestamp', 'yith-woocommerce-subscription' ),
						'private' => false,
						'type'    => 'text',
						'std'     => '',
					),

					'cancelled_date'   => array(
						'label'   => __( 'Cancelled Date', 'yith-woocommerce-subscription' ),
						'desc'    => __( 'Update Cancelled Date as timestamp', 'yith-woocommerce-subscription' ),
						'private' => false,
						'type'    => 'text',
						'std'     => '',
					),

					'end_date'         => array(
						'label'   => __( 'End Date', 'yith-woocommerce-subscription' ),
						'desc'    => __( 'Update Expired Date as timestamp', 'yith-woocommerce-subscription' ),
						'private' => false,
						'type'    => 'text',
						'std'     => '',
					),

				)
			),
		),
	),
);
