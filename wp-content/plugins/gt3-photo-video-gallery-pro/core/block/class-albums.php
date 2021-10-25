<?php

namespace GT3\PhotoVideoGalleryPro\Block;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGalleryPro\Help\Types;
use GT3\PhotoVideoGalleryPro\Settings;
use GT3_Post_Type_Gallery;
use WP_Query;
use GT3\PhotoVideoGalleryPro\Lazy_Images;

class Albums extends Album_Basic {
	protected $isCategoryEnabled = true;

	const dataFormats = array(
		'format_1' => 'F j, Y',
		'format_2' => 'Y-m-d',
		'format_3' => 'm/d/Y',
		'format_4' => 'd/m/Y',
	);

	public static function getDateFormats(){
		return self::dataFormats;
	}

	private $packery_grids = array(
		1 => array(
			'lap'  => 6,
			'grid' => 3,
			'elem' => array(
				1 => array( 'w' => 2, 'h' => 2, ),
				3 => array( 'h' => 2, ),
				4 => array( 'w' => 2, ),
				6 => array( 'w' => 2, ),
			)
		),
		2 => array(
			'lap'  => 8,
			'grid' => 4,
			'elem' => array(
				1 => array( 'w' => 2, 'h' => 2, ),
				4 => array( 'w' => 2, ),
				7 => array( 'w' => 2, 'h' => 2, ),
				8 => array( 'w' => 2, ),
			)
		),
		3 => array(
			'lap'  => 10,
			'grid' => 5,
			'elem' => array(
				2  => array( 'h' => 2, ),
				3  => array( 'w' => 2, ),
				4  => array( 'h' => 2, ),
				6  => array( 'w' => 2, 'h' => 2, ),
				7  => array( 'w' => 2, 'h' => 2, ),
				10 => array( 'w' => 2, ),
			)
		),
		4 => array(
			'lap'  => 12,
			'grid' => 4,
			'elem' => array(
				1  => array( 'w' => 2, ),
				6  => array( 'w' => 2, ),
				7  => array( 'w' => 2, ),
				12 => array( 'w' => 2, ),
			)
		),
	);

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'gridType'           => array(
					'type'    => 'string',
					'default' => 'square',
				),
				'albumType'          => array(
					'type'    => 'string',
					'default' => 'masonry',
				),
				'packery'            => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMeta'           => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMetaTitle'      => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMetaCategory'   => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMetaCount'      => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMetaDate'       => array(
					'type'    => 'string',
					'default' => '1',
				),
				'showMetaDateFormat' => array(
					'type'    => 'string',
					'default' => 'system',
				),
			)
		);
	}

	protected function getCheckTypeSettings(){
		return array_merge(
			parent::getCheckTypeSettings(),
			array(
				'packery'          => Types::TYPE_INT,
				'showMeta'         => Types::TYPE_INT,
				'showMetaTitle'    => Types::TYPE_INT,
				'showMetaCategory' => Types::TYPE_INT,
				'showMetaCount'    => Types::TYPE_INT,
				'showMetaDate'     => Types::TYPE_INT,
			)
		);
	}

	protected function getUnselectedSettings(){
		return array_merge(
			parent::getUnselectedSettings(),
			array(
				'showMeta' => array(
					'showMetaTitle',
					'showMetaCategory',
					'showMetaCount',
					'showMetaDate',
					'showMetaDateFormat',
				),
			)
		);
	}

	protected $name = 'albums';


	/**
	 * @param \WP_Post $album
	 * @param string   $meta
	 *
	 * @return string
	 */
	protected function renderMeta($album, $meta = 'title'){
		$render = '';
		switch($meta) {
			case 'title':
				$render .= '<h5 class="title">'.esc_html($album->post_title).'</h5>';
				break;
			case 'date':
				$format = key_exists($album->dateFormat, self::dataFormats) ? self::dataFormats[$album->dateFormat] : get_option('date_format');
				$date   = get_date_from_gmt($album->post_modified_gmt, $format);
				$render .= '<span class="date">'.esc_html($date).'</span>';
				break;
			case 'category':
				$categories = wp_get_post_terms($album->ID, self::TAXONOMY);
				if(count($categories)) {
					$render .= '<span class="categories">';
					array_walk(
						$categories, function(&$item, $key){
						$item = '<span>'.$item->name.'</span>';
					}
					);
					$render .= join(', ', $categories);
					$render .= '</span>';
				}
				break;
			case 'count':
				$count = get_post_meta($album->ID, sprintf('_cpt_%s_images_count', self::POST_TYPE), true);
				if('' === $count) {
					// Try count from images
					$gallery = GT3_Post_Type_Gallery::get_gallery_images($album->ID);
					if(is_array($gallery) && count($gallery)) {
						$count = count($gallery);
					}
				}

				$render .= '<span class="count">'.esc_html($count).' '.esc_html(_n('photo', 'photos', $count, 'gt3pg_pro')).'</span>';
				break;
		}

		return $render;
	}

	protected function renderItem($album, &$settings){
		$item_class    = '';
		$item_category = '';

		$post_id   = $album->ID;
		$thumbnail = 0;

		if(get_post_thumbnail_id($post_id)) {
			$thumbnail = get_post_thumbnail_id($post_id);
			$thumbnail = wp_prepare_attachment_for_js($thumbnail);
		}
		if(!is_array($thumbnail)) {
			$gallery = GT3_Post_Type_Gallery::get_gallery_images($post_id);
			if(is_array($gallery) && count($gallery)) {
				foreach($gallery as $image) {
					if(is_array($image) && key_exists('id', $image)) {
						$image = $image['id'];
					}
					$thumbnail = wp_prepare_attachment_for_js($image);
					if(!empty($thumbnail)) {
						break;
					}
				}
			}
		}
		if(!is_array($thumbnail)) {
			return '';
		}

		$render                  = '';
		$this->active_image_size = $settings['imageSize'];
//		$lightbox_item_src       = wp_get_attachment_image_src($thumbnail['id'], $settings['lightboxImageSize']);
		if(!isset($thumbnail['sizes']['thumbnail'])) {
			$thumbnail['sizes']['thumbnail'] = $thumbnail['sizes']['full'];
		}

//		$wrapper_title = $settings['showMeta'] && $settings['showMetaTitle'] ? ' title="'.esc_attr($album->post_title).'"' : '';

		$render .= '<div class="gt3pg-isotope-item loading '.$item_class.'"><div class="isotope_item-wrapper">';

		$link       = '';
		$href_class = '';
		$target     = '';

		$link   = get_permalink($post_id);
		$render .= '<a href="'.esc_url($link).'" class="'.$href_class.'" '.$target.' data-elementor-open-lightbox="no">';

		$img_wrapper_class = $settings['showMeta'] ? 'has_text_info' : '';
		$render            .= '<span class="img-wrapper '.esc_attr($img_wrapper_class).'">';
		$render            .= $this->wp_get_attachment_image($thumbnail['id'], $settings['imageSize']);
		$render            .= '</span>';

		if(($settings['showMeta'])) {
			$render .= '<span class="text_info_wrapper">';
			$render .= '<span class="text_wrap_title">';
			if($settings['showMetaTitle']) {
				$render .= $this->renderMeta($album, 'title');
			}
			$render .= '<span class="text_wrap_meta">';
			if($settings['showMetaDate']) {
				$album->dateFormat = $settings['showMetaDateFormat'];
				$render            .= $this->renderMeta($album, 'date');
			}
			if($settings['showMetaCount']) {
				$render .= $this->renderMeta($album, 'count');
			}
			$render .= '</span>';
			$render .= '</span>';
			$render .= '</span>';
		}

		$render .= '</a>';

		$render .= '</div>';

		$render .= '</div>';

		return $render;
	}

	protected function render($settings){

		global $paged;
		global $post;
		if(empty($paged)) {
			$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		}

		$settings['query'] = array_merge(
			array(
				'posts_per_page'      => 12,
				'orderby'             => '',
				'order'               => '',
				'taxonomy'            => array(),
				'tags'                => array(),
				'author__in'          => array(),
				'post__in'            => array(),
				'ignore_sticky_posts' => 0,
			), $settings['query']
		);

		$query_raw             = $settings['query'];
		$query_args            = static::buildQuery($settings['query']);
		$query_args['paged']   = $paged;
		$query_args['exclude'] = array();

		$query_args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS'
			),
			array(
				'key'     => '_cpt_gt3_gallery_images_count',
				'compare' => '>',
				'value'   => '0',
				'type'    => 'NUMBER',
			),
		);
		if(key_exists('taxonomy', $query_raw) && (!is_array($query_raw['taxonomy']) || !count($query_raw['taxonomy']))) {
			$settings['filter_array'] = array();
			$settings['filterEnable'] = false;
		}

		$query = new WP_Query($query_args);
		if(!$query->have_posts()) {
			$query_args['paged'] = 1;
			$query               = new WP_Query($query_args);

			if(!$query->have_posts()) {
				return;
			}
		}

		$this->add_render_attribute('_wrapper', 'class', 'gt3-photo-gallery-pro--isotope_gallery');
		switch($settings['paginationType']) {
			case 'pagination':
				$settings['paginationEnable'] = true;
				$settings['loadMoreEnable']   = false;
				break;
			case 'loadMore':
				$settings['paginationEnable'] = false;
				$settings['loadMoreEnable']   = true;
				break;
			default:
				$settings['paginationEnable'] = false;
				$settings['loadMoreEnable']   = false;
				break;
		};

		if($settings['lazyLoad']) {
			Lazy_Images::instance()->setup_filters();
		}

		if($settings['imageSize'] === 'thumbnail') {
			$settings['imageSize'] = 'medium_large';
		}
		$settings['lightboxArray'] = array();
		$settings['lightbox']      = false;//$settings['linkTo'] === 'lightbox';
		$settings['hover']         = !$settings['lightbox'] ? 'hover-none' : 'hover-default';

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
//			'columns-'.$settings['columns'],
				$settings['hover'],
				'gallery-'.$this->name,
