<?php
/**
 * Evoau front end submission form
 * @version 2.3
 */
class evoau_form{

private $form_lang = 'L1';
public $EVENT = false;
private $form_type = 'new';

function __construct(){
	$this->opt_1= get_option('evcal_options_evcal_1');
	$this->opt_2 = get_option('evcal_options_evcal_2');

	EVO()->cal->load_more('evoau_1');

	$this->auop_1 = EVOAU()->frontend->evoau_opt;
}

// FORM
	// form fields
	// field order has all the fields in it
	function _get_form_field_order(){
		$evoopt= $this->auop_1;
		$FIELD_ORDER = !empty($evoopt['evoau_fieldorder'])? array_filter(explode(',',$evoopt['evoau_fieldorder'])): false;
		return $FIELD_ORDER;
	}

	// shows only selected fields not in order
	function _get_form_selected_fields(){
		$evoopt= $this->auop_1;
		return (!empty($evoopt['evoau_fields']))?
		( (is_array($evoopt['evoau_fields']) && count($evoopt['evoau_fields'])>0 )? $evoopt['evoau_fields']:
			array_filter(explode(',', $evoopt['evoau_fields']))):
		false;
	}


// FORM CONTENT 
	function get_content($event_id ='', $atts='', $form_field_permissions=array(), $override_pluggable_check= false, $atts2=''){

		global $eventon;

		// form default arguments
			$defaults = array(
				'lang'=>'L1',
				'lightbox'=>'no',
				'msub'=>'no',
				'rlink'=>'',		
				'rdir'=>'no',		
				'calltype'=>'new',
				'wordcount'=>0,
				'formrtl'=>'no'
			);

		//add_post_meta(527,'aa','fre+fre');
		$atts = !empty($atts)? array_merge($defaults, $atts): $defaults;
		$hidden_fields = array(
			'action'=>'evoau_event_submission',
			'evoau_v'=> EVOAU()->version
		);
		$hidden_fields = !empty($atts['hidden_fields'])? array_merge($hidden_fields, $atts['hidden_fields']): $hidden_fields;
		
		// set current calendar settings
		EVO()->cal->set_cur('evoau_1');

		$evoopt= EVOAU()->frontend->evoau_opt;
		$evoopt_1= $this->opt_1;
		$opt_2 = $this->opt_2;

		$FIELD_ORDER = $this->_get_form_field_order();	
		$SELECTED_FIELDS = $this->_get_form_selected_fields();

		ob_start();

		// INIT Values
			// the form type
				$_EDITFORM = ($atts['calltype']=='edit' && !empty($event_id))? true:false;

			// language for the form fields & set as global language
				$lang = $this->form_lang = (!empty($atts['lang'])? $atts['lang']:'L1');
				$hidden_fields['_evo_lang'] = $lang;
				evo_set_global_lang($lang);

			// login required
				//$_USER_login_required = evo_settings_check_yn($evoopt,'evoau_access');
				$_USER_LOGIN_REQ = (evo_settings_check_yn($evoopt,'evoau_access') && !is_user_logged_in())? true:false;

			// Check loggedin user have submission permissions
				$_USER_CAN = true;

				if( !$_USER_LOGIN_REQ && evo_settings_check_yn($evoopt,'evoau_access')){
					if( evo_settings_check_yn($evoopt, 'evoau_access_role') && !current_user_can('submit_new_events_from_submission_form')){
						$_USER_CAN = false;
						$_USER_LOGIN_REQ = true;
					}
				}

			// if edit form and has event id create event object
				if($_EDITFORM){
					$this->EVENT = new EVO_Event($event_id);
					$this->form_type = 'edit';
				}
			
			//if shortcode arguments passed
				$atts = !empty($atts)? $atts: false;
				$_LIGTHBOX = ($atts && !empty($atts['lightbox']) && $atts['lightbox']=='yes')? true:false;
				$_LIGTHBOX = (!$_LIGTHBOX && $atts && !empty($atts['ligthbox']) && $atts['ligthbox']=='yes')? true:$_LIGTHBOX;
				$_msub = ($atts && !empty($atts['msub']) && $atts['msub']=='yes')? true:false;

			
			// limit submissions to one
				$LIMITSUB = $this->_is_limit_form_submission_restriction();

			
			// before showing the form
			// Ability for pluggable functions to display other content instead of form
				
				$pluggable_check = apply_filters('evoau_form_display_check',true, $event_id, $_EDITFORM, $atts);

				if(!$pluggable_check && !$override_pluggable_check){
					do_action('evoau_form_before', $atts);
					return ob_get_clean();
				} 

		$rand = rand(10000,99999);

	?>
	<div class='eventon_au_form_section <?php echo ($_LIGTHBOX)?'overLay':'';?>' style='display:<?php echo $_LIGTHBOX?'none':'block';?>'>
		

	<div id='eventon_form_<?php echo $rand;?>' class='evoau_submission_form <?php echo ($_USER_LOGIN_REQ?'loginneeded':'') . ' '. ($LIMITSUB && !$_EDITFORM ?' limitSubmission':'').' '.($_LIGTHBOX?'lightbox':''). ( $atts['formrtl']=='yes'? ' evortl':''); ?>' >
		<a class='closeForm'>X</a>
		<form method="POST" action="" enctype="multipart/form-data" id='evoau_form' class='' data-msub='<?php echo ($_msub)?'ow':'nehe';?>' data-redirect='<?php echo ($atts && !empty($atts['rlink']) && !empty($atts['rdir']) && $atts['rdir']=='yes')?$atts['rlink']:'nehe';?>' data-rdur='<?php echo $this->val_check($atts,'rdur');?>' data-limitsubmission='<?php echo (!empty($evoopt['evoau_limit_submissions']) && $evoopt['evoau_limit_submissions']=='yes')?'ow':'nehe';?>' data-enhance="false">
			
		<?php 		
			// hidden fields for the form
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();
					$hidden_fields['_current_user_id'] = $current_user->ID;
				} 

				// for only edit forms
				if($_EDITFORM){
					$hidden_fields['form_action'] = 'editform';
					$hidden_fields['eventid'] = $event_id;
					$hidden_fields['evoau_limit_submissions'] = (!empty($evoopt['evoau_limit_submissions'])? $evoopt['evoau_limit_submissions']:'no');
				}else{
					$hidden_fields['form_action'] = 'newform';
				}

				foreach(apply_filters('evoau_form_hidden_fields', $hidden_fields) as $key=>$val){
					echo "<input type='hidden' name='{$key}' value='{$val}'/>";
				}
		?>
		<?php 	wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoau_noncename' );	?>
			
			<div class='evoau_form_fields inner' style='display:<?php echo $LIMITSUB && !$_EDITFORM ?'none':'block';?>'>
			
			<h2><?php echo 
				$_EDITFORM? 
					eventon_get_custom_language($opt_2, 'evoAUL_ese', 'Edit Submitted Event', $lang):
					(
						($atts && !empty($atts['header']) )? 
							stripslashes($atts['header']):  
							( ( !empty($evoopt['evo_au_title']) )? 
								stripslashes($evoopt['evo_au_title']):
								'Submit your event'		
							)				
					);?></h2>
			<?php
				// form subtitle text
				$SUBTITLE = ($atts && !empty($atts['sheader']))? $atts['sheader']:
					(!empty($evoopt['evo_au_stitle'])? $evoopt['evo_au_stitle']: false);
				echo ($SUBTITLE)? '<h3>'.stripslashes($SUBTITLE).'</h3>':null;?>		
			<?php

			// display event post publish status for editing events
				if($_EDITFORM && !empty($event_id)){
					$pub_status = get_post_status($event_id);
					echo "<p class='event_post_status'>". evo_lang('Event Publish Status').': <b>' . evo_lang($pub_status)."</b></p>";
				}

			do_action('evoau_submit_form_under_title', $_EDITFORM, $this->EVENT);

				// event post status
			/*?><h4><?php evo_lang_e('Event Post Status');?>: <?php echo get_post_status($event_id);?></h4><?php*/
			
		//access control to form
			if( $_USER_LOGIN_REQ ):
				// current logged in user does not have permission to submit events
				if( !$_USER_CAN):
					$this->get_form_access_restricted('permission');
				else:
					$this->get_form_access_restricted('login');				
				endif;		
			?>
			</div><!-- inner -->
			<?php

