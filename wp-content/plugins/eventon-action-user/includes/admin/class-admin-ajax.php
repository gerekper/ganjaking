<?php
/**
 * ActionUser admin ajax section
 * @version 
 */

class evoau_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'evoau_load_assigned_users'=>'evoau_load_assigned_users',
			'evoau_save_assigned_users'=>'evoau_save_assigned_users',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// load the HTML for assigning users section
		function evoau_load_assigned_users(){

			// initial values
				$eventid = $_POST['e_id'];			
				$all_users = get_users();
				$saved_users = wp_get_object_terms($eventid, 'event_users', array('fields'=>'slugs'));
				$saved_users = (!empty($saved_users))? $saved_users:null;

			ob_start();?>
			<form>
				<input type="hidden" name='action' value='evoau_save_assigned_users'/>
				<input type="hidden" name='event_id' value='<?php echo $eventid;?>'/>
			<table style='vertical-align:top; width:100%'>
				<tr>
				<td valign='top'>
					<p><i><?php _e('Select users that are assigned to this event. This can be used to create calendars with users variable to show events from only those users. eg. [add_eventon users=\'2\']','eventon');?></i></p>
					<div id='evoau_us_list' class='evoau_users_list evoau_assign_selection'>
						<?php
							$checkbox_state = ''; $all = false;
							if(is_array($saved_users) && !empty($saved_users) && in_array('all', $saved_users)){
								$checkbox_state = 'checked="checked"';
								$assigned_users[] = array('all', 'All Users');
								$all = true;
							}

							echo "<p><input name='evoau_users[]' data-id='evoau_all' class='evoau_user_list_item allusers' type='checkbox' value='all' uname='".__('All','eventon')."' ".$checkbox_state."> ".__('All Users','eventon')."</p>";

							foreach($all_users as $uu){
								$checkbox_state='';
								if(is_array($saved_users) && !empty($saved_users) && in_array($uu->ID, $saved_users)){
									$checkbox_state = 'checked="checked"';
									$assigned_users[] = array($uu->ID, $uu->display_name);
								}
								
								if($all) $checkbox_state = 'checked="checked"';
								
								echo "<p><input name='evoau_users[]' data-id='evoau_".$uu->ID."' class='evoau_user_list_item' type='checkbox' value='".$uu->ID."' uname='".$uu->display_name."' ".$checkbox_state."> ".$uu->display_name." <i>(ID: {$uu->ID})</i></p>";
							}
						?>
					</div>
				</td><td valign='top'>
					<?php do_action('evoau_poptable', $eventid);?>							
				</td>
				</tr>
			</table>
			</form>
			<a class='evo_admin_btn btn_prime evoau_save_assigned_user_data' ><?php _e('Save','eventon');?></a>
			<?php

			echo json_encode(array(
				'content'=>ob_get_clean(),'status'=>'good'
			));
			exit;
		}

	// assign new users
		function evoau_save_assigned_users(){
			$event_id = $_POST['event_id'];
			
			$users = (!empty($_POST['evoau_users']))? $_POST['evoau_users']:null;	
			$result = wp_set_object_terms( $event_id, $users, 'event_users' );

			// perform the update
				$saved_users = wp_get_object_terms($event_id, 'event_users', array('fields'=>'slugs'));
				$saved_users = (!empty($saved_users))? $saved_users:null;

				do_action('evoau_save_assigned_user_data', $event_id);
			
			$all_users = get_users();			
			$assigned_users = array();	

			// Get Assigned users information
				if(is_array($saved_users)  && !empty($saved_users)){
					if( in_array('all', $saved_users) ){
						$assigned_users[] = array('all', 'All Users');
					}else{
						foreach($all_users as $uu){
							if( in_array($uu->ID, $saved_users)){
								$assigned_users[] = array($uu->ID, $uu->display_name);
							}
						}
					}
				}	

			ob_start();

			if(!empty($assigned_users)){
				echo "<h4>".__('Users Assigned to this Event','eventon')."</h4>";
				echo "<div class='EVOAU_assigned_users_list'>";
				foreach($assigned_users as $user){
					echo "<p><i>{$user[1]} ({$user[0]})</i></p>";
				}
				echo "</div>";
			}else{
				echo "<p>".__('You can assign users to this event and build calendars with events from only those users.','eventon')." <a href='http://www.myeventon.com/documentation/assign-users-events/' target='_blank'>".__('Learn More','eventon')."</a></p><br/>";
			}

			echo json_encode(
				apply_filters('evoau_saved_assigned_user_results',array(
					'content'=>ob_get_clean(),
					'status'=>'good'
				), $event_id)
			);
			exit;

		}
}
new evoau_admin_ajax();