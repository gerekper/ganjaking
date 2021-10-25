<?php
defined('ABSPATH') OR exit;

add_action('init', function(){
	global $gt3pg_defaults;
	$gt3pg_defaults = array(
		'basic' => array(
			'lightboxAutoplay'     => false,
			'lightboxAutoplayTime' => 6,
			'lightboxThumbnails'   => true,
			'lightboxImageSize'    => 'full',
			'lightboxShowTitle'    => true,
			'lightboxShowCaption'  => true,
			'lightboxDeeplink'     => false,
			'allowDownload'        => false,
			'ytWidth'              => false,
			'lightboxCover'        => false,
			'socials'              => true,
			'lightboxTheme'        => 'dark',

			'random'     => false,
			'rightClick' => false,
			'lazyLoad'   => false,
		)
	);

	$gt3pg_defaults['grid'] = array_merge(
		array(
			'gridType'      => 'square',
			'showTitle'     => 1,
			'linkTo'        => 'lightbox',
			'showCaption'   => 1,
			'imageSize'     => 'thumbnail',
			'columns'       => 3,
			'margin'        => 30,
			'cornersType'   => 'standard',
			'borderType'    => 0,
			'borderColor'   => '#dddddd',
			'borderPadding' => 0,
			'borderSize'    => 1,
		)
	);

	$gt3pg_defaults['masonry'] = array_merge(
		array(
			'showTitle'     => 1,
			'linkTo'        => 'lightbox',
			'showCaption'   => 1,
			'imageSize'     => 'thumbnail',
			'columns'       => 3,
			'margin'        => 30,
			'cornersType'   => 'standard',
			'borderType'    => 0,
			'borderColor'   => '#dddddd',
			'borderPadding' => 0,
			'borderSize'    => 1,
		)
	);

	$gt3pg_defaults['packery'] = array_merge(
		array(
			'packery'       => 2,
			'linkTo'        => 'lightbox',
			'imageSize'     => 'thumbnail',
			'margin'        => 30,
			'cornersType'   => 'standard',
			'borderType'    => 0,
			'borderColor'   => '#dddddd',
			'borderPadding' => 0,
			'borderSize'    => 1,
		)
	);

	$gt3pg_defaults['thumbnails'] = array_merge(
		array(
			'controls'            => true,
			'size'                => '16x9',
			'imageSize'           => 'medium_large',
			'thumbnailsImageSize' => 'medium_large',
			'thumbnailsSize'      => 'fixed',
			'lightbox'            => false,
		)
	);

	$gt3pg_defaults['slider'] = array_merge(
		array(
			'sliderAutoplay'     => false,
			'sliderAutoplayTime' => 6,
			'sliderThumbnails'   => 1,
			'sliderCover'        => false,
			'sliderImageSize'    => 'full',
			'ytWidth'            => false,
			'allowDownload'      => false,
			'socials'            => true,
			'sliderShowTitle'    => true,
			'sliderShowCaption'  => true,
		)
	);

//	$gt3pg_defaults['old']    = $GLOBALS["gt3_photo_gallery"];

	$gt3pg_defaults = apply_filters('gt3pg_default_settings', $gt3pg_defaults);
}, 11);

