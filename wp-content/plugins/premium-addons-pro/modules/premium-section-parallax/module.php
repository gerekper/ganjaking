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

		// Enqueue the required JS file
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Creates Premium Prallax tab at the end of section/column layout tab
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, '_print_template' ), 10, 2 );

		// insert data before section/column rendering
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

                        if ( "undefined" == typeof $scope || ! $scope.hasClass( "premium-parallax-yes" ) ) {
                                return;
                        }

                        if(elementorFrontend.isEditMode()){

                            window.current_scope = $scope;

                            var url = papro_addons.parallax_url;

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

                    if ( jQuery.find( ".premium-parallax-yes" ).length < 1 ) {
                        return;
                    }

                    var url = papro_addons.parallax_url;

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
	 * Register Parallax controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

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
				'label' => '<div class="elementor-update-preview editor-pa-preview-update" style="background-color: #fff;"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
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
			'premium_parallax_layer_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
				'render_type' => 'template',
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
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_z_index',
			array(
				'label'       => __( 'z-index', 'premium-addons-pro' ),
				'description' => __( 'Set z-index for the current layer', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
			)
		);

		$repeater->add_control(
			'premium_parallax_layer_mouse',
			array(
				'label'       => __( 'Mouse Track', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable mousemove interaction', 'premium-addons-pro' ),
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
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
				'multiple'    => true,
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
				'raw'             => __( 'NEW: Now you can position, resize parallax layers from the preview area', 'premium-addons-pro' ),
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
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'condition' => array(
					'premium_parallax_type' => 'multi',
				),
			)
		);

		$element->add_control(
			'premium_parallax_layers_devices',
			array(
				'label'       => __( 'Apply Scroll Parallax On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
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

		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' ) {
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

				var speed                 = "" !== settings.premium_parallax_speed.size ? settings.premium_parallax_speed.size : 0.5;

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

		$data = $element->get_data();

		$type = $data['elType'];

		$settings = $element->get_settings_for_display();

		$parallax = isset( $settings['premium_parallax_type'] ) ? $settings['premium_parallax_type'] : '';

		if ( ( 'section' === $type || 'column' === $type ) && isset( $parallax ) && '' !== $parallax && 'yes' === $element->get_settings( 'premium_parallax_switcher' ) ) {

			$parallax_settings = array(
				'type' => $parallax,
			);

			if ( 'multi' !== $parallax && 'automove' !== $parallax ) {

				// Fix image bounce issue.
				$element->add_render_attribute( '_wrapper', 'class', 'premium-parallax-section-hide' );

				$speed = isset( $settings['premium_parallax_speed']['size'] ) ? $settings['premium_parallax_speed']['size'] : 0.5;

				$positiont = $settings['background_position_tablet'];
				if ( 'initial' === $positiont ) {
					$positiont = sprintf( '%s%s %s%s', $settings['background_xpos_tablet']['size'], $settings['background_xpos_tablet']['unit'], $settings['background_ypos_tablet']['size'], $settings['background_ypos_tablet']['unit'] );

				}

				$positionm = $settings['background_position_mobile'];
				if ( 'initial' === $positionm ) {
					$positionm = sprintf( '%s%s %s%s', $settings['background_xpos_mobile']['size'], $settings['background_xpos_mobile']['unit'], $settings['background_ypos_mobile']['size'], $settings['background_ypos_mobile']['unit'] );

				}

				$parallax_settings = array_merge(
					$parallax_settings,
					array(
						'speed'     => $speed,
						'android'   => 'yes' === $settings['premium_parallax_android_support'] ? 0 : 1,
						'ios'       => 'yes' === $settings['premium_parallax_ios_support'] ? 0 : 1,
						'size'      => $settings['background_size'],
						'position'  => $settings['background_position'],
						'positiont' => $positiont,
						'positionm' => $positionm,
						'repeat'    => $settings['background_repeat'],
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

				foreach ( $settings['premium_parallax_layers_list'] as $layer ) {

					$layer['alt'] = Control_Media::get_image_alt( $layer['premium_parallax_layer_image'] );

					array_push( $layers, $layer );

				}

				$parallax_settings = array_merge(
					$parallax_settings,
					array(
						'items'   => $layers,
						'devices' => $settings['premium_parallax_layers_devices'],
					)
				);

			}

			$element->add_render_attribute( '_wrapper', 'data-pa-parallax', wp_json_encode( $parallax_settings ) );

		}
	}
}