			else: // not loggedin
		?>
			<div class='evoau_table'>
			<?php	
				// initials			
					$EPMV = '';
					if($this->EVENT)	$EPMV = $this->EVENT->get_data();	
								
				// form messages
					echo "<div class='form_msg' style='display:none'></div>";
				
				// get all the fields after processing
					$FORM_FIELDS = EVOAU()->frontend->au_form_fields();	
					$EACH_FIELD = $this->process_form_fields_array( 
						apply_filters('evoau_form_field_permissions_array', $form_field_permissions, $_EDITFORM, $event_id),
						$FIELD_ORDER
					);
				
				// if the user is loggedin
					if(is_user_logged_in() ) $current_user = wp_get_current_user();

				// before form fields action
					do_action('evoau_before_submission_form_fields', $_EDITFORM, $EPMV);

				// skip fields for the form
					$FORM_SKIPS = apply_filters('evoau_form_skip_fields',array('event_special_edit'));

				// Create event object if its edit event 
					$EVENT = '';

				// DATE/ TIME data for fields
					// force wp date format to be used for date format creation
					$evcal_date_format = eventon_get_timeNdate_format('',true);
					
					$dateFormat = $evcal_date_format[1];
						//$dateFormat = ($dateFormat=='d/m/Y')? 'm/d/Y':$dateFormat;

					// date format in JS compatible value
					$dateFormatJS = $evcal_date_format[0];				
						//$dateFormatJS = ($dateFormatJS=='dd/mm/yy')? 'mm/dd/yy':$dateFormatJS;
					
					// the fixed date format to save the selected value
					$fixed_date_format = 'Y-m-d';
					


					$sow = get_option('start_of_week');	
					$wp_time_format = get_option('time_format');
					$evo_date_format = EVO()->cal->get_date_format();

