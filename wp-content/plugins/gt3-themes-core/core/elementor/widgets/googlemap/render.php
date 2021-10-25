<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_GoogleMap $widget */

$settings = array(
	'custom_coordinates' => '',
	'custom_latitude' => '',
	'custom_longitude' => '',
	'custom_map_marker_info' => 'default',
	'custom_marker_info' => '',
	'custom_marker_info_street_number' => '',
	'custom_marker_info_street' => '',
	'custom_marker_info_descr' => '',

	// CSS
	'map_height' => '30%',
	// Other
	'from_elementor' => true,
	'section_map_height' => '',
	'module_custom_map_style' => '',
	'module_custom_map_code' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_core_elementor_map',
	!empty($settings['section_map_height']) ? 'section_map_height-'.$settings['section_map_height'] : '',
));

// Args from Theme Options
$zoom_map = gt3_option("zoom_map");
$custom_map_style = gt3_option("custom_map_style");
$custom_map_code = gt3_option("custom_map_code");
if ((bool)$settings['module_custom_map_style'] || $custom_map_style == '1') {
	$custom_map_style = true;
} else {
	$custom_map_style = false;
}
if (!empty($settings['module_custom_map_code'])) {
	$custom_map_code = $settings['module_custom_map_code'];
}

$google_map_latitude = gt3_option("google_map_latitude");
$google_map_longitude = gt3_option("google_map_longitude");

if ($settings['custom_coordinates'] == 'yes' && $settings['custom_latitude'] !== '') {
	$google_map_latitude = esc_attr($settings['custom_latitude']);
}

if ($settings['custom_coordinates'] == 'yes' && $settings['custom_longitude'] !== '') {
	$google_map_longitude = esc_attr($settings['custom_longitude']);
}

$map_marker_info = gt3_option("map_marker_info");

if ($settings['custom_map_marker_info'] == 'show') {
	$map_marker_info = true;
} else if ($settings['custom_map_marker_info'] == 'hide') {
	$map_marker_info = false;
}

$map_marker_info_street_number = gt3_option("map_marker_info_street_number");
$map_marker_info_street = gt3_option("map_marker_info_street");
$map_marker_info_descr = gt3_option("map_marker_info_descr");

if ($settings['custom_marker_info'] == 'yes' && $settings['custom_marker_info_street_number'] !== '') {
	$map_marker_info_street_number = $settings['custom_marker_info_street_number'];
}
if ($settings['custom_marker_info'] == 'yes' && $settings['custom_marker_info_street'] !== '') {
	$map_marker_info_street = $settings['custom_marker_info_street'];
}
if ($settings['custom_marker_info'] == 'yes' && $settings['custom_marker_info_descr'] !== '') {
	$map_marker_info_descr = $settings['custom_marker_info_descr'];
}

$info_street_number = $info_street = $info_descr = $info_divider = '';

if (!empty($map_marker_info_street_number) && strlen($map_marker_info_street_number) > 0) {
	$info_street_number = '<div class="marker_info_street_number">' . esc_html($map_marker_info_street_number) . '</div>';
}
if (!empty($map_marker_info_street) && strlen($map_marker_info_street) > 0) {
	$info_street = '<div class="marker_info_street">' . esc_html($map_marker_info_street) . '</div>';
}
if (!empty($map_marker_info_descr) && strlen($map_marker_info_descr) > 0) {
	$info_descr = '<div class="marker_info_desc">' . esc_html($map_marker_info_descr) . '</div>';
}

if (!empty($info_descr) && (!empty($info_street_number) || !empty($info_street))) {
	$info_divider = '<div class="marker_info_divider"></div>';
}

$marker_content = $info_street_number . $info_street . $info_divider . $info_descr;
$rand = substr(md5(mt_rand(1000,9999999)),0,8);

