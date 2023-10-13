<?php
/**
 * General Integration with other eventON addons and EventON
 * @version 1.2.2
 */

class EVOST_Integration{
	public function __construct(){

		add_filter('evodp_event_edit_enable_dp',array($this, 'event_edit_dp'),10,2);
	}

	function event_edit_dp($boolean, $EVENT){
		if($EVENT->check_yn('_enable_seat_chart')) return false;

		return $boolean;
	}
}

new EVOST_Integration();