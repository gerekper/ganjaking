<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use Elementor\Repeater;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

//use \Essential_Addons_Elementor\Classes\Helper;
use \Essential_Addons_Elementor\Pro\Classes\Helper;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Content_Timeline extends Widget_Base
{

	use \Essential_Addons_Elementor\Traits\Template_Query;

	public function get_name()
	{
		return 'eael-content-timeline';
	}

	public function get_title()
	{
		return __('Content Timeline', 'essential-addons-elementor');
	}

	public function get_icon()
	{
		return 'eaicon-content-timeline';
	}

	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'content timeline',
			'ea post timeline',
			'ea content timeline',
			'ea timeline',
			'content',
			'timeline',
			'blog post',
			'blog',
			'post',
			'ea',
			'essential addons'
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/content-timeline/';
	}

	protected function register_controls()
	{
		/**
		 * Custom Timeline Settings
		 */
		$this->start_controls_section(
			'eael_section_custom_timeline_settings',
			[
				'label' => __('Timeline Content', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'eael_content_timeline_choose',
			[
				'label'       	=> esc_html__('Content Source', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'dynamic',
				'label_block' 	=> false,
				'options' 		=> [
					'custom'  	=> esc_html__('Custom', 'essential-addons-elementor'),
					'dynamic'  	=> esc_html__('Dynamic', 'essential-addons-elementor'),
				],
			]
		);

		$this->end_controls_section();
		/**
		 * Custom Content
		 */
		$this->start_controls_section(
			'eael_section_custom_content_settings',
			[
				'label' => __('Custom Content Settings', 'essential-addons-elementor'),
				'condition' => [
					'eael_content_timeline_choose' => 'custom'
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'eael_custom_title',
			[
				'label' => esc_html__('Title', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('The Ultimate Addons For Elementor', 'essential-addons-elementor'),
				'dynamic' => ['active' => true],
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'eael_custom_excerpt',
			[
				'label' => esc_html__('Content', 'essential-addons-elementor'),
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'default' => __('<p>A new concept of showing content in your web page with more interactive way.</p>', 'essential-addons-elementor'),
			]
		);

		$repeater->add_control(
			'eael_custom_post_date',
			[
				'label' => __('Post Date', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Nov 09, 2017', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'eael_show_custom_image_or_icon',
			[
				'label' => __('Show Circle Image / Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'img' => [
						'title' => __('Image', 'essential-addons-elementor'),
						'icon' => 'eicon-image-bold',
					],
					'icon' => [
						'title' => __('Icon', 'essential-addons-elementor'),
						'icon' => 'fa fa-info',
					],
					'bullet' => [
						'title' => __('Bullet', 'essential-addons-elementor'),
						'icon' => 'fa fa-circle',
					]
				],
				'default' => 'icon',
				'separator' => 'before'
			]
		);

        $repeater->add_control(
			'eael_custom_icon_image',
			[
				'label' => __( 'Choose Image', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
                'condition' => [
					'eael_show_custom_image_or_icon' => 'img',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'eael_custom_icon_image_size',
			[
				'label' => esc_html__('Icon Image Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::NUMBER,
				'default' => 24,
				'condition' => [
					'eael_show_custom_image_or_icon' => 'img',
				],
			]
		);

		$repeater->add_control(
			'eael_custom_content_timeline_circle_icon_new',
			[
				'label' => esc_html__('Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'eael_custom_content_timeline_circle_icon',
				'default' => [
					'value' => 'fas fa-pencil-alt',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_show_custom_image_or_icon' => 'icon',
				]
			]
		);

		$repeater->add_control(
			'eael_show_custom_read_more',
			[
				'label' => __('Show Read More', 'essential-addons-elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'1' => [
						'title' => __('Yes', 'essential-addons-elementor'),
						'icon' => 'fa fa-check',
					],
					'0' => [
						'title' => __('No', 'essential-addons-elementor'),
						'icon' => 'eicon-ban',
					]
				],
				'default' => '1',
				'separator' => 'before'
			]
		);

		$repeater->add_control(
			'eael_show_custom_read_more_text',
			[
				'label' => esc_html__('Label Text', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'dynamic'   => ['active' => true],
				'label_block' => true,
				'default' => esc_html__('Read More', 'essential-addons-elementor'),
				'condition' => [
					'eael_show_custom_read_more' => '1',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'eael_read_more_text_link',
			[
				'label' => esc_html__('Button Link', 'essential-addons-elementor'),
				'type' => Controls_Manager::URL,
				'dynamic'   => ['active' => true],
				'label_block' => true,
				'default' => [
					'url' => '#',
					'is_external' => '',
				],
				'show_external' => true,
				'condition' => [
					'eael_show_custom_read_more' => '1',
				]
			]
		);

		$this->add_control(
			'eael_coustom_content_posts',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[
						'eael_custom_title' => __('The Ultimate Addons For Elementor', 'essential-addons-elementor'),
						'eael_custom_excerpt' => __('<p>A new concept of showing content in your web page with more interactive way.</p>', 'essential-addons-elementor'),
						'eael_custom_post_date' => 'Nov 09, 2017',
						'eael_read_more_text_link' => '#',
						'eael_show_custom_read_more' => '1',
						'eael_show_custom_read_more_text' => 'Read More',
					],
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '{{eael_custom_title}}',
			]
		);



		$this->end_controls_section();

		/**
		 * Query And Layout Controls!
		 * @source includes/elementor-helper.php
		 */
		do_action('eael/controls/query', $this);

		do_action('eael/controls/layout', $this);

        /**
         * Content Tab: Links
         */

        $this->start_controls_section(
            'section_content_timeline_links',
            [
                'label' => __('Links', 'essential-addons-elementor'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                       [
                          'name' => 'eael_content_timeline_choose',
                          'operator' => '==',
                          'value' => 'dynamic',
                       ],
                       [
                          'relation' => 'or',
                          'terms' => [
                             [
                                'name' => 'eael_show_title',
                                'operator' => '==',
                                'value' => 'yes',
                             ],
                             [
                                'name' => 'eael_show_read_more',
                                'operator' => '==',
                                'value' => 'yes',
                             ],
                          ],
                       ],
                    ],
                 ],
            ]
        );

        $this->add_control(
            'title_link',
            [
                'label' => __('Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_title' => 'yes',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'read_more_link',
            [
                'label' => __('Read More', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_read_more' => 'yes',
                ],
            ]
        );

		$this->add_control(
			'thumbnail_link',
			[
				'label'     => __( 'Thumbnail', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'eael_show_image' => 'yes',
					'eael_image_linkable' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'thumbnail_link_nofollow',
			[
				'label'        => __( 'No Follow', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'condition'    => [
					'eael_show_image' => 'yes',
					'eael_image_linkable' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_link_target_blank',
			[
				'label'        => __( 'Target Blank', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'condition'    => [
					'eael_show_image' => 'yes',
					'eael_image_linkable' => 'yes',
				],
			]
		);

        $this->end_controls_section();

        /**
         * Section: Style
         */

		$this->start_controls_section(
			'eael_section_post_timeline_style',
			[
				'label' => __('Timeline', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'eael_timeline_line_size',
			[
				'label' => esc_html__('Line Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-line' => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline__line' => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .eael-content-timeline-line .eael-content-timeline-inner' => 'width: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_timeline_line_from_left',
			[
				'label' => esc_html__('Position From Left', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-line' => 'margin-left: -{{SIZE}}px;',
				],
				'description' => __('Use half of the Line size for perfect centering', 'essential-addons-elementor'),
				'condition' => [
					'eael_dynamic_template_Layout' => 'default',
				],
			]
		);

		$this->add_control(
			'eael_timeline_line_from_top',
			[
				'label' => esc_html__('Position From Top', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-horizontal-timeline__line' => 'top: {{SIZE}}px;',
				],
				'description' => __('Use half of the Line size for perfect centering', 'essential-addons-elementor'),
				'condition' => [
					'eael_dynamic_template_Layout' => 'horizontal',
				],
			]
		);

		$this->add_control(
			'eael_timeline_line_color',
			[
				'label' => __('Inactive Line Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#d7e4ed',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-line' => 'background: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline__line' => 'background: {{VALUE}}',
				]

			]
		);

		$this->add_control(
			'eael_timeline_line_active_color',
			[
				'label' => __('Active Line Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#3CCD94',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-line .eael-content-timeline-inner' => 'background: {{VALUE}}',
					// '{{WRAPPER}} .eael-horizontal-timeline__line' => 'background: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline__line::after' => 'background: {{VALUE}}; border: 3px solid {{VALUE}};',
				]

			]
		);

		$this->end_controls_section();

		/**
		 * Card Style
		 */
		$this->start_controls_section(
			'eael_section_post_timeline_card_style',
			[
				'label' => __('Card', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'eael_card_bg_color',
			[
				'label' => __('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f1f2f3',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-content-timeline-content::before' => 'border-left-color: {{VALUE}}; border-right-color: {{VALUE}};',
				]

			]
		);

		$this->add_responsive_control(
			'eael_card_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_card_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_card_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-content-timeline-content, {{WRAPPER}} .eael-horizontal-timeline-item__card-inner',
			]
		);

		$this->add_responsive_control(
			'eael_card_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_card_shadow',
				'selector' => '{{WRAPPER}} .eael-content-timeline-content, {{WRAPPER}} .eael-horizontal-timeline-item__card-inner',
			]
		);

		$this->end_controls_section();

        /**
         * -------------------------------------------
         * Caret Style
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_content_timeline_caret_style',
            [
                'label' => esc_html__('Caret', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        //Caret can be hidden using caret size : 0
//        $this->add_control(
//            'eael_content_timeline_tab_caret_show',
//            [
//                'label' => esc_html__('Show Caret', 'essential-addons-elementor'),
//                'type' => Controls_Manager::SWITCHER,
//                'default' => 'yes',
//                'return_value' => 'yes',
//            ]
//        );
        $this->add_responsive_control(
            'eael_content_timeline_tab_caret_size',
            [
                'label' => esc_html__('Caret Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-content-timeline-content::before' => 'border-width: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-horizontal-timeline-item__card-arrow' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
                ],
                'condition' => [
//                    'eael_content_timeline_tab_caret_show' => 'yes',
                ],
            ]
        );

		$this->add_responsive_control(
			'eael_content_timeline_tab_caret_position',
			[
				'label'      => esc_html__( 'Caret Position', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'size'  => 35,
					'unite' => 'px'
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-content-timeline-content::before'     => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-arrow' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
//                    'eael_content_timeline_tab_caret_show' => 'yes',
				],
			]
		);

        $this->add_control(
            'eael_content_timeline_tab_caret_color',
            [
                'label' => esc_html__('Caret Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
//                'default' => 'transparent',
                'default' => '#f1f2f3',
                'selectors' => [
                    '{{WRAPPER}} .eael-content-timeline-content::before' => 'border-left-color: {{VALUE}};border-right-color: {{VALUE}};',
                    '{{WRAPPER}} .eael-horizontal-timeline-item__card-arrow:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
//                    'eael_content_timeline_tab_caret_show' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

		/**
		 * Icon Circle Style
		 */
		$this->start_controls_section(
			'eael_section_post_timeline_icon_circle_style',
			[
				'label' => __('Bullet', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'eael_icon_circle_size',
			[
				'label' => esc_html__('Bullet Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_icon_font_size',
			[
				'label' => esc_html__('Icon Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img i' => 'font-size: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-content-timeline-img .content-timeline-bullet-svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-content-timeline-img svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; vertical-align:middle;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_icon_circle_from_top',
			[
				'label' => esc_html__('Position From Top', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img' => 'margin-top: {{SIZE}}px;',
					// '{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .eael-content-timeline-line' => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline__line' => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} ..eael-content-timeline-line .eael-content-timeline-inner' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_icon_circle_from_left',
			[
				'label' => esc_html__('Position From Left', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'description' => __('Use half of the Icon Cicle Size for perfect centering', 'essential-addons-elementor'),
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img' => 'margin-left: -{{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'margin-left: -{{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_icon_circle_border_width',
			[
				'label' => esc_html__('Bullet Border Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 6,
				],
				'range' => [
					'px' => [
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img.eael-picture' => 'border-width: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'border-width: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_icon_circle_color',
			[
				'label' => __('Bullet Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f1f2f3',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img.eael-picture' => 'background: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'background: {{VALUE}}',
				]

			]
		);


		$this->add_control(
			'eael_icon_circle_border_color',
			[
				'label' => __('Bullet Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-img.eael-picture' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'border-color: {{VALUE}}',
				]

			]
		);


		$this->add_control(
			'eael_icon_circle_font_color',
			[
				'label' => __('Bullet Font Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
                    '{{WRAPPER}} .eael-content-timeline-img i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-content-timeline-img svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item__point-content .eael-elements-icon svg' => 'fill: {{VALUE}}',
				]

			]
		);


		$this->add_control(
			'eael_timeline_icon_active_state',
			[
				'label' => __('Active State (Highlighted)', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_icon_circle_active_color',
			[
				'label' => __('Bullet Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#3CCD94',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-block.eael-highlight .eael-content-timeline-img.eael-picture' => 'background: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item.is-active .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'background: {{VALUE}}',
				]

			]
		);


		$this->add_control(
			'eael_icon_circle_active_border_color',
			[
				'label' => __('Bullet Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-block.eael-highlight .eael-content-timeline-img.eael-picture' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item.is-active .eael-horizontal-timeline-item__point-content .eael-elements-icon' => 'border-color: {{VALUE}}',
				]

			]
		);

		$this->add_control(
			'eael_icon_circle_active_font_color',
			[
				'label' => __('Bullet Font Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
                    '{{WRAPPER}} .eael-content-timeline-block.eael-highlight .eael-content-timeline-img i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-content-timeline-block.eael-highlight .eael-content-timeline-img svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item.is-active .eael-horizontal-timeline-item__point-content .eael-elements-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-horizontal-timeline-item.is-active .eael-horizontal-timeline-item__point-content .eael-elements-icon svg' => 'fill: {{VALUE}}',
				]

			]
		);


		$this->end_controls_section();

		/**
         * -------------------------------------------
         * Scrollbar Style
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_content_timeline_hor_scrollbar_style',
            [
                'label' => esc_html__('Scrollbar', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_dynamic_template_Layout' => 'horizontal',
					'eael_content_timeline_navigation_type' => 'scrollbar',
				],
            ]
        );

        $this->add_responsive_control(
            'eael_content_timeline_hor_scrollbar_height',
            [
                'label' => esc_html__('Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track::-webkit-scrollbar' => 'height: {{SIZE}}px;',
                ],
            ]
        );

		$this->add_control(
			'eael_content_timeline_hor_scrollbar_background',
			[
				'label' => __('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#D7E4ED',
				'selectors' => [
					'{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track::-webkit-scrollbar' => 'background: {{VALUE}};',
				]

			]
		);

		$this->add_control(
			'eael_content_timeline_hor_scrollbar_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
                    'size' => 4,
                ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track::-webkit-scrollbar' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_content_timeline_hor_scrollbar_thumb_style',
			[
				'label' => __('Scrollbar Thumb', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_content_timeline_hor_scrollbar_thumb_background',
			[
				'label' => __('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#3CCD94',
				'selectors' => [
					'{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track' => 'scrollbar-color: {{VALUE}} transparent;',
				]

			]
		);

		$this->add_control(
			'eael_content_timeline_hor_scrollbar_thumb_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
                    'size' => 4,
                ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-horizontal-timeline--scrollbar .eael-horizontal-timeline-track::-webkit-scrollbar-thumb' => 'border-radius: {{SIZE}}px;',
				],
			]
		);
        
        $this->end_controls_section();

		/**
         * -------------------------------------------
         * Arrows Style
         * -------------------------------------------
         */
		$this->start_controls_section(
			'section_content_timeline_hor_arrows_style',
			array(
				'label'     => esc_html__( 'Arrows', 'essential-addons-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'eael_content_timeline_navigation_type' => 'arrows',
				),
			)
		);

		$this->add_control(
            'eael_content_timeline_hor_arrow_background_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-horizontal-timeline .eael-arrow' => 'background-color: {{VALUE}};',
                ],
            ]
        );
		
		$this->add_control(
            'eael_content_timeline_hor_arrow_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-horizontal-timeline .eael-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
			'eael_content_timeline_hor_prev_arrow_position',
			array(
				'label'     => esc_html__( 'Prev Arrow Position', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eael_content_timeline_hor_prev_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Position by', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'essential-addons-elementor' ),
					'right' => esc_html__( 'Right', 'essential-addons-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'eael_content_timeline_hor_prev_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'eael_content_timeline_hor_prev_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eael-horizontal-timeline .eael-arrow.eael-prev-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'eael_content_timeline_hor_prev_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'eael_content_timeline_hor_prev_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eael-horizontal-timeline .eael-arrow.eael-prev-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			)
		);

		$this->add_control(
			'eael_content_timeline_hor_next_arrow_position',
			array(
				'label'     => esc_html__( 'Next Arrow Position', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eael_content_timeline_hor_next_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Position by', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'essential-addons-elementor' ),
					'right' => esc_html__( 'Right', 'essential-addons-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'eael_content_timeline_hor_next_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'eael_content_timeline_hor_next_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eael-horizontal-timeline .eael-arrow.eael-next-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'eael_content_timeline_hor_next_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'eael_content_timeline_hor_next_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .eael-horizontal-timeline .eael-arrow.eael-next-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_section_typography',
			[
				'label' => __('Color & Typography', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'eael_timeline_title_style',
			[
				'label' => __('Title Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_timeline_title_color',
			[
				'label' => __('Title Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#303e49',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-timeline-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-content-timeline-content .eael-timeline-title a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__card-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__card-title a' => 'color: {{VALUE}};',
				]

			]
		);

		$this->add_responsive_control(
			'eael_timeline_title_alignment',
			[
				'label' => __('Title Alignment', 'essential-addons-elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					]
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-timeline-title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-content-timeline-content .eael-timeline-title a' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__card-title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__card-title a' => 'text-align: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_timeline_title_typography',
				'label' => __('Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY
				],
                'selector' =>'{{WRAPPER}} .eael-content-timeline-content .eael-timeline-title, {{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__card-title',
			]
		);

		$this->add_control(
			'eael_timeline_excerpt_style',
			[
				'label' => __('Excerpt Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_timeline_excerpt_color',
			[
				'label' => __('Excerpt Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner p' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_responsive_control(
			'eael_timeline_excerpt_alignment',
			[
				'label' => __('Excerpt Alignment', 'essential-addons-elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item__card-inner' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_timeline_excerpt_typography',
				'label' => __('Excerpt Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT
				],
				'selector' => '{{WRAPPER}} .eael-content-timeline-content p, {{WRAPPER}} .eael-horizontal-timeline-item__card-inner p',
			]
		);

		$this->add_control(
			'eael_timeline_date_style',
			[
				'label' => __('Date Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'eael_timeline_date_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_timeline_date_color',
			[
				'label' => __('Date Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#4d4d4d',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-date' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__meta' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_timeline_date_typography',
				'label' => __('Date Typography', 'essential-addons-elementor'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT
				],
				'selector' => '{{WRAPPER}} .eael-content-timeline-content .eael-date, {{WRAPPER}} .eael-horizontal-timeline-item .eael-horizontal-timeline-item__meta',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_section_load_more_btn',
			[
				'label' => __('Load More Button', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_load_more' => '1'
				]
			]
		);

		$this->add_responsive_control(
			'eael_post_block_load_more_btn_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_post_block_load_more_btn_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_post_block_load_more_btn_typography',
				'selector' => '{{WRAPPER}} .eael-load-more-button',
			]
		);

		$this->start_controls_tabs('eael_post_block_load_more_btn_tabs');

		// Normal State Tab
		$this->start_controls_tab('eael_post_block_load_more_btn_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_post_block_load_more_btn_normal_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_load_more_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#29d8d8',
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_post_block_load_more_btn_normal_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-load-more-button',
			]
		);

		$this->add_control(
			'eael_post_block_load_more_btn_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		// Hover State Tab
		$this->start_controls_tab('eael_post_block_load_more_btn_hover', ['label' => esc_html__('Hover', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_post_block_load_more_btn_hover_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_post_block_load_more_btn_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#27bdbd',
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_post_block_load_more_btn_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button:hover' => 'border-color: {{VALUE}};',
				],
			]

		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_post_block_load_more_btn_shadow',
				'selector' => '{{WRAPPER}} .eael-load-more-button',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_post_timeline_load_more_loader_pos_title',
			[
				'label' => esc_html__('Loader Position', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_post_timeline_loader_pos_left',
			[
				'label' => esc_html__('From Left', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button.button--loading .button__loader' => 'left: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_post_timeline_loader_pos_top',
			[
				'label' => esc_html__('From Top', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-load-more-button.button--loading .button__loader' => 'top: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Button Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_read_more_button_style',
			[
				'label' => esc_html__('Read More Button', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'eael_read_more_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_read_more_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_read_more_typography',
				'selector' => '{{WRAPPER}} .eael-content-timeline-content .eael-read-more, {{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more',
			]
		);

		$this->start_controls_tabs('eael_read_more_tabs');

		// Normal State Tab
		$this->start_controls_tab('eael_read_more_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_read_more_normal_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more, {{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_read_more_normal_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#3CCD94',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_read_more_normal_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-content-timeline-content .eael-read-more, {{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more',
			]
		);

		$this->add_control(
			'eael_read_more_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		// Hover State Tab
		$this->start_controls_tab('eael_read_more_hover', ['label' => esc_html__('Hover', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_read_more_hover_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_read_more_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#bac4cb',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_read_more_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-content-timeline-content .eael-read-more:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more:hover' => 'border-color: {{VALUE}};',
				],
			]

		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_read_more_shadow',
				'selector' => '{{WRAPPER}} .eael-content-timeline-content .eael-read-more, {{WRAPPER}} .eael-horizontal-timeline-item .eael-read-more',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();
	}


	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$settings = Helper::fix_old_query($settings);
		$args = Helper::get_query_args($settings);

		$slide_to_scroll = [
			'desktop' => ! empty( $settings['eael_content_timeline_slides_to_scroll'] ) ? esc_attr( $settings['eael_content_timeline_slides_to_scroll'] ) : 1,
			'tablet' => ! empty( $settings['eael_content_timeline_slides_to_scroll_tablet'] ) ? esc_attr( $settings['eael_content_timeline_slides_to_scroll_tablet'] ) : 1,
			'mobile' => ! empty( $settings['eael_content_timeline_slides_to_scroll_mobile'] ) ? esc_attr( $settings['eael_content_timeline_slides_to_scroll_mobile'] ) : 1,
		];
		
		$this->add_render_attribute(
			'timeline-wrapper',
			[
				'id'	=> 'eael-content-timeline-' . esc_attr($this->get_id()),
				'class'	=> [
					'content-timeline-layout-' . esc_attr($settings['content_timeline_layout']),
					'date-position-' . esc_attr($settings['date_position']),
					'horizontal-timeline-wrapper'
				],
				'data-slide_to_scroll' => json_encode( $slide_to_scroll ),
			]
		);
		$template_layout = $this->get_settings('eael_dynamic_template_Layout');
		$template = $this->get_template( esc_html( $template_layout ) );
		?>
		<div <?php echo $this->get_render_attribute_string('timeline-wrapper'); ?>>
			<div class="eael-content-timeline-container">
				<div class="eael-content-timeline-container">
					<?php
					if ('dynamic' === $settings['eael_content_timeline_choose']) :
						if (file_exists($template)) {
							$query = new \WP_Query($args);
							if ($query->have_posts()) {
								if( 'horizontal' === $template_layout ) {
									include($template);
								} else {
									while ($query->have_posts()) {
										$query->the_post();
										$content = $this->dynamic_content_manager( $query->ID, $settings ) ;
										include($template);
									}
								}
							} else {
								_e('<p class="no-posts-found">No posts found!</p>', 'essential-addons-elementor');
							}
							wp_reset_postdata();
						} else {
							_e('<p class="no-posts-found">No layout found!</p>', 'essential-addons-elementor');
						}

					elseif ('custom' === $settings['eael_content_timeline_choose']) :
						if (file_exists($template)) :
							if( 'horizontal' === $template_layout ) {
								include($template);
							} else {
								foreach ($settings['eael_coustom_content_posts'] as $custom_content) : ?>
									<?php
									$content = $this->custom_content_manager( $custom_content, $settings );
									?>
									<?php 
									include($template);
								endforeach; 
							}
						endif;
					endif; ?>
				</div>
			</div>
		</div>
	<?php
	}

	protected function dynamic_content_manager( $post_ID, $settings )
	{
		$excerpt      = get_the_excerpt( $post_ID );
		$the_content  = get_the_content( $post_ID );
		$nofollow     = $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '';
		$target_blank = $settings['read_more_link_target_blank'] ? 'target="_blank"' : '';
		$circle_icon  = $settings['eael_show_image_or_icon'] !== 'icon' ? '' : ( isset( $settings['__fa4_migrated']['eael_content_timeline_circle_icon_new'] ) || empty( $settings['eael_content_timeline_circle_icon'] ) ? \Essential_Addons_Elementor\Classes\Helper::get_render_icon( $settings['eael_content_timeline_circle_icon_new'] ) : '<i class="'.esc_attr( $settings['eael_content_timeline_circle_icon'] ).'"></i>' );

		$content = [
			'title'               => get_the_title( $post_ID ),
			'permalink'           => get_the_permalink( $post_ID ),
			'date'                => get_the_date(),
			'excerpt'             => empty( $settings['eael_excerpt_length'] ) ? '<p>' . strip_shortcodes( $excerpt ? $excerpt : $the_content ) . '</p>' : '<p>' . wp_trim_words( strip_shortcodes( $excerpt ? $excerpt : $the_content ), $settings['eael_excerpt_length'], $settings['excerpt_expanison_indicator'] ) . '</p>',
			'image'               => '',
			'read_more_btn'       => '',
			'nofollow'            => $settings['title_link_nofollow'] ? 'rel="nofollow"' : '',
			'target_blank'        => $settings['title_link_target_blank'] ? 'target="_blank"' : '',
			'post_thumbnail'      => $settings['eael_show_image'] == 'yes' ? get_the_post_thumbnail( $post_ID, $settings['image_size'] ) : '',
			'image_linkable'       => $settings['eael_image_linkable'],
			'image_link_nofollow' => empty( $settings['thumbnail_link_nofollow'] ) ? '' : 'rel="nofollow"',
			'image_link_target'   => empty( $settings['thumbnail_link_target_blank'] ) ? '' : 'target="_blank"',
		];

		if( "img" === $settings["eael_show_image_or_icon"] ) {
		    $content['image'] = '<img src="'. esc_url( $settings['eael_icon_image']['url'] ).'" alt="'.esc_attr(get_post_meta($settings['eael_icon_image']['id'], '_wp_attachment_image_alt', true)).'">';
		}

		if( 'icon' === $settings['eael_show_image_or_icon'] ) {
		    if( isset($circle_icon['url'])) {
		        $content['image'] = '<img class="content-timeline-bullet-svg" src="'.esc_attr( $circle_icon['url'] ).'" alt="'.esc_attr(get_post_meta($circle_icon['id'], '_wp_attachment_image_alt', true)).'"/>';
		    }else {
		        $content['image'] = $circle_icon;
		    }
		}

		if( 'yes' == $settings['eael_show_read_more'] && !empty( $settings['eael_read_more_text'] ) ) {
		    $content['read_more_btn'] = '<a href="'.esc_url( $content['permalink'] ).'" class="eael-read-more"' . $nofollow . '' . $target_blank .'>'.esc_html__( $settings['eael_read_more_text'], 'essential-addons-elementor' ).'</a>';
		}

		return $content;
	}

	protected function custom_content_manager( $custom_content, $settings )
	{
		// button url
		$url = isset($custom_content['eael_read_more_text_link']['url'])?$custom_content['eael_read_more_text_link']['url']:'';

		$icon_migrated = isset($settings['__fa4_migrated']['eael_custom_content_timeline_circle_icon_new']);
		$icon_is_new = empty($settings['eael_custom_content_timeline_circle_icon']);
		$content = [
			'title'          => $custom_content['eael_custom_title'],
			'permalink'      => isset( $custom_content['eael_read_more_text_link']['url'] ) ? $custom_content['eael_read_more_text_link']['url'] : '',
			'date'           => $custom_content['eael_custom_post_date'],
			'excerpt'        => $custom_content['eael_custom_excerpt'],
			'image'          => '',
			'read_more_btn'  => '',
			'nofollow'       => ! empty( $custom_content['eael_read_more_text_link']['nofollow'] ) ? 'rel="nofollow"' : '',
			'target_blank'   => ! empty( $custom_content['eael_read_more_text_link']['is_external'] ) ? 'target="_blank"' : '',
			'post_thumbnail' => '',
		];

		if ('img' === $custom_content['eael_show_custom_image_or_icon']) :
			$content['image'] = '<img src="'. esc_url($custom_content['eael_custom_icon_image']['url']) .'" style="width: '.$custom_content['eael_custom_icon_image_size'].'px;" alt="
			'.esc_attr(get_post_meta($custom_content['eael_custom_icon_image']['id'], '_wp_attachment_image_alt', true)) .'">';
		endif;

		if ('icon' === $custom_content['eael_show_custom_image_or_icon']) :
			if ($icon_migrated || $icon_is_new) {
					if (isset($custom_content['eael_custom_content_timeline_circle_icon_new']['value']['url'])) :
					$content['image'] = '<img class="content-timeline-bullet-svg" src="'. esc_attr($custom_content['eael_custom_content_timeline_circle_icon_new']['value']['url']) .'" alt="'. esc_attr(get_post_meta($custom_content['eael_custom_content_timeline_circle_icon_new']['value']['id'], '_wp_attachment_image_alt', true)) .'" />';
				else :
					$content['image'] = \Essential_Addons_Elementor\Classes\Helper::get_render_icon( $custom_content['eael_custom_content_timeline_circle_icon_new'] );# '<i class="'. esc_attr($custom_content['eael_custom_content_timeline_circle_icon_new']['value']) .'"></i>';
				endif;
			} else {
				$content['image'] = '<i class="'. esc_attr($custom_content['eael_custom_content_timeline_circle_icon']) .'"></i>';
			}
		endif;
		
		if ('1' == $custom_content['eael_show_custom_read_more'] && !empty($custom_content['eael_show_custom_read_more_text'])) :
			$content['read_more_btn'] = '<a href="'. esc_url($custom_content['eael_read_more_text_link']['url']) .'" class="eael-read-more" '. $content['target_blank'] .' '. $content['nofollow'] .'>'. esc_html__($custom_content['eael_show_custom_read_more_text'], 'essential-addons-elementor') .'</a>';
		endif;

		return $content;
	}

	public function print_horizontal_timeline_content( $settings, $query, $part = 'top' ){
		$available_parts = ['top', 'middle', 'bottom'];
		if( !in_array( $part, $available_parts) ) {
			return false;
		}

		$counter = 0;
		if( 'dynamic' === $settings['eael_content_timeline_choose'] ){
			while ($query->have_posts()) {
				$counter++;
				$part1 = $this->fetch_horizontal_layout_part_name( $settings, $counter, $part );

				$query->the_post();
				$content = $this->dynamic_content_manager( $query->ID, $settings ) ;
				$this->print_horizontal_timeline_content_inner( $settings, $content,$part1, $counter );
			}
		} else if( 'custom' === $settings['eael_content_timeline_choose'] ) {
			foreach ($settings['eael_coustom_content_posts'] as $custom_content) {
				$counter++;
				$part1 = $this->fetch_horizontal_layout_part_name( $settings, $counter, $part );
				
				$content = $this->custom_content_manager( $custom_content, $settings );
				$this->print_horizontal_timeline_content_inner( $settings, $content, $part1, $counter );
			}
		}
	}

	public function print_horizontal_timeline_content_inner( $settings, $content, $part = 'top', $counter = 0 ){
		$available_parts = ['top', 'middle', 'bottom'];
		if( !in_array( $part, $available_parts) ) {
			return false;
		}
		$show_date_inside = ! empty( $settings['date_position_horizontal'] ) && 'inside' === $settings['date_position_horizontal'] ? 1 : 0;
		$horizontal_layout = ! empty( $settings['content_timeline_layout_horizontal'] ) ? esc_html( $settings['content_timeline_layout_horizontal'] ) : esc_html('middle');
		$horizontal_layout_middle = 'middle' === $horizontal_layout ? 1 : 0;
		
		$arrow_direction = esc_html( $horizontal_layout );
						
		if( 'middle' === $horizontal_layout ) {
			$arrow_direction = $counter % 2 === 1 ? 'top' : 'bottom';
		}
						
		switch ( $part ) {
			case 'top':
				?>
				<div class="eael-horizontal-timeline-item <?php echo esc_attr( $arrow_direction ) ?> <?php echo 1 === $counter ? esc_attr('is-active') : '' ?>">
					<div class="eael-horizontal-timeline-item__card <?php echo esc_attr( $arrow_direction ) ?>">
						<div class="eael-horizontal-timeline-item__card-inner">
							<?php if ( $show_date_inside && !$horizontal_layout_middle ) : ?>
								<div class="eael-horizontal-timeline-item__meta">
									<?php echo Helper::eael_wp_kses( $content['date'] ); ?>
								</div>
							<?php endif; ?>

							<?php 
							if ( 'yes' == $settings['eael_show_title'] ) {
								echo '<' . Helper::eael_pro_validate_html_tag( $settings['title_tag'] ) . ' class="eael-horizontal-timeline-item__card-title"><a href="' . esc_url( $content['permalink'] ) . '"' . $content['nofollow'] . '' . $content['target_blank'] . '>' . esc_html( $content['title'] ) . '</a></' . Helper::eael_pro_validate_html_tag( $settings['title_tag'] ) . '>';
							}

							if ( ! empty( $content['image_linkable'] ) && $content['image_linkable'] === 'yes' ) {
								echo '<a href="' . esc_url( $content['permalink'] ) . '"' . $content['image_link_nofollow'] . '' . $content['image_link_target'] . '>';
							}

							printf( '%s', $content['post_thumbnail'] );

							if ( ! empty( $content['image_linkable'] ) && $content['image_linkable'] === 'yes' ) {
								echo '</a>';
							}
							?>
							<div class="eael-horizontal-timeline-item__card-desc">
								<?php 
								if ( 'yes' == $settings['eael_show_excerpt'] ) {
									echo Helper::eael_wp_kses( $content['excerpt'] );
								}

								printf( '%s', $content['read_more_btn'] );
								?>
							</div>
						</div>
						
						<div class="eael-horizontal-timeline-item__card-arrow <?php echo esc_attr( $arrow_direction ) ?>"></div>
					</div>
				</div>
				<?php
				break;
			case 'middle':
				?>
				<div class="eael-horizontal-timeline-item <?php echo 1 === $counter ? esc_attr('is-active') : '' ?>">
					<div class="eael-horizontal-timeline-item__point">
						<div class="eael-horizontal-timeline-item__point-content">
							<span class="eael-elements-icon"> <?php printf( '%s', $content['image'] ); ?> </span>
						</div>
					</div>
				</div>
				<?php 
				break;
			case 'bottom':
				?>
				<div class="eael-horizontal-timeline-item <?php echo 1 === $counter ? esc_attr('is-active') : '' ?>">
					<?php if ( !$show_date_inside || $horizontal_layout_middle ) : ?>
						<div class="eael-horizontal-timeline-item__meta">
							<?php echo Helper::eael_wp_kses( $content['date'] ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php 
				break;
		}
	}

	public function fetch_horizontal_layout_part_name( $settings, $counter, $part ){
		$part1 = $part;
		$horizontal_layout = ! empty( $settings['content_timeline_layout_horizontal'] ) ? esc_html( $settings['content_timeline_layout_horizontal'] ) : esc_html( 'center' );
		
		if( 'middle' === $horizontal_layout ) {
			if ( 'top' === $part ) {
				$part1 =  $counter % 2 === 1 ? 'top' : 'bottom';
			} elseif( 'bottom' === $part ) {
				$part1 =  $counter % 2 === 1 ? 'bottom' : 'top';
			} 
		}

		return $part1;
	}
}
