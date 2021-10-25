<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Controls;

defined('ABSPATH') OR exit;

use Elementor\Base_Data_Control;

class Gallery extends Base_Data_Control {

	const TYPE = 'gt3pg-pro--gallery';

	public function get_type(){
		return self::TYPE;
	}

	protected function get_default_settings(){
		return array(
			'withCustomVideoLink' => false,
		);
	}

	public function get_default_value(){
		return '';
	}

	public function get_value($control, $widget){

		if(isset($widget[$control['name']]) && !empty($widget[$control['name']])) {
			$images = json_decode($widget[$control['name']], true);
			if(!json_last_error() && is_array($images)) {

			} else {
				$images = explode(',', $widget[$control['name']]);
			}
		} else {
			$images = array();
		}

		return $images;
	}

	public function content_template(){
		?>
		<div class="gt3-control-field">
			<div class="gt3-control-title">{{{ data.label }}}</div>
			<div class="gt3-control-input-wrapper">
				<# if ( data.description ) { #>
				<div class="gt3-control-field-description">{{{ data.description }}}</div>
				<# } #>
				<div class="gt3-control-media__content gt3-control-tag-area">
					<div class="gt3-controls">
						<a class="gt3-add-media gt3-control-gallery-add"><?php echo esc_html__("+ Add Media", 'gt3pg_pro'); ?></a>
						<a class="gt3-clear-media button page-title-action gt3-control-gallery-clear disabled"><?php echo esc_html__("Clear Gallery", 'gt3pg_pro'); ?></a>
						<span class="gt3-media-status gt3-control-gallery-status-title"></span>
					</div>
				</div>
				<div class="gt3-control-gallery-content">
					<div class="gt3-control-gallery-thumbnails"></div>
				</div>
			</div>
		</div>
		<?php
	}
}


