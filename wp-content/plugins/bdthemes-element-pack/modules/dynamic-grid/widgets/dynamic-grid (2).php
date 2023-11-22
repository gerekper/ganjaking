<?php

namespace ElementPack\Modules\DynamicGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Base\Module_Base;
use ElementPack\Element_Pack_Loader;
use ElementPack\Traits\Global_Widget_Controls;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Dynamic_Grid extends Module_Base {

    use Group_Control_Query;
    use Global_Widget_Controls;
    private $dynamic_link;
    private $dynamic_id;
    private $counter = 0;

    private $_query = null;

    public function get_name() {
        return 'bdt-dynamic-grid';
    }

    public function get_title() {
        return BDTEP . __('Dynamic Grid', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-dynamic-grid';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['grid', 'navigation', 'dynamic'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-dynamic-grid'];
        }
    }

    public function get_query() {
        return $this->_query;
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/3H6eSrLkse4';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_grid_layout',
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
                'label'           => __('Columns', 'bdthemes-element-pack'),
                'type'            => Controls_Manager::SELECT,
                'desktop_default' => 3,
                'tablet_default'  => 2,
                'mobile_default'  => 1,
                'options'         => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-dynamic-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label'     => esc_html__('Column Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-dynamic-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-dynamic-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label'   => __('Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-dynamic-grid-item' => 'text-align: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->register_query_builder_controls();

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_layout',
            [
                'label'     => __('Items', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
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
                'name'      => 'item_background',
                'selector'  => '{{WRAPPER}} .bdt-ep-dynamic-grid-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'item_border',
                'selector'  => '{{WRAPPER}} .bdt-ep-dynamic-grid-item',
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
                    '{{WRAPPER}} .bdt-ep-dynamic-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-ep-dynamic-grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-grid-item',
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
                'name'      => 'items_hover_background',
                'selector'  => '{{WRAPPER}} .bdt-ep-dynamic-grid-item:hover',
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
                    '{{WRAPPER}} .bdt-ep-dynamic-grid-item:hover' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-dynamic-grid-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Get post query builder arguments
     */

    public function query_posts($posts_per_page) {

        $default = $this->getGroupControlQueryArgs();
        $args = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
            $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
        }
        $args         = array_merge($default, $args);
        $this->_query = new \WP_Query($args);
    }

    public function render_posts() {
        $settings = $this->get_settings_for_display();
        echo '<div class="bdt-ep-dynamic-grid-item">';
        echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id']);
        echo '</div>';
        $this->counter++;
    }

    public function render_posts_loop() {
        $settings = $this->get_settings_for_display();
        $this->query_posts($settings['posts_per_page']);
        $query = $this->get_query();
        $this->counter = 0;
        if (empty($query->found_posts)) {
            echo esc_html_x('posts not found', 'Frontend', 'bdthemes-element-pack');
        } else {
            if ($query->in_the_loop) {
                $this->dynamic_link = get_permalink();
                $this->dynamic_id = get_the_ID();
                $this->render_posts();
            } else {
                while ($query->have_posts()) {
                    $query->the_post();
                    $this->dynamic_link = get_permalink();
                    $this->dynamic_id = get_the_ID();
                    $this->render_posts();
                }
            }
            wp_reset_postdata();
        }
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if ($settings['template_id'] !== '') { ?>
            <div class="bdt-dynamic-grid">
                <?php $this->render_posts_loop(); ?>
            </div>
        <?php
        } else {
            echo '<div class="bdt-alert-warning" bdt-alert>oops!! There is no template selected, please select a template first.<div>';
        }
        ?>

<?php
    }
}
