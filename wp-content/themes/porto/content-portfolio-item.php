<?php
global $porto_settings, $post, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal;

$portfolio_view        = ( $porto_portfolio_view && 'classic' != $porto_portfolio_view ) ? $porto_portfolio_view : ( isset( $porto_settings['portfolio-related-style'] ) ? $porto_settings['portfolio-related-style'] : '' );
$portfolio_thumb       = $porto_portfolio_thumb ? $porto_portfolio_thumb : ( isset( $porto_settings['portfolio-related-thumb'] ) ? $porto_settings['portfolio-related-thumb'] : '' );
$portfolio_thumb_bg    = $porto_portfolio_thumb_bg ? $porto_portfolio_thumb_bg : ( isset( $porto_settings['portfolio-related-thumb-bg'] ) ? $porto_settings['portfolio-related-thumb-bg'] : 'lighten' );
$portfolio_thumb_image = $porto_portfolio_thumb_image ? ( 'zoom' == $porto_portfolio_thumb_image ? '' : $porto_portfolio_thumb_image ) : ( isset( $porto_settings['portfolio-related-thumb-image'] ) ? $porto_settings['portfolio-related-thumb-image'] : '' );
$portfolio_show_link   = isset( $porto_settings['portfolio-related-link'] ) ? $porto_settings['portfolio-related-link'] : true;
$portfolio_show_zoom   = isset( $porto_settings['portfolio-zoom'] ) ? $porto_settings['portfolio-zoom'] : false;
$portfolio_ajax        = false;
$portfolio_ajax_modal  = false;

if ( 'yes' == $porto_portfolio_ajax_load ) {
	$portfolio_ajax = true;
} elseif ( 'no' == $porto_portfolio_ajax_load ) {
	$portfolio_ajax = false;
}

if ( 'yes' == $porto_portfolio_ajax_modal ) {
	$portfolio_ajax_modal = true;
} elseif ( 'no' == $porto_portfolio_ajax_modal ) {
	$portfolio_ajax_modal = false;
}

$archive_image = (int) get_post_meta( $post->ID, 'portfolio_archive_image', true );
if ( $archive_image ) {
	$featured_images   = array();
	$featured_image    = array(
		'thumb'         => wp_get_attachment_thumb_url( $archive_image ),
		'full'          => wp_get_attachment_url( $archive_image ),
		'attachment_id' => $archive_image,
	);
	$featured_images[] = $featured_image;
} else {
	$featured_images = porto_get_featured_images();
}
$portfolio_link     = get_post_meta( $post->ID, 'portfolio_link', true );
$show_external_link = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;

$count = count( $featured_images );

$classes = array();
if ( 'full' == $portfolio_view ) {
	$classes[] = 'thumb-info-no-borders';
}
if ( $portfolio_thumb_bg ) {
	$classes[] = 'thumb-info-' . $portfolio_thumb_bg;
}

switch ( $portfolio_thumb ) {
	case 'centered-info':
		$classes[] = 'thumb-info-centered-info';
		break;
	case 'bottom-info':
		$classes[] = 'thumb-info-bottom-info';
		break;
	case 'bottom-info-dark':
		$classes[] = 'thumb-info-bottom-info thumb-info-bottom-info-dark';
		break;
	case 'hide-info-hover':
		$classes[] = 'thumb-info-centered-info thumb-info-hide-info-hover';
		break;
}

if ( $portfolio_thumb_image ) {
	$classes[] = 'thumb-info-' . $portfolio_thumb_image;
}

$ajax_attr = '';
if ( ! ( $show_external_link && $portfolio_link ) && $portfolio_ajax ) {
	$portfolio_show_zoom = false;
	if ( $portfolio_ajax_modal ) {
		$ajax_attr = ' data-ajax-on-modal';
	} else {
		$ajax_attr = ' data-ajax-on-page';
	}
}
if ( $portfolio_show_link && ( 'centered-info' == $portfolio_thumb || 'hide-info-hover' == $portfolio_thumb ) ) {
	$portfolio_show_zoom = false;
}

