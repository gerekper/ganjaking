<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
}

$settings = array(
	'general' => array(

		// >>>>>>>>>>>>>>>>> General Settings.

		'section_general_settings'                        => array(
			'name' => esc_html__( 'General settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_general',
		),

		'enable_subscriptions_multiple'                   => array(
			'name'      => esc_html__( 'User can add to cart', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose if a user can add only one or more subscription products to cart.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_subscriptions_multiple',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no'  => esc_html__( 'Only one subscription product', 'yith-woocommerce-subscription' ),
				'yes' => esc_html__( 'Unlimited subscription products', 'yith-woocommerce-subscription' ),
			),
			'default'   => 'yes',
		),

		'enable_manual_renews_gateways'                   => array(
			'name'      => esc_html__( 'Allow user to manually renew a subscription', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose whether a user can renew a subscription if the payment gateway does not support automatic payments.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_manual_renews',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'yes' => esc_html__( 'Yes, the customer will be able to pay the renewal order on My Account page, if the payment gateway does not support automatic payments.', 'yith-woocommerce-subscription' ),
				'no'  => esc_html__( 'No, enable the payment gateways only to support automatic payments.', 'yith-woocommerce-subscription' ),
			),
			'default'   => 'yes',
		),

		'disable_the_reduction_of_order_stock_in_renew'   => array(
			'name'      => esc_html__( 'Stock management with recurring payments', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose if the recurring payments will reduce the stock count of a subscription product.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_disable_the_reduction_of_order_stock_in_renew',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no'  => esc_html__( 'Reduce stock of subscription products', 'yith-woocommerce-subscription' ),
				'yes' => esc_html__( 'Do not reduce stock of subscription products', 'yith-woocommerce-subscription' ),
			),
			'default'   => 'no',
		),

		'change_status_after_renew_order_creation'        => array(
			'name'      => esc_html__( ' If a recurring payment is not paid', 'yith-woocommerce-subscription' ),
			'desc'      => sprintf( '<div class="hide-overdue">%s</div>', esc_html__( 'Choose how to manage the subscription when a recurring payment is not paid.', 'yith-woocommerce-subscription' ) ),
			'class'     => 'renew_order_step1',
			'id'        => 'ywsbs_change_status_after_renew_order_creation',
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'html0'    => array(
					'type' => 'html',
					'html' => esc_html__( 'after', 'yith-woocommerce-subscription' ),
				),
				'wait_for' => array(
					'type'              => 'number',
					'std'               => 48,
					'custom_attributes' => 'style="width:40px"',
				),
				'html'     => array(
					'type' => 'html',
					'html' => esc_html__( 'hours put the subscription in status', 'yith-woocommerce-subscription' ),
				),
				'status'   => array(
					'type'              => 'select',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => array(
						'overdue'   => esc_html__( 'Overdue', 'yith-woocommerce-subscription' ),
						'suspended' => esc_html__( 'Suspended', 'yith-woocommerce-subscription' ),
						'cancelled' => esc_html__( 'Cancelled', 'yith-woocommerce-subscription' ),
					),
					'std'               => 'suspended',
				),
				'html2'    => array(
					'type'  => 'html',
					'class' => 'show-if-overdue show-if-suspended',
					'html'  => esc_html__( 'for', 'yith-woocommerce-subscription' ),
				),

				'length'   => array(
					'type'              => 'number',
					'class'             => 'show-if-overdue show-if-suspended ',
					'std'               => 20,
					'custom_attributes' => 'style="width:40px"',
				),

				'html3'    => array(
					'type'  => 'html',
					'class' => 'show-if-overdue',
					'html'  => esc_html__( 'days.', 'yith-woocommerce-subscription' ),
				),

				'html4'    => array(
					'type'  => 'html',
					'class' => 'show-if-suspended',
					'html'  => esc_html__( 'days before cancelling the subscription.', 'yith-woocommerce-subscription' ),
				),
			),
		),

		'change_status_after_renew_order_creation_step_2' => array(
			'name'      => '',
			'id'        => 'ywsbs_change_status_after_renew_order_creation_step_2',
			'desc'      => esc_html__( 'Choose how to manage the subscription when a recurring payment is not paid.', 'yith-woocommerce-subscription' ),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'class'     => 'without-padding',
			'fields'    => array(
				'html'   => array(
					'type' => 'html',
					'html' => esc_html__( 'After that, put it as', 'yith-woocommerce-subscription' ),
				),
				'status' => array(
					'type'              => 'select',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => array(
						'suspended' => esc_html__( 'Suspended', 'yith-woocommerce-subscription' ),
						'cancelled' => esc_html__( 'Cancelled', 'yith-woocommerce-subscription' ),
					),
				),
				'html2'  => array(
					'type'  => 'html',
					'class' => 'show-if-no-cancelled-step-2',
					'html'  => esc_html__( 'for', 'yith-woocommerce-subscription' ),
				),

				'length' => array(
					'type'              => 'number',
					'std'               => 15,
					'class'             => 'show-if-no-cancelled-step-2',
					'custom_attributes' => 'style="width:40px"',
				),

				'html3'  => array(
					'type'  => 'html',
					'class' => 'show-if-no-cancelled-step-2',
					'html'  => esc_html__( 'days before to cancel the subscription.', 'yith-woocommerce-subscription' ),
				),
			),
			'deps'      => array(
				'id'    => 'ywsbs_change_status_after_renew_order_creation_status',
				'value' => 'overdue',
			),
		),

		'allow_users_to_pause_subscriptions'              => array(
			'name'      => esc_html__( 'Allow users to pause subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose if a user can pause a subscription, and if so, to do so with or without limits.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_allow_users_to_pause_subscriptions',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no'      => esc_html__( 'No, never', 'yith-woocommerce-subscription' ),
				'yes'     => esc_html__( 'Yes, user can pause without limits', 'yith-woocommerce-subscription' ),
				'limited' => esc_html__( 'Yes, user can pause with certain limits', 'yith-woocommerce-subscription' ),
			),
			'default'   => 'no',
		),

		'max_pause'                                       => array(
			'name'      => esc_html__( 'Subscription pausing limits', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_max_pause',
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'class'     => 'without-bottom-padding',
			'fields'    => array(
				'html'  => array(
					'type' => 'html',
					'html' => esc_html__( 'The user can pause a subscription a maximum of', 'yith-woocommerce-subscription' ),
				),
				'value' => array(
					'type'              => 'number',
					'std'               => 2,
					'custom_attributes' => 'style="width:40px"',
				),
				'html2' => array(
					'type' => 'html',
					'html' => esc_html__( 'times;', 'yith-woocommerce-subscription' ),
				),
			),
			'deps'      => array(
				'id'    => 'ywsbs_allow_users_to_pause_subscriptions',
				'value' => 'limited',
			),
		),

		'max_pause_duration'                              => array(
			'name'      => '',
			'id'        => 'ywsbs_max_pause_duration',
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'class'     => 'without-padding',
			'fields'    => array(
				'html'  => array(
					'type' => 'html',
					'html' => esc_html__( 'Each pause can last a maximum of', 'yith-woocommerce-subscription' ),
				),
				'value' => array(
					'type'              => 'number',
					'std'               => 30,
					'custom_attributes' => 'style="width:40px"',
				),
				'html2' => array(
					'type' => 'html',
					'html' => esc_html__( 'days. After which, the subscription will reactivate automatically.', 'yith-woocommerce-subscription' ),
				),
			),
			'deps'      => array(
				'id'    => 'ywsbs_allow_users_to_pause_subscriptions',
				'value' => 'limited',
			),
		),

		'delete_subscription_order_cancelled'             => array(
			'name'      => esc_html__( 'Delete subscription if the main order is cancelled', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable if you want to delete a subscription when the main order is cancelled.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_delete_subscription_order_cancelled',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'section_end_form'                                => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_general_end_form',
		),

		// >>>>>>>>>>>>>>>>> Subscription Renewal Synchronization.

		'section_synch_settings'                          => array(
			'name' => esc_html__( 'Subscription Renewal Synchronization', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_synch_general',
		),

		'enable_sync'                                     => array(
			'name'      => esc_html_x( 'Recurring payment synchronization options for:', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html_x( 'Choose if you want to synchronize subscription payments for any or specific products or categories, to a specific day of the week, month, or year. For example, each Monday or the first day of each month. You can do that for all products or for specific product/categories.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_sync',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no'           => esc_html_x( 'None', 'Admin option choice', 'yith-woocommerce-subscription' ),
				// translators:placeholders are html tags.
				'all_products' => sprintf( esc_html_x( 'All products %1$sYou will be able to exclude some products or categories if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				// translators:placeholders are html tags.
				'virtual'      => sprintf( esc_html_x( 'Only virtual products %1$sYou will be able to exclude some products or categories if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				'products'     => esc_html_x( 'Specific products', 'Admin option choice', 'yith-woocommerce-subscription' ),
				// translators:placeholders are html tags.
				'categories'   => sprintf( esc_html_x( 'Specific categories %1$sYou will be able to exclude some products if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
			),
			'default'   => 'no',
		),

		'sync_exclude_category_and_product'               => array(
			'name'              => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to exclude products.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_category_and_product',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'yes',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync',
				'data-deps_value' => 'all_products',
			),

		),

		// All products. Exclude categories.
		'sync_exclude_categories_all_products'            => array(
			'name'              => esc_html_x( 'Categories to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the categories to exclude from recurring payments synchronization.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_categories_all_products',
			'type'              => 'yith-field',
			'yith-type'         => 'show-categories',
			'placeholder'       => __( 'Search category to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_exclude_category_and_product',
				'data-deps_value' => 'all_products,yes',
			),
		),

		// All products. Exclude products.
		'sync_exclude_products_all_products'              => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from recurring payments synchronization.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_products_all_products',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_exclude_category_and_product',
				'data-deps_value' => 'all_products,yes',
			),
		),

		// Virtual products. Exclude categories.
		'sync_exclude_category_and_product_virtual'       => array(
			'name'              => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to exclude products.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_category_and_product_virtual',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'yes',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync',
				'data-deps_value' => 'virtual',
			),

		),

		// Virtual products. Exclude categories.
		'sync_exclude_categories_virtual'                 => array(
			'name'              => esc_html_x( 'Categories to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from recurring payments synchronization.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_categories_virtual',
			'type'              => 'yith-field',
			'yith-type'         => 'show-categories',
			'placeholder'       => __( 'Search category to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_exclude_category_and_product_virtual',
				'data-deps_value' => 'virtual,yes',
			),
		),

		// Virtual products. Exclude products.
		'sync_exclude_products_virtual'                   => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from recurring payments synchronization.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_products_virtual',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_exclude_category_and_product_virtual',
				'data-deps_value' => 'virtual,yes',
			),
		),

		// Products.
		'sync_include_product'                            => array(
			'name'              => esc_html_x( 'Products to include', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products that allow a specific renewal date to be set for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_include_product',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to include', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync',
				'data-deps_value' => 'products',
			),
		),

		// Categories.
		'sync_include_categories'                         => array(
			'name'        => esc_html_x( 'Categories to include', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'        => esc_html_x( 'Choose the categories that allow a specific renewal date to be set for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_sync_include_categories',
			'type'        => 'yith-field',
			'yith-type'   => 'show-categories',
			'placeholder' => __( 'Search category to include', 'yith-woocommerce-subscription' ),
			'deps'        => array(
				'id'     => 'ywsbs_enable_sync',
				'values' => 'categories',
			),
		),

		'sync_include_categories_enable_exclude_products' => array(
			'name'      => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable if you want to exclude products from the category list.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_sync_include_categories_enable_exclude_products',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'deps'      => array(
				'id'     => 'ywsbs_enable_sync',
				'values' => 'categories',
			),

		),

		'sync_exclude_products_from_categories'           => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from recurring payments synchronization.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_exclude_products_from_categories',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search Product to exclude', 'yith-woocommerce-subscription' ),
			'default'           => array(),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_include_categories_enable_exclude_products',
				'data-deps_value' => 'categories,yes',
			),
		),

		'sync_first_payment'                              => array(
			'name'              => esc_html_x( 'First payment at sign-up options', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose how to manage the first recurring payment at signup of the subscription products that have a synchronized renewal day set.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_first_payment',
			'type'              => 'yith-field',
			'yith-type'         => 'radio',
			'options'           => array(
				// translators:placeholders are html tags.
				'no'      => sprintf( esc_html_x( 'Don\'t charge the first recurring amount at sign-up. (only sign-up fee, if this is set) %1$sWhen you create a subscription product, you can choose on which day to synchronize renewals and charge the subscription payment to your users.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				// translators:placeholders are html tags.
				'prorate' => sprintf( esc_html_x( 'Charge a prorated payment and therefore when to charge the first recurring amount to your users.%1$sThe user will pay a part of the recurring amount, calculated automatically on the basis of the days left till the renewal. (Renewal day is set in the subscription product page)%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				'full'    => esc_html_x( 'Charge the full recurring amount on signup', 'Admin option choice', 'yith-woocommerce-subscription' ),
			),
			'default'           => 'no',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync',
				'data-deps_value' => 'all_products|virtual|products|categories',
			),
		),

		'sync_prorate_disabled'                           => array(
			'name'              => esc_html_x( 'Postpone the first payment, if the next payment is in less than:', 'Admin option title', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Use this option to avoid charging the user twice in quick succession if a subscription has been bought near a renewal date.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_prorate_disabled',
			'type'              => 'yith-field',
			'yith-type'         => 'inline-fields',
			'fields'            => array(
				'number_of_days' => array(
					'type'              => 'number',
					'std'               => 30,
					'custom_attributes' => 'style="width:40px"',
				),
				'html'           => array(
					'type' => 'html',
					'html' => esc_html_x( 'days until the next renewal.', 'Admin option description', 'yith-woocommerce-subscription' ),
				),
			),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_first_payment',
				'data-deps_value' => 'all_products|virtual|products|categories,prorate',
			),
		),

		'sync_show_product_info'                          => array(
			'name'              => esc_html__( 'Display recurring payments info on the product page', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to show the information about the recurring payments to the customer on the product page.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_sync_show_product_info',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'yes',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_sync,ywsbs_sync_first_payment',
				'data-deps_value' => 'all_products|virtual|products|categories,no|prorate',
			),
		),

		'section_synch_settings_end'                      => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_synch_settings_end',
		),

		// >>>>>>>>>>>>>>>>> Delivery Schedule.

		'section_delivery_settings'                       => array(
			'name' => esc_html__( 'Subscription Delivery Schedules', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_delivery_general',
		),

		'enable_delivery'                                 => array(
			'name'      => esc_html_x( 'Set a delivery schedule of subscription products for:', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html_x( 'Choose if you need to set a delivery schedule for all products, only for non-virtual products or for specific categories or products', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_delivery',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'no'           => esc_html_x( 'None - No delivery schedule needed', 'Admin option choice', 'yith-woocommerce-subscription' ),
				// translators:placeholders are html tags.
				'all_products' => sprintf( esc_html_x( 'All products %1$sYou will be able to exclude some products or categories if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				// translators:placeholders are html tags.
				'physical'     => sprintf( esc_html_x( 'Only non-virtual products %1$sYou will be able to exclude some products or categories if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
				'products'     => esc_html_x( 'Specific products', 'Admin option choice', 'yith-woocommerce-subscription' ),
				// translators:placeholders are html tags.
				'categories'   => sprintf( esc_html_x( 'Specific categories %1$sYou will be able to exclude some products if this option is selected.%2$s', 'Admin option, the placeholder are tags', 'yith-woocommerce-subscription' ), '<small>', '</small>' ),
			),
			'default'   => 'no',
		),

		'delivery_exclude_category_and_product'           => array(
			'name'              => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to exclude products.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_category_and_product',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'no',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery',
				'data-deps_value' => 'all_products',
			),
		),

		// All products. Exclude categories.
		'delivery_exclude_categories_all_products'        => array(
			'name'              => esc_html_x( 'Categories to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the categories to exclude from delivery schedule for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_categories_all_products',
			'type'              => 'yith-field',
			'yith-type'         => 'show-categories',
			'placeholder'       => __( 'Search category to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_exclude_category_and_product',
				'data-deps_value' => 'all_products,yes',
			),
		),

		// All products. Exclude products.
		'delivery_exclude_products_all_products'          => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from delivery schedule for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_products_all_products',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_exclude_category_and_product',
				'data-deps_value' => 'all_products,yes',
			),
		),

		// Physical products. Enable exclude products.
		'delivery_exclude_category_and_product_non_virtual' => array(
			'name'              => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to exclude products.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_category_and_product_non_virtual',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'no',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery',
				'data-deps_value' => 'physical',
			),
		),

		// Physical products. Exclude categories.
		'delivery_exclude_categories_physical'            => array(
			'name'              => esc_html_x( 'Categories to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the categories to exclude from delivery schedule for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_categories_physical',
			'type'              => 'yith-field',
			'yith-type'         => 'show-categories',
			'placeholder'       => __( 'Search category to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_exclude_category_and_product_non_virtual',
				'data-deps_value' => 'physical,yes',
			),
		),

		// Physical products. Exclude products.
		'delivery_exclude_products_physical'              => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from delivery schedule for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_products_physical',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to exclude', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_exclude_category_and_product_non_virtual',
				'data-deps_value' => 'physical,yes',
			),
		),


		// Products.
		'delivery_include_product'                        => array(
			'name'              => esc_html_x( 'Products to include', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products that allow a specific delivery schedule to be set.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_include_product',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search product to include', 'yith-woocommerce-subscription' ),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery',
				'data-deps_value' => 'products',
			),
		),

		// Categories.
		'delivery_include_categories'                     => array(
			'name'        => esc_html_x( 'Categories to include', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'        => esc_html_x( 'Choose the categories that allow a specific delivery schedule to be set.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'          => 'ywsbs_delivery_include_categories',
			'type'        => 'yith-field',
			'yith-type'   => 'show-categories',
			'placeholder' => __( 'Search category to include', 'yith-woocommerce-subscription' ),
			'deps'        => array(
				'id'     => 'ywsbs_enable_delivery',
				'values' => 'categories',
			),
		),

		'delivery_include_categories_enable_exclude_products' => array(
			'name'      => esc_html__( 'Exclude products', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable if you want to exclude products from the category list.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_delivery_include_categories_enable_exclude_products',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'deps'      => array(
				'id'     => 'ywsbs_enable_delivery',
				'values' => 'categories',
			),

		),

		'delivery_exclude_products_from_categories'       => array(
			'name'              => esc_html_x( 'Products to exclude', 'Admin option', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html_x( 'Choose the products to exclude from delivery schedule for recurring payments.', 'Admin option description', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_exclude_products_from_categories',
			'type'              => 'yith-field',
			'yith-type'         => 'ywsbs-products',
			'placeholder'       => __( 'Search Product to exclude', 'yith-woocommerce-subscription' ),
			'default'           => array(),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_include_categories_enable_exclude_products',
				'data-deps_value' => 'categories,yes',
			),
		),

		'delivery_default_schedule'                       => array(
			'name'              => esc_html__( 'Default delivery schedule', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Set a default delivery schedule. You can override this option and set a different schedule inside the product page.', 'yith-woocommerce-subscription' ),
			'class'             => 'default_schedule1',
			'id'                => 'ywsbs_delivery_default_schedule',
			'type'              => 'yith-field',
			'yith-type'         => 'inline-fields',
			'fields'            => array(
				'html0'           => array(
					'type' => 'html',
					'html' => esc_html_x( 'Deliver the subscription products every', 'Part of an option text', 'yith-woocommerce-subscription' ),
				),
				'delivery_gap'    => array(
					'type'              => 'number',
					'std'               => 1,
					'custom_attributes' => 'style="width:40px"',
				),
				'delivery_period' => array(
					'type'              => 'select',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => array(
						'days'   => esc_html__( 'Days', 'yith-woocommerce-subscription' ),
						'weeks'  => esc_html__( 'Weeks', 'yith-woocommerce-subscription' ),
						'months' => esc_html__( 'Months', 'yith-woocommerce-subscription' ),
						'years'  => esc_html__( 'Years', 'yith-woocommerce-subscription' ),
					),
					'std'               => 'months',
				),
			),
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery',
				'data-deps_value' => 'categories|products|physical|all_products',
			),
		),
		'delivery_sync_delivery_schedulesd'               => array(
			'name'              => esc_html__( 'Synchronize delivery schedules', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to ship the product on a specific day.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_sync_delivery_schedules',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'no',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_default_schedule[delivery_period]',
				'data-deps_value' => 'categories|products|physical|all_products,weeks|months|years',
			),
		),
		'delivery_default_schedule_sync'                  => array(
			'name'              => esc_html__( 'Synchronize delivery on', 'yith-woocommerce-subscription' ),
			'desc'              => sprintf( '<div class="hide-if-days">%s</div>', esc_html__( 'Set a default delivery schedule synchronization. You can override this option inside the product page.', 'yith-woocommerce-subscription' ) ),
			'class'             => 'without-padding',
			'id'                => 'ywsbs_delivery_default_schedule2',
			'type'              => 'yith-field',
			'yith-type'         => 'inline-fields',
			'fields'            => array(

				'sych_weeks'                => array(
					'type'              => 'select',
					'class'             => 'short-field show-if-weeks',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => ywsbs_get_period_options( 'day_weeks' ),
					'std'               => 'suspended',
				),

				'months'                    => array(
					'type'              => 'select',
					'class'             => 'short-field show-if-months',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => ywsbs_get_period_options( 'day_months' ),
					'std'               => 'suspended',
				),

				'delivery_sych_months_text' => array(
					'type'  => 'html',
					'class' => 'show-if-months',
					'html'  => esc_html_x( 'of each month', 'Part of an option text', 'yith-woocommerce-subscription' ),
				),

				'years_day'                 => array(
					'type'              => 'select',
					'class'             => 'short-field show-if-years',
					'custom_attributes' => 'style="width: 100px!important;"',
					'options'           => ywsbs_get_period_options( 'day_months' ),
					'std'               => 'suspended',
				),

				'years_month'               => array(
					'type'              => 'select',
					'class'             => 'short-field show-if-years',
					'custom_attributes' => 'style="width: 100px!important;"',
					'options'           => ywsbs_get_period_options( 'months' ),
					'std'               => 'suspended',
				),



			),

			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery,ywsbs_delivery_sync_delivery_schedules',
				'data-deps_value' => 'categories|products|physical|all_products,yes',
			),
		),

		'delivery_show_product_info'                      => array(
			'name'              => esc_html__( 'Show delivery schedule info in product page', 'yith-woocommerce-subscription' ),
			'desc'              => esc_html__( 'Enable if you want to show information about the delivery schedule on the product page.', 'yith-woocommerce-subscription' ),
			'id'                => 'ywsbs_delivery_show_product_info',
			'type'              => 'yith-field',
			'yith-type'         => 'onoff',
			'default'           => 'yes',
			'custom_attributes' => array(
				'data-deps'       => 'ywsbs_enable_delivery',
				'data-deps_value' => 'all_products|virtual|products|categories',
			),
		),

		'section_delivery_settings_end'                   => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_delivery_settings_end',
		),

		// >>>>>>>>>>>>>>>>> Extra settings.

		'section_extra_settings'                          => array(
			'name' => esc_html__( 'Extra settings', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_section_extra',
		),

		'enable_shop_manager'                             => array(
			'name'      => esc_html__( 'Shop manager can manage subscription settings', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable to allow the shop manager to access and edit the plugin options.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_shop_manager',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'site_staging'                                    => array(
			'name'      => esc_html__( 'Staging mode', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable if you want to use this installation as a test site and avoid generating duplicate orders.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_site_staging',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),


		'enable_log'                                      => array(
			'name'      => esc_html__( 'Enable Log', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Enable to generate a list of plugin actions. Note: this is a useful option for development improvements and to provide support..', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_enable_log',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'section_extra_end_form'                          => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_extra_end_form',
		),

		// >>>>>>>>>>>>>>>>> GDPR

		'privacy_settings'                                => array(
			'name' => esc_html__( 'GPDR & Privacy', 'yith-woocommerce-subscription' ),
			'type' => 'title',
			'id'   => 'ywsbs_privacy_settings',
		),

		'erasure_request'                                 => array(
			'name'      => esc_html__( 'Delete personal info after an account erasure requests', 'yith-woocommerce-subscription' ),
			'desc'      => sprintf( '%s <br> %s', esc_html__( 'Enable to erase the personal information of a subscription if an account erasure request is made.', 'yith-woocommerce-subscription' ), esc_html__( 'Note: all affected subscription status\' will be changed to \'cancelled\'.', 'yith-woocommerce-subscription' ) ),
			'id'        => 'ywsbs_erasure_request',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'delete_unused_subscription'                      => array(
			'name'      => esc_html__( 'Delete pending and cancelled subscriptions', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose if pending and/or cancelled subscriptions can be trashed after the specified duration.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_delete_personal_info',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'trash_pending_subscriptions'                     => array(
			'title'     => esc_html__( 'Delete pending subscriptions after', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose when to delete pending subscriptions.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_trash_pending_subscriptions',
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'number' => array(
					'type'              => 'number',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width:100px"',
				),
				'unit'   => array(
					'type'              => 'select',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => array(
						'days'   => esc_html__( 'days', 'yith-woocommerce-subscription' ),
						'weeks'  => esc_html__( 'weeks', 'yith-woocommerce-subscription' ),
						'months' => esc_html__( 'months', 'yith-woocommerce-subscription' ),
						'years'  => esc_html__( 'years', 'yith-woocommerce-subscription' ),
					),
				),
			),
			'deps'      => array(
				'id'    => 'ywsbs_delete_personal_info',
				'value' => 'yes',
			),
		),

		'trash_cancelled_subscriptions'                   => array(
			'title'     => esc_html__( 'Delete cancelled subscriptions after', 'yith-woocommerce-subscription' ),
			'desc'      => esc_html__( 'Choose when to delete cancelled subscriptions.', 'yith-woocommerce-subscription' ),
			'id'        => 'ywsbs_trash_cancelled_subscriptions',
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'number' => array(
					'type'              => 'number',
					'custom_attributes' => 'style="width:100px"',
				),
				'unit'   => array(
					'type'              => 'select',
					'class'             => 'short-field',
					'custom_attributes' => 'style="width: 150px!important;"',
					'options'           => array(
						'days'   => esc_html__( 'days', 'yith-woocommerce-subscription' ),
						'weeks'  => esc_html__( 'weeks', 'yith-woocommerce-subscription' ),
						'months' => esc_html__( 'months', 'yith-woocommerce-subscription' ),
						'years'  => esc_html__( 'years', 'yith-woocommerce-subscription' ),
					),
				),
			),
			'deps'      => array(
				'id'    => 'ywsbs_delete_personal_info',
				'value' => 'yes',
			),
		),

		'section_end_privacy_settings'                    => array(
			'type' => 'sectionend',
			'id'   => 'ywsbs_section_end_privacy_settings',
		),
	),
);

return apply_filters( 'yith_ywsbs_panel_settings_options', $settings );
