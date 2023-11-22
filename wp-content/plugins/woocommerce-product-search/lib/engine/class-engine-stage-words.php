<?php
/**
 * class-engine-stage-words.php
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

class Engine_Stage_Words extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_verbum';

	const ATOMIC_CACHE_GROUP = 'ixwps_atomus';

	const CACHE_LIFETIME = Cache::DAY;

	protected $stage_id = 'words';

	protected $atomic_caching = true;

	protected $atomic_lifetime = Cache::DAY;

	private $q = '';

	private $title = true;

	private $excerpt = true;

	private $content = true;

	private $tags = true;

	private $sku = true;

	private $categories = true;

	private $attributes = true;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );

		$settings = Engine_Stage_Settings::get_instance();
		$stages = $settings->get();
		if ( !is_array( $stages ) ) {
			$stages = array();
		}
		if (
			array_key_exists( $this->stage_id, $stages ) &&
			is_array( $stages[$this->stage_id] )
		) {

			if (
				array_key_exists( 'atomic_lifetime', $stages[$this->stage_id] ) &&
				is_numeric( $stages[$this->stage_id]['atomic_lifetime'] )
			) {
				$this->atomic_lifetime = max( 0, intval( $stages[$this->stage_id]['atomic_lifetime'] ) );
			}

			if (
				array_key_exists( 'atomic_caching', $stages[$this->stage_id] ) &&
				is_bool( $stages[$this->stage_id]['atomic_caching'] )
			) {
				$this->atomic_caching = boolval( $stages[$this->stage_id]['atomic_caching'] );
			}
		}

		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'q':
						if ( !is_string( $value ) ) {
							$value = '';
						}
						break;
					case 'title':
					case 'excerpt':
					case 'content':
					case 'tags':
					case 'sku':
					case 'categories':
					case 'attributes':
						$value = boolval( $value );
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

			if ( !( $this->title || $this->excerpt || $this->content || $this->tags || $this->sku || $this->categories || $this->attributes ) ) {
				$this->title = true;
			}
		}
	}

	public function get_parameters() {
		return array_merge(
			array(
				'q'         => $this->q,
				'title'     => $this->title,
				'excerpt'   => $this->excerpt,
				'content'   => $this->content,
				'tags'      => $this->tags,
				'sku'       => $this->sku,
				'categories' => $this->categories,
				'attributes' => $this->attributes
			),
			parent::get_parameters()
		);
	}

	public function get_matching_ids( &$ids ) {

		global $wpdb;

		$this->timer->start();

		$search_query = $this->q;
		$search_query = apply_filters( 'woocommerce_product_search_request_search_query', $search_query );
		$record_search_query = \WooCommerce_Product_Search_Indexer::equalize( $search_query );
		$search_query = \WooCommerce_Product_Search_Indexer::normalize( $search_query );
		$search_query = trim( \WooCommerce_Product_Search_Indexer::remove_accents( $search_query ) );
		$search_terms = explode( ' ', $search_query );
		$search_terms = array_unique( $search_terms );

		$cache_context = $this->get_parameters();

		unset( $cache_context['q'] );
		$cache_context['search_query'] = $search_query;
		$cache_key = $this->get_cache_key( $cache_context );

		$cache = Cache::get_instance();
		$ids = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( is_array( $ids ) ) {

			$this->count = count( $ids );
			if ( $this->count === 1 ) {
				if ( in_array( \WooCommerce_Product_Search_Service::NAUGHT, $ids ) ) {
					$this->count = 0;
				}
			}
			$this->is_cache_hit = true;
			$this->timer->stop();
			$this->timer->log( 'verbose' );
			\WooCommerce_Product_Search_Service::maybe_record_hit( $record_search_query, $this->count );
			return;
		}
		$this->is_cache_hit = false;

		$options = get_option( 'woocommerce-product-search', null );

		$match_split = isset( $options[\WooCommerce_Product_Search_Service::MATCH_SPLIT] ) ? intval( $options[\WooCommerce_Product_Search_Service::MATCH_SPLIT] ) : \WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;

		$indexer = new \WooCommerce_Product_Search_Indexer();
		$object_type_ids = array();
		$variation_object_type_ids = array();
		if ( $this->title ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_title' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'product', 'posts', 'post_title' );
		}
		if ( $this->excerpt ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_excerpt' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'product', 'posts', 'post_excerpt' );
		}
		if ( $this->content ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_content' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'product', 'posts', 'post_content' );
		}
		if ( $this->sku ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'sku', 'postmeta', 'meta_key', '_sku' );
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_id' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'sku', 'postmeta', 'meta_key', '_sku' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'product', 'posts', 'post_id' );
		}
		if ( $this->tags ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'tag', 'term_taxonomy', 'taxonomy', 'product_tag' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'tag', 'term_taxonomy', 'taxonomy', 'product_tag' );
		}
		if ( $this->categories ) {
			$object_type_ids[] = $indexer->get_object_type_id( 'product', 'category', 'term_taxonomy', 'taxonomy', 'product_cat' );
			$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', 'category', 'term_taxonomy', 'taxonomy', 'product_cat' );
		}
		if ( $this->attributes ) {
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( !empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute ) {
					$object_type_ids[] = $indexer->get_object_type_id( 'product', $attribute->attribute_name, 'term_taxonomy', 'taxonomy', 'pa_' . $attribute->attribute_name );
					$variation_object_type_ids[] = $indexer->get_object_type_id( 'product_variation', $attribute->attribute_name, 'term_taxonomy', 'taxonomy', 'pa_' . $attribute->attribute_name );
				}
			}
		}

		unset( $indexer );

		$atoms = array();
		if ( count( $object_type_ids ) > 0 ) {
			sort( $object_type_ids );
			sort( $variation_object_type_ids );
			$like_prefix = apply_filters( 'woocommerce_product_search_like_within', false, $object_type_ids, $search_terms ) ? '%' : '';
			$key_table   = \WooCommerce_Product_Search_Controller::get_tablename( 'key' );
			$index_table = \WooCommerce_Product_Search_Controller::get_tablename( 'index' );
			foreach ( $search_terms as $search_term ) {
				$length = function_exists( 'mb_strlen' ) ? mb_strlen( $search_term ) : strlen( $search_term );

				if ( $length === 0 ) {
					continue;
				}

				if ( $length < $match_split ) {
					$query = $wpdb->prepare(
						"SELECT object_id, NULL AS post_parent FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` = %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $object_type_ids ) ) . " ) " .
						"UNION ALL " .
						"SELECT ID AS object_id, post_parent FROM $wpdb->posts WHERE ID IN ( SELECT object_id FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` = %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $variation_object_type_ids ) ) . ") )",
						$search_term,
						$search_term
					);
					if ( $this->limit !== null ) {
						$query .= ' LIMIT ' . intval( $this->limit );
					}
					$atoms[] = array(
						'atom'  => $search_term,
						'mode'  => 'equal',
						'query' => $query
					);
				} else {
					$like = $like_prefix . $wpdb->esc_like( $search_term ) . '%';

					$query = $wpdb->prepare(
						"SELECT object_id, NULL AS post_parent FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` LIKE %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $object_type_ids ) ) . " ) " .
						"UNION ALL " .
						"SELECT ID AS object_id, post_parent FROM $wpdb->posts WHERE ID IN ( SELECT object_id FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` LIKE %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $variation_object_type_ids ) ) . ") )",
						$like,
						$like
					);
					if ( $this->limit !== null ) {
						$query .= ' LIMIT ' . intval( $this->limit );
					}
					$atoms[] = array(
						'atom'  => $search_term,
						'mode'  => 'like',
						'query' => $query
					);
				}
			}
		}

		$ids = array();
		if ( !empty( $atoms ) ) {
			$matrix = new Matrix();
			foreach ( $atoms as $atom ) {
				$results = null;
				if ( $this->atomic_caching ) {
					$atomic_cache_context = $this->get_parameters();

					unset( $atomic_cache_context['q'] );
					$atomic_cache_context['atom'] = $atom['atom'];
					$atomic_cache_context['mode'] = $atom['mode'];
					$atomic_cache_key = $this->get_cache_key( $atomic_cache_context );
					$results = $cache->get( $atomic_cache_key, self::ATOMIC_CACHE_GROUP );
				}
				if ( !is_array( $results ) ) {
					$results = $wpdb->get_results( $atom['query'] );
					if ( $this->atomic_caching ) {
						if ( is_array( $results ) ) {
							$cache->set( $atomic_cache_key, $results, self::ATOMIC_CACHE_GROUP, $this->atomic_lifetime );
						}
					}
				}
				if ( is_array( $results ) ) {
					$matrix->inc_stage();
					foreach ( $results as $result ) {
						$is_variation = !empty( $result->post_parent );
						if ( $is_variation ) {
							$matrix->inc( (int) $result->post_parent );
						}
						if ( !$is_variation || $this->variations ) {
							$matrix->inc( (int) $result->object_id );
						}
					}

					$matrix->evaluate();
				}
			}

			$ids = $matrix->get_ids();
		}

		$this->count = count( $ids );
		$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );

		$extra = count( $search_terms ) > 0 ? implode( ' ', $search_terms ) : null;

		$this->timer->stop();
		$this->timer->log( 'verbose', $extra );

		\WooCommerce_Product_Search_Service::maybe_record_hit( $record_search_query, $this->count );
	}
}