					$hr24 = (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? true:false;	
					$time_hour_span= $hr24 ? 25:13;
					$minIncre = !empty($evoopt['evo_minute_increment'])? (int)$evoopt['evo_minute_increment']:60;
					$minADJ = 60/$minIncre;	
					$timeFormat = ($evcal_date_format[2])? 'H:i':'h:i:a';
					$isAllDay = ($EPMV && !empty($EPMV['evcal_allday']) && $EPMV['evcal_allday'][0]=='yes')? true:false;		

				// disable edit capabilities for date and time fields
					$disable_date_editing = apply_filters('evoau_datetime_editing', false, $_EDITFORM, $EPMV);

				// date picker language values
					$this->_print_date_picker_text();

					//print_r($EACH_FIELD);

				// EACH field array from EVOAU()->au_form_fields()
					foreach(apply_filters('evoau_form_fields_array',$EACH_FIELD)  as $__index=>$ff):

						if(in_array($ff, $FORM_SKIPS)) continue;

						$INDEX = (!empty($FIELD_ORDER))? $ff:$__index;

						if( ($SELECTED_FIELDS && in_array($INDEX, $SELECTED_FIELDS) )
							|| in_array($INDEX, EVOAU()->frontend->au_form_fields('defaults_ar')) 
						){						

							// get form array for the field parameter
								if(empty($FORM_FIELDS[$INDEX])) continue;

								$field = $FORM_FIELDS[$INDEX];

								$__field_name = (!empty($field[4]))?  
									eventon_get_custom_language($opt_2, $field[4], $field[0], $lang) :
									evo_lang($field[0]);

								
								$__field_type = $field[2];
								$_placeholder = (!empty($field[3]))? __($field[3],'eventon'):null;
								$__field_id =$field[1];
								$__req = (!empty($field[5]) && $field[5]=='req')? ' *':null;
								$__req_ = (!empty($field[5]) && $field[5]=='req')? ' req':null;
						
							// dont show name and email field is user is logged in
								if(is_user_logged_in() && ($INDEX=='yourname' || $INDEX=='youremail') && !empty($current_user) ){

									if($INDEX=='yourname')
										echo "<input type='hidden' name='yourname' value='{$current_user->display_name}'/>";
									if($INDEX=='youremail')
										echo "<input type='hidden' name='youremail' value='{$current_user->user_email}'/>";

									continue;
								}

							// default value for fields
								$default_val = (!empty($_POST[$__field_id]))? $_POST[$__field_id]: null;
								if($EPMV){								
								 	$default_val = !empty($EPMV[$__field_id])? $EPMV[$__field_id][0]:$default_val;
								}


							// switch statement for dif fields
							switch($__field_type){
								// pluggable
									case has_action("evoau_frontform_{$__field_type}"):
										do_action('evoau_frontform_'.$__field_type, $field, $event_id, $default_val, $EPMV, $opt_2, $lang, $this);
									break;

								// default fields
									case 'title':
										if($EPMV)
											$default_val = get_the_title($event_id);
										echo "<div class='row title'>
											<p class='label'>
											<input id='_evo_date_format' type='hidden' name='_evo_date_format' jq='".$dateFormatJS."' value='". $fixed_date_format. "'/>
											<input id='_evo_time_format' type='hidden' name='_evo_time_format' value='".(($evcal_date_format[2])?'24h':'12h')."'/>
											<label for='event_name'>".$__field_name." <em>*</em></label></p>
											<p><input type='text' class='fullwidth req' name='event_name' value='".$default_val."' data-role='none'/></p>
										</div>";
									break;
									case 'startdate':

										if($_EDITFORM){
											?><div class='evoau_sh_row datetime'>
												<p class='evoau_sh_label'><label><?php evo_lang_e('Event Date & Time');?><span class='evcal_btn evoau_sh_toggle'><?php evo_lang_e('Edit');?></span></label></p>
											<div class='evoau_sh_content evoau_sub_formfield' style='display:none'><?php
										}

										// date time value
										$SD = ($EPMV)? date($dateFormat, (int)$EPMV['evcal_srow'][0]):
											((!empty($_POST['event_start_date']))? $_POST['event_start_date']: null);
										$SDX = ($EPMV)? date($fixed_date_format, (int)$EPMV['evcal_srow'][0]):
											((!empty($_POST['event_start_date_x']))? $_POST['event_start_date_x']: null);
										
										$hour = $minute = $ampm = null;
										if($EPMV){
											$hour = date( ($hr24 ?'H':'h'), (int)$EPMV['evcal_srow'][0]);
											$minute = date('i', (int)$EPMV['evcal_srow'][0]);
											$ampm = date('a', (int)$EPMV['evcal_srow'][0]);
										} 

										// lang corrected date
										if(isset($EPMV['evcal_srow']) ){
											$SD = eventon_get_langed_pretty_time($EPMV['evcal_srow'][0], $dateFormat);
										}

										if( !$this->_can_edit_date_time() ){
											$ST = ($EPMV)? date($timeFormat, (int)$EPMV['evcal_srow'][0]):
											((!empty($_POST['event_start_time']))? $_POST['event_start_time']: null);

											echo "<div class='row start_date '>
												<p class='label'><label for='event_start_date'>".$__field_name." *</label></p>";
												echo "<p>". $SD.' '.$ST . "</p>";
											echo "</p></div>";
										}else{
											
											echo "<div class='row start_date event_datetime'>
												<p class='label'><label for='event_start_date'>".$__field_name." *</label></p>
												<p>";

												$this->_print_date_time_picker(array(
													'disable_date_editing'=>$disable_date_editing,
													'rand'=>$rand,
													'sow'=>$sow,
													'time_hour_span'=>$time_hour_span,
													'minIncre'=>$minIncre,
													'minADJ'=>$minADJ,
													'hr24'=>$hr24,
													'isAllDay'=>$isAllDay,
													'date_val'=>$SD,
													'date_val_x'=>$SDX,
													'hour'=>$hour,
													'minute'=>$minute,
													'ampm'=>$ampm,
													'type'=>'start',
													'assoc'=>'reg',
													'names'=>true, 'required'=> true,
												));	
													
												echo "</p>
											</div>";
										}
									break;
									case 'enddate':
										$isAllDay = ($this->EVENT && $this->EVENT->is_all_day() )? 'display:none': '';
										$hideEnd = (!empty($EPMV['evo_hide_endtime']) && $EPMV['evo_hide_endtime'][0]=='yes')? 'display:none': '';
										$hideVirEnd = (!empty($EPMV['_evo_virtual_endtime']) && $EPMV['_evo_virtual_endtime'][0]=='yes')? 'display:block': 'display:none';
										
										$ED = ($EPMV)? date($dateFormat, $EPMV['evcal_erow'][0]):
											((!empty($_POST['event_end_date']))? $_POST['event_end_date']: null);
										$EDX = ($EPMV)? date($fixed_date_format, $EPMV['evcal_erow'][0]):
											((!empty($_POST['event_end_date_x']))? $_POST['event_end_date_x']: null);

										$hour = $minute = $ampm = null;
										if($EPMV){
											$hour = date( ($hr24 ?'H':'h'), (int)$EPMV['evcal_erow'][0]);
											$minute = date('i', (int)$EPMV['evcal_erow'][0]);
											$ampm = date('a', (int)$EPMV['evcal_erow'][0]);
										}

										// lang corrected date
										if(isset($EPMV['evcal_erow']) ){
											$ED = eventon_get_langed_pretty_time($EPMV['evcal_erow'][0], $dateFormat);
										}

										if( !$this->_can_edit_date_time()){
											$ET = ($EPMV)? date($timeFormat, (int)$EPMV['evcal_erow'][0]):
											((!empty($_POST['event_end_date']))? $_POST['event_end_date']: null);

											echo "<div class='row end_date ' id='evoAU_endtime_row' style='{$hideEnd}'>
												<p class='label'><label for='event_start_date'>".$__field_name." *</label></p>";
												echo "<p>". $ED.' '.$ET . "</p>";
											echo "</p></div>";
										}else{
											
											echo "<div class='row end_date event_datetime' id='evoAU_endtime_row' style='{$hideEnd}'>
												<p class='label'><label for='event_end_date'>".$__field_name." *</label></p>
												<p>";
												$this->_print_date_time_picker(array(
													'disable_date_editing'=>$disable_date_editing,
													'rand'=>$rand,
													'sow'=>$sow,
													'time_hour_span'=>$time_hour_span,
													'minIncre'=>$minIncre,
													'minADJ'=>$minADJ,
													'hr24'=>$hr24,
													'date_val'=>$ED,
													'date_val_x'=>$EDX,
													'hour'=>$hour,
													'minute'=>$minute,
													'ampm'=>$ampm,
													'type'=>'end',
													'assoc'=>'reg',
													'names'=>true, 'required'=> true,
												));	
												echo "</p>
											</div>";

											// virtual visible end time
											$VD = $VDX = '';
											$hour = $minute = $ampm = null;

											if( $this->EVENT){
												$_VD = $this->EVENT->is_virtual_end();
												if( $_VD){
													$VD = date($dateFormat,$_VD);
													$VDX = date($fixed_date_format,$_VD);

													$hour = date( ($hr24 ?'H':'h'), $_VD );
													$minute = date('i', $_VD );
													$ampm = date('a', $_VD );
												}
											}

											echo "<div class='row vir_end_date event_datetime' id='evoAU_virendtime_row' style='{$hideVirEnd}'>
												<p class='label'><label for='event_vir_date'>". evo_lang('Virtual visible end date/time')." *</label></p>
												<p>";
												$this->_print_date_time_picker(array(
													'disable_date_editing'=>$disable_date_editing,
													'rand'=>$rand,
													'sow'=>$sow,
													'time_hour_span'=>$time_hour_span,
													'minIncre'=>$minIncre,
													'minADJ'=>$minADJ,
													'hr24'=>$hr24,
													'date_val'=>$VD,
													'date_val_x'=>$VDX,
													'hour'=>$hour,
													'minute'=>$minute,
													'ampm'=>$ampm,
													'type'=>'vir',
													'assoc'=>'reg',
													'names'=>true, 'required'=> false
												));	
												echo "</p>
											</div>";
										}


										

									break;				

								case 'allday':
									$helper = new evo_helper();
									
									echo "<div class='row allday_noendtime'>
										<p class='label'>";
									echo $helper->html_yesnobtn(array(
										'id'=>'evcal_allday',
										'input'=>true,
										'label'=>eventon_get_custom_language($opt_2, 'evoAUL_001', 'All Day Event', $lang),
										'var'=> (($EPMV && !empty($EPMV['evcal_allday']) && $EPMV['evcal_allday'][0]=='yes')?'yes':'no'),
										'lang'=>$lang
									));
									echo "</p>";

									echo "<p class='label' style='padding-top:5px'>";
									echo $helper->html_yesnobtn(array(
										'id'=>'evo_hide_endtime',
										'input'=>true,
										'label'=>eventon_get_custom_language($opt_2, 'evoAUL_002', 'No end time', $lang),
										'var'=> (($EPMV && !empty($EPMV['evo_hide_endtime']) && $EPMV['evo_hide_endtime'][0]=='yes')?'yes':'no'),
										'lang'=>$lang
									));
									echo "</p>";

									// if virtual event end time is allowed
									if( EVO()->cal->check_yn('evoau_allow_vir_enddate','evoau_1') && 
										$this->_can_edit_date_time()
									){
										echo "<p class='label' style='padding-top:5px'>";
										echo $helper->html_yesnobtn(array(
											'id'=>'_evo_virtual_endtime',
											'input'=>true,
											'label'=> evo_lang('Enable virtual visible event end time [Beta]'),
											'var'=> (($EPMV && !empty($EPMV['_evo_virtual_endtime']) && $EPMV['_evo_virtual_endtime'][0]=='yes')?'yes':'no'),
											'lang'=>$lang
										));
										echo "</p>";
									}

									echo "</div>";

									// if set to hide repeating fields from the form
									if(!empty($evoopt['evoau_hide_repeats']) && $evoopt['evoau_hide_repeats']=='yes'){}else{
										
										echo "<div class='row evoau_repeating'><p>";
										$evcal_repeat = ($EPMV && !empty($EPMV['evcal_repeat']) && $EPMV['evcal_repeat'][0]=='yes')? true: false;
										echo $helper->html_yesnobtn(array(
											'id'=>'evcal_repeat',
											'input'=>true,
											'label'=>eventon_get_custom_language($opt_2, 'evoAUL_ere1', 'This is a repeating event', $lang),
											'var'=> ($evcal_repeat?'yes':'no'),
											'lang'=>$lang
										));
										echo "</p></div>";

										// saved values for edit form
											$evcal_rep_freq = ($this->EVENT && $this->EVENT->get_prop('evcal_rep_freq'))? $this->EVENT->get_prop('evcal_rep_freq'):false;
											$evcal_rep_gap = ($EPMV && !empty($EPMV['evcal_rep_gap']))? $EPMV['evcal_rep_gap'][0]:false;
											$evcal_rep_num = ($EPMV && !empty($EPMV['evcal_rep_num']))? $EPMV['evcal_rep_num'][0]:false;

											$repeat_gap_text = evo_lang('Days');

										echo "<div class='row row_2' id='evoau_repeat_data' style='display:".($evcal_repeat?'':'none')."'>

											<p class='evoau_repeat_frequency'>
												<label>".eventon_get_custom_language($opt_2, 'evoAUL_ere5', 'Event Repeat Type', $lang)."</label>
												<span class='repeat_type'>
													<input type='hidden' name='evcal_rep_freq' value='{$evcal_rep_freq}'/>";

													$R_array = array(
														'daily'=> array(evo_lang_get('evoAUL_ere2', 'Daily'), evo_lang('Days')),
														'weekly'=> array(evo_lang_get('evoAUL_ere3', 'Weekly'),evo_lang('Weeks') ),
														'monthly'=> array(evo_lang_get('evoAUL_ere4', 'Monthly'), evo_lang('Months') ),
														'yearly'=> array( evo_lang_get('evoAUL_ere4y', 'Yearly'),evo_lang('Years') ),
														'custom'=> array( evo_lang('Custom') ,'custom'),
													);

													foreach($R_array as $F=>$V){
														$_sel = '';
														if( $evcal_rep_freq == $F || (!$evcal_rep_freq && $F=='daily') ){
															$_sel ='select';
															$repeat_gap_text = $V[1];
														}

														echo "<span class='evo_repeat_type_val {$_sel}' data-val='{$F}' data-v='{$V[1]}'>{$V[0]}</span>";
													}
												echo "</span>	
											</p>

											<div class='evo_preset_repeat_settings' style='display:".( $evcal_rep_freq =='custom'? 'none':'block')."'>
												<p class='evcal_rep_gap'>
													<input type='number' name='evcal_rep_gap' min='1' placeholder='1' value='".($evcal_rep_gap? $evcal_rep_gap:'1')."' data-role='none'/>
													<label>".eventon_get_custom_language($opt_2, 'evoAUL_ere6', 'Gap Between Repeats', $lang)." (<i class='evcal_rep_gap_name'>{$repeat_gap_text}</i>)</label>
												</p>
												<p class='evcal_rep_num'>
													<input type='number' name='evcal_rep_num' min='1' placeholder='1' value='".($evcal_rep_num? $evcal_rep_num:'1')."' data-role='none'/>
													<label>".eventon_get_custom_language($opt_2, 'evoAUL_ere7', 'Number of Repeats', $lang)."</label>
												</p>
											</div>";

											// Custom Repeat
											echo "<div class='evo_custom_repeat_settings' style='display:".( $evcal_rep_freq =='custom'? 'block':'none')."'>
												<p>". evo_lang('Custom Repeat Times') ."</p>

												<ul class='evo_custom_repeat_list'>";
												$count = 1;

												$repeat_times = false;
												if($this->EVENT) $repeat_times = $this->EVENT->get_repeats();

												if($this->EVENT && is_array($repeat_times) && count($repeat_times)>0 ){												
													$date_format_string = $evo_date_format.' '.( $hr24? 'G:i':'h:ia');

													$event_start_unix = $this->EVENT->get_start_time();
													
													foreach($repeat_times as $rt){
														$startUNIX = (int)$rt[0];
														$endUNIX = (int)$rt[1];

														$initial = false;

														// skip times same as event time
														if( $startUNIX == $event_start_unix ) continue;

														echo '<li data-cnt="'.$count.'" style="display:'.(( $count>3)?'none':'block').'" class="'.($initial?'initial':'').($count>3?' over':'').'">'. ($initial? '<dd>'.__('Initial','eventon').'</dd>':'').'<span>'.__('from','eventon').'</span> '.date($date_format_string,$startUNIX).' <span class="e">End</span> '.date($date_format_string,$endUNIX).'<em alt="Delete">x</em>
														<input type="hidden" name="repeat_intervals['.$count.'][0]" value="'.$startUNIX.'"/><input type="hidden" name="repeat_intervals['.$count.'][1]" value="'.$endUNIX.'"/></li>';
														$count++;
													}
												}

												echo "</ul>";

												if($repeat_times) 
													echo  "<p class='evo_custom_repeat_list_count' data-cnt='{$count}' style='padding-bottom:20px'>".($count-1)." ". evo_lang('other repeat intervals exists'). " ". ($count>3? "<span class='evo_repeat_interval_view_all' data-show='no'>".__('View All','eventon')."</span>":'') ."</p>";


												// ADD new custom repeat
												echo "<div class='evo_repeat_interval_new' style='display:none' data-h24='".( $hr24? 'y':'n') ."'>";

												echo "<p>";
												echo "<span>". evo_lang('Start Date/Time') ."</span>";
												$_cr_date = current_time('timestamp');
												
												$this->_print_date_time_picker(array(
													'disable_date_editing'=>$disable_date_editing,
													'rand'=>$rand,
													'sow'=>$sow,
													'time_hour_span'=>$time_hour_span,
													'minIncre'=>$minIncre,
													'minADJ'=>$minADJ,
													'hr24'=>$hr24,
													'date_val'=> date($dateFormat, $_cr_date),
													'date_val_x'=> date($fixed_date_format, $_cr_date),
													'hour'=>'0',
													'minute'=>'00',
													'ampm'=>'am',
													'type'=>'start',
													'assoc'=>'rp',
													'names'=>false
												));	
												echo "</p>";

												echo "<p>";
												echo "<span>". evo_lang('End Date/Time') ."</span>";
												$_cr_date = current_time('timestamp');
												
												$this->_print_date_time_picker(array(
													'disable_date_editing'=>$disable_date_editing,
													'sow'=>$sow,
													'time_hour_span'=>$time_hour_span,
													'minIncre'=>$minIncre,
													'minADJ'=>$minADJ,
													'hr24'=>$hr24,
													'date_val'=> date($dateFormat, $_cr_date),
													'date_val_x'=> date($fixed_date_format, $_cr_date),
													'hour'=>'0',
													'minute'=>'00',
													'ampm'=>'am',
													'type'=>'end',
													'assoc'=>'rp',
													'names'=>false 
												));	
												echo "</p>";

												echo "<p><span class='err_msg' style='display:none'>". evo_lang('All fields are required') . "</span></p>"; 

												echo "</div>";


												echo "<p class='evo_repeat_interval_button'><a id='evo_add_repeat_interval' class='evcal_btn'>+ ". evo_lang('Add New Repeat Interval') ."</a><span></span></p>";

											echo "</div>";


										echo "</div>";
									}

									if($_EDITFORM ) echo "</div></div><!--evoau_sh_row-->";
								break;

							// Other general fields
								case 'text':
									$default_val = str_replace("'", '"', $default_val);

									echo $this->get_form_html($__field_id, array(
										'type'=>'text',
										'name'=>$__field_name,
										'placeholder'=>$_placeholder,
										'value'=>$default_val,
										'required_html'=>$__req,
										'required_class'=>$__req_
									));
									
								break;
								case 'html':
									$HTML = !empty($evoopt['evoau_html_content'])? $evoopt['evoau_html_content']: false;
									if($HTML){
										echo $this->get_form_html($__field_id, array(
											'type'=>'html',
											'html'=>$HTML,
										));
									}
								break;
								case 'button':
									echo "<div class='row'>
										<p class='label'><label for='".$__field_id."'>".$__field_name.' '.evo_lang('(Text)', $lang,$opt_2).' '.$__req."</label></p>
										<p><input type='text' class='fullwidth{$__req_}' name='".$__field_id."' ".$_placeholder." value='{$default_val}' data-role='none'/></p>
										<p class='label'><label for='".$__field_id."'>".$__field_name.' '.evo_lang('(Link)', $lang,$opt_2).' '.$__req."</label></p>
										<p><input type='text' class='fullwidth{$__req_}' name='".$__field_id."L' ".$_placeholder." value='".(!empty($EPMV[$__field_id."L"])? $EPMV[$__field_id."L"][0]:null)."' data-role='none'/></p>
									</div>";
								break;
								case 'textarea':
									// for event details field
									if($field[1]== 'event_description'){
										$event = get_post($event_id);
										if($event_id){
											setup_postdata($event);
											$content = $event->post_content;

											//$default_val = $eventon->frontend->filter_evo_content( $content );
											$default_val = $content;
											//$content = apply_filters('the_content', $content);
											//$default_val = str_replace(']]>', ']]&gt;', $content);
											//$default_val = $content;
											wp_reset_postdata();
										}else{
											$default_val = '';
										}
									}
									if($field[1]== 'event_description'){
										
										// USE basic text editor
										if(!empty(EVOAU()->frontend->evoau_opt['evoau_eventdetails_textarea']) && EVOAU()->frontend->evoau_opt['evoau_eventdetails_textarea']=='yes'){
											echo $this->get_form_html($__field_id, array(
												'type'=>'textarea',
												'name'=>$__field_name,
												'value'=>$default_val,
												'placeholder'=>$_placeholder
											));
										}else{
											echo $this->get_form_html($__field_id, array(
												'type'=>'textarea',
												'name'=>$__field_name,
												'value'=>$default_val,
												'editor'=>'wysiwyg',
												'placeholder'=>$_placeholder
											));
										// WYSIWYG editor
											/*$editor_id = (!empty($field[4])? $field[4]:'');
											$editor_var_name = 'event_description';
											$editor_args = array(
												'wpautop'=>true,
												'media_buttons'=>false,
												'textarea_name'=>$editor_var_name,
												'editor_class'=>'',
												'tinymce'=>true,
											);
											//echo "<div id='{$editor_id}' class='evoau_eventdetails'>".wp_editor($default_val, $editor_id, $editor_args)."</div>";*/
										}

									}else{
										echo $this->get_form_html($__field_id, array(
											'type'=>'textarea',
											'name'=>$__field_name,
											'value'=>$default_val,
											'placeholder'=>$_placeholder
										));
									}

								break;
								case 'color':

									// get the default color from eventon settings
									$defaultColor = !empty(EVOAU()->frontend->options['evcal_hexcode'])? EVOAU()->frontend->options['evcal_hexcode']: '8c8c8c';

									echo "<div class='row'>
										<p class='color_circle' data-hex='".(!empty($EPMV['evcal_event_color'])? $EPMV['evcal_event_color'][0]:$defaultColor)."' style='background-color:#".(!empty($EPMV['evcal_event_color'])? $EPMV['evcal_event_color'][0]:$defaultColor)."'></p>
										<p class='evoau_color_picker'>
											<input type='hidden' class='evcal_event_color' name='evcal_event_color' value='".(!empty($EPMV['evcal_event_color'])? $EPMV['evcal_event_color'][0]:$defaultColor)."'/>
											<input type='hidden' name='evcal_event_color_n' class='evcal_event_color_n' value='".(!empty($EPMV['evcal_event_color_n'])? $EPMV['evcal_event_color_n'][0]:'0')."'/>
											<label for='".$__field_id."'>".$__field_name."</label>
										</p>									
									</div>";
								break;
								case 'tax':
									// get all terms for categories
									$terms = get_terms($field[1], apply_filters('evoau_form_get_terms_'.$field[1], array('hide_empty'=>false, 'orderby'=>'name'))
									);

									if(count($terms)>0){
										echo "<div class='row'>
											<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p><p class='checkbox_row'>";
											
											// if edit form
											$slectedterms = array();
											if($_EDITFORM){
												$postterms = wp_get_post_terms($event_id, $field[1]);
												if(!empty($postterms)){
													foreach($postterms as $postterm)
														$slectedterms[] = $postterm->term_id;
												}
											}
											/*
											echo "<select multiple class='evoau_selectmul'>";
											foreach($terms as $term){											
												echo "<option ".( (count($slectedterms) && in_array($term->term_id, $slectedterms))? 'selected="selected"':null )." value='".$term->term_id."'>".$term->name."</option>";
											}
											echo "</select>";
											*/

											echo "<span class='evoau_cat_select_field {$field[1]}' data-enhance='false'>";
											foreach($terms as $term){
												echo "<span class='{$field[1]}_{$term->term_id}'><input type='checkbox' name='".$__field_id."[]' value='".$term->term_id."' ".( (count($slectedterms) && in_array($term->term_id, $slectedterms))? 'checked="checked"':null )." data-role='none'/> ".$term->name."</span>";
											}
											echo "</span>";

										echo "</p>";

										if(!empty($evoopt['evoau_add_cats']) && $evoopt['evoau_add_cats']=='yes')
											echo "<p class='label'><label>".eventon_get_custom_language($opt_2,'evoAUL_ocn','or create New (type other categories seperated by commas)',$lang)."</label></p><p><input class='fullwidth' type='text' name='".$__field_id."_new' data-role='none'/></p>";
										echo "</div>";
									}
								break;
								case 'image':
									// check if the user has permission to upload event images
									$imgUP = !empty($evoopt['evoau_allow_img_up']) && $evoopt['evoau_allow_img_up']=='yes' ? true: false;
									if(  !$imgUP && !current_user_can('upload_files')) break;

									// if image already exists
									if($_EDITFORM){
										$IMFSRC = false;
										$img_id =get_post_thumbnail_id($event_id);
										if($img_id!=''){
											$img_src = wp_get_attachment_image_src($img_id,'thumbnail');
											$IMFSRC = $img_src[0];
										}
									}
									echo "<div class='row'>
										<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p>";
									
									if($_EDITFORM && $IMFSRC){
										echo"<div class='evoau_img_preview'>
											<input class='evoau_img_input' type='hidden' name='evoau_event_image_id' value='{$img_id}'/>
											<img src='{$IMFSRC}'/><br/>
											<span class='evoau_event_image_remove'>".evo_lang('Remove Image',$lang,$opt_2)."</span>
											<input type='hidden' name='event_image_exists' value='yes'/>
										</div>";
									}
									echo "<div class='evoau_file_field' style='display:".($_EDITFORM && $IMFSRC?'none':'block')."'>
										<p>
											<span class='evoau_img_btn' >".eventon_get_custom_language($opt_2, 'evoAUL_img002', 'Select an Image', $lang)."</span>
											<input class='evoau_img_input' style='opacity:0' type='file' id='".$__field_id."' name='".$__field_id."' data-text='".eventon_get_custom_language($opt_2, 'evoAUL_img001', 'Image Chosen', $lang)."' data-role='none'/>";
											wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' );
										echo "</p></div>
									</div>";
								break;
								case 'uiselect':								
									// options
									$uis = array(
										'1'=>eventon_get_custom_language($opt_2, 'evoAUL_ux1', 'Slide Down EventCard', $lang),
										'2'=>eventon_get_custom_language($opt_2, 'evoAUL_ux2', 'External Link', $lang),
										'3'=>eventon_get_custom_language($opt_2, 'evoAUL_ux3', 'Lightbox Popup Window', $lang),
										'4'=> eventon_get_custom_language($opt_2, 'evoAUL_ux4a', 'Open as Single Event Page', $lang)
									);


									$ux_val = '';

									if($this->EVENT) 
										$ux_val = $this->EVENT->get_prop('_evcal_exlink_option');

									echo "<div class='row evoau_ui'>
											<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p><p class='dropdown_row'><select name='".$__field_id."'>";

											foreach($uis as $ui=>$uiv){
												$select = $ui == $ux_val ? 'selected="selected"':'';
												?><option type='checkbox' <?php echo $select; ?> value='<?php echo $ui;?>'> <?php echo $uiv;?></option>
												<?php 
											}
										echo "</select></p>
										<div class='evoau_exter' style='display:none'>
											<p class='label'><label for='evoau_ui'>".eventon_get_custom_language($opt_2, 'evoAUL_ux4', 'Type the External Url', $lang)."</label></p>
											<p class='input_field'><input name='evcal_exlink' class='fullwidth' type='text' data-role='none'/></p>
											<p class='checkbox_field'>
												<input name='_evcal_exlink_target' value='yes' type='checkbox' data-role='none'/> 
												<label>".eventon_get_custom_language($opt_2, 'evoAUL_lm1', 'Open in new window', $lang)."</label>
											</p>
										</div></div>";
								break;
								case 'learnmore':
									$default_val = str_replace("'", '"', $default_val);
									echo "<div class='row learnmove'>
										<p class='label'><label for='".$__field_id."'>".$__field_name.$__req."</label></p>
										<p class='input_field'><input type='text' class='fullwidth{$__req_}' name='".$__field_id."' ".$_placeholder." value='{$default_val}' data-role='none'/></p>
										<p class='checkbox_field'>
											<input type='checkbox' ".($default_val?'checked':'')." name='".$__field_id."_target' value='yes' data-role='none'/> 
											<label>".eventon_get_custom_language($opt_2, 'evoAUL_lm1', 'Open in new window', $lang)."</label></p>
									</div>";
								break;
								case 'locationselect':

									EVO()->cal->set_cur('evoau_1');
									$allow_add_new = EVO()->cal->check_yn('evoau_evoloc_new');
									$hide_list = EVO()->cal->check_yn('evoau_evoloc_list_hide');


									$locations = get_terms(array(
										'taxonomy'=>'event_location',
										'hide_empty'=>false,
										'orderby'=>'name'
									));
									$terms_exists =( ! empty( $locations ) && ! is_wp_error( $locations ) )? true:false;

									if(!$allow_add_new && !$terms_exists) break;

									if(!$allow_add_new && $hide_list) break;

									include_once 'form-type-location.php';

								break;
								case 'organizerselect':
									EVO()->cal->set_cur('evoau_1');
									$allow_add_new = EVO()->cal->check_yn('evoau_evoorg_new');
									$hide_list = EVO()->cal->check_yn('evoau_evoorg_list_hide');
									
									$organizers = get_terms('event_organizer' , array('hide_empty'=>false, 'orderby'=>'name'));
									$terms_exists = ( ! empty( $organizers ) && ! is_wp_error( $organizers ) )? true:false;

									// if no terms and can not add new
									if(!$terms_exists && !$allow_add_new) break;

									if(!$terms_exists && $hide_list) break;

									// if organizer tax saved before
								    	$organizer_terms = !empty($event_id)? wp_get_post_terms($event_id, 'event_organizer'):'';
								    	$termMeta = $evo_organizer_tax_id = '';
								    	if ( $organizer_terms && ! is_wp_error( $organizer_terms ) ){
											$evo_organizer_tax_id =  $organizer_terms[0]->term_id;
											
											$termMeta = evo_get_term_meta('event_organizer',$evo_organizer_tax_id, '', true);
										}

									echo "<div class='row organizerSelect'>
										<p class='label'><label for='".$__field_id."'>".$__field_name.$__req."</label></p>";
									
									echo '<p class="selection" data-role="none">';
									
									// edit form
									if($_EDITFORM && !empty($evo_organizer_tax_id) && $hide_list){
										echo "<span class='evoau_selected_val'>".$organizer_terms[0]->name."</span>";
									}

									// organizers list for selection
									if($terms_exists && !$hide_list):
										echo '<select class="evoau_organizer_select" name="evoau_organizer_select" data-role="none">';
										echo "<option value=''>".eventon_get_custom_language($opt_2, 'evoAUL_sso', 'Select Saved Organizers', $lang)."</option>";
								   
									   	// each organizer meta data
									    foreach ( $organizers as $org ) {
									    	//$taxmeta = get_option("taxonomy_".$org->term_id);
									    	$taxmeta = evo_get_term_meta('event_organizer',$org->term_id, '', true);

									    	$__selected = ($evo_organizer_tax_id== $org->term_id)? "selected='selected'":null;

									    	// select option attributes
									    	$data = array(
									    		'contact'=>(!empty($taxmeta['evcal_org_contact'])?$taxmeta['evcal_org_contact']:''),
									    		'img'=>(!empty($taxmeta['evo_org_img'])? $taxmeta['evo_org_img']:''),
									    		'exlink'=>(!empty($taxmeta['evcal_org_exlink'])?$taxmeta['evcal_org_exlink']:''),
									    		'address'=>(!empty($taxmeta['evcal_org_address'])?$taxmeta['evcal_org_address']:''),
									    	);
									    	$datastr = '';
									    	foreach($data as $f=>$v){
									    		$datastr.= ' data-'.$f.'="'.$v.'"';
									    	}

									       	echo "<option value='{$org->term_id}' {$datastr} {$__selected}>" . $org->name . '</option>';								        
									    }								    
									    echo "</select>";
								    endif;
										
									//echo "<input type='hidden' name='evoau_organizer_select' value='{$evo_organizer_tax_id}'/>
										//<input type='hidden' name='evo_org_img_id' value=''/>";

									// edit organizer button
										if($_EDITFORM && !empty($evo_organizer_tax_id) && EVO()->cal->check_yn('evoau_allow_edit_organizer')){
											echo "<span class='editMeta formBtnS'>". eventon_get_custom_language($opt_2,'evoAUL_edit','Edit', $lang)."</span>";
										}

									// Add new organizer BUTTONS
										if($allow_add_new){ 
											$_alt_txt = $hide_list? evo_lang('Hide Create New Form'): eventon_get_custom_language($opt_2, 'evoAUL_sfl', 'Select from List', $lang); 
											echo "<span class='enterNew formBtnS' data-txt='".$_alt_txt."' data-st='".($terms_exists?'ow':'nea')."'>". eventon_get_custom_language($opt_2,'evoAUL_cn','Create New', $lang)."</span>";
										}

								    echo "</p>";

								    // add new FORM
								    if($allow_add_new){ 
									    $data = array(
									    	'event_organizer',
									    	'event_org_contact',
									    	'event_org_address',
									    	'event_org_link',
									    );
									    echo "<div class='enterownrow' style='display:". ( $allow_add_new? 'none':'block'). "'>";

									    $fields = EVOAU()->frontend->au_form_fields();
									    foreach($data as $v){
									    	$dataField = $fields[$v];
									    	$savedValue = (!empty($termMeta) && !empty($termMeta[$dataField[1]]) )?$termMeta[$dataField[1]]: ''; 

									    	// Organizer name
									    	if($v == 'event_organizer' && !empty($organizer_terms)){
									    		$savedValue = $organizer_terms[0]->name;
									    	}

									    	echo "<p class='subrows {$v}'><label>".eventon_get_custom_language($opt_2, $dataField[4], $dataField[0], $lang)."</label><input class='fullwidth' type='text' name='{$dataField[1]}' value='{$savedValue}' data-role='none'/></p>";
									    }
									    echo "</div>";
									}
								    echo "</div>";

								break;
								case 'captcha':
									$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
									$rr = rand(0, 4);
									$calc = $cals[$rr];

									echo "<div class='row au_captcha'>
										<p><span style='margin-bottom:6px; margin-top:3px' class='verification'>{$calc} = ?</span>
										<input type='text' data-cal='{$rr}' class='fullwidth' id='".$__field_id."' name='".$__field_id."' data-role='none'/>
										</p>
										<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p>									
									</div>";
								break;

							// External file fields
								case 'virtual':
									include_once 'form-type-virtual.php';
								break;
								case 'health':
									include_once 'form-type-health.php';
								break;

								case 'timezone':
									include_once 'form-type-timezone.php';
								break;
								
							}

						}
					endforeach;
							
			// only edit form options
			if($_EDITFORM && is_array($SELECTED_FIELDS) && in_array('event_special_edit', $SELECTED_FIELDS)){
				echo apply_filters('evoau_edit_form_options_html', $this->get_edit_form_section($EPMV , $this->EVENT), $event_id);
			}

			// footer 
			$this->get_form_footer($atts, $_EDITFORM, $LIMITSUB);
			?>			
					
		<?php endif; // close if $_USER_LOGIN_REQ?>
		
		</form>
		
	</div>
	</div><!--.eventon_au_form_section-->
	<?php	
		return ob_get_clean();
	}

// Supportive function
	// date time picker html
		function _print_date_picker_text(){
			// get translated month and day name for date picker
			$opt_2 = $this->opt_2;
			$lang = $this->form_lang;
			$lang_options = isset($opt_2[$lang])? $opt_2[$lang]: array();

			$eventon_day_names = array(
				7=>'sunday', 
				1=>'monday',
				2=>'tuesday',
				3=>'wednesday',
				4=>'thursday',
				5=>'friday',
				6=>'saturday'
			);

			$daynames = $fulldayname = $month_names = array();
			foreach($eventon_day_names as $count=>$day){
				$daynames[] = ucfirst(substr( ((!empty($lang_options['evo_lang_3Ld_'.$count]))? 
					$lang_options['evo_lang_3Ld_'.$count]: $day), 0, 2));
			}
			foreach($eventon_day_names as $count=>$day){
				$fulldayname[] = ucfirst((!empty($lang_options['evcal_lang_day'.$count]))? 
					$lang_options['evcal_lang_day'.$count]: $day);
			}

			$data_month_names = evo_get_long_month_names($lang_options);
			//print_r($lang_options);
			
			foreach($data_month_names as $month){
				$month_names[] = ucfirst($month);
			}

			$other = array(
				'txtnext'=> evo_lang('Next'),
				'txtprev'=> evo_lang('Prev'),
			);

			$data_str = '';
			foreach(array(
				'mn'=>json_encode($month_names),
				'dn'=>json_encode($daynames),
				'fdn'=>json_encode($fulldayname),
				'ot'=>json_encode($other),										
			) as $k=>$v){
				$data_str .= 'data-'.$k."='".$v."'";
			}

			echo "<div class='evoau_dp_text' ". $data_str ."></div>";
		}
		function _print_date_time_picker($A){

			extract($A);

			$rand = rand(10000,99999);

			// required class
				$required_class_tag = '';
				if( !empty($required) && $required ) $required_class_tag = 'req';

			echo 
			"<span class='evo_date_time_select' data-sow='{$sow}'> 
				<span class='evo_date_select'>
					<input id='evoAU_end_date_{$rand}' class='". ($disable_date_editing?'':"datepicker{$type}date")." ". $required_class_tag." end evoau_dpicker ' readonly='true' type='text' data-role='none' name='event_{$type}_date' value='".$date_val."' data-assoc='{$assoc}'/>
					<input type='hidden' name='".($names? "event_{$type}_date_x":'')."' class='evoau_{$type}_alt_date alt_date' value='{$date_val_x}'/>
				</span>

				<span class='evoau_time_edit'>
					<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". $hour ."</span>";
					}else{													
						echo "<select class='evcal_date_select _{$type}_hour' name='".($names? "_{$type}_hour":'')."' data-role='none'>";

						for($x=1; $x<$time_hour_span;$x++){	
							$y = ($time_hour_span==25)? sprintf("%02d",($x-1)): $x;							
							echo "<option value='$y'".(($hour==$y)?'selected="selected"':'').">$y</option>";
						}
						echo "</select>";
					}
					echo "</span>";

