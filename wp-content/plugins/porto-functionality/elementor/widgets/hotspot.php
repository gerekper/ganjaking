<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Hotspot Widget
 *
 * Porto Elementor widget to display html, block content or product on hover.
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Hotspot_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hotspot';
	}

	public function get_title() {
		return __( 'Hotspot', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'spot', 'product', 'block', 'html' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hotspot',
			array(
				'label' => __( 'Hot Spot', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'ctype',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Content Type', 'porto-functionality' ),
				'options' => array(
					'html'    => __( 'HTML', 'porto-functionality' ),
					'product' => __( 'Product', 'porto-functionality' ),
					'block'   => __( 'Block', 'porto-functionality' ),
				),
				'default' => 'html',
			)
		);

		$this->add_control(
			'content',
			array(
				'type'      => Controls_Manager::WYSIWYG,
				'label'     => __( 'HTML Content', 'porto-functionality' ),
				'condition' => array(
					'ctype' => 'html',
				),
			)
		);

		$this->add_control(
			'pid',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Product', 'porto-functionality' ),
				'description' => __( 'Please input a product id or slug.', 'porto-functionality' ),
				'condition'   => array(
					'ctype' => 'product',
				),
			)
		);

		$this->add_control(
			'addlinks_pos',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Product Layout', 'porto-functionality' ),
				'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
				'options'     => array_combine( array_values( porto_sh_commons( 'products_addlinks_pos' ) ), array_keys( porto_sh_commons( 'products_addlinks_pos' ) ) ),
				'condition'   => array(
					'ctype' => 'product',
				),
			)
		);

		$this->add_control(
			'block',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Block ID or Slug', 'porto-functionality' ),
				'condition' => array(
					'ctype' => 'block',
				),
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
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'pos',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Popup position', 'porto-functionality' ),
				'options' => array(
					'top'    => __( 'Top', 'porto-functionality' ),
					'right'  => __( 'Right', 'porto-functionality' ),
					'bottom' => __( 'Bottom', 'porto-functionality' ),
					'left'   => __( 'Left', 'porto-functionality' ),
				),
				'default' => 'right',
			)
		);

		$this->add_control(
			'x1',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Horizontal Position', 'porto-functionality' ),
				'range'     => array(
					'%' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default'   => array(
					'unit' => '%',
					'size' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'y1',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Vertical Position', 'porto-functionality' ),
				'range'     => array(
					'%' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default'   => array(
					'unit' => '%',
					'size' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'size1',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spot Size', 'porto-functionality' ),
				'range'      => array(
					'%'   => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
					'px'  => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 200,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'default'    => array(
					'unit' => 'px',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-hotspot' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Icon Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-hotspot' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_hotspot' ) ) {
			$atts['type'] = $atts['ctype'];
			if ( ! empty( $atts['pid'] ) ) {
				$atts['id'] = $atts['pid'];
			}
			if ( isset( $atts['icon_cl'] ) && isset( $atts['icon_cl']['value'] ) ) {
				if ( isset( $atts['icon_cl']['library'] ) && isset( $atts['icon_cl']['value']['id'] ) ) {
					$atts['icon'] = $atts['icon_cl']['value']['id'];
				} else {
					$atts['icon'] = $atts['icon_cl']['value'];
				}
			}
			include $template;
		}
	}
}
