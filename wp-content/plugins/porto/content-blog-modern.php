<?php
global $porto_settings, $page_share;

$post_layout  = 'modern';
$show_format  = ! empty( $porto_settings['post-format'] ) && get_post_format();
$post_class   = array();
$columns      = isset( $porto_settings['grid-columns'] ) ? $porto_settings['grid-columns'] : '3';
$post_class[] = 'post post-' . $post_layout;
if ( ! $show_format ) {
	$post_class[] = 'hide-post-date';
}

if ( ! isset( $image_size ) ) {
	$image_size = 'blog-large';
}

if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}

if ( isset( $el_class ) ) {
	$post_class[] = esc_attr( $el_class );
}

$post_share = get_post_meta( get_the_ID(), 'post_share', true );

$social_share = true;
if ( ! $porto_settings['share-enable'] ) {
	$social_share = false;
} elseif ( isset( $post_share ) && 'no' == $post_share ) {
	$social_share = false;
} elseif ( '' == $page_share && ! $porto_settings['blog-post-share'] ) {
	$social_share = false;
}

$post_meta = '<div class="post-meta">';

if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-date">' . get_the_date( esc_html( $porto_settings['blog-date-format'] ) ) . '</span>';
}

if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-author">' . get_the_author_posts_link() . '</span>';
}

	$cats_list = get_the_category_list( ', ' );
if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-cats">' . $cats_list . '</span>';
}

	$tags_list = get_the_tag_list( '', ', ' );
if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-tags">' . $tags_list . '</span>';
}
if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-comments">' . get_comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ) . '</span>';
}

if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
	$post_count = do_shortcode( '[post-views]' );
	if ( $post_count ) {
		$post_meta .= $post_count;
	}
}

if ( isset( $porto_settings['post-metas'] ) && in_array( 'like', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-like">' . porto_blog_like() . '</span>';
}

$post_meta .= '</div>';

$share = get_post_meta( get_the_ID(), 'post_share', true );

?>

<article <?php post_class( $post_class ); ?>>
<?php
if ( is_sticky() && is_home() && ! is_paged() ) {
	printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'porto' ) );
}
		// Post Slideshow
		$slideshow_type = get_post_meta( get_the_ID(), 'slideshow_type', true );
if ( ! $slideshow_type ) {
	$slideshow_type = 'images';
}
		$args = array();
if ( 'images' == $slideshow_type ) {
	$args['image_size'] = $image_size;
}
		porto_get_template_part( 'views/posts/post-media/' . $slideshow_type, null, $args );
?>

	<?php if ( $show_format ) : ?>
		<div class="post-date">
			<?php
			porto_post_format();
			?>
		</div>
	<?php endif; ?>

	<div class="post-content">

		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php
		// Post meta before content
		if ( isset( $porto_settings['post-meta-position'] ) && 'before' === $porto_settings['post-meta-position'] ) {
			echo porto_filter_output( $post_meta );
		}
		porto_render_rich_snippets( false );
		if ( ! empty( $porto_settings['blog-excerpt'] ) ) {
			echo empty( $porto_settings['post-link'] ) ? '' : '<a href="' . get_the_permalink() . '">';
			echo porto_get_excerpt( $porto_settings['blog-excerpt-length'], false );
			echo empty( $porto_settings['post-link'] ) ? '' : '</a>';
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
		<?php if ( '' === $porto_settings['blog-post-share-position'] && ( 'yes' == $share || ( empty( $share ) && $social_share ) ) ) : ?>
			<div class="post-block post-share">
				<?php get_template_part( 'share' ); ?>
			</div>
		<?php endif; ?>
	</div>

	<!-- Post meta after content -->
	<?php
	if ( isset( $porto_settings['post-meta-position'] ) && 'before' !== $porto_settings['post-meta-position'] ) {
		echo porto_filter_output( $post_meta );}
	?>

	<div class="clearfix">
		<a class="btn-readmore" href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>"><?php esc_html_e( 'Read more +', 'porto' ); ?></a>
	</div>
</article>
