<?php
/**
 * Twitter Feed
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

defined('ABSPATH') || die();

class Twitter_Carousel extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Twitter Feed Carousel', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-twitter';
	}

	public function get_keywords() {
		return ['twitter-feed', 'twitter', 'feed', 'social media', 'carousel'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__feed_content_controls();
		$this->__feed_settings_content_controls();
		$this->__settings_content_controls();
	}

	protected function __feed_content_controls() {

		$this->start_controls_section(
			'_section_twitter',
			[
				'label' => __( 'Twitter Feed Carousel', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'twitter_carousel_layout_type',
			[
				'label' => __( 'Layout Type', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => 'carousel',
                'options' => [
                    'carousel' => __('Carousel', 'happy-addons-pro'),
                    'remote_carousel' => __('Remote Carousel', 'happy-addons-pro'),
                ],
                'description' => __('Select layout type', 'happy-addons-pro')
			]
		);
        $this->add_control(
			'twitter_carousel_rcc_unique_id',
			[
				'label' => __( 'Unique ID', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter remote carousel unique id', 'happy-addons-pro' ),
                'description' => __('Input carousel ID that you want to remotely connect', 'happy-addons-pro'),
                'condition' => [ 'twitter_carousel_layout_type' => 'remote_carousel' ]
			]
		);

        $this->add_control(
            'credentials',
            [
                'label' => __('Credentials from', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' =>  [
                    'global' => __('Global', 'happy-addons-pro'),
                    'custom' => __('Custom', 'happy-addons-pro'),
                ],
            ]
        );

        $this->add_control(
            'credentials_set_notice',
            [
                'raw' => '<strong>' . esc_html__('Note!', 'happy-addons-pro') . '</strong> ' . esc_html__('Please set credentials in Happy Addons Dashboard - ', 'happy-addons-pro') . '<a style="border-bottom-color: inherit;" href="'. esc_url(admin_url('admin.php?page=happy-addons#credentials')) . '" target="_blank" >'. esc_html__('Credentials', 'happy-addons-pro') .'</a>',
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'render_type' => 'ui',
                'condition' => [
                    'credentials' => 'global',
                ],
            ]
        );

		$this->add_control(
			'user_name',
			[
				'label' => esc_html__('User Name', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => '@HappyAddons',
				'label_block' => false,
				'description' => esc_html__('Use @ sign with your Twitter user name.', 'happy-addons-pro' ),
				'condition' => [
                    'credentials' => 'custom',
                ],

			]
		);

		$this->add_control(
			'consumer_key',
			[
				'label' => esc_html__('Consumer Key', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'eNoxL16kBQYcJ3u6NafUmv6NZ',
				'description' => '<a href="https://apps.twitter.com/app/" target="_blank">Get Consumer Key.</a>',
				'condition' => [
                    'credentials' => 'custom',
                ],
			]
		);

		$this->add_control(
			'consumer_secret',
			[
				'label' => esc_html__('Consumer Secret', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'wnwKqdRkkJzPJ8bZIWPRBKjGYEU4PBWAUYiyShArQQJV6VaPBY',
				'description' => '<a href="https://apps.twitter.com/app/" target="_blank">Get Consumer Secret key.</a>',
				'condition' => [
                    'credentials' => 'custom',
                ],
			]
		);

		$this->end_controls_section();
	}

	protected function __feed_settings_content_controls() {

		$this->start_controls_section(
			'_section_twitter_settings',
			[
				'label' => __('Twitter Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'sort_by',
			[
				'label' => __( 'Sort By', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'recent-posts',
				'options' => [
					'recent-posts' => __( 'Recent Posts', 'happy-addons-pro' ),
					'old-posts' => __( 'Old Posts', 'happy-addons-pro' ),
					'favorite_count' => __( 'Favorite', 'happy-addons-pro' ),
					'retweet_count' => __( 'Retweet', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'tweets_limit',
			[
				'label' => __( 'Number of tweets to show', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 800,
				'step' => 1,
				'default' => 6,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'clear_cash',
			[
				'label' => __( 'Remove Cache', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'show_twitter_logo',
			[
				'label' => __('Show Twitter Logo', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_user_image',
			[
				'label' => __('Show Profile image', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_name',
			[
				'label' => __('Show Name', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_user_name',
			[
				'label' => __('Show User Name', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __('Show Date', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_favorite',
			[
				'label' => __('Show Favorite Count', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_retweet',
			[
				'label' => __('Show Retweets Count', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'read_more',
			[
				'label' => __( 'Show Read More', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => __('Read More Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Read More',
				'condition' => [
					'read_more' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_general_settings',
			[
				'label' => __('Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'ha-twitter-',
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'favorite_retweet_position',
			[
				'label' => __( 'Footer Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-footer' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'user_info_position',
			[
				'label' => __( 'User Info Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-arrow-up',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon' => 'eicon-arrow-down',
					],
				],
				'default' => 'top',
				'toggle' => false,
				'prefix_class' => 'ha-twitter-user-',
				'selectors_dictionary' => [
					'top' => 'flex-direction: column',
					'bottom' => 'flex-direction: column-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-inner-wrapper' => '{{VALUE}};'
				]
			]
		);

		$this->add_control(
			'equal_height',
			[
				'label' => __( 'Equal Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'yes', 'happy-addons-pro' ),
				'label_off' => __( 'no', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'ha-equal-height-',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'link_target',
			[
				'label' => __( 'Link Target', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'_self' => __( 'Open in same window', 'happy-addons-pro' ),
					'_blank' => __( 'Open in new window', 'happy-addons-pro' ),
				],
				'default' => '_blank',
			]
		);

		$this->add_control(
			'_carousel_settings_heading',
			[
				'label' => __( 'Carousel Settings', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label' => __( 'Animation Speed', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 10,
				'max' => 10000,
				'default' => 800,
				'description' => __( 'Slide speed in milliseconds', 'happy-addons-pro' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay?', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 100,
				'max' => 10000,
				'default' => 2000,
				'description' => __( 'Autoplay speed in milliseconds', 'happy-addons-pro' ),
				'condition' => [
					'autoplay' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop?', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'happy-addons-pro' ),
					'arrow' => __( 'Arrow', 'happy-addons-pro' ),
					'dots' => __( 'Dots', 'happy-addons-pro' ),
					'both' => __( 'Arrow & Dots', 'happy-addons-pro' ),
				],
				'default' => 'arrow',
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => __( 'Slides To Show', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					1 => __( '1 Slide', 'happy-addons-pro' ),
					2 => __( '2 Slides', 'happy-addons-pro' ),
					3 => __( '3 Slides', 'happy-addons-pro' ),
					4 => __( '4 Slides', 'happy-addons-pro' ),
					5 => __( '5 Slides', 'happy-addons-pro' ),
					6 => __( '6 Slides', 'happy-addons-pro' ),
				],
				'desktop_default' => 3,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__user_info_style_controls();
		$this->__content_footer_style_controls();
		$this->__arrow_style_controls();
		$this->__dots_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_twitter_style',
			[
				'label' => __( 'Common', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Spacing between Tweets', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'items_border',
				'selector' => '{{WRAPPER}} .ha-tweet-item',
			]
		);

		$this->add_responsive_control(
			'items_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'items_box_shadow',
				'selector' => '{{WRAPPER}} .ha-tweet-item'
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-tweet-item',
			]
		);

		$this->add_control(
			'item_background_overlay',
			[
				'label' => __( 'Background Overlay', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'item_background_background' => 'classic'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-item:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_glassy_effect',
			[
				'label' => __('Content Glassy Effect', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'item_background_background' => 'classic'
				],
				'prefix_class' => 'ha-tweet-glassy-',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function __user_info_style_controls() {

		$this->start_controls_section(
			'_section_twitter_user_info',
			[
				'label' => __( 'User Info', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'user_info_spacing',
			[
				'label' => __( 'User Info Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}.ha-twitter-user-top .ha-tweet-author' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-twitter-user-bottom .ha-tweet-author' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'twitter_logo_heading',
			[
				'label' => __( 'Twitter Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'twitter_icon_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_twitter_logo' => ''
				],
				'raw' => __( 'Twitter Icon is hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'twitter_logo_icon_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'show_twitter_logo' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweeter-feed-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'twitter_logo_icon_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_twitter_logo' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweeter-feed-icon i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'profile_image_heading',
			[
				'label' => __( 'Profile Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'profile_image_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_user_image' => ''
				],
				'raw' => __( 'Profile Image is hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'profile_image_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'show_user_image' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'profile_image_spacing',
			[
				'label' => __( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'show_user_image' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}}.ha-twitter-left .ha-tweet-avatar' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-twitter-center .ha-tweet-avatar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-twitter-right .ha-tweet-avatar' => 'margin-left: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'profile_image_border',
				'selector' => '{{WRAPPER}} .ha-tweet-avatar',
				'condition' => [
					'show_user_image' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'profile_image_box_shadow',
				'selector' => '{{WRAPPER}} .ha-tweet-avatar',
				'condition' => [
					'show_user_image' => 'yes'
				],
			]
		);

		$this->add_control(
			'name_heading',
			[
				'label' => __( 'Name & User Name', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'name_username_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_name' => '',
					'show_user_name' => ''
				],
				'raw' => __( 'Name and UserName both are hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'label' => __( 'Name Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tweet-author-name',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'show_name' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'user_name_typography',
				'label' => __( 'User Name Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tweet-username',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'show_user_name' => 'yes'
				],
			]
		);

		$this->start_controls_tabs(
			'_tabs_name_username',
			[
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'show_name',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'show_user_name',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);
		$this->start_controls_tab(
			'_tab_name_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Name Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_name' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-author-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'user_name_color',
			[
				'label' => __( 'User Name Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_user_name' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-username' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'name_hover_color',
			[
				'label' => __( 'Name Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_name' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-author-name:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'user_name_hover_color',
			[
				'label' => __( 'User Name Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_user_name' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-username:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __content_footer_style_controls() {

		$this->start_controls_section(
			'_section_twitter_content',
			[
				'label' => __('Content & Footer', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content' => 'padding: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->start_controls_tabs( '_tabs_content' );

		$this->start_controls_tab(
			'_tab_content',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tweet-content p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_read_more',
			[
				'label' => __( 'Read More', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'read_more_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'read_more' => ''
				],
				'raw' => __( 'Read More is hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tweet-content p a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'read_more' => 'yes'
				],
			]
		);

		$this->add_control(
			'read_more_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'read_more' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label' => __( 'Hover Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'read_more' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_date',
			[
				'label' => __( 'Date', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'date_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_date' => ''
				],
				'raw' => __( 'Date is hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tweet-date',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'show_date' => 'yes'
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_date' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-date' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'footer_heading',
			[
				'label' => __( 'Footer - Favorite & Retweet', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'favorite_retweet_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'show_favorite' => '',
					'show_retweet' => '',
				],
				'raw' => __( 'Favorite and Retweet both are hidden from <strong>Twitter Settings</strong> section.', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'favorite_retweet_spacing',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'show_favorite' => 'yes',
					'show_retweet' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-favorite' => 'margin-right: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'favorite_retweet_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-favorite' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-tweet-retweet' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'favorite_retweet_icon_color',
			[
				'label' => __( 'Icon Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-favorite i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-tweet-retweet i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __arrow_style_controls() {

		$this->start_controls_section(
			'_section_style_arrow',
			[
				'label' => __( 'Navigation - Arrow', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_position_toggle',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrow_sync_position',
			[
				'label' => __( 'Sync Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'yes' => [
						'title' => __( 'Yes', 'happy-addons-pro' ),
						'icon' => 'eicon-sync',
					],
					'no' => [
						'title' => __( 'No', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-stretch',
					]
				],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'default' => 'no',
				'toggle' => false,
				'prefix_class' => 'ha-arrow-sync-'
			]
		);

		$this->add_control(
			'sync_position_alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'condition' => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position' => 'yes'
				],
				'default' => 'center',
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'left: 0',
					'center' => 'left: 50%',
					'right' => 'left: 100%',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => '{{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label' => __( 'Vertical', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_x',
			[
				'label' => __( 'Horizontal', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-arrow-sync-no .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-no .slick-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next, {{WRAPPER}}.ha-arrow-sync-yes .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_spacing',
			[
				'label' => __( 'Space between Arrows', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Background Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 70,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_arrow' );

		$this->start_controls_tab(
			'_tab_arrow_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'arrow_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __dots_style_controls() {

		$this->start_controls_section(
			'_section_style_dots',
			[
				'label' => __( 'Navigation - Dots', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'dots_nav_position_y',
			[
				'label' => __( 'Vertical Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_spacing',
			[
				'label' => __( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->start_controls_tabs( '_tabs_dots' );
		$this->start_controls_tab(
			'_tab_dots_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'dots_nav_active_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_active_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="ha-tweeter-feed-wrapper">
			<?php $this->twitter_feed_render($this->get_id(), $settings); ?>
		</div>
		<?php
	}

	protected function twitter_feed_render( $id, $settings ) {
		$ha_tweets_token = '_' . $id . '_tweet_token';
		$ha_tweets_cash = '_' . $id . '_tweet_cash';

		$messages = [];

		if( 'global' == $settings['credentials'] && is_array( ha_get_credentials('twitter_feed') ) ){
			$credentials = ha_get_credentials('twitter_feed');
			$user_name = trim($credentials['user_name']);
			$consumer_key = $credentials['consumer_key'];
			$consumer_secret = $credentials['consumer_secret'];
		}else {
			$user_name = trim($settings['user_name']);
			$consumer_key = $settings['consumer_key'];
			$consumer_secret = $settings['consumer_secret'];
		}

		if ( empty( $user_name ) ) {
			$messages[] = __( 'Add user Name', 'happy-addons-pro' );
		} elseif ( empty( $consumer_key ) ) {
			$messages[] = __( 'Add Consumer Key', 'happy-addons-pro' );
		} elseif ( empty( $consumer_secret ) ) {
			$messages[] = __( 'Add Consumer Secret Key', 'happy-addons-pro' );
		}

		if ( !empty( $messages ) ) {
			printf('<div class="ha-tweet-error-message">%1$s</div>', esc_html( $messages[0] ) );
			return;
		}

		$transient_key = $user_name . $ha_tweets_cash;
		$twitter_data = get_transient($transient_key);
		$credentials = base64_encode($consumer_key . ':' . $consumer_secret);

		if ( $twitter_data === false ) {
			$auth_url = 'https://api.twitter.com/oauth2/token';
			$auth_response = wp_remote_post( $auth_url,
				[
					'method' => 'POST',
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => [
						'Authorization' => 'Basic ' . $credentials,
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
					],
					'body' => ['grant_type' => 'client_credentials'],
				]
			);
			$body = json_decode( wp_remote_retrieve_body( $auth_response ) );

			if ( !empty( $body ) ) {
				$token = $body->access_token;

				$twitter_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $user_name . '&count=999&tweet_mode=extended';
				$tweets_response = wp_remote_get( $twitter_url,
					array(
						'httpversion' => '1.1',
						'blocking' => true,
						'headers' => [ 'Authorization' => "Bearer $token", ],
					) );

				$twitter_data = json_decode( wp_remote_retrieve_body( $tweets_response ), true );
				set_transient( $transient_key, $twitter_data, 0 );
			}

		}
		if ( $settings['clear_cash'] == 'yes' ) {
			delete_transient( $transient_key );
		}

		if ( !empty( $twitter_data ) && !array_key_exists( 'errors', $twitter_data ) && count( $twitter_data ) < $settings['tweets_limit'] ) {
			$messages[] = __( ' "Number of Tweets to show" is more than your actual total Tweets\'s number. You have only ' . count( $twitter_data ) . ' Tweets', 'happy-addons-pro' );
		}
		if ( !empty( $twitter_data ) ) {
			if ( array_key_exists( 'errors', $twitter_data ) ) {
				foreach ( $twitter_data['errors'] as $error ) {
					$messages[] = $error['message'];
				}
			}
		}
		if ( empty( $twitter_data ) ) {
			$messages[] = __( 'Nothing Found', 'happy-addons-pro' );
		}

		if ( !empty( $messages ) ) {
			printf('<div class="ha-tweet-error-message">%1$s</div>', esc_html( $messages[0] ) );
			return;
		}

		switch ($settings['sort_by']) {
			case 'old-posts':
				usort($twitter_data, function ($a,$b) {
					if ( strtotime($a['created_at']) == strtotime($b['created_at']) ) return 0;
					return ( strtotime($a['created_at']) < strtotime($b['created_at']) ? -1 : 1 );
				});
				break;
			case 'favorite_count':
				usort($twitter_data, function ($a,$b){
					if ($a['favorite_count'] == $b['favorite_count']) return 0;
					return ($a['favorite_count'] > $b['favorite_count']) ? -1 : 1 ;
				});
				break;
			case 'retweet_count':
				usort($twitter_data, function ($a,$b){
					if ($a['retweet_count'] == $b['retweet_count']) return 0;
					return ($a['retweet_count'] > $b['retweet_count']) ? -1 : 1 ;
				});
				break;
			default:
				$twitter_data;
		}

		if ( !empty( $settings['tweets_limit'] ) && count( $twitter_data ) > $settings['tweets_limit'] ) {
			$items = array_splice($twitter_data, 0, $settings['tweets_limit'] );
		}
		if ( empty( $settings['tweets_limit'] ) ) {
			$items = $twitter_data;
		}
		$link_target = 'target="_blank"';
		if ( !empty( $settings['link_target'] ) && '_self' == $settings['link_target'] ) {
			$link_target = 'target="_self"';
		}

		$harcc_uid = !empty($settings['twitter_carousel_rcc_unique_id']) && $settings['twitter_carousel_layout_type'] == 'remote_carousel' ? 'harccuid_' . $settings['twitter_carousel_rcc_unique_id'] : '';
		?>
		<div data-ha_rcc_uid="<?php echo esc_attr( $harcc_uid ); ?>" class="ha-tweet-carousel-items">
			<?php foreach ( $items as $item ) : ?>

				<div class="ha-tweet-item-wrap">
					<div class="ha-tweet-item">

						<?php if ( $settings['show_twitter_logo'] == 'yes' ) : ?>
							<div class="ha-tweeter-feed-icon">
								<i class="fa fa-twitter"></i>
							</div>
						<?php endif; ?>

						<div class="ha-tweet-inner-wrapper">

							<div class="ha-tweet-author">
								<?php if ( $settings['show_user_image'] == 'yes' ) : ?>
									<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>" <?php echo $link_target;?>>
										<img
											src="<?php echo esc_url( $item['user']['profile_image_url_https'] ); ?>"
											alt="<?php echo esc_attr( $item['user']['name'] ); ?>"
											class="ha-tweet-avatar"
										>
									</a>
								<?php endif; ?>

								<div class="ha-tweet-user">
									<?php if ( $settings['show_name'] == 'yes' ) : ?>
										<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>" class="ha-tweet-author-name" <?php echo $link_target;?>>
											<?php echo esc_html( $item['user']['name'] ); ?>
										</a>
									<?php endif; ?>

									<?php if ( $settings['show_user_name'] == 'yes' ) : ?>
										<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>" class="ha-tweet-username" <?php echo $link_target;?>>
											<?php echo esc_html( $settings['user_name'] ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>

							<div class="ha-tweet-content">
								<?php
								if ( !empty( $item['entities']['urls'] ) ) {
									$content = str_replace( $item['entities']['urls'][0]['url'], '', $item['full_text'] );
								} else {
									$content = $item['full_text'];
								}
								?>
								<p>
									<?php echo esc_html( $content ); ?>

									<?php if ( $settings['read_more'] == 'yes' ) : ?>
										<a href="<?php echo esc_url( '//twitter.com/' . $item['user']['screen_name'] . '/status/' . $item['id'] ); ?>" <?php echo $link_target;?>>
											<?php echo esc_html( $settings['read_more_text'] ); ?>
										</a>
									<?php endif; ?>
								</p>

								<?php if ( $settings['show_date'] == 'yes' ) : ?>
									<div class="ha-tweet-date">
										<?php echo esc_html( date("M d Y", strtotime( $item['created_at'] ) ) ); ?>
									</div>
								<?php endif; ?>
							</div>

						</div>

						<?php if ( $settings['show_favorite'] == 'yes' || $settings['show_retweet'] == 'yes' ) : ?>
							<div class="ha-tweet-footer-wrapper">
								<div class="ha-tweet-footer">

									<?php if ( $settings['show_favorite'] == 'yes' ) : ?>
										<div class="ha-tweet-favorite">
											<?php echo esc_html( $item['favorite_count'] ); ?>
											<i class="fa fa-heart-o"></i>
										</div>
									<?php endif; ?>

									<?php if ( $settings['show_retweet'] == 'yes' ) : ?>
										<div class="ha-tweet-retweet">
											<?php echo esc_html( $item['retweet_count'] ); ?>
											<i class="fa fa-retweet"></i>
										</div>
									<?php endif; ?>

								</div>
							</div>
						<?php endif; ?>

					</div>
				</div>

			<?php endforeach; ?>
		</div>

	<?php
	}

}
