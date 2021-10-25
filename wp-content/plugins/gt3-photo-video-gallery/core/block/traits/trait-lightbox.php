<?php

namespace GT3\PhotoVideoGallery\Block\Traits;
defined('ABSPATH') OR exit;

trait Lightbox_Trait {
	protected function GetVideoTypeByDescription($video_url){
		if(false !== strpos($video_url, 'youtu')) {
			return 'youtube';
		}
		if(false !== strpos($video_url, 'vimeo')) {
			return 'vimeo';
		}

		return false;
	}

	protected function GetYoutubeID($video_url){
		$result = array( '', '' );
		preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})#", $video_url, $result);
		if(is_array($result) && count($result)) {
			return end($result);
		}

		return false;

	}

	protected function GetVimeoID($video_url){
		$result = '';
		preg_match('#([0-9]+)#is', $video_url, $result);

		return $result[1];
	}

	protected function getVideoTypeByLink($link){
		$result = wp_remote_head($link);
		if($result instanceof \WP_Error) {
			return 404;
		} else if($result['response']['code'] == 404) {
			return 404;
		} else {
			$result = $result['headers']->getAll();

			return $result['content-type'];
		}
	}

	protected function isAllowedVideo($src){
		return false;
	}
}
