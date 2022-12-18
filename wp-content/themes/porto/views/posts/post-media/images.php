<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_count     = count( $featured_images );

if ( ! $image_count ) {
	return;
}
if ( ! isset( $carousel_options ) ) {
	$carousel_options = array();
}
$carousel_options['nav'] = true;
if ( isset( $navigation ) ) {
	if ( '' != $navigation ) {
		$carousel_options['nav'] = true;
	} else {
		$carousel_options['nav'] = false;
	}
}
if ( isset( $pagination ) ) {
	if ( '' != $pagination ) {
		$carousel_options['dots'] = true;
	} else {
		$carousel_options['dots'] = false;
	}
}
$el_class = ! empty( $carousel_class ) ? $carousel_class : ' nav-inside nav-inside-center nav-style-2 show-nav-hover';
if ( ! empty( $hover_effect ) ) {
	$el_class .= ' ' . $hover_effect;
}
if ( ! empty( $show_thumbnail ) ) {
	$el_class .= ' thumb-gallery-detail';
}
?>
<?php echo ( ! empty( $show_thumbnail ) ? '<div class="thumbnail-wrapper">' : '' ); ?>
	<div class="post-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
		<div class="post-slideshow porto-carousel owl-carousel has-ccols ccols-1 <?php echo esc_attr( $el_class ); ?>" data-plugin-options='<?php echo json_encode( $carousel_options ); ?>'>
			<?php
			foreach ( $featured_images as $featured_image ) {
				$attachment_large = porto_get_attachment( $featured_image['attachment_id'], ( isset( $image_size ) ? $image_size : 'blog-large' ) );
				$attachment       = porto_get_attachment( $featured_image['attachment_id'] );
				if ( ! $attachment ) {
					continue;
				}
				$placeholder = porto_generate_placeholder( $attachment_large['width'] . 'x' . $attachment_large['height'] );
				?>
				<?php if ( is_single() ) : ?>
				<div>
			<?php else : ?>
				<a href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>" aria-label="post image">
			<?php endif; ?>
					<div class="img-thumbnail">
						<?php echo wp_get_attachment_image( $featured_image['attachment_id'], ( isset( $image_size ) ? $image_size : 'blog-large' ), false, array( 'class' => 'owl-lazy img-responsive' ) ); ?>
						<?php if ( ! empty( $porto_settings['post-zoom'] ) ) { ?>
							<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment_large['caption'] ); ?>"><i class="fas fa-search"></i></span>
						<?php } ?>
					</div>
				<?php if ( is_single() ) : ?>
				</div>
			<?php else : ?>
				</a>
			<?php endif; ?>
			<?php } ?>
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
	<?php if ( ! empty( $show_thumbnail ) ) : ?>
			<?php
			if ( $image_count >= 1 ) :
				global $post;
				$slider_thumbs_count = get_post_meta( $post->ID, 'slider_thumbs_count', true );
				$slider_thumbs_count = ! empty( $slider_thumbs_count ) ? $slider_thumbs_count : 4;
				$options             = array();
				$options['items']    = $slider_thumbs_count;
				$options['margin']   = isset( $thumbnail_space ) && '' !== $thumbnail_space ? (int) $thumbnail_space : 10;
				$options['nav']      = false;
				$options['dots']     = false;
				$options['loop']     = false;
				if ( $slider_thumbs_count > 4 ) {
					$options['sm'] = 4;
					$options['xs'] = 3;
				}
				$image_thumbnail_size = ! empty( $image_thumbnail_size ) ? $image_thumbnail_size : 'thumbnail';
				?>
				<div class="porto-carousel thumbnail-gallery owl-carousel show-nav-hover has-ccols ccols-<?php echo porto_filter_output( $slider_thumbs_count ); ?>" data-plugin-options="<?php echo esc_attr( json_encode( $options ) ); ?>">
				<?php
				foreach ( $featured_images as $featured_image ) {
					$attachment = porto_get_attachment( $featured_image['attachment_id'], $image_thumbnail_size );
					if ( $attachment ) {
						?>
						<div class="thumb-gallery-thumbs-item">
							<img alt="<?php echo esc_attr( $attachment['alt'] ); ?>" src="<?php echo esc_url( $attachment['src'] ); ?>" class="img-responsive cur-pointer" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>">
						</div>
						<?php
					}
				}
				?>
				</div>
		<?php endif; ?>
	<?php endif; ?>
<?php echo ( ! empty( $show_thumbnail ) ? '</div>' : '' ); ?>
