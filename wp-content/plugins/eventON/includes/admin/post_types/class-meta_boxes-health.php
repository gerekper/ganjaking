<?php
/**
 * Event Edit Meta box Health Guidance
 * @4.2.3
 */

?>
<div class='evcal_data_block_style1 event_health_settings'>
	<div class='evcal_db_data'>

		<p class='yesno_row evo single_main_yesno_field'>
			<?php 	
			echo EVO()->elements->yesno_btn(array(
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
				apply_filters('evo_healthcaredata_eventedit',
				array(
					array(
						'type'=>'yesno_btn',
						'label'=> __('Face masks required', 'eventon'),
						'id'=> '_edata[_health_mask]',
						'value'=> $EVENT->get_eprop("_health_mask"),
					),array(
						'type'=>'yesno_btn',
						'label'=> __('Temperature will be checked at entrance', 'eventon'),
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
					),
				), $EVENT
				)
			);

			echo EVO()->elements->get_element(
				array(
					'type'=>'textarea',
					'name'=> __('Other additional health guidelines', 'eventon'),
					'id'=> '_edata[_health_other]',
					'value'=> $EVENT->get_eprop("_health_other"),
				)
			);

			?>

			<p class='evo_elm_row'><a href='https://docs.myeventon.com/documentations/how-to-add-additional-healthcare-guidelines/' class='' target="_blank"><?php _e('Learn how to expand the healthcare guidelines','eventon');?></a></p>
		
		</div>
	</div>									
</div>