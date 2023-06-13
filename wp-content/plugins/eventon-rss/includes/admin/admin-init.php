<?php
/**
 * Admin class for RSS plugin
 *
 * @version 	1.1.4
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
		add_filter('eventon_troubleshooter', array($this,'troubleshooting'), 10, 1);

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
				'fields'=> apply_filters('evo_rss_setting_fields', array(
					array('id'=>'evorss_page','type'=>'text','name'=>__('Set page slug for RSS feed page'),'default'=>'evofeed', 'legend'=>__('You can use this to set a custom page slug for RSS feed for the calendar. eg. "evofeed" will give ..yoursite.com/evofeed')),
					array('id'=>'evorss_page','type'=>'note','name'=>__('NOTE: If changing the page slug cause page not found. Go to Settings > Permalinks and refresh permalinks','eventon')),
					array('id'=>'evorss_url','type'=>'text','name'=>__('Custom RSS feed link'),'default'=>'evofeed', 'legend'=>__('You can use this to link to a custom RSS feed link in case the default RSS feed link doesnt work due to any customization in your site. Be sure to enter a complete http:// link'),'default'=>'http://'),
					array('id'=>'evorss_page','type'=>'note',
						'name'=>__('<b>Other specific RSS feed links</b> You can use a custom links like <code>http://yoursite.com/evofeed?event_type=63</code> to get events only for certain event type. Other supported fields by default are event_type_2, event_type_3, event_location, event_organizer. You can pass value similar to inside eventon shortcodes.','eventon')),
					array('id'=>'evorss_date','type'=>'yesno',
						'name'=>__('Use event start date as RSS pubDate','eventon'),
						'legend'=>__('By default event post date will be used for RSS item publish date')),
					
					array('id'=>'evorss_paut','type'=>'yesno',
						'name'=>__('Always use post author as event feed item author','eventon'),
						'legend'=>__('By default event organizer is used as post author (if available). Setting this will always use post author.'),
					),
					array('id'=>'evorss_evotimeformating','type'=>'yesno',
						'name'=>__('Use eventON time formatting for event time in RSS event item description','eventon'),
						'legend'=>__('By default date() function is used to create event time. Setting this will use eventON language corrected date formatting'),
					),
					array('id'=>'evorss_order','type'=>'dropdown',
						'name'=>__('Order to show the events in the feed','eventon'),
						'legend'=>__('By default the oldest events will show in the bottom.'),
						'options'=> array(
							'ASC'=>'ASC',
							'DESC'=>'DESC'
						)
					),
					array('id'=>'evorss_orderby','type'=>'dropdown',
						'name'=>__('Order RSS event items by','eventon'),
						'options'=> array(
							'sort_date'=>'Event Date',
							'sort_posted'=>'Event Posted Date'
						)
					),
					array('id'=>'evorss_hide_past','type'=>'yesno',
						'name'=>__('Hide past events from the feed, based on event time','eventon'),
						'legend'=>__('By default the feed will show both past and future events.'),
					),
					array(
						'id'=>'evorss_title',
						'type'=>'text',
						'name'=>__('RSS Feed Title'),
						'default'=> apply_filters('evorss_blog_title', get_bloginfo_rss('name') .' - Event Feed'), 
					),
				)
			));
			
			return $new_array;

		}
		function rssLanguage(){
			update_option('rss_language', 'en');
		}
	// troubleshooting
		function troubleshooting($array){
			$newarray['RSS Addon'] = array(
				'My RSS Feed link show error page or 404'=>'First: Refresh permalinks, go into settings > permalinks and click save.</br><br/> If you are using a custom link struction for page URLs. Find the custom linkbase to a page. Go to myeventon > RSS Feed under Custom RSS feed link enter this custom linkbase and at the end add "/evofeed" <br/>An example  complete URL will look something like "http://www.mysite.com/index.php/evofeed"'
			);
			return array_merge($array, $newarray);
		}

	// icons
		function custom_icons($array){
			$array[] = array('id'=>'evcal__evorss_001','type'=>'icon','name'=>'RSS Icon','default'=>'fa-rss');
			return $array;
		}
	// Appearnace section
		function appearance_settings($array){			
			$new[] = array('id'=>'evorss','type'=>'hiddensection_open','name'=>'RSS Styles','display'=>'none');
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
					array('label'=>'START','var'=>'1'),
					array('label'=>'END','var'=>'1'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}


}
new evorss_admin();