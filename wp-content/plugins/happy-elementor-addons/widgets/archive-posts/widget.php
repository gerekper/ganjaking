<?php

/**
 * Archive Posts widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

defined('ABSPATH') || die();

class Archive_Posts extends Base {

    public $query;
    public $display_ids = [];

    public $settings = [];
    private $current_permalink;

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Archive Posts', 'happy-elementor-addons');
    }

    public function get_custom_help_url() {
        return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/archive-posts/';
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-tb-archieve-content';
    }

    public function get_keywords() {
        return ['archive posts', 'posts', 'post', 'recent post'];
    }

    public function add_to_avoid_list($ids) {
        $this->display_ids = array_unique(array_merge($this->display_ids, $ids));
    }

    /**
     * Register widget content controls
     */
    protected function register_content_controls() {
        $this->__archive_layout_controls();
        $this->__archive_pagination_controls();
        $this->__archive_advanced_controls();
    }

    protected function __archive_layout_controls() {
        $this->start_controls_section(
            '_section_archive_layout',
            [
                'label' => __('Layout', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'skin',
            [
                'label' => esc_html__('Skin', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'classic',
                'options' => [
                    'classic' => 'Classic',
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container' => '--grid_columns: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail',
            [
                'label' => esc_html__('Image Position', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top' => esc_html__('Top', 'happy-elementor-addons'),
                    'left' => esc_html__('Left', 'happy-elementor-addons'),
                    'right' => esc_html__('Right', 'happy-elementor-addons'),
                    'none' => esc_html__('None', 'happy-elementor-addons'),
                ],
                'prefix_class' => 'ha-archive-posts--thumbnail-',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail_size',
                'default' => 'medium',
                'exclude' => ['custom'],
                'condition' => [
                    'thumbnail!' => 'none',
                ],
                'prefix_class' => 'ha-archive-posts--thumbnail-size-',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Title', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Excerpt', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => esc_html__('Excerpt Length', 'happy-elementor-addons'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'apply_to_custom_excerpt',
            [
                'label' => esc_html__('Apply to custom Excerpt', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'happy-elementor-addons'),
                'label_off' => esc_html__('No', 'happy-elementor-addons'),
                'default' => 'no',
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_data',
            [
                'label' => esc_html__('Meta Data', 'happy-elementor-addons'),
                'label_block' => true,
                'type' => Controls_Manager::SELECT2,
                'default' => ['date', 'comments'],
                'multiple' => true,
                'options' => [
                    'author' => esc_html__('Author', 'happy-elementor-addons'),
                    'date' => esc_html__('Date', 'happy-elementor-addons'),
                    'comments' => esc_html__('Comments', 'happy-elementor-addons'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'meta_separator',
            [
                'label' => esc_html__('Separator Between', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXT,
                'default' => '///',
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-meta-wrap span + span:before' => 'content: "{{VALUE}}"',
                ],
                'condition' => [
                    'meta_data!' => [],
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => esc_html__('Read More', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => esc_html__('Read More Text', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Read More »', 'happy-elementor-addons'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'open_new_tab',
            [
                'label' => esc_html__('Open in new window', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'happy-elementor-addons'),
                'label_off' => esc_html__('No', 'happy-elementor-addons'),
                'default' => 'no',
                'render_type' => 'none',
            ]
        );

        $this->end_controls_section();
    }

    public function __archive_pagination_controls() {
        $this->start_controls_section(
            '_section_archive_pagination',
            [
                'label' => esc_html__('Pagination', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => esc_html__('Pagination', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('None', 'happy-elementor-addons'),
                    'numbers' => esc_html__('Numbers', 'happy-elementor-addons'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pagination_page_limit',
            [
                'label' => esc_html__('Page Limit', 'happy-elementor-addons'),
                'default' => '',
                'condition' => [
                    'pagination_type!' => [
                        'load_more_on_click',
                        'load_more_infinite_scroll',
                        '',
                    ],
                ],
            ]
        );

        $this->add_control(
            'pagination_prev_label',
            [
                'label' => esc_html__('Previous Label', 'happy-elementor-addons'),
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('&laquo; Previous', 'happy-elementor-addons'),
                'condition' => [
                    'pagination_type' => [
                        'numbers',
                        // 'prev_next',
                        // 'numbers_and_prev_next',
                    ],
                ],
            ]
        );

        $this->add_control(
            'pagination_next_label',
            [
                'label' => esc_html__('Next Label', 'happy-elementor-addons'),
                'default' => esc_html__('Next &raquo;', 'happy-elementor-addons'),
                'condition' => [
                    'pagination_type' => [
                        'numbers',
                        // 'prev_next',
                        // 'numbers_and_prev_next',
                    ],
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'pagination_align',
            [
                'label' => esc_html__('Alignment', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-pagination' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'pagination_type!' => [
                        'load_more_on_click',
                        'load_more_infinite_scroll',
                        '',
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function __archive_advanced_controls() {
        $this->start_controls_section(
            '_section_archive_advanced',
            [
                'label' => esc_html__('Advanced', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'query_id',
            [
                'label' => esc_html__('Query ID', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'nothing_found_message',
            [
                'label' => esc_html__('Nothing Found Message', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('It seems we can\'t find what you\'re looking for.', 'happy-elementor-addons'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register styles related controls
     */
    protected function register_style_controls() {
        $this->__archive_layout_style_controls();
        $this->__archive_image_style_controls();
        $this->__archive_content_style_controls();
        $this->__archive_pagination_style_controls();
    }


    protected function __archive_layout_style_controls() {

        $this->start_controls_section(
            '_section_style_layout',
            [
                'label' => __('Layout', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'content_align',
            [
                'label' => esc_html__('Alignment', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts__text' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'content_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-post' => 'background: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .ha-archive-posts-container .ha-archive-post',
			]
		);

        $this->end_controls_section();
    }

    protected function __archive_image_style_controls() {

        $this->start_controls_section(
            '_section_style_image',
            [
                'label' => __('Image', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts__thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'image_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-post' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Width', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 10,
                        'max' => 600,
                    ],
                ],
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts__thumbnail__link' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'thumbnail!' => 'none',
                ],
            ]
        );
        
        $this->add_control(
            'image_align',
            [
                'label' => esc_html__('Alignment', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-post' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'thumbnail' => 'top'
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function __archive_content_style_controls() {

        $this->start_controls_section(
            '_section_style_content',
            [
                'label' => __('Content', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'content_title',
			[
				'label' => esc_html__( 'Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-title' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-title',
			]
		);

        $this->add_control(
            'title_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'content_meta',
			[
				'label' => esc_html__( 'Meta', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $this->add_control(
			'meta_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-meta-wrap' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'selector' => '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-meta-wrap',
			]
		);

        $this->add_control(
            'meta_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-meta-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'content_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $this->add_control(
			'excerpt_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-excerpt' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-excerpt',
			]
		);

        $this->add_control(
            'excerpt_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'content_readmore',
			[
				'label' => esc_html__( 'Read More', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $this->add_control(
			'readmore_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-readmore' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-readmore',
			]
		);

        $this->add_control(
            'readmore_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-container .ha-archive-posts-readmore' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function __archive_pagination_style_controls() {

        $this->start_controls_section(
            '_section_style_pagination',
            [
                'label' => __('Pagination', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'pagination_color_tabs'
        );
        
        $this->start_controls_tab(
            'pagination_normal_color_tab',
            [
                'label' => esc_html__( 'Normal', 'happy-elementor-addons' ),
            ]
        );

        $this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-pagination .page-numbers' => 'color: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_hover_color_tab',
            [
                'label' => esc_html__( 'Hover', 'happy-elementor-addons' ),
            ]
        );

        $this->add_control(
			'pagination_color_hover',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-pagination .page-numbers:hover' => 'color: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_active_color_tab',
            [
                'label' => esc_html__( 'Active', 'happy-elementor-addons' ),
            ]
        );

        $this->add_control(
			'pagination_color_active',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-archive-posts-pagination .page-numbers.current' => 'color: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_control(
            'pagination_space_between',
            [
                'label' => esc_html__('Space Between (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-pagination' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_spacing',
            [
                'label' => esc_html__('Spacing (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-archive-posts-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $this->settings = $this->get_settings_for_display();
        global $wp_query;

        $query_vars = $wp_query->query_vars;

        if ($this->settings['query_id']) {
            $query_vars = apply_filters("happyaddons/archive_posts/{$this->settings['query_id']}", $query_vars);
        }

        if ($query_vars !== $wp_query->query_vars) {
            $this->query = new \WP_Query($query_vars); // SQL_CALC_FOUND_ROWS is used.
        } else {
            $this->query = $wp_query;
        }

        if (ha_elementor()->editor->is_edit_mode() || is_preview()) {
            // need to remove this after task done
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type'              => array('post'),
                'post_status'            => array('publish'),
                'paged'                  => $paged,
                'offset'                 => 1,
                'posts_per_page'         => 10,
                'order'                  => 'DESC',
                'orderby'                => 'date',

            );
            $this->query = new \WP_Query($args);
        }

        $this->add_to_avoid_list(wp_list_pluck($this->query->posts, 'ID'));

        // if (!$this->query->found_posts) {
        //     return;
        // }
?>
        <div class="ha-archive-posts-wrapper ha-ap-skin-<?php echo esc_attr($this->settings['skin']); ?>">
            <?php
            // It's the global `wp_query` it self. and the loop was started from the theme.
            if ($this->query->in_the_loop) {
                $this->current_permalink = get_permalink();
                $this->render_post();
            } else {
                if ($this->query->have_posts()) {
                    echo '<div class="ha-archive-posts-container">';

                    while ($this->query->have_posts()) {
                        $this->query->the_post();

                        $this->current_permalink = get_permalink();
                        $this->render_post();
                    }
                    echo '</div>';

                    $this->get_pagination($this->query);
                } else {
                    echo $this->settings['nothing_found_message'];
                }
            }

            wp_reset_postdata();
            ?>
        </div>
    <?php
    }

    protected function render_post() {
        $show_title = $this->settings['show_title'];
        $title_tag = $this->settings['title_tag'];
        $active_meta = $this->settings['meta_data'];
        $excerpt_length = $this->settings['excerpt_length'];
        $readmore = $this->settings['show_read_more'];
        $readmore_text = $this->settings['read_more_text'];

        $this->add_render_attribute('posts', 'class', [
            'ha-archive-post',
            'post-' . get_the_ID(),
            'type-' . get_post_type(),
            'status-publish'
        ], true);

    ?>
        <article <?php $this->print_render_attribute_string('posts'); ?>>
            <?php $this->render_thumbnail(); ?>
            <div class="ha-archive-posts__text">
                <?php $this->render_title($show_title, $title_tag); ?>
                <?php $this->render_meta($active_meta); ?>
                <?php $this->render_excerpt($excerpt_length); ?>
                <?php $this->render_read_more($readmore, $readmore_text); ?>
            </div>
        </article>
    <?php
    }

    protected function get_optional_link_attributes_html() {
        $optional_attributes_html = 'yes' === $this->settings['open_new_tab'] ? 'target="_blank"' : '';

        return $optional_attributes_html;
    }

    protected function render_thumbnail() {
        $thumbnail = $this->settings['thumbnail'];

        if ('none' === $thumbnail && !ha_elementor()->editor->is_edit_mode()) {
            return;
        }

        $this->settings['thumbnail_size'] = [
            'id' => get_post_thumbnail_id(),
        ];

        $thumbnail_html = Group_Control_Image_Size::get_attachment_image_html($this->settings, 'thumbnail_size');

        if (empty($thumbnail_html)) {
            return;
        }

        $optional_attributes_html = $this->get_optional_link_attributes_html();

    ?>
        <a class="ha-archive-posts__thumbnail__link" href="<?php echo esc_attr($this->current_permalink); ?>" <?php echo esc_attr($optional_attributes_html); ?>>
            <div class="ha-archive-posts__thumbnail"><?php echo wp_kses_post($thumbnail_html); ?></div>
        </a>
    <?php
    }

    public function get_pagination($query) {

        if ('numbers' !== $this->settings['pagination_type']) {
            return;
        }

        $paged = intval(isset($query->query['paged']) ? $query->query['paged'] : max(1, get_query_var('paged')));

        $big  = 99999999; // need an unlikely integer
        $html = paginate_links(
            array(
                'base'     => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'   => '/page/%#%',
                'current'  => max(1, $paged),
                'total'    => intval(!empty($this->settings['pagination_page_limit']) ? $this->settings['pagination_page_limit'] : $query->max_num_pages),
                'end_size' => 2,
                'show_all' => 'yes',
                'type'     => 'list',
                'prev_text' => $this->settings['pagination_prev_label'],
                'next_text' => $this->settings['pagination_next_label'],
            )
        );

        echo sprintf(
            '<div class="ha-archive-posts-pagination">%s</div>',
            wp_kses($html, ha_get_allowed_html_tags('intermediate'))
        );
    }

    protected function render_title($show_title, $title_tag) {

        if ('yes' === $show_title && get_the_title()) {
            printf(
                '<%1$s %2$s><a href="%3$s">%4$s</a></%1$s>',
                tag_escape($title_tag),
                'class="ha-archive-posts-title"',
                esc_url(get_the_permalink(get_the_ID())),
                esc_html(get_the_title())
            );
        }
    }

    protected function render_meta($active_meta) {
        if (empty($active_meta)) {
            return;
        }
    ?>
        <div class="ha-archive-posts-meta-wrap">
            <?php foreach ($active_meta as $meta) : ?>
                <span class="ha-archive-posts-<?php echo esc_attr($meta); ?>">
                    <?php
                    if ('author' == $meta) {
                        $this->render_author();
                    }
                    if ('date' == $meta) {
                        $this->render_date();
                    }
                    if ('comments' == $meta) {
                        $this->render_comments();
                    }
                    ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php
    }

    protected function render_author($has_icon = false) {
        $link = get_author_posts_url(get_the_author_meta('ID'));
    ?>
        <a class="ha-archive-posts-author-text" href="<?php echo esc_url($link); ?>">
            <?php the_author(); ?>
        </a>
    <?php
    }

    protected function render_avater() {
    ?>
        <div class="ha-archive-posts-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="300px" height="105px" viewBox="0 0 300 105" xml:space="preserve">
                <path d="M0,104.9h300V79.8h-17.9c-26.1,0-49.8-14.6-62.1-37.6c-13.4-25-39.9-42.1-70.3-42.1s-56.8,17-70.3,42.1  c-12.3,23-36,37.6-62.1,37.6H0V104.9z"></path>
            </svg>
            <?php echo get_avatar(get_the_author_meta('ID'), '60'); ?>
        </div>
    <?php
    }

    protected function render_date($has_icon = false) {
        $link = ha_get_date_link();
    ?>
        <a class="ha-archive-posts-date-text" href="<?php echo esc_url($link); ?>">
            <?php echo esc_html(get_the_date(get_option('date_format'))); ?>
        </a>
    <?php
    }

    protected function render_comments($has_icon = false) {
    ?>
        <span class="ha-archive-posts-comment-text">
            <?php comments_number(); ?>
        </span>
    <?php
    }

    protected function render_excerpt($excerpt_length = false) {
        if (empty($excerpt_length)) {
            return;
        }
    ?>
        <div class="ha-archive-posts-excerpt">
            <?php printf('<p>%1$s</p>', ha_get_excerpt(get_the_ID(), $excerpt_length)); ?>
        </div>
<?php
    }

    protected function render_read_more($read_more = false, $read_more_text = '') {
        if ($read_more) {
            printf(
                '<div class="%1$s"><a href="%2$s" %3$s>%4$s</a></div>',
                'ha-archive-posts-readmore',
                esc_url(get_the_permalink(get_the_ID())),
                $this->get_optional_link_attributes_html(),
                esc_html($read_more_text)
            );
        }
    }
}
