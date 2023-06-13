<?php
/** 
 * Event Manager for frontend user submitted events
 * @version 2.2.10
 * @author  AJDE
 *
 * You can copy this template file and place it in ...wp-content/themes/<--your-theme-name->/eventon/actionuser/ folder 
 * and edit that file to customize this template.
 * This sub content template will use default page.php template from your theme and the below
 * content will be placed in content area of the page template.
 */


	// INITIAL
	$fnc = new evoau_functions();

	do_action('evoau_manager_print_styles');

	$current_user = wp_get_current_user();

	$events = false;
	if(is_user_logged_in()){
		$events = EVOAU()->manager->get_user_events($current_user->ID);	
	}

	// verify if user has events
	if( !EVOAU()->manager->verify_user_events($events)) return false;

?>

<div id='evoau_event_manager' class='evoau_manager' data-mce='0'>

<?php
	echo "<h2 class='title'>". evo_lang('My Eventon Events Manager')."</h2>";

	// for not loggedin users
	if(!is_user_logged_in()){
		
		$evo_login_link = EVO()->cal->get_prop('evo_login_link');

		if($evo_login_link){
			$_add = strpos($evo_login_link, '?') !== false? '&':'?';
			$login_url = $evo_login_link . $_add. 'redirect_to='. $current_page_link;
		}else{
			$login_url = wp_login_url($current_page_link);
		}


		echo "<p class='intro'>".evo_lang('Login required to manage your submitted events')." <br/><a href='". $login_url ."' class='evcal_btn evoau'><i class='fa fa-user'></i> ".evo_lang('Login Now')."</a></p>";
		?></div><!--#evoau_event_manager--><?php
		return;
	}	
?>

<p class='evoau_intro'><?php evo_lang_e('Hello');?> <?php echo $current_user->display_name?>. <?php evo_lang_e('From your event manager dashboard you can view your submitted events and manage them in here');?></p>


<h3 class='evoau_subheader'><?php evo_lang_e('My Submitted Events');?><span style='float:right'><i class='fa fa-search evo_event_manager_search_trig'></i><?php echo count($events).' '.evo_lang('Events');?></span></h3>

<div class='evoau_search_form'>
	<input class='evoau_search_form_input' type='input' name='event_name' placeholder="<?php evo_lang_e('Type in event name to search visible events below');?>"/>
</div>

<?php		
	
	// GET events for the current user
	if($events){
		?>
		<div class='evoau_manager_event_section'>
		<div class='evoau_manager_event_list'>
		<div class='eventon_actionuser_eventslist'>
			<div class='evoau_delete_trigger' style="display:none" data-eid=''>
				<p class='deletion_message'><?php evo_lang_e('Are you sure you want to delete this event');?>? <br/><span class='ow'><?php evo_lang_e('Yes');?></span> <span class='nehe'><?php evo_lang_e('No');?></span></p>
			</div>			
			<?php
			do_action('evoau_manager_before_events', $events, $atts);

			// get each event
			do_action('evoau_manager_print_events', $events, $atts);

			do_action('evoau_manager_after_events', $atts);

			?>
		</div>
		<div class='evoau_manager_event'>
			<p style='margin-bottom:10px'><a class='evoau evoau_back_btn'><i class='fa fa-angle-left'></i> <?php echo evo_lang_e('Back to my events');?></a></p>
			<div class='evoau_manager_event_content trig_evo_loading'></div>
		</div>
		<div class='clear'></div>
		</div>

		<?php EVOAU()->manager->footer_json_data($atts);?>
		</div>
		<?php
	}else{
		echo "<p class='evoau_outter_shell'>". evo_lang('You do not have submitted events') . "</p>";
	}
?>
</div><!--#evoau_event_manager-->

