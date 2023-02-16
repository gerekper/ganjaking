<?php
/* 
 *	Integration with QR Code Addon
 *	@version 1.0
 */

class EVOVO_qr{
	public function __construct(){	
		add_filter('evoqr_data_output',array($this, 'vo_data'), 10,4);
	}

	public function vo_data($output, $ticket_number, $ticket_type, $ticket_meta_data){

		//print_r($ticket_meta_data);

		if( $ticket_type != 'evo-tix') return $output;
		if( empty($ticket_meta_data)) return $output;		
		if(	!isset($ticket_meta_data['oDD'])) return $output;
		if(	!isset($ticket_meta_data['oDD']['evovo'])) return $output;

		foreach($ticket_meta_data['oDD']['evovo'] as $F=>$V){
			$output['otherdata'][ $F ] = $V;
		}


		return $output;

		
	}
}

new EVOVO_qr();