					echo "<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". $minute ."</span>";
					}else{	
						echo "<select class='evcal_date_select _{$type}_minute' name='".($names? "_{$type}_minute":'')."' data-role='none'>";

						for($x=0; $x<$minIncre;$x++){
							$min = $minADJ * $x;
							$min = ($min<10)?('0'.$min):$min;
							echo "<option value='$min'".(($minute==$min)?'selected="selected"':'').">$min</option>";
						}
						echo "</select>";
					}
					echo "</span>";

					// AM PM
					if(!$hr24){
						echo "<span class='time_select'>";
						if($disable_date_editing){
							echo "<span>". $minute ."</span>";
						}else{	
							echo "<select name='".($names? "_{$type}_ampm":'')."' class='_{$type}_ampm ampm_sel'>";													
							foreach(array('am'=> evo_lang_get('evo_lang_am','AM'),'pm'=> evo_lang_get('evo_lang_pm','PM') ) as $f=>$sar){
								echo "<option value='".$f."' ".(($ampm==$f)?'selected="selected"':'').">".$sar."</option>";
							}							
							echo "</select>";
							echo "</span>";
						}
					}
					
				echo "</span>
			</span>";

		}

	// date time editing
		function _can_edit_date_time( ){

			if($this->form_type == 'new') return true;

			if(!$this->EVENT && $this->form_type =='edit') return false;

			EVO()->cal->set_cur('evoau_1');

			$date_time_editing = EVO()->cal->get_prop('evoau_dis_datetime_editing');

			// allow editing date time
			if(!$date_time_editing) return true;
			if($date_time_editing == 'def') return true;
			if($date_time_editing == 'all') return false; // disable date time editing for all events

			// if disable for past events and event is not past allow editing
			if($date_time_editing == 'past' && !$this->EVENT->is_past_event()) return true;

			return false;
		}

