<?php
/**
 * AJAX
 * not using anymore
 */
class evovo_ajax{
	public function __construct(){
		$ajax_events = array(
			//'evovo_add_to_cart'=>'evovo_add_to_cart',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

// deprecated
	function evovo_add_to_cart(){

		$event_data = $_POST['event_data'];
		$evovo_data = $_POST['evovo_data'];
		$event_id = $event_data['eid'];
		$wcid = $event_data['wcid'];

		global $product;
		$product = wc_get_product( $wcid );
		$variation_base_price = $product->get_price();

		$var_id = isset($evovo_data['var_id'])? $evovo_data['var_id']:'';
		$qty = $_POST['qty'];
		$price_options = isset($evovo_data['po'])? $evovo_data['po']:false;
		$vart = isset($evovo_data['vart'])?$evovo_data['vart'] : false;

		$EVENT = new EVO_Event( $event_id);
		$VOs = new EVOVO_Var_opts($EVENT, $wcid ,'variation');
		
		$status = 'good'; $output = ''; $cart_item_keys = array();

		$item_price_additions = 0;
		$outofstock = false;

		// Create cart item data
			// for each variation types
			if(sizeof($vart)>0){
				$cart_item_data['evovo_data']['vart'] = $vart;
			}

			// foreach price options
			if($price_options && sizeof($price_options)>0){
				$OPs = new EVOVO_Var_opts($EVENT, $wcid,'option');
				foreach($price_options as $po_id=>$po_val){
					$OPs->set_item_data( $po_id);
					$sin_price = $OPs->get_item_prop('regular_price');
					$sin_stock = $OPs->get_item_prop('stock');

					$po_qty = ( isset($po_val['qty'])? $po_val['qty']:1);

					// price option qty is more than available stock
					if( $sin_stock && $po_qty > $sin_stock){
						$outofstock = true; continue;
					}

					// price option price addition
					$po_price = $sin_price * $po_qty;

					$item_price_additions += $po_price;
				}
				
				$cart_item_data['evovo_data']['po'] = $price_options;
			}

		// ticket variations
			if(!empty($var_id)){
				$cart_item_data['evovo_data']['var_id'] = $var_id;
				$VOs->set_item_data($var_id);
				if($VOs->get_item_prop('regular_price'))
					$variation_base_price = $VOs->get_item_prop('regular_price');
			}
			
		// set item base data
			$total_item_price = $variation_base_price + $item_price_additions;

			$cart_item_data['evovo_price'] = $total_item_price;
			$cart_item_data['evotx_event_id_wc'] = $event_id;
			$cart_item_data['evotx_repeat_interval_wc'] = $event_data['ri'];

		// if any part of item is out of stock
			if($outofstock){
				echo json_encode(array(
					'msg'=> __('Item out of stock!'), 
					'status'=> 'bad',
				)); exit;
			}

			//print_r($cart_item_data);

		// add to cart object
			$cart_item_keys[] = WC()->cart->add_to_cart(
				$wcid,
				$qty,0,array(),
				$cart_item_data
			);
		
		
		if(sizeof($cart_item_keys)>0){
			$tx_help = new evotx_helper();
			$output = $tx_help->add_to_cart_html();
			$msg = evo_lang('Ticket added to cart successfully!');
		}else{
			$msg = evo_lang('Could not add ticket to cart, please try later!');
		}

		echo json_encode(array(
			'msg'=>$msg, 
			'status'=> $status,
			'html'=>$output
		)); exit;
	}
}
new evovo_ajax();