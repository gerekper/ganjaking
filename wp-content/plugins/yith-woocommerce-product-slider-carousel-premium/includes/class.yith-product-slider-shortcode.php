<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Product_Slider_Shortcode' ) ) {

	/**
	 * YITH_Product_Slider_Shortcode
	 */
	class YITH_Product_Slider_Shortcode {

		/**
		 * Print_product_slider
		 *
		 * @param mixed $atts atts.
		 * @param mixed $content content.
		 *
		 * @return apply_filter
		 */
		public static function print_product_slider( $atts, $content = null ) {

			global $woocommerce_loop;

			$default_attrs = array(
				'id'                => '',
				'z_index'           => '',
				'en_responsive'     => get_option( 'ywcps_check_responsive' ) === 'yes' ? 'true' : 'false',
				'n_item_desk_small' => get_option( 'ywcps_n_item_small_desk' ),
				'n_item_tablet'     => get_option( 'ywcps_n_item_tablet' ),
				'n_item_mobile'     => get_option( 'ywcps_n_item_mobile' ),
				'is_rtl'            => get_option( 'ywcps_check_rtl' ) === 'yes' ? 'true' : 'false',
				'posts_per_page'    => get_option( 'ywcps_n_posts_per_page' ),
			);

			$atts = shortcode_atts( $default_attrs, $atts );

			extract( $atts ); //phpcs:ignore WordPress.PHP.DontExtract

			$id = intval( yith_wpml_get_translated_id( $id, 'yith_wcps_type' ) );

			$show_title       = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcp_show_title', true ) );
			$hide_add_to_cart = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_hide_add_to_cart', true ) );
			$hide_price       = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_hide_price', true ) );
			$is_loop          = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_check_loop', true ) ) ? 'true' : 'false';
			$stop_hov         = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_stop_hover', true ) ) ? 'true' : 'false';
			$show_nav         = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_show_navigation', true ) ) ? 'true' : 'false';
			$show_dot_nav     = yith_plugin_fw_is_true( get_post_meta( $id, '_ywcps_show_dot_navigation', true ) ) ? 'true' : 'false';

			if ( isset( $id ) && ! empty( $id ) ) {

				$extra_params    = array(
					'en_responsive'     => $en_responsive,
					'n_item_desk_small' => $n_item_desk_small,
					'n_item_tablet'     => $n_item_tablet,
					'n_item_mobile'     => $n_item_mobile,
					'is_rtl'            => $is_rtl,
					'posts_per_page'    => $posts_per_page,

					// Slider Settings!
					'title'             => get_the_title( $id ),
					'how_category'      => get_post_meta( $id, '_ywcps_all_cat', true ),
					'how_brands'        => get_post_meta( $id, '_ywcps_all_brand', true ),
					'product_type'      => get_post_meta( $id, '_ywcps_product_type', true ),
					'show_title'        => $show_title,
					'hide_add_to_cart'  => $hide_add_to_cart,
					'hide_price'        => $hide_price,
					'n_items'           => get_post_meta( $id, '_ywcps_image_per_row', true ),
					'order_by'          => get_post_meta( $id, '_ywcps_order_by', true ),
					'order'             => get_post_meta( $id, '_ywcps_order_type', true ),
					'is_loop'           => $is_loop,
					'page_speed'        => get_post_meta( $id, '_ywcps_pagination_speed', true ),
					'auto_play'         => get_post_meta( $id, '_ywcps_auto_play', true ),
					'stop_hov'          => $stop_hov,
					'show_nav'          => $show_nav,
					'anim_in'           => get_post_meta( $id, '_ywcps_animate_in', true ),
					'anim_out'          => get_post_meta( $id, '_ywcps_animate_out', true ),
					'anim_speed'        => get_post_meta( $id, '_ywcps_animation_speed', true ),
					'show_dot_nav'      => $show_dot_nav,
					'template_slider'   => get_post_meta( $id, '_ywcps_layout_type', true ),
				);
				$atts['id']      = $id;
				$atts['z_index'] = $z_index;
				$atts            = array_merge( $extra_params, $atts );
				// let third part filter shortcode attr!
				$atts['atts'] = apply_filters( 'yith_product_slider_attributes', $atts, $id );

				$old_woocommerce_loop = $woocommerce_loop;
				/**
				 * Since 1.0.5
				 *
				 * @param $woocommerce_loop mixed Woocommerce loop global
				 * @param $plugin_slug string Current plugin slug
				 */

				$woocommerce_loop = apply_filters( 'yith_customize_product_carousel_loop', $woocommerce_loop, YWCPS_SLUG );

				ob_start();
				wc_get_template( 'product_slider_view.php', $atts, '', YWCPS_TEMPLATE_PATH );
				$template = ob_get_contents();
				ob_end_clean();
				$woocommerce_loop = $old_woocommerce_loop;

				return apply_filters( 'yith_wcpsl_productslider_html', $template, array(), true );

			}

		}
	}
}
add_shortcode( 'yith_wc_productslider', array( 'YITH_Product_Slider_Shortcode', 'print_product_slider' ) );
