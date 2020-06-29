<?php
/**
 * Custom post type: Template
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Template')) {
	class Mfn_Post_Type_Template extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Template constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// admin only methods

			if( is_admin() ){
				$this->fields = $this->set_fields();
				$this->builder = new Mfn_Builder_Admin();
			}

		}

		/**
		 * Set post type fields
		 */

		private function set_fields(){

			return array(

				'id' => 'mfn-meta-template',
				'title' => esc_html__('Template Options', 'mfn-opts'),
				'page' => 'template',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(),
				
			);

		}

		/**
		 * Register new post type and related taxonomy
		 */

		public function register()
		{
			$labels = array(
				'name' => esc_html__('Templates', 'mfn-opts'),
				'singular_name' => esc_html__('Template', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Template', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Template', 'mfn-opts'),
				'new_item' => esc_html__('New Template', 'mfn-opts'),
				'view_item' => esc_html__('View Template', 'mfn-opts'),
				'search_items' => esc_html__('Search Template', 'mfn-opts'),
				'not_found' => esc_html__('No templates found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No templates found in Trash', 'mfn-opts'),
				'parent_item_colon' => ''
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-schedule',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'rewrite' => array('slug'=>'template-item', 'with_front'=>true),
				'supports' => array( 'title' ),
			);

			register_post_type('template', $args);
		}

	}
}

new Mfn_Post_Type_Template();
