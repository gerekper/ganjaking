<?php
/** 
 * Event Tickets Integration
 * @version 1.1.1
 */

class EVOVO_tx{
	public $help;
	public function __construct(){		


		// frontend view
		add_filter('evotx_single_product_temp', array($this, 'frontend_temp'), 10,3);
		add_filter('evotx_add_to_cart_evotxdata', array($this, 'cart_evotx_data'), 10,1);
		
		// pricing update
			add_filter('woocommerce_get_cart_item_from_session', array($this,'get_cart_item_meta_values'), 1, 3 );

		// ADD TO CART
			add_filter('evotx_addtocart_text_strings', array($this, 'text_strings'),10,1);
			add_filter('evotx_add_cart_item_meta', array($this, 'add_to_cart'),10,4);
			add_filter('evotx_ticket_item_price_for_cart', array($this, 'cart_ticket_price'),20,4);
			add_filter('evotx_is_ticket_in_stock', array($this, 'is_in_stock'),10,2);

		// CART VIEW
			add_filter('evotx_cart_item_name',array($this,'add_to_cart_item_names'),1,4);
			add_filter('evotx_cart_item_quantity',array($this,'cart_item_quantity'),1,4);
			add_action('evotix_cart_item_validation', array($this, 'cart_validation'), 10, 3);

			//add_filter('woocommerce_cart_product_subtotal', array($this, 'cart_product_subtotal'), 10, 4);
			//add_filter('woocommerce_cart_item_subtotal', array($this, 'cart_item_subtotal'), 10, 3);
			//add_filter('woocommerce_before_calculate_totals', array($this, 'cart_002'), 10, 1);
			//add_filter('woocommerce_calculate_totals', array($this, 'cart_003'), 10, 1);
			//add_filter('woocommerce_adjust_non_base_location_prices', array($this, 'cart_004'), 10, 1);
		
		// checkout
			add_action('evotx_checkout_create_order_line_item',array($this,'order_item_meta_update_new'),1,4);
			add_action('woocommerce_before_cart_item_quantity_zero',array($this,'update_removed_cart_items'),1,1);
			add_filter('evotx_adjust_orderitem_ticket_stockother', array($this, 'adjust_ticket_orderitem_vo_stock'), 10, 6);
			add_filter('evotx_tix_save_field_meta', array( $this, 'tix_meta_values'),10,2);
			add_filter('evotx_checkout_addnames_other_vars', array( $this, 'add_ticket_infor_adds'),10,3);

		// on admin, emails, frontend
			add_filter('evotx_hidden_order_itemmeta', array($this,'hide_order_item_metafields'),10,1);
			add_filter('woocommerce_order_item_get_formatted_meta_data', array($this, 'order_item_meta_forshowing'), 10,2);
			add_action('evotix_confirmation_email_data', array($this, 'tix_confirmation_email'), 10, 6);

			add_filter('evotx_get_attendees_for_event', array($this, 'view_attendee_list_data'), 10, 2);		

		if(is_admin()){				
			
			add_action('eventontx_tix_post_table', array($this, 'evo_tix_table_row'), 10, 3);
			add_filter('evotx_csv_headers', array($this, 'csv_headers'), 10, 2);
			add_filter('evotx_csv_row', array($this, 'csv_row'), 10, 4);

			// sales insight
			add_filter('evotx_sales_insight_data_item', array($this, 'evotx_sales_insight_data_item'), 10, 5);
			add_filter('evotx_sales_insight_after', array($this, 'evotx_sales_insight_print'), 10, 3);
		}

		$this->help = class_exists('evotx_helper') ? new evotx_helper() : false;
	}

	// front end show VO content
		function frontend_temp($boolean, $event, $product){

			// if there are other ticket addons
			$show_vo_options = apply_filters('evovo_ticket_frontend_mod', true, $event, $boolean, $product);
			if( $show_vo_options !== true ) return $show_vo_options;

			$VOs = new EVOVO_Var_opts($event, $event->wcid,'variation');
			$POs = new EVOVO_Var_opts($event, $event->wcid,'option');

			//print_r($event->get_prop('_evovo_variation'));

			if(!$VOs->is_vo()) return $boolean;
			if(!$VOs->is_set() && !$POs->is_set()) return $boolean;

			$show_remaining_stock = $VOs->is_event_show_remaining_stock();

			// evo correct lang is already loaded via tickets addon

			$HTML = $VOs->print_frontend_html(
				$event->ID, 
				'event', 
				array(
					'event_data'=>array(
						'showRem'=>$show_remaining_stock,	
					)
				),
				$product,
				array(
					'hidableSection'=>true,
				)
			);	
			
			if(!$HTML) return $boolean;

			return $HTML;
			
		}

