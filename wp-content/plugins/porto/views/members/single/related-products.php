<?php
$product_ids = get_post_meta( get_the_ID(), 'member_products', true );
$products    = porto_get_products_by_ids( $product_ids );
if ( $products->have_posts() ) : ?>
	<div class="post-gap"></div>
	<div class="related products">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="sub-title"><?php printf( esc_html__( 'My %1$sProducts%2$s', 'porto' ), '<strong>', '</strong>' ); ?></h4>
		<div class="slider-wrapper">
			<?php
			global $porto_woocommerce_loop;
			$porto_woocommerce_loop['view']       = 'products-slider';
			$porto_woocommerce_loop['navigation'] = false;
			$porto_woocommerce_loop['pagination'] = true;
			woocommerce_product_loop_start();
			?>
			<?php
			while ( $products->have_posts() ) :
				$products->the_post();
				?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endwhile; // end of the loop. ?>
			<?php
			woocommerce_product_loop_end();
			?>
		</div>
	</div>
<?php endif;
wp_reset_postdata();
?>
