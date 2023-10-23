<?php
/**
 * Query modification to filter products
 *
 * Filters WooCommerce query, to show only products matching selection
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Query' ) ) {
	/**
	 * Query Handling
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Query {

		/**
		 * Query parameter added to any filtered page url
		 *
		 * @var string
		 */
		protected $query_param = 'yith_wcan';

		/**
		 * List of query vars submitted for current query
		 *
		 * @var array submitted query vars.
		 */
		protected $query_vars = null;

		/**
		 * Get taxonomies that will be used for filtering
		 *
		 * @var array
		 */
		protected $supported_taxonomies = array();

		/**
		 * Products retrieved by by last query
		 *
		 * @var array
		 */
		protected $products = array();

		/**
		 * An array of product ids matcing current query, per filter
		 *
		 * @var array
		 */
		protected $products_per_filter = array();

		/**
		 * An array of currently choosen attributes
		 *
		 * @var array
		 */
		protected $chosen_attributes;

		/**
		 * Main instance
		 *
		 * @var YITH_WCAN_Query
		 * @since 4.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method for the class
		 */
		public function __construct() {
			// prepare query param.
			$this->query_param = apply_filters( 'yith_wcan_query_param', $this->query_param );

			// do all pre-flight preparation.
			add_action( 'parse_request', array( $this, 'suppress_default_query_vars' ) );
			add_filter( 'redirect_canonical', array( $this, 'suppress_canonical_redirect' ) );

			// let's start filtering.
			add_action( 'wp', array( $this, 'start_filtering' ) );
			add_action( 'wp', array( $this, 'register_products' ) );

			// alter default wc query.
			add_action( 'woocommerce_product_query', array( $this, 'alter_product_query' ), 10, 1 );
		}

		/* === QUERY VARS METHODS === */

		/**
		 * Get single query var
		 *
		 * @param string $query_var Query var to retrieve.
		 * @param mixed  $default   Default value, to use when query var isn't set.
		 *
		 * @return mixed Query var value, or default
		 */
		public function get( $query_var, $default = '' ) {
			$query_vars = $this->get_query_vars();

			if ( isset( $query_vars[ $query_var ] ) ) {
				return $query_vars[ $query_var ];
			}

			return $default;
		}

		/**
		 * Get single query var
		 *
		 * @param string $query_var Query var to retrieve.
		 * @param mixed  $value     Value to ues for the query var.
		 */
		public function set( $query_var, $value ) {
			$this->query_vars[ $query_var ] = $value;
		}

		/* === GET METHODS === */

		/**
		 * Get supported filter labels
		 *
		 * @return array Array of supported filter labels (filter id => filter label)
		 * @since 4.0.2
		 */
		public function get_supported_labels() {
			$taxonomies = $this->get_supported_taxonomies();

			$labels = apply_filters( 'yith_wcan_query_supported_labels', wp_list_pluck( $taxonomies, 'label' ) );

			return $labels;
		}

		/**
		 * Returns an array of supported taxonomies for filtering
		 *
		 * @return WP_Taxonomy[] Array of WP_Taxonomy objects
		 */
		public function get_supported_taxonomies() {
			if ( empty( $this->supported_taxonomies ) ) {
				$product_taxonomies   = get_object_taxonomies( 'product', 'objects' );
				$supported_taxonomies = array();

				if ( ! empty( $product_taxonomies ) ) {
					foreach ( $product_taxonomies as $taxonomy_slug => $taxonomy ) {
						if ( ! in_array( $taxonomy_slug, array( 'product_cat', 'product_tag' ), true ) && 0 !== strpos( $taxonomy_slug, 'pa_' ) ) {
							continue;
						}

						$supported_taxonomies[ $taxonomy_slug ] = $taxonomy;
					}
				}

				$this->supported_taxonomies = apply_filters( 'yith_wcan_supported_taxonomies', $supported_taxonomies );
			}

			return $this->supported_taxonomies;
		}

		/**
		 * Retrieves currently set query vars
		 *
		 * @return array Array of retrieved query vars; expected format: [
		 *     <product_taxonomy> => list of terms separated by , (OR) or by + (AND)
		 *     filter_<product_attribute> => list of terms separated by ,
		 *     meta_<meta_key> => meta value, eventually prefixed by operator (<,>, <=, >=, !=, IN, NOTIN)
		 *     query_type_<product_attribute> => and/or,
		 *     min_price => float,
		 *     max_price => float,
		 *     rating_filter => int,
		 *     orderby => string,
		 *     order => string,
		 *     onsale_filter => bool,
		 *     instock_filter => bool,
		 * ]
		 */
		public function get_query_vars() {
			if ( ! is_null( $this->query_vars ) ) {
				return $this->query_vars;
			}

			$query = $this->sanitize_query( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// unset parameters that aren't related to filters.
			$supported_parameters = apply_filters(
				'yith_wcan_query_supported_parameters',
				array_merge(
					array( 's' ),
					array_keys( $this->get_supported_taxonomies() )
				)
			);

			// remove parameters that won't contribute to filtering.
			if ( ! empty( $query ) ) {
				foreach ( $query as $key => $value ) {
					if ( 0 === strpos( $key, 'filter_' ) ) {
						// include layered nav attributes filtering parameters.
						continue;
					} elseif ( 0 === strpos( $key, 'meta_' ) ) {
						// include meta filtering parameters.
						continue;
					} elseif ( 0 === strpos( $key, 'query_type_' ) ) {
						// include meta filtering parameters.
						continue;
					} elseif ( ! in_array( $key, $supported_parameters, true ) ) {
						unset( $query[ $key ] );
					}
				}
			}

			// add any parameter related to current page.
			if ( is_product_taxonomy() ) {
				global $wp_query;

				$qo = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;

				if ( $qo instanceof WP_Term && ! isset( $query[ $qo->taxonomy ] ) ) {
					$query[ $qo->taxonomy ] = $qo->slug;
				}
			}

			/**
			 * We only store _query_vars once main query is executed, to be sure not to left behind any parameter.
			 *
			 * @since 4.1.1
			 */
			$this->query_vars = did_action( 'wp' ) ? apply_filters( 'yith_wcan_query_vars', $query, $this ) : null;

			// return query.
			return $query;
		}

		/**
		 * Returns query param
		 *
		 * @return string Query param.
		 */
		public function get_query_param() {
			return $this->query_param;
		}

		/**
		 * Return array with details about currently active filters of a specific type, or false if the filter isn't active
		 *
		 * Array will contain details about the filter and the selected terms, as follows:
		 * [
		 *   'label' => 'Product Categories',         // Localized label for current filter
		 *   'values' => [                            // Each of the items active for current filter (most filter will only accepts one)
		 *      [
		 *         'label' => 'Accessories'           // Label of the item
		 *         'query_vars' => [                  // Query vars that describes this item (used to remove item from filters when needed)
		 *             'product_cat' => 'accessories,
		 *         ],
		 *      ],
		 *   ],
		 * ]
		 *
		 * @param string $filter Slug of the filter to describe.
		 *
		 * @return array|bool Array describing active filter, or false if filter isn't active
		 * @since 4.0.2
		 */
		public function get_active_filter( $filter ) {
			$query_vars    = $this->get_query_vars();
			$labels        = $this->get_supported_labels();
			$taxonomies    = $this->get_supported_taxonomies();
			$label         = isset( $labels[ $filter ] ) ? $labels[ $filter ] : false;
			$active_filter = false;

			if ( ! $label ) {
				return false;
			}

			if ( array_key_exists( $filter, $taxonomies ) ) {
				global $wp_query;

				$qo = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;

				$taxonomy = $filter;
				$filter   = str_replace( 'pa_', 'filter_', $filter );

				if ( ! isset( $query_vars[ $filter ] ) ) {
					return false;
				}
				$terms  = yith_wcan_separate_terms( $query_vars[ $filter ] );
				$values = array();

				if ( empty( $terms ) ) {
					return false;
				}

				foreach ( $terms as $term_slug ) {
					$term = get_term_by( 'slug', $term_slug, $taxonomy );

					if ( ! $term || apply_filters( 'yith_wcan_remove_current_term_from_active_filters', is_product_taxonomy() && $qo instanceof WP_Term && $qo->taxonomy === $taxonomy && $qo->slug === $term->slug, $term->slug, $qo, $taxonomy ) ) {
						continue;
					}

					$values[] = array(
						'label'      => $term->name,
						'query_vars' => array(
							$filter => yith_wcan_esc_term_slug( $term_slug ),
						),
					);
				}

				if ( empty( $values ) ) {
					return false;
				}

				$active_filter = array(
					'label'  => $label,
					'values' => $values,
				);
			} else {
				$active_filter = apply_filters( 'yith_wcan_active_filter', $active_filter, $filter, $query_vars, $labels );
			}

			return $active_filter;

		}

		/**
		 * Returns an array of active filters
		 *
		 * Format of the array will change depending on context param:
		 * 'edit' : will provide an internal filters description, as provided by \YITH_WCAN_Query::get_query_vars.
		 * 'view' : will provide a formatted description, to be used to print templates; this format will be as follows:
		 * [
		 *    'filter_slug' => [                          // Each active filter will be described by an array
		 *       'label' => 'Product Categories',         // Localized label for current filter
		 *       'values' => [                            // Each of the items active for current filter (most filter will only accepts one)
		 *          [
		 *             'label' => 'Accessories'           // Label of the item
		 *             'query_vars' => [                  // Query vars that describes this item (used to remove item from filters when needed)
		 *                 'product_cat' => 'accessories,
		 *             ],
		 *          ],
		 *       ],
		 *    ],
		 * ]
		 *
		 * @param string $context Type of expected result.
		 * @return array Result set.
		 */
		public function get_active_filters( $context = 'edit' ) {
			$query_vars = $this->get_query_vars();

			if ( 'edit' === $context ) {
				return $query_vars;
			} else {
				$active_filters = array();
				$labels         = $this->get_supported_labels();

				foreach ( $labels as $filter => $label ) {
					$active_filter = $this->get_active_filter( $filter );

					if ( ! $active_filter ) {
						continue;
					}

					$active_filters[] = $active_filter;
				}

				return apply_filters( 'yith_wcan_active_filter_labels', $active_filters, $query_vars );
			}
		}

		/**
		 * Retrieves a list of product ids that matches current query vars
		 *
		 * @return array Array of products ids.
		 */
		public function get_filtered_products() {
			return $this->get_filtered_products_by_query_vars();
		}

		/**
		 * Retrieves a list of product ids that matches passed query vars
		 *
		 * @param array|null $query_vars A list of query vars to use for product filtering (for the format check @see \YITH_WCAN_Query::get_query_vars).
		 *
		 * @return array Array of products ids.
		 */
		public function get_filtered_products_by_query_vars( $query_vars = null ) {
			$query_vars = ! is_null( $query_vars ) ? $query_vars : $this->get_query_vars();

			// WPML support.
			$current_lang = apply_filters( 'wpml_current_language', null );

			if ( ! ! $current_lang ) {
				$query_vars['lang'] = $current_lang;
			}

			$calculate_hash = md5( http_build_query( $query_vars ) );
			$product_ids    = YITH_WCAN_Cache_Helper::get( 'queried_products', $calculate_hash );

			if ( ! $product_ids ) {
				// store original query values, and switch to current context.
				$tmp_query_vars          = $this->query_vars;
				$tmp_chosen_attributes   = $this->chosen_attributes;
				$this->query_vars        = $query_vars;
				$this->chosen_attributes = null;

				// create query to retrieve products.
				$query = new WP_Query(
					apply_filters(
						'yith_wcan_filtered_products_query',
						array(
							'post_type'      => 'product',
							'post_status'    => 'publish',
							'posts_per_page' => '-1',
							'fields'         => 'ids',
						)
					)
				);

				// filter with current query vars.
				$this->filter( $query );

				// mark query as internal.
				$query->set( 'yith_wcan_prefetch_cache', true );

				// retrieve product ids for current filters.
				$product_ids = $query->get_posts();

				// save result set to stored queries.
				YITH_WCAN_Cache_Helper::set( 'queried_products', $product_ids, $calculate_hash );

				// reset original query values.
				$this->query_vars        = $tmp_query_vars;
				$this->chosen_attributes = $tmp_chosen_attributes;
			}

			return $product_ids;
		}

		/**
		 * Checks whether filters should be applied
		 *
		 * @return bool Whether filters should be applied.
		 */
		public function should_filter() {
			$query_param = isset( $_REQUEST[ $this->get_query_param() ] ) ? intval( wp_unslash( $_REQUEST[ $this->get_query_param() ] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			return apply_filters( 'yith_wcan_should_filter', ! ! $query_param, $this );
		}

		/**
		 * Checks whether passed Query object should be processed by filter
		 *
		 * @param WP_Query $query Query object.
		 * @return bool Whether object should be filtered or not.
		 */
		public function should_process_query( $query ) {
			$result = true;

			if ( ! $query instanceof WP_Query ) {
				// skip if wrong parameter.
				$result = false;
			} elseif ( ! in_array( 'product', (array) $query->get( 'post_type' ), true ) && ! in_array( $query->get( 'taxonomy' ), array_keys( $this->get_supported_taxonomies() ), true ) ) {
				// skip if we're not querying products.
				$result = false;
			} elseif ( $query->is_main_query() && ! $query->get( 'wc_query' ) ) {
				// skip if main query.
				$result = false;
			} elseif ( ! $query->is_main_query() && $query->get( 'wc_query' ) ) {
				// skip if we're already executing a special wc_query.
				$result = false;
			} elseif ( $query->get( 'yith_wcan_suppress_filters' ) ) {
				// skip if we're prefetching products.
				$result = false;
			}

			return apply_filters( 'yith_wcan_should_process_query', $result, $query, $this );
		}

		/* === QUERY METHODS === */

		/**
		 * Retrieve all defined query vars for current url, and set the for current query
		 *
		 * @param WP_Query $query Current query object.
		 *
		 * @return void
		 */
		public function fill_query_vars( &$query ) {
			$query_vars = apply_filters( 'yith_wcan_query_vars_to_merge', $this->get_query_vars() );

			if ( empty( $query_vars ) ) {
				return;
			}

			$query->query_vars = array_merge(
				$query->query_vars,
				$query_vars
			);
		}

		/**
		 * Start to filter the query
		 *
		 * @return void
		 */
		public function start_filtering() {
			// if we don't have plugin parameter, just skip.
			if ( ! $this->should_filter() ) {
				return;
			}

			// suppress conditional tags for global query, when we're executing a filter.
			$this->suppress_default_conditional_tags();

			// append handling to queries.
			add_action( 'pre_get_posts', array( $this, 'filter' ), 10, 1 );

			// append handling to product shortcodes.
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'filter_query_vars' ) );

			// during our filtering, WC blocks cannot use cached contents.
			add_filter( 'woocommerce_blocks_product_grid_is_cacheable', '__return_false' );

			// prevent redirect to product page while filtering a search page and getting a single result.
			add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
		}

		/**
		 * Filters query, and apply all additional query vars
		 *
		 * @param WP_Query $query Current query object.
		 *
		 * @return void
		 */
		public function filter( $query ) {
			// skip if query object shouldn't be processed.
			if ( ! $this->should_process_query( $query ) ) {
				return;
			}

			do_action( 'yith_wcan_before_query', $query, $this );

			// get tax query for current loop (even if we're on single).
			$this->fill_query_vars( $query );

			// set layered nav for current query.
			$this->set_tax_query( $query );
			$this->set_meta_query( $query );
			$this->set_orderby( $query );
			$this->set_post_in( $query );

			// set special meta for current query.
			$query->set( 'yith_wcan_query', $this->get_query_vars() );

			do_action( 'yith_wcan_after_query', $query, $this );
		}

		/**
		 * When we don't have a query object, we can pass query_var
		 *
		 * @param WP_Query|array $query Array of query vars, or query object.
		 * @return array Filtered array of query vars
		 */
		public function filter_query_vars( $query ) {
			if ( is_array( $query ) ) {
				$query = new WP_Query( $query );
			} elseif ( ! $query instanceof WP_Query ) {
				return $query;
			}

			// apply current filters.
			$this->filter( $query );

			// retrieve filtered query vars.
			$query_vars = $query->query_vars;

			// destroy new query object.
			unset( $query );

			return $query_vars;
		}

		/**
		 * Filters tax_query param of a query, to add parameters specified in $this->_query_vars
		 *
		 * @param array $tax_query Tax query array of current query.
		 *
		 * @return array Array describing meta query currently set in the query vars
		 */
		public function get_tax_query( $tax_query = array() ) {
			if ( ! is_array( $tax_query ) ) {
				$tax_query = array(
					'relation' => 'AND',
				);
			}

			// Layered nav filters on terms.
			foreach ( $this->get_layered_nav_chosen_attributes() as $taxonomy => $data ) {
				$tax_query[] = array(
					'taxonomy'         => $taxonomy,
					'field'            => 'slug',
					'terms'            => $data['terms'],
					'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
					'include_children' => false,
				);
			}

			// Set visibility tax_query.
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
				'operator' => 'NOT IN',
			);

			return array_filter( apply_filters( 'yith_wcan_product_query_tax_query', $this->reduce_tax_query( $tax_query ), $this ) );
		}

		/**
		 * Get an array of attributes and terms selected with the layered nav widget.
		 *
		 * @return array
		 */
		public function get_layered_nav_chosen_attributes() {
			if ( ! is_array( $this->chosen_attributes ) ) {
				$this->chosen_attributes = array();

				$query_vars = $this->get_query_vars();

				if ( ! empty( $query_vars ) ) {
					foreach ( $query_vars as $key => $value ) {
						if ( 0 === strpos( $key, 'filter_' ) ) {
							$attribute    = wc_sanitize_taxonomy_name( str_replace( 'filter_', '', $key ) );
							$taxonomy     = wc_attribute_taxonomy_name( $attribute );
							$filter_terms = ! empty( $value ) ? explode( ',', wc_clean( wp_unslash( $value ) ) ) : array();

							if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! wc_attribute_taxonomy_id_by_name( $attribute ) ) {
								continue;
							}

							$query_type = $this->get( 'query_type_' . $attribute );

							$this->chosen_attributes[ $taxonomy ]['terms']      = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
							$this->chosen_attributes[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
						}
					}
				}
			}
			return $this->chosen_attributes;
		}

		/**
		 * Set tax_query parameter according to current query_vars, for the passed query object
		 *
		 * @param WP_Query $query Query object to modify.
		 * @return void
		 */
		public function set_tax_query( &$query ) {
			// get tax_query for current query.
			$tax_query = $query->get( 'tax_query' );

			// add layered nav parameters.
			$tax_query = $this->get_tax_query( $tax_query );

			// remove any default taxonomy filtering, if we've set a tax query.
			if ( ! empty( $tax_query ) ) {
				$query->set( 'taxonomy', '' );
				$query->set( 'term', '' );
			}

			// finally set tax_query parameter for the query.
			$query->set( 'tax_query', $tax_query );
		}

		/**
		 * This method is just a placeholder, that will always return the passed parameter
		 * It was included within the plugin for future developments.
		 *
		 * @param array $meta_query Meta query array of current query.
		 *
		 * @return array Array describing meta query currently set in the query vars
		 */
		public function get_meta_query( $meta_query = array() ) {
			return $meta_query;
		}

		/**
		 * Set meta_query parameter according to current query_vars, for the passed query object
		 *
		 * @param WP_Query $query Query object to modify.
		 * @return void
		 */
		public function set_meta_query( &$query ) {
			// get meta_query for current query.
			$meta_query = $query->get( 'meta_query' );

			// add layered nav parameters.
			$meta_query = $this->get_meta_query( $meta_query );

			// finally set meta_query parameter for the query.
			$query->set( 'meta_query', $meta_query );
		}

		/**
		 * This method is just a placeholder, that will always return false
		 * It was included within the plugin for future developments.
		 *
		 * @return array|bool Query's ordering parameters, or false when no ordering is required.
		 */
		public function get_orderby() {
			return false;
		}

		/**
		 * Set order parameters according to current query_vars, for the passed query object
		 *
		 * @param WP_Query $query Query object to modify.
		 * @return void
		 */
		public function set_orderby( &$query ) {
			$orderby = $this->get( 'orderby' );

			if ( ! $orderby ) {
				return;
			}

			/**
			 * Same behaviour WC applies to main query
			 *
			 * @see \WC_Query::product_query
			 */
			$ordering = $this->get_orderby();

			if ( ! $ordering ) {
				return;
			}

			$query->set( 'orderby', $ordering['orderby'] );
			$query->set( 'order', $ordering['order'] );

			if ( isset( $ordering['meta_key'] ) ) {
				$query->set( 'meta_key', $ordering['meta_key'] );
			}
		}

		/**
		 * This method is just a placeholder, that will always return false
		 * It was included within the plugin for future developments.
		 *
		 * @param array $post_in Post__in for current query.
		 *
		 * @return array|bool Query's post__in, or false when no limitation shall be applied.
		 */
		public function get_post_in( $post_in = array() ) {
			return apply_filters( 'yith_wcan_query_post_in', $post_in );
		}

		/**
		 * Set post__in parameter according to current query_vars, for the passed query object
		 *
		 * @param WP_Query $query Query object to modify.
		 * @return void
		 */
		public function set_post_in( &$query ) {
			$post_in = $this->get_post_in( $query->get( 'post__in', array() ) );

			if ( empty( $post_in ) ) {
				return;
			}

			$query->set( 'post__in', $post_in );
		}

		/**
		 * Suppresses default query vars when filtering on home page
		 * That's done to avoid WP loading terms as queried objects, when filtering the home page
		 *
		 * @return void
		 */
		public function suppress_default_query_vars() {
			global $wp;

			if ( empty( $wp->request ) && $this->should_filter() && get_option( 'permalink_structure' ) && apply_filters( 'yith_wcan_suppress_default_query_vars', true ) ) {
				$wp->query_vars = array();
			}
		}

		/**
		 * Suppress conditional tags for current global query
		 *
		 * This should only be done when filtering (\YITH_WCAN_Query::should_filter) and is shop page.
		 * Otherwise system could set query to behave like we're on a category/tag/etc, depending on query_vars.
		 *
		 * @return void
		 */
		public function suppress_default_conditional_tags() {
			global $wp_query;

			if ( apply_filters( 'yith_wcan_suppress_default_conditional_tags', false ) ) {
				$wp_query->is_tax        = false;
				$wp_query->is_tag        = false;
				$wp_query->is_home       = false;
				$wp_query->is_single     = false;
				$wp_query->is_posts_page = false;
			}
		}

		/**
		 * Suppress canonical redirect when filtering homepage with session param
		 *
		 * @param bool $redirect Whether to redirect to canonical url.
		 * @return bool Filtered value.
		 */
		public function suppress_canonical_redirect( $redirect ) {
			if ( $this->should_filter() ) {
				$redirect = false;
			}

			return $redirect;
		}

		/**
		 * Register an array of filtered products
		 *
		 * @return void
		 */
		public function register_products() {
			if ( ! $this->is_filtered() || ! empty( $this->products ) || ! apply_filters( 'yith_wcan_process_filters_intersection', true ) ) {
				return;
			}

			$this->products = $this->get_filtered_products_by_query_vars();
		}

		/* === ALTER DEFAULT WC QUERY === */

		/**
		 * Set custom filtering for default WC query, for those parameters specific to our plugin
		 *
		 * @param WP_Query $query Query object.
		 * @return void
		 */
		public function alter_product_query( $query ) {
			$this->set_post_in( $query );
		}

		/* === FILTER URL METHODS === */

		/**
		 * Get url for filtering.
		 *
		 * @param array  $query_to_add    Params to add to the url (additionally to the existing ones).
		 * @param array  $query_to_remove Params to remove from the url (from the one already existing).
		 * @param string $merge_mode      Whether params should be added or removed using AND or OR method, when applicable.
		 *
		 * @return string Url for filtering.
		 */
		public function get_filter_url( $query_to_add = array(), $query_to_remove = array(), $merge_mode = 'and' ) {
			if ( ! did_action( 'wp' ) ) {
				return '';
			}

			$query_vars = $this->get_query_vars();
			$base_url   = $this->get_base_filter_url();

			if ( ! empty( $query_to_add ) ) {
				$query_vars = $this->merge_query_vars( $query_vars, $merge_mode, $query_to_add );
			}

			if ( ! empty( $query_to_remove ) ) {
				$query_vars = $this->diff_query_vars( $query_vars, $merge_mode, $query_to_remove );
			}

			$params = array_merge(
				array(
					$this->get_query_param() => 1,
				),
				$query_vars
			);

			return apply_filters( 'yith_wcan_filter_url', add_query_arg( $params, $base_url ), $query_vars, $merge_mode );
		}

		/**
		 * Returns base url for the filters (it will return current page url, or product archive url when in shop page)
		 *
		 * @return string Base filtering url.
		 */
		public function get_base_filter_url() {
			global $wp;

			if ( is_shop() ) {
				$base_url = yit_get_woocommerce_layered_nav_link();
			} else {
				$base_url = home_url( $wp->request );
			}

			return apply_filters( 'yith_wcan_base_filter_url', $base_url );
		}

		/* === TEST METHODS === */

		/**
		 * Checks whether current view is applying any filter over eligible queries
		 *
		 * @return bool
		 */
		public function is_filtered() {
			return apply_filters( 'yith_wcan_is_filtered', $this->should_filter() && ! empty( $this->get_query_vars() ) );
		}

		/**
		 * Returns true iw we're filtering for the specific parameter passed as argument
		 *
		 * @param string $param Parameter to search among query vars.
		 * @return bool Whether we're filtering for passed argument or not.
		 */
		public function is_filtered_by( $param ) {
			$query_vars = $this->get_query_vars();

			if ( 'tax' === $param ) {
				$taxonomies = array_keys( $this->get_supported_taxonomies() );

				foreach ( $taxonomies as & $taxonomy ) {
					$taxonomy = str_replace( 'pa_', 'filter_', $taxonomy );
				}

				return is_tax() || ! ! array_intersect( array_keys( $query_vars ), $taxonomies );
			}

			$query_var = $param;

			if ( in_array( $param, wc_get_attribute_taxonomy_names(), true ) ) {
				$query_var = str_replace( 'pa_', 'filter_', $param );
			}

			return is_tax( $query_var ) || array_key_exists( $query_var, $query_vars );
		}

		/**
		 * Checks whether we're currently filtering for a specific term, or if we're that term page
		 *
		 * @param string  $taxonomy Taxonomy to test.
		 * @param WP_Term $term     Term to test.
		 * @return bool Whether we're filtering by that term or not.
		 */
		public function is_term( $taxonomy, $term ) {
			$taxonomies = array_keys( $this->get_supported_taxonomies() );
			$query_var  = $taxonomy;

			if ( ! in_array( $taxonomy, $taxonomies, true ) ) {
				return false;
			}

			if ( is_tax( $taxonomy, $term->slug ) ) {
				return true;
			}

			if ( in_array( $taxonomy, wc_get_attribute_taxonomy_names(), true ) ) {
				$query_var = str_replace( 'pa_', 'filter_', $taxonomy );
			}

			$terms = $this->get( $query_var, '' );
			$terms = yith_wcan_separate_terms( $terms );

			return in_array( $term->slug, $terms, true );
		}

		/* === RETRIEVE QUERY-RELEVANT PRODUCTS === */

		/**
		 * Count how many products for the passed term match current filter
		 *
		 * @param string $taxonomy       Taxonomy to test.
		 * @param int    $term_id        Term id to test.
		 * @param bool   $auto_exclusive Whether we should exclude passed taxonomy from query_vars for filtering.
		 *
		 * @return bool|int Count of matching products, or false on failure
		 */
		public function count_query_relevant_term_objects( $taxonomy, $term_id, $auto_exclusive = true ) {
			if ( ! apply_filters( 'yith_wcan_process_filters_intersection', true ) ) {
				return false;
			}

			return count( $this->get_query_relevant_term_objects( $taxonomy, $term_id, $auto_exclusive ) );
		}

		/**
		 * Return ids for term's products matching current filter
		 *
		 * @param string $taxonomy         Taxonomy to test.
		 * @param int    $term_id          Term id to test.
		 * @param bool   $exclude_taxonomy Whether we should exclude passed taxonomy from query_vars for filtering.
		 *
		 * @return array Array of post ids that are both query-relevant and bound to term
		 */
		public function get_query_relevant_term_objects( $taxonomy, $term_id, $exclude_taxonomy = true ) {
			if ( ! apply_filters( 'yith_wcan_process_filters_intersection', true ) ) {
				return array();
			}

			if ( isset( $this->products_per_filter[ $taxonomy ][ $term_id ] ) ) {
				return $this->products_per_filter[ $taxonomy ][ $term_id ];
			} else {
				$posts = YITH_WCAN_Cache_Helper::get( 'object_in_terms', $term_id );

				if ( ! $posts ) {
					$posts = get_objects_in_term( $term_id, $taxonomy );

					// save result set to stored queries.
					YITH_WCAN_Cache_Helper::set( 'object_in_terms', $posts, $term_id );
				}

				if ( is_wp_error( $posts ) ) {
					return array();
				}

				if ( ! $exclude_taxonomy ) {
					$query_vars        = $this->get_query_vars();
					$original_taxonomy = $taxonomy;

					if ( in_array( $original_taxonomy, wc_get_attribute_taxonomy_names(), true ) ) {
						$original_taxonomy = str_replace( 'pa_', 'filter_', $original_taxonomy );
					}

					if ( isset( $query_vars[ $original_taxonomy ] ) ) {
						unset( $query_vars[ $original_taxonomy ] );
					}

					$products = $this->get_filtered_products_by_query_vars( $query_vars );
				} else {
					$products = $this->get_filtered_products();
				}

				$match = array_intersect( $posts, $products );

				$this->products_per_filter[ $taxonomy ][ $term_id ] = $match;

				return $match;
			}
		}

		/* === UTILS === */

		/**
		 * Retrieves list of ids of in-stock products
		 *
		 * @return array Array of product ids
		 */
		public function get_product_ids_in_stock() {

			// Load from cache.
			$product_ids_in_stock = YITH_WCAN_Cache_Helper::get( 'products_instock' );

			// Valid cache found.
			if ( false !== $product_ids_in_stock ) {
				return $product_ids_in_stock;
			}

			$product_ids_in_stock = wc_get_products(
				apply_filters(
					'yith_wcan_product_ids_in_stock_args',
					array(
						'status'                     => 'publish',
						'stock_status'               => 'instock',
						'limit'                      => - 1,
						'return'                     => 'ids',
						'yith_wcan_suppress_filters' => true,
					)
				)
			);

			YITH_WCAN_Cache_Helper::set( 'products_instock', $product_ids_in_stock );

			return $product_ids_in_stock;
		}

		/**
		 * Retrieves list of ids of in-stock products
		 *
		 * @return array Array of product ids
		 */
		public function get_product_ids_on_sale() {
			return wc_get_product_ids_on_sale();
		}

		/**
		 * Retrieves list of ids of featured products
		 *
		 * @return array Array of product ids
		 */
		public function get_product_ids_featured() {
			return wc_get_featured_product_ids();
		}

		/**
		 * Merge sets of query vars together; when applicable, uses merge mode to merge parameters together
		 *
		 * @param array  $query_vars     Initial array of parameters.
		 * @param string $merge_mode     Merge mode (AND/OR).
		 * @param array  ...$vars_to_add Additional sets of params to merge.
		 *
		 * @return array Merged parameters.
		 */
		public function merge_query_vars( $query_vars, $merge_mode, ...$vars_to_add ) {
			$supported_taxonomies = $this->get_supported_taxonomies();

			if ( ! empty( $vars_to_add ) ) {
				foreach ( $vars_to_add as $vars ) {
					foreach ( $vars as $key => $value ) {
						if ( in_array( $key, array_keys( $supported_taxonomies ), true ) ) {
							if ( ! isset( $query_vars[ $key ] ) ) {
								$query_vars[ $key ] = $value;
							} else {
								$glue     = 'and' === $merge_mode ? '+' : ',';
								$existing = explode( $glue, $query_vars[ $key ] );
								$new      = explode( $glue, $value );

								$query_vars[ $key ] = implode( $glue, array_unique( array_merge( $existing, $new ) ) );
							}
						} elseif ( 0 === strpos( $key, 'filter_' ) ) {
							$attribute = str_replace( 'filter_', '', $key );

							$query_vars[ "query_type_{$attribute}" ] = $merge_mode;

							if ( ! isset( $query_vars[ $key ] ) ) {
								$query_vars[ $key ] = $value;
							} else {
								$existing = explode( ',', $query_vars[ $key ] );
								$new      = explode( ',', $value );

								$query_vars[ $key ] = implode( ',', array_unique( array_merge( $existing, $new ) ) );
							}
						} elseif ( is_array( $value ) ) {
							$glue               = 'and' === $merge_mode ? '+' : ',';
							$query_vars[ $key ] = implode( $glue, array_merge( isset( $query_vars[ $key ] ) ? (array) $query_vars[ $key ] : array(), $value ) );
						} else {
							$query_vars[ $key ] = $value;
						}
					}
				}
			}

			return $query_vars;
		}

		/**
		 * Diff sets of query vars together; when applicable, uses merge mode to diff parameters apart
		 *
		 * @param array  $query_vars        Initial array of parameters.
		 * @param string $merge_mode        Merge mode (AND/OR).
		 * @param array  ...$vars_to_remove Additional sets of params to diff.
		 *
		 * @return array Merged parameters.
		 */
		public function diff_query_vars( $query_vars, $merge_mode, ...$vars_to_remove ) {
			$supported_taxonomies = $this->get_supported_taxonomies();

			if ( ! empty( $vars_to_remove ) ) {
				foreach ( $vars_to_remove as $vars ) {
					foreach ( $vars as $key => $value ) {
						if ( in_array( $key, array_keys( $supported_taxonomies ), true ) ) {
							if ( isset( $query_vars[ $key ] ) ) {
								$glue     = 'and' === $merge_mode ? '+' : ',';
								$existing = explode( $glue, $query_vars[ $key ] );
								$new      = explode( $glue, $value );

								$query_vars[ $key ] = implode( $glue, array_unique( array_diff( $existing, $new ) ) );
							}

							if ( empty( $query_vars[ $key ] ) ) {
								unset( $query_vars[ $key ] );
							}
						} elseif ( 0 === strpos( $key, 'filter_' ) ) {
							$attribute = str_replace( 'filter_', '', $key );

							$query_vars[ "query_type_{$attribute}" ] = $merge_mode;

							if ( isset( $query_vars[ $key ] ) ) {
								$existing = explode( ',', $query_vars[ $key ] );
								$new      = explode( ',', $value );

								$query_vars[ $key ] = implode( ',', array_unique( array_diff( $existing, $new ) ) );
							}

							if ( empty( $query_vars[ $key ] ) ) {
								unset( $query_vars[ $key ] );
								unset( $query_vars[ "query_type_{$attribute}" ] );
							}
						} elseif ( is_array( $value ) ) {
							$glue               = 'and' === $merge_mode ? '+' : ',';
							$query_vars[ $key ] = implode( $glue, array_diff( isset( $query_vars[ $key ] ) ? (array) $query_vars[ $key ] : array(), $value ) );

							if ( empty( $query_vars[ $key ] ) ) {
								unset( $query_vars[ $key ] );
							}
						} else {
							unset( $query_vars[ $key ] );
						}
					}
				}
			}

			return $query_vars;
		}

		/**
		 * Sanitize query vars coming from $_GET
		 *
		 * @param array $query Array of parameters coming from request.
		 *
		 * @return array Array of sanitized parameters.
		 * @since 4.0.2
		 */
		public function sanitize_query( $query ) {
			// perform basic sanitization.
			$query = array_map(
				function ( $string ) {
					if ( is_scalar( $string ) ) {
						$string = str_replace( ' ', '+', $string );
					}

					return wc_clean( $string );
				},
				$query
			);

			return $query;
		}

		/**
		 * Merges together tax queries whenever possible, to avoid performing queries more complex than needed
		 *
		 * @param array $tax_queries Array of tax queries.
		 * @return array Filtered array of tax queries.
		 */
		public function reduce_tax_query( $tax_queries ) {
			$pre = apply_filters( 'yith_wcan_pre_reduce_tax_query', false, $tax_queries );

			if ( false !== $pre ) {
				return $pre;
			}

			$result   = array();
			$queries  = array();
			$relation = 'AND';

			if ( isset( $tax_queries['relation'] ) ) {
				$relation = $tax_queries['relation'];
				unset( $tax_queries['relation'] );
			}

			if ( empty( $tax_queries ) ) {
				return $result;
			}

			foreach ( $tax_queries as $tax_query ) {
				if ( ! is_array( $tax_query ) ) {
					continue;
				}

				$taxonomy = isset( $tax_query['taxonomy'] ) ? $tax_query['taxonomy'] : false;
				$operator = isset( $tax_query['operator'] ) ? $tax_query['operator'] : 'IN';
				$field    = isset( $tax_query['field'] ) ? $tax_query['field'] : 'term_id';
				$children = isset( $tax_query['include_children'] ) ? $tax_query['include_children'] : true;

				$hash = "{$taxonomy}_{$operator}_{$field}_{$children}";

				if ( ! isset( $queries[ $hash ] ) ) {
					$queries[ $hash ] = $tax_query;
				} elseif ( ! empty( $tax_query['terms'] ) ) {
					$queries[ $hash ]['terms'] = array_unique( (array) $queries[ $hash ]['terms'] + (array) $tax_query['terms'] );
				}
			}

			$result = array_merge(
				array(
					'relation' => $relation,
				),
				array_values( $queries )
			);

			return $result;
		}

		/**
		 * Query class Instance
		 *
		 * @return YITH_WCAN_Query Query class instance
		 */
		public static function instance() {
			if ( is_null( static::$instance ) ) {
				static::$instance = new static();
			}

			return static::$instance;
		}
	}
}

if ( ! function_exists( 'YITH_WCAN_Query' ) ) {
	/**
	 * Returns single instance of YITH_WCAN_Query class
	 *
	 * @return YITH_WCAN_Query
	 */
	function YITH_WCAN_Query() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		if ( defined( 'YITH_WCAN_PREMIUM' ) ) {
			return YITH_WCAN_Query_Premium::instance();
		} elseif ( defined( 'YITH_WCAN_EXTENDED' ) ) {
			return YITH_WCAN_Query_Extended::instance();
		}

		return YITH_WCAN_Query::instance();
	}
}
