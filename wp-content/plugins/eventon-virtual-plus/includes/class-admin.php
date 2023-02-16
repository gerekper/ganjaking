<?php
/**
 * Admin
 * @version 0.1
 */

class EVOVP_Admin{
	public function __construct(){		
		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init(){

		// events post
		add_action('evo_editevent_vir_after_event_end', array($this, 'after_event'),10,1);
		add_action('evo_editevent_vir_before_after_event', array($this, 'before_after_event'),15,1);

		// addons list
		add_filter('evo_addons_details_list',array($this, 'addon_list'),10,1);

		// settigns
		add_filter('eventon_settings_tab1_arr_content', array($this, 'settings'),10,1);
			
		// language
			//add_filter('evors_lang_ar', array($this, 'language_additions'), 10, 1);
	}	


// event post
	public function before_after_event($EVENT){			

		?>
		<div class='evo_edit_field_box' style='background-color: #e0e0e0;' >
			<p style='font-size: 16px;'><b><?php _e('Pre-Event Information','evovp');?></b></p>
			<p style=''><?php _e('This information will appear before the event goes live/event start time. You can set when to show this content.','evovp');?></p>
			<?php
			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'type'=>	'textarea',
						'id'=>		'_vir_pre_content', 
						'value'=>		$EVENT->get_prop('_vir_pre_content'),
						'input'=>	true,
						'name'=> 	__('Text/html content or embed video html code', 'evovp'),
						'tooltip'=> __('Use this field to enter text or html content that will show before event go live.','evovp'),
					),					
					array(
						'type'=>	'dropdown',
						'id'=>		'_vir_pre_when', 
						'value'=>		$EVENT->get_prop('_vir_pre_when'),
						'input'=>	true,
						'options'=> apply_filters('evo_vir_pre_content_show',array(
							'900'=>__('15 Minutes before the event start','evovp'),
							'1800'=>__('30 Minutes before the event start','evovp'),
							'3600'=>__('1 Hour before the event start','evovp'),	
							'86400'=>__('1 Day before the event start','evovp'),
							'all'=>__('All the way till event start','evovp'),
						)),
						'name'=> 	__('When to show the above content on eventcard', 'evovp'),
					),					
				)
			);	
			?>
			<?php do_action('evovp_editevent_vir_pre_event_end', $EVENT);?>
		</div>
		<?php 
	}
	public function after_event($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(	
				array(
					'type'=>	'yesno_btn',
					'id'=>		'_vir_after_mod_end', 
					'value'=>		$EVENT->get_prop('_vir_after_mod_end'),
					'input'=>	true,					
					'label'=> 	__('Enable moderator must end event (only for jitsi)', 'evovp'),
					'tooltip'=> __('If set, moderator will see a button to end the event, unless this button is clicked the event will be live and after event content will not show. If this is enabled, after event content show time will be overridden & shown immediately after moderator end event.'),
				),				
				array(
					'type'=>	'dropdown',
					'id'=>		'_vir_after_content_dur', 
					'value'=>		$EVENT->get_prop('_vir_after_content_dur'),
					'input'=>	true,
					'options'=> apply_filters('evo_vir_after_content_duration',array(
						'always'=>__('All the time','evovp'),
						'1800'=>__('For 30 Minutes','evovp'),
						'3600'=>__('For 1 Hour','evovp'),	
						'86400'=>__('For 1 Day','evovp'),
						'172800'=>__('For 2 Days','evovp'),
						'259200'=>__('For 3 Days','evovp'),
						'345600'=>__('For 4 Days','evovp'),
						'432000'=>__('For 5 Days','evovp'),
						'604800'=>__('For 1 week','evovp'),
						'1209600'=>__('For 2 weeks','evovp'),
						'1814400'=>__('For 3 weeks','evovp'),
						'2419200'=>__('For 1 month','evovp'),
						'4838400'=>__('For 2 months','evovp'),
						'31556952'=>__('For 1 year','evovp'),
					)),
					'name'=> __('How long to show the after event content', 'evovp'),
					'tooltip'=> __('Set how long to show the after event content from the time it started to show.', 'evovp'),
				),					
			)
		);	
	}

// Settings
	public function settings($arr){

		$arr[] = array(
				'id'=>'evcal_vp',
				'name'=>__('Virtual Plus Settings','evovp'),
				'display'=>'show',
				'icon'=>'gears',
				'tab_name'=>__('Virtual Plus','evovp'),
				'top'=>'4',
				'fields'=> apply_filters('eventon_settings_general', array(

					array('id'=>'evo_realtime_vir_update',
						'type'=>'yesno',
						'name'=>__('Enable realtime virtual event information update. (Available only on event page)','evovp'), 
						'legend'=>__('This will enable heartbeat script to run and refresh virtual event data real time.','evovp')
					),
					array('id'=>'_vir_hrrate',
						'type'=>'dropdown','name'=>__('Page refresh rate to update virtual event information','evovp'),
						'width'=>'full',
						'legend'=> __('This set how fast the wordpress heartbeat script will run to update virtual event information automatically.','evovp'),
						'options'=>array(
							'15'=> __('Every 15 seconds','evovp'),
							'20'=> __('Every 20 seconds','evovp'),
							'30'=> __('Every 30 seconds','evovp'),
							'40'=> __('Every 40 seconds','evovp'),
							'50'=> __('Every 50 seconds','evovp'),
							'60'=> __('Every 60 seconds','evovp'),
						)
					),	
					array('id'=>'evovp_disable_signin',
						'type'=>'yesno',
						'name'=>__('Disable sign-in to virtual event access','evovp'), 
						'legend'=>__('This option will disable the default: if users are logged to rsvp or buy tickets, they will have to sign-in to see virtual events access.','evovp')
					),				
			)));
		return $arr;
		
	}

// Language 
	function language_additions($_existen){
		$new_ar = array(
			array('label'=>'VIRTUAL PLUS','type'=>'subheader'),
				array('var'=>1,'label'=>'Mark as live event ended'),
				array('var'=>1,'label'=>'Please sign in to access the virtual event information'),
			array('type'=>'togend'),
			
		);
		return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}

	function addon_list($array){
		$array['eventon-virtual-plus'] = array(
			'id'=>'EVOVP',
			'name'=>'Virtual Plus',
			'link'=>'http://www.myeventon.com/addons/virtual-plus/',
			'download'=>'http://www.myeventon.com/addons/virtual-plus/',
			'desc'=>'Extends virtual event features',
		);

		return $array;
	}

}

new EVOVP_Admin();