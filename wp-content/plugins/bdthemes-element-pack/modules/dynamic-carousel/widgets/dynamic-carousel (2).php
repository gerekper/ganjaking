<?php

namespace ElementPack\Modules\DynamicCarousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Base\Module_Base;
use ElementPack\Element_Pack_Loader;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Traits\Global_Swiper_Controls;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;


if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Dynamic_Carousel extends Module_Base {

    use Group_Control_Query;
    use Global_Widget_Controls;
    use Global_Swiper_Controls;

    private $dynamic_link;
    private $dynamic_id;
    private $counter = 0;

    private $_query = null;

    public function get_name() {
        return 'bdt-dynamic-carousel';
    }

    public function get_title() {
        return BDTEP . __('Dynamic Carousel', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-dynamic-carousel';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['carousel', 'navigation', 'dynamic', 'custom'];
    }

    public function get_style_depends() {
        if ( $this->ep_is_edit_mode() ) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-dynamic-carousel'];
        }
    }

    public function get_script_depends() {
        if ( $this->ep_is_edit_mode() ) {
            return ['ep-scripts'];
        } else {
            return ['ep-dynamic-carousel'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/0j1KGXujc78';
    }

    public function get_query() {
        return $this->_query;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_carousel_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'template_id',
            [
                'label'       => __('Select Template', 'bdthemes-element-pack'),
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
                'query_args'  => [
                    'query' => 'elementor_dynamic_loop_template',
                ],
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'label'          => __('Columns', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options'        => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label'   => __('Item Gap', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range'   => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
            ]
        );

        $this->add_control(
            'match_height',
            [
                'label' => __('Item Match Height', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'    => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-dynamic-carousel-item' => 'text-align: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
            ]
        );

        //Global Navigation Controls
        $this->register_navigation_controls();

        $this->end_controls_section();

        //Global Carousel Settings Controls
        $this->register_carousel_settings_controls();
        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => __('Items', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'item_background',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'item_border',
                'selector'  => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-dynamic-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-dynamic-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item',
            ]
        );

        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label'       => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                // 'default'     => [
                //     'size' => 10
                // ],
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'selectors'   => [
                    '{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'items_hover_background',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-dynamic-carousel-item:hover' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'item_active_background',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item.swiper-slide-active',
            ]
        );

        $this->add_control(
            'item_active_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-dynamic-carousel-item.swiper-slide-active' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_active_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-carousel-item.swiper-slide-active',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'      => __('Navigation', 'bdthemes-element-pack'),
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
        $this->register_navigation_style_controls('swiper-carousel');

        $this->end_controls_section();
    }

    /**
     * Get post query builder arguments
     */

    public function query_posts($posts_per_page) {

        $default = $this->getGroupControlQueryArgs();
        $args    = [];
        if ( $posts_per_page ) {
            $args['posts_per_page'] = $posts_per_page;
            $args['paged']          = max(1, get_query_var('paged'), get_query_var('page'));
        }
        $args         = array_merge($default, $args);
        $this->_query = new \WP_Query($args);
    }

    public function render_header() {
        $settings = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('carousel');
        if ( 'yes' == $settings['match_height'] ) {
            $this->add_render_attribute('carousel', 'bdt-height-match', 'target: .bdt-ep-dynamic-carousel-item');
        }
        $this->add_render_attribute('carousel', 'class', ['bdt-dynamic-carousel']);
        ?>
    <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
        <?php
    }

    protected function render_posts() {
        $settings = $this->get_settings_for_display();
        echo '<div class="bdt-ep-dynamic-carousel-item swiper-slide">';
        echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id']);
        echo '</div>';
        $this->counter++;
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if ( $settings['template_id'] !== '' ) {
            $this->query_posts($settings['posts_per_page']);
            $query         = $this->get_query();
            $this->counter = 0;
            if ( empty($query->found_posts) ) {
                echo 'posts not found';
            } else {
                $this->render_header();
                if ( $query->in_the_loop ) {
                    $this->dynamic_link = get_permalink();
                    $this->dynamic_id   = get_the_ID();
                    $this->render_posts();
                } else {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $this->dynamic_link = get_permalink();
                        $this->dynamic_id   = get_the_ID();
                        $this->render_posts();
                    }
                }
                wp_reset_postdata();
                $this->render_footer();
            }
        } else {
            echo '<div class="bdt-alert-warning" bdt-alert>Oops!! There is no template selected, please select a template first.<div>';
        }
    }
}
