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
            'name' => esc_html__( 'Coupon fields settings', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_apply_gift_card_on_coupon_form'    => array(
            'name'    => esc_html__( 'Allow gift card codes in WooCommerce coupon fields', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_apply_gift_card_on_coupon_form',
            'desc'    => esc_html__( 'Let customers apply the gift card code in the standard WooCommerce coupon fields', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_apply_coupon_button_text_button'    => array(
            'name'    => esc_html__( "Enter a text for the 'Apply coupon' button", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_coupon_button_text_button',
            'custom_attributes' => 'placeholder="' . __( 'write the apply coupon button text', 'yith-woocommerce-gift-cards' ) . '"',
            'default' => esc_html__( 'Apply coupon', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_apply_coupon_label_text'    => array(
            'name'    => esc_html__( "Enter a text for the 'Have a coupon?' label", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_coupon_label_text',
            'custom_attributes' => 'placeholder="' . __( 'write the apply coupon button text', 'yith-woocommerce-gift-cards' ) . '"',

            'default' => esc_html__( 'Have a coupon?', 'yith-woocommerce-gift-cards' ),
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
            'name' => esc_html__( 'Cart page options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_show_recipient_on_cart'           => array(
            'id'      => 'ywgc_show_recipient_on_cart',
            'name'    => esc_html__( 'Show gift card info in the cart', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "If enabled, when a gift card is added to the cart, the gift card info will be displayed.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'ywgc_minimal_cart_total_option'           => array(
            'id'      => 'ywgc_minimal_cart_total_option',
            'name'    => esc_html__( 'Minimum amount in cart', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "If enabled, customers can only use the gift card code if the cart minimum amount requirement is fulfilled.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'ywgc_minimal_cart_total_value'           => array(
            'id'      => 'ywgc_minimal_cart_total_value',
            'name'    => esc_html__( 'Set cart minimum amount', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "Enter here the cart minimum amount to allow using gift card codes.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'deps'      => array(
                'id'    => 'ywgc_minimal_cart_total_option',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_card_form_on_cart'    => array(
            'name'    => esc_html__( 'Apply gift card on Cart page', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_gift_card_form_on_cart',
            'desc'    => esc_html__( 'If enabled, customers can apply the gift card code on the Cart page.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_gift_card_form_on_cart_place' => array(
            'name'    => esc_html__( 'Apply gift card position', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_gift_card_form_on_cart_place',
            'options' => array(
                'woocommerce_before_cart' => esc_html__( 'before cart', 'yith-woocommerce-gift-cards' ),
                'woocommerce_after_cart_table' => esc_html__( 'after cart', 'yith-woocommerce-gift-cards' ),
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
            'name' => esc_html__( 'Checkout page options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_gift_card_form_on_checkout'    => array(
            'name'    => esc_html__( 'Apply gift card on Checkout page', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_gift_card_form_on_checkout',
            'desc'    => esc_html__( 'If enabled, customers can apply the gift card code on the Checkout page.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_gift_card_form_on_checkout_place' => array(
            'name'    => esc_html__( 'Apply gift card position', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'    => 'wc-enhanced-select',
            'id'      => 'ywgc_gift_card_form_on_checkout_place',
            'options' => array(
                'woocommerce_before_checkout_form' => esc_html__( 'before checkout form', 'yith-woocommerce-gift-cards' ),
                'woocommerce_after_checkout_form' => esc_html__( 'after checkout form', 'yith-woocommerce-gift-cards' ),
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
            'name' => esc_html__( 'Apply gift card section design', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_text_before_gc_form'    => array(
            'name'    => esc_html__( 'Enter a text before the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_text_before_gc_form',
            'default' => esc_html__( 'Got a gift card from a loved one?', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_link_text_before_gc_form'    => array(
            'name'    => esc_html__( 'Enter here the text of the link that opens the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_link_text_before_gc_form',
            'default' => esc_html__( 'Use it here!', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_icon_text_before_gc_form'   => array(
            'name'    => esc_html__( 'Enable icon before text', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_icon_text_before_gc_form',
            'desc'    => esc_html__( 'Show a gift-card icon in the text before the gift card form.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_display_form' => array(
            'name' => esc_html__('Apply gift card layout', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id' => 'ywgc_display_form',
            'options' => array(
                'ywgc_display_form_hidden' => esc_html__( 'Hidden form', 'yith-woocommerce-gift-cards' ),
                'ywgc_display_form_visible' => esc_html__( 'Visible form', 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'ywgc_display_form_hidden',
        ),
        'ywgc_text_in_the_form'    => array(
            'name'    => esc_html__( 'Enter a text for the gift card form', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_text_in_the_form',
            'default' => esc_html__( 'Apply the gift card code in the following box', 'yith-woocommerce-gift-cards' ),
        ),

        'ywgc_apply_gift_cards_colors' => array(
            'name'      => esc_html__( 'Colors of apply gift card section', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'multi-colorpicker',
            'id'        => 'ywgc_apply_gift_cards_colors',
            'colorpickers' => array(
                array(
                    'name'      => esc_html__( 'default color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => esc_html__( 'hover color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => esc_html__( 'default text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default_text',
                    'default'   => '#000000'
                ),
                array(
                    'name'      => esc_html__( 'hover text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover_text',
                    'default'   => '#000000'
                ),
            ),
        ),
        'ywgc_apply_gift_card_button_text'    => array(
            'name'    => esc_html__( "Text of Apply gift card button", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_apply_gift_card_button_text',
            'default' => esc_html__( 'Apply Gift Card', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_apply_gift_cards_button_colors' => array(
            'name'      => esc_html__( 'Colors of Apply gift card button', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'multi-colorpicker',
            'id'        => 'ywgc_apply_gift_cards_button_colors',
            'colorpickers' => array(
                array(
                    'name'      => esc_html__( 'default color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default',
                    'default'   => '#448a85'
                ),
                array(
                    'name'      => esc_html__( 'hover color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover',
                    'default'   => '#4ac4aa'
                ),
                array(
                    'name'      => esc_html__( 'default text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'default_text',
                    'default'   => '#ffffff'
                ),
                array(
                    'name'      => esc_html__( 'hover text color', 'yith-woocommerce-gift-cards' ),
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
