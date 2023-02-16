<?php
/**
 * Shortcode
 */

class aup_shortcode{
	public $userroles;
	public function __construct(){
		// users variable to shortcode
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'add_actionuser_fields_to_eventon_basic_cal'), 10, 1);

		add_action( 'eventon_process_after_shortcodes', array( $this, 'eventon_cal_variable_action' ) ,10,1);
		add_action( 'eventon_cal_variable_action_au', array( $this, 'eventon_cal_variable_check' ) ,10,1);
		
		//add_filter('eventon_event_types_update', array($this, 'eventon_event_types_update'),10, 2 );
		add_filter('eventon_event_type_value', array($this, 'eventon_event_type_value'),10, 3 );
	}
	
	function calhead_args($array, $arg=''){
		if(!empty($arg['userroles']))	$array['userroles'] = $arg['userroles'];
		return $array;
	}
	/*	SHORTCODE processing	*/
		function eventon_cal_variable_check($args){
			//print_r($args);
			// user roles
			$urole = $this->get_correct_userrole($args);
			if($urole!='all'){
				$this->userroles = $urole;	
				add_action('eventon_sorting_filters', array($this, 'eventon_frontend_filter_role'),7);
			}else{
				remove_action('eventon_sorting_filters', array($this, 'eventon_frontend_filter_role'));
			}
		}
		function eventon_cal_variable_action($args){
			$args['userroles']=$this->get_correct_userrole($args);
			//print_r($args);
			return $args;
		}

		function get_correct_userrole($args){
			global $eventon_aup;

			if( !empty($args['userroles']) && $args['userroles']!='all' ){
				return $args['userroles'];
			}else{ // userrolles = all
				if(!is_user_logged_in()){
					if(!empty($args['currentuserrole']) && $args['currentuserrole']=='yes'){
						return 'none';
					}else{
						return 'all';
					}
				}else{
					if(!empty($args['currentuserrole']) && $args['currentuserrole']=='yes'){
						return $eventon_aup->frontend->get_current_userrole();
					}else{
						return 'all';
					}					
				}
			}

		}
		function eventon_frontend_filter_role(){
			echo "<div class='eventon_filter' data-filter_field='event_user_roles' data-filter_val='{$this->userroles}' data-filter_type='tax'></div>";
		}


	// include event user roles in event type taxonomies
	// @updated: 2016-4-11
		function eventon_event_types_update($event_types, $shortcode_args){

			if(!empty($shortcode_args['userroles'])) $event_types[] = 'event_user_roles';
			return $event_types;
		}

		// return the shortcode value for event types in filter cal argument
			function eventon_event_type_value($eventypeval, $event_type, $shortcode_args){
				
				$neweventypeval = $eventypeval;
				if($event_type=='event_user_roles'){
					$neweventypeval = !empty($shortcode_args['userroles'])? $shortcode_args['userroles']:$eventypeval ;
				}

				return $neweventypeval;
			}
	// add new default shortcode arguments
		function add_shortcode_defaults($arr){
			return array_merge($arr, array(
				'userroles'=>'all',
				'currentuserrole'=>'no'				
			));	
		}
	// Add user IDs field to shordcode basic cal version
		function add_actionuser_fields_to_eventon_basic_cal($array){

			foreach( get_editable_roles() as $role_name=>$role_info){
				$Roleitem[$role_name] = $role_info['name'];
			}		
			$array[] = array(
				'name'=>'Events with only these user roles',
				'placeholder'=>'eg. administrator',
				'type'=>'select',
				'guide'=>'Show events that have only these user roles assigned to',
				'var'=>'userroles',
				'options'=>$Roleitem,
				'default'=>'all'
			);
			$array[] = array(
				'name'=>'Show only events from current loggedin user role',
				'type'=>'YN',
				'guide'=>'This will show events that have been assigned with current logged-in user role type',
				'var'=>'currentuserrole',
				'default'=>'no'
			);
			return $array; 			
		}
}
new aup_shortcode();