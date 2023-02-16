<?php
/**
 * QR Code addon integration
 * @version 1.0
 */
class EVO_Seats_QR{
	public function __construct(){
		add_filter('evoqr_data_output',array($this, 'seat_data'), 10,4);
	}

	function seat_data($output, $ticket_number, $id_type, $ticket_meta_data){

		if( $id_type != 'evo-tix') return $output;
		if( empty($ticket_meta_data)) return $output;		
		if(	!isset($ticket_meta_data['oD'])) return $output;
		if(	!isset($ticket_meta_data['oD']['seat_number'])) return $output;

		$seat_number = $ticket_meta_data['oD']['seat_number'];

		$evotix = new evotx_tix();
		$wcid = $evotix->get_product_id_by_ticketnumber($ticket_number);

		$event_id 	= 	isset($ticket_meta_data['event_id']) ? $ticket_meta_data['event_id']: false;
		$seat_slug 	= 	isset($ticket_meta_data['oDD']['seat_slug']) ? $ticket_meta_data['oDD']['seat_slug']: false;
		
		
		$SEAT = new EVOST_Seats_Seat($event_id, $wcid, $seat_slug);
		$readable_seat = $SEAT->get_readable_seat_number();

		if(empty($output['otherdata'])) $output['otherdata'] = array();

		// seat number
		$output['otherdata']['Seat-Number'] = $seat_number;

		// check if readable seat infor exists
		if(empty($readable_seat['section']) && empty($readable_seat['seat'])) return $output;
		
		// append readable seat information
		$output['otherdata']['Seat-Info'] = evo_lang('Section'). ': '. $readable_seat['section'] . (!empty($readable_seat['section_name'])? ' ('.$readable_seat['section_name'].')':'') .', '. 
			evo_lang('Row') .': '. $readable_seat['row'] .', '.
			evo_lang('Seat') .': '. $readable_seat['seat'] ;

		return $output;

	}
}
new EVO_Seats_QR();