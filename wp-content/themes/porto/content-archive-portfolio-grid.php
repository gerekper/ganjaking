<?php
global $porto_settings, $porto_layout, $post, $porto_portfolio_columns, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_style, $porto_portfolio_slider, $porto_portfolio_image_counter, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal, $portfolio_num, $porto_portfolio_thumbs_html, $porto_portfolio_show_zoom;

$portfolio_columns = isset( $porto_settings['portfolio-grid-columns'] ) ? $porto_settings['portfolio-grid-columns'] : '4';
if ( $porto_portfolio_columns ) {
	$portfolio_columns = $porto_portfolio_columns;
}
$portfolio_layout          = 'grid';
$portfolio_view            = isset( $porto_settings['portfolio-grid-view'] ) ? $porto_settings['portfolio-grid-view'] : 'default';
$portfolio_thumb           = $porto_portfolio_thumb ? $porto_portfolio_thumb : ( isset( $porto_settings['portfolio-archive-thumb'] ) ? $porto_settings['portfolio-archive-thumb'] : '' );
$portfolio_thumb_style     = $porto_portfolio_thumb_style ? $porto_portfolio_thumb_style : ( isset( $porto_settings['portfolio-archive-thumb-style'] ) ? $porto_settings['portfolio-archive-thumb-style'] : '' );
$portfolio_thumb_bg        = $porto_portfolio_thumb_bg ? $porto_portfolio_thumb_bg : ( isset( $porto_settings['portfolio-archive-thumb-bg'] ) ? $porto_settings['portfolio-archive-thumb-bg'] : 'lighten' );
$portfolio_thumb_image     = $porto_portfolio_thumb_image ? ( 'zoom' == $porto_portfolio_thumb_image ? '' : $porto_portfolio_thumb_image ) : ( isset( $porto_settings['portfolio-archive-thumb-image'] ) ? $porto_settings['portfolio-archive-thumb-image'] : '' );
$portfolio_show_link       = isset( $porto_settings['portfolio-archive-link'] ) ? $porto_settings['portfolio-archive-link'] : true;
$portfolio_show_all_images = isset( $porto_settings['portfolio-archive-all-images'] ) ? $porto_settings['portfolio-archive-all-images'] : false;
$portfolio_images_count    = isset( $porto_settings['portfolio-archive-images-count'] ) ? $porto_settings['portfolio-archive-images-count'] : '2';
$portfolio_show_zoom       = isset( $porto_portfolio_show_zoom ) ? $porto_portfolio_show_zoom : ( isset( $porto_settings['portfolio-archive-zoom'] ) ? $porto_settings['portfolio-archive-zoom'] : false );
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

if ( $porto_portfolio_view && 'classic' != $porto_portfolio_view ) {
	$portfolio_view = $porto_portfolio_view;
}

$post_class   = array();
$post_class[] = 'portfolio';
$post_class[] = 'portfolio-' . $portfolio_layout;
$post_class[] = 'portfolio-col-' . $portfolio_columns;
$item_cats    = get_the_terms( $post->ID, 'portfolio_cat' );
if ( $item_cats ) {
	foreach ( $item_cats as $item_cat ) {
		$post_class[] = urldecode( $item_cat->slug );
	}
}

