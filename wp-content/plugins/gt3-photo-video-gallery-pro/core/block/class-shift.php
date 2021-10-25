<?php

namespace GT3\PhotoVideoGalleryPro\Block;

use GT3\PhotoVideoGalleryPro\Help\Types;

defined('ABSPATH') OR exit;

class Shift extends Basic {

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'controls'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'infinityScroll' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'autoplay'       => array(
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
				'showTitle'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'expandable'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'hoverEffect'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'moduleHeight'   => array(
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
				'transitionTime' => Types::TYPE_INT,
				'interval'       => Types::TYPE_INT,
				'autoplay'       => Types::TYPE_BOOL,
				'expandable'     => Types::TYPE_BOOL,
				'infinityScroll' => Types::TYPE_BOOL,
			)
		);
	}

	protected $name = 'shift';

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		$this->wrapper_classes = array( 'gt3-photo-gallery-pro--shift_gallery' );
		if(!count($settings['ids'])) {
			return;
		}

		if(!is_numeric($settings['interval']) || empty($settings['interval']) || $settings['interval'] < 1) {
			$settings['interval'] = 6;
		}
		if(!is_numeric($settings['transitionTime']) || empty($settings['transitionTime']) || $settings['transitionTime'] < 300) {
			$settings['transitionTime'] = 300;
		}

		$title_tag   = 'h2';
		$caption_tag = 'div';

		$settings['autoplay'] = empty($settings['infinityScroll']) ? '' : $settings['autoplay'];
		$autoplay_class       = (bool) ($settings['autoplay']) ? 'autoplay' : 'no_autoplay';

		if(is_array($settings['ids']) && count($settings['ids'])) {
			$base_count = 1;
			$even_count = 1;
			$odd_count  = 1;
			$this->add_render_attribute('wrapper', 'class', array(
				'shift-gallery-wrapper',
				esc_attr('controls_'.((bool) $settings['controls'] ? 'on' : 'off')),
//				esc_attr('hover_'.((bool) $settings['hoverEffect'] ? 'on' : 'off')),
			));
			if ($settings['moduleHeight'] === '100%') {
				$this->add_render_attribute('wrapper', 'class', 'full-height');
			}
			$gal_settings = array(
				'autoplay'   => (bool) $settings['autoplay'],
				'interval'   => $settings['interval']*1000,
				'expandable' => (bool) $settings['expandable'],
				'infinity'   => (bool) $settings['infinityScroll'],
				'transition' => $settings['transitionTime'],
				'height'     => esc_attr($settings['moduleHeight']),
			);
			$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode($gal_settings));

			$this->add_render_attribute('container', 'class', array(
				'shift-gallery',
				esc_attr('expandable_'.((bool) $settings['expandable'] ? 'yes' : 'no')),
				esc_attr('title_state_'.$settings['showTitle']),
			));

			$this->add_render_attribute('container', 'data-controls', esc_attr($settings['controls']));
			$this->add_render_attribute('container', 'data-title', esc_attr($settings['showTitle']));
			$this->add_render_attribute('container', 'data-height', esc_attr($settings['moduleHeight']));

			$this->add_style('.shift-gallery-wrapper .shift-gallery .gallery-slide', array(
				'transition-duration: %1$s%2$s, %1$s%2$s' => array( $settings['transitionTime'], 'ms' ),
			));
			?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<div class="controls">
					<a href="javascript:void(0)" class="control--prev">
						<span class="gt3_shift_arrow">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								 width="284.929px" height="284.929px" viewBox="0 0 284.929 284.929" style="enable-background:new 0 0 284.929 284.929;"
								 xml:space="preserve">
<g>
	<path d="M282.082,76.511l-14.274-14.273c-1.902-1.906-4.093-2.856-6.57-2.856c-2.471,0-4.661,0.95-6.563,2.856L142.466,174.441
		L30.262,62.241c-1.903-1.906-4.093-2.856-6.567-2.856c-2.475,0-4.665,0.95-6.567,2.856L2.856,76.515C0.95,78.417,0,80.607,0,83.082
		c0,2.473,0.953,4.663,2.856,6.565l133.043,133.046c1.902,1.903,4.093,2.854,6.567,2.854s4.661-0.951,6.562-2.854L282.082,89.647
		c1.902-1.903,2.847-4.093,2.847-6.565C284.929,80.607,283.984,78.417,282.082,76.511z"/>
</g>
</svg>
						</span>
					</a>
					<a href="javascript:void(0)" class="control--next">
						<span class="gt3_shift_arrow">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								 width="284.929px" height="284.929px" viewBox="0 0 284.929 284.929" style="enable-background:new 0 0 284.929 284.929;"
								 xml:space="preserve">
<g>
	<path d="M282.082,195.285L149.028,62.24c-1.901-1.903-4.088-2.856-6.562-2.856s-4.665,0.953-6.567,2.856L2.856,195.285
		C0.95,197.191,0,199.378,0,201.853c0,2.474,0.953,4.664,2.856,6.566l14.272,14.271c1.903,1.903,4.093,2.854,6.567,2.854
		c2.474,0,4.664-0.951,6.567-2.854l112.204-112.202l112.208,112.209c1.902,1.903,4.093,2.848,6.563,2.848
		c2.478,0,4.668-0.951,6.57-2.848l14.274-14.277c1.902-1.902,2.847-4.093,2.847-6.566
		C284.929,199.378,283.984,197.188,282.082,195.285z"/>
</g></svg>
						</span>
					</a>
				</div>
				<div <?php $this->print_render_attribute_string('container') ?>>
					<?php
					if(is_array($settings['ids']) && count($settings['ids'])) {
						foreach($settings['ids'] as $key => $image) {
							$photoTitle   = $image['title'];
							$photoCaption = $image['caption'];
							$PCREpattern  = '/\r\n|\r|\n/u';
							$photoCaption = preg_replace($PCREpattern, '', nl2br($photoCaption));

							if(($base_count%2) == 0) {
								$slide_class      = 'even_slide'.$even_count;
								$slide_style      = 'even_slide';
								$slide_data_count = $even_count;
								$even_count++;
							} else {
								$slide_class      = 'odd_slide'.$odd_count;
								$slide_style      = 'odd_slide';
								$slide_data_count = $odd_count;
								$odd_count++;
							}
							$base_count++;
							?>
							<div class="gallery-slide block2preload <?php echo esc_attr($slide_class).' '.esc_attr($slide_style); ?>"
							     data-src="<?php echo esc_url($image['url']); ?>">
								<div class="image-background-wrapper">

								</div>
								<div class="gallery-title_wrapper">
									<?php
									printf('<%s class="shift_title">%s</%s>',
										esc_attr($title_tag), wp_kses_post($image['title']), esc_attr($title_tag));

									printf('<%s class="shift_descr">%s</%s>',
										esc_attr($caption_tag), wp_kses_post($photoCaption), esc_attr($caption_tag));
									?>
								</div>
								<?php
								echo $this->wp_get_attachment_image($image['id'], 'full', false, array( 'class' => 'seo_hidden_image' ));
								?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php
		}
	}
}

