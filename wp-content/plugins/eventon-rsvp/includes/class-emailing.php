<?php
/**
 * RSVP Email class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-rsvp/classes
 * @version     2.5.3
 */
class evors_email{

	public function __construct(){				
		$this->optRS = get_option('evcal_options_evcal_rs');
		$this->opt2 = EVORS()->opt2;
	}

	
	// SEND email
		function send_email($args, $type='confirmation'){

			EVO()->cal->set_cur('evcal_rs');
			
			// when email sending is disabled 
			if(!empty($this->optRS['evors_disable_emails']) && $this->optRS['evors_disable_emails']=='yes') return false;
  	
  			// if notification email, and if notification emails are disabled
  				if($type == 'notification' && !EVO()->cal->check_yn('evors_notif'))
					return false;

			// if attendee notification, and if attendee notifications are disabled				
				if($type == 'attendee_notification' && EVO()->cal->check_yn('evors_disable_attendee_notifications'))
					return false;

  			// get email data
  			$args['html']= 'yes';

  			//update_post_meta(1,'aaa',$this->get_email_data($args, $type ));
  			//print_r( $this->get_email_data($args, $type ));

  			
  			$email_data = $this->get_email_data($args, $type );
  			//print_r($email_data);

  			// send email
  			return EVORS()->helper->send_email( $email_data );
			
		}

	// send email confirmation of RSVP  to submitter
		function get_email_data($args, $type='confirmation'){
			$this->evors_args = $args;

			$RR = false; $event_id = false;
			$email_data = array();

			$from_email = $this->get_from_email($type);
			
			$email_data['args'] = $args;
			$email_data['type'] = $type;

			// attachment processing
				if(isset($args['attachments'])){
					$email_data['attachments'] = $args['attachments'];
					unset($email_data['args']['attachments']);
				}

			// From email
				$to_email = isset($args['email'])? $args['email'] : '';
				if(isset($args['rsvp_id'])){
					$RR = new EVO_RSVP_CPT( $args['rsvp_id']);
					if(empty($to_email)) $to_email = $RR->email();
					$event_id = $RR->event_id();
				}

			// Based on each email type
			switch ($type) {
				case 'newuser':
					$email_data['to'] = $to_email;
					$email_data['subject'] = (!empty($this->optRS['evors_email_subject_newuser'])) ? 
						htmlspecialchars_decode($this->optRS['evors_email_subject_newuser']): 
						__('Your new password','evors');
					$headers = 'From: '.$from_email."\r\n";
					$headers .= 'Reply-To: '.$from_email. "\r\n";

					$filename = 'newuser_email';

					do_action('evors_newuser_email_before', $args['rsvp_id']);
				break;
				case 'confirmation':
					$email_data['to'] = $to_email;

					$email_data['subject'] = '[#'.$args['rsvp_id'].'] '.((!empty($this->optRS['evors_notfiesubjest_e']))? 
					htmlspecialchars_decode($this->optRS['evors_notfiesubjest_e']): __('RSVP Confirmation','evors'));
					$filename = 'confirmation_email';
					$headers = 'From: '.$from_email."\r\n";
					$headers .= 'Reply-To: '.$from_email. "\r\n";

					do_action('evors_confirmation_email_before', $args['rsvp_id']);

				break;

				case 'digest':
					
					$__to_email = (!empty($this->optRS['evors_digestemail_to']) )?
						htmlspecialchars_decode ($this->optRS['evors_digestemail_to'])
						:get_bloginfo('admin_email');
					$email_data['to'] = $__to_email;

					$text = (!empty($this->optRS['evors_digestemail_subjest']))? $this->optRS['evors_digestemail_subjest']: 'Digest Email for {event-name}';
					
					if(!empty($args['e_id']))
						$text = str_replace('{event-name}', get_the_title($args['e_id']), $text);

					$email_data['subject'] = $text;
					$filename = 'digest_email';
					$headers = 'From: '.$from_email. "\r\n";

				break;

				// Other attendee notification
				case 'attendee_notification':
					$email_data['to'] = $to_email;
					$email_data['subject'] = isset($args['notice_subject']) ? $args['notice_subject']:
						'[#'.$args['rsvp_id'].'] '.((!empty($this->optRS['evors_notfi_update_subject']))? 
						htmlspecialchars_decode($this->optRS['evors_notfi_update_subject']): 
						__('RSVP Update Confirmation','evors'));
					$filename = 'attendee_notification_email';
					$headers = 'From: '.$from_email;
				break;
				
				// admin notification
				case 'notification': 

					$_other_to = '';
					$__to_email = '';

					// if manual sending notification use that to email
					if( isset($args['method']) && $args['method'] == 'manual'){
						$__to_email = $to_email;
					}

					if(empty($__to_email)){
						// pre get value
						$event_pmv = get_post_custom( $event_id );

						$__to_email = (!empty($this->optRS['evors_notfiemailto']) )?
							htmlspecialchars_decode ($this->optRS['evors_notfiemailto'])
							:get_bloginfo('admin_email');
						

						// post author to be included
							$notify_event_author = evo_check_yn($event_pmv, 'evors_notify_event_author');
							
							if($notify_event_author){
								$post_author_id = get_post_field( 'post_author', $args['e_id'] );

								if(!empty($post_author_id)) 
									$author_email = get_the_author_meta( 'user_email' , $post_author_id);

								if($author_email) $__to_email .= ','. $author_email;
							}	

						// other email addresses mentioned in event edit page
							$other_emails = evo_var_val($event_pmv, 'evors_add_emails');
							if(!empty($other_emails)) $_other_to = ','. $other_emails;
					}
				

					$email_data['to'] = $__to_email . $_other_to;

					if(!empty($args['emailtype']) && $args['emailtype']=='update'){							
						$text = (!empty($this->optRS['evors_notfiesubjest_update']))? $this->optRS['evors_notfiesubjest_update']: 'Update RSVP Notification';
					}else{
						$text = (!empty($this->optRS['evors_notfiesubjest']))? $this->optRS['evors_notfiesubjest']: 'New RSVP Notification';
					}					

					$email_data['subject'] ='[#'.$args['rsvp_id'].'] '.$text;
					$filename = 'notification_email';
					$headers = 'From: '.$from_email. "\r\n";				

				break;
			}
			
			// if TO is set generate other email data
			if(isset($email_data['to'])){

				$email_complete_html = $this->_get_email_body($args, $filename);
				//$email_complete_html = $this->get_evo_email_body( $email_complete_html );

				$email_data['message'] = $email_complete_html;
				$email_data['header'] = $headers;	
				$email_data['from'] = $from_email;
			}	
			
			// additions
			if( isset($args['return_details'])) $email_data['return_details'] = $args['return_details'];

			return apply_filters('evors_beforesend_email_data', $email_data);
		}

