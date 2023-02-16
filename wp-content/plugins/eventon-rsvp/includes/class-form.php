<?php
/**
 * RSVP frontend form function
 * @version 2.6.8
 */
class evors_form{
	private $active_fields = false;

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
				$active_fields = $this->active_fields =(!empty($optRS['evors_ffields']))?$optRS['evors_ffields']:false;
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


// Incard form close button
	if($args['incard']=='yes')	echo "<a class='evors_incard_close'></a>";

?>
<div id='evorsvp_form' class='evors_forms form_<?php echo $args['formtype'];?>' data-rsvpid='<?php echo $args['rsvpid'];?>'>
	<form class='evors_submission_form <?php echo $rsvpChoice?'rsvp_'.$rsvpChoice:'';?>' method="POST" action="" enctype="multipart/form-data">

		<?php
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
			<div class="form_row name">
				<input class='name input req' name='first_name' type="text" placeholder='<?php echo $_field_fname;?>' title='<?php echo $_field_fname;?>' data-passed='' value='<?php echo $args['fname'];?>' <?php echo (!$prefill_edittable && !empty($args['fname']))? 'readonly="readonly"':'';?>/>
				<input class='name input req' name='last_name' type="text" placeholder='<?php echo $_field_lname;?>' title='<?php echo $_field_lname;?>' data-passed='' value='<?php echo $args['lname'];?>' <?php echo (!$prefill_edittable && !empty($args['lname']))? 'readonly="readonly"':'';?>/>
			</div>
		
		<?php
			// initial key fields
			foreach(array(
				'email'=>array('Email Address','evoRSL_009'),
				'phone'=>array('Phone Number','evoRSL_009a'),
				'count'=>array('How Many People in Your Party?','evoRSL_010'),
			) as $key=>$val){
				if(
					$key == 'email'||
					($key !='email' && $active_fields && in_array($key, $active_fields) )
				):
					$name = $evors->lang( $val[1], $val[0]);
					$value = ($RR && $RR->get_prop($key) && $key!= 'email')? $RR->get_prop($key): (!empty($args[$key])? $args[$key]:'');

					// Read only field
					$readonly = ($key=='email' && $args['formtype']=='update' && !empty($value) )? 
						'readonly="readonly"':'';
					
					// capacity limit
					if($key=='count'){
						$cap = 'na';
						
						if(!empty($args['cap']) || !empty($args['precap'])){
							$capacity = !empty($args['cap'])? $args['cap']:'';
							$precap = !empty($args['precap'])? $args['precap']:'';

							// get minimum value as capacity
							$cap = min( $capacity,$precap  );

							// if no capacity limit but per rsvp capacity set
							if( !empty($precap) && empty($capacity) )	$cap = $precap;

							// when value is not passed used capacity values
							if(!empty($value) && $value>0){
								$cap = max($value, $cap);
							}

						} 
						$value = empty($value)? 1:$value;
					}
				?>
					<div class="form_row <?php echo $key.' '.( in_array($key, array('count','phone'))?'show_yes':'');?>">
						<?php echo ( in_array($key, array('count')))? '<label>'.$name.'</label>':'';?>
						<input <?php echo $readonly;?> class='regular input req evors_rsvp_<?php echo $key;?>' name='<?php echo $key;?>' type="text" placeholder='<?php echo ($key!='count')?$name:'';?>' title='<?php echo $name;?>' data-passed='<?php echo $value;?>' value='<?php echo $value;?>' <?php echo ((!$prefill_edittable && !empty($value) && $key=='email')?'readonly="readonly"':'');?>/>
					</div>
				<?php
				endif;
			}
		
			// Additional Guest Names			
			if($active_fields && in_array('names', $active_fields)):
				$_field_names = $evors->lang('evoRSL_010b','List Full Name of Other Guests');
				$count = $RR && $RR->count()? $RR->count():1;
				$names = $RR && $RR->get_prop('names')? $RR->get_prop('names'):false;
				// /print_r($names);								
		?>
			<div class="form_row names form_guest_names show_yes" style='display:<?php echo ($count>1)?'':'none';?>'>
				<label><?php echo $_field_names;?></label>
				<div class='form_row_inner form_guest_names_list'>
					<?php for($x=0; $x< ($count-1); $x++):
						$name = ($names && isset($names[$x] ))? $names[$x]:'';
					?>
					<input class='regular input <?php echo $x;?>' name='names[]' type="text" value='<?php echo $name;?>'/>
					<?php endfor;?>
				</div>
			</div>
		<?php  endif;?>

