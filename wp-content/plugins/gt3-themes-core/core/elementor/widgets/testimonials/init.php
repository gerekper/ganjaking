<?php

namespace ElementorModal\Widgets;

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Testimonials')) {
	class GT3_Core_Elementor_Widget_Testimonials extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array( 'slick' )
			);
		}


		public function get_name(){
			return 'gt3-core-testimonials';
		}

		public function get_title(){
			return esc_html__('Testimonials', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-testimonial-carousel';
		}

		protected function construct() {
		}

		public function get_repeater_fields(){
			$repeater = new Repeater();

			$repeater->add_control(
				'tstm_author',
				array(
					'label' => esc_html__('Author name', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'sub_name',
				array(
					'label' => esc_html__('Sub Name', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'image',
				array(
					'label'   => esc_html__('Photo'),
					'type'    => Controls_Manager::MEDIA,
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			$repeater->add_control(
				'content',
				array(
					'label' => esc_html__('Description', 'gt3_themes_core'),
					'type'  => Controls_Manager::WYSIWYG,
				)
			);

			$repeater->add_control(
				'icons',
				array(
					'label'     => esc_html__('Icons', 'gt3_themes_core'),
					'type'      => Controls_Manager::TEXTAREA,
					'condition' => array(
						'shows' => 'never',
					),
				)
			);

			$repeater->add_control(
				'icons_preview',
				array(
					'label' => esc_html__('Icons', 'gt3_themes_core'),
					'type'  => Controls_Manager::RAW_HTML,
					'raw'   => '<input type="button" data-event="update_icons" class="update_icons" value="'.esc_html__('Update', 'gt3_themes_core').'" />
					<script> (function($) { $(".update_icons").click();})( jQuery ); </script>',
				)
			);

			$repeater->add_control(
				'add_button',
				array(
					'label'       => esc_html__('Add Icon', 'gt3_themes_core'),
					'type'        => Controls_Manager::BUTTON,
					'button_type' => 'success',
					'text'        => esc_html__('Add icon', 'gt3_themes_core'),
					'event'       => 'show_modal',
					'separator'   => 'before',
				)
			);
			$repeater->add_control(
				'update_icons',
				array(
					'label'       => esc_html__('Update Icons', 'gt3_themes_core'),
					'type'        => Controls_Manager::BUTTON,
					'button_type' => 'success',
					'text'        => esc_html__('Update', 'gt3_themes_core'),
					'event'       => 'update_icons',
					'separator'   => 'before',
				)
			);

			$repeater->add_control(
				'modal',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => $this->getRAWCode(),
					'content_classes' => 'modal-wrapper',

				)
			);

			return $repeater->get_controls();
		}

		protected function getRAWCode(){
			return '<div class="modal-content">
				  <div class="modal-header">
					<span class="close">&times;</span>
					<h2>'.esc_html__('Chose icon', 'gt3_themes_core').'</h2>
				  </div>
				  <div class="modal-body">
				  <textarea class="preview_code" rows="10" style="display: none"></textarea><br/>
				  <div class="preview_html" style="min-height: 40px"></div>
				  <div>
					<div style="display: inline-block;" class="icon_preview">

					</div>
					<div style="width: 59%; display: inline-block;">
						<input type="text" class="modal_edit_title" disabled placeholder="'.esc_attr__('Enter your title here', 'gt3_themes_core').'"/><br/>
						<input type="url" class="modal_edit_link" disabled placeholder="'.esc_attr__('Enter your link here', 'gt3_themes_core').'" />
					</div>
					<div style="width: 29%; display: inline-block;">
						<div class="remove_wrapper">
							<input type="button" class="remove_icon" disabled="disabled" value="'.esc_attr__('Remove', 'gt3_themes_core').'" />
						</div>
						<div class="insert_wrapper">
							<input type="button" class="insert_icon" disabled="disabled" value="'.esc_attr__('Insert', 'gt3_themes_core').'" />
						</div>
					</div>
				</div>
				<div class="prev_orig">
				<a href="#" class="social" title="'.esc_attr__('Behance', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Behance.png"  title="'.esc_attr__('Behance', 'gt3_themes_core').'"  alt="'.esc_attr__('Behance', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Deviantart', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Deviantart.png"  title="'.esc_attr__('Deviantart', 'gt3_themes_core').'"  alt="'.esc_attr__('Deviantart', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Dribbble', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Dribbble.png"  title="'.esc_attr__('Dribbble', 'gt3_themes_core').'"  alt="'.esc_attr__('Dribbble', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Facebook', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Facebook.png"  title="'.esc_attr__('Facebook', 'gt3_themes_core').'"  alt="'.esc_attr__('Facebook', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Github', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Github.png"  title="'.esc_attr__('Github', 'gt3_themes_core').'"  alt="'.esc_attr__('Github', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Google', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/GooglePlus.png"  title="'.esc_attr__('Google', 'gt3_themes_core').'"  alt="'.esc_attr__('Google', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Instagram', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Instagram.png"  title="'.esc_attr__('Instagram', 'gt3_themes_core').'"  alt="'.esc_attr__('Instagram', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Linkedin', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Linkedin.png"  title="'.esc_attr__('Linkedin', 'gt3_themes_core').'"  alt="'.esc_attr__('Linkedin', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Pinterest', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Pinterest.png"  title="'.esc_attr__('Pinterest', 'gt3_themes_core').'"  alt="'.esc_attr__('Pinterest', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Reddit', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Reddit.png"  title="'.esc_attr__('Reddit', 'gt3_themes_core').'"  alt="'.esc_attr__('Reddit', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Snapchat', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Snapchat.png"  title="'.esc_attr__('Snapchat', 'gt3_themes_core').'"  alt="'.esc_attr__('Snapchat', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Stumbleupon', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Stumbleupon.png"  title="'.esc_attr__('Stumbleupon', 'gt3_themes_core').'"  alt="'.esc_attr__('Stumbleupon', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Tumblr', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Tumblr.png"  title="'.esc_attr__('Tumblr', 'gt3_themes_core').'"  alt="'.esc_attr__('Tumblr', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Twitter', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Twitter.png"  title="'.esc_attr__('Twitter', 'gt3_themes_core').'"  alt="'.esc_attr__('Twitter', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Vine', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Vine.png"  title="'.esc_attr__('Vine', 'gt3_themes_core').'"  alt="'.esc_attr__('Vine', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('WhatsApp', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/WhatsApp.png"  title="'.esc_attr__('WhatsApp', 'gt3_themes_core').'"  alt="'.esc_attr__('WhatsApp', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('Yelp', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/Yelp.png"  title="'.esc_attr__('Yelp', 'gt3_themes_core').'"  alt="'.esc_attr__('Yelp', 'gt3_themes_core').'" /></a>
				<a href="#" class="social" title="'.esc_attr__('YouTube', 'gt3_themes_core').'"><img src="'.GT3_CORE_WIDGETS_IMG.'icons_circle_color/YouTube.png"  title="'.esc_attr__('YouTube', 'gt3_themes_core').'"  alt="'.esc_attr__('YouTube', 'gt3_themes_core').'" /></a>
				</div>
				  </div>
				  <div class="modal-footer">
				   <div class="modal_save"><input type="button" class="save_button" value="'.esc_attr__('Save', 'gt3_themes_core').'" /> </div>
				  </div>
				</div>';
		}
	}
}











