<?php
/**
 * RSVP Event Manager class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-rsvp/classes
 * @version     2.5.3
 */
class evors_event_manager{

	// user RSVP manager
		function user_rsvp_manager($atts){
			
			add_filter('evo_frontend_lightbox', array($this, 'lightbox'),10,1);
			
			EVO()->evo_generator->process_arguments($atts);

			EVORS()->frontend->register_styles_scripts();
			EVORS()->frontend->print_scripts();
			
			// intial variables
			$current_user = get_user_by( 'id', get_current_user_id() );
			$USERID = is_user_logged_in()? get_current_user_id(): false;
			$current_page_link = get_page_link();

			// loading child templates
				$file_name = 'rsvp_user_manager.php';
				$paths = array(
					0=> TEMPLATEPATH.'/'.EVO()->template_url.'rsvp/',
					1=> STYLESHEETPATH.'/'.EVO()->template_url.'rsvp/',
					2=> EVORS()->plugin_path.'/templates/',
				);

				foreach($paths as $path){	
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}
				}

			require($template);
		}

		function lightbox($array){
			global $eventon_rs;
			return $eventon_rs->frontend->lightbox($array);
			return $array;
		}

	// get events for a user
		function get_user_events($userid){
			global $eventon_rs;

			$rsvps = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'evo-rsvp',
				'meta_query' => array(
					array('key' => 'userid','value' => $userid)
				),
				//'meta_key'=>'last_name',
				'orderby'=>'post_date'
			));
			$userRSVP = array();

			ob_start();
			if($rsvps->have_posts()):					

				$currentTime = current_time('timestamp');
				$content = array('live'=>array(),'past'=>array());

				while( $rsvps->have_posts() ): $rsvps->the_post();
					$_id = $rsvps->post->ID;

					$RSVP = new EVO_RSVP_CPT($_id);
					$event_id = $RSVP->event_id();

					if(!$event_id) continue;
					if(!$RSVP->get_prop('rsvp') ) continue; // if there are no RSVP info

					$checkin_status = $RSVP->checkin_status();

					$RI = $RSVP->repeat_interval();
					$eRSVP = new EVORS_Event($event_id, $RI);
					
					$_is_current_event = $eRSVP->event->is_current_event();					
										
					// individual event class values
						$p_classes = array();
						$p_classes[] = ($_is_current_event)?'':'pastevent';
						$p_classes[] = $checkin_status;
					
					$output = '';
					$output.= "<p id='rsvp_event_{$event_id}' class='rsvpmanager_event ".(count($p_classes)>0? implode(' ', $p_classes):'')."'>" . evo_lang('RSVP ID'). ": <b>#".$_id."</b> <span class='rsvpstatus status_{$RSVP->trans_rsvp_status()}'>{$RSVP->trans_rsvp_status()}</span>
						<em class='checkin_status {$checkin_status}'>". EVORS()->frontend->get_checkin_status($checkin_status)."<em class='count'>".( $RSVP->count() )."</em></em>	
						<em class='event_data' >
							<span style='font-size:18px;'><a href='".$eRSVP->event->get_permalink()."'>". $eRSVP->event->get_title() ."</a></span>";
						
						$output.=  "<span class='event_time'>".evo_lang('Time').": ".$eRSVP->event->get_formatted_smart_time()."</span>";
						
						$output.=  "</em>";

					// if the event is current event, allow updating rsvp status
					$output.= ($_is_current_event)? 
						"<span class='action' data-cap='". $eRSVP->remaining_rsvp()."' data-etitle='".get_the_title($event_id)."' data-precap='".$eRSVP->is_per_rsvp_max_set()."' data-uid='{$userid}' data-rsvpid='{$_id}' data-eid='{$event_id}' data-ri='{$RI}' ><a class='update_rsvp' data-val='chu'>".evo_lang('Update')."</a></span>":'';

					// JSON data
						$JSON_data = EVORS()->frontend->event_rsvp_data(
							$eRSVP,array(
								'rsvpid'=>$_id,
								'rsvp'=> $RSVP->get_rsvp_status(),
								'incard'=>'no'
							)
						);
						$output .= "<span class='evors_jdata' data-j='{$JSON_data}'></span>";

					$output.= "<em class='clear'></em></p>";

					// based on live or past event arrange rsvped events
						if($_is_current_event){
							$content['live'][$RI.'.'.$event_id] = $output;
						}else{							
							$content['past'][$RI.'.'.$event_id] = $output;
						}					
				endwhile;
									
					if( count($content['live'])>0) ksort($content['live']);
					if( count($content['past'])>0) ksort($content['past']);

				// print out output
					echo !empty($content['live'])? implode('',$content['live']):'';

					if( count($content['past'])>0) 
						echo "<p class='evors_manager_subheader' style='margin: 0;padding: 10px 20px;background-color: #e8e8e8;'>". evo_lang('Past Events')."</p>";
					echo !empty($content['past'])? implode('',$content['past']):'';

			endif;
			wp_reset_postdata();
			return ob_get_clean();
		}
}