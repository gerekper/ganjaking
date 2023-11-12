<?php
/**
 * UAEL Card Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Event
 */
class Skin_Event extends Skin_Base {

	/**
	 * Get Skin Slug.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_id() {
		return 'event';
	}

	/**
	 * Get Skin Title.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Event', 'uael' );
	}

	/**
	 * Register Control Actions.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-posts/event_section_title_field/before_section_end', array( $this, 'register_update_title_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_design_layout/before_section_end', array( $this, 'register_update_layout_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_image_field/before_section_end', array( $this, 'register_update_image_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_featured_field/before_section_end', array( $this, 'register_update_meta_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_design_blog/before_section_end', array( $this, 'register_blog_design_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_meta_field/before_section_end', array( $this, 'register_meta_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_meta_style/before_section_end', array( $this, 'register_meta_style_controls' ) );

		add_action( 'elementor/element/uael-posts/event_section_general_field/before_section_end', array( $this, 'update_general_controls' ) );
	}

	/**
	 * Register controls callback.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.7.0
	 * @access public
	 */
	public function register_sections( Widget_Base $widget ) {

		$this->parent = $widget;

		// Content Controls.
		$this->register_content_filters_controls();
		$this->register_content_slider_controls();
		$this->register_content_featured_controls();
		$this->register_content_image_controls();
		$this->register_content_title_controls();
		$this->register_content_meta_controls();
		$this->register_content_excerpt_controls();
		$this->register_content_cta_controls();

		// Style Controls.
		$this->register_style_layout_controls();
		$this->register_style_blog_controls();
		$this->register_style_datebox_controls();
		$this->register_style_pagination_controls();
		$this->register_style_featured_controls();
		$this->register_style_title_controls();
		$this->register_style_meta_controls();
		$this->register_style_excerpt_controls();
		$this->register_style_cta_controls();
		$this->register_posts_schema();
		$this->register_style_navigation_controls();
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
	 * Update Blog Design control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_meta_controls() {

		$this->update_control(
			'show_meta',
			array(
				'default' => '',
			)
		);
	}

	/**
	 * Update Blog Design control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_meta_style_controls() {

		$this->update_control(
			'section_meta_style',
			array(
				'condition' => array(
					$this->get_control_id( 'show_meta' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Update Blog Design control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_blog_design_controls() {

		$this->update_control(
			'blog_padding',
			array(
				'label'   => __( 'Content Padding', 'uael' ),
				'default' => array(
					'top'    => '25',
					'bottom' => '30',
					'right'  => '30',
					'left'   => '30',
					'unit'   => 'px',
				),
			)
		);

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
				'name'     => 'event_box_shadow',
				'selector' => '{{WRAPPER}} .uael-post__bg-wrap',
			)
		);
	}

	/**
	 * Update Meta control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_meta_controls() {

		$this->update_control(
			'_f_meta',
			array(
				'condition' => array(
					$this->get_control_id( 'show_meta' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Update Image control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_image_controls() {

		$this->update_control(
			'image_position',
			array(
				'default' => 'top',
				'options' => array(
					'top'  => __( 'Top', 'uael' ),
					'none' => __( 'None', 'uael' ),
				),
			)
		);
		$this->remove_control( 'image_background_color' );
	}

	/**
	 * Update Layout control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_layout_controls() {

		$this->update_control(
			'alignment',
			array(
				'default' => 'center',
			)
		);
	}

	/**
	 * Update Title control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_title_controls() {

		$this->update_control(
			'title_tag',
			array(
				'default'   => 'h4',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Register Datebox control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_style_datebox_controls() {

		$this->start_controls_section(
			'section_datebox_style',
			array(
				'label' => __( 'Date Box', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'datebox_size',
				array(
					'label'     => __( 'Date Box Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 50,
							'max' => 100,
						),
					),
					'default'   => array(
						'size' => 60,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__datebox:not(.uael-post__noimage)' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}}/2);',
						'{{WRAPPER}} .uael-post__datebox.uael-post__noimage' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: {{event_blog_padding.top}}{{event_blog_padding.unit}};',
					),
				)
			);

			$this->add_control(
				'datebox_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-post__datebox' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'datebox_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__datebox' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'datebox_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'selector' => '{{WRAPPER}} .uael-post__datebox',
				)
			);

		$this->end_controls_section();
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
