<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$coupons_active = YWCES_MultiVendor()->vendors_coupon_active();
$query_args     = array(
    'page' => 'yith_wpv_panel',
    'tab'  => 'vendors',
);
$vendor_url     = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
$description    = ( $coupons_active ? '' : sprintf( esc_html__( 'Coupon management must be enabled to make YITH WooCommerce Coupon Email System work correctly for vendors. %s Enable %s', 'yith-woocommerce-coupon-email-system' ), '<a href=" ' . $vendor_url . '" target="_blank">', '</a>' ) );
$disabled       = ( $coupons_active ? '' : array( 'disabled' => '' ) );


return array(
    'admin-vendor' => array(

        'ywces_vendors_title' => array(
            'name' => esc_html__( 'Allow coupon event management for vendors', 'yith-woocommerce-coupon-email-system' ),
            'type' => 'title',
            'desc' => $description
        ),

        'ywces_enable_register_vendor'           => array(
            'name'              => esc_html__( 'Enable coupon on user registration', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_register_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_first_purchase_vendor'     => array(
            'name'              => esc_html__( 'Enable coupon on first purchase', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_first_purchase_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_purchases_vendor'          => array(
            'name'              => esc_html__( 'Enable coupon on specific order threshold', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_purchases_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_spending_vendor'           => array(
            'name'              => esc_html__( 'Enable coupon on specific spent threshold', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_spending_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_product_purchasing_vendor' => array(
            'name'              => esc_html__( 'Enable coupon on specific product purchase', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_product_purchasing_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_birthday_vendor'           => array(
            'name'              => esc_html__( 'Enable coupon on customer birthday', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_birthday_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),
        'ywces_enable_last_purchase_vendor'      => array(
            'name'              => esc_html__( 'Enable coupon on a specific number of days from the last purchase', 'yith-woocommerce-coupon-email-system' ),
            'type'              => 'checkbox',
            'id'                => 'ywces_enable_last_purchase_vendor',
            'default'           => 'no',
            'custom_attributes' => $disabled
        ),

        'ywces_vendors_end' => array(
            'type' => 'sectionend',
        ),

    )

);