<?php

namespace ElementPack\Modules\HoverVideo\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Utils;

use ElementPack\Modules\HoverVideo\Skins;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit();
}

class Hover_Video extends Module_Base {

    public function get_name() {
        return 'bdt-hover-video';
    }

    public function get_title() {
        return BDTEP . esc_html__('Hover Video', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-hover-video';
    }

    public function get_categories() {
        return [
            'element-pack',
        ];
    }

    public function get_keywords() {
        return ['hover', 'insta', 'video', 'player'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-hover-video'];
        }
    }
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-hover-video'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/RgoWlIm5KOo';
    }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Accordion($this));
        $this->add_skin(new Skins\Skin_Vertical($this));
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_layouts',
            [
                'label' => esc_html__('Hover Video', 'element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'source_type',
            [
                'label'       => esc_html__('Video Type', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'hosted_url',
                'label_block' => true,
                'options'     => [
                    'remote_url' => esc_html__('Remote Video', 'bdthemes-element-pack'),
                    'hosted_url' => esc_html__('Local Video', 'bdthemes-element-pack'),
                ],
            ]
        );

        $repeater->add_control(
            'remote_url',
            [
                'type'          => Controls_Manager::URL,
                'label'         => __('Video Source', 'bdthemes-element-pack'),
                'label_block'   => true,
                'show_external' => false,
                'placeholder'   => __('https://exmaple.com/sample.mp4', 'bdthemes-element-pack'),
                'dynamic'       => ['active' => true],
                'condition'     => [
                    'source_type' => 'remote_url',
                ],
            ]
        );

        $repeater->add_control(
            'hosted_url',
            [
                'label'       => __('Select Video', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active'     => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::MEDIA_CATEGORY,
                    ],
                ],
                'media_type' => 'video',
                'condition' => [
                    'source_type' => 'hosted_url'
                ],
            ]
        );

        $repeater->add_control(
            'hover_video_poster',
            [
                'label'       => __('Select Poster', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::MEDIA,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block' => true,

            ]
        );

        $repeater->add_control(
            'hover_video_title',
            [
                'label'       => __('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __('Title Item', 'bdthemes-element-pack'),
                'default'     => __('Title Item', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'hover_item_icon_type',
            [
                'label'        => esc_html__('Icon Type', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::CHOOSE,
                'toggle'       => false,
                'default'      => 'icon',
                'prefix_class' => 'bdt-icon-type-',
                'render_type'  => 'template',
                'options'      => [
                    'icon' => [
                        'title' => esc_html__('Icon', 'bdthemes-element-pack'),
                        'icon'  => 'fas fa-star'
                    ],
                    'image' => [
                        'title' => esc_html__('Image', 'bdthemes-element-pack'),
                        'icon'  => 'far fa-image'
                    ]
                ]
            ]
        );

        $repeater->add_control(
            'hover_item_icon',
            [
                'label'            => __('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
                'condition'        => [
                    'hover_item_icon_type' => 'icon',
                ]
            ]
        );

        $repeater->add_control(
            'hover_selected_image',
            [
                'label'       => __('Image Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'default'     => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'hover_item_icon_type' => 'image'
                ]
            ]
        );

        $repeater->add_control(
            'video_wrapper_link',
            [
                'label'       => __('Wrapper Link', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block' => true,
                'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_video_list',
            [
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'hover_video_title' => __('Hover Video 01', 'bdthemes-element-pack'),
                        'hover_item_icon'   => ['value' => 'far fa-laugh', 'library' => 'fa-regular'],
                    ],
                    [
                        'hover_video_title' => __('Hover Video 02', 'bdthemes-element-pack'),
                        'hover_item_icon'   => ['value' => 'far fa-laugh', 'library' => 'fa-regular'],
                    ],
                ],
                'title_field' => '{{{ hover_video_title }}}',
            ]
        );

        $this->add_responsive_control(
            'hover_video_height',
            [
                'label'     => __('Video Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-wrapper-list' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'aspect_ratio',
            [
                'label'       => esc_html__('Aspect Ratio', 'bdthemes-element-pack') . BDTEP_NC,
                'type'        => Controls_Manager::SELECT,
                'default'     => '',
                'options'     => [
                    ''    => esc_html__('Select Aspect Ratio', 'bdthemes-element-pack'),
                    '11'  => '1:1',
                    '21'  => '2:1',
                    '32'  => '3:2',
                    '43'  => '4:3',
                    '85'  => '8:5',
                    '169' => '16:9',
                    '219' => '21:9',
                    '916' => '9:16',
                ],
                'prefix_class' => 'bdt-hv-ratio-yes bdt-hv-ratio-',
                'render_type' => 'template',
                'description' => esc_html__('Note: We recommend you choose only one from "Video Height" or "Aspect Ratio". Please don\'t use both.', 'bdthemes-element-pack')
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_additional',
            [
                'label' => esc_html__('Additional', 'element-pack'),
            ]
        );

        $this->add_control(
            'progress_visibility',
            [
                'label' => __('Show Progress Bar', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'icon_visibility',
            [
                'label' => __('Show Icon', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'video_preload',
            [
                'label'   => __('Video Preload', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'video_autoplay',
            [
                'label'   => __('Autoplay', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'video_replay',
            [
                'label'   => __('Video Replay', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'description' => __('Note: If you will activate this option, the video will play from the beginning.', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'poster_show_again',
            [
                'label'   => __('Show Poster Again', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'description' => __('Note: If you will activate this option, the video will play from the beginning and also visible your poster again.', 'bdthemes-element-pack')
            ]
        );

        $this->add_control(
            'btn_progress_visibility',
            [
                'label'   => __('Progress On Button', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => ['']
                ]
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'hover_video',
            [
                'label' => __('Video', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'hover_video_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-hover-video .bdt-hover-wrapper-list video',
            ]
        );

        $this->add_responsive_control(
            'hover_video_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-wrapper-list video, {{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'hover_video_shadow',
                'selector' => '{{WRAPPER}} .bdt-hover-video .bdt-hover-wrapper-list video',
                'condition' => [
                    '_skin' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'      => 'hover_video_css_filters_active',
                'selector'  => '{{WRAPPER}} .bdt-hover-video .bdt-hover-wrapper-list video',
            ]
        );

        $this->add_control(
            'hr',
            [
                'type'      => Controls_Manager::DIVIDER,
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'hover_video_item_heading',
            [
                'label'     => __('Hover Item', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->start_controls_tabs('mask_content_tabs');

        $this->start_controls_tab(
            'mask_content_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'mask_content_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask, {{WRAPPER}} .bdt-hover-video.skin-vertical  .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion  .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text, {{WRAPPER}} .bdt-hover-video.skin-vertical  .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'mask_content_bg',
                'selector'  => '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group, {{WRAPPER}}  .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group',
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'accrordion_text_shadow',
                'label' => __('Text Shadow', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text',
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_responsive_control(
            'mask_content_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'mask_content_text',
                'selector' => '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-text',
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'mask_content_align',
            [
                'label'       => __('Alignment', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'toggle'      => false,
                'default'     => 'left',
                'options'     => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    '_skin!' => '',
                ],
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'mask_alignment',
            [
                'label' => __('Vertical Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'top',
                'options' => [
                    'top' => [
                        'title' => __('Top', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __('Middle', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __('Bottom', 'bdthemes-element-pack'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'prefix_class' => 'bdt-hover-video-position-',
                'toggle' => false,
                'condition' => [
                    '_skin!' => '',
                ],
            ]
        );

        $this->add_control(
            'accr_mask_opacity',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-mask-text-group' => 'opacity: {{SIZE}}',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'mask_content_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'mask_content_color_active',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-text, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-text' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'mask_content_bg_active',
                'selector'  => '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-mask-text-group, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-mask-text-group',
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'mask_content_text_active',
                'selector' => '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-text, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-text',
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'accr_mask_opacity_active',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-mask-text-group, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask.active .bdt-hover-mask-text-group' => 'opacity: {{SIZE}}',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'mask_hover_svg_img_heading',
            [
                'label'     => __('Icon', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    '_skin!' => '',
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'mask_hover_svg_img_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 15,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask  i, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask  i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask img, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask  svg, {{WRAPPER}} .bdt-hover-video.skin-vertical .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask  svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => '',
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'mask_hover_svg_img_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 15,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-accordion .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-icon, {{WRAPPER}} .bdt-hover-video.skin-vertical  .bdt-hover-wrapper-list .bdt-hover-mask-list .bdt-hover-mask .bdt-hover-icon' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => '',
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'hover_progress_style',
            [
                'label'     => __('Progress Bar', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'progress_visibility',
                            'value' => 'yes',
                        ],
                        [
                            'name'  => 'btn_progress_visibility',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );


        $this->add_control(
            'hover_progress_bg',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-bar' => 'background-color: {{VALUE}}',

                ],
            ]
        );

        $this->add_control(
            'hover_progress_fill',
            [
                'label'     => __('Fill Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar .bdt-hover-progress' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-bar .bdt-hover-progress' => 'background-color: {{VALUE}}',

                ],
            ]
        );

        $this->add_responsive_control(
            'hover_progress_height',
            [
                'label'     => __('Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar .bdt-hover-progress'  => 'height: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-bar'  => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-bar .bdt-hover-progress'  => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hover_progress_width',
            [
                'label'     => __('Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->add_control(
            'hover_progress_border_radius',
            [
                'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list .bdt-hover-bar-wrapper .bdt-hover-bar .bdt-hover-progress'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-bar'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-bar .bdt-hover-progress'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hover_progress_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-bar-list' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => '',
                ]
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'hover_button_style',
            [
                'label' => __('Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => ['']
                ]
            ]
        );

        $this->add_responsive_control(
            'hover_button_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('hover_button_tabs');

        $this->start_controls_tab(
            'hover_button_normal',
            [
                'label' => __('Normal  ', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_button_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn, {{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-btn-text' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_button_bg',
                'selector'  => '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'hover_button_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn',
            ]
        );


        $this->add_responsive_control(
            'hover_button_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hover_button_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'hover_button_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'hover_button_shadow',
                'selector' => '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn'
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hover_button_text_size',
                'selector' => '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn .bdt-hover-btn-text',
            ]
        );

        $this->add_responsive_control(
            'hover_button_align',
            [
                'label'       => __('Alignment', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'toggle'      => false,
                'default'     => 'center',
                'options'     => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper' => 'text-align: {{VALUE}};',
                ],
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'mask_icon',
            [
                'label'     => __('Icon', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'hover_svg_img_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 15,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn img, {{WRAPPER}} .bdt-hover-video.skin-default .bdt-hover-btn-wrapper .bdt-hover-btn svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'hover_svg_img_spacing',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn .bdt-hover-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'icon_visibility' => 'yes'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'hover_button_active',
            [
                'label' => __('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_button_color_active',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn.active, {{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn.active .bdt-hover-btn-text' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn.active svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'hover_button_border_active',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn.active'  => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'hover_button_border_border!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_button_bg_active',
                'selector'  => '{{WRAPPER}} .bdt-hover-video .bdt-hover-btn-wrapper .bdt-hover-btn.active',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $id = 'default-' . $this->get_id();
        $id = $id . '-' . rand(10, 500);

        $this->add_render_attribute(
            [
                'hover_video_attr' => [
                    'id'         => $id,
                    'class'      => 'bdt-hover-video skin-default',
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            'id'         => $id,
                            'videoReplay' => (isset($settings['video_replay']) && $settings['video_replay'] == 'yes') ? 'yes' : 'no',
                            'posterAgain' => (isset($settings['poster_show_again']) && $settings['poster_show_again'] == 'yes') ? 'yes' : 'no'
                        ])),
                    ],
                ],
            ]
        );

        // $proVisibility = ($settings['progress_visibility']) == 'yes' ? 'yes' : 'no';

        $video_preload = ($settings['video_preload']) == 'yes' ? 'auto' : 'none';

        if ('yes' == $settings['video_autoplay']) {
            $this->add_render_attribute('hover_video_wrapper', 'class', 'bdt-hover-wrapper-list autoplay');
        } else {
            $this->add_render_attribute('hover_video_wrapper', 'class', 'bdt-hover-wrapper-list');
        }

?>

        <div <?php echo $this->get_render_attribute_string('hover_video_attr'); ?>>
            <span class="hover-video-loader"></span>
            <div <?php echo $this->get_render_attribute_string('hover_video_wrapper'); ?>>
                <?php
                $i = 0;
                foreach ($settings['hover_video_list'] as $index => $item) :
                    $i++;
                    $this->add_render_attribute('bdt_hover_video_attr', 'id', $id . '-' . $item['_id'], true);
                    $active_class = ($i == 1) ? 'active' : '';
                    $this->add_render_attribute('bdt_hover_video_attr', 'class', $active_class, true);
                    $this->add_render_attribute('bdt_hover_video_attr', 'preload', $video_preload, true);

                    $video_poster = ($item['hover_video_poster']['url']) ? $item['hover_video_poster']['url'] : BDTEP_ASSETS_URL . 'images/video-thumbnail.svg';

                    $video_source = '';

                    if ('hosted_url' == $item['source_type']) {
                        $video_source = $item['hosted_url']['url'];
                    } else {
                        $video_source = $item['remote_url']['url'];
                    }

                    if (!$video_source) {
                        $video_poster = BDTEP_ASSETS_URL . 'images/no-video.svg';
                    }

                ?>
                    <video <?php echo $this->get_render_attribute_string('bdt_hover_video_attr'); ?> oncontextmenu="return false;" src="<?php echo esc_url($video_source); ?>" poster="<?php echo esc_url($video_poster); ?>" muted>
                    </video>
                <?php endforeach; ?>

            </div>
            <?php if ($settings['progress_visibility'] == 'yes') { ?>
                <div class="bdt-hover-bar-list">
                    <?php
                    $i = 0;
                    foreach ($settings['hover_video_list'] as $index => $item) :
                        $i++;
                        // pro = progress
                        $this->add_render_attribute('bdt_hover_pro_attr', 'class', 'bdt-hover-progress', true);
                        $this->add_render_attribute('bdt_hover_pro_attr', 'data-id', $id . '-' . $item['_id'], true);
                        if ($i == 1) {
                            $this->add_render_attribute('bdt_hover_pro_attr', 'class', 'active');
                        }
                        // echo $i;

                    ?>
                        <div class="bdt-hover-bar-wrapper">
                            <div class="bdt-hover-bar">
                                <div <?php echo $this->get_render_attribute_string('bdt_hover_pro_attr'); ?>></div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php } ?>
            <div class="bdt-hover-btn-wrapper">
                <?php
                $i = 0;
                foreach ($settings['hover_video_list'] as $index => $item) :
                    $i++;
                    $this->add_render_attribute('bdt_hover_btn_attr', 'class', 'bdt-hover-btn', true);
                    $this->add_render_attribute('bdt_hover_btn_attr', 'data-id', $id . '-' . $item['_id'], true);

                    $this->add_render_attribute('button_pro_attr', 'class', 'bdt-hover-progress', true);
                    $this->add_render_attribute('button_pro_attr', 'data-id', $id . '-' . $item['_id'], true);

                    if ($i == 1) {
                        $this->add_render_attribute('bdt_hover_btn_attr', 'class', 'active');
                        $this->add_render_attribute('button_pro_attr', 'class', 'active');
                    }

                    if (!empty($item['video_wrapper_link']['url'])) {
                        $target = $item['video_wrapper_link']['is_external'] ? '_blank' : '_self';
                        $this->add_render_attribute('bdt_hover_btn_attr', 'onclick', "window.open('" . $item['video_wrapper_link']['url'] . "', '$target')", true);
                    }

                ?>
                    <?php if (($settings['icon_visibility'] == 'yes' or (!empty($item['hover_video_title']))) or $settings['btn_progress_visibility'] == 'yes') { ?>
                        <div <?php echo $this->get_render_attribute_string('bdt_hover_btn_attr'); ?>>
                            <?php if ($settings['icon_visibility'] == 'yes') { ?>
                                <div class="bdt-hover-icon-wrapper">
                                    <span class="bdt-hover-icon">
                                        <?php
                                        $has_icon  = !empty($item['hover_item_icon']);
                                        $has_image = !empty($item['hover_selected_image']['url']);

                                        if ($has_icon and 'icon' == $item['hover_item_icon_type']) {
                                            $this->add_render_attribute('font-icon', 'class', $item['hover_item_icon']);
                                            $this->add_render_attribute('font-icon', 'aria-hidden', 'true');
                                        } elseif ($has_image and 'image' == $item['hover_item_icon_type']) {
                                            $this->add_render_attribute('image-icon', 'src', $item['hover_selected_image']['url'], true);
                                            $this->add_render_attribute('image-icon', 'alt', $item['hover_video_title'], true);
                                        }

                                        if (!$has_icon && !empty($item['hover_item_icon']['value'])) {
                                            $has_icon = true;
                                        }

                                        ?>

                                        <?php
                                        if ($has_icon and 'icon' == $item['hover_item_icon_type']) {
                                            Icons_Manager::render_icon($item['hover_item_icon'], ['aria-hidden' => 'true']);
                                        } elseif ($has_image and 'image' == $item['hover_item_icon_type']) {
                                        ?>
                                            <img <?php echo $this->get_render_attribute_string('image-icon'); ?>>
                                        <?php } ?>
                                    </span>
                                </div>
                            <?php } ?>

                            <?php if (!empty($item['hover_video_title'])) : ?>
                                <div class="bdt-hover-btn-text">
                                    <?php echo $item['hover_video_title']; ?>
                                </div>
                            <?php endif ?>

                            <?php if ($settings['btn_progress_visibility'] == 'yes') { ?>
                                <div class="bdt-hover-bar">
                                    <div <?php echo $this->get_render_attribute_string('button_pro_attr'); ?>></div>
                                </div>
                            <?php } ?>

                        </div>
                    <?php } ?>


                <?php endforeach; ?>
            </div>
        </div>

<?php
    }
}
