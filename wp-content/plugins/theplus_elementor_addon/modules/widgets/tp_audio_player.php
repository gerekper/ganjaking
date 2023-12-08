<?php 
/*
Widget Name: Audio Player
Description: Audio Player
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Audio_Player extends Widget_Base {
		
	public function get_name() {
		return 'tp-audio-player';
	}

    public function get_title() {
        return esc_html__('Audio Player', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-headphones theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
    protected function register_controls() {
		$this->start_controls_section(
			'section_audio_player',
			[
				'label' => esc_html__( 'Audio Player', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'ap_style',
			[
				'label' => esc_html__( 'Styles', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
					'style-4'  => esc_html__( 'Style 4', 'theplus' ),
					'style-5'  => esc_html__( 'Style 5', 'theplus' ),
					'style-6'  => esc_html__( 'Style 6', 'theplus' ),
					'style-7'  => esc_html__( 'Style 7', 'theplus' ),
					'style-8'  => esc_html__( 'Style 8', 'theplus' ),
					'style-9'  => esc_html__( 'Style 9', 'theplus' ),
				],
			]
		);
		$this->end_controls_section();		
		$this->start_controls_section(
			'section_audio_playlist',
			[
				'label' => esc_html__( 'Playlist', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'title',
			[
				'label' 		=> esc_html__( 'Song Title', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,
				'dynamic' 		=> [ 'active' => true ],
				'label_block' 	=> true,
				'default' 		=> esc_html__( 'ThePlus Audio', 'theplus' ),
			]
		);
		$repeater->add_control(
			'author',
			[
				'label' 		=> esc_html__( 'Song Author', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,
				'dynamic' 		=> [ 'active' => true ],
				'default' 		=> esc_html__( 'The Plus', 'theplus' ),
				'label_block' 	=> true,				
			]
		);		
		$repeater->add_control(
			'audio_source',
			[
				'label' 		=> esc_html__( 'Source', 'theplus' ),
				'type' 			=> Controls_Manager::SELECT,
				'default'		=> 'url',
				'options'		=> [
					'file'		=> esc_html__( 'Self Hosted', 'theplus' ),
					'url'		=> esc_html__( 'External', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'source_mp3',
			[
				'label' 		=> esc_html__( 'File', 'theplus' ),
				'type' 			=> Controls_Manager::MEDIA,
				'dynamic' 		=> ['active' 	=> true,],
				'condition'		=> [
					'audio_source' => 'file',
				],
				'media_type' => 'audio',
			]
		);
		$repeater->add_control(
			'source_mp3_url',
			[
				'label' 		=> esc_html__( 'URL', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,				
				'dynamic' 		=> [
					'active' 	=> true,
				],
				'condition' 	=> [
					'audio_source' => 'url',
				],
			]
		);
		$repeater->add_control(
			'audio_track_image',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,				
				'separator'	=> 'before',
			]
		);
		$this->add_control(
			'playlist',
			[
				'type' 		=> Controls_Manager::REPEATER,
				'default' 	=> [
					[
						
					]
				],
				'fields' 		=> $repeater->get_controls(),
				'title_field' 	=> '{{{ title }}}',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_audio_common',
			[
				'label' => esc_html__( 'Common Setting', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'audio_track_image_c',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,				
				'condition' => [
					'ap_style!' => ['style-1','style-2','style-3','style-7'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
			]
		);
		$this->add_control(
			'split_text',
			[
				'label' 		=> esc_html__( 'Split Text', 'theplus' ),
				'type' 			=> Controls_Manager::TEXT,
				'dynamic' 		=> [ 'active' => true ],
				'label_block' 	=> true,
				'default' 		=> esc_html__( 'by', 'theplus' ),
				'condition' => [
					'ap_style!' => ['style-2','style-4','style-7','style-8','style-9'],
				],
			]
		);
		$this->add_responsive_control(
            'ap_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max-width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 2000,
						'step' => 5,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper' => 'max-width: {{SIZE}}{{UNIT}};margin:0 auto;',
				],				
            ]
        );
		$this->add_control(
			's_volume',
			[
				'label' => esc_html__( 'Default Volume', 'theplus' ),
				'type' => Controls_Manager::SLIDER,				
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 80,
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Song Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style!' => 'style-2',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .title,{{WRAPPER}} .tp-audio-player-wrapper .title',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .title,{{WRAPPER}} .tp-audio-player-wrapper .title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_author_style',
            [
                'label' => esc_html__('Song Author', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style!' => ['style-2','style-7'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .artist,{{WRAPPER}} .tp-audio-player-wrapper .artist',
			]
		);
		$this->add_control(
			'author_color',
			[
				'label' => esc_html__( 'Author Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .artist,{{WRAPPER}} .tp-audio-player-wrapper .artist' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_split_txt_style',
            [
                'label' => esc_html__('Split Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style!' => ['style-2','style-4','style-7','style-8','style-9'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'splittext_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .splitTxt,{{WRAPPER}} .tp-audio-player-wrapper .splitTxt',
			]
		);
		$this->add_control(
			'splittext_color',
			[
				'label' => esc_html__( 'Split Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .trackDetails .splitTxt,{{WRAPPER}} .tp-audio-player-wrapper .splitTxt' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_player_control_style',
            [
                'label' => esc_html__('Control', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );		
		$this->add_control(
			'player_control_bg_st9',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-9 .controls' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ap_style' => ['style-9'],
				],
			]
		);
		$this->add_responsive_control(
				'st_9_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-audio-player-wrapper.style-9 .controls' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'ap_style' => ['style-9'],
					],			
				]
			);
		$this->add_control(
			'player_control_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .playlistIcon,{{WRAPPER}} .tp-audio-player-wrapper .volumeIcon .vol-icon-toggle,{{WRAPPER}} .tp-audio-player-wrapper .controls' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ap_style!' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
            'player_control_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .playlistIcon,
					{{WRAPPER}} .tp-audio-player-wrapper .volumeIcon .vol-icon-toggle,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .rew,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .fwd,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .pause' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
				'condition' => [
					'ap_style!' => ['style-2','style-4'],
				],
            ]
        );
		$this->add_control(
			'pl_list_c_play_i',
			[
				'label' => esc_html__( 'Play/Pause Icon Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'player_control_play_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Play/Pause Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .pause' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'player_control_play_color',
			[
				'label' => esc_html__( 'Play/Pause Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
					{{WRAPPER}} .tp-audio-player-wrapper .controls .pause' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'player_control_play_bg_size1',
			[
				'label' => esc_html__( 'Background Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .controls .play,{{WRAPPER}} .tp-audio-player-wrapper .controls .pause' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-audio-player-wrapper.style-3 .tp-ap-pp,{{WRAPPER}} .tp-audio-player-wrapper.style-5 .controls .tp-ap-pp,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-ap-pp' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'player_control_play_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
				{{WRAPPER}} .tp-audio-player-wrapper .controls .pause,{{WRAPPER}} .tp-audio-player-wrapper.style-3 .tp-ap-pp,{{WRAPPER}} .tp-audio-player-wrapper.style-5 .controls .tp-ap-pp,
				{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-ap-pp',
			]
		);		
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'player_control_play_bg_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
									{{WRAPPER}} .tp-audio-player-wrapper .controls .pause',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'player_control_play_bg_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-audio-player-wrapper .controls .play,
						{{WRAPPER}} .tp-audio-player-wrapper .controls .pause,
						{{WRAPPER}} .tp-audio-player-wrapper.style-3 .tp-ap-pp,
						{{WRAPPER}} .tp-audio-player-wrapper.style-5 .controls .tp-ap-pp,
						{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-ap-pp' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'default' => [
					'%' => [
					'top' => '50',
					'right' => '50',
					'bottom' => '50',
					'left' => '50',					
					],
				],
				]
			);
			
			$this->add_control(
				'pl_list_c_playlist_i',
				[
					'label' => esc_html__( 'Playlist Icon Option', 'theplus' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'ap_style!' => ['style-2','style-7','style-8','style-9'],
					],
				]
			);
			$this->add_responsive_control(
				'pl_list_c_playlist_i_size',
				[
					'type' => Controls_Manager::SLIDER,
					'label' => esc_html__('Size', 'theplus'),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 200,
							'step' => 1,
						],
					],				
					'render_type' => 'ui',			
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlistIcon' => 'font-size: {{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'ap_style!' => ['style-2','style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
			'pl_list_c_playlist_i_color',
				[
					'label' => esc_html__( 'Playlist Icon Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlistIcon' => 'color: {{VALUE}}',
					],
					'condition' => [
						'ap_style!' => ['style-2','style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
				'pl_list_c_volume_i',
				[
					'label' => esc_html__( 'Volume Icon Option', 'theplus' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
				
			);
			$this->add_responsive_control(
				'pl_list_c_volume_i_size',
				[
					'type' => Controls_Manager::SLIDER,
					'label' => esc_html__('Size', 'theplus'),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 200,
							'step' => 1,
						],
					],				
					'render_type' => 'ui',			
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .volumeIcon .vol-icon-toggle' => 'font-size: {{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
			'pl_list_c_volume_i_color',
				[
					'label' => esc_html__( 'Volume Icon Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .volumeIcon .vol-icon-toggle' => 'color: {{VALUE}}',
					],
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
			'pl_list_c_volume_slider_rng_color',
				[
					'label' => esc_html__( 'Volume Slider Range Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .volume .ui-slider-range' => 'background: {{VALUE}}',
					],
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
			'pl_list_c_volume_slider_color',
				[
					'label' => esc_html__( 'Volume Slider Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .volume.ui-widget-content' => 'background: {{VALUE}}',
					],
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
			);
			$this->add_control(
			'pl_list_c_volume_slider_bg',
				[
				'label' => esc_html__( 'Volume Slider Background', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .tp-volume-bg' => 'background: {{VALUE}}',
					],
					'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pl_list_c_volume_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .tp-volume-bg',
				'condition' => [
						'ap_style!' => ['style-7','style-8','style-9'],
					],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_audio_tracker',
			[
				'label' => esc_html__( 'Tracker', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,	
				'condition' => [
					'ap_style!' => 'style-9',
				],
			]
		);
		$this->add_responsive_control(
            'player_tracker_time',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Time Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .currenttime,
					{{WRAPPER}} .tp-audio-player-wrapper .durationtime' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'ap_style' => 'style-3',
				],
            ]
        );
		$this->add_control(
			'player_tracker_time_color',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .currenttime,
					{{WRAPPER}} .tp-audio-player-wrapper .durationtime' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'ap_style' => 'style-3',
				],
			]
		);
		$this->add_responsive_control(
			'tracker_width',
			[
				'label' => esc_html__('Tracker width', 'theplus'),
				'type' => Controls_Manager::SLIDER,				
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-8 .controls,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tracker,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .ap-time-seek-vol,
					{{WRAPPER}} .tp-audio-player-wrapper.style-7 .ap-time-title,
					{{WRAPPER}} .tp-audio-player-wrapper.style-7 .controls,
					{{WRAPPER}} .tp-audio-player-wrapper.style-7 .tracker,
					{{WRAPPER}} .tp-audio-player-wrapper.style-6 .tracker,
					{{WRAPPER}} .tp-audio-player-wrapper.style-5  .tracker,
					{{WRAPPER}} .tp-audio-player-wrapper.style-4 .main-wrapper-style,
					{{WRAPPER}} .tp-audio-player-wrapper.style-3 .ap-time-seek-vol,					
					{{WRAPPER}} .tp-audio-player-wrapper.style-2 .main-wrapper-style,					
					{{WRAPPER}} .tp-audio-player-wrapper.style-1 .tracker' => 'width: {{SIZE}}%;',
				],
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'player_tracker_dot_color',
			[
				'label' => esc_html__( 'Dot Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .ui-slider .ui-slider-handle' => 'background: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'player_track_color',
			[
				'label' => esc_html__( 'Track Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .ui-widget-content' => 'background: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'player_track_fill_color',
			[
				'label' => esc_html__( 'Track Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .tracker .ui-slider-range' => 'background: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_trackimage_style',
            [
                'label' => esc_html__('Track Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style' => ['style-3','style-5','style-8','style-9'],
				],
            ]
        );
		$this->add_control(
			'background_size_st9',
			[
				'label' => esc_html__( 'Background Size', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => theplus_get_image_size_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img' => 'background-size:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-9'],
				],
			]
		);
		$this->add_control(
			'background_position_st9',
			[
				'label' => esc_html__( 'Background Position', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => theplus_get_image_position_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img' => 'background-position:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-9'],
				],
			]
		);
		$this->add_control(
			'background_repeat_st9',
			[
				'label' => esc_html__( 'Background Repeat', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => theplus_get_image_reapeat_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img' => 'background-repeat:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-9'],
				],
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'trackimg_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-audio-player-wrapper.style-3 .trackimage img,{{WRAPPER}} .tp-audio-player-wrapper.style-5 .ap-st5-img,{{WRAPPER}} .tp-audio-player-wrapper.style-8 .trackimage img,
					{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'trackimg_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-audio-player-wrapper.style-3 .trackimage img,{{WRAPPER}} .tp-audio-player-wrapper.style-5 .ap-st5-img,{{WRAPPER}} .tp-audio-player-wrapper.style-8 .trackimage img,{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'trackimg_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper.style-3 .trackimage img,{{WRAPPER}} .tp-audio-player-wrapper.style-5 .ap-st5-img,{{WRAPPER}} .tp-audio-player-wrapper.style-8 .trackimage img,{{WRAPPER}} .tp-audio-player-wrapper.style-9 .tp-player .tp-player-hover .tp-player-bg-img',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();		
		$this->start_controls_section(
            'section_playlist_style',
            [
                'label' => esc_html__('PlayList', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style!' => ['style-2','style-7','style-8','style-9'],
				],
            ]
        );
		$this->add_responsive_control(
			'playlist_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .playlist li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'playlist_margin',
			[
				'label' => esc_html__( 'Inner Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .playlist li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'playlist_outer_margin',
			[
				'label' => esc_html__( 'Outer Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .playlist' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'playlist_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .playlist li',
				
			]
		);
		$this->start_controls_tabs( 'playlist_n_a' );
			$this->start_controls_tab(
				'playlist_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'pl_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlist li' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'playlist_active',
				[
					'label' => esc_html__( 'Active', 'theplus' ),					
				]
			);
			$this->add_control(
				'pl_a_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlist li.active' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_control(
				'pl_a_bg',
				[
					'label' => esc_html__( 'Background', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlist li.active' => 'background-color: {{VALUE}}',
					],
				]
			);	
			$this->end_controls_tab();
		$this->end_controls_tabs();
			$this->add_responsive_control(
				'pl_bg_top_offset',
				[
					'type' => Controls_Manager::SLIDER,
					'label' => esc_html__('Top Offset', 'theplus'),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 200,
							'step' => 1,
						],
					],				
					'render_type' => 'ui',			
					'selectors' => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlist' => 'margin-top: {{SIZE}}{{UNIT}}',
					],				
				]
			);			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'pl_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .playlist',
					'separator' => 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pl_bg_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .playlist',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'pl_bg_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-audio-player-wrapper .playlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pl_bg_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .playlist',
				'separator' => 'before',
			]
		);	
		$this->end_controls_section();
		$this->start_controls_section(
            'section_player_bg_style',
            [
                'label' => esc_html__('Player Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ap_style!' => ['style-9'],
				],
            ]
        );
		$this->add_responsive_control(
			'player_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .tp-player' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ap_style' => ['style-5','style-7','style-8'],
				],
			]
		);
		$this->add_responsive_control(
			'player_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper .tp-player' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ap_style' => ['style-5'],
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'player_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .tp-player',
				'condition' => [
					'ap_style!' => ['style-4','style-8','style-9'],
				],
			]
		);
		$this->add_control(
			'player_bg_overlay',
			[
				'label' => esc_html__( 'Background Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-4 .tp-player' => 'box-shadow:0 0 500px  {{VALUE}} inset;',
				],
				'condition' => [
					'ap_style' => ['style-4'],
				],
			]
		);
		$this->add_control(
			'player_img_bg_overlay',
			[
				'label' => esc_html__( 'Background Image Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-6 .ap-st5-content' => 'box-shadow:0 0 500px  {{VALUE}} inset;',
				],
				'condition' => [
					'ap_style' => ['style-6'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'player_img_bg_css_filters',
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-player-bg-img',
				'condition' => [
					'ap_style' => ['style-8'],
				],				
			]
		);
		$this->add_control(
			'background_position',
			[
				'label' => esc_html__( 'Background Position', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => theplus_get_image_position_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-4 .tp-player,
					{{WRAPPER}} .tp-audio-player-wrapper.style-6 .ap-st5-content,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-player-bg-img' => 'background-position:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-4','style-6','style-8'],
				],
			]
		);
		$this->add_control(
			'background_repeat',
			[
				'label' => esc_html__( 'Background Repeat', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => theplus_get_image_reapeat_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-4 .tp-player,
					{{WRAPPER}} .tp-audio-player-wrapper.style-6 .ap-st5-content,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-player-bg-img' => 'background-repeat:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-4','style-6','style-8'],
				],
			]
		);
		$this->add_control(
			'background_size',
			[
				'label' => esc_html__( 'Background Size', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => theplus_get_image_size_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-audio-player-wrapper.style-4 .tp-player,
					{{WRAPPER}} .tp-audio-player-wrapper.style-6 .ap-st5-content,
					{{WRAPPER}} .tp-audio-player-wrapper.style-8 .tp-player-bg-img' => 'background-size:{{VALUE}} !important;',
				],
				'condition' => [
					'ap_style' => ['style-4','style-6','style-8'],
				],
			]
		);
		
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'player_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .tp-player',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'player_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-audio-player-wrapper .tp-player' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'player_bg_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-audio-player-wrapper .tp-player',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'section_player_eo_style',
            [
                'label' => esc_html__('Extra Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'eo_autoplay',[
				'label'   => esc_html__( 'Autoplay Loop', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
			]
		);
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$ap_style = $settings['ap_style'];
		$uid_widget=uniqid("ap");
		$split_text =$settings['split_text'];		
		$s_volume = (isset($settings['s_volume']['size'])) ? $settings['s_volume']['size'] : 80;
		
			/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

			/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-audio-player-widget";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

			$ap_trackdetails_splittxt='<span class="splitTxt"> '.esc_html($split_text).' </span>';
			$ap_play_pause ='<div class="tp-ap-pp"><div class="play"><i class="fas fa-play" aria-hidden="true"></i></div><div class="pause"><i class="fas fa-pause" aria-hidden="true"></i></div></div>';
			$ap_rew='<div class="rew"><i class="fas fa-backward" aria-hidden="true"></i></div>';
			$ap_fwd='<div class="fwd"><i class="fas fa-forward" aria-hidden="true"></i></div>';
			$ap_endtime ='<div class="durationtime"></div>';
			$ap_currenttime ='<div class="currenttime">00.00</div>';
			$ap_tracker='<div class="tracker"></div>';
			$ap_volume='<div class="volumeIcon"><i class="fas fa-volume-up vol-icon-toggle" aria-hidden="true"></i><div class="tp-volume-bg"><div class="volume"></div></div></div>';
			$ap_playlisticon ='<div class="playlistIcon"><i class="fas fa-list" aria-hidden="true"></i></div>';

			$i=0;
			$ap_trackdetails_title=$ap_trackdetails_artist=$ap_img='';
			$ap_playlist='<ul class="playlist" id="playlist">';

			foreach ( $settings['playlist'] as $item ) {
				$audiourl=$thumb_img='';
				
				if(!empty($item['audio_source']) && $item['audio_source'] == 'file'){
					$audiourl=$item['source_mp3']['url'];
				}else if(!empty($item['audio_source']) && $item['audio_source'] == 'url'){
					$audiourl=$item['source_mp3_url'];
				}
				
				if(!empty($item['audio_track_image']['id'])){
					$image_id=$item['audio_track_image']['id'];
					$thumb_img=wp_get_attachment_image_src($image_id, $settings['thumbnail_size']);
					$thumb_img= isset($thumb_img[0]) ? $thumb_img[0] : '';
				}else if(!empty($settings['audio_track_image_c']['url'])){					
					$image_id=$settings['audio_track_image_c']['id'];
					$thumb_img=wp_get_attachment_image_src($image_id, $settings['thumbnail_size']);					
					$thumb_img= isset($thumb_img[0]) ? $thumb_img[0] : '';
				}else{
					$thumb_img=THEPLUS_ASSETS_URL.'images/placeholder-grid.jpg';
				}
				
				if($i==0){ 
					$ap_trackdetails_title='<span class="title">'.esc_html($item['title']).'</span>';
					$ap_trackdetails_artist='<span class="artist">'.esc_html($item['author']).'</span>';
					$lazyclass='';
					if($ap_style=='style-3' || $ap_style=='style-8'){
						if(!empty($item['audio_track_image']['url'])){
							$image_id = $item["audio_track_image"]["id"];
							$ap_img= tp_get_image_rander( $image_id,$settings['thumbnail_size']);
						}elseif(!empty($settings['audio_track_image_c']['url'])) {
							$image_id = $settings["audio_track_image_c"]["id"];
							$ap_img= tp_get_image_rander( $image_id,$settings['thumbnail_size']);
						}else{
							$ap_img = '<img src="'.THEPLUS_ASSETS_URL.'"images/placeholder-grid.jpg">';
						}						
					}else if($ap_style=='style-4' || $ap_style=='style-5' || $ap_style=='style-6' || $ap_style=='style-9'){
						if(!empty($item['audio_track_image']['url'])){
							$image_id=$item['audio_track_image']['id'];
							$ap_img=wp_get_attachment_image_src($image_id, $settings['thumbnail_size']);
							$ap_img= isset($ap_img[0]) ? $ap_img[0] : '';
						}elseif(!empty($settings['audio_track_image_c']['url'])) {	
							$image_id=$settings['audio_track_image_c']['id'];
							$ap_img=wp_get_attachment_image_src($image_id, $settings['thumbnail_size']);
							$ap_img= isset($ap_img[0]) ? $ap_img[0] : '';
						}else{
							$ap_img=THEPLUS_ASSETS_URL.'images/placeholder-grid.jpg';
						}
					}
					
					if(tp_has_lazyload()){
						$lazyclass=' lazy-background';
					}
				}
				
				$ap_playlist.='<li audioURL="'.esc_url($audiourl).'" audioTitle="'.esc_html($item['title']).'" artist="'.esc_attr($item['author']).'"  data-thumb="'.esc_url($thumb_img).'">'.esc_html($item['title']).'</li>';
				$i++;				
			}
			$ap_playlist.='</ul>';

			$eo_autoplay = isset($settings['eo_autoplay']) ? $settings['eo_autoplay'] : 'no';

			$eo_autoplayopt = '';
			if($eo_autoplay != 'yes'){
				$eo_autoplayopt = 'data-eoautoplay="disable"'; 
			}
			
			$audio_player = '<div class="tp-audio-player-wrapper '.esc_attr($uid_widget).' '.esc_attr($ap_style).' '.$animated_class.'" '.$animation_attr.' data-id="'.esc_attr($uid_widget).'" data-style="'.esc_attr($ap_style).'" data-apvolume="'.esc_attr($s_volume).'" '.$eo_autoplayopt.'>';
			if($ap_style=='style-1'){
				$audio_player .= '<div class="tp-player">';
					$audio_player .= $ap_playlisticon;
					$audio_player .= '<div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_splittxt.' '.$ap_trackdetails_artist.'</div>';
					$audio_player .= '<div class="controls"> '.$ap_rew.' '.$ap_play_pause.' '.$ap_fwd.' </div>';
					$audio_player .= $ap_volume;					
					$audio_player .= $ap_tracker;
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-2'){
				$audio_player .= '<div class="tp-player">';				
						$audio_player .= '<div class="main-wrapper-style">';					
							$audio_player .= '<div class="controls">';					
								$audio_player .= $ap_play_pause;					
							$audio_player .= '</div>';							
							$audio_player .= $ap_tracker;
							$audio_player .= $ap_volume;							
							$audio_player .= '</div>';
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;						
			}else if($ap_style=='style-3'){
				$audio_player .= '<div class="tp-player">';
					$audio_player .= $ap_playlisticon;
					$audio_player .= '<div class="trackimage">'.$ap_img.'</div>';
					$audio_player .= '<div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_splittxt.' '.$ap_trackdetails_artist.'</div>';
					$audio_player .= '<div class="controls"> '.$ap_rew.' '.$ap_play_pause.' '.$ap_fwd.' </div>';
					$audio_player .= '<div class="ap-time-seek-vol">'.$ap_volume;					
					$audio_player .= '<div class="ap-time">'.$ap_currenttime;
					$audio_player .= $ap_endtime.'</div>';
					$audio_player .= $ap_tracker;
				$audio_player .= '</div></div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-4'){
				$audio_player .= '<div class="tp-player '.esc_attr($lazyclass).'" style="background:url('.$ap_img.');">';
						$audio_player .= $ap_playlisticon;
						$audio_player .= '<div class="ap-title-art">';
						$audio_player .= $ap_trackdetails_title .$ap_trackdetails_artist;
						$audio_player .= '</div>';
						$audio_player .= '<div class="main-wrapper-style">';
							$audio_player .= '<div class="controls">';					
								$audio_player .= $ap_play_pause;					
							$audio_player .= '</div>';							
							$audio_player .= $ap_tracker;
							$audio_player .= $ap_volume;							
						$audio_player .= '</div>';
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-5'){
				$audio_player .= '<div class="tp-player">';
						$audio_player .= $ap_playlisticon;						
						$audio_player .= '<div class="ap-st5-img '.esc_attr($lazyclass).'" style="background:url('.$ap_img.');"></div>';						
						$audio_player .= '<div class="ap-st5-content">';
								$audio_player .= '<div class="ap-controls-track"><div class="controls">'.$ap_play_pause.' <div class="ap-nextprev"> '.$ap_rew.'  '.$ap_fwd.' </div></div>';
								$audio_player .= $ap_tracker.'</div>';
								$audio_player .= '<div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_splittxt.' '.$ap_trackdetails_artist.'</div>';						
						$audio_player .= '</div>';
						$audio_player .= $ap_volume;
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-6'){
				$audio_player .= '<div class="tp-player">';
						$audio_player .= $ap_playlisticon;
							$audio_player .= '<div class="ap-st5-img"><div class="controls">'.$ap_play_pause.' 
											<div class="ap-nextprev"> '.$ap_rew.'  '.$ap_fwd.' </div>
								</div></div>';
						$audio_player .= '<div class="ap-st5-content '.esc_attr($lazyclass).'" style="background:url('.$ap_img.');">';								
								$audio_player .= '<div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_splittxt.' '.$ap_trackdetails_artist.'</div>';						
								$audio_player .= $ap_tracker;								
						$audio_player .= '</div>';
						$audio_player .= $ap_volume;
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;		
			}else if($ap_style=='style-7'){
				$audio_player .= '<div class="tp-player">';
						$audio_player .= '<div class="controls"> '.$ap_rew.' '.$ap_play_pause.' '.$ap_fwd.' </div>';
						$audio_player .= $ap_tracker;
						$audio_player .= '<div class="ap-time-title"> '.$ap_currenttime.' '.$ap_trackdetails_title.' '.$ap_endtime.' </div>';					
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-8'){
				$audio_player .= '<div class="tp-player">';
					$audio_player .= '<div class="tp-player-bg-img '.esc_attr($lazyclass).'"></div>';					
					$audio_player .= '<div class="trackimage">'.$ap_img.'</div>';
					$audio_player .= '<div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_artist.'</div>';
					$audio_player .= '<div class="controls"> '.$ap_rew.' '.$ap_play_pause.' '.$ap_fwd.' </div>';					
					$audio_player .= $ap_tracker;
					$audio_player .= '<div class="ap-time-seek-vol">';
					$audio_player .= '<div class="ap-time">'.$ap_currenttime;
					$audio_player .= $ap_endtime.'</div>';
				$audio_player .= '</div></div>';
				$audio_player .= $ap_playlist;
			}else if($ap_style=='style-9'){
				$audio_player .= '<div class="tp-player">';
					$audio_player .= '<div class="tp-player-hover">';
					$audio_player .= '<div class="tp-player-bg-img '.esc_attr($lazyclass).'" style="background:url('.$ap_img.');"><div class="trackDetails ">'.$ap_trackdetails_title.' '.$ap_trackdetails_artist.'</div></div>';
					$audio_player .= '<div class="controls"> '.$ap_rew.' '.$ap_play_pause.' '.$ap_fwd.' </div>';
				$audio_player .= '</div>';
				
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}
			$audio_player .= '</div>';	
		
			echo $before_content.$audio_player.$after_content;
	}
}