if ( $portfolio_show_zoom ) {
	$classes[] = 'thumb-info-centered-icons';
}

$class = implode( ' ', $classes );

$sub_title = porto_portfolio_sub_title( $post );

if ( $count ) :
	$attachment_id      = $featured_images[0]['attachment_id'];
	$attachment         = porto_get_attachment( $attachment_id );
	$attachment_related = porto_get_attachment( $attachment_id, isset( $image_size ) ? $image_size : 'related-portfolio' );
	if ( $attachment && $attachment_related ) :
		?>
		<div class="portfolio-item <?php echo 'outimage' == $portfolio_view ? 'outimage' : $portfolio_view; ?>">
			<a class="text-decoration-none" href="<?php echo ! $show_external_link || ! $portfolio_link ? esc_url( get_the_permalink() ) : esc_url( $portfolio_link ); ?>"<?php echo porto_filter_output( $ajax_attr ); ?>>
				<span class="thumb-info <?php echo esc_attr( $class ); ?>">
					<span class="thumb-info-wrapper">
						<img class="img-responsive" width="<?php echo esc_attr( $attachment_related['width'] ); ?>" height="<?php echo esc_attr( $attachment_related['height'] ); ?>" src="<?php echo esc_url( $attachment_related['src'] ); ?>" alt="<?php echo esc_attr( $attachment_related['alt'] ); ?>" />
						<?php if ( 'outimage' != $portfolio_view ) : ?>
							<span class="thumb-info-title">
								<span class="thumb-info-inner"><?php the_title(); ?></span>
								<?php
								if ( $sub_title ) :
									?>
									<span class="thumb-info-type"><?php echo wp_kses_post( $sub_title ); ?></span>
								<?php endif; ?>
							</span>
							<?php
						else :
							if ( ! empty( $porto_settings['portfolio-archive-readmore'] ) ) :
								?>
								<span class="thumb-info-title">
									<span class="thumb-info-inner"><?php echo empty( $porto_settings['portfolio-archive-readmore-label'] ) ? esc_html__( 'View Project...', 'porto' ) : porto_strip_script_tags( $porto_settings['portfolio-archive-readmore-label'] ); ?></span>
								</span>
								<?php
							endif;
						endif;
						?>
						<?php if ( $portfolio_show_link || $portfolio_show_zoom ) : ?>
							<span class="thumb-info-action">
								<?php if ( $portfolio_show_link ) : ?>
									<span class="thumb-info-action-icon"><i class="fa <?php echo ! empty( $ajax_attr ) ? 'fa-plus-square' : 'fa-link'; ?>"></i></span>
								<?php endif; ?>
								<?php if ( $portfolio_show_zoom ) : ?>
									<span class="thumb-info-action-icon thumb-info-action-icon-light thumb-info-zoom zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</span>
				</span>
				<?php if ( 'outimage' == $portfolio_view ) : ?>
					<h4 class="m-t-md m-b-none portfolio-title"><?php the_title(); ?></h4>
					<?php
					if ( $sub_title ) :
						?>
						<p class="m-b-sm color-body"><?php echo wp_kses_post( $sub_title ); ?></p>
						<?php
					endif;
				endif;
				?>
				<?php if ( isset( $porto_settings['portfolio-related-show-content'] ) && $porto_settings['portfolio-related-show-content'] ) : ?>
				<div class="m-t p-l-lg p-r-lg">
					<?php
					if ( has_excerpt() ) {
						the_excerpt();
					} else {
						echo porto_get_excerpt( isset( $porto_settings['portfolio-excerpt-length'] ) ? $porto_settings['portfolio-excerpt-length'] : 80, false );
					}
					?>
				</div>
				<?php endif; ?>

				<?php if ( 'outimage' == $portfolio_view ) : ?>
					<?php porto_get_template_part( 'views/portfolios/quote' ); ?>
				<?php endif; ?>
			</a>
		</div>
		<?php
	endif;
endif;
