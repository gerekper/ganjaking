<?php
/**
 * Inquires Section for front-end
 * @version 2.2
 */
	
	$opt = EVOTX()->opt2;
?>
<div class='evotx_inquery'>
	
	<h3 class='evo_h3'><?php evo_lang_e('Submit a Ticket Inquiry Before Purchase');?></h3>
	
	<div class='evotxINQ_box' style=''>
		<form class='evotxINQ_form'>
			<?php 
			wp_nonce_field( 'evotx_inqure_form', 'evotx_inqure_nonce' );

			EVO()->elements->print_hidden_inputs(array(
				'event_id'=> $EVENT->event_id,
				'ri'=> $EVENT->ri,
			));
			

			foreach(EVOTX()->frontend->inqure_form_fields() as $key=>$val){
				if($val[0]=='textarea'):
			?>
				<p><label for="<?php echo $key;?>"><?php echo $val[1];?></label><textarea id='<?php echo $key;?>' class='evotxinq_field' name='<?php echo $key;?>' ></textarea></p>
			<?php else: ?>
				<p><label for="<?php echo $key;?>"><?php echo $val[1];?></label><input id='<?php echo $key;?>' class='evotxinq_field' name='<?php echo $key;?>' type="text" autocomplete='off'></p>
			<?php
				endif;
			}
		 
			// validation calculations
			$cals = array(	0=>'3+8', '5-2', '4+2', '6-3', '7+1'	);
			$rr = rand(0, 4);
			$calc = $cals[$rr];
			
			?>
			<p class='verifyhuman'>
				<label for='verifyhuman_input_<?php echo EVO()->calendar->ID;?>'><?php echo eventon_get_custom_language($opt, 'evoTX_inq_02a','Verify Your Inquiry');?>
				<span><b><?php echo $calc;?> = </b><input id='verifyhuman_input_<?php echo EVO()->calendar->ID;?>' class='captcha evotxinq_field' name='verify_code' type='text' data-cal='<?php echo $rr;?>' autocomplete="off"/></span>
			</p>
			
			<p class='submit_row'><a class="evcal_btn evotx_INQ_submit"><?php echo eventon_get_custom_language($opt, 'evoTX_inq_07','Submit');?></a>
			</p>

			<p class='message'></p>

		
			<div class='evotxINQ_msg' style='display:none'>
				<div class='evotxINQ_msg_in'>
					<em></em>
					<span><?php echo eventon_get_custom_language($opt, 'evoTX_inq_08','GOT IT! -- We will get back to you as soon as we can.');?></span>
				</div>
			</div>

		</form>

	</div>
</div>
