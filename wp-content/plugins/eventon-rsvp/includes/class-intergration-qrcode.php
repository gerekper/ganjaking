<?php
/**
 * QR Code addon integration
 */

class EVORS_QRcode{
	public function __construct(){
		add_filter('evoqr_checkin_otherdata_ar', array($this, 'other_data'), 10,3);
	}

	function other_data($output, $arr, $type){
		if($type != 'rsvp') return $output;

		return $output;
	}
}

new EVORS_QRcode();