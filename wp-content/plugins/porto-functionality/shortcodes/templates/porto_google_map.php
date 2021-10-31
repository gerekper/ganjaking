<?php

$width = $height = $map_type = $lat = $lng = $zoom = $streetviewcontrol = $maptypecontrol = $top_margin = $pancontrol = $zoomcontrol = $zoomcontrolposition = $dragging = $marker_icon = $icon_img = $map_override = $output = $map_style = $scrollwheel = $el_class = '';

extract(
	shortcode_atts(
		array(
			//"id" => "map",
			'width'               => '100%',
			'height'              => '300px',
			'map_type'            => 'ROADMAP',
			'lat'                 => '51.5074',
			'lng'                 => '0.1278',
			'zoom'                => '14',
			'scrollwheel'         => '',
			'streetviewcontrol'   => 'false',
			'maptypecontrol'      => 'false',
			'pancontrol'          => 'false',
			'zoomcontrol'         => 'false',
			'zoomcontrolposition' => 'RIGHT_BOTTOM',
			'dragging'            => 'true',
			'marker_icon'         => 'default',
			'icon_img'            => '',
			'top_margin'          => 'page_margin_top',
			'map_override'        => '0',
			'map_style'           => '',
			'el_class'            => '',
			'infowindow_open'     => 'on',
			'className'           => '',
		),
		$atts
	)
);

if ( ( ! isset( $content ) || empty( $content ) ) && isset( $atts['content'] ) && ! empty( $atts['content'] ) ) {
	$content = $atts['content'];
}

if ( empty( $zoomcontrolposition ) ) {
	$zoomcontrolposition = 'RIGHT_BOTTOM';
}

wp_enqueue_script( 'googleapis' );
wp_enqueue_script( 'porto_shortcodes_map_loader_js' );

$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'porto-adjust-bottom-margin' : '';

if ( $className ) {
	if ( $el_class ) {
		$el_class .= ' ' . $className;
	} else {
		$el_class = $className;
	}
}

$marker_lat = $lat;
$marker_lng = $lng;
$icon_url   = '';
if ( 'default' == $marker_icon ) {
	$icon_url = '';
} elseif ( $icon_img ) {
	$attachment = wp_get_attachment_image_src( $icon_img, 'full' );
	if ( isset( $attachment ) ) {
		$icon_url = $attachment[0];
	}
}
$id         = 'map_' . uniqid();
$wrap_id    = 'wrap_' . $id;
$map_type   = strtoupper( $map_type );
$width      = ( substr( $width, -1 ) != '%' && substr( $width, -2 ) != 'px' ? $width . 'px' : $width );
if ( $height ) {
	$map_height = ( substr( $height, -1 ) != '%' && substr( $height, -2 ) != 'px' ? $height . 'px' : $height );
} else {
	$map_height = '';
}

$margin_css = '';
if ( 'none' != $top_margin ) {
	$margin_css = $top_margin;
}

$output .= "<div id='" . esc_attr( $wrap_id ) . "' class='porto-map-wrapper " . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $el_class ) . "' style='" . ( $map_height ? 'height:' . esc_attr( $map_height ) . ';' : '' ) . "'><div id='" . esc_attr( $id ) . "' data-map_override='" . esc_attr( $map_override ) . "' class='porto_google_map wpb_content_element " . esc_attr( $margin_css ) . "'" . ( $width || $map_height ? " style='" . ( $width ? 'width:' . esc_attr( $width ) . ';' : '' ) . ( $map_height ? 'height:' . esc_attr( $map_height ) . ';' : '' ) . "'" : '' ) . '></div></div>';

if ( $scrollwheel ) {
	$scrollwheel = 'false';
} else {
	$scrollwheel = 'true';
}

