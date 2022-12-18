<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_size      = isset( $image_size ) ? $image_size : 'full';
if ( ! isset( $meta_type ) ) {
	$meta_type = '';
}
?>

<?php
if ( count( $featured_images ) ) :
	$attachment_id = $featured_images[0]['attachment_id'];
	$attachment    = porto_get_attachment( $attachment_id, $image_size );
	?>

	<div class="thumb-info thumb-info-no-borders thumb-info-bottom-info<?php echo 'hover_info2' != $post_style ? ' thumb-info-lighten' : ''; ?> thumb-info-bottom-info-dark thumb-info-centered-icons">
		<div class="thumb-info-wrapper">
			<div class="post-image">
				<img width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" src="<?php echo esc_url( $attachment['src'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>" />
			</div>
			<?php if ( 'date' == $meta_type || 'both' == $meta_type ) : ?>
				<div class="post-date"><?php porto_post_date(); ?></div>
			<?php endif; ?>
			<div class="thumb-info-title">
			<?php if ( 'hover_info2' == $post_style ) : ?>
				<?php if ( 'cat' == $meta_type || 'both' == $meta_type ) : ?>
					<span class="thumb-info-type"><?php echo get_the_category_list( ' ' ); ?></span>
				<?php endif; ?>
				<h3 class="thumb-info-inner"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
				<div class="thumb-info-show-more-content">
					<?php echo porto_get_excerpt( $porto_settings['blog-excerpt-length'], false ); ?>
				</div>
			<?php else : ?>
				<h3 class="thumb-info-inner"><?php echo the_title(); ?></h3>
				<?php if ( 'cat' == $meta_type || 'both' == $meta_type ) : ?>
					<span class="thumb-info-type"><?php echo get_the_category_list( ', ' ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			</div>
			<span class="thumb-info-action">
				<a href="<?php the_permalink(); ?>">
					<span class="thumb-info-action-icon thumb-info-action-icon-light"><i class="fas fa-link text-dark"></i></span>
				</a>
			</span>
		</div>
	</div>
<?php endif; ?>
