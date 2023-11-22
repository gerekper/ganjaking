<?php
/**
 * Virtual Plus frontend
 * @version 0.2
 */

class EVOVP_Front{

	public $refresh = true;
	public $refresh_main, $helper;

	public function __construct(){

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 10 );
		add_filter( 'evo_virtual_refreshable', array( $this, 'refreshable' ), 10,1 );

		// main content
		add_filter('evo_eventcard_vir_pre_content', array($this, 'pre_content'),10,2);
		add_filter('evo_eventcard_vir_main_content', array($this, 'main_content'),10,3);
		add_filter('evo_eventcard_vir_modbox_end', array($this, 'mod_box'),10,1);

		// after event
		add_filter('evodata_vir_ended',array($this, 'vir_ended'),10 ,2);
		add_filter('evodata_vir_live',array($this, 'vir_live'),10 ,2);

		// heartbeat
		add_filter('evo_heartbeat_received', array($this, 'heartbeat'),10,2);
		add_filter('evo_heartbeat_received_nopriv', array($this, 'heartbeat_nopriv'),10,2);
		//add_filter('evo_heartbeat_settings', array($this,'wp_heartbeat_settings') ,10,1);

		// ajax
		add_action( 'evo_ajax_refresh_event_elms', array( $this, 'ajax_refresh_elms' ), 10,5 );
		add_action( 'evo_ajax_general_send_results', array( $this, 'evo_general_ajax' ), 10,2 );

