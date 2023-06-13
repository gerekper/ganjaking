<?php
/**
 * RSVP Events Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-RS/Functions/AJAX
 * @version     2.3.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evorsvp_ajax{
	public function __construct(){
		$ajax_events = array(
			
			//'the_ajax_evors_fnd'=>'evoRS_find_rsvp',			
			'the_ajax_evors_a7'=>'save_rsvp_from_eventtop',
			//'the_ajax_evors_a8'=>'find_rsvp_byuser',	
			'evors_get_rsvp_form'=>'evors_get_rsvp_form',
			'evors_find_rsvp_form'=>'evors_find_rsvp_form',	
			'the_ajax_evors'=>'save_new_rsvp',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		// AJAX only for loggedin user
		$ajax_events = array(			
			'the_ajax_evors_f4'=>'checkin_guests',
			'the_ajax_evors_a10'=>'update_rsvp_manager',
			'the_ajax_evors_f3'=>'generate_attendee_csv',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, 'nopriv') );
		}

		$this->help = new evo_helper();
	}
	// no priv
		function nopriv(){
			echo json_encode( array(
				'status'=>'nopriv','content'=> __('Login Needed')
			));exit;
		}
	// checkin guests 
		function checkin_guests(){
			
			$nonceDisabled = evo_settings_check_yn(EVORS()->frontend->optRS, 'evors_nonce_disable');

			if(!isset($_POST['rsvp_id'])){
				echo json_encode(array('message','Missing ID'));
				exit;
			}

			// verify nonce check 
			if(isset($_POST['nonce']) && !wp_verify_nonce( $_POST['nonce'], AJDE_EVCAL_BASENAME ) && !$nonceDisabled){
				echo json_encode(array('message','Invalid Nonce'));
				exit;
			}

			$post_data = $this->help->sanitize_array($_POST);

			$RSVP_POST = new EVO_RSVP_CPT( $post_data['rsvp_id'] );

			$RSVP_POST->set_prop('status', $post_data['status'] );

			do_action('evors_checkin_guest', $RSVP_POST->ID, $RSVP_POST->status(), $RR );
			
			$return_content = array(
				'status'=>'0',
				'new_status_lang'=> EVORS()->frontend->get_checkin_status($status),
				'new_rsvp_status'=> $status
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// Download CSV of attendance
		function generate_attendee_csv(){

			$nonceDisabled = evo_settings_check_yn(EVORS()->frontend->optRS, 'evors_nonce_disable');

			// verify nonce check 			
			if(( !$nonceDisabled && isset($_REQUEST['nonce']) && !wp_verify_nonce( $_REQUEST['nonce'], AJDE_EVCAL_BASENAME ) )
			){
				echo json_encode(array('message','Invalid Nonce!'));exit;
			}

			if( !empty($_REQUEST['e_id'])){
				EVORS()->functions->generate_csv_attendees_list($_REQUEST['e_id']);
			}else{
				echo json_encode(array('message','Event ID not provided!'));exit;
			}
		}

	// NEW RSVP from EVENTTOP
		function save_rsvp_from_eventtop(){
			$status = 0;
			$message = $content = $card_content = $user_info = '';
			
			$EVORS_front = EVORS()->frontend;
			
			// sanitize each posted values
			foreach($_POST as $key=>$val){
				$post[$key]= sanitize_text_field(urldecode($val));
			}

			// Load Event
				$EVENT = new EVO_Event( $post['e_id'], '', $post['repeat_interval']);
				
				$EVORS_front->load_rsvp_event($EVENT);			

				$RSVP = $EVORS_front->RSVP;

			// pull email and name from user data
			if(!empty($post['uid'])){
				$user_info = get_userdata($post['uid']);
				if(!empty($user_info->user_email))
					$post['email']= $user_info->user_email;
				if(!empty($user_info->first_name))
					$post['first_name']= $user_info->first_name;
				if(!empty($user_info->last_name))
					$post['last_name']= $user_info->last_name;

				// other default values
				$post['count']='1';
			}			

			$prevalidate = apply_filters('evors_rsvp_submit_pre_validation_eventtop', true, $RSVP, $post);

			if($prevalidate === true ){

				// check if already rsvped
				$already_rsvped = $RSVP->has_user_rsvped($post);

				// if user have not already RSVPed save the RSVP
				if(!$already_rsvped){ 				
					
					$save= $EVORS_front->RSVP->save_new_rsvp($post);

					
					$message = ($save==7)? 
						$EVORS_front->get_form_message('err7', $post['lang']): 
						$EVORS_front->get_form_message('succ', $post['lang']);


					$RSVP->event->relocalize_event_data();

					$content = $EVORS_front->get_eventtop_data('', (int)$post['repeat_interval'], (int)$post['e_id']);

					do_action('evors_after_rsvp_data_processed_eventtop', $status, $save, $RSVP, $post);

				// already rsvped
				}else{
					$message = $EVORS_front->get_form_message('err8', $post['lang']);
					$status = 0;
				}
			// pre-validation failed
			}else{
				if(isset($prevalidate['message'])) $message = $prevalidate['message'];
				if(isset($prevalidate['status'])) $status = $prevalidate['status'];
			}

			// event card content			
				$show_eventcard_rsvp_content = apply_filters('evors_eventcard_content_show',true, $EVORS_front->oneRSVP, $RSVP, $EVENT);	
				ob_start();
				if(  $show_eventcard_rsvp_content !== false):
				 	echo $EVORS_front->_get_event_card_content($RSVP, $EVORS_front->oneRSVP);	
				else: 
					do_action('evors_eventcard_notshow_content', $RSVP, $EVENT);
				endif; 
				$card_content = ob_get_clean();
			
			// RETURN		
				$return_content = array(
					'message'=> 	$message,
					'status'=>		(($status==7)?7:0),
					'content'=>		$content,
					'card_content'=>	$card_content,
					'd'=> $RSVP->remaining_rsvp()
				);
				
				echo json_encode($return_content);		
				exit;
		}
	
	// GET RSVP form
		function evors_get_rsvp_form(){
			
			$args = array();
			
			foreach($_POST as $K=>$V){
				if(in_array($K, array('action'))) continue;
				if( $K == 'precap'){
					if( $V == 'na'){
						$args[$K] = 'na'; continue;
					}
					$args[$K] = !empty($V)? (int)$V: '';
					continue;
				}				

				if(in_array($K, array('e_id','uid'))){
					$args[$K] = (!empty($V)? (int)$V: '');
				}else{
					$args[$K] = (!empty($V)? addslashes($V): '');
				}
			}

			//print_r($args);

			$content = EVORS()->rsvpform->get_form($args);

			echo json_encode(array(
				'status'=>'good',
				'content'=>$content
			)); exit;
		}
		
	// SAVE a RSVP from the rsvp form - NEW /UPDATE
		function save_new_rsvp(){
			global $eventon_rs;

			$HELP = new evo_helper();


			$nonce_code = EVO()->cal->check_yn('evors_nonce_disable') ? '' : AJDE_EVCAL_BASENAME;

			$post = $HELP->process_post( $_POST, 'evors_nonce', $nonce_code);
			
			$errors = false;
			$status = 0;
			$message = $save = $rsvpID = $e_id =  $EVENT = '';
			
			// verify nonce check 
			if(!$post){
				$errors = true;
				$status = 1;	$message ='Invalid Nonce';				
			}else{
				// form type
					$formtype = !empty($post['formtype']) ? $post['formtype']:'submit';
			
				// set lang
					if(!empty($post['lang']))	EVORS()->l = EVO()->lang = $post['lang'];
					if(isset($post['lang'])) EVORS()->frontend->currentlang = $post['lang'];
					$front = EVORS()->frontend;
				

				// after process
					$e_id = !empty($post['e_id'])? $post['e_id']:false;
					$repeat_interval = !empty($post['repeat_interval'])? $post['repeat_interval']:0;	

				// load event
					$EVENT = new EVO_Event( $e_id, '', $repeat_interval);
					EVORS()->frontend->load_rsvp_event($EVENT);
					$RSVP = EVORS()->frontend->RSVP;

					$count = isset($post['count'])? (int)$post['count']: 1;


				$prevalidate = apply_filters('evors_rsvp_submit_pre_validation', true, $RSVP, $post);

				$old_rsvp_status = false;

				if($prevalidate === true){
					// if UPDATING
					if(!empty($post['rsvpid'])){

						$RSVP_POST = new EVO_RSVP_CPT( $post['rsvpid'] );

						$rsvpID = $post['rsvpid'];

						$old_rsvp_status = $RSVP_POST->get_rsvp_status();

						$proceed = true;

						// if chnaging rsvp > YES make sure there are enough spaces
						if($old_rsvp_status == 'n' && $post['rsvp'] =='y'){

							$remaining_rsvp = $RSVP->remaining_rsvp();

							if($remaining_rsvp == 'wl') $proceed = false; // legacy
							if( !$RSVP->has_space_to_rsvp( $post['count'] ) ) $proceed = false;

							// @since 2.8.4
							$proceed = apply_filters('evors_updatersvp_n_to_y', $proceed, $RSVP, $RSVP_POST, $remaining_rsvp);
						}

						if( $proceed){
							$save= $RSVP->update_rsvp($post, $EVENT);
							$status = 0;
						// not enough spaces to change rsvp
						}else{
							$save = evo_lang('There are not enough space!');
							$status = 1;
						}
					// creating new
					}else{
						// check if already rsvped
						$already_rsvped = $RSVP->has_user_rsvped($post);

						// havent rsvped before
						if(!$already_rsvped){

							// check if there are spaces to rsvp
							if($RSVP->has_space_to_rsvp( $count )){
								// pass the rsvp id for change rsvp status after submit
								
								$save= EVORS()->frontend->RSVP->save_new_rsvp($post); 
														
								$rsvpID = $save;
								$status = ($save==7)? 7: 0;
							}else{
								$status = 11;
							}
							
						// user has already rsvped
						}else{ 
							$status = 8;
							$rsvpID = $already_rsvped;
						}
					}

					$message = $save;

					do_action('evors_after_rsvp_data_processed', $status, $rsvpID, $RSVP, $post, $old_rsvp_status);

				// pre-validation return false		
				}else{
					$message = isset($prevalidate['message']) ? $prevalidate['message']: 'Pre-validation Failed';
					if(isset($prevalidate['status'])) $status = $prevalidate['status'];
				}
				
			}

			$RR = !empty($rsvpID)? EVORS()->frontend->oneRSVP = new EVO_RSVP_CPT($rsvpID):false;

			// get success message HTML
				$otherdata = array('guestlist'=>'','newcount'=>'0', 'remaining'=>'0','minhap'=>'0');
				if($status == 0){

					// GET the form message
					$message = EVORS()->rsvpform->form_message(
						$RSVP, 	$rsvpID, 	$formtype,	$post
					);

					// guest list information
						$otherParts = EVORS()->rsvpform->get_form_guestlist($RSVP);
						if($otherParts){
							$otherdata['guestlist'] = $otherParts['guestlist'];
							$otherdata['newcount'] = $otherParts['newcount'];
						}

					// remaining
						$otherdata['remaining'] = $RSVP->remaining_rsvp();

					// rsvp status options selection new HTML
						$_html_option_selection = EVORS()->frontend->_get_evc_html_rsvpoption($RR, $RSVP);
				}

			// if errors
				if($errors){
					$return_content = array(
						// 'post'=>$_POST,
						'message'=> $message,
						'status'=>$status,						
					);

					echo json_encode($return_content);	exit;
				}

			// update event data object with new values
				if(!empty($EVENT) ) $EVENT->relocalize_event_data();

			// data content	
				$eventtop_content = EVORS()->frontend->get_eventtop_data($RSVP);
				$eventtop_content_your = EVORS()->frontend->get_eventtop_your_rsvp();
				$new_rsvp_text = (!empty($post['rsvp'])? 	EVORS()->frontend->get_rsvp_status($post['rsvp']):'');
					
			$return_content = array(
				// 'post'=>$_POST,
				'message'=> $message,
				'status'=>$status,
				'rsvpid'=> $rsvpID,
				'guestlist'=>$otherdata['guestlist'],
				'newcount'=>$otherdata['newcount'],
				'e_id'=> $e_id,
				'ri'=>$repeat_interval,
				'lang'=> EVORS()->frontend->currentlang,
				'data_content_eventcard'=>		EVORS()->frontend->_get_event_card_content($RSVP,$RR),
				'data_content_eventtop'=>		$eventtop_content,
				'data_content_eventtop_your'=>	$eventtop_content_your,
				'new_rsvp_text'=>$new_rsvp_text
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// FIND RSVP in order to change
		function evors_find_rsvp_form(){
			global $eventon_rs;

			$RSVP = new EVORS_Event( (int)$_POST['e_id'], 
				(!empty($_POST['repeat_interval'])?$_POST['repeat_interval']:'')
			);

			$rsvpid = $RSVP->get_rsvpid_by_email( $_POST['email'] );
			
			if($rsvpid){
				$args = array();
				foreach(array(
					'e_id',
					'repeat_interval',
					'cap',
					'precap',
					'email',					
					'formtype',
					'incard'
				) as $key){
					$args[$key] = (!empty($_POST[$key])? $_POST[$key]: '');
				}

				$args['rsvpid'] = $rsvpid;

				$content = EVORS()->rsvpform->get_form($args);
				echo json_encode(array(
					'status'=>'good',
					'content'=>$content
				)); exit;
			}else{
				echo json_encode(array(
					'status'=>'bad',
				)); exit;
			}
		}
		/*function evoRS_find_rsvp(){
			global $eventon_rs;
			$front = $eventon_rs->frontend;

			$rsvp = get_post($_POST['rsvpid']);
			$post_type = get_post_type($_POST['rsvpid']);

			if($rsvp!='' && $post_type =='evo-rsvp'){
				$rsvp_meta = get_post_meta($_POST['rsvpid']);
			}else{
				$rsvp_meta = false;
			}		
			// send out results
			echo json_encode(array(
				'status'=>(($rsvp!='')? '0':'1'),			
				'content'=> $rsvp_meta,
			));		
			exit;
		}
		*/
		/*function find_rsvp_byuser(){
			$rsvp = new WP_Query(array(
				'post_type'=>'evo-rsvp',
				'meta_query' => array(
					array(
						'key'     => 'userid',
						'value'   => $_POST['uid'],
					),
					array(
						'key'     => 'e_id',
						'value'   => $_POST['eid'],
					),array(
						'key'     => 'repeat_interval',
						'value'   => $_POST['ri'],
					),
				),
			));
			$rsvpid = false;
			if($rsvp->have_posts()){
				while($rsvp->have_posts()): $rsvp->the_post();
					$rsvpid = $rsvp->post->ID;
				endwhile;
				wp_reset_postdata();

				if(!empty($rsvpid)){
					$rsvp_meta = get_post_meta($rsvpid);
					$status = 0;
				}else{
					$status = 1;
				}
			}else{
				$status = 1;
			}

			// send out results
			echo json_encode(array(
				'status'=>$status,
				'rsvpid'=> ($rsvpid? $rsvpid:''),		
				'content'=> (!empty($rsvp_meta)? $rsvp_meta: ''),
			));		
			exit;
		}
		*/

	// update RSVP Manager
		function update_rsvp_manager(){
			global $eventon_rs;
			$manager = new evors_event_manager();
			$return_content = array(
				'content'=> $manager->get_user_events($_POST['uid'])
			);
			
			echo json_encode($return_content);		
			exit;
		}

}
new evorsvp_ajax();
?>