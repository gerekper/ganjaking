<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Product Finder class
 */
class WooCommerce_Product_Finder {

	public static function search_filter( $query ) {

		if ( ! is_admin() ) {

			if ( ! $query->is_main_query() ) {
				return;
			}

			$query_args = array();

			// Basic arguments.
			$query_args['post_type']   = 'product';
			$query_args['post_status'] = 'publish';

			// Pagination.
			$paged               = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$query_args['paged'] = $paged;

			// Check selected taxonomies
			if ( isset( $_GET['tax'] ) && is_array( $_GET['tax'] ) && count( $_GET['tax'] ) > 0 ) {

				foreach ( $_GET['tax'] as $row => $tax ) {
					if ( 'none' !== $tax ) {
						if ( $tax && strlen( $tax ) > 0 && isset( $_GET['val'][ $row ] ) && isset( $_GET['op'][ $row ] ) ) {
							$query_args['tax_query'][] = array(
								'taxonomy' => $tax,
								'terms'    => esc_attr( $_GET['val'][ $row ] ),
								'field'    => 'slug',
								'operator' => esc_attr( $_GET['op'][ $row ] ),
							);
						}
					}
				}
			}

			// Check price input.
			$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
			$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

			// Add minimum price.
			if ( '' != $min_price ) {
				$query_args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => $min_price,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				);
			}

			// Add maximum price.
			if ( '' != $max_price ) {
				$query_args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => $max_price,
					'compare' => '<=',
					'type'    => 'NUMERIC',
				);
			}

			// Add tax query relation string.
			if ( isset( $query_args['tax_query'] ) && is_array( $query_args['tax_query'] ) ) {
				if ( isset( $_GET['relation'] ) && in_array( $_GET['relation'] , array( 'AND', 'OR' ) ) ) {
					$query_args['tax_query']['relation'] = esc_attr( $_GET['relation'] );
				}
			}

			// Check text search string.
			$string = esc_attr( $_GET['s'] );
			if ( $string && strlen( $string ) > 0 ) {
				$query_args['s'] = $string;
			} else {
				$query_args['s'] = '';
			}

