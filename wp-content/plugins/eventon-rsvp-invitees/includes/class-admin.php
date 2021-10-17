<?php
/**
 * Admin
 */

class evorsi_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));		
	}

	function admin_init(){
		add_action('evors_admin_before_settings',array($this, 'before_event_settings'), 10, 1);
		
		add_action('evors_admin_rsvp_event_options_before',array($this, 'before_rsvp_options'), 10, 1);
		add_action('evors_event_metafields',array($this, 'rsvp_meta_box'), 10, 2);
		add_action('evors_event_metafield_names',array($this, 'save_fields'), 10, 2);
		add_action('evors_enqueue_admin_scripts',array($this, 'scripts'));

		// RSVP post
		add_action('evors_rsvppost_confirmation_end', array($this, 'resend_email'),10,1);
		// addons list
		add_filter('evo_addons_details_list',array($this, 'addon_list'),10,1);
		
		// backend
		add_action('evors_admin_cpt_column_rsvp_', array($this, 'column_rsvp'), 10,1);
		add_action('evors_admin_cpt_column_rsvp_status', array($this, 'column_rsvp_status'), 10,2);

		// settings
		add_action('evors_settings_fields',array($this, 'rsvp_settings'),10,1);
		add_filter('evors_appearance_settings', array($this, 'appearance_settings'), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_css') , 10, 1);

		// LANGUAGE
		add_filter('evors_lang_ar', array($this, 'language_additions'), 10, 1);
	}

	// scripts and styles
		function scripts(){
			wp_enqueue_script( 'evorsi_admin_script',EVORSI()->assets_path.'admin.js','',EVORSI()->version);
			wp_enqueue_style( 'evorsi_admin_style',EVORSI()->assets_path.'admin.css','',EVORSI()->version);
			wp_localize_script( 
				'evorsi_admin_script', 
				'evorsi_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( AJDE_EVCAL_BASENAME )
				)
			);
		}

	// settings
		function rsvp_settings($A){

			$A['evors_email']['fields'][] =	array(
				'id'=>'evors_dis_invitation_email',
				'type'=>'subheader',
				'name'=>'Invitation Emails'
			);
			$A['evors_email']['fields'][] =	array(
				'id'=>'evors_dis_invitation_email',
				'type'=>'yesno',
				'name'=>'Disable sending out invitation email'
			);
			$A['evors_email']['fields'][] =	array(
				'id'=>'evors_invitation_e_subject',
				'type'=>'text',
				'name'=>'Email Subject Line',
				'default'=>'You are invted!'
			);
			
			return $A;
		}

	// Appearance settings
		function appearance_settings($A){
			$A[] = array('id'=>'evors','type'=>'subheader','name'=>'RSVP Invitee Styles', );
			$A[] = array('id'=>'evors','type'=>'fontation','name'=>'Message Wall',
				'variations'=>array(
					array('id'=>'_evorsi_1', 'name'=>'Public Message Background Color','type'=>'color', 'default'=>'eaeaea'),
					array('id'=>'_evorsi_2', 'name'=>'Public Message Font Color','type'=>'color', 'default'=>'808080'),	
					array('id'=>'_evorsi_3', 'name'=>'Private Message Background Color','type'=>'color', 'default'=>'ded3bb'),
					array('id'=>'_evorsi_4', 'name'=>'Private Message Font Color','type'=>'color', 'default'=>'808080'),
					array('id'=>'_evorsi_5', 'name'=>'Admin Message Background Color','type'=>'color', 'default'=>'d2ebf9'),
					array('id'=>'_evorsi_6', 'name'=>'Admin Message Font Color','type'=>'color', 'default'=>'808080'),						
				)
			);

			return $A;
		}
		function dynamic_css($A){
			if(!is_array($A)) return $A;

			$new = array(
				array(
					'item'=>'.evorsi_message_wall p .t',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'_evorsi_1',	'default'=>'eaeaea'),
						array('css'=>'background:#$', 'var'=>'_evorsi_2',	'default'=>'808080')
					)
				),
				array(
					'item'=>'.evorsi_message_wall p.private .t',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'_evorsi_3',	'default'=>'ded3bb'),
						array('css'=>'background:#$', 'var'=>'_evorsi_4',	'default'=>'808080')
					)
				),
				array(
					'item'=>'.evorsi_message_wall p.admin .t,.evorsi_message_wall p.admin.private .t',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'_evorsi_5',	'default'=>'d9dfe2'),
						array('css'=>'background:#$', 'var'=>'_evorsi_6',	'default'=>'808080')
					)
				)
			);
			return array_merge($A, $new);

		}

	// LANG
		function language_additions($_existen){
			$new_ar = array(
				array('label'=>'Invitees','type'=>'subheader'),
					array('var'=>1,'label'=>'Sorry to hear you can not make it to the event!'),
					array('var'=>1,'label'=>'We look forward to seeing you at the event!'),
					array('var'=>1,'label'=>'We look forward to seeing you at our event! Please let us know your attendance!'),
					array('var'=>1,'label'=>'Only invited guests are allowed to RSVP!'),
					array('var'=>1,'label'=>'You are invited to [event-name]'),
					array('var'=>1,'label'=>'Please use the form below to let us know your attendance!'),
					array('var'=>1,'label'=>'Message Wall'),
					array('var'=>1,'label'=>'New message from the host'),
					array('var'=>1,'label'=>'New message from'),
					array('var'=>1,'label'=>'Post a message to wall or message host'),
					array('var'=>1,'label'=>'Send a message to host'),
					array('var'=>1,'label'=>'Post message to wall as well'),
					array('var'=>1,'label'=>'Post Message'),
					array('var'=>1,'label'=>'Created'),
					array('var'=>1,'label'=>'Waiting'),
				array('type'=>'togend'),
				array('label'=>'Invitees: other','type'=>'subheader'),
					array('var'=>1,'label'=>'Message could not be saved, try again later!'),
					array('var'=>1,'label'=>'Message successfully posted!'),
				array('type'=>'togend'),
				array('label'=>'Invitees: Emails','type'=>'subheader'),
					array('var'=>1,'label'=>'New RSVP Invitation Sent Out'),
					array('var'=>1,'label'=>'New RSVP Invitation has been created and the email has been sent out to the invitee.'),
					array('var'=>1,'label'=>'A guest has replied to invitation'),
					array('var'=>1,'label'=>'You have recived a respond from the guest for the invitation!'),
					array('var'=>1,'label'=>'You are Invited!'),
					array('var'=>1,'label'=>'Please RSVP to let us know if you can make it!'),
					
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	// evo-rsvp post
		function resend_email($RR){

			if($RR && $RR->get_rsvp_type() == 'invitee'){
				echo "<p>".__('To resend the invitation email to invitee please go to Event Edit > RSVP Invitee Manager','evorsi')."</p>";
			}

		}
	// wp-admin backend
		function column_rsvp($post_id){
			$I = new EVORSI_Invitee($post_id);
			if(!$I->is_invitee()) return false;

			if($I->get_prop('rsvp')) return false;
			echo "<p class='". $I->get_prop('status') ."'> - </p>";
		}
		function column_rsvp_status($S, $post_id){
			$I = new EVORSI_Invitee($post_id);
			if(!$I->is_invitee()) return $S;

			if(in_array($I->get_prop('status'), array('checked','check-in'))) return $S;
			return $I->get_prop('status');
		}

	// POST META BOX
		// before rsvp options begins
			function before_rsvp_options($RSVP){
				$Is =  new EVORSI_Invitees($RSVP->event_id);
				$this->is_invitee_active = $Is->is_invitee_active();
			}

		// before event rsvp options
			function before_event_settings($RSVP){
				if(!$this->is_invitee_active) return;

				echo "<p style='margin:20px 20px 0; font-style:italic'>" .__('NOTE: When invitees active, you can not set capacity.') ."</p>";
			}

		function rsvp_meta_box($RSVP, $OPT){

			global $ajde;

			$this->lightbox_content($RSVP);

			// debug
			//EVORSI()->manager->_send_email('invitation');

			if( $RSVP->event->is_repeating_event()){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Invitees for RSVP: Is not available for repeating events.','evorsw') ."</p>";
				?></td></tr><?php
				return;
			}

			if( $RSVP->event->check_yn('_evorsw_waitlist_on')):
				?><tr><td colspan='2'><?php
				echo "<p>". __('Invitees for RSVP: Can not use invitees while waitlist is active.','evorsw') ."</p>";
				?></td></tr><?php
			else:
			?>		
			<tr><td colspan='2'>
				<p class='yesno_leg_line ' >
				<?php echo $ajde->wp_admin->html_yesnobtn(

					array(
						'id'=>'evorsi_invitees',
						'var'=> $RSVP->event->get_prop('evorsi_invitees'),
						'label'=> __('Enable Invitee Only RSVP','evorsi'),
						'input'=>true,
						'afterstatement'=>'evorsi_section'
					)
				);?>	
				</p>						
			</td></tr>
		
			<?php $display = $RSVP->event->check_yn('evorsi_invitees') ? '':'none';?>
			<tr class='innersection yesnosub' id='evorsi_section' style='display:<?php echo $display;?>; background-color:#e8e8e8;'><td colspan='2' style='padding:25px'>
				
				<p><a class='evorsi_invitee_mgr ajde_popup_trig button_evo' data-popc='evorsi_lightbox'  data-eid='<?php echo $RSVP->event->ID;?>' ><?php _e('RSVP Invitee Manager','evorsi');?></a></p>
				<p class='yesno_leg_line ' >
				<?php echo $ajde->wp_admin->html_yesnobtn(
					array(
						'id'=>'_evorsi_messaging',
						'var'=> $RSVP->event->get_prop('_evorsi_messaging'),
						'label'=> __('Enable messaging on eventcard','evorsi'),
						'guide'=>__('This will allow only the invitees to message you (host) or post on message wall as well'),
						'input'=>true,
					)
				);?>	
				</p>
				<p class='yesno_leg_line ' >
				<?php echo $ajde->wp_admin->html_yesnobtn(
					array(
						'id'=>'_evorsi_invitee_wall',
						'var'=> $RSVP->event->get_prop('_evorsi_invitee_wall'),
						'label'=> __('Enable invitees only message wall (Messaging must be enabled as well)','evorsi'),
						'guide'=>__('This will show only messages posted for the wall by invitees only to invitees'),
						'input'=>true,
					)
				);?>	
				</p>
			</td></tr>
		
			<?php
			endif;
		}

		function lightbox_content($EVENT){
			
			global $ajde;

			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evorsi_lightbox rsvp_invitee_manager ', 
				'content'=>	"<p class='evo_lightbox_loading'></p>", 
				'title'=>__('Invitee Manager','eventon'), 
				'max_height'=>500,
				'outside_click'=>false
			));

			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evorsi_lightbox_two rsvp_invitee_manager_two ', 
				'content'=>	"<p class='evo_lightbox_loading'></p>", 
				'title'=>__('Invitee','eventon'), 
				'width'=>500,
				'outside_click'=>false
			));
		}

	// Save event post meta fields
		function save_fields($array, $event_id){

			// if rsvp invitee only enabled disable rsvp capacity
			if(isset($_POST['evorsi_invitees']) && $_POST['evorsi_invitees']=='yes'){
				$_POST['evors_capacity'] = 'no';
			}

			$array[] = 'evorsi_invitees';
			$array[] = '_evorsi_messaging';
			$array[] = '_evorsi_invitee_wall';
			return $array;
		}

	function addon_list($array){
		$array['eventon-rsvp-invitees'] = array(
			'id'=>'EVORSI',
			'name'=>'RSVP Invitees',
			'link'=>'http://www.myeventon.com/addons/rsvp-events-invitees/',
			'download'=>'http://www.myeventon.com/addons/rsvp-events-invitees/',
			'desc'=>'Invitees feature for RSVP',
		);

		return $array;
	}
}