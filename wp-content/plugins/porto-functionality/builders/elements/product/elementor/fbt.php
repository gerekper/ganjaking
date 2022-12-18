<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product FBT Widget
 *
 * @since 2.6.0
 */
use Elementor\Controls_Manager;
class Porto_Elementor_CP_Fbt_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_fbt';
	}

	public function get_title() {
		return __( 'Frequently Bought Together', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'yith', 'product', 'single', 'fbt' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_cp_fbt_image',
			array(
				'label' => __( 'Image', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'image_w',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Width (px)', 'porto-functionality' ),
					'description' => __( 'Controls the width of the image.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wfbt-images td img' => 'width: {{SIZE}}{{UNIT}};',
					),
					'qa_selector' => '.image-td:first-child',
				)
			);
			$this->add_control(
				'plus_w',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Plus Width (px)', 'porto-functionality' ),
					'description' => __( 'Controls the width of the plus.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wfbt-images .image_plus' => 'width: {{SIZE}}{{UNIT}};',
					),
					'qa_selector' => '.image_plus_1',
				)
			);
			$this->add_control(
				'plus_sz',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Plus Size (px)', 'porto-functionality' ),
					'description' => __( 'Controls the size of the plus.', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wfbt-images .image_plus' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_cp_fbt_text',
			array(
				'label' => __( 'Text', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'hide_title',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'label'       => __( 'Hide Title', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .yith-wfbt-section>h3' => 'display:none;',
					),
					'qa_selector' => '.yith-wfbt-section>h3',
				)
			);
			$this->add_control(
				'spacing',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Between Spacing (px)', 'porto-functionality' ),
					'selectors'   => array(
						'.elementor-element-{{ID}} .price_text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'qa_selector' => '.price_text',
				)
			);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'item_sz',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .yith-wfbt-item',
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_fbt( $settings );
		}
	}
}
