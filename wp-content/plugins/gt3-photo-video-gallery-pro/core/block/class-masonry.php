<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGalleryPro\Assets;
use GT3\PhotoVideoGalleryPro\Help\Types;
use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Lazy_Images;

class Masonry extends Isotope_Gallery {
	protected $isCategoryEnabled = true;

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'horizontalOrder' => array(
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
				'horizontalOrder' => Types::TYPE_BOOL,
			)
		);
	}

	protected $name = 'masonry';

	protected function getDeprecatedSettings(){
		return array_merge(
			parent::getDeprecatedSettings(),
			array()
		);
	}

	public function register_rest(){
		$namespace = 'gt3/v1';
		register_rest_route(
			$namespace,
			'masonry/load-more',
			array(
				array(
					'methods'  => \WP_REST_Server::CREATABLE,
					'permission_callback' => '__return_true',
					'callback' => array( $this, 'rest_handler' ),
				)
			)
		);
	}

	protected function construct(){
		add_action('rest_api_init', array( $this, 'register_rest' ));
	}

	public function rest_handler(\WP_REST_Request $request){
		ob_start();

		$settings = $request->get_params();

		if(!is_array($settings['ids'])) {
			$settings['ids'] = array();
		}

		$respond            = '';
		$this->render_index = 0;

		$settings                  = $this->checkTypeSettings($settings);
		$settings['lightboxArray'] = array();
		Lazy_Images::instance()->setup_filters();

		foreach($settings['ids'] as $image) {
			foreach($image as $k => $v) {
				$image[$k] = is_array($v) ? $v : stripslashes($v);
			}

			$respond .= $this->renderItem($image, $settings);
		}

		Lazy_Images::instance()->remove_filters();

		$response = array(
			'post_count' => count($settings['ids']),
			'html'       => $respond,
			'items'      => $settings['lightboxArray'],
		);

		$errors = ob_get_clean();

		if(!empty($errors)) {
			$response['errors'] = $errors;
		}

		return rest_ensure_response($response);
	}

	protected function renderItem($image, &$settings){
		$render                      = '';
		$this->active_image_size     = $settings['imageSize'];
		$lightbox_item               = $this->getLightboxItem($image, $settings);
		$settings['lightboxArray'][] = $lightbox_item;

		$wrapper_title = $settings['showTitle'] && !empty($image['title']) ? ' title="'.esc_attr($image['title']).'"' : '';

		$render .= '<div class="gt3pg-isotope-item loading '.$image['item_class'].'" '.$wrapper_title.'><div class="isotope_item-wrapper">';
		if($settings['linkTo'] !== 'none') {
			$link       = '';
			$href_class = '';
			$target     = '';
			switch($settings['linkTo']) {
				case 'post':
					$link = get_permalink($image['id']);
					break;
				case 'lightbox':
					$link       = wp_get_attachment_image_url($image['id'], $settings['imageSize']);
					$href_class = 'gt3pg-lightbox';
					$target     = ' target="_blank"';
					break;
				case 'file':
					$link = wp_get_attachment_image_url($image['id'], $settings['imageSize']);
					break;
			}
			$external_link = get_post_meta($image['id'], 'gt3_external_link_url', true);
			if($external_link) {
				$link       = $external_link;
				$href_class .= ' external-link';
			}
			$render .= '<a href="'.esc_url($link).'" class="'.$href_class.'" '.$target.' data-elementor-open-lightbox="no">';
		}
		$img_wrapper_class = (($settings['showTitle'] && !empty($image['title'])) || ($settings['showCaption'] && !empty($image['caption']))) ? 'has_text_info' : '';
		$render            .= '<div class="img-wrapper '.esc_attr($img_wrapper_class).'">';
		$render            .= $this->wp_get_attachment_image($image['id'], $settings['imageSize']);
		$render            .= '</div>';
		if($settings['linkTo'] !== 'none') {
			$render .= '</a>';
		}
		$render .= '</div>';
		if(($settings['showTitle'] || $settings['showCaption']) && (!empty($image['title']) || !empty($image['caption']))) {
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
		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--isotope_gallery');
		$settings['lazyLoad'] = true;

		if($settings['random']) {
			shuffle($settings['ids']);
		}
		if($settings['search']) {
			$settings['filterEnable'] = false;
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
				$settings['hover'],
				'gallery-'.$this->name,
				$settings['externalVideoThumb'] ? 'video-thumbnails-hidden' : '',
			)
		);
		$this->add_render_attribute(
			'wrapper',
			array(
				'data-cols'        => $settings['columns'],
				'data-cols-tablet' => $settings['columnsTablet'],
				'data-cols-mobile' => $settings['columnsMobile'],
			)
		);
		$dataSettings = array(
			'lightbox'       => array(
				'enable' => $settings['lightbox']
			),
			'id'             => $this->render_index,
			'uid'            => $this->_id,
			'lazyLoad'       => $settings['lazyLoad'],
			'isotopeOptions' => array(
				'masonry' => array(
					'horizontalOrder' => $settings['horizontalOrder'],
				)
			),
		);

		if($settings['lightbox']) {
			Assets::enqueue_script('lightbox');

			if(!in_array($settings['lightboxCapDesc'], array( 'caption', 'description' ))) {
				$settings['lightboxCapDesc'] = 'caption';
			}

			$dataSettings['lightbox']['options'] = array(
				'showTitle'           => $settings['lightboxShowTitle'],
				'showCaption'         => $settings['lightboxShowCaption'],
				'descriptionProperty' => $settings['lightboxCapDesc'],
				'allowDownload'       => $settings['allowDownload'],
				'allowZoom'           => $settings['lightboxAllowZoom'],
				'socials'             => $settings['socials'],
				'deepLink'            => $settings['lightboxDeeplink'],
				'stretchImages'       => $settings['lightboxCover'],
				'thumbnailIndicators' => $settings['lightboxThumbnails'],
				'startSlideshow'      => $settings['lightboxAutoplay'],
				'slideshowInterval'   => $settings['lightboxAutoplayTime']*1000, // s -> ms
				'instance'            => static::$index,
				'customClass'         => 'style-'.$settings['lightboxTheme'],
				'rightClick'          => $settings['rightClick'],
				'externalPosters'     => $settings['externalVideoThumb'],
			);

			if($settings['ytWidth']) {
				$dataSettings['lightbox']['options']['ytWidth'] = true;
			}
		}

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
		if($settings['loadMoreEnable']) {
			$this->add_style(
				'.view_more_link', array(
					'marginTop: %spx' => $settings['margin'],
				)
			);
		}
		if($settings['borderType']) {
			$this->add_style(
				'.isotope_item-wrapper', array(
					'border: %1$spx solid %2$s' => array( $settings['borderSize'], $settings['borderColor'] ),
					'padding: %spx'             => $settings['borderPadding'],
				)
			);

			if($settings['borderType'] === 'rounded') {
				$this->add_style(
					array(
						'.isotope_item-wrapper',
						'.img-wrapper'
					), array( 'border-radius: %spx' => $settings['borderPadding']+$settings['borderSize']+5 )
				);
			}
		}

		$this->add_render_attribute('wrapper', 'class', 'corner-'.$settings['cornersType']);

		$items      = '';
		$foreachIds = $settings['loadMoreEnable']
			? array_slice($settings['ids'], 0, $settings['loadMoreFirst'])
			: $settings['ids'];

		Lazy_Images::instance()->setup_filters();
		foreach($foreachIds as $id) {
			$items .= $this->renderItem($id, $settings);
		}
		Lazy_Images::instance()->remove_filters();

		$dataSettings['items'] = $settings['lightboxArray'];

		if($settings['loadMoreEnable']) {
			$dataSettings['loadMore'] = array(
				'restName'  => 'masonry',
				'enable'    => true,
				'items'     => array_slice($settings['ids'], $settings['loadMoreFirst']),
				'firstLoad' => $settings['loadMoreFirst'],
				'limit'     => $settings['loadMoreLimit'],
				'ajax'      => array(
					'action'             => 'gt3pg_isotope_load_images',
					'_blockName'         => $this->name,
					'source'             => $settings['source'],
					'imageSize'          => $settings['imageSize'],
					'lightbox'           => $settings['lightbox'],
					'lightboxImageSize'  => $settings['lightboxImageSize'],
					'showTitle'          => $settings['showTitle'],
					'linkTo'             => $settings['linkTo'],
					'showCaption'        => $settings['showCaption'],
					'lazyLoad'           => $settings['lazyLoad'],
					'externalVideoThumb' => $settings['externalVideoThumb'],
				)
			);
		}

		$dataSettings['modules'] = array();
		if($settings['lightbox']) {
			array_push($dataSettings['modules'], 'Lightbox');
		}
		if($settings['loadMoreEnable']) {
			array_push($dataSettings['modules'], 'LoadMore');

			$dataSettings['loadMore'] = array(
				'restName'  => 'grid',
				'enable'    => true,
				'items'     => array_slice($settings['ids'], $settings['loadMoreFirst']),
				'firstLoad' => $settings['loadMoreFirst'],
				'limit'     => $settings['loadMoreLimit'],
				'ajax'      => array(
					'source'            => $settings['source'],
					'imageSize'         => $settings['imageSize'],
					'lightbox'          => $settings['lightbox'],
					'lightboxImageSize' => $settings['lightboxImageSize'],
					'showTitle'         => $settings['showTitle'],
					'linkTo'            => $settings['linkTo'],
					'showCaption'       => $settings['showCaption'],
					'lazyLoad'          => $settings['lazyLoad'],
				)
			);
		}

		if($settings['filterEnable']) {
			array_push($dataSettings['modules'], 'Filter');
			$dataSettings['filter'] = array(
				'enable' => true,
				'type'   => 'category',
			);
		}

		if($settings['search']) {
			array_push($dataSettings['modules'], 'Filter');
			$dataSettings['filter'] = array(
				'enable' => true,
				'type'   => 'search',
			);
		}

//		$this->add_render_attribute('wrapper', array(
//			'data-settings' => wp_json_encode($dataSettings)
//		));

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<?php

			if($settings['search']) {
				$this->get_search_form($settings);
			}

			if($settings['filterEnable'] && count($settings['filter_array']) > 1) {
				?>
				<div class="isotope-filter <?php echo $settings['filterShowCount'] ? ' with-counts' : '' ?>">
					<?php
					$this->add_inline_editing_attributes('filterText');
					$this->add_render_attribute(
						'filterText', array(
							'class'       => 'active',
							'href'        => '#',
							'data-filter' => '',
							'data-count'  => $settings['filterCount']['*'],
						)
					);
					echo '<a '.$this->get_render_attribute_string('filterText').'>'.esc_html($settings['filterText']).'</a>';
					ksort($settings['filter_array']);
					foreach($settings['filter_array'] as $cat_slug) {
						echo '<a href="#" data-filter="'.esc_attr($cat_slug['slug']).'" data-count="'.$settings['filterCount'][$cat_slug['slug']].'">'.esc_html($cat_slug['name']).'</a>';
					}
					?>
				</div>
			<?php } ?>
			<div class="gallery-isotope-wrapper">
				<?php
				echo $items; // XSS Ok
				//				$this->getPreloader();
				?>
			</div>
			<script type="application/json" id="settings--<?php echo $this->_id; ?>"><?php echo wp_json_encode($dataSettings) ?></script>
			<?php
			if($settings['loadMoreEnable'] && $settings['loadMoreFirst'] < count($settings['ids'])) {
				$settings['loadMoreButtonText'] = esc_html__(!empty($settings['loadMoreButtonText']) ? $settings['loadMoreButtonText'] : 'More', 'gt3pg_pro');
				$this->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
				$this->add_render_attribute('view_more_button', 'class', 'view_more_link');
				$this->add_inline_editing_attributes('view_more_button');

				echo '<a '.$this->get_render_attribute_string('view_more_button').'>'.esc_html($settings['loadMoreButtonText']).'</a>';
			} // End button
			?>
		</div>
		<?php
		return;
	}
}

