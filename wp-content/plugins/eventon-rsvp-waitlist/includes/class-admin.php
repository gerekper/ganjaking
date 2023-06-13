<?php
/**
 * RSVP Waitlist Admin
 * @version 1.1.1
 */

class EVORSW_Admin{
	public $is_wl_active = false;

	public function __construct(){

		// AJAX
			add_action( 'wp_ajax_evorsw_add_attendee_list', array( $this, 'add_to_attendee_list' ) );
			add_action( 'wp_ajax_evorsw_move_to_waitlist', array( $this, 'move_to_waitlist' ) );
			add_action( 'wp_ajax_nopriv_evorsw_add_attendee_list', array( $this, 'add_to_attendee_list_nopriv' ) );
			add_action( 'wp_ajax_nopriv_evorsw_move_to_waitlist', array( $this, 'add_to_attendee_list_nopriv' ) );

		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init(){
		add_action('evors_admin_rsvp_event_options_before',array($this, 'event_edit_post_before'), 10, 1);
		add_action('evors_event_metafields',array($this, 'rsvp_meta_box'), 10, 2);
		add_action('evors_event_metafield_names',array($this, 'save_fields'), 10, 2);		
		add_action('evors_admin_eventedit_stats_end', array($this, 'rsvp_stats'), 10,1);
		add_action('evors_admin_eventedit_statbar_end', array($this, 'rsvp_stats_line'), 10,3);
		add_action('evors_attendee_info_lb_end', array($this, 'attendee_info_lb'), 10, 1);

		// emailing
		add_filter('evors_email_attendees_emailing_type', array($this, 'email_attendee_email_type'), 10,2);
		add_filter('evors_email_attendees_emails_array', array($this, 'email_attendee_emails'), 10,3);

		add_filter('evors_sync_after_query', array($this, 'after_sync_query'), 10,5);

		// addons list
		add_filter('evo_addons_details_list',array($this, 'addon_list'),10,1);

		// settigns
		add_filter('evors_settings_fields', array($this, 'settings_fields_rsvp'), 10, 1);		

		// RSVP CPT post meta
			add_action('evors_admin_rsvp_cpt_checkinstatus', array($this, 'checkinstatus_mod'),10,2);
	
		// language
			add_filter('evors_lang_ar', array($this, 'language_additions'), 10, 1);

		$this->help = new evo_helper();	
	}
	

	// AJAX
		function add_to_attendee_list(){

			$postdata = $this->help->sanitize_array($_POST);

			if( !isset($postdata['rsvp_id'])){
				echo json_encode(array(
					'status'=>'bad','message'=> __('Status: Bad - Missing rsvp id!')
				));		
				exit;
			}

			$RR = new EVO_RSVP_CPT( $postdata['rsvp_id']);
			$WL = new EVORSW_Waitlist( $RR->event_id() );

			// add the guest to attendees list
			$WL->add_to_event_attendee_list($RR, get_current_user_id() );

			// sync rsvp count
			$WL->sync_rsvp_count();

			$RR->load_rsvp_data();

			// actions based on where its coming from - lightbox or page
			if( isset($postdata['type']) && $postdata['type'] == 'from_lb'){	}else{	}

			$return_content = array(
				'status'=>'good',
				'content'=> __('Successfully moved to attendance list','evorsw'),
				'new_checkin_status'=> $RR->checkin_status()
			);			
			echo json_encode($return_content);		
			exit;

		}

		// move to waitlist from attendee list
		function move_to_waitlist(){
			$postdata = $this->help->sanitize_array($_POST);

			$RSVP_POST = new EVO_RSVP_CPT( $postdata['rsvp_id']);
			$WL = new EVORSW_Waitlist( $RSVP_POST->event_id() );				
			$WL->add_to_waitlist($RSVP_POST);

			$return_content = array(
				'status'=>'good',
				'content'=> __('Successfully moved to waitlist','evorsw'),
				'new_checkin_status'=> $RSVP_POST->checkin_status()
			);			
			echo json_encode($return_content);		
			exit;
		}

		function add_to_attendee_list_nopriv(){
			echo __('Status: Bad, You do not have permission!');	exit;
		}

		// when sync make sure empty spaces are offered to waitlist
		// run only on manual sync
		function after_sync_query($rsvp_count, $query_results, $RSVP, $ri_count, $sync_type){
			$WL = new EVORSW_Waitlist( $RSVP );

			if( $sync_type != 'manual_sync') return $rsvp_count;
			if( !$WL->is_waitlist_active()) return $rsvp_count;
			if( $RSVP->is_ri_count_active() ) return $rsvp_count;
			if( !$RSVP->is_capacity_limit_set()) return $rsvp_count;

			$remaining_rsvp = $RSVP->remaining_rsvp();

			if( $remaining_rsvp == 'wl' ) return $rsvp_count;
			if( $remaining_rsvp <1 ) return $rsvp_count;

			// remaining count > 1 / waitlist is on
			$waitlist_posts = $WL->get_waitlist();
			if( !$waitlist_posts) return $rsvp_count;

			$total_spaces_offered_waitlist = $WL->offer_space_to_waitlist( $remaining_rsvp , $waitlist_posts);

			$rsvp_count['y'] = $rsvp_count['y'] + $total_spaces_offered_waitlist;

			return $rsvp_count;
		}

		// attendee info LB from event edit page
		function attendee_info_lb( $RSVP_POST){

			// only allow this for admin
			if(!is_admin()) return false;
			if(!current_user_can('administrator')) return false;

			if( $RSVP_POST->status() == 'waitlist'){
								
				$btn_data = array(
					'd'=> array(
						'uid'=>'evorsw_add_to_list',
						'lightbox_key'=>'evors_get_one_attendee',
						'ajaxdata'=> array(					
							'rsvp_id'=> $RSVP_POST->ID,
							'type'=>'from_lb',
							'action'=> 'evorsw_add_attendee_list',
						)
					)
				);
				echo "<p class=''><a class='evo_admin_btn evo_trigger_ajax_run' ". $this->help->array_to_html_data($btn_data) .">". __('Add to event attendance list','evorsw') .'</a></p>';
			}else{
				$btn_data = array(
					'd'=> array(
						'uid'=>'evorsw_move_to_waitlist',
						'lightbox_key'=>'evors_get_one_attendee',
						'ajaxdata'=> array(					
							'rsvp_id'=> $RSVP_POST->ID,
							'type'=>'from_lb',
							'action'=> 'evorsw_move_to_waitlist',
						)
					)
				);
				echo "<p class=''><a class='evo_admin_btn evo_trigger_ajax_run' ". $this->help->array_to_html_data($btn_data) .">". __('Move to waitlist','evorsw') .'</a></p>';
			}
		}


	// POST META BOX
		// initial load
			public function event_edit_post_before($EVENT){
				$this->WAITLIST = new EVORSW_Waitlist($EVENT);

				$this->is_waitlist_active = $this->WAITLIST->is_waitlist_active();

			}
		// rsvp stats
			public function rsvp_stats( $EVENT){

				if( $this->is_waitlist_active ){
					$WL_size = $this->WAITLIST->get_waitlist_size();
					if(!$WL_size) $WL_size = 0;

					?><p class='wl'><b><?php echo $WL_size;?></b><span><?php _e('WaitList','evors');?></span></p><?php
				}
			}
			public function rsvp_stats_line($EVENT, $synced, $evors_capacity_count){
				return;
				$WAITLIST = new EVORSW_Waitlist($EVENT);
				if( $this->is_waitlist_active ){
					$WL_size = $this->WAITLIST->get_waitlist_size();

					if($WL_size >0 ){

					}
				}
			}

		// RSVP CPT
			function checkinstatus_mod($RSVP_POST){
				if( $RSVP_POST->checkin_status() != 'waitlist') return false;

				// only allow this for admin
				if(!is_admin()) return false;
				if(!current_user_can('administrator')) return false;
				

				$btn_data = array(
					'd'=> array(
						'uid'=>'evorsw_add_to_list_pg',
						'ajaxdata'=> array(					
							'rsvp_id'=> $RSVP_POST->ID,
							'type'=>'from_page',
							'action'=> 'evorsw_add_attendee_list',
						)
					)
				);

				echo "<a class='evo_admin_btn evo_trigger_ajax_run evorsw_add_to_list_pg' ".$this->help->array_to_html_data($btn_data).">" . __('Add to event attendance list') . "</a>";

			}
		
		// EVENT CPT
		function rsvp_meta_box($RSVP, $OPT){

			global $ajde;
			

			if( $RSVP->event->check_yn('evorsi_invitees')){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Waitlist for RSVP: Can not use waitlist while invitee is active.','evorsw') ."</p>";
				?></td></tr><?php
			}elseif(!$RSVP->is_capacity_limit_set()){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Waitlist for RSVP: capacity must be set before enabling waitlist.','evorsw') ."</p>";
				?></td></tr><?php

			}elseif( $RSVP->is_ri_count_active()){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Waitlist for RSVP: Does not support manage capacity separate for repeats.','evorsw') ."</p>";
				?></td></tr><?php
			}else{

				?>		
				<tr><td colspan='2'>
					<p class='yesno_leg_line ' >
					<?php echo $ajde->wp_admin->html_yesnobtn(
						array(
							'id'=>'_evorsw_waitlist_on',
							'var'=> $RSVP->event->get_prop('_evorsw_waitlist_on'),
							'label'=> __('Enable Waitlist for RSVP','evorsw'),
							'guide'=> __('This will allow waitlist for this event, once the RSVP capacity is set.','evorsw'),
							'input'=>true,
							'afterstatement'=>'evorsw_section'
						)
					);?>	
					</p>						
				</td></tr>
			
				<?php $display = $this->is_waitlist_active  ? '':'none';?>
				<tr class='innersection yesnosub' id='evorsw_section' style='display:<?php echo $display;?>; background-color:#e8e8e8;'><td colspan='2' style='padding:25px'>
					<?php	

					$WL_size = $this->WAITLIST->get_waitlist_size();

					if($WL_size):?>
						<p class='evorsw_waitlist_info'>
						<span class='evorsw_size'>
							<em style=''><?php echo $WL_size;?></em>
							<i><?php _e('Waitlist Size');?></i>
						</span>
						<span class='evorsw_note'>
							<?php _e('NOTE: Once an attending guest change their RSVP status to NO, their space will be offered to waitlist guest automatically.');?>
						</span>
						</p>						
					
					<?php else:?>
						<p><?php _e('Waitlist is empty!','evorsw');?></p>
					<?php endif;?>	

					<?php 
					/*
					// only if repeating events
					if($RSVP->event->is_repeating_event() && $RSVP->is_ri_count_active()):?>
					<div>
						<p class='yesno_leg_line ' >
						<?php echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'_evorsw_waitlist_repeat_on',
								'var'=> $RSVP->event->get_prop('_evorsw_waitlist_repeat_on'),
								'label'=> __('Enable separate waitlist for each event repeat','evorsw'),
								'guide'=> __('This will allow waitlist for this event, once the RSVP capacity is set.','evorsw'),
								'input'=>true,
							)
						);?>	
						</p>
					</div>
					<?php endif; */?>

				</td></tr>
			
				<?php
			}
		}

	// save fields
		function save_fields($array, $event_id){
			$array[] = '_evorsw_waitlist_on';
			$array[] = '_evorsw_waitlist_repeat_on';
			return $array;
		}

	// emailing
		function email_attendee_email_type( $array, $RSVP){
			$array['waitlist'] = __('Email to waitlist Guests','evorsw');
			return $array;
		}
		function email_attendee_emails($emails, $RSVP, $post_data){

			if( isset($post_data['evors_emailing_options']) && $post_data['evors_emailing_options'] == 'waitlist'){
				$guests = $RSVP->GET_rsvp_list('waitlist', 'all');
				
				foreach(array('y','m','n') as $rsvp_status){
					if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
						foreach($guests[$rsvp_status] as $guest){
							$emails[] = $guest['email'];
						}
					}
				}
			}
			return $emails;
		}

