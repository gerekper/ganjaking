<?php
/**
 * class-rest-cache-controller.php
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
 * Cache REST API Controller
 */
class REST_Cache_Controller extends \WP_REST_Controller {

	protected $namespace = 'wps/v1';

	protected $rest_base = 'search/cache';

	/**
	 * Registers the Cache REST routes.
	 *
	 * {@inheritDoc}
	 *
	 * @see \WP_REST_Controller::register_routes()
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/status',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'status' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array()
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/flush',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'flush' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array()
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/gc',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'gc' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array()
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/gcs',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'gc' ),
				'permission_callback' => array( $this, 'scheduled_gc_permissions_check' ),
				'args'                => array()
			)
		);
	}

	/**
	 * Status
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function status( $request ) {
		ob_start();
		$t0 = function_exists( 'microtime' ) ? microtime( true ) : time();
		$status = array();
		$cache = Cache::get_instance();
		$cache_id = $request->get_param( 'cache_id' );
		if ( $cache_id !== null ) {
			if ( $cache->has_cache( $cache_id ) ) {
				$instance = $cache->get_cache( 'file_cache' );
				if ( $instance ) {
					if ( method_exists( $instance, 'get_status' ) ) {
						$status = $instance->get_status();
						$status['cache_id'] = $instance->get_id();
					}
				}
			}
		}

		$dt = ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $t0;
		$status['dt'] = $dt;

		$response = rest_ensure_response( $status );
		ob_get_clean();
		return $response;
	}

	/**
	 * Flush
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function flush( $request ) {
		ob_start();
		$t0 = function_exists( 'microtime' ) ? microtime( true ) : time();
		$flushed = false;
		$cache = Cache::get_instance();
		$group = $request->get_param( 'group' );
		$cache_id = $request->get_param( 'cache_id' );
		if ( $cache_id === null ) {
			$flushed = $cache->flush( $group );
		} else {
			if ( $cache->has_cache( $cache_id ) ) {
				$flushed = $cache->flush_cache( $cache_id );
			} else {
				$cache_settings = Cache_Settings::get_instance();
				$settings = $cache_settings->get();
				if ( isset( $settings[$cache_id] ) ) {
					$settings[$cache_id]['enabled'] = true;
					$id = 'tmp_' . $cache_id;
					$caches = array(
						'id' => $id,
						$cache_id => $settings[$cache_id]
					);
					$tmp_cache = Cache::create_instance( $caches );
					$flushed = $tmp_cache->flush( $group );
					Cache::delete_instance( $id );
				}
			}
		}
		$dt = ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $t0;
		$response = rest_ensure_response( array( 'flush' => $flushed, 'dt' => $dt ) );
		ob_get_clean();
		return $response;
	}

	/**
	 * GC
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function gc( $request ) {
		ob_start();
		$t0 = function_exists( 'microtime' ) ? microtime( true ) : time();
		$success = false;
		$cache = Cache::get_instance();
		$group = $request->get_param( 'group' );
		$cache_id = $request->get_param( 'cache_id' );
		if ( $cache_id === null ) {
			$success = $cache->gc( $group );
		} else {
			if ( $cache->has_cache( $cache_id ) ) {
				$success = $cache->gc_cache( $cache_id );
			} else {
				$cache_settings = Cache_Settings::get_instance();
				$settings = $cache_settings->get();
				if ( isset( $settings[$cache_id] ) ) {
					$settings[$cache_id]['enabled'] = true;
					$id = 'tmp_' . $cache_id;
					$caches = array(
						'id' => $id,
						$cache_id => $settings[$cache_id]
					);
					$tmp_cache = Cache::create_instance( $caches );
					$success = $tmp_cache->gc( $group );
					Cache::delete_instance( $id );
				}
			}
		}
		$dt = ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $t0;
		$response = rest_ensure_response( array( 'gc' => $success, 'dt' => $dt ) );
		ob_get_clean();
		return $response;
	}

	/**
	 * Checks if a given request has access to get a specific item.
	 *
	 * Requires the current user to have site administrator privileges or permission to manage WooCommerce.
	 *
	 * {@inheritDoc}
	 *
	 * @see \WP_REST_Controller::get_item_permissions_check()
	 */
	public function get_item_permissions_check( $request ) {
		$has_permission = is_super_admin();
		if ( !$has_permission ) {
			$has_permission = current_user_can( 'manage_woocommerce' );
		}
		if ( !$has_permission ) {
			return new \WP_Error(
				'woocommerce_product_search_rest_cache_get_denied',
				__( 'Access denied.', 'woocommerce-product-search' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Requires the current user to have site administrator privileges or permission to manage WooCommerce.
	 *
	 * {@inheritDoc}
	 *
	 * @see \WP_REST_Controller::delete_item_permissions_check()
	 */
	public function delete_item_permissions_check( $request ) {
		$has_permission = is_super_admin();
		if ( !$has_permission ) {
			$has_permission = current_user_can( 'manage_woocommerce' );
		}
		if ( !$has_permission ) {
			return new \WP_Error(
				'woocommerce_product_search_rest_cache_delete_denied',
				__( 'Access denied.', 'woocommerce-product-search' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Special request check for scheduled GC.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return boolean
	 */
	public function scheduled_gc_permissions_check( $request ) {

		$result = false;
		$token = get_transient( 'wps_scheduled_gc_token' );
		if ( !empty( $token ) ) {
			$request_token = $request->get_param( 'token' );
			if ( !empty( $request_token ) ) {
				if ( $request_token === $token ) {
					delete_transient( 'wps_scheduled_gc_token' );
					$result = true;
				}
			}
		}
		return $result;
	}
}
