<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGalleryPro\Help\Types;
use GT3_Post_Type_Gallery;

class Slider extends Isotope_Gallery {
	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'ytWidth'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderAutoplay'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderAutoplayTime' => array(
					'type'    => 'string',
					'default' => '6',
				),
				'sliderThumbnails'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderCover'        => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderImageSize'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'socials'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'allowDownload'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderShowTitle'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sliderShowCaption'  => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'lightboxTheme'      => array(
					'type'    => 'string',
					'default' => 'default',
				)
			)
		);
	}

	protected $name = 'slider';

	protected function construct(){
		$this->add_script_depends('imageloaded');
		$this->add_script_depends('isotope');
		$this->add_script_depends('youtube_api');
		$this->add_script_depends('vimeo_api');
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array(
				'ytWidth'            => Types::TYPE_BOOL,
				'sliderAutoplay'     => Types::TYPE_BOOL,
				'sliderAutoplayTime' => Types::TYPE_INT,
				'sliderThumbnails'   => Types::TYPE_BOOL,
				'sliderCover'        => Types::TYPE_BOOL,
				'socials'            => Types::TYPE_BOOL,
				'allowDownload'      => Types::TYPE_BOOL,
				'sliderShowTitle'    => Types::TYPE_BOOL,
				'sliderShowCaption'  => Types::TYPE_BOOL,
				'sliderAllowZoom'    => Types::TYPE_BOOL,
			)
		);
	}

	protected function renderItem($image, &$settings){
		$settings['lightboxImageSize'] = $settings['sliderImageSize'];
		$lightbox_item                 = $this->getLightboxItem($image, $settings);
		$settings['lightboxArray'][]   = $lightbox_item;
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		if(!count($settings['ids'])) {
			return;
		}
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--isotope_gallery');

		if($settings['random']) {
			shuffle($settings['ids']);
		}

		if($settings['sliderImageSize'] === 'thumbnail') {
			$settings['sliderImageSize'] = 'medium_large';
		}
		$settings['lightboxArray']  = array();
		$settings['lightbox']       = true;
		$settings['loadMoreEnable'] = false;

		if(!isset($GLOBALS['gt3pg']) || !is_array($GLOBALS['gt3pg']) ||
		   !isset($GLOBALS['gt3pg']['extension']) || !is_array($GLOBALS['gt3pg']['extension']) ||
		   !isset($GLOBALS['gt3pg']['extension']['pro_optimized'])
		) {
			if($settings['sliderImageSize'] === 'gt3pg_optimized') {
				$settings['lightboxImageSize'] = 'large';
			}

		}

		if($settings['rightClick']) {
			$this->add_render_attribute('wrapper', array(
				'oncontextmenu' => 'return false',
				'onselectstart' => 'return false'
			));
		}

		$this->add_render_attribute('wrapper', 'class', array(
			'gt3pg-isotope-gallery',
			'gallery-'.$this->name,
			$settings['externalVideoThumb'] ? 'video-thumbnails-hidden' : '',
		));
		$dataSettings = array(
			'lightbox' => $settings['lightbox'],
			'id'       => $this->render_index,
			'uid'      => $this->_id,
		);

		if(!in_array($settings['lightboxCapDesc'], array( 'caption', 'description' ))) {
			$settings['lightboxCapDesc'] = 'caption';
		}
		$dataSettings['lightboxOptions'] = array(
			'showTitle'           => $settings['sliderShowTitle'],
			'showCaption'         => $settings['sliderShowCaption'],
			'descriptionProperty' => $settings['lightboxCapDesc'],
			'allowDownload'       => $settings['allowDownload'],
			'allowZoom'           => $settings['sliderAllowZoom'],
			'socials'             => $settings['socials'],
			'stretchImages'       => $settings['sliderCover'],
			'thumbnailIndicators' => $settings['sliderThumbnails'],
			'startSlideshow'      => $settings['sliderAutoplay'],
			'slideshowInterval'   => $settings['sliderAutoplayTime']*1000, // s -> ms
			'instance'            => static::$index,
			'customClass'         => 'style-'.$settings['sliderTheme'],
			'rightClick'          => $settings['rightClick'],
			'externalPosters'     => $settings['externalVideoThumb'],
		);

		if($settings['sliderAutoplay']) {
			$this->add_style('.gt3pg_pro_duration', array(
				'animation-duration: %ss' => $settings['sliderAutoplayTime'],
			));
		}

		if($settings['ytWidth']) {
			$dataSettings['lightboxOptions']['ytWidth'] = true;
		}

		$items      = '';
		$foreachIds = $settings['ids'];

		foreach($foreachIds as $id) {
			$this->renderItem($id, $settings);
		}

		$dataSettings['lightboxArray'] = $settings['lightboxArray'];

//		$this->add_render_attribute('wrapper', array(
//			'data-settings' => wp_json_encode($dataSettings)
//		));

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="gallery-isotope-wrapper">
			</div>
		</div>
		<script type="application/json" id="settings--<?php echo $this->_id; ?>"><?php echo wp_json_encode($dataSettings) ?></script>
		<?php

		return;
	}
}

