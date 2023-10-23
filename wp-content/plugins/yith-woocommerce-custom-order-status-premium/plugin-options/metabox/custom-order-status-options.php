<?php
/**
 * Meta-box Options
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

return array(
	'settings'      => array(
		'label'  => __( 'Settings', 'yith-woocommerce-custom-order-status' ),
		'fields' => array(
			'status_type'         => array(
				'label'   => __( 'Status Type', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Select a type for your status.', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'select',
				'options' => array(
					'custom'     => _x( 'Custom Status', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'pending'    => _x( 'Pending Payment', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'processing' => _x( 'Processing', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'on-hold'    => _x( 'On Hold', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'completed'  => _x( 'Completed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'cancelled'  => _x( 'Cancelled', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'refunded'   => _x( 'Refunded', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					'failed'     => _x( 'Failed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
				),
				'private' => false,
				'std'     => 'custom',
			),
			'slug'                => array(
				'label'   => __( 'Slug', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Unique slug of your status', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => '',
			),
			'color'               => array(
				'label'         => __( 'Color', 'yith-woocommerce-custom-order-status' ),
				'desc'          => __( 'Color of your status', 'yith-woocommerce-custom-order-status' ),
				'type'          => 'colorpicker',
				'private'       => false,
				'std'           => '#2470FF',
				'alpha_enabled' => false,
			),
			'icon-type'           => array(
				'label'   => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Icon of your status', 'yith-woocommerce-custom-order-status' ),
				'name'    => 'yit_metaboxes[icon][select]',
				'type'    => 'select',
				'private' => false,
				'options' => array(
					'none' => __( 'Default', 'yith-woocommerce-custom-order-status' ),
					'icon' => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
				),
				'std'     => 'none',
			),
			'icon-icon'           => array(
				'label'   => __( 'Choose Icon', 'yith-woocommerce-custom-order-status' ),
				'name'    => 'yit_metaboxes[icon][icon]',
				'type'    => 'icons',
				'private' => false,
				'std'     => 'FontAwesome:genderless',
				'deps'    => array(
					'id'    => 'icon-type',
					'value' => 'icon',
					'type'  => 'hide',
				),
			),
			'graphicstyle'        => array(
				'label'   => __( 'Graphic Style', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Style of your status button and indicator', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'select',
				'options' => array(
					'icon' => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
					'text' => __( 'Text', 'yith-woocommerce-custom-order-status' ),
				),
				'private' => false,

			),
			'nextactions'         => array(
				'label'    => __( 'Next Actions', 'yith-woocommerce-custom-order-status' ),
				'desc'     => __( 'Select statuses that will be enabled by this status', 'yith-woocommerce-custom-order-status' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => wc_get_order_statuses(),
				'std'      => array(
					'wc-completed',
				),
				'multiple' => true,
				'private'  => false,

			),
			'can-cancel'          => array(
				'label'   => __( 'User can cancel', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the customer can cancel orders when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'can-pay'             => array(
				'label'   => __( 'User can pay', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the customer can pay orders when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'is-paid'             => array(
				'label'   => __( 'Order is paid', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the order is considered paid or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
				'deps'    => array(
					'id'    => 'status_type',
					'value' => 'custom',
				),
			),
			'downloads-permitted' => array(
				'label'   => __( 'Allow Downloads', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to allow downloads when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'display-in-reports'  => array(
				'label'   => __( 'Display in Reports', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to include orders marked with this status in Reports', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'restore-stock'       => array(
				'label'   => __( 'Restore Stock', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to restore stock quantities or not when this status is applied', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'show-in-actions'     => array(
				'label'   => __( 'Show always in Actions', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to always show this status in WooCommerce Order Actions', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			),
			'mail-settings-info'  => array(
				'label'   => __( 'Email Settings', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'To set emails for WooCommerce default status, use WooCommerce Panel in ', 'yith-woocommerce-custom-order-status' ) . '<a href="admin.php?page=wc-settings&tab=email">' . __( 'WooCommerce -> Settings -> Emails', 'yith-woocommerce-custom-order-status' ) . '</a>',
				'type'    => 'simple-text',
				'private' => false,
			),
		),
	),
	'mail_settings' => array(
		'label'  => __( 'Email Settings', 'yith-woocommerce-custom-order-status' ),
		'fields' => array(
			'recipients'          => array(
				'label'    => __( 'Recipients', 'yith-woocommerce-custom-order-status' ),
				'desc'     => __( 'Choose recipients of email notifications for this status', 'yith-woocommerce-custom-order-status' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'multiple' => true,
				'options'  => yith_wccos_get_allowed_recipients(),
				'private'  => false,
			),
			'custom_recipient'    => array(
				'label'   => __( 'Recipient Email Address', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Type here the email address to notify when the selected status is selected', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => '',
			),
			'mail_name_from'      => array(
				'label'   => __( '"From" Name', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Enter the email sender name which will appear to recipients', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => get_bloginfo( 'name' ),
			),
			'mail_from'           => array(
				'label'   => __( '"From" Email Address', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Enter the email address which will appear to recipients', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => get_option( 'admin_email' ),
			),
			'mail_subject'        => array(
				'label'   => __( 'Email Subject', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Enter the email subject which will appear to recipients of the email', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => '',
			),
			'mail_heading'        => array(
				'label'   => __( 'Email Heading', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Enter the heading you want to appear in the email sent', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'text',
				'private' => false,
				'std'     => '',
			),
			'mail_custom_message' => array(
				'label'   => __( 'Custom Message', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Available Shortcodes: {customer_first_name} , {customer_last_name} , {order_date} , {order_number} , {order_value} , {billing_address} , {shipping_address}', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'textarea',
				'private' => false,
				'std'     => '',
			),
			'mail_order_info'     => array(
				'label'   => __( 'Include Order Information', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Select whether you want to include order information (billing and shipping address, order items, total, etc)', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
				'std'     => '',
			),

		),
	),
);
