<?php 
// Toggle Elements
if(!class_exists("ThePlus_toggle")){
	class ThePlus_toggle{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_toggle') );
			add_shortcode( 'tp_toggle',array($this,'tp_toggle_shortcode'));
		}
		function tp_toggle_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				"style" => 'style-1',
				'btn_hover_style'=>'hover-left',
				'icon_hover_style'=>'hover-top',
				'btn_padding'=>'15px 30px',
				'btn_width'=>'250px',
				'btn_height'=>'50px',
				"btn_text" => 'The Plus',
				'btn_hover_text'=>'',
				"btn_icon" => 'fontawesome',
			  'icon_fontawesome'=>'fa fa-arrow-right',
			   'icon_openiconic'=> 'vc-oi vc-oi-dial',
				 'icon_typicons'=> 'typcn typcn-adjust-brightness',
			   'icon_entypo'=> 'entypo-icon entypo-icon-note',    
				 'icon_linecons'=> 'vc_li vc_li-heart',
				 'icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
				"before_after" => 'after',
				"btn_url" => '',
				'btn_align' =>'text-left',
				'select_bg_option'=>'normal',
				'normal_bg_color'=>'#252525',
				'gradient_color1'=>'#1e73be',
				'gradient_color2'=>'#2fcbce',
				'gradient_style'=>'horizontal',
				'bg_image'=>'',
				//'gradient_opacity'=>'1',
				'select_bg_hover_option'=>'normal',
				
				'normal_bg_hover_color'=>'#ff214f',
				'normal_bg_hover_color1'=>'#d3d3d3',
				'gradient_hover_color1'=>'#2fcbce',
				'gradient_hover_color2'=>'#1e73be',
				'gradient_hover_style'=>'horizontal',
				'bg_hover_image'=>'',
				
				'font_size'=>'20px',
				'line_height'=>'25px',
				'letter_spacing'=>'1px',
				'btn_use_theme_fonts'=>'custom-font-family',
				'btn_font_family'=>'',
				'btn_font_weight'=>'400',
				'btn_google_fonts'=>'',
				
				'text_color'=>'#8a8a8a',
				'text_hover_color'=>'#252525',
				'border_color'=>'#252525',
				'border_hover_color'=>'#252525',
				'border_radius'=>'30px',
				
				'full_width_btn'=>'',
				'hover_shadow'=>'',
				'transition_hover' =>'',	  
				'todggle_open' =>'off',
				'animation_effects'=>'no-animation',
				'animation_delay'=>'50',
				'el_class'=>'',
					
			   ), $atts ) );
				$uid = uniqid('service_toggle');
					$toggle_on='';
					
					if($todggle_open !='on'){
						$toggle_on= ' style="display:none;" ';
					}
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
			$rand_no=rand(1000000, 1500000);
			$data_class=$data_attr=$a_href=$a_title=$a_target=$a_rel=$style_content=$icons_before=$icons_after=$button_text=$button_hover_text=$gradient_color=$gradient_hover_color='';



				if($btn_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $btn_google_fonts );
				$btn_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($btn_use_theme_fonts=='custom-font-family'){
				$btn_font_family='font-family:'.$btn_font_family.';font-weight:'.$btn_font_weight.';';
			}else{
				$btn_font_family='';
			}

				$data_class=' button-'.$rand_no.' ';
				$data_class .=' button-'.$style.' ';
				
				if($full_width_btn=='yes'){
					$data_class .=' full-button ';
				}
				if($transition_hover=='yes'){
					$data_class .=' trnasition_hover ';
				}
			
				if($select_bg_option=='normal'){
					$bg_color = $normal_bg_color;
				}else if($select_bg_option=='gradient'){
					$gradient_color = pt_theplus_gradient_color($gradient_color1,$gradient_color2,$gradient_style);
				$bg_color = $gradient_color;
				}else if($select_bg_option=='image'){
					if(isset($bg_image) && !empty($bg_image)){
						$img = wp_get_attachment_image_src($bg_image, "full");
						$imgSrc = $img[0];
						$bg_color='url('.esc_url($imgSrc).')';
					}
				}else{
					$bg_color = '';
				}
				if($select_bg_hover_option=='normal'){
					$bg_hover_color = $normal_bg_hover_color;
				}else if($select_bg_hover_option=='gradient'){
					$gradient_hover_color = pt_theplus_gradient_color($gradient_hover_color1,$gradient_hover_color2,$gradient_hover_style);
					$bg_hover_color = $gradient_hover_color;
				}else if($select_bg_hover_option=='image'){
					if(isset($bg_hover_image) && !empty($bg_hover_image)){
						$img = wp_get_attachment_image_src($bg_hover_image, "full");
						$imgSrc = $img[0];
						$bg_hover_color= 'url('.esc_url($imgSrc).')';
					}
				}else{
						$bg_hover_color='';
				}
				
					$btn_url = ( '||' === $btn_url ) ? '' : $btn_url;
					$btn_url_a= vc_build_link( $btn_url);
					

					$a_title = $btn_url_a['title'];
					$a_target = $btn_url_a['target'];
					$a_rel = $btn_url_a['rel'];
					if ( ! empty( $a_rel ) ) {
						$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
					}
				
				if(!empty($btn_icon)){
			  vc_icon_element_fonts_enqueue( $btn_icon );
			  $icon_class = isset( ${'icon_' . $btn_icon} ) ? esc_attr( ${'icon_' . $btn_icon} ) : 'fa fa-arrow-right';
			  
			  if($before_after=='before'){
			   $icons_before = '<i class="btn-icon button-'.esc_attr($before_after).' '.esc_attr($icon_class).'"></i>';
			  }else{
			   $icons_after = '<i class="btn-icon button-'.esc_attr($before_after).' '.esc_attr($icon_class).'"></i>';
			  }
			 }
				if($style=='style-1'){
					$button_text =$icons_before.$btn_text . $icons_after;
					$style_content='<div class="button_line"></div>';
				}
				if($style=='style-2' || $style=='style-5' || $style=='style-8' || $style=='style-10'){
					$button_text =$icons_before . $btn_text . $icons_after;
				}
				if($style=='style-3'){
					$button_text =$btn_text.'<svg class="arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg><svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg>';
				}
				if($style=='style-4'){
					$button_text =$icons_before.$btn_text . $icons_after;
					if(!empty($btn_hover_text)){
						$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
					}else{
						$button_hover_text =' data-hover="'.esc_attr($btn_text).'" ';
					}
				}
				if($style=='style-6'){
					$button_text =$btn_text;
				}
				if($style=='style-7'){
					$button_text =$btn_text.'<span class="btn-arrow"></span>';
				}
				if($style=='style-9'){
					$button_text =$btn_text.'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
				}
				if($style=='style-11'){
					$button_text ='<span>'.$icons_before . $btn_text . $icons_after.'</span>';
					if(!empty($btn_hover_text)){
						$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
					}else{
						$button_hover_text =' data-hover="'.esc_attr($btn_text).'" ';
					}
					$data_class .=' '.esc_attr($btn_hover_style).' ';
				}
				if($style=='style-12' || $style=='style-15' || $style=='style-16'){
					$button_text ='<span>'.$icons_before . $btn_text . $icons_after.'</span>';
				}
				if($style=='style-13'){
					$button_text ='<span>'.$icons_before . $btn_text . $icons_after.'</span>';
					$data_class .=' '.$btn_hover_style.' ';
				}
				if($style=='style-14'){
					$button_text ='<span>'.$icons_before . $btn_text . $icons_after.'</span>';
					if(!empty($btn_hover_text)){
						$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
					}else{
						$button_hover_text =' data-hover="'.esc_attr($btn_text).'" ';
					}
				}
				if($style=='style-17'){
					$icons_before=$icons_after;
					$button_text =$icons_before .'<span>'. esc_html($btn_text) .'</span>';
					$data_class .=' '.$icon_hover_style.' ';
				}
				if($style=='style-18' || $style=='style-19' || $style=='style-20' || $style=='style-21' || $style=='style-22'){
					$button_text =$icons_before .'<span>'. esc_html($btn_text) .'</span>'. $icons_after;
				}
				
				if($style=='style-23'){
					$button_text ='<span><div class="align-center">'. $icons_before . $btn_text . $icons_after .'</div></span>';
					if(!empty($btn_hover_text)){
						$button_text .='<span><div class="align-center">'. $icons_before . $btn_hover_text . $icons_after .'</div></span>';
					}else{
						$button_text .='<span><div class="align-center">'. $icons_before . $btn_text . $icons_after .'</div></span>';
					}
					$data_class .=' '.esc_attr($btn_hover_style).' ';
				}

				
				$the_button ='<div class="'.esc_attr($btn_align).' ts-button">';
					$the_button .='<div class="pt_plus_button '.$data_class.' " '.$data_attr.'>';
						$the_button .='<a class="button-link-wrap button-toggle-link"'.$button_hover_text.'>';
							$the_button .=$button_text;
							$the_button .=$style_content;
						$the_button .='</a>';
					$the_button .='</div>';
				$the_button .='</div>';		
				

				
						$service_toggle ='<div class="pt-plus-toggle '.esc_attr($uid).' '.esc_attr($animated_class).' '.esc_attr($el_class).' " data-uid="'.esc_attr($uid).'" '.$data_attr.'  data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
							$service_toggle .='<div class="">';
								$service_toggle .='<div class="toggle_inner_wrap">';
									$service_toggle .='<div class="'.esc_attr($uid).' pt-plus-toggle-click ">'.$the_button.'</div>' ;
								$service_toggle .= '</div>';	
								$service_toggle .='<div id="'.esc_attr($uid).'" class="toggle-class " '.$toggle_on.'>';
									$service_toggle .= do_shortcode($content);
								$service_toggle .= '</div>';
							
							$service_toggle .= '</div>';	
						$service_toggle .= '</div>';
				$css_rule = '';	
				$css_rule .= '<style >';
				$css_rule .= include THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/button_css.php';
				$css_rule .= '</style>';
			return $css_rule.$service_toggle;
		}
		function init_tp_toggle(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
						"name" => __('View More Toggle', 'pt_theplus'),
						'base' => "tp_toggle",
						'icon' => "tp-toggle",
						"description" => esc_html__('Hide/Show any content', 'pt_theplus'),
						'as_parent' => array(
							'except' => 'vc_gmaps'
						),
						'content_element' => true,
						'controls' => 'full',
						"category" => __("The Plus", "pt_theplus"),
						'show_settings_on_create' => true,
						'params' => array(
							array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Button Styles using this option','pt_theplus').'</span></span>'.esc_html__('Button Style', 'pt_theplus')), 
								'param_name'  => 'style',
								'simple_mode' => false,
								'value'  => 'style_1',
								'options'     => array(
								 'style-1' => array(
								  'tooltip' => esc_attr__('Style 1','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-1.png'
								 ),
								 'style-2' => array(
								  'tooltip' => esc_attr__('Style 2','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-2.png'
								 ),
								 'style-3' => array(
								  'tooltip' => esc_attr__('Style 3','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-3.png'
								 ),
								 'style-4' => array(
								  'tooltip' => esc_attr__('Style 4','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-4.png'
								 ),
								 'style-5' => array(
								  'tooltip' => esc_attr__('Style 5','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-5.png'
								 ),
								 'style-6' => array(
								  'tooltip' => esc_attr__('Style 6','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-6.png'
								 ),
								 'style-7' => array(
								  'tooltip' => esc_attr__('Style 7','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-7.png'
								 ),
								 'style-8' => array(
								  'tooltip' => esc_attr__('Style 8','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-8.png'
								 ),
								 'style-9' => array(
								  'tooltip' => esc_attr__('Style 9','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-9.png'
								 ),
								 'style-10' => array(
								  'tooltip' => esc_attr__('Style 10','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-10.png'
								 ),
								 'style-11' => array(
								  'tooltip' => esc_attr__('Style 11','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-11.png'
								 ),
								 'style-12' => array(
								  'tooltip' => esc_attr__('Style 12','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-12.png'
								 ),
								 'style-13' => array(
								  'tooltip' => esc_attr__('Style 13','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-13.png'
								 ),
								 'style-14' => array(
								  'tooltip' => esc_attr__('Style 14','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-14.png'
								 ),
								 'style-15' => array(
								  'tooltip' => esc_attr__('Style 15','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-15.png'
								 ),
								 'style-16' => array(
								  'tooltip' => esc_attr__('Style 16','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-16.png'
								 ),
								 'style-17' => array(
								  'tooltip' => esc_attr__('Style 17','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-17.png'
								 ),
								 'style-18' => array(
								  'tooltip' => esc_attr__('Style 18','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-18.png'
								 ),
								 'style-19' => array(
								  'tooltip' => esc_attr__('Style 19','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-19.png'
								 ),
								 'style-20' => array(
								  'tooltip' => esc_attr__('Style 20','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-20.png'
								 ),
								 'style-21' => array(
								  'tooltip' => esc_attr__('Style 21','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-21.png'
								 ),
								 'style-22' => array(
								  'tooltip' => esc_attr__('Style 22','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-22.png'
								 ),
								 'style-23' => array(
								  'tooltip' => esc_attr__('Style 23','pt_theplus'),
								  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/button/button-23.png'
								 ),
								),
							),
							array(
								"type" => "dropdown",
								"heading" => __("Hover Style", "pt_theplus"),
								"param_name" => "btn_hover_style",
								"value" => array(
									__("On Left", "pt_theplus") => "hover-left",
									__("On Right", "pt_theplus") => "hover-right",
									__("On Top", "pt_theplus") => "hover-top",
									__("On Bottom", "pt_theplus") => "hover-bottom"
								),
								"description" => "",
								"std" => 'hover-left',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-11',
										'style-13',
										'style-23'
									)
								)
							),
							array(
								"type" => "dropdown",
								"heading" => __("Hover Style", "pt_theplus"),
								"param_name" => "icon_hover_style",
								"value" => array(
									__("On Top", "pt_theplus") => "hover-top",
									__("On Bottom", "pt_theplus") => "hover-bottom"
								),
								"description" => "",
								"std" => 'hover-top',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-17'
									)
								)
							),
							array(
								"type" => "textfield",
								"heading" => esc_html__("Button Width", 'pt_theplus'),
								"param_name" => "btn_width",
								"value" => '250px',
								'description' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-23'
									)
								),
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "textfield",
								"heading" => esc_html__("Button Height", 'pt_theplus'),
								"param_name" => "btn_height",
								"value" => '50px',
								'description' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-23'
									)
								),
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Text', 'pt_theplus')), 
								"param_name" => "btn_text",
								"value" => 'The Plus',
								'description' => '',
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write on hover  title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Hover Text', 'pt_theplus')),
								"param_name" => "btn_hover_text",
								"value" => '',
								'description' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-11',
										'style-14',
										'style-23'
									)
								)
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Setup Inner Padding top-bottom and right-left to Button from this option. E.g. 15px 20px, 30px 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Inner Padding ', 'pt_theplus')),
								"param_name" => "btn_padding",
								"value" => '15px 30px',
								'description' => '',
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
									__('Mono Social', 'pt_theplus') => 'monosocial'
								),
								'std' => 'fontawesome',
								'param_name' => 'btn_icon',
								'description' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-1',
										'style-2',
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_fontawesome',
								'value' => 'fa fa-arrow-right',
								'settings' => array(
									'emptyIcon' => false,
									'iconsPerPage' => 4000
								),
								'dependency' => array(
									'element' => 'btn_icon',
									'value' => 'fontawesome'
								),
								
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
									'iconsPerPage' => 4000
								),
								'dependency' => array(
									'element' => 'btn_icon',
									'value' => 'openiconic'
								),
								
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
									'iconsPerPage' => 4000
								),
								'dependency' => array(
									'element' => 'btn_icon',
									'value' => 'typicons'
								),
								
								'description' => '',
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
									'element' => 'btn_icon',
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
									'element' => 'btn_icon',
									'value' => 'linecons'
								),
								
								'description' => '',
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
									'element' => 'btn_icon',
									'value' => 'monosocial'
								),
								'description' => '',
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Position of Icon before or after content from this option.','pt_theplus').'</span></span>'.esc_html__('Icon Position', 'pt_theplus')),
								"param_name" => "before_after",
								"value" => array(
									__("After", "pt_theplus") => "after",
									__("Before", "pt_theplus") => "before"
								),
								"description" => "",
								"std" => 'after',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-1',
										'style-2',
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'vc_link',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('dd Button URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Button URL', 'pt_theplus')),
								'param_name' => 'btn_url',
								'description' => '',
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Button Text Style', 'pt_theplus'),
								'param_name' => 'text_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
								"param_name" => "font_size",
								"value" => '20px',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "line_height",
								"value" => '25px',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "letter_spacing",
								"value" => '1px',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								"group" => esc_attr__('Style', 'pt_theplus')
							),		
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button text color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
								'param_name' => 'text_color',
								"value" => '#8a8a8a',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button hover text color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Hover Color', 'pt_theplus')),
								'param_name' => 'text_hover_color',
								"value" => '#252525',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Button Custom font family', 'pt_theplus'),
									'param_name' => 'btn_use_theme_fonts',
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
								'param_name' => 'btn_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'btn_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'btn_font_weight',
								'value' => __('400','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'btn_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'btn_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'btn_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Border Style', 'pt_theplus'),
								'param_name' => 'border_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-1',
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button border color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
								'param_name' => 'border_color',
								"value" => '#252525',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-1',
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button border hover color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
								'param_name' => 'border_hover_color',
								"value" => '#252525',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose radius for border using this option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Border Radius', 'pt_theplus')),
								"param_name" => "border_radius",
								"value" => "30px",
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-10',
										'style-11',
										'style-14',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22'
									)
								)
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Background Style', 'pt_theplus'),
								'param_name' => 'background_style_heading',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								"type" => "dropdown",
								"heading" => __("Select Background Option", "pt_theplus"),
								"param_name" => "select_bg_option",
								"value" => array(
									__("Normal color", "pt_theplus") => "normal",
									__("Gradient color", "pt_theplus") => "gradient",
									__("Background Image", "pt_theplus") => "image"
								),
								"description" => "",
								"std" => 'normal',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-18',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('color', 'pt_theplus'),
								'param_name' => 'normal_bg_color',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"value" => '#252525',
								'dependency' => array(
									'element' => 'select_bg_option',
									'value' => 'normal'
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('First Color', 'pt_theplus'),
								'param_name' => 'gradient_color1',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#1e73be'
								
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Second Color', 'pt_theplus'),
								'param_name' => 'gradient_color2',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#2fcbce'
								
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
								'param_name' => 'gradient_style',
								'value' => array(
									__('Horizontal', 'pt_theplus') => 'horizontal',
									__('Vertical', 'pt_theplus') => 'vertical',
									__('Diagonal', 'pt_theplus') => 'diagonal',
									__('Radial', 'pt_theplus') => 'radial'
								),
								'std' => 'horizontal',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_option',
									'value' => 'gradient'
								)
							),
							array(
								'type' => 'attach_image',
								'heading' => __('Background Image', 'pt_theplus'),
								'param_name' => 'bg_image',
								'value' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_option',
									'value' => 'image'
								)
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Background Hover Style', 'pt_theplus'),
								'param_name' => 'background_style_hover_heading',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										 'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								"type" => "dropdown",
								"heading" => __("Background Option", "pt_theplus"),
								"param_name" => "select_bg_hover_option",
								"value" => array(
									__("Normal color", "pt_theplus") => "normal",
									__("Gradient color", "pt_theplus") => "gradient",
									__("Background Image", "pt_theplus") => "image"
								),
								"description" => "",
								"std" => 'normal',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Background color', 'pt_theplus'),
								'param_name' => 'normal_bg_hover_color',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"value" => '#ff214f',
								'dependency' => array(
									'element' => 'select_bg_hover_option',
									'value' => 'normal'
								)
							),
							
							array(
								'type' => 'colorpicker',
								'heading' => __('Color 1', 'pt_theplus'),
								'param_name' => 'gradient_hover_color1',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_hover_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#1e73be'
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Color 2', 'pt_theplus'),
								'param_name' => 'gradient_hover_color2',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_hover_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#2fcbce'
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
								'param_name' => 'gradient_hover_style',
								'value' => array(
									__('Horizontal', 'pt_theplus') => 'horizontal',
									__('Vertical', 'pt_theplus') => 'vertical',
									__('Diagonal', 'pt_theplus') => 'diagonal',
									__('Radial', 'pt_theplus') => 'radial'
								),
								'std' => 'horizontal',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_hover_option',
									'value' => 'gradient'
								)
							),
							array(
								'type' => 'attach_image',
								'heading' => __('Background Image', 'pt_theplus'),
								'param_name' => 'bg_hover_image',
								'value' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_bg_hover_option',
									'value' => 'image'
								)
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Hover Button Shadow', 'pt_theplus'),
								'param_name' => 'btn_hover_shadow',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" => esc_html__("Hover Button Shadow", 'pt_theplus'),
								"param_name" => "hover_shadow",
								"value" => '',
								'description' => '',
								"edit_field_class" => "vc_col-xs-6",
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								'type' => 'checkbox',
								'heading' => __('Full Width Button', 'pt_theplus'),
								'param_name' => 'full_width_btn',
								'value' => array(
									__('Yes', 'pt_theplus') => 'yes'
								),
								'description' => '',
								'std' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-4',
										'style-5',
										'style-8',
										'style-10',
										'style-11',
										'style-12',
										'style-13',
										'style-14',
										'style-15',
										'style-16',
										'style-17',
										'style-18',
										'style-19',
										'style-20',
										'style-21',
										'style-22',
										'style-23'
									)
								)
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose button alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Alignment', 'pt_theplus')), 
								'param_name' => 'btn_align',
								'value' => array(
									__('Left', 'pt_theplus') => 'text-left',
									__('Center', 'pt_theplus') => 'text-center',
									__('Right', 'pt_theplus') => 'text-right'
								),
								'std' => 'text-left',
								"description" => ""
							),
												array(
												   'type' => 'pt_theplus_checkbox',
												   'class' => '',
												   'heading' => __('Open on load ', 'pt_theplus'),
												   'param_name' => 'todggle_open',
												   'description' => "",
												   'value' => 'off',
												   'options' => array(
													'on' => array(
													  'label' => '',
													  'on' => 'Yes',
													  'off' => 'No',
													 ),
													),
													
												  ),
												  array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Animation Settings', 'pt_theplus'),
							'param_name' => 'annimation_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						
											 array(
								  "type"        => "dropdown",
								  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
								  "param_name"  => "animation_effects",
								  "value"       => array(
													__( 'No-animation', 'pt_theplus' )             => 'no-animation',
									__( 'FadeIn', 'pt_theplus' )             => 'transition.fadeIn',
									__( 'FlipXIn', 'pt_theplus' )            => 'transition.flipXIn',
								   __( 'FlipYIn', 'pt_theplus' )            => 'transition.flipYIn',
								   __( 'FlipBounceXIn', 'pt_theplus' )      => 'transition.flipBounceXIn',
								   __( 'FlipBounceYIn', 'pt_theplus' )      => 'transition.flipBounceYIn',
								   __( 'SwoopIn', 'pt_theplus' )            => 'transition.swoopIn',
								   __( 'WhirlIn', 'pt_theplus' )            => 'transition.whirlIn',
								   __( 'ShrinkIn', 'pt_theplus' )           => 'transition.shrinkIn',
								   __( 'ExpandIn', 'pt_theplus' )           => 'transition.expandIn',
								   __( 'BounceIn', 'pt_theplus' )           => 'transition.bounceIn',
								   __( 'BounceUpIn', 'pt_theplus' )         => 'transition.bounceUpIn',
								   __( 'BounceDownIn', 'pt_theplus' )       => 'transition.bounceDownIn',
								   __( 'BounceLeftIn', 'pt_theplus' )       => 'transition.bounceLeftIn',
								   __( 'BounceRightIn', 'pt_theplus' )      => 'transition.bounceRightIn',
								   __( 'SlideUpIn', 'pt_theplus' )          => 'transition.slideUpIn',
								   __( 'SlideDownIn', 'pt_theplus' )        => 'transition.slideDownIn',
								   __( 'SlideLeftIn', 'pt_theplus' )        => 'transition.slideLeftIn',
								   __( 'SlideRightIn', 'pt_theplus' )       => 'transition.slideRightIn',
								   __( 'SlideUpBigIn', 'pt_theplus' )       => 'transition.slideUpBigIn',
								   __( 'SlideDownBigIn', 'pt_theplus' )     => 'transition.slideDownBigIn',
								   __( 'SlideLeftBigIn', 'pt_theplus' )     => 'transition.slideLeftBigIn',
								   __( 'SlideRightBigIn', 'pt_theplus' )    => 'transition.slideRightBigIn',
								   __( 'PerspectiveUpIn', 'pt_theplus' )    => 'transition.perspectiveUpIn',
								   __( 'PerspectiveDownIn', 'pt_theplus' )  => 'transition.perspectiveDownIn',
								   __( 'PerspectiveLeftIn', 'pt_theplus' )  => 'transition.perspectiveLeftIn',
								   __( 'PerspectiveRightIn', 'pt_theplus' ) => 'transition.perspectiveRightIn',
								  ),
								  'std' =>'no-animation',
								   "edit_field_class" => "vc_col-xs-6",
							),		
							array(
								  "type"        => "textfield",
								 "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
								  "param_name"  => "animation_delay",
								  "value"       => '50',
								   "edit_field_class" => "vc_col-xs-6",
								  "description" => "",
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
							
											),
											'js_view' => 'VcColumnView'
										)
									);
			}
		}
	}
	new ThePlus_toggle;

	if(class_exists('WPBakeryShortCodesContainer') && !class_exists('WPBakeryShortCode_tp_toggle'))
	{
		class WPBakeryShortCode_tp_toggle extends WPBakeryShortCodesContainer
    {
        protected function contentInline($atts, $content = null)
        {
        }
    }
	}
}
