<?php
/**
 * Porto Woocommerce Image Swatches
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.7.0
 */

if ( ! class_exists( 'Porto_Woocommerce_Swatches' ) ) :
	class Porto_Woocommerce_Swatches {

		public function __construct() {

			add_action( 'init', array( $this, 'init' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1001 );
		}

		public function init() {
			global $porto_settings;
			if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() && current_user_can( 'manage_options' ) && isset( $porto_settings['product_variation_display_mode'] ) && 'button' == $porto_settings['product_variation_display_mode'] ) {
				require 'classes/class-product-swatches-tab.php';
				$this->product_data_tab = new Porto_Product_Swatches_Tab();

				add_action( 'wp_ajax_porto_load_swatches', array( $this, 'porto_load_swatches' ) );
				add_action( 'wp_ajax_nopriv_porto_load_swatches', array( $this, 'porto_load_swatches' ) );
			}

			$image_size = get_option( 'swatches_image_size', array() );
			$size       = array();

			$size['width']  = isset( $image_size['width'] ) && ! empty( $image_size['width'] ) ? $image_size['width'] : '32';
			$size['height'] = isset( $image_size['height'] ) && ! empty( $image_size['height'] ) ? $image_size['height'] : '32';
			$size['crop']   = isset( $image_size['crop'] ) ? $image_size['crop'] : 1;

			$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

			add_image_size( 'swatches_image_size', apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] ), apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] ), $image_size['crop'] );
		}

		public function enqueue_scripts() {
			global $pagenow;
			if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ! porto_is_elementor_preview() && is_admin() && ( 'post-new.php' == $pagenow || 'post.php' == $pagenow || 'edit.php' == $pagenow || 'edit-tags.php' == $pagenow ) ) {
				wp_enqueue_media();
				global $post;
				$data = array(
					'placeholder_src' => apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' ),
					'wpnonce'         => wp_create_nonce( 'porto_swatch_nonce' ),
					'ajax_url'        => esc_url( admin_url( 'admin-ajax.php' ) ),
				);
				if ( $post ) {
					$data['post_id'] = $post->ID;
				}
				wp_localize_script( 'porto-admin', 'porto_swatches_params', $data );
			}
		}

		public function porto_load_swatches() {
			if ( current_user_can( 'manage_options' ) && wp_verify_nonce( wp_unslash( $_POST['wpnonce'] ), 'porto_swatch_nonce' ) && $this->product_data_tab ) {
				echo porto_filter_output( $this->product_data_tab->render_product_tab_content( (int) $_POST['product_id'] ) );
				die();
			}
		}
	}
endif;

new Porto_Woocommerce_Swatches();
