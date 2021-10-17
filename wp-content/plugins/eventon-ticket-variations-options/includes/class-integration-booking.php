<?php
/**
 * Integration with Booking Addon
 */

class EVOVO_BO{

	public function __construct(){
		if( !class_exists('EVOBO_Blocks')) return false;
		if(is_admin()){
			add_action('evobo_new_block_form', array($this, 'new_block_form'), 10, 2);
			add_action('evovo_save_vo_before_echo', array($this, 'save_block_variation'), 10, 4);
			add_action('evobo_admin_booking_slot_data',array($this, 'edit_event_booking_block_data'), 10, 2);
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
		// append variations section to booking block form
			function new_block_form( $EVENT, $block_index){

				if(!$EVENT->check_yn('_evovo_activate')) return false;
				if( empty($block_index)) return false;		

				$have_VOs = false; $buttons = '';

				echo "<div id='evovo_ext_section'>";
				echo "<div class='evovo_vos_container_booking'>";

				$VO = new EVOVO_Var_opts($EVENT, $_POST['wcid']);
				echo $VO->get_all_vos_html($block_index, 'booking',true);
				
				echo "</div>";

				?><p class='evovo_booking_actions' ><?php	echo $VO->get_vos_action_btn_html($block_index, 'booking', true); ?></p><?php

				echo "</div>";


			}

		// save/ update booking block variation
			function save_block_variation($html, $vo_id, $json, $EVENT){
				
				if( !isset($json['parent_type'])) return false;
				if( !isset($json['parent_id'])) return false;
				if( $json['parent_type'] != 'booking') return false;


				// save VO for the booking block
				$booking_index = $json['parent_id'];

				$BLOCKS = new EVOBO_Blocks( $EVENT, $json['wcid']);

				$BLOCKS->save_block_prop($booking_index, 'vo_id', $vo_id);

				$VO = new EVOVO_Var_opts($BLOCKS->event);

				// for variations update block capacity
				if(isset($json['method']) && $json['method'] == 'variation'){
					$VO = new EVOVO_Var_opts($BLOCKS->event, '', 'variation');
					$s = $VO->get_total_stock_for_method($booking_index, 'booking');

					if($s)
						$BLOCKS->save_block_prop($booking_index, 'capacity', $s);
				}

				$html = $VO->get_all_vos_html($booking_index, 'booking',true);
				
				echo json_encode( array(
					'html'=>	$html, 
					'msg'=> 	'New Booking block variation added!',
					'status'=>	'good',
				));exit;

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

}
new EVOVO_BO();
?>