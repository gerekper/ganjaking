<?php

namespace ElementPack\Modules\VideoGallery\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use ElementPack\Utils;

use Elementor\Repeater;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Video_Gallery extends Module_Base {

	public function get_name() {
		return 'bdt-video-gallery';
	}

	public function get_title() {
		return BDTEP . esc_html__('Video Gallery', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-video-gallery';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['video', 'gallery'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-video-gallery'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['rvslider', 'ep-scripts'];
		} else {
			return ['rvslider', 'ep-video-gallery'];
		}
	}


	public function get_custom_help_url() {
		return 'https://youtu.be/wbkou6p7l3s';
	}

	public function register_controls() {

		$this->start_controls_section(
			'section_custom_gallery_content',
			[
				'label' => esc_html__('Video Gallery', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'thumb_layout',
			[
				'label'   => esc_html__('Thumb Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical'   => esc_html__('Vertical', 'bdthemes-element-pack'),
					'horizontal' => esc_html__('Horizontal', 'bdthemes-element-pack'),
				],
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('tabs_content_video_gallery');

		$repeater->start_controls_tab(
			'tab_video_gallery_source',
			[
				'label' => esc_html__('Source', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'source_type',
			[
				'label'       => esc_html__('Video Type', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'remote_url',
				'label_block' => true,
				'options'     => [
					'remote_url' => esc_html__('Remote Video', 'bdthemes-element-pack'),
					'hosted_url' => esc_html__('Local Video', 'bdthemes-element-pack'),
				],
			]
		);

		$repeater->add_control(
			'remote_url',
			[
				'type'          => Controls_Manager::URL,
				'label'         => __('Video Source', 'bdthemes-element-pack'),
				'label_block'   => true,
				'show_external' => false,
				'placeholder'   => __('https://www.youtube.com/watch?v=vN9DnFiRMX0&feature=youtu.be', 'bdthemes-element-pack'),
				'dynamic'       => ['active' => true],
				'default'       => [
					'url' => __('https://www.youtube.com/watch?v=vN9DnFiRMX0&feature=youtu.be', 'bdthemes-element-pack'),
				],
				'condition'     => [
					'source_type' => 'remote_url',
				],
			]
		);

		$repeater->add_control(
			'hosted_url',
			[
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type' => 'video',
				'condition' => [
					'source_type' => 'hosted_url'
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_video_gallery_desc',
			[
				'label' => esc_html__('Description', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title and Description', 'bdthemes-element-pack'),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default' => esc_html__('Video Title', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'desc',
			[
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Women typing keyboard', 'bdthemes-element-pack'),
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_video_gallery_poster',
			[
				'label' => esc_html__('Poster', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'poster',
			[
				'label'   => esc_html__('Poster', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'video_gallery',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'title'      => esc_html__('Youtube Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Women typing keyboard', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://www.youtube.com/watch?v=vN9DnFiRMX0&feature=youtu.be'],
					],
					[
						'title'      => esc_html__('Vimeo Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Auto VR Concept', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://vimeo.com/258349022'],
					],
					[
						'title'      => esc_html__('Wista Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Brendan - Make It Clap', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://home.wistia.com/medias/e4a27b971d'],
					],
					[
						'title'      => esc_html__('Dailymotion Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Drama B - DREAMS', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'http://www.dailymotion.com/embed/video/x2ioxee'],
					],
					[
						'title'      => esc_html__('MP4 Format Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('BdThemes Intro', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://bdthemes.net/demo/wordpress/element-pack/wp-content/uploads/2018/02/countdown.mp4'],
					],
					[
						'title'      => esc_html__('WEBM Format Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Fish Frenzy - Oceans Clip', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://s3.amazonaws.com/fooplugins/rvs/oceans-clip.webm'],
					],
					[
						'title'      => esc_html__('OGV Format Video', 'bdthemes-element-pack'),
						'desc'       => esc_html__('Fish Frenzy - Oceans Clip', 'bdthemes-element-pack'),
						'remote_url' => ['url' => 'https://s3.amazonaws.com/fooplugins/rvs/oceans-clip.ogv'],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_video',
			[
				'label' => esc_html__('Video', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_video_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_video_title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_video_title' => 'yes',
				]
			]
		);

		///

		$this->add_control(
			'show_video_desc',
			[
				'label'   => esc_html__('Description', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_thumbnail',
			[
				'label' => esc_html__('Playlist', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_thumbnail_thumb',
			[
				'label'        => esc_html__('Thumbnail', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				// 'prefix_class' => 'bdt-video-gallery-thumbnail-',
			]
		);

		$this->add_control(
			'show_thumbnail_title',
			[
				'label'        => esc_html__('Title', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				// 'prefix_class' => 'bdt-video-gallery-title-',
			]
		);

		$this->add_control(
			'thumbnail_title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_thumbnail_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_thumbnail_desc',
			[
				'label'        => esc_html__('Description', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				// 'prefix_class' => 'bdt-video-gallery-desc-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_play_btn_on_hover',
			[
				'label' => esc_html__('Play Button on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		/*$this->add_control(
			'play_on_click_thumb',
			[
				'label' => esc_html__( 'Play on Click Thumb', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);*/

		$this->add_control(
			'continuous_play',
			[
				'label' => esc_html__('Play when Navigate', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'arrow_always_show',
			[
				'label' => esc_html__('Show Arrows Always?', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-show-arrows-always-'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_video_title',
			[
				'label'     => esc_html__('Video Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_video_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'video_title_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-item-text h2' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'video_title_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-item-text h2' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'video_title_text_shadow',
				'selector' => '{{WRAPPER}} .rvs-item-text h2',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'video_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .rvs-item-text h2',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_video_desc',
			[
				'label'     => esc_html__('Video Description', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_video_desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'video_desc_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vg-video-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'video_desc_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vg-video-desc' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'video_desc_space',
			[
				'label' => __('Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 0,
						'min'  => 200,
						'step' => 2
					]
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-vg-video-desc' => 'margin-top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'video_desc_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-vg-video-desc',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_poster',
			[
				'label'     => __('Poster', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'poster_bg_position',
			[
				'label'              => esc_html__('Position', 'bdthemes-element-pack') . BDTEP_NC,
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [
					'' => esc_html__('Default', 'bdthemes-element-pack'),
					'center center' => esc_html__('Center Center', 'bdthemes-element-pack'),
					'center left' => esc_html__('Center Left', 'bdthemes-element-pack'),
					'center right' => esc_html__('Center Right', 'bdthemes-element-pack'),
					'top center' => esc_html__('Top Center', 'bdthemes-element-pack'),
					'top left' => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top right' => esc_html__('Top Right', 'bdthemes-element-pack'),
					'bottom center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
					'bottom left' => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom right' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-video-gallery .rvs-item' => 'background-position: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'poster_bg_size',
			[
				'label'              => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [
					'' => esc_html__('Default', 'bdthemes-element-pack'),
					'auto' => esc_html__('Auto', 'bdthemes-element-pack'),
					'cover' => esc_html__('Cover', 'bdthemes-element-pack'),
					'contain' => esc_html__('Contain', 'bdthemes-element-pack'),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-video-gallery .rvs-item' => 'background-size: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumb_item',
			[
				'label' => __('Playlist', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_thumb_item_style');

		$this->start_controls_tab(
			'tab_thumb_item_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'thumb_item_bg',
			[
				'label'      => __('Background', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_item_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-container a.rvs-nav-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_item_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumb_item_border',
				'selector'  => '{{WRAPPER}} .rvs-nav-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumb_item_radius',
			[
				'label'      => __('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_item_shadow',
				'selector' => '{{WRAPPER}} .rvs-nav-item span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumb_item_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'thumb_item_hover_bg',
			[
				'label'      => __('Background', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'thumb_item_hover_border_color',
			[
				'label'      => __('Border Color', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item:hover' => 'border-color: {{VALUE}} !important;',
				]
			]
		);

		$this->add_responsive_control(
			'thumb_item_hover_radius',
			[
				'label'      => __('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_item_hover_shadow',
				'selector' => '{{WRAPPER}} .rvs-nav-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumb_item_active',
			[
				'label' => __('Active', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'thumb_item_active_bg',
			[
				'label'      => __('Background', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item.rvs-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'thumb_item_active_border_color',
			[
				'label'      => __('Border Color', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item.rvs-active' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail',
			[
				'label'     => __('Thumbnail', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnail_thumb' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_width',
			[
				'label' => __('Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item span' => 'width: {{SIZE}}{{UNIT}};margin-left: auto;margin-right: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('thumbnail_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item span' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .rvs-nav-item span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item span:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .rvs-nav-item span:hover',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration (s)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item span' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumbnail_border',
				'selector'  => '{{WRAPPER}} .rvs-nav-item span',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbnail_radius',
			[
				'label'      => __('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .rvs-nav-item span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumbnail_shadow',
				'selector' => '{{WRAPPER}} .rvs-nav-item span',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail_title',
			[
				'label'     => esc_html__('Thumbnail Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnail_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_title_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'thumbnail_title_hover_color',
			[
				'label'     => esc_html__('Text Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item-title:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'thumbnail_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .rvs-nav-container .rvs-nav-item-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail_desc',
			[
				'label'     => esc_html__('Thumbnail Description', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnail_desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_desc_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item-credits' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'thumbnail_desc_hover_color',
			[
				'label'     => esc_html__('Text Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item-credits:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'thumbnail_desc_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .rvs-nav-container .rvs-nav-item-credits',
			]
		);

		$this->add_responsive_control(
			'thumbnail_desc_space',
			[
				'label'     => __('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .rvs-nav-item-credits' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_play_button',
			[
				'label' => __('Play/Pause Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_play_button');

		$this->start_controls_tab(
			'tab_play_btn_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'play_btn_icon_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-play-video' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_btn_bg',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-play-video' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'play_btn_size',
			[
				'label' => __('Size (px)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 20,
						'max'  => 200,
						'step' => 2
					]
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-container a.rvs-play-video' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'play_btn_border',
				'separator'   => 'before',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .rvs-play-video'
			]
		);

		$this->add_responsive_control(
			'play_btn_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .rvs-play-video' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_btn_shadow',
				'selector' => '{{WRAPPER}} .rvs-play-video',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_play_btn_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'play_btn_hover_icon_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-play-video:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_btn_hover_bg',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvs-play-video:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_btn_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'play_btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .rvs-play-video:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_btn_hover_shadow',
				'selector' => '{{WRAPPER}} .rvs-play-video:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->render_header();

		foreach ($settings['video_gallery'] as $video) :

			$video_poster = ($video['poster']['url']) ? $video['poster']['url'] : BDTEP_ASSETS_URL . 'images/video-thumbnail.svg';
			$video_source = $video['remote_url']['url'];

			if ('hosted_url' == $video['source_type']) {
				$video_source = $video['hosted_url']['url'];
			} else {
				$youtube_id = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['remote_url']['url'], $match)) ? $match[1] : false;

				$vimeo_id = (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video['remote_url']['url'], $match)) ? $match[3] : false;

				if ($youtube_id) {
					$video_source    = 'https://www.youtube.com/watch?v=' . $youtube_id;
					$video_poster    = ($video['poster']['url']) ? $video['poster']['url'] : 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg';
				} elseif ($vimeo_id) {
					$video_source    = 'https://vimeo.com/' . $vimeo_id;
					$video_poster    = ($video['poster']['url']) ? $video['poster']['url'] : 'https://i.vimeocdn.com/video/' . $vimeo_id . '.webp?mw=960&mh=540';
				}
			}

?>
			<!-- items go here -->
			<div class="rvs-item" style="background-image: url(<?php echo esc_url($video_poster); ?>)">

				<?php if ($settings['show_video_title'] or $settings['show_video_desc']) : ?>
					<div class="rvs-item-text">

						<?php if ($settings['show_video_title']) : ?>
							<<?php echo Utils::get_valid_html_tag($settings['show_video_title_tags']); ?> class="bdt-vg-video-title"><?php echo esc_html($video['title']); ?></<?php echo Utils::get_valid_html_tag($settings['show_video_title_tags']); ?>>
						<?php endif; ?>

						<?php if ($settings['show_video_desc']) : ?>
							<div class="bdt-vg-video-desc"><?php echo wp_kses_post($video['desc']); ?></div>
						<?php endif; ?>

					</div>
				<?php endif; ?>

				<a href="<?php echo esc_url($video_source); ?>" class="rvs-play-video"></a>
			</div>

		<?php endforeach; ?>


		</div>
		</div>

		<?php if ('yes' == $settings['show_thumbnail_thumb'] or 'yes' == $settings['show_thumbnail_title'] or 'yes' == $settings['show_thumbnail_desc']) : ?>
			<div class="rvs-nav-container">
				<a class="rvs-nav-prev"></a>
				<div class="rvs-nav-stage">

					<?php foreach ($settings['video_gallery'] as $video) :

						$video_thumbnail = ($video['poster']['url']) ? $video['poster']['url'] : BDTEP_ASSETS_URL . 'images/video-thumbnail.svg';

						$youtube_id = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['remote_url']['url'], $match)) ? $match[1] : false;

						$vimeo_id = (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video['remote_url']['url'], $match)) ? $match[3] : false;

						if ($youtube_id) {
							$video_thumbnail = ($video['poster']['url']) ? $video['poster']['url'] : 'https://img.youtube.com/vi/' . $youtube_id . '/default.jpg';
						} elseif ($vimeo_id) {
							$video_thumbnail = ($video['poster']['url']) ? $video['poster']['url'] : 'https://i.vimeocdn.com/video/' . $vimeo_id . '.webp?mw=60&mh=60';
						}

					?>
						<!-- nav items go here -->
						<a class="rvs-nav-item">

							<?php if ('yes' == $settings['show_thumbnail_thumb']) : ?>
								<span class="rvs-nav-item-thumb" style="background-image: url(<?php echo esc_url($video_thumbnail); ?>)"></span>
							<?php endif; ?>

							<?php if ('yes' == $settings['show_thumbnail_title']) : ?>
								<<?php echo Utils::get_valid_html_tag($settings['thumbnail_title_tags']); ?> class="rvs-nav-item-title" title="<?php echo esc_html($video['title']); ?>"><?php echo esc_html($video['title']); ?></<?php echo Utils::get_valid_html_tag($settings['thumbnail_title_tags']); ?>>
							<?php endif; ?>

							<?php if ('yes' == $settings['show_thumbnail_desc']) : ?>
								<div class="rvs-nav-item-credits" title="<?php echo wp_kses_post($video['desc']); ?>"><?php echo wp_kses_post($video['desc']); ?></div>
							<?php endif; ?>
						</a>

					<?php endforeach; ?>

				<?php endif;

			$this->render_footer();
		}

		public function render_header() {
			$settings = $this->get_settings_for_display();

			$this->add_render_attribute('video-gallery', 'class', ['bdt-video-gallery', 'rvs-container', 'rvs-flat-circle-play']);

			if ('horizontal' == $settings['thumb_layout']) {
				$this->add_render_attribute('video-gallery', 'class', 'rvs-horizontal');
			}

			if ($settings['show_play_btn_on_hover']) {
				$this->add_render_attribute('video-gallery', 'class', 'rvs-show-play-on-hover');
			}

			if ($settings['continuous_play']) {
				$this->add_render_attribute('video-gallery', 'class', 'rvs-continuous-play');
			}

			if ('yes' !== $settings['show_thumbnail_desc']) {
				$this->add_render_attribute('video-gallery', 'class', 'rvs-hide-credits');
			}

				?>
				<div <?php echo $this->get_render_attribute_string('video-gallery'); ?>>
					<div class="rvs-item-container">
						<div class="rvs-item-stage">
						<?php
					}

					public function render_footer() {
						?>
						</div>
						<a class="rvs-nav-next"></a>
					</div>
				</div>
		<?php
					}
				}
