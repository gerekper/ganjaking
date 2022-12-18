<?php
/**
 * Product Video Thumbnail
 *
 * Display video instead of thumbnail images
 * 
 * @since 6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Video_Thumbnail' ) ) :

	class Porto_Video_Thumbnail {
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			add_filter( 'porto_single_product_after_thumbnails', array( $this, 'printVideoThumbnails' ) );
		}

		/**
		 * Load assets for video thumbnails
		 */
		public function enqueue_scripts() {
			wp_register_script( 'porto-video-thumbnail', PORTO_LIB_URI . '/video-thumbnail/video-thumbnail.min.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}

		/**
		 * Print video for product thumbnails.
		 */
		public function printVideoThumbnails() {
			ob_start();
			$featured = get_the_post_thumbnail_url(); 

			// from library
			$ids = get_post_meta( get_the_ID(), 'porto_product_video_thumbnails' );
			if ( ! empty( $ids ) ) {
				wp_enqueue_script( 'jquery-fitvids' );
				wp_enqueue_script( 'porto-video-thumbnail' );

				foreach ( $ids as $id ) {
					$url = wp_get_attachment_url( $id );
					$poster = get_the_post_thumbnail_url( $id ) ? get_the_post_thumbnail_url( $id ) : $featured;
					?>

					<div class="img-thumbnail">
						<a href="#" class="porto-video-thumbnail-viewer"><img src="<?php echo esc_url( $poster ); ?>" alt="poster image"></a>
						<script type="text/template" class="porto-video-thumbnail-data">
							<figure class="post-media fit-video">
								<?php echo do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $poster ) . '"]' ); ?>
							</figure>
						</script>
					</div>

					<?php
				}
			}

			// with video thumbnail shortcode
			$video_code = get_post_meta( get_the_ID(), 'porto_product_video_thumbnail_shortcode', true );
			$video_html = '';
			if ( false !== strpos( $video_code, '[video src="' ) ) {
				wp_enqueue_script( 'jquery-fitvids' );
				wp_enqueue_script( 'porto-video-thumbnail' );

				preg_match( '/poster="([^\"]*)"/', $video_code, $poster );
				$poster = empty( $poster ) ? $featured : $poster[1];
				$video_html = do_shortcode( preg_replace( '/poster="([^\"]*)"/', '', $video_code ) );
			} else {
				$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
				if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
					$youtube_id = $matches[1];
				} else {
					$youtube_id = '';
				}
				if ( ! $youtube_id ) {
					$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
					if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
						$vimeo_id = $matches[1];
					} else {
						$vimeo_id = '';
					}
				}
				$poster = $featured;
			}

			if ( $video_html ) {
				wp_enqueue_script( 'jquery-fitvids' );
				wp_enqueue_script( 'porto-video-thumbnail' );
				?>
				<div class="img-thumbnail">
					<a href="<?php echo esc_url( $video_code ); ?>" class="porto-video-thumbnail-viewer popup-<?php echo ! empty( $youtube_id ) ? 'youtube' : 'vimeo'; ?>"><img src="<?php echo esc_url( $poster ); ?>"></a>
					<script type="text/template" class="porto-video-thumbnail-data">
						<figure class="post-media fit-video">
						<?php echo porto_strip_script_tags( $video_html ); ?>
						</figure>
					</script>
				</div>
				<?php
			} elseif ( ! empty( $youtube_id ) || ! empty( $vimeo_id ) ) {
				?>
				<div class="img-thumbnail">
					<a href="<?php echo esc_url( $video_code ); ?>" class="porto-video-thumbnail-viewer popup-<?php echo ! empty( $youtube_id ) ? 'youtube' : 'vimeo'; ?>"><img src="<?php echo esc_url( $poster ); ?>"></a>
				</div>
				<?php
			}
			return ob_get_clean();
		}
	}
endif;

new Porto_Video_Thumbnail;
