<?php
/**
 * class-woocommerce-product-search-filter.php
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

if ( !function_exists( 'woocommerce_product_filter' ) ) {
	/**
	 * Renders a product filter form which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired filter options
	 *
	 * @return string form HTML
	 */
	function woocommerce_product_filter( $atts = array() ) {
		return WooCommerce_Product_Search_Filter::render( $atts );
	}
}

if ( !function_exists( 'woocommerce_product_filter_products' ) ) {
	/**
	 * Renders the products section based on the shop loop.
	 *
	 * @param array $atts desired options
	 *
	 * @return string HTML
	 */
	function woocommerce_product_filter_products( $atts = array() ) {
		return WooCommerce_Product_Search_Filter::render_products( $atts );
	}
}

/**
 * Filter
 */
class WooCommerce_Product_Search_Filter {

	private static $fields = 0;

	private static $filters = 0;

	const CACHE_GROUP = 'ixwps_filter';

	/**
	 * Adds shortcodes.
	 */
	public static function init() {

		add_shortcode( 'woocommerce_product_filter', array( __CLASS__, 'shortcode' ) );

		add_shortcode( 'woocommerce_product_filter_products', array( __CLASS__, 'shortcode_products' ) );

		add_action( 'wp_footer', array( __CLASS__, 'wp_footer' ) );
		add_action( 'shutdown', array( __CLASS__, 'shutdown' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$options = get_option( 'woocommerce-product-search', array() );
		$enable_css = isset( $options[WooCommerce_Product_Search::ENABLE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_CSS] : WooCommerce_Product_Search::ENABLE_CSS_DEFAULT;
		wp_enqueue_script( 'typewatch' );
		wp_enqueue_script( 'product-filter' );
		if ( $enable_css ) {
			wp_enqueue_style( 'product-search' );
		}
	}

	/**
	 * Shortcode handler to renders a product filter form.
	 *
	 * @param array $atts
	 * @param array $content not used
	 *
	 * @return string form HTML
	 */
	public static function shortcode( $atts = array(), $content = '' ) {
		return self::render( $atts );
	}

	/**
	 * Renders the product filter form.
	 *
	 * Enqueues required scripts and styles.
	 *
	 * @param array $atts
	 * @param array $results
	 *
	 * @return string form HTML
	 */
	public static function render( $atts = array(), &$results = null ) {

		self::load_resources();

		$atts = shortcode_atts(
			array(

				'title'                     => 'yes',
				'excerpt'                   => 'yes',
				'content'                   => 'yes',
				'categories'                => 'yes',
				'attributes'                => 'yes',
				'tags'                      => 'yes',
				'sku'                       => 'yes',

				'order'                     => null,
				'order_by'                  => null,

				'placeholder'               => __( 'Search', 'woocommerce-product-search' ),
				'blinker_timeout'           => null,
				'delay'                     => WooCommerce_Product_Search::DEFAULT_DELAY,
				'characters'                => WooCommerce_Product_Search::DEFAULT_CHARACTERS,
				'submit_button'             => 'no',
				'submit_button_label'       => __( 'Search', 'woocommerce-product-search' ),
				'show_clear'                => 'yes',

				'update_address_bar'        => 'yes',
				'update_document_title'     => 'no',

				'use_shop_url'              => 'no',

				'unpage_url'                => 'yes',

				'breadcrumb_container'      => '.woocommerce-breadcrumb',
				'products_header_container' => '.woocommerce-products-header',
				'products_container'        => '.products',
				'product_container'         => '.product',
				'info_container'            => '.woocommerce-info',
				'ordering_container'        => '.woocommerce-ordering',
				'pagination_container'      => '.woocommerce-pagination',
				'result_count_container'    => '.woocommerce-result-count',

				'style'                     => '',

				'heading'                   => null,
				'heading_class'             => null,
				'heading_element'           => 'div',
				'heading_id'                => null,
				'show_heading'              => 'no'
			),
			$atts
		);

		if ( $atts['heading'] === null ) {
			$atts['heading']  = _x( 'Search', 'product search filter heading', 'woocommerce-product-search' );
		}

		$url_params = array();
		foreach ( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'order' :
					case 'order_by' :
						break;
					case 'title' :
					case 'excerpt' :
					case 'content' :
					case 'categories' :
					case 'attributes' :
					case 'tags' :
					case 'sku' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					default :
						$add = false;
				}
				if ( $add ) {
					$url_params[$key] = urlencode( $value );
				}
			}
		}

		$params = array();
		foreach ( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'show_clear' :
					case 'show_heading' :
					case 'submit_button' :

					case 'update_address_bar' :
					case 'update_document_title' :
					case 'use_shop_url' :
					case 'unpage_url' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'delay' :
					case 'characters' :
					case 'blinker_timeout' :
						$value = intval( $value );
						break;
					case 'placeholder' :
					case 'style' :
					case 'submit_button_label' :
						$value = trim( $value );
						break;
					case 'breadcrumb_container' :
					case 'products_header_container' :
					case 'products_container' :
					case 'product_container' :
					case 'info_container' :
					case 'ordering_container' :
					case 'pagination_container' :
					case 'result_count_container' :
					case 'heading_class' :
					case 'heading_id' :
						$value = preg_replace( '/[^a-zA-Z0-9 _.#-]/', '', $value );
						$value = trim( $value );
						break;
					case 'heading_element' :
						if ( !in_array( $value, self::get_allowed_filter_heading_elements() ) ) {
							$value = 'div';
						}
						break;
					case 'heading' :
						$value = esc_html( $value );
						break;
					default :
						$add = false;
				}
				if ( $add ) {
					$params[$key] = $value;
				}
			}
		}

