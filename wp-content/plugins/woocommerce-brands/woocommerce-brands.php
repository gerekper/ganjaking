<?php
/**
 * Plugin Name: WooCommerce Brands
 * Plugin URI: https://woo.com/products/brands/
 * Description: Add brands to your products, as well as widgets and shortcodes for displaying your brands.
 * Author: WooCommerce
 * Author URI: https://woo.com/
 * Developer: WooCommerce
 * Developer URI: http://woo.com/
 * Requires at least: 5.4
 * Tested up to: 6.4
 * Version: 1.6.62
 * Text Domain: woocommerce-brands
 * Domain Path: /languages/
 * WC tested up to: 8.3
 * WC requires at least: 4.6
 *
 * Copyright (c) 2020 WooCommerce
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * Woo: 18737:8a88c7cbd2f1e73636c331c7a86f818c
 *
 * @package woocommerce-brands
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Automattic\WooCommerce\Admin\BlockTemplates\BlockTemplateInterface;
use Automattic\WooCommerce\Admin\Features\ProductBlockEditor\ProductTemplates\ProductFormTemplateInterface;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_brands_init', 1 );

// Automatic translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-brands', '__return_true' );

// HPOS and new product editor compatibility declaration.
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ) );
			FeaturesUtil::declare_compatibility( 'product_block_editor', plugin_basename( __FILE__ ) );
		}
	}
);

/**
 * Initialize plugin.
 */
function wc_brands_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_brands_woocommerce_deactivated' );
		return;
	}

	define( 'WC_BRANDS_VERSION', '1.6.62' ); // WRCS: DEFINED_VERSION.

	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'woocommerce-brands', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * WC_Brands classes
	 */
	require_once 'includes/class-wc-brands.php';

	if ( is_admin() ) {
		require_once 'includes/class-wc-brands-admin.php';
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_brands_plugin_action_links' );
	}

	require_once 'includes/wc-brands-functions.php';
}

/**
 * Add custom action links on the plugin screen.
 *
 * @param  mixed $actions Plugin Actions Links.
 * @return array
 */
function wc_brands_plugin_action_links( $actions ) {
	$custom_actions = array();

	// Documentation URL.
	$custom_actions['docs'] = sprintf( '<a href="%s">%s</a>', 'https://woo.com/document/woocommerce-brands/', __( 'Docs', 'woocommerce-brands' ) );

	// Support URL.
	$custom_actions['support'] = sprintf( '<a href="%s">%s</a>', 'https://woo.com/contact-us/', __( 'Support', 'woocommerce-brands' ) );

	// Changelog link.
	$custom_actions['changelog'] = sprintf( '<a href="%s" target="_blank">%s</a>', 'https://woo.com/changelogs/woocommerce-brands/changelog.txt', __( 'Changelog', 'woocommerce-brands' ) );

	// Add the links to the front of the actions list.
	return array_merge( $custom_actions, $actions );
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_brands_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Brands requires %s to be installed and active.', 'woocommerce-brands' ), '<a href="https://woo.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

/**
 * Activation hooks.
 */
register_activation_hook( __FILE__, 'wc_brands_activate', 10 );
register_activation_hook( __FILE__, 'flush_rewrite_rules', 20 );

/**
 * Register taxonomy upon activation so we can flush rewrite rules and prevent a 404.
 */
function wc_brands_activate() {
	if ( class_exists( 'WooCommerce' ) ) {
		require_once 'includes/class-wc-brands.php';
		WC_Brands::init_taxonomy();
	}
}

if ( ! function_exists( 'wc_brands_on_block_template_register' ) ) {
	/**
	 * Add a new block to the template.
	 */
	function wc_brands_on_block_template_register( BlockTemplateInterface $template ) {
		if ( $template instanceof ProductFormTemplateInterface && 'simple-product' === $template->get_id() ) {
			$section = $template->get_section_by_id( 'product-catalog-section' );
			if ( $section !== null ) {
				$section->add_block(
					[
						'id'         => 'woocommerce-brands-select',
						'blockName'  => 'woocommerce/product-taxonomy-field',
						'order'      => 15,
						'attributes' => [
							'label'       => __( 'Brands', 'woocommerce-brands' ),
							'createTitle' => __( 'Create new brand', 'woocommerce-brands' ),
							'slug'        => 'product_brand',
							'property'    => 'brands',
						],
					]
				);
			}
		}
	}
	add_action( 'woocommerce_block_template_register', 'wc_brands_on_block_template_register' );
}
