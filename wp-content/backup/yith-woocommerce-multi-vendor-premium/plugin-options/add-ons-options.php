<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
return array(

	'add-ons' => apply_filters( 'yith_wcmv_add_ons_options', array(
			'vendors_seller_vacation_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_seller_vacation_title' => array(
				'title' => __( 'Seller vacation', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'yith_wpv_vendors_seller_vacation_title',
			),

			'vendors_seller_vacation_management' => array(
				'title'   => __( 'Enable seller vacation module', 'yith-woocommerce-product-vendors' ),
				'type'    => 'checkbox',
				'desc'    => __( 'If you enable this option, each vendor will be able to close his/her shop for vacation.', 'yith-woocommerce-product-vendors' ),
				'id'      => 'yith_wpv_vendors_option_seller_vacation_management',
				'default' => 'no',
			),

			'vendors_seller_vacation_end' => array(
				'type' => 'sectionend',
			),

			'vendors_shipping_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_shipping_title' => array(
				'title' => __( 'Shipping', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'yith_wpv_vendors_shipping_title',
			),

			'vendors_shipping_management' => array(
				'title'   => __( 'Enable shipping module', 'yith-woocommerce-product-vendors' ),
				'type'    => 'checkbox',
				'desc'    => __( 'If you enable this option, each vendor will be able to set a self cost for the vendor shipping method', 'yith-woocommerce-product-vendors' ),
				'id'      => 'yith_wpv_vendors_option_shipping_management',
				'default' => 'no',
			),

			'vendors_shipping_end' => array(
				'type' => 'sectionend',
			),

			'vendors_live_chat_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_live_chat_title' => array(
				'title' => __( 'Live Chat', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'live-chat', 'display' ),
				'id'    => 'yith_wpv_vendors_live_chat_title',
			),

			'vendors_enable_chat' => array(
				'title'             => __( 'Enable live chat for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'live-chat' ),
				'id'                => 'yith_wpv_vendors_option_live_chat_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'live-chat' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_live_chat_end' => array(
				'type' => 'sectionend',
			),

			'vendors_membership_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_membership_title' => array(
				'title' => __( 'Membership', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'membership', 'display' ),
				'id'    => 'yith_wpv_vendors_membership_title',
			),

			'vendors_enable_membership' => array(
				'title'             => __( 'Enable membership for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'membership' ),
				'id'                => 'yith_wpv_vendors_option_membership_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'membership' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_membership_end' => array(
				'type' => 'sectionend',
			),

			'vendors_subscription_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_subscription_title' => array(
				'title' => __( 'Subscription', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'subscription', 'display' ),
				'id'    => 'yith_wpv_vendors_subscription_title',
			),

			'vendors_enable_subscription' => array(
				'title'             => __( 'Enable subscription for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'subscription' ),
				'id'                => 'yith_wpv_vendors_option_subscription_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'subscription' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_subscription_end' => array(
				'type' => 'sectionend',
			),

			'vendors_badge_management_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_badge_management_title' => array(
				'title' => __( 'Badge Management', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'badge-management', 'display' ),
				'id'    => 'yith_wpv_vendors_badge_management_title',
			),

			'vendors_enable_badge_management' => array(
				'title'             => __( 'Enable badge management for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'badge-management' ),
				'id'                => 'yith_wpv_vendors_option_badge_management_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'badge-management' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_badge_management_end' => array(
				'type' => 'sectionend',
			),

			'vendors_size_charts_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_size_charts_title' => array(
				'title' => __( 'Product Size Charts', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'size-charts', 'display' ),
				'id'    => 'yith_wpv_vendors_size_charts_title',
			),

			'vendors_enable_size_charts' => array(
				'title'             => __( 'Enable product size charts for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'size-charts' ),
				'id'                => 'yith_wpv_vendors_option_size_charts_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'size-charts' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_size_charts_end' => array(
				'type' => 'sectionend',
			),

			'vendors_name_your_price_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_name_your_price_title' => array(
				'title' => __( 'Name Your Price', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'name-your-price', 'display' ),
				'id'    => 'yith_wpv_vendors_name_your_price_title',
			),

			'vendors_enable_name_your_price' => array(
				'title'             => __( 'Enable Name Your Price for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'name-your-price' ),
				'id'                => 'yith_wpv_vendors_option_name_your_price_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'name-your-price' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_name_your_price_end' => array(
				'type' => 'sectionend',
			),

			'vendors_order_tracking_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_order_tracking_title' => array(
				'title' => __( 'Order Tracking', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'order-tracking', 'display' ),
				'id'    => 'yith_wpv_vendors_order_tracking_title',
			),

			'vendors_enable_order_tracking' => array(
				'title'             => __( 'Enable Order Tracking for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'order-tracking' ),
				'id'                => 'yith_wpv_vendors_option_order_tracking_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'order-tracking' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_order_tracking_end' => array(
				'type' => 'sectionend',
			),

			'vendors_waiting_list_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_waiting_list_title' => array(
				'title' => __( 'Waiting List', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'waiting-list', 'display' ),
				'id'    => 'yith_wpv_vendors_waiting_list_title',
			),

			'vendors_enable_waiting_list' => array(
				'title'             => __( 'Enable Waiting List for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'waiting-list' ),
				'id'                => 'yith_wpv_vendors_option_waiting_list_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'waiting-list' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_waiting_list_end' => array(
				'type' => 'sectionend',
			),

			'vendors_surveys_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_surveys_title' => array(
				'title' => __( 'Surveys', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'surveys', 'display' ),
				'id'    => 'yith_wpv_vendors_surveys_title',
			),

			'vendors_enable_surveys' => array(
				'title'             => __( 'Enable surveys for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'surveys' ),
				'id'                => 'yith_wpv_vendors_option_surveys_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'surveys' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_surveys_end' => array(
				'type' => 'sectionend',
			),

			'vendors_review_discounts_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_review_discounts_title' => array(
				'title' => __( 'Review For Discounts', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'review-discounts', 'display' ),
				'id'    => 'yith_wpv_vendors_review_discounts_title',
			),

			'vendors_enable_review_discounts' => array(
				'title'             => __( 'Enable Review for Discounts for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'review-discounts' ),
				'id'                => 'yith_wpv_vendors_option_review_discounts_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'review-discounts' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_review_discounts_end' => array(
				'type' => 'sectionend',
			),

			'vendors_coupon_email_system_start' => array(
				'type' => 'sectionstart',
			),

			'vendors_coupon_email_system_title' => array(
				'title' => __( 'Coupon Email System', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'coupon-email-system', 'display' ),
				'id'    => 'yith_wpv_vendors_coupon_email_system_title',
			),

			'vendors_enable_coupon_email_system' => array(
				'title'             => __( 'Coupon Email System for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'yith_premium_addons',
				'desc'              => sprintf( '%s:', __( 'You can manage this features here', 'yith-woocommerce-product-vendors' ) ),
				'settings_tab'      => array(
					'uri'         => add_query_arg( array(
						'page' => 'yith-wc-coupon-email-system',
						'tab'  => 'admin-vendor'
					), admin_url( 'admin.php' ) ),
					'desc'        => __( 'Vendor Settings', 'yith-woocommerce-product-vendors' ),
					'plugin_name' => __( 'Coupon Email System', 'yith-woocommerce-product-vendors' ),
				),
				'id'                => 'yith_wpv_vendors_option_coupon_email_system_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'coupon-email-system' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_coupon_email_system_end' => array(
				'type' => 'sectionend',
			),

			'vendors_pdf_invoice' => array(
				'type' => 'sectionstart',
			),

			'vendors_pdf_invoice_title' => array(
				'title' => __( 'PDF Invoice', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'pdf-invoice', 'display' ),
				'id'    => 'yith_wpv_vendors_pdf_invoice_title',
			),

			'vendors_enable_pdf_invoice' => array(
				'title'             => __( 'Enable PDF Invoice for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'pdf-invoice' ),
				'id'                => 'yith_wpv_vendors_enable_pdf_invoice',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'pdf-invoice' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_pdf_invoice_end' => array(
				'type' => 'sectionend',
			),

			'vendors_request_quote' => array(
				'type' => 'sectionstart',
			),

			'vendors_request_quote_title' => array(
				'title' => __( 'Request a quote', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'request-quote', 'display' ),
				'id'    => 'yith_wpv_vendors_request_quote_title',
			),

			'vendors_enable_request_quote' => array(
				'title'             => __( 'Enable Request a quote for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'request-quote' ),
				'id'                => 'yith_wpv_vendors_enable_request_quote',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'request-quote' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_request_quote_end' => array(
				'type' => 'sectionend',
			),

			'vendors_catalog_mode' => array(
				'type' => 'sectionstart',
			),

			'vendors_catalog_mode_title' => array(
				'title' => __( 'Catalog Mode', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'catalog-mode', 'display' ),
				'id'    => 'yith_wpv_vendors_catalog_mode_title',
			),

			'vendors_enable_catalog_mode' => array(
				'title'             => __( 'Enable Catalog Mode for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'catalog-mode' ),
				'id'                => 'yith_wpv_vendors_enable_catalog_mode',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'catalog-mode' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_catalog_mode_end' => array(
				'type' => 'sectionend',
			),

			'vendors_role_based_prices' => array(
				'type' => 'sectionstart',
			),

			'vendors_role_based_prices_title' => array(
				'title' => __( 'Role Based Prices', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'role-based-prices', 'display' ),
				'id'    => 'yith_wpv_vendors_role_based_prices_title',
			),

			'vendors_role_based_prices_mode' => array(
				'title'             => __( 'Enable Role Based Prices for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'role-based-prices' ),
				'id'                => 'yith_wpv_vendors_option_role_based_prices_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'role-based-prices' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_role_based_prices_end' => array(
				'type' => 'sectionend',
			),

			'vendors_advanced_product_options' => array(
				'type' => 'sectionstart',
			),

			'vendors_advanced_product_options_title' => array(
				'title' => __( 'Product Add-ons', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'advanced-product-options', 'display' ),
				'id'    => 'yith_wpv_vendors_advanced_product_options_title',
			),

			'vendors_advanced_product_options_mode' => array(
				'title'             => __( 'Enable Product Add-ons Options for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'advanced-product-options' ),
				'id'                => 'yith_wpv_vendors_option_advanced_product_options_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'advanced-product-options' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_advanced_product_options_end' => array(
				'type' => 'sectionend',
			),

			'vendors_sms_notifications' => array(
				'type' => 'sectionstart',
			),

			'vendors_sms_notifications_title' => array(
				'title' => __( 'SMS Notifications', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'sms-notifications', 'display' ),
				'id'    => 'yith_wpv_vendors_sms_notifications_title',
			),

			'vendors_sms_notifications_mode' => array(
				'title'             => __( 'Enable SMS Notifications for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'sms-notifications' ),
				'id'                => 'yith_wpv_vendors_enable_sms',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'sms-notifications' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_sms_notifications_end' => array(
				'type' => 'sectionend',
			),

			'vendors_bulk_product_editing_options' => array(
				'type' => 'sectionstart',
			),

			'vendors_bulk_product_editing_options_title' => array(
				'title' => __( 'Bulk Product Editing', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'bulk-product-editing', 'display' ),
				'id'    => 'yith_wpv_vendors_bulk_product_editing_title',
			),

			'vendors_bulk_product_editing_options_mode' => array(
				'title'             => __( 'Enable Bulk Product Editing Options for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'bulk-product-editing' ),
				'id'                => 'yith_wpv_vendors_option_bulk_product_editing_options_management',
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'bulk-product-editing' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_bulk_product_editing_options_end' => array(
				'type' => 'sectionend',
			),


			'vendors_product_bundles_options' => array(
				'type' => 'sectionstart',
			),

			'vendors_product_bundles_options_title' => array(
				'title' => __( 'Product Bundles', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'product-bundles', 'display' ),
			),

			'vendors_product_bundles_options_mode' => array(
				'title'             => __( 'Product Bundles features for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'yith_premium_addons',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'product-bundles' ),
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'product-bundles' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_product_bundles_options_end' => array(
				'type' => 'sectionend',
			),

			'vendors_eu_energy_label_options' => array(
				'type' => 'sectionstart',
			),

			'vendors_eu_energy_label_options_title' => array(
				'title' => __( 'EU Energy Label', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'eu-energy-label', 'display' ),
			),

			'vendors_eu_energy_label_options_mode' => array(
				'title'             => __( 'EU Energy Label features for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'yith_premium_addons',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'eu-energy-label' ),
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'eu-energy-label' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_eu_energy_label_options_end' => array(
				'type' => 'sectionend',
			),

			/*'vendors_auctions_title' => array(
				'title' => __( 'Auctions', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'auctions', 'display' ),
			),

			'vendors_enable_auctions' => array(
				'title'             => __( 'Enable Auction products for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'checkbox',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'auctions' ),
				'id'                => 'yith_wpv_vendors_option_auctions_management',
				'default'           => 'yes',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'auctions' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_auctions_end' => array(
				'type' => 'sectionend',
			),*/

			'vendors_booking_options' => array(
				'type' => 'sectionstart',
			),

			'vendors_booking_options_title' => array(
				'title' => __( 'Booking', 'yith-woocommerce-product-vendors' ),
				'type'  => 'title',
				'desc'  => YITH_Vendors()->addons->get_plugin_landing_uri( 'booking', 'display' ),
			),

			'vendors_booking_options_mode' => array(
				'title'             => __( 'Booking features for vendors', 'yith-woocommerce-product-vendors' ),
				'type'              => 'yith_premium_addons',
				'desc'              => YITH_Vendors()->addons->get_option_description( 'booking' ),
				'default'           => 'no',
				'custom_attributes' => YITH_Vendors()->addons->has_plugin( 'booking' ) ? false : array(
					'disabled' => 'disabled',
				),
			),

			'vendors_booking_options_end' => array(
				'type' => 'sectionend',
			),
		)
	)
);