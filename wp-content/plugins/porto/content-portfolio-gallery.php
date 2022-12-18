<?php
global $porto_settings, $porto_layout;

$portfolio_layout = 'gallery';

$portfolio_info         = get_post_meta( $post->ID, 'portfolio_info', true );
$portfolio_link         = get_post_meta( $post->ID, 'portfolio_link', true );
$skill_list             = get_the_term_list( $post->ID, 'portfolio_skills', '', '</li><li><i class="fas fa-check-circle"></i> ', '' );
$portfolio_location     = get_post_meta( $post->ID, 'portfolio_location', true );
$portfolio_client       = get_post_meta( $post->ID, 'portfolio_client', true );
$portfolio_client_link  = get_post_meta( $post->ID, 'portfolio_client_link', true );
$portfolio_author_quote = get_post_meta( $post->ID, 'portfolio_author_quote', true );
$portfolio_author_name  = get_post_meta( $post->ID, 'portfolio_author_name', true );
$portfolio_author_image = get_post_meta( $post->ID, 'portfolio_author_image', true );
$portfolio_author_role  = get_post_meta( $post->ID, 'portfolio_author_role', true );
$portfolio_name         = empty( $porto_settings['portfolio-singular-name'] ) ? __( 'Portfolio', 'porto' ) : $porto_settings['portfolio-singular-name'];

$share        = porto_get_meta_value( 'portfolio_share' );
$post_class   = array();
$post_class[] = 'portfolio-' . $portfolio_layout;
if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
	$post_class[] = 'post-title-simple';
}
?>

<article <?php post_class( $post_class ); ?>>

	<?php if ( ! empty( $porto_settings['portfolio-page-nav'] ) ) : ?>
	<div class="portfolio-title<?php echo 'widewidth' === $porto_layout ? ' container m-t-lg' : ''; ?>">
		<div class="row">
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
	<hr class="<?php echo 'widewidth' === $porto_layout ? 'm-t-xl m-b-none solid' : 'tall'; ?>">
	<?php endif; ?>

	<?php porto_render_rich_snippets( false ); ?>

	<?php
	// Portfolio Gallery
	$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );

	if ( ! $slideshow_type ) {
		$slideshow_type = 'images';
	}

	$options                       = array();
	$options['delegate']           = 'a.lightbox-portfolio';
	$options['type']               = 'image';
	$options['gallery']['enabled'] = true;
	$options                       = json_encode( $options );

	if ( 'none' != $slideshow_type ) :
		?>
		<?php
		if ( 'images' == $slideshow_type ) :
			$featured_images = porto_get_featured_images();
			$image_count     = count( $featured_images );

			if ( $image_count ) :
				?>
			<div class="row<?php echo 'widewidth' === $porto_layout ? ' m-n' : ''; ?>">
				<ul class="portfolio-list<?php echo empty( $porto_settings['portfolio-zoom'] ) ? '' : ' lightbox'; ?>"<?php echo empty( $porto_settings['portfolio-zoom'] ) ? '' : ' data-plugin-options="' . esc_attr( $options ) . '"'; ?>>
					<?php
					foreach ( $featured_images as $featured_image ) {
						$attachment = porto_get_attachment( $featured_image['attachment_id'], 'widewidth' === $porto_layout ? 'full' : 'blog-masonry-small' );
						if ( $attachment ) {
							?>
							<li class="col-lg-3 col-sm-6<?php echo 'widewidth' === $porto_layout ? ' p-0' : ''; ?>">
								<div class="portfolio-item<?php echo 'widewidth' === $porto_layout ? ' m-0' : ''; ?>">
									<span class="thumb-info thumb-info-lighten thumb-info-centered-icons<?php echo 'widewidth' === $porto_layout ? ' thumb-info-no-borders' : ''; ?>">
										<span class="thumb-info-wrapper">
											<img width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" src="<?php echo esc_url( $attachment['src'] ); ?>" class="img-responsive" alt="<?php echo esc_attr( $attachment['alt'] ); ?>">
											<?php if ( ! empty( $porto_settings['portfolio-zoom'] ) ) : ?>
												<span class="thumb-info-action">
												<a href="<?php echo esc_url( $attachment['src'] ); ?>" class="lightbox-portfolio">
													<span class="thumb-info-action-icon thumb-info-action-icon-light"><i class="fas fa-search-plus"></i></span>
												</a>
											</span>
											<?php endif; ?>
										</span>
									</span>
								</div>
							</li>
							<?php
						}
					}
					?>
				</ul>
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
					<div class="img-thumbnail fit-video<?php echo 'widewidth' === $porto_layout ? ' img-thumbnail-no-borders' : ''; ?>">
						<?php echo do_shortcode( $video_code ); ?>
					</div>
				</div>
				<?php
			}
		}
	endif;
	?>

	<div class="m-t-lg<?php echo 'widewidth' === $porto_layout ? ' container' : ''; ?>">
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
			<div class="<?php echo 'wide-both-sidebar' == $porto_layout || 'both-sidebar' == $porto_layout ? 'col-md-12' : 'col-md-7'; ?> m-t-sm">
				<?php
				if ( get_the_content() ) :
					?>
					<?php /* translators: $1: Portfolio Description $2 and $3 opening and closing bold tags respectively */ ?>
					<h4 class="portfolio-desc m-t-sm"><?php printf( esc_html__( '%1$s %2$sDescription%3$s', 'porto' ), esc_html( $portfolio_name ), '<b>', '</b>' ); ?></h4><?php endif; ?>

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
					<h5 class="m-t-sm"><?php esc_html_e( 'More Information', 'porto' ); ?></h5>
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
