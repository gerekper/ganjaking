<?php
/**
 * RSVP frontend form function
 * @version 2.8.4
 */
class evors_form{
	private $active_fields = false;

	function __construct(){
		EVO()->cal->set_cur('evcal_rs');
		$this->options_rsvp = EVO()->cal->get_op('evcal_rs');
		$this->active_fields =EVO()->cal->get_prop('evors_ffields');
	}

	function get_form($args=''){
		global $eventon_rs;

		$args = !empty($args)?$args: array();
		$args = array_merge(array(
			'e_id'=>'',
			'repeat_interval'=>'0',
			'uid'=>get_current_user_id(),
			'rsvpid'=>'',
			'cap'=>'na',
			'precap'=>'na',
			'rsvp'=>'',
			'fname'=>'',
			'lname'=>'',
			'email'=>'',
			'formtype'=>'submit',
			'lang'=>'L1',
			'incard'=>'no',
			'send_confirmation_email'=>'no',
			'loginuser'=> true
		), $args);

		// form must have a event ID
		if(empty($args['e_id'])) return false;

		// Intial values
			$user_ID = $args['uid'];
			$event_id = $e_id = $args['e_id'];
			$rpmv = $rsvpid = $RR = false;	

			$frontend = EVORS()->frontend;	
			$optRS = $frontend->optRS;		
			$lang = EVORS()->l = $args['lang'];		
			evo_set_global_lang($lang);

			$RSVP = new EVORS_Event($event_id, $args['repeat_interval']);

			// if RSVP information is avialable					
				if(empty($args['rsvpid']) && !empty($args['uid'])){
					$args['rsvpid'] = $RSVP->get_rsvp_id_by_author( $args['uid'] );					
				}

			// if rsvp id exists > initiate RSVP object
				if(!empty($args['rsvpid'])){
					$RR = new EVO_RSVP_CPT( $args['rsvpid'] );
					$args['formtype'] = 'update';					
				}


			// RSVP TYPE
				$rsvp_type = apply_filters('evors_form_rsvp_type', 'normal', $args, $RSVP, $RR);


			$evors = $eventon_rs;

			// Disable pre-filled field editing
				$prefill_edittable = (!empty($optRS['evors_prefil_block']) && $optRS['evors_prefil_block']=='yes')? false: true;
				$prefill = (!empty($optRS['evors_prefil']) && $optRS['evors_prefil']=='yes')? true: false;
				if(!$prefill) $prefill_edittable = false;
			
			// form fields
				$active_fields = $this->active_fields;
				$this->active_fields[] = 'submit_btn';

			// if form type is update but can not find RSVP id
				if(empty($args['rsvpid']) && $args['formtype']=='update'){
					return $this->find_rsvp_form($args);
				}

			// if user loggedin prefil user date
				if(!empty($user_ID)){
					$user_info = get_userdata($user_ID);
					$args['fname'] = $user_info->first_name;
					$args['lname'] = $user_info->last_name;
					$args['email'] = $user_info->user_email;
				}

				// should form be prefilled or not
					if( !$prefill && $args['formtype'] !='update'  ){
						$args['fname'] = $args['lname'] = $args['email'] = '';
					}
					
					if($args['formtype']=='update'){
						$args['fname'] = ($RR && $RR->first_name())? $RR->first_name():'';
						$args['lname'] = ($RR && $RR->last_name())? $RR->last_name():'';
						$args['email'] = ($RR && $RR->email())? $RR->email():'';
					}
				
		// RSVP status
			$rsvpChoice = ( $RR && $RR->get_rsvp_status() )? $RR->get_rsvp_status(): 	
				(!empty($args['rsvp'])? $args['rsvp']:'y');


		// pluggable
			$args = apply_filters('evors_rsvp_form_args', $args, $RR);
		
		ob_start();




?>
<div id='evorsvp_form' class='evors_forms form_<?php echo $args['formtype'];?>' data-rsvpid='<?php echo $args['rsvpid'];?>'>
	<form class='evors_gen_form evors_submission_form <?php echo $rsvpChoice?'rsvp_'.$rsvpChoice:'';?>' method="POST" action="" enctype="multipart/form-data">

		<?php

		// Incard form close button
			if($args['incard']=='yes')	echo "<a class='evors_incard_close'></a>";

		// hidden input fields
			$arr = apply_filters('evors_form_hidden_values',array(
				'rsvpid'=>	$args['rsvpid'],
				'e_id'=>	$event_id,
				'repeat_interval'=>	$RSVP->event->ri,
				'uid'=>$user_ID,
				'formtype'=> $args['formtype'],
				'lang'=>$lang,
				'rsvp_type' => $rsvp_type,
				'loginuser'=> $args['loginuser']
			), $args, $RSVP, $RR);

			// if count is not visible field add default count
			if( !in_array('count', $this->active_fields)){
				$arr['count'] = '1';
			}

			// pass original status to help for notes
			if($args['formtype'] == 'update') $arr['original_status'] = $rsvpChoice;

			// print HIDDEN fields
			foreach($arr as $key=>$val){
				echo "<input type='hidden' name='{$key}' value='{$val}'/>";
			}
		
			wp_nonce_field( AJDE_EVCAL_BASENAME, 'evors_nonce' );
			
			do_action('evors_before_form');

		?>
		
		<div class='submission_form form_section'>
			<h3 class="form_header"><?php 
				$_title_txt = ($args['formtype']=='submit')? 
					EVORS()->lang('evoRSL_x2','RSVP to [event-name]'): 
					EVORS()->lang('evoRSL_x2a','Change RSVP to [event-name]');
			
				echo $frontend->replace_en( apply_filters('evors_form_event_title', $_title_txt , $args, $RR) , get_the_title($args['e_id'] ) );
			?></h3>
			<?php // subtitle
				$subtitle_text = '';

				$subtitle_text = ($args['formtype']=='submit')?
					evo_lang('Fill in the form below to RSVP!'):
					($prefill ? evo_lang('You have already RSVPed for this event!'):'');

				$subtitle_text = apply_filters('evors_form_event_subtitle', $subtitle_text, $RR, $args);


			?>
			<p class='evors_subtitle'><?php echo $subtitle_text; ?></p>

			<?php do_action('evors_form_under_subtitle', $args, $RSVP, $RR);?>

			<div class="form_row rsvp_status">	
				<?php if($args['formtype']=='update'):?>
					<p class='evors_rsvpid_tag'><?php echo EVORS()->lang('evoRSL_007a','RSVP ID #');?>: <?php echo $args['rsvpid'];?></p>
				<?php endif;?>	
				
				<?php 
				// RSVP choices					
					$choices_content = $frontend->get_rsvp_choices($frontend->opt2, $optRS, array(), $rsvpChoice ,$args['formtype']);
				?>
				<p class='<?php echo ($frontend->rsvp_option_count==1)?'sin':'';?>'>
					<?php echo $choices_content;?>
					<input type="hidden" name='rsvp' value='<?php echo $rsvpChoice;?>'/>
				</p>
			</div>			
			<?php
				$_field_fname = $evors->lang( 'evoRSL_007','First Name');
				$_field_lname = $evors->lang( 'evoRSL_008','Last Name');				
			?>
			<div class="form_row name req">
				<label><?php echo evo_lang('Your Name');?></label>
				<input class='name input' name='first_name' type="text" placeholder='<?php echo $_field_fname;?>' title='<?php echo $_field_fname;?>' data-passed='' value='<?php echo $args['fname'];?>' <?php echo (!$prefill_edittable && !empty($args['fname']))? 'readonly="readonly"':'';?>/>
				<input class='name input' name='last_name' type="text" placeholder='<?php echo $_field_lname;?>' title='<?php echo $_field_lname;?>' data-passed='' value='<?php echo $args['lname'];?>' <?php echo (!$prefill_edittable && !empty($args['lname']))? 'readonly="readonly"':'';?>/>
			</div>
		
		<?php

		// EMAIL field
			$name = evo_lang_get('evoRSL_009','Email Address');
			$value = (!empty($args['email'])? $args['email']:'');
			$readonly = ($args['formtype']=='update' && !empty($value) )? 
						'readonly="readonly"':'';

			?>
				<div class="form_row email req">
					<label><?php echo evo_lang('Your Email Address');?></label>
					<input <?php echo $readonly;?> class='regular input evors_rsvp_email' name='email' type="text" placeholder='<?php echo $name;?>' title='<?php echo $name;?>' data-passed='<?php echo $value;?>' value='<?php echo $value;?>' <?php echo ((!$prefill_edittable && !empty($value))?'readonly="readonly"':'');?>/>
				</div>
			<?php


		// each for field
			$form_fields = $this->get_form_fields($RSVP, $RR);

			//print_r($form_fields);
			//print_r($active_fields);

			foreach( $form_fields  as $key=> $fdata){

				// skip auto generated names field
				if( $key == 'names' ) continue;

				extract($fdata);

				//$check_fields = true;
				//if( !empty($skip_active_check) && $skip_active_check) $check_fields = false;
				//if( $check_fields && $active_fields && !in_array($key, $active_fields)  ) continue;
				
				$value = ($RR && $RR->get_prop($key) )? $RR->get_prop($key): (!empty($args[$key])? $args[$key]:'');
				$placeholder = $name;

				$name .= $required? ' <abbr class="required" title="required">*</abbr>':'';

				if( $type == 'checkbox') $type = 'yesno';
				if( $type == 'html'){ 
					$type = 'code';
					$content = $name;
				}

				if( $key == 'count'){ 
					$placeholder = ''; 
					if( empty($value)) $value = 1;
				}
				if( $type == 'file'){
					?>
					<p class='form_row <?php echo !empty($visibility_type)? 'show_'.$visibility_type:'';?>'>
						<label><?php echo $name;?></label>
						<input name='rsvpfile_<?php echo $x;?>' type='file' value='<?php echo $value;?>'>
					</p>
					<?php
					continue;
				}
				

				// additional notes field
					if( $key == 'additional'):
						?>
						<div class="form_row additional_note show_yes" >
							<label><?php echo $name;?></label>
							<textarea class='input' name='additional_notes' type="text" placeholder='<?php echo $name;?>'><?php echo $value;?></textarea>
						</div>
						<?php
						continue;
					endif;

				// captcha field - auto required
					if( $key == 'captcha'):
						// validation calculations
						$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
						$rr = rand(0, 4);
						$calc = $cals[$rr];
						?>
						<div class="form_row captcha req">
							<p><?php echo $name;?></p>
							<p><?php echo $calc;?> = <input type="text" data-cal='<?php echo $rr;?>' class='regular_a captcha'/></p>
						</div>
						<?php
						continue;
					endif;

				// all other fields
					if( !empty($type)):
						echo "<div class='form_row {$key} ". (!empty($visibility_type)? 'show_'.$visibility_type:'') . ( $required ? ' req':'') . "'>";
						echo EVO()->elements->get_element(array(
							'type'=>$type,
							'id'=> $key,
							'name'=> $name, 'default'=> $placeholder,
							'value' => $value,
							'options'=> !empty($options) ? $options : '',
							'content'=> !empty($content) ? $content : '',
						));	
						echo "</div>";
					endif;
				

				// after count show additional guests
					if( $key == 'count'):

						if($active_fields && in_array('names', $active_fields)):
							$_field_names = $evors->lang('evoRSL_010b','List Full Name of Other Guests');
							$count = $RR && $RR->count()? $RR->count():1;
							$names = $RR && $RR->get_prop('names')? $RR->get_prop('names'):false;
							// /print_r($names);								
						?>
						<div class="form_row names form_guest_names show_yes" style='display:<?php echo ($count>1)?'':'none';?>'>
							<p class='evo_field_label'><?php echo $_field_names;?></p>
							<div class='form_row_inner form_guest_names_list'>
								<?php for($x=0; $x< ($count-1); $x++):
									$name = ($names && isset($names[$x] ))? $names[$x]:'';
								?>
								<input class='regular input <?php echo $x;?>' name='names[]' type="text" value='<?php echo $name;?>'/>
								<?php endfor;?>
							</div>
						</div>
						<?php 
						endif;
					endif;

				// pluggable field
					if( has_action("evors_additional_field_{$type}") ):
						do_action("evors_additional_field_{$type}", $value, $name, $required);
					endif;
			}
		

		
		// SUBMIT BUTTON
		?>
			<div class="form_row">
				<a id='submit_rsvp_form' class='evors_submit_rsvpform_btn evcal_btn evors_submit'><?php echo EVORS()->lang( 'evoRSL_012','Submit');?></a>
				<?php
				// terms and conditions field
					if( EVO()->cal->check_yn('evors_terms','evcal_rs') && EVO()->cal->get_prop('evors_terms_link','evcal_rs') ){
						echo "<p class='terms' style='padding-top:10px'><a href='". EVO()->cal->get_prop('evors_terms_link') ."' target='_blank'>". EVORS()->lang( 'evoRSL_tnc','Terms & Conditions')."</a></p>";
					}
				?>
			</div>
		<?php	 do_action('evors_after_form');?>			
		</div>
	<!-- submission_form-->
	</form>
	<?php $this->form_footer($evors->l );?>
</div>
<?php
		return ob_get_clean();
	}


// form fields array
	function get_form_fields($RSVP, $RR = ''){
		$optRS = EVORS()->frontend->optRS;

		$return = array(
			'phone'=>array( 'type'=>'text','name'=> evo_lang_get('evoRSL_009a','Phone Number'), 'visibility_type'=>'yes' ),
			'count'=>array( 'type'=>'text','name'=> evo_lang_get('evoRSL_010','How Many People in Your Party?') ),
			'names'=>array( 'type'=>'text','name'=> evo_lang_get('evoRSL_010b','List Full Name of Other Guests') ),
			'twitter'=> array( 'type'=>'text','name'=> 'Twitter User Handle' ),
			'instagram'=> array( 'type'=>'text','name'=> 'Instagram Handle' ),
			'youtube'=> array( 'type'=>'text','name'=> 'Youtube Handle' ),
			'tiktok'=> array( 'type'=>'text','name'=> 'TikTok Handle' ),
		);

		// additional fields
		for($x=1; $x <= EVORS()->frontend->addFields; $x++){
			// if fields is activated and name of the field is not empty
			if(evo_settings_val('evors_addf'.$x, $optRS) && !empty($optRS['evors_addf'.$x.'_1'])){
				if($RSVP->_show_none_AF()) continue;
				if( !$RSVP->_can_show_AF('AF'.$x) ) continue;

				$FIELDTYPE = (!empty($optRS['evors_addf'.$x.'_2']) || (!empty($optRS['evors_addf'.$x.'_2']) && $optRS['evors_addf'.$x.'_2']=='dropdown' && !empty($optRS['evors_addf'.$x.'_4'])) 
						)? 	$optRS['evors_addf'.$x.'_2']:'text';

				$value = $RR && $RR->get_prop('evors_addf'.$x.'_1')? $RR->get_prop('evors_addf'.$x.'_1'):'';
				
				$placeholder = !empty($optRS['evors_addf'.$x.'_ph'])? $optRS['evors_addf'.$x.'_ph']: '';

				$FIELDNAME = !empty($optRS['evors_addf'.$x.'_1'])? 
						html_entity_decode(stripslashes($optRS['evors_addf'.$x.'_1'])): 'field';
						$FIELDNAME = evo_lang($FIELDNAME);
				$visibility_type = !empty($optRS['evors_addf'.$x.'_vis'])? $optRS['evors_addf'.$x.'_vis']: 'def';
				$required = evo_settings_check_yn($optRS , 'evors_addf'.$x.'_3')? 'req':null;
				
				$return[ 'evors_addf'.$x] = array(
					'type'=> $FIELDTYPE,
					'name'=> $FIELDNAME, 
					'x'=> $x,					
					'value'=>$value,
					'placeholder'=> $placeholder,
					'visibility_type' => $visibility_type,
					'skip_active_check'=> true,
					'options'=> EVORS()->frontend->get_additional_field_options($optRS['evors_addf'.$x.'_4']),
					'required'=> $required
				);
			}
		}

		// additional notes field for NO option
			$value = $RR && $RR->get_prop('additional_notes')? $RR->get_prop('additional_notes'):'';		

		$return[ 'additional' ] = array('value'=> $value, 'name'=> evo_lang_get('evoRSL_010a','Additional Notes'));
		$return[ 'captcha' ] = array('type'=>'captcha', 'name'=> evo_lang_get('evoRSL_011a','Verify you are a human'));
		$return[ 'updates' ] = array('type'=>'yesno', 'name'=> evo_lang_get('evoRSL_011','Receive updates about event'), 'value'=> ($RR && $RR->get_updates()? 'yes':'no'));

		// process fields -> add empty fields, remove not active fields
		foreach($return as $key=> $values){

			$return[ $key ] = array_merge(array(
				'type'=>'','value'=>'','name'=>'', 'skip_active_check'=> false, 'required'=> false,
				'visibility_type'=>''
			), $values);

			if( $return[$key]['skip_active_check']) continue;

			if( $this->active_fields && !in_array($key, $this->active_fields) ){
				unset(  $return[$key] );
			}
			
		}

		return apply_filters('evors_form_fields_array', $return, $RSVP, $RR);
	}

