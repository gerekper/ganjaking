<?php

namespace GT3\PhotoVideoGallery\Block;

defined('ABSPATH') OR exit;

abstract class Isotope_Gallery extends Basic {

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				// Basic
				'borderColor'   => array(
					'type'    => 'string',
					'default' => '#dddddd',
				),
				'borderPadding' => array(
					'type'    => 'string',
					'default' => '0',
				),
				'borderSize'    => array(
					'type'    => 'string',
					'default' => '1',
				),
				'borderType'    => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'columns'       => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'cornersType'   => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'isMargin'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'margin'        => array(
					'type'    => 'string',
					'default' => '20',
				),
				'linkTo'        => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'lazyLoad'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'imageSize'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showTitle'     => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showCaption'   => array(
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
				'borderColor'          => $this->TYPE_STRING,
				'borderPadding'        => $this->TYPE_INT,
				'borderSize'           => $this->TYPE_INT,
				'columns'              => $this->TYPE_INT,
				'isMargin'             => $this->TYPE_BOOL,
				'margin'               => $this->TYPE_INT,
				'lazyLoad'             => $this->TYPE_BOOL,
				'showTitle'            => $this->TYPE_BOOL,
				'showCaption'          => $this->TYPE_BOOL,
				'ytWidth'              => $this->TYPE_BOOL,
				'lightboxAutoplay'     => $this->TYPE_BOOL,
				'lightboxAutoplayTime' => $this->TYPE_INT,
				'lightboxThumbnails'   => $this->TYPE_BOOL,
				'lightboxCover'        => $this->TYPE_BOOL,
				'lightboxDeeplink'     => $this->TYPE_BOOL,
				'lightboxAllowZoom'    => $this->TYPE_BOOL,
				'socials'              => $this->TYPE_BOOL,
				'allowDownload'        => $this->TYPE_BOOL,
				'lightboxShowTitle'    => $this->TYPE_BOOL,
				'lightboxShowCaption'  => $this->TYPE_BOOL,
				'filterEnable'         => $this->TYPE_BOOL,
				'loadMoreEnable'       => $this->TYPE_BOOL,
				'loadMoreLimit'        => $this->TYPE_INT,
				'loadMoreFirst'        => $this->TYPE_INT,
			)
		);
	}

	protected function getUnselectedAttributes(){
		return array(
			'borderType' => array(
				'borderColor',
				'borderPadding',
				'borderSize',
			),
			'isMargin'   => 'margin',
		);
	}


	protected function render($settings){
		return;
	}

	protected function renderItem($image, &$settings){
		$item_class    = '';
		$item_category = '';

		parent::_renderItem();
		$render                  = '';
		$this->active_image_size = $settings['imageSize'];
		if($settings['lightbox']) {
			$lightbox_item_src = wp_get_attachment_image_src($image['id'], $settings['lightboxImageSize']);
			if(!isset($image['sizes']['thumbnail'])) {
				$image['sizes']['thumbnail'] = $image['sizes']['full'];
			}
			$lightbox_item = array(
				'href'        => $lightbox_item_src[0],
				'title'       => $image['title'],
				'thumbnail'   => $image['sizes']['thumbnail']['url'],
				'description' => $image['caption'],
				'is_video'    => 0,
				'image_id'    => $image['id'],
				'width'       => $lightbox_item_src[1],
				'height'      => $lightbox_item_src[2],
			);

			$tmp_url = get_post_meta($image['id'], 'gt3_video_url', true);
			if($tmp_url != '') {
				$video_type = $this->GetVideoTypeByDescription($tmp_url);
				if($video_type == 'youtube') {
					wp_enqueue_script('youtube_api');
					$item_class                .= ' mfp-iframe youtube';
					$lightbox_item['poster']   = $lightbox_item['href'];
					$lightbox_item['type']     = 'text/html';
					$lightbox_item['youtube']  = $this->GetYoutubeID($tmp_url);
					$lightbox_item['is_video'] = 1;
				} else if($video_type == 'vimeo') {
					wp_enqueue_script('vimeo_api');
					$item_class                .= ' mfp-iframe vimeo';
					$lightbox_item['poster']   = $lightbox_item['href'];
					$lightbox_item['type']     = 'text/html';
					$lightbox_item['vimeo']    = $this->GetVimeoID($tmp_url);
					$lightbox_item['is_video'] = 1;
				}
			}
			$settings['lightboxArray'][] = $lightbox_item;
		}

		$wrapper_title = $settings['showTitle'] && !empty($image['title']) ? ' title="'.esc_attr($image['title']).'"' : '';

		$render .= '<div class="gt3pg-isotope-item loading '.$item_class.'" '.$wrapper_title.'><div class="isotope_item-wrapper">';
		if($settings['linkTo'] !== 'none') {
			$link       = '';
			$href_class = '';
			switch($settings['linkTo']) {
				case 'post':
					$link = get_permalink($image['id']);
					break;
				case 'lightbox':
					$link       = wp_get_attachment_image_url($image['id'], $settings['imageSize']);
					$href_class = 'gt3pg-lightbox';
					break;
				case 'file':
					$link = wp_get_attachment_image_url($image['id'], $settings['imageSize']);
					break;
			}

			$render .= '<a href="'.esc_url($link).'" class="'.$href_class.'" data-elementor-open-lightbox="no">';
		}
		$img_wrapper_class = (($settings['showTitle'] && !empty($image['title'])) || ($settings['showCaption'] && !empty($image['caption']))) ? 'has_text_info' : '';
		$render            .= '<div class="img-wrapper '.esc_attr($img_wrapper_class).'">';
		$render            .= $this->wp_get_attachment_image($image['id'], $settings['imageSize'], false, array(
			'class' => 'skip-lazy'
		));
		$render            .= '</div>';
		if($settings['linkTo'] !== 'none') {
			$render .= '</a>';
		}
		$render .= '</div>';
		if(($settings['showTitle'] && !empty($image['title'])) || ($settings['showCaption'] && !empty($image['caption']))) {
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

}
