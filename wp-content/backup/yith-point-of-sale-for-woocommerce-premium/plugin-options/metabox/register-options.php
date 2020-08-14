<?php
/**
 * @var YITH_POS_Register $register
 */
// Exit if accessed directly
!defined( 'YITH_POS' ) && exit();

$indexed_payment_methods = yith_pos_get_indexed_payment_methods();


return array(
    'name'                     => array(
        'type'              => 'text',
        'label'             => __( 'Register name', 'yith-point-of-sale-for-woocommerce' ),
        'desc'              => __( 'Enter a name to identify this Register.', 'yith-point-of-sale-for-woocommerce' ) . yith_pos_get_required_field_message(),
        'required'          => true,
        'class'             => 'yith-pos-required-field',
        'custom_attributes' => 'data-message="' . __( 'The Register name is required.', 'yith-point-of-sale-for-woocommerce' ) . '"',
    ),
    'store_id'                 => array(
        'type' => 'hidden'
    ),
    'scanner_enabled'            => array(
        'type'  => 'onoff',
        'label' => __( 'Scan barcodes', 'yith-point-of-sale-for-woocommerce' ),
        'desc'  => __( 'If enabled, products can be added to the cart by scanning their barcodes.', 'yith-point-of-sale-for-woocommerce' ),
        'std'   => 'yes',
    ),
    /*'guest_enabled'            => array(
	    'type'  => 'onoff',
	    'label' => __( 'Guest Register', 'yith-point-of-sale-for-woocommerce' ),
	    'desc'  => __( 'If enabled the customer can access to this register without login. (Example: the self-register of some supermarkets.)', 'yith-point-of-sale-for-woocommerce' ),
	    'std'   => 'no',
    ),*/
    'payment_methods'          => array(
        'type'    => 'checkbox-array',
        'label'   => __( 'Payment methods', 'yith-point-of-sale-for-woocommerce' ),
        'class'   => 'yith-pos-register-payment-methods no-bottom',
        'options' => $indexed_payment_methods,
        'std'     => array_keys( $indexed_payment_methods ),
    ),
    'what_to_show'             => array(
        'type'    => 'radio',
        'label'   => __( 'Select products to show', 'yith-point-of-sale-for-woocommerce' ),
        'class'   => 'no-bottom',
        'options' => array(
            'all'      => __( 'All products', 'yith-point-of-sale-for-woocommerce' ),
            'specific' => __( 'Specific products / product categories', 'yith-point-of-sale-for-woocommerce' ),
        ),
        'std'     => 'all',
    ),
    'show_categories'          => array(
        'type'  => 'show-categories',
        'label' => '',
        'deps'  => array(
            'id'    => '_what_to_show',
            'value' => 'specific',
            'type'  => 'disable',
        ),
    ),
    'show_products'            => array(
        'type'  => 'show-products',
        'label' => '',
        'class' => 'inactive',
        'deps'  => array(
            'id'    => '_what_to_show',
            'value' => 'specific',
            'type'  => 'disable',
        ),
    ),
    'how_to_show_in_dashboard' => array(
        'type'    => 'radio',
        'label'   => __( 'In Register dashboard show', 'yith-point-of-sale-for-woocommerce' ),
        'desc'    => implode( '<br />', array(
            __( 'Set Categories > Products if you want to show your product categories and the products of each category in your Register dashboard.', 'yith-point-of-sale-for-woocommerce' ),
            __( 'Set Only Products if you want to show just your products without categories', 'yith-point-of-sale-for-woocommerce' )
        ) ),
        'options' => array(
            'categories' => __( 'Categories > Products', 'yith-point-of-sale-for-woocommerce' ),
            'products'   => __( 'Only products', 'yith-point-of-sale-for-woocommerce' ),
        ),
        'std'     => 'categories',
    ),
    'visibility'               => array(
        'type'              => 'radio',
        'label'             => __( 'Register visibility', 'yith-point-of-sale-for-woocommerce' ),
        'class'             => 'no-bottom',
        'desc'              => __( 'Set if you want to show this Register for specific Cashiers.', 'yith-point-of-sale-for-woocommerce' ) . '<strong>' . __( 'You must set at least one Cashier in Store.', 'yith-point-of-sale-for-woocommerce' ) . '</strong>',
        'options'           => array(
            'all'               => __( 'All', 'yith-point-of-sale-for-woocommerce' ),
            'specific_cashiers' => __( 'Hide / Show to specific Cashiers', 'yith-point-of-sale-for-woocommerce' ),
        ),
        'std'               => 'all',
        'custom_attributes' => 'data-message="' . __( 'You must add at least one Cashier to activate this option', 'yith-point-of-sale-for-woocommerce' ) . '"',
    ),
    'visibility_cashiers'      => array(
        'label' => '',
        'type'  => 'show-cashiers',
        'deps'  => array(
            'id'    => '_visibility',
            'value' => 'specific_cashiers',
            'type'  => 'disable',
        ),
    ),

    'receipt_id'    => array(
        'label'   => __( 'Enable receipts', 'yith-point-of-sale-for-woocommerce' ),
        'type'    => 'select',
        'options' => yith_pos_get_receipts_options(),
        'class'   => 'wc-enhanced-select',
    ),

    'closing_report_enabled' => array(
        'type'  => 'onoff',
        'label' => __( 'Enable Register closing report', 'yith-point-of-sale-for-woocommerce' ),
        'desc'  => __( 'If enabled, the Cashier will see the final report before closing the Register.', 'yith-point-of-sale-for-woocommerce' ),
        'std'   => 'yes',
    ),

    'closing_report_note_enabled' => array(
	    'type'  => 'onoff',
	    'label' => __( 'Enable Register final notes', 'yith-point-of-sale-for-woocommerce' ),
	    'desc'  => __( 'If enabled, the Cashier will be able to add notes before closing the Register.', 'yith-point-of-sale-for-woocommerce' ),
	    'std'   => 'no',
	    'deps'  => array(
		    'id'    => '_closing_report_enabled',
		    'value' => 'yes',
		    'type'  => 'disable',
	    ),
    )
);