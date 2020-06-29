<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Append 'add to card' link to the list of Add link for grid element shortcodes.
 *
 * @param $param
 *
 * @return array
 * @since 4.5
 *
 */
function vc_gitem_add_link_param_woocommerce( $param ) {
	$param['value'][ esc_html__( 'WooCommerce add to card link', 'js_composer' ) ] = 'woo_add_to_card';

	return $param;
}

/**
 * Add WooCommerce link attributes to enable add to cart functionality
 *
 * @param $link
 * @param $atts
 * @param string $css_class
 *
 * @return string
 * @since 4.5
 *
 */
function vc_gitem_post_data_get_link_link_woocommerce( $link, $atts, $css_class = '' ) {
	if ( isset( $atts['link'] ) && 'woo_add_to_card' === $atts['link'] ) {
		$css_class .= ' add_to_cart_button vc-gitem-link-ajax product_type_simple';

		return 'a href="{{ woocommerce_product_link }}" class="' . esc_attr( $css_class ) . '" data-product_id="{{ woocommerce_product:id }}"' . ' data-product_sku="{{ woocommerce_product:sku }}" data-product-quantity="1"';
	}

	return $link;
}

/**¬
 * Remove target as useless for add to cart link.
 *
 * @param $link
 * @param $atts
 *
 * @return string
 * @since 4.5
 *
 */
function vc_gitem_post_data_get_link_target_woocommerce( $link, $atts ) {
	if ( isset( $atts['link'] ) && 'woo_add_to_card' === $atts['link'] ) {
		return '';
	}

	return $link;
}

/**
 * Add WooCommerce link attributes to enable add to cart functionality. Not using item element templates vars.
 *
 * @param $link
 * @param $atts
 * @param $post
 * @param string $css_class
 * @return string
 * @since 4.5
 *
 */
function vc_gitem_post_data_get_link_real_link_woocommerce( $link, $atts, $post, $css_class = '' ) {
	if ( isset( $atts['link'] ) && 'woo_add_to_card' === $atts['link'] ) {
		$css_class .= ' add_to_cart_button vc-gitem-link-ajax product_type_simple';

		$link = 'a href="' . esc_url( do_shortcode( '[add_to_cart_url id="' . $post->ID . '"]' ) ) . '" class="' . esc_attr( $css_class ) . '" data-product_id="' . esc_attr( vc_gitem_template_attribute_woocommerce_product( '', array(
				'post' => $post,
				'data' => 'id',
			) ) ) . '"' . ' data-product_sku="' . esc_attr( vc_gitem_template_attribute_woocommerce_product( '', array(
				'post' => $post,
				'data' => 'sku',
			) ) ) . '" data-product-quantity="1"';
	}

	return $link;
}

/**¬
 * Remove target as useless for add to cart link.
 *
 * @param $link
 * @param $atts
 * @param $post
 *
 * @return string
 * @since 4.5
 *
 */
function vc_gitem_post_data_get_link_real_target_woocommerce( $link, $atts, $post ) {
	return 'woo_add_to_card' === $link ? '' : $link;
}

/**
 * @param $image_block
 * @param $link
 * @param $css_class
 * @return string
 */
function vc_gitem_zone_image_block_link_woocommerce( $image_block, $link, $css_class ) {
	if ( 'woo_add_to_card' === $link ) {
		$css_class .= ' add_to_cart_button vc-gitem-link-ajax product_type_simple';

		return '<a href="{{ woocommerce_product_link }}" class="' . esc_attr( $css_class ) . '" data-product_id="{{ woocommerce_product:id }}"' . ' data-product_sku="{{ woocommerce_product:sku }}" data-product-quantity="1"></a>';
	}

	return $image_block;
}
