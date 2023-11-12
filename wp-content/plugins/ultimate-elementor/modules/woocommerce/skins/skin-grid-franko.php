<?php
/**
 * UAEL WooCommerce Skin Grid - Classic.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

use UltimateElementor\Modules\Woocommerce\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Grid_Franko
 *
 * @property Products $parent
 */
class Skin_Grid_Franko extends Skin_Grid_Base {

	/**
	 * Get ID.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function get_id() {
		return 'grid-franko';
	}

	/**
	 * Get title.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function get_title() {
		return __( 'Modern', 'uael' );
	}

	/**
	 * Register control actions.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		parent::_register_controls_actions();

		/* Content Controls */
		add_action( 'elementor/element/uael-woo-products/section_filter_field/after_section_end', array( $this, 'register_content_content_controls' ) );
		add_action( 'elementor/element/uael-woo-products/section_design_image/after_section_end', array( $this, 'register_style_content_controls' ) );

		/* Flash Notification Controls */
		add_action( 'elementor/element/uael-woo-products/section_filter_field/after_section_end', array( $this, 'register_content_sale_controls' ) );
		add_action( 'elementor/element/uael-woo-products/section_design_image/after_section_end', array( $this, 'register_style_sale_controls' ) );

		add_action( 'elementor/element/uael-woo-products/section_filter_field/after_section_end', array( $this, 'register_content_featured_controls' ) );
		add_action( 'elementor/element/uael-woo-products/section_design_image/after_section_end', array( $this, 'register_style_featured_controls' ) );
	}

	/**
	 * Register Quick View Controls.
	 *
	 * @since 0.0.1
	 * @param Widget_Base $widget widget object.
	 * @access public
	 */
	public function register_quick_view_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		parent::register_quick_view_controls( $widget );

		/* Update Quick View Control */
		$this->update_control(
			'quick_view_type',
			array(
				'default' => 'show',
				'options' => array(
					''      => __( 'None', 'uael' ),
					'show'  => __( 'On Icon Click', 'uael' ),
					'image' => __( 'On Image Click', 'uael' ),
				),
			)
		);
	}

	/**
	 * Register content control section.
	 *
	 * @since 0.0.1
	 * @param Widget_Base $widget widget object.
	 * @access public
	 */
	public function register_content_content_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_field',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'show_image',
				array(
					'label'        => __( 'Image', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_title',
				array(
					'label'        => __( 'Title', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_ratings',
				array(
					'label'        => __( 'Ratings', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_price',
				array(
					'label'        => __( 'Price', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_short_desc',
				array(
					'label'        => __( 'Short Description', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$this->add_control(
				'show_add_cart',
				array(
					'label'        => __( 'Add to Cart', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_category',
				array(
					'label'        => __( 'Category', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Content Controls.
	 *
	 * @since 0.0.1
	 * @param Widget_Base $widget widget object.
	 * @access public
	 */
	public function register_style_content_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => true,
				'options'      => array(
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
				'default'      => 'left',
				'prefix_class' => 'uael-woo%s--align-',
			)
		);

		$this->add_responsive_control(
			'product_content_padding',
			array(
				'label'      => __( 'Spacing Around Content', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-products-summary-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'product_content_bg_color',
			array(
				'label'     => __( 'Content Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-product-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_category_style',
			array(
				'label'     => __( 'Category', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_category' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_category_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-woo-product-category' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_category' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'product_category_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .uael-woocommerce .uael-woo-product-category',
				'condition' => array(
					$this->get_control_id( 'show_category' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_category_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-woo-product-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_category' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_title_style',
			array(
				'label'     => __( 'Title', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_title_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-loop-product__link, {{WRAPPER}} .uael-woocommerce .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-loop-product__link:hover .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'product_title_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .uael-woocommerce .uael-loop-product__link, {{WRAPPER}} .uael-woocommerce .woocommerce-loop-product__title',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_title_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .woocommerce-loop-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_rating_style',
			array(
				'label'     => __( 'Rating', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_ratings' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_rating_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .star-rating, {{WRAPPER}} .uael-woocommerce .star-rating::before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_ratings' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_rating_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_ratings' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_price_style',
			array(
				'label'     => __( 'Price', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_price' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_price_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce li.product .price' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_price' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'product_price_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .uael-woocommerce li.product .price',
				'condition' => array(
					$this->get_control_id( 'show_price' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_price_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce li.product .price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_price' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_short_desc_style',
			array(
				'label'     => __( 'Short Description', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_short_desc' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_short_desc_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-woo-products-description' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_short_desc' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'product_short_desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .uael-woocommerce .uael-woo-products-description',
				'condition' => array(
					$this->get_control_id( 'show_short_desc' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_short_desc_spacing',
			array(
				'label'     => __( 'Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woocommerce .uael-woo-products-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_short_desc' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'product_add_cart_style',
			array(
				'label'     => __( 'Product Actions', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'product_add_cart_tabs_style' );

			$this->start_controls_tab(
				'product_add_cart_normal',
				array(
					'label' => __( 'Normal', 'uael' ),
				)
			);

				$this->add_control(
					'product_add_cart_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .uael-product-actions .uael-action-item' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'product_add_cart_background_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-product-actions .uael-action-item-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'product_add_cart_hover',
				array(
					'label' => __( 'Hover', 'uael' ),
				)
			);

				$this->add_control(
					'product_add_cart_hover_color',
					array(
						'label'     => __( 'Text Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-product-actions .uael-action-item-wrap:hover .uael-action-item' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'product_add_cart_background_hover_color',
					array(
						'label'     => __( 'Background Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-product-actions .uael-action-item-wrap:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register pagination control section.
	 *
	 * @since 0.0.1
	 * @param Widget_Base $widget widget object.
	 * @access protected
	 */
	public function register_content_sale_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_sale',
			array(
				'label' => __( 'Sale Flash', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'show_sale',
				array(
					'label'        => __( 'Flash', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'sale_flash_content',
				array(
					'label'     => __( 'Flash Content', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						''       => __( 'Default', 'uael' ),
						'custom' => __( 'Custom', 'uael' ),
					),
					'default'   => '',
					'condition' => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'sale_flash_custom_string',
				array(
					'label'       => __( 'Flash String', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '[value]%',
					'description' => __( 'Show Sale % Value ( [value] Autocalculated offer value will replace this ).', 'uael' ),
					'condition'   => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
						$this->get_control_id( 'sale_flash_content' ) => 'custom',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Sale style Controls.
	 *
	 * @since 0.0.1
	 * @param Widget_Base $widget widget object.
	 * @access public
	 */
	public function register_style_sale_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_design_sale',
			array(
				'label'     => __( 'Sale Flash', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_sale' ) => 'yes',
				),
			)
		);
			$this->add_control(
				'sale_flash_style',
				array(
					'label'        => __( 'Flash Style', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'circle' => __( 'Circle', 'uael' ),
						'square' => __( 'Square', 'uael' ),
						'custom' => __( 'Custom', 'uael' ),
					),
					'default'      => 'custom',
					'condition'    => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
					'prefix_class' => 'uael-sale-flash-',
				)
			);
			$this->add_responsive_control(
				'sale_flash_size',
				array(
					'label'      => __( 'Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => 20,
							'max' => 200,
						),
						'em' => array(
							'min' => 1,
							'max' => 10,
						),
					),
					'default'    => array(
						'size' => 2,
						'unit' => 'em',
					),
					'condition'  => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sale-flash-wrap .uael-onsale' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',

					),
				)
			);
			$this->add_responsive_control(
				'sale_flash_radius',
				array(
					'label'      => __( 'Rounded Corners', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
						'unit'   => 'px',
					),
					'condition'  => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
						$this->get_control_id( 'sale_flash_style' ) => 'custom',

					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sale-flash-wrap .uael-onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
			$this->add_responsive_control(
				'sale_flash_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sale-flash-wrap .uael-onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'default'    => array(
						'top'      => '2',
						'bottom'   => '2',
						'left'     => '10',
						'right'    => '10',
						'unit'     => 'px',
						'isLinked' => false,
					),
					'condition'  => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
						$this->get_control_id( 'sale_flash_style' ) => 'custom',
					),
				)
			);
			$this->add_responsive_control(
				'sale_flash_margin',
				array(
					'label'      => __( 'Margin', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '10',
						'bottom' => '10',
						'left'   => '10',
						'right'  => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sale-flash-wrap .uael-onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
				)
			);
			$this->add_control(
				'sale_flash_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-woocommerce .uael-onsale' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'sale_flash_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-woocommerce .uael-onsale' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'sale_flash_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '{{WRAPPER}} .uael-woocommerce .uael-onsale',
					'condition' => array(
						$this->get_control_id( 'show_sale' ) => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}

		/**
		 * Featured flash content controls.
		 *
		 * @since 1.27.1
		 * @param Widget_Base $widget widget object.
		 * @access public
		 */
	public function register_content_featured_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_featured',
			array(
				'label' => __( 'Featured Flash', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'show_featured',
				array(
					'label'        => __( 'Flash', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'featured_flash_string',
				array(
					'label'     => __( 'Flash Content', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'New', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Style Flash Controls.
	 *
	 * @since 1.27.1
	 * @param Widget_Base $widget widget object.
	 * @access public
	 */
	public function register_style_featured_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_design_flash_notification',
			array(
				'label'     => __( 'Featured Flash', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_featured' ) => 'yes',
				),
			)
		);
			$this->add_control(
				'featured_flash_style',
				array(
					'label'        => __( 'Flash Style', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'circle' => __( 'Circle', 'uael' ),
						'square' => __( 'Square', 'uael' ),
						'custom' => __( 'Custom', 'uael' ),
					),
					'default'      => 'custom',
					'condition'    => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
					'prefix_class' => 'uael-featured-flash-',
				)
			);
			$this->add_responsive_control(
				'featured_flash_size',
				array(
					'label'      => __( 'Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => 20,
							'max' => 200,
						),
						'em' => array(
							'min' => 1,
							'max' => 10,
						),
					),
					'default'    => array(
						'size' => 2,
						'unit' => 'em',
					),
					'condition'  => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-featured' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',

					),
				)
			);
			$this->add_responsive_control(
				'featured_flash_radius',
				array(
					'label'      => __( 'Rounded Corners', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
						'unit'   => 'px',
					),
					'condition'  => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
						$this->get_control_id( 'featured_flash_style' ) => 'custom',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-featured' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
			$this->add_responsive_control(
				'featured_flash_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-featured' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'default'    => array(
						'top'      => '2',
						'bottom'   => '2',
						'left'     => '10',
						'right'    => '10',
						'unit'     => 'px',
						'isLinked' => false,
					),
					'condition'  => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
						$this->get_control_id( 'featured_flash_style' ) => 'custom',
					),
				)
			);
			$this->add_responsive_control(
				'featured_flash_margin',
				array(
					'label'      => __( 'Margin', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '10',
						'bottom' => '10',
						'left'   => '10',
						'right'  => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-featured-flash-wrap .uael-featured' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
				)
			);
			$this->add_control(
				'featured_flash_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-woocommerce .uael-featured' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'featured_flash_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-woocommerce .uael-featured' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'featured_flash_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '{{WRAPPER}} .uael-woocommerce .uael-featured',
					'condition' => array(
						$this->get_control_id( 'show_featured' ) => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}
	/**
	 * Render Main HTML.
	 *
	 * @since 1.5.0
	 * @access protected
	 */
	public function render() {

		$settings = $this->parent->get_settings();

		$skin = Skin_Init::get_instance( $this->get_id() );

		echo wp_kses_post( sanitize_text_field( $skin->render( $this->get_id(), $settings, $this->parent->get_id() ) ) );
	}
}
