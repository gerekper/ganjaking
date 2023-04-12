<?php
/**
 * Product Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_product extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Fetch product ids
	 *
	 * @param array $layout The layout.
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 5.0
	 */
	public function add_thumbnail_css( $layout = '', $element = [], $args = [] ) {

		$this_items_per_row   = $element['items_per_row'];
		$this_items_per_row_r = isset( $element['items_per_row_r'] ) ? $element['items_per_row_r'] : [];
		$this__percent        = 100;
		$container_css_id     = 'element_';
		if ( isset( $element['container_css_id'] ) ) {
			$container_css_id = $element['container_css_id'];
		}
		if ( ! isset( $args['product_id'] ) ) {
			$args['product_id'] = '';
		}

		$li_selector = '.tm-product-id-' . $args['product_id'] . ' .cpf-type-product-' . $layout . ' ul.tmcp-ul-wrap.tm-element-ul-product.' . $container_css_id . $args['element_counter'] . $args['form_prefix'] . ' > li.tmcp-field-wrap';

		if ( ! empty( $this_items_per_row ) ) {
			if ( 'auto' === $this_items_per_row || ! is_numeric( $this_items_per_row ) || floatval( $this_items_per_row ) === 0 ) {
				$this_items_per_row = 0;
				$css_string         = $li_selector . '{-ms-flex: 0 0 auto !important;flex: 0 0 auto !important;width:auto !important;}';
			} else {
				$this_items_per_row = (float) $this_items_per_row;
				$this__percent      = (float) ( 100 / $this_items_per_row );
				$css_string         = $li_selector . '{-ms-flex: 0 0 ' . $this__percent . '% !important;flex: 0 0 ' . $this__percent . '% !important;max-width:' . $this__percent . '% !important;}';
			}

			$css_string = str_replace( [ "\r", "\n" ], '', $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		} else {
			$this_items_per_row = (float) $element['items_per_row'];
		}

		foreach ( $this_items_per_row_r as $key => $value ) {
			$before = '';
			$after  = '}';

			if ( ! empty( $value ) ) {

				if ( 'desktop' !== $key ) {

					switch ( $key ) {
						case 'tablets_galaxy': // 800-1280
							$before = '@media only screen and (min-device-width : 800px) and (max-device-width : 1280px),only screen and (min-width : 800px) and (max-width : 1280px) {';
							break;
						case 'tablets': // 768-1024
							$before = '@media only screen and (min-device-width : 768px) and (max-device-width : 1024px),only screen and (min-width : 768px) and (max-width : 1024px) {';
							break;
						case 'tablets_small': // 481-767
							$before = '@media only screen and (min-device-width : 481px) and (max-device-width : 767px),only screen and (min-width : 481px) and (max-width : 767px) {';
							break;
						case 'iphone6_plus': // 414-736
							$before = '@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 414px) and (max--width: 736px) {';
							break;
						case 'iphone6': // 375-667
							$before = '@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 375px) and (max-width: 667px) {';
							break;
						case 'galaxy': // 320-640
							$before = '@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2),only screen and (width: 320px) and (height: 640px) {';
							break;
						case 'iphone5': // 320-568
							$before = '@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 320px) and (max-width: 568px) {';
							break;
						case 'smartphones': // 320-480
							$before = '@media only screen and (min-device-width : 320px) and (max-device-width : 480px), only screen and (min-width : 320px) and (max-width : 480px), only screen and (max-width : 319px){';
							break;

						default:
							// code...
							break;
					}

					$thisitems_per_row = (float) $value;
					$this_percent      = (float) ( 100 / $thisitems_per_row );
					$css_string        = $before . $li_selector . '{-ms-flex: 0 0 ' . $this_percent . '% !important;flex: 0 0 ' . $this_percent . '% !important;max-width:' . $this_percent . '% !important;}' . $after;

					$css_string = str_replace( [ "\r", "\n" ], '', $css_string );
					THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );

				}
			}
		}

	}

	/**
	 * Fetch product ids
	 *
	 * @param array $data Array of arguments.
	 * @param array $args Array of arguments.
	 * @since 5.0
	 */
	public function fetch_ids( $data = [], $args = [] ) {

		$args = wp_parse_args(
			$args,
			[
				// Fetch mode.
				'mode'     => 'products',
				// Sort retrieved posts by parameter.
				'orderby'  => false,
				// Number of post to show per page.
				'per_page' => false,
				// Number of page.
				'paged'    => 1,
				// Default selected product id.
				'default'  => '',
				// Enable query cache.
				'cache'    => false,
			]
		);

		if ( 'product' === $args['mode'] ) {
			$args['mode'] = 'products';
		}

		$is_empty          = false;
		$fetch_cache       = false;
		$id                = $data['id'];
		$transient_name    = 'tc_product_element_query_' . $id;
		$fetch_cache_array = $args['cache'] ? get_transient( $transient_name ) : false;
		$transient_version = WC_Cache_Helper::get_transient_version( 'product' );

		if ( is_array( $fetch_cache_array ) && ! isset( $fetch_cache_array['version'] ) ) {
			if ( isset( $fetch_cache_array[ $id ] ) && is_array( $fetch_cache_array[ $id ] ) ) {
				if ( isset( $fetch_cache_array[ $id ]['version'] ) && $fetch_cache_array[ $id ]['version'] === $transient_version ) {
					$fetch_cache = $fetch_cache_array[ $id ];
				}
			}
		}

		if ( false === $fetch_cache ) {

			$query = [
				'post_status'         => [ 'publish', 'private' ],
				'ignore_sticky_posts' => 1,
				'nopaging'            => true,
				'order'               => 'asc',
				'fields'              => 'ids',
				'meta_query'          => [], // phpcs:ignore WordPress.DB.SlowDBQuery
			];

			if ( 'products' === $args['mode'] ) {

				if ( ! empty( $data['productids'] ) ) {
					$query['post_type'] = [ 'product', 'product_variation' ];
					$query['post__in']  = array_values( $data['productids'] );
				} else {
					$is_empty = true;
				}
			} elseif ( 'categories' === $args['mode'] ) {

				if ( ! empty( $data['categoryids'] ) ) {
					$query['post_type']   = 'product';
					$query['tax_query'][] = [
						'relation' => 'AND',
						[
							'taxonomy' => 'product_cat',
							'terms'    => ! empty( $data['categoryids'] ) ? array_values( $data['categoryids'] ) : [ '0' ],
							'operator' => 'IN',
						],
						[
							'taxonomy' => 'product_type',
							'field'    => 'name',
							'terms'    => [ 'simple', 'variable' ],
							'operator' => 'IN',
						],
					];
				} else {
					$is_empty = true;
				}
			}

			if ( ! $is_empty ) {

				// Sort retrieved posts.
				if ( false === $args['orderby'] ) {

					if ( 'products' === $args['mode'] ) {
						// Preserve post ID order given (WC>=3.5).
						$query['orderby'] = 'post__in';
					} elseif ( 'categories' === $args['mode'] ) {
						// Order by date and title.
						$query['orderby'] = 'date title';
					}

					// Stop the data retrieved from being added to the cache.
					if ( false === $args['per_page'] ) {
						// Disable Post term information cache.
						$query['update_post_term_cache'] = false;
						// Disable Post meta information cache.
						$query['update_post_meta_cache'] = false;
						// Disable Post information cache.
						$query['cache_results'] = false;
					}
				} else {

					if ( is_array( $args['orderby'] ) ) {
						if ( 'none' !== $args['orderby']['orderby'] ) {
							$query['orderby'] = $args['orderby']['orderby'];
							$query['order']   = $args['orderby']['order'];
							if ( isset( $args['orderby']['meta_key'] ) ) {
								$query['meta_key'] = $args['orderby']['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery
							}
						} else {
							if ( 'products' === $args['mode'] ) {
								// Preserve post ID order given (WC>=3.5).
								$query['orderby'] = 'post__in';
							} elseif ( 'categories' === $args['mode'] ) {
								// Order by date and title.
								$query['orderby'] = 'date title';
							}
						}
					} else {
						$query['orderby'] = $args['orderby'];
					}
				}

				// Hide out of stock products.
				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && function_exists( 'wc_get_product_visibility_term_ids' ) ) {
					// Get full list of product visibilty term ids.
					$product_visibility_term_ids = wc_get_product_visibility_term_ids();
					$query['tax_query'][]        = [
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					];
				}

				// Pagination Parameters.
				if ( $args['per_page'] ) {
					if ( '' === $args['default'] ) {
						// Enable pagination.
						$query['nopaging'] = false;
						// Number of post to show per page.
						$query['posts_per_page'] = $args['per_page'];
						// Number of page.
						$query['paged'] = $args['paged'];
					}
				}

				// Exclude current product.
				$query['post__not_in'] = [ $args['product_id'] ];

				$query = new WP_Query( $query );
				$fetch = [
					'total_pages'  => $query->max_num_pages,
					'current_page' => $query->get( 'paged' ),
					'posts'        => $query->posts,
					'found_posts'  => $query->found_posts,
				];

			} else {
				$fetch = [
					'total_pages'  => 0,
					'current_page' => 0,
					'posts'        => [],
					'found_posts'  => 0,
				];
			}

			if ( $args['cache'] ) {
				if ( is_array( $fetch_cache_array ) && ! isset( $fetch_cache_array['version'] ) ) {
					$fetch_cache_array[ $id ] = array_merge( $fetch, [ 'version' => $transient_version ] );
				} else {
					$fetch_cache_array = [
						$id => array_merge(
							$fetch,
							[ 'version' => $transient_version ]
						),
					];
				}

				set_transient( $transient_name, $fetch_cache_array, DAY_IN_SECONDS * 7 );
			}
		} else {
			$fetch = $fetch_cache;
		}

		if ( '' !== $args['default'] && $args['per_page'] && $args['per_page'] < $fetch['found_posts'] ) {

			$fetch        = ! empty( $fetch['posts'] ) ? $fetch['posts'] : [];
			$index        = array_search( $args['default'], $fetch ) + 1; // phpcs:ignore WordPress.PHP.StrictInArray
			$default_page = ceil( $index / $args['per_page'] );

			if ( ! empty( $fetch ) ) {

				$query = new WP_Query(
					[
						'post_type'           => 'product',
						'post_status'         => [ 'publish', 'private' ],
						'ignore_sticky_posts' => 1,
						'nopaging'            => false,
						'posts_per_page'      => $args['per_page'],
						'paged'               => $default_page,
						'order'               => 'desc',
						'orderby'             => 'post__in',
						'post__in'            => $fetch,
						'fields'              => 'ids',
					]
				);

				$fetch = [
					'total_pages'  => $query->max_num_pages,
					'current_page' => $query->get( 'paged' ),
					'posts'        => $query->posts,
					'found_posts'  => $query->found_posts,
				];
			}
		}

		return $fetch;

	}

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$categoryids             = array_map( 'absint', (array) wp_unslash( $this->get_value( $element, 'categoryids', [] ) ) );
		$productids              = array_map( 'absint', (array) wp_unslash( $this->get_value( $element, 'productids', [] ) ) );
		$layout_mode             = $this->get_value( $element, 'layout_mode', '' );
		$placeholder             = $this->get_value( $element, 'placeholder', '' );
		$quantity_min            = $this->get_value( $element, 'quantity_min', 1 );
		$quantity_max            = $this->get_value( $element, 'quantity_max', '' );
		$mode                    = $this->get_value( $element, 'mode', '' );
		$uniqid                  = $this->get_value( $element, 'uniqid', '' );
		$priced_individually     = $this->get_value( $element, 'priced_individually', '' );
		$order                   = $this->get_value( $element, 'order', '' );
		$orderby                 = $this->get_value( $element, 'orderby', '' );
		$discount                = $this->get_value( $element, 'discount', '' );
		$discount_type           = $this->get_value( $element, 'discount_type', '' );
		$discount_exclude_addons = $this->get_value( $element, 'discount_exclude_addons', '' );

		if ( 'product' === $mode ) {
			$layout_mode = 'hidden';
		}

		// default value.
		$get_default_value = $this->get_default_value( $element, $args );

		// class label.
		$class_label = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_select_fullwidth === 'yes' ) {
			$class_label = ' fullwidth';
		}

		$product_list                      = [];
		$product_list_available_variations = [];

		// populate options.
		$options = [];

		$selected_value = '';
		if ( isset( $args['posted_name'] ) ) {
			$name = $args['posted_name'];
		}

		$hide_amount = empty( $element['hide_amount'] ) ? '0' : '1';

		$_default_value_counter = 0;

		if ( 'dropdown' === $layout_mode ) {

			if ( '' !== $placeholder ) {
				$option    = [
					'value_to_show'          => '',
					'data_price'             => '',
					'data_rules'             => '',
					'data_rulestype'         => '',
					'text'                   => wptexturize( apply_filters( 'wc_epo_kses', $placeholder, $placeholder ) ),
					'_default_value_counter' => '',
				];
				$options[] = $option;
			}
		}

		$__min_value              = $quantity_min;
		$__max_value              = $quantity_max;
		$__step                   = 1;
		$__quantity_default_value = '';

		if ( isset( $_REQUEST[ $name . '_quantity' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$__quantity_default_value = sanitize_text_field( wp_unslash( $_REQUEST[ $name . '_quantity' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( '' !== $__min_value ) {
			$__min_value = floatval( $__min_value );
		} else {
			$__min_value = 0;
		}
		if ( '' === $__min_value ) {
			$__min_value = 0;
		}
		if ( '' !== $__max_value ) {
			$__max_value = floatval( $__max_value );
		}

		if ( empty( $__step ) ) {
			$__step = 'any';
		}

		if ( $__min_value < 0 ) {
			$__min_value = 0;
		}
		if ( $__max_value < 0 ) {
			$__max_value = 0;
		}

		if ( '' === $__quantity_default_value || ! is_numeric( $__quantity_default_value ) ) {
			$__quantity_default_value = $__min_value;
		}

		if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) ) {
			if ( $__min_value > $__max_value ) {
				if ( 'any' === $__step ) {
					$__max_value = $__min_value + 1;
				} else {
					$__max_value = $__min_value + $__step;
				}
			}
			if ( $__quantity_default_value > $__max_value ) {
				$__quantity_default_value = $__max_value;
			}
			if ( $__quantity_default_value < $__min_value ) {
				$__quantity_default_value = $__min_value;
			}
		}

		if ( $__quantity_default_value < 0 ) {
			$__quantity_default_value = 0;
		}

		$data = [
			'id'          => $uniqid,
			'categoryids' => $categoryids,
			'productids'  => $productids,
		];

		$_orderby = false;
		if ( $order && $orderby ) {
			$_orderby = [
				'order'   => strtoupper( $order ),
				'orderby' => $orderby,
			];
			if ( 'baseprice' === $orderby ) {
				$_orderby['orderby']  = 'meta_value_num';
				$_orderby['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
			}
		}

		$product_id_array = $this->fetch_ids(
			$data,
			[
				'per_page'   => false,
				'mode'       => $mode,
				'default'    => $get_default_value,
				'product_id' => $args['product_id'],
				'orderby'    => $_orderby,
			]
		);

		foreach ( $product_id_array['posts'] as $key => $product_id ) {

			$selected_value = '';
			if ( isset( $args['posted_name'] ) ) {
				$name = $args['posted_name'];
				if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $this->post_data[ 'tmcp_' . $args['name_inc'] ] ) ) {
					if ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
						$selected_value = $this->post_data[ 'tmcp_' . $args['name_inc'] . '_' . $_default_value_counter ];
					} else {
						$selected_value = $this->post_data[ 'tmcp_' . $args['name_inc'] ];
					}
				} elseif ( empty( $this->post_data ) && ( isset( $_REQUEST[ $name ] ) || isset( $_REQUEST[ $name . '_' . $_default_value_counter ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( isset( $_REQUEST[ $name . '_' . $_default_value_counter ] ) && ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
						$selected_value = wp_unslash( $_REQUEST[ $name . '_' . $_default_value_counter ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
					} elseif ( isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
						$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
					}
				} elseif ( THEMECOMPLETE_EPO()->is_quick_view() || ( empty( $this->post_data ) || ( isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action'] ) ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) {
					$selected_value = -1;
				}
			}

			$selected_value = apply_filters( 'wc_epo_default_value', $selected_value, $element );

			$is_default_value = isset( $element['default_value'] )
				?
				( ( '' !== $element['default_value'] )
					? ( (int) $element['default_value'] === (int) $product_id )
					: false )
				: false;

			$selected = false;

			if ( -1 === $selected_value ) {
				if ( (
						THEMECOMPLETE_EPO()->is_quick_view() ||
						(
							empty( $this->post_data ) ||
							(
								isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action']
							)
						) ||
						'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add
					) && isset( $is_default_value ) ) {
					if ( $is_default_value ) {
						$selected = true;
					}
				}
			} else {
				if ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
					if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $this->post_data[ 'tmcp_' . $args['name_inc'] . '_' . $_default_value_counter ] ) ) {
						$selected_value = $this->post_data[ 'tmcp_' . $args['name_inc'] . '_' . $_default_value_counter ];
					} elseif ( isset( $_REQUEST[ $name ] ) && isset( $_REQUEST[ $name . '_' . $_default_value_counter ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$selected_value = wp_unslash( $_REQUEST[ $name . '_' . $_default_value_counter ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}
				}
				if ( $is_default_value && ! empty( $element['default_value_override'] ) && isset( $element['default_value'] ) ) {
					$selected = true;
				} elseif ( esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $product_id ) ) {
					$selected = true;
				}
			}

			if ( 'hidden' === $layout_mode && $__quantity_default_value > 0 && $__min_value === $__max_value ) {
				$selected = true;
			}

			$css_class = apply_filters( 'wc_epo_multiple_options_css_class', '', $element, $_default_value_counter );
			if ( '' !== $css_class ) {
				$css_class = ' ' . $css_class;
			}
			add_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );
			$product = wc_get_product( $product_id );
			remove_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );
			if ( ! empty( $product ) && is_object( $product ) ) {
				$tc_get_default_currency = apply_filters( 'tc_get_default_currency', get_option( 'woocommerce_currency' ) );

				$type                 = themecomplete_get_product_type( $product );
				$attributes           = [];
				$available_variations = [];

				$title = $product->get_name();
				if ( 'variation' === $type ) {
					$should_include_product_name = apply_filters( 'wc_epo_product_variation_title_include_product_name', true, $product );
					if ( ! $should_include_product_name ) {
						$title = wc_get_formatted_variation( $product, true, false );
					}
				}
				$title = apply_filters( 'wc_epo_associated_product_name', $title, $product, $product_id );

				if ( 'variable' === $type ) {
					if ( ( $selected || 'hidden' === $layout_mode ) && is_callable( [ $product, 'get_variation_attributes' ] ) ) {
						// workaround to get discounts shownn in the product for variable products.
						$isset_discount_type = false;
						if ( isset( $_REQUEST['discount_type'] ) && isset( $_REQUEST['discount'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$isset_discount_type = wp_unslash( $_REQUEST['discount_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							$isset_discount      = wp_unslash( $_REQUEST['discount'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						}
						$_REQUEST['discount_type'] = $discount_type;
						$_REQUEST['discount']      = $discount;
						$attributes                = $product->get_variation_attributes();

						$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

						$available_variations = $get_variations ? $product->get_available_variations() : false;

						$product_list[ $product_id ] = $attributes;

						$variations_json = wp_json_encode( $available_variations );
						$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

						$product_list_available_variations[ $product_id ] = $variations_attr;
						if ( $isset_discount_type ) {
							$_REQUEST['discount_type'] = $isset_discount_type;
							$_REQUEST['discount']      = $isset_discount;
						} else {
							unset( $_REQUEST['discount_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							unset( $_REQUEST['discount'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						}
					}
				} else {
					if ( $selected || 'hidden' === $layout_mode ) {
						$product_list[ $product_id ]                      = [];
						$product_list_available_variations[ $product_id ] = '';
					}
				}

				$price         = '';
				$regular_price = '';
				$price_html    = '';
				if ( $priced_individually ) {
					if ( 'variable' === $type ) {
						$price         = $product->get_variation_price();
						$regular_price = '';
					} else {
						$price         = $product->get_price();
						$regular_price = $product->get_regular_price();
					}
					if ( ! ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) ) {
						$price         = apply_filters( 'wc_epo_convert_to_currency', $price, $tc_get_default_currency, themecomplete_get_woocommerce_currency() );
						$regular_price = apply_filters( 'wc_epo_convert_to_currency', $regular_price, $tc_get_default_currency, themecomplete_get_woocommerce_currency() );
					}
					$price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $price, $discount, $discount_type );
					if ( 'variable' === $type ) {
						$price = apply_filters( 'wc_epo_product_element_initial_variable_price', $price, $price, $product );
					}
					$price_html = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_associated_price_html( $product, $discount, $discount_type );
				}

				$option = [
					'selected'               => $selected,
					'current'                => true,
					'value_to_show'          => $product_id,
					'css_class'              => $css_class,
					'data_price'             => $price,
					'data_price_html'        => $price_html,
					'tm_tooltip_html'        => '',
					'data_rules'             => wp_json_encode( [ $price ] ),
					'data_original_rules'    => wp_json_encode( [ $regular_price ] ),
					'data_rulestype'         => wp_json_encode( [ '' ] ),
					'data_text'              => $title,
					'data_type'              => $type,
					'data_hide_amount'       => $hide_amount,
					'text'                   => $title,
					'attributes'             => $attributes,
					'available_variations'   => $available_variations,
					'_default_value_counter' => '',
					'counter'                => $_default_value_counter,
					'tax_obj'                => wp_json_encode(
						( [
							'has_fee'   => $product->is_taxable(),
							'tax_class' => themecomplete_get_tax_class( $product ),
							'tax_rate'  => themecomplete_get_tax_rate( themecomplete_get_tax_class( $product ) ),
						] )
					),
				];

				if ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
					$option['_default_value_counter'] = $_default_value_counter;
				}

				$option    = apply_filters( 'wc_epo_product_option', $option, $key, $product_id, $element, $_default_value_counter );
				$options[] = $option;

				$_default_value_counter ++;
			}
		}

		$cart_data = [];
		if ( isset( $element ) && THEMECOMPLETE_EPO()->is_edit_mode() && THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				if ( isset( $cart_item['tmpost_data'] ) ) {
					$cart_data = $cart_item['tmpost_data'];
				}
			}
		}

		$labelclass       = '';
		$labelclass_start = '';
		$labelclass_end   = '';
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_css_styles ) {
			$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_end   = true;
		}

		if ( 'thumbnail' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
			$this->add_thumbnail_css( $layout_mode, $element, $args );
		}

		$display = [
			'labelclass_start'                  => $labelclass_start,
			'labelclass'                        => $labelclass,
			'labelclass_end'                    => $labelclass_end,
			'show_title'                        => $this->get_value( $element, 'show_title', '1' ),
			'show_price'                        => $this->get_value( $element, 'show_price', '1' ),
			'show_description'                  => $this->get_value( $element, 'show_description', '1' ),
			'show_meta'                         => $this->get_value( $element, 'show_meta', '1' ),
			'show_image'                        => $this->get_value( $element, 'show_image', '1' ),
			'required'                          => $this->get_value( $element, 'required', '' ),
			'hide_amount'                       => $this->get_value( $element, 'hide_amount', '' ),
			'mode'                              => $mode,
			'discount'                          => $discount,
			'discount_type'                     => $discount_type,
			'discount_exclude_addons'           => $discount_exclude_addons,
			'priced_individually'               => $priced_individually,
			'layout_mode'                       => $layout_mode,
			'categoryids'                       => $categoryids,
			'productids'                        => $productids,
			'get_default_value'                 => $get_default_value,
			'class_label'                       => $class_label,
			'placeholder'                       => $placeholder,
			'options'                           => $options,
			'quantity_min'                      => $__min_value,
			'quantity_max'                      => $__max_value,
			'quantity_default'                  => $__quantity_default_value,
			'cart_data'                         => $cart_data,
			'product_list'                      => $product_list,
			'product_list_available_variations' => $product_list_available_variations,
			'image_rel'                         => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'photoSwipe' : 'prettyPhoto',
			'variation_id'                      => isset( $args['id'] ) && isset( $_REQUEST[ $args['id'] . '_variation_id' ] ) ? absint( wp_unslash( $_REQUEST[ $args['id'] . '_variation_id' ] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		];

		return apply_filters( 'wc_epo_display_field_product', $display, $this, $element, $args );

	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		if ( ! $this->is_setup() ) {
			return false;
		}

		if ( isset( $this->key ) && '' !== $this->key ) {

			$variation_id = isset( $this->post_data[ $this->attribute . '_variation_id' ] ) ? $this->post_data[ $this->attribute . '_variation_id' ] : '';
			$attributes   = [];
			if ( $variation_id ) {
				$product = wc_get_product( $this->key );
				if ( $product ) {
					$product_attributes = $product->get_attributes();

					foreach ( $product_attributes as $attribute ) {

						if ( ! is_object( $attribute ) ) {
							continue;
						}
						if ( ! $attribute->get_variation() ) {
							continue;
						}

						$taxonomy = wc_variation_attribute_name( $attribute->get_name() );

						if ( isset( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) && '' !== $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) {

							// Get value from post data.
							if ( $attribute->is_taxonomy() ) {
								$value = sanitize_title( stripslashes_deep( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) );
							} else {
								$value = html_entity_decode( wc_clean( stripslashes_deep( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
							}

							$attributes[ $taxonomy ] = $value;
						}
					}
				}
			}

			$quantity_min = isset( $this->element['quantity_min'] ) ? $this->element['quantity_min'] : 1;
			$quantity_max = isset( $this->element['quantity_max'] ) ? $this->element['quantity_max'] : '';

			return apply_filters(
				'wc_epo_add_cart_item_data_single',
				[
					'mode'                    => 'products',
					'required'                => isset( $this->element['required'] ) ? $this->element['required'] : '',
					'priced_individually'     => isset( $this->element['priced_individually'] ) ? $this->element['priced_individually'] : '',
					'shipped_individually'    => isset( $this->element['shipped_individually'] ) ? $this->element['shipped_individually'] : '',
					'maintain_weight'         => isset( $this->element['maintain_weight'] ) ? $this->element['maintain_weight'] : '',
					'discount'                => isset( $this->element['discount'] ) ? $this->element['discount'] : '',
					'discount_type'           => isset( $this->element['discount_type'] ) ? $this->element['discount_type'] : '',
					'discount_exclude_addons' => isset( $this->element['discount_exclude_addons'] ) ? $this->element['discount_exclude_addons'] : '',
					'product_id'              => $this->key,
					'variation_id'            => $variation_id,
					'attributes'              => $attributes,
					'cssclass'                => $this->element['class'],
					'hidelabelincart'         => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'         => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'        => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'        => $this->element['hide_element_value_in_order'],
					'element'                 => $this->order_saved_element,
					'name'                    => $this->element['label'],
					'value'                   => $this->key,
					'section'                 => $this->element['uniqid'],
					'section_label'           => $this->element['label'],
					'percentcurrenttotal'     => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
					'fixedcurrenttotal'       => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
					'currencies'              => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'      => $this->fill_currencies( 1 ),
					'quantity'                => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
					'initial_quantity'        => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
					'no_change_quantity'      => $quantity_min === $quantity_max && $quantity_min && '' !== $quantity_max,
					'quantity_min'            => $quantity_min,
					'quantity_max'            => $quantity_max,
					'form_prefix'             => ( isset( $this->post_data['tc_form_prefix_assoc'] ) && isset( $this->post_data['tc_form_prefix_assoc'][ $this->element['uniqid'] ] ) ) ? wp_unslash( $this->post_data['tc_form_prefix_assoc'][ $this->element['uniqid'] ] ) : '',
					'form_prefix_counter'     => isset( $this->post_data[ $this->attribute . '_counter' ] ) ? $this->post_data[ $this->attribute . '_counter' ] : '',
					'hiddenin'                => $this->element['hiddenin'],
				],
				$this
			);

		}

		return false;
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = true;
		$message = [];

		$quantity_once = false;

		$tmcp_post_fields         = THEMECOMPLETE_EPO_HELPER()->array_filter_key( $this->post_data );
		$current_tmcp_post_fields = THEMECOMPLETE_EPO_HELPER()->array_intersect_key_wildcard( $tmcp_post_fields, array_flip( $this->field_names ) );

		foreach ( $current_tmcp_post_fields as $attribute => $value ) {
			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || '' === $this->epo_post_fields[ $attribute ] ) {
					$passed    = false;
					$message[] = 'required';
					break;
				} else {
					$product = wc_get_product( $this->epo_post_fields[ $attribute ] );
					if ( $product ) {
						$type = themecomplete_get_product_type( $product );
						if ( 'variable' === $type ) {
							if ( ! isset( $this->epo_post_fields[ $attribute . '_variation_id' ] ) || '' === $this->epo_post_fields[ $attribute . '_variation_id' ] ) {
								$passed    = false;
								$message[] = 'required';
								break;
							}
						}
					}
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

}