		function cart_evotx_data($data){
			return $data;
		}

	// ADD TO CART
		function is_in_stock($return, $event){

			$VOs = new EVOVO_Var_opts($event, $event->wcid,'variation');
			if(!$VOs->is_vo()) return $return;
			if(!$VOs->is_set()) return $return;

			// VO Extension
			return apply_filters('evovo_is_instock_check', $VOs->method_has_stock(), $return, $event);	
		}

		function text_strings($array){
			$array['tvo1'] = evo_lang('Out of Stock').'!';
			$array['tvo2'] = evo_lang('Selected options not available for sale').'!';
			$array['tvo3'] = evo_lang('Current selection is out of stock, please make new selection').'!';
			return $array;
		}

		function add_to_cart($cart_item_data, $EVENT, $product_price, $DATA){
			
			if( !isset($DATA['other_data'])) return $cart_item_data;	
			if( !isset( $DATA['other_data']['parent_id'] )) return $cart_item_data;	
			if( !isset( $DATA['other_data']['parent_type'] )) return $cart_item_data;	
			if( !isset($DATA['other_data']['has_vo']) && $DATA['other_data']['has_vo']) return $cart_item_data;	

			//return;

			$PO = new EVOVO_Var_opts($EVENT, $EVENT->wcid, 'option');

			// prelim values
				$po_sep = $EVENT->check_yn('_evovo_po_sep_sold');
				$var_sep = $EVENT->check_yn('_evovo_var_sep_sold');
				$po_uncor = $po_sep ? false: $EVENT->check_yn('_evovo_po_uncor_qty');
				
				$outofstock = false;
				$item_price_additions = $sep_price_additions = 0;

				$parent_id = $DATA['other_data']['parent_id'];
				$parent_type = $DATA['other_data']['parent_type'];
		
			// price options
			if( isset( $DATA['other_data']['options']) && is_array( $DATA['other_data']['options'] ) && count($DATA['other_data']['options']) > 0 ){

				$price_options = $DATA['other_data']['options'];

				// if PO as SEPARATE ticket
				if($po_sep ){

					$ind_pos = $DATA['other_data']['options'];

					$CID = $cart_item_data;
					$CID['evovo_data']['po'] = array();

					foreach($ind_pos as $po_id=>$po_qty){
						
						if( $po_qty == 0) continue;

						// use a fresh cart item data copy
						$CID = $cart_item_data;
						
						$PO->set_item_data( $po_id);
						$sin_price = $PO->get_item_prop('regular_price');
						$sin_stock = $PO->get_item_prop('stock');
						
						// price option qty is more than available stock
						if( $sin_stock && $po_qty > $sin_stock){
							$outofstock = true; continue;
						}

						// price option price addition
						$po_price = $sin_price * 1;
						
						$CID['evovo_data']['po'][$po_id]['price'] = $sin_price;

						$CID['evovo_data']['po'][$po_id]['qty'] = 1;

						$CID['evovo_data']['def_price'] = $product_price;
						$CID['evovo_price'] = $product_price + $po_price;

						// pass options values to cart
						$CID['evovo_data']['options'][$po_id] = 1;
						
						$cart_item_keys = WC()->cart->add_to_cart(
							$EVENT->wcid,
							$po_qty,0,array(),
							$CID
						);
					}

					// return cart key so the ticket will not be added again
					return $cart_item_keys;

				// price option as part of single ticket
				}else{

					foreach($price_options as $po_id=>$po_qty){

						$PO->set_item_data( $po_id);
						$sin_price = $PO->get_item_prop('regular_price');
						$sin_stock = $PO->get_item_prop('stock');
						
						// price option qty is more than available stock
						if( $sin_stock && $po_qty > $sin_stock){
							$outofstock = true; 
							continue;
						}

						// calculate price option price X qty
						$item_price_additions += $sin_price * $po_qty;
						$price_options[$po_id] = $PO->get_parent_vos_by_id( $po_id ); 

						// move qty value into po array
						$price_options[$po_id]['qty'] = $po_qty;
						$price_options[$po_id]['uncor'] = $po_uncor;

						// pass options values to cart
						$cart_item_data['evovo_data']['options'][$po_id] = $po_qty;
					}
				
					$cart_item_data['evovo_data']['po'] = $price_options;
					
				}	
			}

			// variations
			if( isset($DATA['other_data']['var_ids']) && is_array( $DATA['other_data']['var_ids'] ) && count( $DATA['other_data']['var_ids'] ) > 0 ){

				$variations = $DATA['other_data']['var_ids'];

				$VO = new EVOVO_Var_opts($EVENT, $EVENT->wcid ,'variation');

				$all_variations_data = $PO->get_all_variation_types_dataset( $parent_id, $parent_type );
				

				$c = 0; 
				foreach($variations as $var_id => $var_qty ){

					if( empty( $var_id )) continue;

					// sell variations as separate
					if( $var_sep){

						$cart_item_keys = false; // flush previous data
						
						if( $var_qty == 0 ) continue;


						$CID = $cart_item_data; // use fresh CIDs

						$VO->set_item_data( $var_id);
						$sin_price = $VO->get_item_prop('regular_price');
						$sin_stock = $VO->get_item_prop('stock');

						// qty is more than available stock
						if( $sin_stock && $var_qty > $sin_stock){
							$outofstock = true; continue;
						}

						$item_price_additions = $sin_price * $var_qty;
						
						$CID['evovo_data']['var_id'] = $var_id;
						$CID['evovo_data']['type'] = 'ind_variation';

						$CID['evovo_data']['vart'] = $all_variations_data['variations'][ $var_id ]['variations'];

						$CID['evovo_data']['def_price'] = $sin_price;
						//$CID['evovo_price'] = $item_price_additions;
						$CID['evovo_price'] = $sin_price;

						//print_r($CID);
						
						
						$cart_item_keys = WC()->cart->add_to_cart(
							$EVENT->wcid,	$var_qty, 0, array(),
							$CID
						);
						

					}else{

						$cart_item_data['evovo_data']['var_id'] = $var_id;
						$VO->set_item_data($var_id);
						
						if($VO->get_item_prop('regular_price'))
							$product_price = $VO->get_item_prop('regular_price');				

					}
				}

				if( $var_sep){
					// Successfully added to cart
					return $cart_item_keys;
				}
				

				// pass variation type values to cart
				if( isset( $DATA['other_data']['vart'] ) )
					$cart_item_data['evovo_data']['vart'] = $DATA['other_data']['vart'];
				
			}else{
				// if there are no variations, only price option and if default price is passed
				$product_price = isset($DATA['other_data']['defp'] ) ? $DATA['other_data']['defp']: $product_price;
			}
			
			// default price				
				$cart_item_data['evovo_data']['def_price'] = $product_price;

			// set item base data
				$total_item_price = $product_price + $item_price_additions;
				$cart_item_data['evovo_price'] = $total_item_price;
				$cart_item_data['evovo_price_adds'] = $item_price_additions;

				//print_r($cart_item_data);

			// if any part of item is out of stock
				if($outofstock){
					echo json_encode(array(
						'msg'=> __('Item out of stock!'), 
						'status'=> 'bad',
					)); exit;
				}

			return apply_filters('evovo_add_cart_item_meta',$cart_item_data, $EVENT, $DATA);
			

		}

