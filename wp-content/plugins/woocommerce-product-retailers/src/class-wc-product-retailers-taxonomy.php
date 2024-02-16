<?php
/**
 * WooCommerce Product Retailers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @package     WC-Product-Retailers/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * Product Retailers Taxonomy
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_Taxonomy {


	/**
	 * Initializes and registers the Product Retailers taxonomies.
	 *
	 * @since 1.0.0
	 */
	public static function initialize() {

		self::init_user_roles();
		self::init_taxonomy();
	}


	/**
	 * Initializes WooCommerce Product Retailers user roles.
	 *
	 * @since 1.0.0
	 */
	private static function init_user_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_product_retailers' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_product_retailers' );
		}
	}


	/**
	 * Initializes WooCommerce taxonomies.
	 *
	 * @since 1.0.0
	 */
	private static function init_taxonomy() {

		$show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : $show_in_menu = true;

		register_post_type( 'wc_product_retailer',
			array(
				'labels'                  => array(
					'name'                => __( 'Retailers', 'woocommerce-product-retailers' ),
					'singular_name'       => __( 'Retailer', 'woocommerce-product-retailers' ),
					'menu_name'           => _x( 'Retailers', 'Admin menu name', 'woocommerce-product-retailers' ),
					'add_new'             => __( 'Add Retailer', 'woocommerce-product-retailers' ),
					'add_new_item'        => __( 'Add New Retailer', 'woocommerce-product-retailers' ),
					'edit'                => __( 'Edit', 'woocommerce-product-retailers' ),
					'edit_item'           => __( 'Edit Retailer', 'woocommerce-product-retailers' ),
					'new_item'            => __( 'New Retailer', 'woocommerce-product-retailers' ),
					'view'                => __( 'View Retailers', 'woocommerce-product-retailers' ),
					'view_item'           => __( 'View Retailer', 'woocommerce-product-retailers' ),
					'search_items'        => __( 'Search Retailers', 'woocommerce-product-retailers' ),
					'not_found'           => __( 'No Retailers found', 'woocommerce-product-retailers' ),
					'not_found_in_trash'  => __( 'No Retailers found in trash', 'woocommerce-product-retailers' ),
				),
				'description'             => __( 'This is where you can add new product retailers that you can add to products.', 'woocommerce-product-retailers' ),
				'public'                  => true,
				'show_ui'                 => true,
				'capability_type'         => 'post',
				'capabilities'            => array(
					'publish_posts'       => 'manage_woocommerce_product_retailers',
					'edit_posts'          => 'manage_woocommerce_product_retailers',
					'edit_others_posts'   => 'manage_woocommerce_product_retailers',
					'delete_posts'        => 'manage_woocommerce_product_retailers',
					'delete_others_posts' => 'manage_woocommerce_product_retailers',
					'read_private_posts'  => 'manage_woocommerce_product_retailers',
					'edit_post'           => 'manage_woocommerce_product_retailers',
					'delete_post'         => 'manage_woocommerce_product_retailers',
					'read_post'           => 'manage_woocommerce_product_retailers',
				),
				'publicly_queryable'      => true,
				'exclude_from_search'     => true,
				'show_in_menu'            => $show_in_menu,
				'hierarchical'            => false,
				'rewrite'                 => false,
				'query_var'               => false,
				'supports'                => array( 'title' ),
				'show_in_nav_menus'       => false,
			)
		);
	}


}
