<?php
/**
 * Add Happy Particle Effects to Section and Column
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Extension;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

defined( 'ABSPATH' ) || die();

class Particle_Effects {

    /**
     * @var mixed
     */
    static $should_script_enqueue = false;

    public static function init() {

        add_action( 'elementor/element/after_section_end', [ __CLASS__, 'register_controls' ], 10, 3 );

        add_action( 'elementor/preview/enqueue_scripts', [__CLASS__, 'enqueue_scripts'] );

        add_action( 'elementor/column/print_template', [ __CLASS__, '_print_template' ], 10, 2 );
        add_action( 'elementor/section/print_template', [ __CLASS__, '_print_template' ], 10, 2 );
        add_action( 'elementor/container/print_template', [ __CLASS__, '_print_template' ], 10, 2 );

        add_action( 'elementor/frontend/column/before_render', [ __CLASS__, '_before_render' ], 10, 1 );
        add_action( 'elementor/frontend/section/before_render', [ __CLASS__, '_before_render' ], 10, 1 );
        add_action( 'elementor/frontend/container/before_render', [ __CLASS__, '_before_render' ], 10, 1 );

        add_action( 'elementor/frontend/column/before_render', [ __CLASS__, 'should_script_enqueue' ] );
        add_action( 'elementor/frontend/section/before_render', [__CLASS__, 'should_script_enqueue'] );
        add_action( 'elementor/frontend/container/before_render', [__CLASS__, 'should_script_enqueue'] );
    }

    /**
     * Enqueue Scripts
     */
    public static function enqueue_scripts() {

        if ( !wp_script_is( 'particles', 'enqueued' ) ) {
            wp_enqueue_script(
                'particles',
                HAPPY_ADDONS_PRO_ASSETS . 'vendor/particles/particles.min.js',
                ['jquery'],
                HAPPY_ADDONS_PRO_VERSION,
                true
            );

        }

        $suffix = ha_is_script_debug_enabled() ? '.' : '.min.';
        $extension_js = HAPPY_ADDONS_PRO_DIR_PATH . 'assets/js/happy-particles-init' . $suffix . 'js';
        if (file_exists($extension_js)) {
			wp_add_inline_script(
				'elementor-frontend',
				file_get_contents( $extension_js )
			);
        }
    }

    /**
     * Set should_script_enqueue based extension settings
     *
     * @param Element_Base $section
     * @return void
     */
    public static function should_script_enqueue( Element_Base $element ) {
        if ( self::$should_script_enqueue ) {
            return;
        }

        if ( 'yes' == $element->get_settings_for_display( 'ha_enable_particles' ) ) {
            self::$should_script_enqueue = true;

            self::enqueue_scripts();

            remove_action( 'elementor/frontend/column/before_render', [__CLASS__, 'should_script_enqueue'] );
            remove_action( 'elementor/frontend/section/before_render', [__CLASS__, 'should_script_enqueue'] );
            remove_action( 'elementor/frontend/container/before_render', [__CLASS__, 'should_script_enqueue'] );
        }
    }

    /**
     * Register Particle Backgrounds controls.
     *
     * @access public
     * @param object $element for current element.
     * @param object $section_id for section ID.
     * @param array  $args for section args.
     */
    public static function register_controls( $element, $section_id, $args ) {

        if (  ( 'section' === $element->get_name() && 'section_background' === $section_id ) || ( 'container' === $element->get_name() && 'section_background' === $section_id ) || ( 'column' === $element->get_name() && 'section_style' === $section_id ) ) {

            $element->start_controls_section(
                'ha_particles',
                [
                    'tab'   => Controls_Manager::TAB_STYLE,
                    'label' => __( 'Happy Particle Effects', 'happy-addons-pro' ),
                ]
            );

            $element->add_control(
                'ha_enable_particles',
                [
                    'type'         => Controls_Manager::SWITCHER,
                    'label'        => __( 'Enable Particle Background', 'happy-addons-pro' ),
                    'default'      => '',
                    'label_on'     => __( 'Yes', 'happy-addons-pro' ),
                    'label_off'    => __( 'No', 'happy-addons-pro' ),
                    'return_value' => 'yes',
                    'prefix_class' => 'ha-particle-',
                    'render_type'  => 'template', //ui
                     // 'frontend_available' => true,
                ]
            );

            $element->add_control(
                'ha_particles_styles',
                [
                    'label'       => __( 'Style', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SELECT,
                    'default'     => 'nasa',
                    'options'     => [
                        'default' => __( 'Polygon', 'happy-addons-pro' ),
                        'nasa'    => __( 'NASA', 'happy-addons-pro' ),
                        'snow'    => __( 'Snow', 'happy-addons-pro' ),
                        'custom'  => __( 'Custom', 'happy-addons-pro' ),
                    ],
                    'condition'   => [
                        'ha_enable_particles' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'help_doc_particles_1',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => __( 'Add custom JSON for the Particle Background below. To generate a completely customized background style follow steps below - ', 'happy-addons-pro' ),
                    'content_classes' => 'ha-editor-doc ha-editor-description',
                    'condition'       => [
                        'ha_enable_particles' => 'yes',
                        'ha_particles_styles' => 'custom',
                    ],
                ]
            );

            $element->add_control(
                'help_doc_particles_2',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf( __( '1. Visit a link %1$s here %2$s and choose required attributes for particle </br></br> 2. Once a custom style is created, download JSON from "Download current config (json)" link </br></br> 3. Copy JSON code from the downloaded file and paste it below', 'happy-addons-pro' ), '<a href="https://vincentgarreau.com/particles.js/" target="_blank" rel="noopener">', '</a>' ),
                    'content_classes' => 'ha-editor-doc ha-editor-description',
                    'condition'       => [
                        'ha_enable_particles' => 'yes',
                        'ha_particles_styles' => 'custom',
                    ],
                ]
            );

            $element->add_control(
                'ha_particle_json',
                [
                    'type'        => Controls_Manager::CODE,
                    'default'     => '',
                    'condition'   => [
                        'ha_enable_particles' => 'yes',
                        'ha_particles_styles' => 'custom',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_color',
                [
                    'label'       => __( 'Particle Color', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::COLOR,
                    'alpha'       => false,
                    'condition'   => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_opacity',
                [
                    'label'       => __( 'Opacity', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SLIDER,
                    'range'       => [
                        'px' => [
                            'min'  => 0,
                            'max'  => 1,
                            'step' => 0.1,
                        ],
                    ],
                    'condition'   => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_direction',
                [
                    'label'       => __( 'Flow Direction', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SELECT,
                    'default'     => 'bottom',
                    'options'     => [
                        'top'          => __( 'Top', 'happy-addons-pro' ),
                        'bottom'       => __( 'Bottom', 'happy-addons-pro' ),
                        'left'         => __( 'Left', 'happy-addons-pro' ),
                        'right'        => __( 'Right', 'happy-addons-pro' ),
                        'top-left'     => __( 'Top Left', 'happy-addons-pro' ),
                        'top-right'    => __( 'Top Right', 'happy-addons-pro' ),
                        'bottom-left'  => __( 'Bottom Left', 'happy-addons-pro' ),
                        'bottom-right' => __( 'Bottom Right', 'happy-addons-pro' ),
                    ],
                    'condition'   => [
                        'ha_enable_particles' => 'yes',
                        'ha_particles_styles' => 'snow',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_enable_advanced',
                [
                    'type'         => Controls_Manager::SWITCHER,
                    'label'        => __( 'Advanced Settings', 'happy-addons-pro' ),
                    'default'      => 'no',
                    'label_on'     => __( 'Yes', 'happy-addons-pro' ),
                    'label_off'    => __( 'No', 'happy-addons-pro' ),
                    'return_value' => 'yes',
                    'prefix_class' => 'ha-particle-adv-',
                    'condition'    => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                    ],
                    'render_type'  => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_number',
                [
                    'label'       => __( 'Number of Particles', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SLIDER,
                    'range'       => [
                        'px' => [
                            'min' => 1,
                            'max' => 500,
                        ],
                    ],
                    'condition'   => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                        'ha_enable_advanced'   => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_size',
                [
                    'label'       => __( 'Particle Size', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SLIDER,
                    'range'       => [
                        'px' => [
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                    'condition'   => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                        'ha_enable_advanced'   => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_particles_speed',
                [
                    'label'       => __( 'Move Speed', 'happy-addons-pro' ),
                    'type'        => Controls_Manager::SLIDER,
                    'range'       => [
                        'px' => [
                            'min' => 1,
                            'max' => 10,
                        ],
                    ],
                    'condition'   => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                        'ha_enable_advanced'   => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'ha_enable_interactive',
                [
                    'type'         => Controls_Manager::SWITCHER,
                    'label'        => __( 'Enable Hover Effect', 'happy-addons-pro' ),
                    'default'      => 'no',
                    'label_on'     => __( 'Yes', 'happy-addons-pro' ),
                    'label_off'    => __( 'No', 'happy-addons-pro' ),
                    'return_value' => 'yes',
                    'condition'    => [
                        'ha_enable_particles'  => 'yes',
                        'ha_particles_styles!' => 'custom',
                        'ha_enable_advanced'   => 'yes',
                    ],
                    'render_type'  => 'template',
                ]
            );

            $element->add_control(
                'help_doc_interactive',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => __( 'Particle hover effect will not work in the following scenarios - </br></br> 1. In the Elementor backend editor</br></br> 2. Content/Spacer added in the section/column occupies the entire space and leaves it inaccessible. Adding padding to the section/column can resolve this.', 'happy-addons-pro' ),
                    'content_classes' => 'ha-editor-doc',
                    'condition'       => [
                        'ha_enable_particles'   => 'yes',
                        'ha_particles_styles!'  => 'custom',
                        'ha_enable_advanced'    => 'yes',
                        'ha_enable_interactive' => 'yes',
                    ],
                ]
            );

            $element->end_controls_section();
        }
    }

    /**
     * Render Particles Background output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @access public
     * @param object $element for current element.
     */
    public static function _before_render( $element ) {

        if ( $element->get_name() !== 'section' && $element->get_name() !== 'container' && $element->get_name() !== 'column' ) {
            return;
        }

        $settings = $element->get_settings();
        $node_id = $element->get_id();
        $is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

        if ( 'yes' === $settings['ha_enable_particles'] ) {

            $element->add_render_attribute( '_wrapper', 'data-ha-partstyle', $settings['ha_particles_styles'] );
            $element->add_render_attribute( '_wrapper', 'data-ha-partcolor', $settings['ha_particles_color'] );
            $element->add_render_attribute( '_wrapper', 'data-ha-partopacity', $settings['ha_particles_opacity']['size'] );
            $element->add_render_attribute( '_wrapper', 'data-ha-partdirection', $settings['ha_particles_direction'] );

            if ( 'yes' === $settings['ha_enable_advanced'] ) {
                $element->add_render_attribute( '_wrapper', 'data-ha-partnum', $settings['ha_particles_number']['size'] );
                $element->add_render_attribute( '_wrapper', 'data-ha-partsize', $settings['ha_particles_size']['size'] );
                $element->add_render_attribute( '_wrapper', 'data-ha-partspeed', $settings['ha_particles_speed']['size'] );
                if ( $is_editor ) {
                    $element->add_render_attribute( '_wrapper', 'data-ha-interactive', 'no' );
                } else {
                    $element->add_render_attribute( '_wrapper', 'data-ha-interactive', $settings['ha_enable_interactive'] );
                }
            }

            if ( 'custom' === $settings['ha_particles_styles'] ) {
                $element->add_render_attribute( '_wrapper', 'data-ha-partdata', json_encode( json_decode( $settings['ha_particle_json'] ) ) );
            }
        }
    }

    /**
     * Render Particles Background output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @access public
     * @param object $template for current template.
     * @param object $widget for current widget.
     */
    public static function _print_template( $template, $widget ) {
		// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
        if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'container' && $widget->get_name() !== 'column' ) {
            return $template;
        }
        $old_template = $template;
        ob_start();
        ?>
		<# if( 'yes' == settings.ha_enable_particles ) {

			view.addRenderAttribute( 'particle_data', 'id', 'ha-particle-' + view.getID() );
			view.addRenderAttribute( 'particle_data', 'class', 'ha-particle-wrapper' );
			view.addRenderAttribute( 'particle_data', 'data-ha-partstyle', settings.ha_particles_styles );
			view.addRenderAttribute( 'particle_data', 'data-ha-partcolor', settings.ha_particles_color );
			view.addRenderAttribute( 'particle_data', 'data-ha-partopacity', settings.ha_particles_opacity.size );
			view.addRenderAttribute( 'particle_data', 'data-ha-partdirection', settings.ha_particles_direction );

			if( 'yes' == settings.ha_enable_advanced ) {
				view.addRenderAttribute( 'particle_data', 'data-ha-partnum', settings.ha_particles_number.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-partsize', settings.ha_particles_size.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-partspeed', settings.ha_particles_speed.size );
				view.addRenderAttribute( 'particle_data', 'data-ha-interactive', 'no' );

			}
			if ( 'custom' == settings.ha_particles_styles ) {
				view.addRenderAttribute( 'particle_data', 'data-ha-partdata', settings.ha_particle_json );
			}
			#>
			<div {{{ view.getRenderAttributeString( 'particle_data' ) }}}></div>
		<# } #>
		<?php
		$slider_content = ob_get_contents();
        ob_end_clean();
        $template = $slider_content . $old_template;

        return $template;
    }

}

Particle_Effects::init();
