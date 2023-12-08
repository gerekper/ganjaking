<?php

namespace ElementPack\Modules\GiveGoal\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Goal extends Module_Base {

	public function get_name() {
		return 'bdt-give-goal';
	}

	public function get_title() {
		return BDTEP . __('Give Goal', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-goal';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall', 'form', 'goal'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-give-goal'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/WdRBJL7fOvk';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'give_login_settings',
			[
				'label' => __('Give Goal', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __('Form ID', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_give_forms_options(),
				'default' => 0
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => __('Show Amount', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'show_bar',
			[
				'label' => __('Show Progress Bar', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_income_style',
			[
				'label' => esc_html__('Income Amount', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'income_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal span.income' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'income_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal span.income' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'income_typography',
				'selector' => '{{WRAPPER}} .bdt-give-goal span.income',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_goal_amount_style',
			[
				'label' => esc_html__('Goal Amount', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'goal_amount_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal .raised' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'goal_amount_typography',
				'selector' => '{{WRAPPER}} .bdt-give-goal .raised',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sectn_style',
			[
				'label' => esc_html__('Progress Bar', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_bar' => 'yes'
				]
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal .give-progress-bar>span' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal .give-progress-bar' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-give-goal .give-progress-bar, {{WRAPPER}} .bdt-give-goal .give-progress-bar>span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal .give-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-goal .give-progress-bar' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}


	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['form_id']) {
			return '<div class="bdt-alert bdt-alert-warning">' . __('Please select a Give Forms From Setting!', 'bdthemes-element-pack') . '</div>';
		}

		$attributes = [
			'id' => $settings['form_id'],
			'show_text' => $settings['show_text'],
			'show_bar' => $settings['show_bar']
		];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_goal %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-goal');

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
