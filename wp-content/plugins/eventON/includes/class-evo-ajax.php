<?php
/**
 * EventON Ajax Handlers
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Functions/AJAX
 * @version     4.2
 */

class evo_ajax{
	/**
	 * Hook into ajax events
	 */

	private $helper;

	public function __construct(){
		$ajax_events = array(
			'init_load'=>'init_load',			
			'ajax_events_data'=>'ajax_events_data',			
			'the_ajax_hook'=>'main_ajax_call',			
			'load_event_content'=>'load_event_content',
			'load_single_eventcard_content'=>'load_single_eventcard_content',
			'ics_download'=>'eventon_ics_download',			
			'export_events_ics'=>'export_events_ics',
			'search_evo_events'=>'search_evo_events',
			'get_local_event_time'=>'get_local_event_time',
			'refresh_now_cal'=>'refresh_now_cal',
			'record_mod_joined'=>'record_mod_joined',
			'refresh_elm'=>'refresh_elm',
			'gen_trig_ajax'=>'gen_trig_ajax',

		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			$prepend = ( in_array($ajax_event, array('the_ajax_hook','evo_dynamic_css','the_post_ajax_hook_3','the_post_ajax_hook_2')) )? '': 'eventon_';
			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		$this->helper = new evo_helper();

		add_filter('evo_ajax_general_send_results',array($this, 'general_send_req'), 10,2);
	}	


	// Initial load
		function callback($name, $nonce){
			$name = str_replace('eventon_', '', $name);
			if(!wp_verify_nonce( $nonce, 'rest_'.EVO()->version)) return false;
			return call_user_func_array(array($this, $name),array(true) );
		}
		function init_load($return = false){
			// get global cals on init
			global $EVOAJAX;	

			// init load calendar events
			$CALS = array();
			if(isset($_POST['cals']) && is_array($_POST['cals'])){
				foreach($_POST['cals'] as $calid=>$CD){
					if(!isset( $CD['sc'])) continue;

					$SC = $this->helper->sanitize_array( $CD['sc'] );

					$CALS[$calid]['sc'] = $SC;

					EVO()->calendar->process_arguments( $SC );

					// get events for the calendar
					$E = EVO()->calendar->_generate_events(
						'both',	apply_filters('evo_init_ajax_wparg_additions',array())
					);

					//print_r($$CD);
					$CALS[$calid]['json'] = $E['data'];
					$CALS[$calid]['html'] = $E['html'];
					//$CALS[$calid]['debug'] = date('Y-m-d H:i', $CD['sc']['focus_start_date_range']).'-'.date('Y-m-d H:i', $CD['sc']['focus_end_date_range']);
				}
			}

			$global = isset($_POST['global'])? 
				$this->helper->sanitize_array( $_POST['global'] ): array();

			$R =  apply_filters('evo_init_ajax_data', array(
				'cal_def'=> EVO()->calendar->helper->get_calendar_defaults(),
				'temp'=> EVO()->temp->get_init(),
				'dms' => array(
					'd'=> EVO()->cal->get_all_day_names(),
					'd3'=> EVO()->cal->get_all_day_names('three'),
					'd1'=> EVO()->cal->get_all_day_names('one'),
					'm'=> EVO()->cal->_get_all_month_names(),
					'm3'=> EVO()->cal->_get_all_month_names('short'),
				),		
				'cals'=> apply_filters('evo_init_ajax_cals',$CALS),		
				'txt'=> array(
					'no_events'=> evo_lang_get('evcal_lang_noeve','No Events'),
					'all_day'=> evo_lang_get('evcal_lang_allday','All Day'),
					'event_completed'=> evo_lang('Event Completed'),
				), 
				'html'=>array(
					'no_events' => EVO()->calendar->helper->get_no_event_content()
				),	
				// language translated texts for client side
				'terms'=> array(),
			), $global);

			if($return){ return $R; }else{ echo json_encode($R);	}
			exit;

		}

	// General ajax call - added 3.1
		public function gen_trig_ajax(){

			$PP = $this->helper->sanitize_array( $_POST );

			if(!wp_verify_nonce($PP['nn'], 'eventon_nonce')) {echo 'Evo Nonce Failed!'; exit;}
		
			echo json_encode(
				apply_filters('evo_ajax_general_send_results', array('status'=>'good'), $PP)
			);exit;			
		}

	// general send request
		function general_send_req($arr, $post){

			// get organizer more details
			if( $post['uid'] == 'evo_get_organizer_info'){
				$EVENT = new EVO_Event($post['eventid'] );

				$org_data = $EVENT->get_taxonomy_data( 'event_organizer', true, $post['term_id'] );
				if( !$org_data) return $arr;

				$org_data_this = $org_data['event_organizer'][$post['term_id']];

				// organizer link
				$organizer_link_target = (!empty($org_data_this->organizer_link_target) && $org_data_this->organizer_link_target == 'yes')? '_blank':'';

				$organizer_term_link = !empty($org_data_this->organizer_link) ? evo_format_link($org_data_this->organizer_link): false;

				$organizer_term_name = $organizer_term_link ? '<a target="'.$organizer_link_target.'" href="'. $organizer_term_link .'">' . $org_data_this->name . '</a>' : $org_data_this->name; 

				ob_start();
				?>
				<div class='evo_event_moreinfo_org pad20'>
					<div class='evo_row'>
						
						<?php 
							// image
							if( !empty($org_data_this->img_id)):
								$img_url = wp_get_attachment_image_src( $org_data_this->img_id ,'full');
								$img_url = $img_url[0];
								?>
								<div class='evo_row_item evo_row6_l evo_row6_m evo_row12_s padr20'>
								<p class=''><img class='borderr15' src='<?php echo $img_url;?>'/></p>
								</div>
								<?php 
							endif;

						?>
						
						<div class='evo_row_item evo_row6_l evo_row6_m evo_row12_s'>
							<h3 class='evo_h3 padt20 padb20 fw900i' style="font-size:36px;"><?php echo $org_data_this->name;?></h3>
							<p class='padb10'><?php echo $org_data_this->description;?></p>

							<?php
							if(!empty($org_data_this->organizer_contact)){						
								echo "<p class='padt10 padb10 evo_borderb' >". $org_data_this->organizer_contact ."</p>";
							}
							if(!empty($org_data_this->contact_email)){						
								echo "<p class='padt10 padb10 evo_borderb' >". $org_data_this->contact_email ."</p>";
							}

							// physical address
							if(!empty($org_data_this->organizer_address)){						
								echo "<p class='padt10 padb10 evo_borderb' >". $org_data_this->organizer_address ."</p>";
							}
							?>

							<?php 
							// social media links
								$social_html = '';
								foreach(apply_filters('evo_organizer_archive_page_social', array(
									'twitter'=>'evcal_org_tw',
									'facebook'=>'evcal_org_fb',
									'linkedin'=>'evcal_org_ln',
									'youtube'=>'evcal_org_yt'
								)) as $f=>$k){
									if( empty($org_data_this->$f)) continue;

									$social_html .= "<a class='pad10' href='". $org_data_this->$f. "'><i class='fa fa-{$f}'></i></a>";
								}

								if(!empty($social_html)){
									echo "<div class='evo_tax_social_media padt10 padb10'>{$social_html}</div>";
								}
							?>

							<?php if( $organizer_term_link):?>
								<p class='mar0 pad0'><a class='evo_btn evcal_btn' href='<?php echo $organizer_term_link;?>' target='<?php echo $organizer_link_target;?>'><?php evo_lang_e('Learn More');?></a></p>
							<?php endif;?>

						</div>						
					</div>
					
					<?php 
					// location map
						if( !empty($org_data_this->organizer_address) ):
						
						EVO()->cal->set_cur('evcal_1');
						$zoomlevel = EVO()->cal->get_prop('evcal_gmap_zoomlevel');
							if(!$zoomlevel) $zoomlevel = 16;

						$map_type = EVO()->cal->get_prop('evcal_gmap_format');
							if(!$map_type) $map_type = 'roadmap';

						$location_address = stripslashes( $org_data_this->organizer_address );

						$map_data = array(
							'address'=> $location_address,
							'latlng'=>'',
							'location_type'=> 'add',
							'zoom'=> $zoomlevel,
							'scroll'=> EVO()->cal->check_yn('evcal_gmap_scroll')? 'no':'yes',
							'mty'=>$map_type,
							'delay'=>400
						);

					?>
					
					<div id='evo_org_<?php echo $org_data_this->term_id;?>' class="evo_trigger_map borderr15 mart15" style='height:250px;' <?php echo $this->helper->array_to_html_data($map_data);?>></div>
				
					<?php endif;?>	
					

					<?php do_action('evo_eventcard_organizer_info_before_events', $org_data_this, $EVENT);?>
					
					<div class='evo_databox borderr15 pad30 mart15'>
						
						<h3 class="evo_h3 evo_borderb" ><?php evo_lang_e('Events by');?> <?php echo $org_data_this->name;?></h3>
						<div class='padt20'>
							<?php 

							$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

							
							echo EVO()->shortcodes->events_list( array(
								'number_of_months'=>5,
								'event_organizer'=>$post['term_id'],
								'hide_mult_occur'=>'no',
								'hide_empty_months'=>'yes',
								'eventtop_style'=> $eventtop_style,
								'ux_val'=>3
							));

							?>
						</div>
					</div>
				</div>

				<?php

				return array('status'=>'good', 'content'=> ob_get_clean());

				print_r($org_data);

			}

			return $arr;
		}


	// Primary function to load event data 
		function main_ajax_call(){
			$shortcode_args = $focused_month_num = $focused_year = '';
			$status = 'GOOD';

			$SC = isset($_POST['shortcode']) ? $this->helper->sanitize_array( $_POST['shortcode'] ): array();
			$ajaxtype = isset($_POST['ajaxtype'])? sanitize_text_field( $_POST['ajaxtype'] ): '';

			extract($SC);

			if(empty($number_of_months)){ return false; exit; }
			if($number_of_months < 1){ return false; exit; }

			EVO()->calendar->shortcode_args = $SC;
							
			// date range calculation
				if( isset($SC['focus_start_date_range']) && isset($SC['focus_end_date_range']) ){
					$focus_start_date_range = (int)$SC['focus_start_date_range'];
					$focus_end_date_range = (int)$SC['focus_end_date_range'];
				}

				$calendar_type = 'default';
				// event list with more than one month
				if( $SC['number_of_months']==1){

					// calculate new date range if calendar direction is changing
					if($_POST['direction'] !='none'){
						if(!empty($fixed_month) && !empty($fixed_year)){
							$fixed_year = ($_POST['direction']=='next')? 
								(($fixed_month==12)? $fixed_year+1:$fixed_year):
								(($fixed_month==1)? $fixed_year-1:$fixed_year);

							$fixed_month = ($_POST['direction']=='next')?
								(($fixed_month==12)? 1:$fixed_month+1):
								(($fixed_month==1)? 12:$fixed_month-1);													

							$DD = new DateTime();
							$DD->setTimezone( EVO()->calendar->timezone0 );
							$DD->setDate($fixed_year,$fixed_month,1 );
							$DD->setTime(0,0,0);

							$SC['fixed_month'] = $fixed_month;
							$SC['fixed_year'] = $fixed_year;
							$SC['focus_start_date_range'] = $DD->format('U');
							$DD->modify('last day of this month');
							$DD->setTime(23,59,59);
							$SC['focus_end_date_range'] = $DD->format('U');							
						}
					}else{ // not switching months

						// Going to today
						if($ajaxtype == 'today' || $ajaxtype == 'jumper'){
							$SC['focus_start_date_range']='';
							$SC['focus_end_date_range']='';	
							EVO()->calendar->shortcode_args = $SC;						
							EVO()->calendar->shell->set_calendar_range();	
							$SC = EVO()->calendar->shortcode_args;
						}
					}

				}else{	$calendar_type = 'list';	}
														

			// set calendar shortcode values
				$SC = apply_filters('eventon_ajax_arguments',$SC, $_POST, $ajaxtype);		
				ksort($SC);
				extract($SC);

				EVO()->calendar->shortcode_args = $SC; // set arguments to the calendar object
					
			// GET calendar header month year values
				$calendar_month_title = get_eventon_cal_title_month($fixed_month, $fixed_year, $lang);
						
			// Calendar content		
				$content = EVO()->evo_generator->_generate_events();

			// RETURN VALUES
				echo json_encode( apply_filters('evo_ajax_query_returns', array(
					'status'=> 					$status,
					'json'=> 					$content['data'],	
					'html'=>					$content['html'],				
					'cal_month_title'=>			$calendar_month_title,
					'SC'=> 	$SC,
					'debug' => array(
						's' => date('y-m-d h:i:s', $focus_start_date_range),
						'e' => date('y-m-d h:i:s', $focus_end_date_range),
					),
					
				), 
				$SC, $content) );exit;
		}

	// AJAX Events data
		function ajax_events_data(){
			$postdata = $this->helper->sanitize_array( $_POST );

			$SC = isset($postdata['shortcode']) ? $postdata['shortcode']: array();
			EVO()->calendar->shortcode_args = $SC;
		}

	// Now Calendar
		public function refresh_now_cal(){

			$PP = $this->helper->sanitize_array( $_POST );

			$calnow = new Evo_Calendar_Now();

			$SC = isset($PP['SC']) ? $PP['SC']: array();
			
			$defA = isset($PP['defA']) ? $PP['defA'] : array();

			$args = array_merge($defA, $SC);

			$calnow->process_a( $args );

			ob_start();
			$calnow->get_body( true );
			$html = ob_get_clean();

			echo json_encode(array(
				'status'=>'good',
				'html'=> $html,
			)); exit;
		}

	// refresh elements
		public function refresh_elm(){

			$PP = $this->helper->sanitize_array( $_POST );

			echo json_encode($this->get_refresh_elm_data( $PP )); exit;			
		}

		//get ajax refresh element's data array
		public function get_refresh_elm_data($PP, $type ='ajax'){
			$response = array();

			if(isset($PP['evo_data']) && is_array($PP['evo_data']) ){
				
				foreach($PP['evo_data'] as $ekey=>$classes){

					$ee = explode('_', $ekey);
					$EVENT = new EVO_Event($ee[0], '', (int)$ee[1] );

					foreach( $classes as $classnm=>$classdata){

						$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri][$classnm] = apply_filters('evo_ajax_refresh_elm', 
							array(
								'html'=> '',
								'data'=> $classdata,
							), $EVENT, $classnm, $classdata, $type, $PP
						);

						$response['status'] = 'good';
					}

					$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri] = apply_filters('evo_ajax_refresh_event_elms', 
						$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri],
						$EVENT , $classes, $type, $PP
					);
					
				}									
			}
			return $response;
		}

	// record moderator joined for virtual events
		public function record_mod_joined(){			

			if(!isset($_POST['eid'])) return false;
			if(!isset($_POST['ri'])) return false;
			if(!isset($_POST['nonce'])) return false;
			if(!isset($_POST['joined'])) return false;

			if(!wp_verify_nonce($_POST['nonce'], 'eventon_nonce')) {echo 'nonce failed'; exit;}

			$postdata = $this->helper->sanitize_array( $_POST );

			$EVENT = new EVO_Event( (int)$postdata['eid'], (int)$postdata['eid'] );

			// joined in or left
			$joined = ($postdata['joined'] == 'yes') ? 'in': 'left';

			$EVENT->record_mod_joined($joined);

			echo json_encode(array(
				'status'=>'good',
				'msg'=> ( $joined =='in' ? __('Recorded as moderator joined','eventon') : 
						__('Recorded as moderator left','eventon') )
			)); exit;
		}


	// Load single event content
	// @2.6.13
		function load_event_content(){

			if(!isset($_POST['eid'])) return false;
			if(!isset($_POST['nonce'])) return false;

			if(!wp_verify_nonce($_POST['nonce'], 'eventon_nonce')) {echo 'nonce failed'; exit;} // nonce verification

			$postdata = $this->helper->sanitize_array( $_POST );
			
			$EVENT = new EVO_Event($postdata['eid']);
			echo json_encode(
				apply_filters('evo_single_event_content_data',array(), $EVENT)
			);exit;
		}

	// load single eventcard content
	// @ 2.9.2
		public function load_single_eventcard_content(){
			$postdata = $this->helper->sanitize_array( $_POST );

			$event_id = (int) $postdata['event_id'];
			$ri = (int) $postdata['ri'];

			$SC = array();
			
			$SC = isset($postdata['SC']) ? $postdata['SC'] : array();
			$lang = isset($SC['lang'])? $SC['lang']:'L1';

			$SC['show_exp_evc'] = 'yes';

			$event_data = EVO()->calendar->get_single_event_data( $event_id, $lang, $ri, $SC);

			if($event_data && is_array($event_data)) $event_data = $event_data[0];

			echo json_encode(array(
				'status'=>'good',
				'content'=> $event_data['content']
			)); exit;
		}

	// OUTPUT: json headers
		private function json_headers() {		header( 'Content-Type: application/json; charset=utf-8' );	}



	// ICS file generation for add to calendar buttons
	// @updated 4.3
		function eventon_ics_download(){

			if( !isset( $_GET['event_id'])) return false;

			$event_id = (int)( sanitize_text_field( $_GET['event_id']) );
			$ri = isset($_GET['ri'])? (int)( sanitize_text_field($_GET['ri']) ) : 0;

			$EVENT = new EVO_Event($event_id,'',$ri);
			$EVENT->get_event_post();
			
			
			$slug = $EVENT->post_name;
						
			header("Content-Type: text/Calendar; charset=utf-8");
			header("Content-Disposition: inline; filename={$slug}.ics");

			echo "BEGIN:VCALENDAR\r\n";
			echo "VERSION:2.0\r\n";
			echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";

			echo $EVENT->get_ics_content();

			echo "END:VCALENDAR";
			
			exit;

		}

	// download all event data as ICS
	// @updated 4.3
		function export_events_ics(){
			
			if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Nonce Security Failed.');

			$events = EVO()->calendar->get_all_event_data(array(
				'hide_past'=>'yes'
			));
			
			if(!empty($events)):

				$HELP = new evo_helper();

				$slug = 'eventon_events';
				header("Content-Type: text/Calendar; charset=utf-8");
				header("Content-Disposition: inline; filename={$slug}.ics");
				echo "BEGIN:VCALENDAR\n";
				echo "VERSION:2.0\n";
				echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
				echo "CALSCALE:GREGORIAN\n";
				echo "METHOD:PUBLISH\n";

				// EACH EVENT
				foreach($events as $event_id=>$event){

					$EVENT = new EVO_Event( $event_id, $event['pmv'], 0, true, false);

					echo $EVENT->get_ics_content();

				}
				echo "END:VCALENDAR";
				exit;

			endif;
		}

	// get event time based on local time on browswr
		function get_local_event_time(){
			$datetime = new evo_datetime();
			$offset = $datetime->get_UTC_offset();
			$brosweroffset = (int)$_POST['browser_offset'] *60;
			echo $brosweroffset.' '.$offset.' '.$object->evvals['evcal_srow'][0];

			$newunix = $object->evvals['evcal_srow'][0] + ($offset + $brosweroffset);
			echo date('Y-m-d h:ia', $newunix);
		}


	// Search results for ajax search of events from search box
	function search_evo_events(){
		
		$postdata = $this->helper->sanitize_array( $_POST );

		$searchfor = isset($postdata['search']) ? $postdata['search'] :'';
		$shortcode = isset($postdata['shortcode']) ? $postdata['shortcode']: array();
	
		$searchfor = str_replace("\'",'', $searchfor);

		// if search all events regardless of date
			if( !empty($shortcode['search_all'] ) && $shortcode['search_all']=='yes'){
				$DD = EVO()->calendar->DD;
				$DD->modify('first day of this month'); $DD->setTime(0,0,0);
				$DD->modify('-15 years');
				
				$__focus_start_date_range = $DD->format('U');
				
				$DD->modify('+30 years');
				$__focus_end_date_range = $DD->format('U');
			
			}else{
				$current_timestamp = current_time('timestamp');

				// restrained time unix
					$number_of_months = !empty($shortcode['number_of_months'])? $shortcode['number_of_months']:12;
					$month_dif = '+';
					$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

					$restrain_monthN = ($number_of_months>0)?				
						date('n',  $unix_dif):
						date('n',$current_timestamp);

					$restrain_year = ($number_of_months>0)?				
						date('Y', $unix_dif):
						date('Y',$current_timestamp);			

				// upcoming events list 
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
			}
		

		// Add extra arguments to shortcode arguments			
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
				's'=>$searchfor,
				'search_all'=> (isset($shortcode['search_all'])? $shortcode['search_all']:'no')
			);

			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;

			// merge passed shortcode values
				if(!empty($shortcode))
					$args= wp_parse_args($shortcode, $args);

			EVO()->calendar->process_arguments($args);

			$content = EVO()->calendar->body->get_calendar_header(
				array(
					'date_header'=>false,
					'sortbar'=>true,
					'range_start'=>$__focus_start_date_range,
					'range_end'=>$__focus_end_date_range,
					'header_title'=>'',
					'send_unix'=>true
				)
			);

			$content .= EVO()->calendar->_generate_events('html');
			
			$content .= EVO()->calendar->body->get_calendar_footer();
			
			echo json_encode(array(
				'content'=>$content,
				'range'=> date('Y-m-d', $__focus_start_date_range).' '.date('Y-m-d', $__focus_end_date_range)
			));
			exit;

	}
	/* dynamic styles */
		/*function eventon_dymanic_css(){
			//global $foodpress_menus;
			require('admin/inline-styles.php');
			exit;
		}*/

}
