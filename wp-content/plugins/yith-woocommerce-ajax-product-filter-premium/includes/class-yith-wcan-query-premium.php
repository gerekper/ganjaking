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

if ( ! class_exists( 'YITH_WCAN_Query_Premium' ) ) {
	/**
	 * Query Handling
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Query_Premium extends YITH_WCAN_Query_Extended {

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
			add_action( 'posts_clauses', array( $this, 'price_ranges_handling' ), 10, 2 );
			add_action( 'yith_wcan_after_query', array( $this, 'additional_query_handling' ) );
			add_filter( 'yith_wcan_filtered_products_query', array( $this, 'check_in_stock_products' ) );

			parent::__construct();
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

			$labels = apply_filters(
				'yith_wcan_query_supported_labels',
				array_merge(
					array(
						'price_range'     => _x( 'Price', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
						'orderby'         => _x( 'Order by', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
						'rating_filter'   => _x( 'Rating', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
						'onsale_filter'   => _x( 'On sale', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
						'instock_filter'  => _x( 'In stock', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
						'featured_filter' => _x( 'Featured', '[FRONTEND] Active filter labels', 'yith-woocommerce-ajax-navigation' ),
					),
					wp_list_pluck( $taxonomies, 'label' )
				)
			);

			return $labels;
		}

		/**
		 * Returns an array of supported taxonomies for filtering
		 *
		 * @return WP_Taxonomy[] Array of WP_Taxonomy objects
		 */
		public function get_supported_taxonomies() {
			$this->_supported_taxonomies = apply_filters( 'yith_wcan_pre_get_supported_taxonomies', $this->supported_taxonomies );

			if ( empty( $this->_supported_taxonomies ) ) {
				$product_taxonomies   = get_object_taxonomies( 'product', 'objects' );
				$supported_taxonomies = array();
				$excluded_taxonomies  = apply_filters(
					'yith_wcan_excluded_taxonomies',
					array(
						'product_type',
						'product_visibility',
						'product_shipping_class',
					)
				);

				if ( ! empty( $product_taxonomies ) ) {
					foreach ( $product_taxonomies as $taxonomy_slug => $taxonomy ) {
						if ( in_array( $taxonomy_slug, $excluded_taxonomies, true ) ) {
							continue;
						}

						$supported_taxonomies[ $taxonomy_slug ] = $taxonomy;
					}
				}

				$this->_supported_taxonomies = apply_filters( 'yith_wcan_supported_taxonomies', $supported_taxonomies );
			}

			return $this->_supported_taxonomies;
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
			$query_vars = $this->get_query_vars();
			$labels     = $this->get_supported_labels();
			$label      = isset( $labels[ $filter ] ) ? $labels[ $filter ] : false;
			$values     = array();

			if ( ! $label ) {
				return false;
			}

			switch ( $filter ) {
				case 'price_range':
					if ( ! isset( $query_vars['min_price'] ) && ! isset( $query_vars['max_price'] ) && ! isset( $query_vars['price_ranges'] ) ) {
						return false;
					}

					$formatted_ranges = $this->get_active_price_ranges();

					if ( empty( $formatted_ranges ) ) {
						break;
					}

					foreach ( $formatted_ranges as $range ) {
						if ( $range['max_price'] ) {
							$range_label = sprintf(
							// translators: 1. Formatted min price of the range. 2. Formatted max price of the range.
								_x( '%1$s - %2$s', '[FRONTEND] Active price filter label', 'yith-woocommerce-ajax-navigation' ),
								isset( $range['min_price'] ) ? wc_price( $range['min_price'] ) : wc_price( 0 ),
								isset( $range['max_price'] ) ? wc_price( $range['max_price'] ) : '-'
							);
						} else {
							$range_label = sprintf(
							// translators: 1. Formatted min price of the range. 2. Formatted max price of the range.
								_x( '%1$s & above', '[FRONTEND] Active price filter label', 'yith-woocommerce-ajax-navigation' ),
								isset( $range['min_price'] ) ? wc_price( $range['min_price'] ) : wc_price( 0 )
							);
						}

						$values[] = array(
							'label'      => $range_label,
							'query_vars' => array(
								'min_price' => ! empty( $range['min_price'] ) ? $range['min_price'] : 0,
								'max_price' => ! empty( $range['max_price'] ) ? $range['max_price'] : 0,
							),
						);
					}

					$active_filter = array(
						'label'  => $label,
						'values' => $values,
					);

					break;
				case 'orderby':
					$supported_orders = YITH_WCAN_Filter_Factory::get_supported_orders();

					if ( ! isset( $query_vars['orderby'] ) ) {
						return false;
					}

					$orderby = $query_vars['orderby'];

					if ( ! empty( $query_vars['order'] ) ) {
						$orderby = "{$orderby}-{$query_vars['order']}";
					}

					if ( ! array_key_exists( $orderby, $supported_orders ) ) {
						return false;
					}

					$active_filter = array(
						'label'  => $label,
						'values' => array(
							array(
								'label'      => $supported_orders[ $orderby ],
								'query_vars' => array(
									'orderby' => $orderby,
								),
							),
						),
					);

					break;
				case 'rating_filter':
					if ( ! isset( $query_vars['rating_filter'] ) ) {
						return false;
					}

					$current_rating = explode( ',', $query_vars['rating_filter'] );
					$values         = array();

					foreach ( $current_rating as $rate ) {
						$values[] = array(
							'label'      => wc_get_rating_html( $rate ),
							'query_vars' => array(
								'rating_filter' => $rate,
							),
						);
					}

					$active_filter = array(
						'label'  => $label,
						'values' => $values,
					);

					break;
				case 'onsale_filter':
					if ( ! isset( $query_vars['onsale_filter'] ) ) {
						return false;
					}

					$active_filter = array(
						'label'  => $label,
						'values' => array(
							array(
								'label'      => $label,
								'query_vars' => array(
									'onsale_filter' => 1,
								),
							),
						),
					);

					break;
				case 'instock_filter':
					if ( ! isset( $query_vars['instock_filter'] ) ) {
						return false;
					}

					$active_filter = array(
						'label'  => $label,
						'values' => array(
							array(
								'label'      => $label,
								'query_vars' => array(
									'instock_filter' => 1,
								),
							),
						),
					);

					break;
				case 'featured_filter':
					if ( ! isset( $query_vars['featured_filter'] ) ) {
						return false;
					}

					$active_filter = array(
						'label'  => $label,
						'values' => array(
							array(
								'label'      => $label,
								'query_vars' => array(
									'featured_filter' => 1,
								),
							),
						),
					);

					break;
				default:
					$active_filter = parent::get_active_filter( $filter );
					break;
			}

			return $active_filter;

		}

		/**
		 * Retrieves currently active price ranges
		 *
		 * @return array Array of active price ranges, formatted as follows
		 * [
		 *     'min_price' => 0 (float)
		 *     'max_price' => 0 (float, optional)
		 * ]
		 */
		public function get_active_price_ranges() {
			$query_vars       = $this->get_query_vars();
			$formatted_ranges = array();

			if ( isset( $query_vars['price_ranges'] ) ) {
				$ranges = explode( ',', $query_vars['price_ranges'] );

				foreach ( $ranges as $range ) {
					$range_limits = explode( '-', $range );

					if ( ! isset( $range_limits[0] ) ) {
						continue;
					}

					$min_price = (float) $range_limits[0];
					$max_price = ! empty( $range_limits[1] ) ? (float) $range_limits[1] : false;

					$formatted_ranges[] = compact( 'min_price', 'max_price' );
				}
			} elseif ( isset( $query_vars['max_price'] ) ) {
				$formatted_ranges[] = array(
					'min_price' => isset( $query_vars['min_price'] ) ? (float) $query_vars['min_price'] : (float) 0,
					'max_price' => (float) $query_vars['max_price'],
				);
			} elseif ( isset( $query_vars['min_price'] ) ) {
				$formatted_ranges[] = array(
					'min_price' => (float) $query_vars['min_price'],
					'max_price' => false,
				);
			}

			return $formatted_ranges;
		}

		/* === QUERY METHODS === */

		/**
		 * Filters tax_query param of a query, to add parameters specified in $this->_query_vars
		 *
		 * @param array $tax_query Tax query array of current query.
		 *
		 * @return array Array describing meta query currently set in the query vars
		 */
		public function get_tax_query( $tax_query = array() ) {
			$tax_query = parent::get_tax_query( $tax_query );

			// Filter by rating.
			$rating_filter            = $this->get( 'rating_filter' );
			$product_visibility_terms = wc_get_product_visibility_term_ids();

			if ( $rating_filter ) {
				$rating_filter = array_filter( array_map( 'absint', explode( ',', $rating_filter ) ) );
				$rating_terms  = array();

				for ( $i = 1; $i <= 5; $i ++ ) {
					if ( in_array( $i, $rating_filter, true ) && isset( $product_visibility_terms[ 'rated-' . $i ] ) ) {
						$rating_terms[] = $product_visibility_terms[ 'rated-' . $i ];
					}
				}

				if ( ! empty( $rating_terms ) ) {
					$tax_query[] = array(
						'taxonomy'      => 'product_visibility',
						'field'         => 'term_taxonomy_id',
						'terms'         => $rating_terms,
						'operator'      => 'IN',
						'rating_filter' => true,
					);
				}
			}

			return array_filter( apply_filters( 'yith_wcan_product_query_tax_query', $tax_query, $this ) );
		}

		/**
		 * Filters meta_query param of a query, to add parameters specified in $this->_query_vars
		 *
		 * @param array $meta_query Meta query array of current query.
		 *
		 * @return array Array describing meta query currently set in the query vars
		 */
		public function get_meta_query( $meta_query = array() ) {
			if ( ! is_array( $meta_query ) ) {
				$meta_query = array(
					'relation' => 'AND',
				);
			}

			$query_vars = $this->get_query_vars();

			if ( ! empty( $query_vars ) ) {
				foreach ( $query_vars as $key => $value ) {
					if ( 0 !== strpos( $key, 'meta_' ) ) {
						continue;
					}

					$meta_key = str_replace( 'meta_', '', $key );

					// check if value contains operator.
					if ( 0 === strpos( $value, 'IN' ) ) {
						$operator = 'IN';
					} elseif ( 0 === strpos( $value, 'NOTIN' ) ) {
						$operator = 'NOT IN';
						$value    = str_replace( $operator, 'NOTIN', $value );
					} elseif ( 0 === strpos( $value, '>=' ) ) {
						$operator = '>=';
					} elseif ( 0 === strpos( $value, '=<' ) ) {
						$operator = '=<';
					} elseif ( 0 === strpos( $value, '>' ) ) {
						$operator = '>';
					} elseif ( 0 === strpos( $value, '<' ) ) {
						$operator = '<';
					} elseif ( 0 === strpos( $value, '!=' ) ) {
						$operator = '!=';
					} else {
						$operator = '=';
					}

					$meta_query[] = array(
						'key'      => $meta_key,
						'value'    => str_replace( $operator, '', $value ),
						'operator' => $operator,
					);
				}
			}

			return array_filter( apply_filters( 'yith_wcan_product_query_meta_query', $meta_query, $this ) );
		}

		/**
		 * Returns array of parameters needed for ordering query
		 *
		 * @return array|bool Query's ordering parameters, or false when no ordering is required.
		 */
		public function get_orderby() {
			$orderby = $this->get( 'orderby' );
			$order   = $this->get( 'order' );

			if ( false !== strpos( $orderby, '-' ) ) {
				$orderby_parts = explode( '-', $orderby );

				$orderby = $orderby_parts[0];

				if ( ! empty( $orderby_parts[1] ) ) {
					$order = $orderby_parts[1];
				}
			}

			if ( ! $orderby ) {
				return false;
			}

			/**
			 * This reference to WC_Query is ok, since it is one of the rare case
			 * when we can provide input, instead of relying on $_GET parameter
			 */
			return WC()->query->get_catalog_ordering_args( $orderby, $order );
		}

		/**
		 * This method is just a placeholder, that will always return false
		 * It was included within the plugin for future developments.
		 *
		 * @param array $post_in Post_in for current query.
		 *
		 * @return array|bool Query's post__in, or false when no limitation shall be applied.
		 */
		public function get_post_in( $post_in = array() ) {
			$on_sale_only  = $this->is_sale_only();
			$in_stock_only = $this->is_stock_only();
			$featured_only = $this->is_featured_only();

			if ( $on_sale_only ) {
				$on_sale = $this->get_product_ids_on_sale();
				$post_in = $post_in ? array_intersect( $post_in, $on_sale ) : $on_sale;
			}

			if ( $in_stock_only || 'yes' === yith_wcan_get_option( 'yith_wcan_hide_out_of_stock_products', 'no' ) ) {
				$in_stock = $this->get_product_ids_in_stock();
				$post_in  = $post_in ? array_intersect( $post_in, $in_stock ) : $in_stock;
			}

			if ( $featured_only ) {
				$featured = $this->get_product_ids_featured();
				$post_in  = $post_in ? array_intersect( $post_in, $featured ) : $featured;
			}

			return apply_filters( 'yith_wcan_query_post_in', $post_in );
		}

		/**
		 * This method will be used to add additional price clauses to any query, including WC_Query, that is normally
		 * excluded by the processing of our plugin (most filtering action, indeed, happens @ wp, after main query is
		 * already executed)
		 *
		 * @param array    $args Post clauses.
		 * @param WP_Query $query Query.
		 *
		 * @return array Array of filtered args
		 */
		public function price_ranges_handling( $args, $query ) {
			global $wpdb;
			$price_ranges = $this->get( 'price_ranges' );

			if ( ! $this->should_process_query( $query ) || ! $price_ranges ) {
				return $args;
			}

			$formatted_ranges = $this->get_active_price_ranges();

			if ( empty( $formatted_ranges ) ) {
				return $args;
			}

			$args['join'] .= ! strstr( $args['join'], 'wc_product_meta_lookup' ) ?
				" LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " :
				'';

			$price_conditions = '';

			foreach ( $formatted_ranges as $rage ) {
				$current_min_price = $rage['min_price'] ? $rage['min_price'] : 0;
				$current_max_price = $rage['max_price'] ? $rage['max_price'] : PHP_INT_MAX;

				/**
				 * Adjust if the store taxes are not displayed how they are stored.
				 * Kicks in when prices excluding tax are displayed including tax.
				 */
				if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
					$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
					$tax_rates = WC_Tax::get_rates( $tax_class );

					if ( $tax_rates ) {
						$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
						$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
					}
				}

				$price_conditions .= $wpdb->prepare(
					' NOT (%f<wc_product_meta_lookup.min_price OR %f>wc_product_meta_lookup.max_price ) OR ',
					$current_max_price,
					$current_min_price
				);
			}

			$price_conditions = trim( $price_conditions, 'OR ' );

			$args['where'] .= " AND ( {$price_conditions} )";

			return $args;
		}

		/**
		 * Hooks after main changes to the query, and applies additional modifications
		 *
		 * @return void
		 */
		public function additional_query_handling() {
			add_filter( 'posts_clauses', array( $this, 'additional_post_clauses' ), 10, 2 );
			add_filter( 'the_posts', array( $this, 'do_cleanup' ), 10, 2 );
		}

		/**
		 * Adds additional clauses to product query, in order to apply additional filters
		 *
		 * @param array    $args     Query parts.
		 * @param WP_Query $wp_query Query object.
		 *
		 * @return array Array of filtered query parts.
		 */
		public function additional_post_clauses( $args, $wp_query ) {
			global $wpdb;

			$min_price = floatval( $this->get( 'min_price' ) );
			$max_price = floatval( $this->get( 'max_price' ) );

			if ( ! $min_price && ! $max_price || ! $this->should_process_query( $wp_query ) ) {
				return $args;
			}

			$current_min_price = $min_price ? $min_price : 0;
			$current_max_price = $max_price ? $max_price : PHP_INT_MAX;

			/**
			 * Adjust if the store taxes are not displayed how they are stored.
			 * Kicks in when prices excluding tax are displayed including tax.
			 */
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
					$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
				}
			}

			$args['join']  .= ! strstr( $args['join'], 'wc_product_meta_lookup' ) ?
				" LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " :
				'';
			$args['where'] .= $wpdb->prepare(
				' AND NOT (%f<wc_product_meta_lookup.min_price OR %f>wc_product_meta_lookup.max_price ) ',
				$current_max_price,
				$current_min_price
			);

			return $args;
		}

		/**
		 * Remove additional parameters from the query
		 *
		 * @param array    $posts Array of retrieved posts.
		 * @param WP_Query $query Query object.
		 * @return array Array of posts (unchanged).
		 */
		public function do_cleanup( $posts, $query ) {
			if ( ! $query->get( 'yith_wcan_query' ) ) {
				return $posts;
			}

			remove_filter( 'posts_clauses', array( $this, 'additional_post_clauses' ), 10 );
			return $posts;
		}

		/* === TEST METHODS === */

		/**
		 * Returns true iw we're filtering for the specific parameter passed as argument
		 *
		 * @param string $param Parameter to search among query vars.
		 * @return bool Whether we're filtering for passed argument or not.
		 */
		public function is_filtered_by( $param ) {
			$query_vars = $this->get_query_vars();

			switch ( $param ) {
				case 'price_range':
					return array_key_exists( 'min_price', $query_vars ) || array_key_exists( 'max_price', $query_vars ) || array_key_exists( 'price_ranges', $query_vars );
				case 'price_slider':
					return array_key_exists( 'min_price', $query_vars ) || array_key_exists( 'max_price', $query_vars );
				case 'orderby':
					return array_key_exists( 'orderby', $query_vars );
				case 'rating_filter':
				case 'review':
					return array_key_exists( 'rating_filter', $query_vars );
				case 'onsale_filter':
					return array_key_exists( 'onsale_filter', $query_vars );
				case 'instock_filter':
					return array_key_exists( 'instock_filter', $query_vars );
				case 'featured_filter':
					return array_key_exists( 'featured_filter', $query_vars );
				case 'stock_sale':
					return array_key_exists( 'onsale_filter', $query_vars ) || array_key_exists( 'instock_filter', $query_vars ) || array_key_exists( 'featured_filter', $query_vars );
			}

			return parent::is_filtered_by( $param );
		}

		/**
		 * Checks whether we're currently filtering for a specific price range
		 *
		 * @param array $range Expects an array that contains min/max indexes for the range ends.
		 * @return bool Whether that range is active or not
		 */
		public function is_price_range( $range ) {
			$formatted_ranges = $this->get_active_price_ranges();

			if ( empty( $formatted_ranges ) ) {
				return false;
			}

			foreach ( $formatted_ranges as $current_range ) {
				if ( $range['min'] === $current_range['min_price'] && ( $range['max'] === $current_range['max_price'] || $range['unlimited'] ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks if we're filtering by a specific review rate
		 *
		 * @param int $rate Review rate to check.
		 * @return bool Whether that rate is active or not
		 */
		public function is_review_rate( $rate ) {
			$current_rating = $this->get( 'rating_filter', false );

			if ( ! $current_rating ) {
				return false;
			}

			return in_array( (int) $rate, array_map( 'intval', explode( ',', $current_rating ) ), true );
		}

		/**
		 * Checks if we're currently sorting by a specific order
		 *
		 * @param string $order Order to check.
		 *
		 * @return bool Whether products are sorted by specified order
		 */
		public function is_ordered_by( $order ) {
			$current_order = $this->get( 'orderby' );

			return $order === $current_order || 'menu_order' === $order && ! $current_order;
		}

		/**
		 * Checks whether on sale filter is active for current query
		 *
		 * @return bool Whether on sale filter is currently active
		 */
		public function is_stock_only() {
			return 1 === (int) $this->get( 'instock_filter', 0 ) || $this->should_filter() && 'yes' === yith_wcan_get_option( 'yith_wcan_hide_out_of_stock_products', 'no' );
		}

		/**
		 * Checks whether in stock filter is active for current query
		 *
		 * @return bool Whether in stock filter is currently active
		 */
		public function is_sale_only() {
			return 1 === (int) $this->get( 'onsale_filter', 0 );
		}

		/**
		 * Checks whether featured filter is active for current query
		 *
		 * @return bool Whether featured filter is currently active
		 */
		public function is_featured_only() {
			return 1 === (int) $this->get( 'featured_filter', 0 );
		}

		/* === RETRIEVE QUERY-RELEVANT PRODUCTS === */

		/**
		 * Count how many on sale products match current filter
		 *
		 * @return int Count of matching products
		 */
		public function count_query_relevant_on_sale_products() {
			return count( $this->get_query_relevant_on_sale_products() );
		}

		/**
		 * Count how many in stock products match current filter
		 *
		 * @return int Count of matching products
		 */
		public function count_query_relevant_in_stock_products() {
			return count( $this->get_query_relevant_in_stock_products() );
		}

		/**
		 * Count how many featured products match current filter
		 *
		 * @return int Count of matching products
		 */
		public function count_query_relevant_featured_products() {
			return count( $this->get_query_relevant_featured_products() );
		}

		/**
		 * Count how many products with a specific review rating match current filter
		 *
		 * @param int $rate Review rating to test.
		 *
		 * @return int Count of matching products
		 */
		public function count_query_relevant_rated_products( $rate ) {
			return count( $this->get_query_relevant_rated_products( $rate ) );
		}

		/**
		 * Count how many products in a specific price range match current filter
		 *
		 * @param array $range Array containing min and max indexes.
		 *
		 * @return int Count of matching products
		 */
		public function count_query_relevant_price_range_products( $range ) {
			return count( $this->get_query_relevant_price_range_products( $range ) );
		}

		/**
		 * Return ids for on sale  products matching current filter
		 *
		 * @return array Array of post ids that are both query-relevant and on sale
		 */
		public function get_query_relevant_on_sale_products() {
			return array_intersect( $this->get_filtered_products(), $this->get_product_ids_on_sale() );
		}

		/**
		 * Return ids for in stock  products matching current filter
		 *
		 * @return array Array of post ids that are both query-relevant and in stock
		 */
		public function get_query_relevant_in_stock_products() {
			return array_intersect( $this->get_filtered_products(), $this->get_product_ids_in_stock() );
		}

		/**
		 * Return ids for featured  products matching current filter
		 *
		 * @return array Array of post ids that are both query-relevant and featured
		 */
		public function get_query_relevant_featured_products() {
			return array_intersect( $this->get_filtered_products(), $this->get_product_ids_featured() );
		}

		/**
		 * Return ids for products with a specific review rating matching current filter
		 *
		 * @param int $rate Review rating to test.
		 *
		 * @return array Array of post ids that are both query-relevant and with a specific review rating
		 */
		public function get_query_relevant_rated_products( $rate ) {
			$term = get_term_by( 'slug', 'rated-' . $rate, 'product_visibility' );

			if ( ! $term ) {
				return array();
			}

			$query_vars = $this->get_query_vars();

			if ( isset( $query_vars['rating_filter'] ) ) {
				unset( $query_vars['rating_filter'] );
			}

			return array_intersect( $this->get_filtered_products_by_query_vars( $query_vars ), get_objects_in_term( $term->term_id, 'product_visibility' ) );
		}

		/**
		 * Return ids for  products in a specific price range matching current filter
		 *
		 * @param array $range Array containing min and max indexes.
		 *
		 * @return array Array of post ids that are both query-relevant and within a specific price range
		 */
		public function get_query_relevant_price_range_products( $range ) {
			global $wpdb;

			$query_vars = $this->get_query_vars();

			if ( isset( $query_vars['min_price'] ) ) {
				unset( $query_vars['min_price'] );
			}

			if ( isset( $query_vars['max_price'] ) ) {
				unset( $query_vars['max_price'] );
			}

			if ( isset( $query_vars['price_ranges'] ) ) {
				unset( $query_vars['price_ranges'] );
			}

			$products = $this->get_filtered_products_by_query_vars( $query_vars );

			if ( empty( $products ) ) {
				return $products;
			}

			$query = $wpdb->prepare(
				"SELECT product_id FROM {$wpdb->prefix}wc_product_meta_lookup wc_product_meta_lookup WHERE NOT (%f<wc_product_meta_lookup.min_price OR %f>wc_product_meta_lookup.max_price ) AND product_id IN (" . implode( ',', $products ) . ')', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				isset( $range['max'] ) && ! $range['unlimited'] ? (float) $range['max'] : PHP_INT_MAX,
				isset( $range['min'] ) ? (float) $range['min'] : 0
			);

			return $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery
		}

		/**
		 * Returns minimum price among currently queried products.
		 * If it cannot determine current query, it will return absolute minimum price.
		 *
		 * @return int Minimum price found
		 */
		public function get_query_relevant_min_price() {
			global $wpdb;

			$query_vars = $this->get_query_vars();

			if ( isset( $query_vars['min_price'] ) ) {
				unset( $query_vars['min_price'] );
			}

			if ( isset( $query_vars['max_price'] ) ) {
				unset( $query_vars['max_price'] );
			}

			if ( isset( $query_vars['price_ranges'] ) ) {
				unset( $query_vars['price_ranges'] );
			}

			$post_in = $this->get_filtered_products_by_query_vars( $query_vars );


			$lookup_query = "SELECT MIN(min_price) FROM {$wpdb->prefix}wc_product_meta_lookup";

			if ( ! empty( $post_in ) ) {
				$lookup_query .= ' WHERE product_id IN ( ' . implode( ',', array_map( 'esc_sql', $post_in ) ) . ' )';
			}

			$min_price = $wpdb->get_var( $lookup_query ); // phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery

			// Check to see if we should add taxes to the prices if store are excl tax but display incl.
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

			if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
				}
			}

			return floor( $min_price );
		}

		/**
		 * Returns maximum price among currently queried products.
		 * If it cannot determine current query, it will return absolute maximum price.
		 *
		 * @return int Minimum price found
		 */
		public function get_query_relevant_max_price() {
			global $wpdb;

			$query_vars = $this->get_query_vars();

			if ( isset( $query_vars['min_price'] ) ) {
				unset( $query_vars['min_price'] );
			}

			if ( isset( $query_vars['max_price'] ) ) {
				unset( $query_vars['max_price'] );
			}

			if ( isset( $query_vars['price_ranges'] ) ) {
				unset( $query_vars['price_ranges'] );
			}

			$post_in = $this->get_filtered_products_by_query_vars( $query_vars );

			$lookup_query = "SELECT MAX(max_price) FROM {$wpdb->prefix}wc_product_meta_lookup";

			if ( ! empty( $post_in ) ) {
				$lookup_query .= ' WHERE product_id IN ( ' . implode( ',', array_map( 'esc_sql', $post_in ) ) . ' )';
			}

			$max_price = $wpdb->get_var( $lookup_query ); // phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery

			// Check to see if we should add taxes to the prices if store are excl tax but display incl.
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

			if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
				}
			}
			return ceil( $max_price );
		}
	}
}