// function process form fields with permissions
	function process_form_fields_array($form_field_permissions, $FIELD_ORDER=''){

		// add default fields to field order
		if(!empty($FIELD_ORDER)){
			$EACH_FIELD = array_merge( EVOAU()->frontend->au_form_fields('defaults_ar') , $FIELD_ORDER);			
		}else{
			$FORM_FIELDS = EVOAU()->frontend->au_form_fields();	
			$EACH_FIELD = array_merge(EVOAU()->frontend->au_form_fields('default'), $FORM_FIELDS);
		}	
		
		$form_fields_array = array();

		if(empty($form_field_permissions) || sizeof($form_field_permissions)<1){
			$form_fields_array = $EACH_FIELD;
		}else{
			foreach($EACH_FIELD as $i=>$fieldvar){
				if(in_array($fieldvar, $form_field_permissions))
					$form_fields_array[] = $fieldvar;
			}			

			// include default fields that are required
			$form_fields_array = array_merge( EVOAU()->frontend->au_form_fields('defaults_ar') , $form_fields_array);
		} 
					
		return apply_filters('evoau_processed_form_fields', $form_fields_array);
	}

// edit form section
	function get_edit_form_section($EPMV, $EVENT){		
		ob_start();
		?><div class='edit_special'><?php
		
		foreach(apply_filters('evoau_editform_options_array',EVOAU()->frontend->au_form_fields('editonly')) 
			as $key=>$value
		){
			if(in_array($key, array('event_special_edit'))) continue;

			echo $this->get_form_html(
				$key,
				array(
					'type'=>$value[2],
					'yesno_args'=>array(
						'label'=>evo_lang($value[0]),
						'input'=>true,
						'id'=>$key,
						'default'=> (evo_check_yn($EPMV,$key)?'yes':'no')
					) 
				)
			);
		}

		do_action('evoau_editform_addeditfields', $this, $EVENT);
		?>
		</div>
		<?php
		return ob_get_clean();
	}

