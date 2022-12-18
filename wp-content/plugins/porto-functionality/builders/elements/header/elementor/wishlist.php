<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder Wishlist Icon widget
 *
 * @since 2.0
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

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-wishlist-icon-element/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_hb_wishlist',
			array(
				'label' => __( 'Wishlist Icon', 'porto-functionality' ),
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
				'selectors' => array(
					'#header .elementor-element-{{ID}} .my-wishlist' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sticky_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color In Sticky', 'porto-functionality' ),
				'selectors' => array(
					'#header.sticky-header .elementor-element-{{ID}} .my-wishlist' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Hover Color', 'porto-functionality' ),
				'selectors' => array(
					'#header .elementor-element-{{ID}} .my-wishlist:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sticky_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Hover Color In Sticky', 'porto-functionality' ),
				'selectors' => array(
					'#header.sticky-header .elementor-element-{{ID}} .my-wishlist:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Badge Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .wishlist-count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_bg_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Badge Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .wishlist-count' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'offcanvas',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Off Canvas ?', 'porto-functionality' ),
				'description' => __( 'Controls to show the wishlist dropdown as off canvas.', 'porto-functionality' ),
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
			global $porto_settings;
			if ( isset( $porto_settings['wl-offcanvas'] ) ) {
				$backup_offcanvas = $porto_settings['wl-offcanvas'];
			}
			$porto_settings['wl-offcanvas'] = ! empty( $settings['offcanvas'] ) ? true : false;
			if ( function_exists( 'porto_wishlist' ) ) {
				echo porto_wishlist( '', $icon_cl );
			} else {
				$wc_count = yith_wcwl_count_products();
				echo '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '"' . ' title="' . esc_attr__( 'Wishlist', 'porto' ) . '" class="my-wishlist"><i class="' . esc_attr( $icon_cl ) . '"></i><span class="wishlist-count">' . intval( $wc_count ) . '</span></a>';
			}
			if ( isset( $backup_offcanvas ) ) {
				$porto_settings['wl-offcanvas'] = $backup_offcanvas;
			}
		}
	}
}