if ( ! empty( $post_classes ) ) {
	$post_class[] = trim( $post_classes );
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
$portfolio_link             = get_post_meta( $post->ID, 'portfolio_link', true );
$show_external_link         = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;
$options                    = array();
$options['margin']          = 10;
$options['animateOut']      = 'fadeOut';
$options['autoplay']        = true;
$options['autoplayTimeout'] = 3000;
$options                    = json_encode( $options );
$count                      = count( $featured_images );
$classes                    = array();
if ( 'full' == $portfolio_view ) {
	$classes[] = 'thumb-info-no-borders';
}
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
	case 'left-info-no-bg':
		$classes[]           = 'thumb-info-left-no-bg';
		$portfolio_show_zoom = false;
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
$portfolio_lightbox_thumb = isset( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) ? $porto_settings['portfolio-archive-img-lightbox-thumb'] : '';
if ( $count ) :

	$portfolio_id             = $post->ID;
	$portfolio_slider_ids_arr = explode( ',', $porto_portfolio_slider );
	$carousel_options         = array(
		'items'        => 1,
		'margin'       => 0,
		'loop'         => false,
		'dots'         => true,
		'nav'          => false,
		'stagePadding' => 0,
	);
	$featured_images_all      = porto_get_featured_images();
	if ( isset( $featured_image ) && $featured_image ) {
		$featured_images_all[0] = $featured_image;
	}
	?>
	<article <?php post_class( $post_class ); ?>>
		<?php porto_render_rich_snippets( 'h3' ); ?>
		<div class="portfolio-item <?php echo esc_attr( $portfolio_view ); ?>">
			<?php if ( isset( $show_counter ) && ( $show_counter ) ) : ?>
				<span class="thumb-info-icons position-style-2 text-color-light">
					<span class="thumb-info-icon pictures background-color-primary">
					<?php echo function_exists( 'porto_get_featured_images' ) ? count( porto_get_featured_images() ) : 0; ?>
					<i class="far fa-image"></i>
					</span>
				</span>
			<?php endif; ?>

			<a class="text-decoration-none portfolio-link" href="<?php
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
				<span class="thumb-info <?php echo esc_attr( $class ); ?>">
					<span class="thumb-info-wrapper">
						<?php

						if ( in_array( $portfolio_id, $portfolio_slider_ids_arr ) && empty( $porto_settings['portfolio-archive-link-zoom'] ) ) :
							?>
							<div class="porto-carousel owl-carousel m-b-none owl-theme nav-inside" data-plugin-options='<?php echo json_encode( $carousel_options ); ?>'>
							<?php
								$featured_images           = $featured_images_all;
								$portfolio_show_all_images = true;
						elseif ( $count > 1 && $portfolio_show_all_images ) :
							?>
							<div class="porto-carousel owl-carousel m-b-none nav-inside show-nav-hover" data-plugin-options="<?php echo esc_attr( $options ); ?>">
							<?php
						endif;

							$i = 0;
						foreach ( $featured_images as $featured_image ) :
							$attachment_id = $featured_image['attachment_id'];
							$attachment    = porto_get_attachment( $attachment_id );
							if ( isset( $image_size ) ) {
							} elseif ( 1 == $portfolio_columns ) {
								$image_size = 'portfolio-grid-one';
							} elseif ( 2 == $portfolio_columns ) {
								$image_size = 'portfolio-grid-two';
							} else {
								$image_size = 'portfolio-grid';
							}
							$attachment_grid = porto_get_attachment( $attachment_id, $image_size );
							if ( 1 == $portfolio_columns ) {
								$attachment_alternative = porto_get_attachment( $attachment_id, 'portfolio-grid-two' );
							}
							if ( $attachment && $attachment_grid ) :
								$zoom_src[]   = $attachment['src'];
								$zoom_title[] = $attachment['caption'];
								?>
									<img class="img-responsive"<?php echo isset( $attachment_alternative ) ? ' srcset="' . esc_url( $attachment_alternative['src'] ) . ' 575w, ' . esc_url( $attachment_grid['src'] ) . ' ' . esc_attr( $attachment_grid['width'] ) . 'w" sizes="(max-width: 575px) ' . esc_attr( $attachment_alternative['width'] ) . 'px, ' . esc_attr( $attachment_grid['width'] ) . 'px"' : ''; ?> src="<?php echo esc_url( $attachment_grid['src'] ); ?>" alt="<?php echo esc_attr( $attachment_grid['alt'] ); ?>" width="<?php echo esc_attr( $attachment_grid['width'] ); ?>" height="<?php echo esc_attr( $attachment_grid['height'] ); ?>" />
									<?php

									if ( ! empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) && $attachment_id ) {
										$attachment_thumb             = porto_get_attachment( $attachment_id, 'thumbnail' );
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

						<?php if ( in_array( $portfolio_id, $portfolio_slider_ids_arr ) && empty( $porto_settings['portfolio-archive-link-zoom'] ) ) : ?>
							</div>
						<?php elseif ( $count > 1 && $portfolio_show_all_images ) : ?>
							</div>
						<?php endif; ?>

						<?php if ( 'outimage' != $portfolio_view ) : ?>

							<?php if ( $show_info ) : ?>
									<span class="thumb-info-title">

										<span class="thumb-info-inner<?php echo ( (int) $portfolio_columns > 4 && ( 'fullwidth' == $porto_layout || 'left-sidebar' == $porto_layout || 'right-sidebar' == $porto_layout ) ) ? ' font-size-xs line-height-xs' . ( 'bottom-info' == $portfolio_thumb ? ' p-t-xs' : '' ) : ''; ?>"><?php the_title(); ?></span>

										<?php

										if ( $sub_title ) :
											?>
											<span class="thumb-info-type"><?php echo wp_kses_post( $sub_title ); ?></span>
										<?php endif ?>

									</span>
							<?php elseif ( $show_plus_icon ) : ?>
								<span class="thumb-info-plus"></span>
							<?php endif; ?>
							<?php
						else :
							if ( ! empty( $porto_settings['portfolio-archive-readmore'] ) ) :
								?>
							<span class="thumb-info-title">
								<span class="thumb-info-inner<?php echo ( (int) $portfolio_columns > 4 && ( 'fullwidth' == $porto_layout || 'left-sidebar' == $porto_layout || 'right-sidebar' == $porto_layout ) ) ? ' font-size-xs line-height-xs' . ( 'bottom-info' == $portfolio_thumb ? ' p-t-xs' : '' ) : ''; ?>"><?php echo empty( $porto_settings['portfolio-archive-readmore-label'] ) ? esc_html__( 'View Project...', 'porto' ) : wp_kses_post( $porto_settings['portfolio-archive-readmore-label'] ); ?></span>
							</span>
								<?php
							endif;
						endif;
						?>
						<?php if ( $portfolio_show_link || $portfolio_show_zoom ) : ?>
							<span class="thumb-info-action">
								<?php if ( $portfolio_show_link ) : ?>
									<span class="thumb-info-action-icon thumb-info-action-icon-<?php echo ! $portfolio_show_zoom ? 'dark opacity-8' : 'primary'; ?>"><i class="fa <?php echo ! $ajax_attr ? 'fa-link' : 'fa-plus-square'; ?>"></i></span>
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
			<?php if ( 'outimage' == $portfolio_view ) : ?>
				<?php if ( $portfolio_columns > 4 ) : ?>
					<h5 class="m-t-md m-b-none portfolio-title"><?php the_title(); ?></h5>
				<?php else : ?>
					<h4 class="m-t-md m-b-none portfolio-title"><?php the_title(); ?></h4>
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<p class="m-b-sm color-body"><?php echo esc_html( $sub_title ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $porto_settings['portfolio-show-content'] ) ) : ?>
					<div class="portfolio-brief-content m-t p-l-lg p-r-lg">
					<?php
					if ( ! empty( $porto_settings['portfolio-excerpt'] ) ) {
						if ( has_excerpt() ) {
							the_excerpt();
						} else {
							echo porto_get_excerpt( $porto_settings['portfolio-excerpt-length'], $porto_settings['portfolio-archive-readmore'] ? true : false );
						}
					} else {
						porto_the_content();
					}
					?>
					</div>
				<?php endif; ?>

				<?php porto_get_template_part( 'views/portfolios/quote' ); ?>

			<?php endif; ?>
			<?php do_action( 'porto_portfolio_after_content' ); ?>
		</div>
	</article>
	<?php
endif;
