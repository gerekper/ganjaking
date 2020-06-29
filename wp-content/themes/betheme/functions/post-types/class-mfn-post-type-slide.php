<?php
/**
 * Custom post type: Slide
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Slide')) {
	class Mfn_Post_Type_Slide extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Slide constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-slide_columns', array($this, 'add_columns'));

			// allows to add or remove (unset) custom columns to the list post/page/custom post type pages
			add_action('manage_posts_custom_column', array($this, 'custom_column'));

			// admin only methods

			if( is_admin() ){
				$this->fields = $this->set_fields();
			}

		}

		/**
		 * Set post type fields
		 */

		private function set_fields(){

			return array(
				
				'id' => 'mfn-meta-slide',
				'title' => esc_html__('Slide Options', 'mfn-opts'),
				'page' => 'slide',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(

					array(
						'id' => 'mfn-post-desc',
						'type' => 'custom',
						'title' => __('Featured Image size', 'mfn-opts'),
						'sub_desc' => __('recommended', 'mfn-opts'),
						'desc' => __('1630px x 860px', 'mfn-opts'),
						'action' => 'description',
					),

					array(
						'id' => 'mfn-post-link',
						'type' => 'text',
						'title' => __('Link', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-target',
						'type' => 'select',
						'title' => __('Target', 'mfn-opts'),
						'options'	=> array(
							0 => __('Default | _self', 'mfn-opts'),
							1 => __('New Tab or Window | _blank', 'mfn-opts'),
							'lightbox' => __('Lightbox (image or embed video)', 'mfn-opts'),
						),
					),

					array(
						'id' => 'mfn-post-desc',
						'type' => 'textarea',
						'title' => __('Description', 'mfn-opts'),
						'sub_desc' => __('for Slider Style: Image & Text', 'mfn-opts'),
					),

				),
			);

		}

		/**
		 * Register new post type and related taxonomy
		 */

		public function register()
		{
			$labels = array(
				'name' => esc_html__('Slides', 'mfn-opts'),
				'singular_name' => esc_html__('Slide', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Slide', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Slide', 'mfn-opts'),
				'new_item' => esc_html__('New Slide', 'mfn-opts'),
				'view_item' => esc_html__('View Slides', 'mfn-opts'),
				'search_items' => esc_html__('Search Slides', 'mfn-opts'),
				'not_found' => esc_html__('No slides found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No slides found in Trash', 'mfn-opts'),
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-slides',
				'public' => false,
				'show_ui' => true,
				'supports' => array('title', 'page-attributes', 'thumbnail'),
			);

			register_post_type('slide', $args);

			register_taxonomy('slide-types', 'slide', array(
				'label' =>  esc_html__('Slide categories', 'mfn-opts'),
				'hierarchical' => true,
			));
		}

		/**
		 * Add new columns to posts screen
		 */

		public function add_columns($columns)
		{
			$newcolumns = array(
				"cb" => '<input type="checkbox" />',
				"slide_thumbnail"	=> esc_html__('Photo', 'mfn-opts'),
				"title" => esc_html__('Title', 'mfn-opts'),
				"slide_types" => esc_html__('Categories', 'mfn-opts'),
				"slide_order" => esc_html__('Order', 'mfn-opts'),
  		);
			$columns = array_merge($newcolumns, $columns);

			return $columns;
		}

		/**
		 * Custom column on posts screen
		 */

		public function custom_column($column)
		{
			global $post;

			switch ($column) {
				case "slide_thumbnail":
					if (has_post_thumbnail()) {
						the_post_thumbnail('50x50');
					}
					break;
				case "slide_types":
					echo get_the_term_list($post->ID, 'slide-types', '', ', ', '');
					break;
				case "slide_order":
					echo esc_attr($post->menu_order);
					break;
			}
		}

	}
}

new Mfn_Post_Type_Slide();