	// CART INIT
		function get_cart_item_meta_values($session_data, $values, $key){	
			
	        foreach(array(
	        	'evovo_data',
	        ) as $meta_key){
	        	if (array_key_exists( $meta_key, $values ) ){
	        		$session_data[$meta_key] = $values[$meta_key];
	        	}
	        }
	        
	        return $session_data;
		}
		function cart_ticket_price($boolean, $def_price, $session_data, $values){
			//print_r($values);
			if (array_key_exists( 'evovo_data', $values ) ){
				return $values['evovo_price'];
	        }
	        return $boolean;
		}

	// CART VIEW
		// cart item name alteration
		function add_to_cart_item_names($product_name, $EVENT, $values, $cart_item_key){
			if(isset($values['evovo_price'])){
				//print_r($values);	
				$evovo_html = $this->get_vo_display_html($values['evovo_data'], $EVENT, $values['product_id']);

	        	$product_name .= $evovo_html;  
			}
			return $product_name;
		}
		
	   	function cart_item_quantity($boolean, $_product, $cart_item_key, $cart_item){
	   		if(empty($cart_item['evovo_price']) ) return $boolean;
	   		if( !isset($cart_item['evovo_data']['var_id']))	return $boolean; 

	   		$VOs = new EVOVO_Var_opts($cart_item['evotx_event_id_wc'], $cart_item['product_id'] ,'variation');
	   		
	   		$VOs->set_item_data( $cart_item['evovo_data']['var_id'] );
	   		
	   		$product_quantity = woocommerce_quantity_input( array(
				'input_name'  => "cart[{$cart_item_key}][qty]",
				'input_value' => $cart_item['quantity'],
				'max_value'   => $VOs->get_item_stock(),
				'min_value'   => '0',
			), '', false );

			return $product_quantity; 
	   	}

