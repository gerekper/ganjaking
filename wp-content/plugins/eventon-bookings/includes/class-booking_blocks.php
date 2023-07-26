<?php
/**
 * Booking Blocks and single block for event
 * @version 1.4
 */
class EVOBO_Blocks{
	public $dataset = array(); // all blocks data for the event
	public $item_data = array(); // single block item data
	public $block_id = false;
	public $date_format = 'Y-m-d';
	public $time_format = 'H:i';
	public $is_admin = false;
	public $event, $event_id, $wcid, $DD, $timezone0, $current_time;

	// methods option, variation_type, variation
	public function __construct($EVENT, $wcid=''){

		if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT);
		$this->event = $EVENT;
		$this->event_id = $EVENT->ID;
		$this->wcid = !empty($wcid)? $wcid:'';

		$this->time_format = get_option('time_format');

		$this->is_admin = is_admin();

		// set data
		$this->set_data();	

		$this->DD = new DateTime();
		$this->timezone0 = new DateTimeZone( 'UTC' );
		$this->current_time = current_time('timestamp');
		$this->DD->setTimezone( $this->timezone0 );
		$this->DD->setTimestamp( $this->current_time );
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

		public function get_total_block_count(){
			$count = 0;

			if(!empty($this->dataset) && sizeof($this->dataset)>0){				
				return count( $this->dataset );
			}
			return $count;
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


		public function get_frontend_block_json($show_past = false, $encode = true, $attendees = false){
			return $this->get_json_booking_slots($show_past, $encode, $attendees, true);	
		}
		public function get_backend_block_json($show_past = false, $encode = true, $attendees = false){
			return $this->get_json_booking_slots($show_past, $encode, $attendees, false);	
		}

		
		// JSON booking data for frontend and backend
		function get_json_booking_slots($show_past = false, $encode = true, $attendees = false, $is_front = true){
			if(!$this->dataset) return false;


			date_default_timezone_set('UTC');
			$current_time = time();

			$datetime = new evo_datetime();

			$current_time += $datetime->get_UTC_offset();

			$json = array();
			$count = 1;
			
			//$months = $EVO_Cal->get_all_months();
			//$days = $EVO_Cal->get_all_days('','three');
			$months = EVO()->cal->_get_all_month_names();
			$days = EVO()->cal->get_all_day_names('three');
			$_CUR = get_woocommerce_currency_symbol();

			$all_attendees = ($attendees) ? $this->get_all_event_attendees():false;

			foreach($this->dataset as $index=>$data){

				if(empty($data['start'])) continue;
				if(empty($data['end'])) continue;
				$AT = '';

				// if booking slot is past 
				if( $data['end'] < $current_time && !$show_past) continue;

				// if slot have no capacity skip
				if( $is_front && $data['capacity'] == 0) continue;

				if($all_attendees) $AT = $this->get_attendees_for_block($all_attendees, $index);

				$start = date('Y-F-n-j-w', $data['start']);
				$start = explode('-', $start);

				$end = date('Y-F-n-j-w', $data['end']);
				$end = explode('-', $end);				

				// if start and end dates are different
				if($start[2] != $end[2] && $start[1] != $end[1] && $start[0] != $end[0] ){
					
					$date_diff = $data['end'] - $data['start'];
					$date_diff = round($date_diff/ (60*60*24));

					for($dd=1; $dd<= $date_diff; $dd++){

						$this_date_unix = strtotime(date('Y-m-d',$data['start']) ." +{$dd} day" );

						// skip past dates
						if($this_date_unix < $current_time) continue;

						$this_date = date('Y-F-n-j-N',  $this_date_unix);
						$this_date = explode('-',$this_date);

						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ][$count] = apply_filters('evobo_blocks_json',
								array(
									'data'=>$this_date[3],
									'c'=>$data['capacity'],
									'index'=> $index,
									'p'=>	$_CUR. $this->_convert_str_to_cur($data['price']),
									'times'=> $this->get_formatted_block_times($data['start'], $data['end']),
						), $index, $this);

						if($attendees && !empty($AT)) 
							$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ][$count]['a'] = $AT;

						$json[ $this_date[0] ][ $this_date[2] ][ $this_date[3] ]['day'] = $days[ $this_date[4] ];
						$json[ $this_date[0] ][ $this_date[2] ]['name'] = $months[ $this_date[2]];
						$count++;
					}

				// block start and end on same date
				}else{
					$json[ $start[0] ][ $start[2] ][ $start[3] ][$count] = apply_filters('evobo_blocks_json',
						array(
							'c'=>$data['capacity'],
							'index'=> $index,
							'p'=>	$_CUR. $this->_convert_str_to_cur($data['price']),
							'times'=> $this->get_formatted_block_times($data['start'], $data['end']),
					), $index, $this);

					if($attendees && !empty($AT)) 
						$json[ $start[0] ][ $start[2] ][ $start[3] ][$count]['a'] = $AT;

					$json[ $start[0] ][ $start[2] ][ $start[3] ]['day'] = isset($days[ $start[4] ]) ? $days[ $start[4] ] :'';
					$json[ $start[0] ][ $start[2] ]['name'] = $months[ $start[2]];
					$count++;
				}	

			}

