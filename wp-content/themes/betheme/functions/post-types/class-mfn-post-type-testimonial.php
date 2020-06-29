<?php
/**
 * Custom post type: Testimonial
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Testimonial')) {
	class Mfn_Post_Type_Testimonial extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Testimonial constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-testimonial_columns', array($this, 'add_columns'));

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

				'id' => 'mfn-meta-testimonial',
				'title' => esc_html__('Testimonial Options', 'mfn-opts'),
				'page' => 'testimonial',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(

					array(
						'id' => 'mfn-post-desc',
						'type' => 'custom',
						'title' => __('Featured Image size', 'mfn-opts'),
						'sub_desc' => __('recommended', 'mfn-opts'),
						'desc' => __('85px x 85px', 'mfn-opts'),
						'action' => 'description',
					),

					array(
						'id' => 'mfn-post-author',
						'type' => 'text',
						'title' => __('Author', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-company',
						'type' => 'text',
						'title' => __('Company', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-link',
						'type' => 'text',
						'title' => __('Link', 'mfn-opts'),
						'sub_desc' => __('Link to company page', 'mfn-opts'),
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
				'name' => esc_html__('Testimonials', 'mfn-opts'),
				'singular_name' => esc_html__('Testimonial', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Testimonial', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Testimonial', 'mfn-opts'),
				'new_item' => esc_html__('New Testimonial', 'mfn-opts'),
				'view_item' => esc_html__('View Testimonials', 'mfn-opts'),
				'search_items' => esc_html__('Search Testimonials', 'mfn-opts'),
				'not_found' => esc_html__('No testimonials found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No testimonials found in Trash', 'mfn-opts'),
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-format-quote',
				'public' => false,
				'show_ui' => true,
				'supports' => array('title', 'editor', 'page-attributes', 'thumbnail'),
			);

			register_post_type('testimonial', $args);

			register_taxonomy('testimonial-types', 'testimonial', array(
				'label' => esc_html__('Testimonial categories', 'mfn-opts'),
				'hierarchical' => true,
			));
		}

		/**
		 * Add new columns to posts screen
		 */

		public function add_columns($columns)
		{
			$newcolumns = array(
				'cb' => '<input type="checkbox" />',
				'testimonial_thumbnail' => esc_html__('Photo', 'mfn-opts'),
				'title' => esc_html__('Title', 'mfn-opts'),
				'testimonial_types' => esc_html__('Categories', 'mfn-opts'),
				'testimonial_order' => esc_html__('Order', 'mfn-opts'),
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
				case 'testimonial_thumbnail':
					if (has_post_thumbnail()) {
						the_post_thumbnail('50x50');
					}
					break;
				case 'testimonial_types':
					echo get_the_term_list($post->ID, 'testimonial-types', '', ', ', '');
					break;
				case 'testimonial_order':
					echo esc_attr($post->menu_order);
					break;
			}
		}

	}
}

new Mfn_Post_Type_Testimonial();
