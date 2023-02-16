<?php
/**
 * 
 * Admin section for search
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-sr/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosr_admin{
	
	public $optRS;
	function __construct(){
		add_filter('eventon_settings_lang_tab_content', array( $this, 'language' ), 10, 1);	
		add_filter('eventon_settings_tab1_arr_content', array( $this, 'search_settings' ) ,10,1 );
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);	
	}

	// appearance
		function appearance_settings($array){
			$new[] = array('id'=>'evors','type'=>'hiddensection_open','name'=>'Search Styles', 'display'=>'none');
				$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Search Field',
					'variations'=>array(
						array('id'=>'evosr_1', 'name'=>'Border Color','type'=>'color', 'default'=>'EDEDED'),
						array('id'=>'evosr_2', 'name'=>'Background Color','type'=>'color', 'default'=>'F2F2F2'),
						array('id'=>'evosr_3', 'name'=>'Border Color (Hover)','type'=>'color', 'default'=>'c5c5c5')	
					)
				);
				$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Search Icon',
					'variations'=>array(
						array('id'=>'evosr_4', 'name'=>'Color','type'=>'color', 'default'=>'3d3d3d'),
						array('id'=>'evosr_5', 'name'=>'Hover Color','type'=>'color', 'default'=>'bbbbbb'),	
					)
				);$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Search Effect',
					'variations'=>array(
						array('id'=>'evosr_6', 'name'=>'Background Color','type'=>'color', 'default'=>'f9d789'),
						array('id'=>'evosr_7', 'name'=>'Text Color','type'=>'color', 'default'=>'14141E'),	
					)
				);$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Events Found Data',
					'variations'=>array(
						array('id'=>'evosr_8', 'name'=>'Caption Color','type'=>'color', 'default'=>'14141E'),
						array('id'=>'evosr_9', 'name'=>'Event Count Background Color','type'=>'color', 'default'=>'d2d2d2'),	
						array('id'=>'evosr_10', 'name'=>'Event Count Text Color','type'=>'color', 'default'=>'ffffff'),	
					)
				);
				
			
			$new[] = array('id'=>'evors','type'=>'hiddensection_close',);

			return array_merge($array, $new);
		}

		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'body .EVOSR_section a.evo_do_search, body a.evosr_search_btn, .evo_search_bar_in a.evosr_search_btn',
					'css'=>'color:#$', 'var'=>'evosr_4',	'default'=>'3d3d3d'
				),array(
					'item'=>'body .EVOSR_section a.evo_do_search:hover, body a.evosr_search_btn:hover, .evo_search_bar_in a.evosr_search_btn:hover',
					'css'=>'color:#$', 'var'=>'evosr_5',	'default'=>'bbbbbb'
				),array(
					'item'=>'.EVOSR_section input, .evo_search_bar input','multicss'=>array(
						array('css'=>'border-color:#$', 'var'=>'evosr_1','default'=>'ededed'),
						array('css'=>'background-color:#$', 'var'=>'evosr_2','default'=>'ffffff')
					)	
				),array(
					'item'=>'.evosr_blur','multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evosr_6','default'=>'f9d789'),
						array('css'=>'color:#$', 'var'=>'evosr_7','default'=>'14141E')
					)	
				),array(
					'item'=>'.evo_search_results_count span','multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evosr_9','default'=>'d2d2d2'),
						array('css'=>'color:#$', 'var'=>'evosr_10','default'=>'f9d789')
					)	
				),array(
					'item'=>'.EVOSR_section input:hover, .evo_search_bar input:hover',
					'css'=>'color:#$', 'var'=>'evosr_3',	'default'=>'c5c5c5'
				),array(
					'item'=>'.evo_search_results_count',
					'css'=>'color:#$', 'var'=>'evosr_8',	'default'=>'14141E'
				)				
			);			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
	
	// language
		function language($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Search'),	
					array('label'=>'Search Events','name'=>'evoSR_001','legend'=>'placeholder for search input fields'),
					array('label'=>'Search Calendar Events','name'=>'evoSR_001a'),
					array('label'=>'Searching','name'=>'evoSR_002'),
					array('label'=>'What do you want to search for?','name'=>'evoSR_003'),
					array('label'=>'Event(s) found','name'=>'evoSR_004'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	/**
	 * Settings page content for search
	 * @param  $array 
	 * @return
	 */
		function search_settings($array){

			ob_start();?>

				<p>By default search icon and search bar are set to visible in all calendars, once you activate EventON Search.
				<br/>
				You can <strong>disable search</strong> by adding the before variable into shortcodes:
				<br/>
				<br/>
				<code>search="no"</code> example within a shortcode <code>[add_eventon search="no"]</code>
				<br/>
				<br/>
				The placeholder text that shows in the search bar can be edited from <strong>language</strong>.
				</p>


			<?php $content = ob_get_clean();
			
			$new_array = $array;
			
			$new_array[]= array(
				'id'=>'eventon_search',
				'name'=>'Settings & Instructions for Event Search',
				'display'=>'none','icon'=>'search',
				'tab_name'=>'Search Events',
				'fields'=> apply_filters('evo_sr_setting_fields', array(
					array('id'=>'evo_sr_001','type'=>'customcode',
							'code'=>$content),
					array('id'=>'EVOSR_showfield','type'=>'yesno','name'=>'Show search input field on calendar load','legend'=>'This will show the search field when the page first load instead of having to click on search button'),
					array('id'=>'EVOSR_advance_search','type'=>'yesno','name'=>'Enable Advanced Search','legend'=>'This will include custom meta data, category values and comments into search query pool'),

				)
			));
			
			return $new_array;
		}

}
new evosr_admin();
