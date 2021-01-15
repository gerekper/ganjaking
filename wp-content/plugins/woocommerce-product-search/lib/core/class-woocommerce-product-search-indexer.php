<?php
/**
 * class-woocommerce-product-search-indexer.php
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
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Indexer.
 */
class WooCommerce_Product_Search_Indexer {

	/**
	 * Lock filename.
	 * @var string
	 */
	const LOCK = '.wps_indexer';

	const BASE_DELTA = 1048576;
	const DELTA_F    = 1.2;

	const INDEX_PER_CYCLE         = 'index-per-cycle';
	const INDEX_PER_CYCLE_DEFAULT = 1000;

	const INDEX_ORDER                         = 'index-order';
	const INDEX_ORDER_MOST_RECENTLY_MODIFIED  = 'modified-desc';
	const INDEX_ORDER_LEAST_RECENTLY_MODIFIED = 'modified-asc';
	const INDEX_ORDER_MOST_RECENT             = 'id-desc';
	const INDEX_ORDER_LEAST_RECENT            = 'id-asc';
	const INDEX_ORDER_DEFAULT                 = self::INDEX_ORDER_MOST_RECENT;

	const GC_CYCLE = 3600;

	const CACHE_GROUP = 'ixwpsidx';

	private $limit = self::INDEX_PER_CYCLE_DEFAULT;
	private $check_memory_limit = true;
	private $check_execution_limit = true;

	/**
	 * @var int File pointer for the lockfile.
	 */
	private $h = null;

	private $raw = false;

	/**
	 * Initialize.
	 */
	public static function init() {
	}

	/**
	 * Checks and returns true if the lockfile exists.
	 *
	 * @return boolean true if the lockfile exists
	 */
	private static function check_lock() {
		$exists = false;
		if ( !file_exists( WOO_PS_CORE_LIB . '/' . self::LOCK ) ) {
			if ( $h = @fopen( WOO_PS_CORE_LIB . '/' . self::LOCK, 'w' ) ) {
				@fclose( $h );
				$exists = true;
			} else {
				wps_log_warning( 'The indexer could not create the lockfile.' );
			}
		} else {
			$exists = true;
		}
		return $exists;
	}

	/**
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters set of parameters for which to compute the key
	 *
	 * @return string
	 */
	private static function get_cache_key( $parameters ) {
		return md5( implode( '-', $parameters ) );
	}

	/**
	 * Acquire the lock; also acquires if the lockfile is not available.
	 *
	 * @return boolean true if the lock was succesfully acquired or the lockfile is not available
	 */
	public function acquire() {
		$acquired = true;
		if ( self::check_lock() ) {
			if ( $this->h = @fopen( WOO_PS_CORE_LIB . '/' . self::LOCK, 'r+' ) ) {
				if ( !flock( $this->h, LOCK_EX | LOCK_NB ) ) {
					$acquired = false;
				}
			} else {
				wps_log_warning( 'The indexer could not open the lockfile.' );
			}
		}
		if ( $acquired ) {
			wps_log_info( 'The indexer has acquired the lock.' );
		}
		return $acquired;
	}

	/**
	 * Release the lock on the bucket.
	 * @return boolean true if the lock on the bucket could be released, false on failure
	 */
	public function release() {
		$released = false;
		if ( $this->h !== null ) {
			if ( self::check_lock() ) {
				if ( flock( $this->h, LOCK_UN ) ) {
					$released = true;
				}
				@fclose( $this->h );
				$this->h = null;
			}
		}
		if ( $released ) {
			wps_log_info( 'The indexer has released the lock.' );
		}
		return $released;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$options = get_option( 'woocommerce-product-search', array() );
		$this->limit = intval( isset( $options[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) ? $options[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] : WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT );
		$this->limit = apply_filters( 'woocommerce_product_search_indexer_limit', $this->limit );
		if ( $this->limit <= 0 ) {
			$this->limit = self::INDEX_PER_CYCLE_DEFAULT;
		}
		$this->check_memory_limit = apply_filters( 'woocommerce_product_search_indexer_check_memory_limit', $this->check_memory_limit ) !== false;
		$this->check_execution_limit = apply_filters( 'woocommerce_product_search_indexer_check_execution_limit', $this->check_execution_limit ) !== false;
		register_shutdown_function( array( $this, 'release' ) );
	}

	/**
	 * Product terms to process.
	 *
	 * @since 3.0.0
	 *
	 * @return int[]
	 */
	public function get_processable_term_ids() {
		global $wpdb;
		$term_ids = array();
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			$product_taxonomies = self::get_applicable_product_taxonomies();
			if ( count( $product_taxonomies ) > 0 ) {
				$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
				$query = "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy IN ( '" . implode( "','", esc_sql( $product_taxonomies ) ) . "' ) AND term_id NOT IN (SELECT DISTINCT term_id FROM $object_term_table WHERE object_id = 0)";
				$term_ids = $wpdb->get_col( $query );
				if ( is_array( $term_ids ) ) {
					$term_ids = array_map( 'intval', array_unique( $term_ids ) );
				}
			}
		}
		return $term_ids;
	}

