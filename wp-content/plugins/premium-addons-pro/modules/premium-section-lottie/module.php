<?php
/**
 * Class: Module
 * Name: Section Lottie
 * Slug: premium-lottie
 */

namespace PremiumAddonsPro\Modules\PremiumSectionLottie;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Lottie section addon.
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

		// Checks if Section Lottie is enabled.
		$lottie = $modules['premium-lottie'];

		if ( ! $lottie ) {
			return;
		}

		// Enqueue the required CSS/JS files.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Register Controls inside Section/Column Layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );

		// insert data before section rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );

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
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'elementor-waypoints', 'enqueued' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		if ( ! wp_script_is( 'premium-pro', 'enqueued' ) ) {
			wp_enqueue_script( 'premium-pro' );
		}

		if ( ! wp_script_is( 'lottie-js', 'enqueued' ) ) {
			wp_enqueue_script( 'lottie-js' );
		}

	}

	/**
	 * Register Lottie Animations controls.
	 *
	 * @since 1.9.4
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_lottie',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Lottie Animations', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_lottie_switcher',
			array(
				'label'        => __( 'Enable Lottie Animations', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-lottie-',
			)
		);

		$url     = 'https://premiumaddons.com/docs/how-to-speed-up-elementor-pages-with-many-lottie-animations';
		$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'lottie_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, __( 'How to speed up pages with many Lottie animations »', 'premium-addons-pro' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info editor-pa-doc',
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
			'source',
			array(
				'label'   => __( 'File Source', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'url'  => __( 'External URL', 'premium-addons-pro' ),
					'file' => __( 'Media File', 'premium-addons-pro' ),
				),
				'default' => 'url',
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'source' => 'url',
				),
			)
		);

		$repeater->add_control(
			'lottie_file',
			array(
				'label'              => __( 'Upload JSON File', 'elementor-pro' ),
				'type'               => Controls_Manager::MEDIA,
				'media_type'         => 'application/json',
				'frontend_available' => true,
				'condition'          => array(
					'source' => 'file',
				),
			)
		);

		$repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$repeater->add_control(
			'lottie_speed',
			array(
				'label'   => __( 'Speed', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 0.1,
				'max'     => 3,
				'step'    => 0.1,
			)
		);

		$repeater->add_control(
			'hover_action',
			array(
				'label'   => __( 'Hover Action', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'  => __( 'None', 'premium-addons-pro' ),
					'play'  => __( 'Play', 'premium-addons-pro' ),
					'pause' => __( 'Pause', 'premium-addons-pro' ),
				),
				'default' => 'none',
			)
		);

		$repeater->add_control(
			'start_on_visible',
			array(
				'label'        => __( 'Start Animation On Viewport', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Enable this option if you want the animation to start when the element is visible on the viewport', 'premium-addons-pro' ),
				'return_value' => 'true',
				'condition'    => array(
					'hover_action!'      => 'play',
					'animate_on_scroll!' => 'true',
				),
			)
		);

		$repeater->add_control(
			'animate_on_scroll',
			array(
				'label'        => __( 'Animate On Scroll', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'hover_action!'     => 'play',
					'start_on_visible!' => 'true',
					'lottie_reverse!'   => 'true',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_animate_speed',
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
					'hover_action!'     => 'play',
					'animate_on_scroll' => 'true',
					'lottie_reverse!'   => 'true',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_animate_view',
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
					'hover_action!'     => 'play',
					'animate_on_scroll' => 'true',
					'lottie_reverse!'   => 'true',
				),
			)
		);

		$repeater->add_control(
			'lottie_renderer',
			array(
				'label'       => __( 'Render As', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'svg'    => __( 'SVG', 'premium-addons-pro' ),
					'canvas' => __( 'Canvas', 'premium-addons-pro' ),
				),
				'default'     => 'svg',
				'classes'     => 'editor-pa-spacer',
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'render_notice',
			array(
				'raw'             => __( 'Set render type to canvas if you\'re having performance issues on the page.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$repeater->add_responsive_control(
			'premium_lottie_hor',
			array(
				'label'       => __( 'Horizontal Position (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'default'     => array(
					'size' => 50,
				),
				'min'         => 0,
				'max'         => 100,
				'label_block' => true,
				'separator'   => 'before',
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_lottie_ver',
			array(
				'label'       => __( 'Vertical Position (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'default'     => array(
					'size' => 50,
				),
				'min'         => 0,
				'max'         => 100,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_lottie_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 60,
					),
				),
				'label_block' => true,
				'separator'   => 'before',
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-lottie-canvas, {{WRAPPER}} {{CURRENT_ITEM}}.premium-lottie-svg svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'opacity: {{SIZE}};',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_rotate',
			array(
				'label'       => __( 'Rotate', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set rotation value in degrees', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'     => array(
					'size' => 0,
				),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'transform: rotate({{SIZE}}deg)',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_lottie_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			)
		);

		$repeater->add_control(
			'premium_lottie_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_lottie_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_lottie_shadow_hover',
				'label'    => __( 'Hover Box Shadow', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			)
		);

		$repeater->add_responsive_control(
			'premium_lottie_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_parallax',
			array(
				'label'       => __( 'Scroll Parallax', 'premium-addons-pro' ),
				'description' => __( 'Enable or disable vertical scroll parallax', 'premium-addons-pro' ),
				'separator'   => 'before',
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_lottie_parallax_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Up', 'premium-addons-pro' ),
					'down' => __( 'Down', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array(
					'premium_lottie_parallax' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_parallax_speed',
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
					'premium_lottie_parallax' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_parallax_view',
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
					'premium_lottie_parallax' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_lottie_zindex',
			array(
				'label'       => __( 'Z-index', 'premium-addons-pro' ),
				'description' => __( 'Set z-index for the current layer', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'classes'     => 'editor-pa-spacer',
				'default'     => 2,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{VALUE}}',
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

		$element->add_control(
			'premium_lottie_repeater',
			array(
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'condition'     => array(
					'premium_lottie_switcher' => 'yes',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Lottie Animations output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.9.4
	 * @access public
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function _print_template( $template, $widget ) {

		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'container' ) {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' === settings.premium_lottie_switcher ) {

			view.addRenderAttribute( 'lottie_data', {
				'id': 'premium-lottie-' + view.getID(),
				'class': 'premium-lottie-wrapper',
				'data-pa-lottie': JSON.stringify( settings.premium_lottie_repeater )
			});

		#>
			<div {{{ view.getRenderAttributeString( 'lottie_data' ) }}}>
				<# _.each( settings.premium_lottie_repeater, function( layer , index ) {

					var key = 'lottie_' + layer._id;

					view.addRenderAttribute(key, 'class', [
						'premium-lottie-layer',
						'premium-lottie-animation',
						'premium-lottie-' + layer.lottie_renderer,
						'elementor-repeater-item-' + layer._id
					]);

					view.addRenderAttribute(key, 'initialized', true );
					#>
						<div {{{ view.getRenderAttributeString( key ) }}}></div>
					<#
				});
				#>
			</div>
		<# } #>
		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}

	/**
	 * Render Lottie Animations output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.9.4
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$settings = $element->get_settings_for_display();

		$lottie = $settings['premium_lottie_switcher'];

		if ( 'yes' !== $lottie ) {
			return;
		}

		$repeater = $settings['premium_lottie_repeater'];

		if ( ! count( $repeater ) ) {
			return;
		}

		$element->add_render_attribute( '_wrapper', 'data-pa-lottie', wp_json_encode( $repeater ) );

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

		if ( 'yes' === $element->get_settings_for_display( 'premium_lottie_switcher' ) ) {

			$this->enqueue_styles();

			$this->enqueue_scripts();

			$this->load_assets = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_assets_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_assets_enqueue' ) );
		}

	}
}
