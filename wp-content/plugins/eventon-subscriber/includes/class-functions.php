<?php
/**
 * Subscriber addon functions for both front and back
 * @version 0.1
 */
class evosb_functions{
	private $evoOpt_sb;
	public function __construct($evoOpt_sb){
		$this->evoOpt_sb = $evoOpt_sb;
	}

	// MAIL CHIMP functions
		// add subscribers to mailchimp list
			function add_to_mailchimp_list($__post, $subscriber_id){				
				if(!empty($this->evoOpt_sb['evosb4_mailchimp']) && $this->evoOpt_sb['evosb4_mailchimp']=='yes' && !empty($this->evoOpt_sb['evosb4_mailchimp_api'] )){

					global $eventon_sb;
					
					if(empty($this->evoOpt_sb['evosb4_mailchimp_list'])) return;

					$listid = $this->evoOpt_sb['evosb4_mailchimp_list'];
					$email = $__post['email'];

					include_once($eventon_sb->plugin_path.'/includes/lib/mailchimp/MailChimp.php');
					$MAILCHIMP = new MailChimp( $this->evoOpt_sb['evosb4_mailchimp_api'] );

					$vars = array();
					if( !empty($__post['name']))
						$vars['FNAME'] = $__post['name'];

					$subscribe_options = array(
						'listid'            => $listid,
						'email'             => $email,
						'vars'              => $vars,
						'email_type'        => 'html',
						'double_optin'      => false,
						'update_existing'   => true,
						'replace_interests' => false,
						'send_welcome'      => false
					);

					extract($subscribe_options);

					$result = $MAILCHIMP->post("lists/$listid/members", [
						'email_address' => $email,
						'status'        => 'subscribed',
					]);

					$subscriber_hash = $MAILCHIMP->subscriberHash($email);

					if(isset($__post['name'])){
						$name = explode(' ', $__post['name']);
						$fname = isset($name[0])? $name[0]:false;
						$lname = isset($name[1])? $name[1]:false;

						if($fname || $lname){
							$result_2 = $MAILCHIMP->patch("lists/$listid/members/$subscriber_hash", [
								'merge_fields' => ['FNAME'=>$fname, 'LNAME'=>$lname],
							]);
						}
						
					}
					

					//$api_response      = $MAILCHIMP->listSubscribe( $listid, $email, $vars, $email_type, $double_optin, $update_existing, $replace_interests, $send_welcome );

					// Log api response
					//self::log( __( 'MailChimp API response: %s', $api_response ) );

					if ( $MAILCHIMP->errorCode && $MAILCHIMP->errorCode != 214 ) {
						// Format error message
						$error_response = sprintf( __( 'MailChimp subscription failed: %s (%s)', 'eventon' ), $MAILCHIMP->errorMessage, $MAILCHIMP->errorCode );

						// Log
						//self::log( $error_response );

						update_post_meta($subscriber_id, '_mailchimp', 'failed');	
					}else {
						update_post_meta($subscriber_id, '_mailchimp', 'added');	
						update_post_meta($subscriber_id, '_mailchimp_list', $listid);	
					}				
				}
			}
		// UNsubscriber from mailchimp
			function unsubscribe_mailchimp_email($email, $subscriber_id, $delete_member=false){
				if(empty($this->evoOpt_sb['evosb4_mailchimp_api'] )) return;

				$listID = get_post_meta($subscriber_id, '_mailchimp_list', true);
				if(!$listID) return;

				global $eventon_sb;

				// initiate mailchimp API
				include_once($eventon_sb->plugin_path.'/includes/lib/mailchimp/MailChimp.php');
				$MAILCHIMP = new MailChimp( $this->evoOpt_sb['evosb4_mailchimp_api'] );
	
				$subscribe_options = array(
					'listid'            => $listID,
					'email'     		=> $email,
					'delete_member'     => $delete_member,
					'send_goodbye'   	=> false,
					'send_notify' 		=> false
				);

				extract($subscribe_options);

				$subscriber_hash = $MAILCHIMP->subscriberHash($email);
				//$api_response = $MAILCHIMP->listUnsubscribe($listid, $email,$delete_member,$send_goodbye, $send_notify  );
				$api_response = $MAILCHIMP->delete("lists/{$listid}/members/{$subscriber_hash}");

				if ( property_exists($MAILCHIMP, 'errorCode') && $MAILCHIMP->errorCode && $MAILCHIMP->errorCode != 214 ) {
					// Format error message
					$error_response = sprintf( __( 'MailChimp subscription failed: %s (%s)', 'eventon' ), $MAILCHIMP->errorMessage, $MAILCHIMP->errorCode );

					// Log
					//self::log( $error_response );

					update_post_meta($subscriber_id, '_mailchimp', 'unsubscribed');	
				}else {
					update_post_meta($subscriber_id, '_mailchimp', 'added');	
				}
			}
		// subscribe an unsubscribed email
			function subscribe_back_mailchimp_email($email, $subscription_id){
				if(empty($this->evoOpt_sb['evosb4_mailchimp_api'] )) return;
				
				$listID = get_post_meta($subscription_id, '_mailchimp_list', true);
				if(!$listID) return;
				//if(empty($this->evoOpt_sb['evosb4_mailchimp_list'])) return;
				//$listid = $this->evoOpt_sb['evosb4_mailchimp_list'];
				
				global $eventon_sb;
				
				include_once($eventon_sb->plugin_path.'/includes/lib/mailchimp/MailChimp.php');
				$MAILCHIMP = new MailChimp( $this->evoOpt_sb['evosb4_mailchimp_api'] );

				$vars = array();
				$name = get_post_meta($subscriber_id, 'name', true);
				if( $name)
					$vars['FNAME'] = $name;

				$subscribe_options = array(
					'listid'            => $listID,
					'email'             => $email,
					'vars'              => $vars,
					'email_type'        => 'html',
					'double_optin'      => false,
					'update_existing'   => true,
					'replace_interests' => false,
					'send_welcome'      => false
				);

				extract($subscribe_options);

				$api_response      = $MAILCHIMP->listSubscribe( $listid, $email, $vars, $email_type, $double_optin, $update_existing, $replace_interests, $send_welcome );

				// Log api response
				//self::log( __( 'MailChimp API response: %s', $api_response ) );

				if ( $MAILCHIMP->errorCode && $MAILCHIMP->errorCode != 214 ) {
					// Format error message
					$error_response = sprintf( __( 'MailChimp subscription failed: %s (%s)', 'eventon' ), $MAILCHIMP->errorMessage, $MAILCHIMP->errorCode );

					// Log
					//self::log( $error_response );

					// update_post_meta($subscriber_id, '_mailchimp', 'failed');	
				}else {
					update_post_meta($subscriber_id, '_mailchimp', 'added');	
					update_post_meta($subscriber_id, '_mailchimp_list', $listID);	
				}
			}

