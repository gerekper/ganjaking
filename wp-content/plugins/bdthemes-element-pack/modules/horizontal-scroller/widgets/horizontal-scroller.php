<?php

namespace ElementPack\Modules\HorizontalScroller\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Horizontal_Scroller extends Module_Base {

    public function get_name() {
        return 'bdt-horizontal-scroller';
    }

    public function get_title() {
        return BDTEP . esc_html__('Horizontal Scroller', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-horizontal-scroller';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['scroller', 'scroll', 'toggle', 'horizontal'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-horizontal-scroller'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['gsap', 'scroll-trigger-js', 'scroll-to-plugin-js', 'ep-scripts'];
        } else {
            return ['gsap', 'scroll-trigger-js', 'scroll-to-plugin-js', 'ep-horizontal-scroller'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/x6vpXQt6__k';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Horizontal Scroller', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'horizontal_scroller_section_id',
            [
                'label'       => esc_html__('Select ID', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__('select section id', 'bdthemes-element-pack'),
            ]
        );
        $repeater->add_control(
            'horizontal_scroller_section_navigation_title',
            [
                'label'       => esc_html__('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Item 1', 'bdthemes-element-pack')
            ]
        );
        $this->add_control(
            'horizontal_scroller_section_list',
            [
                'label'       => __('Section Lists', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ horizontal_scroller_section_id }}}',
                'frontend_available' => true,
                'render_type'        => 'none',
                'prevent_empty' => false,
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_addtional',
            [
                'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'horizontal_scroller_auto_fill',
            [
                'label'         => esc_html__('Auto Fill', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'horizontal_scroller_show_dots',
            [
                'label'         => esc_html__('Dots', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Show', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('Hide', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => '',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'show_dots_label',
            [
                'label'         => esc_html__('Label', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Show', 'bdthemes-element-pack'),
                'label_off'     => esc_html__('Hide', 'bdthemes-element-pack'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition' => [
                    'horizontal_scroller_show_dots' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'show_dots_label_only_active',
            [
                'label'         => esc_html__('Label Only Active', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition' => [
                    'horizontal_scroller_show_dots' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-pagination-wrapper li:not(.is-active) .bdt-dot-text' => 'display:none'
                ]
            ]
        );
        $this->add_control(
            'dots_position',
            [
                'label'      => esc_html__('Position', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SELECT,
                'default'    => 'bottom-center',
                'options'    => [
                    'bottom-center'  => esc_html__('Bottom Center', 'bdthemes-element-pack'),
                    'bottom-left' => esc_html__('Bottom Left', 'bdthemes-element-pack'),
                    'bottom-right' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
                    'left-center' => esc_html__('Center Left', 'bdthemes-element-pack'),
                    'right-center'   => esc_html__('Center Right', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'horizontal_scroller_show_dots' => 'yes'
                ]
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_dots',
            [
                'label' => esc_html__('Dots', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'horizontal_scroller_show_dots' => 'yes'
                ]
            ]
        );
        $this->start_controls_tabs(
            'tabs_style_dots'
        );
        $this->start_controls_tab(
            'dots_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'dot_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li span.bdt-dot' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'dot_spacing',
            [
                'label'         => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller' => '--dot-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'dot_width',
            [
                'label'         => esc_html__('Width', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li span.bdt-dot' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'dot_height',
            [
                'label'         => esc_html__('Height', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li span.bdt-dot' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'dot_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li span.bdt-dot',
            ]
        );
        $this->add_responsive_control(
            'dot_radius',
            [
                'label'                 => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li span'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'dots_shadow',
                'label'     => esc_html__('Shadow', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li .bdt-dot',
            ]
        );
        $this->add_control(
            'dots_label_header',
            [
                'label'     => esc_html__('Label', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'label_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li .bdt-dot-text' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'dots_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li .bdt-dot-text',
            ]
        );
        $this->add_control(
            'dots_offset_heading',
            [
                'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'dot_offset_x',
            [
                'label'         => esc_html__('Horizontal', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller' => '--dot-offset-x: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'dots_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'active_dot_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active span.bdt-dot' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'dot_active_width',
            [
                'label'         => esc_html__('Width', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'dot_active_height',
            [
                'label'         => esc_html__('Height', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'dot_active_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot',
            ]
        );
        $this->add_responsive_control(
            'dot_active_radius',
            [
                'label'                 => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'dot_active_shadow',
                'label'     => esc_html__('Shadow', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot',
            ]
        );
        $this->add_control(
            'dots_label_active_header',
            [
                'label'     => esc_html__('Label', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'label_active_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-horizontal-scroller .ep-dot-nav li.is-active .bdt-dot-text' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    public function render_navigation() {
        $settings = $this->get_settings_for_display(); ?>
        <nav id="nav-id-<?php echo esc_attr($this->get_id()); ?>" class="ep-pagination-wrapper">
            <ul class="ep-dot-nav">
                <?php
                foreach ($settings['horizontal_scroller_section_list'] as $key => $section) {
                    if (0 === $key) { ?>
                        <li class="is-active">
                            <span class="bdt-dot"></span>
                            <?php
                            if (($settings['show_dots_label'] == 'yes')) :
                                printf('<span class="bdt-dot-text"> %1$s</span>', $section['horizontal_scroller_section_navigation_title']);
                            endif;
                            ?>
                        </li>
                    <?php

                    } else { ?>
                        <li class="dots-list">
                            <span class="bdt-dot"></span>
                            <?php
                            if (($settings['show_dots_label'] == 'yes')) :
                                printf('<span class="bdt-dot-text"> %1$s</span>', $section['horizontal_scroller_section_navigation_title']);
                            endif;
                            ?>
                        </li>
                <?php
                    }
                }; ?>
            </ul>
        </nav>
    <?php
    }
    public function render() {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', [
            'bdt-ep-hc-wrapper',
            'bdt-ep-dot-position-' . $settings['dots_position'],
        ]);
    ?>
        <div class="bdt-ep-horizontal-scroller">
            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <?php
                if ($settings['horizontal_scroller_show_dots'] === 'yes') :
                    echo $this->render_navigation();
                endif;
                ?>
            </div>
        </div>
<?php
    }
}
