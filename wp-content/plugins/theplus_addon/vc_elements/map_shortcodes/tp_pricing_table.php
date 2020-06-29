<?php 
// Pricing Table Elements
if(!class_exists("ThePlus_pricing_table")){
	class ThePlus_pricing_table{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_pricing_table') );
			add_shortcode( 'tp_pricing_table',array($this,'tp_pricing_table_shortcode'));
		}
		function tp_pricing_table_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'title' => 'The Plus',
					'title_color' =>'#252525',
					'title_size' => '24px',
					'title_line' =>'1.4',
					'title_letter' =>'1px',
					'title_use_theme_fonts'=>'custom-font-family',
				'title_font_family'=>'',
				'title_font_weight'=>'600',
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
					
					'pricing_size' =>'small',
					'icon_postition' =>'after',	  
					'pre_symbol' => '$',
					'prefix_color' =>'#252525',
					'prefix_size' =>'30px',
					'prefix_font_family'=>'',
					'pos_symbol' => '/max',
					'postfix_color' =>'#252525',
					'postfix_size' =>'12px',
					'postfix_font_family'=>'',
					'number' => '60',
					'prifix_posi' =>'middle',
					'postfix_posi' =>'top',	  
					'number_color' =>'#252525',
					'number_size' => '30px',
					'number_line' =>'1.4',
					'number_use_theme_fonts'=>'custom-font-family',
					'number_font_family'=>'',
					'number_font_weight'=>'400',
					'number_google_fonts'=>'',
					
					'table_border_style'=>'style-1',
					'bg_border_width' =>'2px',
					'border_clr' =>'#efefef',
					'border_hvr_clr' =>'#121212',  
					'bg_solid_hv_clr' =>'#efefef',
					'gradient_hover_style' =>'horizontal',
					'hvr_gradient_color2' =>'#1e73be',
					'hvr_gradient_color1' =>'#2fcbce', 
					'bg_color_img' =>'bg_clr',
					'price_bg_img' =>'',
					'bg_color' =>'gradient',
					'bg_solid_color' =>'#45a9f2',
					'gradient_color1' =>'#1e73be',
					'gradient_color2' =>'#2fcbce',
					'gradient_style' =>'horizontal', 
					'to_bg_color_img' =>'top_bg_clr',
					'top_bg_img' =>'',
					'top_color' =>'#f461f7',
					'top_hvr_color' =>'#6387ff',
					'btm_border_clr' =>'#f461f7',
					'btm_bdr_hvr_clr' =>'#f461f7',
					'counter_bg' =>'#f461f7',
					'counter_hover_bg' =>'#f461f7', 
					'pricing_content' => 'custom',
					'pricing_classic' =>'',
					'pricing_content_style'=>'style-1',
					'icon_style_position'=>'style-1',
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
					'classic_bg_f' =>'#efefef',
					'classic_bg_s' =>'#d3d3d3',
					'classic_bg_f_h' =>'#d3d3d3',
					'classic_bg_s_h' =>'#efefef',
					'classic_color' =>'#121212',
					'classic_size' =>'14px',
					'classic_family' =>'',
					'classic_line' =>'1.4',
					'button_link' =>'',
					'btn_text' =>'BUY NOW',
					'show_hove_btn' =>'no',
					'price_animation_efect' =>'',
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
					'btn_icon_openiconic'=>'vc-oi vc-oi-dial',
					'btn_icon_typicons'=>'typcn typcn-adjust-brightness',
					'btn_icon_entypo'=> 'entypo-icon entypo-icon-note',    
					'btn_icon_linecons'=>'vc_li vc_li-heart',
					'btn_icon_monosocial'=>'vc-mono vc-mono-fivehundredpx',
					"before_after" => 'after',
					"btn_url" => '',
					'select_bg_option'=>'normal',
					'normal_bg_color'=>'#252525',
					'btn_gradient_color1'=>'#1e73be',
					'btn_gradient_color2'=>'#2fcbce',
					'btn_gradient_style'=>'horizontal',
					'bg_image'=>'',
					'select_bg_hover_option'=>'normal',

					'normal_bg_hover_color'=>'#ff214f',
					'normal_bg_hover_color1'=>'#d3d3d3',
					'gradient_hover_color1'=>'#2fcbce',
					'gradient_hover_color2'=>'#1e73be',
					'btn_gradient_hover_style'=>'horizontal',
					'bg_hover_image'=>'',
					'display_button'=> 'on',
					'font_size'=>'20px',
					'line_height'=>'25px',
					'letter_spacing'=>'1px',
					'text_color'=>'#8a8a8a',
					'text_hover_color'=>'#252525',
					'border_color'=>'#252525',
					'border_hover_color'=>'#252525',
					'border_radius'=>'30px',

					'full_width_btn'=>'',
					'hover_shadow'=>'',
					'transition_hover'=>'',		
				  
				), $atts ) );
				
				$rand_no=rand(1000000, 1500000);
				$data_class=$data_attr=$a_href=$a_title=$a_target=$a_rel=$style_content=$icons_before=$icons_after=$button_text=$button_hover_text=$gradient_color=$gradient_hover_color='';
				
				$data_class=' button-'.esc_attr($rand_no).' ';
				$data_class .=' button-'.esc_attr($style).' ';
				
				if($full_width_btn=='yes'){
					$data_class .=' full-button ';
				}
				if($transition_hover=='yes'){
					$data_class .=' trnasition_hover ';
				}
								
				if($select_bg_option=='normal'){
					$bg_color_btn = $normal_bg_color;
				}else if($select_bg_option=='gradient'){
					$gradient_color = pt_theplus_gradient_color($btn_gradient_color1,$btn_gradient_color2,$btn_gradient_style);
				$bg_color_btn = $gradient_color;
				}else if($select_bg_option=='image'){
					if(isset($bg_image) && !empty($bg_image)){
						$img = wp_get_attachment_image_src($bg_image, "full");
						$imgSrc = $img[0];
						$bg_color_btn='url('.esc_url($imgSrc).')';
					}
				}else{
					$bg_color_btn = '';
				}
				
				if($select_bg_hover_option=='normal'){
					$bg_hover_color = $normal_bg_hover_color;
				}else if($select_bg_hover_option=='gradient'){
					$gradient_hover_color = pt_theplus_gradient_color($gradient_hover_color1,$gradient_hover_color2,$btn_gradient_hover_style);
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
					$button_text =$icons_before. esc_html($btn_text) . $icons_after;
					$style_content='<div class="button_line"></div>';
				}
				if($style=='style-2' || $style=='style-5' || $style=='style-8' || $style=='style-10'){
					$button_text =$icons_before . esc_html($btn_text) . $icons_after;
				}
				if($style=='style-3'){
					$button_text =$btn_text.'<svg class="arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg><svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg>';
				}
				if($style=='style-4'){
					$button_text =$icons_before. esc_html($btn_text) . $icons_after;
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
					$button_text ='<span>'.$icons_before .esc_html($btn_text). $icons_after.'</span>';
					if(!empty($btn_hover_text)){
						$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
					}else{
						$button_hover_text =' data-hover="'.esc_attr($btn_text).'" ';
					}
					$data_class .=' '.esc_attr($btn_hover_style).' ';
				}
				if($style=='style-12' || $style=='style-15' || $style=='style-16'){
					$button_text ='<span>'.$icons_before . esc_html($btn_text) . $icons_after.'</span>';
				}
				if($style=='style-13'){
					$button_text ='<span>'.$icons_before . esc_html($btn_text) . $icons_after.'</span>';
					$data_class .=' '.esc_attr($btn_hover_style).' ';
				}
				if($style=='style-14'){
					$button_text ='<span>'.$icons_before . esc_html($btn_text) . $icons_after.'</span>';
					if(!empty($btn_hover_text)){
						$button_hover_text =' data-hover="'.esc_attr($btn_hover_text).'" ';
					}else{
						$button_hover_text =' data-hover="'.esc_attr($btn_text).'" ';
					}
				}
				if($style=='style-17'){
					$icons_before=$icons_after;
					$button_text =$icons_before .'<span>'. esc_html($btn_text) .'</span>';
					$data_class .=' '.esc_attr($icon_hover_style).' ';
				}
				if($style=='style-18' || $style=='style-19' || $style=='style-20' || $style=='style-21' || $style=='style-22'){
					$button_text =$icons_before .'<span>'. esc_html($btn_text) .'</span>'. $icons_after;
				}
				
				if($style=='style-23'){
					$button_text ='<span><div class="align-center">'. $icons_before . esc_html($btn_text) . $icons_after .'</div></span>';
					if(!empty($btn_hover_text)){
						$button_text .='<span><div class="align-center">'. $icons_before . esc_html($btn_hover_text) . $icons_after .'</div></span>';
					}else{
						$button_text .='<span><div class="align-center">'. $icons_before . esc_html($btn_text) . $icons_after .'</div></span>';
					}
					$data_class .=' '.esc_attr($btn_hover_style).' ';
				}

				$the_button ='<div class="text-center ts-button">';
					$the_button .='<div class="pt_plus_button '.$data_class.'" '.$data_attr.'>';
						$the_button .='<a class="button-link-wrap" href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' '.$button_hover_text.'>';
							$the_button .=$button_text;
							$the_button .=$style_content;
						$the_button .='</a>';
					$the_button .='</div>';
				$the_button .='</div>';		
				
				 $content = wpb_js_remove_wpautop($content, true);
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
				 
				$price_content = $gradient_color = $pricing_table_img = $progress_bar_btn =  $progress_bar_align = $progress_bar_border = $progress_bar_icon_style= $imge_content =$number_markup=$pie_border_after=$pricing_table_sub=$pricing_table_title=$price_effect=$bg_color_css=$bg_hover=$btm_border_class=$pricing_content_classic='';
				
				if($price_animation_efect == "grow") {
					$price_effect ='Price_table_grow';	
					}elseif($price_animation_efect == "push") {
					$price_effect ='Price_table_push';	
					}elseif($price_animation_efect == "bounce-in") {
					$price_effect ='Price_table_bounce_in';	
					}elseif($price_animation_efect == "float") {
					$price_effect ='Price_table_float';	
					}elseif($price_animation_efect == "wobble_horizontal") {
					$price_effect ='Price_table_wobble_horizontal';	
					}elseif($price_animation_efect == "wobble_vertical") {
					$price_effect ='Price_table_wobble_vertical';	
					}elseif($price_animation_efect == "float_shadow") {
					$price_effect ='Price_table_float_shadow';	
					}elseif($price_animation_efect == "grow_shadow") {
					$price_effect ='Price_table_grow_shadow';	
					}elseif($price_animation_efect == "shadow_radial") {
					$price_effect ='Price_table_radial';	
					}
					
				$img = wp_get_attachment_image_src($price_bg_img, "full");
					$imgSrc_bg = $img[0];
				
				$img = wp_get_attachment_image_src($top_bg_img, "full");
					$imgSrc_top_bg = $img[0];
				$bg_style='';
				if($table_border_style=='style-1'){
				$bg_style = ' style="';
					if($bg_border_width != "") {
							$bg_style .= 'border-width: '.esc_attr($bg_border_width).' ;';
						}
					if($border_clr != "") {
							$bg_style .= 'border-color: '.esc_attr($border_clr).' ;';
						}
				$bg_style .= '"';
				}
				$bg_style2='';
				if($table_border_style=='style-2'){
				$bg_style2 = ' style="';
					if($bg_border_width != "") {
							$bg_style2 .= 'border-width: '.esc_attr($bg_border_width).' ;';
						}
					if($border_clr != "") {
							$bg_style2 .= 'border-color: '.esc_attr($border_clr).' ;';
						}
				$bg_style2 .= '"';
				}
				
				$pre_style = ' style="';
					if($prefix_size != "") {
							$pre_style .= 'font-size: '.esc_attr($prefix_size).' ;';
						}
					if($prefix_color != "") {
							$pre_style .= 'color: '.esc_attr($prefix_color).' ;';
						}	
						if($prefix_font_family != "") {
							$pre_style .= 'font-family: '.esc_attr($prefix_font_family).' ;';
						}						
				$pre_style .= '"';
				
				$post_style = ' style="';
					if($postfix_size != "") {
							$post_style .= 'font-size: '.esc_attr($postfix_size).' ;';
						}
					if($postfix_color != "") {
							$post_style .= 'color: '.esc_attr($postfix_color).' ;';
						}	
						if($postfix_font_family != "") {
							$post_style .= 'font-family: '.esc_attr($postfix_font_family).' ;';
						}			
				$post_style .= '"';
				
				$content_style = ' style="';
					if($classic_size != "") {
							$content_style .= 'font-size: '.esc_attr($classic_size).' ;';
						}
					if($classic_family != "") {
							$content_style .= 'font-family: '.esc_attr($classic_family).' ;';
						}
					if($classic_color != "") {
							$content_style .= 'color: '.esc_attr($classic_color).' ;';
						}
					if($classic_line != "") {
							$content_style .= 'line-height: '.esc_attr($classic_line).' ;';
						}	
						
				$content_style .= '"';
							
				if($bg_color_img == 'bg_clr'){	
						if($bg_color == "gradient") {
							$bg_color_css .= pt_plus_gradient_color($gradient_color1,$gradient_color2,$gradient_style);
						}else{
							$bg_color_css .= 'background: '.esc_attr($bg_solid_color).';';
						}				
					}else{
							$bg_color_css .= 'background-image: url('.esc_url($imgSrc_bg).');';
					}
						
				if($bg_border_width != "") {
							$border_class = 'border-price';
						}else{
						$border_class ='';
						}
						
				if($bg_color_img == 'bg_clr'){	
						if($bg_color == "gradient") {
							$bg_hover .= pt_plus_gradient_color($hvr_gradient_color1,$hvr_gradient_color2,$gradient_hover_style);
						}else{
							$bg_hover .= 'background: '.esc_attr($bg_solid_hv_clr).';';
						}				
					}
					
				
				$bg_top_style = ' style="';
					if($to_bg_color_img == 'top_bg_clr'){	
							$bg_top_style .= 'background: '.esc_attr($top_color).';';			
					}else{
							$bg_top_style .= 'background-image: url('.esc_url($imgSrc_top_bg).');';
					}
					if($btm_border_clr != ''){	
							$bg_top_style .= 'border-bottom-color: '.esc_attr($btm_border_clr).';';			
					}
					
				$bg_top_style .= '"';
				
				if($btm_border_clr != ''){	
							$btm_border_class .= 'border-bottom';			
					}
					
				if($bg_color_img == 'bg_img'){	
					$bg_class ="bg-img";
				}else{
					$bg_class ="bg-color";
				}
				if($show_hove_btn == "yes"){
						$btn_hover = "show-hover-btn";
					}

						$order   = array("\r\n", "\n", "\r", "<br/>", "<br>");
						$replace = '|';
						
						$str = str_replace($order, $replace, $pricing_classic);			
						$lines = explode("|", $str);			
						$count_lines = count($lines);			
							foreach($lines as $line){	
								$pricing_content_classic .= '<li class="pricing_table-classic '.esc_attr($pricing_content_style).'" '.$content_style.'>'.strip_tags($line).' </li> ';
							}
						$strings = '['; 
							foreach($lines as $key => $line){ 
								$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
								if($key != ($count_lines-1))
									$strings .= ','; 
								} 
						$strings .= ']';
				
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
							$number_css .= 'background: '.esc_attr($counter_bg).';';
						}	
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
					$number_css .= '"';
					$symbol2 = '<span class="prifix-'.esc_attr($prifix_posi).'" '.$pre_style.'>'.esc_html($pre_symbol).'</span> <span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span> <span class="postfix-'.esc_attr($postfix_posi).'" '.$post_style.'>'.esc_html($pos_symbol).'</span>';
				
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
					
					 $pricing_table_title= '<div class="pricing_table-title" '.$title_css.'> '.esc_html($title).' </div>';
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
					
					 $pricing_table_sub= '<div class="pricing_table-sub_title" '.$sub_css.'> '.esc_html($sub_title).' </div>';
				}
				if($image_icon == 'image'){
				$img = wp_get_attachment_image_src("$select_image", "full");
				$imgSrc = $img[0];
					 $pricing_table_img='<span class="progres-ims"><img src="'.esc_url($imgSrc).'"   class="pricing_table-img '.esc_attr($imge_content).'" alt="" /></span>';
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
					$pricing_table_img = '<span class="progres-ims"><i class=" '.esc_attr($icon_class).'  '.esc_attr($progress_bar_icon_style).'" '.$icon_css.'></i></span>';
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
					
					if($svg_url ==''){
					if(!empty($border_stroke_color)){
						$border_stroke_color=$border_stroke_color;
					}else{
						$border_stroke_color='none';
					}
				
					}
					$pricing_table_img ='<span class="pricetable-ims"><div class="pt_plus_animated_svg '.esc_attr($alignment).' svg-'.esc_attr($rand_no).'" data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($svg_type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none" >';
						$pricing_table_img .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';max-height:'.esc_attr($max_width).';">';
							$pricing_table_img .='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
						$pricing_table_img .='</div>';
					$pricing_table_img .='</div></span>';
					}	
					if($symbol2!= ''){
						$number_markup = '<h5 class="price-counter counter-number" '.$number_css.'>';
						if(empty($icon_style_position) || $icon_style_position=='style-2'){
										$number_markup .= $pricing_table_img;
									}
						$number_markup .=$symbol2.'</h5>';
					}
							
					if($pricing_content != 'custom'){
							$price_content = $pricing_content_classic;
						}else{
							$price_content = $content;
					}
					if($pricing_content == 'custom'){
							$price_content_class = 'ul-pd-20';
						}else{
							$price_content_class = '';
					}
			
					$uid=uniqid('pricing_table');
					$uid2=uniqid('pricing');
					$pricing_table = '<div class="pricing_table-inner '.esc_attr($uid2).' pricing-'.esc_attr($pricing_size).' border-'.$table_border_style.'" '.$bg_style2.'>';
					$pricing_table .='<div class="pricing_table text-center  '.esc_attr($bg_class).' '.esc_attr($border_class).' '.esc_attr($price_effect).' '.esc_attr($el_class).' '.esc_attr($uid).'  '.esc_attr($animated_class).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'"  data-uid="'.esc_attr($uid).'" '.$bg_style.'>';
						
							$pricing_table .= '<div class="pricing_table-top '.esc_attr($btm_border_class).'" '.$bg_top_style.'>';
								$pricing_table .= '<div class="price-title price-icon ">';
									if(empty($icon_style_position) || $icon_style_position=='style-1'){
										$pricing_table .= $pricing_table_img;
									}
										$pricing_table .= $pricing_table_title;
									$pricing_table .= $pricing_table_sub;
								$pricing_table .= '</div>'; 	
							$pricing_table .= '</div>';	
							if($number_markup !='' || $price_content !=''){
							$pricing_table .= '<div class="pricing_table-middle">';
								$pricing_table .= '<div class="price-counter-middle" >';
								$pricing_table .= $number_markup;	
								$pricing_table .= '</div>';
								$pricing_table .= '<ul class="pricing-content '.esc_attr($price_content_class).' '.esc_attr($pricing_content_style).'" '.$content_style.'>';
								$pricing_table .= $price_content;
								$pricing_table .= '</ul>';
							$pricing_table .= '</div>';	
							}
							if($display_button =='on' ){
							$pricing_table .= '<div class="pricing_table-bottom">';
								$pricing_table .= $the_button;	
							$pricing_table .= '</div>';	
							}
						$pricing_table .= '</div>';
					$pricing_table .= '</div>';
					
				$css_rule='';
		$css_rule .= '<style >';
		$css_rule .= '.'.esc_js($uid).' {'.esc_js($bg_color_css).';}.'.esc_js($uid).':hover {'.esc_js($bg_hover).'; border-color: '.esc_js($border_hvr_clr).' !important; }.'.esc_js($uid).':hover .pricing_table-middle .price-counter{ background: '.esc_js($counter_hover_bg).' !important;}.'.esc_js($uid).':hover .pricing_table-top{ background-color: '.esc_js($top_hvr_color).' !important;}.'.esc_js($uid).':hover .pricing_table-top.border-bottom{border-color: '.esc_js($btm_bdr_hvr_clr).'!important; }.'.esc_js($uid).'.pricing_table .pricing-content .pricing_table-classic{ background: '.esc_js($classic_bg_s).';}.'.esc_js($uid).'.pricing_table:hover .pricing-content .pricing_table-classic{ background: '.esc_js($classic_bg_s_h).';}.'.esc_js($uid).' .pricing-content .pricing_table-classic:nth-child(odd){ background: '.esc_js($classic_bg_f).';} .'.esc_js($uid).'.pricing_table:hover .pricing-content .pricing_table-classic:nth-child(odd){ background: '.esc_js($classic_bg_f_h).';}.'.esc_js($uid).' .pricing-content p,.'.esc_js($uid).' .pricing-content{font-size: '.esc_js($classic_size).'; line-height: '.esc_js($classic_line).'; font-family: '.esc_js($classic_family).'; color: '.esc_js($classic_color).';}.'.esc_js($uid2).':hover{border-color: '.esc_js($border_hvr_clr).' !important;}';
		if($display_button =='on' ){
		$bg_color=$bg_color_btn;
			$css_rule .= include THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/button_css.php';
		}
		$css_rule .= '</style>'; 
		
			return $css_rule.$pricing_table;
		}
		function init_tp_pricing_table(){
			if(function_exists("vc_map"))
			{
				require(THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/vc_arrays.php');
				vc_map( array(
						  "name" => __( "Pricing Table", "pt_theplus" ),
						  "base" => "tp_pricing_table",
						  "icon" => "tp-pricing-table",
						  "category" => __( "The Plus", "pt_theplus"),
						  "description" => esc_html__('Crafted to make them buy', 'pt_theplus'),
						  "params" => array(

							
							array(
							  "type"        => "dropdown",
							 'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Pricing Table Size using this option.','pt_theplus').'</span></span>'.esc_html__('Pricing Table Size', 'pt_theplus')), 
							  "param_name"  => "pricing_size",
							  "value"       => array(
									__( 'Small', 'pt_theplus' ) => 'small',
									__( 'Medium', 'pt_theplus' ) => 'medium',
									__( 'Large', 'pt_theplus' ) => 'large',
								),
							  "std" => "small",
							  "description" => '',
							),
							 array(
							  "type" => "textfield",
							  'heading' =>  esc_html__('Title', 'pt_theplus'), 
							  "param_name" => "title",
							  "value" => 'The Plus',
							   "admin_label" => true,
							  "description" => ""
							),
							array(
							  "type" => "textfield",
							  'heading' =>  esc_html__('Sub Title', 'pt_theplus'), 
							  "param_name" => "sub_title",
							  "value" => 'Pie chart subtitle',
							  "admin_label" => true,
							  "description" => ""
							),
								array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add digits/count of your number. e.g. 999, 99 .','pt_theplus').'</span></span>'.esc_html__('Enter Price Tag', 'pt_theplus')), 
								  "param_name" => "number",
								  'value' => '60',
								  "description" => '',
								   "group" => "Content",
								),
								array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('An optional symbol to place next to the number counted to. e.g. "%" or "$" ','pt_theplus').'</span></span>'.esc_html__('Prefix Symbol', 'pt_theplus')), 
								  "param_name" => "pre_symbol",
								  "value" => '$',
								  "description" => '',
								   "group" => "Content",
								),
								array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('An optional symbol to place next to the number counted to. e.g. "%" or "$" ','pt_theplus').'</span></span>'.esc_html__('Postfix Symbol', 'pt_theplus')), 
								  "param_name" => "pos_symbol",
								  "value" => '/max',
								  "description" => '',
								   "group" => "Content",
								),
								array(
								  "type" => "dropdown",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Prefix Position using this option.','pt_theplus').'</span></span>'.esc_html__('Prefix Position', 'pt_theplus')),
								  "param_name" => "prifix_posi",
								  "value" => array(
											__( 'Top', 'pt_theplus' ) => 'top',
											__( 'Middle', 'pt_theplus' ) => 'middle',
											__( 'Bottom', 'pt_theplus' ) => 'bottom',					
										),
								  "std" =>'middle',
								  "description" => '',
										 "group" => "Content",
									),
								array(
								  "type" => "dropdown",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Postfix Position using this option.','pt_theplus').'</span></span>'.esc_html__('Postfix Position', 'pt_theplus')),
								  "param_name" => "postfix_posi",
								  "value" => array(
											__( 'Top', 'pt_theplus' ) => 'top',
											__( 'Middle', 'pt_theplus' ) => 'middle',
											__( 'Bottom', 'pt_theplus' ) => 'bottom',					
										),
								  "std" =>'top',
								  "description" => '',
										 "group" => "Content",
								),	
							array(
							  "type" => "dropdown",
							   'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Body Content Style using this option.','pt_theplus').'</span></span>'.esc_html__('Body Content Style', 'pt_theplus')),
							  "param_name" => "pricing_content",
							  "value" => array(
										__( 'Using Rich Editor', 'pt_theplus' ) => 'custom',
										__( 'Classic Editor', 'pt_theplus' ) => 'classic',	
										__( 'No', 'pt_theplus' ) => 'no',	
									),
							  "std" => 'custom',	
							  "description" => '',		  
							  "group" => "Content",
							),	
							array(
							  "type" => "textarea_html",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add your content the way you want. You may use HTML and other Rich content.','pt_theplus').'</span></span>'.esc_html__('Using Rich Editor', 'pt_theplus')),
							  "heading" => __("Using Rich Editor", 'pt_theplus'),
							  "param_name" => "content",
							  "value" => "",	
							  "description" => '',
							  "group" => "Content",
								  "dependency" => array(
									"element" => "pricing_content",
									"value" => "custom"
								),		  
							),				
							array(
							  "type" => "textarea",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Multiple Values by pressing Enter.','pt_theplus').'</span></span>'.esc_html__('Classic Editor', 'pt_theplus')),
							  "param_name" => "pricing_classic",
							  "value" => "",	
							  "description" => '',
							  "group" => "Content",
								  "dependency" => array(
									"element" => "pricing_content",
									"value" => "classic"
										),		  
							),	
							array(
							  "type" => "dropdown",
							   'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Body Content Style using this option.','pt_theplus').'</span></span>'.esc_html__('Content Style', 'pt_theplus')),
							  "param_name" => "pricing_content_style",
							  "value" => array(
										__( 'Style 1', 'pt_theplus' ) => 'style-1',
										__( 'Style 2', 'pt_theplus' ) => 'style-2',
								),
								"dependency" => array(
									"element" => "pricing_content",
									"value" => "classic"
										),	
							  "std" => 'style-1',	
							  "description" => '',		  
							  "group" => "Content",
							),
							array(
							'type' => 'pt_theplus_checkbox',
								'class' => '',
								'heading' => __('Display Button', 'pt_theplus'),
								'param_name' => 'display_button',
								'description' => '',
								'value' => 'on',
								'options' => array(
									'on' => array(
											'on' => 'on',
											'off' => 'off',
										),
									),
								"edit_field_class" => "vc_col-xs-6",
							),
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
								"group" => esc_attr__('Button', 'pt_theplus'),
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
								'description' => '',
								'dependency' => array(
									'element' => 'style',
									'value' => array(
										'style-23'
									)
								),
								"group" => esc_attr__('Button', 'pt_theplus'),
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
								"group" => esc_attr__('Button', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Text', 'pt_theplus')), 
								"param_name" => "btn_text",
								"group" => esc_attr__('Button', 'pt_theplus'),
								"value" => 'The Plus',
								'description' => '',

							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write on hover  title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Hover Text', 'pt_theplus')),
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
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Setup Inner Padding top-bottom and right-left to Button from this option. E.g. 15px 20px, 30px 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Inner Padding ', 'pt_theplus')),
								"param_name" => "btn_padding",
								"value" => '15px 30px',
								"group" => esc_attr__('Button', 'pt_theplus'),
								'description' => '',
							),
						array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
									'value' => array(
										__( 'Select Icon', 'pt_theplus' ) => '',
										__( 'Font Awesome', 'pt_theplus' ) => 'fontawesome',
										__( 'Open Iconic', 'pt_theplus' ) => 'openiconic',
										__( 'Typicons', 'pt_theplus' ) => 'typicons',
										__( 'Entypo', 'pt_theplus' ) => 'entypo',
										__( 'Mono Social', 'pt_theplus' ) => 'monosocial',
									),
									'std'=>'fontawesome',
									'param_name' => 'btn_icon',
									"group" => esc_attr__('Button', 'pt_theplus'),
									'description' => "",
									'dependency' => array(
										'element' => 'style',
										'value' => array('style-1','style-2','style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-15','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-23'),
									),
							),
							array(
									'type' => 'iconpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'btn_icon_fontawesome',
									'value' => 'fa fa-arrow-right', // default value to backend editor admin_label
									'settings' => array(
										'emptyIcon' => false,
										'iconsPerPage' => 100,
									),
									"group" => esc_attr__('Button', 'pt_theplus'),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'fontawesome',
									),
									
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
										'iconsPerPage' => 4000,
									),
									"group" => esc_attr__('Button', 'pt_theplus'),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'openiconic',
									),
									
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
										'iconsPerPage' => 4000,
									),
									"group" => esc_attr__('Button', 'pt_theplus'),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'typicons',
									),
									
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
										'iconsPerPage' => 4000,
									),
									"group" => esc_attr__('Button', 'pt_theplus'),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'entypo',
									),
									
									
							),
							array(
									'type' => 'iconpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'btn_icon_linecons',
									'value' => 'vc_li vc_li-heart',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'linecons',
										'iconsPerPage' => 4000,
									),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'linecons',
									),	
									
									'description' => '',
									"group" => esc_attr__('Button', 'pt_theplus'),
							),
							array(
									'type' => 'iconpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
									'param_name' => 'btn_icon_monosocial',
									'value' => 'vc-mono vc-mono-fivehundredpx',
									'settings' => array(
										'emptyIcon' => false,
										'type' => 'monosocial',
										'iconsPerPage' => 4000,
									),
									'dependency' => array(
										'element' => 'btn_icon',
										'value' => 'monosocial',
									),
									'description' => '',
									"group" => esc_attr__('Button', 'pt_theplus'),
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Position of Icon before or after content from this option.','pt_theplus').'</span></span>'.esc_html__('Icon Position', 'pt_theplus')),
								"param_name" => "before_after",
								"value" => array(
									__("After Icon", "pt_theplus") => "after",
									__("Before Icon", "pt_theplus") => "before",
								),
								"description" => "",
								"std" =>'after', 
								"group" => esc_attr__('Button', 'pt_theplus'),
								'dependency' => array(
										'element' => 'style',
										'value' => array('style-1','style-2','style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-15','style-16','style-18','style-19','style-20','style-21','style-22','style-23'),
								),
							),
							array(
								'type' => 'vc_link',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Button URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Button URL', 'pt_theplus')),
								'param_name' => 'btn_url',
								"group" => esc_attr__('Button', 'pt_theplus'),
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
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Font Weight using this Option. E.g. 400, 700, etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								"param_name" => "font_weight",
								"value" => '600',
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
								'param_name' => 'btn_gradient_color1',
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
								'param_name' => 'btn_gradient_color2',
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
								'param_name' => 'btn_gradient_style',
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
								'param_name' => 'btn_gradient_hover_style',
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
								 'group' => esc_attr__('Button', 'pt_theplus'),
								'std' => 'text-left',
								"description" => ""
							),
							 array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Background Setting', 'pt_theplus'),
							   'param_name'  => 'background_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),
							  array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Border style effect.','pt_theplus').'</span></span>'.esc_html__('Alignment', 'pt_theplus')), 
								'param_name' => 'table_border_style',
								'value' => array(
									__('Style 1', 'pt_theplus') => 'style-1',
									__('Style 2', 'pt_theplus') => 'style-2',
								),
								 'group' => esc_attr__('Style', 'pt_theplus'),
								'std' => 'style-1',
								"description" => ""
								),
							  array(
							  "type"        => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set border width using this option. E.g. 1px, 2px, 3px, etc.','pt_theplus').'</span></span>'.esc_html__('Box Border Width', 'pt_theplus')), 
							  "param_name"  => "bg_border_width",
							  "value"       => '2px',
							  "description" => "",
							   'edit_field_class' => 'vc_col-sm-4',
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							  array(
							  "type"        => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Box Border Color', 'pt_theplus')),
							  "param_name"  => "border_clr",
							  "value"       => '#efefef',
							  "description" => "",
							   'edit_field_class' => 'vc_col-sm-4',
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							  array(
							  "type"        => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border Hover using this option.','pt_theplus').'</span></span>'.esc_html__('Box Border Hover Color', 'pt_theplus')),
							  "param_name"  => "border_hvr_clr",
							  "value"       => '#121212',
							  "description" => "",
							   'edit_field_class' => 'vc_col-sm-4',
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							  array(
							  "type"        => "dropdown",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Options using this option.','pt_theplus').'</span></span>'.esc_html__('Background Options', 'pt_theplus')),
							  "param_name"  => "bg_color_img",
							  "value"       => array(
									__( 'Background Color', 'pt_theplus' ) => 'bg_clr',
									__( 'Background Image', 'pt_theplus' ) => 'bg_img',
								),
							  "std" => "bg_clr",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							 array(
							  "type"        => "attach_image",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Advertisement banner using this option. .jpg, .png, .gif images supported.','pt_theplus').'</span></span>'.esc_html__('Background Image', 'pt_theplus')),
							  "param_name"  => "price_bg_img",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							  'dependency' => array('element' => 'bg_color_img','value' => 'bg_img'),
							),
							 array(
							  "type"        => "dropdown",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Color Options using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color Options', 'pt_theplus')),
							  "param_name"  => "bg_color",
							  "value"       => array(
									__( 'Solid', 'pt_theplus' ) => 'solid',
									__( 'Gradient', 'pt_theplus' ) => 'gradient',
								),
							  "std" => "gradient",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							  'dependency' => array('element' => 'bg_color_img','value' => 'bg_clr'),
							),
							array(
							   'type' => 'colorpicker',
							   'heading' => __( 'Background Color ', 'pt_theplus' ),
							   'param_name' => 'bg_solid_color',  
							   'dependency' => array('element' => 'bg_color','value' => 'solid'),
							   "edit_field_class" => "vc_col-xs-12",
							   "value" => '#45a9f2',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							   'type' => 'colorpicker',
							   'heading' => __( 'Background Hover Color ', 'pt_theplus' ),
							   'param_name' => 'bg_solid_hv_clr',  
							   'dependency' => array('element' => 'bg_color','value' => 'solid'),
							   "edit_field_class" => "vc_col-xs-12",
							   "value" => '#efefef',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							   'type' => 'colorpicker',
							   'heading' => __( 'Color 1', 'pt_theplus' ),
							   'param_name' => 'gradient_color1',  
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
							   "edit_field_class" => "vc_col-xs-6",
							   "value" => '#1e73be',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
								'type' => 'colorpicker',
								'heading' => __( 'Color 2', 'pt_theplus' ),
								'param_name' => 'gradient_color2',   
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#2fcbce',
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
									'param_name' => 'gradient_style',
									'value' => array(
										__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
										__( 'Vertical', 'pt_theplus' ) => 'vertical',
										__( 'Diagonal', 'pt_theplus' ) => 'diagonal',
										__( 'Radial', 'pt_theplus' ) => 'radial',                                
									),
								'std'=>'horizontal',
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							   'type' => 'colorpicker',
							   'heading' => __( 'Hover Color 1', 'pt_theplus' ),
							   'param_name' => 'hvr_gradient_color1',  
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
							   "edit_field_class" => "vc_col-xs-6",
							   "value" => '#2fcbce',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
								'type' => 'colorpicker',
								'heading' => __( 'Hover Color 2', 'pt_theplus' ),
								'param_name' => 'hvr_gradient_color2',   
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#1e73be',
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Hover Gradient Style', 'pt_theplus')),
									'param_name' => 'gradient_hover_style',
									'value' => array(
										__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
										__( 'Vertical', 'pt_theplus' ) => 'vertical',
										__( 'Diagonal', 'pt_theplus' ) => 'diagonal',
										__( 'Radial', 'pt_theplus' ) => 'radial',                                
									),
								'std'=>'horizontal',
								'dependency' => array('element' => 'bg_color','value' => 'gradient'),
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							 array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Top BackgroundSetting', 'pt_theplus'),
							   'param_name'  => 'top_bg_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),
							  array(
							  "type"        => "dropdown",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Options using this option.','pt_theplus').'</span></span>'.esc_html__('Top Background Options', 'pt_theplus')), 
							  "param_name"  => "to_bg_color_img",
							  "value"       => array(
									__( 'Background Color', 'pt_theplus' ) => 'top_bg_clr',
									__( 'Background Image', 'pt_theplus' ) => 'top_bg_img',
								),
							  "std" => "top_bg_clr",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							 array(
							  "type"        => "attach_image",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Advertisement banner using this option. .jpg, .png, .gif images supported.','pt_theplus').'</span></span>'.esc_html__('Top Background Image', 'pt_theplus')), 	
							  "param_name"  => "top_bg_img",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							  'dependency' => array('element' => 'to_bg_color_img','value' => 'top_bg_img'),
							),
							array(
							  "type"        => "colorpicker",
							  "heading"     => __("Top Background Color" , "pt_theplus"),
							  "param_name"  => "top_color",
							  "value"		=>'#f461f7',
							  "edit_field_class" => "vc_col-xs-6",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							  'dependency' => array('element' => 'to_bg_color_img','value' => 'top_bg_clr'),
							),
							array(
							  "type"        => "colorpicker",
							  "heading"     => __("Top Background Hover Color" , "pt_theplus"),
							  "param_name"  => "top_hvr_color",
							  "value"		=>'#6387ff',
							  "edit_field_class" => "vc_col-xs-6",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							  'dependency' => array('element' => 'to_bg_color_img','value' => 'top_bg_clr'),
							),
							array(
							  "type"        => "colorpicker",
							  "heading"     => __("Bottom Boder Color" , "pt_theplus"),
							  "param_name"  => "btm_border_clr",
							  "edit_field_class" => "vc_col-xs-6",
							  "value"		=>'#f461f7',
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							  "type"        => "colorpicker",
							  "heading"     => __("Bottom Boder Hover Color" , "pt_theplus"),
							  "param_name"  => "btm_bdr_hvr_clr",
							  "value"		=>'#f461f7',
							  "edit_field_class" => "vc_col-xs-6",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							),
							 array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Price Counter Setting', 'pt_theplus'),
							   'param_name'  => 'price_counter_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),
							 array(
							  "type"        => "colorpicker",
							  "heading"     => __("Background Color" , "pt_theplus"),
							  "param_name"  => "counter_bg",
							  "value"		=>'#f461f7',
							  "edit_field_class" => "vc_col-xs-6",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							), 
							 array(
							  "type"        => "colorpicker",
							  "heading"     => __("Background Hover Color" , "pt_theplus"),
							  "param_name"  => "counter_hover_bg",
							  "value"		=>'#f461f7',
							  "edit_field_class" => "vc_col-xs-6",
							  "description" => "",
							  'group' => esc_attr__('Style', 'pt_theplus'),
							), 
							array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Title Setting', 'pt_theplus'),
							   'param_name'  => 'title_style_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')), 
							  "param_name" => "title_color",
							  "value" => '#252525',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
							  "param_name" => "title_size",
							  "value" => '24px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_line",
								'value' => '1.4',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
								),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_letter",
								'value' => '1px',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
							   'group' => __( 'Style', 'pt_theplus' ),
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
								'value' => __('600','pt_theplus'),
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
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Sub-Title Setting', 'pt_theplus'),
							   'param_name'  => 'subtitle_style_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							  "param_name" => "sub_color",
							  "value" => '#d3d3d3',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							  "param_name" => "sub_size",
							  "value" => '14px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "sub_line",
								'value' => '1.4',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
								),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "sub_letter",
								'value' => '1px',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
							   'group' => __( 'Style', 'pt_theplus' ),
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
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Number Setting', 'pt_theplus'),
							   'param_name'  => 'number_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							  "param_name" => "number_color",
							  "value" => '#252525',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							  "param_name" => "number_size",
							  "value" => '30px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line height', 'pt_theplus')),
								"param_name" => "number_line",
								'value' => '1.4',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
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
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Prefix Setting', 'pt_theplus'),
							   'param_name'  => 'prefix_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							  "param_name" => "prefix_color",
							  "value" => '#252525',
							   "edit_field_class" => "vc_col-xs-4",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							  "param_name" => "prefix_size",
							  "value" => '30px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-4",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'prefix_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-4',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Postfix Setting', 'pt_theplus'),
							   'param_name'  => 'postfix_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							  "param_name" => "postfix_color",
							  "value" => '#252525',
							   "edit_field_class" => "vc_col-xs-4",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							  "param_name" => "postfix_size",
							  "value" => '12px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-4",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'postfix_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-4',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),
							),
							array(
							   'type'    => 'pt_theplus_heading_param',
							   'text'    => esc_html__('Content Setting', 'pt_theplus'),
							   'param_name'  => 'content_setting',
							   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							   'group' => esc_attr__('Style', 'pt_theplus'),
							  ),	
							  array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for classic background First using this option.','pt_theplus').'</span></span>'.esc_html__('Classic Background First Color', 'pt_theplus')),
							  "param_name" => "classic_bg_f",
							  "value" => '#efefef',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							   'dependency' => array(
									'element' => 'pricing_content',
									'value' => 'classic',
								),
							),
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for classic background Second using this option.','pt_theplus').'</span></span>'.esc_html__('Classic Background Second Color', 'pt_theplus')),
							  "param_name" => "classic_bg_s",
							  "value" => '#d3d3d3',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'dependency' => array(
									'element' => 'pricing_content',
									'value' => 'classic',
								),
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for classic background hover First  using this option.','pt_theplus').'</span></span>'.esc_html__('Classic Background Hover First Color', 'pt_theplus')),
							  "param_name" => "classic_bg_f_h",
							  "value" => '#d3d3d3',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'dependency' => array(
									'element' => 'pricing_content',
									'value' => 'classic',
								),
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for classic background hover second  using this option.','pt_theplus').'</span></span>'.esc_html__('Classic Background Hover Second Color', 'pt_theplus')),
							  "param_name" => "classic_bg_s_h",
							  "value" => '#efefef',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'dependency' => array(
									'element' => 'pricing_content',
									'value' => 'classic',
								),
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							 array(
							  "type" => "colorpicker",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for Font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							  "param_name" => "classic_color",
							  "value" => '#121212',
							   "edit_field_class" => "vc_col-xs-6",
							  "description" => '',
							   'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
							  "type" => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							  "param_name" => "classic_size",
							  "value" => '14px',
							  "description" => '',
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can use font family style using this Option. E.g. Open Sans, Roboto, etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								"param_name" => "classic_family",
								'value' => '',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
								),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "classic_line",
								'value' => '1.4',
							   "description" => "",
							   "edit_field_class" => "vc_col-xs-6",
								'group' => __( 'Style', 'pt_theplus' ),
								),
							array(
							  "type" => "dropdown",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon Style of Position using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon Style', 'pt_theplus')),
							  "param_name" => "icon_style_position",
							  "value" => array(
									__( 'Style 1', 'pt_theplus' ) => 'style-1',
									__( 'Style 2', 'pt_theplus' ) => 'style-2',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							  "std" => "style-1",
							),
							array(
							  "type" => "dropdown",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon ', 'pt_theplus')),
							  "param_name" => "image_icon",
							  "value" => array(
									__( 'Select Image Or Icon', 'pt_theplus' ) => '',
									__( 'Icon', 'pt_theplus' ) => 'icon',
									__( 'Image', 'pt_theplus' ) => 'image',
									__( 'Svg', 'pt_theplus' ) => 'svg',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							  "std" => "icon",
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
								"type" => "attach_image",
								"heading" => esc_html__("Use Image As icon", 'pt_theplus') ,
								"value" => "",
								"description" => '',  
								"param_name" => 'select_image',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'image',
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
									__( 'Select Style', 'pt_theplus' ) => '',
									__( 'Square', 'pt_theplus' ) => 'square',
									__( 'Rounded', 'pt_theplus' ) => 'rounded',
								),
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon'
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
								 "std" => "square",
							   "admin_label" => false,					
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
								'param_name' => 'icon_size',
								'value' => '18px',		
								'group' => __( 'Icon Option', 'pt_theplus' ),
								'description' => '',
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
								'description' => '',
								'dependency' => array(
									'element' => 'image_icon',
									'value' => 'icon',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
								),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Animation Settings', 'pt_theplus'),
								'param_name' => 'annimation_effect',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							),
							array(
							  "type"        => "dropdown",
							  "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This Effects will be applied when you hover on this section.','pt_theplus').'</span></span>'.esc_html__('Content Hover Effects', 'pt_theplus')),
							  "param_name"  => "price_animation_efect",
							  "value"       => array(
							__( 'Select Table animation Style', 'pt_theplus' ) => '',
							__( 'Grow', 'pt_theplus' ) => 'grow',
							__( 'Push', 'pt_theplus' ) => 'push',
							__( 'Bounce In', 'pt_theplus' ) => 'bounce-in',
							__( 'Float', 'pt_theplus' ) => 'float',	
							__( 'wobble horizontal', 'pt_theplus' ) => 'wobble_horizontal',	
							__( 'Wobble Vertical', 'pt_theplus' ) => 'wobble_vertical',
							__( 'Float Shadow', 'pt_theplus' ) => 'float_shadow',
							__( 'Grow Shadow', 'pt_theplus' ) => 'grow_shadow',
							__( 'Shadow Radial', 'pt_theplus' ) => 'shadow_radial',			
							  ),
							  "description" => "",
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
								  'edit_field_class' => 'vc_col-sm-6',
								  'std' =>'no-animation',
							),		
							array(
								  "type"        => "textfield",
								 "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
								  "param_name"  => "animation_delay",
								  'edit_field_class' => 'vc_col-sm-6',
								  "value"       => '50',
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
							 
						 )	
					   ) );
			}
		}
	}
	new ThePlus_pricing_table;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_pricing_table'))
	{
		class WPBakeryShortCode_tp_pricing_table extends WPBakeryShortCode {
		   protected function contentInline( $atts, $content = null ) {
			 
		 }
		}
	}
}