	// subscriber manager page creation
		// create subsctiption page
		function subscription_page(){
			global $wpdb, $post;

			// if page exists with this name
			$subscription_page = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name='subscription' AND post_status='publish'");
			
			if(empty($subscription_page)){
				//$path = AJDE_EVCAL_PATH;
				//require_once( $path.'/includes/admin/eventon-admin-install.php' );

				$content = '-- DO NOT move this page under any page! -- this page is used for eventon subscription addon -- ';

				return $this->create_page( esc_sql( _x( 'subscription', 'page_slug', 'eventon' ) ), 'eventon_subscription_page_id', __( 'Subscription', 'eventon' ), $content );
			}else{

				// make sure options are saved
				$op = get_option('eventon_subscription_page_id');
				if(!$op) update_option('eventon_subscription_page_id', $subscription_page);

				return $subscription_page;
			}
		}
		function create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ){
			global $wpdb;

			$page_id = get_option( $option );

			// if options have page id and that page is published
			if ( $page_id > 0 ){
				$page = get_post( $page_id );
				if($page && $page->post_status=='publish')
					return;
			}

			// find page in db
			$page_found = $wpdb->get_row( $wpdb->prepare( "SELECT ID, post_status FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
			
			// if page exist and its published then update options
			if ( null !== $page_found && $page_found->post_status=='publish'){
				if ( $page_id )
					update_option( $option, $page_found->ID );
				return;
			}

			$page_data = array(
		        'post_status' 		=> 'publish',
		        'post_type' 		=> 'page',
		        'post_author' 		=> 1,
		        'post_name' 		=> $slug,
		        'post_title' 		=> $page_title,
		        'post_content' 		=> $page_content,
		        'post_parent' 		=> $post_parent,
		        'comment_status' 	=> 'closed'
		    );
		    $page_id = wp_insert_post( $page_data );

		    update_option( $option, $page_id );

		    return $page_id;
		}

}	