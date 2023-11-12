<?php
/**
 * UAEL Business Reviews Skin - Base.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessReviews\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Base
 *
 * @property Reviews $parent
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Register control actions.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_info_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_date_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_rating_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_content_controls' ), 20 );

		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_box_style_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_spacing_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_navigation_controls' ), 20 );
		add_action( 'elementor/element/uael-business-reviews/section_filters_controls/after_section_end', array( $this, 'register_typography_controls' ), 20 );
	}

	/**
	 * Register Business Reviews Image Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_info_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_info_controls',
			array(
				'label' => __( 'Reviewer Info', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'reviewer_image',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'        => sprintf( __( '%1$sReviewer Image%2$s', 'uael' ), '<b>', '</b>' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'prefix_class' => 'uael-review-image-enable-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'image_align',
				array(
					'label'     => __( 'Image Position', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'left',
					'options'   => array(
						'top'      => __( 'Above Name', 'uael' ),
						'left'     => __( 'Left of Name', 'uael' ),
						'all_left' => __( 'Left of all content', 'uael' ),
					),
					'condition' => array(
						$this->get_control_id( 'reviewer_image' ) => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'image_size',
				array(
					'label'              => __( 'Image Size', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => array(
						'size' => 60,
					),
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 130,
						),
					),
					'condition'          => array(
						$this->get_control_id( 'reviewer_image' ) => 'yes',
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-review-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			// This Overall alignment control in case of image top alignment condition.
			$this->add_control(
				'overall_align',
				array(
					'label'        => __( 'Overall Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'      => 'center',
					'toggle'       => false,
					'condition'    => array(
						$this->get_control_id( 'image_align' )    => 'top',
					),
					'prefix_class' => 'uael-reviews-align-',
				)
			);

			// This Overall alignment control in case of image left and all left alignment condition.
			$this->add_control(
				'overall_alignment_left',
				array(
					'label'        => __( 'Overall Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'      => 'center',
					'toggle'       => false,
					'condition'    => array(
						$this->get_control_id( 'reviewer_image' ) . '!'     => 'yes',
						$this->get_control_id( 'image_align' ) . '!'        => 'top',
					),
					'prefix_class' => 'uael-reviews-align-',
				)
			);

			$this->add_control(
				'reviewer_name',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'        => sprintf( __( '%1$sReviewer Name%2$s', 'uael' ), '<b>', '</b>' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'reviewer_name_link',
				array(
					'label'        => __( 'Link Name', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'condition'    => array(
						$this->get_control_id( 'reviewer_name' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'reviewer_name_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-reviewer-name a, {{WRAPPER}} .uael-reviewer-name' => 'color: {{VALUE}}',
					),
					'condition' => array(
						$this->get_control_id( 'reviewer_name' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'review_source_icon',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'        => sprintf( __( '%1$sReview Source Icon%2$s', 'uael' ), '<b>', '</b>' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
					'render_type'  => 'template',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Business Reviews Date Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_date_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_date_controls',
			array(
				'label' => __( 'Review Date', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'review_date',
				array(
					'label'        => __( 'Review Date', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'review_date_type',
				array(
					'label'     => __( 'Select Type', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'relative',
					'options'   => array(
						'default'  => __( 'Numeric', 'uael' ),
						'relative' => __( 'Relative', 'uael' ),
					),
					'condition' => array(
						$this->get_control_id( 'review_date' ) => 'yes',
						'review_type' => 'google',
					),
				)
			);

			$this->add_control(
				'reviewer_date_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'default'   => '#adadad',
					'selectors' => array(
						'{{WRAPPER}} .uael-review-time' => 'color: {{VALUE}}',
					),
					'condition' => array(
						$this->get_control_id( 'review_date' ) => 'yes',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Register Business Reviews Star Rating Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_rating_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_rating_controls',
			array(
				'label' => __( 'Rating', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'review_rating',
				array(
					'label'        => __( 'Star Rating', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'select_star_style',
				array(
					'label'     => __( 'Star Icon Style', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'custom',
					'options'   => array(
						'default' => __( 'Default', 'uael' ),
						'custom'  => __( 'Custom', 'uael' ),
					),
					'condition' => array(
						$this->get_control_id( 'review_rating' ) => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'icon_size',
				array(
					'label'              => __( 'Icon Size', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-review .uael-star-full, {{WRAPPER}} .uael-review .uael-star-empty' => 'font-size: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						$this->get_control_id( 'review_rating' ) => 'yes',
						$this->get_control_id( 'select_star_style' ) => 'custom',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'stars_color',
				array(
					'label'     => __( 'Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-review .uael-star-full' => 'color: {{VALUE}}',
					),
					'condition' => array(
						$this->get_control_id( 'review_rating' ) => 'yes',
						$this->get_control_id( 'select_star_style' ) => 'custom',
					),
				)
			);

			$this->add_control(
				'stars_unmarked_color',
				array(
					'label'     => __( 'Unmarked Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-review .uael-star-empty' => 'color: {{VALUE}}',
					),
					'condition' => array(
						$this->get_control_id( 'review_rating' ) => 'yes',
						$this->get_control_id( 'select_star_style' ) => 'custom',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Business Reviews Star Rating Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_content_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_content_controls',
			array(
				'label' => __( 'Review Text', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'review_content',
				array(
					'label'        => __( 'Review Text', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'uael' ),
					'label_off'    => __( 'Hide', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'reviewer_content_color',
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

			$this->add_control(
				'review_content_length',
				array(
					'label'     => __( 'Text Length', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 25,
					'condition' => array(
						$this->get_control_id( 'review_content' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'yelp_review_length_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => __( 'Yelp API allows fetching maximum 160 characters from a review.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type!' => 'google',
					),
				)
			);

			$this->add_control(
				'read_more',
				array(
					'label'     => __( 'Read More Text', 'uael' ),
					'default'   => __( 'Read More »', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						$this->get_control_id( 'review_content' ) => 'yes',
					),
				)
			);

			$this->add_control(
				'reviewer_readmore_color',
				array(
					'label'     => __( 'Read More Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors' => array(
						'{{WRAPPER}} a.uael-reviews-read-more' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'review_content' ) => 'yes',
						$this->get_control_id( 'read_more' ) . '!' => '',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Spacing Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_spacing_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'column_gap',
				array(
					'label'              => __( 'Columns Gap', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => array(
						'size' => 25,
					),
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-review-wrap' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'row_gap',
				array(
					'label'              => __( 'Rows Gap', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => array(
						'size' => 25,
					),
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-review-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						'review_structure!' => 'carousel',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'reviewer_image_spacing',
				array(
					'label'              => __( 'Image Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'separator'          => 'before',
					'selectors'          => array(
						'{{WRAPPER}} .uael-review-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-review-image-left .uael-review-image, {{WRAPPER}} .uael-review-image-all_left .uael-review-image' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: 0px;',
					),
					'condition'          => array(
						$this->get_control_id( 'reviewer_image' ) => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'reviewer_name_spacing',
				array(
					'label'              => __( 'Name Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-reviewer-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'star_spacing',
				array(
					'label'              => __( 'Star Rating Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-star-rating__wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'date_spacing',
				array(
					'label'              => __( 'Review Date Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-review-time' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						$this->get_control_id( 'review_date' ) => 'yes',
					),
					'frontend_available' => true,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Business Reviews Date Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_box_style_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_styling',
			array(
				'label' => __( 'Box', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'block_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fafafa',
					'selectors' => array(
						'{{WRAPPER}} .uael-review' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'block_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'    => '30',
						'bottom' => '30',
						'right'  => '30',
						'left'   => '30',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'block_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'    => '5',
						'bottom' => '5',
						'right'  => '5',
						'left'   => '5',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register carousel navigation controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_navigation_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation'       => array( 'arrows', 'dots', 'both' ),
					'review_structure' => 'carousel',
				),
			)
		);

			$this->add_control(
				'heading_style_arrows',
				array(
					'label'     => __( 'Arrows', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'navigation'       => array( 'arrows', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'arrows_position',
				array(
					'label'        => __( 'Arrows Position', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'outside',
					'options'      => array(
						'inside'  => __( 'Inside', 'uael' ),
						'outside' => __( 'Outside', 'uael' ),
					),
					'prefix_class' => 'uael-reviews-carousel-arrow-',
					'condition'    => array(
						'navigation'       => array( 'arrows', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'arrows_size',
				array(
					'label'     => __( 'Arrows Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 20,
							'max' => 60,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'navigation'       => array( 'arrows', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'arrows_color',
				array(
					'label'     => __( 'Arrows Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'navigation'       => array( 'arrows', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'heading_style_dots',
				array(
					'label'     => __( 'Dots', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'navigation'       => array( 'dots', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'dots_size',
				array(
					'label'     => __( 'Dots Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 5,
							'max' => 15,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'navigation'       => array( 'dots', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'dots_color',
				array(
					'label'     => __( 'Dots Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'navigation'       => array( 'dots', 'both' ),
						'review_structure' => 'carousel',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Typography Controls.
	 *
	 * @param Widget_Base $widget Current Widget object.
	 * @since 1.13.0
	 * @access public
	 */
	public function register_typography_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->start_controls_section(
			'section_typography',
			array(
				'label' => __( 'Typography', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'name_typography',
					'label'    => __( 'Reviewer Name', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .uael-reviewer-name a, {{WRAPPER}} .uael-reviewer-name',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'date_time_typography',
					'label'    => __( 'Review Date', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-review-time',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'content_typography',
					'label'    => __( 'Review Content', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-review-content',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'readmore_typography',
					'label'    => __( 'Read More Text', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} .uael-reviews-read-more',
				)
			);

		$this->end_controls_section();
	}

}
