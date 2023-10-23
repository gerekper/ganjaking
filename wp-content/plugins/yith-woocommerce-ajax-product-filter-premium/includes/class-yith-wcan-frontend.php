<?php
/**
 * Frontend class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Frontend {

		/**
		 * Array of product ids filtered for taxonomy
		 *
		 * @var array
		 * @deprecated
		 * @since version 3.0
		 */
		public $filtered_product_ids_for_taxonomy = array();

		/**
		 * Array of product ids filtered for current layered nav selection
		 *
		 * @var array
		 * @deprecated
		 * @since version 3.0
		 */
		public $layered_nav_product_ids = array();

		/**
		 * Array of unfiltered product ids for current shop page
		 *
		 * @var array
		 * @deprecated
		 * @since version 3.0
		 */
		public $unfiltered_product_ids = array();

		/**
		 * Array of product ids for current filters selection
		 *
		 * @var array
		 * @deprecated
		 * @since version 3.0
		 */
		public $filtered_product_ids = array();

		/**
		 * Array of product ids to include in current main query
		 *
		 * @var array
		 * @deprecated
		 * @since version 3.0
		 */
		public $layered_nav_post__in = array();

		/**
		 * Query object
		 *
		 * @var YITH_WCAN_Query
		 */
		protected $query = null;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			// new query object.
			$this->query = YITH_WCAN_Query();

			// Legacy query methods.
			add_filter( 'woocommerce_layered_nav_link', 'yit_plus_character_hack', 99 );
			add_filter( 'woocommerce_is_filtered', 'yit_is_filtered_uri', 20 );

			if ( is_active_widget( false, false, 'yith-woo-ajax-navigation' ) ) {
				add_filter( 'the_posts', array( $this, 'the_posts' ), 15, 2 );
				add_filter( 'woocommerce_is_layered_nav_active', '__return_true' );
			}

			// Frontend methods.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_action( 'body_class', array( $this, 'body_class' ) );
			add_action( 'wp_head', array( $this, 'add_meta' ) );
			add_action( 'wp_robots', array( $this, 'add_robots_directives' ) );

			// Template methods.
			add_action( 'init', array( $this, 'add_reset_button' ) );
			add_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'remove_duplicated_templates' ), 99, 1 );

			// YITH WCAN Loaded.
			do_action( 'yith_wcan_loaded' );

			// Deprecated filters.
			$deprecated_filters_map = array(
				'yith-wcan-frontend-args' => array(
					'since'  => '4.1.1',
					'use'    => 'yith_wcan_frontend_args',
					'params' => 1,
				),
			);

			yith_wcan_deprecated_filter( $deprecated_filters_map );
		}

		/* === LEGACY QUERY METHODS === */

		/**
		 * Returns main query object
		 *
		 * @return YITH_WCAN_Query
		 */
		public function get_query() {
			return $this->query;
		}

		/**
		 * Select the correct query object
		 *
		 * @param WP_Query|bool $current_wp_query Fallback query object.
		 *
		 * @access public
		 * @return array The query params
		 */
		public function select_query_object( $current_wp_query ) {
			/**
			 * For WordPress 4.7 Must use WP_Query object
			 */
			global $wp_the_query;

			return apply_filters( 'yith_wcan_use_wp_the_query_object', true ) ? $wp_the_query->query : $current_wp_query->query;
		}

		/**
		 * Hook into the_posts to do the main product query if needed.
		 *
		 * @access public
		 *
		 * @param WP_Post[]     $posts Retrieved posts.
		 * @param WP_Query|bool $query Query object, when relevant.
		 *
		 * @return array
		 */
		public function the_posts( $posts, $query = false ) {
			global $wp_query;
			$queried_object = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;

			if ( ! empty( $queried_object ) && ( is_shop() || is_product_taxonomy() || ! apply_filters( 'yith_wcan_is_search', is_search() ) ) ) {
				$filtered_posts   = array();
				$queried_post_ids = array();

				$special_handling_themes = array(
					'basel',
					'ux-shop',
					'aardvark',
				);

				$wp_theme      = wp_get_theme();
				$template_name = $wp_theme->get_template();
				$theme_version = $wp_theme->Version; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				/**
				 * Support for Flatsome Theme lower then 3.6.0
				 */
				if ( 'flatsome' === $template_name && version_compare( '3.6.0', $theme_version, '<' ) ) {
					$special_handling_themes[] = 'flatsome';
				}

				$needs_special_qtranslatex_handling = class_exists( 'QTX_Translator' ) && defined( 'YIT_CORE_VERSION' ) && '1.0.0' === YIT_CORE_VERSION;
				$is_special_handling_theme          = in_array( $template_name, $special_handling_themes, true );

				if ( $needs_special_qtranslatex_handling || $is_special_handling_theme || class_exists( 'SiteOrigin_Panels' ) ) {
					add_filter( 'yith_wcan_skip_layered_nav_query', '__return_true' );
				}

				$query_filtered_posts = array_map( 'intval', $this->layered_nav_query() );

				foreach ( $posts as $post ) {
					if ( in_array( $post->ID, $query_filtered_posts, true ) ) {
						$filtered_posts[]   = $post;
						$queried_post_ids[] = $post->ID;
					}
				}

				$query->posts      = $filtered_posts;
				$query->post_count = count( $filtered_posts );

				// Get main query.
				$current_wp_query = $this->select_query_object( $query );

				if ( is_array( $current_wp_query ) ) {
					// Get WP Query for current page (without 'paged').
					unset( $current_wp_query['paged'] );
				} else {
					$current_wp_query = array();
				}

				// Ensure filters are set.
				$unfiltered_args = array_merge(
					$current_wp_query,
					array(
						'post_type'              => 'product',
						'numberposts'            => - 1,
						'post_status'            => 'publish',
						'meta_query'             => is_object( $current_wp_query ) ? $current_wp_query->meta_query : array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						'fields'                 => 'ids',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'pagename'               => '',
						'wc_query'               => 'get_products_in_view', // Only for WC <= 2.6.x.
						'suppress_filters'       => true,
					)
				);

				$hide_out_of_stock_items = apply_filters( 'yith_wcan_hide_out_of_stock_items', 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ? true : false );

				if ( $hide_out_of_stock_items ) {
					$unfiltered_args['meta_query'][] = array(
						'key'     => '_stock_status',
						'value'   => 'instock',
						'compare' => 'AND',
					);
				}

				$unfiltered_args              = apply_filters( 'yith_wcan_unfiltered_args', $unfiltered_args );
				$this->unfiltered_product_ids = apply_filters( 'yith_wcan_unfiltered_product_ids', get_posts( $unfiltered_args ), $query, $current_wp_query );
				$this->filtered_product_ids   = $queried_post_ids;

				// Also store filtered posts ids...
				if ( count( $queried_post_ids ) > 0 ) {
					$this->filtered_product_ids = array_intersect( $this->unfiltered_product_ids, $queried_post_ids );
				} else {
					$this->filtered_product_ids = $this->unfiltered_product_ids;
				}

				if ( count( $this->layered_nav_post__in ) > 0 ) {
					$this->layered_nav_product_ids = array_intersect( $this->unfiltered_product_ids, $this->layered_nav_post__in );
				} else {
					$this->layered_nav_product_ids = $this->unfiltered_product_ids;
				}
			}

			return $posts;
		}

		/**
		 * Layered Nav post filter.
		 *
		 * @param array $filtered_posts Optional array of filtered post ids.
		 *
		 * @return array
		 */
		public function layered_nav_query( $filtered_posts = array() ) {
			global $wp_query;
			if ( apply_filters( 'yith_wcan_skip_layered_nav_query', false ) ) {
				return $filtered_posts;
			}

			$_chosen_attributes  = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$is_product_taxonomy = false;
			if ( is_product_taxonomy() ) {
				global $wp_query;
				$queried_object      = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
				$is_product_taxonomy = false;

				if ( $queried_object ) {
					$is_product_taxonomy = array(
						'taxonomy' => $queried_object->taxonomy,
						'terms'    => $queried_object->slug,
						'field'    => YITH_WCAN()->filter_term_field,
					);
				}
			}

			if ( count( $_chosen_attributes ) > 0 ) {

				$matched_products   = array(
					'and' => array(),
					'or'  => array(),
				);
				$filtered_attribute = array(
					'and' => false,
					'or'  => false,
				);

				foreach ( $_chosen_attributes as $attribute => $data ) {
					$matched_products_from_attribute = array();
					$filtered                        = false;

					if ( count( $data['terms'] ) > 0 ) {
						foreach ( $data['terms'] as $value ) {

							$args = array(
								'post_type'        => 'product',
								'numberposts'      => - 1,
								'post_status'      => 'publish',
								'fields'           => 'ids',
								'no_found_rows'    => true,
								'suppress_filters' => true,
								'tax_query'        => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
									array(
										'taxonomy' => $attribute,
										'terms'    => $value,
										'field'    => YITH_WCAN()->filter_term_field,
									),
								),
							);

							$args = yit_product_visibility_meta( $args );

							if ( $is_product_taxonomy ) {
								$args['tax_query'][] = $is_product_taxonomy;
							}

							// TODO: Increase performance for get_posts().
							$post_ids = apply_filters( 'woocommerce_layered_nav_query_post_ids', get_posts( $args ), $args, $attribute, $value );

							if ( ! is_wp_error( $post_ids ) ) {

								if ( count( $matched_products_from_attribute ) > 0 || $filtered ) {
									$matched_products_from_attribute = 'or' === $data['query_type'] ? array_merge( $post_ids, $matched_products_from_attribute ) : array_intersect( $post_ids, $matched_products_from_attribute );
								} else {
									$matched_products_from_attribute = $post_ids;
								}

								$filtered = true;
							}
						}
					}

					if ( count( $matched_products[ $data['query_type'] ] ) > 0 || true === $filtered_attribute[ $data['query_type'] ] ) {
						$matched_products[ $data['query_type'] ] = 'or' === $data['query_type'] ? array_merge( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] ) : array_intersect( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] );
					} else {
						$matched_products[ $data['query_type'] ] = $matched_products_from_attribute;
					}

					$filtered_attribute[ $data['query_type'] ] = true;

					$this->filtered_product_ids_for_taxonomy[ $attribute ] = $matched_products_from_attribute;
				}

				// Combine our AND and OR result sets.
				if ( $filtered_attribute['and'] && $filtered_attribute['or'] ) {
					$results = array_intersect( $matched_products['and'], $matched_products['or'] );
				} else {
					$results = array_merge( $matched_products['and'], $matched_products['or'] );
				}

				if ( $filtered ) {

					$this->layered_nav_post__in   = $results;
					$this->layered_nav_post__in[] = 0;

					if ( ! count( $filtered_posts ) ) {
						$filtered_posts   = $results;
						$filtered_posts[] = 0;
					} else {
						$filtered_posts   = array_intersect( $filtered_posts, $results );
						$filtered_posts[] = 0;
					}
				}
			} else {

				$args = array(
					'post_type'        => 'product',
					'numberposts'      => - 1,
					'post_status'      => 'publish',
					'fields'           => 'ids',
					'no_found_rows'    => true,
					'suppress_filters' => true,
					'tax_query'        => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'meta_query'       => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				);

				if ( $is_product_taxonomy ) {
					$args['tax_query'][] = $is_product_taxonomy;
				}

				// phpcs:disable WordPress.Security.NonceVerification.Recommended
				$min_price = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : false;
				$max_price = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : false;
				// phpcs:enable WordPress.Security.NonceVerification.Recommended

				if ( $min_price && $max_price ) {
					$args['meta_query'][] = array(
						'key'     => '_price',
						'value'   => array( $min_price, $max_price ),
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
					);
				}

				$args           = yit_product_visibility_meta( $args );
				$queried_object = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
				$taxonomy       = false;
				$slug           = false;

				if ( $queried_object instanceof WP_Term ) {
					$taxonomy = $queried_object->taxonomy;
					$slug     = $queried_object->slug;
				}

				// TODO: Increase performance for get_posts().
				$post_ids = apply_filters( 'woocommerce_layered_nav_query_post_ids', get_posts( $args ), $args, $taxonomy, $slug );

				if ( ! is_wp_error( $post_ids ) ) {
					$this->layered_nav_post__in   = $post_ids;
					$this->layered_nav_post__in[] = 0;

					if ( ! count( $filtered_posts ) ) {
						$filtered_posts   = $post_ids;
						$filtered_posts[] = 0;
					} else {
						$filtered_posts   = array_intersect( $filtered_posts, $post_ids );
						$filtered_posts[] = 0;
					}
				}
			}

			return (array) $filtered_posts;
		}

		/* === FRONTEND METHODS === */

		/**
		 * Enqueue frontend styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( yith_wcan_can_be_displayed() ) {
				// frontend style.
				wp_enqueue_style( 'yith-wcan-frontend', YITH_WCAN_URL . 'assets/css/frontend.css', false, YITH_WCAN_VERSION );

				// custom style.
				$custom_style = yith_wcan_get_option( 'yith_wcan_custom_style', '' );

				if ( ! empty( $custom_style ) ) {
					wp_add_inline_style( 'yith-wcan-frontend', sanitize_text_field( $custom_style ) );
				}

				// frontend scripts.
				wp_register_script( 'jseldom', YITH_WCAN_URL . 'assets/js/jquery.jseldom' . $suffix . '.js', array( 'jquery' ), '0.0.2', true );
				wp_enqueue_script( 'yith-wcan-script', YITH_WCAN_URL . 'assets/js/yith-wcan-frontend' . $suffix . '.js', array( 'jquery', 'jseldom' ), YITH_WCAN_VERSION, true );
				wp_localize_script( 'yith-wcan-script', 'yith_wcan', apply_filters( 'yith_wcan_frontend_args', $this->get_main_localize() ) );
			}

			wp_enqueue_style( 'yith-wcan-shortcodes' );
			wp_localize_script( 'yith-wcan-shortcodes', 'yith_wcan_shortcodes', $this->get_shortcodes_localize() );

			$custom_css = $this->build_custom_css();

			if ( ! empty( $custom_css ) ) {
				wp_add_inline_style( 'yith-wcan-shortcodes', $custom_css );
			}
		}

		/**
		 * Add a body class(es)
		 *
		 * @param array $classes The classes array.
		 *
		 * @return array
		 * @since  1.0
		 */
		public function body_class( $classes ) {
			$classes[] = apply_filters( 'yith_wcan_body_class', 'yith-wcan-free' );

			if ( YITH_WCAN_Query()->is_filtered() || yith_wcan_can_be_displayed() && yit_is_filtered_uri() ) {
				$classes[] = 'filtered';
			}

			return $classes;
		}

		/**
		 * Add robots directives to filtered pages
		 *
		 * @param array $directives Array of directives.
		 * @return array Filtered array of directives
		 */
		public function add_robots_directives( $directives ) {
			$enable_seo   = 'yes' === yith_wcan_get_option( 'yith_wcan_enable_seo' );
			$meta_options = yith_wcan_get_option( 'yith_wcan_seo_value', 'noindex-follow' );

			if ( $enable_seo && 'disabled' !== $meta_options && ( YITH_WCAN_Query()->is_filtered() || yith_wcan_can_be_displayed() && yit_is_filtered_uri() ) ) {
				$meta_options = explode( '-', $meta_options );

				foreach ( $meta_options as $directive ) {
					$directives[ $directive ] = true;

					if ( 'follow' === $directive && ! empty( $directives['nofollow'] ) ) {
						$directives['nofollow'] = false;
					}

					if ( 'nofollow' === $directive && ! empty( $directives['follow'] ) ) {
						$directives['follow'] = false;
					}
				}
			}

			return $directives;
		}

		/**
		 * Add custom meta to filtered page
		 *
		 * @return void
		 */
		public function add_meta() {
			$enable_seo   = 'yes' === yith_wcan_get_option( 'yith_wcan_enable_seo' );
			$meta_options = yith_wcan_get_option( 'yith_wcan_seo_value', 'noindex-follow' );

			if ( function_exists( 'wp_robots_no_robots' ) ) {
				return;
			}

			if ( $enable_seo && 'disabled' !== $meta_options && ( YITH_WCAN_Query()->is_filtered() || yith_wcan_can_be_displayed() && yit_is_filtered_uri() ) ) {
				$content = str_replace( '-', ', ', $meta_options );
				?>
				<meta name="robots" content="<?php echo esc_attr( $content ); ?>">
				<?php
			}
		}

		/**
		 * Returns an array of parameters to use to localize main frontend script
		 *
		 * @return array Array of parameters.
		 */
		protected function get_main_localize() {
			$current_theme    = function_exists( 'wp_get_theme' ) ? wp_get_theme() : null;
			$current_template = $current_theme instanceof WP_Theme ? $current_theme->get_template() : '';

			return apply_filters(
				'yith_wcan_ajax_frontend_classes',
				array(
					'container'          => yith_wcan_get_option( 'yith_wcan_ajax_shop_container', '.products' ),
					'pagination'         => yith_wcan_get_option( 'yith_wcan_ajax_shop_pagination', 'nav.woocommerce-pagination' ),
					'result_count'       => yith_wcan_get_option( 'yith_wcan_ajax_shop_result_container', '.woocommerce-result-count' ),
					'wc_price_slider'    => array(
						'wrapper'   => '.price_slider',
						'min_price' => '.price_slider_amount #min_price',
						'max_price' => '.price_slider_amount #max_price',
					),
					'is_mobile'          => wp_is_mobile(),
					'scroll_top'         => yith_wcan_get_option( 'yith_wcan_ajax_scroll_top_class', '.yit-wcan-container' ),
					'scroll_top_mode'    => yith_wcan_get_option( 'yith_wcan_scroll_top_mode', 'mobile' ),
					'change_browser_url' => 'yes' === yith_wcan_get_option( 'yith_wcan_change_browser_url', 'yes' ) ? true : false,
					/* === Avada Theme Support === */
					'avada'              => array(
						'is_enabled' => class_exists( 'Avada' ),
						'sort_count' => 'ul.sort-count.order-dropdown',
					),
					/* Flatsome Theme Support */
					'flatsome'           => array(
						'is_enabled'        => function_exists( 'flatsome_option' ),
						'lazy_load_enabled' => get_theme_mod( 'lazy_load_images' ),
					),
					/* === YooThemes Theme Support === */
					'yootheme'           => array(
						'is_enabled' => 'yootheme' === $current_template,
					),
				)
			);
		}

		/**
		 * Returns an array of parameters to use to localize shortcodes script
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array Array of parameters.
		 */
		protected function get_shortcodes_localize( $context = 'view' ) {
			$params = array(
				'query_param'           => YITH_WCAN_Query()->get_query_param(),
				'supported_taxonomies'  => array_keys( YITH_WCAN_Query()->get_supported_taxonomies() ),
				'content'               => apply_filters( 'yith_wcan_content_selector', '#content' ),
				'change_browser_url'    => in_array( yith_wcan_get_option( 'yith_wcan_change_browser_url', 'yes' ), array( 'yes', 'custom' ), true ),
				'instant_filters'       => true,
				'ajax_filters'          => true,
				'reload_on_back'        => true,
				'show_clear_filter'     => false,
				'scroll_top'            => false,
				'scroll_target'         => false,
				'modal_on_mobile'       => false,
				'session_param'         => false,
				'show_current_children' => false,
				'loader'                => false,
				'toggles_open_on_modal' => false,
				'mobile_media_query'    => 991,
				'base_url'              => $this->get_base_url( is_shop() ? yit_get_woocommerce_layered_nav_link() : '' ),
				'terms_per_page'        => apply_filters( 'yith_wcan_dropdown_terms_per_page', 10 ),
				'currency_format'       => apply_filters(
					'yith_wcan_shortcodes_script_currency_format',
					array(
						'symbol'    => get_woocommerce_currency_symbol(),
						'decimal'   => esc_attr( wc_get_price_decimal_separator() ),
						'thousand'  => esc_attr( wc_get_price_thousand_separator() ),
						'precision' => wc_get_price_decimals(),
						'format'    => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
					)
				),
				'labels'                => apply_filters(
					'yith_wcan_shortcodes_script_labels',
					array(
						'empty_option'         => _x( 'All', '[FRONTEND] "All" label shown when no term is selected', 'yith-woocommerce-ajax-navigation' ),
						'search_placeholder'   => _x( 'Search...', '[FRONTEND] Search placeholder shown in terms dropdown', 'yith-woocommerce-ajax-navigation' ),
						'no_items'             => _x( 'No item found', '[FRONTEND] Empty items list in the dropdown', 'yith-woocommerce-ajax-navigation' ),
						// translators: 1. Number of items to show.
						'show_more'            => _x( 'Show %d more', '[FRONTEND] Show more link on terms dropdown', 'yith-woocommerce-ajax-navigation' ),
						'close'                => _x( 'Close', '[FRONTEND] Alt text for modal close button on mobile', 'yith-woocommerce-ajax-navigation' ),
						'save'                 => _x( 'Save', '[FRONTEND] Label for filter button, on horizontal layout', 'yith-woocommerce-ajax-navigation' ),
						'show_results'         => _x( 'Show results', '[FRONTEND] Label for filter button, on mobile modal', 'yith-woocommerce-ajax-navigation' ),
						'clear_selection'      => _x( 'Clear', '[FRONTEND] Label for clear selection link, that appears above filter after selection', 'yith-woocommerce-ajax-navigation' ),
						'clear_all_selections' => _x( 'Clear All', '[FRONTEND] Label for clear selection link, that appears above filter after selection', 'yith-woocommerce-ajax-navigation' ),
					)
				),
			);

			if ( 'view' === $context ) {
				return apply_filters( 'yith_wcan_shortcodes_script_args', $params );
			}

			return $params;
		}

		/**
		 * Build custom CSS template, to be used in page header
		 *
		 * @return bool|string Custom CSS template, ro false when no content should be output.
		 */
		protected function build_custom_css() {
			$default_accent_color = apply_filters( 'yith_wcan_default_accent_color', '#A7144C' );

			$variables = array();
			$options   = array(
				'filters_colors'       => array(
					'default'  => array(
						'titles'     => '#434343',
						'background' => '#FFFFFF',
						'accent'     => $default_accent_color,
					),
					'callback' => function( $raw_value ) {
						// register accent color as rgb component, to be used in rgba() function.
						$accent = $raw_value['accent'];

						list( $accent_r, $accent_g, $accent_b ) = yith_wcan_hex2rgb( $accent );

						$raw_value['accent_r'] = $accent_r;
						$raw_value['accent_g'] = $accent_g;
						$raw_value['accent_b'] = $accent_b;

						return $raw_value;
					},
				),
				'color_swatches_style' => array(
					'default'  => 'round',
					'variable' => 'color_swatches_border_radius',
					'callback' => function( $raw_value ) {
						return 'round' === $raw_value ? '100%' : '5px';
					},
				),
				'color_swatches_size'  => array(
					'default'  => '30',
					'callback' => function( $raw_value ) {
						return $raw_value . 'px';
					},
				),
				'labels_style'         => array(
					'default' => array(
						'background'        => '#FFFFFF',
						'background_hover'  => $default_accent_color,
						'background_active' => $default_accent_color,
						'text'              => '#434343',
						'text_hover'        => '#FFFFFF',
						'text_active'       => '#FFFFFF',
					),
				),
				'anchors_style'        => array(
					'default' => array(
						'text'        => '#434343',
						'text_hover'  => $default_accent_color,
						'text_active' => $default_accent_color,
					),
				),
			);

			// cycles through options.
			foreach ( $options as $variable => $settings ) {
				$option   = "yith_wcan_{$variable}";
				$variable = '--yith-wcan-' . ( isset( $settings['variable'] ) ? $settings['variable'] : $variable );
				$value    = yith_wcan_get_option( $option, $settings['default'] );

				if ( isset( $settings['callback'] ) && is_callable( $settings['callback'] ) ) {
					$value = $settings['callback']( $value );
				}

				if ( empty( $value ) ) {
					continue;
				}

				if ( is_array( $value ) ) {
					foreach ( $value as $sub_variable => $sub_value ) {
						$variables[ "{$variable}_{$sub_variable}" ] = $sub_value;
					}
				} else {
					$variables[ $variable ] = $value;
				}
			}

			if ( empty( $variables ) ) {
				return false;
			}

			// start CSS snippet.
			$template = ":root{\n";

			// cycles through variables.
			foreach ( $variables as $variable => $value ) {
				$template .= "\t{$variable}: {$value};\n";
			}

			// close :root directive.
			$template .= '}';

			return apply_filters( 'yith_wcan_custom_css', $template );
		}

		/* === TEMPLATE METHODS === */

		/**
		 * Hooks callback that will print list fo active filters
		 *
		 * @return void
		 */
		public function add_reset_button() {
			$show_reset_button     = 'yes' === yith_wcan_get_option( 'yith_wcan_show_reset', 'yes' );
			$reset_button_position = yith_wcan_get_option( 'yith_wcan_reset_button_position', 'after_filters' );

			if ( ! $show_reset_button ) {
				return;
			}

			switch ( $reset_button_position ) {
				case 'before_filters':
					add_action( 'yith_wcan_before_preset_filters', array( $this, 'reset_button' ) );
					break;
				case 'after_filters':
					add_action( 'yith_wcan_after_preset_filters', array( $this, 'reset_button' ) );
					break;
				case 'before_products':
					$locations = $this->get_before_product_locations( 2 );

					if ( ! $locations ) {
						return;
					}

					foreach ( $locations as $location ) {
						add_action( $location['hook'], array( $this, 'reset_button' ), $location['priority'] );
					}
					break;
				case 'after_active_labels':
					add_action( 'yith_wcan_after_active_filters', array( $this, 'reset_button' ) );
					break;
			}
		}

		/**
		 * Print list of active filters
		 *
		 * @param YITH_WCAN_Preset|bool $preset Current preset, when applicable; false otherwise.
		 *
		 * @return void
		 */
		public function reset_button( $preset = false ) {
			if ( ! YITH_WCAN_Query()->is_filtered() ) {
				return;
			}

			yith_wcan_get_template( 'filters/global/reset-filters.php', compact( 'preset' ) );
		}

		/**
		 * Remove duplicated templates before products shortcode
		 *
		 * When paginating shortcode, WC will execute both woocommerce_shortcode_before_products_loop and
		 * woocommerce_before_shop_loop; in order to avoid to print filter templates twice, we listeb for first event
		 * and remove_action from the second, when pagination is enabled
		 *
		 * @param array $shortcode_settings Array of shortcode configuration.
		 * @return void
		 */
		public function remove_duplicated_templates( $shortcode_settings = array() ) {
			if ( ! wc_string_to_bool( $shortcode_settings['paginate'] ) ) {
				return;
			}

			$locations = $this->get_before_product_locations( 2 );

			if ( ! isset( $locations['before_shop'] ) ) {
				return;
			}

			remove_action( $locations['before_shop']['hook'], array( $this, 'reset_button' ), $locations['before_shop']['priority'] );
		}

		/* === UTILS METHODS === */

		/**
		 * Returns base url for filtering
		 *
		 * @param string $base_url Base url; if none passed, try to calculate it.
		 *
		 * @return string Base url for filters.
		 */
		public function get_base_url( $base_url = '' ) {
			$base_url = ! ! $base_url ? $base_url : get_pagenum_link(); // remove page param.
			$base_url = preg_replace( '/\?.*/', '', $base_url ); // remove query string.
			$base_url = trailingslashit( $base_url ); // add trailing slash.

			return apply_filters( 'yith_wcan_base_url', $base_url );
		}

		/**
		 * Returns an array of locations where items shown "Before products" should be hooked
		 *
		 * @param int $offset Integer used to offset hook priority.
		 *                    It is used when multiple templates are hooked to the same location, and you want to define a clear order.
		 *
		 * @return array Array of locations.
		 */
		public function get_before_product_locations( $offset = 0 ) {
			return apply_filters(
				'yith_wcan_before_product_locations',
				array(
					// before shop.
					'before_shop'               => array(
						'hook'     => 'woocommerce_before_shop_loop',
						'priority' => 10 + $offset,
					),
					// before products shortcode.
					'shortcode_before_products' => array(
						'hook'     => 'woocommerce_shortcode_before_products_loop',
						'priority' => 10 + $offset,
					),
					// before no_products template.
					'no_products_found'         => array(
						'hook'     => 'woocommerce_no_products_found',
						'priority' => 5 + $offset,
					),
				),
				$offset
			);
		}
	}
}
