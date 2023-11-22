<?php

namespace ElementPack\Modules\InteractiveCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use ElementPack\Utils;

use ElementPack\Traits\Global_Mask_Controls;
 
if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Interactive_Card extends Module_Base {

    use Global_Mask_Controls;

    public function get_name() {
        return 'bdt-interactive-card';
    }

    public function get_title() {
        return BDTEP . esc_html__('Interactive Card', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-interactive-card';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['advanced', 'interactive', 'image', 'services', 'card', 'box', 'features'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-interactive-card'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['gsap', 'wavify', 'ep-scripts'];
        } else {
            return ['gsap', 'wavify', 'ep-interactive-card'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/r8IXJUD3PA4';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_interactive-card_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label'       => __('Image', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'thumbnail_size',
                'default' => 'full',
            ]
        );

        $this->add_control(
            'image_mask_popover',
            [
                'label'        => esc_html__('Image Mask', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'template',
                'return_value' => 'yes',
            ]
        );

        //Global Image Mask Controls
        $this->register_image_mask_controls();

        $this->add_control(
            'title_text',
            [
                'label'       => __('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('Interactive Card Title', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_link',
            [
                'label'        => __('Title Link', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-title-link-'
            ]
        );


        $this->add_control(
            'title_link_url',
            [
                'label'       => __('Title Link URL', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => ['active' => true],
                'placeholder' => 'http://your-link.com',
                'condition'   => [
                    'title_link' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'show_sub_title',
            [
                'label'   => __('Show Sub Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'sub_title_text',
            [
                'label'       => __('Sub Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('This is a Label', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your sub title', 'bdthemes-element-pack'),
                'label_block' => true,
                'condition'   => [
                    'show_sub_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'description_text',
            [
                'label'       => __('Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::WYSIWYG,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
                'placeholder' => __('Enter your description', 'bdthemes-element-pack'),
                'rows'        => 10,
            ]
        );

        $this->add_control(
            'readmore',
            [
                'label'   => __('Read More Button', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'badge',
            [
                'label' => __('Badge', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'title_size',
            [
                'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => element_pack_title_tags(),
            ]
        );

        $this->add_control(
            'hr_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'    => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label'   => esc_html__('Position', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top'    => __('Top', 'bdthemes-element-pack'),
                    'bottom' => __('Bottom', 'bdthemes-element-pack'),
                    'left'   => __('Left', 'bdthemes-element-pack'),
                    'right'  => __('Right', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'hr_divider_1',
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'content_position' => ['top', 'bottom']
                ]
            ]
        );

        $this->add_control(
            'show_wavify_effect',
            [
                'label'   => __('Show Wavify Effect', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'condition' => [
                    'content_position' => ['top', 'bottom']
                ]
            ]
        );

        $this->add_control(
            'wavify_toggle',
            [
                'label'        => __('Wavify', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'show_wavify_effect' => 'yes',
                    'content_position' => ['top', 'bottom']
                ]
            ]
        );

        $this->start_popover();

        $this->add_control(
            'wave_bones',
            [
                'label'       => __('Bones', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'default'     => [
                    'size' => 3,
                ],
                'range'       => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'condition'   => [
                    'wavify_toggle' => 'yes'
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'wave_amplitude',
            [
                'label'       => __('Amplitude', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'default'     => [
                    'size' => 40,
                ],
                'range'       => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition'   => [
                    'wavify_toggle' => 'yes'
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'wave_speed',
            [
                'label'       => __('Speed', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'default'     => [
                    'size' => .25,
                ],
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 0.1,
                        'max'  => 1,
                    ],
                ],
                'condition'   => [
                    'wavify_toggle' => 'yes'
                ],
                'render_type' => 'template',
            ]
        );

        $this->end_popover();

        $this->add_control(
			'global_link',
			[
				'label'        => __( 'Global Link', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => __( 'Be aware! When Global Link activated then title link and read more link will not work', 'bdthemes-element-pack' ),
                'separator' => 'before'
			]
		);

		$this->add_control(
			'global_link_url',
			[
				'label'       => __( 'Global Link URL', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'condition'   => [
					'global_link' => 'yes'
				]
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_readmore',
            [
                'label'     => __('Read More', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label'       => __('Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'default'     => __('Read More', 'bdthemes-element-pack'),
                'placeholder' => __('Read More', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_link',
            [
                'label'       => __('Link to', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'separator'   => 'before',
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
                'default'     => [
                    'url' => '#',
                ],
                'condition'   => [
                    'readmore' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'onclick',
            [
                'label'     => esc_html__('OnClick', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'readmore' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'onclick_event',
            [
                'label'       => esc_html__('OnClick Event', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'myFunction()',
                'description' => sprintf(esc_html__('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp'),
                'condition'   => [
                    'readmore' => 'yes',
                    'onclick'  => 'yes'
                ]
            ]
        );

        $this->add_control(
			'button_css_id',
			[
				'label' => __( 'Button ID', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack' ),
				'separator' => 'before',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_badge',
            [
                'label'     => __('Badge', 'bdthemes-element-pack'),
                'condition' => [
                    'badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label'       => __('Badge Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'POPULAR',
                'placeholder' => 'Type Badge Title',
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'badge_position',
            [
                'label'   => esc_html__('Position', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'top-right',
                'options' => element_pack_position(),
            ]
        );

        $this->add_control(
            'badge_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[
				'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-badge-h-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[
				'label' => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-badge-v-offset: {{SIZE}}px;'
                ],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition' => [
                    'badge_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-badge-rotate: {{SIZE}}deg;'
                ],
			]
		);

		$this->end_popover();

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_feature',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_feature_image');

        $this->start_controls_tab(
            'tab_image_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'img_shadow',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-image'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'image_border',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-image'
            ]
        );

        $this->add_control(
            'iamge_radius',
            [
                'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-image, {{WRAPPER}} .bdt-interactive-card-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_iamge_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_iamge_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'image_hover_effect',
            [
                'label'   => __('Image Hover Effect', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_image_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'icon_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-image'
            ]
        );

        $this->add_control(
            'image_hover_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-image' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'image_border_border!' => '',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_radius',
            [
                'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_default_style');

        $this->start_controls_tab(
            'tab_default_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'default_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-content' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .bdt-wavify-effect svg *'     => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_default_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'default_hover_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-content' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-wavify-effect svg *'     => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_style_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_bottom_space',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-title',
            ]
        );

        $this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
                'label' => __('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-interactive-card-title',
			]
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-interactive-card-title',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_style_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow_hover',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-title',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_sub_title',
            [
                'label'     => __('Sub Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sub_title' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_sub_title_style');

        $this->start_controls_tab(
            'tab_sub_title_style_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'sub_title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sub_title_bottom_space',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-sub-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_sub_title_style_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'sub_title_color_hover',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'sub_title_typography_hover',
                'selector' => '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-sub-title',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_description',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_description_style');

        $this->start_controls_tab(
            'tab_description_style_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-text',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_description_style_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'description_color_hover',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card:hover .bdt-interactive-card-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_readmore',
            [
                'label'     => __('Read More', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_attention',
            [
                'label' => __('Attention', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'button_top_space',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hr_divider_3',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->start_controls_tabs('tabs_readmore_style');

        $this->start_controls_tab(
            'tab_readmore_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_text_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_icon_color',
            [
                'label'     => __('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore span.ep-icon-long-arrow-right' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'readmore_background',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-readmore:before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'readmore_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-interactive-card-readmore:before'
            ]
        );

        $this->add_responsive_control(
            'readmore_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'readmore_shadow',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-readmore:before',
            ]
        );

        $this->add_responsive_control(
            'readmore_button_size',
            [
                'label'      => __('Button Size', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore:before, {{WRAPPER}} .bdt-interactive-card-readmore span.ep-icon-long-arrow-right' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'readmore_padding_right',
            [
                'label'      => __('Padding (Right)', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore' => 'padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'readmore_icon_spacing',
            [
                'label'      => __('Icon Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore span.ep-icon-long-arrow-right' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'readmore_typography',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-readmore',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_readmore_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_hover_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_icon_color',
            [
                'label'     => __('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore:hover span.ep-icon-long-arrow-right' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'readmore_hover_background',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-readmore:hover:before',
            ]
        );

        $this->add_control(
            'readmore_hover_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-readmore:hover:before' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'readmore_border_border!' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'readmore_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-readmore:hover:before',
            ]
        );

        $this->add_control(
            'readmore_hover_animation',
            [
                'label' => __('Hover Animation', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_badge',
            [
                'label'     => __('Badge', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-interactive-card-badge span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'badge_background',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-badge span',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'badge_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-interactive-card-badge span'
            ]
        );

        $this->add_responsive_control(
            'badge_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'badge_shadow',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-badge span',
            ]
        );

        $this->add_responsive_control(
            'badge_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-interactive-card .bdt-interactive-card-badge.bdt-position-small' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'badge_typography',
                'selector' => '{{WRAPPER}} .bdt-interactive-card-badge span',
            ]
        );

        $this->end_controls_section();

    }

    public function render_interactive_card_badge() {
        $settings = $this->get_settings_for_display();

        ?>
        <?php if ( $settings['badge'] and '' != $settings['badge_text'] ) : ?>
            <div class="bdt-interactive-card-badge bdt-position-small bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
                <span class="bdt-badge bdt-padding-small"><?php echo esc_html($settings['badge_text']); ?></span>
            </div>
        <?php endif; ?>
        <?php
    }

    public function render_interactive_card_image() {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();

        if ( $settings['image_hover_effect'] == 'yes') {
            $this->add_render_attribute('image-effect', 'class', 'bdt-image-hover-effect');
        }
        $this->add_render_attribute('image-effect', 'class', 'bdt-interactive-card-image');

        $image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
		$this->add_render_attribute('image-wrap', 'class', 'bdt-position-relative' . $image_mask);

        ?>
        <div class="bdt-position-relative">
            <?php $this->render_interactive_card_badge(); ?>
            <div <?php echo $this->get_render_attribute_string('image-effect'); ?>>
                <div <?php echo $this->get_render_attribute_string('image-wrap'); ?>>
                    <?php 
                    $thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail_size', $settings);
                    if (!$thumb_url) {
                        printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['title_text']));
                    } else {
                        print(wp_get_attachment_image(
                            $settings['image']['id'],
                            $settings['thumbnail_size_size'],
                            false,
                            [
                                'alt' => esc_html($settings['title_text'])
                            ]
                        ));
                    }
                    ?>
                </div>
            </div>
            <?php if ( 'yes' == $settings['show_wavify_effect'] ) : ?>
                <div class="bdt-wavify-effect">
                    <svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <defs></defs>
                        <path id="wave-<?php echo esc_attr($id) ?>" d=""/>
                    </svg>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_interactive_card_content() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('interactive-card-title', 'class', 'bdt-interactive-card-title');
        if ( 'yes' == $settings['title_link'] and $settings['title_link_url']['url'] ) {

            $target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

            $this->add_render_attribute('interactive-card-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
        }

        $this->add_render_attribute('interactive-card-sub-title', 'class', 'bdt-interactive-card-sub-title');

        $this->add_render_attribute('description_text', 'class', 'bdt-interactive-card-text');

        $this->add_inline_editing_attributes('title_text', 'none');
        $this->add_inline_editing_attributes('description_text');


        $this->add_render_attribute('readmore', 'class', ['bdt-interactive-card-readmore', 'bdt-display-inline-block']);

        if ( !empty($settings['readmore_link']['url']) ) {
            $this->add_render_attribute('readmore', 'href', $settings['readmore_link']['url']);

            if ( $settings['readmore_link']['is_external'] ) {
                $this->add_render_attribute('readmore', 'target', '_blank');
            }

            if ( $settings['readmore_link']['nofollow'] ) {
                $this->add_render_attribute('readmore', 'rel', 'nofollow');
            }

        }

        if ( $settings['readmore_attention'] ) {
            $this->add_render_attribute('readmore', 'class', 'bdt-ep-attention-button');
        }

        if ( $settings['readmore_hover_animation'] ) {
            $this->add_render_attribute('readmore', 'class', 'elementor-animation-' . $settings['readmore_hover_animation']);
        }

        if ( $settings['onclick'] ) {
            $this->add_render_attribute('readmore', 'onclick', $settings['onclick_event']);
        }

        if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'readmore', 'id', $settings['button_css_id'] );
		}

        ?>
        <?php if ( 'yes' == $settings['show_sub_title'] ) : ?>
            <div <?php echo $this->get_render_attribute_string('interactive-card-sub-title'); ?>>
                <?php echo wp_kses($settings['sub_title_text'], element_pack_allow_tags('title')); ?>
            </div>
        <?php endif; ?>

        <?php if ( $settings['title_text'] ) : ?>
            <<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> <?php echo $this->get_render_attribute_string('interactive-card-title'); ?>>
            <span <?php echo $this->get_render_attribute_string('title_text'); ?>>
                <?php echo wp_kses($settings['title_text'], element_pack_allow_tags('title')); ?>
            </span>
            </<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
        <?php endif; ?>

        <?php if ( $settings['description_text'] ) : ?>
            <div <?php echo $this->get_render_attribute_string('description_text'); ?>>
                <?php echo $settings['description_text']; ?>
            </div>
        <?php endif; ?>

        <?php if ( $settings['readmore'] ) : ?>
            <div class="bdt-interactive-card-button">
                <a <?php echo $this->get_render_attribute_string('readmore'); ?>>
                    <span class="ep-icon-long-arrow-right"></span>
                    <span class="bdt-position-relative">
						<?php echo esc_html($settings['readmore_text']); ?>
					</span>
                </a>
            </div>
        <?php endif ?>
        <?php
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('interactive-card', 'class', ['bdt-interactive-card', 'bdt-interactive-card-default']);

        if ( 'top' == $settings['content_position'] ) {
            $this->add_render_attribute('interactive-card-wrapper', 'class', ['bdt-grid bdt-grid-collapse bdt-card-effect-top']);
            $this->add_render_attribute('interactive-card-width', 'class', ['bdt-width-1-1']);
        } elseif ( 'bottom' == $settings['content_position'] ) {
            $this->add_render_attribute('interactive-card-wrapper', 'class', ['bdt-grid bdt-grid-collapse bdt-flex-column bdt-flex-column-reverse bdt-card-effect-bottom']);
            $this->add_render_attribute('interactive-card-width', 'class', ['bdt-width-1-1']);
        } elseif ( 'left' == $settings['content_position'] ) {
            $this->add_render_attribute('interactive-card-wrapper', 'class', ['bdt-grid bdt-grid-collapse bdt-flex bdt-flex-middle bdt-card-effect-left']);
            $this->add_render_attribute('interactive-card-width', 'class', ['bdt-width-1-1 bdt-width-1-2@s']);
        } elseif ( 'right' == $settings['content_position'] ) {
            $this->add_render_attribute('interactive-card-wrapper', 'class', ['bdt-grid bdt-grid-collapse bdt-flex bdt-flex-middle bdt-flex-row bdt-flex-row-reverse bdt-card-effect-right']);
            $this->add_render_attribute('interactive-card-width', 'class', ['bdt-width-1-1 bdt-width-1-2@s']);
        }

        if ( 'yes' == $settings['show_wavify_effect'] ) {
            $this->add_render_attribute(
                [
                    'interactive-card' => [
                        'id'            => 'interactive-card-' . $this->get_id(),
                        'data-settings' => [
                            wp_json_encode(array_filter([
                                    'id'             => 'wave-' . $this->get_id(),
                                    'wave_bones'     => ("yes" == $settings["wavify_toggle"]) ? $settings["wave_bones"]["size"] : 3,
                                    'wave_amplitude' => ("yes" == $settings["wavify_toggle"]) ? $settings["wave_amplitude"]["size"] : 40,
                                    'wave_speed'     => ("yes" == $settings["wavify_toggle"]) ? $settings["wave_speed"]["size"] : 0.25,
                                ])
                            ),
                        ],
                    ],
                ]
            );
        }

        if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute( 'interactive-card', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')" );
		}

        ?>
        <div <?php echo $this->get_render_attribute_string('interactive-card'); ?>>

            <div <?php echo $this->get_render_attribute_string('interactive-card-wrapper'); ?>>

                <div <?php echo $this->get_render_attribute_string('interactive-card-width'); ?>>
                    <?php $this->render_interactive_card_image(); ?>
                </div>

                <div <?php echo $this->get_render_attribute_string('interactive-card-width'); ?>>
                    <div class="bdt-interactive-card-content">
                        <?php $this->render_interactive_card_content(); ?>
                    </div>
                </div>

            </div>

        </div>

        <?php
    }
}
