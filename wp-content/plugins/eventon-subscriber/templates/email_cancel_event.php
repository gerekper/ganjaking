<?php
/**
 * Email Template: Cancel event
 * @version 0.1
 *
 * You can edit this template by copying this file to 
 * ../wp-content/themes/yourtheme/eventon/subscriber/
 */

	echo EVO()->get_email_part('header');
	$vars = isset($args['args'])? $args['args']: false;

	$event_name = sanitize_text_field($vars['event-name']);
	$event_id = $vars['e_id'];
	$e_pmv = get_post_custom($event_id );
	
	$evo_options = get_option('evcal_options_evcal_1');

	$admin_url = admin_url();

	// get processed event data from $_POST array when saving events
	$event_data = EVOSB()->admin->get_event_data($vars['e_id'], $e_pmv);

	//	styles
		$sty = array(
			'ttu'=>'text-transform:uppercase;',
			'ttn'=>'text-transform:none;',
			'mh110'=>'min-height:110%;',
			'lh110'=>'line-height:110%;',
			'm0'=>'margin:0px;',
			'p0'=>'padding:0px;',
			'p20'=>'padding:20px;',
			'brdn'=>'border:none;',
			'pb5'=>'padding-bottom:5px;',
			'pb20'=>'padding-bottom:20px;',
			'pb40'=>'padding-bottom:40px;',
			'clr1'=>'color:#afafaf;',
			'clr2'=>'color:#303030;',
			'ffo'=>'font-family:"open sans";',
			'fsi'=>'font-style:italic;',
		);
		$__styles_button = "font-size:14px; background-color:#".( !empty($evo_options['evcal_gen_btn_bgc'])? $evo_options['evcal_gen_btn_bgc']: "237ebd")."; color:#".( !empty($evo_options['evcal_gen_btn_fc'])? $evo_options['evcal_gen_btn_fc']: "ffffff")."; padding: 5px 10px; text-decoration:none; border-radius:4px; ";
		$__styles_01 = "font-size:30px;".$sty['clr2']." font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px;".$sty['clr2']." font-weight:normal; text-transform:uppercase; display:block; ".$sty['fsi']." margin: 4px 0; ".$sty['lh110'];
		$__styles_02b = "font-size:18px;".$sty['clr2']." font-weight:normal; display:block; ".$sty['fsi']." margin: 4px 0; ".$sty['lh110'];
		$__styles_03 = $sty['clr1'].$sty['fsi']."font-size:14px; margin:0 0 10px 0;";
		$__styles_04 = $sty['clr2'].$sty['ttu']."font-size:18px; ".$sty['fsi']." padding-bottom:0px; margin-bottom:0px; ".$sty['lh110'];
		$sty_001 = "margin-bottom:5px; border-radius:50%; overflow:hidden;height:90px; width:90px; ";

?>

<table width='100%' style='width:100%; margin:0; font-family:"open sans"'>
	<tr>		
		<td style='<?php echo $sty['p0'].$sty['brdn'];?>'>
			
			<div style="padding:20px;font-family:'open sans'">
				

				<p style='<?php echo $sty['lh110'].$sty['m0'].$sty['fsi'];?>font-size:18px;'><?php evo_lang_e('This Event is Cancelled');?></p>
				<p style='<?php echo $__styles_01.$sty['lh110'].$sty['pb20'];?>'><a target='_blank' href='<?php echo get_permalink($event_id);?>'><?php echo $event_name;?></a></p>
				
				<p style='<?php echo $__styles_02.$sty['pb5'];?>'><span style='<?php echo $sty['clr1'];?>'><?php evo_lang_e('Cancellation Reason');?>:</span> <?php echo !empty($event_data['_cancel_reason'])? $event_data['_cancel_reason']:'n/a';?></p>	
				
			</div>
		</td>
	</tr>
	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>			
			<p style='<?php echo $sty['lh110'].$sty['m0'];?>'><a style='' href='<?php echo EVOSB()->frontend->subscriber_url(array('action'=>'manage'));?>'><?php evo_lang_e('Manage your subscription settings');?></a> | <a href='<?php echo EVOSB()->frontend->subscriber_url(array('action'=>'unsubscribe'));?>'><?php evo_lang_e('Unsubscribe');?></a></p>
		</td>
	</tr>
</table>


<?php
	echo EVO()->get_email_part('footer');
?>

