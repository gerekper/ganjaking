<?php
/**
 * UAEL Business Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Business
 */
class Skin_Business extends Skin_Base {

	/**
	 * Get Skin Slug.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function get_id() {

		return 'business';
	}

	/**
	 * Get Skin Title.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function get_title() {

		return __( 'Business Card', 'uael' );
	}

	/**
	 * Register controls on given actions.
	 *
	 * @since 1.10.1
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-posts/business_section_image_field/before_section_end', array( $this, 'register_update_image_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_meta_field/before_section_end', array( $this, 'register_update_meta_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_excerpt_field/before_section_end', array( $this, 'register_update_excerpt_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_cta_field/before_section_end', array( $this, 'register_update_cta_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_design_blog/before_section_end', array( $this, 'register_update_blog_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_design_layout/before_section_end', array( $this, 'register_update_layout_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_general_field/before_section_end', array( $this, 'register_update_general_controls' ) );

		add_action( 'elementor/element/uael-posts/business_section_title_style/before_section_end', array( $this, 'register_update_title_style' ) );

		add_action( 'elementor/element/uael-posts/business_section_featured_field/before_section_end', array( $this, 'register_update_featured_style' ) );

	}

	/**
	 * Register controls callback.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.10.1
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
		$this->register_content_badge_controls();
		$this->register_content_excerpt_controls();
		$this->register_content_cta_controls();

		// Style Controls.
		$this->register_style_layout_controls();
		$this->register_style_blog_controls();
		$this->register_style_pagination_controls();
		$this->register_style_featured_controls();
		$this->register_style_title_controls();
		$this->register_style_meta_controls();
		$this->register_style_term_controls();
		$this->register_style_excerpt_controls();
		$this->register_style_cta_controls();
		$this->register_posts_schema();
		$this->register_style_navigation_controls();
		$this->register_authorbox_style_controls();
	}

	/**
	 * Update Image control.
	 *
	 * @since 1.10.1
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
	 * Update Meta control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_meta_controls() {
		$this->update_control(
			'show_meta',
			array(
				'default' => 'no',
			)
		);
	}

	/**
	 * Update Excerpt control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_excerpt_controls() {
		$this->update_control(
			'show_excerpt',
			array(
				'default' => 'no',
			)
		);
	}

	/**
	 * Update CTA control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_cta_controls() {
		$this->update_control(
			'show_cta',
			array(
				'default' => 'no',
			)
		);
	}

	/**
	 * Update Layout control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_layout_controls() {

		$this->update_control(
			'alignment',
			array(
				'selectors'    => array(
					'{{WRAPPER}} .uael-post-wrapper, {{WRAPPER}} .uael-post__separator-wrap' => 'text-align: {{VALUE}};',
				),
				'prefix_class' => 'uael-post__content-align-',
				'render_type'  => 'template',
				'toggle'       => false,
				'default'      => 'left',
			)
		);

		$this->add_control(
			'separator_title',
			array(
				'label'     => __( 'Separator', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_separator_height',
			array(
				'label'      => __( 'Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 2,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__separator' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_separator_width',
			array(
				'label'      => __( 'Separator Length ( In Percentage )', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__separator' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'separator_spacing',
			array(
				'label'     => __( 'Bottom Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'default'   => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__separator-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_separator_color',
			array(
				'label'     => __( 'Separator Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}}.uael-post__content-align-left .uael-post__gradient-separator' => 'background: linear-gradient( to right, {{VALUE}} 0%, #ffffff00 100% );',
					'{{WRAPPER}}.uael-post__content-align-center .uael-post__gradient-separator' => 'background: radial-gradient( {{VALUE}} 10%, #ffffff00 80% );',
					'{{WRAPPER}}.uael-post__content-align-right .uael-post__gradient-separator' => 'background: linear-gradient( to left, {{VALUE}} 0%, #ffffff00 100% );',
				),
			)
		);
	}

	/**
	 * Update Blog Design control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_blog_controls() {

		$this->update_control(
			'blog_padding',
			array(
				'default' => array(
					'top'    => '25',
					'bottom' => '25',
					'right'  => '25',
					'left'   => '25',
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

		$this->add_control(
			'content_radius',
			array(
				'label'      => __( 'Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '15',
					'bottom' => '15',
					'left'   => '15',
					'right'  => '15',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__bg-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
	 * Update General Design control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_general_controls() {
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
	 * Update Title style control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_title_style() {
		$this->update_control(
			'title_spacing',
			array(
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
			)
		);
	}

	/**
	 * Update featured post control.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_update_featured_style() {

		$this->update_control(
			'_f_meta',
			array(
				'condition' => array(
					$this->get_control_id( 'post_structure' ) => 'featured',
					$this->get_control_id( 'show_meta' ) => 'yes',
				),
			)
		);

		$this->update_control(
			'_f_excerpt_length',
			array(
				'default' => apply_filters( 'uael_post_featured_excerpt_length', 0 ),
			)
		);
	}

	/**
	 * Register Style Taxonomy Badge Controls.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_style_term_controls() {

		$this->start_controls_section(
			'section_term_style',
			array(
				'label' => __( 'Taxonomy Badge', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'term_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'    => '5',
						'bottom' => '5',
						'left'   => '15',
						'right'  => '15',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-post__terms' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'term_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-post__terms' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'term_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'term_hover_color',
				array(
					'label'     => __( 'Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms a:hover' => 'color: {{VALUE}};',
						'{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__terms a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'term_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-posts[data-skin="business"] .uael-post__terms' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'term_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					),
					'selector' => '{{WRAPPER}} .uael-post__terms',
				)
			);

			$this->add_control(
				'term_spacing',
				array(
					'label'     => __( 'Bottom Spacing', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 100,
						),
					),
					'default'   => array(
						'size' => 5,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Posts AuthorBox Controls.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function register_authorbox_style_controls() {

		$this->start_controls_section(
			'section_authorbox_field',
			array(
				'label' => __( 'Author Box', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'show_authorbox_meta',
				array(
					'label'        => __( 'Author Box', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'author_image_heading',
				array(
					'label'     => __( 'Image', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'author_image_size',
				array(
					'label'      => __( 'Image Width', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 200,
						),
					),
					'default'    => array(
						'size' => 40,
						'unit' => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-post__authorbox-image img' => 'width: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'image_spacing',
				array(
					'label'     => __( 'Image Spacing', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 100,
						),
					),
					'default'   => array(
						'size' => 10,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}}.uael-post__content-align-left .uael-post__authorbox-image' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.uael-post__content-align-center .uael-post__authorbox-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.uael-post__content-align-right .uael-post__authorbox-image' => 'margin-left: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'author_content_heading',
				array(
					'label'     => __( 'Content', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'writtenby_text',
				array(
					'label'     => __( 'Author Info Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Written by', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'authorbox_desc_color',
				array(
					'label'     => __( 'Info Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__authorbox-desc' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'authorbox_name_color',
				array(
					'label'     => __( 'Author Name Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__authorbox-name, {{WRAPPER}} .uael-post__authorbox-name a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label'     => __( 'Info Text Typography', 'uael' ),
					'name'      => 'authorbox_desc_typography',
					'selector'  => '{{WRAPPER}} .uael-post__authorbox-desc',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label'     => __( 'Author Name Typography', 'uael' ),
					'name'      => 'authorbox_name_typography',
					'selector'  => '{{WRAPPER}} .uael-post__authorbox-name, {{WRAPPER}} .uael-post__authorbox-name a',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'authorbox_spacing',
				array(
					'label'     => __( 'Top Spacing', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 100,
						),
					),
					'default'   => array(
						'size' => 15,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__authorbox-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_authorbox_meta' ) => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render Main HTML.
	 *
	 * @since 1.10.1
	 * @access protected
	 */
	public function render() {

		$settings = $this->parent->get_settings_for_display();

		$skin = Skin_Init::get_instance( $this->get_id() );

		echo wp_kses_post( sanitize_text_field( $skin->render( $this->get_id(), $settings, $this->parent->get_id() ) ) );
	}
}

