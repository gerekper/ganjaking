<?php
/**
 * Class: Module
 * Name: Section Parallax
 * Slug: premium-parallax
 */

namespace PremiumAddonsPro\Modules\PremiumSectionParallax;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Control_Media;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Parallax section addon.
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

		// Checks if Section Parallax is enabled.
		$parallax = $modules['premium-parallax'];

		if ( ! $parallax ) {
			return;
		}

		// Enqueue the required CSS/JS files.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Prallax tab at the end of section/column layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, '_print_template' ), 10, 2 );

		// insert data before section/column rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'check_assets_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, '_print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ), 10, 1 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );

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
	 * Enqueue scripts.
	 *
	 * Enqueue required JS dependencies for the extension.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'imagesloaded', 'enqueued' ) ) {
			wp_enqueue_script( 'imagesloaded' );
		}

		$draw_svg = $this->check_icon_draw();

		if ( ! wp_script_is( 'pa-tweenmax', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-tweenmax' );
		}

		if ( $draw_svg ) {
			if ( ! wp_script_is( 'pa-motionpath', 'enqueued' ) ) {
				wp_enqueue_script( 'pa-motionpath' );
			}
		}

		if ( ! wp_script_is( 'pa-parallax', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-parallax' );
		}

	}

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		if ( version_compare( PREMIUM_ADDONS_VERSION, '4.9.26', '<' ) ) {
			return false;
		}

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-parallax' );
		return $is_enabled;
	}

	/**
	 * Register Parallax controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$draw_svg = $this->check_icon_draw();

		$element->start_controls_section(
			'section_premium_parallax',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Parallax', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_parallax_update',
			array(
				'label' => '<div class="elementor-update-preview editor-pa-preview-update"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
				'type'  => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_parallax_switcher',
			array(
				'label'        => __( 'Enable Parallax', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-parallax-',
				'render_type'  => 'template',
			)
		);

		$options = array(
			'scroll'         => __( 'Scroll', 'premium-addons-pro' ),
			'scroll-opacity' => __( 'Scroll + Opacity', 'premium-addons-pro' ),
			'opacity'        => __( 'Opacity', 'premium-addons-pro' ),
			'scale'          => __( 'Scale', 'premium-addons-pro' ),
			'scale-opacity'  => __( 'Scale + Opacity', 'premium-addons-pro' ),
			'automove'       => __( 'Auto Moving Background', 'premium-addons-pro' ),
			'multi'          => __( 'Multi Layer Parallax', 'premium-addons-pro' ),
		);

		if ( strpos( current_filter(), 'column/' ) ) {
			unset( $options['multi'] );
		}

		$element->add_control(
			'premium_parallax_type',
			array(
				'label'       => __( 'Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $options,
				'label_block' => 'true',
				'render_type' => 'template',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'hide_layer',
			array(
				'label'     => __( 'Hide This Layer', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: none',
				),
			)
		);

		$repeater->add_control(
			'layer_type',
			array(
				'label'   => __( 'Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'img' => __( 'Image', 'premium-addons-pro' ),
					'svg' => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'default' => 'img',
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => array(
					'layer_type' => 'img',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'layer_type' => 'svg',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_hor',
			array(
				'label'   => __( 'Horizontal Alignment', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'left'   => __( 'Left', 'premium-addons-pro' ),
					'center' => __( 'Center', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
					'custom' => __( 'Custom', 'premium-addons-pro' ),
				),
				'default' => 'custom',
			)
		);

		$repeater->add_responsive_control(
			'premium_parallax_layer_hor_pos',
			array(
				'label'       => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set the horizontal position for the layer background, default: 50%', 'premium-addons-pro' ),
				'default'     => array(
					'size' => 0,
					'unit' => '%',
				),
				'min'         => 0,
				'max'         => 100,
				'label_block' => true,
				'condition'   => array(
					'premium_parallax_layer_hor' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_ver',
			array(
				'label'   => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'top'     => __( 'Top', 'premium-addons-pro' ),
					'vcenter' => __( 'Center', 'premium-addons-pro' ),
					'bottom'  => __( 'Bottom', 'premium-addons-pro' ),
					'custom'  => __( 'Custom', 'premium-addons-pro' ),
				),
				'default' => 'custom',
			)
		);

		$repeater->add_responsive_control(
			'premium_parallax_layer_ver_pos',
			array(
				'label'       => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 0,
					'unit' => '%',
				),
				'min'         => 0,
				'max'         => 100,
				'description' => __( 'Set the vertical position for the layer background, default: 50%', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_parallax_layer_ver' => 'custom',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_parallax_layer_width',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 100,
					'unit' => '%',
				),
				'label_block' => true,
				'condition'   => array(
					'layer_type' => 'img',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_parallax_svg_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'max'  => 100,
						'step' => 1,
					),
				),
				'condition'  => array(
					'layer_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_parallax_svg_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition'  => array(
					'layer_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_z_index',
			array(
				'label'       => __( 'Z-index', 'premium-addons-pro' ),
				'description' => __( 'Set z-index for the current layer', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
			)
		);

		$repeater->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw SVG', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_svg ? '' : 'editor-pa-control-disabled',
				'separator' => 'before',
				'condition' => array(
					'layer_type' => 'svg',
				),
			)
		);

		if ( $draw_svg ) {
			$repeater->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_notice',
				array(
					'raw'             => __( 'Loop and Speed options are overriden when Draw SVGs in Sequence option is enabled.', 'premium-addons-pro' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
						'svg_hover!' => 'true',
					),
				)
			);

			$repeater->add_control(
				'svg_reverse',
				array(
					'label'        => __( 'Reverse', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'layer_type'   => 'svg',
						'draw_svg'     => 'yes',
						'svg_reverse!' => 'true',
					),
				)
			);

			$repeater->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'layer_type'  => 'svg',
						'draw_svg'    => 'yes',
						'svg_reverse' => 'true',
					),

				)
			);

			$repeater->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Animate on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'restart_draw',
				array(
					'label'        => __( 'Restart Animation on Scroll Up', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
						'svg_hover!' => 'true',
					),

				)
			);

			$repeater->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
						'svg_loop'   => 'true',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$repeater,
				'parallax',
				array(
					'layer_type' => 'svg',
				)
			);

		}

		$repeater->add_control(
			'path_width',
			array(
				'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 3,
					'unit' => 'px',
				),
				'condition' => array(
					'layer_type' => 'svg',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg *' => 'stroke-width: {{SIZE}}',
				),
			)
		);

		$repeater->add_control(
			'layer_fill',
			array(
				'label'     => __( 'Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'layer_type' => 'svg',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg, {{WRAPPER}} {{CURRENT_ITEM}} svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'layer_stroke',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6EC1E4',
				'condition' => array(
					'layer_type' => 'svg',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg *' => 'stroke: {{VALUE}};',
				),
			)
		);

		if ( $draw_svg ) {
			$repeater->add_control(
				'svg_color',
				array(
					'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_stroke',
				array(
					'label'     => __( 'After Draw Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'layer_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);
		}

		$repeater->add_control(
			'premium_parallax_layer_mouse',
			array(
				'label'       => __( 'Mouse Track', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable mousemove interaction.', 'premium-addons-pro' ),
				'separator'   => 'before',
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_rate',
			array(
				'label'       => __( 'Rate', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -10,
				'min'         => -20,
				'max'         => 20,
				'step'        => 1,
				'description' => __( 'Choose the movement rate for the layer background, default: -10', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_parallax_layer_mouse' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_scroll',
			array(
				'label'       => __( 'Scroll Parallax', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable scroll parallax', 'premium-addons-pro' ),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_scroll_ver',
			array(
				'label'     => __( 'Vertical Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_parallax_layer_scroll' => 'yes',
				),
				'default'   => 'yes',
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Up', 'premium-addons-pro' ),
					'down' => __( 'Down', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array(
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_ver' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_speed',
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
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_ver' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_view',
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
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_ver' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_scroll_hor',
			array(
				'label'     => __( 'Horizontal Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_parallax_layer_scroll' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_direction_hor',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Left', 'premium-addons-pro' ),
					'down' => __( 'Right', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array(
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_hor' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_speed_hor',
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
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_hor' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_view_hor',
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
					'premium_parallax_layer_scroll'     => 'yes',
					'premium_parallax_layer_scroll_hor' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'show_layer_on',
			array(
				'label'       => __( 'Show Layer On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'default'     => Helper_Functions::get_all_breakpoints( 'keys' ),
				'multiple'    => true,
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_id',
			array(
				'label'       => __( 'CSS ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Set a CSS ID to this layer.', 'premium-addons-pro' ),
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$element->add_control(
			'premium_parallax_auto_type',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'left'   => __( 'Left to Right', 'premium-addons-pro' ),
					'right'  => __( 'Right to Left', 'premium-addons-pro' ),
					'top'    => __( 'Top to Bottom', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom to Top', 'premium-addons-pro' ),
				),
				'default'   => 'left',
				'condition' => array(
					'premium_parallax_type' => 'automove',
				),
			)
		);

		$element->add_control(
			'premium_parallax_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => -1,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_parallax_type!' => array( 'automove', 'multi' ),
				),
			)
		);

		$element->add_control(
			'premium_auto_speed',
			array(
				'label'       => __( 'Speed', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 0,
				'max'         => 150,
				'description' => __( 'Set the speed of background movement, default: 3', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_parallax_type' => 'automove',
				),
			)
		);

		$element->add_control(
			'premium_parallax_android_support',
			array(
				'label'     => __( 'Enable Parallax on Android', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_parallax_type!' => array( 'automove', 'multi' ),
				),
			)
		);

		$element->add_control(
			'premium_parallax_ios_support',
			array(
				'label'     => __( 'Enable Parallax on iOS', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_parallax_type!' => array( 'automove', 'multi' ),
				),
			)
		);

		$element->add_control(
			'premium_parallax_notice',
			array(
				'raw'             => __( 'You can position, resize parallax layers from the preview area. Note that freehand resize not working for SVG layers', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_parallax_type' => 'multi',
				),
			)
		);

		$element->add_control(
			'premium_parallax_layers_list',
			array(
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'condition'     => array(
					'premium_parallax_type' => 'multi',
				),
			)
		);

		$element->add_control(
			'draw_svgs_sequence',
			array(
				'label'        => __( 'Draw SVGs In Sequence', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-svg-draw-seq-',
				'render_type'  => 'template',
			)
		);

		$element->add_control(
			'draw_svgs_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-svg-draw-loop-',
				'render_type'  => 'template',
				'condition'    => array(
					'draw_svgs_sequence' => 'yes',
				),
			)
		);

		$element->add_control(
			'frames',
			array(
				'label'       => __( 'Speed', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
				'default'     => 5,
				'min'         => 1,
				'max'         => 100,
				'condition'   => array(
					'draw_svgs_sequence' => 'yes',
				),
			)
		);

		$element->add_control(
			'svg_yoyo',
			array(
				'label'        => __( 'Yoyo Animation', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-svg-draw-yoyo-',
				'render_type'  => 'template',
				'condition'    => array(
					'draw_svgs_sequence' => 'yes',
					'draw_svgs_loop'     => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_parallax_layers_devices',
			array(
				'label'       => __( 'Apply Scroll Parallax On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'default'     => Helper_Functions::get_all_breakpoints( 'keys' ),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array(
					'premium_parallax_type' => 'multi',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Parallax output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function _print_template( $template, $widget ) {

		if ( $widget->get_name() === 'widget' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();

		?>
		<#
		var parallax = ( typeof settings.premium_parallax_type !== "undefined" && settings.premium_parallax_type ) ? settings.premium_parallax_type: '';

		if( 'yes' === settings.premium_parallax_switcher && "" !== parallax ) {

			var parallaxSettings = {};

			parallaxSettings.type = parallax;

			if ( "multi" !== parallax && "automove" !== parallax ) {

				var speed = "" !== settings.premium_parallax_speed.size ? settings.premium_parallax_speed.size : 0.5;

				var positiont = settings.background_position_tablet;
				if ( 'initial' === positiont ) {
					positiont = settings.background_xpos_tablet.size + settings.background_xpos_tablet.unit + ' ' + settings.background_ypos_tablet.size + settings.background_ypos_tablet.unit;
				}

				var positionm = settings.background_position_mobile;
				if ( 'initial' === positionm ) {
					positionm = settings.background_xpos_mobile.size + settings.background_xpos_mobile.unit + ' ' + settings.background_ypos_mobile.size + settings.background_ypos_mobile.unit;

				}

				parallaxSettings.speed    = speed;
				parallaxSettings.android  = "yes" === settings.premium_parallax_android_support ? 0 : 1;
				parallaxSettings.ios      = "yes" === settings.premium_parallax_ios_support ? 0 : 1;
				parallaxSettings.size     = settings.background_size;
				parallaxSettings.position = settings.background_position;
				parallaxSettings.positiont = positiont;
				parallaxSettings.positionm = positionm;
				parallaxSettings.repeat   = settings.background_repeat;

			} else if ( "automove" === parallax ) {

				var speed = "" !== settings.premium_auto_speed ? settings.premium_auto_speed : 3 ,
					type  = "" !== settings.premium_parallax_auto_type ? settings.premium_parallax_auto_type : 'left';

				parallaxSettings.speed     = speed;
				parallaxSettings.direction = type;

			} else {
				var layers = [] ;

				_.each( settings.premium_parallax_layers_list, function( layer, index ) {
					layers.push( layer );
				});

				parallaxSettings.items   = layers;
				parallaxSettings.devices = settings.premium_parallax_layers_devices;
				parallaxSettings.speed = settings.frames;

			}

			view.addRenderAttribute( 'parallax_data', {
				'id': 'premium-parallax-' + view.getID(),
				'class': 'premium-parallax-wrapper',
				'data-pa-parallax': JSON.stringify( parallaxSettings )
			});

		#>
			<div {{{ view.getRenderAttributeString( 'parallax_data' ) }}}></div>
		<# } #>
		<?php

		$parallax_content = ob_get_contents();
		ob_end_clean();
		$template = $parallax_content . $old_template;
		return $template;
	}

	/**
	 * Render Parallax output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$settings = $element->get_settings_for_display();

		$parallax = isset( $settings['premium_parallax_type'] ) ? $settings['premium_parallax_type'] : '';

		if ( isset( $parallax ) && '' !== $parallax && 'yes' === $element->get_settings( 'premium_parallax_switcher' ) ) {

			$parallax_settings = array(
				'type' => $parallax,
			);

			if ( 'multi' !== $parallax && 'automove' !== $parallax ) {

				// Fix image bounce issue.
				$element->add_render_attribute( '_wrapper', 'class', 'premium-parallax-section-hide' );

				$speed = isset( $settings['premium_parallax_speed']['size'] ) ? $settings['premium_parallax_speed']['size'] : 0.5;

				$parallax_settings = array_merge(
					$parallax_settings,
					array(
						'speed'   => $speed,
						'android' => 'yes' === $settings['premium_parallax_android_support'] ? 0 : 1,
						'ios'     => 'yes' === $settings['premium_parallax_ios_support'] ? 0 : 1,
						'size'    => $settings['background_size'],
						'repeat'  => $settings['background_repeat'],
					)
				);

			} elseif ( 'automove' === $parallax ) {

				$speed             = ! empty( $settings['premium_auto_speed'] ) ? $settings['premium_auto_speed'] : 3;
				$type              = ! empty( $settings['premium_parallax_auto_type'] ) ? $settings['premium_parallax_auto_type'] : 'left';
				$parallax_settings = array_merge(
					$parallax_settings,
					array(
						'speed'     => $speed,
						'direction' => $type,
					)
				);

			} else {

				$layers = array();

				if ( is_countable( $settings['premium_parallax_layers_list'] ) ) {
					foreach ( $settings['premium_parallax_layers_list'] as $layer ) {

						$layer['alt'] = Control_Media::get_image_alt( $layer['premium_parallax_layer_image'] );

						array_push( $layers, $layer );

					}
				}

				$parallax_settings = array_merge(
					$parallax_settings,
					array(
						'items'   => $layers,
						'devices' => $settings['premium_parallax_layers_devices'],
						'speed'   => $settings['frames'],
					)
				);

			}

			$element->add_render_attribute( '_wrapper', 'data-pa-parallax', wp_json_encode( $parallax_settings ) );

		}
	}

	/**
	 * Check Assets Enqueue
	 *
	 * Check if the assets files should be loaded.
	 *
	 * @since 2.6.3
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_assets_enqueue( $element ) {

		if ( $this->load_assets ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_parallax_switcher' ) ) {

			$this->enqueue_styles();

			$this->enqueue_scripts();

			$this->load_assets = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/column/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );
		}

	}
}
