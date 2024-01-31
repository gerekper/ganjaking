<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_Shortcodes' ) ) {

	class YITH_Delivery_Date_Shortcodes {


		public static function print_dynamic_message_shortcode( $atts = array() ) {

			$default = array(
				'product_id' => ''
			);

			$atts    = shortcode_atts( $default, $atts );
			$product = null;
			if ( '' !== $atts['product_id'] ) {
				$product = wc_get_product( $atts['product_id'] );
			}

			if ( ! $product instanceof WC_Product ) {
				global $product;
			}

			if ( $product instanceof WC_Product && ! $product->is_downloadable() && ! $product->is_virtual() && apply_filters( 'yith_delivery_date_show_date_info', true, $product ) ) {

				ob_start();
				YITH_Delivery_Date_Product_Frontend()->get_template_info( $product );
				$template = ob_get_contents();
				ob_end_clean();

				return $template;
			}
			return '';
		}

	}
}

add_shortcode( 'ywcdd_dynamic_messages', array( 'YITH_Delivery_Date_Shortcodes', 'print_dynamic_message_shortcode' ) );