			return $encode? json_encode($json) : $json;
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
			function get_booking_days( $check_past_dates = false, $times = false){
				if(!$this->dataset) return false;

				date_default_timezone_set('UTC');

				$current_time = time();

				$dates = array();
				foreach($this->dataset as $index=>$data){

					if(!isset($data['start'])) continue;
					if(!isset($data['end'])) continue;

					// if booking slot is past 
					if( $check_past_dates && $data['end'] < $current_time) continue;

					$dates_key = date($this->date_format . ($times? ' '.$this->time_format:'') ,$data['start']);
					$this_cap = isset($data['capacity']) ? $data['capacity'] : 0;
					$capacity = isset($dates[$dates_key] )? (int)$dates[$dates_key] + $this_cap:$this_cap;
					$dates[$dates_key] = $capacity;
				}

				return $dates;
			}

			// return block id and its dates
			public function get_all_block_dates($check_past_dates = false,$times = false){
				if(!$this->dataset) return false;

				date_default_timezone_set('UTC');

				$current_time = time();

				$dates = array();
				foreach($this->dataset as $index=>$data){

					if(!isset($data['start'])) continue;
					if(!isset($data['end'])) continue;

					// if booking slot is past 
					if( $check_past_dates && $data['end'] < $current_time) continue;

					$block_time = date($this->date_format . ($times? ' '.$this->time_format:'') ,$data['start']);
					$dates[$index] = $block_time;
				}

				return $dates;
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

		// return blocks are good to go
		function is_blocks_active(){
			$status = $this->event->get_prop('_evobo_activate');
			if($status == 'yes') return true;
			return false;
		}

		// checks if booking blocks enabled and check if booking data is there
		function is_blocks_ready(){
			if(! $this->is_blocks_active()) return false;

			if( !$this->dataset) return false;

			if( !is_array($this->dataset) ) return false;
			if( sizeof( $this->dataset ) <= 0 ) return false;

			return $this->dataset;

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
		function get_block_time_string($block_id='', $type = 'both'){
			if(!empty($block_id)) $this->set_block_data($block_id);

			if($type == 'start'){
				return date($this->date_format.' '.$this->time_format, $this->get_item_prop('start'));
			}
			
			return (date($this->date_format, $this->get_item_prop('start')) == date($this->date_format, $this->get_item_prop('end')))	?
						date($this->date_format, $this->get_item_prop('start')).' '.date($this->time_format, $this->get_item_prop('start')).' - '.date($this->time_format, $this->get_item_prop('end'))	:
						date($this->date_format .' '. $this->time_format, $this->get_item_prop('start')).' - '.date($this->date_format .' '.$this->time_format, $this->get_item_prop('end'));
		}
		public function get_block_duration($block_id=''){	
			if(!empty($block_id)) $this->set_block_data($block_id);

			$dur = $this->get_item_prop('end') - $this->get_item_prop('start');

			$help = new evo_helper();
			return $help->get_human_time( $dur);
			
		}
		function get_start_date(){
			return date('Y-m-d', $this->get_item_prop('start') );
		}

		// return booking blocks stock quantity in cart
		function get_blocks_in_cart(){
			$blocks_in_cart = 0;
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if( !isset($cart_item['evobo_index'])) continue;
				if( !isset($cart_item['evotx_event_id_wc'])) continue;
				
				if( $cart_item['evotx_event_id_wc'] == $this->event_id && $cart_item['evobo_index'] == $this->block_id){
					$blocks_in_cart += $cart_item['quantity'];						
				}
			}

			return $blocks_in_cart;
		}

	// Attendees	
		function get_all_event_attendees(){
			$slots = $this->get_frontend_block_json(true, false);

			$TA = new EVOTX_Attendees();
			$TH = $TA->get_tickets_for_event($this->event_id);

			return count($TH)>0? $TH : false;
		}
		function get_attendees_for_block($attendees, $BID){
			if(count($attendees) == 0) return array();

			$customers = array();

			foreach($attendees as $tn=>$td){
				if(!isset($td['oDD']) ) continue;
				if(!isset($td['oDD']['block_index']) ) continue;
				if( $td['oDD']['block_index']!= $BID) continue;

				$customers[$tn] = $td;
			}
			return $customers;
		}
		function get_attendees($block_id){

			$ATS = $this->get_all_event_attendees();
			if(!$ATS) return $ATS;

			return $this->get_attendees_for_block( $ATS, $block_id);
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

		function _admin_get_unix_from_post($post){
			$P = $post;

			if(!isset( $P['event_start_date_x'])) return false;

			// start
			$_h = $this->_get_hour($P['_start_hour'], isset($P['_start_ampm'])? $P['_start_ampm']:'' );
			$str = $P['event_start_date_x'].' '.$_h.":".$P['_start_minute'].':00';

			$this->DD = new DateTime($str );
			$this->DD->setTimezone( $this->timezone0 );

			$_S = $this->DD->format('U');

			// end
			$_h = $this->_get_hour($P['_end_hour'], isset($P['_end_ampm'])? $P['_end_ampm']:'' );
			$str = $P['event_end_date_x'].' '.$_h.":".$P['_end_minute'].':00';

			$this->DD = new DateTime($str );
			$this->DD->setTimezone( $this->timezone0 );

			$_E = $this->DD->format('U');

			$R = array(	'start'=> $_S, 'end'=>$_E	);
			return $R;
		}	
		// return hour in 24 format
			function _get_hour($h, $ampm=''){
				if(!empty($ampm) && $ampm == 'pm' && $h <12) return ((int)$h) +12;
				return $h;
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
		function update_wc_block_stock($stock = ''){
			$all_blocks_count = empty($stock)? $this->get_total_block_capacities() : $stock;

			$WC_Product = wc_get_product( $this->wcid);

			if($WC_Product){
				$WC_Product->set_manage_stock(true);
				$WC_Product->set_stock_quantity($all_blocks_count);
				$WC_Product->save();
			}

			return $all_blocks_count;
		}	

		public function fast_set_wc_stock($stock){
			update_post_meta($this->wcid, '_stock', $stock);
		}


		function save_dataset($data, $save = true){
			$this->dataset = $data;

			if( $save){
				$this->event->set_prop( '_evobo_data', $data);
			}
		}	

		function delete_all_dataset(){
			$this->dataset = array();
			$this->event->set_prop( '_evobo_data', array());
		}

	// Individual block
		public function get_next_block_id(){
			
		}
		// @since 1.4
		function get_block_prop($block_id, $field){
			$dataset = $this->dataset;

			// set block data if they exist
			if( is_array($dataset) && isset( $dataset[$block_id]) && isset( $dataset[$block_id][$field] ) ){
				return $dataset[$block_id][$field];
			}
			return false;
		}
		function get_item_prop($field){
			if( count($this->item_data) == 0) return false;
			if( !isset($this->item_data[$field])) return false;
			return $this->item_data[$field];
		}
		function get_item_price(){
			$price = $this->get_item_prop('price');
			if(!$price) return false;

			return $this->_convert_str_to_cur( $price);
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
				$times = $this->_admin_get_unix_from_post($data);
				if(!empty($times) && is_array($times) && count($times)>0){
					if(isset( $times['start'] )) $data['start'] = $times['start'];
					if(isset( $times['end'] )) $data['end'] = $times['end'];
				} 
			}

			// unset unnecessary fields
			foreach( array(
				'event_start_date_x','event_end_date_x','_start_hour','_start_minute','_start_ampm',
				'_end_hour','_end_minute','_end_ampm', 'eid','wcid'
			) as $F){
				unset($data[$F]);
			}

			if(!is_array($data) || empty($data)) return false;

			$dataset = $this->dataset;
			$dataset[$block_id] = $data;

			// new dataset with new data included along with old data
			$this->save_dataset( $dataset );
			return true;
		}		

		function save_block_prop($block_id, $field, $value){
			$dataset = $this->dataset;

			if( !isset($dataset[$block_id])) return false;

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
	function _convert_str_to_cur($V){
		$v = floatval(preg_replace("/[^0-9.]/", '', $V));
		return number_format($v,2);
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