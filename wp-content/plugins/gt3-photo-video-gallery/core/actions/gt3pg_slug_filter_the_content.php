<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	function gt3pg_slug_filter_the_content( $content ) {
		global $post;

		if ( 'attachment' == get_post_type() ) {
			wp_enqueue_script( 'blueimp-gallery.js' );
			wp_enqueue_style('blueimp-gallery.css');
			$src                          = get_post_meta( get_the_ID(), 'gt3_video_url', true );
			$iframe                       = '';
			$gallery_json_item            = new stdClass();
			$gallery_json_item->title     = $post->post_title;
			$gallery_json_item->thumbnail = wp_get_attachment_image_url( $post->ID, 'medium' );
			$gallery_json_item->poster    = wp_get_attachment_image_url( $post->ID, 'medium' );
			$gallery_json_item->type      = 'text/html';
			if ( gt3pg_get_video_type_from_description( $src ) != false ) {
				$class      = apply_filters( 'gt3pg_before_render_slider_class_wrap', array(
					'gt3pg_gallery_wrap'         => 'gt3pg_gallery_wrap',
					'gt3pg_wrap_controls'     => 'gt3pg_wrap_controls',
					'gt3_gallery_type_slider' => 'gt3_gallery_type_slider',
				) );
				$video_type = gt3pg_get_video_type_from_description( $src );
				if ( $video_type == 'youtube' ) {
					$gallery_json_item->type     = 'text/html';
					$gallery_json_item->youtube  = gt3pg_get_youtube_id( $src );
					$gallery_json_item->is_video = 1;
				} else if ( $video_type == 'vimeo' ) {
					$gallery_json_item->type     = 'text/html';
					$gallery_json_item->vimeo    = gt3pg_get_vimeo_id( $src );
					$gallery_json_item->is_video = 1;
				}

				$iframe = '<div id="gt3pg_video" class="' . implode( ' ', $class ) . '"><div class="gt3pg_slides"></div></div>';
				$iframe .= '<script>
				var gt3pg_videolinks = [' . json_encode( $gallery_json_item ) . '];
				var gt3pg_videooptions = {
							carousel: true,
							container: "#gt3pg_video",
						};
				jQuery(function($) {
	                var gt3pg_videogallery = blueimp.Gallery(gt3pg_videolinks ,gt3pg_videooptions);
	                $("#gt3pg_video").height(Math.ceil($("#gt3pg_video").width()*0.5625));
	                $(window).on(\'resize\', function () {
						$("#gt3pg_video").height(Math.ceil($("#gt3pg_video").width()*0.5625));
					})
 				});
				</script>';


			}

			return $iframe . $content;
		} else {
			return $content;
		}
	}
