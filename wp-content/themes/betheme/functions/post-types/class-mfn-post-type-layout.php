<?php
/**
 * Custom post type: Layout
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Layout')) {
	class Mfn_Post_Type_Layout extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Layout constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-layout_columns', array($this, 'add_columns'));

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
				
				'id' => 'mfn-meta-layout',
				'title' => esc_html__('Layout Options', 'mfn-opts'),
				'page' => 'layout',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(

					// layout

					array(
						'id' => 'mfn-post-info-layout',
						'type' => 'info',
						'title' => '',
						'desc' => __('Layout', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-layout',
						'type' => 'radio_img',
						'title' => __('Layout', 'mfn-opts'),
						'options' => array(
							'full-width' => array(
								'title' => 'Full width',
								'img' => MFN_OPTIONS_URI.'img/select/style/full-width.png'
							),
							'boxed' => array(
								'title' => 'Boxed',
								'img' => MFN_OPTIONS_URI.'img/select/style/boxed.png'
							),
						),
						'std' => 'full-width',
						'class' => 'wide',
					),

					array(
						'id' => 'mfn-post-info-background',
						'type' => 'info',
						'title' => '',
						'desc' => __('Background', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-bg',
						'type' => 'upload',
						'title' => __('Image', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-bg-pos',
						'type' => 'select',
						'title' => __('Position', 'mfn-opts'),
						'desc' => __('This option can be used only with your custom image selected above', 'mfn-opts'),
						'options' => mfna_bg_position(),
						'std' => 'center top no-repeat',
					),

					// logo

					array(
						'id' => 'mfn-post-info-logo',
						'type' => 'info',
						'title' => '',
						'desc' => __('Logo', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-logo-img',
						'type' => 'upload',
						'title' => __('Logo', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-retina-logo-img',
						'type' => 'upload',
						'title' => __('Retina', 'mfn-opts'),
						'desc' => __('Retina Logo should be 2x larger than Custom Logo', 'mfn-opts'),
						'sub_desc' => __('optional', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-sticky-logo-img',
						'type' => 'upload',
						'title' => __('Sticky Header', 'mfn-opts'),
						'sub_desc' => __('optional', 'mfn-opts'),
						'desc' => __('Use if you want different logo for Sticky Header', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-sticky-retina-logo-img',
						'type' => 'upload',
						'title' => __('Sticky Header Retina', 'mfn-opts'),
						'sub_desc' => __('optional', 'mfn-opts'),
						'desc' => __('Retina Logo should be 2x larger than Sticky Logo', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-responsive-logo-img',
						'type' => 'upload',
						'title' => __('Mobile', 'mfn-opts'),
						'sub_desc' => __('<b>< 768px</b><br />optional', 'mfn-opts'),
						'desc' => __('Use if you want different logo for Mobile', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-responsive-retina-logo-img',
						'type' => 'upload',
						'title' => __('Mobile Retina', 'mfn-opts'),
						'sub_desc' => __('optional', 'mfn-opts'),
						'desc' => __('Retina Logo should be 2x larger than Mobile Logo', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-responsive-sticky-logo-img',
						'type' => 'upload',
						'title' => __('Mobile Sticky Header', 'mfn-opts'),
						'sub_desc' => __('<b>< 768px</b><br />optional', 'mfn-opts'),
						'desc' => __('Use if you want different logo for Mobile Sticky Header', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-responsive-sticky-retina-logo-img',
						'type' => 'upload',
						'title' => __('Mobile Sticky Header Retina', 'mfn-opts'),
						'sub_desc' => __('optional', 'mfn-opts'),
						'desc' => __('Retina Logo should be 2x larger than Mobile Sticky Header Logo', 'mfn-opts'),
					),

					// header

					array(
						'id' => 'mfn-post-info-header',
						'type' => 'info',
						'title' => '',
						'desc' => __('Header', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-header-style',
						'type' => 'radio_img',
						'title' => __('Style', 'mfn-opts'),
						'options' => mfna_header_style(),
						'std' => 'modern',
						'class' => 'wide',
					),

					array(
						'id' => 'mfn-post-minimalist-header',
						'type' => 'select',
						'title' => __('Minimalist', 'mfn-opts'),
						'desc' => __('Header without background image & padding', 'mfn-opts'),
						'options' => array(
							'0' => 'Default | OFF',
							'1' => 'Minimalist | ON',
							'no' => 'Minimalist without Header space',
						),
					),

					array(
						'id' => 'mfn-post-sticky-header',
						'type' => 'switch',
						'title' => __('Sticky', 'mfn-opts'),
						'options' => array('1' => 'On','0' => 'Off'),
						'std' => '1'
					),

					array(
						'id' => 'mfn-post-sticky-header-style',
						'type' => 'select',
						'title' => __('Sticky | Style', 'mfn-opts'),
						'options'	=> array(
							'tb-color' => __('The same as Top Bar Left background', 'mfn-opts'),
							'white' => __('White', 'mfn-opts'),
							'dark' => __('Dark', 'mfn-opts'),
						),
					),

					// colors

					array(
						'id' => 'mfn-post-info-colors',
						'type' => 'info',
						'title' => '',
						'desc' => __('Colors', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-skin',
						'type' => 'select',
						'title' => __('Skin', 'mfn-opts'),
						'sub_desc' => __('Choose one of the predefined styles or set your own colors', 'mfn-opts'),
						'desc' => __('<strong>Important:</strong> Color options can be used only with the <strong>Custom Skin</strong>', 'mfn-opts'),
						'options' => mfna_skin(),
						'std' => 'custom',
					),

					array(
						'id' => 'mfn-post-background-subheader',
						'type' => 'color',
						'title' => __('Subheader | Background', 'mfn-opts'),
						'std' => '#F7F7F7',
					),

					array(
						'id' => 'mfn-post-color-subheader',
						'type' => 'color',
						'title' => __('Subheader | Text color', 'mfn-opts'),
						'std' => '#888888',
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
				'name' => esc_html__('Layouts', 'mfn-opts'),
				'singular_name' => esc_html__('Layout', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Layout', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Layout', 'mfn-opts'),
				'new_item' => esc_html__('New Layout', 'mfn-opts'),
				'view_item' => esc_html__('View Layout', 'mfn-opts'),
				'search_items' => esc_html__('Search Layouts', 'mfn-opts'),
				'not_found' => esc_html__('No layouts found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No layouts found in Trash', 'mfn-opts'),
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-edit',
				'public' => false,
				'show_ui' => true,
				'supports' => array( 'title', 'page-attributes' ),
			);

			register_post_type('layout', $args);
		}

		/**
		 * Add new columns to posts screen
		 */

		public function add_columns($columns)
		{
			$newcolumns = array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__('Title', 'mfn-opts'),
				'layout_ID' => esc_html__('Layout ID', 'mfn-opts'),
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
				case 'layout_ID':
					echo esc_attr($post->ID);
					break;
			}
		}

	}
}

new Mfn_Post_Type_Layout();