// Check submission restriction
	function _is_limit_form_submission_restriction(){
		$evoopt= EVOAU()->frontend->evoau_opt;

		$one_event_restriction = (!empty($evoopt['evoau_limit_submissions']) && $evoopt['evoau_limit_submissions']=='yes')? true: false;

		if(!$one_event_restriction) return false;

		// using user meta 
		if(is_user_logged_in()){
			$uid = get_current_user_id();
			$submitted = get_user_meta($uid, '_evoau_submissions');

			if(!$submitted) return false;
			if($submitted>0) return true;

		// using cookies
		}else{
			return (isset($_COOKIE['evoau_event_submited']) && $_COOKIE['evoau_event_submited']=='yes')? true:false;				
		}

	}

// Form Access restricted content
	function get_form_access_restricted($permission_type){

		if($permission_type == 'login'){
			$evoopt_1= $this->opt_1;
			$opt_2 = $this->opt_2;
			$lang = $this->form_lang;

			$__001 = eventon_get_custom_language($opt_2, 'evoAUL_ymlse', 'You must login to submit events.', $lang);
			$text_login = eventon_get_custom_language($opt_2, 'evoAUL_00l1', 'Login', $lang);
			$text_register = eventon_get_custom_language($opt_2, 'evoAUL_00l2', 'Register', $lang);

			// Login link
				$login_link = evo_login_url( get_permalink() );
			
			$log_msg = $__001. (sprintf(__(' <br/><a class="evcal_btn" title="%1$s" href="%2$s">%1$s</a>','eventon'), $text_login, $login_link ) );			

			// register new user
				if (get_option('users_can_register')){
					$log_msg.= (sprintf(__(' <a class="evcal_btn" title="%1$s" href="%2$s/wp-login.php?action=register">%1$s</a>','eventon'), $text_register, get_bloginfo('wpurl') ) );
				}
			echo "<p class='eventon_form_message'><span>".$log_msg."</span></p>";
		}

		if($permission_type=='permission'){
			$log_msg = evo_lang('You do not have permission to submit events!', $this->form_lang);
			echo "<p class='eventon_form_message'><span>".$log_msg."</span></p>";
		}
		if($permission_type=='permission_edit'){
			$log_msg = evo_lang('You do not have permission to submit events!', $this->form_lang);
			echo "<p class='eventon_form_message'><span>".$log_msg."</span></p>";
		}

	}

