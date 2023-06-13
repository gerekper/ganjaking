<?php
/*
 *	ActionUser front-end
 *	@version 	2.4
 */

class evoau_frontend{
	private $urls;
	var $log= array();
	var $users;
	var $formtype = 'new';

	public $message, $link, $lang;

	function __construct(){
		add_filter('evo_cal_gen_options', array($this, 'load_options'));
		$this->evoau_opt = get_option('evcal_options_evoau_1');

		add_filter('eventon_extra_tax',array($this,'extra_tax'),10,1);
		add_action( 'init', array( $this, 'register_frontend_scripts' ) ,15);

		//when a new post is published
		add_action('transition_post_status',array($this,'send_approval_email'), 10, 3);
		add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);

		// single event access restriction
		add_filter('evo_single_page_access',array($this,'access_to_event'), 10);

		$this->options = get_option('evcal_options_evcal_1');
		$this->tax_count = evo_get_ett_count($this->options);
		$this->tax_names = evo_get_ettNames($this->options);
		

		// functions
		$this->HELP = new evo_helper();
		$this->functions = new evoau_functions($this->evoau_opt);

	}
	public function load_options($A){
		$A['evoau_1'] = 'evcal_options_evoau_1';
		return $A;
	}
	function extra_tax($array){
		$array['evoau']='event_users';
		return $array;
	}

	// FRONTEND scripts
		function register_frontend_scripts(){
			
			wp_register_script( 'evoau_cookies',EVOAU()->assets_path.'js/jq_cookie.js',array('jquery','jquery-ui-core','jquery-ui-datepicker'), EVOAU()->version, true );

			wp_register_script( 'evo_au_frontend',EVOAU()->assets_path.'js/au_script_f.js',array('jquery','jquery-ui-core','jquery-ui-datepicker'), EVOAU()->version, true );
			wp_register_script( 'evo_wyg_editor',EVOAU()->assets_path.'js/trumbowyg.min.js','', EVOAU()->version, true );
			wp_register_style( 'evo_wyg_editor',EVOAU()->assets_path.'css/trumbowyg.css', '', EVOAU()->version);
			
			wp_register_style( 'evo_au_styles_f',EVOAU()->assets_path.'au_styles.css', '', EVOAU()->version);

			wp_localize_script( 
				'evo_au_frontend', 
				'evoau_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonau_nonce' )
				)
			);
			EVO()->elements->register_colorpicker();

		}

		// ENQUEUE
			public function print_frontend_scripts(){
				
				$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';	

				wp_enqueue_script('tiny_mce');	
		
				wp_enqueue_style("jquery-ui-css", "//ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");
		
				wp_enqueue_style( 'evo_font_icons');
				wp_enqueue_style( 'evo_wyg_editor');
				wp_enqueue_style( 'evo_au_styles_f');			
				
				wp_enqueue_script('evo_wyg_editor');	
				wp_enqueue_script('jquery-form');
				wp_enqueue_script('evcal_ajax_handle');	// call eventon_script.js if not loaded
				wp_enqueue_script('evoau_cookies');
				wp_enqueue_script('evo_au_frontend');

				global $ajde;
				$ajde->load_colorpicker();

				do_action('evoau_frontend_scripts_enqueue');			
			}

			public function print_styles(){
				wp_enqueue_style( 'evo_wyg_editor');
				wp_enqueue_style( 'evo_font_icons');
				wp_enqueue_style( 'evo_au_styles_f');
			}

	// access to see event
		function access_to_event(){
			global $post;

			$event = new EVO_Event( $post->ID);

			if( !$event->get_prop('_evoau_accesscode') ) return true;

			$code = isset($_POST['_evoau_accesscode'])? $_POST['_evoau_accesscode']: false;

			if( !$code || $code != $event->get_prop('_evoau_accesscode') ):

			wp_enqueue_style( 'evo_au_styles_f');
			// print out access pass field
			?>
			<div class='evoau_event_access'>
				<form action='' method='post'>
					<label><?php evo_lang_e('Enter the access code to see this event');?></label>
					<input name='_evoau_accesscode' type="password"/>
					<button class='evcal_btn'><?php evo_lang_e('Submit');?></button>					
				</form>
				<?php if( $code && $code != $event->get_prop('_evoau_accesscode') ):?>
						<p class='error'><?php evo_lang_e('Access code is invalid!');?></p>
					<?php endif;?>
			</div>
			<?php

			else:

				return true;

			endif;

			return false;
		}

	// submission form
		function ligthbox($array){
			$array['evoau_lightbox']= array(
				'id'=>'evoau_lightbox',
				'CLclosebtn'=> 'evoau_lightbox',
			);return $array;
		}


	
	// FORM Fields for the front end form
		public function au_form_fields($var=''){
			$evcal_opt = $this->options;		
			
			/* 
				structure = 0=>name, 1=> var, 2=> field type, 3=>placeholder, 4=>lang var, 5=> required or not, 6=>special name for settings
			*/
			$event_fields = array(				
				'event_name'=>array('Event Name', 'event_name', 'title','','evoAUL_evn'),
				'event_subtitle'=>array('Event Sub Title', 'evcal_subtitle', 'text','','evoAUL_est'),
				'event_description'=>array('Event Details', 'event_description', 'textarea','','evcal_evcard_details_au'),
				'event_start_date'=>array('Event Start Date/Time', 'evcal_start_date', 'startdate','','evoAUL_esdt'),
				'event_end_date'=>array('Event End Date/Time', 'event_end_date', 'enddate','','evoAUL_eedt'),
				
				'event_allday'=>array('All Day', 'event_allday', 'allday','',''),
				
				'event_location_select'=>array('Event Location Fields', 'evcal_location_select', 'locationselect','','evoAU_pseld'),
					'event_location_name'=>array('Event Location Name', 'evcal_location_name', 'text','', 'evoAUL_lca'),
					'event_location'=>array('Event Location Address', 'location_address', 'text','','evoAUL_ln'),
					'event_location_cord'=>array('Event Location Coordinates (lat,lon Seperated by comma)', 'event_location_cord', 'text','','evoAUL_lcor'),
					'event_location_link'=>array('Event Location Link', 'evcal_location_link', 'text','','evoAUL_llink'),
				'event_color'=>array('Event Color', 'evcal_event_color', 'color','','evoAUL_ec'),
				'event_organizer_select'=>array('Event Organizer Fields', 'evcal_organizer_select', 'organizerselect','','evoAU_pseod'),
					'event_organizer'=>array('Event Organizer', 'evcal_organizer', 'text','','evoAUL_eo'),	
					'event_org_contact'=>array('Event Organizer Contact Information', 'evcal_org_contact', 'text','','evoAUL_eoc'),	
					'event_org_address'=>array('Event Organizer Address', 'evcal_org_address', 'text','','evoAUL_eoa'),	
					'event_org_link'=>array('Event Organizer Link', 'evcal_org_exlink', 'text','','evoAUL_eol'),	
				'learnmorelink'=>array('Learn More Link', 'evcal_lmlink', 'learnmore','','evoAUL_lml'),	
				'virtual'=>array('Virtual Event Details', '_virtual', 'virtual','',''),	
				'health'=>array('Health Guidelines', '_health', 'health','',''),	
			);

			// event type categories
				$ett_verify = $this->tax_count;
				$_tax_names_array = $this->tax_names;
				for($x=1; $x< ($ett_verify+1); $x++){
					$ab = ($x==1)? '':'_'.$x;
					$__tax_name = $_tax_names_array[$x];

					$event_fields['event_type'.$ab] = array(
						'Select the '.$__tax_name.' Category', 
						'event_type'.$ab, 'tax','',
						'evoAUL_stet'.$x
					);
				}

			$event_fields_1 = array(
				'event_image'=>array('Event Image', 'event_image', 'image','','evoAUL_ei'),
				'yourname'=>array('Your Full Name', 'yourname', 'text','','evoAUL_fn','req'),
				'youremail'=>array('Your Email Address', 'youremail', 'text','','evoAUL_ea','req'),
				'user_interaction'=>array('User Interaction', 'uinter', 'uiselect','',''),		
				'event_captcha'=>array('Form Human Submission Validation', 'evcal_captcha', 'captcha','','evoAUL_cap'),
				'event_additional'=>array(
					'Additional Private Notes', 'evcalau_notes', 'textarea','','evoAU_add','','*** Additional private notes for admin'),
				'event_html'=>array('** Additional HTML Field', 'evoau_html', 'html'),
				'event_access'=>array(
					'Event Access Password','_evoau_accesscode','text','','','', '** Event Access Password'),
				'event_timezone'=>array(
					'Event Timezone','_evo_tz','timezone'),
			);

			// additional edit only form fields
			$event_fields_editonly = array(
				'event_special_edit'=>array(evo_lang('Special Event Edit Fields (exclude, feature and event status)'), 'event_special_edit', 'yesno'),
				
				'_featured'=>array(evo_lang('Feature this event'), '_featured', 'yesno'),
				'_status'=>array(evo_lang('Event Status'), '_status','status'),
				'evo_exclude_ev'=>array(evo_lang('Exclude this event from calendar'), 'evo_exclude_ev', 'yesno'),
			);

			$event_fields = array_merge($event_fields, $event_fields_1, $event_fields_editonly);

			// get custom meta fields for 
				$custom_field_count = evo_calculate_cmd_count($evcal_opt);	// get activated custom field count		
				for($x=1; $x<=$custom_field_count; $x++){	
					$new_additions = array();

					if(eventon_is_custom_meta_field_good($x, $evcal_opt)){
						$index = 'evo_customfield_'.$x;
						$_variable_name = '_evcal_ec_f'.$x.'a1_cus';
						$_field_name = $evcal_opt['evcal_ec_f'.$x.'a1'];
						$content_type = $evcal_opt['evcal_ec_f'.$x.'a2'];

						$new_additions[$index]= array(
							$_field_name, $_variable_name,  $content_type, $_field_name, 'evcal_cmd_'.$x
						);
						$event_fields = array_merge($event_fields, $new_additions);
					}
				}

			// Filter for other additions
			$event_fields = apply_filters('evoau_form_fields', $event_fields);
			
			// return certain fields from above list
				if($var=='savefields'){
					unset($event_fields['event_name']);
					unset($event_fields['event_start_date']);
					unset($event_fields['event_end_date']);
					unset($event_fields['event_allday']);
					unset($event_fields['event_description']);
					unset($event_fields['yourname']);
					unset($event_fields['youremail']);
					unset($event_fields['event_captcha']);
					unset($event_fields['user_interaction']);
					unset($event_fields['event_location_cord']);
					unset($event_fields['event_color']);
				}
				if($var=='default'){
					$event_fields = array(
						'event_name'=>$event_fields['event_name'],
						'event_start_date'=>$event_fields['event_start_date'],
						'event_end_date'=>$event_fields['event_end_date'],
						'event_allday'=>$event_fields['event_allday'],
					);
				}
				if($var=='additional'){
					unset($event_fields['event_name']);
					unset($event_fields['event_start_date']);
					unset($event_fields['event_end_date']);
					unset($event_fields['event_allday']);

					unset($event_fields['event_organizer']);
					unset($event_fields['event_org_contact']);
					unset($event_fields['event_org_address']);
					unset($event_fields['event_org_link']);

					unset($event_fields['event_location_name']);
					unset($event_fields['event_location']);
					unset($event_fields['event_location_cord']);
					unset($event_fields['event_location_link']);
					unset($event_fields['evo_exclude_ev']);
					unset($event_fields['_cancel']);
					unset($event_fields['_featured']);
				}
				if($var== 'defaults_ar'){
					$event_fields = array('event_name','event_start_date','event_end_date','event_allday');
				}
				if($var =='editonly'){
					$event_fields = $event_fields_editonly;
				}
			
			return $event_fields;
		}

	// SAVE form submittions UPON submit
		function save_form_submissions(){
			$status= $cu_email='';

			//process $_POST array	
				foreach($_POST as $ff=>$post){
					if(!is_array($post))	$_POST[$ff]= urldecode($post);
				}
				$post_data = $this->HELP->sanitize_array( $_POST );

				// temp solution
				$post_data['event_description'] = $_POST['event_description'];


			// before form submission validation
				$ready_to_go = apply_filters('evoau_before_form_submission', true, $post_data);
				if(!$ready_to_go) return array('status'=>'bad','msg'=>'nof4');

			// edit or add new
				if(isset($post_data['form_action']) && isset($post_data['eventid']) && $post_data['form_action']=='editform' ){
					$created_event_id = (int)$post_data['eventid'];
					$this->formtype = 'edit';
					$__post_content = (!empty($post_data['event_description']))?
	        			$this->filter_post_content($post_data['event_description']): null;

	        		//print_r( sanitize_textarea_field($_POST['event_description']) );
					
					// update event name and event details
					$event = array(
						'ID'=> $created_event_id,
						'post_title'=>wp_strip_all_tags($post_data['event_name']),
						'post_content'=>$__post_content
					);

					// set to draft when editing event - if enabled via settings evoau_edit_to_draft
					if( isset($this->evoau_opt['evoau_edit_to_draft']) && $this->evoau_opt['evoau_edit_to_draft']=='yes'){
						$event['post_status'] = 'draft';
					}


					wp_update_post( $event );
				}else{
					$created_event_id = $this->create_post();
					$this->formtype = 'new';
				}

			// PLUG before saving event meta data
				do_action('eventonau_before_save_form_submissions', $created_event_id, $this->formtype);

			// Event is created
			if($created_event_id){	

				$EVENT = new EVO_Event( $created_event_id );


				$is_user_logged_in = is_user_logged_in();

				// saved field values
				$saved_fields = (!empty($this->evoau_opt['evoau_fields']) && is_array($this->evoau_opt['evoau_fields']) && count($this->evoau_opt['evoau_fields'])>0)? $this->evoau_opt['evoau_fields']: false;	


				// Save limit submissions value
					if( isset($post_data['evoau_limit_submissions']) && $post_data['evoau_limit_submissions'] == 'yes' && $is_user_logged_in){
						$uid = get_current_user_id();

						$submitted = get_user_meta($uid, '_evoau_submissions');
						update_user_meta($uid, '_evoau_submissions', (!$submitted? 1: (int)$submitted+1));
					}

				// SAVE DATE TIMES and start/end - meta data
					if(isset($_POST['event_start_date_x'])  ){

						// if no end date
						$end_date = (!empty($_POST['event_end_date_x']))? 
							$_POST['event_end_date_x']: $_POST['event_start_date_x'];


						//  times
						$_start_hour = isset($_POST['_start_hour']) ? $_POST['_start_hour'] : '1';
						$_start_minute = isset($_POST['_start_minute']) ? $_POST['_start_minute'] : '00';
						$_start_ampm = isset($_POST['_start_ampm']) ? $_POST['_start_ampm'] : null;

						$_end_hour = isset($_POST['_end_hour']) ? $_POST['_end_hour'] : '1';
						$_end_minute = isset($_POST['_end_minute']) ? $_POST['_end_minute'] : '00';
						$_end_ampm = isset($_POST['_end_ampm']) ? $_POST['_end_ampm'] : null;
										


						// date and time array
							$date_array_end = array(
								'evcal_end_date'=>			$end_date,
								'evcal_end_time_hour'=>		$_end_hour,
								'evcal_end_time_min'=>		$_end_minute,
								'evcal_et_ampm'=>			$_end_ampm,
								'evcal_start_date'=> 		$_POST['event_start_date_x'],
								'evcal_start_time_hour'=>	$_start_hour,
								'evcal_start_time_min'=>	$_start_minute,
								'evcal_st_ampm'=>			$_start_ampm,
								'event_vir_date_x'=>	isset($_POST['event_vir_date_x']) ? $_POST['event_vir_date_x'] : null,
								'_vir_hour'=>	isset($_POST['_vir_hour']) ? $_POST['_vir_hour'] : null,
								'_vir_minute'=>	isset($_POST['_vir_minute']) ? $_POST['_vir_minute'] : null,
								'_vir_ampm'=>	isset($_POST['_vir_ampm']) ? $_POST['_vir_ampm'] : null,
							);			
										
						// all day events
						if(!empty($_POST['evcal_allday']) ){
							$this_val = $_POST['evcal_allday']=='yes'? 'yes':'no';
							$this->create_custom_fields($created_event_id, 'evcal_allday', $this_val);
						}

						// no end time
						if(!empty($_POST['evo_hide_endtime']) ){
							$this_val = $_POST['evo_hide_endtime']=='yes'? 'yes':'no';
							$this->create_custom_fields($created_event_id, 'evo_hide_endtime', $this_val);
						}	

																		
						//$__evo_date_format = (!empty($_POST['_evo_date_format']))? $_POST['_evo_date_format']: 'd/m/Y';
						$_evo_time_format = (!empty($_POST['_evo_time_format']))? $_POST['_evo_time_format']: '12h';

						$proper_time = eventon_get_unix_time($date_array_end, 'Y-m-d', $_evo_time_format);
										
						// save required start time variables
						$this->create_custom_fields($created_event_id, 'evcal_srow', $proper_time['unix_start']);
						$this->create_custom_fields($created_event_id, 'evcal_erow', $proper_time['unix_end']);	

						// virtual visible end time
						if(!empty($_POST['_evo_virtual_endtime']) ){
							$this_val = $_POST['_evo_virtual_endtime']=='yes'? 'yes':'no';
							$this->create_custom_fields($created_event_id, '_evo_virtual_endtime', $this_val);

							//print_r($proper_time);

							if( isset($proper_time['unix_vir_end']) && !empty($proper_time['unix_vir_end'])){
								$this->create_custom_fields($created_event_id, '_evo_virtual_erow', $proper_time['unix_vir_end']);	
							}
						}

						// save repeating data for the event
							if( isset($_POST['evcal_repeat']) && $_POST['evcal_repeat']=='yes' 
								&& isset($_POST['evcal_rep_freq'])
							){	

								$_POST['repeat_intervals'][0]= array(0=>$proper_time['unix_start'], $proper_time['unix_end']);

								$repeat_intervals = eventon_get_repeat_intervals($proper_time['unix_start'],$proper_time['unix_end'] );
								
								if ( !empty($repeat_intervals) ){
									
									//asort($repeat_intervals);
									
									$this->create_custom_fields($created_event_id, 'repeat_intervals', $repeat_intervals);

									// other repeat data
									$this->create_custom_fields($created_event_id, 'evcal_repeat', $_POST['evcal_repeat']);
									$this->create_custom_fields($created_event_id, 'evcal_rep_freq', $_POST['evcal_rep_freq']);
									$this->create_custom_fields($created_event_id, 'evcal_rep_gap', $_POST['evcal_rep_gap']);
									$this->create_custom_fields($created_event_id, 'evcal_rep_num', $_POST['evcal_rep_num']);
								}
							}
					}
				
				// initial
					$image_set = false;
					$edata = array();

				// create custom meta fields and assign taxonomies			
					foreach($this->au_form_fields('savefields') as $field=>$fn){
						$__var_name = $fn[1];


						// check if value passed
						if(isset($_POST[$__var_name]) && !empty($_POST[$__var_name]) ){
							// for event taxonomies
							if($fn[2] =='tax' ){	
								// save post terms
								if(count($_POST[$__var_name])>0 && is_array($_POST[$__var_name])){	
									// for tax #1 and #2
									if($field=='event_type_2' || $field=='event_type'){
										$ab = ($field=='event_type')? '':'_2';
										$terms = $_POST[$__var_name];

										// append default tax terms if activated in options
										if(!empty($this->evoau_opt['evoau_set_def_ett'.$ab]) 
											&& !empty($this->evoau_opt['evoau_def_ett_v'.$ab]) 
											&& $this->evoau_opt['evoau_def_ett_v'.$ab]!='-' 
											&& $this->evoau_opt['evoau_set_def_ett'.$ab]=='yes'
										){
											$terms[] = $this->evoau_opt['evoau_def_ett_v'.$ab];
										}	
										wp_set_post_terms($created_event_id, $terms, $field);

									}else{
										wp_set_post_terms($created_event_id, $_POST[$__var_name], $field);
									}
								}
							// learn more field
							}elseif($fn[2] == 'learnmore'){
								// the actual learn more link url
								$value = $this->convert_links_to_proper($_POST[$__var_name]);
								$this->create_custom_fields($created_event_id, $__var_name, $value);
								
								// learn more open in new window
								if(isset($_POST['evcal_lmlink_target'])) 
									$this->create_custom_fields($created_event_id, 'evcal_lmlink_target', $_POST['evcal_lmlink_target']);

							}else{
								//$value = addslashes($_POST[$__var_name]);
								$value = $this->filter_post_content( $_POST[$__var_name] );
								$this->create_custom_fields($created_event_id, $__var_name, $value);
							}

							// custom meta field that is a button
							if($fn[2]=='button' && !empty($_POST[$__var_name.'L'])){
								$this->create_custom_fields($created_event_id, $__var_name.'L', $_POST[$__var_name.'L']);
							}
						}elseif($this->formtype=='edit'){
							// if edit form and the field have no value then delete it
							
							// if this field is in form fields array
							if( $saved_fields && in_array($field, $saved_fields)){
								delete_post_meta($created_event_id, $__var_name);
							}
							
						}// end if var not set

						// Pluggable
							if( (!empty($fn[3]) && $fn[3] == 'custom') || (!empty($fn[2]) && $fn[2] == 'custom') ){
								do_action('evoau_save_formfields',$field, $fn, $created_event_id);
							}

						// create new tax term
							if($fn[2] =='tax' && !empty($_POST[$__var_name.'_new'])){								
								$terms = $_POST[$__var_name.'_new'];
								$terms = explode(',', $terms);

								foreach($terms as $term){
									$this->set_new_term($term, $__var_name, $created_event_id);	
								}
							}	

						// virtual
							if( $field == 'virtual'){
								foreach(array(
									'_vir_url',
									'_vir_pass',
									'_virtual_type',
									'_vir_show',
									'_vir_hide',
									'_vir_nohiding',
									'_vir_after_content',
									'_vir_after_content_when'
								) as $F){
									if( empty($_POST[ $F ])) continue;
									$EVENT->set_prop( $F, sanitize_text_field($_POST[$F]) );
								}
							}

						// health
							if( $field == 'health'){
								
								foreach(array(
									'_health_mask',
									'_health_temp',
									'_health_pdis',
									'_health_san',
									'_health_out',
									'_health_other',
								) as $F){
									if( empty($_POST[ '_edata'][$F] )) continue;
									$EVENT->set_eprop($F, sanitize_text_field($_POST[ '_edata'][$F]), false, false);
								}
								
							}

						// event status
							if( $field == '_status'){
								foreach(array(
									'_cancel_reason',
									'_movedonline_reason',
									'_postponed_reason',
									'_rescheduled_reason'
								) as $F){
									if( empty($_POST[ $F ])) continue;
									$EVENT->set_prop( $F, sanitize_text_field($_POST[$F]) );
								}
							}

						// Assign tax terms if activated but NOT visible on the form
							if($field=='event_type_2' || $field=='event_type'){
								$ab = ($field=='event_type')? '':'_2';
								
								// append default tax terms if activated in options
								if(!empty($this->evoau_opt['evoau_set_def_ett'.$ab]) 
									&& !empty($this->evoau_opt['evoau_def_ett_v'.$ab]) 
									&& $this->evoau_opt['evoau_def_ett_v'.$ab]!='-' 
									&& $this->evoau_opt['evoau_set_def_ett'.$ab]=='yes'
								){
									$terms[] = $this->evoau_opt['evoau_def_ett_v'.$ab];
									wp_set_post_terms($created_event_id, $terms, $field);
								}	
							}

						// image
							// check id default image set for forms and evnet doesnt have an image already
							if(!empty($this->evoau_opt['evoau_def_image']) && !$image_set && !isset($_POST['evoau_event_image_id'])){
								set_post_thumbnail($created_event_id, $this->evoau_opt['evoau_def_image']);
								$image_set = true;
							}

							if($field == 'event_image'  ){
								// on edit form if image already set
								if(isset($_POST['event_image_exists'])){
									// if image exit in edit form
									if($_POST['event_image_exists']=='yes')
										continue;

									if($_POST['event_image_exists']=='no')
										delete_post_thumbnail($created_event_id);
								}

								// set allowed file types
								$allowed_file_types = array("image/jpeg", "image/jpg", "image/png",'application/pdf');


								if( !empty( $_FILES ) && !empty($_FILES[$__var_name]) && 'POST' == $_SERVER['REQUEST_METHOD']  ){

									// check file type 
									if( ! in_array( $_FILES[ $__var_name ]['type'] , $allowed_file_types )) continue;

									if ($_FILES[$__var_name]['error'] !== UPLOAD_ERR_OK) 
										continue;

									// file size limit
									
									//print_r($_FILES);

									require_once (ABSPATH.'/wp-admin/includes/media.php');
									require_once (ABSPATH.'/wp-admin/includes/file.php');
									require_once (ABSPATH.'/wp-admin/includes/image.php');	

									$attachmentId = media_handle_upload($__var_name, $created_event_id);

									// if image upload failed stop the rest of event submission process
									if(is_wp_error($attachmentId)){

										// trash the created event
										wp_trash_post($created_event_id);

										return array('status'=>'bad','msg'=>'nof7');
									}

									//unset($_FILES);

									set_post_thumbnail($created_event_id, $attachmentId);
									$this->create_custom_fields($created_event_id, 'ftimg', $attachmentId);

								}
							}
						
					} // end foreach

				// UNSET Files
					if($_FILES) unset($_FILES);
				
				// event color
					$COLOR = !empty($_POST['evcal_event_color'])? $_POST['evcal_event_color']: 
						( !empty($this->options['evcal_hexcode'])? $this->options['evcal_hexcode']:'206177' );
					$this->create_custom_fields($created_event_id, 'evcal_event_color', $COLOR);
					if(isset($_POST['evcal_event_color_n']))
						$this->create_custom_fields($created_event_id, 'evcal_event_color_n', $_POST['evcal_event_color_n']);

				// current user 
					$current_user = wp_get_current_user();
					// if user is logged in
					if(!empty($current_user)){
						// get the user email if the user is logged in and has email
						$cu_email = $current_user->user_email;						
					}

				// assign author if set to do so
					if($this->formtype=='new' && ( $is_user_logged_in || !empty($_POST['_current_user_id']) ) && evo_settings_check_yn($this->evoau_opt,'evoau_assignu')){
						
						$current_user_id = (!empty($current_user) && $current_user->ID >0)? (string)$current_user->ID:
							(!empty($_POST['_current_user_id'])? $_POST['_current_user_id']: false );

						// if user is logged in
						if($current_user_id){	
							wp_set_object_terms( $created_event_id, array( $current_user_id ), 'event_users' );
						}
					}

				// Save user interaction fields
					if(
						$saved_fields && in_array('user_interaction', $saved_fields) || 
						(!empty($this->evoau_opt['evoau_ux']) && $this->evoau_opt['evoau_ux']=='yes') 
					){
							
						$default_ux = $this->evoau_opt['evoau_ux_val'];
						$ux_val = isset($_POST['uinter'])? $_POST['uinter']: $default_ux;

						if( $ux_val){
							// only for external links
							if($ux_val==2){ // open as external links
								if(!empty($_POST['_evcal_exlink_target']))
									$this->create_custom_fields($created_event_id, '_evcal_exlink_target', $_POST['_evcal_exlink_target']);
								if(!empty($_POST['evcal_exlink']))
									$this->create_custom_fields($created_event_id, 'evcal_exlink', $_POST['evcal_exlink']);

							}elseif($ux_val==4){// open as single events
								$exlink = get_permalink($created_event_id);
								$this->create_custom_fields($created_event_id, 'evcal_exlink', $exlink);
							}
						}

						// ux value, check if submit from form, else default val set in settings
						$ux_val = (!empty($_POST['uinter']))? $_POST['uinter']:
							( (!empty($this->evoau_opt['evoau_ux']) && $this->evoau_opt['evoau_ux']=='yes')?
								$this->evoau_opt['evoau_ux_val']:
								false
							);

						if($ux_val)
							$this->create_custom_fields($created_event_id, '_evcal_exlink_option', $ux_val);
					}

				//$this->create_custom_fields($created_event_id,'aaa', $debug);

				// save language of the event
					if(!empty($post_data['_evo_lang']))
						$this->create_custom_fields($created_event_id, '_evo_lang', $post_data['_evo_lang']);

				// generate google maps
					$googleMapsVal = ( !empty($post_data['location_address']) || !empty($post_data['evcal_location_name'])) ?
						'yes':'no';
					$this->create_custom_fields($created_event_id, 'evcal_gmap_gen', $googleMapsVal);
				
				// save location as taxonomy						
					// from terms list or existing term
					if( !empty($post_data['evoau_location_select'])){
						$term_id = (int)$post_data['evoau_location_select'];
						wp_set_object_terms($created_event_id, $term_id, 'event_location', false);
					}		

					// if created new override existing with new
					if(!empty($post_data['evcal_location_name'])){
						$this->set_new_term($post_data['evcal_location_name'], 'event_location', $created_event_id);
					}		

				// save organizer as taxonomy					
					// from terms list or existing term
					if( !empty($post_data['evoau_organizer_select'])){
						$term_id = (int)$post_data['evoau_organizer_select'];
						wp_set_object_terms($created_event_id, $term_id, 'event_organizer', false);
					}

					// if created new override existing with new
					if(!empty($post_data['evcal_organizer'])){
						$this->set_new_term($post_data['evcal_organizer'], 'event_organizer', $created_event_id);
					}
				
				// OTHER eventon addon intergration
					// Reviewer addon
						if( !empty($this->evoau_opt['evoar_re_addon']) && $this->evoau_opt['evoar_re_addon']=='yes' ){
							$this->create_custom_fields($created_event_id, 'event_review', 'yes');
						}

				// save edata 
					$EVENT->save_eprops('_edata');


				// PLUGGABLE eventon addon intergration
					do_action('eventonau_save_form_submissions',$created_event_id, $this->formtype);


				// save submitter email address
					if(!empty($post_data['yourname']) && isset($post_data['yourname']))
						$this->create_custom_fields($created_event_id, '_submitter_name', $post_data['yourname']);

					// save email address for submitter
					if(!empty($post_data['youremail']) && isset($post_data['youremail'])){
						$this->create_custom_fields($created_event_id, '_submitter_email', $post_data['youremail']);
					}elseif(!empty($cu_email)){
						// save current user email if it exist
						$this->create_custom_fields($created_event_id, '_submitter_email', $cu_email);
					}

				// save whether to notify when draft is published if submission saved as draft
					if((empty($this->evoau_opt['evoau_post_status'])) || 
						( !empty($this->evoau_opt['evoau_post_status']) && $this->evoau_opt['evoau_post_status']=='draft' )){
						$this->create_custom_fields($created_event_id, '_send_publish_email', 'true');
					}				

				// email notification
					$__evo_admin_email = get_option('admin_email');
					if($this->formtype=='new'){
						$this->send_au_email_notif($created_event_id, $__evo_admin_email);
						$this->send_submitter_email_notif($created_event_id, $__evo_admin_email);
					}

				// return form footer
					$form = new evoau_form();
				
				return array(
					'status'=>'good',
					'msg'=>'',
					'success_message_html' => $form->get_form_success_html( 
						$this->formtype, (isset($post_data['form_atts_data']) ? $post_data['form_atts_data'] : array() ) )
				);
			}else{
				// could not create custom post type
				return array('status'=>'bad','msg'=>'nof4');
			}
		}
	
	/** Create the event post	 */
		function create_post() {
			
			// event post status
			$opt_draft = (!empty($this->evoau_opt['evoau_post_status']))?
				$this->evoau_opt['evoau_post_status']:'draft';

				// override new event publish status if the user can submit events
					if( !evo_settings_check_yn($this->evoau_opt,'evoau_dis_permis_status') ){
						if($opt_draft=='draft' && (current_user_can('publish_eventons') ))
							$opt_draft = 'publish';
					}
				
	        $type = 'ajde_events';
	        $valid_type = (function_exists('post_type_exists') &&  post_type_exists($type));

	        if (!$valid_type) {
	            $this->log['error']["type-{$type}"] = sprintf(
	                'Unknown post type "%s".', $type);
	        }

	        //print_r($_POST);
	        //echo 'ttt';
	        $__post_content = (!empty($_POST['event_description']))?
	        	$this->filter_post_content($_POST['event_description']): '';

	        return $this->HELP->create_posts(array(
				'post_type'=>$type,
				'post_title'=> isset($_POST['event_name'])? wp_strip_all_tags($_POST['event_name']):'Event',
				'post_status'=>$opt_draft,
				'post_content'=>$__post_content,
			));     
	    }
	    // filter for post content
	    function filter_post_content($content){
	    	return wp_kses_post(convert_chars(stripslashes($content)) );
	    }
		function create_custom_fields($post_id, $field, $value) { 
			if($this->formtype=='new'){
				add_post_meta($post_id, $field, $value);
			}else{
				update_post_meta($post_id, $field, $value);
			}	        
	    }
	    function set_new_term($term, $taxonomy, $post_id){
	    	$TERMEXIST = term_exists($term, $taxonomy);
	    	$data = $termID = '';

	    	// Setting the tax term to event
		    	if($TERMEXIST !== 0 && $TERMEXIST !== null){
		    		wp_set_object_terms( $post_id, $term, $taxonomy);
		    		$termID = (int)$TERMEXIST['term_id'];
		    	// create the new term
		    	}else{
		    		$slug = str_replace(' ', '-', $term);
		    		$newTerm = wp_insert_term(
					  	$term, // the term 
					  	$taxonomy, // the taxonomy
					  	array(	'slug'=>$slug 	)
					);
					if(!is_wp_error($newTerm)){
						$termID = (int)$newTerm['term_id'];
						wp_set_object_terms($post_id, $termID, $taxonomy, false);
					}
		    	}

	    	// update/ save term meta
	    		if(!empty($termID)){
	    			if($taxonomy =='event_organizer'){
						$term_meta = array();

						if(isset($_POST['evcal_org_contact'])) $term_meta['evcal_org_contact'] = $_POST['evcal_org_contact'];
						if(isset($_POST['evcal_org_address'])) $term_meta['evcal_org_address'] = $_POST['evcal_org_address'];
						if(isset($_POST['evcal_org_exlink'])) $term_meta['evcal_org_exlink'] = $_POST['evcal_org_exlink'];

						evo_save_term_metas($taxonomy, $termID, $term_meta);
					}

					// Location
					if($taxonomy == 'event_location'){
						$term_meta = $latlon = $cord = array();

						// generate coordinates for address
							if(!empty($_POST['location_address'])){
								$latlon = eventon_get_latlon_from_address($_POST['location_address']);
							}
						
						// if coordinates submitted from form
						if(!empty($_POST['event_location_cord'])){
							$cord = explode(',', $_POST['event_location_cord']);						
						}

						// longitude
						$term_meta['location_lon'] = (!empty($cord[1]))? $cord[1]:
							(!empty($latlon['lng'])? floatval($latlon['lng']): null);

						// latitude
						$term_meta['location_lat'] = (!empty($cord[0]))? $cord[0]:
							(!empty($latlon['lat'])? floatval($latlon['lat']): null);

						if(isset($_POST['location_address'])) $term_meta['location_address'] = $_POST['location_address'];
						if(isset($_POST['evcal_location_link'])) $term_meta['evcal_location_link'] = $_POST['evcal_location_link'];

						// location image
						if(!empty($_POST['evo_loc_img_id']))
							$this->create_custom_fields($post_id, 'evo_loc_img', $_POST['evo_loc_img_id']);

						//update_option("taxonomy_".$NEWTERMID['term_id'], $term_meta);					
						evo_save_term_metas($taxonomy, $termID, $term_meta);
					}
	    		}
	    }
	    // check if the submitted link data have complete url if not make it http:// url
	    function convert_links_to_proper($linkData){
	    	if(strpos($linkData, 'http')!== false){
	    		return $linkData;
	    	}else{
	    		$linkData =str_replace('http://', '', $linkData);
	    		$linkData =str_replace('http:/', '', $linkData);
	    		$linkData =str_replace('http', '', $linkData);
	    		$linkData =str_replace('://', '', $linkData);
	    		return 'http://'.$linkData;
	    	}
	    }

	// EMAILING
	// ACTUAL SENDING OF EMAIL
		function send_email($to, $from, $subject, $message){
			$helper = new evo_helper();	

			$send_wp_mail = $helper->send_email(array(
				'to'=>$to,
				'subject'=>$subject,
				'message'=> $message,
				'from'=>$from,
				'html'=>'yes'
			));

			return $send_wp_mail;
		}
	// when event is published or apporved and published
		function send_approval_email($new_status, $old_status, $post){

			$post_type  = get_post_type($post->ID);
			if( $post_type !== 'ajde_events' )
       			return;

			if($old_status == 'draft' && $new_status == 'publish'){

				$pmv = get_post_custom($post->ID);
				$event_id = $post->ID;
				//$this->create_custom_fields($event_id, 'tester', 'sendOut');

				// settings set to send approval email notifications and the event is set to notify upon event approval (publish) 
				if(!empty($this->evoau_opt['evoau_notsubmitterAP']) 
					&& ($this->evoau_opt['evoau_notsubmitterAP'])=='yes' 
					&& ( !empty($pmv['_send_publish_email']) && $pmv['_send_publish_email'][0]=='true')
					&&  !empty($pmv['_submitter_email']) 
				){									

					//$this->create_custom_fields($event_id, 'tester', 'send');

					$to = $pmv['_submitter_email'][0];

					$from = (!empty( $this->evoau_opt['evoau_ntf_pub_from'])) ? htmlspecialchars_decode($this->evoau_opt['evoau_ntf_pub_from']) : get_option('admin_email');

					$subject = (!empty( $this->evoau_opt['evoau_ntf_pub_subject'])) ? $this->evoau_opt['evoau_ntf_pub_subject'] : 'We have approved your event!';

					$_message = (!empty( $this->evoau_opt['evoau_ntf_pub_msg'])) ? stripslashes($this->evoau_opt['evoau_ntf_pub_msg']) : 'Thank you for submitting your event and we have approved it!';

					$message = $this->_get_email_body($_message, $event_id);

					$send_wp_mail = $this->send_email($to, $from, $subject, $message);

					// Update event meta to not send publish email again
					update_post_meta($event_id, '_send_publish_email', 'no');
					
					return $send_wp_mail;
				}
			}
		}

	// send email notification of new events to ADMIN
		function send_au_email_notif($event_id, $admin_email){

			$__evo_admin_email = $admin_email;				

			if(!empty($this->evoau_opt['evoau_notif']) && ($this->evoau_opt['evoau_notif'])=='yes'){
				
				$to = (!empty( $this->evoau_opt['evoau_ntf_admin_to'])) ? htmlspecialchars_decode($this->evoau_opt['evoau_ntf_admin_to']):$__evo_admin_email;

				// From email
					$from = (!empty( $this->evoau_opt['evoau_ntf_admin_from']) && strpos($this->evoau_opt['evoau_ntf_admin_from'], '@') !== false) ? 
						htmlspecialchars_decode($this->evoau_opt['evoau_ntf_admin_from']) : $__evo_admin_email;

				$subject = (!empty( $this->evoau_opt['evoau_ntf_admin_subject'])) ? $this->evoau_opt['evoau_ntf_admin_subject'] : 'New Event Submission';

				$_message = (!empty( $this->evoau_opt['evoau_ntf_admin_msg'])) ? stripslashes($this->evoau_opt['evoau_ntf_admin_msg']) : 'You have a new event submission!';

				$message = $this->_get_email_body($_message, $event_id);

				$send_wp_mail = $this->send_email($to, $from, $subject, $message);

				//update_post_meta(25518,'aa', $to.'> '.$from.'> '.$message);
				
				return $send_wp_mail;

			}
		}

	// send email to event submitter
		function send_submitter_email_notif($event_id, $admin_email){
			$__evo_admin_email = $admin_email;	
			
			if(!empty($this->evoau_opt['evoau_notsubmitter']) && ($this->evoau_opt['evoau_notsubmitter'])=='yes' ){
				
				// current user if there is any
				$current_user = wp_get_current_user();

				if(!empty($current_user->user_email) || (!empty($_POST['youremail']) && isset($_POST['youremail'])) ){

					// use the correct email address logged in email first and then submitted email
					$to = (!empty($current_user->user_email))? $current_user->user_email: $_POST['youremail'];

					$from = (!empty( $this->evoau_opt['evoau_ntf_user_from'])) ? htmlspecialchars_decode($this->evoau_opt['evoau_ntf_user_from']) : $__evo_admin_email;

					$subject = (!empty( $this->evoau_opt['evoau_ntf_drf_subject'])) ? $this->evoau_opt['evoau_ntf_drf_subject'] : 'We have received your event!';

					$_message = (!empty( $this->evoau_opt['evoau_ntf_drf_msg'])) ? stripslashes($this->evoau_opt['evoau_ntf_drf_msg']) : 'Thank you for submitting your event!';
					
					$message = $this->_get_email_body($_message, $event_id);

					// hook
					do_action('evoau_before_sending_submitter_notification_email');

					$send_wp_mail = $this->send_email($to, $from, $subject, $message);
					
					return $send_wp_mail;
				}

			}
			
		}

	// GET email body for messages
		function _get_email_body($message, $eventid=''){
			global $eventon, $eventon_au;

			$adminurl = get_admin_url();
			$editlink = $adminurl."post.php?post={$eventid}&action=edit";

			// get post fields
				$post = !empty($_POST)? $_POST: array();

			// event data
				$pmv = false;
				if(!empty($eventid)){
					$pmv = get_post_custom($eventid);

					$wp_time_format = get_option('time_format');
					$wp_date_format = get_option('date_format');

					$evcal_srow = !empty($pmv['evcal_srow'])? $pmv['evcal_srow'][0]:false;
					$evcal_erow = !empty($pmv['evcal_erow'])? $pmv['evcal_erow'][0]:false;
					if($evcal_srow){
						$post['event_start_time'] = date_i18n($wp_time_format, $evcal_srow);
						$post['event_start_date'] = date_i18n($wp_date_format, $evcal_srow);
					}
					if($evcal_erow){
						$post['event_end_time'] = date_i18n($wp_time_format, $evcal_erow);
						$post['event_end_date'] = date_i18n($wp_date_format, $evcal_erow);
					}	

					// other data
					if(!empty($pmv['_submitter_name']))	$post['yourname'] = $pmv['_submitter_name'][0];			
					if(!empty($pmv['_submitter_email']))	$post['youremail'] = $pmv['_submitter_email'][0];			
				}

			// process body tags for email message 
				$message =str_replace('{event-edit-link}', $editlink, $message);
				$message =str_replace('{event-name}', get_the_title($eventid), $message);
				$message =str_replace('{event-link}', get_permalink($eventid), $message);

				foreach(array(
					'yourname'=>'submitter-name',
					'youremail'=> 'submitter-email',
					'event_end_time'=>'event-end-time',
					'event_end_date'=>'event-end-date',
					'event_start_date'=>'event-start-date',
					'event_start_time'=>'event-start-time',
					'newline'=>'new-line',
				) as $field=>$value){

					if($field == 'newline'){
						$message = str_replace('{'.$value.'}', '<br/>', $message);
					}
					$message = (isset($post[$field]))?
						str_replace('{'.$value.'}', $post[$field], $message):
						str_replace('{'.$value.'}', '', $message);
				}	

			$this->message = html_entity_decode($message);			
			$path = $eventon_au->plugin_path.'/templates/';

			ob_start();
			$file_location = EVO()->template_locator(
				'notif_email.php', 
				$eventon_au->plugin_path."/templates/", 
				'/templates/email/actionuser/'
			);
			include($file_location);

			return ob_get_clean();
		}
}




