<?php
/**
 * Virtual Events Meta box content
 * @ 4.2.3
 */
?>

<div class='evcal_data_block_style1 event_virtual_settings'>
	<div class='evcal_db_data'>

	<p class='yesno_row evo single_main_yesno_field'>
		<?php 	
		echo EVO()->elements->yesno_btn(array(
			'id'=>		'_virtual', 
			'var'=>		$EVENT->get_prop('_virtual'),
			'input'=>	true,
			'attr'=>	array('afterstatement'=>'evo_virtual_details')
		));
		?>						
		<label class='single_yn_label' for='_virtual'><?php _e('This is a virtual (online) event', 'eventon')?></label>
	</p>


	<div id='evo_virtual_details' class='evo_edit_field_box' style='display:<?php echo $EVENT->check_yn('_virtual')?'block':'none';?>'>
		<?php 
		$btn_data = array(
			'lbvals'=> array(
				'lbc'=>'config_vir_events',
				't'=>__('Configure Virtual Event Details','eventon'),
				'ajax'=>'yes',
				'd'=> array(					
					'eid'=> $EVENT->ID,
					'action'=> 'eventon_config_virtual_event',
					'uid'=>'evo_get_virtual_events',
				)
			)
		);
		?>
		<p><span class='evo_btn evolb_trigger' <?php echo $this->helper->array_to_html_data($btn_data);?>  style='margin-right: 10px'><?php _e('Configure Virtual Event Details','eventon');?></span></p>		
	</div>									
	</div>									
</div>