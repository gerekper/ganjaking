<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Mini cart widget
 *
 * @since 2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Mini_Cart_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_mini_cart';
	}

	public function get_title() {
		return __( 'Mini Cart', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'minicart', 'shopping', 'cart', 'woocommerce' );
	}

	public function get_icon() {
		return 'porto-icon-shopping-cart';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-mini-cart-element/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_hb_mini_cart',
			array(
				'label' => __( 'Mini Cart', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'description_cart',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Options -> Header -> WooCommerce%2$s panel.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'type',
				array(
					'type'        => Controls_Manager::SELECT,
					'label'       => __( 'Mini Cart Type', 'porto-functionality' ),
					'description' => __( 'Controls the cart type.', 'porto-functionality' ),
					'options'     => array(
						'none'               => __( 'None', 'porto-functionality' ),
						'simple'             => __( 'Simple', 'porto-functionality' ),
						'minicart-arrow-alt' => __( 'Arrow Alt', 'porto-functionality' ),
						'minicart-inline'    => __( 'Text', 'porto-functionality' ),
						'minicart-text'      => __( 'Icon & Text', 'porto-functionality' ),
					),
					'default'     => 'minicart-arrow-alt',
				)
			);

			$this->add_control(
				'content_type',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Content Type', 'porto-functionality' ),
					'options' => array(
						''          => __( 'Default', 'porto-functionality' ),
						'offcanvas' => __( 'Off Canvas', 'porto-functionality' ),
					),
					'default' => '',
				)
			);

			$this->add_control(
				'icon_cl',
				array(
					'type'                   => Controls_Manager::ICONS,
					'label'                  => __( 'Icon', 'porto-functionality' ),
					'fa4compatibility'       => 'icon',
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'label_block'            => false,
					'default'                => array(
						'value'   => '',
						'library' => '',
					),
				)
			);

			$this->add_control(
				'icon_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Size', 'porto-functionality' ),
					'separator'  => 'before',
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 12,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0.1,
							'max'  => 5,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => 32,
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#mini-cart .minicart-icon, #mini-cart.minicart-inline .minicart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} #mini-cart .minicart-icon, .elementor-element-{{ID}} #mini-cart.minicart-arrow-alt .cart-head:after' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'sticky_icon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color In Sticky', 'porto-functionality' ),
					'selectors' => array(
						'.sticky-header .elementor-element-{{ID}} #mini-cart .minicart-icon, .sticky-header .elementor-element-{{ID}} #mini-cart.minicart-arrow-alt .cart-head:after' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'hover_icon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Hover Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} #mini-cart:hover .minicart-icon, .elementor-element-{{ID}} #mini-cart.minicart-arrow-alt:hover .cart-head:after' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'sticky_hover_icon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Hover Color In Sticky', 'porto-functionality' ),
					'selectors' => array(
						'.sticky-header .elementor-element-{{ID}} #mini-cart:hover .minicart-icon, .sticky-header .elementor-element-{{ID}} #mini-cart.minicart-arrow-alt:hover .cart-head:after' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'icon_margin_left',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Margin Left', 'porto-functionality' ),
					'separator'  => 'before',
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'default'    => array(
						'unit' => 'px',
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-icon' => 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_margin_right',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Margin Right', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'default'    => array(
						'unit' => 'px',
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-icon' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'text_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Text Color', 'porto-functionality' ),
					'separator' => 'before',
					'selectors' => array(
						'#mini-cart .cart-subtotal' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'type' => array( 'minicart-inline', 'minicart-text' ),
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'text_font',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Text Typography', 'porto-functionality' ),
					'selector'  => '#mini-cart .cart-subtotal',
					'condition' => array(
						'type' => array( 'minicart-inline', 'minicart-text' ),
					),
				)
			);

			$this->add_control(
				'price_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Price Color', 'porto-functionality' ),
					'selectors' => array(
						'#mini-cart .cart-price' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'type' => array( 'minicart-inline', 'minicart-text' ),
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'price_font',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Price Typography', 'porto-functionality' ),
					'selector'  => '#mini-cart .cart-price',
					'condition' => array(
						'type' => array( 'minicart-inline', 'minicart-text' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hb_mini_cart_badge',
			array(
				'label' => __( 'Cart Badge', 'porto-functionality' ),
			)
		);
			$this->add_control(
				'icon_item_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Font Size', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-items' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_item_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'#mini-cart .cart-items' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'icon_item_bg_size',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Badge Background Size', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-items' => '--porto-badge-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'icon_item_bg_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'selectors' => array(
						'#mini-cart .cart-items' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'icon_item_right',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Badge Right Position', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-items' => ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'icon_item_top',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Badge Top Position', 'porto-functionality' ),
					'range'      => array(
						'px'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'rem' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors'  => array(
						'#mini-cart .cart-items' => 'top: {{SIZE}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) ) {
			$custom_icon = '';
			if ( isset( $settings['icon_cl'] ) && ! empty( $settings['icon_cl']['value'] ) ) {
				if ( isset( $settings['icon_cl']['library'] ) && ! empty( $settings['icon_cl']['value']['id'] ) ) {
					$custom_icon = $settings['icon_cl']['value']['id'];
				} else {
					$custom_icon = $settings['icon_cl']['value'];
				}
			}
			global $porto_settings;
			if ( $settings['type'] ) {
				if ( isset( $porto_settings['minicart-type'] ) ) {
					$backup_type = $porto_settings['minicart-type'];
				}
				$porto_settings['minicart-type'] = $settings['type'];
			}
			if ( $custom_icon ) {
				if ( isset( $porto_settings['minicart-icon'] ) ) {
					$backup_icon = $porto_settings['minicart-icon'];
				}
				$porto_settings['minicart-icon'] = $custom_icon;
			}
			if ( $settings['content_type'] ) {
				if ( isset( $porto_settings['minicart-content'] ) ) {
					$backup_content_type = $porto_settings['minicart-content'];
				}
				$porto_settings['minicart-content'] = $settings['content_type'];
			}

			porto_header_elements( array( (object) array( 'mini-cart' => '' ) ) );

			if ( isset( $backup_type ) ) {
				$porto_settings['minicart-type'] = $backup_type;
			}
			if ( isset( $backup_icon ) ) {
				$porto_settings['minicart-icon'] = $backup_icon;
			}
			if ( isset( $backup_content_type ) ) {
				$porto_settings['minicart-content'] = $backup_content_type;
			}
		}
	}
}
