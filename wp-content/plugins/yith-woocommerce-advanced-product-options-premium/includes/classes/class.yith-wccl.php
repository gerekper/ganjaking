<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WAPO' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCL' ) ) {
	/**
	 * YITH WooCommerce Color and Label Variations Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCCL {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCCL
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WAPO_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCCL
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return mixed YITH_WCCL_Admin | YITH_WCCL_Frontend
		 */
		public function __construct() {

			// Load Plugin Framework
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// Class admin
			if ( $this->is_admin() ) {
				// require classes
				require_once( 'class.yith-wccl-admin.php' );
				// Admin Class
				YITH_WCCL_Admin();
			} else {
				// require classes
				require_once( 'class.yith-wccl-frontend.php' );
				// Frontend Class
				YITH_WCCL_Frontend();
			}


			// add new attribute types
			add_filter( 'product_attributes_type_selector', array( $this, 'attribute_types' ), 10, 1 );

			// delete transient on update stock
			add_action( 'woocommerce_variation_set_stock', array( $this, 'delete_transient' ), 10, 1 );
			add_action( 'woocommerce_product_set_stock', array( $this, 'delete_transient' ), 10, 1 );
			// delete transient on save product
			add_action( 'woocommerce_before_product_object_save', array( $this, 'delete_transient' ), 10, 2 );
			add_action( 'woocommerce_before_product_variation_object_save', array( $this, 'delete_transient' ), 10, 2 );

			// Compatibility with WP ALL IMPORT (import gallery for variations)
			add_action( 'pmxi_gallery_image', array( $this, 'wpai_import_gallery_images_variation' ), 10, 4 );

			// Process variation gallery for WC import/export products
			add_filter( 'woocommerce_product_export_meta_value', array( $this, 'product_export_meta_value' ), 10, 4 );
			add_filter( 'woocommerce_product_import_process_item_data', array( $this, 'product_import_gallery' ), 10, 2 );

			// init variations terms
			add_action( 'init', array( $this, 'init_variations_term' ) );
			add_action( 'woocommerce_product_object_updated_props', array( $this, 'update_variations_terms' ), 10, 1 );
			add_action( 'woocommerce_before_delete_product_variation', array( $this, 'delete_variations_terms' ), 10, 1 );
		}

		/**
		 * Check if context is admin
		 *
		 * @since  1.2.2
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_admin() {
			$actions     = apply_filters( 'yith_wccl_is_admin_actions_array', array(
				'prdctfltr_respond_550',
				'flatsome_quickview',
			) );
			$is_frontend = isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend';
			$is_ajax     = defined( 'DOING_AJAX' ) && DOING_AJAX && ( $is_frontend || ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions ) ) );

			return apply_filters( 'yith_wccl_load_admin_class', ( is_admin() && ! $is_ajax ) );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function plugin_fw_loader() {

			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Delete plugin transient on product save
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param \WC_Product $product
		 * @param array       $data
		 */
		public function delete_transient( $product, $data = array() ) {
			// get the ID
			$id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			delete_transient( 'yith_wccl_available_variations_' . $id );
			delete_transient( 'yith_wccl_variations_parent_product_id' );
			delete_transient( 'yith_wccl_loop_excluded_variations' );
		}

		/**
		 * Add new attribute types to standard WooCommerce
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $default_type
		 * @return array
		 */
		public function attribute_types( $default_type ) {
			$custom = ywccl_get_custom_tax_types();
			return is_array( $custom ) ? array_merge( $default_type, $custom ) : $default_type;
		}


		/**
		 * Import gallery images for single variation with WP ALL IMPORT
		 * @param        $post_id
		 * @param        $att_id
		 * @param        $filepath
		 * @param string $is_keep_existing_images
		 */
		public function wpai_import_gallery_images_variation( $post_id, $att_id, $filepath, $is_keep_existing_images = '' ) {
			$key = '_yith_wccl_gallery';  // Edit this: Set meta key for gallery array here

			// Get the ID of the featured image
			$featured_image = get_post_meta( $post_id, '_thumbnail_id', true );

			$gallery = get_post_meta( $post_id, $key, TRUE );
			if ( empty( $gallery ) ) {
				$gallery = array();
			}

			if ( ! in_array( $att_id, $gallery ) && ( ! empty( $featured_image ) && $featured_image != $att_id ) ) {
				$gallery[] = $att_id;
				update_post_meta( $post_id, $key, $gallery );
			}
		}

		/**
		 * Filter gallery value on product export
		 *
		 * @since  1.8.15
		 * @author Francesco Licandro
		 * @param mixed      $value
		 * @param string     $meta
		 * @param WC_Product $product
		 * @param            $row
		 * @return mixed
		 */
		public function product_export_meta_value( $value, $meta, $product, $row ) {
			if ( $meta == '_yith_wccl_gallery' || empty( $value ) ) {
				return $value;
			}

			$gallery = $product->get_meta( '_yith_wccl_gallery', true ); // make sure gallery is correct
			if ( $gallery ) {
				$value = [];
				foreach ( $gallery as $attachment_id ) {
					$image = wp_get_attachment_url( $attachment_id );
					$image && $value[] = $image;
				}
				$value = implode( ',', $value );
			}

			return $value;
		}

		/**
		 * Import variation gallery
		 *
		 * @author Francesco Licandro
		 * @param array $data
		 * @return array
		 */
		public function product_import_gallery( $data ) {
			if ( isset( $data['meta_data'] ) ) {
				foreach ( $data['meta_data'] as $index => $meta ) {
					if ( $meta['key'] == '_yith_wccl_gallery' && ! empty( $meta['value'] ) ) {
						$gallery   = explode( ',', $meta['value'] );
						$new_value = [];

						foreach ( $gallery as $image ) {
							$id = $this->get_attachment_id_from_url( $image );
							$id && $new_value[] = $id;
						}

						$data['meta_data'][ $index ]['value'] = $new_value;
					}
				}
			}

			return $data;
		}

		/**
		 * Get an attachment id from the url
		 *
		 * @author Francesco Licandro
		 * @param string $url
		 * @return int|mixed|WP_Post
		 */
		public function get_attachment_id_from_url( $url ) {
			if ( empty( $url ) ) {
				return 0;
			}

			$id  = 0;
			$ids = get_posts( array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => array( // @codingStandardsIgnoreLine.
					array(
						'value' => $url,
						'key'   => '_wc_attachment_source',
					),
				),
			) ); // @codingStandardsIgnoreLine.

			if ( $ids ) {
				$id = current( $ids );
			}

			// Upload if attachment does not exists.
			if ( ! $id && stristr( $url, '://' ) ) {
				$upload = wc_rest_upload_image_from_url( $url );

				if ( is_wp_error( $upload ) ) {
					return 0;
				}

				$id = wc_rest_set_uploaded_image_as_attachment( $upload, 0 );
				if ( ! wp_attachment_is_image( $id ) ) {
					return 0;
				}
				// Save attachment source for future reference.
				update_post_meta( $id, '_wc_attachment_source', $url );
			}

			return $id;
		}

		/**
		 * Init variations terms
		 *
		 * @since  1.9.3
		 * @author Francesco Licandro
		 * @return void
		 */
		public function init_variations_term() {

			$variation_enabled = get_option( 'yith-wccl-show-single-variations-loop', 'no' );
			$initialized       = get_option( 'yith_wccl_init_variation_terms_initialized', 'no' );
			$running           = get_option( 'yith_wccl_init_variation_terms_running', 'no' );
			if ( $variation_enabled != 'yes' || $initialized == 'yes' || $running == 'yes' ) {
				return;
			}

			// START
			update_option( 'yith_wccl_init_variation_terms_running', 'yes' );
			$offset = get_option( 'yith_wccl_init_variation_terms_offset', 0 );
			$offset = absint( $offset );

			$products = wc_get_products( array(
				'status' => 'publish',
				'type'   => 'variable',
				'limit'  => 1,
				'offset' => $offset,
			) );

			if ( empty( $products ) ) {
				delete_option( 'yith_wccl_init_variation_terms_running' );
				delete_option( 'yith_wccl_init_variation_terms_offset' );
				update_option( 'yith_wccl_init_variation_terms_initialized', 'yes' );
			}

			foreach ( $products as $product ) {
				$product_id     = $product->get_id();
				$variations_ids = $product->get_children();
				if ( empty( $variations_ids ) ) {
					continue;
				}

				// collect terms
				$product_terms = array();
				foreach ( array( 'product_cat', 'product_tag' ) as $taxonomy ) {
					$terms = wp_get_post_terms( $product_id, $taxonomy, array( 'fields' => 'ids' ) );
					if ( ! is_wp_error( $terms ) ) {
						$product_terms[ $taxonomy ] = $terms;
					}
				}

				foreach ( $variations_ids as $variation_id ) {
					// add terms
					foreach ( $product_terms as $taxonomy => $terms ) {
						wp_set_post_terms( $variation_id, $terms, $taxonomy );
					}
					// add attributes
					$attributes = wc_get_product_variation_attributes( $variation_id );

					if ( ! empty( $attributes ) ) {
						foreach ( $attributes as $taxonomy => $value ) {
							$taxonomy = str_replace( 'attribute_', '', $taxonomy );
							wp_set_object_terms( $variation_id, $value, $taxonomy );
						}
					}
				}
			}

			update_option( 'yith_wccl_init_variation_terms_running', 'no' );
			update_option( 'yith_wccl_init_variation_terms_offset', 1 + $offset );
		}

		/**
		 * Update variation terms on product update
		 *
		 * @since  1.9.3
		 * @author Francesco Licandro
		 * @param WC_Product $product
		 * @return void
		 */
		public function update_variations_terms( $product ) {

			$changes = $product->get_changes();

			if ( $product->is_type( 'variable' ) ) {
				$variation_ids = $product->get_children();
				$categories    = $product->get_category_ids( 'edit' );
				$tags          = $product->get_tag_ids( 'edit' );

				if ( ! empty( $variation_ids ) ) {
					foreach ( $variation_ids as $variation_id ) {
						wp_set_post_terms( $variation_id, $categories, 'product_cat', false );
						wp_set_post_terms( $variation_id, $tags, 'product_tag', false );
						error_log( class_exists( 'YITH_WCBR' ) );
						if ( class_exists( 'YITH_WCBR' ) ) {
							$product_brands = wc_get_product_term_ids( $product->get_id(), YITH_WCBR::$brands_taxonomy );
							wp_set_post_terms( $variation_id, $product_brands, YITH_WCBR::$brands_taxonomy, false );
						}


						if ( array_key_exists( 'attributes', $changes ) ) {
							// reset attribute relationship
							wp_delete_object_term_relationships( $variation_id, $this->get_attribute_taxonomies() );

							$variation  = wc_get_product( $variation_id );
							$attributes = $variation->get_attributes();
							foreach ( $attributes as $taxonomy => $value ) {
								wp_set_object_terms( $variation_id, $value, $taxonomy );
							}
						}
					}
				}
			}

			if ( $product->is_type( 'variation' ) && array_key_exists( 'attributes', $changes ) ) {
				// as we don't know attributes changed, remove all attribute taxonomies from variation
				wp_delete_object_term_relationships( $product->get_id(), $this->get_attribute_taxonomies() );

				$attributes = $product->get_attributes();
				foreach ( $attributes as $taxonomy => $value ) {
					wp_set_object_terms( $product->get_id(), $value, $taxonomy );
				}
			}
		}

		/**
		 * Delete variation taxonomies on delete variation
		 *
		 * @since  1.9.3
		 * @author Francesco Licandro
		 * @param integer $product_id
		 * @return void
		 */
		public function delete_variations_terms( $product_id ) {
			$taxonomies = array_merge( array( 'product_cat', 'product_tag' ), $this->get_attribute_taxonomies() );
			wp_delete_object_term_relationships( $product_id, $taxonomies );
		}

		/**
		 * Get an array of attribute taxonomies
		 *
		 * @since  1.9.3
		 * @author Francesco Licandro
		 * @return array
		 */
		protected function get_attribute_taxonomies() {

			$taxonomies = wp_cache_get( 'yith_wccl_attribute_taxonomies', 'yith_wccl' );
			if ( $taxonomies === false ) {
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				$taxonomies           = array_map( function ( $tax ) {
					return wc_attribute_taxonomy_name( $tax->attribute_name );
				}, $attribute_taxonomies );

				wp_cache_set( 'yith_wccl_attribute_taxonomies', $taxonomies, 'yith_wccl' );
			}

			return $taxonomies;
		}
	}
}

/**
 * Unique access to instance of YITH_WCCL class
 *
 * @since 1.0.0
 * @return \YITH_WCCL
 */
function YITH_WCCL() {
	return YITH_WCCL::get_instance();
}