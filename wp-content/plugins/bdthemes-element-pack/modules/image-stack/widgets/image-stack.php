<?php
namespace ElementPack\Modules\ImageStack\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Image_Stack extends Module_Base {

	public function get_name() {
		return 'bdt-image-stack';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Image Stack', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-image-stack';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'image', 'icon', 'stack', 'group' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-image-stack', 'tippy'];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
			return [ 'popper', 'tippyjs', 'ep-image-stack' ];
        }
  	}

	public function get_custom_help_url() {
		return 'https://youtu.be/maLIlug2RwM';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_image_stack',
			[
				'label' => __( 'Image Stack', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'item_content_tabs' );

		$repeater->start_controls_tab( 
			'tab_item_content',
			[
				'label' => __( 'Content', 'bdthemes-element-pack' )
			]
		);

		$repeater->add_control(
			'media_type',
			[
				'label'        => esc_html__('Media Type', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'image',
				'render_type'  => 'template',
				'options'      => [
					'image' => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'far fa-image'
					],
					'icon' => [
						'title' => esc_html__('Icon', 'bdthemes-element-pack'),
						'icon'  => 'fas fa-star'
					]
				]
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label'   => __( 'Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'render_type'      => 'template',
				'condition'        => [
					'media_type' => 'icon',
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'       => __( 'Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'media_type' => 'image'
				]
			]
		);

		$repeater->add_control(
			'link_url',
			[
				'label'       => __( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 
			'tab_tooltip_text',
			[
				'label' => __( 'Tooltip', 'bdthemes-element-pack' )
			]
		);

		$repeater->add_control(
			'tooltip_text',
			[
				'label' => __( 'Text', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'tooltip_placement',
			[
				'label'   => __( 'Placement', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top'    => __( 'Top', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
					'left'   => __( 'Left', 'bdthemes-element-pack' ),
					'right'  => __( 'Right', 'bdthemes-element-pack' ),
				],
				'condition'   => [
					'tooltip_text!' => '',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 
			'tab_item_style',
			[
				'label' => __( 'Style', 'bdthemes-element-pack' )
			]
		);

		$repeater->add_control(
            'icon_color', 
             [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} svg' => 'fill: {{VALUE}};',
                ],
				'condition' => [ 
					'media_type' => 'icon' 
				],
            ]
        );

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} span, {{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} a',
				'condition' => [ 
					'media_type' => 'icon' 
				],
			]
		);

		$repeater->add_control(
			'border_color_item',
			[
				'label' => __( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} span, {{WRAPPER}} .bdt-ep-image-stack-item{{CURRENT_ITEM}} a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'image_stack_items',
			[
				'show_label' => false,
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
					],
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
					],
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
					],
					[
						'image' => ['url' => Utils::get_placeholder_image_src()],
					],
				],
				'title_field' => '{{{ tooltip_text }}}',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'default'      => 'medium',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-bdt-image-stack .elementor-widget-container' => 'text-align: {{VALUE}};'
				]
			]
		);
	
		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[
				'label' => __( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'item_style_tabs' );

		$this->start_controls_tab( 
			'tab_item_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' )
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-ep-image-stack-item svg' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a'
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'item_border',
                'label'          => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width'  => [
                        'default' => [
                            'top'      => '3',
                            'right'    => '3',
                            'bottom'   => '3',
                            'left'     => '3',
                            'isLinked' => false,
                        ],
                    ],
                    'color'  => [
                        'default' => '#fff',
                    ],
                ],
                'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a',
                'separator'   => 'before',
            ]
        );

		$this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-image-stack-item,{{WRAPPER}}  .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);

		$this->add_responsive_control(
			'item_size',
			[
				'label' => __( 'Item Size', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item:not(:last-child) span, {{WRAPPER}} .bdt-ep-image-stack-item:not(:last-child) a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_stack_spacing',
			[
				'label' => __( 'Stack Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item:not(:first-child) a, {{WRAPPER}} .bdt-ep-image-stack-item:not(:first-child) span' => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control( 
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'label' => __( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tab_item_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' )
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Icon Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover svg' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_color_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a'
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control( 
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow_hover',
				'label' => __( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_stack_animations',
			[
				'label' => __( 'Stack Animations', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'stack_style_tabs' );

		$this->start_controls_tab( 
			'tab_stack_item_hover',
			[
				'label' => __( 'Item Hover', 'bdthemes-element-pack' )
			]
		);

		$this->add_control(
			'item_translate_toggle_hover',
			[
				'label' 		=> __( 'Translate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();


		$this->add_responsive_control(
			'item_effect_transx_hover',
			[
				'label'      => esc_html__( 'Translate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'condition' => [
					'item_translate_toggle_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-trans-x-hover: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_transy_hover',
			[
				'label'      => esc_html__( 'Translate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-trans-y-hover: {{SIZE}}px;'
				],
				'condition' => [
					'item_translate_toggle_hover' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_rotate_toggle_hover',
			[
				'label' 		=> __( 'Rotate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_rotatex_hover',
			[
				'label'      => esc_html__( 'Rotate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'item_rotate_toggle_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-rotate-x-hover: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_rotatey_hover',
			[
				'label'      => esc_html__( 'Rotate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'item_rotate_toggle_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-rotate-y-hover: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_rotatez_hover',
			[
				'label'   => __( 'Rotate Z', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-rotate-z-hover: {{SIZE||0}}deg;'
				],
				'condition' => [
					'item_rotate_toggle_hover' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_scale_hover',
			[
				'label' 		=> __( 'Scale', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_scalex_hover',
			[
				'label'      => esc_html__( 'Scale X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'item_scale_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-scale-x-hover: {{SIZE}};'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_scaley_hover',
			[
				'label'      => esc_html__( 'Scale Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'item_scale_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-scale-y-hover: {{SIZE}};'
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_skew_hover',
			[
				'label' 		=> __( 'Skew', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_skewx_hover',
			[
				'label'      => esc_html__( 'Skew X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'item_skew_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-skew-x-hover: {{SIZE}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_skewy_hover',
			[
				'label'      => esc_html__( 'Skew Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'item_skew_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-skew-y-hover: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_effect_transition',
			[
				'label' => __( 'Transition', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'none',
			]
		);
		
		$this->start_popover();

		$this->add_control(
			'item_effect_transition_duration',
			[
				'label'       => esc_html__( 'Duration (ms)', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '300',
				'condition' => [
					'item_effect_transition' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-transition-duration: {{VALUE}}ms;',
				],
			]
		);

		$this->add_control(
			'item_effect_transition_delay',
			[
				'label'       => esc_html__( 'Delay (ms)', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'condition' => [
					'item_effect_transition' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-transition-delay: {{VALUE}}ms;',
				],
			]
		);

		$this->add_control(
			'item_effect_transition_easing',
			[
				'label'   => esc_html__( 'Easing', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'If you want use Cubic Bezier easing, Go %1s HERE %2s', 'bdthemes-element-pack' ), '<a href="https://cubic-bezier.com/" target="_blank">', '</a>' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'ease-out',
				'condition' => [
					'item_effect_transition' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-item-transition-easing: {{VALUE}};',
				],
			]
		);
		
		$this->end_popover();

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tab_stack_hover',
			[
				'label' => __( 'Stack Hover', 'bdthemes-element-pack' )
			]
		);

		$this->add_control(
			'stack_translate_toggle_normal',
			[
				'label' 		=> __( 'Translate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'stack_effect_transx_normal',
			[
				'label'      => esc_html__( 'Translate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'condition' => [
					'stack_translate_toggle_normal' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-stack-trans-x-normal: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'stack_effect_transy_normal',
			[
				'label'      => esc_html__( 'Translate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-stack-trans-y-normal: {{SIZE}}px;'
				],
				'condition' => [
					'stack_translate_toggle_normal' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'stack_rotate_toggle_normal',
			[
				'label' 		=> __( 'Rotate', 'bdthemes-element-pack' ),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'stack_effect_rotatex_normal',
			[
				'label'      => esc_html__( 'Rotate X', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'stack_rotate_toggle_normal' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-stack-rotate-x-normal: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'stack_effect_rotatey_normal',
			[
				'label'      => esc_html__( 'Rotate Y', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'stack_rotate_toggle_normal' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-stack-rotate-y-normal: {{SIZE||0}}deg;'
				],
			]
		);


		$this->add_responsive_control(
			'stack_effect_rotatez_normal',
			[
				'label'   => __( 'Rotate Z', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-stack-rotate-z-normal: {{SIZE||0}}deg;'
				],
				'condition' => [
					'stack_rotate_toggle_normal' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => __( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tooltip_width',
			[
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'tooltip_text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tooltip_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_section();

	}

	protected function render_media($item) {
		$settings  = $this->get_settings_for_display();

		?>

		<?php if ( 'icon' == $item['media_type'] ) { ?>
			<?php Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
		<?php } elseif ( 'image' == $item['media_type'] ) { 
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
			if (!$thumb_url) {
				printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['tooltip_text']));
			} else {
				print(wp_get_attachment_image(
					$item['image']['id'],
					$settings['thumbnail_size_size'],
					false,
					[
						'alt' => esc_html($item['tooltip_text'])
					]
				));
			}
		} ?>

		<?php
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		
		if ( empty( $settings['image_stack_items'] ) ) {
			return;
		}

		?>
		<div class="bdt-image-stack">
			<?php foreach ( $settings['image_stack_items'] as $index => $item ) : 
				
				$this->add_render_attribute('stack-item', 'class', 'bdt-ep-image-stack-item elementor-repeater-item-' . esc_attr($item['_id']), true);

				if ( isset($item['tooltip_text']) && !empty($item['tooltip_text']) ) {
					// Tooltip settings
					$this->add_render_attribute( 'stack-item', 'class', [
						'bdt-ep-image-stack-item elementor-repeater-item-' . esc_attr($item['_id']),
						'bdt-tippy-tooltip',
					], true );
					$this->add_render_attribute( 'stack-item', 'data-tippy', '', true );
					$this->add_render_attribute( 'stack-item', 'data-tippy-arrow', 'true', true );
					$this->add_render_attribute( 'stack-item', 'data-tippy-placement', $item['tooltip_placement'], true );
					$this->add_render_attribute( 'stack-item', 'data-tippy-content', $item['tooltip_text'], true );
				}

				if ( !empty( $item['link_url']['url'] ) ) {
					$this->add_link_attributes( 'link-wrap' . $index, $item['link_url'] );
                }

				?>
				
				<div <?php echo($this->get_render_attribute_string('stack-item')); ?>>
				<?php if( !empty( $item['link_url']['url'] ) ) : ?>
					<a <?php echo($this->get_render_attribute_string('link-wrap' . $index)); ?>>
						<?php $this->render_media($item); ?>
					</a>
				<?php else: ?>
					<span>
						<?php $this->render_media($item); ?>
					</span>
				<?php endif; ?>
				</div>

			<?php endforeach; ?>
		</div>
		<?php
	}
}
