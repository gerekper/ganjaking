<?php
/**
 * Admin Section for DailyView
 * @version 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVODV_admin{
	public function __construct(){
		add_action('admin_init', array($this,'evoDV_admin_init'));
	}
	function evoDV_admin_init(){
		add_filter( 'eventon_appearance_add', array($this,'evoDV_appearance_settings') , 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this,'evoDV_dynamic_styles') , 1, 1);
		add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
		add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);
	}

	// Font families
		function primary_font($str){
			$str .= ',.evo_day span,
			.evodv_tooltip,
			.evodv_current_day p.evodv_daynum';
			return $str;
		}
		function secondary_font($str){
			return $str.',.evodv_current_day';
		}

	// appearance settings
		function evoDV_appearance_settings($array){	
			$new[] = array('id'=>'evoDV','type'=>'hiddensection_open','name'=>'DailyView Styles','display'=>'none');
			$new[] = array('id'=>'evoDV','type'=>'fontation',
				'name'=>'Date Number Text Colors',
				'variations'=>array(
					array('id'=>'evoDV_1', 'name'=>'Default','type'=>'color', 'default'=>'e8e8e8'),
					array('id'=>'evoDV_2', 'name'=>'Default (Hover)','type'=>'color', 'default'=>'d4d4d4'),
					array('id'=>'evoDV_3', 'name'=>'Days with events','type'=>'color', 'default'=>'d5c3ac'),
					array('id'=>'evoDV_4', 'name'=>'Days with events (Hover)','type'=>'color', 'default'=>'d5c3ac'),
					array('id'=>'evoDV_5', 'name'=>'Focus Day','type'=>'color', 'default'=>'ffffff')
				)
			);
			$new[] = array(
				'id'=>'evoDV','type'=>'fontation',
				'name'=>'Date Number Box Background Colors',
				'variations'=>array(
					array('id'=>'evoDV_2b', 'name'=>'Default (Hover)','type'=>'color', 'default'=>'e8e8e8'),
					array('id'=>'evoDV_5b', 'name'=>'Focus Day','type'=>'color', 'default'=>'636363'),
					array('id'=>'evoDV_7c', 'name'=>'Today (Color)','type'=>'color', 'default'=>'ec754b'),
				)
			);
			$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Current Date Box',
				'variations'=>array(
					array('id'=>'evoDV_8', 'name'=>'Background Color','type'=>'color', 'default'=>'fed582'),
					array('id'=>'evoDV_8b', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),							
				)
			);
			$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Day Stripe',
				'variations'=>array(
					array('id'=>'evodv_ds_1', 'name'=>'Background Color','type'=>'color', 'default'=>'f5f5f5'),
					array('id'=>'evoDV_9', 'name'=>'Arrow Color','type'=>'color', 'default'=>'c3bebe'),
					array('id'=>'evoDV_9a', 'name'=>'Arrow Background Color','type'=>'color', 'default'=>'e8e8e8'),
					array('id'=>'evoDV_9b', 'name'=>'Arrow Color (hover)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoDV_9c', 'name'=>'Arrow Background Color (hover)','type'=>'color', 'default'=>'e8e8e8'),
				)
			);
			$new[] = array('id'=>'evoDV','type'=>'hiddensection_close','name'=>'DailyView Styles');

			return array_merge($array, $new);
		}

	// styles
		function evoDV_dynamic_styles($_existen){
			$new= array(	
				array(
					'item'=>'.ajde_evcal_calendar .eventon_daily_list',
					'css'=>'background-color:#$', 'var'=>'evodv_ds_1','default'=>'f5f5f5'
				),
				array(
					'item'=>'.ajde_evcal_calendar.evoDV .eventon_daily_in .evo_day:hover',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoDV_2b','default'=>'e8e8e8'),
					)						
				),
				array(
					'item'=>'.ajde_evcal_calendar.evoDV .eventon_daily_in .evo_day.on_focus',
					'multicss'=>array(			
						array('css'=>'background-color:#$', 'var'=>'evoDV_5b','default'=>'636363'),	
						array('css'=>'color:#$', 'var'=>'evoDV_5','default'=>'ffffff'),
					)						
				),
				array(
					'item'=>'.ajde_evcal_calendar .evodv_current_day',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoDV_8','default'=>'fed582'),
						array('css'=>'color:#$', 'var'=>'evoDV_8b','default'=>'ffffff'),
					)	
				),
				array(
					'item'=>'.ajde_evcal_calendar .evodv_current_day p, .ajde_evcal_calendar .evodv_current_day p.evodv_daynum b',
					'css'=>'color:#$', 'var'=>'evoDV_8b','default'=>'ffffff'
				),
				array(
					'item'=>'.eventon_daily_in .evodv_action',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoDV_9a','default'=>'e8e8e8'),
						array('css'=>'color:#$', 'var'=>'evoDV_9','default'=>'c3bebe'),
					)	
				),array(
					'item'=>'.eventon_daily_in .evodv_action:hover',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoDV_9c','default'=>'e8e8e8'),
						array('css'=>'color:#$', 'var'=>'evoDV_9b','default'=>'ffffff'),
					)	
				),
				array(
					'item'=>'.eventon_daily_in .evo_day.today .evo_day_num',
					'css'=>'color:#$', 'var'=>'evoDV_7c','default'=>'ec754b'
				)
			);
			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
}
new EVODV_admin();
