<?php get_header(); ?>

<div class="full-width">
</div>
<?php
wp_reset_postdata();

global $porto_settings, $porto_layout;

// check portfolio ajax modal
if ( porto_is_ajax() && isset( $_POST['ajax_action'] ) && 'portfolio_ajax_modal' == $_POST['ajax_action'] ) {
	$porto_settings['portfolio-zoom'] = false;
}

add_action( 'porto_after_main', 'porto_display_related_portfolios' );
?>

	<div id="content" role="main">

		<?php
		if ( have_posts() ) :
			the_post();
			$portfolio_layout = get_post_meta( $post->ID, 'portfolio_layout', true );
			$portfolio_layout = ( 'default' == $portfolio_layout || ! $portfolio_layout ) ? $porto_settings['portfolio-content-layout'] : $portfolio_layout;
			?>

			<?php get_template_part( 'content', 'portfolio-' . $portfolio_layout ); ?>

		<?php endif; ?>
	</div>
<?php get_footer(); ?>