		$heading_class = 'product-search-filter-search-heading';
		$heading_id    = sprintf( 'product-search-filter-search-heading-%s', md5( json_encode( $atts ) ) );
		if ( !empty( $params['heading_class'] ) ) {
			$heading_class = $params['heading_class'];
		}
		if ( !empty( $params['heading_id'] ) ) {
			$heading_id = $params['heading_id'];
		}

		if ( $params['delay'] < WooCommerce_Product_Search::MIN_DELAY ) {
			$params['delay'] = WooCommerce_Product_Search::MIN_DELAY;
		}
		if ( $params['characters'] < WooCommerce_Product_Search::MIN_CHARACTERS ) {
			$params['characters'] = WooCommerce_Product_Search::MIN_CHARACTERS;
		}
		$params['placeholder'] = apply_filters( 'woocommerce_product_filter_placeholder', $params['placeholder'] );

		$output = '';

		$product_search = true;

		$n          = self::$fields;
		$search_id  = 'product-filter-search-' . $n;
		$form_id    = 'product-filter-search-form-' . $n;
		$field_id   = 'product-filter-field-' . $n;
		$results_id = 'product-filter-results-' . $n;
		$ixwpss     = !empty( $_REQUEST['ixwpss'] ) ? $_REQUEST['ixwpss'] : '';

		$output .= self::inline_styles();

		$output .= sprintf(
			'<div id="%s" class="product-search product-filter product-search-filter-search" style="%s">',
			esc_attr( $search_id ),
			!empty( $params['style'] ) ? esc_attr( $params['style'] ) : ''
		);

		$heading_output = '';
		if ( $params['show_heading'] ) {
			$heading_output .= sprintf(
				'<%s class="%s" id="%s">%s</%s>',
				esc_html( $params['heading_element'] ),
				esc_attr( $heading_class ),
				esc_attr( $heading_id ),
				esc_html( $params['heading'] ),
				esc_html( $params['heading_element'] )
			);
		}
		$output .= $heading_output;

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg( array( 'ixwpss', 'title', 'excerpt', 'content', 'categories', 'attributes', 'tags', 'sku', 'lang', 'paged' ), $current_url );
		$href        = $current_url;
		$add_post_type = false;
		if ( isset( $params['use_shop_url'] ) && $params['use_shop_url'] ) {
			$href = get_permalink( wc_get_page_id( 'shop' ) );
			if ( !$href ) {
				$query_post_type = self::get_query_arg( $current_url, 'post_type' );

				if ( $query_post_type !== 'product' ) {

					$href = add_query_arg( array( 'post_type' => 'product' ), trailingslashit( home_url() ) );
					$add_post_type = true;
				}
			}
		}

		$reset_url = WooCommerce_Product_Search_Filter_Reset::get_reset_url();

