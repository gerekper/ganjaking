<?php
/** 
 * Integration with event tickets addon
 */

class EVOBO_Tickets_Int{
	public function __construct(){
		// WC
			// ADD to cart
				add_filter('evotx_cart_session_item_values', array($this,'cart_session_item_data'), 10, 1 );
				add_filter('evotx_add_cart_item_meta', array($this, 'add_to_cart'),10,4);
				add_filter('evotx_ticket_item_price_for_cart', array($this, 'cart_ticket_price'),10,4);

			// Cart view
				add_filter('evotx_ticket_item_meta_data',array($this,'cart_ticket_meta_data'),1,3);
				add_filter('evotx_cart_item_quantity',array($this,'cart_item_quantity'),1,4);
				add_action('evotix_cart_item_validation', array($this, 'cart_validation'), 10, 3);

			// checkout
				add_action('evotx_checkout_create_order_line_item',array($this,'order_item_meta_update_new'),1,4);
				add_action('evotx_cart_ticket_qty_zero',array($this,'update_removed_cart_items'),1,2);
				add_filter('evotx_adjust_orderitem_ticket_stockother', array($this, 'adjust_ticket_block_item_stock'), 10, 6);
				add_filter('evotx_order_item_meta_slug_replace', array($this, 'ticket_meta_slug_replace'),10,1);
				add_filter('evotx_tix_save_field_meta', array( $this, 'tix_meta_values'),10,2);
				add_filter('evotx_cart_add_field_eventtime', array( $this, 'tix_eventtime'),10,2);

				add_filter('woocommerce_order_item_get_formatted_meta_data', array( $this, 'order_item_meta_data'),10,2);


			// Emailing
				add_action('evotix_confirmation_email_data', array($this, 'tix_confirmation_email'), 10, 6);
		// frontend template
			add_filter('evotx_single_product_temp', array($this, 'frontend_temp'), 10,2);

			add_filter('evotx_get_attendees_for_event', array($this, 'view_attendees'), 10, 2);

		// ADMIN ONLY
		if(is_admin()){
			add_action('eventontx_tix_post_table', array($this, 'evo_tix_table_row'), 10, 2);
			
			add_filter('evotx_csv_headers', array($this, 'csv_headers'), 10, 2);
			add_filter('evotx_csv_row', array($this, 'csv_row'), 10, 4);
			//add_action('evotx_sales_insight_after', array($this, 'sales_insight'), 10, 2);
		}

		add_filter('evotx_hidden_order_itemmeta', array($this,'hide_order_item_metafields'),10,1);
	}

	// FRONTEND
		function frontend_temp($boolean, $event){

			$BLOCKS = new EVOBO_Blocks( $event);

			if( !$BLOCKS->is_blocks_active()) return $boolean;

			ob_start();

			$select_year = $select_month = $select_date = '';
			$current_time = current_time('timestamp');

			$EVO_Cal = new EVO_Calendar('evcal_2');

			global $EVOLANG; // get global evo lang value set via tickets addon
			
			$dataset = array(
				'event_id'=>$event->ID,
				'wcid'=> $event->wcid,
				'l'=> $EVOLANG				
			);

			$cal_dataset = array(
				'sow'=> get_option('start_of_week'),
				'cty'=> date('Y',$current_time),
				'ctm'=> date('n',$current_time),
				'ctd'=> date('j',$current_time),
				'm'=> 	$EVO_Cal->get_all_months('L1'),
				'df'=> 	$EVO_Cal->get_all_days('L1','full',true),
				'd3'=> 	$EVO_Cal->get_all_days('L1','three',true),
				'd1'=> 	$EVO_Cal->get_all_days('L1','one',true),
				't1'=> __( evo_lang('Select a date')),
				't2'=> __( evo_lang('Select an available time slot')),
				't3'=> __( evo_lang('No available slots, please try another date!')),
				't4'=> __( evo_lang('Today')),
			);


			?>
			<div class='tx_single evobo_booking_section'>
				<div class='evobo_main_selection evotx_hidable_box'>
					<div class='evobo_calendar' data-dataset='<?php echo json_encode($cal_dataset);?>' ></div>
					<div class='evobo_slots' data-json='<?php echo $BLOCKS->get_json_booking_slots();?>' data-dataset='<?php echo json_encode($dataset);?>'></div>
					<div class='evobo_selections'></div>
				</div>
				<?php /* this is where price info & added to cart msg will go */?>
				<div class="evobo_price_values evobo_style1" style='display:none'></div>	
			</div>

			<?php 
			$content = ob_get_clean();
			return $content;
		}

