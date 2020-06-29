<?php
/**
 * Custom post type: Portfolio
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Portfolio')) {
	class Mfn_Post_Type_Portfolio extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Portfolio constructor
		 */

		public function __construct()
		{
			parent::__construct();

			// fires after WordPress has finished loading but before any headers are sent
			add_action('init', array($this, 'register'));

			// applied to the list of columns to print on the manage posts screen for a custom post type
			add_filter('manage_edit-portfolio_columns', array($this, 'add_columns'));

			// allows to add or remove (unset) custom columns to the list post/page/custom post type pages
			add_action('manage_posts_custom_column', array($this, 'custom_column'));

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

				'id' => 'mfn-meta-portfolio',
				'title' => esc_html__('Portfolio Item Options', 'mfn-opts'),
				'page' => 'portfolio',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(

					// layout

					array(
						'id' => 'mfn-meta-info-layout',
						'type' => 'info',
						'title' => '',
						'desc' => __('Layout', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-hide-content',
						'type' => 'switch',
						'title' => __('Hide the content', 'mfn-opts'),
						'sub_desc' => __('Hide the content from the WordPress editor', 'mfn-opts'),
						'desc' => __('<strong>Turn it ON if you build content using Content Builder</strong><br />Use the Content item if you want to display the Content from editor within the Content Builder', 'mfn-opts'),
						'options' => array('1' => 'On', '0' => 'Off'),
						'std' => '0'
					),

					array(
						'id' => 'mfn-post-layout',
						'type' => 'radio_img',
						'title' => __('Layout', 'mfn-opts'),
						'desc' => __('<b>Full width</b> sections works only <b>without</b> sidebars', 'mfn-opts'),
						'options' => array(
							'no-sidebar' => array('title' => 'Full width. No sidebar', 'img' => MFN_OPTIONS_URI.'img/1col.png'),
							'left-sidebar' => array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cl.png'),
							'right-sidebar' => array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cr.png'),
							'both-sidebars' => array('title' => 'Both Sidebars', 'img' => MFN_OPTIONS_URI.'img/2sb.png'),
						),
						'std' => mfn_opts_get('sidebar-layout'),
					),

					array(
						'id' => 'mfn-post-sidebar',
						'type' => 'select',
						'title' => __('Sidebar', 'mfn-opts'),
						'desc' => __('Shows only if layout with sidebar is selected', 'mfn-opts'),
						'options' => mfn_opts_get('sidebars'),
					),

					array(
						'id' => 'mfn-post-sidebar2',
						'type' => 'select',
						'title' => __('Sidebar 2nd', 'mfn-opts'),
						'desc' => __('Shows only if layout with both sidebars is selected', 'mfn-opts'),
						'options' => mfn_opts_get('sidebars'),
					),

					array(
						'id' => 'mfn-post-template',
						'type' => 'select',
						'title' => __('Template', 'mfn-opts'),
						'options' => array(
							'' => __('Default Template', 'mfn-opts'),
							'builder' => __('Builder', 'mfn-opts'),
							'intro' => __('Intro Header', 'mfn-opts'),
						),
					),

					// media

					array(
						'id' => 'mfn-meta-info-media',
						'type' => 'info',
						'title' => '',
						'desc' => __('Media', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-slider',
						'type' => 'select',
						'title' => __('Slider | Revolution Slider', 'mfn-opts'),
						'desc' => __('Select one from the list of available <a target="_blank" href="admin.php?page=revslider">Revolution Sliders</a>', 'mfn-opts'),
						'options' => Mfn_Builder_Helper::get_sliders('rev'),
					),

					array(
						'id' => 'mfn-post-slider-layer',
						'type' => 'select',
						'title' => __('Slider | Layer Slider', 'mfn-opts'),
						'desc' => __('Select one from the list of available <a target="_blank" href="admin.php?page=layerslider">Layer Sliders</a>', 'mfn-opts'),
						'options' => Mfn_Builder_Helper::get_sliders('layer'),
					),

					array(
						'id' => 'mfn-post-video',
						'type' => 'text',
						'title' => __('Video | ID', 'mfn-opts'),
						'sub_desc' => __('YouTube or Vimeo', 'mfn-opts'),
						'desc' => __('It`s placed in every YouTube & Vimeo video, for example:<br /><b>YouTube:</b> http://www.youtube.com/watch?v=<u>WoJhnRczeNg</u><br /><b>Vimeo:</b> http://vimeo.com/<u>62954028</u>', 'mfn-opts'),
						'class' => 'small-text mfn-post-format video'
					),

					array(
						'id' => 'mfn-post-video-mp4',
						'type' => 'upload',
						'title' => __('Video | HTML5', 'mfn-opts'),
						'sub_desc' => __('m4v [.mp4]', 'mfn-opts'),
						'desc' => __('<strong>Notice:</strong> HTML5 video works only in modern browsers', 'mfn-opts'),
						'class' => __('video', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-header-bg',
						'type' => 'upload',
						'title' => __('Header Image', 'mfn-opts'),
					),

					// description

					array(
						'id' => 'mfn-meta-info-desc',
						'type' => 'info',
						'title' => '',
						'desc' => __('Description', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-client',
						'type' => 'text',
						'title' => __('Client', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-link',
						'type' => 'text',
						'title' => __('Website', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-task',
						'type' => 'text',
						'title' => __('Task', 'mfn-opts'),
					),

					// options

					array(
						'id' => 'mfn-meta-info-options',
						'type' => 'info',
						'title' => '',
						'desc' => __('Options', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-hide-title',
						'type' => 'switch',
						'title' => __('Subheader | Hide', 'mfn-opts'),
						'options'	=> array('1' => 'On', '0' => 'Off'),
						'std' => '0'
					),

					array(
						'id' => 'mfn-post-remove-padding',
						'type' => 'switch',
						'title' => __('Content | Remove Padding', 'mfn-opts'),
						'desc' => __('Remove default Content Padding', 'mfn-opts'),
						'options' => array('1' => 'On','0' => 'Off'),
						'std' => '0'
					),

					array(
						'id' => 'mfn-post-slider-header',
						'type' => 'switch',
						'title' => __('Slider | Show in Header', 'mfn-opts'),
						'sub_desc' => __('Show slider in Header instead of the Content', 'mfn-opts'),
						'options' => array( '1' => 'On', '0' => 'Off' ),
						'std' => '0'
					),

					array(
						'id' => 'mfn-post-custom-layout',
						'type' => 'select',
						'title' => __('Custom | Layout', 'mfn-opts'),
						'desc' => __('Custom Layout overwrites Theme Options', 'mfn-opts'),
						'options' 	=> $this->get_layouts(),
					),

					// advanced

					array(
						'id' => 'mfn-meta-info-advanced',
						'type' => 'info',
						'title' => '',
						'desc' => __('Advanced', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-post-bg',
						'type' => 'upload',
						'title' => __('Background | Image', 'mfn-opts'),
						'desc' => __('for <b>Portfolio Layout: List</b>', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-bg-hover',
						'type' => 'color',
						'title' => __('Background | Color', 'mfn-opts'),
						'desc' => __('for <b>Portfolio Layout: List & Masonry Hover Details</b>', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-post-intro',
						'type' => 'checkbox',
						'title' => __('Intro | Options', 'mfn-opts'),
						'desc' => __('for <b>Template: Intro</b>', 'mfn-opts'),
						'options' => array(
							'light' => __('Light | light image, dark text', 'mfn-opts'),
							'full-screen'	=> __('Full Screen', 'mfn-opts'),
							'parallax' => __('Parallax', 'mfn-opts'),
							'cover' => __('Background size: Cover<span>enabled by default in parallax</span>', 'mfn-opts'),
						),
					),

					array(
						'id' => 'mfn-post-size',
						'type' => 'select',
						'title' => __('Masonry Flat | Item Size', 'mfn-opts'),
						'desc' => __('for <b>Portfolio Layout: Masonry Flat</b>', 'mfn-opts'),
						'options' => array(
							'' => __('Default', 'mfn-opts'),
							'wide' => __('Wide', 'mfn-opts'),
							'tall' => __('Tall', 'mfn-opts'),
							'wide tall'	=> __('Big', 'mfn-opts'),
						),
					),

					// seo

					array(
						'id' => 'mfn-meta-info-seo',
						'type' => 'info',
						'title' => '',
						'desc' => __('SEO <span>below settings overwrite theme options</span>', 'mfn-opts'),
						'class' => 'mfn-info',
					),

					array(
						'id' => 'mfn-meta-seo-title',
						'type' => 'text',
						'title' => __('SEO | Title', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-meta-seo-description',
						'type' => 'text',
						'title' => __('SEO | Description', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-meta-seo-keywords',
						'type' => 'text',
						'title' => __('SEO | Keywords', 'mfn-opts'),
					),

					array(
						'id' => 'mfn-meta-seo-og-image',
						'type' => 'upload',
						'title' => __('Open Graph | Image', 'mfn-opts'),
						'sub_desc' => __('e.g. Facebook share image', 'mfn-opts'),
					),

				),
			);

		}

		/**
		 * Register new post type and related taxonomy
		 */

		public function register()
		{
			$slug = esc_attr(mfn_opts_get('portfolio-slug', 'portfolio-item'));
			$tax = esc_attr(mfn_opts_get('portfolio-tax', 'portfolio-types'));

			$labels = array(
				'name' => esc_html__('Portfolio', 'mfn-opts'),
				'singular_name' => esc_html__('Portfolio item', 'mfn-opts'),
				'add_new' => esc_html__('Add New', 'mfn-opts'),
				'add_new_item' => esc_html__('Add New Portfolio item', 'mfn-opts'),
				'edit_item' => esc_html__('Edit Portfolio item', 'mfn-opts'),
				'new_item' => esc_html__('New Portfolio item', 'mfn-opts'),
				'view_item' => esc_html__('View Portfolio item', 'mfn-opts'),
				'search_items' => esc_html__('Search Portfolio items', 'mfn-opts'),
				'not_found' => esc_html__('No portfolio items found', 'mfn-opts'),
				'not_found_in_trash' => esc_html__('No portfolio items found in Trash', 'mfn-opts'),
				'parent_item_colon' => '',
			  );

			$args = array(
				'labels' => $labels,
				'menu_icon' => 'dashicons-portfolio',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'rewrite' => array(
					'slug' => $slug,
				),
				'supports' => array('author', 'comments', 'custom-fields', 'editor', 'excerpt', 'page-attributes', 'thumbnail', 'title'),
			);

			register_post_type('portfolio', $args);

			register_taxonomy('portfolio-types', 'portfolio', array(
				'label' => esc_html__('Portfolio categories', 'mfn-opts'),
				'hierarchical' => true,
				'query_var' => true,
				'rewrite' => array(
					'slug' => $tax,
				),
			));
		}

		/**
		 * Add new columns to posts screen
		 */

		public function add_columns($columns)
		{
			$newcolumns = array(
				'cb' => '<input type="checkbox" />',
				'portfolio_thumbnail' => esc_html__('Thumbnail', 'mfn-opts'),
				'title' => esc_html__('Title', 'mfn-opts'),
				'portfolio_types' => esc_html__('Categories', 'mfn-opts'),
				'portfolio_order' => esc_html__('Order', 'mfn-opts'),
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
				case 'portfolio_thumbnail':
					if (has_post_thumbnail()) {
						the_post_thumbnail('50x50');
					}
					break;
				case 'portfolio_types':
					echo get_the_term_list($post->ID, 'portfolio-types', '', ', ', '');
					break;
				case 'portfolio_order':
					echo esc_attr($post->menu_order);
					break;
			}
		}

	}
}

new Mfn_Post_Type_Portfolio();