		$this->helper = new evo_helper();
	}

	// register scripts for the page
		public function register_scripts(){

			if( !is_single() ) return;
			if( !EVO()->cal->check_yn('evo_realtime_vir_update','evcal_1') ) return;

			wp_enqueue_script('heartbeat');
		}

	// enable refreshing
		public function refreshable($bool){
			return true;
		}

	// check moderator
		function is_user_mod( $EV){
			//return false;
			return $EV->_is_user_moderator;
		}
	// pre event content
		public function pre_content($html, $EV){

			if($EV->is_live_now || $EV->is_past || $this->is_user_mod($EV) ) return $html;

			// pre event content			
			return apply_filters('evovp_eventcard_virtual_pre_content', $this->is_vir_pre_content($EV), $EV);
			
		}

		// pre event live content
			public function is_vir_pre_content($EV){
				if(!$EV->event->is_virtual()) return false;
				if( !$EV->event->get_prop('_vir_pre_content')) return false;

				$when = $EV->event->get_prop('_vir_pre_when');
				$current_time = EVO()->calendar->get_current_time();

				$event_start_time = $EV->event->get_event_time('start');

				// if event has started
				if( $current_time > $event_start_time) return false;

				// show pre content all the time
				if($when == 'all' ){
					return  $current_time < $event_start_time ? 
						$EV->event->get_prop('_vir_pre_content') : false; 
				} 

				$cutoff = $event_start_time - (int)$when;

				if( $cutoff <= $current_time && $current_time < $event_start_time ){
					return $EV->event->get_prop('_vir_pre_content');
				} 

				return false;
			}

	// AJAX
		public function ajax_refresh_elms( $array, $EVENT, $classes, $type, $PP ){

			// heartbeat
			if( !array_key_exists( 'evo_vir_data' , $classes) ) return $array;
			$classdata = $classes['evo_vir_data'];

			// heartbeat
			if( $type == 'heartbeat' ){				

				if( !isset($classdata['single'])) return $array;
				if( $classdata['single'] != 'y') return $array;


				$EV = new EVO_Event_Virtual($EVENT->ID, $EVENT->ri );

				$old_stage = isset($classdata['stage']) ? $classdata['stage']: '';
				$new_stage = $EV->get_current_stage();
				$this->refresh_main = isset($classdata['refresh_main']) ? $classdata['refresh_main']: 'n';

				// if stage change
				if( $old_stage != $new_stage){

					// get new pre content
					$array['evo_vir_pre_content']['html'] = $EV->get_pre_content();
					$array['evo_vir_post_content']['html'] = $EV->get_post_content();
				}

				$classdata['stage'] = $new_stage;				
				
				$array['evo_vir_main_content']['html'] = $this->get_main_content( $EV, $old_stage, $new_stage);

				// update forced refresh values
				$classdata['refresh_main'] = $this->refresh_main;
				$classdata['refresh'] = $this->refresh;
				$classdata['mod_joined'] = $EV->_get_current_mod_status(); // update current mod joined status

				$array['evo_vir_data']['data'] = $classdata;

				return $array;			
				
			}

			// regular ajax
			if( $type == 'ajax'){

				$EV = new EVO_Event_Virtual($EVENT->ID, $EVENT->ri );

				// signin
				if( isset($classdata['signin']) && $classdata['signin'] == 'y' ){
					$classdata = apply_filters('evovp_signin_user',$classdata, $EVENT, $PP);
				}

				$old_stage = isset($classdata['stage']) ? $classdata['stage']: '';
				$new_stage = $EV->get_current_stage();
				$this->refresh_main = isset($classdata['refresh_main']) ? $classdata['refresh_main']: '';

				// if stage change
				if( $old_stage != $new_stage){

					// get new pre content
					$array['evo_vir_pre_content']['html'] = $EV->get_pre_content();
					$array['evo_vir_post_content']['html'] = $EV->get_post_content();
				}

				$classdata['stage'] = $new_stage;
				
				$array['evo_vir_main_content']['html'] = $this->get_main_content( $EV, $old_stage, $new_stage);

				// update forced refresh values
				$classdata['refresh_main'] = $this->refresh_main;
				$classdata['refresh'] = $this->refresh;
				$classdata['mod_joined'] = $EV->_get_current_mod_status(); // update current mod joined status

				$array['evo_vir_data']['data'] = $classdata;

				return $array;	

			}	
		}

		public function evo_general_ajax($array, $PP){

			if(isset($PP['fnct']) && $PP['fnct'] != 'mark_event_ended') return $array;
			if( !isset($PP['uid']) ) return $array;
			if( $PP['uid'] != 'evo_mark_live_event_status' ) return $array;

			$event = new EVO_Event( $PP['eid'], '', $PP['ri']);

			// check if the user is a moderator
			$moderator = $event->get_prop('_mod');
			$this->current_user = wp_get_current_user();
			$_is_user_moderator = ( $this->current_user && $moderator !== false && $this->current_user->ID == $moderator )? true: false;

			
			if(!$_is_user_moderator) return $array;

			$val = isset($PP['vire']) && $PP['vire'] =='yes'? 'yes':'no';

			$event->set_prop('_vir_ended', $val);

			$array['_vir_ended'] = $val;

			return $array;
			
		}

	// sending data >> HEARTBEAT
		public function heartbeat_nopriv($response, $data){
			return $this->heartbeat($response, $data);
		}
		public function heartbeat($response, $data){

			if( !EVO()->cal->check_yn('evo_realtime_vir_update','evcal_1') ) return $response;

			$response = EVO_AJAX::get_refresh_elm_data( $data, 'heartbeat');
			
			return $response;
		}

	// Get virtual event content
		public function get_main_content($EV, $old_stage, $new_stage){
						
			// send values based on current stage			
			$run_new_content = false;

			
			// pre -> live
			if( $old_stage == 'pre' && $new_stage == 'live'){
				$run_new_content = true;
			}

			// if event is stil live & forced
			if( $old_stage =='live' && $this->refresh_main  == 'y'){
				$run_new_content = true;
			}

			// live -> post
			if( $old_stage == 'live' && $new_stage == 'post'){				
				$run_new_content = true;
				$this->refresh = false; // stop refreshing after this
			}

			// super refresh main content
			if( $this->refresh_main  == 'yy' ) $run_new_content = true;

			// if not to refresh
			if($this->refresh_main == 'n') return '';
 
			// get new content
			$content = '';
			if( $run_new_content ){
				$EV->single_override = true;
				$content = $EV->get_main_content();
			}

			// reset passed values
			if( $this->refresh_main  == 'yy' ) $this->refresh_main = '';

			return $content;
		}

	// moderator box end
		public function mod_box($EV){

			if( $EV->vir_type != 'jitsi') return;



			// if mod require ending event enabled
			if( $EV->EVENT->check_yn('_vir_after_mod_end') ){

				$data = array(
					'eid'=>$EV->EVENT->ID,
					'ri'=> $EV->EVENT->ri,
					'vire'=>'yes',
					'uid'=>'evo_mark_live_event_status'
				);

				if( !$EV->EVENT->check_yn('_vir_ended')){
					

					echo "<p style='padding-top:10px;'><a class='evcal_btn evo_trig_ajax evo_trig_vir_end' ". $this->helper->array_to_html_data($data) ." >". evo_lang('Mark as live event ended') ."</a></p>";
				}else{
					echo "<p style='padding-top:10px;'><a class='evcal_btn evo_trig_ajax evo_trig_vir_end' ". $this->helper->array_to_html_data($data) .">". evo_lang('Start event back') ."</a></p>";
				}
				
			}
			
		}

	// signin before seeing virtual event		
		public function main_content($html, $EE, $current_user){

			$show_signin_box = apply_filters('evovp_show_signin_box', false, $EE, $current_user);

			
			// if moderator dont show signin box
			if( $current_user && $current_user->ID == $EE->get_prop('_mod')) $show_signin_box = false;


			if($show_signin_box){
				return "<div class='evo_vir_signin evo_vir_access'>
					<p class='evo_vir_access_title'><span>". evo_lang('Please sign in to access the virtual event information') ."</span></p>
					<p><a class='evcal_btn evo_vir_signin_btn' >". evo_lang('Sign-in') ."</a></p>
				</div>";
			}else{
				return $html;
			}			
		}

	// post content 
		public function vir_ended($bool, $EVENT){
			if( $EVENT->virtual_type() != 'jitsi') return $bool;

			// if mod need to end is enabled & vir is not marked as ended
			if( $EVENT->check_yn('_vir_after_mod_end') && !$EVENT->check_yn('_vir_ended')){
				return false;
			}
			return $bool;		
		}
		public function vir_live($bool, $EVENT){
			if( $EVENT->virtual_type() != 'jitsi') return $bool;
			// if mod need to end is enabled & vir is not marked as ended
			//echo $EVENT->check_yn('_vir_ended')? 'y':'g';
			if( $EVENT->is_event_started() && $EVENT->check_yn('_vir_after_mod_end') && !$EVENT->check_yn('_vir_ended')){
				return true;
			}
			return $bool;
		}

	// heartbeat settings
	public function wp_heartbeat_settings($settings){

		if( !EVO()->cal->check_yn('evo_realtime_vir_update','evcal_1') ) return $settings;

		$refresh = EVO()->cal->get_prop('_vir_hrrate','evcal_1');
		$refresh = $refresh ? $refresh : 15;

		$settings['interval'] = (int)$refresh; //Anything between 15-120
		return $settings;
	}
	

}
new EVOVP_Front();