<?php 
/**
 * Admin Ajax
 * @version 0.1
 */
class evobo_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'evobo_get_form'=>'get_form',
			'evobo_save_booking_block'=>'evobo_save_booking_block',
			'evobo_save_to_event'=>'evobo_save_to_event',
			'evobo_delete_block'=>'evobo_delete_block',
			'evobo_arrange_block'=>'evobo_arrange_block',

			'evobo_get_attendees'=>'get_attendees',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}
//view attendees
	function get_attendees(){

		$event_id = (int)$_POST['eid'];
		$wcid = (int)$_POST['wcid'];
		$block_index = (int)$_POST['index'];

		$BLOCKS = new EVOBO_Blocks( $event_id,  $_POST['wcid']);
		$TA = new EVOTX_Attendees();

		$BLOCKS->set_block_data($block_index);
		$block_cap = $BLOCKS->has_stock();
			$block_cap = $block_cap>0? $block_cap:0;

		$customers = $BLOCKS->get_attendees($block_index); // get attendees for this block

		ob_start();
		echo "<div class='evobo_admin_attendees_section'>";
		echo "<a class='evoboE_hide_form evo_admin_btn btn_triad' style='margin-bottom:20px'>". __('Close','evobo') . "</a>";
		

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

		echo json_encode(array(
			'content'=>	ob_get_clean(), 
			'status'=>	'good',
			'bi'=>$block_index
		)); exit;

	}

// GET FORM
	function get_form(){

		if( !isset($_POST['eid'])) return false;

		ob_start();

		$date_format = 'Y/m/d';		
		$__woo_currencySYM = get_woocommerce_currency_symbol();
		$event_id = (int)$_POST['eid'];
		$EVENT = new EVO_Event( $event_id );
				
		// data for edit form
			$values = array();
			if($_POST['type'] == 'edit' && !empty($_POST['index'])){	
				$BLOCKS = new EVOBO_Blocks($EVENT, $_POST['wcid']);			
				$BLOCKS->set_block_data( $_POST['index']);
				$values = $BLOCKS->item_data;
			}

			$time_format = $this->get_time_format();

		// get start and end time of event
			$event_start = $EVENT->get_start_unix();
			$event_end = $EVENT->get_end_unix();

		$block_index = (!empty($_POST['index'])? $_POST['index']:'');

		// if unix sent
			$dates_sent = false;
			if(!empty($values['start']) && !empty($values['end'])){				
				//date_default_timezone_set($tzstring);
				$dates_sent = true;
			}
		?>
		<div class="evobo_add_block_form" >	
			<?php /*<p class='information'><span><?php _e('Event Start','evobo');?>:</span> <b class='event_times'><?php echo date($date_format.' '.$time_format,$event_start);?></b></p>		
			<p class='information'><span><?php _e('Event End','evobo');?>:</span> <b class='event_times'><?php echo date($date_format.' '.$time_format,$event_end);?></b></p>	
			<p><i><?php _e('NOTE: When creating booking block times be sure to select times that will be within this event duration!','eventon');?></i></p>	
			*/?>		
			
			<h3><?php _e('Booking Slot Information','evobo');?></h3>
			<p>
				<span><?php _e('Block Start','evobo');?>: *</span>
				<input type="text" name='sd' value='<?php echo $dates_sent? date($date_format, $values['start']):'';?>'/>
				<input type="hidden" name='sd_'/>

				<input class='evobo_time_selection' type="text" name='st' value='<?php echo $dates_sent? date( $time_format, $values['start']):'';?>'/>
				<br/>
				<span><?php _e('Block End','evobo');?>: *</span>
				<input type="text" name='ed' value='<?php echo $dates_sent? date($date_format, $values['end']):'';?>'/>
				<input type="hidden" name='ed_'/>
				<input class='evobo_time_selection' type="text" name='et' value='<?php echo $dates_sent? date( $time_format, $values['end']):'';?>'/>
			</p>
	
			<div class='evobo_pricing'>
				<p><b><?php _e('Other Block Data','evobo');?></b></p>
				<?php 

					$regular_price = get_post_meta($_POST['wcid'], '_regular_price',true);
					$manage_stock = get_post_meta($_POST['eid'], '_manage_stock',true);
					$stock = get_post_meta($_POST['wcid'], '_stock',true);
					$capacity = ($manage_stock && $manage_stock =='yes' && !empty($stock))? $stock:0;

				?>
				<p>
					<span><?php _e('Block Price','evobo');?>: * (<?php echo $__woo_currencySYM;?>)</span>
					<input name='price' type="text" value='<?php echo !empty($values['price'])? $values['price']: $regular_price;?>'>
					<em><?php _e('Regular Price:','evobo');?> <?php echo $__woo_currencySYM.$regular_price;?></em>
				</p>
				<p>
					<span><?php _e('Block Capacity','evobo');?>: *</span>
					<input name='capacity' type="text" value='<?php echo !empty($values['capacity'])? $values['capacity']:$capacity;?>'>
				</p>
			</div>
			
			<?php do_action('evobo_new_block_form', $EVENT, $block_index);?>
			
			
			<?php
				$attrs = '';
				foreach(array(
					'data-type'=>$_POST['type'],
					'data-index'=> (!empty($_POST['index'])? $_POST['index']:''),
				) as $key=>$val){
					$attrs .= $key .'="'. $val .'" ';
				}
			?>
			<p><a class='evobo_form_submission evo_btn' <?php echo $attrs;?>><?php echo $_POST['type']=='new'? 'Add New':'Save';?></a> <a class='evo_admin_btn btn_triad evobo_cancel_form'><?php _e('Cancel','evobo');?></a></p>
		</div>
		<?php
		$content =  ob_get_clean();
		echo json_encode(array(
			'content'=> $content,
			'status'=>'good'
		)); exit;
	}

