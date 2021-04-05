<?php
/**
 * Storefront Powerpack Frontend Homepage Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Homepage' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Homepage extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'storefront_product_categories_args', array( $this, 'product_category_args' ) );
			add_filter( 'storefront_recent_products_args', array( $this, 'recent_product_args' ) );
			add_filter( 'storefront_featured_products_args', array( $this, 'featured_product_args' ) );
			add_filter( 'storefront_popular_products_args', array( $this, 'popular_product_args' ) );
			add_filter( 'storefront_on_sale_products_args', array( $this, 'on_sale_product_args' ) );
			add_filter( 'storefront_best_selling_products_args', array( $this, 'best_selling_args' ) );

			add_action( 'wp', array( $this, 'shop_layout' ), 999 );

			add_action( 'storefront_homepage_after_product_categories',           'sp_homepage_product_categories_view_more' );
			add_action( 'storefront_homepage_after_recent_products',              'sp_homepage_recent_products_view_more' );
			add_action( 'storefront_homepage_after_featured_products',            'sp_homepage_featured_products_view_more' );
			add_action( 'storefront_homepage_after_popular_products',             'sp_homepage_top_rated_products_view_more' );
			add_action( 'storefront_homepage_after_on_sale_products',             'sp_homepage_on_sale_products_view_more' );
			add_action( 'storefront_homepage_after_best_selling_products',        'sp_homepage_best_selling_products_view_more' );

			add_action( 'storefront_homepage_after_product_categories_title',     'sp_homepage_product_categories_description' );
			add_action( 'storefront_homepage_after_recent_products_title',        'sp_homepage_recent_products_description' );
			add_action( 'storefront_homepage_after_featured_products_title',      'sp_homepage_featured_products_description' );
			add_action( 'storefront_homepage_after_popular_products_title',       'sp_homepage_popular_products_description' );
			add_action( 'storefront_homepage_after_on_sale_products_title',       'sp_homepage_on_sale_products_description' );
			add_action( 'storefront_homepage_after_best_selling_products_title',  'sp_homepage_best_selling_products_description' );
		}

		/**
		 * Filter the homepage on sale product args
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function on_sale_product_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_on_sale_products_title' );
			$columns = get_theme_mod( 'sp_homepage_on_sale_products_columns' );
			$limit 	 = get_theme_mod( 'sp_homepage_on_sale_products_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Filter the homepage popular product args
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function popular_product_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_top_rated_products_title' );
			$columns = get_theme_mod( 'sp_homepage_top_rated_products_columns' );
			$limit 	 = get_theme_mod( 'sp_homepage_top_rated_products_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Filter the homepage featured product args
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function featured_product_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_featured_products_title' );
			$columns = get_theme_mod( 'sp_homepage_featured_products_columns' );
			$limit 	 = get_theme_mod( 'sp_homepage_featured_products_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Filter the homepage recent product args
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function recent_product_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_recent_products_title' );
			$columns = get_theme_mod( 'sp_homepage_recent_products_columns' );
			$limit   = get_theme_mod( 'sp_homepage_recent_products_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Filter the homepage best seller product args
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function best_selling_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_best_sellers_products_title' );
			$columns = get_theme_mod( 'sp_homepage_best_sellers_products_columns' );
			$limit   = get_theme_mod( 'sp_homepage_best_sellers_products_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Filter the homepage product categories
		 *
		 * @param  array $args the default args.
		 * @return array $args the filtered args based on settings
		 */
		public function product_category_args( $args ) {
			$title   = get_theme_mod( 'sp_homepage_category_title' );
			$columns = get_theme_mod( 'sp_homepage_category_columns' );
			$limit   = get_theme_mod( 'sp_homepage_category_limit' );

			if ( ! empty( $title ) ) {
				$args['title'] = $title;
			}

			$args['columns'] = $columns;
			$args['limit']   = $limit;

			return $args;
		}

		/**
		 * Shop Layout
		 * Tweaks the WooCommerce layout based on settings
		 */
		public function shop_layout() {
			$homepage_content      = get_theme_mod( 'sp_homepage_content', true );
			$homepage_cats         = get_theme_mod( 'sp_homepage_categories', true );
			$homepage_recent       = get_theme_mod( 'sp_homepage_recent', true );
			$homepage_featured     = get_theme_mod( 'sp_homepage_featured', true );
			$homepage_top_rated    = get_theme_mod( 'sp_homepage_top_rated', true );
			$homepage_on_sale      = get_theme_mod( 'sp_homepage_on_sale', true );
			$homepage_best_sellers = get_theme_mod( 'sp_homepage_best_sellers', true );

			if ( false === $homepage_content ) {
				remove_action( 'homepage', 'storefront_homepage_content', 10 );
			}

			if ( false === $homepage_cats ) {
				remove_action( 'homepage', 'storefront_product_categories', 20 );
			}

			if ( false === $homepage_recent ) {
				remove_action( 'homepage', 'storefront_recent_products', 30 );
			}

			if ( false === $homepage_featured ) {
				remove_action( 'homepage', 'storefront_featured_products', 40 );
			}

			if ( false === $homepage_top_rated ) {
				remove_action( 'homepage', 'storefront_popular_products', 50 );
			}

			if ( false === $homepage_on_sale ) {
				remove_action( 'homepage', 'storefront_on_sale_products', 60 );
			}

			if ( false === $homepage_best_sellers ) {
				remove_action( 'homepage', 'storefront_best_selling_products', 70 );
			}
		}

	}

endif;

return new SP_Frontend_Homepage();