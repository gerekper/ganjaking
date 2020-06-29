<?php
global $porto_settings;

$post_layout     = 'medium';
$featured_images = porto_get_featured_images();

$post_class   = array();
$post_class[] = 'post-' . $post_layout;

if ( 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>

	<div class="row">
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
			<div class="col-lg-5">
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
			<div class="col-lg-7">
		<?php else : ?>
			<div class="col-lg-12">
		<?php endif; ?>

			<div class="post-content">
				<?php if ( $porto_settings['post-title'] ) : ?>
					<h2 class="entry-title"><?php the_title(); ?></h2>
				<?php endif; ?>
				<?php porto_render_rich_snippets( false ); ?>
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
		</div>
	</div>

	<div class="post-gap-small"></div>

	<div class="post-meta">
		<?php
		if ( in_array( 'date', $porto_settings['post-metas'] ) ) :
			?>
			<span class="meta-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span><?php endif; ?>
		<?php
		if ( in_array( 'author', $porto_settings['post-metas'] ) ) :
			?>
			<span class="meta-author"><i class="far fa-user"></i> <?php esc_html_e( 'By', 'porto' ); ?> <?php the_author_posts_link(); ?></span><?php endif; ?>
		<?php
		$cats_list = get_the_category_list( ', ' );
		if ( $cats_list && in_array( 'cats', $porto_settings['post-metas'] ) ) :
			?>
			<span class="meta-cats"><i class="far fa-folder"></i> <?php echo porto_filter_output( $cats_list ); ?></span>
		<?php endif; ?>
		<?php
		$tags_list = get_the_tag_list( '', ', ' );
		if ( $tags_list && in_array( 'tags', $porto_settings['post-metas'] ) ) :
			?>
			<span class="meta-tags"><i class="far fa-envelope"></i> <?php echo porto_filter_output( $tags_list ); ?></span>
		<?php endif; ?>
		<?php
		if ( in_array( 'comments', $porto_settings['post-metas'] ) ) :
			?>
			<span class="meta-comments"><i class="far fa-comments"></i> <?php comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ); ?></span><?php endif; ?>			<?php if ( in_array( 'like', $porto_settings['post-metas'] ) ) : ?>
			<span class="meta-like">
				<?php echo porto_blog_like(); ?>
			</span>
		<?php endif; ?>

		<?php
		if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
			echo do_shortcode( '[post-views]' );
		}
		?>
	</div>

	<?php if ( 'advance' !== $porto_settings['post-share-position'] ) : ?>
		<?php get_template_part( 'views/posts/single/share' ); ?>
	<?php endif; ?>

	<?php get_template_part( 'views/posts/single/author' ); ?>

	<?php if ( $porto_settings['post-comments'] ) : ?>
		<div class="post-gap-small"></div>
		<?php
		wp_reset_postdata();
		comments_template();
		?>
	<?php endif; ?>

</article>
