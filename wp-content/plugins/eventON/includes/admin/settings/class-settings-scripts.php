<?php
/*
 * Scripts and style settings
 */

class Evo_Admin_Settings_Scripts{
	public $options;
	public function __construct(){
		$this->options = EVO()->cal->get_op('evcal_1');
	}
	public function get(){

		return apply_filters('eventon_script_add',
			array(
				array('id'=>'evo_googlefonts','type'=>'yesno','name'=>__('Disable google web fonts','eventon'), 'legend'=>__('This will stop loading all google fonts used in eventon calendar.','eventon')),

				array('id'=>'evo_fontawesome','type'=>'yesno','name'=>__('Disable font awesome fonts','eventon'), 'legend'=>__('This will stop loading font awesome fonts in eventon calendar.','eventon')),
				array('id'=>'evcal_css_head','type'=>'yesno','name'=>__('Write dynamic styles to header','eventon'), 'legend'=>__('If making changes to appearances dont reflect on front-end try this option. This will write those dynamic styles inline to page header','eventon')),
				array('id'=>'evcal_concat_styles','type'=>'yesno',
					'name'=>__('Concatenate all eventon addon style files - Beta (Only supported addons)','eventon'), 
					'legend'=>__('Enabling this will create single style file for all the eventon addons activated in your site that support this feature. This will help improve loading speed. Furthermore you can use https://wordpress.org/plugins/autoptimize/ plugin to optimize JS files into just few files.','eventon')),
					
				array('id'=>'evo_load_scripts_only_onevo',
					'type'=>'yesno',
					'name'=>__('Load eventON scripts and styles only on eventON pages','eventon'), 
					'legend'=>__('This will load eventon scripts, google maps API and styles only when eventon shortcode is called in the page.','eventon'),
					'afterstatement'=>'evo_load_scripts_only_onevo'
				),
					array('id'=>'evo_load_scripts_only_onevo','type'=>'begin_afterstatement'),
					array('id'=>'evo_load_all_styles_onpages',
						'type'=>'yesno',
						'name'=>__('Load all eventON scripts/styles to page headers, where eventON is used','eventon'), 
						'legend'=>__('This will load eventon styles into every page header. This will make sure that styles are already loaded in the page when eventon calendar HTML is loaded on to the page and avoid delay in calendar layout rendering.','eventon')
					),
					array('id'=>'evo_load_scripts_only_onevo','type'=>'end_afterstatement'),

				array('id'=>'evo_dis_jqmobile',
					'type'=>'yesno',
					'name'=>__('Disable JQuery Mobile script','eventon'), 
					'legend'=>__('JQ Mobile sometimes cause conflicts for users and disabling this will stop eventON from loading JQ Mobile','eventon')
				),
				array('id'=>'evo_dis_moment',
					'type'=>'yesno',
					'name'=>__('Disable Moment script','eventon'), 
					'legend'=>__('This will disable moment library from loading into the pages','eventon')
				),	
				array('id'=>'evo_dis_jitsi',
					'type'=>'yesno',
					'name'=>__('Disable Jitsi script','eventon'), 
					'legend'=>__('This will disable jitsi external API from loading into front-end pages','eventon')
				),
			)
		);		
	}
}