<?php
/**
 * EventON ActionUser Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	ActionUser/Functions/AJAX
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoau_ajax{
	// construct
		public function __construct(){
			$ajax_events = array(
				'evoau_get_form'=>'event_get_form',
				'evoau_get_manager_event'=>'get_manager_event',
				'evoau_delete_event'=>'evoau_delete_event',
				'evoau_event_submission'=>'event_form_submission',
				'evoau_get_paged_events'=>'get_paged_events',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}

			add_action( 'wp_ajax_load_editor_new_editor', array($this,'load_editor_new_editor') );
			add_action( 'wp_ajax_nopriv_load_editor_new_editor', array($this,'load_editor_new_editor') );
		}

	// editor
		function load_editor_new_editor(){
			$id      = $_POST['id'];
		    $number  = $_POST['number'];
		    $next    = $number + 1;
		    $full_id = $id.$number;
		    $content = isset($_POST['content'])? $_POST['content'] : '';
		    $textarea = isset($_POST['textarea_name'])? $_POST['textarea_name']:'';
		   
		    wp_editor($content, $full_id, array(
		    	'textarea_rows' => 3,
		    	'textarea_rows'=>5,
		    	'textarea_name'=> $textarea,
		    	'wpautop'=>true,
		    	'tinymce'=>true,
		    	'media_buttons'=>false,
		    ));		   	
		   	echo "<div id='newreply' class='{$textarea}'></div>";
		    die();
		}

	// get paged events for event manager
		function get_paged_events(){
			
			$atts = array(
				'page'=>$_POST['page'],
				'pages'=>$_POST['pages'],
				'events_per_page'=> $_POST['epp'],
				'total_events'=> $_POST['events'],
				'pagination'=> 'yes',
				'direction'=>$_POST['direction']
			);	

			$FNC = new evoau_functions();

			$next_page = $FNC->get_next_pagination_page( $atts);

			// no next page
			if($next_page == (int)$_POST['page']){
				echo json_encode(array(
					'status'=>'same_page',
					'next_page' => $next_page
				)); exit;
			}else{
				$atts['page'] = $next_page;
			}

			$events = EVOAU()->manager->get_user_events($_POST['uid']);

			$event_html = $FNC->get_paged_events($events, $atts);

			echo json_encode(array(
				'status'=>'good',
				'html'=>$event_html,
				'next_page' => $next_page
			)); exit;

		}

	// delete an event
		function evoau_delete_event(){

			if(isset($_POST['eid'])){
				$event_id = (int)$_POST['eid'];
				wp_trash_post($event_id);

				$current_user = get_user_by( 'id', get_current_user_id() );
				$events = EVOAU()->manager->get_user_events($current_user->ID);

				$manager_html = '';
				if($events){
					foreach($events as $event_id=>$data){
						$manager_html .= EVOAU()->frontend->functions->gethtml_event_row_event($event_id, $data);
					}
				}else{
					$manager_html = "<p class='evoau_outter_shell'>". evo_lang('You do not have submitted events') . "</p>";
				}

				echo json_encode(array(
					'status'=>'good',
					'html'=>$manager_html
				)); exit;
			}else{
				echo json_encode(array(
					'status'=>'bad',
				)); exit;
			}
		}

	// get the submission form
		function get_manager_event(){
			$form = new evoau_form();

			// if event id is passed
			if(isset($_POST['eid']) && isset($_POST['method']) && $_POST['method']=='editevent'){

				$event_id = (int)$_POST['eid'];

				// passing arguments
				$atts = (!isset($_POST['json']) && empty($_POST['json']))? 
					array(): $_POST['json'];
				$atts['calltype'] = 'edit';

				//do_action('evoauem_custom_action');				
				$form_html = $form->get_content(
					$_POST['eid'], 
					apply_filters('evoau_get_edit_form_args',$atts,$event_id, $_POST['sformat'] ),
					'', true);
			}else{
				$form_html = $form->get_content();
			}

			echo json_encode(array(
				'status'=>'good',
				'html'=>$form_html,
				'd'=>'tt3'
			)); exit;
		}
		function event_get_form(){
			$form = new evoau_form();

			// if event id is passed
			if(isset($_POST['eid']) && isset($_POST['method']) && $_POST['method']=='editevent'){
				//do_action('evoauem_custom_action');				
				$form_html = $form->get_content(
					$_POST['eid'], 
					apply_filters('evoau_get_edit_form_args',array(
						'calltype'=>'edit'
					),$_POST['eid'], $_POST['sformat'] ),
					'', true);
			}else{
				$D = isset($_POST['d'])? $_POST['d']:array();
				if(isset($D['lightbox'])){
					unset($D['lightbox']);
				}

				$form_html = $form->get_content(
					'',$D 
				);
			}

			echo json_encode(array(
				'status'=>'good',
				'html'=>$form_html
			)); exit;
		}

	// Event form submission
		function event_form_submission(){
			
			if( (isset($_POST['evoau_noncename']) && isset( $_POST ) && wp_verify_nonce( $_POST['evoau_noncename'], AJDE_EVCAL_BASENAME )
				) ||
				( !empty(EVOAU()->frontend->evoau_opt['evoau_form_nonce']) || EVOAU()->frontend->evoau_opt['evoau_form_nonce']=='yes'  )
			){

				echo json_encode(EVOAU()->frontend->save_form_submissions());
				exit;

			}else{
				echo json_encode(array(
					'status'=>'bad',
					'msg'=>'bad_nonce'
				)); exit;
			}
		}


}new evoau_ajax();

?>