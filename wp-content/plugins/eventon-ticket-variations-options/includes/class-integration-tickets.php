<?php
/** 
 * Event Tickets Integration
 * @v 0.3
 */

class EVOVO_tx{
	public function __construct(){		

		add_filter('evotx_single_product_temp', array($this, 'frontend_temp'), 10,3);
		
		// pricing update
			add_filter('woocommerce_get_cart_item_from_session', array($this,'get_cart_item_meta_values'), 1, 3 );

		// ADD TO CART
			add_filter('evotx_addtocart_text_strings', array($this, 'text_strings'),10,1);
			add_filter('evotx_add_cart_item_meta', array($this, 'add_to_cart'),10,4);
			add_filter('evotx_add_cart_item_qty', array($this, 'add_to_cart_qty'),10,4);
			add_filter('evotx_ticket_item_price_for_cart', array($this, 'cart_ticket_price'),20,4);
			add_filter('evotx_is_ticket_in_stock', array($this, 'is_in_stock'),10,2);

		// CART VIEW
			add_filter('evotx_cart_item_name',array($this,'add_to_cart_item_names'),1,4);
			add_filter('evotx_cart_item_quantity',array($this,'cart_item_quantity'),1,4);
			add_action('evotix_cart_item_validation', array($this, 'cart_validation'), 10, 3);
		
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

			if( isset($vo_data['pot']) && sizeof($vo_data['pot'])>0):
				$POs = new EVOVO_Var_opts($EVENT->ID, $item_id ,'option');
				
				foreach($vo_data['pot'] as $po_id=>$po_val){
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


				?>	
			</div>

			<?php
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
			return $array;
		}

		function add_to_cart_qty($QTY, $EVENT, $product_price, $DATA){
			if( !isset($DATA['evovo_data'])) return $QTY;

			$evovo_data = $DATA['evovo_data'];
			$event_data = $DATA['event_data'];
			if( !isset($event_data['wcid'])) return $QTY;
			$po_sep = isset($evovo_data['pomethod']) && $evovo_data['pomethod'] == 'separate'? true: false;

			if(!$po_sep) return $QTY; // if NOT separate PO 

			$wcid = $event_data['wcid'];
			$pot = isset($evovo_data['pot'])? $evovo_data['pot']:false;
			
			if($pot && sizeof($pot)>0){

				$OPs = new EVOVO_Var_opts($EVENT, $wcid,'option');
				
				$c = 0;				
				foreach($pot as $po_id=>$po_val){
					
					if($c >0) continue;

					$OPs->set_item_data( $po_id);
					$sin_stock = $OPs->get_item_prop('stock');

					$po_qty = ( isset($po_val['qty'])? $po_val['qty']:1);

					// not in stock
					if( $sin_stock && $po_qty > $sin_stock)	return $QTY;

					return $po_qty;

					$c++;
				}
			}

		}
		function add_to_cart($cart_item_data, $EVENT, $product_price, $DATA){

			if( !isset($DATA['evovo_data'])) return $cart_item_data;

			$evovo_data = $DATA['evovo_data'];
			$event_data = $DATA['event_data'];
			if( !isset($event_data['wcid'])) return $cart_item_data;
			$wcid = $event_data['wcid'];
			$pot = isset($evovo_data['pot'])? $evovo_data['pot']:false;
			$vart = isset($evovo_data['vart'])?$evovo_data['vart'] : false;
			$po_sep = isset($evovo_data['pomethod']) && $evovo_data['pomethod'] == 'separate'? true: false;

			$var_id = isset($evovo_data['var_id'])? $evovo_data['var_id']:'';
			$qty = $DATA['qty'];

			if(!is_array($vart) && !is_array($pot)) return $cart_item_data;

			if( sizeof($vart)==0 && sizeof($pot) == 0) return $cart_item_data;

			$VOs = new EVOVO_Var_opts($EVENT, $wcid ,'variation');

			$status = 'good'; $output = ''; $cart_item_keys = array();

			$item_price_additions = 0;
			$outofstock = false;

			// for each variation types
				if($vart && sizeof($vart)>0){
					$cart_item_data['evovo_data']['vart'] = $vart;
				}

			// foreach price options
				$pot_ = array();
				if($pot && sizeof($pot)>0){

					$OPs = new EVOVO_Var_opts($EVENT, $wcid,'option');
					
					$c = 0;
					
					foreach($pot as $po_id=>$po_val){
						$c++;
						if( $po_sep && $c >1) continue; // if sep PO run only first item in POT
						$OPs->set_item_data( $po_id);
						$sin_price = $OPs->get_item_prop('regular_price');
						$sin_stock = $OPs->get_item_prop('stock');

						$po_qty = ( isset($po_val['qty'])? $po_val['qty']:1);

						// price option qty is more than available stock
						if( $sin_stock && $po_qty > $sin_stock){
							$outofstock = true; continue;
						}

						// price option price addition
						$po_price = $po_sep? $sin_price: $sin_price * $po_qty;

						$item_price_additions += $po_price;
						$pot_[$po_id] = $po_val; // reconstruct POT array for cart

						// modify cart item data
						if($po_sep)	$pot_[$po_id]['qty'] = 1;
						
					}
					
					$cart_item_data['evovo_data']['pot'] = $pot_;
					
				}else{
					$po_sep = false;
				}

			// if price options added as separate tickets to cart
				if($po_sep){

					$OPs = new EVOVO_Var_opts($EVENT, $wcid,'option');
					$CID = $cart_item_data;

					$c = 0; 
					foreach($pot as $po_id=>$po_val){
						$c++;
						if( array_key_exists($po_id, $pot_)) continue;

						$OPs->set_item_data( $po_id);
						$sin_price = $OPs->get_item_prop('regular_price');
						$sin_stock = $OPs->get_item_prop('stock');

						$po_qty = ( isset($po_val['qty'])? $po_val['qty']:1);

						// price option qty is more than available stock
						if( $sin_stock && $po_qty > $sin_stock){
							$outofstock = true; continue;
						}

						// price option price addition
						$po_price = $sin_price * 1;
						
						$CID['evovo_data']['pot'] = array();
						$CID['evovo_data']['pot'][$po_id]['price'] = $sin_price;
						$CID['evovo_data']['pot'][$po_id]['qty'] = 1;

						$CID['evovo_data']['def_price'] = $product_price;
						$CID['evovo_price'] = $product_price + $po_price;
						
						$cart_item_keys = WC()->cart->add_to_cart(
							$wcid,
							$po_qty,0,array(),
							$CID
						);						
					}
				}

			// ticket variations
				if(!empty($var_id)){
					$cart_item_data['evovo_data']['var_id'] = $var_id;
					$VOs->set_item_data($var_id);
					if($VOs->get_item_prop('regular_price'))
						$product_price = $VOs->get_item_prop('regular_price');
				}else{
					// if there are no variations, only price option and if default price is passed
					$product_price = isset($evovo_data['defp'] ) ? $evovo_data['defp']: $product_price;
				}

			// default price				
				$cart_item_data['evovo_data']['def_price'] = $product_price;

			// set item base data
				$total_item_price = $product_price + $item_price_additions;

				$cart_item_data['evovo_price'] = $total_item_price;

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
	   	// cart validation
			function cart_validation($cart_item_key, $cart_item, $event_id){
				if( !isset($cart_item['evovo_data'])) return false;
				
				$evovo_data = $cart_item['evovo_data']; 

				$EVENT = new EVO_Event($event_id);

				// check if variation in stock
				if( isset($evovo_data['var_id']) ){
					$Vs = new EVOVO_Var_opts($EVENT, $cart_item['product_id'] ,'variation');
					$Vs->set_item_data( $evovo_data['var_id']);

					$var_stock = apply_filters('evovo_var_in_stock', $Vs->in_stock(), $Vs, $EVENT);

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
				if( isset($evovo_data['pot']) && sizeof($evovo_data['pot'])>0){
					$POs = new EVOVO_Var_opts($EVENT, $cart_item['product_id'] ,'option');
					
					foreach($evovo_data['pot'] as $po_id=>$po_val){
						$POs->set_item_data( $po_id);

						$po_stock = apply_filters('evovo_po_in_stock',$POs->in_stock() , $POs, $EVENT);

						if( $po_stock && $po_stock < $po_val['qty'] || !$po_stock && $po_stock !== true){
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
    		if( isset($evovo_data['pot']) && sizeof($evovo_data['pot'])>0){
    			$POs = new EVOVO_Var_opts($TIX_EVENT, $item['product_id'] ,'option');

    			foreach($evovo_data['pot'] as $po_id=>$po_val){
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

		// additional ticekt information at the checkout additions from VO
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
						echo "<span class='evovo_left'><b>". $VTs->get_item_prop('name') ."</b> ".$vt_val."</span>";
					}
				endif;

				// price options
				if( !empty($vo_data['pot']) && sizeof($vo_data['pot'])>0):
					$POs = new EVOVO_Var_opts($EVENT, $wcid ,'option');
					if(!$separate_po) echo "<span class='evovo_subtitle'>".evo_lang('Optional Additions')."</span>";

					foreach($vo_data['pot'] as $po_id=>$po_val){
						$POs->set_item_data($po_id);

						$po_qty = isset($po_val['qty'])? $po_val['qty']: 1;
						$po_price = $POs->get_item_prop('regular_price') * $po_qty;

						$qty_add = $po_qty>1? ' x '.$po_qty:'';

						echo "<span class='evovo_po evovo_spread'>". $POs->get_item_prop('name') .$qty_add. "<em style='padding-left:5px'>".$TXHelp->convert_to_currency($po_price)."</em></span>";
					}
				endif;
			?>
			</span>
			<?php
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

			if( isset($vo_data['pot']) && sizeof($vo_data['pot'])>0):
				$POs = new EVOVO_Var_opts($event_id, $item_id ,'option');
				
				foreach($vo_data['pot'] as $po_id=>$po_val){
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

			if(sizeof($evovo_display_data)>0){
				if( isset($evovo_display_data['vt'])){
					foreach($evovo_display_data['vt'] as $name=>$val){
						$output['oD'][$name] = $val;
					}
				}
				if( isset($evovo_display_data['po'])){
					foreach($evovo_display_data['po'] as $name=>$val){
						$output['oD'][$name] = $val;
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

			if( !empty($evovo_data['pot']) && sizeof($evovo_data['pot'])>0):
				$POs = new EVOVO_Var_opts($event_id, '' ,'option');
				$TXHelp = new evotx_helper();

				foreach($evovo_data['pot'] as $po_id=>$po_val){
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