$custom_map_code = json_decode($custom_map_code);
if (json_last_error() !== JSON_ERROR_NONE) {
	$custom_map_code = array();
}

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="map-core-canvas map-id-<?php echo esc_attr($rand); ?>"></div>
		<?php if ($map_marker_info == true) { ?>
			<div class="content_core_popup">
				<div class="map_info_marker">
					<div class="map_info_marker_content"><?php echo ''.$marker_content; ?></div>
				</div>
			</div>
		<?php } ?>
		<script>
			function gt3_core_initialize_map_<?php echo esc_attr($rand); ?>() {
				<?php if ($custom_map_style == true && count($custom_map_code)) { ?>
					var styleArray = <?php echo json_encode($custom_map_code); ?>;
				<?php } else { ?>
					var styleArray = [
						{
							"featureType": "all",
							"elementType": "labels.text.fill",
							"stylers": [
								{
									"saturation": 36
								},
								{
									"color": "#000000"
								},
								{
									"lightness": 40
								}
							]
						},
						{
							"featureType": "all",
							"elementType": "labels.text.stroke",
							"stylers": [
								{
									"visibility": "on"
								},
								{
									"color": "#000000"
								},
								{
									"lightness": 16
								}
							]
						},
						{
							"featureType": "all",
							"elementType": "labels.icon",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "administrative",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#35383d"
								},
								{
									"lightness": "0"
								}
							]
						},
						{
							"featureType": "administrative",
							"elementType": "geometry.stroke",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 17
								},
								{
									"weight": 1.2
								}
							]
						},
						{
							"featureType": "administrative",
							"elementType": "labels",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "administrative.country",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "simplified"
								}
							]
						},
						{
							"featureType": "administrative.country",
							"elementType": "geometry",
							"stylers": [
								{
									"visibility": "simplified"
								}
							]
						},
						{
							"featureType": "administrative.country",
							"elementType": "labels.text",
							"stylers": [
								{
									"visibility": "simplified"
								}
							]
						},
						{
							"featureType": "administrative.province",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "administrative.locality",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "simplified"
								},
								{
									"saturation": "-100"
								},
								{
									"lightness": "30"
								}
							]
						},
						{
							"featureType": "administrative.neighborhood",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "administrative.land_parcel",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "landscape",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "simplified"
								},
								{
									"gamma": "0.00"
								},
								{
									"lightness": "74"
								}
							]
						},
						{
							"featureType": "landscape",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 20
								}
							]
						},
						{
							"featureType": "landscape",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#35383d"
								}
							]
						},
						{
							"featureType": "landscape.man_made",
							"elementType": "all",
							"stylers": [
								{
									"lightness": "3"
								}
							]
						},
						{
							"featureType": "poi",
							"elementType": "all",
							"stylers": [
								{
									"visibility": "off"
								}
							]
						},
						{
							"featureType": "poi",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 21
								}
							]
						},
						{
							"featureType": "poi.government",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#ff0000"
								}
							]
						},
						{
							"featureType": "road",
							"elementType": "geometry",
							"stylers": [
								{
									"visibility": "simplified"
								}
							]
						},
						{
							"featureType": "road.highway",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#2a2d32"
								},
								{
									"lightness": "0"
								}
							]
						},
						{
							"featureType": "road.highway",
							"elementType": "geometry.stroke",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 29
								},
								{
									"weight": 0.2
								}
							]
						},
						{
							"featureType": "road.arterial",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 18
								}
							]
						},
						{
							"featureType": "road.arterial",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#2a2d32"
								}
							]
						},
						{
							"featureType": "road.local",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 16
								}
							]
						},
						{
							"featureType": "road.local",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#2a2d32"
								}
							]
						},
						{
							"featureType": "transit",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 19
								}
							]
						},
						{
							"featureType": "transit",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#35383d"
								}
							]
						},
						{
							"featureType": "water",
							"elementType": "geometry",
							"stylers": [
								{
									"color": "#000000"
								},
								{
									"lightness": 17
								}
							]
						},
						{
							"featureType": "water",
							"elementType": "geometry.fill",
							"stylers": [
								{
									"color": "#272a2f"
								}
							]
						}
					];
				<?php } ?>

				definePopupClass();

				var myLatlng = new google.maps.LatLng(<?php echo esc_attr($google_map_latitude); ?>, <?php echo esc_attr($google_map_longitude); ?>);

				var mapOptions = {
					zoom: <?php echo esc_attr($zoom_map); ?>,
					scrollwheel: false,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					styles: styleArray
				};

				var map = new google.maps.Map(document.getElementsByClassName('map-id-<?php echo esc_attr($rand); ?>')[0], mapOptions);

				var marker = new google.maps.Marker({
					 position: myLatlng,
					 map: map,
					 icon: '<?php echo esc_url(gt3_option("custom_map_marker")); ?>'
				});

				<?php if ($map_marker_info == true) { ?>
				popup = new Popup(
					myLatlng,
					document.getElementsByClassName('content_core_popup')[0]);
				popup.setMap(map);
				<?php } ?>

			}

			function definePopupClass() {
				Popup = function(position, content) {
					this.position = position;

					content.classList.add('popup-bubble-content');

					var pixelOffset = document.createElement('div');
					pixelOffset.classList.add('popup-bubble-anchor');
					pixelOffset.appendChild(content);

					this.anchor = document.createElement('div');
					this.anchor.classList.add('popup-tip-anchor');
					this.anchor.appendChild(pixelOffset);

					this.stopEventPropagation();
				};
				Popup.prototype = Object.create(google.maps.OverlayView.prototype);

				Popup.prototype.onAdd = function() {
					this.getPanes().floatPane.appendChild(this.anchor);
				};

				Popup.prototype.onRemove = function() {
					if (this.anchor.parentElement) {
						this.anchor.parentElement.removeChild(this.anchor);
					}
				};

				Popup.prototype.draw = function() {
					var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
					var display =
						Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
                                    'block' :
                                    'none';

					if (display === 'block') {
					this.anchor.style.left = divPosition.x + 'px';
					this.anchor.style.top = divPosition.y + 'px';
				}
				if (this.anchor.style.display !== display) {
					this.anchor.style.display = display;
				}
			};

			Popup.prototype.stopEventPropagation = function() {
				var anchor = this.anchor;
				anchor.style.cursor = 'auto';
					['click', 'dblclick', 'contextmenu', 'wheel', 'mousedown', 'touchstart', 'pointerdown'].forEach(function(event) {
						anchor.addEventListener(event, function(e) {
							e.stopPropagation();
						});
					});
			};
			}
			jQuery(document).ready(function(){
				if ('google' in window) {
					gt3_core_initialize_map_<?php echo esc_attr($rand); ?>();
				} else {
					setTimeout(function () {
						if ('google' in window) {
							gt3_core_initialize_map_<?php echo esc_attr($rand); ?>();
						}
					}, 3000);
				}
			});
		</script>
	</div>
<?php




