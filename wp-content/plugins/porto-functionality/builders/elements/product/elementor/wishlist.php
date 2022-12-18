<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Wishlist Widget
 *
 * @since 2.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Wishlist_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_wishlist';
	}

	public function get_title() {
		return __( 'Product Wishlist', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'yith', 'heart', 'single' );
	}

	public function get_icon() {
		return 'eicon-heart-o';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {
		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';
		$this->start_controls_section(
			'section_cp_wishlist',
			array(
				'label' => __( 'Product Wishlist', 'porto-functionality' ),
			)
		);
			$this->add_control(
				'show_label',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'label'       => __( 'Show Label', 'porto-functionality' ),
					'description' => __( 'Show/Hide the wishlist label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a, .elementor-element-{{ID}} a span' => 'width: auto;text-indent: 0;',
						'.elementor-element-{{ID}} .yith-wcwl-add-to-wishlist a:before' => "position: static;margin-{$right}: 0.125rem;line-height: 1;",
					),
				)
			);
			$this->add_control(
				'icon_size',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Icon Size', 'porto-functionality' ),
					'description' => __( 'Controls the size of icon.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a:before' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'label_font',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Label Typography', 'porto-functionality' ),
					'selector'  => '{{WRAPPER}} a, {{WRAPPER}} a span',
					'condition' => array(
						'show_label' => 'yes',
					),
				)
			);
			$this->add_control(
				'spacing',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Spacing', 'porto-functionality' ),
					'description' => __( 'Controls the spacing between icon and label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wcwl-add-to-wishlist a:before' => "margin-{$right}: {{SIZE}}{{UNIT}};",
					),
					'condition'   => array(
						'show_label' => 'yes',
					),
				)
			);
			$this->add_control(
				'icon_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Icon Color', 'porto-functionality' ),
					'description' => __( 'Controls the color of wishlist icon.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wcwl-add-to-wishlist a:before' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'icon_added_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Added Color', 'porto-functionality' ),
					'description' => __( 'Controls the added color of wishlist icon.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wcwl-wishlistaddedbrowse a:before, .elementor-element-{{ID}} .yith-wcwl-wishlistexistsbrowse a:before' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'label_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Label Color', 'porto-functionality' ),
					'description' => __( 'Controls the color of wishlist label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a, .elementor-element-{{ID}} a span' => 'color: {{VALUE}};',
					),
					'condition'   => array(
						'show_label' => 'yes',
					),
				)
			);
			$this->add_control(
				'label_hover_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Label Hover Color', 'porto-functionality' ),
					'description' => __( 'Controls the hover color of label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a:hover, .elementor-element-{{ID}} a:hover span' => 'color: {{VALUE}};',
					),
					'condition'   => array(
						'show_label' => 'yes',
					),
				)
			);
			$this->add_control(
				'bg_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Background Color', 'porto-functionality' ),
					'description' => __( 'Controls the background color of label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a' => 'background-color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'bg_hover_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Background Hover Color', 'porto-functionality' ),
					'description' => __( 'Controls the background hover color of label.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} a:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_wishlist( $settings );
		}
	}
}