	// add to CART via AJAX
		function add_to_cart($cart_item_data, $EVENT, $def_price, $DATA){

			$event_data = $DATA['event_data'];

			if( !isset($event_data['booking_index'])) return $cart_item_data;

			$booking_index = $event_data['booking_index'];

			$BLOCKS = new EVOBO_Blocks($EVENT, $event_data['wcid']);
			$BLOCKS->set_block_data($booking_index);

			$max_stock = $BLOCKS->get_item_prop('capacity')? (int)$BLOCKS->get_item_prop('capacity'): 0;

			if($max_stock != 0){
				$qty = (int)$DATA['qty'];

				if($qty <= $max_stock){
					$status = 'good';
					$cart_item_data['evobo_index'] = $booking_index;
					$cart_item_data['evobo_price'] = $BLOCKS->get_item_prop('price');	

					// get proper block time
					$block_time = $BLOCKS->get_block_time_string($booking_index);
					$cart_item_data['evobo_block_time'] = $block_time;
				}
			}

			return $cart_item_data;

		}
		function cart_session_item_data($array){
			$array[] = 'evobo_price';
			$array[] = 'evobo_index';
			$array[] = 'evobo_block_time';
			return $array;
		}
		function cart_ticket_price($boolean, $def_price, $session_data, $values){
			if (array_key_exists( 'evobo_price', $values ) ){
				return $values['evobo_price'];
	       		//$session_data['data']->set_price( $values['evobo_price'] );
	        }
	        return $boolean;
		}

	// CART VIEW
		function cart_ticket_meta_data($data, $values, $EVENT){
			if(isset($values['evobo_block_time'])){
				unset($data['event_time']); // get rid of the event time
				$data['block_data'] = array(evo_lang('Block Time'), $values['evobo_block_time']);
			}
			return $data;
		}

		function cart_item_quantity($product_quantity, $cart_item_key, $cart_item ){
	   		if(empty($cart_item['evobo_price']) ) return $product_quantity;
	   		
	   		$BLOCKS = new EVOBO_Blocks( $cart_item['evotx_event_id_wc'], $cart_item['product_id']);
	   		
	   		$product_quantity = woocommerce_quantity_input( array(
				'input_name'  => "cart[{$cart_item_key}][qty]",
				'input_value' => $cart_item['quantity'],
				'max_value'   => $BLOCKS->has_stock($cart_item['evobo_index']),
				'min_value'   => '0',
			), '', false );

			return $product_quantity;   		
	   	}
	   	function cart_validation($cart_item_key, $cart_item, $event_id){
			if(!isset($cart_item['evobo_index']) ) return false;

			$_ticket_block_index = $cart_item['evobo_index'];

			$BLOCKS = new EVOBO_Blocks($event_id, $cart_item['product_id']);
				
			//$ticket_options_active = $this->fnc->is_ticket_options_active( $event_id);
			$stock_available = $BLOCKS->is_stock_available( $_ticket_block_index, $cart_item['quantity']);

			// if seat not available or ticket sale is not active
			if( !$stock_available){
				//WC()->cart->remove_cart_item($cart_item_key);
				//wc_add_notice( 'Ticket removed from cart, no longer available for sale!', 'error' );
			}
		}

	// CHECKOUT
		// add custom data as meta data to order item	    
			function order_item_meta_update_new($item, $cart_item_key, $values, $order){
				if(isset($values['evobo_block_time']) ){	  
					$item->add_meta_data('Block-Time', $values['evobo_block_time'],true);
		        }		
		        if(isset($values['evobo_index']) )
		        	$item->add_meta_data('_ticket_block_index', $values['evobo_index'],true);
			}

		// remove custom data if item removed from cart
			function update_removed_cart_items($cart_item_key, $values){
		       	// remove block from cart
		    }

		function adjust_ticket_block_item_stock($boolean, $TIX_EVENT, $order, $item_id, $item, $type){
			
    		$block_index = wc_get_order_item_meta($item_id ,'_ticket_block_index'); 

    		if(!$block_index) return $boolean;		

    		$BLOCKS = new EVOBO_Blocks( $TIX_EVENT->ID, $item['product_id']);			    		
    		$BLOCKS->adjust_stock($block_index, $type, $item['quantity']);
    		
    		return true;   			    	
		}

