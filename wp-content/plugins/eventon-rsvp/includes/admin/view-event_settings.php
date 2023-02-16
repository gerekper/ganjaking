<?php
/**
 * RSVP Event Settings
 * @version 2.8
 */


$settings = new EVO_Settings();

$RS_EVENT = new EVORS_Event( $EVENT );

// manage repeat cap content
	ob_start();
	if( $EVENT->is_repeating_event() ):
	$repeat_intervals = $EVENT->get_repeats();
	?>
		<div id='evors_ri_cap' class='evotx_repeat_capacity'>
			<p><em style='opacity:0.6'><?php _e('NOTE: The capacity above should match the total number of capacity for each repeat occurance below for this event.','evors');?></em></p>
			<?php
				// if repeat intervals set 
				if($repeat_intervals && count($repeat_intervals)>0){
					$count =0;

					// get saved capacities for repeats
					$ri_capacity_rs = $EVENT->get_prop('ri_capacity_rs');


					echo "<div class='evotx_ri_cap_inputs'>";
					// for each repeat interval
					$evcal_opt1 = get_option('evcal_options_evcal_1');
					
					foreach($repeat_intervals as $interval){

						$EVENT->ri = $count;
						$RS_EVENT->ri = $count;										

						$date_time = EVO()->calendar->date_format.' '.EVO()->calendar->time_format;
						$TIME = date($date_time, $interval[0] );

						$ri_open_count = ($ri_capacity_rs && !empty($ri_capacity_rs[$count]))? $ri_capacity_rs[$count]:'0';

						//$remainCount = $RS_EVENT->get_ri_remaining_count('y', $ri_open_count);
						$remainCount = $RS_EVENT->remaining_rsvp();

						echo "<p class=''><input type='text' name='ri_capacity_rs[]' value='". ($ri_open_count) . "'/><span>" . $TIME . " - <em>". __('Remaining','evors')." <i class='rem_{$remainCount}'>".$remainCount."</i></em></span></p>";
						$count++;
					}

					$RS_EVENT->ri = 0;
					
					echo "<div class='clear'></div>";									
					echo "</div>";
				}
			?>
		</div>
<?php
	endif;
	$ri_capacity_rs_content = ob_get_clean();

