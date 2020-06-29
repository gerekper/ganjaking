<?php
/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general' => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-recently-viewed-products' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wrvp-general-options'
		),

		array(
			'id'        => 'yith-wrvp-cookie-time',
			'title'     => __( 'Set cookie time', 'yith-woocommerce-recently-viewed-products' ),
			'desc'      => __( 'Set the duration (days) of the cookie that tracks customer viewed products.', 'yith-woocommerce-recently-viewed-products' ),
            'type'      => 'yith-field',
            'yith-type' => 'number',
			'default'   => '30',
            'min'       => '1'
		),

		array(
			'id'             => 'yith-wrvp-section-title',
			'title'          => __( 'Section title', 'yith-woocommerce-recently-viewed-products' ),
			'desc'           => __( 'The title of the plugin shortcode', 'yith-woocommerce-recently-viewed-products' ),
            'type'           => 'yith-field',
            'yith-type'      => 'text',
			'default'        => __( 'You may be interested in', 'yith-woocommerce-recently-viewed-products' ),
			'css'            => 'min-width:300px;'

		),

		array(
			'id'             => 'yith-wrvp-view-all-text',
			'title'          => __( '"View All" link text', 'yith-woocommerce-recently-viewed-products' ),
			'desc'           => __( 'Label for link to display all products.', 'yith-woocommerce-recently-viewed-products' ),
            'type'           => 'yith-field',
            'yith-type'      => 'text',
			'default'        => __( 'View All', 'yith-woocommerce-recently-viewed-products' ),
			'css'            => 'min-width:300px;'
		),

		array(
			'id'             => 'yith-wrvp-type-products',
			'title'          => __( 'Select which products to show', 'yith-woocommerce-recently-viewed-products' ),
			'desc'           => '',
            'type'           => 'yith-field',
            'yith-type'      => 'radio',
			'options'        => array(
				'viewed'  => __( 'Only viewed products', 'yith-woocommerce-recently-viewed-products' ),
				'similar' => __( 'Includes similar products', 'yith-woocommerce-recently-viewed-products' )
			),
			'default'        => 'viewed'
		),

		array(
			'id'                => 'yith-wrvp-type-similar-products',
			'title'             => __( 'Get similar products by', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Choose to get similar products by categories, tags or both', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'radio',
			'options'           => array(
				'cats' => __( 'Categories', 'yith-woocommerce-recently-viewed-products' ),
				'tags' => __( 'Tags', 'yith-woocommerce-recently-viewed-products' ),
				'both' => __( 'Both', 'yith-woocommerce-recently-viewed-products' )
			),
			'default'           => 'both',
            'deps'              => array(
                'id'    => 'yith-wrvp-type-products',
                'value' => 'similar',
                'type'  => 'hide'
            )
		),

		array(
			'id'                => 'yith-wrvp-num-tot-products',
			'title'             => __( 'Set number of products', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Set how many products to show in plugin section (set -1 to display all).', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'number',
			'default'           => '6',
            'min'               => '-1'
		),

		array(
			'id'                => 'yith-wrvp-num-visible-products',
			'title'             => __( 'Set products per row', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Set how many products to show per row.', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'number',
			'default'           => '4',
            'min'               => '1'
		),

		array(
			'id'             => 'yith-wrvp-order-products',
			'title'          => __( 'Products order by', 'yith-woocommerce-recently-viewed-products' ),
			'desc'           => __( 'Choose in which order the products should be shown.', 'yith-woocommerce-recently-viewed-products' ),
            'type'           => 'yith-field',
            'yith-type'      => 'radio',
			'options'        => array(
				'rand'     => __( 'Random', 'yith-woocommerce-recently-viewed-products' ),
				'viewed'   => __( 'Latest viewed', 'yith-woocommerce-recently-viewed-products' ),
				'sales'    => __( 'Sales', 'yith-woocommerce-recently-viewed-products' ),
				'newest'   => __( 'Newest', 'yith-woocommerce-recently-viewed-products' ),
				'high-low' => __( 'Price: High to Low', 'yith-woocommerce-recently-viewed-products' ),
				'low-high' => __( 'Price: Low to High', 'yith-woocommerce-recently-viewed-products' ),
			),
			'default'        => 'rand'

		),

		array(
			'id'            => 'yith-wrvp-hide-out-of-stock',
			'title'         => __( 'Hide out-of-stock products', 'yith-woocommerce-recently-viewed-products' ),
			'desc'          => __( 'Choose whether to exclude products that are out-of-stock', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'onoff',
			'default'       => 'no'
		),
		
		array(
			'id'            => 'yith-wrvp-hide-free',
			'title'         => __( 'Hide free products', 'yith-woocommerce-recently-viewed-products' ),
			'desc'          => __( 'Choose whether to exclude products that are free', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'onoff',
			'default'       => 'no'
		),

		array(
			'id'            => 'yith-wrvp-excluded-purchased',
			'title'         => __( 'Excluded purchased products', 'yith-woocommerce-recently-viewed-products' ),
			'desc'          => __( 'Choose whether to exclude products that customer has already purchased', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'onoff',
			'default'       => 'no'
		),

		array(
			'id'                => 'yith-wrvp-cat-most-viewed',
			'title'             => __( 'Only the most viewed category', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Show only products of the most viewed category by the customer.', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'onoff',
			'default'           => 'no'
		),

		array(
			'id'                => 'yith-wrvp-slider',
			'title'             => __( 'Enable slider', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Choose whether the product list section has to be shown as slider', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'onoff',
			'default'           => 'yes'
		),

		array(
			'id'                => 'yith-wrvp-slider-autoplay',
			'title'             => __( 'Enable slider autoplay', 'yith-woocommerce-recently-viewed-products' ),
			'desc'              => __( 'Choose whether to enable autoplay for sliders.', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'onoff',
			'default'           => 'yes',
            'deps'              => array(
                'id'    => 'yith-wrvp-slider',
                'value' => 'yes',
                'type'  => 'hide'
            ),
		),

		array(
			'id'             => 'yith-wrvp-show-on-single',
			'title'          => __( 'Add shortcode in single product', 'yith-woocommerce-recently-viewed-products' ),
			'desc'           => __( 'Choose to add the shortcode in single product page', 'yith-woocommerce-recently-viewed-products' ),
            'type'           => 'yith-field',
            'yith-type'      => 'onoff',
			'default'        => 'yes',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wrvp-end-general-options'
		),

		array(
			'title' => __( 'Options for the "Recently Viewed Products" page', 'yith-woocommerce-recently-viewed-products' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wrvp-page-options'
		),

		array(
			'id'            => 'yith-wrvp-nofound-msg',
			'title'         => __( 'Message for no product found', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'text',
			'desc'          => __( 'Set the message for the recently viewed products page when no product has been found.', 'yith-woocommerce-recently-viewed-products' ),
			'default'       => __( 'You have not viewed any product yet.', 'yith-woocommerce-recently-viewed-products' )
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wrvp-end-page-options'
		),

		array(
			'title' => __( 'Email Features', 'yith-woocommerce-recently-viewed-products' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wrvp-mail-options'
		),

		array(
			'id'                => 'yith-wrvp-email-period',
			'title'             => __( 'Schedule email', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'number',
			'desc'              => __( 'Set how many days after their last login the system should send the email to users.', 'yith-woocommerce-recently-viewed-products' ),
			'default'           => '7',
			'min'               => '1'
		),

		array(
			'id'      => 'yith-wrvp-image-size',
			'name'    => __( 'Thumbnail Size', 'yith-woocommerce-recently-viewed-products' ),
			'desc'    => sprintf( __( 'Set product image size (in px). After changing this option, you may need to %s.', 'yith-wacp' ), '<a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">' . __( 'regenerate your thumbnails', 'yith-woocommerce-recently-viewed-products' ) . '</a>' ),
			'type'    => 'ywrvp_image_size',
			'default' => array(
				'width'  => '80',
				'height' => '80',
				'crop'   => 1
			)
		),

		array(
			'id'            => 'yith-wrvp-use-mandrill',
			'title'         => __( 'Enable Mandrill', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'onoff',
			'default'       => 'no',
		),

		array(
			'id'            => 'yith-wrvp-mandrill-api-key',
			'title'         => __( 'Mandrill API KEY', 'yith-woocommerce-recently-viewed-products' ),
			'desc'          => __( 'Insert your Mandrill API KEY', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'text',
			'default'       => '',
            'deps'              => array(
                'id'    => 'yith-wrvp-use-mandrill',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'id'            => 'yith-wrvp-enable-analytics',
			'name'          => __( 'Add Google Analytics to email links', 'yith-woocommerce-recently-viewed-products' ),
            'type'          => 'yith-field',
            'yith-type'     => 'onoff',
			'desc'          => '',
			'default'       => 'no',
		),

		array(
			'id'                => 'yith-wrvp-campaign-source',
			'name'              => __( 'Campaign Source', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'text',
			'desc'              => __( 'Referrer: google, citysearch, newsletter4', 'yith-woocommerce-recently-viewed-products' ),
			'css'               => 'width: 400px;',
            'deps'              => array(
                'id'    => 'yith-wrvp-enable-analytics',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'id'                => 'yith-wrvp-campaign-medium',
			'name'              => __( 'Campaign Medium', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'text',
			'desc'              => __( 'Marketing medium: cpc, banner, email', 'yith-woocommerce-recently-viewed-products' ),
			'css'               => 'width: 400px;',
            'deps'              => array(
                'id'    => 'yith-wrvp-enable-analytics',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'id'                => 'yith-wrvp-campaign-term',
			'name'              => __( 'Campaign Term', 'yith-woocommerce-recently-viewed-products' ),
			'type'              => 'ywrvp_custom_checklist',
			'desc'              => __( 'Identify the paid keywords. Enter values separated by commas, for example: term1, term2', 'yith-woocommerce-recently-viewed-products' ),
			'css'               => 'width: 400px;',
			'placeholder'       => __( 'Insert a term&hellip;', 'yith-woocommerce-recently-viewed-products' ),
            'deps'              => array(
                'id'    => 'yith-wrvp-enable-analytics',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'id'                => 'yith-wrvp-campaign-content',
			'name'              => __( 'Campaign Content', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'text',
			'desc'              => __( 'Use to differentiate ads', 'yith-woocommerce-recently-viewed-products' ),
			'css'               => 'width: 400px;',
            'deps'              => array(
                'id'    => 'yith-wrvp-enable-analytics',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'id'                => 'yith-wrvp-campaign-name',
			'name'              => __( 'Campaign Name', 'yith-woocommerce-recently-viewed-products' ),
            'type'              => 'yith-field',
            'yith-type'         => 'text',
			'desc'              => __( 'Product, promo code, or slogan', 'yith-woocommerce-recently-viewed-products' ),
			'css'               => 'width: 400px;',
            'deps'              => array(
                'id'    => 'yith-wrvp-enable-analytics',
                'value' => 'yes',
                'type'  => 'hide'
            )
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wrvp-end-mail-options'
		),
	)
);

return apply_filters( 'yith_wrvp_panel_general_options', $general );