<?php
/**
 * Global EventON templates
 */

class EVO_Temp{
// return the template HTML
function get($type){

	ob_start();
	switch($type){
		case has_action("evo_temp_{$type}"):
			do_action("evo_temp_{$type}");	
		break;
		case 'event_top':
			$structure = new EVO_Cal_Event_Structure();
			echo $structure->_event_top_template();
		break;
		case 'event_card':
			$structure = new EVO_Cal_Event_Structure();
			echo $structure->_event_card_template();
		break;
	}

	return ob_get_clean();
}

// @+ 2.8 beta
function get_init(){
	return apply_filters('evo_init_templates',array(
		//'eventtop'=> $this->get('event_top'),
		//'eventcard'=> $this->get('event_card'),
	)); 
}
}