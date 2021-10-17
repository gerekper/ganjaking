<?php
/**
 * 
 */

global $ajde;
?>
<div class="inline-edit-col evo_bulk_fields" style='clear:both; display:block'>
	<div id="eventon-fields-bulk" class="inline-edit-col">
		<legend class='inline-edit-legend'><?php _e( 'Event Data', 'eventon' ); ?></legend>
		<fieldset class="inline-edit-col-left">
			<div id="eventon-fields" class="inline-edit-col evo_bedit_fields">
			<?php
				$fields = apply_filters('evo_quick_edit_event_add_fields',array(
					'evcal_allday'=> array(
						'type'=>'yesno',
						'label'=>__('All day event','eventon')
					),
					'evo_hide_endtime'=> array(
						'type'=>'yesno',
						'label'=>__('Hide end time from calendar','eventon')
					),
					'evo_span_hidden_end'=> array(
						'type'=>'yesno',
						'label'=>__('Span the event until hidden end time','eventon')
					),								
					'_featured'=> array(
						'type'=>'yesno',
						'label'=>__('Featured event','eventon')
					),
					'evo_exclude_ev'=> array(
						'type'=>'yesno',
						'label'=>__('Exclude from calendar','eventon')
					),	
					'_ev_status'=> array(
						'type'=>'select',
						'label'=>__('Event Status','eventon'),
						'O'=> EVO()->cal->get_status_array('back')
					),				
				));

				foreach($fields as $field=>$val){
					switch($val['type']){
						case 'yesno': ?>
							<p class="yesno_row evo">
							<?php
								echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>$field,
									'label'=> $val['label'],
									'input'=>true
								));
							?>
							</p>	
						<?php
						break;
						case 'subheader':
							?><p class='evo_subheader'><?php echo $val['label'];?></p><?php
						break;
						case 'select':
							?>
							<span class='title'><?php echo $val['label'];?></span>
							<select name='<?php echo $field;?>'>
							<?php 
								foreach($val['O'] as $F=>$V){
									echo "<option value='{$F}'>{$V}</option>";
								}
							?>
							</select>
							<?php
						break;
					}
				}
			?>
			</div>
		</fieldset>
		<fieldset class="inline-edit-col-right" >
			<div id="eventon-fields" class="inline-edit-col evo_bedit_fields">
			<?php
				$fields = apply_filters('evo_quick_edit_event_add_fields',array(
					'location'=> array(
						'type'=>'subheader',
						'label'=>__('Location Data','eventon')
					),
					'evcal_gmap_gen'=> array(
						'type'=>'yesno',
						'label'=>__('Generate google map from the address','eventon')
					),
					'evcal_hide_locname'=> array(
						'type'=>'yesno',
						'label'=>__('Hide location name from the event card','eventon')
					),
					'evo_access_control_location'=> array(
						'type'=>'yesno',
						'label'=>__('Make location information only visible to logged-in users','eventon')
					),
					'organizer'=> array(
						'type'=>'subheader',
						'label'=>__('Organizer Data','eventon')
					),
					'evo_evcrd_field_org'=> array(
						'type'=>'yesno',
						'label'=>__('Hide organizer field from event card','eventon')
					),
				));

				foreach($fields as $field=>$val){
					switch($val['type']){
						case 'yesno': ?>
							<p class="yesno_row evo">
							<?php
								echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>$field,
									'label'=> $val['label'],
									'input'=>true
								));
							?>
							</p>	
						<?php
						break;
						case 'subheader':
							?><p class='evo_subheader'><?php echo $val['label'];?></p><?php
						break;
						
					}
				}
			?>
			</div>
			<input type="hidden" name="eventon_bulk_edit" value="1" />
			<input type="hidden" name="eventon_quick_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'eventon_quick_edit_nonce' ) ); ?>" />

		</fieldset>
	</div>
</div>