$output .= "<script>
(function($) {
	'use strict';
	jQuery(document).ready(function($) {
		if (typeof google == 'undefined') {
			return;
		}
		var map_$id = null;
		var coordinate_$id;
		try {
			coordinate_$id=new google.maps.LatLng($lat, $lng);
			var isDraggable = $(document).width() > 640 ? true : $dragging;
			var mapOptions = {
				zoom: $zoom,
				center: coordinate_$id,
				scaleControl: true,
				streetViewControl: $streetviewcontrol,
				mapTypeControl: $maptypecontrol,
				panControl: $pancontrol,
				zoomControl: $zoomcontrol,
				scrollwheel: $scrollwheel,
				draggable: isDraggable,
				zoomControlOptions: {
					position: google.maps.ControlPosition.$zoomcontrolposition
				},";
if ( '' == $map_style ) {
	$output .= "mapTypeId: google.maps.MapTypeId.$map_type,";
} else {
	$output .= " mapTypeControlOptions: {
						mapTypeIds: [google.maps.MapTypeId.$map_type, 'map_style']
					}";
}
			$output .= '};';
if ( $map_style ) {
	$map_style         = strip_tags( $map_style );
	$map_style_escaped = base64_decode( $map_style, true );
	if ( ! $map_style_escaped ) {
		$map_style_escaped = $map_style;
	} else {
		$map_style_escaped = rawurldecode( $map_style_escaped );
	}
	$output   .= 'var styles = ' . $map_style_escaped . ';
					var styledMap = new google.maps.StyledMapType(styles,
						{name: "Styled Map"});';
}
			$output .= "var map_$id = new google.maps.Map(document.getElementById('$id'),mapOptions);";
if ( $map_style ) {
	$output .= "map_$id.mapTypes.set('map_style', styledMap);
						 map_$id.setMapTypeId('map_style');";
}
if ( $marker_lat && $marker_lng ) {
	$output .= "
					var x = '" . esc_js( $infowindow_open ) . "';
					var marker_$id = new google.maps.Marker({
					position: new google.maps.LatLng($marker_lat, $marker_lng),
					animation:  google.maps.Animation.DROP,
					map: map_$id,
					icon: '" . esc_url( $icon_url ) . "'
				});
				google.maps.event.addListener(marker_$id, 'click', toggleBounce);";

	if ( trim( $content ) !== '' ) {
		$output .= "var infowindow = new google.maps.InfoWindow();
						infowindow.setContent('<div class=\"map_info_text\" style=\'color:#000;\'>" . trim( preg_replace( '/\s+/', ' ', do_shortcode( $content ) ) ) . "</div>');";

		if ( 'off' == $infowindow_open ) {
			$output .= "infowindow.open(map_$id,marker_$id);";
		}

			$output .= "google.maps.event.addListener(marker_$id, 'click', function() {
							infowindow.open(map_$id,marker_$id);
						});";

	}
}
		$output .= "}
		catch(e){};
		google.maps.event.trigger(map_$id, 'resize');
		$(window).on('resize', function(){
			google.maps.event.trigger(map_$id, 'resize');
			if(map_$id!=null) {
				map_$id.setCenter(coordinate_$id);
			}
		});
		$('.ui-tabs').on('tabsactivate', function(event, ui) {
		   if($(this).find('.porto-map-wrapper').length > 0)
			{
				setTimeout(function(){
					$(window).trigger('resize');
				},200);
			}
		});
		$('.ui-accordion').on('accordionactivate', function(event, ui) {
			if($(this).find('.porto-map-wrapper').length > 0) {
				setTimeout(function(){
					$(window).trigger('resize');
				},200);
			}
		});
		$(document).on('onPortoModalPopupOpen', function(){
			if($(map_$id).parents('.porto_modal-content')) {
				setTimeout(function(){
					$(window).trigger('resize');
				},200);
			}
		});
		function toggleBounce() {
			if (marker_$id.getAnimation() != null) {
				marker_$id.setAnimation(null);
			} else {
				marker_$id.setAnimation(google.maps.Animation.BOUNCE);
			}
		}
	});

	$(window).on('load', function() {
		setTimeout(function() {
			$(window).trigger('resize');
		},200);
	});
})(jQuery);
</script>";

echo porto_filter_output( $output );
