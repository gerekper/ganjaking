<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-slider/classes
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosl_admin{
	
	public $optSL;
	function __construct(){
		add_action('admin_init', array($this,'_admin_init'));

	}
	function _admin_init(){
		add_filter( 'eventon_appearance_add', array($this,'_appearance_settings') , 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this,'_dynamic_styles') , 1, 1);
	}
	function _appearance_settings($array){
		$new[] = array('id'=>'evosl','type'=>'hiddensection_open','name'=>'Slider Styles','display'=>'none');
		$new[] = array('id'=>'evosl','type'=>'fontation','name'=>'Circle arrow nav button',
			'variations'=>array(
				array('id'=>'evosl1', 'name'=>'Background Color','type'=>'color', 'default'=>'ffffff'),
				array('id'=>'evosl1h', 'name'=>'Background Color (on Hover)','type'=>'color', 'default'=>'ffffff'),
				array('id'=>'evosl2', 'name'=>'Arrow Color','type'=>'color', 'default'=>'222222'),
				array('id'=>'evosl3', 'name'=>'Border Color','type'=>'color', 'default'=>'222222'),					
			)
		);
		$new[] = array('id'=>'evosl','type'=>'fontation','name'=>'Arrow nav bar button',
			'variations'=>array(
				array('id'=>'evosl7', 'name'=>'Background Color','type'=>'color', 'default'=>'f1f1f1'),
				array('id'=>'evosl8', 'name'=>'Arrow Color','type'=>'color', 'default'=>'808080'),				
			)
		);
		$new[] = array('id'=>'evosl','type'=>'fontation','name'=>'Nav Dots',
			'variations'=>array(
				array('id'=>'evosl4', 'name'=>'Outer Ring Color','type'=>'color', 'default'=>'a9a9a9'),
				array('id'=>'evosl5', 'name'=>'Dot Color','type'=>'color', 'default'=>'e1e1e1'),				
				array('id'=>'evosl5h', 'name'=>'Dot Color (on Hover)','type'=>'color', 'default'=>'ababab'),
			)
		);
		
		$new[] = array('id'=>'evoYV','type'=>'hiddensection_close');

		return array_merge($array, $new);
	}
	function _dynamic_styles($_existen){
		$new= array(
								
			array(
				'item'=>'.evoslider.cs_tb .evo_slider_outter .evoslider_nav, .evoslider.cs_lr .evo_slider_outter .evoslider_nav',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosl7','default'=>'f1f1f1'),
					array('css'=>'color:#$', 'var'=>'evosl8','default'=>'808080')
				)						
			),
			array(
				'item'=>'.evoslider .evoslider_dots span',
				'css'=>'background-color:#$', 'var'=>'evosl5','default'=>'e1e1e1'					
			),array(
				'item'=>'.evoslider .evoslider_dots span:hover',
				'css'=>'background-color:#$', 'var'=>'evosl5h','default'=>'ababab'					
			),
			array(
				'item'=>'.evoslider .evoslider_dots span.f em',
				'css'=>'border-color:#$', 'var'=>'evosl4','default'=>'a9a9a9'					
			),array(
				'item'=>'.evoslider .evosl_footer_outter .nav:hover',
				'css'=>'background-color:#$', 'var'=>'evosl1h','default'=>'ffffff'					
			),
			array(
				'item'=>'.evoslider .evosl_footer_outter .nav',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosl1','default'=>'ffffff'),
					array('css'=>'border-color:#$', 'var'=>'evosl3','default'=>'222222'),
					array('css'=>'color:#$', 'var'=>'evosl2','default'=>'222222')
				)						
			)
		);
		

		return (is_array($_existen))? array_merge($_existen, $new): $_existen;
	}
	
}

new evoSL_admin();