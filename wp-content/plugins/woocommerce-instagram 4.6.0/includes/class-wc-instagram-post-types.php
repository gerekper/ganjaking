<?php
/**
 * Post Types.
 *
 * Register post types.
 *
 * @package WC_Instagram
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Class WC_Instagram_Post_Types.
 */
class WC_Instagram_Post_Types {

	/**
	 * Init.
	 *
	 * @since 4.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ) );
	}

	/**
	 * Register post types.
	 *
	 * @since 4.0.0
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'wc_instagram_catalog' ) ) {
			return;
		}

		$post_type = array(
			'label'           => _x( 'Product catalogs', 'setting title', 'woocommerce-instagram' ),
			'public'          => false,
			'hierarchical'    => false,
			'supports'        => false,
			'capability_type' => 'product',
			'rewrite'         => false,
		);

		/**
		 * Filters the arguments for the product catalog post type.
		 *
		 * @since 4.0.0
		 *
		 * @param array $post_type The post type arguments.
		 */
		$post_type = apply_filters( 'wc_instagram_catalog_post_type', $post_type );

		register_post_type( 'wc_instagram_catalog', $post_type );
	}

	/**
	 * Register data stores.
	 *
	 * @since 4.0.0
	 *
	 * @param array $stores Data stores.
	 * @return array
	 */
	public static function register_data_stores( $stores ) {
		$stores['instagram_product_catalog'] = 'WC_Instagram_Data_Store_Product_Catalog_CPT';

		return $stores;
	}

	/**
	 * Unregister post types.
	 *
	 * @since 4.1.1
	 */
	public static function unregister_post_types() {
		if ( ! is_blog_installed() || ! post_type_exists( 'wc_instagram_catalog' ) ) {
			return;
		}

		unregister_post_type( 'wc_instagram_catalog' );
	}
}

WC_Instagram_Post_Types::init();
