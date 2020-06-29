<?php
/**
 * Custom post type: Post
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Post')) {
	class Mfn_Post_Type_Post extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Post constructor
		 */

		public function __construct()
		{
			parent::__construct();

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
      	'id' => 'mfn-meta-post',
      	'title' => esc_html__('Post Options', 'mfn-opts'),
      	'page' => 'post',
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
      			'title' => __('Hide The Content', 'mfn-opts'),
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

      		array(
      			'id' => 'mfn-post-subheader-image',
      			'type' => 'upload',
      			'title' => __('Subheader | Image', 'mfn-opts'),
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
      			'id' => 'mfn-post-hide-image',
      			'type' => 'switch',
      			'title' => __('Featured Image | Hide', 'mfn-opts'),
      			'desc' => __('Hide Featured Image in post details', 'mfn-opts'),
      			'options'	=> array( '0' => 'Off', '1' => 'On' ),
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
      			'id' => 'mfn-post-link',
      			'type' => 'text',
      			'title' => __('External Link', 'mfn-opts'),
      			'desc' => __('for <b>Post Format: Link</b>', 'mfn-opts'),
      		),

      		array(
      			'id' => 'mfn-post-bg',
      			'type' => 'color',
      			'title' => __('Background Color', 'mfn-opts'),
      			'desc' => __('for <b>Blog Layout: Masonry Tiles</b> & <b>Template: Intro</b>', 'mfn-opts'),
      		),

      		array(
      			'id' => 'mfn-post-intro',
      			'type' => 'checkbox',
      			'title' => __('Intro | Options', 'mfn-opts'),
      			'desc' => __('for <b>Template: Intro</b>', 'mfn-opts'),
      			'options' => array(
      				'light' => __('Light | light image, dark text', 'mfn-opts'),
      				'full-screen' => __('Full Screen', 'mfn-opts'),
      				'parallax' => __('Parallax', 'mfn-opts'),
      				'cover' => __('Background size: Cover<span>enabled by default in parallax</span>', 'mfn-opts'),
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

	}
}

new Mfn_Post_Type_Post();
