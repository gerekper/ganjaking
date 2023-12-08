<?php

namespace ElementPack\Modules\CustomGallery\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Utils;

use ElementPack\Modules\CustomGallery\Skins;
use ElementPack\Traits\Global_Mask_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Custom_Gallery extends Module_Base {

	use Global_Mask_Controls;

	public function get_name() {
		return 'bdt-custom-gallery';
	}

	public function get_title() {
		return BDTEP . esc_html__('Custom Gallery', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-custom-gallery';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['custom', 'gallery', 'photo', 'image'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-custom-gallery'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'tilt', 'ep-scripts'];
		} else {
			return ['imagesloaded', 'tilt', 'ep-custom-gallery'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/2fAF8Rt7FbQ';
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Abetis($this));
		$this->add_skin(new Skins\Skin_Fedara($this));
	}

	public function register_controls() {
		$this->start_controls_section(
			'section_custom_gallery_content',
			[
				'label' => esc_html__('Custom Gallery', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Slide Title', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'gallery_image',
			[
				'name'    => 'gallery_image',
				'label'   => esc_html__('Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-' . rand(1, 8) . '.svg',
				],
			]
		);

		$repeater->add_control(
			'image_text',
			[
				'label'   => esc_html__('Content', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Slide Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'image_link_type',
			[
				'label'       => esc_html__('Lightbox/Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'label_block' => true,
				'options'     => [
					''           => esc_html__('Selected Image', 'bdthemes-element-pack'),
					'website'    => esc_html__('Website', 'bdthemes-element-pack'),
					'video'      => esc_html__('Video', 'bdthemes-element-pack'),
					'youtube'    => esc_html__('YouTube', 'bdthemes-element-pack'),
					'vimeo'      => esc_html__('Vimeo', 'bdthemes-element-pack'),
					'google-map' => esc_html__('Google Map', 'bdthemes-element-pack'),
				],
			]
		);

		$repeater->add_control(
			'image_link_video',
			[
				'label'         => __('Video Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => '//clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
				],
				'placeholder'   => '//example.com/video.mp4',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'video',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_youtube',
			[
				'label'         => __('YouTube Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
				],
				'placeholder'   => 'https://youtube.com/watch?v=xyzxyz',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'youtube',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_vimeo',
			[
				'label'         => __('Vimeo Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://vimeo.com/1084537',
				],
				'placeholder'   => 'https://vimeo.com/123123',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'vimeo',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_google_map',
			[
				'label'         => __('Goggle Map Embed URL', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4740.819266853735!2d9.99008871708242!3d53.550454675412404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x3f9d24afe84a0263!2sRathaus!5e0!3m2!1sde!2sde!4v1499675200938',
				],
				'placeholder'   => '//google.com/maps/embed?pb',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'google-map',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_website',
			[
				'name'          => 'image_link_website',
				'label'         => esc_html__('Custom Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'condition'     => [
					'image_link_type' => 'website',
				],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'gallery',
			[
				'label'  => esc_html__('Gallery Items', 'bdthemes-element-pack'),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image_title'   => esc_html__('Image #1', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg'],
					],
					[
						'image_title'   => esc_html__('Image #2', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg'],
					],
					[
						'image_title'   => esc_html__('Image #3', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg'],
					],
					[
						'image_title'   => esc_html__('Image #4', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg'],
					],
					[
						'image_title'   => esc_html__('Image #5', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-5.svg'],
					],
					[
						'image_title'   => esc_html__('Image #6', 'bdthemes-element-pack'),
						'image_text'    => esc_html__('I am item content. Click edit button to change this text.', 'bdthemes-element-pack'),
						'gallery_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-6.svg'],
					],
				],
				'title_field' => '{{{ image_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_gallery_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'      => ['custom'],
				'default'      => 'medium',
				'prefix_class' => 'bdt-custom-gallery--thumbnail-size-',
			]
		);

		$this->add_control(
			'image_mask_popover',
			[
				'label'        => esc_html__('Image Mask', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'template',
				'return_value' => 'yes',
			]
		);

		//Global Image Mask Controls
		$this->register_image_mask_controls();

		$this->add_control(
			'masonry',
			[
				'label' => esc_html__('Masonry', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'item_ratio',
			[
				'label'   => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 265,
				],
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'#bdt-custom-gallery-{{ID}} .bdt-gallery-thumbnail img' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'masonry!' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'overlay_animation',
			[
				'label'   => esc_html__('Overlay Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => element_pack_transition_options(),
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_title',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_text',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_lightbox',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_lightbox',
			[
				'label'   => esc_html__('Lightbox', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'direct_link',
			[
				'label'   => esc_html__('Lightbox Link as Image Link', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => '',
				],
			]
		);

		$this->add_control(
			'external_link',
			[
				'label'   => esc_html__('Show in new Tab', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => '',
					'direct_link'   => 'yes',
				],
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
				'condition' => [
					'show_lightbox' => 'yes',
				]
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
							'name'  => 'show_lightbox',
							'value' => 'yes'
						],
						[
							'name'     => 'link_type',
							'value'    => 'icon'
						]
					]
				]
			]
		);

		$this->add_control(
			'tilt_show',
			[
				'label'   => esc_html__('Tilt Effect', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tilt_scale',
			[
				'label' => esc_html__('Tilt Scale', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'condition' => [
					'tilt_show' => 'yes',
				],
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
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'lightbox_autoplay',
			[
				'label'   => __('Lightbox Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'lightbox_pause',
			[
				'label'   => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_lightbox' => 'yes',
					'lightbox_autoplay' => 'yes'
				],

			]
		);

		$this->add_control(
			'grid_animation_type',
			[
				'label'   => esc_html__('Grid Entrance Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => element_pack_transition_options(),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'grid_anim_delay',
			[
				'label'      => esc_html__('Animation delay', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['ms', ''],
				'range'      => [
					'ms' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'default'    => [
					'unit' => 'ms',
					'size' => 300,
				],
				'condition' => [
					'grid_animation_type!' => '',
				],
			]
		);




		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-custom-gallery.bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__('Row Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery.bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-custom-gallery.bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'label' => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-custom-gallery .bdt-custom-gallery-inner',
			]
		);

		$this->add_control(
			'overlay_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'overlay_heading',
			[
				'label'     => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'overlay_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-overlay' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item .bdt-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_gap',
			[
				'label' => esc_html__('Gap', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item .bdt-overlay' => 'margin: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-thumbnail, {{WRAPPER}} .bdt-custom-gallery .bdt-overlay, {{WRAPPER}} .bdt-custom-gallery .bdt-custom-gallery-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'item_skin_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item'      => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-custom-gallery .bdt-overlay'           => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
				],
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_control(
			'overlay_content_alignment',
			[
				'label'   => __('Content Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'prefix_class' => 'bdt-skin-fedara-style-',
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-overlay' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'overlay_content_position',
			[
				'label'       => __('Content Vertical Position', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'default' => 'middle',
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item .bdt-gallery-item-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
					// '_skin'      => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item .bdt-gallery-item-title',
				'condition' => [
					'show_title' => 'yes',
					// '_skin'      => '',
				],
			]
		);

		$this->add_control(
			'text_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_heading',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item .bdt-gallery-item-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'text_typography',
				'selector'  => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-text',
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Link Style', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_lightbox' => 'yes',
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
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link i, {{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'typography',
				'selector'  => '{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link span.bdt-text, {{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link i',
				'condition' => [
					'show_lightbox' => 'yes',
				]
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
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link:hover span, {{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link:hover i'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-custom-gallery .bdt-gallery-item-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_thumbnail($item) {
		$settings = $this->get_settings_for_display();

?>

		<div class="bdt-gallery-thumbnail">

			<?php
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['gallery_image']['id'], 'thumbnail_size', $settings);
			if (!$thumb_url) {
				printf('<img src="%1$s" alt="%2$s">', $item['gallery_image']['url'], esc_html($item['image_title']));
			} else {
				print(wp_get_attachment_image(
					$item['gallery_image']['id'],
					$settings['thumbnail_size_size'],
					false,
					[
						'alt' => esc_html($item['image_title'])
					]
				));
			}
			?>

		</div>
	<?php
	}

	public function render_title($title) {
		if (!$this->get_settings_for_display('show_title')) {
			return;
		}

		$tag = $this->get_settings_for_display('title_tag');
	?>
		<<?php echo Utils::get_valid_html_tag($tag); ?> class="bdt-gallery-item-title bdt-transition-slide-top-small">
			<?php echo wp_kses($title['image_title'], element_pack_allow_tags('text')); ?>
		</<?php echo Utils::get_valid_html_tag($tag); ?>>
	<?php
	}

	public function render_text($text) {
		if (!$this->get_settings_for_display('show_text')) {
			return;
		}

	?>
		<div class="bdt-gallery-item-text bdt-transition-slide-bottom-small"><?php echo wp_kses_post($text['image_text']); ?></div>
	<?php
	}

	public function rendar_link($content, $element_key) {

		$image_url = wp_get_attachment_image_src($content['gallery_image']['id'], 'full');

		$this->add_render_attribute($element_key, 'data-elementor-open-lightbox', 'no');

		if ($content['image_link_type']) {
			if ('google-map' == $content['image_link_type'] and '' != $content['image_link_google_map']) {
				$this->add_link_attributes($element_key, $content['image_link_google_map']);
				$this->add_render_attribute($element_key, 'data-type', 'iframe');
			} elseif ('video' == $content['image_link_type'] and '' != $content['image_link_video']) {
				$this->add_link_attributes($element_key, $content['image_link_video']);
				$this->add_render_attribute($element_key, 'data-type', 'video');
			} elseif ('youtube' == $content['image_link_type'] and '' != $content['image_link_youtube']) {
				$this->add_link_attributes($element_key, $content['image_link_youtube']);
				$this->add_render_attribute($element_key, 'data-type', false);
			} elseif ('vimeo' == $content['image_link_type'] and '' != $content['image_link_vimeo']) {
				$this->add_link_attributes($element_key, $content['image_link_vimeo']);
				$this->add_render_attribute($element_key, 'data-type', false);
			} else {
				$this->add_link_attributes($element_key, $content['image_link_website']);
				$this->add_render_attribute($element_key, 'data-type', 'iframe');
			}
		} else {
			if (!$image_url) {
				$this->add_link_attributes($element_key, $content['gallery_image']);
			} else {
				$this->add_render_attribute($element_key, 'href', $image_url[0]);
			}
		}
	}

	public function render_overlay($content, $element_key) {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_title'] and !$settings['show_text'] and !$settings['show_lightbox']) {
			return;
		}

		$this->add_render_attribute('overlay-settings', 'class', ['bdt-overlay', 'bdt-overlay-default', 'bdt-position-cover'], true);

		if ($settings['overlay_animation']) {
			$this->add_render_attribute('overlay-settings', 'class', 'bdt-transition-' . $settings['overlay_animation']);
		}

	?>
		<div <?php echo $this->get_render_attribute_string('overlay-settings'); ?>>
			<div class="bdt-custom-gallery-content">
				<div class="bdt-custom-gallery-content-inner">



					<?php if ($settings['show_lightbox']) :

						$this->rendar_link($content, $element_key);


						$this->add_render_attribute($element_key, 'class', ['bdt-gallery-item-link', 'bdt-gallery-lightbox-item']);

						$this->add_render_attribute($element_key, 'data-caption', $content['image_title']);

						$icon = $settings['icon'] ?: 'plus';

					?>
						<div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
							<a <?php echo $this->get_render_attribute_string($element_key); ?>>
								<?php if ('icon' == $settings['link_type']) : ?>
									<i class="ep-icon-<?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
								<?php elseif ('text' == $settings['link_type']) : ?>
									<span class="bdt-text"><?php esc_html_e('ZOOM', 'bdthemes-element-pack'); ?></span>
								<?php endif; ?>
							</a>
						</div>
					<?php endif; ?>

					<?php
					$this->render_title($content);
					$this->render_text($content);
					?>

				</div>
			</div>
		</div>
	<?php
	}

	public function render_header($skin = 'default') {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->add_render_attribute('custom-gallery', 'id', 'bdt-custom-gallery-' . $id);
		$this->add_render_attribute('custom-gallery', 'class', ['bdt-custom-gallery', 'bdt-skin-' . esc_attr($skin)]);
		$this->add_render_attribute('custom-gallery', 'class', ['bdt-grid', 'bdt-grid-small']);

		if ('yes' === $settings['masonry']) {
			$this->add_render_attribute('custom-gallery', 'data-bdt-grid', 'masonry: true');
		}

		if ($settings['show_lightbox']) {
			$this->add_render_attribute('custom-gallery', 'data-bdt-lightbox', 'toggle: .bdt-gallery-lightbox-item; animation:' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->add_render_attribute('custom-gallery', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->add_render_attribute('custom-gallery', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}


		if ($settings['grid_animation_type'] !== '') {
			$this->add_render_attribute('custom-gallery', 'data-bdt-scrollspy', 'cls: bdt-animation-' . esc_attr($settings['grid_animation_type']) . ';');
			$this->add_render_attribute('custom-gallery', 'data-bdt-scrollspy', 'delay: ' . esc_attr($settings['grid_anim_delay']['size']) . ';');
			$this->add_render_attribute('custom-gallery', 'data-bdt-scrollspy', 'target: > div > .bdt-custom-gallery-inner' . ';');
		}

		$this->add_render_attribute(
			[
				'custom-gallery' => [
					'data-settings' => [
						wp_json_encode([
							'id'		=> '#bdt-custom-gallery-' . $this->get_id(),
							'tiltShow'  => (isset($settings['tilt_show']) && $settings['tilt_show'] == 'yes') ? true : false
						]),
					],
				],
			]
		);

	?>
		<div <?php echo $this->get_render_attribute_string('custom-gallery'); ?>>
		<?php
	}

	public function render_footer() {

		?>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$columns 		= isset($settings['columns']) ? $settings['columns'] : 3;

		$this->add_render_attribute('custom-gallery-item', 'class', ['bdt-gallery-item', 'bdt-transition-toggle']);

		$this->add_render_attribute('custom-gallery-item', 'class', 'bdt-width-1-' . $columns_mobile);
		$this->add_render_attribute('custom-gallery-item', 'class', 'bdt-width-1-' . $columns_tablet . '@s');
		$this->add_render_attribute('custom-gallery-item', 'class', 'bdt-width-1-' . $columns . '@m');

		$this->add_render_attribute('custom-gallery-inner', 'class', 'bdt-custom-gallery-inner' . $image_mask);

		if ('yes' === $settings['tilt_show']) {
			$this->add_render_attribute('custom-gallery-inner', 'data-tilt', '');
		}

		$this->render_header();
		foreach ($settings['gallery'] as $index => $item) :

		?>
			<div <?php echo $this->get_render_attribute_string('custom-gallery-item'); ?>>
				<div <?php echo $this->get_render_attribute_string('custom-gallery-inner'); ?>>

					<?php $this->rendar_link($item, 'gallery-item-' . $index); ?>

					<?php if ('yes' !== $settings['show_lightbox'] and  $settings['direct_link']) : ?>

						<?php
						if ($settings['external_link']) {
							$this->add_render_attribute('gallery-item-' . $index, 'target', '_blank');
						}
						?>

						<a <?php echo $this->get_render_attribute_string('gallery-item-' . $index); ?>>
						<?php endif; ?>

						<?php
						$this->render_thumbnail($item);
						$this->render_overlay($item, 'overlay-item-' . $index);
						?>

						<?php if ('yes' !== $settings['show_lightbox'] and $settings['direct_link']) : ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

		<?php endforeach; ?>

<?php $this->render_footer();
	}
}
