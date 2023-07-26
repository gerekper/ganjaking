<?php
/**
 * Template for variable item add to cart section
 * @version 1.7
 */

	wp_enqueue_script( 'wc-add-to-cart-variation' );

	// Load the template	
	$attributes = $product->get_variation_attributes();
	$available_variations = $product->get_available_variations();
	//$selected_attributes = $product->get_variation_default_attributes();
	$selected_attributes = $product->get_default_attributes();
	$default_attributes = $product->get_default_attributes();

	$_json_def_var = is_array($default_attributes)? json_encode($default_attributes): false;

	$opt = (!empty($opt))? $opt : get_option('evcal_options_evcal_2');

?>
<div class='evotx_orderonline_trigger'>
	<p class='evotx_price_line'><?php echo evo_lang('Price').' '.$product->get_price_html(); ?></p>
	<a class='evcal_btn evotx_show_variations' data-defv='<?php echo $_json_def_var;?>'><?php echo eventon_get_custom_language($opt, 'evoTX_002ee','Order Now');?></a>
</div>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>
<form style='display:none' class="variations_form cart evotx_orderonline_variable" method="post" enctype='multipart/form-data' data-product_id="<?php echo $woo_product_id; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	
		<table class="variations" cellspacing="0">
		<tbody>
	<?php
		$loop = 0;foreach ($attributes as $name => $options ):
		$loop++; 
	?>
		
		<tr>
			<td class='label'><label for="<?php echo sanitize_title($name); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
			<td class="value">
				<select id="<?php echo esc_attr( sanitize_title($name) ); ?>" name="attribute_<?php echo sanitize_title($name); ?>">
						<option value=""><?php echo evo_lang( 'Choose an option' ) ?>&hellip;</option>

					<?php
		                if ( is_array( $options ) ) {

		                    if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) {
		                        $selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $name ) ];
		                    } elseif ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) {
		                        $selected_value = $selected_attributes[ sanitize_title( $name ) ];
		                    } else {
		                        $selected_value = '';
		                    }

		                    // Get terms if this is a taxonomy - ordered
		                    if ( taxonomy_exists( $name ) ) {
		                        $terms = wc_get_product_terms( $woo_product_id, $name, array( 'fields' => 'all' ) );
		                        foreach ( $terms as $term ) {
		                            if ( ! in_array( $term->slug, $options ) ) {
		                                continue;
		                            }
		                            echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
		                        }
		                    } else {
		                        foreach ( $options as $option ) {
									echo '<option value="' . esc_attr( $option ) . '" ' . selected( $selected_value, $option, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
		                        }
		                    }
		                }
		            ?>
				</select> <?php
						if ( sizeof($attributes) == $loop )
							echo '<a class="reset_variations" href="#reset">' . evo_lang( 'Clear selection' ) . '</a>';
					?></td>
				</tr>
	        <?php endforeach;?>
		</tbody>
	</table>
	<?php do_action( 'evotx_before_add_to_cart', $woo_product_id);?>
	<?php do_action( 'woocommerce_before_add_to_cart_button', $woo_product_id ); ?>

	<div class="single_variation_wrap evotx_orderonline_add_cart" style="display:none;">
		
		<div class="single_variation"></div>
		
		<div class="evotx_variations_soldout" style='display:none'><?php evo_lang_e('This option is sold out!');?></div>
		<div class='evotx_variation_purchase_section'>
			
			<?php if ( ! $product->is_sold_individually() ):?>
				<?php $tix_helper->ticket_qty_html( '' );	?>				
			<?php endif;?>

			<div class="variations_button">				
				
				<a class='evcal_btn evoAddToCart variable_add_to_cart_button' data-product_id='<?php echo $woo_product_id;?>'><?php echo evo_lang('Add to Cart');?></a>
				<input type="hidden" name="variation_id" value="" />

				<?php //woocommerce_quantity_input(array(), $product); ?>
				<input type="hidden" name="add-to-cart" value="<?php echo $woo_product_id; ?>" />
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $woo_product_id ); ?>" />
				<div class="clear"></div>
			</div>
	
			<?php 
				if($event->is_show_remaining_stock()):
					$tix_helper->remaining_stock_html($tix_inStock, $this->langX('Tickets remaining!', 'evoTX_013') );
				endif;
			?>
		
		</div>

	 	
		
	</div>
	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	
</form>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

