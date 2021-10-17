<?php
/**
 * eventon rsvp front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-rsvp/classes
 * @version     2.6.3
 */
class evors_front{

	/* classification
	RR - rsvp cpt
	RSVP - rsvp extension of event obj
	*/
	public $rsvp_array = array('y'=>'yes','m'=>'maybe','n'=>'no');
	public $rsvp_array_ = array('y'=>'Yes','m'=>'Maybe','n'=>'No');
	public $evors_args;
	public $optRS;
	public $addFields;
	public $showRSVPform = false;

	public $t = 66;

	public $RSVP;
	public $event_id;
	public $oneRSVP = false;

	public $rsvp_option_count = 0;

	public $currentlang;

	function __construct(){
		add_action('evo_load_event',array($this,'load_rsvp_event'),10,1);

		$this->evoopt1 = EVO()->cal->get_op('evcal_1');
		$this->optRS = EVORS()->evors_opt;
		$this->opt2 = EVORS()->opt2;

		$this->addFields = apply_filters('evors_field_count',5);
		add_action('evo_addon_styles', array($this, 'styles') );

		include_once('class-functions.php');
		$this->functions = new evorsvp_functions();

		add_filter('eventon_eventCard_evorsvp', array($this, 'frontend_box'), 10, 3);
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);

		// event top inclusion
		add_filter('eventon_eventtop_one', array($this, 'eventop'), 10, 3);
		add_filter('evo_eventtop_adds', array($this, 'eventtop_adds'), 10, 1);
		add_filter('eventon_eventtop_evors', array($this, 'eventtop_content'), 10, 3);
		//add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 10 );

		// scripts and styles
			add_action( 'evo_register_other_styles_scripts', array( $this, 'register_styles_scripts' ) ,15);
			add_action( 'eventon_enqueue_styles', array($this,'print_styles' ));
			add_action( 'eventon_enqueue_scripts', array($this,'print_scripts' ));
		
		add_filter('evo_frontend_lightbox', array($this, 'lightbox'),10,1);

		// event top above title
		add_filter('eventon_eventtop_abovetitle', array($this,'eventtop_above_title'),10, 3);
		add_filter('evo_cal_eventtop_attrs', array($this,'event_attrs'),10, 1);

		//print_r( get_post_meta(1,'aaa'));

