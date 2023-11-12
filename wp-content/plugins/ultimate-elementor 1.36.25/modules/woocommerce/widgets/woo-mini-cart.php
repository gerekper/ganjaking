<?php
/**
 * UAEL Mini Cart.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Widgets;

use UltimateElementor\Base\Common_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Mini_Cart.
 */
class Woo_Mini_Cart extends Common_Widget {

	/**
	 * Retrieve Mini Cart Widget name.
	 *
	 * @since 1.29.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Woo_Mini_Cart' );
	}

	/**
	 * Retrieve Mini Cart Widget title.
	 *
	 * @since 1.29.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Mini_Cart' );
	}

	/**
	 * Retrieve Mini Cart Widget icon.
	 *
	 * @since 1.29.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Mini_Cart' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.29.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Mini_Cart' );
	}

	/**
	 * Retrieve the list of scripts the widget depends on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.29.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'uael-woocommerce',
		);
	}

	/**
	 * Register Mini Cart controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		/* General cart controls */
		$this->register_content_general_controls();

		/* Cart button style controls */
		$this->register_style_cart_button_controls();

		/* Cart style controls */
		$this->register_style_cart_controls();

		/* Cart inner items controls */
		$this->register_product_style_controls();

		/* Cart checkout buttons style controls */
		$this->register_style_view_cart_checkout_buttons_controls();

