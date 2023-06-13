<?php
/**
 * Action User admin functions
 * @version 2.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoau_admin{
	public function __construct(){
		add_action('init', array($this, 'admininit'));
		add_action('admin_menu', array( $this,'menu'), 9);
	}

	// admin init
		function admininit(){
			global $eventon_au;
			add_action('eventon_add_meta_boxes', array($this,'evoAU_trigger_meta_box') );
			add_action('eventon_save_meta', array($this,'evoAU_save_meta_box_values'), 10, 2);

			include_once('class-admin_lang.php');

			
			// admin styles and scripts
			add_action( 'admin_enqueue_scripts', array($this,'eveoAU_admin_setting_styles') );
			add_action( 'eventon_admin_post_script', array( $this, 'backend_post_scripts' ) ,15);
			add_action( 'admin_enqueue_scripts', array($this,'evoau_admin_scripts' ));

			// other hooks
			add_filter('eventon_core_capabilities', array($this, 'add_new_capability_au'),10, 1);			
			add_action( 'user_row_actions', array( $this,'evoAU_user_row'), 10, 2 );

			// column for events page
			add_filter('evo_event_columns', array($this, 'add_column_title'), 10, 1);
			add_filter('evo_column_type_evoau', array($this, 'column_content'), 10, 1);

			// capabilities
			add_filter( 'map_meta_cap', array($this, 'my_map_meta_cap'), 10, 4 );

			// appearance
			add_filter( 'eventon_appearance_add', array($this,'appearance_settings') , 10, 1);
			add_filter( 'eventon_inline_styles_array',array($this,'dynamic_styles') , 1, 1);
			add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
			add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);

			// user profile page
			add_action('show_user_profile', array($this, 'extra_user_info'),10,1);
			add_action('edit_user_profile', array($this, 'extra_user_info'),10,1);
		}
	// MENUS
		function menu(){
			add_submenu_page( 'eventon', 'Action User', __('Action User','evoau'), 'manage_eventon', 'admin.php?page=eventon&tab=evoau_1', '' );
			//add_submenu_page( 'eventon', 'Action User', 'Action User', 'manage_eventon', 'action_user', array($this,'evoAU_action_user_fnct') );
		}
			

	// User profile fields
		function extra_user_info($user){			
			?>	
			<h3><?php _e('Event Information','evoau');?></h3>
			<table class='form-table'>
				<tr>
					<th><label><?php _e('User Capabilities','evoau');?></label></th>
					<td>
						<?php if(current_user_can('administrator')):?>
						<a href='<?php echo wp_nonce_url("admin.php?page=eventon&tab=evoau_1&amp;uid={$user->ID}#evoau_usercap", "evo_user_{$user->ID}");?>' class='button wp-generate-pw hide-if-no-js'>EventON Capabilities</a>
					<?php endif;?>
					</td>
				</tr>
				<?php do_action('evoau_user_profile_info', $user);?>
			</table>
			<?php
		}

	// appearance inserts
		function appearance_settings($array){
			extract( EVO()->elements->get_def_css());

			$new[] = array('id'=>'evoau','type'=>'hiddensection_open','name'=>'ActionUser Styles','display'=>'none');
			$new[] = array('id'=>'evoau','type'=>'fontation','name'=>'Submit Button',
				'variations'=>array(
					array('id'=>'evoau_1', 'name'=>'Background Color','type'=>'color', 'default'=>'52b4e4'),
					array('id'=>'evoau_2', 'name'=>'Text Color','type'=>'color', 'default'=>'ffffff')
				)
			);
			$new[] = array('id'=>'evoau_a1','type'=>'fontation','name'=>'Form',
				'variations'=>array(
					array('id'=>'evoau_a0', 'name'=>'Background Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoau_a1', 'name'=>'Border Color','type'=>'color', 'default'=>'d9d7d7'),
					array('id'=>'evoau_a2', 'name'=>'Field Label Color','type'=>'color', 'default'=> $evo_color_1),
					array('id'=>'evoau_a3', 'name'=>'Headers Text Color','type'=>'color', 'default'=> $evo_color_1)				
				)
			);
			$new[] = array('id'=>'evoau_a1','type'=>'fontation','name'=>'Location & Organizer Buttons',
				'variations'=>array(
					array('id'=>'evoau_lo1', 'name'=>'Background Color','type'=>'color', 'default'=>'52b4e4'),
					array('id'=>'evoau_lo2', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evoau_a1','type'=>'fontation','name'=>'Date Time Picker',
				'variations'=>array(
					array('id'=>'evoau_dtp1', 'name'=>'Date Background Color','type'=>'color', 'default'=>'f5c485'),
					array('id'=>'evoau_dtp1c', 'name'=>'Date Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoau_dtp2', 'name'=>'Time Background Color','type'=>'color', 'default'=>'f9d29f'),
					array('id'=>'evoau_dtp2c', 'name'=>'Time Text Color','type'=>'color', 'default'=>'717171'),
					array('id'=>'evoau_dtp3', 'name'=>'AM/PM Background Color','type'=>'color', 'default'=>'ffebd1'),
					array('id'=>'evoau_dtp3c', 'name'=>'AM/PM Text Color','type'=>'color', 'default'=>'717171'),
				)
			);
			$new[] = array('id'=>'evoau','type'=>'hiddensection_close','name'=>'ActionUser Styles');
			return array_merge($array, $new);
		}
		// Add settings to dynamic styles
			function dynamic_styles($_existen){
				extract( EVO()->elements->get_def_css());

				$new= array(
					array(
						'item'=>'.evoau_submission_form .submit_row input, .evoau_submission_form .evcal_btn',
						'multicss'=>array(
							array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fc',	'default'=>'ffffff'),
							array('css'=>'background:#$', 'var'=>'evcal_gen_btn_bgc',	'default'=>'52b4e4')
						)
					),array(
						'item'=>'.evoau_submission_form .submit_row input:hover',
						'multicss'=>array(
							array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fcx',	'default'=>'fff'),
							array('css'=>'background-color:#$', 'var'=>'evcal_gen_btn_bgcx',	'default'=>'52b4e4')
						)
					),array(
						'item'=>'.evoau_submission_form #evoau_form p #evoau_submit, body a.evoAU_form_trigger_btn, body .evoau_submission_form .msub_row a, body .evcal_btn.evoau, body .evoau_submission_form.loginneeded .evcal_btn',
						'multicss'=>array(
							array('css'=>'color:#$', 'var'=>'evoau_2',	'default'=>'ffffff'),
							array('css'=>'background-color:#$', 'var'=>'evoau_1',	'default'=>'52b4e4')
						)
					),
					array(
						'item'=>'body .evoau_submission_form',
						'multicss'=>array(
							array('css'=>'border-color:#$', 'var'=>'evoau_a1',	'default'=>'d9d7d7'),
							array('css'=>'background-color:#$', 'var'=>'evoau_a0',	'default'=>'ffffff')
						)
					),
					array(
						'item'=>'body #evoau_form .row .enterNew',
						'multicss'=>array(
							array('css'=>'color:#$', 'var'=>'evoau_lo2',	'default'=>'ffffff'),
							array('css'=>'background-color:#$', 'var'=>'evoau_lo1',	'default'=>'52b4e4')
						)
					),
					array(
						'item'=>'body .evoau_submission_form h2, body .evoau_submission_form h3',
						'css'=>'color:#$', 'var'=>'evoau_a3',	'default'=> $evo_color_1
					),
					array(
						'item'=>'body .evoau_submission_form p label',
						'css'=>'color:#$', 'var'=>'evoau_a2',	'default'=> $evo_color_1
					),
					array(
						'item'=>'.evoau_submission_form .row p .evo_date_time_select input.evoau_dpicker',
						'multicss'=> array(
							array('css'=>'background-color:#$', 'var'=>'evoau_dtp1',	'default'=>'f5c485'),
							array('css'=>'color:#$', 'var'=>'evoau_dtp1c',	'default'=>'ffffff'),
						)
					),
					array('item'=>'.evoau_submission_form .evo_date_time_select .evoau_time_edit .time_select select',
						'multicss'=> array(
							array('css'=>'background-color:#$', 'var'=>'evoau_dtp2',	'default'=>'f9d29f'),
							array('css'=>'color:#$', 'var'=>'evoau_dtp2c',	'default'=>'717171'),
						)
					),
					array('item'=>'.evoau_submission_form .evo_date_time_select .evoau_time_edit .time_select select.ampm_sel',
						'multicss'=> array(
							array('css'=>'background-color:#$', 'var'=>'evoau_dtp3',	'default'=>'ffebd1'),
							array('css'=>'color:#$', 'var'=>'evoau_dtp3c',	'default'=>'717171'),
						)
					),
				);

				return (is_array($_existen))? array_merge($_existen, $new): $_existen;
			}
		// Font families
		function primary_font($str){
			$str .= ',#eventon_form h2, 
				#eventon_form h3,
				#eventon_form p #evoau_submit, 
				a.evoAU_form_trigger_btn, 
				.evoau_submission_form .msub_row a, 
				.row .enterNew,
				.evoau_submission_form .formBtnS';
			return $str;
		}
		function secondary_font($str){
			return $str.',#eventon_form p input, 
				#eventon_form p textarea, 
				#eventon_form p select, 
				#eventon_form p.dropdown_row select,
				.evoau_cat_select_field,
				#eventon_form p select.evoau_location_select,
				#eventon_form p label,
				.evoau_selectmul,
				.row .evoau_img_preview span,
				.evoau_file_field span.evoau_img_btn,
				.eventon_au_form_section.overLay .closeForm';
		}
	
	// USERS page: Add capabilities edit button each users line
		function evoAU_user_row($actions, $user)  {
			global $pagenow;
			if ($pagenow == 'users.php') {				
				if (current_user_can( 'manage_eventon' )) {
				  $actions['evo_capabilities'] = '<a href="' . 
					wp_nonce_url("admin.php?page=eventon&tab=evoau_1&amp;uid={$user->ID}#evoau_usercap#evoau_usercap", "evo_user_{$user->ID}") . 
					'">' . __('EventON Capabilities', 'eventon') . '</a>';
				}      
			}
			return $actions;
		}
		// UPDATE user/role capabilities
			function update_role_caps($ID, $type='role', $postdata =''){
				
				$caps = eventon_get_core_capabilities();

				
				if($type == 'role'){

					// can not change the admin permissions
					if( $ID == 'administrator') return;

					global $wp_roles;
					
					$current_role_caps = $wp_roles->get_role($ID);		
					$cur_role_caps = ($current_role_caps->capabilities);	
					
					foreach($caps as $capgroupf=>$capgroup){			
						foreach($capgroup as $cap){

							
							if(!isset($postdata[$cap])) continue;

							//echo $cap.'// ';		

							
							// add cap
							// If capability exist currently
							if(array_key_exists($cap, $cur_role_caps)){ 
								if(isset($postdata[$cap]) && $postdata[$cap]=='no'){
									$wp_roles->remove_cap( $ID, $cap );	
							
								}
							}else{// if capability doesnt exists currently
								if(isset($postdata[$cap]) && $postdata[$cap]=='yes'){
									$wp_roles->add_cap( $ID, $cap );
								}
							}					
						}
					}
				}


				// for each user
				if($type=='user'){					
					$currentuser = new WP_User( $ID );
					$cur_role_caps = $currentuser->allcaps;
					
					foreach($caps as $capgroupf=>$capgroup){			
						foreach($capgroup as $cap){					
							// add cap
							// If capability exist currently
							if(array_key_exists($cap, $cur_role_caps)){ 
								if(isset($postdata[$cap]) && $postdata[$cap]=='no'){
									$currentuser->remove_cap( $cap );
								}
							}else{
							// if capability doesnt exists currently
								if(isset($postdata[$cap]) && $postdata[$cap]=='yes'){
									$currentuser->add_cap( $cap );
								}
							}					
						}
					}
				}
			}
		// save user specific capabilities
			public function my_map_meta_cap($caps, $cap, $user_id, $args ) {

				if ( ('edit_eventon' == $cap || 'delete_eventon' == $cap || 'read_eventon' == $cap ) && !empty($args[0])) {
					$post = get_post( $args[0] );
					$post_type = get_post_type_object( $post->post_type );

					$caps = array();

					if ( 'edit_eventon' == $cap ) {
						if ( $user_id == $post->post_author )
							$caps[] = $post_type->cap->edit_posts;
						else
							$caps[] = $post_type->cap->edit_others_posts;
					}

					elseif ( 'delete_eventon' == $cap ) {
						if ( $user_id == $post->post_author)
							$caps[] = $post_type->cap->delete_posts;
						else
							$caps[] = (!empty($post_type->cap->delete_others_posts)? 
								$post_type->cap->delete_others_posts:null);
					}

					elseif ( 'read_eventon' == $cap ) {

						if ( 'private' != $post->post_status )
							$caps[] = 'read';
						elseif ( $user_id == $post->post_author )
							$caps[] = 'read';
						else
							$caps[] = $post_type->cap->read_private_posts;
					}
				}

				/* Return the capabilities required by the user. */
				return $caps;
			}
			
	// ADMIN stylesheet
		function eveoAU_admin_setting_styles(){
			global $eventon_au;
			wp_enqueue_style( 'au_backend_settings',$eventon_au->plugin_url.'/assets/au_styles_settings.css');
		}
		function backend_post_scripts(){
			global $eventon_au;
			wp_enqueue_script('jquery-form');
			wp_enqueue_script( 'evo_au_backend',$eventon_au->plugin_url.'/assets/js/au_script_b.js',array('jquery'),$eventon_au->version,true);
			wp_localize_script( 'evo_au_backend', 'evoau_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
		}
		function evoau_admin_scripts(){
			global $pagenow, $eventon_au;
			
			if($pagenow=='admin.php' && $_GET['page']=='action_user'){			
				wp_register_script( 'evo_au_backend_admin',$eventon_au->plugin_url.'/assets/js/au_script_b_admin.js',array('jquery'),'1.0',true);
				wp_localize_script( 'evo_au_backend_admin', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));	
				wp_enqueue_script('evo_au_backend_admin');
			}
		}

	// ADD meta box on events page
		function evoAU_trigger_meta_box(){	
			// restrict access to user permission set box only to those can manage eventon
			add_meta_box('ajdeevcal_mb_au','Action User',  array($this,'evoAU_meta_box'),'ajde_events', 'side', 'low');	
		}

	/* Action User META BOX for events post page*/
		function evoAU_meta_box(){
			global $eventon, $post; 

			//testing
			global $eventon_au;
			//echo $eventon_au->frontend->_get_email_body('test',2346);

			if(current_user_can('manage_eventon') || current_user_can('assign_users_to_events')):
	
				$EVENT = new EVO_Event($post->ID);				
				
				// Lightbox Hookup
					global $ajde;
					echo $ajde->wp_admin->lightbox_content(array(
						'class'=>'evoau_lightbox_assign', 
						'content'=> "<p class='evo_lightbox_loading'></p>", 
						'title'=>__('Assign users to this event','eventon'),
						'type'=>'padded'
						)
					);
									
				// The actual fields for data entry
				$p_id = $post->ID;
				$pmv = $EVENT->get_data();
				
				$saved_users = wp_get_object_terms($p_id, 'event_users', array('fields'=>'slugs'));
				$saved_users = (!empty($saved_users))? $saved_users:null;
				
				//$all_users = get_users();			
				$assigned_users = array();	

				// Get Assigned users information
					if(is_array($saved_users)  && !empty($saved_users)){
						if( in_array('all', $saved_users) ){
							$assigned_users[] = array('all', 'All Users');
						}else{

							foreach($saved_users as $UID){
								$udata = get_userdata($UID);
								if(!$udata) continue;
								$assigned_users[] = array($UID, $udata->display_name);
							}
							
						}
					}				
			?>
				
				<!-- disable front end editting -->
				<p class='yesno_leg_line' style='padding-top:0px'>
					<?php 	
						$evoau_disableEditing = (!empty($pmv['evoau_disableEditing']))?
							$pmv['evoau_disableEditing'][0]:null;
						echo eventon_html_yesnobtn(
						array(
							'id'=>'evoau_disableEditing', 
							'var'=>$evoau_disableEditing,
							'input'=>true,
							'label'=>__('Disable frontend editing','eventon_cd'),
							'guide'=>__('This will disable users from editing this event on frontend event manager page. This value will override editing value saved in actionUser settings'),
							'guide_position'=>'L',
						));
					?>	
				</p>
				
				<?php
				// event access password
				if($EVENT->get_prop('_evoau_accesscode')){
					?>
					<p><?php _e('Event Access Password Set')?>: <span style='background-color: #e6e6e6; border-radius: 20px;padding: 3px 15px;'><?php echo $EVENT->get_prop('_evoau_accesscode');?></span></p>
					<?php
				}
				?>

				<div class="evoau_assign_users" style='margin-bottom:10px;'>
					<?php
						echo "<div class='evoau_assigned_users_in'>";
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
						echo "</div>";
					?>
					<input id='EVOAU_assigned_users' type='hidden' name='_evoau_assigned_users' value=''/>
					
					<?php if( !empty($post->post_author)):?>
						<p><b><?php _e('Event Author','eventon');?>:</b> <?php echo get_the_author_meta('display_name',$post->post_author);?></p>
					<?php endif;?>
				</div>
				
				<?php do_action('evoau_assigninfo_display', $p_id, $EVENT);?>
				
				<p style='margin-top:10px'><a class='button ajde_popup_trig evoau_load_lightbox_content' data-eventid='<?php echo $p_id;?>' data-popc='evoau_lightbox_assign' ><?php _e('Manage Assigned Users','eventon');?></a></p>
					
				<?php 
					// if submitters name present
					if(!empty($pmv['_submitter_name']) && !empty($pmv['_submitter_email'])):?>
						<p><i><?php _e('Event submitted by','eventon');?>: <b><?php echo $pmv['_submitter_name'][0]?> (<?php echo $pmv['_submitter_email'][0];?>)</b></i></p>
					<?php endif;?>
				
			<?php else:
				echo "<p>".__('You do not have permission to edit this section!','eventon')."</p>";
			endif;

			// additional private notes to admin
				if( (current_user_can('manage_eventon') || current_user_can('view_private_event_submission_notes'))  && !empty($pmv['evcalau_notes']) ){
					$notes = trim($pmv['evcalau_notes'][0]);
					if(!empty($notes))
						echo "<p class='evoau_private_note'><span><em>".__('Private notes','eventon')."</em>{$pmv['evcalau_notes'][0]}</span><p>";
				}

			do_action('evoau_assigninfo_display_end');
		}
			
	// SAVE meta box values for user assignments
		function evoAU_save_meta_box_values($fields, $post_id){	
			if(isset($_POST['evoau_disableEditing']))
				update_post_meta($post_id,'evoau_disableEditing',$_POST['evoau_disableEditing']);
			
		}

	// add a new capability to be able to manage eventon user capabilities
		function add_new_capability_au($caps){
			$new_caps = $caps;			
			$new_caps[] = 'manage_eventon_user_capabilities';			
			$new_caps[] = 'assign_users_to_events';			
			$new_caps[] = 'view_private_event_submission_notes';	
			$new_caps[] = 'submit_new_events_from_submission_form';	
			return $new_caps;
		}	

	// return HTML content for eventON role editor admin settings
		// type = role, user
		function get_cap_list_admin($ID, $type='role'){
			
			$content = $content_l = $content_r ='';	
			$count=1;
			if($type =='role'){
				global $wp_roles;
				//$wp_roles = new WP_Roles();
									
				$current_role_caps = $wp_roles->get_role($ID);					
				$cur_role_caps = ($current_role_caps->capabilities);	
				
			}

			if($type=='user'){
				$currentuser = new WP_User( $ID );
				$cur_role_caps = $currentuser->allcaps;
			}
						
			$caps = eventon_get_core_capabilities();

			//$caps['eventon'][] = 'edit_draft_eventons'; 
			//$caps['eventon'][] = 'delete_draft_eventons'; 
			//print_r($caps);

			foreach($caps as $capgroupf=>$capgroup){
				
				foreach($capgroup as $cap){
					if(in_array( $cap, array('delete_eventon','publish_eventon','edit_eventon')) ) continue;

					$rowcap = $cap;
					
					if($capgroupf=='core'){
						$cap = str_replace('eventon','eventon Settings', $cap);
					}else{
						$cap = str_replace('eventon','event', $cap);
					}
					
					$human_nam = ucwords(str_replace('_',' ',$cap));
					
					$yesno_val = ($ID=='administrator')? 'yes':((isset($cur_role_caps[$rowcap]))? 'yes':'no');
					$disabled = ($ID=='administrator')?'disable':null;
					
					$yesno_btn = eventon_html_yesnobtn(array('var'=>$yesno_val));

					$content= '<p class="yesno_row">'.$yesno_btn.'<input type="hidden" name="'.$rowcap.'" value="'.$yesno_val.'"><span class="field_name">'.$human_nam.'</span></p>';
					
					if($count >12){
						$content_r .=$content;
					}else{
						$content_l .=$content;
					}
					
					$count++;
				}
			}
			
			$content = "<table width='100%' ><tr><td valign='top'>".$content_l."</td><td valign='top'>".$content_r."</td></tr></table>";
			
			return $content;
		}

	// Assigned users for column for events
	// @version 1.8
		function add_column_title($columns){
			$columns['evoau']= __('Assigned Users','evoau');
			return $columns;
		}
		function column_content($post_id){

			$output = __('None','eventon');

			$saved_users = wp_get_object_terms($post_id, 'event_users', array('fields'=>'slugs'));
			$saved_users = (!empty($saved_users))? $saved_users:null;

			if(!empty($saved_users) && is_array($saved_users)){
				$output=array();
				foreach($saved_users as $user){
					$output[]= ($user=='all')? __('All Users','eventon'):
						get_the_author_meta('display_name', $user);
				}
				$output = implode(', ', $output);
			}

			$output = apply_filters('evoau_assigned_users_column', $output,  $post_id);

			return $output;
		}
}