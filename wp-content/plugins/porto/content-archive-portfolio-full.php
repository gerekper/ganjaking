<?php

global $porto_settings, $post, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal;

$portfolio_layout = 'full';

$post_class   = array();
$post_class[] = 'portfolio';
$post_class[] = 'portfolio-' . $portfolio_layout;
$item_cats    = get_the_terms( $post->ID, 'portfolio_cat' );
if ( $item_cats ) :
	foreach ( $item_cats as $item_cat ) {
		$post_class[] = urldecode( $item_cat->slug );
	}
endif;

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
$portfolio_link = get_post_meta( $post->ID, 'portfolio_link', true );

$show_external_link        = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;
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

$count = count( $featured_images );

$classes = array();
if ( $portfolio_thumb_bg ) {
	$classes[] = 'thumb-info-' . $portfolio_thumb_bg;
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

$class = implode( ' ', $classes );

$zoom_src   = array();
$zoom_title = array();

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
?>

<article <?php post_class( $post_class ); ?>>

	<?php if ( $count ) : ?>
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
			<span class="thumb-info m-b-xl <?php echo esc_attr( $class ); ?>">
				<span class="thumb-info-wrapper">
					<?php
					if ( $count > 1 && $portfolio_show_all_images ) :
						?>
						<div class="porto-carousel owl-carousel m-b-none nav-inside show-nav-hover" data-plugin-options="<?php echo esc_attr( $options ); ?>"><?php endif; ?>
						<?php
						$i = 0;
						foreach ( $featured_images as $featured_image ) :
							$attachment_id = $featured_image['attachment_id'];
							if ( ! isset( $image_size ) ) {
								$image_size = 'portfolio-full';
							}

							$attachment             = porto_get_attachment( $attachment_id );
							$attachment_full        = porto_get_attachment( $attachment_id, $image_size );
							$attachment_alternative = porto_get_attachment( $attachment_id, 'portfolio-large' );
							if ( $attachment && $attachment_full ) :
								$zoom_src[]   = $attachment['src'];
								$zoom_title[] = $attachment['caption'];
								?>
								<img class="img-responsive"<?php echo isset( $attachment_alternative ) ? ' srcset="' . esc_url( $attachment_alternative['src'] ) . ' 575w, ' . esc_url( $attachment_full['src'] ) . ' ' . esc_attr( $attachment_full['width'] ) . 'w" sizes="(max-width: 575px) ' . esc_attr( $attachment_alternative['width'] ) . 'px, ' . esc_attr( $attachment_full['width'] ) . 'px"' : ''; ?> src="<?php echo esc_url( $attachment_full['src'] ); ?>" alt="<?php echo esc_attr( $attachment_full['alt'] ); ?>" width="<?php echo esc_attr( $attachment_full['width'] ); ?>" height="<?php echo esc_attr( $attachment_full['height'] ); ?>" />
								<?php
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
					if ( 1 != $count && $portfolio_show_all_images ) :
						?>
						</div><?php endif; ?>
					<?php if ( $portfolio_show_link || $portfolio_show_zoom ) : ?>
						<span class="thumb-info-action">
							<?php if ( $portfolio_show_link ) : ?>
								<span class="thumb-info-action-icon thumb-info-action-icon-primary"><i class="fas <?php echo ! $ajax_attr ? 'fa-link' : 'fa-plus-square'; ?>"></i></span>
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
	<?php endif; ?>

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

	<?php if ( $ajax_attr || $portfolio_show_link_zoom ) : ?>
		<h4 class="entry-title"><?php the_title(); ?></h4>
	<?php else : ?>
		<h4 class="entry-title"><a href="<?php echo ! $show_external_link || ! $portfolio_link ? get_the_permalink() : esc_url( $portfolio_link ); ?>"><?php the_title(); ?></a></h4>
	<?php endif; ?>

	<?php if ( ! empty( $porto_settings['portfolio-show-content'] ) ) : ?>
	<div class="m-t-lg">
		<?php
		porto_render_rich_snippets( false );
		if ( ! empty( $porto_settings['portfolio-excerpt'] ) ) {
			if ( has_excerpt() ) {
				the_excerpt();
			} else {
				echo porto_get_excerpt( $porto_settings['portfolio-excerpt-length'], false );
			}
		} else {
			echo '<div class="entry-content">';
			porto_the_content();
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
			echo '</div>';
		}
		?>
	</div>
	<?php endif; ?>

	<?php porto_get_template_part( 'views/portfolios/meta' ); ?>

</article>
