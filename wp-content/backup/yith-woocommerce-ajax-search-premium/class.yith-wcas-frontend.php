<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS_Frontend {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// custom styles and javascripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_filter( 'yith_wcas_ajax_search_icon', array( $this, 'ajax_loader' ), 11 );
			add_filter( 'body_class', array( $this, 'add_theme_name_to_body' ) );

		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {

			$autocomplete_enabled = apply_filters( 'yith_wcas_enable_autocomplete', true );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( $autocomplete_enabled ) {

				wp_register_script( 'yith_autocomplete', YITH_WCAS_URL . 'assets/js/yith-autocomplete' . $suffix . '.js', array( 'jquery' ), '1.2.7', true );
				wp_register_script( 'yith_wcas_jquery-autocomplete', YITH_WCAS_URL . 'assets/js/devbridge-jquery-autocomplete' . $suffix . '.js', array( 'jquery' ), '1.2.7', true );

				wp_register_script( 'yith_wcas_frontend', YITH_WCAS_URL . 'assets/js/frontend' . $suffix . '.js', array( 'jquery' ), '1.0', true );

				wp_localize_script(
					'yith_wcas_frontend',
					'yith_wcas_params',
					array(
						'loading'       => YITH_WCAS_ASSETS_IMAGES_URL . 'ajax-loader.gif',
						'show_all'      => get_option( 'yith_wcas_search_show_view_all' ) === 'yes' ? 'true' : 'false',
						'price_label'   => get_option( 'yith_wcas_search_price_label' ),
						'show_all_text' => get_option( 'yith_wcas_search_show_view_all_text' ),
						'ajax_url'      => $this->get_ajax_url(),
					)
				);

				wp_enqueue_script( 'yith_autocomplete' );
			}

			$css = file_exists( get_stylesheet_directory() . '/woocommerce/yith_ajax_search.css' ) ? get_stylesheet_directory_uri() . '/woocommerce/yith_ajax_search.css' : YITH_WCAS_URL . 'assets/css/yith_wcas_ajax_search.css';
			wp_enqueue_style( 'yith_wcas_frontend', $css, array(), YITH_WCAS_VERSION );

			$padding_to_item = ( get_option( 'yith_wcas_show_sale_badge' ) === 'yes' ) ? '20px' : '0px';

			$sale_color         = get_option( 'yith_wcas_sale_badge_color' );
			$sale_badge_bgcolor = isset( $sale_color['bgcolor'] ) ? $sale_color['bgcolor'] : '#7eb742';
			$sale_badge_color   = isset( $sale_color['color'] ) ? $sale_color['color'] : '#ffffff';

			$outofstock_color         = get_option( 'yith_wcas_outofstock_color' );
			$outofstock_badge_bgcolor = isset( $outofstock_color['bgcolor'] ) ? $outofstock_color['bgcolor'] : '#7a7a7a';
			$outofstock_badge_color   = isset( $outofstock_color['color'] ) ? $outofstock_color['color'] : '#ffffff';

			$featured_color         = get_option( 'yith_wcas_featured_badge_color' );
			$featured_badge_bgcolor = isset( $featured_color['bgcolor'] ) ? $featured_color['bgcolor'] : '#c0392b';
			$featured_badge_color   = isset( $featured_color['color'] ) ? $featured_color['color'] : '#ffffff';

			$thumb_size  = get_option( 'yith_wcas_search_show_thumbnail_dim' );
			$title_color = get_option( 'yith_wcas_search_title_color' );
			$min_height  = $thumb_size + 10;
			$custom_css  = "
                .autocomplete-suggestion{
                    padding-right: {$padding_to_item};
                }
                .woocommerce .autocomplete-suggestion  span.yith_wcas_result_on_sale,
                .autocomplete-suggestion  span.yith_wcas_result_on_sale{
                        background: {$sale_badge_bgcolor};
                        color: {$sale_badge_color}
                }
                .woocommerce .autocomplete-suggestion  span.yith_wcas_result_outofstock,
                .autocomplete-suggestion  span.yith_wcas_result_outofstock{
                        background: {$outofstock_badge_bgcolor};
                        color: {$outofstock_badge_color}
                }
                .woocommerce .autocomplete-suggestion  span.yith_wcas_result_featured,
                .autocomplete-suggestion  span.yith_wcas_result_featured{
                        background: {$featured_badge_bgcolor};
                        color: {$featured_badge_color}
                }
                .autocomplete-suggestion img{
                    width: {$thumb_size}px;
                }
                .autocomplete-suggestion .yith_wcas_result_content .title{
                    color: {$title_color};
                }
                ";
			if ( get_option( 'yith_wcas_show_thumbnail' ) !== 'none' ) {
				$custom_css .= ".autocomplete-suggestion{
                                    min-height: {$min_height}px;
                                }";
			}
			wp_add_inline_style( 'yith_wcas_frontend', $custom_css );

		}

		/**
		 * Return the address to use ajax in javascript
		 *
		 * @return string
		 */
		public function get_ajax_url() {
			$ajax_url = version_compare( WC()->version, '2.4.0', '<' ) ? 'admin-ajax.php?action=yith_ajax_search_products' : WC_AJAX::get_endpoint( '%%endpoint%%' );

			return apply_filters( 'ywcas_ajax_url', $ajax_url );
		}

		/**
		 * Return the images loader updated in settings
		 *
		 * @access public
		 * @param string $value Value of ajax loader.
		 * @return string
		 * @since 1.0.0
		 */
		public function ajax_loader( $value ) {
			if ( get_option( 'yith_wcas_loader_url' ) ) {
				$value = get_option( 'yith_wcas_loader_url' );
			}
			return $value;
		}

		/**
		 * Add a class in the body with the name of theme
		 *
		 * @access public
		 * @param array $classes Array of classes.
		 * @return string
		 * @since 1.0.0
		 */
		public function add_theme_name_to_body( $classes ) {

			$theme = wp_get_theme();

			if ( ! $theme ) {
				return;
			}

			$classes[] = 'ywcas-' . sanitize_title( $theme->get( 'Name' ) );

			return $classes;
		}

	}
}
