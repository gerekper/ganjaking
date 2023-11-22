<?php
namespace ElementPack\Modules\VideoPlayer\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use ElementPack\Element_Pack_Loader;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Video_Player extends Module_Base {

	public function get_name() {
		return 'bdt-video-player';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Video Player', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-video-player';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'video', 'player' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-video-player' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['jplayer', 'ep-scripts'];
        } else {
      		return [ 'jplayer' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/ksy2uZ5Hg3M';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Video', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title (Tooltip Text)', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Big Buck Bunny' , 'bdthemes-element-pack' ),
				'placeholder' => __( 'Video Title' , 'bdthemes-element-pack' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => esc_html__( 'Video Source', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v',
				'placeholder' => 'https://example.com/video.mp4',
				'label_block' => true,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
			]
		);

		$this->add_control(
			'poster',
			[
				'label'   => __( 'Choose Poster', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => 'http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png',
				],
			]
		);

		$this->add_responsive_control(
			'player_width',
			[
				'label' => esc_html__( 'Player Width', 'bdthemes-element-pack' ),
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
			]
		);

		$this->add_responsive_control(
			'player_align',
			[
				'label'        => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'elementor%s-align-',
				'options'      => [
					'left' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'player_width!' => ''
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => __( 'Additional', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title_hide',
			[
				'label' => __( 'Title Hide', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'seek_bar',
			[
				'label'   => __( 'Seek Bar', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'time_duration',
			[
				'label'   => esc_html__( 'Time/Duration', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					''         => esc_html__( 'None', 'bdthemes-element-pack' ),
					'time'     => esc_html__( 'Time', 'bdthemes-element-pack' ),
					'duration' => esc_html__( 'Duration', 'bdthemes-element-pack' ),
					'both'     => esc_html__( 'Both', 'bdthemes-element-pack' ),
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_mute',
			[
				'label'   => __( 'Volume Mute/Unmute', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'volume_bar',
			[
				'label'   => __( 'Volume Bar', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'fullscreen',
			[
				'label'   => __( 'Fullscreen Control', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'smooth_show',
			[
				'label'   => __( 'Smoothly Enter', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'keyboard_enable',
			[
				'label'       => __( 'Keyboard Enable', 'bdthemes-element-pack' ),
				'description' => __( 'for example: when you press p=Play, m=Mute, >=Volume + <=Volume -, l=Loop etc  ', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			]
		);

		$this->add_control(
			'volume_level',
			[
				'label' => esc_html__( 'Default Volume', 'bdthemes-element-pack' ),
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

		$this->add_control(
			'sticky_mode',
			[
				'label'       => __( 'Sticky Mode', 'bdthemes-element-pack' ),
				'description' => __( 'When you activate is you video will sticky at playing time.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_control',
			[
				'label' => __( 'Control Area', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'control_padding',
			[
				'label'     => __( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-interface' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'control_background',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-interface, {{WRAPPER}} .jp-video .jp-player-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_play_button',
			[
				'label' => __( 'Play/Pause Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_play_button' );

		$this->start_controls_tab(
			'tab_play_button_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'play_button_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play svg *, {{WRAPPER}} .jp-video .jp-pause svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_border',
			[
				'label'   => esc_html__( 'Border Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''       => esc_html__( 'None', 'bdthemes-element-pack' ),
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_border_width',
			[
				'label'      => __( 'Border Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'play_button_border!' => '',
				],
			]
		);

		$this->add_control(
			'play_button_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'play_button_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'play_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_button_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause',
			]
		);

		$this->add_responsive_control(
			'play_button_size',
			[
				'label' => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play, {{WRAPPER}} .jp-video .jp-pause' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: calc({{SIZE}}{{UNIT}} - 4px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_play_button_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'play_button_hover_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play:hover svg *, {{WRAPPER}} .jp-video .jp-pause:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_hover_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play:hover, {{WRAPPER}} .jp-video .jp-pause:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'play_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'play_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-play:hover, {{WRAPPER}} .jp-video .jp-pause:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'play_button_hover_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-play:hover, {{WRAPPER}} .jp-video .jp-pause:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => __( 'Time/Duration', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'time_duration!' => '',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(51, 51, 51, 0.6)',
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-current-time, {{WRAPPER}} .jp-video .jp-duration' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .jp-video .jp-current-time, {{WRAPPER}} .jp-video .jp-duration',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_seek_bar',
			[
				'label'     => __( 'Seek Bar', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'seek_bar'     => 'yes',
				],
			]
		);

		$this->add_control(
			'seek_bar_height',
			[
				'label' => __( 'Bar Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-seek-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'seek_bar_color',
			[
				'label'     => __( 'Bar Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-seek-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'seek_bar_adjust_color',
			[
				'label'     => __( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-seek-bar .jp-play-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'seek_bar_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-seek-bar .jp-play-bar, {{WRAPPER}} .jp-video .jp-seek-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_volume_button',
			[
				'label'     => __( 'Volume Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'volume_mute' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_volume_button' );

		$this->start_controls_tab(
			'tab_volume_button_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'volume_button_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute svg *, {{WRAPPER}} .jp-video .jp-unmute svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_border',
			[
				'label'   => esc_html__( 'Border Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''       => esc_html__( 'None', 'bdthemes-element-pack' ),
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_border_width',
			[
				'label'     => __( 'Border Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'volume_button_border!' => '',
				],
			]
		);

		$this->add_control(
			'volume_button_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'volume_button_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'volume_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'volume_button_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-mute, {{WRAPPER}} .jp-video .jp-unmute',
			]
		);

		$this->add_responsive_control(
			'volume_button_size',
			[
				'label' => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute,
					{{WRAPPER}} .jp-video .jp-unmute' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: calc({{SIZE}}{{UNIT}} - 4px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_volume_button_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'volume_button_hover_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute:hover svg *, {{WRAPPER}} .jp-video .jp-unmute:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_hover_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute:hover, {{WRAPPER}} .jp-video .jp-unmute:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'volume_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-mute:hover, {{WRAPPER}} .jp-video .jp-unmute:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'volume_button_hover_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-mute:hover, {{WRAPPER}} .jp-video .jp-unmute:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_volume_bar',
			[
				'label'     => __( 'Volume Bar', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'volume_bar'     => 'yes',
				],
			]
		);

		$this->add_control(
			'volume_bar_height',
			[
				'label' => __( 'Bar Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-volume-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'volume_bar_color',
			[
				'label'     => __( 'Bar Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-volume-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'volume_bar_adjust_color',
			[
				'label'     => __( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-volume-bar .jp-volume-bar-value' => 'background-color: {{VALUE}};',
				],
			]
		);		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_fullscreen_button',
			[
				'label'     => __( 'Fullscreen Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'fullscreen' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_fullscreen_button' );

		$this->start_controls_tab(
			'tab_fullscreen_button_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'fullscreen_button_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_border',
			[
				'label'   => esc_html__( 'Border Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''       => esc_html__( 'None', 'bdthemes-element-pack' ),
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_border_width',
			[
				'label'     => __( 'Border Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'fullscreen_button_border!' => '',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'fullscreen_button_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'fullscreen_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'fullscreen_button_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-full-screen',
			]
		);

		$this->add_responsive_control(
			'fullscreen_button_size',
			[
				'label' => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: calc({{SIZE}}{{UNIT}} - 4px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fullscreen_button_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'fullscreen_button_hover_icon_color',
			[
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_hover_background',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fullscreen_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'fullscreen_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .jp-video .jp-full-screen:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'fullscreen_button_hover_shadow',
				'selector' => '{{WRAPPER}} .jp-video .jp-full-screen:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_loop() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		?>
		<div class="bdt-video-player">
		<div id="jp_container_<?php echo esc_attr($id); ?>" class="jp-video jp-video-360p" role="application" aria-label="media player">
			<div class="jp-type-single">
				<div id="jplayer_<?php echo esc_attr($id); ?>" class="jp-jplayer"></div>
				<div class="jp-gui">

					<div class="jp-video-play bdt-position-center">
						<a class="jp-video-play-icon" role="button" tabindex="0">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 41.999 41.999" xml:space="preserve" width="24" height="24">
							<path d="M36.068,20.176l-29-20C6.761-0.035,6.363-0.057,6.035,0.114C5.706,0.287,5.5,0.627,5.5,0.999v40
								c0,0.372,0.206,0.713,0.535,0.886c0.146,0.076,0.306,0.114,0.465,0.114c0.199,0,0.397-0.06,0.568-0.177l29-20
								c0.271-0.187,0.432-0.494,0.432-0.823S36.338,20.363,36.068,20.176z M7.5,39.095V2.904l26.239,18.096L7.5,39.095z"/>
								<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
						</a>
					</div>

					<div class="jp-interface">
						<div class="jp-controls bdt-grid bdt-grid-small bdt-flex-middle bdt-position-relative" data-bdt-grid>
							<div class="bdt-width-auto">
								<a href="javascript:void(0);" class="jp-play" tabindex="1" title="<?php esc_html_e('Play', 'bdthemes-element-pack'); ?>">
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 41.999 41.999" xml:space="preserve">
									<path d="M36.068,20.176l-29-20C6.761-0.035,6.363-0.057,6.035,0.114C5.706,0.287,5.5,0.627,5.5,0.999v40
										c0,0.372,0.206,0.713,0.535,0.886c0.146,0.076,0.306,0.114,0.465,0.114c0.199,0,0.397-0.06,0.568-0.177l29-20
										c0.271-0.187,0.432-0.494,0.432-0.823S36.338,20.363,36.068,20.176z M7.5,39.095V2.904l26.239,18.096L7.5,39.095z"/>
										<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
								</a>
								<a href="javascript:void(0);" class="jp-pause" tabindex="1" title="<?php esc_html_e('Pause', 'bdthemes-element-pack'); ?>">
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
										 viewBox="0 0 42 42" xml:space="preserve">
										 <g>
										 	<path d="M14.5,0c-0.552,0-1,0.447-1,1v40c0,0.553,0.448,1,1,1s1-0.447,1-1V1C15.5,0.447,15.052,0,14.5,0z"/>
											<path d="M27.5,0c-0.552,0-1,0.447-1,1v40c0,0.553,0.448,1,1,1s1-0.447,1-1V1C28.5,0.447,28.052,0,27.5,0z"/>
										</g>
										<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
									</svg>		
								</a>
							</div>

							<?php if ('time' === $settings['time_duration'] or 'both' === $settings['time_duration']) : ?>
								<div class="bdt-width-auto"><div class="jp-current-time"></div></div>
							<?php endif; ?>
							
							<?php if ('yes' === $settings['seek_bar']) : ?>
								<div class="bdt-width-expand bdt-jp-seek-bar">
									<div class="jp-progress" role="button" title="<?php echo esc_html($settings['title']); ?>" data-bdt-tooltip>
										<div class="jp-seek-bar">
											<div class="jp-play-bar"></div>
										</div>
									</div>
								</div>
							<?php endif; ?>
							
							<?php if ('duration' === $settings['time_duration'] or 'both' === $settings['time_duration']) : ?>
								<div class="bdt-width-auto bdt-visible@m bdt-jp-duration"><div class="jp-duration"></div></div>
							<?php endif; ?>

							<?php if ('yes' === $settings['volume_mute']) : ?>
								<div class="bdt-width-auto bdt-jp-mute">
									<a href="javascript:void(0);" class="jp-mute" tabindex="1" title="<?php esc_html_e('Mute', 'bdthemes-element-pack'); ?>">
										<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 52.026 52.026">
											<g>
											 	<path d="M28.404,3.413c-0.976-0.552-2.131-0.534-3.09,0.044c-0.046,0.027-0.09,0.059-0.13,0.093L11.634,15.013H1
												c-0.553,0-1,0.447-1,1v19c0,0.266,0.105,0.52,0.293,0.707S0.734,36.013,1,36.013l10.61-0.005l13.543,12.44
												c0.05,0.046,0.104,0.086,0.161,0.12c0.492,0.297,1.037,0.446,1.582,0.446c0.517-0.001,1.033-0.134,1.508-0.402
												C29.403,48.048,30,47.018,30,45.857V6.169C30,5.008,29.403,3.978,28.404,3.413z M28,45.857c0,0.431-0.217,0.81-0.579,1.015
												c-0.155,0.087-0.548,0.255-1,0.026L13,34.569v-4.556c0-0.553-0.447-1-1-1s-1,0.447-1,1v3.996l-9,0.004v-17h9v4c0,0.553,0.447,1,1,1
												s1-0.447,1-1v-4.536l13.405-11.34c0.461-0.242,0.86-0.07,1.016,0.018C27.783,5.36,28,5.739,28,6.169V45.857z"/>
												<path d="M38.797,7.066c-0.523-0.177-1.091,0.103-1.269,0.626c-0.177,0.522,0.103,1.091,0.626,1.269
													c7.101,2.411,11.872,9.063,11.872,16.553c0,7.483-4.762,14.136-11.849,16.554c-0.522,0.178-0.802,0.746-0.623,1.27
													c0.142,0.415,0.53,0.677,0.946,0.677c0.107,0,0.216-0.017,0.323-0.054c7.896-2.693,13.202-10.106,13.202-18.446
													C52.026,17.166,46.71,9.753,38.797,7.066z"/>
												<path d="M43.026,25.513c0-5.972-4.009-11.302-9.749-12.962c-0.533-0.151-1.084,0.152-1.238,0.684
												c-0.153,0.53,0.152,1.085,0.684,1.238c4.889,1.413,8.304,5.953,8.304,11.04s-3.415,9.627-8.304,11.04
												c-0.531,0.153-0.837,0.708-0.684,1.238c0.127,0.438,0.526,0.723,0.961,0.723c0.092,0,0.185-0.013,0.277-0.039
												C39.018,36.815,43.026,31.485,43.026,25.513z"/>
											</g>
											<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
										</svg>
									</a>
									<a href="javascript:void(0);" class="jp-unmute" tabindex="1" title="<?php esc_html_e('Unmute', 'bdthemes-element-pack'); ?>">
										<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 54 54">
											<g>
											 	<path d="M46.414,26l7.293-7.293c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L45,24.586l-7.293-7.293
												c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414L43.586,26l-7.293,7.293c-0.391,0.391-0.391,1.023,0,1.414
												C36.488,34.902,36.744,35,37,35s0.512-0.098,0.707-0.293L45,27.414l7.293,7.293C52.488,34.902,52.744,35,53,35
												s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L46.414,26z"/>
												<path d="M28.404,4.4c-0.975-0.552-2.131-0.534-3.09,0.044c-0.046,0.027-0.09,0.059-0.13,0.093L11.634,16H1c-0.553,0-1,0.447-1,1v19
												c0,0.266,0.105,0.52,0.293,0.707S0.734,37,1,37l10.61-0.005l13.543,12.44c0.05,0.046,0.104,0.086,0.161,0.12
												c0.492,0.297,1.037,0.446,1.582,0.446c0.517-0.001,1.033-0.134,1.508-0.402C29.403,49.035,30,48.005,30,46.844V7.156
												C30,5.995,29.403,4.965,28.404,4.4z M28,46.844c0,0.431-0.217,0.81-0.579,1.015c-0.155,0.087-0.548,0.255-1,0.026L13,35.556V31
												c0-0.553-0.447-1-1-1s-1,0.447-1,1v3.996L2,35V18h9v4c0,0.553,0.447,1,1,1s1-0.447,1-1v-4.536l13.405-11.34
												c0.46-0.242,0.86-0.07,1.016,0.018C27.783,6.347,28,6.725,28,7.156V46.844z"/>
											</g>
											<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
										</svg>
									</a>
								</div>
							<?php endif; ?>

							<?php if ('yes' === $settings['volume_bar']) : ?>
								<div class="bdt-width-auto bdt-visible@m bdt-jp-volume-bar">
									<div class="jp-volume-bar">
										<div class="jp-volume-bar-value"></div>
									</div>
								</div>
							<?php endif; ?>

							<?php if ('yes' === $settings['fullscreen']) : ?>
								<div class="bdt-width-auto bdt-visible@m">
									<a class="jp-full-screen" role="button" tabindex="0">
										<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
										  <g>
										    <g>
										      <path d="m62,45c-1.105,0-2,0.896-2,2v13h-13c-1.105,0-2,0.895-2,2s0.895,2 2,2h15c1.105,0 2-0.895 2-2v-15c0-1.104-0.895-2-2-2z"/>
										      <path d="m17,60h-13v-13c0-1.104-0.895-2-2-2s-2,0.896-2,2v15c0,1.105 0.895,2 2,2h15c1.105,0 2-0.895 2-2s-0.895-2-2-2z"/>
										      <path d="m17-.001h-15c-1.105,0-2,0.896-2,2v15c0,1.104 0.895,2 2,2s2-0.896 2-2v-13h13c1.105,0 2-0.896 2-2s-0.895-2-2-2z"/>
										      <path d="m62-.001h-15c-1.105,0-2,0.896-2,2s0.895,2 2,2h13v13c0,1.104 0.895,2 2,2s2-0.896 2-2v-15c0-1.104-0.895-2-2-2z"/>
										    </g>
										  </g>
										</svg>
									</a>
								</div>
							<?php endif; ?>
						</div>
							
						<?php if ('yes' != $settings['title_hide']) : ?>
							<div class="jp-player-title">
								<div class="bdt-position-center">
									<div class="bdt-grid" data-bdt-grid>
										<div class="bdt-width-1-2">
											<div class="jp-title" aria-label="title">&nbsp;</div>
										</div>
										<div class="bdt-width-1-2 bdt-text-right">
											<span class="jp-current-time"></span> / <span class="jp-duration"></span>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		</div>

		<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#jplayer_<?php echo esc_attr($id); ?>").jPlayer({
					ready: function () {
						$(this).jPlayer("setMedia", {
							title: "<?php echo esc_attr($settings['title']); ?>",
							m4v: "<?php echo esc_attr($settings['source']); ?>",
							poster: "<?php echo esc_url($settings['poster']['url']); ?>",
						});
					},
					play: function() { // To avoid multiple jPlayers playing together.
						$(this).jPlayer("pauseOthers");
					},
					cssSelectorAncestor: "#jp_container_<?php echo esc_attr($id); ?>",
					swfPath: "<?php echo BDTEP_ASSETS_URL; ?>vendor/js",
					supplied: "webmv, ogv, m4v",
					size: {
						width: "100%",
						height: "100%",
						cssClass: "jp-video-360p"
					},
					useStateClassSkin: true,
					autoBlur: <?php echo ('yes' == $settings['smooth_show']) ? 'true' : 'false'; ?>,
					smoothPlayBar: true,
					keyEnabled: <?php echo ('yes' == $settings['keyboard_enable']) ? 'true' : 'false'; ?>,
					remainingDuration: true,
					volume: true,
					toggleDuration: <?php echo esc_html($settings['volume_level']['size']); ?>,
				});

				<?php if ('yes' === $settings['sticky_mode']) : ?>
				var $featuredVideo = $( "#jp_container_<?php echo esc_attr($id); ?>" ); // Actual Video.
				var top = $featuredVideo.offset().top;
				var offset = Math.floor( top + ( $featuredVideo.outerHeight() / 2 ) );

				$( window ).on( "scroll", function() {
				   $featuredVideo.toggleClass( "jp-is-sticky",
				     $( window ).scrollTop() > offset && $featuredVideo.hasClass( "jp-state-playing" )
				   );
				});
				<?php endif; ?>
			});
		</script>
		<?php 
	}

	protected function render() {
		$id = $this->get_id();

		?>
		<div class="bdt-video" id="bdt-video-<?php echo esc_attr($id); ?>">
			<?php $this->render_loop(); ?>
		</div>		
		<?php
	}
}
