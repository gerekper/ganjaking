<?php
/**
 * Horizontal Timeline widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Horizontal_Timeline extends Base {
	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Horizontal Timeline', 'happy-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-horizontal-timeline';
	}

	public function get_keywords() {
		return [ 'horizontal', 'timeline', 'slider', 'carousel', 'scroll' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__timeline_content_controls();
		$this->__settings_content_controls();
	}

	protected function __timeline_content_controls() {

		$this->start_controls_section(
			'_section_timeline',
			[
				'label' => __( 'Timeline', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'event_date',
			[
				'label' => __( 'Event Date', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Event Date', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'event_icon',
			[
				'label' => __('Icon', 'happy-elementor-addons'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'separator' => 'after',
				'default' => [
					'value' => 'fas fa-calendar-alt',
					'library' => 'solid',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'happy-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'medium',
				'exclude' => [
					'custom'
				]
			]
		);

		$repeater->add_control(
			'event_title',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Event Title', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'event_link',
			[
				'label' => esc_html__( 'Link', 'happy-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'happy-elementor-addons' ),
				'default' => [
					'url' => '',
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'event_subtitle',
			[
				'label' => __( 'Sub Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Event Sub Title', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'event_description',
			[
				'label' => __( 'Description', 'happy-elementor-addons' ),
				// 'type' => Controls_Manager::TEXTAREA,
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'placeholder' => __( 'Event Description', 'happy-elementor-addons' ),
				'default' => __( 'Best Elementor Addons Plugin.', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'custom_look',
			[
				'label' => __( 'Custom Style', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'custom_event_icon_color',
			[
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-horizontal-timeline-icon-box .ha-horizontal-timeline-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-horizontal-timeline-icon-box .ha-horizontal-timeline-icon svg' => 'color: {{VALUE}}'
				],
			]
		);

		$repeater->add_control(
			'custom_event_icon_background_color',
			[
				'label' => __( 'Icon Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-horizontal-timeline-icon-box .ha-horizontal-timeline-icon' => 'background-color: {{VALUE}}'
				],
			]
		);


		$repeater->add_control(
			'custom_title_color',
			[
				'label' => __( 'Title Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content .ha-horizontal-timeline-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content .ha-horizontal-timeline-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'custom_link_hover_color',
			[
				'label' => __( 'Title Color Hover', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content .ha-horizontal-timeline-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'custom_content_background_color',
			[
				'label' => __( 'Content Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-arrow:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-inner' => 'background-color: {{VALUE}}'
				],
			]
		);

		$repeater->add_control(
			'custom_content_color',
			[
				'label' => __( 'Content Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'custom_look' => 'yes'
				],
				'selectors' => [
					// '{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content .ha-horizontal-timeline-subtitle' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-horizontal-timeline-block .ha-horizontal-timeline-content .ha-horizontal-timeline-description' => 'color: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'timeline',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ event_title }}}',
				'default' => [
					[
						'event_date' => 'Jan 01, 2021',
						'event_title' => __( 'Build beautiful websites', 'happy-elementor-addons' ),
					],
					[
						'event_date' => 'Jan 02, 2021',
						'event_title' => __( 'Cross Domain Copy Paste', 'happy-elementor-addons' ),
					],
					[
						'event_date' => 'Jan 03, 2021',
						'event_title' => __( 'CSS Transform', 'happy-elementor-addons' ),
					],
					[
						'event_date' => 'Jan 04, 2021',
						'event_title' => __( 'Fast and Lightweight', 'happy-elementor-addons' ),
					]
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Settings', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'content_alignment',
			[
				'label' => __( 'Content Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors_dictionary' => [
					'left' => 'align-items: flex-start',
					'center' => 'align-items: center',
					'right' => 'align-items: flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-inner'  => '{{VALUE}};'
				],
			]
		);

		$this->add_control(
			'content_arrow',
			[
				'label' => __( 'Hide Content Arrow', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'magnific_popup',
			[
				'label' => __( 'Enable Lightbox', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label' => __( 'Animation Speed', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 10,
				'max' => 10000,
				'default' => 800,
				'description' => __( 'Slide speed in milliseconds', 'happy-elementor-addons' ),
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 100,
				'max' => 10000,
				'default' => 3000,
				'description' => __( 'Autoplay speed in milliseconds', 'happy-elementor-addons' ),
				'condition' => [
					'autoplay' => 'yes'
				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop?', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => __( 'Slides To Show', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					1 => __( '1 Slide', 'happy-elementor-addons' ),
					2 => __( '2 Slides', 'happy-elementor-addons' ),
					3 => __( '3 Slides', 'happy-elementor-addons' ),
					4 => __( '4 Slides', 'happy-elementor-addons' ),
					5 => __( '5 Slides', 'happy-elementor-addons' ),
					6 => __( '6 Slides', 'happy-elementor-addons' ),
				],
				'desktop_default' => 3,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
				'style_transfer' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'arrow_prev_icon',
			[
				'label' => __( 'Previous Icon', 'happy-elementor-addons' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'arrow_next_icon',
			[
				'label' => __( 'Next Icon', 'happy-elementor-addons' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__timeline_style_controls();
		$this->__arrow_style_controls();
		$this->__content_style_controls();
	}

	protected function __timeline_style_controls() {

		$this->start_controls_section(
			'_section_style_timeline',
			[
				'label' => __( 'Timeline', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'line_heading',
			[
				'label' => __( 'Line', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'line_spacing',
			[
				'label' => __( 'Thickness', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-tree' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'line_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-tree' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'date_heading',
			[
				'label' => __( 'Date', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'date_spacing',
			[
				'label' => __( 'Left Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-date' => 'padding-left: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-date',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-date' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'icon_background_size',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 35,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-icon' => 'font-size: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-icon',
			]
		);

		$this->add_control(
			'event_icon_background_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_icon_color',
			[
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __arrow_style_controls() {

		$this->start_controls_section(
			'_section_style_arrows',
			[
				'label' => __( 'Arrows', 'happy-elementor-addons' ),
				'tab'  => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'arrow_vertical_alignment',
			[
				'label' => __( 'Vertical Position', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 1000,
					],
					'%' => [
						'min' => -30,
						'max' => 130,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_horizontal_align',
			[
				'label' => __( 'Horizontal Position', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 1000,
					],
					'%' => [
						'min' => -30,
						'max' => 130,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_arrow' );

		$this->start_controls_tab(
			'_tab_arrow_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'arrow_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-horizontal-timeline-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_responsive_control(
		//     'content_height',
		//     [
		//         'label' => __( 'Height', 'happy-elementor-addons' ),
		//         'type' => Controls_Manager::SLIDER,
		// 		'size_units' => ['px'],
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 10,
		// 				'max' => 3000,
		// 			],
		// 		],
		//         'selectors' => [
		//             '{{WRAPPER}} .ha-horizontal-timeline-content' => 'height: {{SIZE}}{{UNIT}};'
		//         ],
		//     ]
		// );

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-inner' => 'padding: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'content_space',
			[
				'label' => __( 'Space between contents', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-block' => 'padding: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-content, {{WRAPPER}} .ha-horizontal-timeline-arrow',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-content, {{WRAPPER}} .ha-horizontal-timeline-arrow',
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-horizontal-timeline-arrow::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-horizontal-timeline-inner' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'image_heading',
			[
				'label' => __( 'Image', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Width', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-image img' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label' => __( 'Height', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-image img' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-image' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-image img',
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-horizontal-timeline-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label' => __( 'Hover Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'subtitle_heading',
			[
				'label' => __( 'Sub Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-subtitle',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label' => __( 'Description', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-horizontal-timeline-description',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-horizontal-timeline-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['timeline'] ) ) {
			return;
		}
		$magnific_popup = '';

		$this->add_render_attribute( 'wrapper', 'class', 'ha-horizontal-timeline-wrapper' );
		$this->add_render_attribute( 'wrapper', 'class', 'ha-carousel' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>

			<?php foreach ( $settings['timeline'] as $timeline ) : ?>
				<div class="ha-horizontal-timeline-block elementor-repeater-item-<?php echo $timeline['_id']; ?>">
					<div class="ha-horizontal-timeline-icon-box">

						<span class="ha-horizontal-timeline-date"><?php echo esc_html( $timeline['event_date'] ); ?></span>

						<div class="ha-horizontal-timeline-top-inner">
							<?php if ( $timeline['event_icon'] ) : ?>
								<div class="ha-horizontal-timeline-icon">
									<?php Icons_Manager::render_icon( $timeline['event_icon'], ['aria-hidden' => 'true'] ); ?>
								</div>
							<?php endif; ?>
							<div class="ha-horizontal-timeline-tree"></div>
						</div>
					</div>

					<div class="ha-horizontal-timeline-content">

						<?php if ( $settings['content_arrow'] != 'yes' ) : ?>
							<div class="ha-horizontal-timeline-arrow"></div>
						<?php endif; ?>

						<div class="ha-horizontal-timeline-inner">
							<?php if ( ! empty( $timeline['image']['url'] ) ) : ?>
								<?php
									if( 'yes' === $settings['magnific_popup'] && ! ha_elementor()->editor->is_edit_mode() ){
										$magnific_popup = 'data-mfp-src=' . esc_url($timeline['image']['url']);
									}
								?>
								<div class="ha-horizontal-timeline-image" <?php echo $magnific_popup;?>>
									<?php echo Group_Control_Image_Size::get_attachment_image_html( $timeline, 'thumbnail', 'image' ); ?>
								</div>
							<?php endif; ?>

							<?php

								if ( ! empty( $timeline['event_link']['url'] ) ) {
									$this->add_link_attributes( 'event_link', $timeline['event_link'] );
									if ( $timeline['event_title'] ) {
										printf( '<%2$s class="ha-horizontal-timeline-title"><a %1$s>%3$s</a></%2$s>',
											$this->get_render_attribute_string( 'event_link' ),
											ha_escape_tags( $settings['title_tag'], 'h2' ),
											esc_html( $timeline['event_title'] )
										);
									}
									$this->remove_render_attribute( 'event_link');
								}else{
								
									if ( $timeline['event_title'] ) {
										printf( '<%1$s class="ha-horizontal-timeline-title">%2$s</%1$s>',
											ha_escape_tags( $settings['title_tag'], 'h2' ),
											esc_html( $timeline['event_title'] )
										);
									}
							}
							
							?>

							<?php if ( !empty( $timeline['event_subtitle'] ) ) : ?>
								<span class="ha-horizontal-timeline-subtitle"><?php echo esc_html( $timeline['event_subtitle'] ); ?></span>
							<?php endif; ?>

							<?php
							if ($timeline['event_description']) {
								printf('<div class="ha-horizontal-timeline-description">%s</div>', $this->parse_text_editor($timeline['event_description']));
							}
							?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>

		</div>

		<?php if ( ! empty( $settings['arrow_prev_icon']['value'] ) ) : ?>
			<button type="button" class="slick-prev"><?php Icons_Manager::render_icon( $settings['arrow_prev_icon'], ['aria-hidden' => 'true'] ); ?></button>
		<?php endif; ?>

		<?php if ( ! empty( $settings['arrow_next_icon']['value'] ) ) : ?>
			<button type="button" class="slick-next"><?php Icons_Manager::render_icon( $settings['arrow_next_icon'], ['aria-hidden' => 'true'] ); ?></button>
		<?php endif; ?>

		<?php
	}

}
