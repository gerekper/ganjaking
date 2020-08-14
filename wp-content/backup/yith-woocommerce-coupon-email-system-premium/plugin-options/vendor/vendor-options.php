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

$query_args              = array(
    'page' => isset( $_GET['page'] ) ? $_GET['page'] : '',
    'tab'  => 'howto',
);
$howto_url               = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
$placeholders_text       = esc_html__( 'Allowed placeholders:', 'yith-woocommerce-coupon-email-system' );
$ph_reference_link       = ' - <a href="' . $howto_url . '" target="_blank">' . esc_html__( 'More info', 'yith-woocommerce-coupon-email-system' ) . '</a>';
$ph_site_title           = ' <b>{site_title}</b>';
$ph_vendor_name          = ' <b>{vendor_name}</b>';
$ph_customer_name        = ' <b>{customer_name}</b>';
$ph_customer_last_name   = ' <b>{customer_last_name}</b >';
$ph_customer_email       = ' <b>{customer_email}</b>';
$ph_coupon_description   = ' <b>{coupon_description}</b>';
$ph_order_date           = ' <b>{order_date}</b>';
$ph_purchases_threshold  = ' <b>{purchases_threshold}</b>';
$ph_spending_threshold   = ' <b>{spending_threshold}</b>';
$ph_customer_money_spent = ' <b>{customer_money_spent}</b>';
$ph_purchased_product    = ' <b>{purchased_product}</b>';
$ph_days_ago             = ' <b>{days_ago}</b>';

$coupons  = array_merge( array( '' => esc_html__( 'Select a coupon', 'yith-woocommerce-coupon-email-system' ) ), YITH_WCES()->_available_coupons );
$disabled = ( count( YITH_WCES()->_available_coupons ) > 0 ? array() : array( 'disabled' => 'disabled' ) );
$vendor   = yith_get_vendor( 'current', 'user' );

$settings = array();

