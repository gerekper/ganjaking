<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Steps Widget
 *
 * Porto Elementor widget to display steps, history or timeline
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Steps_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_steps';
	}

	public function get_title() {
		return __( 'Porto Steps', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'step', 'timeline', 'history' );
	}

	public function get_icon() {
		return 'eicon-history';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/steps-widget/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_steps',
			array(
				'label' => __( 'Steps', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Type', 'porto-functionality' ),
				'options' => array(
					'schedule' => __( 'Schedule', 'porto-functionality' ),
					'history'  => __( 'History', 'porto-functionality' ),
					'step'     => __( 'Step', 'porto-functionality' ),
				),
				'default' => 'schedule',
			)
		);

		$this->add_control(
			'title',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Title', 'porto-functionality' ),
				'default'   => __( 'Title', 'porto-functionality' ),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Sub Title', 'porto-functionality' ),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'circle_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Circle Type', 'porto-functionality' ),
				'description' => esc_html__( 'This changes the background color of the wrapper which contains title & sub title.', 'porto-functionality' ),
				'options'     => array(
					'filled' => __( 'Filled', 'porto-functionality' ),
					'simple' => __( 'Simple', 'porto-functionality' ),
				),
				'default'     => 'filled',
				'condition'   => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'is_horizontal',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Is Horizontal?', 'porto-functionality' ),
				'description' => __( 'Default layout is vertical.', 'porto-functionality' ),
				'condition'   => array(
					'type' => 'step',
				),
			)
		);

		$repeater = new Elementor\Repeater();

		$repeater->add_control(
			'subtitle',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Time Text', 'porto-functionality' ),
				'description' => __( 'Please input the text which describes time or current step. This is not working for "Step" type.', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon to display', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'   => __( 'Icon Fonts', 'porto-functionality' ),
					'custom' => __( 'Custom Image Icon', 'porto-functionality' ),
				),
				'default'     => 'icon',
				'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'icon_cl',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
				),
			)
		);
		$repeater->add_control(
			'image_id',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image Icon:', 'porto-functionality' ),
				'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);
		$repeater->add_control(
			'image_url',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'External Image Url', 'porto-functionality' ),
				'condition' => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);
		$repeater->add_control(
			'heading',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Item Title', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'type'  => Controls_Manager::WYSIWYG,
				'label' => __( 'Description', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'shadow',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Shadow', 'porto-functionality' ),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);
		$repeater->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_title_typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Title Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .step-item-title',
			)
		);
		$repeater->add_control(
			'item_title_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-title' => 'color: {{VALUE}} !important;',
				),
			)
		);
		$repeater->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'item_subtitle_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Time Text Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-item-subtitle',
				'condition' => array(
					'type!' => 'step',
				),
			)
		);
		$repeater->add_control(
			'item_subtitle_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Time Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-subtitle' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type!' => 'step',
				),
			)
		);

		$presets = array(
			array(
				'subtitle' => 'Time Text',
			),
		);
		$this->add_control(
			'step_item_list',
			array(
				'label'   => esc_html__( 'Step Items', 'porto-functionality' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => $presets,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_steps_style',
			array(
				'label' => __( 'Steps', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Title Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-title',
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'title_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-title' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'subtitle_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Sub Title Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-subtitle',
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'subtitle_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Sub Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-subtitle' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'line_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Line Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .timeline-balloon:before, .elementor-element-{{ID}} .process-step-circle:before, .elementor-element-{{ID}} .process-step-circle:after, .elementor-element-{{ID}} section.timeline:after' => 'background-color: {{VALUE}}; opacity: 1;',
					'.elementor-element-{{ID}} .process-horizontal .process-step:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line_width',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Line Width (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'.elementor-element-{{ID}}' => '--porto-step-line-width: {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} section.timeline:after' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_steps_item_style',
			array(
				'label' => __( 'Item', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_img_sz',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Image / Icon Size', 'porto-functionality' ),
				'range'     => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 150,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'type' => array( 'schedule' ),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .balloon-content .balloon-photo' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_icon_fs',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Icon Font Size (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'type' => array( 'schedule' ),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .balloon-content .balloon-photo' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_icon_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Icon Text Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .balloon-content .balloon-photo' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'type' => array( 'schedule' ),
				),
			)
		);

		$this->add_control(
			'item_circle_wd',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Circle Size (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'type' => array( 'step' ),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step .process-step-circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .process-step-circle:before, .elementor-element-{{ID}} .process-step-circle:after' => 'left: calc( {{SIZE}}{{UNIT}} / 2 - var(--porto-step-line-width, 2px) / 2 - var(--porto-step-circle-bw, 2px) );',
					'.elementor-element-{{ID}} .process-step-circle:before' => 'height: calc( {{SIZE}}{{UNIT}} - var(--porto-step-circle-bw, 2px) );',
					'.elementor-element-{{ID}} .process-step-circle:after' => 'top: calc( {{SIZE}}{{UNIT}} - var(--porto-step-circle-bw, 2px) );',
					'.elementor-element-{{ID}} .process-horizontal .process-step:before' => 'top: calc( {{SIZE}}{{UNIT}} / 2 - var(--porto-step-line-width, 2px) / 2 );',
				),
			)
		);

		$this->add_control(
			'item_circle_mg',
			array(
				'label'      => esc_html__( 'Circle Margin', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type' => array( 'step' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .process-step-circle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_circle_fs',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Circle Font Size (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'type' => array( 'step' ),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step-circle' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_circle_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Circle Text Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step-circle' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'type' => array( 'step' ),
				),
			)
		);

		$this->add_control(
			'item_circle_bw',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Circle Border Width (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'type' => array( 'step' ),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step-circle' => '--porto-step-circle-bw: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_circle_bc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Circle Border Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step-circle' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'type' => array( 'step' ),
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_title_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Title Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .step-item-title',
			)
		);
		$this->add_control(
			'item_title_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-title' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'item_subtitle_tg',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Time Text Typography', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-item-subtitle',
				'condition' => array(
					'type!' => 'step',
				),
			)
		);
		$this->add_control(
			'item_subtitle_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Time Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-subtitle' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type!' => 'step',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_desc_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Description Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .process-step-desc, .elementor-element-{{ID}} .process-step-desc p, .elementor-element-{{ID}} .timeline-item-content',
			)
		);
		$this->add_control(
			'item_desc_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Description Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .process-step-desc, .elementor-element-{{ID}} .process-step-desc p, .elementor-element-{{ID}} .timeline-item-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_sp_icon_text',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing between Icon & Text', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'condition'  => array(
					'type' => array( 'schedule', 'history' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .balloon-content .balloon-photo' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .timeline .timeline-item-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_sp_title',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing between Title & Sub Title', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .step-item-title, .elementor-element-{{ID}} .timeline .timeline-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->add_control(
			'item_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .timeline-balloon .balloon-content' => 'background-color: {{VALUE}} !important;',
					'.elementor-element-{{ID}} .timeline .timeline-box' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'type' => array( 'schedule', 'history' ),
				),
			)
		);

		$this->add_control(
			'item_pd',
			array(
				'label'      => esc_html__( 'Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .timeline-balloon .balloon-content, .elementor-element-{{ID}} .timeline .timeline-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'type' => array( 'schedule', 'history' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_schedule_timeline_container' ) ) {
			include $template;
		}
	}
}
