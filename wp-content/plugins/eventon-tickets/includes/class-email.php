<?php
/*
 * EMAILING object for event tickets
 * @version 1.6
 */

class evotx_email{
	public function __construct(){}

	function get_ticket_email_body($args){
		global $eventon;

		$evoHelper = new evo_helper();
		// get email body content with eventon header and footer
		return $evoHelper->get_email_body_content($this->get_ticket_email_body_only($args));
		//return $this->get_ticket_email_body_only($args);
	}		
	function get_ticket_email_body_only($args){
		ob_start();

		$args = array($args, true);
		// email body message
			$file_location = EVO()->template_locator(
				'ticket_confirmation_email.php', 
				EVOTX()->plugin_path."/templates/email/", 
				'templates/email/tickets/'
			);
			include($file_location);

		return ob_get_clean();
	}
	// this will return eventon email template driven email body
	// need to update this after evo 2.3.8 release
		function get_evo_email_body($message){
			global $eventon;
			// /echo $eventon->get_email_part('footer');
			ob_start();
			echo $eventon->get_email_part('header');
			echo $message;
			echo $eventon->get_email_part('footer');
			return ob_get_clean();
		}
	// reusable tickets HTML for an order -- not used anywhere
		function get_tickets($tix, $email=false){

			$args = array($tix, $email);

			// GET email HTML content
			$message = $this->get_ticket_email_body_only($args);
			return $message;
		}

	// EMAIL the ticket 
		public function send_ticket_email($order_id, $outter_shell = true, $initialSend = true, $toemail=''){
			// initials
				global $woocommerce, $evotx;
				$send_wp_mail = false;

			$order_meta = get_post_custom($order_id);

			// check if order contain event ticket data
			if(!empty($order_meta['_order_type'])){

				// check if email is already sent
				if($initialSend){
					$emailSentAlready = (!empty($order_meta['_tixEmailSent']))? $order_meta['_tixEmailSent'][0]:false;
					if($emailSentAlready) return false;
				}

				$evotx_opt = $evotx->evotx_opt;
				$order = new WC_Order( $order_id );
				$tickets = $order->get_items();

				$evotx_tix = new evotx_tix();

				$ticket_numbers = $evotx_tix->get_ticket_numbers_for_order($order_id);
	
				// if there are no ticket numbers in the order				
				if(!$ticket_numbers) return false;
					
				if($order_meta['_customer_user'][0]==0){// no account created
					$__to_email = $order_meta['_billing_email'][0];
					$__customer_name = $order_meta['_billing_first_name'][0].' '.$order_meta['_billing_last_name'][0];
				}else{
					$usermeta = get_user_meta( $order_meta['_customer_user'][0] );
					$__to_email = $usermeta['billing_email'][0];
					$__customer_name = $usermeta['first_name'][0].' '.$usermeta['last_name'][0];
				}

				// + subject line replacement tags

				// update to email address if passed
					$__to_email = (!empty($toemail))? $toemail: $__to_email;


					// if to email is not present
					if(empty($__to_email)) return false;

				
				// arguments for email body
					$email_body_arguments = array(
						'orderid'=>$order_id,
						'tickets'=>$ticket_numbers, 
						'customer'=>$__customer_name,
						'email'=>'yes' 
					);
				
					$from_email = $this->get_from_email();

					$subject = '[#'.$order_id.'] '.((!empty($evotx_opt['evotx_notfiesubjest']))? 
								htmlspecialchars_decode($evotx_opt['evotx_notfiesubjest']): 
								__('Event Ticket','evotx'));
					$headers = 'From: '.$from_email;	

					// get the email body				
					$body = ($outter_shell)? 
						$this->get_ticket_email_body($email_body_arguments): 
						$this->get_ticket_email_body_only($email_body_arguments);
					

				// Send the email
					$helper = new evo_helper();
					$data = apply_filters('evotx_beforesend_tix_email_data', array(
						'to'=>$__to_email,
						'subject'=>$subject,
						'message'=> $body,
						'from'=>$from_email,
						'html'=>'yes'
					), $order_id);

					
					
					$send_wp_mail = $helper->send_email($data);
					
					

				// if initial sending ticket email record that
				if($initialSend ){
					($send_wp_mail)?
						update_post_meta($order_id,'_tixEmailSent',true):
						update_post_meta($order_id,'_tixEmailSent',false);
				}

				//echo $__to_email.' '.$headers;

				return $send_wp_mail;
			}else{
				return false;
			}
		}

		// emailing helpers
			function get_from_email(){
				$evotx_opt = get_option('evcal_options_evcal_tx');

				$__from_email = (!empty($evotx_opt['evotx_notfiemailfrom']) )?
							htmlspecialchars_decode ($evotx_opt['evotx_notfiemailfrom'])
							:get_bloginfo('admin_email');
				
				$__from_email_name = (!empty($evotx_opt['evotx_notfiemailfromN']) )?
							($evotx_opt['evotx_notfiemailfromN'])
							:get_bloginfo('name');

				// need space before < otherwise first character get cut off
					$from_email = (!empty($__from_email_name))? 
								$__from_email_name.' <'.$__from_email.'>' : $__from_email;

				return $from_email;
			}
			function get_from_email_name(){
				global $evotx;	$evotx_opt = $evotx->evotx_opt;
				return (!empty($evotx_opt['evotx_notfiemailfromN']) )?
					($evotx_opt['evotx_notfiemailfromN'])
					:get_bloginfo('name');
			}
			function get_from_email_address(){
				global $evotx;	$evotx_opt = $evotx->evotx_opt;
				return (!empty($evotx_opt['evotx_notfiemailfrom']) )?
					htmlspecialchars_decode ($evotx_opt['evotx_notfiemailfrom'])
					:get_bloginfo('admin_email');
			}

}