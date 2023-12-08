<?php

namespace ElementPack\Modules\AgeGate\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Age_Gate extends Module_Base {
	public function get_name() {
		return 'bdt-age-gate';
	}

	public function get_title() {
		return BDTEP . esc_html__('Age Gate', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-age-gate';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['age-gate', 'lightbox', 'popup'];
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-age-gate'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/I32wKLfNIes';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_modal_form',
			[
				'label' => esc_html__('Modal Form', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'required_age',
			[
				'label'       => esc_html__('Required Minimum Age', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'dynamic'     => ['active' => true],
				'default'     => 18,
				'placeholder' => esc_html__('Minimum Age', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'age_input_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__('Note: example - 18. That means the user can view this webpage only if he is at least 18 years old.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'form_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'center',
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_control(
			'form_placeholder',
			[
				'label'     => esc_html__('Form Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Your Age', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__('Submit', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'redirect_link',
			[
				'label'     => esc_html__('Redirect URL', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::URL,
				// 'default'	=> get_home_url()
			]
		);

		$this->add_control(
			'redirect_link_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__('Note: If the condition not match with user age, then it will redirect them to this link.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->add_control(
			'age_invalid_msg',
			[
				'label'       => esc_html__('Age Invalid Message', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__('Sorry, your entered age is not suitable for our condition.', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your message', 'bdthemes-element-pack'),
				'dynamic'     => ['active' => true],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_modal_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'modal_width',
			[
				'label' => esc_html__('Modal Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 320,
						'max' => 1200,
					],
				],
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'display_times_expire',
			[
				'label' => esc_html__('Times Expiry (Hour)', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__('Default 72 hours.', 'bdthemes-element-pack'),
				'default' => 72,
			]
		);

		$this->add_control(
			'hr_one',
			[
				'type'        => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_modal_header',
			[
				'label'       => esc_html__('Show Modal Header', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			]
		);

		$this->add_control(
			'show_modal_footer',
			[
				'label'       => esc_html__('Show Modal Footer', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_modal_header',
			[
				'label' => esc_html__('Modal Header', 'bdthemes-element-pack'),
				'condition' => [
					'show_modal_header' => 'yes'
				]
			]
		);

		$this->add_control(
			'header',
			[
				'label'       => esc_html__('Header Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('This is your modal header title', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Modal header title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'header_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_modal',
			[
				'label' => esc_html__('Modal Content', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'content',
			[
				'label'       => esc_html__('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => ['active' => true],
				'show_label'  => false,
				'default'     => esc_html__('You can view this webpage only if you are at least 18 years old.', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Modal content goes here', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'center',
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_modal_footer',
			[
				'label' => esc_html__('Modal Footer', 'bdthemes-element-pack'),
				'condition' => [
					'show_modal_footer' => 'yes'
				]
			]
		);

		$this->add_control(
			'footer',
			[
				'label'       => esc_html__('Footer Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Modal footer goes here', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Modal footer goes here', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'footer_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_modal_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button',
			[
				'label'       => esc_html__('Modal Close Button', 'bdthemes-element-pack'),
				'description' => esc_html__('When you set modal full screen make sure you don\'t set colse button outside', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default'    => esc_html__('Default', 'bdthemes-element-pack'),
					'outside'    => esc_html__('Outside', 'bdthemes-element-pack'),
					'none'       => esc_html__('No Close Button', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'close_btn_delay_show',
			[
				'label'       => esc_html__('Close Button Delay Show', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'close_btn_delay_time',
			[
				'label'   => esc_html__('Close Button Delay Time(sec)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 60,
					],
				],
				'condition' => [
					'close_btn_delay_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'modal_center',
			[
				'label'        => esc_html__('Center Position', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'tab_content_close_button',
			[
				'label'     => esc_html__('Modal Close Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'close_button!' => 'none',
				],
			]
		);

		$this->start_controls_tabs('tabs_close_button_style');

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_backgroun_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'close_button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close',
			]
		);

		$this->add_responsive_control(
			'close_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_backgroun_hover',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'close_button_border_border!' => '',
				],
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog button.bdt-close:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'tab_content_header',
			[
				'label'     => esc_html__('Modal Header', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'header!' => '',
					'show_modal_header' => 'yes'
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'header_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-header',
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'header_box_shadow',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-header',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_modal',
			[
				'label' => esc_html__('Modal Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-body .modal-body-info-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-body, .bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-header' => 'border-top-right-radius: {{TOP}}{{UNIT}}; border-top-left-radius: {{RIGHT}}{{UNIT}};',
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer' => 'border-bottom-right-radius: {{TOP}}{{UNIT}}; border-bottom-left-radius: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-body .modal-body-info-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			[
				'label' => esc_html__('Fields', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'field_width',
			[
				'label' => esc_html__('Width', 'elementor'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min'  => 80,
						'max'  => 200,
					],
				],
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_spacing',
			[
				'label' => esc_html__('Spacing', 'elementor'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_field_style');

		$this->start_controls_tab(
			'tab_field_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-input',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-input',
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-input',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_field_focus',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_focus_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_focus_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-input:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'field_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'separator' => 'before',
				'selector'  => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-button'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-button',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'separator' => 'before',
				'selector'  => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-button:hover'
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'invalid_msg_style',
			[
				'label'     => esc_html__('Invalid Message', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'age_invalid_msg!' => '',
				],
			]
		);

		$this->add_control(
			'invalid_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .modal-msg-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'invalid_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .modal-msg-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'invalid_text_typography',
				'selector' => '{{WRAPPER}} .modal-msg-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tab_content_footer',
			[
				'label'     => esc_html__('Modal Footer', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'footer!' => '',
					'show_modal_footer' => 'yes'
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'footer_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'footer_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer',
			]
		);

		$this->add_responsive_control(
			'footer_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-dialog' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'footer_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'footer_box_shadow',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '.bdt-age-gate-{{ID}}.bdt-modal .bdt-modal-footer',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'  => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Overlay Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => esc_html__('Opacity', 'elementor'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'.bdt-age-gate-{{ID}}.bdt-modal' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings  = $this->get_settings_for_display();
		$id        = 'bdt-age-gate-' . $this->get_id();
		$edit_mode = Element_Pack_Loader::elementor()->editor->is_edit_mode();

		$this->add_render_attribute('button', 'class', ['bdt-modal-button', 'elementor-button']);

		$this->add_render_attribute('modal', 'id', $id);
		$this->add_render_attribute('modal', 'class', 'bdt-age-gate-' . $this->get_id());
		$this->add_render_attribute('modal', 'data-bdt-modal', 'bg-close: false');

		$this->add_render_attribute('modal', 'class', 'bdt-age-gate bdt-modal');

		if ($settings['modal_center'] === 'yes') {
			$this->add_render_attribute('modal', 'class', 'bdt-flex-top');
		}

		$this->add_render_attribute('modal-dialog', 'class', 'bdt-modal-dialog');

		if ($settings['modal_center'] === 'yes') {
			$this->add_render_attribute('modal-dialog', 'class', 'bdt-margin-auto-vertical');
		}

		$this->add_render_attribute('modal-body', 'class', 'bdt-modal-body');
		// $this->add_render_attribute('modal-body', 'class', 'bdt-text-' . esc_attr($settings['content_align']));
		$this->add_render_attribute('modal-body-info-text', 'class', 'modal-body-info-text bdt-text-' . esc_attr($settings['content_align']));

		$this->add_render_attribute('age-gate-form', 'class', 'bdt-age-gate-form bdt-text-' . esc_attr($settings['form_align']));

		$this->add_render_attribute('button', 'class', 'bdt-button bdt-button-default bdt-age-submit');
		if ($settings['button_hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		$this->add_render_attribute('modal-msg-text', 'class', 'modal-msg-text bdt-margin-top bdt-text-warning bdt-hidden bdt-text-' . esc_attr($settings['content_align']));

		$this->add_render_attribute(
			[
				'modal' => [
					'data-settings' => [
						wp_json_encode([
							"widgetId"		  =>  $id,
							"closeBtnDelayShow"     => ("yes" == $settings["close_btn_delay_show"]) ? true : false,
							"delayTime"       => isset($settings["close_btn_delay_time"]['size']) ? $settings["close_btn_delay_time"]['size'] * 1000 : false,
							"displayTimesExpire" => isset($settings['display_times_expire']) && !empty($settings['display_times_expire']) ? (int) $settings['display_times_expire'] : 24,
							'requiredAge' => $settings['required_age'],
							'redirect_link' => !empty($settings['redirect_link']['url']) ? $settings['redirect_link']['url'] : false,
						])
					]
				]
			]
		);

?>
		<div class="bdt-modal-wrapper">

			<div <?php echo $this->get_render_attribute_string('modal'); ?>>
				<div <?php echo $this->get_render_attribute_string('modal-dialog'); ?>>

					<?php if ($settings['close_button'] != 'none') : ?>
						<button class="bdt-modal-close-<?php echo esc_attr($settings['close_button']); ?> elementor-animation-<?php echo esc_attr($settings['close_button_hover_animation']); ?>" id="bdt-modal-close-button" type="button" data-bdt-close></button>
					<?php endif; ?>

					<?php if ($settings['header'] and $settings['show_modal_header']) : ?>
						<div class="bdt-modal-header bdt-text-<?php echo esc_attr($settings['header_align']); ?>">
							<h3 class="bdt-modal-title"><?php echo wp_kses_post($settings['header']); ?></h3>
						</div>
					<?php endif; ?>

					<div <?php echo $this->get_render_attribute_string('modal-body'); ?>>
						<div <?php echo $this->get_render_attribute_string('modal-body-info-text'); ?>>
							<?php
							echo $this->parse_text_editor($settings['content']);
							?>
						</div>
						<div <?php echo $this->get_render_attribute_string('age-gate-form'); ?>>
							<div class="bdt-margin-top">
								<input class="bdt-input bdt-form-width-small bdt-age-input" type="number" placeholder="<?php echo $settings['form_placeholder']; ?>">
								<button <?php echo $this->get_render_attribute_string('button'); ?>>
									<?php echo esc_html($settings['button_text']); ?>
								</button>
							</div>
						</div>
						<div <?php echo $this->get_render_attribute_string('modal-msg-text'); ?>>
							<?php
							echo wp_kses_post($settings['age_invalid_msg']);
							?>
						</div>
					</div>

					<?php if ($settings['footer'] and $settings['show_modal_footer']) : ?>
						<div class="bdt-modal-footer bdt-text-<?php echo esc_attr($settings['footer_align']); ?>">
							<?php echo wp_kses_post($settings['footer']); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

<?php
	}
}
