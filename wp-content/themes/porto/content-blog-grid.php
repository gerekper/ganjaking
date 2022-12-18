<?php
global $porto_settings, $page_share;

$post_layout = 'grid';

$columns = isset( $porto_settings['grid-columns'] ) ? $porto_settings['grid-columns'] : '3';

global $porto_blog_columns, $porto_post_style;
if ( $porto_blog_columns ) {
	$columns = $porto_blog_columns;
}

$post_style   = $porto_post_style ? $porto_post_style : ( isset( $porto_settings['post-style'] ) ? $porto_settings['post-style'] : 'default' );
$post_class   = array();
$post_class[] = 'post post-' . $post_layout;

if ( ! isset( $image_size ) ) {
	if ( 'widget' == $post_style ) {
		$image_size = 'widget-thumb-medium';
	} elseif ( 'list' == $post_style || 'grid' == $post_style ) {
		$image_size = 'related-post';
	} elseif ( porto_is_wide_layout() ) {
		if ( (int) $columns >= 5 ) {
			$image_size = 'blog-grid-small';
		} elseif ( (int) $columns >= 3 ) {
			$image_size = 'blog-grid';
		} else {
			$image_size = 'blog-large';
		}
	} else {
		if ( (int) $columns >= 3 ) {
			$image_size = 'blog-grid-small';
		} elseif ( 1 === (int) $columns ) {
			$image_size = 'blog-large';
		} else {
			$image_size = 'blog-grid';
		}
	}
}

$post_class[] = porto_grid_post_column_class( $columns );
if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}

$post_share = get_post_meta( get_the_ID(), 'post_share', true );

$social_share = true;
if ( ! $porto_settings['share-enable'] ) {
	$social_share = false;
} elseif ( isset( $post_share ) && 'no' == $post_share ) {
	$social_share = false;
} elseif ( '' == $page_share && empty( $porto_settings['blog-post-share'] ) ) {
	$social_share = false;
}

$post_meta = '';

if ( 'date' != $post_style && isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<div class="post-meta"><span class="meta-date"><i class="far fa-calendar-alt"></i>' . get_the_date( esc_html( $porto_settings['blog-date-format'] ) ) . '</span></div>';
} $post_meta .= '<div class="post-meta">';

if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-author"><i class="far fa-user"></i>' . esc_html__( 'By ', 'porto' ) . get_the_author_posts_link() . '</span>';
}

	$cats_list = get_the_category_list( ', ' );
if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-cats"><i class="far fa-folder"></i>' . $cats_list . '</span>';
}

	$tags_list = get_the_tag_list( '', ', ' );
if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-tags"><i class="far fa-envelope"></i>' . $tags_list . '</span>';
}
if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) {
	$post_meta .= '<span class="meta-comments"><i class="far fa-comments"></i>' . get_comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ) . '</span>';
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


if ( 'grid' == $post_style || 'list' == $post_style || 'widget' == $post_style ) {
	$args = array(
		'image_size' => $image_size,
		'meta_first' => true,
	);
	if ( isset( $meta_type ) && 'cat' == $meta_type ) {
		$args['show_cats'] = true;
	}
	$classes = implode( ' ', get_post_class( $post_class ) );
	if ( 'grid' == $post_style || 'list' == $post_style ) {
		$classes .= ' blog-post-item post-item-' . $post_style;
	} else {
		$classes .= ' blog-post-item post-item-small';
	}
	if ( 'grid' == $post_style || ( isset( $meta_type ) && 'cat' == $meta_type ) ) {
		$args['title_tag'] = 'h4';
	}
	$args['post_item_class'] = $classes;
	porto_get_template_part(
		'content',
		'post-item-small',
		$args
	);
	return;
} elseif ( 'author' == $post_style ) {
	$args = array(
		'image_size'     => $image_size,
		'excerpt_length' => $porto_settings['blog-excerpt-length'],
		'post_view'      => 'style-7',
	);
	?>
	<article <?php post_class( $post_class ); ?>>
		<?php
		porto_get_template_part(
			'content',
			'post-item',
			$args
		);
		?>
	</article>
	<?php
	return;
}
?>

<article <?php post_class( $post_class ); ?>>
<?php
if ( is_sticky() && is_home() && ! is_paged() ) {
	printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'porto' ) );
}
if ( 'related' == $post_style ) :
	get_template_part( 'content', 'post-item' );
elseif ( 'hover_info' == $post_style || 'hover_info2' == $post_style ) :
	$args = array(
		'image_size' => $image_size,
		'post_style' => $post_style,
	);
	if ( isset( $meta_type ) ) {
		$args['meta_type'] = $meta_type;
	}
	porto_get_template_part(
		'content',
		'post-item-simple',
		$args
	);
else :
	?>
	<div class="grid-box">
		<?php
			// Post Slideshow
			$slideshow_type = get_post_meta( get_the_ID(), 'slideshow_type', true );
		if ( ! $slideshow_type ) {
			$slideshow_type = 'images';
		}
			$args = array();
		if ( 'images' == $slideshow_type ) {
			$args['image_size'] = $image_size;
		}
		if ( 'date' == $post_style ) {
			$args['extra_html'] = '<div class="post-date">' . porto_post_date( false ) . '</div>';
		}
			porto_get_template_part( 'views/posts/post-media/' . $slideshow_type, null, $args );
		?>

		<!-- Post meta before content -->
		<?php
		if ( isset( $porto_settings['post-meta-position'] ) && 'before' === $porto_settings['post-meta-position'] ) {
			echo porto_filter_output( $post_meta );}
		?>
		<div class="post-content">

			<h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<?php
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
			<?php if ( isset( $porto_settings['blog-post-share-position'] ) && '' === $porto_settings['blog-post-share-position'] && ( 'yes' == $share || ( empty( $share ) && $social_share ) ) ) : ?>
				<div class="post-block post-share">
					<?php get_template_part( 'share' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Post meta after content -->
		<?php
		if ( isset( $porto_settings['post-meta-position'] ) && 'before' !== $porto_settings['post-meta-position'] ) {
			echo porto_filter_output( $post_meta );
		}
		?>
		<div class="clearfix">
			<a class="btn btn-xs btn-default text-xs text-uppercase btn-readmore" href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>"><?php esc_html_e( 'Read more...', 'porto' ); ?></a>
		</div>
	</div>
<?php endif; ?>
</article>
