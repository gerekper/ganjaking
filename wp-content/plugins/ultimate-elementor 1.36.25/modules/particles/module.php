<?php
/**
 * UAEL Particles Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Particles;

use Elementor\Controls_Manager;

use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {



	/**
	 * Module should load or not.
	 *
	 * @since 1.12.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.12.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-particles';
	}

	/**
	 * Check if this is a widget.
	 *
	 * @since 1.12.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public function is_widget() {
		return false;
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.12.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Particles',
		);
	}

	/**
	 * Instance
	 *
	 * @var Instance
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		if ( UAEL_Helper::is_widget_active( 'Particles' ) ) {
			$this->add_actions();
		}
	}

	/**
	 * Add actions and set scripts dependencies required to run the widget.
	 *
	 * @since 1.12.0
	 * @access protected
	 */
	protected function add_actions() {

		// Enqueue scripts.
		add_action( 'wp_footer', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/element/after_section_end', array( $this, 'register_controls' ), 10, 3 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, '_print_template' ), 10, 2 );

		add_action( 'elementor/frontend/column/before_render', array( $this, '_before_render' ), 10, 1 );
		add_action( 'elementor/frontend/section/before_render', array( $this, '_before_render' ), 10, 1 );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the scripts defined as extension dependencies and enqueues them.
	 *
	 * @since 1.12.0
	 * @access public
	 */
	public function enqueue_scripts() {

		$is_elementor = false;

		if ( false !== get_the_ID() ) {
			$ele_document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );

			if ( ! is_bool( $ele_document ) ) {
				$is_elementor = $ele_document->is_built_with_elementor();
			}
		}

		if ( ( true === \Elementor\Plugin::$instance->frontend->has_elementor_in_page() ) || ( true === $is_elementor ) || ( function_exists( 'elementor_location_exits' ) && ( elementor_location_exits( 'archive', true ) || elementor_location_exits( 'single', true ) ) ) ) {
					wp_add_inline_script(
						'elementor-frontend',
						'window.scope_array = [];
								window.backend = 0;
								jQuery.cachedScript = function( url, options ) {
									// Allow user to set any option except for dataType, cache, and url.
									options = jQuery.extend( options || {}, {
										dataType: "script",
										cache: true,
										url: url
									});
									// Return the jqXHR object so we can chain callbacks.
									return jQuery.ajax( options );
								};
							    jQuery( window ).on( "elementor/frontend/init", function() {
									elementorFrontend.hooks.addAction( "frontend/element_ready/global", function( $scope, $ ){
										if ( "undefined" == typeof $scope ) {
												return;
										}
										if ( $scope.hasClass( "uael-particle-yes" ) ) {
											window.scope_array.push( $scope );
											$scope.find(".uael-particle-wrapper").addClass("js-is-enabled");
										}else{
											return;
										}
										if(elementorFrontend.isEditMode() && $scope.find(".uael-particle-wrapper").hasClass("js-is-enabled") && window.backend == 0 ){
											var uael_url = uael_particles_script.uael_particles_url;

											jQuery.cachedScript( uael_url );
											window.backend = 1;
										}else if(elementorFrontend.isEditMode()){
											var uael_url = uael_particles_script.uael_particles_url;
											jQuery.cachedScript( uael_url ).done(function(){
												var flag = true;
											});
										}
									});
								});
								 jQuery( document ).on( "ready elementor/popup/show", () => {
									if ( jQuery.find( ".uael-particle-yes" ).length < 1 ) {
										return;
									}
									var uael_url = uael_particles_script.uael_particles_url;
									jQuery.cachedScript = function( url, options ) {
										// Allow user to set any option except for dataType, cache, and url.
										options = jQuery.extend( options || {}, {
											dataType: "script",
											cache: true,
											url: url
										});
										// Return the jqXHR object so we can chain callbacks.
										return jQuery.ajax( options );
									};
									jQuery.cachedScript( uael_url );
								});	'
					);
		}
	}

	/**
	 * Register Particle Backgrounds controls.
	 *
	 * @since 1.12.0
	 * @access public
	 * @param object $element for current element.
	 * @param object $section_id for section ID.
	 * @param array  $args for section args.
	 */
	public function register_controls( $element, $section_id, $args ) {

		if ( ( 'section' === $element->get_name() && 'section_background' === $section_id ) || ( 'column' === $element->get_name() && 'section_style' === $section_id ) ) {
			$element->start_controls_section(
				'uae_particles',
				array(
					'tab'   => Controls_Manager::TAB_STYLE,
					/* translators: %s admin link */
					'label' => sprintf( __( '%1s - Particle Backgrounds', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
				)
			);

			$element->add_control(
				'uae_enable_particles',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Enable Particle Background', 'uael' ),
					'default'      => '',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'prefix_class' => 'uael-particle-',
					'render_type'  => 'template',
				)
			);

			$element->add_control(
				'uae_particles_styles',
				array(
					'label'     => __( 'Style', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'nasa',
					'options'   => array(
						'default'    => __( 'Polygon', 'uael' ),
						'nasa'       => __( 'NASA', 'uael' ),
						'snow'       => __( 'Snow', 'uael' ),
						'snowflakes' => __( 'Snowflakes', 'uael' ),
						'christmas'  => __( 'Christmas', 'uael' ),
						'halloween'  => __( 'Halloween', 'uael' ),
						'custom'     => __( 'Custom', 'uael' ),
					),
					'condition' => array(
						'uae_enable_particles' => 'yes',
					),
				)
			);

			$element->add_control(
				'uae_particles_help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => __( 'Add custom JSON for the Particle Background below. To generate a completely customized background style follow steps below - ', 'uael' ),
					'content_classes' => 'uael-editor-doc uael-editor-description',
					'condition'       => array(
						'uae_enable_particles' => 'yes',
						'uae_particles_styles' => 'custom',
					),
				)
			);

			$element->add_control(
				'uae_particles_help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( '1. Visit a link %1$s here %2$s and choose required attributes for particle </br></br> 2. Once a custom style is created, download JSON from "Download current config (json)" link </br></br> 3. Copy JSON code from the downloaded file and paste it below', 'uael' ), '<a href="https://vincentgarreau.com/particles.js/" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc uael-editor-description',
					'condition'       => array(
						'uae_enable_particles' => 'yes',
						'uae_particles_styles' => 'custom',
					),
				)
			);

			if ( UAEL_Helper::is_internal_links() ) {
				$element->add_control(
					'uae_particles_help_doc_5',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %s admin link */
						'raw'             => sprintf( __( 'To know more about creating a custom style, refer to a document %1$s here %2$s.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/custom-particle-backgrounds/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc uael-editor-description',
						'condition'       => array(
							'uae_enable_particles' => 'yes',
							'uae_particles_styles' => 'custom',
						),
					)
				);
			}

			$element->add_control(
				'uae_particle_json',
				array(
					'type'        => Controls_Manager::CODE,
					'default'     => '',
					'render_type' => 'template',
					'condition'   => array(
						'uae_enable_particles' => 'yes',
						'uae_particles_styles' => 'custom',
					),
				)
			);

			$element->add_control(
				'uae_particles_color',
				array(
					'label'       => __( 'Particle Color', 'uael' ),
					'type'        => Controls_Manager::COLOR,
					'alpha'       => false,
					'condition'   => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => array( 'custom', 'christmas', 'halloween' ),
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'uae_particles_opacity',
				array(
					'label'       => __( 'Opacity', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1,
							'step' => 0.1,
						),
					),
					'condition'   => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => 'custom',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'uae_particles_direction',
				array(
					'label'     => __( 'Flow Direction', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'bottom',
					'options'   => array(
						'top'          => __( 'Top', 'uael' ),
						'bottom'       => __( 'Bottom', 'uael' ),
						'left'         => __( 'Left', 'uael' ),
						'right'        => __( 'Right', 'uael' ),
						'top-left'     => __( 'Top Left', 'uael' ),
						'top-right'    => __( 'Top Right', 'uael' ),
						'bottom-left'  => __( 'Bottom Left', 'uael' ),
						'bottom-right' => __( 'Bottom Right', 'uael' ),
					),
					'condition' => array(
						'uae_enable_particles' => 'yes',
						'uae_particles_styles' => array( 'snow', 'snowflakes', 'christmas' ),
					),
				)
			);

			$element->add_control(
				'uae_enable_advanced',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Advanced Settings', 'uael' ),
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'prefix_class' => 'uael-particle-adv-',
					'render_type'  => 'template',
					'condition'    => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => 'custom',
					),
				)
			);

			$element->add_control(
				'uae_particles_number',
				array(
					'label'       => __( 'Number of Particles', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 500,
						),
					),
					'condition'   => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => 'custom',
						'uae_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'uae_particles_size',
				array(
					'label'       => __( 'Particle Size', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 200,
						),
					),
					'condition'   => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => 'custom',
						'uae_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'uae_particles_speed',
				array(
					'label'       => __( 'Move Speed', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'min' => 1,
							'max' => 10,
						),
					),
					'condition'   => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => 'custom',
						'uae_enable_advanced'   => 'yes',
					),
					'render_type' => 'template',
				)
			);

			$element->add_control(
				'uae_enable_interactive',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Enable Hover Effect', 'uael' ),
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'condition'    => array(
						'uae_enable_particles'  => 'yes',
						'uae_particles_styles!' => array( 'custom' ),
						'uae_enable_advanced'   => 'yes',
					),
					'render_type'  => 'template',
				)
			);

			$element->add_control(
				'uae_particles_hover_effect_help_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => __( 'Particle hover effect will not work in the following scenarios - </br></br> 1. In the Elementor backend editor</br></br> 2. Content/Spacer added in the section/column occupies the entire space and leaves it inaccessible. Adding padding to the section/column can resolve this.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'uae_enable_particles'   => 'yes',
						'uae_particles_styles!'  => array( 'custom' ),
						'uae_enable_advanced'    => 'yes',
						'uae_enable_interactive' => 'yes',
					),
				)
			);

			if ( UAEL_Helper::is_internal_links() ) {
				$element->add_control(
					'uae_particles_hover_effect_help_doc_not_working',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %s admin link */
						'raw'             => sprintf( __( 'Learn more about this %1$s here. %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/particles-hover-effect-not-working/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
						'condition'       => array(
							'uae_enable_particles'   => 'yes',
							'uae_particles_styles!'  => array( 'snowflakes', 'custom', 'christmas' ),
							'uae_enable_advanced'    => 'yes',
							'uae_enable_interactive' => 'yes',
						),
					)
				);
			}

			$element->end_controls_section();
		}
	}

	/**
	 * Render Particles Background output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.12.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function _before_render( $element ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		if ( $element->get_name() !== 'section' && $element->get_name() !== 'column' ) {
			return;
		}

		$settings  = $element->get_settings();
		$node_id   = $element->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( 'yes' === $settings['uae_enable_particles'] ) {
			$element->add_render_attribute( '_wrapper', 'data-uael-partstyle', $settings['uae_particles_styles'] );
			$element->add_render_attribute( '_wrapper', 'data-uael-partcolor', $settings['uae_particles_color'] );
			$element->add_render_attribute( '_wrapper', 'data-uael-partopacity', $settings['uae_particles_opacity']['size'] );
			$element->add_render_attribute( '_wrapper', 'data-uael-partdirection', $settings['uae_particles_direction'] );

			if ( 'yes' === $settings['uae_enable_advanced'] ) {
				$element->add_render_attribute( '_wrapper', 'data-uael-partnum', $settings['uae_particles_number']['size'] );
				$element->add_render_attribute( '_wrapper', 'data-uael-partsize', $settings['uae_particles_size']['size'] );
				$element->add_render_attribute( '_wrapper', 'data-uael-partspeed', $settings['uae_particles_speed']['size'] );
				if ( $is_editor ) {
					$element->add_render_attribute( '_wrapper', 'data-uael-interactive', 'no' );
				} else {
					$element->add_render_attribute( '_wrapper', 'data-uael-interactive', $settings['uae_enable_interactive'] );
				}
			}

			if ( 'custom' === $settings['uae_particles_styles'] ) {
				$element->add_render_attribute( '_wrapper', 'data-uael-partdata', $settings['uae_particle_json'] );
			}
		}
	}

	/**
	 * Render Particles Background output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.12.0
	 * @access public
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function _print_template( $template, $widget ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' ) {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' == settings.uae_enable_particles ) {

			view.addRenderAttribute( 'particle_data', 'id', 'uael-particle-' + view.getID() );
			view.addRenderAttribute( 'particle_data', 'class', 'uael-particle-wrapper' );
			view.addRenderAttribute( 'particle_data', 'data-uael-partstyle', settings.uae_particles_styles );
			view.addRenderAttribute( 'particle_data', 'data-uael-partcolor', settings.uae_particles_color );
			view.addRenderAttribute( 'particle_data', 'data-uael-partopacity', settings.uae_particles_opacity.size );
			view.addRenderAttribute( 'particle_data', 'data-uael-partdirection', settings.uae_particles_direction );

			if( 'yes' == settings.uae_enable_advanced ) {
				view.addRenderAttribute( 'particle_data', 'data-uael-partnum', settings.uae_particles_number.size );
				view.addRenderAttribute( 'particle_data', 'data-uael-partsize', settings.uae_particles_size.size );
				view.addRenderAttribute( 'particle_data', 'data-uael-partspeed', settings.uae_particles_speed.size );
				view.addRenderAttribute( 'particle_data', 'data-uael-interactive', 'no' );

			}
			if ( 'custom' == settings.uae_particles_styles ) {
				view.addRenderAttribute( 'particle_data', 'data-uael-partdata', settings.uae_particle_json );
			}
			#>
			<div {{{ view.getRenderAttributeString( 'particle_data' ) }}}></div> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
		<# } #>
		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}

	/**
	 * Initiator
	 *
	 * @since 1.12.0
	 * @access public
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
