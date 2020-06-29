<?php

/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general'  => array(

		array(
	        'title'		=> __( 'General', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcps_settings_type'
	    ),
	    array(
			'title'     => __( 'Show add-ons', 'yith-woocommerce-product-add-ons' ),
			'id'        => 'yith_wcps_settings_formposition',
			'type'      => 'select',
			'options'   => array(
				'before'       => __( 'Before "Add to cart" button', 'yith-woocommerce-product-add-ons' ),
				'after'    => __( 'After "Add to cart" button', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'before'
		),
	    array(
	        'title'		=> __( '"Add to cart" button label', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Change button label.', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wcps_settings_addtocartlabel',
	        'default' 	=> __( 'Select Options' , 'yith-woocommerce-product-add-ons' ),
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=> true,

	    ),
		array(
			'title'		=> __( 'Show product price in Cart page', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wcps_settings_show_product_price_cart',
			'default' 	=> 'no',
			'desc'		=> __( 'Checking this option allows you to show the product base price in cart',
                'yith-woocommerce-product-add-ons' ),
			//@since 1.1.0
		),
        array(
            'title'		=> __( 'Always show the price table', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
            'type'		=> 'checkbox',
            'id'  		=> 'yith_wcps_settings_show_add_ons_price_table',
            'default' 	=> 'no',
            'desc'		=> __( 'Checking this option allows you to always show the price table even if the total add-on amount is 0 in
            on the product page',
                'yith-woocommerce-product-add-ons' ),
            //@since 1.1.0
        ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcps_settings_end'
	    ),

		array(
	        'title'		=> __( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcps_settings_type'
	    ),
	    array(
	        'title'		=> __( 'Show add-on titles', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wcps_settings_showlabeltype',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'title'		=> __( 'Show add-on images', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wcps_settings_showimagetype',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'title'		=> __( 'Show add-on descriptions', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wcps_settings_showdescrtype',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcps_settings_end'
	    ),

		array(
	        'title'		=> __( 'Options', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcps_settings_options'
	    ),
	    array(
	        'title'		=> __( 'Show option images', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wcps_settings_showimageopt',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'title'		=> __( 'Show option descriptions', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wcps_settings_showdescropt',
	        'default' 	=> 'yes',
	    ),
	    array(
            'name'    	=> __( 'Option image', 'yith-woocommerce-cart-messages' ),
            'type'    	=> 'yith_wcps_upload',
            'id'      	=> 'yith_wcps_settings_tooltip_icon',
            'default' 	=>	YITH_WAPO_ASSETS_URL . '/img/description-icon.png',
        ),

		array(
			'title'		=> __( 'Date Format', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'text',
			'desc'		=> __( 'Set the format of the date control eg: mm/dd/yy', 'yith-woocommerce-product-add-ons' ),
			'id'  		=> 'yith_wcps_settings_date_format',
			'default' 	=> __( 'mm/dd/yy' , 'yith-woocommerce-product-add-ons' ),
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,

		),

	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcps_settings_end'
	    ),


		array(
	        'title'		=> __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcps_settings_upload'
	    ),
	    array(
			'id'        => 'yith-wcps-enable-tooltip',
			'title'     => __( 'Enable tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Enable tooltip on options', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),
		array(
			'id'        => 'yith-wcps-tooltip-position',
			'title'     => __( 'Tooltip position', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip position', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'top'       => __( 'Top', 'yith-woocommerce-product-add-ons' ),
				'bottom'    => __( 'Bottom', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'top'
		),
		array(
			'id'        => 'yith-wcps-tooltip-animation',
			'title'     => __( 'Tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'fade'     => __( 'Fade in', 'yith-woocommerce-product-add-ons' ),
				'slide'    => __( 'Slide in', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'fade'
		),
		array(
			'id'        => 'yith-wcps-tooltip-background',
			'title'     => __( 'Tooltip background', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#222222'
		),
		array(
			'id'        => 'yith-wcps-tooltip-text-color',
			'title'     => __( 'Tooltip text color', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#ffffff'
		),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcps_settings_end'
	    ),
	    
		array(
	        'title'		=> __( 'Uploading options', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcps_settings_upload'
	    ),
	    array(
	        'title'		=> __( 'Uploading folder name', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Any change you make now will only affect future uploads.', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wcps_settings_uploadfolder',
	        'default' 	=> 'yith_advanced_product_options',
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=> true,
	    ),
	    array(
	        'title'		=> __( 'Uploading file types', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Separate file extensions using commas. Ex: .gif, .jpg, .png', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wcps_settings_filetypes',
	        'default' 	=> '.gif, .jpg, .png, .rar, .txt, .zip',
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=>  true,
	    ),
		array(
			'title'		=> __( 'Uploading file size (MB)', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'number',
			'desc'		=> __( 'Maximum size allowed for uploaded file', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'id'  		=> 'yith_wcps_settings_upload_size',
			'default' 	=> 10,
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,
		),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcps_settings_end'
	    ),

	)

);

return apply_filters( 'yith_wcps_panel_general_options', $general );
