<?php
/**
 * GENERAL ARRAY OPTIONS
 */

$yith_stripe = sprintf( __( 'If you want to take advantage of this feature, you could consider purchasing the %s.', 'yith-woocommerce-one-click-checkout' ), '<a href="https://yithemes.com/themes/plugins/yith-woocommerce-stripe/">YITH WooCommerce Stripe Plugin</a>' );

$general = array(

	'general'  => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-one-click-checkout' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wocc-general-options'
		),

		array(
			'title'     => __( 'Activate after first order', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Activate "One-Click Checkout" features after the first order of the customer', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wocc-after-first-order'
		),

		array(
			'title'     => __( 'Activate with link', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Activate "One-Click Checkout" features after clicking on a link', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wocc-activate-with-link'
		),

		array(
			'title'     => __( 'Activate in shop page', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Activate plugin features also in shop page', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wocc-activate-in-loop'
		),

        array(
            'title'     => __( 'Activate for guest', 'yith-woocommerce-one-click-checkout' ),
            'desc'      => __( 'Activate the plugin features also for guest customers. If activated, the "One click" button redirects customers to the checkout page.', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'id'        => 'yith-wocc-activate-for-guest'
        ),

		array(
			'title' => __( 'Exclude Categories', 'yith-woocommerce-one-click-checkout' ),
			'desc'  => __( 'Exclude selected categories from one-click checkout features', 'yith-woocommerce-one-click-checkout' ),
			'type'  => 'yith_wocc_select_cat',
			'default' => '',
			'id'    => 'yith-wocc-excluded-cat'
		),

		array(
			'title'     => __( 'Invert Exclusion', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Use the elements in the exclusion list as the active ones', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wocc-exclusion-inverted'
		),

		array(
			'title'     => __( 'Redirect after creating the order', 'yith-woocommerce-one-click-checkout' ),
            'desc'      => __( 'Choose to which page you want to redirect users after one-click checkout.', 'yith-woocommerce-one-click-checkout' ),
            'id'        => 'yith-wocc-redirection-url',
            'default'   => 'pay',
            'type'      => 'yith-field',
            'yith-type' => 'radio',
            'options'   => array(
                ''          => __( 'To product page', 'yith-woocommerce-one-click-checkout' ),
                'pay'       => __( 'To payment page', 'yith-woocommerce-one-click-checkout' ),
                'thankyou'  => __( 'To thank you page', 'yith-woocommerce-one-click-checkout' ),
                'custom'  	=> __( 'To custom page', 'yith-woocommerce-one-click-checkout' ),
            )
		),

		array(
			'id'        => 'yith-wocc-custom-link',
			'title'     => __( 'Custom page redirection', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose the page to which you want to redirect users', 'yith-woocommerce-one-click-checkout' ),
			'type'      => 'single_select_page',
			'default'   => '',
			'class'     => 'yith-wocc wc-enhanced-select-nostd',
			'css'       => 'min-width:300px;',
			'desc_tip'  => true,
		),
		
		array(
			'title'     => __( 'Choose default shipping address', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose which address you want to show as default for shipping addresses selection.', 'yith-woocommerce-one-click-checkout' ),
			'id'        => 'yith-wocc-default-shipping-addr',
			'default'   => '',
            'type'      => 'yith-field',
            'yith-type' => 'select',
			'options'   => array(
				''          => __( 'None', 'yith-woocommerce-one-click-checkout' ),
				'billing'   => __( 'Billing Address', 'yith-woocommerce-one-click-checkout' ),
				'shipping'  => __( 'Shipping Address', 'yith-woocommerce-one-click-checkout' ),
			)
		),

		array(
			'title'     => __( 'Text of activation link', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Edit the One-Click Checkout activation link label', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'text',
			'default'   => __( 'Activate one-click checkout', 'yith-woocommerce-one-click-checkout' ),
			'id'        => 'yith-wocc-link-label'
		),

		array(
			'title'     => __( 'Button label', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Edit One-Click Checkout button label', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'text',
			'default'   => __( 'One-Click Purchase', 'yith-woocommerce-one-click-checkout' ),
			'id'        => 'yith-wocc-button-label'
		),

		array(
			'title'     => __( 'Button background', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose One-Click Checkout button background color', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'colorpicker',
			'default'   => '#ebe9eb',
			'id'        => 'yith-wocc-button-background'
		),

		array(
			'title'     => __( 'Button background on hover', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose the color of the One-Click Checkout button background on mouse hover', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'colorpicker',
			'default'   => '#dad8da',
			'id'        => 'yith-wocc-button-background-hover'
		),

		array(
			'title'     => __( 'Button text color', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose One-Click Checkout button text color', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'colorpicker',
			'default'   => '#515151',
			'id'        => 'yith-wocc-button-text'
		),

		array(
			'title'     => __( 'Button text color on hover', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose the color fo the One-Click Checkout button text on mouse hover', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'colorpicker',
			'default'   => '#515151',
			'id'        => 'yith-wocc-button-text-hover'
		),

		array(
			'title'     => __( 'Show form divider', 'yith-woocommerce-one-click-checkout' ),
			'desc'      => __( 'Choose to show a divider between "One-Click Checkout" button and "Add to Cart" button', 'yith-woocommerce-one-click-checkout' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wocc-show-form-divider'
		),

		array(
			'type'      => 'sectionend',
			'id'        => 'yith-wocc-general-options'
		),

		array(
			'title' => __( 'Stripe Integration', 'yith-woocommerce-one-click-checkout' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wocc-stripe-option'
		),

		array(
			'name'              => __( 'Enable stripe payment', 'yit' ),
			'desc'              => sprintf( __( 'Enable direct payment using Stripe. %s', 'yit' ), ( ! ( defined( 'YITH_WCSTRIPE_PREMIUM' ) && YITH_WCSTRIPE_PREMIUM ) ) ? $yith_stripe : '' ),
			'default'           => 'yes',
            'type'              => 'checkbox',
			'custom_attributes' => ( ! ( defined( 'YITH_WCSTRIPE_PREMIUM' ) && YITH_WCSTRIPE_PREMIUM ) ) ? array( 'disabled' => 'disabled' ) : false,
			'id'                => 'yith-wocc-stripe-integration'
		),

		array(
			'type'      => 'sectionend',
			'id'        => 'yith-wocc-stripe-option'
		),
	)
);

return apply_filters( 'yith_wocc_panel_general_options', $general );