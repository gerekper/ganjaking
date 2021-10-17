<?php
/**
 * Booking Blocks and single block for event
 */
class EVOBO_Blocks{
	public $dataset = array(); // all blocks data for the event
	public $item_data = array(); // single block item data
	public $block_id = false;
	public $date_format = 'Y-m-d';
	public $time_format = 'H:i';

	// methods option, variation_type, variation
	public function __construct($EVENT, $wcid=''){

		if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT);
		$this->event = $EVENT;
		$this->event_id = $EVENT->ID;
		$this->wcid = !empty($wcid)? $wcid:'';

		$this->time_format = get_option('time_format');

		// set data
		$this->set_data();	
	}

// RETURNS
	// ALL BLOCKS
		// get capacity of all blocks
		function get_total_block_capacities(){
			$capacity = 0;
			
			if(!empty($this->dataset) && sizeof($this->dataset)>0){
				
				foreach($this->dataset as $index=>$data){
					if(empty($data['capacity'])) continue;
					$capacity += (int)$data['capacity'];
				}
			}
			return $capacity;
		}
		function get_booking_times_for_date($date){
			$times = array();

			if(!$this->dataset) return false;

			foreach($this->dataset as $index=>$data){
				if( $date != date($this->date_format, $data['start']) ) continue;

				$times[ $index ] = array(
					'start'=> date($this->time_format,$data['start']),
					'end'=> date($this->time_format,$data['end']),
					'capacity'=> (int)$data['capacity'],
				); 
			}
			return $times;
		}

		
		// JSON booking data for frontend
		function get_json_booking_slots($show_past = false){
			if(!$this->dataset) return false;


			date_default_timezone_set('UTC');
			$current_time = time();

			$datetime = new evo_datetime();

			$current_time += $datetime->get_UTC_offset();

			$json = array();
			$count = 1;

			$EVO_Cal = new EVO_Calendar('evcal_2');
			$months = $EVO_Cal->get_all_months();
			$days = $EVO_Cal->get_all_days('','three');

			
			foreach($this->dataset as $index=>$data){
				if(empty($data['start'])) continue;
				if(empty($data['end'])) continue;

				//echo ' '.$data['start'].' ';
				//print_r($data);

				// if booking slot is past 
				if( $data['end'] < $current_time && !$show_past) continue;

				// if slot have no capacity skip
				if( !is_admin() && $data['capacity'] == 0) continue;

				$start = date('Y-F-n-j-N', $data['start']);
				$start = explode('-', $start);

				// if start and end dates are different
				if($data['sd'] != $data['ed']){
					
					$date_diff = $data['end'] - $data['start'];
					$date_diff = round($date_diff/ (60*60*24));

					for($dd=1; $dd<= $date_diff; $dd++){

						$this_date_unix = strtotime(date('Y-m-d',$data['start']) ." +{$dd} day" );

						// skip past dates
						if($this_date_unix < $current_time) continue;

						$this_date = date('Y-F-n-j-N',  $this_date_unix);
						$this_date = explode('-',$this_date);

						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ][$count]['data'] = $this_date[3];
						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ][$count]['index'] = $index;
						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ][$count]['times'] = $this->get_formatted_block_times($data['start'], $data['end']);
						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ]['day'] = $days[ $this_date[4] ];
						$json[ $this_date[0] ][ $this_date[2] ]['name'] = $months[ $this_date[2]];
						$count++;
					}

				// block start and end on same date
				}else{
					$json[ $start[0] ][ $start[2] ][ $start[3] ][$count]['index'] = $index;
					$json[ $start[0] ][ $start[2] ][ $start[3] ][$count]['times'] = $this->get_formatted_block_times($data['start'], $data['end']);
					$json[ $start[0] ][ $start[2] ][ $start[3] ]['day'] = $days[ $start[4] ];
					$json[ $start[0] ][ $start[2] ]['name'] = $months[ $start[2]];
					$count++;
				}			

			}

			return json_encode($json);
		}

		// get formatted start and end time for block 
			function get_formatted_block_times($start_, $end_, $include_init_date = false){

				$start = explode('-', date('Y-n-j', $start_));
				$end = explode('-', date('Y-n-j', $end_));

				$output = $front = '';
				$sameDay = false;
				// same year
					if( $start[0] == $end[0]){
						// same month
						if( $start[1] == $end[1]){
							// same date
							if( $start[2] == $end[2]){
								$sameDay = true;
							}
						}
					}

				if($include_init_date && $sameDay){
					$front = date($this->date_format, $start_).' ';
				}

				return ($sameDay) ? 
					$front. date($this->time_format, $start_).' - '. date($this->time_format, $end_):
					date($this->date_format.' '.$this->time_format, $start_).' - '. date($this->date_format.' '.$this->time_format, $end_);

			}

		// return array of all booking dates for this event
		function get_booking_days( $check_past_dates = false){
			if(!$this->dataset) return false;

			date_default_timezone_set('UTC');

			$current_time = time();

			$dates = array();
			foreach($this->dataset as $index=>$data){

				if(!isset($data['start'])) continue;
				if(!isset($data['end'])) continue;

				// if booking slot is past 
				if( $check_past_dates && $data['end'] < $current_time) continue;

				$dates_key = date($this->date_format,$data['start']);
				$this_cap = isset($data['capacity']) ? $data['capacity'] : 0;
				$capacity = isset($dates[$dates_key] )? (int)$dates[$dates_key] + $this_cap:$this_cap;
				$dates[$dates_key] = $capacity;
			}

			return $dates;
		}

		function is_blocks_active(){
			$status = $this->event->get_prop('_evobo_activate');
			if($status == 'yes') return true;
			return false;
		}

		function get_ticket_orders(){

			$wp_arg = array(
				'posts_per_page'=> -1,
				'post_type'=>'evo-tix',
				'meta_query' => array(
					'relation' => 'AND',
					array('key' => 'wcid','value' => $this->wcid,'compare' => '='),
					array('key' => '_eventid','value' => $this->event_id,'compare' => '='),
					array('key' => '_ticket_block_index','compare' => 'EXISTS'),
				)
			);
			
			$ticketItems = new WP_Query($wp_arg);

			$orders = array();

			if($ticketItems->have_posts()):
				while($ticketItems->have_posts()): $ticketItems->the_post();
					$tiid = $ticketItems->post->ID;
					$order_id = get_post_meta($tiid,'_orderid',true);
					$booking_index = get_post_meta($tiid,'_ticket_block_index',true);
					$orders[$booking_index][] = $order_id;
				endwhile;
				wp_reset_postdata();
			endif;

			return $orders;
		}

	// SINGLE BLOCK	
		// need block data set first
		function has_stock(){
			$capacity = $this->get_item_prop('capacity');
			if(!$capacity) return false;
			return (int)$capacity;
		}
		function is_stock_available($block_id, $qty){
			$capacity = $this->get_item_prop('capacity');		
			$capacity = (int)$capacity;
			if($capacity == 0) return false;

			if((int)$qty> $capacity) return false;
			return (int)$capacity;
		}
		function get_block_time_string($block_id=''){
			if(!empty($block_id)) $this->set_block_data($block_id);
			return (date($this->date_format, $this->get_item_prop('start')) == date($this->date_format, $this->get_item_prop('end')))	?
						date($this->date_format, $this->get_item_prop('start')).' '.date($this->time_format, $this->get_item_prop('start')).' - '.date($this->time_format, $this->get_item_prop('end'))	:
						date($this->date_format .' '. $this->time_format, $this->get_item_prop('start')).' - '.date($this->date_format .' '.$this->time_format, $this->get_item_prop('end'));
		}
		function get_start_date(){
			return date('Y-m-d', $this->get_item_prop('start') );
		}

		function get_attendees($block_id){

			$TA = new EVOTX_Attendees();
			$TH = $TA->get_tickets_for_event($this->event_id);

			//print_r($TH);

			$customers = array();

			if(count($TH)>0){
				foreach($TH as $tn=>$td){
					if(!isset($td['oDD']) ) continue;
					if(!isset($td['oDD']['block_index']) ) continue;
					if( $td['oDD']['block_index']!= $block_id) continue;

					$customers[$tn] = $td;
				}
			}			

			return $customers;
		}

		function get_date_time_format(){
			$date_format = $this->get_item_prop('date_format')? $this->get_item_prop('date_format') : get_option('date_format');
			$time_format = $this->get_item_prop('time_format')? $this->get_item_prop('time_format') : get_option('time_format');
			return $date_format . ' ' . $time_format;
		}
		function get_unix_time(){
			if( $this->get_item_prop('start') &&  $this->get_item_prop('end')){
				return array(
					'start'=>$this->get_item_prop('start'),
					'end'=>$this->get_item_prop('end'),				
				);
			}
			
			$data = $this->get_unix_block_time();
			return array(
				'start'=> $data['start'],
				'end'=> $data['end'],
			);
		}
		function get_unix_block_time($args=''){

			if( !empty($args)){
				$sd = $args['sd'];
				$ed = $args['ed'];
				$st = $args['st'];
				$et = $args['et'];
			}else{
				$sd = $this->get_item_prop('sd');
				$ed = $this->get_item_prop('ed');
				$st = $this->get_item_prop('st');
				$et = $this->get_item_prop('et');
			}
			if(empty($sd) && empty($ed)) return false;

			$data = array();

			$time_format = get_option('time_format');
			$_wp_date_format = 'Y/m/d';

			date_default_timezone_set('UTC');		

			$START = date_parse_from_format($_wp_date_format.' '.$time_format, $sd.' '.$st);
			$END = date_parse_from_format($_wp_date_format.' '.$time_format, $ed.' '.$et);


			$data['start'] = mktime($START['hour'], $START['minute'],0, $START['month'], $START['day'], $START['year'] );
			$data['end'] = mktime($END['hour'], $END['minute'],0, $END['month'], $END['day'], $END['year'] );

			return $data;
		}

