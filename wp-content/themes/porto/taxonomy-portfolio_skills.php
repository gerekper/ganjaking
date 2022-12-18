<?php get_header(); ?>
<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $portfolio_columns, $wp_query;

	$term    = $wp_query->queried_object;
	$term_id = $term->term_id;

	$portfolio_layout = isset( $porto_settings['portfolio-layout'] ) ? $porto_settings['portfolio-layout'] : 'grid';

	$portfolio_columns = '';
	$portfolio_view    = '';
	if ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ) {
		$portfolio_columns = isset( $porto_settings['portfolio-grid-columns'] ) ? $porto_settings['portfolio-grid-columns'] : '4';
		$portfolio_view    = isset( $porto_settings['portfolio-grid-view'] ) ? $porto_settings['portfolio-grid-view'] : 'default';
	}

	?>

<div id="content" role="main">
	<?php if ( category_description() ) : ?>
		<div class="page-content">
			<?php echo category_description(); ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<?php
		if ( ! empty( $porto_settings['portfolio-archive-link-zoom'] ) ) :
			?>
			<div class="portfolios-lightbox"><?php endif; ?>

		<div class="page-portfolios portfolios-<?php echo esc_attr( $portfolio_layout ); ?> clearfix">

			<?php if ( ! empty( $porto_settings['portfolio-archive-ajax'] ) && empty( $porto_settings['portfolio-archive-ajax-modal'] ) ) : ?>
				<div id="portfolioAjaxBox" class="ajax-box">
					<div class="bounce-loader">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>
					<div class="ajax-box-content" id="portfolioAjaxBoxContent"></div>
				</div>
			<?php endif; ?>

			<?php
			if ( 'timeline' == $portfolio_layout ) :
				global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

				$prev_post_year      = null;
				$prev_post_month     = null;
				$first_timeline_loop = false;
				$post_count          = 1;
				?>

			<section class="timeline">

				<div class="timeline-body portfolios-container">

			<?php else : ?>

			<div class="portfolios-container clearfix<?php echo 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ? ' portfolio-row portfolio-row-' . ( (int) $portfolio_columns ) : '', ' ' . esc_attr( $portfolio_view ); ?>">

			<?php endif; ?>

				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'archive-portfolio-' . $portfolio_layout );
				}
				?>

			<?php if ( 'timeline' == $portfolio_layout ) : ?>
				</div>
			</section>
			<?php else : ?>
			</div>
			<?php endif; ?>

			<?php porto_pagination(); ?>

		</div>

		<?php wp_reset_postdata(); ?>

		<?php
		if ( ! empty( $porto_settings['portfolio-archive-link-zoom'] ) ) :
			?>
			</div><?php endif; ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>

</div>
<?php } ?>
<?php get_footer(); ?>
