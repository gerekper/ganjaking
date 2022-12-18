<?php get_header(); ?>
<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $portfolio_columns, $wp_query, $porto_layout, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_thumbs_html;

	$term    = $wp_query->queried_object;
	$term_id = $term->term_id;

	$portfolio_options = get_metadata( $term->taxonomy, $term->term_id, 'portfolio_options', true ) == 'portfolio_options' ? true : false;

	$portfolio_layout   = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_layout', true ) : ( isset( $porto_settings['portfolio-layout'] ) ? $porto_settings['portfolio-layout'] : 'grid' );
	$portfolio_infinite = Porto_Infinite_Scroll::get_instance()->is_infinite();

	$portfolio_columns = '';
	$portfolio_view    = '';
	if ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ) {
		$portfolio_columns = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_grid_columns', true ) : ( isset( $porto_settings['portfolio-grid-columns'] ) ? $porto_settings['portfolio-grid-columns'] : '4' );
		$portfolio_view    = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_grid_view', true ) : ( isset( $porto_settings['portfolio-grid-view'] ) ? $porto_settings['portfolio-grid-view'] : 'default' );
	}

	$porto_portfolio_view        = $portfolio_view;
	$porto_portfolio_thumb       = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_archive_thumb', true ) : ( isset( $porto_settings['portfolio-archive-thumb'] ) ? $porto_settings['portfolio-archive-thumb'] : '' );
	$porto_portfolio_thumb_bg    = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_archive_thumb_bg', true ) : ( isset( $porto_settings['portfolio-archive-thumb-bg'] ) ? $porto_settings['portfolio-archive-thumb-bg'] : 'lighten' );
	$porto_portfolio_thumb_image = $portfolio_options ? get_metadata( $term->taxonomy, $term->term_id, 'portfolio_archive_thumb_image', true ) : ( isset( $porto_settings['portfolio-archive-thumb-image'] ) ? $porto_settings['portfolio-archive-thumb-image'] : '' );
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
			<div class="portfolios-lightbox<?php echo empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) ? '' : ' with-thumbs'; ?>"><?php endif; ?>

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
			if ( isset( $porto_settings['portfolio-cat-sort-pos'] ) && 'hide' !== $porto_settings['portfolio-cat-sort-pos'] ) {
				if ( 'sidebar' === $porto_settings['portfolio-cat-sort-pos'] && ! ( 'widewidth' == $porto_layout || 'fullwidth' == $porto_layout ) ) {
					add_action( 'porto_before_sidebar', 'porto_show_portfolio_tax_filter', 1 );
				} elseif ( 'content' === $porto_settings['portfolio-cat-sort-pos'] ) {
					$portfolio_taxs = array();

					$taxs = get_categories(
						array(
							'taxonomy' => 'portfolio_cat',
							'child_of' => $term_id,
							'orderby'  => isset( $porto_settings['portfolio-cat-orderby'] ) ? $porto_settings['portfolio-cat-orderby'] : 'name',
							'order'    => isset( $porto_settings['portfolio-cat-order'] ) ? $porto_settings['portfolio-cat-order'] : 'asc',
						)
					);

					foreach ( $taxs as $tax ) {
						$portfolio_taxs[ urldecode( $tax->slug ) ] = $tax->name;
					}

					if ( ! $portfolio_infinite ) {
						global $wp_query;
						$posts_portfolio_taxs = array();
						if ( is_array( $wp_query->posts ) && ! empty( $wp_query->posts ) ) {
							foreach ( $wp_query->posts as $post ) {
								$post_taxs = wp_get_post_terms( $post->ID, 'portfolio_cat', array( 'fields' => 'all' ) );
								if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
									foreach ( $post_taxs as $post_tax ) {
										$posts_portfolio_taxs[ urldecode( $post_tax->slug ) ] = $post_tax->name;
									}
								}
							}
						}
						foreach ( $portfolio_taxs as $key => $value ) {
							if ( ! isset( $posts_portfolio_taxs[ $key ] ) ) {
								unset( $portfolio_taxs[ $key ] );
							}
						}
					}

					// Show Filters
					if ( is_array( $portfolio_taxs ) && ! empty( $portfolio_taxs ) ) :
						?>
						<ul class="portfolio-filter nav nav-pills sort-source">
							<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
							<?php foreach ( $portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name ) : ?>
								<li data-filter="<?php echo esc_attr( $portfolio_tax_slug ); ?>"><a href="#"><?php echo esc_html( $portfolio_tax_name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php if ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ) { ?>
							<hr>
						<?php } elseif ( 'timeline' == $portfolio_layout ) { ?>
							<hr class="invisible">
						<?php } else { ?>
							<hr class="tall">
						<?php } ?>
						<?php
					endif;
				}
			}
			?>

			<?php
			if ( 'timeline' == $portfolio_layout ) :
				global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

				$prev_post_year      = null;
				$prev_post_month     = null;
				$first_timeline_loop = false;
				$post_count          = 1;
				?>

			<section class="timeline">

				<div class="timeline-body<?php echo ! $portfolio_infinite ? '' : ' portfolios-container'; ?>">

			<?php else : ?>

			<div class="clearfix<?php echo ! $portfolio_infinite ? '' : ' portfolios-container', 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ? ' portfolio-row portfolio-row-' . ( (int) $portfolio_columns ) . ' ' . esc_attr( $portfolio_view ) : ''; ?>">

			<?php endif; ?>

				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'content', 'archive-portfolio-' . $portfolio_layout );
				}
				?>

				<?php
				if ( ! empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) ) :
					$thumbs_carousel_options = array(
						'items'  => 15,
						'loop'   => false,
						'dots'   => false,
						'nav'    => false,
						'margin' => 8,
					);

					?>
					<div class="porto-portfolios-lighbox-thumbnails">
						<div class="owl-carousel owl-theme nav-center" data-plugin-options='<?php echo json_encode( $thumbs_carousel_options ); ?>'>
							<?php echo porto_filter_output( $porto_portfolio_thumbs_html ); ?>
						</div>
					</div>
				<?php endif; ?>

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

		<?php
	endif;

	$porto_portfolio_view        = '';
	$porto_portfolio_thumb       = '';
	$porto_portfolio_thumb_bg    = '';
	$porto_portfolio_thumb_image = '';
	$porto_portfolio_thumbs_html = '';
	?>

</div>
<?php } ?>
<?php get_footer(); ?>
