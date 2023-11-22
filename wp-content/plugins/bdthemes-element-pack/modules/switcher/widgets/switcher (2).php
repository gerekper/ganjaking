<?php

namespace ElementPack\Modules\Switcher\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Switcher extends Module_Base
{

	public function get_name()
	{
		return 'bdt-switcher';
	}

	public function get_title()
	{
		return BDTEP . esc_html__('Switcher', 'bdthemes-element-pack');
	}

	public function get_icon()
	{
		return 'bdt-wi-switcher';
	}

	public function get_categories()
	{
		return ['element-pack'];
	}

	public function get_keywords()
	{
		return ['switcher', 'tab', 'toggle'];
	}

	public function get_style_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-switcher'];
		}
	}
	public function get_script_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-switcher'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/BIEFRxDF1UE';
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_switcher_a_layout',
			[
				'label' => __('Switch A', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_a_title',
			[
				'label'   => __('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Switch A', 'bdthemes-element-pack'),
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'switch_a_select_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'switch_a_icon',
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$this->add_control(
			'source_a',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'    => esc_html__('Custom Content', 'bdthemes-element-pack'),
					'elementor' => esc_html__('Elementor Template', 'bdthemes-element-pack'),
					'anywhere'  => esc_html__('AE Template', 'bdthemes-element-pack'),
					'custom_section'  => esc_html__('Link Section', 'bdthemes-element-pack'),
					'link_widget'  => esc_html__('Link Widget', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'source_a_link_widget',
			[
				'label'   => __('Link Widget ID', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'condition'   => ['source_a' => "link_widget"],
			]
		);

		$this->add_control(
			'source_a_link_widget_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Note: Please insert two widgets on the same section then place your widgets ID here. Don\'t need to add # before ID. You must use Link widget option on both switcher. This result will visible on the Frontend.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'   => ['source_a' => "link_widget"],
			]
		);

		$this->add_control(
			'template_id_a',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => ['source_a' => "elementor"],
			]
		);
		$this->add_control(
			'anywhere_id_a',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'anywhere_template',
				],
				'condition'   => ['source_a' => "anywhere"],
			]
		);


		$this->add_control(
			'switch_a_content',
			[
				'label'      => __('Content', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => __('Switch Content A', 'bdthemes-element-pack'),
				'show_label' => false,
				'condition'  => ['source_a' => 'custom'],
			]
		);

		$this->add_control(
			'switch_a_custom_section_id',
			[
				'label'       => __('Section ID', 'bdthemes-element-pack'),
				'description' => __('Paste your section ID here. Don\'t need to add # before ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'section-a',
				'dynamic'     => ['active' => true],
				'condition'  => ['source_a' => 'custom_section'],
			]
		);

		$this->add_control(
			'show_switch_a_badge',
			[
				'label'        => __('Show Badge', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'switch_a_badge',
			[
				'label'   => __('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Hot', 'bdthemes-element-pack'),
				'dynamic' => ['active' => true],
				'condition' => [
					'show_switch_a_badge'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_a_trigger',
			[
				'label'        => __('Trigger Dynamically', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'separator'	   => 'before',
			]
		);

		$this->add_control(
			'switch_a_trigger_selector',
			[
				'label'   => __('Trigger Selector', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'condition' => [
					'switch_a_trigger'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_a_trigger_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('If you want to Trigger this switch Dynamically from anywhere else, please enter the id or class of the target element . Example. #btn-1, .btn-1', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'     => [
					'switch_a_trigger' => 'yes',
				]
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_switcher_b_layout',
			[
				'label' => __('Switch B', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_b_title',
			[
				'label'   => __('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => __('Switch B', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_b_select_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'switch_b_icon',
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$this->add_control(
			'source_b',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'    => esc_html__('Custom Content', 'bdthemes-element-pack'),
					'elementor' => esc_html__('Elementor Template', 'bdthemes-element-pack'),
					'anywhere'  => esc_html__('AE Template', 'bdthemes-element-pack'),
					'custom_section'  => esc_html__('Link Section', 'bdthemes-element-pack'),
					'link_widget'  => esc_html__('Link Widget', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'source_b_link_widget',
			[
				'label'   => __('Link Widget ID', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'condition'   => ['source_b' => "link_widget"],
			]
		);

		$this->add_control(
			'source_b_link_widget_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Note: Please insert two widgets on the same section then place your widgets ID here. Don\'t need to add # before ID. You must use Link widget option on both switcher. This result will visible on the Frontend.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'   => ['source_b' => "link_widget"],
			]
		);
		$this->add_control(
			'template_id_b',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => ['source_b' => "elementor"],
			]
		);
		$this->add_control(
			'anywhere_id_b',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'anywhere_template',
				],
				'condition'   => ['source_b' => "anywhere"],
			]
		);

		$this->add_control(
			'switch_b_content',
			[
				'label'      => __('Content', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => __('Switch Content B', 'bdthemes-element-pack'),
				'show_label' => false,
				'condition'  => ['source_b' => 'custom'],
			]
		);

		$this->add_control(
			'switch_b_custom_section_id',
			[
				'label'       => __('Section ID', 'bdthemes-element-pack'),
				'description' => __('Paste your section ID here. Don\'t need to add # before ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'section-b',
				'dynamic'     => ['active' => true],
				'condition'  => ['source_b' => 'custom_section'],
			]
		);

		$this->add_control(
			'show_switch_b_badge',
			[
				'label'        => __('Show Badge', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'switch_b_badge',
			[
				'label'   => __('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Update', 'bdthemes-element-pack'),
				'dynamic' => ['active' => true],
				'condition' => [
					'show_switch_b_badge'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_b_trigger',
			[
				'label'        => __('Trigger Dynamically', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'separator'	   => 'before',
			]
		);

		$this->add_control(
			'switch_b_trigger_selector',
			[
				'label'   => __('Trigger Selector', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'condition' => [
					'switch_b_trigger'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_b_trigger_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('If you want to Trigger this switch Dynamically from anywhere else, please enter the id or class of the target element . Example. #btn-2, .btn-2', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'     => [
					'switch_b_trigger' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_switcher_addtional',
			[
				'label' => __('Switch Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tab_layout',
			[
				'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'bottom'  => esc_html__('Bottom', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'content_position_unchanged',
			[
				'label'        => __('Content Position Unchanged', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'condition'	   => [
					'source_a' => 'custom_section',
					'source_b' => 'custom_section',
				],
			]
		);

		$this->add_control(
			'item_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item + .bdt-tabs-item' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_transition',
			[
				'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_transition_options(),
				'default' => ''
			]
		);

		$this->add_control(
			'duration',
			[
				'label' => __('Animation Duration', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 1,
						'max'  => 2000,
						'step' => 50,
					],
				],
				'default' => [
					'size' => 200,
				],
				'condition' => [
					'tab_transition!' => ''
				]
			]
		);

		$this->add_control(
			'media',
			[
				'label'       => __('Turn On Horizontal mode', 'bdthemes-element-pack'),
				'description' => __('It means that when switch to the horizontal tabs mode from vertical mode', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'960' => [
						'title' => __('On Tablet', 'bdthemes-element-pack'),
						'icon'  => 'eicon-device-tablet',
					],
					'768' => [
						'title' => __('On Mobile', 'bdthemes-element-pack'),
						'icon'  => 'eicon-device-mobile',
					],
				],
				'condition' => [
					'tab_layout' => ['left', 'right']
				],
			]
		);

		$this->add_control(
			'default_active',
			[
				'label'   => __('Default Active', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'a' => [
						'title' => __('Switch A', 'bdthemes-element-pack'),
						'icon'  => 'eicon-square',
					],
					'b' => [
						'title' => __('Switch B', 'bdthemes-element-pack'),
						'icon'  => 'eicon-square',
					],
				],
				'default' => 'a',
				'render_type' => 'template',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_switcher_style',
			[
				'label' => __('Switcher Wrapper', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'switcher_alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
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
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switcher_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'switcher_border',
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} .bdt-tabs-container .bdt-tab',
			]
		);

		$this->add_responsive_control(
			'switcher_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switcher_box_shadow',
				'label' => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-tabs-container .bdt-tab',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_switcher_a_title',
			[
				'label' => __('Switch A', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('switch_a_tabs_title_style');

		$this->start_controls_tab(
			'switch_a_tab_title_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_a_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item-a-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'switch_a_title_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-tabs-item-a-title',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'switch_a_title_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-a-title',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'switch_a_title_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-a-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'switch_a_title_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item-a-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'switch_a_title_typography',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-a-title',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_a_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-a-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_a_active_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-a-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'switch_a_active_title_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item a.bdt-tabs-item-a-title:before',
			]
		);

		$this->add_control(
			'switch_a_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-a-title' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'switch_a_title_border_border!' => ''
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'switch_a_active_title_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-a-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_a_active_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-a-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_switcher_b_title',
			[
				'label' => __('Switch B', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('switch_b_tabs_title_style');

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_b_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item-b-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'switch_b_title_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-tabs-item-b-title',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'switch_b_title_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-b-title',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'switch_b_title_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-b-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'switch_b_title_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item-b-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'switch_b_title_typography',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-b-title',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_b_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-b-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'switch_b_tab_title_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_b_active_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-b-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'switch_b_active_title_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item a.bdt-tabs-item-b-title:before',
			]
		);

		$this->add_control(
			'switch_b_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-b-title' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'switch_b_title_border_border!' => ''
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'switch_b_active_title_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-b-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_b_active_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-b-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_switch_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'switch_a_select_icon[value]!' => '',
					'switch_b_select_icon[value]!' => ''
				]
			]
		);

		$this->start_controls_tabs('switch_icon_style');

		$this->start_controls_tab(
			'switch_icon_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Start', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('End', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'right' : 'left',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item a [class*="bdt-button-icon-"]' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item a [class*="bdt-button-icon-"] svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item a .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item a .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'switch_icon_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item.bdt-active a [class*="bdt-button-icon-"]' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-switchers .bdt-tabs-item.bdt-active a [class*="bdt-button-icon-"] svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_content',
			[
				'label' => __('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
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
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers .bdt-switcher-item-content-inner' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-switchers ul'                => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-switchers ul.bdt-tab-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .bdt-switchers .bdt-switcher-item-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_switcher_badge_style',
			[
				'label' => __('Badge', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_switch_a_badge',
							'operator' => '==',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_switch_b_badge',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->start_controls_tabs('switch_tabs_badge_style');

		$this->start_controls_tab(
			'switch_tabs_a_badge',
			[
				'label' => __('Switch A', 'bdthemes-element-pack'),
				'condition' => [
					'show_switch_a_badge'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_a_badge_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switch_a_badge_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge, {{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_a_badge_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_a_badge_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_a_badge_spacing',
			[
				'label'   => esc_html__('Badge Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'switch_a_badge_typography',
				'selector' => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_a_badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-a-badge',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'switch_tabs_b_badge',
			[
				'label' => __('Switch B', 'bdthemes-element-pack'),
				'condition' => [
					'show_switch_b_badge'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'switch_b_badge_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switch_b_badge_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge, {{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_b_badge_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_b_badge_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switch_b_badge_spacing',
			[
				'label'   => esc_html__('Badge Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'switch_b_badge_typography',
				'selector' => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switch_b_badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-tabs-container .bdt-tab .bdt-tabs-item .bdt-badge.bdt-b-badge',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	protected function render_switcher_templates()
	{
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

?>

		<div id="bdt-switcher-<?php echo esc_attr($id); ?>" class="bdt-switcher bdt-switcher-item-content">

			<div class="bdt-switcher-item-content-inner">
				<div>

					<?php
					if ('custom' == $settings['source_a'] and !empty($settings['switch_a_content'])) {
						echo $this->parse_text_editor($settings['switch_a_content']);
					} elseif ("elementor" == $settings['source_a'] and !empty($settings['template_id_a'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id_a']);
						echo element_pack_template_edit_link($settings['template_id_a']);
					} elseif ('anywhere' == $settings['source_a'] and !empty($settings['anywhere_id_a'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['anywhere_id_a']);
						echo element_pack_template_edit_link($settings['anywhere_id_a']);
					} elseif ('custom_section' == $settings['source_a'] and !empty($settings['switch_a_custom_section_id'])) {
						echo '<div class="bdt-switcher-item-a"></div>';
					}
					?>

				</div>
			</div>

			<div class="bdt-switcher-item-content-inner">
				<div>

					<?php
					if ('custom' == $settings['source_b'] and !empty($settings['switch_b_content'])) {
						echo $this->parse_text_editor($settings['switch_b_content']);
					} elseif ("elementor" == $settings['source_b'] and !empty($settings['template_id_b'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id_b']);
						echo element_pack_template_edit_link($settings['template_id_b']);
					} elseif ('anywhere' == $settings['source_b'] and !empty($settings['anywhere_id_b'])) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['anywhere_id_b']);
						echo element_pack_template_edit_link($settings['anywhere_id_b']);
					} elseif ('custom_section' == $settings['source_b'] and !empty($settings['switch_b_custom_section_id'])) {
						echo '<div class="bdt-switcher-item-b"></div>';
					}
					?>

				</div>
			</div>

		</div>

	<?php
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$tab_a_custom_section = ($settings['switch_a_custom_section_id']) ? $settings['switch_a_custom_section_id'] : '';
		$tab_b_custom_section = ($settings['switch_b_custom_section_id']) ? $settings['switch_b_custom_section_id'] : '';

		$this->add_render_attribute(
			[
				'switcher-settings' => [
					'id' => [
						'bdt-tabs-' . esc_attr($id),
					],
					'class' => [
						'bdt-switchers',
					],
				]
			]
		);


		if (('custom_section' == $settings['source_a'] and !empty($settings['switch_a_custom_section_id'])) or ('custom_section' == $settings['source_b'] and !empty($settings['switch_b_custom_section_id']))) {

			$this->add_render_attribute(
				[
					'switcher-settings' => [
						'data-settings' => [
							wp_json_encode([
								'id'			   => $this->get_id(),
								'switch-a-content' => $tab_a_custom_section,
								'switch-b-content' => $tab_b_custom_section,
								'positionUnchanged' => $settings['content_position_unchanged'] == 'yes' ? true : false,
								'defaultActive' => $settings['default_active']
							])
						],
					]
				]
			);
		}

		$this->add_render_attribute(
			[
				'tab-settings' => [
					'class' => [
						'bdt-tab',
						('' !== $settings['tab_layout']) ? 'bdt-tab-' . $settings['tab_layout'] : '',
					],
					'data-bdt-tab' => [
						wp_json_encode(array_filter([
							"connect"   => "#bdt-switcher-" .  esc_attr($id),
							"animation" => $settings["tab_transition"] ? "bdt-animation-" . $settings["tab_transition"] : "",
							"duration"  => $settings["duration"] ? $settings["duration"]["size"] : "",
							"media"     => $settings["media"] ? $settings["media"] : "",
							"swiping"   => false,
							"active"   => ($settings["default_active"] == 'b') ? "1" : ""
						]))
					],
					'id' => "bdt-switcher-activator-" .  esc_attr($id),
				]
			]
		);


		$this->add_render_attribute(
			[
				'switcher-settings' => [
					'data-activator' => [
						wp_json_encode([
							'id' 	  => esc_attr($id),
							// 'switchA' => $settings['switch_a_trigger_selector'] ?  '.not-select' : '',
							// 'switchB' => $settings['switch_b_trigger_selector'] ?  '.not-select' : '',
							'switchA' => $settings['switch_a_trigger_selector'] ? $settings['switch_a_trigger_selector'] : '.not-select',
							'switchB' => $settings['switch_b_trigger_selector'] ? $settings['switch_b_trigger_selector'] : '.not-select',
						])
					],
				]
			]
		);

		// start for link widget
		if (($settings['source_a'] == 'link_widget') || ($settings['source_b'] == 'link_widget')) {
			$this->add_render_attribute(
				[
					'switcher-settings' => [
						'data-bdt-link-widget' => [
							wp_json_encode(array_filter([
								'id' => esc_attr($id),
								"linkWidgetTargetA" => (isset($settings['source_a_link_widget']) && !empty($settings['source_a_link_widget'])) ? '#' . $settings['source_a_link_widget'] : '',
								"linkWidgetTargetB" => (isset($settings['source_b_link_widget']) && !empty($settings['source_b_link_widget'])) ? '#' . $settings['source_b_link_widget'] : '',
							]))
						],

					]
				]
			);
		}

		// end for link widget


		if (!isset($settings['switch_a_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['switch_a_icon'] = 'fas fa-arrow-right';
		}

		$a_migrated  = isset($settings['__fa4_migrated']['switch_a_select_icon']);
		$a_is_new    = empty($settings['switch_a_icon']) && Icons_Manager::is_migration_allowed();

		if (!isset($settings['switch_b_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['switch_b_icon'] = 'fas fa-arrow-right';
		}

		$b_migrated  = isset($settings['__fa4_migrated']['switch_b_select_icon']);
		$b_is_new    = empty($settings['switch_b_icon']) && Icons_Manager::is_migration_allowed();

	?>
		<div <?php echo $this->get_render_attribute_string('switcher-settings'); ?>>

			<?php if ('bottom' == $settings['tab_layout']) : ?>
				<div class="bdt-switcher-container">
					<?php $this->render_switcher_templates(); ?>
				</div>
			<?php endif; ?>

			<div class="bdt-tabs-container">
				<div <?php echo $this->get_render_attribute_string('tab-settings'); ?>>
					<?php
					$tab_title_a = ($settings['switch_a_title']) ? '' : ' bdt-has-no-title';
					$tab_title_b = ($settings['switch_b_title']) ? '' : ' bdt-has-no-title';

					?>
					<div class="bdt-tabs-item<?php echo esc_attr($tab_title_a); ?>">
						<a class="bdt-tabs-item-a-title" href="#">
							<div class="bdt-tab-text-wrapper">

								<?php if ('' != $settings['switch_a_select_icon']['value'] and 'left' == $settings['icon_align']) : ?>
									<span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

										<?php if ($a_is_new || $a_migrated) :
											Icons_Manager::render_icon($settings['switch_a_select_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
										else : ?>
											<i class="<?php echo esc_attr($settings['switch_a_icon']); ?>" aria-hidden="true"></i>
										<?php endif; ?>

									</span>
								<?php endif; ?>

								<?php if ($settings['switch_a_title']) : ?>
									<span class="bdt-tab-text"><?php echo esc_attr($settings['switch_a_title']); ?></span>
								<?php endif; ?>

								<?php if ('' != $settings['switch_a_select_icon']['value'] and 'right' == $settings['icon_align']) : ?>
									<span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

										<?php if ($a_is_new || $a_migrated) :
											Icons_Manager::render_icon($settings['switch_a_select_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
										else : ?>
											<i class="<?php echo esc_attr($settings['switch_a_icon']); ?>" aria-hidden="true"></i>
										<?php endif; ?>

									</span>
								<?php endif; ?>

							</div>
						</a>
						<?php if ($settings['show_switch_a_badge']) : ?>
							<div class="bdt-badge bdt-a-badge"><?php echo esc_attr($settings['switch_a_badge']); ?></div>
						<?php endif; ?>
					</div>

					<div class="bdt-tabs-item<?php echo esc_attr($tab_title_b); ?>">
						<a class="bdt-tabs-item-b-title" href="#">
							<div class="bdt-tab-text-wrapper">

								<?php if ('' != $settings['switch_b_select_icon']['value'] and 'left' == $settings['icon_align']) : ?>
									<span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

										<?php if ($b_is_new || $b_migrated) :
											Icons_Manager::render_icon($settings['switch_b_select_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
										else : ?>
											<i class="<?php echo esc_attr($settings['switch_b_icon']); ?>" aria-hidden="true"></i>
										<?php endif; ?>

									</span>
								<?php endif; ?>

								<?php if ($settings['switch_b_title']) : ?>
									<span class="bdt-tab-text"><?php echo esc_attr($settings['switch_b_title']); ?></span>
								<?php endif; ?>

								<?php if ('' != $settings['switch_b_select_icon']['value'] and 'right' == $settings['icon_align']) : ?>
									<span class="bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

										<?php if ($b_is_new || $b_migrated) :
											Icons_Manager::render_icon($settings['switch_b_select_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
										else : ?>
											<i class="<?php echo esc_attr($settings['switch_b_icon']); ?>" aria-hidden="true"></i>
										<?php endif; ?>

									</span>
								<?php endif; ?>

							</div>
						</a>
						<?php if ($settings['show_switch_b_badge']) : ?>
							<div class="bdt-badge bdt-b-badge"><?php echo esc_attr($settings['switch_b_badge']); ?></div>
						<?php endif; ?>
					</div>

				</div>
			</div>

			<?php if ('bottom' != $settings['tab_layout']) : ?>
				<div class="bdt-switcher-wrapper">

					<?php $this->render_switcher_templates(); ?>

				</div>
			<?php endif; ?>

		</div>


<?php
	}
}
