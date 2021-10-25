<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

class Zoom extends Isotope_Gallery {
	protected $name = 'zoom';
	protected $isCategoryEnabled = true;

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'is_custom'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'loader'          => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'gap'             => array(
					'type'    => 'string',
					'default' => '10',
				),
				'lightbox'        => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'height'          => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'fadeDuration'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'fadeDelay'       => array(
					'type'    => 'string',
					'default' => '140',
				),
				'imageSize'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'filterEnable'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'filterShowCount' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'filterText'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected function getUnselectedSettings(){
		return array_merge(
			parent::getUnselectedSettings(),
			array(/*'autoplay'  => 'interval',
				'fade' => array(
					'fadeDuration',
					'fadeDelay',
				),
				'height' => array(
					'heightValue',
					'heightUnit',
				),
				'gridGap' => array(
					'gridGapValue',
				),*/
			)
		);
	}

	protected function construct(){
	$this->add_script_depends('imagesloaded');
	}

	protected function renderItem($image, &$settings){
		if($settings['lightbox']) {
			$lightbox_item               = $this->getLightboxItem($image, $settings);
			$settings['lightboxArray'][] = $lightbox_item;
		}

		$img_ratio = round($image['width']/$image['height'], 4);
		$img_meta  = 'data-width="'.$image['width'].'" data-height="'.$image['height'].'" data-ratio="'.$img_ratio.'"';
		$render    = '';

		$render .= '<div class="gt3pg-isotope-item loading '.$image['item_class'].'" '.$img_meta.' style="flex-grow: '.($img_ratio*100).'; flex-basis:'.($img_ratio*$settings['height']+$settings['gap']).'px; min-width_: '.($img_ratio*$settings['height']).'px" data-grow="'.($img_ratio*$settings['height']+$settings['gap']).'" data-ratio="'.($img_ratio).'"><div class="wrapper">';
		if($settings['lightbox']) {
			$render .= '<a href="'.esc_url($image['url']).'" class="lightbox" data-elementor-open-lightbox="no">';
		}

		$render .= '<div class="img_wrap"><div class="img" >';
		$render .= $this->wp_get_attachment_image($image['id'], $settings['imageSize']);
//		$render .= '<img '.$img_meta.' src="'.wp_get_attachment_image_url($image['id'], $settings['imageSize']).'" title="'.esc_attr($image['title']).'"/>';
		$render .= '</div></div>';

		/*if((bool) $settings['show_title'] && (!empty($image['title']))) {
			$render .= '<div class="text_wrap">';
			if((bool) $settings['show_title'] && !empty($image['title'])) {
				$render .= '<h4 class="title">'.esc_html($image['title']).'</h4>';
			}
			$render .= '</div>';
		}*/

		if($settings['lightbox']) {
			$render .= '</a>';
		}
		$render .= '</div></div>';

		return $render;
	}

