<?php
/**
 * Notification Email Template
 * @version 0.2
 */

	global $eventon, $eventon_au;
	echo $eventon->get_email_part('header');	
?>
<table style='padding:20px; width:100%; border-collapse: separate;'>
	<tr>
		<td>
			<p style='margin:0; padding-bottom:10px; color:#4D4D4D'><?php 
				echo isset($eventon_au->frontend->message)?$eventon_au->frontend->message:''; ?></p>
			<?php if(!empty($eventon_au->frontend->link)):?>
			<p style='margin:0; padding-bottom:10px; color:#4D4D4D'><?php 
				echo $eventon_au->frontend->link; ?></p>
			<?php endif;?>
		</td>
	</tr>
</table>

<?php
	echo $eventon->get_email_part('footer');
?>

