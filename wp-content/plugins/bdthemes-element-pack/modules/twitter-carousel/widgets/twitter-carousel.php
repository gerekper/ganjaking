<?php
namespace ElementPack\Modules\TwitterCarousel\Widgets;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;


use ElementPack\Traits\Global_Swiper_Controls;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Twitter_Carousel extends Module_Base {

	use Global_Swiper_Controls;

	private $_query = null;

	public function get_name() {
		return 'bdt-twitter-carousel';
	}

	public function get_title() {
		return BDTEP . __( 'Twitter Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-twitter-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'twitter', 'carousel' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-twitter-carousel' ];
        }
    }
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return [ 'ep-twitter-carousel' ];
        }
    }

	public function on_import( $element ) {
		if ( ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}
		return $element;
	}

	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/IrQVteaaAow';
	}

	protected function register_controls() {
		$this->register_query_section_controls();
	}

	private function register_query_section_controls() {

		$this->start_controls_section(
			'section_carousel_layout',
			[
				'label' => __( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
			]
		);

		$this->add_control(
			'num_tweets',
			[
				'label'   => __( 'Limit', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'cache_time',
			[
				'label'   => __( 'Cache Time(m)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 60,
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label' => __( 'Show Avatar', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
            'enable_twitter_auth2_api',
            [
                'label' => __('Enable Twitter Auth2 API', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

		$this->add_control(
			'avatar_link',
			[
				'label'     => __( 'Avatar Link', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_avatar' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'   => __( 'Show Time', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'long_time_format',
			[
				'label'     => __( 'Long Time Format', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_time' => 'yes',
				]
			]
		);


		$this->add_control(
			'show_meta_button',
			[
				'label'   => __( 'Execute Buttons', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'exclude_replies',
			[
				'label' => __( 'Exclude Replies', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'strip_emoji',
			[
				'label' => __( 'Strip Emoji', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'match_height',
			[
				'label'   => __( 'Item Match Height', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		//Style
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-twitter-text,
					{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-twitter-text *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'   => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-card-body' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item, {{WRAPPER}} .bdt-twitter-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_gap',
			[
				'label'   => __( 'Item Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'    => '40',
					'bottom' => '40',
					'left'   => '40',
					'right'  => '40',
					'unit'   => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'shadow_mode',
			[
				'label'        => esc_html__( 'Shadow Mode', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-shadow-mode-',
			]
		);

		$this->add_control(
			'shadow_color',
			[
				'label'     => esc_html__( 'Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'shadow_mode' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container:before' => is_rtl() ? 'background: linear-gradient(to left, {{VALUE}} 5%,rgba(255,255,255,0) 100%);' : 'background: linear-gradient(to right, {{VALUE}} 5%,rgba(255,255,255,0) 100%);',
					'{{WRAPPER}} .elementor-widget-container:after'  => is_rtl() ? 'background: linear-gradient(to left, rgba(255,255,255,0) 0%, {{VALUE}} 95%);' : 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
				],
			]
		);

		$this->add_control(
			'item_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover',
			]
		);

		$this->add_responsive_control(
			'item_shadow_padding',
			[
				'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
				'description' => __( 'You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default' => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'item_hover_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_active',
			[
				'label' => __( 'Active', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);

		$this->add_control(
			'item_active_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_active_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active',
			]
		);

		$this->add_control(
			'item_active_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			[
				'label'     => __( 'Avatar', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'avatar_width',
			[
				'label' => __( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 48,
						'min' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'avatar_align',
			[
				'label'   => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'avatar_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'avatar_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper, {{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'avatar_opacity',
			[
				'label'   => __( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'avatar_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'      => 'avatar_css_filters',
                'selector'  => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
            ]
        );

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => __( 'Execute Buttons', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-button > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-button > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => __( 'Time', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-wrapper a.bdt-twitter-time-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'time_hover_color',
			[
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-wrapper a.bdt-twitter-time-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel');

		$this->end_controls_section();

	}

	public function getTwitterAuth2Data($consumerKey, $consumerSecret, $username) {

        $access_token = get_option('elementpack_twitter_access_token_' . $username);

        if ( !$access_token ) {
            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
            $response    = wp_remote_post('https://api.twitter.com/oauth2/token', [
                'method'      => 'POST',
                'httpversion' => '1.1',
                'sslverify'   => false,
                'blocking'    => true,
                'headers'     => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
                ],
                'body'        => ['grant_type' => 'client_credentials'],
            ]);

            $body = json_decode(wp_remote_retrieve_body($response));

            if ( $body && isset($body->access_token) ) {
                update_option('elementpack_twitter_access_token_' . $username, $body->access_token);
                $access_token = $body->access_token;
            }
        }

        $response = wp_remote_get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $username . '&count=999&tweet_mode=extended', [
            'httpversion' => '1.1',
            'blocking'    => true,
            'sslverify'   => false,
            'headers'     => [
                'Authorization' => "Bearer $access_token",
            ],
        ]);

        if ( $response['response']['code'] == 200 && !empty($response['response']) ) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }
    }

	public function getTwitterAuth1Data($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name) {
        $connection = new \TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

        $settings        = $this->get_settings_for_display();
        $exclude_replies = ('yes' === $settings['exclude_replies']) ? true : false;

        // If excluding replies, we need to fetch more than requested as the
        // total is fetched first, and then replies removed.
        $totalToFetch = ($exclude_replies) ? max(50, $settings['num_tweets'] * 3) : $settings['num_tweets'];

        $fetchedTweets = $connection->get(
            'statuses/user_timeline',
            array(
                'screen_name' => $twitter_name,
                'count'       => $totalToFetch,
            )
        );

        if ( $connection->http_code == 200 ) {
            return $fetchedTweets;
        }
    }

	public function render_loop_twitter( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		$settings          = $this->get_settings_for_display();
		$isEnableAuth2 = isset($settings['enable_twitter_auth2_api']) && $settings['enable_twitter_auth2_api'] == 'yes';
		$name              = $twitter_name;

		$tweets        = [];
        $fetchedTweets = [];
		$exclude_replies   = ('yes' === $settings['exclude_replies'] ) ? true : false;
		$transName         = 'bdt-tweets-'.$name; // Name of value in database. [added $name for multiple account use]
		$backupName        = $transName . '-backup'; // Name of backup value in database.

		if ( $isEnableAuth2 ) {
            $transName  = 'bdt-tweets-auth2-' . $name;
            $backupName = $transName . '-backup';
        }

		if ( $isEnableAuth2 ) {
            if ( !get_transient($name) ) {
                $fetchedTweets = $this->getTwitterAuth2Data($consumerKey, $consumerSecret, $twitter_name);
                if ( $fetchedTweets ) {
                    $fetchedTweets = json_decode(json_encode($fetchedTweets)); // convert array to json recursively.
                }
            }

        } else {
            if ( !get_transient($name) ) {
                $fetchedTweets = $this->getTwitterAuth1Data($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name);
            }
        }

		// Did the fetch fail?
        if ( !$fetchedTweets ) :
            $tweets = get_option($backupName); // False if there has never been data saved.
        else :
            // Fetch succeeded.
            // Now update the array to store just what we need.
            // (Done here instead of PHP doing this for every page load)
            $limitToDisplay = min($settings['num_tweets'], count($fetchedTweets));

            for ( $i = 0; $i < $limitToDisplay; $i++ ) :
                $tweet = $fetchedTweets[$i];

                // Core info.
                $name = $tweet->user->name;
                // COMMUNITY REQUEST !!!!!! (2)
                $screen_name = $tweet->user->screen_name;
                $permalink   = 'https://twitter.com/' . $screen_name . '/status/' . $tweet->id_str;
                $tweet_id    = $tweet->id_str;

                /* Alternative image sizes method: http://dev.twitter.com/doc/get/users/profile_image/:screen_name */
                //  Check for SSL via protocol https then display relevant image - thanks SO - this should do
                if ( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
                    // $protocol = 'https://';
                    $image = $tweet->user->profile_image_url_https;
                } else {
                    // $protocol = 'http://';
                    $image = $tweet->user->profile_image_url;
                }

                // Process Tweets - Use Twitter entities for correct URL, hash and mentions
                $text = $this->process_links($tweet);
                // lets strip 4-byte emojis
                if ( $settings['strip_emoji'] == 'yes' ) {
                    $text = $this->twitter_api_strip_emoji($text);
                }

                // Need to get time in Unix format.
                $time  = $tweet->created_at;
                $time  = date_parse($time);
                $uTime = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);

                // Now make the new array.
                $tweets[] = array(
                    'text'      => $text,
                    'name'      => $name,
                    'permalink' => $permalink,
                    'image'     => $image,
                    'time'      => $uTime,
                    'tweet_id'  => $tweet_id
                );
            endfor;

            set_transient($transName, $tweets, 60 * $settings['cache_time']);
            update_option($backupName, $tweets);
        endif;

		?>

		<?php

		// Now display the tweets, if we can.
		if($tweets) : ?>
			<?php foreach( (array) $tweets as $t) : // casting array to array just in case it's empty - then prevents PHP warning ?>
					<div class="bdt-carousel-item swiper-slide">
						<div class="bdt-card">
							<div class="bdt-card-body">
								<?php if ('yes' === $settings['show_avatar']) : ?>

									<?php if ('yes' === $settings['avatar_link']) : ?>
										<a href="https://twitter.com/<?php echo esc_attr( $name ); ?>">
									<?php endif; ?>
										<div class="bdt-twitter-thumb">
											<div class="bdt-twitter-thumb-wrapper">
												<img src="<?php echo esc_url($t['image']); ?>" alt="<?php echo esc_html($t['name']); ?>" />
											</div>
										</div>
									<?php if ('yes' === $settings['avatar_link']) : ?>
										</a>
									<?php endif; ?>

								<?php endif; ?>

								<div class="bdt-twitter-text bdt-clearfix">
									<?php echo wp_kses_post($t['text']); ?>
								</div>

								<div class="bdt-twitter-meta-wrapper">

									<?php if('yes' === $settings['show_time']) : ?>
									<a href="<?php echo esc_url($t['permalink']); ?>" target="_blank" class="bdt-twitter-time-link">
										<?php
											// Original - long time ref: hours...
											if('yes' === $settings['long_time_format']){
												// New - short Twitter style time ref: h...
												$timeDisplay = human_time_diff($t['time'], current_time('timestamp'));
											} else {
												$timeDisplay = $this->twitter_time_diff($t['time'], current_time('timestamp'));
											}
											$displayAgo = _x('ago', 'leading space is required', 'bdthemes-element-pack');
											// Use to make il8n compliant
											printf(__( '%1$s %2$s', 'bdthemes-element-pack' ), $timeDisplay, $displayAgo);
										?>
									</a>
									<?php endif; ?>


									<?php if ('yes' === $settings['show_meta_button']) : ?>
									<div class="bdt-twitter-meta-button">
										<a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_url($t['tweet_id']); ?>" data-lang="en" class="bdt-tmb-reply" title="<?php _e('Reply','bdthemes-element-pack'); ?>" target="_blank">
											<i class="ep-icon-reply" aria-hidden="true"></i>
										</a>
										<a href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_url($t['tweet_id']); ?>" data-lang="en" class="bdt-tmb-retweet" title="<?php _e('Retweet','bdthemes-element-pack'); ?>" target="_blank">
											<i class="ep-icon-refresh" aria-hidden="true"></i>
										</a>
										<a href="https://twitter.com/intent/favorite?tweet_id=<?php echo esc_url($t['tweet_id']); ?>" data-lang="en" class="bdt-tmb-favorite" title="<?php _e('Favourite','bdthemes-element-pack'); ?>" target="_blank">
											<i class="ep-icon-star" aria-hidden="true"></i>
										</a>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
			<?php endforeach; ?>
		<?php endif;
	}

	public function render() {

		if ( ! class_exists('TwitterOAuth') ) {
			include BDTEP_PATH . 'includes/twitteroauth/twitteroauth.php';
		}

		$settings          = $this->get_settings_for_display();
		$options           = get_option( 'element_pack_api_settings' );

		$consumerKey       = (!empty($options['twitter_consumer_key'])) ? $options['twitter_consumer_key'] : '';
		$consumerSecret    = (!empty($options['twitter_consumer_secret'])) ? $options['twitter_consumer_secret'] : '';
		$accessToken       = (!empty($options['twitter_access_token'])) ? $options['twitter_access_token'] : '';
		$accessTokenSecret = (!empty($options['twitter_access_token_secret'])) ? $options['twitter_access_token_secret'] : '';
		$twitter_name      = (!empty($options['twitter_name'])) ? $options['twitter_name'] : '';

		$this->render_loop_header();

		if ( $consumerKey and $consumerSecret and $accessToken and $accessTokenSecret  ) {
			$this->render_loop_twitter( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name );
		} else {
			?>
			<div class="bdt-alert-warning" bdt-alert>
			    <a class="bdt-alert-close" bdt-close></a>
			    <?php $ep_setting_url = esc_url( admin_url('admin.php?page=element_pack_options#element_pack_api_settings')); ?>
			    <p><?php printf(__( 'Please set your twitter API settings from here <a href="%s">element pack settings</a> to show your map correctly.', 'bdthemes-element-pack' ), $ep_setting_url); ?></p>
			</div>
			<?php
		}

		$this->render_footer();

	}

	private function twitter_api_strip_emoji( $text ){
		// four byte utf8: 11110www 10xxxxxx 10yyyyyy 10zzzzzz
		return preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/', '', $text );
	}

	private function process_links($tweet) {

		// Is the Tweet a ReTweet - then grab the full text of the original Tweet
        $fullText = isset($tweet->text) ? $tweet->text : (isset($tweet->full_text) ? $tweet->full_text : '');
        if ( isset($tweet->retweeted_status) ) {
            // Split it so indices count correctly for @mentions etc.
            $rt_section = current(explode(":", $fullText));
            $text       = $rt_section . ": ";
            // Get Text
            $text .= $tweet->retweeted_status->text;
        } else {
            // Not a retweet - get Tweet
            $text = $fullText;
        }

		// NEW Link Creation from clickable items in the text
		$text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text );
		// Clickable Twitter names
		$text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text );
		// Clickable Twitter hash tags
		$text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text );
		// END TWEET CONTENT REGEX
		return $text;
	}

	private function twitter_time_diff( $from, $to = '' ) {
		$diff = human_time_diff($from,$to);
		$replace = array(
				' hour'    => 'h',
				' hours'   => 'h',
				' day'     => 'd',
				' days'    => 'd',
				' minute'  => 'm',
				' minutes' => 'm',
				' second'  => 's',
				' seconds' => 's',
		);
		return strtr($diff,$replace);
	}

	protected function render_loop_header() {
		$settings        = $this->get_settings_for_display();

		//Global Function
		$this->render_swiper_header_attribute( 'twitter-carousel');

		$this->add_render_attribute( 'carousel', 'class', 'bdt-twitter-carousel bdt-carousel' );

		if ( $settings['match_height'] ) {
			$this->add_render_attribute( 'carousel', 'data-bdt-height-match', 'target: > div > div > .bdt-carousel-item > div > div > .bdt-twitter-text' );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'carousel' ); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
		<?php
	}
}
