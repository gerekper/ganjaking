<?php

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

add_action('init', function(){
	$convert = array(
		'gt3pg_border'                 => "borderType",
		'gt3pg_border_col'             => "borderColor",
		'gt3pg_border_padding'         => "borderPadding",
		'gt3pg_border_size'            => "borderSize",
		'gt3pg_columns'                => "columns",
		'gt3pg_corner_type'            => "cornersType",
		'gt3pg_download'               => "allowDownload",
		'gt3pg_lightbox_autoplay'      => "lightboxAutoplay",
		'gt3pg_lightbox_autoplay_time' => "lightboxAutoplayTime",
		'gt3pg_lightbox_cover'         => "lightboxCover",
		'gt3pg_lightbox_deeplink'      => "lightboxDeeplink",
		'gt3pg_lightbox_image_size'    => "lightboxImageSize",
		'gt3pg_lightbox_preview'       => "lightboxThumbnails",
		'gt3pg_link_to'                => "linkTo",
		'gt3pg_margin'                 => "margin",
		'gt3pg_packery_grid'           => "packery",
		'gt3pg_rand_order'             => "random",
		'gt3pg_right_click'            => "rightClick",
		'gt3pg_show_image_caption'     => "showCaption",
		'gt3pg_show_image_title'       => "showTitle",
		'gt3pg_size'                   => "imageSize",
		'gt3pg_slider_autoplay'        => "sliderAutoplay",
		'gt3pg_slider_autoplay_time'   => "sliderAutoplayTime",
		'gt3pg_slider_cover'           => "sliderCover",
		'gt3pg_slider_image_size'      => "sliderImageSize",
		'gt3pg_slider_preview'         => "sliderThumbnails",
		'gt3pg_social'                 => "socials",
		'gt3pg_yt_width'               => "ytWidth",
	);

	$GLOBALS["gt3_photo_gallery_defaults"] = apply_filters('gt3_photo_gallery_defaults', array(
		'linkTo'                         => 'lightbox',
		'imageSize'                      => 'thumbnail',
		'columns'                        => 3,
		'random'                         => '0',
		'gt3pg_thumbnail_type'           => 'rectangle',
		'cornersType'                    => 'standard',
		'margin'                         => 30,
		'borderType'                     => 'off',
		'borderColor'                    => '#dddddd',
		'borderPadding'                  => 0,
		'borderSize'                     => 1,
		'gt3pg_text_before_head'         => '',
		'gridType'                       => 'square',
		'lightboxAutoplay'               => '0', // false
		'lightboxAutoplayTime'           => 6, // 6s
		'lightboxThumbnails'             => '1', // true
		'sliderAutoplay'                 => '0', // false
		'sliderAutoplayTime'             => 6, // 6s
		'sliderThumbnails'               => '1', // true
		'socials'                        => '1', // Show
		'rightClick'                     => '0', // Normal
		'lightboxCover'                  => '0', // Normal
		'sliderCover'                    => '0', // Normal
		'lightboxImageSize'              => 'full', // Normal
		'sliderImageSize'                => 'full', // Normal
		'thumbnails_Lightbox'            => '0',
		'thumbnails_Size'                => '16x9',
		'thumbnails_Controls'            => '1',
		'thumbnails_thumbnailsSize'      => 'fixed',
		'thumbnails_thumbnailsImageSize' => 'medium_large',
		'showCaption'                    => '1', // Show
		'lightboxDeeplink'               => '0',
		'allowDownload'                  => '1', // Show
		'packery'                        => '1',
		'showTitle'                      => '0',
		'ytWidth'                        => '0',
		'lightboxShowTitle'              => '1',
		'lightboxShowCaption'            => '1',
		'sliderShowTitle'                => '1',
		'sliderShowCaption'              => '1',
		'lazyLoad'                       => '0',
		'lightboxTheme'                  => 'dark',
		'fsSliderAutoplay'               => 0,
		'fsSliderAutoplayTime'           => 6,
		'fsSliderThumbnails'             => 6,
		'fsSliderImageSize'              => 'full',
		'fsSliderShowTitle'              => 1,
		'fsSliderShowCaption'            => 1,
		'fsSliderBoxed'                  => 1,
		'fsSliderDisableScroll'          => 1,
		'fsSliderCover'                  => 0,
		'lightboxAllowZoom'              => 1,
		'useFilter'                      => 0,
		'filterText'                     => 'All',
		'loadMoreEnable'                 => 0,
		'loadMoreLimit'                  => 3,
		'loadMoreFirst'                  => 9,
	));
	$changed                               = false;
	foreach($convert as $oldKey => $newKey) {
		if(array_key_exists($oldKey, $GLOBALS['gt3_photo_gallery_defaults']) &&
		   !array_key_exists($newKey, $GLOBALS['gt3_photo_gallery_defaults'])) {
			$GLOBALS['gt3_photo_gallery_defaults'][$newKey] =
				in_array($GLOBALS['gt3_photo_gallery_defaults'][$oldKey], array(
					'1',
					1,
					'true',
					true,
					'on',
					'yes'
				), true) ? '1' : (in_array($GLOBALS['gt3_photo_gallery_defaults'][$oldKey], array(
					'0',
					0,
					'false',
					false,
					'off',
					'no'
				), true) ? '0' : $GLOBALS['gt3_photo_gallery_defaults'][$oldKey]);
		}
	}

	$GLOBALS["gt3_photo_gallery"] = gt3pg_get_option("photo_gallery");

	foreach($convert as $oldKey => $newKey) {
		if(array_key_exists($oldKey, $GLOBALS['gt3_photo_gallery']) &&
		   !array_key_exists($newKey, $GLOBALS['gt3_photo_gallery'])) {
			$GLOBALS['gt3_photo_gallery'][$newKey] =
				in_array($GLOBALS['gt3_photo_gallery'][$oldKey], array(
					'1',
					1,
					'true',
					true,
					'on',
					'yes'
				), true) ? '1' : (in_array($GLOBALS['gt3_photo_gallery'][$oldKey], array(
					'0',
					0,
					'false',
					false,
					'off',
					'no'
				), true) ? '0' : $GLOBALS['gt3_photo_gallery'][$oldKey]);
		}
	}
}, 1);
