<?php
namespace ElementPack\Modules\PriceList\Widgets;

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Price_List extends Module_Base {

	public function get_name() {
		return 'bdt-price-list';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Price List', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-price-list';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'price', 'lsit', 'rate', 'cost', 'value' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-price-list' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/QsXkIYwfXt4';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_list',
			[
				'label' => esc_html__( 'List', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'price', 
			[
				'label'   => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'old_price', 
			[
				'label'   => esc_html__( 'Old Price', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'title', 
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'First item on the list', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => 'true',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_description', 
			[
				'label'   => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_badge', 
			[
				'label'   => esc_html__( 'Badge', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'image', 
			[
				'label'   => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [],
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'link', 
			[
				'label'   => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_control(
			'price_list',
			[
				'label'  => esc_html__( 'List Items', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__( 'First item on the list', 'bdthemes-element-pack' ),
						'price' => '$20',
						'link'  => [ 'url' => '#' ],
					],
					[
						'title' => esc_html__( 'Second item on the list', 'bdthemes-element-pack' ),
						'price' => '$9',
						'link'  => [ 'url' => '#' ],
					],
					[
						'title' => esc_html__( 'Third item on the list', 'bdthemes-element-pack' ),
						'price' => '$32',
						'link'  => [ 'url' => '#' ],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__( 'Additional', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail_size',
				'label'     => esc_html__( 'Image Size', 'bdthemes-element-pack' ) . BDTEP_NC,
				'exclude'   => [ 'custom' ],
				'default'   => 'thumbnail',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'image_hide_on',
			[
				'label'       => __( 'Image Hide On', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'        => Controls_Manager::SELECT2,
				'default'     => ['mobile'],
				'multiple'    => true,
				'label_block' => true,
				'options'     => [
					'desktop'    => __( 'Desktop', 'bdthemes-element-pack' ),
					'tablet'     => __( 'Tablet', 'bdthemes-element-pack' ),
					'mobile'     => __( 'Mobile', 'bdthemes-element-pack' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
            'vertical_align',
            [
				'label'       => esc_html__( 'Vertical Align', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'description' => 'When you will take image then you understand its function',
				'options'     => [
					'middle' => esc_html__( 'Middle', 'bdthemes-element-pack' ),
					'top'    => esc_html__( 'Top', 'bdthemes-element-pack' ),
					'bottom' => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
                ],
				'default'   => 'middle',
				'separator' => 'before',
            ]
        );

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Columns', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
				],
				'selectors'      => [
					'{{WRAPPER}} .bdt-price-list' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'separator'    => 'before',
			]
		);

		$this->add_responsive_control(
			'grid_column_gap',
			[
				'label'     => esc_html__( 'Column Gap', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'columns!' => '1'
				]
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_counter',
			[
				'label'     => __( 'Item Counter', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'bdthemes-element-pack' ),
				'label_off' => __( 'Hide', 'bdthemes-element-pack' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_cart',
			[
				'label'     => esc_html__( 'Add to Cart', 'bdthemes-element-pack' ) . BDTEP_NC,
				'label_on'  => __( 'Show', 'bdthemes-element-pack' ),
				'label_off' => __( 'Hide', 'bdthemes-element-pack' ),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'cart_icon',
			[
				'label'       => __( 'Cart Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'render_type' => 'template',
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => [
					'value'   => 'fas fa-cart-arrow-down',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'show_cart' => 'yes',
                ],
			]
		);

		$this->add_responsive_control(
            'cart_spacing',
            [
                'label' => esc_html__( 'Cart Space', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
				'condition'   => [
					'show_cart' => 'yes',
                ],
				'default'   => [
					'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-cart-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'show_old_price',
			[
				'label'     => esc_html__( 'Old Price', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'bdthemes-element-pack' ),
				'label_off' => __( 'Hide', 'bdthemes-element-pack' ),
				'separator' => 'before',
				'return_value' => 'yes',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
            'old_price_spacing',
            [
                'label' => esc_html__( 'Old Price Space', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
				'condition'   => [
					'show_old_price' => 'yes',
                ],
				'default'   => [
					'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-old-price' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'show_badge',
			[
				'label'       => esc_html__( 'Badge', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => __( 'Show', 'bdthemes-element-pack' ),
				'label_off'   => __( 'Hide', 'bdthemes-element-pack' ),
				'separator'   => 'before',
			]
		);
		
		$this->add_control(
			'badge_offset',
			[
				'label'       => esc_html__( 'Badge Offset', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::POPOVER_TOGGLE,
				'label_off'   => __( 'Default', 'bdthemes-element-pack' ),
				'label_on'    => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'condition'   => [
					'show_badge' => 'yes',
				],
			]
		);
		
		$this->start_popover();
		
		$this->add_responsive_control(
            'badge_h_spacing',
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
					'show_badge' => 'yes',
                    'badge_offset' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-price-list-badge-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_v_spacing',
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
					'show_badge' => 'yes',
                    'badge_offset' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-price-list-badge-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack') . BDTEP_NC,
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
					'show_badge' => 'yes',
                    'badge_offset' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-price-list-badge-rotate: {{SIZE}}deg;'
                ],
            ]
        );

		$this->end_popover();

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .bdt-price-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-list-item',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_hover_background',
				'selector' => '{{WRAPPER}} .bdt-price-list-item:hover',
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label'      => esc_html__( 'Title', 'bdthemes-element-pack' ),
                'tab'        => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_hover_color',
            [
                'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ) . BDTEP_NC,
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-title' => 'color: {{VALUE}};',
                ],
            ]
        );

		// margin
		$this->add_responsive_control(
			'heading_margin',
			[
				'label'      => __( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'heading_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-price-list-header',
            ]
        );

		//group control text stroke
		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'heading_text_stroke',
				'label'    => esc_html__( 'Text Stroke', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-price-list-title',
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label'      => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		// price tabs control
		$this->start_controls_tabs('tabs_price_style');
		// price normal tab
		$this->start_controls_tab(
			'tab_price_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4AB8F8',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-price' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'selector'    => '{{WRAPPER}} .bdt-price-list-price',
			]
		);
		
		$this->add_responsive_control(
			'price_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '50',
					'right'  => '50',
					'bottom' => '50',
					'left'   => '50',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'price_width',
			[
				'label'   => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-price' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'price_height',
			[
				'label' => esc_html__( 'Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-price' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'price_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-price',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .bdt-price-list-price',
			]
		);

		// price end normal tab
		$this->end_controls_tab();
		// price hover tab
		$this->start_controls_tab(
			'tab_price_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'price_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_hover_bg',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-price' => 'background-color: {{VALUE}};',
				],
			]
		);

		//border color
		$this->add_control(
			'price_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-price' => 'border-color: {{VALUE}};',
				],
			]
		);

		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'price_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-price',
			]
		);

		// price end hover tab
		$this->end_controls_tab();
		// price tabs end
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_old_price',
			[
				'label'      => esc_html__( 'Old Price', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => [
					'show_old_price' => 'yes',
				],
			]
		);
		// old price tabs control
		$this->start_controls_tabs('tabs_old_price_style');
		// old price normal tab
		$this->start_controls_tab(
			'tab_old_price_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'old_price_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-old-price del' => 'color: {{VALUE}};',
				],
			]
		);
		//background color
		$this->add_control(
			'old_price_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-old-price del' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'old_price_border',
				'selector'    => '{{WRAPPER}} .bdt-price-list-old-price del',
			]
		);

		$this->add_responsive_control(
			'old_price_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '50',
					'right'  => '50',
					'bottom' => '50',
					'left'   => '50',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-old-price del' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'old_price_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-old-price del' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'old_price_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-old-price del',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'old_price_typography',
				'selector' => '{{WRAPPER}} .bdt-price-list-old-price del',
			]
		);

		// old price end normal tab
		$this->end_controls_tab();
		// old price hover tab
		$this->start_controls_tab(
			'tab_old_price_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);

		$this->add_control(
			'old_price_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-old-price del' => 'color: {{VALUE}};',
				],
			]
		);

		//background color
		$this->add_control(
			'old_price_hover_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-old-price del' => 'background-color: {{VALUE}};',
				],
			]
		);

		//border color
		$this->add_control(
			'old_price_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'old_price_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-old-price del' => 'border-color: {{VALUE}};',
				],
			]
		);

		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'old_price_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-old-price del',
			]
		);

		// old price end hover tab
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

        $this->start_controls_section(
            'section_style_description',
            [
				'label'      => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'description_hover_color',
            [
                'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ) . BDTEP_NC,
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-price-list-description',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_separator',
            [
				'label'      => esc_html__( 'Separator', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
            ]
        );

        $this->add_control(
            'separator_style',
            [
                'label'   => esc_html__( 'Style', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
                    'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
                    'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
                    'double' => esc_html__( 'Double', 'bdthemes-element-pack' ),
                    'none'   => esc_html__( 'None', 'bdthemes-element-pack' ),
                ],
                'default'   => 'dashed',
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'separator_weight',
            [
                'label' => esc_html__( 'Width', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 10,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'size' => 1,
                ],
            ]
        );

        $this->add_responsive_control(
            'separator_spacing',
            [
                'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 40,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
            'separator_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-color: {{VALUE}};',
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
				'separator' => 'before'
            ]
        );

        $this->add_control(
            'separator_hover_color',
            [
                'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-separator' => 'border-bottom-color: {{VALUE}};',
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_image_style',
			[
				'label'      => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		//tabs control
		$this->start_controls_tabs('tabs_image_style');
		//normal tab
		$this->start_controls_tab(
			'tab_image_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'image_border',
				'selector'    => '{{WRAPPER}} .bdt-price-list-image',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-image, {{WRAPPER}} .bdt-price-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// padding
		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 250,
					],
				],
				'default' => [
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-image' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-image' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'default' => [
					'size' => 20,
				],
			]
		);

		// box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-image',
			]
		);

		// normal tab end
		$this->end_controls_tab();
		// hover tab
		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);
		// border color
		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'image_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-image' => 'border-color: {{VALUE}};',
				],
			]
		);
		// box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-image',
			]
		);
		// hover tab end
		$this->end_controls_tab();
		// tabs end
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
            'section_style_counter',
            [
				'label'      => esc_html__( 'Counter', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => [
					'item_counter' => 'yes',
				],
            ]
        );

		$this->start_controls_tabs( 'tabs_counter_style' );

		$this->start_controls_tab(
			'tab_counter_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'counter_color',
			[
				'label' => __( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-counter::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'counter_background',
				'selector'  => '{{WRAPPER}} .bdt-price-list-counter',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'counter_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-list-counter',
			]
		);

		$this->add_responsive_control(
			'counter_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-counter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'counter_space',
			[
				'label'      => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-counter' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'counter_typography',
				'label'     => __( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-price-list-counter::before',
				'exclude' => [
					'letter_spacing',
					'text_transform',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'counter_shadow',
				'selector' => '{{WRAPPER}}  .bdt-price-list-counter',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_counter_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);

		$this->add_control(
			'counter_hover_color',
			[
				'label' => __( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-counter::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'counter_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-counter',
			]
		);

		$this->add_control(
			'counter_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'counter_border_border!' => ''
				],
				'selectors' => [
					'{{WRAPPER}}  .bdt-price-list-item:hover .bdt-price-list-counter' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'counter_hover_shadow',
				'selector' => '{{WRAPPER}}  .bdt-price-list-item:hover .bdt-price-list-counter',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => esc_html__( 'Cart Icon', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cart'         => 'yes',
					'cart_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_cart_icon_style' );

		$this->start_controls_tab(
			'tab_cart_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'cart_icon_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-cart-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-price-list-cart-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cart_icon_background',
				'selector'  => '{{WRAPPER}} .bdt-price-list-cart-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'cart_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-list-cart-icon',
			]
		);

		$this->add_responsive_control(
			'cart_icon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-cart-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-cart-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cart_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-cart-icon',
			]
		);

		$this->add_responsive_control(
			'cart_icon_size',
			[
				'label' => __( 'Icon Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-cart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cart_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'cart_icon_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-cart-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-cart-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cart_icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-cart-icon',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'cart_icon_border_border!' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-cart-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'      => esc_html__( 'Badge', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => [
					'show_badge' => 'yes',
                ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-item:hover .bdt-price-list-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_background_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-list-badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '50',
					'right'  => '50',
					'bottom' => '50',
					'left'   => '50',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-price-list-badge',
			]
		);

		$this->end_controls_section();
	}

	private function render_image( $item, $settings ) {

		$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
		if (!$thumb_url) {
			printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($item['title']));
		} else {
			print(wp_get_attachment_image(
				$item['image']['id'],
				$settings['thumbnail_size_size'],
				false,
				[
					'alt' => esc_html($item['title'])
				]
			));
		}
	}

	private function render_item_header( $item ) {
		$settings        = $this->get_settings_for_display();
		$bdt_has_image   = $item['image']['url'] ? ' bdt-has-image' : '';
		$unique_link_id  = 'item-link-' . $item['_id'];           
		$bdt_has_badge   = ( 'yes' == $settings['show_badge'] and $item['item_badge'] ) ? '<span class="bdt-price-list-badge">' . esc_attr( $item['item_badge'] ) . '</span>' : '';

		$bdt_has_counter   = ( 'yes' === $settings['item_counter'] ) ? '<div class="bdt-price-list-counter"></div>' : '';

	    $this->add_render_attribute( $unique_link_id, 'class', 'bdt-grid bdt-grid-collapse bdt-flex-'. esc_attr($settings['vertical_align']) );
	    $this->add_render_attribute( $unique_link_id, 'class', esc_attr($bdt_has_image) );

        if ( $item['link']['url'] ) {
            $target = $item['link']['is_external'] ? '_blank' : '_self';
            $this->add_render_attribute( $unique_link_id, 'onclick', "window.open('" . $item['link']['url'] . "', '$target')" );
        }

		return '<li class="bdt-price-list-item">'.$bdt_has_counter.$bdt_has_badge.'<div '. $this->get_render_attribute_string( $unique_link_id ) .'bdt-grid>';
	}


	private function render_item_footer( $item ) {
			return '</div></li>';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image_hide_on_setup = '';
		 
		if (!empty($settings['image_hide_on'])) {
			foreach ( $settings['image_hide_on'] as $element ) {
				
				if ( $element == 'desktop' ) {
					$image_hide_on_setup .= ' bdt-desktop';
				}
				if ( $element == 'tablet' ) {
					$image_hide_on_setup .= ' bdt-tablet';
				}
				if ( $element == 'mobile' ) {
					$image_hide_on_setup .= ' bdt-mobile';
				}
			}
		}

		?>
		<ul class="bdt-price-list">

		<?php foreach ( $settings['price_list'] as $item ) :
			echo $this->render_item_header( $item );

			if ( ! empty( $item['image']['url'] ) ) : ?>
				<div class="bdt-price-list-image bdt-width-auto <?php echo $image_hide_on_setup; ?>">
					<?php echo $this->render_image( $item, $settings ); ?>
				</div>
			<?php endif; ?>

			<div class="bdt-price-list-text bdt-width-expand">
				<div>
					<div class="bdt-price-list-header bdt-grid bdt-grid-small bdt-flex-middle" bdt-grid>
						<span class="bdt-price-list-title"><?php echo esc_html($item['title']); ?></span>

						<?php if ( 'none' != $settings['separator_style'] ) : ?>
							<span class="bdt-price-list-separator bdt-width-expand"></span>
						<?php endif; ?>

					</div>

                    <?php if ( $item['item_description'] ) : ?>
                        <p class="bdt-price-list-description"><?php echo $this->parse_text_editor($item['item_description']); ?></p>
                    <?php endif; ?>
				</div>
			</div>
			<div class="bdt-width-auto bdt-flex-inline bdt-flex-middle">
				<?php if ( $item['old_price'] and $settings['show_old_price'] ) : ?>
					<span class="bdt-price-list-old-price bdt-flex bdt-flex-middle bdt-flex-center"><del><?php echo esc_html($item['old_price']); ?></del></span>
				<?php endif; ?>
				<span class="bdt-price-list-price bdt-flex bdt-flex-middle bdt-flex-center"><?php echo esc_html($item['price']); ?></span>
			</div>

			<?php if ( ! empty( $settings['cart_icon']['value'] ) ) : ?>
				<div class="bdt-width-auto bdt-flex-inline">
					<span class="bdt-price-list-cart-icon"><?php Icons_Manager::render_icon($settings['cart_icon'], ['aria-hidden' => 'true']); ?></span>
				</div>
			<?php endif; ?>

			<?php echo $this->render_item_footer( $item ); ?>

		<?php endforeach; ?>

		</ul>
		<?php
	}

}
