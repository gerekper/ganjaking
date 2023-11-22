<?php

namespace ElementPack\Modules\InteractiveTabs\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Plugin;
use ElementPack\Utils;

use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Interactive_Tabs extends Module_Base {

	use Global_Swiper_Controls;

	public function get_name() {
		return 'bdt-interactive-tabs';
	}

	public function get_title() {
		return BDTEP . esc_html__('Interactive Tabs', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-interactive-tabs';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['interactive', 'tabs', 'toggle', 'accordion'];
	}

	public function is_reload_preview_required() {
		return false;
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-interactive-tabs'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return ['ep-interactive-tabs'];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/O3VFyW0G6_Q';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_tabs_item',
			[
				'label' => __('Tabs Item', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('tabs_item_style');

		$repeater->start_controls_tab(
			'tabs_item_normal',
			[
				'label' => __('Tab', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label'            => __('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$repeater->add_control(
			'tab_title',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('Tab Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_sub_title',
			[
				'label'       => __('Sub Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tabs_item_content',
			[
				'label' => __('Content', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'source',
			[
				'label'   => esc_html__('Select Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background',
				'options' => [
					'background' => esc_html__('Background', 'bdthemes-element-pack'),
					"elementor"  => esc_html__('Elementor Template', 'bdthemes-element-pack'),
				],
			]
		);
		$repeater->add_control(
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

		$repeater->add_control(
			'background',
			[
				'label'   => esc_html__('Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'image',
				'toggle'  => false,
				'options' => [
					'image' => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'far fa-image',
					],
					'video' => [
						'title' => esc_html__('Video', 'bdthemes-element-pack'),
						'icon'  => 'fas fa-play-circle',
					],
					'youtube' => [
						'title' => esc_html__('Youtube', 'bdthemes-element-pack'),
						'icon'  => 'fab fa-youtube',
					],
				],
				'condition' => ['source' => "background"],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'     => esc_html__('Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::MEDIA,
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-' . rand(1, 4) . '.svg',
				],
				'condition' => [
					'background' => 'image',
					'source' => "background"
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link',
			[
				'label'       => esc_html__('Link', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'condition' => [
					'background' => 'image',
					'source' => "background"
				],
			]
		);

		$repeater->add_control(
			'video_link',
			[
				'label'     => esc_html__('Video Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'video',
					'source' => "background"
				],
				'default' => '//clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'youtube_link',
			[
				'label'     => esc_html__('Youtube Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'youtube',
					'source' => "background"
				],
				'default' => 'https://youtu.be/YE7VzlLtp-4',
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'tabs',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_sub_title'   => __('This is a subtitle', 'bdthemes-element-pack'),
						'tab_title'   	  => __('Interactive title one', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-smile', 'library' => 'fa-solid'],
						'image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg']
					],
					[
						'tab_sub_title'   => __('This is a subtitle', 'bdthemes-element-pack'),
						'tab_title'   	  => __('Interactive title two', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
						'image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg']
					],
					[
						'tab_sub_title'   => __('This is a subtitle', 'bdthemes-element-pack'),
						'tab_title'   	  => __('Interactive title three', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-dice-d6', 'library' => 'fa-solid'],
						'image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg']
					],
					[
						'tab_sub_title'   => __('This is a subtitle', 'bdthemes-element-pack'),
						'tab_title'   	  => __('Interactive title four', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-ring', 'library' => 'fa-solid'],
						'image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg']
					],
				],

				'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) }}} {{{ tab_title }}}',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'default'      => 'full',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_interactive_tabs',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'   => esc_html__('Show Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'tabs_icon_top',
			[
				'label'   => esc_html__('Icon Position Top', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-icon-top--',
				'condition' => [
					'show_icon' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'   => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'single_column',
			[
				'label'        => esc_html__('Single Column', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-single-column--',
				'render_type'  => 'template'
			]
		);

		$this->add_control(
			'column_reverse_on_desktop',
			[
				'label'   => esc_html__('Column Reverse on Desktop', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-column-reverse-on-desktop--',
				'condition' => [
					'single_column' => 'yes'
				],
			]
		);

		$this->add_control(
			'column_reverse',
			[
				'label'   => esc_html__('Column Reverse on Mobile', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-column-reverse--',
			]
		);

		$this->add_control(
			'row_reverse',
			[
				'label'   => esc_html__('Row Reverse', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-row-reverse--',
				'condition' => [
					'single_column' => ''
				],
			]
		);

		$this->add_control(
			'space_between',
			[
				'label'     => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--ep-space-between: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'tabs_heading',
			[
				'label'   => esc_html__('TABS ITEM', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default'        => '2',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-wrap' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-wrap' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_width',
			[
				'label' => __('Width(%)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 50,
				],
				'tablet_default' => [
					'size' => 50,
				],
				'mobile_default' => [
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--ep-tabs-width: {{SIZE}}%;'
				],
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'tabs_position',
			[
				'label'   => __('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'start'   => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'end'  => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
					'auto'  => [
						'title' => __('Stretch', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-stretch',
					],
				],
				'render_type' => 'template',
				'toggle' => false,
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-wrap' => 'align-self: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_text_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
					'justify'  => [
						'title' => __('Justify', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'render_type' => 'template',
				'toggle' => false,
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_control(
            'thumbs_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'thumbs_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
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
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'thumbs_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-interactive-tabs-thumbs-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
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
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'thumbs_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-interactive-tabs-thumbs-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbs_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
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
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'condition' => [
                    'thumbs_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-interactive-tabs-thumbs-rotate: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->update_control(
			'arrows_position',
			[
				'default' => 'bottom-left'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => __('Slider Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'transition',
			[
				'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'slide'     => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'      => esc_html__('Fade', 'bdthemes-element-pack'),
					'cube'      => esc_html__('Cube', 'bdthemes-element-pack'),
					'coverflow' => esc_html__('Coverflow', 'bdthemes-element-pack'),
					'flip'      => esc_html__('Flip', 'bdthemes-element-pack'),
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __('Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_interactive_tabs_style',
			[
				'label' => __('Tabs Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tabs_item_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'glassmorphism_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

			]
		);

		$this->add_control(
			'glassmorphism_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'glassmorphism_effect' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item',
			]
		);

		$this->add_responsive_control(
			'tabs_item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'tabs_item_border',
				'label'          => __('Border', 'bdthemes-element-pack'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#f2f2f2',
					],
				],
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item',
			]
		);

		$this->add_responsive_control(
			'tabs_item_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'tabs_item_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'tabs_item_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tabs_item_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/interactive-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'tabs_item_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_item_hover',
			[
				'label' => __('hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover',
			]
		);

		$this->add_control(
			'tabs_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default' => '#4AB8F8',
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_item_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_active_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active',
			]
		);

		$this->add_control(
			'tabs_item_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default' => '#4AB8F8',
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tabs_content',
			[
				'label'      => __('Tabs Content', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				// 'condition' => [
				// 	'source' => "background"
				// ]
			]
		);

		$this->start_controls_tabs('tabs_content_iamge');

		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .swiper-slide .bdt-interactive-tabs-main-img',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .swiper-slide .bdt-interactive-tabs-main-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .swiper-slide .bdt-interactive-tabs-main-img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_content_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .swiper-slide, {{WRAPPER}} .bdt-interactive-tabs .swiper-slide .bdt-interactive-tabs-main-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'video_height',
			[
				'label' => __('Video Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs embed, {{WRAPPER}} .bdt-interactive-tabs iframe, {{WRAPPER}} .bdt-interactive-tabs object, {{WRAPPER}} .bdt-interactive-tabs video' => 'height: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_content_width',
			[
				'label' => esc_html__( 'Width', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs-main-img' => 'width: {{SIZE}}{{UNIT}}; margin: auto;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters_hover',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .swiper-slide:hover .bdt-interactive-tabs-main-img',
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .swiper-slide:hover .bdt-interactive-tabs-main-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon_box',
			[
				'label'      => __('Icon', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('icon_colors');

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon',
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'selector'    => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon'
			]
		);

		$this->add_control(
			'icon_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'icon_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'icon_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/interactive-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'icon_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-icon'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-icon',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-icon'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-icon'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_active_background',
				'selector'  => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-icon',
			]
		);

		$this->add_control(
			'icon_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-icon'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-icon'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__('Subtitle', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item:hover .bdt-interactive-tabs-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_title_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-item.bdt-active .bdt-interactive-tabs-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-interactive-tabs .bdt-interactive-tabs-sub-title',
			]
		);

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls('swiper-carousel');

		$this->update_responsive_control(
			'arrows_ncx_position',
			[
				'default'        => [
					'size' => 20,
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
			]
		);

		$this->update_responsive_control(
			'arrows_ncy_position',
			[
				'default'        => [
					'size' => -20,
				],
				'tablet_default' => [
					'size' => -20,
				],
				'mobile_default' => [
					'size' => -20,
				],
			]
		);

		$this->end_controls_section();
	}

	public function render_item_image($image) {
		$settings  = $this->get_settings_for_display();

		$thumb_url = Group_Control_Image_Size::get_attachment_image_src($image['image']['id'], 'thumbnail_size', $settings);

		if (!$thumb_url) {
			$thumb_url = $image['image']['url'];
		}

		if (!empty($image['image_link']['url'])) {
			$this->add_render_attribute('image-link', 'href', $image['image_link']['url'], true);

			if ($image['image_link']['is_external']) {
				$this->add_render_attribute('image-link', 'target', '_blank', true);
			}

			if ($image['image_link']['nofollow']) {
				$this->add_render_attribute('image-link', 'rel', 'nofollow', true);
			}
		}

?>
		<?php if (!empty($image['image_link']['url'])) : ?>
			<a <?php echo $this->get_render_attribute_string('image-link'); ?>>
			<?php endif; ?>

			<?php 
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($image['image']['id'], 'thumbnail_size', $settings);
			if (!$thumb_url) {
				printf('<img src="%1$s" alt="%2$s">', $image['image']['url'], esc_html($image['tab_title']));
			} else {
				print(wp_get_attachment_image(
					$image['image']['id'],
					$settings['thumbnail_size_size'],
					false,
					[
						'alt' => esc_html($image['tab_title'])
					]
				));
			}
			?>

			<?php if (!empty($image['image_link']['url'])) : ?>
			</a>
		<?php endif; ?>
	<?php
	}

	public function rendar_item_video($link) {
		$video_src = $link['video_link'];

	?>
		<video class="bdt-interactive-tabs-iframe" src="<?php echo  $video_src; ?>" autoplay muted></video>
	<?php

	}

	public function rendar_item_youtube($link) {
		$match = [];
		$id = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link['youtube_link'], $match)) ? $match[1] : false;
		$url = '//www.youtube.com/embed/' . $id . '?autoplay=1&amp;controls=0&amp;showinfo=0&amp;rel=0&amp;loop=1&amp;modestbranding=1&amp;wmode=transparent&amp;playsinline=1&playlist=' . $id;

	?>
		<iframe class="bdt-interactive-tabs-iframe" height="460" src="<?php echo  esc_url($url); ?>" frameborder="0" allow='autoplay' allowfullscreen></iframe>

	<?php

	}

	public function tabs_content() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

	?>

		<?php foreach ($settings['tabs'] as $index => $item) :
			$tab_count = $index + 1;
			$tab_id    = 'bdt-tab-' . $tab_count . esc_attr($id);

			$this->add_render_attribute('tabs-content', 'class', 'swiper-slide', true);

		?>

			<div id="<?php echo esc_attr($tab_id); ?>" <?php echo ($this->get_render_attribute_string('tabs-content')); ?>>

				<?php
				if ('background' == $item['source']) { ?>
					<div class="bdt-interactive-tabs-main-img">
						<?php if (($item['background'] == 'image') && $item['image']) : ?>
							<?php $this->render_item_image($item); ?>
						<?php elseif (($item['background'] == 'video') && $item['video_link']) : ?>
							<?php $this->rendar_item_video($item); ?>
						<?php elseif (($item['background'] == 'youtube') && $item['youtube_link']) : ?>
							<?php $this->rendar_item_youtube($item); ?>
						<?php endif; ?>
					</div>
				<?php } elseif ("elementor" == $item['source'] and !empty($item['template_id'])) {
					echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($item['template_id']);
					echo element_pack_template_edit_link($item['template_id']);
				}
				?>

			</div>
		<?php endforeach; ?>

	<?php
	}

	public function tab_items() {
		$settings = $this->get_settings_for_display();
	?>
		<div class="bdt-interactive-tabs-wrap">

			<?php
			$slide_index = 1;
			foreach ($settings['tabs'] as $index => $item) :

				$this->add_render_attribute('tabs-item', 'class', 'bdt-interactive-tabs-item', true);

				$this->add_render_attribute('title-title', 'class', 'bdt-interactive-tabs-title', true);

			?>
				<div <?php echo ($this->get_render_attribute_string('tabs-item')); ?> data-slide="<?php echo ($slide_index - 1); ?>">

					<?php if ($settings['show_icon']) : ?>
						<div class="bdt-interactive-tabs-icon">
							<?php Icons_Manager::render_icon($item['selected_icon'], ['aria-hidden' => 'true']); ?>
						</div>
					<?php endif; ?>

					<div>
						<?php if ($item['tab_sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
							<div class="bdt-interactive-tabs-sub-title">
								<?php echo wp_kses($item['tab_sub_title'], element_pack_allow_tags('title')); ?>
							</div>
						<?php endif; ?>

						<?php if ($item['tab_title'] && ('yes' == $settings['show_title'])) : ?>
							<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('title-title'); ?>>
								<?php echo wp_kses($item['tab_title'], element_pack_allow_tags('title')); ?>
							</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
						<?php endif; ?>
					</div>

					<?php $slide_index++; ?>
				</div>

			<?php endforeach; ?>

		</div>
	<?php
	}


	public function render() {
		$settings        = $this->get_settings_for_display();
		$id              = 'bdt-interactive-tabs-' . $this->get_id();

		$this->add_render_attribute('interactive-tabs', 'id', $id);
		$this->add_render_attribute('interactive-tabs', 'class', 'bdt-interactive-tabs-content');

		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute('interactive-tabs', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('interactive-tabs', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute('interactive-tabs', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute('interactive-tabs', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position']);
		}

		if ('arrows-fraction' == $settings['navigation']) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation']) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'interactive-tabs' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"id"			 => '#' . $id,
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => true,
							"speed"          => $settings["speed"]["size"],
							"effect"         => $settings["transition"],
							"fadeEffect"     => ['crossFade' => true],
							"lazy"           => true,
							"autoHeight"     => true,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"observer"       => true,
							"observeParents" => true,
							"navigation" => [
								"nextEl" => "#" . $id . " .bdt-navigation-next",
								"prevEl" => "#" . $id . " .bdt-navigation-prev",
							],
							"pagination" => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,
							],
							"scrollbar" => [
								"el"            => "#" . $id . " .swiper-scrollbar",
								"hide"          => "true",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);

	?>
		<div class="bdt-interactive-tabs">
			<?php $this->tab_items(); ?>

			<div <?php $this->print_render_attribute_string('interactive-tabs'); ?>>
				<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
					<div class="swiper-wrapper">
						<?php
						$this->tabs_content();
						$this->render_footer(); ?>

					</div>
			<?php
		}
	}
