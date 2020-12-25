<?php
/**
 * class-woocommerce-product-search-field.php
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
		return WooCommerce_Product_Search_Field::woocommerce_product_search( $atts );
	}
}

/**
 * Search field definitions and renderers.
 */
class WooCommerce_Product_Search_Field {

	private static $instances = 0;

	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_search', array( __CLASS__, 'woocommerce_product_search' ) );
		$options = get_option( 'woocommerce-product-search', array() );
		$auto_replace_form = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_FORM] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_FORM] : WooCommerce_Product_Search::AUTO_REPLACE_FORM_DEFAULT;
		if ( $auto_replace_form ) {
			add_filter( 'get_product_search_form', array( __CLASS__, 'get_product_search_form' ) );
		}
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
				'order'               => null,
				'order_by'            => null,
				'title'               => 'yes',
				'excerpt'             => 'yes',
				'content'             => 'yes',
				'categories'          => 'yes',
				'attributes'          => 'yes',
				'tags'                => 'yes',
				'sku'                 => 'yes',
				'limit'               => null,
				'height'              => '',
				'category_results'    => null,
				'category_limit'      => null,
				'product_thumbnails'  => 'yes',
				'show_description'    => 'yes',
				'show_price'          => 'yes',
				'show_add_to_cart'    => 'yes',
				'show_more'           => 'yes',
				'show_clear'          => 'yes',
				'placeholder'         => __( 'Search', 'woocommerce-product-search' ),
				'no_results'          => '',
				'blinker_timeout'     => null,
				'delay'               => WooCommerce_Product_Search::DEFAULT_DELAY,
				'characters'          => WooCommerce_Product_Search::DEFAULT_CHARACTERS,
				'dynamic_focus'       => 'yes',
				'floating'            => 'yes',
				'inhibit_enter'       => 'no',
				'submit_button'       => 'no',
				'submit_button_label' => __( 'Search', 'woocommerce-product-search' ),
				'navigable'           => 'yes',
				'wpml'                => 'no'
			),
			$atts
		);

		$url_params = array();
		foreach ( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'order' :
						$value = strtoupper( $value );
						switch ( $value ) {
							case 'ASC' :
							case 'DESC' :
								break;
							default :
								$value = 'DESC';
						}
						break;
					case 'order_by' :
						switch ( $value ) {
							case '' :
							case 'date' :
							case 'ID' :
							case 'popularity' :
							case 'rating' :
							case 'sku' :
							case 'title' :
								break;
							default :
								$value = 'date';
						}
						break;
					case 'title' :
					case 'excerpt' :
					case 'content' :
					case 'categories' :
					case 'attributes' :
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
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'dynamic_focus' :
					case 'floating' :
					case 'inhibit_enter' :
					case 'submit_button' :
					case 'navigable' :
					case 'wpml' :
					case 'product_thumbnails' :
					case 'show_description' :
					case 'show_price' :
					case 'show_add_to_cart' :
					case 'show_more' :
					case 'show_clear' :
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
					case 'height' :
						$value = WooCommerce_Product_Search_Utility::get_css_unit( $value );
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

		$n          = self::$instances;
		$search_id  = 'product-search-' . $n;
		$form_id    = 'product-search-form-' . $n;
		$field_id   = 'product-search-field-' . $n;
		$results_id = 'product-search-results-' . $n;
		$results_content_id = 'product-search-results-content-' . $n;

		$output .= self::inline_styles();

		$output .= sprintf(
			'<div id="%s" class="product-search %s">',
			esc_attr( $search_id ),
			esc_attr( $floating )
		);

		$output .= '<div class="product-search-form">';
		$output .= sprintf(
			'<form id="%s" class="product-search-form %s" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_attr( $params['submit_button'] ? 'show-submit-button' : '' ),
			esc_url( home_url( '/' ) )
		);
		$output .= sprintf(
			'<input id="%s" name="s" type="text" class="product-search-field" placeholder="%s" autocomplete="off"/>',
			esc_attr( $field_id ),
			esc_attr( $params['placeholder'] )
		);
		$output .= '<input type="hidden" name="post_type" value="product"/>';
		$output .= sprintf( '<input type="hidden" name="title" value="%d"/>', isset( $url_params['title'] ) && $url_params['title'] ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="excerpt" value="%d"/>', isset( $url_params['excerpt'] ) && $url_params['excerpt'] ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="content" value="%d"/>', isset( $url_params['content'] ) && $url_params['content'] ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="categories" value="%d"/>', isset( $url_params['categories'] ) && $url_params['categories'] || !isset( $url_params['categories'] ) && WooCommerce_Product_Search_Service::DEFAULT_CATEGORIES ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="attributes" value="%d"/>', isset( $url_params['attributes'] ) && $url_params['attributes'] || !isset( $url_params['attributes'] ) && WooCommerce_Product_Search_Service::DEFAULT_ATTRIBUTES ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="tags" value="%d"/>', isset( $url_params['tags'] ) && $url_params['tags'] || !isset( $url_params['tags'] ) && WooCommerce_Product_Search_Service::DEFAULT_TAGS ? 1 : 0 );
		$output .= sprintf( '<input type="hidden" name="sku" value="%d"/>', isset( $url_params['sku'] ) && $url_params['sku'] ? 1 : 0 );

		if ( isset( $url_params['order_by'] ) && $url_params['order_by'] !== '' ) {
			$orderby_value = $url_params['order_by'];
			if ( isset( $url_params['order'] ) ) {
				$orderby_value .= '-' . $url_params['order'];
			}
			$output .= sprintf( '<input type="hidden" name="orderby" value="%s"/>', esc_attr( $orderby_value ) );
		}

		if ( $params['wpml'] && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$output .= sprintf( '<input type="hidden" name="lang" value="%s"/>', ICL_LANGUAGE_CODE );
		}
		$output .= '<input type="hidden" name="ixwps" value="1"/>';

		if ($params['show_clear'] ) {
			$output .= sprintf(
				'<span title="%s" class="product-search-field-clear" style="display:none"></span>',
				esc_attr__( 'Clear', 'woocommerce-product-search' )
			);
		}

		if ( $params['submit_button'] ) {
			$output .= ' ';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
		} else {
			$output .= '<noscript>';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
			$output .= '</noscript>';
		}

		$output .= '</form>';
		$output .= '</div>';

		$output .= sprintf( '<div id="%s" class="product-search-results">', $results_id );
		$results_content_style = '';
		if ( !empty( $params['height'] ) ) {
			$results_content_style = sprintf( 'max-height:%s;', $params['height'] );
		}
		$output .= sprintf( '<div id="%s" class="product-search-results-content" style="%s">', $results_content_id, esc_attr( $results_content_style ) );
		$output .= '</div>';
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
		if ( isset( $params['show_add_to_cart'] ) ) {
			$js_args[] = 'show_add_to_cart:' . ( $params['show_add_to_cart'] ? 'true' : 'false' );
		}
		if ( isset( $params['show_more'] ) ) {
			$js_args[] = 'show_more:' . ( $params['show_more'] ? 'true' : 'false' );
		}
		$js_args = '{' . implode( ',', $js_args ) . '}';

		$post_target_url = add_query_arg( $url_params , admin_url( 'admin-ajax.php' ) );

		$output .= '<script type="text/javascript">';
		$output .= 'document.getElementById("' . $field_id . '").disabled = true;';
		$output .= 'document.addEventListener( "DOMContentLoaded", function() {';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'if ( typeof jQuery().typeWatch !== "undefined" ) {';
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
			esc_attr( $search_id . ' div.product-search-results-content' ),
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
			$output .= sprintf( 'ixwps.dynamicFocus("%s","%s");', $search_id, $results_content_id );
		}
		$output .= '} else {';
		$output .= 'if ( typeof console !== "undefined" && typeof console.log !== "undefined" ) { ';
		$output .= 'document.getElementById("' . $field_id . '").disabled = false;';
		$output .= 'console.log("A conflict is preventing required resources to be loaded."); ';
		$output .= '}';
		$output .= '}';
		$output .= '}';
		$output .= '} );';
		$output .= '</script>';

		self::$instances++;

		return $output;
	}

	/**
	 * Render the standard search form replacement.
	 *
	 * @param string $form
	 *
	 * @return string WPS form
	 */
	public static function get_product_search_form( $form ) {
		$options = get_option( 'woocommerce-product-search', array() );
		$auto_replace = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE] : WooCommerce_Product_Search::AUTO_REPLACE_DEFAULT;
		if ( $auto_replace ) {
			$auto_instance = isset( $options[WooCommerce_Product_Search::AUTO_INSTANCE] ) ? $options[WooCommerce_Product_Search::AUTO_INSTANCE] : WooCommerce_Product_Search_Widget::get_auto_instance_default();
			$form = self::woocommerce_product_search( $auto_instance );
		}
		return $form;
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
WooCommerce_Product_Search_Field::init();
