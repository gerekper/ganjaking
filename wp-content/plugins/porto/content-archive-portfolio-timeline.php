<?php
global $porto_settings, $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count, $post, $porto_portfolio_thumb, $porto_portfolio_thumb_style, $porto_portfolio_image_counter, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal, $portfolio_num, $porto_portfolio_thumbs_html;
$portfolio_layout = 'timeline';
$archive_image    = (int) get_post_meta( $post->ID, 'portfolio_archive_image', true );
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
$portfolio_link            = get_post_meta( $post->ID, 'portfolio_link', true );
$show_external_link        = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;
$portfolio_thumb           = $porto_portfolio_thumb ? $porto_portfolio_thumb : ( isset( $porto_settings['portfolio-archive-thumb'] ) ? $porto_settings['portfolio-archive-thumb'] : '' );
$portfolio_thumb_style     = $porto_portfolio_thumb_style ? $porto_portfolio_thumb_style : ( isset( $porto_settings['portfolio-archive-thumb-style'] ) ? $porto_settings['portfolio-archive-thumb-style'] : '' );
$portfolio_thumb_bg        = $porto_portfolio_thumb_bg ? $porto_portfolio_thumb_bg : ( isset( $porto_settings['portfolio-archive-thumb-bg'] ) ? $porto_settings['portfolio-archive-thumb-bg'] : 'lighten' );
$portfolio_thumb_image     = $porto_portfolio_thumb_image ? ( 'zoom' == $porto_portfolio_thumb_image ? '' : $porto_portfolio_thumb_image ) : ( isset( $porto_settings['portfolio-archive-thumb-image'] ) ? $porto_settings['portfolio-archive-thumb-image'] : '' );
$portfolio_show_link       = isset( $porto_settings['portfolio-archive-link'] ) ? $porto_settings['portfolio-archive-link'] : true;
$portfolio_show_all_images = isset( $porto_settings['portfolio-archive-all-images'] ) ? $porto_settings['portfolio-archive-all-images'] : false;
$portfolio_images_count    = isset( $porto_settings['portfolio-archive-images-count'] ) ? $porto_settings['portfolio-archive-images-count'] : '2';
$portfolio_show_zoom       = isset( $porto_settings['portfolio-archive-zoom'] ) ? $porto_settings['portfolio-archive-zoom'] : false;
$portfolio_ajax            = isset( $porto_settings['portfolio-archive-ajax'] ) ? $porto_settings['portfolio-archive-ajax'] : false;
$portfolio_ajax_modal      = isset( $porto_settings['portfolio-archive-ajax-modal'] ) ? $porto_settings['portfolio-archive-ajax-modal'] : false;
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
$options                    = array();
$options['margin']          = 10;
$options['animateOut']      = 'fadeOut';
$options['autoplay']        = true;
$options['autoplayTimeout'] = 3000;
$options                    = json_encode( $options );
$count                      = count( $featured_images );
$classes                    = array();
$classes[]                  = 'thumb-info-no-borders';
if ( $portfolio_thumb_bg ) {
	$classes[] = 'thumb-info-' . $portfolio_thumb_bg;
}

