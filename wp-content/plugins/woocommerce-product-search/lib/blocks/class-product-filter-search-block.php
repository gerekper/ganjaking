<?php
/**
 * class-product-filter-search-block.php
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

class Product_Filter_Search_Block extends Block {

	public static function register_block_type() {
		register_block_type(
			'woocommerce-product-search/woocommerce-product-filter',
			array(
				'api_version' => 2,
				'style_handles' => array( 'product-search' ),
				'editor_style_handles' => array( 'woocommerce-product-search-blocks-editor' ),
				'editor_script_handles' => array( 'woocommerce-product-search-blocks' ),
				'render_callback' => array( __CLASS__, 'render' ),
				'attributes' => array(

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

					'order' => array(
						'type' => 'string',
						'default' => ''
					),
					'order_by' => array(
						'type' => 'string',
						'default' => ''
					),

					'shop_only' => array(
						'type' => 'boolean',
						'default' => false
					),

					'placeholder' => array(
						'type' => 'string',
						'default' => __( 'Search', 'woocommerce-product-search' ),
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
					'submit_button' => array(
						'type' => 'boolean',
						'default' => false
					),
					'submit_button_label' => array(
						'type' => 'string',
						'default' => __( 'Search', 'woocommerce-product-search' )
					),
					'show_clear' => array(
						'type' => 'boolean',
						'default' => true
					),
					'update_address_bar' => array(
						'type' => 'boolean',
						'default' => true
					),
					'update_document_title' => array(
						'type' => 'boolean',
						'default' => false
					),
					'unpage_url' => array(
						'type' => 'boolean',
						'default' => true
					),

					'breadcrumb_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-breadcrumb'
					),
					'products_header_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-products-header',
					),
					'products_container' => array(
						'type' => 'string',
						'default' => '.products'
					),
					'product_container' => array(
						'type' => 'string',
						'default' => '.product'
					),
					'info_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-info'
					),
					'ordering_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-ordering'
					),
					'pagination_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-pagination'
					),
					'result_count_container' => array(
						'type' => 'string',
						'default' => '.woocommerce-result-count'
					),

					'style' => array(
						'type' => 'string',
						'default' => '',
					),

					'heading' => array(
						'type' => array( 'null', 'string' )
					),
					'heading_class' => array(
						'type' => 'string',
						'default' => ''
					),
					'heading_element' => array(
						'type' => 'string',
						'default' => 'div'
					),
					'heading_id' => array(
						'type' => 'string',
						'default' => ''
					),
					'show_heading' => array(
						'type' => 'boolean',
						'default' => false
					)
				)
			)
		);
	}

	public static function render( $atts, $content = '' ) {
		return woocommerce_product_search_filter( $atts );
	}
}

Product_Filter_Search_Block::init();
