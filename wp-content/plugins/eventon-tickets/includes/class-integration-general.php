<?php
/**
 * General integration parts with other components
 */

class evotx_int{
	public function __construct(){
		if(is_admin()){
			add_filter('evo_csv_export_fields', array($this,'export_field_names'), 10,1);
			add_filter('evocsv_additional_csv_fields', array($this,'csv_importer_fields'), 10,1);
		}

		// confirmation email additional information
		add_action('evotix_confirmation_email_data_after_tr', array($this, 'additional_info'), 10,4);
	}

	// Confirmation email
		function additional_info($EVENT, $TIX_CPT, $order, $styles){
			if( $EVENT->get_prop('_tx_add_info') && $order->get_status() == 'completed'){
				?>
				 <tr><td colspan='3' style='border-top:1px solid #e0e0e0; padding:8px 20px; text-transform: none'>
				 	<div style='font-size: 14px;font-style: italic;font-family: "open sans",helvetica; padding: 0px; margin: 0px;font-weight: bold;line-height: 100%;word-break:break-word'><?php echo $EVENT->get_prop('_tx_add_info');?></div>
				 	<p style="<?php echo $styles['004'];?>"><?php evo_lang_e('Additional Ticket Information');?></p>
				 </td></tr>
				<?php
			}
		}
	
	// include ticket event meta data fields for exporting events as CSV
	function export_field_names($array){
		global $evotx; 
		$adds = $evotx->functions->get_event_cpt_meta_fields();

		foreach($adds as $ad){
			$array[$ad] = $ad;
		}
		return $array;
	}

	// for CSV Importer
	function csv_importer_fields($array){
		global $evotx; 
		$adds = $evotx->functions->get_event_cpt_meta_fields();

		return array_merge($array, $adds);
	}
}

new evotx_int();