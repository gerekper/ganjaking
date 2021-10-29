<?php
/**
 * class-woocommerce-product-search-filter-reset.php
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
 * @since 2.4.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search_filter_reset' ) ) {
	/**
	 * Renders a product filter reset which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_reset( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Reset::render( $atts );
	}
}

/**
 * Filter reset.
 */
class WooCommerce_Product_Search_Filter_Reset {

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_reset', array( __CLASS__, 'shortcode' ) );
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
	 * [woocommerce_product_filter_reset] shortcode renderer.
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
	 * Renders the reset function.
	 *
	 * @param array $atts
	 * @param null $results currently returns nothing
	 *
	 * @return string|mixed
	 */
	public static function render( $atts = array(), &$results = null) {
		self::load_resources();

		$_atts = $atts;

		$atts = shortcode_atts(
			array(
				'container_class'     => '',
				'container_id'        => null,
				'heading'             => null,
				'heading_class'       => null,
				'heading_element'     => 'div',
				'heading_id'          => null,
				'show_heading'        => 'yes',
				'submit_button_label' => __( 'Clear', 'woocommerce-product-search' ),
				'use_shop_url'        => 'no'
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-reset-%d', $n );
		$heading_class   = 'product-search-filter-reset-heading';
		$heading_id      = sprintf( 'product-search-filter-reset-heading-%d', $n );
		$containers      = array();

		if ( $atts['heading'] === null ) {
			$atts['heading']  = _x( 'Filters', 'product filter reset heading', 'woocommerce-product-search' );
		}

		$params = array();
		foreach ( $atts as $key => $value ) {
			$is_param = true;
			if ( $value !== null ) {
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {

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

		$output = apply_filters(
			'woocommerce_product_search_filter_reset_prefix',
			sprintf(
				'<div id="%s" class="product-search-filter-reset %s">',
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

		$reset_url = self::get_reset_url();
		if ( isset( $params['use_shop_url'] ) && $params['use_shop_url'] ) {
			$reset_url = get_permalink( wc_get_page_id( 'shop' ) );
			if ( !$reset_url ) {
				$query_post_type = WooCommerce_Product_Search_Filter::get_query_arg( $reset_url, 'post_type' );
				if ( $query_post_type !== 'product' ) {
					$reset_url = add_query_arg( array( 'post_type' => 'product' ), trailingslashit( home_url() ) );
				}
			}
		}

		$form_id = 'product-search-filter-reset-form-' . $n;
		$output .= sprintf(
			'<form id="%s" class="product-search-filter-reset-form" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_url( $reset_url )
		);
		$output .= sprintf(
			'<button class="button product-search-filter-reset-clear" type="submit">%s</button>',
			esc_html( $params['submit_button_label'] )
		);
		$output .= '</form>';

		$output .= apply_filters(
			'woocommerce_product_search_filter_reset_suffix',
			'</div>'
		);

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		return $output;
	}

	/**
	 * Returns the cleared URL.
	 *
	 * @param string $current_url URL to clear, null by default uses current URL
	 *
	 * @return string cleared URL
	 */
	public static function get_reset_url( $current_url = null ) {
		if ( $current_url === null ) {
			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		$current_url = remove_query_arg(
			array(
				'ixwpss', 'title', 'excerpt', 'content', 'categories', 'attributes', 'tags', 'sku', 'lang', 'paged',
				'ixwpst', 'ixwpsp', 'min_price', 'max_price', 'ixwpse', 'on_sale', 'rating'
			),
			$current_url
		);
		return $current_url;
	}

}
WooCommerce_Product_Search_Filter_Reset::init();
