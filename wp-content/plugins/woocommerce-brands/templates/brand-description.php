<?php $image_size = wc_get_image_size( 'shop_catalog' ); ?>
<div class="term-description brand-description">

	<?php if ( $thumbnail ) : ?>

		<img src="<?php echo esc_url( $thumbnail ); ?>" alt="Thumbnail" class="wp-post-image alignright fr brand-thumbnail" width="<?php echo esc_attr( $image_size['width'] ) ?>" />

	<?php endif; ?>
	
	<div class="text">

		<?php echo do_shortcode( wpautop( wptexturize( term_description() ) ) ); ?>
	
	</div>

</div>