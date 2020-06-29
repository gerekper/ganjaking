<?php

global $porto_settings;

$video_code = get_post_meta( get_the_ID(), 'video_code', true );
if ( ! $video_code ) {
	return;
}
?>
	<div class="post-image single">
		<div class="img-thumbnail fit-video">
			<?php echo do_shortcode( $video_code ); ?>
		</div>
		<?php if ( is_single() && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php endif; ?>

		<?php if ( isset( $extra_html ) ) : ?>
			<?php // @codingStandardsIgnoreLine ?>
			<?php echo porto_filter_output( $extra_html ); ?>
		<?php endif; ?>
	</div>
