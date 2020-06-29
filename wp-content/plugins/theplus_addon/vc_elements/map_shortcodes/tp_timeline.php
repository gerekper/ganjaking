<?php
// Timeline Elements
if(!class_exists("ThePlus_timeline")){
	class ThePlus_timeline{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_timeline') );
			add_shortcode( 'tp_timeline',array($this,'tp_timeline_shortcode'));
		}
		function tp_timeline_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'style'=>'style-1',
					
					'tl_start'=>'icon',
					'icon_start'=> 'fontawesome',
					'icon_fontawesome'=>'fa fa-adjust',
					 'icon_openiconic'=> 'vc-oi vc-oi-dial',
					 'icon_typicons'=> 'typcn typcn-adjust-brightness',
				   'icon_entypo'=> 'entypo-icon entypo-icon-note',    
					 'icon_linecons'=> 'vc_li vc_li-heart',
					 'icon_monosocial'=> 'vc-mono vc-mono-fivehundredpx',
					'image_start'=>'',
					'text_start'=>'Beginning',
					
					'tl_end'=>'icon',
					'icon_end'=> 'fontawesome',
					'end_fontawesome'=>'fa fa-adjust',
					'end_openiconic'=>'vc-oi vc-oi-dial',
					'end_typicons'=>'typcn typcn-adjust-brightness',
					'end_entypo'=> 'entypo-icon entypo-icon-note',    		  
					'end_linecons_'=>'vc_li vc_li-heart',
					'end_monosocial'=>'vc-mono vc-mono-fivehundredpx',
					'image_end'=>'',
					'text_end'=>'End',
					
					'timeline_content'=>'',
					'pin_center_style'=>'style-1',
					
					'start_pin_size' =>'18px',
					'start_pin_color' =>'#bdbdbd',
					'start_pin_bd_color' =>'#000000',
					'start_pin_bg_color' =>'#ffffff',
					'end_pin_size' =>'18px',
					'end_pin_color' =>'#bdbdbd',
					'end_pin_bd_color' =>'#000000',
					'end_pin_bg_color' =>'#ffffff',
					
					'start_pin_icon_size' =>'18px',
					'start_pin_icon_color' =>'#bdbdbd',
					'end_pin_icon_size' =>'18px',
					'end_pin_icon_color' =>'#bdbdbd',
					
					'pin_icon_color'=>'#222',
					'pin_icon_hover_color'=>'#e2e2e2',
					'pin_icon_bg_color'=>'#e2e2e2',
					'pin_icon_hoverbg_color'=>'#222',
					'pin_icon_border_color' =>'#e2e2e2',
					'pin_icon_hoverborder_color'=>'#222',
					
					'pin_text_color'=>'#222',
					'pin_text_hover_color'=>'#e2e2e2',
					'pin_text_bg_color'=>'#e2e2e2',
					'pin_text_bghover_color'=>'#222',
					
					'title_font_size'=>'22px',
					'title_line_height'=>'36px',
					'title_letter_space'=>'0px',
					'title_color'=>'#333333',
					'title_hover_color'=>'#252525',
					'title_use_theme_fonts'=>'custom-font-family',
					'title_font_family'=>'',
					'title_font_weight'=>'600',
					'title_google_fonts'=>'',
					
					'content_font_size'=>'14px',
					'contnet_line_height'=>'30px',
					'content_letter_space'=>'0px',
					'content_color'=>'#333333',
					'content_hover_color'=>'#252525',
					'content_use_theme_fonts'=>'custom-font-family',
					'content_font_family'=>'',
					'content_font_weight'=>'400',
					'content_google_fonts'=>'',
					
					'box_bg_color'=>'#d8d8d8',
					'box_bg_hover_color'=>'#bdbdbd',
					
					'border_color'=>'#d8d8d8',
					'border_hover_color'=>'#bdbdbd',
					
					'time_line_center' =>'#4d4d4d',
					
					'content_text_alignment' =>'left',
					
					
					), $atts ) );
					$uid=uniqid('timeline');
					$data_class=$timeline_start=$timeline_loop=$timeline_end='';

					if($title_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $title_google_fonts );
					$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($title_use_theme_fonts=='custom-font-family'){
					$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
				}else{
					$title_font_family='';
				}

				if($content_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $content_google_fonts );
					$content_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($content_use_theme_fonts=='custom-font-family'){
					$content_font_family='font-family:'.$content_font_family.';font-weight:'.$content_font_weight.';';
				}else{
					$content_font_family='';
				}
					$data_class=$uid;
					$data_class .=' timeline-'.esc_attr($style).' ';
					
					if($tl_start=='icon'){
						vc_icon_element_fonts_enqueue( $icon_start );
						$icon_class = isset( ${'icon_' . $icon_start} ) ? esc_attr( ${'icon_' . $icon_start} ) : 'fa fa-adjust';
						$icon_img = '<i class=" '.esc_attr($icon_class).'"></i>';
						
						$timeline_start .='<div class="timeline-beginning-icon">'.$icon_img.'</div>';
						
					}else if($tl_start=='image'){
							$image_start = wp_get_attachment_image_src($image_start, "full");
							$imgSrc = $image_start[0];
						$timeline_start .='<div class="timeline-beginning-icon"><img src="'.esc_url($imgSrc).'" alt="" /></div>';
					}else if($tl_start=='text'){
						$timeline_start .='<div class="timeline-text timeline-text-start"><div class="beginning-text">'.esc_html($text_start).'</div></div>';
					}
					
					if($tl_end=='icon'){
					
						vc_icon_element_fonts_enqueue( $icon_end);
						$icon_class_end = isset( ${'end_' . $icon_end} ) ? esc_attr( ${'end_' . $icon_end} ) : 'fa fa-adjust';
						$icon_img_end = '<i class=" '.esc_attr($icon_class_end ).'"></i>';
						
						$timeline_end .='<div class="timeline-end-icon">'.$icon_img_end.'</div>';
					
					}else if($tl_end=='image'){
							$image_end = wp_get_attachment_image_src($image_end, "full");
							$imgSrc = $image_end[0];
						$timeline_end .='<div class="timeline-end-icon"><img src="'.esc_url($imgSrc).'" alt="" /></div>';
					}else if($tl_end=='text'){
						$timeline_end .='<div class="timeline-text timeline-text-end"><div class="end-text">'.esc_html($text_end).'</div></div>';
					}
					
					$timeline_loop='';
					if(isset($timeline_content) && !empty($timeline_content) && function_exists('vc_param_group_parse_atts')) {
						$timeline_content= (array) vc_param_group_parse_atts( $timeline_content);
						
					foreach($timeline_content as $item) {
						$time_line_pin=$time_line_pin_icon=$time_line_pin_icon_text=$content_title=$content_alignment=$content_image=$align_text=$border_bottom=$style_border=$title_border_bottom=$content_text_alignment=$content_desc='';
						
						if(!empty($item['pin_text_position'])){
							$pin_position=$item['pin_text_position'];
						}else{
							$pin_position='position-top';
						}
						
						if(!empty($item['pin_text'])){
							$time_line_pin='<div class="timeline-text-tooltip '.esc_attr($pin_position).' timeline-transition" style="opacity: 1;">'.esc_html($item['pin_text']).'<div class="tooltip-arrow timeline-transition"></div></div>';
						}
						
						if(!empty($item['pin_icon'])){
							$pin_icon=$item['pin_icon'];
							vc_icon_element_fonts_enqueue( $pin_icon );
							$pin_class = isset( $item['pin_' . $pin_icon] ) ? esc_attr( $item['pin_' . $pin_icon] ) : 'fa fa-circle';
							$icon_img = '<i class="point-icon-inner '.esc_attr($pin_class).'"></i>';		
							$time_line_pin_icon ='<div class="timeline-pin-icon">'.$icon_img.'</div>';		
						}
						
						if(!empty($item['content_title'])){
							if(!empty($item['title_url'])){
								$title_url =$item['title_url'];
				  
								$title_url = ( '||' === $title_url ) ? '' : $title_url;
								$title_url= vc_build_link( $title_url);

								$a_href = $title_url['url'];
								$a_title = $title_url['title'];
								$a_target = $title_url['target'];
								$a_rel = $title_url['rel'];
								if ( ! empty( $a_rel ) ) {
									$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
								}
								$content_title='<a class="timeline-item-heading timeline-transition" href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.'>'.esc_html($item['content_title']).'</a>';
							}else{
								$content_title='<h3 class="timeline-item-heading timeline-transition">'.esc_html($item['content_title']).'</h3>';
							}
						}
						
						if(!empty($item['content_image'])){
							$content_image=$item['content_image'];
							$img_size = pt_plus_getImageSquareSize( $content_image,'full');
							//$content_image = wp_get_attachment_image_src($content_image, "full");
							$img = wpb_getImageBySize( array(
								'attach_id' => $content_image,
								'thumb_size' => 'full',
								'class' => 'vc_single_image-img hover__img',
							) );
							$content_image=$img['thumbnail'];
						}
						if(!empty($item['content_desc'])){
							$content_desc='<div class="timeline-item-description timeline-transition"><p>'.esc_html($item['content_desc']).'</p></div>';
						}
						if(!empty($item['content_alignment'])){
						$content_alignment=$item['content_alignment'];
							
						}
						if(!empty($item['content_text_alignment'])){
						$content_text_alignment=$item['content_text_alignment'];
							if($content_text_alignment=='left'){
								$align_text='text-left';
							}else if($content_text_alignment=='center'){			
								$align_text='text-center';
							}else if($content_text_alignment=='right'){			
								$align_text='text-right';
							}
						}
						if(!empty($item['animation_effects'])){
						$animation_delay = $item['animation_delay'];
						$animation_effects = $item['animation_effects'];
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
						}	
						if($style=='style-1'){
							$style_border='<div class="timeline-tl-before"></div>';
							$border_bottom='';
						}
						if($style=='style-2'){
							$style_border='<div class="timeline-tl-before"></div>';
							$title_border_bottom='<div class="border-bottom '.esc_attr($align_text).'"><hr/></div>';
						}
						
						
						$timeline_loop .='<div class="timeline-item-wrap timeline-'.esc_attr($content_alignment).'-content '.esc_attr($animated_class).' text-pin-'.esc_attr($pin_position).'"  data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">
							<div class="timeline-inner-block timeline-transition">
								<div class="timeline-item '.esc_attr($align_text).'">
									'.$style_border.'
									'.$content_title.'
									'.$title_border_bottom.'
									<div class="timeline-content-image">
											'.$content_image.'
									</div>
									'.$content_desc.'
									'.$border_bottom.'
								</div>
								<div class="point-icon '.esc_attr($pin_center_style).'">
									<div class="timeline-tooltip-wrap">
										<div class="timeline-point-icon">
											'.$time_line_pin_icon_text.''.$time_line_pin_icon.'
										</div>
									</div>
									'.$time_line_pin.'
								</div>
							</div>
					</div>';
					}
				}

					$timeline='<div id="pt_plus_timeline" class="pt-plus-timeline-list layout-both '.$data_class.'">';
						$timeline .='<div class="timeline-track"></div>';
						$timeline .='<div class="timeline--icon">'.$timeline_start.'</div>';
						$timeline .=$timeline_loop;
						$timeline .='<div class="timeline--icon">'.$timeline_end.'</div>';
					$timeline .='</div>';
					$css_rule='';
					$css_rule .= '<style >';
					$css_rule .='.'.esc_js($uid).'.timeline-'.esc_js($style).' .point-icon.style-1 .timeline-tooltip-wrap,.'.esc_js($uid).'.timeline-'.esc_js($style).' .point-icon.style-2 .timeline-tooltip-wrap{color:'.esc_js($pin_icon_color).';background:'.esc_js($pin_icon_bg_color).';border-color:'.esc_js($pin_icon_border_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .point-icon.style-1 .timeline-tooltip-wrap,.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .point-icon.style-2 .timeline-tooltip-wrap{color:'.esc_js($pin_icon_hover_color).';background:'.esc_js($pin_icon_hoverbg_color).';border-color:'.esc_js($pin_icon_hoverborder_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text-tooltip{color:'.esc_js($pin_text_color).';background:'.esc_js($pin_text_bg_color).';border-color:'.esc_js($pin_text_bg_color).'}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .timeline-text-tooltip{color:'.esc_js($pin_text_hover_color).';background:'.esc_js($pin_text_bghover_color).';}.pt-plus-timeline-list.layout-both.'.esc_js($uid).'.timeline-'.esc_js($style).' .tooltip-arrow{border-color:'.esc_js($pin_text_bg_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text-tooltip.position-right .tooltip-arrow,.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text-tooltip.position-left .tooltip-arrow{border-color:'.esc_js($pin_text_bg_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .tooltip-arrow {border-color:'.esc_js($pin_text_bghover_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-item-heading{font-size:'.esc_js($title_font_size).';line-height:'.esc_js($title_line_height).';letter-spacing:'.esc_js($title_letter_space).';color:'.esc_js($title_color).';'.esc_js($title_font_family).'}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-item-description p{font-size:'.esc_js($content_font_size).';line-height:'.esc_js($contnet_line_height).';letter-spacing:'.esc_js($content_letter_space).';color:'.esc_js($content_color).'; '.esc_js($content_font_family).'}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .timeline-item-heading{color:'.esc_js($title_hover_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .timeline-item-description p{color:'.esc_js($content_hover_color).';}.'.esc_js($uid).'.timeline-style-2 .timeline-inner-block:hover{background-color:'.esc_js($box_bg_hover_color).'; border:1px solid '.esc_js($border_hover_color).'; }.'.esc_js($uid).'.timeline-style-2 .timeline-inner-block{background-color:'.esc_js($box_bg_color).';  border:1px solid '.esc_js($border_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text .beginning-text{color:'.esc_js($start_pin_color).'; font-size: '.esc_js($start_pin_size).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text-start{border-color:'.esc_js($start_pin_bd_color).';background-color:'.esc_js($start_pin_bg_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text .end-text{color:'.esc_js($end_pin_color).';font-size: '.esc_js($end_pin_size).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-text-end{border-color:'.esc_js($end_pin_bd_color).';background-color:'.esc_js($end_pin_bg_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-track{background:'.esc_js($time_line_center).';} .'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-beginning-icon{color:'.esc_js($start_pin_icon_color).'; font-size: '.esc_js($start_pin_icon_size).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-end-icon{color:'.esc_js($end_pin_icon_color).'; font-size: '.esc_js($end_pin_icon_size).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .border-bottom hr{border-color:'.esc_js($title_color).';}.'.esc_js($uid).'.timeline-'.esc_js($style).' .timeline-inner-block:hover .border-bottom hr{border-color:'.esc_js($title_hover_color).';}.'.esc_js($uid).'.timeline-style-2 .timeline-tl-before{border-color:'.esc_js($border_color).';}.'.esc_js($uid).'.timeline-style-2 .timeline-inner-block:hover .timeline-tl-before{border-color:'.esc_js($border_hover_color).';}';
					
					$css_rule .= '</style>';
					return $css_rule.$timeline;
		}
		function init_tp_timeline(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
						"name" => __("Timeline", "pt_theplus"),
						"base" => "tp_timeline",
						"icon" => "tp-timeline",
						"description" => esc_html__('Showcase History Effectively', 'pt_theplus'),
						"category" => __("The Plus", "pt_theplus"),
						"params" => array(
							array(
								"type" => "dropdown",
								"heading" =>  esc_html__('Select Style', 'pt_theplus'),
								'admin_label' => true,
								"param_name" => "style",
								"value" => array(
									__("Style-1", "pt_theplus") => "style-1",
									__("Style-2", "pt_theplus") => "style-2"
								),
								"description" => "",
								"std" => 'style-1'
							),
							array(
								"type" => "dropdown",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon/Image/Text as a Symbol of Pin Start from these options.','pt_theplus').'</span></span>'.esc_html__('Starting Pin', 'pt_theplus')), 
								"param_name" => "tl_start",
								"value" => array(
									__("Icon", "pt_theplus") => "icon",
									__("Image", "pt_theplus") => "image",
									__("Text", "pt_theplus") => "text"
								),
								"description" => "",
								"std" => 'icon'
							),
							array(
								'type' => 'dropdown',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Linecons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')), 
								'value' => array(
									__('Font Awesome', 'pt_theplus') => 'fontawesome',
									__('Open Iconic', 'pt_theplus') => 'openiconic',
									__('Typicons', 'pt_theplus') => 'typicons',
									__('Entypo', 'pt_theplus') => 'entypo',
									__( 'Linecons', 'pt_theplus' ) => 'linecons',
									__('Mono Social', 'pt_theplus') => 'monosocial'
								),
								
								'param_name' => 'icon_start',
								'description' => "",
								'dependency' => array(
									'element' => 'tl_start',
									'value' => 'icon'
								)
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_fontawesome',
								'value' => 'fa fa-adjust', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false,
									// default true, display an "EMPTY" icon?
									'iconsPerPage' => 4000
									// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'fontawesome'
								),
								
								'description' => '',
								
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_openiconic',
								'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'openiconic',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'openiconic'
								),
								
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_typicons',
								'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'typicons',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'typicons'
								),
								
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_entypo',
								'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'entypo',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'entypo'
								)
								
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_linecons',
								'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'linecons',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'linecons'
								),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'icon_monosocial',
								'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'monosocial',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_start',
									'value' => 'monosocial'
								),
								
								'description' => '',
							),
							array(
								'type' => 'attach_image',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload an image as a starting icon by uploading that here.','pt_theplus').'</span></span>'.esc_html__('Upload Image ', 'pt_theplus')), 
								'param_name' => 'image_start',
								'value' => '',
								'description' => "",
								'dependency' => array(
									'element' => 'tl_start',
									'value' => array(
										'image'
									)
								)
							),
							array(
								"type" => "textfield",
							   "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can write a text here for starting Point Section.','pt_theplus').'</span></span>'.esc_html__('Starting pin Text', 'pt_theplus')), 
								"param_name" => "text_start",
								"value" => 'Beginning',
								"dependency" => array(
									'element' => "tl_start",
									'value' => 'text'
								)
							),
							array(
								"type" => "dropdown",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon/Image/Text as a Symbol of Pin End from these options.','pt_theplus').'</span></span>'.esc_html__('Ending Pin', 'pt_theplus')), 
								"param_name" => "tl_end",
								"value" => array(
									__("Icon", "pt_theplus") => "icon",
									__("Image", "pt_theplus") => "image",
									__("Text", "pt_theplus") => "text"
								),
								"description" => "",
								"std" => 'icon'
							),
							
							array(
								'type' => 'dropdown',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Linecons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')), 
								'value' => array(
									__('Font Awesome', 'pt_theplus') => 'fontawesome',
									__('Open Iconic', 'pt_theplus') => 'openiconic',
									__('Typicons', 'pt_theplus') => 'typicons',
									__('Entypo', 'pt_theplus') => 'entypo',
									__( 'Linecons', 'pt_theplus' ) => 'linecons',
									__('Mono Social', 'pt_theplus') => 'monosocial'
								),
								
								'param_name' => 'icon_end',
								'description' => "",
								'dependency' => array(
									'element' => 'tl_end',
									'value' => 'icon'
								)
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_fontawesome',
								'value' => 'fa fa-adjust', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false,
									// default true, display an "EMPTY" icon?
									'iconsPerPage' => 4000
									// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'fontawesome'
								),
								
								'description' => '',
								
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_openiconic',
								'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'openiconic',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'openiconic'
								),
								
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_typicons',
								'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'typicons',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'typicons'
								),
								
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_entypo',
								'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'entypo',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'entypo'
								)
								
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_linecons',
								'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'linecons',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'linecons'
								),
								'description' => '',
							),
							array(
								'type' => 'iconpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon ', 'pt_theplus')), 
								'param_name' => 'end_monosocial',
								'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
								'settings' => array(
									'emptyIcon' => false, // default true, display an "EMPTY" icon?
									'type' => 'monosocial',
									'iconsPerPage' => 4000 // default 100, how many icons per/page to display
								),
								'dependency' => array(
									'element' => 'icon_end',
									'value' => 'monosocial'
								),
								
								'description' => '',
							),
							array(
								'type' => 'attach_image',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload an image as a Ending icon by uploading that here.','pt_theplus').'</span></span>'.esc_html__('Upload Image ', 'pt_theplus')), 
								'param_name' => 'image_end',
								'value' => '',
								'description' => "",
								'dependency' => array(
									'element' => 'tl_end',
									'value' => array(
										'image'
									)
								)
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can write a text here for Ending Point Section.','pt_theplus').'</span></span>'.esc_html__('Ending Pin Text', 'pt_theplus')), 
								"param_name" => "text_end",
								"value" => 'End',
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'dropdown',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Icon Styles for middle part of timeline from here.','pt_theplus').'</span></span>'.esc_html__('Center Pin Icon Style', 'pt_theplus')), 
								'value' => array(
									__('Style-1', 'pt_theplus') => 'style-1',
									__('Style-2', 'pt_theplus') => 'style-2'
								),
								'param_name' => 'pin_center_style',
								'description' => '',
								'std' => 'style-1'
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Start and end Pin setting', 'pt_theplus'),
								'param_name' => 'pin_start_end_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Start Pin Font Size', 'pt_theplus')),
								'param_name' => 'start_pin_size',
								"description" => "",
								'value' => '18px',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Start Pin Font Color', 'pt_theplus')),
								'param_name' => 'start_pin_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Start Pin Border Color', 'pt_theplus')),
								'param_name' => 'start_pin_bd_color',
								"description" => "",
								'value' => '#000000',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Start pin background Color', 'pt_theplus')),
								'param_name' => 'start_pin_bg_color',
								"description" => "",
								'value' => '#ffffff',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('End Pin Font size', 'pt_theplus')),
								'heading' => __('End Pin Font size', 'pt_theplus'),
								'param_name' => 'end_pin_size',
								"description" => "",
								'value' => '18px',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('End Pin Color', 'pt_theplus')),
								'param_name' => 'end_pin_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('End Pin Border Color', 'pt_theplus')),
								'param_name' => 'end_pin_bd_color',
								"description" => "",
								'value' => '#000000',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('End Pin Backgroud Color', 'pt_theplus')),
								'param_name' => 'end_pin_bg_color',
								"description" => "",
								'value' => '#ffffff',
								'edit_field_class' => 'vc_col-xs-12',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'text'
								)
							),
							
							array(
								'type' => 'textfield',
								 "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Start Pin Icon Size', 'pt_theplus')),
								'param_name' => 'start_pin_icon_size',
								"description" => "",
								'value' => '18px',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'icon'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Start Pin Icon Color', 'pt_theplus')),
								'param_name' => 'start_pin_icon_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'icon'
								)
							),
							array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('End Pin Icon Size', 'pt_theplus')),
								'param_name' => 'end_pin_icon_size',
								"description" => "",
								'value' => '18px',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'icon'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('End Pin Icon Color', 'pt_theplus')),
								'param_name' => 'end_pin_icon_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "tl_end",
									'value' => 'icon'
								)
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Inner Pin Icon Style', 'pt_theplus'),
								'param_name' => 'icon_style_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus')
							),
							
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Timeline Icon Color', 'pt_theplus')),
								'param_name' => 'time_line_center',
								"description" => "",
								'value' => '#4d4d4d',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Color', 'pt_theplus')),
								'param_name' => 'pin_icon_color',
								"description" => "",
								'value' => '#222',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Hover Color', 'pt_theplus')),
								'param_name' => 'pin_icon_hover_color',
								"description" => "",
								'value' => '#e2e2e2',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Backgroud Color', 'pt_theplus')),
								'param_name' => 'pin_icon_bg_color',
								"description" => "",
								'value' => '#e2e2e2',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Backgroud Hover Color', 'pt_theplus')),
								'param_name' => 'pin_icon_hoverbg_color',
								"description" => "",
								'value' => '#222',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Border Color', 'pt_theplus')),
								'param_name' => 'pin_icon_border_color',
								"description" => "",
								'value' => '#e2e2e2',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Icon Border Hover Color', 'pt_theplus')),
								'param_name' => 'pin_icon_hoverborder_color',
								"description" => "",
								'value' => '#222',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Inner Pin Text Style', 'pt_theplus'),
								'param_name' => 'text_style_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Text Color', 'pt_theplus')),
								'param_name' => 'pin_text_color',
								"description" => "",
								'value' => '#222',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Text Hover Color', 'pt_theplus')),
								'param_name' => 'pin_text_hover_color',
								"description" => "",
								'value' => '#e2e2e2',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Text Backgroud Color', 'pt_theplus')),
								'param_name' => 'pin_text_bg_color',
								"description" => "",
								'value' => '#e2e2e2',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Pin Text Backgroud Hover Color', 'pt_theplus')),
								'param_name' => 'pin_text_bghover_color',
								"description" => "",
								'value' => '#222',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Title Style', 'pt_theplus'),
								'param_name' => 'title_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_font_size",
								"value" => '22px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_line_height",
								"value" => '36px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_letter_space",
								"value" => '0px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color', 'pt_theplus')),
								'param_name' => 'title_color',
								"description" => "",
								'value' => '#333333',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Title Hover Color', 'pt_theplus')),
								'param_name' => 'title_hover_color',
								"description" => "",
								'value' => '#252525',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
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
									'group' => esc_attr__('Styling', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'title_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styling', 'pt_theplus'),	
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
								'group' => esc_attr__('Styling', 'pt_theplus'),	
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
									'group' => esc_attr__('Styling', 'pt_theplus'),	
							),	
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Content Style', 'pt_theplus'),
								'param_name' => 'content_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "content_font_size",
								"value" => '14px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "contnet_line_height",
								"value" => '30px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "content_letter_space",
								"value" => '0px',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
								'param_name' => 'content_color',
								"description" => "",
								'value' => '#666666',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Hover Color', 'pt_theplus')),
								'param_name' => 'content_hover_color',
								"description" => "",
								'value' => '#252525',
								'edit_field_class' => 'vc_col-xs-4',
								'group' => esc_attr__('Styling', 'pt_theplus')
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Content Custom font family', 'pt_theplus'),
									'param_name' => 'content_use_theme_fonts',
									 "value" => array(
										esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
										esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
									),
									'std' =>  'custom-font-family',
									'group' => esc_attr__('Styling', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'content_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styling', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'content_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'content_font_weight',
								'value' => __('400','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styling', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'content_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'content_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'content_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Styling', 'pt_theplus'),	
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Box Style', 'pt_theplus'),
								'param_name' => 'box_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "style",
									'value' => 'style-2'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Box Backgroud Color', 'pt_theplus')),
								'param_name' => 'box_bg_color',
								"description" => "",
								'value' => '#d8d8d8',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "style",
									'value' => 'style-2'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Box Backgroud Hover Color', 'pt_theplus')),
								'param_name' => 'box_bg_hover_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "style",
									'value' => 'style-2'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Backgroud Color', 'pt_theplus')),
								'param_name' => 'border_color',
								"description" => "",
								'value' => '#d8d8d8',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "style",
									'value' => 'style-2'
								)
							),
							array(
								'type' => 'colorpicker',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Backgroud Hover Color', 'pt_theplus')),
								'param_name' => 'border_hover_color',
								"description" => "",
								'value' => '#bdbdbd',
								'edit_field_class' => 'vc_col-xs-6',
								'group' => esc_attr__('Styling', 'pt_theplus'),
								"dependency" => array(
									'element' => "style",
									'value' => 'style-2'
								)
							),
							/* group content */
							array(
								'type' => 'param_group',
								'heading' => esc_html__('Content', 'pt_theplus'),
								'param_name' => 'timeline_content',
								'description' => '',
								'params' => array(
									
									array(
										'type' => 'dropdown',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We have given options of icons from Font Awesome, Open Iconic, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon library', 'pt_theplus')),
										'value' => array(
											__('Font Awesome', 'pt_theplus') => 'fontawesome',
											__('Open Iconic', 'pt_theplus') => 'openiconic',
											__('Typicons', 'pt_theplus') => 'typicons',
											__('Entypo', 'pt_theplus') => 'entypo',
											__('Mono Social', 'pt_theplus') => 'monosocial',
											__('Linecons', 'pt_theplus') => 'linecons'
											
										),
										
										'param_name' => 'pin_icon',
										'description' => "",
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_fontawesome',
										'value' => 'fa fa-circle', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false,
											// default true, display an "EMPTY" icon?
											'iconsPerPage' => 4000
											// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'fontawesome'
										),
										
										'description' => '',
										
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_openiconic',
										'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false, // default true, display an "EMPTY" icon?
											'type' => 'openiconic',
											'iconsPerPage' => 4000 // default 100, how many icons per/page to display
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'openiconic'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_typicons',
										'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false, // default true, display an "EMPTY" icon?
											'type' => 'typicons',
											'iconsPerPage' => 4000 // default 100, how many icons per/page to display
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'typicons'
										),
										
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_entypo',
										'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false, // default true, display an "EMPTY" icon?
											'type' => 'entypo',
											'iconsPerPage' => 4000 // default 100, how many icons per/page to display
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'entypo'
										)
										
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_linecons',
										'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false, // default true, display an "EMPTY" icon?
											'type' => 'linecons',
											'iconsPerPage' => 4000 // default 100, how many icons per/page to display
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'linecons'
										),
										'description' => '',
									),
									array(
										'type' => 'iconpicker',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon	', 'pt_theplus')),
										'param_name' => 'pin_monosocial',
										'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
										'settings' => array(
											'emptyIcon' => false, // default true, display an "EMPTY" icon?
											'type' => 'monosocial',
											'iconsPerPage' => 4000 // default 100, how many icons per/page to display
										),
										'dependency' => array(
											'element' => 'pin_icon',
											'value' => 'monosocial'
										),
										'description' => '',
										
									),
									
									array(
										"type" => "textfield",
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Heading of Pin from this section.','pt_theplus').'</span></span>'.esc_html__('Pin Heading Text', 'pt_theplus')),
										"param_name" => "pin_text",
										'admin_label' => true,
										"value" => ''
									),
									array(
										'type' => 'dropdown',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select a position of Heading using this section.','pt_theplus').'</span></span>'.esc_html__('Pin Heading Position', 'pt_theplus')),
										'value' => array(
											__('Top Position', 'pt_theplus') => 'position-top',
											__('Bottom Position', 'pt_theplus') => 'position-bottom',
											__('Left Position', 'pt_theplus') => 'position-left',
											__('Right Position', 'pt_theplus') => 'position-right'
										),
										'param_name' => 'pin_text_position',
										'description' => '',
										'std' => 'position-top'
									),
									array(
										'type' => 'pt_theplus_heading_param',
										'text' => esc_html__('Content Timeline', 'pt_theplus'),
										'param_name' => 'timeline_heading',
										'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12'
									),
									array(
										'type' => 'textfield',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add text for content&#39;s title from this option.','pt_theplus').'</span></span>'.esc_html__('Content Title Text', 'pt_theplus')),
										'param_name' => 'content_title',
										'value' => '',
										'admin_label' => true,
										'description' => '',
									),
									array(
										'type' => 'vc_link',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Content Section on Click  Link, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Content Section URL', 'pt_theplus')),
										'param_name' => 'title_url',
										'description' => '',
									),
									array(
										'type' => 'attach_image',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can Add Content Section&#39;s Image from this option.','pt_theplus').'</span></span>'.esc_html__('Content Section Image', 'pt_theplus')),
										'param_name' => 'content_image',
										'value' => '',
										'description' => '',
									),
									array(
										'type' => 'textarea',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Add Content Description from this option.','pt_theplus').'</span></span>'.esc_html__('Content Description Text', 'pt_theplus')),
										'param_name' => 'content_desc',
										'value' => '',
										'description' => '',
									),
									array(
										"type" => "dropdown",
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set Alignment of Main part from this option.','pt_theplus').'</span></span>'.esc_html__('Main Section Alignment', 'pt_theplus')),
										"param_name" => "content_alignment",
										"value" => array(
											__("Left", "pt_theplus") => "left",
											__("Right", "pt_theplus") => "right"
										),
										"description" => "",
										"std" => 'left'
									),
									array(
										"type" => "dropdown",
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set Alignment of Content part from this option.','pt_theplus').'</span></span>'.esc_html__('Content Section Alignment', 'pt_theplus')),
										"param_name" => "content_text_alignment",
										"value" => array(
											__("Left", "pt_theplus") => "left",
											__("Center", "pt_theplus") => "center",
											__("Right", "pt_theplus") => "right"
										),
										"description" => "",
										"std" => 'left'
									),
									array(
										'type' => 'pt_theplus_heading_param',
										'text' => esc_html__('Animation Settings', 'pt_theplus'),
										'param_name' => 'annimation_effect',
										'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
									),	
									array(
										"type" => "dropdown",
										"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from.','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
										"param_name" => "animation_effects",
										
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
										'edit_field_class' => 'vc_col-sm-6',
										'std' => 'no-animation'
									),
									array(
										"type" => "textfield",
										"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add value of on load column gap time in millisecond. 1 sec = 1000 Millisecond','pt_theplus').'</span></span>'.esc_html__('Animation Gap Time', 'pt_theplus')),	
										"param_name" => "animation_delay",
										"value" => '50',
										'edit_field_class' => 'vc_col-sm-6',
										"description" => ""
									)
								),
								'group' => esc_attr__('Content', 'pt_theplus')
							),
							/* group content */
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
	new ThePlus_timeline;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_timeline'))
	{
		class WPBakeryShortCode_tp_timeline extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}


