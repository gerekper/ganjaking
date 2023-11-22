<?php
/**
 * Notification email sent to admin
 * @version 	0.1
 *
 * To Customize this template: copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/reviewer/ folder and edit that file.
 */

	global $eventon, $eventon_re;
	echo $eventon->get_email_part('header');

	$args = $args;

	$event_name = get_the_title($args['e_id']);
	$e_pmv = get_post_meta($args['e_id'] );
	$review_pmv = get_post_custom($args['review_id']);
	
	$evo_options = get_option('evcal_options_evcal_1');
	$evo_options_2 = $eventon_re->opt2;	
	$optRE = $eventon_re->opt;

	$lang = (!empty($args['lang']))? $args['lang']: 'L1';	 // language version
	$repeat_interval = (!empty($args['repeat_interval']))? $args['repeat_interval']: 0;	 // repeating interval

	//event time
		$__date = $eventon_re->frontend->_event_date($e_pmv, $repeat_interval);

	// location data
		$location = (!empty($e_pmv['evcal_location_name'])? $e_pmv['evcal_location_name'][0].': ': null).(!empty($e_pmv['evcal_location'])? $e_pmv['evcal_location'][0]:null);

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
	
	// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";
?>

<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='<?php echo $__sty_td;?>'>			
			<div style="padding:20px; font-family:'open sans'">
				<p style='<?php echo $__sty_lh;?>font-size:18px; font-style:italic; margin:0'><?php echo $eventon_re->lang('evoRSLX_010', 'You have received a new review for', $lang)?></p>
				<p style='<?php echo $__styles_01.$__sty_lh;?>'><?php echo $event_name;?></p>
				
				<!-- Rating -->
				<p style='<?php echo $__styles_02;?> padding-top:15px;'><span style='<?php echo $__styles_02a;?>'><?php echo $eventon_re->lang('evoRE_e_001', 'Rating', $lang)?>:</span> <?php echo $args['rating'];?></p>
				<!-- name-->
				<?php if(!empty($args['name']) ):
					echo $__item_p_beg;?><?php echo $eventon_re->lang('evoRE_e_002', 'Reviewer', $lang)?>:</span> <?php echo $args['name'];?></p>
				<?php endif;?>

				<!-- email address-->
				<?php if(!empty($args['email']) ):
					echo $__item_p_beg;?><?php echo $eventon_re->lang('evoRE_e_003', 'Email Address', $lang)?>:</span> <?php echo $args['email'];?></p>
				<?php endif;?>

				<?php if(!empty($args['review']) ):
					echo $__item_p_beg;?><?php echo $eventon_re->lang('evoRE_e_004', 'Review', $lang)?>:</span> <?php echo stripslashes($args['review']);?></p>
				<?php endif;?>

				
				<!-- event time -->
				<?php echo $__item_p_beg;?><?php echo $eventon_re->lang('evoRSLX_008', 'Event Time', $lang)?>:</span> <?php echo $__date['start'].' - '.$__date['end'];?></p>
				
			</div>
		</td>
	</tr>
	<?php
		$review_edit_link = get_edit_post_link($args['review_id']);
		$event_edit_link = get_edit_post_link($args['e_id']);

		if(!empty($review_edit_link) && !empty($event_edit_link)):
	?>

	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a target='_blank' href='<?php echo $review_edit_link; ?>'>Edit Review</a> | <a target='_blank' href='<?php  echo $event_edit_link;?>'>Edit Event</a></p>
		</td>
	</tr>
	<?php endif;?>
</table>

<?php
	echo $eventon->get_email_part('footer');
?>

