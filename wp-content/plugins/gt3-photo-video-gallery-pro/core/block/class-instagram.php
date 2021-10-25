<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;


class Instagram extends Isotope_Gallery {
	protected $name = 'instagram';

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'source'   => array(
					'type'    => 'string',
					'default' => 'user',
				),
				'userName' => array(
					'type'    => 'string',
					'default' => '',
				),
				'userID'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'tag'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'gridType' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'linkTo'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array()
		);
	}

	protected function construct(){
		$this->add_script_depends('isotope');
		parent::construct();
	}

	protected function render($settings){
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--isotope_gallery');
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--instagram_gallery');

		$settings['lightbox'] = false;
		$settings['lazyLoad'] = false;

		if($settings['rightClick']) {
			$this->add_render_attribute(
				'wrapper', array(
					'oncontextmenu' => 'return false',
					'onselectstart' => 'return false'
				)
			);
		}

		$this->add_render_attribute(
			'wrapper', 'class', array(
				'gt3pg-isotope-gallery',
				'columns-'.$settings['columns'],
				'gallery-'.$this->name,
				'gallery-grid',
			)
		);

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-cols' => $settings['columns'],
			)
		);

		if($settings['loadMoreFirst'] > 12) {
			$settings['loadMoreFirst'] = 12;
		}

		$dataSettings = array(
			'lightbox'  => $settings['lightbox'],
			'id'        => $this->render_index,
			'uid'       => $this->_id,
			'grid_type' => $settings['gridType'],
			'lazyLoad'  => $settings['lazyLoad'],
			'source'    => $settings['source'],
			'userID'    => $settings['userID'],
			'userName'  => $settings['userName'],
			'tag'       => $settings['tag'],
			'limit'     => $settings['loadMoreFirst'],
			'linkTo'    => $settings['linkTo'],
		);

		$this->add_style(
			'.gt3pg-isotope-item', array(
				'padding-right: %spx'  => $settings['margin'],
				'padding-bottom: %spx' => $settings['margin'],
			)
		);
		$this->add_style(
			'.gallery-isotope-wrapper', array(
				'margin-right: -%spx'  => $settings['margin'],
				'margin-bottom: -%spx' => $settings['margin'],
			)
		);

		$this->add_render_attribute('wrapper', 'class', $settings['gridType']);

		$this->add_render_attribute(
			'wrapper', array(
				'data-settings' => wp_json_encode($dataSettings)
			)
		);

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="gallery-isotope-wrapper">
			</div>
			<?php
			$this->getPreloader(true);
			?>
		</div>

		<?php
	}
}
