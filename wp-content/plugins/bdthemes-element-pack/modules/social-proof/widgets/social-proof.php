<?php
namespace ElementPack\Modules\SocialProof\Widgets;

use Elementor\Plugin;use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {exit;} // Exit if accessed directly

class Social_Proof extends Module_Base {

    public function get_name() {
        return 'bdt-social-proof';
    }

    public function get_title() {
        return BDTEP . esc_html__( 'Social Proof', 'bdthemes-element-pack' );
    }

    public function get_icon() {
        return 'bdt-wi-social-proof';
    }

    public function get_categories() {
        return [ 'element-pack' ];
    }

    public function get_keywords() {
        return [ 'Social', 'Proof', 'Social Proof' ];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-social-proof' ];
        }
    }
    
    public function get_custom_help_url() {
		return 'https://youtu.be/jpIX4VHzSxA';
	}

    protected function register_controls() {

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_google_review',
            [
                'label'     => __( 'Show Google Review', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'show_facebook_review',
            [
                'label'   => __( 'Show Facebook Review', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_yelp_review',
            [
                'label'   => __( 'Show Yelp Review', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_g2_review',
            [
                'label'   => __( 'Show G2 Review', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SWITCHER,
                // 'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'refresh_reviews',
            [
                'label'   => __( 'Reload Reviews After', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'day',
                'options' => [
                    'hour'  => __( 'Hour', 'bdthemes-element-pack' ),
                    'day'   => __( 'Day', 'bdthemes-element-pack' ),
                    'week'  => __( 'Week', 'bdthemes-element-pack' ),
                    'month' => __( 'Month', 'bdthemes-element-pack' ),
                    'year'  => __( 'Year', 'bdthemes-element-pack' ),
                ],
            ]
        );

        $options        = get_option('element_pack_api_settings');
        $GoogleApiKey   = isset($options['google_api_key']) ? esc_html($options['google_api_key']) : '';
        $FbApiKey       = isset($options['facebook_app_id']) ? esc_html($options['facebook_app_id']) : '';
        $YelpApiKey     = isset($options['yelp_api_key']) ? esc_html($options['yelp_api_key']) : '';

        $this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
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
                'separator' => 'before',
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => [
					'small'    => esc_html__( 'Small', 'bdthemes-element-pack' ),
					'medium'   => esc_html__( 'Medium', 'bdthemes-element-pack' ),
					'large'    => esc_html__( 'Large', 'bdthemes-element-pack' ),
					'collapse' => esc_html__( 'Collapse', 'bdthemes-element-pack' ),
				],
			]
        );
        
        $this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-social-proof .bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
			]
		);

        $this->add_control(
			'show_social_icon',
			[
				'label'   => esc_html__( 'Show Social Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
        );

        $this->add_control(
			'show_social_name',
			[
				'label'   => esc_html__( 'Show Social Name', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
        );

        $this->add_control(
			'show_total_rating',
			[
				'label'   => esc_html__( 'Show Total Rating', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
        );

        $this->add_control(
			'show_rating',
			[
				'label'   => esc_html__( 'Show Rating', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
        );

        $this->add_control(
			'show_reviews',
			[
				'label'   => esc_html__( 'Show Reviews', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_google_review',
            [
                'label' => esc_html__( 'Google Reviews', 'bdthemes-element-pack' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
                'condition'   => [
                    'show_google_review' => 'yes',
                ]
            ]
        );

        if ( !$GoogleApiKey) {
            $this->add_control(
                'google_err_msg',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => __( 'To display Google place reviews, please configure Google Map API key.', 'bdthemes-element-pack' ),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                ]
            );
        }

        $this->add_control(
            'google_place_id',
            [
                'label'       => __( 'Google Place ID', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Click %1s HERE %2s to find place ID  ', 'bdthemes-element-pack' ), '<a href="https://developers-dot-devsite-v2-prod.appspot.com/maps/documentation/javascript/examples/full/places-placeid-finder" target="_blank">', '</a>' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_google_page_avg_rating',
            [
                'label'       => __( 'Avg. Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_google_page_total_rating',
            [
                'label'       => __( 'Total Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_google_page_url',
            [
                'label'       => __( 'Google Map URL', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'https://google.com/maps/place/BdThemes+Ltd/',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_facebook_review',
            [
                'label' => esc_html__( 'Facebook Reviews', 'bdthemes-element-pack' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
                'condition'   => [
                    'show_facebook_review' => 'yes'
                ]
            ]
        );

        if ( !$FbApiKey ) {
            $this->add_control(
                'facebook_err_msg',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => __( 'To display Facebook reviews, please configure Facebook APP ID.', 'bdthemes-element-pack' ),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                ]
            );
        }

        $this->add_control(
            'facebook_page_id',
            [
                'label'       => __( 'Page ID', 'bdthemes-element-pack' ),
                'description' => __( 'For example: your page link is https://facebook.com/bdthemes so your page ID is: bdthemes', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'bdthemes',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'facebook_access_token',
            [
                'label'         => __( 'Page Access Token', 'elementor-artbees-extension' ),
                'description'   => sprintf( __( 'Go to %1s this link %2s and copy and paste your page access token from there.', 'bdthemes-element-pack' ), '<a href="https://developers.facebook.com/tools/accesstoken/" target="_blank">', '</a>' ),
                'type'          => Controls_Manager::TEXT, // EP_FB_TOKEN
                'label_block'   => true,
                'show_label'    => true,
                'page_id'       => 'facebook_page_id',
                'button_label'  =>'Or Generate Page Token',
                'dynamic'       => [
                    'active'    => true,
                ],
            ]
        );

        $this->add_control(
            'default_facebook_page_avg_rating',
            [
                'label'       => __( 'Avg. Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_facebook_page_total_rating',
            [
                'label'       => __( 'Total Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_facebook_page_url',
            [
                'label'       => __( 'Facebook Page URL', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'https://facebook.com/abc/',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_yelp_review',
            [
                'label'     => esc_html__( 'Yelp Reviews', 'bdthemes-element-pack' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => ['show_yelp_review'=>'yes']
            ]
        );

        if ( !$YelpApiKey) {
            $this->add_control(
                'yelp_err_msg',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => __( 'To display Yelp reviews, please configure Yelp API key.', 'bdthemes-element-pack' ),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                ]
            );
        }

        $this->add_control(
            'yelp_business_id',
            [
                'label'       => __( 'Yelp Business ID', 'bdthemes-element-pack' ),
                'description' => __( 'For example: your page link is https://www.yelp.com/biz/apple-store-new-york so your business ID is: apple-store-new-york', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_yelp_page_avg_rating',
            [
                'label'       => __( 'Avg. Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'default_yelp_page_total_rating',
            [
                'label'       => __( 'Total Rating (Static)', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '0',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'default_yelp_page_url',
            [
                'label'       => __( 'Yelp Page URL', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'https://yelp.com/abc/',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_g2_review',
            [
                'label' => esc_html__( 'G2 Reviews', 'bdthemes-element-pack' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
                'condition'   => [
                    'show_g2_review'=>'yes'
                ]
            ]
        );

        $this->add_control(
            'g2_info_msg',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => __( 'G2 will show statically right now but we are working for dynamic part.', 'bdthemes-element-pack' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            ]
        );

        $this->add_control(
            'g2_page_avg_rating',
            [
                'label'       => __( 'G2 Avg. Rating', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'Average Rating',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'g2_page_total_ratings',
            [
                'label'       => __( 'G2 Total Rating', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'Total Rating',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'g2_page_url',
            [
                'label'       => __( 'G2 Page URL', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>'https://www.g2.com/products/my-business',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );
        $this->end_controls_section();
        
        //Style
        $this->start_controls_section(
			'section_style_item',
			[
				'label'     => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
        );

        $this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
			'item_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item' => 'background: {{VALUE}};',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'selector'    => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
			[
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
			'item_hover_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover',
			]
        );

        $this->add_control(
			'social_name_heading',
			[
				'label'     => esc_html__( 'Social Name', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
        );

        $this->add_control(
			'item_hover_name_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover .bdt-social-proof-title' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'social_total_rating_heading',
			[
				'label'     => esc_html__( 'Total Rating', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
        );

        $this->add_control(
			'item_hover_rating_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover .bdt-rating-number' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'social_total_reviews_heading',
			[
				'label'     => esc_html__( 'Reviews', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
        );

        $this->add_control(
			'item_hover_reviews_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item:hover .bdt-social-proof-reviews' => 'color: {{VALUE}};',
				],
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_google_reviews',
			[
				'label'     => esc_html__( 'Google Reviews', 'bdthemes-element-pack' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_google_review' => 'yes',
                ]
			]
        );

        $this->start_controls_tabs( 'tabs_google_reviews_style' );

		$this->start_controls_tab(
			'tab_google_reviews_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
			'google_reviews_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'google_reviews_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'google_reviews_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'google_reviews_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_google_reviews_hover',
			[
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
			'google_reviews_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google:hover .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'google_reviews_hover_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google:hover' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'google_reviews_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google:hover' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'google_reviews_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-google:hover',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_facebook_reviews',
			[
				'label'     => esc_html__( 'Facebook Reviews', 'bdthemes-element-pack' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_facebook_review' => 'yes',
                ]
			]
        );

        $this->start_controls_tabs( 'tabs_facebook_reviews_style' );

		$this->start_controls_tab(
			'tab_facebook_reviews_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
			'facebook_reviews_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'facebook_reviews_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'facebook_reviews_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'facebook_reviews_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_facebook_reviews_hover',
			[
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
			'facebook_reviews_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook:hover .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'facebook_reviews_hover_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook:hover' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'facebook_reviews_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook:hover' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'facebook_reviews_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-facebook:hover',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_yelp_reviews',
			[
				'label'     => esc_html__( 'Yelp Reviews', 'bdthemes-element-pack' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_yelp_review' => 'yes',
                ]
			]
        );

        $this->start_controls_tabs( 'tabs_yelp_reviews_style' );

		$this->start_controls_tab(
			'tab_yelp_reviews_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
			'yelp_reviews_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'yelp_reviews_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'yelp_reviews_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'yelp_reviews_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_yelp_reviews_hover',
			[
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
			'yelp_reviews_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp:hover .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'yelp_reviews_hover_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp:hover' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'yelp_reviews_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp:hover' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'yelp_reviews_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-yelp:hover',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_g2_reviews',
			[
				'label'     => esc_html__( 'G2 Reviews', 'bdthemes-element-pack' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_g2_review' => 'yes',
                ]
			]
        );

        $this->start_controls_tabs( 'tabs_g2_reviews_style' );

		$this->start_controls_tab(
			'tab_g2_reviews_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
			'g2_reviews_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2 .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'g2_reviews_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'g2_reviews_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'g2_reviews_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_g2_reviews_hover',
			[
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
			'g2_reviews_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2:hover .bdt-social-icon' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'g2_reviews_hover_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2:hover' => 'background: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'g2_reviews_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2:hover' => 'border-color: {{VALUE}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'g2_reviews_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-item.bdt-social-proof-g2:hover',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_icon',
			[
				'label'     => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-icon' => 'color: {{VALUE}};',
				]
			]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'icon_typography',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-icon i',
			]
        );

        $this->add_responsive_control(
			'icon_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_name',
			[
				'label'     => esc_html__( 'Name', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-title' => 'color: {{VALUE}};',
				]
			]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-title',
			]
        );

        $this->add_responsive_control(
			'name_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-title' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item i.ep-icon-star-empty' => 'color: {{VALUE}};',
				]
			]
        );

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item:nth-child(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item:nth-child(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item:nth-child(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item:nth-child(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item:nth-child(-n+5)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-social-proof .bdt-rating .bdt-rating-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_responsive_control(
			'rating_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-ratting-wrapper' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
        );

        $this->add_control(
			'rating_number_color',
			[
				'label'     => esc_html__( 'Number Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-ratting-wrapper .bdt-rating-number' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-ratting-wrapper .bdt-rating-number',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_reviews',
			[
				'label'     => esc_html__( 'Reviews', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_reviews' => 'yes',
				],
			]
		);

		$this->add_control(
			'reviews_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-social-proof .bdt-social-proof-reviews' => 'color: {{VALUE}};',
				]
			]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'reviews_typography',
				'selector' => '{{WRAPPER}} .bdt-social-proof .bdt-social-proof-reviews',
			]
        );

        $this->end_controls_section();

    }
    public function is_edit_mode(){
        return Plugin::instance()->editor->is_edit_mode();
    }

    public function get_transient_expire( $settings ) {

        if($this->is_edit_mode()){
            return 1 * MINUTE_IN_SECONDS;
        }

        $expire_value = isset($settings['refresh_reviews']) ? $settings['refresh_reviews']: 'week';
        $expire_time  = 24 * HOUR_IN_SECONDS;

        if ( 'hour' === $expire_value ) {
            $expire_time = 60 * MINUTE_IN_SECONDS;
        } elseif ( 'week' === $expire_value ) {
            $expire_time = 7 * DAY_IN_SECONDS;
        } elseif ( 'month' === $expire_value ) {
            $expire_time = 30 * DAY_IN_SECONDS;
        } elseif ( 'year' === $expire_value ) {
            $expire_time = 365 * DAY_IN_SECONDS;
        }

        return $expire_time;
    }

    protected function get_transient_key($key, $placeId){
        $placeId    = strtolower($placeId);
        return 'ep_social_proof_'.$key.'_data_' . $placeId;
    }

    protected function get_google_cache_data($placeId){

        $transient = $this->get_transient_key('google',$placeId);
        $data      = get_transient($transient);

        if(is_array($data) && count($data) > 0){
            if( isset($data['place_id']) && $placeId == $data['place_id'] ){
                return $data;
            } else {
                delete_transient($transient);
            }
        }
        return false;
    }

    protected function get_facebook_cache_data($fb_key){

        $transient = $this->get_transient_key('facebook',$fb_key);
        $data      = get_transient($transient);

        if(is_array($data) && count($data) > 0){
            if(isset($data['fb_key']) && $fb_key == $data['fb_key']){
                return $data;
            }else{
                delete_transient($transient);
            }
            return $data;
        }
        return false;
    }

    protected function get_yelp_cache_data($placeId){

        $transient = $this->get_transient_key('yelp', $placeId);
        $data      = get_transient($transient);

        if(is_array($data) && count($data) > 0){
            if(isset($data['business_id']) && $placeId == $data['business_id']){
                return $data;
            }else{
                delete_transient($transient);
            }
        }
        return false;
    }

    protected function get_google_review_by_api($settings){

        $data = [];
        if( $settings['show_google_review'] == 'yes' ) {

            $options = get_option('element_pack_api_settings');
            $placeId = isset($settings['google_place_id']) ? esc_html($settings['google_place_id']) : '';
            $ApiKey = isset($options['google_map_key']) ? esc_html($options['google_map_key']) : '';

            if (!$placeId || !$ApiKey) {
                return array('error_message' => "Google API key or Google Place ID is missing.");
            }

            $reviewData = $this->get_google_cache_data($placeId);

            if (is_array($reviewData) && count($reviewData) > 2) {
                return $reviewData;
            } else {

                $parameters = "key=$ApiKey&placeid=$placeId";
                $requestUrl = "https://maps.googleapis.com/maps/api/place/details/json?$parameters";

                $response = wp_remote_get($requestUrl);

                if (is_wp_error($response)) {
                    return array('error_message' => $response->get_error_message());
                }
                $response = json_decode($response['body'], true);
                $result = (isset($response['result']) && is_array($response['result'])) ? $response['result'] : '';

                if (is_array($result)) {
                    if (isset($result['status'])) {
                        $_error = "";
                        if ($result['status'] == 'OVER_QUERY_LIMIT') {
                            $_error = 'You have exceeded your daily request quota for this API. If you did not set a custom daily request quota, verify your project has an active billing account: http://g.co/dev/maps-no-account';
                        } elseif ($result['status'] == 'REQUEST_DENIED') {
                            $_error = "Invalid Google API key!";
                        } elseif ($result['status'] == 'UNKNOWN_ERROR') {
                            $_error = "Seems like a server-side error; Please try again later.";
                        } elseif ($result['status'] == 'INVALID_REQUEST') {
                            $_error = "Please check if the entered Place ID is invalid.";
                        } elseif ($result['status'] == 'NOT_FOUND') {
                            $_error = "Not found.";
                        }
                        if ($_error) {
                            return array('error_message' => 'Google Error Message: ' . $_error);
                        }
                    }
                    if (isset($result['error_message'])) {
                        return $result;
                    }

                    $data['rating']         = (isset($result['rating']) && $result['rating'] > 0) ? $result['rating'] : $settings['default_google_page_avg_rating'];
                    $data['total_ratings']  = (isset($result['total_ratings']) && $result['total_ratings'] > 0) ? $result['total_ratings'] : $settings['default_google_page_total_rating'];
                    $data['url']            = (isset($result['url'])) ? $result['url'] : $settings['default_google_page_url']['url'];

                    if ($data) {
                        $data['place_id'] = $placeId;
                        $transient = $this->get_transient_key('google', $placeId);
                        $expireTime = $this->get_transient_expire($settings);
                        set_transient($transient, $data, $expireTime); // One day
                    }

                    return $data;
                }
                return $response;
            }
        }
        return $data;
    }

    public function get_yelp_review_by_api($settings){

        $data = [];
        if(isset($settings['show_yelp_review']) && $settings['show_yelp_review'] == 'yes') {
            $business_id = $settings['yelp_business_id'];
            if ('' !== $business_id) {

                $url = 'https://api.yelp.com/v3/businesses/' . $business_id;

                $options = get_option('element_pack_api_settings');
                $ApiKey = isset($options['yelp_api_key']) ? esc_html($options['yelp_api_key']) : '';

                if ('' == $ApiKey) {
                    return array('error_message' => 'Yelp Error Message: Please set the Yelp API key to display the reviews.');
                }

                $reviewData = $this->get_yelp_cache_data($business_id);

                if ($reviewData) {
                    return $reviewData;
                }

                $response = wp_remote_get(
                    $url,
                    array(
                        'method' => 'GET',
                        'timeout' => 60,
                        'httpversion' => '1.0',
                        'user-agent' => '',
                        'headers' => array(
                            'Authorization' => 'Bearer ' . $ApiKey,
                        ),
                    )
                );

                if (is_wp_error($response)) {
                    return array('error_message' => $response->get_error_message());
                }
                $result = json_decode($response['body'], true);

                // NOT_FOUND
                if (is_array($result)) {
                    if (isset($result['error'])) {
                        if(isset($result['error']['description'])){
                            return $result['error']['description'];
                        }
                        return $result;
                    }

                    if (isset($result['rating'])) {
                        $data['rating'] = $result['rating'];
                    }
                    if (isset($result['review_count'])) {
                        $data['total_ratings'] = $result['review_count'];
                    }
                    if (isset($result['url'])) {
                        $data['url'] = "https://www.yelp.com/biz/" . $business_id;
                    }

                    if ($data) {
                        $data['business_id'] = $business_id;
                        $transient = $this->get_transient_key('yelp', $business_id);
                        $expireTime = $this->get_transient_expire($settings);
                        set_transient($transient, $data, $expireTime); // One day
                    }

                    return $data;
                }
                return $response;
            }
        }
        return $data;
    }

    protected function get_facebook_review_by_api($settings){
        $data = [];

        if(isset($settings['facebook_access_token']) && !empty($settings['facebook_access_token'])
            && isset($settings['facebook_page_id']) && !empty($settings['facebook_page_id'])) {
            $access_token = esc_attr($settings['facebook_access_token']);
            $facebook_page_id = esc_attr($settings['facebook_page_id']);

            $fb_key = $access_token.$facebook_page_id;
            $reviewData = $this->get_facebook_cache_data($fb_key);
            if ($reviewData) {
                return $reviewData;
            } else {
                $parameters = "?fields=overall_star_rating,rating_count,page_token&access_token=$access_token";
                $requestUrl = "https://graph.facebook.com/$facebook_page_id" . $parameters;
                $response = wp_remote_get($requestUrl);

                if (is_wp_error($response)) {
                    return array('error_message' => $response->get_error_message());
                }

                $result = json_decode($response['body'], true);
                if(isset($result['error'])){
                    if(isset($result['error']['message'])){
                        return array('error_message'=> $result['error']['message']);
                    }else{
                        return array('error_message'=> $result['error']);
                    }
                }

                if (is_array($result)) {
                    if (isset($result['error_message'])) {
                        return $result;
                    }
                    if(isset($result['rating_count']) && isset($result['id'])){
                        $data['rating']         = 5;
                        $data['total_ratings']  = $result['rating_count'];
                        $data['fb_key']         = $fb_key;
                        $data['url']            = 'https://www.facebook.com/'.$result['id'];
                        if(isset($result['overall_star_rating'])){
                            $data['rating']     = $result['overall_star_rating'];
                        }
                        $transient = $this->get_transient_key('facebook', $fb_key);
                        $expireTime = $this->get_transient_expire($settings);
                        delete_transient($transient);
                        set_transient($transient, $data, $expireTime); // one week
                    }

                    return $data;
                }
                return $response;
            }
        }
        return $data;

    }



    protected function get_default_facebook_review($settings){
        $data = [];
        $data['rating']         = isset($settings['default_facebook_page_avg_rating']) ? $settings['default_facebook_page_avg_rating']:5;
        $data['total_ratings']  = isset($settings['default_facebook_page_total_rating']) ? $settings['default_facebook_page_total_rating']:1;
        $data['_url']           = isset($settings['default_facebook_page_url']) ? $settings['default_facebook_page_url']:1;
        $facebook_page_id       = isset($settings['facebook_page_id']) ? $settings['facebook_page_id'] : '';
        if($facebook_page_id){
            $data['url']       = 'https://www.facebook.com/'.$facebook_page_id;
        }else{
            $data['url']       = $data['_url'];
        }
        return $data;
    }

    protected function get_default_google_review($settings){
        $data = [];
        $data['rating']         = isset($settings['default_google_page_avg_rating']) ? $settings['default_google_page_avg_rating']:5;
        $data['total_ratings']  = isset($settings['default_google_page_total_rating']) ? $settings['default_google_page_total_rating']:1;
        $data['url']            = isset($settings['default_google_page_url']) ? $settings['default_google_page_url']:'';

        return $data;
    }

    protected function get_default_yelp_review($settings){

        $data = [];
        $data['rating']         = isset($settings['default_yelp_page_avg_rating']) ? $settings['default_yelp_page_avg_rating']:5;
        $data['total_ratings']  = isset($settings['default_yelp_page_total_rating']) ? $settings['default_yelp_page_total_rating']:1;
        $data['url']            = isset($settings['default_yelp_page_url']) ? $settings['default_yelp_page_url']:'';

        return $data;
    }
    protected function get_facebook_review($settings){

        $data = [];
        if(isset($settings['show_facebook_review']) && $settings['show_facebook_review'] == 'yes'){
            $fbApiReview            = $this->get_facebook_review_by_api($settings);
            $fbDefaultReview        = $this->get_default_facebook_review($settings);

            $data['rating']         = (isset($fbApiReview['rating']) && $fbApiReview['rating'] > 0) ? $fbApiReview['rating'] : $fbDefaultReview['rating'];
            $data['total_ratings']  = (isset($fbApiReview['total_ratings']) && $fbApiReview['total_ratings'] > 0) ? $fbApiReview['total_ratings'] : $fbDefaultReview['total_ratings'];
            $data['url']            = isset($fbApiReview['url']) ? $fbApiReview['url'] : $fbDefaultReview['url'];
            if(isset($fbApiReview['error_message'])){
                $data['error_message'] =  $fbApiReview['error_message'];
            }
        }
        return $data;
    }

    protected function get_google_review($settings){

        $data = [];
        if(isset($settings['show_google_review']) && $settings['show_google_review'] == 'yes'){
            $googleApiReview            = $this->get_google_review_by_api($settings);
            $googleDefaultReview        = $this->get_default_google_review($settings);

            $data['rating']         = (isset($googleApiReview['rating']) && $googleApiReview['rating'] > 0) ? $googleApiReview['rating'] : $googleDefaultReview['rating'];
            $data['total_ratings']  = (isset($googleApiReview['total_ratings']) && $googleApiReview['total_ratings'] > 0) ? $googleApiReview['total_ratings'] : $googleDefaultReview['total_ratings'];
            $data['url']            = isset($googleApiReview['url']) ? $googleApiReview['url'] : $googleDefaultReview['url'];
            if(isset($fbApiReview['error_message'])){
                $data['error_message'] =  $googleApiReview['error_message'];
            }
        }
        return $data;
    }

    protected function get_yelp_review($settings){

        $data = [];
        if(isset($settings['show_yelp_review']) && $settings['show_yelp_review'] == 'yes'){
            $yelpApiReview        = $this->get_yelp_review_by_api($settings);
            $yelpDefaultReview    = $this->get_default_yelp_review($settings);

            $data['rating']         = (isset($yelpApiReview['rating']) && $yelpApiReview['rating'] > 0) ? $yelpApiReview['rating'] : $yelpDefaultReview['rating'];
            $data['total_ratings']  = (isset($yelpApiReview['total_ratings']) && $yelpApiReview['total_ratings'] > 0) ? $yelpApiReview['total_ratings'] : $yelpDefaultReview['total_ratings'];
            $data['url']            = isset($yelpApiReview['url']) ? $yelpApiReview['url'] : $yelpDefaultReview['url'];
            if(isset($fbApiReview['error_message'])){
                $data['error_message'] =  $yelpApiReview['error_message'];
            }
        }
        return $data;
    }

     protected function get_g2_review($settings){

         $data = [];
         if(isset($settings['show_g2_review']) && $settings['show_g2_review'] == 'yes'){
             $data['rating']         = (isset($settings['g2_page_avg_rating']) && $settings['g2_page_avg_rating'] > 0) ? $settings['g2_page_avg_rating']: 5;
             $data['total_ratings']  = (isset($settings['g2_page_total_ratings']) && $settings['g2_page_total_ratings'] > 0) ? $settings['g2_page_total_ratings']: 1;
             $data['url']            = isset($settings['g2_page_url']) ? $settings['g2_page_url']:'';
         }
         return $data;
     }

    public function get_all_reviews($settings){

        $reviews = [];
        $reviews['google']  = $this->get_google_review($settings);
        $reviews['facebook']= $this->get_facebook_review($settings);
        $reviews['yelp']    = $this->get_yelp_review($settings);
        $reviews['g2']      = $this->get_g2_review($settings);

        $reviews = array_filter($reviews);
        return apply_filters('ep_social_proof_filter_reviews', $reviews);

    }

    public function print_error($review){
        if(isset($review['error_message'])){
            if($this->is_edit_mode()){
                if(is_string($review['error_message'])){
                    echo esc_html($review['error_message']);
                }else{
                    print_r($review['error_message']);
                }
            }
        }
    }

    public function render_rating($rating = 0) {
		$settings = $this->get_settings_for_display();

		if( ! $settings['show_rating'] ) {
			return;
		}

        if(!is_numeric($rating)){
		    $rating = 0;
		}
        $intpart = floor ( $rating );
        $fraction = $rating - $intpart;
        $unrated = 5 - ceil ( $rating );

		?>
		<div class="bdt-social-proof-rating">
			<ul class="bdt-rating bdt-grid bdt-grid-collapse bdt-rating-<?php echo esc_attr($rating); ?>" data-bdt-grid>
                <?php
                if ( $intpart <= 5 ) {
                    for ( $i=0; $i<$intpart; $i++ )
                    {echo '<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>';}
                }

                if ( $fraction >= 0.5 ) {
                    echo '<li class="bdt-rating-item"><i class="ep-icon-star-half" aria-hidden="true"></i></li>';
                }

                if ( $unrated > 0 ) {
                    for ( $j=0; $j<$unrated; $j++ ) {
                    	echo '<li class="bdt-rating-item"><i class="ep-icon-star-empty" aria-hidden="true"></i></li>';
                    }
                }
                ?>
			</ul>
		</div>
		<?php
	}

    protected function render() {
        $settings       = $this->get_settings_for_display();
        $reviews        = $this->get_all_reviews($settings);
        $id       = $this->get_id();

        $desktop_cols = isset($settings['columns']) ? $settings['columns'] : 3;
        $tablet_cols = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
        $mobile_cols = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;


    ?>
    <div id="bdt-social-proof-<?php echo esc_attr($id); ?>" class="bdt-social-proof">
        <div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?> bdt-child-width-1-<?php echo esc_attr($mobile_cols); ?> bdt-child-width-1-<?php echo esc_attr($tablet_cols); ?>@s bdt-child-width-1-<?php echo esc_attr($desktop_cols); ?>@l" data-bdt-grid>

        <?php

        foreach($reviews as $key=>$val){

            if(isset($reviews['error_message'])){
                $this->print_error($val);
            } else {
                $social         = $key;
                $rating         = isset($val['rating']) ? $val['rating'] : 0;
                $totalRatings   = isset($val['total_ratings']) ? $val['total_ratings'] : 0;
                $pageUrl        = isset($val['url']) ? $val['url'] : '#';
                ?>

                <div>
                    <a class="bdt-social-proof-item bdt-social-proof-<?php echo esc_html($social) ?> bdt-flex bdt-flex-middle" href="<?php echo esc_url($pageUrl) ?>" target="_blank">

                        <div>

                            <?php if ( 'yes' == $settings['show_social_icon'] ) : ?>
                            <div class="bdt-social-icon">
                                <i class="ep-icon-<?php echo esc_html($social) ?>" aria-hidden="true"></i>
                            </div>
                            <?php endif; ?>

                        </div>
                        
                        <div>

                            <?php if ( 'yes' == $settings['show_social_name'] ) : ?>
                            <h3 class="bdt-social-proof-title">
                                <?php echo esc_html($social) ?> <?php echo esc_html('Rating') ?> 
                            </h3>
                            <?php endif; ?>

                            <?php if ( 'yes' == $settings['show_total_rating'] or 'yes' == $settings['show_rating'] ) : ?>
                            <div class="bdt-social-proof-ratting-wrapper bdt-flex bdt-flex-middle">
                                <?php if ( 'yes' == $settings['show_total_rating'] ) : ?>
                                <span class="bdt-rating-number"><?php echo esc_html($rating) ?> </span> 
                                <?php endif; ?>
                                <?php $this->render_rating($rating); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ( 'yes' == $settings['show_reviews'] ) : ?>
                            <span class="bdt-social-proof-reviews">
                                <?php echo esc_html('Based on') ?> <?php echo esc_html($totalRatings) ?> <?php echo esc_html('reviews') ?>
                            </span>
                            <?php endif; ?>

                        </div>

                    </a>
                </div>

                <?php
            }
        }
    }
}
