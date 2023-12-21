<?php
/**
 * Timeline
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
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Repeater;

defined('ABSPATH') || die();

class Timeline extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Timeline', 'happy-addons-pro');
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
		return 'hm hm-timeline';
	}

	public function get_keywords() {
		return ['timeline', 'time', 'schedule'];
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
				'label' => __('Timeline', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('tabs_timeline_item');
		$repeater->start_controls_tab(
			'tab_timeline_content_item',
			[
				'label' => __('Content', 'happy-addons-pro'),
			]
		);
		$repeater->add_control(
			'icon_type',
			[
				'label' => __('Icon Type', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __('Icon', 'happy-addons-pro'),
					'image' => __('Image', 'happy-addons-pro'),
				],
				'default' => 'icon',
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => __('Icon', 'happy-addons-pro'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-calendar-alt',
					'library' => 'solid',
				],
				'condition' => [
					'icon_type' => 'icon'
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __('Image Icon', 'happy-addons-pro'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image'
				],
                'dynamic' => [
                    'active' => true,
                ]
			]
		);

		$repeater->add_control(
			'time_style',
			[
				'label' => __('Time', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'calender' => __('Calender', 'happy-addons-pro'),
					'text' => __('Text', 'happy-addons-pro'),
				],
				'default' => 'calender',
				'style_transfer' => true,
			]
		);


		$repeater->add_control(
			'time',
			[
				'label' => __('Calender Time', 'happy-addons-pro'),
				'show_label' => false,
				'type' => Controls_Manager::DATE_TIME,
				'default' => date('M d Y g:i a'),
				'condition' => [
					'time_style' => 'calender'
				],
			]
		);

		$repeater->add_control(
			'time_text',
			[
				'label' => __('Text Time', 'happy-addons-pro'),
				'show_label' => false,
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __('2016 - 2018', 'happy-addons-pro'),
				'placeholder' => __('Text Time', 'happy-addons-pro'),
				'condition' => [
					'time_style' => 'text'
				],
                'dynamic' => [
                    'active' => true,
                ]
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __('Title', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('This Is Happy Title', 'happy-addons-pro'),
				'placeholder' => __('Title', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'gallery',
			[
				'type' => Controls_Manager::GALLERY
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'exclude' => ['custom'],
				'default' => 'thumbnail',
			]
		);

		$repeater->add_control(
			'image_position',
			[
				'label' => __('Image Position', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'before' => __('Before Title', 'happy-addons-pro'),
					'after' => __('After Content', 'happy-addons-pro'),
				],
				'default' => 'before',
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'content',
			[
				'label' => __('Content', 'happy-addons-pro'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut',
				'placeholder' => __('Type your content here', 'happy-addons-pro'),
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => __('Button Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Read More', 'happy-addons-pro'),
				'placeholder' => __('Button Text', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => __('Button Link', 'happy-addons-pro'),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://happyaddons.com/',
				'default' => [
					'url' => '#',
					'is_external' => true,
					'nofollow' => true,
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'button_text!' => '',
				]
			]
		);
		$repeater->end_controls_tab();//Timeline Content Tab END

		//Timeline Style Tab
		$repeater->start_controls_tab(
			'tab_timeline_style_item',
			[
				'label' => __('Style', 'happy-addons-pro'),
			]
		);

		$repeater->add_control(
			'single_icon_color',
			[
				'label' => __('Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-timeline-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-timeline-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'icon_type' => 'icon'
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'single_icon_box_bg',
			[
				'label' => __('Icon box Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-timeline-icon' => 'background: {{VALUE}}',
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'single_icon_box_border_bg',
			[
				'label' => __('Icon box border color', 'happy-addons-pro'),
				'description' => __('Color will apply after set the border from style.', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-timeline-icon' => 'border-color: {{VALUE}}',
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'single_icon_box_tree_color',
			[
				'label' => __('Icon box tree color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-timeline-tree' => 'background: {{VALUE}}',
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_responsive_control(
			'single_content_align',
			[
				'label' => __('Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justify', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-wrap > {{CURRENT_ITEM}} .ha-timeline-content' => 'text-align: {{VALUE}}'
				],
				'style_transfer' => true,
			]
		);
		$repeater->end_controls_tab();//Timeline Style Tab END
		$repeater->end_controls_tabs();

		$this->add_control(
			'timeline_item',
			[
				'label' => __('Content List', 'happy-addons-pro'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-calendar-alt',
							'library' => 'solid',
						],
						'image' => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'time' => date('M d Y g:i a'),
						'title' => __('This Is Happy Title', 'happy-addons-pro'),
						'image_position' => 'before',
						'content' => '<p>' . __('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut', 'happy-addons-pro') . '</p>',
						'button_text' => __('Button Text', 'happy-addons-pro'),
						'button_link' => [
							'url' => '#',
							'is_external' => true,
							'nofollow' => true,
						],
					],
					[
						'icon_type' => 'icon',
						'icon' => [
							'value' => 'fas fa-calendar-alt',
							'library' => 'solid',
						],
						'image' => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'time' => date('M d Y g:i a'),
						'title' => __('This Is Happy Title', 'happy-addons-pro'),
						'image_position' => 'before',
						'content' => '<p>' . __('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut', 'happy-addons-pro') . '</p>',
						'button_text' => __('Button Text', 'happy-addons-pro'),
						'button_link' => [
							'url' => '#',
							'is_external' => true,
							'nofollow' => true,
						],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_timeline_settings',
			[
				'label' => __('Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'show_date',
			[
				'label' => __('Show Date?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'happy-addons-pro'),
				'label_off' => __('Hide', 'happy-addons-pro'),
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_time',
			[
				'label' => __('Show Time?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'happy-addons-pro'),
				'label_off' => __('Hide', 'happy-addons-pro'),
				'return_value' => 'yes',
				'default' => '',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_content_arrow',
			[
				'label' => __('Show Content Arrow?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'happy-addons-pro'),
				'label_off' => __('Hide', 'happy-addons-pro'),
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __('Title Tag', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'h2',
				'options' => [
					'h1' => [
						'title' => __('H1', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h1'
					],
					'h2' => [
						'title' => __('H2', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h2'
					],
					'h3' => [
						'title' => __('H3', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h3'
					],
					'h4' => [
						'title' => __('H4', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h4'
					],
					'h5' => [
						'title' => __('H5', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h5'
					],
					'h6' => [
						'title' => __('H6', 'happy-addons-pro'),
						'icon' => 'eicon-editor-h6'
					]
				],
				'toggle' => false,
			]
		);

		$this->add_control(
			'icon_box_align',
			[
				'label' => __('Icon Box Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'toggle' => false,
				'default' => 'top',
				'prefix_class' => 'ha-timeline-icon-box-vertical-align-',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'tree_align',
			[
				'label' => __('Tree Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => false,
				'prefix_class' => 'ha-timeline-align-',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_scroll_tree',
			[
				'label' => __('Show Scroll Tree?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'happy-addons-pro'),
				'label_off' => __('Hide', 'happy-addons-pro'),
				'return_value' => 'yes',
				'default' => '',
				'style_transfer' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'scroll_tree_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-timeline-scroll-tree .ha-timeline-icon, {{WRAPPER}} .ha-timeline-tree-inner',
				'exclude' => [
					'image'
				],
				'condition' => [
					'show_scroll_tree' => 'yes'
				],
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__content_box_style_controls();
		$this->__icon_box_style_controls();
		$this->__title_style_controls();
		$this->__time_date_style_controls();
		$this->__button_style_controls();
	}

	protected function __content_box_style_controls() {

		$this->start_controls_section(
			'_section_timeline_content_box_style',
			[
				'label' => __('Content Box', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_box_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-timeline-content',
			]
		);

		$this->add_control(
			'content_box_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_box_arrow_color',
			[
				'label' => __('Arrow Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					//Align center
					'(desktop){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content.arrow::before' => 'border-left-color: {{VALUE}}',
					'(desktop){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}}; border-left-color: transparent;',
					'(tablet){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}};border-left-color: transparent',
					'(tablet){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}}; border-left-color: transparent;',
					'(mobile){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}};border-left-color: transparent',
					'(mobile){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}}; border-left-color: transparent;',
					//Align Left
					'{{WRAPPER}}.ha-timeline-align-left .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}}',
					//Align Right
					'(desktop){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content.arrow::before' => 'border-left-color: {{VALUE}};border-right-color: transparent',
					'(tablet){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}};border-left-color: transparent',
					'(mobile){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content.arrow::before' => 'border-right-color: {{VALUE}};border-left-color: transparent',
				],
				'condition' => [
					'show_content_arrow' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .ha-timeline-content',
			]
		);

		$this->add_control(
			'content_bg_after', ['type' => Controls_Manager::DIVIDER,]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_box_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-timeline-content',
			]
		);

		$this->add_control(
			'content_border_after', ['type' => Controls_Manager::DIVIDER,]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_box_shadow',
				'label' => __('Box Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-timeline-content',
			]
		);

		$this->add_responsive_control(
			'content_box_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_box_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_box_margin_bottom',
			[
				'label' => __('Margin Bottom', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-timeline-block:last-child .ha-timeline-content' => 'margin-bottom: 0;',
					'{{WRAPPER}}.ha-timeline-icon-box-vertical-align-center .ha-timeline-icon' => 'margin-top: calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}}.ha-timeline-icon-box-vertical-align-center .ha-timeline-block:last-child .ha-timeline-icon' => 'margin-top: 0;',
					'{{WRAPPER}}.ha-timeline-icon-box-vertical-align-bottom .ha-timeline-icon' => 'margin-top: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-timeline-icon-box-vertical-align-bottom .ha-timeline-block:last-child .ha-timeline-icon' => 'margin-top: 0;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __icon_box_style_controls() {

		$this->start_controls_section(
			'_section_timeline_icon_box_style',
			[
				'label' => __('Icon Box', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_box_width',
			[
				'label' => __('Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-timeline-block:nth-child(even) .ha-timeline-icon' => 'width: {{SIZE}}{{UNIT}};',
					//timeline align center -> content box width
					'(desktop){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content' => 'width: calc(50% - (({{icon_box_width.SIZE || 48}}{{UNIT}}/2) + {{icon_box_space.SIZE || 30}}{{UNIT}}));',
					'(tablet){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content' => 'width: calc(100% - (({{icon_box_width_tablet.SIZE || 40}}{{UNIT}}/2) + {{icon_box_space_tablet.SIZE || 35}}{{UNIT}}));',
					'(mobile){{WRAPPER}}.ha-timeline-align-center .ha-timeline-content' => 'width: calc(100% - (({{icon_box_width_mobile.SIZE || 40}}{{UNIT}}/2) + {{icon_box_space_mobile.SIZE || 35}}{{UNIT}}));',

					//timeline align left -> content box width
					'(desktop){{WRAPPER}}.ha-timeline-align-left .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width.SIZE || 48}}{{UNIT}} + {{icon_box_space.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space.SIZE || 110}}{{UNIT}}));',
					'(tablet){{WRAPPER}}.ha-timeline-align-left .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width_tablet.SIZE || 40}}{{UNIT}} + {{icon_box_space_tablet.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space_tablet.SIZE || 0}}{{UNIT}}));',
					'(mobile){{WRAPPER}}.ha-timeline-align-left .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width_mobile.SIZE || 40}}{{UNIT}} + {{icon_box_space_mobile.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space_mobile.SIZE || 0}}{{UNIT}}));',

					//timeline align right -> content box width
					'(desktop){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width.SIZE || 48}}{{UNIT}} + {{icon_box_space.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space.SIZE || 110}}{{UNIT}}));',
					'(tablet){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width_tablet.SIZE || 40}}{{UNIT}} + {{icon_box_space_tablet.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space_tablet.SIZE || 0}}{{UNIT}}));',
					'(mobile){{WRAPPER}}.ha-timeline-align-right .ha-timeline-content' => 'width: calc(100% - ({{icon_box_width_mobile.SIZE || 40}}{{UNIT}} + {{icon_box_space_mobile.SIZE || 30}}{{UNIT}} + {{icon_box_tree_space_mobile.SIZE || 0}}{{UNIT}}));',

				],
			]
		);

		$this->add_responsive_control(
			'icon_box_height',
			[
				'label' => __('Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-icon' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-timeline-block:nth-child(even) .ha-timeline-icon' => 'height: {{SIZE}}{{UNIT}};',
					//timeline content box arrow position
					'(desktop){{WRAPPER}}.ha-timeline-icon-box-vertical-align-top .ha-timeline-content.arrow::before' => 'top: calc(({{icon_box_height.SIZE}}{{UNIT}}/2) - 8px)',
					'(desktop){{WRAPPER}}.ha-timeline-icon-box-vertical-align-bottom .ha-timeline-content.arrow::before' => 'bottom: calc(({{icon_box_height.SIZE}}{{UNIT}}/2) - 8px)',
				],
			]
		);
		//Box Space
		$this->add_responsive_control(
			'icon_box_space',
			[
				'label' => __('Box Space', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					//timeline align center -> box margin
					'(desktop){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block .ha-timeline-icon-box' => 'margin-left: {{icon_box_space.SIZE || 30}}{{UNIT}};margin-right: 0;',
					'(desktop){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-icon-box' => 'margin-left: 0;margin-right: {{icon_box_space.SIZE || 30}}{{UNIT}};',

					'(tablet){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_tablet.SIZE || 35}}{{UNIT}};margin-left: 0;',
					'(tablet){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-icon-box' => 'margin-left: 0;margin-right: {{icon_box_space_tablet.SIZE || 35}}{{UNIT}};',

					'(mobile){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_mobile.SIZE || 35}}{{UNIT}};margin-left: 0;',
					'(mobile){{WRAPPER}}.ha-timeline-align-center .ha-timeline-block:nth-child(even) .ha-timeline-icon-box' => 'margin-left: 0;margin-right: {{icon_box_space_mobile.SIZE || 35}}{{UNIT}};',
				],
			]
		);
		//Box Tree Space
		$this->add_responsive_control(
			'icon_box_tree_space',
			[
				'label' => __('Tree Space', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => ['tree_align!' => 'center'],
				'selectors' => [
					//timeline align left -> box margin
					'(desktop){{WRAPPER}}.ha-timeline-align-left .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space.SIZE || 30}}{{UNIT}};margin-left: {{icon_box_tree_space.SIZE || 110}}{{UNIT}};',
					'(tablet){{WRAPPER}}.ha-timeline-align-left .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_tablet.SIZE || 30}}{{UNIT}};margin-left: {{icon_box_tree_space_tablet.SIZE || 0}}{{UNIT}};',
					'(mobile){{WRAPPER}}.ha-timeline-align-left .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_mobile.SIZE || 30}}{{UNIT}};margin-left: {{icon_box_tree_space_mobile.SIZE || 0}}{{UNIT}};',

					//timeline align right -> box margin
					'(desktop){{WRAPPER}}.ha-timeline-align-right .ha-timeline-block .ha-timeline-icon-box' => 'margin-left: {{icon_box_space.SIZE || 30}}{{UNIT}};margin-right: {{icon_box_tree_space.SIZE || 110}}{{UNIT}};',
					'(tablet){{WRAPPER}}.ha-timeline-align-right .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_tablet.SIZE || 30}}{{UNIT}};margin-left: {{icon_box_tree_space_tablet.SIZE || 0}}{{UNIT}};',
					'(mobile){{WRAPPER}}.ha-timeline-align-right .ha-timeline-block .ha-timeline-icon-box' => 'margin-right: {{icon_box_space_mobile.SIZE || 30}}{{UNIT}};margin-left: {{icon_box_tree_space_mobile.SIZE || 0}}{{UNIT}};',

				],
			]
		);

		$this->add_control(
			'icon_box_bg',
			[
				'label' => __('Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-icon' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_box_border',
				'label' => __('Icon box border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-timeline-icon',
			]
		);


		$this->add_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-timeline-icon svg' => 'fill: {{VALUE}}',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'icon_box_tree_width',
			[
				'label' => __('Tree Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-tree' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-timeline-tree-inner' => 'width: {{SIZE}}{{UNIT}};',

				],
			]
		);

		$this->add_control(
			'icon_box_tree_color',
			[
				'label' => __('Tree color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-tree' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __title_style_controls() {

		$this->start_controls_section(
			'_section_timeline_title_style',
			[
				'label' => __('Title', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .ha-timeline-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __time_date_style_controls() {

		$this->start_controls_section(
			'_section_timeline_time_date_style',
			[
				'label' => __('Time & Date', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'show_date',
									'operator' => '==',
									'value' => 'yes',
								],
								[
									'name' => 'show_time',
									'operator' => '==',
									'value' => 'yes',
								]
							],
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __('Date Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-timeline-date .date',
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'date_color',
			[
				'label' => __('Date Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'devices' => ['desktop', 'tablet', 'mobile'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-date .date' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		// $this->add_responsive_control(
		// 	'date_color',
		// 	[
		// 		'label' => __('Date Color', 'happy-addons-pro'),
		// 		'type' => Controls_Manager::COLOR,
		// 		'devices' => ['tablet'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-timeline-date .date' => 'color: {{VALUE}}',
		// 		],
		// 		'condition' => [
		// 			'show_date' => 'yes'
		// 		]
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'date_color',
		// 	[
		// 		'label' => __('Date Color', 'happy-addons-pro'),
		// 		'type' => Controls_Manager::COLOR,
		// 		'devices' => ['mobile'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-timeline-date .date' => 'color: {{VALUE}}',
		// 		],
		// 		'condition' => [
		// 			'show_date' => 'yes'
		// 		]
		// 	]
		// );

		$this->add_responsive_control(
			'date_margin',
			[
				'label' => __('Date Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-date .date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-timeline-date .time',
				'condition' => [
					'show_time' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'devices' => ['desktop', 'tablet', 'mobile'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-date .time' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_time' => 'yes'
				]
			]
		);

		// $this->add_responsive_control(
		// 	'time_color',
		// 	[
		// 		'label' => __('Time Color', 'happy-addons-pro'),
		// 		'type' => Controls_Manager::COLOR,
		// 		'devices' => ['tablet'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-timeline-date .time' => 'color: {{VALUE}}',
		// 		],
		// 		'condition' => [
		// 			'show_time' => 'yes'
		// 		]
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'time_color',
		// 	[
		// 		'label' => __('Time Color', 'happy-addons-pro'),
		// 		'type' => Controls_Manager::COLOR,
		// 		'devices' => ['mobile'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-timeline-date .time' => 'color: {{VALUE}}',
		// 		],
		// 		'condition' => [
		// 			'show_time' => 'yes'
		// 		]
		// 	]
		// );

		$this->add_responsive_control(
			'time_margin',
			[
				'label' => __('Time Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-date .time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_time' => 'yes'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __button_style_controls() {

		$this->start_controls_section(
			'_section_timeline_button_style',
			[
				'label' => __('Button', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-timeline-button',
			]
		);

		$this->start_controls_tabs(
			'button_tabs'
		);
		$this->start_controls_tab(
			'button_normal_tab',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);
		$this->add_control(
			'button_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-timeline-button',
			]
		);
		$this->end_controls_tab();//Button Normal Tab END

		$this->start_controls_tab(
			'button_hover_tab',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-button:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-timeline-button:hover',
			]
		);
		$this->end_controls_tab(); //Button Hover Tab END
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-timeline-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('timeline-wrap', 'class', 'ha-timeline-wrap');
		if( 'yes' === $settings['show_scroll_tree'] ){
			$this->add_render_attribute('timeline-wrap', 'data-scroll', $settings['show_scroll_tree']);
		}
		?>
		<div <?php echo $this->get_render_attribute_string('timeline-wrap'); ?>>
		<?php if ($settings['timeline_item']):
			foreach ($settings['timeline_item'] as $key => $item):?>
				<?php
				//Block
				$this->set_render_attribute('timeline-block', 'class', ['ha-timeline-block', 'elementor-repeater-item-' . esc_attr($item['_id'])]);
				//Date
				$date = date("d M Y", strtotime($item['time']));
				if('text' == $item['time_style']){
					$date = $item['time_text'];
				}
				$time = 'calender' == $item['time_style'] ? date("g:i a", strtotime($item['time'])) : '';
				//Icon Image
				if ('image' == $item['icon_type'] && $item['image']) {
					$this->add_render_attribute('image', 'src', $item['image']['url']);
					$this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($item['image']));
					$this->add_render_attribute('image', 'title', Control_Media::get_image_title($item['image']));
				}

				//Title
				$title_key = $this->get_repeater_setting_key('title', 'timeline_item', $key);
				// $this->add_inline_editing_attributes($title_key, 'none');
				$this->add_render_attribute($title_key, 'class', 'ha-timeline-title');

				//Content box
				$this->add_render_attribute('content-box', 'class', 'ha-timeline-content');
				if ($settings['show_content_arrow']) {
					$this->add_render_attribute('content-box', 'class', 'arrow');
				}

				//Content Text
				$content_key = $this->get_repeater_setting_key('content', 'timeline_item', $key);
				// $this->add_inline_editing_attributes($content_key, 'advanced');
				$this->add_render_attribute($content_key, 'class', 'ha-timeline-content-text');

				//Button
				if ($item['button_text']) {
					$button_key = $this->get_repeater_setting_key('button_text', 'timeline_item', $key);
					// $this->add_inline_editing_attributes($button_key, 'none');
					$this->add_render_attribute($button_key, 'class', 'ha-timeline-button');

					$this->add_link_attributes( $button_key, $item['button_link'] );
				}
				?>
				<div <?php $this->print_render_attribute_string('timeline-block'); ?>>
					<div class="ha-timeline-icon-box align-center">
						<div class="ha-timeline-icon">
							<?php
							if ('icon' == $item['icon_type'] && $item['icon']) {
								Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true']);
							} elseif ('image' == $item['icon_type'] && $item['image']) {
								echo Group_Control_Image_Size::get_attachment_image_html($item, 'thumbnail', 'image');
							}
							?>
							<?php if (($date || $time) && ($settings['show_date'] || $settings['show_time'])): ?>
								<span class="ha-timeline-date ha-timeline-date-desktop">
									<?php
									if ($date && $settings['show_date']) {
										printf('<span class="date">%s</span>', esc_html($date));
									}
									if ($time && $settings['show_time']) {
										printf('<span class="time">%s</span>', esc_html($time));
									}
									?>
								</span>
							<?php endif; ?>
						</div>
						<div class="ha-timeline-tree">
							<?php if( 'yes' === $settings['show_scroll_tree'] ):?>
							<div class="ha-timeline-tree-inner"></div>
							<?php endif;?>
						</div>
					</div>
					<div <?php $this->print_render_attribute_string('content-box'); ?>">
					<?php if (($date || $time) && ($settings['show_date'] || $settings['show_time'])): ?>
						<span class="ha-timeline-date ha-timeline-date-tablet">
							<?php
							if ($date && $settings['show_date']) {
								printf('<span class="date">%s</span>', esc_html($date));
							}
							if ($time && $settings['show_time']) {
								printf('<span class="time">%s</span>', esc_html($time));
							}
							?>
						</span>
					<?php endif; ?>
					<?php
					if (!empty($item['gallery']) && 'before' == $item['image_position']) {
						echo '<figure class="ha-timeline-images before">';
						foreach ($item['gallery'] as $id => $single) {
							echo wp_get_attachment_image(
								$single['id'],
								$item['thumbnail_size'],
								false,
								[
									'alt' => wp_get_attachment_caption($single['id'])
								]
							);
						}
						echo '</figure>';
					}
					?>
					<?php
					if ($item['title']) {
						printf('<%1$s %2$s>%3$s</%1$s>', ha_escape_tags($settings['title_tag']), $this->get_render_attribute_string($title_key), esc_html($item['title']));
					}

					if ($item['content']) {
						printf('<div %s>%s</div>', $this->get_render_attribute_string($content_key), $this->parse_text_editor($item['content']));
					}
					if (!empty($item['gallery']) && 'after' == $item['image_position']) {
						echo '<figure class="ha-timeline-images after">';
						foreach ($item['gallery'] as $id => $single) {
							echo wp_get_attachment_image(
								$single['id'],
								$item['thumbnail_size'],
								false,
								[
									'alt' => wp_get_attachment_caption($single['id'])
								]
							);
						}
						echo '</figure>';
					}
					if ($item['button_text']) {
						printf('<a %s>%s</a>', $this->get_render_attribute_string($button_key), esc_html($item['button_text']));
					}
					?>
				</div>
				</div>
			<?php endforeach;
		endif; ?>
		</div>
		<?php

	}
}
