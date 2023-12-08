<?php

namespace ElementPack\Modules\ReadingTimer\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Reading_Timer extends Module_Base {

    public function get_name() {
        return 'bdt-reading-timer';
    }

    public function get_title() {
        return BDTEP . esc_html__('Reading Timer', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-reading-timer';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['reading', 'timer', 'reading timer'];
    }

    public function get_style_depends() {
        return ['ep-font'];
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-reading-timer'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/7lRyOmR6yqo?si=KUcyEB7v3ZVrVVC8';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Reading Timer', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'ignore_element_notes',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: This widget\'s functionality may not be available in editor mode (dummy text, 2 min read), but rest assured, it works seamlessly when you switch to the perview mode/frontend perspective.', 'bdthemes-element-pack'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );


        $this->add_control(
            'reading_timer_content_id',
            [
                'label'       => esc_html__('Selector ID', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__("Just write the content selector ID here such 'my-id'. N.B: No need to add '#'.", 'bdthemes-element-pack'),
                'frontend_available' => true,
                'render_type' => 'none',
                'separator'       => 'before',

            ]
        );
        $this->add_control(
            'reading_timer_avg_words_per_minute',
            [
                'label'         => __('Average Words Per Minute', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'frontend_available' => true,
                'render_type'   => 'none',
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 1,
                        'max'   => 500,
                        'step'  => 1,
                    ],
                ],
                'default'       => [
                    'unit'      => 'px',
                    'size'      => 200,
                ],
            ]
        );

        $this->add_control(
            'reading_timer_minute_text',
            [
                'label'       => __('Minute Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('min read', 'bdthemes-element-pack'),
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );

        $this->add_control(
            'reading_timer_seconds_text',
            [
                'label'       => __('Seconds Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('sec read', 'bdthemes-element-pack'),
                'frontend_available' => true,
                'render_type' => 'none',
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => __('Show Icon', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SWITCHER,
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Reading Time', 'bdthemes-element-pac'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'reading_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-reading-timer' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'reading_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-reading-timer',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'reading_border',
                'label'     => __('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-reading-timer',
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'reading_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-reading-timer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'reading_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-reading-timer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'reading_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-reading-timer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        //space between
        $this->add_responsive_control(
            'reading_space_between',
            [
                'label'      => __('Space Between', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px'     => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-reading-timer i' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'show_icon' => 'yes',
                ],
            ]
        );
        //box-shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'reading_box_shadow',
                'label'     => __('Box Shadow', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-reading-timer',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'reading_typography',
                'label'     => __('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-reading-timer',
            ]
        );

        $this->add_responsive_control(
            'reading_timer_alignment',
            [
                'label'         => __('Alignment', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'      => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'     => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors'     => [
                    '{{WRAPPER}}.elementor-widget-bdt-reading-timer .elementor-widget-container' => 'text-align: {{VALUE}};',
                ],
                'separator'     => 'before',
            ]
        );

        $this->end_controls_section();
    }

    public function render() { ?>
        <div class="bdt-reading-timer bdt-inline">
            <?php if ($this->get_settings('show_icon')) : ?>
                <i class="ep-icon-clock-o" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
<?php
    }
}
