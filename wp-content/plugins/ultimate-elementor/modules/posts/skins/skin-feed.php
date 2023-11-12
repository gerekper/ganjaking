<?php
/**
 * UAEL Feed Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Init;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Classes\UAEL_Posts_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Feed
 */
class Skin_Feed extends Skin_Base {

	/**
	 * Get Skin Slug.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_id() {
		return 'feed';
	}

	/**
	 * Get Skin Title.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Creative Feed', 'uael' );
	}

	/**
	 * Register controls on given actions.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		parent::_register_controls_actions();

		add_action( 'elementor/element/uael-posts/feed_section_general_field/before_section_end', array( $this, 'register_update_general_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_image_field/before_section_end', array( $this, 'register_update_image_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_design_blog/before_section_end', array( $this, 'register_blog_design_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_design_layout/before_section_end', array( $this, 'register_update_layout_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_meta_field/before_section_end', array( $this, 'register_update_meta_field_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_cta_field/before_section_end', array( $this, 'register_update_cta_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_meta_style/before_section_end', array( $this, 'register_update_meta_styles_controls' ) );

		add_action( 'elementor/element/uael-posts/feed_section_title_style/before_section_end', array( $this, 'register_update_title_styles_controls' ) );
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
		$this->register_style_title_controls();
		$this->register_style_meta_controls();
		$this->register_style_term_controls();
		$this->register_style_excerpt_controls();
		$this->register_style_cta_controls();
		$this->register_posts_schema();
		$this->register_style_navigation_controls();
	}

	/**
	 * Register Posts Filter Controls.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_content_filters_controls() {

		$this->start_controls_section(
			'section_filter_masonry',
			array(
				'label'     => __( 'Filterable Tabs', 'uael' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

			$this->add_control(
				'show_filters',
				array(
					'label'              => __( 'Show Filters', 'uael' ),
					'type'               => Controls_Manager::SWITCHER,
					'label_on'           => __( 'Yes', 'uael' ),
					'label_off'          => __( 'No', 'uael' ),
					'return_value'       => 'yes',
					'default'            => 'no',
					'condition'          => array(
						'query_type' => 'custom',
					),
					'frontend_available' => true,
				)
			);

		$post_types = UAEL_Posts_Helper::get_post_types();

		foreach ( $post_types as $key => $type ) {

			// Get all the taxanomies associated with the post type.
			$taxonomy = UAEL_Posts_Helper::get_taxonomy( $key );

			if ( ! empty( $taxonomy ) ) {

				$related_tax = array();

				// Get all taxonomy values under the taxonomy.
				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index );

					$related_tax[ $index ] = $tax->label;
				}

				// Add control for all taxonomies.
				$this->add_control(
					'tax_masonry_' . $key . '_filter',
					array(
						'label'     => __( 'Filter By', 'uael' ),
						'type'      => Controls_Manager::SELECT,
						'options'   => $related_tax,
						'default'   => array_keys( $related_tax )[0],
						'condition' => array(
							'post_type_filter' => $key,
							'query_type'       => 'custom',
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
						'separator' => 'before',
					)
				);
			}
		}

		$this->add_control(
			'filters_all_text',
			array(
				'label'     => __( '"All" Tab Label', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'query_type' => 'custom',
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'default_filter_switch',
			array(
				'label'        => __( 'Default Tab on Page Load', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'label_off'    => __( 'First', 'uael' ),
				'label_on'     => __( 'Custom', 'uael' ),
				'condition'    => array(
					'query_type' => 'custom',
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'default_filter',
			array(
				'label'     => __( 'Enter Category Name', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'query_type' => 'custom',
					$this->get_control_id( 'show_filters' ) => 'yes',
					$this->get_control_id( 'default_filter_switch' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'tabs_dropdown',
			array(
				'label'        => __( 'Responsive Support', 'uael' ),
				'description'  => __( 'Enable this option to display Filterable Tabs in a Dropdown on Mobile.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'render_type'  => 'template',
				'prefix_class' => 'uael-post__filter-align-',
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
				'separator'    => 'before',
				'selectors'    => array(
					'{{WRAPPER}} .uael-post__header-filters' => 'text-align: {{VALUE}};',
					'(mobile){{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown' => 'text-align: {{VALUE}};',
				),
				'condition'    => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'filter_tabs_style' );

			$this->start_controls_tab(
				'filter_normal',
				array(
					'label'     => __( 'Normal', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'show_filters' ) => 'yes',
					),
				)
			);

				$this->add_control(
					'filter_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown-button,{{WRAPPER}} .uael-post__header-filter' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

				$this->add_control(
					'filter_background_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown-button,{{WRAPPER}} .uael-post__header-filter' => 'background-color: {{VALUE}};',
						),
						'default'   => '#e4e4e4',
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'filter_border',
						'label'     => __( 'Border', 'uael' ),
						'selector'  => '{{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown-button,{{WRAPPER}} .uael-post__header-filter',
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'filter_active',
				array(
					'label'     => __( 'Active', 'uael' ),
					'condition' => array(
						$this->get_control_id( 'show_filters' ) => 'yes',
					),
				)
			);

				$this->add_control(
					'filter_active_color',
					array(
						'label'     => __( 'Text Active / Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#ffffff',
						'selectors' => array(
							'{{WRAPPER}} .uael-post__header-filter.uael-filter__current, {{WRAPPER}} .uael-post__header-filters .uael-post__header-filter:hover' => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

				$this->add_control(
					'filter_background_active_color',
					array(
						'label'     => __( 'Background Active / Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__header-filters .uael-post__header-filter.uael-filter__current, {{WRAPPER}} .uael-post__header-filters .uael-post__header-filter:hover' => 'background-color: {{VALUE}};',
						),
						'default'   => '#333333',
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

				$this->add_control(
					'filter_active_border_color',
					array(
						'label'     => __( 'Border Active / Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-post__header-filter.uael-filter__current, {{WRAPPER}} .uael-post__header-filter:hover' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_control_id( 'show_filters' ) => 'yes',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'filter_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown-button, {{WRAPPER}} .uael-post__header-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'(mobile){{WRAPPER}} .uael-posts-tabs-dropdown .uael-post__header-filter' => 'border-radius: 0px;',
				),
				'condition'  => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'filter_padding',
			array(
				'label'      => __( 'Filter Tab Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__header-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 4,
					'bottom' => 4,
					'left'   => 14,
					'right'  => 14,
					'unit'   => 'px',
				),
				'condition'  => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'filter_inner_padding',
			array(
				'label'     => __( 'Spacing Between Tabs', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 5,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__header-filter' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-post__header-filter:last-child' => 'margin-right: 0;',
					'(mobile){{WRAPPER}} .uael-posts-tabs-dropdown .uael-post__header-filter' => 'margin-right: 0px; margin-bottom: 0px;',
				),
				'condition' => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'filter_bottom_padding',
			array(
				'label'     => __( 'Filter Bottom Spacing', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__header-filters' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .uael-posts-tabs-dropdown .uael-post__header-filters' => 'padding-bottom: 0px;',
				),
				'condition' => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_separator_width',
			array(
				'label'     => __( 'Filter Separator', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__header-filters' => 'border-bottom: {{SIZE}}{{UNIT}} solid #B7B7BF;',
					'(mobile){{WRAPPER}} .uael-posts-tabs-dropdown .uael-post__header-filters' => 'border: 0px solid;',
				),
				'condition' => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_separator_color',
			array(
				'label'     => __( 'Filter Separator Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-post__header-filters' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'filter_typography',
				'selector'  => '{{WRAPPER}} .uael-posts-tabs-dropdown .uael-filters-dropdown-button,{{WRAPPER}} .uael-post__header-filter',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'condition' => array(
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Style Pagination Controls.
	 *
	 * @since 1.7.2
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
					'raw'             => __( 'Note: Infinite Load is prevented at the backend. You can see it working in the frontend.', 'uael' ),
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
	 * Update Meta control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_meta_field_controls() {

		$this->update_control(
			'show_comments',
			array(
				'default' => '',
			)
		);
	}

	/**
	 * Update Meta control.
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
	 * Update Meta control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_meta_styles_controls() {

		$this->update_control(
			'meta_spacing',
			array(
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
			)
		);
	}

	/**
	 * Update Meta control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_title_styles_controls() {

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
						'top'    => '3',
						'bottom' => '3',
						'left'   => '10',
						'right'  => '10',
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
				'term_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-post__terms a:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'term_hover_color',
				array(
					'label'     => __( 'Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
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
					'default'   => '#e4e4e4',
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms' => 'background-color: {{VALUE}};',
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
						'size' => 10,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-post__terms' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Update General control.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function register_update_general_controls() {

		$this->remove_control( 'post_structure' );
		$this->remove_control( 'show_filters' );
		$this->remove_control( 'slides_to_show' );

		$this->update_control(
			'posts_per_page',
			array(
				'default' => 3,
			)
		);

		$this->update_control(
			'pagination',
			array(
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

		$this->update_control(
			'infinite_event',
			array(
				'condition' => array(
					$this->get_control_id( 'pagination' ) => 'infinite',
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
				'default' => 'left',
				'options' => array(
					'left' => __( 'Left', 'uael' ),
					'none' => __( 'None', 'uael' ),
				),
			)
		);

		$this->update_control(
			'image_size',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => 'left',
				),
			)
		);

		$this->update_control(
			'heading_image_hover_options',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => 'left',
				),
			)
		);

		$this->update_control(
			'image_scale_hover',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => 'left',
				),
			)
		);

		$this->update_control(
			'image_opacity_hover',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => 'left',
				),
			)
		);

		$this->update_control(
			'link_img',
			array(
				'condition' => array(
					$this->get_control_id( 'image_position' ) => 'left',
				),
			)
		);

		$this->update_control(
			'link_new_tab',
			array(
				'condition' => array(
					$this->get_control_id( 'link_img' ) => 'yes',
					$this->get_control_id( 'image_position' ) => 'left',
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
				'selectors' => array(
					'{{WRAPPER}} .uael-post-wrapper' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .uael-post__separator-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->remove_control( 'column_gap' );

		$this->update_control(
			'row_gap',
			array(
				'range'   => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 50,
				),
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
				'label'      => __( 'Separator Width', 'uael' ),
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
					'size' => 25,
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
						'max' => 100,
					),
				),
				'default'   => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-post__separator' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .uael-post__separator' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_alignment',
			array(
				'label'        => __( 'Separator Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => true,
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
				'prefix_class' => 'uael-post__separator-',
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
			'blog_bg_color',
			array(
				'label'     => __( 'Content Background Color', 'uael' ),
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .uael-post__content-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inner_blog_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f6f6f6',
				'selectors' => array(
					'{{WRAPPER}} .uael-post__bg-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->update_control(
			'blog_padding',
			array(
				'label'   => __( 'Content Padding', 'uael' ),
				'default' => array(
					'top'    => '40',
					'bottom' => '40',
					'right'  => '40',
					'left'   => '40',
					'unit'   => 'px',
				),
			)
		);

		$this->add_control(
			'inner_blog_padding',
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
					'{{WRAPPER}} .uael-post__content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'selector' => '{{WRAPPER}} .uael-post__content-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'feed_box_shadow',
				'selector' => '{{WRAPPER}} .uael-post__content-wrap',
			)
		);

		$this->add_control(
			'feed_max_width',
			array(
				'label'      => __( 'Content Max Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'size' => 50,
					'unit' => '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 30,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__inner-wrap:not(.uael-post__noimage) .uael-post__content-wrap' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-post__thumbnail' => 'width: calc( 100% - {{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'feed_lift_up',
			array(
				'label'      => __( 'Content Box Padding', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-post__inner-wrap:not(.uael-post__noimage) .uael-post__content-wrap' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-post__inner-wrap.uael-post__noimage' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-post-wrapper .uael-post__inner-wrap:not(.uael-post__noimage) .uael-post__content-wrap' => 'margin-left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-post-wrapper:first-child' => 'margin-top: {{SIZE}}{{UNIT}};',
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