// ACTIONS
	// for all blocks
		function update_wc_block_stock(){
			$all_blocks = $this->get_total_block_capacities();

			$WC_Product = wc_get_product( $this->wcid);

			if($WC_Product){
				$WC_Product->set_manage_stock(true);
				$WC_Product->set_stock_quantity($all_blocks);
				$WC_Product->save();
			}
		}	
		private function save_dataset($data, $save = true){
			$this->dataset = $data;

			if( $save){
				$this->event->set_prop( '_evobo_data', $data);
			}
		}	

	// Individual block
		function get_item_prop($field){
			if( count($this->item_data) == 0) return false;
			if( !isset($this->item_data[$field])) return false;
			return $this->item_data[$field];
		}
		function reorder_blocks($ORDER){
			$BLOCK_data = $this->dataset;
			foreach($ORDER as $block_index){
				if(!isset($BLOCK_data[$block_index])) continue;
				$new_block_data[$block_index] = $BLOCK_data[$block_index];
			}
			$this->save_dataset($new_block_data);
		}
		
		// save individual block item
		function save_item($block_id, $data, $convert_unix = true){
			
			if(empty($block_id)) return false;
			
			if($convert_unix){
				$times = $this->get_unix_block_time($data);
				if(count($times)>0) $data = array_merge($data, $times);
			}

			$dataset = $this->dataset;
			$dataset[$block_id] = $data;

			// new dataset with new data included along with old data
			$this->save_dataset( $dataset );
			return true;
		}		

		function save_block_prop($block_id, $field, $value){
			$dataset = $this->dataset;

			$dataset[$block_id][$field] = $value;
			$this->save_dataset( $dataset );
			return true;
		}
		function adjust_stock($block_id, $adjustment_type='reduce', $adjust_by_qty=0){
			$this->set_block_data($block_id);

			$capacity = $this->get_item_prop('capacity');

			if( $capacity ){
				$capacity = (int)$capacity;
				
				$newstock = ($adjustment_type=='reduce')? 
					$capacity - (int)$adjust_by_qty: 
					$capacity + (int)$adjust_by_qty;
				$newstock = ($newstock<0)? 0 : $newstock;

				$this->save_block_prop($block_id, 'capacity' , $newstock );
			}
		}

		function delete_item($block_id){		
			$dataset = $this->dataset;

			if(!isset($dataset[$block_id])) return true;
			unset($dataset[$block_id]);

			$this->save_dataset($dataset, true);

			return true;
		}
	

