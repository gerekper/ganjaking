<?php
/**
 * Brand A-Z listing
 *
 * @usedby [product_brand_list]
 */
?>
<div id="brands_a_z">

	<ul class="brands_index">
		<?php
		foreach ( $index as $i ) {
			if ( isset( $product_brands[ $i ] ) ) {
				echo '<li><a href="#brands-' . $i . '">' . $i . '</a></li>';
			} elseif ( $show_empty ) {
				echo '<li><span>' . $i . '</span></li>';
			}
		}
		?>
	</ul>

	<?php foreach ( $index as $i ) if ( isset( $product_brands[ $i ] ) ) : ?>

		<h3 id="brands-<?php echo $i; ?>"><?php echo $i; ?></h3>

		<ul class="brands">
			<?php
			foreach ( $product_brands[ $i ] as $brand ) {
				echo '<li><a href="' . get_term_link( $brand->slug, 'product_brand' ) . '">' . $brand->name . '</a></li>';
			}
			?>
		</ul>

		<?php if ( $show_top_links ) : ?>
			<a class="top" href="#brands_a_z"><?php _e( '&uarr; Top', 'woocommerce-brands' ) ?></a>
		<?php endif; ?>

	<?php endif; ?>

</div>