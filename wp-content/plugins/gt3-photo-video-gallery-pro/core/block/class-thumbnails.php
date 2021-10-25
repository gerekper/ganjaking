<?php

namespace GT3\PhotoVideoGalleryPro\Block;

use GT3\PhotoVideoGalleryPro\Help\Types;

defined('ABSPATH') OR exit;

class Thumbnails extends Isotope_Gallery {
	private $render_item_index = 0;

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'thumbnails_Lightbox'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'thumbnails_Size'                => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'thumbnails_Controls'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'thumbnails_thumbnailsSize'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'thumbnails_thumbnailsImageSize' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'previewSize'                    => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected $name = 'thumbnails';

	protected function construct(){
		$this->add_script_depends('imageloaded');
		$this->add_script_depends('youtube_api');
		$this->add_script_depends('vimeo_api');

		$this->add_style_depends('gt3pg-pro-blocks-frontend');
	}

	protected function getDeprecatedSettings(){
		return array_merge(
			parent::getDeprecatedSettings(),
			array()
		);
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array(
				'thumbnails_Controls' => Types::TYPE_BOOL,
				'thumbnails_Lightbox' => Types::TYPE_BOOL,
			)
		);
	}

	protected function renderItem($image, &$settings){
		if($settings['lightbox']) {
			$lightbox_item               = $this->getLightboxItem($image, $settings);
			$settings['lightboxArray'][] = $lightbox_item;
		}

		$render      = '';
		$wrap_ratio  = $settings['thumbnails_Size'] === '16x9' ? 1.78 : 1.33;
		$ratio       = round($image['width']/$image['height'], 2);
		$ratio_class = 'landscape';
		$align       = 'height';

		switch($settings['previewSize']) {
			case 'auto':
				if($ratio > $wrap_ratio) {
					$ratio_class = 'portrait';
					$align       = 'width';
				}
				break;
			case 'fixed':
				if($ratio < $wrap_ratio) {
					$ratio_class = 'portrait';
					$align       = 'width';
				}
				break;
		}

		$render .= '<div class="gt3pg-thumbnails-item '.$ratio_class.'" data-index="'.esc_attr($this->render_item_index).'" data-align="'.$align.'"><div class="isotope_item-wrapper"><div class="img-wrapper">';
		$render .= $this->wp_get_attachment_image($image['id'], $settings['imageSize']);
		$render .= '</div></div></div>';

		return $render;
	}

	private function renderItemThumbnail($image, &$settings){
		$ratio_class = '';
		if($settings['thumbnails_thumbnailsSize'] != 'auto') {
			$wrap_ratio  = $settings['thumbnails_thumbnailsSize'] === '16x9' ? 1.78 : 1.33;
			$ratio       = round($image['width']/$image['height'], 2);
			$ratio_class = ($ratio < $wrap_ratio) ? 'portrait' : 'landscape';
		}

		$render = '';
		$render .= '<div class="gt3pg-thumbnails-item '.$ratio_class.'"><div class="isotope_item-wrapper">';
		$render .= $this->wp_get_attachment_image($image['id'], $settings['thumbnails_thumbnailsImageSize']);
		$render .= '</div></div>';

		return $render;
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		if(!count($settings['ids'])) {
			return;
		}
		if($settings['random']) {
			shuffle($settings['ids']);
		}
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--thumbnails_gallery');

		if(!isset($GLOBALS['gt3pg']) || !is_array($GLOBALS['gt3pg']) ||
		   !isset($GLOBALS['gt3pg']['extension']) || !is_array($GLOBALS['gt3pg']['extension']) ||
		   !isset($GLOBALS['gt3pg']['extension']['pro_optimized'])
		) {
			if($settings['imageSize'] === 'gt3pg_optimized') {
				$settings['imageSize'] = 'large';
			}
			if($settings['thumbnails_thumbnailsImageSize'] === 'gt3pg_optimized') {
				$settings['thumbnails_thumbnailsImageSize'] = 'medium';
			}
		}

		if($settings['rightClick']) {
			$this->add_render_attribute(
				'wrapper', array(
					'oncontextmenu' => 'return false',
					'onselectstart' => 'return false'
				)
			);
		}

		if($settings['imageSize'] === 'thumbnail') {
			$settings['imageSize'] = 'medium_large';
		}

		if($settings['thumbnails_thumbnailsSize'] == 'fixed') {
			$settings['thumbnails_thumbnailsSize'] = $settings['thumbnails_Size'];
		}

		$this->add_render_attribute(
			'wrapper', 'class',
			array(
				'thumbnails-isotope-gallery',
				'loading',
				$settings['externalVideoThumb'] ? 'video-thumbnails-hidden' : '',
			)
		);
		$dataSettings = array(
			'id'  => $this->render_index,
			'uid' => $this->_id,
		);

		$items                     = '';
		$thumbs                    = '';
		$settings['lightboxArray'] = array();
		$settings['lightbox']      = $settings['thumbnails_Lightbox'];
		$dataSettings['lightbox']  = $settings['lightbox'];
		$this->render_item_index   = 0;
		foreach($settings['ids'] as $id) {
			$items  .= $this->renderItem($id, $settings);
			$thumbs .= $this->renderItemThumbnail($id, $settings);
			$this->render_item_index++;
		}


		if($settings['lightbox']) {
			if(!in_array($settings['lightboxCapDesc'], array( 'caption', 'description' ))) {
				$settings['lightboxCapDesc'] = 'caption';
			}
			$dataSettings['lightboxOptions'] = array(
				'descriptionProperty' => $settings['lightboxCapDesc'],
				'allowDownload'       => $settings['allowDownload'],
				'socials'             => $settings['socials'],
				'stretchImages'       => $settings['lightboxCover'],
				'thumbnailIndicators' => $settings['lightboxThumbnails'],
				'startSlideshow'      => $settings['lightboxAutoplay'],
				'slideshowInterval'   => $settings['lightboxAutoplayTime']*1000, // s -> ms
				'customClass'         => 'style-'.$settings['lightboxTheme'],
				'instance'            => $this->render_index,
				'rightClick'          => $settings['rightClick'],
				'externalPosters'     => $settings['externalVideoThumb'],
			);
			if($settings['ytWidth']) {
				$dataSettings['lightboxOptions']['ytWidth'] = true;
			}
			$dataSettings['lightboxArray'] = $settings['lightboxArray'];
		}

//		$this->add_render_attribute(
//			'wrapper', array(
//				'data-images'   => wp_json_encode(array()),
//				'data-settings' => wp_json_encode($dataSettings)
//			)
//		);

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="gallery-thumbnails-wrapper<?php echo ' size-'.$settings['thumbnails_Size']; ?><?php echo ' align-'.$settings['previewSize']; ?>">
				<?php
				echo $items; // XSS Ok
				?>
			</div>
			<div class="thumbnails-wrapper">
				<div class="thumbnails-wrapper-items <?php echo 'size-'.$settings['thumbnails_thumbnailsSize']; ?>">
					<?php
					echo $thumbs; // XSS Ok
					?>
				</div>
				<?php
				if($settings['thumbnails_Controls']) {
					echo '<div class="controls">
						      <span class="nav prev hide"></span>
						      <span class="nav next hide"></span>
						  </div>';
				}
				?>
			</div>
		</div>
		<script type="application/json" id="settings--<?php echo $this->_id; ?>"><?php echo wp_json_encode($dataSettings) ?></script>

		<?php

		return;
	}
}