		/* Cart doc */
		$this->register_helpful_information();
	}

	/**
	 * Register Mini Cart general controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_content_general_controls() {
		$this->start_controls_section(
			'content_section_button',
			array(
				'label' => __( 'Cart Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'cart_btn_style',
			array(
				'label'   => __( 'Style', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon-text',
				'options' => array(
					'icon-text' => __( 'Icon + Text', 'uael' ),
					'icon'      => __( 'Icon', 'uael' ),
					'text'      => __( 'Text', 'uael' ),
				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'solid',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'cart_button_text',
			array(
				'label'     => __( 'Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Cart', 'uael' ),
				'condition' => array(
					'cart_btn_style' => array( 'text', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'icon_placement',
			array(
				'label'     => __( 'Icon Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'after'  => __( 'After', 'uael' ),
					'before' => __( 'Before', 'uael' ),
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'show_subtotal',
			array(
				'label'        => __( 'Show Subtotal', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-mc-btn__show-subtotal-',
				'condition'    => array(
					'cart_btn_style!' => 'icon',
				),
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => __( 'Show Badge', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'cart_btn_style!' => 'text',
				),
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'hide_empty_badge',
			array(
				'label'        => __( 'Hide Empty Badge', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'prefix_class' => 'uael-mc__btn-badge-empty-hide-',
				'condition'    => array(
					'show_badge'      => 'yes',
					'cart_btn_style!' => 'text',
				),
			)
		);

		$this->add_control(
			'badge_style',
			array(
				'label'     => __( 'Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '100%',
				'options'   => array(
					'100%' => __( 'Circle', 'uael' ),
					'0'    => __( 'Square', 'uael' ),
				),
				'condition' => array(
					'show_badge'      => 'yes',
					'cart_btn_style!' => 'text',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-badge' => 'border-radius: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-dropdown__header-badge' => 'border-radius: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__header-badge' => 'border-radius: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-badge' => 'border-radius: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_placement',
			array(
				'label'     => __( 'Position', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'    => __( 'Top', 'uael' ),
					'inline' => __( 'Inline', 'uael' ),
				),
				'condition' => array(
					'show_badge'      => 'yes',
					'cart_btn_style!' => 'text',
				),
			)
		);

		$this->add_control(
			'cart_btn_align',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'flex-start' => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'      => 'left',
				'toggle'       => true,
				'selectors'    => array(
					'{{WRAPPER}} .uael-mc' => 'justify-content: {{VALUE}};',
				),
				'separator'    => 'before',
				'prefix_class' => 'uael-mc-dropdown-',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_section_cart',
			array(
				'label' => __( 'Cart', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_cart',
			array(
				'label'        => __( 'Preview Cart', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-mini-cart--preview-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'in_cart_icon',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'solid',
				),
				'condition' => array(
					'cart_btn_style' => array( 'text' ),
				),
			)
		);

		$this->add_control(
			'cart_style',
			array(
				'label'   => __( 'Style', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dropdown',
				'options' => array(
					'dropdown'  => __( 'Dropdown', 'uael' ),
					'modal'     => __( 'Modal', 'uael' ),
					'offcanvas' => __( 'Off Canvas', 'uael' ),
				),
			)
		);

		$this->add_control(
			'offcanvas_position',
			array(
				'label'        => __( 'Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'right',
				'options'      => array(
					'right' => __( 'Right', 'uael' ),
					'left'  => __( 'Left', 'uael' ),
				),
				'condition'    => array(
					'cart_style' => 'offcanvas',
				),
				'prefix_class' => 'uael-mini-cart-offcanvas-pos-',
			)
		);

		$this->add_control(
			'cart_open_style',
			array(
				'label'     => __( 'Show On', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover',
				'options'   => array(
					'hover' => __( 'Hover', 'uael' ),
					'click' => __( 'Click', 'uael' ),
				),
				'condition' => array(
					'cart_style' => 'dropdown',
				),
			)
		);

		$this->add_control(
			'cart_title',
			array(
				'label' => __( 'Title', 'uael' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'cart_message',
			array(
				'label' => __( 'Message', 'uael' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Mini Cart cart style controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_style_cart_controls() {
		$this->start_controls_section(
			'style_section_cart',
			array(
				'label' => __( 'Cart Extras', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'cart_width',
			array(
				'label'      => __( 'Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => '',
				),
				'range'      => array(
					'px' => array(
						'min' => 150,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown'  => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal'     => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cart_height',
			array(
				'label'      => __( 'Height', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => '450',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 150,
						'max' => 800,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-modal' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_style' => 'modal',
				),
			)
		);

		$this->add_responsive_control(
			'cart_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal'     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cart_background',
				'label'    => __( 'Cart Background', 'uael' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .uael-mc-dropdown, {{WRAPPER}} .uael-mc-modal, {{WRAPPER}} .uael-mc-offcanvas',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cart_border',
				'label'    => __( 'Cart Border', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-mc-dropdown, {{WRAPPER}} .uael-mc-modal, {{WRAPPER}} .uael-mc-offcanvas',
			)
		);

		$this->add_control(
			'cart_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'cart_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .uael-mc-dropdown, {{WRAPPER}} .uael-mc-modal, {{WRAPPER}} .uael-mc-offcanvas',
			)
		);

		$this->add_control(
			'cart_title_styles',
			array(
				'label'     => __( 'Cart Title', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_control(
			'cart_title_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__title > p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__title > p'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__title > p' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_control(
			'cart_title_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__title' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__title'    => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__title' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cart_title_typography',
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-mc-dropdown__title, {{WRAPPER}} .uael-mc-modal__title, {{WRAPPER}} .uael-mc-offcanvas__title',
				'condition' => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'cart_title_text_align',
			array(
				'label'     => __( 'Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__title'    => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__title' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'cart_title_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__title'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_title!' => '',
				),
			)
		);

		$this->add_control(
			'container_cart_button_icon_styles',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text', 'text' ),
				),
			)
		);

		$this->add_control(
			'container_cart_button_icon_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-modal__header-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-offcanvas__header-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text', 'text' ),
				),
			)
		);

		$this->add_responsive_control(
			'container_cart_button_icon_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'size' => '20',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__header-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_btn_style'         => array( 'icon', 'icon-text', 'text' ),
					'in_cart_icon[library]!' => 'svg',
				),
			)
		);

		$this->add_responsive_control(
			'container_cart_button_svg_icon_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'size' => '20',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-icon > svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__header-icon > svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-icon > svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_btn_style'        => array( 'icon', 'icon-text', 'text' ),
					'in_cart_icon[library]' => 'svg',
				),
			)
		);

		$this->add_control(
			'container_cart_button_badge_styles',
			array(
				'label'     => __( 'Badge', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'container_cart_button_badge_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 6,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'unit' => 'em',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-badge' => 'font-size: calc(10px + {{SIZE}}{{UNIT}})',
					'{{WRAPPER}} .uael-mc-modal__header-badge' => 'font-size: calc(10px + {{SIZE}}{{UNIT}})',
					'{{WRAPPER}} .uael-mc-offcanvas__header-badge' => 'font-size: calc(10px + {{SIZE}}{{UNIT}})',
				),
				'condition'  => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'container_cart_button_badge_gap',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '2',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-badge' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__header-badge' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-badge' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_cart_button_badge_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-badge' => 'color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-modal__header-badge' => 'color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-offcanvas__header-badge' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_cart_button_badge_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-badge' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-modal__header-badge' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .uael-mc-offcanvas__header-badge' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_control(
			'cart_subtotal_styles',
			array(
				'label'     => __( 'Subtotal', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_subtotal_typography',
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-mc-dropdown__header-text, {{WRAPPER}} .uael-mc-modal__header-text, {{WRAPPER}} .uael-mc-offcanvas__header-text',
			)
		);

		$this->add_responsive_control(
			'cart_subtotal_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__header-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cart_subtotal_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__header-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_subtotal_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-text' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__header-text' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-text' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'cart_subtotal_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '',
				'selector'    => '{{WRAPPER}} .uael-mc-dropdown__header-text, {{WRAPPER}} .uael-mc-modal__header-text, {{WRAPPER}} .uael-mc-offcanvas__header-text',
			)
		);

		$this->add_control(
			'cart_subtotal_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__header-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__header-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__header-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				),
			)
		);

		$this->add_control(
			'cart_message_styles',
			array(
				'label'     => __( 'Cart Message', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cart_message_typography',
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-mc-dropdown__message, {{WRAPPER}} .uael-mc-modal__message, {{WRAPPER}} .uael-mc-offcanvas__message',
				'condition' => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'cart_message_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown__message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal__message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas__message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_control(
			'cart_message_color',
			array(
				'label'     => __( 'Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__message' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__message' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__message' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_control(
			'cart_message_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__message' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__message' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__message' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'cart_message_text_align',
			array(
				'label'     => __( 'Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown__message' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal__message' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas__message' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'cart_message!' => '',
				),
			)
		);

		$this->add_control(
			'empty_cart_message_styles',
			array(
				'label'     => __( 'Empty Cart Message', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'empty_cart_message_typography',
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__empty-message',
			)
		);

		$this->add_control(
			'empty_cart_message_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__empty-message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'empty_cart_message_text_align',
			array(
				'label'     => __( 'Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__empty-message'   => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'container_cart_backdrop_styles',
			array(
				'label'     => __( 'Overlay', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_style' => array( 'modal', 'offcanvas' ),
				),
			)
		);

		$this->add_control(
			'container_cart_modal_backdrop_bg_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => 'rgba(0,0,0,0.4)',
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-modal-wrap' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'cart_style' => 'modal',
				),
			)
		);

		$this->add_control(
			'container_cart_offcanvas_backdrop_bg_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => 'rgba(0,0,0,0.75)',
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-offcanvas-wrap' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'cart_style' => 'offcanvas',
				),
			)
		);

		$this->add_control(
			'container_cart_close_o_btn_styles',
			array(
				'label'     => __( 'Close Icon', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_style' => array( 'modal', 'offcanvas' ),
				),
			)
		);

		$this->add_control(
			'container_cart_close_o_btn_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .uael-close-o' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_style' => array( 'modal', 'offcanvas' ),
				),
			)
		);

		$this->add_control(
			'container_cart_close_o_btn_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .uael-close-o' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'cart_style' => array( 'modal', 'offcanvas' ),
				),
			)
		);

		$this->add_control(
			'container_cart_close_o_btn_border',
			array(
				'label'     => __( 'Border Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => __( 'Solid', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'none'   => __( 'None', 'uael' ),
				),
				'default'   => 'solid',
				'selectors' => array(
					'{{WRAPPER}} .uael-close-o' => 'border-style: {{VALUE}}',
				),
				'condition' => array(
					'cart_style' => array( 'modal', 'offcanvas' ),
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Mini Cart cart button style controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_style_cart_button_controls() {
		$this->start_controls_section(
			'style_section_cart_button',
			array(
				'label' => __( 'Cart Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cart_button_display_position',
			array(
				'label'        => __( 'Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'inline',
				'options'      => array(
					'inline'   => __( 'Inline', 'uael' ),
					'floating' => __( 'Floating', 'uael' ),
				),
				'condition'    => array(
					'cart_style!' => 'dropdown',
				),
				'prefix_class' => 'uael-mini-cart-align-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'cart_button_display_position_align',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'left',
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'       => false,
				'label_block'  => false,
				'condition'    => array(
					'cart_style!'                  => 'dropdown',
					'cart_button_display_position' => 'floating',
				),
				'prefix_class' => 'uael-mini-cart-align-floating-pos-',
			)
		);

		$this->add_responsive_control(
			'uael_display_floating_on_window_position',
			array(
				'label'          => __( 'Vertical Floating Position', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => '%',
				'default'        => array(
					'size' => '50',
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => '50',
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => '50',
					'unit' => '%',
				),
				'range'          => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}}.uael-mini-cart-align-floating .uael-mc' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'cart_style!'                  => 'dropdown',
					'cart_button_display_position' => 'floating',
				),
			)
		);

		$this->add_responsive_control(
			'uael_display_floating_on_window_horizontal_position',
			array(
				'label'          => __( 'Horizontal Floating Position', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => '%',
				'default'        => array(
					'size' => '0',
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => '0',
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => '0',
					'unit' => '%',
				),
				'range'          => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}}.uael-mini-cart-align-floating .uael-mc'
					=> 'left: {{SIZE}}{{UNIT}}; right: unset;',
					'{{WRAPPER}}.uael-mini-cart-align-floating.uael-mini-cart-align-floating-pos-right .uael-mc' => 'right: {{SIZE}}{{UNIT}}; left: unset;',
				),
				'condition'      => array(
					'cart_style!'                  => 'dropdown',
					'cart_button_display_position' => 'floating',
				),
				'separator'      => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cart_button_typography',
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-mc__btn-text, {{WRAPPER}} .uael-mc__btn-text .woocommerce-Price-amount.amount',
				'condition' => array(
					'cart_btn_style' => array( 'text', 'icon-text' ),
				),
			)
		);

		$this->add_responsive_control(
			'container_cart_button_text_gap',
			array(
				'label'     => __( 'Text Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '2',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-inner-text' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon-text', 'text' ),
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_padding_normal',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cart_button_tabs' );

		$this->start_controls_tab(
			'cart_button_tab_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'cart_button_color_normal',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc a .uael-mc__btn-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'text', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'cart_button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'cart_button_border_normal',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '',
				'selector'    => '{{WRAPPER}} .uael-mc__btn',
			)
		);

		$this->add_control(
			'cart_button_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc__btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cart_button_box_shadow_normal',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-mc__btn',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_button_tab_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'cart_button_color_hover',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc:hover a .uael-mc__btn-text' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'text', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'cart_button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cart_button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cart_button_box_shadow_hover',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-mc__btn:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_button_icon_styles',
			array(
				'label'     => __( 'Icon', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_icon_size_normal',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'size' => '20',
					'unit' => 'px',
				),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 100,
					),
					'em'  => array(
						'min' => 0,
						'max' => 20,
					),
					'rem' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc__btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'icon[library]!' => 'svg',
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_svg_icon_size_normal',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'size' => '20',
					'unit' => 'px',
				),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 100,
					),
					'em'  => array(
						'min' => 0,
						'max' => 20,
					),
					'rem' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc__btn-icon > svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'icon[library]'  => 'svg',
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_icon_right_spacing_normal',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '5',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-text' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cart_btn_style' => 'icon-text',
					'icon_placement' => 'after',
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_icon_left_spacing_normal',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '5',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cart_btn_style' => 'icon-text',
					'icon_placement' => 'before',
				),
			)
		);

		$this->start_controls_tabs( 'cart_button_icon_tabs' );

		$this->start_controls_tab(
			'cart_button_icon_tab_normal',
			array(
				'label'     => __( 'Normal', 'uael' ),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'cart_button_icon_color_normal',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_button_icon_tab_hover',
			array(
				'label'     => __( 'hover', 'uael' ),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->add_control(
			'cart_button_icon_color_hover',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc:hover .uael-mc__btn-icon' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_button_badge_styles',
			array(
				'label'     => __( 'Badge', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_badge_position',
			array(
				'label'     => __( 'Position', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '0.5',
					'unit' => 'em',
				),
				'range'     => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-badge' => 'top: -{{SIZE}}{{UNIT}}; right: -{{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cart_btn_style'  => array( 'icon', 'icon-text' ),
					'show_badge'      => 'yes',
					'badge_placement' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'cart_button_badge_size_normal',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '10',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc__btn-badge' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'cart_button_badge_tabs' );

		$this->start_controls_tab(
			'cart_button_badge_tab_normal',
			array(
				'label'     => __( 'Normal', 'uael' ),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->add_control(
			'cart_button_badge_color_normal',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-badge' => 'color: {{VALUE}}',
				),
				'default'   => '#ffffff',
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->add_control(
			'cart_button_badge_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '#d9534f',
				'selectors' => array(
					'{{WRAPPER}} .uael-mc__btn-badge' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_button_badge_tab_hover',
			array(
				'label'     => __( 'Hover', 'uael' ),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->add_control(
			'cart_button_badge_color_hover',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc:hover .uael-mc__btn-badge' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->add_control(
			'cart_button_badge_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-mc:hover .uael-mc__btn-badge' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'cart_btn_style' => array( 'icon', 'icon-text' ),
					'show_badge'     => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Mini Cart View Cart and checkout buttons style controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_style_view_cart_checkout_buttons_controls() {
		$this->start_controls_section(
			'style_section_checkout_buttons',
			array(
				'label' => __( 'View Cart & Checkout Buttons', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ck_btn_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a',
			)
		);

		$this->add_control(
			'space_between_buttons',
			array(
				'label'     => __( 'Space Between', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_styles',
			array(
				'label'     => __( 'View Cart Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'ck_btn_view_cart_tabs' );

		$this->start_controls_tab(
			'ck_btn_view_cart_tab_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'ck_btn_view_cart_border_normal',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '',
				'selector'    => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)',
			)
		);

		$this->add_control(
			'ck_btn_view_cart_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ck_btn_view_cart_box_shadow_normal',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ck_btn_view_cart_tab_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ck_btn_view_cart_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ck_btn_view_cart_box_shadow_hover',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ck_btn_checkout_styles',
			array(
				'label'     => __( 'Checkout Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'ck_btn_checkout_tabs' );

		$this->start_controls_tab(
			'ck_btn_checkout_tab_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'ck_btn_checkout_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ck_btn_checkout_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'ck_btn_checkout_border_normal',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '',
				'selector'    => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout',
			)
		);

		$this->add_control(
			'ck_btn_checkout_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ck_btn_checkout_box_shadow_normal',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ck_btn_checkout_tab_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'ck_btn_checkout_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ck_btn_checkout_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ck_btn_checkout_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ck_btn_checkout_box_shadow_hover',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.button.checkout:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Mini Cart cart inner container style controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_product_style_controls() {
		$this->start_controls_section(
			'style_section_cart_inner_container',
			array(
				'label' => __( 'Products', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cart_inner_item_image_style',
			array(
				'label'     => __( 'Image', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cart_inner_item_image_border',
				'label'    => __( 'Border', 'uael' ),
				'selector' => '{{WRAPPER}} img.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail, {{WRAPPER}} .uael-mc img.woocommerce-placeholder.wp-post-image',
			)
		);

		$this->add_control(
			'cart_inner_item_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} img.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail, {{WRAPPER}} .uael-mc img.woocommerce-placeholder.wp-post-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cart_inner_item_image_box_shadow',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} img.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail, {{WRAPPER}} .uael-mc img.woocommerce-placeholder.wp-post-image',
			)
		);

		$this->add_control(
			'cart_inner_item_name_styles',
			array(
				'label'     => __( 'Title', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_inner_item_name_typography',
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} li.woocommerce-mini-cart-item.mini_cart_item > a:nth-child(2)',
			)
		);

		$this->add_control(
			'cart_inner_item_name_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} li.woocommerce-mini-cart-item.mini_cart_item > a:nth-child(2)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_inner_item_price_styles',
			array(
				'label'     => __( 'Quantity & Price', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_inner_item_price_typography',
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart-item.mini_cart_item span.quantity',
			)
		);

		$this->add_control(
			'cart_inner_item_price_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart-item.mini_cart_item span.quantity' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_inner_remove_icon_styles',
			array(
				'label'     => __( 'Remove Product Icon', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cart_inner_remove_icon_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 35,
					),
				),
				'default'    => array(
					'size' => '',
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} ul.woocommerce-mini-cart.cart_list.product_list_widget li a.remove.remove_from_cart_button' => 'width: calc({{SIZE}}{{UNIT}} + 24px); height: calc({{SIZE}}{{UNIT}} + 24px); font-size: calc({{SIZE}}{{UNIT}} + 18px)',
				),
			)
		);

		$this->add_control(
			'cart_inner_remove_icon_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.woocommerce-mini-cart.cart_list.product_list_widget li a.remove.remove_from_cart_button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cart_inner_remove_icon_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.woocommerce-mini-cart.cart_list.product_list_widget li a.remove.remove_from_cart_button' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cart_inner_remove_icon_border',
				'label'    => __( 'Border', 'uael' ),
				'selector' => '{{WRAPPER}} ul.woocommerce-mini-cart.cart_list.product_list_widget li a.remove.remove_from_cart_button',
			)
		);

		$this->add_control(
			'cart_inner_subtotal_styles',
			array(
				'label'     => __( 'Subtotal', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cart_inner_subtotal_typography',
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total, {{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total, {{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total',
			)
		);

		$this->add_responsive_control(
			'cart_inner_subtotal_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cart_inner_subtotal_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_inner_subtotal_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'cart_inner_subtotal_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '',
				'selector'    => '{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total, {{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total, {{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total',
			)
		);

		$this->add_control(
			'cart_inner_subtotal_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-mc-dropdown .woocommerce-mini-cart__total.total' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-modal .woocommerce-mini-cart__total.total' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .uael-mc-offcanvas .woocommerce-mini-cart__total.total' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				),
			)
		);

		$this->add_control(
			'heading_product_divider_style',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Divider', 'uael' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_divider',
			array(
				'label'        => __( 'Show Divider', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-mini-cart--show-divider-',
			)
		);

		$this->add_control(
			'divider_style',
			array(
				'label'     => __( 'Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
					'groove' => __( 'Groove', 'uael' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart-item, {{WRAPPER}} .woocommerce-mini-cart__total' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-mini-cart__total' => 'border-top-style: {{VALUE}}',
				),
				'condition' => array(
					'show_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'divider_width',
			array(
				'label'     => __( 'Weight', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart-item, {{WRAPPER}} .woocommerce-mini-cart-items, {{WRAPPER}} .woocommerce-mini-cart__total' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .woocommerce-mini-cart__total' => 'border-top-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'show_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart-item, {{WRAPPER}} .woocommerce-mini-cart__total' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'show_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'divider_gap',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-mini-cart-item, {{WRAPPER}} .woocommerce-mini-cart__total' => 'padding-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .woocommerce-mini-cart-item:not(:first-of-type), {{WRAPPER}} .elementor-menu-cart__footer-buttons, {{WRAPPER}} .woocommerce-mini-cart__total' => 'padding-top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'show_divider' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

		/**
		 * Helpful Information.
		 *
		 * @since 1.1.0
		 * @access protected
		 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/woo-mini-cart/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}


	/**
	 * Get Modal and Off Canvas cart markup.
	 *
	 * @since 1.29.0
	 * @access public
	 * @param string $style Cart Style.
	 * @param array  $settings Widget settings.
	 * @param string $cart_count Cart content count.
	 * @param string $cart_subtotal Cart subtotal.
	 */
	public function get_modal_offcanvas_markup( $style, $settings, $cart_count, $cart_subtotal ) {
		?>
		<div class="uael-mc-<?php echo esc_attr( $style ); ?>-wrap uael-mc-<?php echo esc_attr( $style ); ?>-wrap-close"></div>
		<div class="uael-mc-<?php echo esc_attr( $style ); ?> uael-mc-<?php echo esc_attr( $style ); ?>-close">
			<div class="uael-mc-<?php echo esc_attr( $style ); ?>__close-btn">
				<i class="uael-close-o"></i>
			</div>
			<?php if ( ! empty( $settings['cart_title'] ) ) { ?>
				<div class="uael-mc-<?php echo esc_attr( $style ); ?>__title">
					<p><?php echo esc_html( $settings['cart_title'] ); ?></p>
				</div>
			<?php } ?>
			<div class="uael-mc-<?php echo esc_attr( $style ); ?>__header">
				<div class="uael-mc-<?php echo esc_attr( $style ); ?>__icon-wrap">
					<div class="uael-mc-dropdown__header-icon">
						<?php
						if ( isset( $settings['in_cart_icon']['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $settings['in_cart_icon'], array( 'aria-hidden' => 'true' ) );
						} else {
							\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
						}
						?>
					</div>
					<div class="uael-mc-<?php echo esc_attr( $style ); ?>__header-badge">
						<?php echo esc_html( $cart_count ); ?>
					</div>
				</div>
				<span class="uael-mc-<?php echo esc_attr( $style ); ?>__header-text">
					<?php
					esc_attr_e( 'Subtotal: ', 'uael' );
					echo wp_kses_post( $cart_subtotal );
					?>
				</span>
			</div>
			<div class="uael-mc-<?php echo esc_attr( $style ); ?>__items">
				<?php echo esc_html( woocommerce_mini_cart() ); ?>
			</div>
			<?php if ( ! empty( $settings['cart_message'] ) ) { ?>
				<div class="uael-mc-<?php echo esc_attr( $style ); ?>__message">
					<?php echo esc_html( $settings['cart_message'] ); ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Mini Cart output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function render() {

		if ( null === WC()->cart ) {
			return;
		}

		$settings  = $this->get_settings_for_display();
		$id        = $this->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		$cart_count    = WC()->cart->get_cart_contents_count();
		$cart_subtotal = WC()->cart->get_cart_subtotal();

		$this->add_render_attribute(
			'cart_btn_behaviour',
			array(
				'data-behaviour' => $settings['cart_open_style'],
			)
		);

		if ( ( 'floating' === $settings['cart_button_display_position'] ) && $is_editor ) {
			?>
			<div class="uael-builder-msg" style="text-align: center;">
				<h5><?php esc_html_e( 'Woo - Mini Cart - ID ', 'uael' ); ?><?php echo esc_attr( $id ); ?></h5>
				<p><?php esc_html_e( 'Click here to edit the "Woo - Mini Cart" settings. This text will not be visible on frontend.', 'uael' ); ?></p>
			</div>
			<?php
		}
		?>
		<div class="uael-mc" data-cart_dropdown="<?php echo esc_attr( $settings['cart_style'] ); ?>">
			<a href="#" class="uael-mc__btn" id="uael-mc__btn" <?php echo wp_kses_post( $this->get_render_attribute_string( 'cart_btn_behaviour' ) ); ?>>
				<?php
				if ( 'text' === $settings['cart_btn_style'] || 'icon-text' === $settings['cart_btn_style'] ) {
					?>
					<span class="uael-mc__btn-text">
						<span class="uael-mc__btn-inner-text">
							<?php echo esc_html( $settings['cart_button_text'] ); ?>
						</span>
						<span class="uael-mc__btn-subtotal">
							<?php echo wp_kses_post( $cart_subtotal ); ?>
						</span>
					</span>
					<?php
				}
				?>
				<?php
				if ( 'icon' === $settings['cart_btn_style'] || 'icon-text' === $settings['cart_btn_style'] ) {
					?>
					<div class="uael-mc__btn-icon uael-badge-<?php echo esc_attr( $settings['badge_placement'] ); ?> uael-cart-icon-<?php echo esc_attr( $settings['icon_placement'] ); ?>">
						<?php \Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
						<?php
						if ( 'yes' === $settings['show_badge'] ) {
							?>
							<div class="uael-mc__btn-badge uael-badge-<?php echo esc_attr( $settings['badge_placement'] ); ?>" data-counter="<?php echo esc_attr( $cart_count ); ?>">
								<?php echo esc_html( $cart_count ); ?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</a>
			<?php

			switch ( $settings['cart_style'] ) {
				case 'dropdown':
					?>
					<div class="uael-mc-dropdown uael-mc-dropdown-close">
						<?php if ( ! empty( $settings['cart_title'] ) ) { ?>
							<div class="uael-mc-dropdown__title">
								<p><?php echo esc_html( $settings['cart_title'] ); ?></p>
							</div>
						<?php } ?>
						<div class="uael-mc-dropdown__header">
							<div class="uael-mc-dropdown__icon-wrap">
								<div class="uael-mc-dropdown__header-icon">
								<?php
								if ( isset( $settings['in_cart_icon']['value'] ) ) {
									\Elementor\Icons_Manager::render_icon( $settings['in_cart_icon'], array( 'aria-hidden' => 'true' ) );
								} else {
									\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
								}
								?>
								</div>
								<div class="uael-mc-dropdown__header-badge">
									<?php echo esc_html( $cart_count ); ?>
								</div>
							</div>
							<span class="uael-mc-dropdown__header-text">
							<?php
							esc_attr_e( 'Subtotal: ', 'uael' );
							echo wp_kses_post( $cart_subtotal );
							?>
						</span>
						</div>
						<div class="uael-mc-dropdown__items">
							<?php echo esc_html( woocommerce_mini_cart() ); ?>
						</div>
						<?php if ( ! empty( $settings['cart_message'] ) ) { ?>
							<div class="uael-mc-dropdown__message">
								<?php echo esc_html( $settings['cart_message'] ); ?>
							</div>
						<?php } ?>
					</div>
					<?php
					break;

				case 'modal':
					$this->get_modal_offcanvas_markup( 'modal', $settings, $cart_count, $cart_subtotal );
					break;

				case 'offcanvas':
					$this->get_modal_offcanvas_markup( 'offcanvas', $settings, $cart_count, $cart_subtotal );
					break;
			}
			?>
		</div>
		<?php
	}
}
