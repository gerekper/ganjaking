<?php
/**
 * class-engine-stage-visibility.php
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

class Engine_Stage_Visibility extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_visio';

	const CACHE_LIFETIME = Cache::UNLIMITED;

	protected $stage_id = 'visibility';

	private $visibility = null;

	/**
	 * Caller is responsible to determine whether current user is eligible to view based on visibility parameter.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'visibility':
						if ( is_string( $value ) ) {
							$value = sanitize_text_field( trim( $value ) );
							$value = array_unique( array_map( 'trim', explode( ',', $value ) ) );
						}
						if ( is_array( $value ) ) {
							$stati = array();
							foreach ( $value as $status ) {
								switch ( $status ) {

									case 'visible':
									case 'catalog':
									case 'search':
									case 'hidden':
									case 'exclude-from-search':
									case 'exclude-from-catalog':
										$stati[] = $status;
										break;
								}
							}
							if ( count( $stati ) > 0 ) {
								$value = $stati;
							} else {
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
				'visibility' => $this->visibility
			),
			parent::get_parameters()
		);
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

		$ids = array();

		if ( $this->visibility !== null && is_array( $this->visibility ) && count( $this->visibility ) > 0 ) {
			$operator = 'NOT IN';
			$terms = array();
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			foreach ( $this->visibility as $visibility ) {
				switch ( $visibility ) {
					case 'visible':

						$terms[] = 'exclude-from-search';
						$terms[] = 'exclude-from-catalog';
						break;
					case 'catalog':

						$terms[] = 'exclude-from-catalog';
						break;
					case 'search':

						$terms[] = 'exclude-from-search';
						break;
					case 'hidden':

						$operator = 'AND';
						$terms[] = 'exclude-from-search';
						$terms[] = 'exclude-from-catalog';
						break;
					case 'exclude-from-search':

						$operator = 'IN';
						$terms[] = 'exclude-from-search';
						break;
					case 'exclude-from-catalog':

						$operator = 'IN';
						$terms[] = 'exclude-from-catalog';
						break;
				}
			}
			$terms = array_unique( $terms );
			$visibility_term_ids = array();
			foreach ( $terms as $term ) {
				if ( isset( $product_visibility_term_ids[$term] ) ) {
					$visibility_term_ids[] = $product_visibility_term_ids[$term];
				}
			}

			$query = '';
			if ( count( $visibility_term_ids ) > 0 ) {
				switch ( $operator ) {
					case 'AND':
						$conditions = array(
							"post_type = 'product'"
						);
						if ( $this->variations ) {
							$v_conditions = array(
								"post_type = 'product_variation'"
							);
						}
						foreach ( $visibility_term_ids as $visibility_term_id ) {
							$conditions[] = $wpdb->prepare(
								"ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d )",
								intval( $visibility_term_id )
							);
							if ( $this->variations ) {
								$v_conditions[] = $wpdb->prepare(
									"post_parent IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d )",
									intval( $visibility_term_id )
								);
							}
						}
						$query = $wpdb->prepare(
							"SELECT ID FROM $wpdb->posts " .
							"WHERE " .
							implode( ' AND ', $conditions )
						);
						if ( $this->variations ) {
							$query .=
								" " .
								"UNION ALL " .
								"SELECT ID FROM $wpdb->posts " .
								"WHERE " .
								implode( ' AND ', $v_conditions );
						}
						break;
					case 'IN':
						$query =
							"SELECT ID FROM $wpdb->posts " .
							"WHERE " .
							"post_type = 'product' " .
							"AND " .
							"ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " .
							implode( ',', array_map( 'intval', $visibility_term_ids ) ) .
							" ) )";
						if ( $this->variations ) {
							$query .=
								" " .
								"UNION ALL " .
								"SELECT ID FROM $wpdb->posts " .
								"WHERE " .
								"post_type = 'product_variation' " .
								"AND " .
								"post_parent IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " .
								implode( ',', array_map( 'intval', $visibility_term_ids ) ) .
								" ) )";
						}
						break;
					case 'NOT IN':
						$query =
							"SELECT ID FROM $wpdb->posts " .
							"WHERE " .
							"post_type = 'product' " .
							"AND " .
							"ID NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " .
							implode( ',', array_map( 'intval', $visibility_term_ids ) ) .
							" ) )";
						if ( $this->variations ) {
							$query .=
								" " .
								"UNION ALL " .
								"SELECT ID FROM $wpdb->posts " .
								"WHERE " .
								"post_type = 'product_variation' " .
								"AND " .
								"post_parent NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " .
								implode( ',', array_map( 'intval', $visibility_term_ids ) ) .
								" ) )";
						}
						break;
				}
				if ( $query !== '' ) {
					if ( $this->limit !== null ) {
						$query .= ' LIMIT ' . intval( $this->limit );
					}
					$results = $wpdb->get_results( $query );
					if ( is_array( $results ) ) {
						foreach ( $results as $result ) {
							$ids[] = (int) $result->ID;
						}
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
