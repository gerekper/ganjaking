<?php
/**
 * Product Field class
 *
 * @package Extra Product Options/Fields
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_product extends THEMECOMPLETE_EPO_FIELDS {
	
	/**
	 * Fetch product ids
	 *
	 * @since 5.0
	 */
	public function add_thumbnail_css( $element = array(), $args = array() ) {

		$this_items_per_row   = $element['items_per_row'];
		$this_items_per_row_r = isset( $element['items_per_row_r'] ) ? $element['items_per_row_r'] : array();
		$this__percent        = 100;
		$container_css_id      = 'element_';
		if ( isset( $element['container_css_id'] ) ) {
			$container_css_id = $element['container_css_id'];
		}
		if ( ! isset( $args['product_id'] ) ) {
			$args['product_id'] = '';
		}
		
		$li_selector = ".tm-product-id-" . $args['product_id'] . " .cpf-type-product-thumbnail ul.tmcp-ul-wrap.tm-element-ul-product." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " > li.tmcp-field-wrap";

		if ( ! empty( $this_items_per_row ) ) {
			if ( $this_items_per_row == "auto" || ! is_numeric( $this_items_per_row ) || floatval( $this_items_per_row ) === 0 ) {
				$this_items_per_row = 0;
				$css_string          = $li_selector . "{-ms-flex: 0 0 auto !important;flex: 0 0 auto !important;width:auto !important;}";
			} else {
				$this_items_per_row = (float) $this_items_per_row;
				$this__percent      = (float) ( 100 / $this_items_per_row );
				$css_string          = $li_selector . "{-ms-flex: 0 0 " . $this__percent . "% !important;flex: 0 0 " . $this__percent . "% !important;max-width:" . $this__percent . "% !important;}";
			}

			$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		} else {
			$this_items_per_row = (float) $element['items_per_row'];
		}

		foreach ( $this_items_per_row_r as $key => $value ) {
			$before        = "";
			$after         = "}";

			if ( ! empty( $value ) ) {

				if ( $key !== "desktop" ) {

					switch ( $key ) {
						case 'tablets_galaxy'://800-1280
							$before = "@media only screen and (min-device-width : 800px) and (max-device-width : 1280px),only screen and (min-width : 800px) and (max-width : 1280px) {";
							break;
						case 'tablets'://768-1024
							$before = "@media only screen and (min-device-width : 768px) and (max-device-width : 1024px),only screen and (min-width : 768px) and (max-width : 1024px) {";
							break;
						case 'tablets_small'://481-767
							$before = "@media only screen and (min-device-width : 481px) and (max-device-width : 767px),only screen and (min-width : 481px) and (max-width : 767px) {";
							break;
						case 'iphone6_plus'://414-736
							$before = "@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 414px) and (max--width: 736px) {";
							break;
						case 'iphone6'://375-667
							$before = "@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 375px) and (max-width: 667px) {";
							break;
						case 'galaxy'://320-640
							$before = "@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2),only screen and (width: 320px) and (height: 640px) {";
							break;
						case 'iphone5'://320-568
							$before = "@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 320px) and (max-width: 568px) {";
							break;
						case 'smartphones'://320-480
							$before = "@media only screen and (min-device-width : 320px) and (max-device-width : 480px), only screen and (min-width : 320px) and (max-width : 480px),, only screen and (max-width : 319px){";
							break;

						default:
							# code...
							break;
					}

					$thisitems_per_row = (float) $value;
					$this_percent      = (float) ( 100 / $thisitems_per_row );
					$css_string        = $before . $li_selector . "{-ms-flex: 0 0 " . $this_percent . "% !important;flex: 0 0 " . $this_percent . "% !important;max-width:" . $this_percent . "% !important;}" . $after;

					$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
					THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );

				}

			}
		}

	}

	/**
	 * Fetch product ids
	 *
	 * @since 5.0
	 */
	public function fetch_ids( $data = array(), $args = array() ) {

		$args = wp_parse_args( $args, array(
			// Fetch mode
			'mode'     => 'products',
			// Sort retrieved posts by parameter
			'orderby'  => FALSE,
			// Number of post to show per page
			'per_page' => FALSE,
			// Number of page
			'paged'    => 1,
			// Default selected product id
			'default'  => '',
			// Enable query cache
			'cache'    => FALSE,
		) );

		if ($args['mode'] === "product"){
			$args['mode'] = "products";
		}

		$fetch_cache       = FALSE;
		$id                = $data['id'];
		$transient_name    = 'tc_product_element_query_' . $id;
		$fetch_cache_array = $args['cache'] ? get_transient( $transient_name ) : FALSE;
		$transient_version = WC_Cache_Helper::get_transient_version( 'product' );

		if ( is_array( $fetch_cache_array ) && ! isset( $fetch_cache_array['version'] ) ) {
			if ( isset( $fetch_cache_array[ $id ] ) && is_array( $fetch_cache_array[ $id ] ) ) {
				if ( isset( $fetch_cache_array[ $id ]['version'] ) && $fetch_cache_array[ $id ]['version'] === $transient_version ) {
					$fetch_cache = $fetch_cache_array[ $id ];
				}
			}
		}

		if ( $fetch_cache === FALSE ) {

			$query = array(
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'nopaging'            => TRUE,
				'order'               => 'desc',
				'fields'              => 'ids',
				'meta_query'          => array()
			);

			if ( $args['mode'] === 'products' ) {
				$query['post_type'] = array( 'product', 'product_variation' );
				$query['post__in']  = array_values( $data['productids'] );

			} elseif ( $args['mode'] === 'categories' ) {

				$query['post_type']   = 'product';
				$query['tax_query'][] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_cat',
						'terms'    => ! empty( $data['categoryids'] ) ? array_values( $data['categoryids'] ) : array( '0' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => array( 'simple', 'variable' ),
						'operator' => 'IN'
					)
				);

			}

			// Sort retrieved posts
			if ( $args['orderby'] === FALSE ) {

				if ( $args['mode'] === 'products' ) {
					// Preserve post ID order given (WC>=3.5)
					$query['orderby'] = 'post__in';
				} elseif ( $args['mode'] === 'categories' ) {
					// Order by date and title
					$query['orderby'] = 'date title';
				}

				// Stop the data retrieved from being added to the cache
				if ( $args['per_page'] === FALSE ) {
					// Disable Post term information cache
					$query['update_post_term_cache'] = FALSE;
					// Disable Post meta information cache.
					$query['update_post_meta_cache'] = FALSE;
					// Disable Post information cache
					$query['cache_results'] = FALSE;
				}

			} else {

				if ( is_array( $args['orderby'] ) ) {
					$query['orderby'] = $args['orderby']['orderby'];
					$query['order']   = $args['orderby']['order'];
				} else {
					$query['orderby'] = $args['orderby'];
				}

			}

			// Hide out of stock products
			if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' && function_exists( 'wc_get_product_visibility_term_ids' ) ) {
				// Get full list of product visibilty term ids
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();
				$query['tax_query'][]        = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN'
				);
			}

			// Pagination Parameters
			if ( $args['per_page'] ) {
				if ( $args['default'] === '' ) {
					// Enable pagination		
					$query['nopaging'] = FALSE;
					// Number of post to show per page
					$query['posts_per_page'] = $args['per_page'];
					// Number of page
					$query['paged'] = $args['paged'];
				}
			}

			// Exclude current product
			$query['post__not_in'] = array( $args['product_id'] );

			$query = new WP_Query( $query );
			$fetch = array(
				'total_pages'  => $query->max_num_pages,
				'current_page' => $query->get( 'paged' ),
				'posts'        => $query->posts,
				'found_posts'  => $query->found_posts,
			);

			if ( $args['cache'] ) {
				if ( is_array( $fetch_cache_array ) && ! isset( $fetch_cache_array['version'] ) ) {
					$fetch_cache_array[ $id ] = array_merge( $fetch, array( 'version' => $transient_version ) );
				} else {
					$fetch_cache_array = array(
						$id => array_merge(
							$fetch,
							array( 'version' => $transient_version )
						)
					);
				}

				set_transient( $transient_name, $fetch_cache_array, DAY_IN_SECONDS * 7 );
			}

		} else {
			$fetch = $fetch_cache;
		}

		if ( $args['default'] !== '' && $args['per_page'] && $args['per_page'] < $fetch['found_posts'] ) {

			$fetch        = ! empty( $fetch['posts'] ) ? $fetch['posts'] : array();
			$index        = array_search( $args['default'], $fetch ) + 1;
			$default_page = ceil( $index / $args['per_page'] );

			if ( ! empty( $fetch ) ) {

				$query = new WP_Query( array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'nopaging'            => FALSE,
					'posts_per_page'      => $args['per_page'],
					'paged'               => $default_page,
					'order'               => 'desc',
					'orderby'             => 'post__in',
					'post__in'            => $fetch,
					'fields'              => 'ids',
				) );

				$fetch = array(
					'total_pages'  => $query->max_num_pages,
					'current_page' => $query->get( 'paged' ),
					'posts'        => $query->posts,
					'found_posts'  => $query->found_posts,
				);
			}
		}

		return $fetch;

	}

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$categoryids         = isset( $element['categoryids'] ) && ! empty( $element['categoryids'] ) ? array_map( 'absint', (array) wp_unslash( $element['categoryids'] ) ) : array();
		$productids          = isset( $element['productids'] ) && ! empty( $element['productids'] ) ? array_map( 'absint', (array) wp_unslash( $element['productids'] ) ) : array();
		$layout_mode         = isset( $element['layout_mode'] ) ? $element['layout_mode'] : "";
		$placeholder         = isset( $element['placeholder'] ) ? $element['placeholder'] : "";
		$quantity_min        = isset( $element['quantity_min'] ) ? $element['quantity_min'] : 1;
		$quantity_max        = isset( $element['quantity_max'] ) ? $element['quantity_max'] : "";
		$mode                = isset( $element['mode'] ) ? $element['mode'] : "";
		$uniqid              = isset( $element['uniqid'] ) ? $element['uniqid'] : "";
		$priced_individually = isset( $element['priced_individually'] ) ? $element['priced_individually'] : "";

		if ($mode === "product"){
			$layout_mode = "hidden";
		}

		// default value
		$default_value     = isset( $element['default_value'] ) ? esc_attr( $element['default_value'] ) : '';
		$get_default_value = "";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_POST[ $args['name'] ] );
		} elseif ( isset( $_GET[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_GET[ $args['name'] ] );
		} else {
			$get_default_value = $default_value;
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		// class lable
		$class_label = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_select_fullwidth === 'yes' ) {
			$class_label = ' fullwidth';
		}

		$product_list                      = array();
		$product_list_available_variations = array();

		// populate options 
		$options = array();

		$selected_value = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $this->post_data[ 'tmcp_' . $args['name_inc'] ] ) ) {
			$selected_value = $this->post_data[ 'tmcp_' . $args['name_inc'] ];
		} elseif ( isset( $_GET[ 'tmcp_' . $args['name_inc'] ] ) ) {
			$selected_value = $_GET[ 'tmcp_' . $args['name_inc'] ];
		} elseif ( THEMECOMPLETE_EPO()->is_quick_view() || ( empty( $this->post_data ) || ( isset( $this->post_data['action'] ) && $this->post_data['action'] === 'wc_epo_get_associated_product_html' ) ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
			$selected_value = - 1;
		}

		$selected_value = apply_filters( 'wc_epo_default_value', $selected_value, $element );

		$hide_amount = empty( $element['hide_amount'] ) ? "0" : "1";

		$_default_value_counter = 0;

		if ( $layout_mode === "dropdown" ) {

			if ( $placeholder !== "" ) {
				$option    = array(
					'value_to_show'  => "",
					'data_price'     => "",
					'data_rules'     => "",
					'data_rulestype' => "",
					'text'           => wptexturize( apply_filters( 'wc_epo_kses', $placeholder, $placeholder ) ),
				);
				$options[] = $option;
			}
		}

		$__min_value              = $quantity_min;
		$__max_value              = $quantity_max;
		$__step                   = 1;
		$__quantity_default_value = '';

		if ( isset( $_POST[ $args['name'] . '_quantity' ] ) ) {
			$__quantity_default_value = stripslashes( $_POST[ $args['name'] . '_quantity' ] );
		} elseif ( isset( $_GET[ $args['name'] . '_quantity' ] ) ) {
			$__quantity_default_value = stripslashes( $_GET[ $args['name'] . '_quantity' ] );
		}

		if ( $__min_value !== '' ) {
			$__min_value = floatval( $__min_value );
		} else {
			$__min_value = 0;
		}
		if ( $__min_value === '' ) {
			$__min_value = 0;	
		}
		if ( $__max_value !== '' ) {
			$__max_value = floatval( $__max_value );
		}

		if ( empty( $__step ) ) {
			$__step = 'any';
		}

		if ($__min_value<0){
			$__min_value = 0;
		}
		if ($__max_value<0){
			$__max_value = 0;
		}

		if ( $__quantity_default_value == '' || ! is_numeric( $__quantity_default_value ) ) {
			$__quantity_default_value = $__min_value;
		}

		if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) ) {
			if ( $__min_value > $__max_value ) {
				$__max_value = $__min_value + $__step;
			}
			if ( $__quantity_default_value > $__max_value ) {
				$__quantity_default_value = $__max_value;
			}
			if ( $__quantity_default_value < $__min_value ) {
				$__quantity_default_value = $__min_value;
			}
		}

		if ($__quantity_default_value<0){
			$__quantity_default_value = 0;
		}

		$data = array(
			"id"          => $uniqid,
			"categoryids" => $categoryids,
			"productids"  => $productids,
		);

		$product_id_array = $this->fetch_ids( $data, array(
			"per_page"   => FALSE,
			"mode"       => $mode,
			"default"    => $get_default_value,
			"product_id" => $args['product_id'],
		) );

		foreach ( $product_id_array["posts"] as $key => $product_id ) {

			$is_default_value = isset( $element['default_value'] )
				?
				( ( $element['default_value'] !== "" )
					? ( (int) $element['default_value'] == $product_id )
					: FALSE )
				: FALSE;

			$selected = FALSE;

			if ( $selected_value == - 1 ) {
				if ( 
					( 
						THEMECOMPLETE_EPO()->is_quick_view() || 
						( 
							empty( $this->post_data ) || 
							( 
								isset( $this->post_data['action'] ) && $this->post_data['action'] === 'wc_epo_get_associated_product_html' 
							) 
						) || 
						THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" 
					) && isset( $is_default_value ) ) {
					if ( $is_default_value ) {
						$selected = TRUE;
					}
				}
			} else {
				if ( $is_default_value && ! empty( $element['default_value_override'] ) && isset( $element['default_value'] ) ) {
					$selected = TRUE;
				} elseif ( esc_attr( stripcslashes( $selected_value ) ) == esc_attr( $product_id ) ) {
					$selected = TRUE;
				}
			}

			if ($layout_mode === "hidden" && $__quantity_default_value>0 && $__min_value===$__max_value){
				$selected = TRUE;
			}

			$css_class = apply_filters( 'wc_epo_multiple_options_css_class', '', $element, $_default_value_counter );
			if ( $css_class !== '' ) {
				$css_class = ' ' . $css_class;
			}

			$product = wc_get_product( $product_id );

			if ( ! empty( $product ) && is_object( $product ) ) {
				$price                = $product->get_price();
				$regular_price        = $product->get_regular_price();
				$price_html           = $product->get_price_html();
				$title                = $product->get_title();
				$type                 = themecomplete_get_product_type( $product );
				$attributes           = array();
				$available_variations = array();

				if ( $type === "variable" ) {
					if ( ($selected || $layout_mode === "hidden") && is_callable( array( $product, 'get_variation_attributes' ) ) ) {
						$attributes = $product->get_variation_attributes();

						$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

						$available_variations = $get_variations ? $product->get_available_variations() : FALSE;

						$product_list[ $product_id ] = $attributes;

						$variations_json = wp_json_encode( $available_variations );
						$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', TRUE );

						$product_list_available_variations[ $product_id ] = $variations_attr;
					}

					$price         = apply_filters( 'wc_epo_product_element_initial_variable_price', "", $price, $product );
					$regular_price = "";
				} else {
					if ($selected || $layout_mode === "hidden") {
						$product_list[ $product_id ]                      = array();
						$product_list_available_variations[ $product_id ] = "";
					}
				}

				if ( ! $priced_individually ) {
					$price         = "";
					$regular_price = "";
				}

				$option = array(
					'selected'             => $selected,
					'current'              => TRUE,
					'value_to_show'        => $product_id,
					'css_class'            => $css_class,
					'data_price'           => $price,
					'tm_tooltip_html'      => '',
					'data_rules'           => wp_json_encode( array( $price ) ),
					'data_original_rules'  => wp_json_encode( array( $regular_price ) ),
					'data_rulestype'       => wp_json_encode( array( '' ) ),
					'data_text'            => $title,
					'data_type'            => $type,
					'data_hide_amount'     => $hide_amount,
					'text'                 => $title,
					'attributes'           => $attributes,
					'available_variations' => $available_variations,
				);

				$option    = apply_filters( 'wc_epo_product_option', $option, $key, $product_id, $element, $_default_value_counter );
				$options[] = $option;

				$_default_value_counter ++;
			}

		}

		$cart_data = array();
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
		if ( THEMECOMPLETE_EPO()->tm_epo_css_styles == "on" ) {
			$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_end   = TRUE;
		}

		if ( $layout_mode === "thumbnail" ) {
			$this->add_thumbnail_css($element, $args);
		}

		return array(
			'labelclass_start' => $labelclass_start,
			'labelclass'       => $labelclass,
			'labelclass_end'   => $labelclass_end,
			'required'                          => isset( $element['required'] ) ? $element['required'] : "",
			'hide_amount'                       => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'mode'                              => isset( $element['mode'] ) ? $element['mode'] : "",
			'discount'                          => isset( $element['discount'] ) ? $element['discount'] : "",
			'discount_type'                     => isset( $element['discount_type'] ) ? $element['discount_type'] : "",
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
			'variation_id'                      => isset( $args['id'] ) && isset( $_REQUEST[ $args['id'] . '_variation_id' ] ) ? $_REQUEST[ $args['id'] . '_variation_id' ] : '',
		);

	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		if ( ! $this->is_setup() ) {
			return FALSE;
		}

		if ( isset( $this->key ) && $this->key != '' ) {

			$variation_id = isset( $this->post_data[ $this->attribute . '_variation_id' ] ) ? $this->post_data[ $this->attribute . '_variation_id' ] : '';
			$attributes   = array();
			if ( $variation_id ) {
				$product = wc_get_product( $this->key );
				if ( $product ) {
					$product_attributes = $product->get_attributes();

					foreach ( $product_attributes as $attribute ) {

						if ( ! $attribute->get_variation() ) {
							continue;
						}

						$taxonomy = wc_variation_attribute_name( $attribute->get_name() );

						if ( isset( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) && '' !== $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) {

							// Get value from post data.
							if ( $attribute->is_taxonomy() ) {
								$value = sanitize_title( stripslashes( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) );
							} else {
								$value = html_entity_decode( wc_clean( stripslashes( $this->post_data[ $this->attribute . '_attribute_' . $taxonomy ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
							}

							$attributes[ $taxonomy ] = $value;
						}
					}
				}
			}

			return apply_filters( 'wc_epo_add_cart_item_data_single', array(
				'mode'                 => 'products',
				"required"             => isset( $this->element['required'] ) ? $this->element['required'] : "",
				"priced_individually"  => isset( $this->element['priced_individually'] ) ? $this->element['priced_individually'] : "",
				"shipped_individually" => isset( $this->element['shipped_individually'] ) ? $this->element['shipped_individually'] : "",
				"maintain_weight"      => isset( $this->element['maintain_weight'] ) ? $this->element['maintain_weight'] : "",
				"discount"             => isset( $this->element['discount'] ) ? $this->element['discount'] : "",
				"discount_type"        => isset( $this->element['discount_type'] ) ? $this->element['discount_type'] : "",
				"product_id"           => $this->key,
				"variation_id"         => $variation_id,
				"attributes"           => $attributes,
				'cssclass'             => $this->element['class'],
				'hidelabelincart'      => $this->element['hide_element_label_in_cart'],
				'hidevalueincart'      => $this->element['hide_element_value_in_cart'],
				'hidelabelinorder'     => $this->element['hide_element_label_in_order'],
				'hidevalueinorder'     => $this->element['hide_element_value_in_order'],
				'element'              => $this->order_saved_element,
				'name'                 => $this->element['label'],
				'value'                => $this->key,
				'section'              => $this->element['uniqid'],
				'section_label'        => $this->element['label'],
				'percentcurrenttotal'  => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
				'fixedcurrenttotal'    => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
				'currencies'           => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'   => $this->fill_currencies(),
				'quantity'             => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
				"quantity_min"         => isset( $this->element['quantity_min'] ) ? $this->element['quantity_min'] : 1,
				"quantity_max"         => isset( $this->element['quantity_max'] ) ? $this->element['quantity_max'] : "",
			), $this );

		}

		return FALSE;
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = TRUE;
		$message = array();

		$quantity_once = FALSE;

		foreach ( $this->field_names as $attribute ) {

			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || $this->epo_post_fields[ $attribute ] == "" ) {
					$passed    = FALSE;
					$message[] = 'required';
					break;
				} else {
					$product = wc_get_product( $this->epo_post_fields[ $attribute ] );
					if ( $product ) {
						$type = themecomplete_get_product_type( $product );
						if ( $type === "variable" ) {
							if ( ! isset( $this->epo_post_fields[ $attribute . '_variation_id' ] ) || $this->epo_post_fields[ $attribute . '_variation_id' ] == "" ) {
								$passed    = FALSE;
								$message[] = 'required';
								break;
							}
						}
					}
				}

			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}

}
