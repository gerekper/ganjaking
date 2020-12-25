<?php
/**
 * class-woocommerce-product-search-filter-price.php
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

if ( !function_exists( 'woocommerce_product_search_filter_price' ) ) {
	/**
	 * Renders a product price filter which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired filter options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_price( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Price::render( $atts );
	}
}

/**
 * Filter by price.
 */
class WooCommerce_Product_Search_Filter_Price {

	/**
	 * @var int Default decimals.
	 */
	const DECIMALS = 0;

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_price', array( __CLASS__, 'shortcode' ) );
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

		if ( isset( $_GET['min_price'] ) ) {
			if ( strlen( trim( $_GET['min_price'] ) ) === 0 ) {
				unset( $_GET['min_price'] );
			}
		}
		if ( isset( $_GET['max_price'] ) ) {
			if ( strlen( trim( $_GET['max_price'] ) ) === 0 ) {
				unset( $_GET['max_price'] );
			}
		}

		if ( isset( $_GET['min_price'] ) && isset( $_GET['max_price'] ) ) {
			$min = floatval( $_GET['min_price'] );
			$max = floatval( $_GET['max_price'] );
			if ( $max < $min ) {
				unset( $_GET['max_price'] );
			}
		}
	}

	/**
	 * [woocommerce_product_filter_price] shortcode renderer.
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
	 * Renders the price filter.
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
				'delay'               => WooCommerce_Product_Search::DEFAULT_DELAY,
				'fields'              => 'yes',
				'filter'              => 'yes',
				'heading'             => null,
				'heading_class'       => null,
				'heading_element'     => 'div',
				'heading_id'          => null,
				'heading_no_results'  => '',
				'max_placeholder'     => __( 'Max', 'woocommerce-product-search' ),
				'min_placeholder'     => __( 'Min', 'woocommerce-product-search' ),
				'show_currency_symbol' => 'yes',
				'show_clear'          => 'yes',
				'show_heading'        => 'yes',
				'slider'              => 'yes',
				'submit_button'       => 'no',
				'submit_button_label' => __( 'Go', 'woocommerce-product-search' ),
				'use_shop_url'        => 'no'
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-price-%d', $n );
		$heading_class   = 'product-search-filter-price-heading';
		$heading_id      = sprintf( 'product-search-filter-price-heading-%d', $n );
		$containers      = array();

		if ( $atts['heading'] === null ) {
			$atts['heading']  = _x( 'Price', 'product price filter heading', 'woocommerce-product-search' );
		}

		$params = array();
		foreach ( $atts as $key => $value ) {
			$is_param = true;
			if ( $value !== null ) {
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'fields' :
					case 'filter' :
					case 'show_currency_symbol' :
					case 'show_clear' :
					case 'show_heading' :
					case 'slider' :
					case 'submit_button' :
					case 'use_shop_url' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;

					case 'delay' :
						$value = intval( $value );
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
					case 'heading_no_results' :
						$value = esc_html( $value );
						break;

					case 'min_placeholder' :
					case 'max_placeholder' :
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

		$min_price   = isset( $_REQUEST['min_price'] ) ? WooCommerce_Product_Search_Service::to_float( $_REQUEST['min_price'] ) : '';
		$max_price   = isset( $_REQUEST['max_price'] ) ? WooCommerce_Product_Search_Service::to_float( $_REQUEST['max_price'] ) : '';
		if ( $min_price === null ) {
			$min_price = '';
		}
		if ( $max_price === null ) {
			$max_price = '';
		}
		if ( $min_price !== '' && $min_price < 0 ) {
			$min_price = '';
		}
		if ( $max_price !== '' && $max_price < 0 ) {
			$max_price = '';
		}
		if ( $min_price !== '' && $max_price !== '' && $max_price < $min_price ) {
			$max_price = '';
		}

		$output = apply_filters(
			'woocommerce_product_search_filter_price_prefix',
			sprintf(
				'<div id="%s" class="product-search-filter-price %s">',
				esc_attr( $container_id ),
				esc_attr( $container_class ) .
				( $params['slider'] ? ' show-slider ' : ' hide-slider ' ) .
				( $params['fields'] ? ' show-fields ' : ' hide-fields ' ) .
				( $params['submit_button'] ? ' show-submit ' : ' hide-submit' )
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
		$current_url = remove_query_arg( array( 'ixwpsp', 'min_price', 'max_price', 'paged' ), $current_url );
		$href        = $current_url;
		$add_post_type = false;
		if ( isset( $params['use_shop_url'] ) && $params['use_shop_url'] ) {
			$href = get_permalink( wc_get_page_id( 'shop' ) );
			if ( !$href ) {
				$query_post_type = WooCommerce_Product_Search_Filter::get_query_arg( $current_url, 'post_type' );
				if ( $query_post_type !== 'product' ) {
					$href = add_query_arg( array( 'post_type' => 'product' ), trailingslashit( home_url() ) );
					$add_post_type = true;
				}
			}
		}

		$min_field_id = 'product-search-filter-min-price-' . $n;
		$max_field_id = 'product-search-filter-max-price-' . $n;
		$form_id      = 'product-search-filter-price-form-' . $n;
		$slider_id    = 'product-search-filter-price-slider-' . $n;
		$slider_range_id = 'product-search-filter-price-slider-range-' . $n;

		$min_max = WooCommerce_Product_Search_Service::get_min_max_price();
		WooCommerce_Product_Search_Service::min_max_price_adjust_for_display( $min_max['min_price'], $min_max['max_price'] );

		if ( $min_price !== '' && $min_price < $min_max['min_price'] ) {
			$min_price = $min_max['min_price'];
		}
		if ( $max_price !== '' && $max_price > $min_max['max_price'] ) {
			$max_price = $min_max['max_price'];
		}

		if ( $params['slider'] ) {
			wp_enqueue_script( 'wps-price-slider' );
			wp_enqueue_style( 'wps-price-slider' );

			$output .= '<script type="text/javascript">';
			$output .= 'document.addEventListener( "DOMContentLoaded", function() {';
			$output .= 'if ( typeof jQuery !== "undefined" ) {';
			$output .= 'if ( typeof wps_price_slider !== "undefined" ) {';
			$output .= sprintf(
				'wps_price_slider.create( "%s", %d, %d, %d, %d, %d );',
				'#' . $slider_id,
				$min_max['min_price'],
				$min_max['max_price'],
				$min_price,
				$max_price,
				self::get_decimals()
			);
			$output .= '}';
			$output .= '}';
			$output .= '} );';
			$output .= '</script>';
		}

		$output .= sprintf(
			'<form id="%s" class="product-search-filter-price-form" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_url( $href )
		);

		$price_format = '%.0' . self::get_decimals() . 'F';
		$min_price_display = '';
		if ( $min_price !== '' ) {
			$min_price_display = sprintf( $price_format, $min_price );
		}
		$max_price_display = '';
		if ( $max_price !== '' ) {
			$max_price_display = sprintf( $price_format, $max_price );
		}
		$min_max_display = array(
			'min_price' => sprintf( $price_format, $min_max['min_price'] ),
			'max_price' => sprintf( $price_format, $min_max['max_price'] )
		);

		$output .= '<span class="min-max-fields">';
		$output .= sprintf(
			'<input id="%s" class="product-search-filter-price-field product-search-filter-min-price" type="text" name="min_price" value="%s" placeholder="%s" autocomplete="off"/>',
			esc_attr( $min_field_id ),
			esc_attr( $min_price_display ),
			esc_attr( $params['min_placeholder'] )
		);
		$output .= ' &mdash; ';
		$output .= sprintf(
			'<input id="%s" class="product-search-filter-price-field product-search-filter-max-price" type="text" name="max_price" value="%s" placeholder="%s" autocomplete="off"/>',
			esc_attr( $max_field_id ),
			esc_attr( $max_price_display ),
			esc_attr( $params['max_placeholder'] )
		);
		if ( $params['show_currency_symbol'] ) {
			$currency_symbol = get_woocommerce_currency_symbol();
			$output .= '<span class="product-search-filter-price-currency-symbol">';
			$output .= '&nbsp;' . esc_html( $currency_symbol );
			$output .= '</span>';
		}
		$output .= '</span>';

		if ( $add_post_type ) {
			$output .= '<input type="hidden" name="post_type" value="product"/>';
		}
		$output .= WooCommerce_Product_Search_Filter::render_query_args_form_fields( $current_url );
		$output .= '<input type="hidden" name="ixwpsp" value="1"/>';

		if ( $params['submit_button'] ) {
			$output .= ' ';
			$output .= sprintf( '<button class="button" type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
		} else {

			$output .= '<noscript>';
			$output .= sprintf( '<button class="button" type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
			$output .= '</noscript>';
		}

		if ( $params['slider'] ) {
			$output .= sprintf(
				'<div id="%s" class="product-search-filter-price-slider" data-min_price="%s" data-max_price="%s" data-precision="%s">' .
				'<div class="slider-min-max">' .
				'<span class="slider-min">%s</span>' .
				'<span class="slider-min-max-separator">' .
				'&ndash;' .
				'</span>' .
				'<span class="slider-max">%s</span>' .
				'</div>' .
				'<span class="slider-limit-min">%s</span>' .
				'<span class="slider-limit-max">%s</span>' .
				'</div>',
				esc_attr( $slider_id ),
				esc_attr( $min_max['min_price'] ),
				esc_attr( $min_max['max_price'] ),
				esc_attr( self::get_decimals() ),
				esc_html( $min_price_display ),
				esc_html( $max_price_display ),
				esc_html( $min_max_display['min_price'] ),
				esc_html( $min_max_display['max_price'] )
			);
		}

		if ( $params['show_clear'] ) {
			$output .= sprintf(
				'<span class="product-search-filter-price-clear" style="%s">',
				( empty( $min_price ) && empty( $max_price ) ? 'display:none' : '' )
			);
			$output .= __( 'Clear', 'woocommerce-product-search' );
			$output .= '</span>';
		}
		$output .= wc_query_string_form_fields( null, array( 'ixwpsp', 'min_price', 'max_price', 'submit', 'paged', 'product-page' ), '', true );
		$output .= '</form>';

		$output .= apply_filters(
			'woocommerce_product_search_filter_price_suffix',
			'</div>'
		);

		$output .= '<script type="text/javascript">';
		$output .= 'document.getElementById("' . $min_field_id . '").disabled = true;';
		$output .= 'document.getElementById("' . $max_field_id . '").disabled = true;';
		$output .= 'document.addEventListener( "DOMContentLoaded", function() {';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'if ( typeof jQuery().typeWatch !== "undefined" ) {';
		$output .= sprintf(
			'jQuery(".product-search-filter-price-field").typeWatch( {' .
			'callback: function (value) {' .
			'var ' .
				'min_price = jQuery(this).parent().find(".product-search-filter-min-price").first().val().trim(),' .
				'max_price = jQuery(this).parent().find(".product-search-filter-max-price").first().val().trim();' .
			'if ( min_price !== "" ) {' .
				'min_price = parseFloat( min_price );' .
				'if ( isNaN( min_price ) ) {' .
					'min_price = "";' .
				'}' .
				'jQuery(this).parent().find(".product-search-filter-min-price").first().val( min_price )' .
			'}' .
			'if ( max_price !== "" ) {' .
				'max_price = parseFloat( max_price );' .
				'if ( isNaN( max_price ) ) {' .
					'max_price = "";' .
				'}' .
				'jQuery(this).parent().find(".product-search-filter-max-price").first().val( max_price )' .
			'}' .

			'if ( typeof jQuery().slider !== "undefined" ) {' .
				'var slider = jQuery( this ).parent().find( ".product-search-filter-price-slider" );' .
				'if ( slider.length > 0 ) {' .
					'var min = slider.slider( "option", "min" ),' .
						'max = slider.slider( "option", "max" );' .
					'if ( min_price !== "" )  {' .
						'min = Math.max( Math.floor( min_price ), min );' .
					'}' .
					'if ( max_price !== "" )  {' .
						'max = Math.min( Math.ceil( max_price ), max );' .
					'}' .
					'slider.slider( "option", "values", [min, max] );' .
				'}' .
			'}' .

			'jQuery(".product-filter-field").first().trigger( "ixPriceFilter", [min_price, max_price] );' .
			'},' .
			'wait: %d,' .
			'highlight: true,' .
			'captureLength: %d' .
			'} );',
			$params['delay'],
			0
		);
		$output .= '} else {';
		$output .= 'if ( typeof console !== "undefined" && typeof console.log !== "undefined" ) {';
		$output .= 'document.getElementById("' . $min_field_id . '").disabled = false;';
		$output .= 'document.getElementById("' . $max_field_id . '").disabled = false;';
		$output .= 'console.log("A conflict is preventing required resources to be loaded.");';
		$output .= '}';
		$output .= '}';
		$output .= '}';
		$output .= '} );';
		$output .= '</script>';

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		return $output;
	}

	/**
	 * Returns the number of decimals.
	 *
	 * @return int
	 */
	public static function get_decimals() {
		$decimals = apply_filters( 'woocommerce_product_search_filter_price_decimals', self::DECIMALS );
		if ( !is_numeric( $decimals ) ) {
			$decimals = self::DECIMALS;
		}
		$decimals = max( 0, intval( $decimals ) );
		return $decimals;
	}
}
WooCommerce_Product_Search_Filter_Price::init();
