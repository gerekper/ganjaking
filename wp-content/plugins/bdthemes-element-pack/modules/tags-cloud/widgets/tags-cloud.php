<?php

namespace ElementPack\Modules\TagsCloud\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use ElementPack\Base\Module_Base;
use ElementPack\Modules\TagsCloud\Skins;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Tags_Cloud extends Module_Base {

    public function get_name() {
        return 'bdt-tags-cloud';
    }

    public function get_title() {
        return BDTEP . esc_html__('Tags Cloud', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-tags-cloud';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['tags', 'cloud'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-tags-cloud'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['tags-cloud', 'tags-exCanvas', 'ep-scripts'];
        } else {
            return ['tags-cloud', 'tags-exCanvas', 'ep-tags-cloud'];
        }
    }

    //   public function get_custom_help_url() {
    //     return 'https://youtu.be/faIeyW7LOJ8';
    // }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Animated($this));
        $this->add_skin(new Skins\Skin_Cloud($this));
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Tags Cloud', 'bdthemes-dark-mode'),
            ]
        );

        $this->add_control(
            'custom_post_type',
            [
                'label' => __('Custom Post Type', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'open_new_window',
            [
                'label' => __('Open In a New Window', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'custom_post_type_input',
            [
                'label'       => __('Custom Post Name', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('Custom Post Name', 'bdthemes-element-pack'),
                'description' => 'Example: post_tag, product_tag etc.',
                'dynamic'     => [
                    'active' => true,
                ],
                'condition' => [
                    'custom_post_type' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'cloud_style',
            [
                'label'     => __('Cloud Style', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'circle',
                'options'   => [
                    'circle' => __('Circle', 'bdthemes-element-pack'),
                    'square' => __('Square', 'bdthemes-element-pack'),
                    'star'   => __('Star', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    '_skin' => 'bdt-cloud',
                ],
            ]
        );

        $this->add_responsive_control(
            'globe_height',
            [
                'label'     => esc_html__('Height', 'bdthemes-element-pack'),
                'description' => 'Note:- Please set the height as per your design need, otherwise Globe will not work perfectly.',
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                    ],
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_active_cursor',
            [
                'label'     => __('Active Cursor', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'pointer',
                'options'   => [
                    'pointer'   => __('Pointer', 'bdthemes-element-pack'),
                    'crosshair' => __('Crosshair', 'bdthemes-element-pack'),
                    'cursor'    => __('Cursor', 'bdthemes-element-pack'),
                    'text'      => __('Text', 'bdthemes-element-pack'),
                    'wait'      => __('Wait', 'bdthemes-element-pack'),
                    'progress'  => __('Progress', 'bdthemes-element-pack'),
                    'help'      => __('Help', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'globe_depth',
            [
                'label'     => esc_html__('Depth', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 80,
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_animation_speed',
            [
                'label'     => esc_html__('Animation Speed', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );


        $this->add_control(
            'globe_drag_control',
            [
                'label' => __('Drag Control', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_outline_method',
            [
                'label'     => __('Method', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'outline',
                'options'   => [
                    'outline' => __('Outline', 'bdthemes-element-pack'),
                    'classic' => __('Classic', 'bdthemes-element-pack'),
                    'block'   => __('Block', 'bdthemes-element-pack'),
                    'colour'  => __('Colour', 'bdthemes-element-pack'),
                    'size'    => __('Size', 'bdthemes-element-pack'),
                    'none'    => __('None', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_freeze_active',
            [
                'label' => __('Freeze Active', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_wheel_zoom',
            [
                'label' => __('Wheel Zoom', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_fade_in',
            [
                'label'     => esc_html__('Visible Time', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_basic',
            [
                'label' => __('Basic Tags', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'basic_tags_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'basic_tags_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'basic_tags_typo',
                'selector'  => '{{WRAPPER}} ul.bdt-tags-list li a',
            ]
        );

        $this->start_controls_tabs('tabs_mode_style');

        $this->start_controls_tab(
            'basic_tags_normal',
            [
                'label'     => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'basic_tags_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a' => 'color: {{VALUE}}  !important;',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'basic_tags_text_shadow',
                'label' => __('Text Shadow', 'bdthemes-element-pack') . BDTEP_NC,
                'selector' => '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a',
            ]
        );

        $this->add_control(
            'basic_tags_bg_type',
            [
                'label'     => __('Background Type', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'random',
                'options'   => [
                    'random' => __('Random', 'bdthemes-element-pack'),
                    'solid'  => __('Solid', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'basic_tags_solid_bg',
            [
                'label'     => __('Background Solid', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'basic_tags_bg_type' => 'solid',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'basic_tags_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a',
            ]
        );

        $this->add_control(
            'basic_tags_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'basic_tags_hover',
            [
                'label'     => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'basic_tags_hover_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a:hover' => 'color: {{VALUE}}  !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'basic_tags_text_shadow_hover',
                'label' => __('Text Shadow', 'bdthemes-element-pack') . BDTEP_NC,
                'selector' => '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a:hover',
            ]
        );

        $this->add_control(
            'basic_tags_hover_bg_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a:hover' => 'background-color: {{VALUE}}  !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'basic_tags_border_hover',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a:hover',
            ]
        );

        $this->add_control(
            'basic_tags_hover_effect',
            [
                'label'     => __('Hover Effect', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'scale(1.1)',
                'options'   => [
                    'scale(1.1)'                   => __('Basic', 'bdthemes-element-pack'),
                    'rotate(5deg)'                 => __('Rotate ', 'bdthemes-element-pack'),
                    'rotate(10deg)'                => __('Rotate 1x', 'bdthemes-element-pack'),
                    'rotate(20deg)'                => __('Rotate 2x', 'bdthemes-element-pack'),
                    'rotate(360deg)'               => __('Rotate 360', 'bdthemes-element-pack'),
                    'translate3d(7px, 14px, 10px)' => __('Translate', 'bdthemes-element-pack'),
                    'skew(8deg, -9deg)'            => __('Skew', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tags-cloud .bdt-tags-list li a:hover' => 'transform: {{VALUE}}  !important;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        // start skin animated

        $this->start_controls_section(
            'animated_tags_style',
            [
                'label' => __('Animated Tags', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => 'bdt-animated',
                ],
            ]
        );

        $this->add_control(
            'globe_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'globe_text_bg',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'globe_shadow_color',
            [
                'label'     => __('Text Shadow Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => 'none',
            ]
        );

        $this->add_control(
            'globe_shadow_blur',
            [
                'label'     => esc_html__('Shadow Blur', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 10,
                ],
            ]
        );

        $this->add_control(
            'globe_text_bg_radius',
            [
                'label'     => esc_html__('Background Radius', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'globe_outline_heading',
            [
                'label'     => __('Outline', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'globe_outline_colour',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ddd',
            ]
        );

        $this->add_control(
            'globe_outline_thickness',
            [
                'label'     => esc_html__('Thickness', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 2,
                ],
            ]
        );

        $this->add_control(
            'globe_bg_outline_thickness',
            [
                'label'     => esc_html__('Background Thickness', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->add_control(
            'globe_outline_dash',
            [
                'label'     => esc_html__('Dash', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );

        $this->add_control(
            'globe_outline_dash_space',
            [
                'label'     => esc_html__('Dash Space', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 2,
                ],
            ]
        );
        $this->add_control(
            'globe_outline_dash_speed',
            [
                'label'     => esc_html__('Dash Speed', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 3,
                ],
            ]
        );

        $this->add_control(
            'globe_outline_increase',
            [
                'label'     => esc_html__('Increase', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 5,
                ],
            ]
        );

        $this->add_control(
            'globe_outline_border_radius',
            [
                'label'     => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 2,
                ],
            ]
        );

        $this->end_controls_section();

        // end skin animated

        // start skin cloud

        $this->start_controls_section(
            'section_style_cloud',
            [
                'label' => __('Cloud Tags', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => 'bdt-cloud',
                ],
            ]
        );

        $this->add_control(
            'cloud_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'random-dark',
                'options'   => [
                    'random-dark'  => __('Random Dark', 'bdthemes-element-pack'),
                    'random-light' => __('Random Light', 'bdthemes-element-pack'),
                    'gradient'     => __('Gradient', 'bdthemes-element-pack'),
                    'custom'        => __('Custom', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    '_skin' => 'bdt-cloud',
                ],
            ]
        );

        $this->add_control(
            'cloud_custom_color',
            [
                'label'     => __('Custom Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'cloud_color' => 'custom',
                ],
            ]
        );

        $this->end_controls_section();

        // end skin cloud

    }

    public function render() {
        $settings  = $this->get_settings_for_display();
        $basic     = 'basic';

        $taxonomy_filter = (isset($settings['custom_post_type_input']) && !empty($settings['custom_post_type_input'])) ? $settings['custom_post_type_input'] : 'post_tag';

        $tag_cloud = $this->wp_tag_cloud(
            $basic,
            array(
                'taxonomy' => $taxonomy_filter, //$current_taxonomy,
                'echo'     => false,
                // 'show_count' => '20', //$show_count,
            )
        );

        if (empty($settings['basic_tags_solid_bg'])) {
            $custom_bg = '#3FB8FD';
        } else {
            $custom_bg = $settings['basic_tags_solid_bg'];
        }



        $this->add_render_attribute('basic_tags', 'class', 'bdt-tags-cloud skin-default');
        $this->add_render_attribute(
            [
                'basic_tags' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            "basic_tags_bg_type"   => $settings['basic_tags_bg_type'],
                            "basic_tags_solid_bg" => $custom_bg
                        ])),
                    ],
                ],
            ]
        );

?>

        <div <?php echo $this->get_render_attribute_string('basic_tags'); ?>>
            <ul class="bdt-tags-list">
                <?php
                echo $tag_cloud;
                ?>
            </ul>
        </div>

<?php
    }

    public function wp_tag_cloud($animaitedSetting, $args = array()) {
        $defaults = array(
            'smallest'   => 12,
            'largest'    => 120,
            'unit'       => 'pt',
            'number'     => 45,
            'format'     => 'flat',
            'separator'  => "\n",
            'orderby'    => 'rand',
            'order'      => 'ASC',
            'exclude'    => '',
            'include'    => '',
            'link'       => 'view',
            'taxonomy'   => 'post_tag',
            'post_type'  => '',
            'echo'       => true,
            'show_count' => 0,
        );
        $args = wp_parse_args($args, $defaults);

        $tags = get_terms(
            array_merge(
                $args,
                array(
                    'orderby' => 'count',
                    'order'   => 'DESC',
                )
            )
        ); // Always query top tags

        if (empty($tags) || is_wp_error($tags)) {
            return element_pack_get_alert(__('Sorry, your entered tag is not valid!', 'bdthemes-element-pack'));
        }

        foreach ($tags as $key => $tag) {
            if ('edit' == $args['link']) {
                $link = get_edit_term_link($tag->term_id, $tag->taxonomy, $args['post_type']);
            } else {
                $link = get_term_link(intval($tag->term_id), $tag->taxonomy);
            }
            if (is_wp_error($link)) {
                return element_pack_get_alert(__('Sorry, your entered tag is not valid!', 'bdthemes-element-pack'));
            }

            $tags[$key]->link = $link;
            $tags[$key]->id   = $tag->term_id;
        }

        $return = $this->wp_generate_tag_cloud($tags, $animaitedSetting, $args); // Here's where those top tags get sorted according to $args

        /**
         * Filters the tag cloud output.
         *
         * @since 2.3.0
         *
         * @param string $return HTML output of the tag cloud.
         * @param array  $args   An array of tag cloud arguments.
         */
        $return = apply_filters('wp_tag_cloud', $return, $args);

        if ('array' == $args['format'] || empty($args['echo'])) {
            return $return;
        }

        echo $return;
    }

    public function wp_generate_tag_cloud($tags, $animaitedSetting, $args = '') {
        $settings = $this->get_settings_for_display();

        $target = (isset($settings['open_new_window']) && $settings['open_new_window'] == 'yes') ? 'target="_blank"' : '';

        $defaults = array(
            'smallest'                   => 12,
            'largest'                    => 100,
            'unit'                       => 'pt',
            'number'                     => 0,
            'format'                     => 'flat',
            'separator'                  => "\n",
            'orderby'                    => 'name',
            'order'                      => 'ASC',
            'topic_count_text'           => null,
            'topic_count_text_callback'  => null,
            'topic_count_scale_callback' => 'default_topic_count_scale',
            'filter'                     => 1,
            'show_count'                 => 0,
        );

        $args = wp_parse_args($args, $defaults);

        $return = ('array' === $args['format']) ? array() : '';

        if (empty($tags)) {
            return $return;
        }

        // Juggle topic counts.
        if (isset($args['topic_count_text'])) {
            // First look for nooped plural support via topic_count_text.
            $translate_nooped_plural = $args['topic_count_text'];
        } elseif (!empty($args['topic_count_text_callback'])) {
            // Look for the alternative callback style. Ignore the previous default.
            if ($args['topic_count_text_callback'] === 'default_topic_count_text') {
                /* translators: %s: Number of items (tags). */
                $translate_nooped_plural = _n_noop('%s item', '%s items', 'bdthemes-element-pack');
            } else {
                $translate_nooped_plural = false;
            }
        } elseif (isset($args['single_text']) && isset($args['multiple_text'])) {
            // If no callback exists, look for the old-style single_text and multiple_text arguments.
            // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingle,WordPress.WP.I18n.NonSingularStringLiteralPlural
            $translate_nooped_plural = _n_noop($args['single_text'], $args['multiple_text']);
        } else {
            // This is the default for when no callback, plural, or argument is passed in.
            /* translators: %s: Number of items (tags). */
            $translate_nooped_plural = _n_noop('%s item', '%s items', 'bdthemes-element-pack');
        }

        /**
         * Filters how the items in a tag cloud are sorted.
         *
         * @since 2.8.0
         *
         * @param WP_Term[] $tags Ordered array of terms.
         * @param array     $args An array of tag cloud arguments.
         */
        $tags_sorted = apply_filters('tag_cloud_sort', $tags, $args);
        if (empty($tags_sorted)) {
            return $return;
        }

        if ($tags_sorted !== $tags) {
            $tags = $tags_sorted;
            unset($tags_sorted);
        } else {
            if ('RAND' === $args['order']) {
                shuffle($tags);
            } else {
                // SQL cannot save you; this is a second (potentially different) sort on a subset of data.
                if ('name' === $args['orderby']) {
                    uasort($tags, '_wp_object_name_sort_cb');
                } else {
                    uasort($tags, '_wp_object_count_sort_cb');
                }

                if ('DESC' === $args['order']) {
                    $tags = array_reverse($tags, true);
                }
            }
        }

        if ($args['number'] > 0) {
            $tags = array_slice($tags, 0, $args['number']);
        }

        $counts      = array();
        $real_counts = array(); // For the alt tag
        foreach ((array) $tags as $key => $tag) {
            $real_counts[$key] = $tag->count;
            $counts[$key]      = call_user_func($args['topic_count_scale_callback'], $tag->count);
        }

        $min_count = min($counts);
        $spread    = max($counts) - $min_count;
        if ($spread <= 0) {
            $spread = 1;
        }
        $font_spread = $args['largest'] - $args['smallest'];
        if ($font_spread < 0) {
            $font_spread = 1;
        }
        $font_step = $font_spread / $spread;

        $aria_label = false;
        /*
         * Determine whether to output an 'aria-label' attribute with the tag name and count.
         * When tags have a different font size, they visually convey an important information
         * that should be available to assistive technologies too. On the other hand, sometimes
         * themes set up the Tag Cloud to display all tags with the same font size (setting
         * the 'smallest' and 'largest' arguments to the same value).
         * In order to always serve the same content to all users, the 'aria-label' gets printed out:
         * - when tags have a different size
         * - when the tag count is displayed (for example when users check the checkbox in the
         *   Tag Cloud widget), regardless of the tags font size
         */
        if ($args['show_count'] || 0 !== $font_spread) {
            $aria_label = true;
        }

        // Assemble the data that will be used to generate the tag cloud markup.
        $tags_data = array();
        foreach ($tags as $key => $tag) {
            $tag_id = isset($tag->id) ? $tag->id : $key;

            $count      = $counts[$key];
            $real_count = $real_counts[$key];

            if ($translate_nooped_plural) {
                $formatted_count = sprintf(translate_nooped_plural($translate_nooped_plural, $real_count), number_format_i18n($real_count));
            } else {
                $formatted_count = call_user_func($args['topic_count_text_callback'], $real_count, $tag, $args);
            }

            $tags_data[] = array(
                'id'              => $tag_id,
                'url'             => '#' != $tag->link ? $tag->link : '#',
                'role'            => '#' != $tag->link ? '' : ' role="button"',
                'name'            => $tag->name,
                'formatted_count' => $formatted_count,
                'slug'            => $tag->slug,
                'real_count'      => $real_count,
                'class'           => 'tag-cloud-link tag-link-' . $tag_id,
                'font_size'       => $args['smallest'] + ($count - $min_count) * $font_step,
                'aria_label'      => $aria_label ? sprintf(' aria-label="%1$s (%2$s)"', esc_attr($tag->name), esc_attr($formatted_count)) : '',
                'show_count'      => $args['show_count'] ? '<span class="tag-link-count"> (' . $real_count . ')</span>' : '',
            );
        }

        /**
         * Filters the data used to generate the tag cloud.
         *
         * @since 4.3.0
         *
         * @param array $tags_data An array of term data for term used to generate the tag cloud.
         */
        $tags_data = apply_filters('wp_generate_tag_cloud_data', $tags_data);

        $a = array();

        if ($animaitedSetting == 'animaitedTags') {
            // Generate the output links array.
            foreach ($tags_data as $key => $tag_data) {
                $class = $tag_data['class'] . ' tag-link-position-' . ($key + 1);
                $a[]   = sprintf(
                    '<li><a href="%1$s"%2$s class="%3$s" ' . $target . ' data-weight="%4$s"%5$s>%6$s%7$s</a></li>',
                    esc_url($tag_data['url']),
                    $tag_data['role'],
                    esc_attr($class),
                    esc_attr(str_replace(',', '.', $tag_data['font_size'])),
                    $tag_data['aria_label'],
                    esc_html($tag_data['name']),
                    $tag_data['show_count']

                );
            }
        } elseif ($animaitedSetting == 'basic') {
            // Generate the output links array.
            foreach ($tags_data as $key => $tag_data) {
                $class = $tag_data['class'] . ' tag-link-position-' . ($key + 1);
                $a[]   = sprintf(
                    '<li><a href="%1$s"%2$s class="%3$s" ' . $target . ' data-weight="%4$s"%5$s>%6$s%7$s</a></li>',
                    esc_url($tag_data['url']),
                    $tag_data['role'],
                    esc_attr($class),
                    esc_attr(str_replace(',', '.', $tag_data['font_size'])),
                    $tag_data['aria_label'],
                    esc_html($tag_data['name']),
                    $tag_data['show_count']

                );
            }
        } else {
            // Generate the output links array.
            foreach ($tags_data as $key => $tag_data) {
                $class = $tag_data['class'] . ' tag-link-position-' . ($key + 1);
                $a[]   = sprintf(
                    '<span data-weight="%4$s"><a href="%1$s"%2$s ' . $target . '  class="%3$s" %5$s>%6$s%7$s</a> </span>',
                    esc_url($tag_data['url']),
                    $tag_data['role'],
                    esc_attr($class),
                    esc_attr(str_replace(',', '.', $tag_data['font_size'])),
                    $tag_data['aria_label'],
                    esc_html($tag_data['name']),
                    $tag_data['show_count']

                );
            }
        }

        switch ($args['format']) {
            case 'array':
                $return = &$a;
                break;
            case 'list':
                /*
                 * Force role="list", as some browsers (sic: Safari 10) don't expose to assistive
                 * technologies the default role when the list is styled with `list-style: none`.
                 * Note: this is redundant but doesn't harm.
                 */
                $return = "<ul class='wp-tag-cloud' role='list'>\n\t<li>";
                $return .= join("</li>\n\t<li>", $a);
                $return .= "</li>\n</ul>\n";
                break;
            default:
                $return = join($args['separator'], $a);
                break;
        }

        if ($args['filter']) {
            /**
             * Filters the generated output of a tag cloud.
             *
             * The filter is only evaluated if a true value is passed
             * to the $filter argument in wp_generate_tag_cloud().
             *
             * @since 2.3.0
             *
             * @see wp_generate_tag_cloud()
             *
             * @param array|string $return String containing the generated HTML tag cloud output
             *                             or an array of tag links if the 'format' argument
             *                             equals 'array'.
             * @param WP_Term[]    $tags   An array of terms used in the tag cloud.
             * @param array        $args   An array of wp_generate_tag_cloud() arguments.
             */
            return apply_filters('wp_generate_tag_cloud', $return, $tags, $args);
        } else {
            return $return;
        }
    }
}
