<?php
/**
 * Show a grid of thumbnails
 */
?>
<ul class="brand-thumbnails-description">

	<?php foreach ( $brands as $index => $brand ) :

		$thumbnail = get_brand_thumbnail_url( $brand->term_id, apply_filters( 'woocommerce_brand_thumbnail_size', 'shop_catalog' ) );

		if ( ! $thumbnail )
			$thumbnail = wc_placeholder_img_src();

		$class = '';

		if ( $index == 0 || $index % $columns == 0 )
			$class = 'first';
		elseif ( ( $index + 1 ) % $columns == 0 )
			$class = 'last';

		$width = floor( ( ( 100 - ( ( $columns - 1 ) * 2 ) ) / $columns ) * 100 ) / 100;
		?>
		<li class="<?php echo $class; ?>" style="width: <?php echo $width; ?>%;">
			<a href="<?php echo get_term_link( $brand->slug, 'product_brand' ); ?>" title="<?php echo $brand->name; ?>" class="term-thumbnail">
				<img src="<?php echo $thumbnail; ?>" alt="<?php echo $brand->name; ?>" />
			</a>
			<div id="term-<?php echo $brand->term_id; ?>" class="term-description">
				<?php echo wpautop( wptexturize( $brand->description ) ); ?>
			</div>
		</li>

	<?php endforeach; ?>

</ul>