$show_info      = true;
$show_plus_icon = false;
switch ( $portfolio_thumb ) {
	case 'plus-icon':
		$show_info      = false;
		$show_plus_icon = true;
		break;
	case 'centered-info':
		$classes[]           = 'thumb-info-centered-info';
		$portfolio_show_zoom = false;
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

if ( 'alternate-info' == $portfolio_thumb_style || 'alternate-with-plus' == $portfolio_thumb_style ) {
	if ( 0 == $portfolio_num % 2 ) {
		$show_info = false;
		$classes[] = 'alternate-info-hide';
	} else {
		$classes[] = 'alternate-info';
	}
}

if ( 'alternate-with-plus' == $portfolio_thumb_style ) {
	$show_plus_icon = true;
}

$show_counter = isset( $porto_settings['portfolio-archive-image-counter'] ) ? $porto_settings['portfolio-archive-image-counter'] : false;
switch ( $porto_portfolio_image_counter ) {
	case 'show':
		$show_counter = true;
		break;
	case 'hide':
		$show_counter = false;
		break;
}
if ( $count > 1 && $portfolio_show_all_images ) {
	$classes[] = 'thumb-info-no-zoom';
} elseif ( $portfolio_thumb_image ) {
	$classes[] = 'thumb-info-' . $portfolio_thumb_image;
}
$ajax_attr = '';
if ( ! ( $show_external_link && $portfolio_link ) && $portfolio_ajax ) {
	$portfolio_show_zoom       = false;
	$portfolio_show_all_images = false;
	if ( $portfolio_ajax_modal ) {
		$ajax_attr = ' data-ajax-on-modal';
	} else {
		$ajax_attr = ' data-ajax-on-page';
	}
}
if ( $portfolio_show_zoom ) {
	$classes[] = 'thumb-info-centered-icons';
}
$class                    = implode( ' ', $classes );
$zoom_src                 = array();
$zoom_title               = array();
$sub_title                = porto_portfolio_sub_title( $post );
$portfolio_show_link_zoom = false;
if ( ! empty( $porto_settings['portfolio-archive-link-zoom'] ) ) {
	$portfolio_show_link_zoom  = true;
	$portfolio_show_zoom       = false;
	$portfolio_show_link       = false;
	$portfolio_show_all_images = false;
}
if ( $ajax_attr ) {
	$portfolio_show_link_zoom = false;
}
if ( $count ) :
	$post_timestamp = strtotime( $post->post_date );
	$post_month     = date( 'n', $post_timestamp );
	$post_year      = get_the_date( 'o' );
	$current_date   = get_the_date( 'o-n' );
	?>
	<?php
	if ( $prev_post_month != $post_month || ( $prev_post_month == $post_month && $prev_post_year != $post_year ) ) :
		$post_count = 1;
		?>
	<div class="timeline-date" data-date="<?php echo esc_attr( $current_date ); ?>"><h3><?php echo get_the_date( 'F Y' ); ?></h3></div>
<?php endif; ?>
	<?php
	$post_class   = array();
	$post_class[] = 'portfolio';
	$post_class[] = 'portfolio-' . $portfolio_layout;
	$post_class[] = 'timeline-box';
	$post_class[] = ( 1 == $post_count % 2 ? 'left' : 'right' );
	$item_cats    = get_the_terms( $post->ID, 'portfolio_cat' );
	if ( $item_cats && ! is_wp_error( $item_cats ) ) :
		foreach ( $item_cats as $item_cat ) {
			$post_class[] = urldecode( $item_cat->slug );
		}
	endif;
	?>
	<article <?php post_class( $post_class ); ?>>
		<?php porto_render_rich_snippets( 'h3' ); ?>
		<a class="portfolio-link" href="<?php
		if ( $portfolio_show_link_zoom ) {
			foreach ( $featured_images as $featured_image ) {
				$attachment_id = $featured_image['attachment_id'];
				$attachment    = porto_get_attachment( $attachment_id );
				if ( $attachment ) {
					echo esc_url( $attachment['src'] );
					break;
				}
			}
		} else {
			if ( $show_external_link && $portfolio_link ) {
				echo esc_url( $portfolio_link );
			} else {
				the_permalink();
			}
		}
		?>"<?php echo porto_filter_output( $ajax_attr ); ?>>
			<span class="portfolio-item thumb-info <?php echo esc_attr( $class ); ?>">
				<?php if ( isset( $show_counter ) && ( $show_counter ) ) : ?>
					<span class="thumb-info-icons position-style-2 text-color-light">
						<span class="thumb-info-icon pictures background-color-primary">
							<?php echo function_exists( 'porto_get_featured_images' ) ? count( porto_get_featured_images() ) : 0; ?>
							<i class="far fa-image"></i>
						</span>
					</span>
				<?php endif; ?>
				<span class="thumb-info-wrapper">
					<?php
					if ( $count > 1 && $portfolio_show_all_images ) :
						?>
						<div class="porto-carousel owl-carousel m-b-none nav-inside show-nav-hover" data-plugin-options="<?php echo esc_attr( $options ); ?>"><?php endif; ?>
						<?php
						$i = 0;
						foreach ( $featured_images as $featured_image ) :
							$attachment_id       = $featured_image['attachment_id'];
							$attachment          = porto_get_attachment( $attachment_id );
							$attachment_timeline = porto_get_attachment( $attachment_id, isset( $image_size ) ? $image_size : 'portfolio-timeline' );
							if ( $attachment && $attachment_timeline ) :
								$zoom_src[]   = $attachment['src'];
								$zoom_title[] = $attachment['caption'];
								?>
								<img class="img-responsive" width="<?php echo esc_attr( $attachment_timeline['width'] ); ?>" height="<?php echo esc_attr( $attachment_timeline['height'] ); ?>" src="<?php echo esc_url( $attachment_timeline['src'] ); ?>" alt="<?php echo esc_attr( $attachment_timeline['alt'] ); ?>" />
								<?php

								if ( ! empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) && $attachment_id ) {
									$attachment_thumb             = porto_get_attachment( $attachment_id, 'widget-thumb-medium' );
									$porto_portfolio_thumbs_html .= '<span><img src="' . esc_url( $attachment_thumb['src'] ) . '" alt="' . esc_attr( $attachment_thumb['alt'] ) . '" ></span>';
								}
								if ( ! $portfolio_show_all_images ) {
									break;
								}
								$i++;
								if ( $i >= $portfolio_images_count ) {
									break;
								}
							endif;
						endforeach;
						?>
					<?php
					if ( $count > 1 && $portfolio_show_all_images ) :
						?>
						</div><?php endif; ?>

						<?php if ( $show_info ) : ?>
							<span class="thumb-info-title">
								<span class="thumb-info-inner"><?php the_title(); ?></span>
								<?php if ( $sub_title ) : ?>
									<span class="thumb-info-type"><?php echo wp_kses_post( $sub_title ); ?></span>
								<?php endif; ?>
							</span>
						<?php else : ?>
							<span class="thumb-info-plus"></span>
						<?php endif; ?>

					<?php if ( $portfolio_show_link || $portfolio_show_zoom ) : ?>
						<span class="thumb-info-action">
							<?php if ( $portfolio_show_link ) : ?>
								<span class="thumb-info-action-icon thumb-info-action-icon-primary"><i class="fa <?php echo ! $ajax_attr ? 'fa-link' : 'fa-plus-square'; ?>"></i></span>
							<?php endif; ?>
							<?php if ( $portfolio_show_zoom ) : ?>
								<span class="thumb-info-action-icon thumb-info-action-icon-light thumb-info-zoom" data-src="<?php echo esc_attr( json_encode( $zoom_src ) ); ?>" data-title="<?php echo esc_attr( json_encode( $zoom_title ) ); ?>"><i class="fas fa-search-plus"></i></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<?php
					if ( $portfolio_show_link_zoom ) :
						?>
						<span class="thumb-info-zoom" data-src="<?php echo esc_attr( json_encode( $zoom_src ) ); ?>" data-title="<?php echo esc_attr( json_encode( $zoom_title ) ); ?>"></span><?php endif; ?>
				</span>
			</span>
		</a>
	</article>
	<?php
	$prev_post_year  = $post_year;
	$prev_post_month = $post_month;
	$post_count++;

endif;
