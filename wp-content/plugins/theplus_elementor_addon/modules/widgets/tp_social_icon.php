<?php 
/*
Widget Name: Social Icon
Description: share social icon list design.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Social_Icon extends Widget_Base {
		
	public function get_name() {
		return 'tp-social-icon';
	}

    public function get_title() {
        return esc_html__('Social Icon', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-share-square-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'styles',[
				'label' => esc_html__( 'Style','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'separator' => 'after',
				'options' => [
					'style-1' => esc_html__( 'Style 1','theplus' ),
					'style-2' => esc_html__( 'Style 2','theplus' ),
					'style-3' => esc_html__( 'Style 3','theplus' ),
					'style-4' => esc_html__( 'Style 4','theplus' ),
					'style-5' => esc_html__( 'Style 5','theplus' ),
					'style-6' => esc_html__( 'Style 6','theplus' ),
					'style-7' => esc_html__( 'Style 7','theplus' ),
					'style-8' => esc_html__( 'Style 8','theplus' ),
					'style-9' => esc_html__( 'Style 9','theplus' ),
					'style-10' => esc_html__( 'Style 10','theplus' ),
					'style-11' => esc_html__( 'Style 11','theplus' ),
					'style-12' => esc_html__( 'Style 12','theplus' ),
					'style-13' => esc_html__( 'Style 13','theplus' ),
					'style-14' => esc_html__( 'Style 14','theplus' ),
					'style-15' => esc_html__( 'Style 15','theplus' ),
					'custom' => esc_html__( 'Custom','theplus' ),
				],
			]
		);
		$this->add_control(
			'hover_animation',[
				'label' => esc_html__( 'Select Hover Style','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover-1',
				'separator' => 'after',
				'options' => [
					'hover-1' => esc_html__( 'Style 1','theplus' ),
					'hover-2' => esc_html__( 'Style 2','theplus' ),
				],
				'condition' => [
					'styles' => ['style-14','style-15'],
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'pt_plus_social_icons',[
				'label' => esc_html__( 'Social Network Select','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'none' => esc_html__( 'None','theplus' ),
					'fa-deviantart' => esc_html__( 'Deviantart ','theplus' ),
					'fa-digg' => esc_html__( 'Digg ','theplus' ),
					'fa-dribbble' => esc_html__( 'Dribbble ','theplus' ),
					'fa-dropbox' => esc_html__( 'Dropbox ','theplus' ),
					'fa-facebook' => esc_html__( 'Facebook ','theplus' ),
					'fa-flickr' => esc_html__( 'Flickr ','theplus' ),
					'fa-foursquare' => esc_html__( 'Foursquare ','theplus' ),
					'fa-google-plus' => esc_html__( 'Google + ','theplus' ),
					'fa-instagram' => esc_html__( 'Instagram ','theplus' ),
					'fa-lastfm' => esc_html__( 'LastFM ','theplus' ),
					'fa-linkedin' => esc_html__( 'LinkedIN ','theplus' ),
					'fa-pinterest-p' => esc_html__( 'Pinterest ','theplus' ),
					'fa-rss' => esc_html__( 'RSS ','theplus' ),
					'fa-tumblr' => esc_html__( 'Tumblr ','theplus' ),
					'fa-twitter' => esc_html__( 'Twitter ','theplus' ),
					'fa-vimeo' => esc_html__( 'Vimeo ','theplus' ),
					'fa-wordpress' => esc_html__( 'Wordpress ','theplus' ),
					'fa-youtube' => esc_html__( 'YouTube','theplus' ),
					'fa-envelope' => esc_html__( 'Mail','theplus' ),
					'fa-yelp' => esc_html__( 'Yelp','theplus' ),
					'fa-xing' => esc_html__( 'Xing ','theplus' ),
					'fa-spotify' => esc_html__( 'Spotify ','theplus' ),
					'fa-houzz' => esc_html__( 'Houzz ','theplus' ),
					'fa-skype' => esc_html__( 'Skype ','theplus' ),
					'fa-slideshare' => esc_html__( 'Slideshare ','theplus' ),
					'fa-bandcamp' => esc_html__( 'Bandcamp ','theplus' ),
					'fa-soundcloud' => esc_html__( 'Soundcloud ','theplus' ),
					'fa-snapchat-ghost' => esc_html__( 'Snapchat ','theplus' ),
					'fa-behance' => esc_html__( 'Behance ','theplus' ),
					'fa-windows' => esc_html__( 'Windows','theplus' ),
					'fa-video-camera' => esc_html__( 'Video ','theplus' ),
					'fa-tripadvisor' => esc_html__( 'TripAdvisor ','theplus' ),
					'fa-vk' => esc_html__( 'VK ','theplus' ),
					'fa-odnoklassniki-square' => esc_html__( 'Odnoklassniki','theplus' ),
					'fa-odnoklassniki' => esc_html__( 'Odnoklassniki 1','theplus' ),
					'fa-get-pocket' => esc_html__( 'Get Pocket','theplus' ),
					'fa-tiktok' => esc_html__( 'Tiktok','theplus' ),
					'custom' => esc_html__( 'Custom','theplus' ),
				],
			]
		);
		$repeater->add_control(
			'pt_plus_social_icon_custom',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-whatsapp',
				'condition' => [
					'pt_plus_social_icons' => 'custom',	
				],	
			]
		);
		$repeater->add_control(
			'social_url',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
			]
		);
		$repeater->add_control(
			'social_text',[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => esc_html__('Title', 'theplus'),
				'default' => '',
				'dynamic' => ['active'   => true,],
			]
		);
		
		$repeater->add_control(
			'icon_color',[
				'label' => esc_html__('Icon Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#d3d3d3',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-12) a,{{WRAPPER}} {{CURRENT_ITEM}}.style-12 a .fa' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'icon_hover_color',[
				'label' => esc_html__('Icon Hover Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-12):not(.style-4):hover a,{{WRAPPER}} {{CURRENT_ITEM}}.style-12 a span,{{WRAPPER}} {{CURRENT_ITEM}}.style-4 a i.fa,{{WRAPPER}} {{CURRENT_ITEM}}.style-5:hover a i.fa,{{WRAPPER}} {{CURRENT_ITEM}}.style-14 a span' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'bg_color',[
				'label' => esc_html__('Background Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#404040',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-3):not(.style-9):not(.style-11):not(.style-12) a,{{WRAPPER}} {{CURRENT_ITEM}}.style-12 a .fa' => 'background: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.style-3' => 'background: {{VALUE}};border-color: {{VALUE}};background-clip: content-box;',
					'{{WRAPPER}} {{CURRENT_ITEM}}.style-9:hover a span:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.style-11 a:before' => '-webkit-box-shadow: inset 0 0 0 70px {{VALUE}};-moz-box-shadow: inset 0 0 0 70px {{VALUE}};box-shadow: inset 0 0 0 70px {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'bg_hover_color',[
				'label' => esc_html__('Background Hover Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#222222',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-3):not(.style-9):not(.style-11):not(.style-12):hover a,{{WRAPPER}} {{CURRENT_ITEM}}.style-6 a .social-hover-style,{{WRAPPER}} {{CURRENT_ITEM}}.style-12:hover a span' => 'background: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.style-3:hover' => 'background: {{VALUE}};border-color: {{VALUE}};background-clip: content-box;',					
					'{{WRAPPER}} {{CURRENT_ITEM}}.style-11:hover a:before' => '-webkit-box-shadow: inset 0 0 0 4px {{VALUE}};-moz-box-shadow: inset 0 0 0 4px {{VALUE}};box-shadow: inset 0 0 0 4px {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'border_color',[
				'label' => esc_html__('Border Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#404040',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-11):not(.style-12):not(.style-13) a,{{WRAPPER}} {{CURRENT_ITEM}}.style-12 a .fa,{{WRAPPER}} {{CURRENT_ITEM}}.style-13 a:after,{{WRAPPER}} {{CURRENT_ITEM}}.style-13 a:before' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'border_hover_color',[
				'label' => esc_html__('Border Hover Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#222222',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.style-11):not(.style-12):not(.style-13):hover a,{{WRAPPER}} {{CURRENT_ITEM}}.style-12:hover a span,{{WRAPPER}} {{CURRENT_ITEM}}.style-13:hover a:after,{{WRAPPER}} {{CURRENT_ITEM}}.style-13:hover a:before' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'loop_magic_scroll',[
				'label'   => esc_html__( 'Magic Scroll', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_Option_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Scroll Options', 'theplus' ),
				'name'           => 'loop_scroll_option',
				'render_type'  => 'template',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->start_controls_tabs( 'loop_tab_magic_scroll' );
		$repeater->start_controls_tab(
			'loop_tab_scroll_from',
			[
				'label' => esc_html__( 'Initial', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_From_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Initial Position', 'theplus' ),
				'name'           => 'loop_scroll_from',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'loop_tab_scroll_to',
			[
				'label' => esc_html__( 'Final', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_To_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Final Position', 'theplus' ),
				'name'           => 'loop_scroll_to',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'plus_tooltip',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);

		$repeater->start_controls_tabs( 'plus_tooltip_tabs' );

		$repeater->start_controls_tab(
			'plus_tooltip_content_tab',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'render_type'  => 'template',
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal_desc',
				'options' => [
					'normal_desc'  => esc_html__( 'Content Text', 'theplus' ),
					'content_wysiwyg'  => esc_html__( 'Content WYSIWYG', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_wysiwyg',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'render_type'  => 'template',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'plus_tooltip_content_type' => 'content_wysiwyg',
					'plus_tooltip' => 'yes',
				],
			]				
		);
		$repeater->add_control(
			'plus_tooltip_content_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'theplus' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'plus_tooltip_content_typography',
				'selector' => '{{WRAPPER}} .tippy-tooltip .tippy-content',
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'plus_tooltip_content_color',
			[
				'label'  => esc_html__( 'Text Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tippy-tooltip .tippy-content,{{WRAPPER}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'plus_tooltip_styles_tab',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			array(
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_opt',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$repeater->add_group_control(
			\Theplus_Loop_Tooltips_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Style Options', 'theplus' ),
				'name'           => 'tooltip_style',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'plus_mouse_move_parallax',
			[
				'label'        => esc_html__( 'Mouse Move Parallax', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_group_control(
			\Theplus_Mouse_Move_Parallax_Group::get_type(),
			array(
				'label' => esc_html__( 'Parallax Options', 'theplus' ),
				'name'           => 'plus_mouse_parallax',
				'condition'    => [
					'plus_mouse_move_parallax' => [ 'yes' ],
				],
			)
		);
		$repeater->add_control(
			'plus_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'plus_animation_effect',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => [
					'pulse'  => esc_html__( 'Pulse', 'theplus' ),
					'floating'  => esc_html__( 'Floating', 'theplus' ),
					'tossing'  => esc_html__( 'Tossing', 'theplus' ),
					'rotating'  => esc_html__( 'Rotating', 'theplus' ),
				],
				'condition' => [
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_animation_hover',
			[
				'label'        => esc_html__( 'Hover Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'condition' => [
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_animation_duration',
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
					'size' => 1.2,
				],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_transform_origin',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
				],
				'render_type'  => 'template',
				'condition' => [
					'plus_continuous_animation' => 'yes',
					'plus_animation_effect' => 'rotating',
				],
			]
		);
		$this->add_control(
            'pt_plus_social_networks',
            [
				'label' => esc_html__( 'Social Network Select', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'pt_plus_social_icons' => '',                       
                    ],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ pt_plus_social_icons }}}',
            ]
        );
		
		$this->add_responsive_control(
			'social_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'text-left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'text-center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'text-right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'text-center',
			]
		);
		$this->add_control(
			'social_icon_verical',
			[
				'label' => esc_html__( 'Vertical Layout', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'styles!' => 'custom',
				],
			]
		);
		$this->add_responsive_control(
			'vl_max_width',
			[
				'label'      => esc_html__( 'Max. Width', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],				
				'selectors'  => [					
					'{{WRAPPER}} .pt_plus_social_list.pt_plus_sl_vertical.style-14 ul.social_list' => 'max-width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_social_list.pt_plus_sl_vertical.style-15 ul.social_list li' => 'min-width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'styles' => ['style-14','style-15'],
					'social_icon_verical' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_social_styling',
            [
                'label' => esc_html__('Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'social_icon_gap_margin',
			[
				'label' => esc_html__( 'Icons Between Gap', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_social_list ul.social_list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'social_icon_gap_padding',
			[
				'label' => esc_html__( 'Icons Gap', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_social_list ul.social_list li a i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'height_social',
			[
				'label'      => esc_html__( 'Height', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [					
					'{{WRAPPER}} .pt_plus_social_list.style-15 ul.social_list li a' => 'height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'styles' => ['style-15'],
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Font Size', 'theplus'),
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min'	=> 8,
						'max'	=> 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_social_list ul.social_list .style-1 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-2 a i.fa,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-3 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-4 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-5 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-6 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-7 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-8 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-9 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-10 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-11 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-12 a .fa,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-13 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-14 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-15 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .custom a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				
            ]
		);
		$this->add_responsive_control(
			'social_icon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_social_list ul.social_list .style-1 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-2 a,					
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-4 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-5 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-6 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-7 a,
					{{WRAPPER}} .pt_plus_social_list ul.social_list .style-10 a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'styles' => ['style-1','style-2','style-4','style-5','style-6','style-7','style-10'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .pt_plus_social_list ul.social_list .style-1 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-2 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-4 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-10 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-12 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-14 a span,
				{{WRAPPER}} .pt_plus_social_list ul.social_list .style-15 a span',
				'condition' => [
					'styles' => ['style-1','style-2','style-4','style-10','style-12','style-14','style-15'],
				],
			]
		);
		$this->add_responsive_control(
            'social_icon_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'styles' => 'custom',
				],
				'separator' => 'before',
            ]
        );
		$this->add_responsive_control(
            'social_icon_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'styles' => 'custom',
				],
            ]
        );
		$this->add_control(
			'social_icon_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->add_control(
			'social_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'styles' => 'custom',
					'social_icon_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'social_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'styles' => 'custom',
					'social_icon_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->add_responsive_control(
			'social_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->add_responsive_control(
			'social_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li:hover a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'social_icon_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'social_icon_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li a',
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'social_icon_box_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_social_list.custom ul.social_list li:hover a',
				'condition' => [
					'styles' => 'custom',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	 protected function render() {
		$settings = $this->get_settings_for_display();	
			$styles=$settings['styles'];
			$hover_animation=$settings["hover_animation"];
			$social_align=$settings["social_align"];
			$social_align .= !empty($settings["social_align_tablet"]) ? ' tsocial' . $settings["social_align_tablet"] : '';
			$social_align .= !empty($settings["social_align_mobile"]) ? ' msocial' . $settings["social_align_mobile"] : '';
			
			$social_icon_verical = isset($settings['social_icon_verical']) ? $settings['social_icon_verical'] : '';

			$si_v_class='';
			if($social_icon_verical==='yes'){
				$si_v_class=' pt_plus_sl_vertical';
			}

			$social_animation =$social_chaffle =$hover_style=$social_text=$css_loop=$link=$link_atts_title=$link_atts_url=$link_atts_target='';
			
			if($styles=='style-14' || $styles=='style-15'){
				if($hover_animation == 'hover-1'){
					$social_animation ='social-faded';
				}else if($hover_animation == 'hover-2'){
					$social_animation = 'socail-chaffal';
					$social_chaffle = 'ts-chaffle';
				}
			}
			
			/*--On Scroll View Animation ---*/
				include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
			
			$social='<div class="pt_plus_social_list '.esc_attr($si_v_class).' '.esc_attr($social_align).' '.esc_attr($styles).' '.esc_attr($animated_class).'" '.$animation_attr.'>';
				$social .='<ul class="social_list '.esc_attr($social_animation).'">';
			if(!empty($settings['pt_plus_social_networks'])){			
				foreach($settings['pt_plus_social_networks'] as $network) {
				
				$id=rand(1000,10000000);
					if(!empty($network['pt_plus_social_icons']) && !empty($network['social_url']['url'])) {						
					
						if((!empty($network['pt_plus_social_icons']) && $network['pt_plus_social_icons']=='custom') && !empty($network['pt_plus_social_icon_custom'])){
							$icon = $network['pt_plus_social_icon_custom'];
						}else if(!empty($network['pt_plus_social_icons'])) {
							$icon = $network['pt_plus_social_icons'];
						}
						if ( ! empty( $network['social_url']['url'] ) ) {
							$link_atts_url = 'href="'.$network["social_url"]["url"].'"';
						}
						if ( ! empty( $network['social_url']['is_external'] ) ) {
						$link_atts_target = 'target="_blank"';
						}
						if ( ! empty($network['social_url']['nofollow']) ) {
							$link_atts_title = 'rel="nofollow"';
						}
						
						if(!empty($network['social_text']) && ($styles=='style-1' || $styles=='style-2' || $styles=='style-4' || $styles=='style-10' || $styles=='style-12' || $styles=='style-14' || $styles=='style-15' || $styles=='custom')){
							$social_text='<span class="'.esc_attr($social_chaffle).'" data-lang="en">'.$network['social_text'].'</span>';
						}
						
						
						$icon_html = '<i class="fa fab '.esc_attr($icon).'"></i>';
						
						
						if($styles=='style-6'){							
							$hover_style='<i class="social-hover-style"></i>';
						}
						if($styles=='style-9'){
							$hover_style = '<span class="line-top-left style-'.esc_attr($icon).'"></span><span class="line-top-center style-'.esc_attr($icon).'"></span><span class="line-top-right style-'.esc_attr($icon).'"></span><span class="line-bottom-left style-'.esc_attr($icon).'"></span><span class="line-bottom-center style-'.esc_attr($icon).'"></span><span class="line-bottom-right style-'.esc_attr($icon).'"></span>';
						}
						$border_hover_color=$icon_color=$icon_hover_color=$bg_color=$bg_hover_color=$border_color='';
						if(!empty($network['icon_color'])){
							$icon_color= $network['icon_color'];
						}
						if(!empty($network['icon_hover_color'])){
							$icon_hover_color= $network['icon_hover_color'];
						}
						if(!empty($network['bg_color'])){
							$bg_color= $network['bg_color'];
						}
						if(!empty($network['bg_hover_color'])){
							$bg_hover_color= $network['bg_hover_color'];
						}
						if(!empty($network['border_color'])){
							$border_color= $network['border_color'];
						}
						if(!empty($network['border_hover_color'])){
							$border_hover_color= $network['border_hover_color'];
						}
						
						$magic_class = $magic_attr = $parallax_scroll = '';
						if (!empty($network['loop_magic_scroll']) && $network['loop_magic_scroll'] == 'yes') {
							if($network["loop_scroll_option_popover_toggle"]==''){
								$scroll_offset=0;
								$scroll_duration=300;
							}else{
								$scroll_offset=$network['loop_scroll_option_scroll_offset'];
								$scroll_duration=$network['loop_scroll_option_scroll_duration'];
							}
							if($network["loop_scroll_from_popover_toggle"]==''){
								$scroll_x_from=0;
								$scroll_y_from=0;
								$scroll_opacity_from=1;
								$scroll_scale_from=1;
								$scroll_rotate_from=0;
							}else{
								$scroll_x_from=$network['loop_scroll_from_scroll_x_from'];
								$scroll_y_from=$network['loop_scroll_from_scroll_y_from'];
								$scroll_opacity_from=$network['loop_scroll_from_scroll_opacity_from'];
								$scroll_scale_from=$network['loop_scroll_from_scroll_scale_from'];
								$scroll_rotate_from=$network['loop_scroll_from_scroll_rotate_from'];
							}
							if($network["loop_scroll_to_popover_toggle"]==''){
								$scroll_x_to=0;
								$scroll_y_to=-50;
								$scroll_opacity_to=1;
								$scroll_scale_to=1;
								$scroll_rotate_to=0;
							}else{
								$scroll_x_to=$network['loop_scroll_to_scroll_x_to'];
								$scroll_y_to=$network['loop_scroll_to_scroll_y_to'];
								$scroll_opacity_to=$network['loop_scroll_to_scroll_opacity_to'];
								$scroll_scale_to=$network['loop_scroll_to_scroll_scale_to'];
								$scroll_rotate_to=$network['loop_scroll_to_scroll_rotate_to'];
							}
							$magic_attr .= ' data-scroll_type="position" ';
							$magic_attr .= ' data-scroll_offset="' . esc_attr($scroll_offset) . '" ';
							$magic_attr .= ' data-scroll_duration="' . esc_attr($scroll_duration) . '" ';
							
							$magic_attr .= ' data-scroll_x_from="' . esc_attr($scroll_x_from) . '" ';
							$magic_attr .= ' data-scroll_x_to="' . esc_attr($scroll_x_to) . '" ';
							$magic_attr .= ' data-scroll_y_from="' . esc_attr($scroll_y_from) . '" ';
							$magic_attr .= ' data-scroll_y_to="' . esc_attr($scroll_y_to) . '" ';
							$magic_attr .= ' data-scroll_opacity_from="' . esc_attr($scroll_opacity_from) . '" ';
							$magic_attr .= ' data-scroll_opacity_to="' . esc_attr($scroll_opacity_to) . '" ';
							$magic_attr .= ' data-scroll_scale_from="' . esc_attr($scroll_scale_from) . '" ';
							$magic_attr .= ' data-scroll_scale_to="' . esc_attr($scroll_scale_to) . '" ';
							$magic_attr .= ' data-scroll_rotate_from="' . esc_attr($scroll_rotate_from) . '" ';
							$magic_attr .= ' data-scroll_rotate_to="' . esc_attr($scroll_rotate_to) . '" ';
							
							$parallax_scroll .= ' parallax-scroll ';							
							$magic_class .= ' magic-scroll ';
						}
						
						if( $network['plus_tooltip'] == 'yes' ) {
							$this->add_render_attribute( '_tooltip', 'data-tippy', '', true );

							if (!empty($network['plus_tooltip_content_type']) && $network['plus_tooltip_content_type']=='normal_desc') {
								$this->add_render_attribute( '_tooltip', 'title', $network['plus_tooltip_content_desc'], true );
							}else if (!empty($network['plus_tooltip_content_type']) && $network['plus_tooltip_content_type']=='content_wysiwyg') {
								$tooltip_content=$network['plus_tooltip_content_wysiwyg'];
								$this->add_render_attribute( '_tooltip', 'title', $tooltip_content, true );
							}
							$plus_tooltip_position=(!empty($network["tooltip_opt_plus_tooltip_position"])) ? $network["tooltip_opt_plus_tooltip_position"] : 'top';
							$this->add_render_attribute( '_tooltip', 'data-tippy-placement', $plus_tooltip_position, true );
							
							$tooltip_interactive =($network["tooltip_opt_plus_tooltip_interactive"]=='' || $network["tooltip_opt_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
							$this->add_render_attribute( '_tooltip', 'data-tippy-interactive', $tooltip_interactive, true );
							
							$plus_tooltip_theme=(!empty($network["tooltip_opt_plus_tooltip_theme"])) ? $network["tooltip_opt_plus_tooltip_theme"] : 'dark';
							$this->add_render_attribute( '_tooltip', 'data-tippy-theme', $plus_tooltip_theme, true );
							
							
							$tooltip_arrow =($network["tooltip_opt_plus_tooltip_arrow"]!='none' || $network["tooltip_opt_plus_tooltip_arrow"]=='') ? 'true' : 'false';
							$this->add_render_attribute( '_tooltip', 'data-tippy-arrow', $tooltip_arrow , true );
							
							$plus_tooltip_arrow=(!empty($network["tooltip_opt_plus_tooltip_arrow"])) ? $network["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
							$this->add_render_attribute( '_tooltip', 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
							
							$plus_tooltip_animation=(!empty($network["tooltip_opt_plus_tooltip_animation"])) ? $network["tooltip_opt_plus_tooltip_animation"] : 'shift-toward';
							$this->add_render_attribute( '_tooltip', 'data-tippy-animation', $plus_tooltip_animation, true );
							
							$plus_tooltip_x_offset=(isset($network["tooltip_opt_plus_tooltip_x_offset"])) ? $network["tooltip_opt_plus_tooltip_x_offset"] : 0;
							$plus_tooltip_y_offset=(isset($network["tooltip_opt_plus_tooltip_y_offset"])) ? $network["tooltip_opt_plus_tooltip_y_offset"] : 0;
							$this->add_render_attribute( '_tooltip', 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
							
							$tooltip_duration_in =(isset($network["tooltip_opt_plus_tooltip_duration_in"])) ? $network["tooltip_opt_plus_tooltip_duration_in"] : 250;
							$tooltip_duration_out =(isset($network["tooltip_opt_plus_tooltip_duration_out"])) ? $network["tooltip_opt_plus_tooltip_duration_out"] : 200;
							$tooltip_trigger =(!empty($network["tooltip_opt_plus_tooltip_triggger"])) ? $network["tooltip_opt_plus_tooltip_triggger"] : 'mouseenter';
							$tooltip_arrowtype =(!empty($network["tooltip_opt_plus_tooltip_arrow"])) ? $network["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
						}
						
						$move_parallax=$move_parallax_attr=$parallax_move='';
						if(!empty($network['plus_mouse_move_parallax']) && $network['plus_mouse_move_parallax']=='yes'){
							$move_parallax='pt-plus-move-parallax';
							$parallax_move='parallax-move';
							$parallax_speed_x=(isset($network["plus_mouse_parallax_speed_x"]["size"])) ? esc_attr($network["plus_mouse_parallax_speed_x"]["size"]) : 30;
							$parallax_speed_y=(isset($network["plus_mouse_parallax_speed_y"]["size"])) ? esc_attr($network["plus_mouse_parallax_speed_y"]["size"]) : 30;
							
							$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($parallax_speed_x) . '" ';
							$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($parallax_speed_y) . '" ';
						}
						
						$continuous_animation='';
						if(!empty($network["plus_continuous_animation"]) && $network["plus_continuous_animation"]=='yes'){
							if($network["plus_animation_hover"]=='yes'){
								$animation_class='hover_';
							}else{
								$animation_class='image-';
							}
							$continuous_animation=$animation_class.$network["plus_animation_effect"];
						}
							$uid_social=uniqid("social").$network['_id'];
							$social .= '<li id="'.esc_attr($uid_social).'" class="elementor-repeater-item-' . $network['_id'] . ' '.esc_attr($styles).'  social-'.esc_attr($icon).' social-'.esc_attr($id).' ' . esc_attr($magic_class) . ' '.esc_attr($move_parallax).' '.esc_attr($continuous_animation).'" '.$this->get_render_attribute_string( '_tooltip' ).'>';
								$social .= '<div class="social-loop-inner ' . esc_attr($parallax_scroll) . ' '.esc_attr($parallax_move).'" ' . $magic_attr . ' '.$move_parallax_attr.'>';
									$social .= '<a '.$link_atts_url.' '.$link_atts_title.' '.$link_atts_target.'>'.$icon_html.$social_text.$hover_style.'</a>';
								$social .= '</div>';
							$social .= '</li>';
							
							$inline_tippy_js='';
							if($network['plus_tooltip'] == 'yes'){
								$inline_tippy_js ='jQuery( document ).ready(function() {
								"use strict";
									if(typeof tippy === "function"){
										tippy( "#'.esc_attr($uid_social).'" , {
											arrowType : "'.esc_attr($tooltip_arrowtype).'",
											duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
											trigger : "'.esc_attr($tooltip_trigger).'",
											appendTo: document.querySelector("#'.esc_attr($uid_social).'")
										});
									}
								});';
								$social .= wp_print_inline_script_tag($inline_tippy_js);
							}
					}
				}
			}
			$social .='</ul>';
		$social .='</div>';
		$css_rule='';
		if(!empty($css_loop)){
			$css_rule .= '<style >';
				$css_rule .=$css_loop;
			$css_rule .= '</style>';
		}
	echo $css_rule.$social;
	}
    protected function content_template() {
	
    }

}
