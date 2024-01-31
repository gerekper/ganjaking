<?php
/**
 * Bulk edit event settings
 * @version 4.5.6
 */

global $ajde;
?>
<style type="text/css">
	#eventon-fields-bulk .evo_bedit_fields label{display: flex;align-items: flex-start;    flex-direction: column;margin-bottom: 10px;}
	#eventon-fields-bulk .evo_bedit_fields label .title, #eventon-fields-bulk .evo_bedit_fields label select{width:100%;}
</style>
<div class="inline-edit-col evo_bulk_fields" style='clear:both; display:block'>
	<div id="eventon-fields-bulk" class="inline-edit-col">
		<fieldset class="inline-edit-col-left">
			<div id="eventon-fields" class="inline-edit-col evo_bedit_fields">
				<p class='evo_subheader'><?php _e('Event Data','eventon');?></p>
			<?php
				$fields = apply_filters('evo_quick_edit_event_add_fields',array(
					
					
					'evo_hide_endtime'=> array(
						'L'=>__('Hide end time from calendar','eventon'),
					),
					'evo_span_hidden_end'=> array(
						'L'=>__('Span the event until hidden end time','eventon'),
					),								
					'_featured'=> array(
						'L'=>__('Featured event','eventon'),
					),
					'evo_exclude_ev'=> array(
						'L'=>__('Exclude from calendar','eventon'),
					),	
					'_ev_status'=> array(
						'L'=>__('Event Status','eventon'),
						'O'=> EVO()->cal->get_status_array('back')
					),
					'_time_ext_type'=> array(
						'L'=>__('Event Time extended type','eventon'),
						'O'=> array(
							'n' => __('None','eventon'),
							'dl' => __('Day Long','eventon'),
							'ml' => __('Month Long','eventon'),
							'yl' => __('Year Long','eventon'),
						)
					),				
				));

				foreach($fields as $field=>$val){					
					?>
					<label class='inline-edit-<?php echo $field;?>'>
						<span class='title'><?php echo $val['L'];?></span>
						<select name='<?php echo $field;?>'>
							<option value='-'><?php _e('— No Change —','eventon');?></option>
						<?php 
						 	$O = isset($val['O']) ? $val['O'] : array( __('Yes','eventon'), __('No','eventon'));

							foreach($O as $F=>$V){
								echo "<option value='{$F}'>{$V}</option>";
							}
						?>
						</select>
					</label>
					<?php
				}
			?>
			</div>
		</fieldset>
		<fieldset class="inline-edit-col-right" >
			<div id="eventon-fields" class="inline-edit-col evo_bedit_fields">
				<p class='evo_subheader'><?php _e('Location Data','eventon');?></p>
				<?php
					$fields = apply_filters('evo_quick_edit_event_add_fields_location',array(
						'evcal_gmap_gen'=> array(
							'L'=>__('Generate google map from the address','eventon')
						),
						'evcal_hide_locname'=> array(
							'L'=>__('Hide location name from the event card','eventon')
						),
						'evo_access_control_location'=> array(
							'L'=>__('Make location information only visible to logged-in users','eventon')
						),
					));

					foreach($fields as $field=>$val){					
					?>
					<label class='inline-edit-<?php echo $field;?>'>
						<span class='title'><?php echo $val['L'];?></span>
						<select name='<?php echo $field;?>'>
							<option value='-'><?php _e('— No Change —','eventon');?></option>
						<?php 
						 	$O = isset($val['O']) ? $val['O'] : array( __('Yes','eventon'), __('No','eventon'));

							foreach($O as $F=>$V){
								echo "<option value='{$F}'>{$V}</option>";
							}
						?>
						</select>
					</label>
					<?php
				}
				?>
				<p class='evo_subheader'><?php _e('Location Data','eventon');?></p>

				<?php
					$fields = apply_filters('evo_quick_edit_event_add_fields_organizer',array(
						'evo_evcrd_field_org'=> array(
							'L'=>__('Hide organizer field from event card','eventon')
						),
					));

					foreach($fields as $field=>$val){					
						?>
						<label class='inline-edit-<?php echo $field;?>'>
							<span class='title'><?php echo $val['L'];?></span>
							<select name='<?php echo $field;?>'>
								<option value='-'><?php _e('— No Change —','eventon');?></option>
							<?php 
							 	$O = isset($val['O']) ? $val['O'] : array( __('Yes','eventon'), __('No','eventon'));

								foreach($O as $F=>$V){
									echo "<option value='{$F}'>{$V}</option>";
								}
							?>
							</select>
						</label>
						<?php
					}
				?>

			</div>
			<input type="hidden" name="eventon_bulk_edit" value="1" />
			<input type="hidden" name="eventon_quick_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'eventon_quick_edit_nonce' ) ); ?>" />

		</fieldset>
	</div>
</div>