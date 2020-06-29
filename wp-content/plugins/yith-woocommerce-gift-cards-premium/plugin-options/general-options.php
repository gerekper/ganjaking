<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$general_options = array(

	'general' => array(
        /**
         *
         * General settings
         *
         */
		array(
			'name' => esc_html__( 'General settings', 'yith-woocommerce-gift-cards' ),
			'type' => 'title',
		),
        'ywgc_plugin_date_format_option' => array(
            'name'    => esc_html__( 'Date format', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'date-format',
            'id'      => 'ywgc_plugin_date_format_option',
            'js'        => true,
            'desc'    => esc_html__( 'Choose the date format for the gift card expiry date, date of delivery and so on.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yy-mm-dd',
        ),
        'ywgc_enable_pre_printed'     => array(
            'name'    => esc_html__( 'Disable code generation', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_enable_pre_printed',
            'desc'    => esc_html__( 'Enable this option if you want to create gift cards without any code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_code_pattern'           => array(
            'id'      => 'ywgc_code_pattern',
            'name'    => esc_html__( 'Gift card code pattern', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "Choose the pattern of new gift cards. If you set ***-*** your cards will have a code like: 1ME-D28.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'default' => '****-****-****-****',
			'deps'    => array(
				'id'    => 'ywgc_enable_pre_printed',
				'value' => 'no',
				'type'  => 'hide'
			)
        ),
        'ywgc_apply_gc_code_on_gc_product'     => array(
            'name'    => esc_html__( 'Prevent the use of a gift card to purchase another gift card', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_apply_gc_code_on_gc_product',
            'desc'    => esc_html__( 'If enabled, the use of gift cards codes to purchase a gift card product is not allowed.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_allow_shop_manager'      => array(
	        'name'    => esc_html__( 'Enable Shop Managers', 'yith-woocommerce-gift-cards' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'onoff',
	        'id'      => 'ywgc_allow_shop_manager',
	        'desc'    => esc_html__( 'If enabled, the user with role Shop Manager will be able to manage the plugin settings panel.', 'yith-woocommerce-gift-cards' ),
	        'default' => 'no',
        ),
		array(
			'type' => 'sectionend',
		),
		array(
			'name' => esc_html__( 'Notifications', 'yith-woocommerce-gift-cards' ),
			'type' => 'title',
		),
        'ywgc_blind_carbon_copy'      => array(
            'name'    => esc_html__( 'Gift card admin notification', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_blind_carbon_copy',
            'desc'    => esc_html__( 'If enabled, admin will receive a BCC email with the gift card code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_blind_carbon_copy_to_buyer'      => array(
            'name'    => esc_html__( 'Gift card sender notification', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_blind_carbon_copy_to_buyer',
            'desc'    => esc_html__( 'If enabled, the customer (the gift card sender) will receive a BCC email with the gift card code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_blind_carbon_copy_additionals'      => array(
	        'name'    => esc_html__( 'BCC additional emails', 'yith-woocommerce-gift-cards' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'text',
	        'id'      => 'ywgc_blind_carbon_copy_additionals',
	        'desc'    => esc_html__( 'Create additional emails separating by a comma, to receive a BCC email with the gift card code.', 'yith-woocommerce-gift-cards' ),
	        'default' => '',
	        'custom_attributes' => 'placeholder="' . __( 'Write the additional emails here, separated by comma', 'yith-woocommerce-gift-cards' ) . '"',
        ),
        'ywgc_delivery_notify_customer'      => array(
	        'name'    => esc_html__( 'Notify the delivery of a gift card', 'yith-woocommerce-gift-cards' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'onoff',
	        'id'      => 'ywgc_delivery_notify_customer',
	        'desc'    => esc_html__( 'If enabled, the sender will receive an email when the gift card has been delivered to recipient.', 'yith-woocommerce-gift-cards' ),
	        'default' => 'no',
        ),
        'ywgc_notify_customer'      => array(
            'name'    => esc_html__( 'Notify the use of a gift card', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_notify_customer',
            'desc'    => esc_html__( 'If enabled, the sender will receive an email notifying the use of the gift card purchased by him.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        array(
            'type' => 'sectionend',
        ),
        /**
         *
         * Global settings
         *
         */
        array(
            'name' => esc_html__( 'Global settings', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
            'desc' => esc_html__('These options will be applied to all your gift card products, but in the gift card product edit page you can override them if you want different values for specific gift cards.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_select_amount_title'          => array(
            'name'    => esc_html__( 'Title for “Set an amount” section', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_select_amount_title',
            'desc'    => esc_html__( "Enter a title for the 'Set an amount' area on your gift card page. This area will include the preset gift card amounts and the custom amount if enabled.", 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => 'placeholder="' . __( 'write the set an amount area title', 'yith-woocommerce-gift-cards' ) . '"',

            'default' => esc_html__( "Set an amount", 'yith-woocommerce-gift-cards'),
        ),
        'ywgc_permit_free_amount'     => array(
            'name'    => esc_html__( 'Enable/disable custom amount', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_permit_free_amount',
            'desc'    => esc_html__( 'If enabled, customers can enter a custom amount when buying a gift card.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_minimal_amount_gift_card'           => array(
            'id'      => 'ywgc_minimal_amount_gift_card',
            'name'    => esc_html__( 'Minimum custom amount', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "Set a minimum value for the custom amount of your gift cards. Leave empty if you don't want to set a minimum amount.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'default' => '0',
            'custom_attributes' => 'placeholder="' . __( 'write a minimal amount', 'yith-woocommerce-gift-cards' ) . '"',
            'min'     => 0,
            'deps'      => array(
                'id'    => 'ywgc_permit_free_amount',
                'value' => 'yes',
            )
        ),
        'ywgc_usage_expiration'       => array(
            'id'                => 'ywgc_usage_expiration',
            'name'              => esc_html__( 'Gift card expiration date', 'yith-woocommerce-gift-cards' ),
            'desc'              => esc_html__( 'Set a default expiration for gift cards in months. If the value is zero, your gift cards will never expire.', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'default' => 0,
            'custom_attributes' => 'placeholder="' . __( 'write expiration date in months', 'yith-woocommerce-gift-cards' ) . '"',

            'min'     => 0,
        ),

        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift card orders settings
         *
         */
        array(
            'name' => esc_html__( 'Gift card orders settings', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_order_cancelled_action' => array(
            'name'    => esc_html__( 'When an order containing a gift card is cancelled', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_order_cancelled_action',
            'options' => array(
                'nothing' => esc_html__( 'Do nothing', 'yith-woocommerce-gift-cards' ),
                'disable' => esc_html__( 'Disable the gift cards', 'yith-woocommerce-gift-cards' ),
                'dismiss' => esc_html__( 'Dismiss the gift cards', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'nothing',
        ),
        'ywgc_order_refunded_action'  => array(
            'name'    => esc_html__( 'When an order containing a gift card is refunded', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_order_refunded_action',
            'options' => array(
                'nothing' => esc_html__( 'Do nothing', 'yith-woocommerce-gift-cards' ),
                'disable' => esc_html__( 'Disable the gift cards', 'yith-woocommerce-gift-cards' ),
                'dismiss' => esc_html__( 'Dismiss the gift cards', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'nothing',
        ),
        array(
            'type' => 'sectionend',
        ),

        'convert_smart_coupons_tab_start'    => array(
            'type' => 'sectionstart',
            'id'   => 'yith_convert_smart_coupons_settings_tab_start'
        ),
        'convert_smart_coupons_tab_title'    => array(
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith_convert_smart_coupons_tab'
        ),
        'convert_smart_coupons_tab_button' => array(
            'title'   => '',
            'desc'    => '',
            'id'      => '',
            'type'  => 'yith_ywgc_transform_smart_coupons_html',
            'html'  => '',
        ),
        'convert_smart_coupons_tab_end'      => array(
            'type' => 'sectionend',
            'id'   => 'yith_settings_tab_end'
        ),

    ),
);

return apply_filters( 'yith_ywgc_general_options_array', $general_options );
