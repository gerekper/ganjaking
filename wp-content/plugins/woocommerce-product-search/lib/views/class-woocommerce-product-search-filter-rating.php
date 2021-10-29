<?php
/**
 * class-woocommerce-product-search-filter-rating.php
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
 * @since 2.20.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search_filter_rating' ) ) {
	/**
	 * Renders a rating filter for products.
	 * Returned as HTML and loads required resources.
	 *
	 * @param array $atts desired options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_rating( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Rating::render( $atts );
	}
}

/**
 * Filter reset.
 */
class WooCommerce_Product_Search_Filter_Rating {

	/**
	 * Minimum rating value.
	 *
	 * @var integer
	 */
	const MIN_RATING = 1;

	/**
	 * Maximum rating value.
	 *
	 * @var integer
	 */
	const MAX_RATING = 5;

	/**
	 * Ratings filter cache group.
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'ixwpse_rating';

	/**
	 * @var integer seconds in a day
	 */
	const SECONDS_PER_DAY = 86400;

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_rating', array( __CLASS__, 'shortcode' ) );
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
	 * [woocommerce_product_filter_rating] shortcode renderer.
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
	 * Renders the rating filter.
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
				'has_rating_only'    => 'yes',
				'heading'             => null,
				'heading_class'       => null,
				'heading_element'     => 'div',
				'heading_id'          => null,
				'show_heading'        => 'yes',

				'use_shop_url'        => 'no'
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-rating-%d', $n );
		$heading_class   = 'product-search-filter-rating-heading product-search-filter-extras-heading';
		$heading_id      = sprintf( 'product-search-filter-rating-heading-%d', $n );
		$containers      = array();

		if ( $atts['heading'] === null ) {
			$atts['heading']  = _x( 'Rating', 'product filter rating heading', 'woocommerce-product-search' );
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
					case 'has_rating_only' :

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

		$rating = isset( $_REQUEST['rating'] ) ? intval( $_REQUEST['rating'] ) : false;

		if ( $params['has_rating_only'] ) {
			if ( !self::has_ratings() ) {
				return '';
			}
		}

		$output = apply_filters(
			'woocommerce_product_search_filter_rating_prefix',
			sprintf(
				'<div id="%s" class="product-search-filter-extras product-search-filter-rating %s %s">',
				esc_attr( $container_id ),
				esc_attr( $container_class ),
				$rating ? ' product-search-filter-rating-active ' : ''
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

		$current_url = remove_query_arg( array( 'ixwpse', 'rating', 'paged' ), $current_url );
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

		$rating_field_id = 'product-search-filter-rating-' . $n;
		$form_id          = 'product-search-filter-rating-form-' . $n;

		$output .= sprintf(
			'<form id="%s" class="product-search-filter-extras-form product-search-filter-rating-form" action="%s" method="get">',
			esc_attr( $form_id ),
			esc_url( $href )
		);

		$output .= '<input type="hidden" name="ixwpse" value="1"/>';

		$output .= sprintf( '<input type="hidden" name="rating" value="%d" />', esc_attr( intval( $rating ) ) );

		$filter_extra_class = $params['filter'] ? '' : ' filter-dead ';

		$output .= '<ul class="rating-filter-options">';

		if ( $rating ) {
			$output .= '<li class="rating-clear">';
			$output .= sprintf(
				'<a title="%s" data-rating="" class="product-search-filter-extra rating-filter-clear %s" href="%s">%s</a>',
				esc_html__( 'Clear', 'woocommerce-product-search' ),
				esc_attr( $filter_extra_class ),
				esc_url( $href ),
				esc_html__( 'Clear', 'woocommerce-product-search' )
			);
			$output .= '</li>';
		}

		for ( $_rating = self::MAX_RATING; $_rating >= self::MIN_RATING; $_rating-- ) {
			$rating_title =
				$_rating < self::MAX_RATING ?
				sprintf( esc_html( _nx( 'Rated 1 Star &amp; Up', 'Rated %d Stars &amp; Up', $_rating, 'product filter rating', 'woocommerce-product-search' ) ), esc_attr( $_rating ) ) :
				sprintf( esc_html_x( 'Rated %d Stars', 'product filter rating', 'woocommerce-product-search' ), esc_attr( $_rating ) );
			$output .= sprintf(
				'<li class="%s">',
				$rating === $_rating ? ' rating-selected ' : ' rating-not-selected '
			);
			$output .= sprintf(
				'<a title="%s" data-rating="%d" class="product-search-filter-extra rating-filter-option %s %s" href="%s"><span class="rating-filter-star-rating rating-%d">%s</span></a>',
				esc_attr( $rating_title ),
				esc_attr( $_rating ),
				esc_attr( $filter_extra_class ),
				$rating === $_rating ? ' rating-selected ' : ' rating-not-selected ',
				esc_url( add_query_arg( array( 'ixwpse' => 1, 'rating' => $_rating ), $href ) ),
				esc_attr( $_rating ),
				$_rating !== self::MAX_RATING ? esc_html__( '& Up', 'woocommerce-product-search' ) : ''
			);
			$output .= '</li>';
		}
		$output .= '</ul>';

		if ( $add_post_type ) {
			$output .= '<input type="hidden" name="post_type" value="product"/>';
		}
		$output .= WooCommerce_Product_Search_Filter::render_query_args_form_fields( $current_url );

		$output .= '</form>';

		$output .= apply_filters(
			'woocommerce_product_search_filter_rating_suffix',
			'</div>'
		);

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		return $output;
	}

	/**
	 * Returns product IDs for products rated at least $rating.
	 *
	 * @param float $rating
	 *
	 * @return array
	 */
	public static function get_product_ids_by_rating_and_up( $rating = self::MIN_RATING ) {

		global $wpdb;
		$post_ids = array();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
			$query = sprintf(
				"SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.average_rating >= %s",
				esc_sql( floatval( $rating ) )
			);
		} else {
			$query = sprintf(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND meta_value >= %s",
				esc_sql( floatval( $rating ) )
			);
		}
		$post_ids = $wpdb->get_col( $query );
		if ( is_array( $post_ids ) ) {
			$post_ids = array_map( 'intval', $post_ids );
		}
		return $post_ids;
	}

	/**
	 * Returns true if there are any rated products.
	 *
	 * @return boolean
	 */
	public static function has_ratings() {

		global $wpdb;
		$result = false;

		$cached = wp_cache_get( 'has_ratings', self::CACHE_GROUP );
		if ( $cached !== false ) {
			$result = json_decode( $cached );
		} else {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
				$query = "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.average_rating >= 1.0 LIMIT 1";
			} else {
				$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND meta_value >= 1.0 LIMIT 1";
			}
			$post_ids = $wpdb->get_col( $query );
			if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
				$result = true;
			}

			$cached = wp_cache_set( 'has_ratings', json_encode( $result ), self::CACHE_GROUP, self::SECONDS_PER_DAY );
		}
		return $result;
	}
}
WooCommerce_Product_Search_Filter_Rating::init();
