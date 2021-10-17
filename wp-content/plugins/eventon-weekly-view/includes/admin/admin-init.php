<?php
/**
 * EventON WeeklyView Ajax Handlers
 *
 * Handles admin hook functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-WV/admin/
 * @version     1.0.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evowv_admin{
	public function __construct(){
		add_filter( 'eventon_appearance_add', array($this,'evoWV_appearance_settings') , 10, 1);
		add_filter( 'eventon_settings_time', array($this,'time_settings') , 10, 1);
		
		add_filter( 'eventon_inline_styles_array',array($this,'evoWV_dynamic_styles') , 1, 1);
		add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
		add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);
		
		// language
		add_filter('eventon_settings_lang_tab_content', array($this,'evoWV_language_additions'), 10, 1);
	}

	// time settings
		function time_settings($A){
			$A[] = array('id'=>'evowv_','type'=>'subheader','name'=>__('WeeklyView Time/Date Settings','eventon'));
			$A[] = array('id'=>'evowv_range_timeformat','type'=>'dropdown',
				'name'=>__('Select week range date form','eventon'),
				'width'=>'full',
				'options'=>array(
					'MM D/MM D, YYYY'=> 'Feb 1 - Feb 3, 2020',
					'MMMM D/MMMM D, YYYY'=> 'February 1 - February 3, 2020',
					'MM D, YYYY/MM D, YYYY'=> 'Feb 1, 2020 - Feb 3, 2020',
					'MMMM D, YYYY/MMMM D, YYYY'=> 'February 1, 2020 - February 3, 2020',
				)
			);
			return $A;
		}

	// appearance settings
		function evoWV_appearance_settings($array){
			
			$new[] = array('id'=>'evoWV','type'=>'hiddensection_open','name'=>'WeeklyView Styles','display'=>'none');
			$new[] = array('id'=>'evoWV','type'=>'fontation','name'=>'Date Box',
				'variations'=>array(
					array('id'=>'evowv_4c', 'name'=>'Date Box Background Color','type'=>'color', 'default'=>'fbfbfb'),
					array('id'=>'evowv_4d', 'name'=>'Date Box Text Color','type'=>'color', 'default'=>'949494'),
					array('id'=>'evowv_4e', 'name'=>'Date Box Border Color','type'=>'color', 'default'=>'f1f1f1'),
				)
			);
			$new[] = array('id'=>'evoWV','type'=>'fontation','name'=>'Focused Date Box',
				'variations'=>array(
					array('id'=>'evowv_1', 'name'=>'Background Color','type'=>'color', 'default'=>'f79191'),
					array('id'=>'evowv_2', 'name'=>'Text Color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evoWV','type'=>'fontation','name'=>'Other weeks dropdown',
				'variations'=>array(
					array('id'=>'evowv_3a', 'name'=>'Button Background Color','type'=>'color', 'default'=>'f79191'),
					array('id'=>'evowv_3b', 'name'=>'Dropdown Background Color','type'=>'color', 'default'=>'f79191'),
					array('id'=>'evowv_3c', 'name'=>'Dropdown Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evowv_3d', 'name'=>'Dropdown Hover Background Color','type'=>'color', 'default'=>'ef7777'),
					array('id'=>'evowv_3e', 'name'=>'Dropdown Current Week Background Color','type'=>'color', 'default'=>'ef7777'),
					array('id'=>'evowv_3f', 'name'=>'Dropdown Hover Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evowv_3g', 'name'=>'Border Color','type'=>'color', 'default'=>'f7a6a6'),
				)
			);
			$new[] = array('id'=>'evoWV','type'=>'fontation','name'=>'Week Strip',
				'variations'=>array(
					array('id'=>'evowv_4f', 'name'=>'Today Date Box Top Bar Color','type'=>'color', 'default'=>'f79191'),
				)
			);
			$new[] = array('id'=>'evoWV','type'=>'hiddensection_close','name'=>'WeeklyView Styles');
			return array_merge($array, $new);
		}
	// Font families
		function primary_font($str){
			$str .= ',.evoWV_days .evo_wv_day span.day_num,
			.evoWV_days .evo_wv_day span.day_name,
			.evowv_tooltip,
			ul.EVOWV_date_ranges li';
			return $str;
		}
		function secondary_font($str){
			return $str.',.eventon_weeklyview';
		}

	// dynamic styles saving
		function evoWV_dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.ajde_evcal_calendar.evoWV .EVOWV_content .EVOWV_grid .evo_wv_day.focus .evowv_daybox',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evowv_1','default'=>'f79191'),
						array('css'=>'color:#$', 'var'=>'evowv_2','default'=>'ffffff')
					)						
				),array(
					'item'=>'.EVOWV_content .EVOWV_change',
					'css'=>'background-color:#$', 'var'=>'evowv_3a','default'=>'f79191'					
				),array(
					'item'=>'.EVOWV_content .EVOWV_ranger',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evowv_3b','default'=>'f79191'),
						array('css'=>'color:#$', 'var'=>'evowv_3c','default'=>'ffffff')
					)						
				),array(
					'item'=>'.EVOWV_content ul.EVOWV_date_ranges li:hover, .EVOWV_ranger a:hover',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evowv_3d','default'=>'ef7777'),
						array('css'=>'color:#$', 'var'=>'evowv_3f','default'=>'ffffff')
					)						
				),array(
					'item'=>'.EVOWV_content ul.EVOWV_date_ranges li.thisweek',
					'css'=>'background-color:#$', 'var'=>'evowv_3e','default'=>'ef7777'					
				),array(
					'item'=>'.EVOWV_content ul.EVOWV_date_ranges li, ul.EVOWV_date_ranges',
					'css'=>'border-color:#$', 'var'=>'evowv_3g','default'=>'f7a6a6'					
				),array(
					'item'=>'.ajde_evcal_calendar .EVOWV_content .EVOWV_grid .evo_wv_day .evowv_daybox',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evowv_4c','default'=>'fbfbfb'),
						array('css'=>'color:#$', 'var'=>'evowv_4d','default'=>'949494'),
						array('css'=>'border-color:#$', 'var'=>'evowv_4e','default'=>'f1f1f1')
					)						
				),
				array(
					'item'=>'.evoWV_days.wk_style_1 .evowv_table, .evoWV_days.wk_style_1 .day_col, .evoWV_days .eventon_wv_days',
					'css'=>'border-color:#$', 'var'=>'evowv_4e','default'=>'f1f1f1'			
				),
				array(
					'item'=>'.evoWV.ajde_evcal_calendar .EVOWV_content .EVOWV_grid .evo_wv_day.today:before',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evowv_4f','default'=>'f79191'),
					)						
				)
			);
			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}

	// language settings additinos
		function evoWV_language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Weekly View'),
					array('label'=>'Week View', 'name'=>'evoWV_001', 'legend'=>''),
					array('label'=>'Click on Date to see events', 'var'=>1),
					array('label'=>'This Week', 'var'=>1),
					
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}
}
new evowv_admin();