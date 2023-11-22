<?php

namespace ElementPack\Modules\GiveTotals\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Totals extends Module_Base {

	public function get_name() {
		return 'bdt-give-totals';
	}

	public function get_title() {
		return BDTEP . __('Give Totals', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-totals';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall', 'form', 'goal', 'totals'];
	}

	// public function get_style_depends() {
	// 	if ($this->ep_is_edit_mode()) {
	// 		return ['ep-styles'];
	// 	} else {
	// 		return ['ep-give-totals'];
	// 	}
	// }

	public function get_custom_help_url() {
		return 'https://youtu.be/fZMljNFdvKs';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'give_totals_settings',
			[
				'label' => __('Give Totals', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'forms',
			[
				'label' => __('Forms', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT2,
				'options' => element_pack_give_forms_options(),
				'multiple' => true,
				'label_block' => true
			]
		);

		$this->add_control(
			'total_goal',
			[
				'label' => __('Goal Amount', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => '1000'
			]
		);

		$this->add_control(
			'message',
			[
				'label' => __('Message:', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('Hey! We\'ve raised {total} of the {total_goal} we are trying to raise for this campaign!', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'show_external' => false,
				'default' => [
					'url' => 'https://example.org',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->add_control(
			'link_text',
			[
				'label' => __('Link Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Donate Now', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_progress',
			[
				'label' => __('Show Progress', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Raised Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_progress' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .raised' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .raised' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-give-totals .raised',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'progress_bar_style',
			[
				'label' => esc_html__('Progress Bar', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_progress' => 'yes'
				]
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-progress-bar>span' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-progress-bar, {{WRAPPER}} .bdt-give-totals .give-progress-bar>span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-progress-bar' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_message_style',
			[
				'label' => esc_html__('Message', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'message!' => ''
				]
			]
		);

		$this->add_control(
			'message_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-totals .give-totals-shortcode-wrap' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'selector' => '{{WRAPPER}} .bdt-give-totals .give-totals-shortcode-wrap',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_link_style',
			[
				'label' => esc_html__('Link', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.give-totals-text-link' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.give-totals-text-link:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'link_margin',
			[
				'label' => __('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} a.give-totals-text-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'link_typography',
				'selector' => '{{WRAPPER}} a.give-totals-text-link',
			]
		);

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['forms']) {
			return '<div class="bdt-alert bdt-alert-warning">' . __('Please select a Give Forms From Setting!', 'bdthemes-element-pack') . '</div>';
		}

		$attributes = [
			'ids' => $settings['forms'],
			'total_goal' => $settings['total_goal'],
			'message' => esc_html($settings['message']),
			'link' => esc_url($settings['link']['url']),
			'link_text' => esc_html($settings['link_text']),
			'progress_bar' => $settings['show_progress'],
		];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_totals %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-totals');

?>

		<div <?php echo $this->get_render_attribute_string('give_wrapper'); ?>>

			<?php echo do_shortcode($this->get_shortcode()); ?>

		</div>

<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
