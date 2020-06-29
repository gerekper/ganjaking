<?php 
// Countdown Elements
if(!class_exists("ThePlus_countdown")){
	class ThePlus_countdown{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_countdown') );
			add_shortcode( 'tp_countdown',array($this,'tp_countdown_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_countdown_scripts' ), 1 );
		}
		function tp_countdown_scripts() {		
			wp_register_script( 'downCount-js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.downCount.js',array(),'', true );
		}
		function tp_countdown_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				  'counting_timer' => '30-12-2017',
				  'text_days'	=>'Days',
				  'text_hours'	=>'Hours',
				  'text_minutes'	=>'Minutes',
				  'text_seconds'	=>'Seconds',
				  
				  'no_font_size' =>'40px',
				  'number_text_color' =>'',
				  'counter_use_theme_fonts'=>'custom-font-family',
				'counter_font_family'=>'',
				'counter_font_weight'=>'400',
				'counter_google_fonts'=>'',
				
				  'days_border_color' =>'',
				  'hours_border_color' =>'',
				  'minutes_border_color' =>'',
				  'seconds_border_color' =>'',
				   'label_use_theme_fonts'=>'custom-font-family',
				'label_font_family'=>'',
				'label_font_weight'=>'400',
				'label_google_fonts'=>'',
				   'animation_effects'=>'no-animation',
			  'animation_delay'=>'50',
				  'el_class' =>'',
				  'border_type'=>'simple',
				  'view_animate' =>''
			   ), $atts ) );
			   wp_enqueue_script( 'downCount-js');
			   if($animation_effects=='no-animation'){
					$animated_class='';
				$animation_effects='';
				$animation_delay='';
				$animation_delay_time='';
			}else{
				$animated_class='animate-general';
				$animation_effects=$animation_effects;
				$animation_delay_time=$animation_delay;
			}

			if($counter_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $counter_google_fonts );
				$counter_style = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($counter_use_theme_fonts=='custom-font-family'){
				$counter_style='font-family:'.$counter_font_family.';font-weight:'.$counter_font_weight.';';
			}else{
				$counter_style='';
			}

			if($label_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $label_google_fonts );
				$label_style = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($label_use_theme_fonts=='custom-font-family'){
				$label_style='font-family:'.$label_font_family.';font-weight:'.$label_font_weight.';';
			}else{
				$label_style='';
			}

			   if($no_font_size != "" || $number_text_color != ""){
			  $span_css = ' style="';
			  if($no_font_size != "") {
					$span_css .='font-size:'.esc_attr($no_font_size).';';
					$span_css .='line-height:'.esc_attr($no_font_size).';';
				}
				$span_css .=$counter_style;
				if($number_text_color != "") {
					$span_css .= 'color: '.esc_attr($number_text_color).';';
				}
			  $span_css .= '"';
			   }
				  
			  
			  $h6_css = ' style="';
				if($number_text_color != "") {
					$h6_css .= 'color: '.esc_attr($number_text_color).';';
				}
				$h6_css .= $label_style;
			  $h6_css .= '"';
			  
			   

			  $days_css = ' style="';
				if($days_border_color != "") {
					$days_css .= 'border: 4px solid '.esc_attr($days_border_color).';';
				}else{
					$days_css .= "border: none;";
				}
			  $days_css .= '"';

			   $hours_css = ' style="';
				if($hours_border_color != "") {
					$hours_css .= 'border: 4px solid '.esc_attr($hours_border_color).';';
				}else{
					$hours_css .= "border: none;";
				}
			  $hours_css .= '"';
			  
			   $minutes_css = ' style="';
				if($minutes_border_color != "") {
					$minutes_css .= 'border: 4px solid '.esc_attr($minutes_border_color).';';
				}else{
					$minutes_css .= "border: none;";
				}
			  $minutes_css .= '"';
			  
			   $seconds_css = ' style="';
				if($seconds_border_color != "") {
					$seconds_css .= 'border: 4px solid '.esc_attr($seconds_border_color).';';
				}else{
					$seconds_css .= "border: none;";
				}
			  $seconds_css .= '"';
			  ?>

			<?php
			$uid=uniqid('count_down');
			 $attr='';
			if($text_days==''){
				$text_days='Days';
			}
			if($text_hours==''){
				$text_hours='Hours';
			}
			if($text_minutes==''){
				$text_minutes='Minutes';
			}
			if($text_seconds==''){
				$text_seconds='Seconds';
			}
				$attr .='data-days="'.esc_attr($text_days).'"';
			 $attr .='data-hours="'.esc_attr($text_hours).'"';
			 $attr .='data-minutes="'.esc_attr($text_minutes).'"';
			 $attr .='data-seconds="'.esc_attr($text_seconds).'"';
			$countdown = '<ul class="pt_plus_countdown '. esc_attr($uid) .' '. esc_attr($el_class) .' '. esc_attr($border_type) .' '.esc_attr($animated_class).'" '.$attr.' data-timer="'. esc_attr($counting_timer) .'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">
					<li class="count_1" '.$days_css.'>
						<span class="days" '.$span_css.'>00</span>
						<h6 class="days_ref" '.$h6_css.'>'.esc_html($text_days).'</h6>
					</li>
					<li class="count_2" '.$hours_css.'>
						<span class="hours" '.$span_css.'>00</span>
						<h6 class="hours_ref" '.$h6_css.'>'.esc_html($text_hours).'</h6>
					</li>
					<li class="count_3" '.$minutes_css.'>
						<span class="minutes" '.$span_css.'>00</span>
						<h6 class="minutes_ref" '.$h6_css.'>'.esc_html($text_minutes).'</h6>
					</li>
					<li class="count_4" '.$seconds_css.'>
						<span class="seconds last" '.$span_css.'>00</span>
						<h6 class="seconds_ref" '.$h6_css.'>'.esc_html($text_seconds).'</h6>
					</li>
				</ul>';
				$css_rule='<script>( function ( $ ) { 
						"use strict";
					$(document).ready(function () {
						$(".pt_plus_countdown").each(function () {
							var timer1 = $(this).attr("data-timer");
							var res = timer1.split("-");
							var text_days=$(this).data("days");
							var text_hours=$(this).data("hours");
							var text_minutes=$(this).data("minutes");
							var text_seconds=$(this).data("seconds");
							$(this).downCount({
								date: res[1]+"/"+res[0]+"/"+res[2]+" 12:00:00",
								offset: +1,
								text_day:text_days,
								text_hour:text_hours,
								text_minute:text_minutes,
								text_second:text_seconds,
							});
						});
					});	
					} ( jQuery ) );</script>';
			return $css_rule.$countdown;
		}
		function init_tp_countdown(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Countdown", 'pt_theplus'),
					"base" => "tp_countdown",
					"icon" => "tp-countdown",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Count your time in Style', 'pt_theplus'),
					"params" => array(
						array(
							"type" => "textfield",
							'heading' =>  esc_html__('Launch Date', 'pt_theplus'),
							"param_name" => "counting_timer",
							 "admin_label" => true,
							"value" => '30-12-2017',
							"description" => ""
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text for days countdown section.','pt_theplus').'</span></span>'.esc_html__('Days Section Text', 'pt_theplus')),
							"param_name" => "text_days",
							 'edit_field_class' => 'vc_col-sm-3',
							"value" => 'Days',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text for hours countdown section.','pt_theplus').'</span></span>'.esc_html__('Hours Section Text', 'pt_theplus')),
							"param_name" => "text_hours",
							 'edit_field_class' => 'vc_col-sm-3',
							"value" => 'Hours',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text for minutes countdown section.','pt_theplus').'</span></span>'.esc_html__('Minutes Section Text', 'pt_theplus')),
							"param_name" => "text_minutes",
							 'edit_field_class' => 'vc_col-sm-3',
							"value" => 'Minutes',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text for seconds countdown section.','pt_theplus').'</span></span>'.esc_html__('Seconds Section Text', 'pt_theplus')),
							"param_name" => "text_seconds",
							 'edit_field_class' => 'vc_col-sm-3',
							"value" => 'Seconds',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Counter Font Size', 'pt_theplus')),
							"param_name" => "no_font_size",
							 'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Counter Font Color', 'pt_theplus')),
							"param_name" => "number_text_color",
							 'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Counter Custom font family', 'pt_theplus'),
								'param_name' => 'counter_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'counter_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
									'element' => 'counter_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'counter_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',	
							'dependency' => array(
									'element' => 'counter_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'counter_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'counter_use_theme_fonts',
									'value' => 'google-fonts',
								),
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Border Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Select Border Style', 'pt_theplus')),
							"param_name" => "border_type",
							"admin_label" => false,
							"value" => array(
								esc_attr__('Simple', 'pt_theplus') => 'simple',
								esc_attr__('Rounded', 'pt_theplus') => 'rounded'
							)
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Days Border using this option.','pt_theplus').'</span></span>'.esc_html__('Days Border Color', 'pt_theplus')),
							"param_name" => "days_border_color",
							 'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Hours Border using this option.','pt_theplus').'</span></span>'.esc_html__('Hours Border Color', 'pt_theplus')),
							"param_name" => "hours_border_color",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
							"type" => "colorpicker",
							"class" => "",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Minutes Border using this option.','pt_theplus').'</span></span>'.esc_html__('Minutes Border Color', 'pt_theplus')),
							"param_name" => "minutes_border_color",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Seconds Border using this option.','pt_theplus').'</span></span>'.esc_html__('Seconds Border Color', 'pt_theplus')),
							"param_name" => "seconds_border_color",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Label Custom font family', 'pt_theplus'),
								'param_name' => 'label_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'label_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
									'element' => 'label_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'label_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',	
							'dependency' => array(
									'element' => 'label_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'label_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'label_use_theme_fonts',
									'value' => 'google-fonts',
								),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Animation Settings', 'pt_theplus'),
							'param_name' => 'annimation_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							"param_name" => "animation_effects",
							"admin_label" => false,
							 'edit_field_class' => 'vc_col-sm-6',
							"value" => array(
								__('No-animation', 'pt_theplus') => 'no-animation',
								__('FadeIn', 'pt_theplus') => 'transition.fadeIn',
								__('FlipXIn', 'pt_theplus') => 'transition.flipXIn',
								__('FlipYIn', 'pt_theplus') => 'transition.flipYIn',
								__('FlipBounceXIn', 'pt_theplus') => 'transition.flipBounceXIn',
								__('FlipBounceYIn', 'pt_theplus') => 'transition.flipBounceYIn',
								__('SwoopIn', 'pt_theplus') => 'transition.swoopIn',
								__('WhirlIn', 'pt_theplus') => 'transition.whirlIn',
								__('ShrinkIn', 'pt_theplus') => 'transition.shrinkIn',
								__('ExpandIn', 'pt_theplus') => 'transition.expandIn',
								__('BounceIn', 'pt_theplus') => 'transition.bounceIn',
								__('BounceUpIn', 'pt_theplus') => 'transition.bounceUpIn',
								__('BounceDownIn', 'pt_theplus') => 'transition.bounceDownIn',
								__('BounceLeftIn', 'pt_theplus') => 'transition.bounceLeftIn',
								__('BounceRightIn', 'pt_theplus') => 'transition.bounceRightIn',
								__('SlideUpIn', 'pt_theplus') => 'transition.slideUpIn',
								__('SlideDownIn', 'pt_theplus') => 'transition.slideDownIn',
								__('SlideLeftIn', 'pt_theplus') => 'transition.slideLeftIn',
								__('SlideRightIn', 'pt_theplus') => 'transition.slideRightIn',
								__('SlideUpBigIn', 'pt_theplus') => 'transition.slideUpBigIn',
								__('SlideDownBigIn', 'pt_theplus') => 'transition.slideDownBigIn',
								__('SlideLeftBigIn', 'pt_theplus') => 'transition.slideLeftBigIn',
								__('SlideRightBigIn', 'pt_theplus') => 'transition.slideRightBigIn',
								__('PerspectiveUpIn', 'pt_theplus') => 'transition.perspectiveUpIn',
								__('PerspectiveDownIn', 'pt_theplus') => 'transition.perspectiveDownIn',
								__('PerspectiveLeftIn', 'pt_theplus') => 'transition.perspectiveLeftIn',
								__('PerspectiveRightIn', 'pt_theplus') => 'transition.perspectiveRightIn'
							),
							'std' => 'no-animation'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
							"param_name" => "animation_delay",
							 'edit_field_class' => 'vc_col-sm-6',
							"value" => '50',
							"description" => ""
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Extra Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),
						
					)
				));
			}
		}
	}
	new ThePlus_countdown;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_countdown'))
	{
		class WPBakeryShortCode_tp_countdown extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}



