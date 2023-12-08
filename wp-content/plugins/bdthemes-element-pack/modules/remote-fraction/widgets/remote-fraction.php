<?php

namespace ElementPack\Modules\RemoteFraction\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Remote_Fraction extends Module_Base
{

    public function get_name()
    {
        return 'bdt-remote-fraction';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Remote Fraction', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-remote-fraction bdt-new';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['remote', 'fraction', 'arrows'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return [
                'ep-styles'
            ];
        } else {
            return [ 'ep-font', 'ep-remote-fraction' ];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-remote-fraction'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/UfmwcTjX7L8';
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'section_remote_fraction',
            [
                'label' => esc_html__('Remote Fraction', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'remote_id',
            [
                'label'       => esc_html__('Remote Carousel ID', 'bdthemes-element-pack'),
                'description' => esc_html__('Unique ID of Carousel. Not need to add #', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
            ]
        );

        $this->add_responsive_control(
            'vertical_align',
            [
                'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__('Middle', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fraction-wrapper' => '{{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'top'    => 'align-items: flex-start;',
                    'middle' => 'align-items: center;',
                    'bottom' => 'align-items: baseline;'
                ]
            ]
        );

        $this->add_control(
            'digit_pad',
            [
                'label'   => esc_html__('Digit Pad', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 2,
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_fraction',
            [
                'label'      => esc_html__('Remote Fraction', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'fraction_align',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start'    => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fraction-wrapper' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'fraction_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fraction-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs('tabs_fraction_style');

        $this->start_controls_tab(
            'fraction_current',
            [
                'label'     => esc_html__('Current', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'text_shadow',
                'label'    => esc_html__('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-current',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'fraction_typography',
                'selector' => '{{WRAPPER}} .bdt-current',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'fraction_active',
            [
                'label'     => esc_html__('Total', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'text_color_total',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-total' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'text_shadow_total',
                'label'    => esc_html__('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-total',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'fraction_typography_total',
                'selector' => '{{WRAPPER}} .bdt-total',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_separator',
            [
                'label'      => esc_html__('Separator', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'separator_total',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-fr-separator' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'separator_size',
            [
                'label' => __('Size', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min'  => 10,
                        'max'  => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fr-separator' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-remote-fraction-' . $this->get_id();

        $this->add_render_attribute('remote', [
            'class' => 'bdt-remote-fraction',
            'id'    => $id,
            'data-settings' => [
                wp_json_encode(array_filter([
                    'id'         => '#' . $id,
                    'remoteId'   => !empty($settings['remote_id']) ? '#' . $settings['remote_id'] : false,
                    'pad'        => !empty($settings['digit_pad']) ? $settings['digit_pad'] : 1,
                ]))
            ]
        ]);

?>
        <div <?php echo $this->get_render_attribute_string('remote'); ?>>
            <div class="bdt-fraction-wrapper">
                <span class="bdt-current"></span>
                <span class="bdt-fr-separator">/</span>
                <span class="bdt-total"></span>
            </div>
        </div>

        <div id="<?php echo esc_attr($id) . '-notice' ?>" class="bdt-alert-danger bdt-hidden" bdt-alert>
            <a class="bdt-alert-close" bdt-close></a>
            <p><?php
                echo esc_html__('Sorry, your ID is maybe not correct. And please make sure that your selected element is developed with Swiper.', 'bdthemes-element-pack');
                ?></p>
        </div>

<?php
    }
}
