<?php
/**
 * QR Code addon integration
 * @version 1.0
 */
class EVO_Seats_QR{
	public function __construct(){
		add_filter('evoqr_data_output',array($this, 'seat_data'), 10,4);
	}

	function seat_data($output, $tixid, $id_type, $ticket_meta_data){

		if( $id_type != 'evo-tix') return $output;
		if(empty($ticket_meta_data['Seat-Number'])) return $output;

		$seat_number = $ticket_meta_data['Seat-Number'][0];

		if(!$seat_number) return $output;

		$evotix = new evotx_tix();
		$wcid = $evotix->get_product_id_by_ticketnumber($tixid);

		$seat_id 	= 	isset($ticket_meta_data['seat_id']) ? $ticket_meta_data['seat_id'][0]: false;
		$event_id 	= 	isset($ticket_meta_data['_eventid']) ? $ticket_meta_data['_eventid'][0]: false;
		$seat_slug 	= 	isset($ticket_meta_data['_evost_seat_slug']) ? $ticket_meta_data['_evost_seat_slug'][0]: false;
		

		// compatibility with old seat slug
		if(empty($seat_slug)) $seat_slug = $seat_id;
		
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