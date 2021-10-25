<?php

namespace GT3\PhotoVideoGalleryPro\Block;

use GT3\PhotoVideoGalleryPro\Help\Types;

defined('ABSPATH') OR exit;

class Flow extends Basic {
	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'is_custom'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'transitionTime' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'autoplay'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showTitle'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'interval'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'moduleHeight'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'imageRatio'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected function getUnselectedSettings(){
		return array_merge(
			parent::getUnselectedSettings(),
			array(
				'autoplay'  => 'interval',
				'is_custom' => array(
					'imageRatio',
					'showTitle',
					'autoplay',
					'interval',
					'transitionTime',
					'moduleHeight',
				),
			)
		);
	}


	protected $name = 'flow';

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array(
				'transitionTime' => Types::TYPE_INT,
				'autoplay'       => Types::TYPE_BOOL,
				'showTitle'      => Types::TYPE_BOOL,
				'interval'       => Types::TYPE_INT,
				'imageRatio'     => Types::TYPE_STRING,
			)
		);
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--flow_gallery');

		if(!count($settings['ids'])) {
			return;
		}

		if(!is_numeric($settings['transitionTime']) || empty($settings['transitionTime']) || $settings['transitionTime'] < 300) {
			$settings['transitionTime'] = 300;
		}

		$autoplay_class = $settings['autoplay'] ? 'autoplay' : 'no_autoplay';

		if($settings['moduleHeight'] != '100%') {
			$settings['moduleHeight'] = intval($settings['moduleHeight']);
		}

		$this->add_render_attribute('wrapper', 'class', array(
			'flow_slider_wrapper',
			'fadeOnLoad',
			'no_transition',
			$settings['imageRatio'],
		));
		if ($settings['moduleHeight'] === '100%') {
			$this->add_render_attribute('wrapper', 'class', 'full-height');
		}
		$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode(array(
			'autoplay'   => $settings['autoplay'],
			'interval'   => $settings['interval']*1000,
			'height'     => $settings['moduleHeight'],
			'imageRatio' => $settings['imageRatio'],
			'imgWidth'   => '4x3' === $settings['imageRatio'] ? 8000 : 16000,
			'imgHeight'  => '4x3' === $settings['imageRatio'] ? 6000 : 9000,
		)));
		if($settings['autoplay']) {
			$this->add_style('.flow_slide', array(
				'transitionDuration: %3$sms, %2$sms, %1$sms, %1$sms' => array( $settings['transitionTime'], floor($settings['transitionTime']*.9), 400 ),
			));
		}
		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="flow_gallery_container flow_slider wait4load_first wait4load wait4load2 <?php echo esc_attr($autoplay_class); ?>">
				<?php
				$count = 1;
				foreach($settings['ids'] as $key => $image) {
					$photoCaption = $image['caption'];
					$PCREpattern  = '/\r\n|\r|\n/u';
					$photoCaption = preg_replace($PCREpattern, '', nl2br($photoCaption));
					?>
					<div
							class="flow_slide flow_block2preload flow_slide<?php echo esc_attr($count); ?>"
							data-count="<?php echo esc_attr($count); ?>"
							data-title="<?php echo esc_attr($image['title']); ?>"
							data-descr="<?php echo esc_attr($photoCaption); ?>">
						<?php
						echo wp_get_attachment_image($image['id'], 'full', false, array(
							'class' => 'seo_hidden_image skip-lazy'
						));
						?>
					</div>
					<?php
					$count++;
				}   //End of Foreach
				?>
			</div>
			<?php if($settings['showTitle']) { ?>
				<div class="flow_title_content"></div>
			<?php } ?>

			<div class="control-prev flow_arrow">
				<div class="theme_icon-arrows-left"></div>
			</div>
			<div class="control-next flow_arrow">
				<div class="theme_icon-arrows-right"></div>
			</div>
		</div>
		<?php
	}
}

