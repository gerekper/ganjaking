<?php
/**
 * Event Edit Meta box Health Guidance
 * @3.0
 */

?>
<div class='evcal_data_block_style1 event_health_settings'>
	<div class='evcal_db_data'>

		<p class='yesno_row evo single_main_yesno_field'>
			<?php 	
			echo $ajde->wp_admin->html_yesnobtn(array(
				'id'=>		'_health', 
				'var'=>		$EVENT->get_prop('_health'),
				'input'=>	true,
				'attr'=>	array('afterstatement'=>'evo_health_details')
			));
			?>						
			<label class='single_yn_label' for='_health'><?php _e('Enable health guidelines for this event', 'eventon')?></label>
		</p>

		<div id='evo_health_details' class='evo_edit_field_box Xevo_metabox_secondary evo_meta_elements' style='display:<?php echo $EVENT->check_yn('_health')?'block':'none';?>'>

			<?php

			$EVENT->localize_edata('_edata');
			
			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'type'=>'yesno_btn',
						'label'=> __('Face masks required', 'eventon'),
						'id'=> '_edata[_health_mask]',
						'value'=> $EVENT->get_eprop("_health_mask"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Temperate will be checked at entrance', 'eventon'),
						'id'=> '_edata[_health_temp]',
						'value'=> $EVENT->get_eprop("_health_temp"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Physical distance maintained event', 'eventon'),
						'id'=> '_edata[_health_pdis]',
						'value'=> $EVENT->get_eprop("_health_pdis"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Event area sanitized before event', 'eventon'),
						'id'=> '_edata[_health_san]',
						'value'=> $EVENT->get_eprop("_health_san"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Event is held outside', 'eventon'),
						'id'=> '_edata[_health_out]',
						'value'=> $EVENT->get_eprop("_health_out"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Vaccination Required', 'eventon'),
						'id'=> '_edata[_health_vac]',
						'value'=> $EVENT->get_eprop("_health_vac"),
					),array(
						'type'=>'textarea',
						'name'=> __('Other additional health guidelines', 'eventon'),
						'id'=> '_edata[_health_other]',
						'value'=> $EVENT->get_eprop("_health_other"),
					),
				)
			);

			?>
		
		</div>
	</div>									
</div>