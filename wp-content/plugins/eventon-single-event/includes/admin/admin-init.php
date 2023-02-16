<?php
/** 
 * Single Events Admin
 * @version  1.1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evose_admin{

	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
	}
	function admin_init(){
		add_filter( 'eventon_appearance_add',  array($this,'evoSE_appearance_settings') , 10, 1);
		add_filter( 'eventon_inline_styles_array', array($this,'evoSE_dynamic_styles') , 10, 1);
		add_filter( 'eventon_uix_shortcode_opts', array($this,'evoSE_shortcode_ux_opts') , 10, 1);

		// eventCard inclusion
			add_filter( 'eventon_eventcard_boxes',array($this,'eventcard_order') , 10, 1);
		// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'language_additions'), 10, 1);

		add_action('evcal_ui_click_additions', array( $this, 'event_meta_settings' ) );		
		add_action('eventon_admin_post_script', array( $this, 'event_meta_post_script' ) );

		// backend
			add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );	

		// settings
			add_filter('eventon_settings_tab1_arr_content', array( $this, 'single_event_settings' ) ,10,1 );
	}


	// eventcard inclusiong
		function eventcard_order($array){
			$array['evosocial']= array('evosocial',__('Social Share Icons','eventon'));
			return $array;
		}
		// inject into shortcode generator popup for user interaction options
			function evoSE_shortcode_ux_opts($array){
				$new_arr = $array;

				$new_arr['4']='Open in single event page';
				return $new_arr;
			}
	// appearance
		function evoSE_appearance_settings($array){
			
			$new[] = array('id'=>'evose','type'=>'hiddensection_open','name'=>'Social Media Styles');
			$new[] = array('id'=>'evose','type'=>'fontation','name'=>'Social Media Icons',
				'variations'=>array(
					array('id'=>'evose_1', 'name'=>'Icon Color','type'=>'color', 'default'=>'888686'),			
					array('id'=>'evose_3', 'name'=>'Icon Box Color','type'=>'color', 'default'=>'eaeaea'),
					array('id'=>'evose_2', 'name'=>'Icon Color (:Hover)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evose_4', 'name'=>'Icon Box Color (:Hover)','type'=>'color', 'default'=>'9e9e9e'),
					array('id'=>'evose_5', 'name'=>'Icon right border Color','type'=>'color', 'default'=>'cdcdcd')
					,				
				)
			);	
			$new[] = array('id'=>'evose','type'=>'hiddensection_close','name'=>'Social Media Styles');
			return array_merge($array, $new);
		}
		function evoSE_dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.evo_metarow_socialmedia a.evo_ss',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evose_3','default'=>'transparent'),
					)						
				),array(
					'item'=>'.evo_metarow_socialmedia a.evo_ss:hover',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evose_4','default'=>'9d9d9d'),
					)						
				),array(
					'item'=>'.evo_metarow_socialmedia a.evo_ss i',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evose_1','default'=>'858585')
					)						
				),array(
					'item'=>'.evo_metarow_socialmedia a.evo_ss:hover i',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evose_2','default'=>'ffffff')
					)						
				),array(
					'item'=>'.evo_metarow_socialmedia .evo_sm',
					'css'=>'border-color:#$', 'var'=>'evose_5','default'=>'cdcdcd'
				),
			);
			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}

	// language
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Single Events'),
				array('label'=>'Login','var'=>'1',),
				array('label'=>'You must login to see this event','var'=>'1'),
				array('label'=>'This is a repeating event','var'=>'1'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	// Eventon Settings Page Additions
		function single_event_settings($array){
			
			$new_array = $array;
			
			$new_array[]= array(
				'id'=>'eventon_social',
				'name'=>'Settings for Single Events',
				'display'=>'none',
				'tab_name'=>'Single Events',
				'icon'=>'calendar',
				'fields'=> apply_filters('evo_se_setting_fields', array(
					array('id'=>'evosm','type'=>'subheader','name'=>'Single Event Page',),
					array('id'=>'evosm_1','type'=>'yesno','name'=>'Create Single Events Page Sidebar',
							'legend'=>'This will create a sidebar for single event page, to which you can add widgets from Appearance > Widget'
						),
					array('id'=>'evosm_loggedin','type'=>'yesno','name'=>'Restrict single event pages to logged-in users only', 'legend'=>'Settings this will restrict single events page content to logged-in users to your site'),

					array('id'=>'evosm','type'=>'subheader','name'=>'Social Media Control',),

					array('id'=>'evosm_som','type'=>'yesno','name'=>'Show social media share icons only on single events', 'legend'=>'Setting this to Yes will only add social media share link buttons to single event page and single event box you created'),
					

					array('id'=>'evosm','type'=>'subheader','name'=>'Sharable Options',),
					array('id'=>'eventonsm_fbs','type'=>'yesno','name'=>'Facebook Share',),
					array('id'=>'eventonsm_tw','type'=>'yesno','name'=>'Twitter'),
					array('id'=>'eventonsm_ln','type'=>'yesno','name'=>'LinkedIn'),
					array('id'=>'eventonsm_gp','type'=>'yesno','name'=>'GooglePlus'),
					array('id'=>'eventonsm_pn','type'=>'yesno','name'=>'Pinterest (Only shows if the event has featured image)'),

					array('id'=>'eventonsm_email','type'=>'yesno','name'=>'Share Event via Email' ,'legend'=>'This will trigger a new email in the users device.','afterstatement'=>'eventonsm_email'),

					array('id'=>'eventonsm_note','type'=>'note','name'=>'NOTE: Go to "EventCard" and rearrange where you would like the social share icons to appear in the eventcard for an event.'),
				)
			));
			
			return $new_array;
		}

	/** Save event meta values **/
		function event_meta_settings(){
			
			$this_id = get_the_ID();
			
			$exlink_option = get_post_meta($this_id, '_evcal_exlink_option',true);
			
			
			$code ="<a link='yes' linkval='".get_permalink($this_id)."' class='evcal_db_ui evcal_db_ui_4 ".(($exlink_option=='4')?'selected':null)."' title='Open Event Page' value='4'></a>";
			
			echo $code;
		}
		/** Javascript for event post page */
			function event_meta_post_script(){
				global $eventon_sin_event;
				wp_enqueue_script('evo_sin_post_script',$eventon_sin_event->plugin_url.'/assets/post_script.js',array('jquery'),1.0,true);
			}
			

	/** backend pages **/
		function backend_scripts(){
			global $eventon_sin_event;
			wp_enqueue_style( 'evo_sin_wpadmin',$eventon_sin_event->plugin_url.'/assets/style-wp-admin.css');
		}
}
new evose_admin();