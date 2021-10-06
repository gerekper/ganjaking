<?php
/**
 * class-product-search-field-block.php
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

class Product_Search_Field_Block extends Block {

	public static function register_block_type() {
		register_block_type(
			'woocommerce-product-search/woocommerce-product-search',
			array(
				'api_version' => 2,
				'style' => 'product-search',

				'editor_style' => 'woocommerce-product-search-blocks-editor',
				'editor_script' => 'woocommerce-product-search-blocks',
				'render_callback' => array( __CLASS__, 'render' ),
				'attributes' => array(
					'order' => array(
						'type' => 'string',
						'default' => 'DESC'
					),
					'order_by' => array(
						'type' => 'string',
						'default' => 'date'
					),
					'title' => array(
						'type' => 'boolean',
						'default' => true
					),
					'excerpt' => array(
						'type' => 'boolean',
						'default' => true
					),
					'content' => array(
						'type' => 'boolean',
						'default' => true
					),
					'categories' => array(
						'type' => 'boolean',
						'default' => true
					),
					'attributes' => array(
						'type' => 'boolean',
						'default' => true
					),
					'tags' => array(
						'type' => 'boolean',
						'default' => true
					),
					'sku' => array(
						'type' => 'boolean',
						'default' => true
					),
					'limit' => array(
						'type' => 'number',
						'default' => \WooCommerce_Product_Search_Service::DEFAULT_LIMIT
					),
					'height' => array(
						'type' => 'string',
						'default' => ''
					),
					'height_value' => array(
						'type' => 'string',
						'default' => ''
					),
					'height_unit' => array(
						'type' => 'string',
						'default' => ''
					),
					'category_results' => array(
						'type' => 'boolean',
						'default' => true
					),
					'category_limit' => array(
						'type' => 'number',
						'default' => \WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT
					),
					'product_thumbnails' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_description' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_price' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_add_to_cart' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_more' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_clear' => array(
						'type' => 'boolean',
						'default' => true
					),
					'placeholder' => array(
						'type' => 'string',
						'default' => __( 'Search', 'woocommerce-product-search' ),
					),
					'no_results' => array(
						'type' => 'string',
						'default' => ''
					),
					'blinker_timeout' => array(
						'type' => array( 'number', 'string' ),
						'default' => ''
					),
					'delay' => array(
						'type' => 'number',
						'default' => \WooCommerce_Product_Search::DEFAULT_DELAY
					),
					'characters' => array(
						'type' => 'number',
						'default' => \WooCommerce_Product_Search::DEFAULT_CHARACTERS
					),
					'dynamic_focus' => array(
						'type' => 'boolean',
						'default' => true
					),
					'floating' => array(
						'type' => 'boolean',
						'default' => true
					),
					'inhibit_enter' => array(
						'type' => 'boolean',
						'default' => false
					),
					'submit_button' => array(
						'type' => 'boolean',
						'default' => false
					),
					'submit_button_label' => array(
						'type' => 'string',
						'default' => __( 'Search', 'woocommerce-product-search' )
					),
					'navigable' => array(
						'type' => 'boolean',
						'default' => true
					),
					'wpml' => array(
						'type' => 'boolean',
						'default' => false
					)
				)
			)
		);
	}

	public static function render( $atts, $content = '' ) {
		return woocommerce_product_search( $atts );
	}
}

Product_Search_Field_Block::init();
