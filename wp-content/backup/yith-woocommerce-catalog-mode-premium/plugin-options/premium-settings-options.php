<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$admin_override_settings = '';
$admin_override          = '';

if ( ywctm_is_multivendor_active() && '' === ywctm_get_vendor_id( true ) ) {
	$admin_override          = array(
		'name'      => esc_html__( 'Admininstrator overrides vendors\' settings', 'yith-woocommerce-catalog-mode' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => esc_html__( 'If enabled, the administrator settings will override the vendors\' settings', 'yith-woocommerce-catalog-mode' ),
		'id'        => 'ywctm_admin_override',
		'default'   => 'no',
	);
	$admin_override_settings = array(
		'type'      => 'yith-field',
		'yith-type' => 'yith-multiple-field',
		'name'      => esc_html__( 'Apply the admin settings to all or only to selected vendors.', 'yith-woocommerce-catalog-mode' ),
		'desc'      => esc_html__( 'Choose to apply the admin settings to all or only to selected vendors.', 'yith-woocommerce-catalog-mode' ),
		'id'        => 'ywctm_admin_override_settings',
		'fields'    => array(
			'action' => array(
				'inline-label' => '',
				'options'      => array(
					'enable'  => esc_html__( 'Apply admin settings', 'yith-woocommerce-catalog-mode' ),
					'disable' => esc_html__( 'Do not apply admin settings', 'yith-woocommerce-catalog-mode' ),
				),
				'std'          => 'enable',
				'type'         => 'select',
			),
			'target' => array(
				'inline-label' => esc_html__( 'to', 'yith-woocommerce-catalog-mode' ),
				'options'      => array(
					'all'       => esc_html__( 'All vendors', 'yith-woocommerce-catalog-mode' ),
					'selection' => esc_html__( 'Vendors in exclusion list', 'yith-woocommerce-catalog-mode' ),
				),
				'std'          => 'enable',
				'type'         => 'select',
			),
		),
		'deps'      => array(
			'id'    => 'ywctm_admin_override',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
		'class'     => 'ywctm-inline-selects',
	);
}

return array(
	'premium-settings' => array(
		'step_one_title'           => array(
			'name' => esc_html__( 'Step 1 - Set your users rules', 'yith-woocommerce-catalog-mode' ),
			'type' => 'title',
		),
		'affected_users'           => array(
			'name'      => esc_html__( 'Enable Catalog Mode for', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => esc_html__( 'Choose if to enable the Catalog Mode for all users or only to guest users.', 'yith-woocommerce-catalog-mode' ),
			'options'   => array(
				'all'          => esc_html__( 'All users', 'yith-woocommerce-catalog-mode' ),
				'unregistered' => esc_html__( 'Only guest users', 'yith-woocommerce-catalog-mode' ),
			),
			'default'   => 'all',
			'id'        => 'ywctm_apply_users' . ywctm_get_vendor_id(),
		),
		'enable_geolocation'       => array(
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Use this option if you want to enable or disable the Catalog Mode options to users of a specific country.', 'yith-woocommerce-catalog-mode' ),
			'name'      => esc_html__( 'Additional geolocation filter', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_enable_geolocation' . ywctm_get_vendor_id(),
			'default'   => 'no',
		),
		'gelocation_options'       => array(
			'type'      => 'yith-field',
			'yith-type' => 'yith-multiple-field',
			'desc'      => esc_html__( 'Choose to enable or disable the Catalog Mode settings to users from specific countries.', 'yith-woocommerce-catalog-mode' ),
			'name'      => esc_html__( 'Enable or disable Catalog Mode settings to users from specific countries', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_geolocation_settings' . ywctm_get_vendor_id(),
			'fields'    => array(
				'action'    => array(
					'inline-label' => '',
					'options'      => array(
						'enable'  => esc_html__( 'Enable Catalog Mode', 'yith-woocommerce-catalog-mode' ),
						'disable' => esc_html__( 'Disable Catalog Mode', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'enable',
					'type'         => 'select',
				),
				'users'     => array(
					'inline-label' => esc_html__( 'for', 'yith-woocommerce-catalog-mode' ),
					'options'      => array(
						'all'          => esc_html__( 'All users', 'yith-woocommerce-catalog-mode' ),
						'unregistered' => esc_html__( 'Only guest users', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'all',
					'type'         => 'select',
				),
				'countries' => array(
					'inline-label' => esc_html__( 'from', 'yith-woocommerce-catalog-mode' ),
					'options'      => WC()->countries->get_countries(),
					'std'          => array(),
					'multiple'     => 'true',
					'type'         => 'select-buttons',
				),
			),
			'class'     => 'ywctm-inline-selects',
			'deps'      => array(
				'id'    => 'ywctm_enable_geolocation' . ywctm_get_vendor_id(),
				'value' => 'yes',
				'type'  => 'hide-disable',
			),
		),
		'catalog_mode_admin_view'  => array(
			'name'      => esc_html__( 'Catalog mode for administrators', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Choose to enable the Catalog Mode for admins.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_admin_view' . ywctm_get_vendor_id(),
			'default'   => 'yes',
		),
		'disable_review'           => array(
			'name'      => esc_html__( 'Hide review tab for guest users', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'If enabled, guest users can\'t view the review tab in the product pages.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_disable_review' . ywctm_get_vendor_id(),
			'default'   => 'no',
		),
		'vendor_admin_override'    => $admin_override,
		'vendor_override_settings' => $admin_override_settings,
		'step_one_end'             => array(
			'type' => 'sectionend',
		),
		'step_two_title'           => array(
			'name' => esc_html__( 'Step 2: Set your Catalog Mode options', 'yith-woocommerce-catalog-mode' ),
			'type' => 'title',
		),
		'disable_shop'             => array(
			'name'      => esc_html__( 'Disable shop', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Use this option to hide the "Cart" page, "Checkout" page and all the "Add to Cart" buttons in the shop.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_disable_shop' . ywctm_get_vendor_id(),
			'default'   => 'no',
		),
		'hide_add_to_cart'         => array(
			'type'      => 'yith-field',
			'yith-type' => 'yith-multiple-field',
			'name'      => esc_html__( '"Add to Cart" settings in Catalog Mode', 'yith-woocommerce-catalog-mode' ),
			'desc'      => esc_html__( 'Choose to hide or to show "Add to Cart" and whether to apply these to all products or only to the exclusion list.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id(),
			'fields'    => array(
				'action' => array(
					'inline-label' => '',
					'options'      => array(
						'show' => esc_html__( 'Show "Add to Cart"', 'yith-woocommerce-catalog-mode' ),
						'hide' => esc_html__( 'Hide "Add to Cart"', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'show',
					'type'         => 'select',
				),
				'where'  => array(
					'inline-label' => esc_html__( 'in', 'yith-woocommerce-catalog-mode' ),
					'options'      => array(
						'all'     => esc_html__( 'All pages', 'yith-woocommerce-catalog-mode' ),
						'shop'    => esc_html__( 'Shop page', 'yith-woocommerce-catalog-mode' ),
						'product' => esc_html__( 'Product page', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'all',
					'type'         => 'select',
				),
				'items'  => array(
					'inline-label' => esc_html__( 'for', 'yith-woocommerce-catalog-mode' ),
					'options'      => array(
						'all'       => esc_html__( 'All products', 'yith-woocommerce-catalog-mode' ),
						'exclusion' => esc_html__( 'Items in exclusion list only', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'all',
					'type'         => 'select',
				),
			),
			'deps'      => array(
				'id'    => 'ywctm_disable_shop' . ywctm_get_vendor_id(),
				'value' => 'no',
				'type'  => 'hide-disable',
			),
			'class'     => 'ywctm-inline-selects',
		),
		'hide_variations'          => array(
			'name'      => esc_html__( 'Hide product variations', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Use this option to hide product variations where "add to cart" is hidden.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_hide_variations' . ywctm_get_vendor_id(),
			'default'   => 'no',
		),
		'custom_button'            => array(
			'name'      => esc_html__( 'In product pages, replace "add to cart" with:', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'desc'      => esc_html__( 'Optional: choose a custom button to replace ALL the hidden "add to cart" buttons or links.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_custom_button_settings' . ywctm_get_vendor_id(),
			'options'   => ywctm_get_buttons_labels(),
			'default'   => 'none',
		),
		'custom_button_loop'       => array(
			'name'      => esc_html__( 'In shop pages, replace add to cart with:', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'desc'      => esc_html__( 'Optional: choose a custom button to replace ALL the hidden "add to cart" buttons or links.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id(),
			'options'   => ywctm_get_buttons_labels(),
			'default'   => 'none',
		),
		'hide_price'               => array(
			'type'      => 'yith-field',
			'yith-type' => 'yith-multiple-field',
			'name'      => esc_html__( 'Price settings in Catalog Mode', 'yith-woocommerce-catalog-mode' ),
			'desc'      => esc_html__( 'Choose to hide or to show product prices and whether to apply these to all products or only to the exclusion list.', 'yith-woocommerce-catalog-mode' ) . '<br />' . esc_html__( 'Note: if you hide the price, "add to cart" will be hidden too.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_hide_price_settings' . ywctm_get_vendor_id(),
			'fields'    => array(
				'action' => array(
					'inline-label' => '',
					'options'      => array(
						'show' => esc_html__( 'Show price', 'yith-woocommerce-catalog-mode' ),
						'hide' => esc_html__( 'Hide price', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'show',
					'type'         => 'select',
				),
				'items'  => array(
					'inline-label' => esc_html__( 'for', 'yith-woocommerce-catalog-mode' ),
					'options'      => array(
						'all'       => esc_html__( 'All products', 'yith-woocommerce-catalog-mode' ),
						'exclusion' => esc_html__( 'Items in exclusion list only', 'yith-woocommerce-catalog-mode' ),
					),
					'std'          => 'all',
					'type'         => 'select',
				),
			),
			'class'     => 'ywctm-inline-selects',
		),
		'custom_price_text'        => array(
			'name'      => esc_html__( 'Where hidden, replace price with:', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'desc'      => esc_html__( 'Optional: choose a label to replace ALL the hidden prices.', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id(),
			'options'   => ywctm_get_buttons_labels(),
			'default'   => 'none',
		),
		'step_two_end'             => array(
			'type' => 'sectionend',
		),
	),
);
