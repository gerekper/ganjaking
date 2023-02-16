<?php 
/** 
 * Post Meta Boxes
 */
class evorm_meta_boxes{
	public function __construct(){
		//add_action('evors_event_metafields',array($this, 'rsvp_meta_box'), 10, 2);
		//add_action('evotx_event_metabox_end',array($this, 'tx_meta_box'), 10, 2);
		add_filter('eventon_event_metafields',array($this, 'save_fields'), 10, 2);

		add_action( 'add_meta_boxes', array($this, 'meta_boxes') );
	}

	function meta_boxes(){
		add_meta_box('evorm_mb1',__('All Event Reminders','eventon'), array($this, 'metabox_content'),'ajde_events', 'normal', 'high');
	}
	function metabox_content(){

		global $post, $eventon, $ajde;

		$event_id = $post->ID;
		$Event = new EVO_Event($event_id);

		//print_r($eventon);

		
		?>
		<div class='eventon_mb'>
		<div class="evors">			
			<div id='evorm_details' class='evorm_details evomb_body' style=''>
				
				<?php if( class_exists('EventON_rsvp')):?>			
	
				<h3 style='font-size:14px;'><?php _e('RSVP Reminders','eventon');?></h3>
				<div class='evo_negative_25'>
				<table width='100%' class='eventon_settings_table'>
				<?php $this->reminder_meta_box($event_id, 'rs', '');?>
				</table>
				</div>

				<?php endif;?>

				<?php if( class_exists('evotx')):?>			
	
				<h3 style='font-size:14px;'><?php _e('Ticket Reminders','eventon');?></h3>
				<div class='evo_negative_25'>
				<table width='100%' class='eventon_settings_table'>
				<?php $this->reminder_meta_box( $event_id, 'tx', '');?>
				</table>
				</div>

				<?php endif;?>
			</div>
		</div>
		</div>
		<?php
	}

	
	function tx_meta_box($event_id, $epmv){
		$this->reminder_meta_box( $event_id, 'tx',$epmv);
	}
	function rsvp_meta_box($epmv, $event_id){
		$this->reminder_meta_box( $event_id, 'rs', $epmv);
	}

	function reminder_meta_box($event_id, $addon, $epmv=''){
		// only for simple, non-repeating - events
		
		global $ajde;

		$Event = new EVO_Event($event_id);
		$CRON = new evo_cron();
		$fnc = new evorm_fnc();
		$all_cronjobs = $CRON->get_all_cron_hooks();
		
		$addon_name = $addon=='tx'? 'Tickets': 'RSVP';

		$showing_reminder_options = false;
		foreach(EVORM()->get_reminders() as $key=>$value){

			$check_enable = $addon == 'tx'? EVOTX()->check_tx_prop($value['var']): EVORS()->check_rsvp_prop($value['var']);
			$check_time = $addon == 'tx'? EVOTX()->get_tx_prop($value['var'].'_time'): EVORS()->get_rsvp_prop($value['var'].'_time');
			$check_msg = $addon == 'tx'? EVOTX()->get_tx_prop($value['var'].'_message'): EVORS()->get_rsvp_prop($value['var'].'_message');

			// check if enabled in settings and have time and message set in settings
			if($check_enable && $check_time && $check_msg){  
				$showing_reminder_options = true;	
					
				$field_variable = '_'.$value['var'].'-'.$addon;
				$cron_hook = 'evorm_reminder_'.$event_id. $field_variable;

				// Append a cron status next to reminder line
				$cron_status_addition = '';

				$styles = 'style="margin-left:50px; display:inline-block;font-size:12px; border-radius:5px; background-color:#adadad;padding:2px 7px;color:#fff"';
				
				// if there are current cron jobs for this reminder
				if( array_key_exists($cron_hook, $all_cronjobs) ){

					if( isset($all_cronjobs[$cron_hook]['time']) ){

						$time = esc_html( get_date_from_gmt( date( 'Y-m-d H:i:s', $all_cronjobs[$cron_hook]['time'] ), 'Y-m-d h:s:a' ) );

						$cron_status_addition = "<span {$styles}>". __('Set to run at','evorm').': '. $time ."</span>";
					}
				}else{

					// get reminder properties to check recorded status of reminder cron job
					$reminder_prop = $fnc->get_reminder_prop($event_id, $field_variable);

					$msg = __('No Cron jobs','evorm');

					if($reminder_prop == 'completed') $msg = __('Reminders Sent!','evorm');
					if($reminder_prop == 'attempted') $msg = __('Tried sending reminders!','evorm');
					
					$cron_status_addition = "<span {$styles}>". $msg."</span>";
				}

				?>
				<tr><td colspan='2'>
					<p class='yesno_row evo'><?php echo $ajde->wp_admin->html_yesnobtn(array(
						'id'=>	$field_variable,
						'var'=> $Event->get_prop( $field_variable ),
						'default'=>'',
						'label'=> $value['label'].' for this event'. $cron_status_addition,
						'guide_position'=>'L',
						'input'=>true
					));
					?></p>											
				</td></tr>				
				<?php

			}
		}

		// notice
		if($showing_reminder_options){
			?>
			<tr><td colspan='2'>
				<p class=' evo'><i><?php _e('Reminders Notice: Event time must be saved first and then enable reminders for the reminders cron jobs to be created. If you change event time, save changes once and save again to update cron job with new times.','evorm');?></i></p>											
			</td></tr>
			<?php
		}else{
			?>
			<tr><td colspan='2'>
				<p class=' evo'><i><?php _e('There are no active reminders.','evorm');?></i></p>											
			</td></tr>
			<?php
		}
		
	}

	// save fields
		function save_fields($array, $event_id){

			// set reminder email schedule during save post 
			foreach(apply_filters('evorm_event_save_fields', array(
				'evorm_pre_1', 'evorm_pre_2', 'evorm_post_1', 'evorm_post_2'
			)) as $var){

				foreach( array('tx','rs') as $addon){

					// skip the reminders that are not enabled in settings
					$options = get_option('evcal_options_evcal_'.$addon);
					if( !evo_settings_check_yn($options, $var) ) continue;

					$field_variable = '_'.$var . '-'.$addon;

					if(!empty($_POST[$field_variable]) && $_POST[$field_variable]=='yes'){					
						EVORM()->cron->schedule_reminders($event_id, $field_variable);
					}else{
						EVORM()->cron->unschedule_reminders($event_id, $field_variable);
					}

					$array[] = $field_variable;
				}
				
			}


			return $array;
		}
}
new evorm_meta_boxes();