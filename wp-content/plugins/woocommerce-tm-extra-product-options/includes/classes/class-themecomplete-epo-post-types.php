<?php
/**
 * Extra Product Options Post Types
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Post Types
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_POST_TYPES {

	/**
	 * The global post type
	 *
	 * @var string
	 */
	public static $global_type;

	/**
	 * The template post type
	 *
	 * @var string
	 */
	public static $template_type;

	/**
	 * The lookup table post type
	 *
	 * @var string
	 */
	public static $lookuptable_type;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_POST_TYPES|null
	 * @since 6.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 6.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register local post type
	 * (This is used in Normal mode)
	 *
	 * @since 4.8
	 */
	public static function register_local_post_type() {

		register_post_type(
			THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			[
				'labels'              => [
					'name' => esc_html_x( 'TM Extra Product Options', 'post type general name', 'woocommerce-tm-extra-product-options' ),
				],
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'rewrite'             => false,
				'show_in_nav_menus'   => false,
				'public'              => false,
				'hierarchical'        => false,
				'supports'            => false,
				'_edit_link'          => 'post.php?post=%d', // WordPress 4.4 fix.
			]
		);

	}

	/**
	 * Register global post type
	 * (This is used in Global builder forms mode)
	 *
	 * @since 4.8
	 */
	public static function register_global_post_type() {

		register_post_type(
			THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
			[
				'labels'              => [
					'name'               => esc_html__( 'Global Forms', 'woocommerce-tm-extra-product-options' ),
					'singular_name'      => esc_html__( 'Global Form', 'woocommerce-tm-extra-product-options' ),
					'menu_name'          => esc_html_x( 'Global Forms', 'post type general name', 'woocommerce-tm-extra-product-options' ),
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
				],
				'description'         => esc_attr__( 'This is where you can add new global options to your store.', 'woocommerce' ),
				'public'              => false,
				'show_ui'             => false,
				'capability_type'     => 'product',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => [ 'title', 'excerpt' ],
				'has_archive'         => false,
				'show_in_nav_menus'   => false,
				'_edit_link'          => 'post.php?post=%d', // WordPress 4.4 fix.
			]
		);

		$custom_product_taxonomies = get_object_taxonomies( 'product' );
		if ( is_array( $custom_product_taxonomies ) && count( $custom_product_taxonomies ) > 0 ) {
			foreach ( $custom_product_taxonomies as $tax ) {
				if ( 'translation_priority' === $tax ) {
					continue;
				}
				register_taxonomy_for_object_type( $tax, THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );
			}
		}

	}

	/**
	 * Register template post type
	 * (This is for the Option Templates)
	 *
	 * @since 6.0
	 */
	public static function register_template_post_type() {

		register_post_type(
			THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE,
			[
				'labels'               => [
					'name'               => esc_html__( 'Option Templates', 'woocommerce-tm-extra-product-options' ),
					'singular_name'      => esc_html__( 'Option Template', 'woocommerce-tm-extra-product-options' ),
					'menu_name'          => esc_html_x( 'Option Templates', 'post type general name', 'woocommerce-tm-extra-product-options' ),
					'add_new'            => esc_html__( 'Add Template', 'woocommerce-tm-extra-product-options' ),
					'add_new_item'       => esc_html__( 'Add New Template', 'woocommerce-tm-extra-product-options' ),
					'edit'               => esc_html__( 'Edit', 'woocommerce-tm-extra-product-options' ),
					'edit_item'          => esc_html__( 'Edit Template', 'woocommerce-tm-extra-product-options' ),
					'new_item'           => esc_html__( 'New Template', 'woocommerce-tm-extra-product-options' ),
					'view'               => esc_html__( 'View Template', 'woocommerce-tm-extra-product-options' ),
					'view_item'          => esc_html__( 'View Template', 'woocommerce-tm-extra-product-options' ),
					'search_items'       => esc_html__( 'Search Template', 'woocommerce-tm-extra-product-options' ),
					'not_found'          => esc_html__( 'No Template found', 'woocommerce-tm-extra-product-options' ),
					'not_found_in_trash' => esc_html__( 'No Template found in trash', 'woocommerce-tm-extra-product-options' ),
					'parent'             => esc_html__( 'Parent Template', 'woocommerce-tm-extra-product-options' ),
				],
				'description'          => esc_attr__( 'This is where you can add new global template options.', 'woocommerce' ),
				'public'               => false,
				'show_ui'              => true,
				'show_in_menu'         => 'tcepo',
				'capability_type'      => 'product',
				'map_meta_cap'         => true,
				'publicly_queryable'   => false,
				'exclude_from_search'  => true,
				'hierarchical'         => false,
				'rewrite'              => false,
				'query_var'            => false,
				'supports'             => [ 'title' ],
				'has_archive'          => false,
				'show_in_nav_menus'    => false,
				'register_meta_box_cb' => [ THEMECOMPLETE_EPO_ADMIN_GLOBAL(), 'preload_template_settings' ],
				'_edit_link'           => 'post.php?post=%d', // WordPress 4.4 fix.
			]
		);

	}

	/**
	 * Register lookup table post type
	 *
	 * @since 6.1
	 */
	public static function register_lookuptable_post_type() {

		register_post_type(
			THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE,
			[
				'labels'               => [
					'name'               => esc_html__( 'Lookup tables', 'woocommerce-tm-extra-product-options' ),
					'singular_name'      => esc_html__( 'Lookup table', 'woocommerce-tm-extra-product-options' ),
					'menu_name'          => esc_html_x( 'Lookup tables', 'post type general name', 'woocommerce-tm-extra-product-options' ),
					'add_new'            => esc_html__( 'Add Lookup table', 'woocommerce-tm-extra-product-options' ),
					'add_new_item'       => esc_html__( 'Add New Lookup table', 'woocommerce-tm-extra-product-options' ),
					'edit'               => esc_html__( 'Edit', 'woocommerce-tm-extra-product-options' ),
					'edit_item'          => esc_html__( 'Edit Lookup table', 'woocommerce-tm-extra-product-options' ),
					'new_item'           => esc_html__( 'New Lookup table', 'woocommerce-tm-extra-product-options' ),
					'view'               => esc_html__( 'View Lookup table', 'woocommerce-tm-extra-product-options' ),
					'view_item'          => esc_html__( 'View Lookup table', 'woocommerce-tm-extra-product-options' ),
					'search_items'       => esc_html__( 'Search Lookup table', 'woocommerce-tm-extra-product-options' ),
					'not_found'          => esc_html__( 'No Lookup table found', 'woocommerce-tm-extra-product-options' ),
					'not_found_in_trash' => esc_html__( 'No Lookup table found in trash', 'woocommerce-tm-extra-product-options' ),
					'parent'             => esc_html__( 'Parent Lookup table', 'woocommerce-tm-extra-product-options' ),
				],
				'description'          => esc_attr__( 'This is where you can add new lookup tables.', 'woocommerce' ),
				'public'               => false,
				'show_ui'              => true,
				'show_in_menu'         => 'tcepo',
				'capability_type'      => 'product',
				'map_meta_cap'         => true,
				'publicly_queryable'   => false,
				'exclude_from_search'  => true,
				'hierarchical'         => false,
				'rewrite'              => false,
				'query_var'            => false,
				'supports'             => [ 'title' ],
				'has_archive'          => false,
				'show_in_nav_menus'    => false,
				'register_meta_box_cb' => [ THEMECOMPLETE_EPO_ADMIN_LOOKUPTABLE(), 'preload_lookuptable_settings' ],
				'_edit_link'           => 'post.php?post=%d', // WordPress 4.4 fix.
			]
		);

	}

	/**
	 * Register post types
	 *
	 * @since 4.8
	 */
	public static function register() {

		self::register_local_post_type();
		self::register_global_post_type();
		self::register_template_post_type();
		self::register_lookuptable_post_type();

		self::$global_type      = get_post_type_object( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE );
		self::$template_type    = get_post_type_object( THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE );
		self::$lookuptable_type = get_post_type_object( THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE );

	}

}
