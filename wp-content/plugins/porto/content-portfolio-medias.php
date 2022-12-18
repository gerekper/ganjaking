<?php
global $porto_settings, $porto_layout;

$portfolio_layout = 'medias';

$portfolio_info = get_post_meta( $post->ID, 'portfolio_info', true );
$portfolio_name = empty( $porto_settings['portfolio-singular-name'] ) ? __( 'Portfolio', 'porto' ) : $porto_settings['portfolio-singular-name'];
$share          = porto_get_meta_value( 'portfolio_share' );
$post_class     = array();
$post_class[]   = 'portfolio-' . $portfolio_layout;
if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>

	<?php if ( ! empty( $porto_settings['portfolio-page-nav'] ) ) : ?>
	<div class="portfolio-title<?php echo ( 'widewidth' === $porto_layout ? ' container m-t-lg' : '' ); ?>">
		<div class="row m-b-xl">
			<div class="portfolio-nav-all col-lg-1">
				<a title="<?php esc_attr_e( 'Back to list', 'porto' ); ?>" data-bs-tooltip href="<?php echo get_post_type_archive_link( 'portfolio' ); ?>"><i class="fas fa-th"></i></a>
			</div>
			<div class="col-lg-10 text-center">
				<h2 class="entry-title shorter"><?php the_title(); ?></h2>
			</div>
			<div class="portfolio-nav col-lg-1">
				<?php previous_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Previous', 'porto' ) . '" class="portfolio-nav-prev"><i class="fa"></i></div>' ); ?>
				<?php next_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Next', 'porto' ) . '" class="portfolio-nav-next"><i class="fa"></i></div>' ); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php porto_render_rich_snippets( false ); ?>

	<?php
	// Portfolio Medias
	$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );

	if ( ! $slideshow_type ) {
		$slideshow_type = 'images';
	}

	$portfolio_link     = get_post_meta( $post->ID, 'portfolio_link', true );
	$show_external_link = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;

	$options                = array();
	$options['themeConfig'] = true;
	if ( 'widewidth' === $porto_layout && is_singular( 'portfolio' ) ) {
		$options['dots']         = 0;
		$options['nav']          = 1;
		$options['stagePadding'] = 0;
	}
	$options = json_encode( $options );

	if ( 'none' != $slideshow_type ) :
		?>
		<?php
		if ( 'images' == $slideshow_type ) :
			$featured_images = porto_get_featured_images();
			$image_count     = count( $featured_images );

			if ( $image_count ) :
				?>
			<div class="portfolio-image<?php echo 1 == $image_count ? ' single' : '', 'widewidth' === $porto_layout && is_singular( 'portfolio' ) ? ' wide' : ''; ?>">
				<div class="portfolio-slideshow porto-carousel owl-carousel<?php echo 'widewidth' === $porto_layout && is_singular( 'portfolio' ) ? ' big-nav' : ''; ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
					<?php
					foreach ( $featured_images as $featured_image ) {
						$attachment = porto_get_attachment( $featured_image['attachment_id'] );
						if ( $attachment ) {
							$placeholder = porto_generate_placeholder( $attachment['width'] . 'x' . $attachment['height'] );
							?>
							<div>
								<div class="img-thumbnail<?php echo 'widewidth' === $porto_layout ? ' img-thumbnail-no-borders' : ''; ?>">
									<?php
										echo wp_get_attachment_image(
											$featured_image['attachment_id'],
											'full',
											false,
											array(
												'class'    => 'owl-lazy img-responsive',
												'data-src' => esc_url( $attachment['src'] ),
												'src'      => porto_is_amp_endpoint() ? esc_url( $attachment['src'] ) : esc_url( $placeholder[0] ),
											)
										);
									?>
									<?php if ( ! empty( $porto_settings['portfolio-zoom'] ) ) : ?>
										<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
									<?php endif; ?>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
				<?php
			endif;
		endif;
		?>

		<?php
		if ( 'video' == $slideshow_type ) {
			$video_code = get_post_meta( $post->ID, 'video_code', true );
			if ( $video_code ) {
				wp_enqueue_script( 'jquery-fitvids' );
				?>
				<div class="portfolio-image single">
					<div class="img-thumbnail fit-video img-thumbnail-no-borders">
						<?php echo do_shortcode( $video_code ); ?>
					</div>
				</div>
				<?php
			}
		}
	endif;
	?>

	<div class="m-t-xl<?php echo ( 'widewidth' === $porto_layout ? ' container' : '' ); ?>">
		<div class="portfolio-info pt-none">
			<ul>
				<?php if ( isset( $porto_settings['portfolio-metas'] ) && in_array( 'like', $porto_settings['portfolio-metas'] ) ) : ?>
					<li>
						<?php echo porto_portfolio_like(); ?>
					</li>
					<?php
				endif;
				if ( isset( $porto_settings['portfolio-metas'] ) && in_array( 'date', $porto_settings['portfolio-metas'] ) ) :
					?>
					<li>
						<i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?>
					</li>
					<?php
				endif;
				$cat_list = get_the_term_list( $post->ID, 'portfolio_cat', '', ', ', '' );
				if ( isset( $porto_settings['portfolio-metas'] ) && in_array( 'cats', $porto_settings['portfolio-metas'] ) && $cat_list ) :
					?>
					<li>
						<i class="fas fa-tags"></i> <?php echo porto_filter_output( $cat_list ); ?>
					</li>
				<?php endif; ?>
				<?php
				if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'portfolio', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
					$post_count = do_shortcode( '[post-views]' );
					if ( $post_count ) :
						?>
						<li>
							<?php echo wp_kses_post( $post_count ); ?>
						</li>
						<?php
					endif;
				}
				?>
			</ul>
		</div>

		<div class="row">
			<div class="<?php echo 'wide-both-sidebar' == $porto_layout || 'both-sidebar' == $porto_layout ? 'col-md-12' : 'col-md-7'; ?> m-t-sm mb-3 mb-md-0">
				<?php
				if ( get_the_content() ) :
					?>
					<?php /* translators: %s: Portfolio name */ ?>
					<h4 class="portfolio-desc m-t-sm"><?php printf( porto_strip_script_tags( __( '%s <strong>Description</strong>', 'porto' ) ), esc_html( $portfolio_name ) ); ?></h4><?php endif; ?>

				<div class="post-content">

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

				<?php if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && ! empty( $porto_settings['portfolio-share'] ) ) ) ) : ?>
					<hr class="tall">
					<div class="share-links-block">
						<h5><?php esc_html_e( 'Share', 'porto' ); ?></h5>
						<?php get_template_part( 'share' ); ?>
					</div>
				<?php endif; ?>

			</div>
			<div class="<?php echo 'wide-both-sidebar' == $porto_layout || 'both-sidebar' == $porto_layout ? 'col-md-12' : 'col-md-5'; ?> m-t-sm">
				<?php
				porto_get_template_part(
					'views/portfolios/meta',
					null,
					array(
						'title_tag'   => 'h4',
						'title_class' => 'm-t-sm',
					)
				)
				?>

				<?php if ( $portfolio_info ) : ?>
					<h4 class="m-t-sm"><?php esc_html_e( 'More Information', 'porto' ); ?></h4>
					<div class="m-b-lg">
						<?php echo do_shortcode( $portfolio_info ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! empty( $porto_settings['portfolio-author'] ) ) : ?>
			<div class="post-gap"></div>
			<div class="post-block post-author clearfix">
				<?php if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) : ?>
					<h4><?php esc_html_e( 'Author', 'porto' ); ?></h4>
				<?php else : ?>
					<h3><i class="fas fa-user"></i><?php esc_html_e( 'Author', 'porto' ); ?></h3>
				<?php endif; ?>
				<div class="img-thumbnail">
					<?php echo get_avatar( get_the_author_meta( 'email' ), '80' ); ?>
				</div>
				<p><strong class="name"><?php the_author_posts_link(); ?></strong></p>
				<p><?php the_author_meta( 'description' ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $porto_settings['portfolio-comments'] ) ) : ?>
			<div class="post-gap"></div>
			<?php
			wp_reset_postdata();
			comments_template();
			?>
		<?php endif; ?>

	</div>

</article>