	function get_form_field_keys( $RSVP, $RR=''){
		$return = array();
		foreach( $this->get_form_fields( $RSVP, $RR) as $key=>$v){
			$return[] = $key;		
		}
		return $return;
	}

// Find RSVP form
	function find_rsvp_form($args=''){
		global $eventon_rs;
		$front = $eventon_rs->frontend;

		// set Lang
			if(!empty($args['lang'])) $eventon_rs->l = $args['lang'];
		
		ob_start();


		?>
	<div id='evorsvp_form' class='evors_forms'>
	<div class='find_rsvp_to_change form_section'>
	<form class='evors_gen_form evors_findrsvp_form' method="POST" action="" enctype="multipart/form-data">
		<?php 

		if($args['incard']=='yes')		echo "<a class='evors_incard_close'></a>";	

		wp_nonce_field( AJDE_EVCAL_BASENAME, 'evors_nonce' );	?>
		<?php
			if(!empty($args) && is_array($args)){
				foreach($args as $key=>$val){
					if(empty($val)) continue;
					echo "<input type='hidden' name='{$key}' value='{$val}'/>";
				}
			}
		?>

		<h3 class="form_header"><?php echo $front->replace_en( $eventon_rs->lang('evoRSL_x3','Find my RSVP for [event-name]'), get_the_title($args['e_id']));?></h3>
		<div class="form_row">
			<?php /*<input class='name input req' name='first_name' type="text" placeholder=' <?php echo $_field_fname;?>'/>
			<input class='name input req' name='last_name' type="text" placeholder=' <?php echo $_field_lname;?>'/>*/?>
			<input class='regular input req' name='email' type="text" placeholder='<?php echo $eventon_rs->lang( 'evoRSL_009','Email Address');?>' value=''/>
		</div>
		<?php 
		/*
		<div class="form_row">
			<input class='regular input req' name='rsvpid' type="text" placeholder='<?php echo $front->lang( 'evoRSL_007a','RSVP ID');?>' value=''/>
		</div>
		*/?>
		<div class="form_row evors_find_action">
			<p><i><?php echo $eventon_rs->lang( 'evoRSL_x1','We have to look up your RSVP in order to change it!');?></i></p>
			<a id='change_rsvp_form' class='evors_findrsvp_form_btn evcal_btn evors_submit'><?php echo $eventon_rs->lang( 'evoRSL_012y','Find my RSVP');?></a>
		</div>
		<?php $this->form_footer($eventon_rs->l);?>
	</form>
	</div>
	</div>
		<?php
		return ob_get_clean();
	}

// Success message content
	function form_message($RSVP, $rsvpid, $form_type, $post){
		
		$form_type = empty($form_type)? 'submit': $form_type;

		$front = EVORS()->frontend;
		$RSVP_cpt = $RR = new EVO_RSVP_CPT($rsvpid);
		
		$optRS = $front->optRS;
		$active_fields =(!empty($optRS['evors_ffields'])) ? $optRS['evors_ffields']:false;
		$eventName = get_the_title($RSVP_cpt->event_id());

		ob_start();

		// pluggable proceed check
			$proceed = apply_filters('evors_rsvp_form_message',true, $form_type, $RSVP, $RSVP_cpt, $post);
			if($proceed !== true) return $proceed;

		?>
	<div id='evorsvp_form' class='evors_forms'>
	<div class='rsvp_confirmation form_section' data-rsvpid='<?php echo $rsvpid;?>'>
		<b></b>
		<p><?php evo_lang_e('RSVP ID');?> #<?php echo $rsvpid;?></p>
		<?php 
		if($form_type=='submit'):?>
			<?php
				$_html_header = $front->replace_en( apply_filters('evors_form_success_msg_header', EVORS()->lang( 'evoRSL_x5','Successfully RSVP-ed for [event-name]'), $RSVP_cpt , $post) , $eventName );
			?>
			<h3 class="form_header submit"><?php echo $_html_header;?></h3>
		<?php 
		// updating
		else:?>
			
			<h3 class="form_header update"><?php echo $front->replace_en(EVORS()->lang( 'evoRSL_x4','Successfully updated RSVP for [event-name]'), get_the_title( $RSVP_cpt->event_id() ) );?></h3>
			
			<?php 
			// @since 2.8.4
			do_action('evors_form_success_msg_updated_rsvp', $RSVP_cpt, $RSVP);?>
		
		<?php endif;?>
		
		<p><?php echo EVORS()->lang( 'evoRSL_x7','Thank You');?> 
			<span class='name'><?php echo $RSVP_cpt->full_name();?></span>
		</p>
		
		<?php 

		// Sucess message body content based on RSVP status
		// YES
		if($RSVP_cpt->status()=='y'){
			if($active_fields && in_array('count', $active_fields) && $RSVP_cpt->count() ){
			
				$_txt_reseverd = str_replace('[spaces]', 
					"<span class='spots'>".( $RSVP_cpt->count() )."</span>", 
					EVORS()->lang( 'evoRSL_x6','You have reserved [spaces] space(s) for [event-name]')
				);
				$_txt_reseverd = $front->replace_en($_txt_reseverd, $eventName);
				echo "<p class='coming'>{$_txt_reseverd}</p>";
			}

			// check whether confirmation emails are disabled
			if( !evo_settings_check_yn($optRS, 'evors_disable_emails')){
				$_txt_emails = str_replace('[email]', 
					"<span class='email'>".($RSVP_cpt->email()? $RSVP_cpt->email():'' )."</span>", 
					EVORS()->lang( 'evoRSL_x8','We have email-ed you a confirmation to [email]')
				);
				echo "<p class='coming'>{$_txt_emails}</p>";
			}

		}elseif($RSVP_cpt->status()=='n'){		
			echo "<p class='notcoming'>".evo_lang('Sorry to hear you are not coming', EVORS()->l)."</p>";
		}else{}
		

		// get data string
		$datastring = $front->event_rsvp_data(
			$RSVP, 	true
		);

		?>		
		<div class="form_row" style='padding-top:10px' data-rsvpid='<?php echo $rsvpid;?>' 
		<?php echo $datastring;?>>
			<a id='call_change_rsvp_form' class='evcal_btn evors_submit'><?php echo EVORS()->lang('evoRSL_012x','Change my RSVP');?></a>
		</div>

		<?php do_action('evors_form_success_msg_end',$RSVP_cpt);?>
	</div>
	</div>
		<?php
		return ob_get_clean();
	}

// Return the guest list after a user has rsvped so the list on event card can be updated with new information
	function get_form_guestlist($RSVP){
		if(!$RSVP->show_whoscoming()) return false;

		$repeat_interval = !empty($post['repeat_interval']) ? $post['repeat_interval']:0;
		$attendee_icons = EVORS()->frontend->GET_attendees_icons($RSVP, $RSVP->ri);
		if(!$attendee_icons) return false;

		$newCount = $RSVP->get_rsvp_count('y');
		
		return array(
			'guestlist'=>"<em class='tooltip'></em>".$attendee_icons,
			'newcount'=>$newCount
		);
	}

	function form_footer($lang){
		echo '<div class="form_row notification" style="display:none"><p></p></div>';
		echo EVORS()->frontend->get_form_msg($lang);
	}
}
