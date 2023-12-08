<?php 
/*
Widget Name: Video Player 
Description: Video player.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Video_Player extends Widget_Base {
		
	public function get_name() {
		return 'tp-video-player';
	}

    public function get_title() {
        return esc_html__('Video', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-video-camera theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Video', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'video_type',
			[
				'label' => esc_html__( 'Source', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => esc_html__( 'Youtube', 'theplus' ),
					'vimeo' => esc_html__( 'Vimeo', 'theplus' ),
					'self-hosted' => esc_html__( 'Self Hosted', 'theplus' ),
				],
			]
		);
		$this->add_control(
            'youtube_id',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('YouTube Id', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => esc_html__('TJ1SDXbij8Y', 'theplus'),
				'placeholder' => esc_html__( 'YouTube ID : TJ1SDXbij8Y', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'video_type' => 'youtube',
				],
            ]
        );
		$this->add_control(
            'vimeo_id',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Vimeo Id', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => esc_html__('27246366', 'theplus'),
				'placeholder' => esc_html__( 'Vimeo ID : 27246366', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'video_type' => 'vimeo',
				],
            ]
        );
		$this->add_control(
			'mp4_link',
			[
				'label' => esc_html__( 'Mp4 Video Link', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'video',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'video_type' => 'self-hosted',
				],
			]
		);
        $this->end_controls_section();
		$this->start_controls_section(
			'content_video_option',
			[
				'label' => esc_html__( 'Video Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'video_options',
			[
				'label' => esc_html__( 'Video Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'video_autoplay',
			[
				'label' => esc_html__( 'AutoPlay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'video_muted',
			[
				'label' => esc_html__( 'Mute', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);
		$this->add_control(
			'video_loop',
			[
				'label' => esc_html__( 'Loop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);
		$this->add_control(
			'video_controls',
			[
				'label' => esc_html__( 'Controls', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'video_type!' => 'vimeo',
				],
			]
		);
		$this->add_control(
			'showinfo',
			[
				'label' => esc_html__( 'Video Info', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'default' => 'yes',
				'description' => 'Video Info is <a href="https://developers.google.com/youtube/player_parameters#showinfo" class="theplus-btn" target="_blank">deprecated.</a>',
				'condition' => [
					'video_type' => [ 'youtube' ],
				],
			]
		);
		
		$this->add_control(
			'video_touch_disable',
			[
				'label' => esc_html__( 'Video Touch Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'theplus' ),
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'modest_branding',
			[
				'label' => esc_html__( 'Modest Branding', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => [ 'youtube' ],
					'video_controls' => 'yes',
				],
			]
		);
		$this->add_control(
			'video_color',
			[
				'label' => esc_html__( 'Controls Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'video_type' => [ 'vimeo'],
				],
			]
		);
		$this->add_control(
			'rel',
			[
				'label' => esc_html__( 'Suggested Videos', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'description' => 'Suggested Videos <a href="https://developers.google.com/youtube/player_parameters#rel" class="theplus-btn" target="_blank">Parameter change.</a>',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_privacy',
			[
				'label' => esc_html__( 'Privacy Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'theplus' ),
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);
		$this->add_control(
			'vimeo_title',
			[
				'label' => esc_html__( 'Intro Title', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label' => esc_html__( 'Intro Portrait', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label' => esc_html__( 'Intro Byline', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'theplus' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'youtube',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_icon',
			[
				'label' => esc_html__( 'Image/Icon', 'theplus' ),
			]
		);
		$this->add_control(
			'image_banner',
			[
				'label' => esc_html__( 'Only Icon / Full Banner', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'banner_img',
				'options' => [
					'only_icon' => esc_html__( 'Only Icon image', 'theplus' ),
					'banner_img' => esc_html__( 'Banner Image', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'only_img',
			[
				'label' => esc_html__( 'Choose Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'image_banner' => 'only_icon',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'only_img_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition'    => [
					'image_banner' => 'only_icon',
				],
			]
		);
		
		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__( 'Icon Align', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'condition'    => [
					'image_banner' => 'only_icon',
				],
			]
		);
		$this->add_control(
			'display_banner_image',
			[
				'label' => esc_html__( 'Banner Image', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'label_on' => esc_html__( 'On', 'theplus' ),
				'default' => 'no',
				'condition'    => [
					'image_banner' => 'banner_img',
				],
			]
		);
		$this->add_control(
			'banner_image',
			[
				'label' => esc_html__( 'Image Upload', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'banner_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
			]
		);
		$this->add_control(
			'image_video',
			[
				'label' => esc_html__( 'Icon Upload', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
			]
		);
		
		$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'image_video_thumbnail',
					'default' => 'full',
					'separator' => 'none',
					'separator' => 'after',
					'condition'    => [
						'display_banner_image' => 'yes',
						'image_banner' => 'banner_img',
					],
				]
			);
		$this->add_control(
            'video_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Title of Video', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => esc_html__('Video Title', 'theplus'),
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
            ]
        );
		$this->add_control(
            'video_desc',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => esc_html__('Video Description', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => '',
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
            ]
        );
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_schema_markup',
			[
				'label' => esc_html__( 'Schema Markup', 'theplus' ),
			]
		);
		$this->add_control(
			'markupSch',
			[
				'label' => esc_html__( 'Schema Markup', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'label_on' => esc_html__( 'On', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'video_date',
			[
				'label' => __( 'Video Date', 'theplus' ),
				'type' => Controls_Manager::DATE_TIME,
				'condition'    => [
					'markupSch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__('Video Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'video_title_typography',
                'label' => esc_html__('Title Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .ts-video-caption-text',
				'separator' => 'before',
				'condition'    => [
					'display_banner_image' => 'yes',
				],
            ]
        );
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ts-video-caption-text' => 'color: {{VALUE}};',
				],
				'default' => '#313131',
				'condition'    => [
					'display_banner_image' => 'yes',
				],
			]
		);
		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__( 'Title Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ts-video-caption-text' => 'background: {{VALUE}};',
				],
				'default' => '#ffffff',
				'condition'    => [
					'display_banner_image' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_desc_styling',
            [
                'label' => esc_html__('Video Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'display_banner_image' => 'yes',
					'image_banner' => 'banner_img',
				],
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'video_desc_typography',
                'label' => esc_html__('Description Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .tp-video-desc',
				'separator' => 'before',
				'condition'    => [
					'display_banner_image' => 'yes',
				],
            ]
        );
		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-video-desc' => 'color: {{VALUE}};',
				],
				'default' => '#888',
				'condition'    => [
					'display_banner_image' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_video_styling',
            [
                'label' => esc_html__('Video Styling', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'popup_video',
			[
				'label' => esc_html__( 'Video On Popup', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'label_on' => esc_html__( 'On', 'theplus' ),
				'default' => 'no',
				'condition'    => [
					'image_banner' => 'banner_img',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_effect_video' );

		$this->start_controls_tab(
			'tab_effect_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'video_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow',				
			]
		);
		$this->add_responsive_control(
			'video_bor_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video-box-shadow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow',
			]
		);
		$this->add_control(
            'video_transform',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Transform Effect', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
				'placeholder' => esc_html__( 'rotate(2deg) skew(50deg)', 'theplus' ),
                'default' => '',
            ]
        );
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_effect_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'video_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow:hover',
			]
		);
		$this->add_responsive_control(
			'video_bor_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video-box-shadow:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hover_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow:hover',
			]
		);
		$this->add_control(
            'hover_video_transform',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Hover Transform Effect', 'theplus'),
                'label_block' => true,
				'placeholder' => esc_html__( 'rotate(2deg) skew(50deg)', 'theplus' ),
                'default' => '',
            ]
        );
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .pt_plus_video-box-shadow:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*icon style*/
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'icon_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_animation_effect',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => [
					'pulse'  => esc_html__( 'Pulse', 'theplus' ),
					'floating'  => esc_html__( 'Floating', 'theplus' ),
					'tossing'  => esc_html__( 'Tossing', 'theplus' ),
					'rotating'  => esc_html__( 'Rotating', 'theplus' ),
					'drop_waves'  => esc_html__( 'Drop Waves', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_animation_hover',
			[
				'label'        => esc_html__( 'Hover Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_animation_duration',
			[	
				'label' => esc_html__( 'Duration Time', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 's',
				'range' => [
					's' => [
						'min' => 0.5,
						'max' => 50,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 2.5,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video_player .tp-video-icon-inner,{{WRAPPER}} .pt_plus_video_player .tp-video-popup,{{WRAPPER}} .pt_plus_video_player .tp-video-popup-icon .tp-video-icon' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect!' => 'drop_waves',
				],
			]
		);
		$this->add_control(
			'icon_transform_origin',
			[
				'label' => esc_html__( 'Transform Origin', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'top left'  => esc_html__( 'Top Left', 'theplus' ),
					'top center"'  => esc_html__( 'Top Center', 'theplus' ),
					'top right'  => esc_html__( 'Top Right', 'theplus' ),
					'center left'  => esc_html__( 'Center Left', 'theplus' ),
					'center center'  => esc_html__( 'Center Center', 'theplus' ),
					'center right'  => esc_html__( 'Center Right', 'theplus' ),
					'bottom left'  => esc_html__( 'Bottom Left', 'theplus' ),
					'bottom center'  => esc_html__( 'Bottom Center', 'theplus' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video_player .tp-video-icon-inner,{{WRAPPER}} .pt_plus_video_player .tp-video-popup,{{WRAPPER}} .pt_plus_video_player .tp-video-popup-icon .tp-video-icon' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
				],
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect' => 'rotating',
				],
			]
		);
		$this->add_control(
			'drop_waves_color',
			[
				'label' => esc_html__( 'Drop Wave Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_video_player .image-drop_waves:after,{{WRAPPER}} .pt_plus_video_player .hover_drop_waves:after' => 'background: {{VALUE}}'
				],
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect' => 'drop_waves',
				],
			]
		);
		$this->add_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'Icon Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video_player .tp-video-icon-inner,{{WRAPPER}} .pt_plus_video_player .tp-video-popup,{{WRAPPER}} .pt_plus_video_player .tp-video-popup-icon .tp-video-icon,{{WRAPPER}} .pt_plus_video_player .image-drop_waves:after,{{WRAPPER}} .pt_plus_video_player .hover_drop_waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'play_icon_size',
			[	
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 'px',
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_video_player .tp-video-icon-inner,{{WRAPPER}} .pt_plus_video_player .tp-video-popup,{{WRAPPER}} .pt_plus_video_player .tp-video-popup-icon' => 'max-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*icon style*/
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
		
		/*Mask Image tab*/
		$this->start_controls_section(
            'section_image_masking_styling',
            [
                'label' => esc_html__('Mask Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'mask_image_display',
			[
				'label' => esc_html__( 'Mask Image Shape', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__('Use PNG image with the shape you want to mask around Media.', 'theplus' ),
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mask_shape_image',
			[
				'label' => esc_html__( 'Mask Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_video-box-shadow.creative-mask-media' => 'mask-image: url({{URL}});-webkit-mask-image: url({{URL}});',
				],
				'condition' => [
					'mask_image_display' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'mask_shape_image_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Mask Position', 'theplus'),
				'default' => 'center center',
				'options' => theplus_get_image_position_options(),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_video-box-shadow.creative-mask-media' => '-webkit-mask-position:{{VALUE}};',
				],
				'condition' => [					
					'mask_image_display' => 'yes',
				],			
			]
        );
		$this->add_responsive_control(
            'mask_shape_image_repeat', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Mask Repeat', 'theplus'),
				'default' => 'no-repeat',
				'options' => theplus_get_image_reapeat_options(),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_video-box-shadow.creative-mask-media' => 'mask-repeat:{{VALUE}};-webkit-mask-repeat:{{VALUE}};',
				],
				'condition' => [					
					'mask_image_display' => 'yes',
				],
			]
        );
		$this->add_responsive_control(
            'mask_shape_image_size', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Mask Size', 'theplus'),
				'default' => 'contain',
				'options' => theplus_get_image_size_options(),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_video-box-shadow.creative-mask-media' => 'mask-size:{{VALUE}};-webkit-mask-size:{{VALUE}};',
				],
				'condition' => [					
					'mask_image_display' => 'yes',
				],
			]
        );
		$this->end_controls_section();
		/*Mask Image tab*/

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

	}
	protected function render() {

        $settings = $this->get_settings_for_display();
		
		//Google Schema Attributes
		$mainsch =  $thumbsch =  $titlesch =  $descsch = $uploadate = '';
		if(!empty($settings[ 'markupSch' ]) && $settings[ 'markupSch' ]=='yes'){
			$mainsch = 'itemscope="" itemprop="VideoObject" itemtype="http://schema.org/VideoObject"';
			$thumbsch = 'itemprop="thumbnailUrl"';
			$titlesch = 'itemprop="name"';
			$descsch = 'itemprop="description"';
			$uploadate = $this->get_settings( 'video_date' );
		}
		
		$uid=uniqid('video_player');
			$video_type=$settings['video_type'];
			$youtube_id=$vimeo_id=$mp4_link='';
			if(!empty($settings["youtube_id"])){
				$youtube_id=$settings["youtube_id"];
			}
			if(!empty($settings["vimeo_id"])){
				$vimeo_id=$settings["vimeo_id"];
			}
			if(!empty($settings["mp4_link"]['url'])){
				$mp4_link=$settings["mp4_link"]['url'];
			}
			$icon_effect='';
			if(!empty($settings["icon_continuous_animation"]) && $settings["icon_continuous_animation"]=='yes'){
				if($settings["icon_animation_hover"]=='yes'){
					$animation_class='hover_';
				}else{
					$animation_class='image-';
				}
				$icon_effect=$animation_class.$settings["icon_animation_effect"];
			}
			$video_content=$banner_url=$video_space=$image_video_url=$image_video=$image_alt=$only_image=$title=$banner_image='';
			
				$icon_align_video = '';
				if(!empty($settings["video_title"])){
					$title = '<div class="ts-video-caption-text" >';
						$title .= '<span '.$titlesch.'>'.esc_html($settings["video_title"]).'</span>';
						if(! empty ( $settings[ "video_desc" ] )){
							$title .= '<div class="tp-video-desc" '.$descsch.' >';
								$title .= esc_html ( $settings[ "video_desc" ] ) ;
							$title .= '</div>';
						}
					$title .= '</div>';
				}
				if(!empty($settings["only_img"]["url"])){						
						$only_img=$settings['only_img']['id'];
						$img = wp_get_attachment_image_src($only_img,$settings['only_img_thumbnail_size']);
						$only_img_icon = isset($img[0]) ? $img[0] : '';
						$only_image .='<img class="ts-video-only-icon" src="'.esc_url($only_img_icon).'" alt="'.esc_attr__("play-icon","theplus").'" />';
				}
				
			if(!empty($settings["image_video"]["url"])){
				$image_video_src=$settings['image_video']['id'];
				$img = wp_get_attachment_image_src($image_video_src,$settings['image_video_thumbnail_size']);
				$image_video = isset($img[0]) ? $img[0] : '';
				
				$image_id=$settings["image_video"]["id"];
				$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
				if(!$image_alt){
					$image_alt = get_the_title($image_id);
				}else if(!$image_alt){
					$image_alt = 'Plus video thumb';
				}
				$image_video_url .='<div class="tp-video-icon-inner '.esc_attr($icon_effect).'"><img class="ts-video-icon" src="'.esc_url($image_video).'"  alt="'.esc_attr($image_alt).'" /></div>';
			}
			if(!empty($settings["banner_image"]["url"])){
				$banner_image=$settings['banner_image']['id'];
				$img = wp_get_attachment_image_src($banner_image,$settings['banner_image_thumbnail_size']);
				$banner_image = isset($img[0]) ? $img[0] : '';
				$banner_url .='<img class="ts-video-image-zoom set-image" '.$thumbsch.' content="'.esc_url( $banner_image ).'" src="'.esc_url($banner_image).'" alt="" /><div class="tp-video-popup-icon"> <div class="tp-video-icon '.esc_attr($icon_effect).'"><img class="ts-video-caption" src="'.esc_url($image_video).'" alt="'.esc_attr($image_alt).'" /></div></div>'.$title;
			}
			
			$youtube_attr=$youtube_frame_attr=$video_touchable=$self_video_attr=$vimeo_frame_attr='';
			if(!empty($settings['video_autoplay']) && $settings['video_autoplay']=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;autoplay=1&amp;version=3';
					$youtube_attr.=' allow="autoplay; encrypted-media"  ';
				}
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;autoplay=1';
				}
				if($video_type=='self-hosted'){
					$self_video_attr.=' autoplay';
					$self_video_attr.=' playsinline=""';
				}
			}
			
			if(!empty($settings['video_loop']) && $settings['video_loop']=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;loop=1&amp;playlist='.esc_attr($settings["youtube_id"]);					
				}
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;loop=1';
				}
				if($video_type=='self-hosted'){
					$self_video_attr.=' loop ';
				}
			}
			
			if(!empty($settings["video_controls"]) && $settings["video_controls"]=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;controls=1';
				}
				if($video_type=='self-hosted'){
					$self_video_attr.=' controls ';
				}
			}else{
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;controls=0';
				}
			}
			if(!empty($settings["showinfo"]) && $settings["showinfo"]=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;showinfo=1';
				}
			}else{
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;showinfo=0';
				}
			}
			if(!empty($settings["modest_branding"]) && $settings["modest_branding"]=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;modestbranding=1';
				}
			}else{
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;modestbranding=0';
				}
			}
			if(!empty($settings["rel"]) && $settings["rel"]=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;rel=0';
				}
			}else{
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;rel=1';
				}
			}
			$youtube_privacy='';
			if(!empty($settings["yt_privacy"]) && $settings["yt_privacy"]=='yes'){
				if($video_type=='youtube'){
					$youtube_privacy .='-nocookie';
				}
			}else{
				if($video_type=='youtube'){
					$youtube_privacy .='';
				}
			}
			if(!empty($settings["video_muted"]) && $settings["video_muted"]=='yes'){
				if($video_type=='youtube'){
					$youtube_frame_attr .='&amp;mute=1';
				}
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;muted=1';
				}
				if($video_type=='self-hosted'){
					$self_video_attr.=' muted ';
				}
			}
			if(!empty($settings["video_color"])){
				if($video_type=='vimeo'){
					$video_color=str_replace( '#', '', $settings['video_color'] );
					$vimeo_frame_attr .='&amp;color='.esc_attr($video_color).';';
				}
			}
			if(!empty($settings["vimeo_title"]) && $settings["vimeo_title"]=='yes'){
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;title=1;';
				}
			}else{
				$vimeo_frame_attr .='&amp;title=0;';
			}
			if(!empty($settings["vimeo_portrait"]) && $settings["vimeo_portrait"]=='yes'){
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;portrait=1;';
				}
			}else{
				$vimeo_frame_attr .='&amp;portrait=0;';
			}
			if(!empty($settings["vimeo_byline"]) && $settings["vimeo_byline"]=='yes'){
				if($video_type=='vimeo'){
					$vimeo_frame_attr .='&amp;byline=1;';
				}
			}else{
				$vimeo_frame_attr .='&amp;byline=0;';
			}
			if(!empty($settings["video_touch_disable"]) && $settings["video_touch_disable"]=='yes'){
				$video_touchable=' not-touch ';
			}
			$image_banner=$settings['image_banner'];
			$display_banner_image=$settings['display_banner_image'];
			if ($image_banner == 'banner_img'){
					if($display_banner_image =='yes'){
						if($settings['popup_video'] =='yes'){
							if($video_type=='youtube'){
									$video_content .='<a href="https://www.youtube'.$youtube_privacy.'.com/embed/'.esc_attr($youtube_id).'" data-lity >'.$banner_url.'</a>';
								} else if($video_type=='vimeo') {
									$video_content .='<a href="https://player.vimeo.com/video/'.esc_attr($vimeo_id).'" data-lity >'.$banner_url.'</a>';
								} else if ($video_type=='self-hosted')  {
									$video_content .='<a href="'.esc_url($mp4_link).'" data-lity type="video/mp4">'.$banner_url.'</a>';						
							   }
								//  $video_space = 'video-space';
								$video_space = '';
						}else{
							if($video_type=='youtube'){
								$video_content .='<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-'.esc_attr($video_type).'" data-mode="lazyload" data-provider="'.$video_type.'" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="ts-video-embed-container" ><img class="ts-video-thumbnail" data-object-fit="" '.$thumbsch.' content="'.esc_url( $banner_image ).'" src="'.esc_url($banner_image).'" alt="'.esc_attr("Video Thumbnail").'"><h5 class="ts-video-title">'.$title.'</h5><span class="ts-video-lazyload" data-allowfullscreen="" data-class="pt-plus-video-frame fitvidsignore" data-frameborder="0" data-scrolling="no" data-src="https://www.youtube'.$youtube_privacy.'.com/embed/'.esc_attr($youtube_id).'?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1'.$youtube_frame_attr.'"  data-sandbox="allow-scripts allow-same-origin allow-presentation allow-forms" data-width="480" data-height="270"></span><button class="ts-video-play-btn ts-video-blay-btn-youtube" type="button">'.$image_video_url.'</button>';
								if(!empty($settings[ 'markupSch' ])){
									$video_content .= '<div class="tp-video-upload" itemprop="uploadDate" content="'.$uploadate.'" style="display: none;"></div><div class="tp-video-upload" itemprop="contentUrl" content="https://www.youtube' . $youtube_privacy . '.com/embed/' . esc_attr ( $youtube_id ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1' . $youtube_frame_attr . '" style="display: none;"></div>';
								}
								$video_content .='</div></div>';
							}else if($video_type=='vimeo'){
								$video_content .='<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-'.esc_attr($video_type).'" data-mode="lazyload" data-provider="'.$video_type.'" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="ts-video-embed-container" ><img class="ts-video-thumbnail" data-object-fit="" '.$thumbsch.' content="'.esc_url( $banner_image ).'" src="'.esc_url($banner_image).'" alt="'.esc_attr("Video Thumbnail").'"><h5 class="ts-video-title">'.$title.'</h5><span class="ts-video-lazyload" data-allowfullscreen="" data-class="pt-plus-video-frame fitvidsignore" data-frameborder="0" data-scrolling="no" data-src="https://player.vimeo.com/video/'.esc_attr($vimeo_id).'?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" data-sandbox="allow-scripts allow-same-origin allow-presentation allow-forms" data-width="480" data-height="270"></span><button class="ts-video-play-btn ts-video-blay-btn-youtube" type="button">'.$image_video_url.'</button>';
								if(!empty($settings[ 'markupSch' ])){
									$video_content .= '<div class="tp-video-upload" itemprop="uploadDate" content="'.$uploadate.'" style="display: none;"></div><div class="tp-video-upload" itemprop="contentUrl" content="https://player.vimeo.com/video/' . esc_attr ( $vimeo_id ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" style="display: none;"></div>';
								}
								$video_content .='</div></div>';							
							}else if($video_type=='self-hosted'){
								$video_content .='<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-'.esc_attr($video_type).'" data-mode="lazyload" data-provider="'.$video_type.'" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="ts-video-embed-container" ><img class="ts-video-thumbnail" data-object-fit="" '.$thumbsch.' content="'.esc_url( $banner_image ).'" src="'.esc_url($banner_image).'" alt="'.esc_attr("Video Thumbnail").'"><h5 class="ts-video-title">'.$title.'</h5><div class="video_container"><video class="ts-video-poster" width="100%" poster="'.esc_url($banner_image).'" controls > <source src="'.esc_url($mp4_link).'" type="video/mp4" ></video></div></span><button class="ts-video-play-btn ts-video-blay-btn-youtube" type="button">'.$image_video_url.'</button>';
								if(!empty($settings[ 'markupSch' ])){
									$video_content .= '<div class="tp-video-upload" itemprop="uploadDate" content="'.$uploadate.'" style="display: none;"></div><div class="tp-video-upload" itemprop="contentUrl" content="' . esc_url ( $mp4_link ) . '" style="display: none;"></div>';
								}
								$video_content .='</div></div>';							
							}
						}
					}else{
					
						if($video_type=='youtube'){
							$video_content .='<div class="ts-video-wrapper embed-container  ts-type-'.esc_attr($video_type).'"><iframe id="'.$uid.'" width="100%"  src="https://www.youtube'.$youtube_privacy.'.com/embed/'.esc_attr($youtube_id).'?&amp;autohide=1&amp;showtitle=0'.$youtube_frame_attr.'" '.$youtube_attr.' frameborder="0" allowfullscreen></iframe></div>';
						}else if($video_type=='vimeo'){
							$video_content .='<div class="ts-video-wrapper embed-container  ts-type-'.esc_attr($video_type).'"><iframe id="'.$uid.'" src="https://player.vimeo.com/video/'.esc_attr($vimeo_id).'?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;'.$vimeo_frame_attr.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
						}else if($video_type=='self-hosted'){
							$video_content .='<div class="ts-video-wrapper ts-type-'.esc_attr($video_type).'"><video class="lazy" width="100%" '.esc_attr($self_video_attr).'> <source src="'.esc_url($mp4_link).'" type="video/mp4" ></video></div>';
						}
					}					
			}else if ($image_banner == 'only_icon'){
					if($display_banner_image!='yes'){
							if($video_type=='youtube'){
									$video_content .='<a href="https://www.youtube.com/embed/'.esc_attr($youtube_id).'" class="tp-video-popup '.esc_attr($icon_effect).'" data-lity >'.$only_image.'</a>';
								} else if($video_type=='vimeo') {
									$video_content .='<a href="https://player.vimeo.com/video/'.esc_attr($vimeo_id).'" class="tp-video-popup '.esc_attr($icon_effect).'" data-lity >'.$only_image.'</a>';
								} else if ($video_type=='self-hosted')  {
									$video_content .='<a href="'.esc_url($mp4_link).'" class="tp-video-popup '.esc_attr($icon_effect).'" data-lity type="video/mp4">'.$only_image.'</a>';	
								}
					}
					$icon_align_video= $settings['icon_align'];
			}
			
			/*--On Scroll View Animation ---*/
				include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

			/*--Plus Extra ---*/
				$PlusExtra_Class = "";
				include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

			$mask_image='';
			if(!empty($settings["mask_image_display"]) && $settings["mask_image_display"]=='yes'){
				$mask_image=' creative-mask-media';
			}
			$video_player ='<div class="pt_plus_video-box-shadow '.esc_attr($uid).' '.esc_attr($animated_class).' '.$mask_image.'" '.$animation_attr.'>';
			$video_player .='<div class="pt_plus_video_player '.esc_attr($video_touchable).' '.esc_attr($video_space).' text-'.esc_attr($icon_align_video).'">';
					$video_player .=$video_content;
					if($display_banner_image!='yes'){
					$video_player .=$banner_url;
					}
			$video_player .='</div>';
			$video_player .='</div>';
			
			$css_rules='';
			if(!empty($settings["video_transform"]) || !empty($settings["hover_video_transform"])){
				$css_rules .='<style>';
				if(!empty($settings["video_transform"])){
					$css_rules .='.'.esc_attr($uid).'.pt_plus_video-box-shadow{-webkit-transform: '.esc_attr($settings["video_transform"]).';-ms-transform: '.esc_attr($settings["video_transform"]).';-moz-transform: '.esc_attr($settings["video_transform"]).';-o-transform: '.esc_attr($settings["video_transform"]).';transform: '.esc_attr($settings["video_transform"]).';}';
				}
				if(!empty($settings["hover_video_transform"])){
					$css_rules .='.'.esc_attr($uid).'.pt_plus_video-box-shadow:hover{-webkit-transform: '.esc_attr($settings["hover_video_transform"]).';-ms-transform: '.esc_attr($settings["hover_video_transform"]).';-moz-transform: '.esc_attr($settings["hover_video_transform"]).';-o-transform: '.esc_attr($settings["hover_video_transform"]).';transform: '.esc_attr($settings["hover_video_transform"]).';}';
				}
				$css_rules .='</style>';
			}
			
			echo $css_rules.$before_content.$video_player.$after_content;
	}
    protected function content_template() {
	
    }

}
