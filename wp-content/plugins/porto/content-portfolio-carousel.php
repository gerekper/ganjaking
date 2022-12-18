<?php
global $porto_settings, $porto_layout;

$portfolio_layout = 'carousel';

$portfolio_info = get_post_meta( $post->ID, 'portfolio_info', true );
$portfolio_name = empty( $porto_settings['portfolio-singular-name'] ) ? __( 'Portfolio', 'porto' ) : $porto_settings['portfolio-singular-name'];
$share          = porto_get_meta_value( 'portfolio_share' );

$post_class   = array();
$post_class[] = 'portfolio-' . $portfolio_layout;
if ( empty( $porto_settings['portfolio-page-nav'] ) && 'fullwidth' === $porto_layout ) {
	$post_class[] = ' m-t-n-xl';
}

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
	// Portfolio Carousel
	$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );

	if ( ! $slideshow_type ) {
		$slideshow_type = 'images';
	}

	if ( 'none' != $slideshow_type ) :
		?>
		<?php
		if ( 'images' == $slideshow_type ) :
			$featured_images = porto_get_featured_images();
			$count           = count( $featured_images );
			$i               = 1;

			if ( $count ) :
				?>
			<rs-module-wrap id="revolutionSliderCarouselContainer" class="rev_slider_wrapper fullwidthbanner-container m-b-none br-none" data-alias="" style="background: #f3f3f2;">
				<rs-module id="revolutionSliderCarousel" class="rev_slider fullwidthabanner" data-version="6.0.4">
					<rs-slides>
						<?php
						foreach ( $featured_images as $featured_image ) {
							$attachment = porto_get_attachment( $featured_image['attachment_id'], 'blog-masonry' );
							if ( $attachment ) {
								?>
								<rs-slide data-index="rs-<?php echo (int) ( $i++ ); ?>" data-transition="fade" data-slotamount="7" data-easein="default" data-easeout="default" data-masterspeed="300" data-rotate="0" data-saveperformance="off" data-title="" data-description="">
									<img width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" src="<?php echo esc_url( $attachment['src'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>" data-bg="f:contain;" class="rev-slidebg" data-no-retina>
								</rs-slide>
								<?php
							}
						}
						?>
					</rs-slides>
				</rs-module>
			</rs-module-wrap>
			<script>
				( function() {
					var porto_init_rs_carousel = function() {
						( function( $ ) {
							'use strict';

							var revapi5;

							if ( window.RS_MODULES === undefined ) window.RS_MODULES = {};
							if ( RS_MODULES.modules === undefined ) RS_MODULES.modules = {};
							RS_MODULES.modules[ 'revslider51' ] = {
								init: function() {
									revapi5 = $( '#revolutionSliderCarousel' );
									if ( revapi5 == undefined || revapi5.revolution == undefined ) {
										revslider_showDoubleJqueryError("revolutionSliderCarousel");
										return;
									}
									revapi5.revolutionInit( {
										sliderType: "carousel",
										sliderLayout: "<?php echo ( porto_get_wrapper_type() == 'boxed' || 'boxed' == $porto_settings['main-wrapper'] || ( porto_is_ajax() && isset( $_POST['ajax_action'] ) && 'portfolio_ajax_modal' == $_POST['ajax_action'] ) ) ? 'auto' : 'fullwidth'; ?>",
										dottedOverlay: "none",
										delay: 4000,
										navigation: {
											keyboardNavigation: "off",
											keyboard_direction: "horizontal",
											mouseScrollNavigation: "off",
											onHoverStop: "off",
											arrows: {
												style: "tparrows-carousel",
												enable: true,
												hide_onmobile: false,
												hide_onleave: false,
												tmp: '',
												left: {
													h_align: "left",
													v_align: "center",
													h_offset: 30,
													v_offset: 0
												},
												right: {
													h_align: "right",
													v_align: "center",
													h_offset: 30,
													v_offset: 0
												}
											}
										},
										carousel: {
											maxRotation: 65,
											vary_rotation: "on",
											minScale: 55,
											vary_scale: "off",
											horizontal_align: "center",
											vertical_align: "center",
											fadeout: "on",
											vary_fade: "on",
											maxVisibleItems: 5,
											infinity: "on",
											space: -100,
											stretch: "off"
										},
										gridwidth: 600,
										gridheight: 600,
										lazyType: "none",
										shadow: 0,
										spinner: "off",
										stopLoop: "on",
										stopAfterLoops: 0,
										stopAtSlide: 3,
										shuffle: "off",
										//autoHeight: "off",
										disableProgressBar: "on",
										hideThumbsOnMobile: "off",
										hideSliderAtLimit: 0,
										hideCaptionAtLimit: 0,
										hideAllCaptionAtLilmit: 0,
										debugMode: false,
										fallbacks: {
											simplifyAll: "off",
											nextSlideOnWindowFocus: "off",
											disableFocusListener: false
										},
									} );
								}
							};

							if ( window.RS_MODULES.checkMinimal !== undefined ) {
								window.RS_MODULES.checkMinimal();
							}

						} )( window.jQuery );
					};

					if ( window.jQuery ) {
						porto_init_rs_carousel();
					} else {
						document.addEventListener( 'DOMContentLoaded', porto_init_rs_carousel );
					}
				} )();
			</script>
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
			<div class="<?php echo 'wide-both-sidebar' == $porto_layout || 'both-sidebar' == $porto_layout ? 'col-md-12' : 'col-md-7'; ?> m-t-sm m-b-lg">
				<?php
				if ( get_the_content() ) :
					?>
					<?php /* translators: %s: Portfolio Description */ ?>
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
