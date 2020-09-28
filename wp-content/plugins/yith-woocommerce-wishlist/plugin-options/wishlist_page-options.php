<?php
/**
 * Wishlist Page settings
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters( 'yith_wcwl_wishlist_page_options', array(
	'wishlist_page' => array(
		'manage_section_start' => array(
			'name' => __( 'All your wishlists', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_manage_settings'
		),

		'wishlist_page' => array(
			'name'     => __( 'Wishlist page', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Pick a page as the main Wishlist page; make sure you add the <span class="code"><code>[yith_wcwl_wishlist]</code></span> shortcode into the page content', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_wishlist_page_id',
			'type'     => 'single_select_page',
			'default'  => '',
			'class'    => 'chosen_select_nostd',
		),

		'manage_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_manage_settings'
		),

		'wishlist_section_start' => array(
			'name' => __( 'Wishlist Detail Page', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_wishlist_settings'
		),

		'show_product_variation' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Product variations selected by the user (example: size or color)', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_variation_show',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'start'
		),

		'show_unit_price' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Product price', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_price_show',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'wishlist_info'
		),

		'show_stock_status' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Product stock (show if the product is available or not)', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_stock_show',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'wishlist_info'
		),

		'show_dateadded' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Date on which the product was added to the wishlist', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_show_dateadded',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'wishlist_info'
		),

		'show_add_to_cart' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Add to cart option for each product', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_add_to_cart_show',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'wishlist_info'
		),

		'show_remove_button' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Icon to remove the product from the wishlist - to the left of the product', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_show_remove',
			'type'     => 'checkbox',
			'default'  => 'yes',
			'checkboxgroup' => 'wishlist_info'
		),

		'repeat_remove_button' => array(
			'name'     => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Button to remove the product from the wishlist - to the right of the product', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_repeat_remove_button',
			'type'     => 'checkbox',
			'default'  => '',
			'checkboxgroup' => 'end'
		),

		'redirect_to_cart' => array(
			'name'      => __( 'Redirect to cart', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Redirect users to the cart page when they add a product to the cart from the wishlist page', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_redirect_cart',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'remove_after_add_to_cart' => array(
			'name'      => __( 'Remove if added to the cart', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Remove the product from the wishlist after it has been added to the cart', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_remove_after_add_to_cart',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'enable_wishlist_share' => array(
			'name'      => __( 'Share wishlist', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Enable this option to let users share their wishlist on social media', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_enable_share',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'share_on_facebook' => array(
			'name'    => __( 'Share on social media', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Share on Facebook', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_fb',
			'default' => 'yes',
			'type'    => 'checkbox',
			'checkboxgroup' => 'start'
		),

		'share_on_twitter' => array(
			'name'    => __( 'Share on social media', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Tweet on Twitter', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_twitter',
			'default' => 'yes',
			'type'    => 'checkbox',
			'checkboxgroup' => 'wishlist_share'
		),

		'share_on_pinterest' => array(
			'name'    => __( 'Share on social media', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Pin on Pinterest', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_pinterest',
			'default' => 'yes',
			'type'    => 'checkbox',
			'checkboxgroup' => 'wishlist_share'
		),

		'share_by_email' => array(
			'name'    => __( 'Share on social media', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Share by email', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_email',
			'default' => 'yes',
			'type'    => 'checkbox',
			'checkboxgroup' => 'wishlist_share'
		),

		'share_by_whatsapp' => array(
			'name'    => __( 'Share on social media', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Share on WhatsApp', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_whatsapp',
			'default' => 'yes',
			'type'    => 'checkbox',
			'checkboxgroup' => 'wishlist_share'
		),

		'share_by_url' => array(
			'name'    => __( 'Share by URL', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Show "Share URL" field on wishlist page', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_share_url',
			'default' => 'no',
			'type'    => 'checkbox',
			'checkboxgroup' => 'end'
		),

		'socials_title' => array(
			'name'    => __( 'Sharing title', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Wishlist title used for sharing (only used on Twitter and Pinterest)', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_socials_title',
			'default' => sprintf( __( 'My wishlist on %s', 'yith-woocommerce-wishlist' ), get_bloginfo( 'name' ) ),
			'type'    => 'text',
		),

		'socials_text' =>  array(
			'name'    => __( 'Social text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Type the message you want to publish when you share your wishlist on Twitter and Pinterest', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_socials_text',
			'default' => '',
			'type'    => 'yith-field',
			'yith-type' => 'textarea'
		),

		'socials_image' => array(
			'name'    => __( 'Social image URL', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'It will be used to pin the wishlist on Pinterest.', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_socials_image_url',
			'default' => '',
			'type'    => 'text',
		),

		'wishlist_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_wishlist_settings',
		),

		'text_section_start' => array(
			'name' => __( 'Text customization', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_text_section_settings'
		),

		'default_wishlist_title' => array(
			'name'    => __( 'Default wishlist name', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter a name for the default wishlist. This is the wishlist that will be automatically generated for all users if they do not create any custom one', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_wishlist_title',
			'default' => __( 'My wishlist', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'add_to_cart_text' => array(
			'name'    => __( '"Add to cart" text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter a text for the "Add to cart" button', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_add_to_cart_text',
			'default' => __( 'Add to cart', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'text_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_text_section_settings'
		),

		'style_section_start' => array(
			'name' => __( 'Style & color customization', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_style_section_settings'
		),

		'use_buttons' => array(
			'name'      => __( 'Style of "Add to cart"', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose whether to show a textual "Add to cart" link or a button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_cart_style',
			'options'   => array(
				'link'           => __( 'Textual (anchor)', 'yith-woocommerce-wishlist' ),
				'button_default' => __( 'Button with theme style', 'yith-woocommerce-wishlist' ),
				'button_custom'  => __( 'Button with custom style', 'yith-woocommerce-wishlist' )
			),
			'default'   => 'link',
			'type'      => 'yith-field',
			'yith-type' => 'radio'
		),

		'add_to_cart_colors' => array(
			'name'         => __( '"Add to cart" button style', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_add_to_cart',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'desc' => __( 'Choose the colors for the "Add to cart" button', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
						'id'   => 'background',
						'default' => '#333333'
					),
					array(
						'name' => __( 'Text', 'yith-woocommerce-wishlist' ),
						'id'   => 'text',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border', 'yith-woocommerce-wishlist' ),
						'id'   => 'border',
						'default' => '#333333'
					),
				),
				array(
					'desc' => __( 'Choose colors for the "Add to cart" button on hover state', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'background_hover',
						'default' => '#4F4F4F'
					),
					array(
						'name' => __( 'Text Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'text_hover',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'border_hover',
						'default' => '#4F4F4F'
					),
				)
			),
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'rounded_buttons_radius' => array(
			'name'      => __( 'Border radius', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Set the radius for the "Add to cart" button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_cart_rounded_corners_radius',
			'default'   => 16,
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'min'       => 1,
			'max'       => 100,
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'add_to_cart_icon' => array(
			'name'      => __( '"Add to cart" icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the "Add to cart" button (optional)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_cart_icon',
			'default'   => apply_filters( 'yith_wcwl_add_to_cart_std_icon', 'fa-shopping-cart' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons(),
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)

		),

		'add_to_cart_custom_icon' => array(
			'name'      => __( '"Add to cart" custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for the "Add to cart" button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_cart_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'style_1_button_colors' => array(
			'name'         => __( 'Primary button style', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_button_style_1',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'desc' => __( 'Choose colors for the primary button<br/><small>This style will be applied to "Edit title" button on Wishlist view, "Submit Changes" button on Manage view and "Search wishlist" button on Search view</small>', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
						'id'   => 'background',
						'default' => '#333333'
					),
					array(
						'name' => __( 'Text', 'yith-woocommerce-wishlist' ),
						'id'   => 'text',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border', 'yith-woocommerce-wishlist' ),
						'id'   => 'border',
						'default' => '#333333'
					),
				),
				array(
					'desc' => __( 'Choose colors for the primary button on hover state<br/><small>This style will be applied to "Edit title" button on Wishlist view, "Submit Changes" button on Manage view and "Search wishlist" button on Search view</small>', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'background_hover',
						'default' => '#4F4F4F'
					),
					array(
						'name' => __( 'Text Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'text_hover',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'border_hover',
						'default' => '#4F4F4F'
					),
				)
			),
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'style_2_button_colors' => array(
			'name'         => __( 'Secondary button style', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_button_style_2',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'desc' => __( 'Choose colors of the secondary button<br/><small>This style will be applied to the buttons that allow showing and hiding the Edit title form on Wishlist view and "Create new Wishlist" button on Manage view</small>', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
						'id'   => 'background',
						'default' => '#333333'
					),
					array(
						'name' => __( 'Text', 'yith-woocommerce-wishlist' ),
						'id'   => 'text',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border', 'yith-woocommerce-wishlist' ),
						'id'   => 'border',
						'default' => '#333333'
					),
				),
				array(
					'desc' => __( 'Choose colors of the secondary button<br/><small>This style will be applied to the buttons that allow showing and hiding the Edit title form on Wishlist view and "Create new Wishlist" button on Manage view</small>', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'background_hover',
						'default' => '#4F4F4F'
					),
					array(
						'name' => __( 'Text Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'text_hover',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'border_hover',
						'default' => '#4F4F4F'
					),
				)
			),
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'wishlist_table_style' => array(
			'name'         => __( 'Wishlist table style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose the colors for the wishlist table (when set to "Traditional" layout)', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_wishlist_table',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#FFFFFF'
				),
				array(
					'name' => __( 'Text', 'yith-woocommerce-wishlist' ),
					'id'   => 'text',
					'default' => '#6d6c6c'
				),
				array(
					'name' => __( 'Border', 'yith-woocommerce-wishlist' ),
					'id'   => 'border',
					'default' => '#FFFFFF'
				),
			),
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'headings_style' => array(
			'name'         => __( 'Highlight color', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose the color for all sections with background<br/><small>This color will be used as background for the wishlist table heading and footer (when set to "Traditional" layout), and for various form across wishlist views</small>', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_headers_background',
			'type'         => 'yith-field',
			'yith-type'    => 'colorpicker',
			'default'      => '#F4F4F4',
			'deps' => array(
				'id' => 'yith_wcwl_add_to_cart_style',
				'value' => 'button_custom'
			)
		),

		'share_colors' => array(
			'name'         => __( 'Share button text color', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for share buttons text', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_share_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Text', 'yith-woocommerce-wishlist' ),
					'id'   => 'color',
					'default' => '#FFFFFF'
				),
				array(
					'name' => __( 'Text hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'color_hover',
					'default' => '#FFFFFF'
				),
			),
			'deps' => array(
				'id' => 'yith_wcwl_enable_share',
				'value' => 'yes'
			)

		),

		'fb_button_icon' => array(
			'name'      => __( 'Facebook share button icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the Facebook share button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_fb_button_icon',
			'default'   => 'fa-facebook',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'fb_button_custom_icon' => array(
			'name'      => __( 'Facebook share button custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for Facebook share button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_fb_button_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'fb_button_colors' => array(
			'name'         => __( 'Facebook share button style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for Facebook share button', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_fb_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#39599E'
				),
				array(
					'name' => __( 'Background hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'background_hover',
					'default' => '#595A5A'
				),
			),
		),

		'tw_button_icon' => array(
			'name'      => __( 'Twitter share button icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the Twitter share button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_tw_button_icon',
			'default'   => 'fa-twitter',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'tw_button_custom_icon' => array(
			'name'      => __( 'Twitter share button custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for Twitter share button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_tw_button_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'tw_button_colors' => array(
			'name'         => __( 'Twitter share button style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for Twitter share button', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_tw_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#45AFE2'
				),
				array(
					'name' => __( 'Background hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'background_hover',
					'default' => '#595A5A'
				),
			),
		),

		'pr_button_icon' => array(
			'name'      => __( 'Pinterest share button icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the Pinterest share button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_pr_button_icon',
			'default'   => 'fa-pinterest',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'pr_button_custom_icon' => array(
			'name'      => __( 'Pinterest share button custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for Pinterest share button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_pr_button_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'pr_button_colors' => array(
			'name'         => __( 'Pinterest share button style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for Pinterest share button', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_pr_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#AB2E31'
				),
				array(
					'name' => __( 'Background hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'background_hover',
					'default' => '#595A5A'
				),
			),
		),

		'em_button_icon' => array(
			'name'      => __( 'Email share button icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the Email share button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_em_button_icon',
			'default'   => 'fa-envelope-o',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'em_button_custom_icon' => array(
			'name'      => __( 'Email share button custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for the Email share button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_em_button_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'em_button_colors' => array(
			'name'         => __( 'Email share button style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for the Email share button', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_em_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#FBB102'
				),
				array(
					'name' => __( 'Background hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'background_hover',
					'default' => '#595A5A'
				),
			),
		),

		'wa_button_icon' => array(
			'name'      => __( 'WhatsApp share button icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the WhatsApp share button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_wa_button_icon',
			'default'   => 'fa-whatsapp',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'icon-select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'wa_button_custom_icon' => array(
			'name'      => __( 'WhatsApp share button custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for WhatsApp share button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_wa_button_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload'
		),

		'wa_button_colors' => array(
			'name'         => __( 'WhatsApp share button style', 'yith-woocommerce-wishlist' ),
			'desc'         => __( 'Choose colors for WhatsApp share button', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_wa_button',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'name' => __( 'Background', 'yith-woocommerce-wishlist' ),
					'id'   => 'background',
					'default' => '#00A901'
				),
				array(
					'name' => __( 'Background hover', 'yith-woocommerce-wishlist' ),
					'id'   => 'background_hover',
					'default' => '#595A5A'
				),
			),
		),

		'style_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_style_section_settings'
		),
	)
) );