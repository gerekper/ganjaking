<?php
/**
 * Class: Module
 * Name: Section Blob Generator
 * Slug: premium-blob
 */

namespace PremiumAddonsPro\Modules\PremiumSectionBlob;

use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use PremiumAddonsPro\Base\Module_Base;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Blob Generator section addon.
 */
class Module extends Module_Base {

	/**
	 * Load Script
	 *
	 * @var $load_assets
	 */
	private $load_assets = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		parent::__construct();

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Section Blob Generator is enabled.
		$blob = $modules['premium-blob'];

		if ( ! $blob ) {
			return;
		}

		// Enqueue the required CSS/JS files.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Blob Generator tab at the end of section layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );

		// insert data before section rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, 'print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ), 10, 1 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );
		}

	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue required JS dependencies for the extension.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		// This is required for free-hand positioning.
		if ( $is_edit_mode ) {

			if ( ! wp_script_is( 'premium-pro', 'enqueued' ) ) {
				wp_enqueue_script( 'premium-pro' );
			}
		}

		if ( ! wp_script_is( 'pa-blob-path', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-blob-path' );
		}

		if ( ! wp_script_is( 'pa-anime', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-anime' );
		}

		if ( ! wp_script_is( 'pa-blob', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-blob' );
		}

	}

	/**
	 * Enqueue styles.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( 'pa-global', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-global' );
		}
	}

	/**
	 * Register Blob Generator controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_blob',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Blob Generator', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_blob_switcher',
			array(
				'label'        => __( 'Enable Blob Generator', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-blob-',
			)
		);

		$doc_link = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/elementor-animated-blob-generator-section-addon-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'pa_blob_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="' . esc_url( $doc_link ) . '" target="_blank">' . __( 'How to use Premium Blob Generator for Elementor »', 'premium-addons-pro' ) . '</a>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_blob_switcher' => 'yes',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'hide_layer',
			array(
				'label'     => __( 'Hide This Blob', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: none',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_type',
			array(
				'label'   => __( 'Blob Source', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pre',
				'options' => array(
					'pre'    => __( 'External Source', 'premium-addons-pro' ),
					'custom' => __( 'Create Your Own', 'premium-addons-pro' ),
				),

			)
		);

		$repeater->add_control(
			'premium_blob_pre',
			array(
				'label'       => __( 'Blob Code (svg)', 'premium-addons-pro' ),
				'description' => 'Get Blob SVG code from <a href="https://www.blobmaker.app/" target="_blank">Blobmaker</a>, <a href="https://blobs.app/" target="_blank">Blobs</a> or <a href="https://squircley.app/" target="_blank">Squircley</a>.',
				'type'        => Controls_Manager::CODE,
				'rows'        => 10,
				'placeholder' => __( 'Paste your svg code here', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_blob_type' => 'pre',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_custom',
			array(
				'label'     => __( 'Custom Blob', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HIDDEN,
				'default'   => $this->get_default_blob_params(),
				'condition' => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_complexity',
			array(
				'label'       => __( 'Nodes', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'description' => 'The more the nodes, the more complex the blob gets.',
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 5,
				),
				'condition'   => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_randomness',
			array(
				'label'       => __( 'Randomness', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'description' => __( 'Controls the variation between each node.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 5,
				),
				'condition'   => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_blob_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units'  => array( 'px', '%', 'em' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 400,
				),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_responsive_control(
			'pa_blob_size_pre',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 400,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_blob_type' => 'pre',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_generate',
			array(
				'label'       => __( 'Generate New Shape', 'premium-addons-pro' ),
				'type'        => Controls_Manager::BUTTON,
				'button_type' => 'default elementor-button-success',
				'text'        => __( 'Generate', 'premium-addons-pro' ),
				'event'       => 'generate',
				'description' => __( 'Click to generate random blob shapes based on your Size, Nodes, and Randomness settings.', 'premium-adons-pro' ),
				'condition'   => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_shadow_switcher',
			array(
				'label' => __( 'Shadow', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'pa_blob_shadow',
			array(
				'label'        => __( 'Shadow', 'premium-addons-pro' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type'  => 'template',
				'condition'    => array(
					'pa_blob_shadow_switcher' => 'yes',
				),
			)
		);

		$repeater->start_popover();

		$repeater->add_control(
			'pa_blob_shadow_color',
			array(
				'label'   => __( 'Color', 'premium-addons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#00000035',
			)
		);

		$repeater->add_control(
			'pa_blob_shadow_h',
			array(
				'label'   => __( 'Horizontal', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_shadow_v',
			array(
				'label'   => __( 'Vertical', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_shadow_blur',
			array(
				'label'     => __( 'Blur', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'   => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-blob-shadow' => 'filter: drop-shadow({{pa_blob_shadow_h.SIZE}}px {{pa_blob_shadow_v.SIZE}}px {{SIZE}}px {{pa_blob_shadow_color.VALUE}})',
				),
			)
		);

		$repeater->end_popover();

		$repeater->add_control(
			'pa_blob_zindex',
			array(
				'label'     => __( 'Z-Index', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_stroke',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg > path' => 'stroke: {{VALUE}};',
				),
				'condition' => array(
					'premium_blob_type' => 'custom',
				),

			)
		);

		$repeater->add_control(
			'pa_blob_stroke_width',
			array(
				'label'      => __( 'Stroke Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 3,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg > path' => 'stroke-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_blob_type' => 'custom',
					'pa_blob_stroke!'   => '',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_stroke_dash',
			array(
				'label'       => __( 'Stroke Dasharray', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Apply Dashed Stroke, you can enter a single value or multiple values sepatated by ",". dash,space,dash,space,... etc', 'premium-addons-pro' ),
				'placeholder' => '0,10,5,5,10',
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg > path' => 'stroke-dasharray: {{VALUE}};',
				),
				'condition'   => array(
					'premium_blob_type' => 'custom',
					'pa_blob_stroke!'   => '',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_bg_type',
			array(
				'label'       => __( 'Fill', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'default'     => 'color',
				'options'     => array(
					'none'     => __( 'None', 'premium-addons-pro' ),
					'color'    => __( 'Color', 'premium-addons-pro' ),
					'image'    => __( 'Image', 'premium-addons-pro' ),
					'gradient' => __( 'Gradient', 'premium-addons-pro' ),
				),
				'condition'   => array(
					'premium_blob_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_fill_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Note that: when fill is <b>NONE</b> you should apply <b>STROKE</b> to outline your blob shape.', 'premium-addons-pro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_blob_bg_type' => 'none',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#D1BCF5',
				'condition' => array(
					'premium_blob_bg_type' => 'color',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_image',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_blob_bg_type' => 'image',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_image_pos',
			array(
				'label'     => __( 'Image Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'Default', 'premium-addons-pro' ),
					'custom'  => __( 'Custom', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_blob_bg_type' => 'image',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 500,
				),
				'condition'  => array(
					'premium_blob_bg_type'   => 'image',
					'premium_blob_image_pos' => 'custom',
					'premium_blob_type'      => 'custom',
				),
			)
		);

		$repeater->add_control(
			'image_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 500,
				),
				'condition'  => array(
					'premium_blob_bg_type'   => 'image',
					'premium_blob_image_pos' => 'custom',
					'premium_blob_type'      => 'custom',
				),
			)
		);

		$repeater->add_control(
			'image_xpos',
			array(
				'label'      => __( 'X-Postion', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'  => array(
					'premium_blob_bg_type'   => 'image',
					'premium_blob_image_pos' => 'custom',
					'premium_blob_type'      => 'custom',
				),
			)
		);

		$repeater->add_control(
			'image_ypos',
			array(
				'label'      => __( 'Y-Postion', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'  => array(
					'premium_blob_bg_type'   => 'image',
					'premium_blob_image_pos' => 'custom',
					'premium_blob_type'      => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_firstcolor',
			array(
				'label'     => __( 'Color 1', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF5F6D',
				'condition' => array(
					'premium_blob_bg_type' => 'gradient',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_firstloc',
			array(
				'label'      => __( 'Location', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'  => array(
					'premium_blob_bg_type' => 'gradient',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_secondcolor',
			array(
				'label'     => __( 'Color 2', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC371',
				'condition' => array(
					'premium_blob_bg_type' => 'gradient',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_secondloc',
			array(
				'label'      => __( 'Location', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'condition'  => array(
					'premium_blob_bg_type' => 'gradient',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_type',
			array(
				'label'     => __( 'Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'linear',
				'options'   => array(
					'linear' => __( 'Linear', 'premium-addons-pro' ),
					'radial' => __( 'Radial', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_blob_bg_type' => 'gradient',
					'premium_blob_type'    => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_angle',
			array(
				'label'      => __( 'Angle', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'%' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'condition'  => array(
					'premium_blob_bg_type'       => 'gradient',
					'premium_blob_gradient_type' => 'linear',
					'premium_blob_type'          => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_xpos',
			array(
				'label'      => __( 'X-Postion', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'condition'  => array(
					'premium_blob_bg_type'       => 'gradient',
					'premium_blob_gradient_type' => 'radial',
					'premium_blob_type'          => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_gradient_ypos',
			array(
				'label'      => __( 'Y-Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'condition'  => array(
					'premium_blob_bg_type'       => 'gradient',
					'premium_blob_gradient_type' => 'radial',
					'premium_blob_type'          => 'custom',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_blob_hor_offset',
			array(
				'label'     => __( 'Horizontal Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				// 'render_type' => 'template',
				'separator' => 'before',
				'default'   => array(
					'unit' => '%',
					'size' => 0,
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_blob_ver_offset',
			array(
				'label'     => __( 'Vertical Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				// 'render_type' => 'template',
				'default'   => array(
					'unit' => '%',
					'size' => 0,
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_animate',
			array(
				'label'      => __( 'Animate', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'premium_blob_type',
							'value' => 'custom',
						),
						array(
							'name'     => 'premium_blob_randomness[size]',
							'operator' => '>',
							'value'    => 0,
						),
						array(
							'name'     => 'premium_blob_complexity[size]',
							'operator' => '>',
							'value'    => 0,
						),
					),
				),

			)
		);

		$repeater->add_control(
			'pa_blob_anime_dur',
			array(
				'label'       => __( 'Animation Duration', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'render_type' => 'template',
				'default'     => 2,
				'step'        => 0.1,
				'min'         => 0.1,
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'premium_blob_type',
							'value' => 'custom',
						),
						array(
							'name'  => 'pa_blob_animate',
							'value' => 'yes',
						),
						array(
							'name'     => 'premium_blob_randomness[size]',
							'operator' => '>',
							'value'    => 0,
						),
						array(
							'name'     => 'premium_blob_complexity[size]',
							'operator' => '>',
							'value'    => 0,
						),
					),
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_scroll',
			array(
				'label'       => __( 'Scroll Parallax', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => 'Enable or disable vertical or horizontal scroll parallax',
			)
		);

		$repeater->add_control(
			'pa_blob_parallax_ver',
			array(
				'label'     => __( 'Vertical Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Up', 'premium-addons-pro' ),
					'down' => __( 'Down', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_ver'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 4,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_ver'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_ver'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'pa_blob_parallax_hor',
			array(
				'label'     => __( 'Horizontal Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_direction_hor',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Left', 'premium-addons-pro' ),
					'down' => __( 'Right', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_hor'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_speed_hor',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 4,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_hor'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_blob_parallax_view_hor',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_blob_parallax_scroll' => 'yes',
					'pa_blob_parallax_hor'         => 'yes',
				),
			)
		);

		$repeater->add_control(
			'hide_blob_on',
			array(
				'label'       => __( 'Hide Blob On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$element->add_control(
			'premium_blob_repeater',
			array(
				'label'         => __( 'Blobs', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'condition'     => array(
					'premium_blob_switcher' => 'yes',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Blob Generator output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function print_template( $template, $widget ) {

		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'container' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' === settings.premium_blob_switcher && 0 !== settings.premium_blob_repeater.length ) {

			var blobSettings = [];

			_.each(settings.premium_blob_repeater, function( blob, index ) {

				var type = blob.premium_blob_type,
					source = 'pre' === type ? blob.premium_blob_pre : blob.pa_blob_custom,
					parallax = 'yes' === blob.premium_blob_parallax_scroll ? true : false,
					animate = 'yes' === blob.pa_blob_animate && 'custom' === type ? true : false,
					shadow   = 'yes' === blob.pa_blob_shadow_switcher ? true : false,
					layerSettings = {
						'id': blob._id,
						'type': type,
						'source': source,
						'parallax': parallax,
						'animate': animate,
						'shadow' : shadow,
						'devices': blob.hide_blob_on
					};

				if (animate) {
					layerSettings.animeDur = 0 != blob.pa_blob_anime_dur && '' !== blob.pa_blob_anime_dur ? blob.pa_blob_anime_dur : 2;
				}

				if ( 'custom' === type ) {
					var fillType = blob.premium_blob_bg_type,
						fill = {};

					switch ( fillType )  {
						case 'color':
							fill = blob.premium_blob_color;
							break;

						case 'image':
							var pos = blob.premium_blob_image_pos,
								imgOptions = {};

							if ('custom' === pos ) {
								imgOptions = {
									'width': blob.image_width.size ? blob.image_width.size + 'px' : '500px',
									'height': blob.image_height.size ? blob.image_height.size + 'px' : '500px',
									'xpos': blob.image_xpos.size ? blob.image_xpos.size: 0,
									'ypos': blob.image_ypos.size ? blob.image_ypos.size: 0,
									'aspect': ''
								};
							}

							fill = Object.assign(
								{
									'img': blob.premium_blob_image,
									'width': '100%',
									'height': '100%',
									'xpos': 0,
									'ypos': 0 ,
									'aspect': ' preserveAspectRatio="none"'
								}, imgOptions);

							break;

						case 'gradient':
							var gradient_type = blob.premium_blob_gradient_type,
								$pos = 'linear' === gradient_type ? blob.premium_blob_gradient_angle.size : [ blob.premium_blob_gradient_xpos.size, blob.premium_blob_gradient_ypos.size ];

							fill = {
								'gradType': gradient_type,
								'firstColor': blob.premium_blob_gradient_firstcolor,
								'secColor': blob.premium_blob_gradient_secondcolor,
								'firstLoc': blob.premium_blob_gradient_firstloc.size,
								'secLoc': blob.premium_blob_gradient_secondloc.size,
								'pos': $pos
							};

							break;

						default:
							fill = 'none';
							break;
					}

					customSettings = {
						size: blob.premium_blob_size.size ? blob.premium_blob_size.size : 400,
						extraPoints: '' !== blob.premium_blob_complexity.size ? blob.premium_blob_complexity.size: 5,
						randomness: '' !== blob.premium_blob_randomness.size ? blob.premium_blob_randomness.size: 5,
						fillType: fillType,
						fill: fill,
					};

					layerSettings = Object.assign( layerSettings, customSettings);
				}

				if ( parallax ) {
					var parallaxSettings = {};

					if ( 'yes' === blob.pa_blob_parallax_ver) {
						parallaxSettings = {
							'vscroll': 'yes',
							'speed': blob.premium_blob_parallax_speed.size ? blob.premium_blob_parallax_speed.size : 4,
							'start': blob.premium_blob_parallax_view.sizes.start ? blob.premium_blob_parallax_view.sizes.start : 0,
							'end': blob.premium_blob_parallax_view.sizes.end ? blob.premium_blob_parallax_view.sizes.end : 100,
							'direction': blob.premium_blob_parallax_direction
						};
					}

					if ( 'yes' === blob.pa_blob_parallax_hor) {
						parallaxSettings = Object.assign( parallaxSettings, {
							'hscroll': 'yes',
							'speed_h'    : blob.premium_blob_parallax_speed_hor.size ? blob.premium_blob_parallax_speed_hor.size : 4,
							'start_h'    : blob.premium_blob_parallax_view_hor.sizes.start ? blob.premium_blob_parallax_view_hor.sizes.start : 0,
							'end_h'      : blob.premium_blob_parallax_view_hor.sizes.end ? blob.premium_blob_parallax_view_hor.sizes.end : 100,
							'direction_h': blob.premium_blob_parallax_direction_hor,
						} );
					}

					layerSettings.parallaxSetting = parallaxSettings;
				}

				blobSettings.push(layerSettings);
			});

			view.addRenderAttribute( 'blob_data', {
				'id': 'premium-blob-gen-' + view.getID(),
				'class': 'premium-blob-gen-wrapper',
				'data-blob': JSON.stringify( blobSettings )
			});

		#>
			<div {{{ view.getRenderAttributeString( 'blob_data' ) }}}></div>
		<# } #>
		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Blob Generator output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$data = $element->get_data();

		$type = $data['elType'];

		$settings = $element->get_settings_for_display();

		$blobs = $settings['premium_blob_repeater'];

		if ( ( 'section' === $type || 'container' === $type ) && 'yes' === $settings['premium_blob_switcher'] && isset( $settings['premium_blob_repeater'] ) && 0 < count( $blobs ) ) {

			$blob_settings = array();

			foreach ( $blobs as $blob ) {

				$type     = $blob['premium_blob_type'];
				$source   = 'pre' === $type ? $blob['premium_blob_pre'] : $blob['pa_blob_custom'];
				$parallax = 'yes' === $blob['premium_blob_parallax_scroll'] ? true : false;
				$animate  = 'yes' === $blob['pa_blob_animate'] && 'custom' === $type ? true : false;
				$shadow   = 'yes' === $blob['pa_blob_shadow_switcher'] ? true : false;

				$layer_settings = array(
					'id'       => $blob['_id'],
					'type'     => $type,
					'source'   => $source,
					'parallax' => $parallax,
					'animate'  => $animate,
					'shadow'   => $shadow,
					'devices'  => $blob['hide_blob_on'],
				);

				if ( $animate ) {
					$layer_settings['animeDur'] = ( ! empty( $blob['pa_blob_anime_dur'] ) && 0 !== floatVal( $blob['pa_blob_anime_dur'] ) ) ? $blob['pa_blob_anime_dur'] : 2;
				}

				if ( 'custom' === $type ) {

					$fill_type = $blob['premium_blob_bg_type'];
					$fill      = array();

					switch ( $fill_type ) {
						case 'color':
							$fill = $blob['premium_blob_color'];
							break;

						case 'image':
							$pos         = $blob['premium_blob_image_pos'];
							$img_options = array();

							if ( 'custom' === $pos ) {
								$img_options = array(
									'width'  => $blob['image_width']['size'] ? $blob['image_width']['size'] . 'px' : '500px',
									'height' => $blob['image_height']['size'] ? $blob['image_height']['size'] . 'px' : '500px',
									'xpos'   => $blob['image_xpos']['size'] ? $blob['image_xpos']['size'] : 0,
									'ypos'   => $blob['image_ypos']['size'] ? $blob['image_ypos']['size'] : 0,
									'aspect' => '',
								);
							}

							$fill = array_merge(
								array(
									'img'    => $blob['premium_blob_image'],
									'width'  => '100%',
									'height' => '100%',
									'xpos'   => 0,
									'ypos'   => 0,
									'aspect' => 'preserveAspectRatio="none"',
								),
								$img_options
							);

							break;

						case 'gradient':
							$gradient_type = $blob['premium_blob_gradient_type'];
							$pos           = 'linear' === $gradient_type ? $blob['premium_blob_gradient_angle']['size'] : array( $blob['premium_blob_gradient_xpos']['size'], $blob['premium_blob_gradient_ypos']['size'] );

							$fill = array(
								'gradType'   => $gradient_type,
								'firstColor' => $blob['premium_blob_gradient_firstcolor'],
								'secColor'   => $blob['premium_blob_gradient_secondcolor'],
								'firstLoc'   => $blob['premium_blob_gradient_firstloc']['size'],
								'secLoc'     => $blob['premium_blob_gradient_secondloc']['size'],
								'pos'        => $pos,
							);

							break;

						default:
							$fill = 'none';
							break;
					}

					$custom_settings = array(
						'size'        => $blob['premium_blob_size']['size'] ? $blob['premium_blob_size']['size'] : 400,
						'extraPoints' => '' !== $blob['premium_blob_complexity']['size'] ? $blob['premium_blob_complexity']['size'] : 5,
						'randomness'  => '' !== $blob['premium_blob_randomness']['size'] ? $blob['premium_blob_randomness']['size'] : 5,
						'fillType'    => $fill_type,
						'fill'        => $fill,
					);

					$layer_settings = array_merge( $layer_settings, $custom_settings );
				}

				if ( $parallax ) {
					$parallax_settings = array();

					if ( 'yes' === $blob['pa_blob_parallax_ver'] ) {
						$parallax_settings = array(
							'vscroll'   => 'yes',
							'speed'     => $blob['premium_blob_parallax_speed']['size'] ? $blob['premium_blob_parallax_speed']['size'] : 4,
							'start'     => $blob['premium_blob_parallax_view']['sizes']['start'] ? $blob['premium_blob_parallax_view']['sizes']['start'] : 0,
							'end'       => $blob['premium_blob_parallax_view']['sizes']['end'] ? $blob['premium_blob_parallax_view']['sizes']['end'] : 100,
							'direction' => $blob['premium_blob_parallax_direction'],
						);
					}

					if ( 'yes' === $blob['pa_blob_parallax_hor'] ) {

						$parallax_settings = array_merge(
							$parallax_settings,
							array(
								'hscroll'     => 'yes',
								'speed_h'     => $blob['premium_blob_parallax_speed_hor']['size'] ? $blob['premium_blob_parallax_speed_hor']['size'] : 4,
								'start_h'     => $blob['premium_blob_parallax_view_hor']['sizes']['start'] ? $blob['premium_blob_parallax_view_hor']['sizes']['start'] : 0,
								'end_h'       => $blob['premium_blob_parallax_view_hor']['sizes']['end'] ? $blob['premium_blob_parallax_view_hor']['sizes']['end'] : 100,
								'direction_h' => $blob['premium_blob_parallax_direction_hor'],
							)
						);
					}

					$layer_settings['parallaxSetting'] = $parallax_settings;
				}

				array_push( $blob_settings, $layer_settings );
			}

			$element->add_render_attribute( '_wrapper', 'data-blob', wp_json_encode( $blob_settings ) );
		}
	}

	/**
	 * Returns initial blob params.
	 */
	private function get_default_blob_params() {
		return array(
			'seed' => 0.5,
			'path' => 'M200,386.36996944745385C244.9196931276957,378.06269321316313,264.730910854225,329.27445735853564,295.61434491113516,295.61434491113516C324.13812629548147,264.52603759877724,366.7500808247663,241.9769116039796,370.99600218546885,200C375.49189789991624,155.55173990437655,348.7152607231028,114.46481151870907,317.13357813720216,82.86642186279785C285.54263142544414,51.25876318020111,244.60026009695432,31.715110115648525,200,28.9135389806082C152.1934075309217,25.910561660860413,97.30481338201393,29.934828647407755,67.16458720706562,67.16458720706558C38.17487949310586,102.97320432364309,62.53809162616684,153.93951955938655,61.49293387619159,199.99999999999997C60.400232127715974,248.15576166782176,32.26433379429449,300.34305743279504,60.996289265237436,339.0037107347625C91.3503453155561,379.847002395041,149.96095399311486,395.62399922227615,200,386.36996944745385',
			'html' => '<svg width="400" height="400" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"><path fill="#D1BCF5" d="M200,384.1341635345348C272.78983976438803,383.7330760911808,319.36688654639164,315.31573634299525,341.00326225074974,245.81473713836246C361.6314742020995,179.55219104405637,356.0617244591451,103.2632954509613,300.29927825027835,61.949886780009344C243.17748574166143,19.629362513795158,164.48899823602054,30.19423926430739,107.70713441453674,72.96976841969219C53.99796086782868,113.43054523007896,35.01026496626941,182.92750117778576,54.13177352112435,247.39545983754905C75.08395695690338,318.0355189989181,126.31928938977407,384.5401598650504,200,384.1341635345348"></path></svg>',
		);
	}

	/**
	 * Check Assets Enqueue
	 *
	 * Check if the assets files should be loaded.
	 *
	 * @since 2.6.
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_assets_enqueue( $element ) {

		if ( $this->load_assets ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_blob_switcher' ) ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();

			$this->load_assets = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );
		}

	}
}
