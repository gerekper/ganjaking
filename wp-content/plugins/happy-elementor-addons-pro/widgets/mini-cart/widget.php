<?php
/**
 * Woocommerce mini cart widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

class Mini_Cart extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Mini Cart', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-mini-cart';
	}

	public function get_keywords() {
		return ['mini-cart', 'woo', 'product', 'woocommerce', 'cart', 'mini', 'shop'];
	}


	/**
     * Register widget content controls
     */
	protected function register_content_controls() {

        $this->start_controls_section(
			'mini_cart_content_section',
			[
				'label' => __( 'Mini Cart', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'mini_cart_icons',
			[
				'label' => __( 'Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ha_woo_mini_cart_icon',
                'default' => [
                    'value' => 'fas fa-shopping-basket',
					'library' => 'fa-solid',
                ],
			]
        );

        $this->add_control(
            'mini_cart_show',
            [
                'label' => __( 'Cart Popup Show', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'click',
				'options' => [
					'none'     => __( 'None', 'happy-addons-pro' ),
					'click'     => __( 'Click', 'happy-addons-pro' ),
					'hover'     => __( 'Hover', 'happy-addons-pro' ),
				],

            ]
        );

		$this->add_control(
			'mini_cart_subtotal_show',
			[
				'label'                 => __( 'Show Subtotal', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_on'              => __( 'Show', 'happy-addons-pro' ),
				'label_off'             => __( 'Hide', 'happy-addons-pro' ),
				'return_value'          => 'yes',
				'default'               => 'yes',
			]
		);

        $this->add_control(
            'mini_cart_subtotal_position',
            [
                'label' =>__( 'Subtotal Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'right',
				'toggle' => false,
                'style_transfer' => true,
				'selectors_dictionary' => [
                    'left' => 'order: -1;',
                    'right' => 'order: 1;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-mini-cart-wrapper .ha-mini-cart-button .ha-mini-cart-total' => '{{VALUE}};'
				],
				'prefix_class' => 'ha-mini-cart-subtotal-position-',
				'condition'             => [
					'mini_cart_subtotal_show' => 'yes',
				],
            ]
        );

        $this->add_responsive_control(
            'mini_cart_alignment',
            [
                'label' =>__( 'Alignment', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors_dictionary' => [
                    'left' => 'text-align: left;',
                    'center' => 'text-align: center;',
                    'right' => 'text-align: right;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-mini-cart-wrapper' => '{{VALUE}};'
				],
				'prefix_class' => 'ha-mini-cart%s-align-',
                'default' => 'left',
            ]
        );

        $this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__cart_btn_style_controls();
		$this->__body_style_controls();
		$this->__header_style_controls();
		$this->__item_style_controls();
		$this->__subtotal_style_controls();
		$this->__btn_style_controls();
	}

	// Mini Cart Style
	protected function __cart_btn_style_controls() {

		$this->start_controls_section(
			'mini_cart_button_section',
			[
				'label' => __( 'Mini Cart', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'mini_cart_button_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mini_cart_icon_size',
			[
				'label' => __( 'Icon Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-mini-cart-button svg' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mini_cart_subtotal_space',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}}.ha-mini-cart-subtotal-position-right .ha-mini-cart-button .ha-mini-cart-total' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-mini-cart-subtotal-position-left .ha-mini-cart-button .ha-mini-cart-total' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'mini_cart_subtotal_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'mini_cart_button_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-button',
			]
		);

		$this->add_responsive_control(
			'mini_cart_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mini_cart_button_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-button',
			]
		);

		$this->start_controls_tabs('mini_cart_button_color_tabs');
		$this->start_controls_tab(
			'mini_cart_button_color_normal_tab',
			[
				'label' => __('Normal', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'mini_cart_button_normal_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-mini-cart-button svg'  => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'mini_cart_button_normal_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'mini_cart_button_border',
				'label'     => __( 'Border', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .ha-mini-cart-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'mini_cart_button_color_hover_tab',
			[
				'label' => __('Hover', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'mini_cart_button_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-mini-cart-button:hover svg'  => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'mini_cart_button_hover_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'mini_cart_hover_button_border',
				'label'     => __( 'Border', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .ha-mini-cart-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'mini_cart_button_count_heading',
			[
				'label' => __( 'Count:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'mini_cart_button_count_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_toggle',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'mini_cart_button_count_x',
			[
				'label' => __( 'Horizontal', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'style_transfer' => true,
				'render_type' => 'ui',
				'condition' => [
					'mini_cart_button_count_toggle' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'top: {{SIZE}}px;'
				]
			]
		);

		$this->add_responsive_control(
			'mini_cart_button_count_y',
			[
				'label' => __( 'Vertical', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'style_transfer' => true,
				'render_type' => 'ui',
				'condition' => [
					'mini_cart_button_count_toggle' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'right: {{SIZE}}px;'
				]
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'mini_cart_button_count_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mini_cart_button_count_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-count',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'mini_cart_button_count_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'selector'    => '{{WRAPPER}} .ha-mini-cart-count',
			]
		);

		$this->start_controls_tabs('mini_cart_button_count_tabs');
		$this->start_controls_tab(
			'mini_cart_button_count_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'mini_cart_button_count_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-count' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'mini_cart_button_count_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'mini_cart_button_count_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-button:hover .ha-mini-cart-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_hover_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-button:hover .ha-mini-cart-count' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_button_count_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-button:hover .ha-mini-cart-count' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	// Body Style
	protected function __body_style_controls(){

		$this->start_controls_section(
			'popup_body_section',
			[
				'label' => __( 'Popup:- Body', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition'             => [
					'mini_cart_show!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'popup_body_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_offset_x',
			[
				'label' => __( 'Offset X', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'popup_body_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_body_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_body_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'popup_body_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} .ha-mini-cart-popup',
			]
		);

		$this->add_responsive_control(
			'popup_body_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'popup_body_border_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup',
			]
		);

		$this->end_controls_section();
	}

	// Header Style
	protected function __header_style_controls(){

		$this->start_controls_section(
			'popup_header_section',
			[
				'label' => __( 'Popup:- Header', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition'             => [
					'mini_cart_show!' => 'none',
				],
			]
		);

		$this->add_control(
			'popup_header_content_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_header_content_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'		 => 'popup_header_content_typo',
				'selector'	 => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area, {{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area a',
			]
		);



		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'popup_header_side_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'selector'    => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area:before,{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area:after',
			]
		);

		$this->add_responsive_control(
			'popup_header_side_border_space',
			[
				'label' => __( 'Border Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area:before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-header .ha-mini-cart-popup-count-text-area:after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// Item Style
	protected function __item_style_controls(){

		$this->start_controls_section(
			'popup_item_section',
			[
				'label' => __( 'Popup:- Item', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition'             => [
					'mini_cart_show!' => 'none',
				],
			]
		);

		$this->add_control(
			'popup_item_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'popup_item_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_item_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_item_title_heading',
			[
				'label' => __( 'Title:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'		 => 'popup_item_title_typo',
				'selector'	 => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a:not(.remove)',
			]
		);

		$this->add_control(
			'popup_item_title_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a:not(.remove)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_item_quantity_heading',
			[
				'label' => __( 'Quantity:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'		 => 'popup_item_quantity_typo',
				'selector'	 => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li .quantity',
			]
		);

		$this->add_control(
			'popup_item_quantity_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li .quantity' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_item_image_heading',
			[
				'label' => __( 'Image:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'popup_item_image_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'selector'    => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a > img',
			]
		);

		$this->add_control(
			'popup_item_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'popup_item_image_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a > img',
			]
		);

		$this->add_responsive_control(
			'popup_item_remove_heading',
			[
				'label' => __( 'Remove:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'popup_item_remove_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'popup_item_remove_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'popup_item_remove_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove',
			]
		);

		$this->add_control(
			'popup_item_remove_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'popup_item_remove_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'popup_item_remove_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'selector'    => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove',
			]
		);

		$this->start_controls_tabs('popup_item_remove_color_tabs');
		$this->start_controls_tab(
			'popup_item_remove_color_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'popup_item_remove_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'popup_item_remove_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'popup_item_remove_color_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'popup_item_remove_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'popup_item_remove_hover_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'popup_item_remove_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body ul li a.remove:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	// Subtotal Style
	protected function __subtotal_style_controls(){

		$this->start_controls_section(
			'popup_subtotal_section',
			[
				'label' => __( 'Popup:- Subtotal', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition'             => [
					'mini_cart_show!' => 'none',
				],
			]
		);

		$this->add_control(
			'popup_subtotal_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_subtotal_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_subtotal_title_heading',
			[
				'label' => __( 'Title:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'popup_subtotal_title_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total strong',
			]
		);

		$this->add_control(
			'popup_subtotal_title_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_subtotal_price_heading',
			[
				'label' => __( 'Price:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'popup_subtotal_price_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total .amount',
			]
		);

		$this->add_control(
			'popup_subtotal_price_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__total .amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// Button Style
	protected function __btn_style_controls(){

		$this->start_controls_section(
			'popup_button_section',
			[
				'label' => __( 'Popup:- Button', 'happy-addons-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition'             => [
					'mini_cart_show!' => 'none',
				],
			]
		);

		$this->view_cart_btn_style();

		$this->checkout_btn_style();

		$this->end_controls_section();

	}

	/**
	 * View Cart Button Style controls
	 */
    protected function view_cart_btn_style(){

		$this->add_responsive_control(
			'view_cart_button_heading',
			[
				'label' => __( 'View Cart:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'view_cart_button_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'view_cart_button_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)',
			]
		);

		$this->add_responsive_control(
			'view_cart_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'view_cart_button_border',
				'label'     => __( 'Border', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'view_cart_button_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)',
			]
		);

		$this->start_controls_tabs('view_cart_button_color_tabs');
		$this->start_controls_tab(
			'view_cart_button_color_normal_tab',
			[
				'label' => __('Normal', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'view_cart_button_normal_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'view_cart_button_normal_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1)',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'view_cart_button_color_hover_tab',
			[
				'label' => __('Hover', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'view_cart_button_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'view_cart_button_hover_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1):hover',
			]
		);

		$this->add_control(
			'view_cart_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(1):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

	}

	/**
	 * Checkout Button Style controls
	 */
    protected function checkout_btn_style(){

		$this->add_responsive_control(
			'checkout_button_heading',
			[
				'label' => __( 'Checkout:', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'checkout_button_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_button_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)',
			]
		);

		$this->add_responsive_control(
			'checkout_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_button_border',
				'label'     => __( 'Border', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'checkout_button_shadow',
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)',
			]
		);

		$this->start_controls_tabs('checkout_button_color_tabs');
		$this->start_controls_tab(
			'checkout_button_color_normal_tab',
			[
				'label' => __('Normal', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'checkout_button_normal_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'checkout_button_normal_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2)',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'checkout_button_color_hover_tab',
			[
				'label' => __('Hover', 'happy-addons-pro')
			]
		);

		$this->add_control(
			'checkout_button_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'checkout_button_hover_bg_color',
				'exclude' => [
					'classic' => 'image' // remove image bg option
				],
				'selector' => '{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2):hover',
			]
		);

		$this->add_control(
			'checkout_button_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-mini-cart-popup .ha-mini-cart-popup-body .woocommerce-mini-cart__buttons .button:nth-child(2):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

	}


    protected function render(){

		if ( ! function_exists( 'WC' ) ) {
			$this->show_wc_missing_alert();
			return;
		}

		$settings = $this->get_settings();

		$this->add_render_attribute(
			'wrapper',
			'class',
			[
				'ha-mini-cart-wrapper',
			]
		);

		$this->add_render_attribute(
			'inner',
			[
				'class' => [
					'ha-mini-cart-inner',
					$settings['mini_cart_show'] !== 'none' ? 'ha-mini-cart-on-' . esc_attr( $settings['mini_cart_show'] ) : '',
				]
			]
		);
        ?>

        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>

                <div class="ha-mini-cart-button">

                    <div class="ha-mini-cart-count-area">
						<span class="ha-mini-cart-icon">
							<?php
								if ( $settings['mini_cart_icons']['value'] ) {
									Icons_Manager::render_icon( $settings['mini_cart_icons'], [ 'aria-hidden' => 'true' ] );
								} else { ?>
									<i class="fas fa-shopping-basket" aria-hidden="true"></i>
									<?php
								}
							?>
						</span>
						<span class="ha-mini-cart-count">
	                        <?php echo (( WC()->cart != '' ) ? WC()->cart->get_cart_contents_count()." " : '' ); ?>
						</span>
                    </div>

					<?php if( 'yes' == $settings['mini_cart_subtotal_show'] ): ?>
						<div class="ha-mini-cart-total">
							<?php echo (( WC()->cart != '' ) ? WC()->cart->get_cart_total() : '' ); ?>
						</div>
					<?php endif; ?>

                </div>

                <?php if( $settings['mini_cart_show'] !== 'none' ): ?>
					<div class="ha-mini-cart-popup">
						<div class="ha-mini-cart-popup-header">
								<div class="ha-mini-cart-popup-count-text-area">
									<span class="ha-mini-cart-popup-count"><?php echo (( WC()->cart != '' ) )?  WC()->cart->get_cart_contents_count() : '' ; ?></span>
									<span class="ha-mini-cart-popup-count-text"><?php esc_html_e( 'items', 'happy-addons-pro' ); ?></span>
								</div>
						</div>
						<div class="ha-mini-cart-popup-body">
							<div class="widget_shopping_cart_content">
								<?php (( WC()->cart != '' ) ? woocommerce_mini_cart() : '' ); ?>
							</div>
						</div>
					</div>
                <?php endif; ?>
            </div>
        </div>

    <?php

	}

	public function show_wc_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'WooCommerce is missing! Please install and activate WooCommerce.', 'happy-addons-pro' )
				);
		}
	}

}
