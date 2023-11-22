<?php

namespace ElementPack\Modules\AdvancedProgressBar\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Advanced_Progress_Bar extends Module_Base {

    public function get_name() {
        return 'bdt-advanced-progress-bar';
    }

    public function get_title() {
        return BDTEP . esc_html__('Advanced Progress Bar', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-advanced-progress-bar';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['advanced bar', 'progress', 'skills', 'bars'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-advanced-progress-bar'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-advanced-progress-bar'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/7hnmMdd2-Yo';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_progress_bars',
            [
                'label' => esc_html__('Progress Bars', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'name',
            [
                'label'       => esc_html__('Name', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Design', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Type a skill name', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'max_level',
            [
                'label'       => esc_html__('Max Value', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'default'     => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'size_units'  => ['%'],
                'range'       => [
                    '%' => [
                        'min'  => 0,
                        'step' => 10,
                        'max'  => 100,
                    ],
                ],
                // 'selectors'   => [
                //     '{{WRAPPER}} .bdt-ep-advanced-progress-bar-item{{CURRENT_ITEM}} ' => 'width: {{SIZE}}{{UNIT}};',
                // ],
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'level',
            [
                'label'      => esc_html__('Level (Out Of Max Value)', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'unit' => '%',
                    'size' => 95,
                ],
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-advanced-progress-bar-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'base_color',
            [
                'label'          => esc_html__('Base Color', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::COLOR,
                'selectors'      => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-advanced-progress-bar-level' => 'background-color: {{VALUE}};',
                ],
                'style_transfer' => true,
            ]
        );

        $repeater->add_control(
            'fill_color',
            [
                'label'          => esc_html__('Fill Color', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::COLOR,
                'selectors'      => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-advanced-progress-bar-fill' => 'background-color: {{VALUE}};',
                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'progress_bars',
            [
                'show_label'  => false,
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '<# print((name || level.size) ? (name || "Skill") + " - " + level.size + level.unit : "Skill - 0%") #>',
                'default'     => [
                    [
                        'name'  => esc_html__('Design', 'bdthemes-element-pack'),
                        'level' => ['size' => 97, 'unit' => '%'],
                    ],
                    [
                        'name'  => esc_html__('UX', 'bdthemes-element-pack'),
                        'level' => ['size' => 88, 'unit' => '%'],
                    ],
                    [
                        'name'  => esc_html__('Coding', 'bdthemes-element-pack'),
                        'level' => ['size' => 92, 'unit' => '%'],
                    ],
                    [
                        'name'  => esc_html__('Speed', 'bdthemes-element-pack'),
                        'level' => ['size' => 95, 'unit' => '%'],
                    ],
                    [
                        'name'  => esc_html__('Passion', 'bdthemes-element-pack'),
                        'level' => ['size' => 100, 'unit' => '%'],
                    ],
                ],
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label'     => esc_html__('Animation Speed (s)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => .1,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-fill' => '-webkit-transition: width {{SIZE}}s ease;  -o-transition: width {{SIZE}}s ease; transition: width {{SIZE}}s ease;',
                ],
            ]
        );

        $this->add_control(
            'skills_style',
            [
                'type'           => Controls_Manager::SELECT,
                'label'          => esc_html__('Progress Style', 'bdthemes-element-pack'),
                'separator'      => 'before',
                'default'        => 'default',
                'options'        => [
                    'default'                          => esc_html__('Default', 'bdthemes-element-pack'),
                    'bdt-progress-with-perc'           => esc_html__('Percentage With Progress', 'bdthemes-element-pack'),
                    'bdt-progress-inner-perc'          => esc_html__('Inner Content', 'bdthemes-element-pack'),
                    'bdt-progress-inner-perc-and-name' => esc_html__('Inner Content Between', 'bdthemes-element-pack'),

                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'text_position',
            [
                'type'           => Controls_Manager::SELECT,
                'label'          => esc_html__('Text Position', 'bdthemes-element-pack'),
                'separator'      => 'before',
                'default'        => 'outside-top',
                'options'        => [
                    'outside-top'    => esc_html__('Text Outside Top', 'bdthemes-element-pack'),
                    'outside-bottom' => esc_html__('Text Outside Bottom', 'bdthemes-element-pack'),
                ],
                'style_transfer' => true,
                'condition'      => [
                    'skills_style' => ['default', 'bdt-progress-with-perc'],
                ],
            ]
        );

        $this->add_control(
            'skills_extra_style',
            [
                'type'           => Controls_Manager::SELECT,
                'label'          => esc_html__('Additional Style', 'bdthemes-element-pack'),
                'separator'      => 'before',
                'default'        => 'null',
                'options'        => [
                    'null'                                            => esc_html__('Default', 'bdthemes-element-pack'),
                    'bdt-progress-fill-striped'                       => esc_html__('Striped', 'bdthemes-element-pack'),
                    'bdt-progress-fill-striped bdt-progress-animated' => esc_html__('Striped With Animation', 'bdthemes-element-pack'),
                    'bdt-progress-rainbow-animate'                    => esc_html__('Rainbow Animation', 'bdthemes-element-pack'),

                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'rainbow_animation_speed',
            [
                'label'     => esc_html__('Rainbow Animation Speed (s)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar.bdt-progress-rainbow-animate .bdt-ep-advanced-progress-bar-fill' => ' -webkit-animation: animateRainbow {{SIZE}}s ease infinite;
                    animation: animateRainbow {{SIZE}}s ease infinite;',
                ],
                'condition' => [
                    'skills_extra_style' => ['bdt-progress-rainbow-animate'],
                ],
            ]
        );


        $this->add_control(
            'show_perc',
            [
                'label'   => esc_html__('Show Percentage', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_max_value',
            [
                'label'   => esc_html__('Show Max Value', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_progress_bars',
            [
                'label' => esc_html__('Progress Bars', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'height',
            [
                'label'      => esc_html__('Height', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => 1,
                        'max' => 250,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-level' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'spacing',
            [
                'label'      => esc_html__('Spacing Between', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_control(
            'border_radius_level',
            [
                'label'      => esc_html__('Border Radius Level', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-level' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label'      => esc_html__('Border Radius Fill', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'exclude'  => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-level',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'progress_base_color',
            [
                'label'     => esc_html__('Base Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-level' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'show_progress_fill',
            [
                'label'   => esc_html__('Fill Color', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'progress_fill_color',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-fill',
                'condition' => [
                    'skills_extra_style!' => ['bdt-progress-rainbow-animate'],
                    'show_progress_fill'  => 'yes'
                ],
            ]
        );

        $this->add_control(
            'rainbow_first_color',
            [
                'label'       => esc_html__('Rainbow Color', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => 'Input your colors. example: red, #9400d3, indigo',
                'default'     => 'red, orange, yellow, blue, indigo, violet',
                'selectors'   => [
                    '{{WRAPPER}} .bdt-progress-rainbow-animate .bdt-ep-advanced-progress-bar-fill' => 'background: linear-gradient(270deg, {{VALUE}} ); background-size: 300% 300%;',
                ],
                'condition'   => [
                    'skills_extra_style' => ['bdt-progress-rainbow-animate'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'info_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'info_text_shadow',
                'selector' => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-content',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_percentage _style',
            [
                'label'     => esc_html__('Percentage', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'skills_style' => ['bdt-progress-with-perc'],
                ],
            ]
        );

        $this->add_control(
            'percentage_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'percentage_bg_color',
                'label'    => esc_html__('Background', 'bdthemes-element-pack'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage::before',

            ]
        );

        $this->add_control(
            'percentage_size',
            [
                'label'      => esc_html__('Size', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage'         => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage::before' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'percentage_vertical_position',
            [
                'label'      => esc_html__('Vertical Position', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -100,
                        'max' => 150,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'percentage_horizontal_position',
            [
                'label'      => esc_html__('Horizontal Position', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -100,
                        'max' => 150,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'percentage_typography',
                'selector' => '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage, {{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage::before',
            ]
        );

        $this->add_control(
            'percentage_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-advanced-progress-bar-parcentage::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('progress-bar', 'class', 'bdt-ep-advanced-progress-bar ' . $settings['skills_style'] . ' ' . $settings['skills_extra_style'] . '  ');

?>
        <div <?php echo $this->get_render_attribute_string('progress-bar'); ?>>
            <?php
            foreach ($settings['progress_bars'] as $progress) :
            ?>

                <!-- was here render-bak-code from below -->
                <?php if ($settings['skills_style'] == 'bdt-progress-with-perc') { ?>
                    <div class="bdt-ep-advanced-progress-bar-item elementor-repeater-item-<?php echo esc_attr($progress['_id']); ?>">
                        <?php if ($settings['text_position'] == 'outside-top') { ?>
                            <div class="bdt-ep-advanced-progress-bar-content">
                                <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                            </div>
                        <?php } ?>
                        <div class="bdt-ep-advanced-progress-bar-level">
                            <div class="bdt-ep-advanced-progress-bar-fill " data-max-value="<?php echo ($progress['max_level']['size'] > 0 ? $progress['max_level']['size'] : '100') ?>" data-width="<?php echo esc_attr($progress['level']['size']) ?>%">
                                <span class="bdt-ep-advanced-progress-bar-parcentage"><?php echo esc_html($progress['level']['size']); ?>
                                    <?php echo esc_html($settings['show_max_value'] == 'yes' ? ' / ' . $progress['max_level']['size'] : ''); ?>
                                    <?php echo ($settings['show_perc'] == 'yes') ? '%' : ''; ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($settings['text_position'] == 'outside-bottom') { ?>
                            <div class="bdt-ep-advanced-progress-bar-content">
                                <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } elseif ($settings['skills_style'] == 'bdt-progress-inner-perc') { ?>
                    <div class="bdt-ep-advanced-progress-bar-item elementor-repeater-item-<?php echo esc_attr($progress['_id']); ?>">
                        <div class="bdt-ep-advanced-progress-bar-level">
                            <div class="bdt-ep-advanced-progress-bar-fill " data-max-value="<?php echo ($progress['max_level']['size'] > 0 ? $progress['max_level']['size'] : '100') ?>" data-width="<?php echo esc_attr($progress['level']['size']) ?>%">
                                <div class="bdt-ep-advanced-progress-bar-content">
                                    <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                                    <span class="bdt-ep-advanced-progress-bar-parcentage"><?php echo esc_html($progress['level']['size']); ?>
                                        <?php echo esc_html($settings['show_max_value'] == 'yes' ? ' / ' . $progress['max_level']['size'] : ''); ?>
                                        <?php echo ($settings['show_perc'] == 'yes' ? '%' : '') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } elseif ($settings['skills_style'] == 'bdt-progress-inner-perc-and-name') { ?>
                    <div class="bdt-ep-advanced-progress-bar-item elementor-repeater-item-<?php echo esc_attr($progress['_id']); ?>">
                        <div class="bdt-ep-advanced-progress-bar-level">
                            <div class="bdt-ep-advanced-progress-bar-fill " data-max-value="<?php echo ($progress['max_level']['size'] > 0 ? $progress['max_level']['size'] : '100') ?>" data-width="<?php echo esc_attr($progress['level']['size']) ?>%">
                                <div class="bdt-ep-advanced-progress-bar-content">
                                    <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                                    <span class="bdt-ep-advanced-progress-bar-parcentage"><?php echo esc_html($progress['level']['size']); ?>
                                        <?php echo esc_html($settings['show_max_value'] == 'yes' ? ' / ' . $progress['max_level']['size'] : ''); ?>
                                        <?php echo ($settings['show_perc'] == 'yes' ? '%' : '') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="bdt-ep-advanced-progress-bar-item elementor-repeater-item-<?php echo esc_attr($progress['_id']); ?>">
                        <?php if ($settings['text_position'] == 'outside-top') { ?>
                            <div class="bdt-ep-advanced-progress-bar-content">
                                <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                                <span class="bdt-ep-advanced-progress-bar-parcentage"><?php echo esc_html($progress['level']['size']); ?>
                                    <?php echo esc_html($settings['show_max_value'] == 'yes' ? ' / ' . $progress['max_level']['size'] : ''); ?>
                                    <?php echo ($settings['show_perc'] == 'yes' ? '%' : '') ?>
                                </span>
                            </div>
                        <?php } ?>
                        <div class="bdt-ep-advanced-progress-bar-level">
                            <div class="bdt-ep-advanced-progress-bar-fill " data-max-value="<?php echo ($progress['max_level']['size'] > 0 ? $progress['max_level']['size'] : '100') ?>" data-width="<?php echo esc_attr($progress['level']['size']) ?>%">
                            </div>
                        </div>
                        <?php if ($settings['text_position'] == 'outside-bottom') { ?>
                            <div class="bdt-ep-advanced-progress-bar-content">
                                <span class="bdt-ep-advanced-progress-bar-name"> <?php echo esc_html($progress['name']); ?> </span>
                                <span class="bdt-ep-advanced-progress-bar-parcentage"><?php echo esc_html($progress['level']['size']) ?>
                                    <?php echo esc_html($settings['show_max_value'] == 'yes' ? ' / ' . $progress['max_level']['size'] : ''); ?>
                                    <?php echo ($settings['show_perc'] == 'yes' ? '%' : '') ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php endforeach; ?>
        </div>

<?php
    }
}
