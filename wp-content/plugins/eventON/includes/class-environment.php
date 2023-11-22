<?php
/** 
 * Eventon Environment Setting
 * @version 2.8.9
 */

class EVO_Environment{
	// TIME zones
	function get_UTC_offset(){
		$offset = (get_option('gmt_offset', 0) * 3600);

		$opt = EVO()->frontend->evo_options;
		$customoffset = !empty($opt['evo_time_offset'])? 
			(intval($opt['evo_time_offset'])) * 60:
			0;

		return $offset + $customoffset;
	}
	function set_utc_timezone(){
		date_default_timezone_set('UTC');
	}
	function get_local_unix_now(){
		$this->set_local_timezone();
		return time();
	}
	function set_local_timezone(){
		$tzstring = $this->get_timezone_str();
		$tzstring = $tzstring == 'UTC+0'? 'UTC': $tzstring;
		date_default_timezone_set($tzstring);
	}

	function get_timezone_str(){
		$tzstring = get_option('timezone_string');

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( false !== strpos($tzstring,'Etc/GMT') )
			$tzstring = '';

		$current_offset='';
		if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
			$check_zone_info = false;
			if ( 0 == $current_offset )
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}

		return $tzstring;
	}
}