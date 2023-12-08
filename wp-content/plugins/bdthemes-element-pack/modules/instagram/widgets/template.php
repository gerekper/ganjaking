
<?php

	if ( $_REQUEST['show_lightbox'] ) {
		$link_url =  esc_url( $insta_feeds[$i]['image']['large'] );
	} else {
		$link_url = esc_url( $insta_feeds[ $i ]['link'] );
	}

	?>

	<div class="bdt-instagram-item-wrapper feed-type-<?php echo esc_attr( $insta_feeds[ $i ]['post_type'] ); ?>">
		<div class="bdt-instagram-item bdt-transition-toggle bdt-position-relative bdt-scrollspy-inview bdt-animation-fade">
			<div class="bdt-instagram-thumbnail">
				<img src="<?php echo esc_url($insta_feeds[$i]['image']['medium']); ?>" alt="<?php esc_html_e( 'Image by:', 'bdthemes-element-pack' ); ?> <?php echo esc_html($insta_feeds[ $i ]['user']['full_name']); ?> " loading="lazy">
				
			</div>

			<?php if ( $_REQUEST['show_lightbox'] or $_REQUEST['show_link'] ) : ?>
			<a href="<?php echo $link_url; ?>" data-elementor-open-lightbox="no">

				<div class='bdt-transition-fade bdt-inline-clip bdt-position-cover bdt-overlay bdt-overlay-default '>
					<span class='bdt-position-center' bdt-overlay-icon></span>


					<div class='bdt-instagram-like-comment bdt-flex-center bdt-child-width-auto bdt-grid'>
						<?php if ( $_REQUEST['show_like'] ) : ?>
							<span><span class='ep-icon-heart-empty'></span> <b><?php echo esc_attr( $insta_feeds[ $i ]['like'] ); ?></b></span>
						<?php endif; ?>							
						<?php if ( $_REQUEST['show_comment'] ) : ?>
							<span><span class='ep-icon-bubble'></span> <b><?php echo esc_attr( $insta_feeds[ $i ]['comment']['count'] ); ?></b></span>
						<?php endif; ?>							
					</div>

				</div>
				            			
			
			</a>
			<?php endif; ?>

		</div>

		
	</div>
						