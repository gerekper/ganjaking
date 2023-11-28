<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Repeater;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Team Member Carousel Widget
 */
class Team_Member_Carousel extends Widget_Base
{

	/**
	 * Retrieve team member carousel widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'eael-team-member-carousel';
	}

	/**
	 * Retrieve team member carousel widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __('Team Member Carousel', 'essential-addons-elementor');
	}

	/**
	 * Retrieve the list of categories the team member carousel widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'team',
			'team member carousel',
			'ea team member carousel',
			'ea team member slider',
			'ea team slider',
			'carousel',
			'member',
			'team member',
			'person',
			'card',
			'meet the team',
			'team builder',
			'our team ea',
			'essential addons',
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/team-member-carousel/';
	}

	/**
	 * Retrieve team member carousel widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eaicon-team-member-carousel';
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim',
		];
	}
	/**
	 * Register team member carousel widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls()
	{

		/*-----------------------------------------------------------------------------------*/
		/*    CONTENT TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Team Members
		 */
		$this->start_controls_section(
			'section_team_member',
			[
				'label' => __('Team Members', 'essential-addons-elementor'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'team_member_name',
			[
				'label'   => __('Name', 'essential-addons-elementor'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('John Doe', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'team_member_position',
			[
				'label'   => __('Position', 'essential-addons-elementor'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('WordPress Developer', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'team_member_description',
			[
				'label'   => __('Description', 'essential-addons-elementor'),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter member description here which describes the position of member in company', 'essential-addons-elementor'),
			]
		);

		$repeater->add_control(
			'team_member_image',
			[
				'label'   => __('Image', 'essential-addons-elementor'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'team_member_image_hyperlink',
			[
				'label' => esc_html__( 'Link', 'essential-addons-elementor'),
				'type' => Controls_Manager::URL,
				'description' => esc_html__( 'Link image with custom URL', 'essential-addons-elementor' )
			]
		);

		$repeater->add_control(
			'social_links_heading',
			[
				'label'     => __('Social Links', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'mail_address',
			[
				'label'       => __('Mail Address', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Email Address of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'facebook_url',
			[
				'label'       => __('Facebook', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Facebook page or profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'twitter_url',
			[
				'label'       => __('Twitter', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Twitter profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'google_plus_url',
			[
				'label'       => __('Google+', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Google+ profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'linkedin_url',
			[
				'label'       => __('Linkedin', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Linkedin profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'instagram_url',
			[
				'label'       => __('Instagram', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Instagram profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'youtube_url',
			[
				'label'       => __('YouTube', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter YouTube profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'pinterest_url',
			[
				'label'       => __('Pinterest', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Pinterest profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'dribbble_url',
			[
				'name'        => 'dribbble_url',
				'label'       => __('Dribbble', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Dribbble profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'xing_url',
			[
				'name'        => 'xing_url',
				'label'       => __('XING', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter XING profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'snapchat_url',
			[
				'name'        => 'snapchat_url',
				'label'       => __('Snapchat', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Snapchat profile URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'custom_url',
			[
				'name'        => 'custom_url',
				'label'       => __('Custom URL', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active'     => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'description' => __('Enter Custom URL of team member', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'custom_icon',
			[
				'label' => esc_html__( 'Custom Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
				'exclude_inline_options' => [ 'svg' ],
				'default' => [
					'value' => 'fab fa-wordpress',
					'library' => 'fa-brands',
				],
			]
		);

		$this->add_control(
			'team_member_details',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'team_member_name'     => 'Team Member #1',
						'team_member_position' => 'WordPress Developer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'google_plus_url'      => '#',
					],
					[
						'team_member_name'     => 'Team Member #2',
						'team_member_position' => 'Web Designer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'google_plus_url'      => '#',
					],
					[
						'team_member_name'     => 'Team Member #3',
						'team_member_position' => 'Testing Engineer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'google_plus_url'      => '#',
					],
				],
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ team_member_name }}}',
			]
		);

		$this->add_control(
			'member_social_links',
			[
				'label'        => __('Show Social Icons', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __('Yes', 'essential-addons-elementor'),
				'label_off'    => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Team Member Settings
		 */
		$this->start_controls_section(
			'section_member_box_settings',
			[
				'label' => __('Team Member Settings', 'essential-addons-elementor'),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'full',
			]
		);

		$this->add_control(
			'name_html_tag',
			[
				'label'   => __('Name HTML Tag', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1'   => __('H1', 'essential-addons-elementor'),
					'h2'   => __('H2', 'essential-addons-elementor'),
					'h3'   => __('H3', 'essential-addons-elementor'),
					'h4'   => __('H4', 'essential-addons-elementor'),
					'h5'   => __('H5', 'essential-addons-elementor'),
					'h6'   => __('H6', 'essential-addons-elementor'),
					'div'  => __('div', 'essential-addons-elementor'),
					'span' => __('span', 'essential-addons-elementor'),
					'p'    => __('p', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'position_html_tag',
			[
				'label'   => __('Position HTML Tag', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => [
					'h1'   => __('H1', 'essential-addons-elementor'),
					'h2'   => __('H2', 'essential-addons-elementor'),
					'h3'   => __('H3', 'essential-addons-elementor'),
					'h4'   => __('H4', 'essential-addons-elementor'),
					'h5'   => __('H5', 'essential-addons-elementor'),
					'h6'   => __('H6', 'essential-addons-elementor'),
					'div'  => __('div', 'essential-addons-elementor'),
					'span' => __('span', 'essential-addons-elementor'),
					'p'    => __('p', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'social_links_position',
			[
				'label'     => __('Social Icons Position', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after_desc',
				'options'   => [
					'before_desc' => __('Before Description', 'essential-addons-elementor'),
					'after_desc'  => __('After Description', 'essential-addons-elementor'),
				],
				'condition' => [
					'member_social_links' => 'yes',
				],
			]
		);

		$this->add_control(
			'overlay_content',
			[
				'label'   => __('Overlay Content', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'         => __('None', 'essential-addons-elementor'),
					'social_icons' => __('Social Icons', 'essential-addons-elementor'),
					'all_content'  => __('Description + Social Icons', 'essential-addons-elementor'),
					'atoz_content' => __('All Content', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'member_title_divider',
			[
				'label'        => __('Divider after Name', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __('Show', 'essential-addons-elementor'),
				'label_off'    => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'member_position_divider',
			[
				'label'        => __('Divider after Position', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'hide',
				'label_on'     => __('Show', 'essential-addons-elementor'),
				'label_off'    => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'member_description_divider',
			[
				'label'        => __('Divider after Description', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'hide',
				'label_on'     => __('Show', 'essential-addons-elementor'),
				'label_off'    => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Slider Settings
		 */
		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => __('Slider Settings', 'essential-addons-elementor'),
			]
		);

		$this->add_responsive_control(
			'items',
			[
				'label'          => __('Visible Items', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['size' => 3],
				'tablet_default' => ['size' => 2],
				'mobile_default' => ['size' => 1],
				'range'          => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'size_units'     => '',
			]
		);

		$this->add_responsive_control(
			'margin',
			[
				'label'          => __('Margin', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['size' => 10],
				'tablet_default' => ['size' => 10],
				'mobile_default' => ['size' => 10],
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units'     => '',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __('Autoplay', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __('Yes', 'essential-addons-elementor'),
				'label_off'    => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'      => __('Autoplay Speed', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => ['size' => 2000],
				'range'      => [
					'px' => [
						'min'  => 500,
						'max'  => 5000,
						'step' => 1,
					],
				],
				'size_units' => '',
				'condition'  => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => __('Pause On Hover', 'essential-addons-elementor'),
				'description'  => __('Pause slider when hover on slider area.', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __('Pause', 'essential-addons-elementor'),
				'label_off'    => __('Play', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'infinite_loop',
			[
				'label'        => __('Infinite Loop', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __('Yes', 'essential-addons-elementor'),
				'label_off'    => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label'        => __('Grab Cursor', 'essential-addons-elementor'),
				'description'  => __('Shows grab cursor when you hover over the slider', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __('Show', 'essential-addons-elementor'),
				'label_off'    => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'name_navigation_heading',
			[
				'label'     => __('Navigation', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'arrows',
			[
				'label'        => __('Arrows', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __('Yes', 'essential-addons-elementor'),
				'label_off'    => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'dots',
			[
				'label'        => __('Dots', 'essential-addons-elementor'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __('Yes', 'essential-addons-elementor'),
				'label_off'    => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*    STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Box Style
		 */
		$this->start_controls_section(
			'section_member_box_style',
			[
				'label' => __('Box Style', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_box_alignment',
			[
				'label'     => __('Alignment', 'essential-addons-elementor'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'member_box_margin',
			[
				'label'      => __('Margin', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'member_main_box_padding',
			[
				'label'      => __('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'member_box_radius',
			[
				'label'      => __('Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'member_box_border',
				'label'    => __('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-tm',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'member_box_shadow',
				'label'    => __('Box Shadow', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-tm',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'member_box_background',
				'label'    => __('Background', 'essential-addons-elementor'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .eael-tm',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Content
		 */
		$this->start_controls_section(
			'section_member_content_style',
			[
				'label' => __('Content', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'member_box_heightttt',
			[
				'label'      => esc_html__('Height', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-content.eael-tm-content-normal' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'member_box_bg_color',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-content-normal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'member_main_box_border',
				'label'       => __('Border', 'essential-addons-elementor'),
				'placeholder' => '1px',
				'default'     => '1px',
				'separator'   => 'before',
				'selector'    => '{{WRAPPER}} .eael-tm-content-normal',
			]
		);

		$this->add_control(
			'member_box_border_radius',
			[
				'label'      => __('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-content-normal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_box_padding',
			[
				'label'      => __('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-content-normal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'pa_member_box_shadow',
				'selector'  => '{{WRAPPER}} .eael-tm-content-normal',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Overlay
		 */
		$this->start_controls_section(
			'section_member_overlay_style',
			[
				'label'     => __('Overlay', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'overlay_content!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_alignment',
			[
				'label'     => __('Alignment', 'essential-addons-elementor'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-overlay-content-wrap' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'overlay_content!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'overlay_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .eael-tm-overlay-content-wrap:before',
				'condition' => [
					'overlay_content!' => 'none',
				],
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label'     => __('Opacity', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-overlay-content-wrap:before' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'overlay_content!' => 'none',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_member_image_style',
			[
				'label' => __('Image', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_image_width',
			[
				'label'          => __('Image Width', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['%', 'px'],
				'range'          => [
					'px' => [
						'max' => 1200,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_image_height',
			[
				'label'          => __('Image Height', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['%', 'px'],
				'range'          => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-image img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'member_image_border',
				'label'       => __('Border', 'essential-addons-elementor'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .eael-tm-image img',
			]
		);

		$this->add_control(
			'member_image_border_radius',
			[
				'label'      => __('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-image img, {{WRAPPER}} .eael-tm-overlay-content-wrap:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_image_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Name
		 */
		$this->start_controls_section(
			'section_member_name_style',
			[
				'label' => __('Name', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'member_name_typography',
				'label'    => __('Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'selector' => '{{WRAPPER}} .eael-tm-name',
			]
		);

		$this->add_control(
			'member_name_text_color',
			[
				'label'     => __('Text Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'member_name_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_divider_heading',
			[
				'label'     => __('Divider', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_divider_color',
			[
				'label'     => __('Divider Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-title-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_divider_style',
			[
				'label'     => __('Divider Style', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => __('Solid', 'essential-addons-elementor'),
					'dotted' => __('Dotted', 'essential-addons-elementor'),
					'dashed' => __('Dashed', 'essential-addons-elementor'),
					'double' => __('Double', 'essential-addons-elementor'),
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-title-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition' => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_width',
			[
				'label'          => __('Divider Width', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-title-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_height',
			[
				'label'          => __('Divider Height', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 4,
				],
				'size_units'     => ['px'],
				'range'          => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-title-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'name_divider_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-title-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_title_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Position
		 */
		$this->start_controls_section(
			'section_member_position_style',
			[
				'label' => __('Position', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'member_position_typography',
				'label'    => __('Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'selector' => '{{WRAPPER}} .eael-tm-position',
			]
		);

		$this->add_control(
			'member_position_text_color',
			[
				'label'     => __('Text Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-position' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'member_position_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'position_divider_heading',
			[
				'label'     => __('Divider', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'position_divider_color',
			[
				'label'     => __('Divider Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-position-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'position_divider_style',
			[
				'label'     => __('Divider Style', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => __('Solid', 'essential-addons-elementor'),
					'dotted' => __('Dotted', 'essential-addons-elementor'),
					'dashed' => __('Dashed', 'essential-addons-elementor'),
					'double' => __('Double', 'essential-addons-elementor'),
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-position-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition' => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_width',
			[
				'label'          => __('Divider Width', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-position-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_height',
			[
				'label'          => __('Divider Height', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 4,
				],
				'size_units'     => ['px'],
				'range'          => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-position-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'position_divider_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-position-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_position_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Description
		 */
		$this->start_controls_section(
			'section_member_description_style',
			[
				'label' => __('Description', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'member_description_typography',
				'label'    => __('Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'selector' => '{{WRAPPER}} .eael-tm-description',
			]
		);

		$this->add_control(
			'member_description_text_color',
			[
				'label'     => __('Text Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'member_description_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_divider_heading',
			[
				'label'     => __('Divider', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_divider_color',
			[
				'label'     => __('Divider Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-description-divider' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_divider_style',
			[
				'label'     => __('Divider Style', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => __('Solid', 'essential-addons-elementor'),
					'dotted' => __('Dotted', 'essential-addons-elementor'),
					'dashed' => __('Dashed', 'essential-addons-elementor'),
					'double' => __('Double', 'essential-addons-elementor'),
				],
				'selectors' => [
					'{{WRAPPER}} .eael-tm-description-divider' => 'border-bottom-style: {{VALUE}}',
				],
				'condition' => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_width',
			[
				'label'          => __('Divider Width', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 100,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 800,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-description-divider' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_height',
			[
				'label'          => __('Divider Height', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 4,
				],
				'size_units'     => ['px'],
				'range'          => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-description-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'description_divider_margin',
			[
				'label'          => __('Margin Bottom', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 10,
					'unit' => 'px',
				],
				'size_units'     => ['px', '%'],
				'range'          => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-description-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'member_description_divider' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Social Icons
		 */
		$this->start_controls_section(
			'section_member_social_links_style',
			[
				'label' => __('Social Icons', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'member_icons_gap',
			[
				'label'          => __('Icons Gap', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['size' => 10],
				'size_units'     => ['%', 'px'],
				'range'          => [
					'px' => [
						'max' => 60,
					],
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-social-links li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_icon_size',
			[
				'label'          => __('Icon Size', 'essential-addons-elementor'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => [
					'px' => [
						'max' => 30,
					],
				],
				'default'        => [
					'size' => '14',
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_links_style');

		$this->start_controls_tab(
			'tab_links_normal',
			[
				'label' => __('Normal', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'member_links_icons_color',
			[
				'label'     => __('Icons Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'member_links_bg_color',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'member_links_border',
				'label'       => __('Border', 'essential-addons-elementor'),
				'placeholder' => '1px',
				'default'     => '1px',
				'separator'   => 'before',
				'selector'    => '{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap',
			]
		);

		$this->add_control(
			'member_links_border_radius',
			[
				'label'      => __('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_links_padding',
			[
				'label'      => __('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			[
				'label' => __('Hover', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'member_links_icons_color_hover',
			[
				'label'     => __('Icons Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-social-links li a:hover .eael-tm-social-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'member_links_bg_color_hover',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'member_links_border_color_hover',
			[
				'label'     => __('Border Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-tm-social-links .eael-tm-social-icon-wrap:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Arrows
		 */
		$this->start_controls_section(
			'section_arrows_style',
			[
				'label'     => __('Arrows', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow',
			[
				'label'       => __('Choose Arrow', 'essential-addons-elementor'),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'fa fa-angle-right',
				'options'     => [
					'fa fa-angle-right'          => __('Angle', 'essential-addons-elementor'),
					'fa fa-angle-double-right'   => __('Double Angle', 'essential-addons-elementor'),
					'fa fa-chevron-right'        => __('Chevron', 'essential-addons-elementor'),
					'fa fa-chevron-circle-right' => __('Chevron Circle', 'essential-addons-elementor'),
					'fa fa-arrow-right'          => __('Arrow', 'essential-addons-elementor'),
					'fa fa-long-arrow-right'     => __('Long Arrow', 'essential-addons-elementor'),
					'fa fa-caret-right'          => __('Caret', 'essential-addons-elementor'),
					'fa fa-caret-square-o-right' => __('Caret Square', 'essential-addons-elementor'),
					'fa fa-arrow-circle-right'   => __('Arrow Circle', 'essential-addons-elementor'),
					'fa fa-arrow-circle-o-right' => __('Arrow Circle O', 'essential-addons-elementor'),
					'fa fa-toggle-right'         => __('Toggle', 'essential-addons-elementor'),
					'fa fa-hand-o-right'         => __('Hand', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'      => __('Arrows Size', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => ['size' => '22'],
				'range'      => [
					'px' => [
						'min'  => 15,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'left_arrow_position',
			[
				'label'      => __('Align Left Arrow', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => -100,
						'max'  => 40,
						'step' => 1,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'right_arrow_position',
			[
				'label'      => __('Align Right Arrow', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => -100,
						'max'  => 40,
						'step' => 1,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_arrows_style');

		$this->start_controls_tab(
			'tab_arrows_normal',
			[
				'label' => __('Normal', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'arrows_bg_color_normal',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color_normal',
			[
				'label'     => __('Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'arrows_border_normal',
				'label'       => __('Border', 'essential-addons-elementor'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev',
			]
		);

		$this->add_control(
			'arrows_border_radius_normal',
			[
				'label'      => __('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			[
				'label' => __('Hover', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'arrows_bg_color_hover',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color_hover',
			[
				'label'     => __('Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_border_color_hover',
			[
				'label'     => __('Border Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => __('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Dots
		 */
		$this->start_controls_section(
			'section_dots_style',
			[
				'label'     => __('Dots', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dots' => 'yes',
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'   => __('Position', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'inside'  => __('Inside', 'essential-addons-elementor'),
					'outside' => __('Outside', 'essential-addons-elementor'),
				],
				'default' => 'outside',
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label'      => __('Size', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 2,
						'max'  => 40,
						'step' => 1,
					],
				],
				'size_units' => '',
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'dots_spacing',
			[
				'label'      => __('Spacing', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 30,
						'step' => 1,
					],
				],
				'size_units' => '',
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_dots_style');

		$this->start_controls_tab(
			'tab_dots_normal',
			[
				'label' => __('Normal', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'dots_color_normal',
			[
				'label'     => __('Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_dot_color_normal',
			[
				'label'     => __('Active Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dots_border_normal',
				'label'       => __('Border', 'essential-addons-elementor'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet',
			]
		);

		$this->add_control(
			'dots_border_radius_normal',
			[
				'label'      => __('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_margin',
			[
				'label'              => __('Margin', 'essential-addons-elementor'),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => ['px', 'em', '%'],
				'allowed_dimensions' => 'vertical',
				'placeholder'        => [
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				],
				'selectors'          => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullets' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			[
				'label' => __('Hover', 'essential-addons-elementor'),
			]
		);

		$this->add_control(
			'dots_color_hover',
			[
				'label'     => __('Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dots_border_color_hover',
			[
				'label'     => __('Border Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
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
		$image = $this->get_settings('member_image');

		$this->add_render_attribute(
			'team-member-carousel-wrap',
			[
				'class' => ['swiper-container-wrap', 'eael-team-member-carousel-wrap'],
			]
		);

		if ($settings['dots_position']) {
			$this->add_render_attribute('team-member-carousel-wrap', 'class', 'swiper-container-wrap-dots-' . $settings['dots_position']);
		}

		$swiper_class = $swiper_version_class = '';
        if ( class_exists( 'Elementor\Plugin' ) ) {
            $swiper_class           = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
            $swiper_version_class   = 'swiper' === $swiper_class ? 'swiper-8' : 'swiper-8-lower';
        }

		$this->add_render_attribute(
			'team-member-carousel',
			[
				'class'           => [ esc_attr( $swiper_class ), esc_attr( $swiper_version_class ), 'eael-tm-wrapper', 'eael-tm-carousel'],
				'id'              => 'swiper-container-' . esc_attr($this->get_id()),
				'data-pagination' => '.swiper-pagination-' . esc_attr($this->get_id()),
				'data-arrow-next' => '.swiper-button-next-' . esc_attr($this->get_id()),
				'data-arrow-prev' => '.swiper-button-prev-' . esc_attr($this->get_id()),
			]
		);

		$this->add_render_attribute('team-member-carousel', 'data-id', 'swiper-container-' . esc_attr($this->get_id()));

        $default_items = !empty($settings['items']['size']) ? $settings['items']['size'] : 3;
        $this->add_render_attribute('team-member-carousel', 'data-items', $default_items);

        $default_margin = !empty($settings['margin']['size']) ? $settings['margin']['size'] : 10;
        $this->add_render_attribute('team-member-carousel', 'data-margin', $default_margin);

        if (empty($settings['items_mobile']['size'])){
            $this->add_render_attribute('team-member-carousel', 'data-items-mobile', 1);
        }
        if (empty($settings['margin_mobile']['size'])){
            $this->add_render_attribute('team-member-carousel', 'data-margin-mobile', 10);
        }
        if (empty($settings['items_tablet']['size'])){
            $this->add_render_attribute('team-member-carousel', 'data-items-tablet', 2);
        }
        if (empty($settings['margin_tablet']['size'])){
            $this->add_render_attribute('team-member-carousel', 'data-margin-tablet', 10);
        }

        if ( method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_breakpoints_config' ) && ! empty( $breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints_config() ) ) {
            foreach ( $breakpoints as $key => $breakpoint ){
                if ($breakpoint['is_enabled']) {
                    if (!empty($settings['items_'.$key]['size'])) {
                        $this->add_render_attribute('team-member-carousel', 'data-items-'.$key, $settings['items_'.$key]['size']);
                    }
                    if (!empty($settings['margin_'.$key]['size'])) {
                        $this->add_render_attribute('team-member-carousel', 'data-margin-'.$key, $settings['margin_'.$key]['size']);
                    }
                }
            }
        }

		if (!empty($settings['slider_speed']['size'])) {
			$this->add_render_attribute('team-member-carousel', 'data-speed', $settings['slider_speed']['size']);
		}

		if ($settings['autoplay'] == 'yes' && !empty($settings['autoplay_speed']['size'])) {
			$this->add_render_attribute('team-member-carousel', 'data-autoplay', $settings['autoplay_speed']['size']);
		} else {
			$this->add_render_attribute('team-member-carousel', 'data-autoplay', "0");
		}

		if ($settings['infinite_loop'] == 'yes') {
			$this->add_render_attribute('team-member-carousel', 'data-loop', "1");
		}

		if ($settings['grab_cursor'] == 'yes') {
			$this->add_render_attribute('team-member-carousel', 'data-grab-cursor', "1");
		}

		if ($settings['arrows'] == 'yes') {
			$this->add_render_attribute('team-member-carousel', 'data-arrows', "1");
		}

		if ($settings['dots'] == 'yes') {
			$this->add_render_attribute('team-member-carousel', 'data-dots', "1");
		}

		if ($settings['pause_on_hover'] == 'yes') {
			$this->add_render_attribute('team-member-carousel', 'data-pause-on-hover', "1");
		}

		if ($settings['dots_position']) {
			$this->add_render_attribute('team-member-carousel', 'class', 'eael-tm-carousel-dots-' . $settings['dots_position']);
		}

		?>
        <div <?php echo $this->get_render_attribute_string('team-member-carousel-wrap'); ?>>
            <div <?php echo $this->get_render_attribute_string('team-member-carousel'); ?>>
                <div class="swiper-wrapper">
					<?php foreach ($settings['team_member_details'] as $index => $item) : ?>
                        <div class="swiper-slide">
                            <div class="eael-tm">
                                <div class="eael-tm-image">
									<?php
									if ( isset( $item['team_member_image_hyperlink'] ) && '' != $item['team_member_image_hyperlink']['url'] ) {
										$this->add_render_attribute( 'eael-team-member-img-link-'.$item['_id'], 'href', esc_url( $item['team_member_image_hyperlink']['url'] )
										);

										if ($item['team_member_image_hyperlink']['is_external']) {
										    $this->add_render_attribute('eael-team-member-img-link-'.$item['_id'], 'target', '_blank');
										}

										if ($item['team_member_image_hyperlink']['nofollow']) {
										    $this->add_render_attribute('eael-team-member-img-link-'.$item['_id'], 'rel', 'nofollow');
										}

										if ( '' != $item['team_member_image_hyperlink']['custom_attributes'] ) {
											$attributes = explode(',', $item['team_member_image_hyperlink']['custom_attributes'] );
											if ( count( $attributes ) > 0 ) {
												foreach ( $attributes as $_attribute ) {
													$attribute = explode( '|', $_attribute );
													if ( isset( $attribute[1] ) ) {
														$this->add_render_attribute('eael-team-member-img-link-'.$item['_id'], esc_attr( $attribute[0] ), esc_attr( $attribute[1] ));
													}
												}
											}
										}
										echo "<a " . $this->get_render_attribute_string('eael-team-member-img-link-'.$item['_id']) . ">";
									}
									$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['team_member_image']['id'], 'image_size', $settings );

									if ( $image_url ) {
										echo $image_html = '<img src="' . esc_url( $image_url ) . '"alt="' . esc_attr(get_post_meta($item['team_member_image']['id'], '_wp_attachment_image_alt', true)) . '">';
									} else {
										echo $image_html = '<img src="' . esc_url( $item['team_member_image']['url'] ) .'">';
									}
									if ( isset( $item['team_member_image_hyperlink'] ) && $item['team_member_image_hyperlink']['url'] != '' ) {
										echo "</a>";
									}
									?>
									<?php if ($settings['overlay_content'] !== 'none') : ?>
                                        <div class="eael-tm-overlay-content-wrap">
                                            <div class="eael-tm-content">
												<?php
												if ($settings['overlay_content'] == 'social_icons') {
													if ($settings['member_social_links'] == 'yes') {
														$this->member_social_links($item);
													}
												} else if ($settings['overlay_content'] == 'all_content') {
													if ($settings['member_social_links'] == 'yes') {
														if ($settings['social_links_position'] == 'before_desc') {
															$this->member_social_links($item);
														}
													}
													$this->render_description($item);
													if ($settings['member_social_links'] == 'yes') {
														if ($settings['social_links_position'] == 'after_desc') {
															$this->member_social_links($item);
														}
													}
												} else if ($settings['overlay_content'] == 'atoz_content') {
													$this->render_name($item);
													$this->render_position($item);
													if ($settings['member_social_links'] == 'yes' && $settings['social_links_position'] == 'before_desc') {
														$this->member_social_links($item);
													}
													$this->render_description($item);
													if ($settings['member_social_links'] == 'yes' && $settings['social_links_position'] == 'after_desc') {
														$this->member_social_links($item);
													}
												}
												?>
                                            </div>
                                        </div>
									<?php endif; ?>
                                </div>

                                <div class="eael-tm-content eael-tm-content-normal">
									<?php
									if ($settings['overlay_content'] !== 'atoz_content') {
										if ($settings['overlay_content'] == 'all_content') {
											// Name
											$this->render_name($item);
											// Position
											$this->render_position($item);
										} else if ($settings['overlay_content'] != 'all_content') {
											$this->render_name($item);
											$this->render_position($item);
											if ($settings['member_social_links'] == 'yes' && $settings['overlay_content'] == 'none') {
												if ($settings['social_links_position'] == 'before_desc') {
													$this->member_social_links($item);
												}
											}
											$this->render_description($item);
											if ($settings['member_social_links'] == 'yes' && $settings['overlay_content'] == 'none') {
												if ($settings['social_links_position'] == 'after_desc') {
													$this->member_social_links($item);
												}
											}
										}
									}
									?>
                                </div>
                            </div><!-- .eael-tm -->
                        </div><!-- .swiper-slide -->
					<?php endforeach; ?>
                </div>
            </div>
			<?php
			$this->render_dots();

			$this->render_arrows();
			?>
        </div>
		<?php
	}

	protected function render_name($item)
	{
		$settings = $this->get_settings_for_display();

		if ($item['team_member_name'] != '') {
			printf('<%1$s class="eael-tm-name">%2$s</%1$s>', $settings['name_html_tag'], $item['team_member_name']);
		}
		?>
		<?php if ($settings['member_title_divider'] == 'yes') { ?>
        <div class="eael-tm-title-divider-wrap">
            <div class="eael-tm-divider eael-tm-title-divider"></div>
        </div>
	<?php }
	}

	protected function render_position($item)
	{
		$settings = $this->get_settings_for_display();

		if ($item['team_member_position'] != '') {
			printf('<%1$s class="eael-tm-position">%2$s</%1$s>', $settings['position_html_tag'], $item['team_member_position']);
		}
		?>
		<?php if ($settings['member_position_divider'] == 'yes') { ?>
        <div class="eael-tm-position-divider-wrap">
            <div class="eael-tm-divider eael-tm-position-divider"></div>
        </div>
	<?php }
	}

	protected function render_description($item)
	{
		$settings = $this->get_settings_for_display();
		if ($item['team_member_description'] != '') { ?>
            <div class="eael-tm-description">
				<?php echo $this->parse_text_editor($item['team_member_description']); ?>
            </div>
		<?php } ?>
		<?php if ($settings['member_description_divider'] == 'yes') { ?>
        <div class="eael-tm-description-divider-wrap">
            <div class="eael-tm-divider eael-tm-description-divider"></div>
        </div>
	<?php }
	}

	private function member_social_links($item)
	{

		$mail_address = $item['mail_address'];
		$facebook_url = $item['facebook_url'];
		$twitter_url = $item['twitter_url'];
		$google_plus_url = $item['google_plus_url'];
		$linkedin_url = $item['linkedin_url'];
		$instagram_url = $item['instagram_url'];
		$youtube_url = $item['youtube_url'];
		$pinterest_url = $item['pinterest_url'];
		$dribbble_url = $item['dribbble_url'];
		$xing_url = $item['xing_url'];
		$snapchat_url = $item['snapchat_url'];
		$custom_url = $item['custom_url'];
		$custom_icon = empty( $item['custom_icon']['value'] ) ? '' : $item['custom_icon']['value'];
		?>
        <div class="eael-tm-social-links-wrap">
            <ul class="eael-tm-social-links">
				<?php
				if ($mail_address) {
					printf('<li><a href="%1$s"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-envelope"></span></span></a></li>', esc_attr("mailto:" . $mail_address));
				}
				if ($facebook_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-facebook"></span></span></a></li>', esc_url($facebook_url));
				}
				if ($twitter_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-twitter"></span></span></a></li>', esc_url($twitter_url));
				}
				if ($google_plus_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-google-plus"></span></span></a></li>', esc_url($google_plus_url));
				}
				if ($linkedin_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-linkedin"></span></span></a></li>', esc_url($linkedin_url));
				}
				if ($instagram_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-instagram"></span></span></a></li>', esc_url($instagram_url));
				}
				if ($youtube_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-youtube"></span></span></a></li>', esc_url($youtube_url));
				}
				if ($pinterest_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-pinterest"></span></span></a></li>', esc_url($pinterest_url));
				}
				if ($dribbble_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-dribbble"></span></span></a></li>', esc_url($dribbble_url));
				}
				if ($xing_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-xing"></span></span></a></li>', esc_url($xing_url));
				}
				if ($snapchat_url) {
					printf('<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon fa fa-snapchat"></span></span></a></li>', esc_url($snapchat_url));
				}
				if ( $custom_url ) {
					printf( '<li><a href="%1$s" target="_blank"><span class="eael-tm-social-icon-wrap"><span class="eael-tm-social-icon %2$s"></span></span></a></li>', esc_url( $custom_url ), esc_attr( $custom_icon ) );
				}
				?>
            </ul>
        </div>
		<?php
	}

	/**
	 * Render team member carousel dots output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_dots()
	{
		$settings = $this->get_settings_for_display();

		if ($settings['dots'] == 'yes') { ?>
            <!-- Add Pagination -->
            <div class="swiper-pagination swiper-pagination-<?php echo esc_attr($this->get_id()); ?>"></div>
		<?php }
	}

	/**
	 * Render team member carousel arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_arrows()
	{
		$settings = $this->get_settings_for_display();

		if ($settings['arrows'] == 'yes') { ?>
			<?php
			if ($settings['arrow']) {
				$pa_next_arrow = $settings['arrow'];
				$pa_prev_arrow = str_replace("right", "left", $settings['arrow']);
			} else {
				$pa_next_arrow = 'fa fa-angle-right';
				$pa_prev_arrow = 'fa fa-angle-left';
			}
			?>
            <!-- Add Arrows -->
            <div class="swiper-button-next swiper-button-next-<?php echo esc_attr($this->get_id()); ?>">
                <i class="<?php echo esc_attr($pa_next_arrow); ?>"></i>
            </div>
            <div class="swiper-button-prev swiper-button-prev-<?php echo esc_attr($this->get_id()); ?>">
                <i class="<?php echo esc_attr($pa_prev_arrow); ?>"></i>
            </div>
		<?php }
	}

	protected function content_template()
	{
	}
}
