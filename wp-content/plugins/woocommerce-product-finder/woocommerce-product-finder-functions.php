<?php
/**
 * Helper and wrapper functions for WooCommerce Product Finder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Template tag for advanced search display
 */
if ( ! function_exists( 'woocommerce_product_finder' ) ) {

	function woocommerce_product_finder( $args = array(), $echo = true ) {

		$defaults = array(
			'search_attributes' => array(),
			'use_category'      => '',
		);

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		if ( false === is_bool( $use_category ) ) {
			if ( 'no' === $use_category ) {
				$use_category = false;
			} elseif ( 'yes' === $use_category ) {
				$use_category = true;
			} else {
				$use_cat = get_option( 'advanced_search_atts_product_cat' );
				if ( ! $use_cat || 'yes' === $use_cat ) {
					$use_category = true;
				} else {
					$use_category = false;
				}
			}
		}

		$html = woocommerce_product_finder_display( $args , $search_attributes , $use_category );

		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'woocommerce_product_finder_shortcode' ) ) {

	function woocommerce_product_finder_shortcode( $args = '' ) {
		$defaults = array(
			'search_attributes' => array(),
			'use_category'      => '',
		);

		$args = shortcode_atts( $defaults , $args );
		extract( $args, EXTR_SKIP );

		if ( 'no' === $use_category ) {
			$use_category = false;
		} elseif ( 'yes' === $use_category ) {
			$use_category = true;
		} else {
			$use_cat = get_option( 'advanced_search_atts_product_cat' );
			if ( ! $use_cat || 'yes' === $use_cat ) {
				$use_category = true;
			} else {
				$use_category = false;
			}
		}

		return woocommerce_product_finder_display( $args , $search_attributes , $use_category );
	}

}

if ( ! function_exists( 'woocommerce_product_finder_display' ) ) {

	function woocommerce_product_finder_display( $args, $search_attributes, $use_category ) {

		if ( ! is_array( $search_attributes ) ) {
			$search_attributes = explode( ',' , $search_attributes );
		}

		if ( 0 === count( $search_attributes ) ) {
			$att_list = wc_get_attribute_taxonomies();
			if ( $att_list && is_array( $att_list ) && count( $att_list ) > 0 ) {
				foreach ( $att_list as $att ) {
					if ( isset( $att->attribute_name ) && strlen( $att->attribute_name ) > 0 ) {
						$tax_name = wc_attribute_taxonomy_name( $att->attribute_name );
						$use_tax  = get_option( 'advanced_search_atts_' . $tax_name );
						if ( ! $use_tax || 'yes' === $use_tax ) {
							$search_attributes[] = $att->attribute_name;
						}
					}
				}
			}
		}

		return WooCommerce_Product_Finder::search_form( $search_attributes , $use_category );
	}
}

// Enable do_action( 'woocommerce_advanced_search' , $args );
add_action( 'woocommerce_product_finder', 'woocommerce_product_finder' , 10 , 1 );

// Register shortcode to display advanced search
add_shortcode( 'woocommerce_product_finder' , 'woocommerce_product_finder_shortcode' );
