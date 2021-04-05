<?php
use Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Background;
use Elementor\Widget_Base;
use ElementorPro\Base\Base_Widget_Trait;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BetterDocs_Elementor_Multiple_Kb extends Widget_Base {

    use Base_Widget_Trait;
    use \Better_Docs_Elementor\Traits\Template_Query;

    public function get_name () {
        return 'betterdocs-multiple-kb';
    }

    public function get_title () {
        return __('BetterDocs Multiple KB', 'betterdocs-pro');
    }

    public function get_icon () {
        return 'betterdocs-icon-category-box';
    }

    public function get_categories () {
        return ['docs-archive'];
    }

    public function get_style_depends()
    {
        return [
            'betterdocs-category-box',
        ];
    }

    public function get_keywords () {
        return [
            'knowledgebase',
            'knowledge Base',
            'documentation',
            'doc',
            'kb',
            'betterdocs-pro', 
            'docs', 
            'category-box'
        ];
    }

    public function get_custom_help_url() {
        return 'https://betterdocs.co/docs/multiple-knowledge-bases-elementor/';
    }

    protected function _register_controls()
    {
        /**
         * Query  Controls!
         * @source includes/elementor-helper.php
         */
        do_action('betterdocs/elementor/widgets/query', $this, 'knowledge_base');

        /**
         * ----------------------------------------------------------
         * Section: Layout Options
         * ----------------------------------------------------------
         */
        $this->start_controls_section(
            'section_layout_options',
            [
                'label' => __('Layout Options', 'betterdocs-pro')
            ]
        );

        $this->add_control(
            'layout_template',
            [
                'label'       => __('Select Layout', 'betterdocs-pro'),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $this->template_options(),
                'default'     => $this->get_default(),
                'label_block' => true
            ]
        );
        
        $this->add_responsive_control(
            'box_column',
            [
                'label'              => __('Box Column', 'betterdocs-pro'),
                'type'               => Controls_Manager::SELECT,
                'default'            => '3',
                'tablet_default'     => '2',
                'mobile_default'     => '1',
                'options'            => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4'
                ],
                'prefix_class'       => 'elementor-grid%s-',
                'frontend_available' => true,
                'label_block'        => true
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label'        => __('Show Icon', 'betterdocs-pro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'betterdocs-pro'),
                'label_off'    => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'        => __('Show Title', 'betterdocs-pro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'betterdocs-pro'),
                'label_off'    => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label'     => __('Select Tag', 'betterdocs-pro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h2',
                'options'   => [
                    'h1'   => __('H1', 'betterdocs-pro'),
                    'h2'   => __('H2', 'betterdocs-pro'),
                    'h3'   => __('H3', 'betterdocs-pro'),
                    'h4'   => __('H4', 'betterdocs-pro'),
                    'h5'   => __('H5', 'betterdocs-pro'),
                    'h6'   => __('H6', 'betterdocs-pro'),
                    'span' => __('Span', 'betterdocs-pro'),
                    'p'    => __('P', 'betterdocs-pro'),
                    'div'  => __('Div', 'betterdocs-pro'),
                ],
                'condition' => [
                    'show_title' => 'true'
                ],
            ]
        );

        $this->add_control(
            'show_count',
            [
                'label'        => __('Show Count', 'betterdocs-pro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'betterdocs-pro'),
                'label_off'    => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'count_prefix',
            [
                'label'     => __('Prefix', 'betterdocs-pro'),
                'type'      => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'condition' => [
                    'show_count' => 'true',
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->add_control(
            'count_suffix',
            [
                'label'     => __('Suffix', 'betterdocs-pro'),
                'type'      => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'default'   => __('articles', 'betterdocs-pro'),
                'condition' => [
                    'show_count' => 'true',
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->add_control(
            'count_suffix_singular',
            [
                'label'     => __('Suffix Singular', 'betterdocs-pro'),
                'type'      => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'default'   => __('article', 'betterdocs-pro'),
                'condition' => [
                    'show_count' => 'true',
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->end_controls_section();

        /**
         * ----------------------------------------------------------
         * Section: Box Styles
         * ----------------------------------------------------------
         */
        $this->start_controls_section(
            'section_card_settings',
            [
                'label' => __('Box', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'column_space', // Legacy control id but new control
            [
                'label'      => __('Box Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'column_padding',
            [
                'label'      => __('Box Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->start_controls_tabs('card_settings_tabs');

        // Normal State Tab
        $this->start_controls_tab(
            'card_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'card_bg_normal',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'card_border_normal',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner'
            ]
        );

        $this->add_responsive_control(
            'card_border_radius_normal',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'card_box_shadow_normal',
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner'
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'card_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_control(
            'card_transition',
            [
                'label'      => __('Transition', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 300,
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'max'  => 2500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner' => 'transition: {{SIZE}}ms;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'card_bg_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'card_border_hover',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover'
            ]
        );

        $this->add_responsive_control(
            'card_border_radius_hover',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'card_box_shadow_hover',
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover'
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section(); # end of 'Card Settings'

        /**
         * ----------------------------------------------------------
         * Section: Icon Styles
         * ----------------------------------------------------------
         */
        $this->start_controls_section(
            'section_box_icon_style',
            [
                'label' => __('Icon', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'category_settings_area',
            [
                'label' => __( 'Area', 'betterdocs-pro' ),
                'type' =>   Controls_Manager::HEADING
            ]
        );

        $this->add_responsive_control(
            'category_settings_icon_area_size_normal',
            [
                'label'      => esc_html__('Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2'    => 'flex-basis: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'category_settings_icon',
            [
                'label' => __( 'Icon', 'betterdocs-pro' ),
                'type' =>   Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'category_settings_icon_size_normal',
            [
                'label'      => esc_html__('Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2 img'    => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->start_controls_tabs('box_icon_styles_tab');

        // Normal State Tab
        $this->start_controls_tab(
            'icon_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'icon_background_normal',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon, {{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2',
                'exclude'  => [
                    'image'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_border_normal',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon, {{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2'
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius_normal',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label'              => esc_html__('Padding', 'betterdocs-pro'),
                'type'               => Controls_Manager::DIMENSIONS,
                'size_units'         => ['px', 'em', '%'],
                'selectors'          => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label'              => esc_html__('Spacing', 'betterdocs-pro'),
                'type'               => Controls_Manager::DIMENSIONS,
                'size_units'         => ['px', 'em', '%'],
                'allowed_dimensions' => [
                    'top',
                    'bottom'
                ],
                'selectors'          => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon' => 'margin: {{TOP}}{{UNIT}} auto {{BOTTOM}}{{UNIT}} auto;'
                ],
                'condition' => [
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'icon_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'icon_background_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-icon,
                {{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-icon__layout-2'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_border_hover',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-icon,
                {{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-icon__layout-2'
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius_hover',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template' => 'default'
                ]
            ]
        );

        $this->add_control(
            'category_settings_icon_size_transition',
            [
                'label'      => __('Transition', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 300,
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'max'  => 2500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .el-betterdocs-cb-cat-icon'     => 'transition: {{SIZE}}ms;',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .el-betterdocs-cb-cat-icon img' => 'transition: {{SIZE}}ms;',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2'    => 'transition: {{SIZE}}ms;',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-icon__layout-2 img'    => 'transition: {{SIZE}}ms;'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section(); # end of 'Icon Styles'


        /**
         * ----------------------------------------------------------
         * Section: Title Styles
         * ----------------------------------------------------------
         */
        $this->start_controls_section(
            'section_box_title_styles',
            [
                'label' => __('Title', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_styles_area_heading',
            [
                'label' => __( 'Area', 'betterdocs-pro' ),
                'type' =>   Controls_Manager::HEADING,
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_responsive_control(
            'title_area_size',
            [
                'label'      => esc_html__('Area Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-title__layout-2'    => 'flex-basis: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'cat_title_typography_normal',
                'selector' => '{{WRAPPER}} .el-betterdocs-cb-inner .el-betterdocs-cb-cat-title, {{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-title__layout-2'
            ]
        );

        $this->add_responsive_control(
            'title_alignment',
            [
                'label' => __('Text Alignment', 'betterdocs-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'betterdocs-pro'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'betterdocs-pro'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'betterdocs-pro'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-title__layout-2' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ],
                'separator' => 'before'
            ]
        );

        $this->start_controls_tabs('box_title_styles_tab');

        // Normal State Tab
        $this->start_controls_tab(
            'title_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_control(
            'cat_title_color_normal',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-betterdocs-cb-inner .el-betterdocs-cb-cat-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-title__layout-2' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'      => __('Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-cb-inner .el-betterdocs-cb-cat-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-title__layout-2 span'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'title_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_control(
            'cat_title_color_hover',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-title__layout-2' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'category_title_transition',
            [
                'label'      => __('Transition', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 300,
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'max'  => 2500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-cb-inner .el-betterdocs-cb-cat-title' => 'transition: {{SIZE}}ms;',
                    '{{WRAPPER}} .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-title__layout-2'   => 'transition: {{SIZE}}ms;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section(); # end of 'Icon Styles'

        /**
         * ----------------------------------------------------------
         * Section: Count Styles
         * ----------------------------------------------------------
         */
        $this->start_controls_section(
            'section_box_count_styles',
            [
                'label' => __('Count', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'count_typography_normal',
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .el-betterdocs-cb-cat-count, {{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2'
            ]
        );

        $this->add_responsive_control(
            'count_area_size',
            [
                'label'      => esc_html__('Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-count__layout-2'    => 'flex-basis: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'count_box_bg',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->start_controls_tabs('box_count_styles_tab');

        // Normal State Tab
        $this->start_controls_tab(
            'count_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_control(
            'count_color_normal',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .el-betterdocs-cb-cat-count' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'count_box_border',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_responsive_control(
            'count_box_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'count_box_box_shadow',
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_responsive_control(
            'count_box_size',
            [
                'label'      => esc_html__('Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .count-inner__layout-2'    => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_responsive_control(
            'count_spacing',
            [
                'label'      => __('Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner .el-betterdocs-cb-cat-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'layout_template!'   => 'Layout_2'
                ]
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'count_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_control(
            'count_color_hover',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .el-betterdocs-cb-cat-count' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .count-inner__layout-2'    => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'count_box_bg_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'count_box_border_hover',
                'label'    => esc_html__('Border', 'betterdocs-pro'),
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_responsive_control(
            'count_box_border_radius_hover',
            [
                'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .count-inner__layout-2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'count_box_box_shadow_hover',
                'selector' => '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-inner:hover .count-inner__layout-2',
                'condition' => [
                    'layout_template'   => 'Layout_2'
                ]
            ]
        );

        $this->add_control(
            'category_count_transition',
            [
                'label'      => __('Transition', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 300,
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'max'  => 2500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .el-betterdocs-category-box-post .el-betterdocs-cb-cat-count' => 'transition: {{SIZE}}ms;',
                    '{{WRAPPER}} .layout__2 .el-betterdocs-cb-cat-count__layout-2 .count-inner__layout-2' => 'transition: {{SIZE}}ms;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section(); # end of 'Count Styles'
    }


	protected function render()
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute(
            'bd_category_box_wrapper',
            [
                'id'    => 'el-betterdocs-cat-box-' . esc_attr($this->get_id()),
                'class' => [
                    'el-betterdocs-category-box-wrapper',
                ],
            ]
        );

        $this->add_render_attribute(
            'bd_category_box_inner',
            [
                'class' => [
                    'el-betterdocs-category-box'
                ]
            ]
        );

        $terms_object = array(
            'taxonomy' => 'knowledge_base',
            'order'    => $settings['order'],
            'orderby'  => $settings['orderby'],
            'offset'   => $settings['offset'],
            'number'   => $settings['box_per_page']
        );

        if ($settings['include'])
        {
            $terms_object['include'] = array_diff($settings['include'], (array) $settings['exclude']);
        }

        if ($settings['exclude'])
        {
            $terms_object['exclude'] = $settings['exclude'];
        }


        $multiple_kb_status = BetterDocs_Elementor::get_betterdocs_multiple_kb_status();

        if ($settings['layout_template'] == 'Layout_2') 
        {
            $settings['layout_template'] = 'layout-2';
        }
        

        $taxonomy_objects = get_terms($terms_object);

        $html = '<div ' . $this->get_render_attribute_string('bd_category_box_wrapper') . '>';
        $html .= '<div ' . $this->get_render_attribute_string('bd_category_box_inner') . '>';

        if ($multiple_kb_status != true) {
            echo sprintf('<p class="elementor-alert elementor-alert-warning">%1$s <strong>%2$s</strong> %3$s <strong>%4$s</strong></p>.',
                __('Whoops! It seems like you have the', 'betterdocs-pro'),
                __('‘Multiple Knowledge Base’', 'betterdocs-pro'),
                __('option disabled. Make sure to enable this option from your', 'betterdocs-pro'),
                __('WordPress Dashboard -> BetterDocs -> Settings -> General.', 'betterdocs-pro'));
        } else if (file_exists($this->get_template($settings['layout_template']))) {
            if ($taxonomy_objects && !is_wp_error($taxonomy_objects))
            {
                foreach ($taxonomy_objects as $term)
                {
                    ob_start();
                    include($this->get_template($settings['layout_template']));
                    $html .= ob_get_clean();
                }
            } else {
                _e('<p class="no-posts-found">No posts found!</p>', 'betterdocs-pro');
            }

            wp_reset_postdata();
        } else {
            $html .= '<h4>' . __('File Not Found', 'betterdocs-pro') . '</h4>';
        }

        $html .= '</div>';
        $html .= '</div>';

        echo $html;
    }
}
