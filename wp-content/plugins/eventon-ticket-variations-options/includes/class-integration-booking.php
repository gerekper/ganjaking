<?php
/**
 * Integration with Booking Addon
 */

class EVOVO_BO{

	public function __construct(){
		if( !class_exists('EVOBO_Blocks')) return false;
		if(is_admin()){
			add_action('evobo_new_block_form', array($this, 'new_block_form'), 10, 3);
			add_action('evovo_save_vo_before_save', array($this, 'generate_vo_for_booking'), 10, 4);
			add_action('evobo_admin_booking_slot_data',array($this, 'edit_event_booking_block_data'), 10, 2);
			add_filter('evobo_save_booking_block_data', array($this, 'save_booking_block'),10,3);

			add_action('evobo_auto_generator_form', array($this, 'autogen_form'),10,1);
			add_action('evobo_autogen_after_saved', array($this, 'autogen_slot'),10,3);

			add_action('evobo_delete_all_blocks', array($this, 'delete_all'),10,1);
			add_action('evobo_delete_single_blocks', array($this, 'delete_single'),10,2);
		}

		// front end 
		add_filter('evobo_block_preview', array($this, 'preview_blocks'), 10, 3);
		add_filter('evovo_ticket_frontend_mod', array($this, 'frontend_mod'),10, 4);
		add_action('evovo_add_to_cart_before', array($this, 'default_values'), 10, 1);		
	}

	// FRONTEND
		// make sure if blocks are enable show blocks instead of VOs
			function frontend_mod($boolean, $EVENT, $content, $product){
				$BLOCKS = new EVOBO_Blocks( $EVENT);
				if( $BLOCKS->is_blocks_active()){
					return $content;
				}
				return $boolean;
			}

		// show VO values in final booking stage if available
		function preview_blocks($boolean, $BLOCKS){

			if( !$BLOCKS->block_id) return $boolean;
			$block_index = $BLOCKS->block_id;

			// check for variations for the block
			$VOs = new EVOVO_Var_opts($BLOCKS->event, $BLOCKS->wcid,'variation');

			if( !$VOs->is_vo()) return $boolean;
							
			$evotx_data = array();
			$evotx_data['event_data']['booking_index'] = $block_index;

			// get VO HTML while passing pluggable value to avoid footer msg not including in return
			$VO_html =  $VOs->print_frontend_html(
				$block_index, 'booking', $evotx_data, '', array(
					'default_price'=> $BLOCKS->get_item_prop('price'),
					'default_max_qty'=> $BLOCKS->has_stock(),
					'pluggable'=>true,
					'show_pricing_fields'=> false
				)
			);

			if(!$VO_html) return $boolean;

			return $VO_html;
		}

		// Base price override
			function default_values($O){
				$this->o = $O;

				// price
				add_filter('evobo_base_price', function($price){
					return isset($this->o->evotx_data['evovo_data']['defp']) ? 
						$this->o->evotx_data['evovo_data']['defp'] : $price;
				});

				// capacity
				add_filter('evobo_base_capacity', function($capacity){
					return (isset($this->o->evotx_data['evovo_data']['outofstock']) && $this->o->evotx_data['evovo_data']['outofstock']) ? 
						false : $capacity;
				});
			}

	// ADMIN
		// auto generator form
			public function autogen_form($BLOCKS){
				
				if( !$BLOCKS->event->check_yn('_evovo_activate')) return false;

				$VO = new EVOVO_Var_opts($BLOCKS->event, (int)$_POST['wcid']);

				echo "<div id='evovo_ext_section'>";

					echo "<div class='evovo_vos_container_booking evovo_vos_container' data-pt='booking'></div>";

					?><p class='evovo_booking_actions' ><?php	
						echo $VO->get_vos_action_btn_html('', 'booking', true, false); 
					?></p><?php
					
					echo "<div class='evovo_all_vo_data' data-all_vo_data=''></div>";

				echo "</div>";
			}

			// save vo values for each booking block generated auto
			public function autogen_slot($slots, $BLOCKS, $P){

				if( !isset($P['all_vo_data'])) return;

				$all_vo_data = $P['all_vo_data'];
				
				$VO = new EVOVO_Var_opts($BLOCKS->event);

				$new_vo_data = array('variation'=> array(),'option'=> array());

				// for each method load all existing vo data for the event
				foreach( $new_vo_data as $method=>$data){

					// set existing data
					$VO->set_new_method( $method);
					$new_vo_data[ $method ] = $VO->dataset;
				}

				$x = 1;

				// run through all auto generated slots
				foreach($slots as $booking_id){
					foreach( array('variation', 'option') as $method){
						if(isset($all_vo_data[ $method ]) && is_array( $all_vo_data[ $method ] )){
							foreach( $all_vo_data[ $method ] as $vo_id=>$data){
								unset($data['event_id']);
								unset($data['save']);
								unset($data['wcid']);
								unset($data['vo_id']);
								unset($data['all_vo_data']);

								$data['parent_id'] = $booking_id;
								$data['parent_type'] = 'booking';

								
								$new_vo_data[$method][ $vo_id. $x ] = $data;
								$x++;
							}
						}
					}
				}

				// for each method save new vo data to event
				foreach( array('variation', 'option') as $method){
					if(!is_array( $new_vo_data[ $method ] )) continue;
					if( count($new_vo_data[ $method ]) == 0) continue;
					$VO->method = $method;

					$BLOCKS->event->set_prop('_evovo_'. $method, $new_vo_data[ $method ]);
				}
			}

