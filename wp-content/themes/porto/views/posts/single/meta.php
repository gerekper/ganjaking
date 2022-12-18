<?php
	global $porto_settings;
	$hide_icon = isset( $args ) && ! empty( $args['hide_icon'] );
	$show_date = isset( $args ) && ! empty( $args['show_date'] );
?>

<div class="post-meta <?php echo ( ! empty( $args['el_class'] ) ) ? $args['el_class'] : ''; ?>">
	<?php if ( $show_date && isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) : ?>
		<span class="meta-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
	<?php endif; ?>
	<?php
	if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) :
		?>
		<span class="meta-author">
		<?php if ( ! $hide_icon ) : ?>
			<i class="far fa-user"></i>
		<?php endif; ?>
		<?php if ( ! isset( $args ) || empty( $args['hide_by'] ) ) : ?>
			<span><?php esc_html_e( 'By', 'porto' ); ?></span>
		<?php endif; ?>
			<?php the_author_posts_link(); ?>
		</span>
		<?php
	endif;
	$cats_list = get_the_category_list( ', ' );
	if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) :
		?>
		<span class="meta-cats">
		<?php if ( ! $hide_icon ) : ?>
			<i class="far fa-folder"></i>
		<?php endif; ?>
			<?php echo porto_filter_output( $cats_list ); ?>
		</span>
	<?php endif; ?>
	<?php
	$tags_list = get_the_tag_list( '', ', ' );
	if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) :
		?>
		<span class="meta-tags">
		<?php if ( ! $hide_icon ) : ?>
			<i class="far fa-envelope"></i>
		<?php endif; ?>
			<?php echo porto_filter_output( $tags_list ); ?>
		</span>
	<?php endif; ?>
	<?php
	if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) :
		?>
		<span class="meta-comments">
		<?php if ( ! $hide_icon ) : ?>
			<i class="far fa-comments"></i>
		<?php endif; ?>
			<?php comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ); ?>
		</span>
	<?php endif; ?>

	<?php if ( isset( $porto_settings['post-metas'] ) && in_array( 'like', $porto_settings['post-metas'] ) ) : ?>
		<span class="meta-like">
			<?php echo porto_blog_like( true ); ?>
		</span>
	<?php endif; ?>

	<?php
	if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
		echo do_shortcode( '[post-views]' );
	}
	?>
</div>
