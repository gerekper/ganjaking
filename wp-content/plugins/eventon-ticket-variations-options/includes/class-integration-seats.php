<?php
/**
 * Integration with Seats addon
 * @version v 1.1.1
 */

class EVOVO_Seats{
	public $o;

	public function __construct(){
		if( !class_exists('EVO_seats')) return false;
		if(is_admin()){
			add_action('evost_admin_formfields', array($this, 'form'), 10, 3);
			add_action('evovo_after_save', array($this, 'save_block_variation'), 10, 4);
			add_action('evovo_after_delete', array($this, 'after_delete_vo'), 10, 3);
			add_filter('evovo_variations_form_fields', array($this, 'new_v_form'), 10, 2);
			add_action('evost_duplicate_section_after_save', array($this, 'duplicate_section'), 10, 4);
			add_action('evost_delete_item', array($this, 'after_seat_delete'), 10, 2);
		}

		// front end 
		add_filter('evovo_ticket_frontend_mod', array($this, 'frontend_mod'),10, 4);
		add_filter('evost_seat_preview', array($this, 'preview_vo_seats'), 10, 2);
		add_filter('evost_seat_prev_before_total_price', array($this, 'preview_before_total_price'), 10, 1);
		add_action('evovo_add_to_cart_before', array($this, 'seat_base_override'), 10, 1);		
		add_filter('evost_seats_in_cart_json', array($this, 'show_vo_seats'), 10, 4);

		// stock validation
		add_filter('evovo_is_instock_check', array($this, 'in_stock_check'), 10, 3);
		add_filter('evovo_var_in_stock', array($this, 'cart_stock_validation'), 10, 4);
	}

	// frontend
		// is in stock check
		function in_stock_check($status, $return, $EVENT){
			$ST = new EVOST_Seats( $EVENT);
			if( $ST->is_seats_active()) return true;
			return $status;
		}

