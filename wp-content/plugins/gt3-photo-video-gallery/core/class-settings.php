<?php

namespace GT3\PhotoVideoGallery;

defined('ABSPATH') OR exit;

class Settings {
	private static $key      = 'gt3pg_lite';
	private static $instance = null;
	private        $defaults = array();
	private        $settings = array();
	private        $blocks   = array(
		'Grid',
		'Masonry',
	);

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		$this->initDefaultsSettings();

		add_action('init', array( $this, 'init' ), 1);
	}

	private function initDefaultsSettings(){
		$this->defaults = array();

		$lightboxSettings = array(
			'lightboxAutoplay'     => '0',
			'lightboxAutoplayTime' => 6,
			'lightboxThumbnails'   => '1',
			'lightboxImageSize'    => 'full',
			'lightboxShowTitle'    => '1',
			'lightboxShowCaption'  => '1',
			'lightboxTheme'        => 'dark',
			'lightboxDeeplink'     => '0',
			'lightboxAllowZoom'    => '0',
			'allowDownload'        => '0',
			'ytWidth'              => '0',
			'lightboxCover'        => '0',
			'socials'              => '1',
		);

		$borderSettings = array(
			'borderType'    => '0',
			'borderColor'   => '#dddddd',
			'borderPadding' => 0,
			'borderSize'    => 1,
		);

		$this->defaults['basic'] = array_merge(
			$lightboxSettings,
			array(
				'random'         => '0',
				'rightClick'     => '0',
				'lazyLoad'       => '0',
				'loadMoreEnable' => '0',
				'loadMoreFirst'  => 12,
				'loadMoreLimit'  => 4,
				'filterEnable'   => '0',
				'usage'          => '0',
			)
		);

		$this->defaults['grid'] = array_merge(
			$borderSettings,
			array(
				'watermark'   => '0',
				'gridType'    => 'square',
				'showTitle'   => '0',
				'linkTo'      => 'lightbox',
				'showCaption' => '0',
				'imageSize'   => 'thumbnail',
				'columns'     => 3,
				'margin'      => 20,
				'cornersType' => 'standard',
			)
		);

		$this->defaults['masonry'] = array_merge(
			$borderSettings,
			array(
				'watermark'   => '0',
				'showTitle'   => '0',
				'linkTo'      => 'lightbox',
				'showCaption' => '0',
				'imageSize'   => 'thumbnail',
				'columns'     => 3,
				'margin'      => 30,
				'cornersType' => 'standard',
			)
		);

		$this->defaults['packery'] = array_merge(
			$borderSettings,
			array(
				'watermark'   => '0',
				'packery'     => 2,
				'linkTo'      => 'lightbox',
				'imageSize'   => 'thumbnail',
				'margin'      => 30,
				'cornersType' => 'standard',
			)
		);

		$this->defaults['thumbnails'] = array_merge(
			array(
				'thumbnails_Controls'            => '1',
				'thumbnails_Size'                => '16x9',
				'thumbnails_thumbnailsImageSize' => 'medium_large',
				'thumbnails_thumbnailsSize'      => 'fixed',
				'thumbnails_Lightbox'            => '0',
				'imageSize'                      => 'medium_large',
			)
		);

		$this->defaults['slider'] = array_merge(
			array(
				'sliderAllowZoom'    => '0',
				'sliderAutoplay'     => '0',
				'sliderAutoplayTime' => 6,
				'sliderThumbnails'   => '1',
				'sliderCover'        => '0',
				'sliderImageSize'    => 'full',
				'ytWidth'            => '0',
				'allowDownload'      => '0',
				'socials'            => '1',
				'sliderShowTitle'    => '1',
				'sliderShowCaption'  => '1',
				'sliderTheme'        => 'dark',
			)
		);

		$this->defaults['fsslider'] = array_merge(
			array(
				'socials'            => '1',
				'autoplay'           => '1',
				'interval'           => 4,
				'thumbnails'         => '1',
				'showTitle'          => '1',
				'showCaption'        => '1',
				'scroll'             => '1',
				'boxed'              => '0',
				'cover'              => '0',
				'imageSize'          => 'full',
				'footerAboveSlider'  => '0',
				'textColor'          => '#ffffff',
				'borderOpacity'      => 0.5,
				'externalVideoThumb' => '1',
			)
		);

		$this->defaults['kenburns'] = array_merge(
			array(
				'moduleHeight'   => '100%',
				'interval'       => 4,
				'transitionTime' => 600,
				'overlayState'   => '0',
				'overlayBg'      => '#ffffff',
			)
		);

		$this->defaults['shift']     = array_merge(
			array(
				'expandable'     => '0',
				'showTitle'      => '1',
				'controls'       => '1',
				'infinityScroll' => '1',
				'autoplay'       => '1',
				'interval'       => 4,
				'transitionTime' => 600,
				'moduleHeight'   => '100%',
			)
		);
		$this->defaults['instagram'] = array_merge(
			array(
				'loadMoreFirst' => 12,
				'columns'       => 4,
				'gridType'      => 'square',
				'margin'        => 30,
				'source'        => 'user',
				'userName'      => '',
				'userID'        => '',
				'tag'           => '',
				'linkTo'        => 'instagram',
			)
		);

		$this->defaults['justified'] = array_merge(
			array(
				'loader'       => 'fromFirst',
				'gap'          => '10',
				'lightbox'     => '0',
				'height'       => 240,
				'fadeDuration' => 200,
				'fadeDelay'    => 120,
				'imageSize'    => 'full',
			)
		);

		$this->defaults['beforeafter'] = array_merge(
			array(
				'image_before' => '',
				'image_after'  => '',
				'moduleHeight' => '100%',
			)
		);

		$this->defaults['basic']['watermark'] = array(
			'enable'    => false,
			'upload'    => false,
			'alignment' => 'right_bottom',
			'position'  => array(
				'x' => 100,
				'y' => 100,
			),
			'image'     => array(
				'id'    => 0,
				'url'   => '',
				'ratio' => 1
			),
			'sizes'     => array(
				'medium_large' => true,
				'large'        => true,
				'full'         => true,
			),
//			'width'     => 100,
//			'height'    => 100,
			'opacity'   => 100,
			'quality'   => 95,
		);

	}


	function getDefaultsSettings(){
		return $this->defaults;
	}

	function getDeprecatedValue($array, $key){
		$value = false;
		if(key_exists($key, $array)) {
			$value = in_array($array[$key], [ 1, '1', 'on', 'yes', 'true' ], true) ? '1' :
				(in_array($array[$key], [ 0, '0', 'off', 'no', 'false' ], true) ? '0' : $array[$key]);
		}

		return $value;
	}

	function deprecated($options){
		$old_settings = get_option('gt3pg_photo_gallery');
		if(!is_array($old_settings)) {
			return $options;
		}
		if(!key_exists('basic', $options)) {
			$options['basic'] = array(
				'lightboxAutoplay'     => $this->getDeprecatedValue($old_settings, 'lightboxAutoplay'),
				'lightboxAutoplayTime' => $this->getDeprecatedValue($old_settings, 'lightboxAutoplayTime'),
				'lightboxThumbnails'   => $this->getDeprecatedValue($old_settings, 'lightboxThumbnails'),
				'lightboxImageSize'    => $this->getDeprecatedValue($old_settings, 'lightboxImageSize'),
				'lightboxShowTitle'    => $this->getDeprecatedValue($old_settings, 'lightboxShowTitle'),
				'lightboxShowCaption'  => $this->getDeprecatedValue($old_settings, 'lightboxShowCaption'),
				'lightboxTheme'        => $this->getDeprecatedValue($old_settings, 'lightboxTheme'),
				'lightboxDeeplink'     => $this->getDeprecatedValue($old_settings, 'lightboxDeeplink'),
				'lightboxAllowZoom'    => $this->getDeprecatedValue($old_settings, 'lightboxAllowZoom'),

				'allowDownload' => $this->getDeprecatedValue($old_settings, 'allowDownload'),
				'ytWidth'       => $this->getDeprecatedValue($old_settings, 'ytWidth'),
				'socials'       => $this->getDeprecatedValue($old_settings, 'socials'),

				'random'     => $this->getDeprecatedValue($old_settings, 'random'),
				'rightClick' => $this->getDeprecatedValue($old_settings, 'rightClick'),
				'lazyLoad'   => $this->getDeprecatedValue($old_settings, 'lazyLoad'),
			);
		}

		if(!key_exists('grid', $options)) {
			$options['grid'] = array(
				'gridType'      => $this->getDeprecatedValue($old_settings, 'gridType'),
				'showTitle'     => $this->getDeprecatedValue($old_settings, 'showTitle'),
				'showCaption'   => $this->getDeprecatedValue($old_settings, 'showCaption'),
				'linkTo'        => $this->getDeprecatedValue($old_settings, 'linkTo'),
				'imageSize'     => $this->getDeprecatedValue($old_settings, 'imageSize'),
				'columns'       => $this->getDeprecatedValue($old_settings, 'columns'),
				'margin'        => $this->getDeprecatedValue($old_settings, 'margin'),
				'cornersType'   => $this->getDeprecatedValue($old_settings, 'cornersType'),
				'borderType'    => $this->getDeprecatedValue($old_settings, 'borderType'),
				'borderColor'   => $this->getDeprecatedValue($old_settings, 'borderColor'),
				'borderPadding' => $this->getDeprecatedValue($old_settings, 'borderPadding'),
				'borderSize'    => $this->getDeprecatedValue($old_settings, 'borderSize'),
			);
		}

		if(!key_exists('masonry', $options)) {
			$options['masonry'] = array(
				'showTitle'     => $this->getDeprecatedValue($old_settings, 'showTitle'),
				'showCaption'   => $this->getDeprecatedValue($old_settings, 'showCaption'),
				'linkTo'        => $this->getDeprecatedValue($old_settings, 'linkTo'),
				'imageSize'     => $this->getDeprecatedValue($old_settings, 'imageSize'),
				'columns'       => $this->getDeprecatedValue($old_settings, 'columns'),
				'margin'        => $this->getDeprecatedValue($old_settings, 'margin'),
				'cornersType'   => $this->getDeprecatedValue($old_settings, 'cornersType'),
				'borderType'    => $this->getDeprecatedValue($old_settings, 'borderType'),
				'borderColor'   => $this->getDeprecatedValue($old_settings, 'borderColor'),
				'borderPadding' => $this->getDeprecatedValue($old_settings, 'borderPadding'),
				'borderSize'    => $this->getDeprecatedValue($old_settings, 'borderSize'),
			);
		}

		if(!key_exists('packery', $options)) {
			$options['packery'] = array(
				'packery'       => $this->getDeprecatedValue($old_settings, 'packery'),
				'linkTo'        => $this->getDeprecatedValue($old_settings, 'linkTo'),
				'imageSize'     => $this->getDeprecatedValue($old_settings, 'imageSize'),
				'margin'        => $this->getDeprecatedValue($old_settings, 'margin'),
				'cornersType'   => $this->getDeprecatedValue($old_settings, 'cornersType'),
				'borderType'    => $this->getDeprecatedValue($old_settings, 'borderType'),
				'borderColor'   => $this->getDeprecatedValue($old_settings, 'borderColor'),
				'borderPadding' => $this->getDeprecatedValue($old_settings, 'borderPadding'),
				'borderSize'    => $this->getDeprecatedValue($old_settings, 'borderSize'),
			);
		}

		if(!key_exists('thumbnails', $options)) {
			$options['thumbnails'] = array(
				'thumbnails_Controls'            => $this->getDeprecatedValue($old_settings, 'thumbnails_Controls'),
				'thumbnails_Size'                => $this->getDeprecatedValue($old_settings, 'thumbnails_Size'),
				'imageSize'                      => $this->getDeprecatedValue($old_settings, 'imageSize'),
				'thumbnails_thumbnailsImageSize' => $this->getDeprecatedValue($old_settings, 'thumbnails_thumbnailsImageSize'),
				'thumbnails_thumbnailsSize'      => $this->getDeprecatedValue($old_settings, 'thumbnails_thumbnailsSize'),
				'thumbnails_Lightbox'            => $this->getDeprecatedValue($old_settings, 'thumbnails_Lightbox'),
			);
		}

		if(!key_exists('slider', $options)) {
			$options['slider'] = array(
				'sliderAutoplay'     => $this->getDeprecatedValue($old_settings, 'sliderAutoplay'),
				'sliderAutoplayTime' => $this->getDeprecatedValue($old_settings, 'sliderAutoplayTime'),
				'sliderThumbnails'   => $this->getDeprecatedValue($old_settings, 'sliderThumbnails'),
				'sliderCover'        => $this->getDeprecatedValue($old_settings, 'sliderCover'),
				'sliderImageSize'    => $this->getDeprecatedValue($old_settings, 'sliderImageSize'),
				'ytWidth'            => $this->getDeprecatedValue($old_settings, 'ytWidth'),
				'allowDownload'      => $this->getDeprecatedValue($old_settings, 'allowDownload'),
				'socials'            => $this->getDeprecatedValue($old_settings, 'socials'),
				'sliderShowTitle'    => $this->getDeprecatedValue($old_settings, 'sliderShowTitle'),
				'sliderShowCaption'  => $this->getDeprecatedValue($old_settings, 'sliderShowCaption'),
				'lightboxTheme'      => $this->getDeprecatedValue($old_settings, 'lightboxTheme'),
			);
		}

		return $options;
	}

	function init(){
		$options = get_option(self::$key, '');

		try {
			if(!is_array($options) && is_string($options)) {
				$options = json_decode($options, true);
				if(json_last_error() || !is_array($options) || !count($options)) {
					$options = array();
				}
			}
		} catch(\Exception $exception) {
			$options = array();
		}
		if(!count($options) && false !== get_option('gt3pg_photo_gallery')) {
			$options = $this->deprecated($options);
		}
		$options        = array_replace_recursive($this->defaults, $options);
		$this->settings = $options;

		$this->blocks = apply_filters('gt3pg-lite/blocks/allowed', $this->blocks);

		foreach($this->blocks as $block) {
			$block = __NAMESPACE__.'\\Block\\'.$block;
			if(class_exists($block)) {
				/** @var Block\Basic $block */
				$block::instance();
			};
		}

		if (Watermark::check_folder()) {
			$watermark_settings = $this->settings['basic']['watermark'];
			add_action('delete_attachment', array( Watermark::class, 'delete_attachment_handler' ));
			if(!!$watermark_settings['enable'] && !!$watermark_settings['upload']) {
				add_filter('wp_handle_upload', array( Watermark::class, 'wp_handle_upload_handler' ), 10, 2);
			}
		}
	}

	function getSettings($module = false){
		if($module && key_exists($module, $this->settings)) {
			return $this->settings[$module];
		}

		return $this->settings;
	}

	function getBlocks(){
		return $this->blocks;
	}

	function setSettings($settings){
		if(!is_array($settings) || !count($settings)) {
			return false;
		}
		$this->settings = $settings;
		update_option(self::$key, wp_json_encode($settings));

		return true;
	}

	function resetSettings(){
		$this->setSettings($this->getDefaultsSettings());
	}
}

