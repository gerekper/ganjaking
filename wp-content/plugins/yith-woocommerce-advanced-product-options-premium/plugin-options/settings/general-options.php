<?php
/**
 * General settings Tab
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$general_settings = array(

    'settings-general' => array(
        'general-options'         => array(
            'id'    => 'yith_wapo_general_options',
            // translators: [ADMIN] General Settings tab option
            'title' => __( 'General options', 'yith-woocommerce-product-add-ons' ),
            'type'  => 'title',
            'desc'  => '',
        ),
        'options-position'        => array(
            'id'        => 'yith_wapo_options_position',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Options position in product page', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Choose the position for the options blocks.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'radio',
            'default'   => 'before',
            'options'   => array(
                // translators: [ADMIN] General Settings tab option
                'before' => __( 'Before "Add to cart"', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] General Settings tab option
                'after'  => __( 'After "Add to cart"', 'yith-woocommerce-product-add-ons' ),
            ),
        ),
        'button-in-shop'          => array(
            'id'        => 'yith_wapo_button_in_shop',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'In WooCommerce pages show', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Choose the button to display on WooCommerce pages.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'radio',
            'default'   => 'select',
            'options'   => array(
                // translators: [ADMIN] General Settings tab option
                'select' => __( '"Select options" button', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] General Settings tab option
                'add'    => __( '"Add to cart" button', 'yith-woocommerce-product-add-ons' ),
            ),
        ),
        'select-options-label'    => array(
            'id'        => 'yith_wapo_select_options_label',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Label for "Select options" button', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Enter the text for the "Select options" button.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'text',
            'default'   => 'Select options',
            'deps'      => array(
                'id'    => 'yith_wapo_button_in_shop',
                'value' => 'select',
                'type'  => 'hide-disable',
            ),
        ),
        'replace-product-price'   => array(
            'id'        => 'yith_wapo_replace_product_price',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Change the product base price with the calculated total', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Enable to replace the product base price (below the title) with the newly calculated total of the selected options.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'hide-button-if-required' => array(
            'id'        => 'yith_wapo_hide_button_if_required',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Hide "Add to cart" until the required options are selected', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Enable to hide the "Add to cart" button until the user selects the required options.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'total-price-box'         => array(
            'id'        => 'yith_wapo_total_price_box',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Total price box', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Choose what information to show in the total price box.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'select',
            'class'     => 'wc-enhanced-select',
            'yith-type' => 'radio',
            'default'   => 'all',
            'options'   => array(
                // translators: [ADMIN] General Settings tab option
                'all'          => __( 'Show product price and total options', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] General Settings tab option
                'hide_options' => __( 'Show the final total but hide options total only if the value is 0', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] General Settings tab option
                'only_final'   => __( 'Show only the final total', 'yith-woocommerce-product-add-ons' ),
                // translators: [ADMIN] General Settings tab option
                'hide_all'     => __( 'Hide price box on the product page', 'yith-woocommerce-product-add-ons' ),
            ),
        ),
        'hide-titles-and-images'  => array(
            'id'        => 'yith-wapo-hide-titles-and-images',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Hide titles and images of options groups', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Enable to hide all titles and images set in the "Display & Style" tab of the options.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'hide-images'             => array(
            'id'        => 'yith_wapo_hide_images',
            // translators: [ADMIN] General Settings tab option
            'name'      => __( 'Hide images of the single options', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] General Settings tab option
            'desc'      => __( 'Enable to hide all the images uploaded in the "populate options" tab of the options.', 'yith-woocommerce-product-add-ons' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),
        'general-options-end'     => array(
            'id'   => 'yith-wapo-general-option',
            'type' => 'sectionend',
        ),
    )

);

return apply_filters( 'yith_wapo_general_options_array', $general_settings );