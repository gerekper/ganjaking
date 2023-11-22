<?php
/**
 * Advent Calendar Frontend
 * @version 0.1
 */

class EVOAD_frontend{
	public function __construct(){
		// styles
		add_action( 'evo_register_other_styles_scripts', array( $this, 'register_styles_scripts' ) ,15);
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 10 );

		// calendar
		add_filter('eventon_cal_class', array($this, 'cal_classes'),10,1);

		// update event card fields
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10,5);		
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
		add_filter('eventon_eventCard_evoad', array($this, 'frontend_box'), 10, 2);
		

		// eventtop
		add_filter('eventon_eventtop_abovetitle', array($this, 'above_title'), 10, 3);
		add_filter('evo_event_etop_class_names', array($this, 'eventtop_class_names'), 10, 3);
		add_filter('eventon_eventtop_two', array($this, 'eventtop'), 10, 4);		
		//add_filter('evo_event_desc_trig_outter_styles', array($this, 'eventtop_styles'), 10, 3);

		// event fields
		add_filter('evodata_image', array($this, 'eventdata_image'), 15, 2);

		// integration with repeat customizer
		add_filter('evorc_event_edit_fields_array', array($this, 'repeat_cus_fields'),10,3);

		EVO()->cal->load_more('evcal_ad');		
	}

	// styles and scripts
		function register_styles_scripts(){
			wp_register_style( 'evoad_styles',EVOAD()->assets_path.'evoad_styles.css', array(), EVOAD()->version);
			
		}
		function load_styles(){
			wp_enqueue_style('evoad_styles');
		}

	// repeat customizer
		public function repeat_cus_fields($A, $EVENT, $EVC){

			if($EVENT->check_yn('_evo_advent_event')){

				$A[] = array(
					'type'=>'text',
					'name'=> __('Advent Event: On date content to be revealed','evoad'),
					'id'=>'_evoad_msg_onday',
					'value'=> $EVENT->get_prop('_evoad_msg_onday'),
				);
				$A[] = array(
					'type'=>'text',
					'name'=> __('Advent Event: Content to show when event is past','evoad'),
					'id'=>'_evoad_past_msg',
					'value'=> $EVENT->get_prop('_evoad_past_msg'),
				);

			}

			return $A;
			
		}

	// Calendar
		// pass a unique class name for advent calendar
		public function cal_classes($A){
			$SC = EVO()->calendar->shortcode_args;

			if( isset($SC['advent_events']) && $SC['advent_events'] == 'yes'){
				$A[] = 'evoad';
			}
			return $A;
		}

	// event fields
		public function eventdata_image($id, $EVENT){

			//if(is_admin()) return $id;

			if(!$id) return $id;
			if(!$EVENT->check_yn('_evo_advent_event')  ) return $id;
			
			// male sure event image is set to show
			$fields_status = EVO()->cal->get_prop('evoad_field_status', 'evcal_ad');

			if($fields_status && is_array($fields_status)){

				if( !array_key_exists('ftimage', $fields_status)) return $id;
				$status = $fields_status['ftimage'];

				// hide always
				if($status == 'always') return false;

				if($EVENT->is_future_event( )  && in_array($status, array('before','before/after'))){

					return false;
				}
				if($EVENT->is_past_event( ) && in_array($status, array('after','before/after')) ){
					return false;
				}


			}			

			return $id;
		}

	// Event Card array of data 
		function eventcard_array($array, $pmv, $event_id, $__repeatInterval, $EVENT){
			$event = $EVENT;
		
			// Is Advent Activated for event
			if(! $EVENT->check_yn('_evo_advent_event')  ) return $array;

			$fields_status = EVO()->cal->get_prop('evoad_field_status', 'evcal_ad');	

			// Future / before - date has not arrive yet - so hide the advent fields
			if($event->is_future_event( )){			
				
				if(!$fields_status || !is_array($fields_status)) return $array;

				foreach($fields_status as $field=>$status){
					if(array_key_exists($field, $array) && in_array($status, array('before','before/after')) ){
						unset($array[$field]);
					}
				}

				// show unrevealed message if set
				if( $msg = EVO()->cal->get_prop('evoad_hidden_ev_msg','evcal_ad')){
					$array['evoad'] = array(
						'type'=>'unrevealed',
						'msg' => $msg
					);
				}
			}

			// on the advent date
			if( $event->is_event_live_now() && $m = $event->get_prop('_evoad_msg_onday')){
				
				// run through repeat customizer
				if(function_exists('EVORC')){
					$m = EVORC()->frontend->process_value('text', '_evoad_msg_onday', $m, $event);
				}

				$array['evoad'] = array(
					'type'=>'revealnow',
					'msg' => $m
				);
			}

			// after - passed advent date
			if( $event->is_past_event('end') ){

				// is event past message is set
				if( $m = $event->get_prop('_evoad_past_msg')){

					// run through repeat customizer
					if(function_exists('EVORC')){
						$m = EVORC()->frontend->process_value('text', '_evoad_past_msg', $m, $event);
					}

					$array['evoad'] = array(
						'type'=>'pastreveal',
						'msg' => $m
					);
				}

				// hide past fields
				if($fields_status && is_array($fields_status)){
					foreach($fields_status as $field=>$status){
						if(array_key_exists($field, $array) && in_array($status, array('after','before/after')) ){
							unset($array[$field]);
						}
					}
				}
			}

			// always hidden fields
				if($fields_status && is_array($fields_status)){
					foreach($fields_status as $field=>$status){
						if(array_key_exists($field, $array) && in_array($status, array('always')) ){
							unset($array[$field]);
						}
					}
				}


			return $array;
		}

	// include eventcard advent message if set via event edit page
		function frontend_box($object, $helpers){
			ob_start();

			if(!isset($object->msg)) return false;

			$type = !empty($object->type) ? $object->type : null;

			?>
			<div class='evo_metarow_evoad evorow bordb evcal_evdata_row <?php echo $type;?>'>
				<p class='evoad_row_content' style='margin:0'><?php echo $object->msg;?></p>
			</div>
			<?php
			return ob_get_clean();
		}

		// include evoad in event card fields
		function eventcard_adds($array){
			$array[] = 'evoad';
			return $array;
		}


	// EventTop
		public function eventtop_class_names($_eventClasses, $EVENT, $object ){
			if(!$EVENT->check_yn('_evo_advent_event')  ) return $_eventClasses;
			if(!$EVENT->is_event_live_now( )  ) return $_eventClasses;

			$_eventClasses[] = 'evoad_active_now';
			return $_eventClasses;
		}
		public function above_title($content, $O, $EVENT){
			if(!$EVENT->check_yn('_evo_advent_event')  ) return $content;
			if(!$EVENT->is_event_live_now( )  ) return $content;

			return "<span class='evo_event_headers canceled movedonline'>". evo_lang('Active Now') ."</span>" . $content;

		}
		public function eventtop_styles($styles, $cal, $EVENT){
				
			// checks
			if(!$EVENT->check_yn('_evo_advent_event')  ) return $styles;
			if(!$EVENT->is_current_event( 'start')  ) return $styles;

			//unset($styles['background-image']);

			return $styles;
		}

		function eventtop($array, $pmv, $pass_vals, $EVENT){
			
			if($EVENT->check_yn('_evo_advent_event')  ){

				if($EVENT->is_current_event( 'start')){

					$hidden_fields = EVO()->cal->get_prop('evoad_hidden_fields','evcal_ad');

					if( !is_array($hidden_fields)) return $array;

					if(in_array('ftimage', $hidden_fields)){
						unset($array['ft_img']);
					}

					if(in_array('timelocation', $hidden_fields)){
						foreach(array(
							'locationaddress',
							'location',
							'locationname',
							'html'
						) as $field){
							if(isset($array['belowtitle'][$field]))	unset($array['belowtitle'][$field]);
						}
					}

					if(in_array('organizer', $hidden_fields)){
						unset($array['belowtitle']['organizer_name']);
					}
				}
			}


			return $array;
		}

}