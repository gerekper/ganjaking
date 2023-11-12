<?php
/**
 * UAEL WooCommerce Add To Cart Button.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use UltimateElementor\Base\Module_Base;
use Elementor\Modules\DynamicTags\Module as TagsModule;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Add_To_Cart.
 */
class Woo_Add_To_Cart extends Common_Widget {

	/**
	 * Retrieve Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Woo_Add_To_Cart' );
	}

	/**
	 * Retrieve Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Add_To_Cart' );
	}

	/**
	 * Retrieve Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Add_To_Cart' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Add_To_Cart' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array scripts.
	 */
	public function get_script_depends() {
		return array( 'uael-woocommerce' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		/* Product Control */
		$this->register_content_product_controls();
		/* Button Control */
		$this->register_content_button_controls();
		/* Button Style */
		$this->register_style_button_controls();
		$this->register_quantity_style_controls();
		$this->register_variation_style_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Content Product Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_product_controls() {

		$this->start_controls_section(
			'section_product_field',
			array(
				'label' => __( 'Product', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'product_id',
				array(
					'label'     => __( 'Select Product', 'uael' ),
					'type'      => 'uael-query-posts',
					'post_type' => 'product',
					'condition' => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {

			$this->add_control(
				'dynamic_product',
				array(
					'label'        => __( 'Use Dynamic Product', 'uael' ),
					'description'  => __( 'Enable this option to use Add to Cart button on the single product page.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);

			$this->add_control(
				'dynamic_product_id',
				array(
					'label'     => __( 'Select Product', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'dynamic_product'             => 'yes',
						'enable_single_product_page!' => 'yes',
					),
				)
			);

		}

			$this->add_control(
				'enable_single_product_page',
				array(
					'label'        => __( 'Use Default WooCommerce Template', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'Enable this to use the Add To Cart default template by WooCommerce', 'uael' ),
				)
			);

			$this->add_control(
				'quantity',
				array(
					'label'     => __( 'Quantity', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 1,
					'condition' => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);

			$this->add_control(
				'enable_redirect',
				array(
					'label'        => __( 'Auto Redirect', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'Enable this option to redirect cart page after the product gets added to cart', 'uael' ),
					'condition'    => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Content Button Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_button_controls() {
		$this->start_controls_section(
			'section_button_field',
			array(
				'label' => __( 'Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'btn_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Add to cart', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);
			$this->add_responsive_control(
				'align',
				array(
					'label'              => __( 'Alignment', 'uael' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => array(
						'left'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
						'justify' => array(
							'title' => __( 'Justified', 'uael' ),
							'icon'  => 'fa fa-align-justify',
						),
					),
					'prefix_class'       => 'uael-add-to-cart%s-align-',
					'default'            => 'left',
					'frontend_available' => true,
				)
			);
			$this->add_control(
				'btn_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'sm',
					'options'   => array(
						'xs' => __( 'Extra Small', 'uael' ),
						'sm' => __( 'Small', 'uael' ),
						'md' => __( 'Medium', 'uael' ),
						'lg' => __( 'Large', 'uael' ),
						'xl' => __( 'Extra Large', 'uael' ),
					),
					'condition' => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);
			$this->add_responsive_control(
				'btn_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', 'em', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'enable_single_product_page!' => 'yes',
					),
					'frontend_available' => true,
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_btn_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'btn_icon',
					'default'          => array(
						'value'   => 'fa fa-shopping-cart',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);
		} else {
			$this->add_control(
				'btn_icon',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-shopping-cart',
					'condition' => array(
						'enable_single_product_page!' => 'yes',
					),
				)
			);
		}
			$this->add_control(
				'btn_icon_align',
				array(
					'label'      => __( 'Icon Position', 'uael' ),
					'type'       => Controls_Manager::SELECT,
					'default'    => 'left',
					'options'    => array(
						'left'  => __( 'Before', 'uael' ),
						'right' => __( 'After', 'uael' ),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'btn_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'enable_single_product_page',
								'operator' => '!=',
								'value'    => 'yes',
							),
						),
					),
				)
			);
			$this->add_control(
				'btn_icon_indent',
				array(
					'label'      => __( 'Icon Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'btn_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'enable_single_product_page',
								'operator' => '!=',
								'value'    => 'yes',
							),
						),

					),
					'selectors'  => array(
						'{{WRAPPER}} .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Style Button Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_button_controls() {

		$this->start_controls_section(
			'section_design_button',
			array(
				'label' => __( 'Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
			)
		);

		$this->start_controls_tabs( 'button_tabs_style' );

			$this->start_controls_tab(
				'button_normal',
				array(
					'label' => __( 'Normal', 'uael' ),
				)
			);

				$this->add_control(
					'button_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'button_background_color',
						'label'          => __( 'Background Color', 'uael' ),
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button',
						'fields_options' => array(
							'color' => array(
								'global' => array(
									'default' => Global_Colors::COLOR_ACCENT,
								),
							),
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'        => 'button_border',
						'placeholder' => '',
						'default'     => '',
						'selector'    => '{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button',
					)
				);

				$this->add_control(
					'border_radius',
					array(
						'label'      => __( 'Border Radius', 'uael' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors'  => array(
							'{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					'btn_atc_padding',
					array(
						'label'              => __( 'Padding', 'uael' ),
						'type'               => Controls_Manager::DIMENSIONS,
						'size_units'         => array( 'px', 'em', '%' ),
						'selectors'          => array(
							'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart .button.single_add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'condition'          => array(
							'enable_single_product_page' => 'yes',
						),
						'frontend_available' => true,
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'button_box_shadow',
						'selector' => '{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button',
					)
				);

				$this->add_control(
					'view_cart_color',
					array(
						'label'     => __( 'View Cart Text', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .added_to_cart' => 'color: {{VALUE}};',
						),
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'condition' => array(
							'enable_single_product_page!' => 'yes',
						),
					)
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'button_hover',
				array(
					'label' => __( 'Hover', 'uael' ),
				)
			);

				$this->add_control(
					'button_hover_color',
					array(
						'label'     => __( 'Text Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-button:focus, {{WRAPPER}} .uael-button:hover,{{WRAPPER}} .uael-add-to-cart button:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'           => 'button_background_hover_color',
						'label'          => __( 'Background Color', 'uael' ),
						'types'          => array( 'classic', 'gradient' ),
						'selector'       => '{{WRAPPER}} .uael-button:focus, {{WRAPPER}} .uael-button:hover,{{WRAPPER}} .uael-add-to-cart button:hover',
						'fields_options' => array(
							'color' => array(
								'global' => array(
									'default' => Global_Colors::COLOR_ACCENT,
								),
							),
						),
					)
				);

				$this->add_control(
					'button_border_hover_color',
					array(
						'label'     => __( 'Border Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'condition' => array(
							'button_border_border!' => '',
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-button:focus, {{WRAPPER}} .uael-button:hover,{{WRAPPER}} .uael-add-to-cart button:hover' => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'hover_animation',
					array(
						'label'     => __( 'Hover Animation', 'uael' ),
						'type'      => Controls_Manager::HOVER_ANIMATION,
						'condition' => array(
							'enable_single_product_page!' => 'yes',
						),
					)
				);

				$this->add_control(
					'view_cart_hover_color',
					array(
						'label'     => __( 'View Cart Text Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .added_to_cart:hover' => 'color: {{VALUE}};',
						),
						'global'    => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
						'condition' => array(
							'enable_single_product_page!' => 'yes',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'      => 'button_hover_box_shadow',
						'selector'  => '{{WRAPPER}} .uael-button,{{WRAPPER}} .uael-add-to-cart button:hover',
						'condition' => array(
							'enable_single_product_page' => 'yes',
						),
					)
				);
			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Style Button Controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_quantity_style_controls() {
		$this->start_controls_section(
			'section_atc_quantity_style',
			array(
				'label'     => __( 'Quantity', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'enable_single_product_page' => 'yes',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'      => __( 'Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'body:not(.rtl) {{WRAPPER}} .uael-add-to-cart .quantity + .button' => 'margin-left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .uael-add-to-cart .quantity + .button' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'quantity_typography',
				'selector' => '{{WRAPPER}} .uael-add-to-cart .quantity .qty',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'quantity_border',
				'selector' => '{{WRAPPER}} .uael-add-to-cart .quantity .qty',
				'exclude'  => array( 'color' ),
			)
		);

		$this->add_control(
			'quantity_border_radius',
			array(
				'label'     => __( 'Border Radius', 'uael' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'quantity_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'quantity_style_tabs' );

		$this->start_controls_tab(
			'quantity_style_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'quantity_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'enable_single_product_page' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'quantity_box_shadow',
				'selector' => '{{WRAPPER}} .uael-add-to-cart .quantity .qty',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'quantity_style_focus',
			array(
				'label' => __( 'Focus', 'uael' ),
			)
		);

		$this->add_control(
			'quantity_text_color_focus',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_bg_color_focus',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_border_color_focus',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty:focus' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_transition',
			array(
				'label'     => __( 'Transition Duration', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0.2,
				),
				'range'     => array(
					'px' => array(
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-add-to-cart .quantity .qty' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	 * Register Style Button Controls.
	 *
	 * @since 1.29.0
	 * @access protected
	 */
	protected function register_variation_style_controls() {
		$this->start_controls_section(
			'section_atc_variations_style',
			array(
				'label'     => __( 'Variations', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'enable_single_product_page' => 'yes',
				),
			)
		);

		$this->add_control(
			'variations_width',
			array(
				'label'      => __( 'Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-add-to-cart form.cart .variations' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'variations_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart .variations' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'heading_variations_label_style',
			array(
				'label'     => __( 'Label', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variations_label_color_focus',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'variations_label_typography',
				'selector' => '.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations label',
			)
		);

		$this->add_control(
			'heading_variations_select_style',
			array(
				'label'     => __( 'Select field', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variations_select_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value select' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value select' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value select' => 'border: 1px solid {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'variations_select_typography',
				'selector' => '.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value select, .woocommerce div.product.elementor{{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value:before',
			)
		);

		$this->add_control(
			'variations_select_border_radius',
			array(
				'label'     => __( 'Border Radius', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'.woocommerce {{WRAPPER}} .uael-add-to-cart form.cart table.variations td.value select' => 'border-radius: {{SIZE}}{{UNIT}}',
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

		$help_link_1 = UAEL_DOMAIN . 'docs/how-to-add-woocommerce-add-to-cart-button-on-the-page/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
	 * Render Woo Product Grid output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$node_id  = $this->get_id();
		$atc_html = '';
		$product  = false;

		if ( ! empty( $settings['product_id'] ) ) {
			$product_data = get_post( $settings['product_id'] );

		} elseif ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			if ( 'yes' === $settings['dynamic_product'] ) {
				$product_data = get_post( $settings['dynamic_product_id'] );
			}
		}

		$product = ! empty( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ), true ) ? wc_setup_product_data( $product_data ) : false;

		if ( 'yes' === $settings['enable_single_product_page'] ) {

			if ( ! is_product() ) { ?>
				<span class='uael-add-to-cart-error-message'>
				<?php
					echo '<div class="elementor-alert elementor-alert-warning">';
					echo esc_attr__( 'Please enable the option on Single Product Page.', 'uael' );
					echo '</div>';
				?>
				</span>
				<?php
			} else {
				$product = wc_get_product();

				if ( empty( $product ) ) {
					return;
				}

				?>
				<div class="uael-add-to-cart" data-enable-feature="<?php echo wp_kses_post( $settings['enable_single_product_page'] ); ?>">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
				<?php
			}
		} elseif ( $product ) {

			$product_id   = $product->get_id();
			$product_type = $product->get_type();

			$class = array(
				'uael-button',
				'elementor-button',
				'elementor-animation-' . $settings['hover_animation'],
				'elementor-size-' . $settings['btn_size'],
				'product_type_' . $product_type,
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			);

			if ( 'yes' === $settings['enable_redirect'] ) {
				$class[] = 'uael-redirect';
			}

			$this->add_render_attribute(
				'button',
				array(
					'rel'             => 'nofollow',
					'href'            => $product->add_to_cart_url(),
					'data-quantity'   => ( isset( $settings['quantity'] ) ? $settings['quantity'] : 1 ),
					'data-product_id' => $product_id,
					'class'           => $class,
				)
			);

			$this->add_render_attribute(
				'icon-align',
				'class',
				array(
					'uael-atc-icon-align',
					'elementor-align-icon-' . $settings['btn_icon_align'],
				)
			);

			$atc_html     .= '<div class="uael-woo-add-to-cart">';
				$atc_html .= '<a ' . $this->get_render_attribute_string( 'button' ) . '>';
				$atc_html .= '<span class="uael-atc-content-wrapper">';

			if ( UAEL_Helper::is_elementor_updated() ) {
				if ( ! empty( $settings['btn_icon'] ) || ! empty( $settings['new_btn_icon'] ) ) :
					$migrated = isset( $settings['__fa4_migrated']['new_btn_icon'] );
					$is_new   = ! isset( $settings['btn_icon'] );

					$atc_html .= '<span ' . $this->get_render_attribute_string( 'icon-align' ) . '">';
					if ( $is_new || $migrated ) {
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['new_btn_icon'], array( 'aria-hidden' => 'true' ) );
						$atc_html .= ob_get_clean();
					} elseif ( ! empty( $settings['btn_icon'] ) ) {
						$atc_html .= '<i class="' . $settings['btn_icon'] . '" aria-hidden="true"></i>';
					}
					$atc_html .= '</span>';
				endif;
			} elseif ( ! empty( $settings['btn_icon'] ) ) {
				$atc_html     .= '<span ' . $this->get_render_attribute_string( 'icon-align' ) . '">';
					$atc_html .= '<i class="' . $settings['btn_icon'] . '" aria-hidden="true"></i>';
				$atc_html     .= '</span>';
			}

				$atc_html .= '<span class="uael-atc-btn-text">' . $settings['btn_text'] . '</span>';
				$atc_html .= '</span>';
				$atc_html .= '</a>';
				$atc_html .= '</div>';

			echo $atc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( current_user_can( 'manage_options' ) ) {

			$class = implode(
				' ',
				array_filter(
					array(
						'button',
						'uael-button',
						'elementor-animation-' . $settings['hover_animation'],
					)
				)
			);
			$this->add_render_attribute(
				'button',
				array( 'class' => $class )
			);

			$atc_html     .= '<div class="uael-woo-add-to-cart">';
				$atc_html .= '<a ' . $this->get_render_attribute_string( 'button' ) . '>';
				$atc_html .= __( 'Please select the product', 'uael' );
				$atc_html .= '</a>';
			$atc_html     .= '</div>';

			echo $atc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
