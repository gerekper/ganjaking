<?php

namespace ElementPack\Modules\GiveForm\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Give_Form extends Module_Base {

	public function get_name() {
		return 'bdt-give-form';
	}

	public function get_title() {
		return BDTEP . __('Give Form', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-give-form';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['give', 'charity', 'donation', 'donor', 'history', 'wall', 'form'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-give-form'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/k18Mgivy9Mw';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_give_form',
			[
				'label' => __('Give Form', 'bethemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __('Form ID', 'bethemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_give_forms_options(),
				'default' => 0
			]
		);

		$this->add_control(
			'display_style',
			[
				'label' => __('Form Type', 'bethemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'onpage',
				'options' => [
					'onpage' => __('Full Form', 'bethemes-element-pack'),
					'button' => __('Donate Button', 'bethemes-element-pack'),
					// 'reveal' => __('Reveal', 'bethemes-element-pack'),
					// 'modal' => __('Modal', 'bethemes-element-pack'),
				]
			]
		);

		$this->add_control(
			'continue_button_title',
			[
				'label' => __('Button Text', 'bethemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Continue to Donate', 'bethemes-element-pack'),
				'condition' => [
					'display_style' => 'button',
				]
			]
		);

		// $this->add_control(
		// 	'show_title',
		// 	[
		// 		'label' => __( 'Show Title', 'bethemes-element-pack' ),
		// 		'type' => Controls_Manager::SWITCHER,
		// 		'default' => 'yes'
		// 	]
		// );

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_style' => 'button',
				]
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-give-form .js-give-embed-form-modal-opener:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_full_form',
			[
				'label'     => esc_html__('Note', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_style' => 'onpage',
				]
			]
		);

		$this->add_control(
			'html_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__('Note: This is Iframe based Form So do not possible to custom style. We are sorry for that because it is a third party plugin', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
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
			//'show_title' => ($settings["show_title"] === "yes") ? 'true' : 'false',
			'display_style' => $settings['display_style'],
			'continue_button_title' => $settings['continue_button_title']
		];

		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[give_form %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {

		$this->add_render_attribute('give_wrapper', 'class', 'bdt-give-form');

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
