<?php
/**
 * class-redis-cache-base.php
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
 * Basis for Redis-based caches.
 */
abstract class Redis_Cache_Base extends Cache_Base {

	/**
	 * Default host.
	 *
	 * @var string
	 */
	public const HOST_DEFAULT = 'localhost';

	/**
	 * Default port.
	 *
	 * @var integer
	 */
	public const PORT_DEFAULT = 6379;

	/**
	 * Flush scan count
	 *
	 * @var integer
	 */
	protected const FLUSH_SCAN_COUNT = 100;

	/**
	 * @var string
	 */
	protected $id = 'redis';

	/**
	 * @var string
	 */
	protected $host = self::HOST_DEFAULT;

	/**
	 * @var int
	 */
	protected $port = self::PORT_DEFAULT;

	/**
	 * @var string
	 */
	protected $username = null;

	/**
	 * @var string
	 */
	protected $password = null;

	/**
	 * Build the key for the given key and group.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string
	 */
	protected function get_cache_key( $key, $group = '' ) {
		$group = $this->get_group( $group );
		$blog_id = get_current_blog_id();
		return sprintf( '%d_%s_%s', intval( $blog_id ), md5( $group ), $key );
	}

}
