<?php
/**
 * UAEL Grid Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Classic
 */
class Skin_Classic extends Skin_Base {

	/**
	 * Get Skin Slug.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_id() {

		return 'classic';
	}

	/**
	 * Get Skin Title.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_title() {

		return __( 'Classic', 'uael' );
	}

	/**
	 * Register Control Actions.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-posts/classic_section_design_blog/before_section_end', array( $this, 'update_blog_controls' ) );

		add_action( 'elementor/element/uael-posts/classic_section_general_field/before_section_end', array( $this, 'update_general_controls' ) );
	}

	/**
	 * Update Blog Design control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function update_blog_controls() {

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'selector' => '{{WRAPPER}} .uael-post__bg-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'classic_box_shadow',
				'selector' => '{{WRAPPER}} .uael-post__bg-wrap',
			)
		);
	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.1
	 * @access public
	 */
	public function update_general_controls() {

		$this->add_control(
			'equal_grid_height',
			array(
				'label'        => __( 'Equal Height', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'label_off'    => __( 'No', 'uael' ),
				'label_on'     => __( 'Yes', 'uael' ),
				'prefix_class' => 'uael-equal__height-',
				'description'  => __( 'Enable this to display all posts with same height.', 'uael' ),
				'condition'    => array(
					$this->get_control_id( 'post_structure' ) => array( 'featured', 'normal' ),
				),
			)
		);
	}

	/**
	 * Render Main HTML.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function render() {

		$settings = $this->parent->get_settings_for_display();

		$skin = Skin_Init::get_instance( $this->get_id() );

		echo wp_kses_post( sanitize_text_field( $skin->render( $this->get_id(), $settings, $this->parent->get_id() ) ) );
	}
}

