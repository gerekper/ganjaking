<?php
/**
 * Digest email sent to ADMIN
 * @version 	0.1
 *
 * To Customize this template: copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/rsvp/ folder and edit that file.
 */

	global $eventon, $eventon_rs;
	echo $eventon->get_email_part('header');

	$args = $args;

	// Event Initiation
		$event_id = $args['e_id'];
		$EVENT = new EVORS_Event($event_id);

	$e_pmv = get_post_meta($args['e_id'] );
	
	$evo_options = get_option('evcal_options_evcal_1');
	$evo_options_2 = $eventon_rs->opt2;	
	$optRS = $eventon_rs->evors_opt;

	// Language
		$lang = (!empty($args['lang']))? $args['lang']: 'L1';	 // language version
		EVO()->lang = $lang; // set evneton global language

	// location data
		$location_data = $EVENT->event->get_location_data();
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
		$__sty_button ="display: inline-block;padding: 5px 10px;border: 1px solid #B7B7B7; text-decoration:none; font-style:normal;";
		
		$__sty_07 ="margin:0; font-size:48px;color:#646464; ";
		$__sty_07a ="margin:0; font-size:30px;color:#d0d0d0; ";
		$__sty_08 ="margin:0; background-color:#f4f1e9; color:#9A9A9A; border-radius:5px; border:1px solid #cecece; padding:4px 8px;text-transform:uppercase;font-size:18px; margin-left:8px;display:inline-block; ";
		$__sty_08a ="margin:0; background-color:#f4f1e9; color:#C5C5C5; border-radius:5px; border:1px solid #cecece; padding:4px 8px;text-transform:uppercase;font-size:16px; margin-left:8px;display:inline-block; ";
			
	// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";
?>

<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='<?php echo $__sty_td;?>'>			
			<div style="padding:20px; font-family:'open sans'">
				<p style='<?php echo $__sty_lh;?>font-size:18px; font-style:italic; margin:0; padding-bottom:10px; text-transform:uppercase'><?php evo_lang_e('Digest for event',$lang);?></p>
				<p style='<?php echo $__styles_01.$__sty_lh;?> padding-bottom:10px;'><?php echo $EVENT->event->get_title();?></p>
				
				<?php 
					// repeat intervals for this event
					$repeat_intervals = $EVENT->event->get_repeats();
					$this_repeat_intervals = array(0 => 0); // base set

					// for events with repeat instances 
						if( is_array($repeat_intervals) && count($repeat_intervals)>0 && $EVENT->event->check_yn('_manage_repeat_cap_rs') ){	
							$this_repeat_intervals = $repeat_intervals;
						}

					$repeat_interval = 0;
					foreach($this_repeat_intervals as $RI):

						$EVENT->ri = $RI;

						echo "<div style='padding-bottom:10px; border-bottom:1px dashed #E6E7E8; margin-bottom:10px;'>";
						$date_string = $EVENT->event->get_formatted_smart_time($repeat_interval);
						
						echo $__item_p_beg;?><?php echo $eventon_rs->lang('evoRSLX_008', 'Event Time', $lang)?>:</span> <?php echo $date_string;?></p>
						<?php
							$count_remain = $EVENT->remaining_rsvp();
							$count_yes = $EVENT->get_rsvp_count('y');
							$count_no = $EVENT->get_rsvp_count('n');
							$count_maybe = $EVENT->get_rsvp_count('m');
							$count_capacity = $EVENT->get_total_adjusted_capacity();
						?>
						<?php echo $__item_p_beg;?><?php echo $eventon_rs->lang('evoRSLX_email_03', 'Event RSVP Stats', $lang)?></p>
						<div style="padding-bottom:10px;padding-top:5px;">
							<div>
							<table>
								<tr>
									<td>
										<table style='padding-right:15px;'>
											<tr>
												<td><p style="<?php echo $__sty_07;?>"><?php echo $count_yes;?></p></td>
												<td><p style="<?php echo $__sty_08;?>"><?php echo $EVENT->trans_rsvp_status('y', $lang);?></p></td>
											</tr>
										</table>
									</td>
									<td>
										<table>
											<tr>
												<td><p style="<?php echo $__sty_07;?>"><?php echo $count_no;?></p></td>
												<td><p style="<?php echo $__sty_08;?>"><?php echo $EVENT->trans_rsvp_status('n', $lang);?></p></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<table style='padding-right:15px;'>
											<tr>
												<td><p style="<?php echo $__sty_07a;?>"><?php echo $count_capacity;?></p></td>
												<td><p style="<?php echo $__sty_08a;?>"><?php echo EVORS()->lang('evoRSLX_email_01', 'Capacity', $lang)?></p></td>
											</tr>
										</table>
									</td>
									<td>
										<table>
											<tr>
												<td><p style="<?php echo $__sty_07a;?>"><?php echo $count_remain;?></p></td>
												<td><p style="<?php echo $__sty_08a;?>"><?php echo EVORS()->lang('evoRSLX_email_02', 'Remaining', $lang)?></p></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							</div>
						<div style='clear:both'></div>
						</div>						
						<?php
						// the bar graph of rsvp data visualization
							if($count_capacity):
								$yes = (int)(($count_yes/$count_capacity)*100);
								$no = (int)(($count_no/$count_capacity)*100);
								$maybe = (int)(($count_maybe/$count_capacity)*100);
							?>
							<div style='padding-bottom:20px;'>
							<div style='background-color:#f2f2f2; padding:20px; margin:0 -22px;'>
								<p style='border-radius:5px; height:12px; padding:0; margin:0; overflow:hidden; background-color:#dedede;'>
									<span style='width:<?php echo $yes;?>%; display:inline-block; float:left;height:12px; background-color:#92c170'></span>
									<span style='width:<?php echo $no;?>%; display:inline-block; float:left;height:12px; background-color:#e98a8a'></span>
									<span style='width:<?php echo $maybe;?>%; display:inline-block; float:left;height:12px; background-color:#e3c85e'></span>
								</p>
							</div>
							</div>
							<?php endif;?>
						<?php 
						// guests list for this event repeat instance
						$guests = $EVENT->GET_rsvp_list();

						// if there are guests that are attending
						if(!empty($guests['y'])){
							echo "<div style='padding-bottom:20px;'>";
							echo $__item_p_beg. EVORS()->lang('evoRSL_002a', 'Guests List', $lang). ' ('.EVORS()->lang('evoRSL_002a1', 'Attending', $lang).")</p>";

							// for each guest that is attending
							foreach($guests['y'] as $guest){
								// if the guest count is greater than 1 show the count next to guest name
								$count = ($guest['count']>1)? ' (+'.($guest['count']-1).')':'';
								echo "<p style='display:inline-block; border-radius:8px; padding:3px 6px; background-color:#f2f2f2; margin:5px 5px 0; text-transform:uppercase; color:#8C8C8C;'>".$guest['name'].$count."</p>";
							}
							echo "</div>";
						}

						$repeat_interval++;

						echo "</div>";
					endforeach;
				?>				
			</div>
		</td>
	</tr>
	<?php
		// footer of the email with link to edit this event
		$event_edit_link = $EVENT->event->edit_post_link();
		if( !empty($event_edit_link)):
	?>
	<tr>
		<td  style='padding:15px; text-align:left; font-style:italic; color:#ADADAD'>				
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='<?php echo $__sty_button;?>' target='_blank' href='<?php  echo $event_edit_link;?>'>Edit Event</a></p>
		</td>
	</tr>
	<?php endif;?>
</table>
<?php
	echo EVO()->get_email_part('footer');
?>