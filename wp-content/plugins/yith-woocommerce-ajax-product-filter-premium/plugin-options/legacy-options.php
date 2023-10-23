<?php
/**
 * Legacy options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

return apply_filters(
	'yith_wcan_panel_legacy_options',
	array(

		'legacy' => array(
			'legacy_frontend_start' => array(
				'name' => _x( 'Frontend options', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcan_legacy_frontend_settings',
			),

			'product_container'     => array(
				'name'      => _x( 'Product container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'desc'      => _x( 'Enter here the CSS selector (class or ID) of the product container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_container]',
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'default'   => '.products',
			),

			'pagination_container'  => array(
				'name'      => _x( 'Shop pagination container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'desc'      => _x( 'Enter here the CSS selector (class or ID) of the shop pagination container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_pagination]',
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'default'   => 'nav.woocommerce-pagination',
			),

			'count_container'       => array(
				'name'      => _x( 'Result count container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'desc'      => _x( 'Enter here the CSS selector (class or ID) of the results count container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_result_container]',
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'default'   => 'nav.woocommerce-pagination',
			),

			'scroll_top_selector'   => array(
				'name'      => _x( '"Scroll to top" anchor selector', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'desc'      => _x( 'Enter here the CSS selector (class or ID) of the "Scroll to to top" anchor', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'        => 'yit_wcan_options[yith_wcan_ajax_scroll_top_class]',
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'default'   => 'nav.woocommerce-pagination',
			),

			'order_by'              => array(
				'name'      => _x( 'Terms sorting', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'desc'      => _x( 'Choose how to sort terms inside filters', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_terms_order]',
				'type'      => 'yith-field',
				'default'   => 'menu_order',
				'yith-type' => 'radio',
				'options'   => array(
					'product'      => _x( 'Product count', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'alphabetical' => _x( 'Alphabetical', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'menu_order'   => _x( 'Default', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				),
			),

			'legacy_frontend_end'   => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcan_legacy_frontend_settings',
			),

		),
	)
);
