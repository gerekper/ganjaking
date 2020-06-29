<?php
/**
 * Add to Wishlist settings
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters( 'yith_wcwl_add_to_wishlist_options', array(
	'add_to_wishlist' => array(

		'general_section_start' => array(
			'name' => __( 'General Settings', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_general_settings'
		),

		'after_add_to_wishlist_behaviour' => array(
			'name'      => __( 'After product is added to wishlist', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose the look of the Wishlist button when the product has already been added to a wishlist', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_after_add_to_wishlist_behaviour',
			'options'   => array_merge(
				array(
					'add'    => __( 'Show "Add to wishilist" button', 'yith-woocommerce-wishlist' ),
					'view'   => __( 'Show "View wishlist" link', 'yith-woocommerce-wishlist' ),
					'remove' => __( 'Show "Remove from list" link', 'yith-woocommerce-wishlist' ),
				)
			) ,
			'default'   => 'view',
			'type'      => 'yith-field',
			'yith-type' => 'radio'
		),

		'general_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_general_settings'
		),

		'shop_page_section_start' => array(
			'name' => __( 'Loop settings', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => __( 'Loop options will be visible on Shop page, category pages, product shortcodes, products sliders, and all the other places where the WooCommerce products\' loop is used', 'yith-woocommerce-wishlist' ),
			'id' => 'yith_wcwl_shop_page_settings'
		),

		'show_on_loop' => array(
			'name'      => __( 'Show "Add to wishlist" in loop', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Enable the "Add to wishlist" feature in WooCommerce products\' loop', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_show_on_loop',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff'
		),

		'loop_position' => array(
			'name'      => __( 'Position of "Add to wishlist" in loop', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose where to show "Add to wishlist" button or link in WooCommerce products\' loop. <span class="addon">Copy this shortcode <span class="code"><code>[yith_wcwl_add_to_wishlist]</code></span> and paste it where you want to show the "Add to wishlist" link or button</span>', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_loop_position',
			'default'   => 'after_add_to_cart',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'before_image' => __( 'On top of the image', 'yith-woocommerce-wishlist' ),
				'before_add_to_cart' => __( 'Before "Add to cart" button', 'yith-woocommerce-wishlist' ),
				'after_add_to_cart' => __( 'After "Add to cart" button', 'yith-woocommerce-wishlist' ),
				'shortcode' => __( 'Use shortcode', 'yith-woocommerce-wishlist' )
			),
			'deps'      => array(
				'id'    => 'yith_wcwl_show_on_loop',
				'value' => 'yes'
			)
		),

		'shop_page_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_shop_page_settings'
		),

		'product_page_section_start' => array(
			'name' => __( 'Product page settings', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_product_page_settings'
		),

		'add_to_wishlist_position' => array(
			'name'      => __( 'Position of "Add to wishlist" on product page', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose where to show "Add to wishlist" button or link on the product page. <span class="addon">Copy this shortcode <span class="code"><code>[yith_wcwl_add_to_wishlist]</code></span> and paste it where you want to show the "Add to wishlist" link or button</span>', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_button_position',
			'default'   => 'after_add_to_cart',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'add-to-cart' => __( 'After "Add to cart"', 'yith-woocommerce-wishlist' ),
				'thumbnails'  => __( 'After thumbnails', 'yith-woocommerce-wishlist' ),
				'summary'     => __( 'After summary', 'yith-woocommerce-wishlist' ),
				'shortcode'   => __( 'Use shortcode', 'yith-woocommerce-wishlist' )
			),
		),

		'product_page_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_product_page_settings'
		),

		'text_section_start' => array(
			'name' => __( 'Text customization', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_text_section_settings'
		),

		'add_to_wishlist_text' => array(
			'name'    => __( '"Add to wishlist" text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter a text for "Add to wishlist" button', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_add_to_wishlist_text',
			'default' => __( 'Add to wishlist', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'product_added_text' => array(
			'name'    => __( '"Product added" text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter the text of the message displayed when the user adds a product to the wishlist', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_product_added_text',
			'default' => __( 'Product added!', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'browse_wishlist_text' => array(
			'name'    => __( '"Browse wishlist" text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter a text for the "Browse wishlist" link on the product page', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_browse_wishlist_text',
			'default' => __( 'Browse wishlist', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'already_in_wishlist_text' => array(
			'name'    => __( '"Product already in wishlist" text', 'yith-woocommerce-wishlist' ),
			'desc'    => __( 'Enter the text for the message displayed when the user views a product that is already in the wishlist', 'yith-woocommerce-wishlist' ),
			'id'      => 'yith_wcwl_already_in_wishlist_text',
			'default' => __( 'The product is already in your wishlist!', 'yith-woocommerce-wishlist' ),
			'type'    => 'text',
		),

		'text_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_text_section_settings'
		),

		'style_section_start' => array(
			'name' => __( 'Style & Color customization', 'yith-woocommerce-wishlist' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcwl_style_section_settings'
		),

		'use_buttons' => array(
			'name'      => __( 'Style of "Add to wishlist"', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose if you want to show a textual "Add to wishlist" link or a button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_wishlist_style',
			'options'   => array(
				'link'           => __( 'Textual (anchor)', 'yith-woocommerce-wishlist' ),
				'button_default' => __( 'Button with theme style', 'yith-woocommerce-wishlist' ),
				'button_custom'  => __( 'Button with custom style', 'yith-woocommerce-wishlist' )
			),
			'default'   => 'link',
			'type'      => 'yith-field',
			'yith-type' => 'radio'
		),

		'add_to_wishlist_colors' => array(
			'name'         => __( '"Add to wishlist" button style', 'yith-woocommerce-wishlist' ),
			'id'           => 'yith_wcwl_color_add_to_wishlist',
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'desc' => __( 'Choose colors for the "Add to wishlist" button', 'yith-woocommerce-wishlist' ),
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
					'desc' => __( 'Choose colors for the "Add to wishlist" button on hover state', 'yith-woocommerce-wishlist' ),
					array(
						'name' => __( 'Background Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'background_hover',
						'default' => '#333333'
					),
					array(
						'name' => __( 'Text Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'text_hover',
						'default' => '#FFFFFF'
					),
					array(
						'name' => __( 'Border Hover', 'yith-woocommerce-wishlist' ),
						'id'   => 'border_hover',
						'default' => '#333333'
					),
				)
			),
			'deps' => array(
				'id'    => 'yith_wcwl_add_to_wishlist_style',
				'value' => 'button_custom'
			)
		),

		'rounded_buttons_radius' => array(
			'name'      => __( 'Border radius', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Choose radius for the "Add to wishlist" button', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_rounded_corners_radius',
			'default'   => 16,
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'min'       => 1,
			'max'       => 100,
			'deps' => array(
				'id'    => 'yith_wcwl_add_to_wishlist_style',
				'value' => 'button_custom'
			)
		),

		'add_to_wishlist_icon' => array(
			'name'      => __( '"Add to wishlist" icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the "Add to wishlist" button (optional)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_wishlist_icon',
			'default'   => apply_filters( 'yith_wcwl_add_to_wishlist_std_icon', 'fa-heart-o', 'yith_wcwl_add_to_wishlist_icon' ),
			'type'      => 'yith-field',
			'class'     => 'icon-select',
			'yith-type' => 'select',
			'options'   => yith_wcwl_get_plugin_icons()
		),

		'add_to_wishlist_custom_icon' => array(
			'name'      => __( '"Add to wishlist" custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for "Add to wishlist" button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_add_to_wishlist_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'deps'      => array(
				'id'    => 'yith_wcwl_add_to_wishlist_icon',
				'value' => 'custom'
			)
		),

		'added_to_wishlist_icon' => array(
			'name'      => __( '"Added to wishlist" icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Select an icon for the "Added to wishlist" button (optional)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_added_to_wishlist_icon',
			'default'   => apply_filters( 'yith_wcwl_add_to_wishlist_std_icon', 'fa-heart', 'yith_wcwl_added_to_wishlist_icon' ),
			'type'      => 'yith-field',
			'class'     => 'icon-select',
			'yith-type' => 'select',
			'options'   => yith_wcwl_get_plugin_icons( __( 'Same used for Add to wishlist', 'yith-woocommerce-wishlist' ) )
		),

		'added_to_wishlist_custom_icon' => array(
			'name'      => __( '"Added to wishlist" custom icon', 'yith-woocommerce-wishlist' ),
			'desc'      => __( 'Upload an icon you\'d like to use for "Add to wishlist" button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
			'id'        => 'yith_wcwl_added_to_wishlist_custom_icon',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'deps'      => array(
				'id'    => 'yith_wcwl_added_to_wishlist_icon',
				'value' => 'custom'
			)
		),

		'custom_css' => array(
			'name'     => __( 'Custom CSS', 'yith-woocommerce-wishlist' ),
			'desc'     => __( 'Enter custom CSS to be applied to Wishlist elements (optional)', 'yith-woocommerce-wishlist' ),
			'id'       => 'yith_wcwl_custom_css',
			'default'  => '',
			'type'     => 'yith-field',
			'yith-type' => 'textarea',
		),

		'style_section_end' => array(
			'type' => 'sectionend',
			'id' => 'yith_wcwl_style_section_settings'
		),

	),
) );
