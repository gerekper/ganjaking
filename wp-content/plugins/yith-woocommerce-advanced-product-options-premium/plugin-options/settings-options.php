<?php
/**
 *  Settings Tab
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$settings = array(

	'settings'         => array(
		'general-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'settings-general' => array(
					'title' => esc_html_x( 'General options', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                    'description' => esc_html_x( 'Set the general options of the Product Add-ons features.', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                ),
				'settings-cart'    => array(
					'title' => esc_html_x( 'Cart & Order', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                    'description' => esc_html_x( 'Set the cart options of the Product Add-ons plugin on the Cart and Checkout pages.', 'Admin title of tab', 'yith-woocommerce-product-add-ons' ),
                ),

			),
		),
	),
);

return apply_filters( 'yith_wapo_panel_settings_options', $settings );
