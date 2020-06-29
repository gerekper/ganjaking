<?php 
// ProgressBar Elements
if(!class_exists("ThePlus_progressbar")){
	class ThePlus_progressbar{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_progressbar') );
			add_shortcode( 'tp_progressbar',array($this,'tp_progressbar_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_progressbar_scripts' ), 1 );
		}
		function tp_progressbar_scripts() {
			wp_register_script( 'circle-progress', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/circle-progress.js',array(),'', false ); //circle-progress js
		}
		function tp_progressbar_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				  'main_style' =>'progressbar',
				  'pie_chart_style' =>'style_1',
				  'pie_border_style' =>'style_1',
				  'progressbar_style' =>'style_1',
				  
				  'title' => 'The Plus',
				  'title_color' =>'#252525',
				  'title_size' => '24px',
				  'title_line' =>'1.4',
				  'title_letter' =>'1px',
				  'title_use_theme_fonts'=>'custom-font-family',
				'title_font_family'=>'',
				'title_font_weight'=>'400',
				'title_google_fonts'=>'',
				  
				  'sub_title' =>'Pie chart subtitle',
				  'sub_color' =>'#d3d3d3',
				  'sub_size' => '14px',
				  'sub_line' =>'1.4',
				  'sub_letter' =>'1px',
				  'subtitle_use_theme_fonts'=>'custom-font-family',
				'subtitle_font_family'=>'',
				'subtitle_font_weight'=>'400',
				'subtitle_google_fonts'=>'',
				  
				  'progress_bar_size' =>'small',
				  'value_width' =>'50%',
				  'icon_postition' =>'after',
				  
				  'pie_value' =>'80',
				  'pie_size' =>'300',
				  'pie_anim_start' =>'0',
				  'pie_thickness' =>'10',
				  
				  'symbol' => '',
				  'symbol_position' => 'after',
				  'number' => '60',
				  
				  'number_color' =>'#252525',
				  'number_size' => '16px',
				  'number_line' =>'1.4',
				  'number_use_theme_fonts'=>'custom-font-family',
				'number_font_family'=>'',
				'number_font_weight'=>'400',
				'number_google_fonts'=>'',
				  
				  'pie_fill' =>'gradient',
				  'pie_fill_color' =>'#45a9f2',
				  'gradient_color1' =>'#1e73be',
				  'gradient_color2' =>'#2fcbce',
				  'gradient_hover_style' =>'horizontal',
				  'pie_empty_color' =>'#d3d3d3',
				  
				  'image_icon' => 'icon',
				  'select_image' =>'',
				  'type'=> 'fontawesome',
				  'icon_fontawesome'=>'fa fa-adjust',
				  'icon_openiconic'=> 'vc-oi vc-oi-dial',
				 'icon_typicons'=> 'typcn typcn-adjust-brightness',
			   'icon_entypo'=> 'entypo-icon entypo-icon-note',    
				 'icon_linecons'=> 'vc_li vc_li-heart',
				 'icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
				  'icon_size' =>'18px',
				  'icon_color' => '#0099CB',
				  'animation_effects'=>'no-animation',
				  'animation_delay'=>'50',
				  'el_class' =>'',
				  
				  'svg_icon' =>'svg',
				  'svg_image'=>'',
				  'svg_d_icon' =>'app.svg',
					'svg_type'=>'delayed',
					'duration'=>'80',
					'alignment'=>'text-center',
					'max_width'=>'100px',
					'border_stroke_color'=>'#ff0000',
				  
				), $atts ) );
				
				/*$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'progress_bar' . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );*/
				wp_enqueue_script( 'circle-progress');
				$rand_no=rand(1000000, 1500000);
				
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
				 
				$pia_width_size = $pie_value/100; 
				
				$progress_bar_title = $gradient_color = $progress_bar_img = $progress_bar_btn =  $progress_bar_align = $progress_bar_border = $progress_bar_icon_style= $imge_content =$data_fill_color=$number_markup=$pie_border_after=$progress_bar_sub='';
				
					if($value_width != "") {
						$fill_width = ' style="';
							$fill_width .= 'width: '.esc_attr($value_width).';';
							if($pie_fill== "solid") {
							$fill_width .= 'background-color: '.esc_attr($pie_fill_color).';';
							}else{
							$fill_width .=	pt_plus_gradient_color($gradient_color1,$gradient_color2,$gradient_hover_style);
							}
						$fill_width .= '"';
					}		
				
						
					
				if($number_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $number_google_fonts );
				$number_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($number_use_theme_fonts=='custom-font-family'){
				$number_font_family='font-family:'.$number_font_family.';font-weight:'.$number_font_weight.';';
			}else{
				$number_font_family='';
			}
				$number_css = ' style="';
						if($number_color != "") {
							$number_css .= 'color: '.esc_attr($number_color).';';
						}	
						if($number_size != "") {
							$number_css .= 'font-size: '.esc_attr($number_size).';';
						}
						
						if($number_line != "") {
							$number_css .= 'line-height: '.esc_attr($number_line).';';
						}
						$number_css .= $number_font_family;
						if($main_style == 'pie_chart_style'){
							if($pie_chart_style != 'style_3'){
								$number_css .= 'width: '.esc_attr($pie_size).'px;';
								$number_css .= 'height: '.esc_attr($pie_size).'px;';
								$number_css .= 'line-height: '.esc_attr($pie_size).'px;';
							}	
						}
					$number_css .= '"';
					
				if(!empty($symbol)) {
				  if($symbol_position=="after"){
					$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span><span>'.esc_html($symbol).'</span>';
					}elseif($symbol_position=="before"){
						$symbol2 = '<span>'.esc_html($symbol).'</span><span class="theserivce-milestone-number" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span>';
					}
				} else {
					$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span>';
				}
				
				if($title !=''){
				if($title_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $title_google_fonts );
				$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($title_use_theme_fonts=='custom-font-family'){
				$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
			}else{
				$title_font_family='';
			}
					$title_css = ' style="';
						if($title_color != "") {
							$title_css .= 'color: '.esc_attr($title_color).';';
						}	
						if($title_size != "") {
							$title_css .= 'font-size: '.esc_attr($title_size).';';
						}
						
						if($title_line != "") {
							$title_css .= 'line-height: '.esc_attr($title_line).';';
						}
						
						if($title_letter != "") {
							$title_css .= 'letter-spacing: '.esc_attr($title_letter).';';
						}
						$title_css .= $title_font_family;
					$title_css .= '"';
					
					 $progress_bar_title= '<span class="progress_bar-title" '.$title_css.'> '.esc_html($title).' </span>';
				}
				
				if($sub_title !=''){
				
			if($subtitle_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $subtitle_google_fonts );
				$subtitle_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($subtitle_use_theme_fonts=='custom-font-family'){
				$subtitle_font_family='font-family:'.$subtitle_font_family.';font-weight:'.$subtitle_font_weight.';';
			}else{
				$subtitle_font_family='';
			}
					$sub_css = ' style="';
						if($sub_color != "") {
							$sub_css .= 'color: '.esc_attr($sub_color).';';
						}	
						if($sub_size != "") {
							$sub_css .= 'font-size: '.esc_attr($sub_size).';';
						}
						
						if($sub_line != "") {
							$sub_css .= 'line-height: '.esc_attr($sub_line).';';
						}
						
						if($sub_letter != "") {
							$sub_css .= 'letter-spacing: '.esc_attr($sub_letter).';';
						}
						$sub_css .= $subtitle_font_family;
					$sub_css .= '"';
					
					 $progress_bar_sub= '<div class="progress_bar-sub_title" '.$sub_css.'> '.esc_html($sub_title).' </div>';
				}
				
					if($pie_size != ''){
					$inner_width = ' style="';
						$inner_width .= 'width: '.esc_attr($pie_size).'px;';
						$inner_width .= 'height: '.esc_attr($pie_size).'px;';
					$inner_width .= '"';
					}
				
				if($image_icon == 'image'){
				$img = wp_get_attachment_image_src("$select_image", "full");
				$imgSrc = $img[0];
					 $progress_bar_img='<span class="progres-ims"><img src="'.esc_url($imgSrc).'"   class="progress_bar-img '.esc_attr($imge_content).'" alt="" /></span>';
				}
				if($image_icon == 'icon'){		
					$icon_css = ' style="';
					if($icon_color != "") {
					$icon_css .= 'color: '.esc_attr($icon_color).';';
					}
					if($icon_size != "") {
					$icon_css .= 'font-size: '.esc_attr($icon_size).';';
					}
					
					$icon_css .= '"'; 
					vc_icon_element_fonts_enqueue( $type );
					$type12= $type; 
					$icon_class = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
					$progress_bar_img = '<span class="progres-ims"><i class=" '.esc_attr($icon_class).'  '.esc_attr($progress_bar_icon_style).'" '.$icon_css.'></i></span>';
				}
				$svg_attach = wp_get_attachment_image_src( $svg_image,true);
					$svg_url = $svg_attach[0];
				if($image_icon == 'svg'){
					if($svg_icon == 'img'){
						$svg_attach = wp_get_attachment_image_src( $svg_image,true);
						$svg_url = $svg_attach[0];
					}else{
						$svg_url = THEPLUS_PLUGIN_URL.'vc_elements/images/svg/'.esc_attr($svg_d_icon); 
					}
					$rand_no=rand(1000000, 1500000);
					
					if($svg_url ==''){
					if(!empty($border_stroke_color)){
						$border_stroke_color=$border_stroke_color;
					}else{
						$border_stroke_color='none';
					}
					}
					$progress_bar_img ='<span class="progres-ims"><div class="pt_plus_animated_svg '.esc_attr($alignment).' svg-'.esc_attr($rand_no).'" data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none" >';
						$progress_bar_img .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';max-height:'.esc_attr($max_width).';">';
							$progress_bar_img .='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
						$progress_bar_img .='</div>';
					$progress_bar_img .='</div></span>';
				}	
				
				if($icon_postition == 'after'){
					$icon_text = $progress_bar_title.$progress_bar_img.$progress_bar_sub;
				}else{
					$icon_text = $progress_bar_img.$progress_bar_title.$progress_bar_sub;
				}
					if($pie_fill =='gradient'){
						$data_fill_color = ' data-fill="{&quot;gradient&quot;: [&quot;' . esc_attr($gradient_color1) . '&quot;,&quot;' . esc_attr($gradient_color2) . '&quot;]}" ';
					}else{
					$data_fill_color .= ' data-fill="{&quot;color&quot;: &quot;' . esc_attr($pie_fill_color) . '&quot;}" ';
					}
					if($main_style == 'pie_chart_style'){
						if($pie_chart_style == 'style_1'){
							if($symbol2!= ''){
							$number_markup = '<h5 class="counter-number" '.$number_css.'>'.$progress_bar_img.$symbol2.'</h5>';
							}
						}else{
							if($symbol2!= ''){
							$number_markup = '<h5 class="counter-number" '.$number_css.'>'.$symbol2.'</h5>';
							}
						}
					}else{
						if($symbol2!= ''){
							$number_markup = '<h5 class="counter-number" '.$number_css.'>'.$symbol2.'</h5>';
							}
					}
					if($pie_border_style == "style_2") {
						$pie_border_after = "pie_border_after";
						$pie_empty_color1 = "transparent";
					}else{
						$pie_empty_color1 = $pie_empty_color;
						}
					
					$uid=uniqid('progress_bar');
					$progress_bar ='<div class="progress_bar pt-plus-peicharts progress-skill-bar  '.esc_attr($el_class).' '.esc_attr($uid).' progress_bar-'.esc_attr($main_style).' '.esc_attr($animated_class).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" data-empty="'.esc_attr($pie_empty_color).'" data-uid="'.esc_attr($uid).'" >';
					if($main_style == 'progressbar'){
						if($progressbar_style == 'style_1'){			
							if($progress_bar_size != 'large'){
								$progress_bar .= '<div class="progress_bar-media">';
									$progress_bar .= '<div class="prog-title prog-icon">';
										$progress_bar .= $icon_text; 
									$progress_bar .= '</div>'; 	
									$progress_bar .=$number_markup;
								$progress_bar .= '</div>';	
									
									$progress_bar .= '<div class="progress_bar-skill skill-fill '.esc_attr($progress_bar_size).'" style="background-color:'.esc_attr($pie_empty_color).'">';
										$progress_bar .= '<div class="progress_bar-skill-bar-filled " data-width="90" '.$fill_width.'>	</div>';
									$progress_bar .= '</div>';
							}else{
									$progress_bar .= '<div class="progress_bar-skill skill-fill '.esc_attr($progress_bar_size).'" style="background-color:'.esc_attr($pie_empty_color).'" >';
										$progress_bar .= '<div class="progress_bar-skill-bar-filled " '.$fill_width.'>	</div>';
										$progress_bar .= '<div class="progress_bar-media '.esc_attr($progress_bar_size).' " '.$fill_width.'>';	
											$progress_bar .= '<div class="prog-title prog-icon '.esc_attr($progress_bar_size).'">';
												$progress_bar .= $progress_bar_img.$progress_bar_title; 	
											$progress_bar .= '</div>';
											$progress_bar .=$number_markup;
										$progress_bar .= '</div>';
									$progress_bar .= '</div>';
									
								}
						}else if($progressbar_style == 'style_2'){
								$progress_bar .= '<div class="progress_bar-media">';	
									$progress_bar .= '<div class="prog-title prog-icon">';
										$progress_bar .= $icon_text; 	
									$progress_bar .= '</div>'; 	
									$progress_bar .=$number_markup;
								$progress_bar .= '</div>';	
								$progress_bar .= '<div class="progress_bar-skill skill-fill progress-'.esc_attr($progressbar_style).'" style="background-color:'.esc_attr($pie_empty_color).'">';
									$progress_bar .= '<div class="progress_bar-skill-bar-filled " data-width="90" '.$fill_width.'>	</div>';
								$progress_bar .= '</div>';
						
						}
						
					}
					
					if($main_style == 'pie_chart'){
					$progress_bar .= '<div class="pt-plus-piechart '.esc_attr($pie_border_after).' pie-'.esc_attr($pie_chart_style).'"  '.$data_fill_color.' data-emptyfill="'.esc_attr($pie_empty_color1).'" data-value="'.esc_attr($pia_width_size).'"  data-size="'.esc_attr($pie_size).'" data-thickness="'.esc_attr($pie_thickness).'"  data-animation-start-value="0"  data-reverse="false">';
					
						$progress_bar .= '<div class="pt-plus-circle" '.$inner_width.'>';
							$progress_bar .='<div class="pianumber-css" >';
							if($pie_chart_style != 'style_3'){
								$progress_bar .= $number_markup;
							}else{	
								$progress_bar .= $progress_bar_img;
							}
							$progress_bar .= '</div>';	
						$progress_bar .= '</div>';
					$progress_bar .= '</div>';
						if($pie_chart_style == 'style_1'){
							$progress_bar .= '<div class="pt-plus-pie_chart" >';
								$progress_bar .= $progress_bar_title;
								$progress_bar .= $progress_bar_sub;
							$progress_bar .= '</div>';	
						}else if($pie_chart_style == 'style_2'){
							$progress_bar .= '<div class="pt-plus-pie_chart style-2" >';
								$progress_bar .= '<div class="pie_chart " >';
									$progress_bar .= $progress_bar_img;
								$progress_bar .= '</div >';	
								$progress_bar .= '<div class="pie_chart-style2">';
								$progress_bar .= $progress_bar_title;
								$progress_bar .= $progress_bar_sub;
								$progress_bar .= '</div>';
									
							$progress_bar .= '</div>';	
						}else if($pie_chart_style == 'style_3'){
							$progress_bar .= '<div class="pt-plus-pie_chart style-3">';
								$progress_bar .= '<div class="pie_chart " >';
									$progress_bar .= $number_markup;
								$progress_bar .= '</div >';	
								$progress_bar .= '<div class="pie_chart-style3">';
								$progress_bar .= $progress_bar_title;
								$progress_bar .= $progress_bar_sub;
								$progress_bar .= '</div>';
									
							$progress_bar .= '</div>';	
						}
						
					}
					$progress_bar .= '</div>';	
					
					
					$css_rule='';
					$css_rule .= '<style >';
					
					if($main_style == 'progressbar'){			
						$css_rule .= '.'.esc_js($uid).' .progress-style_2 .progress_bar-skill-bar-filled:after {border-color: '.esc_js($pie_fill_color).';}';
					}
					if($main_style == 'pie_chart'){			
						$css_rule .= '.'.esc_js($uid).'.progress_bar-pie_chart .pie_border_after .pt-plus-circle:before,.'.esc_js($uid).'.progress_bar-progressbar .progress-style_2 .progress_bar-skill-bar-filled{border-color:'.esc_js($pie_empty_color).';}';
					}				
					$css_rule .= '</style>';
					$css_rule .= '<script>( function ( $ ) { 
		"use strict";
		$( document ).ready(function() {
			var elements = document.querySelectorAll(".pt-plus-piechart");
			Array.prototype.slice.apply(elements).forEach(function(el) {
				var $el = jQuery(el);
				//$el.circleProgress({value: 0});
				new Waypoint({
					element: el,
					handler: function() {
						if(!$el.hasClass("done-progress")){
						setTimeout(function(){
							$el.circleProgress({
								value: $el.data("value"),
								emptyFill: $el.data("emptyfill"),
								startAngle: -Math.PI/4*2,
							});
							//  this.destroy();
						}, 800);
						$el.addClass("done-progress");
						}
					},
					offset: "80%"
				});
			});
		});
		$(window).on("load resize scroll", function(){
			$(".pt-plus-peicharts").each( function(){
				var height=$("canvas",this).outerHeight();
				var width=$("canvas",this).outerWidth();
				$(".pt-plus-circle",this).css("height",height+"px");
				$(".pt-plus-circle",this).css("width",width+"px");
			});
		});
	} ( jQuery ) );</script>';
		
			return $css_rule.$progress_bar;
		}
		function init_tp_progressbar(){
			if(function_exists("vc_map"))
			{
		require(THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/vc_arrays.php');
				vc_map(array(
						"name" => __("Progress Bar", "pt_theplus"),
						"base" => "tp_progressbar",
						"icon" => "tp-progress-bar",
						"category" => __("The Plus", "pt_theplus"),
						"description" => esc_html__('Show Progress in Style', 'pt_theplus'),
						"params" => array(
							array(
									'type'        => 'radio_select_image',
									"admin_label" => true,
									'heading' =>  esc_html__('Main Style ', 'pt_theplus'),
									'param_name'  => 'main_style',
									'simple_mode' => false,
									'value' => 'progressbar',
									'options'     => array(
										'progressbar' => array(
											'tooltip' => esc_attr__('Progress bar','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/progress-bar-1.jpg'
										),
										'pie_chart' => array(
											'tooltip' => esc_attr__('Pie Chart','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/pie-chart.jpg'
										),
									),
								),
								array(
									'type'        => 'radio_select_image',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Pie Chart Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Pie Chart Styles', 'pt_theplus')),
									'param_name'  => 'pie_chart_style',
									'simple_mode' => false,
									'value' => 'style_1',
									'options'     => array(
										'style_1' => array(
											'tooltip' => esc_attr__('Style 1','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/ts-piechart-style-1.jpg'
										),
										'style_2' => array(
											'tooltip' => esc_attr__('Style 2','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/ts-piechart-style-2.jpg'
										),
										'style_3' => array(
											'tooltip' => esc_attr__('Style 3','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/ts-piechart-style-3.jpg'
										),
									),
									 'dependency' => array(
										'element' => 'main_style',
										'value' => 'pie_chart'
									),
								),
								array(
									'type'        => 'radio_select_image',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Progress Bar Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Progress Bar Styles', 'pt_theplus')),
									'param_name'  => 'progressbar_style',
									'simple_mode' => false,
									'value' => 'style_1',
									'options'     => array(
										'style_1' => array(
											'tooltip' => esc_attr__('Style 1','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/progress-bar-1.jpg'
										),
										'style_2' => array(
											'tooltip' => esc_attr__('Style 2','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/progress-bar-2.jpg'
										),
									),
									'dependency' => array(
										'element' => 'main_style',
										'value' => 'progressbar'
									),
								),
								array(
									'type'        => 'radio_select_image',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Pie Chart Round Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Pie Chart Round Styles', 'pt_theplus')),
									'param_name'  => 'pie_border_style',
									'simple_mode' => false,
									'value' => 'style_1',
									'options'     => array(
										'style_1' => array(
											'tooltip' => esc_attr__('Style 1','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/ts-piechart-style-4.jpg'
										),
										'style_2' => array(
											'tooltip' => esc_attr__('Style 2','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/ts-piechart-style-5.jpg'
										),
									),
									'dependency' => array(
										'element' => 'main_style',
										'value' => 'pie_chart'
									),
								),
								
								array(
									'type'        => 'radio_select_image',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Progress Bar Height using this option.','pt_theplus').'</span></span>'.esc_html__('Progress Bar Height', 'pt_theplus')),
									'param_name'  => 'progress_bar_size',
									'simple_mode' => false,
									'value' => 'small',
									'options'     => array(
										'small' => array(
											'tooltip' => esc_attr__('Small','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/small-bar.jpg'
										),
										'medium' => array(
											'tooltip' => esc_attr__('Medium','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/medium-bar.jpg'
										),
										'large' => array(
											'tooltip' => esc_attr__('Large','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/progress-bar/large-bar.jpg'
										),
									),
									'dependency' => array(
										'element' => 'progressbar_style',
										'value' => 'style_1'
									),
								),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value for pie chart, according to this value pie chart will be filled. e.g. 30,70 etc.','pt_theplus').'</span></span>'.esc_html__('Dynamic Value', 'pt_theplus')),
								"param_name" => "pie_value",
								"value" => '80',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'main_style',
									'value' => 'pie_chart'
								)
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of circle size must be in numbers. e.g. 200,350 etc.','pt_theplus').'</span></span>'.esc_html__('Pie Chart Circle Size', 'pt_theplus')),
								"param_name" => "pie_size",
								"admin_label" => false,
								"value" => '300',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'main_style',
									'value' => 'pie_chart'
								)
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can enter thickness of piechart value using this option. e.g. 10,15 etc.','pt_theplus').'</span></span>'.esc_html__('Thickness', 'pt_theplus')),
								"param_name" => "pie_thickness",
								"admin_label" => false,
								"value" => '10',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'main_style',
									'value' => 'pie_chart'
								)
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Charts fill color using this option.','pt_theplus').'</span></span>'.esc_html__('Chart Fill Color', 'pt_theplus')),
								"param_name" => "pie_fill",
								"admin_label" => false,
								"value" => array(
									__('Solid', 'pt_theplus') => 'solid',
									__('Gradient', 'pt_theplus') => 'gradient'
								),
								"std" => "gradient",
								"description" => "",
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Fill Color ', 'pt_theplus'),
								'param_name' => 'pie_fill_color',
								'dependency' => array(
									'element' => 'pie_fill',
									'value' => 'solid'
								),
								"edit_field_class" => "vc_col-xs-12",
								"value" => '#45a9f2'
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Color 1', 'pt_theplus'),
								'param_name' => 'gradient_color1',
								
								'dependency' => array(
									'element' => 'pie_fill',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#1e73be'
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Color 2', 'pt_theplus'),
								'param_name' => 'gradient_color2',
								'dependency' => array(
									'element' => 'pie_fill',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#2fcbce'
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
								'param_name' => 'gradient_hover_style',
								'value' => array(
									__('Horizontal', 'pt_theplus') => 'horizontal',
									__('Vertical', 'pt_theplus') => 'vertical',
									__('Diagonal', 'pt_theplus') => 'diagonal',
									__('Radial', 'pt_theplus') => 'radial'
								),
								'std' => 'horizontal',
								"description" => "",
								'dependency' => array(
									'element' => 'pie_fill',
									'value' => 'gradient'
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color for the space which isn&#39;t filled by value.','pt_theplus').'</span></span>'.esc_html__('Empty space color', 'pt_theplus')),
								'param_name' => 'pie_empty_color',
								"edit_field_class" => "vc_col-xs-12",
								"value" => '#d3d3d3'
							),
							array(
								"type" => "textfield",
								'heading' =>  esc_html__('Title', 'pt_theplus'),
								"admin_label" => true,
								"param_name" => "title",
								"value" => 'The Plus',
								"description" => ""
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add sub title of progress bar using this option.','pt_theplus').'</span></span>'.esc_html__('Sub Title', 'pt_theplus')),
								"param_name" => "sub_title",
								"value" => 'Pie chart subtitle',
								"description" => ""
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter value for progress bar, according to this value progress bar will be filled. e.g. 30,70 etc.','pt_theplus').'</span></span>'.esc_html__('Dynamic Value', 'pt_theplus')),
								"heading" => esc_html__("Dynamic Value", 'pt_theplus'),
								"param_name" => "value_width",
								"value" => '50%',
								'dependency' => array(
									'element' => 'main_style',
									'value' => 'progressbar'
								),
								"description" => ""
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This value will be just static numbers on progress bar. e,g, 30,70 etc.','pt_theplus').'</span></span>'.esc_html__('Numbers', 'pt_theplus')),
								"param_name" => "number",
								'value' => '60',
								"description" => __(" ", 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('An optional symbol to place next to the number counted to. e.g. \"%\" or \"+\"','pt_theplus').'</span></span>'.esc_html__('Prefix/Postfix Symbol', 'pt_theplus')),
								"param_name" => "symbol",
								"admin_label" => false,
								"description" => ""
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Symbol Position using this option.','pt_theplus').'</span></span>'.esc_html__('Symbol Position', 'pt_theplus')),
								"param_name" => "symbol_position",
								"value" => array(
									esc_attr__("After Number", 'pt_theplus') => "after",
									esc_attr__("Before Number", 'pt_theplus') => "before"
								),
								"description" => "",
								"dependency" => Array(
									'element' => "symbol",
									'not_empty' => true
								)
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Title Setting', 'pt_theplus'),
								'param_name' => 'number_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								'group' => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "title_color",
								"value" => '#252525',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => __('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_size",
								"value" => '24px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"class" => "",
								"param_name" => "title_line",
								'value' => '1.4',
								"description" => __(" ", 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_letter",
								'value' => '1px',
								"description" => __(" ", 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
									'param_name' => 'title_use_theme_fonts',
									 "value" => array(
										esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
										esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
									),
									'std' =>  'custom-font-family',
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'title_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'title_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'title_font_weight',
								'value' => __('400','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'title_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'title_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'title_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),	
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Sub-Title Setting', 'pt_theplus'),
								'param_name' => 'number_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								'group' => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "sub_color",
								"value" => '#d3d3d3',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => __('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "sub_size",
								"value" => '14px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "sub_line",
								'value' => '1.4',
								"description" => __(" ", 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "sub_letter",
								'value' => '1px',
								"description" => __(" ", 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Subtitle Custom font family', 'pt_theplus'),
									'param_name' => 'subtitle_use_theme_fonts',
									 "value" => array(
										esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
										esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
									),
									'std' =>  'custom-font-family',
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'subtitle_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'subtitle_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'subtitle_font_weight',
								'value' => __('400','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'subtitle_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'subtitle_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'subtitle_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Number Setting', 'pt_theplus'),
								'param_name' => 'number_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								'group' => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "number_color",
								"value" => '#252525',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => __('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "number_size",
								"value" => '16px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
						   
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line height', 'pt_theplus')),
								"param_name" => "number_line",
								'value' => '1.4',
								"description" => __(" ", 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Style', 'pt_theplus')
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Number Custom font family', 'pt_theplus'),
									'param_name' => 'number_use_theme_fonts',
									 "value" => array(
										esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
										esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
									),
									'std' =>  'custom-font-family',
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'number_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'number_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'number_font_weight',
								'value' => __('400','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'number_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'number_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'number_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon ', 'pt_theplus')),
								"param_name" => "image_icon",
								"value" => array(
									__('None', 'pt_theplus') => '',
									__('Icon', 'pt_theplus') => 'icon',
									__('Image', 'pt_theplus') => 'image',
									__('Svg', 'pt_theplus') => 'svg'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								"std" => "icon"
							),
							array(
								"type" => "dropdown",
								"heading" => __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Svg Type', 'pt_theplus')),
								"param_name" => "svg_icon",
								"value" => array(
									__('Pre Built SVG Icon', 'pt_theplus') => 'img',
									__('Custom Upload', 'pt_theplus') => 'svg'
								),
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'svg'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								"std" => "svg"
							),
							$svg_attach,
							$svg_attach_icon,
							$svg_type,
							$svg_duration,
							$svg_width,
							$svg_border,
							array(
								"type" => "attach_image",
								"heading" => esc_html__("Use Image As icon", 'pt_theplus'),
								"value" => "",
								"description" => '',
								"param_name" => 'select_image',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'image'
								),
								'group' => __('Icon Option', 'pt_theplus')
							),
							
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' We have given options of icons from Font Awesome, Open Iconic, Linecons, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
								'value' => array(
									__('Font Awesome', 'pt_theplus') => 'fontawesome',
									__('Open Iconic', 'pt_theplus') => 'openiconic',
									__('Typicons', 'pt_theplus') => 'typicons',
									__('Entypo', 'pt_theplus') => 'entypo',
									__( 'Linecons', 'pt_theplus' ) => 'linecons',
									__('Mono Social', 'pt_theplus') => 'monosocial'
								),
								'admin_label' => false,
								'param_name' => 'type',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_fontawesome',
								'value' => 'fa fa-adjust',
								'settings' => array(
									'emptyIcon' => false,
									'iconsPerPage' => 100
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'fontawesome'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_openiconic',
								'value' => 'vc-oi vc-oi-dial',
								'settings' => array(
									'emptyIcon' => false,
									'type' => 'openiconic',
									'iconsPerPage' => 100
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'openiconic'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_typicons',
								'value' => 'typcn typcn-adjust-brightness',
								'settings' => array(
									'emptyIcon' => false,
									'type' => 'typicons',
									'iconsPerPage' => 100
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'typicons'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_entypo',
								'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'entypo',
									'iconsPerPage' => 100 // default 100, how many icons per/page to display
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'dependency' => array(
									'element' => 'type',
									'value' => 'entypo'
								)
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_linecons',
								'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'linecons',
									'iconsPerPage' => 100 // default 100, how many icons per/page to display
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'dependency' => array(
									'element' => 'type',
									'value' => 'linecons'
								),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_monosocial',
								'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'monosocial',
									'iconsPerPage' => 100 // default 100, how many icons per/page to display
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'dependency' => array(
									'element' => 'type',
									'value' => 'monosocial'
								),
								'description' => '',
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Icon Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Styles', 'pt_theplus')),
								"param_name" => "icon_style",
								"value" => "",
								"description" => "",
								"value" => array(
									__('None', 'pt_theplus') => '',
									__('Square', 'pt_theplus') => 'square',
									__('Rounded', 'pt_theplus') => 'rounded'
								),
								'group' => __('Icon Option', 'pt_theplus'),
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon'
								),
								"std" => "square",
								"admin_label" => false
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
								'param_name' => 'icon_size',
								'value' => '18px',
								"edit_field_class" => "vc_col-xs-6",
								'group' => __('Icon Option', 'pt_theplus'),
								'description' => '',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon'
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
								'param_name' => 'icon_color',
								"edit_field_class" => "vc_col-xs-6",
								'value' => '#0099CB',
								'description' => '',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon'
								),
								'group' => __('Icon Option', 'pt_theplus')
							),
							
							array(
								"type" => "dropdown",
								"heading" => __("Icon Title Before after", "pt_theplus"),
								"param_name" => "icon_postition",
								"value" => array(
									__('Before', 'pt_theplus') => 'Before',
									__('After', 'pt_theplus') => 'after'
								),
								"std" => "after",
								'dependency' => array(
									'element' => 'image_icon',
									'value' => array('icon','image','svg')
								),
								'group' => __('Icon Option', 'pt_theplus'),
								"description" => "",
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
								'edit_field_class' => 'vc_col-sm-6',
								"admin_label" => false,
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
	new ThePlus_progressbar;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_progressbar'))
	{
		class WPBakeryShortCode_tp_progressbar extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}