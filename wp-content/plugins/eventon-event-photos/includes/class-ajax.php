<?php
/**
 * Photos Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-EP/Functions/AJAX
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoep_ajax{
	public function __construct(){
		$ajax_events = array(
				'the_ajax_evoep'=>'evoep_save_review',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
	}

	// save new review
		function evoep_save_review(){
			global $eventon_re;
			$nonce = $_POST['postnonce'];
			$status = 0;
			$message = $save = '';

			if(! wp_verify_nonce( $nonce, 'eventon_nonce' ) ){
				$status = 1;	$message ='Invalid Nonce';				
			}else{
				global $eventon_re;

				// sanitize each posted values
				foreach($_POST as $key=>$val){
					$post[$key]= sanitize_text_field(urldecode($val));
				}

				// check if already submit a rating
				if($eventon_re->frontend->functions->has_user_reviewed($post) ){					
					$message = $eventon_re->frontend->get_form_message('err8', $post['lang']);
					$status = 8;
				}else{
					$save= $eventon_re->frontend->_form_save_review($post);
					$status = 0;
				}							
			}

			$return_content = array(
				'message'=> $message,
				'status'=>$status
			);
			
			echo json_encode($return_content);		
			exit;
		}

	
}
new evoep_ajax();
?>