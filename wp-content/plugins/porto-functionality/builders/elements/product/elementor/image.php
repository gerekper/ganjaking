<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Image Widget
 *
 * Porto Elementor widget to display images section on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Image_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_image';
	}

	public function get_title() {
		return __( 'Product Image', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	public function get_keywords() {
		return array( 'product', 'image', 'media', 'thumbnail' );
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_image',
			array(
				'label' => __( 'Product Image', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'style',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Style', 'porto-functionality' ),
					'options' => array(
						''                       => __( 'Default', 'porto-functionality' ),
						'extended'               => __( 'Extended', 'porto-functionality' ),
						'grid'                   => __( 'Grid Images', 'porto-functionality' ),
						'full_width'             => __( 'Thumbs on Image', 'porto-functionality' ),
						'sticky_info'            => __( 'List Images', 'porto-functionality' ),
						'transparent'            => __( 'Left Thumbs 1', 'porto-functionality' ),
						'centered_vertical_zoom' => __( 'Left Thumbs 2', 'porto-functionality' ),
					),
				)
			);

			$this->add_control(
				'spacing',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Spacing', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 60,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .product-layout-centered_vertical_zoom .img-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'style' => 'centered_vertical_zoom',
					),
				)
			);

			$this->add_control(
				'spacing1',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Spacing', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 60,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .product-images .product-image-slider .img-thumbnail' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'style' => 'extended',
					),
				)
			);

			$this->add_control(
				'spacing2',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Spacing', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 60,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .product-images-block .img-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'.elementor-element-{{ID}} .product-layout-grid .product-images-block' => '--bs-gutter-x: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'style' => array( 'sticky_info', 'grid' ),
					),
				)
			);

			$this->add_control(
				'br_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Border Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .img-thumbnail .inner' => 'border-color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cp_thumbnail',
			array(
				'label' => __( 'Thumbnail Image', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'thumbnail_width',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Thumbnail Width', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 172,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .product-layout-centered_vertical_zoom .product-thumbnails' => 'width: {{SIZE}}{{UNIT}};',
						'.elementor-element-{{ID}} .product-layout-centered_vertical_zoom .product-images' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
					),
					'condition'  => array(
						'style' => 'centered_vertical_zoom',
					),
				)
			);

			$this->add_control(
				'thumbnail_img_width',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Thumbnail Image Width', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 172,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'style' => 'centered_vertical_zoom',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_thumbnail' );
				$this->start_controls_tab(
					'tab_thumbnail',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'thumbnail_br_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Thumbnail Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .product-thumbs-slider.owl-carousel .img-thumbnail, .elementor-element-{{ID}} .product-layout-full_width .img-thumbnail, .elementor-element-{{ID}} .product-thumbs-vertical-slider img, .elementor-element-{{ID}} .product-layout-centered_vertical_zoom .img-thumbnail' => 'border-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_thumbnail_hover',
					array(
						'label' => esc_html__( 'Hover', 'porto-functinoality' ),
					)
				);
					$this->add_control(
						'thumbnail_hover_br_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Border Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} .product-thumbs-slider .owl-item.selected .img-thumbnail, html:not(.touch) .elementor-element-{{ID}} .product-thumbs-slider .owl-item:hover .img-thumbnail, .elementor-element-{{ID}} .product-layout-full_width .img-thumbnail.selected, .elementor-element-{{ID}} .product-thumbs-vertical-slider .slick-current img, .elementor-element-{{ID}} .product-layout-centered_vertical_zoom .img-thumbnail.selected' => 'border-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function get_style_depends() {
		$depends = array();
		if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
			wp_register_style( 'porto-sp-layout', PORTO_CSS . '/theme/shop/single-product/builder' . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
			$depends[] = 'porto-sp-layout';
		}
		return $depends;
	}


	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_image( $settings );
		}
	}
}
