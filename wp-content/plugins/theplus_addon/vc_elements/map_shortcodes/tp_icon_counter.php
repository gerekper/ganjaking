<?php 
// Icon Counter Elements
if(!class_exists("ThePlus_icon_counter")){
	class ThePlus_icon_counter{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_icon_counter') );
			add_shortcode( 'tp_icon_counter',array($this,'tp_icon_counter_shortcode'));
		}
		function tp_icon_counter_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				  'icn_layout' =>'single_layout',
				  'icn_style' =>'style_1',
				  "subject" => 'Title',
				  'symbol' => '',
				  'symbol_position' => 'after',
				   'no_size_tag'=> '30px',
				   'no_line' =>'1',
				   'no_letter' =>'1px',
				   'digit_use_theme_fonts'=>'custom-font-family',
					'digit_font_family'=>'',
					'digit_font_weight'=>'400',
					'digit_google_fonts'=>'',
				
				   'title_size'=> '30px',
				   'title_line' =>'1',
				   'title_letter' =>'1px',
				   'title_use_theme_fonts'=>'custom-font-family',
					'title_font_family'=>'',
					'title_font_weight'=>'600',
					'title_google_fonts'=>'',
				
				   'subtitle_size'=> '22px',
				   'subtitle_line' =>'1',
				   'subtitle_letter' =>'1px',
				   'subtitle_use_theme_fonts'=>'custom-font-family',
					'subtitle_font_family'=>'',
					'subtitle_font_weight'=>'400',
					'subtitle_google_fonts'=>'',
					
				  'number' => '99',
				  'numbers_font_family' =>'',
				  'style_color' => '',
				  'style_hover_color' => '',
				  'sub_style_color' =>'',
				  'title_hv_color'=>'#000',
				  'extra_class'=>'',
				  'type'=> 'fontawesome',
				  'icon_fontawesome'=> 'fa fa-adjust',
				  'icon_openiconic'=> 'vc-oi vc-oi-dial',
				  'icon_typicons'=> 'typcn typcn-adjust-brightness',
				  'icon_entypo'=> 'entypo-icon entypo-icon-note',    
				  'icon_linecons'=> 'vc_li vc_li-heart',
				  'icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
				  'icon_custom_color'=>'',
				  'icon_2custom_color'=>'#ccc',
				  'icon_background_shape'=>'#abcaea',
				  'icon_background_color'=>'',
				  'icon_custom_background_color'=>'',
				  'icon_font_size'=> '1.2',
				  'icon_link' => '',
				  'icon_align'=> 'left',
				  'icon_border_color'=> '',
				  'icon_border_width'=> '',
				  'background_color_counter'=> '',
				  'bg_hv_clr_ctr'=>'',
				  'icon_hover_style_counter'=>'style-1',
				  'icon_counter_style_counter'=> 'style-2',
				 
				  'icon_2custom_hover_color'=>'#000',
				  'sub_subject'=>'',
				  'sub_title_clr'=>'#555',
				  'imge'=>'', 
				  'icon_imge' =>'icon_',
				  'icon_image' => '',
				  'sub_title_hv_clr' => '',
				  'box_border' => '',	
				  'box_border_clr' =>'#4d4d4d',
				  'bd_width' => '',
				  'bd_rad' => '',
				  'bd_clr' => '#4d4d4d',
				  'border_width' =>'10%',
				  'bd_height' =>'2px',
				  'cont_bg' =>'#F9B701',
				  'btn_text' =>'',
				  'btn_link' =>'',
				  'btn_bg' =>'',
				  'btn_bg_hvr' =>'',
				  'btn_clr_hvr' =>'',
				  'btn_clr' => '',
				  'box_shadow' => '-1px 1px 3px 3px #c6c6c6',	
				   'hover_box_shadow' => '9px 5px 20px 4px #c6c6c6', 
				  'btn_font' => '',
				  'btn_bd' => '',
				  'btn_wid' => '',
				  'btn_rad' => '',
				  'btn_bd_clr' => '',
				  'btn_h_cr' => '',
					'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					
					'svg_icon' =>'svg',
					'svg_image'=>'',
					'svg_d_icon' =>'app.svg',
					'svg_type'=>'delayed',
					'duration'=>'80',
					'max_width'=>'100px',
					'border_stroke_color'=>'#ff0000',
					'loop_content' =>'',
					
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
				
				'content_hover_effects' => '',
				'hover_shadow_color' => 'rgba(0, 0, 0, 0.6)',
				
				'dots_border_color'=>'#000',
				'dots_bg_color'=>'#fff',
				'dots_active_border_color'=>'#000',
				'dots_active_bg_color'=>'#000',
				
				'arrow_bg_color'=>'#c44d48',
				'arrow_icon_color'=>'#fff',
				'arrow_hover_bg_color'=>'#fff',
				'arrow_hover_icon_color'=>'#c44d48',
				'arrow_text_color'=>'#fff',
				
				'tablet_hide' => 'off',
					'desktop_hide' =>'off',
					'mobile_hide' => 'off',

			   ), $atts ) );
			   
				$button_link = $pd0 = $icon_img_ic =$icon_border_box= $number_markup =$subject_markup=$subject_markup1 =$border_box_css ='';
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
				if($icn_style == 'style_2'){
					$pd0="pad-0";
				}elseif ($icn_style == 'style_1'){
					$pd0="pad-5";
				}else{
					$pd0="pad-30";
				}
				if($box_border=='true'){
					$icon_border_box='border-pd';
					
					$border_box_css = 'style="';
						if($box_border_clr!= "") {
							$border_box_css .='border-color: '.esc_attr($box_border_clr).';';
						}
						
					$border_box_css .= '"';
					
				}

				
			   $btn_link = ( '||' === $btn_link ) ? '' : $btn_link;
				$btn_link= vc_build_link( $btn_link);

				$a_href = $btn_link['url'];
				$a_title = $btn_link['title'];
				$a_target = $btn_link['target'];
				$a_rel = $btn_link['rel'];
				if ( ! empty( $a_rel ) ) {
				$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
				}

				   $img = wp_get_attachment_image_src($icon_image, "full");
			 
					$imgSrc = $img[0];

			  if(!empty($symbol)) {
				  if($symbol_position=="after"){
					$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span><span>'.esc_html($symbol).'</span>';
					}elseif($symbol_position=="before"){
						$symbol2 = '<span>'.esc_html($symbol).'</span><span class="theserivce-milestone-number" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span>';
					}
				} else {
					$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($number).'</span>';
				}
			if($icon_align=='left'){
				$alignment_no='text-left';
			}elseif($icon_align=='center'){
				$alignment_no='text-center';
			}elseif($icon_align=='right'){
				$alignment_no='text-right';
			}
			if($icon_align=='left'){
				$alignment_no_num='numtext-left';
			}elseif($icon_align=='center'){
				$alignment_no_num='numtext-center';
			}elseif($icon_align=='right'){
				$alignment_no_num='numtext-right';
			}
			if($icon_align=='left'){
				$icon_alignment_no='icon-left';
			}elseif($icon_align=='center'){
				$icon_alignment_no='icon-center';
			}elseif($icon_align=='right'){
				$icon_alignment_no='icon-right';
			}
			$rand_no=rand(1000000, 1500000);
				if($icn_style == "style_2") {
					$footer_css= 'style="';
						if($cont_bg!= "") {
							$footer_css.='background: '.esc_attr($cont_bg).';';
						}
					$footer_css.= '"';
				}
				
				$border_bottom = 'style="';
					if($bd_height!= "") {
						$border_bottom .='border-width: '.esc_attr($bd_height).';';
					}
					if($bd_clr!= "") {
						$border_bottom .='border-color: '.esc_attr($bd_clr).';';
					}
					if($border_width!= "") {
						$border_bottom .='width: '.esc_attr($border_width).';';
					}
				$border_bottom .= '"';
				
			if($subtitle_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $subtitle_google_fonts );
				$subtitle_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($subtitle_use_theme_fonts=='custom-font-family'){
				$subtitle_font_family='font-family:'.$subtitle_font_family.';font-weight:'.$subtitle_font_weight.';';
			}else{
				$subtitle_font_family='';
			}	
				$subtitle_css = ' style="';
				   if($sub_title_clr!= "") {
					$subtitle_css .= 'color: '.esc_attr($sub_title_clr).';';
					}
					if($subtitle_size!= "") {
					$subtitle_css .='font-size:'.esc_attr($subtitle_size).';';
					}
					if($subtitle_line!= "") {
					$subtitle_css .='line-height:'.esc_attr($subtitle_line).';';
					}
					if($subtitle_letter!= "") {
					$subtitle_css .='letter-spacing:'.esc_attr($subtitle_letter).';';
					}  
					$subtitle_css .= $subtitle_font_family;
				$subtitle_css .= '"';

			if($digit_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $digit_google_fonts );
				$digit_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($digit_use_theme_fonts=='custom-font-family'){
				$digit_font_family='font-family:'.$digit_font_family.';font-weight:'.$digit_font_weight.';';
			}else{
				$digit_font_family='';
			}
				$title_css = ' style="';
				   if($style_color != "") {
					$title_css .= 'color: '.esc_attr($style_color).';';
					}
					if($no_size_tag != "") {
					$title_css .='font-size:'.esc_attr($no_size_tag).';';
					}
					if($no_line!= "") {
					$title_css .='line-height:'.esc_attr($no_line).';';
					}
					if($no_letter!= "") {
					$title_css .='letter-spacing:'.esc_attr($no_letter).';';
					} 
					$title_css .= $digit_font_family;
				$title_css .= '"';
			   
				$icon_style = 'style="';
				if($icon_2custom_color != "") {
					$icon_style .='color: '.esc_attr($icon_2custom_color).';';
				}

						if($icon_font_size != "") {
					$icon_style .='font-size : '.esc_attr($icon_font_size).'em;';
				}
				$icon_style .= '"';
				
				
				
				$icon_background_style = 'style="';
					if($icon_custom_background_color != "") {
					$icon_background_style .='background : '.esc_attr($icon_custom_background_color).';';
					}
					if($icon_border_width != "") {
					$icon_background_style .='border : '.esc_attr($icon_border_width).' solid;';
					}
					if($icon_border_color != "") {
					$icon_background_style .='border-color : '.esc_attr($icon_border_color).';';
					}

				$icon_background_style .= '"';	

				$icon_img_background_style = 'style="';
					if($icon_custom_background_color != "") {
					$icon_img_background_style  .='background : '.esc_attr($icon_custom_background_color).';';
					}
					if($icon_border_width != "") {
					$icon_img_background_style .='border : '.esc_attr($icon_border_width).' solid;';
					}
					if($icon_border_color != "") {
					$icon_img_background_style .='border-color : '.esc_attr($icon_border_color).';';
					}
				$icon_img_background_style .= '"';	



					$icon_counter_background_style = 'style="';
				
					if($background_color_counter != "") {
					$icon_counter_background_style .='background : '.esc_attr($background_color_counter).';';
					}	
						if($bd_width != "") {
					$icon_counter_background_style .='border : '.esc_attr($bd_width).' '.esc_attr($box_border).' '.esc_attr($bd_clr).';';
					}
					if($bd_rad != "") {
					$icon_counter_background_style .='-moz-border-radius:'.esc_attr($bd_rad).';-webkit-border-radius: '.esc_attr($bd_rad).';border-radius : '.esc_attr($bd_rad).';';
					}
				$icon_counter_background_style .= '"';

			  if($icn_style=='style_1'){
				$icn_style_class='icn-style-1';
			}elseif($icn_style=='center'){
				$icn_style_class='icon-center';
			}elseif($icn_style=='right'){
				$icn_style_class='icon-right';
			}else{
				$icn_style_class='';
			}

			if($title_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $title_google_fonts );
				$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($title_use_theme_fonts=='custom-font-family'){
				$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
			}else{
				$title_font_family='';
			}	
				$title_font = 'style="';
					if($sub_style_color!=''){
						$title_font .=' color : '.esc_attr($sub_style_color).';';
					}
					if($title_size!=''){
						$title_font .=' font-size : '.esc_attr($title_size).';';
					}
					
					if($title_line!=''){
						$title_font .=' line-height : '.esc_attr($title_line).';';
					}
					if($title_letter!=''){
						$title_font .=' letter-spacing : '.esc_attr($title_letter).';';
					}
					$title_font .=$title_font_family;
				$title_font .= '"';
			vc_icon_element_fonts_enqueue( $type );
			//echo $icon_fontawesome;
			$type12= $type; 
			$iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
				
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
				
			$svg_attach = wp_get_attachment_image_src( $svg_image,true);
						$svg_url = $svg_attach[0];
				if($icon_imge =="image_"){
					$icon_img_ic ='<div class="ts-icon-img icon-img-b '.esc_attr($icon_alignment_no).'" '.$icon_img_background_style.'>';
							$icon_img_ic .='<img class="" src='.esc_url($imgSrc).' alt="" />';
					$icon_img_ic .='</div>';
				}else if($icon_imge =="icon_"){		 
					$icon_img_ic .='<div class="ts-icon icon-img-b '.esc_attr($icon_alignment_no).'" " '.$icon_background_style.'>';
						$icon_img_ic .='<div class="ts-icon-1" >';
							$icon_img_ic .='<span '.$icon_style.' class="counter-icon counter-'.esc_attr($rand_no).' '.$iconClass.'"></span>';
						$icon_img_ic .='</div>';	
					$icon_img_ic .='</div>';	
				}else if($icon_imge == 'svg'){
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
					$svg_uid=uniqid('svg');
					$icon_img_ic ='<div class="pt_plus_animated_svg '.esc_attr($icon_alignment_no).' '.esc_attr($svg_uid).'" data-id="'.esc_attr($svg_uid).'" data-type="'.esc_attr($svg_type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
						$icon_img_ic .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';max-height:'.esc_attr($max_width).';">';
							$icon_img_ic .='<object id="'.esc_attr($svg_uid).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
						$icon_img_ic .='</div>';
					$icon_img_ic .='</div>';
				}	
				if($number!= ''){
				$number_markup = '<h5 class="counter-number  color-'.esc_attr($rand_no).' '.esc_attr($alignment_no_num).'" '.$title_css.' >'.$symbol2.'</h5>';
				}
				if($subject!= ''){
				$subject_markup = '<h6 class="subject-color subject-color-'.esc_attr($rand_no).' '.esc_attr($alignment_no).'"  '.$title_font.'>'.esc_html($subject).'</h6>';
				}
				if($sub_subject!= ''){
				$subject_markup1 = '<h6 class="sub-subject-color sub-subject-color-'.esc_attr($rand_no).' '.esc_attr($alignment_no).'" '.$subtitle_css.'>'.esc_html($sub_subject).'</h6>';
				}

				if($btn_text != ''){
				$button_link = '<div class="icon-btn" ><a class="read-more" href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' '.$btn_style.'>'.esc_html($btn_text).'</a></div>';
				}
				$data_attr =$isotope='';
				if($icn_layout=='carousel_layout'){
					
					$data_attr .=' data-show_arrows="'.esc_attr($show_arrows).'"';
					$data_attr .=' data-show_dots="'.esc_attr($show_dots).'"';
					$data_attr .=' data-show_draggable="'.esc_attr($show_draggable).'"';
					$data_attr .=' data-slide_loop="'.esc_attr($slide_loop).'"';
					$data_attr .=' data-slide_autoplay="'.esc_attr($slide_autoplay).'"';
					$data_attr .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
					$data_attr .=' data-steps_slide="'.esc_attr($steps_slide).'"';
					$data_attr .=' data-carousel_column="'.esc_attr($carousel_column).'"';
					$data_attr .=' data-carousel_tablet_column="'.esc_attr($carousel_tablet_column).'"';
					$data_attr .=' data-carousel_mobile_column="'.esc_attr($carousel_mobile_column).'"';
					$data_attr .=' data-dots_style="slick-dots '.esc_attr($dots_style).'" ';
					$data_attr .=' data-arrows_style="'.esc_attr($arrows_style).'" ';
					$data_attr .=' data-arrows_position="'.esc_attr($arrows_position).'" ';
					
					$data_attr .=' data-dots_border_color="'.esc_attr($dots_border_color).'" ';
					$data_attr .=' data-dots_bg_color="'.esc_attr($dots_bg_color).'" ';
					$data_attr .=' data-dots_active_border_color="'.esc_attr($dots_active_border_color).'" ';
					$data_attr .=' data-dots_active_bg_color="'.esc_attr($dots_active_bg_color).'" ';
					
					$data_attr .=' data-arrow_bg_color="'.esc_attr($arrow_bg_color).'" ';
					$data_attr .=' data-arrow_icon_color="'.esc_attr($arrow_icon_color).'" ';
					$data_attr .=' data-arrow_hover_bg_color="'.esc_attr($arrow_hover_bg_color).'" ';
					$data_attr .=' data-arrow_hover_icon_color="'.esc_attr($arrow_hover_icon_color).'" ';
					$data_attr .=' data-arrow_text_color="'.esc_attr($arrow_text_color).'" ';
					$isotope = 'list-carousel-slick';
				}
					$data_attr .=' data-bg_hover_clr="'.esc_attr($bg_hv_clr_ctr).'" ';
					$data_attr .=' data-icon_2custom_hover_color="'.esc_attr($icon_2custom_hover_color).'" '; 
					$data_attr .=' data-style_hover_color="'.esc_attr($style_hover_color).'" '; 
					
					$data_attr .=' data-title_hv_color="'.esc_attr($title_hv_color).'" '; 
					$data_attr .=' data-sub_title_hv_clr="'.esc_attr($sub_title_hv_clr).'" '; 
					
					$data_attr .=' data-box_shadow="'.esc_attr($box_shadow).'" ';  
					$data_attr .=' data-hover_box_shadow="'.esc_attr($hover_box_shadow).'" ';
				
				$arrow_class='';
				if($arrows_style=='style-4' || $arrows_style=='style-5'){
					$arrow_class=$arrows_position;
				}
				$icon_single ='';
				if ($icn_layout == 'carousel_layout'){
					if(isset($loop_content) && !empty($loop_content) && function_exists('vc_param_group_parse_atts')) {
						$loop_content= (array) vc_param_group_parse_atts( $loop_content);
						
						foreach($loop_content as $item) {
						
							$title_color=$loop_title=$title_line=$title_size=$title_color=$title_letter_spacing=$svg_d_icon=$svg_type=$duration=$loop_image_icon=$svg_image=$loop_max_width=$loop_latter=$list_img=$loop_number_markup=$list_title=$list_subtitle='';
							
							if(!empty($item['loop_latter'])){
								$loop_latter= $item['loop_latter'];
							}
							if(!empty($item['loop_symbol'])){
								$loop_symbol= $item['loop_symbol'];
							}
							if(!empty($item['loop_symbol_position'])){
								$loop_symbol_position= $item['loop_symbol_position'];
							}
							
							 if(!empty($loop_symbol)) {
								  if($loop_symbol_position=="after"){
									$loop_symbol_number = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($loop_latter).'</span><span>'.esc_html($loop_symbol).'</span>';
									}elseif($loop_symbol_position=="before"){
										$loop_symbol_number = '<span>'.esc_html($loop_symbol).'</span><span class="theserivce-milestone-number" data-counterup-nums="'.esc_attr($number).'">'.esc_html($loop_latter).'</span>';
									}
								} else {
									$loop_symbol_number = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($number).'">'.esc_html($loop_latter).'</span>';
								}
					
							if($loop_latter!= ''){
								$loop_number_markup = '<h5 class="counter-number  color-'.esc_attr($rand_no).' '.esc_attr($alignment_no_num).'" '.$title_css.' >'.$loop_symbol_number.'</h5>';
							}
					
							if(!empty($item['loop_title'])){
								$loop_title= $item['loop_title'];
								$list_title = '<h6 class="subject-color subject-color-'.esc_attr($rand_no).' '.esc_attr($alignment_no).'"  '.$title_font.'>'.esc_html($loop_title).'</h6>';
							}
							if(!empty($item['loop_sub'])){
								$loop_sub= $item['loop_sub'];
								$list_subtitle = '<h6 class="sub-subject-color sub-subject-color-'.esc_attr($rand_no).' '.esc_attr($alignment_no).'" '.$subtitle_css.'>'.esc_html($loop_sub).'</h6>';
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
								
								$icon_size= $item['icon_size'];
								$icon_color= $item['icon_color'];
								$icon_line= $item['icon_line'];
								

					
									if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
										if(isset($item['loop_select_image']) && !empty($item['loop_select_image'])){
										$loop_select_image= $item['loop_select_image'];
										$loop_img = wp_get_attachment_image_src("$loop_select_image", "full");
										$loop_imgSrc = $loop_img[0];
										$list_img ='<div class="ts-icon-img icon-img-b '.esc_attr($icon_alignment_no).'" '.$icon_img_background_style.'>';
											$list_img .='<img class="" src='.esc_url($loop_imgSrc).' alt="" />';
										$list_img .='</div>';
										
										}
									}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){		
										$icon_css = ' style="';
										if($icon_color != "") {
										$icon_css .= 'color: '.esc_attr($icon_color).';';
										}	
										if($icon_size != "") {
										$icon_css .= 'font-size: '.esc_attr($icon_size).';';
										}
										if($icon_line != "") {
										$icon_css .= 'line-height: '.esc_attr($icon_line).';';
										}
										
										$icon_css .= '"'; 
										vc_icon_element_fonts_enqueue( $type );
										$type12= $type; 
										$icon_class = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
											$list_img ='<div class="ts-icon icon-img-b '.esc_attr($icon_alignment_no).'" " '.$icon_background_style.'>';
												$list_img .='<div class="ts-icon-1" >';
													$list_img .='<span '.$icon_css.' class="counter-icon counter-'.esc_attr($rand_no).' '.$icon_class.'"></span>';
												$list_img .='</div>';	
											$list_img .='</div>';
										/*$list_img = '<i class=" '.esc_attr($icon_class).' stylish-icon" '.$icon_css.'></i>';*/
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
										$svg_uid=uniqid('svg');
										$list_img ='<div class="pt_plus_animated_svg '.esc_attr($svg_uid).' '.esc_attr($icon_alignment_no).'" data-id="'.esc_attr($svg_uid).'" data-type="'.esc_attr($loop_svg_type).'" data-duration="'.esc_attr($loop_duration).'" data-stroke="'.esc_attr($loop_border_stroke_color).'" data-fill_color="none">';
											$list_img .='<div class="svg_inner_block" style="max-width:'.esc_attr($loop_max_width).';max-height:'.esc_attr($loop_max_width).';">';
												$list_img .='<object id="'.esc_attr($svg_uid).'" type="image/svg+xml" data="'.esc_url($loop_svg_url).'" ></object>';
											$list_img .='</div>';
										$list_img .='</div>';
										
									}	
							}
							

							$icon_single .= '<div class="icon-counte-inner '.esc_attr($pd0).' " '.$icon_counter_background_style.' >';
							if($icn_style == 'style_1'){	
							$icon_single .='<div class="border-icon '.esc_attr($icon_border_box).'" '.$border_box_css .'>
										  '.$list_img.'
										  <div class="ts-milestone-'.esc_attr($rand_no).' icn-txt '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >
											'.$loop_number_markup.'
											<hr class="hr-border" '.$border_bottom.'>
											'.$list_title.'
											'.$list_subtitle.'
											</div>
										</div>';
							}elseif($icn_style == 'style_2'){
								$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
									$icon_single .= '<div class="icn-header">';
										$icon_single .= $list_img;
										$icon_single .= $loop_number_markup;
									$icon_single .= '</div>';	
									
									$icon_single .= '<div class="icn-content" '.$footer_css.'>';
										$icon_single .= $list_title;
										$icon_single .= $list_subtitle;
									$icon_single .= '</div>';
								$icon_single .= '</div>';
							}elseif($icn_style == 'style_3'){
								$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
									$icon_single .= '<div class="icn-top service-media">';
										$icon_single .= $list_img;
										$icon_single .= '<div class="content-center service-content">';
											$icon_single .= $loop_number_markup;
										$icon_single .= '</div>';	
									$icon_single .= '</div>';	
									$icon_single .= '<div class="icn-bottom">';
										$icon_single .= $list_title;
										$icon_single .= $list_subtitle;
									$icon_single .= '</div>';
								$icon_single .= '</div>';
							}elseif($icn_style == 'style_4'){
								$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
									$icon_single .= '<div class="icn-top service-media">';
										$icon_single .= $list_img;
										$icon_single .= '<div class="content-center service-content">';
											$icon_single .= $loop_number_markup;
											$icon_single .= $list_title;
											$icon_single .= $list_subtitle;
										$icon_single .= '</div>';	
									$icon_single .= '</div>';
								$icon_single .= '</div>';
							}
							$icon_single .= '</div>';
						}
					}
				}else if ($icn_layout == 'single_layout'){
					
					$icon_single = '<div class="icon-counte-inner '.esc_attr($pd0).'" '.$icon_counter_background_style.' >';
						if($icn_style == 'style_1'){
						$icon_single .='<div class="border-icon '.esc_attr($icon_border_box).'" '.$border_box_css .'>';
							$icon_single .= $icon_img_ic;
							$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' icn-txt '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
							$icon_single .= $number_markup;
							$icon_single .= '<hr class="hr-border" '.$border_bottom.'>';
							$icon_single .= $subject_markup;
							$icon_single .= $subject_markup1;
							$icon_single .= '</div>';
						$icon_single .= '</div>';	
						}elseif($icn_style == 'style_2'){
							$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
								$icon_single .= '<div class="icn-header">';
									$icon_single .= $icon_img_ic;
									$icon_single .= $number_markup;
								$icon_single .= '</div>';	
								
								$icon_single .= '<div class="icn-content" '.$footer_css.'>';
									$icon_single .= $subject_markup;
									$icon_single .= $subject_markup1;
								$icon_single .= '</div>';
							$icon_single .= '</div>';
						}elseif($icn_style == 'style_3'){
							$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
								$icon_single .= '<div class="icn-top service-media">';
									$icon_single .= $icon_img_ic;
									$icon_single .= '<div class="content-center service-content">';
										$icon_single .= $number_markup;
									$icon_single .= '</div>';	
								$icon_single .= '</div>';	
								
								$icon_single .= '<div class="icn-bottom">';
									
									$icon_single .= $subject_markup;
									$icon_single .= $subject_markup1;
								$icon_single .= '</div>';
							$icon_single .= '</div>';
						}elseif($icn_style == 'style_4'){
							$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >';
								$icon_single .= '<div class="icn-top service-media">';
									$icon_single .= $icon_img_ic;
									$icon_single .= '<div class="content-center service-content">';
										$icon_single .= $number_markup;
										$icon_single .= $subject_markup;
										$icon_single .= $subject_markup1;
									$icon_single .= '</div>';	
								$icon_single .= '</div>';
							$icon_single .= '</div>';
						}else{
							$icon_single .='<div class="ts-milestone-'.esc_attr($rand_no).' icn-txt '.esc_attr($alignment_no).' '.esc_attr($extra_class).'" >'.$number_markup.' '.$subject_markup.' '.$subject_markup1.' </div>';
						}
					$icon_single .= '</div>';
				}
				
					$hover_class  = $hover_attr = '';
				$hover_uniqid = uniqid('hover-effect');
				if ($content_hover_effects == "float_shadow" || $content_hover_effects == "grow_shadow" || $content_hover_effects == "shadow_radial") {
					$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
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
				$uid=uniqid('counter');
				$icon_counter  = '<div class=" content_hover_effect ' . esc_attr($hover_class) . ' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'" ' . $hover_attr . ' " ' . $hover_attr . '>';
				$icon_counter .='<div class="ts-icon-cunter service-icon-'.esc_attr($icn_style).' '.esc_attr($arrow_class).' ts-icon-cunter-'.esc_attr($rand_no).' '.esc_attr($isotope).' '.esc_attr($alignment_no).' '.esc_attr($icn_style_class).' '.esc_attr($animated_class).' '.esc_attr($uid).' "  data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" data-id="'.esc_attr($uid).'" '.$data_attr.' >';
				$icon_counter .= '<div class="post-inner-loop">';
					$icon_counter .= $icon_single;	
				$icon_counter .='</div>';	
				$icon_counter .='</div>';
				$icon_counter .='</div>';
				
				$css_rule='';
				$css_rule .= '<style >';
				$css_rule .= '.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover{background : '.esc_js($bg_hv_clr_ctr).' !important;}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover .counter-icon {color : '.esc_js($icon_2custom_hover_color).' !important;}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner{-moz-box-shadow : '.esc_js($box_shadow).';-webkit-box-shadow : '.esc_js($box_shadow).';box-shadow : '.esc_js($box_shadow).';}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover{-webkit-box-shadow : '.esc_js($hover_box_shadow).';-moz-box-shadow : '.esc_js($hover_box_shadow).';box-shadow : '.esc_js($hover_box_shadow).';}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover .sub-subject-color{color: '.esc_js($sub_title_hv_clr).' !important;}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover .subject-color{color: '.esc_js($title_hv_color).' !important;}.'.esc_js($uid).'.ts-icon-cunter .icon-counte-inner:hover .counter-number{color: '.esc_js($style_hover_color).' !important;}';
				$css_rule .= '</style>';
			return $css_rule.$icon_counter;
		}
		function init_tp_icon_counter(){
			if(function_exists("vc_map"))
			{
			require(THEPLUS_PLUGIN_PATH.'/vc_elements/vc_param/vc_arrays.php');
				vc_map( array(
					  "name" => __( "Icon Counter", "pt_theplus" ),
					  "base" => "tp_icon_counter",
					  "icon" => "tp-icon-counter",
					  "category" => __( "The Plus", "pt_theplus"),
					  "description" => esc_html__('Show Numbers and Icons', 'pt_theplus'),
					  "params" => array(
					array(
						  "type"        => "dropdown",
						  'heading' =>  esc_html__('Select Layout', 'pt_theplus'), 
						  "param_name"  => "icn_layout",
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
					'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Icon Counter Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Styles', 'pt_theplus')), 
					'param_name'  => 'icn_style',
					'simple_mode' => false,
					'value'  => 'style_1',
					'options'     => array(
					 'style_1' => array(
					  'tooltip' => esc_attr__('Style 1','pt_theplus'),
					  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/icon-counter/ts-counter-style-1.jpg'
					 ),
					 'style_2' => array(
					  'tooltip' => esc_attr__('Style 2','pt_theplus'),
					  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/icon-counter/ts-counter-style-2.jpg'
					 ),
					 'style_3' => array(
					  'tooltip' => esc_attr__('Style 3','pt_theplus'),
					  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/icon-counter/ts-counter-style-3.jpg'
					 ),
					 'style_4' => array(
					  'tooltip' => esc_attr__('Style 4','pt_theplus'),
					  'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/icon-counter/ts-counter-style-4.jpg'
					 ),
					),
				   ),	
				  array(
					  "type" => "textfield",
					  'heading' =>  esc_html__('Title', 'pt_theplus'), 
					  "param_name" => "subject",
					  "value" => "Title",
					  "admin_label" => true,
						"dependency" => Array('element' => "icn_layout", 'value' => 'single_layout'),
					  "description" => ""
					),
				  array(
					  "type" => "textfield",
					  'heading' =>  esc_html__('Sub Title', 'pt_theplus'), 
					  "param_name" => "sub_subject",		
					  "admin_label" => true,
					  "dependency" => Array('element' => "icn_layout", 'value' => 'single_layout'),
					  "description" => ""
					),	
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter value of digits/numbers you want to showcase in icon counter. e.g. 200,300.','pt_theplus').'</span></span>'.esc_html__('Digits', 'pt_theplus')), 
					  "param_name" => "number",
					  "value" =>'99',
					  "dependency" => Array('element' => "icn_layout", 'value' => 'single_layout'),
					  "description" => ""
					),
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add any value in this option which will be setup as prefix or postfix on Digits. e.g. +,%,etc.','pt_theplus').'</span></span>'.esc_html__('Symbol Meta Value', 'pt_theplus')), 
					  "param_name" => "symbol",
					  "dependency" => Array('element' => "icn_layout", 'value' => 'single_layout'),
					  "description" => ""
					),
					 array(
					  "type" => "dropdown",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Symbol position using this option.','pt_theplus').'</span></span>'.esc_html__('Symbol Position', 'pt_theplus')),
					  "param_name" => "symbol_position",
					  "value" => array(
						 esc_attr__("After Number", 'pt_theplus') => "after",
						 esc_attr__("Before Number", 'pt_theplus') => "before",
					   ),
					  "description" => "",
					 
					  "dependency" => Array('element' => "icn_layout", 'value' => 'single_layout'),
					   "dependency" => Array('element' => "symbol", 'not_empty' => true),
					),
					array(
							'type'        => 'param_group',
							'heading'     => esc_html__( 'Content', 'pt_theplus' ),
							'param_name'  => 'loop_content',
							"dependency" => Array('element' => "icn_layout", 'value' => 'carousel_layout'),
							'description' => '',
							'params'      => array(
								
										array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add title of icon counter using this option.','pt_theplus').'</span></span>'.esc_html__('Title', 'pt_theplus')), 
								  "param_name" => "loop_title",
								  "value" => '',
								  "description" => __("	", 'pt_theplus')
								),
								 array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add sub title of icon counter using this option.','pt_theplus').'</span></span>'.esc_html__('Sub Title', 'pt_theplus')), 
								  "param_name" => "loop_sub",
								  "value" => '',
								  "description" => ""
								),
								array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter value of digits/numbers you want to showcase in icon counter. e.g. 200,300.','pt_theplus').'</span></span>'.esc_html__('Digits', 'pt_theplus')), 
								  "param_name" => "loop_latter",
								  "value" => '',
								   "edit_field_class" => "vc_col-xs-6",
								  "description" => ""
								),
								array(
								  "type" => "textfield",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add any value in this option which will be setup as prefix or postfix on Digits. e.g. +,%,etc.','pt_theplus').'</span></span>'.esc_html__('Symbol Meta Value', 'pt_theplus')), 
								  "param_name" => "loop_symbol",
								  "value" => '',
								  "edit_field_class" => "vc_col-xs-6",
								  "description" => ""
								),
								 array(
								  "type" => "dropdown",
								  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Symbol position using this option.','pt_theplus').'</span></span>'.esc_html__('Symbol Position', 'pt_theplus')),
								  "param_name" => "loop_symbol_position",
								  "value" => array(
									 esc_attr__("After Number", 'pt_theplus') => "after",
									 esc_attr__("Before Number", 'pt_theplus') => "before",
								   ),
								  "description" => "",		
								  "dependency" => Array('element' => "loop_symbol", 'not_empty' => true),
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
											__( 'Custom Upload', 'pt_theplus' ) => 'img',
											__( 'Pre Built SVG Icon', 'pt_theplus' ) => 'svg_icon',
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
								'description' => '',
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
									"type" => "attach_image",
									"heading" => esc_html__("Use Image As icon", 'pt_theplus') ,
									"value" => "",
									"description" => '',  
									"param_name" => 'loop_select_image',
									'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'image',
									),
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
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
									'param_name' => 'icon_size',
									'value' => '20px',
									'description' => '',
									'dependency' => array(
										'element' => 'loop_image_icon',
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
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
									),	
								array(
										"type" => "textfield",
										'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Icon Line Height', 'pt_theplus')),
										"param_name" => "icon_line",
										'value' => '1.2',
									   "description" => "",
									   "edit_field_class" => "vc_col-xs-6",
									   'dependency' => array(
										'element' => 'loop_image_icon',
										'value' => 'icon',
									),
									),
							),
						),	
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon ', 'pt_theplus')),
								'param_name' => 'icon_imge',
								'value' => array(
									__( 'None', 'pt_theplus' ) => '',
									__( 'Icon', 'pt_theplus' ) => 'icon_',
									__( 'Image', 'pt_theplus' ) => 'image_',
									__( 'Svg', 'pt_theplus' ) => 'svg',
								),
								'std' => 'icon_',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
						array(
						  "type" => "dropdown",
						  "heading" => __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Svg Type', 'pt_theplus')),
						  "param_name" => "svg_icon",
						  "value" => array(
								__( 'Pre Built SVG Icon', 'pt_theplus' ) => 'svg',
								__( 'Custom Upload', 'pt_theplus' ) => 'img',
							),
							'dependency' => array(
								'element' => 'icon_imge',
								'value' => 'svg',
							),
						  "std" => "svg",
						  'group' => __( 'Icon Option', 'pt_theplus' ),
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
										"param_name" => "imge",
										"value" => "",
										"description" => '',  
								'param_name' => 'icon_image',
								'dependency' => array(
									'element' => 'icon_imge',
									'value' => 'image_',
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
									'element' => 'icon_imge',
									'value' => 'icon_',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_fontawesome',
								'value' => 'fa fa-adjust', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false,
									// default true, display an "EMPTY" icon?
									'iconsPerPage' => 4000,
									// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'fontawesome',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_openiconic',
								'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'openiconic',
									'iconsPerPage' => 4000, // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'openiconic',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_typicons',
								'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'typicons',
									'iconsPerPage' => 4000, // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'typicons',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_entypo',
								'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'entypo',
									'iconsPerPage' => 4000, // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'entypo',
								),
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_linecons',
								'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'linecons',
									'iconsPerPage' => 4000, // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'linecons',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'iconpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
								'param_name' => 'icon_monosocial',
								'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'monosocial',
									'iconsPerPage' => 4000, // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'type',
									'value' => 'monosocial',
								),
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
								'param_name' => 'icon_2custom_color',
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
								'dependency' => array(
									'element' => 'icon_imge',
									'value' => 'icon_',
								),
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Hover Color', 'pt_theplus')),
								'param_name' => 'icon_2custom_hover_color',
								'value' =>'#000',
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
								'dependency' => array(
									'element' => 'icon_imge',
									'value' => 'icon_',
								),
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon Size for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
								'param_name' => 'icon_font_size',
								'value' => array( 'Min' => '1',
												  'Small' => '1.6',
												  'Normal' => '2.15',
												  'Large' => '2.85',
												  'Extra Large' => '5',
								) ,
								
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
								'dependency' => array(
									'element' => 'icon_imge',
									'value' => 'icon_',
								),
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Box Alignment for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Box Alignment', 'pt_theplus')),
								'param_name' => 'icon_align',
								'value' => array(
									__( 'Left', 'pt_theplus' ) => 'left',
									__( 'Right', 'pt_theplus' ) => 'right',
									__( 'Center', 'pt_theplus' ) => 'center',
								),
								'description' => '',		
							),
							array(
								'type' => 'vc_link',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add Icon Counter URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Icon Counter URL', 'pt_theplus')),
								'param_name' => 'icon_link',
								'description' => '',
								'group' => __( 'Icon Option', 'pt_theplus' ),
							),

					array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Digits Options', 'pt_theplus'),
							'param_name'		=> 'letter_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
								  "group" =>'Style',
						), 
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
					  "param_name" => "no_size_tag",
					  "value" =>"30px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),
					
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
					  "param_name" => "no_line",
					  "value" => "1",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
					  "param_name" => "no_letter",
					  "value" => "1px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-4",
					),  
					
						 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "style_color",
							"value" => "",
							"group" => "Style",
							"description" => "" ,
							   "edit_field_class" => "vc_col-xs-4",
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "style_hover_color",
							"value" => "",
							"group" => "Style",
							   "edit_field_class" => "vc_col-xs-4",
							"description" => ""  
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Digit Custom font family', 'pt_theplus'),
								'param_name' => 'digit_use_theme_fonts',
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
							'param_name' => 'digit_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'digit_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'digit_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'digit_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'digit_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'digit_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Style', 'pt_theplus'),	
						),	
					array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Title Options', 'pt_theplus'),
							'param_name'		=> 'title_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
								  "group" =>'Style',
						), 
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')), 
					  "param_name" => "title_size",
					  "value" =>"30px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
					  "param_name" => "title_line",
					  "value" => "1",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
					  "param_name" => "title_letter",
					  "value" => "1px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-4",
					),

					 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "sub_style_color",
							"value" => "",
							"group" => "Style",
							"edit_field_class" => "vc_col-xs-4",
							"description" => ""  
						),
						 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "title_hv_color",
							"value" => "",
							"edit_field_class" => "vc_col-xs-4",
							"group" => "Style",
							"description" => ""  
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
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Sub Title Options', 'pt_theplus'),
							'param_name'		=> 'subtitle_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
								  "group" =>'Style',
						),
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
					  "param_name" => "subtitle_size",
					  "value" =>"22px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),
					
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
					  "param_name" => "subtitle_line",
					  "value" => "1",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-6",
					),	
					array(
					  "type" => "textfield",
					  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
					  "param_name" => "subtitle_letter",
					  "value" => "1px",
					  "description" => '',
					   "group" => "Style",
					   "edit_field_class" => "vc_col-xs-4",
					),
						
							 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "sub_title_clr",
							"value" => "",
							"edit_field_class" => "vc_col-xs-4",
							"description" => '',
							"group" => "Style",
						),

					 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "sub_title_hv_clr",
							"value" => "",
							"edit_field_class" => "vc_col-xs-4",
							"description" => '',
							"group" => "Style",
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
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Background Options', 'pt_theplus'),
							'param_name'		=> 'background_option',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
								  "group" =>'Style',
						), 
						array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
								'param_name' => 'background_color_counter',
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
								"edit_field_class" => "vc_col-xs-6",
							),	
						array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background hover using this option.','pt_theplus').'</span></span>'.esc_html__('Background Hover Color', 'pt_theplus')),
								'param_name' => 'bg_hv_clr_ctr',
								"edit_field_class" => "vc_col-xs-6",
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
							),
						 array(
								  "type"        => "checkbox",
								  "heading"     => __("Box Border" , "pt_theplus"),
								  "param_name"  => "box_border",
								  "edit_field_class" => "vc_col-xs-6",
								  "admin_label" => false,
								  "value"       => array(
										'True' => 'true',
									),
									'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_1',
									),
								  "description" => '',
								  "group" => "Style",
						),	
						array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Box Borde using this option.','pt_theplus').'</span></span>'.esc_html__('Box Borde Color', 'pt_theplus')),
								'param_name' => 'box_border_clr',
								'value'=>'#4d4d4d',
								"edit_field_class" => "vc_col-xs-6",
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
								'dependency' => array(
										'element' => 'box_border',
										'value' => 'true',
									),
							),
							
						array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose radius for border using this option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Box Borde Radius', 'pt_theplus')),
								'param_name' => 'bd_rad',
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
								"edit_field_class" => "vc_col-xs-6",
							),
							array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Seprator Settings', 'pt_theplus'),
						'param_name' => 'sep_effect',
						'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_1',
									),
						'group' => __( 'Style', 'pt_theplus' ),
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							'param_name'  => 'border_width',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Seprator Width using this option.','pt_theplus').'</span></span>'.esc_html__('Seprator Width', 'pt_theplus')),
							'description' => '',
							'type'        => 'dropdown',
							'group' => __( 'Style', 'pt_theplus' ),
							"edit_field_class" => "vc_col-xs-4",
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
							'std' => '10%',
							'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_1',
									),
						),	
						array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Seprator height using this option. E.g. 1px, 2px, 3px, etc.','pt_theplus').'</span></span>'.esc_html__('Seprator height', 'pt_theplus')),
								'heading' => __( 'Seprator height ', 'pt_theplus' ),
								'param_name' => 'bd_height',
								"value" => '2px',
								'group' => __( 'Style', 'pt_theplus' ),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_1',
								),
							),
						array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Seprator using this option.','pt_theplus').'</span></span>'.esc_html__('Seprator Color', 'pt_theplus')),
								'param_name' => 'bd_clr',
								"edit_field_class" => "vc_col-xs-4",
								'value'=>'#4d4d4d',				
								'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_1',
									),
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
							),	
						array(
								'type' => 'colorpicker',
								'heading' => __( 'Content background Color', 'pt_theplus' ),
								'param_name' => 'cont_bg',
								'value'=>'#F9B701',
								"edit_field_class" => "vc_col-xs-6",
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
								'dependency' => array(
										'element' => 'icn_style',
										'value' => 'style_2',
									),
						),	
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Box Shadow Setting', 'pt_theplus'),
							'param_name'		=> 'boxshadow_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Style', 'pt_theplus'), 
						),	
						array(
								'type' => 'textfield',
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Box Shadow ', 'pt_theplus')),
								'param_name' => 'box_shadow',
								"value" => '-1px 1px 3px 3px #c6c6c6',
								'description' => '',	
								'group' => __( 'Style', 'pt_theplus' ),
								"edit_field_class" => "vc_col-xs-6",
							),
						
							array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Hover Box Shadow ', 'pt_theplus')),
							'param_name' => 'hover_box_shadow',
							"value" => '9px 5px 20px 4px #c6c6c6',
							'description' => '',	
							'group' => __( 'Style', 'pt_theplus' ),
							"edit_field_class" => "vc_col-xs-6",
						),

							array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Carousel Setting', 'pt_theplus'),
							'param_name'		=> 'carousel_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Carousel', 'pt_theplus'),
							"dependency" => array(
								"element" => "icn_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Desktop screen size( More than 768px width).','pt_theplus').'</span></span>'.esc_html__('Desktop Columns', 'pt_theplus')), 
							"param_name"  => "carousel_column",
							"admin_label" => false,
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
							"element" => "layout",
							"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "icn_layout",
								"value" => array("carousel_layout"),
							),
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Tablet screen size( In between 768px and 480px width).','pt_theplus').'</span></span>'.esc_html__('Tablet Columns', 'pt_theplus')), 
							"param_name"  => "carousel_tablet_column",
							"admin_label" => false,
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
							"element" => "layout",
							"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "icn_layout",
								"value" => array("carousel_layout"),
							),
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Mobile screen size( Less than 480px width).','pt_theplus').'</span></span>'.esc_html__('Mobile Columns', 'pt_theplus')), 
							"param_name"  => "carousel_mobile_column",
							"admin_label" => false,
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
							"element" => "layout",
							"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
								"value" => array("carousel_layout"),
							),
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Loop or Infinite style of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Infinite Mode', 'pt_theplus')),
							'param_name' => 'slide_loop',
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
								"element" => "icn_layout",
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
							'edit_field_class' => 'vc_col-sm-6',
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
								"type" => "textfield",
								 "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
								"param_name" => "extra_class",
								"value" => '',
								"description" => "",
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
				));
			}
		}
	}
	new ThePlus_icon_counter;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_icon_counter'))
	{
		class WPBakeryShortCode_tp_icon_counter extends WPBakeryShortCode {
			protected function contentInline( $atts, $content = null ) {
			 
			}
		}
	}
}