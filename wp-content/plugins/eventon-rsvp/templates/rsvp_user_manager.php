<?php
/** 
 * User RSVP manager
 * @version 2.5.12
 * @author  AJDE
 *
 * You can copy this template file and place it in ...wp-content/themes/<--your-theme-name->/eventon/rsvp/ folder 
 * and edit that file to customize this template.
 * This sub content template will use default page.php template from your theme and the below
 * content will be placed in content area of the page template.
 */

$manager = new evors_event_manager();

	echo "<h2>". evo_lang_get('evoRSL_EM1', 'Events I have RSVPed to')."</h2>";
	if(!is_user_logged_in()){
		echo "<p>".evo_lang_get('evoRSL_EM2', 'Login required to manage events you have RSVP-ed.')." <br/><a href='".wp_login_url($current_page_link)."' class='evcal_btn evors'><i class='fa fa-user'></i> ".evo_lang_get('evoRSL_EM3', 'Login Now')."</a></p>";
		return;
	}
?>

<?php echo "<p class='intro'>". evo_lang_get('evoRSL_EM3a', 'Hello');?> <?php echo $current_user->display_name?>. <?php echo evo_lang_get('evoRSL_EM4', 'From your RSVP manager dashboard you can view events you have RSVP-ed to and update options.');
	echo "</p>";
?>

<h3><?php echo evo_lang_get('evoRSL_EM5', 'My Events');?></h3>
<?php	
	
	$rsvps = $manager->get_user_events($current_user->ID);

	if(!empty($rsvps)){
		echo "<div id='evors_rsvp_manager' class='eventon_rsvp_rsvplist' data-uid='{$current_user->ID}'>";
		echo $rsvps;
		echo "</div>";
	}else{ // user have not rsvped to any events

		echo "<p>". evo_lang_get('evoRSL_EM52', 'You have not RSVP-ed to any events yet') .'</p>';
	}
?>