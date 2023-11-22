<?php

namespace ElementPack\Modules\Honeycombs\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use ElementPack\Element_Pack_Loader;
use ElementPack\Utils;

if (!defined('ABSPATH')) {
    exit();
}

class Honeycombs extends Module_Base {

    public function get_name() {
        return 'bdt-honeycombs';
    }

    public function get_title() {
        return BDTEP . esc_html__('Honeycombs', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-honeycombs';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-honeycombs'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['honeycombs', 'ep-scripts'];
        } else {
            return ['honeycombs', 'ep-honeycombs'];
        }
    }

    public function get_keywords() {
        return ['hexagon', 'box', 'honeycomb'];
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/iTWXzc329vQ';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_honeycombs_item',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'honeycomb_style',
            [
                'label'   => esc_html__('HoneyComb Style', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'bdthemes-element-pack'),
                    'radius'  => esc_html__('Radius', 'bdthemes-element-pack'),
                    'radius2' => esc_html__('Large Radius', 'bdthemes-element-pack'),
                    'zigzag'  => esc_html__('Zigzag', 'bdthemes-element-pack'),
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_invisible',
            [
                'label'        => esc_html__('Item Invisible', 'bdthemes-element-pack') . BDTEP_NC,
                'type'         => Controls_Manager::SWITCHER,
            ]
        );

        $repeater->start_controls_tabs('tabs_content', [
            'condition' => [
                'item_invisible' => ''
            ]
        ]);

        $repeater->start_controls_tab(
            'tab_content_front',
            [
                'label' => esc_html__('Front', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'honeycombs_item_icon',
            [
                'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'default'     => [
                    'value'   => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
                // 'condition'    => [
                //  'icon_display' => 'yes',
                // ],
            ]
        );

        $repeater->add_control(
            'honeycombs_title',
            [
                'label'       => esc_html__('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => esc_html__('Title Item', 'bdthemes-element-pack'),
                'default'     => esc_html__('Title Item', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'title_color_item',
            [
                'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb{{CURRENT_ITEM}} .bdt-inner .bdt-wrapper .bdt-title' => 'color: {{VALUE}}',
                ],
            ]
        );


        $repeater->add_control(
            'background_item',
            [
                'label'       => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::COLOR,
                'selectors'   => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs  .bdt-honeycombs-inner-wrapper .bdt-comb{{CURRENT_ITEM}} .bdt-icon-hex-lg' => 'background-color: {{VALUE}}',
                ],
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'honeycombs_bg_img',
            [
                'label'       => esc_html__('Background Image', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::MEDIA,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block' => true,
                'selectors'   => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-comb{{CURRENT_ITEM}} .bdt-icon-hex-lg' => 'background: url({{URL}}) no-repeat center center;
                        background-size: cover; background-clip: text; -webkit-background-clip: text;  color: transparent;
                        background-position: center;
                        filter: none;',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_content_back',
            [
                'label' => esc_html__('Back', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'honeycombs_content',
            [
                'label'       => esc_html__('Content', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::WYSIWYG,
                'default'     => esc_html__("Default description. Lorem Ipsum is simply dummy text of the printing and typesetting industry.   ", 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Type your description here', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'title_color_item_back',
            [
                'label'     => esc_html__('Content Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb{{CURRENT_ITEM}} .bdt-inner .bdt-wrapper .bdt-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'background_item_back',
            [
                'label'       => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::COLOR,
                'selectors'   => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb{{CURRENT_ITEM}}:hover .bdt-icon-hex-lg' => 'background-color: {{VALUE}} !important',
                ],
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'honeycombs_bg_img_back',
            [
                'label'       => esc_html__('Background Image', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::MEDIA,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block' => true,
                'selectors'   => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-comb{{CURRENT_ITEM}}:hover .bdt-icon-hex-lg' => 'background: url({{URL}}) no-repeat center center;
                        background-size: cover; background-clip: text; -webkit-background-clip: text;  color: transparent;
                        background-position: center;
                        filter: none;',
                ],
            ]
        );


        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();

        $repeater->add_control(
            'honeycombs_link',
            [
                'label'       => esc_html__('Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block' => true,
                'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
                'condition' => [
                    'item_invisible' => ''
                ]
            ]
        );

        $this->add_control(
            'honeycombs_list',
            [
                'label'       => esc_html__('Items', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'separator'   => 'before',
                'default'     => [
                    [
                        'honeycombs_title'     => esc_html__('Comb 1', 'bdthemes-element-pack'),
                        'honeycombs_content'   => esc_html__('@1 Click edit button to change this text. Lorem agaca ipsum.', 'bdthemes-element-pack'),
                        'honeycombs_item_icon' => [
                            'value'   => 'far fa-moon',
                            'library' => 'fa-regular'
                        ],
                    ],
                    [
                        'honeycombs_title'     => esc_html__('Comb 2', 'bdthemes-element-pack'),
                        'honeycombs_content'   => esc_html__('@2 Click edit button to change this text. Lorem agaca ipsum. ', 'bdthemes-element-pack'),
                        'honeycombs_item_icon' => [
                            'value'   => 'far fa-smile',
                            'library' => 'fa-regular'
                        ],
                    ],
                    [
                        'honeycombs_title'     => esc_html__('Comb 3', 'bdthemes-element-pack'),
                        'honeycombs_content'   => esc_html__('@3 Click edit button to change this text. Lorem agaca ipsum. ', 'bdthemes-element-pack'),
                        'honeycombs_item_icon' => [
                            'value'   => 'far fa-heart',
                            'library' => 'fa-regular'
                        ],
                    ],

                ],
                'title_field' => '{{{ honeycombs_title }}}',
            ]
        );

        $this->add_control(
            'icon_display',
            [
                'label'        => esc_html__('Show Icon', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
                'separator'    => 'before'
            ]
        );

        $this->add_control(
            'title_display',
            [
                'label'        => esc_html__('Show Title', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'description_display',
            [
                'label'        => esc_html__('Show Description', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_honeycombs_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'items_width',
            [
                'label'      => esc_html__('Width', 'bdthemes-element-pack'),
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
                    'size' => 250,
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'items_spacing',
            [
                'label'      => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range'      => [
                    'px' => [
                        'min'  => -50,
                        'max'  => 100,
                        'step' => 5,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => -20,
                ],
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label'     => esc_html__('Title Tag', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h3',
                'options'   => element_pack_heading_size(),
                'condition' => [
                    'title_display' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'comb_animation_type',
            [
                'label'     => esc_html__('Comb Animation', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => element_pack_transition_options(),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'combs_anim_delay',
            [
                'label'      => esc_html__('Animation delay', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range'      => [
                    'ms' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                ],
                'default'    => [
                    'unit' => 'ms',
                    'size' => 300,
                ],
                'condition'  => [
                    'comb_animation_type!' => '',
                ],
            ]
        );


        $this->end_controls_section();

        //Style
        // content items
        $this->start_controls_section(
            'section_style_items',
            [
                'label' => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_items_style');

        $this->start_controls_tab(
            'tab_items_front',
            [
                'label' => esc_html__('Front', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'items_background',
                'selector'    => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-icon-hex-lg',
                'render_type' => 'template'
            ]
        );


        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'items_css_filters',
                'selector' => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-icon-hex-lg',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_items_back',
            [
                'label' => esc_html__('Back', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'        => 'items_background_back',
                'selector'    => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb:hover .bdt-comb-inner-wrapper .bdt-icon-hex-lg',
                'render_type' => 'template'
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'items_css_filters_back',
                'selector' => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb:hover .bdt-icon-hex-lg',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // end content items

        // icon
        $this->start_controls_section(
            'section_style_icon',
            [
                'label'     => esc_html__('Icon', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'icon_display' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => esc_html__('Size', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 10,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon'     => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon svg' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon'     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-honeycombs-icon svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        // icon

        // title
        $this->start_controls_section(
            'section_style_title',
            [
                'label'     => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'title_display' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-title',
            ]
        );


        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        // end title


        // description
        $this->start_controls_section(
            'section_style_description',
            [
                'label'     => esc_html__('Description', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'description_display' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'description_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-content',
            ]
        );


        $this->add_responsive_control(
            'description_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-honeycombs-area .bdt-honeycombs .bdt-honeycombs-inner-wrapper .bdt-comb .bdt-inner .bdt-wrapper .bdt-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        // end description


    }

    protected function render() {

        $settings = $this->get_settings_for_display();


        $frontSideDisplay = '';
        if ($settings['title_display'] == 'yes' || $settings['icon_display'] == 'yes') {
            $frontSideDisplay = 'yes';
        }

        $titleTag                   = $settings['title_tag'];
        $honeycomb_style            = 'honeycomb-style-' . $settings['honeycomb_style'];
        $honeycomb_des_visibility   = ($settings['description_display'] == 'yes') ? ' ' : 'honeycomb-des-visibility-hide';
        $honeycomb_title_visibility = ($frontSideDisplay == 'yes') ? ' ' : 'honeycomb-title-visibility-hide';
        $elementor_vp_lg            = get_option('elementor_viewport_lg');
        $elementor_vp_md            = get_option('elementor_viewport_md');
        $viewport_lg                = !empty($elementor_vp_lg) ? $elementor_vp_lg - 1 : 1023;
        $viewport_md                = !empty($elementor_vp_md) ? $elementor_vp_md - 1 : 767;

        $items_width = isset($settings['items_width']['size']) ? $settings['items_width']['size'] : 250;
        $items_width_tablet = isset($settings['items_width_tablet']['size']) ? $settings['items_width_tablet']['size'] : 250;
        $items_width_mobile = isset($settings['items_width_mobile']['size']) ? $settings['items_width_mobile']['size'] : 250;

        $this->add_render_attribute(
            [
                'honeycombs' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            "id"           => $this->get_id(),
                            "width"        => $items_width,
                            "width_tablet" => $items_width_tablet,
                            "width_mobile" => $items_width_mobile,
                            "viewport_lg"  => $viewport_lg,
                            "viewport_md"  => $viewport_md,
                            "margin"       => $settings['items_spacing']['size'],
                        ])),
                    ],
                ],
            ]
        );

        $this->add_render_attribute('honeycombs', 'class', 'bdt-honeycombs');
        $this->add_render_attribute('honeycombs', 'class', $honeycomb_style);
        $this->add_render_attribute('honeycombs', 'class', $honeycomb_des_visibility . ' ' . $honeycomb_title_visibility);


?>


        <div class="bdt-honeycombs-area" <?php if ($settings['comb_animation_type'] !== '') { ?> bdt-grid bdt-scrollspy="cls: bdt-animation-<?php echo esc_attr($settings['comb_animation_type']); ?>; target: .bdt-comb-inner-wrapper; delay: <?php echo $settings['combs_anim_delay']['size']; ?>;" <?php } ?>>

            <div <?php echo $this->get_render_attribute_string('honeycombs'); ?>>

                <?php foreach ($settings['honeycombs_list'] as $index => $item) : ?>

                    <?php

                    if (!Element_Pack_Loader::elementor()->editor->is_edit_mode()) {

                        if (!empty($item['honeycombs_link']['url'])) {

                            $this->add_render_attribute('bdt-comb-link', 'href', $item['honeycombs_link']['url'], true);

                            if ($item['honeycombs_link']['is_external']) {
                                $this->add_render_attribute('bdt-comb-link', 'target', '_blank', true);
                            } else {
                                $this->add_render_attribute('bdt-comb-link', 'target', '_self', true);
                            }

                            if ($item['honeycombs_link']['nofollow']) {
                                $this->add_render_attribute('bdt-comb-link', 'rel', 'nofollow', true);
                            }
                        } else {
                            $this->add_render_attribute('bdt-comb-link', 'href', 'javascript:void(0);', true);
                            $this->add_render_attribute('bdt-comb-link', 'target', '_self', true);
                        }
                    } else {
                        $this->add_render_attribute('bdt-comb-link', 'href', 'javascript:void(0);', true);
                    }
                    $this->add_render_attribute('bdt-comb-link', 'class', 'bdt-comb elementor-repeater-item-' . esc_attr($item['_id']), true);

                    if ($item['item_invisible']) {
                        $this->add_render_attribute('bdt-comb-link', 'class', 'bdt-invisible bdt-comb placeholder hide elementor-repeater-item-' . esc_attr($item['_id']), true);
                    }


                    ?>
                    <a <?php echo $this->get_render_attribute_string('bdt-comb-link'); ?>>
                        <div class="bdt-front-content">
                            <?php if ($settings['icon_display'] == 'yes') : ?>
                                <?php if (!empty($item['honeycombs_item_icon']['value'])) : ?>
                                    <div class="bdt-honeycombs-icon">
                                        <?php Icons_Manager::render_icon($item['honeycombs_item_icon'], ['aria-hidden' => 'true']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($settings['title_display'] == 'yes') : ?>
                                <<?php echo Utils::get_valid_html_tag($titleTag); ?> class="bdt-title">
                                    <?php echo wp_kses_post($item['honeycombs_title']); ?>
                                </<?php echo Utils::get_valid_html_tag($titleTag); ?>>
                            <?php endif; ?>
                        </div>
                        <div class="bdt-back-content">
                            <?php if ($settings['description_display'] == 'yes') : ?>
                                <div class="bdt-content">
                                    <?php echo wp_kses_post($item['honeycombs_content']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>

                <?php endforeach; ?>

            </div>


        </div>


<?php
    }
}
