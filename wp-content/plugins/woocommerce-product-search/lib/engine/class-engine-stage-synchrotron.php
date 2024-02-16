<?php
/**
 * class-engine-stage-synchrotron.php
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

class Engine_Stage_Synchrotron extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_synchro';

	const CACHE_LIFETIME = Cache::UNLIMITED;

	protected $stage_id = 'synchrotron';

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {

					default:
						$set_param = false;
				}
				if ( $set_param ) {
					$params[$key] = $value;
				}
			}
			foreach ( $params as $key => $value ) {
				$this->$key = $value;
			}
		}
	}

	public function get_parameters() {
		return parent::get_parameters();
	}

	protected function get_cache_context() {
		$cache_context = $this->get_parameters();
		if ( $this->engine !== null ) {
			$cache_context['context'] = json_encode( $this->engine->get_parameters() );
		}
		$cache_context = apply_filters( 'woocommerce_product_search_engine_stage_cache_context', $cache_context, $this );
		return $cache_context;
	}

	public function get_matching_ids( &$ids ) {

		global $wpdb;

		$this->timer->start();

		$cache_context = $this->get_cache_context();
		$cache_key = $this->get_cache_key( $cache_context );
		$cache = Cache::get_instance();
		$ids = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( is_array( $ids ) ) {
			$this->count = count( $ids );
			$this->is_cache_hit = true;
			$this->timer->stop();
			$this->timer->log( 'verbose' );
			return;
		}
		$this->is_cache_hit = false;

		if ( $this->engine !== null ) {

			$ids = $this->engine->get_matrix()->get_ids();
		} else {
			$ids = array();
		}
		if ( !empty( $ids ) ) {
			$object_term_table = \WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

			Tools::unique_int( $ids );
			$query = sprintf(
				"SELECT object_id, parent_object_id, object_type FROM $object_term_table WHERE term_id = 0 AND object_id IN (%s)",
				implode( ',', $ids )
			);
			$results = $wpdb->get_results( $query );
			if ( is_array( $results ) ) {

				$map = array();
				foreach ( $results as $result ) {
					$object_id = $result->object_id !== null ? (int) $result->object_id : null;
					$parent_object_id = $result->parent_object_id !== null ? (int) $result->parent_object_id : null;
					if ( $object_id !== null && $result->object_type !== null ) {
						switch ( $result->object_type ) {
							case 'simple':
								$map[$object_id] = array( $object_id );
								break;
							case 'variable':
								if ( !key_exists( $object_id, $map ) ) {
									$map[$object_id] = array();
								}
								break;
							case 'variation':
								$map[$object_id] = array( $object_id );
								if ( !key_exists( $parent_object_id, $map ) ) {
									$map[$parent_object_id] = array();
								}
								$map[$parent_object_id][] = $object_id;
								break;
							default:
								$map[$object_id] = array( $object_id );
						}
					}
				}

				$ids = array();
				foreach ( $map as $id => $related ) {
					if ( count( $related ) > 0 ) {
						$ids[] = $id;
					}
				}
			}
		}

		$this->count = count( $ids );
		$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );

		$this->timer->stop();
		$this->timer->log( 'verbose' );
	}

}
