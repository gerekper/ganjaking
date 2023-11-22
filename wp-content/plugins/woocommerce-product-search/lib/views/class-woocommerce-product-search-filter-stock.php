<?php
/**
 * class-woocommerce-product-search-filter-stock.php
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
 * @since 3.8.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Cache;
use com\itthinx\woocommerce\search\engine\Filter_Renderer;
use com\itthinx\woocommerce\search\engine\Query_Control;
use com\itthinx\woocommerce\search\engine\Settings;

if ( !function_exists( 'woocommerce_product_search_filter_stock' ) ) {
	/**
	 * Renders a stock filter for products in stock.
	 * Returned as HTML and loads required resources.
	 *
	 * @param array $atts desired options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_stock( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Stock::render( $atts );
	}
}

/**
 * Stock filter.
 */
class WooCommerce_Product_Search_Filter_Stock extends Filter_Renderer {

	const CACHE_GROUP = 'ixwpsstock';

	/**
	 * @var int
	 */
	const DATA_CACHE_LIFETIME = 3600;

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_stock', array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$settings = Settings::get_instance();
		$enable_css = $settings->get( WooCommerce_Product_Search::ENABLE_CSS, WooCommerce_Product_Search::ENABLE_CSS_DEFAULT );
		wp_enqueue_script( 'typewatch' );
		wp_enqueue_script( 'product-filter' );
		if ( $enable_css ) {
			wp_enqueue_style( 'product-search' );
		}
	}

	/**
	 * Handler for init
	 */
	public static function wp_init() {
	}

	/**
	 * [woocommerce_product_filter_stock] shortcode renderer.
	 *
	 * @param array $atts
	 * @param string $content not used
	 *
	 * @return string|mixed
	 */
	public static function shortcode( $atts = array(), $content = '' ) {
		return self::render( $atts );
	}

	/**
	 * Instance ID.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private static function get_n() {
		$n = self::$instances;
		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			$n .= '-' . md5( rand() );
		}
		return $n;
	}

	/**
	 * Renders the stock filter.
	 *
	 * @param array $atts
	 * @param null $results currently returns nothing
	 *
	 * @return string|mixed
	 */
	public static function render( $atts = array(), &$results = null ) {

		global $wp_query;

		self::load_resources();

		$atts = shortcode_atts(
			array(
				'container_class'     => '',
				'container_id'        => null,
				'filter'              => 'yes',
				'has_in_stock_only'   => 'yes',
				'heading'             => null,
				'heading_class'       => null,
				'heading_element'     => 'div',
				'heading_id'          => null,
				'shop_only'           => 'no',
				'show_heading'        => 'yes',
				'submit_button'       => 'no',
				'submit_button_label' => __( 'Go', 'woocommerce-product-search' ),

			),
			$atts
		);

		$shop_only = strtolower( $atts['shop_only'] );
		$shop_only = in_array( $shop_only, array( 'true', 'yes', '1' ) );
		if ( $shop_only && !woocommerce_product_search_is_shop() ) {
			return '';
		}

		$has_in_stock_only = strtolower( $atts['has_in_stock_only'] );
		$has_in_stock_only = in_array( $has_in_stock_only, array( 'true', 'yes', '1' ) );
		if ( $has_in_stock_only ) {
			$counts = self::get_stock_counts();
			if ( $counts['instock'] + $counts['onbackorder'] <= 0 ) {
				return '';
			}
		}

		$n               = self::get_n();
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-stock-%d', $n );
		$heading_class   = 'product-search-filter-stock-heading product-search-filter-extras-heading';
		$heading_id      = sprintf( 'product-search-filter-stock-heading-%d', $n );
		$containers      = array();

		$render_cache = apply_filters( 'woocommerce_product_search_render_cache', WPS_RENDER_CACHE, __CLASS__, $atts );
		if ( $render_cache ) {
			$query_control = new Query_Control();
			if ( isset( $wp_query ) && $wp_query->is_main_query() ) {
				$query_control->set_query( $wp_query );
			}
			$request_parameters = $query_control->get_request_parameters();
			unset( $query_control );
			$cache = Cache::get_instance();
			$cache_key = md5( json_encode( array( $container_id, $request_parameters, $atts ) ) );
			$data = $cache->get( $cache_key, __CLASS__ );
			if ( $data !== null ) {

				foreach ( $data['inline_scripts'] as $script_data ) {
					wp_add_inline_script( $script_data['handle'], $script_data['inline_script'] );
				}
				WooCommerce_Product_Search_Filter::filter_added();
				self::$instances++;
				return $data['output'];
			}
			$data = array(
				'output'         => '',
				'inline_scripts' => array()
			);
		}

		if ( $atts['heading'] === null || $atts['heading'] === '' ) {
			$atts['heading']  = _x( 'Stock', 'product filter stock heading', 'woocommerce-product-search' );
		}

		$params = array();
		foreach ( $atts as $key => $value ) {
			$is_param = true;
			if ( $value !== null ) {
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {

					case 'filter' :
					case 'has_in_stock_only' :
					case 'shop_only' :
					case 'submit_button' :
					case 'show_heading' :

						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;

					case 'container_class' :
					case 'container_id' :
					case 'heading_class' :
					case 'heading_id' :
						$value = preg_replace( '/[^a-zA-Z0-9 _.#-]/', '', $value );
						$value = trim( $value );
						$containers[$key] = $value;
						$is_param = false;
						break;

					case 'heading_element' :
						if ( !in_array( $value, WooCommerce_Product_Search_Filter::get_allowed_filter_heading_elements() ) ) {
							$value = 'div';
						}
						break;

					case 'heading' :
						$value = esc_html( $value );
						break;

					case 'submit_button_label' :
						$value = esc_html( $value );
						break;
				}
			}
			if ( $is_param ) {
				$params[$key] = $value;
			}
		}

		if ( !empty( $containers['container_class'] ) ) {
			$container_class = $containers['container_class'];
		}
		if ( !empty( $containers['container_id'] ) ) {
			$container_id = $containers['container_id'];
		}
		if ( !empty( $containers['heading_class'] ) ) {
			$heading_class = $containers['heading_class'];
		}
		if ( !empty( $containers['heading_id'] ) ) {
			$heading_id = $containers['heading_id'];
		}

		$in_stock = isset( $_REQUEST['in_stock'] ) ? boolval( $_REQUEST['in_stock'] ) : false;

		$output = apply_filters(
			'woocommerce_product_search_filter_stock_prefix',
			sprintf(
				'<div id="%s" class="product-search-filter-extras product-search-filter-stock %s">',
				esc_attr( $container_id ),
				esc_attr( $container_class )
			)
		);

		$heading_output = '';
		if ( $params['show_heading'] ) {
			$heading_output .= sprintf(
				'<%s class="%s" id="%s">%s</%s>',
				esc_html( $params['heading_element'] ),
				esc_attr( $heading_class ),
				esc_attr( $heading_id ),
				true ? esc_html( $params['heading'] ) : esc_html( $params['heading_no_results'] ),
				esc_html( $params['heading_element'] )
			);
		}
		$output .= $heading_output;

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'ixwpse', 'in_stock', 'paged' ), $current_url );
		$href        = $current_url;
		$add_post_type = false;

		$in_stock_field_id = 'product-search-filter-in-stock-' . $n;
		$form_id          = 'product-search-filter-stock-form-' . $n;

		$output .= sprintf(
			'<form id="%s" class="product-search-filter-extras-form product-search-filter-stock-form" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_url( $href )
		);

		$output .= '<input type="hidden" name="ixwpse" value="1"/>';

		$filter_extra_class = $params['filter'] ? '' : ' filter-dead ';

		$output .= '<label>';

		$output .= sprintf(
			'<input id="%s" class="product-search-filter-extra product-search-filter-in-stock %s" type="checkbox" name="in_stock" value="1" %s />',
			esc_attr( $in_stock_field_id ),
			esc_attr( $filter_extra_class ),
			$in_stock ? ' checked="checked" ' : ''
		);

		$output .= sprintf(
			'<a class="product-search-filter-extra product-search-filter-in-stock %s" href="%s">%s</a>',
			esc_attr( $filter_extra_class ),
			esc_url( $in_stock ? $href : add_query_arg( array( 'ixwpse' => 1, 'in_stock' => 1 ), $href ) ),
			esc_html_x( 'Available', 'product filter stock link', 'woocommerce-product-search' )
		);
		$output .= '</label>';

		if ( isset( $params['submit_button'] ) && $params['submit_button'] ) {
			$output .= sprintf(
				'<button class="button product-search-filter-stock-submit" type="submit">%s</button>',
				esc_html( $params['submit_button_label'] )
			);
		}

		if ( $add_post_type ) {
			$output .= '<input type="hidden" name="post_type" value="product"/>';
		}
		$output .= WooCommerce_Product_Search_Filter::render_query_args_form_fields( $current_url );

		$output .= '</form>';

		$output .= apply_filters(
			'woocommerce_product_search_filter_stock_suffix',
			'</div>'
		);

		WooCommerce_Product_Search_Filter::filter_added();

		if ( $render_cache ) {
			$data['output'] = $output;
			$cache->set( $cache_key, $data, __CLASS__, self::get_render_cache_lifetime() );
		}

		self::$instances++;

		return $output;
	}

	/**
	 * Return stock status counts by instock, outofstock and onbackorder.
	 *
	 * @return array
	 */
	public static function get_stock_counts() {

		global $wpdb;

		$cache = Cache::get_instance();
		$counts = $cache->get( 'stock_counts', self::CACHE_GROUP );
		if ( $counts === null ) {
			$counts = array( 'instock' => 0, 'outofstock' => 0, 'onbackorder' => 0 );
			if ( property_exists( $wpdb, 'wc_product_meta_lookup' ) ) {
				$stock_status_counts = $wpdb->get_results( "SELECT count(stock_status) AS count, stock_status FROM $wpdb->wc_product_meta_lookup GROUP BY stock_status" );
				if ( is_array( $stock_status_counts ) ) {
					foreach ( $stock_status_counts as $stock_status_count ) {
						switch ( $stock_status_count->stock_status ) {
							case 'instock':
								$counts['instock'] = intval( $stock_status_count->count );
								break;
							case 'outofstock':
								$counts['outofstock'] = intval( $stock_status_count->count );
								break;
							case 'onbackorder':
								$counts['onbackorder'] = intval( $stock_status_count->count );
								break;

						}
					}
				}
			}
			$_counts = apply_filters( 'woocommerce_product_search_filter_stock_stock_counts', $counts );
			foreach ( array_keys( $counts ) as $key ) {
				if ( isset( $_counts[$key] ) ) {
					$counts[$key] = max( 0, intval( $_counts[$key] ) );
				}
			}
			$cache->set( 'stock_counts', $counts, self::CACHE_GROUP, self::get_data_cache_lifetime() );
		}
		return $counts;
	}

}
WooCommerce_Product_Search_Filter_Stock::init();
