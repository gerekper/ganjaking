<?php
/**
 * Breadcrumbs Hour widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Happy_Addons_Pro\Breadcrumb_Trail;

defined('ABSPATH') || die();

class Breadcrumbs extends Base {
	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Breadcrumbs', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-breadcrumbs';
	}

	public function get_keywords() {
		return ['breadcrumb', 'breadcrumbs', 'crumb', 'crumbs', 'list', 'header', 'builder'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__breadcrumbs_display_content_controls();
		$this->__breadcrumbs_content_controls();
	}

	protected function __breadcrumbs_display_content_controls() {

		$this->start_controls_section(
			'_section_breadcrumbs_display',
			[
				'label' => __('Display Text', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'home',
			[
				'label'                 => __( 'Homepage', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'Home', 'happy-addons-pro' ),
				'dynamic'               => [
					'active'        => true,
					'categories'    => [ TagsModule::POST_META_CATEGORY ]
				],
			]
		);

		$this->add_control(
			'page_title',
			[
				'label'                 => __( 'Pages', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'Pages', 'happy-addons-pro' ),
				'dynamic'               => [
					'active'        => true,
					'categories'    => [ TagsModule::POST_META_CATEGORY ]
				],
			]
		);

		$this->add_control(
			'search',
			[
				'label'                 => __( 'Search', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'Search results for:', 'happy-addons-pro' ),
				'dynamic'               => [
					'active'        => true,
					'categories'    => [ TagsModule::POST_META_CATEGORY ]
				],
			]
		);

		$this->add_control(
			'error_404',
			[
				'label'                 => __( 'Error 404', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( '404 Not Found', 'happy-addons-pro' ),
				'dynamic'               => [
					'active'        => true,
					'categories'    => [ TagsModule::POST_META_CATEGORY ]
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __breadcrumbs_content_controls() {

		$this->start_controls_section(
			'_section_breadcrumbs',
			[
				'label' => __('Breadcrumbs', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'home_icon',
			[
				'label'					=> __( 'Home Icon', 'happy-addons-pro' ),
				'label_block'			=> false,
				'type'					=> Controls_Manager::ICONS,
				'default'				=> [
					'value'		=> 'fas fa-home',
					'library'	=> 'fa-solid',
				],
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
			]
		);

		$this->add_control(
			'separator_type',
			[
				'label'                 => __( 'Separator Type', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'icon',
				'options'               => [
					'text'          => __( 'Text', 'happy-addons-pro' ),
					'icon'          => __( 'Icon', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'separator_text',
			[
				'label'                 => __( 'Separator', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( '>', 'happy-addons-pro' ),
				'condition'             => [
					'separator_type'    => 'text'
				],
			]
		);

		$this->add_control(
			'separator_icon',
			[
				'label'					=> __( 'Separator', 'happy-addons-pro' ),
				'label_block'			=> false,
				'type'					=> Controls_Manager::ICONS,
				'default'				=> [
					'value'		=> 'fas fa-angle-right',
					'library'	=> 'fa-solid',
				],
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'condition'             => [
					'separator_type'    => 'icon'
				],
			]
		);

		$this->add_control(
			'show_on_front',
			[
				'label' => __( 'Show on front page', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __( 'Show last item', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                 => __( 'Alignment', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => '',
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__breadcrumbs_style_controls();
		$this->__common_style_controls();
		$this->__home_style_controls();
		$this->__separator_style_controls();
		$this->__current_style_controls();
	}

	protected function __breadcrumbs_style_controls() {

		$this->start_controls_section(
			'_section_breadcrumbs_style',
			[
				'label' => __('Breadcrumbs', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'breadcrumbs_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'breadcrumbs_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs',
			]
		);

		$this->add_control(
			'breadcrumbs_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'breadcrumbs_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-breadcrumbs',
			]
		);

		$this->add_responsive_control(
			'breadcrumbs_margin',
			[
				'label'                 => __( 'Margin', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'breadcrumbs_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_common_style',
			[
				'label' => __('Common', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'common_spacing',
			[
				'label'                 => __( 'Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' 	=> [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-breadcrumbs li:last-child' => 'margin-right: 0;',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_common_style' );

		$this->start_controls_tab(
			'tab_common_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'common_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text' => 'color: {{VALUE}}'
				],
			]
		);

		// $this->add_control(
		// 	'common_background_color',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_background_color',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'common_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'common_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text',
			]
		);

		$this->add_control(
			'common_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_common_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'common_color_hover',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text:hover' => 'color: {{VALUE}}',
				],
			]
		);

		// $this->add_control(
		// 	'common_background_color_hover',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text:hover' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_background_color_hover',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'common_typography_hover',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					'font_family',
					'font_size',
					'text_transform',
					'font_style',
					'line_height',
					'letter_spacing',
				],
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text:hover',
			]
		);

		$this->add_control(
			'common_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'common_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text',
			]
		);

		$this->add_responsive_control(
			'common_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __home_style_controls() {

		$this->start_controls_section(
			'_section_home_style',
			[
				'label' => __('Home', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_home_style' );

		$this->start_controls_tab(
			'tab_home_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'home_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'home_icon_color',
			[
				'label'                 => __( 'Home Icon Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text .ha-breadcrumbs-home-icon' => 'color: {{VALUE}}',
				],
			]
		);

		// $this->add_control(
		// 	'home_background_color',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'default'               => '',
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'home_background_color',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'home_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'home_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text',
			]
		);

		$this->add_control(
			'home_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_home_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'home_color_hover',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'home_icon_color_hover',
			[
				'label'                 => __( 'Home Icon Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover .ha-breadcrumbs-home-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text .ha-breadcrumbs-home-icon' => '-webkit-transition: all .4s;transition: all .4s;',
				],
			]
		);

		// $this->add_control(
		// 	'home_background_color_hover',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'default'               => '',
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'home_background_color_hover',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'home_typography_hover',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					'font_family',
					'font_size',
					'text_transform',
					'font_style',
					'line_height',
					'letter_spacing',
				],
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover',
			]
		);

		$this->add_control(
			'home_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-start span.ha-breadcrumbs-text:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'home_spacing',
			[
				'label'                 => __( 'Home Icon Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' 	=> [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li span.ha-breadcrumbs-home-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function __separator_style_controls() {

		$this->start_controls_section(
			'_section_separator_style',
			[
				'label' => __('Separator', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text' => 'color: {{VALUE}}',
				],
			]
		);

		// $this->add_control(
		// 	'separator_background_color',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'default'               => '',
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon' => 'background-color: {{VALUE}}',
		// 			'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'separator_background_color',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon, {{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'separator_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon, {{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'separator_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon, {{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon',
			]
		);

		$this->add_control(
			'separator_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'separator_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-separator span.ha-breadcrumbs-separator-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __current_style_controls() {

		$this->start_controls_section(
			'_section_current_style',
			[
				'label' => __('Current', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'current_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text' => 'color: {{VALUE}}',
				],
			]
		);

		// $this->add_control(
		// 	'current_background_color',
		// 	[
		// 		'label'                 => __( 'Background Color', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'default'               => '',
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text' => 'background-color: {{VALUE}}',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'current_background_color',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'current_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'current_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text',
			]
		);

		$this->add_control(
			'current_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-breadcrumbs li.ha-breadcrumbs-item.ha-breadcrumbs-end span.ha-breadcrumbs-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$home_icon = '';
		if($settings['home_icon']['value']){
			//$attributes = ! empty( $settings['home_icon']['value'] ) ? 'class="' . esc_attr($settings['home_icon']['value']) . '"' : '';
			$home_icon = sprintf( '<%1$s class="%2$s" aria-hidden="true"></%1$s>', ha_escape_tags( 'i' ), esc_attr( $settings['home_icon']['value'] ) );
		}

		$separator = '';
		if( 'icon' === $settings['separator_type'] && $settings['separator_icon']['value'] ){
			$icon = sprintf( '<%1$s class="%2$s" aria-hidden="true"></%1$s>', ha_escape_tags( 'i' ), esc_attr( $settings['separator_icon']['value'] ) );
			$attributes = 'class="ha-breadcrumbs-separator-icon"';
			$separator = sprintf( '<%1$s %2$s>%3$s</%1$s>', ha_escape_tags( 'span' ), $attributes, $icon );
		}elseif( 'text' === $settings['separator_type'] && $settings['separator_text'] ){
			$attributes = 'class="ha-breadcrumbs-separator-text"';
			$separator = sprintf( '<%1$s %2$s>%3$s</%1$s>', ha_escape_tags( 'span' ), $attributes, esc_html( $settings['separator_text'] ) );
		}

		$labels = array(
			'home' => $settings['home'] ? esc_html( $settings['home'] ) : '',
			'page_title' => $settings['page_title'] ? esc_html( $settings['page_title'] ) : '',
			'search' => $settings['search'] ? esc_html( $settings['search'] ).' %s' : '%s',
			'error_404' => $settings['error_404'] ? esc_html( $settings['error_404'] ) : '',
		);

		$args = array(
			'list_class'      => 'ha-breadcrumbs',
			'item_class'      => 'ha-breadcrumbs-item',
			'separator'      => $separator,
			'separator_class' => 'ha-breadcrumbs-separator',
			'home_icon' => $home_icon,
			'home_icon_class' => 'ha-breadcrumbs-home-icon',
			'labels' => $labels,
			'show_on_front' => 'yes' === $settings['show_on_front'] ? true : false,
			'show_title' => 'yes' === $settings['show_title'] ? true : false,
		);

		$breadcrumb = new Breadcrumb_Trail( $args );
		echo $breadcrumb->trail();
	}

}
