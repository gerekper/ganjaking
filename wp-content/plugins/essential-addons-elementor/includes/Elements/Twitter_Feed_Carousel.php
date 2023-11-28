<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Classes\Helper;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) {
    exit;
}
// If this file is called directly, abort.

class Twitter_Feed_Carousel extends Widget_Base
{
    use \Essential_Addons_Elementor\Traits\Twitter_Feed;

    public function get_name()
    {
        return 'eael-twitter-feed-carousel';
    }

    public function get_title()
    {
        return esc_html__('Twitter Feed Carousel', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-twitter-feed-carousel';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'twitter feed',
            'ea twitter feed carousel',
            'social media',
            'carousel',
            'twitter marketing',
            'twitter embed',
            'twitter feed slider',
            'ea',
            'essential addons'
        ];
    }

    public function get_style_depends() {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/twitter-feed-carousel/';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_acc_settings',
            [
                'label' => esc_html__('Account Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_twitter_feed_ac_name',
            [
                'label' => esc_html__('Account Name', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '@wpdevteam',
                'label_block' => false,
                'description' => esc_html__('Use @ sign with your account name.', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],

            ]
        );

        $this->add_control(
            'eael_twitter_feed_hashtag_name',
            [
                'label' => esc_html__('Hashtag Name', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'description' => esc_html__('Remove # sign from your hashtag name.', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],

            ]
        );

        $this->add_control(
            'eael_twitter_feed_consumer_key',
            [
                'label' => esc_html__('Consumer Key', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => '',
                'description' => '<a href="https://developer.twitter.com/en/portal/dashboard" target="_blank">Get Consumer Key.</a> Create a new app or select existing app and grab the <b>consumer key.</b>',
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_consumer_secret',
            [
                'label' => esc_html__('Consumer Secret', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => '',
                'description' => '<a href="https://developer.twitter.com/en/portal/dashboard" target="_blank">Get Consumer Secret.</a> Create a new app or select existing app and grab the <b>consumer secret.</b>',
                'ai' => [
					'active' => false,
				],
            ]
        );

	    $this->add_control(
		    'eael_auto_clear_cache',
		    [
			    'label'        => esc_html__( 'Auto Cache Clear', 'essential-addons-elementor' ),
			    'type'         => Controls_Manager::SWITCHER,
			    'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
			    'label_off'    => __( 'No', 'essential-addons-elementor' ),
			    'default'      => 'yes',
			    'return_value' => 'yes',
		    ]
	    );

	    $this->add_control(
		    'eael_twitter_feed_cache_limit',
		    [
			    'label'       => __( 'Data Cache Time', 'essential-addons-elementor' ),
			    'type'        => Controls_Manager::NUMBER,
			    'min'         => 1,
			    'default'     => 60,
			    'description' => __( 'Cache expiration time (Minutes)', 'essential-addons-elementor' ),
			    'condition'   => [
				    'eael_auto_clear_cache' => 'yes'
			    ]
		    ]
	    );

	    $this->add_control(
		    'eael_clear_cache_control',
		    [
			    'label'       => __( 'Clear Cache', 'essential-addons-elementor' ),
			    'type'        => Controls_Manager::BUTTON,
			    'text'        => __( 'Clear', 'essential-addons-elementor' ),
			    'event'       => 'ea:cache:clear',
			    'description' => esc_html__( 'Note: This will refresh your feed and fetch the latest data from your Twitter account', 'essential-addons-elementor' ),
			    'condition'   => [
				    'eael_auto_clear_cache' => ''
			    ]
		    ]
	    );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_layout_settings',
            [
                'label' => esc_html__('Layout Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_twitter_feed_content_length',
            [
                'label' => esc_html__('Content Length', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'label_block' => false,
                'min' => 1,
                'max' => 400,
                'default' => 400,
            ]
        );

        $this->add_control(
            'eael_twitter_feed_post_limit',
            [
                'label' => esc_html__('Post Limit', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'label_block' => false,
                'default' => 10,
            ]
        );
        
        $this->add_control(
            'eael_twitter_feed_show_replies',
            [
                'label' => esc_html__('Show Replies', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_card_settings',
            [
                'label' => esc_html__('Card Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_twitter_feed_show_avatar',
            [
                'label' => esc_html__('Show Avatar', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_avatar_style',
            [
                'label' => __('Avatar Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'circle' => 'Circle',
                    'square' => 'Square',
                ],
                'default' => 'circle',
                'condition' => [
                    'eael_twitter_feed_show_avatar' => 'true',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_show_date',
            [
                'label' => esc_html__('Show Date', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_show_read_more',
            [
                'label' => esc_html__('Show Read More', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_show_icon',
            [
                'label' => esc_html__('Show Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_media',
            [
                'label' => esc_html__('Show Media', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'true',
                'return_value' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_settings',
            [
                'label' => __('Carousel Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'carousel_effect',
            [
                'label' => __('Effect', 'essential-addons-elementor'),
                'description' => __('Sets transition effect', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide', 'essential-addons-elementor'),
                    'fade' => __('Fade', 'essential-addons-elementor'),
                    'cube' => __('Cube', 'essential-addons-elementor'),
                    'coverflow' => __('Coverflow', 'essential-addons-elementor'),
                    'flip' => __('Flip', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_responsive_control(
            'items',
            [
                'label' => __('Visible Items', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 3],
                'tablet_default' => ['size' => 2],
                'mobile_default' => ['size' => 1],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
                'condition' => [
                    'carousel_effect' => ['slide', 'coverflow']
                ]
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label' => __('Items Gap', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 10],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
                'condition' => [
                    'carousel_effect' => ['slide', 'coverflow']
                ]
            ]
        );

        $this->add_control(
            'slider_speed',
            [
                'label' => __('Slider Speed', 'essential-addons-elementor'),
                'description' => __('Duration of transition between slides (in ms)', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 400],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 3000,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay Speed', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => 2000],
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 5000,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __('Pause On Hover', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'navigation_heading',
            [
                'label' => __('Navigation', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'arrows',
            [
                'label' => __('Arrows', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'dots',
            [
                'label' => __('Dots', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Twitter Feed Card Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_card_style_settings',
            [
                'label' => esc_html__('Card Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_twitter_feed_card_choose_style',
            [
                'label'   => __('Choose Style', 'essential-addons-elementor'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''      => __('Default Style', 'essential-addons-elementor'),
                    'two'   => __('Style Two (right icon)', 'essential-addons-elementor'),
                    'three' => __('Style Three', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_card_left_icon_alignment',
            [
                'label'     => __('Left Icon Alignment', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start' => [
                        'title' => __('Top', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'     => [
                        'title' => __('Middle', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'   => [
                        'title' => __('Bottom', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-entry-iconwrap' => 'align-self: {{VALUE}};',
                ],
                'condition' => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_card_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_twitter_feed_card_inner_padding',
            [
                'label'      => esc_html__('Main Card Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_twitter_feed_card_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-twitter-feed-item-content' => 'padding: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_twitter_feed_card_choose_style!' => 'three',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_twitter_feed_card_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item-inner',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item-inner, {{WRAPPER}} .swiper-slide' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_twitter_feed_card_shadow',
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item-inner',
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_left_icon_heading',
            [
                'label'     => __('Left Icon Area', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_twitter_feed_card_item_left_padding',
            [
                'label'      => esc_html__('Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item-style-three .eael-twitter-feed-item-inner .eael-twitter-feed-entry-iconwrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'      => '10',
                    'right'    => '10',
                    'bottom'   => '10',
                    'left'     => '10',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'condition'  => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_right_content_heading',
            [
                'label'     => __('Right Content Area', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_twitter_feed_card_item_right_padding',
            [
                'label'      => esc_html__('Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item-style-three .eael-twitter-feed-item-inner .eael-twitter-feed-entry-contentwrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'eael_twitter_feed_card_item_right_border',
                'label'    => __('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item-style-three .eael-twitter-feed-item-inner .eael-twitter-feed-entry-contentwrap',
                'condition'  => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_twitter_feed_card_item_right_radius',
            [
                'label'      => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item-style-three .eael-twitter-feed-item-inner .eael-twitter-feed-entry-contentwrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_twitter_feed_card_choose_style' => 'three',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Card Hover Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_twitter_feed_card_hover_settings',
            [
                'label' => esc_html__('Card Hover Style', 'essential-addons-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_hover_title_color',
            [
                'label'     => __('Title Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item-inner:hover .eael-twitter-feed-item-author' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_hover_content_color',
            [
                'label'     => __('Content Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item-inner:hover .eael-twitter-feed-item-content p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_hover_link_color',
            [
                'label'     => __('Link Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .eael-twitter-feed-item-inner:hover .eael-twitter-feed-item-content a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_hover_date_color',
            [
                'label'     => __('Date Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .eael-twitter-feed-item-inner:hover .eael-twitter-feed-item-header .eael-twitter-feed-item-date' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_twitter_feed_card_hover_icon_color',
            [
                'label'     => __('Icon Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .eael-twitter-feed-item-inner:hover .eael-twitter-feed-item-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_card_border_hover_color',
            [
                'label'     => __('Border Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .eael-twitter-feed-item-inner:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'eael_twitter_feed_card_hover_bg',
                'label'    => __('Background', 'essential-addons-elementor'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item-inner:hover',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'eael_twitter_feed_card_hover_shadow',
                'label'    => __('Box Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item-inner:hover',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Twitter Feed Typography Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_twitter_feed_carousel_card_typo_settings',
            [
                'label' => esc_html__('Color &amp; Typography', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_twitter_feed_title_heading',
            [
                'label' => esc_html__('Title Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'eael_twitter_feed_title_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-author' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_twitter_feed_title_typography',
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-author',
            ]
        );
        // Content Style
        $this->add_control(
            'eael_twitter_feed_content_heading',
            [
                'label' => esc_html__('Content Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_content_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-content p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_twitter_feed_content_typography',
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-content p',
            ]
        );

        // Content Link Style
        $this->add_control(
            'eael_twitter_feed_content_link_heading',
            [
                'label' => esc_html__('Link Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_twitter_feed_content_link_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-content a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_content_link_hover_color',
            [
                'label' => esc_html__('Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-content a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_twitter_feed_content_link_typography',
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-content a',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label' => __('Arrows', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'arrows' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrow_left',
            [
                'label' => __('Choose Left Arrow', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-angle-left',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'arrow_right',
            [
                'label' => __('Choose Right Arrow', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-angle-right',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_size',
            [
                'label' => __('Arrows Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => ['size' => '22', 'unit'=>'px'],
                'range' => [
                    'px' => [
                        'min' => 15,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .swiper-container .swiper-button-next svg, {{WRAPPER}} .swiper-container .swiper-button-prev svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'left_and_right_arrow_top_position',
            [
                'label' => __('Left & Right Arrow Position From Top', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['%'],
                'default' => [
                    'unit' => '%',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'left_arrow_position',
            [
                'label' => __('Align Left Arrow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 40,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'right_arrow_position',
            [
                'label' => __('Align Right Arrow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 40,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
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
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_normal',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .swiper-container .swiper-button-next svg, {{WRAPPER}} .swiper-container .swiper-button-prev svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrows_border_normal',
                'label' => __('Border', 'essential-addons-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev',
            ]
        );

        $this->add_control(
            'arrows_border_radius_normal',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next:hover, {{WRAPPER}} .swiper-container .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_hover',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next:hover, {{WRAPPER}} .swiper-container .swiper-button-prev:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .swiper-container .swiper-button-next:hover svg, {{WRAPPER}} .swiper-container .swiper-button-prev:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_color_hover',
            [
                'label' => __('Border Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next:hover, {{WRAPPER}} .swiper-container .swiper-button-prev:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'arrows_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-button-next, {{WRAPPER}} .swiper-container .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label' => __('Dots', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'dots' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label' => __('Position', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'absolute' => __('Inside', 'essential-addons-elementor'),
                    'initial' => __('Outside', 'essential-addons-elementor'),
                ],
                'default' => 'initial',
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination' => 'position: {{VALUE}}'
                ]
            ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label' => __('Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 40,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_spacing',
            [
                'label' => __('Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'size_units' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
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
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'active_dot_color_normal',
            [
                'label' => __('Active Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'dots_border_normal',
                'label' => __('Border', 'essential-addons-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .swiper-container .swiper-pagination-bullet',
            ]
        );

        $this->add_control(
            'dots_border_radius_normal',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'allowed_dimensions' => 'vertical',
                'placeholder' => [
                    'top' => '',
                    'right' => 'auto',
                    'bottom' => '',
                    'left' => 'auto',
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullets' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_hover',
            [
                'label' => __('Border Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .swiper-container .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        /**
         * -------------------------------------------
         * Tab Style (avatar style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_twitter_feed_avatar_style',
            [
                'label'     => esc_html__('Avatar', 'essential-addons-elementor'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_twitter_feed_show_avatar' => 'true',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_avatar_width',
            [
                'label'      => __('Width', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 38,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-avatar img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_twitter_feed_avatar_height',
            [
                'label'      => __('Height', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-avatar img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'eael_twitter_feed_avatar_border',
                'label'    => __('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-avatar img',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'eael_twitter_feed_avatar_shadow',
                'label'    => __('Box Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-avatar img',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Icon style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_twitter_feed_icon_style',
            [
                'label'     => esc_html__('Icon', 'essential-addons-elementor'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_twitter_feed_show_icon' => 'true',
                ],
            ]
        );

        $this->add_control(
            'eael_section_twitter_feed_icon_size',
            [
                'label'      => __('Font Size', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_section_twitter_feed_icon_color',
            [
                'label'     => __('Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'global' => [
	                'default' => Global_Colors::COLOR_PRIMARY
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-twitter-feed-item .eael-twitter-feed-item-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
	    $settings    = $this->get_settings_for_display();
	    $render_html = $this->twitter_feed_render_items( $this->get_id(), $settings, 'swiper-slide' );

	    if ( empty( $render_html ) ) {
		    return;
	    }

	    $this->add_render_attribute( 'eael-twitter-feed-carousel-wrap', [
		    'data-items'          => $this->get_settings( 'items' )['size'],
		    'data-items-tablet'   => isset( $this->get_settings( 'items_tablet' )['size'] ) ? $this->get_settings( 'items_tablet' )['size'] : 2,
		    'data-items-mobile'   => isset( $this->get_settings( 'items_mobile' )['size'] ) ? $this->get_settings( 'items_mobile' )['size'] : 1,
		    'data-margin'         => $this->get_settings( 'margin' )['size'],
		    'data-margin-tablet'  => isset( $this->get_settings( 'margin_tablet' )['size'] ) ? $this->get_settings( 'margin_tablet' )['size'] : 10,
		    'data-margin-mobile'  => isset( $this->get_settings( 'margin_mobile' )['size'] ) ? $this->get_settings( 'margin_mobile' )['size'] : 10,
		    'data-effect'         => $settings['carousel_effect'],
		    'data-speed'          => $settings['slider_speed']['size'],
		    'data-autoplay'       => ( $settings['autoplay'] == 'yes' && ! empty( $settings['autoplay_speed']['size'] ) ) ? $settings['autoplay_speed']['size'] : '0',
		    'data-pause-on-hover' => ( $settings['pause_on_hover'] == 'yes' ? 'true' : 'false' ),
		    'data-dots'           => '1',
	    ] );

        $swiper_class = $swiper_version_class = '';
        if ( class_exists( 'Elementor\Plugin' ) ) {
            $swiper_class           = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
            $swiper_version_class   = 'swiper' === $swiper_class ? 'swiper-8' : 'swiper-8-lower';
        }

		echo '<div class="eael-twitter-feed eael-twitter-feed-carousel ' . esc_attr( $swiper_class ) . ' ' . esc_attr( $swiper_version_class ) . ' eael-twitter-feed-' . $this->get_id() . '" ' . $this->get_render_attribute_string('eael-twitter-feed-carousel-wrap') . '>
			<div class="swiper-wrapper">
				' . $render_html . '
			</div>';

        if ($settings['dots'] == 'yes') {
            echo '<div class="swiper-pagination swiper-pagination-' . esc_attr($this->get_id()) . '"></div>';
        }

        if ($settings['arrows'] == 'yes') {

            echo '<div class="swiper-button-next swiper-button-next-' . esc_attr($this->get_id()) . '">
					'. Helper::get_render_icon( $settings['arrow_right'] ) .'
				</div>
				<div class="swiper-button-prev swiper-button-prev-' . esc_attr($this->get_id()) . '">
				    '. Helper::get_render_icon( $settings['arrow_left'] ) .'
				</div>';
        }else{
            echo '<div class="swiper-button-next" style="display:none;"></div>
				<div class="swiper-button-prev" style="display:none;"></div>';
        }
        echo '</div>';
    }
}
