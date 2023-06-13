<?php
/**
 * Confirmation email sent to the attendee
 * @version 	2.9.3
 *
 * To Customize this template: copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/rsvp/ folder and edit the copied file customzie this email template.
 */

	
	echo EVO()->get_email_part('header');
	$args = $args;

	$RSVP = new EVO_RSVP_CPT($args['rsvp_id']);
	
	$eRSVP = new EVORS_Event( $RSVP->event_id(), $RSVP->repeat_interval());
	
	
	$EVENT = $eRSVP->event;
	$EVENT->get_event_post();
	
	$evo_options = get_option('evcal_options_evcal_1');
	$evo_options_2 = EVORS()->opt2;	
	$optRS = EVORS()->evors_opt;

	$lang = (!empty($args['lang']))? $args['lang']: evo_get_current_lang();	 // language version	
	EVO()->lang = $lang; // set evneton global language

	
	// location data
		$location =  false;
		
		$location_data = $EVENT->get_location_data();
		if($location_data){
			$location = (!empty($location_data['name'])? $location_data['name'].' - ': null).(!empty($location_data['location_address'])? $location_data['location_address']:null);
		}
		
	//event time	
		$readable_time = $EVENT->get_formatted_smart_time($RSVP->repeat_interval());

	//	styles
		$__styles_date = "font-size:48px; color:#ABABAB; font-weight:bold; margin-top:5px";
		$__styles_em = "font-size:14px; font-weight:bold; text-transform:uppercase; display:block;font-style:normal";
		$__styles_button = "font-size:14px; background-color:#".( !empty($evo_options['evcal_gen_btn_bgc'])? $evo_options['evcal_gen_btn_bgc']: "237ebd")."; color:#".( !empty($evo_options['evcal_gen_btn_fc'])? $evo_options['evcal_gen_btn_fc']: "ffffff")."; padding: 6px 10px; text-decoration:none; border-radius:4px;";
		$__styles_01 = "font-size:30px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; margin: 4px 0; line-height:110%;";
		$__styles_02b = "text-transform:none; font-size:14px; line-height:130%;padding:10px 0; display:inline-block";
		$__sty_lh = "line-height:110%;";
		$__styles_02a = "color:#afafaf; text-transform:none";
		$__styles_03 = "color:#afafaf; font-style:italic;font-size:14px; margin:0 0 10px 0;";
		$__styles_04 = "color:#303030; text-transform:uppercase; font-size:18px; font-weight:bold; padding-bottom:0px; margin-bottom:0px; line-height:110%;";
		$__styles_05 = "padding-bottom:40px; ";
		$__styles_06 = "border-bottom:1px dashed #d1d1d1; padding:5px 20px";
		$__styles_07 = "padding: 5px 15px;border: 3px solid #B7B7B7;border-radius:20px;font-weight:bold;text-transform:uppercase;font-size:20px;";
		$__sty_td ="padding:0px;border:none; text-align:center;";
		$__sty_m0 ="margin:0px;";
		$__sty_wb = "overflow-wrap:break-word;word-wrap:break-word;hyphens:auto";

	// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";		
?>

