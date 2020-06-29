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

    'vendors' => array(

	    'vendors_product_amount_start'      => array(
            'type' => 'sectionstart',
        ),

        'vendors_product_amount_limit'      => array(
            'title'   => __( 'Enable product amount limit', 'yith-woocommerce-product-vendors' ),
            'desc'    => __( 'Limit product amount for each vendor', 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_enable_product_amount',
            'default' => 'no',
            'type'    => 'checkbox',
        ),

        'vendors_product_amount'            => array(
            'title'             => __( 'Product amount limit', 'yith-woocommerce-product-vendors' ),
            'type'              => 'number',
            'default'           => 25,
            'desc'              => __( 'Set a maximum number of products that each vendor can publish', 'yith-woocommerce-product-vendors' ),
            'id'                => 'yith_wpv_vendors_product_limit',
            'css'               => 'width:65px;',
            'custom_attributes' => array(
                'min'  => 0,
                'step' => 1
            )
        ),

        'vendors_product_listing'           => array(
            'title'    => __( 'Product listings', 'yith-woocommerce-product-vendors' ),
            'desc'     => __( 'Hide vendor products from store loop page', 'yith-woocommerce-product-vendors' ),
            'id'       => 'yith_wpv_hide_vendor_products',
            'default'  => 'no',
            'desc_tip' => __( 'Hide products belonging to vendors from store loop page - this means that vendor products will only be visible on the individual vendor pages.', 'yith-woocommerce-product-vendors' ),
            'type'     => 'checkbox',
        ),

        'vendors_skip_reviews'              => array(
            'title'   => __( 'Skip admin review', 'yith-woocommerce-product-vendors' ),
            'type'    => 'checkbox',
            'desc'    => __( 'If you enable this option any vendor could publish products without super admin authorization. This is the default option for any new vendor It is possible to override these settings for each vendor. ', 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_vendors_option_skip_review',
            'default' => 'no'
        ),

        'vendors_pending_post_status'              => array(
            'title'   => __( 'Set products to “Pending review" status after vendors edit them', 'yith-woocommerce-product-vendors' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Enabling this option will let you set any product on which a vendor applies a change to “Pending review” status, if the vendor is not allowed to apply changes to his/her products without super admin approval.', 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_vendors_option_pending_post_status',
            'default' => 'no'
        ),


        'vendors_force_review'              => array(
            'title'   => __( 'Force "Skip reviews" option for all vendors', 'yith-woocommerce-product-vendors' ),
            'type'    => 'button',
            'name'    => __( 'Force option', 'yith-woocommerce-product-vendors' ),
            'desc'    => __( 'Force "Skip admin review" options for all vendors.', 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_vendors_skip_review_for_all',
            'default' => 'no'
        ),

        'vendors_yit_shortcodes_management' => function_exists( 'YIT_Shortcodes' ) ? array(
            'title'   => __( 'Enable YIT Shortcodes Button', 'yith-woocommerce-product-vendors' ),
            'type'    => 'checkbox',
            'desc'    => __( 'If you enable this option, each vendor will be able to use YIT Shortcodes in Add/Edit Product page.', 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_yit_shortcodes',
            'default' => 'no'
        ) : false,

        'vendors_product_amount_end'        => array(
            'type' => 'sectionend',
        ),

        'new_section_options'               => array(
            'vendors_coupons_start'           => array(
                'type' => 'sectionstart',
            ),

            'vendors_coupons_title'           => array(
                'title' => __( 'Coupon management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_coupons_title'
            ),

            'vendors_coupon_management'       => array(
                'title'   => __( 'Enable coupon management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to create coupon for their own products.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_coupon_management',
                'default' => 'no'
            ),

            'vendors_coupons_end'             => array(
                'type' => 'sectionend',
            ),

            'vendors_reviews_start'           => array(
                'type' => 'sectionstart',
            ),

            'vendors_review_title'            => array(
                'title' => __( 'Review management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_reviews_title'
            ),

            'vendors_review_management'       => array(
                'title'   => __( 'Enable review management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to manage reviews on his/her own products independently.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_review_management',
                'default' => 'no'
            ),

            'vendors_review_end'              => array(
                'type' => 'sectionend',
            ),

            'vendors_order_start'             => array(
                'type' => 'sectionstart',
            ),

            'vendors_order_title'             => array(
                'title' => __( 'Order management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_orders_title'
            ),

            'vendors_order_management'        => array(
                'title'   => __( 'Enable order management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to manage orders on his/her own products independently.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_management',
                'default' => 'no'
            ),

            'vendors_order_synchronization'   => array(
                'title'   => __( 'Parent order - suborder synchronization', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "All changes made to general orders will be synchronized with the individual vendor's order", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_synchronization',
                'default' => 'no'
            ),

            //string added @version 2.0.8
            'vendors_suborder_synchronization'   => array(
                'title'   => __( 'Suborder - parent order status synchronization', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "Check this option to update the parent order status when editing the child order status", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_suborder_synchronization',
                'default' => 'no'
            ),

            'vendors_order_refund_management' => array(
                'title'   => __( 'Order refund management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "If you enable this option, each vendor will be able to manage refund on his/her own orders independently.", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_refund_synchronization',
                'default' => 'yes'
            ),

            'vendors_order_hide_customer'     => array(
                'title'   => __( 'Hide Customer Section', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "Check this option to prevent vendors from seeing 'Customer' section in order details", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_hide_customer',
                'default' => 'no'
            ),

            'vendors_order_hide_payment'     => array(
                'title'   => __( 'Hide Payment Information', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "Check this option to prevent vendors from seeing 'Payment' section in order details", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_hide_payment',
                'default' => 'no'
            ),

            'vendors_order_hide_shipping_billing_fields'     => array(
	            'title'   => _x( 'Hide Shipping and Billing Information', '[Admin Area]: Order details page', 'yith-woocommerce-product-vendors' ),
	            'type'    => 'checkbox',
	            'desc'    => __( "Check this option to prevent vendors from seeing 'Billing and Shipping' section in order details", 'yith-woocommerce-product-vendors' ),
	            'id'      => 'yith_wpv_vendors_option_order_hide_shipping_billing',
	            'default' => 'no'
            ),

            'vendors_order_hide_emails'     => array(
                'title'   => __( 'Prevent vendors to resend order emails', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "It prevents the vendors to resend the emails related to the orders to customers.", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_prevent_resend_email',
                'default' => 'no'
            ),

            //string added @version 1.14.0
            'vendors_order_hide_custom_fields'     => array(
                'title'   => __( 'Prevent vendors to edit custom fields', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "It prevents the vendors to edit the shop orders custom fileds", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_prevent_edit_custom_fields',
                'default' => 'no'
            ),

            'vendors_order_end'               => array(
                'type' => 'sectionend',
            ),

            'vendors_featured_start'          => array(
                'type' => 'sectionstart',
            ),

            'vendors_featured_title'          => array(
                'title' => __( 'Featured Products management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_featured_title'
            ),

            'vendors_featured_management'     => array(
                'title'   => __( 'Enable featured products management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to set "Featured" on his/her own products independently (this option can be override for each vendor).', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_featured_management',
                'default' => 'no'
            ),

            'vendors_featured_end'            => array(
                'type' => 'sectionend',
            ),

            'vendors_editor_start'            => array(
                'type' => 'sectionstart',
            ),

            'vendors_editor_title'            => array(
                'title' => __( 'Advanced editor', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_editor_title'
            ),

            'vendors_editor_management'       => array(
                'title'   => __( 'Enable advanced editor for vendor description', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to use an advanced editor for store description.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_editor_management',
                'default' => 'no'
            ),

            'vendors_editor_media'       => array(
                'title'   => __( 'Enable media button in text editor', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to use the media button in advanced editor.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_editor_media',
                'default' => 'no'
            ),

            'vendors_editor_end'              => array(
                'type' => 'sectionend',
            ),

            'vendor_shop_admins_start' => array(
                'type' => 'sectionstart',
            ),

            'vendor_shop_admins_title'            => array(
                'title' => __( 'Shop Admins', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
            ),

            'vendor_shop_admins_title_cap'       => array(
                'title'   => __( 'Enable vendors to add admins', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Thanks to this option, your vendors will be able to assign shop admins for their own shop page. Please, be careful while using this option, because this way your vendors will be able to see all users registered to the entire store, first name, last name and email address and this could be a violation of their own privacy. We always recommend you enable this option only if necessary. Alternatively, your vendors will be able to see who the admins of their shop page are and make a request to the global admin to have them added.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_ahop_admins_cap',
                'default' => 'no'
            ),

            'vendor_shop_admins_end' => array(
                'type' => 'sectionend',
            ),
        ),
    ),
);
