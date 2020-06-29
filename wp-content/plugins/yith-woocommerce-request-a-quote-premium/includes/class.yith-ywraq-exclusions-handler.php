<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Implements the YITH_YWRAQ_Exclusions_Handler class.
 *
 * @class    YITH_YWRAQ_Exclusions_Handler
 * @package  YITH
 * @since    2.0.0
 * @author   YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Exclusions_Handler' ) ) {
	/**
	 * YITH_YWRAQ_Exclusions_Handler
	 *
	 * @since 2.0.0
	 */
	class YITH_YWRAQ_Exclusions_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_YWRAQ_Exclusions_Handler
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_YWRAQ_Exclusions_Handler
		 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		public function __construct() {

			// save exclusions list.
			add_action( 'admin_init', array( $this, 'save_exclusions_prod' ) );
			add_action( 'admin_init', array( $this, 'save_exclusions_cat' ) );
			add_action( 'admin_init', array( $this, 'save_exclusions_tag' ) );

			// remove item from exclusions list.
			add_action( 'admin_init', array( $this, 'delete_exclusion_prod' ) );
			add_action( 'admin_init', array( $this, 'delete_exclusion_cat' ) );
			add_action( 'admin_init', array( $this, 'delete_exclusion_tag' ) );

			// search products.
			add_action( 'wp_ajax_yith_ywraq_search_products', array( $this, 'search_products_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_ywraq_search_products', array( $this, 'search_products_ajax' ) );

			// search categories.
			add_action( 'wp_ajax_yith_ywraq_search_categories', array( $this, 'search_categories_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_ywraq_search_categories', array( $this, 'search_categories_ajax' ) );

			// search tags.
			add_action( 'wp_ajax_yith_ywraq_search_tags', array( $this, 'search_tags_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_ywraq_search_tags', array( $this, 'search_tags_ajax' ) );
		}

		/**
		 * Save products exclusions
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function save_exclusions_prod() {

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( ! isset( $posted['_nonce'] ) || ! wp_verify_nonce( $posted['_nonce'], 'yith_ywraq_add_exclusions_prod' ) || ! isset( $posted['add_products'] ) ) {
				return;
			}

			// get older items.
			$old_items = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-prod-list' ) ) );
			// get new items.
			$new_items = $posted['add_products'];

			! is_array( $new_items ) && $new_items = explode( ',', $new_items );

			$new_items = array_filter( $new_items );

			// merge old with new.
			$exclusions = array_merge( $old_items, $new_items );

			update_option( 'yith-ywraq-exclusions-prod-list', implode( ',', $exclusions ) );
		}

		/**
		 * Delete product from exclusions list
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function delete_exclusion_prod() {
			$get = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $get['remove_nonce'] ) || ! wp_verify_nonce( $get['remove_nonce'], 'yith_ywraq_remove_exclusions_prod' ) || ! isset( $get['remove_prod_exclusion'] ) ) {
				return;
			}

			$exclusions = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-prod-list' ) ) );

			if ( ( $key = array_search( $get['remove_prod_exclusion'], $exclusions ) ) !== false ) {
				unset( $exclusions[ $key ] );
			}

			update_option( 'yith-ywraq-exclusions-prod-list', implode( ',', $exclusions ) );

			$args = array( 'remove_nonce', 'remove_prod_exclusion' );
			$url  = esc_url_raw( remove_query_arg( $args ) );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Ajax action search products
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function search_products_ajax() {
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term       = (string) wc_clean( stripslashes( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) );
			$post_types = array( 'product' );

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				's'              => $term,
				'fields'         => 'ids',
			);

			if ( is_numeric( $term ) ) {

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids',
				);

				$args3 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'post_parent'    => $term,
					'fields'         => 'ids',
				);

				$args4 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
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
					'posts_per_page' => - 1,
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
			// get excluded products.
			$excluded = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-prod-list', '' ) ) );

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$product = wc_get_product( $post );
					if ( in_array( $post, $excluded, true ) ) {
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
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function save_exclusions_cat() {

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! isset( $posted['_nonce'] ) || ! wp_verify_nonce( $posted['_nonce'], 'yith_ywraq_add_exclusions_cat' ) || ! isset( $posted['add_categories'] ) ) {
				return;
			}

			// get older items.
			$old_items = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-cat-list' ) ) );
			// get new items.
			$new_items = $posted['add_categories'];

			! is_array( $new_items ) && $new_items = explode( ',', $posted['add_categories'] );

			$new_items = array_filter( $new_items );

			// merge old with new.
			$exclusions = array_merge( $old_items, $new_items );

			update_option( 'yith-ywraq-exclusions-cat-list', implode( ',', $exclusions ) );
		}




		/**
		 * Delete category from exclusions list
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function delete_exclusion_cat() {

			$get = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $get['remove_nonce'] ) || ! wp_verify_nonce( $get['remove_nonce'], 'yith_ywraq_remove_exclusions_cat' ) || ! isset( $get['remove_cat_exclusion'] ) ) {
				return;
			}

			$exclusions = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-cat-list' ) ) );

			if ( ( $key = array_search( $get['remove_cat_exclusion'], $exclusions ) ) !== false ) {
				unset( $exclusions[ $key ] );
			}

			update_option( 'yith-ywraq-exclusions-cat-list', implode( ',', $exclusions ) );

			$args = array( 'remove_nonce', 'remove_cat_exclusion' );
			$url  = esc_url_raw( remove_query_arg( $args ) );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Ajax action search tags
		 *
		 * @since 2.2.0
		 * @author Francesco Licandro
		 */
		public function search_categories_ajax() {
			ob_start();

			$term = wc_clean( stripslashes( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( empty( $term ) ) {
				die();
			}

			$tax = array( 'product_cat' );

			// search by name.
			$args = array(
				'hide_empty' => false,
				'fields'     => 'id=>name',
				'name__like' => $term,
			);

			$categories = get_terms( $tax, $args );

			if ( is_numeric( $term ) ) {
				// search by id.
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
				// search by slug.
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

			// get excluded categories.
			$excluded = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-cat-list', '' ) ) );

			if ( $categories ) {
				foreach ( $excluded as $id ) {
					if ( array_key_exists( $id, $categories ) ) {
						unset( $categories[ $id ] );
					}
				}
			}

			wp_send_json( $categories );
		}

		/**
		 * Save tag exclusions
		 *
		 * @since 2.2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function save_exclusions_tag() {

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! isset( $posted['_nonce'] ) || ! wp_verify_nonce( $posted['_nonce'], 'yith_ywraq_add_exclusions_tag' ) || ! isset( $posted['add_tags'] ) ) {
				return;
			}

			// get older items.
			$old_items = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-tag-list' ) ) );
			// get new items.
			$new_items = $posted['add_tags'];

			! is_array( $new_items ) && $new_items = explode( ',', $posted['add_tags'] );

			$new_items = array_filter( $new_items );

			// merge old with new.
			$exclusions = array_merge( $old_items, $new_items );
			update_option( 'yith-ywraq-exclusions-tag-list', implode( ',', $exclusions ) );
		}


		/**
		 * Delete tag from exclusions list
		 *
		 * @since 2.2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function delete_exclusion_tag() {

			$get = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $get['remove_nonce'] ) || ! wp_verify_nonce( $get['remove_nonce'], 'yith_ywraq_remove_exclusions_tag' ) || ! isset( $get['remove_tag_exclusion'] ) ) {
				return;
			}

			$exclusions = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-tag-list' ) ) );

			if ( ( $key = array_search( $get['remove_tag_exclusion'], $exclusions, true ) ) !== false ) {
				unset( $exclusions[ $key ] );
			}

			update_option( 'yith-ywraq-exclusions-tag-list', implode( ',', $exclusions ) );

			$args = array( 'remove_nonce', 'remove_tag_exclusion' );
			$url  = esc_url_raw( remove_query_arg( $args ) );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Ajax action search tags
		 *
		 * @since 2.2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function search_tags_ajax() {
			ob_start();

			$term = wc_clean( stripslashes( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( empty( $term ) ) {
				die();
			}

			$tax = array( 'product_tag' );

			// search by name.
			$args = array(
				'hide_empty' => false,
				'fields'     => 'id=>name',
				'name__like' => $term,
			);

			$tags = get_terms( $tax, $args );

			if ( is_numeric( $term ) ) {
				// search by id.
				$args = array(
					'hide_empty' => false,
					'fields'     => 'id=>name',
					'include'    => array( $term ),
				);

				$found = get_terms( $tax, $args );

				foreach ( $found as $id => $name ) {
					if ( array_key_exists( $id, $tags ) ) {
						continue;
					}
					$tags[ $id ] = $name;
				}
			} else {
				// search by slug.
				$args = array(
					'hide_empty' => false,
					'fields'     => 'id=>name',
					'slug'       => $term,
				);

				$found = get_terms( $tax, $args );

				foreach ( $found as $id => $name ) {
					if ( array_key_exists( $id, $tags ) ) {
						continue;
					}
					$tags[ $id ] = $name;
				}
			}

			// get excluded categories.
			$excluded = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-tag-list', '' ) ) );

			if ( $tags ) {
				foreach ( $excluded as $id ) {
					if ( array_key_exists( $id, $tags ) ) {
						unset( $tags[ $id ] );
					}
				}
			}

			wp_send_json( $tags );
		}

	}
}

/**
 * Unique access to instance of YITH_YWRAQ_Exclusions_Handler class
 *
 * @return \YITH_YWRAQ_Exclusions_Handler
 * @since 2.0.0
 */
function YITH_YWRAQ_Exclusions_Handler() {
	return YITH_YWRAQ_Exclusions_Handler::get_instance();
}