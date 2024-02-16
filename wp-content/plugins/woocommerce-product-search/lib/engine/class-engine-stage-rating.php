<?php
/**
 * class-engine-stage-rating.php
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

class Engine_Stage_Rating extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_taxatio';

	const CACHE_LIFETIME = Cache::DAY;

	const DELTA = 0.5;

	protected $stage_id = 'rating';

	private $delta = self::DELTA;

	private $rating = null;

	private $min_rating = null;

	private $max_rating = null;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'delta':
					case 'rating':
					case 'min_rating':
					case 'max_rating':
						if ( is_string( $value ) || is_numeric( $value ) ) {
							$value = sanitize_text_field( trim( '' . $value ) );
							if ( strlen( $value ) === 0 ) {
								$value = null;
							} else {
								$value = $this->to_float( $value );
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

		if ( $this->delta !== null && $this->delta < 0 ) {
			$this->delta = self::DELTA;
		}
		if ( $this->rating !== null && $this->rating <= 0 ) {
			$this->rating = null;
		}
		if ( $this->rating !== null ) {
			$this->min_rating = $this->rating;
		}
		if ( $this->min_rating !== null && $this->min_rating <= 0 ) {
			$this->min_rating = null;
		}
		if ( $this->max_rating !== null && $this->max_rating <= 0 ) {
			$this->max_rating = null;
		}
		if ( $this->min_rating !== null && $this->max_rating !== null && $this->max_rating < $this->min_rating ) {
			$this->max_rating = null;
		}
	}

	public function get_parameters() {
		return array_merge(
			array(
				'delta'      => $this->delta,
				'rating'     => $this->rating,
				'min_rating' => $this->min_rating,
				'max_rating' => $this->max_rating
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

		$query = null;

		if ( $this->min_rating !== null && $this->max_rating === null ) {
			if ( !$this->variations ) {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s ",
					esc_sql( floor( $this->min_rating ) - $this->delta )
				);
			} else {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s " .
					"UNION ALL " .
					"SELECT ID AS product_id FROM {$wpdb->posts} WHERE post_type = 'product_variation' AND post_parent IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s ) "
					,
					esc_sql( floor( $this->min_rating ) - $this->delta ),
					esc_sql( floor( $this->min_rating ) - $this->delta )
				);
			}
		} else if ( $this->min_rating === null && $this->max_rating !== null ) {
			if ( !$this->variations ) {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating <= %s ",
					esc_sql( ceil( $this->max_rating ) + $this->delta )
				);
			} else {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating <= %s " .
					"UNION ALL " .
					"SELECT ID AS product_id FROM {$wpdb->posts} WHERE post_type = 'product_variation' AND post_parent IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating <= %s ) "
					,
					esc_sql( ceil( $this->max_rating ) + $this->delta ),
					esc_sql( ceil( $this->max_rating ) + $this->delta )
				);
			}
		} else if ( $this->min_rating !== null && $this->max_rating !== null ) {
			if ( !$this->variations ) {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s AND wc_product_meta_lookup.average_rating <= %s ",
					esc_sql( floor( $this->min_rating ) - $this->delta ),
					esc_sql( ceil( $this->max_rating ) + $this->delta )
				);
			} else {
				$query = sprintf(
					"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s AND wc_product_meta_lookup.average_rating <= %s " .
					"UNION ALL " .
					"SELECT ID AS product_id FROM {$wpdb->posts} WHERE post_type = 'product_variation' AND post_parent IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.rating_count > 0 AND wc_product_meta_lookup.average_rating >= %s AND wc_product_meta_lookup.average_rating <= %s ) "
					,
					esc_sql( floor( $this->min_rating ) - $this->delta ),
					esc_sql( ceil( $this->max_rating ) + $this->delta ),
					esc_sql( floor( $this->min_rating ) - $this->delta ),
					esc_sql( ceil( $this->max_rating ) + $this->delta )
				);
			}
		}

		if ( $query !== null ) {
			if ( $this->limit !== null ) {
				$query .= ' LIMIT ' . intval( $this->limit );
			}

			$results = $wpdb->get_results( $query );
			if ( is_array( $results ) ) {
				foreach ( $results as $result ) {

					$ids[] = (int) $result->product_id;
				}

				if ( $this->variations ) {
					Tools::unique( $ids );
				}
			}
		}

		$this->count = count( $ids );
		$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );

		$this->timer->stop();
		$this->timer->log( 'verbose' );
	}

	/**
	 * Float conversion.
	 *
	 * @param string|float|null $x to convert
	 *
	 * @return float|null converted or null
	 */
	public function to_float( $x ) {

		if ( $x !== null && !is_float( $x ) && is_string( $x ) ) {
			$locale = localeconv();

			$decimal_characters = array_unique( array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], '.', ',' ) );
			$x = str_replace( $decimal_characters, '.', trim( $x ) );
			$x = preg_replace( '/[^0-9\.,-]/', '', $x );
			$i = strrpos( $x, '.' );
			if ( $i !== false ) {
				$x = ( $i > 0 ? str_replace( '.', '', substr( $x, 0, $i ) ) : '' ) . '.' . ( $i < strlen( $x ) ? str_replace( '.', '', substr( $x, $i + 1 ) ) : '' );
			}
			if ( strlen( $x ) > 0 ) {
				$x = floatval( $x );
			} else {
				$x = null;
			}
		}
		return $x;
	}

}
