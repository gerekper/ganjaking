<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return apply_filters( 'yith_wpv_panel_vendors_options', array(

        'vendors' => array(

	        'vendors_label_start' => array(
		        'type' => 'sectionstart',
	        ),

	        'vendors_label_options_title' => array(
		        'title' => __('Vendor Label: Change it with...', 'yith-woocommerce-product-vendors'),
		        'type' => 'title',
	        ),

	        'vendors_label_singular_text' => array(
		        'title' => __('Singular', 'yith-woocommerce-product-vendors'),
		        'type' => 'text',
		        'desc' => __('Change "Vendor" singular label', 'yith-woocommerce-product-vendors'),
		        'id' => 'yith_wpv_vendor_label_singular_text',
		        'default' => _x( "Vendor", 'default singular vendor label', 'yith-woocommerce-product-vendors' )
	        ),

	        'vendors_label_plural_text' => array(
		        'title' => __('Plural', 'yith-woocommerce-product-vendors'),
		        'type' => 'text',
		        'desc' => __('Change "Vendors" plural label', 'yith-woocommerce-product-vendors'),
		        'id' => 'yith_wpv_vendor_label_plural_text',
		        'default' => _x( "Vendors", 'default plural vendors label', 'yith-woocommerce-product-vendors' )
	        ),

	        'vendors_label_end' => array(
		        'type' => 'sectionend',
	        ),

            'vendors_options_start' => array(
                'type' => 'sectionstart',
            ),

            'vendors_options_title' => array(
                'title' => __( 'Product management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_options_title'
            ),

            'vendors_color_name'    => array(
                'title'   => __( 'Vendor name label color', 'yith-woocommerce-product-vendors' ),
                'type'    => 'color',
                'desc'    => __( 'Use in shop page and single product page', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_vendors_color_name',
                'default' => '#bc360a'
            ),

            'vendors_options_end'   => array(
                'type' => 'sectionend',
            ),

            'vendors_order_start'           => array(
                'type' => 'sectionstart',
            ),

            'vendors_order_title'           => array(
                'title' => __( 'Order management', 'yith-woocommerce-product-vendors' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wpv_vendors_orders_title'
            ),

            'vendors_order_management'      => array(
                'title'   => __( 'Enable order management', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( 'If you enable this option, each vendor will be able to manage orders on his/her own products independently.', 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_management',
                'default' => 'no'
            ),

            'vendors_order_synchronization' => array(
                'title'   => __( 'Order synchronization', 'yith-woocommerce-product-vendors' ),
                'type'    => 'checkbox',
                'desc'    => __( "All changes to general orders will be synchronized with the individual vendor's order", 'yith-woocommerce-product-vendors' ),
                'id'      => 'yith_wpv_vendors_option_order_synchronization',
                'default' => 'yes'
            ),

            'vendors_order_end'             => array(
                'type' => 'sectionend',
            ),
        )
    ), 'vendors'
);