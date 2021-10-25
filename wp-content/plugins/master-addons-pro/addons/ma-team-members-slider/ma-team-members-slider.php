<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Utils;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Image_Size;

use MasterAddons\Inc\Controls\MA_Group_Control_Transition;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Team_Slider extends Widget_Base
{

	public function get_name()
	{
		return 'ma-team-members-slider';
	}

	public function get_title()
	{
		return esc_html__('Team Slider', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-person';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return [
			'team',
			'members',
			'carousel',
			'slider',
			'team members',
			'team scroll',
			'team members slider',
			'person slider'
		];
	}

	public function get_script_depends()
	{
		return [
			'swiper',
			'gridder',
			'master-addons-scripts'
		];
	}

	public function get_style_depends()
	{
		return [
			'gridder',
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/team-carousel/';
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'section_team_carousel',
			[
				'label' => esc_html__('Contents', MELA_TD),
			]
		);


		// Premium Version Codes
		
			$this->add_control(
				'ma_el_team_carousel_preset',
				[
					'label' => esc_html__('Style Preset', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'default' => '-default',
					'options' => [
						'-default'              => __('Team Carousel', MELA_TD),
						'-circle'               => __('Circle Gradient', MELA_TD),
						'-circle-animation'     => __('Circle Animation', MELA_TD),
						'-social-left'          => __('Social Left on Hover', MELA_TD),
						'-content-hover'        => __('Content on Hover', MELA_TD),
						'-content-drawer'       => __('Content Drawer', MELA_TD),
					],
					'frontend_available' 	=> true,
				]
			);
		




		
			$this->add_control(
				'ma_el_team_circle_image',
				[
					'label' => esc_html__('Circle Gradient Image', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'default' => 'circle_01',
					'options' => [
						'circle_01'                   => esc_html__('Circle 01', MELA_TD),
						'circle_02'                   => esc_html__('Circle 02', MELA_TD),
						'circle_03'                   => esc_html__('Circle 03', MELA_TD),
						'circle_04'                   => esc_html__('Circle 04', MELA_TD),
						'circle_05'                   => esc_html__('Circle 05', MELA_TD),
						'circle_06'                   => esc_html__('Circle 06', MELA_TD),
						'circle_07'                   => esc_html__('Circle 07', MELA_TD),
					],
					'condition' => [
						'ma_el_team_carousel_preset' => '-circle'
					]
				]
			);
		


		// Premium Version Codes
		
			$this->add_control(
				'ma_el_team_circle_image_animation',
				[
					'label'       => esc_html__('Animation Style', MELA_TD),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'animation_svg_01',
					'options'     => [
						'animation_svg_01' 		=> esc_html__('Animation 1', MELA_TD),
						'animation_svg_02' 		=> esc_html__('Animation 2', MELA_TD),
						'animation_svg_03' 		=> esc_html__('Animation 3', MELA_TD),
						'svg_animated_pro_1' 	=> esc_html__('Animation 4 (Pro)', MELA_TD),
						'svg_animated_pro_2' 	=> esc_html__('Animation 5 (Pro)', MELA_TD),
						'svg_animated_pro_3' 	=> esc_html__('Animation 6 (Pro)', MELA_TD),
						'svg_animated_pro_4' 	=> esc_html__('Animation 7 (Pro)', MELA_TD),
					],
					'condition'   => [
						'ma_el_team_carousel_preset' => '-circle-animation'
					],
					'description' => sprintf(
						'5+ More Animated Variations Available on Pro Version <a href="%s" target="_blank">%s</a>',
						esc_url_raw(admin_url('admin.php?page=master-addons-settings-pricing')),
						__('Upgrade Now', MELA_TD)
					)
				]
			);
		



		$team_repeater = new Repeater();

		/*
			* Team Member Image
			*/
		$team_repeater->add_control(
			'ma_el_team_carousel_image',
			[
				'label' => __('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					//						'{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb .animation_svg_02:after' => 'background-image: url("{{URL}}");'
				]

			]
		);
		$team_repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'condition' => [
					'ma_el_team_carousel_image[url]!' => '',
				]
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_name',
			[
				'label' => esc_html__('Name', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('John Doe', MELA_TD),
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_designation',
			[
				'label' => esc_html__('Designation', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('My Designation', MELA_TD),
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_description',
			[
				'label' => esc_html__('Description', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Add team member details here', MELA_TD),
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_enable_social_profiles',
			[
				'label' => esc_html__('Display Social Profiles?', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_facebook_link',
			[
				'label' => __('Facebook URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'condition' => [
					'ma_el_team_carousel_enable_social_profiles!' => '',
				],
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_twitter_link',
			[
				'label' => __('Twitter URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'condition' => [
					'ma_el_team_carousel_enable_social_profiles!' => '',
				],
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_instagram_link',
			[
				'label' => __('Instagram URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'condition' => [
					'ma_el_team_carousel_enable_social_profiles!' => '',
				],
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_linkedin_link',
			[
				'label' => __('Linkedin URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'condition' => [
					'ma_el_team_carousel_enable_social_profiles!' => '',
				],
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$team_repeater->add_control(
			'ma_el_team_carousel_dribbble_link',
			[
				'label' => __('Dribbble URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'condition' => [
					'ma_el_team_carousel_enable_social_profiles!' => '',
				],
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);


		$this->add_control(
			'team_carousel_repeater',
			[
				'label' => esc_html__('Team Carousel', MELA_TD),
				'type' => Controls_Manager::REPEATER,
				'fields' => $team_repeater->get_controls(),
				'title_field' => '{{{ ma_el_team_carousel_name }}}',
				'default' => [
					[
						'ma_el_team_carousel_name' => __('Member #1', MELA_TD),
						'ma_el_team_carousel_description' => __('Add team member details here', MELA_TD),
					],
					[
						'ma_el_team_carousel_name' => __('Member #2', MELA_TD),
						'ma_el_team_carousel_description' => __('Add team member details here', MELA_TD),
					],
					[
						'ma_el_team_carousel_name' => __('Member #3', MELA_TD),
						'ma_el_team_carousel_description' => __('Add team member details here', MELA_TD),
					],
					[
						'ma_el_team_carousel_name' => __('Member #4', MELA_TD),
						'ma_el_team_carousel_description' => __('Add team member details here', MELA_TD),
					],
				]
			]
		);


		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('Title HTML Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h3',
			]
		);


		$this->end_controls_section();

		/*
			* Team Members Styling Section
			*/
		$this->start_controls_section(
			'ma_el_section_team_carousel_styles_preset',
			[
				'label' => esc_html__('General Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ma_el_team_image_bg_size',
			[
				'label' => __('Background Image Size', MELA_TD),
				'description' => __('Height Width will be same ratio', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 5,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 125,
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb svg,
						{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb svg,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb .animation_svg_02,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb .animation_svg_03,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb' =>
					'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};'

				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_image_bg_position_left',
			[
				'label' => __('Background Position(Left)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'default' => [
					'unit' => 'px',
					'size' => -5,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 150,
						'step' => 1,
					]
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb svg,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb svg' => 'left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_image_bg_position_top',
			[
				'label' => __('Background Position(Top)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
						'step' => 1,
					]
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb svg,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb svg' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_team_image_size',
			[
				'label' => __('Member Image Size', MELA_TD),
				'description' => __('Height Width will be same ratio', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'size_units'    => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					//						'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb' =>
					//                            'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb img,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb .animation_svg_03_center,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',

					//						'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',

				]
			]
		);


		$this->add_responsive_control(
			'ma_el_team_image_position_left',
			[
				'label' => __('Member Image Position(Left)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'default' => [
					'unit' => '%',
					'size' => 45,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 150,
						'step' => 1,
					]
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb img,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb img' => 'left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_image_position_top',
			[
				'label' => __('Member Image Position(Top)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'default' => [
					'unit' => '%',
					'size' => 45,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 150,
						'step' => 1,
					]
				],
				'condition' => [
					'ma_el_team_carousel_preset' => ['-circle', '-circle-animation']
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb img,
						{{WRAPPER}} .ma-el-team-member-circle-animation .ma-el-team-member-thumb img' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_avatar_bg',
			[
				'label' => esc_html__('Avatar Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#826EFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-circle .ma-el-team-member-thumb svg.team-avatar-bg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'ma_el_team_carousel_preset' => '-circle',
				],
			]
		);

		$this->add_control(
			'ma_el_team_carousel_bg',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-basic,
						{{WRAPPER}} .ma-el-team-member-circle,
						{{WRAPPER}} .ma-el-team-member-social-left,
						{{WRAPPER}} .ma-el-team-members-slider-section,
						{{WRAPPER}} .ma-el-team-member-rounded' => 'background: {{VALUE}};',
					'{{WRAPPER}} .gridder .gridder-show' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} #animation_svg_04 circle' => 'fill: {{VALUE}}'
				],
			]
		);


		$this->add_control(
			'ma_el_team_carousel_content_align',
			[
				'label'         => __('Content Alignment', MELA_TD),
				'type'          => Controls_Manager::CHOOSE,
				'options'       => [
					'left'      => [
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default'       => 'left',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-team-member-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();



		/*
		Style Tab: Carousel Settings
		*/

		$this->start_controls_section(
			'ma_el_team_carousel_style_section',
			[
				'label'         => __('Carousel', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_team_carousel_preset!'         => '-content-drawer'
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_arrows_style_heading',
			[
				'label' 	=> __('Arrows', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'     => [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> __('Left', MELA_TD),
					'center' 	=> __('Center', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_team_carousel_arrows_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 12,
						'max' => 48,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'font-size: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_arrows_padding',
			[
				'label' 		=> __('Padding', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'padding: {{SIZE}}em;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);


		$this->add_responsive_control(
			'arrows_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--middle.jltma-arrows--horizontal .jltma-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--middle).jltma-arrows--horizontal .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--prev' => 'left: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--next' => 'right: -{{SIZE}}px;',

					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--center.jltma-arrows--vertical .jltma-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--center).jltma-arrows--vertical .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--prev' => 'top: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--next' => 'bottom: -{{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 100,
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'border-radius: {{SIZE}}%;',
				],
				'separator'		=> 'after',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-swiper__button',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);


		$this->start_controls_tabs('ma_el_team_carousel_arrow_style_tabs');

		// Normal Tab
		$this->start_controls_tab(
			'ma_el_team_carousel_arrow_style_tab',
			[
				'label'         => __('Normal', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'ma_el_team_arrow_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'ma_el_team_arrow_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();



		// Hover Tab
		$this->start_controls_tab(
			'ma_el_team_carousel_arrow_hover_style_tab',
			[
				'label'         => __('Hover', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'ma_el_team_arrow_hover_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'ma_el_team_arrow_hover_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'ma_el_team_carousel_pagination_style_heading',
			[
				'separator'	=> 'before',
				'label' 	=> __('Pagination', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'carousel_pagination' => 'yes',
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_align',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'center',
				'options' 		=> [
					'left'    		=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'fa fa-align-center',
					],
					'right' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'fa fa-align-right',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--horizontal' => 'text-align: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_align_vertical',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'middle',
				'options' 		=> [
					'flex-start'    => [
						'title' 	=> __('Top', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'flex-end' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--vertical' => 'justify-content: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--horizontal' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--horizontal' => 'padding: {{SIZE}}px 0 0 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--vertical' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--vertical' => 'padding: 0 0 0 {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_spacing',
			[
				'label' 		=> __('Spacing', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--horizontal .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}px',
					'{{WRAPPER}} .jltma-swiper__pagination--vertical .swiper-pagination-bullet' => 'margin: {{SIZE}}px 0',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'pagination_bullets_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'ma_el_team_carousel_pagination_bullet',
				'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
				'condition'		=> [
					'carousel_pagination' => 'yes'
				]
			]
		);


		$this->start_controls_tabs('ma_el_team_carousel_pagination_bullets_tabs_hover');

		$this->start_controls_tab('ma_el_team_carousel_pagination_bullets_tab_default', [
			'label' 		=> __('Default', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 12,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_pagination_bullets_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_opacity',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'on',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_team_carousel_pagination_bullets_tab_hover', [
			'label' 		=> __('Hover', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_size_hover',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_pagination_bullets_color_hover',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_opacity_hover',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_team_carousel_pagination_bullets_tab_active', [
			'label' => __('Active', MELA_TD),
			'condition'	=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_size_active',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_team_carousel_pagination_bullets_color_active',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_team_carousel_pagination_bullets_opacity_active',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();





		/*
		Style Tab: Name
		*/
		$this->start_controls_section(
			'section_team_carousel_name',
			[
				'label' => __('Name', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_title_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ma-el-team-member-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_team_member_designation',
			[
				'label' => __('Designation', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_designation_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#8a8d91',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'designation_typography',
				'selector' => '{{WRAPPER}} .ma-el-team-member-designation',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_team_carousel_description',
			[
				'label' => __('Description', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_description_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#8a8d91',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-about,
						{{WRAPPER}} .gridder-expanded-content p.ma-el-team-member-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_description_typography',
				'selector' => '{{WRAPPER}} .ma-el-team-member-about',
			]
		);

		$this->end_controls_section();


		/* Carousel Settings */
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => esc_html__('Carousel Settings', MELA_TD),
			]
		);

		$this->add_control(
			'autoheight',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Auto Height', MELA_TD),
				'default' 		=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->add_control(
			'carousel_height',
			[
				'label' 		=> __('Custom Height', MELA_TD),
				'description'	=> __('The carousel needs to have a fixed defined height to work in vertical mode.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> [
					'px', '%', 'vh'
				],
				'default' => [
					'size' => 500,
					'unit' => 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 200,
						'max' => 2000,
					],
					'%' 		=> [
						'min' => 0,
						'max' => 100,
					],
					'vh' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__container' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'		=> [
					'autoheight!' => 'yes'
				],
			]
		);

		$this->add_control(
			'slide_effect',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Effect', MELA_TD),
				'default' 		=> 'slide',
				'options' 		=> [
					'slide' 	=> __('Slide', MELA_TD),
					'fade' 		=> __('Fade', MELA_TD),
				],
				'frontend_available' => true
			]
		);

		$this->add_control(
			'slide_effect_fade_warning',
			[
				'type' 				=> Controls_Manager::RAW_HTML,
				'raw' 				=> __('The Fade effect ignores the Slides per View and Slides per Column settings', MELA_TD),
				'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				'condition' 		=> [
					'slide_effect' => 'fade'
				],
			]
		);


		$this->add_control(
			'duration_speed',
			[
				'label' 	=> __('Duration (ms)', MELA_TD),
				'description' => __('Duration of the effect transition.', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 300,
					'unit' 	=> 'px',
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 2000,
						'step'	=> 100,
					],
				],
				'frontend_available' => true
			]
		);



		$this->add_control(
			'resistance_ratio',
			[
				'label' 		=> __('Resistance', MELA_TD),
				'description'	=> __('Set the value for resistant bounds.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'size' 		=> 0.25,
					'unit' 		=> 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.05,
					],
				],
				'frontend_available' => true
			]
		);


		$this->add_control(
			'ma_el_team_carousel_layout_heading',
			[
				'label' 			=> __('Layout', MELA_TD),
				'type' 				=> Controls_Manager::HEADING,
				'separator'			=> 'before'
			]
		);

		$this->add_responsive_control(
			'carousel_direction',
			[
				'type' 				=> Controls_Manager::SELECT,
				'label' 			=> __('Orientation', MELA_TD),
				'default'			=> 'horizontal',
				'tablet_default'	=> 'horizontal',
				'mobile_default'	=> 'horizontal',
				'options' 			=> [
					'horizontal' 	=> __('Horizontal', MELA_TD),
					'vertical' 		=> __('Vertical', MELA_TD),
				],
				'frontend_available' 	=> true
			]
		);




		$slides_per_view = range(1, 6);
		$slides_per_view = array_combine($slides_per_view, $slides_per_view);

		$this->add_responsive_control(
			'ma_el_team_per_view',
			[
				'type'           		=> Controls_Manager::SELECT,
				'label'          		=> esc_html__('Slides Per View', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'        		=> '4',
				'tablet_default' 		=> '3',
				'mobile_default' 		=> '2',
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides Per Column', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'frontend_available' 	=> true,
				'condition' 		=> [
					'ma_el_team_carousel_direction' => 'horizontal',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_team_slides_to_scroll',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__('Slides to Scroll', MELA_TD),
				'options' 	=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'   => '1',
				'frontend_available' 	=> true,
			]
		);


		$this->add_responsive_control(
			'columns_spacing',
			[
				'label' 			=> __('Columns Spacing', MELA_TD),
				'type' 				=> Controls_Manager::SLIDER,
				'default'			=> [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default'	=> [
					'size' => 12,
					'unit' => 'px',
				],
				'mobile_default'	=> [
					'size' => 0,
					'unit' => 'px',
				],
				'size_units' 		=> ['px'],
				'range' 			=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'condition'				=> [
					'carousel_direction' => 'horizontal',
				],
			]
		);


		$this->add_control(
			'ma_el_team_autoplay',
			[
				'label'     	=> esc_html__('Autoplay', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'   	=> 'yes',
				'separator'   	=> 'before',
				'return_value' 	=> 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->start_popover();

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', MELA_TD),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'ma_el_team_autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' 		=> __('Disable on Interaction', MELA_TD),
				'description' 	=> __('Removes autoplay completely on the first interaction with the carousel.', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
				'condition' 	=> [
					'ma_el_team_autoplay'           => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'ma_el_team_pause',
			[
				'label'     => esc_html__('Pause on Hover', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'ma_el_team_autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->end_popover();




		$this->add_control(
			'free_mode',
			[
				'type' 					=> Controls_Manager::POPOVER_TOGGLE,
				'label' 				=> __('Free Mode', MELA_TD),
				'description'			=> __('Disable fixed positions for slides.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->start_popover();

		$this->add_control(
			'free_mode_sticky',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> __('Snap to position', MELA_TD),
				'description'			=> __('Enable to snap slides to positions in free mode.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'condition' 			=> [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Momentum', MELA_TD),
				'description'	=> __('Enable to keep slide moving for a while after you release it.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'separator'		=> 'before',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_ratio',
			[
				'label' 		=> __('Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum distance after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_velocity',
			[
				'label' 		=> __('Velocity', MELA_TD),
				'description'	=> __('Higher value produces larger momentum velocity after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Bounce', MELA_TD),
				'description'	=> __('Set to No if you want to disable momentum bounce in free mode.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce_ratio',
			[
				'label' 		=> __('Bounce Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum bounce effect.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' => '',
					'free_mode_momentum!' => '',
					'free_mode_momentum_bounce!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->end_popover();



		$this->add_control(
			'carousel_arrows',
			[
				'label'         => __('Arrows', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'       => 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrows_placement',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Placement', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 	=> __('Inside', MELA_TD),
					'outside' 	=> __('Outside', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> __('Left', MELA_TD),
					'center' 	=> __('Center', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->end_popover();



		$this->add_control(
			'ma_el_team_loop',
			[
				'label'   => esc_html__('Infinite Loop', MELA_TD),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'slide_change_resize',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Trigger Resize on Slide', MELA_TD),
				'description'	=> __('Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', MELA_TD),
				'default' 		=> '',
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'carousel_pagination',
			[
				'label' 		=> __('Pagination', MELA_TD),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'pagination_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 		=> __('Inside', MELA_TD),
					'outside' 		=> __('Outside', MELA_TD),
				],
				'frontend_available' 	=> true,
				'condition'		=> [
					'carousel_pagination'         => 'yes',
				]
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Type', MELA_TD),
				'default'		=> 'bullets',
				'options' 		=> [
					'bullets' 		=> __('Bullets', MELA_TD),
					'fraction' 		=> __('Fraction', MELA_TD),
				],
				'condition'		=> [
					'carousel_pagination'         => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_pagination_clickable',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Clickable', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'condition' => [
					'carousel_pagination'         => 'yes',
					'pagination_type'       		=> 'bullets'
				],
				'frontend_available' 	=> true,
			]
		);
		$this->end_popover();


		$this->end_controls_section();



		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/team-carousel/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/team-members-carousel/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=ubP_h86bP-c" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_team_carousel_social_section',
			[
				'label' => __('Social', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_team_carousel_preset' => ['-social-left', '-default'],
				],
			]
		);

		$this->start_controls_tabs('ma_el_team_carousel_social_icons_style_tabs');

		$this->start_controls_tab(
			'ma_el_team_carousel_social_icon_control',
			['label' => esc_html__('Normal', MELA_TD)]
		);

		$this->add_control(
			'ma_el_team_carousel_social_icon_color',
			[
				'label' => esc_html__('Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-social li a' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_team_carousel_social_color_1',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-social-left .ma-el-team-member-social li a' => 'background: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_team_carousel_social_icon_hover_control',
			['label' => esc_html__('Hover', MELA_TD)]
		);

		$this->add_control(
			'ma_el_team_carousel_social_icon_hover_color',
			[
				'label' => esc_html__('Icon Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-social li a:hover' => 'color: {{VALUE}};'
				],
			]
		);


		$this->add_control(
			'ma_el_team_carousel_social_hover_bg_color_1',
			[
				'label' => esc_html__('Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff6d55',
				'selectors' => [
					'{{WRAPPER}} .ma-el-team-member-social-left .ma-el-team-member-social li a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$team_carousel_classes = $this->get_settings_for_display('ma_el_team_carousel_image_rounded');

		$team_preset = $settings['ma_el_team_carousel_preset'];

		$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

		$this->add_render_attribute([
			'ma_el_team_carousel' => [
				'class' => [
					'ma-el-team-members-slider-section',
					'ma-el-team-carousel-wrapper',
					'ma-el-team-carousel' . $team_preset,
					'jltma-swiper',
					'jltma-swiper__container',
					'swiper-container',
					'elementor-jltma-element-' . $unique_id
				],
				'data-jltma-template-widget-id' => $unique_id
			],
			'swiper-wrapper' => [
				'class' => [
					'jltma-blog-carousel',
					'jltma-swiper__wrapper',
					'swiper-wrapper',
				],
			],

			'swiper-item' => [
				'class' => [
					'jltma-slider__item',
					'jltma-swiper__slide',
					'swiper-slide',
					'ma-el-team-carousel' . $team_preset . '-inner'
				],
			],
		]);


		$this->add_render_attribute(
			'ma_el_team_slider_section',
			[
				'class' => 'ma-el-team-members-slider-section',
				'data-team-preset' => $team_preset,
			]
		);
?>



		<?php if ($team_preset == '-content-drawer') { ?>

			<div <?php echo $this->get_render_attribute_string('ma_el_team_slider_section'); ?>>
				<!-- Gridder navigation -->
				<ul class="gridder">

					<?php foreach ($settings['team_carousel_repeater'] as $key => $member) {

						$team_carousel_image = $member['ma_el_team_carousel_image'];
						$team_carousel_image_url = Group_Control_Image_Size::get_attachment_image_src($team_carousel_image['id'], 'thumbnail', $member);
						if (empty($team_carousel_image_url)) :
							$team_carousel_image_url = $team_carousel_image['url'];
						else :
							$team_carousel_image_url = $team_carousel_image_url;
						endif;
					?>

						<li class="gridder-list" data-griddercontent="#ma-el-team<?php echo $key + 1; ?>">
							<img src="<?php echo esc_url($team_carousel_image_url); ?>" class="circled" alt="<?php echo $member['ma_el_team_carousel_name']; ?>">
							<div class="ma-team-drawer-hover-content">

								<<?php echo $settings['title_html_tag']; ?> class="ma-el-team-member-name">
									<?php echo $member['ma_el_team_carousel_name']; ?>
								</<?php echo $settings['title_html_tag']; ?>>

								<span class="ma-el-team-member-designation">
									<?php echo $member['ma_el_team_carousel_designation']; ?>
								</span>
							</div>
						</li>

					<?php } ?>
				</ul>

				<!-- Gridder content -->
				<?php foreach ($settings['team_carousel_repeater'] as $key => $member) { ?>

					<div id="ma-el-team<?php echo $key + 1; ?>" class="gridder-content">
						<div class="content-left">
							<span class="ma-el-team-member-designation"><?php echo $member['ma_el_team_carousel_designation']; ?></span>
							<<?php echo $settings['title_html_tag']; ?> class="ma-el-team-member-name">
								<?php echo $member['ma_el_team_carousel_name']; ?>
							</<?php echo $settings['title_html_tag']; ?>>
							<p class="ma-el-team-member-desc">
								<?php echo $this->parse_text_editor($member['ma_el_team_carousel_description']); ?>
							</p>
						</div>

						<div class="content-right">
							<?php if ($member['ma_el_team_carousel_enable_social_profiles'] == 'yes') : ?>
								<ul class="list-inline ma-el-team-member-social">

									<?php if (!empty($member['ma_el_team_carousel_facebook_link']['url'])) : ?>
										<?php $target = $member['ma_el_team_carousel_facebook_link']['is_external'] ? ' target="_blank"' : ''; ?>
										<li>
											<a href="<?php echo esc_url($member['ma_el_team_carousel_facebook_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-facebook"></i></a>
										</li>
									<?php endif; ?>

									<?php if (!empty($member['ma_el_team_carousel_twitter_link']['url'])) : ?>
										<?php $target = $member['ma_el_team_carousel_twitter_link']['is_external'] ? ' target="_blank"' : ''; ?>
										<li>
											<a href="<?php echo esc_url($member['ma_el_team_carousel_twitter_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-twitter"></i></a>
										</li>
									<?php endif; ?>

									<?php if (!empty($member['ma_el_team_carousel_instagram_link']['url'])) : ?>
										<?php $target = $member['ma_el_team_carousel_instagram_link']['is_external'] ?
											' target="_blank"' : ''; ?>
										<li>
											<a href="<?php echo esc_url(
															$member['ma_el_team_carousel_instagram_link']['url']
														); ?>" <?php echo $target; ?>><i class="fa fa-instagram"></i></a>
										</li>
									<?php endif; ?>

									<?php if (!empty($member['ma_el_team_carousel_linkedin_link']['url'])) : ?>
										<?php $target = $member['ma_el_team_carousel_linkedin_link']['is_external'] ? ' target="_blank"' : ''; ?>
										<li>
											<a href="<?php echo esc_url($member['ma_el_team_carousel_linkedin_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-linkedin"></i></a>
										</li>
									<?php endif; ?>

									<?php if (!empty($member['ma_el_team_carousel_dribbble_link']['url'])) : ?>
										<?php $target = $member['ma_el_team_carousel_dribbble_link']['is_external'] ? ' target="_blank"' : ''; ?>
										<li>
											<a href="<?php echo esc_url($member['ma_el_team_carousel_dribbble_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-dribbble"></i></a>
										</li>
									<?php endif; ?>

								</ul>
							<?php endif; ?>
						</div>
					</div>
				<?php } ?>

			</div>

		<?php } else { ?>


			<div <?php echo $this->get_render_attribute_string('ma_el_team_carousel'); ?>>
				<div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>
					<?php foreach ($settings['team_carousel_repeater'] as $key => $member) {
						$team_carousel_image = $member['ma_el_team_carousel_image'];
						$team_carousel_image_url = Group_Control_Image_Size::get_attachment_image_src($team_carousel_image['id'], 'thumbnail', $member);
						if (empty($team_carousel_image_url)) : $team_carousel_image_url = $team_carousel_image['url'];
						else : $team_carousel_image_url = $team_carousel_image_url;
						endif;
					?>

						<div <?php echo $this->get_render_attribute_string('swiper-item'); ?>>
							<div class="ma-el-team-member<?php echo $team_preset; ?> text-center">
								<div class="ma-el-team-member-thumb">
									<?php
									//                                            if( $team_preset == '-circle' && isset( $settings['ma_el_team_circle_image'] ) && !isset( $settings['ma_el_team_circle_image_animation'] )) {
									if ($team_preset == '-circle' && isset($settings['ma_el_team_circle_image'])) {
										$file_path =  MELA_PLUGIN_PATH . '/assets/images/circlesvg/' . $settings['ma_el_team_circle_image'] . '.svg';
										echo file_get_contents($file_path);
										echo '<img src="' . esc_url($team_carousel_image_url) . '" class="circled" alt="' . $member['ma_el_team_carousel_name'] . '">';
									} elseif ($team_preset == '-circle-animation' && isset($settings['ma_el_team_circle_image_animation'])) {

										if ($settings['ma_el_team_circle_image_animation'] == "animation_svg_02") {

											echo '<div class="animation_svg_02"><img src="' . esc_url($team_carousel_image_url) . '" class="circled" alt="' . $member['ma_el_team_carousel_name'] . '"></div>';
										} elseif ($settings['ma_el_team_circle_image_animation'] == "animation_svg_03") {

											echo '<div class="animation_svg_03"></div><div class="animation_svg_03"></div><div class="animation_svg_03"></div><div class="animation_svg_03_center"><img src="' . esc_url($team_carousel_image_url) . '" class="circled" alt="' . $member['ma_el_team_carousel_name'] . '"></div>';
										} else {

											$file_path =  MELA_PLUGIN_PATH . '/assets/images/animation/' .
												$settings['ma_el_team_circle_image_animation'] . '.svg';
											echo file_get_contents($file_path);
											echo '<img src="' . esc_url($team_carousel_image_url) . '" class="circled" alt="' . $member['ma_el_team_carousel_name'] . '">';
										}
									} else {

										echo '<img src="' . esc_url($team_carousel_image_url) . '" class="circled" alt="' . $member['ma_el_team_carousel_name'] . '">';
									} ?>

								</div>
								<div class="ma-el-team-member-content">
									<<?php echo $settings['title_html_tag']; ?> class="ma-el-team-member-name">
										<?php echo $member['ma_el_team_carousel_name'];
										?>
									</<?php echo $settings['title_html_tag']; ?>>
									<span class="ma-el-team-member-designation"><?php echo $member['ma_el_team_carousel_designation']; ?></span>
									<p class="ma-el-team-member-about">
										<?php echo $member['ma_el_team_carousel_description']; ?>
									</p>
									<?php if ($member['ma_el_team_carousel_enable_social_profiles'] == 'yes') : ?>
										<ul class="list-inline ma-el-team-member-social">

											<?php if (!empty($member['ma_el_team_carousel_facebook_link']['url'])) : ?>
												<?php $target = $member['ma_el_team_carousel_facebook_link']['is_external'] ? ' target="_blank"' : ''; ?>
												<li>
													<a href="<?php echo esc_url($member['ma_el_team_carousel_facebook_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-facebook"></i></a>
												</li>
											<?php endif; ?>

											<?php if (!empty($member['ma_el_team_carousel_twitter_link']['url'])) : ?>
												<?php $target = $member['ma_el_team_carousel_twitter_link']['is_external'] ? ' target="_blank"' : ''; ?>
												<li>
													<a href="<?php echo esc_url($member['ma_el_team_carousel_twitter_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-twitter"></i></a>
												</li>
											<?php endif; ?>

											<?php if (!empty($member['ma_el_team_carousel_instagram_link']['url'])) : ?>
												<?php $target = $member['ma_el_team_carousel_instagram_link']['is_external'] ?
													' target="_blank"' : ''; ?>
												<li>
													<a href="<?php echo esc_url(
																	$member['ma_el_team_carousel_instagram_link']['url']
																); ?>" <?php echo $target; ?>><i class="fa fa-instagram"></i></a>
												</li>
											<?php endif; ?>

											<?php if (!empty($member['ma_el_team_carousel_linkedin_link']['url'])) : ?>
												<?php $target = $member['ma_el_team_carousel_linkedin_link']['is_external'] ? ' target="_blank"' : ''; ?>
												<li>
													<a href="<?php echo esc_url($member['ma_el_team_carousel_linkedin_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-linkedin"></i></a>
												</li>
											<?php endif; ?>

											<?php if (!empty($member['ma_el_team_carousel_dribbble_link']['url'])) : ?>
												<?php $target = $member['ma_el_team_carousel_dribbble_link']['is_external'] ? ' target="_blank"' : ''; ?>
												<li>
													<a href="<?php echo esc_url($member['ma_el_team_carousel_dribbble_link']['url']); ?>" <?php echo $target; ?>><i class="fa fa-dribbble"></i></a>
												</li>
											<?php endif; ?>

										</ul>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php } // repeater loop end
					?>
				</div> <!-- swiper-wrapper -->

				<?php
				$this->render_swiper_navigation();
				$this->render_swiper_pagination();
				?>

			</div>

		<?php } // carousel layout
		?>


	<?php
	}

	protected function render_swiper_navigation()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute([
			'navigation' => [
				'class' => [
					'jltma-arrows',
					'jltma-swiper__navigation',
					'jltma-swiper__navigation--' . $settings['arrows_placement'],
					'jltma-swiper__navigation--' . $settings['arrows_position'],
					'jltma-swiper__navigation--' . $settings['arrows_position_vertical']
				],
			],
		]);
	?>
		<div <?php echo $this->get_render_attribute_string('navigation'); ?>>
			<?php
			$this->render_swiper_arrows();
			?>
		</div>
	<?php
	}



	public function render_swiper_pagination()
	{
		$settings = $this->get_settings_for_display();
		if ('yes' !== $settings['carousel_pagination'])
			return;

		$this->add_render_attribute('pagination', 'class', [
			'jltma-swiper__pagination',
			'jltma-swiper__pagination--' . $settings['carousel_direction'],
			'jltma-swiper__pagination--' . $settings['pagination_position'],
			'jltma-swiper__pagination-' . $this->get_id(),
			'swiper-pagination',
		]);

	?>
		<div <?php echo $this->get_render_attribute_string('pagination'); ?>>
		</div>
	<?php
	}
	protected function render_swiper_arrows()
	{
		$settings = $this->get_settings_for_display();
		if ('yes' !== $settings['carousel_arrows'])
			return;

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$this->add_render_attribute([
			'button-prev' => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--prev',
					'jltma-arrow',
					'jltma-arrow--prev',
					'jltma-swiper__button--prev-' . $this->get_id(),
				],
			],
			'button-prev-icon' => [
				'class' => 'eicon-chevron-' . $prev,
			],
			'button-next' => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--next',
					'jltma-arrow',
					'jltma-arrow--next',
					'jltma-swiper__button--next-' . $this->get_id(),
				],
			],
			'button-next-icon' => [
				'class' => 'eicon-chevron-' . $next,
			],
		]);

	?><div <?php echo $this->get_render_attribute_string('button-prev'); ?>>
			<i <?php echo $this->get_render_attribute_string('button-prev-icon'); ?>></i>
		</div>
		<div <?php echo $this->get_render_attribute_string('button-next'); ?>>
			<i <?php echo $this->get_render_attribute_string('button-next-icon'); ?>></i>
		</div><?php
			}
		}
