<?php get_header(); ?>

<?php
$post_type = ( isset( $_GET['post_type'] ) && $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : null;
if ( isset( $post_type ) && locate_template( 'archive-' . $post_type . '.php' ) ) {
	get_template_part( 'archive', $post_type );
	exit;
}

$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
	get_footer();
	exit;
}
global $porto_settings;
$post_layout = isset( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'large';
?>

	<div id="content" role="main">

		<?php if ( have_posts() ) : ?>

			<?php
			if ( 'timeline' == $post_layout ) {
				global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

				$prev_post_year      = null;
				$prev_post_month     = null;
				$first_timeline_loop = false;
				$post_count          = 1;
				?>

				<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php
				if ( ! empty( $porto_settings['post-style'] ) ) {
					echo 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); }
				?>
				">
				<section class="timeline">
				<div class="timeline-body posts-container">

			<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>

				<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php
				if ( ! empty( $porto_settings['post-style'] ) ) {
					echo 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); }
				?>
				">
				<div class="row posts-container">

			<?php } else { ?>

				<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> posts-container">

			<?php } ?>

			<?php
			while ( have_posts() ) {
				the_post();

				get_template_part( 'content', 'blog-' . $post_layout );
			}
			?>

			<?php if ( 'timeline' == $post_layout ) { ?>

				</div>
				</section>

			<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>

				</div>

			<?php } else { ?>

			<?php } ?>

			<?php porto_pagination(); ?>
			</div>

			<?php wp_reset_postdata(); ?>

		<?php else : ?>

			<h2 class="entry-title m-b"><?php esc_html_e( 'Nothing Found', 'porto' ); ?></h2>

			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<?php /* translators: %s: Admin post add page url */ ?>
				<p class="alert alert-info"><?php printf( porto_strip_script_tags( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'porto' ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<p class="alert alert-info"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'porto' ); ?></p>
				<?php get_search_form(); ?>

			<?php else : ?>

				<p class="alert alert-info"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'porto' ); ?></p>
				<?php get_search_form(); ?>

			<?php endif; ?>

		<?php endif; ?>
	</div>

<?php get_footer(); ?>
