<?php
/**
 * class-cache-base.php
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
 * Cache base.
 */
abstract class Cache_Base {

	/**
	 * @var int
	 */
	const UNLIMITED = 0;

	/**
	 * @var int
	 */
	const MINUTE = 60;

	/**
	 * @var int
	 */
	const HOUR = 3600;

	/**
	 * @var int
	 */
	const DAY = 86400;

	/**
	 * @var int
	 */
	const WEEK = 657000;

	/**
	 * @var int
	 */
	const MONTH = 2628000;

	/**
	 * @var int
	 */
	const QUARTER = 7884000;

	/**
	 * @var int
	 */
	const YEAR = 31536000;

	/**
	 * @var string|null
	 */
	protected $id = null;

	/**
	 * @var int
	 */
	protected $priority = 0;

	/**
	 * @var boolean
	 */
	protected $active = false;

	/**
	 * @var boolean
	 */
	protected $unigroup = false;

	/**
	 * @var boolean
	 */
	protected $volatile = false;

	/**
	 * @var boolean
	 */
	protected $ui = true;

	/**
	 * Timeout in seconds
	 *
	 * @var float
	 */
	protected $connection_timeout = 5.0;

	/**
	 * Timeout in seconds
	 *
	 * @var float
	 */
	protected $timeout = 1.0;

	/**
	 * Create an instance.
	 *
	 * Parameters:
	 *
	 * - int 'priority'
	 *
	 * @param array $params instance parameters
	 */
	public function __construct( $params = null ) {
		if ( isset( $params['priority'] ) && $params['priority'] !== null ) {
			$this->set_priority( $params['priority'] );
		}
		if ( isset( $params['ui'] ) && $params['ui'] !== null ) {
			$this->set_ui( $params['ui'] );
		}
	}

	/**
	 * Provide the cache's identifier.
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Provide the cache's priority.
	 *
	 * @return int
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * Whether the cache is active.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return $this->active;
	}

	/**
	 * Determine whether to operate in unigroup mode.
	 *
	 * @param boolean $unigroup
	 *
	 * @return boolean the cache's unigroup property
	 */
	public function set_unigroup( $unigroup ) {
		if ( is_bool( $unigroup ) ) {
			$this->unigroup = boolval( $unigroup );
		}
		return $this->unigroup;
	}

	/**
	 * Whether the cache is operating in unigroup mode.
	 *
	 * @return boolean
	 */
	public function get_unigroup() {
		return $this->unigroup;
	}

	/**
	 * Whether the cache is volatile.
	 *
	 * @return boolean
	 */
	public function is_volatile() {
		return $this->volatile;
	}

	/**
	 * Whether UI is enabled.
	 *
	 * @return boolean
	 */
	public function is_ui() {
		return $this->ui;
	}

	/**
	 * Determine the cache's priority.
	 *
	 * @param int $priority
	 *
	 * @return int the cache's priority
	 */
	public function set_priority( $priority ) {
		if ( is_numeric( $priority ) ) {
			$priority = intval( $priority );
			$this->priority = $priority;
		}
		return $this->priority;
	}

	/**
	 * Enable or disable the UI.
	 *
	 * @param boolean $ui
	 *
	 * @return boolean
	 */
	public function set_ui( $ui ) {
		if ( is_bool( $ui ) ) {
			$ui = boolval( $ui );
			$this->ui = $ui;
		}
		return $this->ui;
	}

	/**
	 * Get from cache.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return mixed|null
	 */
	abstract public function get( $key, $group = '' );

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
	abstract public function set( $key, $data, $group = '', $expire = 0 );

	/**
	 * Flush the cache.
	 *
	 * @param string $group
	 *
	 * @return boolean
	 */
	abstract public function flush( $group = null );

	/**
	 * GC the cache.
	 *
	 * @param string $group
	 */
	abstract public function gc( $group = null );

