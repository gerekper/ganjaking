<?php
/**
 * class-engine-stage-featured.php
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

class Engine_Stage_Featured extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_insignis';

	const CACHE_LIFETIME = Cache::MONTH;

	protected $stage_id = 'featured';

	private $featured = null;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'featured':
						if ( is_string( $value ) || is_numeric( $value ) ) {
							$value = sanitize_text_field( trim( '' . $value ) );
							$values = explode( ',', $value );
							$stati = array();
							foreach ( $values as $status ) {
								switch ( $status ) {
									case '1':
										$stati[] = 1;
										break;
									case '0':
										$stati[] = 0;
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
				'featured' => $this->featured,
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

		if ( $this->featured !== null && is_array( $this->featured ) && count( $this->featured ) > 0 ) {

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			if ( isset( $product_visibility_term_ids['featured'] ) ) {
				$query = '';

				if ( count( $this->featured ) === 1 ) {
					if ( in_array( 1, $this->featured ) ) {

						if ( !$this->variations ) {

							$query = $wpdb->prepare(
								"SELECT ID FROM $wpdb->posts " .
								"WHERE " .
								"post_type IN ('product', 'product_variation') " .
								"AND " .
								"ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d )",
								intval( $product_visibility_term_ids['featured'] )
							);
						} else {

							$query = $wpdb->prepare(
								"SELECT ID, post_parent FROM $wpdb->posts " .
								"WHERE " .
								"post_type IN ('product', 'product_variation') " .
								"AND ( " .
								"ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) " .
								"OR " .
								"post_parent IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) " .
								")",
								intval( $product_visibility_term_ids['featured'] ),
								intval( $product_visibility_term_ids['featured'] )
							);
						}
					} else if ( in_array( 0, $this->featured ) ) {

						if ( !$this->variations ) {
							$query = $wpdb->prepare(
								"SELECT ID FROM $wpdb->posts " .
								"WHERE " .
								"post_type IN ('product', 'product_variation') " .
								"AND " .
								"ID NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d )",
								intval( $product_visibility_term_ids['featured'] )
							);
						} else {
							$query = $wpdb->prepare(
								"SELECT ID, post_parent FROM $wpdb->posts " .
								"WHERE " .
								"post_type IN ('product', 'product_variation') " .
								"AND ( " .
								"NOT post_parent AND ID NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) " .
								"OR " .
								"post_parent AND post_parent NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) " .
								")",
								intval( $product_visibility_term_ids['featured'] ),
								intval( $product_visibility_term_ids['featured'] )
							);
						}
					}
				} else {

					if ( in_array( 0, $this->featured ) && in_array( 1, $this->featured ) ) {

						$query =
							"SELECT ID, post_parent FROM $wpdb->posts " .
							"WHERE " .
							"post_type IN ('product', 'product_variation')";
					}
				}
				if ( $query !== '' ) {
					if ( $this->limit !== null ) {
						$query .= ' LIMIT ' . intval( $this->limit );
					}
					$results = $wpdb->get_results( $query );
					if ( is_array( $results ) ) {
						foreach ( $results as $result ) {
							$is_variation = !empty( $result->post_parent );
							if ( $is_variation ) {
								$ids[] = (int) $result->post_parent;
							}
							if ( !$is_variation || $this->variations ) {
								$ids[] = (int) $result->ID;
							}
						}

						Tools::unique_int( $ids );
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
