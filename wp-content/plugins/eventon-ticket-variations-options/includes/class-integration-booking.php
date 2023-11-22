<?php
/**
 * Integration with Booking Addon
 * @version 1.1.2
 */

class EVOVO_BO{
	public $parent_type ='booking';

	public function __construct(){
		if( !class_exists('EVOBO_Blocks')) return false;
		if(is_admin()){
			add_action('evobo_new_block_form', array($this, 'new_block_form'), 10, 3);
			add_action('evovo_after_save', array($this, 'save_block_variation'), 10, 4);
			add_filter('evobo_after_save_block', array($this, 'save_booking_block'),10,3);
			add_filter('evovo_variations_form_fields', array($this, 'variations_form'),10,4);

			add_action('evobo_auto_generator_form', array($this, 'autogen_form'),10,2);
			add_action('evobo_autogen_after_saved', array($this, 'autogen_slot'),10,3);

			add_action('evobo_delete_all_blocks', array($this, 'delete_all'),10,1);
			add_action('evobo_delete_single_blocks', array($this, 'delete_single'),10,2);
			
		}

		// front end 
		add_filter('evobo_block_preview', array($this, 'preview_blocks'), 10, 3);
		add_filter('evovo_ticket_frontend_mod', array($this, 'frontend_mod'),10, 4);
		add_action('evovo_add_to_cart_before', array($this, 'default_values'), 10, 1);
		
		add_filter('evovo_vo_item_stock_return', array($this, 'evovo_vo_item_stock_return'), 10, 2);
		
	
		add_filter('evobo_blocks_json', array($this, 'json_blocks'), 10, 3);


	}

	// passing VO data into json
		function json_blocks($json, $booking_index, $BLOCKS){

			$VO = new EVOVO_Var_opts($BLOCKS->event, '', 'variation');
			$BO_vs = $VO->get_parent_vos($booking_index, 'booking');

			if( $BO_vs && count($BO_vs)>0){
				$json['vo_var']='y';				
			}

			$VO->set_new_method('option');
			$BO_pos = $VO->get_parent_vos($booking_index, 'booking');

			if( $BO_pos && count($BO_pos)>0){
				$json['vo_opt']='y';				
			}

			return $json;
		}

	// FRONTEND
		// evovo_data array
			function evovo_vo_item_stock_return( $stock, $class){

				// if variations are set
				if( $class->method == 'variation' && !$stock && $class->get_parent_type() == 'booking'){
					
					$BLOCKS = new EVOBO_Blocks( $class->event);
					$BLOCKS->set_block_data( $class->get_parent_id() );

					$block_stock = $BLOCKS->has_stock();

					$stock = $block_stock;
				}
				return $stock;
			}

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
			public function autogen_form($BLOCKS, $rand_id){
				
				if( !$BLOCKS->event->check_yn('_evovo_activate')) return false;

				$VO = new EVOVO_Var_opts($BLOCKS->event, (int)$_POST['wcid']);

				echo "<div id='evovo_ext_section' class='block_generator_vos'>";

				$VO->print_all_vos_container_html( $rand_id, 'booking_generator',true );
			
				//print_r($VO->get_all_vo_data_for_parent($rand_id, 'booking_generator') );				
				
				?><p class='evovo_booking_actions' ><?php	
					echo $VO->get_vos_action_btn_html($rand_id, 'booking_generator', true, false); 
				?></p><?php

				echo "</div>";
			}

