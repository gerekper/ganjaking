<?php
/**
 * Notification email sent to ADMIN
 * @version 	2.9.3
 *
 * To Customize this template: copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/rsvp/ folder and edit that file.
 *
 * You can preview this email by visiting .../wp-admin/post.php?post={X}&action=edit&debug=true&type=notification_email  -- replace X with rsvp post ID
 */

	echo EVO()->get_email_part('header');

	$args = $args;

	$RSVP = $RR = new EVO_RSVP_CPT($args['rsvp_id']);
	$this_event = new EVORS_Event($RR->event_id(), $RR->repeat_interval());	

	if( !$this_event) return;
	
	$evo_options = get_option('evcal_options_evcal_1');
	$evo_options_2 = EVORS()->opt2;	
	$optRS = EVORS()->evors_opt;

	$lang = (!empty($args['lang']))? $args['lang']: 'L1';	 // language version
	EVO()->lang = $lang;

	
	// location data
		$location_data = $this_event->event->get_location_data();
		if($location_data){
			$location = (!empty($location_data['name'])? $location_data['name'].' - ': null).(!empty($location_data['location_address'])? $location_data['location_address']:null);
		}

	//styles
		$__styles_date = "font-size:48px; color:#ABABAB; font-weight:bold; margin-top:5px";
		$__styles_em = "font-size:14px; font-weight:bold; text-transform:uppercase; display:block;font-style:normal";
		$__styles_button = "font-size:14px; background-color:#".( !empty($evo_options['evcal_gen_btn_bgc'])? $evo_options['evcal_gen_btn_bgc']: "237ebd")."; color:#".( !empty($evo_options['evcal_gen_btn_fc'])? $evo_options['evcal_gen_btn_fc']: "ffffff")."; padding: 5px 10px; text-decoration:none; border-radius:4px; ";
		$__styles_01 = "font-size:30px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; font-style:italic; margin: 4px 0; line-height:110%;";
		$__sty_lh = "line-height:110%;";
		$__styles_02a = "color:#afafaf; text-transform:none";
		$__styles_03 = "color:#afafaf; font-style:italic;font-size:14px; margin:0 0 10px 0;";
		$__styles_04 = "color:#303030; text-transform:uppercase; font-size:18px; font-style:italic; padding-bottom:0px; margin-bottom:0px; line-height:110%;";
		$__styles_05 = "padding-bottom:40px; ";
		$__styles_06 = "border-bottom:1px dashed #d1d1d1; padding:5px 20px";
		$__sty_td ="padding:0px;border:none";
		$__sty_m0 ="margin:0px;";
		$__sty_button ="display: inline-block;padding: 5px 10px;border: 1px solid #B7B7B7; text-decoration:none; font-style:normal; border-radius:5px;";
	
	// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";

	// Notification Values
		$notice_type = (isset($args['notice_type']) && !empty($args['notice_type'])) ? $args['notice_type']: 'new_rsvp';
		$notice_title = (isset($args['notice_title']) && !empty($args['notice_title'])) ? $args['notice_title']: evo_lang('RSVP Notification');

?>

