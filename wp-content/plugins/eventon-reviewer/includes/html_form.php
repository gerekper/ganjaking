<?php 
/**
 * HTML form for Reviewer
 * @version 0.2
 */
	
	global $eventon_re;
	$front = $eventon_re->frontend;
	$options = $front->opt;
	$options2 = $front->opt2;
	$args = $_POST;

	$eventname = !empty($_POST['eventname'])? $_POST['eventname']:'';
	$lang = !empty($_POST['lang'])? $_POST['lang']: 'L1';
	$front->lang = $lang;

	$active_fields = !empty($options['evore_fields'])? explode(',',$options['evore_fields']): false;


	// default values for name and email if prefill fields are set via options
	$field_val_name = $field_val_email = '';
	if( evo_settings_check_yn($options, 'evore_prefil') && is_user_logged_in()){
		$current_user = wp_get_current_user();

		$field_val_name = $current_user->user_firstname.' '.$current_user->user_lastname;
		$field_val_email = $current_user->user_email;
	}
?>
<div class='evore_form_section' data-eid="<?php echo !empty($args['eid'])? $args['eid']:'';?>" data-uid="<?php echo !empty($args['uid'])? $args['uid']:'';?>" data-ri="<?php echo !empty($args['ri'])? $args['ri']:'';?>" data-lang='<?php echo $lang;?>'>
	<div class='review_submission_form'>
		<h3 class="form_header"><?php echo $front->replace_en( $front->lang('evoREL_x8','Write a review for [event-name]'), $eventname);?></h3>
		<p class='star_rating'><?php echo $front->functions->get_star_rating_html(1);?><input class='input' type='hidden' name='rating' value='1'/></p>
		
		<?php 
		// Your name field
		if($active_fields && in_array('name', $active_fields)): ?>
			<p><label for=""><?php echo $front->lang( 'evoREL_x9','Your Name');?></label><input class='input' name='name' type="text" value='<?php echo $field_val_name;?>'></p>
		<?php endif;?>
	
		<?php // Your email address field ?>
		<p><label for=""><?php echo $front->lang( 'evoREL_x10','Your Email Address');?><?php echo (!empty($options['evore_email_req']) && $options['evore_email_req']=='yes')? ' *':'';?></label><input class='input inputemail <?php echo (!empty($options['evore_email_req']) && $options['evore_email_req']=='yes')? 'req':'';?>' name='email' type="text" value='<?php echo $field_val_email;?>'></p>
		
		<?php if($active_fields && in_array('review', $active_fields)): ?>
			<p><label for=""><?php echo $front->lang( 'evoREL_x11','Event Review Text');?><?php echo (!empty($options['evore_review_req']) && $options['evore_review_req']=='yes')? ' *':'';?></label><textarea class='input<?php echo (!empty($options['evore_review_req']) && $options['evore_review_req']=='yes')? ' req':'';?>' name="review" id="" cols="30" rows="10"></textarea></p>
		<?php endif;?>
		
		<?php 
		if($active_fields && in_array('validation', $active_fields)):
			// validation calculations
			$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
			$rr = rand(0, 4);
			$calc = $cals[$rr];
		?>
			<div class="form_row captcha">
				<p><?php echo $front->lang( 'evoREL_x12','Verify you are a human:');?> <?php echo $calc;?> = ?<input type="text" data-cal='<?php echo $rr;?>' class='regular_a captcha'/></p>
			</div>
		<?php endif;?>

		<p style='margin-top: 20px;'>
			<a id='submit_review_form' class='evcal_btn evore_submit'><?php echo $front->lang( 'evoREL_x13','Submit');?></a>
		</p>

		<?php if($active_fields && in_array('terms', $active_fields) && !empty($options['evore_termscond_text'])): ?>
			<p><a href='<?php echo $options['evore_termscond_text'];?>' target='_blank'><?php echo $front->lang( 'evoREL_x12a','Terms & Conditions');?></a></p>
		<?php endif;?>
	</div>

	<!-- Success review confirmation -->
	<div class='review_confirmation form_section' style="display:none" data-rsvpid=''>
		<b></b>
		<p><?php echo evo_lang( 'Thank you for submitting your review', $lang);?> <span class='name'></span></p>
	</div>
	<!-- form messages -->			
	<div class="form_row notification" style='display:none'><p></p></div>		
	<?php echo $front->get_form_msg($options2, $lang);?>
	
</div>