			// save vo values for each booking block generated auto
			public function autogen_slot($slots, $BLOCKS, $P){

				$VO = new EVOVO_Var_opts($BLOCKS->event);

				$x = 1;

				// get all VO data for booking_generator
				$all_vo_data = $VO->get_all_vo_data_for_parent( 'G123456','booking_generator',true);

				//print_r($all_vo_data);

				// run through all auto generated slots
				foreach( array('variation', 'option') as $method){

					// if VO method not set -> pass
					if( !isset( $all_vo_data[ $method]) ) continue;
					if( count( $all_vo_data[ $method] ) == 0) continue;

					foreach($all_vo_data[ $method] as $vo_id=>$vo_data){
						foreach($slots as $block_id){
							$unique_index = rand(100000, 900000);
							$new_vo_id = $unique_index + 1;

							$all_vo_data[$method][$new_vo_id] = $all_vo_data[$method][$vo_id];

							$all_vo_data[$method][$new_vo_id]['parent_id'] = $block_id;
							$all_vo_data[$method][$new_vo_id]['parent_type'] = 'booking';
						}						
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

				// delete the auto gen VOS
				$VO->delete_allitems_for_parent('G123456','booking_generator');

			}

		// variations form from booking
			function variations_form( $fields, $post, $values, $EVENT){

				if( $post['parent_type'] == 'booking' && $post['method'] == 'variation'){
					$block_index = $post['parent_id'];
					$BLOCKS = new EVOBO_Blocks( $EVENT);
					$BLOCKS->set_block_data( $block_index );

					$block_stock = $BLOCKS->has_stock();


					$fields['evovobo_notice'] =array(
						'name'=> __('NOTE: Variations stock must be less than block capacity','evovo') . 
							($block_stock ? ': '. $block_stock: '' ) .' ' . __('If variation stock left blank for unlimited, variations stock will be capped at block capacity.','evovo'),
						'type'=>'notice',
					);
				}

				return $fields;
			}

		// booking block form -> VO html
			function new_block_form( $EVENT, $block_index, $post){

				if(!$EVENT->check_yn('_evovo_activate')) return false;

				$form_type = isset( $post['type'])? $post['type']:'new';

				// not show for new form
				if( $form_type == 'new') return;
				
				$VO = new EVOVO_Var_opts($EVENT, $post['wcid']);
				
				echo "<div id='evovo_ext_section'>";

				$VO->print_all_vos_container_html( $block_index, 'booking',true );
				
				?><p class='evovo_booking_actions' ><?php	
					echo $VO->get_vos_action_btn_html($block_index, 'booking', true, false); 
				?></p><?php
				
				echo "</div>";
			}
		
		// Create VO for booking block
			function save_block_variation( $new_vo_data , $EVENT, $VO, $PP ){

				extract($PP);

				if( $parent_type != 'booking' && $parent_type != 'booking_generator' ) return false;

				// save VO data to booking
					$BLOCKS = new EVOBO_Blocks( $EVENT);
					$BLOCKS->save_block_prop($parent_id, 'has_vos', true);
							
				// new VO section HTML					
					ob_start();
					$VO->print_all_vos_container_html( $parent_id, $parent_type ,false );
					$html = ob_get_clean();

					$VO->set_new_method('variation');

				// return
				echo json_encode( array(
					'content'			=> $html, 
					'msg'			=> 'New Booking block variation created!',
					'status'		=> 'good',
					'total_block_cap'=> $VO->get_total_stock_for_method($parent_id,$parent_type ),
				));exit;		
			}

		// when save booking block
			public function save_booking_block($index, $BLOCKS, $post){
				
				// get all booking VOS
				$VO = new EVOVO_Var_opts($BLOCKS->event);
				$VO->set_new_method('variation');

				// if variations set update block capacity
				if( $VO->is_set()){

					$stock = 0;

					$vos = $VO->get_parent_vos($index, 'booking');

					// for each variation calculate stock
					foreach($vos as $vo_id=>$vo){
						$VO->set_item_data($vo_id);
						if(!$VO->in_stock()) continue;
						//$s = $this->get_item_stock();
						$s = $VO->get_item_prop('stock');
						if($s) $stock +=$s;
					}

					// if variations have set stock number > use it as block stock
					if( $stock > 0  ) {
						$BLOCKS->save_block_prop($index, 'capacity', $stock);
					}
				}
				
				return;
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