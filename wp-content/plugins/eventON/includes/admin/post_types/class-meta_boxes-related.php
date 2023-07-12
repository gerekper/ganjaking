<?php
/**
 * Event Edit Meta box Related Events
 * @4.4.1
 */


$related_events = $EVENT->get_prop('ev_releated');


echo "<div class='evcal_data_block_style1'>
<div class='evcal_db_data evo_rel_events_box'>
	<input type='hidden' class='evo_rel_events_sel_list' name='ev_releated' value='". ($related_events )."' />";

	if($EVENT->is_repeating_event()){
		echo "<p>".__('NOTE: You can not select a repeat instance of this event as related event.','eventon').'</p>';
	}
	?>
	<span class='ev_rel_events_list'><?php
		if($related_events){
			$D = json_decode($related_events, true);

			$rel_events = array();

			foreach($D as $I=>$N){
				$id = explode('-', $I);
				$EE = new EVO_Event($id[0]);
				$x = isset($id[1])? $id[1]:'0';
				$time = $EE->get_formatted_smart_time($x);
				
				$rel_events[ $I.'.'. $EE->get_start_time() ] =  "<span class='l' data-id='{$I}'><span class='t'>{$time}</span><span class='n'>{$N}</span><i>X</i></span>";
			}

			//krsort($rel_events);

			foreach($rel_events as $html){
				echo $html;
			}
			
		}
	?></span>

	<?php
		$btn_data = array(
			'lbvals'=> array(
				'lbc'=>'evo_related_events_lb',
				't'=>__('Configure Related Event Details','eventon'),
				'ajax'=>'yes',
				'd'=> array(					
					'eventid'=> $EVENT->ID,
					'action'=> 'eventon_rel_event_list',
					'EVs'=> $related_events,
					'uid'=>'evo_get_related_events',
				)
			)
		);
	?>
	<span class='evo_btn evolb_trigger' <?php echo $this->helper->array_to_html_data($btn_data);?> ><?php _e('Add related event','eventon');?></span>

	<?php 
	// option to hide related event images
	echo EVO()->elements->get_element(array(
		'type'=>'yesno',
		'id'=>'_evo_relevs_hide_img',
		'value'=> $EVENT->get_prop('_evo_relevs_hide_img'),
		'name'=> __('Hide related event image','eventon'),
		'tooltip'=> __('This will show related events without the event image.','eventon')
	));
	?>

<?php echo "</div></div>";