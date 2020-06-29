<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Check ZipArchive
if( ! class_exists('ZipArchive') ) {
	wp_die( __('The PHP ZipArchive extension is required to export sliders.', 'LayerSlider') );
}

if( ! LS_Config::isActivatedSite() ) {
	wp_die( __('Product activation is required in order to use this feature.', 'LayerSlider') );
}

// Get dindent
include LS_ROOT_PATH.'/static/dindent/Indenter.php';
$indenter = new \Gajus\Dindent\Indenter( array(
	'indentation_character' => "\t"
));

include LS_ROOT_PATH.'/classes/class.ls.exportutil.php';
$zip = new LS_ExportUtil;

// Fetch slider data
$slider = LS_Sliders::find( $sliderID );

// Override some slider settings
$slider['data']['properties']['useSrcset'] = false;

// Use alternate image URLs
remove_all_filters('wp_get_attachment_image_src');
add_filter('wp_get_attachment_image_src', function( $image ) {
	if( ! empty( $image[0] ) ) {
		$image[0] = 'images/'.basename($image[0]);
	}

	return $image;
});



// Use alternate image URLs in the init code
add_filter('layerslider_init_props_image', function( $src ) {
	return 'images/'.basename( $src );
});

add_filter('layerslider_skin_url', function( $url, $skin ) {
	return '../../layerslider/skins/'.$skin;
}, 10, 2);

add_filter('layerslider_attr_list', function( $attrs ) {
	return implode(' ', $attrs);
});

add_filter( 'layerslider_init_props_separator', function( $value ) {
	return "[[LN]]";
});

// Generate the markup
$parts 		= LS_Shortcode::generateSliderMarkup( $slider );
$template 	= file_get_contents( LS_ROOT_PATH.'/templates/html-export/template.html' );

// Replace slider ID
$parts['container'] = str_replace('layerslider_'.$sliderID.'' , 'slider', $parts['container'] );

// Format the markup with perfect indentations to elimiate the
// effect of concatenation.
$markup = $indenter->indent( $parts['container'].$parts['markup'] );

// Add an extra tab for the formatted markup, so it matches
// perfectly to the template structure.
$markup = str_replace("\n", "\r\n\t", $markup);

// Add extra line breaks between slides
$markup = str_replace('<div class="ls-slide"', "\r\n\t\t".'<div class="ls-slide"', $markup);

// Capture slider settings from the init code
preg_match('/, {(.*?)}/', $parts['init'], $matches);

// Format slider properties for use in the init code
$init = implode(",\r\n\t\t\t\t", explode('[[LN]]', $matches[1]) );

// Google Fonts
$googleFonts = '';
$fonts = $zip->fontsForSlider( $slider['data'] );
if( ! empty( $fonts ) ) {

	foreach( $fonts as $font ) {
		$googleFont[] = htmlspecialchars( $font['param'] );
	}

	$fontFamily = implode('|', $googleFont );
	$googleFonts = "\r\n\t<!-- Google Fonts -->\r\n\t".'<link rel="stylesheet" href="https://fonts.googleapis.com/css?family='.$fontFamily.'">'."\r\n\t";

}

// Icon Fonts
$iconFonts = '';
if( ! empty( $parts['fonts'] ) ) {
	$tmp = array("\r\n\t<!-- Icon Fonts -->");
	foreach( $parts['fonts'] as $font ) {

		// FontAwesome
		if( $font === 'font-awesome' ) {

			$tmp[] = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">'."\r\n\t";
		}
	}

	$iconFonts = implode("\r\n\t", $tmp);
}


// LayerSlider plugin files
$pluginFiles = '';
if( ! empty( $parts['plugins'] ) ) {

	$tmp = array("\r\n\t<!-- LayerSlider plugin files -->");
	foreach( $parts['plugins'] as $plugin ) {
		$tmp[] = '<link rel="stylesheet" href="../../layerslider/plugins/'.$plugin.'/layerslider.'.$plugin.'.css">';
		$tmp[] = '<script src="../../layerslider/plugins/'.$plugin.'/layerslider.'.$plugin.'.js"></script>'."\r\n\t";
	}

	$pluginFiles = implode("\r\n\t", $tmp);
}

// LayerSlider API
$sliderAPI = '';
if( ! empty( $slider['data']['callbacks'] ) && is_array( $slider['data']['callbacks'] ) ) {
	$apiPart[] = "$('#slider')";
	foreach( $slider['data']['callbacks'] as $event => $function) {
		$function = substr($function, 0, -1 ) . "\r\n}";
		$function = preg_replace( '/^/m', "\t\t\t", stripslashes( $function ) );
		$function = ltrim( $function );
		$apiPart[] .= '.on(\''.$event.'\', '.$function.')';
	}

	$sliderAPI = implode('', $apiPart).";\r\n\r\n";
}

// Replace placeholders
$template = str_replace( '{{slider-title}}', $slider['name'], $template );
$template = str_replace( '{{google-fonts}}', $googleFonts, $template );
$template = str_replace( '{{icon-fonts}}', $iconFonts, $template );
$template = str_replace( '{{layerslider-plugins}}', $pluginFiles, $template );
$template = str_replace( '{{slider-markup}}', $markup, $template );
$template = str_replace( '{{slider-api}}', $sliderAPI, $template);
$template = str_replace( '{{slider-props}}', $init, $template );


// Add extra line breaks between slides
$template = str_replace( '<div class="ls-slide"', "\r\n\t\t".'<div class="ls-slide"', $template );

// Add explanation comment before slides
$count = 1;
$template = preg_replace_callback('/<div class\=\"ls-slide\"/', function( $matches ) use (&$count) {
	return "<!-- Slide ".$count++."-->\r\n\t\t".'<div class="ls-slide"';
}, $template);

// Remove 'fitvidsignore'
$template = str_replace( ' class="ls-wp-container fitvidsignore"', '', $template );


// Build ZIP

	// Remove URL generation related filters
	remove_all_filters('wp_get_attachment_image_src');

	// Add slider folder
	$name = empty( $slider['name'] ) ? 'slider_' . $slider['id'] : $slider['name'];
	$name = sanitize_file_name( $name );

	// Add images
	$images = $zip->getImagesForSlider( $slider['data'] );
	$images = $zip->getFSPaths( $images );
	$zip->addImage( $images, $name, 'images' );

	// Add slider HTML file
	$zip->addFileFromString( "$name/slider.html", $template);

	// Add instructions
	$zip->addFile( LS_ROOT_PATH.'/templates/html-export/INSTRUCTIONS.html', "$name/" );
	$zip->addImage( LS_ROOT_PATH.'/templates/html-export/ls-instructions.png', "$name", 'images');



	$zip->download( 'LayerSlider HTML - '.$name.' '.ls_date('Y-m-d').' at '.ls_date('H.i.s').'.zip');
die();