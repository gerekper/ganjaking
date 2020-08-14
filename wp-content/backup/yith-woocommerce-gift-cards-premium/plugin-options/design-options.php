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
$design_options = array(

	'design' => array(
        /**
         *
         * Shop logo options
         *
         */
        array(
            'name' => esc_html__( 'Shop logo options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_shop_logo_on_gift_card' => array(
            'name' => esc_html__('Add your shop logo on gift cards', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywgc_shop_logo_on_gift_card',
            'desc' => esc_html__('Set if you want the shop logo to show up on the gift card template sent to the customers. We suggest you keep it disabled if your gift card template image contains your shop logo.', 'yith-woocommerce-gift-cards'),
            'default' => 'no',
        ),
        'ywgc_shop_logo_url'         => array (
            'name' => esc_html__( 'Upload your shop logo', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'upload',
            'id'   => 'ywgc_shop_logo_url',
            'desc' => esc_html__( 'Upload the logo you want to show in the gift card sent to customers.', 'yith-woocommerce-gift-cards' ),
            //banner 850x300, logo, 100x60
            'deps'      => array(
                'id'    => 'ywgc_shop_logo_on_gift_card',
                'value' => 'yes',
            )
        ),

        'ywgc_shop_logo_on_gift_card_after' => array(
            'name' => esc_html__('Add your shop logo after the gift card image', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywgc_shop_logo_on_gift_card_after',
            'default' => 'no',
            'deps'      => array(
                'id'    => 'ywgc_shop_logo_on_gift_card',
                'value' => 'yes',
            )
        ),
        'ywgc_shop_logo_after_alignment' => array(
            'name' => esc_html__('Logo alignment', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id' => 'ywgc_shop_logo_after_alignment',
            'options' => array(
                'left' => esc_html__( "Left", 'yith-woocommerce-gift-cards'),
                'center' => esc_html__( "Center", 'yith-woocommerce-gift-cards'),
                'right' => esc_html__( "Right", 'yith-woocommerce-gift-cards'),
            ),
            'default' => 'left',
            'deps'      => array(
                'id'    => 'ywgc_shop_logo_on_gift_card_after',
                'value' => 'yes',
            )
        ),

        'ywgc_shop_logo_on_gift_card_before' => array(
            'name' => esc_html__('Add your shop logo before the gift card image', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywgc_shop_logo_on_gift_card_before',
            'default' => 'no',
            'deps'      => array(
                'id'    => 'ywgc_shop_logo_on_gift_card',
                'value' => 'yes',
            )
        ),
        'ywgc_shop_logo_before_alignment' => array(
            'name' => esc_html__('Logo alignment', 'yith-woocommerce-gift-cards'),
            'type'    => 'yith-field',
            'yith-type' => 'radio',
            'id' => 'ywgc_shop_logo_before_alignment',
            'options' => array(
                'left' => esc_html__( "Left", 'yith-woocommerce-gift-cards'),
                'center' => esc_html__( "Center", 'yith-woocommerce-gift-cards'),
                'right' => esc_html__( "Right", 'yith-woocommerce-gift-cards'),
            ),
            'default' => 'left',
            'deps'      => array(
                'id'    => 'ywgc_shop_logo_on_gift_card_before',
                'value' => 'yes',
            )
        ),

        array(
            'type' => 'sectionend',
        ),



        /**
         *
         * Gift Card design options
         *
         */
        array(
            'name' => esc_html__( 'Gift card design options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'yith_gift_card_header_url'  => array (
            'name' => esc_html__( 'Default gift card image', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'upload',
            'id'   => 'ywgc_gift_card_header_url',
            'desc' => esc_html__( 'Upload a image that will be used by default for all your gift cards. You can, however, override it when you create a new gift card product and leave empty if you don\'t want to apply a default image.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_choose_design_title'          => array(
            'name'    => esc_html__( 'Title for “Choose your image” section', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_choose_design_title',
            'desc'    => esc_html__( " Enter a title for the 'Choose your image' area on your gift card page.", 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => 'placeholder="' . __( 'write the choose image area title', 'yith-woocommerce-gift-cards' ) . '"',

            'default' => esc_html__( "Choose your image", 'yith-woocommerce-gift-cards'),
        ),
        'ywgc_template_design'        => array(
            'name'    => esc_html__( 'Enable the gallery', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_template_design',
            'desc'    => esc_html__( 'Allow users to pick the gift card image from those available in the gallery. Note: images that can be used by customers have to be uploaded through the Media gallery. To make the search easier, you can group images into categories (e.g. Christmas, Easter, Birthday, etc.) through this link: ', 'yith-woocommerce-gift-cards' ) . ' <a href="' . admin_url( 'edit-tags.php?taxonomy=giftcard-category&post_type=attachment' ) . '" title="' . esc_html__( 'Set your gallery categories.', 'yith-woocommerce-gift-cards' ) . '">' . esc_html__( 'Set your template categories', 'yith-woocommerce-gift-cards' ) . '</a>',
            'default' => 'yes',
        ),
        'ywgc_template_design_number_to_show'      => array(
            'id'      => 'ywgc_template_design_number_to_show',
            'name'    => esc_html__( 'How many images to show', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( 'Set how many gift card images to show on the gift card page. Other designs will be shown when the customer clicks on "View all" button.', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'min'      => 0,
            'step'     => 1,
            'default' => '3',
            'deps'      => array(
                'id'    => 'ywgc_template_design',
                'value' => 'yes',
            )
        ),
        'ywgc_template_design_view_all_button'      => array(
            'id'      => 'ywgc_template_design_view_all_button',
            'name'    => esc_html__( 'Text for "View all" button', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'custom_attributes' => 'placeholder="' . __( 'write the viev all button text', 'yith-woocommerce-gift-cards' ) . '"',
            'default' => esc_html__( "VIEW ALL", 'yith-woocommerce-gift-cards'),
            'deps'      => array(
                'id'    => 'ywgc_template_design',
                'value' => 'yes',
            )
        ),

        'ywgc_show_preset_title'      => array(
            'id'      => 'ywgc_show_preset_title',
            'name'    => esc_html__( 'Show image title', 'yith-woocommerce-gift-cards' ),
            'desc'    => esc_html__( 'Enable if you want to show a title below every image in the gallery.', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'default' => 'no',
            'deps'      => array(
                'id'    => 'ywgc_template_design',
                'value' => 'yes',
            )
        ),

        'ywgc_custom_design'          => array(
            'name'    => esc_html__( 'Custom Image upload', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_custom_design',
            'desc'    => esc_html__( 'Enable if the customer can upload a custom image/photo for the gift card.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),

        'ywgc_custom_design_suggested_size'          => array(
            'name'    => esc_html__( 'Enter a recommended image size (in pixels) for custom images', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_custom_design_suggested_size',
            'desc'    => esc_html__( 'Enter a recommended size for the uploaded image that fits your gift card layout.', 'yith-woocommerce-gift-cards' ),
            'default' => '180x330',
            'custom_attributes' => 'placeholder="' . __( 'write the suggest image size in pixels', 'yith-woocommerce-gift-cards' ) . '"',
            'deps'      => array(
                'id'    => 'ywgc_custom_design',
                'value' => 'yes',
            )
        ),

        'ywgc_custom_image_max_size' => array (
            'name'              => esc_html__( 'Set a max size in MB for custom images (required)', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
            'id'       => 'ywgc_custom_image_max_size',
            'desc'     => esc_html__( 'Set up a maximum size (in MB) for the custom images uploaded by customers. Enter 0 if you don\'t want to set any limit.', 'yith-woocommerce-gift-cards' ),
            'min'      => 0,
            'step'     => 1,
            'required' => 'required',
            'default'           => 1,
            'deps'      => array(
                'id'    => 'ywgc_custom_design',
                'value' => 'yes',
            )
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * Gift Card Customization Layout
         *
         */
//        array(
//            'name' => esc_html__( 'Gift Card Customization Layout', 'yith-woocommerce-gift-cards' ),
//            'type' => 'title',
//        ),
//        'ywgc_customization_layout_mode' => array(
//            'name' => esc_html__('Select a Customization layout for gift card page', 'yith-woocommerce-gift-cards'),
//            'type'    => 'yith-field',
//            'yith-type' => 'radio',
//            'id' => 'ywgc_customization_layout_mode',
//            'options' => array(
//                'ywgc_customization_layout_mode_in_page' => 'In the page',
//                'ywgc_customization_layout_mode_on_modal' => 'On modal',
//                'ywgc_customization_layout_mode_multistep_modal' => 'Multistep modal',
//            ),
//            'default' => 'ywgc_customization_layout_mode_in_page',
//        ),
//        array(
//            'type' => 'sectionend',
//        ),


        /**
         *
         * Plugin color
         *
         */
        array(
            'name' => esc_html__( 'Plugin design options', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_plugin_main_color' => array(
            'name' => esc_html__('Plugin main color', 'yith-woocommerce-gift-cards'),
            'desc'     => esc_html__( 'Select the plugin main color.', 'yith-woocommerce-gift-cards' ),
            'type'      => 'yith-field',
            'yith-type' => 'colorpicker',
            'default'   => '#000000',
            'id' => 'ywgc_plugin_main_color',
        ),
        array(
            'type' => 'sectionend',
        ),


    ),
);

return apply_filters( 'yith_ywgc_design_options_array', $design_options );