	   	// cart subtotal modifications
	   		function cart_item_subtotal($product_subtotal, $cart_item, $cart_item_key){

	   			if( isset($cart_item['evovo_data']) && isset($cart_item['evovo_price_adds'])){
	   				$qty = (float) $cart_item['quantity'];
	   				$def_price = $cart_item['evovo_data']['def_price'];
	   				$uncor_price_adds = $cart_item['evovo_price_adds'];

	   				$new_subtotal = ( (float) $def_price * $qty ) + (float)$uncor_price_adds;
	   				return wc_price( $new_subtotal );
	   			}

	   			//print_r($cart_item);
	   			return $product_subtotal;
	   		}
	   		function cart_product_subtotal($product_subtotal, $product, $quantity, $class){

	   			return $product_subtotal;
	   		}
	   		function cart_004($bool){
	   			foreach(WC()->cart->get_cart() as $cart_item){
	   				$cart_item['data']->set_price( 20.55 );
	   				echo 'R';
	   			}
	   			return $bool;
	   		}
	   		function cart_003($cart_object){
	   			foreach ( $cart_object->get_cart() as $cart_item ) {
	   				$cart_item['data']->set_price( 20.55 );
	   			}
	   		}
	   		function cart_002($cart_object){
	   			echo 'f';
	   			foreach ( $cart_object->get_cart() as $cart_item ) {
	   				//$cart_item['data']->set_price( 20.55 );
	   				$cart_item['line_subtotal'] = 20.55;
	   			}
	   		}
	   		function cart_001( $total, $cart_item, $cart){
	   			
	   			if( isset($cart_item['evovo_data']) && isset($cart_item['evovo_price_adds'])){
	   				$qty = (float) $cart_item['quantity'];
	   				$def_price = $cart_item['evovo_data']['def_price'];
	   				$uncor_price_adds = $cart_item['evovo_price_adds'];

	   				$new_subtotal = ( (float) $def_price * $qty ) + (float)$uncor_price_adds;

	   				//echo $new_subtotal.'XX';
	   				return ( $new_subtotal );
	   			}
    			
    			return $total;

	   		}

	   	// cart validation
			function cart_validation($cart_item_key, $cart_item, $event_id){

				if( !isset($cart_item['evovo_data'])) return false;
				
				$evovo_data = $cart_item['evovo_data']; 


				// grab the prices data array 
				$evovo_data_prices = isset($evovo_data['prices']) ? isset($evovo_data['prices']): array();

				$EVENT = new EVO_Event($event_id);


				// check if variation in stock
				if( isset($evovo_data['var_id']) ){
					$Vs = new EVOVO_Var_opts($EVENT, $cart_item['product_id'] ,'variation');
					$Vs->set_item_data( $evovo_data['var_id']);

					$var_stock = apply_filters('evovo_var_in_stock', $Vs->in_stock(), $Vs, $EVENT, $cart_item);

					if( $var_stock && $var_stock< $cart_item['quantity'] || !$var_stock){
						WC()->cart->remove_cart_item($cart_item_key);
						wc_add_notice( 'Ticket removed from cart, no longer available for sale!', 'error' );
					}

					// check if variation is can be added based on login status
					if( $Vs->item_data){
						if(!$Vs->_can_user_see( $Vs->item_data) ){
							WC()->cart->remove_cart_item($cart_item_key);
							wc_add_notice( 'Ticket removed from cart, only available for members!', 'error' );
						}
					}
				}				 

				// check if price option in stock
				if( isset($evovo_data['po']) && sizeof($evovo_data['po'])>0){
					$POs = new EVOVO_Var_opts($EVENT, $cart_item['product_id'] ,'option');
					
					foreach($evovo_data['po'] as $po_id=>$po_val){
						$POs->set_item_data( $po_id);

						$po_qty = 1;
						if( isset( $po_val['qty'])) $po_qty = (int)$po_val['qty'];
						if( isset($evovo_data_prices[ $po_id]) && isset($evovo_data_prices[ $po_id]['qty']) ) $po_qty = (int)$evovo_data_prices[ $po_id]['qty'];
						 

						$po_stock = apply_filters('evovo_po_in_stock',$POs->in_stock() , $POs, $EVENT);

						
						if( $po_stock && $po_stock < $po_qty || !$po_stock && $po_stock !== true){
							WC()->cart->remove_cart_item($cart_item_key);
							wc_add_notice( 'Ticket removed from cart, no longer available for sale!', 'error' );
						}
					}
				}
			}