	/**
	 * Product taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	public static function get_applicable_product_taxonomies() {
		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );

		$product_taxonomies = array_unique( $product_taxonomies );
		return $product_taxonomies;
	}

	/**
	 * Preprocess terms for indexing.
	 *
	 * @since 3.0.0
	 */
	public function preprocess_terms() {

		global $wpdb;
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
			$term_ids = $this->get_processable_term_ids();
			if ( count( $term_ids ) > 0 ) {
				$max = WooCommerce_Product_Search_Controller::get_max_allowed_packet();

				$tuples = array();
				$values = array();
				foreach ( $term_ids as $term_id ) {

					$size = strlen(
						"INSERT INTO $object_term_table " .
						"( object_id, term_id, modified ) " .
						"VALUES " . implode( ',', $tuples ) . implode( ' ', $values )
					);
					if ( $size > 0.9 * $max ) {
						break;
					}

					$tuples[] = '( 0, %d, %s )';
					$values[] = intval( $term_id );
					$values[] = gmdate( 'Y-m-d H:i:s' );
				}
				$wpdb->query( $wpdb->prepare(
					"INSERT INTO $object_term_table " .
					"( object_id, term_id, modified ) " .
					"VALUES " . implode( ',', $tuples ),
					$values
				) );
			}
		}
	}

	/**
	 * Process term weights.
	 *
	 * @since 3.0.0
	 *
	 * @param int[] $term_ids process all if not provided, otherwise those and their children
	 * @param string $taxonomy ignored
	 */
	public function process_term_weights( $term_ids = null, $taxonomy = null ) {
		global $wpdb;

		$taxonomy = 'product_cat';
		$terms = array();
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$query =
				"SELECT tt.term_id AS term_id, tt.parent AS parent, tm.meta_value AS weight " .
				"FROM $wpdb->term_taxonomy tt " .
				"LEFT JOIN $wpdb->termmeta tm " .
				"ON tm.term_id = tt.term_id AND tm.meta_key = '_search_weight' " .
				"WHERE taxonomy = %s ";
			if ( $term_ids !== null && is_array( $term_ids ) && count( $term_ids ) > 0 ) {
				$term_ids = array_unique( array_map( 'intval', $term_ids ) );
				$the_term_ids = $term_ids;
				foreach ( $term_ids as $term_id ) {

					$parent_term_ids = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
					if ( is_array( $parent_term_ids ) ) {
						$parent_term_ids = array_map( 'intval', $parent_term_ids );
						$the_term_ids = array_merge( $the_term_ids, $parent_term_ids );
					}

					$child_term_ids = get_term_children( $term_id, $taxonomy );
					if ( is_array( $child_term_ids ) ) {
						$child_term_ids = array_map( 'intval', $child_term_ids );
						$the_term_ids = array_merge( $the_term_ids, $child_term_ids );
					}
				}
				$term_ids = array_unique( array_map( 'intval', $the_term_ids ) );
				$query .= 'AND tt.term_id IN ( ' . implode( ',', $term_ids ) . ' ) ';
			} else {

				delete_metadata( 'term', null, '_search_weight_sum', null, true );
			}

			$entries = $wpdb->get_results( $wpdb->prepare(
				$query,
				$taxonomy
			) );

			if ( is_array( $entries ) ) {

				foreach ( $entries as $entry ) {
					$term_id = intval( $entry->term_id );
					$parent_id = null;
					$weight = 0;
					if ( !empty( $entry->parent ) ) {
						$parent = intval( $entry->parent );
						if ( $parent > 0 ) {
							$parent_id = $parent;
						}
					}
					if ( !empty( $entry->weight ) ) {
						$weight = intval( $entry->weight );
					}
					$terms[$term_id] = array(
						'parent_id' => $parent_id,
						'weight' => $weight,
						'weight_sum' => $weight,
						'children' => array()
					);
				}

				foreach ( $terms as $term_id => $term ) {
					if ( isset( $term['parent_id'] ) && $term['parent_id'] !== null ) {
						$terms[$term['parent_id']]['children'][] = $term_id;
					}
				}

				foreach ( $terms as $term_id => $term ) {
					if ( isset( $term['weight'] ) && $term['weight'] !== 0 ) {
						foreach ( $term['children'] as $child_id ) {
							$terms[$child_id]['weight_sum'] += $term['weight'];
						}
					}
				}

				foreach ( $terms as $term_id => $term ) {
					if ( isset( $term['weight_sum'] ) && $term['weight_sum'] !== 0 ) {
						update_term_meta( $term_id, '_search_weight_sum', $term['weight_sum'] );
					} else {
						delete_term_meta( $term_id, '_search_weight_sum' );
					}
				}
			}
		}
	}

	/**
	 * Counts terms not yet processed.
	 *
	 * @return int unprocessed terms
	 */
	public function get_processable_terms_count() {

		global $wpdb;
		$count = 0;
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			$product_taxonomies = self::get_applicable_product_taxonomies();
			if ( count( $product_taxonomies ) > 0 ) {
				$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
				$query = "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy IN ( '" . implode( "','", esc_sql( $product_taxonomies ) ) . "' ) AND term_id NOT IN (SELECT DISTINCT term_id FROM $object_term_table )";
				$count = $wpdb->get_var( $query );
				if ( $count !== null ) {
					$count = intval( $count );
				}
			}
		}
		return $count;
	}

	/**
	 * Counts products not yet processed.
	 *
	 * @return int unprocessed products
	 */
	public function get_processable_count() {
		global $wpdb;

		$result            = 0;
		$key_table         = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
		$index_table       = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
		$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );
		$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

		if (
			WooCommerce_Product_Search_Controller::table_exists( 'key' ) &&
			WooCommerce_Product_Search_Controller::table_exists( 'index' ) &&
			WooCommerce_Product_Search_Controller::table_exists( 'object_type' )
		) {

			$object_term_query = '';
			if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {

				$object_term_query = "OR ID NOT IN (SELECT object_id FROM $object_term_table WHERE term_id = 0) ";
			}

			$object_types = array( 'product', 'product_variation' );
			foreach ( $object_types as $object_type ) {
				$object_type_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT object_type_id FROM $object_type_table WHERE object_type = %s AND context_table = 'posts' AND context_column IS NULL AND context_key IS NULL",
					$object_type
				) );
				$count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(DISTINCT ID) FROM $wpdb->posts " .
					"WHERE " .
					"post_type = %s " .
					"AND post_status IN ( 'publish', 'pending', 'draft', 'private' ) " .
					"AND ( " .
					"ID NOT IN (SELECT object_id FROM $index_table WHERE object_type_id = %d) " .
					$object_term_query .
					" ) ",
					$object_type,
					intval( $object_type_id )
				) );
				if ( $count !== null ) {
					$result += intval( $count );
				}
			}
		}

		return $result;
	}

	public function get_processable_ids() {
		global $wpdb;

		$post_ids          = array();
		$key_table         = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
		$index_table       = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
		$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );
		$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

		$options = get_option( 'woocommerce-product-search', null );

		$index_order_by  = 'ID';
		$index_order_dir = 'DESC';
		$index_order     = isset( $options[self::INDEX_ORDER] ) ? $options[self::INDEX_ORDER] : self::INDEX_ORDER_DEFAULT;
		switch( $index_order ) {
			case self::INDEX_ORDER_MOST_RECENT :
				$index_order_by  = 'ID';
				$index_order_dir = 'DESC';
				break;
			case self::INDEX_ORDER_LEAST_RECENT :
				$index_order_by  = 'ID';
				$index_order_dir = 'ASC';
				break;
			case self::INDEX_ORDER_MOST_RECENTLY_MODIFIED :
				$index_order_by  = 'post_modified';
				$index_order_dir = 'DESC';
				break;
			case self::INDEX_ORDER_LEAST_RECENTLY_MODIFIED :
				$index_order_by  = 'post_modified';
				$index_order_dir = 'ASC';
				break;
		}

		if (
			WooCommerce_Product_Search_Controller::table_exists( 'key' ) &&
			WooCommerce_Product_Search_Controller::table_exists( 'index' ) &&
			WooCommerce_Product_Search_Controller::table_exists( 'object_type' )
		) {

			$object_term_query = '';
			if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {

				$object_term_query = "OR ID NOT IN (SELECT object_id FROM $object_term_table WHERE term_id = 0) ";
			}

			$object_types = array( 'product', 'product_variation' );
			foreach ( $object_types as $object_type ) {
				$object_type_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT object_type_id FROM $object_type_table WHERE object_type = %s AND context_table = 'posts' AND context_column IS NULL AND context_key IS NULL",
					$object_type
				) );
				$_post_ids = $wpdb->get_col( $wpdb->prepare (
					"SELECT ID FROM $wpdb->posts WHERE " .
					"post_type = %s " .
					"AND post_status IN ( 'publish', 'pending', 'draft', 'private' ) " .
					"AND ( " .
					"ID NOT IN (SELECT object_id FROM $index_table WHERE object_type_id = %d) " .
					$object_term_query .
					" ) " .
					"ORDER BY $index_order_by $index_order_dir " .
					"LIMIT %d",
					$object_type,
					intval( $object_type_id ),
					intval( $this->limit )
				) );
				if ( is_array( $_post_ids ) && count( $_post_ids ) > 0 ) {
					$post_ids = array_merge( $post_ids, $_post_ids );
					$post_ids = array_unique( $post_ids );
					if ( count( $post_ids ) > $this->limit ) {
						$post_ids = array_slice( $post_ids, 0, $this->limit );
					}
				}
			}
		}

		return $post_ids;
	}

	/**
	 * Counts products than can be indexed.
	 *
	 * @return int indexable products
	 */
	public function get_total_count() {
		global $wpdb;
		$result = 0;
		$count = $wpdb->get_var(
			"SELECT COUNT(DISTINCT ID) FROM $wpdb->posts " .
			"WHERE " .
			"post_type IN ( 'product', 'product_variation' ) " .
			"AND post_status IN ( 'publish', 'pending', 'draft', 'private' ) "
		);
		if ( $count !== null ) {
			$result = intval( $count );
		}
		return $result;
	}

	public function work() {

		global $wpdb;

		if ( self::get_processable_count() === 0 ) {
			return;
		}

		if ( !$this->acquire() ) {
			return;
		}

		if ( $this->check_memory_limit ) {
			$bytes = memory_get_peak_usage( true );
			$memory_limit = ini_get( 'memory_limit' );
			$matches = null;
			preg_match( '/([0-9]+)(.)/', $memory_limit, $matches );
			if ( isset( $matches[2] ) ) {
				$exp = array( 'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6 );
				if ( key_exists( $matches[2], $exp ) ) {
					$memory_limit = intval( preg_replace( '/[^0-9]/', '', $memory_limit ) ) * pow( 1024, $exp[$matches[2]] );
				}
			}
		}

		if ( $this->check_execution_limit ) {
			$max_execution_time = intval( ini_get( 'max_execution_time' ) );

			if ( $max_execution_time === 0 ) {
				$max_execution_time = PHP_INT_MAX;
			}
			$max_input_time = ini_get( 'max_input_time' );
			if ( $max_input_time !== false ) {
				$max_input_time = intval( $max_input_time );
				switch ( $max_input_time ) {
					case -1 :

						break;
					case 0 :

						$max_execution_time = min( $max_execution_time, PHP_INT_MAX );
						break;
					default :

						$max_execution_time = min( $max_execution_time, $max_input_time );
				}
			}

			if ( function_exists( 'getrusage' ) ) {
				$resource_usage = getrusage();
				if ( isset( $resource_usage['ru_utime.tv_sec'] ) ) {
					$initial_execution_time = $resource_usage['ru_stime.tv_sec'] + $resource_usage['ru_utime.tv_sec'] + 2;
				}
			}
		}

		$this->preprocess_terms();

		$post_ids = self::get_processable_ids();
		$n = count( $post_ids );

		$first = is_array( $post_ids ) && isset( $post_ids[0] ) ? $post_ids[0] : '-';
		$last  = is_array( $post_ids ) && ( $n > 0 ) ? $post_ids[$n - 1] : '-';
		wps_log_info( sprintf( 'The indexer has found %d entries to process, %s - %s.', $n, $first, $last ) );

		$i = 0;
		$stop = false;
		foreach( $post_ids as $post_id ) {

			if ( !WooCommerce_Product_Search_Worker::get_status() ) {
				break;
			}

			$this->index( $post_id );

			if ( $this->check_memory_limit ) {
				if ( is_numeric( $memory_limit ) ) {
					$old_bytes = $bytes;
					$bytes     = memory_get_peak_usage( true );
					$remaining = $memory_limit - $bytes;
					$delta = self::BASE_DELTA;
					if ( $bytes > $old_bytes ) {
						$delta += intval( ( $bytes - $old_bytes ) * self::DELTA_F );
					}
					if ( $remaining < $delta ) {
						wps_log_info(
							'WooCommerce Product Search - ' .
							esc_html__( 'Info', 'woocommerce-product-search' ) .
							' : ' .
							sprintf(
								esc_html__( 'Stopped on iteration %d to avoid PHP memory issues.', 'woocommerce-product-search' ),
								esc_html( $i )
							)
						);
						$stop = true;
					}
				}
			}

			if ( $this->check_execution_limit ) {
				if ( function_exists( 'getrusage' ) ) {
					$resource_usage = getrusage();
					if ( isset( $resource_usage['ru_utime.tv_sec'] ) ) {
						$execution_time = $resource_usage['ru_stime.tv_sec'] + $resource_usage['ru_utime.tv_sec'] + 2;
						$d = ceil( $execution_time - $initial_execution_time );
						if ( intval( $d * self::DELTA_F ) > ( $max_execution_time - $d ) ) {
							wps_log_info(
								'WooCommerce Product Search - ' .
								esc_html__( 'Info', 'woocommerce-product-search' ) .
								' : ' .
								sprintf(
									esc_html__( 'Stopped on iteration %d to avoid reaching the maximum execution time for PHP.', 'woocommerce-product-search' ),
									esc_html( $i )
									)
							);
							$stop = true;
						}
					}
				}
			}

			if ( $stop ) {
				break;
			}
			$i++;
		}

		$this->release();
	}

	public function purge( $post_id ) {

		global $wpdb;

		$this->delete_indexes( $post_id, 'product' );

		if ( $product = wc_get_product( $post_id ) ) {
			if ( $product->is_type( 'variable' ) ) {

				$variation_ids = $wpdb->get_col( $wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'",
					intval( $post_id )
				) );
				if ( is_array( $variation_ids ) ) {
					foreach( $variation_ids as $variation_id ) {
						$this->delete_indexes( $variation_id, 'product_variation' );
					}
				}
			}
			unset( $product );
		}
		$this->delete_unused_keys();
	}

	/**
	 * Process the terms in the index.
	 *
	 * @param int|int[]|null $term_ids process term_id or term_ids, null triggers processing for all (default)
	 */
	public function process_terms( $term_ids = null ) {

		global $wpdb;
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

			if ( !is_array( $term_ids ) ) {
				if ( is_numeric( $term_ids ) ) {
					$term_ids = array( $term_ids );
				} else {

					$term_ids = array( -1 );
				}
			}
			$query = null;
			if ( $term_ids !== null && count( $term_ids ) > 0 ) {
				$term_ids = array_unique( array_map( 'intval', $term_ids ) );
				$query =
					"DELETE FROM $object_term_table WHERE " .
					'term_id = 0 AND ' .
					'object_id IN ( ' .
					'SELECT object_id FROM ( ' .
					"SELECT DISTINCT object_id FROM $object_term_table WHERE term_id IN ( " . implode( ',', $term_ids ) . ' ) ' .
					' ) tmp ' .
					' ) ';

			} else {
				$query = "DELETE FROM $object_term_table WHERE term_id = 0";
			}
			if ( $query !== null ) {
				$wpdb->query( $query );
			}
		}
	}

	public function index( $post_id ) {

		wps_log_info( 'Indexing ' . $post_id );

		global $wpdb;

		$this->preprocess_terms();

		if ( $this->raw ) {
			$product = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", intval( $post_id ) ) );
		} else {
			$product = wc_get_product( $post_id );
		}

		if ( $product ) {

			$object_type = 'product';
			if ( $product->is_type( 'variation' ) ) {
				$object_type = 'product_variation';
			}

			$key_table         = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
			$index_table       = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
			$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );
			$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

			$this->delete_indexes( $post_id, $object_type );

			if ( $product->is_type( 'variable' ) ) {

				$variation_ids = $wpdb->get_col( $wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'",
					intval( $post_id )
				) );
				if ( is_array( $variation_ids ) ) {
					foreach( $variation_ids as $variation_id ) {
						$this->delete_indexes( $variation_id, 'product_variation' );
					}
				}
			}

			$ids = array();
			$titles = array();
			$descriptions = array();
			$short_descriptions = array();
			$skus = array();
			if ( $product->is_type( 'variable' ) ) {
				if ( method_exists( $product, 'get_children' ) ) {
					$variation_ids = $product->get_children();
					foreach( $variation_ids as $variation_id ) {
						if ( $variation = wc_get_product( $variation_id ) ) {
							$ids[] = $variation->get_id();
							$titles[] = $variation->get_name();
							$descriptions[] = wc_format_content( $variation->get_description() );
							$short_descriptions[] = wc_format_content( $variation->get_short_description() );
							$skus[] = $variation->get_sku();
						}
						unset( $variation );
					}
				}
			}

			$parent_id = null;
			if ( $product->is_type( 'variation' ) ) {
				if ( method_exists( $product, 'get_parent_id' ) ) {
					$parent_id = $product->get_parent_id();
					if ( $parent = wc_get_product( $parent_id ) ) {
						$ids[]          = $parent->get_id();
						$titles[]       = $parent->get_name();
						$descriptions[] = wc_format_content( $parent->get_description() );
						$short_descriptions[] = wc_format_content( $parent->get_short_description() );
						$skus[]         = $parent->get_sku();
					}
				}
			}

			if ( count( $ids ) > 0 ) {
				$ids = ' ' . implode( ' ', $ids );
			} else {
				$ids = '';
			}
			if ( count( $titles ) > 0 ) {
				$titles = ' ' . implode( ' ', $titles );
			} else {
				$titles = '';
			}
			if ( count( $descriptions ) > 0 ) {
				$descriptions = ' ' . implode( ' ', $descriptions );
			} else {
				$descriptions = '';
			}
			if ( count( $short_descriptions ) > 0 ) {
				$short_descriptions = ' ' . implode( ' ', $short_descriptions );
			} else {
				$short_descriptions = '';
			}
			if ( count( $skus ) > 0 ) {
				$skus = ' ' . implode( ' ', $skus );
			} else {
				$skus = '';
			}

			$attribute_names = array();
			if ( $this->raw ) {
				$context_columns = array(
					'post_title'   => $this->filter( $product->post_title . $titles, 'post_title', $post_id ),
					'post_excerpt' => $this->filter( $product->post_excerpt . $descriptions, 'post_excerpt', $post_id ),
					'post_content' => $this->filter( $product->post_content, 'post_content', $post_id )
				);
			} else {
				if ( !$product->is_type( 'variation' ) ) {
					$context_columns = array(
						'post_id'      => $this->filter( $product->get_id() . $ids, 'post_id', $post_id ),
						'post_title'   => $this->filter( $product->get_title() . $titles, 'post_title', $post_id ),
						'post_excerpt' => $this->filter( wc_format_content( $product->get_short_description() ) . $short_descriptions, 'post_excerpt', $post_id ),
						'post_content' => $this->filter( wc_format_content( $product->get_description() ) . $descriptions, 'post_content', $post_id ),
						'sku'          => $this->filter( $product->get_sku() . $skus, 'sku', $post_id ),
						'tag'          => $this->filter( wc_get_product_tag_list( $post_id, ' ' ), 'tag', $post_id ),
						'category'     => $this->filter( wc_get_product_category_list( $post_id, ' ' ), 'category', $post_id )
					);

					$attribute_taxonomies = wc_get_attribute_taxonomies();
					if ( !empty( $attribute_taxonomies ) ) {
						foreach ( $attribute_taxonomies as $attribute ) {

							$index_attribute = true;

							if ( $product->is_type( 'variable' ) ) {

								/**
								 * @var WC_Product_Attribute $attribute_object
								 */
								$attribute_object = null;
								$attribute_objects = $product->get_attributes();

								if ( is_array( $attribute_objects ) ) {
									if ( isset( $attribute_objects[ $attribute->attribute_name ] ) ) {
										$attribute_object = $attribute_objects[ $attribute->attribute_name ];
									} elseif ( isset( $attribute_objects[ 'pa_' . $attribute->attribute_name ] ) ) {
										$attribute_object = $attribute_objects[ 'pa_' . $attribute->attribute_name ];
									}
								}

								if ( $attribute_object !== null ) {
									if ( is_object( $attribute_object ) && method_exists( $attribute_object, 'get_variation' ) ) {
										if ( $attribute_object->get_variation() ) {
											$index_attribute = false;
										}
									}
								}
							}

							if ( $index_attribute ) {
								$term_list = get_the_term_list( $post_id, 'pa_' . $attribute->attribute_name, '', ' ', '' );
								if ( !empty( $term_list ) && is_string( $term_list ) ) {
									$attribute_names[] = $attribute->attribute_name;
									$context_columns[$attribute->attribute_name] = $this->filter( $term_list, $attribute->attribute_name, $post_id );
								}
							}
						}
					}
				} else {

					if ( $parent_id ) {
						$context_columns = array(
							'post_id'      => $this->filter( $product->get_id() . $ids, 'post_id', $product->get_id() ),
							'post_title'   => $this->filter( $product->get_title() . $titles, 'post_title', $product->get_id() ),
							'post_excerpt' => $this->filter( wc_format_content( $product->get_short_description() ) . $short_descriptions, 'post_excerpt', $product->get_id() ),
							'post_content' => $this->filter( wc_format_content( $product->get_description() ) . $descriptions, 'post_content', $product->get_id() ),
							'sku'          => $this->filter( $product->get_sku() . $skus, 'sku', $product->get_id() ),
							'tag'          => $this->filter( wc_get_product_tag_list( $parent_id, ' ' ), 'tag', $product->get_id() ),
							'category'     => $this->filter( wc_get_product_category_list( $parent_id, ' ' ), 'category', $product->get_id() )
						);
						$attribute_taxonomies = wc_get_attribute_taxonomies();
						if ( !empty( $attribute_taxonomies ) ) {
							foreach ( $attribute_taxonomies as $attribute ) {
								$term_string = $product->get_attribute( $attribute->attribute_name );
								if ( !empty( $term_string ) && is_string( $term_string ) ) {
									$attribute_names[] = $attribute->attribute_name;
									$context_columns[$attribute->attribute_name] = $this->filter( $term_string, $attribute->attribute_name, $product->get_id() );
								}
							}
						}
					}
				}
			}

			foreach( $context_columns as $context_column => $content ) {
				switch( $context_column ) {
					case 'category' :
						$object_type_id = $this->get_object_type_id( $object_type, 'category', 'term_taxonomy', 'taxonomy', 'product_cat' );
						break;
					case 'tag' :
						$object_type_id = $this->get_object_type_id( $object_type, 'tag', 'term_taxonomy', 'taxonomy', 'product_tag' );
						break;
					case 'sku' :
						$object_type_id = $this->get_object_type_id( $object_type, 'sku', 'postmeta', 'meta_key', '_sku' );
						break;
					default :
						if ( !in_array( $context_column, $attribute_names ) ) {
							$object_type_id = $this->get_object_type_id( $object_type, 'product', 'posts', $context_column );
						} else {
							$object_type_id = $this->get_object_type_id( $object_type, $context_column, 'term_taxonomy', 'taxonomy', 'pa_' .$context_column );
							if ( $object_type_id === null ) {
								$object_type_id = $this->get_or_add_object_type( $object_type, $context_column, 'term_taxonomy', 'taxonomy', 'pa_' .$context_column );
							}
						}
				}
				if ( $object_type_id !== null ) {
					$tokens = $this->tokenize( $content );
					foreach( $tokens as $token => $count ) {
						if ( strlen( $token ) === 0 || $count === 0 ) {
							continue;
						}
						$key_id = $this->add_key( $token );
						if ( $key_id ) {
							$this->add_index( $key_id, $post_id, $object_type_id, $count );
						}
					}
				}
			}

			$object_type_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT object_type_id FROM $object_type_table WHERE object_type = %s AND context_table = 'posts' AND context_column IS NULL AND context_key IS NULL",
				$object_type
			) );
			$wpdb->query( $wpdb->prepare(
				"INSERT INTO $index_table (key_id, object_id, object_type_id, count, modified) VALUES (0, %d, %d, 0, %s)",
				intval( $post_id ),
				intval( $object_type_id ),
				gmdate( 'Y-m-d H:i:s' )
			) );

			if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {

				$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

				$product_id   = $product->get_id();
				$type         = $product->get_type();
				$category_ids = $product->get_category_ids();
				$tag_ids      = $product->get_tag_ids();

				$attributes   = $product->get_attributes();

				$attribute_term_ids = array();

				$taxonomy_inherited = array();

				$parent_product = null;
				$parent_product_id = null;

				if ( $product->is_type( 'variation' ) ) {

					$parent_product_id = $product->get_parent_id();
					if ( $parent_product_id ) {
						$parent_product = wc_get_product( $parent_product_id );
						if ( ! ( $parent_product instanceof WC_Product ) ) {
							$parent_product = null;
							$parent_product_id = null;
						} else {

							$category_ids = array_merge( $category_ids, $parent_product->get_category_ids() );
							$tag_ids = array_merge( $tag_ids, $parent_product->get_tag_ids() );
						}
					}

					foreach ( $attributes as $taxonomy => $attribute ) {

						if ( $parent_product !== null ) {
							$parent_attributes = $parent_product->get_attributes();
							foreach ( $parent_attributes as $parent_taxonomy => $wc_product_attribute ) {
								if ( is_string( $parent_taxonomy ) && ( $wc_product_attribute instanceof WC_Product_Attribute ) ) {
									if ( !$wc_product_attribute->get_variation() ) {
										$attribute_term_ids = array_merge( $attribute_term_ids, wc_get_product_term_ids( $parent_product_id, $parent_taxonomy ) );
									}
								}
							}
						}

						if ( is_string( $taxonomy ) && is_string( $attribute ) ) {
							$term = get_term_by( 'slug', $attribute, $taxonomy );
							if ( $term ) {
								$attribute_term_ids[] = $term->term_id;
							}
						}
					}

				} else if ( $product->is_type( 'variable' ) ) {

					$taxonomy_inherited[] = 'product_cat';
					$taxonomy_inherited[] = 'product_tag';

					foreach ( $attributes as $taxonomy => $wc_product_attribute ) {
						if ( is_string( $taxonomy ) && ( $wc_product_attribute instanceof WC_Product_Attribute ) ) {

							$attribute_term_ids = array_merge( $attribute_term_ids, wc_get_product_term_ids( $product_id, $taxonomy ) );

							if ( !$wc_product_attribute->get_variation() ) {
								$taxonomy_inherited[] = $taxonomy;
							}
						}
					}

				} else {

					foreach ( $attributes as $taxonomy => $wc_product_attribute ) {
						if ( is_string( $taxonomy ) && ( $wc_product_attribute instanceof WC_Product_Attribute ) ) {
							$attribute_term_ids = array_merge( $attribute_term_ids, wc_get_product_term_ids( $product_id, $taxonomy ) );
						}
					}

				}

				$fields = array( 'object_id', 'parent_object_id', 'term_id', 'parent_term_id', 'object_type', 'taxonomy', 'inherit', 'modified' );
				$term_ids = array_merge( $category_ids, $tag_ids, $attribute_term_ids );

				foreach ( $term_ids as $term_id ) {
					$term = get_term( $term_id );
					if ( $term instanceof WP_Term ) {
						$parent_term_id = $term->parent;
						if ( is_numeric( $parent_term_id ) ) {
							$parent_term_id = intval( $parent_term_id );
							if ( $parent_term_id === 0 ) {
								$parent_term_id = null;
							}
						} else {
							$parent_term_id = null;
						}
						$placeholders = array(
							'%d',
							$parent_product_id !== null ? '%d' : 'NULL',
							'%d',
							$parent_term_id !== null ? '%d' : 'NULL',
							'%s',
							'%s',
							'%d',
							'%s'
						);
						$values = array();
						$values[] = intval( $product_id );
						if ( $parent_product_id !== null ) {
							$values[] = intval( $parent_product_id );
						}
						$values[] = intval( $term_id );
						if ( $parent_term_id !== null ) {
							$values[] = intval( $parent_term_id );
						}
						$values[] = $type;
						$values[] = $term->taxonomy;
						$values[] = in_array( $term->taxonomy, $taxonomy_inherited ) ? 1 : 0;
						$values[] = gmdate( 'Y-m-d H:i:s' );
						$query = "INSERT INTO $object_term_table ";
						$query .= ' ( ' . implode( ',', $fields ) . ' ) ';
						$query .= 'VALUES ( ' . implode( ',', $placeholders ) . ' ) ';
						$query = $wpdb->prepare( $query, $values );
						$wpdb->query( $query );
					}
				}

				if ( $parent_product_id === null ) {
					$wpdb->query( $wpdb->prepare(
						"INSERT INTO $object_term_table " .
						"( object_id, term_id, object_type, modified ) " .
						"VALUES ( %d, 0, %s, %s )",
						intval( $product_id ),
						$type,
						gmdate( 'Y-m-d H:i:s' )
					) );
				} else {
					$wpdb->query( $wpdb->prepare(
						"INSERT INTO $object_term_table " .
						"( object_id, parent_object_id, term_id, object_type, modified ) " .
						"VALUES ( %d, %d, 0, %s, %s )",
						intval( $product_id ),
						intval( $parent_product_id ),
						$type,
						gmdate( 'Y-m-d H:i:s' )
					) );
				}

			}
		}
	}

	public function get_object_type_id( $object_type = null, $context = null, $context_table = null, $context_column = null, $context_key = null ) {

		global $wpdb;

		$object_type_id = null;

		$object_types = wp_cache_get( 'object_types', self::CACHE_GROUP );
		if ( $object_types === false ) {
			$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );
			$query = "SELECT * FROM $object_type_table";
			$rows = $wpdb->get_results( $query );
			if ( is_array( $rows ) ) {
				foreach ( $rows as $row ) {
					$hash = md5( implode( ',', array(
						'object_type'    => $row->object_type,
						'context'        => $row->context,
						'context_table'  => $row->context_table,
						'context_column' => $row->context_column,
						'context_key'    => $row->context_key
					) ) );
					$object_types[$hash] = $row->object_type_id;
				}

				$cached = wp_cache_set( 'object_types', $object_types, self::CACHE_GROUP );
			}
		}
		$hash = md5( implode( ',', array(
			'object_type'    => $object_type,
			'context'        => $context,
			'context_table'  => $context_table,
			'context_column' => $context_column,
			'context_key'    => $context_key
		) ) );
		if ( isset( $object_types[$hash] ) ) {
			$object_type_id = $object_types[$hash];
		}
		return $object_type_id;
	}

	private function get_or_add_object_type( $object_type = null, $context = null, $context_table = null, $context_column = null, $context_key = null ) {

		global $wpdb;
		$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_type' ) ) {
			$object_type = array(
				'object_type'    => $object_type,
				'context'        => $context,
				'context_table'  => $context_table,
				'context_column' => $context_column,
				'context_key'    => $context_key
			);
			$fields       = array();
			$placeholders = array();
			$values       = array();
			foreach( $object_type as $field => $value ) {
				if ( $value !== null ) {
					$fields[]       = $field;
					$placeholders[] = '%s';
					$values[]       = $value;
				}
			}
			$conditions = array();
			for ( $i = 0; $i < count( $fields ); $i++ ) {
				$conditions[] = sprintf( '%s = %s', $fields[$i], $placeholders[$i] );
			}
			if ( count( $conditions ) > 0 ) {
				$where = ' WHERE ' . implode( ' AND ', $conditions );
				$object_type_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT object_type_id FROM $object_type_table $where", $values
				) );
				if ( !$object_type_id ) {
					$query = $wpdb->prepare(
						sprintf(
							"INSERT INTO $object_type_table (%s) VALUES (%s)",
							implode( ',', $fields ),
							implode( ',', $placeholders )
						),
						$values
					);
					if ( $wpdb->query( $query ) === false ) {
						$result = false;
						wps_log_error( 'Failed to execute database query: ' . $query );
					} else {
						$object_type_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );

						$deleted = wp_cache_delete( 'object_types', self::CACHE_GROUP );
					}
				}
			}
		}
		if ( $object_type_id !== null ) {
			$object_type_id = intval( $object_type_id );
		}
		return $object_type_id;
	}

	private function delete_indexes( $object_id, $object_type, $context = null, $context_table = null, $context_column = null, $context_key = null ) {

		global $wpdb;

		$index_table       = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
		$object_type_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_type' );

		$where = array( 'object_type = %s' );
		$values = array( $object_type );
		if ( $context !== null ) {
			$where[] = 'context = %s';
			$values[] = $context;
		}
		if ( $context_table !== null ) {
			$where[] = 'context_table = %s';
			$values[] = $context_table;
		}
		if ( $context_column !== null ) {
			$where[] = 'context_column = %s';
			$values[] = $context_column;
		}
		if ( $context_key !== null ) {
			$where[] = 'context_key = %s';
			$values[] = $context_key;
		}
		$where = ' WHERE ' . implode( ' AND ', $where );

		$object_type_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT object_type_id FROM $object_type_table " . $where,
			$values
		) );
		if ( !empty( $object_type_ids ) && is_array( $object_type_ids ) ) {
			$object_type_ids = '(' . implode( ',', array_map( 'intval', $object_type_ids ) ) . ')';
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $index_table WHERE object_id = %d AND object_type_id IN $object_type_ids",
				intval( $object_id )
			) );

		}

		$object_term_table = WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
		if ( WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			$wpdb->query( $wpdb->prepare(
				"DELETE FROM $object_term_table WHERE object_id = %d",
				intval( $object_id )
			) );
		}
	}

	public function gc() {

		$now = time();
		$last = get_transient( 'woocommerce_product_search_indexer_gc' );

		if ( $now - $last > self::GC_CYCLE ) {
			wps_log_info( sprintf(
				'GC is cleaning up @ %s; last @ %s',
				date( 'Y-m-d H:i:s', $now ),
				( $last ? date( 'Y-m-d H:i:s', $last ) : '-' )
			) );
			$this->delete_unused_keys();
			set_transient( 'woocommerce_product_search_indexer_gc', $now );
		}
	}

	private function delete_unused_keys() {
		global $wpdb;
		$key_table = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
		$index_table = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
		$wpdb->query( "DELETE FROM $key_table WHERE key_id NOT IN (SELECT key_id FROM $index_table)" );
	}

	/**
	 * Adds the given key and returns the key_id. Returns the key_id for an existing entry.
	 *
	 * @param string $key
	 *
	 * @return int key_id
	 */
	private function add_key( $key ) {
		global $wpdb;

		$key = trim( remove_accents( $key ) );

		$key = function_exists( 'mb_strlen' ) ?
			mb_substr( $key, 0, WooCommerce_Product_Search_Controller::MAX_KEY_LENGTH ) :
			strlen( $key, 0, WooCommerce_Product_Search_Controller::MAX_KEY_LENGTH );

		$key_table = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
		$key_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT key_id FROM $key_table WHERE `key` = %s",
			$key
		) );
		if ( !$key_id ) {
			if (
				$wpdb->query( $wpdb->prepare(
				"INSERT INTO $key_table (`key`) VALUES (%s)",
				$key
			) ) ) {
				$key_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
			}
		}

		return intval( $key_id );
	}

	/**
	 * Adds (or updates an existing one's count and modified column) the given index based on $key_id, $object_id and $object_type_id.
	 *
	 * @param int $key_id
	 * @param int $object_id
	 * @param int $object_type_id
	 * @param int $count
	 *
	 * @return int index_id
	 */
	private function add_index( $key_id, $object_id, $object_type_id, $count ) {
		global $wpdb;
		$index_table = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
		$index_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT index_id FROM $index_table WHERE key_id = %d AND object_id = %d AND object_type_id = %d",
			intval( $key_id ),
			intval( $object_id ),
			intval( $object_type_id )
		) );
		if ( !$index_id ) {
			if ( $wpdb->query( $wpdb->prepare(
				"INSERT INTO $index_table (key_id, object_id, object_type_id, count, modified) VALUES (%d, %d, %d, %d, %s)",
				intval( $key_id ),
				intval( $object_id ),
				intval( $object_type_id ),
				intval( $count ),
				gmdate( 'Y-m-d H:i:s' )
			) ) ) {
				$index_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" );
			}
		} else {
			$wpdb->query( $wpdb->prepare(
				"UPDATE $index_table SET count = %d, modified = %s WHERE key_id = %d AND object_id = %d AND object_type_id = %d",
				intval( $count ),
				gmdate( 'Y-m-d H:i:s' ),
				intval( $key_id ),
				intval( $object_id ),
				intval( $object_type_id )
			) );
		}
		return intval( $index_id );
	}

	public function filter( $content, $context = '', $post_id = null ) {
		$content = apply_filters( 'woocommerce_product_search_indexer_filter_content', $content, $context, $post_id );
		$content = self::clean( $content, $context );
		return $content;
	}

	public function tokenize( $s ) {

		$s = self::normalize( $s );

		$tokens = explode( ' ', $s );
		$tokens_ = array();
		foreach ( $tokens as $token ) {
			$has_dash = false;
			if ( function_exists( 'mb_strpos' ) ) {
				$has_dash = mb_strpos( $token, '-' ) !== false;
			} else {
				$has_dash = strpos( $token, '-' ) !== false;
			}
			if ( $has_dash ) {
				$token_ = preg_replace( '/-+/', ' ', $token );
				$tokens_[] = $token_;
				$tokens_ = array_merge( $tokens_, explode( ' ', $token_ ) );
				$tokens_[] = preg_replace( '/-+/', '', $token );
			}
		}
		$tokens = array_merge( $tokens, $tokens_ );

		$counts = array();
		foreach ( $tokens as $token ) {
			if ( strlen( $token ) > 0 ) {
				if ( !isset( $counts[$token] ) ) {
					$counts[$token] = 0;
				}
				$counts[$token]++;
			}
		}
		return $counts;
	}

	public static function clean( $content, $context = '' ) {
		if ( is_string( $content ) ) {
			$content = preg_replace( '/<(script|style)[^>]*?>.*?<\/\\1>/si', ' ', $content );
			$content = strip_tags( $content );
			$content = strip_shortcodes( $content );
		} else {
			if ( !empty( $content ) ) {
				$error_message = json_encode( $content );
				if ( !empty( $content ) && ( $content instanceof WP_Error ) ) {
					$error_message .= ' | ' . $content->get_error_message();
				}
				$error_message .= ' | ' . $context;
				wps_log_warning( 'Invalid content received.' . ( !empty( $error_message ) ? ( ' ' . esc_html( $error_message ) ) : '' ) );
			}
			$content = '';
		}
		return $content;
	}

	public static function normalize( $s ) {
		$s = preg_replace( '/(\p{Han})/u', ' $0 ', $s );
		$s = preg_replace( '/(\p{Hiragana}+)/u', ' $0 ', $s );
		$s = preg_replace( '/(\p{Katakana}+)/u', ' $0 ', $s );
		$s = preg_replace( '/(\p{Hangul}+)/u', ' $0 ', $s );
		$s = preg_replace( '/(\p{Thai}+)/u', ' $0 ', $s );
		$s = preg_replace( '/(\p{Myanmar}+)/u', ' $0 ', $s );

		$s = self::equalize( $s );

		if ( function_exists( 'mb_strtolower' ) ) {
			$s = mb_strtolower( $s );
		} else {
			$s = strtolower( $s );
		}
		return $s;
	}

	/**
	 * Equalize the input string.
	 *
	 * @since 2.9.0
	 * @param string $s
	 *
	 * @return string
	 */
	public static function equalize( $s ) {

		$s = preg_replace( '/[^\P{P}-]+/u', ' ', $s );
		$s = preg_replace( '/[^\p{L}\p{N}-]++/u', ' ', $s );
		$s = trim( preg_replace( '/\s-+/', ' ', $s ) );
		$s = trim( preg_replace( '/-+\s/', ' ', $s ) );
		$s = trim( preg_replace( '/\s+/', ' ', $s ) );
		return $s;
	}
}
WooCommerce_Product_Search_Indexer::init();
