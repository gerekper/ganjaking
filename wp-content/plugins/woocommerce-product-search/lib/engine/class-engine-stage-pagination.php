<?php
/**
 * class-engine-stage-pagination.php
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

class Engine_Stage_Pagination extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_numerus';

	const CACHE_LIFETIME = Cache::MINUTE;

	const PAGE_DEFAULT = 1;

	const PER_PAGE_DEFAULT = 10;

	protected $stage_id = 'pagination';

	protected $caching = false;

	private $page = self::PAGE_DEFAULT;

	private $per_page = self::PER_PAGE_DEFAULT;

	private $offset = null;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'page':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value < 1 ) {
								$value = self::PAGE_DEFAULT;
							}
						} else {
							$value = null;
						}
						break;
					case 'per_page':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value <= 0 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					case 'offset':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value < 0 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
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

		return array_merge(
			array(
				'page'     => $this->page,
				'per_page' => $this->per_page,
				'offset'   => $this->offset

			),
			parent::get_parameters()
		);
	}

	/**
	 * {@inheritDoc}
	 * @see \com\itthinx\woocommerce\search\engine\Engine_Stage::get_cache_context()
	 */
	protected function get_cache_context() {

		$cache_context = $this->get_parameters();
		if ( $this->engine !== null ) {
			$cache_context['context'] = json_encode( $this->engine->get_parameters() );
		}
		$cache_context = apply_filters( 'woocommerce_product_search_engine_stage_cache_context', $cache_context, $this );
		return $cache_context;
	}

	/**
	 * {@inheritDoc}
	 * @see \com\itthinx\woocommerce\search\engine\Engine_Stage::get_matching_ids()
	 */
	public function get_matching_ids( &$ids ) {

		$this->timer->start();

		if ( $this->engine !== null ) {

			if ( $this->caching ) {
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
			}

			$ids = $this->engine->get_matrix()->get_ids();

			if ( $this->offset !== null ) {
				$ids = array_slice( $ids, $this->offset, $this->limit );
			}

			if ( $this->per_page !== null ) {
				$offset = ( $this->page - 1 ) * $this->per_page;
				$ids = array_slice( $ids, $offset, $this->per_page );
			}

			$this->count = count( $ids );
			if ( $this->caching ) {
				$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );
			}
		}

		$this->timer->stop();
		$this->timer->log( 'verbose' );
	}

}