		include_once('class-intergration-virtual.php');

	}

	// Initially load rsvp object for the event
		function load_rsvp_event($E){
			$this->RSVP = $R= new EVORS_Event($E, $E->ri);		
			$this->event_id = $this->RSVP->event->ID;

			// if this event have current user RSVPed
			$RSVP_id = $this->RSVP->get_rsvp_id();

			$this->oneRSVP = $RR = false;
			if($RSVP_id){
				$this->oneRSVP = $RR = new EVO_RSVP_CPT($RSVP_id);
			}


			do_action('evors_load_event',$R, $RR);
		}	

	// STYLES: for the tab page
		public function register_styles_scripts(){
			global $eventon_rs;

			if(is_admin()) return false;

			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$this->evoopt1, true))
				wp_register_style( 'evo_RS_styles',$eventon_rs->assets_path.'RS_styles.css', '', $eventon_rs->version);

			wp_register_script('evo_RS_script',$eventon_rs->assets_path.'RS_script.js',
				array('jquery','jquery-ui-core'),
				$eventon_rs->version, true );

			wp_localize_script(
				'evo_RS_script',
				'evors_ajax_script',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ) ,
					'postnonce' => wp_create_nonce( 'evors_nonce' )
				)
			);
		}
		public function print_scripts(){
			wp_enqueue_script('evo_RS_ease');
			//wp_enqueue_script('evo_RS_mobile');
			wp_enqueue_script('jquery-form');
			wp_enqueue_script('evo_RS_script');
		}
		function print_styles(){	wp_enqueue_style( 'evo_RS_styles');		}
		function styles(){
			global $eventon_rs;
			ob_start();
			include_once($eventon_rs->plugin_path.'/assets/RS_styles.css');
			echo ob_get_clean();
		}

	// EVENTTOP inclusion
		public function eventop($array, $pmv, $vals){
			$array['evors'] = array(	'vals'=>$vals,	);
			return $array;
		}
		function eventtop_adds($array){
			$array[] = 'evors';
			return $array;
		}
		public function eventtop_content($object, $helpers, $EVENT){
			global $eventon;

			$RSVP = $this->RSVP;

			// check if rsvp enabled
			if(!$RSVP->is_rsvp_active()) return false;

			// Hide event top content if set via pluggable filters
			$show_eventtop_content = apply_filters('evors_eventtop_show_content',true, $RSVP);
			if(!$show_eventtop_content) return false;
			
			$this->print_scripts();
			$output = '';

			// Initial Values
				$emeta = get_post_custom($this->event_id);

			// check if RSVP info is ok to show
				if(!$RSVP->can_rsvp() || $RSVP->close_rsvp_beforex() ) return;


			$lang = $this->get_local_lang();
			$this->currentlang = $lang;
			$opt = $this->opt2;

			// logged-in user RSVPing with one click
			$output .= $this->get_eventtop_your_rsvp();

			// get the eventtop data values
			$output .= $this->get_eventtop_data();

			//construct HTML
			if(!empty($output)){
				$output = "<span class='evcal_desc3_rsvp'>".$output."</span>";
			}

			return $output;

		}

		// GET the event top data values
		function get_eventtop_your_rsvp(){
			$RSVP = $this->RSVP;
			$existing_rsvp_status = false;
			$output = '';
			if(evo_settings_check_yn($this->optRS, 'evors_eventop_rsvp')){


				// if users can rsvp to this event
				if( $RSVP->can_user_rsvp() ){
					
					$this_user_id = $RSVP->get_current_user_id();
					$this_user_id = !$this_user_id? 'na':$this_user_id;

					$user_rsvp_status = false;
					if($this->oneRSVP){
						$user_rsvp_status = apply_filters('evors_user_existing_rsvp_status',$this->oneRSVP->get_rsvp_status());
					}		


					// Initial values
						$closeRSVPbeforeX = $RSVP->close_rsvp_beforex();
						$can_still_rsvp = $RSVP->can_rsvp();
						$lang = $this->get_local_lang();


					if($RSVP->has_space_to_rsvp()){

						// if loggedin user have not rsvp-ed yet
						if(!$user_rsvp_status && !$closeRSVPbeforeX && $can_still_rsvp){
							$TEXT = eventon_get_custom_language($this->opt2, 'evoRSL_001','RSVP Now', $lang);
							$output .=  "<span class='evors_rsvpiable' data-eid='{$RSVP->event->ID}' data-ri='{$RSVP->ri}'data-uid='{$this_user_id}' data-lang='{$lang}'>". $this->get_rsvp_choices($this->opt2, $this->optRS).'<b >'.$TEXT."</b ></span>";
						}else{
						// user has rsvp-ed already
							$TEXT = evo_lang('You have already RSVP-ed', $lang);
							$output .="<span class='evors_rsvpiable'>{$TEXT}: <em class='evors_rsvped_status_user'>".$this->get_rsvp_status($user_rsvp_status)."</em></span>";
						}
					}
				}
			}
			return $output;
		}

		function get_eventtop_data(){

			// initial values
			$lang = $this->get_local_lang();
			$opt = $this->opt2;
			$output = '';

			// show attending count
				$attending_html = '';
				if(evo_settings_check_yn($this->optRS, 'evors_eventop_attend_count')){

					// correct language text for based on count coming to event
						$lang_str =  array(
							'0'=>'Be the first to RSVP',
							'1'=>'Guest is attending',
							'2'=>'Guests are attending',
						);

					//if the current user have rsvped and it is "maybe"
					if( $this->oneRSVP && $this->oneRSVP->get_rsvp_status() == 'm'){
						$attending_html = '';
					}else{
						$yes_count = $this->RSVP->get_rsvp_count('y');

						// correct language string
						$__count_lang = evo_lang($lang_str['0'], $lang);
						if( $yes_count == 1) $__count_lang = evo_lang($lang_str['1'], $lang);
						if( $yes_count > 1 ) $__count_lang = evo_lang($lang_str['2'], $lang);


						$attending_html .= "<span class='evors_eventtop_data count_$yes_count attending'>".($yes_count>0? '<em>'.$yes_count.'</em> ':'').$__count_lang."</span>";
					}
					
				}

				// show not attending count
				if(evo_settings_check_yn($this->optRS, 'evors_eventop_notattend_count')){
					// correct language text for based on count coming to event
						$lang_str = array(
							'1'=>'Guest is not attending',
							'2'=>'Guests are not attending',
						);

					$no_count = $this->RSVP->get_rsvp_count('n');

					if($no_count >0){

						if($no_count == 1) $__count_lang = evo_lang($lang_str['1'], $lang);
						if($no_count > 1) $__count_lang = evo_lang($lang_str['2'], $lang);

						$attending_html .= "<span class='evors_eventtop_data count_$no_count notattending'><em>".$no_count.'</em> '.$__count_lang."</span>";
					}

				}

			// show remainging count
				$count_html = '';

				if(evo_settings_check_yn($this->optRS,'evors_eventop_remaining_count') && $this->RSVP->can_user_rsvp()){
					// /print_r($object);
					$remaining_rsvp = $this->RSVP->remaining_rsvp();

					if($remaining_rsvp =='0'){
						$count_html .= "<span class='evors_eventtop_data remaining_count'>".evo_lang_get( 'evoRSL_002c','No more spots left!', $lang, $opt)."</span>";
					// no capacity set
					}elseif($remaining_rsvp == 'nocap'){
						$count_html .= "<span class='evors_eventtop_data remaining_count'>".evo_lang_get( 'evoRSL_002bb','Spaces Still Available', $lang, $opt)."</span>";
					}else{
						$count_html .= "<span class='evors_eventtop_data remaining_count'><em>".($remaining_rsvp>0? $remaining_rsvp.' ': evo_lang('na') ).'</em>'.evo_lang_get('evoRSL_002b','Spots remaining', $lang, $opt)."</span>";
					}
				}

				$count_html = apply_filters('evors_eventtop_count_html', $count_html, $this);

				if(!empty($attending_html) || !empty($count_html) )
					$output = '<span class="evors_eventtop_section_data'.(empty($attending_html)?' sinval':'').'">'.$attending_html.$count_html .'</span>';
				return $output;
		}

		// ABOVE title - event over tag
			function eventtop_above_title($var, $object, $EVENT){
				$epmv = $object->evvals;

				$RSVP = $this->RSVP;

				// dismiss if set in ticket settings not to show sold out tag on eventtop
				if(!empty($this->optRS['evors_eventop_soldout_hide']) && $this->optRS['evors_eventop_soldout_hide']=='yes') return $var;

				// Initial Check
				if(!$this->RSVP->is_rsvp_active()) return $var;
				
				$output = $var;

				// if the event is over
				if(!$RSVP->can_rsvp() ){
					if( $EVENT->is_past_event()) 
						$output = $var."<span class='eventover'>".evo_lang('Event Over', '',$this->opt2)."</span>";
				// if there are no more spaces left to rsvp
				}elseif(!$RSVP->has_space_to_rsvp()){
					$output = $var."<span class='eventover nomore_spaces'>".evo_lang('No more spaces left', '',$this->opt2)."</span>";
				}

				return apply_filters('evors_eventtop_above_title', $output,$var, $EVENT);
			}

		// event attrs
			public function event_attrs($attr){
				
				if(!$this->RSVP->is_rsvp_active()) return $attr;
				if(!$this->RSVP->can_rsvp() ){
					$attr['class'] = $attr['class'].' event_over';
				}

				return $attr;
			}

	// RSVP EVENTCARD form HTML
		// add RSVP box to front end
			function frontend_box($object, $helpers, $EVENT){
				global $eventon_rs;

				$EV = $RSVP = $this->RSVP;

				// check if RSVP is ok to show
					if(!$EV->is_rsvp_active()) return;

				// INITIAL VALUES
					

					$event_pmv 	= $EV->event->get_data();
					$optRS 		= $this->optRS;
					$opt 		= $this->opt2;
					$is_user_logged_in = is_user_logged_in();

					// set language					
						$lang = $this->get_local_lang();
						EVORS()->l = $lang;
					
					// if user has RSVP get rsvp id
						$oneRSVP = $RSVP_id = $RR = false;

						// loggedin user have rsvped - because rsvp id return
						if($this->oneRSVP){
							$oneRSVP = $RR = $this->oneRSVP;
							$RSVP_id = $RR->rsvp_id;
						} 

					// Eventcard first load for RSVP
						do_action('evors_evc_first_load', $RSVP, $RR);

					// event end time
						$unixTime = $EV->event->get_start_end_times();
						$row_endTime = $unixTime['end'];

					


				// if only loggedin users can see rsvp form
					if( EVO()->cal->check_yn('evors_onlylogu','evcal_rs') && !$is_user_logged_in ||
						!$EV->user_need_login_to_rsvp()
					){
						return $this->rsvp_for_none_loggedin($helpers, $object, $EVENT);
						return;	// not proceeding forward from here

					}elseif(EVO()->cal->check_yn('evors_onlylogu','evcal_rs')){

					// if user is loggedin
						if(!$EV->can_user_rsvp() ){
							return $this->rsvp_not_for_userrole($helpers, $object);
							return;
						}
					}

				

				// FILTER
					$show_eventcard = apply_filters('evors_eventcard_before_rsvp', true, $RSVP, $EVENT);
					if(!$show_eventcard) return;

				// per rsvp capacity set - check
					$precapVal = $RSVP->is_per_rsvp_max_set();

				// get options array					
					$fields_options = 	EVO()->cal->check_yn('evors_ffields','evcal_rs');
			

				$this->print_scripts();
				ob_start();


					echo  "<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_rsvp".$helpers['end_row_class']."' >";

						// JSON data for the event
						$JSON_data = $this->event_rsvp_data(
							$RSVP, 
							array(
								'rsvpid'=>$RSVP_id,
								'rsvp'=> ($RR? $RR->get_rsvp_status():null )								
							)
						);
						echo "<div class='evors_jdata' data-j='". $JSON_data."'></div>";
						
						echo "<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evors_001', 'fa-envelope',$helpers['evOPT'] )."'></i></span>
							<div class='evcal_evdata_cell'>
								<h3 class='evo_h3'>".eventon_get_custom_language($opt, 'evoRSL_001','RSVP Now')."</h3>";

						// subtext for rsvp section
						$subtext = '';
						if( !$EV->can_user_rsvp() ){
							$subtext = eventon_get_custom_language($opt, 'evoRSL_002d',"RSVPing is closed at this time.");
						}else{
							// current user has not RSVPed
							$show_subtitle = apply_filters('evors_eventcard_show_subtitle',( $RR? false:true),$RSVP);

							if( $show_subtitle )	
								$subtext = eventon_get_custom_language($opt, 'evoRSL_002','Make sure to RSVP to this amazing event!');
						}

						// subtitle text
						if(!empty($subtext))	echo "<div class='evors_section evors_subtext'><p class='evo_data_val'>".$subtext."</p></div>";				

						// Under subtitle plug
						do_action('evors_eventcard_after_subtitle', $RSVP, $RR);


						echo "<div class='evors_eventcard_content'>";

						// Event Card content
						$show_eventcard_rsvp_content = apply_filters('evors_eventcard_content_show',true, $RR,$RSVP, $EVENT);	
						if(  $show_eventcard_rsvp_content !== false):

						 	echo $this->_get_event_card_content($RSVP, $RR);							

						else: 
							do_action('evors_eventcard_notshow_content', $RSVP, $EVENT);
						endif; // show_eventcard_rsvp_content
						
						echo "</div>";

						echo "</div>".$helpers['end'];
						echo "</div>";

				return ob_get_clean();
			}

			// Event Card Content
				function _get_event_card_content($RSVP, $RR){

					// whether current user have rsvped
					$current_user_rsvped = $RR? $RR->get_rsvp_status(): false;

					$can_newuser_rsvp = $RSVP->can_user_rsvp();

					ob_start();

					// user rsvp based text
						$_user_txt = $RR ? ( $RR->get_rsvp_status()=='n'? 
								evo_lang('Sorry to hear you can not make it to the event.'):
								evo_lang('We look forward to seeing you at the event!')): 
							evo_lang('Please let us know if you can make it to the event.');

					
					// there are RSVP spots remaining OR user loggedin
						if( $can_newuser_rsvp || (!$can_newuser_rsvp && $current_user_rsvped)){	
							if( $RSVP->event->is_future_event() )
								echo "<p class='evors_section evors_user_text'><span>". apply_filters('evors_evc_user_rsvp_txt', $_user_txt,$RSVP, $RR) ."</span></p>";

							echo "<div class='evoRS_status_option_selection'>";
							echo $this->_get_evc_html_rsvpoption($RR, $RSVP);							
							echo "</div>";
						}

					?>
					<div class="evors_incard_form"></div>
					<?php

					echo "<div class='evors_information'>";
					echo $this->get_eventcard_rsvp_html($RSVP,$RR);
					echo "</div>";


					// change RSVP status section								
					if( !EVO()->cal->check_yn('evors_hide_change') && apply_filters('evors_eventcard_change_rsvp', $RSVP->show_change_rsvp($current_user_rsvped), $RSVP, $RR) ){

						$proceed = ( $RR && !$RR->get_rsvp_status())? false: true;

						if($proceed){
							// change rsvp button
							$_txt_changersvp = EVORS()->lang('evoRSL_005a','Change my RSVP');									

							$user_id = (is_user_logged_in())? $current_user_rsvped:'na';
						
							echo "<div class='evors_section evors_change_rsvp'>
								<p class=''>".
									'<span class="evors_change_rsvp_label">'.evo_lang_get('evoRSL_002a2','Can not make it to this event?') . '</span>'
									."<span class='change evors_change_rsvp_trig' data-rsvpid='". ($RR? $RR->ID:'')."' data-val='".($current_user_rsvped?'chu':'ch')."'>".$_txt_changersvp."</span>
								</p></div>";
						}
					}

					// additional information to rsvped logged in user
						if($RSVP->event->get_prop('evors_additional_data') && $current_user_rsvped){
							$lang = $this->get_local_lang();

							echo "<div class='evors_additional_data'>";
							echo "<h3 class='evo_h3 additional_info'>".evo_lang('Additional Information', $lang)."</h3>";
							echo "<p class='evo_data_val'>".$RSVP->event->get_prop('evors_additional_data')."</p>";
							echo "</div>";
						}

					do_action('evors_eventcard_end_rsvp',$RSVP, $RR );

					return ob_get_clean();
				}

			// get all the data values pertaining to event
				function event_rsvp_data($EV, $other_data=''){

					// pre calculations
					$remaining_rsvp = 	$EV->remaining_rsvp();
					$precapVal = 		$EV->is_per_rsvp_max_set();
					$currentUserID = 	$EV->get_current_user_id();
					$lang = $this->get_local_lang();

					$data_array = array();
					$data_array['etitle'] = htmlspecialchars( get_the_title($EV->event->ID) );
					$data_array['e_id'] = $EV->event->ID;
					$data_array['repeat_interval'] = $EV->ri;
					$data_array['cap'] = $remaining_rsvp;
					$data_array['precap'] = !$precapVal?'na':$precapVal; // capacity per each rsvp
					$data_array['uid'] = ($currentUserID=='0')? 'na': $currentUserID;
					$data_array['prefill'] = $currentUserID;
					$data_array['lang'] = $lang;
					$data_array['incard'] =  ($EV->inCard_form()?'yes':'no');
					
					if(!empty($other_data) && is_array($other_data)) $data_array = array_merge($data_array, $other_data);

					if(empty($data_array['rsvpid'])) $data_array['rsvpid'] = null;

					return json_encode( apply_filters('evors_eventcard_selection_data_array',$data_array,$EV));
				}

			// for not loggedin users
				function rsvp_for_none_loggedin($helpers, $object, $EVENT){
					$lang = (!empty($eventon->evo_generator->shortcode_args['lang'])? EVO()->evo_generator->shortcode_args['lang']:'L1');
					
					ob_start();
					echo  "<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_rsvp".$helpers['end_row_class']."' data-rsvp='' data-event_id='".$object->event_id."'>
								<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evors_001', 'fa-envelope',$helpers['evOPT'] )."'></i></span>
								<div class='evcal_evdata_cell'>
									<h3 class='evo_h3'>".eventon_get_custom_language($helpers['evoOPT2'], 'evoRSL_001','RSVP Now')."</h3>";

							$txt_1 = evo_lang('You must login to RSVP for this event',$lang, $helpers['evoOPT2']);
							$txt_2 = evo_lang('Login Now',$lang, $helpers['evoOPT2']);
							echo "<p>{$txt_1} ";

							$login_link = wp_login_url( $EVENT->get_permalink() );

							// check if custom login lin kprovided
								if(!empty($this->evoopt1['evo_login_link']))
									$login_link = $this->evoopt1['evo_login_link'];

							echo apply_filters('evo_login_button',"<a class='evors_loginnow_btn evcal_btn' href='".$login_link ."'>{$txt_2}</a>", $login_link, $txt_2);
							echo "</p>";
					echo "</div></div>";
					return ob_get_clean();
				}

			// Do not have permission to RSVP
				function rsvp_not_for_userrole($helpers, $object){
					global $eventon;
					$lang = (!empty($eventon->evo_generator->shortcode_args['lang'])? $eventon->evo_generator->shortcode_args['lang']:'L1');
					ob_start();
					echo  "<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_rsvp".$helpers['end_row_class']."' data-rsvp='' data-event_id='".$object->event_id."'>
								<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evors_001', 'fa-envelope',$helpers['evOPT'] )."'></i></span>
								<div class='evcal_evdata_cell'>
									<h3 class='evo_h3'>".eventon_get_custom_language($helpers['evoOPT2'], 'evoRSL_001','RSVP Now')."</h3>";

							$txt_1 = evo_lang('You do not have permission to RSVP to this event!',$lang, $helpers['evoOPT2']);

							echo "<p>{$txt_1}  ";
							echo "</p>";

					echo "</div></div>";
					return ob_get_clean();
				}

		// RSVP options selection HTML
			function _get_evc_html_rsvpoption($RR, $RSVP){

				ob_start();

				// if User already RSVPED
				if( $RR && $RR->get_rsvp_status()  ){
					$_uid = $RSVP->get_current_user_id();
					
					echo "<p class='nobrbr loggedinuser evors_evc_rsvpchoice' data-uid='{$_uid}' data-eid='{$RSVP->event->ID}' data-ri='{$RSVP->event->ri}'><i class='fa fa-check evors_checkmark'></i>";
					echo '<em class="evors_evc_rsvpchoice_txt">'. evo_lang('You have already RSVP-ed')."</em> <em class='evors_rsvped_status_user status_".$RR->get_rsvp_status()."'>".$this->get_rsvp_status( $RR->get_rsvp_status() )."</em> ";
					echo "</p>";

				// user havent rsvped yet
				}elseif(!$RSVP->close_rsvp_beforex() && $RSVP->can_rsvp()){

					// count for rsvp options
					$countARR = array();
					if( $RSVP->show_rsvp_count() ){
						$countARR = array(
							'y' => $RSVP->get_rsvp_count('y'),
							'n' => $RSVP->get_rsvp_count('n'),
							'm' => $RSVP->get_rsvp_count('m'),
						);
					}

					$content = $this->get_rsvp_choices('', '', $countARR);
					echo "<p class='".($this->rsvp_option_count==1?'sin':'')."'>". $content ."</p>";
				}

				return ob_get_clean();
			}

		// RSVP details for eventCard
			function get_eventcard_rsvp_html($RSVP, $RR){
				
				$opt = $this->opt2;

				$pmv = $RSVP->event->get_data();

				$lang = $this->get_local_lang();

				$currentUserRSVP = ($RR && $RR->get_rsvp_status())? true: false;
				$remaining_rsvp = 	$RSVP->remaining_rsvp();
				$ri = $RSVP->ri;

				$unixTime = $this->get_correct_eventTime($pmv, $RSVP->ri);
				$row_endTime = $unixTime['end'];

				$closeRSVPbeforeX = $RSVP->close_rsvp_beforex();
				$can_still_rsvp = $RSVP->can_user_rsvp();
				$show_remainingrsvp_onCard = evo_check_yn($pmv, 'evors_capacity_show');


				ob_start();

				// spots remaining
					$spots_remaining_HTML = '';
					$_show_remaining_rsvp_section = apply_filters('evors_eventcard_show_remaining_rsvp_section',true,$RSVP);
					
					if(!$closeRSVPbeforeX && $can_still_rsvp && $_show_remaining_rsvp_section ){
						$spots_remaining_HTML .= "<div class='evors_section evors_remaining_spots'>";
						if($remaining_rsvp == '0'){
							$spots_remaining_HTML .= "<p class='remaining_count no_spots_left'><em class='nospace'>".evo_lang('Filled')."</em>".evo_lang_get( 'evoRSL_002c','No more spots left!', $lang, $opt)."</p>";
						}elseif($remaining_rsvp == 'nocap' ){
							$spots_remaining_HTML .= "<p class='remaining_count'><em class='space'>".evo_lang('Open')."</em>".evo_lang_get( 'evoRSL_002bb','Spaces Still Available', $lang, $opt)."</p>";
						}else{
							if($show_remainingrsvp_onCard)
								$spots_remaining_HTML .= "<p class='remaining_count'><em>". $remaining_rsvp  ."</em> ".evo_lang_get('evoRSL_002b','Spots remaining', $lang, $opt)."</p>";
						}
						$spots_remaining_HTML .= "</div>";

						$spots_remaining_HTML = apply_filters('evors_eventcard_html_srem', $spots_remaining_HTML,$RSVP, $RR);
					}

				// minimum capacity event happening
					$min_needed_HTML = '';
					if(!empty($pmv['evors_min_cap']) && $pmv['evors_min_cap'][0]=='yes' && !empty($pmv['evors_min_count']) ){
						$output = '';
						$minCap = (int)$pmv['evors_min_count'][0];
						$coming = $RSVP->get_rsvp_count('y');
						if($coming>=$minCap){
							$output = evo_lang('Event is happening for certain');
						}else{
							$need = $minCap - $coming;
							$output = '<em>'.$need.'<i>'.evo_lang('rsvps').'</i></em>';
							$output .= str_replace('-count-', '', evo_lang('Needed for the event to happen') );
						}
						if(!empty($output)){
							$min_needed_HTML = "<div class='evors_section evors_mincap ".(empty($spots_remaining_HTML)?'nosr ':''). ($coming>=$minCap? 'happening':'nothappening')."'><p class='evo_data_val'>".$output."</p></div>";
						}
					}

					if(!empty($spots_remaining_HTML) || $min_needed_HTML){
						echo "<div class='evors_stat_data'>";
						echo $spots_remaining_HTML.$min_needed_HTML;
						echo "<div class='clear'></div></div>";
					}

				// Guest List
					if($RSVP->show_whoscoming()){

						// check if only rsvped users can see guest list
						if( $RSVP->can_show_guestList( $currentUserRSVP)){

							$attendee_icons = $this->GET_attendees_icons($RSVP, $ri);
							if($attendee_icons){
								echo "<div class='evors_section evors_guests_list'>";
								echo "<p class='evors_whos_coming_title whoscoming'>".evo_lang_get('evoRSL_002a','Guests List', $lang, $opt).' <em>'.evo_lang_get('evoRSL_002a1','Attending', $lang, $opt).' <i>'.$RSVP->get_rsvp_count('y')."</i></em></p>
									<p class='evors_whos_coming'><em class='tooltip'></em>". $attendee_icons."</p>";
								echo "</div>";
							}
						}
					}

				// List of people not coming
					if($RSVP->show_whosnotcoming()){
						// check if only rsvped users can see guest list
						if( $RSVP->can_show_notcomingList( $currentUserRSVP)){

							$attendee_icons = $this->GET_attendees_icons($RSVP, $ri, 'n');
							if($attendee_icons){
								echo "<div class='evors_section evors_guests_list evors_notcoming_list'>";
								echo "<p class='evors_whos_coming_title whosnotcoming'>".evo_lang('List of guests not attending to this event', $lang, $opt).' <em>'.evo_lang('Not Attending', $lang, $opt).' <i>'.$RSVP->get_rsvp_count('n')."</i></em></p>
									<p class='evors_whos_coming'><em class='tooltip'></em>". $attendee_icons."</p>";
								echo "</div>";
							}
						}
					}

				return ob_get_clean();
			}
		
		// save a cookie for RSVP
			function set_user_cookie($args){

				$cookie_name = 'evors_'.$args['email'].'_'.$args['e_id'].'_'.$args['repeat_interval'];
				$cookie_value = 'rsvped_'. $args['rsvp'];
				setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
			}
			function check_user_cookie($userid, $eventid){
				$cookie_name = 'evors_'.$eventid.'_'.$userid;
				if(!empty($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]=='rsvped'){
					return true;
				}else{
					return false;
				}
			}
		// get form messages html
			function get_form_message($code='', $lang=''){
				$lang = empty($lang) ? $this->get_local_lang() : $lang;
				$opt = $this->opt2;
				$array =  apply_filters('evors_form_messages', array(
					'err'=>eventon_get_custom_language($opt, 'evoRSL_013','Required fields missing',$lang),
					'err2'=>eventon_get_custom_language($opt, 'evoRSL_014','Invalid email address',$lang),
					'err3'=>eventon_get_custom_language($opt, 'evoRSL_015','Please select RSVP option',$lang),
					'err4'=>eventon_get_custom_language($opt, 'evoRSL_016','Could not update RSVP, please contact us.',$lang),
					'err5'=>eventon_get_custom_language($opt, 'evoRSL_017','Could not find RSVP, please try again.',$lang),
					'err6'=>eventon_get_custom_language($opt, 'evoRSL_017x','Invalid Validation code.',$lang),
					'err7'=>eventon_get_custom_language($opt, 'evoRSL_017y','Could not create a RSVP please try later.',$lang),
					'err8'=>eventon_get_custom_language($opt, 'evoRSL_017z1','You can only RSVP once for this event.',$lang),
					'err9'=>eventon_get_custom_language($opt, 'evoRSL_017z2','Your party size exceed available space.',$lang),
					'err10'=>eventon_get_custom_language($opt, 'evoRSL_017z3','Your party size exceed allowed space per RSVP.',$lang),
					'err11'=> eventon_get_custom_language($opt, 'evoRSL_017z4','There are no spaces available to RSVP.',$lang),
					'succ'=>eventon_get_custom_language($opt, 'evoRSL_018','Thank you for submitting your rsvp',$lang),
					'succ_n'=>eventon_get_custom_language($opt, 'evoRSL_019','Sorry to hear you are not going to make it to our event.',$lang),
					'succ_m'=>eventon_get_custom_language($opt, 'evoRSL_020','Thank you for updating your rsvp',$lang),
					'succ_c'=>eventon_get_custom_language($opt, 'evoRSL_021','Great! we found your RSVP!',$lang),
				));
				return (!empty($code))? $array[$code]: $array;
			}
			function get_form_msg($lang){
				$str='';
				$ar = array('codes'=> $this->get_form_message('', $lang) );
				return "<div class='evors_msg_' style='display:none' data-j='". json_encode($ar)."'></div>";
			}
		// GET attendees icons
			function GET_attendees_icons($EVENT, $ri, $list_type='y'){
				
				$list = $EVENT->GET_rsvp_list();
				
				$output = array();

				$guestListInitials = (!empty($this->optRS['evors_guestlist']) && $this->optRS['evors_guestlist']!='fn')? true: false;

				//$LINKGUEST = (!empty($this->optRS['evors_guest_link']) && $this->optRS['evors_guest_link'] == 'yes')? true: false;

				$LINKGUEST = evo_settings_check_yn($this->optRS, 'evors_guest_link');
				$LINKstructure = !empty($this->optRS['evors_profile_link_structure'])?$this->optRS['evors_profile_link_structure']:false ;
				$site_url = get_site_url();

				if(!empty($list[ $list_type ])){
					
					foreach($list[ $list_type ] as $field=>$value){
						//$gravatar_link = 'http://www.gravatar.com/avatar/' . md5($value['email']) . '?s=32';

						$LINK = 'na';
						$initials = ($guestListInitials)?
							mb_substr($value['fname'], 0, 1).mb_substr($value['lname'], 0, 1):
							$value['fname'].' '.$value['lname'];
						$spaces = $value['count'];

						if(empty($initials)) continue;

						// link to profile - if custom link structure is given use that instead of buddypress link
							if($LINKGUEST && $value['userid'] != 'na' && !empty($value['userid'])){

								if($LINKstructure){
									$user_info = get_userdata( $value['userid'] );
									$link_append = str_replace('{user_id}', $value['userid'], $LINKstructure);
									
									// user nicename
									if($user_info->user_nicename && strpos($link_append, 'user_nicename') !== false){
										$link_append = str_replace('{user_nicename}', $user_info->user_nicename, $link_append);
									}
									
									$LINK = $site_url . $link_append;
								}elseif(function_exists('bp_core_get_user_domain')){
									$LINK = bp_core_get_user_domain( (int)$value['userid'] );
								}
							}

						$output[$value['email']] = apply_filters('evors_guestlist_guest',"<span class='".($guestListInitials? 'initials':'fullname')."' data-name='{$value['fname']} {$value['lname']}' data-link='{$LINK}' data-uid='". (!empty($value['userid'])? $value['userid']:'-')."'>{$initials}". ($spaces>1? '<i>+'.($spaces-1).'</i>':'' )."</span>",
							$value
						);
					}
				}

				if(count($output)<1) return false;

				return implode('', $output);
			}
		// GET rsvp status selection HTML
			function get_rsvp_choices($opt2, $optRS='', $countARR='', $setchoice='', $formtype=''){
				if(empty($optRS)) $optRS = $this->optRS;
				if(empty($opt2)) $opt2 = $this->opt2;
				$selection = (!empty($optRS['evors_selection']))? $optRS['evors_selection']: true;
				$selOpt = array(
					'y'=>array('Yes', 'evoRSL_003'),
					'n'=>array('No', 'evoRSL_005'),
					'm'=>array('Maybe', 'evoRSL_004'),
				);

				$content ='';
				$lang = $this->get_local_lang();

				//if(!is_array($selection)) return false;
				//print_r($countARR);
				
				$rsvp_option_count = 0;
				foreach($selOpt as $field=>$value){

					if(is_array($selection) &&  in_array($field, $selection) || $field=='y' || ($field=='n' && !empty($formtype) && $formtype!='submit')
					){
						$selCount = (!is_array($selection))? 'one ': '';

						// get count
						$count = (!empty($countARR) && !empty($countARR[$field]) )? ' ('.$countARR[$field] .')': null;
						
						$setChoice = (!empty($setchoice) && $setchoice==$field)?'set':'';

						$content .= "<span data-val='{$field}' class='evors_choices {$selCount}{$setChoice}'>".eventon_get_custom_language($opt2, $value[1],$value[0], $lang).$count."</span>";
						$rsvp_option_count++;
					}
				}

				$this->rsvp_option_count = $rsvp_option_count;
				return $content;
			}
		// add eventon rsvp event card field to filter
			function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
				$array['evorsvp']= array(
					'event_id' => $eventid,
					'value'=>'tt',
					'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
				);
				return $array;
			}
			function eventcard_adds($array){
				$array[] = 'evorsvp';
				return $array;
			}
		

	// RETURN corected event end time for repeat interval
		function get_correct_event_end_time($e_pmv, $__repeatInterval){
			$datetime = new evo_datetime();
			return $datetime->get_int_correct_event_time($e_pmv, $__repeatInterval, 'end');
	    }
	    function get_correct_eventTime($e_pmv, $__repeatInterval){
			$datetime = new evo_datetime();
			return $datetime->get_correct_event_repeat_time($e_pmv, $__repeatInterval);
	    }
	    function get_adjusted_event_formatted_times($e_pmv, $repeat_interval=''){
	    	$datetime = new evo_datetime();
	    	return $datetime->get_correct_formatted_event_repeat_time($e_pmv,$repeat_interval );
	    }

	// SUPPORT functions
	    // EventON lightbox Call
			function lightbox($array){
				$array['evors_lightbox']= array(
					'id'=>'evors_lightbox',
					'CLclosebtn'=> 'evors_lightbox',
					'CLin'=> 'evors_lightbox_body',
				);
				return $array;
			}
		// RETURN: language
			function lang($variable, $default_text, $lang=''){
				global $eventon_rs;
				return $eventon_rs->lang($variable, $default_text, $lang);
			}
			function get_local_lang(){

				$lang = EVORS()->l;

				if(!empty($this->currentlang)) return $this->currentlang;

				if( !empty(EVO()->evo_generator->shortcode_args['lang']))
					$lang = EVO()->evo_generator->shortcode_args['lang'];

				return $lang;
			}

		// function replace event name from string
			function replace_en($string, $eventTitle=''){
				return (empty($eventTitle))?
					str_replace('[event-name]', "<span class='eventName'>Event Name</span>", $string):
					str_replace('[event-name]', $eventTitle, $string);
			}
		// get proper rsvp status name I18N
			public function get_checkin_status($status, $lang='', $evopt=''){
				$evopt = $this->opt2;
				$lang = (!empty($lang))? $lang : 'L1';

				if($status=='check-in'){
					return (!empty($evopt[$lang]['evoRSL_003x']))? $evopt[$lang]['evoRSL_003x']: 'check-in';
				}
				if($status=='checked'){
					return (!empty($evopt[$lang]['evoRSL_003y']))? $evopt[$lang]['evoRSL_003y']: 'checked';
				}
				return evo_lang($status);
			}
			public function get_trans_checkin_status($lang=''){
				$evopt = $this->opt2;
				$lang = (!empty($lang))? $lang : 'L1';

				return apply_filters('evors_checking_status_text_ar', array(
					'check-in'=>(!empty($evopt[$lang]['evoRSL_003x'])? $evopt[$lang]['evoRSL_003x']: 'check-in'),
					'checked'=>(!empty($evopt[$lang]['evoRSL_003y'])? $evopt[$lang]['evoRSL_003y']: 'checked'),
				));
			}

		// Internationalization rsvp status yes, no, maybe
			public function get_rsvp_status($status, $lang=''){
				if(empty($status)) return;

				$_sta = array(
					'y'=>array('Yes', 'evoRSL_003'),
					'n'=>array('No', 'evoRSL_005'),
					'm'=>array('Maybe', 'evoRSL_004'),
				);

				$lang = (!empty($lang))? $lang : (!empty($this->currentlang)? $this->currentlang: 'L1');
				if(!isset($_sta[$status])) return;
				return $this->lang($_sta[$status][1], $_sta[$status][0], $lang);
			}
		
		
    	function get_author_id() {
			$current_user = wp_get_current_user();
	        return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
	    }
	    function get_event_post_date() {
	        return date('Y-m-d H:i:s', time());
	    }
	    // return sanitized additional rsvp field option values
	    function get_additional_field_options($val){
	    	$OPTIONS = stripslashes($val);
			$OPTIONS = str_replace(', ', ',', $OPTIONS);
			$OPTIONS = explode(',', $OPTIONS);
			$output = false;
			foreach($OPTIONS as $option){
				$slug = str_replace(' ', '-', $option);
				$output[$slug]= $option;
			}
			return $output;
	    }
}