$register = ( get_option( 'ywces_enable_register_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_register' => array(
        'name' => esc_html__( 'On user registration', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_register'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_register'        => array(
        'name'              => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'checkbox',
        'id'                => 'ywces_enable_register_' . $vendor->id,
        'default'           => 'no',
        'custom_attributes' => $disabled
    ),
    'ywces_coupon_register'        => array(
        'name'    => esc_html__( 'Selected Coupon', 'yith-woocommerce-coupon-email-system' ),
        'type'    => 'select',
        'desc'    => esc_html__( 'Choose the coupon to send', 'yith-woocommerce-coupon-email-system' ),
        'options' => $coupons,
        'default' => '',
        'id'      => 'ywces_coupon_register_' . $vendor->id,
    ),
    'ywces_subject_register'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_register_' . $vendor->id,
        'default'           => esc_html__( 'You have received a welcome coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_register'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_register_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
thanks for your the registration on {site_title}!
We would like to offer you this coupon as a welcome gift:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_register'          => array(
        'name'              => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'ywces-send',
        'field_id'          => 'ywces_test_register',
        'custom_attributes' => $disabled
    ),
    'ywces_section_end_register'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings = array_merge( $settings, $register );

$first_purchase = ( get_option( 'ywces_enable_first_purchase_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_first_purchase' => array(
        'name' => esc_html__( 'On first purchase', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_first_purchase'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_first_purchase'        => array(
        'name'              => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'checkbox',
        'id'                => 'ywces_enable_first_purchase_' . $vendor->id,
        'default'           => 'no',
        'custom_attributes' => $disabled
    ),
    'ywces_coupon_first_purchase'        => array(
        'name'    => esc_html__( 'Selected Coupon', 'yith-woocommerce-coupon-email-system' ),
        'type'    => 'select',
        'desc'    => esc_html__( 'Choose the coupon to send', 'yith-woocommerce-coupon-email-system' ),
        'options' => $coupons,
        'default' => '',
        'id'      => 'ywces_coupon_first_purchase_' . $vendor->id,
    ),
    'ywces_subject_first_purchase'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_first_purchase_' . $vendor->id,
        'default'           => esc_html__( 'You have received a coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_first_purchase'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_order_date . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_first_purchase_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
thanks for making the first purchase on {order_date} on our shop {site_title}!
Because of this, we would like to offer you this coupon as a little gift:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_first_purchase'          => array(
        'name'              => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'ywces-send',
        'field_id'          => 'ywces_test_first_purchase',
        'custom_attributes' => $disabled
    ),
    'ywces_section_end_first_purchase'   => array(
        'type' => 'sectionend',
    )
) : array();
$settings       = array_merge( $settings, $first_purchase );

$purchases = ( get_option( 'ywces_enable_purchases_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_purchases' => array(
        'name' => esc_html__( 'On specific order threshold', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_purchases'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_purchases'        => array(
        'name'              => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'checkbox',
        'id'                => 'ywces_enable_purchases_' . $vendor->id,
        'default'           => 'no',
        'custom_attributes' => $disabled
    ),
    'ywces_thresholds_purchases'    => array(
        'name'    => esc_html__( 'Order thresholds', 'yith-woocommerce-coupon-email-system' ),
        'id'      => 'ywces_thresholds_purchases_' . $vendor->id,
        'options' => $coupons,
        'default' => '',
        'type'    => 'ywces-table'
    ),
    'ywces_subject_purchases'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_purchases_' . $vendor->id,
        'default'           => esc_html__( 'You have received a coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_purchases'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_order_date . $ph_purchases_threshold . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_purchases_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
with the order made on {order_date}, you have reached {purchases_threshold} orders!
Because of this, we would like to offer you this coupon as a gift:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_purchases'          => array(
        'name'              => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'ywces-send',
        'field_id'          => 'ywces_test_purchases',
        'custom_attributes' => $disabled
    ),
    'ywces_section_end_purchases'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings  = array_merge( $settings, $purchases );

$spending = ( get_option( 'ywces_enable_spending_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_spending' => array(
        'name' => esc_html__( 'On specific spent threshold', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_spending'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_spending'        => array(
        'name'              => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'checkbox',
        'id'                => 'ywces_enable_spending_' . $vendor->id,
        'default'           => 'no',
        'custom_attributes' => $disabled
    ),
    'ywces_thresholds_spending'    => array(
        'name'    => esc_html__( 'Amount thresholds', 'yith-woocommerce-coupon-email-system' ),
        'id'      => 'ywces_thresholds_spending_' . $vendor->id,
        'options' => $coupons,
        'default' => '',
        'type'    => 'ywces-table'
    ),
    'ywces_subject_spending'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_spending_' . $vendor->id,
        'default'           => esc_html__( 'You have received a coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_spending'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_order_date . $ph_spending_threshold . $ph_customer_money_spent . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_spending_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
with the order made on {order_date}, you have reached the amount of {spending_threshold} for a total purchase amount of {customer_money_spent}!
Because of this, we would like to offer you this coupon as a gift:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_spending'          => array(
        'name'              => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'ywces-send',
        'field_id'          => 'ywces_test_spending',
        'custom_attributes' => $disabled
    ),
    'ywces_section_end_spending'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings = array_merge( $settings, $spending );

$product_purchasing = ( get_option( 'ywces_enable_product_purchasing_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_product_purchasing' => array(
        'name' => esc_html__( 'On specific product purchase', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_product_purchasing'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_product_purchasing'        => array(
        'name'    => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'    => 'checkbox',
        'id'      => 'ywces_enable_product_purchasing_' . $vendor->id,
        'default' => 'no',
    ),
    'ywces_targets_product_purchasing'       => array(
        'title'       => esc_html__( 'Target products', 'yith-woocommerce-coupon-email-system' ),
        'id'          => 'ywces_targets_product_purchasing_' . $vendor->id,
        'type'        => 'yith-wc-product-select',
        'class'       => 'wc-product-search',
        'multiple'    => true,
        'default'     => '',
        'variations'  => 'true',
        'placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-coupon-email-system' ),
        'desc'        => esc_html__( 'Products that will cause the sending of the coupon', 'yith-woocommerce-coupon-email-system' )
    ),
    'ywces_coupon_product_purchasing'        => array(
        'name'    => esc_html__( 'Coupon settings', 'yith-woocommerce-coupon-email-system' ),
        'id'      => 'ywces_coupon_product_purchasing_' . $vendor->id,
        'default' => array(
            'discount_type'      => 'fixed_cart',
            'coupon_amount'      => '',
            'expiry_days'        => '',
            'minimum_amount'     => '',
            'maximum_amount'     => '',
            'free_shipping'      => '',
            'individual_use'     => '',
            'exclude_sale_items' => '',
        ),
        'type'    => 'ywces-coupon'
    ),
    'ywces_subject_product_purchasing'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_product_purchasing_' . $vendor->id,
        'default'           => esc_html__( 'You have received a coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_product_purchasing'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_order_date . $ph_purchased_product . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_product_purchasing_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
thanks for purchasing the following product with the order made on {order_date}: {purchased_product}.
We would like to offer you this coupon as a gift:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_product_purchasing'          => array(
        'name'     => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'     => 'ywces-send',
        'field_id' => 'ywces_test_product_purchasing',
    ),
    'ywces_section_end_product_purchasing'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings           = array_merge( $settings, $product_purchasing );

$birthday = ( get_option( 'ywces_enable_birthday_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_birthday' => array(
        'name' => esc_html__( 'On customer birthday', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_birthday'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_birthday'        => array(
        'name'    => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'    => 'checkbox',
        'id'      => 'ywces_enable_birthday_' . $vendor->id,
        'default' => 'no',
    ),
    'ywces_coupon_birthday'        => array(
        'name'    => esc_html__( 'Coupon settings', 'yith-woocommerce-coupon-email-system' ),
        'id'      => 'ywces_coupon_birthday_' . $vendor->id,
        'default' => array(
            'discount_type'      => 'fixed_cart',
            'coupon_amount'      => '',
            'expiry_days'        => '',
            'minimum_amount'     => '',
            'maximum_amount'     => '',
            'free_shipping'      => '',
            'individual_use'     => '',
            'exclude_sale_items' => '',
        ),
        'type'    => 'ywces-coupon'
    ),
    'ywces_subject_birthday'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_birthday_' . $vendor->id,
        'default'           => esc_html__( 'Happy birthday from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_birthday'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_birthday_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
we would like to make you our best wishes for a happy birthday!
Please, accept our coupon as a small gift for you:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_birthday'          => array(
        'name'     => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'     => 'ywces-send',
        'field_id' => 'ywces_test_birthday',
    ),
    'ywces_section_end_birthday'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings = array_merge( $settings, $birthday );

$last_purchase = ( get_option( 'ywces_enable_last_purchase_vendor' ) == 'yes' ) ? array(
    'ywces_section_title_last_purchase' => array(
        'name' => esc_html__( 'On a specific number of days from the last purchase', 'yith-woocommerce-coupon-email-system' ),
        'type' => 'title',
    ),
    'ywces_collapse_last_purchase'      => array(
        'type' => 'ywces-collapse'
    ),
    'ywces_enable_last_purchase'        => array(
        'name'    => esc_html__( 'Enable coupon sending', 'yith-woocommerce-coupon-email-system' ),
        'type'    => 'checkbox',
        'id'      => 'ywces_enable_last_purchase_' . $vendor->id,
        'default' => 'no',
    ),
    'ywces_days_last_purchase'          => array(
        'name'              => esc_html__( 'Days to elapse', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'number',
        'desc'              => esc_html__( 'The number of days that have to pass after the last order has been set as "completed"', 'yith-woocommerce-coupon-email-system' ),
        'default'           => 20,
        'id'                => 'ywces_days_last_purchase_' . $vendor->id,
        'custom_attributes' => array(
            'min'      => 1,
            'required' => 'required'
        )
    ),
    'ywces_coupon_last_purchase'        => array(
        'name'    => esc_html__( 'Coupon settings', 'yith-woocommerce-coupon-email-system' ),
        'id'      => 'ywces_coupon_last_purchase_' . $vendor->id,
        'default' => array(
            'discount_type'      => 'fixed_cart',
            'coupon_amount'      => '',
            'expiry_days'        => '',
            'minimum_amount'     => '',
            'maximum_amount'     => '',
            'free_shipping'      => '',
            'individual_use'     => '',
            'exclude_sale_items' => '',
        ),
        'type'    => 'ywces-coupon'
    ),
    'ywces_subject_last_purchase'       => array(
        'name'              => esc_html__( 'Email subject', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'text',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_reference_link,
        'id'                => 'ywces_subject_last_purchase_' . $vendor->id,
        'default'           => esc_html__( 'You have received a coupon from {vendor_name} on {site_title}', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'ywces-text',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_mailbody_last_purchase'      => array(
        'name'              => esc_html__( 'Email content', 'yith-woocommerce-coupon-email-system' ),
        'type'              => 'yith-wc-textarea',
        'desc'              => $placeholders_text . $ph_site_title . $ph_vendor_name . $ph_customer_name . $ph_customer_last_name . $ph_customer_email . $ph_days_ago . $ph_coupon_description . $ph_reference_link,
        'id'                => 'ywces_mailbody_last_purchase_' . $vendor->id,
        'default'           => esc_html__( 'Hi {customer_name},
{days_ago} days have passed since your last order.
We would like to encourage you to purchase something more with this coupon:

{coupon_description}

See you on our shop,

{vendor_name}.', 'yith-woocommerce-coupon-email-system' ),
        'class'             => 'yith-wc-textarea',
        'custom_attributes' => array(
            'required' => 'required'
        )
    ),
    'ywces_test_last_purchase'          => array(
        'name'     => esc_html__( 'Test email', 'yith-woocommerce-coupon-email-system' ),
        'type'     => 'ywces-send',
        'field_id' => 'ywces_test_last_purchase',
    ),
    'ywces_section_end_last_purchase'   => array(
        'type' => 'sectionend',
    ),
) : array();
$settings      = array_merge( $settings, $last_purchase );

return array(
    'vendor' => $settings
);