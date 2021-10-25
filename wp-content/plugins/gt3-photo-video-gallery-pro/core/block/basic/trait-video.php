<?php

namespace GT3\PhotoVideoGalleryPro\Block\Basic;
defined('ABSPATH') OR exit;

trait Video_Trait {
	protected function getVideoThumbnail_Youtube($id){

	}

	protected function getVideoThumbnail_Vimeo($id){
		$cache = get_transient(sprintf('gt3_video_thumbnail__Vimeo_%1$s', (int) $id));
		if(false !== $cache) {
			return $cache;
		}
		$url    = sprintf('https://vimeo.com/api/v2/video/%1$s.json', (int) $id);
		$remote = wp_remote_get($url);
		if(!is_wp_error($remote)) {
			$remote = $remote['body'];
			try {
				$remote = json_decode($remote, true);
				$remote = $remote[0];
				if(key_exists('thumbnail_large', $remote) && !empty($remote['thumbnail_large'])) {
					set_transient(sprintf('gt3_video_thumbnail__Vimeo_%1$s', (int) $id), $remote['thumbnail_large'], WEEK_IN_SECONDS);

					return $remote['thumbnail_large'];
				}
			} catch(\Exception $ex) {
			}
		}

		return false;
	}
}