	/**
	 * Determines the effective cache group.
	 *
	 * @param string $group
	 *
	 * @return string
	 */
	protected function get_group( $group ) {

		if ( $this->unigroup ) {
			return $group;
		}

		$roles = array();
		$group_ids = array();

		if ( function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
			if ( $user->exists() ) {
				if ( WPS_ROLES_CACHE ) {

					if ( is_array( $user->roles ) ) {
						$roles = $user->roles;
						sort( $roles );
					}
				}

				if ( WPS_GROUPS_CACHE ) {
					if ( class_exists( '\Groups_User' ) ) {
						$groups_user = new \Groups_User( $user->ID );
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

		$group_suffix = apply_filters( 'woocommerce_product_search_engine_cache_group_suffix', '', $group );
		if ( !is_string( $group_suffix ) ) {
			$group_suffix = '';
		} else {
			$group_suffix = trim( $group_suffix );
		}
		if ( strlen( $group_suffix ) > 0 ) {
			$group = $group . '_' . $group_suffix;
		}

		return $group;
	}

	/**
	 * Get all derived groups for the given group.
	 *
	 * @param string $group
	 *
	 * @return string[]
	 */
	protected function get_all_groups( $group ) {

		$groups = array( $group );

		if ( $this->unigroup ) {
			return $groups;
		}

		$roles = array();
		$group_ids = array();

		$wp_roles = wp_roles();
		$role_names = $wp_roles->get_names();
		if ( is_array( $role_names ) ) {
			$roles = array_keys( $role_names );
			sort( $roles );
		}

		if ( class_exists( '\Groups_Group' ) && method_exists( 'get_group_ids' ) ) {
			$group_ids = \Groups_Group::get_group_ids();
			$group_ids = array_map( 'intval', $group_ids );
			sort( $group_ids, SORT_NUMERIC );
		}

		$role_sequences = array();
		if ( count( $roles ) > 0 ) {
			$role_sequences = $this->get_sequences( $roles );
		}
		array_unshift( $role_sequences, array() );

		$group_id_sequences = array();
		if ( count( $group_ids ) > 0 ) {
			$group_id_sequences = $this->get_sequences( $group_ids );
		}
		array_unshift( $group_id_sequences, array() );

		foreach ( $role_sequences as $roles ) {
			foreach( $group_id_sequences as $group_ids ) {
				$_group = $group;

				if ( count( $roles ) > 0 ) {
					$_group .= '_';
					$_group .= implode( '_', $roles );
				}
				if ( count( $group_ids ) > 0 ) {
					$_group .= '_';
					$_group .= implode( '_', $group_ids );
				}
				if ( !in_array( $_group, $groups ) ) {
					$groups[] = $_group;
				}
			}
		}

		$groups_filtered = array();
		foreach ( $groups as $group ) {
			$group_suffix = apply_filters( 'woocommerce_product_search_engine_cache_group_suffix', '', $group );
			if ( !is_string( $group_suffix ) ) {
				$group_suffix = '';
			} else {
				$group_suffix = trim( $group_suffix );
			}
			if ( strlen( $group_suffix ) > 0 ) {
				$group = $group . '_' . $group_suffix;
			}
			$groups_filtered[] = $group;
		}

		return $groups_filtered;
	}

	/**
	 * Provide sequential combinations of elements.
	 *
	 * @param array $elements
	 *
	 * @return array
	 */
	private function get_sequences( $elements ) {
		$sequences = array();
		$k = count( $elements );
		if ( $k > 0 ) {
			$n = pow( 2, $k );
			for ( $i = 0; $i < $n; $i++ ) {
				$spots = sprintf( "%0{$k}b", $i );
				$sequence = array();
				for ( $j = 0; $j < $k; $j++ ) {
					if ( $spots[$j] ) {
						$sequence[] = $elements[$j];
					}
				}
				if ( count( $sequence ) > 0 ) {
					$sequences[] = $sequence;
				}
			}
		}
		return $sequences;
	}
}
