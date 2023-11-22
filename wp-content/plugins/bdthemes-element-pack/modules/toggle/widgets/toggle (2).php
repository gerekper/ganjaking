<?php

namespace ElementPack\Modules\Toggle\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Toggle extends Module_Base {

    public function get_name() {
        return 'bdt-toggle';
    }

    public function get_title() {
        return BDTEP . esc_html__('Read More Toggle', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-toggle';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['toggle', 'accordion', 'tab', 'unfold', 'expand', 'collapse', 'content', 'show', 'hide', 'element pack', 'read more toggle'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-toggle'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-toggle'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/7_jk_NvbKls';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_title',
            [
                'label' => esc_html__('Toggle', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'toggle_title',
            [
                'label'       => esc_html__('Normal Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'default'     => esc_html__('Show All', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'toggle_open_title',
            [
                'label'       => esc_html__('Opened Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'default'     => esc_html__('Collapse', 'bdthemes-element-pack'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'source',
            [
                'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'custom'    => esc_html__('Custom', 'bdthemes-element-pack'),
                    "elementor" => esc_html__('Elementor Template', 'bdthemes-element-pack'),
                    'anywhere'  => esc_html__('AE Template', 'bdthemes-element-pack'),
                    'widget'    => esc_html__('Widget Selector', 'bdthemes-element-pack'),
                ],
            ]
        );

        // $this->add_control(
        //     'template_id',
        //     [
        //         'label'       => __('Select Template', 'bdthemes-element-pack'),
        //         'type'        => Controls_Manager::SELECT,
        //         'default'     => '0',
        //         'options'     => element_pack_et_options(),
        //         'label_block' => 'true',
        //         'condition'   => ['source' => "elementor"],
        //     ]
        // );

        // $this->add_control(
        //     'anywhere_id',
        //     [
        //         'label'       => esc_html__('Select Template', 'bdthemes-element-pack'),
        //         'type'        => Controls_Manager::SELECT,
        //         'default'     => '0',
        //         'options'     => element_pack_ae_options(),
        //         'label_block' => 'true',
        //         'condition'   => ['source' => 'anywhere'],
        //     ]
        // );
        $this->add_control(
            'template_id',
            [
                'label'       => __('Select Template', 'bdthemes-element-pack'),
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
                'query_args'  => [
                    'query'        => 'elementor_template',
                ],
                'condition'   => ['source' => "elementor"],
            ]
        );
        $this->add_control(
            'anywhere_id',
            [
                'label'       => __('Select Template', 'bdthemes-element-pack'),
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
                'query_args'  => [
                    'query'        => 'anywhere_template',
                ],
                'condition'   => ['source' => "anywhere"],
            ]
        );

        $this->add_control(
            'toggle_content',
            [
                'label'      => esc_html__('Content', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::WYSIWYG,
                'dynamic'    => ['active' => true],
                'default'    => esc_html__('Toggle Content', 'bdthemes-element-pack'),
                'show_label' => false,
                'condition'  => ['source' => 'custom'],
            ]
        );


        $this->add_control(
            'source_selector',
            [
                'label'     => __('Widget Selector', 'bdthemes-element-pack'),
                'description'     => __('Enter your widget ID or class (for example: #my-gallery) which widget you want to attached the toogle.', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::TEXT,
                'condition' => ['source' => "widget"],
            ]
        );


        $this->add_responsive_control(
            'widget_visibility',
            [
                'label'      => esc_html__('Widget Visibility', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range'      => [
                    'px' => [
                        'min'  => 100,
                        'max'  => 600,
                        'step' => 5,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 30,
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_show',
            [
                'label'   => esc_html__('Toggle Icon', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'toggle_icon_position',
            [
                'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'right',
                'options'   => [
                    'left'    => esc_html__('Left', 'bdthemes-element-pack'),
                    'right'   => esc_html__('Right', 'bdthemes-element-pack')
                ],
                'condition' => [
                    'toggle_icon_show' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_normal',
            [
                'label'            => esc_html__('Normal Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon_normal',
                'default'          => [
                    'value'   => 'fas fa-plus',
                    'library' => 'fa-solid',
                ],
                'condition'        => [
                    'toggle_icon_show!' => '',
                ],
            ]
        );

        $this->add_control(
            'toggle_icon_active',
            [
                'label'            => esc_html__('Active Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon_active',
                'default'          => [
                    'value'   => 'fas fa-minus',
                    'library' => 'fa-solid',
                ],
                'condition'        => [
                    'toggle_icon_show!' => '',
                ],
            ]
        );

        $this->add_control(
            'toggle_initially_open',
            [
                'label'     => esc_html__('Initially Opened', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'active_scrollspy',
            [
                'label'     => esc_html__('Active Scrollspy', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'separator' => 'before',
                // 'condition'    =>[
                //  'source!' => 'widget',
                // ],
            ]
        );

        $this->add_control(
            'hash_location',
            [
                'label'     => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'active_scrollspy' => 'yes',
                    'source!'          => 'widget',
                ],

            ]
        );

        $this->add_control(
            'scrollspy_top_offset',
            [
                'label'      => esc_html__('Top Offset ', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'active_scrollspy',
                            'value' => 'yes'
                        ],
                        [
                            'name'  => 'hash_location',
                            'value' => 'yes'
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'scrollspy_time',
            [
                'label'      => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range'      => [
                    'px' => [
                        'min'  => 500,
                        'max'  => 5000,
                        'step' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'active_scrollspy',
                            'value' => 'yes'
                        ],
                        [
                            'name'  => 'hash_location',
                            'value' => 'yes'
                        ],
                    ]
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title'     => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'title_shadow',
                'selector' => '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-item .bdt-show-hide-title',
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title',
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
            ]
        );


        $this->add_control(
            'shadow_color',
            [
                'label'     => esc_html__('Shadow Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide-container .bdt-show-hide .bdt-show-hide-item .bdt-show-hide-title:before' => 'background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, {{VALUE}} 100%);',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'shadow_height',
            [
                'label'     => esc_html__('Shadow Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide-container .bdt-show-hide .bdt-show-hide-item .bdt-show-hide-title:before' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-item.bdt-open .bdt-show-hide-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_content_style',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_align',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-content',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_icon',
            [
                'label'     => esc_html__('Icon', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'toggle_icon_show' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_icon_style');

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title .bdt-show-hide-icon'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-title .bdt-show-hide-icon svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_space',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-icon.left-position' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_active_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-item.bdt-open .bdt-show-hide-icon'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-show-hide .bdt-show-hide-item.bdt-open .bdt-show-hide-icon svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();

        $this->add_render_attribute(
            [
                'toggle_data' => [
                    'id'    => 'bdt-show-hide-' . esc_attr($id),
                    'class' => 'bdt-show-hide',
                    // 'bdt-show-hide' => [
                    //  wp_json_encode( array_filter( [
                    //      "collapsible" => true,
                    //      "transition"  => "ease-in-out"
                    //  ] ) )
                    // ]
                ]
            ]
        );

        $elementor_vp_lg = get_option('elementor_viewport_lg');
        $elementor_vp_md = get_option('elementor_viewport_md');
        $viewport_lg     = !empty($elementor_vp_lg) ? $elementor_vp_lg - 1 : 1023;
        $viewport_md     = !empty($elementor_vp_md) ? $elementor_vp_md - 1 : 767;

        $widget_visibility        = ($settings['source'] == 'widget') ? $settings['widget_visibility']['size'] : '';
        $widget_visibility_tablet = isset($settings['widget_visibility_tablet']['size']) ? $settings['widget_visibility_tablet']['size'] : '';
        $widget_visibility_mobile = isset($settings['widget_visibility_mobile']['size']) ? $settings['widget_visibility_mobile']['size'] : '';

        $this->add_render_attribute(
            [
                'toggle_data' => [
                    'data-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"                   => esc_attr($id),
                                "status_scrollspy"     => $settings['active_scrollspy'],
                                "scrollspy_top_offset" => (isset($settings['scrollspy_top_offset']['size']) ? $settings['scrollspy_top_offset']['size'] : 70),
                                "scrollspy_time"       => (isset($settings['scrollspy_time']['size']) ? $settings['scrollspy_time']['size'] : 1000),
                                "hash_location"        => $settings['hash_location'],
                                "toggle_initially_open"     => ($settings['toggle_initially_open'] == 'yes') ? 'yes' : ' ',
                                "by_widget_selector_status" => ($settings['source'] == 'widget') ? 'yes' : 'no',
                                "source_selector"           => ($settings['source'] == 'widget') ? $settings['source_selector'] : '',
                                "widget_visibility"         => $widget_visibility,
                                "widget_visibility_tablet"  => $widget_visibility_tablet,
                                "widget_visibility_mobile"  => $widget_visibility_mobile,
                                "viewport_lg"               => $viewport_lg,
                                "viewport_md"               => $viewport_md,

                            ])
                        ),
                    ],
                ],
            ]
        );

        if (!isset($settings['icon_normal']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['icon_normal'] = 'fas fa-plus';
        }

        if (!isset($settings['icon_active']) && !Icons_Manager::is_migration_allowed()) {
            // add old default
            $settings['icon_active'] = 'fas fa-minus';
        }

        $migrated = isset($settings['__fa4_migrated']['toggle_icon_normal']);
        $is_new   = empty($settings['icon_normal']) && Icons_Manager::is_migration_allowed();

        $active_migrated = isset($settings['__fa4_migrated']['toggle_icon_active']);
        $active_is_new   = empty($settings['icon_active']) && Icons_Manager::is_migration_allowed();

        $this->add_render_attribute('tab_title', ['class' => ['bdt-show-hide-title'],]);
        // if( $settings['hash_location'] == 'yes' ){
        //  $this->add_render_attribute( 'tab_title', [ 'href' => [ '#bdt-show-hide-'.$this->get_id() ], ] );
        // }

        $this->add_render_attribute('toggle_content', ['class' => ['bdt-show-hide-content'],]);
        $this->add_inline_editing_attributes('toggle_content', 'advanced');

?>

        <div class="bdt-show-hide-container">
            <div <?php echo $this->get_render_attribute_string('toggle_data'); ?>>
                <div class="bdt-show-hide-item<?php echo ('yes' == $settings['toggle_initially_open']) ? ' bdt-open ' : ''; ?> ">
                    <div <?php echo $this->get_render_attribute_string('toggle_content'); ?>>
                        <?php
                        if ('custom' == $settings['source'] and !empty($settings['toggle_content'])) {
                            echo $this->parse_text_editor($settings['toggle_content']);
                        } elseif ("elementor" == $settings['source'] and !empty($settings['template_id'])) {
                            echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['template_id']);
                            echo element_pack_template_edit_link($settings['template_id']);
                        } elseif ('anywhere' == $settings['source'] and !empty($settings['anywhere_id'])) {
                            echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($settings['anywhere_id']);
                            echo element_pack_template_edit_link($settings['anywhere_id']);
                        } elseif ('widget' == $settings['source'] and !empty($settings['source_selector'])) {
                            echo " ";
                        }
                        ?>
                    </div>

                    <a <?php echo $this->get_render_attribute_string('tab_title'); ?> href='javascript:void(0)'>
                        <!--  -->
                        <?php if ('yes' === $settings['toggle_icon_show']) : ?>
                            <?php if ($settings['toggle_icon_position'] == 'left') : ?>
                                <span class="bdt-show-hide-icon left-position" aria-hidden="true">
                                    <span class="bdt-show-hide-icon-closed">
                                        <?php if ($is_new || $migrated) :
                                            Icons_Manager::render_icon($settings['toggle_icon_normal'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                                        else : ?>
                                            <i class="<?php echo esc_attr($settings['icon_normal']); ?>" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </span>

                                    <span class="bdt-show-hide-icon-opened">
                                        <?php if ($active_is_new || $active_migrated) :
                                            Icons_Manager::render_icon($settings['toggle_icon_active'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                                        else : ?>
                                            <i class="<?php echo esc_attr($settings['icon_active']); ?>" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </span>

                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!--  -->
                        <span class="bdt-toggle-open">
                            <?php echo wp_kses($settings['toggle_title'], element_pack_allow_tags('title')); ?>
                        </span>
                        <span class="bdt-toggle-close">
                            <?php echo wp_kses($settings['toggle_open_title'], element_pack_allow_tags('title'));
                            ?>
                        </span>
                        <?php if ('yes' === $settings['toggle_icon_show']) : ?>
                            <?php if ($settings['toggle_icon_position'] == 'right') : ?>
                                <span class="bdt-show-hide-icon" aria-hidden="true">

                                    <span class="bdt-show-hide-icon-closed">
                                        <?php if ($is_new || $migrated) :
                                            Icons_Manager::render_icon($settings['toggle_icon_normal'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                                        else : ?>
                                            <i class="<?php echo esc_attr($settings['icon_normal']); ?>" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </span>

                                    <span class="bdt-show-hide-icon-opened">
                                        <?php if ($active_is_new || $active_migrated) :
                                            Icons_Manager::render_icon($settings['toggle_icon_active'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                                        else : ?>
                                            <i class="<?php echo esc_attr($settings['icon_active']); ?>" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </span>

                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </a>
                </div>
            </div>
        </div>

<?php
    }
}
