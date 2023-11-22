<?php
/**
 * Email template for confirmation email
 * 
 * You can edit this template by copying this file to 
 * ../wp-content/themes/yourtheme/eventon/subscriber/
 *
 * @version  1.3
 */	

$evo_options = get_option('evcal_options_evcal_1');

// styles
	$styles = array(
		'btn'=>"background-color:#".( !empty($evo_options['evcal_gen_btn_bgc'])? $evo_options['evcal_gen_btn_bgc']: "237ebd")."; color:#".( !empty($evo_options['evcal_gen_btn_fc'])? $evo_options['evcal_gen_btn_fc']: "ffffff") . ";display: inline-block;padding: 5px 20px;border: none; border-radius:20px; text-decoration:none; font-style:normal;text-transform:uppercase;"
	);	
	
?>
<p><?php  evo_lang_e('Thank you for subscribing to our calendar events!');?></p>
<p><?php  evo_lang_e('You can manage your subscription settings from the below link.');?></p>
<p><a style='<?php echo $styles['btn'];?>' href='<?php echo $_link;?>'><?php  evo_lang_e('Subscriber Manager');?></a></p>
<p style='padding-top:20px'><i><?php  evo_lang_e('NOTE: If clicking on the link does not work, please copy the link and paste it in your browser window to verify your email address.');?></i></p>