<?php
global $porto_settings;

$post_layout = 'full-alt';

$show_date    = isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] );
$show_format  = ! empty( $porto_settings['post-format'] ) && get_post_format();
$post_class   = array();
$post_class[] = 'post-' . $post_layout;
if ( ! ( $show_date || $show_format ) ) {
	$post_class[] = 'hide-post-date';
}

if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>
	<?php
		// Post Media
		$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );
	if ( ! $slideshow_type ) {
		$slideshow_type = 'images';
	}
		porto_get_template_part(
			'views/posts/post-media/' . $slideshow_type,
			null,
			( 'images' == $slideshow_type ? array(
				'image_size' => 'full',
			) : false )
		);
		?>

	<div class="post-content">
		<?php if ( ! empty( $porto_settings['post-title'] ) && isset( $porto_settings['post-replace-pos'] ) && $porto_settings['post-replace-pos'] ) : ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php endif; ?>
		<div>
			<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) : ?>
				<span>
					<?php esc_html_e( 'Posted by: ', 'porto' ); ?>
					<span class="text-color-dark font-weight-semibold"><?php the_author(); ?></span>
				</span>
			<?php endif; ?>

			<?php
			$cats_list = get_the_category_list( ', ' );
			if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-cats m-l-lg"><?php esc_html_e( 'Category: ', 'porto' ); ?> <?php echo porto_filter_output( $cats_list ); ?></span>
			<?php endif; ?>

			<?php
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-tags m-l-lg"><?php esc_html_e( 'Tags: ', 'porto' ); ?> <?php echo porto_filter_output( $tags_list ); ?></span>
			<?php endif; ?>

			<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) : ?>
				<span class="m-l-lg"><?php esc_html_e( 'Comments: ', 'porto' ); ?>
					<?php /* translators: %s: Comments number */ ?>
					<span class="text-color-primary font-weight-semibold"><?php printf( _nx( 'One Comment', '%1$s', get_comments_number(), 'comments title', 'porto' ), number_format_i18n( get_comments_number() ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'like', $porto_settings['post-metas'] ) ) : ?>
				<span class="m-l-lg meta-like">
					<?php echo porto_blog_like(); ?>
				</span>
			<?php endif; ?>

			<?php
			if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
				echo do_shortcode( '[post-views]' );
			}
			?>

			<?php if ( $show_date || $show_format ) : ?>
				<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) : ?>
					<span class="post-date-block m-l-lg">
						<span><?php esc_html_e( 'Post Date: ', 'porto' ); ?></span><span class="font-weight-semibold"><?php echo get_the_date(); ?></span>
					</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<hr class="solid">
		<?php if ( ! empty( $porto_settings['post-title'] ) && ( ! isset( $porto_settings['post-replace-pos'] ) || ! $porto_settings['post-replace-pos'] ) ) : ?>
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

	<div class="post-gap"></div>

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
