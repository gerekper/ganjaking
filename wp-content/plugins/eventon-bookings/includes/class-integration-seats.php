<?php
/** 
 * Integration with event seats addon
 * still working progress
 */

class EVOBO_Seats_Int{
	public function __construct(){

		// seat data modification
		add_filter('evost_construct', array($this, 'seat_construct'), 10, 1);

		// ajax
		add_action('evost_save_map_editor_aftersave', array($this, 'save_full_map_editor'), 10, 1);

		if( is_admin()){
			add_action('evost_mapeditor_before', array($this, 'editor'), 10, 1);
			add_action('evost_admin_formfields', array($this, 'form'), 10, 3);
		}

		$ajax_events = array(
			'evobo_apply_seats'=>'apply_seats',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}


// AJAX
	// when full map editor is saved
	public function save_full_map_editor($SEATS){

		$bost_data = $SEATS->event->get_prop('_evobost');

		if(!$bost_data) return;
		if(!isset($_POST['block_id']) ) return;

		$block_id = (int)$_POST['block_id'];

		$BLOCKS = new EVOBO_Blocks( $SEATS->event, $SEATS->wcid );

		// get total map stock - available seats
		$stock = $this->save_seat_for_blocks($BLOCKS, $SEATS, $block_id);
	}


	public function apply_seats(){

		$HELP = new evo_helper();
		$PP = $HELP->process_post( $_POST);
		$BLOCKS = new EVOBO_Blocks( $PP['data']['eid'], $PP['data']['wcid']);

		$block_data = $BLOCKS->dataset;
		if(count($block_data) == 0){
			echo json_encode(array('content'=> 'There are no booking blocks','status'=>'good')); exit;
		}

		$SEATS = new EVOST_Seats($BLOCKS->event, $BLOCKS->wcid);

		if( !is_array($SEATS->seats_data) ){
			echo json_encode(array('content'=> 'Seats must be created first','status'=>'good')); exit;
		}

		$bost_data = $this->save_seat_for_blocks( $BLOCKS, $SEATS);

		echo json_encode(array(
			'd'=> $bost_data,
			'msg'=>'Block data applied to seats successfully!'
		));exit;		
	}

	

	// save seat data for blocks - this will update block stock to match seat available stock
	// it will also update wc with total stock
	function save_seat_for_blocks($BLOCKS, $SEATS, $block_id = ''){
		$block_data = $BLOCKS->dataset;

		$bost_data = $this->get_all_block_seats_data( $BLOCKS );

		$bost_data = is_array($bost_data) && count($bost_data)> 0 ? $bost_data: array();

		$total_stock = 0;

		// run each block
		foreach($block_data as $bid=>$bd){

			$stock_per_block = 0;

			// if seat data already exists use that or use most recent saved seat data from _evost_sections
			$seat_data = ( isset($bost_data[$bid]) && count($bost_data[$bid])>0) ? $bost_data[$bid]: $SEATS->seats_data;


			// each seat section
			foreach($seat_data as $section_id=>$section){
				$bost_data[ $bid ][$section_id] = $section; 

				if( $section['type'] == 'aoi') continue;
				
				// assign seating
				if( isset($section['rows'])){
					foreach( $section['rows'] as $rowid=>$row){
						foreach($row as $seat_id=> $seat){
							if( in_array( $seat_id, array('row_index', 'row_price'))) continue;
							
							$stock = ($seat['status'] == 'av') ? 1:0;
							$stock_per_block += $stock;
						}
					}
				// type una
				}else{
					$stock_per_block += (int)$section['capacity'] - ( isset($section['sold']) ? $section['sold']:0);
				}
			}

			// update block stock to match to available seats in the map
			$BLOCKS->save_block_prop($bid, 'capacity' , $stock_per_block );

			$total_stock += $stock_per_block;
		}

		$BLOCKS->fast_set_wc_stock($total_stock);

		// save seat price, stock for blocks
		$BLOCKS->event->set_prop('_evobost', $bost_data);

		return $bost_data;
	}


// General 
	public function get_all_block_seats_data($BLOCKS){
		return $BLOCKS->event->get_prop('_evobost');
	}

	public function seat_construct($SEATS){
		if( !$SEATS->event->get_prop('_evobost') ) return;

	}


// ADMIN
	public function editor($SEATS){
		$BLOCKS = new EVOBO_Blocks( $SEATS->event);
		if( !$BLOCKS->is_blocks_active()) return false;

		// if apply blocks to seat not enabled
		if( !$SEATS->event->get_prop('_evobost')) return false;

		$block_dates  = $BLOCKS->get_all_block_dates(false, true);

		?>
		<div class="evosteditor_booking_header" data-j='' style='background-color: #52b4e4;color: #fff; padding: 10px;margin: 0;'>
			<span>Select Booking Block <select class='evobost_block_id evost_trig_new_map' data-type='blocks' name='_evost_block'>
				<option value='def'>Default</option>
				<?php foreach($block_dates as $id=>$dt){
					echo "<option value='{$id}'>". $dt. "</option>";
				}?>
				</select>
			</span>
		</div>

		<?php
		
	}
	public function form($key, $form_data, $SEATS){
		// do not show vos for new form without section ID
		if(!isset($form_data['section_id'])) return false;

		$BLOCKS = new EVOBO_Blocks( $SEATS->event);

		if( !$BLOCKS->is_blocks_active()) return false;

		$block_data = $BLOCKS->is_blocks_ready();
		if(!$block_data) return false;			
			
	}
}

// this is still in working progress
//new EVOBO_Seats_Int();