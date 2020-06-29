<?php
// Pin Point Elements
if(!class_exists("ThePlus_pin_point")){
	class ThePlus_pin_point{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_pin_point') );
			add_shortcode( 'tp_pin_point',array($this,'tp_pin_point_shortcode'));
		}
		function tp_pin_point_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'pin_point_loop'=> '',
					'image_source' => 'media_library',
					'bg_image'=>'',
					'external_img' =>'',
					'tooltip_style'=>'default',
					'pin_trigger' => 'hover',
					'tooltip_animation' => 'fade',
					'title_fontsize' => '20px',
					'title_lineheight' => '24px',
					'title_color' => '#777',
					'tooltip_bg_color' =>'#fff',
			   ), $atts ) );
			   wp_enqueue_style('tooltipster');
				wp_enqueue_style('tooltipster_theme');
				wp_enqueue_script('tooltipster_js');

			$bg_imgSrc = '';
			if ($image_source == 'media_library') {
			$bg_img = wp_get_attachment_image_src($bg_image, "full");
			$bg_imgSrc = $bg_img[0];
			} else if ($image_source == 'externals_link') {
			$bg_imgSrc = $external_img;
			}
			$css_loop='';
			$pin_point = '<div class="pt-plus-pin-point">';
			$pin_point .= '<img src="'.$bg_imgSrc.'" alt="" class="bg-image" alt="" />';
			if(isset($pin_point_loop) && !empty($pin_point_loop) && function_exists('vc_param_group_parse_atts')) {
			$pin_point .= '<ul>';
				$pin_loops= (array) vc_param_group_parse_atts( $pin_point_loop);		
				foreach($pin_loops as $item) {
					$effects=$icons='';
					$xpos='10';
					if(isset($item['xposition']) && !empty($item['xposition'])){
						$xpos=$item['xposition'];
					}
					$ypos='10';
					if(isset($item['yposition']) && !empty($item['yposition'])){
						$ypos=$item['yposition'];
					}
					if(isset($item['effects']) && !empty($item['effects'])){
						$effects=$item['effects'];
					}
					$tooltip_direction='';
					if(isset($item['tooltip_direction']) && !empty($item['tooltip_direction'])){
						$tooltip_direction=$item['tooltip_direction'];
					}
					$icon_size='medium';
					if(isset($item['icon_size']) && !empty($item['icon_size'])){
						$icon_size=$item['icon_size'];
					}
					$icon_fontawesome=$icon_openiconic=$icon_typicons=$icon_entypo=$icon_linecons=$icon_monosocial='';
					if(!empty($item['icon_fontawesome'])){
					   $icon_fontawesom = $item['icon_fontawesome'];
					  }
					  if(!empty($item['icon_openiconic'])){
					   $icon_openiconic= $item['icon_openiconic'];
					  }
					  if(!empty($item['icon_typicons'])){
					   $icon_typicons= $item['icon_typicons'];
					  }
					  if(!empty($item['icon_entypo'])){
					   $icon_entypo= $item['icon_entypo'];
					  }
					  if(!empty($item['icon_linecons'])){
					   $icon_linecons= $item['icon_linecons'];
					  }
					  if(!empty($item['icon_monosocial'])){
					   $icon_monosocial= $item['icon_monosocial'];
					  }	
					$pin_style='style-1';
					if(!empty($item['pin_style'])){
					   $pin_style=$item['pin_style'];
					}
					
					$rand_no=rand(100, 1500000);
					$attr_data='';
					$attr_tooltip='';
					$attr_tooltip .='data-tooltip_direction="'.esc_attr($tooltip_direction).'" data-tooltip_style="'.esc_attr($tooltip_style).'" data-pin_trigger="'.esc_attr($pin_trigger).'" data-tooltip_animation="'.esc_attr($tooltip_animation).'"';
					
					if(empty($item['options']) || $item['options']=='icons'){
						$attr_data .='data-uid="pin-'.esc_attr($rand_no).'"  data-pin_style="'.esc_attr($pin_style).'" data-pin_icon_color="'.esc_attr($item['pin_icon_color']).'" data-pin_hover_icon_color="'.esc_attr($item['pin_hover_icon_color']).'" data-pin_bg_color="'.esc_attr($item['pin_bg_color']).'" data-pin_hover_bg_color="'.esc_attr($item['pin_hover_bg_color']).'" data-title_fontsize="'.esc_attr($title_fontsize).'" data-title_lineheight="'.esc_attr($title_lineheight).'" data-title_color="'.esc_attr($title_color).'" data-tooltip_bg_color="'.esc_attr($tooltip_bg_color).'" data-tooltip_style="'.esc_attr($tooltip_style).'" data-tooltip_direction="'.esc_attr($tooltip_direction).'" ';
					}
					
				$pin_point .= '<li class="pt-plus-pin-point-single pin-'.esc_attr($rand_no).' '.esc_attr($icon_size).' '.esc_attr($pin_style).'"  '.$attr_data.' style="top: '.esc_attr($ypos).'%;left:'.esc_attr($xpos).'%;">';
								
								if(empty($item['options']) && empty($item['pin_icon'])){
								
									$pin_point .= '<a class="pin-point-trigger pt_plus_pin_point_icon '.esc_attr($effects).' " '.$attr_tooltip.' data-tooltip-content="#pin-'.esc_attr($rand_no).'" href="#">'.esc_html__('More','pt_theplus').'</a>';
								
								}else if(!empty($item['options']) && $item['options']=='icons' && !empty($item['options'])){
									
									if(!empty($item['fonts_icon'])){
									$font_icons=$item['fonts_icon'];
									  vc_icon_element_fonts_enqueue( $font_icons );

									 $icon_class = isset( ${'icon_' .$font_icons} ) ? $item['icon_' .$font_icons] : 'fa fa-arrow-right';
									  $icons = '<i class="pin-fonts-icon '.esc_attr($icon_class).'"></i>';
									}
									$pin_point .= '<a class="pin-point-trigger pt_plus_pin_point_icons '.esc_attr($effects).' " '.$attr_tooltip.' data-tooltip-content="#pin-'.esc_attr($rand_no).'" href="#">'.$icons.'</a>';
								
								}else if(!empty($item['options']) && $item['options']=='image' && !empty($item['pin_icon'])){
									
									$pin_img = wp_get_attachment_image_src($item['pin_icon'], "full");
									$imgSrc = $pin_img[0];
									$pin_point .= '<a class="pin-point-trigger pin_point_image '.esc_attr($effects).' " '.$attr_tooltip.' data-tooltip-content="#pin-'.esc_attr($rand_no).'" href="#"><img src="'.esc_url($imgSrc).'" > </a>';
									
								}
								
								$pin_point .= '<div class="pin-point-content-wrap tooltip_templates tooltipster-right">';
								$pin_point .='<div id="pin-'.esc_attr($rand_no).'" class="pt-plus-pin-point-content-tooltip">';
									if(!empty($item['pin_title'])){
										$pin_point .= '<div class="pin-point-title">'.esc_html($item['pin_title']).'</div>';
									}
									if(!empty($item['pin_desc']) || !empty($item['pin_desc_text'])){
										$pin_point .='<div class="pin-point-content">';
										if(!empty($item['pin_desc_text'])){
											$pin_point .=$item['pin_desc_text'];
										}
										if(!empty($item['pin_desc'])){
											$pin_point .= wpb_js_remove_wpautop( $item['pin_desc'], true );
										}
										$pin_point .= '</div>';						
									}			
								$pin_point .= '</div>';
								$pin_point .= '</div>';
								
				$pin_point .= '</li>';
				if(!empty($item['options']) && $item['options'] == 'icons'|| empty($item['options']) && empty($item['pin_icon'])){
					$css_loop.= '.pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pin-point-trigger{background :'.esc_js($item['pin_bg_color']).';color: '.esc_js($item['pin_icon_color']).';}.pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pin-point-trigger:hover{background :'.esc_js($item['pin_hover_bg_color']).';color: '.esc_js($item['pin_hover_icon_color']).';}.pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pt_plus_pin_point_icon::after, .pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pt_plus_pin_point_icon:before{background-color: '.esc_js($item['pin_icon_color']).';}.pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pt_plus_pin_point_icon:hover:after, .pin-'.esc_attr($rand_no).'.'.$pin_style.' > a.pt_plus_pin_point_icon:hover:before{background-color: '.esc_js($item['pin_hover_icon_color']).';}.pin-'.esc_attr($rand_no).'.pt-plus-pin-point-single.style-3:after{background: '.esc_js($item['pin_hover_bg_color']).';}';
				}		
			
				if($tooltip_style=='custom'){
					$css_loop.= '.tooltipster-custom .tooltipster-content .pt-plus-pin-point-content-tooltip{background:'.esc_js($tooltip_bg_color).' ;}.tooltipster-custom.tooltipster-bottom  .tooltipster-arrow-background,.tooltipster-custom.tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-border{border-bottom-color: '.esc_js($tooltip_bg_color).' !important;}.tooltipster-custom.tooltipster-sidetip.tooltipster-left .tooltipster-arrow-border,.tooltipster-custom.tooltipster-left  .tooltipster-arrow-background{border-left-color: '.esc_js($tooltip_bg_color).' !important;}.tooltipster-custom.tooltipster-sidetip.tooltipster-right .tooltipster-arrow-border,.tooltipster-custom.tooltipster-right .tooltipster-arrow-background,.tooltipster-custom.tooltipster-right .tooltipster-arrow-border{border-right-color: '.esc_js($tooltip_bg_color).' !important;}.tooltipster-custom.tooltipster-top  .tooltipster-arrow-background,.tooltipster-custom.tooltipster-sidetip.tooltipster-top .tooltipster-arrow-border{border-top-color: '.esc_js($tooltip_bg_color).' !important;}';
				}
				
			//	$css_loop.= '#pin-'.esc_attr($rand_no).' .pin-point-title{font-size:'.esc_js($title_fontsize).';line-height:'.esc_js($title_lineheight).';color:'.esc_js($title_color).';'.esc_js($title_style).'}#pin-'.esc_attr($rand_no).' .pin-point-content{font-size:'.esc_js($desc_fontsize).';line-height:'.esc_js($desc_lineheight).';color:'.esc_js($desc_color).';'.esc_js($desc_style).'}';
				}
				$pin_point .='</ul>';
				}
			$pin_point .='</div>';
			
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .=$css_loop;
			$css_rule .= '</style>';
			$css_rule.= '<script>( function ( $ ) {"use strict";$(window).load(function () {$(".pin-point-trigger").each(function () {var tooltip_direction=$(this).data("tooltip_direction") || "top",	tooltip_style=$(this).data("tooltip_style") || "top", e = $(this).data("pin_trigger") || "hover", d = $(this).data("tooltip_animation");
			
				$(this).on("click", function(b) {
                ("" == $(this).attr("href") || "#" == $(this).attr("href")) && b.preventDefault()
            });
				$(this).tooltipster({ theme: "tooltipster-" + tooltip_style,   delay: 150,  maxWidth: 200,  speed: 400,  interactive: true,  trigger: e,  position: tooltip_direction, animation: d,contentAsHTML: !0,	});
			});	});	} ( jQuery ) )</script>';
			return $css_rule.$pin_point;
		}
		function init_tp_pin_point(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Pin Point", "pt_theplus"),
					"base" => "tp_pin_point",
					"icon" => "tp-pin-to-point",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Highlight Your Area', 'pt_theplus'),
					"params" => array(
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose the Image Source from below options.','pt_theplus').'</span></span>'.esc_html__('Image Source', 'pt_theplus')),
							"param_name" => "image_source",
							"value" => array(
								esc_html__('Media library', 'pt_theplus') => 'media_library',
								esc_html__('External link', 'pt_theplus') => 'externals_link',
							),
							'std' => 'media_library',
							"description" => '',
							),	
						array(
							"type" => "attach_image",
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose Main Background image for all pins from this option.','pt_theplus').'</span></span>'.esc_html__('Main Background Image', 'pt_theplus')), 
							"param_name" => "bg_image",
							"value" => "",
							'dependency' => array(
								'element' => 'image_source',
								'value' => 'media_library'
							),
							"description" => ""
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select external link.','pt_theplus').'</span></span>'.esc_html__('External Image', 'pt_theplus')),
							'param_name' => 'external_img',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'image_source',
								'value' => array( 'externals_link')
							),
						),
						array(
									"type" => "dropdown",
									'heading' => esc_html__('Tooltip Style', 'pt_theplus'),
									"param_name" => "tooltip_style",
									'admin_label' => true,
									"value" => array(
										__("Default Style", "pt_theplus") => "default",
										__("Light", "pt_theplus") => "light",
										__("BorderLess", "pt_theplus") => "borderless",
										__("Noir", "pt_theplus") => "noir",
										__("Shadow", "pt_theplus") => "shadow",
										__("Custom", "pt_theplus") => "custom",
									),
									"description" => "",
									"std" => 'default'
						),
						 array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for tooltip background color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
									"description" => "",
									'param_name' => 'tooltip_bg_color',
									'value' => '#fff'
						 ),
						array(
									"type" => "dropdown",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Tooltip Animation from given design options.','pt_theplus').'</span></span>'.esc_html__('Tooltip Animation', 'pt_theplus')),
									"param_name" => "tooltip_animation",
									"value" => array(
										__("Fade", "pt_theplus") => "fade",
										__("Grow", "pt_theplus") => "grow",
										__("Swing", "pt_theplus") => "swing",
										__("Slide", "pt_theplus") => "slide",
										__("Fall", "pt_theplus") => "fall"
									),
									"description" => "",
									"std" => 'fade'
						),
						array(
									"type" => "dropdown",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select the way tooltip will open using Pin.','pt_theplus').'</span></span>'.esc_html__('Pin Trigger', 'pt_theplus')),
									"param_name" => "pin_trigger",
									"value" => array(
										__("Hover", "pt_theplus") => "hover",
										__("Click", "pt_theplus") => "click",
									),
									"description" => "",
									"std" => 'hover'
						),
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Add Layer for Modern Image Effect', 'pt_theplus'),
							'param_name' => 'pin_point_loop',
							'params' => array(
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set positive or Negative Pin&#39;s Position from X-Axis. e.g. 10, 40 etc','pt_theplus').'</span></span>'.esc_html__('X Position', 'pt_theplus')), 
									'param_name' => 'xposition',
									'admin_label' => true,
									'value' => '45'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set positive or Negative Pin&#39;s Position from Y-Axis. e.g. 10, 40 etc.','pt_theplus').'</span></span>'.esc_html__('Y Position', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									"type" => "dropdown",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Tooltip style from given design options.','pt_theplus').'</span></span>'.esc_html__('Tooltip Direction', 'pt_theplus')), 
									"param_name" => "tooltip_direction",
									 'edit_field_class' => 'vc_col-xs-4',
									"value" => array(
										__("Left", "pt_theplus") => "left",
										__("Right", "pt_theplus") => "right",
										__("Top", "pt_theplus") => "top",
										__("Bottom", "pt_theplus") => "bottom"
									),
									"description" => "",
									"std" => 'top'
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Title Text for Pin using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Title', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'pin_title',
									'admin_label' => true,
									'value' => ''
								),
								array(
									"type" => "textarea",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Title Text for Pin Description using this option.','pt_theplus').'</span></span>'.esc_html__('Content Description', 'pt_theplus')), 
									"param_name" => "pin_desc_text",
									"value" => "",
									"description" => ""
								),
								array(
									"type" => "textarea_raw_html",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Title Text for Pin Description using this option.','pt_theplus').'</span></span>'.esc_html__('Content Embeded Code', 'pt_theplus')), 
									"param_name" => "pin_desc",
									"value" => "",
									"description" => ""
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Pin Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Style', 'pt_theplus')),
									'param_name' => 'pin_style',
									'value' => array(
										__('Style-1', 'pt_theplus') => 'style-1',
										__('Style-2', 'pt_theplus') => 'style-2',
										__('Style-3', 'pt_theplus') => 'style-3',
									),
									'std' => 'style-1',
									'admin_label' => false
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select pin design using this options','pt_theplus').'</span></span>'.esc_html__('Pin Options', 'pt_theplus')),
									'param_name' => 'options',
									'value' => array(
										__('Default Icon', 'pt_theplus') => '',
										__('Pre built icons', 'pt_theplus') => 'icons',
										__('Custom Image', 'pt_theplus') => 'image'
									),
									'std' => '',
									'admin_label' => false
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
									'value' => array(
										__('Select Icon', 'pt_theplus') => '',
										__('Font Awesome', 'pt_theplus') => 'fontawesome',
										__('Open Iconic', 'pt_theplus') => 'openiconic',
										__('Typicons', 'pt_theplus') => 'typicons',
										__('Entypo', 'pt_theplus') => 'entypo',
										__('Linecons', 'pt_theplus') => 'linecons',
										__('Mono Social', 'pt_theplus') => 'monosocial'
									),
									'admin_label' => true,
									'std' => 'fontawesome',
									'param_name' => 'fonts_icon',
									'description' => "",
									'dependency' => array(
										'element' => 'options',
										'value' => array(
											'icons'
										)
									)
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_fontawesome',
									'value' => 'fa fa-arrow-right', // default value to backend editor admin_label
									'settings' => array(
										'emptyIcon' => false,
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'fontawesome'
									),
									
									'description' => ""
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_openiconic',
									'value' => 'vc-oi vc-oi-dial',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'openiconic',
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'openiconic'
									),
									
									'description' => ""
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_typicons',
									'value' => 'typcn typcn-adjust-brightness',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'typicons',
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'typicons'
									),
									
									'description' => ""
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_entypo',
									'value' => 'entypo-icon entypo-icon-note',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'entypo',
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'entypo'
									)
									
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_linecons',
									'value' => 'vc_li vc_li-heart',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'linecons',
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'linecons'
									),
									
									'description' => ""
								),
								array(
									'type' => 'iconpicker',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_monosocial',
									'value' => 'vc-mono vc-mono-fivehundredpx',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'monosocial',
										'iconsPerPage' => 4000
									),
									'dependency' => array(
										'element' => 'fonts_icon',
										'value' => 'monosocial'
									),
									'description' => ""
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Pin color using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Color', 'pt_theplus')),
									'param_name' => 'pin_icon_color',
									'edit_field_class' => 'vc_col-xs-6',
									'value' => '#fff',
									 'dependency' => array(
										'element' => 'options',
										'value' => array(
											'','icons'
										)
									),
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for pin hover icon color using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Hover Icon Color', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-6',
									'param_name' => 'pin_hover_icon_color',
									'value' => '#fff',
									 'dependency' => array(
										'element' => 'options',
										'value' => array(
											'','icons'
										)
									),
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for pin background color using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Background Color', 'pt_theplus')),
									'param_name' => 'pin_bg_color',
									'edit_field_class' => 'vc_col-xs-6',
									'value' => '#313131',
									 'dependency' => array(
										'element' => 'options',
										'value' => array(
											'','icons'
										)
									),
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for pin hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Hover Background Color', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-6',
									'param_name' => 'pin_hover_bg_color',
									'value' => '#ff214f',
									 'dependency' => array(
										'element' => 'options',
										'value' => array(
											'','icons'
										)
									),
								),
								 array(
									"type" => "attach_image",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Pin Icon using this option. .jpg, .png, .gif images supported.','pt_theplus').'</span></span>'.esc_html__('Pin Icon', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-6',
									"param_name" => "pin_icon",
									"value" => "",
									'dependency' => array(
										'element' => 'options',
										'value' => array(
											'image'
										)
									),
									"description" => ""
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Icon size from given design options.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
									'param_name' => 'icon_size',
									'value' => array(
										__('Small', 'pt_theplus') => 'small',
										__('Medium', 'pt_theplus') => 'medium',
										__('Large', 'pt_theplus') => 'large'
									),
									'std' => 'medium',
									'edit_field_class' => 'vc_col-xs-6',
								),
								
							   
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Effect of Pin which will be continuous.','pt_theplus').'</span></span>'.esc_html__('Pin Effect', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-6',
									'param_name' => 'effects',
									'value' => array(
										__('Select Effects', 'pt_theplus') => '',
										__('Pulse', 'pt_theplus') => 'pin-pulse',
										__('Floating', 'pt_theplus') => 'pin-floating',
										__('Tossing', 'pt_theplus') => 'pin-tossing',
										__('Rotating', 'pt_theplus') => 'pin-rotating',
										
									),
									
								)
							)
							
						
						),
						 array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Title Font Size', 'pt_theplus')),
									'param_name' => 'title_fontsize',
									'value' => '20px'
						),
						 array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Title Line Height', 'pt_theplus')), 
									'param_name' => 'title_lineheight',
									'value' => '24px'
						 ),
						  array(
									'type' => 'colorpicker',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'title_color',
									'value' => '#777'
						 ),
					)
				));

			}
		}
	}
	new ThePlus_pin_point;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_pin_point'))
	{
		class WPBakeryShortCode_tp_pin_point extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}