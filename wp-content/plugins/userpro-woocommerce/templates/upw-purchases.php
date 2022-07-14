<?php

global $post, $product, $userpro;

for($i=0;$i<count($result);$i++){

$product = wc_get_product( $result[$i] );

$total_sales = (int)get_post_meta( $result[$i], 'total_sales', true );
$total_sales = number_format( $total_sales );

$stock_state = (int)get_post_meta( $result[$i], '_stock_status', true );
if ( $stock_state == 'instock' ) {
	$stock_state = __('Instock','userpro-woocommerce');
}

?>


	<div class="upw-product ca-item">

		<div class="upw-product-image">
			
			<?php
				
				$product_link   = get_permalink( $result[$i] );
				
				if ( has_post_thumbnail( $result[$i] ) ) {

					$image = get_the_post_thumbnail( $result[$i], 'thumb' );

					echo sprintf( __('<a href="%s" class="">%s</a>','userpro-woocommerce'), $product_link, $image );

				} else {

					echo sprintf( __('<img src="%s" alt="%s" class="" />','userpro-woocommerce'), wc_placeholder_img_src(), __( 'Placeholder', 'userpro-woocommerce' ) );

				}
			?>
			
		</div>
		
		<span class="upw-product-title upw-db"><a href="<?php echo $product_link; ?>"><span><?php echo get_the_title($result[$i]); ?></span></a></span>
		<span class="upw-product-price upw-db"><?php echo $product->get_price_html(); ?></span>
		<span class="upw-total-sales upw-db" title="<?php _e('Total Sales','userpro-woocommerce'); ?>"><i class="upw-cart"></i><?php echo $total_sales; ?></span>
		<span class="upw-stock-sate upw-db"><?php echo $stock_state; ?></span>
	</div>

<?php } ?>
	
