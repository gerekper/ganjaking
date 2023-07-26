<?php
/**
 * Event Seats class extension
 * @version 1.2
 */

class EVOST_Seats{
	public $seats_data= false;
	public $seatmap_settings= false;
	public $section = false;
	public $row = false;
	public $seat = false;
	public $item_data = array();
	private $section_data = array();
	public $item_type = '';

	public $custom_id = false; // pass custom id to further separate data
	public $custom_id2 = false; // pass custom id to further separate data
	public $section_key = '_evost_sections';
	public $event, $event_id, $wcid, $ri;

	public function __construct($EVENT, $wcid='', $RI=0, $cid='', $cid2=''){
		if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT, '', $RI);

		$this->event = $EVENT;
		$this->event_id = $this->event->ID;
		if(!empty($wcid)) $this->wcid = $this->event->wcid = $wcid;
		$this->ri = $RI;

		if(!empty($cid)) $this->custom_id = $cid;
		if(!empty($cid2)) $this->custom_id2 = $cid2;

		do_action('evost_construct', $this);

		// set up section key
		$this->load_section_key();

		// set seats data
		$this->set_seats_data();	
	}

	// load section key
	public function load_section_key(){
		$this->section_key = '_evost_sections'. 
			( $this->custom_id ? '_'.$this->custom_id:''). 
			( $this->custom_id2? '_'.$this->custom_id2:'');
	}

	// get seat section data
	function _get_seat_data(){		
		return apply_filters('evost_seat_data', $this->event->get_prop( $this->section_key ) , $this );
	}

	private function set_seats_data(){
		$seats = $this->_get_seat_data();
		if($seats && is_array($seats))	$this->seats_data = $seats;
	}
	function reload_seats_data(){
		$this->set_seats_data();
	}
	function update_from_local_seat_data(){
		if(!$this->seats_data) return false;
		$this->save_seat_map_data( $this->seats_data);
	}
	function get_seats_data(){	return $this->seats_data;}

