<?php
/**
 * UAEL BusinessReviews Skin - Card.
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
 * Class Skin_Card
 *
 * @property Products $parent
 */
class Skin_Card extends Skin_Base {

	/**
	 * Get ID.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function get_id() {
		return 'card';
	}

	/**
	 * Get title.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Card', 'uael' );
	}

	/**
	 * Register Control Actions.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-business-reviews/card_section_info_controls/before_section_end', array( $this, 'update_image_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/card_section_date_controls/before_section_end', array( $this, 'register_update_date_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/card_section_styling/before_section_end', array( $this, 'update_box_style_controls' ) );

		add_action( 'elementor/element/uael-business-reviews/card_section_spacing/before_section_end', array( $this, 'update_spacing_controls' ) );
	}

	/**
	 * Update Date control.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function update_image_controls() {
		$this->update_control(
			'image_align',
			array(
				'default'   => 'top',
				'condition' => array(
					$this->get_control_id( 'reviewer_image' ) => 'yes',
				),
			)
		);

		$this->update_control(
			'review_source_icon',
			array(
				'default'   => 'no',
				'condition' => array(
					$this->get_control_id( 'image_align' ) . '!' => 'top',
				),
			)
		);

		$this->update_control(
			'image_size',
			array(
				'default' => array(
					'size' => 55,
				),
			)
		);
	}

	/**
	 * Update Date control.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function register_update_date_controls() {
		$this->update_control(
			'review_date',
			array(
				'default' => 'yes',
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

		$this->add_responsive_control(
			'reviewer_content_spacing',
			array(
				'label'              => __( 'Content Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 15,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-review-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
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

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'card_block_border',
				'label'          => __( 'Border', 'uael' ),
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'    => '1',
							'right'  => '1',
							'bottom' => '1',
							'left'   => '1',
						),
					),
					'color'  => array(
						'default' => '#ededed',
					),
				),
				'selector'       => '{{WRAPPER}} .uael-review',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_block_box_shadow',
				'selector' => '{{WRAPPER}} .uael-review',
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
