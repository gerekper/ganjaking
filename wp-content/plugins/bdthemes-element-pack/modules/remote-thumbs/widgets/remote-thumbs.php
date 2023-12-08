<?php

namespace ElementPack\Modules\RemoteThumbs\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;

use ElementPack\Utils;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Remote_Thumbs extends Module_Base
{

    public function get_name()
    {
        return 'bdt-remote-thumbs';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Remote Thumbs', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-remote-thumbs bdt-new';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['remote', 'thumbs', 'arrows', 'pagination'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return [
                'ep-styles'
            ];
        } else {
            return [
                'ep-font', 
                'ep-remote-thumbs'
            ];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts']; 
        } else {
            return ['ep-remote-thumbs'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/PKKnqB0vhzE';
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'section_remote_thumbs',
            [
                'label' => esc_html__('Remote Thumbs', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'remote_thumbs_layout',
            [
                'label'      => esc_html__('Layout', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SELECT,
                'default'    => 'horizontal',
                'options'    => [
                    'horizontal'  => esc_html__('Horizontal', 'bdthemes-element-pack'),
                    'vertical'  => esc_html__('Vertical', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'remote_id',
            [
                'label'       => esc_html__('Remote Carousel ID', 'bdthemes-element-pack'),
                'description' => esc_html__('Unique ID of Carousel / Slider. The Elements must be developed with Swiper. No need to add a hash(#) before ID. Note: If you will insert both Elements in the same section, then at first system will try to detect the Element Automatically.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
            ]
        );

        $this->add_control(
            'loop_status',
            [
                'label'       => esc_html__('Slider / Carousel Loop Status', 'bdthemes-element-pack'),
                'description' => esc_html__('If the connected Carousel/Slider Loop feature is off then please deactivate this option.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_remote_thumbs_item',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label'   => esc_html__('Choose Image', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::MEDIA,
                'dynamic' => ['active' => true],
                'default'     => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'thumbs_list',
            [
                'label'       => '',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'image'   => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'image'   => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'image'   => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'image'   => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'image'   => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],

                ],
                'title_field'   => '<img src="{{{ image.url }}}" style="height: 40px; width: 40px; object-fit: cover;">',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'thumbnail_size',
                'default' => 'medium',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_thumbs',
            [
                'label'      => esc_html__('Remote Thumbs', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'thumbs_align',
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
                    '{{WRAPPER}} .bdt-remote-horizontal' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .bdt-remote-vertical' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_spacing',
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
                    '{{WRAPPER}} .bdt-thumbs-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'img_border',
                'selector' => '{{WRAPPER}} .bdt-item img'
            ]
        );

        $this->add_responsive_control(
            'img_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'img_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-item img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_tabs_img'
        );

        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'img_height',
            [
                'label'      => esc_html__('Height', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-item img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'img_width',
            [
                'label' => esc_html__('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-item img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'img_css_filters',
                'selector' => '{{WRAPPER}} .bdt-item img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'img_shadow',
                'selector' => '{{WRAPPER}} .bdt-item img'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_active_tab',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'active_img_height',
            [
                'label' => esc_html__('Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-item.bdt-active img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'active_img_width',
            [
                'label' => esc_html__('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => .1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-item.bdt-active img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'active_img_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'img_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-item.bdt-active img' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'active_img_css_filters',
                'selector' => '{{WRAPPER}} .bdt-item.bdt-active img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'active_img_shadow',
                'selector' => '{{WRAPPER}} .bdt-item.bdt-active img'
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-remote-thumbs-' . $this->get_id();

        $this->add_render_attribute('remote', [
            'class' => 'bdt-remote-thumbs',
            'id'    => $id,
            'data-settings' => [
                wp_json_encode(array_filter([
                    'id'         => '#' . $id,
                    'remoteId'   => !empty($settings['remote_id']) ? '#' . $settings['remote_id'] : false,
                    'loopStatus' => 'yes' == $settings['loop_status'] ? true : false,
                ]))
            ]
        ]);

?>
        <div <?php echo $this->get_render_attribute_string('remote'); ?>>
            <div class="bdt-thumbs-wrapper  bdt-remote-<?php echo $settings['remote_thumbs_layout']; ?>">
                <?php
                foreach ($settings['thumbs_list'] as $index => $item) :
                ?>
                    <a href="javascript:void(0);" class="bdt-item" data-index="<?php echo esc_attr($index); ?>">
                        <?php echo Group_Control_Image_Size::get_attachment_image_html($item, 'thumbnail_size', 'image'); ?>
                    </a>
                <?php
                endforeach;
                ?>
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
