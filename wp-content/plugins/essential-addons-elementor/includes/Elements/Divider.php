<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Divider Widget
 */
class Divider extends Widget_Base
{

	/**
	 * Retrieve divider widget name.
	 */
	public function get_name()
	{
		return 'eael-divider';
	}

	/**
	 * Retrieve divider widget title.
	 */
	public function get_title()
	{
		return __('Divider', 'essential-addons-elementor');
	}

	/**
	 * Retrieve the list of categories the divider widget belongs to.
	 */
	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'ea divider',
			'separator',
			'ea separator',
			'line',
			'split',
			'splitter',
			'ea',
			'essential addons'
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/divider/';
	}

	/**
	 * Retrieve divider widget icon.
	 */
	public function get_icon()
	{
		return 'eaicon-divider';
	}

	/**
	 * Register divider widget controls.
	 */
	protected function register_controls()
	{

		/*-----------------------------------------------------------------------------------*/
		/*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Divider
		 */
		$this->start_controls_section(
			'section_buton',
			[
				'label'                 => __('Divider', 'essential-addons-elementor'),
			]
		);


		$this->add_control(
			'divider_type',
			[
				'label'                 => esc_html__('Choose Layout', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'plain'        => [
						'title'    => esc_html__('Plain', 'essential-addons-elementor'),
						'icon'     => 'fa fa-ellipsis-h',
					],
					'text'         => [
						'title'    => esc_html__('Text', 'essential-addons-elementor'),
						'icon'     => 'eicon-text',
					],
					'icon'         => [
						'title'    => esc_html__('Icon', 'essential-addons-elementor'),
						'icon'     => 'fa fa-certificate',
					],
					'image'        => [
						'title'    => esc_html__('Image', 'essential-addons-elementor'),
						'icon'     => 'eicon-image-bold',
					],
				],
				'default'               => 'plain',
			]
		);

		$this->add_control(
			'divider_direction',
			[
				'label'                 => __('Direction', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'horizontal',
				'options'               => [
					'horizontal'     => __('Horizontal', 'essential-addons-elementor'),
					'vertical'       => __('Vertical', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'divider_position',
			[
				'label' => __('Divider Position', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex' => [
						'title' => __('Inline', 'essential-addons-elementor'),
						'icon' => 'fas fa-ellipsis-v',
					],
					'block' => [
						'title' => __('Block', 'essential-addons-elementor'),
						'icon' => 'fas fa-bars',
					],
				],
				'toggle' => false,
				'default' => 'flex',
				'selectors'             => [
					'{{WRAPPER}} .divider-text-wrap'   => 'display: {{VALUE}}; justify-content: center;',
				],
				'condition' => [
					'divider_direction'	=> 'horizontal',
					'divider_type!'		=> 'plain'
				]
			]
		);

		$this->add_control(
			'divider_left_switch',
			[
				'label' => __('Show Left Divider', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'essential-addons-elementor'),
				'label_off' => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'divider_type!'		=> 'plain'
				]
			]
		);
		$this->add_control(
			'divider_left_width',
			[
				'label' => __('Left Divider Width', 'plugin-domain'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .divider-text-wrap .divider-border-left .divider-border' => 'width: {{SIZE}}{{UNIT}}; margin: auto;',
				],
				'condition'	=> [
					'divider_left_switch'	=> 'yes',
					'divider_direction'	=> 'horizontal',
					'divider_type!'		=> 'plain'
				]
			]
		);
		$this->add_control(
			'divider_right_switch',
			[
				'label' => __('Show Right Divider', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'essential-addons-elementor'),
				'label_off' => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'divider_type!'		=> 'plain'
				]
			]
		);
		$this->add_control(
			'divider_right_width',
			[
				'label' => __('Right Divider Width', 'plugin-domain'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .divider-text-wrap .divider-border-right .divider-border' => 'width: {{SIZE}}{{UNIT}}; margin: auto;',
				],
				'condition'	=> [
					'divider_right_switch'	=> 'yes',
					'divider_direction'	=> 'horizontal',
					'divider_type!'		=> 'plain'
				]
			]
		);

        $this->add_control(
            'divider_text',
            [
                'label'                 => __( 'Text', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'               => [ 'active' => true ],
                'default'               => __( 'Divider Text', 'essential-addons-elementor' ),
				'condition'             => [
					'divider_type'    => 'text',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'divider_icon_new',
			[
				'label'                 => __('Icon', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility' 		=> 'divider_icon',
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'condition'             => [
					'divider_type'    => 'icon',
				],
			]
		);

		$this->add_control(
			'text_html_tag',
			[
				'label'                 => __('HTML Tag', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'span',
				'options'               => [
					'h1'            => __('H1', 'essential-addons-elementor'),
					'h2'            => __('H2', 'essential-addons-elementor'),
					'h3'            => __('H3', 'essential-addons-elementor'),
					'h4'            => __('H4', 'essential-addons-elementor'),
					'h5'            => __('H5', 'essential-addons-elementor'),
					'h6'            => __('H6', 'essential-addons-elementor'),
					'div'           => __('div', 'essential-addons-elementor'),
					'span'          => __('span', 'essential-addons-elementor'),
					'p'             => __('p', 'essential-addons-elementor'),
				],
				'condition'             => [
					'divider_type'    => 'text',
				],
			]
		);

		$this->add_control(
			'divider_image',
			[
				'label'                 => __('Image', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::MEDIA,
				'default'               => [
					'url'           => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'divider_type'    => 'image',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                 => __('Alignment', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'center',
				'options'               => [
					'left'          => [
						'title'     => __('Left', 'essential-addons-elementor'),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __('Center', 'essential-addons-elementor'),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __('Right', 'essential-addons-elementor'),
						'icon'      => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}}'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Divider
		 */
		$this->start_controls_section(
			'section_divider_style',
			[
				'label'                 => __('Divider', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'divider_vertical_align',
			[
				'label'                 => __('Vertical Alignment', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __('Top', 'essential-addons-elementor'),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __('Center', 'essential-addons-elementor'),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __('Bottom', 'essential-addons-elementor'),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .divider-text-wrap'   => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'condition'             => [
					'divider_direction'    => 'horizontal',
					'divider_type!'   => 'plain'
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'                 => __('Style', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'dashed',
				'options'               => [
					'solid'          => __('Solid', 'essential-addons-elementor'),
					'dashed'         => __('Dashed', 'essential-addons-elementor'),
					'dotted'         => __('Dotted', 'essential-addons-elementor'),
					'double'         => __('Double', 'essential-addons-elementor'),
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider, {{WRAPPER}} .divider-border' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'horizontal_height',
			[
				'label'                 => __('Height', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px'       => [
						'min'  => 1,
						'max'  => 60,
					],
				],
				'default'               => [
					'size'     => 3,
					'unit'     => 'px',
				],
				'tablet_default'    => [
					'unit'     => 'px',
				],
				'mobile_default'    => [
					'unit'     => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider.horizontal' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .divider-border' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'divider_direction'    => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_height',
			[
				'label'                 => __('Height', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px'           => [
						'min'      => 1,
						'max'      => 500,
					],
				],
				'default'               => [
					'size'         => 80,
					'unit'         => 'px',
				],
				'tablet_default'   => [
					'unit'         => 'px',
				],
				'mobile_default'   => [
					'unit'         => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider-wrap.divider-direction-vertical .divider-border' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-divider-wrap.divider-direction-vertical .eael-divider.vertical' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition'             => [
					'divider_direction'    => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'horizontal_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px'           => [
						'min'      => 1,
						'max'      => 1200,
					],
				],
				'default'              => [
					'size'         => 300,
					'unit'         => 'px',
				],
				'tablet_default'   => [
					'unit'         => 'px',
				],
				'mobile_default'   => [
					'unit'         => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider.horizontal' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .divider-text-container' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'divider_direction'    => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px'           => [
						'min'      => 1,
						'max'      => 100,
					],
				],
				'default'               => [
					'size'         => 3,
					'unit'         => 'px',
				],
				'tablet_default'   => [
					'unit'         => 'px',
				],
				'mobile_default'   => [
					'unit'         => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider-wrap.divider-direction-vertical .divider-border' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-divider-wrap.divider-direction-vertical .eael-divider.vertical' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'divider_direction'    => 'vertical',
				],
			]
		);

		$this->add_control(
			'divider_border_color',
			[
				'label'                 => __('Divider Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .eael-divider, {{WRAPPER}} .divider-border' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'divider_type'    => 'plain',
				],
			]
		);

		$this->start_controls_tabs('tabs_before_after_style');

		$this->start_controls_tab(
			'tab_before_style',
			[
				'label'                 => __('Before', 'essential-addons-elementor'),
				'condition'             => [
					'divider_type!'   => 'plain',
				],
			]
		);

		$this->add_control(
			'divider_before_color',
			[
				'label'                 => __('Divider Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'condition'             => [
					'divider_type!'   => 'plain',
				],
				'selectors'             => [
					'{{WRAPPER}} .divider-border-left .divider-border' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_after_style',
			[
				'label'                 => __('After', 'essential-addons-elementor'),
				'condition'             => [
					'divider_type!'   => 'plain',
				],
			]
		);

		$this->add_control(
			'divider_after_color',
			[
				'label'                 => __('Divider Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'condition'             => [
					'divider_type!'   => 'plain',
				],
				'selectors'             => [
					'{{WRAPPER}} .divider-border-right .divider-border' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Text
		 */
		$this->start_controls_section(
			'section_text_style',
			[
				'label'                 => __('Text', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'divider_type'    => 'text',
				],
			]
		);

		$this->add_control(
			'text_position',
			[
				'label'                 => __('Position', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'         => [
						'title'    => __('Left', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-left',
					],
					'center'       => [
						'title'    => __('Center', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-center',
					],
					'right'        => [
						'title'    => __('Right', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-right',
					],
				],
				'default'               => 'center',
				'prefix_class'		    => 'eael-divider-'
			]
		);

		$this->add_control(
			'divider_text_color',
			[
				'label'                 => __('Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'condition'             => [
					'divider_type'    => 'text',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'typography',
				'label'                 => __('Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'selector'              => '{{WRAPPER}} .eael-divider-text',
				'condition'             => [
					'divider_type'    => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'                  => 'divider_text_shadow',
				'selector'              => '{{WRAPPER}} .eael-divider-text',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label'                 => __('Spacing', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 200,
					],
				],
				'condition'             => [
					'divider_type'    => 'text',
				],
				'selectors'             => [
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'                 => __('Icon', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'divider_type'    => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'                 => __('Position', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'         => [
						'title'    => __('Left', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-left',
					],
					'center'       => [
						'title'    => __('Center', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-center',
					],
					'right'        => [
						'title'    => __('Right', 'essential-addons-elementor'),
						'icon'     => 'eicon-h-align-right',
					],
				],
				'default'               => 'center',
				'prefix_class'		    => 'eael-divider-'
			]
		);

		$this->add_control(
			'divider_icon_color',
			[
				'label'                 => __('Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'condition'             => [
					'divider_type'    => 'icon',
				],
				'selectors'             => [
                    '{{WRAPPER}} .eael-divider-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-divider-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'                 => __('Size', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'default'               => [
					'size' => 16,
					'unit' => 'px',
				],
				'condition'             => [
					'divider_type'    => 'icon',
				],
				'selectors'             => [
                    '{{WRAPPER}} .eael-divider-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-divider-icon svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-divider-svg-icon'	=> 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_rotation',
			[
				'label'                 => __('Icon Rotation', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 360,
					],
				],
				'default'               => [
					'unit' => 'px',
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'selectors'             => [
                    '{{WRAPPER}} .eael-divider-icon span' => 'transform: rotate( {{SIZE}}deg );',
                    '{{WRAPPER}} .eael-divider-icon svg' => 'transform: rotate( {{SIZE}}deg );',
					'{{WRAPPER}} .eael-divider-svg-icon'	=> 'transform: rotate( {{SIZE}}deg );'
				],
				'condition'             => [
					'divider_type'    => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'                 => __('Spacing', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 200,
					],
				],
				'condition'             => [
					'divider_type'    => 'icon',
				],
				'selectors'             => [
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_image_style',
			[
				'label'                 => __('Image', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'divider_type'    => 'image',
				],
			]
		);

		$this->add_control(
			'image_position',
			[
				'label'                 => __('Position', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-h-align-left',
					],
					'center'    => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-h-align-center',
					],
					'right'     => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'               => 'center',
				'prefix_class'		    => 'eael-divider-'
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 1200,
					],
				],
				'default'               => [
					'size' => 80,
					'unit' => 'px',
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'condition'             => [
					'divider_type'    => 'image',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'                 => __('Border Radius', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%'],
				'condition'             => [
					'divider_type'    => 'image',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-divider-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'                 => __('Spacing', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => ['%', 'px'],
				'range'                 => [
					'px' => [
						'max' => 200,
					],
				],
				'condition'             => [
					'divider_type'    => 'image',
				],
				'selectors'             => [
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-horizontal .eael-divider-content' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-center .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-left .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.eael-divider-right .eael-divider-wrap.divider-direction-vertical .eael-divider-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render divider widget output on the frontend.
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
		$icon_migrated = isset($settings['__fa4_migrated']['divider_icon_new']);
		$icon_is_new = empty($settings['divider_icon']);

		$this->add_render_attribute('divider', 'class', 'eael-divider');

		if ($settings['divider_direction']) {
			$this->add_render_attribute( 'divider', 'class', esc_attr( $settings['divider_direction'] ) );
		}

		if ($settings['divider_style']) {
			$this->add_render_attribute('divider', 'class', esc_attr( $settings['divider_style'] ) );
		}

	    $this->add_render_attribute( 'divider-content', 'class', 'eael-divider-' . esc_attr( $settings['divider_type'] ) );

		$this->add_inline_editing_attributes('divider_text', 'none');
	    $this->add_render_attribute( 'divider_text', 'class', 'eael-divider-' . esc_attr( $settings['divider_type'] ) );
		$this->add_render_attribute(
			'divider-wrap',
			[
				'class'	=> [
					'eael-divider-wrap',
					"divider-direction-" . esc_attr( $settings['divider_direction'] )
				]
			]
		);

?>
		<div <?php echo $this->get_render_attribute_string('divider-wrap'); ?>>
			<?php
			if ($settings['divider_type'] == 'plain') { ?>
				<div <?php echo $this->get_render_attribute_string('divider'); ?>></div>
			<?php
			} else { ?>
				<div class="divider-text-container">
					<div class="divider-text-wrap">
						<?php
						if ($settings['divider_left_switch'] == 'yes') :
						?>
							<span class="divider-border-wrap divider-border-left">
								<span class="divider-border"></span>
							</span>
						<?php endif; ?>
						<span class="eael-divider-content">
							<?php if ($settings['divider_type'] == 'text' && $settings['divider_text']) { ?>
								<?php
								printf( '<%1$s %2$s>%3$s</%1$s>', $settings['text_html_tag'], $this->get_render_attribute_string( 'divider_text' ), esc_html( $settings['divider_text'] ) );
								?>
							<?php } elseif ($settings['divider_type'] == 'icon') { ?>
								<span <?php echo $this->get_render_attribute_string('divider-content'); ?>>
									<?php if ($icon_migrated || $icon_is_new) { ?>
										<?php if (isset($settings['divider_icon_new']['value']['url'])) : ?>
											<img class="eael-divider-svg-icon" src="<?php echo esc_url( $settings['divider_icon_new']['value']['url'] ); ?>" alt="<?php echo esc_attr( get_post_meta( $settings['divider_icon_new']['value']['id'], '_wp_attachment_image_alt', true ) ); ?>"/>
										<?php else :
                                            Icons_Manager::render_icon($settings['divider_icon_new'], ['aria-hidden'=>'false']);
                                        endif; ?>
									<?php } else { ?>
										<span class="<?php echo esc_attr($settings['divider_icon']); ?>" aria-hidden="true"></span>
									<?php } ?>
								</span>
							<?php } elseif ($settings['divider_type'] == 'image') { ?>
								<span <?php echo $this->get_render_attribute_string('divider-content'); ?>>
									<?php
									if (isset($settings['divider_image']['url'])) { ?>
										<img src="<?php echo esc_url($settings['divider_image']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['divider_image']['id'], '_wp_attachment_image_alt', true)); ?>">
									<?php } ?>
								</span>
							<?php } ?>
						</span>
						<?php
						if ($settings['divider_right_switch'] == 'yes') :
						?>
							<span class="divider-border-wrap divider-border-right">
								<span class="divider-border"></span>
							</span>
						<?php endif; ?>
					</div>
				</div>
			<?php
			}
			?>
		</div>
<?php
	}

	/**
	 * Render divider widget output in the editor.
	 */
	protected function content_template()
	{
	}
}