	// settings
		function settings_fields_rsvp($array){
			$array[] = array(
				'id'=>'evors_waitlist','display'=>'none',
				'name'=>'Waitlist Settings for RSVP',
				'tab_name'=>'waitlist','icon'=>'hourglass',
				'fields'=> array(
					array(
						'id'=>			"_evorsw_remove_from_wl",
						'type'=>		'yesno',
						'name'=>		__('Allow guests to remove themselves from waitlist'),
						'legend'=>		__('This will show option in event card for guests to remove themselves from waitlist, if they no longer wish to be in the waitlist.'),
					),
				) 			
			);

			return $array;
		}


	// Language 
		function language_additions($_existen){
			$new_ar = array(
				array('label'=>'WAITLIST','type'=>'subheader'),
					array('var'=>1,'label'=>'Get on the waitlist'),
					array('var'=>1,'label'=>'Waitlist is empty'),
					array('var'=>1,'label'=>'Waitlist Size'),
					array('var'=>1,'label'=>'Successfully added to waitlist for [event-name]'),
					array('var'=>1,'label'=>'All available spaces are taken, but we added you to waitlist for [event-name]'),
					array('var'=>1,'label'=>'All available spaces are taken, but we added you to waitlist!'),
					array('var'=>1,'label'=>'All the spaces are filled, but you will be added to our waitlist!'),
					array('var'=>1,'label'=>'You are in our waitlist!'),
					array('var'=>1,'label'=>'Make sure to add yourself to waitlist!'),
					array('var'=>1,'label'=>'Join the waitlist!'),
					array('var'=>1,'label'=>'Add yourself to waitlist'),
					array('var'=>1,'label'=>'All spaces are reserved!'),
					array('var'=>1,'label'=>'Waitlist is Open'),
					array('var'=>1,'label'=>'Remove me from waitlist'),
					array('var'=>1,'label'=>'NOTE: You will be offered space only when all your party size spaces available'),
					array('var'=>1,'label'=>'You are successfully removed from waitlist!'),
				array('type'=>'togend'),
				array('label'=>'EMAILS: Waitlist','type'=>'subheader'),
					array('var'=>1,'label'=>'Attendee Offered Space'),
					array('var'=>1,'label'=>'Waitlist attendee has been offered space to the event'),
					array('var'=>1,'label'=>'Added to waitlist'),
					array('var'=>1,'label'=>'You have been added to our waitlist. You will be offered space as soon as ample space open up'),
					array('var'=>1,'label'=>'You have been offered confirmed space!'),
					array('var'=>1,'label'=>'Your waitlist status has been moved to confirmed. We look forward to seeing you at the event.'),
					array('var'=>1,'label'=>'Add to event attendance list'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	function addon_list($array){
		$array['eventon-rsvp-waitlist'] = array(
			'id'=>'EVORSW',
			'name'=>'RSVP Waitlist',
			'link'=>'http://www.myeventon.com/addons/rsvp-events-waitlist/',
			'download'=>'http://www.myeventon.com/addons/rsvp-events-waitlist/',
			'desc'=>'Waitlist feature for RSVP',
		);

		return $array;
	}

}

new EVORSW_Admin();