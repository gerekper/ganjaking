<?php
/**
 * Admin Functions
 * @version 0.1
 */

class evorm_fnc{

	private $addon ='rs';
	function send_email($event_id, $var, $addon){
		
		$to_emails = array();
		$this->addon = $addon;

		// sending list
			$towho = $addon=='rs'? EVORS()->get_rsvp_prop($var.'_group'): EVOTX()->get_tx_prop($var.'_group');
			
			switch( $towho){
				// for RSVP
				case 'all':
					$guests = EVORS()->functions->GET_rsvp_list($event_id, 'all');
					foreach(array('y','m','n') as $rsvp_status){
						if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
							foreach($guests[$rsvp_status] as $guest){
								$to_emails[] = $guest['email'];
							}
						}
					}
				break;
				case 'coming':
					$guests = EVORS()->functions->GET_rsvp_list($event_id, 'all');

					foreach(array('y','m') as $rsvp_status){
						if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
							foreach($guests[$rsvp_status] as $guest){
								if(!isset($guest['email'])) continue;
								$to_emails[] = $guest['email'];
							}
						}
					}
				break;
				case "notcoming":
					$guests = EVORS()->functions->GET_rsvp_list($event_id, 'all');
					if(is_array($guests) && isset($guests['n']) && count($guests['n'])>0){
						foreach($guests['n'] as $guest){
							$to_emails[] = $guest['email'];
						}
					}
				break;
				case "checkedguests":
					add_filter('evors_guest_list_metaquery', array($this, '_args_checked_guests')) ;
					$guests = EVORS()->functions->GET_rsvp_list($event_id, 'all');

