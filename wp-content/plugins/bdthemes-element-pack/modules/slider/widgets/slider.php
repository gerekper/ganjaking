<?php

namespace ElementPack\Modules\Slider\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Plugin;
use ElementPack\Element_Pack_Loader;

use ElementPack\Utils;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class Slider
 */
class Slider extends Module_Base {

	public function get_name() {
		return 'bdt-slider';
	}

	public function get_title() {
		return BDTEP . esc_html__('Slider', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-slider';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['slider', 'hero'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-slider', 'ep-font'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'ep-scripts'];
		} else {
			return ['imagesloaded', 'ep-slider'];
		}
	}

	public function on_import($element) {
		if (!get_post_type_object($element['settings']['posts_post_type'])) {
			$element['settings']['posts_post_type'] = 'services';
		}

		return $element;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/SI4K4zuNOoE';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_sliders',
			[
				'label' => esc_html__('Sliders', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'source',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'        => esc_html__('Custom Content', 'bdthemes-element-pack'),
					"elementor"     => esc_html__('Elementor Template', 'bdthemes-element-pack'),
				],
			]
		);
		$repeater->add_control(
			'template_id',
			[
				'label'       => __('Select Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => ['source' => 'elementor'],
			]
		);

		$repeater->add_control(
			'tab_title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Slide Title', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition' => ['source' => 'custom'],
			]
		);

		$repeater->add_control(
			'tab_image',
			[
				'label'   => esc_html__('Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'condition' => ['source' => 'custom'],
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label'      => esc_html__('Content', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => esc_html__('Slide Content', 'bdthemes-element-pack'),
				'show_label' => false,
				'condition' => ['source' => 'custom'],
			]
		);

		$repeater->add_control(
			'tab_link',
			[
				'label'       => esc_html__('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
				'condition' => ['source' => 'custom'],
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => esc_html__('Slider Items', 'bdthemes-element-pack'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title'   => esc_html__('Slide #1', 'bdthemes-element-pack'),
						'tab_content' => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
					],
					[
						'tab_title'   => esc_html__('Slide #2', 'bdthemes-element-pack'),
						'tab_content' => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
					],
					[
						'tab_title'   => esc_html__('Slide #3', 'bdthemes-element-pack'),
						'tab_content' => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
					],
					[
						'tab_title'   => esc_html__('Slide #4', 'bdthemes-element-pack'),
						'tab_content' => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'   => esc_html__('Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 600,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1024,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label'   => esc_html__('Content Width', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-desc' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'origin',
			[
				'label'   => esc_html__('Content Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
					'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
					'center'        => esc_html__('Center', 'bdthemes-element-pack'),
					'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
					'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
					'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
					'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
				]
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
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
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'description'  => 'Use align to match position',
				'default'      => 'center',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__('Show Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'scroll_to_section',
			[
				'label' => esc_html__('Scroll to Section', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'section_id',
			[
				'label'       => esc_html__('Section ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'Section ID Here',
				'description' => 'Enter section ID of this page, ex: #my-section',
				'label_block' => true,
				'condition'   => [
					'scroll_to_section' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_scroll_to_section_icon',
			[
				'label'       => esc_html__('Scroll to Section Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'scroll_to_section_icon',
				'default' => [
					'value' => 'fas fa-angle-double-down',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'scroll_to_section' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'slider_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__('Left', 'bdthemes-element-pack'),
					'right' => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'slider_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'slider_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-slider .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => __('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'            => esc_html__('Arrows and Dots', 'bdthemes-element-pack'),
					'arrows-fraction' => esc_html__('Arrows and Fraction', 'bdthemes-element-pack'),
					'arrows'          => esc_html__('Arrows', 'bdthemes-element-pack'),
					'dots'            => esc_html__('Dots', 'bdthemes-element-pack'),
					'progressbar'     => esc_html__('Progress', 'bdthemes-element-pack'),
					'none'            => esc_html__('None', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'dynamic_bullets',
			[
				'label'     => __('Dynamic Bullets?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'show_scrollbar',
			[
				'label'     => __('Show Scrollbar?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'both_position',
			[
				'label'     => __('Arrows and Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],

			]
		);

		$this->add_control(
			'arrows_fraction_position',
			[
				'label'     => __('Arrows and Fraction Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows-fraction',
				],

			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => __('Arrows Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],

			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => __('Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],

			]
		);

		$this->add_control(
			'progress_position',
			[
				'label'   => __('Progress Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
					'top'    => esc_html__('Top', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => 'progressbar',
				],

			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__('Arrows Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => esc_html__('Default', 'bdthemes-element-pack'),
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['arrows-fraction', 'both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __('Hide Arrow on Mobile ?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navigation' => ['arrows-fraction', 'arrows', 'both'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_slider_settings',
			[
				'label' => esc_html__('Slider Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'transition',
			[
				'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide'     => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'      => esc_html__('Fade', 'bdthemes-element-pack'),
					'cube'      => esc_html__('Cube', 'bdthemes-element-pack'),
					'coverflow' => esc_html__('Coverflow', 'bdthemes-element-pack'),
					'flip'      => esc_html__('Flip', 'bdthemes-element-pack'),
					// 'creative'      => esc_html__('creative', 'bdthemes-element-pack'),
				],
			]
		);

		// $this->add_control(
		// 	'animation_kenburns',
		// 	[
		// 		'label'        => esc_html__('Animation Kenburns', 'bdthemes-element-pack'),
		// 		'prefix_class' => 'bdt-animation-kenburns-',
		// 		'type'         => Controls_Manager::SWITCHER,
		// 	]
		// );

		$this->add_control(
			'effect',
			[
				'label'   => esc_html__('Text Effect', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'    => esc_html__('Slide Right to Left', 'bdthemes-element-pack'),
					'bottom'  => esc_html__('Slider Bottom to Top', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'     => esc_html__('Autoplay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'observer',
			[
				'label'       => __('Observer', 'bdthemes-element-pack') . BDTEP_NC,
				'description' => __('When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__('Slider', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slider_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14ABF4',
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name' => 'slider_background_color',
		// 		'label' => esc_html__( 'Background', 'bdthemes-element-pack' ),
		// 		'types' => [ 'classic', 'gradient' ],
		// 		'exclude' => [ 'image' ],
		// 		'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item',
		// 		'fields_options' => [
		// 			'background' => [
		// 				'default' => 'classic',
		// 			],
		// 			'color' => [
		// 				'default' => '#14ABF4',
		// 			],
		// 		],
		// 	]
		// );

		$this->add_control(
			'overlay_type',
			[
				'label'   => esc_html__('Overlay', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-element-pack'),
					'background' => esc_html__('Background', 'bdthemes-element-pack'),
					'blend'      => esc_html__('Blend', 'bdthemes-element-pack'),
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slider-image-wrapper:before',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(3, 4, 16, 0.4)',
					],
				],
				'condition' => [
					'overlay_type' => ['background', 'blend'],
				],
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => element_pack_blend_options(),
				'condition' => [
					'overlay_type' => 'blend',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slider-image-wrapper:before' => 'mix-blend-mode: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		// $this->add_control(
		// 	'show_text_stroke',
		// 	[
		// 		'label'   => esc_html__('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
		// 		'type'    => Controls_Manager::SWITCHER,
		// 		'prefix_class' => 'bdt-text-stroke--',
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'text_stroke_width',
		// 	[
		// 		'label' => esc_html__('Text Stroke Width', 'bdthemes-element-pack') . BDTEP_NC,
		// 		'type'  => Controls_Manager::SLIDER,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
		// 		],
		// 		'condition' => [
		// 			'show_text_stroke' => 'yes'
		// 		]
		// 	]
		// );

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'label' => __('Text_Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title',
			]
		);

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__('Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Text Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-text',
			]
		);

		$this->add_responsive_control(
			'text_space',
			[
				'label' => esc_html__('Text Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
				],
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
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link',
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
			'hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-slide-item .bdt-slide-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'navigation',
							'operator' => '!=',
							'value' => 'none',
						],
						[
							'name'     => 'show_scrollbar',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __('A R R O W S', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_navigation_arrows_style');

		$this->start_controls_tab(
			'tabs_nav_arrows_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-slider .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev, {{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'nav_arrows_border',
				'selector'    => '{{WRAPPER}} .bdt-slider .bdt-navigation-prev, {{WRAPPER}} .bdt-slider .bdt-navigation-next',
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'nav_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev, {{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev, {{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-slider .bdt-navigation-next i' => 'font-size: {{SIZE || 36}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_space',
			[
				'label' => __('Space Between Arrows', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_arrows_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-slider .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-slider .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'nav_arrows_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-slider .bdt-navigation-next:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'nav_arrows_border_border!' => '',
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'dots_heading',
			[
				'label'     => __('D O T S', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_navigation_dots_style');

		$this->start_controls_tab(
			'tabs_nav_dots_normal',
			[
				'label'     => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'dots_space_between',
			[
				'label'     => __('Space Between', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-dots-space-between: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_control(
			'advanced_dots_size',
			[
				'label'     => __('Advanced Size', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_width',
			[
				'label'     => __('Width(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_height',
			[
				'label'     => __('Height(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_box_shadow',
				'selector' => '{{WRAPPER}} .swiper-pagination-bullet',
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_dots_active',
			[
				'label'     => __('Active', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'active_dots_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ep-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_width',
			[
				'label'     => __('Width(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_height',
			[
				'label'     => __('Height(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ep-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-dots-align: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_active_box_shadow',
				'selector' => '{{WRAPPER}} .swiper-pagination-bullet-active',
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hr_2',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_heading',
			[
				'label'     => __('F R A C T I O N', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'hr_12',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'active_fraction_color',
			[
				'label'     => __('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-current' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'fraction_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-slider .swiper-pagination-fraction',
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'hr_3',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_heading',
			[
				'label'     => __('P R O G R E S B A R', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_13',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_color',
			[
				'label'     => __('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progres_color',
			[
				'label'     => __('Progress Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_4',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_heading',
			[
				'label'     => __('S C R O L L B A R', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_14',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_color',
			[
				'label'     => __('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-scrollbar' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_drag_color',
			[
				'label'     => __('Drag Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_height',
			[
				'label'   => __('Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-carousel-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}px;',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_5',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'navi_offset_heading',
			[
				'label'     => __('O F F S E T', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'hr_6',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'   => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'   => __('Arrows Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'   => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'   => __('Dots Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-dots-nnx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'   => __('Dots Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -30,
				],
				'tablet_default' => [
					'size' => -30,
				],
				'mobile_default' => [
					'size' => -30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-dots-nny: -{{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'   => __('Arrows & Dots Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-both-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'   => __('Arrows & Dots Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-both-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'   => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'   => __('Dots Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -55,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncx_position',
			[
				'label'   => __('Arrows & Fraction Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-fraction-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncy_position',
			[
				'label'   => __('Arrows & Fraction Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-carousel-arrows-fraction-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cx_position',
			[
				'label'   => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slider .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cy_position',
			[
				'label'   => __('Fraction Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -55,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-fraction' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'progress_y_position',
			[
				'label'   => __('Progress Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-pagination-progressbar' => 'transform: translateY({{SIZE}}px);',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_responsive_control(
			'scrollbar_vertical_offset',
			[
				'label'   => __('Scrollbar Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .swiper-carousel-horizontal > .swiper-scrollbar' => 'bottom: {{SIZE}}px;',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_scroll_to_top',
			[
				'label'      => esc_html__('Scroll to Top', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'scroll_to_section',
							'value' => 'yes',
						],
						[
							'name'     => 'section_id',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs('tabs_scroll_to_top_style');

		$this->start_controls_tab(
			'scroll_to_top_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'scroll_to_top_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_to_top_shadow',
				'selector' => '{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'scroll_to_top_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'scroll_to_top_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_icon_size',
			[
				'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_bottom_space',
			[
				'label' => esc_html__('Bottom Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 300,
						'step' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'scroll_to_top_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'scroll_to_top_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_hover_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'scroll_to_top_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slider .bdt-ep-scroll-to-section a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render_loop_header() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-slider-' . $this->get_id();

		$this->add_render_attribute('slider', 'id', $id);
		$this->add_render_attribute('slider', 'class', 'bdt-slider');

		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position']);
		}

		if ('arrows-fraction' == $settings['navigation']) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation']) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay"          => $settings["autoplay_speed"]] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"effect"         => $settings["transition"],
							// "creativeEffect" => [
							// 	'prev' => [
							// 		'shadow'    => true,
							// 		'translate' => ["-125%", 0, -800],
							// 		'rotate'    => [0, 0, -90],
							// 	],
							// 	'next' => [
							// 		'shadow'    => true,
							// 		'translate' => ["125%", 0, -800],
							// 		'rotate'    => [0, 0, 90],
							// 	]
							// ],
							"navigation"     => [
								"nextEl" => "#" . $id . " .bdt-navigation-next",
								"prevEl" => "#" . $id . " .bdt-navigation-prev",
							],
							"pagination" => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,

							],
							"scrollbar" => [
								"el"            => "#" . $id . " .swiper-scrollbar",
								"hide"          => "true",
							],
						]))
					]
				]
			]
		);

		if (!isset($settings['scroll_to_section_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['scroll_to_section_icon'] = 'fas fa-arrow-down';
		}

		$migrated  = isset($settings['__fa4_migrated']['slider_scroll_to_section_icon']);
		$is_new    = empty($settings['scroll_to_section_icon']) && Icons_Manager::is_migration_allowed();

		$swiper_class = Plugin::$instance->experiments->is_feature_active('e_swiper_latest') ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

?>
		<div <?php echo $this->get_render_attribute_string('slider'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<?php if ($settings['scroll_to_section'] && $settings['section_id']) : ?>
					<div class="bdt-ep-scroll-to-section bdt-position-bottom-center">
						<a href="<?php echo esc_url($settings['section_id']); ?>" bdt-scroll>
							<span class="bdt-ep-scroll-to-section-icon">

								<?php if ($is_new || $migrated) :
									Icons_Manager::render_icon($settings['slider_scroll_to_section_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
								else : ?>
									<i class="<?php echo esc_attr($settings['scroll_to_section_icon']); ?>" aria-hidden="true"></i>
								<?php endif; ?>

							</span>
						</a>
					</div>
				<?php endif;
			}

			public function render_navigation() {
				$settings = $this->get_settings_for_display();
				$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';

				if ('arrows' == $settings['navigation']) : ?>
					<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_position'] . $hide_arrow_on_mobile); ?>">
						<div class="bdt-arrows-container bdt-slidenav-container">
							<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
							</a>
							<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
							</a>
						</div>
					</div>
				<?php endif;
			}

			public function render_pagination() {
				$settings = $this->get_settings_for_display();

				if ('dots' == $settings['navigation'] or 'arrows-fraction' == $settings['navigation']) : ?>
					<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['dots_position']); ?>">
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					</div>

				<?php elseif ('progressbar' == $settings['navigation']) : ?>
					<div class="swiper-pagination bdt-position-z-index bdt-position-<?php echo esc_attr($settings['progress_position']); ?>"></div>
				<?php endif;
			}

			public function render_both_navigation() {
				$settings = $this->get_settings_for_display();
				$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

				?>
				<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
					<div class="bdt-arrows-dots-container bdt-slidenav-container ">

						<div class="bdt-flex bdt-flex-middle">
							<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
								<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
							</div>

							<?php if ('center' !== $settings['both_position']) : ?>
								<div class="swiper-pagination"></div>
							<?php endif; ?>

							<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
								<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
							</div>

						</div>
					</div>
				</div>
			<?php
			}

			public function render_arrows_fraction() {
				$settings             = $this->get_settings_for_display();
				$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

			?>
				<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_fraction_position']); ?>">
					<div class="bdt-arrows-fraction-container bdt-slidenav-container ">

						<div class="bdt-flex bdt-flex-middle">
							<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
								<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
							</div>

							<?php if ('center' !== $settings['arrows_fraction_position']) : ?>
								<div class="swiper-pagination"></div>
							<?php endif; ?>

							<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
								<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
									<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
								</a>
							</div>

						</div>
					</div>
				</div>
			<?php
			}

			protected function render_loop_footer() {
				$settings    = $this->get_settings_for_display();

			?>
				<?php if ('yes' === $settings['show_scrollbar']) : ?>
					<div class="swiper-scrollbar"></div>
				<?php endif; ?>
			</div>
			<?php if ('both' == $settings['navigation']) : ?>
				<?php $this->render_both_navigation(); ?>
				<?php if ('center' === $settings['both_position']) : ?>
					<div class="bdt-position-z-index bdt-position-bottom">
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php elseif ('arrows-fraction' == $settings['navigation']) : ?>
				<?php $this->render_arrows_fraction(); ?>
				<?php if ('center' === $settings['arrows_fraction_position']) : ?>
					<div class="bdt-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php $this->render_pagination(); ?>
				<?php $this->render_navigation(); ?>
			<?php endif; ?>
		</div>

	<?php
			}

			public function render() {
				$settings  = $this->get_settings_for_display();

				$this->render_loop_header();

	?>
		<div class="swiper-wrapper">
			<?php $counter = 1; ?>
			<?php foreach ($settings['tabs'] as $index => $item) : ?>

				<?php
					$image_src = isset($item['tab_image']['id']) ? wp_get_attachment_image_src($item['tab_image']['id'], 'full') : '';
					$image     =  $image_src ? $image_src[0] : '';

					$this->add_render_attribute(
						[
							'slide-item' => [
								'class' => [
									'bdt-slide-item',
									'swiper-slide',
									'bdt-slide-effect-' . $settings['effect'] ? $settings['effect'] : 'left',
								],
							]
						],
						'',
						'',
						true
					);
					
					$link_key = 'link_' . $index;
					$this->add_render_attribute(
						[
							$link_key => [
								'class' => [
									'bdt-slide-link',
									$settings['button_hover_animation'] ? 'elementor-animation-' . $settings['button_hover_animation'] : '',
								],
							]
						],
						'',
						'',
						true
					);
					
					if ('custom' == $item['source']) {
						$this->add_link_attributes($link_key, $item['tab_link']);
					}

					if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
						// add old default
						$settings['icon'] = 'fas fa-arrow-right';
					}

					$migrated  = isset($settings['__fa4_migrated']['slider_icon']);
					$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

					$this->add_render_attribute('bdt-slide-title', 'class', ['bdt-slide-title bdt-clearfix'], true);

				?>
				<div <?php echo $this->get_render_attribute_string('slide-item'); ?>>

					<?php
					if ('custom' == $item['source']) {
					?>

						<?php if ($image) : ?>
							<div class="bdt-slider-image-wrapper">
								<?php
								print(wp_get_attachment_image(
									$item['tab_image']['id'],
									'full',
									false,
									[
										'class' => 'bdt-cover',
										'alt' => wp_kses_post($item['tab_title']),
										'data-bdt-cover' => true
									]
								));
								?>
							</div>
						<?php endif; ?>

						<div class="bdt-slide-desc bdt-position-large bdt-position-<?php echo ($settings['origin']); ?> bdt-position-z-index">

							<?php if (('' !== $item['tab_title']) && ($settings['show_title'])) : ?>
								<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-slide-title'); ?>>
									<?php echo wp_kses_post($item['tab_title']); ?>
								</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
							<?php endif; ?>

							<?php if ('' !== $item['tab_content']) : ?>
								<div class="bdt-slide-text"><?php echo $this->parse_text_editor($item['tab_content']); ?></div>
							<?php endif; ?>

							<?php if ((!empty($item['tab_link']['url'])) && ($settings['show_button'])) : ?>
								<div class="bdt-slide-link-wrapper">
									<a <?php echo $this->get_render_attribute_string($link_key); ?>>

										<?php echo esc_html($settings['button_text']); ?>

										<?php if ($settings['slider_icon']['value']) : ?>
											<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

												<?php if ($is_new || $migrated) :
													Icons_Manager::render_icon($settings['slider_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
												else : ?>
													<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
												<?php endif; ?>

											</span>
										<?php endif; ?>
									</a>
								</div>
							<?php endif; ?>
						</div>

					<?php

					} elseif ("elementor" == $item['source']) {
						echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($item['template_id']);
						echo element_pack_template_edit_link($item['template_id']);
					}
					?>

				</div>
			<?php
					$counter++;
				endforeach;
			?>
		</div>
<?php
				$this->render_loop_footer();
			}
		}
