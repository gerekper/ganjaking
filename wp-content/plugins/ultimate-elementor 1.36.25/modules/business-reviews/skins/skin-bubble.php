<?php
/**
 * UAEL BusinessReviews Skin - bubble.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessReviews\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

use UltimateElementor\Modules\BusinessReviews\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Bubble
 *
 * @property Products $parent
 */
class Skin_Bubble extends Skin_Base {

	/**
	 * Get ID.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function get_id() {
		return 'bubble';
	}

	/**
	 * Get title.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Bubble', 'uael' );
	}

	/**
	 * Register Control Actions.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-business-reviews/bubble_section_info_controls/before_section_end', array( $this, 'update_image_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/bubble_section_date_controls/before_section_end', array( $this, 'update_date_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/bubble_section_content_controls/before_section_end', array( $this, 'update_content_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/bubble_section_styling/before_section_end', array( $this, 'update_box_style_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/bubble_section_spacing/before_section_end', array( $this, 'update_spacing_controls' ) );

	}

	/**
	 * Update Date control.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_image_controls() {
		$this->remove_control( 'image_align' );

		$this->update_control(
			'image_size',
			array(
				'condition' => array(
					$this->get_control_id( 'reviewer_image' ) => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-review-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-review-content-arrow-wrap' => 'left: calc( {{SIZE}}{{UNIT}} - 14px );',
				),
			)
		);

		$this->update_control(
			'review_source_icon',
			array(
				'default' => 'yes',
			)
		);
	}

	/**
	 * Update Date control.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_date_controls() {
		$this->update_control(
			'review_date',
			array(
				'default' => 'no',
			)
		);
	}

	/**
	 * Update content controls.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_content_controls() {

		$this->remove_control( 'reviewer_content_color' );

		$this->add_control(
			'hide_arrow_content',
			array(
				'label'        => __( 'Hide Arrow', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					$this->get_control_id( 'review_content' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Update Box control.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_box_style_controls() {

		$this->add_control(
			'bubble_content_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-review-content' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'review_content' ) => 'yes',
				),
			)
		);

		$this->update_control(
			'block_bg_color',
			array(
				'default'   => '#fafafa',
				'selectors' => array(
					'{{WRAPPER}} .uael-review-content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-review-arrow'   => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->update_control(
			'block_padding',
			array(
				'selectors' => array(
					'{{WRAPPER}} .uael-review-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bubble_block_border_style',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'label_block' => false,
				'options'     => array(
					'none'  => __( 'None', 'uael' ),
					'solid' => __( 'Solid', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-review-content' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bubble_block_border_size',
			array(
				'label'              => __( 'Border Width', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 1,
				),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'condition'          => array(
					$this->get_control_id( 'bubble_block_border_style' ) . '!'    => 'none',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-review-content' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-review-arrow'   => 'top: -{{SIZE}}px; left: 0px; border-width: 16px;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'bubble_block_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					$this->get_control_id( 'bubble_block_border_style' ) . '!'    => 'none',
				),
				'default'   => '#ededed',
				'selectors' => array(
					'{{WRAPPER}} .uael-review-content' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-review-arrow-border' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->update_control(
			'block_radius',
			array(
				'default'   => array(
					'top'    => '5',
					'bottom' => '5',
					'right'  => '5',
					'left'   => '5',
					'unit'   => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-review-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	/**
	 * Register Styling Controls.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_spacing_controls() {

		$this->remove_control( 'reviewer_name_spacing' );

		$this->update_responsive_control(
			'reviewer_image_spacing',
			array(
				'default'   => array(
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-review-image' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_block_spacing',
			array(
				'label'              => __( 'Content Block Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-review-content-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);
	}

	/**
	 * Render Main HTML.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function render() {

		$settings = $this->parent->get_settings_for_display();

		$skin = Skin_Init::get_instance( $this->get_id() );

		echo wp_kses_post( sanitize_text_field( $skin->render( $this->get_id(), $settings, $this->parent->get_id() ) ) );
	}
}
