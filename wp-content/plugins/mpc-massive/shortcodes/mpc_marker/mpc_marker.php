<?php
/*----------------------------------------------------------------------------*\
	MARKER SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Marker' ) ) {
	class MPC_Marker {
		public $shortcode = 'mpc_marker';
		public $panel_section = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_marker', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_marker-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_marker/css/mpc_marker.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_marker-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_marker/js/mpc_marker' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Shortcode, $MPC_Map, $mpc_ma_options, $mpc_frontend;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'location' => '',
				'icon'     => '',
			), $atts );

			$icon_url = '';
			if ( ! empty( $atts[ 'icon' ] ) ) {
				$icon_url = wp_get_attachment_url( $atts[ 'icon' ] );
			}

			if ( ! $icon_url ) {
				$icon_url = mpc_get_plugin_path( __FILE__ ) . '/assets/images/defaults/location.png';
			}

			$marker_options = array(
				'location' => $atts[ 'location' ],
				'icon_url' => $icon_url,
			);

			$MPC_Shortcode[ 'map' ][ 'markers' ][] = $marker_options;

			if ( $mpc_frontend ) {
				$marker_options[ 'location' ] = $MPC_Map->get_map_location( $marker_options[ 'location' ] );

				$marker = '<div class="mpc-marker" data-marker-options="' . htmlentities( json_encode( $marker_options ), ENT_QUOTES, 'UTF-8' ) . '">';
					$marker .= '<img class="mpc-marker__icon" src="' . $icon_url . '" />';
					$marker .= '<span>(' . $atts[ 'location' ] . ')</span>';
				$marker .= '</div>';

				return $marker;
			}
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			return array(
				'name'        => __( 'Marker', 'mpc' ),
				'description' => __( 'Custom map marker', 'mpc' ),
				'base'        => 'mpc_marker',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-google-maps.png',
				'icon'        => 'mpc-shicon-marker',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => 'mpc_map' ),
				'params'      => array(
					array(
						'type'             => 'attach_image',
						'heading'          => __( 'Icon', 'mpc' ),
						'param_name'       => 'icon',
						'holder'           => 'img',
						'tooltip'          => __( 'Choose marker image. Marker will display this image on the map in specified location.', 'mpc' ),
						'value'            => '',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
					),
					array(
						'type'             => 'textfield',
						'heading'          => __( 'Location', 'mpc' ),
						'param_name'       => 'location',
						'admin_label'      => true,
						'tooltip'          => __( 'Define location for the map center point. Please provide point coordinates (e.g. <em>40.781415, -73.966643</em>) or a full address (e.g. <em>Mountain View, CA, USA</em>). The full address will use addtional Google Map API call for geocode service.', 'mpc' ),
						'description'      => __( 'GPS location or address can be converted into Google Map coordinates at <a href="https://www.gps-coordinates.net/" target="_blank">https://www.gps-coordinates.net/</a>.', 'mpc' ),
						'value'            => '',
						'edit_field_class' => 'vc_col-sm-8 vc_column mpc-first-row',
					),
				),
			);
		}
	}
}
if ( class_exists( 'MPC_Marker' ) ) {
	global $MPC_Marker;
	$MPC_Marker = new MPC_Marker;
}
if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_marker' ) ) {
	class WPBakeryShortCode_mpc_marker extends MPCShortCode_Base {}
}
