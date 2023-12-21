<?php
/**
 * Table of Contents
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Control_Media;

defined( 'ABSPATH' ) || die();

class Table_Of_Contents extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Table of Contents', 'happy-addons-pro' );
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
		return 'hm hm-list-2';
	}

	public function get_keywords() {
		return ['table of content', 'table', 'toc'];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__content_controls();
		$this->__settings_content_controls();
		$this->toc_sticky_controls();
	}

	protected function __content_controls() {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __( 'Table of Contents', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'widget_title',
			[
				'label'              => __( 'Title', 'happy-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => __( 'Table of Contents', 'happy-addons-pro' ),
				'placeholder'        => __( 'Type your title here', 'happy-addons-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label'              => esc_html__( 'HTML Tag', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				],
				'default'            => 'h4',
				'frontend_available' => true,
			]
		);

		$this->start_controls_tabs( 'include_exclude_tags', [ 'separator' => 'before' ] );

		$this->start_controls_tab(
			'include',
			[
				'label' => esc_html__( 'Include', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'headings_by_tags',
			[
				'label'              => esc_html__( 'Anchors By Tags', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT2,
				'multiple'           => true,
				'default'            => [ 'h2', 'h3', 'h4', 'h5', 'h6' ],
				'options'            => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'label_block'        => true,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'container',
			[
				'label'              => esc_html__( 'Container', 'happy-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'label_block'        => true,
				'description'        => __( 'With this control you can use only a specific container’s heading element with Table of Content </br> Example: .toc, .toc-extra', 'happy-addons-pro' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab(); // include

		$this->start_controls_tab(
			'exclude',
			[
				'label' => esc_html__( 'Exclude', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'exclude_headings_by_selector',
			[
				'label'              => esc_html__( 'Anchors By Selector', 'happy-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'description'        => esc_html__( 'CSS selectors, in a comma-separated list', 'happy-addons-pro' ),
				'default'            => [],
				'label_block'        => true,
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab(); // exclude

		$this->end_controls_tabs(); // include_exclude_tags

		$this->add_control(
			'custom_style',
			[
				'label'              => esc_html__( 'Custom Style', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'frontend_available' => true,
				'render_type'        => 'template',
				'separator'          => 'before',
			]
		);

		$this->add_control(
			'custom_style_list',
			[
				'label'              => esc_html__( 'Select Style', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'hm-toc-slide-style',
				'options'            => [
					'hm-toc-slide-style'    => __( 'Slide', 'happy-addons-pro' ),
					'hm-toc-timeline-style' => __( 'Timeline', 'happy-addons-pro' ),
					'hm-toc-list-style'     => __( 'List', 'happy-addons-pro' ),
				],
				'condition'          => [
					'custom_style' => 'yes',
				],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'marker_view',
			[
				'label'              => esc_html__( 'Marker View', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'numbers',
				'options'            => [
					'numbers' => esc_html__( 'Numbers', 'happy-addons-pro' ),
					'bullets' => esc_html__( 'Bullets', 'happy-addons-pro' ),
				],
				'frontend_available' => true,
				'condition'          => [
					'custom_style' => '',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'                  => esc_html__( 'Icon', 'happy-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'default'                => [
					'value'   => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended'            => [
					'fa-solid'   => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition'              => [
					'marker_view'  => 'bullets',
					'custom_style' => '',
				],
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'frontend_available'     => true,
			]
		);

		$this->add_control(
			'hierarchical_view',
			[
				'label'              => esc_html__( 'Hierarchical View', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'condition'          => [
					'custom_style' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'collapse_subitems',
			[
				'label'              => __( 'Collapse Subitems', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'The "Collapse" option will not work unless you make the Table of Contents sticky.', 'happy-addons-pro' ),
				'condition'          => [
					'hierarchical_view' => 'yes',
					'custom_style'      => '',
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_toc_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'word_wrap',
			[
				'label'              => __( 'Word Wrap', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'return_value'       => 'ellipsis',
				'prefix_class'       => 'ha-toc--content-',
			]
		);

		$this->add_control(
			'minimize_box',
			[
				'label'              => __( 'Minimize Box', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'custom_style!' => 'yes',
				],
			]
		);

		$this->add_control(
			'expand_icon',
			[
				'label'       => esc_html__( 'Expand Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block' => false,
				'skin'        => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'condition'   => [
					'minimize_box'  => 'yes',
					'custom_style!' => 'yes',
				],
			]
		);

		$this->add_control(
			'collapse_icon',
			[
				'label'       => esc_html__( 'Collapse Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'        => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'label_block' => false,
				'condition'   => [
					'minimize_box'  => 'yes',
					'custom_style!' => 'yes',
				],
			]
		);

		// TODO: For Pro 3.6.0, convert this to the breakpoints utility method introduced in core 3.5.0.
		$breakpoints = ha_elementor()->breakpoints->get_active_breakpoints();

		$minimized_on_options = [];

		foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
			// This feature is meant for mobile screens.
			if ( 'widescreen' === $breakpoint_key ) {
				continue;
			}

			$minimized_on_options[ $breakpoint_key ] = sprintf(
				/* translators: 1: `<` character, 2: Breakpoint value. */
				esc_html__( '%1$s (%2$s %3$dpx)', 'happy-addons-pro' ),
				$breakpoint->get_label(),
				'<',
				$breakpoint->get_value()
			);
		}

		$minimized_on_options['desktop'] = esc_html__( 'Desktop (or smaller)', 'happy-addons-pro' );

		$this->add_control(
			'minimized_on',
			[
				'label'              => esc_html__( 'Minimized On', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'tablet',
				'options'            => $minimized_on_options,
				'prefix_class'       => 'ha-toc--minimized-on-',
				'condition'          => [
					'minimize_box!' => '',
					'custom_style!' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'scroll_offset',
			[
				'label'              => __( 'Scroll Offset', 'happy-addons-pro' ),
				'description'        => __( 'Minimum Value 0 is required for it to function.', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 0,
				],
				'frontend_available' => true,
				'responsive'         => true,
			]
		);

		$this->end_controls_section();
	}

	protected function toc_sticky_controls() {
		$this->start_controls_section(
			'_section_toc_sticky',
			[
				'label' => __( 'Sticky', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'sticky_toc_toggle',
			[
				'label'              => __( 'Sticky', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'sticky_toc_disable_on',
			[
				'label'              => esc_html__( 'Sticky On', 'happy-addons-pro' ),
				'label_block'        => true,
				'type'               => Controls_Manager::SELECT2,
				'multiple'           => true,
				'options'            => [
					'desktop' => esc_html__( 'Desktop', 'happy-addons-pro' ),
					'tablet'  => esc_html__( 'Table', 'happy-addons-pro' ),
					'mobile'  => esc_html__( 'Mobile', 'happy-addons-pro' ),
				],
				'default'            => [ 'desktop', 'tablet', 'mobile' ],
				'frontend_available' => true,
				'condition'          => [
					'sticky_toc_toggle' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_toc_type',
			[
				'label'              => __( 'Sticky Type', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'in-place',
				'options'            => [
					'in-place'        => __( 'Sticky In Place', 'happy-addons-pro' ),
					'custom-position' => __( 'Custom Position', 'happy-addons-pro' ),
				],
				'prefix_class'       => 'sticky-',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => [
					'sticky_toc_toggle' => 'yes',
				],
			]
		);

		$this->add_control(
			'toc_horizontal_align',
			[
				'label'              => __( 'Horizontal Align', 'happy-addons-pro' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => [
					'ha-toc-left'  => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'ha-toc-right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'            => 'ha-toc-right',
				'toggle'             => false,
				'frontend_available' => true,
				'condition'          => [
					'sticky_toc_type'   => 'custom-position',
					'sticky_toc_toggle' => 'yes',
				],
			]
		);

		$this->add_control(
			'toc_vertical_align',
			[
				'label'              => __( 'Vertical Align', 'happy-addons-pro' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => [
					'ha-toc-position-top'    => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					],
					'ha-toc-position-middle' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'ha-toc-position-bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'            => 'ha-toc-position-middle',
				'toggle'             => false,
				'frontend_available' => true,
				'condition'          => [
					'sticky_toc_type'   => 'custom-position',
					'sticky_toc_toggle' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_position_left',
			[
				'label'      => __( 'Left', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-left' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'toc_horizontal_align' => 'ha-toc-left',
					'sticky_toc_type'      => 'custom-position',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_position_left_top',
			[
				'label'      => __( 'Top', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-left.ha-toc-position-top' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'toc_horizontal_align' => 'ha-toc-left',
					'toc_vertical_align'   => 'ha-toc-position-top',
					'sticky_toc_type'      => 'custom-position',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'sticky_toc_position_left_bottom',
			[
				'label'      => __( 'Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-left.ha-toc-position-bottom' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'toc_horizontal_align' => 'ha-toc-left',
					'toc_vertical_align'   => 'ha-toc-position-bottom',
					'sticky_toc_type'      => 'custom-position',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'sticky_toc_position_right',
			[
				'label'      => __( 'Right', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-right' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'sticky_toc_type'      => 'custom-position',
					'toc_horizontal_align' => 'ha-toc-right',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_position_right_top',
			[
				'label'      => __( 'Top', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-right.ha-toc-position-top' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'toc_horizontal_align' => 'ha-toc-right',
					'sticky_toc_type'      => 'custom-position',
					'toc_vertical_align'   => 'ha-toc-position-top',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_position_right_bottom',
			[
				'label'      => __( 'Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.ha-toc-right.ha-toc-position-bottom' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'toc_horizontal_align' => 'ha-toc-right',
					'sticky_toc_type'      => 'custom-position',
					'toc_vertical_align'   => 'ha-toc-position-bottom',
					'sticky_toc_toggle'    => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_top_offset',
			[
				'label'      => __( 'Offset', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'condition'  => [
					'sticky_toc_type'   => 'in-place',
					'sticky_toc_toggle' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}}.sticky-in-place .ha-toc-wrapper.floating-toc' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'sticky_toc_z_index',
			[
				'label'      => __( 'Z-Index', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 999,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-toc-wrapper.floating-toc' => 'z-index: {{SIZE}}',
				],
				'condition'  => [
					'sticky_toc_toggle' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->toc_box_style_controls();
		$this->toc_box_style_header_controls();
		$this->toc_box_style_list_item();
		$this->toc_slide_item();
		$this->toc_timeline_item();
		$this->toc_list_item();
	}

	protected function toc_box_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			[
				'label'     => esc_html__( 'Box', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'wrapper_bg',
				'label'    => esc_html__( 'Background Color', 'happy-addons-pro' ),
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'video', 'image'],
				'selector' => '{{WRAPPER}} .ha-toc-wrapper',
			]
		);

		$this->add_control(
			'loader_color',
			[
				'label'     => esc_html__( 'Loader Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					// Not using CSS var for BC, when not configured: the loader should get the color from the body tag.
					'{{WRAPPER}} .ha-toc__spinner' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-toc-wrapper' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label'     => esc_html__( 'Border Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-toc-wrapper' => 'border-width: {{SIZE}}{{UNIT}}; border-style:solid',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-toc-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'              => esc_html__( 'Min Height', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px', 'vh' ],
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'          => [
					'{{WRAPPER}} .ha-toc-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'frontend_available' => true,
				'condition' => [
					'custom_style!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .ha-toc-wrapper',
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'     => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ha-toc__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-toc-wrapper.hm-toc-slide-style' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-toc-wrapper.hm-toc-timeline-style' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-toc-wrapper.hm-toc-list-style' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); // box_style
	}

	protected function toc_box_style_header_controls() {
		$this->start_controls_section(
			'section_box_style_header',
			[
				'label'     => esc_html__( 'Header', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'custom_style!' => 'yes',
				],
			]
		);

		$this->add_control(
			'header_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--header-background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'header_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--header-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_typography',
				'selector' => '{{WRAPPER}} .ha-toc__header, {{WRAPPER}} .ha-toc__header-title',
			]
		);

		$this->add_control(
			'toggle_button_color',
			[
				'label'     => esc_html__( 'Icon Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'minimize_box' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--toggle-button-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'header_separator_width',
			[
				'label'     => esc_html__( 'Separator Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--separator-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'header_sepa_color',
			[
				'label'     => esc_html__( 'Separator Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--separator-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function toc_box_style_list_item() {
		$this->start_controls_section(
			'section_box_style_list_item_style',
			[
				'label'     => esc_html__( 'List Item', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'custom_style!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'list_padding',
			[
				'label'     => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default'   => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--list-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'max_height',
			[
				'label'      => esc_html__( 'Max Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--toc-body-max-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'list_typography',
				'selector' => '{{WRAPPER}} .ha-toc__list-item',
			]
		);

		$this->add_control(
			'list_indent',
			[
				'label'      => esc_html__( 'Indent', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--nested-list-indent: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'hierarchical_view' => 'yes',
					'custom_style'      => '',
				],
			]
		);

		$this->start_controls_tabs( 'item_text_style' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'item_text_color_normal',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_text_underline_normal',
			[
				'label'     => esc_html__( 'Underline', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-decoration: underline',
				],
			]
		);

		$this->end_controls_tab(); // normal

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'item_text_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'    => '#E04D8B',
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-hover-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_text_underline_hover',
			[
				'label'     => esc_html__( 'Underline', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'selectors' => [
					'{{WRAPPER}}' => '--item-text-hover-decoration: underline',
				],
			]
		);

		$this->end_controls_tab(); // hover
		$this->end_controls_tabs(); // item_text_style

		$this->add_control(
			'heading_marker',
			[
				'label'     => esc_html__( 'Marker', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label'     => esc_html__( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--marker-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'marker_size',
			[
				'label'      => esc_html__( 'Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--marker-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function toc_slide_item() {
		$this->start_controls_section(
			'section_toc_slide_item',
			[
				'label'     => esc_html__( 'Slide', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'custom_style'      => 'yes',
					'custom_style_list' => 'hm-toc-slide-style',
				],
			]
		);

		$this->add_control(
			'toc_slide_title',
			[
				'label' => __( 'Heading', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'toc_slide_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toc_slide_title_bar_color',
			[
				'label'     => esc_html__( 'Bar Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc .hm-toc-title:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toc_slide_title_typography',
				'selector' => '{{WRAPPER}} .hm-toc-title',
			]
		);
		$this->add_responsive_control(
			'toc_slide_space_bottom',
			[
				'label'      => __( 'Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toc_slide_item',
			[
				'label'     => __( 'List Item', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toc_slide_item_typography',
				'selector' => '{{WRAPPER}} .hm-toc .hm-toc-entry a',
			]
		);

		$this->add_responsive_control(
			'toc_slide_lsit_space_bottom',
			[
				'label'      => __( 'List Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-entry' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toc_slide_bar_style' );

		$this->start_controls_tab(
			'toc_slide_bar_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_slide_text_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc .hm-toc-entry a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc .hm-toc-entry a:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toc_slide_bar_normal_color',
			[
				'label'     => esc_html__( 'Bar Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc .hm-toc-entry a:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toc_slide_bar_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_slide_text_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'    => '#E04D8B',
				'selectors' => [
					'{{WRAPPER}} .hm-toc .hm-toc-entry a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc .hm-toc-entry a.ha-toc-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toc_slide_bar_hover_color',
			[
				'label'     => esc_html__( 'Bar Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc .hm-toc-entry a.ha-toc-item-active:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function toc_timeline_item() {
		$this->start_controls_section(
			'section_toc_timeline_item',
			[
				'label'     => __( 'Timeline', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'custom_style'      => 'yes',
					'custom_style_list' => 'hm-toc-timeline-style',
				],
			]
		);

		$this->add_control(
			'tml_toc_title',
			[
				'label' => __( 'Heading', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'tml_toc_title_color',
			[
				'label'     => __( 'Title Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tml_toc_title_typography',
				'selector' => '{{WRAPPER}} .hm-toc-timeline-style .hm-toc-title',
			]
		);
		$this->add_responsive_control(
			'tml_space_bottom',
			[
				'label'      => __( 'Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toc_tml_items',
			[
				'label'     => __( 'List Item', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toc_tml_item_typography',
				'selector' => '{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a',
			]
		);

		$this->add_responsive_control(
			'toc_tml_item_space_bottom',
			[
				'label'      => __( 'List Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc-entry' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toc_tml_item_tabs' );

		$this->start_controls_tab(
			'toc_tml_item_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_tml_item_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toc_tml_item_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_tml_item_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'    => '#E04D8B',
				'selectors' => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a.ha-toc-item-active' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a:hover::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a.ha-toc-item-active::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'toc_tml_dots',
			[
				'label'     => __( 'Dots', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'toc_tml_dots_size',
			[
				'label'     => esc_html__( 'Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-timeline-style' => '--hm-toc-timeline-dot-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toc_tml_dots_box_shadow',
				'selector' => '{{WRAPPER}} .hm-toc-timeline-style .hm-toc .hm-toc-entry a::before',
			]
		);

		$this->add_control(
			'toc_tml_tree_color',
			[
				'label'     => esc_html__( 'Tree Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-timeline-style .hm-toc-items-inner:before' => 'border-left-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function toc_list_item() {
		$this->start_controls_section(
			'section_toc_list_item',
			[
				'label'     => __( 'List', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'custom_style'      => 'yes',
					'custom_style_list' => 'hm-toc-list-style',
				],
			]
		);

		$this->add_control(
			'toc_list_title',
			[
				'label' => __( 'Heading', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'toc_list_title_color',
			[
				'label'     => __( 'Title Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toc_list_title_typography',
				'selector' => '{{WRAPPER}} .hm-toc-list-style .hm-toc-title',
			]
		);
		$this->add_responsive_control(
			'toc_list_title_space_bottom',
			[
				'label'      => __( 'Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toc_list_items',
			[
				'label'     => __( 'List Item', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toc_list_item_typography',
				'selector' => '{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a',
			]
		);

		$this->add_responsive_control(
			'toc_list_item_space_bottom',
			[
				'label'      => __( 'List Space Bottom', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc-entry' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toc_list_item_tabs' );

		$this->start_controls_tab(
			'toc_list_item_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_list_item_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toc_list_item_bar_normal_color',
			[
				'label'     => esc_html__( 'Bar Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toc_list_item_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toc_list_item_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'    => '#E04D8B',
				'selectors' => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a.ha-toc-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toc_list_item_bar_hover_color',
			[
				'label'     => esc_html__( 'Bar Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'    => '#E04D8B',
				'selectors' => [
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a:hover::before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .hm-toc-list-style .hm-toc .hm-toc-entry a.ha-toc-item-active::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$html_tag = Utils::validate_html_tag( $settings['html_tag'] );

		$this->add_render_attribute( 'body', 'class', 'ha-toc__body' );
		$this->add_render_attribute( 'wrapper', 'class', 'ha-toc-wrapper' );

		if ( 'yes' == $settings['custom_style'] ) {
			$this->add_render_attribute( 'wrapper', 'class', [$settings['custom_style_list']] );
		} else {
			$this->add_render_attribute( 'wrapper', 'class', ['hm-toc-default-style'] );
		}

		if ( $settings['collapse_subitems'] ) {
			$this->add_render_attribute( 'body', 'class', 'ha-toc__list-items--collapsible' );
		}

		if ( 'yes' === $settings['sticky_toc_toggle'] && 'custom-position' === $settings['sticky_toc_type'] ) {
			$this->add_render_attribute(
				'wrapper',
				'class',
				[
					'sticky_position_fixed',
					$settings['toc_horizontal_align'],
					$settings['toc_vertical_align'],
				]
			);
		}
		if ( ha_elementor()->editor->is_edit_mode() && 'yes' === $settings['sticky_toc_toggle'] && 'custom-position' === $settings['sticky_toc_type'] ) :
			?>
			<div class="ha-toc-editor-placeholder">
				<div class="ha-toc-editor-placeholder-content">
					<?php esc_html_e( 'This is a placeholder text which won\'t be displayed in the preview panel or front end.', 'happy-addons-pro' ); ?>
				</div>
			</div>
			<?php
		endif;
		?>
		<div id="<?php echo 'ha-toc-' . esc_attr( $this->get_id() ); ?>" <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
		<?php if ( 'yes' != $settings['custom_style'] ) : ?>
			<div class="ha-toc__header">
				<<?php Utils::print_validated_html_tag( $html_tag ); ?> class="ha-toc__header-title">
					<?php echo esc_html( $settings['widget_title'] ); ?>
				</<?php Utils::print_validated_html_tag( $html_tag ); ?>>

				<?php if ( 'yes' === $settings['minimize_box'] ) : ?>
				<div class="ha-toc__toggle-button ha-toc__toggle-button--expand">
					<?php Icons_Manager::render_icon( $settings['expand_icon'] ); ?>
				</div>
				<div class="ha-toc__toggle-button ha-toc__toggle-button--collapse">
					<?php Icons_Manager::render_icon( $settings['collapse_icon'] ); ?>
				</div>
				<?php endif; ?>
			</div>
			<div <?php $this->print_render_attribute_string( 'body' ); ?>>
				<div class="ha-toc__spinner-container">
					<i class="ha-toc__spinner eicon-loading eicon-animation-spin" aria-hidden="true"></i>
				</div>
				<span class="line"></span>
			</div>
			<?php endif; ?>
		</div>
		<?php

	}
}
