<?php

global $porto_settings;

$post_layout     = 'medium-alt';
$featured_images = porto_get_featured_images();

$post_class   = array();
$post_class[] = 'post post-' . $post_layout;
if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
$post_meta  = '';
$post_meta .= '<div class="post-meta' . ( empty( $porto_settings['post-metas'] ) ? ' d-none' : '' ) . '">';
if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-author">' . esc_html__( 'Posted By ', 'porto' ) . '<span class="text-color-dark font-weight-semibold">' . get_the_author_posts_link() . '</span></span>';
}
	$cats_list = get_the_category_list( ', ' );
if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-cats m-l-lg">' . esc_html__( 'Category: ', 'porto' ) . $cats_list . '</span>';
}
	$tags_list = get_the_tag_list( '', ', ' );
if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-tags m-l-lg">' . esc_html__( 'Tags: ', 'porto' ) . $tags_list . '</span>';
}
if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-comments m-l-lg">' . esc_html__( 'Comments: ', 'porto' ) . '<span class="text-color-primary font-weight-semibold">' . get_comments_popup_link( __( '0', 'porto' ), __( '1', 'porto' ), '%' ) . '</span></span>';
}
if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
	$post_count = do_shortcode( '[post-views]' );
	if ( $post_count ) {
		$post_meta .= $post_count;
	}
}
if ( isset( $porto_settings['post-metas'] ) && in_array( 'like', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-like m-l-lg">' . porto_blog_like() . '</span>';
}
if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) && ! count( $featured_images ) ) {
	$post_meta .= '<span class="meta-date m-l-lg">' . esc_html__( 'Post Date: ', 'porto' ) . '<strong>' . get_the_date( esc_html( $porto_settings['blog-date-format'] ) ) . '</strong></span>';
}
$post_meta .= '</div>';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="row">
	<?php if ( count( $featured_images ) ) : ?>
		<div class="col-md-8 col-lg-5">
			<?php
				// Post Slideshow
				$slideshow_type = get_post_meta( get_the_ID(), 'slideshow_type', true );

			if ( ! $slideshow_type ) {
				$slideshow_type = 'images';
			}
				$args = array();
			if ( 'images' == $slideshow_type ) {
				$args['image_size'] = 'blog-medium';
			}
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) {
				$args['extra_html'] = '<span class="blog-post-date background-color-primary text-color-light font-weight-bold"> ' . esc_html( get_the_date( 'j' ) ) . '<span class="month-year font-weight-light">' . esc_html( get_the_date( 'M-y' ) ) . '</span></span>';
			}
				porto_get_template_part(
					'views/posts/post-media/' . $slideshow_type,
					null,
					$args
				);
			?>
		</div>
		<div class="col-md-12 col-lg-7">
	<?php else : ?>
		<div class="col-lg-12">
	<?php endif; ?>

			<div class="post-content">

				<?php
				if ( is_sticky() && is_home() && ! is_paged() ) {
					printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'porto' ) );
				}
				?>

				<h2 class="entry-title"><?php the_title(); ?></h2>
				<!-- Post meta before content -->
				<?php
				if ( isset( $porto_settings['post-meta-position'] ) && 'before' === $porto_settings['post-meta-position'] ) {
					echo porto_filter_output( $post_meta ) . '<hr class="solid">';}
				?>

				<?php
				porto_render_rich_snippets( false );
				if ( ! empty( $porto_settings['blog-excerpt'] ) ) {
					echo porto_get_excerpt( $porto_settings['blog-excerpt-length'], false );
				} else {
					echo '<div class="entry-content">';
					porto_the_content();
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
					echo '</div>';
				}
				?>

			</div>

			<!-- Post meta after content -->
			<?php
			if ( isset( $porto_settings['post-meta-position'] ) && 'before' !== $porto_settings['post-meta-position'] ) {
				echo '<hr class="solid">' . porto_filter_output( $post_meta );}
			?>

			<div>
				<a class="btn btn-lg btn-borders btn-primary custom-border-radius font-weight-semibold text-uppercase m-t-lg" href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>"><?php esc_html_e( 'Read more', 'porto' ); ?></a>
			</div>

		</div>
	</div>
</article>
