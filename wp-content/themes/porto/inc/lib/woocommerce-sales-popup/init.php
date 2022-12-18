<?php
/**
 * Porto WooCommerce Sales Popup Initialize
 *
 * @author     Porto Themes
 * @category   Library
 * @since      6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( wp_doing_ajax() ) {
	function porto_recent_sale_products() {
		//check_ajax_referer( 'porto-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		global $wpdb, $porto_settings;

		if ( ! empty( $_POST['load_first'] ) ) {
			$atts = array(
				'limit' => (int) $porto_settings['woo-sales-popup-count'],
			);
			$type = 'best_selling_products';

			switch ( $porto_settings['woo-sales-popup'] ) {
				case 'popular':
					$type = 'best_selling_products';
					break;
				case 'rating':
					$type = 'top_rated_products';
					break;
				case 'sale':
					$type = 'sale_products';
					break;
				case 'featured':
					$type = 'featured_products';
					break;
				case 'recent':
					$type            = 'recent_products';
					$atts['orderby'] = 'date';
					$atts['order']   = 'DESC';
					break;
			}

			$products = new Porto_Woocommerce_Sales_Popup( $atts, $type );

			echo json_encode( $products->get_products() );
		} else {

			$date   = date( 'Y-m-d H:i:s', strtotime( '-' . $porto_settings['woo-sales-popup-interval'] . ' seconds' ) );
			$result = $wpdb->get_results( $wpdb->prepare( 'select product_id, date_created from ' . $wpdb->prefix . 'wc_order_product_lookup where date_created>=%s ORDER BY date_created DESC', $date ) );

			$products = array();
			if ( $result ) {
				foreach ( $result as $item ) {
					$product    = wc_get_product( $item->product_id );
					$date       = $item->date_created;
					$products[] = array(
						'id'     => esc_html( $item->product_id ),
						'title'  => esc_html( $product->get_title() ),
						'link'   => esc_url( $product->get_permalink() ),
						'image'  => esc_js( wp_get_attachment_image_src( $product->get_image_id(), 'woocommerce_gallery_thumbnail' )[0] ),
						'price'  => $product->get_price_html(),
						'rating' => (float) $product->get_average_rating(),
						'date'   => Porto_Woocommerce_Sales_Popup::get_period_from( strtotime( $date ) ),
					);

				}
			}
			echo json_encode( $products );
		}
		// phpcs: enable
		die();
	}
	add_action( 'wp_ajax_porto_recent_sale_products', 'porto_recent_sale_products' );
	add_action( 'wp_ajax_nopriv_porto_recent_sale_products', 'porto_recent_sale_products' );
}

if ( class_exists( 'WC_Shortcode_Products' ) && ! class_exists( 'Porto_Woocommerce_Sales_Popup' ) ) :

	class Porto_Woocommerce_Sales_Popup extends WC_Shortcode_Products {

		public function __construct( $attributes = array(), $type = 'products' ) {
			parent::__construct( $attributes, $type );
		}

		public function get_products() {
			global $wpdb;
			$products = $this->get_query_results();
			$result   = array();
			if ( $products && $products->ids ) {
				foreach ( $products->ids as $product_id ) {
					$product  = wc_get_product( $product_id );
					$date     = $wpdb->get_var( $wpdb->prepare( 'select date_created from ' . $wpdb->prefix . 'wc_order_product_lookup where product_id=%d order by date_created DESC', $product_id ) );
					$result[] = array(
						'id'     => esc_html( $product_id ),
						'title'  => esc_html( $product->get_title() ),
						'link'   => esc_url( $product->get_permalink() ),
						'image'  => esc_js( wp_get_attachment_image_src( $product->get_image_id(), 'woocommerce_gallery_thumbnail' )[0] ),
						'price'  => $product->get_price_html(),
						'rating' => (float) $product->get_average_rating(),
						'date'   => isset( $date ) ? self::get_period_from( strtotime( $date ) ) : 'not sale',
					);
				}
			}
			return $result;
		}
		public static function get_period_from( $time ) {
			$time = time() - $time;     // to get the time since that moment
			$time = ( $time < 1 ) ? 1 : $time;

			$tokens = array(
				31536000 => 'year',
				2592000  => 'month',
				604800   => 'week',
				86400    => 'day',
				3600     => 'hour',
				60       => 'minute',
				1        => 'second',
			);

			foreach ( $tokens as $unit => $text ) {
				if ( $time < $unit ) {
					continue;
				}
				$number_of_units = floor( $time / $unit );
				if ( 'year' == $text ) {
					return sprintf( _n( '%d year ago', '%d years ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'month' == $text ) {
					return sprintf( _n( '%d month ago', '%d months ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'week' == $text ) {
					return sprintf( _n( '%d week ago', '%d weeks ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'day' == $text ) {
					return sprintf( _n( '%d day ago', '%d days ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'hour' == $text ) {
					return sprintf( _n( '%d hour ago', '%d hours ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'minute' == $text ) {
					return sprintf( _n( '%d minute ago', '%d minutes ago', $number_of_units, 'porto' ), $number_of_units );
				} elseif ( 'second' == $text ) {
					return sprintf( _n( '%d second ago', '%d seconds ago', $number_of_units, 'porto' ), $number_of_units );
				}
			}
		}
	}
endif;

if ( ! function_exists( 'porto_sales_popup_data' ) ) {

	function porto_sales_popup_data() {

		global $porto_settings;

		return array(
			'title'    => esc_js( $porto_settings['woo-sales-popup-title'] ),
			'type'     => esc_js( $porto_settings['woo-sales-popup'] ),
			'start'    => (int) $porto_settings['woo-sales-popup-start-delay'],
			'interval' => (int) $porto_settings['woo-sales-popup-interval'],
			'limit'    => (int) $porto_settings['woo-sales-popup-count'],
			'themeuri' => esc_url( PORTO_URI ),
		);
	}
}