// INITIAL FUNCTIONS
	// LOCALIZE DATA
		function set_section($id){
			//print_r($this->seats_data);
			if(empty($this->seats_data)) return false;
			if( !isset($this->seats_data[$id])) return false;
			$this->item_data = $this->seats_data[$id];
			$this->item_type = 'section';
			$this->section = $id;
			$this->section_data = $this->seats_data[$id];
			//print_r($this->seats_data);
		}
		function set_row($id, $section_id=''){
			if(empty($section_id)) $section_id = $this->section;
			if(empty($section_id)) return false;
			if(empty($this->seats_data)) return false;
			if( !isset($this->seats_data[$section_id])) return false;
			if( !isset($this->seats_data[$section_id]['rows'])) return false;
			if( !isset($this->seats_data[$section_id]['rows'][$id])) return false;
			$this->item_data = $this->seats_data[$section_id]['rows'][$id];
			$this->item_type = 'row';
			$this->section = $section_id;
			$this->row = $id;

			//print_r($this->item_data);
		}
		function set_seat($id='', $row_id='', $section_id=''){
			if(empty($id) && !$this->seat) return false;
			if(empty($id) && $this->seat) $id = $this->seat; // use local seat

			if(empty($section_id)) $section_id = $this->section;
			if(empty($row_id)) $row_id = $this->row;

			if(empty($this->seats_data)) return false;
			if( !isset($this->seats_data[$section_id])) return false;
			if( !isset($this->seats_data[$section_id]['rows'])) return false;
			if( !isset($this->seats_data[$section_id]['rows'][$row_id])) return false;
			if( !isset($this->seats_data[$section_id]['rows'][$row_id][$id])) return false;
			$this->item_data = $this->seats_data[$section_id]['rows'][$row_id][$id];
			$this->item_type = 'seat';
			$this->section = $section_id;
			$this->row = $row_id;
			$this->seat = $id;
		}

	/// Seat STOCK
		// return all the seats sold and unsold
		public function get_total_seats_capacities($seat_status = 'all'){
			$total = 0;

			if( is_array($this->seats_data)){
				foreach($this->seats_data as $section_id=>$section){

					// skip aoi 
					if( $section['type'] == 'aoi') continue;

					// assign seating
					if( isset($section['rows'])){
						foreach( $section['rows'] as $rowid=>$row){
							foreach($row as $seat_id=> $seat){
								if(!is_array($seat)) continue;
								if( in_array( $seat_id, array('row_index', 'row_price'))) continue;

								// skip other seat status types if status is speficied
								if( $seat_status != 'all' && $seat_status != $seat['status']) continue;
								$total ++;
							}
						}
					// type non seat
					}else{
						if(isset($section['capacity'])){
							$total += (int)$section['capacity'] - ( isset($section['sold']) ? $section['sold']:0);
						} 
					}
					
				}
			}

			return $total;
		}

	// Woocommerce update stock with available seats stock
		function update_wc_block_stock($stock = false){
			if( empty($this->wcid)) return false;
			if( !$stock) $stock = $this->get_total_seats_capacities('av');

			$WC_Product = wc_get_product( $this->wcid);

			if($WC_Product){
				$WC_Product->set_manage_stock(true);
				$WC_Product->set_stock_quantity($stock);
				$WC_Product->save();
			}
		}	

	// Individual Seat functions
		function adjust_stock($type){
			if($type=='reduce'){
				$this->make_uav();
			}else{// restock
				$this->make_available();
			}
		}		
		function make_tuav(){
			$this->update_seat_status('tuav');
		}
		function make_available(){
			$this->update_seat_status('av');
		}
		function make_uav(){
			$this->update_seat_status('uav');						
		}
		private function update_seat_status($status){
			$this->set_seat_prop('status', $status);
		}
		
		// Unassigned seating
			function una_restock_seat($qty){
				$this->una_adjust_sold('reduce',$qty );
			}
		// for section
			function set_section_prop($field, $value){
				$sData = $this->seats_data;
				$sData[$this->section][$field] = $value;
				$this->save_seat_map_data($sData);
			}
		// put nonseat as in progress
			function set_nonseat_inprogress($qty){

			}

		// set UNA new sold value
			function una_adjust_sold($type, $by_qty){
				if(empty($this->section)) return false;

				// get the sold value and make adjustments
				$sold = $this->get_item_prop('sold');
				$cap = $this->get_item_prop('capacity');
				$new_sold = ($type == 'add')? $sold + $by_qty: $sold - $by_qty ;
				if($new_sold> $cap) $new_sold = $cap;
				if($new_sold < 0) $new_sold = 0;
				$this->una_set_prop('sold', $new_sold);
			}

	// getting item
		function get_item_prop($field){
			if(empty($this->item_data)) return false;
			if(!isset($this->item_data[$field])) return false;			
			return $this->item_data[$field];
		}

		// get item property from all seat data as oppose to item data
		function get_item_prop_from($field){
			$sData = $this->seats_data;

			switch($this->item_type){
				case 'section':
					return $sData[$this->section][$field];
				break;
				case 'row':
					return $sData[$this->section]['rows'][$this->row][$field];
				break;
				case 'seat':
					return $sData[$this->section]['rows'][$this->row][$this->seat][$field];
				break;
			}
		}

		// get section data
		function get_section_prop($field){
			$sData = $this->seats_data;
			if(!isset($sData[$this->section][$field])) return false;
			return $sData[$this->section][$field];
		}

		// get seat status by seat slug, return false if no status
		function get_seat_status($seat_slug){
			$seat_data = $this->get_seat_data($seat_slug);

			if($this->seat_type == 'seat'){
				return (isset($seat_data['status']))? $seat_data['status']: false;
			}else{
				return $this->nonseat_get_available_seats($this->seat_type);
			}
		}


		// section releated
		function get_rows(){
			if(empty($this->item_data)) return false;
			if(empty($this->item_type)) return false;
			if($this->item_type != 'section') return false;
			if(!isset($this->item_data['rows'])) return false;
			return count($this->item_data['rows']);
		}

		function get_row_seats(){
			if(empty($this->item_data)) return false;
			if($this->item_type != 'row') return false;
			//print_r($this->item_data);
			return count($this->item_data) - 2;
		}
		
		function get_max_seats(){
			if(empty($this->item_data)) return false;
			if(empty($this->item_type)) return false;
			if($this->item_type == 'seat') return false;

			if( $this->item_type == 'section'){
				if(!isset($this->item_data['rows'])) return false;
				$max_seats = 0;
				foreach($this->item_data['rows'] as $row=>$seats){
					$_seats = count($seats) - 2;
					if( $_seats > $max_seats) $max_seats = $_seats;
				}
			}else{// row
				$max_seats = count($this->item_data)-2;
				
			}			
			return $max_seats;
		}
		function get_next_section_index(){
			if(!$this->seats_data) return 1;

			$max = 1;
			foreach($this->seats_data as $section){
				if( $section['section_index'] > $max) $max = $section['section_index'];
			}
			return $max;
		}

		// get index letter from an ID
			function get_section_letter_by_id($id=''){
				$id = empty($id)? $this->section: $id;
				if(empty($this->seats_data)) return false;
				if(!isset($this->seats_data[$id])) return false;			
				if(!isset($this->seats_data[$id]['section_index'])) return false;			
				return $this->seats_data[$id]['section_index'];
			}
			function get_row_letter_by_id($id=''){
				$_row_id = empty($id)? $this->row: $id;
				if(empty($this->seats_data)) return false;
				
				foreach($this->seats_data as $section_id=>$section){
					if(!isset($section['rows'])) continue;
					foreach($section['rows'] as $row_id=>$rd){
						if(!isset($rd['row_index'])) continue;
						if($row_id == $_row_id) return $rd['row_index'];
					}
				}
				return false;
			}
		function get_new_item_index(){
			return  rand(1000, 9000);
		}
		function get_section_last_row_letter(){
			if(!$this->seats_data) return 'A';
			if($this->item_type != 'section') return 'A';

			if(!isset($this->seats_data[$this->section]) ) return 'A';
			if(!isset($this->seats_data[$this->section]['rows']) ) return 'A';
			if(sizeof($this->seats_data[$this->section]['rows'])<1 ) return 'A';

			$last_row = end( $this->seats_data[$this->section]['rows'] );

			if(!isset($last_row['row_index'])) return 'A';
			return $last_row['row_index'];
		}

		// return all the seats in user cart
		function get_all_seats_in_cart(){
			$cart_seats = array();
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				// skips
				if(empty($values['evost_data'])) continue;
				if(empty($values['evost_data']['seat_slug']) ) continue;

				$cart_seats[ $values['evost_data']['seat_slug'] ] = $cart_item_key;
			}
			return $cart_seats;
		}
	
	// DELETE
			function delete_item(){
				$sData = $this->seats_data;
				switch($this->item_type){
					case 'section':
						unset($sData[$this->section]);
					break;
					case 'row':
						unset($sData[$this->section]['rows'][$this->row]);
					break;
					case 'seat':
						unset($sData[$this->section]['rows'][$this->row][$this->seat]);
					break;
				}
				$this->save_seat_map_data($sData);
			}
		
	// PRIVATE 
		private function set_seat_prop($field, $value){
			$allSeats = $this->seats_data;
			$allSeats[$this->section]['rows'][$this->row][$this->seat][$field] = $value;
			$this->save_seat_map_data($allSeats);//s ave the new values
		}
		private function una_set_prop($field, $value){
			$sData = $this->seats_data;
			if(!$this->section) return false;
			$sData[$this->section][$field] = $value;
			$this->save_seat_map_data($sData);
		}
		public function save_item_data($item_data){
			$sData = $this->seats_data;
			switch($this->item_type){
				case 'section':
					$sData[$this->section] = $item_data;
				break;
				case 'row':
					$sData[$this->section]['rows'][$this->row] = $item_data;
				break;
				case 'seat':
					$sData[$this->section]['rows'][$this->row][$this->seat] = $item_data;
				break;
			}
			$this->save_seat_map_data($sData);
		}

		// adjust seat status direct onto event post meta and update local values after
		// Only for assigned seating
		private function hard_set_seat_status($new_status){
			if( $this->seat_type != 'seat' ) return false;

			$seats = get_post_meta($this->event->ID,'_evost_sections',true);
			
			$seats[$this->section]['rows'][$this->row][$this->seat]['status'] = $new_status;
				
			// update the 
			update_post_meta($this->event->ID, '_evost_sections',$seats);

			$this->seats_data = get_post_meta($this->event->ID,'_evost_sections',true);

			// localize the new data
			$this->set_seat($this->seat);
		}

		function save_seat_map_data($data){			
			$this->event->set_prop('_evost_sections',$data);
			$this->event->globalize_event_pmv();
			$this->seats_data = $data;
		}

