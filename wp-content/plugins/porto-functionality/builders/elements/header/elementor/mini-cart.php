<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Mini cart widget
 *
 * @since 6.0
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

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_mini_cart',
			array(
				'label' => __( 'Mini Cart', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Mini Cart Type', 'porto-functionality' ),
				'description' => __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> Mini Cart Type instead.', 'porto-functionality' ),
				'options'     => array(
					''                   => __( 'Theme Options', 'porto-functionality' ),
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
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => '',
					'library' => '',
				),
			)
		);

		$this->add_control(
			'cart_text',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Mini Cart Text', 'porto-functionality' ),
				'description' => __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> Mini Cart Text instead.', 'porto-functionality' ),
				'condition'   => array(
					'type' => array( 'minicart-inline', 'minicart-text' ),
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Size', 'porto-functionality' ),
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
					'#mini-cart .minicart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#mini-cart .cart-subtotal, #mini-cart .minicart-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_margin_left',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Margin Left', 'porto-functionality' ),
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

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'text_font',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Text Typograhy', 'porto-functionality' ),
				'selector'  => '#mini-cart .cart-subtotal',
				'condition' => array(
					'type' => array( 'minicart-inline', 'minicart-text' ),
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'default'   => '',
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
				'name'      => 'price_font',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Price Typograhy', 'porto-functionality' ),
				'selector'  => '#mini-cart .cart-price',
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
				'default'   => '',
				'selectors' => array(
					'#mini-cart .cart-price' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'type' => array( 'minicart-inline', 'minicart-text' ),
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
				$backup_type                     = $porto_settings['minicart-type'];
				$porto_settings['minicart-type'] = $settings['type'];
			}
			if ( $custom_icon ) {
				$backup_icon                     = $porto_settings['minicart-icon'];
				$porto_settings['minicart-icon'] = $custom_icon;
			}
			if ( $settings['content_type'] ) {
				$backup_content_type                = $porto_settings['minicart-content'];
				$porto_settings['minicart-content'] = $settings['content_type'];
			}
			if ( ! empty( $settings['cart_text'] ) ) {
				$backup_cart_text = false;
				if ( isset( $porto_settings['minicart-text'] ) ) {
					$backup_cart_text = $porto_settings['minicart-text'];
				}
				$porto_settings['minicart-text'] = $settings['cart_text'];
			}

			porto_header_elements( array( (object) array( 'mini-cart' => '' ) ) );

			if ( $settings['type'] ) {
				$porto_settings['minicart-type'] = $backup_type;
			}
			if ( $custom_icon ) {
				$porto_settings['minicart-icon'] = $backup_icon;
			}
			if ( $settings['content_type'] ) {
				$porto_settings['minicart-content'] = $backup_content_type;
			}
			if ( ! empty( $settings['cart_text'] ) && false !== $backup_cart_text ) {
				$porto_settings['minicart-text'] = $backup_cart_text;
			}
		}
	}
}
