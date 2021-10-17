<?php
/**
 * Admin class for RSS plugin
 *
 * @version 	0.1
 * @author  	AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evorss_admin{
	function __construct(){
		// icon in eventon settings
		add_filter( 'eventon_custom_icons',array($this,'custom_icons') , 10, 1);

		// appearance
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);

		// language
		add_filter('eventon_settings_lang_tab_content', array($this,'language_additions'), 10, 1);

		add_action('admin_init', array($this,'rssLanguage'));
		add_filter('eventon_settings_tab1_arr_content', array($this, 'evo_settings'), 10, 1);	
	}
	// settings
		function evo_settings($array){			
			$new_array = $array;
			
			$new_array[]= array(
				'id'=>'eventon_rss',
				'name'=>'Settings & Instructions for Event RSS Feed',
				'display'=>'none','icon'=>'rss',
				'tab_name'=>'RSS Feed',
				'fields'=> apply_filters('evo_se_setting_fields', array(
					array('id'=>'evorss_page','type'=>'text','name'=>__('Set page slug for RSS feed page'),'default'=>'evofeed', 'legend'=>__('You can use this to set a custom page slug for RSS feed for the calendar. eg. yoursite.com/evofeed')),
					array('id'=>'evorss_date','type'=>'yesno','name'=>__('Use event start date as RSS date','eventon'),'legend'=>__('By default event post date will be used for RSS item publish date'))
				)
			));
			
			return $new_array;

		}
		function rssLanguage(){
			update_option('rss_language', 'en');
		}
	// icons
		function custom_icons($array){
			$array[] = array('id'=>'evcal__evorss_001','type'=>'icon','name'=>'RSS Icon','default'=>'fa-rss');
			return $array;
		}
	// Appearnace section
		function appearance_settings($array){			
			$new[] = array('id'=>'evorss','type'=>'hiddensection_open','name'=>'RSS Styles');
			$new[] = array('id'=>'evorss','type'=>'fontation','name'=>'RSS Button',
				'variations'=>array(
					array('id'=>'evorss_1', 'name'=>'Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evorss_2', 'name'=>'Background Color','type'=>'color', 'default'=>'ff7e42'),					
				)
			);

			$new[] = array('id'=>'evotx','type'=>'hiddensection_close',);
			return array_merge($array, $new);
		}
		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.ajde_evcal_calendar .evorss_rss_btn.evcal_btn',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evorss_2',	'default'=>'ff7e42'),
						array('css'=>'color:#$', 'var'=>'evorss_1',	'default'=>'ffffff'),
					)
				)		
			);

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
	// language settings additinos
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: RSS FEED'),
					array('label'=>'RSS FEED for our calendar','name'=>'evoRSS_001',),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}


}
new evorss_admin();