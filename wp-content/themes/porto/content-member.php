<?php
global $porto_settings, $porto_layout, $post, $porto_member_socials;
$member_advance_layout = isset( $porto_settings['member-page-style'] ) ? $porto_settings['member-page-style'] : false;

$member_cls = array( 'member' );
$item_cats  = get_the_terms( get_the_ID(), 'member_cat' );
if ( $item_cats ) {
	foreach ( $item_cats as $item_cat ) {
		$member_cls[] = urldecode( $item_cat->slug );
	}
}
?>
<article <?php post_class( $member_cls ); ?>>

<?php
	$social_share = isset( $porto_member_socials ) && 'no' === $porto_member_socials ? false : true;
	$share_links  = '';
	// Social Share
	$member_id   = $post->ID;
	$member_link = get_post_meta( $member_id, 'member_link', true );
	$target      = ( isset( $porto_settings['member-social-target'] ) && $porto_settings['member-social-target'] ) ? ' target="_blank"' : '';

if ( $social_share ) {
	if ( isset( $porto_settings['single-member-social-link-style'] ) && 'advance' == $porto_settings['single-member-social-link-style'] ) {
		$social_links_adv_pos = true;
	} else {
		$social_links_adv_pos = false;
	}

	if ( ! empty( $porto_settings['member-social-nofollow'] ) ) {
		$target .= ' rel="nofollow"';
	}

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

	if ( $share_facebook || $share_twitter || $share_linkedin || $share_googleplus || $share_pinterest || $share_email || $share_vk || $share_xing || $share_tumblr || $share_reddit || $share_vimeo || $share_instagram || $share_whatsapp ) :

			$share_links .= '<div class="member-share-links share-links">';

		if ( $share_facebook ) :
			$share_links .= '<a href="' . esc_url( $share_facebook ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Facebook', 'porto' ) . '" class="share-facebook">' . esc_html__( 'Facebook', 'porto' ) . '</a>';
			endif;

		if ( ! empty( $porto_settings['member-social-target'] ) && empty( $porto_settings['member-social-nofollow'] ) ) {
			$target .= ' rel="noopener noreferrer"';
		}

		if ( $share_twitter ) :
			$share_links .= '<a href="' . esc_url( $share_twitter ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Twitter', 'porto' ) . '" class="share-twitter">' . esc_html__( 'Twitter', 'porto' ) . '</a>';
				endif;
		if ( $share_linkedin ) :
			$share_links .= '<a href="' . esc_url( $share_linkedin ) . '" ' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'LinkedIn', 'porto' ) . '" class="share-linkedin">' . esc_html__( 'LinkedIn', 'porto' ) . '</a>';
				endif;
		if ( $share_googleplus ) :
			$share_links .= '<a href="' . esc_url( $share_googleplus ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Google +', 'porto' ) . '" class="share-googleplus">' . esc_html__( 'Google +', 'porto' ) . '</a>';
				endif;
		if ( $share_pinterest ) :
			$share_links .= '<a href="' . esc_url( $share_pinterest ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Pinterest', 'porto' ) . '" class="share-pinterest">' . esc_html__( 'Pinterest', 'porto' ) . '</a>';
				endif;
		if ( $share_email ) :
			$share_links .= '<a href="mailto:' . esc_attr( $share_email ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Email', 'porto' ) . '" class="share-email">' . esc_html( $share_email ) . '</a>';
				endif;
		if ( $share_vk ) :
			$share_links .= '<a  href="' . esc_url( $share_vk ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'VK', 'porto' ) . '" class="share-vk">' . esc_html__( 'VK', 'porto' ) . '</a>';
				endif;
		if ( $share_xing ) :
			$share_links .= '<a  href="' . esc_url( $share_xing ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Xing', 'porto' ) . '" class="share-xing">' . esc_html__( 'Xing', 'porto' ) . '</a>';
				endif;
		if ( $share_tumblr ) :
			$share_links .= '<a  href="' . esc_url( $share_tumblr ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Tumblr', 'porto' ) . '" class="share-tumblr">' . esc_html__( 'Tumblr', 'porto' ) . '</a>';
				endif;
		if ( $share_reddit ) :
			$share_links .= '<a  href="' . esc_url( $share_reddit ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Reddit', 'porto' ) . '" class="share-reddit">' . esc_html__( 'Reddit', 'porto' ) . '</a>';
				endif;
		if ( $share_vimeo ) :
			$share_links .= '<a  href="' . esc_url( $share_vimeo ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Vimeo', 'porto' ) . '" class="share-vimeo">' . esc_html__( 'Vimeo', 'porto' ) . '</a>';
				endif;
		if ( $share_instagram ) :
			$share_links .= '<a  href="' . esc_url( $share_instagram ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Instagram', 'porto' ) . '" class="share-instagram">' . esc_html__( 'Instagram', 'porto' ) . '</a>';
				endif;
		if ( $share_whatsapp ) :
			$share_links .= '<a href="whatsapp://send?text=' . esc_attr( $share_whatsapp ) . '"' . $target . ' data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'WhatsApp', 'porto' ) . '" class="share-whatsapp" style="display:none">' . esc_html__( 'WhatsApp', 'porto' ) . '</a>';
				endif;
		if ( $share_phone ) :
			$share_links .= '<div data-bs-tooltip data-bs-placement="bottom" title="' . esc_attr__( 'Phone', 'porto' ) . '" class="share-phone"><i class="Simple-Line-Icons-call-out"></i>' . esc_html( $share_phone ) . '</div>';
				endif;
			$share_links .= '</div>';
		endif;
}
?>

	<?php if ( ! $member_advance_layout ) : ?>

		<?php
		if ( is_singular( 'member' ) && 'widewidth' === $porto_layout ) {
			echo '<div class="container m-t-lg">';}
		?>
	<div class="member-overview row">
		<?php
		// Member Slideshow
		$slideshow_type = get_post_meta( $post->ID, 'slideshow_type', true );
		$video_code     = get_post_meta( $post->ID, 'video_code', true );
		if ( ! $slideshow_type ) {
			$slideshow_type = 'images';
		}
		$featured_images = porto_get_featured_images();
		$image_count     = count( $featured_images );
		if ( ( 'images' == $slideshow_type && $image_count ) || ( 'video' == $slideshow_type && $video_code ) ) :
			?>
		<div class="col-md-5<?php echo ! isset( $member_counter ) || 0 === $member_counter % 2 ? ' order-md-2' : ''; ?> mb-4 mb-lg-0">
			<?php if ( 'images' == $slideshow_type && $image_count ) : ?>
				<div class="member-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
					<?php if ( $social_share && isset( $social_links_adv_pos ) && $social_links_adv_pos ) : ?>
						<div class="share-links post-share-advance member-share-advance">
							<div class="post-share-advance-bg">
								<?php echo porto_filter_output( $share_links ); ?>
								<i class="fas fa-share-alt"></i>
							</div>
						</div>
					<?php endif; ?>
					<div class="member-slideshow porto-carousel owl-carousel has-ccols ccols-1">
						<?php
						foreach ( $featured_images as $featured_image ) {
							$attachment_medium = porto_get_attachment( $featured_image['attachment_id'], 'blog-masonry' );
							$attachment        = porto_get_attachment( $featured_image['attachment_id'] );
							if ( $attachment ) {
								$placeholder = porto_generate_placeholder( $attachment_medium['width'] . 'x' . $attachment_medium['height'] );
								?>
								<div>
									<div class="img-thumbnail">
										<?php
											echo wp_get_attachment_image(
												$featured_image['attachment_id'],
												'blog-masonry',
												false,
												array(
													'class' => 'owl-lazy img-responsive',
													'data-src' => esc_url( $attachment_medium['src'] ),
													'src' => porto_is_amp_endpoint() ? esc_url( $attachment_medium['src'] ) : esc_url( $placeholder[0] ),
												)
											);
										?>
										<?php if ( ! empty( $porto_settings['member-zoom'] ) ) : ?>
											<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-search"></i></span>
											<?php
											if ( ! is_singular( 'member' ) ) :
												?>
												<a class="link" href="<?php the_permalink(); ?>"><i class="fas fa-link"></i></a><?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			<?php endif; ?>
			<?php
			if ( 'video' == $slideshow_type && $video_code ) :
				wp_enqueue_script( 'jquery-fitvids' );
				?>
				<div class="member-image single">
					<div class="img-thumbnail fit-video">
						<?php echo do_shortcode( $video_code ); ?>
					</div>
				</div>
				<?php
			endif;
			if ( $social_share && isset( $porto_settings['member-socials-pos'] ) && 'below_thumb' == $porto_settings['member-socials-pos'] && isset( $social_links_adv_pos ) && ! $social_links_adv_pos ) :
				?>
			<div class="share-links-block">
				<h5><?php esc_html_e( 'Follow Me', 'porto' ); ?></h5>
				<?php echo porto_filter_output( $share_links ); ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="col-md-7">
		<?php else : ?>
		<div class="col-md-12">
		<?php endif; ?>
			<?php
			$firstname = get_post_meta( $member_id, 'member_firstname', true );
			$lastname  = get_post_meta( $member_id, 'member_lastname', true );
			$role      = get_post_meta( $member_id, 'member_role', true );
			?>
			<h2 class="entry-title<?php echo ! $role ? '' : ' shorter'; ?>"><strong><?php echo esc_html( $firstname . ' ' . $lastname ); ?></strong></h2>
			<?php porto_render_rich_snippets( false ); ?>
			<?php echo ! $role ? '' : '<h4 class="member-role">' . esc_html( $role ) . '</h4>'; ?>

			<?php if ( $social_share && isset( $porto_settings['member-socials-pos'] ) && 'before' == $porto_settings['member-socials-pos'] && isset( $social_links_adv_pos ) && ! $social_links_adv_pos ) : ?>
				<div class="share-links-block mb-4">
					<h5><?php esc_html_e( 'Follow Me', 'porto' ); ?></h5>
					<?php echo porto_filter_output( $share_links ); ?>
				</div>
			<?php endif; ?>
			<?php
				echo porto_output_tagged_content( get_post_meta( $post->ID, 'member_overview', true ) );
			?>
			<?php if ( $member_link || ! is_singular( 'member' ) || ( $social_share && $share_links && isset( $porto_settings['member-socials-pos'] ) && '' == $porto_settings['member-socials-pos'] && isset( $social_links_adv_pos ) && ! $social_links_adv_pos ) ) : ?>
				<hr class="tall">
			<?php endif; ?>
			<div class="row align-items-center">
			<?php

			if ( $member_link || ! is_singular( 'member' ) ) :
				?>
				<div class="col-lg-6">
				<?php if ( $member_link ) : ?>
					<a<?php echo porto_filter_output( $target ); ?> class="btn btn-dark btn-modern mb-3 mb-lg-0" href="<?php echo esc_url( $member_link ); ?>"><?php esc_html_e( 'Get In Touch', 'porto' ); ?></a>
				<?php endif; ?>
				<?php if ( ! is_singular( 'member' ) ) : ?>
					<a class="btn btn-primary btn-modern mb-3 mb-lg-0" href="<?php the_permalink(); ?>"><?php esc_html_e( 'More', 'porto' ); ?></a>
				<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( $social_share && isset( $porto_settings['member-socials-pos'] ) && '' == $porto_settings['member-socials-pos'] && isset( $social_links_adv_pos ) && ! $social_links_adv_pos ) : ?>
				<div class="col-lg-6 share-links-block<?php echo ! $member_link ? '' : ' d-flex justify-content-lg-end'; ?>">
					<h5><?php esc_html_e( 'Follow Me', 'porto' ); ?></h5>
					<?php echo porto_filter_output( $share_links ); ?>
				</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
		<?php
		if ( is_singular( 'member' ) && 'widewidth' === $porto_layout ) {
			echo '</div>';}
		?>
<?php endif; ?>
	<?php if ( is_singular( 'member' ) && ( get_the_content() || porto_is_elementor_preview() ) ) : ?>
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
	<?php endif; ?>
</article>
