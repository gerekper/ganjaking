<?php
/**
 * Class: Module
 * Name: Section Ken Burns
 * Slug: premium-kenburns
 */

namespace PremiumAddonsPro\Modules\PremiumSectionKenburns;

use Elementor\Controls_Manager;
use Elementor\Repeater;

use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Ken Burns section addon.
 */
class Module extends Module_Base {

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		parent::__construct();

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Section Ken Burns is enabled.
		$kenburns = $modules['premium-kenburns'];

		if ( ! $kenburns ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Creates Premium Prallax tab at the end of section/column layout tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		// insert data before section/column rendering.
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
                        if ( "undefined" == typeof $scope || ! $scope.hasClass( "premium-kenburns-yes" ) ) {
                            return;
                        }

                        var id = $scope.data("id");
                        window.scopes_array[ id ] = $scope;

                    });
                });

                jQuery(document).ready(function(){

                    if ( jQuery.find( ".premium-kenburns-yes" ).length < 1 ) {
                        return;
                    }

                    var url = papro_addons.kenburns_url;

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
	 * Register Ken Burns controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_kenburns',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Ken Burns Effect', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'premium_kenburns_notice',
			array(
				'raw'  => __( 'Add the images that you need, Save and Preview to see your changes', 'premium-addons-pro' ),
				'type' => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_kenburns_switcher',
			array(
				'label'        => __( 'Enable Ken Burns Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-kenburns-',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'premium_kenburns_images',
			array(
				'label'       => __( 'Upload Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'premium_kenburns_image_fit',
			array(
				'label'       => __( 'Image Fit', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'pa-fill'    => __( 'Fill', 'premium-addons-pro' ),
					'pa-contain' => __( 'Contain', 'premium-addons-pro' ),
					'pa-cover'   => __( 'Cover', 'premium-addons-pro' ),
				),
				'default'     => 'pa-fill',
				'label_block' => 'true',
			)
		);

		$repeater->add_control(
			'premium_kenburns_dir',
			array(
				'label'       => __( 'Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'center' => __( 'Center Center', 'premium-addons-pro' ),
					'cl'     => __( 'Center Left', 'premium-addons-pro' ),
					'cr'     => __( 'Center Right', 'premium-addons-pro' ),
					'tc'     => __( 'Top Center', 'premium-addons-pro' ),
					'bc'     => __( 'Bottom Center', 'premium-addons-pro' ),
					'tl'     => __( 'Top Left', 'premium-addons-pro' ),
					'tr'     => __( 'Top Right', 'premium-addons-pro' ),
					'bl'     => __( 'Bottom Left', 'premium-addons-pro' ),
					'br'     => __( 'Bottom Right', 'premium-addons-pro' ),
				),
				'default'     => 'center',
				'label_block' => 'true',
			)
		);

		$repeater->add_control(
			'premium_kenburns_zoom_dir',
			array(
				'label'       => __( 'Zoom Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'in'  => __( 'In', 'premium-addons-pro' ),
					'out' => __( 'Out', 'premium-addons-pro' ),
				),
				'default'     => 'in',
				'label_block' => 'true',
			)
		);

		$element->add_control(
			'premium_kenburns_repeater',
			array(
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			)
		);

		$element->add_control(
			'premium_kenburns_speed',
			array(
				'label'     => __( 'Scale Speed (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-kenburns-img' => 'animation-duration: {{SIZE}}s;',
				),
			)
		);

		$element->add_control(
			'premium_kenburns_effect',
			array(
				'label'       => __( 'Effect', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fade'       => __( 'Fade', 'premium-addons-pro' ),
					'scrollHorz' => __( 'Scroll Horizontal', 'premium-addons-pro' ),
					'scrollVert' => __( 'Scroll Vertical', 'premium-addons-pro' ),
				),
				'default'     => 'fade',
				'label_block' => 'true',
			)
		);

		$element->add_control(
			'premium_kenburns_fade',
			array(
				'label' => __( 'Effect Speed (sec)', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 0.1,
					),
				),
			)
		);

		$element->add_control(
			'premium_kenburns_infinite',
			array(
				'label'        => __( 'Infinite', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This option works only if you have only one image slide', 'premium-addons-pro' ),
				'return_value' => true,
			)
		);

		$element->add_control(
			'premium_kenburns_overlay',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-kenburns-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Ken Burns output on the frontend.
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

		if ( ( 'section' === $type || 'column' === $type ) && 'yes' === $settings['premium_kenburns_switcher'] && isset( $settings['premium_kenburns_repeater'] ) ) {

			$transition = 1000 * ( ( isset( $settings['premium_kenburns_speed'] ) && ! empty( $settings['premium_kenburns_speed']['size'] ) ) ? $settings['premium_kenburns_speed']['size'] : 6.5 );

			$fade = 1000 * ( ( isset( $settings['premium_kenburns_fade'] ) && ! empty( $settings['premium_kenburns_fade']['size'] ) ) ? $settings['premium_kenburns_fade']['size'] : 0.5 );

			$slides = array();

			foreach ( $settings['premium_kenburns_repeater'] as $slide ) {

				array_push( $slides, $slide );

			}

			$kenburns_settings = array(
				'fx'       => $settings['premium_kenburns_effect'],
				'speed'    => $transition,
				'fade'     => $fade,
				'slides'   => $slides,
				'infinite' => $settings['premium_kenburns_infinite'],
			);

			$element->add_render_attribute( '_wrapper', 'data-kenburns', wp_json_encode( $kenburns_settings ) );

		}
	}
}
