<?php 
/** 
 * Post Meta Boxes
 */

class EVOAD_meta_boxes{
	public function __construct(){
		add_action( 'add_meta_boxes', array($this, 'meta_boxes') );
		add_action( 'eventon_event_metafields', array($this, 'save_meta_data'), 10 , 2 );
	}

	function meta_boxes(){
		add_meta_box('evoad_mb1',__('Advent Event','eventon'), array($this, 'metabox_content'),'ajde_events', 'normal', 'high');
	}

	function metabox_content(){

		global $post, $ajde;
		
		// initial			
		$EVENT = new EVO_Event($post->ID);

		$advent_active = $EVENT->check_yn('_evo_advent_event')?'yes':null;

		//$EVENT->del_prop('_evo_advent_event_data');

		?>
		<style type="text/css">
			#EVOAD_dates textarea{width: 100%}
		</style>
		<div class='eventon_mb'>
		<div class="evoad evotx">
			<p class='yesno_leg_line ' style='padding:10px'>
				<?php echo eventon_html_yesnobtn(array('var'=>$advent_active, 'attr'=>array('afterstatement'=>'EVOAD_details'))); ?>
				<input type='hidden' name='_evo_advent_event' value="<?php echo ($advent_active=='yes')?'yes':'no';?>"/>
				<label for='_evo_advent_event'><?php _e('Enable Advent event for this event')?></label>
			</p>
			<div id='EVOAD_details' class='EVOAD_details evomb_body ' <?php echo ( $advent_active =='yes')? null:'style="display:none"'; ?>>
					
				<div id='EVOAD_dates'>
					<p style='padding-bottom:20px;'><?php _e('NOTE: Make sure to set the event date to correctly match within your advent events month date range. Set which event card fields to be hidden for future advent calendar dates from','evoad'); 
					echo " <a href='". get_admin_url('','admin.php?page=eventon&tab=evcal_ad')."'>";_e('advent settings.','evoad');?></a>

					<?php echo " <a target='_blank' href='https://docs.myeventon.com/documentations/how-to-install-and-use-the-advent-calendar-addon/'>". __('Learn how to setup Advent Events','evoad') ."</a>";?>
					</p>
										
					<?php

					echo EVO()->elements->get_element( array(
						'id'=> '_evoad_msg_onday',
						'type'=>'textarea',
						'name'=> __('On date content to be revealed','evoad'),
						'tooltip'=> __('HTML or text content that will be revealed in eventcard on the date only. When current date is = event set date above.'),
						'value'=> $EVENT->get_prop('_evoad_msg_onday')
					));

					echo EVO()->elements->get_element( array(
						'id'=> '_evoad_past_msg',
						'type'=>'textarea',
						'name'=> __('Content to show when event is past','evoad'),
						'tooltip'=> __('Text or HTML content to show on the eventcard when the event date is past current END date.'),
						'value'=> $EVENT->get_prop('_evoad_past_msg')
					));

					?>

				</div>
			</div>
		</div>
		</div>
		<?php

	}

	function save_meta_data($array, $post_id){
		$array[] = '_evo_advent_event';
		$array[] = '_evoad_hide_after';
		$array[] = '_evoad_past_msg';
		$array[] = '_evoad_msg_onday';
		return $array;		
	}

}
new EVOAD_meta_boxes();