// CHECK
	// check if seats activated
		public function is_seats_active(){
			$s = $this->event->check_yn('_enable_seat_chart');
			return ($s)? true: false;
		}

// SETTINGS
	function get_seat_settings(){
		$settings = $this->event->get_prop('_evost_settings');
		if($settings && is_array($settings)){

			// get background image url
			if(isset($settings['_evost_seat_bg_img_id'])){
				$settings['bg_url'] = wp_get_attachment_url( $settings['_evost_seat_bg_img_id'] );
			}

			return $this->seatmap_settings = $settings;
		}	
		return false;
	}
	function load_seatmap_settings(){
		$settings = $this->event->get_prop('_evost_settings');
		if($settings && is_array($settings))	$this->seatmap_settings = $settings;		
	}
	function get_seatmap_settings_prop($field){
		if(!$this->seatmap_settings) return false;
		if(!isset( $this->seatmap_settings[$field])) return false;
		return $this->seatmap_settings[$field];
	}
	
	function set_settings($settings){
		$this->event->set_prop('_evost_settings',$settings);
	}

// SUPPORTIVE
	public function process_seatmap_data_for_save($data){

		$new_data = $data;
		
		foreach($data as $section_id=>$section){
			if( isset($section['rows']) && sizeof($section['rows'])>0){
				
				foreach($section['rows'] as $row_id=>$row){
					foreach($row as $seat_id=>$seat){

						if( $seat_id == 'seats'){
							foreach($seat as $other_seat_id=>$other_seat_data){
								$new_data[$section_id]['rows'][ $row_id ][$other_seat_id]= $other_seat_data;
							}
							unset($new_data[$section_id]['rows'][ $row_id ]['seats']);
						}
					}
				}
			}
		}

		return $new_data;

	}
	function get_seat_data($seat_slug){
		$this->_get_seat_type_by_slug($seat_slug);
		
		if($this->seat_type == 'seat'){
			$this->process_seat_slug($seat_slug);
			if(isset($this->seats_data[$this->section]['rows'][$this->row][$this->seat] ))
				return $this->seats_data[$this->section]['rows'][$this->row][$this->seat];
		}else{
			$this->set_section( $seat_slug);
			if(isset($this->seats_data[$this->section] ))
				return $this->seats_data[$this->section];
		}
		
		return false;
	}
	// Localize seat slug, process seat slug based on seat type and set local values including seat type
		function _localize_seat_slug($slug){
			$this->_get_seat_type_by_slug($slug);

			if( $this->seat_type == 'seat'){
				$this->process_seat_slug();
			// unassigned seating & booth seating
			}else{
				$this->set_section($this->seat_slug);
			}
			return $this->seat_type;
		}
	// get seat type @u 1.2
		function _get_seat_type_by_slug($slug){
			$this->seat_slug = $slug;

			$seat_type = $this->seat_type = EVOST()->frontend->get_seat_type( $slug );

			return $seat_type;
		}

	// process seat id and return section row and seat index
		function process_seat_slug(){
			if($this->seat_type != 'seat') return false;
			$data = explode('-', $this->seat_slug);

			$this->section = $data[0];
			$this->row = $data[1];
			$this->seat = $data[2];

			// set the seat for class
			$this->set_seat($this->seat);
			return array(
				'section'=>$data[0],
				'row'=> $data[1],
				'seat'=> $data[2],
			);
		}
