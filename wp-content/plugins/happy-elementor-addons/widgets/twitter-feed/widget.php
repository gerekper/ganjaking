<?php
/**
 * Twitter Feed
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

defined('ABSPATH') || die();

class Twitter_Feed extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Twitter Feed', 'happy-elementor-addons');
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
		return 'hm hm-twitter-feed';
	}

	public function get_keywords() {
		return ['twitter-feed', 'twitter', 'feed', 'social media'];
	}



	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__twitter_content_controls();
		$this->__twitter_settings_content_controls();
		$this->__general_settings_content_controls();
	}

	protected function __twitter_content_controls() {

		$this->start_controls_section(
			'_section_twitter',
			[
				'label' => __( 'Twitter Feed', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'credentials',
            [
                'label' => __('Credentials from', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' =>  [
                    'global' => __('Global', 'happy-elementor-addons'),
                    'custom' => __('Custom', 'happy-elementor-addons'),
                ],
            ]
        );

        $this->add_control(
            'credentials_set_notice',
            [
                'raw' => '<strong>' . esc_html__('Note!', 'happy-elementor-addons') . '</strong> ' . esc_html__('Please set credentials in Happy Addons Dashboard - ', 'happy-elementor-addons') . '<a style="border-bottom-color: inherit;" href="'. esc_url(admin_url('admin.php?page=happy-addons#credentials')) . '" target="_blank" >'. esc_html__('Credentials', 'happy-elementor-addons') .'</a>',
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
				'label' => esc_html__('User Name', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXT,
				'default' => '@HappyAddons',
				'label_block' => false,
				'description' => esc_html__('Use @ sign with your Twitter user name.', 'happy-elementor-addons' ),
                'condition' => [
                    'credentials' => 'custom',
                ],

			]
		);

		$this->add_control(
			'consumer_key',
			[
				'label' => esc_html__('Consumer Key', 'happy-elementor-addons' ),
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
				'label' => esc_html__('Consumer Secret', 'happy-elementor-addons' ),
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

	protected function __twitter_settings_content_controls() {

		$this->start_controls_section(
			'_section_twitter_settings',
			[
				'label' => __('Twitter Settings', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'sort_by',
			[
				'label' => __( 'Sort By', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'recent-posts',
				'options' => [
					'recent-posts' => __( 'Recent Posts', 'happy-elementor-addons' ),
					'old-posts' => __( 'Old Posts', 'happy-elementor-addons' ),
					'favorite_count' => __( 'Favorite', 'happy-elementor-addons' ),
					'retweet_count' => __( 'Retweet', 'happy-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'tweets_limit',
			[
				'label' => __( 'Number of tweets to show', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 800,
				'step' => 1,
				'default' => 6,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'remove_cache',
			[
				'label' => __('Remove Cache', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Column Number', 'happy-elementor-addons'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'desktop_default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => __( '1 Column', 'happy-elementor-addons' ),
					'2' => __( '2 Column', 'happy-elementor-addons' ),
					'3' => __( '3 Column', 'happy-elementor-addons' ),
					'4' => __( '4 Column', 'happy-elementor-addons' ),
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ha-tweet-items' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					'(tablet){{WRAPPER}} .ha-tweet-items' => 'grid-template-columns: repeat({{columns_tablet.VALUE || 0}}, 1fr);',
					'(mobile){{WRAPPER}} .ha-tweet-items' => 'grid-template-columns: repeat({{columns_mobile.VALUE || 0}}, 1fr);'
				]
			]
		);

		$this->add_control(
			'show_twitter_logo',
			[
				'label' => __('Show Twitter Logo', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_user_image',
			[
				'label' => __('Show Profile image', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_name',
			[
				'label' => __('Show Name', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_user_name',
			[
				'label' => __('Show User Name', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __('Show Date Time', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_favorite',
			[
				'label' => __('Show Favorite Count', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_retweet',
			[
				'label' => __('Show Retweets Count', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'read_more',
			[
				'label' => __('Read More', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => __('Read More Text', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Read More',
				'condition' => [
					'read_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_word_count',
			[
				'label' => __( 'Content Word Count', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'max' => 500,
				'default' => 15,
			]
		);

		$this->add_control(
			'load_more',
			[
				'label' => __('Load More Button', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label' => __('Load More Text', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Load More',
				'condition' => [
					'load_more' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __general_settings_content_controls() {

		$this->start_controls_section(
			'_section_general_settings',
			[
				'label' => __('General Settings', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
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
				'label' => __( 'Footer Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
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
			'button_alignment',
			[
				'label' => __( 'Button Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more-wrapper' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'user_info_position',
			[
				'label' => __( 'User Info Position', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'happy-elementor-addons' ),
						'icon' => 'eicon-arrow-up',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-elementor-addons' ),
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
			'link_target',
			[
				'label' => __( 'Link Target', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'_self' => __( 'Open in same window', 'happy-elementor-addons' ),
					'_blank' => __( 'Open in new window', 'happy-elementor-addons' ),
				],
				'default' => '_blank',
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
		$this->__content_style_controls();
		$this->__footer_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_twitter_style',
			[
				'label' => __( 'Common', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Spacing between Tweets', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-items' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-inner-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
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
				'label' => __( 'Background Overlay', 'happy-elementor-addons' ),
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
				'label' => __('Content Glassy Effect', 'happy-elementor-addons'),
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
				'label' => __( 'User Info', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'user_info_spacing',
			[
				'label' => __( 'User Info Spacing', 'happy-elementor-addons' ),
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
				'label' => __( 'Twitter Icon', 'happy-elementor-addons' ),
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
				'raw' => __( 'Twitter Icon is hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_responsive_control(
			'twitter_logo_icon_size',
			[
				'label' => __( 'Size', 'happy-elementor-addons' ),
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
				'label' => __( 'Color', 'happy-elementor-addons' ),
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
				'label' => __( 'Profile Image', 'happy-elementor-addons' ),
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
				'raw' => __( 'Profile Image is hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_responsive_control(
			'profile_image_size',
			[
				'label' => __( 'Size', 'happy-elementor-addons' ),
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
				'label' => __( 'Spacing', 'happy-elementor-addons' ),
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
				'label' => __( 'Name & User Name', 'happy-elementor-addons' ),
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
				'raw' => __( 'Name and UserName both are hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'label' => __( 'Name Typography', 'happy-elementor-addons' ),
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
				'label' => __( 'User Name Typography', 'happy-elementor-addons' ),
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
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Name Color', 'happy-elementor-addons' ),
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
				'label' => __( 'User Name Color', 'happy-elementor-addons' ),
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
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'name_hover_color',
			[
				'label' => __( 'Name Color', 'happy-elementor-addons' ),
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
				'label' => __( 'User Name Color', 'happy-elementor-addons' ),
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

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_twitter_content',
			[
				'label' => __('Content', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content' => 'padding: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label' => __( 'Description', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
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
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-tweet-content p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'read_more_heading',
			[
				'label' => __( 'Read More', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
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
				'raw' => __( 'Read More is hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
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
				'label' => __( 'Color', 'happy-elementor-addons' ),
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
				'label' => __( 'Hover Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'read_more' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-content p a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'date_heading',
			[
				'label' => __( 'Date Time', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
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
				'raw' => __( 'Date is hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
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
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_date' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-date' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __footer_style_controls() {

		$this->start_controls_section(
			'_section_twitter_footer_button',
			[
				'label' => __( 'Footer & Button', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
				'raw' => __( 'Favorite and Retweet both are hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'favorite_retweet_heading',
			[
				'label' => __( 'Favorite & Retweet', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING
			]
		);

		$this->add_responsive_control(
			'favorite_retweet_spacing',
			[
				'label' => __( 'Space Between', 'happy-elementor-addons' ),
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
				'label' => __( 'Color', 'happy-elementor-addons' ),
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
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tweet-favorite i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-tweet-retweet i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label' => __( 'Load More Button', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'button_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'load_more' => ''
				],
				'raw' => __( 'Button is hidden from <strong>Twitter Settings</strong> section.', 'happy-elementor-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'condition' => [
					'load_more' => 'yes'
				],
				'selector' => '{{WRAPPER}} .ha-twitter-load-more',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition' => [
					'load_more' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'condition' => [
					'load_more' => 'yes'
				],
				'selector' => '{{WRAPPER}} .ha-twitter-load-more'
			]
		);

		$this->start_controls_tabs(
			'_tabs_button',
			[
				'condition' => [
					'load_more' => 'yes'
				],
			]
		);
		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more' => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label' => __('Background Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more:hover' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label' => __('Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more:hover' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'button_border_hover_color',
			[
				'label' => __('Border Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-twitter-load-more:hover' => 'border-color: {{VALUE}};'
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
			$messages[] = __( 'Add user Name', 'happy-elementor-addons' );
		} elseif ( empty( $consumer_key ) ) {
			$messages[] = __( 'Add Consumer Key', 'happy-elementor-addons' );
		} elseif ( empty( $consumer_secret ) ) {
			$messages[] = __( 'Add Consumer Secret Key', 'happy-elementor-addons' );
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
				array(
					'method' => 'POST',
					'httpversion' => '1.1',
					'blocking' => true,
					'headers' => [
						'Authorization' => 'Basic ' . $credentials,
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
					],
					'body' => ['grant_type' => 'client_credentials'],
				) );

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
		if ( $settings['remove_cache'] == 'yes' ) {
			delete_transient( $transient_key );
		}

		if ( !empty( $twitter_data ) && !array_key_exists( 'errors', $twitter_data ) && count( $twitter_data ) < $settings['tweets_limit'] ) {
			$messages[] = __( ' "Number of Tweets to show"  is more than your actual total Tweets\'s number. You have only ' . count( $twitter_data ) . ' Tweets', 'happy-elementor-addons' );
		}
		if ( !empty( $twitter_data ) ) {
			if ( array_key_exists( 'errors', $twitter_data ) ) {
				foreach ( $twitter_data['errors'] as $error ) {
					$messages[] = $error['message'];
				}
			}
		}
		if ( empty( $twitter_data ) ) {
			$messages[] = __( 'Nothing Found', 'happy-elementor-addons' );
		}

		if ( !empty( $messages ) ) {
			printf('<div class="ha-tweet-error-message">%1$s</div>', esc_html( $messages[0] ) );
			return;
		}

		$query_settings = [
			'credentials' 			=> $credentials,
			'id' 					=> $id,
			'user_name' 			=> $user_name,
			'remove_cache' 			=> $settings['remove_cache'],
			'sort_by' 				=> $settings['sort_by'],
			'show_twitter_logo' 	=> $settings['show_twitter_logo'],
			'tweets_limit' 			=> $settings['tweets_limit'],
			'show_user_image' 		=> $settings['show_user_image'],
			'show_name' 			=> $settings['show_name'],
			'show_user_name' 		=> $settings['show_user_name'],
			'show_date' 			=> $settings['show_date'],
			'show_favorite' 		=> $settings['show_favorite'],
			'show_retweet' 			=> $settings['show_retweet'],
			'read_more' 			=> $settings['read_more'],
			'read_more_text'		=> $settings['read_more_text'],
			'content_word_count'	=> $settings['content_word_count'],
		];
		$query_settings = json_encode($query_settings, true);

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
		?>
		<div class="ha-tweet-items">
			<?php
			foreach ( $items as $item ) :
				if ( !empty( $item['entities']['urls'] ) ) {
					$content = str_replace( $item['entities']['urls'][0]['url'], '', $item['full_text'] );
				} else {
					$content = $item['full_text'];
				}

				$description = explode( ' ', $content );
				if ( !empty( $settings['content_word_count'] ) && count( $description ) > $settings['content_word_count'] ) {
					$description_shorten = array_slice( $description, 0, $settings['content_word_count'] );
					$description = implode( ' ', $description_shorten ) . '...';
				} else {
					$description = $content;
				}
				?>
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
							<p>
								<?php echo esc_html( $description ); ?>

								<?php if ( $settings['read_more'] == 'yes' ) : ?>
									<a href="<?php echo esc_url( '//twitter.com/' . $item['user']['screen_name'] . '/status/' . $item['id'] ); ?>" <?php echo $link_target;?>>
										<?php echo esc_html( $settings['read_more_text'] ); ?>
									</a>
								<?php endif; ?>
							</p>

							<?php if ( $settings['show_date'] == 'yes' ) : ?>
								<div class="ha-tweet-date">
									<?php echo esc_html( date("M d Y, g:i a", strtotime( $item['created_at'] ) ) ); ?>
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
			<?php endforeach; ?>
		</div>

		<?php if ( $settings['load_more'] == 'yes' ) : ?>
			<div class="ha-twitter-load-more-wrapper">
				<button class="ha-twitter-load-more" data-settings="<?php echo esc_attr( $query_settings ); ?>" data-total="<?php echo esc_attr( count( $twitter_data ) ); ?>">
					<?php echo esc_html( $settings['load_more_text'] ); ?>
				</button>
			</div>
		<?php
		endif;
	}

}
