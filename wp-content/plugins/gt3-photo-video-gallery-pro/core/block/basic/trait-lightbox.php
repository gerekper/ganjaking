<?php

namespace GT3\PhotoVideoGalleryPro\Block\Basic;
defined('ABSPATH') OR exit;

trait Lightbox_Trait {
	protected function get_video_type_from_description($video_url){
		if(strpos($video_url, 'youtu') !== false) {
			return 'youtube';
		}
		if(strpos($video_url, 'vimeo') !== false) {
			return 'vimeo';
		}

		return 'hosted';
	}

	protected function get_youtube_id($video_url){
		$result = array( '', '' );
		preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})#", $video_url, $result);
		if(is_array($result) && count($result)) {
			return end($result);
		}

		return false;
	}

	protected function get_vimeo_id($video_url){
		$result = array( 0, 0 );
		preg_match('#([0-9]+)#is', $video_url, $result);

		return $result[1];
	}

	protected function get_video_type_by_link($link){
		$result = wp_remote_head($link);
		if($result instanceof \WP_Error) {
			return 404;
		} else if($result['response']['code'] == 404) {
			return 404;
		} else {
			$result = $result['headers']->getAll();

			if($result['content-type'] === 'video/quicktime') {
				$result['content-type'] = 'video/mp4';
			}

			return $result['content-type'];
		}
	}

	protected function is_allowed_video($src){
		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		$ext = call_user_func($strtolower_function, substr(strrchr($src, '.'), 1));

		return in_array($ext, array( 'm4v', 'mp4', 'webm', 'ogg', 'ogv', 'mov' ));
	}

	protected function has_local_video($local_video){
		$video = false;
		$type  = key_exists('video_type', $local_video) ? $local_video['video_type'] : '';
		switch($type) {
			case 'hosted':
				if(!$local_video['hosted_id'] || !$this->is_allowed_video($local_video['hosted_url'])) {
					break;
				}
				$type = $this->get_video_type_by_link($local_video['hosted_url']);

				$video = ($type != 404) ? array(
					'type' => $type,
					'url'  => $local_video['hosted_url']
				) : false;
				break;
			case 'youtube':
				$video = !(empty($local_video['youtube_url'])) ? array(
					'type' => 'youtube',
					'url'  => mb_strlen($local_video['youtube_url']) == 11 ? $local_video['youtube_url'] : $this->get_youtube_id($local_video['youtube_url'])
				) : false;
				break;
			case 'vimeo':
				$video = !(empty($local_video['vimeo_url'])) ? array(
					'type' => 'vimeo',
					'url'  => mb_strlen($local_video['vimeo_url']) == 8 ? intval($local_video['vimeo_url']) : $this->get_vimeo_id($local_video['vimeo_url']),
				) : false;

				break;
		}

		return $video;
	}

	protected function has_global_video($video_url_global){
		$video      = false;
		$video_type = is_string($video_url_global) ? $this->get_video_type_from_description($video_url_global) : '';

		switch($video_type) {
			case 'youtube':
				$video = !(empty($video_url_global)) ? array(
					'type' => 'youtube',
					'url'  => mb_strlen($video_url_global) == 11 ? $video_url_global : $this->get_youtube_id($video_url_global)
				) : false;
				break;
			case 'vimeo':
				$video = !empty($video_url_global) ? array(
					'type' => 'vimeo',
					'url'  => (mb_strlen($video_url_global) >= 7 && mb_strlen($video_url_global) <= 10) ? intval($video_url_global) : $this->get_vimeo_id($video_url_global),
				) : false;
				break;
			case 'hosted':
				if(!$this->is_allowed_video($video_url_global)) {
					break;
				}
				$type  = $this->get_video_type_by_link($video_url_global);
				$video = ($type != 404) ? array(
					'type' => $type,
					'url'  => $video_url_global
				) : false;
				break;
		}

		return $video;
	}


	protected function getLightboxItem(&$image, &$settings){
		$lightbox_item_src = wp_get_attachment_image_src($image['id'], $settings['lightboxImageSize']);
		if(!isset($image['sizes']['thumbnail'])) {
			$image['sizes']['thumbnail'] = $image['sizes']['full'];
		}

		$lightbox_item = array(
			'href'            => $lightbox_item_src[0],
			'title'           => $image['title'],
			'caption'         => $image['caption'],
			'description'     => $image['description'],
			'alt'             => $image['alt'],
			'thumbnail'       => $image['sizes']['thumbnail']['url'],
			'is_video'        => 0,
			'image_id'        => $image['id'],
			'width'           => $image['width'],
			'height'          => $image['height'],
			'item_class_list' => $image['item_class_list'],
		);

		$video_url_global = get_post_meta($image['id'], 'gt3_video_url', true);
		$video_url_global = (is_string($video_url_global) && !empty(trim($video_url_global))) ? $video_url_global : '';

		$local_video = key_exists('videoLink', $image) ? $image['videoLink'] : array();

		$video = $this->has_local_video($local_video);

		if(!$video) {
			$video = $this->has_global_video($video_url_global);
		}
		if($video) {
			switch($video['type']) {
				case 'youtube':
					$this->add_script_depends('youtube_api');
					$image['item_class']     .= ' mfp-iframe youtube';
					$lightbox_item['poster'] = $lightbox_item['href'];
					if(!$settings['externalVideoThumb']) {
						$lightbox_item['thumbnail'] = $lightbox_item['href'];
						$lightbox_item['poster']    = $lightbox_item['href'];
					}
					$lightbox_item['type']     = 'text/html';
					$lightbox_item['youtube']  = $video['url'];
					$lightbox_item['is_video'] = 1;
					break;
				case 'vimeo':
					$this->add_script_depends('vimeo_api');
					$image['item_class'] .= ' mfp-iframe vimeo';
					if(!$settings['externalVideoThumb']) {
						$lightbox_item['thumbnail'] = $lightbox_item['href'];
						$lightbox_item['poster']    = $lightbox_item['href'];
					}
					$lightbox_item['poster']   = $lightbox_item['href'];
					$lightbox_item['type']     = 'text/html';
					$lightbox_item['vimeo']    = $video['url'];
					$lightbox_item['is_video'] = 1;
					break;
				default:
					$image['item_class']       .= ' mfp-iframe self-hosted';
					$lightbox_item['poster']   = $lightbox_item['href'];
					$lightbox_item['href']     = $video['url'];
					$lightbox_item['type']     = $video['type'];
					$lightbox_item['is_video'] = 1;
					break;
			}
		}

		return $lightbox_item;
	}

}
