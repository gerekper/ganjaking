<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-li/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoli_admin{
	
	public $optSL;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
		//add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){
			// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'language_additions'), 10, 1);
			add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
			add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
		}
		// appearance
		function appearance_settings($array){
			
			$new[] = array('id'=>'EVOLI','type'=>'hiddensection_open','name'=>'Lists & Items', 'display'=>'none');
			$new[] = array('id'=>'EVOLI','type'=>'fontation','name'=>'Back to list button',
				'variations'=>array(
					array('id'=>'EVOLI_1', 'name'=>'Border Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'EVOLI_2', 'name'=>'Background Color','type'=>'color', 'default'=>'6b6b6b'),
				)
			);	
			$new[] = array('id'=>'EVOLI','type'=>'fontation','name'=>'Text Color',
				'variations'=>array(
					array('id'=>'EVOLI_3', 'name'=>'Item name color in list','type'=>'color', 'default'=>'6b6b6b'),
					array('id'=>'EVOLI_4', 'name'=>'Item other text color','type'=>'color', 'default'=>'8c8c8c'),
					array('id'=>'EVOLI_5', 'name'=>'Item section header text color','type'=>'color', 'default'=>'6b6b6b'),
				)
			);$new[] = array('id'=>'EVOLI','type'=>'fontation','name'=>'Individual Item',
				'variations'=>array(
					array('id'=>'EVOLI_6', 'name'=>'Item background color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'EVOLI_7', 'name'=>'Item background color (hover)','type'=>'color', 'default'=>'fffbf1'),
					array('id'=>'EVOLI_8', 'name'=>'Arrow color','type'=>'color', 'default'=>'141412'),
				)
			);	
			$new[] = array('id'=>'EVOLI','type'=>'hiddensection_close');
			return array_merge($array, $new);
		}

		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.EVOLI_back_btn',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'EVOLI_1',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'EVOLI_2',	'default'=>'6b6b6b'),
					)
				),
				array('item'=>'.EVOLI ul li .inner h2','css'=>'color:#$', 'var'=>'EVOLI_3','default'=>'6b6b6b'),
				array('item'=>'.EVOLI ul li .inner p','css'=>'color:#$', 'var'=>'EVOLI_4','default'=>'8c8c8c'),
				array('item'=>'.EVOLI_section','css'=>'color:#$', 'var'=>'EVOLI_5','default'=>'6b6b6b'),
				array('item'=>'.EVOLI ul li .inner','css'=>'background-color:#$', 'var'=>'EVOLI_6','default'=>'ffffff'),
				array('item'=>'.EVOLI ul li .inner:hover','css'=>'background-color:#$', 'var'=>'EVOLI_7','default'=>'fffbf1'),
				array('item'=>'.EVOLI ul li .inner:after','css'=>'color:#$', 'var'=>'EVOLI_8','default'=>'141412'),
			);
			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Lists & Items'),
					array('label'=>'Back to List','name'=>'EVOLIL_001'),				
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	// other hooks
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Lists & Items', __('Lists & Items','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_li', '' );
		}
	
	
}

new evoli_admin();