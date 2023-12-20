<?php

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( ! class_exists( 'Theplus_Elements_Widgets' ) ) {

	/**
	 * Define Theplus_Elements_Widgets class
	 */
	class Theplus_Elements_Widgets extends Widget_Base {

		public function __construct() {
			parent::__construct();
			$this->add_actions();
		}

		public function get_name() {
			return 'plus-elementor-widget';
		}

		public function register_controls_widget_magic_scroll( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);
			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}
			$widget->add_control(
				'magic_scroll',
				array(
					'label'       => esc_html__( 'Magic Scroll', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
				)
			);
			$widget->add_group_control(
				\Theplus_Magic_Scroll_Option_Style_Group::get_type(),
				array(
					'label'       => esc_html__( 'Scroll Options', 'theplus' ),
					'name'        => 'scroll_option',
					'render_type' => 'template',
					'condition'   => array(
						'magic_scroll' => array( 'yes' ),
					),
				)
			);
			$widget->start_controls_tabs( 'tabs_magic_scroll' );
			$widget->start_controls_tab(
				'tab_scroll_from',
				array(
					'label'     => esc_html__( 'Initial', 'theplus' ),
					'condition' => array(
						'magic_scroll' => array( 'yes' ),
					),
				)
			);
			$widget->add_group_control(
				\Theplus_Magic_Scroll_From_Style_Group::get_type(),
				array(
					'label'     => esc_html__( 'Initial Position', 'theplus' ),
					'name'      => 'scroll_from',
					'condition' => array(
						'magic_scroll' => array( 'yes' ),
					),
				)
			);
			$widget->end_controls_tab();
			$widget->start_controls_tab(
				'tab_scroll_to',
				array(
					'label'     => esc_html__( 'Final', 'theplus' ),
					'condition' => array(
						'magic_scroll' => array( 'yes' ),
					),
				)
			);
			$widget->add_group_control(
				\Theplus_Magic_Scroll_To_Style_Group::get_type(),
				array(
					'label'     => esc_html__( 'Final Position', 'theplus' ),
					'name'      => 'scroll_to',
					'condition' => array(
						'magic_scroll' => array( 'yes' ),
					),
				)
			);

			$widget->end_controls_tab();
			$widget->end_controls_tabs();
		}

		public function register_controls_widget_gsap_scroll( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv',
				'section_plus_extra_adv_hs', /* Section */
			);
			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}
			$widget->add_control(
				'gsapScroll',
				array(
					'label'       => esc_html__( 'Horizontal Scroll', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
					'default'     => '',
				)
			);

			$gsapRepeater = new \Elementor\Repeater();
			$gsapRepeater->add_control(
				'hsscrollOpttp',
				array(
					'label'     => __( 'Scroll Options', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'triggerStarth',
					array(
						'label'       => esc_html__( 'Trigger Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.01,
							),
						),
						'default'     => array(
							'size' => 0.5,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'hsscrollOpttp' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'triggerEndh',
					array(
						'label'       => esc_html__( 'Trigger End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.01,
							),
						),
						'default'     => array(
							'size' => 0.4,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'hsscrollOpttp' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scrollStarth',
					array(
						'label'       => esc_html__( 'Scroll Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.01,
							),
						),
						'default'     => array(
							'unit' => 'px',
							'size' => 0.8,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'hsscrollOpttp' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scrollEndh',
					array(
						'label'       => esc_html__( 'Scroll End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.01,
							),
						),
						'default'     => array(
							'size' => 0.2,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'hsscrollOpttp' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'vertical',
				array(
					'label'     => __( 'Vertical', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'verticalStart',
					array(
						'label'       => esc_html__( 'Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => -200,
								'max'  => 200,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'vertical' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'verticalEnd',
					array(
						'label'       => esc_html__( 'End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => -200,
								'max'  => 200,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 50,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'vertical' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'horizontal',
				array(
					'label'     => __( 'Horizontal', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'horiStart',
					array(
						'label'       => esc_html__( 'Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'separator'   => 'before',
						'range'       => array(
							'px' => array(
								'min'  => -200,
								'max'  => 200,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'horizontal' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'horiEnd',
					array(
						'label'       => esc_html__( 'End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -200,
								'max'  => 200,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 50,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'horizontal' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'opacity',
				array(
					'label'     => __( 'Opacity', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'opacityStart',
					array(
						'label'       => esc_html__( 'Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 0.2,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'opacity' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'opacityEnd',
					array(
						'label'       => esc_html__( 'End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => 0,
								'max'  => 1,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'opacity' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'rotate',
				array(
					'label'     => __( 'Rotate', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_control(
					'positiontpsr',
					array(
						'label'     => esc_html__( 'Position', 'theplus' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => 'center center',
						'options'   => array(
							'left top'      => esc_html__( 'Left Top', 'theplus' ),
							'left center'   => esc_html__( 'Left Center', 'theplus' ),
							'left bottom'   => esc_html__( 'Left Bottom', 'theplus' ),
							'center top'    => esc_html__( 'Center Top', 'theplus' ),
							'center center' => esc_html__( 'Center Center', 'theplus' ),
							'center bottom' => esc_html__( 'Center Bottom', 'theplus' ),
							'right top'     => esc_html__( 'Right Top', 'theplus' ),
							'right center'  => esc_html__( 'Right Center', 'theplus' ),
							'right bottom'  => esc_html__( 'Right Bottom', 'theplus' ),
						),
						'condition' => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateXstart',
					array(
						'label'       => esc_html__( 'RotateX Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateXEnd',
					array(
						'label'       => esc_html__( 'RotateX End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateYStart',
					array(
						'label'       => esc_html__( 'RotateY Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateYEnd',
					array(
						'label'       => esc_html__( 'RotateY End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateZStart',
					array(
						'label'       => esc_html__( 'RotateZ Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'rotateZEnd',
					array(
						'label'       => esc_html__( 'RotateZ End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -360,
								'max'  => 360,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'rotate' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'scalehs',
				array(
					'label'     => __( 'Scale', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'scaleXhsss',
					array(
						'label'       => esc_html__( 'ScaleX Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scaleXhsse',
					array(
						'label'       => esc_html__( 'ScaleX End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scaleYhsss',
					array(
						'label'       => esc_html__( 'ScaleY Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scaleYhsse',
					array(
						'label'       => esc_html__( 'ScaleY End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scaleZhss',
					array(
						'label'       => esc_html__( 'ScaleZ Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'scaleZhse',
					array(
						'label'       => esc_html__( 'ScaleZ End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 1,
							),
						),
						'default'     => array(
							'size' => 1,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'scalehs' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'skewhs',
				array(
					'label'     => __( 'Skew', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'skewXhsss',
					array(
						'label'       => esc_html__( 'SkewX Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'skewhs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'skewXhsse',
					array(
						'label'       => esc_html__( 'SkewX End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'skewhs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'skewYhsss',
					array(
						'label'       => esc_html__( 'SkewY Start', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'skewhs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'skewYhsse',
					array(
						'label'       => esc_html__( 'SkewY End', 'theplus' ),
						'type'        => Controls_Manager::SLIDER,
						'range'       => array(
							'px' => array(
								'min'  => -10,
								'max'  => 10,
								'step' => 0.1,
							),
						),
						'default'     => array(
							'size' => 0,
						),
						'render_type' => 'ui',
						'condition'   => array(
							'skewhs' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'borderHs',
				array(
					'label'     => __( 'Border Radius', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_responsive_control(
					'fromBRhs',
					array(
						'label'      => esc_html__( 'Start Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'condition'  => array(
							'borderHs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_responsive_control(
					'toBRhs',
					array(
						'label'      => esc_html__( 'End Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'condition'  => array(
							'borderHs' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'bgColorhs',
				array(
					'label'     => __( 'Background Color', 'theplus' ),
					'type'      => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => __( 'Enable', 'theplus' ),
					'label_on'  => __( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->start_popover();
				$gsapRepeater->add_control(
					'fromColorhs',
					array(
						'label'     => esc_html__( 'Start Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'condition' => array(
							'bgColorhs' => array( 'yes' ),
						),
					)
				);
				$gsapRepeater->add_control(
					'toColorhs',
					array(
						'label'     => esc_html__( 'End Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'condition' => array(
							'bgColorhs' => array( 'yes' ),
						),
					)
				);
			$gsapRepeater->end_popover();

			$gsapRepeater->add_control(
				'hsdeveloptp',
				array(
					'label'     => esc_html__( 'Developer', 'theplus' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => esc_html__( 'Enable', 'theplus' ),
					'label_off' => esc_html__( 'Disable', 'theplus' ),
					'default'   => '',
				)
			);
			$gsapRepeater->add_control(
				'hsdevNametp',
				array(
					'label'       => esc_html__( 'Trigger Name', 'theplus' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active' => true,
					),
					'default'     => '',
					'placeholder' => esc_html__( 'You can set your unique start and end trigger name.', 'theplus' ),
					'condition'   => array(
						'hsdeveloptp' => array( 'yes' ),
					),
				)
			);

			$widget->add_control(
				'GSAPFrame',
				array(
					'label'     => esc_html__( 'Add Frame', 'theplus' ),
					'type'      => Controls_Manager::REPEATER,
					'default'   => array(
						array(),
					),
					'fields'    => $gsapRepeater->get_controls(),
					'condition' => array(
						'gsapScroll' => array( 'yes' ),
					),
				)
			);
			$widget->add_control(
				'HSwidth',
				array(
					'label'     => esc_html__( 'Visibility', 'theplus' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => esc_html__( 'On', 'theplus' ),
					'label_off' => esc_html__( 'Off', 'theplus' ),
					'default'   => '',
					'condition' => array(
						'gsapScroll' => 'yes',
					),
				)
			);
			$widget->add_control(
				'resWidth',
				array(
					'label'       => esc_html__( 'Responsive Width', 'theplus' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 300,
					'max'         => 2000,
					'step'        => 5,
					'default'     => 300,
					'description' => esc_html__( 'ex. 900 < Scroll Normal Site', 'theplus' ),
					'condition'   => array(
						'HSwidth'    => 'yes',
						'gsapScroll' => 'yes',
					),
				)
			);
			$widget->add_control(
				'effeNotice',
				array(
					'type'        => Controls_Manager::RAW_HTML,
					'raw'         => '<p class="tp-controller-notice"><i>These effects will exclusively work with our Horizontal Scroll. This enables you to custom animate widgets based on the scroll viewport during scrolling.
					</i></p>',
					'label_block' => true,
				)
			);
		}

		public function register_controls_widget_tooltip( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);

			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}

			$widget->add_control(
				'plus_tooltip',
				array(
					'label'       => esc_html__( 'Tooltip', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
				)
			);

			$widget->start_controls_tabs( 'plus_tooltip_tabs' );

			$widget->start_controls_tab(
				'plus_tooltip_content_tab',
				array(
					'label'       => esc_html__( 'Content', 'theplus' ),
					'render_type' => 'template',
					'condition'   => array(
						'plus_tooltip' => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_tooltip_content_type',
				array(
					'label'       => esc_html__( 'Content Type', 'theplus' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'normal_desc',
					'options'     => array(
						'normal_desc'     => esc_html__( 'Text Content', 'theplus' ),
						'content_wysiwyg' => esc_html__( 'WYSIWYG Editor', 'theplus' ),
					),
					'render_type' => 'template',
					'condition'   => array(
						'plus_tooltip' => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_tooltip_content_desc',
				array(
					'label'     => esc_html__( 'Description', 'theplus' ),
					'type'      => Controls_Manager::TEXTAREA,
					'rows'      => 5,
					'default'   => esc_html__( 'Luctus nec ullamcorper mattis', 'theplus' ),
					'condition' => array(
						'plus_tooltip_content_type' => 'normal_desc',
						'plus_tooltip'              => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_tooltip_content_wysiwyg',
				array(
					'label'       => esc_html__( 'Tooltip Content', 'theplus' ),
					'type'        => Controls_Manager::WYSIWYG,
					'default'     => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
					'render_type' => 'template',
					'condition'   => array(
						'plus_tooltip_content_type' => 'content_wysiwyg',
						'plus_tooltip'              => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_tooltip_content_align',
				array(
					'label'     => esc_html__( 'Text Alignment', 'theplus' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => array(
						'left'   => array(
							'title' => esc_html__( 'Left', 'theplus' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'theplus' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Right', 'theplus' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
					),
					'condition' => array(
						'plus_tooltip_content_type' => 'normal_desc',
						'plus_tooltip'              => 'yes',
					),
				)
			);
			$widget->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'plus_tooltip_content_typography',
					'selector'  => '{{WRAPPER}} .tippy-tooltip .tippy-content',
					'condition' => array(
						'plus_tooltip_content_type' => array( 'normal_desc', 'content_wysiwyg' ),
						'plus_tooltip'              => 'yes',
					),
				)
			);

			$widget->add_control(
				'plus_tooltip_content_color',
				array(
					'label'     => esc_html__( 'Text Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .tippy-tooltip .tippy-content,{{WRAPPER}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
					),
					'condition' => array(
						'plus_tooltip_content_type' => array( 'normal_desc', 'content_wysiwyg' ),
						'plus_tooltip'              => 'yes',
					),
				)
			);
			$widget->end_controls_tab();

			$widget->start_controls_tab(
				'plus_tooltip_styles_tab',
				array(
					'label'     => esc_html__( 'Style', 'theplus' ),
					'condition' => array(
						'plus_tooltip' => 'yes',
					),
				)
			);
			$widget->add_group_control(
				\Theplus_Tooltips_Option_Group::get_type(),
				array(
					'label'       => esc_html__( 'Tooltip Options', 'theplus' ),
					'name'        => 'tooltip_opt',
					'render_type' => 'template',
					'condition'   => array(
						'plus_tooltip' => array( 'yes' ),
					),
				)
			);
			$widget->add_group_control(
				\Theplus_Tooltips_Option_Style_Group::get_type(),
				array(
					'label'       => esc_html__( 'Style Options', 'theplus' ),
					'name'        => 'tooltip_style',
					'render_type' => 'template',
					'condition'   => array(
						'plus_tooltip' => array( 'yes' ),
					),
				)
			);
			$widget->end_controls_tab();
			$widget->end_controls_tabs();
		}

		public function register_controls_widget_mouseparallax( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);

			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}

			$widget->add_control(
				'plus_mouse_move_parallax',
				array(
					'label'       => esc_html__( 'Mouse Move Parallax', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
				)
			);
			$widget->add_group_control(
				\Theplus_Mouse_Move_Parallax_Group::get_type(),
				array(
					'label'       => esc_html__( 'Parallax Options', 'theplus' ),
					'name'        => 'plus_mouse_parallax',
					'render_type' => 'template',
					'condition'   => array(
						'plus_mouse_move_parallax' => array( 'yes' ),
					),
				)
			);
		}

		public function register_controls_widget_tilt_parallax( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);

			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}

			$widget->add_control(
				'plus_tilt_parallax',
				array(
					'label'       => esc_html__( 'Tilt 3D Parallax', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
				)
			);
			$widget->add_group_control(
				\Theplus_Tilt_Parallax_Group::get_type(),
				array(
					'label'       => esc_html__( 'Tilt Options', 'theplus' ),
					'name'        => 'plus_tilt_opt',
					'render_type' => 'template',
					'condition'   => array(
						'plus_tilt_parallax' => array( 'yes' ),
					),
				)
			);
		}
		public function register_controls_widget_reveal_effect( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);

			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}

			$widget->add_control(
				'plus_overlay_effect',
				array(
					'label'       => esc_html__( 'Overlay Special Effect', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
				)
			);
			$widget->add_group_control(
				\Theplus_Overlay_Special_Effect_Group::get_type(),
				array(
					'label'       => esc_html__( 'Overlay Color', 'theplus' ),
					'name'        => 'plus_overlay_spcial',
					'render_type' => 'template',
					'condition'   => array(
						'plus_overlay_effect' => array( 'yes' ),
					),
				)
			);
		}

		public function register_controls_widget_continuous_animation( $widget, $widget_id, $args ) {
			static $widgets = array(
				'section_plus_extra_adv', /* Section */
			);

			if ( ! in_array( $widget_id, $widgets ) ) {
				return;
			}

			$widget->add_control(
				'plus_continuous_animation',
				array(
					'label'       => esc_html__( 'Continuous Animation', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'separator'   => 'before',
				)
			);
			$widget->add_control(
				'plus_animation_effect',
				array(
					'label'       => esc_html__( 'Animation Effect', 'theplus' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'pulse',
					'options'     => array(
						'pulse'    => esc_html__( 'Pulse', 'theplus' ),
						'floating' => esc_html__( 'Floating', 'theplus' ),
						'tossing'  => esc_html__( 'Tossing', 'theplus' ),
						'rotating' => esc_html__( 'Rotating', 'theplus' ),
					),
					'render_type' => 'template',
					'condition'   => array(
						'plus_continuous_animation' => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_animation_hover',
				array(
					'label'       => esc_html__( 'Hover Animation', 'theplus' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_on'    => esc_html__( 'Yes', 'theplus' ),
					'label_off'   => esc_html__( 'No', 'theplus' ),
					'render_type' => 'template',
					'condition'   => array(
						'plus_continuous_animation' => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_animation_duration',
				array(
					'label'      => esc_html__( 'Duration Time', 'theplus' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => 's',
					'range'      => array(
						's' => array(
							'min'  => 0.5,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'default'    => array(
						'unit' => 's',
						'size' => 2.5,
					),
					'selectors'  => array(
						'{{WRAPPER}} .plus-widget-wrapper' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'plus_continuous_animation' => 'yes',
					),
				)
			);
			$widget->add_control(
				'plus_transform_origin',
				array(
					'label'       => esc_html__( 'Transform Origin', 'theplus' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'center center',
					'options'     => array(
						'top left'      => esc_html__( 'Top Left', 'theplus' ),
						'top center"'   => esc_html__( 'Top Center', 'theplus' ),
						'top right'     => esc_html__( 'Top Right', 'theplus' ),
						'center left'   => esc_html__( 'Center Left', 'theplus' ),
						'center center' => esc_html__( 'Center Center', 'theplus' ),
						'center right'  => esc_html__( 'Center Right', 'theplus' ),
						'bottom left'   => esc_html__( 'Bottom Left', 'theplus' ),
						'bottom center' => esc_html__( 'Bottom Center', 'theplus' ),
						'bottom right'  => esc_html__( 'Bottom Right', 'theplus' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .plus-widget-wrapper' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
					),
					'render_type' => 'template',
					'condition'   => array(
						'plus_continuous_animation' => 'yes',
						'plus_animation_effect'     => 'rotating',
					),
				)
			);
		}
		
		protected function add_actions() {
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_magic_scroll' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_gsap_scroll' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_tooltip' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_mouseparallax' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_tilt_parallax' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_reveal_effect' ), 10, 3 );
			add_action( 'elementor/element/before_section_end', array( $this, 'register_controls_widget_continuous_animation' ), 10, 3 );
		}
	}

}
