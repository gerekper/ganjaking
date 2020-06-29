<?php
/**
 * Custom post type: Client
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Client')) {
	class Mfn_Post_Type_Client extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Client constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-client_columns', array($this, 'add_columns'));

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

  		  'id' => 'mfn-meta-client',
  		  'title' => esc_html__('Client Options', 'mfn-opts'),
  		  'page' => 'client',
  		  'context' => 'normal',
  		  'priority' => 'high',
  		  'fields' => array(

  			  array(
  				  'id' => 'mfn-post-desc',
  				  'type' => 'custom',
  				  'title' => __('Featured Image size', 'mfn-opts'),
  				  'sub_desc' => __('recommended', 'mfn-opts'),
  				  'desc' => __('150px x 75px', 'mfn-opts'),
  				  'action' => 'description',
  			  ),

  			  array(
  				  'id' => 'mfn-post-link',
  				  'type' => 'text',
  				  'title' => __('Link', 'mfn-opts'),
  				  'sub_desc' => __('Link to client`s site', 'mfn-opts'),
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
  			'name' => esc_html__('Clients', 'mfn-opts'),
  			'singular_name' => esc_html__('Client', 'mfn-opts'),
  			'add_new' => esc_html__('Add New', 'mfn-opts'),
  			'add_new_item' => esc_html__('Add New Client', 'mfn-opts'),
  			'edit_item' => esc_html__('Edit Client', 'mfn-opts'),
  			'new_item' => esc_html__('New Client', 'mfn-opts'),
  			'view_item' => esc_html__('View Clients', 'mfn-opts'),
  			'search_items' => esc_html__('Search Clients', 'mfn-opts'),
  			'not_found' => esc_html__('No clients found', 'mfn-opts'),
  			'not_found_in_trash' => esc_html__('No clients found in Trash', 'mfn-opts'),
  		);

			$args = array(
  			'labels' => $labels,
  			'menu_icon' => 'dashicons-businessman',
  			'public' => false,
  			'show_ui' => true,
  			'supports' => array('title', 'thumbnail', 'page-attributes'),
  		);

			register_post_type('client', $args);

			register_taxonomy('client-types', 'client', array(
			'label' =>  esc_html__('Client categories', 'mfn-opts'),
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
  			'client_thumbnail' => esc_html__('Thumbnail', 'mfn-opts'),
  			'title' => esc_html__('Title', 'mfn-opts'),
  			'client_types' => esc_html__('Categories', 'mfn-opts'),
  			'client_order' => esc_html__('Order', 'mfn-opts'),
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
  			case 'client_thumbnail':
  				if (has_post_thumbnail()) {
  					the_post_thumbnail('50x50');
  				}
  				break;
  			case 'client_types':
  				echo get_the_term_list($post->ID, 'client-types', '', ', ', '');
  				break;
  			case 'client_order':
  				echo esc_html($post->menu_order);
  				break;
  		}
		}

	}
}

new Mfn_Post_Type_Client();
