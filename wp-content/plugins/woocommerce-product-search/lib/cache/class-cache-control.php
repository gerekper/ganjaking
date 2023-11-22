<?php
/**
 * class-cache-control.php
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
 * Cache handler.
 *
 * Role and group-based caching, lifespan management.
 */
class Cache_Control {

	/**
	 * Marker threshold option key.
	 *
	 * @var string
	 */
	const MARKER_THRESHOLD = 'marker_threshold';

	/**
	 * Default marker threshold is one minute.
	 *
	 * @var int
	 */
	const MARKER_THRESHOLD_DEFAULT = 60;

	/**
	 * Latest marker
	 *
	 * @var int
	 */
	private static $marker = null;

	/**
	 * Marker time frame
	 *
	 * @var int
	 */
	private static $marker_threshold = null;

	/**
	 * Value setup and actions.
	 */
	public static function init() {
		self::$marker = get_option( 'woocommerce_product_search_engine_cache_marker', null );
		if ( self::$marker !== null ) {
			self::$marker = intval( self::$marker );
		}
		$options = get_option( 'woocommerce-product-search', array() );
		self::$marker_threshold = isset( $options[self::MARKER_THRESHOLD] ) ? intval( $options[self::MARKER_THRESHOLD] ) : self::MARKER_THRESHOLD_DEFAULT;

		add_action( 'woocommerce_product_search_indexer_index_end', array( __CLASS__, 'woocommerce_product_search_indexer_index_end' ) );

		add_action( 'woocommerce_product_search_engine_cache_update', array( __CLASS__, 'woocommerce_product_search_engine_cache_update' ) );

		add_action( 'woocommerce_product_search_indexer_purge_end', array( __CLASS__, 'woocommerce_product_search_indexer_purge_end' ) );

	}

	/**
	 * Triggers a marker update.
	 *
	 * @param int $post_id
	 */
	public static function woocommerce_product_search_indexer_index_end( $post_id ) {
		self::woocommerce_product_search_engine_cache_update();
	}

	/**
	 * Triggers a marker update.
	 *
	 * @param int $post_id
	 */
	public static function woocommerce_product_search_indexer_purge_end( $post_id ) {
		self::woocommerce_product_search_engine_cache_update();
	}

	/**
	 * Indicate modifications have been made that should update the search engine's cache.
	 */
	public static function woocommerce_product_search_engine_cache_update() {
		$t = time();
		if ( $t > self::$marker ) {
			self::$marker = $t;
			update_option( 'woocommerce_product_search_engine_cache_marker', self::$marker );
			if ( WPS_CACHE_DEBUG ) {
				wps_log_info( sprintf( "Cache marker updated [%d]", self::$marker ) );
			}
		}
	}

	/**
	 * Whether the timestamp has expired.
	 *
	 * @param int $timestamp
	 *
	 * @return boolean
	 */
	public static function has_timestamp_expired( $timestamp ) {

		$expired = false;
		if ( self::$marker !== null ) {
			$threshold = self::$marker_threshold !== null ? self::$marker_threshold : 0;
			$delta = max( 0, $threshold - ( time() - self::$marker ) );
			if ( $timestamp < self::$marker - $delta ) {
				$expired = true;
			}
		}
		return $expired;
	}
}

Cache_Control::init();