		$output .= '<div class="product-search-form">';
		$output .= sprintf(
			'<form id="%s" class="product-search-form %s" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_attr( $params['submit_button'] ? 'show-submit-button' : '' ),
			esc_url( $href )
		);
		$output .= sprintf(
			'<input id="%s" name="ixwpss" type="text" class="product-filter-field" placeholder="%s" autocomplete="off" value="%s"/>',
			esc_attr( $field_id ),
			esc_attr( $params['placeholder'] ),
			esc_attr( $ixwpss )
		);
		if ( $add_post_type ) {
			$output .= '<input type="hidden" name="post_type" value="product"/>';
		}
		$output .= self::render_query_args_form_fields( $current_url );
		$output .= sprintf( '<input type="hidden" name="title" value="%d"/>', isset( $url_params['title'] ) && $url_params['title'] || !isset( $url_params['title'] ) && WooCommerce_Product_Search_Service::DEFAULT_TITLE ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="excerpt" value="%d"/>', isset( $url_params['excerpt'] ) && $url_params['excerpt'] || !isset( $url_params['excerpt'] ) && WooCommerce_Product_Search_Service::DEFAULT_EXCERPT ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="content" value="%d"/>', isset( $url_params['content'] ) && $url_params['content'] || !isset( $url_params['content'] ) && WooCommerce_Product_Search_Service::DEFAULT_CONTENT ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="categories" value="%d"/>', isset( $url_params['categories'] ) && $url_params['categories'] || !isset( $url_params['categories'] ) && WooCommerce_Product_Search_Service::DEFAULT_CATEGORIES ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="attributes" value="%d"/>', isset( $url_params['attributes'] ) && $url_params['attributes'] || !isset( $url_params['attributes'] ) && WooCommerce_Product_Search_Service::DEFAULT_ATTRIBUTES ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="tags" value="%d"/>', isset( $url_params['tags'] ) && $url_params['tags'] || !isset( $url_params['tags'] ) && WooCommerce_Product_Search_Service::DEFAULT_TAGS ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="sku" value="%d"/>', isset( $url_params['sku'] ) && $url_params['sku'] || !isset( $url_params['sku'] ) && WooCommerce_Product_Search_Service::DEFAULT_SKU ? 1 : 0 );

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$output .= sprintf( '<input type="hidden" name="lang" value="%s"/>', ICL_LANGUAGE_CODE );
		}
		if ( $params['submit_button'] ) {
			$output .= ' ';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
		} else {
			$output .= '<noscript>';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
			$output .= '</noscript>';
		}
		if ( $params['show_clear'] ) {
			$output .= sprintf(
				'<span class="product-search-filter-search-clear" style="%s">',
				( empty( $ixwpss ) || strlen( $ixwpss ) === 0 ? 'display:none' : '' )
			);
			$output .= __( 'Clear', 'woocommerce-product-search' );
			$output .= '</span>';
		}

		$output .= '</form>';
		$output .= '</div>';

		$output .= sprintf( '<div id="%s" class="product-filter-results">', $results_id );
		$output .= '</div>';

		$output .= '</div>';

		$js_args = array();
		if ( isset( $params['blinker_timeout'] ) ) {
			$blinker_timeout = max( array( 0, intval( $params['blinker_timeout'] ) ) );
			$js_args[] = 'blinkerTimeout:' . $blinker_timeout;
		}
		$js_args[] = sprintf( 'title:%d', isset( $url_params['title'] ) && $url_params['title'] || !isset( $url_params['title'] ) && WooCommerce_Product_Search_Service::DEFAULT_TITLE ? 1 : 0 );
		$js_args[] = sprintf( 'excerpt:%d', isset( $url_params['excerpt'] ) && $url_params['excerpt'] || !isset( $url_params['excerpt'] ) && WooCommerce_Product_Search_Service::DEFAULT_EXCERPT ? 1 : 0 );
		$js_args[] = sprintf( 'content:%d', isset( $url_params['content'] ) && $url_params['content'] || !isset( $url_params['content'] ) && WooCommerce_Product_Search_Service::DEFAULT_CONTENT ? 1 : 0 );
		$js_args[] = sprintf( 'categories:%d', isset( $url_params['categories'] ) && $url_params['categories'] || !isset( $url_params['categories'] ) && WooCommerce_Product_Search_Service::DEFAULT_CATEGORIES ? 1 : 0 );
		$js_args[] = sprintf( 'attributes:%d', isset( $url_params['attributes'] ) && $url_params['attributes'] || !isset( $url_params['attributes'] ) && WooCommerce_Product_Search_Service::DEFAULT_ATTRIBUTES ? 1 : 0 );
		$js_args[] = sprintf( 'tags:%d', isset( $url_params['tags'] ) && $url_params['tags'] || !isset( $url_params['tags'] ) && WooCommerce_Product_Search_Service::DEFAULT_TAGS ? 1 : 0 );
		$js_args[] = sprintf( 'sku:%d', isset( $url_params['sku'] ) && $url_params['sku'] || !isset( $url_params['sku'] ) && WooCommerce_Product_Search_Service::DEFAULT_SKU ? 1 : 0 );

		if ( !empty( $url_params['order_by'] ) ) {
			$js_args[] = sprintf( 'order_by:"%s"', esc_attr( $url_params['order_by'] ) );
		}
		if ( !empty( $url_params['order'] ) ) {
			$js_args[] = sprintf( 'order:"%s"', esc_attr( $url_params['order'] ) );
		}

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$js_args[] = sprintf( 'lang:"%s"', esc_attr( ICL_LANGUAGE_CODE ) );
		}
		if ( isset( $params['update_address_bar'] ) ) {
			$js_args[] = 'updateAddressBar:' . ( $params['update_address_bar'] ? 'true' : 'false' );
		}
		if ( isset( $params['update_document_title'] ) ) {
			$js_args[] = 'updateDocumentTitle:' . ( $params['update_document_title'] ? 'true' : 'false' );
		}
		if ( !empty( $ixwpss ) ) {
			$js_args[] = sprintf( 'ixwpss:"%s"', esc_attr( $ixwpss ) );
		}
		if ( isset( $params['use_shop_url'] ) && $params['use_shop_url'] ) {
			$js_args[] = sprintf( 'href:"%s"', esc_attr( $href ) );
		}
		if ( isset( $params['unpage_url'] ) ) {
			$js_args[] = 'unpage_url:' . ( $params['unpage_url'] ? 'true' : 'false' );
		}
		$js_args = '{' . implode( ',', $js_args ) . '}';

		$output .= '<script type="text/javascript">';

		$output .= 'document.getElementById("' . $field_id . '").disabled = true;';

		$output .= 'document.addEventListener( "DOMContentLoaded", function() {';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'if ( typeof jQuery().typeWatch !== "undefined" ) {';
		if ( !apply_filters( 'woocommerce_product_search_auto_toggle_filter_widgets', true ) ) {
			$output .= 'if ( typeof ixwpsf !== "undefined" ) { ixwpsf.autoToggleFilterWidgets = false; }';
		}
		$containers = array(
			sprintf( 'field:"%s"',      esc_attr( '#' . $field_id ) ),
			sprintf( 'breadcrumb:"%s"', esc_attr( $params['breadcrumb_container'] ) ),
			sprintf( 'header:"%s"',     esc_attr( $params['products_header_container'] ) ),
			sprintf( 'products:"%s"',   esc_attr( $params['products_container'] ) ),
			sprintf( 'product:"%s"',   esc_attr( $params['product_container'] ) ),
			sprintf( 'info:"%s"',       esc_attr( $params['info_container'] ) ),
			sprintf( 'ordering:"%s"',   esc_attr( $params['ordering_container'] ) ),
			sprintf( 'pagination:"%s"', esc_attr( $params['pagination_container'] ) ),
			sprintf( 'count:"%s"',      esc_attr( $params['result_count_container'] ) )
		);
		$containers = '{' . implode( ',', $containers ) . '}';

		$output .= sprintf(
			'jQuery("#%s").typeWatch( {' .
			'callback: function (value) { ixwpsf.productFilter(value, %s, %s); },' .
			'wait: %d,' .
			'highlight: true,' .
			'captureLength: %d' .
			'} );',
			esc_attr( $field_id ),
			$containers,
			$js_args,
			$params['delay'],
			$params['characters']
		);

		$output .= sprintf(
			'jQuery("#%s").on("input", function() {' .
			'var query = jQuery(this).val();' .
			'if ((query.length < %d) && (jQuery.trim(query) == "")) {' .
			'ixwpsf.productFilter("", %s, %s);' .
			'}' .
			'} );',
			esc_attr( $field_id ),
			$params['characters'],
			$containers,
			$js_args
		);

		$output .= sprintf(
			'jQuery("#%s").on("ixTermFilter", function(e,term,taxonomy,action,origin_id) {' .
			'var query = jQuery(this).val();' .
			'switch( action ) {' .
			'case "replace":' .
			'case "add":' .
			'case "remove":' .
			'break;' .
			'default:' .
			'action = "replace";' .
			'}' .
			'ixwpsf.productFilter(query, %s, jQuery.extend({},%s,{term:term,taxonomy:taxonomy,action:action,origin_id:origin_id}));' .
			'} );',
			esc_attr( $field_id ),
			$containers,
			$js_args
		);

		$output .= sprintf(
			'jQuery("#%s").on("ixPriceFilter", function(e,min_price,max_price) {' .
			'var query = jQuery(this).val();' .
			'ixwpsf.productFilter(query, %s, jQuery.extend({},%s,{min_price:min_price,max_price:max_price}));' .
			'} );',
			esc_attr( $field_id ),
			$containers,
			$js_args
		);

		$output .= sprintf(
			'jQuery("#%s").on("ixExtraFilter", function(e,extras) {' .
			'var query = jQuery(this).val();' .
			'ixwpsf.productFilter(query, %s, jQuery.extend({},%s,extras));' .
			'} );',
			esc_attr( $field_id ),
			$containers,
			$js_args
		);

		$output .= sprintf(
			'jQuery("#%s").on("ixFilterReset", function(e) {' .
			'var query = jQuery(this).val();' .
			'ixwpsf.productFilter(query, %s, jQuery.extend({},%s,{reset:true,reset_url:"%s"}));' .
			'} );',
			esc_attr( $field_id ),
			$containers,
			$js_args,
			esc_url_raw( $reset_url )

		);

		$output .= '} else {';
		$output .= 'if ( typeof console !== "undefined" && typeof console.log !== "undefined" ) { ';
		$output .= 'document.getElementById("' . $field_id . '").disabled = false;';
		$output .= 'console.log("A conflict is preventing required resources to be loaded.");';
		$output .= '}';
		$output .= '}';
		$output .= '}';
		$output .= '} );';
		$output .= '</script>';

		self::field_added();

		return $output;
	}

	/**
	 * Prefix open
	 */
	public static function filter_products_prefix_open() {

		echo apply_filters(
			'woocommerce_product_search_filter_products_prefix_open',
			'<div class="woocommerce-product-search-filter-products-prefix">'
		);

	}

	/**
	 * Prefix close
	 */
	public static function filter_products_prefix_close() {

		echo apply_filters(
			'woocommerce_product_search_filter_products_prefix_close',
			'</div>'
		);

	}

	/**
	 * Suffix open
	 */
	public static function filter_products_suffix_open() {

		echo apply_filters(
			'woocommerce_product_search_filter_products_suffix_open',
			'<div class="woocommerce-product-search-filter-products-suffix">'
		);

	}

	/**
	 * Suffix close
	 */
	public static function filter_products_suffix_close() {

		echo apply_filters(
			'woocommerce_product_search_filter_products_suffix_close',
			'</div>'
		);

	}

	/**
	 * Result count
	 */
	public static function result_count() {

		global $wps_products;

		$output = '<p class="woocommerce-result-count">';

		$paged    = max( 1, $wps_products->get( 'paged' ) );
		$per_page = $wps_products->get( 'posts_per_page' );
		$total    = $wps_products->found_posts;
		$first    = ( $per_page * $paged ) - $per_page + 1;
		$last     = min( $total, $wps_products->get( 'posts_per_page' ) * $paged );

		if ( $total == 0 ) {
			$output .= esc_html( __( 'No results', 'woocommerce' ) );
		} else if ( $total <= $per_page || -1 === $per_page ) {

			/* translators: %d: total results */
			$output .= esc_html( sprintf( _n( 'Showing the single result', 'Showing all %d results', $total, 'woocommerce' ), $total ) );

		} else {

			/* translators: 1: first result 2: last result 3: total results */
			$output .= esc_html( sprintf( _nx( 'Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'woocommerce-product-search' ), $first, $last, $total ) );

		}
		$output .= '</p>';

		echo apply_filters(
			'woocommerce_product_search_filter_products_result_count',
			$output
		);

	}

	/**
	 * Catalog ordering
	 */
	public static function catalog_ordering() {

		global $wps_products;

		$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => __( 'Default sorting', 'woocommerce' ),
				'popularity' => __( 'Sort by popularity', 'woocommerce' ),
				'rating'     => __( 'Sort by average rating', 'woocommerce' ),
				'date'       => __( 'Sort by newness', 'woocommerce' ),
				'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
			)
		);

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
			unset( $catalog_orderby_options['rating'] );
		}

		ob_start();
		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby
			)
		);

		echo apply_filters(
			'woocommerce_product_search_filter_products_catalog_ordering',
			ob_get_clean()
		);

	}

	/**
	 * Pagination
	 */
	public static function pagination() {

		global $wp_query, $wps_products, $wps_products_paged;

		$paged = max( 1, intval( $wp_query->get( 'paged', 1 ) ) );
		if ( isset( $wp_query->query['paged'] ) ) {
			$query_paged = max( 1, intval( $wp_query->query['paged'] ) );
			if ( $query_paged !== $paged ) {
				$paged = $query_paged;
			}
		}

		if ( ( $paged > $wps_products->max_num_pages ) && isset( $wps_products_paged ) ) {
			$paged = $wps_products_paged;
		}

		$output = '';

		$output .= '<nav class="woocommerce-pagination">';
		if ( $wps_products->max_num_pages > 1 ) {
			$output .= paginate_links(
				apply_filters(
					'woocommerce_pagination_args',
					array(
						'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
						'format'    => '',
						'add_args'  => false,
						'current'   => $paged,
						'total'     => intval( $wps_products->max_num_pages ),
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				)
			);
		}
		$output .= '</nav>';

		echo apply_filters(
			'woocommerce_product_search_filter_products_pagination',
			$output
		);

	}

	/**
	 * [woocommerce_product_filter_products] shortcode handler.
	 * Used to render the product loop.
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode_products( $atts = [], $content = '' ) {
		return self::render_products( $atts );
	}

	/**
	 * Renders the product loop.
	 *
	 * @param array $atts
	 * @param null $results
	 * @return string
	 */
	public static function render_products( $atts = [], &$results = null ) {

		$output = '';

		$atts = shortcode_atts(
			array(
				'columns'               => 3,
				'orderby'               => '',
				'order'                 => '',
				'per_page'              => 12,
				'show_prefix'           => 'yes',
				'show_suffix'           => 'no',
				'show_catalog_ordering' => 'yes',
				'show_result_count'     => 'yes',
				'show_pagination'       => 'yes',
				'taxonomy'              => '',
				'term'                  => '',
				'taxonomy_op'           => 'OR'
			),
			$atts,
			'woocommerce_product_filter_products'
		);

		$atts['columns'] = intval( $atts['columns'] );
		foreach ( $atts as $key => $value ) {
			$valid = true;
			switch ( $key ) {
				case 'columns' :
				case 'per_page' :
					$value = abs( trim( $value ) );
					break;
				case 'orderby' :

					$value = preg_replace( '/[^a-zA-Z0-9 _.-]/', '', $value );
					$value = trim( $value );
					break;
				case 'order' :
					$value = strtoupper( trim( $value ) );
					switch ( $value ) {
						case 'ASC' :
						case 'DESC' :
							break;
						default :
							$value = 'ASC';
					}
					break;
				case 'show_prefix' :
				case 'show_suffix' :
				case 'show_catalog_ordering' :
				case 'show_result_count' :
				case 'show_pagination' :
					if (
						$value === false ||
						strtolower( trim( $value ) ) === 'no' ||
						strtolower( trim( $value ) ) === 'false'
					) {
						$value = false;
					} else {
						$value = true;
					}
					break;
				case 'taxonomy' :
				case 'term' :
					if ( is_string( $value ) ) {
						$value = array_map( 'trim', explode( ',', $value ) );
					}
					break;
				case 'taxonomy_op' :
					$value = strtoupper( $value );
					switch( $value ) {
						case 'OR' :
						case 'AND' :
							break;
						default:
							$value = 'OR';
					}
					break;
				default :
					$valid = false;
			}
			if ( $valid ) {
				$atts[$key] = $value;
			} else {
				unset( $atts[$key] );
			}
		}

		if ( $atts['show_prefix'] ) {
			add_action( 'woocommerce_before_filter_products_loop', array( __CLASS__, 'filter_products_prefix_open' ), 10 );
			if ( $atts['show_catalog_ordering'] ) {
				add_action( 'woocommerce_before_filter_products_loop', array( __CLASS__, 'catalog_ordering' ), 20 );
			}
			if ( $atts['show_result_count'] ) {
				add_action( 'woocommerce_before_filter_products_loop', array( __CLASS__, 'result_count' ), 30 );
			}
			if ( $atts['show_pagination'] ) {
				add_action( 'woocommerce_before_filter_products_loop', array( __CLASS__, 'pagination' ), 40 );
			}
			add_action( 'woocommerce_before_filter_products_loop', array( __CLASS__, 'filter_products_prefix_close' ), 50 );
		}

		if ( $atts['show_suffix'] ) {
			add_action( 'woocommerce_after_filter_products_loop', array( __CLASS__, 'filter_products_suffix_open' ), 10 );
			if ( $atts['show_catalog_ordering'] ) {
				add_action( 'woocommerce_after_filter_products_loop', array( __CLASS__, 'catalog_ordering' ), 20 );
			}
			if ( $atts['show_result_count'] ) {
				add_action( 'woocommerce_after_filter_products_loop', array( __CLASS__, 'result_count' ), 30 );
			}
			if ( $atts['show_pagination'] ) {
				add_action( 'woocommerce_after_filter_products_loop', array( __CLASS__, 'pagination' ), 40 );
			}
			add_action( 'woocommerce_after_filter_products_loop', array( __CLASS__, 'filter_products_suffix_close' ), 50 );
		}

		add_action( 'woocommerce_filter_products_loop_no_results', 'wc_no_products_found' );

		$tax_query = array();

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => $atts['per_page'],
			'meta_query'          => WC()->query->get_meta_query(),
			'tax_query'           => WC()->query->get_tax_query( $tax_query ),
		);

		$output .= self::product_loop( $query_args, $atts, 'filter_products' );

		return $output;
	}

	/**
	 * Renders the product loop.
	 *
	 * @param array $query_args query parameters
	 * @param array $atts shortcode attributes
	 * @param string $loop_name identifies the product filter loop
	 * @return string
	 */
	public static function product_loop( $query_args, $atts, $loop_name ) {

		global $woocommerce_loop, $wps_products, $wps_products_paged;

		if ( isset( $_GET['orderby'] ) ) {
			$query_args['orderby'] = '';
			$query_args['order']   = '';
		}
		$ordering  = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
		$query_args['orderby'] = $ordering['orderby'];
		$query_args['order'] = $ordering['order'];
		if ( isset( $ordering['meta_key'] ) ) {
			$query_args['meta_key'] = $ordering['meta_key'] ;
		}

		$paged = get_query_var( is_front_page() ? 'page' : 'paged' );
		if ( !$paged ) {
			$paged = 1;
		}
		$woocommerce_loop['paged']   = $paged;
		$query_args['paged']         = $paged;

		$columns                     = absint( $atts['columns'] );
		$woocommerce_loop['columns'] = $columns;
		$woocommerce_loop['name']    = $loop_name;
		$query_args                  = apply_filters( 'woocommerce_product_search_filter_shortcode_products_query', $query_args, $atts, $loop_name );

		$cache_key = md5( json_encode( $query_args ) );
		$products = wps_cache_get( $cache_key, self::CACHE_GROUP );
		if ( $products === false ) {
			$products = new WP_Query( $query_args );
			wps_cache_set( $cache_key, $products, self::CACHE_GROUP, WooCommerce_Product_Search_Service::get_cache_lifetime() );
		}

		if (
			!$products->have_posts() &&
			$paged > 1 &&
			(
				!empty( $_REQUEST['ixmbd'] ) ||
				!empty( $_REQUEST['ixwpss'] ) ||
				!empty( $_REQUEST['ixwpst'] ) ||
				!empty( $_REQUEST['ixwpsf'] ) ||
				!empty( $_REQUEST['ixwpsp'] ) ||
				isset( $_REQUEST['ixwpse'] )
			)
		) {
			$url = $_SERVER['REQUEST_URI'];
			$unpage_url = preg_replace( '~/page/[0-9]+~i', '', $url );
			$unpage_url = remove_query_arg( 'paged', $unpage_url );
			if ( $url !== $unpage_url ) {
				unset( $query_args['paged'] );
				$wps_products_paged = 1;

				$cache_key = md5( json_encode( $query_args ) );
				$products = wps_cache_get( $cache_key, self::CACHE_GROUP );
				if ( $products === false ) {
					$products = new WP_Query( $query_args );
					wps_cache_set( $cache_key, $products, self::CACHE_GROUP, WooCommerce_Product_Search_Service::get_cache_lifetime() );
				}
			}
		}

		$wps_products = $products;

		ob_start();

		if ( $products->have_posts() ) {

			update_post_caches( $products->posts, array( 'product', 'product_variation' ) );

			do_action( "woocommerce_before_{$loop_name}_loop", $atts );
			woocommerce_product_loop_start();
			while ( $products->have_posts() ) {
				$products->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
			do_action( "woocommerce_after_{$loop_name}_loop", $atts );
		} else {
			do_action( "woocommerce_before_{$loop_name}_loop", $atts );
			woocommerce_product_loop_start();
			do_action( "woocommerce_{$loop_name}_loop_no_results", $atts );
			woocommerce_product_loop_end();
			do_action( "woocommerce_after_{$loop_name}_loop", $atts );
		}

		wc_reset_loop();
		wp_reset_postdata();

		$output = apply_filters( 'woocommerce_product_search_filter_product_loop_prefix', sprintf( '<div class="woocommerce columns-%d">', $columns ) );
		$output .= apply_filters( 'woocommerce_product_search_filter_product_loop_content', ob_get_clean() );
		$output .= apply_filters( 'woocommerce_product_search_filter_product_loop_suffix', '</div>' );

		WC()->query->remove_ordering_args();

		return $output;
	}

	/**
	 * Renders product search inline styles if defined (once only).
	 *
	 * @return string
	 */
	public static function inline_styles() {
		global $woocommerce_product_search_inline_styles;
		$output = '';
		if ( !isset( $woocommerce_product_search_inline_styles ) ) {
			$options = get_option( 'woocommerce-product-search', array() );
			$enable_inline_css = isset( $options[WooCommerce_Product_Search::ENABLE_INLINE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_INLINE_CSS] : WooCommerce_Product_Search::ENABLE_INLINE_CSS_DEFAULT;
			$inline_css        = isset( $options[WooCommerce_Product_Search::INLINE_CSS] ) ? $options[WooCommerce_Product_Search::INLINE_CSS] : WooCommerce_Product_Search::INLINE_CSS_DEFAULT;
			if ( $enable_inline_css ) {
				if ( !empty( $inline_css ) ) {
					$output .= '<style type="text/css">';
					$output .= wp_strip_all_tags( stripslashes( $inline_css ), true );
					$output .= '</style>';
				}
			}
			$woocommerce_product_search_inline_styles = true;
		}
		return $output;
	}

	/**
	 * Increases the field count.
	 */
	public static function field_added() {
		self::$fields++;
	}

	/**
	 * Increases the filter count.
	 */
	public static function filter_added() {
		self::$filters++;
	}

	/**
	 * Returns the HTML elements that are allowed to be used as filter headings.
	 * The woocommerce_product_search_filter_heading_allowed_elements filter allows to modify these.
	 *
	 * @return mixed
	 */
	public static function get_allowed_filter_heading_elements() {
		return apply_filters(
			'woocommerce_product_search_filter_heading_allowed_elements',
			array( 'b', 'div', 'em', 'i', 'header', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'q', 'section', 'small', 'span', 'strong', 'sub', 'sup', 'u' )
		);
	}

	/**
	 * Render fields
	 *
	 * @param string $current_url the URL
	 *
	 * @return string HTML
	 */
	public static function render_query_args_form_fields( $current_url ) {

		global $wp;

		$output = '';
		$query = @parse_url( $current_url, PHP_URL_QUERY );
		if ( $query !== null && $query !== false && is_string( $query ) && $query !== '' ) {
			$query_args = null;
			@parse_str( $query, $query_args );
			if ( $query_args !== null && is_array( $query_args ) ) {

				$valid_vars = apply_filters( 'query_vars', $wp->public_query_vars );
				foreach ( $valid_vars as $var ) {
					if ( isset( $query_args[$var] ) ) {
						$output .= sprintf( '<input type="hidden" name="%s" value="%s"/>', esc_attr( $var ), esc_attr( $query_args[$var] ) );
					}
				}
			}
		}
		return $output;
	}

	/**
	 * Returns the value of the query arg if present in the URL or null.
	 *
	 * @param string $url the URL
	 * @param string $key the query arg
	 *
	 * @return null|string
	 */
	public static function get_query_arg( $url, $key ) {
		$result = null;
		$query = @parse_url( $url, PHP_URL_QUERY );
		if ( $query !== null && $query !== false && is_string( $query ) && $query !== '' ) {
			$query_args = null;
			@parse_str( $query, $query_args );
			if ( $query_args !== null && isset( $query_args[$key] ) ) {
				$result = $query_args[$key];
			}
		}
		return $result;
	}

	/**
	 * Handler for wp_footer
	 */
	public static function wp_footer() {

		remove_action( 'shutdown', array( __CLASS__, 'shutdown' ) );
		if ( self::$fields === 0 && self::$filters > 0 ) {
			if ( apply_filters( 'woocommerce_product_search_filter_auto_append', true, self::$fields, self::$filters ) ) {

				$maybe_json = file_get_contents( 'php://input' );
				if ( $maybe_json !== false ) {
					$json = json_decode( $maybe_json );
					if ( is_object( $json ) && isset( $json->content ) ) {
						return;
					}
				}
				echo self::render( array( 'style' => 'display:none!important' ) );
			}
		}
	}

	/**
	 * Handler for shutdown
	 */
	public static function shutdown() {

		self::wp_footer();
	}
}
WooCommerce_Product_Search_Filter::init();
