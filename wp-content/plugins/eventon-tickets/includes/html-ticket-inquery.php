<?php
/**
 * Inquires Section for front-end
 */
	if(!empty($eventPMV['_allow_inquire']) && $eventPMV['_allow_inquire'][0]=='yes'):
?>
<div class='evotx_inquery'>
	<em></em>
	<a class='evcal_btn evotx_INQ_btn'><?php echo eventon_get_custom_language($opt, 'evoTX_inq_01','Inquire before buy');?></a>
	<div class='evotxINQ_box' style='display:none'>
		<div class='evotxINQ_form' data-event_id='<?php echo $object->event_id;?>' data-ri='<?php echo $object->repeat_interval; ?>' data-err='<?php echo eventon_get_custom_language($opt, 'evoTX_inq_06','Required fields missing, please try again!');?>'>

			<?php 

				foreach($this->inquire_fields() as $key=>$val){
					if($val[0]=='textarea'):
				?>
					<p><label for=""><?php echo $val[1];?></label><textarea class='evotxinq_field' name='<?php echo $key;?>' ></textarea></p>
				<?php else: ?>
					<p><label for=""><?php echo $val[1];?></label><input class='evotxinq_field' name='<?php echo $key;?>' type="text"></p>
				<?php
					endif;
				}
			?>			
			<?php 
				// validation calculations
				$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
				$rr = rand(0, 4);
				$calc = $cals[$rr];
			?>
			<p class='verifyhuman'><label for=''><?php echo eventon_get_custom_language($opt, 'evoTX_inq_02a','Verify Your Inquiry');?><span><b><?php echo $calc;?> = </b><input class='captcha evotxinq_field' type='text' data-cal='<?php echo $rr;?>'/></span></p>
			
			<p class='submit_row'><a class="evcal_btn evotx_INQ_submit"><?php echo eventon_get_custom_language($opt, 'evoTX_inq_07','Submit');?></a></p>
			<span class='notification'>
				<p class='notif' data-notif='<?php echo eventon_get_custom_language($opt, 'evoTX_inq_05','**All Fields are required.');?>'><?php echo eventon_get_custom_language($opt, 'evoTX_inq_05','**All Fields are required.');?></p>
			</span>
		</div>
		<div class='evotxINQ_msg' style='display:none'>
			<div class='evotxINQ_msg_in'>
				<em></em>
				<span><?php echo eventon_get_custom_language($opt, 'evoTX_inq_08','GOT IT! -- We will get back to you as soon as we can.');?></span>
			</div>
		</div>
	</div>
</div>
<?php
	endif;
?>