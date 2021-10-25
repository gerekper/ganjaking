<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGalleryPro\Help\Types;
use GT3_Post_Type_Gallery;

class Carousel extends Isotope_Gallery {
	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'height'         => array(
					'type'    => 'number',
					'default' => 300,
				),
				'centerMode'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'infinite'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'variableWidth'  => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'fade'           => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'dots'           => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'arrow'          => array(
					'type'    => 'string',
					'default' => 'default',
				),
				/*'slidesToShow'   => array(
					'type'    => 'number',
					'default' => 3,
				),*/
				'slidesToScroll' => array(
					'type'    => 'number',
					'default' => 3,
				),
				'centerPadding'  => array(
					'type'    => 'number',
					'default' => 40,
				),
				'autoplay'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'autoplaySpeed'  => array(
					'type'    => 'number',
					'default' => 2000,
				),
			)
		);
	}

	protected $name = 'carousel';

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
				'height'         => Types::TYPE_INT,
				'centerMode'     => Types::TYPE_BOOL,
				'infinite'       => Types::TYPE_BOOL,
				'variableWidth'  => Types::TYPE_BOOL,
				'fade'           => Types::TYPE_BOOL,
				'dots'           => Types::TYPE_BOOL,
				'arrow'          => Types::TYPE_BOOL,
				'slidesToShow'   => Types::TYPE_INT,
				'slidesToScroll' => Types::TYPE_INT,
				'centerPadding'  => Types::TYPE_INT,
				'autoplay'       => Types::TYPE_BOOL,
				'autoplaySpeed'  => Types::TYPE_INT,
			)
		);
	}

	protected function renderItem($image, &$settings){
		$render                  = '';
		$this->active_image_size = $settings['imageSize'];

		$render .= '<div class="gt3pg-isotope-item '.$image['item_class'].'"><div class="isotope_item-wrapper">';
		$render .= '<div class="img-wrapper ">';
		$render .= $this->wp_get_attachment_image($image['id'], $settings['imageSize']);
		$render .= '</div>';
		$render .= '</div>';
		if(($settings['showTitle'] && !empty($image['title'])) || ($settings['showCaption'] && !empty($image['caption']))) {
			$render .= '<div class="text_info_wrapper">';
			if($settings['showTitle'] && !empty($image['title'])) {
				$render .= '<div class="text_wrap_title">';
				$render .= '<span class="title">'.esc_html($image['title']).'</span>';
				$render .= '</div>';
			}
			if($settings['showCaption'] && !empty($image['caption'])) {
				$render .= '<div class="text_wrap_caption">';
				$render .= '<span class="caption">'.esc_html($image['caption']).'</span>';
				$render .= '</div>';
			}
			$render .= '</div>';
		}
		$render .= '</div>';

		return $render;
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		if(!count($settings['ids'])) {
			return;
		}

		if($settings['imageSize'] === 'thumbnail') {
			$settings['imageSize'] = 'medium_large';
		}

		if(!isset($GLOBALS['gt3pg']) || !is_array($GLOBALS['gt3pg']) ||
		   !isset($GLOBALS['gt3pg']['extension']) || !is_array($GLOBALS['gt3pg']['extension']) ||
		   !isset($GLOBALS['gt3pg']['extension']['pro_optimized'])
		) {
			if($settings['imageSize'] === 'gt3pg_optimized') {
				$settings['imageSize'] = 'large';
			}

		}

		if($settings['rightClick']) {
			$this->add_render_attribute(
				'wrapper', array(
					'oncontextmenu' => 'return false',
					'onselectstart' => 'return false',
				)
			);
		}

		$this->add_render_attribute(
			'wrapper', 'class', array(
				'gallery-'.$this->name,
			)
		);
		$dataSettings = array(
			'id'              => $this->render_index,
			'uid'             => $this->_id,
			'carouselOptions' => array(
				'centerMode'     => $settings['centerMode'],
				'infinite'       => $settings['infinite'],
				'variableWidth'  => $settings['variableWidth'],
//				'fade'           => $settings['fade'],
				'dots'           => $settings['dots'],
				'arrow'          => $settings['arrow'],
//				'slidesToShow'   => $settings['slidesToShow'],
				'slidesToScroll' => $settings['slidesToScroll'],
				'centerPadding'  => $settings['centerPadding'],
				'autoplay'       => $settings['autoplay'],
				'autoplaySpeed'  => $settings['autoplaySpeed'],
			)
		);

		$items      = '';
		$foreachIds = $settings['ids'];

		foreach($foreachIds as $id) {
			$items .= $this->renderItem($id, $settings);
		}

		$this->add_render_attribute(
			'wrapper', array(
				'data-settings' => wp_json_encode($dataSettings),
			)
		);

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="gallery-isotope-wrapper">
				<?php
				echo $items; // XSS Ok
				?>
			</div>
			<?php
			$this->getPreloader();
			?>
		</div>
		<?php
	}
}