			// Set query variables.
			foreach ( $query_args as $key => $value ) {
				$query->set( $key, $value );
			}
		}
	}

	public static function load_template() {
		global $woocommerce;

		wc_get_template( 'archive-product.php' );
		exit;
	}

	public static function set_search_results_body_class( $classes ) {
		$classes[] = 'woocommerce';
		return $classes;
	}

	public static function set_search_results_page_header() {
		return __( 'Search Results', 'woocommerce-product-finder' );
	}

	public static function search_form( $atts = array(), $show_cat = false ) {
		$action = get_permalink( wc_get_page_id( 'shop' ) );

		$att_string = implode( $atts , ',' );

		$html = '<form name="wc_product_finder" id="wc_product_finder" class="woocommerce" action="' . esc_url( $action ) . '" method="get">
					<fieldset>
					<legend>' . __( 'Product Finder','woocommerce-product-finder' ) . '</legend>
					<input type="hidden" id="search_attributes" value="' . $att_string . '" />
					<input type="hidden" id="show_cat" value="' . json_encode( $show_cat ) . '" />
					<input type="hidden" name="adv_search" value="wc" />
					<input type="hidden" name="post_type" value="product" />';

		$html .= self::relation_dropdown();

		if ( ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] ) && isset( $_GET['tax'][0] ) ) {
			foreach ( $_GET['tax'] as $row => $tax ) {
				$html .= self::search_row( $row , $atts , $show_cat , true );
			}
		} else {
			$html .= self::search_row( 0 , $atts , $show_cat );
		}

		$html .= '<div id="last_row" style="display:none;">0</div>
				<a href="javascript:;" id="add_row" class="add_row" title="Add search row"><span class="loader">&nbsp;</span><span class="plus">+</span> <span class="text">' . __( 'Add row', 'woocommerce-product-finder' ) . '</span></a>';

		$string = '';
		if ( isset( $_GET['s'] ) ) {
			$string = $_GET['s'];
		}
		$html .= '<div class="form-row form-row-first keywords"><label for="s">' . __( 'Keywords', 'woocommerce-product-finder' ) . '</label><input type="text" name="s" placeholder="' . __( 'Keywords', 'woocommerce-product-finder' ) . '" value="' . $string . '"/></div>';

		if ( apply_filters( 'product_finder_show_price_slider', true ) ) {
			$html .= self::price_slider();
		}

		$html .= '<div class="form-row form-row-wide form-row-submit"><input type="submit" class="button" value="' . __( 'Search' , 'woocommerce-product-finder' ) . '" /></div>
				</fieldset>
				</form>';

		return $html;
	}

	/**
	 * Get filtered min price for current products.
	 * @return int
	 */
	protected static function get_filtered_price() {
		global $wpdb, $wp_the_query;

		$args       = $wp_the_query->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " 	WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
					AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		if ( $search = WC_Query::get_main_search_query_sql() ) {
			$sql .= ' AND ' . $search;
		}

		return $wpdb->get_row( $sql );
	}

	private static function price_slider() {
		global $wpdb, $woocommerce;

		// Make sure the price slider script is registered already before trying to enqueue it here
		if ( ! wp_script_is( 'wc-price-slider', 'registered' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'wc-price-slider', $woocommerce->plugin_url() . '/assets/js/frontend/price-slider' . $suffix . '.js', array( 'jquery-ui-slider' ), '1.6', true );
		}

		wp_enqueue_script( 'wc-price-slider' );

		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

		wp_localize_script( 'wc-price-slider', 'woocommerce_price_slider_params', array(
			'currency_pos'                 => get_option( 'woocommerce_currency_pos' ),
			'min_price'                    => $min_price,
			'max_price'                    => $max_price,
			'currency_format_num_decimals' => 0,
			'currency_format_symbol'       => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
			'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
			'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
		) );

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$min = $max = 0;
			$max = ceil( $wpdb->get_var( "SELECT max( meta_value + 0 )
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
				WHERE meta_key = '_price'" ) );
		} else {
			$min = 0;
			$prices = self::get_filtered_price();
			$max    = ceil( $prices->max_price );
		}

		if ( $min == $max ) {
			return;
		}

		/**
		 * Adjust max if the store taxes are not displayed how they are stored.
		 * Min is left alone because the product may not be taxable.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
			$class_max   = $max;

			foreach ( $tax_classes as $tax_class ) {
				if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
					$class_max = $max + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
				}
			}

			$max = $class_max;
		}

		$html = '<div class="widget_price_filter form-row form-row-last">
					<div class="price_slider_wrapper">
						<div class="price_slider" style="display:none;"></div>
						<div class="price_slider_amount">
							<input type="text" id="min_price" name="min_price" value="' . esc_attr( $min ) . '" data-min="' . esc_attr( $min ) . '" placeholder="' . __( 'Min price', 'woocommerce-product-finder' ) . '" />
							<input type="text" id="max_price" name="max_price" value="' . esc_attr( $max ) . '" data-max="' . esc_attr( $max ) . '" placeholder="' . __( 'Max price', 'woocommerce-product-finder' ) . '" />
							<div class="price_label">
								' . __( 'Price:', 'woocommerce-product-finder' ) . ' <span class="from"></span> &mdash; <span class="to"></span>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>';

		return $html;
	}

	private static function relation_dropdown() {
		$selected = 'AND';
		if ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] && isset( $_GET['relation'] ) ) {
			$selected = $_GET['relation'];
		}
		$select = '<select class="relation" id="relation" name="relation">
					<option value="AND" ' . selected( $selected , 'AND' , false ) . '>' . __( 'All' , 'woocommerce-product-finder' ) . '</option>
					<option value="OR" ' . selected( $selected , 'OR' , false ) . '>' . __( 'Any' , 'woocommerce-product-finder' ) . '</option>
					</select>';

		$html = '<p class="search-intro">' . sprintf( __( 'Search for products that match %s of these criteria:' , 'woocommerce-product-finder' ) , $select ) . '</p>';

		return $html;
	}

	private static function taxonomy_operator_dropdown( $row = 0 ) {
		$selected = 'IN';
		if ( ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] ) && isset( $_GET['op'][ $row ] ) ) {
			$selected = $_GET['op'][ $row ];
		}

		$html = '<select class="operator" id="op_' . $row . '" name="op[' . $row . ']">
					<option value="IN" ' . selected( $selected , 'IN' , false ) . '>' . __( 'Is' , 'woocommerce-product-finder' ) . '</option>
					<option value="NOT IN" ' . selected( $selected , 'NOT IN' , false ) . '>' . __( 'Is not' , 'woocommerce-product-finder' ) . '</option>
				</select>';

		return $html;
	}

	private static function taxonomy_select_dropdown( $row = 0, $atts = false, $show_cat = false ) {
		global $woocommerce, $product_finder_default;

		$selected = '';
		if ( ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] ) && isset( $_GET['tax'][ $row ] ) ) {
			$selected = $_GET['tax'][ $row ];
		}

		if ( '' == $selected && $product_finder_default && strlen( $product_finder_default ) > 0 && 'none' != $product_finder_default ) {
			$selected = $product_finder_default;
		}

		$html = '<select class="taxonomy" id="tax_' . $row . '" name="tax[' . $row . ']">
					<option value="none">' . __( 'Select criteria' , 'woocommerce-product-finder' ) . '</option>';

		$att_list = false;

		if ( $atts && is_array( $atts ) ) {
			foreach ( $atts as $att ) {
				$att_list[] = (object) array( 'name' => $att );
			}
		}

		if ( $show_cat ) {
			$html .= '<option value="product_cat" ' . selected( $selected , 'product_cat' , false ) . '>' . __( 'Product Category' , 'woocommerce-product-finder' ) . '</option>';
		}

		if ( $att_list && is_array( $att_list ) && count( $att_list ) > 0 ) {

			foreach ( $att_list as $att ) {
				if ( isset( $att->name ) && strlen( $att->name ) > 0 ) {
					if ( version_compare( $woocommerce->version, '2.1-beta-1', '>=' ) ) {
						$tax_name = wc_attribute_taxonomy_name( trim( $att->name ) );
					} else {
						$tax_name = $woocommerce->attribute_taxonomy_name( trim( $att->name ) );
					}

					if ( taxonomy_exists( $tax_name ) ) {
						if ( version_compare( $woocommerce->version, '2.1-beta-1', '>=' ) ) {
							$tax_label = wc_attribute_label( $tax_name );
						} else {
							$tax_label = $woocommerce->attribute_label( $tax_name );
						}
						$html .= '<option value="' . $tax_name . '" ' . selected( $selected , $tax_name , false ) . '>' . $tax_label . '</option>';
					}
				}
			}
		}

		$html .= '</select>';

		return $html;
	}

	private static function taxonomy_value_dropdown( $row = 0, $tax = 'none' ) {
		global $product_finder_default;

		$html = '';

		$load_query = false;
		if ( ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] ) && isset( $_GET['tax'][ $row ] ) ) {
			$tax = $_GET['tax'][ $row ];
			$load_query = true;
		}

		if ( ! $load_query && $product_finder_default && strlen( $product_finder_default ) > 0 && 'none' != $product_finder_default ) {
			$tax = $product_finder_default;
			$load_query = true;
		}

		if ( 'none' === $tax ) {
			$html .= '<select class="value" id="val_' . $row . '" name="val[' . $row . ']" disabled="disabled">
						<option value="0">' . __( 'Select criteria first' , 'woocommerce-product-finder' ) . '</option>
					</select>';
		} else {

			$terms = get_terms( $tax );

			$selected = '';

			if ( $load_query ) {
				$html .= '<select class="value" id="val_' . $row . '" name="val[' . $row . ']">';

				if ( isset( $_GET['val'][ $row ] ) ) {
					$selected = $_GET['val'][ $row ];
				}
			}

			if ( $terms && is_array( $terms ) && count( $terms ) > 0 ) {

				foreach ( $terms as $term ) {
					$html .= '<option value="' . $term->slug . '" ' . selected( $selected , $term->slug , false ) . '>' . $term->name . '</option>';
				}
			}

			if ( $load_query ) {
				$html .= '</select>';
			}
		}

		return $html;
	}

	private static function search_row( $row = 0, $atts = false, $show_cat = false, $display = false ) {
		global $product_finder_default;

		if ( 0 == $row ) {
			$display = true;
		}

		if ( ! $display ) {
			$display = ' style="display:none;"';
		} else {
			$display = '';
		}

		// Get default selection
		$product_finder_default = get_option( 'advanced_search_default' );

		// Set default to none if it is not in the available attributes list
		if ( 'product_cat' === $product_finder_default ) {
			if ( ! $show_cat ) {
				$product_finder_default = 'none';
			}
		} else {
			if ( ! in_array( str_replace( 'pa_', '', $product_finder_default ), $atts ) ) {
				$product_finder_default = 'none';
			}
		}

		$html = '<div id="search_row_' . $row . '" class="search_row form-row form-row-wide"' . $display . '>';

		$html .= self::taxonomy_select_dropdown( $row , $atts , $show_cat );

		$html .= self::taxonomy_operator_dropdown( $row );

		$html .= self::taxonomy_value_dropdown( $row );

		if ( 0 != $row ) {
			$html .= '<a href="javascript:;" class="remove_row" id="remove_' . $row . '" title="Remove row"><span class="minus">&times;</span> <span class="text">' . __( 'Remove row', 'woocommerce-product-finder' ) . '</span></a>';
		}

		$html .= '</div>';

		return $html;
	}

	public static function ajax() {
		$result = false;

		switch ( $_GET['action'] ) {
			case 'wc_product_finder_get_tax_options':
				$result = self::taxonomy_value_dropdown( $_GET['row'] , $_GET['tax'] );
			break;

			case 'wc_product_finder_add_row':
				$atts = explode( ',' , $_GET['search_attributes'] );
				$result = self::search_row( $_GET['row'] , $atts , json_decode( $_GET['show_cat'] ) );
			break;
		}

		if ( $result ) {
			echo $result;
		}

		exit;
	}

}

// Filter search query when form is submitted.
if ( isset( $_GET['adv_search'] ) && 'wc' === $_GET['adv_search'] ) {
	add_filter( 'pre_get_posts' , array( 'WooCommerce_Product_finder', 'search_filter' ) );
	add_action( 'template_redirect' , array( 'WooCommerce_Product_finder', 'load_template' ) );
	add_filter( 'body_class', array( 'WooCommerce_Product_finder', 'set_search_results_body_class' ) );
	add_filter( 'woocommerce_page_title', array( 'WooCommerce_Product_finder', 'set_search_results_page_header' ) );
}

// Handle AJAX calls
add_action( 'wp_ajax_wc_product_finder_get_tax_options', array( 'WooCommerce_Product_finder', 'ajax' ) );
add_action( 'wp_ajax_nopriv_wc_product_finder_get_tax_options', array( 'WooCommerce_Product_finder', 'ajax' ) );
add_action( 'wp_ajax_wc_product_finder_add_row', array( 'WooCommerce_Product_finder', 'ajax' ) );
add_action( 'wp_ajax_nopriv_wc_product_finder_add_row', array( 'WooCommerce_Product_finder', 'ajax' ) );
