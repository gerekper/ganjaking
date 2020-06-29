<?php
/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general' => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-added-to-cart-popup' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wacp-general-options',
		),

		array(
			'title'   => __( 'Popup Size', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'    => __( 'Set popup size.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'    => 'yith_wacp_box_size',
			'default' => array(
				'width'  => '700',
				'height' => '700',
			),
			'id'      => 'yith-wacp-box-size',
		),

		array(
			'title'     => __( 'Popup Animation', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Select popup animation', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'fade-in'         => __( 'Fade In', 'yith-woocommerce-added-to-cart-popup' ),
				'slide-in-right'  => __( 'Slide In (Right)', 'yith-woocommerce-added-to-cart-popup' ),
				'slide-in-left'   => __( 'Slide In (Left)', 'yith-woocommerce-added-to-cart-popup' ),
				'slide-in-bottom' => __( 'Slide In (Bottom)', 'yith-woocommerce-added-to-cart-popup' ),
				'slide-in-top'    => __( 'Slide In (Top)', 'yith-woocommerce-added-to-cart-popup' ),
				'tred-flip-h'     => __( '3D Flip (Horizontal)', 'yith-woocommerce-added-to-cart-popup' ),
				'tred-flip-v'     => __( '3D Flip (Vertical)', 'yith-woocommerce-added-to-cart-popup' ),
				'scale-up'        => __( 'Scale Up', 'yith-woocommerce-added-to-cart-popup' ),
			),
			'default'   => 'fade-in',
			'id'        => 'yith-wacp-box-animation',
		),

		array(
			'title'         => __( 'Enable Popup', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'          => __( 'On Archive Page', 'yith-woocommerce-added-to-cart-popup' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'id'            => 'yith-wacp-enable-on-archive',
			'checkboxgroup' => 'start',
		),

		array(
			'title'         => '',
			'desc'          => __( 'On Single Product Page', 'yith-woocommerce-added-to-cart-popup' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'id'            => 'yith-wacp-enable-on-single',
			'checkboxgroup' => 'end',
		),

		array(
			'title'     => __( 'Popup message', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => '',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Product successfully added to your cart!', 'yith-woocommerce-added-to-cart-popup' ),
			'id'        => 'yith-wacp-popup-message',
		),

		array(
			'title'     => __( 'Select content', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose whether to show the added product or the cart', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'product' => __( 'Added product', 'yith-woocommerce-added-to-cart-popup' ),
				'cart'    => __( 'Cart', 'yith-woocommerce-added-to-cart-popup' ),
			),
			'default'   => 'product',
			'id'        => 'yith-wacp-layout-popup',
		),

		array(
			'title'     => __( 'Show product info', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show the product info in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-info',
		),

		array(
			'title'     => __( 'Show product thumbnail', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show the product thumbnail in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-thumbnail',
		),

		array(
			'id'      => 'yith-wacp-image-size',
			'title'   => __( 'Thumbnail Size', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'    => sprintf( __( 'Set image size (in px). After changing these settings, you may need to %s.', 'yith-woocommerce-added-to-cart-popup' ), '<a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">' . __( 'regenerate your thumbnails', 'yith-woocommerce-added-to-cart-popup' ) . '</a>' ),
			'type'    => 'yith_wacp_image_size',
			'default' => array(
				'width'  => '170',
				'height' => '170',
				'crop'   => 1,
			),
			'deps'    => array(
				'id'    => 'yith-wacp-show-thumbnail',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Show product variations', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show product variations details ( only available of variable product ).', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-product-variation',
		),

		array(
			'title'     => __( 'Show cart total', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show cart total in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-cart-totals',
		),

		array(
			'title'     => __( 'Show shipping fees', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show shipping fees in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-cart-shipping',
		),

		array(
			'title'     => __( 'Show tax amount', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show tax cart amount in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-cart-tax',
		),

		array(
			'title'     => __( 'Show "View Cart" Button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show "View Cart" button in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-go-cart',
		),

		array(
			'title'     => __( '"View Cart" Button Text', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Text for "View Cart" button', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'View Cart', 'yith-woocommerce-added-to-cart-popup' ),
			'id'        => 'yith-wacp-text-go-cart',
			'deps'      => array(
				'id'    => 'yith-wacp-show-go-cart',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Show "Continue Shopping" Button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show "Continue Shopping" button in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-continue-shopping',
		),

		array(
			'title'     => __( '"Continue Shopping" Button Text', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Text for "Continue Shopping" button', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Continue Shopping', 'yith-woocommerce-added-to-cart-popup' ),
			'id'        => 'yith-wacp-text-continue-shopping',
			'deps'      => array(
				'id'    => 'yith-wacp-show-continue-shopping',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Show "Go to Checkout" Button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show "Go to Checkout" button in the popup', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-go-checkout',
		),

		array(
			'title'     => __( '"Go to Checkout" Button Text', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Text for "Go to Checkout" button', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Checkout', 'yith-woocommerce-added-to-cart-popup' ),
			'id'        => 'yith-wacp-text-go-checkout',
			'deps'      => array(
				'id'    => 'yith-wacp-show-go-checkout',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Enable on mobile', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Enable the plugin features on mobile devices', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-enable-mobile',
		),

		defined( 'YITH_YWRAQ_INIT' ) && YITH_YWRAQ_INIT ? array(
			'title'     => __( 'Enable for "Request A Quote" button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Enable the plugin features also for YITH WooCommerce Request A Quote', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wacp-enable-raq',
		) : array(),

		defined( 'YITH_WFBT_INIT' ) && YITH_WFBT_INIT ? array(
			'title'     => __( 'Enable for "Frequently Bought Together" button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Enable the plugin features also for YITH WooCommerce Frequently Bought Together Premium', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-wacp-enable-wfbt',
		) : array(),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wacp-general-options',
		),

		array(
			'title' => __( 'Suggested Products', 'yith-woocommerce-added-to-cart-popup' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wacp-related-options',
		),

		array(
			'title'     => __( 'Show suggested products', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose to show suggested products in popup.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-wacp-show-related',
		),

		array(
			'title'     => __( '"Suggested Products" title', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'The title for "Suggested Products" section.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Suggested Products', 'yith-woocommerce-added-to-cart-popup' ),
			'id'        => 'yith-wacp-related-title',
			'deps'      => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Number of suggested products', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose how many suggested products to show', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => 4,
			'min'       => 1,
			'id'        => 'yith-wacp-related-number',
			'deps'      => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Columns of suggested products', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Choose how many columns to show in suggested products', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'option'    => array( 'min' => 0, 'max' => 6 ),
			'default'   => 4,
			'step'      => 1,
			'id'        => 'yith-wacp-related-columns',
			'deps'      => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'        => 'yith-wacp-suggested-products-type',
			'title'     => __( 'Suggested products type', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Select suggested products type.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'related'  => __( 'Related Products', 'yith-woocommerce-added-to-cart-popup' ),
				'crossell' => __( 'Cross-sell Products', 'yith-woocommerce-added-to-cart-popup' ),
				'upsell'   => __( 'Up-sell Products', 'yith-woocommerce-added-to-cart-popup' ),
			),
			'default'   => 'related',
			'deps'      => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'      => 'yith-wacp-related-products',
			'title'   => __( 'Select products', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'    => __( 'Select suggested products. If filled, these settings will override what set in the above option.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'    => 'yith_wacp_select_prod',
			'default' => '',
			'deps'    => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'        => 'yith-wacp-suggested-add-to-cart',
			'title'     => __( 'Show "Add to cart" button', 'yith-woocommerce-added-to-cart-popup' ),
			'desc'      => __( 'Show the "add to cart" button for suggested products.', 'yith-woocommerce-added-to-cart-popup' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'deps'      => array(
				'id'    => 'yith-wacp-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wacp-related-options',
		),
	),
);

return apply_filters( 'yith_wacp_panel_general_options', $general );