	protected function render($settings){
		$this->wrapper_classes = array( 'gt3-photo-gallery-pro--isotope_gallery' );
		$this->checkImagesNoEmpty($settings);

		if(!count($settings['ids'])) {
			return;
		}
//		\GT3_Lazy_Images::instance()->setup_filters();

		if($settings['imageSize'] === 'thumbnail') {
			$settings['imageSize'] = 'medium_large';
		}
		$settings['lightboxArray'] = array();

		$items           = '';
		$settings['gap'] = intval($settings['gap']);
		foreach($settings['ids'] as $image) {
			$items .= $this->renderItem($image, $settings);
		}

		$dataSettings = array(
			'lightbox'     => (bool) $settings['lightbox'],
			'gap_value'    => intval($settings['gap']),
			'gap_unit'     => substr($settings['gap'], -1) == '%' ? '%' : 'px',
			'thumb_height' => array(
				'size' => intval($settings['height']),
				'unit' => 'px',//$settings['height'] === 'px' ? 'px' : 'vh',
			),
			'height' => array(
				'size' => '100',
				'unit' => 'vh',//$settings['height'] === 'px' ? 'px' : 'vh',
			),
			'fade_delay'   => intval($settings['fadeDelay']),
			'random'       => $settings['loader'] === 'random',
			'loader'       => $settings['loader'],
			'smartResize'  => true,//(bool) $settings['zoomSmartResize'],
		);

		$class_wrapper = array(
			'gt3pg-justified-gallery',
			'zoom_gallery_wrapper'
		);

		if($settings['lightbox']) {
			$dataSettings['lightboxArray']   = $settings['lightboxArray'];
			$dataSettings['lightboxOptions'] = array(
				'showTitle'           => $settings['lightboxShowTitle'],
				'showCaption'         => $settings['lightboxShowCaption'],
				'allowDownload'       => $settings['allowDownload'],
				'allowZoom'           => $settings['lightboxAllowZoom'],
				'socials'             => $settings['socials'],
				'deepLink'            => $settings['lightboxDeeplink'],
				'stretchImages'       => $settings['lightboxCover'],
				'thumbnailIndicators' => $settings['lightboxThumbnails'],
				'startSlideshow'      => $settings['lightboxAutoplay'],
				'slideshowInterval'   => $settings['lightboxAutoplayTime']*1000, // s -> ms
				'instance'            => static::$index,
				'customClass'         => 'style-'.$settings['lightboxTheme'],
				'rightClick'          => $settings['rightClick'],
			);

			if($settings['ytWidth']) {
				$dataSettings['lightboxOptions']['ytWidth'] = true;
			}
		}

//		if((bool) $settings['smartResize']) {
//			$class_wrapper[] = 'smart-resize';
//		}

		$this->add_style('.gt3pg-isotope-gallery', array(
			'margin-right: -%1$s%2$s;'  => array( $dataSettings['gap_value'], $dataSettings['gap_unit'] ),
			'margin-bottom: -%1$s%2$s;' => array( $dataSettings['gap_value'], $dataSettings['gap_unit'] ),
		));
		$this->add_style('.gt3pg-isotope-gallery .gt3pg-isotope-item', array(
			'padding-right: %1$s%2$s;'  => array( $dataSettings['gap_value'], $dataSettings['gap_unit'] ),
			'padding-bottom: %1$s%2$s;' => array( $dataSettings['gap_value'], $dataSettings['gap_unit'] ),
//			'transition-duration: %1$s%2$s;' => array( $settings['fadeDuration'], 'ms' ),
//			'min-height: %1$s%2$s;'          => array( ($settings['height']+$settings['gap']), 'px' ),
		));

		$this->add_render_attribute('wrapper', 'class', $class_wrapper);
		$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode($dataSettings));
		?>
		<div <?php $this->print_render_attribute_string('wrapper') ?>>
			<?php if($settings['filterEnable'] && count($settings['filter_array']) > 1) {
				?>
				<div class="isotope-filter<?php echo $settings['filterShowCount'] ? ' with-counts' : '' ?>">
					<?php
					$this->add_inline_editing_attributes('filterText');
					$this->add_render_attribute('filterText', array(
						'class'       => 'active',
						'href'        => '#',
						'data-filter' => '*',
						'data-count'  => $settings['filterCount']['*'],
					));
					echo '<a '.$this->get_render_attribute_string('filterText').'>'.esc_html($settings['filterText']).'</a>';
					ksort($settings['filter_array']);
					foreach($settings['filter_array'] as $cat_slug) {
						echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'" data-count="'.$settings['filterCount'][$cat_slug['slug']].'">'.esc_html($cat_slug['name']).'</a>';
					}
					?>
				</div>
			<?php } ?>
			<div class="gt3pg-isotope-gallery items_list gt3_clear css-resize">
				<?php
				echo $items; // XSS ok
				?>
			</div>
			<?php
			$this->getPreloader();
			?>
		</div>
		<?php
//		\GT3_Lazy_Images::instance()->remove_filters();

	}
}

