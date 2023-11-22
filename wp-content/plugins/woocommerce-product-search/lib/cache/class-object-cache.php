<?php
/**
 * class-object-cache.php
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
 * Uses the WordPress Object Cache.
 *
 * @see https://developer.wordpress.org/reference/classes/wp_object_cache/
 */
class Object_Cache extends Cache_Base {

	protected $id = 'object_cache';

	/**
	 * Create an instance.
	 *
	 * @param array $params instance parameters
	 * @param int $params['priority']
	 */
	public function __construct( $params = null ) {
		parent::__construct( $params );

		$this->active = true;
		$this->volatile = !wp_using_ext_object_cache();
	}

	/**
	 * Flush the cache.
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	public function flush( $group = null ) {
		$flushed = false;
		if ( $group === null || $group === '' ) {
			if ( function_exists( 'wp_cache_flush' ) ) {
				$flushed = wp_cache_flush();
			}
		} else {
			if ( function_exists( 'wp_cache_flush_group' ) ) {
				$flushed = wp_cache_flush_group( $group );
			} else if ( function_exists( 'wp_cache_flush' ) ) {
				$flushed = wp_cache_flush();
			}
		}
		return $flushed;
	}

	public function gc( $group = null ) {
		return true;
	}

	/**
	 * Get from cache.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return mixed|null
	 */
	public function get( $key, $group = '' ) {
		$value = null;
		$group = $this->get_group( $group );
		$found = null;
		$object = wp_cache_get( $key, $group, false, $found );
		if ( !( $object instanceof Cache_Object ) ) {
			$object = null;
		} else {
			if ( $object->has_expired() ) {
				$this->delete( $key, $group );
				$object = null;
			}
		}
		if ( $object !== null ) {
			$value = $object->get_value();
		}
		return $value;
	}

	/**
	 * Store in cache.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param string $group
	 * @param int $expire
	 *
	 * @return boolean
	 */
	public function set( $key, $data, $group = '', $expire = 0 ) {
		$object = new Cache_Object( $key, $data, $expire );
		$group = $this->get_group( $group );
		$success = wp_cache_set( $key, $object, $group, $expire );
		return $success;
	}

	/**
	 * Delete from cache.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return boolean
	 */
	public function delete( $key, $group = '' ) {
		$deleted = false;
		$group = $this->get_group( $group );
		$deleted = wp_cache_delete( $key, $group );
		return $deleted;
	}

}
