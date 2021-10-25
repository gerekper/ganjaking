<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

class Ribbon extends Basic {
	protected $name = 'ribbon';

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'is_custom'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showTitle'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showCaption'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'moduleHeight'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'itemsPadding'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'controls'       => array(
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
			)
		);
	}

	protected function getUnselectedSettings(){
		return array_merge(
			parent::getUnselectedSettings(),
			array(
				'autoplay'  => 'interval',
				'is_custom' => array(
					'showTitle',
					'showCaption',
					'moduleHeight',
					'itemsPadding',
					'controls',
					'autoplay',
					'interval',
					'transitionTime',
				),
			)
		);
	}

	protected function construct(){
		$this->add_script_depends('imageloaded');
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);
		if(!count($settings['ids'])) {
			return;
		}

		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--ribbon_gallery');

		$autoplay_class = $settings['autoplay'] ? 'autoplay' : 'no_autoplay';
		$title_tag      = 'h5';
		$caption_tag    = 'div';

		$data = array(
			'pad'         => 0,//$settings['itemsPadding'],
			'autoplay'    => $settings['autoplay'],
			'interval'    => $settings['interval']*1000,
			'transition'  => $settings['transitionTime'],
			'height'      => $settings['moduleHeight'],
			'showTitle'   => $settings['showTitle'],
			'showCaption' => $settings['showCaption'],
		);
		$this->add_render_attribute('wrapper', 'class', array(
			'ribbon_slider_wrapper',
			'fadeOnLoad',
			'no_transition',
			'controls-'.($settings['controls'] ? 'on' : 'off'),
		));
		if ($settings['moduleHeight'] === '100%') {
			$this->add_render_attribute('wrapper', 'class', 'full-height');
		}
		if($settings['autoplay']) {
			$this->add_render_attribute('wrapper', 'class', 'start-autoplay');
		}
		$this->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data));

		if(is_array($settings['ids']) && count($settings['ids'])) {
			?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<div class="ribbon_gallery_container ribbon_slider <?php echo esc_attr($autoplay_class); ?>">
					<?php
					$count = 0;

					foreach($settings['ids'] as $key => $image) {
						$count++;
						$photoTitle   = $image['title'];
						$photoCaption = $image['caption'];
						$PCREpattern  = '/\r\n|\r|\n/u';
						$photoCaption = preg_replace($PCREpattern, '', nl2br($photoCaption));
						if($image['height'] < 1) {
							$image['height'] = $image['width'];
						}
						$slide_ratio = round($image['width']/$image['height'], 3);
						?>
						<div
								class="ribbon_slide ribbon_block2preload ribbon_slide<?php echo esc_attr($count); ?>"
								data-count="<?php echo esc_attr($count); ?>"
								data-title="<?php echo esc_attr($photoTitle); ?>"
								data-caption="<?php echo esc_attr($photoCaption); ?>"
								data-ratio="<?php echo esc_attr($slide_ratio); ?>">
							<?php
							echo wp_get_attachment_image($image['id'], 'full', false);
							?>

						</div>
						<?php
					}    //End of Foreach
					?>
				</div>
				<?php
				if((bool) $settings['controls']) {
					?>
					<div class="footer-wrapper">

							<div class="title-wrapper">
								<?php
								if((bool) ($settings['showTitle'])) {
									printf('<%1$s class="ribbon_title"></%1$s>', esc_attr($title_tag));
								}

								if((bool) ($settings['showCaption'])) {
									printf('<%1$s class="ribbon_descr"></%1$s>', esc_attr($caption_tag));
								}
								?>
							</div>

						<div class="status-wrapper">
							<span class="current">
								<?php echo str_pad(1, 2, '0', STR_PAD_LEFT) ?>
							</span>
							<span class="divider">/</span>
							<span class="all_slides">
								<?php echo str_pad($count, 2, '0', STR_PAD_LEFT) ?>
							</span>
						</div>
						<div class="buttons">
							<div class="ribbon_prevSlide ribbon_arrow">
								<div class="gt3_slider_arrow">
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
										 width="284.935px" height="284.936px" viewBox="0 0 284.935 284.936" style="enable-background:new 0 0 284.935 284.936;"
										 xml:space="preserve"><g><path d="M110.488,142.468L222.694,30.264c1.902-1.903,2.854-4.093,2.854-6.567c0-2.474-0.951-4.664-2.854-6.563L208.417,2.857
		C206.513,0.955,204.324,0,201.856,0c-2.475,0-4.664,0.955-6.567,2.857L62.24,135.9c-1.903,1.903-2.852,4.093-2.852,6.567
		c0,2.475,0.949,4.664,2.852,6.567l133.042,133.043c1.906,1.906,4.097,2.857,6.571,2.857c2.471,0,4.66-0.951,6.563-2.857
		l14.277-14.267c1.902-1.903,2.851-4.094,2.851-6.57c0-2.472-0.948-4.661-2.851-6.564L110.488,142.468z"/>
										</g></svg>
								</div>
							</div>
							<?php
							if($settings['autoplay']) {
								echo '<div class="ribbon_autoplay_button"><div class="ribbon_play-pause"></div></div>';
							}
							?>
							<div class="ribbon_nextSlide ribbon_arrow">
								<div class="gt3_slider_arrow">
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
										 width="284.935px" height="284.936px" viewBox="0 0 284.935 284.936" style="enable-background:new 0 0 284.935 284.936;"
										 xml:space="preserve">
<g>
	<path d="M222.701,135.9L89.652,2.857C87.748,0.955,85.557,0,83.084,0c-2.474,0-4.664,0.955-6.567,2.857L62.244,17.133
		c-1.906,1.903-2.855,4.089-2.855,6.567c0,2.478,0.949,4.664,2.855,6.567l112.204,112.204L62.244,254.677
		c-1.906,1.903-2.855,4.093-2.855,6.564c0,2.477,0.949,4.667,2.855,6.57l14.274,14.271c1.903,1.905,4.093,2.854,6.567,2.854
		c2.473,0,4.663-0.951,6.567-2.854l133.042-133.044c1.902-1.902,2.854-4.093,2.854-6.567S224.603,137.807,222.701,135.9z"/>
</g></svg>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
}
