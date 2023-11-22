<?php
/**
 * class-engine-stage-terms.php
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

class Engine_Stage_Terms extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_terminus';

	const CACHE_LIFETIME = Cache::YEAR;

	protected $stage_id = 'terms';

	private $taxonomy = null;

	private $terms = null;

	private $op = 'or';

	private $id_by = 'slug';

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'taxonomy':
						if ( is_string( $value ) ) {
							$value = sanitize_text_field( trim( $value ) );
							$value = preg_replace( '/[^a-zA-Z0-9_\-]/', '', $value );
							if ( strlen( $value ) === 0 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					case 'terms':
						if ( is_array( $value ) ) {
							$terms = array();
							foreach ( $value as $term ) {
								if ( is_string( $term ) || is_numeric( $term ) ) {
									$terms[] = trim( sanitize_text_field( '' . $term ) );
								}
							}
							if ( count( $terms ) > 0 ) {
								$value = array_unique( $terms );
							} else {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					case 'op':
						if ( !is_string( $value ) ) {
							$value = $this->op;
						}
						switch ( $value ) {
							case 'and':
							case 'or':
								break;
							default:
								$value = $this->op;
						}
						break;
					case 'id_by':
						if ( !is_string( $value ) ) {
							$value = $this->id_by;
						}
						switch ( $value ) {
							case 'id':
							case 'slug':
								break;
							default:
								$value = $this->id_by;
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
				'taxonomy' => $this->taxonomy,
				'terms'    => $this->terms,
				'op'       => $this->op,
				'id_by'    => $this->id_by
			),
			parent::get_parameters()
		);
	}

	protected function get_cache_group() {

		$group = self::CACHE_GROUP;
		if ( $this->taxonomy !== null ) {
			$group .= '_' . $this->taxonomy;
		}
		return $group;
	}

	public function get_matching_ids( &$ids ) {

		global $wpdb;

		$this->timer->start();

		$cache_context = $this->get_parameters();
		$cache_key = $this->get_cache_key( $cache_context );

		$cache = Cache::get_instance();
		$ids = $cache->get( $cache_key, $this->get_cache_group() );

		if ( is_array( $ids ) ) {
			$this->count = count( $ids );
			$this->is_cache_hit = true;
			$this->timer->stop();
			$this->timer->log( 'verbose' );
			return;
		}
		$this->is_cache_hit = false;

		$ids = array();

		$term_ids = array();
		if ( $this->terms !== null && count( $this->terms ) > 0 ) {
			foreach ( $this->terms as $term ) {
				switch ( $this->id_by ) {
					case 'slug':
						$term = get_term_by( 'slug', $term, $this->taxonomy );
						break;
					case 'id':
						$term = get_term_by( 'id', $term, $this->taxonomy );
						break;
					default:
						$term = null;
				}

				if ( $term instanceof \WP_Term ) {
					$term_ids[] = intval( $term->term_id );
					if ( is_taxonomy_hierarchical( $term->taxonomy ) ) {
						$child_term_ids = get_term_children( $term->term_id, $term->taxonomy );
						if ( is_array( $child_term_ids ) ) {
							Tools::int( $child_term_ids );
							$term_ids = array_merge( $term_ids, $child_term_ids );
						}
					}
				}
			}
		}

		Tools::unique( $term_ids );

		if ( count( $term_ids ) > 0 ) {

			$object_term_table = \WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

			if ( $this->op === 'or' ) {

				$query =
					"SELECT object_id FROM $object_term_table " .
					'WHERE ' .
					'term_id IN ( ' . implode( ',', $term_ids ) . ' ) ' .
					'AND ( ' .
					( $this->variations ?
						"object_type NOT IN ( 'variable', 'variable-subscription' ) OR " :
						"object_type NOT IN ( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) OR "
					) .
					"object_type IN ( 'variable', 'variable-subscription' ) AND inherit = 1 " .
					') ' .
					'UNION ALL ' .
					"SELECT parent_object_id AS object_id FROM $object_term_table " .
					'WHERE ' .
					'term_id IN ( ' . implode( ',', $term_ids ) . ' ) ' .
					'AND ' .
					"object_type IN ( 'variation', 'subscription_variation' )";
			} else {

				$count = count( $term_ids );
				$query =
					'SELECT DISTINCT object_id FROM ' .
					'( ' .
						"SELECT object_id, term_id FROM $object_term_table " .
						'WHERE ' .
						'term_id IN ( ' . implode( ',', $term_ids ) . ' ) ' .
						'AND ( ' .
						( $this->variations ?
							"object_type NOT IN ( 'variable', 'variable-subscription' ) OR " :
							"object_type NOT IN ( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) OR "
						) .
						"object_type IN ( 'variable', 'variable-subscription' ) AND inherit = 1 " .
						') ' .
						'UNION ALL ' .
						"SELECT parent_object_id AS object_id, term_id FROM $object_term_table " .
						'WHERE ' .
						'term_id IN ( ' . implode( ',', $term_ids ) . ' ) ' .
						'AND ' .
						"object_type IN ( 'variation', 'subscription_variation' ) " .
					') tmp ' .
					'GROUP BY object_id ' .
					'HAVING COUNT(DISTINCT term_id) = ' . $count;
			}

			if ( $this->limit !== null ) {
				$query .= ' LIMIT ' . intval( $this->limit );
			}

			$results = $wpdb->get_results( $query );
			if ( is_array( $results ) ) {
				foreach ( $results as $result ) {
					$ids[] = (int) $result->object_id;
				}

				Tools::unique( $ids );
			}
		}

		$this->count = count( $ids );
		$this->is_cache_write = $cache->set( $cache_key, $ids, $this->get_cache_group(), $this->get_cache_lifetime() );

		$this->timer->stop();
		$this->timer->log( 'verbose' );
	}
}
