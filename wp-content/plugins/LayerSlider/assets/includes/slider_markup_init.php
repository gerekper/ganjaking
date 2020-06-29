<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Get init code
foreach($slides['properties']['attrs'] as $key => $val) {

	if(is_bool($val)) {
		$val = $val ? 'true' : 'false';
		$init[] = $key.': '.$val;
	} elseif(is_numeric($val)) { $init[] = $key.': '.$val;
	} else { $init[] = "$key: '$val'"; }
}

// Full-size sliders
if( ( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'fullsize' ) && ( empty($slides['properties']['attrs']['fullSizeMode']) || $slides['properties']['attrs']['fullSizeMode'] !== 'fitheight' ) ) {
	$init[] = 'height: '.$slides['properties']['props']['height'].'';
}

// Popup
if( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'popup' ) {
	$lsPlugins[] = 'popup';
}

if( ! empty( $lsPlugins ) ) {
	$lsPlugins = array_unique( $lsPlugins );
	sort( $lsPlugins );
	$init[] = 'plugins: ' . json_encode( $lsPlugins );
}

if( get_option('ls_suppress_debug_info', false ) ) {
	$init[] = 'hideWelcomeMessage: true';
}

$callbacks = array();

if( ! empty( $slides['callbacks'] ) && is_array( $slides['callbacks'] ) ) {
	foreach( $slides['callbacks'] as $event => $function ) {
		$callbacks[] = $event.': '.stripslashes( $function );
	}
}

$separator = apply_filters( 'layerslider_init_props_separator', ', ');
$initObj = implode( $separator, $init );
$eventsObj = ! empty( $callbacks ) ? ', {'.implode( $separator, $callbacks ).'}' : '';

$lsInit[] = 'jQuery(function() { _initLayerSlider( \'#'.$sliderID.'\', {'.$initObj.'}'.$eventsObj.'); });';