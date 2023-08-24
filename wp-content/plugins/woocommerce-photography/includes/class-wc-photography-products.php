<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Products.
 *
 * @package  WC_Photography/Products
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Products {

	/**
	 * Initialize the products actions.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_products' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_image_sizes' ) );
		add_action( 'template_redirect', array( $this, 'add_to_order' ), 100 );
		add_action( 'template_redirect', array( $this, 'restrict_access' ) );

		add_filter( 'product_type_selector', array( $this, 'product_type' ) );
		add_filter( 'template_include', array( $this, 'collections_template' ), 20 );
		add_filter( 'wc_get_template_part', array( $this, 'photography_templates' ), 10, 3 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_before_add_to_cart' ), 10, 2 );
	}

	/**
	 * Registers product classes.
	 *
	 * @since 1.2.0
	 */
	public function register_products() {
		include_once 'class-wc-product-photography.php';
	}

	/**
	 * Add Photography to WooCommerce product types.
	 *
	 * @param  array $types
	 *
	 * @return array
	 */
	public function product_type( $types ) {
		$types['photography'] = __( 'Photograph', 'woocommerce-photography' );

		return $types;
	}

	/**
	 * Custom pre get posts.
	 *
	 * @param  WP_Query $query
	 *
	 * @return void
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}

		// Stop filtering if this is not the right page.
		if ( ! $this->is_catalog_page( $query ) || is_search() ) {
			remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			return;
		}

		$settings = get_option( 'woocommerce_photography', array() );

		if ( ! empty( $settings['collections_archive_visibility'] ) ) {
			$restricted_collections = $this->get_restricted_collection_ids();

			if ( ! empty( $restricted_collections ) ) {
				$tax_query = array(
					'taxonomy' => 'images_collections',
					'field'    => 'id',
					'terms'    => $restricted_collections,
					'operator' => 'NOT IN',
				);
				$query->set( 'tax_query', array( $tax_query ) );
			}
		} else {

			$tax_query = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'photography' ),
				'operator' => 'NOT IN',
			);
			$query->set( 'tax_query', array( $tax_query ) );
		}

		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Get a list of restricted collection IDs.
	 *
	 * @since 1.0.24
	 *
	 * @return array
	 */
	public function get_restricted_collection_ids() {

		$ids = get_transient( 'woocommerce_photography_restricted_collections' );

		if ( false !== $ids ) {
			return $ids;
		}

		$args = array(
			'taxonomy'   => 'images_collections',
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => 'visibility',
					'value'   => 'public',
					'compare' => 'NOT IN',
				),
			),
		);

		$ids = get_terms( $args );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			$ids = array(); // Return an empty set when there is an error.
		}

		set_transient( 'woocommerce_photography_restricted_collections', $ids, DAY_IN_SECONDS );
		return $ids;
	}

	/**
	 * Check if is the catalog page.
	 * This method avoid "Trying to get property of non-object in" messages.
	 *
	 * @return bool
	 */
	protected function is_catalog_page( $query ) {
		$is_archive   = is_post_type_archive( 'product' );
		$is_shop_page = is_page() && isset( $query->query_vars['page_id'] ) && wc_get_page_id( 'shop' ) == $query->query_vars['page_id'];
		$is_cat_page  = ! empty( $query->query_vars['product_cat'] );

		return ( $is_archive || $is_shop_page || $is_cat_page );
	}

	/**
	 * Restrict photography access.
	 *
	 * @return void
	 */
	public function restrict_access() {
		// Check for products.
		if ( is_product() ) {
			global $post;

			// Current user ID.
			$user_id = get_current_user_id();

			// Check if is admin or is not a post.
			if ( current_user_can( 'manage_photography' ) || ! isset( $post->ID ) ) {
				return;
			}

			$product_type   = get_the_terms( $post->ID, 'product_type' );
			$is_photography = false;

			// Check if is a photography.
			if ( is_wp_error( $product_type ) || ! $product_type ) {
				return;
			}

			foreach ( $product_type as $key => $value ) {
				if ( 'photography' == $value->slug ) {
					$is_photography = true;
					break;
				}
			}

			if ( ! $is_photography ) {
				return;
			}

			// Check if the current user can see the photography.
			$images_collections = get_the_terms( $post->ID, 'images_collections' );
			if ( $images_collections ) {
				$collections = array();

				foreach ( $images_collections as $key => $collection ) {

					// Check the collection visibility.
					if ( wc_photography_is_collection_public( $collection->term_id ) ) {
						return;
					}

					$collections[] = $collection->term_id;
				}

				$user_collections = get_user_meta( $user_id, '_wc_photography_collections', true );
				$user_collections = is_array( $user_collections ) ? $user_collections : array();

				if ( 0 < count( array_intersect( $collections, $user_collections ) ) ) {
					return;
				}
			}

			wp_redirect( get_the_permalink( wc_get_page_id( 'myaccount' ) ) );
			exit();
		} // End if().

		// Check for taxonomy page.
		if ( is_tax( 'images_collections' ) ) {

			// Check if is admin.
			if ( current_user_can( 'manage_photography' ) ) {
				return;
			}

			// Get the collection data.
			$collection = get_queried_object();

			// Check the collection visibility.
			if ( wc_photography_is_collection_public( $collection->term_id ) ) {
				return true;
			}

			// Check if the current user can see the photography.
			$user_id          = get_current_user_id();
			$user_collections = get_user_meta( $user_id, '_wc_photography_collections', true );
			$user_collections = is_array( $user_collections ) ? $user_collections : array();

			if ( in_array( $collection->term_id, $user_collections ) ) {
				return;
			}

			wp_redirect( get_the_permalink( wc_get_page_id( 'myaccount' ) ) );
			exit();
		}
	}

	/**
	 * Set the collections template.
	 *
	 * @param  string $template
	 *
	 * @return string
	 */
	public function collections_template( $template ) {
		if ( is_tax( 'images_collections' ) ) {
			$file = 'taxonomy-images_collections.php';
			$find = array();
			$term = get_queried_object();

			$find[] = 'taxonomy-images_collections-' . $term->slug . '.php';
			$find[] = WC()->template_path() . 'taxonomy-images_collections-' . $term->slug . '.php';
			$find[] = 'taxonomy-images_collections.php';
			$find[] = WC()->template_path() . 'taxonomy-images_collections.php';
			$find[] = $file;
			$find[] = WC()->template_path() . $file;

			$template       = locate_template( $find );
			$status_options = get_option( 'woocommerce_status_options', array() );
			if ( ! $template || ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) ) {
				$template = WC_Photography::get_templates_path() . $file;
			}
		}

		return $template;
	}

	/**
	 * Set the photography templates.
	 *
	 * @param  string $template
	 * @param  string $slug
	 * @param  string $name
	 *
	 * @return string
	 */
	public function photography_templates( $template, $slug, $name ) {
		if ( 'photography' === $name && 'content' === $slug ) {
			$file = $slug . '-' . $name . '.php';

			if ( basename( $template ) !== $file ) {
				$template = WC_Photography::get_templates_path() . $file;
			}
		}

		return $template;
	}

	/**
	 * Setup image sizes.
	 *
	 * @return void
	 */
	public function setup_image_sizes() {
		$settings = get_option( 'woocommerce_photography' );

		$photography_thumbnail = apply_filters(
			'wc_photography_get_image_size_thumbnail',
			array(
				'width'  => $settings['thumbnail_image_size']['width'],
				'height' => $settings['thumbnail_image_size']['height'],
				'crop'   => $settings['thumbnail_image_size']['crop'],
			)
		);

		$photography_lightbox = apply_filters(
			'wc_photography_get_image_size_lightbox',
			array(
				'width'  => $settings['lightbox_image_size']['width'],
				'height' => $settings['lightbox_image_size']['height'],
				'crop'   => $settings['lightbox_image_size']['crop'],
			)
		);

		add_image_size( 'photography_thumbnail', $photography_thumbnail['width'], $photography_thumbnail['height'], $photography_thumbnail['crop'] );
		add_image_size( 'photography_lightbox', $photography_lightbox['width'], $photography_lightbox['height'], $photography_lightbox['crop'] );
	}

	/**
	 * Add photographs to order/cart.
	 *
	 * @return void
	 */
	public function add_to_order() {
		if ( is_tax( 'images_collections' ) ) {

			// Stop with has no quantity.
			if ( ! isset( $_POST['quantity'] ) ) {
				return;
			}

			if ( empty( $_POST['quantity'] ) || ! is_array( $_POST['quantity'] ) ) {
				return;
			}

			$items             = array_map( 'absint', $_POST['quantity'] );
			$was_added_to_cart = false;
			$quantity_set      = false;
			$added_to_cart     = array();

			foreach ( $items as $product_id => $quantity ) {
				if ( 0 >= $quantity ) {
					continue;
				}

				$quantity_set = true;

				// Add to cart validation.
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

				if ( $passed_validation ) {
					// Add the product to the cart.
					if ( WC()->cart->add_to_cart( $product_id, $quantity ) ) {
						$was_added_to_cart            = true;
						$added_to_cart[ $product_id ] = $quantity;
					}
				}
			}

			if ( $was_added_to_cart ) {
				wc_add_to_cart_message( $added_to_cart );
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your order&hellip;', 'woocommerce-photography' ), 'error' );

				return;
			}

			// If we added the product to the cart we can now optionally do a redirect.
			if ( $was_added_to_cart && 0 == wc_notice_count( 'error' ) ) {

				$url = apply_filters( 'woocommerce_add_to_cart_redirect', '', null );

				// If has custom URL redirect there.
				if ( $url ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( 'yes' == get_option( 'woocommerce_cart_redirect_after_add' ) ) { // Redirect to cart option.
					wp_safe_redirect( wc_get_cart_url() );
					exit;
				}
			}
		} // End if().
	}

	/**
	 * Validate the product before add to cart.
	 *
	 * @param  bool $valid
	 * @param  int  $product_id
	 *
	 * @return bool
	 */
	public function validate_before_add_to_cart( $valid, $product_id ) {
		$product_type = get_the_terms( $product_id, 'product_type' );
		$product_type = is_array( $product_type ) ? current( $product_type ) : array();

		if ( 'photography' == $product_type->name ) {

			// Check if is admin.
			if ( current_user_can( 'manage_photography' ) ) {
				return $valid;
			}

			$images_collections = get_the_terms( $product_id, 'images_collections' );

			if ( $images_collections ) {
				$collections = array();

				foreach ( $images_collections as $collection ) {
					$collections[] = $collection->term_id;

					// Check the collection visibility.
					if ( wc_photography_is_collection_public( $collection->term_id ) ) {
						return true;
					}
				}

				// Check for user permissions.
				$user_id          = get_current_user_id();
				$user_collections = get_user_meta( $user_id, '_wc_photography_collections', true );
				$user_collections = is_array( $user_collections ) ? $user_collections : array();

				if ( 0 < count( array_intersect( $collections, $user_collections ) ) ) {
					return true;
				}
			}

			wc_add_notice( __( 'You don\'t have permission to purchase this photo!', 'woocommerce-photography' ), 'error' );
			return false;
		}

		return $valid;
	}
}

new WC_Photography_Products();
