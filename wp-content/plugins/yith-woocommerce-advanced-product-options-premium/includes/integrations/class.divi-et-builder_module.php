<?php

if ( ! class_exists( 'Divi_ET_Builder_Module_YITH_Product_Addons' ) ) {

	class Divi_ET_Builder_Module_YITH_Product_Addons extends ET_Builder_Module {
		
		function init() {
			$this->name = __('YITH Product Add-ons', 'et_builder');
			$this->slug = 'et_pb_yith_product_addons';
		}

		function get_fields() {
			$fields = array();
			return $fields;
		}

		function shortcode_callback( $atts, $content = null, $function_name ) {

			//////////////////////////////////////////////////////////////////////

			ob_start();

			if ( is_product() ) {
				do_action( 'yith_wapo_show_options_shortcode' );
			} else {
				echo '<strong>' . __( 'This is not a product page!', 'yith-woocommerce-product-add-ons' ) . '</strong>';
			}

			$content = ob_get_clean();

			//////////////////////////////////////////////////////////////////////

			if ( $content ) {
				$output = sprintf(
					'<div%5$s class="%1$s%3$s%6$s"> %2$s %4$s',
					'clearfix ',
					$content,
					esc_attr('et_pb_module et_pb_yith_product_addons et_pb_bg_layout_' . $background_layout . ' et_pb_text_align_' . $text_orientation),
					'</div>',
					( '' !== $module_id ? sprintf(' id="%1$s"', esc_attr( $module_id ) ) : '' ),
					( '' !== $module_class ? sprintf(' %1$s', esc_attr( $module_class ) ) : '' )
				);
			}

			return $output;
		}
	}

	new Divi_ET_Builder_Module_YITH_Product_Addons();

}