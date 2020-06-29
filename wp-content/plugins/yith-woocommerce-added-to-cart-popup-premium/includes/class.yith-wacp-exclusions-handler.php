<?php
/**
 * Exclusion Handler class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_Exclusions_Handler' ) ) {
	/**
	 * YITH_WACP_Exclusions_Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WACP_Exclusions_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @since 1.0.0
		 * @var YITH_WACP_Exclusions_Handler
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WACP_Exclusions_Handler
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor method
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {

			// Save exclusions list.
			add_action( 'admin_init', array( $this, 'save_exclusions_prod' ) );
			add_action( 'admin_init', array( $this, 'save_exclusions_cat' ) );
			// Remove item from exclusions list.
			add_action( 'admin_init', array( $this, 'delete_exclusion_prod' ) );
			add_action( 'admin_init', array( $this, 'delete_exclusion_cat' ) );

			// Search products.
			add_action( 'wp_ajax_yith_wacp_search_products', array( $this, 'search_products_ajax' ) );
			// Search categories.
			add_action( 'wp_ajax_yith_wacp_search_categories', array( $this, 'search_categories_ajax' ) );
		}

		/**
		 * Save products exclusions
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function save_exclusions_prod() {

			if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'yith_wacp_add_exclusions_prod' ) || ! isset( $_POST['add_products'] ) ) {
				return;
			}

			// Get older items.
			$old_items = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-prod-list' ) ) );
			// Get new items.
			$new_items = wp_unslash( $_POST['add_products'] );
			$new_items = array_map( 'sanitize_text_field', is_array( $new_items ) ? $new_items : explode( ',', $new_items ) );
			$new_items = array_filter( $new_items );

			// Merge old with new.
			$exclusions = array_merge( $old_items, $new_items );

			update_option( 'yith-wacp-exclusions-prod-list', implode( ',', $exclusions ) );
		}

		/**
		 * Delete product from exclusions list
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function delete_exclusion_prod() {

			if ( ! isset( $_GET['remove_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['remove_nonce'] ) ), 'yith_wacp_remove_exclusions_prod' ) || ! isset( $_GET['remove_prod_exclusion'] ) ) {
				return;
			}

			$exclusions = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-prod-list' ) ) );
			$key        = array_search( sanitize_text_field( $_GET['remove_prod_exclusion'] ), $exclusions );
			if ( false !== $key ) {
				unset( $exclusions[ $key ] );
			}

			update_option( 'yith-wacp-exclusions-prod-list', implode( ',', $exclusions ) );

			$args = array( 'remove_nonce', 'remove_prod_exclusion' );
			$url  = esc_url_raw( remove_query_arg( $args ) );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Ajax action search products
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function search_products_ajax() {
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term       = ! empty( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
			$post_types = array( 'product' );

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'ids',
			);

			if ( is_numeric( $term ) ) {

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids',
				);

				$args3 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post_parent'    => $term,
					'fields'         => 'ids',
				);

				$args4 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $term,
							'compare' => 'LIKE',
						),
					),
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ), get_posts( $args4 ) ) );

			} else {

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $term,
							'compare' => 'LIKE',
						),
					),
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

			}

			$found_products = array();
			// Get excluded products.
			$excluded = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-prod-list', '' ) ) );

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$product = wc_get_product( $post );
					if ( in_array( $post, $excluded ) ) {
						continue;
					}
					$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
				}
			}

			wp_send_json( $found_products );
		}

		/**
		 * Save categories exclusions
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function save_exclusions_cat() {

			if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'yith_wacp_add_exclusions_cat' ) || ! isset( $_POST['add_categories'] ) ) {
				return;
			}

			// Get older items.
			$old_items = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-cat-list' ) ) );
			// Get new items.
			$new_items = wp_unslash( $_POST['add_categories'] );
			$new_items = array_map( 'sanitize_text_field', is_array( $new_items ) ? $new_items : explode( ',', $new_items ) );
			$new_items = array_filter( $new_items );

			// Merge old with new.
			$exclusions = array_merge( $old_items, $new_items );

			update_option( 'yith-wacp-exclusions-cat-list', implode( ',', $exclusions ) );
		}

		/**
		 * Delete category from exclusions list
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function delete_exclusion_cat() {

			if ( ! isset( $_GET['remove_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['remove_nonce'] ) ), 'yith_wacp_remove_exclusions_cat' ) || ! isset( $_GET['remove_cat_exclusion'] ) ) {
				return;
			}

			$exclusions = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-cat-list' ) ) );
			$key        = array_search( sanitize_text_field( $_GET['remove_cat_exclusion'] ), $exclusions );
			if ( false !== $key ) {
				unset( $exclusions[ $key ] );
			}

			update_option( 'yith-wacp-exclusions-cat-list', implode( ',', $exclusions ) );

			$args = array( 'remove_nonce', 'remove_cat_exclusion' );
			$url  = esc_url_raw( remove_query_arg( $args ) );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Ajax action search categories
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function search_categories_ajax() {
			ob_start();

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$term = ! empty( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

			if ( empty( $term ) ) {
				die();
			}

			$tax = array( 'product_cat' );

			// Search by name.
			$args = array(
				'hide_empty' => false,
				'fields'     => 'id=>name',
				'name__like' => $term,
			);

			$categories = get_terms( $tax, $args );

			if ( is_numeric( $term ) ) {
				// Search by id.
				$args = array(
					'hide_empty' => false,
					'fields'     => 'id=>name',
					'include'    => array( $term ),
				);

				$found = get_terms( $tax, $args );

				foreach ( $found as $id => $name ) {
					if ( array_key_exists( $id, $categories ) ) {
						continue;
					}
					$categories[ $id ] = $name;
				}
			} else {
				// Search by slug.
				$args = array(
					'hide_empty' => false,
					'fields'     => 'id=>name',
					'slug'       => $term,
				);

				$found = get_terms( $tax, $args );

				foreach ( $found as $id => $name ) {
					if ( array_key_exists( $id, $categories ) ) {
						continue;
					}
					$categories[ $id ] = $name;
				}
			}

			// Get excluded categories.
			$excluded = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-cat-list', '' ) ) );

			if ( $categories ) {
				foreach ( $excluded as $id ) {
					if ( array_key_exists( $id, $categories ) ) {
						unset( $categories[ $id ] );
					}
				}
			}

			wp_send_json( $categories );
		}

	}
}

/**
 * Unique access to instance of YITH_WACP_Exclusions_Handler class
 *
 * @since 1.0.0
 * @return YITH_WACP_Exclusions_Handler
 */
function YITH_WACP_Exclusions_Handler() { // phpcs:ignore
	return YITH_WACP_Exclusions_Handler::get_instance();
}
