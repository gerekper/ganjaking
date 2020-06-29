<?php
/**
 * Dropdown option style
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 *
 * @author 		YITHEMES
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

echo '<select name="' . $option_name . '" class="ywcp_component_otpions_select">';

if ( apply_filters( 'ywcp_show_default_none_option',true ) ) {

    echo '<option value="-1" >' . apply_filters( 'ywcp_component_otpions_select_none_text', __( 'None' , 'yith-composite-products-for-woocommerce' ) ) . '</option>';

}

if ( $products_loop->have_posts() ) {

	while ( $products_loop->have_posts() ) {

		$products_loop->the_post();

		global $product;
		
		if ( isset( $product ) && $product->is_purchasable() ) {

			YITH_WCP_Frontend::markProductAsCompositeProcessed( $product, $component_product_id, $wcp_key );

			$price_html = $product->get_price_html();
			$price_html = ( !empty( $price_html ) ) ?  ' - '.$product->get_price_html() : '' ;
            $availability_text = YITH_WCP_Frontend::getAvailabilityText( $product );
            $title =  $product->get_title() . $price_html . ( $availability_text ? '(' . $availability_text . ')' : '' );
			$title = apply_filters( 'yith_wcp_option_title', $title, $product->get_title(), $price_html, $availability_text );
			echo '<option value="' . yit_get_prop( $product, 'id' ) . '" class="ywcp_product_' . yit_get_prop( $product, 'id' ) . '">' . $title . '</option>';

		}

	}
}

echo '</select>';