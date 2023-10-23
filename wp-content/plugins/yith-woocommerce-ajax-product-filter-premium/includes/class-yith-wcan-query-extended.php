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

if ( ! class_exists( 'YITH_WCAN_Query_Extended' ) ) {
	/**
	 * Query Handling
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Query_Extended extends YITH_WCAN_Query {

		/**
		 * Current filtering session, if any
		 *
		 * @var YITH_WCAN_Session
		 */
		protected $session = null;

		/**
		 * Constructor method for the class
		 */
		public function __construct() {
			// session handling.
			add_action( 'request', array( $this, 'prefetch_session' ) );

			// additional query handling.
			add_filter( 'yith_wcan_filtered_products_query', array( $this, 'check_in_stock_products' ) );

			parent::__construct();
		}

		/* === GET METHODS === */

		/**
		 * Retrieves currently set query vars
		 *
		 * @return array Array of retrieved query vars; expected format: [
		 *     <product_taxonomy> => list of terms separated by , (OR) or by + (AND)
		 *     filter_<product_attribute> => list of terms separated by ,
		 *     meta_<meta_key> => meta value, eventually prefixed by operator (<,>, <=, >=, !=, IN, NOTIN)
		 *     query_type_<product_attribute> => and/or,
		 *     price_ranges => list of price rages (as <min>-<max>) separated by ,
		 *     min_price => float,
		 *     max_price => float,
		 *     rating_filter => list of int separated by ,
		 *     orderby => string,
		 *     order => string,
		 *     onsale_filter => bool,
		 *     instock_filter => bool,
		 *     featured_filter => bool,
		 * ]
		 */
		public function get_query_vars() {
			if ( ! is_null( $this->query_vars ) ) {
				return $this->query_vars;
			}

			$session = $this->maybe_retrieve_current_session();

			if ( $session ) {
				$query = $session->get_query_vars();
			} else {
				$query = $this->sanitize_query( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				// unset parameters that aren't related to filters.
				$supported_parameters = apply_filters(
					'yith_wcan_query_supported_parameters',
					array_merge(
						array(
							's',
							'price_ranges',
							'min_price',
							'max_price',
							'rating_filter',
							'orderby',
							'order',
							'onsale_filter',
							'instock_filter',
							'featured_filter',
						),
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
				global $wp_query;

				if ( $wp_query instanceof WP_Query && is_product_taxonomy() ) {
					$qo = $wp_query->get_queried_object();

					if ( $qo instanceof WP_Term && ! isset( $query[ $qo->taxonomy ] ) ) {
						$query[ $qo->taxonomy ] = $qo->slug;
					}
				}
			}

			/**
			 * We only store _query_vars once main query is executed, to be sure not to left behind any parameter.
			 *
			 * @since 4.1.1
			 */
			$this->query_vars = did_action( 'wp' ) ? apply_filters( 'yith_wcan_query_vars', $query, $this ) : null;

			// if current query set isn't provided by a session, try to register one.
			if ( ! $session && $this->query_vars ) {
				$this->maybe_register_current_session( $this->get_base_filter_url(), $this->query_vars );
			}

			// return query.
			return $query;
		}

		/**
		 * Checks whether filters should be applied
		 *
		 * @return bool Whether filters should be applied.
		 */
		public function should_filter() {
			if ( parent::should_filter() ) {
				return true;
			}

			return apply_filters( 'yith_wcan_should_filter', ! ! YITH_WCAN_Session_Factory::get_session_query_var(), $this );
		}

		/* === QUERY METHODS === */

		/**
		 * This method is just a placeholder, that will always return false
		 * It was included within the plugin for future developments.
		 *
		 * @param array $post_in Post_in for current query.
		 *
		 * @return array|bool Query's post__in, or false when no limitation shall be applied.
		 */
		public function get_post_in( $post_in = array() ) {
			if ( 'yes' === yith_wcan_get_option( 'yith_wcan_hide_out_of_stock_products', 'no' ) ) {
				$in_stock = $this->get_product_ids_in_stock();
				$post_in  = $post_in ? array_intersect( $post_in, $in_stock ) : $in_stock;
			}

			return apply_filters( 'yith_wcan_query_post_in', $post_in );
		}

		/**
		 * Adds in stock status to the query
		 *
		 * @param  array $args array of arguments for the query.
		 * @return array
		 */
		public function check_in_stock_products( $args ) {
			if ( 'yes' === yith_wcan_get_option( 'yith_wcan_hide_out_of_stock_products', 'no' ) ) {
				$args['post__in'] = $this->get_product_ids_in_stock();
			}
			return $args;
		}

		/* === SESSION METHODS === */

		/**
		 * Returns current filtering session
		 *
		 * @retun YITH_WCAN_Session|bool Current filtering session, or false when no session is defined
		 */
		public function get_current_session() {
			return $this->session;
		}

		/**
		 * Returns sharing url for current filtering session
		 *
		 * @retun string|bool Sharing url, or false when no session is defined
		 */
		public function get_current_session_share_url() {
			$session = $this->session;

			if ( ! $session ) {
				return false;
			}

			return $session->get_share_url();
		}

		/**
		 * Retrieves current filtering session, if any
		 *
		 * Used to populate query vars from session.
		 *
		 * @return YITH_WCAN_Session|bool Current filter session; false if no session is found, or is sessions are disabled.
		 */
		public function maybe_retrieve_current_session() {
			$filter_by_session = 'custom' === yith_wcan_get_option( 'yith_wcan_change_browser_url' );
			$sessions_enabled  = apply_filters( 'yith_wcan_sessions_enabled', $filter_by_session );

			if ( ! $sessions_enabled ) {
				return false;
			}

			if ( $this->session ) {
				return $this->session;
			}

			$session = YITH_WCAN_Session_Factory::get_current_session();

			if ( $session ) {
				$session->maybe_extend_duration() && $session->save();
				$this->session = $session;
			}

			return $session;
		}

		/**
		 * Register current session, when needed
		 *
		 * @param string $origin_url Filtering url.
		 * @param array  $query_vars Filter parameters.
		 *
		 * @return void
		 */
		public function maybe_register_current_session( $origin_url, $query_vars ) {
			$filter_by_session = 'custom' === yith_wcan_get_option( 'yith_wcan_change_browser_url' );
			$sessions_enabled  = apply_filters( 'yith_wcan_sessions_enabled', $filter_by_session );

			if ( ! $sessions_enabled || ! $origin_url || ! $query_vars ) {
				return;
			}

			$this->session = YITH_WCAN_Session_Factory::generate_session( $origin_url, $query_vars );
		}

		/**
		 * Retrieves current session
		 *
		 * It is important to do this early in the execution to affect also archives main query
		 * It will also modify $_GET super-global, adding query vars retrieved from the session, in order to make
		 * them available to filtering systems (including WC's layered nav) down the line.
		 *
		 * @param array $query_vars Query variables parsed by WP for current request.
		 *
		 * @return array Array of parsed query variables.
		 */
		public function prefetch_session( $query_vars ) {
			$session = $this->maybe_retrieve_current_session();

			// if any session is found, merge its query-vars with $_GET (for layered nav) and with WP query-vars, for main query generation.
			if ( $session ) {
				$_GET       = array_merge( $_GET, $session->get_query_vars() ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query_vars = array_merge( $query_vars, $session->get_query_vars() );
			}

			return $query_vars;
		}
	}
}
