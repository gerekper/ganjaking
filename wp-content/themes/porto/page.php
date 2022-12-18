<?php get_header(); ?>

<?php
global $porto_settings, $porto_layout;

$featured_images = porto_get_featured_images();
?>
	<div id="content" role="main">
		<?php /* The loop */ ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article <?php post_class(); ?>>
				<?php if ( count( $featured_images ) && ! post_password_required() ) : ?>
					<?php
					// Slideshow
					$featured_images = porto_get_featured_images();
					$image_count     = count( $featured_images );

					if ( $image_count ) :
						?>
						<div class="page-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
							<div class="page-slideshow porto-carousel owl-carousel">
								<?php
								foreach ( $featured_images as $featured_image ) {
									$attachment = porto_get_attachment( $featured_image['attachment_id'] );
									if ( $attachment ) {
										?>
										<div>
											<div class="img-thumbnail">
												<img class="owl-lazy img-responsive" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" data-src="<?php echo esc_url( $attachment['src'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>" />
												<?php if ( $porto_settings['page-zoom'] ) : ?>
													<span class="zoom" data-src="<?php echo esc_attr( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
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
					?>
				<?php endif; ?>

				<?php
				$microdata = porto_get_meta_value( 'page_microdata' );
				if ( $porto_settings['rich-snippets'] && 'no' !== $microdata && ( 'yes' === $microdata || ( 'yes' !== $microdata && $porto_settings['page-microdata'] ) ) ) {
					porto_render_rich_snippets( 'h2' );
				}
				?>

				<div class="page-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before'      => '<div class="pagination" role="navigation">',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						)
					);
					?>
				</div>
			</article>
			<?php
			$share           = porto_get_meta_value( 'page_share' );
			$share_enabled   = $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && $porto_settings['page-share'] ) ) && ( isset( $porto_settings['page-share-pos'] ) && ! $porto_settings['page-share-pos'] );
			$comment_enabled = $porto_settings['page-comment'] || comments_open();
			if ( $share_enabled || $comment_enabled ) :
				?>
			<div class="<?php echo 'wide-left-sidebar' == $porto_layout || 'wide-right-sidebar' == $porto_layout || 'wide-both-sidebar' == $porto_layout ? 'm-t-lg m-b-xl' : ''; ?>">
				<?php if ( $share_enabled ) : ?>
				<div class="page-share<?php echo 'widewidth' == $porto_layout ? ' container' : ''; ?>">
					<h3><i class="fas fa-share"></i><?php esc_html_e( 'Share this post', 'porto' ); ?></h3>
					<?php get_template_part( 'share' ); ?>
				</div>
				<?php endif; ?>

				<?php
				if ( $comment_enabled ) {
					wp_reset_postdata();
					comments_template();
				}
				?>
			</div>
			<?php endif; ?>
		<?php endwhile; ?>

	</div>

<?php get_footer(); ?>