	// CHECKOUT
	   	// add custom data as meta data to order item	    
			function order_item_meta_update_new($item, $cart_item_key, $values, $order){
				if(isset($values['evovo_data']) ){
		        	$item->add_meta_data('_evovo_data', $values['evovo_data'],true);
		        }
			}

		// remove custom data if item removed from cart
			function update_removed_cart_items($cart_item_key){ }
		
		// Adjust order ticket item vo stock
		function adjust_ticket_orderitem_vo_stock($boolean, $TIX_EVENT, $order, $item_id, $item, $type){

			$evovo_data = wc_get_order_item_meta($item_id ,'_evovo_data'); 
			//$evovo_qty = wc_get_order_item_meta($item_id ,'_qty');

			if(!$evovo_data) return $boolean;

			// variation
    		if( isset($evovo_data['var_id'])){
    			$Vs = new EVOVO_Var_opts($TIX_EVENT, $item['product_id'] ,'variation');
				$Vs->set_item_data( $evovo_data['var_id']);
				
				// adjust the stock
				$Vs->item_adjust_qty($type, $item['quantity']);

				$TIX_EVENT->relocalize_event_data();
				//$TIX_EVENT->reglobalize_event_data_from_local(); - eventon 2.7

				//update_post_meta(1, 'a'.$item_id,$evovo_data['var_id']);
    		}		

    		// price option
    		if( isset($evovo_data['po']) && sizeof($evovo_data['po'])>0){

    			$POs = new EVOVO_Var_opts($TIX_EVENT, $item['product_id'] ,'option');

    			foreach($evovo_data['po'] as $po_id=>$po_val){
    				$POs->set_item_data( $po_id);

    				// if price option qty not set
    				if(!isset($po_val['qty'])) continue;

    				// adjust the stock			    				
					$POs->item_adjust_qty($type, $po_val['qty']);
    			}
    		}

    		// order ticket item VO stock has been adjusted
    		return true;
		}
		// save ticket item meta custom values for faster retrieve of data
		function tix_meta_values($array, $item){
			if(!empty($item['_evovo_data'])) $array['_evovo_data'] = $item['_evovo_data'];

			return $array;
		}

		// additional ticket information at the checkout additions from VO
		function add_ticket_infor_adds( $O, $V, $EVENT){

			if(!isset($V['evovo_data'])) return $O;
			if(!isset( $V['evovo_data']['vart']) || !isset( $V['evovo_data']['var_id']) ) return $O;

			$vart = $V['evovo_data']['vart'];
			$var_id = $V['evovo_data']['var_id'];

			$VTs = new EVOVO_Var_opts($EVENT, $V['product_id'] ,'variation_type');

			foreach($vart as $vt_id=>$vt_val){
				$VTs->set_item_data($vt_id);
				$O .= "<span style='display:block'><b>". $VTs->get_item_prop('name').':</b> '.$vt_val ."</span>";
			}


			return $O;
		}

	// Display VO data
		function get_vo_display_html($evovo_data, $EVENT, $wcid=''){
			if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT);

			$vo_data = $evovo_data;        	
   			$TXHelp = new evotx_helper();
   			$ticket_time = $EVENT->get_formatted_smart_time();

   			$separate_po = $EVENT->check_yn('_evovo_po_sep_sold');