		// cart validation for stock
		function cart_stock_validation($stock, $V, $EVENT, $cart_item){
			
			if( !isset($cart_item['evost_data'])) return $stock;

			$seat_data = $cart_item['evost_data'];
			$ST = new EVOST_Seats( $EVENT, $cart_item['product_id']);

			if( $ST->is_seats_active()){
				// check if this is unassigned section
				if( isset($seat_data['seat_type']) && $seat_data['seat_type'] == 'unaseat'){
					return $stock;
				}
				
				// 1 seat 
				return 1;
			}
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

			if( !$VOs->is_vo() ) return $boolean;
			if( !$VOs->is_set() && !$POs->is_set() ) return $boolean;

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

		// before total price @since 1.1.1
			function preview_before_total_price($ST){
				$_vos = $ST->get_section_prop('has_vos');
				if(!$_vos) return;

				echo "<div class='evovo_price_option_prices_container'></div>";
				echo "<div class='evovo_price_option_prices_container_extra'></div>";
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

					$seats['seat'][$cart_item_key]['price'] = html_entity_decode( $TXHelp->convert_to_currency($base_price) );

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
					if(isset($values['evovo_data']['po'])){
						$POs = new EVOVO_Var_opts($ST->event, $ST->wcid,'option');
						
						foreach($values['evovo_data']['po'] as $po_id=>$data){
							$POs->set_item_data($po_id);

							$qty = isset($data['qty'])? $data['qty']: 1;							
							$total_price = $POs->get_item_prop('regular_price') * $qty;

							$price_additions += $total_price;

							$seats['seat'][$cart_item_key]['otherdata'][$po_id] = array(
								'label'=> $POs->get_vos_name($po_id) . ' x'. $data['qty'], 
								'price'=> html_entity_decode( $TXHelp->convert_to_currency($total_price) )
							);
						}
					}

					// if there are price additions
					if($price_additions>0){
						$total_price = $price_additions + $base_price;
						$seats['seat'][$cart_item_key]['totalprice'] = html_entity_decode( $TXHelp->convert_to_currency($total_price) );
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

			$HELP = new evo_helper();
			$POST = $HELP->sanitize_array($_POST);
			$json = $POST['json'];

			// if unassigned seats > enable capacity
			$ST = new EVOST_Seats( $json['event_id'], $json['wcid']);
			$ST->set_section($json['parent_id']);
			if( $ST->get_item_prop('type') == 'una') return $fields;

			unset($fields['stock']);
			unset($fields['stock_status']);

			return $fields;
		}

		function form($key, $form_data, $SEATS){

			
			// do not show vos for new form without section ID
			if(!isset($form_data['section_id'])) return false;
			if( !isset($form_data['type'])) return false;
			if( $form_data['type'] == 'aoi') return false;

			$VO = new EVOVO_Var_opts($SEATS->event, $form_data['wcid']);
			if(!$VO->is_vo()) return false;

			//$VO->delete_allitems_for_parent(6307, 'seat');
			
			//$all_vo_data = $VO->get_all_vo_data_for_parent( 6207,'seat',true);

			//print_r($all_vo_data);

			echo "<div id='evovo_ext_section'>";

			$VO->print_all_vos_container_html( $form_data['section_id'], 'seat',true );
			
			?><p class='evovo_seat_actions' >
				<?php	echo $VO->get_vos_action_btn_html($form_data['section_id'], 'seat', true); ?>
				</p><?php

			echo "</div>";
		}

		// duplicate VOs for section
			function duplicate_section($item_data, $original_section_id, $duplicate_section_id, $SEATS){

				if( !$SEATS->get_section_prop('has_vos')) return false;

				$VO = new EVOVO_Var_opts($SEATS->event, $SEATS->wcid);

				$all_vo_data = $VO->get_all_vo_data_for_parent( $original_section_id,'seat',true);

				//print_r($all_vo_data);

				// run through all auto generated slots
				foreach( array('variation', 'option') as $method){

					// if VO method not set -> pass
					if( !isset( $all_vo_data[ $method]) ) continue;
					if( count( $all_vo_data[ $method] ) == 0) continue;

					foreach($all_vo_data[ $method] as $vo_id=>$vo_data){
						$unique_index = rand(100000, 900000);
						$new_vo_id = $unique_index + 1;

						$all_vo_data[$method][$new_vo_id] = $all_vo_data[$method][$vo_id];
						$all_vo_data[$method][$new_vo_id]['parent_id'] = $duplicate_section_id;
					}
				}

				// for each method save new vo data to event
				foreach( array('variation', 'option') as $method){
					if(!is_array( $all_vo_data[ $method ] )) continue;
					if( count($all_vo_data[ $method ]) == 0) continue;
					
					$VO->set_new_method( $method, false );
					$VO->save_dataset( $all_vo_data[ $method ] );
				}

				//print_r($all_vo_data);
			}

		// after seat item is deleted
			function after_seat_delete( $SEATS, $formdata){
				if( !$SEATS->get_section_prop('has_vos')) return false;
				if( $formdata['item_type'] != 'section') return false;
				if( !isset($formdata['section_id']) ) return false;
				$VO = new EVOVO_Var_opts($SEATS->event);

				$all_vo_data = $VO->get_all_vo_data_for_parent( $formdata['section_id'],'seat',true);

				$VO->delete_allitems_for_parent($formdata['section_id'], 'seat');
			}

		// create VO for seat section
			function save_block_variation( $new_vo_data , $EVENT, $VO, $PP){

				extract($PP);

				if( $parent_type != 'seat' ) return false;
				
				$vo_id = $new_vo_data['vo_id'];

				//save VO data into seat
					$ST = new EVOST_Seats( $EVENT, $wcid );
					$ST->set_section($parent_id);

					$ST->set_section_prop('has_vos', $vo_id);

				ob_start();
				$VO->print_all_vos_container_html( $parent_id, 'seat',false );

				$html = ob_get_clean();


				echo json_encode( array(
					'content'=>	$html, 
					'msg'=> 	__('New Seat Variation Options added!'),
					'status'=>	'good',
				));exit;

			}
			function after_delete_vo( $EVENT, $VO, $PP){
				extract($PP);
				if( $parent_type != 'seat') return false;

				ob_start();
				$VO->print_all_vos_container_html( $parent_id, 'seat',false );
				$html = ob_get_clean();

				echo json_encode( array(
					'content'=>	$html, 
					'msg'=> 	__('Successfully Deleted!'),
					'status'=>	'good',
				));exit;

			}
}
new EVOVO_Seats();