<?php
/**
 * Admin
 * @version 0.1
 */
class evobo_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));		
	}

	function admin_init(){
		include_once('class-post_meta.php');
		include_once('class-admin_editor.php');

		add_action('eventon_admin_post_script',array($this, 'event_post_styles'));

		if(defined('DOING_AJAX')){	
			include_once( 'class-admin-ajax.php' );		
		}		

		// appearance
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 2);

		// eventon
		add_filter('evo_addons_details_list',array($this, 'addon_list'),10,1);
		//add_filter( 'evotix_settings_page_content', array( $this, 'settings_tix' ),10,1);

	}
	
	// styles and scripts
		function event_post_styles(){			
			wp_enqueue_style( 'evobo_admin_styles',EVOBO()->assets_path.'evobo_admin_styles.css', '', 
				EVOBO()->version);
			wp_enqueue_script( 'evobo_admin_post_script',EVOBO()->assets_path.'evobo_admin_script.js',
				array('jquery','jquery-ui-draggable','jquery-ui-sortable'), EVOBO()->version);
			wp_localize_script( 
				'evobo_admin_post_script', 
				'evobo_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonbo_nonce' )
				)
			);
		}

	// Appearance
		function appearance_settings($array){	
			extract( EVO()->elements->get_def_css() );

			$new[] = array('id'=>'evobo','type'=>'hiddensection_open',
				'name'=>__('Booking Styles','evobo'), 'display'=>'none');
			$new[] = array('id'=>'evobo','type'=>'fontation','name'=>__('Date circles ','evobo'),
				'variations'=>array(
					array('id'=>'evobo1', 'name'=>'Days with slots - Background color','type'=>'color', 'default'=>'82da95'),
					array('id'=>'evobo2', 'name'=>'Days with slots - Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evobo3', 'name'=>'Today date - Rim Color','type'=>'color', 'default'=>'ffafaf'),
					array('id'=>'evobo4', 'name'=>'Date hover - Background Color','type'=>'color', 'default'=>'f3f3f3'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>__('Time slots','evobo'),
				'variations'=>array(
					array('id'=>'evobo5', 'name'=>'Default background color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evobo5b', 'name'=>'Default text color','type'=>'color', 'default'=>'6b6b6b'),
					array('id'=>'evobo6a', 'name'=>'Selected background color','type'=>'color', 'default'=>'82da95'),
					array('id'=>'evobo6b', 'name'=>'Selected text color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>__('Today Button','evobo'),
				'variations'=>array(
					array('id'=>'evobo8a', 'name'=>'background color','type'=>'color', 'default'=>'cecece'),
					array('id'=>'evobo8b', 'name'=>'text color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>__('Booking Calendar','evobo'),
				'variations'=>array(
					array('id'=>'evobo9a', 'name'=>'Background color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evobo9b', 'name'=>'Month header text color','type'=>'color', 'default'=>'808080'),
					array('id'=>'evobo9c', 'name'=>'Day Letter text color','type'=>'color', 'default'=>'e4e4e4'),
					array('id'=>'evobo9d', 'name'=>'Date text color','type'=>'color', 'default'=> $evo_color_2),
				)
			);
			
			$new[] = array('id'=>'evors','type'=>'hiddensection_close');
			return array_merge($array, $new);
		}

		function dynamic_styles($_existen, $def_css){

			extract($def_css);

			$new= array(
				array(
					'item'=>'.evoTX_wc .evobo_calendar .evoGC .evoGC_week span.hasslots em',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evobo2',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evobo1',	'default'=>'82da95'),
					)
				),
				array('item'=>'.evoTX_wc .evoGC .evoGC_week .evoGC_date.today i','css'=>'border-color:#$', 'var'=>'evobo3','default'=>'ffafaf'),				
				array('item'=>'.evoTX_wc .evoGC .evoGC_week .evoGC_date:hover i','css'=>'background-color:#$', 'var'=>'evobo4','default'=>'f3f3f3'),
				array(
					'item'=>'.evoTX_wc .evobo_selections .evobo_selection_row.evobo_slot_selection span',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evobo5b',	'default'=>'6b6b6b'),
						array('css'=>'background-color:#$', 'var'=>'evobo5',	'default'=>'ffffff'),
					)
				),
				array(
					'item'=>'.evoTX_wc .evobo_selections .evobo_selection_row.evobo_slot_selection span.select',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evobo6b',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evobo6a','default'=>'82da95'),
					)
				),

				array(
					'item'=>'.evoTX_wc .evoGC .evoGC_today',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evobo8a',	'default'=>'cecece'),
						array('css'=>'color:#$', 'var'=>'evobo8b',	'default'=>'ffffff'),						
					)
				),
				array('item'=>'.evoTX_wc .evoGC','css'=>'color:#$', 'var'=>'evobo9a','default'=>'ffffff'),
				array('item'=>'.evoTX_wc .evoGC_monthyear','css'=>'color:#$', 'var'=>'evobo9b','default'=>'808080'),
				array('item'=>'.evoTX_wc .evoGC .evoGC_days','css'=>'color:#$', 'var'=>'evobo9c','default'=>'e4e4e4'),
				array('item'=>'.evoTX_wc .evoGC .evoGC_week span em','css'=>'color:#$', 'var'=>'evobo9d','default'=> $evo_color_2),
			);			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
	// settings
		function settings_tix($array){
			$array[] = array(
				'id'=>'evotxbo',
				'name'=>'Booking Settings For EventON Ticket',
				'tab_name'=>'Booking Settings',
				'icon'=>'calendar',
				'fields'=>array(
					array(
						'id'=>'evobo_display_style',
						'type'=>'dropdown',
						'name'=>'Time slot booking style on eventcard',
						'legend'=>'Select the layout style for how you want the booking to show on frontend.',
						'options'=>array(
							'def'=>'Default',
							'sty1'=>'Separated times',
						),
					)
			));
			return $array;
		}

	// eventon
		function addon_list($array){
			$array['eventon-bookings'] = array(
				'id'=>'EVOBO',
				'name'=>'Bookings',
				'link'=>'http://www.myeventon.com/addons/bookings',
				'download'=>'http://www.myeventon.com/addons/bookings',
				'desc'=>'Sell event tickets as time slot based bookings or appointments'
			);

			return $array;
		}

}