<?php

namespace ElementPack\Modules\Bbpress\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress (deprecated)', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support'];
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/7vkAHZ778c4';
	// }

	protected function register_controls() {
		$this->start_controls_section(
			'section_bbpress_content',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'bbpress_layout',
			[
				'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'forum-index',
				'options' => [
					'forum-index'  => esc_html__('Forum Index', 'bdthemes-element-pack'),
					'forum-form'   => esc_html__('Forum Form', 'bdthemes-element-pack'),
					'single-forum' => esc_html__('Single Forum', 'bdthemes-element-pack'),
					'topic-index'  => esc_html__('Topic Index', 'bdthemes-element-pack'),
					'topic-form'   => esc_html__('Topic Form', 'bdthemes-element-pack'),
					'single-topic' => esc_html__('Single Topic', 'bdthemes-element-pack'),
					'reply-form'   => esc_html__('Reply Form', 'bdthemes-element-pack'),
					'single-reply' => esc_html__('Single Reply', 'bdthemes-element-pack'),
					'topic-tags'   => esc_html__('Topic Tags', 'bdthemes-element-pack'),
					'single-tag'   => esc_html__('Single Tag', 'bdthemes-element-pack'),
					'single-view'  => esc_html__('Single View', 'bdthemes-element-pack'),
					'stats'        => esc_html__('Stats', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'bbpress_id',
			[
				'label'       => esc_html__('ID', 'bdthemes-element-pack'),
				'description' => esc_html__('Enter your forum ID here, to get this id go to dashboard then go into the forum and open a specific post', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'condition'   => [
					'bbpress_layout' => ['single-forum', 'topic-form', 'single-topic', 'single-reply', 'single-tag', 'single-view']
				],
			]
		);

		$this->end_controls_section();

		//		$this->start_controls_section(
		//			'section_header_style',
		//			[
		//				'label' => esc_html__( 'Style', 'bdthemes-element-pack' ),
		//				'tab'   => Controls_Manager::TAB_STYLE,
		//			]
		//		);
		//
		//		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings   = $this->get_settings_for_display();
		$layout     = ['single-forum', 'single-topic', 'single-reply', 'single-tag', 'single-view'];
		$attributes = [];

		if (in_array($settings['bbpress_layout'], $layout) and isset($settings['bbpress_id'])) {
			$attributes = [' id' => $settings['bbpress_id']];
		} elseif ('topic-form' == $settings['bbpress_layout'] and isset($settings['bbpress_id'])) {
			$attributes = [' forum_id' => $settings['bbpress_id']];
		}

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[bbp-' . $settings['bbpress_layout'] . '%s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {
		echo do_shortcode($this->get_shortcode());
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
