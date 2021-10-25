<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

class Stripe extends Basic {

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'moduleHeight' => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}


	protected $name = 'stripe';

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		$this->wrapper_classes = array( 'gt3-photo-gallery-pro--stripe_gallery' );
		if(!count($settings['ids'])) {
			return;
		}

		if(is_array($settings['ids']) && count($settings['ids'])) {
			$this->add_render_attribute('wrapper', 'class', array(
				'gt3_stripes_wrapper',
				'stripe_gallery_container',
			));

			if ($settings['moduleHeight'] === '100%') {
				$this->add_render_attribute('wrapper', 'class', 'full-height');
			}

			$gal_settings = array(
//				'autoplay'   => (bool) $settings['autoplay'],
//				'interval'   => $settings['interval'],
//				'expandable' => (bool) $settings['expandable'],
//				'infinity'   => (bool) $settings['infinityScroll'],
//				'transition' => $settings['transitionTime'],
				'height' => esc_attr($settings['moduleHeight']),
			);
			$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode($gal_settings));
			?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<div class="gt3_stripes">
					<?php
					foreach($settings['ids'] as $key => $image) {
						$url = $image['url'];

						$photoTitle   = $image['title'];
						$photoCaption = $image['caption'];
						$PCREpattern  = '/\r\n|\r|\n/u';
						$photoCaption = preg_replace($PCREpattern, '', nl2br($photoCaption));
						?>
						<div class="gt3_stripe_slide block2preload" data-src="<?php echo esc_url($url); ?>">
							<div class="gt3_stripe_content">
								<h2 class="gts_stripe_title"><?php echo esc_html($photoTitle); ?></h2>
								<div class="gts_stripe_descr"><?php echo esc_html($photoCaption); ?></div>
							</div>
							<span class="gt3_plus_icon"></span>
							<div class="gt3_stripe_overlay"></div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}
}

