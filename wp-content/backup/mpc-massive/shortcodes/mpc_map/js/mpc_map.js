/*----------------------------------------------------------------------------*\
	MAP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $maps = $( '.mpc-map' ),
		_styles = {
		    blue_water: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}],

			apple_maps: [{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}],

			blue_essence: [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}],

			cool_grey: [{"featureType":"landscape","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"stylers":[{"hue":"#00aaff"},{"saturation":-100},{"gamma":2.15},{"lightness":12}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"lightness":24}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":57}]}],

			shades_of_grey: [{featureType:"all",elementType:"labels.text.fill",stylers:[{saturation:36},{color:"#000000"},{lightness:"56"}]},{featureType:"all",elementType:"labels.text.stroke",stylers:[{visibility:"on"},{color:"#000000"},{lightness:16}]},{featureType:"all",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"administrative",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:"30"}]},{featureType:"administrative",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:"17"},{weight:1.2}]},{featureType:"landscape",elementType:"geometry",stylers:[{color:"#000000"},{lightness:"26"}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#000000"},{lightness:21}]},{featureType:"road.highway",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:17}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:29},{weight:.2}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#000000"},{lightness:18}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#000000"},{lightness:16}]},{featureType:"transit",elementType:"geometry",stylers:[{color:"#000000"},{lightness:19}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#000000"},{lightness:17}]}]
	    };

	function add_map_marker( _marker_options, _map ) {
		var _marker = new google.maps.Marker( {
			position: _marker_options.location,
			map:      _map,
			icon:     _marker_options.icon_url
		} );
	}

	function init_shortcode( $maps ) {
		if ( typeof google == 'undefined' ) {
			$maps
				.addClass( 'mpc-empty' )
				.find( '.mpc-error' )
				.show();

			return;
		}

		$maps.each( function() {
			var $map         = $( this ),
				_map_options = $map.data( 'map-options' ),
				_defaults    = {
					'disable_auto_zoom':     false,
					'zoom':                  '',
					'disable_auto_location': false,
					'location':              '',
					'disable_ui':            false,
					'disable_scroll_wheel':  false,
					'style':                 'default',
					'markers':               []
				};

			if ( typeof _map_options == 'undefined' || typeof _map_options.markers == 'undefined' ) {
				return;
			}

			$.extend( _defaults, _map_options );

			var _map_config = {
					disableDefaultUI: _map_options.disable_ui,
					scrollwheel:      !_map_options.disable_scroll_wheel
				},
				_index,
				_map,
				_bounds,
				_loaded;

			if ( _map_options.style == 'custom' && typeof _map_options.custom_style != 'undefined' ) {
				try {
					_map_options.custom_style = JSON.parse( _map_options.custom_style );

					_map_config.styles = _map_options.custom_style;
				} catch (e) {
					// Parsing failed
				}
			} else if ( _map_options.style != 'default' && typeof _styles[ _map_options.style ] != 'undefined' ) {
				_map_config.styles = _styles[ _map_options.style ];
			}

			if ( _map_options.disable_auto_location && _map_options.location != '' ) {
				_map_config.center = new google.maps.LatLng( _map_options.location.latitude, _map_options.location.longitude );
			}

			if ( _map_options.disable_auto_zoom && _map_options.zoom != '' ) {
				_map_config.zoom = parseInt( _map_options.zoom );
			}

			_map = new google.maps.Map( $map[ 0 ], _map_config );

			_bounds = new google.maps.LatLngBounds();

			for( _index in _map_options.markers ) {
				if ( _map_options.markers[ _index ].location != '' ) {
					_map_options.markers[ _index ].location = new google.maps.LatLng( _map_options.markers[ _index ].location.latitude, _map_options.markers[ _index ].location.longitude );

					_bounds.extend( _map_options.markers[ _index ].location );

					add_map_marker( _map_options.markers[ _index ], _map );
				}
			}

			if ( ! _map_options.disable_auto_location ) {
				_loaded = google.maps.event.addListener( _map, 'idle', function() {
					_map.setCenter( _bounds.getCenter() );

					google.maps.event.removeListener( _loaded );
				} );
			}

			if ( ! _map_options.disable_auto_zoom ) {
				_map.fitBounds( _bounds );
			}
		} );

		$maps.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_map = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				this.listenTo( this.model, 'mpc:forceRender', this.rendered );

				window.InlineShortcodeView_mpc_map.__super__.initialize.call( this, params );
			},
			rendered: function() {
				var _self = this,
					$map = this.$el.find( '.mpc-map' );

				$map.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.inited', [ $map ] );

				var _options = $map.data( 'map-options' ),
					_markers = [];

				setTimeout( function() {
					_self.$el.find( '.mpc-markers .mpc-marker' ).each( function() {
						_markers.push( $( this ).data( 'marker-options' ) );
					} );

					if ( _markers.length ) {
						_options.markers = _markers;
						$map.data( 'map-options', _options )
					}

					init_shortcode( $map );

					$map.closest( '.vc_element' ).find( '.mpc-marker-title' ).first().siblings( '.mpc-marker-title' ).remove();
				}, 250 );

				window.InlineShortcodeView_mpc_map.__super__.rendered.call( this );
			}
		} );
	}

	if ( window.vc_mode != 'admin_frontend_editor' ) {
		_mpc_vars.$window.on( 'load', function () {
			init_shortcode( $maps );
		} );
	}
} )( jQuery );