   			//print_r($vo_data);

   			ob_start();
        	?>
			<span class='evo_ticket_vos'>
	
			<?php 
				if( isset($vo_data['def_price'])){
					echo "<span class='evovo_spread'><b>". evo_lang('Base Price') ."</b> ".$TXHelp->convert_to_currency($vo_data['def_price']) ."</span>";
				}
			?>

			<?php	
				// variations		
				if( !empty($vo_data['vart'])):
					$VTs = new EVOVO_Var_opts($EVENT, $wcid ,'variation_type');
					
					echo "<span class='evovo_subtitle'>".evo_lang('Variations for ticket')."</span>";
					foreach($vo_data['vart'] as $vt_id=>$vt_val){
						$VTs->set_item_data($vt_id);
						echo "<span class='evovo_left evotx_itemmeta_secondary'><b>". $VTs->get_item_prop('name') ."</b> ".$vt_val."</span>";
					}
				endif;

				// price options
				if( !empty($vo_data['options']) && sizeof($vo_data['options'])>0):
					$POs = new EVOVO_Var_opts($EVENT, $wcid ,'option');
					if(!$separate_po) echo "<span class='evovo_subtitle'>".evo_lang('Optional Additions')."</span>";

					foreach($vo_data['options'] as $po_id => $po_qty){
						$POs->set_item_data($po_id);

						if( $po_qty == 0 ) continue;
						$po_price = $POs->get_item_prop('regular_price') * $po_qty;

						$qty_add = $po_qty > 1? ' x '.$po_qty:'';

						echo "<span class='evovo_po evovo_spread po_{$po_id}'>". $POs->get_item_prop('name') .$qty_add. "<em style='padding-left:5px'>".$TXHelp->convert_to_currency($po_price)."</em></span>";
					}
				endif;
			?>
			</span><?php
			return ob_get_clean();
		}
	
	// TICKET VIEW
		function hide_order_item_metafields($array){
			return $array;
		}
		function order_item_meta_forshowing($formatted_meta, $item){
			$item_id = $item->get_id();

			//if(!isset($_REQUEST['post'])) return $formatted_meta; // only on order edit page

			$vo_data = wc_get_order_item_meta($item_id ,'_evovo_data');  
			$event_id = wc_get_order_item_meta($item_id ,'_event_id'); 

			if(!$vo_data && !$event_id) return $formatted_meta;

			$TXHelp = new evotx_helper();

			if( isset($vo_data['vart'])):
				$VTs = new EVOVO_Var_opts($event_id, $item_id ,'variation_type');
				foreach($vo_data['vart'] as $vt_id=>$vt_val){
					$VTs->set_item_data($vt_id);
					$formatted_meta['_'.$vt_id] = (object)array(
						'key'=>'',
						'value'=>'',
						'display_key'=>		$VTs->get_item_prop('name'),
						'display_value'=>	$vt_val
					);
				}
			endif;

			if( isset($vo_data['po']) && sizeof($vo_data['po'])>0):
				$POs = new EVOVO_Var_opts($event_id, $item_id ,'option');
				
				foreach($vo_data['po'] as $po_id=>$po_val){
					$POs->set_item_data($po_id);

					$po_qty = isset($po_val['qty'])? $po_val['qty']: 1;
					$po_price = $POs->get_item_prop('regular_price') * $po_qty;

					$qty_add = $po_qty>1? ' x '.$po_qty:'';

					$formatted_meta['_'.$po_id] = (object)array(
						'key'=>'',
						'value'=>'',
						'display_key'=>		$POs->get_item_prop('name'). $qty_add,
						'display_value'=>	$TXHelp->convert_to_currency($po_price)
					);
				}
			endif;

			
			return $formatted_meta;
		}
	
	// EMAILING
		// Show VO information for confirmation ticket email
		function tix_confirmation_email($ticket_item_id, $ticket_pmv, $styles,$ticket_number, $tix_holder_index,$event_id){		

			if(!empty($ticket_pmv['_evovo_data'])): 

				$evovo_data = unserialize($ticket_pmv['_evovo_data'][0]);
				$evovo_display_data = $this->__get_ticket_vo_display_data($evovo_data, $event_id);

				if(sizeof($evovo_display_data)>0){
					if( isset($evovo_display_data['vt'])){
						foreach($evovo_display_data['vt'] as $name=>$val){
							?><div><p style="<?php echo $styles['005'].$styles['pb5'].$styles['pt10'];?>"><?php echo $val ?></p><p style="<?php echo $styles['004'].$styles['pb5'];?>"><?php echo $name; ?></p></div>
							<?php
						}
					}
					if( isset($evovo_display_data['po'])){
						foreach($evovo_display_data['po'] as $name=>$val){
							?><div><p style="<?php echo $styles['005'].$styles['pb5'].$styles['pt10'];?>"><?php echo $val ?></p><p style="<?php echo $styles['004'].$styles['pb5'];?>"><?php echo $name; ?></p></div>
							<?php
						}
					}
				}				
			endif;
		}

	// sales insight
		function evotx_sales_insight_data_item($A, $item_id, $item, $EVENT, $order){

			$vo_data = wc_get_order_item_meta($item_id ,'_evovo_data');  
			if(!$vo_data) return $A;

			$TXHelp = new evotx_helper();

			if( isset($vo_data['vart'])):
				$VTs = new EVOVO_Var_opts($EVENT->ID, $item_id ,'variation_type');
				foreach($vo_data['vart'] as $vt_id=>$vt_val){
					$VTs->set_item_data($vt_id);
					$A['evovo']['_'.$vt_id] = array(
						'name'=>		$VTs->get_item_prop('name'),
						'value'=>	$vt_val
					);
				}
			endif;

			if( isset($vo_data['po']) && sizeof($vo_data['po'])>0):
				$POs = new EVOVO_Var_opts($EVENT->ID, $item_id ,'option');
				
				foreach($vo_data['po'] as $po_id=>$po_val){
					$POs->set_item_data($po_id);

					$po_qty = isset($po_val['qty'])? $po_val['qty']: 1;
					$po_price = $POs->get_item_prop('regular_price') * $po_qty;

					$A['evovo']['_p'.$po_id] = array(
						'name'=>		$POs->get_item_prop('name'),
						'qty'=> $po_qty,
						'total_price'=>	$po_price
					);
				}
			endif;

			return $A;
		}

		function evotx_sales_insight_print($EVENT, $orders, $sales_data){

			$VTs = new EVOVO_Var_opts($EVENT->ID, '' ,'variation_type');
			if(!$VTs->is_vo()) return;

			$TXHelp = new evotx_helper();

			?>

			<div class='evotxsi_row sales_by_variations'>
				<h2 style='font-weight:bold'><?php _e('Sales by Ticket Variations & Options','evotx');?></h2>	
				<h3 style='padding-bottom: 10px'><?php _e('Generated using ticket variations from variations and options addon','evotx');?></h3>

				<?php

				$_vts = array();
				$variation_types = $VTs->dataset;
				//print_r($VTs);

				if( is_array($variation_types)):
					foreach($variation_types as $vt_id=>$vt){
						$vts = $VTs->_process_vt_options( $vt);
						if(!$vts) continue;

						?>
						<div class='evovosi_var_type'>
							<span class='name'><?php echo $vt['name'];?></span>
							<span class='items'>
						<?php
						foreach($vts as $vto){

							$qty = 0;
							$cost = 0;
							foreach($sales_data as $sd){
								if( !isset($sd['evovo']) ) continue;
								// exclude not completed orders.
								if( isset($sd['order_status']) && $sd['order_status'] != 'completed' ) continue; 

								foreach($sd['evovo'] as $sdd ){
									if( $sdd['name'] != $vt['name']) continue;
									if( $sdd['value'] != $vto) continue;

									$qty += (int)$sd['qty']; 
									$cost += $sd['cost'];
								}
								
							}

							echo "<span class='item'>
							<span class='item_name'>{$vto}</span>
							<span class='item_val'><em>{$qty}</em>". $TXHelp->convert_to_currency($cost). "</span>
							</span>";
						}

						echo "</span></div>";
						$_vts[] = array(
							'name'=> $vt['name'],
							'options'=>$vts,
						);
					}
				endif;


				?>	
			</div>

			<?php
		}


	// ADMIN
		// CSV download attendees
			function csv_headers($array, $EVENT){
				if( $EVENT->check_yn('_evovo_activate')){
					// ticket variation types
					$VTs = new EVOVO_Var_opts($EVENT, '' ,'variation_type');
					if( $VTs->is_set()){
						foreach($VTs->dataset as $vt_id=>$vt){
							$array[]= $vt['name'];
						}
					}

					// ticket variation options
					$VOs = new EVOVO_Var_opts($EVENT, '' ,'option');
					if( $VOs->is_set()){
						foreach($VOs->dataset as $vt_id=>$vt){
							$array[]= $vt['name'];
						}
					}
					
					
				}
				return $array;
			}
			function csv_row($array, $tn, $td, $EVENT){
				if( $EVENT->check_yn('_evovo_activate') ){
					// variation types
					$VTs = new EVOVO_Var_opts($EVENT, '' ,'variation_type');
					if( $VTs->is_set()){
						foreach($VTs->dataset as $vt_id=>$vt){
							$array[]= '"'. (isset($td['oD'][$vt['name']] )? $td['oD'][$vt['name']]:'') .'"';
						}
					}

					// price options
					$VOs = new EVOVO_Var_opts($EVENT, '' ,'option');
					if( $VOs->is_set()){
						foreach($VOs->dataset as $vt_id=>$vt){
							$array[]= '"'. (isset($td['oD'][$vt['name']] )? $td['oD'][$vt['name']]:'') .'"';
						}
					}

					
				}
				return $array;
			}

		// show ticket variation information for evo-tix cpt
		function evo_tix_table_row($post_id, $ticketItem_meta, $event_id){
			if(!empty($ticketItem_meta['_evovo_data'])): 

				$evovo_data = unserialize($ticketItem_meta['_evovo_data'][0]);
				$evovo_display_data = $this->__get_ticket_vo_display_data($evovo_data, $event_id);

				if(sizeof($evovo_display_data)>0){
					if( isset($evovo_display_data['vt'])){
						foreach($evovo_display_data['vt'] as $name=>$val){
							echo "<tr><td>". $name ."</td><td>".$val."</td></tr>";
						}
					}
					if( isset($evovo_display_data['po'])){
						foreach($evovo_display_data['po'] as $name=>$val){
							echo "<tr><td>". $name ."</td><td>".$val."</td></tr>";
						}
					}
				}				
			endif;
		}
		// display variation details in the view attendees section
		function view_attendee_list_data($output, $event_id){
			
			if(!isset($output['id'])) return $output;
			$evo_tix_id = $output['id'];
			
			$evovo_data = get_post_meta($evo_tix_id, '_evovo_data',true);
			
			if( !$evovo_data) return $output;

			$evovo_display_data = $this->__get_ticket_vo_display_data($evovo_data, $event_id);

			$output['oDD']['evovo'] = array();

			if(sizeof($evovo_display_data)>0){
				if( isset($evovo_display_data['vt'])){
					foreach($evovo_display_data['vt'] as $name=>$val){
						$output['oD'][$name] = $val;
						$output['oDD']['evovo'][$name] = $val;
					}
				}
				if( isset($evovo_display_data['po'])){
					foreach($evovo_display_data['po'] as $name=>$val){
						$output['oD'][$name] = html_entity_decode( $val );
						$output['oDD']['evovo'][$name] = $val;
					}
				}
			}	

			return $output;

		}

		function __get_ticket_vo_display_data($evovo_data, $event_id){
			$output = array();
			if( isset($evovo_data['vart'])){
				$VTs = new EVOVO_Var_opts($event_id, '' ,'variation_type');

				foreach($evovo_data['vart'] as $vt_id=>$vt_val){
					$VTs->set_item_data($vt_id);
					$output['vt'][ $VTs->get_item_prop('name') ] = $vt_val;
				}
			}

			if( !empty($evovo_data['po']) && sizeof($evovo_data['po'])>0):
				$POs = new EVOVO_Var_opts($event_id, '' ,'option');
				$TXHelp = new evotx_helper();

				foreach($evovo_data['po'] as $po_id=>$po_val){
					$POs->set_item_data($po_id);

					$po_qty = isset($po_val['qty'])? $po_val['qty']: 1;
					$po_price = $POs->get_item_prop('regular_price') * $po_qty;

					$qty_add = $po_qty>1? ' x '.$po_qty:'';

					$output['po'][$POs->get_item_prop('name').$qty_add] = $TXHelp->convert_to_currency($po_price);
				}
			endif;
			return $output;
		}
}
new EVOVO_tx();