$data_array =  array(
	'form_class'=>'evo_rsvp_event_settings',
	'container_class'=>'evo_rsvp pad20',
	'hidden_fields'=>array(
		'event_id'=>$EVENT->ID,
		'action'=>'evors_save_event_rsvp_settings'
	),
	'footer_btns'=> array(
		'save_changes'=> array(
			'label'=> __('Save RSVP Settings','eventon'),
			'data'=> array(
				'uid'=>'evors_save_eventedit_settings',
				'lightbox_key'=>'config_rsvp_data',
				'hide_lightbox'=> 2000,
			),
			'class'=> 'evo_btn evolb_trigger_save'
		)
	),
	'fields'=> array(
		'evors_capacity'=> array(
			'id'=>'evors_capacity',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_capacity'),
			'name'=> __('Set capacity limit for RSVP','evors'),
			'tooltip'=> __('Activating this will allow you to add a limit to how many RSVPs you can receive. When the limit is reached RSVP will close.','evors'),
			'afterstatement'=>'evors_capacity_row'
		),
		'001'=> array(
			'type'=>'begin_afterstatement',
			'id'=>'evors_capacity_row',
			'value'=> $EVENT->get_prop('evors_capacity'),
		),
			'evors_capacity_count'=> array(
				'id'=>'evors_capacity_count',
				'type'=>'input',
				'name'=> __('Total Event RSVP capacity','evors'),
				'value'=> $EVENT->get_prop('evors_capacity_count'),
				'tooltip'=> __('This is the maximum capacity of the event including current attendees','evors')
			),
				'_manage_repeat_cap_rs'=> array(
					'id'=>'_manage_repeat_cap_rs',
					'type'=> 'yesno',
					'value'=> $EVENT->get_prop('_manage_repeat_cap_rs'),
					'name'=> __('Manage available capacity separate for each repeating interval of this event','evors'),
					'tooltip'=> __('Once repeating event capacities are set the total capacity for event will be overridden. If you just made event repeat, this event need to be updated for repeat options to show up.','evors'),
					'afterstatement'=>'ri_capacity_rs'
				),

				'ri_capacity_rs1'=> array(
					'type'=>'begin_afterstatement',
					'id'=>'ri_capacity_rs',
					'value'=> $EVENT->get_prop('_manage_repeat_cap_rs'),
				),
				'ri_capacity_rs'=> array(
					'type'=>'code',
					'id'=>'ri_capacity_rs',
					'content'=> $ri_capacity_rs_content
				),

				'ri_capacity_rs2'=> array(	'type'=>'end_afterstatement'),

			'evors_capacity_show'=> array(
				'id'=>'evors_capacity_show',
				'type'=> 'yesno',
				'value'=> $EVENT->get_prop('evors_capacity_show'),
				'name'=> __('Show remaining spaces count on front-end','evors'),
			),
			'evors_show_bars'=> array(
				'id'=>'evors_show_bars',
				'type'=> 'yesno',
				'value'=> $EVENT->get_prop('evors_show_bars'),
				'name'=> __('Show capacity progress bar on eventcard','evors'),
				'tooltip'=> __('This will show progress bar on eventcard with capacity and total attendance.','evors')
			),
		'002'=> array(	'type'=>'end_afterstatement'),
		'evors_show_rsvp'=> array(
			'id'=>'evors_show_rsvp',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_show_rsvp'),
			'name'=> __('Show RSVP count for the event on EventCard','evors'),
			'tooltip'=>__('This will show how many guests are coming for each RSVP option as a number next to it on eventcard.','evors'),
		),

		'evors_show_whos_coming'=> array(
			'id'=>'evors_show_whos_coming',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_show_whos_coming'),
			'name'=> __('Show guest list to event (on eventCard)','evors'),
			'afterstatement'=>'evors_whoscoming_after'
		),		
			'evors_whoscoming_afterA'=> array(
				'type'=>'begin_afterstatement',
				'id'=>'evors_whoscoming_after',
				'value'=> $EVENT->get_prop('evors_show_whos_coming'),
			),
				'evors_whoscoming_after'=> array(
					'id'=>'evors_whoscoming_after',
					'type'=> 'yesno',
					'value'=> $EVENT->get_prop('evors_whoscoming_after'),
					'name'=> __('Show guest list ONLY after RSVP-ing to event','evors'),
					'tooltip'=> __('This will allow only guests that have RSVP-ed to the event see the guest list.','evors')
				),
			'evors_whoscoming_afterB'=> array(	'type'=>'end_afterstatement'),
		
		'_evors_show_whos_notcoming'=> array(
			'id'=>'_evors_show_whos_notcoming',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_evors_show_whos_notcoming'),
			'name'=> __('Show list of guests who are NOT coming to the event (on eventCard)','evors'),
			'afterstatement'=>'_evors_whosnotcoming_after'
		),	
			'_evors_whosnotcoming_after1'=> array(
				'type'=>'begin_afterstatement',
				'id'=>'_evors_whosnotcoming_after',
				'value'=> $EVENT->get_prop('_evors_show_whos_notcoming'),
			),
				'_evors_whosnotcoming_after'=> array(
					'id'=>'_evors_whosnotcoming_after',
					'type'=> 'yesno',
					'value'=> $EVENT->get_prop('_evors_whosnotcoming_after'),
					'name'=> __('Show list of guests who are NOT coming ONLY after RSVP-ing to event','evors'),
					'tooltip'=> __('This will allow only guests that have RSVP-ed to the event see the list of guests not coming to the event.','evors')
				),
			'_evors_whosnotcoming_after2'=> array(	'type'=>'end_afterstatement'),


		'evors_only_loggedin'=> array(
			'id'=>'evors_only_loggedin',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_only_loggedin'),
			'name'=> __('Allow only logged-in users to RSVP to this event','evors'),
		),
		'_evors_incard_form'=> array(
			'id'=>'_evors_incard_form',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_evors_incard_form'),
			'name'=> __('Show RSVP form within EventCard instead of lightbox','evors'),
			'tooltip'=> __('This will show RSVP form in the eventCard instead of showing the form as a lightbox. This value will be overridden by RSVP settings global value for inCard RSVP form.','evors')
		),

		'evors_max_active'=> array(
			'id'=>'evors_max_active',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_max_active'),
			'name'=> __('Limit maximum capacity count per each RSVP','evors'),
			'tooltip'=> __('This will allow you to limit each RSVP reservation count to a set max number, then the guests can not book more spaces than this limit','evors'),
			'afterstatement'=>'evors_max_count'
		),	
			'evors_max_count1'=> array(
				'type'=>'begin_afterstatement',
				'id'=>'evors_max_count',
				'value'=> $EVENT->get_prop('evors_max_active'),
			),
			'evors_max_count'=> array(
				'id'=>'evors_max_count',
				'type'=>'input',
				'name'=> __('Maximum count number','evors'),
				'value'=> $EVENT->get_prop('evors_max_count'),
			),
			'evors_max_count2'=> array(	'type'=>'end_afterstatement'),

		
		'evors_min_cap'=> array(
			'id'=>'evors_min_cap',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_min_cap'),
			'name'=> __('Activate event happening minimum capacity','evors'),
			'tooltip'=> __('With this you can set a minimum capacity for this event, at which point the event will take place for certain.','evors'),
			'afterstatement'=>'evors_min_count'
		),	
			'evors_min_count1'=> array(
				'type'=>'begin_afterstatement',
				'id'=>'evors_min_count',
				'value'=> $EVENT->get_prop('evors_min_cap'),
			),
			'evors_min_count'=> array(
				'id'=>'evors_min_count',
				'type'=>'input',
				'name'=> __('Minimum capacity for event to happen','evors'),
				'value'=> $EVENT->get_prop('evors_min_count'),
			),
			'evors_min_count2'=> array(	'type'=>'end_afterstatement'),

		'evors_close_time'=> array(
			'id'=>'evors_close_time',
			'type'=> 'text',
			'value'=> $EVENT->get_prop('evors_close_time'),
			'name'=> __('Close RSVP before event start (in minutes)','evors'),
			'tooltip'=> __('Set how many minutes before the event start time to close RSVP form. Time must be in minutes. Leave blank to not close RSVP before event time.','evors')
		),


		'_evors_form_af_filter'=> array(
			'id'=>'_evors_form_af_filter',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_evors_form_af_filter'),
			'name'=> __('Show only certain additional fields in RSVP form (separated by commas)','evors'),
			'tooltip'=> __('You can specify which RSVP form additional fields to show ONLY for the RSVP form for this event. Additional feild keys are like AF1, AF2 etc. Additional field keys entered below WILL show in the RSVP form. If left blank, all actve additional fields will show. AFNONE- will hide all additional fields for this event only.','evors'),
			'afterstatement'=>'_evors_form_af_filter_val'
		),	
			'_evors_form_af_filter_val1'=> array(
				'type'=>'begin_afterstatement',
				'id'=>'_evors_form_af_filter_val',
				'value'=> $EVENT->get_prop('evors_min_cap'),
			),
			'_evors_form_af_filter_val'=> array(
				'id'=>'_evors_form_af_filter_val',
				'type'=>'input',
				'name'=> __('Type the field keys','evors'),
				'default'=> 'eg. AF1,AF2 and AFNONE to not show any',
				'value'=> $EVENT->get_prop('_evors_form_af_filter_val'),
			),
			'_evors_form_af_filter_val2'=> array(	'type'=>'end_afterstatement'),

		'evors_additional_data'=> array(
			'id'=>'evors_additional_data',
			'type'=>'input',
			'name'=> __('Additional Information only visible to loggedin RSVPed guests & in Confirmation Email','evors'),
			'tooltip'=> 'Information entered in here will only be visible on front-end once user has RSVPed to the event',
			'value'=> $EVENT->get_prop('evors_additional_data'),
		),

		'evors_daily_digest'=> array(
			'id'=>'evors_daily_digest',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('evors_daily_digest'),
			'name'=> __('Receive daily digest for this event','evors'),
			'tooltip'=> __('This will send you daily email digest of RSVP information for this event. Email settings can be customized from RSVP settings. This is in BETA version','evors'),
		),
	)
);

// if rsvp notifications enabled via settings
	if( EVO()->cal->check_yn('evors_notif','evcal_rs')){
		$data_array['fields']['evors_add_emails'] = 
			array(
				'id'=>'evors_add_emails',
				'type'=> 'text',
				'value'=> $EVENT->get_prop('evors_add_emails'),
				'name'=> __('Additional email addresses to receive email notifications for new RSVPs','evors'),
				'default'=> 'eg. you@domain.com',
				'tooltip'=> __('Set additional email addresses seperated by commas to receive email notifications upon new RSVP reciept.','evors')
			);

		$data_array['fields']['evors_notify_event_author'] = 
			array(
				'id'=>'evors_notify_event_author',
				'type'=> 'yesno',
				'value'=> $EVENT->get_prop('evors_notify_event_author'),
				'name'=> __('Send email notifications to event author','evors'),
				'tooltip'=> __('Enabling this will send email notification upon new RSVPs to event author in addition to above email addresses and emails set up in RSVP settings.','evors')
			);
	}

echo $settings->get_event_edit_settings( apply_filters('evors_eventedit_fields_array', $data_array, $EVENT, $RS_EVENT, $settings ) );

?>