			<?php
			// ADDITIONAL FIELDS
				for($x=1; $x <= $frontend->addFields; $x++){
					// if fields is activated and name of the field is not empty
					if(evo_settings_val('evors_addf'.$x, $optRS) && !empty($optRS['evors_addf'.$x.'_1'])){

						// if set to not show any additional fields for this event
						if($RSVP->_show_none_AF()) continue;

						// if set to show only certain additional fields for this event.
						if( !$RSVP->_can_show_AF('AF'.$x) ) continue;
						
						$required = evo_settings_check_yn($optRS , 'evors_addf'.$x.'_3')? 'req':null;
						
						$FIELDTYPE = (!empty($optRS['evors_addf'.$x.'_2']) || (!empty($optRS['evors_addf'.$x.'_2']) && $optRS['evors_addf'.$x.'_2']=='dropdown' && !empty($optRS['evors_addf'.$x.'_4'])) 
							)? 	$optRS['evors_addf'.$x.'_2']:'text';

						$value = $RR && $RR->get_prop('evors_addf'.$x.'_1')? $RR->get_prop('evors_addf'.$x.'_1'):'';
						
						$placeholder = !empty($optRS['evors_addf'.$x.'_ph'])? $optRS['evors_addf'.$x.'_ph']: '';
						
						$FIELDNAME = !empty($optRS['evors_addf'.$x.'_1'])? 
							html_entity_decode(stripslashes($optRS['evors_addf'.$x.'_1'])): 'field';
							$FIELDNAME = evo_lang($FIELDNAME);


						// Label
						$asterix = $required? '<abbr class="required" title="required">*</abbr>':'';
						$label_content = '<label for="'.'evors_addf'.$x.'_1'.'">'.$FIELDNAME . $asterix .'</label>';

						// when to hide the field
						$visibility_type = !empty($optRS['evors_addf'.$x.'_vis'])? $optRS['evors_addf'.$x.'_vis']: 'def';

					?>
						<div class="form_row additional_field show_<?php echo $visibility_type;?>">

					<?php
						switch($FIELDTYPE){
							case 'text':
								?><p class='inputfield'>
								<?php echo $label_content;?>
								<input title='<?php echo $FIELDNAME;?>' placeholder='<?php echo $placeholder;?>' class='regular input <?php echo $required;?>' name='<?php echo 'evors_addf'.$x.'_1';?>'type="text" value='<?php echo $value;?>'/><?php
							break;
							case 'html':
								?><p><?php echo $FIELDNAME;?></p><?php
							break;
							case 'textarea':
								?><p><?php echo $label_content;?>
								<textarea title='<?php echo $FIELDNAME;?>' placeholder='<?php echo $placeholder;?>' class='regular input <?php echo $required;?>' name='<?php echo 'evors_addf'.$x.'_1';?>'><?php echo $value;?></textarea></p><?php
							break;
							case 'checkbox':

								$_value = $value? $value: 'no';
								?><p class='field_checkbox'>
								<span><em class='evors_checkbox_field <?php echo $_value =='yes'? 'checked':'';?>'></em>
									<input name='<?php echo 'evors_addf'.$x.'_1';?>' class='<?php echo $required;?> checkbox input' type="hidden" value='<?php echo $_value;?>'> 
									<span class='evors_checkbox_name'><?php echo $FIELDNAME;?></span>
								</span>
								</p><?php
							break;
							case 'dropdown':
								?><p>
									<?php echo $label_content;?>
									<select name='<?php echo 'evors_addf'.$x.'_1';?>' class='input dropdown'>
									<?php
										global $eventon_rs;
										$OPTIONS = $frontend->get_additional_field_options($optRS['evors_addf'.$x.'_4']);
										foreach($OPTIONS as $slug=>$option){
											$selected = (!empty($value) && $value == $slug)? 'selected="selected"':'';
											echo "<option value='{$slug}' {$selected}>{$option}</option>";
										}
									?>
									</select>
								</p><?php
							break;

							case 'file':
								?>
								<p>
									<label><?php echo $FIELDNAME;?></label>
									<input name='rsvpfile_<?php echo $x;?>' type='file' value='<?php echo $value;?>'>
								</p>
								<?php
								
							break;

							case has_action("evors_additional_field_{$FIELDTYPE}"):		
								do_action("evors_additional_field_{$FIELDTYPE}", $value, $FIELDNAME, $required);
							break;
						}
					?>
						
						</div>
					<?php
					}
				}
			?>

		<?php
			// additional notes field for NO option
				$value = $RR && $RR->get_prop('additional_notes')? $RR->get_prop('additional_notes'):'';
				$this->_field_html('additional',$value);
		
