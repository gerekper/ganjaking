<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Compare Icon widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Compare_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_compare';
	}

	public function get_title() {
		return __( 'Compare', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'compare', 'icon', 'yith' );
	}

	public function get_icon() {
		return 'porto-icon-compare-link';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_compare',
			array(
				'label' => __( 'Compare Icon', 'porto-functionality' ),
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
			'size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Font Size', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 72,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'#header .elementor-element-{{ID}} .yith-woocompare-open' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'#header .elementor-element-{{ID}} .yith-woocompare-open' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( class_exists( 'Woocommerce' ) && defined( 'YITH_WOOCOMPARE' ) && class_exists( 'YITH_Woocompare' ) ) {
			global $yith_woocompare;
			$icon_cl = 'porto-icon-compare-link';
			if ( isset( $settings['icon_cl'] ) && ! empty( $settings['icon_cl']['value'] ) ) {
				if ( isset( $settings['icon_cl']['library'] ) && ! empty( $settings['icon_cl']['value']['id'] ) ) {
					$icon_cl = $settings['icon_cl']['value']['id'];
				} else {
					$icon_cl = $settings['icon_cl']['value'];
				}
			}
			$compare_count = isset( $yith_woocompare->obj->products_list ) ? sizeof( $yith_woocompare->obj->products_list ) : 0;
			echo '<a href="#" title="' . esc_attr__( 'Compare', 'porto' ) . '" class="yith-woocompare-open"><i class="' . esc_attr( $icon_cl ) . '"></i><span class="compare-count">' . intval( $compare_count ) . '</span></a>';
		}
	}
}
