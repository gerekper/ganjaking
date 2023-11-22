<?php
/**
 * Admin Section for YV
 * @version 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOYV_admin{
	public function __construct(){
		add_action('admin_init', array($this,'_admin_init'));
	}
	function _admin_init(){
		add_filter( 'eventon_appearance_add', array($this,'_appearance_settings') , 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this,'_dynamic_styles') , 1, 1);
		add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
		add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);

		add_filter( 'evo_addons_details_list', array( $this, 'eventon_addons_list' ), 10, 1 );
	}

	function eventon_addons_list($default){

		$default['eventon-yearly-view'] = array(
			'id'=> EVOYV()->id,
			'name'=> EVOYV()->name,
			'link'=>'http://www.myeventon.com/addons/yearly-view',
			'download'=>'http://www.myeventon.com/addons/yearly-view',
			'desc'=>'Display a complete year of events',
		);
		return $default;
	}

	// Font families
		function primary_font($str){
			$str .= ',.month_title, .evoyv_lightbox .evoyv_lb_header,
	.month_box .day_box .day_box_in,
	.month_box .day_box, 
	.day_names .day_box';
			return $str;
		}
		function secondary_font($str){
			return $str.',.evoyv_year_grid ul.evoyv_ttle_events li';
		}

	// appearance settings
		function _appearance_settings($array){	
			$new[] = array('id'=>'evoYV','type'=>'hiddensection_open','name'=>'YearlyView Styles','display'=>'none');
			$new[] = array('id'=>'evoYV','type'=>'fontation','name'=>'Date Box Color',
				'variations'=>array(
					array('id'=>'evoYV1', 'name'=>'Days with events (Background Color)','type'=>'color', 'default'=>'ffe69e'),
					array('id'=>'evoYV2', 'name'=>'Days with events (Text Color)','type'=>'color', 'default'=>'808080'),
					array('id'=>'evoYV2a', 'name'=>'Days with events (Hover Text Color)','type'=>'color', 'default'=>'808080'),
					array('id'=>'evoYV3', 'name'=>'Day Hover (Background Color)','type'=>'color', 'default'=>'f7f7f7'),
					array('id'=>'evoYV4', 'name'=>'Day Hover (Text Color)','type'=>'color', 'default'=>'808080'),					
				)
			);
			
			$new[] = array('id'=>'evoYV','type'=>'hiddensection_close');

			return array_merge($array, $new);
		}

	// styles
		function _dynamic_styles($_existen){
			$new= array(
									
				array(
					'item'=>'.evoyv_year_grid .month_box .day_box.he .day_box_color',
					'css'=>'background-color:#$', 'var'=>'evoYV1','default'=>'ffe69e'					
				),
				array(
					'item'=>'.evoyv_year_grid .month_box .day_box.he .day_box_in',
					'css'=>'color:#$', 'var'=>'evoYV2','default'=>'808080'					
				),
				array(
					'item'=>'.evoyv_year_grid .month_box .day_box.he:hover .day_box_in',
					'css'=>'color:#$', 'var'=>'evoYV2a','default'=>'808080'					
				),
				array(
					'item'=>'.evoyv_year_grid .month_box .day_box:hover .day_box_in',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoYV3','default'=>'f7f7f7'),
						array('css'=>'color:#$', 'var'=>'evoYV4','default'=>'808080')
					)						
				)
			);
			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
}
new EVOYV_admin();
