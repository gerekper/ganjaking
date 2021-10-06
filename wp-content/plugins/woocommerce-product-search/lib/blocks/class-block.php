<?php
/**
 * class-block.php
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

abstract class Block {

	public static function init() {
		add_action( 'init', array( static::class, 'wp_init' ) );
	}

	public static function wp_init() {

		if ( !wp_style_is( 'product-search', 'registered' ) ) {
			\WooCommerce_Product_Search_Service::wp_enqueue_scripts();
		}
		static::register_block_type();
	}

	public abstract static function register_block_type();

	public abstract static function render( $atts, $content = '' );
}
