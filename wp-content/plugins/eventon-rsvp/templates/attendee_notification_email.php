<?php
/**
 * Atendee Notification Email - sent to customer/attendee
 *
 * @version 2.6.3
 * @customize copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/rsvp/ folder and edit the copied file customzie this email template.
 */

	
	echo EVO()->get_email_part('header');

	$RR = new EVO_RSVP_CPT($args['rsvp_id']);	
	$RSVP = new EVORS_Event($RR->event_id(), $RR->repeat_interval());	

	// set global language 
		$lang = (!empty($args['lang']))? $args['lang']: 'L1';	 // language version
		EVO()->lang = $args['lang'] = $lang;

	
	//styles
		$__styles_01 = "font-size:30px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; font-style:italic; margin: 4px 0; line-height:110%;";
		$__sty_lh = "line-height:110%;";
		$__styles_02a = "color:#afafaf; text-transform:none";
		$__sty_td ="padding:0px;border:none";
		$__sty_m0 ="margin:0px;";
		$__sty_button ="display: inline-block;padding: 5px 10px;border: 1px solid #B7B7B7; text-decoration:none; font-style:normal; border-radius:5px;";
	
	// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";

	// Types of Notice
		$notice_type = (isset($args['notice_type']) && !empty($args['notice_type'])) ? $args['notice_type']: 'update_rsvp';
		$notice_title = (isset($args['notice_title']) && !empty($args['notice_title'])) ? $args['notice_title']: evo_lang('RSVP Update Notice');
		$notice_data = (isset($args['notice_data']) && $args['notice_data']=='no') ? false:true;
?>
<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='<?php echo $__sty_td;?>'>			
			<div style="padding:20px; font-family:'open sans'">
				
				<?php
				// for various attendee notices
				switch($notice_type){
					case has_action("evors_attendee_notification_{$notice_type}"):
						do_action("evors_attendee_notification_{$notice_type}", $RR, $RSVP, $args);
					break;

					// UPDATE RSVP
					case 'update_rsvp':?>

						<p style='<?php echo $__styles_01.$__sty_lh;?>'><?php echO $notice_title ?></p>

						<?php 
						// Attendee notification email message
						if(isset($args['notice_message'])):?>
							<p style='font-size:18px; color:#303030; font-weight:normal;display:block; font-style:italic; margin: 4px 0; line-height:110%;'><?php echo $args['notice_message'];?></p>
						<?php endif;?>
						
						<p style='padding-top:20px;margin:0'></p>

						<?php

						if($notice_data):
							// Email Data Section
							$data = apply_filters('evors_attendee_nofitication_email_data', array(
								array( EVORS()->lang('evoRSL_007a', 'RSVP ID', $lang), $RR->rsvp_id),
								array( EVORS()->lang('evoRSLX_001', 'RSVP Status', $lang), $RR->trans_rsvp_status($lang) ),
								array( EVORS()->lang('evoRSLX_008a', 'Event Name', $lang), $RSVP->event->get_title() ),
								array( EVORS()->lang('evoRSL_009', 'Email Address', $lang), $RR->email() ),
							), $RR, $RSVP, $args);

							// for each data field
							foreach($data as $vv){
								if(empty($vv[1])) continue;
								echo $__item_p_beg . $vv[0] .':</span> '. $vv[1] .'</p>';
							}
						endif;

					// close switch case				
					break;
				}?>
			</div>
		</td>
	</tr>
	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>				<?php 
				$contactLink = (!empty(EVORS()->evors_opt['evors_contact_link']))? EVORS()->evors_opt['evors_contact_link']:site_url();
			?>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>padding-bottom:10px'><a style='' href='<?php echo $contactLink;?>'><?php echo EVORS()->lang('evoRSLX_006', 'Contact Us for questions and concerns', $lang)?></a></p>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='<?php echo $__sty_button;?>' target='_blank' href='<?php echo $RSVP->event->get_permalink(); ?>'><?php echo evo_lang( 'View Event', $lang)?></a></p>
		</td>
	</tr>
</table>
<?php
	echo EVO()->get_email_part('footer');
?>