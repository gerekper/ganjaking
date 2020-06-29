<?php 
	global $post,$product;
	$background_image='';
	$image_html = "";
	if ( has_post_thumbnail() ) {
			$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
	} else if ( wc_placeholder_img_src() ) {
			$image_html = wc_placeholder_img( 'shop_catalog' );
	}
	$product_id=get_the_ID();
	$data_attr='';
	if($layout=='metro'){
		if ( has_post_thumbnail() ) {
			$data_attr=pt_plus_loading_bg_image($product_id);
		}else{
			$data_attr = pt_plus_loading_image_grid($product_id,'background');
		}		
	}
	$catalog_mode='';
	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
?>
<div  <?php post_class("post item  product-".$product_style); ?> <?php echo $data_attr; ?>>
	<?php 
	
		echo '<figure class="product-image">';
			do_action('pt_plus_product_badge');
			$attachment_ids = $product->get_gallery_image_ids();
				if ($attachment_ids) {
				if($layout!='metro'){
						echo '<a href="'.esc_url(get_the_permalink()).'" title="'. the_title_attribute(array('echo' => 0)).'" class="fade">'.$image_html.'</a>';
						if ( ! get_post_meta( $attachment_ids[0], '_woocommerce_exclude_image', true ) ) { 
							echo '<a href="'.esc_url(get_the_permalink()).'" title="'. the_title_attribute(array('echo' => 0)).'" class="fade">'.wp_get_attachment_image( $attachment_ids[0], 'shop_catalog' ).'</a>';	
						}			
				}
				} else {
					if($layout!='metro'){
						if ( has_post_thumbnail() ) {
							echo '<a href="'.esc_url(get_the_permalink()).'" class="blog-media image-loaded" title="'. the_title_attribute(array('echo' => 0)).'">';
							include THEPLUS_PLUGIN_PATH. 'vc_elements/products/format-image.php';
							echo '</a>';
						}else{ 
							echo '<div class="blog-media image-loaded">';
								echo pt_plus_loading_image_grid($product_id);
							echo '</div>';
						}
					}
				 }
				
				if ($product_style == 'style-1' && $layout!='metro') {
					echo '<div class="wrapper-cart-hover-hidden add-cart-btn">';
					$_product = wc_get_product( $product_id );
						if( $_product->is_type( 'simple' ) ) {
							echo '<div class="wcmp-add-to-cart" ><a title="'.esc_attr__('Add to Cart','pt_theplus').'" href="?add-to-cart='.esc_attr($product_id).'" rel="nofollow" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button product_type_simple ajax_add_to_cart">'.esc_html__('Add to Cart','pt_theplus').'</a></div>';
						}else{
						echo '<div class="wcmp-add-to-cart" ><a rel="nofollow" href="'.esc_url(get_the_permalink()).'" data-quantity="1" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button button product_type_simple " data-added-text="">'.esc_html__('Read more','pt_theplus').'</a></div>';
						}
					echo '</div>';
				}
		echo '</figure>';
	
		echo '<header class="post-title">';
			echo '<h3><a href="'.esc_url(get_the_permalink()).' " title="'.esc_attr(get_the_title()).'">'.esc_html(get_the_title()).'</a></h3>';
				echo '<div class="wrapper-cart-price">';					
					$product = new WC_Product( $product_id );
					echo '<span class="price">'.$product->get_price_html().'</span>';
					if ($product_style == 'style-2' || ($product_style == 'style-1' && $layout=='metro')) {
						echo '<div class="wrapper-relative wrapper-cart-button">';
							echo '<div class="wrapper-cart-hover-hidden add-cart-btn">';
								$_product = wc_get_product( $product_id );
							if( $_product->is_type( 'simple' ) ) {
								echo '<a title="'.esc_attr__('Add to Cart','pt_theplus').'" href="?add-to-cart='.esc_attr($product_id).'" rel="nofollow" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button product_type_simple ajax_add_to_cart">'.esc_html__('Add to Cart','pt_theplus').'</a>';
							}else{
								echo '<a rel="nofollow" href="'.esc_url(get_the_permalink()).'" data-quantity="1" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button button product_type_simple " data-added-text="">'.esc_html__('Read more','pt_theplus').'</a>';
							}
							echo '</div>';
						echo '</div>';
					}
					if ($product_style == 'style-3') {
							echo '<div class="wrapper-cart-hover-hidden add-cart-btn">';
								$_product = wc_get_product( $product_id );
							if( $_product->is_type( 'simple' ) ) {
								echo '<a title="'.esc_attr__('Add to Cart','pt_theplus').'" href="?add-to-cart='.esc_attr($product_id).'" rel="nofollow" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button product_type_simple ajax_add_to_cart">'.esc_html__('Add to Cart','pt_theplus').'</a>';
							}else{
								echo '<a rel="nofollow" href="'.esc_url(get_the_permalink()).'" data-quantity="1" data-product_id="'.esc_attr($product_id).'" data-product_sku="" class="add_to_cart add_to_cart_button button product_type_simple " data-added-text="">'.esc_html__('Read more','pt_theplus').'</a>';
							}
							echo '</div>';
					}
				echo '</div>';				
		echo '</header>';
echo '</div>';