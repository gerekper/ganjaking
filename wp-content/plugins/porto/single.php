<?php get_header(); ?>
<?php
$builder_id = porto_check_builder_condition( 'single' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	wp_reset_postdata();

	global $porto_settings, $porto_layout;
	?>

<div id="content" role="main" class="porto-single-page">

	<?php
	if ( have_posts() ) :
		the_post();
		global $post;
		$post_layout = get_post_meta( $post->ID, 'post_layout', true );
		$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? ( isset( $porto_settings['post-content-layout'] ) ? $porto_settings['post-content-layout'] : 'large' ) : $post_layout;

		if ( 'post' == $post->post_type ) :

			if ( ! empty( $porto_settings['post-backto-blog'] ) ) :
				?>
				<?php /* translators: %s: Post archive name */ ?>
			<a class="inline-block m-b-md" href="<?php echo get_post_type_archive_link( 'post' ); ?>"><i class="fas fa-long-arrow-alt-<?php echo ( is_rtl() ? 'right p-r-xs' : 'left p-l-xs' ); ?>"></i> <?php echo sprintf( esc_html__( 'Back to %s', 'porto' ), porto_title_archive_name( 'post' ) ); ?></a><?php endif; ?>

			<?php get_template_part( 'content', 'post-' . $post_layout ); ?>

			<?php
			if ( ! empty( $porto_settings['post-backto-blog'] ) ) :
				?>
				<?php /* translators: %s: Post archive name */ ?>
			<a class="inline-block m-t-md m-b-md" href="<?php echo get_post_type_archive_link( 'post' ); ?>"><i class="fas fa-long-arrow-alt-<?php echo ( is_rtl() ? 'right p-r-xs' : 'left p-l-xs' ); ?>"></i> <?php echo sprintf( esc_html__( 'Back to %s', 'porto' ), porto_title_archive_name( 'post' ) ); ?></a>
				<?php
			endif;

			if ( isset( $porto_settings['post-related'] ) ? $porto_settings['post-related'] : true ) :
				$related_posts = porto_get_related_posts( $post->ID );
				if ( $related_posts->have_posts() ) :
					$options                = array();
					$options['themeConfig'] = true;
					$post_related_cols      = isset( $porto_settings['post-related-cols'] ) ? $porto_settings['post-related-cols'] : '4';
					$options['lg']          = $post_related_cols;
					if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
						$options['lg']--;
					}
					if ( $options['lg'] < 1 ) {
						$options['lg'] = 1;
					}
					$options['md'] = $post_related_cols - 1;
					if ( $options['md'] < 1 ) {
						$options['md'] = 1;
					}
					$options['sm'] = $post_related_cols - 2;
					if ( $options['sm'] < 1 ) {
						$options['sm'] = 1;
					}
					$options['margin'] = (int) $porto_settings['grid-gutter-width'];

					$carousel_class  = 'post-carousel porto-carousel owl-carousel show-nav-title has-ccols has-ccols-spacing ccols-1';
					$carousel_class .= ' ccols-lg-' . (int) $options['lg'];
					if ( $options['md'] > 1 ) {
						$carousel_class .= ' ccols-md-' . (int) $options['md'];
					}
					if ( $options['sm'] > 1 ) {
						$carousel_class .= ' ccols-sm-' . (int) $options['sm'];
					}

					$options = json_encode( $options );
					?>
					<hr class="tall"/>
					<div class="related-posts">
						<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
						<h4 class="sub-title"><?php printf( esc_html__( 'Related %1$sPosts%2$s', 'porto' ), '<strong>', '</strong>' ); ?></h4>
						<div class="<?php echo esc_attr( $carousel_class); ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
						<?php
						while ( $related_posts->have_posts() ) {
							$related_posts->the_post();

							get_template_part( 'content', 'post-item' );
						}
						?>
						</div>
					</div>
					<?php
				endif;
			endif;
		else :
			?>
			<?php get_template_part( 'content' ); ?>
			<?php
		endif;
	endif;
	?>
</div>

<?php }
 get_footer(); ?>
