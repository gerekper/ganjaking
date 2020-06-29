<?php

class WC_CVO_Visibility_Options {

	public function __construct() {
		//add_action( 'woocommerce_init', array( $this, 'on_woocommerce_init' ) );
	}

	public function on_woocommerce_init() {
		global $wc_cvo;

		//Note:  This module is disabled, all filtering is done in the catalog-restrictions
		if (false && $wc_cvo->setting( 'wc_cvo_prices' ) != 'enabled' || $wc_cvo->setting( 'wc_cvo_atc' ) != 'enabled' ) {

			//Configure replacement HTML and content.
			//Note:  If prices are disabled, and purchases are enabled, the alternate add-to-cart button content will still be used.
			//       Add to cart only makes sense when prices are visibile.
			if (
				( ( $wc_cvo->setting( 'wc_cvo_atc' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_atc' ) == 'disabled' ) ||
				( ( $wc_cvo->setting( 'wc_cvo_prices' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_prices' ) == 'disabled' )
			) {

				remove_shortcode( 'woocommerce_cart' );
				remove_shortcode( 'woocommerce_checkout' );
				remove_shortcode( 'woocommerce_order_tracking' );

				add_shortcode( 'woocommerce_cart', array( $this, 'get_woocommerce_cart' ) );
				add_shortcode( 'woocommerce_checkout', array( $this, 'get_woocommerce_checkout' ) );
				add_shortcode( 'woocommerce_order_tracking', array( $this, 'get_woocommerce_order_tracking' ) );
			}
		}
	}


	/*
	 * Replacement Shortcodes
	 */

	public function get_woocommerce_cart( $atts ) {
		global $woocommerce;

		return $woocommerce->shortcode_wrapper( array( $this, 'alternate_single_product_content' ), $atts );
	}

	public function get_woocommerce_checkout( $atts ) {
		global $woocommerce;

		return $woocommerce->shortcode_wrapper( array( $this, 'alternate_single_product_content' ), $atts );
	}

	public function get_woocommerce_order_tracking( $atts ) {
		global $woocommerce;

		return $woocommerce->shortcode_wrapper( array( $this, 'alternate_single_product_content' ), $atts );
	}

	public function alternate_single_product_content( $atts ) {
		global $wc_cvo;

		$html = '';

		if ( ( $wc_cvo->setting( 'wc_cvo_prices' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_prices' ) == 'disabled' ) {
			$html = apply_filters( 'catalog_visibility_alternate_content', apply_filters( 'the_content', $wc_cvo->setting( 'wc_cvo_s_price_text' ) ) );
		} elseif ( ( $wc_cvo->setting( 'wc_cvo_atc' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_atc' ) == 'disabled' ) {
			$html = apply_filters( 'catalog_visibility_alternate_content', apply_filters( 'the_content', $wc_cvo->setting( 'wc_cvo_s_price_text' ) ) );
		}

		echo $html;
	}

	/*
	 * Replacement HTML
	 */

	public function on_price_html( $html, $product ) {
		global $wc_cvo;
		if ( ! WC_Catalog_Restrictions_Filters::instance()->user_can_view_price( $product ) ) {
			if ( ( $wc_cvo->setting( 'wc_cvo_prices' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_prices' ) == 'disabled' ) {
				return apply_filters( 'catalog_visibility_alternate_price_html', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $product );
			}
		}

		return $html;
	}

	public function on_cart_item_price_html( $price, $cart_item ) {
		global $wc_cvo;
		$product = $cart_item['data'];

		if ( ! WC_Catalog_Restrictions_Filters::instance()->user_can_view_price( $product ) ) {
			if ( ( $wc_cvo->setting( 'wc_cvo_prices' ) == 'secured' && ! catalog_visibility_user_has_access() ) || $wc_cvo->setting( 'wc_cvo_prices' ) == 'disabled' ) {
				return apply_filters( 'catalog_visibility_alternate_cart_item_price_html', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $cart_item );
			}
		}

		return $price;
	}


}
