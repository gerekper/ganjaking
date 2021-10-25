<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	function gt3pg_get_video_from_description( $post_description ) {
		$arr = array();
		if ( preg_match( '/\[video=(.+)\]/isU', $post_description, $arr ) ) {
			return $arr[1];
		} else {
			return false;
		}
	}

	function gt3pg_get_video_type_from_description( $video_url ) {
		if ( strpos( $video_url, 'youtu' ) !== false ) {
			return 'youtube';
		}
		if ( strpos( $video_url, 'vimeo' ) !== false ) {
			return 'vimeo';
		}

		return false;
	}

	function gt3pg_get_youtube_id( $video_url ) {
		$result = array('','');
		preg_match( "#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})#", $video_url, $result );
		if (is_array($result) && count($result)) {
				return end($result);
		}
		return false;

	}

	function gt3pg_get_vimeo_id( $video_url ) {
		$result = '';
		preg_match( '#([0-9]+)#is', $video_url, $result );

		return $result[1];
	}

	function gt3pg_get_video_type_by_link( $link ) {
		$result = wp_remote_head( $link );
		if ( $result instanceof WP_Error ) {
			return 404;
		} else if ( $result['response']['code'] == 404 ) {
			return 404;
		} else {
			$result = $result['headers']->getAll();

			return $result['content-type'];
		}
	}

	if ( ! function_exists( 'exif_imagetype' ) ) {
		function exif_imagetype( $filename ) {
			$img = getimagesize( $filename );
			if ( ! empty( $img[2] ) ) {
				return $img[2];
			}

			return false;
		}
	}





