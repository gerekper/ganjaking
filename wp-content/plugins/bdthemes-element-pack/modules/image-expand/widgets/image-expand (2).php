<?php

namespace ElementPack\Modules\ImageExpand\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Repeater;
use ElementPack\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Image_Expand extends Module_Base {

	public function get_name() {
		return 'bdt-image-expand';
	}

	public function get_title() {
		return BDTEP . esc_html__('Image Expand', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-image-expand';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['fancy', 'effects', 'image', 'accordion', 'hover', 'slideshow', 'wide', 'box', 'animated boxs', 'expand'];
	}

	public function is_reload_preview_required() {
		return false;
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-image-expand'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['gsap', 'split-text-js', 'ep-scripts'];
		} else {
			return ['gsap', 'split-text-js', 'ep-image-expand'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/gNg7vpypycY';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_fancy_layout',
			[
				'label' => __('Image Expand', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'skin_type',
			[
				'label'	   => __('Style', 'bdthemes-element-pack') . BDTEP_UC,
				'type' 	   => Controls_Manager::SELECT,
				'options'  => [
					'default' 	=> __('Horizontal', 'bdthemes-element-pack'),
					'vertical' 	=> __('Vertical', 'bdthemes-element-pack'),
					'sliding-box' 	=> __('Sliding Box', 'bdthemes-element-pack'),
				],
				'default'  => 'default',
				'tablet_default'  => 'default',
				'mobile_default'  => 'default',
				'prefix_class' => 'skin-%s-',
				'selectors_dictionary' => [
					'default' => 'flex-direction: unset;',
					'vertical' => 'flex-direction: column;',
					'sliding-box' => 'flex-direction: unset;',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand' => '{{VALUE}};',
				],
				'render_type'     => 'template',
				'style_transfer'  => true,
			]
		);

		$this->add_control(
			'hr_divider',
			[
				'type' 	   => Controls_Manager::DIVIDER,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('items_tabs_controls');

		$repeater->start_controls_tab(
			'tab_item_content',
			[
				'label' => __('Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'image_expand_title',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('Tab Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'image_expand_sub_title',
			[
				'label'       => __('Sub Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'image_expand_button',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'         => esc_html__('Button Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => '#'],
				'show_external' => false,
				'dynamic'       => ['active' => true],
				'condition'     => [
					'image_expand_button!' => ''
				]
			]
		);

		$repeater->add_control(
			'slide_image',
			[
				'label'   => esc_html__('Background Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-' . rand(1, 6) . '.svg',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_item_content_optional',
			[
				'label' => __('Optional', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'title_link',
			[
				'label'         => esc_html__('Title Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => ''],
				'show_external' => false,
				'dynamic'       => ['active' => true],
				'condition'     => [
					'image_expand_title!' => ''
				]
			]
		);

		$repeater->add_control(
			'image_expand_text',
			[
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => __('Box Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'image_expand_items',
			[
				'label'   => esc_html__('Items', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image_expand_sub_title'   => __('This is a label', 'bdthemes-element-pack'),
						'image_expand_title'   	  => __('Image Expand Item One', 'bdthemes-element-pack'),
						'image_expand_text' 	  => __('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni.', 'bdthemes-element-pack'),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg']
					],
					[
						'image_expand_sub_title'   => __('This is a label', 'bdthemes-element-pack'),
						'image_expand_title'   => __('Image Expand Item Two', 'bdthemes-element-pack'),
						'image_expand_text' => __('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni.', 'bdthemes-element-pack'),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg']
					],
					[
						'image_expand_sub_title'   => __('This is a label', 'bdthemes-element-pack'),
						'image_expand_title'   => __('Image Expand Item Three', 'bdthemes-element-pack'),
						'image_expand_text' => __('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni.', 'bdthemes-element-pack'),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg']
					],
					[
						'image_expand_sub_title'   => __('This is a label', 'bdthemes-element-pack'),
						'image_expand_title'   => __('Image Expand Item Four', 'bdthemes-element-pack'),
						'image_expand_text' => __('Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni.', 'bdthemes-element-pack'),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg']
					],
				],
				'title_field' => '{{{ image_expand_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_hover_box',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'image_expand_min_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_expand_width',
			[
				'label' => esc_html__('Content Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'      => ['custom'],
				'default'      => 'full',
				'prefix_class' => 'thumbnail-size-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'divider_hr',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'items_content_position',
			[
				'label'   => __('Content Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'row',
				'options' => [
					'row-reverse' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'row' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-item' => 'flex-direction: {{VALUE}};',
				],
				'prefix_class' => 'ep-img-position--',
				'render_type'     => 'template',
				'style_transfer'  => true,
				'condition' => [
					'skin_type' => 'sliding-box'
				]
			]
		);

		$this->add_responsive_control(
			'items_content_align',
			[
				'label'   => __('Text Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_content_vertical_align',
			[
				'label'   => __('Vertical Alignment', 'bdthemes-element-pack') . BDTEP_NC,
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
					'{{WRAPPER}} .bdt-ep-image-expand-content' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
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
			'show_sub_title',
			[
				'label'   => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
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
			'show_text',
			[
				'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_hide_on',
			[
				'label'       => __('Text Hide On', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => false,
				'options'     => [
					'desktop' => __('Desktop', 'bdthemes-element-pack'),
					'tablet'  => __('Tablet', 'bdthemes-element-pack'),
					'mobile'  => __('Mobile', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
				'condition' => [
					'show_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'animation_heading',
			[
				'label'   => esc_html__('A n i m a t i o n', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'default_animation_type',
			[
				'label'   => esc_html__('Basic Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => element_pack_transition_options(),
				'condition' => [
					'animation_status!' => 'yes'
				]
			]
		);

		$this->add_control(
			'animation_status',
			[
				'label'   => esc_html__('Advanced Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'animation_of',
			[
				'label'	   => __('Animation Of', 'bdthemes-element-pack'),
				'type' 	   => Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => [
					'.bdt-ep-image-expand-sub-title' 	=> __('Sub Title', 'bdthemes-element-pack'),
					'.bdt-ep-image-expand-title'  		=> __('Title', 'bdthemes-element-pack'),
					'.bdt-ep-image-expand-text' 		=> __('Text', 'bdthemes-element-pack'),
				],
				'default'  => ['.bdt-ep-image-expand-sub-title', '.bdt-ep-image-expand-title', '.bdt-ep-image-expand-text'],
				'condition' => [
					'animation_status' => 'yes'
				]
			]
		);

		//Lightbox
		$this->add_control(
			'show_lightbox',
			[
				'label'   => esc_html__('Show Lightbox', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Lightbox
		$this->start_controls_section(
			'section_expand_lightbox',
			[
				'label' => __('Lightbox', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__('Link Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon' => esc_html__('Icon', 'bdthemes-element-pack'),
					'text' => esc_html__('Text', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'plus',
				'options' => [
					'search' => [
						'icon' => 'eicon-search',
					],
					'plus-circle' => [
						'icon' => 'eicon-plus-circle-o',
					],
					'plus' => [
						'icon' => 'eicon-plus',
					],
					'link' => [
						'icon' => 'eicon-link',
					],
					'play-circle' => [
						'icon' => 'eicon-play',
					],
					'play' => [
						'icon' => 'eicon-caret-right',
					],
				],
				'conditions' => [
					'terms'    => [
						[
							'name'     => 'link_type',
							'value'    => 'icon'
						]
					]
				]
			]
		);
		$this->add_control(
			'lightbox_animation',
			[
				'label'   => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lightbox_autoplay',
			[
				'label'   => __('Lightbox Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,

			]
		);

		$this->add_control(
			'lightbox_pause',
			[
				'label'   => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'lightbox_autoplay' => 'yes'
				],

			]
		);

		$this->add_control(
			'lightbox_placement',
			[
				'label'     => esc_html__('Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => [
					'top-left'    => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top-right'   => esc_html__('Top Right', 'bdthemes-element-pack'),
					'bottom-left' => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-right' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
				],
				'selectors_dictionary' => [
					'top-left' => 'left: 0;',
					'top-right' => 'right: 0;',
					'bottom-left' => 'left: 0; bottom: 0;',
					'bottom-right' => 'right: 0; bottom: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox' => '{{VALUE}};',
				],
				'condition' => [
					'skin_type!' => 'sliding-box'
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'anim_option',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'condition' => [
					'animation_status' => 'yes',
				]
			]
		);


		$this->add_control(
			'animation_on',
			[
				'label'   => __('Animation On', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'words',
				'options' => [
					'chars'   => 'Chars',
					'words'   => 'Words',
					'lines'   => 'Lines',
				],
			]
		);

		$this->add_control(
			'animation_options',
			[
				'label' => __('Animation Options', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('Default', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'anim_perspective',
			[
				'label' => esc_html__('Perspective', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'placeholder' => '400',
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 400,
					],
				],
			]
		);

		$this->add_control(
			'anim_duration',
			[
				'label' => esc_html__('Transition Duration', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'step' => 0.1,
						'max' => 1,
					],
				],
			]
		);

		$this->add_control(
			'anim_scale',
			[
				'label' => esc_html__('Scale', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
			]
		);

		$this->add_control(
			'anim_rotationY',
			[
				'label' => esc_html__('rotationY', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
					],
				],
			]
		);

		$this->add_control(
			'anim_rotationX',
			[
				'label' => esc_html__('rotationX', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
					],
				],
			]
		);

		$this->add_control(
			'anim_transform_origin',
			[
				'label'   => esc_html__('Transform Origin', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => '0% 50% -50',
			]
		);


		$this->end_popover();

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_image_expand_style',
			[
				'label' => __('Image Expand Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_expand_overlay_color',
			[
				'label'     => __('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-item:before'  => 'background: {{VALUE}};',
				],
				'condition' => [
					'skin_type!' => 'sliding-box'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliding_overlay_background',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}}.skin--sliding-box .bdt-ep-image-expand-img:before',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Overlay Color', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'skin_type' => 'sliding-box'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-item',
				'condition' => [
					'skin_type' => 'sliding-box'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_content_padding',
			[
				'label'      => __('Content Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-expand-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-title' => 'color: {{VALUE}}; -webkit-text-stroke-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-title' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'label' => __('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => ['yes'],
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-text, {{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-text *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-text' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-text',
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
					'{{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-expand-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-ep-image-expand-button a',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-expand-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-image-expand-button a'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'border_radius_advanced_show' => 'yes',
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
					'{{WRAPPER}} .bdt-ep-image-expand-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-image-expand-button a',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand .bdt-ep-image-expand-button a:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-expand-button a:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-button a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_lightbox',
			[
				'label'     => esc_html__('Lightbox', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_lightbox' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_lightbox_style');

		$this->start_controls_tab(
			'tab_lightbox_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'lightbox_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox i, {{WRAPPER}} .bdt-ep-image-expand-lightbox span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'lightbox_background',
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-lightbox',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'lightbox_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-image-expand-lightbox',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'lightbox_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lightbox_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-lightbox',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'lightbox_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-expand-lightbox span.bdt-text, {{WRAPPER}} .bdt-ep-image-expand-lightbox i',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_lightbox_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'lightbox_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox:hover span, {{WRAPPER}} .bdt-ep-image-expand-lightbox:hover i'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'lightbox_background_hover_color',
				'selector' => '{{WRAPPER}} .bdt-ep-image-expand-lightbox:hover',
			]
		);

		$this->add_control(
			'lightbox_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'lightbox_border_border!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-expand-lightbox:hover' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_lightbox($item) {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_lightbox']) {
			return;
		}

		$image_url = wp_get_attachment_image_src($item['slide_image']['id'], 'full');

		$this->add_render_attribute('lightbox', 'data-elementor-open-lightbox', 'no', true);

		if (!$image_url) {
			$this->add_render_attribute('lightbox', 'href', $item['slide_image']['url'], true);
		} else {
			$this->add_render_attribute('lightbox', 'href', $image_url[0], true);
		}


		$this->add_render_attribute('lightbox', 'class', 'bdt-ep-image-expand-lightbox', true);

		$this->add_render_attribute('lightbox', 'data-caption', $item['image_expand_title'], true);

		$icon = $settings['icon'] ?: 'plus';

		?>
		<div class="bdt-ie-lightbox-wrap">
			<a <?php echo $this->get_render_attribute_string('lightbox'); ?>>
				<?php if ('icon' == $settings['link_type']) : ?>
					<i class="ep-icon-<?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
				<?php elseif ('text' == $settings['link_type']) : ?>
					<span class="bdt-text"><?php esc_html_e('ZOOM', 'bdthemes-element-pack'); ?></span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}

	public function render_expand_content($item) {
		$settings = $this->get_settings_for_display();

		$text_hide_on_setup = '';
		if (!empty($settings['text_hide_on'])) {
			foreach ($settings['text_hide_on'] as $element) {

				if ($element == 'desktop') {
					$text_hide_on_setup .= ' bdt-desktop';
				}
				if ($element == 'tablet') {
					$text_hide_on_setup .= ' bdt-tablet';
				}
				if ($element == 'mobile') {
					$text_hide_on_setup .= ' bdt-mobile';
				}
			}
		}

		$this->add_render_attribute('bdt-ep-image-expand-title', 'class', 'bdt-ep-image-expand-title', true);

	?>
		<div class="bdt-ep-image-expand-content">
			<?php if ($item['image_expand_sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
				<div class="bdt-ep-image-expand-sub-title">
					<?php echo wp_kses($item['image_expand_sub_title'], element_pack_allow_tags('title')); ?>
				</div>
			<?php endif; ?>

			<?php if ($item['image_expand_title'] && ('yes' == $settings['show_title'])) : ?>
				<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-ep-image-expand-title'); ?>>
					<?php if ('' !== $item['title_link']['url']) : ?>
						<a href="<?php echo esc_url($item['title_link']['url']); ?>">
						<?php endif; ?>
						<?php echo wp_kses($item['image_expand_title'], element_pack_allow_tags('title')); ?>
						<?php if ('' !== $item['title_link']['url']) : ?>
						</a>
					<?php endif; ?>
				</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
			<?php endif; ?>

			<?php if ($item['image_expand_text'] && ('yes' == $settings['show_text'])) : ?>
				<div class="bdt-ep-image-expand-text <?php echo esc_attr($text_hide_on_setup); ?>">
					<?php echo $this->parse_text_editor($item['image_expand_text']); ?>
				</div>
			<?php endif; ?>

			<?php if ($item['image_expand_button'] && ('yes' == $settings['show_button'])) : ?>
				<div class="bdt-ep-image-expand-button">
					<?php if ('' !== $item['button_link']['url']) : ?>
						<a href="<?php
									if ($item['button_link']['url'] == '#') {
										echo 'javascript:void(0);';
									} else {
										echo esc_url($item['button_link']['url']);
									}
									?>">
						<?php endif; ?>
						<?php echo wp_kses_post($item['image_expand_button']); ?>
						<?php if ('' !== $item['button_link']['url']) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php
	}

	public function render_image($item) {
		$settings = $this->get_settings_for_display();

	?>
		<div class="bdt-ep-image-expand-img">
			<?php
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['slide_image']['id'], 'thumbnail_size', $settings);
			if (!$thumb_url) {
				printf('<img src="%1$s" alt="%2$s">', $item['slide_image']['url'], esc_html($item['image_expand_title']));
			} else {
				print(wp_get_attachment_image(
					$item['slide_image']['id'],
					$settings['thumbnail_size_size'],
					false,
					[
						'alt' => esc_html($item['image_expand_title'])
					]
				));
			}
			?>

			<?php $this->render_lightbox($item); ?>

		</div>
	<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$animation_of = (isset($settings['animation_of'])) ? implode(", ", $settings['animation_of']) : '.bdt-ep-image-expand-sub-title';

		$animation_of = (strlen($animation_of)) > 0 ? $animation_of : '.bdt-ep-image-expand-sub-title';

		$animation_status = ($settings['animation_status'] == 'yes' ? 'yes' : 'no');

		if ($settings['animation_status'] == 'yes') {
			$this->add_render_attribute(
				[
					'image-expand' => [
						'id' => 'bdt-ep-image-expand-' . $this->get_id(),
						'class' => 'bdt-ep-image-expand bdt-ep-image-expand-default',
						'data-settings' => [
							wp_json_encode([
								'wide_id' 				=> 'bdt-ep-image-expand-' . $this->get_id(),
								'animation_status'		=> $animation_status,
								'animation_of'			=> $animation_of,
								'animation_on'     		=> $settings['animation_on'],
								'anim_perspective'      => ($settings['anim_perspective']['size']) ? $settings['anim_perspective']['size'] : 400,
								'anim_duration'    		=> ($settings['anim_duration']['size']) ? $settings['anim_duration']['size'] : 0.1,
								'anim_scale'    		=> ($settings['anim_scale']['size']) ? $settings['anim_scale']['size'] : 0,
								'anim_rotation_y'    	=> ($settings['anim_rotationY']['size']) ? $settings['anim_rotationY']['size'] : 80,
								'anim_rotation_x'    	=> ($settings['anim_rotationX']['size']) ? $settings['anim_rotationX']['size'] : 180,
								'anim_transform_origin' => ($settings['anim_transform_origin']) ? $settings['anim_transform_origin'] : '0% 50% -50',
							])
						]
					]
				]
			);
		} else {
			$this->add_render_attribute(
				[
					'image-expand' => [
						'id' => 'bdt-ep-image-expand-' . $this->get_id(),
						'class' => 'bdt-ep-image-expand bdt-ep-image-expand-default',
						'data-settings' => [
							wp_json_encode(array_filter([
								'wide_id' => 'bdt-ep-image-expand-' . $this->get_id(),
								'animation_status'		=> $animation_status,
								'default_animation_type' => (strlen($settings['default_animation_type']) > 0 ? $settings['default_animation_type'] : 'fade')
							]))
						]
					]
				]
			);
		}

		if ($settings['show_lightbox']) {
			$this->add_render_attribute('image-expand', 'data-bdt-lightbox', 'toggle: .bdt-ep-image-expand-lightbox; animation:' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->add_render_attribute('image-expand', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->add_render_attribute('image-expand', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}

	?>

		<div <?php echo ($this->get_render_attribute_string('image-expand')); ?>>
			<?php foreach ($settings['image_expand_items'] as $index => $item) :

				$slide_image = Group_Control_Image_Size::get_attachment_image_src($item['slide_image']['id'], 'thumbnail_size', $settings);
				if (!$slide_image) {
					$slide_image = $item['slide_image']['url'];
				}

				$this->add_render_attribute('image-expand-item', 'class', 'bdt-ep-image-expand-item', true);

				$this->add_render_attribute('image-expand-item', 'id', $this->get_id() . '-' . $item['_id'], true);

			?>

				<?php if ($settings['skin_type'] !== 'sliding-box') : ?>
					<div <?php echo $this->get_render_attribute_string('image-expand-item'); ?> style="background-image: url('<?php echo esc_url($slide_image); ?>');">
						<?php $this->render_lightbox($item); ?>
						<?php $this->render_expand_content($item); ?>
					</div>
				<?php else : ?>
					<div <?php echo ($this->get_render_attribute_string('image-expand-item')); ?>>
						<?php $this->render_image($item); ?>
						<?php $this->render_expand_content($item); ?>
					</div>
				<?php endif; ?>

			<?php endforeach; ?>
		</div>
<?php
	}
}
