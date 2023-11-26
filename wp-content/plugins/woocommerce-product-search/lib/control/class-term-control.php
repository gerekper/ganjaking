<?php
/**
 * class-query-control.php
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
 * Term Control.
 */
class Term_Control {

	const CACHE_GROUP = 'term-control';

	const COUNTS_CACHE_GROUP = 'term-control-counts';

	const IXWPST_CACHE_GROUP = 'term-control-ixwpst';

	const CACHE_LIFETIME = Cache::HOUR;

	const PARSE_REQUEST_PRIORITY = 99999;

	private static $get_terms_args_priority = null;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'parse_request', array( __CLASS__, 'parse_request' ), self::PARSE_REQUEST_PRIORITY );
		add_action( 'woocommerce_product_search_engine_process_start', array( __CLASS__, 'woocommerce_product_search_engine_process_start' ) );
		add_action( 'woocommerce_product_search_engine_process_end', array( __CLASS__, 'woocommerce_product_search_engine_process_end' ) );
	}

	public static function wp_init() {
		if (
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpst'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] ) ||
			\WooCommerce_Product_Search_Service::get_s() !== null
		) {
			add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 );
		}
	}

	/**
	 * Handle the parse_request action.
	 *
	 * @param \WP $wp
	 */
	public static function parse_request( $wp ) {

		if ( !has_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ) ) ) {
			if ( self::is_product_taxonomy_request( $wp->query_vars ) ) {
				add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 );
			}
		}
	}

	/**
	 * Check for product taxonomy request.
	 *
	 * @param array $query_vars
	 *
	 * @return boolean
	 */
	private static function is_product_taxonomy_request( $query_vars ) {
		$result = false;
		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		foreach ( $product_taxonomies as $taxonomy ) {
			if ( key_exists( $taxonomy, $query_vars ) ) {

				$result = true;
				break;
			}
		}
		return $result;
	}

	/**
	 * Engine processing start action handler.
	 *
	 * @param Engine $engine
	 */
	public static function woocommerce_product_search_engine_process_start( $engine ) {
		self::$get_terms_args_priority = has_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ) );
		if ( self::$get_terms_args_priority !== false ) {
			remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), self::$get_terms_args_priority );
		}
	}

	/**
	 * Engine processing end action handler.
	 *
	 * @param Engine $engine
	 */
	public static function woocommerce_product_search_engine_process_end( $engine ) {
		if ( self::$get_terms_args_priority !== null && self::$get_terms_args_priority !== false ) {
			add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), self::$get_terms_args_priority, 2 );
		}
	}

	/**
	 * Provides the ixwpst from the request if set, empty array otherwise.
	 *
	 * @return array
	 */
	public static function get_request_ixwpst() {

		return isset( $_REQUEST['ixwpst'] ) && is_array( $_REQUEST['ixwpst'] ) ? $_REQUEST['ixwpst'] : array();
	}

	/**
	 * Resolves the ixwpst for the current context.
	 *
	 * @param \WP_Query $query
	 *
	 * @return array ixwpst
	 */
	public static function get_ixwpst( $query = null ) {

		global $wp_query;

		$_ixwpst = self::get_request_ixwpst();

		$ixwpst = array();
		if ( is_array( $_ixwpst ) ) {
			foreach ( $_ixwpst as $taxonomy => $term_ids ) {
				$taxonomy = sanitize_text_field( $taxonomy );
				if ( taxonomy_exists( $taxonomy ) && is_array( $term_ids ) && count( $term_ids ) > 0 ) {
					Tools::unique_int( $term_ids );
					sort( $term_ids );
					$ixwpst[$taxonomy] = $term_ids;
				}
			}
		}

		require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-context.php';
		$context = \WooCommerce_Product_Search_Filter_Context::get_context();

		$cache_context = array(
			'wp_query' => $query !== null && $query instanceof \WP_Query ? $query->query_vars : null,
			'get'      => !empty( $_GET ) ? $_GET : null,
			'post'     => !empty( $_POST ) ? $_POST : null,
			'ixwpst'   => $ixwpst,
			'context'  => $context
		);
		$cache_key = self::get_cache_key( $cache_context );
		$cache = Cache::get_instance();
		$result = $cache->get( $cache_key, self::IXWPST_CACHE_GROUP );
		if ( is_array( $result ) ) {
			return $result;
		}

		if ( isset( $wp_query ) && ( is_single() || is_page() ) ) {

			if ( $context !== null ) {

				if ( !empty( $context['taxonomy_terms'] ) && is_array( $context['taxonomy_terms'] ) ) {
					$taxonomy_terms = $context['taxonomy_terms'];
					foreach ( $taxonomy_terms as $taxonomy => $term_ids ) {
						if ( count( $term_ids ) > 0 ) {

							if (
								!isset( $ixwpst[$taxonomy] ) ||
								is_array( $ixwpst[$taxonomy] ) && count( $ixwpst[$taxonomy] ) === 0
							) {
								$ixwpst[$taxonomy] = $term_ids;
							} else {

								$context_children = array();
								foreach ( $term_ids as $term_id ) {
									$term_children = get_term_children( $term_id, $taxonomy );
									if (
										!empty( $term_children ) &&
										!( $term_children instanceof \WP_Error ) &&
										( count( $term_children ) > 0 )
									) {
										$context_children = array_merge( $context_children, $term_children );
									}
								}
								$context_term_ids = array_merge( $term_ids, $context_children );

								$ixwpst[$taxonomy] = array_intersect( $ixwpst[$taxonomy], $context_term_ids );

								if ( count( $ixwpst[$taxonomy] ) === 0 ) {
									$ixwpst[$taxonomy] = $term_ids;
								}

							}
						}
					}
				}
			}

		}

		if ( !empty( $query ) && $query->is_tax ) {

			$process_query      = false;
			$product_taxonomies = array( 'product_cat', 'product_tag' );
			$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
			$product_taxonomies = array_unique( $product_taxonomies );
			$queried_object     = $query->get_queried_object();
			if ( is_object( $queried_object ) ) {
				if ( in_array( $queried_object->taxonomy, $product_taxonomies ) ) {
					$process_query = true;
				}
			}
			if ( !$process_query ) {
				$cache->set( $cache_key, $ixwpst, self::IXWPST_CACHE_GROUP, self::CACHE_LIFETIME );
				return $ixwpst;
			}

			$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
			$get_terms_filter_priority = has_filter( 'get_terms', array( '\WooCommerce_Product_Search_Service', 'get_terms' ) );
			if ( $get_terms_filter_priority !== false ) {
				remove_filter( 'get_terms', array( '\WooCommerce_Product_Search_Service', 'get_terms' ), $get_terms_filter_priority );
			}
			$queried_object = $query->get_queried_object();
			if ( $had_get_terms_args ) {
				add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 );
			}
			if ( $get_terms_filter_priority !== false ) {
				add_filter( 'get_terms', array( '\WooCommerce_Product_Search_Service', 'get_terms' ), $get_terms_filter_priority, 4 );
			}

			if ( is_object( $queried_object ) ) {
				if ( isset( $queried_object->taxonomy ) && isset( $queried_object->term_id ) ) {
					if ( !empty( $query->tax_query ) && !empty( $query->tax_query->queried_terms ) ) {
						$taxonomy_term_ids = array();
						foreach ( $query->tax_query->queried_terms as $tax_query_taxonomy => $queried_term ) {
							if ( !empty( $queried_term['terms'] ) && !empty( $queried_term['field'] ) ) {
								switch ( $queried_term['field'] ) {
									case 'term_id':
										$taxonomy_term_ids[$tax_query_taxonomy] = $queried_term['terms'];
										break;
									default:
										foreach ( $queried_term['terms'] as $value ) {
											$term = get_term_by( $queried_term['field'], $value, $tax_query_taxonomy );
											if ( $term instanceof \WP_Term ) {
												$taxonomy_term_ids[$tax_query_taxonomy][] = $term->term_id;
											}
										}
								}
							}
						}

						foreach ( $taxonomy_term_ids as $taxonomy => $term_ids ) {
							if ( in_array( $taxonomy, $product_taxonomies ) && is_array( $term_ids ) && count( $term_ids ) > 0 ) {
								$term_ids = array_unique( array_map( 'intval', $term_ids ) );
								if (
									key_exists( $taxonomy, $ixwpst ) &&
									is_array( $ixwpst[$taxonomy] ) &&
									count( $ixwpst[$taxonomy] ) > 0
								) {

									if ( is_taxonomy_hierarchical( $taxonomy ) ) {

										$use_term_ids = array();

										foreach ( $term_ids as $term_id ) {
											$term_id = intval( $term_id );
											$these_term_ids = array( $term_id );
											$term_children_ids = get_term_children( $term_id, $taxonomy );
											if ( is_array( $term_children_ids ) ) {
												$term_children_ids = array_map( 'intval', $term_children_ids );
												$these_term_ids = array_merge( $these_term_ids, $term_children_ids );
											}
											$use_term_ids = array_merge( $use_term_ids, array_intersect( $ixwpst[$taxonomy], $these_term_ids ) );
										}

										foreach ( $ixwpst[$taxonomy] as $term_id ) {
											$term_id = intval( $term_id );
											$these_term_ids = array( $term_id );
											$term_children_ids = get_term_children( $term_id, $taxonomy );
											if ( is_array( $term_children_ids ) ) {
												$term_children_ids = array_map( 'intval', $term_children_ids );
												$these_term_ids = array_merge( $these_term_ids, $term_children_ids );
											}
											$use_term_ids = array_merge( $use_term_ids, array_intersect( $term_ids, $these_term_ids ) );
										}

										$ixwpst[$taxonomy] = $use_term_ids;
									} else {

										$ixwpst[$taxonomy] = array_intersect( array_unique( array_map( 'intval', $ixwpst[$taxonomy] ) ), $term_ids );
									}
									if ( count( $ixwpst[$taxonomy] ) === 0 ) {

										$ixwpst[$taxonomy] = \WooCommerce_Product_Search_Service::NONE;
									}
								} else {

									$ixwpst[$taxonomy] = array_map( 'intval', $term_ids );
								}
							}
						}
					}
				}
			}
		}

		$cache->set( $cache_key, $ixwpst, self::IXWPST_CACHE_GROUP, self::CACHE_LIFETIME );

		return $ixwpst;
	}

	/**
	 * Handler for the get_terms_args filter.
	 *
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	public static function get_terms_args( $args, $taxonomies ) {

		$settings = Settings::get_instance();
		$apply = $settings->get( \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY, \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY_DEFAULT );
		if ( !apply_filters( 'woocommerce_product_search_get_terms_args_apply', $apply, $args, $taxonomies ) ) {
			return $args;
		}

		$stop_args = apply_filters(
			'woocommerce_product_search_get_terms_args_stop_args',
			array(
				'child_of',
				'description__like',
				'name',
				'name__like',
				'object_ids',
				'parent',
				'search',
				'slug',
				'term_taxonomy_id'
			)
		);
		foreach ( $stop_args as $stop_arg) {
			if ( !empty( $args[$stop_arg] ) ) {
				return $args;
			}
		}

		if ( is_string( $taxonomies ) ) {
			$taxonomies = array( $taxonomies );
		}
		if ( is_array( $taxonomies ) ) {
			$taxonomies = array_unique( $taxonomies );
		} else {
			return $args;
		}

		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		$product_taxonomies = array_intersect( $taxonomies, $product_taxonomies );
		$process_terms      = count( $product_taxonomies ) !== 0 && count( $product_taxonomies ) === count( $taxonomies );

		if ( $process_terms ) {
			foreach ( $taxonomies as $taxonomy ) {
				if (
					isset( $_REQUEST['ixwpsf'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] )
				) {
					if ( strval( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] ) === '0' ) {

						$process_terms = false;
						break;
					}
				}
			}
		}

		$process_terms = apply_filters(
			'woocommerce_product_search_get_terms_args_process_terms',
			$process_terms,
			$args,
			$taxonomies
		);

		if ( !$process_terms ) {
			return $args;
		}

		$allowed_term_ids = self::get_term_ids( $args, $taxonomies );
		if ( is_array( $allowed_term_ids ) && count( $allowed_term_ids ) > 0 ) {
			$args_include = null;
			if ( !empty( $args['include'] ) ) {
				if ( is_string( $args['include'] ) ) {
					$args['include'] = explode( ',', $args['include'] );
				}
				if ( is_array( $args['include'] ) ) {
					Tools::unique_int( $args['include'] );
					if ( count( $args['include'] ) > 0 ) {
						$args_include = $args['include'];
					}
				}
			}
			if ( $args_include !== null ) {
				$allowed_term_ids = array_intersect( $allowed_term_ids, $args_include );
			}
			if ( count( $allowed_term_ids ) > 0 ) {
				$args['include'] = array_map( 'intval', $allowed_term_ids );
			} else {
				$args['include'] = \WooCommerce_Product_Search_Service::NONE;
			}
		} else {
			$args['include'] = \WooCommerce_Product_Search_Service::NONE;
		}

		return $args;
	}

	/**
	 * Returns term IDs corresponding to current context.
	 *
	 * @param array $args options
	 * @param array|string $taxonomies
	 *
	 * @return array term IDs
	 */
	public static function get_term_ids( $args, $taxonomies ) {

		global $wpdb, $wp_query, $wps_doing_ajax;

		$result = array();

		if ( is_string( $taxonomies ) ) {
			$taxonomies = array( $taxonomies );
		}
		if ( is_array( $taxonomies ) ) {
			$taxonomies = array_unique( $taxonomies );
		} else {
			return $result;
		}

		$exclude = null;
		if ( isset( $args['exclude'] ) && is_array( $args['exclude'] ) && count( $args['exclude'] ) > 0 ) {
			$exclude = $args['exclude'];
			Tools::unique_int( $exclude );
		}
		$include = null;
		if ( isset( $args['include'] ) && is_array( $args['include'] ) && count( $args['include'] ) > 0 ) {
			$include = $args['include'];
			Tools::unique_int( $include );
		}
		$hide_empty = !isset( $args['hide_empty'] ) || $args['hide_empty'];

		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		$target_taxonomies  = $product_taxonomies;
		$product_taxonomies = array_intersect( $taxonomies, $product_taxonomies );

		$multiple_taxonomies = array();
		if ( isset( $_REQUEST['ixwpsf'] ) && isset( $_REQUEST['ixwpsf']['taxonomy'] ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy] ) ) {
					if (
						isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['multiple'] ) &&
						intval( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['multiple'] ) === 1
					) {
						$multiple_taxonomies[] = $taxonomy;
					}
				}
			}
		}

		$ixwpst = self::get_ixwpst( $wp_query );
		$taxonomy_term_ids = null;
		if ( !empty( $ixwpst ) ) {
			$taxonomy_term_ids = array();
			foreach ( $ixwpst as $index => $term_ids ) {

				if ( !is_array( $term_ids ) ) {
					$term_ids = array( $term_ids );
				}
				foreach ( $term_ids as $term_id ) {
					$term_id = intval( $term_id );
					$term = get_term( $term_id );
					if ( ( $term !== null ) && !( $term instanceof \WP_Error) ) {
						if ( in_array( $term->taxonomy, $target_taxonomies ) ) {
							$taxonomy_term_ids[$term->taxonomy][] = $term->term_id;

							$term_children = get_term_children( $term->term_id, $term->taxonomy );
							if ( !empty( $term_children ) && !( $term_children instanceof \WP_Error ) ) {
								foreach ( $term_children as $child_term_id ) {
									$taxonomy_term_ids[$term->taxonomy][] = $child_term_id;
									$taxonomy_term_ids[$term->taxonomy] = array_unique( $taxonomy_term_ids[$term->taxonomy] );
								}
							}
						}
					}
				}
			}
		}

		$query_control = new Query_Control();
		$params = $query_control->get_request_parameters();
		$params['variations'] = true;

		$cache_context = array(
			'exclude' => $exclude,
			'include' => $include,
			'hide_empty' => $hide_empty,
			'taxonomies' => $taxonomies,
			'multiple' => $multiple_taxonomies,
			'ixwpst' => $ixwpst,
			'params' => $params
		);
		$cache_key = self::get_cache_key( $cache_context );
		$cache = Cache::get_instance();
		$result = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( is_array( $result ) ) {
			return $result;
		}
		$result = array();

		$object_term_table = \WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );

		$where = array(
			"ot.taxonomy IN ('" . implode( "','", esc_sql( $product_taxonomies ) ) . "') "
		);
		if ( $include !== null ) {

			$include = apply_filters( 'woocommerce_product_search_request_term_ids_include', $include, $args, $taxonomies );
			Tools::unique_int( $include );
			if ( count( $include ) > 0 ) {
				$where[] = 'ot.term_id IN (' . implode( ',', $include ) . ') ';
			}
		}
		if ( $exclude !== null ) {

			$exclude = apply_filters( 'woocommerce_product_search_request_term_ids_exclude', $exclude, $args, $taxonomies );
			Tools::unique_int( $exclude );
			if ( count( $exclude ) > 0 ) {
				$where[] = 'ot.term_id NOT IN (' . implode( ',', $exclude ) . ') ';
			}
		}

		unset( $args );

		$post_ids_condition = null;

		$hide_empty = !isset( $args['hide_empty'] ) || $args['hide_empty'];
		if ( $hide_empty ) {

			$post_ids = null;
			if (
				isset( $_REQUEST['ixwpss'] ) ||
				isset( $_REQUEST['ixwpsp'] ) ||
				isset( $_REQUEST['ixwpse'] ) ||
				isset( $_REQUEST['ixwpst'] ) ||
				$params['search_query'] !== null
			) {

				$post_ids = $query_control->get_ids( $params );

			}

			if ( $post_ids !== null ) {

				if ( count( $post_ids ) === 0 ) {
					$post_ids = array( -1 );
				}

				Tools::int( $post_ids );
				$post_ids_condition = "ot.term_id IN ( SELECT DISTINCT term_id FROM $object_term_table WHERE object_id IN (" . implode( ',', $post_ids ) . ') )';
			}
		}

		if ( $taxonomy_term_ids !== null && is_array( $taxonomy_term_ids ) && count( $taxonomy_term_ids ) > 0 ) {

			$disjunctive_parts = array();
			foreach ( $taxonomy_term_ids as $taxonomy => $term_ids ) {
				if ( in_array( $taxonomy, $multiple_taxonomies ) ) {
					$disjunctive_parts[] = "ot.taxonomy = '" . esc_sql( $taxonomy ) . "'";
				}
			}
			if ( $post_ids_condition !== null ) {
				$disjunctive_parts[] = $post_ids_condition;
			}
			if ( count( $disjunctive_parts ) > 0 ) {
				$where[] = ' ( ' . implode( " OR ", $disjunctive_parts ) . ' ) ';
			}

			foreach ( $product_taxonomies as $taxonomy ) {

				if ( in_array( $taxonomy, $multiple_taxonomies ) ) {
					continue;
				}
				if ( key_exists( $taxonomy, $taxonomy_term_ids ) ) {
					if ( count( $taxonomy_term_ids[$taxonomy] ) > 0 ) {
						$term_ids = $taxonomy_term_ids[$taxonomy];
						Tools::int( $term_ids );
						$where[] = " ot.term_id IN (" . implode( ',', $term_ids ) . ") ";
					}
				}
			}
		}

		$query = "SELECT DISTINCT ot.term_id AS term_id FROM $object_term_table ot ";
		if ( count( $where ) > 0 ) {
			$query .= "WHERE " . implode( ' AND ', $where );
		}

		$rows = $wpdb->get_results( $query );
		if ( is_array( $rows ) && count( $rows ) > 0 ) {
			$allowed_term_ids = array_column( $rows, 'term_id' );
			foreach ( $allowed_term_ids as $allowed_term_id ) {
				$result[] = (int) $allowed_term_id;
			}
		}

		$cache->set( $cache_key, $result, self::CACHE_GROUP, self::CACHE_LIFETIME );

		return $result;
	}

	/**
	 * Compute a parameter-based cache key.
	 *
	 * @param array $parameters set of parameters
	 *
	 * @return string
	 */
	private static function get_cache_key( $parameters ) {

		return md5( json_encode( $parameters ) );
	}

	/**
	 * Provide the number of related products for the term.
	 *
	 * @param int $term_id
	 *
	 * @return int
	 */
	public static function get_term_count( $term_id ) {

		$count = 0;
		$term_id = intval( $term_id );
		$term = get_term( $term_id );
		if ( ( $term !== null ) && !( $term instanceof \WP_Error) ) {
			$count = $term->count;
			$term_counts = Term_Control::get_term_counts( $term->taxonomy );
			if ( isset( $term_counts[$term_id] ) ) {
				$count = $term_counts[$term_id];
			} else {
				$count = 0;
			}
		}
		return $count;
	}

	/**
	 * Provide context term counts.
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public static function get_term_counts( $taxonomy ) {

		global $wpdb, $wp_query;

		if ( !taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$query_control = new Query_Control();

		$unset = true;
		if ( isset( $wp_query ) ) {
			if ( $wp_query->is_main_query() ) {
				$query_control->set_query( $wp_query );
				if ( $wp_query->is_tax ) {
					$queried_object = $wp_query->get_queried_object();
					if ( isset( $queried_object->taxonomy ) ) {
						if ( $queried_object->taxonomy === $taxonomy ) {
							$unset = false;
						}
					}
				}
			}
		}
		$params = $query_control->get_request_parameters();
		$params['variations'] = true;

		if ( $unset ) {
			unset( $params['ixwpst'][$taxonomy] );
		}
		$cache_context = array(
			'taxonomy' => $taxonomy,
			'params' => $params
		);
		$cache_key = self::get_cache_key( $cache_context );
		$cache = Cache::get_instance();
		$counts = $cache->get( $cache_key, self::COUNTS_CACHE_GROUP );
		if ( is_array( $counts ) ) {
			return $counts;
		}
		$counts = array();

		$nodes = array();
		$terms = $wpdb->get_results( $wpdb->prepare(
			"SELECT term_id, parent FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
			$taxonomy
		) );
		if ( $terms ) {

			foreach ( $terms as $term ) {
				$term_id = (int) $term->term_id;
				$parent = !empty( $term->parent ) ? (int) $term->parent : null;
				if ( !isset( $nodes[$term_id] ) ) {
					$nodes[$term_id] = array(
						'count' => 0,
						'parent' => $parent,
						'children' => null
					);
				} else {
					if ( $parent !== null && $nodes[$term_id]['parent'] === null ) {

						$nodes[$term_id]['parent'] = $parent;
					}
				}
				if ( $parent !== null && $parent > 0 ) {
					if ( !isset( $nodes[$parent] ) ) {

						$nodes[$parent] = array(
							'count' => 0,
							'parent' => null,
							'children' => array( $term_id )
						);
					} else {
						if ( $nodes[$parent]['children'] === null || !in_array( $term_id, $nodes[$parent]['children'] ) ) {
							$nodes[$parent]['children'][] = $term_id;
						}
					}
				}
			}
		}

		$post_ids = $query_control->get_ids( $params );
		if ( count( $post_ids ) > 0 ) {
			$object_term_counts = array();
			$object_term_table = \WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
			Tools::unique_int( $post_ids );
			$query = $wpdb->prepare( "SELECT object_id, parent_object_id, term_id, parent_term_id, object_type, inherit FROM $object_term_table WHERE taxonomy = %s ", $taxonomy );
			$query .= ' AND object_id IN (' . implode( ',', $post_ids ) . ')';
			$object_terms = $wpdb->get_results( $query );
			if ( $object_terms ) {
				foreach ( $object_terms as $object_term ) {
					if ( !empty( $object_term->term_id ) ) {
						$term_id = $object_term->term_id;
						if ( !isset( $object_term_counts[$term_id] ) ) {
							$object_term_counts[$term_id] = array();
						}
						if ( !empty( $object_term->object_id ) ) {
							$object_id = null;
							switch ( $object_term->object_type ) {
								case 'variable':
								case 'variable-subscription':
									if ( $object_term->inherit ) {
										$object_id = $object_term->object_id;
									}
									break;
								default:
									$object_id = $object_term->object_id;
							}

							if ( !empty( $object_term->parent_object_id ) ) {
								$object_id = $object_term->parent_object_id;
							}

							if ( $object_id !== null ) {
								if( !isset( $object_term_counts[$term_id][$object_id] ) ) {
									$object_term_counts[$term_id][$object_id] = 0;
								}
								$object_term_counts[$term_id][$object_id]++;
							}
						}
					}
				}
				foreach ( $object_term_counts as $term_id => $object_ids ) {
					$nodes[$term_id]['count'] = count( $object_ids );
				}
			}
		}

		foreach ( $nodes as $term_id => $node ) {
			$counts[$term_id] = self::sum_tree( $nodes, $term_id );
		}

		$cache->set( $cache_key, $counts, self::COUNTS_CACHE_GROUP, self::CACHE_LIFETIME );

		return $counts;
	}

	/**
	 * Calculate the sum of counts for the given $term_id.
	 *
	 * @param array $nodes
	 * @param int $term_id
	 *
	 * @return int
	 */
	private static function sum_tree( &$nodes, $term_id = null ) {

		$sum = 0;
		if ( $term_id !== null ) {
			if ( isset( $nodes[$term_id] ) ) {
				if ( !isset( $nodes[$term_id]['sum'] ) ) {
					if ( !empty( $nodes[$term_id]['children'] ) ) {
						foreach ( $nodes[$term_id]['children'] as $child_id ) {
							$sum += self::sum_tree( $nodes, $child_id );
						}
					}
					$sum += $nodes[$term_id]['count'];
					$nodes[$term_id]['sum'] = $sum;
				} else {
					$sum = $nodes[$term_id]['sum'];
				}
			}
		}
		return $sum;
	}
}

Term_Control::init();
