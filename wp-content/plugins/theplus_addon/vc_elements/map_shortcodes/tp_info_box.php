<?php
//Info Box Elements
if(!class_exists("ThePlus_info_box")){
	class ThePlus_info_box{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_info_box') );
			add_shortcode( 'tp_info_box',array($this,'tp_info_box_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_info_box_scripts' ), 1 );
		}
		function tp_info_box_scripts() {
			wp_register_style( 'theplus-info-box-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-info-box-style.css', false, '1.0.0' );
		}
		function tp_info_box_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					  'info_box_layout' =>'single_layout',
					  'loop_content' =>'',
					  'main_style' =>'style_1',
					  'title' => 'The Plus',
					  'title_color_o' =>'solid',
					  'title_color1' =>'#1e73be',
					  'title_color2' =>'#2fcbce',
					  'title_hover_style' =>'horizontal',
					  'title_color' =>'#ffffff',
					  'title_size' => '24px',
					  'title_line' =>'1.4',
					  'title_letter' =>'1px',
					  'title_use_theme_fonts'=>'custom-font-family',
						'title_font_family'=>'',
						'title_font_weight'=>'600',
						'title_google_fonts'=>'',
					  'title_btm_space' =>'',
					  'title_top_space' =>'',
					  'sub_title' => 'Creative Design',
					  'sub_title_color' => '#4d4d4d',
					  'sub_title_size' => '20px',
					  'sub_title_line' =>'1.4',
					  'sub_title_letter' =>'1px',
					  'subtitle_use_theme_fonts'=>'custom-font-family',
					'subtitle_font_family'=>'',
					'subtitle_font_weight'=>'400',
					'subtitle_google_fonts'=>'',
					  'sub_btm_space' =>'',					  

					  'image_icon' => 'icon',
					  'image_source' =>'media_library',
					  'select_image' =>'',
					  'external_img' =>'',
					  'type'=> 'fontawesome',
					  'icon_fontawesome'=> 'fa fa-adjust',
					  'icon_openiconic'=> 'vc-oi vc-oi-dial',
					  'icon_typicons'=> 'typcn typcn-adjust-brightness',
					  'icon_entypo'=> 'entypo-icon entypo-icon-note',		  
					  'icon_linecons'=> 'vc_li vc_li-heart',
					  'icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
					  'icon_style' =>'square',
					  'icon_size' =>'small',
					  'icon_color' => '#0099CB',
					  'icon_hvr_color' =>'#ffffff',
					  'icon_bg_color' =>'#ffffff',
					  'icon_bg_hvr_color' =>'#0099CB',
					  'icon_border_color' =>'#121212',
					  'icon_hvr_bdr_color' =>'#ffffff',
					  'desc_color' =>'#888888',
					  'desc_size' =>'14px',
					  'desc_letter_space'=>'',
					  'desc_use_theme_fonts'=>'custom-font-family',
					  'desc_family'=>'',
					  'desc_font_weight'=>'400',
					  'desc_google_fonts'=>'',
					
					  'desc_line' =>'30px',
					  'button_check' => '',
					  'btn_bg' =>'#121212',
					  'btn_size' => '16px',
					  'btn_color' =>'#fff',
					  'btn_border_color' => '#fff',
					  'vertical_center' =>'',
					  'text_align' =>'center',
					  'border_check' => '',
					  'border_width' =>'100%',
					  'border_height' =>'2px',
					  'title_border_color' =>'',
					  'border_check_box' =>'',
					  'border_box_radius' =>'',
					  'border_box_width' =>'',
					  'border_box_color' =>'',
					  'border_check_box' =>'',
					  'border_box_hvr_color' =>'',
					  'border_box_hvr_width' =>'',
					  'border_box_hvr_radius' =>'',

					  'border_check_right' =>'',
					  'border_right_color' =>'',
					  'flip_height' => '300px',
					  'flip_style' =>'horizontal',
					  'front_color' =>'#121212',
					  'front_img' =>'',
					  'back_img' =>'',
					  'back_color' =>'#5aa1e3',
					  'box_bg_color' => '#ff004b',
					  'box_hover_color' =>'#0099cc',
					  'animation_effects'=>'no-animation',
					  'animation_delay'=>'50',
					  'box_shadow' => '1px 1px 3px 3px rgba(0, 0, 0, 0.15)',
					  'hvr_box_shadow' =>'0 22px 43px rgba(0, 0, 0, 0.15)',
					  'head_bg_color' =>'#ffffff',
					  'padding_top' =>'',
					  'padding_boottom' =>'',
					  'remove_padding' =>'',
					  'remove_cl_padding' =>'',
					  'el_class' =>'',
					  
					  'btn_svg' =>'Info Link',
					  'btn_svg_color' => '#4d4d4d',
					  'btn_svg_size' =>'14px',
					  
					  'svg_icon' =>'svg',
					  'svg_image'=>'',
					  'svg_d_icon' =>'app.svg',
						'svg_type'=>'delayed',
						'duration'=>'80',
						'alignment'=>'text-center',
						'max_width'=>'100px',
						'border_stroke_color'=>'#ff0000',
					  
						"style" => 'style-1',
					'btn_hover_style'=>'hover-left',
					'icon_hover_style'=>'hover-top',
					'btn_padding'=>'15px 30px',
					'btn_width'=>'250px',
					'btn_height'=>'50px',
					"btn_text" => 'The Plus',
					'btn_hover_text'=>'',
					"btn_icon" => 'fontawesome',
				  'btn_icon_fontawesome'=>'fa fa-arrow-right',
					  'btn_icon_openiconic'=> 'vc-oi vc-oi-dial',
					  'btn_icon_typicons'=> 'typcn typcn-adjust-brightness',
					  'btn_icon_entypo'=> 'entypo-icon entypo-icon-note',		  
					  'btn_icon_linecons'=> 'vc_li vc_li-heart',
					  'btn_icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
					'btn_use_theme_fonts'=>'custom-font-family',
					'btn_font_family'=>'',
					'btn_font_weight'=>'400',
					'btn_google_fonts'=>'',
					
					"before_after" => 'after',
					"btn_url" => '',
					'btn_align' =>'text-left',
					'select_bg_option'=>'normal',
					'normal_bg_color'=>'#252525',
					'gradient_color1'=>'#1e73be',
					'gradient_color2'=>'#2fcbce',
					'gradient_style'=>'horizontal',
					'bg_image'=>'',
					
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
					
					'tablet_font_size'=>'',
					'tablet_line_height'=>'',
					'tablet_letter_spacing'=>'',
					'tablet_btn_padding'=>'',
					
					'mobile_font_size'=>'',
					'mobile_line_height'=>'',
					'mobile_letter_spacing'=>'',
					'mobile_btn_padding'=>'',
					
					'text_color'=>'#8a8a8a',
					'text_hover_color'=>'#252525',
					'border_color'=>'#252525',
					'border_hover_color'=>'#252525',
					'border_radius'=>'30px',
					
					'full_width_btn'=>'',
					'hover_shadow'=>'',
					'transition_hover'=>'',
					
					'content_hover_effects' => '',
						'hover_shadow_color' => 'rgba(0, 0, 0, 0.6)',
					
					'show_arrows'=>'true',
					'show_dots'=>'true',
					'show_draggable'=>'false',
					'slide_loop'=>'false',
					'slide_autoplay'=>'false',
					'autoplay_speed'=>'3000',
					'steps_slide'=>'1',
					'dots_style'=>'style-3',
					'arrows_style'=>'style-1',
					'arrows_position'=>'top-right',
					'carousel_column'=>'4',
					'carousel_tablet_column'=>'3',
					'carousel_mobile_column'=>'2',
					
					'dots_border_color'=>'#000',
					'dots_bg_color'=>'#fff',
					'dots_active_border_color'=>'#000',
					'dots_active_bg_color'=>'#000',
					
					'arrow_bg_color'=>'#c44d48',
					'arrow_icon_color'=>'#fff',
					'arrow_hover_bg_color'=>'#fff',
					'arrow_hover_icon_color'=>'#c44d48',
					'arrow_text_color'=>'#fff',
					
					'column_space' =>'',
					'column_space_pading' => '10px',
					'tablet_hide' => 'off',
						'desktop_hide' =>'off',
						'mobile_hide' => 'off',
					), $atts ) );
					wp_enqueue_style( 'theplus-info-box-style');
					$rand_no=rand(1000000, 1500000);
					
					
					$data_class=$data_attr=$a_href=$a_title=$a_target=$a_rel=$style_content=$icons_before=$icons_after=$button_text=$button_hover_text=$gradient_color=$gradient_hover_color='';
					if($desktop_hide == 'on') {
						$desktop_hide = 'desktop-hide';
					}else{
						$desktop_hide = '';
					}
					if($tablet_hide == 'on') {
						$tablet_hide = 'tablet-hide';
					}else{
						$tablet_hide = '';
					}
					if($mobile_hide == 'on') {
						$mobile_hide = 'mobile-hide';
					}else{
						$mobile_hide = '';
					}
					$data_class=' button-'.esc_attr($rand_no).' ';
					$data_class .=' button-'.esc_attr($style).' ';
					
					if($full_width_btn=='yes'){
						$data_class .=' full-button ';
					}
					if($transition_hover=='yes'){
						$data_class .=' trnasition_hover ';
					}
					
					if($btn_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $btn_google_fonts );
					$btn_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($btn_use_theme_fonts=='custom-font-family'){
					$btn_font_family='font-family:'.$btn_font_family.';font-weight:'.$btn_font_weight.';';
				}else{
					$btn_font_family='';
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
						
						$a_href = $btn_url_a['url'];
						$a_title = $btn_url_a['title'];
						$a_target = $btn_url_a['target'];
						$a_rel = $btn_url_a['rel'];
						if ( ! empty( $a_rel ) ) {
							$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
						}
					
					if(!empty($btn_icon)){
				  vc_icon_element_fonts_enqueue( $btn_icon );
				  $btn_icon_class = isset( ${'btn_icon_' . $btn_icon} ) ? esc_attr( ${'btn_icon_' . $btn_icon} ) : 'fa fa-arrow-right';
				  
				  if($before_after=='before'){
				   $icons_before = '<i class="btn-icon button-'.esc_attr($before_after).' '.esc_attr($btn_icon_class).'"></i>';
				  }else{
				   $icons_after = '<i class="btn-icon button-'.esc_attr($before_after).' '.esc_attr($btn_icon_class).'"></i>';
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
						$data_class .=' '.esc_attr($btn_hover_style).' ';
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
						$the_button .='<div class="pt_plus_button '.$data_class.'" '.$data_attr.' >';
							$the_button .='<a class="button-link-wrap" href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' '.$button_hover_text.'>';
								$the_button .=$button_text;
								$the_button .=$style_content;
							$the_button .='</a>';
						$the_button .='</div>';
					$the_button .='</div>';		
					
					
					
						$hover_class  = $hover_attr = '';
					$hover_uniqid = uniqid('hover-effect');
					if ($content_hover_effects == "float_shadow" || $content_hover_effects == "grow_shadow" || $content_hover_effects == "shadow_radial") {
						$hover_attr .= ' data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
						$hover_attr .= ' data-hover_shadow="' . esc_attr($hover_shadow_color) . '" ';
						$hover_attr .= ' data-content_hover_effects="' . esc_attr($content_hover_effects) . '" ';
					}
					if ($content_hover_effects == "grow") {
						$hover_class .= 'content_hover_grow';
					} elseif ($content_hover_effects == "push") {
						$hover_class .= 'content_hover_push';
					} elseif ($content_hover_effects == "bounce-in") {
						$hover_class .= 'content_hover_bounce_in';
					} elseif ($content_hover_effects == "float") {
						$hover_class .= 'content_hover_float';
					} elseif ($content_hover_effects == "wobble_horizontal") {
						$hover_class .= 'content_hover_wobble_horizontal';
					} elseif ($content_hover_effects == "wobble_vertical") {
						$hover_class .= 'content_hover_wobble_vertical';
					} elseif ($content_hover_effects == "float_shadow") {
						$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_float_shadow';
					} elseif ($content_hover_effects == "grow_shadow") {
						$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_grow_shadow';
					} elseif ($content_hover_effects == "shadow_radial") {
						$hover_class .= '' . esc_attr($hover_uniqid) . ' content_hover_radial';
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
					
					$service_title = $description= $service_img = $service_btn = $service_center= $service_align = $service_border = $service_icon_style= $service_space = $pd=$pd0 =$serice_box_border =$serice_img_border=$border_right_css=$imge_content=$the_service_main_css=$title_css=$subtitle_css=$output='';

					if($title_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $title_google_fonts );
					$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($title_use_theme_fonts=='custom-font-family'){
					$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
				}else{
					$title_font_family='';
				}	
if($title_color_o == "gradient") {
			$title_gradient_cass = 'heading-title-gradient';
		   }else{
			$title_gradient_cass = '';
		 }

						$title_css = ' style="';
							 if($title_color_o == "gradient") {
				$title_css .= pt_plus_gradient_color($title_color1,$title_color2,$title_hover_style);
			   }else{
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
					if($title !=''){
						if (!empty($a_href)){
							$service_title= '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' ><div class="service-title '.esc_attr($title_gradient_cass).'" '.$title_css.'> '.esc_html($title).' </div></a>';
						}else{
							$service_title= '<div class="service-title '.esc_attr($title_gradient_cass).'" '.$title_css.'> '.esc_html($title).' </div>';
						}
					}
				if($subtitle_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $subtitle_google_fonts );
					$subtitle_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($subtitle_use_theme_fonts=='custom-font-family'){
					$subtitle_font_family='font-family:'.$subtitle_font_family.';font-weight:'.$subtitle_font_weight.';';
				}else{
					$subtitle_font_family='';
				}

				
					$sub_title_css = ' style="';
							if($sub_title_color != "") {
								$sub_title_css .= 'color: '.esc_attr($sub_title_color).';';
							}	
							if($sub_title_size != "") {
								$sub_title_css .= 'font-size: '.esc_attr($sub_title_size).';';
							}
							if($sub_title_line != "") {
								$sub_title_css .= 'line-height: '.esc_attr($sub_title_line).';';
							}
							if($sub_title_letter != "") {
								$sub_title_css .= 'letter-spacing: '.esc_attr($sub_title_letter).';';
							}
							$sub_title_css .= $subtitle_font_family;
						$sub_title_css .= '"';
						
					if($border_check_box == 'true'){
						$serice_box_border ='service-border-box';		
					}
					if($remove_padding == 'true'){
						$service_space ='remove-padding';				
					}
					
					if($border_check_right == 'true'){
						$serice_img_border ='service-img-border';
						$border_right_css = ' style="';
						if($border_right_color != "") {
						$border_right_css .= 'border-color: '.esc_attr($border_right_color).';';
						}		
						$border_right_css .= '"';
						
					}
					
					if($desc_use_theme_fonts=='google-fonts'){
					$desc_font_data = pt_plus_getFontsData( $desc_google_fonts );
					$desc_font_family = pt_plus_googleFontsStyles( $desc_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $desc_font_data );
					}elseif($desc_use_theme_fonts=='custom-font-family'){
						$desc_font_family='font-family:'.$desc_family.';font-weight:'.$desc_font_weight.';';
					}else{
						$desc_font_family='';
					}
				 
				 
					$desc_css = ' style="';
						if($desc_color != "") {
							$desc_css .='color:'.esc_attr($desc_color).';';
						}	
						if($desc_size != "") {
							$desc_css .='font-size: '.esc_attr($desc_size).';';
						}
						if($desc_letter_space != "") {
							$desc_css .='letter-spacing: '.esc_attr($desc_letter_space).';';
						}
						
							$desc_css .= $desc_font_family ;
					$desc_css .= '"';	
					if($content !=''){
						$content = wpb_js_remove_wpautop($content, true);
						 $description='<div class="service-desc" '.$desc_css.'> '.$content.' </div>';
					}
					
					if($main_style == 'style_6'){
					   $imge_content ="image-height";
					}
					
					
					if($image_icon == 'image'){
						if($image_source == 'media_library'){
							$img = wp_get_attachment_image_src("$select_image", "full");
							$imgSrc = $img[0];
						}else if($image_source == 'externals_link'){
							$imgSrc = $external_img;
						}else{
							$imgSrc = '';
						}
						
						 $service_img='<img src="'.esc_url($imgSrc).'"   class="service-img '.esc_attr($imge_content).'" alt="" />';
					}
					if($main_style == 'style_8'){
						if($icon_style == 'square'){
						$service_icon_style = 'icon-squre';
						} 
						if($icon_style == 'rounded'){
							$service_icon_style = 'icon-rounded';
						} 
						
						$icon_css = ' style="';
						if($icon_color != "") {
						$icon_css .= 'color: '.esc_attr($icon_color).';';
						}
						if($icon_bg_color != "") {
						$icon_css .= 'background-color: '.esc_attr($icon_bg_color).';';
						}
						if($icon_border_color != "") {
						$icon_css .= 'border-color: '.esc_attr($icon_border_color).';';
						}
						
						$icon_css .= '"'; 
					}	
					
						if($icon_size == 'small'){
							$service_icon_size = 'service-icon-small';
						}
						if($icon_size == 'medium'){
							$service_icon_size = 'service-icon-medium';
						}
						if($icon_size == 'large'){
							$service_icon_size = 'service-icon-large';
						}
						
					if($image_icon == 'icon'){
						if($icon_style == 'square'){
						$service_icon_style = 'icon-squre';
						} 
						if($icon_style == 'rounded'){
							$service_icon_style = 'icon-rounded';
						} 	
						if($icon_style == 'hexagon'){
							$service_icon_style = 'icon-hexagon';
						} 	
						if($icon_style == 'pentagon'){
							$service_icon_style = 'icon-pentagon';
						}  	
						if($icon_style == 'square-rotate'){
							$service_icon_style = 'icon-square-rotate';
						} 	
						
						$icon_css = ' style="';
						if($icon_color != "") {
						$icon_css .= 'color: '.esc_attr($icon_color).';';
						}
						if($icon_bg_color != "") {
						$icon_css .= 'background-color: '.esc_attr($icon_bg_color).' !Important;';
						}
						if($icon_border_color != "") {
						$icon_css .= 'border-color: '.esc_attr($icon_border_color).' !Important;';
						}
						
						$icon_css .= '"'; 
						vc_icon_element_fonts_enqueue( $type );
						$type12= $type; 
						$icon_class = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
						if($main_style == 'style_8'){
							$service_img = '<i class=" '.esc_attr($icon_class).' service-icon '.esc_attr($service_icon_size).'" ></i>';
						}else{	
							$service_img = '<i class=" '.esc_attr($icon_class).' service-icon '.esc_attr($service_icon_size).' '.esc_attr($service_icon_style).'" '.$icon_css.'></i>';
						}
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
						$service_img ='<div class="pt_plus_animated_svg  svg-'.esc_attr($rand_no).'" data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($svg_type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
							$service_img .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';max-height:'.esc_attr($max_width).';">';
								$service_img .='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
							$service_img .='</div>';
						$service_img .='</div>';
					}	
					if($button_check == 'true'){

						$service_btn = $the_button;
					}
					
					if($vertical_center == 'true'){
						$service_center = 'vertical-center';
					}
						
					if($text_align == 'left'){
						$service_align = 'text-left';
					}
					if($text_align == 'center'){
						$service_align = 'text-center';
					}
					if($text_align == 'right'){
						$service_align = 'text-right';
					} 
				   
					if($border_check == 'true'){
						$border_css = ' style="';
						if($title_border_color != "") {
						$border_css .= 'border-color: '.esc_attr($title_border_color).';';
						}
						if($border_height != "") {
						$border_css .='border-width:'.esc_attr($border_height).';';
						}	
						if($border_width != "") {
						$border_css .='width: '.esc_attr($border_width).';';
						}	
						
						$border_css .= '"'; 
						$service_border = '<div class="service-border" '.$border_css.'> </div>' ;
					}
					if($main_style == 'style_5'){
						if($flip_style == 'horizontal'){
							$service_flip= "flip-horizontal";
						}
						if($flip_style == 'vertical'){
							$service_flip= "flip-vertical";
						}
						
						$service_flip_height = ' style="';
						if($flip_height != "") {
						$service_flip_height .= 'min-height: '.esc_attr($flip_height).';';
						}		
						$service_flip_height .= '"'; 
						$flip_front_img = wp_get_attachment_image_src("$front_img ", "full");
							$flip_front_img = $flip_front_img[0];
						$flip_back_img = wp_get_attachment_image_src("$back_img", "full");
							$flip_back_img = $flip_back_img[0];

						$service_front_css = ' style="';
						if($front_color != "") {
						$service_front_css .= 'background-color: '.esc_attr($front_color).';';
						}
						if($front_img != "") {
						$service_front_css .= 'background: url('.esc_attr($flip_front_img ).');';
						}	
						if($flip_height != "") {
						$service_front_css .= 'min-height: '.esc_attr($flip_height).';';
						}
						$service_front_css .= '"'; 
						
						$service_back_css = ' style="';
						if($back_color != "") {
						$service_back_css .= 'background-color: '.esc_attr($back_color).';';
						}
						if($flip_back_img!= "") {
						$service_back_css .= 'background: url('.esc_attr($flip_back_img).');';
						}	
						if($flip_height != "") {
						$service_back_css .= 'min-height: '.esc_attr($flip_height).';';
						}
						$service_back_css .= '"'; 
					}	
					
					if($main_style != 'style_5' ){
						$pd = 'pd-15';
					}
					if($main_style == 'style_8'  ){
						$pd = '';
						}
					
					if($remove_cl_padding == 'true'){
						$pd0 = 'pd-0';
					}
					if($main_style != 'style_8'  ){
					$the_service_main_css = ' style="';
						if($border_box_color != "") {
						$the_service_main_css .= 'border-color: '.esc_attr($border_box_color).';';
						}
						if($main_style != 'style_5'){
						if($box_bg_color!= "") {
						$the_service_main_css .= 'background-color: '.esc_attr($box_bg_color).';';
						}
						}
						if($padding_top != "") {
							$the_service_main_css .='padding-top:'.esc_attr($padding_top).';';
						}	
						if($padding_boottom != "") {
							$the_service_main_css .='padding-bottom: '.esc_attr($padding_boottom).';';
						}
						
					$the_service_main_css .= '"';
					}
					
						$header_css = ' style="';
						if($head_bg_color!= "") {
						$header_css .= 'background-color: '.esc_attr($head_bg_color).';';
						}
					$header_css .= '"';
					
					$style8_css = ' style="';
					if($box_bg_color!= "") {
						$style8_css .= 'background-color: '.esc_attr($box_bg_color).';';
						}
					$style8_css .= '"';
					
					
					if($main_style != 'style_8'){
						$box_hover = ' data-box-hover="'.esc_attr($hvr_box_shadow).'"';
						$box_hadow  = ' data-box-hvr="'.esc_attr($box_shadow).'"';
					}else{
						$box_hover = '';
						$box_hvr = '';
						$box_hadow ='';
					}
					
					
					$hover_attr = ' data-icon_hvr_color="'.esc_attr($icon_hvr_color).'"';
					$hover_attr .= ' data-icon_bg_hvr_color="'.esc_attr($icon_bg_hvr_color).'"';
					$hover_attr .= ' data-icon_hvr_bdr_color="'.esc_attr($icon_hvr_bdr_color).'"';
					$hover_attr .= ' data-btn_svg_color="'.esc_attr($btn_svg_color).'"';
					$hover_attr .= ' data-btn_svg_size="'.esc_attr($btn_svg_size).'"';
					
						$uid=uniqid('info_box');
					

					$arrow_class='';
					if($arrows_style=='style-4' || $arrows_style=='style-5'){
						$arrow_class=$arrows_position;
					}
						$isotope ='';
						$data_slider ='';
					if($info_box_layout=='carousel_layout'){
						
						$data_slider .=' data-show_arrows="'.esc_attr($show_arrows).'"';
						$data_slider .=' data-show_dots="'.esc_attr($show_dots).'"';
						$data_slider .=' data-show_draggable="'.esc_attr($show_draggable).'"';
						$data_slider .=' data-slide_loop="'.esc_attr($slide_loop).'"';
						$data_slider .=' data-slide_autoplay="'.esc_attr($slide_autoplay).'"';
						$data_slider .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
						$data_slider .=' data-steps_slide="'.esc_attr($steps_slide).'"';
						$data_slider .=' data-carousel_column="'.esc_attr($carousel_column).'"';
						$data_slider .=' data-carousel_tablet_column="'.esc_attr($carousel_tablet_column).'"';
						$data_slider .=' data-carousel_mobile_column="'.esc_attr($carousel_mobile_column).'"';
						$data_slider .=' data-dots_style="slick-dots '.esc_attr($dots_style).'" ';
						$data_slider .=' data-arrows_style="'.esc_attr($arrows_style).'" ';
						$data_slider .=' data-arrows_position="'.esc_attr($arrows_position).'" ';
						
						$data_slider .=' data-dots_border_color="'.esc_attr($dots_border_color).'" ';
						$data_slider .=' data-dots_bg_color="'.esc_attr($dots_bg_color).'" ';
						$data_slider .=' data-dots_active_border_color="'.esc_attr($dots_active_border_color).'" ';
						$data_slider .=' data-dots_active_bg_color="'.esc_attr($dots_active_bg_color).'" ';
						
						$data_slider .=' data-arrow_bg_color="'.esc_attr($arrow_bg_color).'" ';
						$data_slider .=' data-arrow_icon_color="'.esc_attr($arrow_icon_color).'" ';
						$data_slider .=' data-arrow_hover_bg_color="'.esc_attr($arrow_hover_bg_color).'" ';
						$data_slider .=' data-arrow_hover_icon_color="'.esc_attr($arrow_hover_icon_color).'" ';
						$data_slider .=' data-arrow_text_color="'.esc_attr($arrow_text_color).'" ';
						if($column_space == 'on'){
							$data_slider .=' data-column_space_pading="'.esc_attr($column_space_pading).'"';	
						}else{
							$data_slider .=' data-column_space_pading="0px"';	
						}
						$isotope = 'list-carousel-slick';
					}

					
					
					if ($info_box_layout == 'carousel_layout'){
					if(isset($loop_content) && !empty($loop_content) && function_exists('vc_param_group_parse_atts')) {
							$loop_content= (array) vc_param_group_parse_atts( $loop_content);
							
							foreach($loop_content as $item) {
							
								$title_color=$loop_title=$title_line=$title_size=$title_letter_spacing=$loop_svg_d_icon=$svg_type=$duration=$loop_image_icon=$svg_image=$loop_max_width=$list_desc=$loop_title=$list_subtitle=$list_title=$loop_btn_text=$list_img='';
								
					

								$loop_button =$loop_btn_url=$loop_btn_before_after='';
								if(!empty($item['loop_btn_url'])){
									$loop_btn_url= $item['loop_btn_url'];
									}
									$loop_btn_url = ( '||' === $loop_btn_url ) ? '' : $loop_btn_url;
												$loop_btn_url_a= vc_build_link( $loop_btn_url);
												
												$loop_a_href = $loop_btn_url_a['url'];
												$loop_a_title = $loop_btn_url_a['title'];
												$loop_a_target = $loop_btn_url_a['target'];
												$loop_a_rel = $loop_btn_url_a['rel'];
												if ( ! empty( $loop_a_rel ) ) {
													$loop_a_rel = ' rel="' . esc_attr( trim( $loop_a_rel ) ) . '"';
												}
								if(!empty($item['loop_button_check']) && $item['loop_button_check'] == 'true'){
									
									if(!empty($item['loop_btn_text'])){
									$loop_btn_text= $item['loop_btn_text'];
									}
									if(!empty($item['loop_btn_before_after'])){
									$loop_btn_before_after= $item['loop_btn_before_after'];
									}
									
										if(!empty($item['loop_btn_icon'])){
									$loop_btn_icon= $item['loop_btn_icon'];
									}
									
									
									$iloop_btn_con_fontawesome= $item['loop_btn_icon_fontawesome'];
									$loop_btn_icon_openiconic= $item['loop_btn_icon_openiconic'];
									$loop_btn_icon_typicons= $item['loop_btn_icon_typicons'];
									$loop_btn_icon_entypo= $item['loop_btn_icon_entypo'];
									$loop_btn_icon_linecons= $item['loop_btn_icon_linecons'];
									$loop_btn_icon_monosocial= $item['loop_btn_icon_monosocial'];
									
										$data_class=$data_attr=$loop_a_href=$loop_a_title=$loop_a_target=$loop_a_rel=$style_content=$icons_before=$icons_after=$button_text=$button_hover_text=$gradient_color=$gradient_hover_color=$loop_icon_bg_color=$loop_icon_style=$loop_icon_border_color=$loop_icon_size=$loop_icon_color=$loop_icon_size=$loop_imgSrc=$loop_external_img=$loop_back_img=$loop_front_img=$flip_loop_front_img =$flip_loop_back_img =$loop_back_options=$loop_front_options='';
											
											$data_class=' button-'.esc_attr($rand_no).' ';
											$data_class .=' button-'.esc_attr($style).' ';
											
											if($full_width_btn=='yes'){
												$data_class .=' full-button ';
											}
											if($transition_hover=='yes'){
												$data_class .=' trnasition_hover ';
											}
											
											
												$loop_btn_url = ( '||' === $loop_btn_url ) ? '' : $loop_btn_url;
												$loop_btn_url_a= vc_build_link( $loop_btn_url);
												
												$loop_a_href = $loop_btn_url_a['url'];
												$loop_a_title = $loop_btn_url_a['title'];
												$loop_a_target = $loop_btn_url_a['target'];
												$loop_a_rel = $loop_btn_url_a['rel'];
												if ( ! empty( $loop_a_rel ) ) {
													$loop_a_rel = ' rel="' . esc_attr( trim( $loop_a_rel ) ) . '"';
												}
											
											if(!empty($loop_btn_icon)){
										  vc_icon_element_fonts_enqueue( $loop_btn_icon );
										  $loop_btn_icon_class = isset( ${'loop_btn_icon_' . $loop_btn_icon} ) ? esc_attr( ${'loop_btn_icon_' . $loop_btn_icon} ) : 'fa fa-arrow-right';
										  
										  if($loop_btn_before_after=='before'){
										   $icons_before = '<i class="btn-icon button-'.esc_attr($loop_btn_before_after).' '.esc_attr($loop_btn_icon_class).'"></i>';
										  }else{
										   $icons_after = '<i class="btn-icon button-'.esc_attr($loop_btn_before_after).' '.esc_attr($loop_btn_icon_class).'"></i>';
										  }
										 }
											if($style=='style-1'){
												$button_text =$icons_before.esc_html($loop_btn_text) . $icons_after;
												$style_content='<div class="button_line"></div>';
											}
											if($style=='style-2' || $style=='style-5' || $style=='style-8' || $style=='style-10'){
												$button_text =$icons_before . esc_html($loop_btn_text) . $icons_after;
											}
											if($style=='style-3'){
												$button_text =$loop_btn_text.'<svg class="arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg><svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg>';
											}
											if($style=='style-4'){
												$button_text =$icons_before.esc_html($loop_btn_text) . $icons_after;
												if(!empty($btn_hover_text)){
													$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
												}else{
													$button_hover_text =' data-hover="'.esc_attr($loop_btn_text).'" ';
												}
											}
											if($style=='style-6'){
												$button_text =$loop_btn_text;
											}
											if($style=='style-7'){
												$button_text =$loop_btn_text.'<span class="btn-arrow"></span>';
											}
											if($style=='style-9'){
												$button_text =$loop_btn_text.'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
											}
											if($style=='style-11'){
												$button_text ='<span>'.$icons_before . $loop_btn_text . $icons_after.'</span>';
												if(!empty($btn_hover_text)){
													$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
												}else{
													$button_hover_text =' data-hover="'.$loop_btn_text.'" ';
												}
												$data_class .=' '.$btn_hover_style.' ';
											}
											if($style=='style-12' || $style=='style-15' || $style=='style-16'){
												$button_text ='<span>'.$icons_before . $loop_btn_text . $icons_after.'</span>';
											}
											if($style=='style-13'){
												$button_text ='<span>'.$icons_before . $loop_btn_text . $icons_after.'</span>';
												$data_class .=' '.$btn_hover_style.' ';
											}
											if($style=='style-14'){
												$button_text ='<span>'.$icons_before . $loop_btn_text . $icons_after.'</span>';
												if(!empty($btn_hover_text)){
													$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
												}else{
													$button_hover_text =' data-hover="'.esc_attr($loop_btn_text).'" ';
												}
											}
											if($style=='style-17'){
												$icons_before=$icons_after;
												$button_text =$icons_before .'<span>'. esc_html($loop_btn_text) .'</span>';
												$data_class .=' '.$icon_hover_style.' ';
											}
											if($style=='style-18' || $style=='style-19' || $style=='style-20' || $style=='style-21' || $style=='style-22'){
												$button_text =$icons_before .'<span>'. esc_html($loop_btn_text).'</span>'. $icons_after;
											}
											
											if($style=='style-23'){
												$button_text ='<span><div class="align-center">'. $icons_before . $loop_btn_text . $icons_after .'</div></span>';
												if(!empty($btn_hover_text)){
													$button_text .='<span><div class="align-center">'. $icons_before . $btn_hover_text . $icons_after .'</div></span>';
												}else{
													$button_text .='<span><div class="align-center">'. $icons_before . $loop_btn_text . $icons_after .'</div></span>';
												}
												$data_class .=' '.$btn_hover_style.' ';
											}
											
										if($button_check == 'true'){
											$loop_button ='<div class="'.$btn_align.' ts-button">';
												$loop_button .='<div class="pt_plus_button '.$data_class.'" '.$data_attr.' >';
													$loop_button .='<a class="button-link-wrap" href="'.esc_url( $loop_a_href ).'" title="'.esc_attr( $loop_a_title ).'" target="'.esc_attr( $loop_a_target ).'" '.$loop_a_rel.' '.$button_hover_text.'>';
														$loop_button .=$button_text;
														$loop_button .=$style_content;
													$loop_button .='</a>';
												$loop_button .='</div>';
											$loop_button .='</div>';
										}
								}
							
								if(!empty($item['loop_desc'])){
									$loop_desc= $item['loop_desc'];
									$list_desc = '<div class="service-desc" '.$desc_css.'>'.esc_html($loop_desc).'</div>';
								}
						
								if(!empty($item['loop_title'])){
									$loop_title= $item['loop_title'];
									if (!empty($loop_a_href)){
										$list_title = '<a href="'.esc_url( $loop_a_href ).'" title="'.esc_attr( $loop_a_title ).'" target="'.esc_attr( $loop_a_target ).'" '.$loop_a_rel.'><h6 class="service-title '.esc_attr($title_gradient_cass).'"  '.$title_css.'>'.esc_html($loop_title).'</h6></a>';						
									}else{
										$list_title = '<h6 class="service-title '.esc_attr($title_gradient_cass).'"  '.$title_css.'>'.esc_html($loop_title).'</h6>';
									}
								}
								if(!empty($item['loop_sub'])){
									$loop_sub= $item['loop_sub'];
									$list_subtitle = '<h6 class="sub-subject-color " '.$subtitle_css.'>'.esc_html($loop_sub).'</h6>';
								}
								if(!empty($item['loop_image_icon'])){
									
									$loop_svg_d_icon= $item['loop_svg_d_icon'];
									$loop_svg_type= $item['loop_svg_type'];
									$loop_duration= $item['loop_duration'];
									$loop_max_width= $item['loop_max_width'];
									$loop_border_stroke_color= $item['loop_border_stroke_color'];
									
									$type= $item['type'];
									
									$icon_fontawesome= $item['icon_fontawesome'];
									$icon_openiconic= $item['icon_openiconic'];
									$icon_typicons= $item['icon_typicons'];
									$icon_entypo= $item['icon_entypo'];
									$icon_linecons= $item['icon_linecons'];
									$icon_monosocial= $item['icon_monosocial'];
									
									if(!empty($item['loop_icon_size'])){
									$loop_icon_size= $item['loop_icon_size'];
									}
									if(!empty($item['loop_icon_color'])){
									$loop_icon_color= $item['loop_icon_color'];
									}
									
									if(!empty($item['loop_icon_border_color'])){
									$loop_icon_border_color= $item['loop_icon_border_color'];
									}else{
									$loop_icon_border_color= '';
									}
									
									if(!empty($item['loop_icon_bg_color'])){
									$loop_icon_bg_color= $item['loop_icon_bg_color'];
									}else{
										$loop_icon_bg_color ='';
									}
									
									if(!empty($item['loop_icon_style'])){
									$loop_icon_style= $item['loop_icon_style'];
									}else{
										$loop_icon_style ='';
									}
									
									if($loop_icon_size == 'small'){
										$service_loop_icon_size = 'service-icon-small';
									}
									if($loop_icon_size == 'medium'){
										$service_loop_icon_size = 'service-icon-medium';
									}
									if($loop_icon_size == 'large'){
										$service_loop_icon_size = 'service-icon-large';
									}
									
									if($loop_icon_style == 'square'){
										$loop_icon_style = 'icon-squre';
										}else if($loop_icon_style == 'rounded'){
											$loop_icon_style = 'icon-rounded';
										}elseif($loop_icon_style == 'hexagon'){
										        $loop_icon_style = 'icon-hexagon';
										}elseif($loop_icon_style == 'pentagon'){
										        $loop_icon_style = 'icon-pentagon';
										}elseif($loop_icon_style == 'square-rotate'){
										       $loop_icon_style = 'icon-square-rotate';
										}else{
											$loop_icon_style='';
										} 
						
										if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
											if(isset($item['loop_image_source']) && $item['loop_image_source'] == 'media_library'){
												if(isset($item['loop_select_image']) && !empty($item['loop_select_image'])){
												$loop_select_image= $item['loop_select_image'];
												$loop_img = wp_get_attachment_image_src("$loop_select_image", "full");
												$loop_imgSrc = $loop_img[0];
												}
											}else if(isset($item['loop_image_source']) && $item['loop_image_source'] == 'externals_link'){
													if(isset($item['loop_external_img']) && !empty($item['loop_external_img'])){
														$loop_external_img= $item['loop_external_img'];
													$loop_imgSrc = $loop_external_img;
													}
											}else{
													$loop_imgSrc = '';
												}		
											
											$list_img ='<div class="ts-icon-img icon-img-b " >';
												$list_img .='<img class="" src='.esc_url($loop_imgSrc).' alt="" />';
											$list_img .='</div>';
										}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){		
											$loop_icon_css = ' style="';
											if($loop_icon_color != "") {
											$loop_icon_css .= 'color: '.esc_attr($loop_icon_color).';';
											}	
											if($loop_icon_bg_color != "") {
											$loop_icon_css .= 'background-color: '.esc_attr($loop_icon_bg_color).' !important;';
											}
											if($loop_icon_border_color != "") {
											$loop_icon_css .= 'border-color: '.esc_attr($loop_icon_border_color).';';
											}
											
											$loop_icon_css .= '"'; 
											vc_icon_element_fonts_enqueue( $type );
											$type12= $type; 
											$icon_class = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
								
												if($main_style == 'style_8'){
													$list_img = '<i class=" '.esc_attr($icon_class).' service-icon '.esc_attr($service_loop_icon_size).'" ></i>';
												}else{	
													$list_img = '<i class=" '.esc_attr($icon_class).' service-icon '.esc_attr($service_loop_icon_size).' '.esc_attr($loop_icon_style).'" '.$loop_icon_css.'></i>';
												
												}
											
										}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'svg'){
											if(isset($item['loop_svg_icon']) && $item['loop_svg_icon']== 'img'){						
												if(isset($item['loop_svg_image']) && !empty($item['loop_svg_image'])){
												$loop_svg_image= $item['loop_svg_image'];
												$loop_svg_attach = wp_get_attachment_image_src( $loop_svg_image,true);
												$loop_svg_url = $loop_svg_attach[0];
												}
											}else{
											
												$loop_svg_url = THEPLUS_PLUGIN_URL.'vc_elements/images/svg/'.esc_attr($loop_svg_d_icon); 
											}
											$rand_no=rand(1000000, 1500000);
											
											if($loop_svg_url ==''){
											if(!empty($loop_border_stroke_color)){
												$loop_border_stroke_color=$loop_border_stroke_color;
											}else{
												$loop_border_stroke_color='none';
											}
											
											}
											$list_img ='<div class="pt_plus_animated_svg svg-'.esc_attr($rand_no).' " data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($loop_svg_type).'" data-duration="'.esc_attr($loop_duration).'" data-stroke="'.esc_attr($loop_border_stroke_color).'" data-fill_color="none">';
												$list_img .='<div class="svg_inner_block" style="max-width:'.esc_attr($loop_max_width).';max-height:'.esc_attr($loop_max_width).';">';
													$list_img .='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($loop_svg_url).'" ></object>';
												$list_img .='</div>';
											$list_img .='</div>';
											
										}	
								}
								
								
											if(isset($item['loop_front_img']) && !empty($item['loop_front_img'])){
												$loop_front_img= $item['loop_front_img'];
												$flip_loop_front_img = wp_get_attachment_image_src($loop_front_img, "full");
												$flip_loop_front_img = $flip_loop_front_img[0];
											}
											if(isset($item['loop_back_img']) && !empty($item['loop_back_img'])){
												$loop_back_img= $item['loop_back_img'];
												$flip_loop_back_img = wp_get_attachment_image_src($loop_back_img, "full");
												$flip_loop_back_img = $flip_loop_back_img[0];
											}	
											$loop_back_color='';
											if(!empty($item['loop_back_color'])){
												$loop_back_color= $item['loop_back_color'];
											}
											$loop_front_color='';
											if(!empty($item['loop_front_color'])){
												$loop_front_color= $item['loop_front_color'];
											}
											
																				
											$service_loop_front_css = ' style="';
											if(isset($item['loop_front_options']) && $item['loop_front_options'] == 'bg-color'){
												if($loop_front_color != "") {												
												$service_loop_front_css .= 'background-color: '.esc_attr($loop_front_color).';';
												}
											}
											if(isset($item['loop_front_options']) && $item['loop_front_options'] == 'bg-image'){
												if(!empty($flip_loop_front_img)) {
												$service_loop_front_css .= 'background: url('.esc_url($flip_loop_front_img ).');';
												}
											}	
											if($flip_height != "") {
												$service_loop_front_css .= 'min-height: '.esc_attr($flip_height).';';
											}
											$service_loop_front_css .= '"'; 
											
											$service_loop_back_css = ' style="';
											if(isset($item['loop_back_options']) && $item['loop_back_options'] == 'bg-color'){
												if($loop_back_color != "") {
												$service_loop_back_css .= 'background-color: '.esc_attr($loop_back_color).';';
												}
											}
											if(isset($item['loop_back_options']) && $item['loop_back_options'] == 'bg-image'){
												if(!empty($flip_loop_back_img)) {
												$service_loop_back_css .= 'background: url('.esc_url($flip_loop_back_img).');';
												}
											}	
											if($flip_height != "") {
												$service_loop_back_css .= 'min-height: '.esc_attr($flip_height).';';
											}
											$service_loop_back_css .= '"'; 
											
							
							$output .= '<div class="info-box-inner">';	
								if($main_style == 'style_1'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
									
										$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
										if($list_img != ''){				
											$output .= '<div class="m-r-16 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$list_img.' </div>';
										}
											$output .= '<div class="service-content ">';
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;
											$output .= '</div>';
											$output .= '<a class="all-link"></a>';
										$output .= '</div>';
									$output .= '</div>';					
								}
								if($main_style == 'style_2'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
									
										$output .= '<div class="service-media text-right '.esc_attr($service_center).' ">';
											$output .= '<div class="service-content">';
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;
											$output .= '</div>';			
											if($list_img != ''){					
											$output .=  '<div class="m-l-16 serice_img_border" '.$border_right_css.'>'.$list_img.'</div>';
											}
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_3'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="'.esc_attr($service_align).'">';
											$output .= '<div class="service-center  ">';
												$output .= $list_img;
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;
												$output .= '</div>';				
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_4'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .' '.esc_attr($serice_box_border).'" '.$the_service_main_css.'>';
										$output .= '<div class="">';
											$output .= '<div class="service-media service-left '.esc_attr($service_center).'">';
												$output .= $list_img;
												$output .= '<div class="service-content">';
													$output .= $list_title;
												$output .= '</div>';								
											$output .= '</div>';	
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;											
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_5'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="service-flipbox '.esc_attr($service_flip).' height-full" '.$service_flip_height.'>';
											$output .= '<div class="service-flipbox-holder height-full text-center perspective bezier-1"	>';
												$output .= '<div class="service-flipbox-front bezier-1 no-backface origin-center" '.$service_loop_front_css.'>';
													$output .= '<div class="service-flipbox-content width-full">';
														$output .= $list_img;
														$output .= '<div class="service-content">';
															$output .= $list_title;
															$output .= $service_border;
														$output .= '</div>';
													$output .= '</div>';
												$output .= '</div>';	
												$output .= '<div class="service-flipbox-back fold-back-horizontal no-backface bezier-1 origin-center" '.$service_loop_back_css.'>';
													$output .= '<div class="service-flipbox-content width-full">';
														$output .= $list_desc;
														$output .= $loop_button;
													$output .= '</div>';
												$output .= '</div>';	
											$output .= '</div>';				
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_6'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class=" text-left '.esc_attr($service_center).' ">';			
											$output .= '<div class="top-content"> '.$list_img.' </div>';
											$output .= '<div class="bottom-content">';
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_7'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
										if($list_img != ''){				
											$output .= '<div class="m-r-16 service-bg-7 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$list_img.' </div>';
										}
											$output .= '<div class="service-content ">';
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= $loop_button;	
											$output .= '</div>';						
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_8'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="about-post  '.esc_attr($service_center).' " '.$style8_css.'>';	
											$output .= '<div class="about-post-content" '.$header_css.'>';
												if($list_img != ''){		
													$output .='<a href="#" class="demo icon-middle '.$service_icon_style.'" '.$icon_css.'>';
													$output .= '<div class="service-bg-7 '.esc_attr($serice_img_border).'"> '.$list_img.' </div>';
													$output .= '</a>';
												}
												$output .= $list_title;
												$output .= $service_border;
											$output .= '</div>';	
											$output .= '<div class="hover-about">';	
												$output .= '<div classs="hiover-about-sub"><h3 '.$sub_title_css.' class="service-sub-space"> '.esc_html($sub_title).' </h3> </div>';
												$output .= '<div classs="about-hover-show">';
													$output .= $list_desc;
													$output .= $loop_button;	
												$output .= '</div>';	
											$output .= '</div>';						
										$output .= '</div>';
									$output .= '</div>';
								}
								
								if($main_style == 'style_9'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="info-box style">';	
											$output .= '<div class="info-box-tag "><h3 class="service-sub-space info-tag-title" '.$sub_title_css.'> '.esc_html($sub_title).' </h3> </div>';
											$output .= '<div class="info-box-conetnt">';
												$output .= $list_title;
												$output .= $service_border;
												$output .= $list_desc;
												$output .= '<a href="'.esc_url( $loop_a_href ).'" title="'.esc_attr( $loop_a_title ).'" target="'.esc_attr( $loop_a_target ).'" '.$loop_a_rel.'><div data-label="'.esc_attr($loop_btn_text).'" class="action arrow-next expandable"><svg viewBox="0 0 36 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="icon-circle" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" transform="rotate(-90 17 17)"><circle cx="17" cy="17" r="17"></circle></g></svg> <svg width="15px" height="14px" viewBox="0 0 15 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-3.000000, -1.000000)" fill-rule="nonzero" fill="#FFFFFF" class="arrow-next-fill"><g id="arrow" transform="translate(3.000000, 1.000000)"><polygon id="Path-2" points="6.29289322 12.3410345 7.70710678 13.7552481 14.4624599 6.99989497 7.75545815 0.292893219 6.34124459 1.70710678 11.6340328 6.99989497"></polygon> <polygon id="Path-5" points="0 8 13 8 13 6 0 6"></polygon></g></g></g></svg></div></a>';
											$output .= '</div>';						
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_10'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="info-box style-10">';	
											$output .= '<div class="info-box-all">';
												$output .= '<div class="info-box "><h3 class="info-title service-sub-space" '.$sub_title_css.'> '.esc_html($sub_title).' </h3> </div>';
												$output .= '<div class="info-box-conetnt">';
													$output .= $list_title;
													$output .= $service_border;
													$output .= $list_desc;						
												$output .= '</div>';
											$output .= '</div>';
											$output .= '<div class="info-box-full color" '.$style8_css.'>';
											$output .= '</div>';						
										$output .= '</div>';
									$output .= '</div>';
								}
								if($main_style == 'style_11'){
									$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' content_hover_effect '. esc_attr($hover_class) .'" '.$the_service_main_css.'>';
										$output .= '<div class="info-box style-11 text-center">';	
											$output .= '<div class="info-box-all">';
												$output .= '<div class="info-box-wrapper ">';
													$output .= '<div class="info-box-conetnt">';
														$output .= '<div class="info-box-icon-img">';
															$output .= $list_img;
														$output .= '</div>';	
														$output .= $list_title;	
														$output .= '<div class="info-box-title-hide" '.$title_css.'> '.esc_html($title).' </div>';	
														$output .= $service_border;
														$output .= $list_desc;
													$output .= '</div>';
												$output .= '</div>';
											$output .= '</div>';
											$output .= '<div class="info-box-full color" '.$style8_css.'>';
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';	
								}
								$output .= '</div>';
							
							}
								
						}
					}
					if ($info_box_layout == 'single_layout'){	
						$output = '<div class="info-box-inner content_hover_effect '. esc_attr($hover_class) .'"  ' . $hover_attr . '>';	
						if($main_style == 'style_1'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).'" '.$the_service_main_css.'>';
								$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
								if($service_img != ''){				
									$output .= '<div class="m-r-16 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$service_img.' </div>';
								}
									$output .= '<div class="service-content ">';
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;
									$output .= '</div>';
									$output .= '<a class="all-link"></a>';
								$output .= '</div>';
							$output .= '</div>';	
						}
						if($main_style == 'style_2'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).'" '.$the_service_main_css.'>';
								$output .= '<div class="service-media text-right '.esc_attr($service_center).' ">';
									$output .= '<div class="service-content">';
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;
									$output .= '</div>';			
								if($service_img != ''){					
									$output .=  '<div class="m-l-16 '.esc_attr($serice_img_border).' " '.$border_right_css.'>'.$service_img.'</div>';
								}
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_3'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).'" '.$the_service_main_css.' >';
								$output .= '<div class="'.esc_attr($service_align).'">';
									$output .= '<div class="service-center  ">';
										$output .= $service_img;
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;
										$output .= '</div>';				
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_4'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).' '.esc_attr($serice_box_border).'" '.$the_service_main_css.'>';
								$output .= '<div class="">';
									$output .= '<div class="service-media service-left '.esc_attr($service_center).'">';
										$output .= $service_img;
										$output .= '<div class="service-content">';
											$output .= $service_title;
										$output .= '</div>';
									$output .= '</div>';	
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_5'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';
								$output .= '<div class="service-flipbox '.esc_attr($service_flip).' height-full" '.$service_flip_height.'>';
									$output .= '<div class="service-flipbox-holder height-full text-center perspective bezier-1"	>';
										$output .= '<div class="service-flipbox-front bezier-1 no-backface origin-center" '.$service_front_css.'>';
											$output .= '<div class="service-flipbox-content width-full">';
												$output .= $service_img;
												$output .= '<div class="service-content">';
													$output .= $service_title;
													$output .= $service_border;
												$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';	
										$output .= '<div class="service-flipbox-back fold-back-horizontal no-backface bezier-1 origin-center" '.$service_back_css.'>';
											$output .= '<div class="service-flipbox-content width-full">';
												$output .= $description;
												$output .= $service_btn;
											$output .= '</div>';
										$output .= '</div>';	
									$output .= '</div>';				
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_6'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';
								$output .= '<div class=" text-left '.esc_attr($service_center).' ">';			
									$output .= '<div class="top-content"> '.$service_img.' </div>';
									$output .= '<div class="bottom-content">';
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_7'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';
								$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
								if($service_img != ''){				
									$output .= '<div class="m-r-16 service-bg-7 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$service_img.' </div>';
								}
									$output .= '<div class="service-content ">';
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= $service_btn;	
									$output .= '</div>';
									
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_8'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';
								$output .= '<div class="about-post  '.esc_attr($service_center).' " '.$style8_css.'>';	
									$output .= '<div class="about-post-content" '.$header_css.'>';
										if($service_img != ''){		
											$output .='<a href="#" class="demo icon-middle '.$service_icon_style.'" '.$icon_css.'>';
											$output .= '<div class="service-bg-7 '.esc_attr($serice_img_border).'"> '.$service_img.' </div>';
											$output .= '</a>';
										}
										$output .= $service_title;
										$output .= $service_border;
									$output .= '</div>';	
									$output .= '<div class="hover-about">';	
										$output .= '<div classs="hiover-about-sub"><h3 class="service-sub-space" '.$sub_title_css.'> '.esc_html($sub_title).' </h3> </div>';
										$output .= '<div classs="about-hover-show">';
											$output .= $description;
											$output .= $service_btn;	
										$output .= '</div>';	
									$output .= '</div>';
									
								$output .= '</div>';
							$output .= '</div>';
						}
						
						if($main_style == 'style_9'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';
								$output .= '<div class="info-box style">';	
									$output .= '<div class="info-box-tag "><h3 class="info-tag-title service-sub-space" '.$sub_title_css.'> '.esc_html($sub_title).' </h3> </div>';
									$output .= '<div class="info-box-conetnt">';
										$output .= $service_title;
										$output .= $service_border;
										$output .= $description;
										$output .= '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' ><div data-label="'.esc_attr($btn_svg).'" class="action arrow-next expandable"><svg viewBox="0 0 36 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="icon-circle" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" transform="rotate(-90 17 17)"><circle cx="17" cy="17" r="17"></circle></g></svg> <svg width="15px" height="14px" viewBox="0 0 15 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-3.000000, -1.000000)" fill-rule="nonzero" fill="#FFFFFF" class="arrow-next-fill"><g id="arrow" transform="translate(3.000000, 1.000000)"><polygon id="Path-2" points="6.29289322 12.3410345 7.70710678 13.7552481 14.4624599 6.99989497 7.75545815 0.292893219 6.34124459 1.70710678 11.6340328 6.99989497"></polygon> <polygon id="Path-5" points="0 8 13 8 13 6 0 6"></polygon></g></g></g></svg></div></a>';
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_10'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';	
								$output .= '<div class="info-box style-10">';	
									$output .= '<div class="info-box-all">';
										$output .= '<div class="info-box "><h3 class="info-title service-sub-space" '.$sub_title_css.'> '.esc_html($sub_title).' </h3> </div>';
										$output .= '<div class="info-box-conetnt">';
											$output .= $service_title;
											$output .= $service_border;
											$output .= $description;						
										$output .= '</div>';
									$output .= '</div>';
									$output .= '<div class="info-box-full color" '.$style8_css.'>';
									$output .= '</div>';	
								$output .= '</div>';
							$output .= '</div>';
						}
						if($main_style == 'style_11'){
							$output .= '<div class="info-box-bg-box '.esc_attr($pd).' '.esc_attr($pd0).'" '.$the_service_main_css.'>';	
								$output .= '<div class="info-box style-11 text-center">';	
									$output .= '<div class="info-box-all">';
										$output .= '<div class="info-box-wrapper ">';
											$output .= '<div class="info-box-conetnt">';
												$output .= '<div class="info-box-icon-img">';
													$output .= $service_img;
												$output .= '</div>';	
												$output .= $service_title;	
												$output .= '<div class="info-box-title-hide" '.$title_css.'> '.esc_html($title).' </div>';	
												$output .= $service_border;
												$output .= $description;
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';
									$output .= '<div class="info-box-full color" '.$style8_css.'>';
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';	
						}
						$output .= '</div>';
					}
					
					
					$info_box ='<div class="pt_plus_info_box '.esc_attr($isotope).'  '.esc_attr($arrow_class).' '.esc_attr($el_class).'  '.esc_attr($uid).' info-box-'.esc_attr($main_style).' '.esc_attr($animated_class).'  '.esc_attr($service_space).' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'"  data-id="'.esc_attr($uid).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'"  '.$box_hover.' '.$box_hadow.' '.$hover_attr.' '.$data_slider.' >';
						$info_box .= '<div class="post-inner-loop">';
							$info_box .= $output;
						$info_box .='</div>';
					$info_box .='</div>';
				
				$css_rule='';
				$css_rule .= '<style >';
				if($column_space == 'on'){
					$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box .info-box-inner{padding : '.esc_js($column_space_pading).'; }';	
				}
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box.info-box-style_1 .info-box-inner:hover .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_2 .info-box-inner:hover .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_3 .info-box-inner:hover .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_4 .info-box-inner:hover .service-border-box{border-radius: '.esc_js($border_box_hvr_radius).';border-width: '.esc_js($border_box_hvr_width).';border-color: '.esc_js($border_box_hvr_color).' !important; }';
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box.info-box-style_1 .info-box-inner .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_2 .info-box-inner .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_3 .info-box-inner .service-border-box,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_4 .info-box-inner .service-border-box{border-radius: '.esc_js($border_box_radius).';border-width: '.esc_js($border_box_width).'; }';
			
				$css_rule .='.'.esc_js($uid).'.pt_plus_info_box.info-box-style_1 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_2 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_3 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_4 .info-box-inner .service-media,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_6 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_7 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_9 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_10 .info-box-inner .service-title{margin-bottom : '.esc_js($title_btm_space).'; }';

				$css_rule .='.'.esc_js($uid).'.pt_plus_info_box.info-box-style_8 .info-box-inner .service-sub-space,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_9 .info-box-inner .service-sub-space,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_10 .info-box-inner .service-sub-space{margin-bottom : '.esc_js($sub_btm_space).' ; }';

				$css_rule .='.'.esc_js($uid).'.pt_plus_info_box.info-box-style_3 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_5 .info-box-inner .service-title,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_6 .info-box-inner .service-sub-space,.'.esc_js($uid).'.pt_plus_info_box.info-box-style_10 .info-box-inner .service-title{margin-top : '.esc_js($title_top_space).'; }';
			if($main_style != 'style_5' && $main_style !=  'style_8'){
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box .info-box-inner .info-box-bg-box{-webkit-box-shadow: '.esc_js($box_shadow).';-moz-box-shadow: '.esc_js($box_shadow).';box-shadow: '.esc_js($box_shadow).';}.'.esc_js($uid).'.pt_plus_info_box .info-box-inner:hover .info-box-bg-box{-webkit-box-shadow: '.esc_js($hvr_box_shadow).';-moz-box-shadow: '.esc_js($hvr_box_shadow).';box-shadow: '.esc_js($hvr_box_shadow).';}';
			}
				$css_rule .= '.'.esc_js($uid).' .service-desc,.'.esc_js($uid).' .service-desc p{color: '.esc_js($desc_color).';font-size: '.esc_js($desc_size).';letter-spacing:'.esc_js($desc_letter_space).';font-family: '.esc_js($desc_family).';line-height: '.esc_js($desc_line).';} .'.esc_js($uid).'.pt_plus_info_box.info-box-style_8 .about-post:hover .hover-about{background: '.esc_js($box_hover_color).';}.'.esc_js($uid).'.pt_plus_info_box.info-box-style_8 .about-post:hover .icon-middle{background: '.esc_js($icon_bg_hvr_color).' !important; color:'.esc_js($icon_hvr_color).' !important; border-color: '.esc_js($icon_hvr_bdr_color).' !important; }';
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box.info-box-style_9 .action.arrow-next:before{color: '.esc_js($btn_svg_color).';font-size:'.esc_js($btn_svg_size).';}';
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box.info-box-style_9 .action.arrow-next:after{background-color: '.esc_js($btn_svg_color).';}.'.esc_js($uid).'.info-box-style_9 .info-box.style .action.expandable svg:first-child g circle{fill:none ; stroke: '.esc_js($btn_svg_color).' !important}.'.esc_js($uid).'.info-box-style_9 .info-box.style .action.expandable svg:nth-child(2) g polygon{fill:  '.esc_js($btn_svg_color).'  ; stroke: '.esc_js($btn_svg_color).' !important}';
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box.info-box-style_9:hover .info-box-inner .info-box-bg-box{background-color: '.esc_js($box_hover_color).' !important;}';
				if($button_check == 'true'){
					$css_rule .= include THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/button_css.php';
				}
				if($show_arrows!='true' && $show_dots!='true'){
				$css_rule .= '.'.esc_js($uid).'.pt_plus_info_box{margin-bottom:0;}';
				}
				$css_rule .= '</style>';
	
				return $css_rule.$info_box;
		}
		function init_tp_info_box(){
			if(function_exists("vc_map"))
			{
			require(THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/vc_arrays.php');
				vc_map( array(
					  "name" => __( "Info Box", "pt_theplus" ),
					  "base" => "tp_info_box",
					  "icon" => 'tp-info-box',
					  "category" => __( "The Plus", "pt_theplus"),
					  "description" => esc_html__('Stunning Sections with Style', 'pt_theplus'),
					  "params" => array(
						array(
						  "type"        => "dropdown",
						  'heading' =>  esc_html__('Select Layout', 'pt_theplus'),
						  "param_name"  => "info_box_layout",
						  "admin_label" => true,
						  "value"       => array(
						__( 'Individual Layout ', 'pt_theplus' ) => 'single_layout',
						__( 'Carousel Layout', 'pt_theplus' ) => 'carousel_layout',
						  ),
						  "std" => 'single_layout',
						  "description" => '',
						   ),
						  array(
							'type'        => 'radio_select_image',
							'heading' =>  esc_html__('Info Box Style', 'pt_theplus'), 
							'param_name'  => 'main_style',
							'admin_label' => true,
							'simple_mode' => false,
							'value' => 'style_1',
							'options'     => array(
								'style_1' => array(
								'tooltip' => esc_attr__('Style-1','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-1.jpg'
								),
								'style_2' => array(
								'tooltip' => esc_attr__('Style-2','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-2.jpg'
								),
								'style_3' => array(
								'tooltip' => esc_attr__('Style-3','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-3.jpg'
								),
								'style_4' => array(
								'tooltip' => esc_attr__('Style-4','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-4.jpg'
								),
								'style_5' => array(
								'tooltip' => esc_attr__('Style-5','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-5.jpg'
								),
								'style_6' => array(
								'tooltip' => esc_attr__('Style-6','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-6.jpg'
								),
								'style_7' => array(
								'tooltip' => esc_attr__('Style-7','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-7.jpg'
								),
								'style_8' => array(
								'tooltip' => esc_attr__('Style-8','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-8.jpg'
								),
								'style_9' => array(
								'tooltip' => esc_attr__('Style-9','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-9.jpg'
								),
								'style_10' => array(
								'tooltip' => esc_attr__('Style-10','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-10.jpg'
								),
								'style_11' => array(
								'tooltip' => esc_attr__('Style-11','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/info-box/ts-info-box-style-11.jpg'
								),
							),
						),
						array(
							'type'        => 'param_group',
							'heading'     => esc_html__( 'Content', 'pt_theplus' ),
							'param_name'  => 'loop_content',
							"dependency" => Array('element' => "info_box_layout", 'value' => 'carousel_layout'),
							'description' => '',
							'params'      => array(
								
										array(
								  "type" => "textfield",
								  "param_name" => "loop_title",
								  'heading' =>  esc_html__('Title of Info Box', 'pt_theplus'),
								  "value" => '',
								  'admin_label' => true,
								  "description" => ""
								),
								 array(
								  "type" => "textfield",
								  'heading' => esc_html__('Sub Title of Info Box', 'pt_theplus'),
								  "param_name" => "loop_sub",
								  "value" => '',
								  'admin_label' => true,
								  "description" => ""
								),
								array(
								  "type" => "textarea",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Description of Info Box using this option.','pt_theplus').'</span></span>'.esc_html__('Description of Info Box', 'pt_theplus')),
								  "param_name" => "loop_desc",
								  "value" => '',
								   "edit_field_class" => "vc_col-xs-12",
								  "description" => ""
								),
								array(
								  "type" => "dropdown",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon ', 'pt_theplus')),
								  "param_name" => "loop_image_icon",
								  "value" => array(
										__( 'None', 'pt_theplus' ) => '',
										__( 'Icon', 'pt_theplus' ) => 'icon',
										__( 'Image', 'pt_theplus' ) => 'image',
										__( 'Svg', 'pt_theplus' ) => 'svg',
									),
								  "std" => "",
								),
									array(
									  "type" => "dropdown",
									  "heading" => __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Svg Type', 'pt_theplus')),
									  "param_name" => "loop_svg_icon",
									  "value" => array(
											__( 'Image', 'pt_theplus' ) => 'img',
											__( 'Svg', 'pt_theplus' ) => 'svg_icon',
										),
										'dependency' => array(
											'element' => 'loop_image_icon',
											'value' => 'svg',
										),
									  "std" => "svg_icon",
									  'group' => __( 'Icon Option', 'pt_theplus' ),
									),
							array(
								'type' => 'attach_image',
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Custom SVG', 'pt_theplus')),
								'param_name' => 'loop_svg_image',
								'value' => '',
								'description' => "",
								'admin_label' => true,
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("img"), 
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
						
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose our tested drawable SVGs from this list.','pt_theplus').'</span></span>'.esc_html__('Pre Built SVG icon', 'pt_theplus')),
								'param_name' => 'loop_svg_d_icon',
								"value" => array(
								__("1. App", "pt_theplus") => "app.svg",
								__("2. Arrow", "pt_theplus") => "arrow.svg",
								__("3. Art", "pt_theplus") => "art.svg",
								__("4. Banknote", "pt_theplus") => "banknote.svg",
								__("5. Building", "pt_theplus") => "building.svg",
								__("6. Bulb-idea", "pt_theplus") => "bulb-idea.svg",
								__("7. Calendar", "pt_theplus") => "calendar.svg",
								__("8. Call", "pt_theplus") => "call.svg",
								__("9. Camera", "pt_theplus") => "camera.svg",
								__("10. Cart", "pt_theplus") => "cart.svg",
								__("11. Cd", "pt_theplus") => "cd.svg",
								__("12. Clip", "pt_theplus") => "clip.svg",
								__("13. Clock", "pt_theplus") => "clock.svg",
								__("14. Cloud", "pt_theplus") => "cloud.svg",
								__("15. Comment", "pt_theplus") => "comment.svg",
								__("16. Content-board", "pt_theplus") => "content-board.svg",
								__("17. Cup", "pt_theplus") => "cup.svg",
								__("18. Diamond", "pt_theplus") => "diamond.svg",
								__("19. Earth", "pt_theplus") => "earth.svg",
								__("20. Eye", "pt_theplus") => "eye.svg",
								__("21. Finger", "pt_theplus") => "finger.svg",
								__("22. Fingerprint", "pt_theplus") => "fingerprint.svg",
								__("23. Food", "pt_theplus") => "food.svg",
								__("24. Foundation", "pt_theplus") => "foundation.svg",
								__("25. Gear", "pt_theplus") => "gear.svg",
								__("26. Graphics-design", "pt_theplus") => "graphics-design.svg",
								__("27. Handshakeandshake", "pt_theplus") => "handshake.svg",
								__("28. Hard-disk", "pt_theplus") => "hard-disk.svg",
								__("29. Heart", "pt_theplus") => "heart.svg",
								__("30. Hook", "pt_theplus") => "hook.svg",
								__("31. Image", "pt_theplus") => "image.svg",
								__("32. Key", "pt_theplus") => "key.svg",
								__("33. Laptop", "pt_theplus") => "laptop.svg",
								__("34. Layers", "pt_theplus") => "layers.svg",
								__("35. List", "pt_theplus") => "list.svg",
								__("36. Location", "pt_theplus") => "location.svg",
								__("37. Loudspeaker", "pt_theplus") => "loudspeaker.svg",
								__("38. Mail", "pt_theplus") => "mail.svg",
								__("39. Map", "pt_theplus") => "map.svg",
								__("40. Mic", "pt_theplus") => "mic.svg",
								__("41. Mind", "pt_theplus") => "mind.svg",
								__("42. Mobile", "pt_theplus") => "mobile.svg",
								__("43. Mobile-comment", "pt_theplus") => "mobile-comment.svg",
								__("44. Music", "pt_theplus") => "music.svg",
								__("45. News", "pt_theplus") => "news.svg",
								__("46. Note", "pt_theplus") => "note.svg",
								__("47. Offer", "pt_theplus") => "offer.svg",
								__("48. Paperplane", "pt_theplus") => "paperplane.svg",
								__("49. Pendrive", "pt_theplus") => "pendrive.svg",
								__("50. Person", "pt_theplus") => "person.svg",
								__("51. Photography", "pt_theplus") => "photography.svg",
								__("52. Posisvg", "pt_theplus") => "posisvg.svg",
								__("53. Recycle", "pt_theplus") => "recycle.svg",
								__("54. Ruler", "pt_theplus") => "ruler.svg",
								__("55. Satelite", "pt_theplus") => "satelite.svg",
								__("56. Search", "pt_theplus") => "search.svg",
								__("57. Secure", "pt_theplus") => "secure.svg",
								__("58. Server", "pt_theplus") => "server.svg",
								__("59. Setting", "pt_theplus") => "setting.svg",
								__("60. Share", "pt_theplus") => "share.svg",
								__("61. Smiley", "pt_theplus") => "smiley.svg",
								__("62. Sound", "pt_theplus") => "sound.svg",
								__("63. Stack", "pt_theplus") => "stack.svg",
								__("64. Star", "pt_theplus") => "star.svg",
								__("65. Study", "pt_theplus") => "study.svg",
								__("66. Suitcase", "pt_theplus") => "suitcase.svg",
								__("67. Tag", "pt_theplus") => "tag.svg",
								__("68. Tempsvg", "pt_theplus") => "tempsvg.svg",
								__("69. Thumbsup", "pt_theplus") => "thumbsup.svg",
								__("70. Tick", "pt_theplus") => "tick.svg",
								__("71. Trash", "pt_theplus") => "trash.svg",
								__("72. Truck", "pt_theplus") => "truck.svg",
								__("73. Tv", "pt_theplus") => "tv.svg",
								__("74. User", "pt_theplus") => "user.svg",
								__("75. Video", "pt_theplus") => "video.svg",
								__("76. Video-production", "pt_theplus") => "video-production.svg",
								__("77. Wallet", "pt_theplus") => "wallet.svg",
							),
								'description' => "",
								'admin_label' => true,
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("svg_icon"),
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
								'std'=>'app.svg',
							),			
							array(
								"type" => "dropdown",
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose from different options of SVG draw animation. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('SVG Animation Type', 'pt_theplus')),
								"param_name" => "loop_svg_type",
								"value" => array(
									__("Delayed", "pt_theplus") => "delayed",
									__("Sync", "pt_theplus") => "sync",
									__("One-By-One", "pt_theplus") => "oneByOne",
									__("Script", "pt_theplus") => "script",
									__("Scenario-Sync", "pt_theplus") => "scenario-sync",
								),
								"description" => "",
								"std" =>'delayed',
								'admin_label'      => true,
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("svg_icon","img"), '',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								"type" => "textfield",
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set SVG draw Animation Duration using this option. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Animation Duration', 'pt_theplus')),
								"param_name" => "loop_duration",
								"value" => '30',
								'admin_label'=> true,
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("svg_icon","img"), '',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can setup Maximum Width of SVG here in Percentage or in Pixels from this option.','pt_theplus').'</span></span>'.esc_html__('Maximum Width', 'pt_theplus')),
								"param_name" => "loop_max_width",
								"value" => '100px',
								"description" => "",
								'admin_label'=> true,
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("svg_icon","img"), '',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose SVG&#39;s Stroke in Normal Terms Border Color Using This Option.','pt_theplus').'</span></span>'.esc_html__('Stroke(Border) Color', 'pt_theplus')),                 
								'edit_field_class' => 'vc_col-xs-6',
								'param_name' => 'loop_border_stroke_color',	
								"value" => '#ff0000',
								'dependency' => array(
									'element' => 'loop_svg_icon',
									'value' => array("svg_icon","img"), '',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose the Image Source from below options.','pt_theplus').'</span></span>'.esc_html__('Image Source', 'pt_theplus')),
							"param_name" => "loop_image_source",
							"value" => array(
								esc_html__('Media library', 'pt_theplus') => 'media_library',
								esc_html__('External link', 'pt_theplus') => 'externals_link',
							),
							'std' => 'media_library',
							"description" => '',
							'dependency' => array(
								'element' => 'loop_image_icon',
								'value' => 'image',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							),							
							array(
							"type" => "attach_image",
							"heading" => esc_html__("Use Image As icon", 'pt_theplus') ,
							"value" => "",
							"description" => '',  
							"param_name" => 'loop_select_image',
							'dependency' => array(
								'element' => 'loop_image_source',
								'value' => 'media_library',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
						),
						 array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select external link.','pt_theplus').'</span></span>'.esc_html__('External Image', 'pt_theplus')),
							'param_name' => 'loop_external_img',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'loop_image_source',
								'value' => array( 'externals_link')
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
						),
								
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' We have given options of icons from Font Awesome, Open Iconic, Linecons, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
									'value' => array(
										__( 'Font Awesome', 'pt_theplus' ) => 'fontawesome',
										__( 'Open Iconic', 'pt_theplus' ) => 'openiconic',
										__( 'Typicons', 'pt_theplus' ) => 'typicons',
										__( 'Entypo', 'pt_theplus' ) => 'entypo',
										__( 'Linecons', 'pt_theplus' ) => 'linecons',
										__( 'Mono Social', 'pt_theplus' ) => 'monosocial',
									),
									'admin_label' => false,
									'param_name' => 'type',
									'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
									'description' => '',
								),
								array(
									'type' => 'iconpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_fontawesome',
									'value' => 'fa fa-adjust', 
									'settings' => array(
										'emptyIcon' => false,
										'iconsPerPage' => 100,
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'fontawesome',
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
										'iconsPerPage' => 100,
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'openiconic',
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
										'iconsPerPage' => 100,
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'typicons',
									),
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
										'iconsPerPage' => 100, // default 100, how many icons per/page to display
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'entypo',
									),
								),
								array(
									'type' => 'iconpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'icon_linecons',
									'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
									'settings' => array(
										'emptyIcon' => false, // default true, display an "EMPTY" icon?
										'type' => 'linecons',
										'iconsPerPage' => 100, // default 100, how many icons per/page to display
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'linecons',
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
										'iconsPerPage' => 100, // default 100, how many icons per/page to display
									),
									'dependency' => array(
										'element' => 'type',
										'value' => 'monosocial',
									),
									'description' => '',
								),array(
									"type" => "dropdown",
									"heading" => __( "Icon Style", 'pt_theplus' ),
									"param_name" => "loop_icon_style", 
									"value" => "",
									"description" => "",
									"value"       => array(
										__( 'Select Style', 'pt_theplus' ) => '',
										__( 'Square', 'pt_theplus' ) => 'square',
										__( 'Rounded', 'pt_theplus' ) => 'rounded',
										__( 'Hexagon', 'pt_theplus' ) => 'hexagon',
										__( 'Pentagon', 'pt_theplus' ) => 'pentagon',
										__( 'Square Rotate', 'pt_theplus' ) => 'square-rotate',
									),
									'group' => __( 'Icon Option', 'pt_theplus' ),
									 "std" => "square",
									 'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
								   "admin_label" => false,					
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon Size for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
									'param_name' => 'loop_icon_size',
									'value' => array( 'Small' => 'small',
													  'Medium' => 'medium',
													  'Large' => 'large',
									) ,		
									'group' => __( 'Icon Option', 'pt_theplus' ),
									"std" => "small",
									'description' => "",
									'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
									),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
									'param_name' => 'loop_icon_color',
									'value' => '#0099CB',
									'description' => "",
									 'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
									"edit_field_class" =>'vc_col-xs-6',
									'group' => __( 'Icon Option', 'pt_theplus' ),
									),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon background using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Backgroung Color', 'pt_theplus')),
									'param_name' => 'loop_icon_bg_color',
									'value' => '',
									'description' => "",
									'dependency' => array(
										'element' => 'loop_icon_style',
										'value' =>  array("square","rounded","hexagon","pentagon","square-rotate"),
									),
									"edit_field_class" =>'vc_col-xs-6',
									'group' => __( 'Icon Option', 'pt_theplus' ),
									),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon border using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Border Color', 'pt_theplus')),
									'param_name' => 'loop_icon_border_color',
									'value' => '',
									'description' => "",
									'dependency' => array(
										'element' => 'loop_icon_style',
										'value' =>  array("square","rounded"),
									),
									"edit_field_class" =>'vc_col-xs-6',
									'group' => __( 'Icon Option', 'pt_theplus' ),
									),
									
								array(
									'param_name'  => 'loop_button_check',
									'heading'     => '',
									'description' => "",
									'type'        => 'checkbox',
									'value'       => array(
									  'Button' => 'true'
									),
									'group' => 'Button',
									"dependency" => array(
										 "element" => "main_style",
										 "value" => array("style_1","style_2","style_3","style_4","style_5","style_6","style_7"),
										)
								 ),
								 array(
										"type" => "textfield",						
										"class" => "",
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Text', 'pt_theplus')),
										"param_name" => "loop_btn_text",
										'value' => 'The Plus',
									   "description" => "",
									   "edit_field_class" => "vc_col-xs-6",
									   'dependency' => array(
										'element' => 'loop_button_check',
										'value' => 'true',
									),
									),
								array(
									'type' => 'vc_link',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add Button URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Button URL', 'pt_theplus')),
									'param_name' => 'loop_btn_url',
									'description' => ""
								),
								 array(
										'type' => 'dropdown',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' We have given options of icons from Font Awesome, Open Iconic, Linecons, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
										'value' => array(
											__('Select Icon', 'pt_theplus') => '',
											__('Font Awesome', 'pt_theplus') => 'fontawesome',
											__('Open Iconic', 'pt_theplus') => 'openiconic',
											__('Typicons', 'pt_theplus') => 'typicons',
											__('Entypo', 'pt_theplus') => 'entypo',
											__('Mono Social', 'pt_theplus') => 'monosocial'
										),
										'admin_label' => true,
										'std' => 'fontawesome',
										'param_name' => 'loop_btn_icon',
										'description' => '',
										'dependency' => array(
											'element' => 'loop_button_check',
											'value' => 'true',
											),
										
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_fontawesome',
										'value' => 'fa fa-arrow-right', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false,
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'fontawesome'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_openiconic',
										'value' => 'vc-oi vc-oi-dial',
										'settings' => array(
											'emptyIcon' => false,
											'type' => 'openiconic',
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'openiconic'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_typicons',
										'value' => 'typcn typcn-adjust-brightness',
										'settings' => array(
											'emptyIcon' => false,
											'type' => 'typicons',
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'typicons'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_entypo',
										'value' => 'entypo-icon entypo-icon-note',
										'settings' => array(
											'emptyIcon' => false,
											'type' => 'entypo',
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'entypo'
										)
										
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_linecons',
										'value' => 'vc_li vc_li-heart',
										'settings' => array(
											'emptyIcon' => false,
											'type' => 'linecons',
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'linecons'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
										'param_name' => 'loop_btn_icon_monosocial',
										'value' => 'vc-mono vc-mono-fivehundredpx',
										'settings' => array(
											'emptyIcon' => false,
											'type' => 'monosocial',
											'iconsPerPage' => 4000
										),
										'dependency' => array(
											'element' => 'loop_btn_icon',
											'value' => 'monosocial'
										),
										'description' => '',
									),
									array(
										"type" => "dropdown",
										'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Icon Position using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Position', 'pt_theplus')),
										"param_name" => "loop_btn_before_after",
										"value" => array(
											__("After Button", "pt_theplus") => "after",
											__("Before Button", "pt_theplus") => "before"
										),
										"description" => "",
										'dependency' => array(
										'element' => 'loop_button_check',
										'value' => 'true',
									),
										"std" => 'after',
									),
									
									array(
										'type' => 'dropdown',
										'heading' => __( 'Flip Box background options', 'pt_theplus' ),
										'param_name' => 'loop_front_options',
										"value" => array(
											__('Background Color', 'pt_theplus') => 'bg-color',
											__('Background Image', 'pt_theplus') => 'bg-image',
										),
										"dependency" => array(
														"element" => "main_style",
														"value" => "style_5",
													),
										"group" => 'Flip Box',				
									),
									array(
										'type' => 'colorpicker',
										'heading' => __( 'Flip Box Front Color', 'pt_theplus' ),
										'param_name' => 'loop_front_color',
										'value' => '#121212',
										"dependency" => array(
														"element" => "loop_front_options",
														"value" => "bg-color",
													),
										"group" => 'Flip Box',				
									),
									array(
										'type' => 'attach_image',
										'heading' => __( 'Flip Box Front Image', 'pt_theplus' ),
										'param_name' => 'loop_front_img',
										'value' => '',
										"dependency" => array(
														"element" => "loop_front_options",
														"value" => "bg-image",
													),
										"group" => 'Flip Box',				
									),
									
									array(
										'type' => 'dropdown',
										'heading' => __( 'Flip Box background options', 'pt_theplus' ),
										'param_name' => 'loop_back_options',
										"value" => array(
											__('Background Color', 'pt_theplus') => 'bg-color',
											__('Background Image', 'pt_theplus') => 'bg-image',
										),
										"dependency" => array(
														"element" => "main_style",
														"value" => "style_5",
													),
										"group" => 'Flip Box',				
									),
									array(
										'type' => 'colorpicker',
										'heading' => __( 'Flip Box back Color', 'pt_theplus' ),
										'param_name' => 'loop_back_color',
										'value' => '#5aa1e3',
										"dependency" => array(
														"element" => "loop_back_options",
														"value" => "bg-color",
													),
										"group" => 'Flip Box',				
									),
									array(
										'type' => 'attach_image',
										'heading' => __( 'Flip Box Back Image', 'pt_theplus' ),
										'param_name' => 'loop_back_img',
										'value' => '',
										"dependency" => array(
														"element" => "loop_back_options",
														"value" => "bg-image",
													),
										"group" => 'Flip Box',				
									),
									
							),
						),
						 array(
						  "type" => "textfield",
						  'heading' =>  esc_html__('Title Of Info Box', 'pt_theplus'),
						  "param_name" => "title",
						  "dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
						  "value" => 'The Plus', 
						  'admin_label' => true,
						  "description" => ""
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Title Options', 'pt_theplus'),
							'param_name'		=> 'title_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => 'Styles',
						),
 						array(
						  "type"        => "dropdown",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Title Color Options using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color Options', 'pt_theplus')),
						  "param_name"  => "title_color_o",
						  "admin_label" => true,
						  "value"       => array(
						  __( 'Solid', 'pt_theplus' ) => 'solid',
						  __( 'Gradient', 'pt_theplus' ) => 'gradient',
						),
						"std" => "solid",
						"description" => "",
						'group' => esc_attr__('Styles', 'pt_theplus'),
							),
			
						 array(
						  "type" => "colorpicker",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
						  "param_name" => "title_color",
						  "value" => '#ffffff',
						   "edit_field_class" => "vc_col-xs-6",
						  'group' => 'Styles',
'dependency' => array('element' => 'title_color_o','value' => 'solid'),
						  "description" => ""
						),
array(
		   'type' => 'colorpicker',
		   'heading' => __( 'Color 1', 'pt_theplus' ),
		   'param_name' => 'title_color1',  
			'dependency' => array('element' => 'title_color_o','value' => 'gradient'),
		   "edit_field_class" => "vc_col-xs-6",
		   "value" => '#1e73be',
		   'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Color 2', 'pt_theplus' ),
			'param_name' => 'title_color2',   
			'dependency' => array('element' => 'title_color_o','value' => 'gradient'),
			"edit_field_class" => "vc_col-xs-6",
			"value" => '#2fcbce',
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
				'type' => 'dropdown',
				'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
				'param_name' => 'title_hover_style',
				'value' => array(
					__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
					__( 'Vertical', 'pt_theplus' ) => 'vertical',
					__( 'Diagonal', 'pt_theplus' ) => 'diagonal',
					__( 'Radial', 'pt_theplus' ) => 'radial',                                
				),
			'std'=>'horizontal',
			'dependency' => array('element' => 'title_color_o','value' => 'gradient'),
			"description" => "",
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),
						array(
						  "type" => "textfield",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
						  "param_name" => "title_size",
						  "value" => '24px',
						  "description" => '',
						   "edit_field_class" => "vc_col-xs-6",
						   'group' => 'Styles',
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "title_line",
							'value' => '1.4',
						   "description" => "",
						   "edit_field_class" => "vc_col-xs-6",
						   'group' => 'Styles',
					   ),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "title_letter",
							'value' => '1px',
						   "description" => "",
						   "edit_field_class" => "vc_col-xs-6",
							'group' => 'Styles',
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
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'title_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'title_font_weight',
							'value' => __('600','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
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
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Title Bottom Space using this Option. E.g. 10px,20px,30px etc.','pt_theplus').'</span></span>'.esc_html__('Bottom Space', 'pt_theplus')),
							'param_name' => 'title_btm_space',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Title top Space using this Option. E.g. 10px,20px,30px etc.','pt_theplus').'</span></span>'.esc_html__('Top Space', 'pt_theplus')),
							'param_name' => 'title_top_space',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),
						),	
						array(
						  "type" => "textfield",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add sub title of info box using this option.','pt_theplus').'</span></span>'.esc_html__('Sub Title Of Info Box ', 'pt_theplus')),
						  "param_name" => "sub_title",
						  "value" => 'Creative Design',
						  "description" => '',
						  "dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
						  'dependency' => array(
								'element' => 'main_style',
								 'value' => array("style_8","style_9","style_10"),
							),
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Sub Title Options', 'pt_theplus'),
							'param_name'		=> 'subtitle_option_on',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => 'Styles',
							"description" => '',
							 'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
						),
						 array(
						  "type" => "colorpicker",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
						  "param_name" => "sub_title_color",
						  "value" => '#4d4d4d',
						   "edit_field_class" => "vc_col-xs-6",
						   'group' => 'Styles',
						  "description" => '',
						   'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
						),
						array(
						  "type" => "textfield",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
						  "param_name" => "sub_title_size",
						  "value" => '20px',
						  "description" => '',
						   "edit_field_class" => "vc_col-xs-6",
						   'group' => 'Styles',
							'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "sub_title_line",
							'value' => '1.4',
						   "description" => "",
						   'group' => 'Styles',
							'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
						   "edit_field_class" => "vc_col-xs-6",
							),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "sub_title_letter",
							'value' => '1px',
						   "description" => "",
						   "edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
							'group' => 'Styles',
							),	
							array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Subtitle Custom font family', 'pt_theplus'),
								'param_name' => 'subtitle_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'dependency' => array(
									'element' => 'main_style',
									'value' => array("style_8","style_9","style_10"),
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'subtitle_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
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
							'group' => esc_attr__('Styles', 'pt_theplus'),	
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
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Sub Title Bottom Space using this Option. E.g. 10px,20px,30px etc.','pt_theplus').'</span></span>'.esc_html__('Bottom Space', 'pt_theplus')),
							'param_name' => 'sub_btm_space',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_8","style_9","style_10"),
							),
							'group' => esc_attr__('Styles', 'pt_theplus'),
						),
							
							 array(
						  "type" => "textfield",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add title of button using this option.','pt_theplus').'</span></span>'.esc_html__('Button Name', 'pt_theplus')),
						  "param_name" => "btn_svg",
						  "value" => 'Info Link',
						  "description" => '',
						  'dependency' => array(
								'element' => 'main_style',
								 'value' => array("style_9"),
							),
							
						),	
							array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Button Options', 'pt_theplus'),
							'param_name'		=> 'button_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => 'Styles',
							 'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_9"),
							),
						),
						array(
						  "type" => "textfield",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add button size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Button size', 'pt_theplus')),
						  "param_name" => "btn_svg_size",
						  "value" => '14px',
						  "description" => '',
						  'dependency' => array(
								'element' => 'main_style',
								 'value' => array("style_9"),
							),
							
							'group' => 'Styles',
						),	
						 array(
						  "type" => "colorpicker",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Button Color', 'pt_theplus')),
						  "param_name" => "btn_svg_color",
						  "value" => '#4d4d4d',
						   "edit_field_class" => "vc_col-xs-6",
						  "description" => '',
						   'dependency' => array(
								'element' => 'main_style',
								'value' => array("style_9"),
							),
							'group' => 'Styles',
						),	
							
						array(
						  "type" => "dropdown",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon ', 'pt_theplus')),
						  "param_name" => "image_icon",
						  "value" => array(
								__( 'None', 'pt_theplus' ) => '',
								__( 'Icon', 'pt_theplus' ) => 'icon',
								__( 'Image', 'pt_theplus' ) => 'image',
								__( 'Svg', 'pt_theplus' ) => 'svg',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
						  "std" => "icon",
						  "dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
						),
						array(
						  "type" => "dropdown",
						  "heading" => __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Svg Type', 'pt_theplus')),
						  "heading" =>  esc_html__( 'Svg Select Option', 'pt_theplus' ),
						  "param_name" => "svg_icon",
						  "value" => array(
								__( 'Custom Upload', 'pt_theplus' ) => 'img',
								__( 'Pre Built SVG Icon', 'pt_theplus' ) => 'svg',
							),
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'svg',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
						  "std" => "svg",
						),
				$svg_attach,
				$svg_attach_icon,
				$svg_type,
				$svg_duration,
				$svg_width,
				$svg_border,		
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
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'image',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							),
						array(
							"type" => "attach_image",
							"heading" => esc_html__("Use Image As icon", 'pt_theplus') ,
							"value" => "",
							"description" => '',  
							"param_name" => 'select_image',
							'dependency' => array(
								'element' => 'image_source',
								'value' => 'media_library',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
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
							'group' => __( 'Icon Option', 'pt_theplus' ),
						),
					

						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' We have given options of icons from Font Awesome, Open Iconic, Linecons, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
							'value' => array(
								__( 'Font Awesome', 'pt_theplus' ) => 'fontawesome',
								__( 'Open Iconic', 'pt_theplus' ) => 'openiconic',
								__( 'Typicons', 'pt_theplus' ) => 'typicons',
								__( 'Entypo', 'pt_theplus' ) => 'entypo',
								__( 'Linecons', 'pt_theplus' ) => 'linecons',
								__( 'Mono Social', 'pt_theplus' ) => 'monosocial',
							),
							'param_name' => 'type',
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							'description' => '',
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_fontawesome',
							'value' => 'fa fa-adjust', 
							'settings' => array(
								'emptyIcon' => false,
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'type',
								'value' => 'fontawesome',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
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
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'type',
								'value' => 'openiconic',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
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
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'type',
								'value' => 'typicons',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
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
								'iconsPerPage' => 100, // default 100, how many icons per/page to display
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'entypo',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_linecons',
							'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
							'settings' => array(
								'emptyIcon' => false, // default true, display an "EMPTY" icon?
								'type' => 'linecons',
								'iconsPerPage' => 100, // default 100, how many icons per/page to display
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'linecons',
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
								'iconsPerPage' => 100, // default 100, how many icons per/page to display
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'monosocial',
							),
							'description' => '',
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Icon Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Styles', 'pt_theplus')),
							"param_name" => "icon_style", 
							"value" => "",
							"description" => "",
							"value"       => array(
								__( 'None', 'pt_theplus' ) => '',
								__( 'Square', 'pt_theplus' ) => 'square',
								__( 'Rounded', 'pt_theplus' ) => 'rounded',
								__( 'Hexagon', 'pt_theplus' ) => 'hexagon',
								__( 'Pentagon', 'pt_theplus' ) => 'pentagon',
								__( 'Square Rotate', 'pt_theplus' ) => 'square-rotate',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
							 "std" => "square",
							  "std" => "square",'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
						   "admin_label" => false,					
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon Size for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
							'param_name' => 'icon_size',
							'value' => array( 'Small' => 'small',
											  'Medium' => 'medium',
											  'Large' => 'large',
							) ,		
							'group' => __( 'Icon Option', 'pt_theplus' ),
							"std" => "small",
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
							'param_name' => 'icon_color',
							'value' => '#0099CB',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							"edit_field_class" =>'vc_col-xs-6',
							'group' => __( 'Icon Option', 'pt_theplus' ),
							),
								
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Hover Color', 'pt_theplus')),
							'param_name' => 'icon_hvr_color',
							'value' => '#ffffff',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'dependency' => array(
								'element' => 'main_style',
								'value' => 'style_8',
							),
							"edit_field_class" =>'vc_col-xs-6',
							'group' => __( 'Icon Option', 'pt_theplus' ),
							),	
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon background using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Background Color', 'pt_theplus')),
							'param_name' => 'icon_bg_color',
							'value' => '#ffffff',
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							
							'group' => __( 'Icon Option', 'pt_theplus' ),
							'description' => "",
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon background hover using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Background Hover Color', 'pt_theplus')),
							'param_name' => 'icon_bg_hvr_color',
							'value' => '#0099CB',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'dependency' => array(
								'element' => 'main_style',
								'value' => 'style_8',
							),
							"edit_field_class" =>'vc_col-xs-6',
							'group' => __( 'Icon Option', 'pt_theplus' ),
							),	
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon border using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Border Color', 'pt_theplus')),
							'param_name' => 'icon_border_color',
							'value' => '#121212',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'group' => __( 'Icon Option', 'pt_theplus' ),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon border hover using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Hover Border Color', 'pt_theplus')),
							'param_name' => 'icon_hvr_bdr_color',
							'value' => '#ffffff',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'dependency' => array(
								'element' => 'main_style',
								'value' => 'style_8',
							),
							"edit_field_class" =>'vc_col-xs-6',
							'group' => __( 'Icon Option', 'pt_theplus' ),
						),
						array(
							"type" => "textarea_html",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add description of Info Box using this option.','pt_theplus').'</span></span>'.esc_html__('Description Of Info Box', 'pt_theplus')),
							"param_name" => "content", 
							"value" => "",
							"description" => "",
							 "dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Description Options', 'pt_theplus'),
							'param_name'		=> 'description_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => __( 'Styles', 'pt_theplus' ),
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "desc_color",
							'group' => __( 'Styles', 'pt_theplus' ),
							"value" => '#888888',
							"description" => '',
							 "edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "desc_size",
							'group' => __( 'Styles', 'pt_theplus' ),
							"value" => '14px',
							"description" => '',
							 "edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add letter spacing in Pixels using this option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "desc_letter_space",
							'group' => __( 'Styles', 'pt_theplus' ),
							"value" => '',
							"description" => '',
							 "edit_field_class" => "vc_col-xs-6",
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Description Custom font family', 'pt_theplus'),
								'param_name' => 'desc_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'desc_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'desc_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'desc_font_weight',
							'value' => __('600','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'desc_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'desc_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'desc_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),	
						
						array(
							"type" => "textfield",
							"class" => "",
							'group' => __( 'Styles', 'pt_theplus' ),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "desc_line",
							'value' => '30px',
						   "description" => "",
						   "edit_field_class" => "vc_col-xs-6",
							),
						array(
							'param_name'  => 'button_check',
							'heading'     => '',
							'description' => __( 'checkbox false no Button...', 'pt_theplus' ),
							'type'        => 'checkbox',
							'value'       => array(
							  'Button' => 'true'
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_1","style_2","style_3","style_4","style_5","style_6","style_7"),
								)
						 ),
						
						
						array(
							'type' => 'vc_link',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('dd Button URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Button URL', 'pt_theplus')),
							'param_name' => 'btn_url',
							"group" => esc_attr__('Button', 'pt_theplus'),
							'description' => '',
							"dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
						),
						array(
							'type'        => 'radio_select_image',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Button Styles using this option','pt_theplus').'</span></span>'.esc_html__('Button Style', 'pt_theplus')), 
							'param_name'  => 'style',
							'simple_mode' => false,
							'value'  => 'style_1',
							"group" => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
							 'dependency' => array(
								'element' => 'style',
								'value' => array(
									'style-1','style-2','style-3','style-4','style-5','style-6','style-7','style-8','style-9','style-10','style-11','style-12','style-13','style-14','style-15','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-23'
								)
							),
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write on hover title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Hover Text', 'pt_theplus')),
							"param_name" => "btn_hover_text",
							"value" => '',
							'description' => '',
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
							'value' => array(
								__('Select Icon', 'pt_theplus') => '',
								__('Font Awesome', 'pt_theplus') => 'fontawesome',
								__('Open Iconic', 'pt_theplus') => 'openiconic',
								__('Typicons', 'pt_theplus') => 'typicons',
								__('Linecons', 'pt_theplus') => 'linecons',
								__('Entypo', 'pt_theplus') => 'entypo',
								__('Mono Social', 'pt_theplus') => 'monosocial'
							),
							'std' => 'fontawesome',
							"group" => esc_attr__('Button', 'pt_theplus'),
							'param_name' => 'btn_icon',
							 "dependency" => Array('element' => "info_box_layout", 'value' => 'single_layout'),
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
							'param_name' => 'btn_icon_fontawesome',
							'value' => 'fa fa-arrow-right', // default value to backend editor admin_label
							'settings' => array(
								'emptyIcon' => false,
								'iconsPerPage' => 4000
							),
							'dependency' => array(
								'element' => 'btn_icon',
								'value' => 'fontawesome'
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
							'description' => '',
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'btn_icon_openiconic',
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
							"group" => esc_attr__('Button', 'pt_theplus'),
							'description' => '',
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'btn_icon_typicons',
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
							"group" => esc_attr__('Button', 'pt_theplus'),
							'description' => '',
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'btn_icon_entypo',
							'value' => 'entypo-icon entypo-icon-note',
							'settings' => array(
								'emptyIcon' => false,
								'type' => 'entypo',
								'iconsPerPage' => 4000
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
							'dependency' => array(
								'element' => 'btn_icon',
								'value' => 'entypo'
							)
							
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'btn_icon_linecons',
							'value' => 'vc_li vc_li-heart',
							'settings' => array(
								'emptyIcon' => false,
								'type' => 'linecons',
								'iconsPerPage' => 4000
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
							'dependency' => array(
								'element' => 'btn_icon',
								'value' => 'linecons'
							),
							
							'description' => '',
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'btn_icon_monosocial',
							'value' => 'vc-mono vc-mono-fivehundredpx',
							'settings' => array(
								'emptyIcon' => false,
								'type' => 'monosocial',
								'iconsPerPage' => 4000
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
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
								__("After Icon", "pt_theplus") => "after",
								__("Before Icon", "pt_theplus") => "before"
							),
							"description" => "",
							"std" => 'after',
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Button Text Style', 'pt_theplus'),
							'param_name' => 'text_style',
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							
							"group" => esc_attr__('Button Style', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "font_size",
							"value" => '20px',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Button Style', 'pt_theplus'),
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "line_height",
							"value" => '25px',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							"group" => esc_attr__('Button Style', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "letter_spacing",
							"value" => '1px',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							"group" => esc_attr__('Button Style', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Setup Inner Padding top-bottom and right-left to Button from this option. E.g. 15px 20px, 30px 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Inner Padding ', 'pt_theplus')),
							"param_name" => "btn_padding",
							"value" => '15px 30px',
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Button Style', 'pt_theplus'),
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							'description' => '',
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button text color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
							'param_name' => 'text_color',
							"value" => '#8a8a8a',
							"description" => "",
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button hover text color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Hover Color', 'pt_theplus')),
							'param_name' => 'text_hover_color',
							"value" => '#252525',
							"description" => "",
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
								'group' => esc_attr__('Button Style', 'pt_theplus'),
								"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'btn_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
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
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
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
								'group' => esc_attr__('Button Style', 'pt_theplus'),	
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Tablet Responsive', 'pt_theplus'),
							'param_name' => 'tablet_text_style',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
							"param_name" => "tablet_font_size",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
						   'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "tablet_line_height",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "tablet_letter_spacing",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Setup Inner Padding top-bottom and right-left to Button from this option. E.g. 15px 20px, 30px 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Tablet Inner Padding ', 'pt_theplus')),
							"param_name" => "tablet_btn_padding",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Mobile Responsive', 'pt_theplus'),
							'param_name' => 'mobile_text_style',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
							"param_name" => "mobile_font_size",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "mobile_line_height",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "mobile_letter_spacing",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Setup Inner Padding top-bottom and right-left to Button from this option. E.g. 15px 20px, 30px 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Mobile Inner Padding ', 'pt_theplus')),
							"param_name" => "mobile_btn_padding",
							"value" => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Button Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'btn_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Border Style', 'pt_theplus'),
							'param_name' => 'border_style',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Button Style', 'pt_theplus'),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button border color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							'param_name' => 'border_color',
							"value" => '#252525',
							"description" => "",
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select button border hover color and Opacity for button using this option.','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
							'param_name' => 'border_hover_color',
							"value" => '#252525',
							"description" => "",
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							"group" => esc_attr__('Button Style', 'pt_theplus'),
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
								__("Bg Image", "pt_theplus") => "image"
							),
							"description" => "",
							"std" => 'normal',
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'heading' => __('Color', 'pt_theplus'),
							'param_name' => 'normal_bg_color',
							
							"description" => "",
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_bg_option',
								'value' => 'gradient'
							)
						),
						array(
							'type' => 'attach_image',
							'heading' => __('Bg Image', 'pt_theplus'),
							'param_name' => 'bg_image',
							'value' => '',
							'group' => esc_attr__('Style', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_bg_option',
								'value' => 'image'
							)
						),
						array(
							"type" => "dropdown",
							"heading" => __("Hover Background Option", "pt_theplus"),
							"param_name" => "select_bg_hover_option",
							"value" => array(
								__("Normal color", "pt_theplus") => "normal",
								__("Gradient color", "pt_theplus") => "gradient",
								__("Bg Image", "pt_theplus") => "image"
							),
							"description" => "",
							"std" => 'normal',
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'heading' => __('Hover Bg color', 'pt_theplus'),
							'param_name' => 'normal_bg_hover_color',
							"description" => "",
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							"value" => '#ff214f',
							'dependency' => array(
								'element' => 'select_bg_hover_option',
								'value' => 'normal'
							),
						),
						
						array(
							'type' => 'colorpicker',
							'heading' => __('Hover Color 1', 'pt_theplus'),
							'param_name' => 'gradient_hover_color1',
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_bg_hover_option',
								'value' => 'gradient'
							),
							"edit_field_class" => "vc_col-xs-6",
							"value" => '#1e73be'
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Hover Color 2', 'pt_theplus'),
							'param_name' => 'gradient_hover_color2',
							'group' => esc_attr__('Button Style', 'pt_theplus'),
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
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_bg_hover_option',
								'value' => 'gradient'
							),
						),
						array(
							'type' => 'attach_image',
							'heading' => __('Bg Hover Image', 'pt_theplus'),
							'param_name' => 'bg_hover_image',
							'value' => '',
							'group' => esc_attr__('Button Style', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_bg_hover_option',
								'value' => 'image'
							),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Hover Button Shadow', 'pt_theplus'),
							'param_name' => 'btn_hover_shadow',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							"group" => esc_attr__('Button Style', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							"heading" => esc_html__("Hover Button Shadow", 'pt_theplus'),
							"param_name" => "hover_shadow",
							"value" => '',
							'description' => __(' ,', 'pt_theplus'),
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Button Style', 'pt_theplus'),
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
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
							"group" => esc_attr__('Button', 'pt_theplus'),
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
							"dependency" => array(
								 "element" => "button_check",
								 "value" => array("true"),
							),
							"group" => esc_attr__('Button', 'pt_theplus'),
							"description" => ""
						),
						
						
						
						
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Carousel Setting', 'pt_theplus'),
							'param_name'		=> 'carousel_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Carousel', 'pt_theplus'),
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Desktop screen size( More than 768px width).','pt_theplus').'</span></span>'.esc_html__('Desktop Columns', 'pt_theplus')), 
							"param_name"  => "carousel_column",
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'4',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Tablet screen size( In between 768px and 480px width).','pt_theplus').'</span></span>'.esc_html__('Tablet Columns', 'pt_theplus')), 
							"param_name"  => "carousel_tablet_column",
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Mobile screen size( Less than 480px width).','pt_theplus').'</span></span>'.esc_html__('Mobile Columns', 'pt_theplus')), 
							"param_name"  => "carousel_mobile_column",
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'2',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can display or hide arrows of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Arrows', 'pt_theplus')),
							'param_name' => 'show_arrows',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
								"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can display or hide navigation dots of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots', 'pt_theplus')),
							'param_name' => 'show_dots',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
								"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Turn on or Off Mouse Draggable functionality of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Draggable', 'pt_theplus')),
							'param_name' => 'show_draggable',
							'description' => '',
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Loop or Infinite style of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Infinite Mode', 'pt_theplus')),
							'param_name' => 'slide_loop',
							'description' => __( '.', 'pt_theplus' ),
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Turn on Auto play functionality of Carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Auto Play', 'pt_theplus')),
							'param_name' => 'slide_autoplay',
							'description' => '',
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							  "type"        => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter speed of autoplay carousel functionality. e.g. 2000,3000 etc.','pt_theplus').'</span></span>'.esc_html__('Autoplay Speed', 'pt_theplus')),
							  "param_name"  => "autoplay_speed",
							  "value"       => '3000',
							  "description" => "",
							  "edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							),
							"dependency" => array(
								"element" => "slide_autoplay",
								"value" => array("true"),
							),
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select option of column scroll on previous or next in carousel.','pt_theplus').'</span></span>'.esc_html__('Next Previous', 'pt_theplus')),
							"param_name" => "steps_slide",
							"value" => array(
								__("One Column", "pt_theplus") => "1",
								__("All Visible Columns", "pt_theplus") => "2",
							),    
							"std" =>'1',
							"description" => "", 
							  "edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
						),
						array(
								'type'        => 'radio_select_image',
								'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('You can select styles of navigation dots using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Navigation Dots Style', 'pt_theplus')),
								'param_name'  => 'dots_style',
								'simple_mode' => false,
								'value' => 'style-3',
								'options'     => array(
									'style-3' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-1.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-2.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-3.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-4.jpg'
									),
									'style-10' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-5.jpg'
									),
									'style-9' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-6.jpg'
									),
									'style-11' => array(
										'tooltip' => esc_attr__('Style-7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-7.jpg'
									),
								),
								'group' => esc_attr__('Carousel', 'pt_theplus'),
								"dependency" => array(
									"element" => "layout",
									"value" => array(
										"carousel"
									)
								),
								"dependency" => array(
									"element" => "show_dots",
									"value" => array(
										"true"
									)
								)
							),
						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for navigation dot border using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots Border Color', 'pt_theplus')),
							'param_name' => 'dots_border_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-2","style-3","style-4","style-6","style-7","style-10"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for navigation dot background using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots Background Color', 'pt_theplus')),
							'param_name' => 'dots_bg_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-2","style-4","style-5","style-6","style-7","style-8","style-9","style-11","style-12","style-13"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active navigation dot Border using this option.','pt_theplus').'</span></span>'.esc_html__('Active Navigation Dots Border Color', 'pt_theplus')),
							'param_name' => 'dots_active_border_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-4","style-7","style-10"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active navigation dot background using this option.','pt_theplus').'</span></span>'.esc_html__('Active Navigation Dots Background Color', 'pt_theplus')),
							'param_name' => 'dots_active_bg_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-4","style-5","style-7","style-8","style-9","style-11","style-12","style-13"),
							),
						),
						array(
								'type'        => 'radio_select_image',
								'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('You can select styles of navigation dots using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Arrow Style', 'pt_theplus')),
								'param_name'  => 'arrows_style',
								'simple_mode' => false,
								'value' => 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-1.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-2.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-3.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-4.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-5.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-6.jpg'
									),
								),
								'group' => esc_attr__('Carousel', 'pt_theplus'),
								"dependency" => array(
									"element" => "layout",
									"value" => array(
										"carousel"
									)
								),
								"dependency" => array(
								"element" => "show_arrows",
								"value" => array(
									"true"
									)
								)
							),
						array(
							"type" => "dropdown",
							"heading" => __("Arrow Position", "pt_theplus"),
							"param_name" => "arrows_position",
							"value" => array(
								__("Top-Right", "pt_theplus") => "top-right",
								__("Bottom-Left", "pt_theplus") => "bottm-left",
								__("Bottom-Center", "pt_theplus") => "bottom-center",
								__("Bottom-Right", "pt_theplus") => "bottom-right",
							),    
							"std" =>'top-right',
							"description" => "", 
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-4","style-5"),
							),
							
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow background using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Background Color', 'pt_theplus')),
							'param_name' => 'arrow_bg_color',			
							'value' =>'#c44d48',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-7"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow icon using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Icon Color', 'pt_theplus')),
							'param_name' => 'arrow_icon_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-6","style-7"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow hover background using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Hover Background Color', 'pt_theplus')),
							'param_name' => 'arrow_hover_bg_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5"),
							),
						),
						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow hover icon using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Hover Icon Color', 'pt_theplus')),
							'param_name' => 'arrow_hover_icon_color',			
							'value' =>'#c44d48',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-6","style-7"),
							),
						),
						
						
						
						
						
						
						
						
						
						
						
						
						array(
						  "type"        => "dropdown",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose info box alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Info Box Alignment', 'pt_theplus')),
						  "param_name"  => "text_align",
						  "value"       => array(
								__( 'Select Style', 'pt_theplus' ) => '',
								__( 'Left', 'pt_theplus' ) => 'left',
								__( 'Center', 'pt_theplus' ) => 'center',
								__( 'Right', 'pt_theplus' ) => 'right',
							),
						  "std" => "center",
						  "description" => '',
						  "dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_3"),
							),
						), 
						array(
							'param_name'  => 'border_check',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' By checking up this option you can turn on underline/border under the title.','pt_theplus').'</span></span>'.esc_html__('Title Underline', 'pt_theplus')),
							'description' => '',
							'type'        => 'checkbox',
							'value'       => array(
							  'Border' => 'true',
							),
							'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_width',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Border Width using this option.','pt_theplus').'</span></span>'.esc_html__('Border Width', 'pt_theplus')),
							'heading'     => __( 'Border Width', 'pt_theplus' ),
							'description' => '',
							'type'        => 'dropdown',
							'edit_field_class' => 'vc_col-sm-4',
							"value"       => array(
								__( 'Select width', 'pt_theplus' ) => '',
								__( '10%', 'pt_theplus' ) => '10%',
								__( '20%', 'pt_theplus' ) => '20%',
								__( '30%', 'pt_theplus' ) => '30%',
								__( '40%', 'pt_theplus' ) => '40%',
								__( '50%', 'pt_theplus' ) => '50%',
								__( '60%', 'pt_theplus' ) => '60%',
								__( '70%', 'pt_theplus' ) => '70%',
								__( '80%', 'pt_theplus' ) => '80%',
								__( '90%', 'pt_theplus' ) => '90%',
								__( '100%', 'pt_theplus' ) => '100%',
								
							),
							"std" => "100%",
							"dependency" => array(
							 "element" => "border_check",
							 "value" => array("true"),
							),
							'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_height',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set border height using this option. E.g. 1px, 2px, 3px, etc.','pt_theplus').'</span></span>'.esc_html__('Border Height', 'pt_theplus')),
							'description' => '',
							'type'        => 'textfield',
							'edit_field_class' => 'vc_col-sm-4',
							"value" => '2px',
							"dependency" => array(
							 "element" => "border_check",
							 "value" => array("true"),
							),
							 'group'     =>'Border',
						 ),
							array(
							'param_name'  => 'title_border_color',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							'description' => '',
							'type'        => 'colorpicker',
							'edit_field_class' => 'vc_col-sm-4',
							'value'    =>'#252525',
							"dependency" => array(
								"element" => "border_check",
								"value" => array("true"),
							),
							'group'     =>'Border',
							),
						 array(
							'param_name'  => 'border_check_box',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By checking this option up you can turn on border around the info box.','pt_theplus').'</span></span>'.esc_html__('Box Border', 'pt_theplus')),
							'description' => '',
							'type'        => 'checkbox',
							'value'       => array(
							  'Border box' => 'true',
							),
							"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_1","style_2","style_3","style_4"),
								),
								'edit_field_class' => 'vc_col-sm-6',
							'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_box_color',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Color', 'pt_theplus')),
							'description' => '',
							'type'        => 'colorpicker',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-6',
								'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_box_width',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add border width in px for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Width', 'pt_theplus')),
							'description' => '',
							'type'        => 'textfield',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-6',
								'group'     =>'Border',
						 ),
 array(
							'param_name'  => 'border_box_radius',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add border radius in px for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Radius', 'pt_theplus')),
							'description' => '',
							'type'        => 'textfield',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-6',
								'group'     =>'Border',
						 ),

						 array(
							'param_name'  => 'border_box_hvr_color',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Hover Color', 'pt_theplus')),
							'description' => '',
							'type'        => 'colorpicker',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-4',
								'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_box_hvr_width',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add border width in px for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Hover Width', 'pt_theplus')),
							'description' => '',
							'type'        => 'textfield',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-4',
								'group'     =>'Border',
						 ),
 array(
							'param_name'  => 'border_box_hvr_radius',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add border radius in px for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Box Hover Radius', 'pt_theplus')),
							'description' => '',
							'type'        => 'textfield',
							"dependency" => array(
							 "element" => "border_check_box",
							 "value" => array("true"),
							),
							'edit_field_class' => 'vc_col-sm-4',
								'group'     =>'Border',
						 ),

						 array(
							'param_name'  => 'border_check_right',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This option is for selected styles only. Using this option you can set options of side image of info box.','pt_theplus').'</span></span>'.esc_html__('Side image Border', 'pt_theplus')),
							'description' => '',
							'type'        => 'checkbox',
							
							'value'       => array(
							  'Border' => 'true',
							),
							'edit_field_class' => 'vc_col-sm-6',
							"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_1","style_2"),
								),
							'group'     =>'Border',
						 ),
						 array(
							'param_name'  => 'border_right_color',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Right Color', 'pt_theplus')),
							'description' => '',
							'type'        => 'colorpicker',
							'edit_field_class' => 'vc_col-sm-6',
							"dependency" => array(
							 "element" => "border_check_right",
							 "value" => array("true"),
							 ),
							 'group'     =>'Border',
							
						 ),
						 array(
							"type" => "textfield",
							"heading" => esc_html__("Flip Box Height", 'pt_theplus') ,
							"param_name" => "flip_height",
							"value" => "300px",
							"description" => '',  
							"dependency" => array(
									"element" => "main_style",
									"value" => "style_5",
									),	
							"group" => 'Flip Box',
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Select Flip Box Style', 'pt_theplus' ),
							'param_name' => 'flip_style',
							'value' => array(
								__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
								__( 'Vertical ', 'pt_theplus' ) => 'vertical',
							),
							'std' => 'horizontal',
							"dependency" => array(
											"element" => "main_style",
											"value" => "style_5",
										),
							"group" => 'Flip Box',				
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Flip Box Front Color', 'pt_theplus' ),
							'param_name' => 'front_color',
							'value' => '#121212',
							"dependency" => array(
											"element" => "main_style",
											"value" => "style_5",
										),
							"group" => 'Flip Box',				
						),
						array(
							'type' => 'attach_image',
							'heading' => __( 'Flip Box Front Image', 'pt_theplus' ),
							'param_name' => 'front_img',
							'value' => '',
							"dependency" => array(
											"element" => "main_style",
											"value" => "style_5",
										),
							"group" => 'Flip Box',				
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Flip Box back Color', 'pt_theplus' ),
							'param_name' => 'back_color',
							'value' => '#5aa1e3',
							"dependency" => array(
											"element" => "main_style",
											"value" => "style_5",
										),
							"group" => 'Flip Box',				
						),
						array(
							'type' => 'attach_image',
							'heading' => __( 'Flip Box Back Image', 'pt_theplus' ),
							'param_name' => 'back_img',
							'value' => '',
							"dependency" => array(
											"element" => "main_style",
											"value" => "style_5",
										),
							"group" => 'Flip Box',				
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Background Options', 'pt_theplus'),
							'param_name'		=> 'background_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => 'Styles',
								"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_1","style_2","style_3","style_4","style_6","style_7","style_8","style_9","style_10"),
								),
						),
						  array(
						   'type' => 'colorpicker',
						   'heading' => __( 'Background Color', 'pt_theplus' ),
						   'param_name' => 'box_bg_color',
						   'value' => '#ff004b',
						   'description' => '',
						   "dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_1","style_2","style_3","style_4","style_6","style_7","style_8","style_9","style_10","style_11"),
							),
							'group' => 'Styles',
						   ),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Background Hover Color', 'pt_theplus' ),
							'param_name' => 'box_hover_color',
							'value' => '#0099cc',
							'description' => '',
							"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_8","style_9"),
								),
								'group' => 'Styles',
							),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Header Background Color', 'pt_theplus' ),
							'param_name' => 'head_bg_color',
							'value' => '#ffffff',
							'description' => '',
							"dependency" => array(
								 "element" => "main_style",
								 "value" => array("style_8"),
								),
								'group' => 'Styles',
							),	
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Box Shadow Setting', 'pt_theplus'),
							'param_name'		=> 'boxshadow_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styles', 'pt_theplus'), 
							"dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_1","style_2","style_3","style_4","style_7","style_6","style_9","style_10","style_11"),
							),
						),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Box Shadow ', 'pt_theplus')),
							'param_name' => 'box_shadow',
							'value' => '1px 1px 3px 3px rgba(0, 0, 0, 0.15)',
							'group' => 'Styles',
							'edit_field_class'	=> 'vc_col-sm-6',
							'description' => __( ' ', 'pt_theplus' ),
							"dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_1","style_2","style_3","style_4","style_7","style_6","style_9","style_10","style_11"),
							),
							),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Hover Box Shadow ', 'pt_theplus')),
							'param_name' => 'hvr_box_shadow',
							'edit_field_class'	=> 'vc_col-sm-6',
							'value' => '0 22px 43px rgba(0, 0, 0, 0.15)',
							'group' => 'Styles',
							'description' => __( ' ', 'pt_theplus' ),
							"dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_1","style_2","style_3","style_4","style_7","style_6","style_9","style_10","style_11"),
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This Effects will be applied when you hover on this section.','pt_theplus').'</span></span>'.esc_html__('Content Hover Effects', 'pt_theplus')),
							"param_name" => "content_hover_effects",
							"value" => array(
								__('Select Hover Effect', 'pt_theplus') => '',
								__('Grow', 'pt_theplus') => 'grow',
								__('Push', 'pt_theplus') => 'push',
								__('Bounce In', 'pt_theplus') => 'bounce-in',
								__('Float', 'pt_theplus') => 'float',
								__('wobble horizontal', 'pt_theplus') => 'wobble_horizontal',
								__('Wobble Vertical', 'pt_theplus') => 'wobble_vertical',
								__('Float Shadow', 'pt_theplus') => 'float_shadow',
								__('Grow Shadow', 'pt_theplus') => 'grow_shadow',
								__('Shadow Radial', 'pt_theplus') => 'shadow_radial'
							),
							"description" => '',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Shadow Color', 'pt_theplus'),
							'param_name' => 'hover_shadow_color',
							'value' => 'rgba(0, 0, 0, 0.6)',
							'edit_field_class' => 'vc_col-sm-6',
							'description' => '',
							'dependency' => array(
								'element' => 'content_hover_effects',
								'value' => array(
									'float_shadow',
									'grow_shadow',
									'shadow_radial'
								)
							)
						),
						 array(
							  "type"        => "dropdown",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							  "param_name"  => "animation_effects",
							  "admin_label" => false,
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
							  'edit_field_class' => 'vc_col-sm-6',
							  'std' =>'no-animation',
						),		
						array(
							  "type"        => "textfield",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
							  "param_name"  => "animation_delay",
							  "value"       => '50',
							  'edit_field_class' => 'vc_col-sm-6',
							  "description" => "",
						),	
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Extra Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							'param_name'  => 'padding_top',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Padding Top using this option. E.g. 20px, 50px, 70px, etc.','pt_theplus').'</span></span>'.esc_html__('Padding Top', 'pt_theplus')),
							'description' => "",
							'type'        => 'textfield',          
							'edit_field_class' => 'vc_col-sm-6',
						 ),
						 array(
							'param_name'  => 'padding_boottom',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Padding Bottom using this option. E.g. 20px, 50px, 70px, etc.','pt_theplus').'</span></span>'.esc_html__('Padding Bottom', 'pt_theplus')),
							'description' => "",
							'type'        => 'textfield',     
							'edit_field_class' => 'vc_col-sm-6',
						 ),
						 array(
							'param_name'  => 'vertical_center',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' By checking this option up You can By checking this option You can make stylish list content vertical center.','pt_theplus').'</span></span>'.esc_html__('Vertical Center', 'pt_theplus')),
							'description' => '',
							'type'        => 'checkbox',
							'value'       => array(
							  'Vertical center alignment' => 'true'
							),
							'edit_field_class' => 'vc_col-sm-6',
							 "dependency" => array(
							 "element" => "main_style",
							 "value" => array("style_1","style_2","style_4"),
							),
						 ),
						array(
							'param_name'  => 'remove_padding',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' By checking this option up You can remove column padding of info boxes.','pt_theplus').'</span></span>'.esc_html__('Column Padding Remove', 'pt_theplus')),
							'type'        => 'checkbox',
							'edit_field_class' => 'vc_col-sm-6',
							'value'       => array(
							  'Remove' => 'true',
							),
						 ),
						 array(
							'param_name'  => 'remove_cl_padding',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By checking this option up You can remove internal padding of info boxes.','pt_theplus').'</span></span>'.esc_html__('Internal Padding Remove', 'pt_theplus')),
							'description' => __( ' ', 'pt_theplus' ),
							'type'        => 'checkbox',
							'edit_field_class' => 'vc_col-sm-6',
							'value'       => array(
							  'Remove' => 'true',
							),
						 ),
						  array(
							'type' => 'pt_theplus_checkbox',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Add space between your columns by turning on this option.', 'pt_theplus') . '</span></span>' . esc_html__('Column Space Option', 'pt_theplus')),
							'param_name' => 'column_space',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"dependency" => array(
								"element" => "info_box_layout",
								"value" => array("carousel_layout"),
							), 
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							'type' => 'textfield',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Enter Value of Column Space here in Pixels. e.g. 10px, 20px etc.', 'pt_theplus') . '</span></span>' . esc_html__('Column Space', 'pt_theplus')),
							'param_name' => 'column_space_pading',
							'description' => '',
							'value' => '10px',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "column_space",
								"value" => array(
									"on"
								)
							)
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Desktop Hide', 'pt_theplus')),
							'param_name' => 'desktop_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Tablet Hide', 'pt_theplus')),
							'param_name' => 'tablet_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Mobile Hide', 'pt_theplus')),
							'param_name' => 'mobile_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
					 )	
				   ) );
			}
		}
	}
	new ThePlus_info_box;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_info_box'))
	{
		class WPBakeryShortCode_tp_info_box extends WPBakeryShortCode {
		   protected function contentInline( $atts, $content = null ) {
			 
		 }
		}
	}
}