<?php
/**
 * Appearance settings for eventon
 * @version 2.4.8
 */

class evoadmin_set_appearance{
	function __construct($evcal_opt)	{
		$this->evcal_opt = $evcal_opt;
	}
	function get(){
		return apply_filters('eventon_appearance_add', 
			array(
				array('id'=>'evo_notice_1','type'=>'notice','name'=>sprintf(__('Once you make changes to appearance make sure to clear browser and website cache to see results. <br/>Can not find appearance? <a href="%s" target="_blank">See how you can add custom styles to change additional appearances</a>','eventon'),'http://www.myeventon.com/documentation/change-css-calendar/') )
				
				,array('id'=>'evoapp_code_1', 'type'=>'customcode','code'=>$this->appearance_theme_selector(), )
				,array('id'=>'fc_mcolor','type'=>'multicolor','name'=>__('Multiple colors','eventon'),
					'variations'=>array(
						array('id'=>'evcal_hexcode', 'default'=>'4bb5d8', 'name'=>__('Primary Calendar Color','eventon')),
						array('id'=>'evcal_header1_fc', 'default'=>'737373', 'name'=>'Header Month/Year text color'),
						array('id'=>'evcal__fc2', 'default'=>'737373', 'name'=>'Calendar Date color'),
					)
				),
				array('id'=>'evcal_font_fam','type'=>'text','name'=>__('Primary Calendar Font family <i>(Note: type the name of the font that is supported in your website. eg. Arial)</i>','eventon')
					,'default'=>'roboto, oswald, arial narrow'
				),
					

				array('id'=>'evcal_font_fam_secondary','type'=>'text','name'=>__('Secondary Calendar Font family <i>(Note: type the name of the font that is supported in your website. eg. Arial)</i>','eventon')
					,'default'=>'open sans, arial',
					'legend' => 'Secondary font family is used in subtitle text through out the calendar.'
				),
				array('id'=>'evcal_arrow_hide','type'=>'yesno','name'=>__('Hide month navigation arrows','eventon'), 'legend'=>'You can also hide individual calendar navigation arrows via shortcode variable hide_arrows="yes"'),
				array('id'=>'evo_arrow_right','type'=>'yesno','name'=>__('Align month navigation arrows to rightside of the calendar','eventon'),'legend'=>'This will align the month navigation arrows to the right side border of the calendar as oppose to next to month title text.'),
				
				// featured events
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Featured Events','eventon')),
					array('id'=>'evo_fte_override','type'=>'yesno','name'=>__('Override featured event color','eventon'),'legend'=>__('This will override the event color you chose for featured event with a different color.','eventon'),'afterstatement'=>'evo_fte_override'),
					array('id'=>'evo_fte_override','type'=>'begin_afterstatement'),
						array('id'=>'evcal__ftec','type'=>'color','name'=>__('Featured event left bar color','eventon'), 'default'=>'ca594a'),
					array('id'=>'evcal_ftovrr','type'=>'end_afterstatement'),

				// Calendar Header
				array('id'=>'evcal_fcx','type'=>'hiddensection_open','name'=>__('Calendar Header','eventon'), 'display'=>'none'),
					array('id'=>'fs_sort_options','type'=>'fontation','name'=>__('Sort Options Text','eventon'),
						'variations'=>array(
							array('id'=>'evcal__sot', 'name'=>'Default State', 'type'=>'color', 'default'=>'B8B8B8'),
							array('id'=>'evcal__sotH', 'name'=>'Hover State', 'type'=>'color', 'default'=>'d8d8d8'),
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jump Months Trigger Button','eventon'),
						'variations'=>array(
							array('id'=>'evcal__jm001', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__jm002', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ADADAD'),
							array('id'=>'evcal__jm001H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__jm002H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'d3d3d3'),						
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons','eventon'),
						'variations'=>array(
							array('id'=>'evcal__jm003', 'name'=>'Text Color', 'type'=>'color', 'default'=>'a0a09f'),
							array('id'=>'evcal__jm004', 'name'=>'Background Color', 'type'=>'color', 'default'=>'f5f5f5'),
							array('id'=>'evcal__jm003H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'a0a09f'),
							array('id'=>'evcal__jm004H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'e6e6e6'),							
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons: Current','eventon'),
						'variations'=>array(
							array('id'=>'evcal__jm006', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__jm007', 'name'=>'Background Color', 'type'=>'color', 'default'=>'CFCFCF'),
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Jumper - Month/Year Buttons: Active','eventon'),
						'variations'=>array(
							array('id'=>'evcal__jm008', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__jm009', 'name'=>'Background Color', 'type'=>'color', 'default'=>'f79191'),
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Current month Button','eventon'),
						'variations'=>array(
							array('id'=>'evcal__thm001', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__thm002', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ADADAD'),
							array('id'=>'evcal__thm001H', 'name'=>'Text Color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__thm002H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'d3d3d3'),						
						)
					),array('id'=>'fs_calhead','type'=>'fontation','name'=>__('Arrow Circle','eventon'),
						'variations'=>array(
							array('id'=>'evcal__jm010', 'name'=>'Line Color', 'type'=>'color', 'default'=>'737373'),
							array('id'=>'evcal__jm011', 'name'=>'Background Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__jm010H', 'name'=>'Line Color (Hover)', 'type'=>'color', 'default'=>'e2e2e2'),
							array('id'=>'evcal__jm011H', 'name'=>'Background Color (Hover)', 'type'=>'color', 'default'=>'ededed'),
							array('id'=>'evcal__jm01A', 'name'=>'The arrow color', 'type'=>'color', 'default'=>'737373'),
							array('id'=>'evcal__jm01AH', 'name'=>'The arrow color (Hover)', 'type'=>'color', 'default'=>'ffffff'),
						)
					),array('id'=>'fs_loader','type'=>'fontation','name'=>__('Calendar Loader','eventon'),
					'variations'=>array(
							array('id'=>'evcal_loader_001', 'name'=>'Bar Color', 'type'=>'color', 'default'=>'efefef'),
							array('id'=>'evcal_loader_002', 'name'=>'Moving Bar Color', 'type'=>'color', 'default'=>'f5b87a'),
						)
					),		
				array('id'=>'evcal_ftovrr','type'=>'hiddensection_close'),

				// GENERAL CAlendar
				array('id'=>'evcal_fcx','type'=>'hiddensection_open','name'=>__('General Calendar','eventon'), 'display'=>'none'),
					array('id'=>'evose','type'=>'fontation','name'=>__('Social Media Icons','eventon'),
						'variations'=>array(
						array('id'=>'evose_1', 'name'=>__('Icon Color','eventon'),'type'=>'color', 'default'=>'888686'),			
						array('id'=>'evose_2', 'name'=>__('Icon Color (:Hover)','eventon'),'type'=>'color', 'default'=>'ffffff'),
						array('id'=>'evose_4', 'name'=>__('Icon Background Color (:Hover)','eventon'),'type'=>'color', 'default'=>'9e9e9e'),
						array('id'=>'evose_5', 'name'=>__('Icon right border Color','eventon'),'type'=>'color', 'default'=>'cdcdcd')
						,				
					)),
					array('id'=>'evors','type'=>'fontation','name'=>__('Search Field','eventon'),
							'variations'=>array(
							array('id'=>'evosr_1', 'name'=>__('Border Color','eventon'),'type'=>'color', 'default'=>'EDEDED'),
							array('id'=>'evosr_2', 'name'=>__('Background Color','eventon'),'type'=>'color', 'default'=>'F2F2F2'),
							array('id'=>'evosr_3', 'name'=>__('Border Color (Hover)','eventon'),'type'=>'color', 'default'=>'c5c5c5')	
						)
					),
					array('id'=>'evors','type'=>'fontation','name'=>__('Search Icon','eventon'),
						'variations'=>array(
							array('id'=>'evosr_4', 'name'=>__('Color','eventon'),'type'=>'color', 'default'=>'3d3d3d'),
							array('id'=>'evosr_5', 'name'=>__('Hover Color','eventon'),'type'=>'color', 'default'=>'bbbbbb'),	
						)
					),
					array('id'=>'evors','type'=>'fontation','name'=>__('Search Effect','eventon'),
						'variations'=>array(
							array('id'=>'evosr_6', 'name'=>__('Background Color','eventon'),'type'=>'color', 'default'=>'f9d789'),
							array('id'=>'evosr_7', 'name'=>__('Text Color','eventon'),'type'=>'color', 'default'=>'14141E'),
						)
					),
					array('id'=>'evors','type'=>'fontation','name'=>__('Events Found Data','eventon'),
						'variations'=>array(
							array('id'=>'evosr_8', 'name'=>__('Caption Color','eventon'),'type'=>'color', 'default'=>'14141E'),
							array('id'=>'evosr_9', 'name'=>__('Event Count Background Color','eventon'),'type'=>'color', 'default'=>'d2d2d2'),	
							array('id'=>'evosr_10', 'name'=>__('Event Count Text Color','eventon'),'type'=>'color', 'default'=>'ffffff'),	
						)
					),
					array('id'=>'evo','type'=>'fontation','name'=>__('Show more events bar','eventon'),
						'variations'=>array(
							array('id'=>'evo_001a', 'name'=>__('Background Color','eventon'),'type'=>'color', 'default'=>'b4b4b4'),
							array('id'=>'evo_001b', 'name'=>__('Text Color','eventon'),'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'evo','type'=>'fontation','name'=>__('Timezone Section','eventon'),
						'variations'=>array(
							array('id'=>'evo_tzoa', 'name'=>__('Timezone Background Color','eventon'),'type'=>'color', 'default'=>'2eb4dc'),
							array('id'=>'evo_tzob', 'name'=>__('Timezone hover/set Text Color','eventon'),'type'=>'color', 'default'=>'ffffff'),
						)
					),

					array('id'=>'evo','type'=>'fontation','name'=>__('Single Event Repeat Header','eventon'),
						'variations'=>array(
							array('id'=>'evo_rep_1', 'name'=>__('Title Section Background Color','eventon'),'type'=>'color', 'default'=>'fed584'),
							array('id'=>'evo_rep_1c', 'name'=>__('Title Section Text Color','eventon'),'type'=>'color', 'default'=>'808080'),
							array('id'=>'evo_rep_2', 'name'=>__('Nav Section Background Color','eventon'),'type'=>'color', 'default'=>'ffe3ad'),
							array('id'=>'evo_rep_2c', 'name'=>__('Nav Section Text Color','eventon'),'type'=>'color', 'default'=>'808080'),
							
						)
					),
					array('id'=>'evcal__evcbrb','type'=>'color','name'=>__('General Calendar/Event Border Color','eventon'), 'default'=>'d5d5d5'),

				array('id'=>'evcal_ftovrr','type'=>'hiddensection_close'),


				// event top
				array('id'=>'evcal_fcx','type'=>'hiddensection_open','name'=>__('EventTop Styles','eventon'), 'display'=>'none'),

					array('id'=>'evcal__fc3','type'=>'color','name'=>__('Event Title font color','eventon'), 'default'=>'6B6B6B'),					

					array('id'=>'evcal__fc3st','type'=>'color','name'=>__('Event Sub Title font color','eventon'), 'default'=>'6B6B6B'),
					array('id'=>'evcal__fc6','type'=>'color','name'=>__('Text under event title (on EventTop. Eg. Time, location etc.)','eventon'),'default'=>'8c8c8c'),
					array('id'=>'evcal__fc7','type'=>'color','name'=>__('Category title color (eg. Event Type)','eventon'),'default'=>'c8c8c8'),					

					array('id'=>'fs_fonti','type'=>'fontation','name'=>__('Background Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal__bgc4', 'name'=>'Default State', 'type'=>'color', 'default'=>'f1f1f1'),
							array('id'=>'evcal__bgc4h', 'name'=>'Hover State', 'type'=>'color', 'default'=>'fbfbfb'),
							array('id'=>'evcal__bgc5', 'name'=>'Featured Event - Default State', 'type'=>'color', 'default'=>'fff6e2'),
							array('id'=>'evcal__bgc5h', 'name'=>'Featured Event - Hover State', 'type'=>'color', 'default'=>'ffecc5'),
						)
					),
					
					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('General EventTop Tags','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_tag_1', 'name'=>'Background color', 'type'=>'color', 'default'=>'F79191'),
							array('id'=>'fs_eventtop_tag_2', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_cancel_event','type'=>'fontation','name'=>__('Cancelled Events Tag','eventon'),
						'variations'=>array(
							array('id'=>'evcal__cancel_event_1', 'name'=>'Background color', 'type'=>'color', 'default'=>'F79191'),
							array('id'=>'evcal__cancel_event_2', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal__cancel_event_3x', 'name'=>'EventTop Background Color', 'type'=>'color', 'default'=>'333333'),
							array('id'=>'evcal__cancel_event_4x', 'name'=>'EventTop Cancelled text color', 'type'=>'color', 'default'=>'464646'),
						)
					),
					
					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('Postponed Tag','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_est_1a', 'name'=>'Background color', 'type'=>'color', 'default'=>'e3784b'),
							array('id'=>'fs_eventtop_est_1b', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('Moved Online Tag','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_est_2a', 'name'=>'Background color', 'type'=>'color', 'default'=>'6edccd'),
							array('id'=>'fs_eventtop_est_2b', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('Rescheduled Tag','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_est_3a', 'name'=>'Background color', 'type'=>'color', 'default'=>'67ef78'),
							array('id'=>'fs_eventtop_est_3b', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),

					array('id'=>'fs_eventtop_tag','type'=>'fontation','name'=>__('Featured Events Tag','eventon'),
						'variations'=>array(
							array('id'=>'fs_eventtop_featured_1', 'name'=>'Background color', 'type'=>'color', 'default'=>'ffcb55'),
							array('id'=>'fs_eventtop_featured_2', 'name'=>'Font Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_eventtop_cmd','type'=>'fontation','name'=>__('Custom Field Buttons','eventon'),
						'variations'=>array(
							array('id'=>'evoeventtop_cmd_btn', 'name'=>'Background color', 'type'=>'color', 'default'=>'237dbd'),
							array('id'=>'evoeventtop_cmd_btnA', 'name'=>'Text Color', 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_eventtop_live','type'=>'fontation','name'=>__('Live Event Progress','eventon'),
						'variations'=>array(
							array('id'=>'evoeventtop_live1', 'name'=>'Bar color', 'type'=>'color', 'default'=>'f79191'),
							array('id'=>'evoeventtop_live2', 'name'=>'Bar color - Completed', 'type'=>'color', 'default'=>'9a9a9a'),
						)
					),
					array('id'=>'evcal__colorful_text','type'=>'color','name'=>__('Colorful EventTop General Text Color','eventon'), 'default'=>'ffffff'),
					
				array('id'=>'evcal_fcx','type'=>'hiddensection_close',),
				

				// eventCard Styles
				array('id'=>'evcal_fcxx','type'=>'hiddensection_open','name'=>__('EventCard Styles','eventon'), 'display'=>'none'),
				array('id'=>'fs_fonti1','type'=>'fontation','name'=> __('Section Title Text','eventon'),
					'variations'=>array(
						array('id'=>'evcal__fc4', 'type'=>'color', 'default'=>'6B6B6B'),
						array('id'=>'evcal_fs_001', 'type'=>'font_size', 'default'=>'18px'),
					)
				),
				array('id'=>'evcal__fc5','type'=>'color','name'=>__('General Font Color','eventon'), 'default'=>'656565'),
				array('id'=>'evcal__bc1','type'=>'color','name'=>__('Event Card Background Color','eventon'), 'default'=>'fdfdfd', 'rgbid'=>'evcal__bc1_rgb'),				
				array('id'=>'evcal__bc1in','type'=>'color','name'=>__('Event Card Inner Section Background Color','eventon'), 'default'=>'f3f3f3'),				
				

					// get direction fiels
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Get Directions Field','eventon')),
					array('id'=>'fs_fonti3','type'=>'fontation','name'=>__('Get Directions','eventon'),
						'variations'=>array(
							array('id'=>'evcal_getdir_001', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal_getdir_002', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'888888'),
							array('id'=>'evcal_getdir_003', 'name'=>__('Button Icon Color','eventon'), 'type'=>'color', 'default'=>'858585'),
						)
					),			

					// Buttons
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Buttons','eventon')),
					
					
					array('id'=>'evo_ec_link','type'=>'color','name'=>__('Default event details link text color','eventon'), 'default'=>'ef4040'),

					array('id'=>'fs_fonti3','type'=>'fontation','name'=>__('Primary Button Colors','eventon'),
						'variations'=>array(
							array('id'=>'evcal_gen_btn_bgc', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'237ebd'),							
							array('id'=>'evcal_gen_btn_fc', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evcal_gen_btn_bgcx', 'name'=>__('Background Hover Color','eventon'), 'type'=>'color', 'default'=>'237ebd'),
							array('id'=>'evcal_gen_btn_fcx', 'name'=>__('Hover Text Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_fonti3','type'=>'fontation','name'=>__('Secondary Button Colors','eventon'),
						'variations'=>array(
							array('id'=>'evo_btn_2nd_bgc', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'d2d2d2'),							
							array('id'=>'evo_btn_2nd_c', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evo_btn_2nd_bgch', 'name'=>__('Background Hover Color','eventon'), 'type'=>'color', 'default'=>'bebebe'),
							array('id'=>'evo_btn_2nd_ch', 'name'=>__('Hover Text Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
						)
					),
					array('id'=>'fs_fonti5','type'=>'fontation','name'=>__('Close Eventcard Button Color','eventon'),
						'variations'=>array(
							array('id'=>'evcal_closebtn', 'name'=>__('Default State','eventon'), 'type'=>'color', 'default'=>'f7f7f7'),
							array('id'=>'evcal_closebtnx', 'name'=>__('Hover State','eventon'), 'type'=>'color', 'default'=>'f1f1f1'),
						)
					),
					array('id'=>'fs_fonti5','type'=>'fontation','name'=>__('Lightbox Close Button Color','eventon'),
						'variations'=>array(
							array('id'=>'evo_color_lb_1', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'000000'),
							array('id'=>'evo_color_lb_2', 'name'=>__('X Color','eventon'), 'type'=>'color', 'default'=>'666666'),
							array('id'=>'evo_color_lb_3', 'name'=>__('Background Color - HOVER','eventon'), 'type'=>'color', 'default'=>'cfcfcf'),
							array('id'=>'evo_color_lb_4', 'name'=>__('X Color - HOVER','eventon'), 'type'=>'color', 'default'=>'666666'),
						)
					),
					array('id'=>'fs_fonti6','type'=>'fontation','name'=>__('Repeating Instances Button','eventon'),
						'variations'=>array(
							array('id'=>'evcal_repinst_btn', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'dedede'),
							array('id'=>'evcal_repinst_btn_txt', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'656565'),
						)
					),
					


				// health guidelines
					array('id'=>'evcal_fcx','type'=>'subheader','name'=>__('Health Guidelines Field','eventon')),
						array('id'=>'fs_fonti6','type'=>'fontation','name'=>__('Health Box','eventon'),
						'variations'=>array(
								array('id'=>'evo_health_1', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'ececec'),
								array('id'=>'evo_health_2', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'8d8d8d'),
								array('id'=>'evo_health_3', 'name'=>__('Icon Color','eventon'), 'type'=>'color', 'default'=>'8d8d8d'),
							)
						),
						array('id'=>'evo_health_4','type'=>'color','name'=>__('Other Health Guidelines Box Color','eventon'), 'default'=>'e8e8e8'),

				
				array('id'=>'evcal_fcx','type'=>'hiddensection_close',),

					
				// Live now calendar
				array('id'=>'evcal_livenow','type'=>'hiddensection_open','name'=>__('Live Now Calendar View Styles','eventon'), 'display'=>'none'),

					array('id'=>'evo_live1b','type'=>'color','name'=>__('Happening Now Section Title Text Color','eventon'),'default'=>'8e8e8e'),
					array('id'=>'evo_live2','type'=>'color','name'=>__('Coming Up Section Background Color','eventon'), 'default'=>'ececec'),
					array('id'=>'evo_live3','type'=>'color','name'=>__('Coming Up Text Color','eventon'), 'default'=>'8e8e8e'),			
					array('id'=>'evo_live4','type'=>'fontation','name'=>__('Coming Up Counter','eventon'),
						'variations'=>array(
							array('id'=>'evo_live4a', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'evo_live4b', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'a5a5a5'),
						)
					),	
					array('id'=>'evo_live5','type'=>'fontation','name'=>__('No Current Events Section','eventon'),
						'variations'=>array(
							array('id'=>'evo_live5a', 'name'=>__('Text Color','eventon'), 'type'=>'color', 'default'=>'888888'),
							array('id'=>'evo_live5b', 'name'=>__('Background Color','eventon'), 'type'=>'color', 'default'=>'d6f5d2'),
						)
					),	

				array('id'=>'evcal_livenow','type'=>'hiddensection_close'),

			)
		);
	}

	function appearance_theme_selector(){			
		ob_start();

			echo  '<h4 class="acus_header">'.__('Calendar Themes','eventon').'</h4>
			<input id="evo_cal_theme" name="evo_cal_theme" value="'.( (!empty($this->evcal_opt[1]['evo_cal_theme']))? $this->evcal_opt[1]['evo_cal_theme']:null).'" type="hidden"/>
			<div id="evo_theme_selection">';

			// scan for themes
			$dir = AJDE_EVCAL_PATH.'/themes/';				
			$a = scandir($dir);
			
			$themes =$the = array();
			foreach($a as $file){
				if($file!= '.' && $file!= '..'){
					$base = basename($file,'.php');
					$themes[$base] = $file;
					if(file_exists($dir.$file)){
						include_once($dir.$file);
						$the[] = array('name'=>$base, 'content'=>$theme);
					}
				}
			}


				echo "<p id='evo_themejson' style='display:none'>".json_encode($the)."</p>";
				$evo_theme_current =  !empty($this->evcal_opt[1]['evo_theme_current'])? $this->evcal_opt[1]['evo_theme_current']: 'default';

			?>
				<p class='evo_theme_selection'><?php _e('Current Theme:','eventon');?> <b><select name='evo_theme_current'>
					<option value='default'><?php _e('Default','eventon');?></option>
					<?php
						if(!empty($themes)){
							foreach($themes as $base=>$theme){
								echo "<option value='{$base}' ". ($base==$evo_theme_current? "selected='selected'":null).">".$base.'</option>';
							}
						}
					?>
				</select></b>
					<span class='evo_theme'>
						<span name='evcal__fc2' style='background-color:#<?php echo $this->colr('evcal__fc2','737373' );?>' data-default='737373'></span>
						<span name='evcal_header1_fc' style='background-color:#<?php echo $this->colr('evcal_header1_fc','C6C6C6' );?>' data-default='C6C6C6'></span>
						<span name='evcal__bgc4' style='background-color:#<?php echo $this->colr('evcal__bgc4','ffffff' );?>' data-default='fafafa'></span>
						<span name='evcal__fc3' style='background-color:#<?php echo $this->colr('evcal__fc3','6B6B6B' );?>' data-default='6B6B6B'></span>
						<span name='evcal__jm010' style='background-color:#<?php echo $this->colr('evcal__jm010','e2e2e2' );?>' data-default='e2e2e2'></span>
						<span name='evcal__bc1' style='background-color:#<?php echo $this->colr('evcal__bc1','fdfdfd' );?>' data-default='fdfdfd'></span>
					</span>
				</p>				
				
				<p style='clear:both'><i><strong><?php _e('NOTE:','eventon');?></strong> <?php _e('After changing theme make sure to click "save changed"','eventon');?></i></p>
	
			<?php

			echo '</div>';

		return ob_get_clean();
	}
	// get options
		private function colr($var, $def){
			return (!empty($this->evcal_opt[1][$var]))? $this->evcal_opt[1][$var]: $def;
		}
}