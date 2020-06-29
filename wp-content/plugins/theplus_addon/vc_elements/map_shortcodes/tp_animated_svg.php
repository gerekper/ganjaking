<?php
// Animated Svg Elements
if(!class_exists("ThePlus_animated_svg")){
	class ThePlus_animated_svg{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_animated_svg') );
			add_shortcode( 'tp_animated_svg',array($this,'tp_animated_svg_shortcode'));
		}
		function tp_animated_svg_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'svg_icon'=>'svg_icons',
				'svg_d_icon'=>'app.svg',
				'svg_image'=>'',
				'type'=>'delayed',
				'duration'=>'80',
				'alignment'=>'text-center',
				'max_width'=>'100%',
				'border_stroke_color'=>'#ff0000',
				
				'el_class' =>'',
				), $atts ) );
				
				if($svg_icon == 'image'){
						$svg_attach = wp_get_attachment_image_src( $svg_image,true);
						$svg_url = $svg_attach[0];
				}else{
						$svg_url = THEPLUS_PLUGIN_URL.'vc_elements/images/svg/'.esc_attr($svg_d_icon); 
				}
					
				$uid=uniqid('svg');
				$animate_svg='';
				if($svg_url!=''){
					if(!empty($border_stroke_color)){
						$border_stroke_color=$border_stroke_color;
					}else{
						$border_stroke_color='none';
					}
					$animate_svg ='<div class="pt_plus_animated_svg-an '.$el_class.'">';
						$animate_svg .='<div class="pt_plus_animated_svg '.esc_attr($alignment).' '.esc_attr($uid).' " style="max-height:'.esc_attr($max_width).';" data-id="'.esc_attr($uid).'" data-type="'.esc_attr($type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
							$animate_svg .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';max-height:'.esc_attr($max_width).';">';
								$animate_svg .='<object id="'.esc_attr($uid).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
							$animate_svg .='</div>';
						$animate_svg .='</div>';
					$animate_svg .='</div>';
				}
				return $animate_svg;
		}
		function init_tp_animated_svg(){
			if(function_exists("vc_map"))
			{
				
				vc_map(array(
					"name" => esc_html__("Animated SVG", 'pt_theplus'),
					"base" => "tp_animated_svg",
					"icon" => "tp-animated-svg",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Draw your SVG', 'pt_theplus'),
					"params" => array(
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Icon Type', 'pt_theplus')),
							"param_name" => "svg_icon",
							"value" => array(
								esc_html__('Pre Built SVG Icon', 'pt_theplus') => 'svg_icons',
								esc_html__('Custom Upload', 'pt_theplus') => 'image'
							),
							'admin_label' => false,
							"std" => "svg"
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose our tested drawable SVGs from this list.','pt_theplus').'</span></span>'.esc_html__('Pre Built SVG icon', 'pt_theplus')), 
							'param_name' => 'svg_d_icon',
							"value" => array(
								esc_html__("1. App", "pt_theplus") => "app.svg",
								esc_html__("2. Arrow", "pt_theplus") => "arrow.svg",
								esc_html__("3. Art", "pt_theplus") => "art.svg",
								esc_html__("4. Banknote", "pt_theplus") => "banknote.svg",
								esc_html__("5. Building", "pt_theplus") => "building.svg",
								esc_html__("6. Bulb-idea", "pt_theplus") => "bulb-idea.svg",
								esc_html__("7. Calendar", "pt_theplus") => "calendar.svg",
								esc_html__("8. Call", "pt_theplus") => "call.svg",
								esc_html__("9. Camera", "pt_theplus") => "camera.svg",
								esc_html__("10. Cart", "pt_theplus") => "cart.svg",
								esc_html__("11. Cd", "pt_theplus") => "cd.svg",
								esc_html__("12. Clip", "pt_theplus") => "clip.svg",
								esc_html__("13. Clock", "pt_theplus") => "clock.svg",
								esc_html__("14. Cloud", "pt_theplus") => "cloud.svg",
								esc_html__("15. Comment", "pt_theplus") => "comment.svg",
								esc_html__("16. Content-board", "pt_theplus") => "content-board.svg",
								esc_html__("17. Cup", "pt_theplus") => "cup.svg",
								esc_html__("18. Diamond", "pt_theplus") => "diamond.svg",
								esc_html__("19. Earth", "pt_theplus") => "earth.svg",
								esc_html__("20. Eye", "pt_theplus") => "eye.svg",
								esc_html__("21. Finger", "pt_theplus") => "finger.svg",
								esc_html__("22. Fingerprint", "pt_theplus") => "fingerprint.svg",
								esc_html__("23. Food", "pt_theplus") => "food.svg",
								esc_html__("24. Foundation", "pt_theplus") => "foundation.svg",
								esc_html__("25. Gear", "pt_theplus") => "gear.svg",
								esc_html__("26. Graphics-design", "pt_theplus") => "graphics-design.svg",
								esc_html__("27. Handshakeandshake", "pt_theplus") => "handshake.svg",
								esc_html__("28. Hard-disk", "pt_theplus") => "hard-disk.svg",
								esc_html__("29. Heart", "pt_theplus") => "heart.svg",
								esc_html__("30. Hook", "pt_theplus") => "hook.svg",
								esc_html__("31. Image", "pt_theplus") => "image.svg",
								esc_html__("32. Key", "pt_theplus") => "key.svg",
								esc_html__("33. Laptop", "pt_theplus") => "laptop.svg",
								esc_html__("34. Layers", "pt_theplus") => "layers.svg",
								esc_html__("35. List", "pt_theplus") => "list.svg",
								esc_html__("36. Location", "pt_theplus") => "location.svg",
								esc_html__("37. Loudspeaker", "pt_theplus") => "loudspeaker.svg",
								esc_html__("38. Mail", "pt_theplus") => "mail.svg",
								esc_html__("39. Map", "pt_theplus") => "map.svg",
								esc_html__("40. Mic", "pt_theplus") => "mic.svg",
								esc_html__("41. Mind", "pt_theplus") => "mind.svg",
								esc_html__("42. Mobile", "pt_theplus") => "mobile.svg",
								esc_html__("43. Mobile-comment", "pt_theplus") => "mobile-comment.svg",
								esc_html__("44. Music", "pt_theplus") => "music.svg",
								esc_html__("45. News", "pt_theplus") => "news.svg",
								esc_html__("46. Note", "pt_theplus") => "note.svg",
								esc_html__("47. Offer", "pt_theplus") => "offer.svg",
								esc_html__("48. Paperplane", "pt_theplus") => "paperplane.svg",
								esc_html__("49. Pendrive", "pt_theplus") => "pendrive.svg",
								esc_html__("50. Person", "pt_theplus") => "person.svg",
								esc_html__("51. Photography", "pt_theplus") => "photography.svg",
								esc_html__("52. Posisvg", "pt_theplus") => "posisvg.svg",
								esc_html__("53. Recycle", "pt_theplus") => "recycle.svg",
								esc_html__("54. Ruler", "pt_theplus") => "ruler.svg",
								esc_html__("55. Satelite", "pt_theplus") => "satelite.svg",
								esc_html__("56. Search", "pt_theplus") => "search.svg",
								esc_html__("57. Secure", "pt_theplus") => "secure.svg",
								esc_html__("58. Server", "pt_theplus") => "server.svg",
								esc_html__("59. Setting", "pt_theplus") => "setting.svg",
								esc_html__("60. Share", "pt_theplus") => "share.svg",
								esc_html__("61. Smiley", "pt_theplus") => "smiley.svg",
								esc_html__("62. Sound", "pt_theplus") => "sound.svg",
								esc_html__("63. Stack", "pt_theplus") => "stack.svg",
								esc_html__("64. Star", "pt_theplus") => "star.svg",
								esc_html__("65. Study", "pt_theplus") => "study.svg",
								esc_html__("66. Suitcase", "pt_theplus") => "suitcase.svg",
								esc_html__("67. Tag", "pt_theplus") => "tag.svg",
								esc_html__("68. Tempsvg", "pt_theplus") => "tempsvg.svg",
								esc_html__("69. Thumbsup", "pt_theplus") => "thumbsup.svg",
								esc_html__("70. Tick", "pt_theplus") => "tick.svg",
								esc_html__("71. Trash", "pt_theplus") => "trash.svg",
								esc_html__("72. Truck", "pt_theplus") => "truck.svg",
								esc_html__("73. Tv", "pt_theplus") => "tv.svg",
								esc_html__("74. User", "pt_theplus") => "user.svg",
								esc_html__("75. Video", "pt_theplus") => "video.svg",
								esc_html__("76. Video-production", "pt_theplus") => "video-production.svg",
								esc_html__("77. Wallet", "pt_theplus") => "wallet.svg"
							),
							'description' => '',
							'admin_label' => false,
							'dependency' => array(
								'element' => 'svg_icon',
								'value' => "svg_icons"
							),
							'std' => 'app.svg'
						),
						array(
							'type' => 'attach_image',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Custom SVG', 'pt_theplus')),
							'param_name' => 'svg_image',
							'value' => '',
							'description' => '',
							'admin_label' => false,
							'dependency' => array(
								'element' => 'svg_icon',
								'value' => 'image'
							)
						),
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose from different options of SVG draw animation. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('SVG Animation Type', 'pt_theplus')),
							"param_name" => "type",
							"value" => array(
								esc_html__("Delayed", "pt_theplus") => "delayed",
								esc_html__("Sync", "pt_theplus") => "sync",
								esc_html__("One-By-One", "pt_theplus") => "oneByOne",
								esc_html__("Scenario-Sync", "pt_theplus") => "scenario-sync"
							),
							"description" => "",
							"std" => 'delayed',
							'admin_label' => false,
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set SVG draw Animation Duration using this option. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Animation Duration', 'pt_theplus')),
							"param_name" => "duration",
							"value" => '80',
							'admin_label' => false,
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose alignment of SVG icon using this option.','pt_theplus').'</span></span>'.esc_html__('SVG Alignment', 'pt_theplus')),
							"param_name" => "alignment",
							"value" => array(
								esc_html__("Center", "pt_theplus") => "text-center",
								esc_html__("Left", "pt_theplus") => "text-left",
								esc_html__("Right", "pt_theplus") => "text-right"
							),
							"description" => "",
							"std" => 'text-center',
							'admin_label' => false
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can setup Maximum Width of SVG here in Percentage or in Pixels from this option.','pt_theplus').'</span></span>'.esc_html__('Maximum Width', 'pt_theplus')),
							"param_name" => "max_width",
							"value" => '100%',
							"description" => "",
							'admin_label' => false
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose SVG&#39;s Stroke in Normal Terms Border Color Using This Option.','pt_theplus').'</span></span>'.esc_html__('Stroke(Border) Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'border_stroke_color',
							"value" => '#ff0000',
							'admin_label' => false
						),
					 array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Extra Settings', 'pt_theplus'),
							'param_name' => 'extra_option',
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
	new ThePlus_animated_svg;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_animated_svg'))
	{
		class WPBakeryShortCode_tp_animated_svg extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}
