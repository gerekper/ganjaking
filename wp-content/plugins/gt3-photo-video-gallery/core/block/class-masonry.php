<?php
namespace GT3\PhotoVideoGallery\Block;

defined('ABSPATH') OR exit;
use GT3\PhotoVideoGallery\Assets;
use GT3\PhotoVideoGallery\Lazy_Images;

class Masonry extends Isotope_Gallery {
	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array()
		);
	}

	protected $slug = 'gt3pg-pro/masonry';
	protected $name = 'masonry';

	protected function getDeprecatedSettings(){
		return array_merge(
			parent::getDeprecatedSettings(),
			array()
		);
	}

	protected function render($settings){
		$this->checkImagesNoEmpty($settings);

		if(!count($settings['ids'])) {
			return;
		}
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--isotope_gallery');

		if($settings['random']) {
			shuffle($settings['ids']);
		}

		if($settings['imageSize'] === 'thumbnail') {
			$settings['imageSize'] = 'medium_large';
		}
		$settings['lightboxArray'] = array();
		$settings['lightbox']      = $settings['linkTo'] === 'lightbox';
		$settings['hover']         = !$settings['lightbox'] ? 'hover-none' : 'hover-default';

		if(!isset($GLOBALS['gt3pg']) || !is_array($GLOBALS['gt3pg']) ||
		   !isset($GLOBALS['gt3pg']['extension']) || !is_array($GLOBALS['gt3pg']['extension']) ||
		   !isset($GLOBALS['gt3pg']['extension']['pro_optimized'])
		) {
			if($settings['lightboxImageSize'] === 'gt3pg_optimized') {
				$settings['lightboxImageSize'] = 'large';
			}

			if($settings['imageSize'] === 'gt3pg_optimized') {
				$settings['imageSize'] = 'large';
			}

		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-cols'        => $settings['columns'],
//				'data-cols-tablet' => $settings['columnsTablet'],
//				'data-cols-mobile' => $settings['columnsMobile'],
			)
		);

		$this->add_render_attribute('wrapper', 'class', array(
			'gt3pg-isotope-gallery',
			'columns-'.$settings['columns'],
			$settings['hover'],
			'gallery-'.$settings['_blockName'],
		));
		$dataSettings = array(
			'lightbox' => $settings['lightbox'],
			'id'       => $this->render_index,
			'uid'      => $this->_id,
		);

		$this->add_style('.gt3pg-isotope-item', array(
			'padding-right: %spx'  => $settings['margin'],
			'padding-bottom: %spx' => $settings['margin'],
		));
		$this->add_style('.gallery-isotope-wrapper', array(
			'margin-right: -%spx'  => $settings['margin'],
			'margin-bottom: -%spx' => $settings['margin'],
		));

		if($settings['borderType']) {
			$this->add_style('.isotope_item-wrapper', array(
				'border: %1$spx solid %2$s' => array( $settings['borderSize'], $settings['borderColor'] ),
				'padding: %spx'             => $settings['borderPadding'],
			));

			if($settings['borderType'] === 'rounded') {
				$this->add_style(array(
					'.isotope_item-wrapper',
					'.img-wrapper'
				), array( 'border-radius: %spx' => $settings['borderPadding']+$settings['borderSize']+5 ));
			}
		}

		$this->add_render_attribute('wrapper', 'class', 'corner-'.$settings['cornersType']);

		$items      = '';
		$foreachIds = $settings['ids'];

		Lazy_Images::instance()->setup_filters();
		foreach($foreachIds as $id) {
			$items .= $this->renderItem($id, $settings);
		}
		Lazy_Images::instance()->remove_filters();

		$dataSettings['modules'] = array();
		if($settings['lightbox']) {
			Assets::enqueue_script('lightbox');
			$dataSettings['lightboxArray'] = $settings['lightboxArray'];
			array_push($dataSettings['modules'], 'Lightbox');

			$dataSettings['lightbox'] = array(
				'enable'  => true,
				'options' => array(
					'instance'            => static::$index,
				)
			);
			$dataSettings['items'] = $settings['lightboxArray'];
		}

		$this->add_render_attribute('wrapper', array(
			'data-settings' => wp_json_encode($dataSettings)
		));

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div class="gallery-isotope-wrapper">
				<?php
				echo $items; // XSS Ok
				?>
			</div>
		</div>
		<script type="application/json" id="settings--<?php echo $this->_id; ?>"><?php echo wp_json_encode($dataSettings) ?></script>
		<?php
		return;
	}
}

