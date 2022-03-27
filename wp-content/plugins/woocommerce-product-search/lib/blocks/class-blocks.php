<?php
/**
 * class-blocks.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 4.0.0
 */

namespace com\itthinx\woocommerce\search;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Blocks {

	public static function init() {
		require_once WOO_PS_BLOCKS_LIB . '/class-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-search-field-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-search-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-attribute-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-category-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-tag-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-price-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-rating-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-sale-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-stock-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-reset-block.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-product-filter-products-block.php';
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_filter( 'block_categories_all', array( __CLASS__, 'block_categories_all' ), 10, 2 );

		add_filter( 'block_categories', array( __CLASS__, 'block_categories' ), 10, 2 );

		add_filter( 'widget_types_to_hide_from_legacy_widget_block', array( __CLASS__, 'widget_types_to_hide_from_legacy_widget_block' ) );
	}

	public static function block_categories( $block_categories, $post ) {
		return self::block_categories_all( $block_categories, null );
	}

	public static function block_categories_all( $block_categories, $block_editor_context ) {

		remove_filter( 'block_categories', array( __CLASS__, 'block_categories' ) );
		$block_categories = array_merge(
			$block_categories,
			array(
				array(
					'slug' => 'woocommerce-product-search',
					'title' => 'WooCommerce Product Search'
				)
			)
		);
		return $block_categories;
	}

	public static function widget_types_to_hide_from_legacy_widget_block( $widgets ) {

		if ( !WPS_LEGACY_WIDGETS ) {
			$widgets[] = 'woocommerce_product_search_filter_attribute_widget';
			$widgets[] = 'woocommerce_product_search_filter_category_widget';
			$widgets[] = 'woocommerce_product_search_filter_price_widget';
			$widgets[] = 'woocommerce_product_search_filter_rating_widget';
			$widgets[] = 'woocommerce_product_search_filter_reset_widget';
			$widgets[] = 'woocommerce_product_search_filter_sale_widget';
			$widgets[] = 'woocommerce_product_search_filter_stock_widget';
			$widgets[] = 'woocommerce_product_search_filter_tag_widget';
			$widgets[] = 'woocommerce_product_search_filter_widget';
			$widgets[] = 'woocommerce_product_search_widget';
		}
		return $widgets;
	}

	public static function wp_init() {
		$asset_file = include WOO_PS_BLOCKS_LIB . '/build/index.asset.php';

		$editor_dependencies = array_merge(
			$asset_file['dependencies'],
			array( 'product-filter', 'wps-price-slider', 'selectize-ix' )
		);

		wp_register_script(
			'woocommerce-product-search-blocks',
			WOO_PS_PLUGIN_URL . '/lib/blocks/build/index.js',
			$editor_dependencies,
			$asset_file['version']
		);

		wp_set_script_translations(
			'woocommerce-product-search-blocks',
			'woocommerce-product-search',
			WOO_PS_CORE_DIR . '/languages'
		);

		$constants = array();
		$r = new \ReflectionClass( 'WooCommerce_Product_Search' );
		$constants['core'] = $r->getConstants();
		$r = new \ReflectionClass( 'WooCommerce_Product_Search_Service' );
		$constants['service'] = $r->getConstants();

		$attributes = array();
		$product_attribute_taxonomies = wc_get_attribute_taxonomy_names();
		foreach( $product_attribute_taxonomies as $product_attribute_taxonomy ) {
			if ( $taxonomy = get_taxonomy( $product_attribute_taxonomy ) ) {

				$attributes[] = array(
					'value' => $taxonomy->name,
					'label' => $taxonomy->label
				);
			}
		}
		$constants['attributes'] = $attributes;

		$constants['taxonomies'] = array(
			array(
				'value' => 'product_cat',
				'label' => __( 'Product Category', 'woocommerce-product-search' )
			),
			array(
				'value' => 'product_tag',
				'label' => __( 'Product Tag', 'woocommerce-product-search' )
			)
		);

		$constants['comp'] = array(
			'ICL_LANGUAGE_CODE' => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : ''
		);

		wp_localize_script(
			'woocommerce-product-search-blocks',
			'woocommerce_product_search_blocks',
			$constants
		);

		wp_register_style(
			'woocommerce-product-search-blocks-editor',
			WOO_PS_PLUGIN_URL . '/lib/blocks/css/woocommerce-product-search-blocks-editor.css',
			array( 'wp-edit-blocks' ),
			WOO_PS_PLUGIN_VERSION

		);

		wp_register_style(
			'woocommerce-product-search-blocks-style',
			WOO_PS_PLUGIN_URL . '/lib/blocks/css/woocommerce-product-search-blocks-style.css',
			array(),
			WOO_PS_PLUGIN_VERSION

		);
	}
}

Blocks::init();
