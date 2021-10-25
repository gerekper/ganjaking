<?php

namespace GT3\PhotoVideoGalleryPro\Block;

use GT3\PhotoVideoGalleryPro\Help\Types;

defined('ABSPATH') OR exit;

class Kenburns extends Basic {

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'moduleHeight'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'interval'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'transitionTime' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'overlayState'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'overlayBg'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array(
				'interval'       => Types::TYPE_INT,
				'transitionTime' => Types::TYPE_INT,
				'overlayState'   => Types::TYPE_BOOL,
			)
		);
	}

	protected $name = 'kenburns';

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--kenburns_gallery');

		if(!count($settings['ids'])) {
			return;
		}

		$images = array();
		foreach($settings['ids'] as $image) {
			$images[] = $image['url'];
		}

		$this->add_render_attribute('wrapper', 'class', array(
			'gallery_kenburns',
			'fadeOnLoad',
		));
		$dataSettings = array(
			'interval'   => $settings['interval']*1000,
			'transition' => $settings['transitionTime'],
			'height'     => $settings['moduleHeight'],
			'count'      => count($images),
			'images'     => $images,
			'autoplay'   => true,
			'scale'      => 1.1,
		);

		if((bool) ($settings['overlayState'])) {
			$dataSettings['overlayBg'] = $settings['overlayBg'];
		}
		if ($settings['moduleHeight'] === '100%') {
			$this->add_render_attribute('wrapper', 'class', 'full-height');
		}

		$dataSettings = apply_filters('gt3pg-pro/blocks/kenburns/data-settings', $dataSettings, $this, $settings);

		$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode($dataSettings));

		$this->add_style('.gallery_kenburns .kenburn-slide', array(
			'transitionDuration: %1$ss, %2$sms, %1$ss' => array( $settings['interval']*1.3, $settings['transitionTime'] ),
		));

		do_action('gt3pg-pro/blocks/kenburns/before-render', $this, $settings);
		?>

		<div <?php $this->print_render_attribute_string('wrapper') ?>></div>

		<?php
		do_action('gt3pg-pro/blocks/kenburns/after-render', $this, $settings);

	}
}
