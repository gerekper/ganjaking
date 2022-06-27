<?php

if(!defined('ABSPATH')) {exit;}

if(!class_exists('UPGM_Ajax')) :

class UPGM_Ajax {
	
	
	public function __construct() {
		$events = array(
				'contact_me'	=> true ,
			);
		foreach ($events as $event => $nopriv) {
			add_action('wp_ajax_upgm-'.$event, array($this, $event));
			if($nopriv) {
				add_action('wp_ajax_nopriv_upgm-'.$event, array($this, $event));
			}
		}
	}

	public function contact_me() {		

		$to_user = get_userdata($_POST['reply_reciever_userid']);
		$from_user = ucfirst(userpro_profile_data('display_name', $_POST['reply_giver_userid']));

		$reply_link = get_review_page_link($_POST['reply_reciever_userid']);

		$subject = userpro_rating_get_option('contact_mail_s');

		// message
		$body = nl2br(userpro_rating_get_option('contact_mail_c'));

		$headers  = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		wp_mail( $user->user_email , $subject, $body, $headers );
		
	}
}

endif;
new UPGM_Ajax();
?>