// form container
	public function get_form_container($atts){
		return "<div class='evoau_form_container waiting evo_ajax_load_events' data-d='". json_encode($atts) ."'>
				<span ></span>
			</div>";
	}

// form footer
	function get_form_footer($atts, $_EDITFORM, $LIMITSUB){
		
		
		$lang = (!empty($atts['lang'])? $atts['lang']:'L1');


		// form message
			echo "<p class='formeMSG' style='display:none'></p>";

		// Submit button
			$btn_text = ($_EDITFORM)? evo_lang('Update Event',$lang, $this->opt_2): eventon_get_custom_language($this->opt_2, 'evoAUL_se', 'Submit Event', $lang);
			echo "<div class='submit_row row'><p><a id='evoau_submit' class='evcal_btn evoau_event_submission_form_btn'>".$btn_text."</a></p></div>";

		?>
		</div><!-- .evoau_table-->
		</div><!-- inner -->
		<?php 

		$evoau_form_footer_json_data = json_encode(
			array(
				'nof0'=>((!empty(EVOAU()->frontend->evoau_opt['evoaun_msg_f']))?
								(EVOAU()->frontend->evoau_opt['evoaun_msg_f'])
								:eventon_get_custom_language($this->opt_2, 'evoAUL_nof1', 'Required Field(s) Missing', $lang) ),
				'nof1'=> eventon_get_custom_language($this->opt_2, 'evoAUL_nof1', 'Required Field(s) Missing', $lang),
				'nof2'=>eventon_get_custom_language($this->opt_2, 'evoAUL_nof2', 'Invalid validation code please try again', $lang),			
				
				'nof4'=>eventon_get_custom_language($this->opt_2, 'evoAUL_nof4', 'Could not create event post, try again later!', $lang),
				'nof5'=>eventon_get_custom_language($this->opt_2, 'evoAUL_nof5', 'Bad nonce form verification, try again!', $lang),
				'nof6'=>eventon_get_custom_language($this->opt_2, 'evoAUL_nof6', 'You can only submit one event!', $lang),
				'nof7'=>eventon_get_custom_language($this->opt_2, 'evoAUL_nof7', 'Image upload failed', $lang),
				
			)
		);

		?>
		<div class='evoau_form_messages'></div>
		<div class='evoau_json' style='display:none' data-j='<?php echo $evoau_form_footer_json_data;?>'></div>	

		
		<?php 
	}	

