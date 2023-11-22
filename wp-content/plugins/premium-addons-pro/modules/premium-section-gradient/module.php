<?php
/**
 * Class: Module
 * Name: Section Animated Gradient
 * Slug: premium-gradient
 */

namespace PremiumAddonsPro\Modules\PremiumSectionGradient;

use Elementor\Repeater;
use Elementor\Controls_Manager;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Animated Gradient section addon.
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

		// Checks if Section Gradient is enabled.
		$gradient = $modules['premium-gradient'];

		if ( ! $gradient ) {
			return;
		}

		// Enqueue the required CSS/JS files.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Animated Gradient tab at the end of section layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );

		add_action( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );

		// Insert data before section rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'check_assets_enqueue' ) );

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

		if ( ! wp_script_is( 'pa-gradient', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-gradient' );
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
	 * Register Animated Gradient controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_gradient',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Animated Gradient', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_gradient_update',
			array(
				'label' => '<div class="elementor-update-preview editor-pa-preview-update" style="background-color: #fff;"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
				'type'  => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_gradient_switcher',
			array(
				'label'        => __( 'Enable Animated Gradient', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-gradient-',
			)
		);

		$repeater = new Repeater();

		$element->add_control(
			'premium_gradient_notice',
			array(
				'raw'  => __( 'NOTICE: Please remove Elementor\'s background image/gradient for this section/column', 'premium-addons-pro' ),
				'type' => Controls_Manager::RAW_HTML,
			)
		);

		$repeater->add_control(
			'premium_gradient_colors',
			array(
				'label' => __( 'Select Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$element->add_control(
			'premium_gradient_colors_repeater',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ premium_gradient_colors }}}',
			)
		);

		$element->add_control(
			'wave_effect_switcher',
			array(
				'label'        => __( 'Enable Wave Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-gradient-wave-',
			)
		);

		$element->add_control(
			'premium_gradient_speed',
			array(
				'label'     => __( 'Animation Speed (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'selectors' => array(
					'{{WRAPPER}}.premium-gradient-yes:not(.premium-gradient-wave-yes), {{WRAPPER}} .premium-wave-gradient-{{ID}}' => 'animation-duration: {{VALUE}}s',
				),
			)
		);

		$element->add_control(
			'premium_gradient_angle',
			array(
				'label'   => __( 'Gradient Angle (degrees)', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => -45,
				'min'     => -180,
				'max'     => 180,
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Animated Gradient output in the editor.
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

		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'container' && $widget->get_name() !== 'column' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();

		?>
		<# if( 'yes' === settings.premium_gradient_switcher ) {


			var gradientSettings = {},
				colorsArr = [];

			_.each( settings.premium_gradient_colors_repeater, function( color, index ) {
				colorsArr.push( color );
			});

			gradientSettings.angle  = settings.premium_gradient_angle;
			gradientSettings.colors = colorsArr;

			view.addRenderAttribute( 'gradient_data', {
				'id': 'premium-animated-gradient-' + view.getID(),
				'class': 'premium-animated-gradient-wrapper',
				'data-gradient': JSON.stringify( gradientSettings )
			});

		#>
			<div {{{ view.getRenderAttributeString( 'gradient_data' ) }}}></div>
		<# } #>
		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Animated Gradient output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$settings = $element->get_settings_for_display();

		if ( 'yes' === $settings['premium_gradient_switcher'] && isset( $settings['premium_gradient_colors_repeater'] ) ) {

			$grad_angle = ! empty( $settings['premium_gradient_angle'] ) ? $settings['premium_gradient_angle'] : -45;

			$colors = array();

			foreach ( $settings['premium_gradient_colors_repeater'] as $color ) {

				array_push( $colors, $color );

			}

			$gradient_settings = array(
				'angle'  => $grad_angle,
				'colors' => $colors,
			);

			$element->add_render_attribute( '_wrapper', 'data-gradient', wp_json_encode( $gradient_settings ) );
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

		if ( 'yes' === $element->get_settings_for_display( 'premium_gradient_switcher' ) ) {

			$this->enqueue_styles();

			$this->enqueue_scripts();

			$this->load_assets = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/column/before_render', array( $this, 'check_assets_enqueue' ) );
		}

	}
}
