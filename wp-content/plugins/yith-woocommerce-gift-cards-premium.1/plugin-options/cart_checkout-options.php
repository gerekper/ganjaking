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
$cart_checkout_options = array(

	'cart_checkout' => array(

        /**
         *
         * Coupon fields settings
         *
         */
        array(
            'name' => __( 'Coupon fields settings', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_apply_gift_card_on_coupon_form'    => array(
            'name'    => __( 'Allow gift card codes in WooCommerce coupon fields', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_apply_gift_card_on_coupon_form',
            'desc'    => __( 'Let customers apply the gift card code in the standard WooCommerce coupon fields', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_apply_coupon_button_text_button'    => array(
            'name'    => __( "Enter a text for the 'Apply coupon' button", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_coupon_button_text_button',
            'custom_attributes' => "placeholder='write the apply coupon button text'",
            'default' => __( 'Apply coupon', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_apply_coupon_label_text'    => array(
            'name'    => __( "Enter a text for the 'Have a coupon?' label", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_coupon_label_text',
            'custom_attributes' => "placeholder='write the apply coupon button text'",
            'default' => __( 'Have a coupon?', 'yith-woocommerce-gift-cards' ),
        ),
        
        array(
            'type' => 'sectionend',
        ),


        /**
         *
         * Cart page options
         *
         */
        array(
            'name' => __( 'Cart page options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_show_recipient_on_cart'           => array(
            'id'      => 'ywgc_show_recipient_on_cart',
            'name'    => __( 'Show gift card info in the cart', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "If enabled, when a gift card is added to the cart, the gift card info will be displayed.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'ywgc_minimal_cart_total_option'           => array(
            'id'      => 'ywgc_minimal_cart_total_option',
            'name'    => __( 'Minimum amount in cart', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "If enabled, customers can only use the gift card code if the cart minimum amount requirement is fulfilled.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'ywgc_minimal_cart_total_value'           => array(
            'id'      => 'ywgc_minimal_cart_total_value',
            'name'    => __( 'Set cart minimum amount', 'yith-woocommerce-gift-cards' ),
            'desc'    => __( "Enter here the cart minimum amount to allow using gift card codes.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'deps'      => array(
                'id'    => 'ywgc_minimal_cart_total_option',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_card_form_on_cart'    => array(
            'name'    => __( 'Apply gift card on Cart page', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_gift_card_form_on_cart',
            'desc'    => __( 'If enabled, customers can apply the gift card code on the Cart page.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_gift_card_form_on_cart_place' => array(
            'name'    => __( 'Apply gift card position', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_gift_card_form_on_cart_place',
            'options' => array(
                'woocommerce_before_cart' => __( 'before cart', 'yith-woocommerce-gift-cards' ),
                'woocommerce_after_cart_table' => __( 'after cart', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'woocommerce_before_cart',
            'deps'      => array(
                'id'    => 'ywgc_gift_card_form_on_cart',
                'value' => 'yes',
            )
        ),
        array(
            'type' => 'sectionend',
        ),


        /**
         *
         * Checkout page options
         *
         */
        array(
            'name' => __( 'Checkout page options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_gift_card_form_on_checkout'    => array(
            'name'    => __( 'Apply gift card on Checkout page', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_gift_card_form_on_checkout',
            'desc'    => __( 'If enabled, customers can apply the gift card code on the Checkout page.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_gift_card_form_on_checkout_place' => array(
            'name'    => __( 'Apply gift card position', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_gift_card_form_on_checkout_place',
            'options' => array(
                'woocommerce_before_checkout_form' => __( 'before checkout form', 'yith-woocommerce-gift-cards' ),
                'woocommerce_after_checkout_form' => __( 'after checkout form', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'woocommerce_before_checkout_form',
            'deps'      => array(
                'id'    => 'ywgc_gift_card_form_on_checkout',
                'value' => 'yes',
            )
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Apply gift card section design
         *
         */
        array(
            'name' => __( 'Apply gift card section design', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_text_before_gc_form'    => array(
            'name'    => __( 'Enter a text before the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_text_before_gc_form',
            'default' => __( 'Got a gift card from a loved one?', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_link_text_before_gc_form'    => array(
            'name'    => __( 'Enter here the text of the link that opens the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_link_text_before_gc_form',
            'default' => __( 'Use it here!', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_icon_text_before_gc_form'   => array(
            'name'    => __( 'Enable icon before text', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_icon_text_before_gc_form',
            'desc'    => __( 'Show a gift-card icon in the text before the gift card form.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_display_form' => array(
            'name' => __('Apply gift card layout', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id' => 'ywgc_display_form',
            'options' => array(
                'ywgc_display_form_hidden' => __( 'Hidden form', 'yith-woocommerce-gift-cards' ),
                'ywgc_display_form_visible' => __( 'Visible form', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'ywgc_display_form_hidden',
        ),
        'ywgc_text_in_the_form'    => array(
            'name'    => __( 'Enter a text for the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_text_in_the_form',
            'default' => __( 'Apply the gift card code in the following box', 'yith-woocommerce-gift-cards' ),
        ),

        'ywgc_apply_gift_cards_colors' => array(
            'name'      => __( 'Colors of apply gift card section', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'multi-colorpicker',
            'id'        => 'ywgc_apply_gift_cards_colors',
            'colorpickers' => array(
                array(
                    'name'      => __( 'default color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => __( 'hover color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => __( 'default text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default_text',
                    'default'   => '#000000'
                ),
                array(
                    'name'      => __( 'hover text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover_text',
                    'default'   => '#000000'
                ),
            ),
        ),
        'ywgc_apply_gift_card_button_text'    => array(
            'name'    => __( "Text of Apply gift card button", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_gift_card_button_text',
            'default' => __( 'Apply Gift Card', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_apply_gift_cards_button_colors' => array(
            'name'      => __( 'Colors of Apply gift card button', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'multi-colorpicker',
            'id'        => 'ywgc_apply_gift_cards_button_colors',
            'colorpickers' => array(
                array(
                    'name'      => __( 'default color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default',
                    'default'   => '#448a85'
                ),
                array(
                    'name'      => __( 'hover color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover',
                    'default'   => '#4ac4aa'
                ),
                array(
                    'name'      => __( 'default text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default_text',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => __( 'hover text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover_text',
                    'default'   => '#ffffff'
                ),
            ),
        ),

        array(
            'type' => 'sectionend',
        ),


    ),
);

return apply_filters( 'yith_ywgc_cart_checkout_options_array', $cart_checkout_options );