// All section 
	// update all the seats
		function update_all_seats($FF, $VV){
			$sections = $this->seats_data;
			if(!is_array($sections)) return false;

			foreach($sections as $section_id=>$section){

				$def_price = 0;

				// for assigned seating section
				if( isset($section['rows']) && sizeof($section['rows'])>0){
					foreach($section['rows'] as $row_id=>$row){
						foreach($row as $seat_id=>$seat){
							
							if( !isset($seat[ $FF])) continue;
							$sections[$section_id]['rows'][$row_id][$seat_id][$FF] = $VV;
						}				

					}
				}

				// for unassigned seating section
				// reset sold count to 0 and make it available
				if( $section['type'] == 'una' && $FF == 'status' && $VV == 'av'){
					$sections[$section_id]['sold']=0;
				}
			}

			$this->seats_data = $sections;
			$this->save_seat_map_data( $sections );

		}

	// get tickets by seat section
		function get_tickets_by_section(){
			$EA = new EVOTX_Attendees();
			$json = $EA->get_tickets_for_event($this->event_id);

			$return = array();

			foreach($json as $ticket_number => $td){
				if( !isset($td['oDD'] ) ) continue;
				if( !isset($td['oDD']['seat_slug'] ) ) continue;
				$section_id = explode('-', $td['oDD']['seat_slug'] );

				$return[ $section_id[0] ][$ticket_number] = $td; 
			}

			return $return;
		}

		function get_ticket_for_section($_section_id){
			$EA = new EVOTX_Attendees();
			$json = $EA->get_tickets_for_event($this->event_id);

			$return = array();

			foreach($json as $ticket_number => $td){
				if( !isset($td['oDD'] ) ) continue;
				if( !isset($td['oDD']['seat_slug'] ) ) continue;
				$section_id = explode('-', $td['oDD']['seat_slug'] );

				if( $section_id[0] != $_section_id) continue;

				$return[$ticket_number] = $td; 
			}

			return $return;
		}

