<?php

namespace ElementPack\Modules\Barcode\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
	exit;
}

class Barcode extends Module_Base {

	public function get_name() {
		return 'bdt-barcode';
	}

	public function get_title() {
		return BDTEP . esc_html__('BarCode', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-barcode';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['bar', 'barcode', 'code', 'qr', 'qrcode'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-barcode'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['jsBarcode', 'ep-scripts'];
        } else {
			return ['jsBarcode', 'ep-barcode'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/PWxNP2zLqDg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_barcode',
			[
				'label' => esc_html__('Barcode', 'bdthemes-element-pack'),
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-bdt-barcode' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ep_barcode_format',
			[
				'label'              => esc_html__('Format', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'code128',
				'render_type'        => 'none',
				'frontend_available' => true,
				'options'            => [
					'code128'    => esc_html__('AUTO', 'bdthemes-element-pack'),
					'EAN13'      => esc_html__('EAN-13', 'bdthemes-element-pack'),
					'EAN8'       => esc_html__('EAN-8', 'bdthemes-element-pack'),
					'EAN5'       => esc_html__('EAN-5', 'bdthemes-element-pack'),
					'EAN2'       => esc_html__('EAN-2', 'bdthemes-element-pack'),
					'UPC'        => esc_html__('UPC', 'bdthemes-element-pack'),
					'pharmacode' => esc_html__('PHARMACODE', 'bdthemes-element-pack'),
					'codabar'    => esc_html__('CODABAR', 'bdthemes-element-pack'),
					'MSI'        => esc_html__('MSI', 'bdthemes-element-pack'),
					'ITF14'      => esc_html__('ITF14', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_control(
			'ep_barcode_content',
			[
				'label'              => esc_html__('Content', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::TEXTAREA,
				'placeholder'        => '1234',
				'default'            => '1234',
				'dynamic'            => ['active' => true],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'ep_barcode_notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(__('This feature will not work if you\'re not given the correct value of selected barcode format. so please make sure you\'re given correct value, For more information please check this <a href="%s" target="_blank">Documentation</a>', 'bdthemes-element-pack'), 'https://bdthemes.com/knowledge-base/how-to-use-barcode-widget'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);
		$this->add_control(
			'ep_barcode_show_label',
			[
				'label'              => __('Show Label', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __('Show', 'bdthemes-element-pack'),
				'label_off'          => __('Hide', 'bdthemes-element-pack'),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'ep_barcode_override_label',
			[
				'label'              => __('Override Label', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __('Yes', 'bdthemes-element-pack'),
				'label_off'          => __('No', 'bdthemes-element-pack'),
				'return_value'       => 'yes',
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'ep_barcode_show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'ep_barcode_label_text',
			[
				'label'              => esc_html__('Label Text', 'bdthemes-element-pack'),
				'label_block'        => true,
				'description'        => esc_html__('Overide the text that is diplayed', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => ['active' => true],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [
					'ep_barcode_override_label' => 'yes'
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_barcode',
			[
				'label' => esc_html__('Barcode', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ep_barcode_line_color',
			[
				'label'   => esc_html__('Line Color', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				// 'render_type'        => 'none',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-barcode svg rect' => 'fill: {{VALUE}} !important',
				],
			]
		);
		$this->add_control(
			'ep_barcode_background',
			[
				'label'   => esc_html__('Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-barcode' => 'background: {{VALUE}} !important',
				],
				// 'render_type'        => 'none',
				// 'frontend_available' => true,
			]
		);
		$this->add_control(
			'ep_barcode_width',
			[
				'label'              => esc_html__('Width', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'description'        => esc_html__('The width option is the width of a single bar.', 'bdthemes-element-pack'),
				'render_type'        => 'none',
				'frontend_available' => true,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
			]
		);
		$this->add_control(
			'ep_barcode_height',
			[
				'label'              => esc_html__('Height', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'description'        => esc_html__('The height of the barcode.', 'bdthemes-element-pack'),
				'render_type'        => 'none',
				'frontend_available' => true,

			]
		);

		$this->add_responsive_control(
			'ep_barcode_padding',
			[
				'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'selectors'             => [
					'{{WRAPPER}} .bdt-ep-barcode'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'layout_section_label',
			[
				'label'     => __('Label', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ep_barcode_show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'ep_barcode_label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-barcode svg text' => 'fill: {{VALUE}} !important',
				],
			]
		);
		$this->add_responsive_control(
			'ep_barcode_label_position',
			[
				'label'              => esc_html__('Position', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => [
					'top'    => [
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => ' eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'ep_barcode_label_alignment',
			[
				'label'              => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'ep_barcode_font_width',
			[
				'label'              => __('Font Width', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					''            => __('Normal', 'bdthemes-element-pack'),
					'bold'        => __('Bold', 'bdthemes-element-pack'),
					'italic'      => __('Italic', 'bdthemes-element-pack'),
					'italic bold' => __('Bold & Italic', 'bdthemes-element-pack'),
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'ep_barcode_label_spacing',
			[
				'label'              => __('Space Between', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'render_type'        => 'none',
				'frontend_available' => true,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 15,
						'step' => 0.1,
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() { ?>
		<div class="bdt-ep-barcode">
			<svg id="bdt-ep-barcode-<?php echo $this->get_id(); ?>" class="bdt-ep-barcode-content"></svg>
		</div>
		<?php
	}
}
