<?php
/**
 * Integration with Seats addon
 */

class EVOVO_Seats{
	public function __construct(){
		if( !class_exists('EVO_seats')) return false;
		if(is_admin()){
			add_action('evost_admin_formfields', array($this, 'form'), 10, 3);
			add_action('evovo_save_vo_before_echo', array($this, 'save_block_variation'), 10, 3);
			add_filter('evovo_variations_form_fields', array($this, 'new_v_form'), 10, 2);
		}

		// front end 
		add_filter('evovo_ticket_frontend_mod', array($this, 'frontend_mod'),10, 4);
		add_filter('evost_seat_preview', array($this, 'preview_vo_seats'), 10, 2);
		add_action('evovo_add_to_cart_before', array($this, 'seat_base_override'), 10, 1);		
		add_filter('evost_seats_in_cart_json', array($this, 'show_vo_seats'), 10, 4);

		// stock validation
		add_filter('evovo_is_instock_check', array($this, 'in_stock_check'), 10, 3);
		add_filter('evovo_var_in_stock', array($this, 'cart_stock_validation'), 10, 3);
	}

	// frontend
		// is in stock check
		function in_stock_check($status, $return, $EVENT){
			$ST = new EVOST_Seats( $EVENT);
			if( $ST->is_seats_active()) return true;
			return $status;
		}

		// cart validation for stock
		function cart_stock_validation($stock, $V, $EVENT){
			$ST = new EVOST_Seats( $EVENT);
			if( $ST->is_seats_active()) return 1;
			return $stock;
		}

		// make sure if seats are enable show seat map instead of VOs
		function frontend_mod($boolean, $EVENT, $content, $product){
			$ST = new EVOST_Seats( $EVENT);
			if( $ST->is_seats_active()){
				return $content;
			}
			return $boolean;
		}
		// echo preview seats before adding to cart
		function preview_vo_seats($boolean, $ST){
			$_vos = $ST->get_section_prop('has_vos');

			if(!$_vos) return $boolean;

			// check for variations for the block
			$VOs = new EVOVO_Var_opts($ST->event, $ST->wcid,'variation');
			$POs = new EVOVO_Var_opts($ST->event, $ST->wcid,'option');

			if(!$VOs->is_vo()) return $boolean;
			if(!$VOs->is_set() && !$POs->is_set()) return $boolean;

			$evotx_data = array();
			$evotx_data['event_data']['section_id'] = $ST->section;

			// get VO HTML while passing pluggable value to avoid footer msg not including in return
			$VO_html =  $VOs->print_frontend_html(
				$ST->section, 
				'seat', 
				$evotx_data, 
				'', 
				array(
					'default_price'=> $ST->get_price(),
					'default_max_qty'=> $ST->get_max_capacity(),
					'pluggable'=>true,
					'show_pricing_fields'=> false
				)
			);

			if(!$VO_html) return $boolean;

			return $VO_html;
		}

		// seat base price override
			function seat_base_override($O){
				$this->o = $O;

				// price
				add_filter('evost_seat_base_price', function($price){
					return isset($this->o->evotx_data['evovo_data']['defp']) ? 
						$this->o->evotx_data['evovo_data']['defp'] : $price;
				});
			}

		// show vos in tickets in cart on event page JSON
			function show_vo_seats($seats, $values, $cart_item_key, $ST){

				if(isset($values['evovo_data']) ){
					$TXHelp = new evotx_helper();

					$price_additions = 0;
					$base_price = $values['evovo_data']['def_price'];

					$seats['seat'][$cart_item_key]['price'] = $TXHelp->convert_to_currency($base_price);

					// variations
					if(isset($values['evovo_data']['vart'])){
						$VTs = new EVOVO_Var_opts($ST->event, $ST->wcid ,'variation_type');

						foreach($values['evovo_data']['vart'] as $vt_id=>$vt_val){
							$VTs->set_item_data($vt_id);
							$seats['seat'][$cart_item_key]['otherdata'][$vt_id] = array(
								'label'=> $VTs->get_item_prop('name'), 
								'price'=> $vt_val
							);
						}
					}

					// price options
					if(isset($values['evovo_data']['pot'])){
						$POs = new EVOVO_Var_opts($ST->event, $ST->wcid,'option');
						foreach($values['evovo_data']['pot'] as $po_id=>$data){
							$POs->set_item_data($po_id);

							$qty = isset($data['qty'])? $data['qty']: 1;							
							$total_price = $POs->get_item_prop('regular_price') * $qty;

							$price_additions += $total_price;

							$seats['seat'][$cart_item_key]['otherdata'][$po_id] = array(
								'label'=> $POs->get_vos_name($po_id) . ' x'. $data['qty'], 
								'price'=> $TXHelp->convert_to_currency($total_price)
							);
						}
					}

					// if there are price additions
					if($price_additions>0){
						$total_price = $price_additions + $base_price;
						$seats['seat'][$cart_item_key]['totalprice'] = $TXHelp->convert_to_currency($total_price);
					}
				}

				return $seats;
			}
	
	// BACKEND
		// add new  or edit variation form fields
		function new_v_form($fields, $post){
			if(!isset($_POST['json'])) return $fields;
			if(!isset($_POST['json']['parent_type'])) return $fields;
			if($_POST['json']['parent_type'] != 'seat') return $fields;

			unset($fields['stock']);
			unset($fields['stock_status']);

			return $fields;
		}
		function form($key, $form_data, $SEATS){
			
			// do not show vos for new form without section ID
			if(!isset($form_data['section_id'])) return false;

			$VO = new EVOVO_Var_opts($SEATS->event, $form_data['wcid']);
			if(!$VO->is_vo()) return false;

			echo "<div id='evovo_ext_section'>";
			echo "<div class='evovo_vos_container_seat evovo_vos_container' data-pid='' data-pt='seat'>";

			$VO = new EVOVO_Var_opts($SEATS->event, $form_data['wcid']);
			echo $VO->get_all_vos_html($form_data['section_id'], 'seat',true);
			
			echo "</div>";

			?><p class='evovo_seat_actions' ><?php	echo $VO->get_vos_action_btn_html($form_data['section_id'], 'seat', true); ?></p><?php

			echo "</div>";

		}

		// save/ update booking block variation
			function save_block_variation($vo_data, $EVENT, $json){
				
				if( !isset($json['parent_type'])) return false;
				if( !isset($json['parent_id'])) return false;
				if( $json['parent_type'] != 'seat') return false;

				$vo_id = $vo_data['vo_id'];


				// save VO for the booking block
				$section_id = $json['parent_id'];

				$ST = new EVOST_Seats( $EVENT, $json['wcid']);
				$ST->set_section($section_id);

				$ST->set_section_prop('has_vos', $vo_id);

				// for variations update block capacity
				$VO = new EVOVO_Var_opts($EVENT);
				$html = $VO->get_all_vos_html($section_id, 'seat',true);

				echo json_encode( array(
					'html'=>	$html, 
					'msg'=> 	'New Seat Variation Options added!',
					'status'=>	'good',
					'data'=> ' vo_id:'.$vo_id
				));exit;

			}
}
new EVOVO_Seats();