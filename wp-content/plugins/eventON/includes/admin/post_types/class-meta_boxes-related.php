<?php
/**
 * Event Edit Meta box Health Guidance
 * @4.0.3
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
	<span class='evo_btn ajde_popup_trig evo_rel_events' data-popc='print_lightbox' data-lb_cl_nm='evo_related_events_lb' data-t='<?php _e('Configure Related Event Details','eventon');?>' data-eventid='<?php echo $EVENT->ID;?>'><?php _e('Add related event','eventon');?></span>

<?php echo "</div></div>";