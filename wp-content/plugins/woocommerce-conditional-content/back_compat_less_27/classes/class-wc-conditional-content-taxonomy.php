<?php

/**
 * Helper class to register and manage the conditional content post type, wccc
 */
class WC_Conditional_Content_Taxonomy {

	private static $instance;

	/**
	 * Registers a single instance of the WC_Conditional_Content_Taxonomy class 
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Conditional_Content_Taxonomy();
		}
	}

	/**
	 * Creates a new instnace of the WC_Conditional_Content_Taxonomy class
	 */
	public function __construct() {
		add_action( 'init', array(&$this, 'on_woocommerce_init'), 99 );
	}

	/**
	 * Registers the wccc post type after woocommerce_init.
	 */
	public function on_woocommerce_init() {
		$menu_name = _x( 'Content Blocks', 'Admin menu name', 'wc_conditional_content' );
		$show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true;
		register_post_type( "wccc", apply_filters( 'woocommerce_conditional_content_post_type', array(
		    'labels' => array(
			'name' => __( 'Content Blocks', 'wc_conditional_content' ),
			'singular_name' => __( 'Content Block', 'wc_conditional_content' ),
			'add_new' => __( 'Add Content Block', 'wc_conditional_content' ),
			'add_new_item' => __( 'Add New Content Block', 'wc_conditional_content' ),
			'edit' => __( 'Edit', 'wc_conditional_content' ),
			'edit_item' => __( 'Edit Content Block', 'wc_conditional_content' ),
			'new_item' => __( 'New Content Blocks', 'wc_conditional_content' ),
			'view' => __( 'View Content Block', 'wc_conditional_content' ),
			'view_item' => __( 'View Content Block', 'wc_conditional_content' ),
			'search_items' => __( 'Search Content Blocks', 'wc_conditional_content' ),
			'not_found' => __( 'No Content Blocks found', 'wc_conditional_content' ),
			'not_found_in_trash' => __( 'No Content Blocks found in trash', 'wc_conditional_content' ),
			'parent' => __( 'Parent Content Blocks', 'wc_conditional_content' ),
			'menu_name' => $menu_name
		    ),
		    'description' => __( 'This is where conditional content blocks and their assoicated rules are stored.', 'wc_conditional_content' ),
		    'public' => false,
		    'show_ui' => true,
		    'capability_type' => 'product',
		    'map_meta_cap' => true,
		    'publicly_queryable' => false,
		    'exclude_from_search' => true,
		    'show_in_menu' => $show_in_menu,
		    'hierarchical' => false,
		    'show_in_nav_menus' => false,
		    'rewrite' => false,
		    'query_var' => true,
		    'supports' => array('title', 'editor'),
		    'has_archive' => false,
		    'register_meta_box_cb' => array('WC_Conditional_Content_Admin_Controller', 'add_metaboxes')
				)
			)
		);
	}
}