		// append variations section to booking block form
			function new_block_form( $EVENT, $block_index, $post){

				if(!$EVENT->check_yn('_evovo_activate')) return false;

				$form_type = isset( $post['type'])? $post['type']:'new';
				
				$have_VOs = false; $buttons = '';

				$VO = new EVOVO_Var_opts($EVENT, $post['wcid']);
				$all_vo_data = $VO->get_all_vo_data_for_parent( $block_index, 'booking',true);	

				//print_r($all_vo_data);		
				
				echo "<div id='evovo_ext_section'>";
				echo "<div class='evovo_vos_container_booking evovo_vos_container' data-pid='{$block_index}' data-pt='booking' data-eid='". $EVENT->ID ."' data-wcid='".$post['wcid']."'>";
				
				// get saved vo data
				if( $form_type != 'new')
					echo $VO->get_all_vos_html($block_index, 'booking',true, $all_vo_data);
				
				echo "</div>";

				?><p class='evovo_booking_actions' ><?php	
					echo $VO->get_vos_action_btn_html($block_index, 'booking', true, false); 
				?></p><?php

				// print all vo data 				
				echo "<div class='evovo_all_vo_data' data-all_vo_data='". json_encode($all_vo_data)."'></div>";

				echo "</div>";
			}
		
		// Create VO for booking parent item
			function generate_vo_for_booking( $vo_data, $all_vo_data, $EVENT, $VO ){
				
				if( !isset($vo_data['parent_type'])) return false;
				if( !isset($vo_data['parent_id']) || empty($vo_data['parent_id'])) return false;
				if( $vo_data['parent_type'] != 'booking') return false;
				//if( isset($vo_data['save']) && $vo_data['save'] != 'no') return false;

				$booking_index = $vo_data['parent_id'];
				
				$html = $VO->get_all_vos_html( $booking_index, 'booking',true, $all_vo_data);

				echo json_encode( array(
					'html'			=> $html, 
					'msg'			=> 'New Booking block variation created!',
					'status'		=> 'good',
					'all_vo_data' 	=> $all_vo_data,
					'data'			=> $vo_data
				));exit;	

				// this will stop from vo ajax completing			
			}

		// save booking block
			public function save_booking_block($post, $index, $BLOCKS){
				
				if(!isset($post['all_vo_data'])) return $post;
				if(!is_array($post['all_vo_data'])) return $post;

				$VO = new EVOVO_Var_opts($BLOCKS->event);

				// save booking vo_data to event's vo data array'
				$vo_id ='';
				$vo_id = $VO->save_parent_vo_data($index, 'booking', $post['all_vo_data']);	

				// for variations update block capacity
					if( isset($post['all_vo_data']['variation'])){
						$VO->set_new_method( 'variation');
						
						$s = $VO->get_total_stock_for_method($index, 'booking');

						if($s)	$BLOCKS->save_block_prop($index, 'capacity', $s);
					}

				unset($post['all_vo_data']);
				$post['vo_id'] = $vo_id;

				return $post;
			}

		// Edit event booking row data
			function edit_event_booking_block_data($booking_index, $BLOCKS){

				$VO = new EVOVO_Var_opts($BLOCKS->event, '', 'variation');
				$BO_vs = $VO->get_parent_vos($booking_index, 'booking');

				if( count($BO_vs)>0){
					?>
					<span class="ebobo_v" style='padding-left:10px'><i class='fa fa-random' title='<?php _e('Has Variations','eventon');?>'></i></span>
					<?php
				}

				$PO = new EVOVO_Var_opts($BLOCKS->event, '', 'option');
				$BO_pos = $PO->get_parent_vos($booking_index, 'booking');

				if( count($BO_pos)>0){
					?>
					<span class="ebobo_po" style='padding-left:10px'><i class='fa fa-plug' title='<?php _e('Has Price Options','eventon');?>'></i></span>
					<?php
				}
			}

		// DELETE parents and delete vos
			public function delete_all($BLOCKS){
				$VO = new EVOVO_Var_opts($BLOCKS->event);

				$VO->delete_allitems_for_parent('', 'booking');
			}
			public function delete_single($block_index, $BLOCKS){
				$VO = new EVOVO_Var_opts($BLOCKS->event);

				$VO->delete_allitems_for_parent($block_index, 'booking');
			}

}
new EVOVO_BO();
?>