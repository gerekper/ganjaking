<?php
global $porto_settings;

$post_layout  = 'medium';
$post_class   = array();
$post_class[] = 'post-' . $post_layout;

if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>

	<?php
	// Post Slideshow
	$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );
	$video_code     = get_post_meta( $post->ID, 'video_code', true );
	if ( ! $slideshow_type ) {
		$slideshow_type = 'images';
	}

	$featured_images = porto_get_featured_images();
	$image_count     = count( $featured_images );

	if ( ( 'video' == $slideshow_type && $video_code ) || $image_count ) :
		?>
	<div class="post-media">
		<?php
			// Post Media
			porto_get_template_part(
				'views/posts/post-media/' . $slideshow_type,
				null,
				( 'images' == $slideshow_type ? array(
					'image_size' => 'blog-medium',
				) : false )
			);
		?>
	</div>
	<?php endif; ?>

	<div class="post-content">
		<?php if ( ! empty( $porto_settings['post-title'] ) ) : ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php endif; ?>
		<?php porto_render_rich_snippets( false ); ?>
		<?php
		if ( isset( $porto_settings['post-meta-position'] ) && 'before' === $porto_settings['post-meta-position'] ) {
			get_template_part(
				'views/posts/single/meta',
				null,
				array(
					'show_date' => true,
				)
			);
		}
		?>
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'porto' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'porto' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				)
			);
			?>
		</div>

	</div>

	<div class="post-gap-small clearfix"></div>

	<?php
	if ( ( isset( $porto_settings['post-meta-position'] ) && 'after' === $porto_settings['post-meta-position'] ) || empty( $porto_settings['post-meta-position'] ) ) {
		get_template_part(
			'views/posts/single/meta',
			null,
			array(
				'show_date' => true,
			)
		);
	}
	?>

	<?php if ( isset( $porto_settings['post-share-position'] ) && 'advance' !== $porto_settings['post-share-position'] ) : ?>
		<?php get_template_part( 'views/posts/single/share' ); ?>
	<?php endif; ?>

	<?php get_template_part( 'views/posts/single/author' ); ?>

	<?php if ( isset( $porto_settings['post-comments'] ) ? $porto_settings['post-comments'] : true ) : ?>
		<div class="post-gap-small"></div>
		<?php
		wp_reset_postdata();
		comments_template();
		?>
	<?php endif; ?>

</article>
