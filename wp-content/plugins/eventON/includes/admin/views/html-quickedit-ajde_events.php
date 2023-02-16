<?php
	
	// get time format
	$wp_time_format = get_option('time_format');
	$evcal_date_format = '12h';
	$evcal_date_format =  (strpos($wp_time_format, 'H')!==false)?'24h':'12h';

	global $ajde;

?>

<fieldset class="inline-edit-col">
	<div id="eventon-fields" class="inline-edit-col evo_qedit_fields">
		<legend class='inline-edit-legend'><?php _e( 'Event Data', 'eventon' ); ?></legend>
		<fieldset class="inline-edit-col-left">
			<div id="eventon-fields" class="inline-edit-col">
			<input type='hidden' name='_evo_date_format' value=''/>
			<input type='hidden' name='_evo_time_format' value=''/>

			<p class='evo_longer_events_notice' style="display: none"><?php _e('This event is either month or year long event. Start and end date will determine the event month and year.','eventon');?></p>

			<label>
			    <span class="title"><?php _e( 'Start Date', 'eventon' ); ?></span>
			    <span class="input-text-wrap">
					<input type="text" name="evcal_start_date" class="text" placeholder="<?php _e( 'Event Start Date', 'eventon' ); ?>" value="">
				</span>
			</label>	
			<label class='evo_event_start_time'>
			    <span class="title"><?php _e( 'Start Time', 'eventon' ); ?></span>
			    <span class="input-text-wrap">
					<span class='input_time'>
						<input type="text" name="evcal_start_time_hour" class="text" placeholder="<?php _e( 'Event Start Hour', 'eventon' ); ?>" value="">
						<em>Hr</em>
					</span>
					<span class='input_time'>
						<input type="text" name="evcal_start_time_min" class="text" placeholder="<?php _e( 'Event Start Minutes', 'eventon' ); ?>" value="">
						<em>Min</em>
					</span>
					<?php if($evcal_date_format=='12h'):?>
					<span class='input_time'>
						<input type="text" name="evcal_st_ampm" class="text" placeholder="<?php _e( 'Event Start AM/PM', 'eventon' ); ?>" value="">
						<em>AM/PM</em>
					</span>
					<?php endif;?>
				</span>
			</label>
			
			<?php // end time date?>
			<label>
			    <span class="title"><?php _e( 'End Date', 'eventon' ); ?></span>
			    <span class="input-text-wrap">
					<input type="text" name="evcal_end_date" class="text" placeholder="<?php _e( 'Event End Date', 'eventon' ); ?>" value="">
				</span>
			</label>	
			<label class='evo_event_end_time'>
			    <span class="title"><?php _e( 'End Time', 'eventon' ); ?></span>
			    <span class="input-text-wrap">
					<span class='input_time'>
						<input type="text" name="evcal_end_time_hour" class="text" placeholder="<?php _e( 'Event End Hour', 'eventon' ); ?>" value="">
						<em>Hr</em>
					</span>
					<span class='input_time'>
						<input type="text" name="evcal_end_time_min" class="text" placeholder="<?php _e( 'Event End Minutes', 'eventon' ); ?>" value="">
						<em>Min</em>
					</span>
					<?php if($evcal_date_format=='12h'):?>
					<span class='input_time'>
						<input type="text" name="evcal_et_ampm" class="text" placeholder="<?php _e( 'Event End AM/PM', 'eventon' ); ?>" value="">
						<em>AM/PM</em>
					</span>
					<?php endif;?>
				</span>
			</label>

			<label>
			    <span class="title"><?php _e( 'Subtitle', 'eventon' ); ?></span>
			    <span class="input-text-wrap">
					<input type="text" name="evcal_subtitle" class="text" placeholder="<?php _e( 'Event Sub Title', 'eventon' ); ?>" value="">
				</span>
			</label>
			</div>
		</fieldset>
		<fieldset class="inline-edit-col-right" >
			<div id="eventon-fields" class="inline-edit-col">
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
					'_evo_month_long'=> array(
						'type'=>'yesno',
						'label'=>__('Month Long Event - Show this event for the entire start event Month','eventon')
					),	
					'evo_year_long'=> array(
						'type'=>'yesno',
						'label'=>__('Year Long Event - Show this event for the entire start event Year','eventon')
					),								
					'_featured'=> array(
						'type'=>'yesno',
						'label'=>__('Featured event','eventon')
					),
					'_ev_status'=> array(
						'type'=>'select',
						'label'=>__('Event Status','eventon'),
						'O'=> EVO()->cal->get_status_array('back')
					),
					'evo_exclude_ev'=> array(
						'type'=>'yesno',
						'label'=>__('Exclude from calendar','eventon')
					),
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
			<input type="hidden" name="eventon_quick_edit" value="1" />
			<input type="hidden" name="eventon_quick_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'eventon_quick_edit_nonce' ) ); ?>" />
			</div>
		</fieldset>
	</div>
</fieldset>