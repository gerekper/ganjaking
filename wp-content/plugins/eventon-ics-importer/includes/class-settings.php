<?php
/**
 * Admin Settings for ICS importer
 * @version 1.1.5
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//require_once 'lib2/IcalParser.php';
//use om\IcalParser;
//EVOICS()->parser = new IcalParser();




class EVOICS_settings{
	function __construct(){

		add_action('admin_init', array($this, 'admin_init'));	
		$this->fnc = EVOICS()->fnc;
	}

	function admin_init(){
		add_filter('eventon_settings_tabs',array($this, 'evo_tab_array' ),10, 1);
		add_filter('evo_save_settings_optionvals',array($this, 'before_save_settings' ),10, 2);
		add_action('eventon_settings_tabs_evoics_1',array($this, 'evo_tab_content' ));
	}

	function before_save_settings( $settings_array, $tab){
		if( $tab != 'evoics_1') return $settings_array;
		
		$settings_array['evoics_file_url'] = esc_url( $_POST['evoics_file_url']);
		return $settings_array;
	}

	function evo_tab_array($evcal_tabs){
		$evcal_tabs['evoics_1']='ICS';		
		return $evcal_tabs;
	}

	function evo_tab_content(){

		EVO()->evo_admin->settings->settings_tab_start(array(
			'field_group'=>'evors_field_group',
			'nonce_key'=> AJDE_EVCAL_BASENAME,
			'nonce_field'=>'evcal_noncename',
			'tab_id'=>'evcal_ics',
			'classes'=>array('evcal_admin_meta'. 'evcal_focus'),
			'inside_classes'=> array('evo_inside')
		));	

		$evcal_opt = $this->options = get_option('evcal_options_evoics_1'); 

		EVO()->evo_admin->settings->print_ajde_customization_form( $this->get_settings_fields_array(), $evcal_opt );
		EVO()->evo_admin->settings->settings_tab_end();

	}

	function get_settings_fields_array(){
		$help = new evo_helper();

		return array(
			array(
				'id'=>'EVOICSa',
				'name'=>'ICS Function Settings','display'=>'show',
				'tab_name'=>'Settings','icon'=>'gears',
				'fields'=>array(
					array('id'=>'EVOICS_status_publish',
						'type'=>'yesno',
						'name'=>'Publish imported events. (By default imported events will be saved as drafts)'
					),
					array('id'=>'evoics_timezone_method',
						'type'=>'dropdown',
						'name'=>'Event processing timezone method',
						'legend'=>'Select which timezone method to use when processing events from ics file.',
						'options'=>array(
							'none'=>'None, provided timezone from ics file',
							'wp'=>'WordPress timezone',
							'utc'=>'UTC timezone',
						)
					),
					array('id'=>'evoics_custom_tz','type'=>'yesno',
						'legend'=>'Select a custom timezone to use instead of the above setting.',
						'name'=>__('Use custom timezone for importing events (override above)','eventon'),
						'afterstatement'=>'evoics_custom_tz'),	
						array('id'=>'evoics_custom_tz','type'=>'begin_afterstatement'),
						array('id'=>'evoics_custom_tz_val','type'=>'dropdown','name'=>__('Select Custom Event Timezone','eventon'),'width'=>'full',
							'options'=> $help->get_timezone_array(false, true)
						),
						array('id'=>'evoics_custom_tz','type'=>'end_afterstatement'),

					array('id'=>'EVOICS_auto_allday_dis',
						'type'=>'yesno',
						'name'=>'Disable auto detect all day events based on ICS event time',
						'legend'=>'Timezone for the website need to be set as timezone string in wordpress settings. If not you can manually adjust times after import.'
						),
					array('id'=>'EVOICS_dupli_check',
						'type'=>'yesno',
						'name'=>'Enable duplication event name check during importing',
						'legend'=>'This will check for existing events with same name to avoid creating duplicate events.'
						),
					array(
						'id'=>'evoics_sync_fetched',
						'type'=>'yesno',
						'name'=>'Sync already imported events if event UID matches in the ICS file'
					),array(
						'id'=>'evoics_import_past',
						'type'=>'yesno',
						'name'=>'Import past events as well from ICS file',
						'legend'=> __('By default the system will import only upcoming events based on event start time. Enabling this option will make sure all the events, including those from past are also imported','evoics'),
					),
					array('id'=>'evoics_import_type',
						'type'=>'dropdown',
						'name'=>'Import Method',
						'options'=>array(
							'manual_file'=>'Manual import by uploading ICS File',
							'manual_link'=>'Manual import from ICS file URL',
							'schedule_daily'=>'Schedule import from ICS file URL - daily',
							'schedule_weekly'=>'Schedule import from ICS file URL - weekly',
							'schedule_monthly'=>'Schedule import from ICS file URL - monthly',
						),
						'legend'=>__('After making changes to import method, please save changes to reflect the changes for process ICS file.','evoics'),
					),array(
						'id'=>'evoics_file_url',
						'type'=>'text',
						'name'=>'ICS File URL - ONLY If you are using ICS file from external source',
						'default'=>'eg. http://www.google.com/ics/'
					),array(
						'id'=>'evoicenote',
						'type'=>'note',
						'name'=>'<b>NOTE:</b> If you are having trouble importing using URL, please make sure <code>allow_url_fopen</code> is enabled in your PHP configurations on the server',
					),

					array('id'=>'evoics_import','type'=>'customcode','code'=>$this->_import_content_section()),
			)),
		);					
	}

	function _import_content_section(){
		ob_start();

		EVO()->elements->print_trigger_element(array(
			'extra_classes'=>'evoics_triger',
			'styles'=> '',
			'title'=> __('Process ICS File'),
			'dom_element'=> 'span',
			'uid'=>'evoics_import_file',
			'lb_class' =>'evoics_import',
			'lb_title'=> __('ICS File Processing'),
			'ajax'=>'yes',	
			'ajax_data'=>array(
				'a'=> 'evoics_process_file'
			),
			'ajax_action'=> 'evoics_process_file'
		), 'trig_lb');


		?><div class='evoics_guidelines_section'><?php
					
		$this->fnc->print_guidelines();

		?></div>
		<?php

		return ob_get_clean();
	}	

	function _import_content(){
		ob_start();

		echo "<div id='evoics_2' class=''><div class='inside'>";
			$steps = (!isset($_GET['steps']))?'ichi':$_GET['steps'];	
			echo $this->import_content($steps);
			echo "</div></div>";

		return ob_get_clean();
	}

}
new EVOICS_settings();