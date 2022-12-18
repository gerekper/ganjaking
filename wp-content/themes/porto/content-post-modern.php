<?php
global $porto_settings;

$post_layout = 'modern';

$show_date    = isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] );
$show_format  = ! empty( $porto_settings['post-format'] ) && get_post_format();
$post_class   = array();
$post_class[] = 'post-' . $post_layout;
if ( ! $show_format ) {
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

	<?php if ( $show_date || $show_format ) : ?>
		<div class="post-date">
			<?php
			porto_post_format();
			?>
		</div>
	<?php endif; ?>

	<div class="post-content">
		<?php if ( ! empty( $porto_settings['post-title'] ) ) : ?>
			<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php endif; ?>
		<?php
			porto_render_rich_snippets( false );
		if ( isset( $porto_settings['post-meta-position'] ) && 'after' !== $porto_settings['post-meta-position'] ) {
			get_template_part(
				'views/posts/single/meta',
				null,
				array(
					'hide_icon' => true,
					'hide_by'   => true,
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

			<!-- Post meta after content -->
			<?php
			if ( isset( $porto_settings['post-meta-position'] ) && 'after' === $porto_settings['post-meta-position'] ) {
				get_template_part(
					'views/posts/single/meta',
					null,
					array(
						'hide_icon' => true,
						'hide_by'   => true,
					)
				);
			}
			?>
		</div>

	</div>

	<?php if ( isset( $porto_settings['post-share-position'] ) && 'advance' !== $porto_settings['post-share-position'] ) : ?>
		<?php get_template_part( 'views/posts/single/share' ); ?>
	<?php endif; ?>

	<?php get_template_part( 'views/posts/single/author' ); ?>

	<?php if ( isset( $porto_settings['post-comments'] ) ? $porto_settings['post-comments'] : true ) : ?>
		<?php
		wp_reset_postdata();
		comments_template();
		?>
	<?php endif; ?>

</article>
