<?php
/**
 * Custom post type: Page
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type_Page')) {
	class Mfn_Post_Type_Page extends Mfn_Post_Type
	{

		/**
		 * Mfn_Post_Type_Page constructor
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

  			'id' => 'mfn-meta-page',
  			'title' => esc_html__('Page Options', 'mfn-opts'),
  			'page' => 'page',
  			'context' => 'normal',
  			'priority' => 'default',
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
  					'options'	=> array('1' => 'On', '0' => 'Off'),
  					'std' => '0'
  				),

  				array(
  					'id' => 'mfn-post-layout',
  					'type' => 'radio_img',
  					'title' => __('Layout', 'mfn-opts'),
  					'desc' => __('<b>Full width</b> sections works only <b>without</b> sidebars', 'mfn-opts'),
  					'options' => array(
  						'no-sidebar' => array('title' => 'Full width No sidebar', 'img' => MFN_OPTIONS_URI .'img/1col.png'),
  						'left-sidebar' => array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI .'img/2cl.png'),
  						'right-sidebar' => array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI .'img/2cr.png'),
  						'both-sidebars' => array('title' => 'Both Sidebars', 'img' => MFN_OPTIONS_URI .'img/2sb.png'),
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
  					'id' => 'mfn-post-slider-shortcode',
  					'type' => 'text',
  					'title' => __('Slider | Shortcode', 'mfn-opts'),
  					'desc' => __('Paste your slider shortcode here if you use slider other than Revolution or Layer', 'mfn-opts'),
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
  					'id' => 'mfn-post-one-page',
  					'type' => 'switch',
  					'title' => __('One Page', 'mfn-opts'),
  					'options'	=> array( '0' => 'Off', '1' => 'On' ),
  					'std' => '0'
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
  					'id' => 'mfn-post-custom-layout',
  					'type' => 'select',
  					'title' => __('Custom | Layout', 'mfn-opts'),
  					'desc' => __('Custom Layout overwrites Theme Options', 'mfn-opts'),
  					'options' => $this->get_layouts(),
  				),

  				array(
  					'id' => 'mfn-post-menu',
  					'type' => 'select',
  					'title' => __('Custom | Menu', 'mfn-opts'),
  					'desc' => __('Do <b>not</b> work with Split Menu', 'mfn-opts'),
  					'options' => $this->get_menus(),
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

  				// custom

  				array(
  					'id' => 'mfn-meta-info-custom',
  					'type' => 'info',
  					'title' => '',
  					'desc' => __('Custom CSS', 'mfn-opts'),
  					'class' => 'mfn-info',
  				),

  				array(
  					'id' => 'mfn-post-css',
  					'type' => 'textarea',
  					'title' => __('Custom | CSS', 'mfn-opts'),
  					'desc' => __('Paste your custom CSS code for this page', 'mfn-opts'),
  					'class' => 'full-width',
  				),

  			),
  		);

		}

	}
}

new Mfn_Post_Type_Page();
