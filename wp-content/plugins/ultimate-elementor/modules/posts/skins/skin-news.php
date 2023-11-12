<?php
/**
 * UAEL Grid Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_News
 */
class Skin_News extends Skin_Base {

	/**
	 * Get Skin Slug.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_id() {

		return 'news';
	}

	/**
	 * Get Skin Title.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_title() {

		return __( 'News', 'uael' );
	}

	/**
	 * Register Control Actions.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-posts/news_section_title_field/before_section_end', array( $this, 'register_update_title_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_excerpt_field/before_section_end', array( $this, 'register_update_excerpt_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_cta_field/before_section_end', array( $this, 'register_update_cta_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_design_blog/before_section_end', array( $this, 'update_blog_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_image_field/before_section_end', array( $this, 'register_update_image_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_general_field/before_section_end', array( $this, 'register_update_general_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_design_layout/before_section_end', array( $this, 'register_update_layout_controls' ) );

		add_action( 'elementor/element/uael-posts/news_section_title_style/before_section_end', array( $this, 'register_update_title_style_controls' ) );
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
		$this->register_content_badge_controls();
		$this->register_content_excerpt_controls();
		$this->register_content_cta_controls();

		// Style Controls.
		$this->register_style_layout_controls();
		$this->register_style_blog_controls();
		$this->register_style_pagination_controls();
		$this->register_style_featured_controls();
		$this->register_style_term_controls();
		$this->register_style_title_controls();
		$this->register_style_meta_controls();
		$this->register_style_excerpt_controls();
		$this->register_style_cta_controls();
		$this->register_posts_schema();
		$this->register_style_navigation_controls();
	}

	/**
	 * Update Layout control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_layout_controls() {

		$this->update_control(
			'column_gap',
			array(
				'selectors' => array(
					'{{WRAPPER}} .uael-post-grid .uael-post-wrapper' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .uael-post-grid:not(.uael-post_structure-featured) .uael-post-grid__inner' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .uael-post-grid.uael-post_structure-featured' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->update_control(
			'row_gap',
			array(
				'selectors' => array(
					'{{WRAPPER}} .uael-post-grid .uael-post-wrapper:not(:last-child) .uael-post__bg-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	/**
	 * Register Style Taxonomy Badge Controls.
	 *
	 * @since 1.7.0
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
						'top'    => '2',
						'bottom' => '2',
						'left'   => '6',
						'right'  => '6',
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
					'default'    => array(
						'top'    => '2',
						'bottom' => '2',
						'left'   => '2',
						'right'  => '2',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-post__terms' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'term_alignment',
				array(
					'label'       => __( 'Alignment', 'uael' ),
					'type'        => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options'     => array(
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
					'selectors'   => array(
						'{{WRAPPER}} .uael-post__terms-wrap' => 'text-align: {{VALUE}};',
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
						'{{WRAPPER}} .uael-posts[data-skin="news"] .uael-post__terms' => 'background-color: {{VALUE}};',
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
	 * Register Style Pagination Controls.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_style_pagination_controls() {

		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'pagination' ) => array( 'numbers', 'infinite' ),
				),
			)
		);

			$this->add_control(
				'infinite_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Note: Infinite Load is prevented at the backend. You can see it working in the frontend,', 'uael' ),
					'condition'       => array(
						$this->get_control_id( 'pagination' ) => 'infinite',
					),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'load_more_text',
				array(
					'label'     => __( '"Load More" Label', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Load More', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'infinite',
						$this->get_control_id( 'infinite_event' ) => 'click',
					),
				)
			);

			$this->add_control(
				'pagination_alignment',
				array(
					'label'       => __( 'Alignment', 'uael' ),
					'type'        => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options'     => array(
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
					'selectors'   => array(
						'{{WRAPPER}} .uael-grid-pagination' => 'text-align: {{VALUE}};',
					),
					'condition'   => array(
						$this->get_control_id( 'pagination' ) => 'numbers',
					),
				)
			);

			$this->add_control(
				'infinite_btn_alignment',
				array(
					'label'     => __( 'Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
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
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} .uael-post__load-more-wrap' => 'text-align: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'infinite',
						$this->get_control_id( 'infinite_event' ) => 'click',
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'pagination_style',
				array(
					'label'     => __( 'Pagination Style', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'flat',
					'separator' => 'before',
					'options'   => array(
						'flat'        => __( 'Flat', 'uael' ),
						'transparent' => __( 'Transparent', 'uael' ),
					),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'numbers',
					),
				)
			);

		$this->start_controls_tabs( 'pagination_tabs_style' );

			$this->start_controls_tab(
				'pagination_normal',
				array(
					'label'     => __( 'Normal', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'numbers',
					),
				)
			);

				$this->add_control(
					'pagination_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination a.page-numbers' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'numbers',
						),
					)
				);

				$this->add_control(
					'pagination_background_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#f6f6f6',
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination a.page-numbers' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'pagination' => 'numbers',
							$this->get_control_id( 'pagination_style' ) => 'flat',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'pagination_border',
						'label'     => __( 'Border', 'uael' ),
						'selector'  => '{{WRAPPER}} .uael-grid-pagination a.page-numbers, {{WRAPPER}} .uael-grid-pagination span.page-numbers.current',
						'condition' => array(
							$this->get_control_id( 'pagination' )        => 'numbers',
							$this->get_control_id( 'pagination_style!' ) => 'flat',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'pagination_hover',
				array(
					'label'     => __( 'Hover', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'numbers',
					),
				)
			);

				$this->add_control(
					'pagination_hover_color',
					array(
						'label'     => __( 'Text Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination a.page-numbers:hover' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'numbers',
						),
					)
				);

				$this->add_control(
					'pagination_background_hover_color',
					array(
						'label'     => __( 'Background Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#f6f6f6',
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination a.page-numbers:hover' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' )       => 'numbers',
							$this->get_control_id( 'pagination_style' ) => 'flat',
						),
					)
				);

				$this->add_control(
					'pagination_hover_border_color',
					array(
						'label'     => __( 'Border Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination a.page-numbers:hover' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' )        => 'numbers',
							$this->get_control_id( 'pagination_style!' ) => 'flat',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'pagination_active',
				array(
					'label'     => __( 'Active', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'numbers',
					),
				)
			);

				$this->add_control(
					'pagination_active_color',
					array(
						'label'     => __( 'Text Active Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'numbers',
						),
					)
				);

				$this->add_control(
					'pagination_background_active_color',
					array(
						'label'     => __( 'Background Active Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'background-color: {{VALUE}};',
						),
						'default'   => '#e2e2e2',
						'condition' => array(
							$this->get_control_id( 'pagination' )       => 'numbers',
							$this->get_control_id( 'pagination_style' ) => 'flat',
						),
					)
				);

				$this->add_control(
					'pagination_active_border_color',
					array(
						'label'     => __( 'Border Active Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' )        => 'numbers',
							$this->get_control_id( 'pagination_style!' ) => 'flat',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .uael-grid-pagination a.page-numbers, {{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'pagination' ) => 'numbers',
				),
			)
		);

		$this->add_control(
			'pagination_box_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .uael-grid-pagination a.page-numbers, {{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'pagination' ) => 'numbers',
				),
			)
		);

		$this->add_control(
			'pagination_box_margin',
			array(
				'label'     => __( 'Page Number Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-grid-pagination a.page-numbers, {{WRAPPER}} .uael-grid-pagination span.page-numbers.current' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-grid-pagination .page-numbers:last-child' => 'margin-right: 0;',
				),
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'numbers',
				),
			)
		);

		$this->start_controls_tabs( 'infinite_btn_tabs_style' );

			$this->start_controls_tab(
				'infinite_btn_normal',
				array(
					'label'     => __( 'Normal', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'infinite',
						$this->get_control_id( 'infinite_event' ) => 'click',
					),
				)
			);

				$this->add_control(
					'infinite_btn_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#ffffff',
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-post__load-more' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

				$this->add_control(
					'infinite_btn_background_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__load-more' => 'background-color: {{VALUE}};',
						),
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'infinite_btn_border',
						'label'     => __( 'Border', 'uael' ),
						'selector'  => '{{WRAPPER}} .uael-post__load-more',
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'infinite_btn_hover',
				array(
					'label'     => __( 'Hover', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'pagination' ) => 'infinite',
						$this->get_control_id( 'infinite_event' ) => 'click',
					),
				)
			);

				$this->add_control(
					'infinite_btn_hover_color',
					array(
						'label'     => __( 'Text Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__load-more:hover' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

				$this->add_control(
					'infinite_btn_background_hover_color',
					array(
						'label'     => __( 'Background Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__load-more:hover' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

				$this->add_control(
					'infinite_btn_hover_border_color',
					array(
						'label'     => __( 'Border Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__load-more:hover' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'pagination' ) => 'infinite',
							$this->get_control_id( 'infinite_event' ) => 'click',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'infinite_btn_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
					$this->get_control_id( 'infinite_event' ) => 'click',
				),
			)
		);

		$this->add_control(
			'infinite_btn_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 10,
					'bottom' => 10,
					'left'   => 10,
					'right'  => 10,
					'unit'   => 'px',
				),
				'condition'  => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
					$this->get_control_id( 'infinite_event' ) => 'click',
				),
			)
		);

		$this->add_control(
			'loader_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Note: This Loader is visible only when user clicks on Load More button.', 'uael' ),
				'condition'       => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
					$this->get_control_id( 'infinite_event' ) => 'click',
				),
				'content_classes' => 'uael-editor-doc',
				'separator'       => 'before',
			)
		);

		$this->add_control(
			'loader_color',
			array(
				'label'     => __( 'Loader Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post-inf-loader > div' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
				),
			)
		);

		$this->add_control(
			'loader_size',
			array(
				'label'     => __( 'Loader Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
						'min' => 5,
					),
				),
				'default'   => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post-inf-loader > div' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'load_more_pagination_typography',
				'selector'  => '{{WRAPPER}} .uael-post__load-more',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
					$this->get_control_id( 'infinite_event' ) => 'click',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_typography',
				'selector'  => '{{WRAPPER}} .uael-grid-pagination a.page-numbers, {{WRAPPER}} .uael-grid-pagination span.page-numbers.current',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'numbers',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Taxonomy Badge Controls.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_content_badge_controls() {

		$this->start_controls_section(
			'section_terms_field',
			array(
				'label' => __( 'Taxonomy Badge', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'show_taxonomy',
				array(
					'label'        => __( 'Show Taxonomy Badge', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'terms_to_show',
				array(
					'label'     => __( 'Select Taxonomy', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'category' => __( 'Category', 'uael' ),
						'post_tag' => __( 'Tag', 'uael' ),
					),
					'condition' => array(
						'post_type_filter' => 'post',
						$this->get_control_id( 'show_taxonomy' ) => 'yes',
					),
					'default'   => 'category',
				)
			);

			$this->add_control(
				'max_terms',
				array(
					'label'       => __( 'Max Terms to Show', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 1,
					'label_block' => false,
					'condition'   => array(
						$this->get_control_id( 'show_taxonomy' ) => 'yes',
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_show_term_icon',
				array(
					'label'            => __( 'Term Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => $this->get_control_id( 'show_term_icon' ),
					'condition'        => array(
						$this->get_control_id( 'show_taxonomy' ) => 'yes',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$this->add_control(
				'show_term_icon',
				array(
					'label'     => __( 'Term Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'condition' => array(
						$this->get_control_id( 'show_taxonomy' ) => 'yes',
					),
				)
			);
		}

			$this->add_control(
				'term_divider',
				array(
					'label'     => __( 'Term Divider', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => '|',
					'selectors' => array(
						'{{WRAPPER}} .uael-listing__terms-link:not(:last-child):after' => 'content: "{{VALUE}}"; margin: 0 0.4em;',
					),
					'condition' => array(
						$this->get_control_id( 'show_taxonomy' ) => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Update Layout control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_title_style_controls() {

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
		 * Register featured Posts Controls.
		 *
		 * @since 1.7.0
		 * @access public
		 */
	public function register_content_featured_controls() {

		$this->start_controls_section(
			'section_featured_field',
			array(
				'label' => __( 'Featured Post', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'_f_meta',
				array(
					/* translators: %s label */
					'label'       => __( 'Meta', 'uael' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'default'     => array( 'author', 'date', 'comment' ),
					'label_block' => true,
					'options'     => array(
						'author'   => __( 'Author', 'uael' ),
						'date'     => __( 'Date', 'uael' ),
						'comment'  => __( 'Comment', 'uael' ),
						'category' => __( 'Category', 'uael' ),
						'tag'      => __( 'Tag', 'uael' ),
					),
				)
			);

			$this->add_control(
				'_f_excerpt_length',
				array(
					'label'       => __( 'Featured Excerpt Length', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'label_block' => true,
					'default'     => apply_filters( 'uael_post_featured_excerpt_length', 25 ),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Style Blog Controls.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function register_style_featured_controls() {

		$this->start_controls_section(
			'section_design_featured',
			array(
				'label' => __( 'Featured Post', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'_f_title_color',
				array(
					'label'     => __( 'Title Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__title, {{WRAPPER}} .uael-post-wrapper-featured .uael-post__title a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => '_f_title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .uael-post-wrapper-featured .uael-post__title, {{WRAPPER}} .uael-post-wrapper-featured .uael-post__title a',
				)
			);

			$this->add_control(
				'_f_meta_color',
				array(
					'label'     => __( 'Meta Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__meta-data' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__meta-data svg' => 'fill: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'_f_meta_spacing',
				array(
					'label'     => __( 'Below Meta Spacing', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 100,
						),
					),
					'default'   => array(
						'size' => 13,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'show_meta' => 'yes',
					),
				)
			);

			$this->add_control(
				'_f_excerpt_color',
				array(
					'label'     => __( 'Excerpt Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__excerpt' => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'show_excerpt' ) => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'featured_padding',
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
						'{{WRAPPER}} .uael-post-wrapper-featured .uael-post__content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; bottom:0;',
					),
				)
			);

		$this->end_controls_section();
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
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Left', 'uael' ),
					'right' => __( 'Right', 'uael' ),
					'none'  => __( 'None', 'uael' ),
				),
			)
		);

		$this->update_control(
			'image_size',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'post_stack_on',
			array(
				'label'        => __( 'Responsive Support', 'uael' ),
				'description'  => __( 'Enable this option to stack post image and content on mobile.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-post__news-stack-',
				'render_type'  => 'template',
				'condition'    => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
			)
		);

		$this->update_control(
			'heading_image_hover_options',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
			)
		);

		$this->update_control(
			'image_scale_hover',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__thumbnail:hover a,{{WRAPPER}} .uael-post__thumbnail:hover span,
					{{WRAPPER}}.uael-post__news-stack-yes .uael-post__thumbnail:hover img,
					{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail a,{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail span,
					{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail img' => 'transform: scale({{SIZE}});',
					'{{WRAPPER}}.uael-post__link-complete-yes .uael-post-wrapper-featured .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail img' => 'transform: translate(-50%,-50%) scale({{SIZE}});',
				),
			)
		);

		$this->update_control(
			'image_opacity_hover',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__thumbnail:hover a,{{WRAPPER}} .uael-post__thumbnail:hover span,
					{{WRAPPER}}.uael-post__news-stack-yes .uael-post__thumbnail:hover img,
					{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail a,{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail span,
					{{WRAPPER}}.uael-post__link-complete-yes .uael-post__complete-box-overlay:hover + .uael-post__inner-wrap .uael-post__thumbnail img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->update_control(
			'link_img',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
			)
		);

		$this->update_control(
			'link_new_tab',
			array(
				'condition' => array(
					$this->get_control_id( 'link_img' ) => 'yes',
					$this->get_control_id( 'image_position' ) => array( 'left', 'right' ),
				),
			)
		);
	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_excerpt_controls() {

		$this->update_control(
			'show_excerpt',
			array(
				'default' => '',
			)
		);
	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_cta_controls() {

		$this->update_control(
			'show_cta',
			array(
				'default' => '',
			)
		);
	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_title_controls() {

		$this->update_control(
			'title_tag',
			array(
				'default'   => 'h5',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_general_controls() {

		$this->update_control(
			'posts_per_page',
			array(
				'default' => 4,
			)
		);

		$this->update_control(
			'slides_to_show',
			array(
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 8,
				'description'    => __( 'Note: Number of columns <b>excludes</b> featured post.', 'uael' ),
			)
		);

		$this->update_control(
			'pagination',
			array(
				'options'   => array(
					'none'    => __( 'None', 'uael' ),
					'numbers' => __( 'Numbers', 'uael' ),
				),
				'condition' => array(),
			)
		);

		$this->update_control(
			'max_pages',
			array(
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'numbers',
				),
			)
		);

		$this->remove_control( 'post_structure' );

		$this->remove_control( 'featured_post' );
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
				'selector' => '{{WRAPPER}} .uael-post__inner-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'grid_box_shadow',
				'selector' => '{{WRAPPER}} .uael-post__inner-wrap',
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

