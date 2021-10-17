<?php
/**
 * New user email sent to new user
 * @version 	2.6.15
 *
 * To Customize this template: copy and paste this file to .../wp-content/themes/--your-theme-name--/eventon/templates/email/rsvp/ folder and edit that file.
 *
 * You can preview this email by visiting .../wp-admin/post.php?post={X}&action=edit&debug=true&type=newuser_email  -- replace X with rsvp post ID
 */

echo EVO()->get_email_part('header');
$args = $args;

?>

<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='padding:10px 20px; border:none'>
			<p><?php evo_lang_e('Your new password'); echo ': '.$args['password'];?></p> 
		</td>
	</tr>
</table>

<?php
	echo EVO()->get_email_part('footer');
?>