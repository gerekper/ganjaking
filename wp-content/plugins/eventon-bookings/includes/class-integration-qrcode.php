<?php
/**
 * QR Code addon integration
 * @version 0.1
 */
class EVO_BO_QR{
	public function __construct(){
		add_filter('evoqr_data_output',array($this, 'seat_data'), 10,3);
	}

	function seat_data($output, $tixid, $id_type){

		if( $id_type != 'evo-tix') return $output;

		$tix_number = explode('-',$tixid);
		$evo_tix_id = $tix_number[0];
		$block_time = get_post_meta($evo_tix_id, '_block_time',true);
		if(!$block_time) $block_time = get_post_meta($evo_tix_id, 'Block-Time',true);

		if(!$block_time) return $output;

		$block_index = get_post_meta($evo_tix_id, '_ticket_block_index',true);


		$output['otherdata']['Block-Index'] = $block_index;
		$output['otherdata']['Block-Time'] = $block_time;

		return $output;

	}
}
new EVO_BO_QR();