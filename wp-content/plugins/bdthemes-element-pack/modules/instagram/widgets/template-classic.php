
<?php

	if ( $_REQUEST['show_lightbox'] ) {
		$link_url =  esc_url( $insta_feeds[$i]['image']['large'] );
	} else {
		$link_url = esc_url( $insta_feeds[ $i ]['link'] );
	}

	?>

	<div class="bdt-instagram-item-wrapper feed-type-<?php echo esc_attr( $insta_feeds[ $i ]['post_type'] ); ?>">
		<div class="bdt-instagram-item bdt-position-relative bdt-scrollspy-inview bdt-animation-fade">
			<div class="bdt-instagram-thumbnail">
				<img src="<?php echo esc_attr( $insta_feeds[$i]['image']['medium'] ); ?>" alt="<?php esc_html_e( 'Image by:', 'bdthemes-element-pack' ); ?> <?php echo esc_attr( $insta_feeds[ $i ]['user']['full_name'] ); ?> " loading="lazy">
				
			</div>

			<?php if ( $_REQUEST['show_lightbox'] or $_REQUEST['show_link'] ) : ?>
			<a class="bdt-position-center bdt-lightbox-icon bdt-icon" href="<?php echo $link_url; ?>" data-elementor-open-lightbox="no">

				<svg xmlns="http://www.w3.org/2000/svg" height="32" width="32" x="0px" y="0px" viewBox="0 0 42 42" xml:space="preserve"><polygon points="42,19 23,19 23,0 19,0 19,19 0,19 0,23 19,23 19,42 23,42 23,23 42,23 "/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>			            			
			
			</a>
			<?php endif; ?>

			<div class='bdt-instagram-like-comment'>
				<?php if ( $_REQUEST['show_like'] ) : ?>
					<span class="bdt-icons"><span class='far fa-heart'></span> <b><?php echo esc_attr( $insta_feeds[ $i ]['like'] ); ?></b></span>
				<?php endif; ?>							
				<?php if ( $_REQUEST['show_comment'] ) : ?>
					<span class="bdt-icons"><span class='far fa-comment'></span> <b><?php echo esc_attr( $insta_feeds[ $i ]['comment']['count'] ); ?></b></span>
				<?php endif; ?>							
			</div>

		</div>

		
	</div>
						