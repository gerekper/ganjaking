<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

class Before_After extends Basic {
	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'image_before' => array(
					'type'    => 'string',
					'default' => '',
				),
				'image_after'  => array(
					'type'    => 'string',
					'default' => '',
				),
			)
		);
	}

	protected function getUnselectedSettings(){
		return array_merge(
			parent::getUnselectedSettings(),
			array()
		);
	}

	protected $name = 'before-after';

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array()
		);
	}

	protected function render($settings){
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--before_after');

		$settings['image_before'] = $settings['image_before']['url'];
		$settings['image_after'] = $settings['image_after']['url'];

		$this->add_render_attribute('wrapper', 'class','before-after');
		$this->add_render_attribute('wrapper', 'data-settings',json_encode(array(
				'a' => 0
		)));

		?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<div class="beforeAfter_wrapper" style="background-image:url(<?php echo $settings['image_after'] ?>)">
					<img src="<?php echo $settings['image_before'] ?>" class="img_before" alt="">
					<div class="after_wrapper" style="background-image:url(<?php echo $settings['image_before'] ?>); width: 50%;" ></div>
					<div class="result_line" style="left: 50%"></div>
				</div>
			</div>
		<?php
	}
}