// form success HTML
	public function get_form_success_html($form_type, $atts){
		$lang = (!empty($atts['lang'])? $atts['lang']:'L1');


		$_allow_multiple_submissions = ($atts && !empty($atts['msub']) && $atts['msub']=='yes')? true:false;
		$_msub = apply_filters('evoau_form_footer_multiple_submissions_bool', $_allow_multiple_submissions , $this);
		
		ob_start();

		$message = false;

		if( $form_type == 'new') 
			$message = evo_lang_get( 'evoAUL_nof3', 'Thank you for submitting your event!', $lang, $this->opt_2);

		if( $form_type == 'edit') 
			$message = evo_lang('Thank you for updating your event!', $lang);

		if($message):
		?>
			<div class='evoau_success_msg' ><p><b></b><?php echo $message;?></p></div>
		<?php 
		endif;

		$LIMITSUB = $this->_is_limit_form_submission_restriction();

		// multiple submission button - if allowed
		if($_msub && $form_type == 'new' && !$LIMITSUB):
		?>
			<p class='msub_row' style='text-align:center'><a class='evoau_submit_another_trig evcal_btn'><?php   evo_lang_e('Submit another event',$lang, $this->opt_2);?></a></p>
		<?php endif;

		return ob_get_clean();
	}

// form HTML content
	function get_form_html($field, $data){
		global $eventon;
		if(empty($data['type'])) return false;


		ob_start();
		$helper = new evo_helper();

		$tooltip = $reqdep = '';
		if(!empty($data['tooltip'])){
			$tooltip = $helper->tooltips($data['tooltip']);
		}

		// required dependancy - the field that also need value for this field to be required
			if(!empty($data['req_dep'])){
				$reqdep = "data-reqd='".json_encode($data['req_dep']) ."'";
			}
			
		
		switch($data['type']){
			case 'hidden':
				echo "<input type='hidden' name='{$field}' value='{$data['value']}'/>";
			break;
			case 'text':
				echo "<div class='row {$field}'>
					<p class='label'>
						<label for='".$field."'>".$this->val_check($data,'name').$this->val_check($data,'required_html').$tooltip."</label> 
					</p>
					<p><input type='text' class='fullwidth ".$this->val_check($data,'name').$this->val_check($data,'required_class')."' name='".$field."' placeholder='".$this->val_check($data,'placeholder')."' value='".$this->val_check($data,'value')."' data-role='none' {$reqdep}/>";

				echo "</p>
				</div>";
			break;
			case 'yesno':
				echo "<div class='row {$field} row_yesno'>
				<p class='yesno_row' style='padding-top:8px;'>";
					echo $helper->html_yesnobtn($data['yesno_args']);					
				echo "</p>";
				echo "</div>";
			break;
			case 'html':
				echo "<div class='row'>";
				echo html_entity_decode($eventon->frontend->filter_evo_content($data['html']));
				echo "</div>";
			break;
			case 'status':
				if($this->EVENT):
					$_status = $this->EVENT->get_event_status();
					echo "<div class='row {$field} event_status'>";
						?>						
						<p class='es_values'>
							<label><?php echo evo_lang('Event Status');?></label>
							<span>							
							<input type="hidden" name="_status" value="<?php echo $_status;?>"/><?php
						 
							foreach( $this->EVENT->get_status_array('front') as $f=>$v){
								$sel = false;
								if($f == $_status) $sel = true;
								?>
								<span class='es_sin_val <?php echo $sel?'select':'';?>' value='<?php echo $f;?>'><?php echo $v;?></span>
								<?php
							}
							?>
							</span>
						</p>
						<div class='cancelled_extra' style="display:<?php echo $_status =='cancelled'? 'block':'none';?>">
							<p><label><?php _e('Reason for cancelling','eventon');?></label><textarea name='_cancel_reason'><?php echo $this->EVENT->get_prop('_cancel_reason');?></textarea>
						</div>
						<div class='movedonline_extra' style="display:<?php echo $_status =='movedonline'? 'block':'none';?>">
							<p><label><?php _e('More details for online event','eventon');?></label><textarea name='_movedonline_reason'><?php echo $this->EVENT->get_prop('_movedonline_reason');?></textarea>
						</div>
						<div class='postponed_extra' style="display:<?php echo $_status =='postponed'? 'block':'none';?>">
							<p><label><?php _e('More details about postpone','eventon');?></label><textarea name='_postponed_reason'><?php echo $this->EVENT->get_prop('_postponed_reason');?></textarea>
						</div>
						<div class='rescheduled_extra' style="display:<?php echo $_status =='rescheduled'? 'block':'none';?>">
							<p><label><?php _e('More details about reschedule','eventon');?></label><textarea name='_rescheduled_reason'><?php echo $this->EVENT->get_prop('_rescheduled_reason');?></textarea>
						</div>
					<?php
					echo "</div>";
				endif;

			break;
			case 'textarea':
				echo "<div class='row textarea {$field}'>
					<p class='label'><label for='".$field."'>".$this->val_check($data,'name')."</label></p>";

				// wysiwig editor
				if($this->val_check($data,'editor')== 'wysiwyg'){

					$editor_id = $field.'au';

					echo "<div id='evoau_form_wisywig' class='evoau_editor_wysiwig' data-textareaname='{$field}' data-editorid='{$editor_id}' >";

					echo "<textarea id='evoau_form_wisywig_content' class='evoau_event_details evoau_wyg' name='".$field."'>". (!empty($data['value'])? $data['value']:'') ."</textarea>";
					// WYSIWYG editor
					/*
					$editor_var_name = $field;
					$editor_args = array(
						'wpautop'=>true,
						'media_buttons'=>false,
						'textarea_name'=>$editor_var_name,
						'editor_class'=>'',
						'tinymce'=>true,
					);
					echo "<div id='{$editor_id}' class='{$field}' >".
						wp_editor(	$this->val_check($data,'value'), $editor_id, $editor_args);
						// /_WP_Editors::editor_js();
					echo "</div>";
					*/
					echo "</div>";
										
				}else{
					echo "<p><textarea id='".$field."' type='text' class='evoau_event_details fullwidth' name='".$field."' ".$this->val_check($data,'placeholder')." data-role='none' placeholder='".$this->val_check($data,'placeholder')."'>".$this->val_check($data,'value')."</textarea></p>";
				}

				echo "</div>";
			break;
			case 'minor_notice':
				echo "<p class='non_simple_wc_product_notice minor_notice'>". $this->val_check($data,'content')."</p>";
			break;

		}

		return ob_get_clean();
	}
	function val_check($array, $key){
		return !empty($array[$key])? $array[$key]:'';
	}
}