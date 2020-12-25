<?php
/**
 * class-woocommerce-product-search-filter-sale.php
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
 * @since 2.19.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search_filter_sale' ) ) {
	/**
	 * Renders a sale filter for products on sale.
	 * Returned as HTML and loads required resources.
	 *
	 * @param array $atts desired options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_sale( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Sale::render( $atts );
	}
}

/**
 * Filter reset.
 */
class WooCommerce_Product_Search_Filter_Sale {

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_sale', array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
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
	 * Handler for init
	 */
	public static function wp_init() {
	}

	/**
	 * [woocommerce_product_filter_sale] shortcode renderer.
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
	 * Renders the sale filter.
	 *
	 * @param array $atts
	 * @param null $results currently returns nothing
	 *
	 * @return string|mixed
	 */
	public static function render( $atts = array(), &$results = null) {
		self::load_resources();

		$atts = shortcode_atts(
			array(
				'container_class'     => '',
				'container_id'        => null,
				'filter'              => 'yes',
				'has_on_sale_only'    => 'yes',
				'heading'             => null,
				'heading_class'       => null,
				'heading_element'     => 'div',
				'heading_id'          => null,
				'show_heading'        => 'yes',
				'submit_button'       => 'no',
				'submit_button_label' => __( 'Go', 'woocommerce-product-search' ),
				'use_shop_url'        => 'no'
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-sale-%d', $n );
		$heading_class   = 'product-search-filter-sale-heading product-search-filter-extras-heading';
		$heading_id      = sprintf( 'product-search-filter-sale-heading-%d', $n );
		$containers      = array();

		if ( $atts['heading'] === null ) {
			$atts['heading']  = _x( 'Sale', 'product filter sale heading', 'woocommerce-product-search' );
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
					case 'has_on_sale_only' :
					case 'submit_button' :
					case 'show_heading' :
					case 'use_shop_url' :
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

		$on_sale = isset( $_REQUEST['on_sale'] ) ? boolval( $_REQUEST['on_sale'] ) : false;

		if ( $params['has_on_sale_only'] ) {
			$on_sale_ids = wc_get_product_ids_on_sale();
			if ( !is_array( $on_sale_ids ) || count( $on_sale_ids ) <= 0 ) {
				return '';
			}
		}

		$output = apply_filters(
			'woocommerce_product_search_filter_sale_prefix',
			sprintf(
				'<div id="%s" class="product-search-filter-extras product-search-filter-sale %s">',
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

		$current_url = remove_query_arg( array( 'ixwpse', 'on_sale', 'paged' ), $current_url );
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

		$on_sale_field_id = 'product-search-filter-on-sale-' . $n;
		$form_id          = 'product-search-filter-sale-form-' . $n;

		$output .= sprintf(
			'<form id="%s" class="product-search-filter-extras-form product-search-filter-sale-form" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_url( $href )
		);

		$output .= '<input type="hidden" name="ixwpse" value="1"/>';

		$filter_extra_class = $params['filter'] ? '' : ' filter-dead ';

		$output .= '<label>';

		$output .= sprintf(
			'<input id="%s" class="product-search-filter-extra product-search-filter-on-sale %s" type="checkbox" name="on_sale" value="1" %s />',
			esc_attr( $on_sale_field_id ),
			esc_attr( $filter_extra_class ),
			$on_sale ? ' checked="checked" ' : ''
		);

		$output .= sprintf(
			'<a class="product-search-filter-extra product-search-filter-on-sale %s" href="%s">%s</a>',
			esc_attr( $filter_extra_class ),
			esc_url( $on_sale ? $href : add_query_arg( array( 'ixwpse' => 1, 'on_sale' => 1 ), $href ) ),
			esc_html_x( 'On Sale', 'product filter sale link', 'woocommerce-product-search' )
		);
		$output .= '</label>';

		if ( isset( $params['submit_button'] ) && $params['submit_button'] ) {
			$output .= sprintf(
				'<button class="button product-search-filter-sale-submit" type="submit">%s</button>',
				esc_html( $params['submit_button_label'] )
			);
		}

		if ( $add_post_type ) {
			$output .= '<input type="hidden" name="post_type" value="product"/>';
		}
		$output .= WooCommerce_Product_Search_Filter::render_query_args_form_fields( $current_url );

		$output .= '</form>';

		$output .= apply_filters(
			'woocommerce_product_search_filter_sale_suffix',
			'</div>'
		);

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		return $output;
	}

}
WooCommerce_Product_Search_Filter_Sale::init();
