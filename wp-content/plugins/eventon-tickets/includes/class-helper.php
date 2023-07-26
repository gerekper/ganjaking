<?php
/**
 * Ticket Addon Helpers for ticket addon extensions
 * @updated 1.8
 */

class evotx_helper extends evo_helper{

	// select data html content
		function print_select_data_element( $args){
			$dd = array_merge(array(
				'class'=>'evotx_other_data',
				'data'=>array()
			), $args);
			extract($dd);

			echo "<div class='{$class}' ". $this->array_to_html_data( $data ) ."></div>";
		}
		
	// convert a value to proper currency
		function convert_to_currency($price, $symbol = true){	
			extract( apply_filters( 'wc_price_args', wp_parse_args( array(), array(
		        'ex_tax_label'       => false,
		        'currency'           => '',
		        'decimal_separator'  => wc_get_price_decimal_separator(),
		        'thousand_separator' => wc_get_price_thousand_separator(),
		        'decimals'           => wc_get_price_decimals(),
		        'price_format'       => get_woocommerce_price_format(),
		    ) ) ) );

			$sym = $symbol? html_entity_decode(get_woocommerce_currency_symbol($currency)):'';

			if(empty($price)) $price = 0;
			$negative = $price < 0 ? true: false;

			// remove commas in price
			$price = str_replace(',', '', $price);


			
			if( $negative )  $price = floatval( $price ) * -1;
			$price = floatval( $price);

			$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

			

			if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
		        $price = wc_trim_zeros( $price );
		    }

		    $return = ( $negative ? '-' : '' ) . sprintf( $price_format, $sym, $price );

		    if ( $ex_tax_label && wc_tax_enabled() ) {
		        $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		    }