// NON-seat Functions
	function get_nonseat_capacity($type){
		if( $type == 'boo') return 1;

		if( $type == 'una'){
			if(!$this->section) return false;
			return $this->get_item_prop('capacity');
		}
	}
	function nonseat_get_available_seats( $type){

		if( $type == 'seat') return;

		if(!$this->section) return false;

		// fetch current sold ticket data
		$tickets = $this->get_ticket_for_section( $this->section );
		$sold = 0;
		$inprogress = 0;

		foreach( $tickets  as $TN=>$TD){
			if( $TD['oS'] == 'completed') $sold++;
			if( $TD['oS'] != 'completed') $inprogress++;
		}
		
		$cap = $this->get_item_prop('capacity');

		return $cap - $sold - $inprogress;
	}

// VIEWS
	function get_frontend_seats_view($event_id, $wcid){

		$OPT = EVOST()->opt;
		// accordion view on mobile
			$dis_accrd = evo_settings_check_yn($OPT, 'evost_seat_accordion')? true: false;

		ob_start();
		
		?>
		<div class='evost_seat_selection'>
					
			<style type="text/css" class='evost_seat_map_styles'></style>
			<div class='evost_seat_layout_outter'>
				<div class='evost_seat_layout'></div>
			</div>	
			<div class='evost_tooltip'></div>		
		
			<div class='evost_map_information'>

				<div class="evost_seat_legends">	
					<span class='legends_trig'><?php evo_lang_e('Seat Legends');?>
						<span class='evost_seat_legends_box'>		
							<?php /*<span class='av'><b></b> <?php evo_lang_e('Available');?></span>*/?>
							<span class='uav'><b></b> <?php evo_lang_e('Unavailable (Sold Out)');?></span>
							<span class='tuav'><b></b> <?php evo_lang_e("In someone's cart");?></span>
							<span class='selected'><b></b> <?php evo_lang_e('Your selected seats');?></span>
							<span class='mine'><b></b> <?php evo_lang_e('Seats in your cart');?></span>
							<span class='res'><b></b> <?php evo_lang_e('Reserved');?></span>
							<span class='hand'><b></b> <?php evo_lang_e('Handicap Accessible');?></span>
						</span>
					</span>	
				</div>
				<div class='evost_view_control'>
					<span class='fit'><?php evo_lang_e('Reset Map');?></span>
					<span class='zoomin'>+</span>
					<span class='zoomout'>-</span>
					<?php /*<input type="range" class='zoom-range' step="0.05" min='0.3' max="6"/>
					<button class="reset">Reset</button>*/?>
				</div>
			</div>
			<div class="evost_seats_footer">
				<?php
					// seat expiration time
					$expiration = !empty($OPT['evost_session_time'])?	$OPT['evost_session_time']  : false;
					if($expiration):
						$string = str_replace('[time]', $expiration, evo_lang('Seats added to cart will expire in [time] minutes of inactivity in cart.'));
				?>
				<p style='margin-top:15px;border-top:1px solid #dadada;padding-top:10px'><?php echo $string;?></p>
			<?php endif;?>
			</div>
			<?php 
				$data = array(
					'currency'=>get_woocommerce_currency_symbol(),
					'event_id'=> $event_id,
					'wcid'=> $wcid,
					'accord'=> $dis_accrd
				);

			?>
			<div class='evost_data' data-s='<?php echo json_encode($data);?>'></div>
		</div>
		<?php
		return ob_get_clean();
	}

	
