<?php

namespace ElementPack\Modules\AudioPlayer\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Modules\DynamicTags\Module as TagsModule;

use ElementPack\Modules\AudioPlayer\Skins;

use ElementPack;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Audio_Player extends Module_Base {

	public function get_name() {
		return 'bdt-audio-player';
	}

	public function get_title() {
		return BDTEP . esc_html__('Audio Player', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-audio-player';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['audio', 'player', 'sounder'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-audio-player'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['jplayer', 'ep-scripts'];
		} else {
			return ['jplayer', 'ep-audio-player'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/VHAEO1xLVxU';
	}

	protected function register_skins() {
		$this->add_skin(new Skins\Skin_Poster($this));
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__('Audio', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'poster',
			[
				'label'   => esc_html__('Choose Poster', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/audio-thumbnail.svg',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-poster' => 'background-image:  url("{{URL}}");',
				],
				'condition' => [
					'_skin' => 'bdt-poster',
				],
			]
		);

		$this->add_control(
			'layout_style',
			[
				'label'   => esc_html__('Layout Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__('Style 01', 'bdthemes-element-pack'),
					'style-2'  => esc_html__('Style 02', 'bdthemes-element-pack'),
					'style-3'  => esc_html__('Style 03', 'bdthemes-element-pack'),
					'style-4'  => esc_html__('Style 04', 'bdthemes-element-pack'),
					'style-5'  => esc_html__('Style 05', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => 'bdt-poster',
				],
			]
		);


		$this->add_responsive_control(
			'player_height',
			[
				'label' => esc_html__('Player Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
						'step' => 10,
					],
				],
				'default' => [
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .skin-poster' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'_skin' => 'bdt-poster',
				],
			]
		);

		$this->add_control(
			'source_type',
			[
				'label'   => esc_html__('Source Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hosted_url',
				'options' => [
					'hosted_url' => esc_html__('Local Audio', 'bdthemes-element-pack'),
					'remote_url'  => esc_html__('Remote Audio', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'hosted_url',
			[
				'label'      => esc_html__('Local Audio', 'elementor'),
				'type'       => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type' => 'audio',
				'default'       => [
					'url'       => BDTEP_ASSETS_URL . 'others/intro.mp3',
				],
				'condition'   => [
					'source_type' => 'hosted_url'
				]
			]
		);


		$this->add_control(
			'remote_url',
			[
				'label'         => esc_html__('Remote URL', 'bdthemes-element-pack'),
				'description'   => 'If you want to add any streaming audio url so please add <b>;stream/1</b> at the end of your url for example: http://cast.com:9942/;stream/1',
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url'       => BDTEP_ASSETS_URL . 'others/intro.mp3',
				],
				'placeholder'   => 'https://example.com/music.mp3',
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'condition'   => [
					'source_type' => 'remote_url'
				]
			]
		);


		$this->add_control(
			'audio_title',
			[
				'label'   => esc_html__('Audio Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tooltip',
				'options' => [
					'tooltip' => esc_html__('Tooltip', 'bdthemes-element-pack'),
					'inline'  => esc_html__('Inline', 'bdthemes-element-pack'),
					'none'    => esc_html__('None', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-audio-player-title-',
				'render_type' => 'template',
				'condition'   => [
					'_skin!' => 'bdt-poster',
				],

			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Audio Title', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
				// 'condition'   => [
				// 	'_skin!' => 'bdt-poster',
				// ],
			]
		);


		$this->add_control(
			'author_name',
			[
				'label'       => esc_html__('Author Name', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => 'John Duo',
				'placeholder' => 'Author Name',
				'condition'   => [
					'_skin' => 'bdt-poster',
				],
			]
		);

		$this->add_responsive_control(
			'player_width',
			[
				'label' => esc_html__('Player Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'fixed_player!' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'player_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
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

				// 'condition' => [
				// 	'player_width!' => '',
				// 	'fixed_player!' => 'yes'
				// ],
				// 'prefix_class' => 'elementor%s-align-',
				// 'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-info-wrapper .bdt-audio-info' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fixed_player',
			[
				'label'        => esc_html__('Fixed Player', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-audio-player-fixed-',
				'condition'   => [
					'_skin!' => 'bdt-poster',
				],
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
			'seek_bar',
			[
				'label'   => esc_html__('Seek Bar', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'time_duration',
			[
				'label'   => esc_html__('Time/Duration(s)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					''         => esc_html__('None', 'bdthemes-element-pack'),
					'time'     => esc_html__('Time', 'bdthemes-element-pack'),
					'duration' => esc_html__('Duration', 'bdthemes-element-pack'),
					'both'     => esc_html__('Both', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'time_restrict',
			[
				'label'       => esc_html__('Restrict Time', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('After some second player will stop', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'restrict_duration',
			[
				'label' => esc_html__('Restrict Duration(s)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 2,
						'max'  => 200
					]
				],
				'default' => [
					'size' => 10
				],
				'condition' => [
					'time_restrict' => 'yes'
				]
			]
		);

		$this->add_control(
			'volume_mute',
			[
				'label'   => esc_html__('Volume Mute/Unmute', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'volume_bar',
			[
				'label'   => esc_html__('Volume Bar', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'smooth_show',
			[
				'label'   => esc_html__('Smoothly Enter', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'keyboard_enable',
			[
				'label'   => esc_html__('Keyboard Enable', 'bdthemes-element-pack'),
				'description'   => esc_html__('for example: when you press p=Play, m=Mute, >=Volume + <=Volume -, l=Loop etc  ', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Auto Play', 'bdthemes-element-pack'),
				'description'   => esc_html__('Some latest browser does not support this feature for not annoying user.', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'description'   => esc_html__('If you set yes so your music will automatically repeat again and again.', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'volume_level',
			[
				'label' => esc_html__('Default Volume', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.8,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_skin_poster',
			[
				'label'     => esc_html__('Skin Poster', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-poster'
				]
			]
		);

		$this->add_control(
			'thumb_style',
			[
				'label' => esc_html__('Thumb Style', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-audio-player-poster-thumb-',
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'skin_poster_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'skin_poster__border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-audio-player.skin-poster',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'skin_poster__radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player.skin-poster' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'skin_poster_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .skin-poster' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'skin_poster_css_filters',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-audio-player-poster',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail',
			[
				'label'     => esc_html__('Thumbnail', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin'       => 'bdt-poster',
					'thumb_style' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_width',
			[
				'label' => esc_html__('Width (px)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 20,
						'max'  => 400,
						'step' => 5
					]
				],
				'default' => [
					'size' => 150
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player-thumb img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player-thumb img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('thumbnail_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-thumb img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-audio-player-thumb img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-thumb img:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-audio-player-thumb img:hover',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => esc_html__('Transition Duration (s)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-thumb img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumbnail_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-audio-player-thumb img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbnail_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumbnail_shadow',
				'selector' => '{{WRAPPER}} .bdt-audio-player-thumb img',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_play_button',
			[
				'label' => esc_html__('Play/Pause Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_play_button');

		$this->start_controls_tab(
			'tab_play_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'play_button_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play svg *, {{WRAPPER}} .jp-audio .jp-pause svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_border',
			[
				'label'   => esc_html__('Border Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					''       => esc_html__('None', 'bdthemes-element-pack'),
					'solid'  => esc_html__('Solid', 'bdthemes-element-pack'),
					'dotted' => esc_html__('Dotted', 'bdthemes-element-pack'),
					'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_border_width',
			[
				'label'      => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top'    => '1',
					'bottom' => '1',
					'left'   => '1',
					'right'  => '1',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'play_button_border!' => '',
				],
			]
		);

		$this->add_control(
			'play_button_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default' => '#d5d5d5',
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'play_button_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'play_button_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_button_shadow',
				'selector' => '{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause',
			]
		);

		$this->add_responsive_control(
			'play_button_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play, {{WRAPPER}} .jp-audio .jp-pause' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: calc({{SIZE}}{{UNIT}} - 4px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_play_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'play_button_hover_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play:hover svg *, {{WRAPPER}} .jp-audio .jp-pause:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play:hover, {{WRAPPER}} .jp-audio .jp-pause:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'play_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-play:hover, {{WRAPPER}} .jp-audio .jp-pause:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_button_hover_shadow',
				'selector' => '{{WRAPPER}} .jp-audio .jp-play:hover, {{WRAPPER}} .jp-audio .jp-pause:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => esc_html__('Time/Duration', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'time_duration!'     => '',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-current-time, {{WRAPPER}} .jp-audio .jp-duration' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .jp-audio .jp-current-time, {{WRAPPER}} .jp-audio .jp-duration',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_seek_bar',
			[
				'label'     => esc_html__('Seek Bar', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'seek_bar'     => 'yes',
				],
			]
		);

		$this->add_control(
			'seek_bar_height',
			[
				'label' => esc_html__('Bar Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-seek-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'seek_bar_color',
			[
				'label'     => esc_html__('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-seek-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'seek_bar_adjust_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-seek-bar .jp-play-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'seek_bar_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-seek-bar .jp-play-bar, {{WRAPPER}} .jp-audio .jp-seek-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_audio_title',
			[
				'label'     => esc_html__('Audio Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'audio_title' => 'inline',
					'_skin'       => '',
				]
			]
		);

		$this->add_control(
			'audio_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'audio_title_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
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
					'{{WRAPPER}} .bdt-audio-title'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'audio_title_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-audio-title'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_volume_button',
			[
				'label' => esc_html__('Volume Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'volume_mute'     => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_volume_button');

		$this->start_controls_tab(
			'tab_volume_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'volume_button_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute svg *, {{WRAPPER}} .jp-audio .jp-unmute svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_border',
			[
				'label'   => esc_html__('Border Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					''       => esc_html__('None', 'bdthemes-element-pack'),
					'solid'  => esc_html__('Solid', 'bdthemes-element-pack'),
					'dotted' => esc_html__('Dotted', 'bdthemes-element-pack'),
					'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_border_width',
			[
				'label'      => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top'    => '1',
					'bottom' => '1',
					'left'   => '1',
					'right'  => '1',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'volume_button_border!' => '',
				],
			]
		);

		$this->add_control(
			'volume_button_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default' => '#d5d5d5',
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'volume_button_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'volume_button_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'volume_button_shadow',
				'selector' => '{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute',
			]
		);

		$this->add_responsive_control(
			'volume_button_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute, {{WRAPPER}} .jp-audio .jp-unmute' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: calc({{SIZE}}{{UNIT}} - 4px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_volume_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'volume_button_hover_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute:hover svg *, {{WRAPPER}} .jp-audio .jp-unmute:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute:hover, {{WRAPPER}} .jp-audio .jp-unmute:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'volume_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-mute:hover, {{WRAPPER}} .jp-audio .jp-unmute:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'volume_button_hover_shadow',
				'selector' => '{{WRAPPER}} .jp-audio .jp-mute:hover, {{WRAPPER}} .jp-audio .jp-unmute:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_volume_bar',
			[
				'label'     => esc_html__('Volume Bar', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'volume_bar'     => 'yes',
				],
			]
		);

		$this->add_control(
			'volume_bar_height',
			[
				'label' => esc_html__('Bar Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-volume-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'volume_bar_color',
			[
				'label'     => esc_html__('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-volume-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_bar_adjust_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-audio .jp-volume-bar .jp-volume-bar-value' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_skin_audio_title',
			[
				'label'     => esc_html__('Audio Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-poster',
				],
			]
		);

		$this->add_responsive_control(
			'skin_audio_title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'skin_audio_title_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'skin_audio_title_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-audio-player-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'skin_audio_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-audio-player-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_skin_author_name',
			[
				'label'     => esc_html__('Author Name', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-poster',
				],
			]
		);

		$this->add_responsive_control(
			'skin_author_name_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-audio-player-artist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'skin_author_name_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-audio-player-artist span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'skin_author_name_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-audio-player-artist span',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'skin_author_name_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-audio-player-artist span',
			]
		);

		$this->end_controls_section();
	}

	public function render_audio_header() {
		$settings      = $this->get_settings_for_display();
		$id            = $this->get_id();

		$this->add_render_attribute('audio-player', 'class', 'jp-jplayer');

		$this->add_render_attribute(
			[
				'audio-player' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'audio_title'       => $settings['title'],
							'volume_level'      => $settings['volume_level']['size'],
							'keyboard_enable'   => ('yes' == $settings['keyboard_enable']) ? true : false,
							'smooth_show'       => ('yes' == $settings['smooth_show']) ? true : false,
							'time_restrict'     => ('yes' == $settings['time_restrict']) ? true : false,
							'restrict_duration' => (isset($settings['restrict_duration']['size']) ? $settings['restrict_duration']['size'] : 10),
							'autoplay'          => ('yes' == $settings['autoplay']) ? true : false,
							'loop'              => ('yes' == $settings['loop']) ? true : false,
							'audio_source'      => ('remote_url' == $settings['source_type']) ? esc_url(do_shortcode($settings['remote_url']['url'])) : esc_url($settings['hosted_url']['url']),
						]))
					]
				]
			]
		);

?>
		<div <?php echo $this->get_render_attribute_string('audio-player'); ?>></div>

	<?php
	}

	public function render_audio_default() {
		$settings      = $this->get_settings_for_display();
		$id            = $this->get_id();

	?>
		<div id="jp_container_<?php echo esc_attr($id); ?>" class="jp-audio" role="application" aria-label="media player">
			<div class="jp-type-playlist">
				<div class="jp-gui jp-interface">
					<div class="jp-controls bdt-grid bdt-grid-small bdt-flex-middle" data-bdt-grid>

						<?php $this->render_play_button(); ?>

						<?php $this->render_current_time(); ?>

						<?php $this->render_seek_bar(); ?>

						<?php $this->render_time_duration(); ?>

						<?php $this->render_mute_button(); ?>

						<?php $this->render_volume_bar(); ?>

					</div>

				</div>

			</div>
		</div>

	<?php
	}

	public function render_play_button() {
		$settings = $this->get_settings_for_display();

	?>

		<div class="bdt-width-auto">
			<a href="javascript:void(0);" class="jp-play" tabindex="1" title="<?php esc_html_e('Play', 'bdthemes-element-pack'); ?> <?php echo esc_html($settings['title']); ?>">
				<?php echo element_pack_svg_icon('play'); ?>
			</a>
			<a href="javascript:void(0);" class="jp-pause" tabindex="1" title="<?php esc_html_e('Pause', 'bdthemes-element-pack'); ?> <?php echo esc_html($settings['title']); ?>">
				<?php echo element_pack_svg_icon('pause'); ?>
			</a>
		</div>

	<?php
	}

	public function render_seek_bar() {
		$settings      = $this->get_settings_for_display();

		$this->add_render_attribute('progress', 'title', $settings['title']);

		if ('tooltip'  == $settings['audio_title'] and 'bdt-poster' !== $settings['_skin']) {
			$this->add_render_attribute('progress', 'data-bdt-tooltip');
		}

	?>

		<?php if ('yes' === $settings['seek_bar']) : ?>
			<div class="bdt-width-expand">
				<div class="jp-progress" <?php echo $this->get_render_attribute_string('progress'); ?>>
					<?php if ('inline' === $settings['audio_title']) : ?>
						<div class="bdt-audio-title"><?php echo esc_html($settings['title']); ?></div>
					<?php endif; ?>
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
			</div>
		<?php endif;
	}

	public function render_current_time() {
		$settings      = $this->get_settings_for_display();

		?>

		<?php if ('time' === $settings['time_duration'] or 'both' === $settings['time_duration']) : ?>
			<div class="bdt-width-auto">
				<div class="jp-current-time"></div>
			</div>
		<?php endif;
	}

	public function render_time_duration() {
		$settings = $this->get_settings_for_display();

		?>

		<?php if ('duration' === $settings['time_duration'] or 'both' === $settings['time_duration']) : ?>
			<div class="bdt-width-auto bdt-visible@m">
				<div class="jp-duration"></div>
			</div>
		<?php endif;
	}

	public function render_mute_button() {
		$settings      = $this->get_settings_for_display();

		?>

		<?php if ('yes' === $settings['volume_mute']) : ?>
			<div class="bdt-width-auto bdt-audio-player-mute">
				<a href="javascript:void(0);" class="jp-mute" tabindex="1" title="<?php esc_html_e('Mute', 'bdthemes-element-pack'); ?>">
					<?php echo element_pack_svg_icon('mute'); ?>
				</a>
				<a href="javascript:void(0);" class="jp-unmute" tabindex="1" title="<?php esc_html_e('Unmute', 'bdthemes-element-pack'); ?>">
					<?php echo element_pack_svg_icon('unmute'); ?>
				</a>
			</div>
		<?php endif;
	}

	public function render_volume_bar() {
		$settings      = $this->get_settings_for_display();
		?>

		<?php if ('yes' === $settings['volume_bar']) : ?>
			<div class="bdt-width-auto bdt-visible@m">
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
			</div>
		<?php endif;
	}




	protected function render() {
		$id = $this->get_id();

		?>
		<div class="bdt-audio-player">
			<?php $this->render_audio_header(); ?>
			<?php $this->render_audio_default(); ?>
		</div>
<?php
	}
}
