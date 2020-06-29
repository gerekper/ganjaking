<?php
global $post, $porto_settings, $porto_layout, $porto_member_columns, $porto_member_role, $porto_member_view, $porto_member_overview, $porto_member_socials, $porto_member_ajax_load, $porto_member_ajax_modal, $porto_custom_zoom;
$member_columns    = $porto_member_columns ? $porto_member_columns : $porto_settings['member-columns'];
$porto_custom_zoom = $porto_custom_zoom ? $porto_custom_zoom : ( empty( $porto_settings['custom-member-zoom'] ) ? 'zoom' : 'no_zoom' );
$post_class        = array();
$post_class[]      = 'member';
$member_id         = get_the_ID();
$item_cats         = get_the_terms( $member_id, 'member_cat' );
if ( $item_cats ) {
	foreach ( $item_cats as $item_cat ) {
		$post_class[] = urldecode( $item_cat->slug );
	}
}

if ( isset( $porto_settings['member-social-link-style'] ) && 'advance' == $porto_settings['member-social-link-style'] ) {
	$social_links_adv_pos = true;
} else {
	$social_links_adv_pos = false;
}


$featured_images    = porto_get_featured_images();
$member_link        = get_post_meta( $member_id, 'member_link', true );
$show_external_link = $porto_settings['member-external-link'];
$member_show_zoom   = $porto_settings['member-zoom'];
$member_ajax        = $porto_settings['member-archive-ajax'];
$member_ajax_modal  = $porto_settings['member-archive-ajax-modal'];
if ( 'yes' == $porto_member_ajax_load ) {
	$member_ajax = true;
} elseif ( 'no' == $porto_member_ajax_load ) {
	$member_ajax = false;
}
if ( 'yes' == $porto_member_ajax_modal ) {
	$member_ajax_modal = true;
} elseif ( 'no' == $porto_member_ajax_modal ) {
	$member_ajax_modal = false;
}
$thumb_class = 'thumb-info-hide-wrapper-bg';
$view_type   = $porto_settings['member-view-type'];
if ( $porto_member_view && 'classic' != $porto_member_view ) {
	if ( 'onimage' == $porto_member_view ) {
		$view_type = 0;
	} elseif ( 'outimage' == $porto_member_view ) {
		$view_type = 2;
	} elseif ( 'outimage_cat' == $porto_member_view ) {
		$view_type = 3;
	} elseif ( 'simple' == $porto_member_view ) {
		$view_type = 4;
	}
}
if ( $view_type ) {
	if ( $porto_settings['member-archive-readmore'] ) {
		$thumb_class = 'thumb-info-centered-info';
	}
}
if ( $porto_custom_zoom && 'zoom' != $porto_custom_zoom ) {
	$thumb_class .= ' thumb-info-no-zoom';
}
$ajax_attr_escaped = '';
if ( ! ( $show_external_link && $member_link ) && $member_ajax ) {
	$member_show_zoom = false;
	if ( $member_ajax_modal ) {
		$ajax_attr_escaped = ' data-ajax-on-modal';
	} else {
		$ajax_attr_escaped = ' data-ajax-on-page';
	}
}
if ( count( $featured_images ) ) :
	$attachment_id = $featured_images[0]['attachment_id'];
	$attachment    = porto_get_attachment( $attachment_id );
	if ( 2 == $member_columns ) {
		$attachment_medium = porto_get_attachment( $attachment_id, 'full' == $porto_settings['member-image-size'] ? 'full' : 'member-two' );
	} else {
		$attachment_medium = porto_get_attachment( $attachment_id, 'full' == $porto_settings['member-image-size'] ? 'full' : 'member' );
	}
	if ( $attachment && $attachment_medium ) :
		$role         = get_post_meta( $member_id, 'member_role', true );
		$cats_escaped = '';
		$terms        = get_the_terms( $member_id, 'member_cat' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$links = array();
			foreach ( $terms as $term ) {
				$links[] = $term->name;
			}
			$cats_escaped .= join( ', ', $links );
		}
		$show_info = false;

		if ( 2 == $view_type || 3 == $view_type || 'yes' == $porto_member_overview || ( ! $porto_member_overview && $porto_settings['member-overview'] ) ) {
			$show_info = true;
		}

		$social_links        = '';
		$share_links_escaped = '';
		$border_class        = array();
		// Social Share
		$share_facebook   = get_post_meta( $member_id, 'member_facebook', true );
		$share_twitter    = get_post_meta( $member_id, 'member_twitter', true );
		$share_linkedin   = get_post_meta( $member_id, 'member_linkedin', true );
		$share_googleplus = get_post_meta( $member_id, 'member_googleplus', true );
		$share_pinterest  = get_post_meta( $member_id, 'member_pinterest', true );
		$share_email      = get_post_meta( $member_id, 'member_email', true );
		$share_phone      = get_post_meta( $member_id, 'member_phone', true );
		$share_vk         = get_post_meta( $member_id, 'member_vk', true );
		$share_xing       = get_post_meta( $member_id, 'member_xing', true );
		$share_tumblr     = get_post_meta( $member_id, 'member_tumblr', true );
		$share_reddit     = get_post_meta( $member_id, 'member_reddit', true );
		$share_vimeo      = get_post_meta( $member_id, 'member_vimeo', true );
		$share_instagram  = get_post_meta( $member_id, 'member_instagram', true );
		$share_whatsapp   = get_post_meta( $member_id, 'member_whatsapp', true );

		$target = ( isset( $porto_settings['member-social-target'] ) && $porto_settings['member-social-target'] ) ? ' target="_blank"' : '';
		if ( ( 'yes' == $porto_member_socials || ( ! $porto_member_socials && $porto_settings['member-socials'] ) ) && ( $share_facebook || $share_twitter || $share_linkedin || $share_googleplus || $share_pinterest || $share_email || $share_vk || $share_xing || $share_tumblr || $share_reddit || $share_vimeo || $share_instagram || $share_whatsapp ) ) :
			$share_links_escaped .= '<span class="thumb-info-social-icons share-links ' . ( $show_info ? '' : ' b-none' ) . ( ! $view_type ? '' : ' m-r-none m-l-none' ) . ( 3 == $view_type ? ' text-center' : '' ) . '">';

			if ( $share_facebook ) :
				$border_class[]       = 'bodr-facebook';
				$share_links_escaped .= '<a href="' . esc_url( $share_facebook ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Facebook', 'porto' ) . '" class="share-facebook">' . esc_html__( 'Facebook', 'porto' ) . '</a>';
			endif;
			if ( $share_twitter ) :
				$border_class[]       = 'bodr-twitter';
				$share_links_escaped .= '<a href="' . esc_url( $share_twitter ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Twitter', 'porto' ) . '" class="share-twitter">' . esc_html__( 'Twitter', 'porto' ) . '</a>';
			endif;
			if ( $share_linkedin ) :
				$border_class[]       = 'bodr-linkedin';
				$share_links_escaped .= '<a href="' . esc_url( $share_linkedin ) . '" ' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'LinkedIn', 'porto' ) . '" class="share-linkedin">' . esc_html__( 'LinkedIn', 'porto' ) . '</a>';
			endif;
			if ( $share_googleplus ) :
				$border_class[]       = 'bodr-googleplus';
				$share_links_escaped .= '<a href="' . esc_url( $share_googleplus ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Google +', 'porto' ) . '" class="share-googleplus">' . esc_html__( 'Google +', 'porto' ) . '</a>';
			endif;
			if ( $share_pinterest ) :
				$border_class[]       = 'bodr-pinterest';
				$share_links_escaped .= '<a href="' . esc_url( $share_pinterest ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Pinterest', 'porto' ) . '" class="share-pinterest">' . esc_html__( 'Pinterest', 'porto' ) . '</a>';
			endif;
			if ( $share_email ) :
				$border_class[]       = 'bodr-email';
				$share_links_escaped .= '<a href="mailto:' . esc_attr( $share_email ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Email', 'porto' ) . '" class="share-email">' . esc_html( $share_email ) . '</a>';
			endif;
			if ( $share_vk ) :
				$border_class[]       = 'bodr-vk';
				$share_links_escaped .= '<a href="' . esc_url( $share_vk ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'VK', 'porto' ) . '" class="share-vk">' . esc_html__( 'VK', 'porto' ) . '</a>';
			endif;
			if ( $share_xing ) :
				$border_class[]       = 'bodr-xing';
				$share_links_escaped .= '<a href="' . esc_url( $share_xing ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Xing', 'porto' ) . '" class="share-xing">' . esc_html__( 'Xing', 'porto' ) . '</a>';
			endif;
			if ( $share_tumblr ) :
				$border_class[]       = 'bodr-tumblr';
				$share_links_escaped .= '<a href="' . esc_url( $share_tumblr ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Tumblr', 'porto' ) . '" class="share-tumblr">' . esc_html__( 'Tumblr', 'porto' ) . '</a>';
			endif;
			if ( $share_reddit ) :
				$border_class[]       = 'bodr-reddit';
				$share_links_escaped .= '<a href="' . esc_url( $share_reddit ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Reddit', 'porto' ) . '" class="share-reddit">' . esc_html__( 'Reddit', 'porto' ) . '</a>';
			endif;
			if ( $share_vimeo ) :
				$border_class[]       = 'bodr-vimeo';
				$share_links_escaped .= '<a href="' . esc_url( $share_vimeo ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Vimeo', 'porto' ) . '" class="share-vimeo">' . esc_html__( 'Vimeo', 'porto' ) . '</a>';
			endif;
			if ( $share_instagram ) :
				$border_class[]       = 'bodr-instagram';
				$share_links_escaped .= '<a href="' . esc_url( $share_instagram ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'Instagram', 'porto' ) . '" class="share-instagram">' . esc_html__( 'Instagram', 'porto' ) . '</a>';
			endif;
			if ( $share_whatsapp ) :
				$border_class[]       = 'bodr-whatsapp';
				$share_links_escaped .= '<a href="whatsapp://send?text=' . esc_attr( $share_whatsapp ) . '"' . $target . ' data-tooltip data-placement="bottom" title="' . esc_attr__( 'WhatsApp', 'porto' ) . '" class="share-whatsapp" style="display:none">' . esc_html__( 'WhatsApp', 'porto' ) . '</a>';
			endif;
			if ( $share_phone ) :
				$share_links_escaped .= '<div data-tooltip data-placement="bottom" title="' . esc_attr__( 'Phone', 'porto' ) . '" class="share-phone"><i class="Simple-Line-Icons-call-out"></i>' . esc_html( $share_phone ) . '</div>';
			endif;

			$share_links_escaped .= '</span>';
		endif;
		?>

		<article <?php post_class( $post_class ); ?>>
			<?php porto_render_rich_snippets(); ?>
			<div class="member-item <?php echo 2 == $view_type ? ' align-center' : ''; ?><?php echo ! $view_type ? '' : ' member-item-' . $view_type; ?>">
				<span class="thumb-info <?php echo esc_attr( $thumb_class ); ?>">

					<span class="thumb-info-wrapper <?php echo ( isset( $social_links_adv_pos ) && $social_links_adv_pos ) ? 'member-social-adv-main' : ''; ?>">
						<?php
						$bodr_class = '';
						if ( ! empty( $border_class ) ) {
							$bodr_class = $border_class[0];
						}
						?>
						<span class="thumb-member-container <?php echo esc_attr( $bodr_class ); ?>">
							<a class="text-decoration-none member-image" href="<?php echo ! $show_external_link || ! $member_link ? get_the_permalink() : esc_url( $member_link ); ?>"<?php echo ! $ajax_attr_escaped ? '' : $ajax_attr_escaped; ?>>
							<img class="img-responsive" width="<?php echo esc_attr( $attachment_medium['width'] ); ?>" height="<?php echo esc_attr( $attachment_medium['height'] ); ?>" src="<?php echo esc_url( $attachment_medium['src'] ); ?>" alt="<?php echo esc_attr( $attachment_medium['alt'] ); ?>" />
							</a>

							<?php if ( 'yes' == $porto_member_overview || ( ! $porto_member_overview && $porto_settings['member-overview'] ) || 'yes' == $porto_member_socials || ( ! $porto_member_socials && $porto_settings['member-socials'] ) ) : ?>
								<?php if ( isset( $social_links_adv_pos ) && $social_links_adv_pos ) : ?>

								<div class="share-links post-share-advance member-share-advance">
									<div class="post-share-advance-bg">
										<?php echo porto_filter_output( $share_links_escaped ); ?>
										<i class="fas fa-share-alt"></i>
									</div>
								</div>

								<?php endif; ?>
							<?php endif; ?>
						</span>
						<a class="text-decoration-none member-info-container" href="<?php echo ! $show_external_link || ! $member_link ? esc_url( get_the_permalink() ) : esc_url( $member_link ); ?>"<?php echo ! $ajax_attr_escaped ? '' : $ajax_attr_escaped; ?>>
							<?php if ( ! $view_type ) : ?>
							<span class="thumb-info-title">
								<span class="thumb-info-inner<?php echo ( (int) $member_columns > 4 && ( 'fullwidth' == $porto_layout || 'left-sidebar' == $porto_layout || 'right-sidebar' == $porto_layout ) ) ? ' font-size-xs line-height-xs' : ''; ?>"><?php the_title(); ?></span>
								<?php
								if ( $role ) :
									?>
									<span class="thumb-info-type"><?php echo wp_kses_post( $role ); ?></span>
								<?php endif; ?>
							</span>
								<?php
							endif;
							if ( $view_type && $porto_settings['member-archive-readmore'] ) :
								?>
							<span class="thumb-info-title">
								<span class="thumb-info-inner"><?php echo ! $porto_settings['member-archive-readmore-label'] ? esc_html__( 'View More...', 'porto' ) : wp_kses_post( $porto_settings['member-archive-readmore-label'] ); ?></span>
							</span>
							<?php endif; ?>
						<?php if ( $member_show_zoom ) : ?>
							<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
						<?php endif; ?>
						</a>
					</span>
					<!--Thumb info wrapper end-->
					<!--Thumb info container -->
					<span class="thumb-info-container"> 
						<a class="text-decoration-none member-info-container" href="<?php echo ! $show_external_link || ! $member_link ? get_the_permalink() : esc_url( $member_link ); ?>"<?php echo ! $ajax_attr_escaped ? '' : $ajax_attr_escaped; ?>>
						<?php
						if ( 2 == $view_type ) :
							$show_info = true;
							if ( $member_columns > 4 ) :
								?>
								<h5 class="m-t-md m-b-none"><?php the_title(); ?></h5>
							<?php else : ?>
								<h4 class="m-t-md m-b-<?php echo ! $role ? 'sm' : 'none'; ?>"><?php the_title(); ?></h4>
								<?php
							endif;
							if ( $role ) :
								?>
								<p class="m-b-sm color-body"><?php echo wp_kses_post( $role ); ?></p>
							<?php endif; ?>
							<?php
						elseif ( 4 == $view_type ) :
							$show_info = true;
							?>
							<?php if ( $role ) : ?>
								<p class="m-t-md mb-0 color-primary"><?php echo wp_kses_post( $role ); ?></p>
							<?php endif; ?>
								<h4><?php the_title(); ?></h4>
						<?php endif; ?>
						<?php
						if ( 3 == $view_type ) :
							$show_info = true;
							?>
							<span class="thumb-info-caption">
								<span class="thumb-info-caption-title">
									<?php if ( $cats_escaped ) : ?>
										<span class="font-weight-light color-body text-md"><?php echo porto_filter_output( $cats_escaped ); ?></span>
									<?php endif; ?>
									<h4 class="m-b-none text-lg"><?php the_title(); ?></h4>
									<?php
									if ( 'yes' == $porto_member_role ) {
										echo '<p>' . wp_kses_post( $role ) . '</p>'; }
									?>
									<i class="view-more Simple-Line-Icons-arrow-right-circle font-weight-semibold"></i>
								</span>
							</span>
						<?php endif; ?>
						</a>

						<?php if ( 'yes' == $porto_member_overview || ( ! $porto_member_overview && $porto_settings['member-overview'] ) || 'yes' == $porto_member_socials || ( ! $porto_member_socials && $porto_settings['member-socials'] ) ) : ?>
						<span class="thumb-info-caption">
							<?php if ( 'yes' == $porto_member_overview || ( ! $porto_member_overview && $porto_settings['member-overview'] ) ) : ?>
								<span class="thumb-info-caption-text<?php echo ! $view_type ? '' : ' p-t-none'; ?>">
									<?php
									$show_info       = true;
									$member_overview = get_post_meta( $member_id, 'member_overview', true );
									if ( $porto_settings['member-excerpt'] ) {
										$member_overview = porto_strip_tags( porto_the_content( $member_overview, false ) );
										$limit           = $porto_settings['member-excerpt-length'] ? $porto_settings['member-excerpt-length'] : 15;
										$member_overview = explode( ' ', $member_overview, $limit );
										if ( count( $member_overview ) >= $limit ) {
											array_pop( $member_overview );
											$member_overview = implode( ' ', $member_overview ) . __( '...', 'porto' );
										} else {
											$member_overview = implode( ' ', $member_overview );
										}
									}
									echo porto_output_tagged_content( $member_overview );
									?>
								</span>
							<?php endif; ?>
							<?php
							// Social Share
							if ( isset( $social_links_adv_pos ) && ! $social_links_adv_pos ) {
								echo porto_filter_output( $share_links_escaped );
							}
							?>
						</span>
						<?php endif; ?>
						<?php if ( 4 == $view_type ) : ?>
							<a href="<?php the_permalink(); ?>" class="text-color-dark font-weight-bold text-decoration-none font-size-sm view-more"><?php esc_html_e( 'VIEW MORE', 'porto' ); ?> <i class="fas fa-arrow-right ml-1"></i></a>
						<?php endif; ?>
					</span>
				</span>
			</div>
		</article>
		<?php
	endif;
endif;
