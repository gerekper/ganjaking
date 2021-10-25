<?php

/**
 * All Custom Post Type
 * Author Appside
 * @since 2.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); //exit if access directly
}

if ( ! class_exists( 'Appside_Custom_Post_Type' ) ) {
	class Appside_Custom_Post_Type {

		//$instance variable
		private static $instance;

		public function __construct() {
			//register post type
			add_action( 'init', array( $this, 'register_custom_post_type' ) );
			//set post type attribute to header/footer/megamenu
			add_action('add_meta_boxes',array($this,'add_meta_boxes_value'),10);
		}

		/**
		 * get Instance
		 * @since  2.0.0
		 * */
		public static function getInstance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Register Custom Post Type
		 * @since  2.0.0
		 * */
		public function register_custom_post_type() {
			if (!defined('ELEMENTOR_VERSION')){
				return;
			}
			$all_post_type = array(
				array(
					'post_type' => 'apside-mega-menu',
					'args'      => array(
						'label'              => esc_html__( 'Mega Menu', 'appside-master' ),
						'description'        => esc_html__( 'Mega Menu', 'appside-master' ),
						'labels'             => array(
							'name'               => esc_html_x( 'Mega Menu', 'Post Type General Name', 'appside-master' ),
							'singular_name'      => esc_html_x( 'Mega Menu', 'Post Type Singular Name', 'appside-master' ),
							'menu_name'          => esc_html__( 'Mega Menu', 'appside-master' ),
							'all_items'          => esc_html__( 'Mega Menus', 'appside-master' ),
							'view_item'          => esc_html__( 'View Mega Menu', 'appside-master' ),
							'add_new_item'       => esc_html__( 'Add New Mega Menu', 'appside-master' ),
							'add_new'            => esc_html__( 'Add New Mega Menu', 'appside-master' ),
							'edit_item'          => esc_html__( 'Edit Mega Menu', 'appside-master' ),
							'update_item'        => esc_html__( 'Update Mega Menu', 'appside-master' ),
							'search_items'       => esc_html__( 'Search Mega Menu', 'appside-master' ),
							'not_found'          => esc_html__( 'Not Found', 'appside-master' ),
							'not_found_in_trash' => esc_html__( 'Not found in Trash', 'appside-master' ),
						),
						'supports'           => array( 'title', 'content' ),
						'hierarchical'       => false,
						'public'             => true,
						"publicly_queryable" => true,
						'show_ui'            => true,
						'show_in_menu'       => 'appside_theme_options',
						'can_export'         => true,
						'capability_type'    => 'post',
						'query_var'          => true,
						'rewrite' => true,
						'exclude_from_search' => true,
						'show_in_admin_bar' => true
					)
				),
				[
					'post_type' => 'apside-foobuilder',
					'args'      => array(
						'label'              => esc_html__( 'Footer Builder', 'appside-master' ),
						'description'        => esc_html__( 'Footer Builder', 'appside-master' ),
						'labels'             => array(
							'name'               => esc_html_x( 'Footer Builder', 'Post Type General Name', 'appside-master' ),
							'singular_name'      => esc_html_x( 'Footer Builder', 'Post Type Singular Name', 'appside-master' ),
							'menu_name'          => esc_html__( 'Footer Builder', 'appside-master' ),
							'all_items'          => esc_html__( 'Footer Builders', 'appside-master' ),
							'view_item'          => esc_html__( 'View Footer Builder', 'appside-master' ),
							'add_new_item'       => esc_html__( 'Add New Footer Builder', 'appside-master' ),
							'add_new'            => esc_html__( 'Add New Footer Builder', 'appside-master' ),
							'edit_item'          => esc_html__( 'Edit Footer Builder', 'appside-master' ),
							'update_item'        => esc_html__( 'Update Footer Builder', 'appside-master' ),
							'search_items'       => esc_html__( 'Search Footer Builder', 'appside-master' ),
							'not_found'          => esc_html__( 'Not Found', 'appside-master' ),
							'not_found_in_trash' => esc_html__( 'Not found in Trash', 'appside-master' ),
						),
						'supports'           => array( 'title', 'content' ),
						'hierarchical'       => false,
						'public'             => true,
						"publicly_queryable" => true,
						'show_ui'            => true,
						'show_in_menu'       => 'appside_theme_options',
						'can_export'         => true,
						'capability_type'    => 'post',
						'query_var'          => true
					)
				],
				[
					'post_type' => 'apside-hebuilder',
					'args'      => array(
						'label'              => esc_html__( 'Header Builder', 'appside-master' ),
						'description'        => esc_html__( 'Header Builder', 'appside-master' ),
						'labels'             => array(
							'name'               => esc_html_x( 'Header Builder', 'Post Type General Name', 'appside-master' ),
							'singular_name'      => esc_html_x( 'Header Builder', 'Post Type Singular Name', 'appside-master' ),
							'menu_name'          => esc_html__( 'Header Builder', 'appside-master' ),
							'all_items'          => esc_html__( 'Header Builders', 'appside-master' ),
							'view_item'          => esc_html__( 'View Header Builder', 'appside-master' ),
							'add_new_item'       => esc_html__( 'Add New Header Builder', 'appside-master' ),
							'add_new'            => esc_html__( 'Add New Header Builder', 'appside-master' ),
							'edit_item'          => esc_html__( 'Edit Header Builder', 'appside-master' ),
							'update_item'        => esc_html__( 'Update Header Builder', 'appside-master' ),
							'search_items'       => esc_html__( 'Search Header Builder', 'appside-master' ),
							'not_found'          => esc_html__( 'Not Found', 'appside-master' ),
							'not_found_in_trash' => esc_html__( 'Not found in Trash', 'appside-master' ),
						),
						'supports'           => array( 'title', 'content' ),
						'hierarchical'       => false,
						'public'             => true,
						"publicly_queryable" => true,
						'show_ui'            => true,
						'show_in_menu'       => 'appside_theme_options',
						'can_export'         => true,
						'capability_type'    => 'post',
						'query_var'          => true
					)
				],
				[
					'post_type' => 'portfolio',
					'args'      => array(
						'label'              => esc_html__( 'Portfolio', 'appside-master' ),
						'description'        => esc_html__( 'Portfolio', 'appside-master' ),
						'labels'             => array(
							'name'               => esc_html_x( 'Portfolio', 'Post Type General Name', 'appside-master' ),
							'singular_name'      => esc_html_x( 'Portfolio', 'Post Type Singular Name', 'appside-master' ),
							'menu_name'          => esc_html__( 'Portfolio', 'appside-master' ),
							'all_items'          => esc_html__( 'Portfolios', 'appside-master' ),
							'view_item'          => esc_html__( 'View Portfolio', 'appside-master' ),
							'add_new_item'       => esc_html__( 'Add New Portfolio', 'appside-master' ),
							'add_new'            => esc_html__( 'Add New Portfolio', 'appside-master' ),
							'edit_item'          => esc_html__( 'Edit Portfolio', 'appside-master' ),
							'update_item'        => esc_html__( 'Update Portfolio', 'appside-master' ),
							'search_items'       => esc_html__( 'Search Portfolio', 'appside-master' ),
							'not_found'          => esc_html__( 'Not Found', 'appside-master' ),
							'not_found_in_trash' => esc_html__( 'Not found in Trash', 'appside-master' ),
						),
						'supports'           => array( 'title','editor', 'excerpt','thumbnail','comments' ),
						'hierarchical'       => false,
						'public'             => true,
						"publicly_queryable" => true,
						'show_ui'            => true,
						'show_in_menu'       => 'appside_theme_options',
						"rewrite" => array( 'slug' => 'all-portfolios', 'with_front' => true),
						'can_export'         => true,
						'capability_type'    => 'post',
						'query_var'          => true
					)
				]
			);

			if ( ! empty( $all_post_type ) && is_array( $all_post_type ) ) {

				foreach ( $all_post_type as $post_type ) {
					call_user_func_array( 'register_post_type', $post_type );
				}
			}

			//add custom taxonomy

			/**
			 * Custom Taxonomy Register
			 */

			$all_custom_taxonmy = array(
				array(
					'taxonomy' => 'portfolio-cat',
					'object_type' => 'portfolio',
					'args' => array(
						"labels" => array(
							"name" => esc_html__( "Portfolio Category", 'additrans-master' ),
							"singular_name" => esc_html__( "Portfolio Category", 'additrans-master' ),
							"menu_name" => esc_html__( "Portfolio Category", 'additrans-master' ),
							"all_items" => esc_html__( "All Portfolio Category", 'additrans-master' ),
							"add_new_item" => esc_html__( "Add New Portfolio Category", 'additrans-master' )
						),
						"public" => true,
						"hierarchical" => true,
						"show_ui" => true,
						"show_in_menu" => true,
						"show_in_nav_menus" => true,
						"query_var" => true,
						"rewrite" => array( 'slug' => 'portfolio-cat', 'with_front' => true),
						"show_admin_column" => true,
						"show_in_rest" => false,
						"show_in_quick_edit" => true,
					)
				)
			);

			if (is_array($all_custom_taxonmy) && !empty($all_custom_taxonmy)){
				foreach ($all_custom_taxonmy as $taxonomy){
					call_user_func_array('register_taxonomy',$taxonomy);
				}
			}

			flush_rewrite_rules();

			//add header/footer/megamenu builder support for elementor page builder
			if (defined('ELEMENTOR_VERSION')){
				$all_custom_ctp = array('apside-foobuilder','apside-hebuilder','apside-mega-menu');
				$get_elementor_cpt_support = get_option('elementor_cpt_support');

				if (!empty($get_elementor_cpt_support) && !in_array($all_custom_ctp,$get_elementor_cpt_support)){
					$get_elementor_cpt_support[] = 'apside-foobuilder';
					$get_elementor_cpt_support[] = 'apside-hebuilder';
					$get_elementor_cpt_support[] = 'apside-mega-menu';
					$get_elementor_cpt_support[] = 'post';
					$get_elementor_cpt_support[] = 'page';
				    update_option( 'elementor_cpt_support',$get_elementor_cpt_support );
				}
			}
		}

		/**
		 * set meta box value
		 * @since 2.0.0
		 * */
		public function add_meta_boxes_value(){
			global $post;
			$all_custom_ctp = array('apside-foobuilder','apside-hebuilder','apside-mega-menu');

			if ( !in_array( $post->post_type, $all_custom_ctp ) ) {return;}
			if ( '' !== $post->page_template ) {return;}

			update_post_meta($post->ID, '_wp_page_template', 'elementor_canvas');
		}

	}//end class

	if ( class_exists( 'Appside_Custom_Post_Type' ) ) {
		Appside_Custom_Post_Type::getInstance();
	}
}