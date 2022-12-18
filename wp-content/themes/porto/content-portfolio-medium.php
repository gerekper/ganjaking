<?php

global $porto_settings, $porto_layout;

$portfolio_layout = 'medium';

$portfolio_name = empty( $porto_settings['portfolio-singular-name'] ) ? __( 'Portfolio', 'porto' ) : $porto_settings['portfolio-singular-name'];
$portfolio_info = get_post_meta( $post->ID, 'portfolio_info', true );

$share = porto_get_meta_value( 'portfolio_share' );

$post_class   = array();
$post_class[] = 'portfolio-' . $portfolio_layout;
if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>

	<?php if ( ! empty( $porto_settings['portfolio-page-nav'] ) ) : ?>
	<div class="portfolio-title<?php echo ( 'widewidth' === $porto_layout ? ' container m-t-lg' : '' ); ?>">
		<div class="row">
			<div class="portfolio-nav-all col-lg-1">
				<a title="<?php esc_attr_e( 'Back to list', 'porto' ); ?>" data-bs-tooltip href="<?php echo get_post_type_archive_link( 'portfolio' ); ?>"><i class="fas fa-th"></i></a>
			</div>
			<div class="col-lg-10 text-center">
				<h2 class="entry-title shorter"><?php the_title(); ?></h2>
			</div>
			<div class="portfolio-nav col-lg-1">
				<?php
				previous_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Previous', 'porto' ) . '" class="portfolio-nav-prev"><i class="fa"></i></div>' );
				next_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Next', 'porto' ) . '" class="portfolio-nav-next"><i class="fa"></i></div>' );
				?>
			</div>
		</div>
	</div>
	<hr class="<?php echo ( 'widewidth' === $porto_layout ? 'm-t-xl m-b-none solid' : 'tall' ); ?>">
	<?php endif; ?>

	<?php porto_render_rich_snippets( false ); ?>

	<div class="row<?php echo ( 'widewidth' === $porto_layout ? ' m-t-lg' : '' ); ?>">
		<?php
		// Portfolio Slideshow
		$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );
		$video_code     = get_post_meta( $post->ID, 'video_code', true );

		if ( ! $slideshow_type ) {
			$slideshow_type = 'images';
		}

		$featured_images    = porto_get_featured_images();
		$image_count        = count( $featured_images );
		$show_external_link = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;

		if ( ( 'images' == $slideshow_type && $image_count ) || ( 'video' == $slideshow_type && $video_code ) ) :
			?>
		<div class="col-md-4">
			<?php
			if ( 'images' == $slideshow_type && $image_count ) :
				$slider_type         = get_post_meta( $post->ID, 'slider_type', true );
				$slider_thumbs_count = get_post_meta( $post->ID, 'slider_thumbs_count', true );
				if ( ! $slider_type ) {
					$slider_type         = isset( $porto_settings['portfolio-slider'] ) ? $porto_settings['portfolio-slider'] : 'without-thumbs';
					$slider_thumbs_count = isset( $porto_settings['portfolio-slider-thumbs-count'] ) ? $porto_settings['portfolio-slider-thumbs-count'] : '4';
				}
				if ( ! $slider_thumbs_count ) {
					$slider_thumbs_count = isset( $porto_settings['portfolio-slider-thumbs-count'] ) ? $porto_settings['portfolio-slider-thumbs-count'] : '4';
				}
				?>

				<div class="portfolio-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
					<?php
					if ( 'with-thumbs' == $slider_type ) :
						$lightbox         = false;
						$lightbox_options = '';
						if ( ! empty( $porto_settings['portfolio-zoom'] ) ) {
							$lightbox                               = true;
							$lightbox_options                       = array();
							$lightbox_options['delegate']           = 'a';
							$lightbox_options['type']               = 'image';
							$lightbox_options['gallery']            = array();
							$lightbox_options['gallery']['enabled'] = true;
							$lightbox_options                       = json_encode( $lightbox_options );
						}

						$options           = array();
						$options['items']  = 1;
						$options['margin'] = 10;
						$options['nav']    = true;
						$options['dots']   = false;
						$options['loop']   = false;
						$options           = json_encode( $options );
						?>

						<div class="thumb-gallery<?php echo ! $lightbox ? '' : ' lightbox'; ?>"<?php echo ! $lightbox ? '' : 'data-plugin-options="' . esc_attr( $lightbox_options ) . '"'; ?>>
							<div class="porto-carousel thumb-gallery-detail owl-carousel show-nav-hover" data-plugin-options="<?php echo esc_attr( $options ); ?>">
								<?php
								foreach ( $featured_images as $featured_image ) {
									$attachment_medium = wp_get_attachment_image_src( $featured_image['attachment_id'], 'blog-masonry' );
									if ( $attachment_medium ) {
										$placeholder = porto_generate_placeholder( $attachment_medium[1] . 'x' . $attachment_medium[2] );
										?>
										<div class="thumb-gallery-item">
											<?php
											if ( $lightbox ) :
												$attachment = porto_get_attachment( $featured_image['attachment_id'] );
												?>
												<a href="<?php echo esc_url( $attachment['src'] ); ?>" title="<?php echo esc_attr( $attachment['alt'] ); ?>"><?php endif; ?>
											<span class="thumb-info thumb-info-centered-info<?php echo ! $lightbox ? ' thumb-info-hide-wrapper-bg thumb-info-no-zoom' : ''; ?> font-size-xl">
												<span class="thumb-info-wrapper font-size-xl">
													<?php
														echo wp_get_attachment_image(
															$featured_image['attachment_id'],
															'blog-masonry',
															false,
															array(
																'class'    => 'img-responsive',
																'data-src' => esc_url( $attachment_medium[0] ),
																'src'      => porto_is_amp_endpoint() ? esc_url( $attachment_medium[0] ) : esc_url( $placeholder[0] ),
															)
														);
													?>
													<?php if ( $lightbox ) : ?>
													<span class="thumb-info-title font-size-xl">
														<span class="thumb-info-inner font-size-xl"><i class="Simple-Line-Icons-magnifier font-size-xl"></i></span>
													</span>
													<?php endif; ?>
												</span>
											</span>
											<?php
											if ( $lightbox ) :
												?>
												</a><?php endif; ?>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
						<?php
						if ( $image_count > 1 ) :
							$options           = array();
							$options['items']  = $slider_thumbs_count;
							$options['margin'] = 10;
							$options['nav']    = false;
							$options['dots']   = false;
							$options['loop']   = false;
							if ( $slider_thumbs_count > 4 ) {
								$options['sm'] = 4;
								$options['xs'] = 3;
							}
							$options = json_encode( $options );
							?>

							<div class="porto-carousel thumb-gallery-thumbs owl-carousel show-nav-hover" data-plugin-options="<?php echo esc_attr( $options ); ?>">
								<?php
								foreach ( $featured_images as $featured_image ) {
									$attachment = porto_get_attachment( $featured_image['attachment_id'], 'portfolio-thumbnail' );
									if ( $attachment ) {
										?>
										<div class="thumb-gallery-thumbs-item">
											<img alt="<?php echo esc_attr( $attachment['alt'] ); ?>" src="<?php echo esc_url( $attachment['src'] ); ?>" class="img-responsive cur-pointer">
										</div>
										<?php
									}
								}
								?>
							</div>
						<?php endif; ?>
						<?php
					else :
						$options = array(
							'nav'  => true,
							'dots' => false,
							'loop' => false,
						);
						?>
						<div class="portfolio-slideshow porto-carousel owl-carousel nav-style-3" data-plugin-options="<?php echo esc_attr( json_encode( $options ) ); ?>">
							<?php
							foreach ( $featured_images as $featured_image ) {

								$attachment_medium = wp_get_attachment_image_src( $featured_image['attachment_id'], 'blog-masonry' );
								if ( $attachment_medium ) :
									$placeholder = porto_generate_placeholder( $attachment_medium[1] . 'x' . $attachment_medium[2] );
									?>
									<div class="img-thumbnail">
									<?php
										echo wp_get_attachment_image(
											$featured_image['attachment_id'],
											'blog-masonry',
											false,
											array(
												'class'    => 'owl-lazy img-responsive',
												'data-src' => esc_url( $attachment_medium[0] ),
												'src'      => porto_is_amp_endpoint() ? esc_url( $attachment_medium[0] ) : esc_url( $placeholder[0] ),
											)
										);
									if ( ! empty( $porto_settings['portfolio-zoom'] ) ) :
										$attachment = porto_get_attachment( $featured_image['attachment_id'], 'full' );
										?>
										<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
									<?php endif; ?>
									</div>
									<?php
								endif;
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			if ( 'video' == $slideshow_type && $video_code ) {
				wp_enqueue_script( 'jquery-fitvids' );
				?>
				<div class="portfolio-image single">
					<div class="img-thumbnail fit-video">
						<?php echo do_shortcode( $video_code ); ?>
					</div>
				</div>
				<?php
			}
			?>

		</div>
		<div class="col-md-8">
		<?php else : ?>
		<div class="col-lg-12">
		<?php endif; ?>

			<?php
			if ( get_the_content() ) :
				?>
				<?php /* translators: %s: Portfolio Description */ ?>
				<h5 class="inline-block portfolio-desc"><?php echo porto_strip_script_tags( sprintf( __( '%s <strong>Description</strong>', 'porto' ), $portfolio_name ) ); ?></h5><?php endif; ?>

			<div class="portfolio-info">
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

			<?php if ( $portfolio_info ) : ?>
				<h5 class="m-t-sm"><?php esc_html_e( 'More Information', 'porto' ); ?></h5>
				<div class="m-b-lg">
					<?php echo do_shortcode( $portfolio_info ); ?>
				</div>
			<?php endif; ?>

			<?php porto_get_template_part( 'views/portfolios/meta' ); ?>

			<?php if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && ! empty( $porto_settings['portfolio-share'] ) ) ) ) : ?>
				<hr>
				<div class="share-links-block">
					<h5><?php esc_html_e( 'Share', 'porto' ); ?></h5>
					<?php get_template_part( 'share' ); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>

	<div class="<?php echo ( 'widewidth' === $porto_layout ? ' container' : '' ); ?>">
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
