<?php
/**
 * Storefront Powerpack Frontend Product Shop Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Shop' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Shop extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'shop_layout' ), 999 );
			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'script' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 999 );
			add_action( 'woocommerce_before_shop_loop', 'sp_scroll_wrapper', 4 );
			add_action( 'woocommerce_after_shop_loop', 'sp_scroll_wrapper_close', 40 );

			global $storefront_version;

			if ( $storefront_version && version_compare( $storefront_version, '2.2.0', '<' ) ) {
				add_action( 'woocommerce_before_shop_loop', 'sp_product_loop_wrap', 40 );
				add_action( 'woocommerce_after_shop_loop', 'sp_product_loop_wrap_close', 5 );
			}

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
				add_filter( 'storefront_loop_columns', array( $this, 'shop_columns' ), 999 );
				add_filter( 'storefront_products_per_page', array( $this, 'shop_products_per_page' ), 999 );
			}
		}

		/**
		 * Shop Layout
		 * Tweaks the WooCommerce layout based on settings
		 */
		public function shop_layout() {
			$shop_layout                 = get_theme_mod( 'sp_shop_layout' );
			$archive_description         = get_theme_mod( 'sp_archive_description' );
			$archive_results_count       = get_theme_mod( 'sp_product_archive_results_count', true );
			$archive_sorting             = get_theme_mod( 'sp_product_archive_sorting',       true );
			$archive_image               = get_theme_mod( 'sp_product_archive_image',         true );
			$archive_sale_flash          = get_theme_mod( 'sp_product_archive_sale_flash',    true );
			$archive_rating              = get_theme_mod( 'sp_product_archive_rating',        true );
			$archive_price               = get_theme_mod( 'sp_product_archive_price',         true );
			$archive_add_to_cart         = get_theme_mod( 'sp_product_archive_add_to_cart',   true );
			$archive_product_description = get_theme_mod( 'sp_product_archive_description',   false );
			$archive_titles              = get_theme_mod( 'sp_product_archive_title',         true );
			$infinite_scroll             = get_theme_mod( 'sp_infinite_scroll',               false );


			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' === $shop_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}
			}

			if ( 'beneath' === $archive_description ) {
				remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
				remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
				add_action( 'woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5 );
				add_action( 'woocommerce_after_main_content', 'woocommerce_product_archive_description', 5 );
			}

			if ( false === $archive_results_count ) {
				remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			}

			if ( false === $archive_sorting ) {
				remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
			}

			if ( false === $archive_image ) {
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			}

			if ( false === $archive_sale_flash ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );
			}

			if ( false === $archive_rating ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			}

			if ( false === $archive_price ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			}

			if ( false === $archive_add_to_cart ) {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			}

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_page_template( 'template-homepage.php' ) ) {
				if ( false === $archive_titles ) {
					remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
				}
			}

			if ( true === $archive_product_description ) {
				add_action( 'woocommerce_after_shop_loop_item', 'sp_loop_product_description', 6 );
			}

			if ( ( true === $infinite_scroll ) && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
				remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
			}
		}

		/**
		 * Storefront Powerpack Body Class
		 *
		 * @param array $classes array of classes applied to the body tag.
		 * @see get_theme_mod()
		 */
		public function body_class( $classes ) {
			$shop_layout    = get_theme_mod( 'sp_shop_layout' );
			$shop_alignment = get_theme_mod( 'sp_shop_alignment' );

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' === $shop_layout ) {
					$classes[] = 'storefront-full-width-content';
				}
			}

			$classes[] = 'sp-shop-alignment-' . $shop_alignment;

			return $classes;
		}

		/**
		 * Shop columns
		 *
		 * @param int $columns number of product columns.
		 * @return integer shop columns
		 */
		public function shop_columns( $columns ) {
			$columns = get_theme_mod( 'sp_product_columns' );

			return $columns;
		}

		/**
		 * Shop products per page
		 *
		 * @param int $per_page products to display.
		 * @return integer shop products per page
		 */
		public function shop_products_per_page( $per_page ) {
			$per_page = get_theme_mod( 'sp_products_per_page' );
			return $per_page;
		}

		/**
		 * Enqueue styles and scripts.
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function script() {
			global $storefront_version;

			$infinite_scroll = get_theme_mod( 'sp_infinite_scroll', false );

			wp_enqueue_style( 'sp-styles', SP_PLUGIN_URL . 'assets/css/style.css', '', storefront_powerpack()->version );
			wp_style_add_data( 'sp-styles', 'rtl', 'replace' );

			// Compatibility with Storefront versions under 2.3.
			if ( version_compare( $storefront_version, '2.3.0', '<' ) ) {
				wp_enqueue_style( 'sp-fontawesome-4' );
			}

			/**
			 * Load the infinite scroll script on appropriate pages if the setting is enabled.
			 * Uses Jetpack's infinite scroll if available.
			 */
			if ( true === $infinite_scroll && ! ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) ) {
				if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
					wp_enqueue_script( 'jscroll', SP_PLUGIN_URL . 'assets/js/jquery.jscroll.min.js', array( 'jquery' ) );
					wp_enqueue_script( 'jscroll-init', SP_PLUGIN_URL . 'assets/js/jscroll-init.min.js', array( 'jscroll' ) );
				}
			}
		}

		/**
		 * Enqueue inline styles.
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function inline_css() {
			$star_color = get_theme_mod( 'sp_reviews_star_color' );

			$storefront_shop_style = '
				.star-rating span:before,
				.star-rating:before {
					color: ' . $star_color . ' !important;
				}

				.star-rating:before {
					opacity: 0.25 !important;
				}
			';

			wp_add_inline_style( 'storefront-woocommerce-style', $storefront_shop_style );
		}
	}

endif;

return new SP_Frontend_Shop();