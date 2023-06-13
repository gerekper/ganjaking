<?php
/**
 * EventON ActionUser shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	ActionUser/Functions/shortcode
 * @version     2.0.11
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class au_shortcode{

	function __construct(){
		// users variable to shortcode
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'evoAU_add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'add_actionuser_fields_to_eventon_basic_cal'), 10, 1);
		add_filter('eventon_shortcode_popup',array($this, 'evoAU_add_shortcode_options'), 10, 1);

		add_action( 'eventon_process_after_shortcodes', array( $this, 'eventon_cal_variable_action' ) ,10,1);
		add_action( 'eventon_cal_variable_action_au', array( $this, 'eventon_cal_variable_check' ) ,10,1);
		
		//add_filter('eventon_event_types_update', array($this, 'eventon_event_types_update'),10, 2 );
		add_filter('eventon_event_type_value', array($this, 'eventon_event_type_value'),10, 3 );
		add_filter('eventon_filter_field_type', array($this, 'eventon_filter_field_type'),10, 2 );

		// shortcodes
		add_shortcode('add_evo_submission_form',array($this, 'output_event_submission_form'));
	}

	// user event submission form	
		function output_event_submission_form($atts){

			// arguments processing				
				$atts = shortcode_atts(
					array(
						'lang'=>'L1',
						'lightbox'=>'no',
						'msub'=>'no',
						'rlink'=>'',		
						'rdir'=>'no',	
						'rdur'=>'',	
						'calltype'=>'new',
						'wordcount'=>0,
						'formrtl'=>'no',
						'btntxt'=> 'Submit New Event',
						'header'=>'',
						'sheader'=>'',
					), $atts , 'add_evo_submission_form'
				);

			EVO()->frontend->load_evo_scripts_styles();		
			EVOAU()->frontend->print_frontend_scripts();		
			EVOAU()->frontend->lang = $atts['lang'];		

			do_action('evoau_submission_form_initiate');

			ob_start();		

			if($atts['lightbox']=='yes'){
				?>
				<a id='evoau_lightbox_form_btn' data-mce='0' class='evoAU_form_trigger_btn ' data-d='<?php echo json_encode($atts);?>'><?php echo $atts['btntxt'];?></a>
				<?php
			}else{

				$form = new evoau_form();
				echo $form->get_form_container( $atts);
			}
			
			return ob_get_clean();
		}
	

	/*	SHORTCODE processing	*/
		function eventon_cal_variable_check($args){
			
			$uid = $this->get_correct_userid($args);
			// check if users variable is present and not empty or not equal to all
			if($uid != 'all'){
				$this->users = $uid;								
				add_action('eventon_sorting_filters', array($this, 'eventon_frontend_filter'),7);
			}else{
				remove_action('eventon_sorting_filters', array($this, 'eventon_frontend_filter'));
			}
		}
		function eventon_cal_variable_action($args){
			$args['users']=$this->get_correct_userid($args);
			return $args;
		}

		// get corrected user ID based on currentuser shortcode argument
		// @require 2.3.5 (eventon)
		function get_correct_userid($args){

			//print_r($args['users']);
			$user_val = !empty($args['users'])? 
				($args['users']=='none'? $args['event_users']: $args['users'] ):'all';

			if( $user_val !='all' ){
				return $args['users'];
			}else{ // userrolles = all
				if(!is_user_logged_in()){
					if(!empty($args['currentuser']) && $args['currentuser']=='yes'){
						return 'none';
					}else{
						return 'all';
					}
				}else{
					if(!empty($args['currentuser']) && $args['currentuser']=='yes'){
						return get_current_user_id();
					}else{
						return 'all';
					}					
				}
			}	
		}
				 
	// include event users in event type taxonomies
	// @updated: 2016-4-11
		function eventon_event_types_update($event_types, $shortcode_args){
			if(!empty($shortcode_args['users'])) $event_types[] = 'event_users';
			return $event_types;
		}

		// return the shortcode value for taxonomy event_users in filter cal argument
			function eventon_event_type_value($eventypeval, $event_type, $shortcode_args){
				
				$neweventypeval = $eventypeval;
				if($event_type=='event_users'){
					$neweventypeval = !empty($shortcode_args['users'])? 
						$shortcode_args['users'].'':
						$eventypeval ;
				}
				return $neweventypeval;
			}
		// filter field type
			function eventon_filter_field_type($type, $filtername){
				return (!empty($filtername) && in_array($filtername, array('event_users','event_user_roles')) )? 'slug':$type;
			}

	
	// Add the user filtering fields to frontend cal
		function eventon_frontend_filter(){			
			echo "<div class='eventon_filter' data-filter_field='event_users' data-filter_val='{$this->users}' data-filter_type='tax'></div>";			
		}
		
	
	function calhead_args($array, $arg=''){
		if(!empty($arg['users']))	$array['users'] = $arg['users'];
		return $array;
	}
	// add new default shortcode arguments
		function evoAU_add_shortcode_defaults($arr){
			return array_merge($arr, array(
				'users'=>'all',
				'currentuser'=>'no',
			));	
		}
	
	// Add user IDs field to shordcode basic cal version
		function add_actionuser_fields_to_eventon_basic_cal($array){

			$array[] = array(
				'name'=>'Events with only these user IDs',
				'placeholder'=>'eg. 8,19',
				'type'=>'text',
				'guide'=>'Show events that have only these user IDs assigned to',
				'var'=>'users',
				'default'=>'all'
			);
			$array[] = array(
				'name'=>'Show only events from current loggedin user',
				'type'=>'YN',
				'guide'=>'This will show events from only the current logged-in user, if not logged in will use above user IDs or revert to all users.',
				'var'=>'currentuser',
				'default'=>'no'
			);
			return $array; 			
		}

	/*	ADD shortcode buttons to eventON shortcode popup*/
		function evoAU_add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_AU',
					'name'=>'Action User - Event Submission Form',
					'code'=>'add_evo_submission_form',
					'variables'=>array(
						array(
							'name'=>'<i>NOTE: Form Header Texts you set in here will override the values saved in eventon action user settings</i>',
							'type'=>'note',
						),
						array(
							'name'=>'Form Header Text',
							'placeholder'=>'eg. Submit your event',
							'type'=>'text',
							'var'=>'header','default'=>'0',
						),
						array(
							'name'=>'Form Subheader Text',
							'placeholder'=>'eg. Submit your event',
							'type'=>'text',
							'var'=>'sheader','default'=>'0',
						),
						array(
							'name'=>'Show button with lightbox form',
							'type'=>'YN',
							'guide'=>'This will show submit events button on page with lightbox event submission form',
							'var'=>'lightbox',
							'default'=>'no',
							'afterstatement'=>'titles'
						),
							array(
								'name'=>'Text for submit events button',
								'placeholder'=>'eg. Submit Events',
								'type'=>'text',
								'guide'=>'Text caption for the submit events button',
								'var'=>'btntxt','default'=>'0',
								'closestatement'=>'titles'
							),
						array('var'=>'rdir','type'=>'YN','name'=>'Redirect after submission','afterstatement'=>'redirection','default'=>'no'),
							array('var'=>'rlink','type'=>'text','placeholder'=>'http://www.google.com','name'=>'URL', 'guide'=>'Complete http:// url to the page you need redirected after successful form submission'),
							array('var'=>'rdur','type'=>'select','name'=>'Redirect delay', 'guide'=>'Select how soon you want the form to redirect after submission','options'=>array(
									'0'=>'Instant',
									'2'=>'2 Seconds Delay',
									'3'=>'3 Seconds Delay',
									'4'=>'4 Seconds Delay',
									'5'=>'5 Seconds Delay',
									'10'=>'10 Seconds Delay',
									),'closestatement'=>'redirection'),
						array('var'=>'msub','type'=>'YN','name'=>'Allow multiple submissions w/o page refresh','guide'=>'Allow users to submit more than one event - one after other- without having to refresh page.','default'=>'no'),
						array(
							'name'=>'Enable RTL Form (Beta)',
							'type'=>'YN',
							'guide'=>'This will make the submission form RTL compatible.',
							'var'=>'formrtl',
							'default'=>'no',
						)

					)
				),array(
					'id'=>'s_AU2',
					'name'=>'Action User - Event Manager',
					'code'=>'evo_event_manager',
					'variables'=>array(
						array(
							'name'=>'Enable pagination',
							'type'=>'YN',
							'var'=>'pagination',
							'default'=>'no',
							'afterstatement'=>'evoau_manager_pag'
						),
						array(
							'name'=>'Events per page',
							'type'=>'text',
							'var'=>'events_per_page','default'=>'5',
							'closestatement'=>'evoau_manager_pag'
						),array(
							'name'=>'Show event manager only if user have events',
							'type'=>'YN',
							'var'=>'hidden_till_events',
							'default'=>'no',
							'guide'=>'With this, when user is not logged in, it will show login message. when user log in, it will not show anything until the user have created events.'
						)
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}


}
new au_shortcode();
?>