<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='<?php echo $__sty_td;?>'>			
			<div style="padding:20px; font-family:'open sans'">
				
				<?php do_action('evors_notification_email_top', $RSVP, $args);?>
				<?php

				// for various admin notices
				switch($notice_type){
					// filters for admin notification
					case has_action("evors_admin_notification_{$notice_type}"):
						do_action("evors_admin_notification_{$notice_type}", $RSVP, $this_event, $args);

					break;

					// default new rsvp admin notification
					case 'new_rsvp':
				?>

				<p style='<?php echo $__styles_01.$__sty_lh;?>'><?php echo $notice_title ?></p>
								
				<?php 

				// Attendee notification email message
					if(isset($args['notice_message'])):?>
						<p style='font-size:18px; color:#303030; font-weight:normal;display:block; font-style:italic; margin:8px 0; line-height:110%;'><?php echo $args['notice_message'];?></p>
				<?php endif;?>

				<p style='padding-top:20px;margin:0'></p>

				<?php

					

					$data = array(
						array(EVORS()->lang('evoRSLX_008a', 'Event Name', $lang), $this_event->event->get_title()),
						array(EVORS()->lang('evoRSL_007a', 'RSVP ID', $lang), $RSVP->rsvp_id ),
						array(EVORS()->lang('evoRSLX_001', 'RSVP Status', $lang), $RSVP->trans_rsvp_status() ),
						array(EVORS()->lang('evoRSL_007', 'First Name', $lang), $RSVP->first_name() ),
						array(EVORS()->lang('evoRSL_008', 'Last Name', $lang), $RSVP->last_name() ),
						array( EVORS()->lang('evoRSL_009', 'Email Address', $lang) , $RSVP->email() ),
						array( EVORS()->lang('evoRSL_009a', 'Phone Number', $lang) , $RSVP->get_prop('phone') ),
						array( EVORS()->lang('evoRSLX_003', 'Spaces', $lang) , $RSVP->count() ),
						array( EVORS()->lang('evoRSLX_008', 'Event Time', $lang) , $this_event->event->get_formatted_smart_time($RSVP->repeat_interval()) ),
						array( EVORS()->lang('evoRSLX_003a', 'Receive Updates', $lang) , $RSVP->get_prop('updates') ),
					);


					// additional guest names
						if($RSVP->get_prop('names')){
							$data[] = array(evo_lang('Additional guest names', $lang), implode(', ', $RSVP->get_prop('names') )	);
						}

					// for each data field
						foreach($data as $vv){
							if(empty($vv[1])) continue;
							echo $__item_p_beg . $vv[0] .':</span> '. html_entity_decode($vv[1]) .'</p>';
						}

					//additional fields
						$af_data = array();
						for($x=1; $x<= EVORS()->frontend->addFields; $x++){

							if( !EVO()->cal->check_yn('evors_addf'.$x,'evcal_rs') ) continue;
							if( !$RSVP->get_prop('evors_addf'.$x) ) continue;					
							
							// if show no AFs
							 	if($this_event->_show_none_AF()) continue;

							// if show only certain AFs
							 	if(!$this_event->_can_show_AF('AF'.$x)) continue;

							// if uploaded file
							 	if( EVO()->cal->get_prop('evors_addf'.$x.'_2','evcal_rs') == 'file' ){

							 		$media_id = $RSVP->get_prop('evors_addf'.$x);
							 		$url = wp_get_attachment_url( $media_id );
									
									if( !$url ) continue;

							 		$af_data[] = array(
							 			html_entity_decode($optRS['evors_addf'.$x.'_1']),
							 			$url
							 		);
							 	}else{
							 		$af_data[] = array(
								 		html_entity_decode($optRS['evors_addf'.$x.'_1']) , $RSVP->get_prop('evors_addf'.$x )
								 	);
							 	}							 		
						}

						// for each additional field data
						if( count($af_data)> 0){
							echo "<div style='background-color: #eee;border-radius: 10px; padding: 20px; margin-top: 20px;'><p style='{$__styles_02}'><span style='color:#333;'>" . evo_lang('Additional Information', $lang) . "</span></p>";
						}
						foreach($af_data as $vv){
							if(empty($vv[1])) continue;
							echo $__item_p_beg . $vv[0] .':</span> '. html_entity_decode($vv[1]) .'</p>';
						}
						if( count($af_data)> 0){
							echo "</div>";
						}

					// close switch case				
					break;
				}

				?>
			</div>
		</td>
	</tr>
	<?php
		$event_edit_link = $this_event->event->edit_post_link();
		$rsvp_edit_link = $RSVP->edit_post_link();

		if(!empty($rsvp_edit_link) && !empty($event_edit_link)):
	?>
	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>				
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='<?php echo $__sty_button;?>' target='_blank' href='<?php echo $rsvp_edit_link; ?>'><?php echo evo_lang( 'View RSVP', $lang)?></a>  <a style='<?php echo $__sty_button;?>' target='_blank' href='<?php  echo $event_edit_link;?>'><?php echo evo_lang( 'Edit Event', $lang)?></a></p>
		</td>
	</tr>
	<?php endif;?>
</table>
<?php
	echo EVO()->get_email_part('footer');
?>

