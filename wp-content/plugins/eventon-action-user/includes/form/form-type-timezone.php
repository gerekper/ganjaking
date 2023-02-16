<?php
// Timezone field

$helper = new evo_helper();

$tz = $unix = '';
	
// get eventon settings timezone if set
if( EVO()->cal->check_yn('evo_tzo_all','evcal_1')){
	$tz = EVO()->cal->get_prop('evo_global_tzo','evcal_1');
}

if( $this->EVENT){
	$tz = $this->EVENT->get_prop('_evo_tz');
	$unix = $this->EVENT->get_prop('evcal_srow');
}


echo "<div class='row evoau_timeone'>";

	echo EVO()->elements->get_element(
		array(
				'type'=>'dropdown',
				'id'=>'_evo_tz',
				'value'=> $tz,				
				'name'=> __('Event Timezone','eventon'),
				'options'=> $helper->get_timezone_array( $unix,true ),
				'row_style'=>'padding-bottom:10px;',
			)
	);

echo "</div>";