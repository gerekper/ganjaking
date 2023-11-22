<?php
/**
 * class-cache-boot.php
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
 * Boot-loader for the search engine's caching system.
 */
class Cache_Boot {

	/**
	 * Loads resources, registers actions.
	 */
	public static function init() {
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-guardian.php';

		require_once 'class-slot.php';
		require_once 'class-cache-settings.php';
		require_once 'class-cache-base.php';
		require_once 'class-cache.php';
		require_once 'class-transitory-cache.php';
		require_once 'class-memcached-cache.php';
		require_once 'class-redis-cache-base.php';
		require_once 'class-redis-cache.php';
		if ( class_exists( '\Redis' ) ) {
			require_once 'class-phpredis-cache.php';
		}
		require_once 'class-file-cache.php';
		require_once 'class-object-cache.php';
		require_once 'class-cache-control.php';
		require_once 'class-cache-object.php';
		add_action( 'rest_api_init', array( __CLASS__, 'rest_api_init' ) );

		$cache = Cache::get_instance();
	}

	/**
	 * Registers the REST controller of the caching system.
	 */
	public static function rest_api_init() {
		require_once 'class-rest-cache-controller.php';
		$controller = new REST_Cache_Controller();
		$controller->register_routes();
	}
}

Cache_Boot::init();
