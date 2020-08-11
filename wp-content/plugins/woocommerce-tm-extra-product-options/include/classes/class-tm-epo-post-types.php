<?php
/**
 * Extra Product Options Post Types
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_POST_TYPES {

	/**
	 * Register local post type
	 * (This is used in Normal mode)
	 *
	 * @since 4.8
	 */
	public static function register_local_post_type() {

		register_post_type( THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			array(
				'labels'              => array(
					'name' => esc_html_x( 'TM Extra Product Options', 'post type general name', 'woocommerce-tm-extra-product-options' ),
				),
				'publicly_queryable'  => FALSE,
				'exclude_from_search' => TRUE,
				'rewrite'             => FALSE,
				'show_in_nav_menus'   => FALSE,
				'public'              => FALSE,
				'hierarchical'        => FALSE,
				'supports'            => FALSE,
				'_edit_link'          => 'post.php?post=%d' //WordPress 4.4 fix
			)
		);

	}

	/**
	 * Register global post type
	 * (This is used in Global builder forms mode)
	 *
	 * @since 4.8
	 */
	public static function register_global_post_type() {

		register_post_type( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
			array(
				'labels'              => array(
					'name'               => esc_html__( 'TM Global Forms', 'woocommerce-tm-extra-product-options' ),
					'singular_name'      => esc_html__( 'TM Global Form', 'woocommerce-tm-extra-product-options' ),
					'menu_name'          => esc_html_x( 'TM Global Product Options', 'post type general name', 'woocommerce-tm-extra-product-options' ),
					'add_new'            => esc_html__( 'Add Global Form', 'woocommerce-tm-extra-product-options' ),
					'add_new_item'       => esc_html__( 'Add New Global Form', 'woocommerce-tm-extra-product-options' ),
					'edit'               => esc_html__( 'Edit', 'woocommerce-tm-extra-product-options' ),
					'edit_item'          => esc_html__( 'Edit Global Form', 'woocommerce-tm-extra-product-options' ),
					'new_item'           => esc_html__( 'New Global Form', 'woocommerce-tm-extra-product-options' ),
					'view'               => esc_html__( 'View Global Form', 'woocommerce-tm-extra-product-options' ),
					'view_item'          => esc_html__( 'View Global Form', 'woocommerce-tm-extra-product-options' ),
					'search_items'       => esc_html__( 'Search Global Form', 'woocommerce-tm-extra-product-options' ),
					'not_found'          => esc_html__( 'No Global Form found', 'woocommerce-tm-extra-product-options' ),
					'not_found_in_trash' => esc_html__( 'No Global Form found in trash', 'woocommerce-tm-extra-product-options' ),
					'parent'             => esc_html__( 'Parent Global Form', 'woocommerce-tm-extra-product-options' ),
				),
				'description'         => esc_attr__( 'This is where you can add new global options to your store.', 'woocommerce' ),
				'public'              => FALSE,
				'show_ui'             => FALSE,//"edit.php?post_type=product",
				'capability_type'     => 'product',
				'map_meta_cap'        => TRUE,
				'publicly_queryable'  => FALSE,
				'exclude_from_search' => TRUE,
				'hierarchical'        => FALSE,
				'rewrite'             => FALSE,
				'query_var'           => FALSE,
				'supports'            => array( 'title', 'excerpt' ),
				'has_archive'         => FALSE,
				'show_in_nav_menus'   => FALSE,
				'_edit_link'          => 'post.php?post=%d' //WordPress 4.4 fix
			)

		);

		register_taxonomy_for_object_type( 'product_cat', THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );

	}

	/**
	 * Register post types
	 *
	 * @since 4.8
	 */
	public static function register() {

		self::register_local_post_type();
		self::register_global_post_type();

	}

}
