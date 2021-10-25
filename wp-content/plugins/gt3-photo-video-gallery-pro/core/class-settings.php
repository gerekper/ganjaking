<?php

namespace GT3\PhotoVideoGalleryPro;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') OR exit;

class Settings {
	private static $key      = 'gt3pg_pro';
	private static $instance = null;
	private        $defaults = array();
	private        $settings = array();
	private        $blocks   = array(
		'Masonry',
		'Grid',
		'Packery',
		'Slider',
		'Thumbnails',
		'Albums',
		'FSSlider',
		'FS_Slider',
		'Shift',
		'Instagram',
		'Justified',
		'Kenburns',
		'Flow',
		'Ribbon',
		'BeforeAfter',
		'Before_After',
	);

	/** @return Settings */
	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		$this->initDefaultsSettings();
		$this->init();

//		add_action('init', array( $this, 'init' ), 10);
//		add_action('admin_init', array( $this, 'init' ), 10);
		add_action('init', array( $this, 'after_theme_setup' ), 5);
		add_action('admin_init', array( $this, 'after_theme_setup' ), 5);
		add_action('after_setup_theme', array( $this, 'after_theme_setup' ), 5);
		add_filter('gt3pg-lite/blocks/allowed', array( $this, 'lite_blocks' ));


	}

	public function after_theme_setup(){
		static $called = false;
		if($called) {
			return $called = true;
		}

		$this->blocks = array_unique(apply_filters('gt3pg-pro/blocks/allowed', $this->blocks));

		foreach($this->blocks as $block) {
			$block = __NAMESPACE__.'\\Block\\'.$block;
			if(class_exists($block)) {
				/** @var Block\Basic $block */
				$block::instance();
			};
		}
	}

	public function lite_blocks($blocks){
		return array_diff($blocks, $this->blocks);
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
			'lightboxCapDesc'      => 'caption',
			'lightboxTheme'        => 'dark',
			'lightboxDeeplink'     => '0',
			'lightboxAllowZoom'    => '0',
			'allowDownload'        => '0',
			'ytWidth'              => '0',
			'lightboxCover'        => '0',
			'socials'              => '1',
			'animationType'        => 'slide',
			'externalVideoThumb'   => '0',
		);

		$borderSettings = array(
			'borderType'    => '0',
			'borderColor'   => '#dddddd',
			'borderPadding' => 0,
			'borderSize'    => 1,
		);

		$search = array(
			'search'      => false,
			'searchAlign' => 'left',
			'searchWidth' => 200,
		);

		$this->defaults['basic'] = array_merge(
			$lightboxSettings,
			array(
				'random'          => '0',
				'rightClick'      => '0',
				'lazyLoad'        => '1',
				'loadMoreEnable'  => '0',
				'loadMoreFirst'   => 12,
				'loadMoreLimit'   => 4,
				'filterEnable'    => '0',
				'filterText'      => esc_html__('All', 'gt3pg_pro'),
				'filterShowCount' => '1',
				'usage'           => '1',
			),
			$search
		);

		$this->defaults['grid'] = array_merge(
			$borderSettings,
			array(
				'gridType'      => 'square',
				'showTitle'     => '1',
				'linkTo'        => 'lightbox',
				'showCaption'   => '1',
				'imageSize'     => 'thumbnail',
				'columns'       => 5,
				'columnsTablet' => 3,
				'columnsMobile' => 2,
				'margin'        => 20,
				'cornersType'   => 'standard',
			)
		);

		$this->defaults['masonry'] = array_merge(
			$borderSettings,
			array(
				'showTitle'       => '1',
				'linkTo'          => 'lightbox',
				'showCaption'     => '1',
				'imageSize'       => 'thumbnail',
				'columns'         => 3,
				'columnsTablet'   => 4,
				'columnsMobile'   => 2,
				'margin'          => 30,
				'cornersType'     => 'standard',
				'horizontalOrder' => '0',
			)
		);

		$this->defaults['packery'] = array_merge(
			$borderSettings,
			array(
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
				'previewSize'                    => 'fixed',
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

		$this->defaults['flow'] = array_merge(
			array(
				'transitionTime' => 600,
				'autoplay'       => '0',
				'showTitle'      => '0',
				'interval'       => 4,
				'moduleHeight'   => '100%',
				'imageRatio'     => '4x3',
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

		$this->defaults['ribbon']    = array_merge(
			array(
				'showTitle'      => '0',
				'showCaption'    => '0',
				'moduleHeight'   => '100%',
				'itemsPadding'   => 30,
				'controls'       => '1',
				'autoplay'       => '1',
				'interval'       => 4,
				'transitionTime' => 600,
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

		$this->defaults['albums'] = array_merge(
			$borderSettings,
			array(
				'albumType'       => 'grid',
				'gridType'        => 'square',
				'showTitle'       => '1',
				'linkTo'          => 'lightbox',
				'showCaption'     => '1',
				'imageSize'       => 'thumbnail',
				'columns'         => 3,
				'columnsTablet'   => 3,
				'columnsMobile'   => 3,
				'margin'          => 20,
				'cornersType'     => 'standard',
				'horizontalOrder' => '0',
				'packery'         => 2,
				'paginationType'  => 'none',
			)
		);

		$this->defaults['carousel'] = array_merge(
			$borderSettings,
			array(
				'imageSize'   => 'thumbnail',
				'showTitle'   => '1',
				'showCaption' => '1',

				'centerMode'     => '1',
				'infinite'       => '1',
				'variableWidth'  => '1',
				'fade'           => '0',
				'dots'           => '0',
				'arrow'          => '1',
//				'slidesToShow'   => 3,
				'slidesToScroll' => 3,
				'centerPadding'  => '10',
				'autoplay'       => '0',
				'autoplaySpeed'  => '2000',
			)
		);

		$this->defaults['zoom'] = array_merge(
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
		$old_settings = get_option('gt3pg_lite');
		if($old_settings === false) {
			$old_settings = get_option('gt3pg_photo_gallery');
			if(!is_array($old_settings)) {
				return $options;
			} else {
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
		} else {
			try {
				if(!is_array($old_settings) && is_string($old_settings)) {
					$options = json_decode($old_settings, true);
					if(json_last_error() || !is_array($options) || !count($options)) {
						$options = array();
					}
				}
			} catch(\Exception $exception) {
				$options = array();
			}

			return $options;
		}
	}

	function init(){
		static $called = false;
		if($called) {
			return;
		}
		$called  = true;
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
		if((!is_array($options) || (is_array($options) && !count($options)))
		   && (false !== get_option('gt3pg_photo_gallery') || false !== get_option('gt3pg_lite'))
		) {
			$options = $this->deprecated($options);
		}

		$options        = array_replace_recursive($this->defaults, $options);
		$this->settings = $options;
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

/**
 * @return Settings
 */

Settings::instance();
