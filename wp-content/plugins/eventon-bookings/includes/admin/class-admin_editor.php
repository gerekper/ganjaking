<?php
/**
 * Admin booking block editor & manager
 */

class EVOBO_Admin_Editor{

	public function __construct(){
		$ajax_events = array(
			'evobo_load_editor'=>'editor',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

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
			'dfj'=>'yy/mm/dd'
		);

		ob_start();

		?>
		<div class='evobo_editor' style='padding:20px;display:flex;'>
			<div class='evoboE_calendar' style='min-width:300px'>
				<h3><?php _e('Booking Slots','evobo');?> <a class="evo_admin_btn btn_triad evobo_add_new_slot"><?php _e('Add New','evobo');?></a></h3>
				<div class='evoboE_slots'></div>
			</div>
			
			<div class='evoboE_form_container'></div>
			
			<div class='evobo_admin_data' data-json='<?php echo $BLOCKS->get_json_booking_slots(true);?>' data-orders='' data-dataset='<?php echo json_encode($dataset);?>'></div>

		</div>
		<?php

		$content =  ob_get_clean();
		echo json_encode(array(
			'content'=> $content,
			'status'=>'good'
		)); exit;
	}
}
new EVOBO_Admin_Editor();