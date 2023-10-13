<?php
return function ( $rootDir ) {
	$distFontDir = $rootDir . '/lib/fonts';

	$default_font = [
		'bold'        => $distFontDir . '/DejaVuSans-Bold',
		'bold_italic' => $distFontDir . '/DejaVuSans-Bold',
		'italic'      => $distFontDir . '/DejaVuSans',
		'normal'      => $distFontDir . '/DejaVuSans',
	];

	return [
		'sans-serif'  => $default_font,
		'serif'       => $default_font,
		'monospace'   => $default_font,
		'fixed'       => $default_font,
		'dejavu sans' => $default_font,
	];
};
