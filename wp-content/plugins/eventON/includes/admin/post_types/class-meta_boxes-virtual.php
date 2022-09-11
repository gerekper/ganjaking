<?php
/**
 * Virtual Events Meta box content
 * @ 4.0.3
 */
?>

<div class='evcal_data_block_style1 event_virtual_settings'>
	<div class='evcal_db_data'>

	<p class='yesno_row evo single_main_yesno_field'>
		<?php 	
		echo $ajde->wp_admin->html_yesnobtn(array(
			'id'=>		'_virtual', 
			'var'=>		$EVENT->get_prop('_virtual'),
			'input'=>	true,
			'attr'=>	array('afterstatement'=>'evo_virtual_details')
		));
		?>						
		<label class='single_yn_label' for='_virtual'><?php _e('This is a virtual (online) event', 'eventon')?></label>
	</p>


	<div id='evo_virtual_details' class='evo_edit_field_box' style='display:<?php echo $EVENT->check_yn('_virtual')?'block':'none';?>'>

		<p><span class='evo_btn trig_virtual_event_config ajde_popup_trig' data-popc='print_lightbox' data-lb_cl_nm='config_vir_events' data-t='<?php _e('Configure Virtual Event','eventon');?>' data-eid='<?php echo $EVENT->ID;?>' style='margin-right: 10px'><?php _e('Configure Virtual Event Details','eventon');?></span></p>		
	</div>									
	</div>									
</div>