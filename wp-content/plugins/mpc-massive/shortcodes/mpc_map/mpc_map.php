<?php
/*----------------------------------------------------------------------------*\
	MAP SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Map' ) ) {
	class MPC_Map {
		public $shortcode = 'mpc_map';
		public $panel_section = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_map', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			add_action( 'wp_ajax_nopriv_mpc_get_location', array( $this, 'get_map_location' ) );
			add_action( 'vc_frontend_editor_enqueue_js_css', array( $this, 'mpc_frontend_enqueue' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function mpc_frontend_enqueue() {
			global $mpc_ma_options;

			if ( $mpc_ma_options[ 'google_api' ] != '' ) {
				$key = '?key=' . $mpc_ma_options[ 'google_api' ];
			} else {
				$key = '';
			}

			wp_enqueue_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js' . $key, array(), 'v3' );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_map-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_map/css/mpc_map.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_map-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_map/js/mpc_map' . MPC_MASSIVE_MIN . '.js', array( 'jquery', 'google-maps-api' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			$this->mpc_frontend_enqueue();

			global $MPC_Shortcode, $mpc_ma_options, $mpc_frontend;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$MPC_Shortcode[ 'map' ][ 'markers' ] = array();

			$atts = shortcode_atts( array(
				'class'                   => '',
				'preset'                  => '',
				'disable_auto_zoom'     => '',
				'zoom'                  => '8',
				'disable_auto_location' => '',
				'location'              => '',
				'disable_ui'            => '',
				'disable_scroll_wheel'  => '',
				'style'                 => 'default',
				'height'                => '',
				'custom_style'          => '',
				'content'               => '',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $atts );
			$classes   = $animation != '' ? ' mpc-animation' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes   .= $atts[ 'height' ] != '' ? ' mpc-custom-height' : '';

			$map_options = array(
				'disable_auto_zoom'     => $atts[ 'disable_auto_zoom' ] != '',
				'zoom'                  => $atts[ 'zoom' ],
				'disable_auto_location' => $atts[ 'disable_auto_location' ] != '',
				'location'              => $atts[ 'disable_auto_location' ] != '' ? $this->get_map_location( $atts[ 'location' ] ) : '',
				'disable_ui'            => $atts[ 'disable_ui' ] != '',
				'disable_scroll_wheel'  => $atts[ 'disable_scroll_wheel' ] != '',
				'style'                 => $atts[ 'style' ],
			);

			if ( $atts[ 'style' ] == 'custom' && $atts[ 'custom_style' ] != '' ) {
				$map_options[ 'custom_style' ] = rawurldecode( base64_decode( strip_tags( $atts[ 'custom_style' ] ) ) );
			}

			$markers = do_shortcode( $content );

			foreach( $MPC_Shortcode[ 'map' ][ 'markers' ] as $key => $values ) {
				$MPC_Shortcode[ 'map' ][ 'markers' ][ $key ][ 'location' ] = $this->get_map_location( $values[ 'location' ] );
			}

			$map_options[ 'markers' ] = $MPC_Shortcode[ 'map' ][ 'markers' ];

			$return = '<div data-id="' . $css_id . '" class="mpc-map-wrap' . $classes . '" ' . $animation . '>';
				if ( isset( $markers ) && $mpc_frontend ) {
					$return .= '<div class="mpc-markers"><span class="mpc-marker-title"><img src="' . mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-google-maps.png' . '" />' . __( 'Map Markers' ) . '</span>' . $markers . '</div>';
				}
				$return .= '<p class="mpc-error">' . __( 'If you see this Google Maps wasn\'t properly loaded. Please refresh your page :)', 'mpc' ) . '</p>';
				$return .= '<div class="mpc-map mpc-init " data-map-options="' . htmlentities( json_encode( $map_options ), ENT_QUOTES, 'UTF-8' ) . '"></div>';
			$return .= '</div>';

			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Map', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_map-' . rand( 1, 100 ) );
			$style = '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $styles[ 'height' ] ) { $inner_styles[] = 'height:' . $styles[ 'height' ] . 'px;'; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-map-wrap[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'             => 'checkbox',
					'heading'          => 'Auto Zoom',
					'param_name'       => 'disable_auto_zoom',
					'tooltip'          => __( 'Check to disable auto zoom. Auto zoom will set the zoom level to display all specified locations on the map.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => 'Scroll Wheel Zoom',
					'param_name'       => 'disable_scroll_wheel',
					'tooltip'          => __( 'Check to disable zoom on mouse wheel scroll.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-first-row',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => 'Map Interface',
					'param_name'       => 'disable_ui',
					'tooltip'          => __( 'Check to disable map interface (<em>zoom slider</em> and <em>positions arrows</em>).', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-first-row',
				),
				array(
					'type'        => 'mpc_slider',
					'heading'     => __( 'Zoom Level', 'mpc' ),
					'param_name'  => 'zoom',
					'tooltip'     => __( 'Choose zoom level for the map. Zoom level goes from <b>0</b> (World view) to <b>21</b> (street view).', 'mpc' ),
					'value'       => 8,
					'max'         => 21,
					'unit'        => '',
					'dependency'  => array(
						'element' => 'disable_auto_zoom',
						'value'   => 'true',
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => 'Auto Center Location',
					'param_name'       => 'disable_auto_location',
					'tooltip'          => __( 'Check to disable auto position. Auto position will set the position to center for all specified locations.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Center Location', 'mpc' ),
					'param_name'       => 'location',
					'admin_label'      => true,
					'tooltip'          => __( 'Define location for the map center point. Please provide point coordinates (e.g. <em>40.781415, -73.966643</em>) or a full address (e.g. <em>Mountain View, CA, USA</em>). The full address will use addtional Google Map API call for geocode service.', 'mpc' ),
					'description'      => __( 'GPS location or address can be converted into Google Map coordinates at <a href="https://www.gps-coordinates.net/" target="_blank">https://www.gps-coordinates.net/</a>.', 'mpc' ),
					'value'            => '',
					'dependency'  => array(
						'element' => 'disable_auto_location',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'style',
					'tooltip'          => __( 'Select map style. You can preview styles on <a href="http://massive.mpcthemes.net/google-maps/" target="_blank">our preview</a>.', 'mpc' ),
					'value'            => array(
						__( 'Default', 'mpc' )        => 'default',
						__( 'Apple Maps', 'mpc' )     => 'apple_maps',
						__( 'Blue Essence', 'mpc' )   => 'blue_essence',
						__( 'Blue Water', 'mpc' )     => 'blue_water',
						__( 'Cool Grey', 'mpc' )      => 'cool_grey',
						__( 'Shades of Grey', 'mpc' ) => 'shades_of_grey',
						__( 'Custom', 'mpc' )         => 'custom',
					),
					'std'              => 'default',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Height', 'mpc' ),
					'param_name'       => 'height',
					'tooltip'          => __( 'Specify the height for map in pixels. Leave empty to use default.', 'mpc' ),
					'value'            => '',
					'label'            => 'px',
					'addon'            => array(
						'icon'  => 'dashicons-editor-expand',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'        => 'textarea_raw_html',
					'heading'     => __( 'Custom Style', 'mpc' ),
					'param_name'  => 'custom_style',
					'tooltip'     => __( 'Define custom map style. Great examples can be found <a href="https://snazzymaps.com/" target="_blank">here</a>.', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'style', 'value' => 'custom' ),
				),
			);

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$animation  = MPC_Snippets::vc_animation();
			$class      = MPC_Snippets::vc_class();

			$params = array_merge( $base, $background, $border, $padding, $margin, $animation, $class );

			return array(
				'name'            => __( 'Map', 'mpc' ),
				'description'     => __( 'Google map with styles', 'mpc' ),
				'base'            => 'mpc_map',
				'class'           => '',
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-google-maps.png',
				'icon'            => 'mpc-shicon-map',
				'category'        => __( 'Massive', 'mpc' ),
				'as_parent'       => array( 'only' => 'mpc_marker' ),
				'content_element' => true,
				'js_view'         => 'VcColumnView',
				'params'          => $params
			);
		}

		/* Get map location */
		function get_map_location( $location ) {
			global $mpc_ma_options;

			if ( $location == '' ) {
				return '';
			}

			$pattern = '/^([-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)),\s*([-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?))$/';

			$matches = array();
			preg_match( $pattern, $location, $matches );

			if ( isset( $matches[ 1 ] ) && isset( $matches[ 5 ] ) ) {
				return array(
					'latitude'  => $matches[ 1 ],
					'longitude' => $matches[ 5 ],
				);
			}

			if ( $mpc_ma_options[ 'google_api' ] != '' ) {
				$key = '?key=' . $mpc_ma_options[ 'google_api' ];
			} else {
				$key = '';
			}

			$mpc_map_locations = get_option( 'mpc_map_locations' );

			$location_md5 = md5( $location );

			if ( is_array( $mpc_map_locations ) && isset( $mpc_map_locations[ $location_md5 ] ) ) {

				return $mpc_map_locations[ $location_md5 ];
			} else {
				$response = wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $location ) . $key );

				if ( 'OK' !== wp_remote_retrieve_response_message( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					return 'Could not connect with Google Map API';
				}

				$location_json = wp_remote_retrieve_body( $response );
				$location_data = json_decode( $location_json );

				if ( isset( $location_data->results[0]->geometry->location ) ) {
					$latitude  = $location_data->results[0]->geometry->location->lat;
					$longitude = $location_data->results[0]->geometry->location->lng;

					if ( ! is_array( $mpc_map_locations ) )
						$mpc_map_locations = array();

					$mpc_map_locations[ $location_md5 ] = array(
						'latitude'  => $latitude,
						'longitude' => $longitude,
					);

					update_option( 'mpc_map_locations', $mpc_map_locations );

					return $mpc_map_locations[ $location_md5 ];
				} else {
					return '';
				}
			}
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_map' ) ) {
	class WPBakeryShortCode_mpc_map extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'MPC_Map' ) ) {
	global $MPC_Map;
	$MPC_Map = new MPC_Map;
}
