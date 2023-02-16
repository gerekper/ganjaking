<?php 
/**
 * Invitation Email Template
 * @version 0.1
 * @customize place a copy of this file to .../wp-content/themes/<-- your theme -->/eventon/templates/email/rsvp/ folder
 * 
 */

// styles
	$__styles_01 = "font-size:36px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";	
	$__styles_btn = "padding: 8px 25px;text-decoration:none;cursor:pointer;border-radius:5px;color:#ffffff;font-size:24px;text-transform:uppercase;font-weight:bold;";
	$__sty_lh = "line-height:110%;";
	$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; font-style:italic; margin: 4px 0; line-height:110%;";	
	$__styles_02a = "color:#afafaf; text-transform:none";
	$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";

// location
	$location = false;	
	$location_data = $RSVP->event->get_location_data();
	if($location_data){
		$location = (!empty($location_data['name'])? $location_data['name'].' - ': null).(!empty($location_data['location_address'])? $location_data['location_address']:null);
	}

$lang = $args['lang'];

?>
<div style="padding:45px 20px; font-family:'open sans',Helvetica">
	<p style='line-height:110%;font-size:30px;margin:0;color:#a2a2a2;text-transform:uppercase;font-weight:bold;'><?php echo evo_lang('You are Invited!')?></p>

	<p style='<?php echo $__styles_01.$__sty_lh;?> padding-bottom:10px;'><?php echo $RSVP->event->get_title();?></p>
	
	<p style='font-size:18px;font-style:italic'><?php evo_lang_e('Please RSVP to let us know if you can make it!');?></p>
	<p style='width:100%;display:block;padding-bottom:25px;'>
		<a href='<?php echo $I->get_invite_link('y');?>' style='<?php echo $__styles_btn;?>background-color:#71ca7c;'><?php evo_lang_e('Yes');?></a>
		<a href='<?php echo $I->get_invite_link('n');?>' style='<?php echo $__styles_btn;?>background-color:#b4b4b4'><?php evo_lang_e('No');?></a>
	</p>
	
	<?php

	// Email Data section
	$data = apply_filters('evorsi_invitation_email_data',array(
		array( EVORS()->lang('evoRSLX_008a', 'Event Time', $lang), $RSVP->event->get_formatted_smart_time() ),
		array( EVORS()->lang('evoRSLX_003x', 'Location', $lang), ($location? $location:'') ),
	), $I, $RSVP, $args);

	// for each data field
	foreach($data as $vv){
		if(empty($vv[1])) continue;
		echo $__item_p_beg . $vv[0] .':</span> '. $vv[1] .'</p>';
	}
	?>
</div>