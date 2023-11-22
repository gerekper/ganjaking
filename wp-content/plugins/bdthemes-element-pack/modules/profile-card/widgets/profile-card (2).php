<?php
namespace ElementPack\Modules\ProfileCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;

use ElementPack\Modules\ProfileCard\Skins;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Profile_Card extends Module_Base {
	public function get_name() {
		return 'bdt-profile-card';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Profile Card', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-profile-card';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'profile card', 'social card', 'social', 'card' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-profile-card' ];
		}
	}

	public function register_skins() {
		$this->add_skin( new Skins\Heline( $this ) );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_profile_card_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'profile',
			[ 
				'label'   => esc_html__( 'Select Profile', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [ 
					'instagram' => esc_html__( 'Instagram', 'bdthemes-element-pack' ),
					'blog'      => esc_html__( 'My Blog', 'bdthemes-element-pack' ),
					'custom'    => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'blog_user_id',
			[ 
				'label'     => esc_html__( 'User ID', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => '1',
				'condition' => [ 
					'profile' => 'blog',
				],
			]
		);

		$this->add_control(
			'alignment',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'profile_badge_text',
			[ 
				'label'     => esc_html__( 'Badge', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Pro', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_badge',
			[ 
				'label'   => __( 'Show Badge', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_user_menu',
			[ 
				'label'   => __( 'Show User Menu', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_image',
			[ 
				'label'   => __( 'Show Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_name',
			[ 
				'label'   => __( 'Show Name', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_username',
			[ 
				'label'   => __( 'Show Username', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_text',
			[ 
				'label'   => __( 'Show Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_status',
			[ 
				'label'   => __( 'Show Status', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_button',
			[ 
				'label'   => __( 'Show Follow Button', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_social_icon',
			[ 
				'label'   => __( 'Show Social Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_custom_profile',
			[ 
				'label'     => esc_html__( 'Custom Profile', 'bdthemes-element-pack' ),
				'condition' => [ 
					'profile' => 'custom',
				],
			]
		);

		$this->add_control(
			'profile_image',
			[ 
				'label'   => __( 'Choose Photo', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [ 
					'url' => BDTEP_ASSETS_URL . 'images/member.svg',
				],
			]
		);

		$this->add_control(
			'profile_name',
			[ 
				'label'       => esc_html__( 'Name', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'Adam Smith', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'profile_username',
			[ 
				'label'       => esc_html__( 'User Name', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => '@adamsmith',
				'label_block' => true,
			]
		);

		$this->add_control(
			'profile_content',
			[ 
				'label'      => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => [ 'active' => true ],
				'default'    => esc_html__( 'Hello, My name is Adam Smith ! I am Web Developer at BDThemes LTD.', 'bdthemes-element-pack' ),
				'show_label' => false,
			]
		);

		$this->add_control(
			'profile_posts',
			[ 
				'label'   => esc_html__( 'Counter Text One', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Posts', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'profile_posts_number',
			[ 
				'label'   => esc_html__( 'Counter Number One', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '213',
			]
		);

		$this->add_control(
			'profile_followers',
			[ 
				'label'   => esc_html__( 'Counter Text Two', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Followers', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'profile_followers_number',
			[ 
				'label'   => esc_html__( 'Counter Number Two', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '423',
			]
		);

		$this->add_control(
			'profile_following',
			[ 
				'label'   => esc_html__( 'Counter Text Three', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Following', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'profile_following_number',
			[ 
				'label'   => esc_html__( 'Counter Number Three', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '213',
			]
		);

		$this->add_control(
			'profile_button_text',
			[ 
				'label'   => esc_html__( 'Follow Button Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Follow', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'follow_link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'bdthemes-element-pack' ),
				'default'     => [ 
					'url' => '#',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_instagram_profile',
			[ 
				'label'     => esc_html__( 'Instagram Profile', 'bdthemes-element-pack' ),
				'condition' => [ 
					'profile' => 'instagram',
				],
			]
		);

		$this->add_control(
			'instagram_posts',
			[ 
				'label'   => esc_html__( 'Posts', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Posts', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'instagram_followers',
			[ 
				'label'   => esc_html__( 'Followers', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Followers', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'instagram_following',
			[ 
				'label'   => esc_html__( 'Following', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Following', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'instagram_button_text',
			[ 
				'label'   => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Follow', 'bdthemes-element-pack' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_blog_profile',
			[ 
				'label'     => esc_html__( 'Blog Profile', 'bdthemes-element-pack' ),
				'condition' => [ 
					'profile' => 'blog',
				],
			]
		);

		$this->add_control(
			'blog_posts',
			[ 
				'label'   => esc_html__( 'Posts', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Posts', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'blog_post_comments',
			[ 
				'label'   => esc_html__( 'Comments', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Comments', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'blog_button_text',
			[ 
				'label'   => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Follow', 'bdthemes-element-pack' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_social_link',
			[ 
				'label'     => __( 'Social Icon', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_social_icon' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_link_title',
			[ 
				'label'   => __( 'Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Facebook',
			]
		);

		$repeater->add_control(
			'social_link',
			[ 
				'label'   => __( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'http://www.facebook.com/bdthemes/',
			]
		);

		$repeater->add_control(
			'social_icon',
			[ 
				'label'   => __( 'Choose Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 
					'value'   => 'fab fa-facebook-f',
					'library' => 'fa-brands',
				],
			]
		);

		$repeater->add_control(
			'icon_color',
			[ 
				'label'     => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link {{CURRENT_ITEM}}'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'icon_background',
			[ 
				'label'     => __( 'Icon Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'social_link_list',
			[ 
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'social_link'       => 'http://www.facebook.com/bdthemes/',
						'social_icon'       => [ 
							'value'   => 'fab fa-facebook-f',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Facebook',
					],
					[ 
						'social_link'       => 'http://www.twitter.com/bdthemes/',
						'social_icon'       => [ 
							'value'   => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Twitter',
					],
					[ 
						'social_link'       => 'http://www.instagram.com/bdthemes/',
						'social_icon'       => [ 
							'value'   => 'fab fa-instagram',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Instagram',
					],
				],
				'title_field' => '{{{ social_link_title }}}',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_content_custom_nav',
			[ 
				'label'     => esc_html__( 'User Menu', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_user_menu' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'custom_nav_title',
			[ 
				'label'   => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'icon',
			[ 
				'label'   => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 
					'value'   => 'fas fa-home',
					'library' => [ 'fa-solid' ],
				],
			]
		);

		$repeater->add_control(
			'custom_nav_link',
			[ 
				'label'   => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_control(
			'custom_navs',
			[ 
				'label'       => esc_html__( 'Menus', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'custom_nav_title' => esc_html__( 'Billing', 'bdthemes-element-pack' ),
						'icon'             => [ 'value' => 'fas fa-dollar-sign', 'library' => 'fa-solid' ],
						'custom_nav_link'  => [ 
							'url' => '#',
						]
					],
					[ 
						'custom_nav_title' => esc_html__( 'Settings', 'bdthemes-element-pack' ),
						'icon'             => [ 'value' => 'fas fa-cog', 'library' => 'fa-solid' ],
						'custom_nav_link'  => [ 
							'url' => '#',
						]
					],
					[ 
						'custom_nav_title' => esc_html__( 'Support', 'bdthemes-element-pack' ),
						'icon'             => [ 'value' => 'fas fa-life-ring', 'library' => 'fa-solid' ],
						'custom_nav_link'  => [ 
							'url' => '#',
						]
					],
				],
				'title_field' => '{{{ custom_nav_title }}}',
			]
		);

		// $this->add_control(
		// 	'show_edit_profile',
		// 	[
		// 		'label'   => __('Edit Profile', 'bdthemes-element-pack'),
		// 		'type'    => Controls_Manager::SWITCHER,
		// 		'default' => 'yes'
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_settings',
			[ 
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'dropdown_width',
			[ 
				'label'     => esc_html__( 'Dropdown Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 100,
						'max' => 450,
					],
				],
				'condition' => [ 
					'show_user_menu' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-dropdown' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dropdown_offset',
			[ 
				'label'     => esc_html__( 'Dropdown Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				//'separator' => 'before',
				'condition' => [ 
					'show_user_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'dropdown_position',
			[ 
				'label'     => esc_html__( 'Dropdown Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-right',
				'options'   => element_pack_drop_position(),
				'condition' => [ 
					'show_user_menu' => 'yes',
				],
			]
		);

		$this->add_control(
			'dropdown_mode',
			[ 
				'label'     => esc_html__( 'Dropdown Mode', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover',
				'options'   => [ 
					'hover' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
					'click' => esc_html__( 'Clicked', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'show_user_menu' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//Style

		$this->start_controls_section(
			'section_profile_card_header_style',
			[ 
				'label' => __( 'Header Area', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_profile_card_header_style' );

		$this->start_controls_tab(
			'tab_profile_card_header_inner',
			[ 
				'label' => esc_html__( 'Inner Style', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'profile_card_header_background',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-header',
			]
		);

		$this->add_control(
			'profile_card_header_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_header_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 
					'px' => [ 
						'min' => 100,
						'max' => 250,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-header' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_skin_header_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 
					'px' => [ 
						'min' => 130,
						'max' => 350,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-header' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 
					'_skin' => 'heline',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_profile_card_header_badge',
			[ 
				'label' => esc_html__( 'Badge', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'profile_badge_text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card-pro span' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'     => 'profile_badge_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-profile-card-pro span',
			]
		);

		$this->add_control(
			'profile_badge_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card-pro span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'profile_badge_text_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card-pro span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'profile_badge_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-profile-card-pro span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_profile_card_header_user_menu',
			[ 
				'label' => esc_html__( 'User Menu', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'settings_menu_size',
			[ 
				'label'     => __( 'Dot Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card-settings i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'settings_icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card-settings i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'settings_dropdown_style',
			[ 
				'label'     => __( 'Dropdown Menu', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'settings_dropdown_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-dropdown-nav>li>a'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-profile-card .bdt-dropdown-nav>li>a svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'settings_dropdown_hover_color',
			[ 
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-dropdown-nav>li>a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'settings_dropdown_background_color',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-dropdown' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'dropdown_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-dropdown',
			]
		);


		$this->add_responsive_control(
			'profile_card_user_menu_left_spacing',
			[ 
				'label'     => __( 'Left Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 250,
					]
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card.bdt-profile-card-heline .bdt-profile-card-settings' => 'left: {{SIZE}}{{UNIT}};'
				],
				'condition' => [ 
					'_skin!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_user_menu_top_spacing',
			[ 
				'label'     => __( 'Top Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 250,
					]
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card.bdt-profile-card-heline .bdt-profile-card-settings' => 'top: {{SIZE}}{{UNIT}};'
				],
				'condition' => [ 
					'_skin!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_profile_card_item_inner_style',
			[ 
				'label' => __( 'Content Area', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'profile_card_inner_background',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'     => 'profile_card_inner_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner',
			]
		);


		$this->add_responsive_control(
			'profile_card_inner_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_inner_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_inner_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'profile_inner_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-inner',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_profile_card_image_style',
			[ 
				'label'     => __( 'Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_image_width',
			[ 
				'label'          => __( 'Size', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'unit' => 'px',
				],
				'tablet_default' => [ 
					'unit' => 'px',
				],
				'mobile_default' => [ 
					'unit' => 'px',
				],
				'size_units'     => [ 'px' ],
				'range'          => [ 
					'px' => [ 
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-left: auto;margin-right: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'     => 'image_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-image img',
			]
		);

		$this->add_control(
			'image_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'profile_card_image_spacing',
			[ 
				'label'     => __( 'Match Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 250,
					]
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-image img'                     => 'margin-top: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-profile-card.bdt-profile-card-heline .bdt-profile-card-image' => 'left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_profile_card_name_style',
			[ 
				'label' => __( 'Full Name', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'profile_card_name_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-name, {{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-name a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-name',
			]
		);


		$this->add_responsive_control(
			'profile_card_name_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-name' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_profile_card_username_style',
			[ 
				'label' => __( 'User Name', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'profile_card_username_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-username' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'username_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-name-info .bdt-username',
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_profile_card_text_style',
			[ 
				'label' => __( 'Bio', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'profile_card_text_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-bio' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-bio',
			]
		);


		$this->add_responsive_control(
			'profile_card_text_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-bio' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_profile_card_statas_style',
			[ 
				'label'     => __( 'Status', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_status' => 'yes',
				],
			]
		);

		$this->add_control(
			'profile_card_stat_color',
			[ 
				'label'     => __( 'Number Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-status .bdt-profile-stat' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'stat_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-status .bdt-profile-stat',
			]
		);

		$this->add_control(
			'profile_card_label_color',
			[ 
				'label'     => __( 'Label Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-status .bdt-profile-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-status .bdt-profile-label',
			]
		);

		$this->add_responsive_control(
			'profile_card_label_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-bio' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[ 
				'label'     => __( 'Follow Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'follow_button_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button' => 'margin-top: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'button_background_color',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'        => 'button_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'button_background_hover_color',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button .bdt-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_social_icon',
			[ 
				'label'     => __( 'Social Icon', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_social_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-button' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_social_icon_style' );

		$this->start_controls_tab(
			'tab_social_icon_normal',
			[ 
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'icon_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'social_icon_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a',
			]
		);

		$this->add_responsive_control(
			'social_icon_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_size',
			[ 
				'label'     => __( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a i'        => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_indent',
			[ 
				'label'     => __( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a + a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'social_icon_tooltip',
			[ 
				'label'   => __( 'Tooltip', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'social_line_color',
			[ 
				'label'     => __( 'Line Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link:before, {{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link:after' => 'background: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_social_icon_hover',
			[ 
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a:hover'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a:hover svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'social_icon_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-profile-card .bdt-profile-card-share-link a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	public function render_loop_custom_nav_list() {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['custom_navs'] as $key => $nav ) {
			$this->add_render_attribute( 'custom-nav-item', 'title', $nav["custom_nav_title"], true );
			$this->add_render_attribute( 'custom-nav-item', 'href', $nav['custom_nav_link']['url'], true );

			if ( $nav['custom_nav_link']['is_external'] ) {
				$this->add_render_attribute( 'custom-nav-item', 'target', '_blank', true );
			}

			if ( $nav['custom_nav_link']['nofollow'] ) {
				$this->add_render_attribute( 'custom-nav-item', 'rel', 'nofollow', true );
			}

			?>
			<li class="bdt-profile-card-custom-item">
				<a <?php echo $this->get_render_attribute_string( 'custom-nav-item' ); ?>>
					<?php if ( $nav['icon'] ) : ?>
						<span class="bdt-ul-custom-nav-icon">
							<?php Icons_Manager::render_icon( $nav['icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
						</span>
					<?php endif; ?>

					<?php echo $nav["custom_nav_title"]; ?>
				</a>
			</li>
			<?php
		}

	}

	public function user_dropdown_menu() {
		$settings        = $this->get_settings_for_display();
		$dropdown_offset = $settings['dropdown_offset'];

		$this->add_render_attribute(
			[ 
				'dropdown-settings' => [ 
					'data-bdt-dropdown' => [ 
						wp_json_encode( array_filter( [ 
							"mode"   => $settings["dropdown_mode"],
							"pos"    => $settings["dropdown_position"],
							"offset" => $dropdown_offset["size"]
						] ) )
					]
				]
			]
		);

		$this->add_render_attribute( 'data-dropdown-settings', 'class', 'bdt-dropdown bdt-text-left bdt-overflow-hidden' );

		?>

		<div <?php echo $this->get_render_attribute_string( 'dropdown-settings' ); ?>>
			<ul class="bdt-nav bdt-dropdown-nav">

				<?php $this->render_loop_custom_nav_list(); ?>

			</ul>
		</div>

		<?php
	}

	public function render_instagram_card() {
		$settings  = $this->get_settings_for_display();
		$instagram = element_pack_instagram_card();

		echo '<pre>';
		print_r( $instagram );
		echo '</pre>';
		?>

		<div class="bdt-profile-card">
			<div class="bdt-profile-card-item">

				<div class="bdt-profile-card-header bdt-flex bdt-flex-between">

					<div class="bdt-profile-card-pro">
						<?php if ( $settings['show_badge'] ) : ?>
							<span>
								<?php echo $settings['profile_badge_text']; ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( $settings['show_user_menu'] ) : ?>
						<div class="bdt-profile-card-settings">
							<a href="javascript:void(0);"><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
						</div>

						<?php $this->user_dropdown_menu(); ?>

					<?php endif; ?>

				</div>

				<div class="bdt-profile-card-inner bdt-text-<?php echo esc_attr( $settings['alignment'] ); ?>">

					<?php if ( $settings['show_image'] && isset( $instagram['profile_picture'] ) ) : ?>
						<div class="bdt-profile-card-image">
							<img src="<?php echo esc_url( $instagram['profile_picture'] ); ?>"
								alt="<?php echo isset( $instagram['full_name'] ) ? $instagram['full_name'] : ''; ?>" />
						</div>
					<?php endif; ?>

					<div class="bdt-profile-card-name-info">

						<?php if ( $settings['show_name'] && isset( $instagram['username'] ) ) : ?>
							<h3 class="bdt-name">
								<a class="" href="https://instagram.com/<?php echo esc_html( $instagram['username'] ); ?>">
									<?php echo isset( $instagram['full_name'] ) ? wp_kses_post( $instagram['full_name'] ) : ''; ?>
								</a>
							</h3>
						<?php endif; ?>

						<?php if ( $settings['show_username'] && isset( $instagram['username'] ) ) : ?>
							<span class="bdt-username">
								<?php echo esc_html( $instagram['username'] ); ?>
							</span>
						<?php endif; ?>

					</div>

					<?php if ( $settings['show_text'] && isset( $instagram['bio'] ) ) : ?>
						<div class="bdt-profile-card-bio">
							<?php echo wp_kses_post( $instagram['bio'] ); ?>
						</div>
					<?php endif; ?>


					<?php if ( $settings['show_status'] ) : ?>
						<div class="bdt-profile-card-status">
							<ul>
								<li>
									<span class="bdt-profile-stat">
										<?php echo isset( $instagram['media_count'] ) ? esc_attr( $instagram['media_count'] ) : ''; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo isset( $settings['instagram_posts'] ) ? esc_html( $settings['instagram_posts'] ) : ''; ?>
									</span>
								</li>
								<li>
									<span class="bdt-profile-stat">
										<?php echo isset( $instagram['counts']['follows'] ) ? esc_attr( $instagram['counts']['follows'] ) : ''; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo isset( $settings['instagram_followers'] ) ? esc_html( $settings['instagram_followers'] ) : ''; ?>
									</span>
								</li>
								<li>
									<span class="bdt-profile-stat">
										<?php echo isset( $instagram['counts']['followed_by'] ) ? esc_attr( $instagram['counts']['followed_by'] ) : ''; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo isset( $settings['instagram_following'] ) ? esc_html( $settings['instagram_following'] ) : ''; ?>
									</span>
								</li>
							</ul>
						</div>
					<?php endif; ?>

					<?php if ( $settings['show_button'] ) : ?>
						<div class="bdt-profile-card-button bdt-margin-medium-top bdt-margin-medium-bottom">
							<a class="bdt-button bdt-button-secondary"
								href="https://instagram.com/<?php echo isset( $instagram['username'] ) ? esc_html( $instagram['username'] ) : ''; ?>">
								<?php echo isset( $settings['instagram_button_text'] ) ? $settings['instagram_button_text'] : ''; ?>
							</a>
						</div>
					<?php endif; ?>

					<?php $this->render_social_icon(); ?>

				</div>

			</div>
		</div>

		<?php
	}

	public function render_blog_card() {
		$settings = $this->get_settings_for_display();

		?>

		<div class="bdt-profile-card">
			<div class="bdt-profile-card-item">

				<div class="bdt-profile-card-header bdt-flex bdt-flex-between">

					<div class="bdt-profile-card-pro">
						<?php if ( $settings['show_badge'] && isset( $settings['profile_badge_text'] ) ) : ?>
							<span>
								<?php echo $settings['profile_badge_text']; ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( $settings['show_user_menu'] ) : ?>
						<div class="bdt-profile-card-settings">
							<a href="javascript:void(0);"><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
						</div>

						<?php $this->user_dropdown_menu(); ?>

					<?php endif; ?>

				</div>

				<div class="bdt-profile-card-inner bdt-text-<?php echo esc_attr( $settings['alignment'] ); ?>">

					<?php if ( $settings['show_image'] ) : ?>
						<div class="bdt-profile-card-image">
							<img src="<?php echo esc_url( get_avatar_url( $settings['blog_user_id'], [ 'size' => 128 ] ) ); ?>"
								alt="<?php echo get_the_author_meta( 'first_name', $settings['blog_user_id'] ); ?>" />
						</div>
					<?php endif; ?>

					<div class="bdt-profile-card-name-info">

						<?php if ( $settings['show_name'] ) : ?>
							<h3 class="bdt-name">
								<?php echo get_the_author_meta( 'first_name', $settings['blog_user_id'] ); ?>
								<?php echo get_the_author_meta( 'last_name', $settings['blog_user_id'] ); ?>
							</h3>
						<?php endif; ?>

						<?php if ( $settings['show_username'] ) : ?>
							<span class="bdt-username">
								<?php echo get_the_author_meta( 'user_nicename', $settings['blog_user_id'] ); ?>
							</span>
						<?php endif; ?>

					</div>

					<?php if ( $settings['show_text'] ) : ?>
						<div class="bdt-profile-card-bio">
							<?php echo get_the_author_meta( 'description', $settings['blog_user_id'] ); ?>
						</div>
					<?php endif; ?>


					<?php if ( $settings['show_status'] ) : ?>
						<div class="bdt-profile-card-status">
							<ul>
								<li>
									<span class="bdt-profile-stat">
										<?php echo count_user_posts( $settings['blog_user_id'] ); ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo esc_html( $settings['blog_posts'] ); ?>
									</span>
								</li>
								<li>
									<span class="bdt-profile-stat">
										<?php
										$comments_count = wp_count_comments();
										echo $comments_count->approved;
										?>
									</span>
									<span class="bdt-profile-label">
										<?php echo esc_html( $settings['blog_post_comments'] ); ?>
									</span>
								</li>
							</ul>
						</div>
					<?php endif; ?>

					<?php if ( $settings['show_button'] ) : ?>
						<div class="bdt-profile-card-button bdt-margin-medium-top bdt-margin-medium-bottom">
							<a class="bdt-button bdt-button-secondary"
								href="<?php echo get_author_posts_url( $settings['blog_user_id'] ); ?>">
								<?php echo $settings['blog_button_text']; ?>
							</a>

						</div>
					<?php endif; ?>

					<?php $this->render_social_icon(); ?>

				</div>

			</div>
		</div>

		<?php

	}

	public function render_custom_card() {
		$settings = $this->get_settings_for_display();

		?>

		<div class="bdt-profile-card">
			<div class="bdt-profile-card-item">

				<div class="bdt-profile-card-header bdt-flex bdt-flex-between">

					<div class="bdt-profile-card-pro">
						<?php if ( $settings['show_badge'] && $settings['profile_badge_text'] ) : ?>
							<span>
								<?php echo $settings['profile_badge_text']; ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( $settings['show_user_menu'] ) : ?>
						<div class="bdt-profile-card-settings">
							<a href="javascript:void(0);"><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
						</div>

						<?php $this->user_dropdown_menu(); ?>

					<?php endif; ?>

				</div>

				<div class="bdt-profile-card-inner bdt-text-<?php echo esc_attr( $settings['alignment'] ); ?>">

					<?php if ( $settings['show_image'] ) : ?>
						<div class="bdt-profile-card-image">
							<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'profile_image' ); ?>
						</div>
					<?php endif; ?>

					<div class="bdt-profile-card-name-info">

						<?php if ( $settings['show_name'] ) : ?>
							<h3 class="bdt-name">
								<?php echo $settings['profile_name']; ?>
							</h3>
						<?php endif; ?>

						<?php if ( $settings['show_username'] ) : ?>
							<span class="bdt-username">
								<?php echo $settings['profile_username']; ?>
							</span>
						<?php endif; ?>

					</div>

					<?php if ( $settings['show_text'] ) : ?>
						<div class="bdt-profile-card-bio">
							<?php echo $settings['profile_content']; ?>
						</div>
					<?php endif; ?>


					<?php if ( $settings['show_status'] ) : ?>
						<div class="bdt-profile-card-status">
							<ul>
								<li>
									<span class="bdt-profile-stat">
										<?php echo $settings['profile_posts_number']; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo esc_html( $settings['profile_posts'] ); ?>
									</span>
								</li>
								<li>
									<span class="bdt-profile-stat">
										<?php echo $settings['profile_followers_number']; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo esc_html( $settings['profile_followers'] ); ?>
									</span>
								</li>
								<li>
									<span class="bdt-profile-stat">
										<?php echo $settings['profile_following_number']; ?>
									</span>
									<span class="bdt-profile-label">
										<?php echo esc_html( $settings['profile_following'] ); ?>
									</span>
								</li>
							</ul>
						</div>
					<?php endif; ?>

					<?php if ( $settings['show_button'] ) : ?>
						<div class="bdt-profile-card-button bdt-margin-medium-top bdt-margin-medium-bottom">
							<a class="bdt-button bdt-button-secondary" href="<?php echo $settings['follow_link']['url'] ?>">
								<?php echo $settings['profile_button_text']; ?>
							</a>
						</div>
					<?php endif; ?>

					<?php $this->render_social_icon(); ?>

				</div>

			</div>
		</div>

		<?php
	}

	public function render_social_icon() {
		$settings = $this->get_settings_for_display();

		?>

		<?php if ( $settings['show_social_icon'] ) : ?>

			<div class="bdt-profile-card-share-wrapper">
				<div class="bdt-profile-card-share-link">
					<?php
					foreach ( $settings['social_link_list'] as $link ) :
						$tooltip = ( 'yes' == $settings['social_icon_tooltip'] ) ? ' title="' . esc_attr( $link['social_link_title'] ) . '" data-bdt-tooltip' : ''; ?>

						<a href="<?php echo esc_url( $link['social_link'] ); ?>" target="_blank" <?php echo $tooltip; ?>
							class="elementor-repeater-item-<?php echo esc_attr( $link['_id'] ); ?>">
							<?php Icons_Manager::render_icon( $link['social_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

		<?php endif;
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if ( 'blog' == $settings['profile'] ) {
			$this->render_blog_card();
		} elseif ( 'instagram' == $settings['profile'] ) {
			$this->render_instagram_card();
		} else {
			$this->render_custom_card();
		}
	}
}
