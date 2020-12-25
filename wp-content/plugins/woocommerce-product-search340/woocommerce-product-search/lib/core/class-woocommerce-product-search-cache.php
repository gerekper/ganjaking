<?php
/**
 * class-woocommerce-product-search-cache.php
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
 * @since 3.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'wps_cache_get' ) ) {
	/**
	 * Get from cache.
	 *
	 * @param string $key
	 * @param string $group
	 * @param boolean $force
	 * @param boolean $found
	 *
	 * @return mixed|false
	 */
	function wps_cache_get( $key, $group = '', $force = false, &$found = null ) {
		return WooCommerce_Product_Search_Cache::get( $key, $group, $force, $found );
	}
}

if ( !function_exists( 'wps_cache_set' ) ) {
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
	function wps_cache_set( $key, $data, $group = '', $expire = 0 ) {
		return WooCommerce_Product_Search_Cache::set( $key, $data, $group, $expire );
	}
}

/**
 * Cache controller.
 *
 * Adds support for role and group-based caching.
 */
class WooCommerce_Product_Search_Cache {

	/**
	 * Get from cache.
	 *
	 * @param string $key
	 * @param string $group
	 * @param boolean $force
	 * @param boolean $found
	 *
	 * @return mixed|false
	 */
	public static function get( $key, $group = '', $force = false, &$found = null ) {
		return wp_cache_get( $key, self::get_group( $group ), $force, $found );
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
	public static function set( $key, $data, $group = '', $expire = 0 ) {
		return wp_cache_set( $key, $data, self::get_group( $group ), $expire );
	}

	/**
	 * Determines the effective cache group.
	 *
	 * @param string $group
	 *
	 * @return string
	 */
	private static function get_group( $group ) {
		$roles = array();
		$group_ids = array();

		if ( function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
			if ( $user->exists() ) {
				if ( WPS_ROLES_CACHE ) {

					$roles = $user->roles;
					sort( $roles );
				}

				if ( WPS_GROUPS_CACHE ) {
					if ( class_exists( 'Groups_User' ) ) {
						$groups_user = new Groups_User( $user->ID );
						$group_ids = $groups_user->group_ids_deep;
						$group_ids = array_map( 'intval', $group_ids );
						sort( $group_ids, SORT_NUMERIC );
					}
				}
			}
		}

		if ( count( $roles ) > 0 ) {
			$group .= '_';
			$group .= implode( '_', $roles );
		}
		if ( count( $group_ids ) > 0 ) {
			$group .= '_';
			$group .= implode( '_', $group_ids );
		}

		return $group;
	}
}