// HTML
	// ADMIN: GET HTML for all the blocks
	function admin_get_all_blocks_html(){
		$_wp_date_format = get_option('date_format');
		$blocks = $this->dataset;

		ob_start();
		if($blocks){							
			
			if(sizeof($blocks)>0 && is_array($blocks)){						

				foreach($blocks as $index=>$data){
					if(empty($index)) continue;
					if(!is_array($data)) continue;

					$data['date_format'] = $_wp_date_format;
					$data['time_format'] = $this->get_time_format();	
					$data['eid'] = $this->event_id;
					$data['wcid'] = $this->wcid;
					echo $this->get_time_based_block_html($data, $index);
				}
			}
		}else{
			echo "<p class='none'>".__('You do not have any booking blocks yet!','eventon')."</p>";
		}

		return ob_get_clean();
	}

	function get_time_based_block_html($args, $index){
		ob_start();
		global $evobo;

		$__woo_currencySYM = get_woocommerce_currency_symbol();

		// Set single block data for object
		$this->set_block_data($index);	
				
		$this->set_timezone();
		$block_time = $this->get_unix_time();

		// common attrs
			$data_attr = '';
			foreach(array(
				'eid'	=>	$this->event_id,
				'wcid'	=>	$this->wcid
			) as $k=>$v){
				$data_attr .= "data-{$k}='{$v}' ";
			}

		// if new generate a random index
			if(!empty($args['type']) && $args['type']=='new'){
				$index = rand(100000, 900000);
			}
		?>
		<li data-cnt="<?php echo $index;?>" class="new">
			<em alt="Edit" class='evobo_block_item edit ajde_popup_trig' data-popc='evobo_lightbox' <?php echo $data_attr;?> data-type='edit'><i class='fa fa-pencil'></i></em>
			<em alt="Delete" class='delete' <?php echo $data_attr;?>>x</em>
	
			<span class='details'>				
				<span class='data'>
					<span class='booking_id'>#<?php echo $index;?></span>
					<span class='time'><?php echo $this->get_formatted_block_times($block_time['start'], $block_time['end'], true);?></span>
					<span class='price'><i><?php _e('Price','eventon');?></i> <?php echo $this->check_data($args, 'price')? 
						$__woo_currencySYM. ($this->check_data($args, 'price')):'';?></span>
					<span class="cap"><i><?php _e('Cap','eventon');?></i> <?php echo $this->check_data($args, 'capacity')? 	($this->check_data($args, 'capacity')): 0;?> </span>

					<?php do_action('evobo_admin_booking_slot_data',$index,$this);?>

				</span>
			</span>
				
			
		</li>
		<?php
		return ob_get_clean();
	}

// SUPPRTIVE
	function check_data($data, $key){
		return !empty($data[$key])? $data[$key]: false;
	}
	function set_timezone(){
		date_default_timezone_set('UTC');
	}
	function get_time_format(){
		$wp_time_format = get_option('time_format');
		return (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
	}

// PRIVATE ACCESS
	private function set_data(){
		$data = $this->event->get_prop('_evobo_data');
		if($data && is_array($data))	$this->dataset = $data;
	}
	public function set_block_data($block_id){
		$dataset = $this->dataset;

		// set block data if they exist
		if( is_array($dataset) && isset( $dataset[$block_id])){
			$this->block_id = $block_id;
			$this->item_data = $dataset[$block_id];
		} 
			

	}
}