			$this->_field_html('captcha');
			$this->_field_html('updates', ($RR && $RR->get_updates()? 'yes':'no') );
			$this->_field_html('submit_btn');
		?>
		<?php do_action('evors_after_form');?>			
		</div>
	<!-- submission_form-->
	</form>
	<?php $this->form_footer($evors->l );?>
</div>
<?php
		return ob_get_clean();
	}


// Form field content
	function _field_html($type, $value=''){
		if(!$this->active_fields) return false;
		if(!in_array($type, $this->active_fields)) return false;

		$op_rsvp = EVORS()->evors_opt;
		EVO()->cal->set_cur('evcal_rs');

		switch ($type){
			case 'additional':
				$label = EVORS()->lang('evoRSL_010a','Additional Notes');
				?>
				<div class="form_row additional_note show_yes" >
					<label><?php echo $label;?></label>
					<textarea class='input' name='additional_notes' type="text" placeholder='<?php echo $label;?>'><?php echo $value;?></textarea>
				</div>
				<?php
			break;
			case 'captcha':
				// validation calculations
				$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
				$rr = rand(0, 4);
				$calc = $cals[$rr];
				?>
				<div class="form_row captcha">
					<p><?php echo EVORS()->lang( 'evoRSL_011a','Verify you are a human');?></p>
					<p><?php echo $calc;?> = <input type="text" data-cal='<?php echo $rr;?>' class='regular_a captcha'/></p>
				</div>
				<?php
			break;
			case 'updates':
				$checked = ($value == 'yes')? 'checked="checked"':'';
				?>
				<div class="form_row updates">
					<input type="checkbox" name='updates' value='yes' <?php echo $checked;?>/> <label><?php echo EVORS()->lang( 'evoRSL_011','Receive updates about event');?></label>
				</div>
				<?php
			break;
			case 'submit_btn':
				?>
				<div class="form_row">
					<a id='submit_rsvp_form' class='evors_submit_rsvpform_btn evcal_btn evors_submit'><?php echo EVORS()->lang( 'evoRSL_012','Submit');?></a>
					<?php
						if( EVO()->cal->check_yn('evors_terms') && EVO()->cal->get_prop('evors_terms_link') ){
							echo "<p class='terms' style='padding-top:10px'><a href='". EVO()->cal->get_prop('evors_terms_link') ."' target='_blank'>". EVORS()->lang( 'evoRSL_tnc','Terms & Conditions')."</a></p>";
						}
					?>
				</div>
				<?php
			break;
		}
	}

// Find RSVP form
	function find_rsvp_form($args=''){
		global $eventon_rs;
		$front = $eventon_rs->frontend;

		// set Lang
			if(!empty($args['lang'])) $eventon_rs->l = $args['lang'];
		
		ob_start();

		if($args['incard']=='yes')		echo "<a class='evors_incard_close'></a>";

		?>
	<div id='evorsvp_form' class='evors_forms'>
	<div class='find_rsvp_to_change form_section'>
	<form class='evors_findrsvp_form' method="POST" action="" enctype="multipart/form-data">
		<?php 	wp_nonce_field( AJDE_EVCAL_BASENAME, 'evors_nonce' );	?>
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
		global $eventon_rs;

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
		<?php if($form_type=='submit'):?>
			<?php
				$_html_header = $front->replace_en( apply_filters('evors_form_success_msg_header', $eventon_rs->lang( 'evoRSL_x5','Successfully RSVP-ed for [event-name]'), $RSVP_cpt , $post) , $eventName );
			?>
			<h3 class="form_header submit"><?php echo $_html_header;?></h3>
		<?php else:?>
			<h3 class="form_header update"><?php echo $front->replace_en($eventon_rs->lang( 'evoRSL_x4','Successfully updated RSVP for [event-name]'), get_the_title( $RSVP_cpt->event_id() ) );?></h3>
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
					$eventon_rs->lang( 'evoRSL_x6','You have reserved [spaces] space(s) for [event-name]')
				);
				$_txt_reseverd = $front->replace_en($_txt_reseverd, $eventName);
				echo "<p class='coming'>{$_txt_reseverd}</p>";
			}

			// check whether confirmation emails are disabled
			if( !evo_settings_check_yn($optRS, 'evors_disable_emails')){
				$_txt_emails = str_replace('[email]', 
					"<span class='email'>".($RSVP_cpt->email()? $RSVP_cpt->email():'' )."</span>", 
					$eventon_rs->lang( 'evoRSL_x8','We have email-ed you a confirmation to [email]')
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
			<a id='call_change_rsvp_form' class='evcal_btn evors_submit'><?php echo $eventon_rs->lang('evoRSL_012x','Change my RSVP');?></a>
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
