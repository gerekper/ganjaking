<?php
/**
 * Admin
 * @version 0.1
 */

class EVORSW_Admin{
	public function __construct(){

		// AJAX
			add_action( 'wp_ajax_evorsw_add_attendee_list', array( $this, 'add_to_attendee_list' ) );
			add_action( 'wp_ajax_nopriv_evorsw_add_attendee_list', array( $this, 'add_to_attendee_list_nopriv' ) );

		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init(){
		add_action('evors_event_metafields',array($this, 'rsvp_meta_box'), 10, 2);
		add_action('evors_event_metafield_names',array($this, 'save_fields'), 10, 2);

		// addons list
		add_filter('evo_addons_details_list',array($this, 'addon_list'),10,1);

		// settigns
		add_filter('evors_settings_fields', array($this, 'settings_fields_rsvp'), 10, 1);
		

		// RSVP CPT post meta
			add_action('evors_admin_rsvp_cpt_checkinstatus', array($this, 'checkinstatus_mod'),10,2);
	
		// language
			add_filter('evors_lang_ar', array($this, 'language_additions'), 10, 1);
	}
	

	// AJAX
		function add_to_attendee_list(){
			
			//verify nonce
			if( !wp_verify_nonce( $_REQUEST['nonce'], 'evorsw_attendee_list') ){
				echo __('Status: Bad - Invalid nonce varification!');exit;
			}else{

				if( !isset($_REQUEST['rsvp_id'])){
					echo __('Status: Bad - Missing rsvp id!');exit;
				}

				$RR = new EVO_RSVP_CPT( $_REQUEST['rsvp_id']);
				$WL = new EVORSW_Waitlist( $RR->event_id() );

				// add the guest to attendees list
				$WL->add_to_event_attendee_list($RR, get_current_user_id() );

				// sync rsvp count
				$WL->RSVP->sync_rsvp_count();

				wp_safe_redirect(  wp_get_referer() ); die;
			}
		}
		function add_to_attendee_list_nopriv(){
			echo __('Status: Bad, You do not have permission!');	exit;
		}

	// POST META BOX
		// RSVP CPT
			function checkinstatus_mod($RR){
				if( $RR->checkin_status() != 'waitlist') return false;

				// only allow this for admin
				if(!is_admin()) return false;
				if(!current_user_can('administrator')) return false;

				$ajax_url = add_query_arg(array(
				    'action' => 'evorsw_add_attendee_list',
				    'nonce'=> wp_create_nonce('evorsw_attendee_list'),
				    'rsvp_id'=> $RR->ID,
				), admin_url('admin-ajax.php'));

				echo "<a href='". $ajax_url ."' class='evo_admin_btn btn_triad'>" . __('Add to event attendee list') . "</a>";

			}
		
		// EVENT CPT
		function rsvp_meta_box($RSVP, $OPT){

			global $ajde;
			
			$WAITLIST = new EVORSW_Waitlist($RSVP);

			if( $RSVP->event->check_yn('evorsi_invitees')){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Waitlist for RSVP: Can not use waitlist while invitee is active.','evorsw') ."</p>";
				?></td></tr><?php
			}elseif(!$RSVP->is_capacity_limit_set()){
				?><tr><td colspan='2'><?php
				echo "<p>". __('Waitlist for RSVP: capacity must be set before enabling waitlist.','evorsw') ."</p>";
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
			
				<?php $display = $WAITLIST->is_waitlist_active() ? '':'none';?>
				<tr class='innersection yesnosub' id='evorsw_section' style='display:<?php echo $display;?>; background-color:#e8e8e8;'><td colspan='2' style='padding:25px'>
					<?php	

					$WL_size = $WAITLIST->get_waitlist_size();

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
					array('var'=>1,'label'=>'All the spaces are filled, but you will be added to our waitlist!'),
					array('var'=>1,'label'=>'You are in our waitlist!'),
					array('var'=>1,'label'=>'Add yourself to waitlist'),
					array('var'=>1,'label'=>'All spaces are reserved'),
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