	// return proper FROM email with name
		function get_from_email($type='confirmation'){

			if($type=='digest'){
				$__from_email = $this->get_from_email_address($type);
				$__from_email_name = $this->get_from_email_name($type);
					$from_email = (!empty($__from_email_name))? 
						$__from_email_name.' <'.$__from_email.'>' : $__from_email;
			}else{
				$var = ($type=='confirmation')?'_e':'';

				$__from_email = $this->get_from_email_address($type);
				$__from_email_name = $this->get_from_email_name($type);
					$from_email = (!empty($__from_email_name))? 
						$__from_email_name.' <'.$__from_email.'>' : $__from_email;
			}					
			return $from_email;
		}

		function get_from_email_address($type='confirmation'){
			if($type=='digest'){
				$__from_email = (!empty($this->optRS['evors_digestemail_from']) )?
					htmlspecialchars_decode ($this->optRS['evors_digestemail_from'])
					:get_bloginfo('admin_email');
			}else{
				$var = ($type=='confirmation')?'_e':'';
				$__from_email = (!empty($this->optRS['evors_notfiemailfrom'.$var]) )?
					htmlspecialchars_decode ($this->optRS['evors_notfiemailfrom'.$var])
					:get_bloginfo('admin_email');				
			}
			return $__from_email;
		}
		function get_from_email_name($type = 'confirmation'){
			if($type=='digest'){
				$__from_email_name = (!empty($this->optRS['evors_digestemail_fromN']) )?
					($this->optRS['evors_digestemail_fromN'])
					:get_bloginfo('name');					
			}else{
				$var = ($type=='confirmation')?'_e':'';
				$__from_email_name = (!empty($this->optRS['evors_notfiemailfromN'.$var]) )?
					($this->optRS['evors_notfiemailfromN'.$var])
					:get_bloginfo('name');
			}	
			return $__from_email_name;
		}

	// email body for confirmation
		function _get_email_body($args, $file, $location ='', $append=''){
			ob_start();

			$args = $args;

			$file_location = EVO()->template_locator(
				$file.'.php', 
				(!empty($location)? $location : EVORS()->addon_data['plugin_path']."/templates/") , 
				(!empty($append)? $append : 'templates/email/rsvp/')
			);

			include($file_location);
			
			return ob_get_clean();
		}
	// this will return eventon email template driven email body
	// need to update this after evo 2.3.8 release
		function get_evo_email_body($message){
			// /echo $eventon->get_email_part('footer');
			ob_start();
			echo EVO()->get_email_part('header');
			echo $message;
			echo EVO()->get_email_part('footer');
			return ob_get_clean();
		}

	// Digest emails
		public function schedule_digest_email(){
			if(!empty($this->optRS['evors_digest']) && $this->optRS['evors_digest']=='yes'){
				$events = new WP_Query(array(
					'post_type'=>'ajde_events',
					'posts_per_page'=>-1,
					'meta_key'     => 'evors_daily_digest',
					'meta_value'   => 'yes',
				));

				// if there are events with RSVP digest enabled
				if($events->have_posts()){
					
					while($events->have_posts()): $events->the_post();
						$eventid = $events->post->ID;
						$eventStartTime = get_post_meta($eventid, 'evcal_srow',true);
						$currentTime = current_time('timestamp');

						if($eventStartTime<= $currentTime) break;

						$what = $this->send_email(array(
							'e_id'=>$eventid,
						), 'digest');
					endwhile;
				}
				wp_reset_postdata();
			}
		}
}