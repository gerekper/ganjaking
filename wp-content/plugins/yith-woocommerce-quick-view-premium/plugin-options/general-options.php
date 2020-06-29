<?php

$link_wcwl = '<a href="http://wordpress.org/plugins/yith-woocommerce-wishlist/">YITH WooCommerce Wishlist</a>';

$general_settings = array(

	'general' => array(

		10 => array(
			'title' => __( 'General Options', 'yith-woocommerce-quick-view' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcqv-general-options',
		),

		20 => array(
			'id'        => 'yith-wcqv-enable',
			'name'      => __( 'Enable Quick View', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		30 => array(
			'id'        => 'yith-wcqv-enable-mobile',
			'name'      => __( 'Enable Quick View on mobile', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Enable quick view features on mobile device too', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		40 => array(
			'id'        => 'yith-wcqv-enable-wishlist',
			'name'      => __( 'Enable Quick View on wishlist', 'yith-woocommerce-quick-view' ),
			'desc'      => sprintf( __( 'Enable quick view on wishlist table (available only with modal window). You need %s installed.', 'yith-woocommerce-quick-view' ), $link_wcwl ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		50 => array(
			'id'        => 'yith-wcqv-enable-lightbox',
			'name'      => __( 'Enable Lightbox', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Enable lightbox. Product images will open in a lightbox.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		60 => array(
			'id'            => 'yith-wcqv-enable-nav',
			'name'          => __( 'Quick View Navigation', 'yith-woocommerce-quick-view' ),
			'desc'          => __( 'Enable product navigation on quick view. NOTE: only available on modal window style.', 'yith-woocommerce-quick-view' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		),

		70 => array(
			'id'            => 'yith-wcqv-enable-nav-same-category',
			'desc'          => __( 'Enable navigation in the same product category', 'yith-woocommerce-quick-view' ),
			'type'          => 'checkbox',
			'default'       => 'no',
			'checkboxgroup' => 'end',
		),

		80 => array(
			'id'        => 'yith-wcqv-nav-style',
			'name'      => __( 'Quick View Navigation Style', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'reveal'  => __( 'Slide ( thumbnail and product name )', 'yith-woocommerce-quick-view' ),
				'diamond' => __( 'Rotate ( thumbnail )', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'reveal',
			'class'     => 'wc-enhanced-select',
		),

		90 => array(
			'id'        => 'yith-wcqv-modal-type',
			'name'      => __( 'Quick View Type', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'yith-modal'  => __( 'Modal Window', 'yith-woocommerce-quick-view' ),
				'yith-inline' => __( 'Cascading', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'yith-modal',
			'class'     => 'wc-enhanced-select',
		),

		95 => array(
			'id'        => 'yith-wcqv-closing-cascading',
			'name'      => __( 'Closing cascading quick view', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'not-scroll' => __( 'Close Quick View and stay in the same row', 'yith-woocommerce-quick-view' ),
				'scroll-up'  => __( 'Close quick view and scroll to the row of selected product', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'not-scroll',
			'class'     => 'wc-enhanced-select',
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-inline',
			),
		),

		100 => array(
			'id'        => 'yith-quick-view-modal-effect',
			'name'      => __( 'Select modal effect', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'slide-in' => __( 'Slide in', 'yith-woocommerce-quick-view' ),
				'fade-in'  => __( 'Fade in', 'yith-woocommerce-quick-view' ),
				'scale-up' => __( 'Scale up', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'slide-in',
			'class'     => 'wc-enhanced-select',
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-modal',
			),
		),

		110 => array(
			'id'        => 'yith-wcqv-enable-loading-effect',
			'name'      => __( 'Modal window loading', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Enable overlay during loading of the modal window.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-modal',
			),
		),

		120 => array(
			'id'        => 'yith-wcqv-enable-loading-text',
			'name'      => __( 'Modal Loading Text', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Enter here the text to show during quick view loading when modal overlay is enabled.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Loading...', 'yith-woocommerce-quick-view' ),
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-modal',
			),
		),

		130 => array(
			'id'        => 'yith-quick-view-modal-width',
			'title'     => __( 'Modal Width', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Set width of modal window.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => '1000',
			'min'       => 1,
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-modal',
			),
		),

		140 => array(
			'id'        => 'yith-quick-view-modal-height',
			'title'     => __( 'Modal Height', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Set height of modal window.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => '500',
			'min'       => 1,
			'deps'      => array(
				'id'    => 'yith-wcqv-modal-type',
				'value' => 'yith-modal',
			),
		),

		150 => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcqv-general-options-end',
		),

		160 => array(
			'title' => __( 'Button Options', 'yith-woocommerce-quick-view' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcqv-button-options',
		),

		170 => array(
			'id'        => 'yith-wcqv-button-type',
			'name'      => __( 'Quick View Button Type', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Label for the quick view button in the WooCommerce loop.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'button' => __( 'Use button', 'yith-woocommerce-quick-view' ),
				'icon'   => __( 'Use icon', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'button',
			'class'     => 'wc-enhanced-select',
		),

		180 => array(
			'id'        => 'yith-wcqv-button-icon',
			'name'      => __( 'Quick View Button Icon', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Icon for the quick view button in the WooCommerce loop.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'default'   => YITH_WCQV_ASSETS_URL . '/image/qv-icon.png',
			'deps'      => array(
				'id'    => 'yith-wcqv-button-type',
				'value' => 'icon',
			),
		),

		190 => array(
			'id'        => 'yith-wcqv-button-label',
			'name'      => __( 'Quick View Button Label', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Label for the quick view button in the WooCommerce loop.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Quick View', 'yith-woocommerce-quick-view' ),
			'deps'      => array(
				'id'    => 'yith-wcqv-button-type',
				'value' => 'button',
			),
		),

		200 => array(
			'id'        => 'yith-wcqv-button-position',
			'name'      => __( 'Quick View Button Position', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Position of the quick view button.', 'yith-woocommerce-quick-view' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'add-cart' => __( 'After \'add to cart\' button', 'yith-woocommerce-quick-view' ),
				'image'    => __( 'Inside product image', 'yith-woocommerce-quick-view' ),
			),
			'default'   => 'add-cart',
			'class'     => 'wc-enhanced-select',
		),

		210 => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcqv-button-options-end',
		),
	),
);

return apply_filters( 'yith_wcqv_panel_general_settings', $general_settings );