			return $return;
		}

	// HTML: remaining stock
	// @added 1.7
		function remaining_stock_html($stock, $text='', $visible=true){
			$remaining_count = apply_filters('evotx_remaining_stock', (int)$stock);

			// text string
			if(empty($text)){
				$text = $remaining_count>1? 
					EVO()->frontend->lang('','evoTX_013','Tickets Remaining!') : 
					evo_lang('Ticket Remaining!');
			} 

			echo "<p class='evotx_remaining' data-count='{$remaining_count}' style='display:". ($visible?'block':'none')."'>
				<span class='evotx_remaining_stock'>";
			echo "<span>" . $remaining_count . "</span> ";
			echo $text;
			echo "</span></p>";
		}

	// HTML Price 
	// @updated: 1.7.3
		function base_price_html($price, $unqiue_class='', $striked_price = '', $label_additions='', $is_name_yp=false){

			if(empty($price)) $price = 0;


			$strike_  = (!empty($striked_price) && $striked_price != $price)? "<span class='strikethrough' style='text-decoration: line-through'>". $this->convert_to_currency($striked_price).'</span> ':'';

			$label_addition  = !empty($label_additions)? " <span class='label_add' style='font-style:italic; text-transform:none;opacity:0.6'>". $label_additions.'</span> ':'';
			?>
			<div itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
				<p itemprop="price" class='price tx_price_line <?php echo $unqiue_class;?> <?php echo $is_name_yp? 'nyp':''?>' content='<?php echo $price;?>'>
					<meta itemprop='priceCurrency' content='<?php echo get_woocommerce_currency_symbol();?>'/>
					<meta itemprop='availability' content='http://schema.org/InStock'/>
					<span class='evo_label'><?php echo $is_name_yp ? evo_lang('Name your price'): evo_lang('Price');?><?php echo $label_addition;?></span> 

					<?php
					if($is_name_yp){
						?>
						<span class='nyp_val value' style='align-items: center;display: flex;' data-sp='<?php echo $price;?>'><?php echo get_woocommerce_currency_symbol();?><input class='nyp' name='nyp' data-minnyp='<?php echo $price;?>' value='<?php echo $this->convert_to_currency( $price, false);?>'/>
						</span>
						<?php
					}else{?>
					<span class='value' data-sp='<?php echo $price;?>'><?php echo $strike_;?><?php echo $this->convert_to_currency( $price);?></span>
					<?php }?>
					<input type="hidden" data-prices=''>
				</p>
			</div> 
			<?php
		}

	// nonce field
		function print_nonce_field($var='evotx_add_tocart'){
			wp_nonce_field($var);
		}

	function custom_item_meta($name, $value, $unqiue_class=''){
		?>
		<p class='evotx_ticket_other_data_line <?php echo $unqiue_class;?>'>
			<span class='evo_label'><?php echo $name;?></span> 
			<span class='value' ><?php echo $value;?></span>
		</p>
		<?php
	}
	function ticket_qty_html($max='', $unqiue_class=''){
		$max = empty($max)? '':$max;
		?>
		<p class="evotx_quantity">
			<span class='evo_label'><?php evo_lang_e('How many tickets?');?></span>
			<span class="qty evotx_qty_adjuster">
				<b class="min evotx_qty_change <?php echo $unqiue_class;?>">-</b><em>1</em>
				<b class="plu evotx_qty_change <?php echo $unqiue_class;?> <?php echo (!empty($max) && $max==1 )? 'reached':'';?>">+</b>
				<input type="hidden" name='quantity' value='1' data-max='<?php echo $max;?>'/>
			</span>
		</p>
		<?php
	}
	// @+1.7.2
	function ticket_qty_one_hidden(){
		?>
		<p class="evotx_quantity" style='display:none'>
			<span class="qty evotx_qty_adjuster">
				<input type="hidden" name='quantity' value='1' data-max='1'/>
			</span>
		</p>
		<?php
	}
		
	function total_price_html($price, $unqiue_class='', $wcid=''){
		?>
		<h4 class='evo_h4 evotx_addtocart_total <?php echo $unqiue_class;?>'>
			<span class="evo_label"><?php evo_lang_e('Total Price');?></span>
			<span class="value"  data-wcid='<?php echo $wcid;?>'><?php echo $this->convert_to_currency($price);?></span>
		</h4>
		<?php
	}
	function add_to_cart_btn_html($btn_class='', $data_arg = array(), $cancel_btn_data = array() ){
		
		if(!isset($data_arg['green'])) $data_arg['green'] = 'y';
		
		$data_addition = $this->array_to_html_data( $data_arg );

		$can_btn_html = '';

		if( count($cancel_btn_data)> 0 ){
			$cancel_btn = array_merge(array(
				'name'=>__('Cancel'),
				'class'=>'evcal_btn',
				'style'=>'',
				'data'=> array()
			), $cancel_btn_data);
  
			extract($cancel_btn);

			$can_btn_html = "<span class='{$class}' style='{$style}' data-d='". json_encode($data) ."'>{$name}</span>";
		}
		?>
		<p class='evotx_addtocart_button'>
			<?php echo $can_btn_html;?>
			<button class="evcal_btn <?php echo $btn_class;?>" style='margin-top:10px' <?php echo $data_addition;?>><?php evo_lang_e('Add to Cart')?></button>
		</p>
		<?php
	}

	// Return price formatting values
		function get_price_format_data(){
			return array(
				'currencySymbol'=>get_woocommerce_currency_symbol(),
				'thoSep'=> htmlentities( get_option('woocommerce_price_thousand_sep'), ENT_QUOTES ),
				'curPos'=> get_option('woocommerce_currency_pos'),
				'decSep'=> get_option('woocommerce_price_decimal_sep'),
				'numDec'=> get_option('woocommerce_price_num_decimals')
			);
		}
		public function get_text_strings(){
			return apply_filters('evotx_addtocart_text_strings',array(
				't1'=> evo_lang('Added to cart'),
				't2'=> evo_lang('View Cart'),
				't3'=> evo_lang('Checkout'),
				't4'=> evo_lang('Ticket could not be added to cart, try again later!'),
				't5'=> evo_lang('Quantity of Zero can not be added to cart!'),
				't6'=> evo_lang('Price must be higher than minimum!'),
			));
		}

	// success or fail message HTML after adding to cart	
	function add_to_cart_html($type='good', $msg=''){
		$newWind = (evo_settings_check_yn(EVOTX()->evotx_opt,'evotx_cart_newwin'))? 'target="_blank"':'';
		ob_start();
		if( $type =='good'):
			?>
			<p class='evotx_success_msg'><b><?php evo_lang_e('Added to cart');?>!</b></p>
			<?php
		else:
			if(empty($msg)) $msg = evo_lang('Ticket could not be added to cart, try again later');
			?>
			<p class='evotx_success_msg bad'><b><?php echo $msg;?>!</b></p>
			<?php
		endif;
		return ob_get_clean();
	}	

	function __get_addtocart_msg_footer($type='', $msg=''){
		?>
		<div class='tx_wc_notic evotx_addtocart_msg marb20'>
		<?php
			if( !empty($type) ){
				echo $this->add_to_cart_html($type, $msg);
			}
		?>
		</div>
		<div class='evotx_cart_actions' style='display:<?php echo $type == 'standalone' ? 'block':'none';?>'>
			<?php 
			$new_window = EVO()->cal->check_yn('evotx_cart_newwin','evcal_tx') ?  'target="_blank"':'';
			?>
			<a class='evcal_btn' href="<?php echo wc_get_cart_url();?>" <?php echo $new_window;?>><?php evo_lang_e('View Cart');?></a> 
			<a class='evcal_btn' href="<?php echo wc_get_checkout_url();?>" <?php echo $new_window;?>><?php evo_lang_e('Checkout');?></a></span>
		</div>
		<?php
	}

}