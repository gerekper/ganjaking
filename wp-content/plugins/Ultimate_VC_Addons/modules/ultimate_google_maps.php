<?php
/**
 * Add-on Name: Ultimate Google Maps
 * Add-on URI: https://www.brainstormforce.com
 *
 *  @package Ultimate Google Maps
 */

if ( ! class_exists( 'Ultimate_Google_Maps' ) ) {
	/**
	 * Function that initializes Ultimate Google Maps Module
	 *
	 * @class Ultimate_Google_Maps
	 */
	class Ultimate_Google_Maps {
		/**
		 * Constructor function that constructs default values for the Ultimate Google Maps module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'google_maps_init' ) );
			}
			add_shortcode( 'ultimate_google_map', array( $this, 'display_ultimate_map' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ultimate_google_map_script' ), 1 );
		}
		/**
		 * Function that register styles and scripts for Ultimate Google Maps Module.
		 *
		 * @method ultimate_google_map_script
		 */
		public function ultimate_google_map_script() {
			$api     = 'https://maps.googleapis.com/maps/api/js';
			$map_key = bsf_get_option( 'map_key' );
			if ( false != $map_key ) {
				$arr_params = array(
					'key' => $map_key,
				);
				$api        = esc_url( add_query_arg( $arr_params, $api ) );
			}
			wp_register_script( 'googleapis', $api, null, null, false ); // PHPCS:ignore:WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}
		/**
		 * Function that initializes settings of Ultimate Google Maps Module.
		 *
		 * @method google_maps_init
		 */
		public function google_maps_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Google Map', 'ultimate_vc' ),
						'base'                    => 'ultimate_google_map',
						'class'                   => 'vc_google_map',
						'controls'                => 'full',
						'show_settings_on_create' => true,
						'icon'                    => 'vc_google_map',
						'description'             => __( 'Display Google Maps to indicate your location.', 'ultimate_vc' ),
						'category'                => 'Ultimate VC Addons',
						'params'                  => array(
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Width (in %)', 'ultimate_vc' ),
								'param_name'  => 'width',
								'admin_label' => true,
								'value'       => '100%',
								'group'       => 'General Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Height (in px)', 'ultimate_vc' ),
								'param_name'  => 'height',
								'admin_label' => true,
								'value'       => '300px',
								'group'       => 'General Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Map type', 'ultimate_vc' ),
								'param_name'  => 'map_type',
								'admin_label' => true,
								'value'       => array(
									__( 'Roadmap', 'ultimate_vc' )   => 'ROADMAP',
									__( 'Satellite', 'ultimate_vc' ) => 'SATELLITE',
									__( 'Hybrid', 'ultimate_vc' )    => 'HYBRID',
									__( 'Terrain', 'ultimate_vc' )   => 'TERRAIN',
								),
								'group'       => 'General Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Latitude', 'ultimate_vc' ),
								'param_name'  => 'lat',
								'admin_label' => true,
								'value'       => '18.591212',
								'description' => '<a href="http://universimmedia.pagesperso-orange.fr/geo/loc.htm" target="_blank" rel="noopener">' . __( 'Here is a tool', 'ultimate_vc' ) . '</a> ' . __( 'where you can find Latitude & Longitude of your location', 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Longitude', 'ultimate_vc' ),
								'param_name'  => 'lng',
								'admin_label' => true,
								'value'       => '73.741261',
								'description' => '<a href="http://universimmedia.pagesperso-orange.fr/geo/loc.htm" target="_blank" rel="noopener">' . __( 'Here is a tool', 'ultimate_vc' ) . '</a> ' . __( 'where you can find Latitude & Longitude of your location', 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Map Zoom', 'ultimate_vc' ),
								'param_name' => 'zoom',
								'value'      => array(
									__( '18 - Default', 'ultimate_vc' ) => 12,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9,
									10,
									11,
									13,
									14,
									15,
									16,
									17,
									18,
									19,
									20,
								),
								'group'      => 'General Settings',
							),
							array(
								'type'       => 'checkbox',
								'heading'    => '',
								'param_name' => 'scrollwheel',
								'value'      => array(
									__( 'Disable map zoom on mouse wheel scroll', 'ultimate_vc' ) => 'disable',
								),
								'group'      => 'General Settings',
							),
							array(
								'type'             => 'textarea_html',
								'class'            => '',
								'heading'          => __( 'Info Window Text', 'ultimate_vc' ),
								'param_name'       => 'content',
								'value'            => '',
								'group'            => 'Info Window',
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
							),
							array(
								'type'        => 'ult_switch',
								'heading'     => __( 'Open on Marker Click', 'ultimate_vc' ),
								'param_name'  => 'infowindow_open',
								'options'     => array(
									'infowindow_open_value' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'value'       => 'infowindow_open_value',
								'default_set' => true,
								'group'       => 'Info Window',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Marker/Point icon', 'ultimate_vc' ),
								'param_name' => 'marker_icon',
								'value'      => array(
									__( 'Use Google Default', 'ultimate_vc' ) => 'default',
									__( "Use Plugin's Default", 'ultimate_vc' ) => 'default_self',
									__( 'Upload Custom', 'ultimate_vc' ) => 'custom',
								),
								'group'      => 'Marker',
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'icon_img',
								'admin_label' => true,
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'marker_icon',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Marker',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Marker Animation', 'ultimate_vc' ),
								'param_name' => 'marker_animation',
								'value'      =>
									array(
										__( 'Yes', 'ultimate_vc' ) => 'yes',
										__( 'No', 'ultimate_vc' ) => 'no',
									),
								'group'      => 'Marker',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Street view control', 'ultimate_vc' ),
								'param_name' => 'streetviewcontrol',
								'value'      => array(
									__( 'Disable', 'ultimate_vc' ) => 'false',
									__( 'Enable', 'ultimate_vc' )  => 'true',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Map type control', 'ultimate_vc' ),
								'param_name' => 'maptypecontrol',
								'value'      => array(
									__( 'Disable', 'ultimate_vc' ) => 'false',
									__( 'Enable', 'ultimate_vc' )  => 'true',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Zoom control', 'ultimate_vc' ),
								'param_name' => 'zoomcontrol',
								'value'      => array(
									__( 'Disable', 'ultimate_vc' ) => 'false',
									__( 'Enable', 'ultimate_vc' )  => 'true',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Zoom Control Position', 'ultimate_vc' ),
								'param_name' => 'zoomcontrolposition',
								'value'      => array(
									__( 'Right Bottom', 'ultimate_vc' ) => 'RIGHT_BOTTOM',
									__( 'Right Top', 'ultimate_vc' ) => 'RIGHT_TOP',
									__( 'Right Center', 'ultimate_vc' ) => 'RIGHT_CENTER',
									__( 'Left Top', 'ultimate_vc' ) => 'LEFT_TOP',
									__( 'Left Center', 'ultimate_vc' ) => 'LEFT_CENTER',
									__( 'Left Bottom', 'ultimate_vc' ) => 'LEFT_BOTTOM',
								),
								'dependency' => array(
									'element' => 'zoomcontrol',
									'value'   => 'true',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Dragging on Mobile', 'ultimate_vc' ),
								'param_name' => 'dragging',
								'value'      => array(
									__( 'Enable', 'ultimate_vc' )  => 'true',
									__( 'Disable', 'ultimate_vc' ) => 'false',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Dragging on Desktop', 'ultimate_vc' ),
								'param_name' => 'dragging_desktop',
								'value'      => array(
									__( 'Enable', 'ultimate_vc' )  => 'true',
									__( 'Disable', 'ultimate_vc' ) => 'false',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Top margin', 'ultimate_vc' ),
								'param_name' => 'top_margin',
								'value'      => array(
									__( 'Page (small)', 'ultimate_vc' ) => 'page_margin_top',
									__( 'Section (large)', 'ultimate_vc' ) => 'page_margin_top_section',
									__( 'None', 'ultimate_vc' ) => 'none',
								),
								'group'      => 'General Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Map Width Override', 'ultimate_vc' ),
								'param_name'  => 'map_override',
								'value'       => array(
									'Default Width'      => '0',
									"Apply 1st parent element's width" => '1',
									"Apply 2nd parent element's width" => '2',
									"Apply 3rd parent element's width" => '3',
									"Apply 4th parent element's width" => '4',
									"Apply 5th parent element's width" => '5',
									"Apply 6th parent element's width" => '6',
									"Apply 7th parent element's width" => '7',
									"Apply 8th parent element's width" => '8',
									"Apply 9th parent element's width" => '9',
									'Full Width '        => 'full',
									'Maximum Full Width' => 'ex-full',
								),
								'description' => __( "By default, the map will be given to the WPBakery Page Builder row. However, in some cases depending on your theme's CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output..", 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'        => 'textarea_raw_html',
								'class'       => '',
								'heading'     => __( 'Google Styled Map JSON', 'ultimate_vc' ),
								'param_name'  => 'map_style',
								'value'       => '',
								'description' => "<a target='_blank' rel='noopener' href='http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html'>" . __( 'Click here', 'ultimate_vc' ) . '</a> ' . __( 'to get the style JSON code for styling your map.', 'ultimate_vc' ),
								'group'       => 'Styling',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/f57sh' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
								'group'            => 'General Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'MapBorder Style', 'ultimate_vc' ),
								'param_name'  => 'map_border_style',
								'value'       => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'description' => '',
								'group'       => 'Border',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'map_color_border',
								'value'       => '',
								'description' => '',
								'dependency'  => array(
									'element'   => 'map_border_style',
									'not_empty' => true,
								),
								'group'       => 'Border',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Width', 'ultimate_vc' ),
								'param_name'  => 'map_border_size',
								'value'       => 1,
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => '',
								'dependency'  => array(
									'element'   => 'map_border_style',
									'not_empty' => true,
								),
								'group'       => 'Border',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Radius', 'ultimate_vc' ),
								'param_name'  => 'map_radius',
								'value'       => 3,
								'min'         => 0,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => '',
								'dependency'  => array(
									'element'   => 'map_border_style',
									'not_empty' => true,
								),
								'group'       => 'Border',
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Map Margin ',
								'param_name'  => 'gmap_margin',
								'mode'        => 'margin',
								'unit'        => 'px',
								'positions'   => array(
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Styling', 'ultimate_vc' ),
								'description' => __( 'Add spacing from outside to the map.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Map padding ',
								'param_name'  => 'gmap_padding',
								'mode'        => 'padding',
								'unit'        => 'px',
								'positions'   => array(
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Styling', 'ultimate_vc' ),
								'description' => __( 'Add spacing from outside to the map.', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Ultimate Google Maps Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function display_ultimate_map( $atts, $content = null ) {
			$output                  = '';
				$ult_google_settings = shortcode_atts(
					array(
						'width'               => '100%',
						'height'              => '300px',
						'map_type'            => 'ROADMAP',
						'lat'                 => '18.591212',
						'lng'                 => '73.741261',
						'zoom'                => '14',
						'scrollwheel'         => '',
						'streetviewcontrol'   => 'false',
						'maptypecontrol'      => 'false',
						'pancontrol'          => 'false',
						'zoomcontrol'         => 'false',
						'zoomcontrolposition' => 'RIGHT_BOTTOM',
						'dragging'            => 'true',
						'dragging_desktop'    => 'true',
						'marker_icon'         => 'default',
						'icon_img'            => '',
						'top_margin'          => 'page_margin_top',
						'map_override'        => '0',
						'map_style'           => '',
						'el_class'            => '',
						'infowindow_open'     => 'infowindow_open_value',
						'map_vc_template'     => '',
						'map_border_style'    => '',
						'map_color_border'    => '',
						'map_border_size'     => '',
						'map_radius'          => '',
						'gmap_margin'         => '',
						'gmap_padding'        => '',
						'marker_animation'    => 'yes',
					),
					$atts
				);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$border_css       = '';
			$gmap_design_css  = '';
			$gmap_design_css  = $ult_google_settings['gmap_margin'];
			$gmap_design_css .= $ult_google_settings['gmap_padding'];
			$marker_lat       = $ult_google_settings['lat'];
			$marker_lng       = $ult_google_settings['lng'];
			if ( 'default_self' == $ult_google_settings['marker_icon'] ) {
				$icon_url = UAVC_URL . 'assets/img/icon-marker-pink.png';
			} elseif ( 'default' == $ult_google_settings['marker_icon'] ) {
				$icon_url = '';
			} else {
				$icon_url = apply_filters( 'ult_get_img_single', $ult_google_settings['icon_img'], 'url' );
			}
			$id                              = 'map_' . uniqid();
			$wrap_id                         = 'wrap_' . $id;
			$ult_google_settings['map_type'] = strtoupper( $ult_google_settings['map_type'] );
			$ult_google_settings['width']    = ( substr( $ult_google_settings['width'], -1 ) != '%' && substr( $ult_google_settings['width'], -2 ) != 'px' ? $ult_google_settings['width'] . 'px' : $ult_google_settings['width'] );
			$map_height                      = ( substr( $ult_google_settings['height'], -1 ) != '%' && substr( $ult_google_settings['height'], -2 ) != 'px' ? $ult_google_settings['height'] . 'px' : $ult_google_settings['height'] );

			$margin_css = '';
			if ( 'none' != $ult_google_settings['top_margin'] ) {
				$margin_css = $ult_google_settings['top_margin'];
			}

			if ( '' != $ult_google_settings['map_border_style'] ) {
				$border_css .= 'border-style:' . $ult_google_settings['map_border_style'] . ';';
			}
			if ( '' != $ult_google_settings['map_color_border'] ) {
				$border_css .= 'border-color:' . $ult_google_settings['map_color_border'] . ';';
			}
			if ( '' != $ult_google_settings['map_border_size'] ) {
				$border_css .= 'border-width:' . $ult_google_settings['map_border_size'] . 'px;';
			}
			if ( '' != $ult_google_settings['map_radius'] ) {
				$border_css .= 'border-radius:' . $ult_google_settings['map_radius'] . 'px;';
			}
			if ( 'map_vc_template_value' == $ult_google_settings['map_vc_template'] ) {
				$ult_google_settings['el_class'] .= 'uvc-boxed-layout';
			}

			$output .= "<div id='" . esc_attr( $wrap_id ) . "' class='ultimate-map-wrapper " . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $ult_google_settings['el_class'] ) . "' style='" . esc_attr( $gmap_design_css ) . ' ' . ( '' != $map_height ? 'height:' . $map_height . ';' : '' ) . "'><div id='" . esc_attr( $id ) . "' data-map_override='" . esc_attr( $ult_google_settings['map_override'] ) . "' class='ultimate_google_map wpb_content_element " . esc_attr( $margin_css ) . "'" . ( '' != $ult_google_settings['width'] || '' != $map_height ? " style='" . esc_attr( $border_css ) . ( '' != $ult_google_settings['width'] ? 'width:' . esc_attr( $ult_google_settings['width'] ) . ';' : '' ) . ( '' != $map_height ? 'height:' . esc_attr( $map_height ) . ';' : '' ) . "'" : '' ) . '></div></div>';

			if ( 'disable' == $ult_google_settings['scrollwheel'] ) {
				$ult_google_settings['scrollwheel'] = 'false';
			} else {
				$ult_google_settings['scrollwheel'] = 'true';
			}

			$output .= "<script type='text/javascript'>
			(function($) {
  			'use strict';
			var map_$id = null;
			var coordinate_$id;
			var isDraggable = $(document).width() > 641 ? " . $ult_google_settings['dragging_desktop'] . ' : ' . $ult_google_settings['dragging'] . ";
			try
			{
				var map_$id = null;
				var coordinate_$id;
				coordinate_$id=new google.maps.LatLng(" . $ult_google_settings['lat'] . ', ' . $ult_google_settings['lng'] . ');
				var mapOptions=
				{
					zoom: ' . $ult_google_settings['zoom'] . ",
					center: coordinate_$id,
					scaleControl: true,
					streetViewControl: " . $ult_google_settings['streetviewcontrol'] . ',
					mapTypeControl: ' . $ult_google_settings['maptypecontrol'] . ',
					panControl: ' . $ult_google_settings['pancontrol'] . ',
					zoomControl: ' . $ult_google_settings['zoomcontrol'] . ',
					scrollwheel: ' . $ult_google_settings['scrollwheel'] . ',
					draggable: isDraggable,
					zoomControlOptions: {
						position: google.maps.ControlPosition.' . $ult_google_settings['zoomcontrolposition'] . '
					},';
			if ( '' == $ult_google_settings['map_style'] ) {
				$output .= 'mapTypeId: google.maps.MapTypeId.' . $ult_google_settings['map_type'] . ',';
			} else {
				$output .= ' mapTypeControlOptions: {
					  		mapTypeIds: [google.maps.MapTypeId.' . $ult_google_settings['map_type'] . ", 'map_style']
						}";
			}
				$output .= '};';
			if ( '' !== $ult_google_settings['map_style'] ) {
				$output .= 'var styles = ' . rawurldecode( base64_decode( wp_strip_all_tags( $ult_google_settings['map_style'] ) ) ) . '; var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});'; //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			}
				$output .= "var map_$id = new google.maps.Map(document.getElementById('$id'),mapOptions);";
			if ( '' !== $ult_google_settings['map_style'] ) {
				$output .= "map_$id.mapTypes.set('map_style', styledMap);
 							 map_$id.setMapTypeId('map_style');";
			}
			if ( '' != $marker_lat && '' != $marker_lng ) {
				$output .= "
						var x = '" . esc_attr( $ult_google_settings['infowindow_open'] ) . "';
						var marker_$id = new google.maps.Marker({
						position: new google.maps.LatLng($marker_lat, $marker_lng),
						animation:  google.maps.Animation.DROP,
						map: map_$id,
						icon: '" . esc_url( $icon_url ) . "'
					});";
				if ( 'yes' == $ult_google_settings['marker_animation'] ) {
					$output .= "	google.maps.event.addListener(marker_$id, 'click', toggleBounce);"; }

				if ( '' !== trim( $content ) ) {
					$output .= "var infowindow = new google.maps.InfoWindow();
							infowindow.setContent('<div class=\"map_info_text\" style=\'color:#000;\'>" . trim( preg_replace( '/\s+/', ' ', do_shortcode( $content ) ) ) . "</div>');";

					if ( 'off' == $ult_google_settings['infowindow_open'] ) {
						$output .= "infowindow.open(map_$id,marker_$id);";
					}

						$output .= "google.maps.event.addListener(marker_$id, 'click', function() {
								infowindow.open(map_$id,marker_$id);
						  	});";

				}
			}
				$output .= "}
			catch(e){};
			jQuery(document).ready(function($){
				google.maps.event.trigger(map_$id, 'resize');
				$(window).resize(function(){
					google.maps.event.trigger(map_$id, 'resize');
					if(map_$id!=null)
						map_$id.setCenter(coordinate_$id);
				});
				$('.ui-tabs').bind('tabsactivate', function(event, ui) {
				   if($(this).find('.ultimate-map-wrapper').length > 0)
					{
						setTimeout(function(){
							$(window).trigger('resize');
						},200);
					}
				});
				$('.ui-accordion').bind('accordionactivate', function(event, ui) {
				   if($(this).find('.ultimate-map-wrapper').length > 0)
					{
						setTimeout(function(){
							$(window).trigger('resize');
						},200);
					}
				});
				$(window).load(function(){
					setTimeout(function(){
						$(window).trigger('resize');
					},200);
				});
				$('.ult_exp_section').select(function(){
					if($(map_$id).parents('.ult_exp_section'))
					{
						setTimeout(function(){
							$(window).trigger('resize');
						},200);
					}
				});
				$(document).on('onUVCModalPopupOpen', function(){
					if($(map_$id).parents('.ult_modal-content'))
					{
						setTimeout(function(){
							$(window).trigger('resize');
						},200);
					}
				});
				$(document).on('click','.ult_tab_li',function(){
					$(window).trigger('resize');
					setTimeout(function(){
						$(window).trigger('resize');
					},200);
				});
			});
			function toggleBounce() {
			  if (marker_$id.getAnimation() != null) {
				marker_$id.setAnimation(null);
			  } else {
				marker_$id.setAnimation(google.maps.Animation.BOUNCE);
			  }
			}
			})(jQuery);
			</script>";
			$is_preset   = false; // Retrieve preset Code.
			if ( isset( $_GET['preset'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
			if ( $is_preset ) {
				$text = 'array ( ';
				foreach ( $atts as $key => $att ) {
					$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
				}
				if ( '' != $content ) {
					$text .= '<br/>	\'content\' => \'' . $content . '\',';
				}
				$text   .= '<br/>)';
				$output .= '<pre>';
				$output .= $text;
				$output .= '</pre>'; // remove backslash once copied.
			}
			return $output;
		}
	}
	new Ultimate_Google_Maps();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Google_Map' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ultimate_Google_Map extends WPBakeryShortCode {
		}
	}
}
