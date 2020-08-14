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
			'id'		=> 'yith_wapo_settings_type'
		),
		array(
			'title'     => __( 'Show add-ons', 'yith-woocommerce-product-add-ons' ),
			'id'        => 'yith_wapo_settings_formposition',
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
			'id'  		=> 'yith_wapo_settings_addtocartlabel',
			'default' 	=> __( 'Select Options' , 'yith-woocommerce-product-add-ons' ),
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,

		),
		array(
			'title'		=> __( 'Show product price in "cart page"', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_show_product_price_cart',
			'default' 	=> 'no',
			'desc'		=> __( 'Checking this option allows you to show the product base price in cart',
				'yith-woocommerce-product-add-ons' ),
			//@since 1.1.0
		),
		array(
			'title'		=> __( 'Always show the price table', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_show_add_ons_price_table',
			'default' 	=> 'no',
			'desc'		=> __( 'Checking this option allows you to always show the price table even if the amount of the add-ons is 0 in the single product page', 'yith-woocommerce-product-add-ons' ),
			//@since 1.1.0
		),
		array(
			'title'		=> __( 'Enable loop "add to cart"', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_enable_loop_add_to_cart',
			'default' 	=> 'no',
			'desc'		=> __( 'Enable again the "add to cart" features in shop and categories pages', 'yith-woocommerce-product-add-ons' ),
			//@since 1.1.0
		),
		array(
			'title'		=> __( 'Disable "labels" features', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_disable_wccl',
			'default' 	=> 'no',
			'desc'		=> __( 'Disable them if you have conflicts with theme features', 'yith-woocommerce-product-add-ons' ),
			//@since 1.1.0
		),

		array(
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),

		array(
			'title'		=> __( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'title',
			'desc'		=> '',
			'id'		=> 'yith_wapo_settings_type'
		),
		array(
			'title'		=> __( 'Show add-on titles', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_showlabeltype',
			'default' 	=> 'yes',
		),
		array(
			'title'		=> __( 'Show add-on images', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_showimagetype',
			'default' 	=> 'yes',
		),
		array(
			'title'		=> __( 'Show add-on descriptions', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_showdescrtype',
			'default' 	=> 'yes',
		),
		array(
			'title'		=> __( 'Enable "collapse" feature', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_enable_collapse_feature',
			'default' 	=> 'yes',
		),
		array(
			'title'		=> __( 'Show add-ons collapsed', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_show_addons_collapsed',
			'default' 	=> 'no',
		),
		array(
			'title'		=> __( 'Enable Textarea Editor', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_enable_textarea_editor',
			'default' 	=> 'no',
		),
		array(
			'title'		=> __( '"Replace Image" method', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'select',
			'id'  		=> 'yith_wapo_settings_alternative_replace_image',
			'options'   => array(
				'standard'		=> __( 'Standard method', 'yith-woocommerce-product-add-ons' ),
				'alternative'	=> __( 'Alternative method', 'yith-woocommerce-product-add-ons' ),
				'divi'			=> __( 'Divi method', 'yith-woocommerce-product-add-ons' ),
				'paul'			=> __( 'Paul\'s method', 'yith-woocommerce-product-add-ons' ),
			),
			'desc'		=> __( 'Select this option if the image replacement feature doesn\'t work properly with your theme', 'yith-woocommerce-product-add-ons' ),
			'default' 	=> 'standard',
		),

		array(
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),

		array(
			'title'		=> __( 'Options', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'title',
			'desc'		=> '',
			'id'		=> 'yith_wapo_settings_options'
		),
		array(
			'title'		=> __( 'Show option images', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_showimageopt',
			'default' 	=> 'yes',
		),

		array(
			'title'		=> __( 'Date Format', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'text',
			'desc'		=> __( 'Set the format of the date control eg', 'yith-woocommerce-product-add-ons' ) . ': mm/dd/yy',
			'id'  		=> 'yith_wapo_settings_date_format',
			'default' 	=> 'mm/dd/yy',
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,

		),

		array(
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),


		array(
			'title'		=> __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'title',
			'desc'		=> '',
			'id'		=> 'yith_wapo_settings_upload'
		),
		/*
		array(
			'id'        => 'yith-wapo-enable-tooltip',
			'title'     => __( 'Enable tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Enable tooltip on options', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),
		*/
		array(
			'title'		=> __( 'Show Tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_settings_showdescropt',
			'default' 	=> 'yes',
		),
		array(
			'name'    	=> __( 'Tooltip icon', 'yith-woocommerce-product-add-ons' ),
			'type'    	=> 'yith_wapo_upload',
			'id'      	=> 'yith_wapo_settings_tooltip_icon',
			'default' 	=>	YITH_WAPO_ASSETS_URL . '/img/description-icon.png',
		),
		array(
			'id'        => 'yith-wapo-tooltip-background',
			'title'     => __( 'Tooltip background', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#222222'
		),
		array(
			'id'        => 'yith-wapo-tooltip-text-color',
			'title'     => __( 'Tooltip text color', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#ffffff'
		),
		array(
			'id'        => 'yith-wapo-tooltip-position',
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
			'id'        => 'yith-wapo-tooltip-animation',
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
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),
		
		array(
			'title'		=> __( 'Uploading options', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'title',
			'desc'		=> '',
			'id'		=> 'yith_wapo_settings_upload'
		),
		array(
			'title'		=> __( 'Uploading folder name', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'text',
			'desc'		=> __( 'Changes will only affect future uploads.', 'yith-woocommerce-product-add-ons' ),
			'id'  		=> 'yith_wapo_settings_uploadfolder',
			'default' 	=> 'yith_advanced_product_options',
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,
		),
		array(
			'title'		=> __( 'Uploading file types', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'text',
			'desc'		=> __( 'Separate file extensions using commas. Ex: .gif, .jpg, .png', 'yith-woocommerce-product-add-ons' ),
			'id'  		=> 'yith_wapo_settings_filetypes',
			'default' 	=> '.gif, .jpg, .png, .rar, .txt, .zip',
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=>  true,
		),
		array(
			'title'		=> __( 'Uploading file size (MB)', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'type'		=> 'number',
			'desc'		=> __( 'Maximum allowed size for uploaded file', 'yith-woocommerce-product-add-ons' ), //@since 1.1.0
			'id'  		=> 'yith_wapo_settings_upload_size',
			'default' 	=> 10,
			'css'     	=> 'min-width: 350px;',
			'desc_tip'	=> true,
		),

		array(
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),

		array(
			'title'		=> __( 'Third-party compatibility', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'title',
			'desc'		=> '',
			'id'		=> 'yith_wapo_settings_upload'
		),

		array(
			'title'		=> __( 'Woo Layout Injector plugin', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_compatibility_woo_layout_injector',
			'desc'		=> __( 'Select this option if the add-ons don\'t appear in cart using a custom layout', 'yith-woocommerce-product-add-ons' ),
			'default' 	=> 'no',
		),
		array(
			'title'		=> __( '7up themes', 'yith-woocommerce-product-add-ons' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wapo_compatibility_7up_themes',
			'desc'		=> __( 'Select this option if the add-ons don\'t appear 7up themes product page', 'yith-woocommerce-product-add-ons' ),
			'default' 	=> 'no',
		),
		
		array(
			'type' 		=> 'sectionend',
			'id' 		=> 'yith_wapo_settings_end'
		),

	)

);

return apply_filters( 'yith_wapo_panel_general_options', $general );