// Save block information
	function evobo_save_booking_block(){
		$post = array();	
		if(!isset($_POST['eid'])){ echo json_encode(array('status'=>'bad'));exit;}

		// process all post variables
		foreach($_POST as $key=>$val){
			if(in_array($key, array('action','index','type'))) continue;
			$post[$key] = urldecode($val);
		}

		// save new or update this block
		$index = rand(100000, 900000);
		if(!empty($_POST['index'])) $index = $_POST['index'] ;

		$BLOCKS = new EVOBO_Blocks( $_POST['eid'], $_POST['wcid']);

		do_action('evobo_before_save_block');
		
		$result = $BLOCKS->save_item($index, $post);

		$BLOCKS->update_wc_block_stock( );
		
		echo json_encode(array(
			'json'=>	json_decode($BLOCKS->get_json_booking_slots(true)), 
			'status'=>	'good',
			'msg'=>	($_POST['type'] == 'edit'? __('Successfully editted item!','evobo'): __('Successfully Added New Item','evobo') )
		)); exit;
	}

// delete block
	function evobo_delete_block(){
		$BLOCKS = new EVOBO_Blocks($_POST['eid'], $_POST['wcid']);
		
		$result = $BLOCKS->delete_item( $_POST['index'] );
		$BLOCKS->update_wc_block_stock( );
		
		echo json_encode(array(
			'json'=>	json_decode($BLOCKS->get_json_booking_slots(true)), 
			'status'=>	'good',
			'msg'=>	__('Successfully Deleted Block','eventon')
		)); exit;

	}

// Arrange blocks
	function evobo_arrange_block(){

		$ORDER = $_POST['index'];

		$BLOCKS = new EVOBO_Blocks($_POST['eid'], $_POST['wcid']);			

		if($ORDER && is_array($ORDER) && isset($_POST['eid']) && isset($_POST['wcid'])){			
			$BLOCKS->reorder_blocks($ORDER);
		}

		echo json_encode(array( 
			'json'=> json_decode($BLOCKS->get_json_booking_slots(true)), 
			'status'=>	'good',
			'msg'=>	__('Successfully Updated Blocks','eventon')
		)); exit;
	}

// SUPPROTIVE
	function get_time_format(){
		$wp_time_format = get_option('time_format');
		return (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
	}

}
new evobo_admin_ajax();