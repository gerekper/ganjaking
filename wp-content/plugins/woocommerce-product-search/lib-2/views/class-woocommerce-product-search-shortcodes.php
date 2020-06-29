<?php
/**
 * class-woocommerce-product-search-shortcodes.php
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
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search' ) ) {
	/**
	 * Renders a product search form which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired search facility options
	 *
	 * @return string form HTML
	 */
	function woocommerce_product_search( $atts = array() ) {
		return WooCommerce_Product_Search_Shortcodes::woocommerce_product_search( $atts );
	}
}

/**
 * Shortcode definitions and renderers.
 */
class WooCommerce_Product_Search_Shortcodes {

	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_search', array( __CLASS__, 'woocommerce_product_search' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$options = get_option( 'woocommerce-product-search', array() );
		$enable_css = isset( $options[WooCommerce_Product_Search::ENABLE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_CSS] : WooCommerce_Product_Search::ENABLE_CSS_DEFAULT;
		wp_enqueue_script( 'typewatch' );
		wp_enqueue_script( 'product-search' );
		if ( $enable_css ) {
			wp_enqueue_style( 'product-search' );
		}
	}

	/**
	 * Shortcode handler for [woocommerce_product_search], renders a product search form.
	 *
	 * Enqueues required scripts and styles.
	 *
	 * @param array $atts
	 * @param array $content not used
	 *
	 * @return string form HTML
	 */
	public static function woocommerce_product_search( $atts = array(), $content = '' ) {

		self::load_resources();

		$atts = shortcode_atts(
			array(
				'order'    => null,
				'order_by' => null,
				'title'    => null,
				'excerpt'  => null,
				'content'  => null,
				'tags'     => null,
				'sku'      => null,
				'limit'    => null,
				'category_results'   => null,
				'category_limit'     => null,
				'product_thumbnails' => null,
				'show_description'   => null,
				'show_price'         => null,
				'placeholder'     => __( 'Search', 'woocommerce-product-search' ),
				'no_results'      => '',
				'blinker_timeout' => null,
				'delay'         => WooCommerce_Product_Search::DEFAULT_DELAY,
				'characters'    => WooCommerce_Product_Search::DEFAULT_CHARACTERS,
				'dynamic_focus' => 'yes',
				'floating'      => 'yes',
				'inhibit_enter' => 'no',
				'submit_button' => 'no',
				'submit_button_label' => __( 'Search', 'woocommerce-product-search' ),
				'navigable'     => 'yes',
				'auto_adjust'   => 'yes',
				'wpml'          => 'no'
			),
			$atts
		);

		$url_params = array();
		foreach ( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				$value = strip_tags( trim( $value ) );
				switch ( $key ) {
					case 'order' :
					case 'order_by' :
						break;
					case 'title' :
					case 'excerpt' :
					case 'content' :
					case 'tags' :
					case 'sku' :
					case 'product_thumbnails' :
					case 'category_results' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'limit' :
					case 'category_limit' :
						$value = intval( $value );
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
				$value = strip_tags( trim( $value ) );
				switch ( $key ) {
					case 'dynamic_focus' :
					case 'floating' :
					case 'inhibit_enter' :
					case 'submit_button' :
					case 'navigable' :
					case 'auto_adjust' :
					case 'wpml' :
					case 'product_thumbnails' :
					case 'show_description' :
					case 'show_price' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'delay' :
					case 'characters' :
					case 'blinker_timeout' :
						$value = intval( $value );
						break;
					case 'no_results' :
					case 'placeholder' :
					case 'submit_button_label' :
						$value = trim( $value );
						break;
					default :
						$add = false;
				}
				if ( $add ) {
					$params[$key] = $value;
				}
			}
		}

		$floating = $params['floating'] ? 'floating' : '';

		if ( $params['delay'] < WooCommerce_Product_Search::MIN_DELAY ) {
			$params['delay'] = WooCommerce_Product_Search::MIN_DELAY;
		}
		if ( $params['characters'] < WooCommerce_Product_Search::MIN_CHARACTERS ) {
			$params['characters'] = WooCommerce_Product_Search::MIN_CHARACTERS;
		}
		$params['placeholder'] = apply_filters( 'woocommerce_product_search_placeholder', $params['placeholder'] );
		$params['no_results']  = apply_filters( 'woocommerce_product_search_no_results', $params['no_results'] );

		$output = '';

		$product_search = true;

		$n          = rand();
		$search_id  = 'product-search-' . $n;
		$form_id    = 'product-search-form-' . $n;
		$field_id   = 'product-search-field-' . $n;
		$results_id = 'product-search-results-' . $n;

		$output .= self::inline_styles();

		$output .= sprintf(
			'<div id="%s" class="product-search %s">',
			esc_attr( $search_id ),
			esc_attr( $floating )
		);

		$output .= '<div class="product-search-form">';
		$output .= sprintf( '<form id="%s" class="product-search-form" action="%s" method="get">', esc_attr( $form_id ), esc_url( home_url( '/' ) ) );
		$output .= '<div>';
		$output .= sprintf(
			'<input id="%s" name="s" type="text" class="product-search-field" placeholder="%s" autocomplete="off"/>',
			esc_attr( $field_id ),
			esc_attr( $params['placeholder'] )
		);
		$output .= '<input type="hidden" name="post_type" value="product"/>';

		$output .= sprintf( '<input type="hidden" name="tags" value="%d"/>', isset( $url_params['tags'] ) && $url_params['tags'] || !isset( $url_params['tags'] ) && WooCommerce_Product_Search_Service::DEFAULT_TAGS ? 1 : 0 );
		if ( !empty( $url_params['sku'] ) ) {
			$output .= '<input type="hidden" name="sku" value="1"/>';
		}
		if ( isset( $url_params['limit'] ) ) {
			$output .= sprintf( '<input type="hidden" name="limit" value="%d"/>', intval( $url_params['limit'] ) );
		}
		if ( $params['wpml'] && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$output .= sprintf( '<input type="hidden" name="lang" value="%s"/>', ICL_LANGUAGE_CODE );
		}
		$output .= '<input type="hidden" name="ixwps" value="1"/>';
		if ( $params['submit_button'] ) {
			$output .= ' ';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
		} else {
			$output .= '<noscript>';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
			$output .= '</noscript>';
		}

		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>'; 

		$output .= sprintf( '<div id="%s" class="product-search-results">', $results_id );
		$output .= '</div>'; 

		$output .= '</div>'; 

		$js_args = array();
		$js_args[] = sprintf( 'no_results:"%s"', esc_js( $params['no_results'] ) );
		$js_args[] = $params['dynamic_focus'] ? 'dynamic_focus:true' : 'dynamic_focus:false';
		if ( isset( $params['blinker_timeout'] ) ) {
			$blinker_timeout = max( array( 0, intval( $params['blinker_timeout'] ) ) );
			$js_args[] = 'blinkerTimeout:' . $blinker_timeout;
		}
		if ( $params['wpml'] && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$js_args[] = sprintf( 'lang:"%s"', ICL_LANGUAGE_CODE );
		}
		if ( isset( $params['product_thumbnails'] ) ) {
			$js_args[] = 'product_thumbnails:' . ( $params['product_thumbnails'] ? 'true' : 'false' );
		}
		if ( isset( $params['show_description'] ) ) {
			$js_args[] = 'show_description:' . ( $params['show_description'] ? 'true' : 'false' );
		}
		if ( isset( $params['show_price'] ) ) {
			$js_args[] = 'show_price:' . ( $params['show_price'] ? 'true' : 'false' );
		}
		$js_args = '{' . implode( ',', $js_args ) . '}';

		if ( apply_filters( 'woocommerce_product_search_use_admin_ajax', WooCommerce_Product_Search::USE_ADMIN_AJAX_DEFAULT ) ) {

			$post_target_url = add_query_arg( $url_params , admin_url( 'admin-ajax.php' ) );
		} else {
			$post_target_url = add_query_arg( $url_params , WOO_PS_PLUGIN_URL . '/lib-2/core/product-search.php' );
		}

		$output .= '<script type="text/javascript">';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'jQuery(document).ready(function(){';
		$output .= sprintf(
			'jQuery("#%s").typeWatch( {
				callback: function (value) { ixwps.productSearch(\'%s\', \'%s\', \'%s\', \'%s\', value, %s); },
				wait: %d,
				highlight: true,
				captureLength: %d
			} );',
			esc_attr( $field_id ), 
			esc_attr( $field_id ), 
			esc_attr( $search_id ), 
			esc_attr( $search_id . ' div.product-search-results' ), 
			$post_target_url,
			$js_args,
			$params['delay'],
			$params['characters']
		);
		if ( $params['inhibit_enter'] ) {
			$output .= sprintf( 'ixwps.inhibitEnter("%s");', $field_id );
		}
		if ( $params['navigable'] ) {
			$output .= sprintf( 'ixwps.navigate("%s","%s");', $field_id, $results_id );
		}
		if ( $params['dynamic_focus'] ) {
			$output .= sprintf( 'ixwps.dynamicFocus("%s","%s");', $search_id, $results_id );
		}
		if ( $params['auto_adjust'] ) {
			$output .= sprintf( 'ixwps.autoAdjust("%s","%s");', $field_id, $results_id );
		}
		$output .= '});'; 
		$output .= '}'; 
		$output .= '</script>';

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

}
WooCommerce_Product_Search_Shortcodes::init();
