<?php
/**
 * class-product-filter-category-block.php
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

class Product_Filter_Category_Block extends Block {

	public static function register_block_type() {
		register_block_type(
			'woocommerce-product-search/woocommerce-product-filter-category',
			array(
				'api_version' => 2,
				'style_handles' => array( 'product-search' ),

				'editor_style_handles' => array( 'woocommerce-product-search-blocks-editor' ),
				'editor_script_handles' => array( 'woocommerce-product-search-blocks' ),
				'render_callback' => array( __CLASS__, 'render' ),
				'attributes' => array(
					'auto_expand' => array(
						'type' => 'boolean',
						'default' => true
					),
					'auto_retract' => array(
						'type' => 'boolean',
						'default' => true
					),
					'child_of' => array(
						'type' => 'string',
						'default' => ''
					),
					'container_class' => array(
						'type' => 'string',
						'default' => ''
					),
					'container_id' => array(
						'type' => 'string',
						'default' => ''
					),
					'depth' => array(
						'type' => 'number',
						'default' => 0
					),
					'exclude' => array(
						'type' => 'string',
						'default' => ''
					),
					'expandable_from_depth' => array(
						'type' => 'number',
						'default' => 0
					),
					'expander' => array(
						'type' => 'boolean',
						'default' => true
					),
					'filter' => array(
						'type' => 'boolean',
						'default' => true
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
					'heading_no_results' => array(
						'type' => 'string',
						'default' => ''
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
					'hide_empty' => array(
						'type' => 'boolean',
						'default' => true
					),
					'hierarchical' => array(
						'type' => 'boolean',
						'default' => true
					),
					'include' => array(
						'type' => 'string',
						'default' => ''
					),
					'multiple' => array(
						'type' => 'boolean',
						'default' => false
					),
					'none_selected' => array(
						'type' => 'string',
						'default' => __( 'Any Category', 'woocommerce-product-search' ),
					),
					'number' => array(
						'type' => array( 'number', 'string' ),
						'default' => ''
					),
					'order' => array(
						'type' => 'string',
						'default' => 'ASC'
					),
					'orderby' => array(
						'type' => 'string',
						'default' => 'name'
					),
					'shop_only' => array(
						'type' => 'boolean',
						'default' => 'no'
					),
					'show' => array(
						'type' => 'string',
						'default' => 'all'
					),
					'show_ancestors' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_count' => array(
						'type' => 'boolean',
						'default' => false
					),
					'show_heading' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_names' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_parent_names' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_parent_navigation' => array(
						'type' => 'boolean',
						'default' => false
					),
					'show_parent_thumbnails' => array(
						'type' => 'boolean',
						'default' => false
					),
					'show_selected_thumbnails' => array(
						'type' => 'boolean',
						'default' => true
					),
					'show_thumbnails' => array(
						'type' => 'boolean',
						'default' => false
					),
					'size' => array(
						'type' => array( 'number', 'string' ),
						'default' => ''
					),
					'style' => array(
						'type' => 'string',
						'default' => 'list'
					),
					'taxonomy' => array(
						'type' => 'string',
						'default' => 'product_cat'
					),
					'toggle' => array(
						'type' => 'boolean',
						'default' => true
					)
				)
			)
		);
	}

	public static function render( $atts, $content = '' ) {
		return woocommerce_product_search_filter_category( $atts );
	}
}

Product_Filter_Category_Block::init();
