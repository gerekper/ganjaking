<?php
/**
 * RSVP frontend supporting functions
 * @version  2.6.8
 */
class evorsvp_functions{
				
	// RSVP post related		
		// used in reminders addon
		// GET RSVP attendee list as ARRAY
			function GET_rsvp_list($eventID, $ri=''){
				$E = new EVORS_Event($eventID, $ri);
				return $E->GET_rsvp_list();
			}

		// find a RSVP
			public function find_rsvp($rsvpid, $fname, $eid){
				$rsvp = get_post($rsvpid);
				if($rsvp){
					$rsvp_meta = get_post_custom($rsvpid);

					// check if first name and event id
					return ($fname == $rsvp_meta['first_name'][0] && $eid == $rsvp_meta['e_id'][0])? array('rsvp'=>$rsvp_meta['rsvp'][0], 'count'=>$rsvp_meta['count'][0]): false;
				}else{ return false;}
			}
		
	// AJAX Functions
	// CSV of attendees list
		function generate_csv_attendees_list($event_id){
			$e_id = $event_id;

			$RSVP = new EVORS_Event($event_id);

			header('Content-Encoding: UTF-8');
			header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename=RSVP_attendees_".date("d-m-y").".csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			//$fp = fopen('file.csv', 'w');
			
			// additional field names
			$optRS = EVORS()->evors_opt;

			$csv_headers = apply_filters('evors_attendees_csv', array(
				'rsvp_id'=>'RSVP ID',
				'last_name'=>'Last Name',
				'first_name'=>'First Name',
				'email'=>'Email Address',
				'phone'=>'Phone',
				'updates'=>'Email Updates',
				'rsvp'=>'RSVP',
				'status'=>'Status',
				'rsvp_type'=>'RSVP Type',
				'count'=>'Count',
				'event_time'=>'Event Time',
				'names'=>'Other Attendees'
			));

			// Other collected additional fields from the rsvp form
			if(!$RSVP->_show_none_AF()){
				for($x=1; $x<= EVORS()->frontend->addFields; $x++){
					if(!evo_settings_val('evors_addf'.$x, $optRS) || empty($optRS['evors_addf'.$x.'_1']) ) continue;
					if(!$RSVP->_can_show_AF('AF'.$x)) continue;
					$csv_headers['evors_addf'.$x.'_1'] = '"'.$optRS['evors_addf'.$x.'_1'].'"';
				}
			}

			echo implode(',', $csv_headers)."\n";

			$entries = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'evo-rsvp',
				'meta_query' => array(
					array('key' => 'e_id','value' => $e_id,'compare' => '=',	)
				)
			));

			$datetime = new evo_datetime();

			if($entries->have_posts()):
				$array = EVORS()->rsvp_array;
				while($entries->have_posts()): $entries->the_post();
					//initials
						$__id = get_the_ID();
						$RR = new EVO_RSVP_CPT($__id);
						$pmv = $RR->pmv;
						$RI = $RR->repeat_interval();

					// event time string
						$event_times = $datetime->get_correct_event_time($event_id, $RI);
						$event_time = $datetime->get_formatted_smart_time($event_times['start'], $event_times['end'], '', $event_id);

					foreach($csv_headers as $field=>$header){
						$switch_run = false;
						switch($field){
							case 'rsvp_id':
								echo $__id .",";
								$switch_run = true;
							break;
							case 'rsvp':
								echo (!empty($pmv['rsvp'])? EVORS()->frontend->get_rsvp_status($pmv['rsvp'][0]):'') .",";
								$switch_run = true;
							break;
							case 'status':
								$_checkinST = ( $RR->get_prop('status') )? $RR->get_prop('status') :'';
								$checkin_status = EVORS()->frontend->get_checkin_status($_checkinST);
								echo $checkin_status .",";
								$switch_run = true;
							break;
							case 'rsvp_type':
								echo $RR->get_rsvp_type() .",";
								$switch_run = true;
							break;
							case 'event_time':
								echo '"'. $event_time.'",';
								$switch_run = true;
							break;
							case 'names':
								if(!empty($pmv['names'])){
									$names = unserialize($pmv['names'][0]);
									echo '"' . implode(", ", $names) . '",';
								}else{
									echo ",";
								}
								$switch_run = true;
								
							break;
						}

						do_action('evors_attendees_csv_field_'.$field);

						if($switch_run) continue;
						
						// Other meta values						
						if(isset($pmv[$field])){
							$cover = in_array($field, array('last_name','first_name','email','phone')) ?'':'"';
							echo $cover . $pmv[$field][0] . $cover;
						}else{
							echo '';
						}
						echo ",";
					}

					echo "\n";

				endwhile;
			endif;
			wp_reset_postdata();
		}

}