<?php
/**
 * Radio Buttons option style
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

echo '<div class="ywcp_component_options_radio_container">';

echo '<span style="display: none;"><input type="radio" name="'.$option_name.'" value="-1" class="ywcp_radio_default_value"> ' . __( 'None' , 'yith-composite-products-for-woocommerce' ) . '</option></span>';

if ( $products_loop->have_posts() ) {

	while ( $products_loop->have_posts() ) : $products_loop->the_post();

        global $product;

		if( isset( $product ) ) {

			if ( ! $product->is_purchasable() ) {
				return;
			}

            $product_id = yit_get_base_product_id( $product );

			YITH_WCP_Frontend::markProductAsCompositeProcessed( $product, $component_product_id, $wcp_key );

			$price_html = $product->get_price_html();
			$price_html = ( !empty( $price_html ) ) ?  ' - '.$product->get_price_html() : '' ;

			$availability_text = YITH_WCP_Frontend::getAvailabilityText( $product );

			$title =  $product->get_title().$price_html.( $availability_text ? '('.$availability_text.')' : '' );

			$input_id = $option_name.'_'. $product_id ;

			echo '<div class="ywcp_component_options_radio_input_container">';

			echo '<input id="'.esc_attr( $input_id ).'" type="radio" name="'.$option_name.'" value="' . $product_id . '" class="ywcp_product_'.$product_id.'"><label for="'.esc_attr( $input_id ).'">' . $title .'</label>' ;

			echo '</div>';

		}

	endwhile;

}

echo '</div>';

echo YITH_WCP()->frontend->getNavigationLinks( $products_loop );

?>



