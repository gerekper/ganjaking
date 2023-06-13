<?php
/**
 * ActionUser admin ajax section
 * @version 2.3.2
 */

class evoau_admin_ajax{
	public $help;

	public function __construct(){
		$ajax_events = array(
			'evoau_load_assigned_users'=>'evoau_load_assigned_users',
			'evoau_save_assigned_users'=>'evoau_save_assigned_users',

			'evoau_load_capability'=>'load_capability',
			'evoau_save_capability'=>'save_capability',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
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

	// capabilities manager
		function load_capability(){

			
			$postdata = $this->help->sanitize_array($_POST);
			$userid = isset($postdata['userid']) ? $postdata['userid'] : false;
			$this_role = isset($postdata['role']) ? $postdata['role'] : 'administrator';

			ob_start();

			$settings_page_role = 'administrator';

			?>
			<form method="post" action="" class='pad20'>
				<?php 
				settings_fields('evoau_field_group'); 
				wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoau_noncename' );

				echo EVO()->elements->process_multiple_elements(array(
					array('type'=>'hidden','name'=>'userid', 'value'=> $userid),
					array('type'=>'hidden','name'=>'action', 'value'=> 'evoau_save_capability' ),
				));
				?>
				<?php					
					// Capabilities for Individual user
					if( $userid  ):
						
						$cur_edit_user = new WP_User( $userid );
						
						if (!is_multisite() || current_user_can('manage_network_users')) {
							$anchor_start = '<a href="' . wp_nonce_url("user-edit.php?user_id={$userid}", 
							  "evo_user_{$userid}") .'" >';
							$anchor_end = '</a>';
						} else {
							$anchor_start = '';
							$anchor_end = '';
						}
						$user_info = ' <span style="font-weight: bold;">'.$anchor_start. $cur_edit_user->user_login; 
						if ($cur_edit_user->display_name!==$cur_edit_user->user_login) {
							$user_info .= ' ('.$cur_edit_user->display_name.')';
						}
						
						$user_info .= $anchor_end.'</span>';
						if (is_multisite() && is_super_admin($userid)) {
							$user_info .= '  <span style="font-weight: bold; color:red;">'. 	esc_html__('Network Super Admin', 'eventon') .'</span>';
						}						
				?>
					<h3 class='evopadb10'><?php _e('Capabilities for user','eventon');?> <?php echo $user_info . ' (#'. $userid .')';?></h3>
					<p><?php _e('Primary Role','eventon');?>: <b><?php echo $cur_edit_user->roles[0] ;?></b></p>				
					
					<div class='capabilities_list'>
						<h4 class='evopadb20'><?php _e('EventON Capabilities','eventon');?></h4>	
						<?php						
							echo EVOAU()->admin->get_cap_list_admin($userid, 'user');
						?>
					</div>				
				<?php	
					// capabilities for a ROLE				
					else:
				?>				
					<h3><?php _e('Select Role and set Capabilities for eventON','eventon');?></h3>
					<p><?php _e('Select Role','eventon');?> <select class='evoau_role_selector' name='current_role'>
					<?php
						global $wp_roles;
							
						$roles = $wp_roles->get_names();
						
						//print_r($roles);
						foreach($roles as $role=>$rolev){
							$selected = ( $this_role == $role)?'selected':null;
							echo "<option value='{$role}' {$selected}>{$rolev}</option>";
						}						
					?>
					</select></p>
					<div class='capabilities_list'>
						<?php						
							$caps =  EVOAU()->admin->get_cap_list_admin( $this_role );
							echo $caps;
						?>
					</div>
					</p><b>NOTE: </b><i><?php _e('Primary Administrator capabilities can not be changed. Permissions for each user can be configured separately from WordPress > Users page, by clicking on "EventON Capabilities" under each user. Each user permissions will prevail user role permissions for EventON.','eventon');?></i></p>					
				<?php endif;?>				
				<br/>
				<h3 class='evopadb20'><?php _e('Guide to Capabilities','eventon');?></h3>
				<p>
					<?php
					foreach(array(
						__('publish events','eventon') => __('Allow user to publish a event','eventon'),
						__('edit events','eventon') => __('Allow editing of the user\'s own events but does not grant publishing permission','eventon'),
						__('edit others events','eventon') => __('Allows the user to edit everyone else\'s events but not publish.','eventon'),
						__('edit published events','eventon') => __("Allows the user to edit his own events that are published.",'eventon'),
						__('delete events','eventon') => __("Grants the ability to delete events created by that user but not other.",'eventon'),
						__('delete others events','eventon') => __("Capability to delete events created by other users.",'eventon'),
						__('read private events','eventon') => __("Allow user to read private events.",'eventon'),
						__('assign event terms','eventon') => __("Allows the user to assign event terms to allowed events.",'eventon'),
						__('submit New Events From Submission Form','eventon') => __("Permission to submit events from new event submission form.",'eventon'),
						__('Upload Files','eventon') => __("Allow user to upload an image file for event image.",'eventon'),
					) as $key=>$val){
						echo "<span class='evopadb5' style='display:block'><b>". $key. "</b> - ". $val ."</span>";
					}
					?>
				</p>
								
				<?php
					EVO()->elements->print_trigger_element(
						array(
							'title'=>__('Save Changes','evoau'),
							'uid'=>'evoau_save_cap_manager',
							'lb_class' =>'config_user_capabilities',
							'lb_loader'=> true,
							'lb_hide'=> 3000
						), 'trig_form_submit'
					);
				?>
			</form>

			<?php
			echo json_encode(array(
				'content'=>ob_get_clean(),'status'=>'good') );
			exit;
		}

	// save user capabilities
		function save_capability(){

			$postdata = $this->help->sanitize_array($_POST);

			$type = 'role';
			if( isset($postdata['userid']) && !empty($postdata['userid'])) $type = 'user';
			$ID = isset($postdata['userid']) && !empty($postdata['userid']) ? $postdata['userid'] : $postdata['current_role'];

			EVOAU()->admin->update_role_caps($ID, $type, $postdata);

			echo json_encode(array(
				'status'=>'good',
				'msg'=> __("Successfully saved user capabilities"),
				'ID'=> $ID,
				'type'=> $type
			) );
			exit;

		}
}
new evoau_admin_ajax();