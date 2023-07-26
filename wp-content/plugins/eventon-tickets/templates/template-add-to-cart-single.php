<?php
/**
 * Single Ticket add to cart section 
 * @updated 2.2
 */

$GLOBALS['product'] = $product;

?>
<div class='tx_single'>
<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>
<?php

	$max_quantity = ($tix_inStock) ? 
		( is_numeric($tix_inStock)? $tix_inStock:''): 
		($product->backorders_allowed() ? '' : $product->get_stock_quantity());

?>

<form class='tx_orderonline_single' data-producttype='single' method="post" enctype='multipart/form-data'>
	<?php do_action( 'woocommerce_before_add_to_cart_button', $woo_product_id ); ?>
	<div class='tx_orderonline_add_cart'>

		<?php do_action('evotx_before_single_addtocart', $woo_product_id, $object->event_id);?>

		<?php $tix_helper->print_nonce_field();?>
		
		<div class='evotx_hidable_section'>
			<?php

				$striked_price = (!empty($product->get_sale_price()) && $product->get_regular_price() != $product->get_sale_price()) ? $product->get_regular_price(): '';

				$base_price =  wc_get_price_to_display( $product ); //$product->get_price()
				$label_adds = '';
				
				// Base Price HTML
				if($event->check_yn('_name_yprice')){
					$base_price = 0;
					if( $min_nyp = $event->get_prop('_evotx_nyp_min')){
						$base_price = $min_nyp;
						$label_adds = evo_lang('Minimum Price is').' '. $tix_helper->convert_to_currency($base_price);
					}					
				} 

				$base_price = apply_filters('evotx_single_prod_price', $base_price, $object);
				$striked_price = apply_filters('evotx_single_prod_striked_price', $striked_price, $base_price, $object);

				$label_adds = apply_filters('evotx_single_prod_label_add', $label_adds, $product->get_price(), $object);

				$tix_helper->base_price_html($base_price, '', $striked_price, $label_adds , $event->check_yn('_name_yprice'));
			?>
		
			<?php if ( ! $product->is_sold_individually() ): ?>
				<?php $tix_helper->ticket_qty_html( (!empty($max_quantity)? $max_quantity:'na') );	?>

			<?php else:?>
				<?php $tix_helper->ticket_qty_one_hidden();?>
			<?php endif;?>
		
			<?php $tix_helper->total_price_html( $base_price, '', $woo_product_id);?>
			
		 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
		 	
		 	<?php 
		 	$tix_helper->add_to_cart_btn_html(
		 		'evotx_addtocart button alt'. ($product->is_sold_individually()? ' si':''), 
		 		array(
			 		'product_id'=>$woo_product_id,
			 		'l'=>EVO()->lang
			 	));
			 ?>
					
			<?php 
			// show remaining tickets
				if($event->is_show_remaining_stock($tix_inStock)):
					$tix_helper->remaining_stock_html($tix_inStock, $this->langX('Tickets remaining!', 'evoTX_013') );
				endif;
			?>

			<?php
			// already purchased message
			if($event->check_yn('_already_purchased')){
				if($event->has_user_purchased_tickets()){
					?><p class='evotx_already_purchased'><?php evo_lang_e('You have already purchased this ticket');?>!</p><?php
				}
			}

			?>
		
		</div>	 	
		
		<?php do_action('evotx_after_single_addtocart', $woo_product_id, $object->event_id);?>

 	</div>
 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
</div>