<table width='100%' style='width:100%; margin:0; font-family:"open sans",Helvetica' cellspacing="0" cellpadding="0">
	<tr>
		<td style='<?php echo $__sty_td;?>'>
			<div style="padding:45px 20px; font-family:'open sans',Helvetica;<?php echo $__sty_wb;?>">
				<p style='<?php echo $__sty_lh;?>font-size:18px; margin:0'><?php echo EVORS()->lang('evoRSLX_009', 'You have RSVP-ed', $lang)?></p>
				<p style='width:100%;text-align:center;display:block;padding:20px 0'><span style='<?php echo $__styles_07;?>'><?php echo EVORS()->frontend->get_rsvp_status( $RSVP->get_rsvp_status(), $lang);?></span></p>
				<p style='<?php echo $__styles_01.$__sty_lh;?> padding-bottom:15px;padding-top:30px'><?php echo $EVENT->get_title();?></p>

				<?php echo $__item_p_beg;?><?php echo EVORS()->lang('evoRSLX_008', 'Event Time', $lang)?>:</span> <?php echo $readable_time;?></p>

				<?php if($EVENT->content):?>
					<div style='<?php echo $__styles_02;?> padding-top:10px;'><span style='<?php echo $__styles_02a;?>'><?php echo EVORS()->lang('evoRSLX_008b', 'Event Details', $lang)?>:</span><br/><em style='<?php echo $__styles_02b;?>'><?php echo apply_filters('the_content',$EVENT->content);?></em></div>
				<?php endif;?>

				<p style='<?php echo $__styles_02;?> padding-top:10px;'><span style='<?php echo $__styles_02a;?>'><?php echo EVORS()->lang('evoRSL_007a', 'RSVP ID', $lang)?>:</span> # <?php echo $RSVP->ID;?></p>

				<?php echo $__item_p_beg;?><?php echo EVORS()->lang('evoRSLX_002', 'Primary Contact on RSVP', $lang)?>:</span> <?php 
				echo ($RSVP->first_name()? $RSVP->first_name():'') .' '. 
				($RSVP->last_name()? $RSVP->last_name():'');?></p>

				<?php if($RSVP->get_prop('names') ):

					$names = $RSVP->get_prop('names');
					$names = array_filter($names);
					if(count($names)>0):
				?>
					<?php echo $__item_p_beg;?><?php evo_lang_e('Additional guest names', $lang)?>:</span> 
					<?php echo implode(', ', $names );?></p>

				<?php endif;

				endif;?>

				<p style='<?php echo $__styles_02;?> padding-bottom:40px;'><span style='<?php echo $__styles_02a;?>'><?php echo EVORS()->lang('evoRSLX_003', 'Spaces', $lang)?>:</span> <?php echo $RSVP->get_prop_('count');?></p>
	

				<?php 
				//additional fields
				for($x=1; $x<=EVORS()->frontend->addFields; $x++){
					
					if( !EVO()->cal->check_yn('evors_addf'.$x,'evcal_rs') ) continue;
					if( !$RSVP->get_prop('evors_addf'.$x ) ) continue;

					// if show no AFs
					 	if($eRSVP->_show_none_AF()) continue;

					// if show only certain AFs
					 	if(!$eRSVP->_can_show_AF('AF'.$x)) continue;

					// skip file type
					 	if( EVO()->cal->get_prop('evors_addf'.$x.'_2','evcal_rs') == 'file' ) continue;

					echo $__item_p_beg. html_entity_decode( $optRS['evors_addf'.$x.'_1'] ) .": </span>".( $RSVP->get_prop('evors_addf'.$x)? $RSVP->get_prop('evors_addf'.$x ) : '-')."</p>";
					
				}
				
				//-- additional information -->
					if($EVENT->get_prop('evors_additional_data')){?>
						<p style='<?php echo $__styles_04;?>'><?php echo evo_lang('Additional Information', $lang);?></p>
						<p style='<?php echo $__styles_03;?> padding-bottom:10px;'><?php echo $EVENT->get_prop('evors_additional_data');?></p><?php
					}?>	

				<!-- location -->
				<?php if(!empty($location)):?>
					<p style='<?php echo $__styles_04;?>'><?php echo EVORS()->lang('evoRSLX_003x', 'Location', $lang)?></p>
					<p style='<?php echo $__styles_03;?> padding-bottom:10px;'><?php echo $location;?></p>
				<?php endif;?>

				<?php
				// customer password
				if( isset($args['password']) ){
					if( !EVO()->cal->check_yn('evors_reg_user','evcal_rs') || !EVO()->cal->check_yn('evors_disable_user_pass')){
						?>
						<p style='<?php echo $__styles_04;?>'><?php evo_lang_e('Your temporary password')?></p>
						<p style='<?php echo $__styles_03;?> padding-bottom:10px;'><?php echo $args['password'];?></p>
						<?php
					}
				}

				?>
				
				<?php do_action('eventonrs_confirmation_email', $RSVP, $eRSVP );?>
				
				<?php //add to calendar 
					$adjusted_event_times = $EVENT->get_utc_adjusted_times();
					$location = !empty($location) ? '&amp;loca='. stripcslashes($location): '' ;
				?>
				<p><a style='<?php echo $__styles_button;?>' href='<?php echo admin_url();?>admin-ajax.php?action=eventon_ics_download&event_id=<?php echo $EVENT->ID;?>&ri=<?php echo $EVENT->ri;?><?php echo $location;?>' target='_blank'><?php echo EVORS()->lang('evcal_evcard_addics', 'Add to calendar', $lang);?></a></p>
			</div>
		</td>
	</tr>
	<tr>
		<td  style='padding:20px; border-top:1px dashed #d1d1d1; color:#ADADAD; text-align:center;background-color:#f7f7f7;border-radius:0 0 5px 5px;'>
			<?php 
				$contactLink = (!empty($optRS['evors_contact_link']))? $optRS['evors_contact_link']:site_url();
			?>
			<p style='<?php echo $__sty_lh.$__sty_m0;?> padding-bottom:5px;'><?php echo EVORS()->lang('evoRSLX_005', 'We look forward to seeing you!', $lang)?></p>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='' href='<?php echo $contactLink;?>'><?php echo EVORS()->lang('evoRSLX_006', 'Contact Us for questions and concerns', $lang)?></a></p>
		</td>
	</tr>
</table>
<?php
	echo EVO()->get_email_part('footer');
?>