// RETURN HTML
// @depre
	function _html_get_sections($is_admin = true, $one_section=''){
		$sections = $this->seats_data;

		if(!is_array($sections)) return false;

		$debug = false;

		//$wc_cart_seats = (!$is_admin) ?$this->get_cart_with_seats( $product_id, $sections): array();
		$wc_cart_seats= (!$is_admin) ?$this->get_cart_seats( $this->wcid, $sections): array();
		$EXP = new EVOST_Expirations($this->event->ID);


		$SYM = get_woocommerce_currency_symbol();

		ob_start();


		foreach($sections as $section_id=>$section){

			// skipping
			if(!empty($one_section) && $section_id != $one_section) continue;

			$this->set_section($section_id);

			$section_index = isset($section['section_index'])? $section['section_index']: $section_id;

			$ang = !empty($section['ang'])? $section['ang']:0;

			$section_class_names = array('evost_section','turn'.$ang);
			$section_class_names[] = $is_admin?'admin':'fnt';
			$section_class_names[] = ($is_admin && $one_section == $section_id)?'editing':'';

			if( $this->item_type=='row') $section_class_names[] = 'rowedit';
			if( $this->item_type=='seat') $section_class_names[] = 'seatedit';

			if($is_admin) $section_class_names[] = 'editable';

			// background color
				$bgc = isset($section['bgc'])? '#'.$section['bgc']:'';

			?>
			<span id='evost_section_<?php echo $section_id;?>' class="<?php echo implode(' ', $section_class_names);?>" data-id='<?php echo $section_id;?>' data-ang='<?php echo $ang;?>' data-index='<?php echo $section['section_index'];?>'  data-name='<?php echo $section['section_name'];?>' style='top:<?php echo $section['top'];?>px;left:<?php echo $section['left'];?>px; background-color:<?php echo $bgc;?>' title='<?php echo $section['section_name'];?>' >
			
				
				<u><?php echo $section['section_name'];?></u>
				
				<?php	

				// EACH ROW
				foreach($section['rows'] as $row_id =>$row):	
					$this->set_row( $row_id);
				?>
				
				<span class="evost_row" data-id='<?php echo $row_id;?>' data-index='<?php echo $row['row_index'];?>' data-name='<?php echo $row['row_index'];?>'>
					
					<?php

						$seat_count = 1;

						// EACH SEAT
						foreach($row as $seat_id=>$seat):

							if(in_array($seat_id, array('row_index','row_price'))) continue;
							
							$this->set_seat($seat_id);

							$seatid = $section['section_index'].'-'.$row_id.'-'.$seat_id;
							$seat_status = (!empty($wc_cart_seats['seatids']) && in_array($seatid, $wc_cart_seats['seatids']))? 'selected'.' '.$seat['status']:$seat['status'];
							
							// all temp unavailable seats
								if($seat['status'] == 'tuav' ){

									$status = '';
									
									// if seat is temporarily unavailable check seat for expiration time 
									if(isset($wc_cart_seats[$seatid])){
										$status = $EXP->run_seat_expiration_check($wc_cart_seats[$seatid], $this);
									}

									if($status == 'removed'){
										$this->update_seat_status( 'av');
										$seat_status = 'av';
									}
								}

							// Seat Number
								$seat_number = $section['section_index'].$row['row_index']. $seat_count;
								if(isset($seat['number'])) $seat_number = $seat['number'];


							// if its handicap compatible seat
							
							// seat json
								$json = array();
								$json['id'] = $seat_id;
								$json['number'] = $seat_number;
								$json['section_index'] = $section['section_index'];
								$json['section_id'] = $section_id;
								$json['row_index'] = $row['row_index'];
								$json['row_id'] = $row_id;
								$json['price'] = $seat['price'];

							?>
							<span data-id='<?php echo $seat_id;?>' class='seat <?php echo $seat_status;?>' data-j='<?php echo json_encode($json);?>'>
								<span class='data'>
									<span><?php evo_lang_e('Sec');?> <?php echo $section['section_index'];?>, <?php evo_lang_e('Row');?> <?php echo $row['row_index'];?>, <?php evo_lang_e('Seat');?> <?php echo $seat_number;?></span>
									<?php if($seat_status=='av' || $seat_status =='selected'):?>
									<span class='price'>
										<span><?php echo_lang_e('Ticket Price');?></span> 
										<span><?php echo $SYM . $seat['price'];?></span>
									</span>
									<?php endif;?>
								</span>
							</span>
							<?php $seat_count++; 

						endforeach;?>
				</span>
				<?php endforeach;?>						
									
			</span>
			<?php

		}

		return ob_get_clean();
	}

}