		function ticket_meta_slug_replace($array){
			$array['Block-Time'] = evo_lang('Block Time');
			return $array;
		}

		// save ticket item meta custom values for faster retrieve of data
		function tix_meta_values($array, $item){
			if(!empty($item['Block-Time'])) $array['Block-Time'] = $item['Block-Time']; // @deprecating
			if(!empty($item['Block-Time'])) $array['_block_time'] = $item['Block-Time'];
			if(!empty($item['_ticket_block_index'])) $array['_ticket_block_index'] = $item['_ticket_block_index'];

			return $array;
		}

		// event time field in the checkout under additional information
		function tix_eventtime($event_time, $values){

			// value check
			if(!isset($values['evotx_event_id_wc'])) return $event_time;
			if(!isset($values['evobo_index'])) return $event_time;
			if(!isset($values['product_id'])) return $event_time;

			//print_r($values);
			$event_id = $values['evotx_event_id_wc'];

			$evobo_index = $values['evobo_index'];

			$BLOCKS = new EVOBO_Blocks( $event_id, $values['product_id']);
			$BLOCKS->set_block_data( $evobo_index );

			return $BLOCKS->get_block_time_string();

		}

		// order item meta data returns
		function order_item_meta_data($array, $item){
			$new_array = $array;
			$remove_event_time = false;
			foreach($array as $keyid=>$meta){
				if($meta->key == 'Block-Time') $remove_event_time = true;
			}
			if($remove_event_time){
				foreach($array as $keyid=>$meta){
					if($meta->key == 'Event-Time'){
						unset($new_array[$keyid]);
					}
				}
			}
			return $new_array;
		}

	// ADMIN ONLY	
		// CSV download attendees
			function csv_headers($array, $EVENT){
				if( $EVENT->check_yn('_evobo_activate')){
					$array[]= 'Block Time';
				}
				return $array;
			}
			function csv_row($array, $tn, $td, $EVENT){
				if( $EVENT->check_yn('_evobo_activate') ){
					$array['block_time']= isset($td['oD']['block_time'])? '"'.$td['oD']['block_time'].'"':'';
				}
				return $array;
			}

		// show ticket booking information for evo-tix cpt
		function evo_tix_table_row($post_id, $ticketItem_meta){
			if(!empty($ticketItem_meta['Block-Time'])): ?>
			
			<tr><td><?php _e('Block Time','eventon');?>: </td>
			<td><?php echo $ticketItem_meta['Block-Time'][0];?></td></tr>

			<?php endif;
		}

		function view_attendees($array, $event_id){

			if(!isset($array['id'])) return $array;
			$evo_tix_id = $array['id'];

			$BT = get_post_meta($evo_tix_id, 'Block-Time',true);
			
			if(!$BT) return $array;

			$array['oD']['block_time'] = $BT;
			unset($array['oD']['event_time']);

			$bi = get_post_meta($evo_tix_id, '_ticket_block_index',true);
			if($bi) $array['oDD']['block_index'] = $bi;
			return $array;

		}
		function hide_order_item_metafields($array){
			$array[] = '_ticket_block_index';
			$array[] = '_block_time';
			return $array;
		}

		// sales insight
			function sales_insight($EVENT, $orders){
				?>
				<div class='evotxsi_row sales_by_booking_slots'>
					<h2><?php _e('Sales by booking slots','evotx');?></h2>	
					<h3><?php _e('Top 3 countries where customers have placed orders from','evotx');?></h3>			
					
				</div>
				
				<?php
			}

	// Show booking information for confirmation ticket email
		function tix_confirmation_email($ticket_item_id, $ticket_pmv, $styles,$ticket_number, $tix_holder_index,$event_id){
			if(!empty($ticket_pmv['Block-Time'])):
				$Block_time = 	$ticket_pmv['Block-Time'][0] ;
			?>			
			<div>
				<p style="<?php echo $styles['005'].$styles['pb5'].$styles['pt10'];?>"><?php echo $Block_time;?></p>
				<p style="<?php echo $styles['004'].$styles['pb5'];?>"><?php echo evo_lang( 'Block Information');?></p>
			</div>		
			<?php endif;
		}
}
new EVOBO_Tickets_Int();