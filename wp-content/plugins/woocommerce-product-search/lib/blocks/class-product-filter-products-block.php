<?php
/**
 * class-product-filter-products-block.php
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

class Product_Filter_Products_Block extends Block {

	public static function register_block_type() {

		register_block_type(
			'woocommerce-product-search/woocommerce-product-filter-products',
			array(
				'api_version' => 2,
				'style' => 'product-search',
				'editor_style' => 'woocommerce-product-search-blocks-editor',
				'editor_script' => 'woocommerce-product-search-blocks',
				'render_callback' => array( __CLASS__, 'render' ),
				'attributes' => array(
					'columns' => array(
						'type' => 'number',
						'default' => 3
					),
					'orderby' => array(
						'type' => 'string',
						'default' => ''
					),
					'order' => array(
						'type' => 'string',
						'default' => 'ASC'
					),
					'per_page' => array(
						'type' => 'number',
						'default' => 12
					),
					'show_prefix' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_suffix' => array(
						'type' => 'boolean',
						'default' => false
					),
					'show_catalog_ordering' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_result_count' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_pagination' => array(
						'type' => 'boolean',
						'default' => true
					),

				)
			)
		);
	}

	public static function render( $atts, $content = '' ) {

		return woocommerce_product_filter_products( $atts );
	}
}

Product_Filter_Products_Block::init();
