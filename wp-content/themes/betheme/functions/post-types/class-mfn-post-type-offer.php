<?php
/**
 * Custom post type: Offer
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Offer')) {
	class Mfn_Post_Type_Offer extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Offer constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-offer_columns', array($this, 'add_columns'));

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

				'id' => 'mfn-meta-offer',
				'title' => esc_html__('Offer Item Options', 'mfn-opts'),
				'page' => 'offer',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(

					array(
						'id' => 'mfn-post-desc',
						'type' => 'custom',
						'title' => __('Featured Image size', 'mfn-opts'),
						'sub_desc' => __('recommended', 'mfn-opts'),
						'desc' => __('960px x 540px', 'mfn-opts'),
						'action' => 'description',
					),

					array(
						'id' => 'mfn-post-link_title',
						'type' => 'text',
						'title' => __('Button text', 'mfn-opts'),
						'class' => 'small-text',
						'std' => 'Read more',
					),

					array(
						'id' => 'mfn-post-link',
						'type' => 'text',
						'title' => __('Button link', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-target',
						'type' => 'switch',
						'title' => __('Open link in a new window', 'mfn-opts'),
						'options' => array( '1' => 'On', '0' => 'Off' ),
						'std' => '0'
					),

					array(
						'id' => 'mfn-post-thumbnail',
						'type' => 'upload',
						'title' => __('Thumbnail', 'mfn-opts'),
						'sub_desc' => __('Thumbnail for Offer Slider Thumb Pager', 'mfn-opts'),
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
				'name' => esc_html__('Offer', 'mfn-opts'),
				'singular_name' => esc_html__('Offer Item', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Item', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Item', 'mfn-opts'),
				'new_item' => esc_html__('New Item', 'mfn-opts'),
				'view_item' => esc_html__('View Item', 'mfn-opts'),
				'search_items' => esc_html__('Search Offer Items', 'mfn-opts'),
				'not_found' => esc_html__('No items found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No items found in Trash', 'mfn-opts'),
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-clipboard',
				'public' => false,
				'show_ui' => true,
				'supports' => array('editor', 'thumbnail', 'title', 'page-attributes'),
			);

			register_post_type('offer', $args);

			register_taxonomy('offer-types', 'offer', array(
				'label' => esc_html__('Offer categories', 'mfn-opts'),
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
				'offer_thumbnail' => esc_html__('Thumbnail', 'mfn-opts'),
				'title' => esc_html__('Title', 'mfn-opts'),
				'offer_types' => esc_html__('Categories', 'mfn-opts'),
				'offer_order' => esc_html__('Order', 'mfn-opts'),
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
				case "offer_thumbnail":
					if (has_post_thumbnail()) {
						the_post_thumbnail('50x50');
					}
					break;
				case "offer_types":
					echo get_the_term_list($post->ID, 'offer-types', '', ', ', '');
					break;
				case "offer_order":
					echo esc_attr($post->menu_order);
					break;
			}
		}

	}
}

new Mfn_Post_Type_Offer();