					foreach(array('y','m') as $rsvp_status){
						if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
							foreach($guests[$rsvp_status] as $guest){
								if(!isset($guest['email'])) continue;
								$to_emails[] = $guest['email'];
							}
						}
					}

					remove_filter('evors_guest_list_metaquery', array($this, '_args_checked_guests'));
				break;
				case "notcheckedguests":
					add_filter('evors_guest_list_metaquery', array($this, '_args_notchecked_guests')) ;
					$guests = EVORS()->functions->GET_rsvp_list($event_id, 'all');
					//print_r($guests);
					foreach(array('y','m') as $rsvp_status){
						if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
							foreach($guests[$rsvp_status] as $guest){
								if(!isset($guest['email'])) continue;
								$to_emails[] = $guest['email'];
							}
						}
					}

					remove_filter('evors_guest_list_metaquery', array($this, '_args_notchecked_guests'));
				break;
				// for ticket addon
					case "completed":
						$guests = EVOTX()->functions->get_customer_ticket_list($event_id,'', '','customer_order_status');
						foreach(array('completed') as $order_status){
							if(is_array($guests) && isset($guests[$order_status]) && count($guests[$order_status])>0){
								foreach($guests[$order_status] as $guest){
									if(!isset($guest['email'])) continue; // for no emails
									$to_emails[] = $guest['email'];
								}
							}
						}
					break;
					case "pending":
						$guests = EVOTX()->functions->get_customer_ticket_list($event_id,'', '','customer_order_status');
						foreach(array('pending','on-hold') as $order_status){
							if(is_array($guests) && isset($guests[$order_status]) && count($guests[$order_status])>0){
								foreach($guests[$order_status] as $guest){
									if(!isset($guest['email'])) continue; // for no emails
									$to_emails[] = $guest['email'];
								}
							}
						}
					break;
				
			}

		if(sizeof($to_emails)>0){
			$subject = $addon=='rs'? EVORS()->get_rsvp_prop($var.'_subject'): EVOTX()->get_tx_prop($var.'_subject');
			$subject = !empty($subject)? $subject: 'Event Reminder';

			$msg = $this->get_email_message($event_id, $var);

			// close any unclosed HTML tags in the email message body
				$msg = $this->closetags($msg);
			
			$message_body = "<div style='padding:15px'>".html_entity_decode($msg). "</div>";
			
			// pluggable hook to override email layout
			$message_body = apply_filters('evorm_reminder_email_body', $this->get_evo_email_body($message_body), $event_id, $msg );

			$args = array(
				'type'=>'bcc',
				'html'=>'yes',
				'to'=> $to_emails,
				'subject'=>$subject,
				'from'=>$this->get_from_email(),
				'from_email'=>$this->get_from_email(),
				'from_name'=>$this->get_from_name(),
				'message'=>$message_body,
			);


			$helper = new evo_helper();
			return $helper->send_email($args);
		}

		//print_r($args);

		return false;
	}

	function closetags($html) {
	    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	    $openedtags = $result[1];
	    preg_match_all('#</([a-z]+)>#iU', $html, $result);
	    $closedtags = $result[1];
	    $len_opened = count($openedtags);
	    if (count($closedtags) == $len_opened) {
	        return $html;
	    }
	    $openedtags = array_reverse($openedtags);
	    for ($i=0; $i < $len_opened; $i++) {
	        if (!in_array($openedtags[$i], $closedtags)) {
	            $html .= '</'.$openedtags[$i].'>';
	        } else {
	            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
	        }
	    }
	    return $html;
	}

	// hook connections
		function _args_checked_guests($array){
			$array[] = array( 'key'=>'status','value'=>'checked');
			return $array;
		}
		function _args_notchecked_guests($array){
			$array[] = array( 'key'=>'status','value'=>'check-in');
			return $array;
		}

	// get eventon driven email body
		function get_evo_email_body($message){
			global $eventon;
			// /echo $eventon->get_email_part('footer');
			ob_start();
			echo $eventon->get_email_part('header');
			echo $message;
			echo $eventon->get_email_part('footer');
			return ob_get_clean();
		}

	// Get Reminder email message body content after tag replacements
	function get_email_message($event_id, $var){
		$msg = $this->addon =='rs'? EVORS()->get_rsvp_prop($var.'_message') : EVOTX()->get_tx_prop($var.'_message');
		$msg = !empty($msg)? $msg: 'Event Reminder';

		// yes count
			if( strpos($msg, '{rsvp-yes-count}') !== false){
				$yes_count = get_post_meta($event_id, '_rsvp_yes',true);
				$yes_count = empty($yes_count)? 0: $yes_count;
				$msg = str_replace('{rsvp-yes-count}', $yes_count , $msg);
			}

		// no count
			if( strpos($msg, '{rsvp-no-count}') !== false){
				$no_count = get_post_meta($event_id, '_rsvp_no',true);
				$no_count = empty($yes_count)? 0: $no_count;
				$msg = str_replace('{rsvp-no-count}', $no_count , $msg);
			}

		// replace values in message
		$msg = str_replace('{event-name}', get_the_title($event_id), $msg);
		$msg = str_replace('{event-link}', get_permalink($event_id), $msg);

		$msg = apply_filters('evorm_reminder_email_message_processing', $msg, $event_id, $var);

		return $msg;
	}
	function get_from_name(){
		$from_name = $this->addon == 'rs'? EVORS()->get_rsvp_prop('evorm_from_name') :EVOTX()->get_tx_prop('evorm_from_name');
		return !empty($from_name)? $from_name: get_bloginfo('name');
	}
	function get_from_email(){
		$from_email = $this->addon == 'rs'? EVORS()->get_rsvp_prop('evorm_from_email') : EVOTX()->get_tx_prop('evorm_from_email');
		return !empty($from_email)? $from_email: get_bloginfo('admin_email'); 
	}

// reminder properties
	function _process_field_var($field_variable){

		if( strpos($field_variable, '-') === false) return false;

		$var = explode('-', $field_variable);
		$v = substr($var[0], 1); // evorm_pre_1
		$addon = $var[1]=='tx'? 'tx':'rs';

		return array( 'addon' => $addon, 'var'=>$v);
	}
	function get_reminders_prop(){
		return get_option('_evorm_reminders');
	}
	function get_reminder_prop($event_id, $field_variable){
		$reminders = $this->get_reminders_prop();
		if(empty($reminders) && sizeof($reminders)==0) return false;
		if(!isset($reminders[$event_id][$field_variable])) return false;

		return $reminders[$event_id][$field_variable];
	}
	function set_reminder_prop($event_id, $field_variable, $status){
		$reminders = $this->get_reminders_prop();

		$reminders = !empty($reminders)? $reminders: array();

		$reminders[$event_id][$field_variable] = $status;

		update_option('_evorm_reminders', $reminders);
	}

	// trash a completed reminder from prop
	function trash_reminder($event_id, $field_variable){
		$reminders = $this->get_reminders_prop();

		if(!isset( $reminders[$event_id][$field_variable] )) return true;

		unset($reminders[$event_id][$field_variable]);

		update_option('_evorm_reminders', $reminders);
		return true;
	}
}