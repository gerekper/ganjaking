<?php
/**
 * Event Slider front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-slider/classes
 * @version     2.0
 */
class evosl_front{
	
	function __construct(){
		$this->evopt1 = get_option('evcal_options_evcal_1');

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	

	}

	// STYLES: for photos 
		public function register_styles_scripts(){

			if(is_admin()) return false;
					
			wp_register_style( 'evosl_styles',EVOSL()->assets_path.'evosl_styles.css', array(), EVOSL()->version);
			
			//wp_register_script('mainscript',EVOSL()->assets_path.'evoslider.js', array('jquery'), EVOSL()->version, true );
			wp_register_script('evosl_script',EVOSL()->assets_path.'evosl_script.js', array('jquery'), EVOSL()->version, true );
			
			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){
			//wp_enqueue_script('mainscript');		
			wp_enqueue_script('evosl_script');		
		}
		function print_styles(){	wp_enqueue_style( 'evosl_styles');	}

		function remove_event_padding_style($_eventInAttr){
			$_eventInAttr['style'][] = 'padding-top: 0px;';
			return $_eventInAttr;
		}

	// Generate Slider HTML content
		function get_slider_content($args){	
			$args['show_et_ft_img']='yes';
			//$args['ux_val']='3';

			// slides visible override
				if( $args['slider_type'] =='def') $args['slides_visible'] = 1;
			
			$this->only__actions();
			$content = '';

			// Old shortcode values compatibility
				if($args['slider_type'] == 'imgab'){
					$args['slider_type'] = 'def'; $args['slide_style'] = 'imgtop';
				}
				if($args['slider_type'] == 'multiimgab'){
					$args['slider_type'] = 'multi'; $args['slide_style'] = 'imgtop';
				}
				if($args['slider_type'] == 'multiimgab') $args['slider_type'] = 'mini'; 
				if($args['slider_type'] == 'minicar') $args['slider_type'] = 'mini'; 

			// Full event featured image using tiles for slide style
				if( $args['slider_type'] == 'multi' && $args['slide_style'] == 'imgleft') 
					$args['slide_style'] = 'imgtop';
				if( $args['slider_type'] == 'mini') $args['slide_style'] = 'def';

				if($args['slide_style'] == 'imgbg' || $args['slide_style'] == 'imgtop' || $args['slide_style'] == 'imgleft'){
					//add_filter('evo_cal_eventtop_in_attrs', array($this, 'remove_event_padding_style'), 10, 1);
					$args['tiles'] = 'yes';
					$args['tile_style'] = '0';
					$args['tile_bg'] = '1';
					if( $args['slide_style'] == 'imgtop' || $args['slide_style'] == 'imgleft' ){
						$args['tile_style'] = '1';
					}
				}
				
			// CUT OFF time calculation
				//fixed time list
				if(!empty($args['pec']) && $args['pec']=='ft'){
					$__D = (!empty($args['fixed_date']))? $args['fixed_date']:date("j", current_time('timestamp'));
					$__M = (!empty($args['fixed_month']))? $args['fixed_month']:date("m", current_time('timestamp'));
					$__Y = (!empty($args['fixed_year']))? $args['fixed_year']:date("Y", current_time('timestamp'));

					$current_timestamp = mktime(0,0,0,$__M,$__D,$__Y);

				// current date cd
				}else if(!empty($args['pec']) && $args['pec']=='cd'){
					$current_timestamp = strtotime( date("m/j/Y", current_time('timestamp')) );
				}else{// current time ct
					$current_timestamp = current_time('timestamp');
				}
				// reset arguments
				$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';
			
			// restrained time unix
				$number_of_months = (!empty($args['number_of_months']))? (int)($args['number_of_months']):0;
				$month_dif = ($args['el_type']=='ue')? '+':'-';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				if($args['el_type']=='ue'){
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
								
				}else{// past events list

					if(!empty($args['event_order']))
						$args['event_order']='DESC';

					$args['hide_past']='no';
					
					$__focus_start_date_range =  mktime(0,0,0,($restrain_monthN),1, ($restrain_year));
					$__focus_end_date_range = $current_timestamp;
				}
			
			
			// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
			);

			// Alter user interaction
				if($args['ux_val']== '1' || $args['ux_val'] == '2' || empty($args['ux_val'])) $args['ux_val'] = 3;		

			//print_r($args);
			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;
			
			
			// PROCESS variables
			$args__ =  EVO()->calendar->process_arguments($args);
			$this->shortcode_args=$args__;
			
			
			// Content for the slider
			$content .= $this->html_header($args);

			$content .= EVO()->evo_generator->_generate_events( 'html');

			$content .= $this->html_footer();

			$this->remove_only__actions();

			remove_filter('evo_cal_eventtop_in_attrs', array($this, 'remove_event_padding_style'), 10, 1);
			
			return  $content;	
		}

		// Header content for the slider
			function html_header($args){
				global $eventon;

				$dataString = '';

				// need compatibility for
					/*
					imgab, multiimgab, multimini - slider_type
					*/

				// class names for slider container
				$classNames = array();
				if(!empty($this->evopt1['evo_rtl']) && $this->evopt1['evo_rtl']=='yes')	
					$classNames[] = 'rtlslider';
				if(!empty($args['slider_type']) ) 	$classNames[] = $args['slider_type'].'Slider';

				if(isset($args['control_style'])) $classNames[] = 'cs_'.$args['control_style'];
				if(isset($args['slide_style'])) $classNames[] = 'ss_'.$args['slide_style'];
				
				array_filter($classNames);
				if(is_array($classNames)) $class_names = implode(' ', $classNames);

				$cal_id = (empty($cal_id))? rand(100,900): $cal_id;
				$cal_id = str_replace(' ', '-', $cal_id);

				$out = '';
				$out .= '<div id="evcal_calendar_'.$cal_id.'" class="ajde_evcal_calendar evoslider evosliderbox '.$class_names.' sltac">';
				$out .= '<div class="evo_slider_outter" >
					<div class="evo_slider_slide_out">
	                <div class="eventon_events_list" style="display:none">';
	            return $out;
			}

			function html_footer(){
				$out = '';
				$out .= '</div>';
				$out .= '</div>';				
				$out .= '</div>';
				$out .= '<div class="evosl_footer_outter"><div class="evosl_footer"></div></div>';

				ob_start();
				EVO()->calendar->body->print_evo_cal_data();
				$out .= ob_get_clean();
				$out .= '</div>';
				return $out;
			}


	// SUPPORT functions
		// ONLY for el calendar actions 
		public function only__actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only__actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoSL';
			return $name;
		}
		// add class name to calendar header for DV
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoSL', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}
		// RETURN: language
			function lang($variable, $default_text){
				global $eventon_sl;
				return $eventon_sl->lang($variable, $default_text);
			}
		// function replace event name from string
			function replace_en($string){
				return str_replace('[event-name]', "<span class='eventName'>Event Name</span>", $string);
			}		
		
	    
}
