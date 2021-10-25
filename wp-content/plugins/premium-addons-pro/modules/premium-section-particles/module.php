<?php
/**
 * Class: Module
 * Name: Section Particles
 * Slug: premium-particles
 */

namespace PremiumAddonsPro\Modules\PremiumSectionParticles;

use Elementor\Controls_Manager;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Particles section addon.
 */
class Module extends Module_Base {

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		parent::__construct();

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Section Particles is enabled.
		$particles = $modules['premium-particles'];

		if ( ! $particles ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register Controls inside Section/Column Layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, '_print_template' ), 10, 2 );

		// Insert data before Section/Column rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ), 10, 1 );

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ( true === \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) || ( function_exists( 'elementor_location_exits' ) && ( elementor_location_exits( 'archive', true ) || elementor_location_exits( 'single', true ) ) ) ) {
			wp_add_inline_script(
				'elementor-frontend',
				'window.scopes_array = {};
                window.backend = 0;
                jQuery( window ).on( "elementor/frontend/init", function() {

                    elementorFrontend.hooks.addAction( "frontend/element_ready/global", function( $scope, $ ){

                        if ( "undefined" == typeof $scope || ! $scope.hasClass( "premium-particles-yes" ) ) {
                            return;
                        }

                        if(elementorFrontend.isEditMode()){

                            window.current_scope = $scope;

                            var url = papro_addons.particles_url;
                            jQuery.cachedAssets = function( url, options ) {
                                // Allow user to set any option except for dataType, cache, and url.
                                options = jQuery.extend( options || {}, {
                                    dataType: "script",
                                    cache: true,
                                    url: url
                                });
                                // Return the jqXHR object so we can chain callbacks.
                                return jQuery.ajax( options );
                            };
                            jQuery.cachedAssets( url );
                            window.backend = 1;
                        } else {
                            var id = $scope.data("id");
                            window.scopes_array[ id ] = $scope;
                        }
                    });
                });

                jQuery(document).ready(function(){

                    if ( jQuery.find( ".premium-particles-yes" ).length < 1 ) {
                        return;
                    }

                    var url = papro_addons.particles_url;

                    jQuery.cachedAssets = function( url, options ) {
                        // Allow user to set any option except for dataType, cache, and url.
                        options = jQuery.extend( options || {}, {
                            dataType: "script",
                            cache: true,
                            url: url
                        });

                        // Return the jqXHR object so we can chain callbacks.
                        return jQuery.ajax( options );
                    };
                    jQuery.cachedAssets( url );
                });	'
			);
		}
	}

	/**
	 * Register Particles controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_particles',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Particles', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_particles_switcher',
			array(
				'label'        => __( 'Enable Particles', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-particles-',
				'render_type'  => 'template',
			)
		);

		$element->add_control(
			'premium_particles_zindex',
			array(
				'label'   => __( 'Z-index', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$element->add_control(
			'premium_particles_custom_style',
			array(
				'label'       => __( 'Custom Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CODE,
				'description' => __( 'Particles has been updated with many new features. You can now generate the JSON config from <a href="https://premiumaddons.com/docs/how-to-use-tsparticles-in-elementor-particles-section-addon/?utm_source=pa-dashboard&utm_medium=pa-editor&utm_campaign=pa-plugin" target="_blank">here</a> or <a href="http://vincentgarreau.com/particles.js/#default" target="_blank">here</a>', 'premium-addons-pro' ),
				'render_type' => 'template',
			)
		);

		$element->add_control(
			'particles_background_notice',
			array(
				'raw'             => __( 'Kindly, be noted that you will need to add a background as particles JSON code doesn\'t include a background color', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->add_control(
			'premium_particles_responsive',
			array(
				'label'       => __( 'Apply Particles On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Particles output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template current template.
	 * @param object $widget current widget.
	 */
	public function _print_template( $template, $widget ) {

		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' ) {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<# if( 'yes' === settings.premium_particles_switcher ) {

			view.addRenderAttribute( 'particles_data', {
				'id': 'premium-particles-' + view.getID(),
				'class': 'premium-particles-wrapper',
				'data-particles-style': settings.premium_particles_custom_style,
				'data-particles-zindex':  settings.premium_particles_zindex,
				'data-particles-devices': settings.premium_particles_responsive
			});

		#>
			<div {{{ view.getRenderAttributeString( 'particles_data' ) }}}></div>
		<# } #>
		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Particles output on the frontend.
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

		$zindex = ! empty( $settings['premium_particles_zindex'] ) ? $settings['premium_particles_zindex'] : 0;

		if ( ( 'section' === $type || 'column' === $type ) && 'yes' === $settings['premium_particles_switcher'] ) {

			if ( ! empty( $settings['premium_particles_custom_style'] ) ) {

				$particles_settings = array(
					'zindex'     => $zindex,
					'style'      => $settings['premium_particles_custom_style'],
					'responsive' => $settings['premium_particles_responsive'],
				);

				$element->add_render_attribute(
					'_wrapper',
					array(
						'data-particles-style'   => $particles_settings['style'],
						'data-particles-zindex'  => $particles_settings['zindex'],
						'data-particles-devices' => $particles_settings['responsive'],
					)
				);

			}
		}
	}
}