//			$settings['gridType'] === 'circle' ? 'circle' : null,
			)
		);
		$dataSettings = array(
			'lightbox'  => $settings['lightbox'],
			'id'        => $this->render_index,
			'uid'       => $this->_id,
			'grid_type' => $settings['gridType'],
			'lazyLoad'  => $settings['lazyLoad'],
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

		$items = '';

		switch($settings['albumType']) {
			case 'packery':
				if(!key_exists($settings['packery'], $this->packery_grids)) {
					$settings['packery'] = 1;
				}

				$dataSettings['packery'] = $this->packery_grids[$settings['packery']];

				break;
			case 'grid':
				$this->add_render_attribute('wrapper', 'class', $settings['gridType']);
				break;
		}

		if($settings['albumType'] !== 'packery') {

			$this->add_render_attribute(
				'wrapper',
				array(
					'data-cols' => $settings['columns'],
//					'data-cols-tablet' => $settings['columnsTablet'],
//					'data-cols-mobile' => $settings['columnsMobile'],
				)
			);

			$this->add_render_attribute('wrapper', 'class', 'columns-'.$settings['columns']);
		}

		while($query->have_posts()) {
			$query->the_post();
			$items                        .= $this->renderItem($query->post, $settings);
			$query_args['post__not_in'][] = $query->post->ID;
		}

		if($settings['loadMoreEnable']) {
			$load_more_data                   = array(
				'query'    => $query_args,
				'hasMore'  => !!($query->max_num_pages-1),
				'maxPages' => $query->max_num_pages,
				'settings' => array(
					'imageSize'          => $settings['imageSize'],
					'showTitle'          => $settings['showTitle'],
					'lazyLoad'           => $settings['lazyLoad'],
					'showMeta'           => $settings['showMeta'],
					'showMetaTitle'      => $settings['showMetaTitle'],
					'showMetaCategory'   => $settings['showMetaCategory'],
					'showMetaCount'      => $settings['showMetaCount'],
					'showMetaDate'       => $settings['showMetaDate'],
					'showMetaDateFormat' => $settings['showMetaDateFormat'],
				)
			);
			$dataSettings['loadMoreSettings'] = $load_more_data;
		}

		$this->add_render_attribute('_wrapper', 'data-album-type', $settings['albumType']);
		$this->add_render_attribute('wrapper', 'class', 'album-'.$settings['albumType']);

		$this->add_render_attribute(
			'wrapper', array(
				'data-settings' => wp_json_encode($dataSettings)
			)
		);

		?>
		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<?php if((bool) $settings['filterEnable']) { ?>
				<div class="isotope-filter container">
					<?php
					echo '<a href="#" class="active" data-filter="*">'.esc_html__('Show All', 'gt3_themes_core').'</a>';
					foreach($this->get_taxonomy($query_raw['taxonomy']) as $cat_slug) {
						echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'">'.esc_html($cat_slug['name']).'</a>';
					}

					/*foreach($settings['filter_array'] as $cat_slug) {
						echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'" data-count="'.$settings['filterCount'][$cat_slug['slug']].'">'.esc_html($cat_slug['name']).'</a>';
					}*/
					?>
				</div>
			<?php } ?>
			<div class="gallery-isotope-wrapper">
				<?php
				echo $items; // XSS Ok
				?>
			</div>
			<?php
			if($settings['paginationEnable'] && !!($query->max_num_pages-1)) {
				$this->add_style('.pagination', array(
					'margin-top: %spx' => $settings['margin'],
				));
				?>
				<div class="pagination">
					<?php
					echo paginate_links(
						array(
							'base'     => str_replace(PHP_INT_MAX, '%#%', get_pagenum_link(PHP_INT_MAX)),
							'format'   => '?paged=%#%',
							'current'  => max(1, get_query_var('paged')),
							'total'    => $query->max_num_pages,
							'end_size' => 1,
							'mid_size' => 1,
						)
					);
					?>
				</div>
				<?php
			} else if($settings['loadMoreEnable'] && !!($query->max_num_pages-1)) {
				$settings['loadMoreButtonText'] = esc_html__(!empty($settings['loadMoreButtonText']) ? $settings['loadMoreButtonText'] : 'More', 'gt3pg_pro');
				$this->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
				$this->add_render_attribute('view_more_button', 'class', 'view_more_link');
				$this->add_style('.pagination', array(
					'margin-top: %spx' => $settings['margin'],
				));


				echo '<a '.$this->get_render_attribute_string('view_more_button').'>'.esc_html($settings['loadMoreButtonText']).'</a>';
			} // End button
			?>
		</div>
		<?php
		wp_reset_postdata();

		if($settings['lazyLoad']) {
			Lazy_Images::instance()->remove_filters();
		}

		return;
	}
}


