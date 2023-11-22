<?php
/**
 * class-filter-renderer.php
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
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter renderer, abstract base class.
 */
abstract class Filter_Renderer {

	/**
	 * Seconds
	 *
	 * @var int
	 */
	const DATA_CACHE_LIFETIME = 900;

	/**
	 * Seconds
	 *
	 * @var int
	 */
	const RENDER_CACHE_LIFETIME = 300;

	/**
	 * Data cache lifetime.
	 *
	 * @return int
	 */
	protected static function get_data_cache_lifetime() {
		$lifetime = apply_filters( 'woocommerce_product_search_filter_renderer_data_cache_lifetime', static::DATA_CACHE_LIFETIME, static::class );
		if ( is_numeric( $lifetime ) ) {
			$lifetime = max( 0, intval( $lifetime ) );
		} else {
			$lifetime = static::DATA_CACHE_LIFETIME;
		}
		return $lifetime;
	}

	/**
	 * Render cache lifetime.
	 *
	 * @return int
	 */
	protected static function get_render_cache_lifetime() {
		$lifetime = apply_filters( 'woocommerce_product_search_filter_renderer_render_cache_lifetime', static::RENDER_CACHE_LIFETIME, static::class );
		if ( is_numeric( $lifetime ) ) {
			$lifetime = max( 0, intval( $lifetime ) );
		} else {
			$lifetime = static::RENDER_CACHE_LIFETIME;
		}
		return $lifetime;
	}
}
