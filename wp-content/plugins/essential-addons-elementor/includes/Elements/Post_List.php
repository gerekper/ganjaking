<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use \Elementor\Plugin;
use \Elementor\Icons_Manager;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Widget_Base;
use \Elementor\Utils;

use \Essential_Addons_Elementor\Pro\Classes\Helper;


if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Post_List extends Widget_Base
{
    use \Essential_Addons_Elementor\Traits\Template_Query;

    protected $all_terms = [];
    protected $tax_query = [];
    protected $page_id;

    public function get_name()
    {
        return 'eael-post-list';
    }

    public function get_title()
    {
        return __('Smart Post List', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-smart-post-list';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'smart post list',
            'ea smart post list',
            'ea post list',
            'ea post grid',
            'ea smart post grid',
            'blog post',
            'bloggers',
            'blog',
            'featured post',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/smart-post-list/';
    }

    public function get_style_depends()
    {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim',
        ];
    }

    public function get_script_depends()
    {
        return [
            'font-awesome-4-shim',
        ];
    }

    protected function post_list_layout_controls()
    {
        $this->start_controls_section(
            'eael_section_post_list_layout',
            [
                'label' => __('Layout Settings', 'essential-addons-elementor'),
            ]
        );
        if ($this->get_name() === 'eael-post-list') {
            $this->add_control(
                'eael_post_list_layout_type',
                [
                    'label' => __('Layout Type', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $this->get_template_list_for_dropdown(),
                    'default' => 'default',
                ]
            );

            $this->add_control(
                'eael_enable_ajax_post_search',
                [
                    'label' => __('Enable Ajax Post Search', 'essential-addons-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'false',
                    'label_on' => __('Yes', 'essential-addons-elementor'),
                    'label_off' => __('No', 'essential-addons-elementor'),
                    'return_value' => 'yes',
                    'condition' => [
                        'post_type!' => 'by_id',
                        'eael_post_list_layout_type' => 'advanced',
                    ],
                ]
            );
        }

        $this->add_control(
            'eael_post_list_topbar',
            [
                'label' => __('Show Top Bar', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_post_list_topbar_title',
            [
                'label' => esc_html__('Title Text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => false,
                'default' => esc_html__('Recent Posts', 'essential-addons-elementor'),
                'condition' => [
                    'eael_post_list_topbar' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_post_list_topbar_term_all_text',
            [
                'label' => esc_html__('Change All Text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => false,
                'default' => esc_html__('All', 'essential-addons-elementor'),
                'condition' => [
                    'eael_post_list_topbar' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_post_list_terms',
            [
                'label' => __('Show Category Filter', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
                'condition' => [
                    'eael_post_list_topbar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_pagination',
            [
                'label' => __('Show Navigation', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_post_list_pagination_prev_icon_new',
            [
                'label' => esc_html__('Prev Post Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_adv_accordion_icon',
                'default' => [
                    'value' => 'fas fa-angle-left',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'eael_post_list_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_pagination_next_icon_new',
            [
                'label' => esc_html__('Next Post Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_adv_accordion_icon',
                'default' => [
                    'value' => 'fas fa-angle-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'eael_post_list_pagination' => 'yes',
                ],
            ]
        );

	    $this->add_control(
		    'eael_post_list_featured_area',
		    [
			    'label'        => __( 'Show Featured Post', 'essential-addons-elementor' ),
			    'type'         => Controls_Manager::SWITCHER,
			    'default'      => 'yes',
			    'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
			    'label_off'    => __( 'No', 'essential-addons-elementor' ),
			    'return_value' => 'yes',
			    'condition'    => [
				    'eael_post_list_layout_type!' => 'advanced',
			    ],
		    ]
	    );

	    $this->add_control(
		    'eael_post_list_scroll_on_pagination',
		    [
			    'label'        => __( 'Scroll to Top', 'essential-addons-elementor' ),
			    'description'  => __( 'Enabling it allows the widget to scroll to top when navigating to the next page through pagination.', 'essential-addons-elementor' ),
			    'type'         => Controls_Manager::SWITCHER,
			    'default'      => 'no',
			    'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
			    'label_off'    => __( 'No', 'essential-addons-elementor' ),
			    'return_value' => 'yes',
		    ]
	    );

        $this->add_control(
            'eael_post_list_scroll_offset',
            [
                'label' => __('Scroll Top Offset', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '100',
                'min' => '0',
                'max' => '500',
                'condition' => [
                    'eael_post_list_scroll_on_pagination' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        do_action('eael/controls/query', $this);

        $this->post_list_layout_controls();

        /**
         * Post List Controls!
         */
        $this->start_controls_section(
            'eael_section_post_list_featured_post_layout',
            [
                'label' => __('Featured Post Settings', 'essential-addons-elementor'),
                'condition' => [
                    'eael_post_list_featured_area' => 'yes',
                    'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'featured_posts',
            [
                'label' => __('Featured Post', 'essential-addons-elementor'),
                'label_block' => true,
                'type' => 'eael-select2',
                'source_name' => 'post_type',
                'source_type' => 'any',
                'condition' => [
                    'eael_post_list_featured_area' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'featured_image_size',
                'exclude' => ['custom'],
                'default' => 'large',
            ]
        );

        $this->add_responsive_control(
            'eael_post_list_featured_height',
            [
                'label' => esc_html__('Featured Post Min Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 450,
                ],
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap' => 'min-height: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_post_equal_height',
            [
                'label' => __('Equal Post Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'eael_post_list_featured_equal_height',
            [
                'label' => esc_html__('Post Min Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 450,
                ],
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-post' => 'min-height: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-post-list-post .eael-post-list-featured-inner' => 'min-height: {{SIZE}}px;',
                ],
                'condition' => [
                    'eael_post_list_post_equal_height' => 'yes',
                    'eael_post_list_layout_type!' => 'advanced',
                ]
            ]
        );

        $this->add_responsive_control(
            'eael_post_list_featured_width',
            [
                'label' => esc_html__('Featured Post Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 30,
                ],
                'range' => [
                    '%' => [
                        'max' => 100,
                    ],
                ],
				'tablet_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap' => 'flex: 0 0 {{SIZE}}%;',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_list_width',
            [
                'label' => esc_html__('List Area Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 70,
                ],
                'range' => [
                    '%' => [
                        'max' => 100,
                    ],
                ],
				'tablet_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-posts-wrap' => 'flex: 0 0 {{SIZE}}%;',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_featured_meta',
            [
                'label' => __('Show Meta', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_title',
            [
                'label' => __('Show Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_excerpt',
            [
                'label' => __('Show Excerpt', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_post_list_featured_excerpt_length',
            [
                'label' => __('Excerpt Words', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '8',
                'condition' => [
                    'eael_post_list_featured_excerpt' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_list_post_layout',
            [
                'label' => __('List Post Settings', 'essential-addons-elementor'),
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_columns',
            [
                'label'       => esc_html__('Post List Column(s)', 'essential-addons-elementor'),
                'type'        => Controls_Manager::SELECT,
                'default'     => '2',
                'label_block' => false,
                'options'     => [
                    '1' => esc_html__('1 Column', 'essential-addons-elementor'),
                    '2' => esc_html__('2 Columns', 'essential-addons-elementor'),
                    '3' => esc_html__('3 Columns', 'essential-addons-elementor'),
                    '4' => esc_html__('4 Columns', 'essential-addons-elementor'),
                ],
				'widescreen_default'   => 3,
				'tablet_extra_default' => 2,
				'tablet_default'       => 1,
				'mobile_default'       => 1,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-posts-wrap' => 'grid-template-columns: repeat({{VALUE}}, 1fr)',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_post_feature_image',
            [
                'label' => __('Show Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'eael_post_featured_image',
                'exclude' => ['custom'],
                'default' => 'large',
                'condition' => [
                    'eael_post_list_post_feature_image' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_post_meta',
            [
                'label' => __('Show Meta', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_post_list_post_title',
            [
                'label' => __('Show Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_post_list_title_tag',
            [
                'label' => __('Title Tag', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h2' => __('h1', 'essential-addons-elementor'),
                    'h2' => __('h2', 'essential-addons-elementor'),
                    'h3' => __('h3', 'essential-addons-elementor'),
                    'h4' => __('h4', 'essential-addons-elementor'),
                    'h5' => __('h5', 'essential-addons-elementor'),
                    'h6' => __('h6', 'essential-addons-elementor'),
                    'p' => __('p', 'essential-addons-elementor'),
                    'span' => __('span', 'essential-addons-elementor'),
                    'div' => __('div', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_post_list_post_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_post_excerpt',
            [
                'label' => __('Show Excerpt', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_post_list_post_excerpt_length',
            [
                'label' => __('Excerpt Words', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '12',
                'condition' => [
                    'eael_post_list_post_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_excerpt_expanison_indicator',
            [
                'label' => esc_html__('Expansion Indicator', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => false,
                'default' => esc_html__('...', 'essential-addons-elementor'),
                'condition' => [
                    'eael_post_list_post_excerpt' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_show_read_more_button',
            [
                'label' => __('Show Read More', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'essential-addons-elementor'),
                'label_off' => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'eael_post_list_post_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_read_more_text',
            [
                'label' => esc_html__('Label Text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => false,
                'default' => esc_html__('Read More', 'essential-addons-elementor'),
                'condition' => [
                    'eael_post_list_post_excerpt' => 'yes',
                    'eael_show_read_more_button' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_post_list_author_meta',
            [
                'label' => __('Show Author Meta', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_post_cat',
            [
                'label' => __('Show Category', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_post_list_post_cat_max_length',
            [
                'label' => __('Max Items to Show', 'essential-addons-for-elementor-lite'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => __('1', 'essential-addons-for-elementor-lite'),
                    2 => __('2', 'essential-addons-for-elementor-lite'),
                    3 => __('3', 'essential-addons-for-elementor-lite'),
                ],
                'default' => 1,
                'condition' => [
                    'eael_post_list_post_cat' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_post_cat_separator',
            [
                'label' => esc_html__('Items Separator', 'essential-addons-for-elementor-lite'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => esc_html__('', 'essential-addons-for-elementor-lite'),
                'condition' => [
                    'eael_post_list_post_cat' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Links
         */

        $this->start_controls_section(
            'section_post_list_links',
            [
                'label' => __('Links', 'essential-addons-elementor'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                       [
                          'name' => 'eael_post_list_post_feature_image',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       [
                          'name' => 'eael_post_list_post_title',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       [
                          'name' => 'eael_show_read_more_button',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       
                    ],
                 ],
            ]
        );

        $this->add_control(
            'image_link',
            [
                'label' => __('Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_post_list_post_feature_image' => 'yes',
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'image_link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_post_list_post_feature_image' => 'yes',
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'image_link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_post_list_post_feature_image' => 'yes',
                    'eael_post_list_layout_type' => 'advanced',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'title_link',
            [
                'label' => __('Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_post_list_post_title' => 'yes',
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
                    'eael_post_list_post_title' => 'yes',
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
                    'eael_post_list_post_title' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_list_style',
            [
                'label' => __('Post List Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'eael_post_list_container_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_container_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_post_list_container_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-list-container',
            ]
        );
        $this->add_control(
            'eael_post_list_container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_post_list_container_shadow',
                'selector' => '{{WRAPPER}} .eael-post-list-container',
            ]
        );

        $this->add_control(
            'eael_post_list_adv_grid_gap',
            [
                'label' => esc_html__('Grid Gap', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-posts-wrap' => 'grid-gap: {{SIZE}}px;',
                ],
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_list_topbar_style',
            [
                'label' => __('Topbar Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_topbar' => 'yes',
                ],
            ]
        );

	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    [
			    'name'           => 'eael_post_list_topbar_background',
			    'types'          => [ 'classic', 'gradient' ],
			    'selector'       => '{{WRAPPER}} .eael-post-list-header',
			    'fields_options' => [
				    'classic' => []
			    ],
			    'exclude'        => [ 'image' ]
		    ]
	    );

	    $this->add_group_control(
		    Group_Control_Border::get_type(),
		    [
			    'name'      => 'eael_post_list_topbar_border',
			    'label'     => esc_html__( 'Topbar Border', 'essential-addons-elementor' ),
			    'selector'  => '{{WRAPPER}} .eael-post-list-header',
			    'separator' => 'before'
		    ]
	    );

        $this->add_control(
            'eael_post_list_topbar_bottom_spacing',
            [
                'label' => esc_html__('Bottom Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container .eael-post-list-header' => 'margin-bottom: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'eael_section_post_list_topbar_tag_style',
            [
                'label' => esc_html__('Title Tag', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_bg_color',
            [
                'label' => __('Title Tag Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e23a47',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .header-title .title' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_color',
            [
                'label' => __('Title Tag Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .header-title .title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_section_post_list_topbar_tag_typo',
                'label' => __('Tag Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-header .header-title .title',
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_category_style',
            [
                'label' => esc_html__('Category Filter', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_section_post_list_topbar_category_typo',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-header .post-categories a',
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_category_background_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_category_color',
            [
                'label' => __('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#5a5a5a',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_category_active_background_color',
            [
                'label' => __('Active Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a.active, {{WRAPPER}} .eael-post-list-header .post-categories a:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_topbar_category_active_color',
            [
                'label' => __('Active Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F56A6A',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a.active, {{WRAPPER}} .eael-post-list-header .post-categories a:hover' => 'color: {{VALUE}} !important',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_section_post_list_topbar_category_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_section_post_list_topbar_category_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-header .post-categories a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_list_navigation_style',
            [
                'label' => __('Navigation Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_pagination' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_list_nav_alignment',
            [
                'label' => __('Alignment', 'essential-addons-elementor'),
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
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination' => 'text-align: {{VALUE}};',
                ],
            ]
        );

	    $this->add_control(
		    'eael_section_post_list_nav_icon_size',
		    [
			    'label' => esc_html__('Icon Size', 'essential-addons-elementor'),
			    'type' => Controls_Manager::SLIDER,
			    'default' => [
				    'size' => 14,
			    ],
			    'range' => [
				    'px' => [
					    'max' => 50,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .post-list-pagination .btn-next-post i, {{WRAPPER}} .post-list-pagination .btn-prev-post i' => 'font-size: {{SIZE}}px;',
				    '{{WRAPPER}} .post-list-pagination .btn-prev-post svg, {{WRAPPER}} .post-list-pagination .btn-next-post svg' => 'width: {{SIZE}}px;height: {{SIZE}}px;line-height: {{SIZE}}px;',
			    ],
		    ]
	    );

        $this->add_control(
            'eael_section_post_list_nav_icon_color',
            [
                'label' => __('Icon Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222',
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-next-post svg, {{WRAPPER}} .post-list-pagination .btn-prev-post svg' => 'fill: {{VALUE}}',
                ],

            ]
        );
        $this->add_control(
            'eael_section_post_list_nav_icon_bg_color',
            [
                'label' => __('Icon Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_nav_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-next-post:hover svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post:hover svg' => 'fill: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_nav_icon_hover_bg_color',
            [
                'label' => __('Icon Background Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222',
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post:hover' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_section_post_list_nav_icon_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_section_post_list_nav_icon_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'eael_section_post_list_nav_icon_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-list-pagination .btn-next-post' => 'border-radius: {{SIZE}}px;',
                    '{{WRAPPER}} .post-list-pagination .btn-prev-post' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'eael_post_list_featured_typography',
            [
                'label' => __('Featured Post Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_featured_thumb_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 3,
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
				    '{{WRAPPER}} .eael-post-list-featured-wrap .eael-post-list-featured-inner, {{WRAPPER}} .eael-post-list-featured-wrap .eael-post-list-featured-inner:after'
				    => 'border-radius: {{SIZE}}px;',
				    '{{WRAPPER}} .eael-post-list-featured-wrap .eael-post-list-featured-inner img'=> 'border-radius: {{SIZE}}px {{SIZE}}px 0 0;',
			    ],
                'condition' => [
                    'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_post_list_featured_box_shadow',
                'selector' => '{{WRAPPER}} .eael-post-list-featured-wrap .eael-post-list-featured-inner',
                'condition' => [
                    'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_featured_title_settings',
            [
                'label' => __('Title Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_title_color',
            [
                'label' => __('Title Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title, {{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_featured_title_hover_color',
            [
                'label' => __('Title Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#92939b',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title:hover, {{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_featured_title_alignment',
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
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_featured_title_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title, {{WRAPPER}} .eael-post-list-featured-wrap .featured-content .eael-post-list-title a',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_excerpt_style',
            [
                'label' => __('Excerpt Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_excerpt_color',
            [
                'label' => __('Excerpt Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f8f8f8',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content p' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_featured_excerpt_alignment',
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
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content p' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_featured_excerpt_typography',
                'label' => __('Excerpt Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content p',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_meta_style',
            [
                'label' => __('Meta Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_post_list_featured_meta_color',
            [
                'label' => __('Meta Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .meta' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_featured_meta_alignment',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .meta' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_featured_meta_typography',
                'label' => __('Date Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-featured-wrap .featured-content .meta',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'eael_post_list_typography',
            [
                'label' => __('Post Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
		    'eael_post_list_border_radius',
		    [
			    'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
			    'type' => Controls_Manager::SLIDER,
			    'default' => [
				    'size' => 3,
			    ],
			    'range' => [
				    'px' => [
					    'max' => 50,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-posts-wrap .eael-post-list-post' => 'border-radius: {{SIZE}}px;',
				    '{{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-posts-wrap .eael-post-list-post' => 'border-radius: {{SIZE}}px;',
				    '{{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-posts-wrap .eael-post-list-post .eael-post-list-featured-inner, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-posts-wrap .eael-post-list-post .eael-post-list-featured-inner:after' => 'border-radius: {{SIZE}}px;',
				    '{{WRAPPER}} .eael-post-list-container .eael-post-list-thumbnail' => 'border-radius: {{SIZE}}px;',
			    ],
			    'condition' => [
                    'eael_post_list_layout_type!' => 'advanced',
			    ],
		    ]
	    );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_post_list_box_shadow',
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-posts-wrap .eael-post-list-post, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-posts-wrap .eael-post-list-post, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-posts-wrap .eael-post-list-post',
                'condition' => [
                    'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_thumbnail_settings',
            [
                'label' => __('Thumbnail Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_margin_bottom',
            [
                'label' => esc_html__('Thumbnail Space', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 30,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-thumbnail' => 'margin-bottom: {{SIZE}}px;',
                ],
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->add_control(
            'eael_post_list_title_settings',
            [
                'label' => __('Title Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_list_title_color',
            [
                'label' => __('Title Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content .eael-post-list-title, {{WRAPPER}} .eael-post-list-content .eael-post-list-title a' => 'color: {{VALUE}};',
                ],

            ]
        );
        $this->add_control(
            'eael_post_list_title_hover_color',
            [
                'label' => __('Title Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e65a50',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content .eael-post-list-title:hover, {{WRAPPER}} .eael-post-list-content .eael-post-list-title a:hover' => 'color: {{VALUE}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'eael_post_list_title_alignment',
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
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content .eael-post-list-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_title_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-content .eael-post-list-title, {{WRAPPER}} .eael-post-list-content .eael-post-list-title a',
            ]
        );
        $this->add_control(
            'eael_post_list_excerpt_style',
            [
                'label' => __('Excerpt Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_post_list_excerpt_color',
            [
                'label' => __('Excerpt Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#4d4d4d',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content p' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_excerpt_alignment',
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
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content p' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_excerpt_typography',
                'label' => __('Excerpt Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-content p',
            ]
        );
        $this->add_control(
            'eael_post_list_meta_style',
            [
                'label' => __('Meta Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_post_list_meta_color',
            [
                'label' => __('Meta Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#aaa',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content .meta' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_post_list_meta_alignment',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-content .meta' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_list_meta_typography',
                'label' => __('Meta Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-content .meta',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'card_style_section',
            [
                'label' => __('Card Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->start_controls_tabs('post_list_advanced_card_style');

        $this->start_controls_tab(
            'post_list_advanced_card_normal',
            [
                'label' => esc_html__('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_responsive_control(
            'card_box_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post .eael-post-list-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'card_background_normal',
                'label' => __('Background', 'essential-addons-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post-inner:after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border_normal',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post-inner:after',
            ]
        );

        $this->add_responsive_control(
            'card_box_border_radius_normal',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post-inner:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow_normal',
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post-inner:after',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'post_list_advanced_card_hover',
            [
                'label' => esc_html__('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'card_background_hover',
                'label' => __('Background', 'essential-addons-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post:hover .eael-post-list-post-inner:after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border_hover',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post:hover .eael-post-list-post-inner:after',
            ]
        );

        $this->add_control(
            'card_box_border_radius_hover',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post:hover .eael-post-list-post-inner:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow_hover',
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post:hover .eael-post-list-post-inner:after',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'author_meta_style_section',
            [
                'label' => __('Author Meta', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                    'eael_post_list_author_meta!' => '',
                ],
            ]
        );

        $this->add_control(
            'author_photo_heading',
            [
                'label' => esc_html__('Author Photo', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'author_image_size',
            [
                'label' => esc_html__('Image Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 44,
                ],
                'range' => [
                    '%' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-post .boxed-meta .author-meta .author-photo' => 'flex-basis:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-post .boxed-meta .author-meta .author-photo' => 'width:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-posts-wrap .eael-post-list-post .eael-post-list-content .boxed-meta .author-meta .author-photo' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'author_image_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-post .boxed-meta .author-meta .author-photo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'author_name_heading',
            [
                'label' => esc_html__('Author Name', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_name_typography',
                'label' => __('Title Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post .eael-post-list-content .boxed-meta .author-info h5',
            ]
        );

        $this->add_control(
            'author_meta_name_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post .eael-post-list-content .boxed-meta .author-info h5 > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'author_name_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-post .eael-post-list-content .boxed-meta .author-info h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'author_meta_date_heading',
            [
                'label' => esc_html__('Date', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_meta_date_typography',
                'label' => __('Date Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-posts-wrap .eael-post-list-post .eael-post-list-content .boxed-meta .author-meta .author-info > a p',
            ]
        );

        $this->add_control(
            'post_list_author_meta_date_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-posts-wrap .eael-post-list-post .eael-post-list-content .boxed-meta .author-meta .author-info a > p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'category_style_section',
            [
                'label' => __('Category Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    //                    'eael_post_list_layout_type' => 'advanced',
                    'eael_post_list_post_cat!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap a, {{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a',
            ]
        );

        $this->start_controls_tabs(
            'post_list_category_tabs',
            [
                'condition' => [
                    // 'eael_post_list_layout_type!' => 'advanced',
                ],
            ]
        );

        $this->start_controls_tab('post_list_category_tab_normal', ['label' => esc_html__('Normal', 'essential-addons-for-elementor')]);

        $this->add_control(
            'post_list_category_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#8040FF',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'post_list_category_bg_color',
            [
                'label' => esc_html__('Background', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'eael_post_list_category_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap' => 'border-radius: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('post_list_category_tab_hover', ['label' => esc_html__('Hover', 'essential-addons-for-elementor')]);

        $this->add_control(
            'post_list_category_color_hover',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#543bc2',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_list_category_bg_color_hover',
            [
                'label' => esc_html__('Background', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap a:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a:hover, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'category_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .eael-post-list-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .eael-post-list-content .meta-categories a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-list-container.layout-default .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-2 .featured-content .meta-categories a, {{WRAPPER}} .eael-post-list-container.layout-preset-3 .featured-content .meta-categories a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'category_box_shadow',
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-content .boxed-meta .meta-categories .meta-cats-wrap',
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'form_style_section',
            [
                'label' => __('Search Form Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_post_list_layout_type' => 'advanced',
                    'eael_enable_ajax_post_search!' => '',
                ],
            ]
        );
        $this->add_responsive_control(
            'post_list_form_width',
            [
                'label' => esc_html__('Form Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 470,
                ],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-header .post-list-ajax-search-form form' => 'width: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'post_list_form_border_color',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-header .post-list-ajax-search-form form input',
            ]
        );
        $this->add_control(
            'post_list_form_button_color',
            [
                'label' => __('Search Icon Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-list-container.layout-advanced .eael-post-list-header .post-list-ajax-search-form form i.fa-search' => 'color: {{VALUE}}',
                ],

            ]
        );
        $this->end_controls_section();

        /**
         * Read More Button Style Controls
         */
        do_action('eael/controls/read_more_button_style', $this);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $settings = Helper::fix_old_query($settings);
        $args = Helper::get_query_args($settings);
        if (!isset($this->page_id)) {
            if ( Plugin::$instance->documents->get_current() ) {
                $this->page_id = Plugin::$instance->documents->get_current()->get_main_id();
            }else{
                $this->page_id = null;
            }
        }
        $data_settings = [
            'eael_post_list_post_feature_image' => $settings['eael_post_list_post_feature_image'],
            'eael_post_list_post_meta' => $settings['eael_post_list_post_meta'],
            'eael_post_list_post_title' => $settings['eael_post_list_post_title'],
            'eael_post_list_post_excerpt' => $settings['eael_post_list_post_excerpt'],
            'eael_post_list_post_excerpt_length' => $settings['eael_post_list_post_excerpt_length'],
            'eael_post_list_featured_area' => $settings['eael_post_list_featured_area'],
            'eael_post_list_featured_meta' => $settings['eael_post_list_featured_meta'],
            'eael_post_list_featured_title' => $settings['eael_post_list_featured_title'],
            'eael_post_list_featured_excerpt' => $settings['eael_post_list_featured_excerpt'],
            'eael_post_list_featured_excerpt_length' => $settings['eael_post_list_featured_excerpt_length'],
            'featured_posts' => $settings['featured_posts'],
            'eael_post_list_pagination' => $settings['eael_post_list_pagination'],
            'eael_post_list_layout_type' => $settings['eael_post_list_layout_type'],
            'eael_post_list_post_cat' => $settings['eael_post_list_post_cat'],
            'eael_post_list_author_meta' => $settings['eael_post_list_author_meta'],
            'eael_post_list_title_tag' => Helper::eael_pro_validate_html_tag($settings['eael_post_list_title_tag']),
            'eael_show_read_more_button' => $settings['eael_show_read_more_button'],
            'eael_post_list_read_more_text' => $settings['eael_post_list_read_more_text'],
            'eael_post_list_excerpt_expanison_indicator'    => $settings['eael_post_list_excerpt_expanison_indicator'],
            'eael_post_featured_image_size' => $settings['eael_post_featured_image_size'],
        ];

        $link_settings = [
            'image_link_nofollow' => $settings['image_link_nofollow'] ? 'rel="nofollow"' : '',
            'image_link_target_blank' => $settings['image_link_target_blank'] ? 'target="_blank"' : '',
            'title_link_nofollow' => $settings['title_link_nofollow'] ? 'rel="nofollow"' : '',
            'title_link_target_blank' => $settings['title_link_target_blank'] ? 'target="_blank"' : '',
            'read_more_link_nofollow' => $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '',
            'read_more_link_target_blank' => $settings['read_more_link_target_blank'] ? 'target="_blank"' : '',
        ];

        $eael_post_list_scroll_on_pagination = ! empty( $settings['eael_post_list_scroll_on_pagination'] ) && 'yes' === $settings['eael_post_list_scroll_on_pagination'] ? 1 : 0;
        $eael_post_list_scroll_offset = ! empty( $settings['eael_post_list_scroll_offset'] ) ? intval( $settings['eael_post_list_scroll_offset'] ) : 100;

        $this->add_render_attribute(
            'post-list-wrapper-attribute',
            [
                'class' => ['eael-post-list-container', $settings['eael_post_list_layout_type']],
            ]
        );

        if ($settings['eael_post_list_layout_type']) {
            $this->add_render_attribute('post-list-wrapper-attribute', 'class', "layout-{$settings['eael_post_list_layout_type']}");
        }

        echo '<div ' . $this->get_render_attribute_string('post-list-wrapper-attribute') . '>';
        if ($settings['eael_post_list_topbar'] === 'yes') {
            echo '<div class="eael-post-list-header">';
            if (!empty($settings['eael_post_list_topbar_title'])) {
                echo '<div class="header-title">
                            <h2 class="title">' . esc_html__($settings['eael_post_list_topbar_title'], 'essential-addons-elementor') . '</h2>
                        </div>';
            }

            if ($settings['eael_post_list_terms'] === 'yes') {
	            $template = $this->get_template($this->get_settings('eael_post_list_layout_type'));
	            $dir_name = method_exists( $this, 'get_temp_dir_name' ) ? $this->get_temp_dir_name( $this->get_filename_only($template) ) : "pro";
                echo '<div class="post-categories" data-nonce="'.wp_create_nonce( 'load_more' ).'" data-page-id="'.$this->page_id.'" data-widget-id="'.$this->get_id().'" data-template=' . json_encode(['dir'   => $dir_name, 'file_name' => $this->get_filename_only($template), 'name' => $this->process_directory_name()], 1) . ' data-widget="' . $this->get_id() . '" data-class="' . get_class($this) . '" data-args="' . http_build_query($args) . '" data-settings="' . http_build_query($data_settings) . '" data-page="1" data-scroll-on-pagination="' . esc_attr( $eael_post_list_scroll_on_pagination ) . '" data-scroll-offset="' . esc_attr( $eael_post_list_scroll_offset ) . '" >
                            <a href="javascript:;" data-taxonomy="all" data-id="" class="active post-list-filter-item post-list-cat-' . $this->get_id() . '">' . __($settings['eael_post_list_topbar_term_all_text'], 'essential-addons-elementor') . '</a>';

                if (!empty($args['tax_query'])) {
                    foreach ($args['tax_query'] as $taxonomy) {
                        if (!empty($taxonomy['terms'])) {
                            foreach ($taxonomy['terms'] as $term_id) {
                                $term = get_term($term_id, $taxonomy['taxonomy']);
                                if (is_object($term)) {
                                    echo '<a href="javascript:;" data-taxonomy="' . $taxonomy['taxonomy'] . '" data-id="' . $term_id . '" class="post-list-filter-item post-list-cat-' . $this->get_id() . '">' . $term->name . '</a>';
                                }
                            }
                        }
                    }
                }
                echo '</div>';
            }

            if ($settings['eael_post_list_layout_type'] == 'advanced' && $settings['eael_enable_ajax_post_search'] == 'yes') {
                $postType = $settings['post_type'] == 'by_id' ? 'any' : $settings['post_type'];

                echo '<div class="post-list-ajax-search-form">
                            <form action="" id="post-list-search-form-' . $this->get_id() . '" autocomplete="off">
                                <input type="hidden" name="post_type" value="' . ($postType) . '">
                                <input type="text" value="" placeholder="' . __('Search', 'essential-addons-elementor') . '" name="search_key" id="search_field">
                                <i class="fa fa-search"></i>
                            </form>
                            <div class="result-posts-wrapper"></div>
                        </div>';
            }
            echo '</div>';
        }

        echo '<div class="eael-post-list-wrap">';

        if ($settings['eael_post_list_featured_area'] == 'yes' && !empty($settings['featured_posts'])) {

            global $post;
            $post = get_post(intval($settings['featured_posts']));
            setup_postdata($post);

            $category = wp_get_object_terms( get_the_ID(), get_object_taxonomies( get_post_type( get_the_ID() ) ) );
            $featured_image_url = Group_Control_Image_Size::get_attachment_image_src(
                get_post_thumbnail_id(),
                'featured_image_size',
                $settings
            );

            if ($settings['eael_post_list_layout_type'] == 'preset-2') {
                echo '<div class="eael-post-list-featured-wrap">
                        <div class="eael-post-list-featured-inner">
                            <div class="featured-thumb">
                            	<img src="' . esc_url($featured_image_url) . '" alt="' . esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)) . '">
							</div>
                            
                            <div class="featured-content">';
                if ($settings['eael_post_list_post_cat'] != '' && !empty($category[0]->term_id)) {
                    echo '<div class="meta-categories">
						                    <a href="' . esc_url(get_category_link($category[0]->term_id)) . '">' . esc_html($category[0]->name) . '</a>
						                </div>';
                }
                if ($settings['eael_post_list_featured_meta'] === 'yes') {
                    echo '<div class="meta">
				                                <span>
				                                    <i class="fas fa-user"></i>
				                                    <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>
				                                </span>
				                                <span><i class="far fa-calendar-alt"></i>' . get_the_date(get_option('date_format')) . '</span>
				                          </div>';
                }

                if ($settings['eael_post_list_featured_title'] == 'yes' && !empty($settings['eael_post_list_title_tag'])) {
                    echo '<' . Helper::eael_pro_validate_html_tag($settings['eael_post_list_title_tag']) . ' class="eael-post-list-title">
		                                        <a href="' . get_the_permalink() . '"' . $link_settings['title_link_nofollow'] . '' . $link_settings['title_link_target_blank'] . '>' . esc_html( get_the_title() ) . '</a>
		                                    </' . Helper::eael_pro_validate_html_tag($settings['eael_post_list_title_tag']) . '>';
                }

                if ($settings['eael_post_list_featured_excerpt'] === 'yes') {
                    echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_post_list_featured_excerpt_length'], $settings['eael_post_list_excerpt_expanison_indicator']) . '</p>';
                }

                echo '</div></div></div>';
            } else {
                echo '<div class="eael-post-list-featured-wrap">
                        <div class="eael-post-list-featured-inner" style="background-image: url(' . wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . ')">
                            <div class="featured-content">';
                if ($settings['eael_post_list_layout_type'] == 'default' && $settings['eael_post_list_post_cat'] != '' && !empty($category[0]->term_id)) {
                    echo '<div class="meta-categories">
						                    <a href="' . esc_url(get_category_link($category[0]->term_id)) . '">' . esc_html($category[0]->name) . '</a>
						                </div>';
                }
                if ($settings['eael_post_list_featured_meta'] === 'yes') {
                    echo '<div class="meta">
				                                <span>
				                                    <i class="fas fa-user"></i>
				                                    <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>
				                                </span>
				                                <span><i class="far fa-calendar-alt"></i>' . get_the_date(get_option('date_format')) . '</span>
				                            </div>';
                }

                if ($settings['eael_post_list_featured_title'] == 'yes' && !empty($settings['eael_post_list_title_tag'])) {
                    echo '<' . Helper::eael_pro_validate_html_tag($settings['eael_post_list_title_tag']) . ' class="eael-post-list-title">
		                                        <a href="' . get_the_permalink() . '"' . $link_settings['title_link_nofollow'] . '' . $link_settings['title_link_target_blank'] . '>' . esc_html( get_the_title() ) . '</a>
		                                    </' . Helper::eael_pro_validate_html_tag($settings['eael_post_list_title_tag']) . '>';
                }

                if ($settings['eael_post_list_featured_excerpt'] === 'yes') {
                    echo '<p>' . wp_trim_words(strip_shortcodes(get_the_excerpt() ? get_the_excerpt() : get_the_content()), $settings['eael_post_list_featured_excerpt_length'], $settings['eael_post_list_excerpt_expanison_indicator']) . '</p>';
                }

                echo '</div>
                        </div>
                    </div>';
            }

            wp_reset_postdata();
        }
        // list
        $template = $this->get_template($this->get_settings('eael_post_list_layout_type'));

        if (file_exists($template)) {
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
                $iterator = 0;

                echo '<div class="eael-post-list-posts-wrap eael-post-appender eael-post-appender-' . $this->get_id() . '">';
                while ($query->have_posts()) {
                    $query->the_post();
                    include($template);
                }
                echo '</div>';
            } else {
                _e('<p class="no-posts-found">No posts found!</p>', 'essential-addons-elementor');
            }
            wp_reset_postdata();
        }else {
            _e('<p class="no-posts-found">No layout found!</p>', 'essential-addons-elementor');
        }
        echo '</div>
		</div>';

        if ($settings['eael_post_list_pagination'] === 'yes') {
            $eael_post_list_pagination_prev_icon = (isset($settings['__fa4_migrated']['eael_post_list_pagination_prev_icon_new']) || empty($settings['eael_post_list_pagination_prev_icon']) ? $settings['eael_post_list_pagination_prev_icon_new']['value'] : $settings['eael_post_list_pagination_prev_icon']);
            $eael_post_list_pagination_next_icon = (isset($settings['__fa4_migrated']['eael_post_list_pagination_next_icon_new']) || empty($settings['eael_post_list_pagination_next_icon']) ? $settings['eael_post_list_pagination_next_icon_new']['value'] : $settings['eael_post_list_pagination_next_icon']);
	        $dir_name = method_exists( $this, 'get_temp_dir_name' ) ? $this->get_temp_dir_name( $this->get_filename_only($template) ) : "pro";
            echo '<div class="post-list-pagination"  data-nonce="'.wp_create_nonce( 'load_more' ).'" data-page-id="'.$this->page_id.'" data-widget-id="'.$this->get_id().'" data-template=' . json_encode(['dir'   => $dir_name, 'file_name' => $this->get_filename_only($template), 'name' => $this->process_directory_name()], 1) . ' data-widget="' . $this->get_id() . '" data-class="' . get_class($this) . '" data-args="' . http_build_query($args) . '" data-settings="' . http_build_query($data_settings) . '" data-page="1" data-scroll-on-pagination="' . esc_attr( $eael_post_list_scroll_on_pagination ) . '" data-scroll-offset="' . esc_attr( $eael_post_list_scroll_offset ) . '" >
                <button class="btn btn-prev-post" id="post-nav-prev-' . $this->get_id() . '" disabled="true">';
                    if (isset($settings['__fa4_migrated']['eael_post_list_pagination_prev_icon_new']) || empty($settings['eael_post_list_pagination_prev_icon'])) {
                        Icons_Manager::render_icon( $settings['eael_post_list_pagination_prev_icon_new'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        echo '<span class="' . $settings['eael_post_list_pagination_prev_icon'] . '"></span>';
                    }               
                echo '</button>';
                echo '<button class="btn btn-next-post" id="post-nav-next-' . $this->get_id() . '">';
                    if (isset($settings['__fa4_migrated']['eael_post_list_pagination_next_icon_new']) || empty
                    ($settings['eael_post_list_pagination_next_icon'])) {
                        Icons_Manager::render_icon( $settings['eael_post_list_pagination_next_icon_new'], [ 'aria-hidden' => 'true' ] );
                    } else {
                        echo '<span class="' . $settings['eael_post_list_pagination_next_icon'] . '"></span>';
                    }
                echo '</button>
			</div>';
        }
    }
}
