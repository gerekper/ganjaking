<?php

global $porto_settings;

$video_code = get_post_meta( get_the_ID(), 'video_code', true );
if ( ! $video_code ) {
	return;
}

wp_enqueue_script( 'jquery-fitvids' );
?>
	<div class="post-image single">
		<div class="img-thumbnail fit-video">
			<?php echo do_shortcode( $video_code ); ?>
		</div>
		<?php if ( is_single() && isset( $porto_settings['post-share-position'] ) && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php elseif ( ! is_single() && isset( $porto_settings['blog-post-share-position'] ) && 'advance' === $porto_settings['blog-post-share-position'] ) : ?>
			<div class="post-block post-share post-share-advance">
				<div class="post-share-advance-bg">
					<?php get_template_part( 'share' ); ?>
					<i class="fa fa-share-alt"></i>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( isset( $extra_html ) ) : ?>
			<?php // @codingStandardsIgnoreLine ?>
			<?php echo porto_filter_output( $extra_html ); ?>
		<?php endif; ?>
	</div>
