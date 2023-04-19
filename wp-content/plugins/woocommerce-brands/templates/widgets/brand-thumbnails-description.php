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
		<li class="<?php echo esc_attr( $class ); ?>" style="width: <?php echo esc_attr( $width ); ?>%;">
			<a href="<?php echo esc_url( get_term_link( $brand->slug, 'product_brand' ) ); ?>" title="<?php echo esc_attr( $brand->name ); ?>" class="term-thumbnail">
				<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" />
			</a>
			<div id="term-<?php echo esc_attr( $brand->term_id ); ?>" class="term-description">
				<?php echo wp_kses_post( wpautop( wptexturize( $brand->description ) ) ); ?>
			</div>
		</li>

	<?php endforeach; ?>

</ul>
