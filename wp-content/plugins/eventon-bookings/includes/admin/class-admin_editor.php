<?php
/**
 * Admin booking block editor & manager
 * @version 1.3.3
 */

class EVOBO_Admin_Editor{
	public $HELP, $postdata;
	public function __construct(){
		$ajax_events = array(
			'evobo_load_editor'=>'editor',
			'evobo_get_form'=>'get_form',
			'evobo_load_generator'=>'generator_form',
			'evobo_generate_slots'=>'generate_slots',
			'evobo_delete_all'=>'delete_all',
			'evobo_delete_block'=>'evobo_delete_block',
			'evobo_save_booking_block'=>'evobo_save_booking_block',		
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->HELP = $this->help = new evo_helper();
		$this->postdata = $this->help->process_post( $_POST);

	}

	// time slot generator
		function generator_form(){
			
			$event_id = (int)$_POST['eid'];
			$wcid = (int)$_POST['wcid'];
			$BLOCKS = new EVOBO_Blocks($event_id, $wcid);		

			$__woo_currencySYM = get_woocommerce_currency_symbol();
			$regular_price = get_post_meta($wcid, '_regular_price',true);

			ob_start();

			$generator_id = 'G123456';
			$rand_id = rand(100000, 900000);

			?>
			<div class='evobo_form' style='padding:20px;'>
				<form class='evobo_generator_form'>
				<?php 
				EVO()->elements->print_hidden_inputs( array(
					'eid'=> $event_id,
					'wcid'=> $wcid,
					'index'	=> $generator_id,
					'action'=> 'evobo_generate_slots'
				));
				?>
				<h3 style="padding-bottom: 10px"><?php _e('Auto Generate Booking Time Blocks','evobo');?></h3>
				<?php  EVO()->elements->_print_date_picker_values(); ?>
				<p>
					<label><?php _e('Select Date Range to create block');?></label>
					<span class='evobo_auto_date_selector' style="">

					<?php 

					$EST = $BLOCKS->event->get_prop('evcal_srow');
					$EET = $BLOCKS->event->get_prop('evcal_erow');

					EVO()->elements->print_date_time_selector(array(
						'minute_increment'=> 1,					
						'date_format'=> EVO()->calendar->date_format,					
						'type'=>'start',
						'unix'=>$EST,
						'rand'=>$rand_id,
						'selector'=>'date',
						'assoc'=>'evobo_gen'
					));
					EVO()->elements->print_date_time_selector(array(
						'minute_increment'=> 1,					
						'date_format'=> EVO()->calendar->date_format,					
						'type'=>'end',
						'unix'=>$EET,
						'rand'=>$rand_id,
						'selector'=>'date',
						'assoc'=>'evobo_gen'
					));				
					?>
					</span>
				</p>

				<p>
					<label><?php _e('Select Time Range for each day');?></label>
					<span class='evobo_auto_time_selector' style="">
					<?php
					EVO()->elements->print_date_time_selector(array(
						'disable_date_editing'=> false,
						'minute_increment'=> 1,					
						'date_format'=> EVO()->calendar->date_format,					
						'type'=>'start',
						'unix'=>$EST,
						'rand'=>$rand_id,
						'selector'=>'time'
					));
					EVO()->elements->print_date_time_selector(array(
						'disable_date_editing'=> false,
						'minute_increment'=> 1,					
						'date_format'=> EVO()->calendar->date_format,					
						'type'=>'end',
						'unix'=>$EET,
						'rand'=>$rand_id,
						'selector'=>'time'
					));
					?>
					</span>
				</p>
								
				<p><em><?php _e('NOTE: You can delete unwanted time blocks after the generator creates them.');?></em></p>
				<p>
					<label><?php _e('Duration of each time block ');?></label>
					<select name='hr'>
						<?php for($x=0; $x<=12; $x++){
							echo "<option value='{$x}'>{$x} H</option>";
						}?>
					</select>
					<select name='min'>
						<?php for($x=0; $x<=60; $x++){
							echo "<option ".($x == 30? 'selected="selected"':'')."value='{$x}'>{$x} M</option>";
						}?>
					</select>
				</p>
				<p>
					<label><?php _e('Cost of each time block ');?> (<?php echo $__woo_currencySYM;?>)</label>
					<input type="text" name='price' value='<?php echo $regular_price;?>' placeholder="<?php _e('Price');?>"/>
				</p>
				<p>
					<label><?php _e('Capacity of each time block ');?></label>
					<input type="text" name='capacity' value='1' placeholder="<?php _e('Capacity');?>"/>
				</p>			

				<?php 	do_action('evobo_auto_generator_form', $BLOCKS, $generator_id);	?>

				<p><?php 
				// save changes
					EVO()->elements->print_trigger_element(array(
						'title'=>__('Generate Blocks','evobo'),
						'uid'=>'evobo_generate_blocks',
						'lb_class' =>'evobo_generator',
						'lb_loader'=>true,
						'lb_hide'=> 2000,
					), 'trig_form_submit');
				?></p>
			</form>
			</div>
			<?php

			$content =  ob_get_clean();
			echo json_encode(array(
				'content'=> $content,
				'status'=>'good'
			)); exit;
		}
		function generate_slots(){
			$P = array();

			$processed_post = $this->HELP->recursive_sanitize_array_fields( $_POST);

			// process all post variables
			foreach($processed_post as $key=>$val){
				if(in_array($key, array('action','index','type'))) continue;
				if( !is_array($val)) $val = urldecode($val);
				$P[$key] = $val;
			}

			$BLOCKS = new EVOBO_Blocks( (int)$P['eid'], (int)$P['wcid']);

			$se = $BLOCKS->_admin_get_unix_from_post( $P );
			extract($se);
		
			// calculate gap in seconds
			$gap = (isset($P['hr'])? ((int)$P['hr'])*60 :0 ) + (int)$P['min'];
			$gap = $gap *60;

			$existing_blocks = $BLOCKS->dataset;

			$unique_index = rand(100000, 900000);

			$end_hour = $BLOCKS->_get_hour($P['_end_hour'], isset($P['_end_ampm'])? $P['_end_ampm']:'' );
			$end_minute = isset($P['_end_minute'])? $P['_end_minute']: 00;

			$end_seconds = ($end_hour * 3600 ) + ( $end_minute * 60 ); 

			$BLOCKS->DD->setTimestamp( $start );
			$BLOCKS->DD->setTimezone( $BLOCKS->timezone0 );
			$U = $BLOCKS->DD->format('U');

			$generated_slots = array();


			$x = 1;

			// for each date
				while( $U <= $end){
					
					$start_hour = $BLOCKS->DD->format('H');
					$start_min = $BLOCKS->DD->format('i');

					$start_seconds = ($start_hour * 3600 ) + ( $start_min * 60 ); 

					// for each gap
					while( $start_seconds <= $end_seconds ){
						$U = $BLOCKS->DD->format('U');

						if( ($start_seconds + $gap ) > $end_seconds  ) break;
										
						// make sure to not over ride existing blocks with same time
						$skip = false;
						foreach( $existing_blocks as $B=>$V){
							if( $V['start'] == $U && $V['end'] == ($U+$gap)) $skip = true;
						}
						if(!$skip){
							$existing_blocks[ $unique_index + $x ] = array(
								'start'=> $U,
								'end'=> ($U + $gap ),
								'price'=> $BLOCKS->_convert_str_to_cur($P['price']),
								'capacity'=> (!isset($P['capacity'])? 1: $P['capacity']),
							);	

							$generated_slots[] = $unique_index + $x ;

							do_action('evobo_auto_generator_slot', $unique_index + $x, $existing_blocks, $BLOCKS, $P, $x);			
						}

						// increment for next round
						$start_seconds += $gap;
						$BLOCKS->DD->modify('+'. ($gap/60) .'minutes');
						$x++;
					}


					$BLOCKS->DD->modify('+1 day');	
					$BLOCKS->DD->setTime($start_hour, $start_min,0);
					$U = $BLOCKS->DD->format('U');			
				}


			$BLOCKS->save_dataset( $existing_blocks );	

			do_action('evobo_autogen_after_saved', $generated_slots, $BLOCKS, $P);

			$BLOCKS->update_wc_block_stock();		
			echo json_encode(array(
				'json'=>	json_decode($BLOCKS->get_backend_block_json(true)), 
				'status'=>	'good',
				'msg'=>	 __('Successfully generated blocks!','evobo')
			)); exit;
		}

	// SAVE Functions		
		function evobo_save_booking_block(){			

			$post = array();	
			if(!isset($_POST['eid'])){ echo json_encode(array('status'=>'bad'));exit;}

			$processed_post = $this->postdata;

			// process all post variables
				foreach($processed_post as $key=>$val){
					if(in_array($key, array('action','index','type'))) continue;
					if( !is_array($val)) $val = urldecode($val);

					$post[$key] = $val;
				}

			$index = !empty($processed_post['index']) ? $processed_post['index'] :rand(100000, 900999);

			// save new or update this block
			$BLOCKS = new EVOBO_Blocks( $post['eid'], $post['wcid']);

			unset($post['event_start_date']);
			unset($post['event_end_date']);

			// capacity value fix
			if( !isset( $post['capacity'] )) $post['capacity'] = '0';
			
			// Save the booking block data
			$result = $BLOCKS->save_item($index, apply_filters('evobo_save_booking_block_data',$post, $index, $BLOCKS) );

			do_action('evobo_after_save_block', $index, $BLOCKS, $post);

			// update the new all available block capacity and return value
			//$all_blocks_count = $BLOCKS->update_wc_block_stock( );
			
			echo json_encode(array(
				'json'=>	json_decode($BLOCKS->get_backend_block_json(true, true, true)), 
				'status'=>	'good',
				'msg'=>	($processed_post['type'] == 'edit'? __('Successfully editted item!','evobo'): __('Successfully Added New Item','evobo') )
			)); exit;
		}
	
	// DELETE
		// delete block
			function evobo_delete_block(){
				$post = $this->HELP->recursive_sanitize_array_fields( $_POST);

				$BLOCKS = new EVOBO_Blocks($post['eid'], $post['wcid']);
				
				$result = $BLOCKS->delete_item( $post['index'] );
				$BLOCKS->update_wc_block_stock( );

				do_action('evobo_delete_single_blocks', $post['index'], $BLOCKS);
				
				echo json_encode(array(
					'json'=>	json_decode($BLOCKS->get_backend_block_json(true,true, true)), 
					'status'=>	'good',
					'msg'=>	__('Successfully Deleted Block','eventon')
				)); exit;

			}
		// delete all slots
			public function delete_all(){
				$post = $this->postdata;

				$event_id = (int)$post['eid'];
				$wcid = (int)$post['wcid'];

				$BLOCKS = new EVOBO_Blocks($event_id, $wcid);

				$BLOCKS->delete_all_dataset();

				do_action('evobo_delete_all_blocks', $BLOCKS);

				echo json_encode(array(
					'json'=>	json_decode($BLOCKS->get_backend_block_json(true,true, true)), 
					'status'=>	'good',
					'msg'=>	__('Successfully Deleted All Blocks','eventon')
				)); exit;
			}

	
	// Main editor
		function editor(){
			$event_id = (int)$_POST['eid'];
			$wcid = (int)$_POST['wcid'];

			$BLOCKS = new EVOBO_Blocks($event_id, $wcid);

			if(!$BLOCKS->is_blocks_active()){
				$content = "<p style='padding:20px;text-align:center'>".__('Booking not activated. Save event and try again!','evobo') . "</p>";
				echo json_encode(array( 'content'=>$content,'status'=>'good')); exit;
			}

			$date_format = 'Y/m/d';
			$wp_time_format = get_option('time_format');
			$time_format = (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
		
			$dataset = array(
				'eid'=>$event_id,
				'wcid'=>$wcid,
				'tf'=>$time_format,
				'df'=>$date_format,
				'dfj'=>'yy/mm/dd',
				't'=> array(
					'left'=> __('Left','evobo'),
					'attendees'=> __('Attendees','evobo'),
				)
			);

			ob_start();

			?>
			<div class='evobo_editor evomart20' style='display:flex;flex-direction: column;'>			
				<div class='evobo_BE_body'>
					<div class='evoboE_slots'></div>
				</div>
				<div class='evobo_BE_foot'>
					<a class="evo_admin_btn evobo_add_new_slot " ><?php _e('Add New','evobo');?></a>
					<?php
					EVO()->elements->print_trigger_element(array(
						'title'=>__('Generate Blocks','evobo'),
						'uid'=>'evobo_generate_slots',
						'lb_class' =>'evobo_generator',
						'lb_title'=>__('Block Generator','evobo'),	
						'ajax_data'=>array('a'=>'evobo_load_generator','eid'=>$event_id, 'wcid'=>$wcid),
					),'trig_lb');
					?>
					<a class="evo_admin_btn btn_triad evobo_slot_delete_all " data-t='<?php _e('Delete all time blocks','evobo');?>?'><?php _e('Delete All Blocks','evobo');?></a>

					<?php 

					do_action('evobo_block_manager_btns', $BLOCKS);

					?>
				</div>
				<div class='evoboE_form_container'></div>
				
				<div class='evobo_admin_data' data-json='<?php echo $BLOCKS->get_backend_block_json(true);?>' data-orders='' data-dataset='<?php echo json_encode($dataset);?>'></div>
			</div>
			<?php

			$content =  ob_get_clean();
			echo json_encode(array(
				'block_json'=> $BLOCKS->get_backend_block_json(true, false, true),
				'content'=> $content,
				'status'=>'good'
			)); exit;
		}

	// Block Form
		function get_form(){
			
			$post = $this->postdata;
			extract($post);

			ob_start();

			$date_format = 'Y/m/d';		
			$__woo_currencySYM = get_woocommerce_currency_symbol();
			$event_id 	= (int)$post['eid'];
			$wc_id 		= (int)$post['wcid'];
			$EVENT = new EVO_Event( $event_id );
			$_edit_slot = $type == 'edit'? true:false;
					
			// data for edit form	
				$values = array();
				if($_edit_slot && !empty($post['index'])){	
					$BLOCKS = new EVOBO_Blocks($EVENT, $wc_id);			
					$BLOCKS->set_block_data( $post['index']);
					$values = $BLOCKS->item_data;
				}

			// date time data
				$DT = EVO()->elements->_get_date_picker_data();
				extract($DT);

			// get start and end time of event
				$event_start = $EVENT->get_start_unix();
				$event_end = $EVENT->get_end_unix();
		
			// if unix sent
				$dates_sent = false;
				if(!empty($values['start']) && !empty($values['end'])){				
					//date_default_timezone_set($tzstring);
					$dates_sent = true;
				}

			// block ID
				$rand_id = rand(100000,999990);
				$block_index = !empty($post['index'])? $post['index']:$rand_id;
				

			?>
			<div class="evobo_add_block_form" style='padding:20px;'>
				<form class='evobo_block_editor_form'>				
				<?php 
				EVO()->elements->print_hidden_inputs( array(
					'eid'=> $eid,
					'wcid'=> $wcid,
					'type'	=> $type,
					'index'	=> $block_index,
					'action'=> 'evobo_save_booking_block'
				));


				echo EVO()->elements->get_element(array(
					'type'=>'notice','row_class'=>'evopadb10',
					'name'=>__("Booking Block ID") .': <b>'. $block_index .'</b>'
				));
				
				EVO()->elements->_print_date_picker_values(); 

				?>
				
				<div class='evobo_fields'>
					<p>
						<span><?php _e('Block Start','evobo');?>: *</span>				
						<?php 
						EVO()->elements->print_date_time_selector(array(
							'disable_date_editing'=> false,
							'time_format'=> $time_format,
							'date_format'=>$date_format,
							'date_format_x'=>$date_format,
							'unix'=> ($dates_sent? $values['start']: $event_start),				
							'type'=>'start',
							'assoc'=>'reg',
							'names'=>true,
							'rand'=> $rand_id
						));				
						?>
						<span><?php _e('Block End','evobo');?>: *</span>
						<?php 
						EVO()->elements->print_date_time_selector(array(
							'disable_date_editing'=> false,
							'time_format'=> $time_format,
							'date_format'=>$date_format,
							'date_format_x'=>$date_format,
							'unix'=> ($dates_sent? $values['end']: $event_start),					
							'type'=>'end',
							'assoc'=>'reg',
							'names'=>true,
							'rand'=> $rand_id
						));				
						?>
					</p>
			
					<div class='evobo_pricing'>
						<p><b><?php _e('Other Block Data','evobo');?></b></p>
						<?php 

							$regular_price = get_post_meta($wc_id, '_regular_price',true);
							$manage_stock = get_post_meta($event_id, '_manage_stock',true);
							$stock = get_post_meta($wc_id, '_stock',true);
							$capacity = ($manage_stock && $manage_stock =='yes' && !empty($stock))? $stock:0;
						?>
						<p>
							<label><?php _e('Block Price','evobo');?>: * (<?php echo $__woo_currencySYM;?>) <em style='opacity: 0.5'><?php _e('Default Price:','evobo');?> <?php echo $__woo_currencySYM.$regular_price;?></em></label>
							<input name='price' type="text" value='<?php echo isset($values['price'])? $BLOCKS->_convert_str_to_cur($values['price']): $regular_price;?>'/>					
						</p>
						<p>
							<label><?php _e('Block Capacity','evobo');?>: *</label>
							<input name='capacity' type="text" value='<?php echo isset($values['capacity'])? $values['capacity']:$capacity;?>'>
						</p>
					</div>
					
					<?php do_action('evobo_new_block_form', $EVENT, $block_index, $post);?>		
				</div>			

				<?php
				// attendee details
					if($_edit_slot){
						$this->_print_attendee( $BLOCKS, $block_index);
					}

				?>
				<p><?php
					// save changes
					EVO()->elements->print_trigger_element(array(
						'class_attr'=> 'evo_btn evolb_trigger_save',
						'title'=>__('Save Changes','evobo'),
						'uid'=>'evobo_save_block',
						'lb_class' =>'evobo_editor',
						'lb_loader'=>true,
						'lb_hide'=> 2000,
					), 'trig_form_submit');

					if( $type != 'new'):
						// delete button
						EVO()->elements->print_trigger_element(array(
							'class_attr'=> 'evo_admin_btn btn_triad evo_trigger_ajax_run',
							'title'=>__('Delete','evobo'),
							'uid'=>'evobo_delete_block',
							'lb_class' =>'evobo_editor',
							'lb_loader'=>true,
							'lb_hide'=> 2000,
							'ajax_data'=>array(
								'a'=>'evobo_delete_block',
								'eid'=> $eid, 'wcid'=> $wcid, 'index'=> $block_index
							),
						), 'trig_ajax');
					endif;
				?></p>

			</form>
			</div>
			<?php
			
			// print json
			echo json_encode(array(
				'content'=> ob_get_clean(),
				'status'=>'good'
			)); exit;
		}

	// get attendees HTML
		function _print_attendee($B, $BI){
			$block_cap = $B->has_stock();
				$block_cap = $block_cap>0? $block_cap:0;
			$customers = $B->get_attendees($BI);

			$TA = new EVOTX_Attendees();

			echo "<div class='evobo_admin_attendees_section'>";

			?><h3 style='padding-bottom: 10px;'><?php _e('Attendee Information','evobo');?></h3><?php	

			if($customers){		

				$purchased_slots = 0;	
				$purchased_slots = count($customers);
			
				$w = ($purchased_slots>0 && $block_cap>0)? ($purchased_slots/ ($purchased_slots+ $block_cap))*100: 0;
				$w = $w>0? (int)$w: 0;

				echo "<span class='evoboE_top_data'>
					<span class='evoboED_top'><em><b>{$purchased_slots}</b> ".__('sold','evobo')."</em> <em>".__('Remaining','evobo')." <b>{$block_cap}</b></em></span>
					<span class='evoboED_bar'><em style='width:{$w}%'></em></span>
				</span>";

				echo "<span class='evobo_customers'>";
				foreach($customers as $tn=>$td){

					echo $TA->__display_one_ticket_data($tn, $td, array(
						'showExtra'=>false,
						'showOrderStatus'=>true,
						'showStatus'=>true
					));				
				}	
				echo "</span>";	
				
			}else{

				echo "<span class='evoboE_top_data'>
					<span class='evoboED_top'><em><b>0</b> ".__('sold','evobo')."</em> <em>".__('Remaining','evobo')." <b>{$block_cap}</b></em></span>
					<span class='evoboED_bar'></span>
				</span>";

				echo "<p>". __('No Attendees Found','evobo') . "</p>";
			}
			echo "</div>";
		}


// SUPPORT
	function get_time_format(){
		$wp_time_format = get_option('time_format');
		return (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
	}

}
new EVOBO_Admin_Editor();