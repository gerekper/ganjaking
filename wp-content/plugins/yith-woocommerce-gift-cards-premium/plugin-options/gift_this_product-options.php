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

$gift_this_product_options = array(

	'gift_this_product' => array(
        /**
         *
         * Product page options
         *
         */
        array(
            'name' => esc_html__( 'Product page options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_permit_its_a_present'   => array(
            'name'    => esc_html__( 'Enable "Gift this product" option', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_permit_its_a_present',
            'desc'    => esc_html__( 'Enable to allow users to buy a gift card of the same amount of the product. This feature appears on the product page. The product will be recommended in the recipient email that includes the gift card code.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_permit_its_a_present_shop_page'   => array(
	        'name'    => esc_html__( 'Show "Gift this product" option in shop page', 'yith-woocommerce-gift-cards' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'onoff',
	        'id'      => 'ywgc_permit_its_a_present_shop_page',
	        'desc'    => esc_html__( 'Enable to show the "Gift this product" link also in shop pages.', 'yith-woocommerce-gift-cards' ),
	        'default' => 'no',
	        'deps'      => array(
		        'id'    => 'ywgc_permit_its_a_present',
		        'value' => 'yes',
	        )
        ),
        'ywgc_gift_this_product_icon'   => array(
            'name'    => esc_html__( 'Gift this product icon', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_gift_this_product_icon',
            'desc'    => esc_html__( 'Enable a gift card icon in the Gift this product title.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
//        'ywgc_gift_this_product_position'   => array(
//            'name'    => esc_html__( 'Position of "Gift this product" button', 'yith-woocommerce-gift-cards' ),
//            'type'    => 'yith-field',
//            'yith-type' => 'select',
//            'id'      => 'ywgc_gift_this_product_position',
//            'options'   => array(
//                'ywgc_gift_this_product_position_shortcode' => 'Shortcode',
//                'ywgc_gift_this_product_position_1' => 'Option 1',
//                'ywgc_gift_this_product_position_2' => 'Option 2'
//            ),
//            'default' => 'ywgc_gift_this_product_position_1',
//            'deps'      => array(
//                'id'    => 'ywgc_permit_its_a_present',
//                'value' => 'yes',
//            )
//        ),
//        'ywgc_gift_this_product_position_copy_shortcode'   => array(
//            'name'    => esc_html__( 'Copy this shortcode', 'yith-woocommerce-gift-cards' ),
//            'type'    => 'yith-field',
//            'yith-type' => 'text',
//            'id'      => 'ywgc_gift_this_product_position_shortcode',
//            'default' => '[gift_this_product_shortcode]',
//            'deps'      => array(
//                'id'    => 'ywgc_gift_this_product_position',
//                'value' => 'ywgc_gift_this_product_position_shortcode',
//                'type'  => 'hide'
//            )
//        ),
        'ywgc_gift_this_product_button_style'   => array(
            'name'    => esc_html__( 'Label style', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id'      => 'ywgc_gift_this_product_button_style',
            'options'   => array(
                'ywgc_gift_this_product_button_style_text' => esc_html__( "Only text", 'yith-woocommerce-gift-cards'),
                'ywgc_gift_this_product_button_style_button' => esc_html__( "Button", 'yith-woocommerce-gift-cards'),
            ),
            'default' => 'ywgc_gift_this_product_button_style_text',
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_this_product_label_description'           => array(
	        'id'      => 'ywgc_gift_this_product_label_description',
	        'name'    => esc_html__( 'Description to show before button', 'yith-woocommerce-gift-cards' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'textarea',
	        'default' => esc_html__( 'Do you feel this product is perfect for a friend or a loved one? You can buy a gift card for this item!', 'yith-woocommerce-gift-cards' ),
	        'deps'      => array(
		        'id'    => 'ywgc_permit_its_a_present',
		        'value' => 'yes',
	        )
        ),
        'ywgc_gift_this_product_label'           => array(
            'id'      => 'ywgc_gift_this_product_label',
            'name'    => esc_html__( 'Enter a custom text', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'default' => esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ),
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_this_product_colors' => array(
            'name'      => esc_html__( 'Label colors', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'multi-colorpicker',
            'id'        => 'ywgc_gift_this_product_colors',
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
                    'default'   => '#448A85'
                ),
                array(
                    'name'      => esc_html__( 'hover text color', 'yith-woocommerce-gift-cards' ),
                    'id'        => 'hover_text',
                    'default'   => '#1A4E43'
                ),
            ),
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift this product email options
         *
         */
        array(
            'name' => esc_html__( 'Gift this product email options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_gift_this_product_email_button_label'           => array(
            'id'      => 'ywgc_gift_this_product_email_button_label',
            'name'    => esc_html__( 'Button text', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( "Enter a text for the button displayed in the email sent to the gift card recipient.", 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'default' => esc_html__( 'Go to the product', 'yith-woocommerce-gift-cards' ),
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_this_product_email_button_redirect'                    => array(
            'name'    => esc_html__( 'Button redirect the user to', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id'      => 'ywgc_gift_this_product_button_redirect',
            'options' => array(
                'to_product_page'     => esc_html__( "Product page", 'yith-woocommerce-gift-cards' ),
                'to_customize_page'   => esc_html__( "Another page", 'yith-woocommerce-gift-cards' ),
            ),
            'default' => 'to_product_page',
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_this_product_redirected_page' => array(
            'name'     => esc_html__( '', 'yith-woocommerce-gift-cards' ),
            'desc'     => esc_html__( 'Set the page you want the user to be redirected to when the button in the gift card email is clicked on.', 'yith-woocommerce-gift-cards' ),
            'id'       => 'ywgc_gift_this_product_redirected_page',
            'type'     => 'single_select_page',
            'default'  => '',
            'class'    => 'chosen_select_nostd',
            'css'      => 'min-width:300px;',
            'desc_tip' => false,

        ),

        'ywgc_gift_this_product_add_to_cart' => array(
            'name'      => esc_html__( 'Automatically add the suggested product to the cart', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'        => 'ywgc_gift_this_product_add_to_cart',
            'desc'    => esc_html__( 'Add the recommended product to the recipient\'s cart' , 'yith-woocommerce-gift-cards' ),
            'default'   => 'yes',
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )
        ),
        'ywgc_gift_this_product_apply_gift_card' => array(
            'name'      => esc_html__( 'Automatically apply the gift card code to cart', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'        => 'ywgc_gift_this_product_apply_gift_card',
            'desc'    => esc_html__( 'Automatically apply the gift voucher code to the cart, without the user having to enter it manually.', 'yith-woocommerce-gift-cards' ),
            'default'   => 'yes',
            'deps'      => array(
                'id'    => 'ywgc_permit_its_a_present',
                'value' => 'yes',
            )

        ),
		array(
			'type' => 'sectionend',
		),

    ),
);

return apply_filters( 'yith_ywgc_gift_this_product_options_array', $gift_this_product_options );
