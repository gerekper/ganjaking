<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Wishlist Icon widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Wishlist_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_wishlist';
	}

	public function get_title() {
		return __( 'Wishlist', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'wishlist', 'icon', 'yith' );
	}

	public function get_icon() {
		return 'porto-icon-wishlist-2';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_wishlist',
			array(
				'label' => __( 'Wishlist Icon', 'porto-functionality' ),
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
					'#header .elementor-element-{{ID}} .my-wishlist' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'#header .elementor-element-{{ID}} .my-wishlist' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( class_exists( 'Woocommerce' ) && defined( 'YITH_WCWL' ) ) {
			$icon_cl = 'porto-icon-wishlist-2';
			if ( isset( $settings['icon_cl'] ) && ! empty( $settings['icon_cl']['value'] ) ) {
				if ( isset( $settings['icon_cl']['library'] ) && ! empty( $settings['icon_cl']['value']['id'] ) ) {
					$icon_cl = $settings['icon_cl']['value']['id'];
				} else {
					$icon_cl = $settings['icon_cl']['value'];
				}
			}
			$wc_count = yith_wcwl_count_products();
			echo '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '"' . ' title="' . esc_attr__( 'Wishlist', 'porto' ) . '" class="my-wishlist"><i class="' . esc_attr( $icon_cl ) . '"></i><span class="wishlist-count">' . intval( $wc_count ) . '</span></a>';
		}
	}
}
