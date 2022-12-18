<?php
global $porto_settings;

$post_layout = 'woocommerce';

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
				'carousel_options' => array(
					'dots' => false,
					'nav'  => true,
				),
			) : false )
		);
		?>

	<?php if ( $show_date || $show_format ) : ?>
		<div class="post-date">
			<?php
			porto_post_date();
			porto_post_format();
			?>
		</div>
	<?php endif; ?>

	<div class="post-content clearfix">
		<?php if ( ! empty( $porto_settings['post-title'] ) ) : ?>
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

		<div class="post-meta">
			<?php
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-author"><i class="far fa-user"></i> <?php esc_html_e( 'By', 'porto' ); ?> <?php the_author_posts_link(); ?></span><?php endif; ?>
			<?php
			$cats_list = get_the_category_list( ', ' );
			if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-cats"><i class="far fa-folder"></i> <?php echo porto_filter_output( $cats_list ); ?></span>
			<?php endif; ?>
			<?php
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-tags"><i class="far fa-envelope"></i> <?php echo porto_filter_output( $tags_list ); ?></span>
			<?php endif; ?>
			<?php
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-comments"><i class="far fa-comments"></i> <?php comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ); ?></span><?php endif; ?>

			<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'like', $porto_settings['post-metas'] ) ) : ?>
				<span class="meta-like">
					<?php echo porto_blog_like(); ?>
				</span>
			<?php endif; ?>

			<?php
			if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
				$post_count = do_shortcode( '[post-views]' );
				if ( $post_count ) {
					echo porto_filter_output( $post_count );
				}
			}
			?>

			<?php if ( isset( $porto_settings['post-share-position'] ) && 'advance' !== $porto_settings['post-share-position'] ) : ?>
				<?php
				porto_get_template_part(
					'views/posts/single/share',
					null,
					array(
						'style' => 'inline',
					)
				);
				?>
			<?php endif; ?>
		</div>

	</div>

	<?php porto_get_template_part( 'views/posts/single/author' ); ?>

	<?php if ( isset( $porto_settings['post-comments'] ) ? $porto_settings['post-comments'] : true ) : ?>
		<?php
		wp_reset_postdata();
		comments_template();
		?>
	<?php endif; ?>

</article>
