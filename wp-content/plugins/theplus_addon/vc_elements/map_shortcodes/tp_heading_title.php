<?php
// Heading Title Elements
if(!class_exists("ThePlus_heading_title")){
	class ThePlus_heading_title{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_heading_title') );
			add_shortcode( 'tp_heading_title',array($this,'tp_heading_title_shortcode'));
		}
		function tp_heading_title_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'title'			 => 'Heading',
				'title_s'		 => 'Title',
				'title_s_postion' => 'text_after',
				'sub_title'		 => 'Sub Title',
				'title_color_o' =>'solid',
				'title_color1' =>'#1e73be',
				'title_color2' =>'#2fcbce',
				'title_hover_style' =>'horizontal',
				'title_color'	 => '#ccc',

				'sub_color_o' =>'solid',
				'sub_color1' =>'#1e73be',
				'sub_color2' =>'#2fcbce',
				'sub_hover_style' =>'horizontal',
				'sub_color'		 => '#ccc',
				'heading_style'  =>'style_1',
				'title_align' 	 =>'text-center',
				'sub_align'		 =>'text-center',
				'position'		 => 'before',
				'title_s_color_o' =>'solid',
				'title_s_color1' =>'#1e73be',
				'title_s_color2' =>'#2fcbce',
				'title_s_hover_style' =>'horizontal',
				'title_s_color'   =>'#ca2b2b', 
				'top_clr'         =>'#1e73be',
				'sep_img'         =>'',
				'sep_clr'  		  =>'#4099c3',
				'sep_width'		  =>'100%',
				'sep_height'	  =>'2px',
				'dot_color' =>'#ca2b2b',
				'double_color'   => '#4d4d4d',
				'double_top'      => '6px',
				'double_bottom'      => '2px',
				'title_h'         =>'h2',
				'title_h_size'    =>'',
				'title_h_line'    =>'1.3',
				'title_h_letter_spacing' =>'2px',
				'title_text_transform' =>'capitalize',
				'title_use_theme_fonts'=>'custom-font-family',
				'title_font_family'=>'',
				'title_font_weight'=>'600',
				'title_google_fonts'=>'',
				'tablet_title_h_size' =>'',
				'tablet_title_h_line' =>'',
				'tablet_title_h_letter_space' =>'',
				'mobile_title_h_size' =>'',
				'mobile_title_h_line' =>'',
				'mobile_title_h_letter_space' =>'',
				
				'mobile_center_align' => 'off',
				
				'subtitle_font'  => 'h3',
				'subtitle_size'   => '',
				'subtitle_line'    =>'1.2',
				'subtitle_letter_spacing' =>'2px',
				'subtitle_text_transform' =>'capitalize',
				'subtitle_use_theme_fonts'=>'custom-font-family',
				'subtitle_font_family'=>'',
				'subtitle_font_weight'=>'400',
				'subtitle_google_fonts'=>'',
				'tablet_sub_size' =>'',
				'tablet_sub_line' =>'',
				'tablet_sub_letter_space' => '',
				'mobile_sub_size' =>'',
				'mobile_sub_line' => '',
				'mobile_sub_letter_space' =>'',
				
				'special_effect' => 'off',
				'effect_color_1' => '#313131',
				'effect_color_2' => '#ff214f',
				
				'title_s_size'   => '',
				'title_s_line'    =>'1.2',
				'title_s_letter_spacing' =>'2px',
				'title_s_use_theme_fonts'=>'custom-font-family',
				'title_s_font_family'=>'',
				'title_s_font_weight'=>'600',
				'title_s_google_fonts'=>'',
				'tablet_title_s_size' =>'',
				'tablet_title_s_line' =>'',
				'tablet_title_s_letter_space' =>'',
				'mobile_title_s_size' =>'',
				'mobile_title_s_line' =>'',
				'mobile_title_s_letter_space' =>'',
			
				'animation_effects'=>'no-animation',
				'animation_delay'=>'50',
				'el_class' => '',
				'css' =>'',
		   ), $atts ) );
		   
		   $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,$el_class . vc_shortcode_custom_css_class( $css, ' ' ), 'tp_heading_title', $atts );
		  
		 
		
				if($sep_img){
					$img = wp_get_attachment_image_src($sep_img, "full"); 
					$imgSrc = $img[0];
				}else{
					$imgSrc='';
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

		if($title_color_o == "gradient") {
			$title_gradient_cass = 'heading-title-gradient';
		   }else{
			$title_gradient_cass = '';
		 }
		if($title_s_color_o == "gradient") {
			$title_s_gradient_cass = 'heading-title-gradient';
		   }else{
			$title_s_gradient_cass = '';
		 }
		if($sub_color_o == "gradient") {
			$sub_gradient_cass = 'heading-title-gradient';
		   }else{
			$sub_gradient_cass = '';
		 }
		$titlle = ' style="';

			   if($title_color_o == "gradient") {
				$titlle .= pt_plus_gradient_color($title_color1,$title_color2,$title_hover_style);
			   }else{
				$titlle .= 'color: '.esc_attr($title_color).';';
			  }				
			
			if($title_h_size != "") {
				$titlle .='font-size:'.esc_attr($title_h_size).';';
			}
			if($title_h_line != "") {
				$titlle .='line-height:'.esc_attr($title_h_line).';';
			}
			if($title_h_letter_spacing != "") {
				$titlle .='letter-spacing :'.esc_attr($title_h_letter_spacing).';';
			}
			if($title_text_transform!= "") {
				$titlle .='text-transform :'.esc_attr($title_text_transform).';';
			}
			$titlle .= $title_font_family;
			$titlle .= '"';
			
		if($title_s_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $title_s_google_fonts );
			$title_s_font_family = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
		}elseif($title_s_use_theme_fonts=='custom-font-family'){
			$title_s_font_family='font-family:'.$title_s_font_family.';font-weight:'.$title_s_font_weight.';';
		}else{
			$title_s_font_family='';
		}
		$title_2 = ' style="';
			   if($title_s_color_o == "gradient") {
				$title_2 .= pt_plus_gradient_color($title_s_color1,$title_s_color2,$title_s_hover_style);
			   }else{
				$title_2 .= 'color: '.esc_attr($title_s_color).';';
			  }
			
			if($title_s_size != "") {
				$title_2 .='font-size:'.esc_attr($title_s_size).';';
			}
			if($title_s_line != "") {
				$title_2 .='line-height:'.esc_attr($title_s_line).';';
			}
			if($title_s_letter_spacing != "") {
				$title_2 .='letter-spacing :'.esc_attr($title_s_letter_spacing).';';
			}
			$title_2 .=$title_s_font_family;
			$title_2 .= '"';


		if($subtitle_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $subtitle_google_fonts );
			$subtitle_font_family = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
		}elseif($subtitle_use_theme_fonts=='custom-font-family'){
			$subtitle_font_family='font-family:'.$subtitle_font_family.';font-weight:'.$subtitle_font_weight.';';
		}else{
			$subtitle_font_family='';
		}
			$sub = ' style="';

			if($sub_color_o == "gradient") {
				$sub .= pt_plus_gradient_color($sub_color1,$sub_color2,$sub_hover_style);
			   }else{
				$sub .= 'color: '.esc_attr($sub_color).';';
			  }		
			
			if($subtitle_size != "") {
				$sub .='font-size:'.esc_attr($subtitle_size).';';
			}
			if($subtitle_line != "") {
				$sub .='line-height:'.esc_attr($subtitle_line).';';
			}
			if($subtitle_letter_spacing != "") {
				$sub .='letter-spacing :'.esc_attr($subtitle_letter_spacing).';';
			}
			if($subtitle_text_transform!= "") {
				$sub .='text-transform :'.esc_attr($subtitle_text_transform).';';
			}
			
			$sub .=$subtitle_font_family;
			$sub .= '"';
			
				$vtop_clr = ' style="';
			if($top_clr != "") {
				$vtop_clr .='background-color:'.esc_attr($top_clr).';';
			}

			$vtop_clr .= '"';
			
			$sep_style = ' style="';
			if($sep_clr != "") {
				$sep_style .='border-color:'.esc_attr($sep_clr).';';
			}
			if($sep_width != "") {
				$sep_style .='width:'.esc_attr($sep_width).';';
			}
			if($sep_height != "") {
				$sep_style .='border-width:'.esc_attr($sep_height).';';
			}
			

			$sep_style .= '"';
			
			$sep_width_st = ' style="';
			if($sep_width != "") {
				$sep_width_st .='width:'.esc_attr($sep_width).';';
			}
			$sep_width_st .= '"';
			

			$dot_color_b = ' style="';
			if($dot_color != "") {
				$dot_color_b .='color:'.esc_attr($dot_color).';';
			}
			$dot_color_b .= '"';
		$style_class='';
			if($heading_style =="style_1"){
				$style_class = 'style-1';
			}else if($heading_style =="style_2"){
				$style_class = 'style-2';
			}else if($heading_style =="style_4"){
				$style_class = 'style-4';
			}else if($heading_style =="style_5"){
				$style_class = 'style-5';
			}else if($heading_style =="style_6"){
				$style_class = 'style-6';
			}else if($heading_style =="style_7"){
				$style_class = 'style-7';
			}else if($heading_style =="style_8"){
				$style_class = 'style-8';
			}else if($heading_style =="style_9"){
				$style_class = 'style-9';
			}else if($heading_style =="style_10"){
				$style_class = 'style-10';
			}else if($heading_style =="style_11"){
				$style_class = 'style-11';
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
			
			
			$uid=uniqid('heading_style');
			
			$heading ='<div class="heading heading_style '.esc_attr($uid).'  '.esc_attr($css_class).'  '.esc_attr($style_class).' '.esc_attr($animated_class).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" >';
			
				$mobile_center='';
				if(!empty($mobile_center_align) && $mobile_center_align=='on'){
					if ($heading_style =="style_1" || $heading_style =="style_2" || $heading_style =="style_4" || $heading_style =="style_5"  || $heading_style =="style_7" || $heading_style =="style_9"){
						$mobile_center='heading-mobile-center';
					}			
				}
				$heading .='<div class="sub-style" >';

				if ($heading_style =="style_6"){
				$heading .='<div class="vertical-divider top" '.$vtop_clr.' > </div>';
				}
					$title_con= $s_title_con = $title_s_before ='';
					
					if($heading_style =="style_1" ){
									$title_s_before .='<span class="title-s '.$title_s_gradient_cass.'" '.$title_2.'> '.esc_html($title_s).' </span>';
					}
						
						if($title !=""){
						
							$reveal_effects=$effect_attr='';
							if ($heading_style =="style_1" || $heading_style =="style_2" || $heading_style =="style_8"){
								if(!empty($special_effect) && $special_effect=='on'){
									$effect_rand_no =uniqid('reveal');
									$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
									$effect_attr .=' data-effect-color-1="'.esc_attr($effect_color_1).'" ';
									$effect_attr .=' data-effect-color-2="'.esc_attr($effect_color_2).'" ';
									$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
								}
							}
							$title_con ='<div class="head-title '.esc_attr($title_align).' '.esc_attr($mobile_center).'" > ';
								$title_con .='<'.esc_attr($title_h).' class="heading-title '.esc_attr($title_align).' '.esc_attr($mobile_center).' '.esc_attr($reveal_effects).' '.esc_attr($title_gradient_cass).'" '.$effect_attr.' '.$titlle .'  data-hover="'.esc_attr($title).'">';
								if($title_s_postion =="text_before"){
									$title_con.= $title_s_before.$title;
								}else{
									$title_con.= $title.$title_s_before;
								}
								$title_con .='</'.esc_attr($title_h).'>';

								if ($heading_style =="style_4" || $heading_style =="style_9"){
									$title_con .='<div class="seprator sep-l" '.$sep_width_st.'>';
									$title_con .='<span class="title-sep sep-l" '.$sep_style.'></span>';
									if ($heading_style =="style_9" ){
										$title_con .='<div class="sep-dot" '.$dot_color_b.'>.</div>';
									}else{	
									  if($imgSrc !=''){  
										$title_con .='<div class="sep-mg"><img src="'.esc_url($imgSrc).'" class="" alt="" /></div>';
									  }
									}
									$title_con .='<span class="title-sep sep-r" '.$sep_style.'></span>';
									$title_con .='</div>';
								}
							$title_con .='</div>';
						}
						if($sub_title !=""){
							$s_title_con ='<div class="sub-heading">';
							$s_title_con .='<'.esc_attr($subtitle_font).' class="heading-sub-title '.esc_attr($sub_align).' '.esc_attr($mobile_center).' '.$sub_gradient_cass.'" '.$sub.'> '.esc_html($sub_title).' </'.esc_attr($subtitle_font).'>';
							$s_title_con .='</div>';
						}
						if($position =="before"){
							$heading.= $s_title_con.$title_con;
							
						}if($position =="after"){
							$heading.= $title_con.$s_title_con;
						}
				if ($heading_style =="style_6"){
				$heading .='<div class="vertical-divider bottom" '.$vtop_clr.'> </div>';
				}
				$heading.='</div>';
			$heading.='</div>';

			
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .= '@media (min-width:601px) and (max-width:991px){.'.esc_js($uid).'.heading .heading-title{';
			if($tablet_title_h_size!=''){
				$css_rule .= 'font-size:'.esc_js($tablet_title_h_size).' !important;';
			}
			if($tablet_title_h_line!=''){
				$css_rule .= 'line-height:'.esc_js($tablet_title_h_line).' !important;';
			}
			if($tablet_title_h_letter_space!=''){
				$css_rule .= 'letter-spacing:'.esc_js($tablet_title_h_letter_space).' !important;';
			}
			$css_rule .= '}';
			$css_rule .= '.'.esc_js($uid).'.heading .heading-sub-title{';
			if($tablet_sub_size!=''){
				$css_rule .= 'font-size:'.esc_js($tablet_sub_size).' !important;';
			}
			if($tablet_sub_line!=''){
				$css_rule .= 'line-height:'.esc_js($tablet_sub_line).' !important;';
			}
			if($tablet_sub_letter_space!=''){
				$css_rule .= 'letter-spacing:'.esc_js($tablet_sub_letter_space).' !important;';
			}
			$css_rule .= '}';
			$css_rule .= '.'.esc_js($uid).'.heading .heading-title .title-s{';
			if($tablet_title_s_size!=''){
				$css_rule .= 'font-size:'.esc_js($tablet_title_s_size).' !important;';
			}
			if($tablet_title_s_line!=''){
				$css_rule .= 'line-height:'.esc_js($tablet_title_s_line).' !important;';
			}
			if($tablet_title_s_letter_space!=''){
				$css_rule .= 'letter-spacing:'.esc_js($tablet_title_s_letter_space).' !important;';
			}
			$css_rule .= '}}';
			$css_rule .= '@media (max-width:600px){.'.esc_js($uid).'.heading .heading-title{';
			if($mobile_title_h_size!=''){
				$css_rule .= 'font-size:'.esc_js($mobile_title_h_size).' !important;';
			}
			if($mobile_title_h_line!=''){
				$css_rule .= 'line-height:'.esc_js($mobile_title_h_line).' !important;';
			}
			if($mobile_title_h_letter_space!=''){
				$css_rule .= 'letter-spacing:'.esc_js($mobile_title_h_letter_space).' !important;';
			}
			$css_rule .= '} .'.esc_js($uid).'.heading .heading-sub-title{';
			if($mobile_sub_size!=''){
			$css_rule .= 'font-size:'.esc_js($mobile_sub_size).' !important;';
			}
			if($mobile_sub_line!=''){
			$css_rule .= 'line-height:'.esc_js($mobile_sub_line).' !important;';
			}
			if($mobile_sub_letter_space!=''){
			$css_rule .= 'letter-spacing:'.esc_js($mobile_sub_letter_space).' !important;';
			}
			
			$css_rule .= '}.'.esc_js($uid).'.heading .heading-title .title-s{';
			if($mobile_title_s_size!=''){
				$css_rule .= 'font-size:'.esc_js($mobile_title_s_size).' !important;';
			}
			if($mobile_title_s_line!=''){
			$css_rule .= 'line-height:'.esc_js($mobile_title_s_line).' !important;';
			}
			if($mobile_title_s_letter_space!=''){
			$css_rule .= 'letter-spacing:'.esc_js($mobile_title_s_letter_space).' !important;';
			}
			$css_rule .= '}}';
			if ($heading_style =="style_5"){
				$css_rule .= '.'.esc_js($uid).'.heading.style-5 .heading-title:before,.'.esc_js($uid).'.heading.style-5 .heading-title:after{background: '.esc_js($double_color).';} .'.esc_js($uid).'.heading.style-5 .heading-title:before{height: '.esc_js($double_top).';} .'.esc_js($uid).'.heading.style-5 .heading-title:after{height: '.esc_js($double_bottom).';}';
			}
			if ($heading_style =="style_7"){
				$css_rule .= '.'.esc_js($uid).'.heading.style-7 .head-title:after{color : '.esc_js($dot_color).';    text-shadow: 15px 0 '.esc_js($dot_color).', -15px 0 '.esc_js($dot_color).';}';
			}
			$css_rule .= '</style>';	
			
		   return $css_rule.$heading;
		}
		function init_tp_heading_title(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
						"name" => __("Heading Style", "pt_theplus"),
						"base" => "tp_heading_title",
						'icon'	=> 'tp-heading-style',
						"category" => __("The Plus", "pt_theplus"),
						"description" => esc_html__('Creative Heading Options', 'pt_theplus'),
						"params" => array(
							array(
									'type'        => 'radio_select_image',
									'heading' =>  esc_html__('Heading Style', 'pt_theplus'), 
									'param_name'  => 'heading_style',
									'admin_label' => true, 
									'simple_mode' => false,
									'value' => 'style_1',
									'options'     => array(
										'style_1' => array(
											'tooltip' => esc_attr__('Modern','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-1.jpg'
										),
										'style_2' => array(
											'tooltip' => esc_attr__('Simple','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-2.jpg'
										),
										'style_4' => array(
											'tooltip' => esc_attr__('Classic','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-3.jpg'
										),
										'style_5' => array(
											'tooltip' => esc_attr__('Double Border','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-4.jpg'
										),
										'style_6' => array(
											'tooltip' => esc_attr__('Vertical Border','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-5.jpg'
										),
										'style_7' => array(
											'tooltip' => esc_attr__('Dashing Dots','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-6.jpg'
										),
										'style_8' => array(
											'tooltip' => esc_attr__('Unique','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-7.jpg'
										),
										'style_9' => array(
											'tooltip' => esc_attr__('Stylish','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/heading-style/ts-heading-style-8.jpg'
										),
									),
								),
							array(
								"type" => "textarea",
								'heading' =>  esc_html__('Title', 'pt_theplus'), 
								"param_name" => "title",
								"value" => 'Heading',
								"description" => "",
								"admin_label" => true
								
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the part of title and available for some heading styles.','pt_theplus').'</span></span>'.esc_html__('Extra Title', 'pt_theplus')), 
								"param_name" => "title_s",
								"value" => 'Title',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
								"description" => '',
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as prefix or postfix to main title text.','pt_theplus').'</span></span>'.esc_html__('Extra Title Position', 'pt_theplus')),
								"param_name" => "title_s_postion",
								'value' => array(
									__('Prefix', 'pt_theplus') => 'text_after',
									__('Postfix', 'pt_theplus') => 'text_before'
								),
								'std' => "text_after",
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
								"description" => '',
							),
							array(
								"type" => "textfield",
								'heading' =>  esc_html__('Sub Title', 'pt_theplus'), 
								"param_name" => "sub_title",
								"value" => 'Sub Title',
								"admin_label" => true,
								"description" => ""
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Separator Settings', 'pt_theplus'),
								'param_name' => 'sep_effect',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_4',
										'style_5',
										'style_9',
										'style_7'
									)
								),
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for Separator using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
								"param_name" => "double_color",
								"value" => '#4d4d4d',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_5'
									)
								),
								 "edit_field_class" => "vc_col-xs-3",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Separator height using this Option.','pt_theplus').'</span></span>'.esc_html__('Top Separator height', 'pt_theplus')), 
								"param_name" => "double_top",
								"value" => '6px',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_5'
									)
								),
								"edit_field_class" => "vc_col-xs-3",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Separator height using this Option.','pt_theplus').'</span></span>'.esc_html__('Bottom Separator Height', 'pt_theplus')), 
								"param_name" => "double_bottom",
								"value" => '2px',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_5'
									)
								),
								"edit_field_class" => "vc_col-xs-3",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
								"type" => "attach_image",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Separator with image using this option. .jpg, .png, .gif images supported.','pt_theplus').'</span></span>'.esc_html__('Separator With Image', 'pt_theplus')), 
								"param_name" => "sep_img",
								"value" => '',
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_4'
									)
								),
								"description" => "",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Separator using this option.','pt_theplus').'</span></span>'.esc_html__('Separator color', 'pt_theplus')),
								"param_name" => "sep_clr",
								"value" => '#4099c3',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										  'style_4','style_9'
									)
								),
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Separator Width using this option.','pt_theplus').'</span></span>'.esc_html__('Separator Width', 'pt_theplus')),
								"param_name" => "sep_width",
								"edit_field_class" => "vc_col-xs-6",
								"value" => array(
									__('Select width', 'pt_theplus') => '',
									__('10%', 'pt_theplus') => '10%',
									__('20%', 'pt_theplus') => '20%',
									__('30%', 'pt_theplus') => '30%',
									__('40%', 'pt_theplus') => '40%',
									__('50%', 'pt_theplus') => '50%',
									__('60%', 'pt_theplus') => '60%',
									__('70%', 'pt_theplus') => '70%',
									__('80%', 'pt_theplus') => '80%',
									__('90%', 'pt_theplus') => '90%',
									__('100%', 'pt_theplus') => '100%'
								),
								"std" => '100%',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_4',
										'style_9'
									)
								),
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								"admin_label" => false
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for Separator dot using this option.','pt_theplus').'</span></span>'.esc_html__('Separator Dot color', 'pt_theplus')),
								"param_name" => "dot_color",
								"value" => '#ca2b2b',
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_9',
										'style_7'
									)
								),
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6'
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Separator height using this Option.','pt_theplus').'</span></span>'.esc_html__('Separator height', 'pt_theplus')), 
								"param_name" => "sep_height",
								"edit_field_class" => "vc_col-xs-6",
								"value" => '2px',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_4','style_9'
									)
								),
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
						   
						   
							array(
								"type" => "colorpicker",
							   'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for vertical line using this option.','pt_theplus').'</span></span>'.esc_html__('Seperator Vertical Color', 'pt_theplus')),
								"param_name" => "top_clr",
								"value" => '#1e73be',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_6'
									)
								),
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Title Settings', 'pt_theplus'),
								'param_name' => 'title_effect',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select font tag using this option.','pt_theplus').'</span></span>'.esc_html__('Font Tag', 'pt_theplus')),
								"param_name" => "title_h",
								'value' => array(
									__('H1', 'pt_theplus') => 'h1',
									__('H2', 'pt_theplus') => 'h2',
									__('H3', 'pt_theplus') => 'h3',
									__('H4', 'pt_theplus') => 'h4',
									__('H5', 'pt_theplus') => 'h5',
									__('H6', 'pt_theplus') => 'h6',
									__('div', 'pt_theplus') => 'div',
									__('p', 'pt_theplus') => 'p'
								),
								'std' => 'h2',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_h_size",
								'value' => '',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_h_line",
								'value' => '1.3',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_h_letter_spacing",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select option Title Text Transform using this option. Eg. UPPERCASE,Capitalize..','pt_theplus').'</span></span>'.esc_html__('Text Transform', 'pt_theplus'),
									'param_name' => 'title_text_transform',
									 "value" => array(
										esc_html__("None", 'pt_theplus') => "none",
										esc_html__("Capitalize", 'pt_theplus') => "capitalize",
										esc_html__("Uppercase", 'pt_theplus') => "uppercase",
										esc_html__("Lowercase", 'pt_theplus') => "lowercase",
									),
									'std' =>  'capitalize',
									'group' => esc_attr__('Styles', 'pt_theplus'),
									"edit_field_class" => "vc_col-xs-4"	
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
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "title_color",
								"value" => '#ccc',
								"edit_field_class" => "vc_col-xs-4",
								"description" => '',
								'dependency' => array('element' => 'title_color_o','value' => 'solid'),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
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
				'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
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
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Choose Title alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Alignment ', 'pt_theplus')), 
								"param_name" => "title_align",
								'value' => array(
									__('Left', 'pt_theplus') => 'text-left',
									__('Right', 'pt_theplus') => 'text-right',
									__('Center', 'pt_theplus') => 'text-center'
								),
								"edit_field_class" => "vc_col-xs-6",
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_2',
										'style_1',
										'style_4',
										'style_5',
										'style_7',
										'style_9'
									)
								),
								'std' => 'text-center',
								"description" => "",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
								'type' => 'pt_theplus_checkbox',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By enabling this option on scroll spacial effect on section.','pt_theplus').'</span></span>'.esc_html__('Special Effect', 'pt_theplus')), 
								'param_name' => 'special_effect',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-12',
								'value' => 'off',
								'options' => array(
									'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No'
									)
								),
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1','style_2','style_8',
									)
								),
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 1', 'pt_theplus')),
								'param_name' => 'effect_color_1',
								"description" => "",
								'value' => '#313131',
								'edit_field_class' => 'vc_col-xs-6',
								'dependency' => array(
									'element' => 'special_effect',
									'value' => array(
										'on',
									)
								),
								"group" => esc_attr__('Styles', 'pt_theplus'),
							),
							array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 2', 'pt_theplus')),
								'param_name' => 'effect_color_2',
								"description" => "",
								'value' => '#ff214f',
								'edit_field_class' => 'vc_col-xs-6',
								'dependency' => array(
									'element' => 'special_effect',
									'value' => array(
										'on',
									)
								),
								"group" => esc_attr__('Styles', 'pt_theplus'),
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Tablet Responsive', 'pt_theplus'),
								'param_name' => 'tablet_content_title',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "tablet_title_h_size",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "tablet_title_h_line",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "tablet_title_h_letter_space",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Mobile Responsive', 'pt_theplus'),
								'param_name' => 'mobile_content_title',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "mobile_title_h_size",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "mobile_title_h_line",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "mobile_title_h_letter_space",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Extra Title Settings', 'pt_theplus'),
								'param_name' => 'etxra_settings',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
							),
							
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_s_size",
								'value' => '',
								"description" => '',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_s_line",
								'value' => '1.2',
								"description" => '',
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter spacing', 'pt_theplus')),
								"param_name" => "title_s_letter_spacing",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
									)
								),
								"edit_field_class" => "vc_col-xs-6",
								"admin_label" => false
							),
							array(
		  "type"        => "dropdown",
		  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Title Color Options using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color Options', 'pt_theplus')),
		  "param_name"  => "title_s_color_o",
		  "admin_label" => true,
		  "value"       => array(
				__( 'Solid', 'pt_theplus' ) => 'solid',
				__( 'Gradient', 'pt_theplus' ) => 'gradient',
			),
		  "std" => "solid",
		  "description" => "",
		  'group' => esc_attr__('Styles', 'pt_theplus'),
'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1',
										'style_3'
									)
								),
		),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "title_s_color",
								"value" => '#ca2b2b',
								"edit_field_class" => "vc_col-xs-4",
								"description" => '',
								'dependency' => array('element' => 'title_s_color_o','value' => 'solid'),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
array(
		   'type' => 'colorpicker',
		   'heading' => __( 'Color 1', 'pt_theplus' ),
		   'param_name' => 'title_s_color1',  
			'dependency' => array('element' => 'title_s_color_o','value' => 'gradient'),
		   "edit_field_class" => "vc_col-xs-6",
		   "value" => '#1e73be',
		   'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Color 2', 'pt_theplus' ),
			'param_name' => 'title_s_color2',   
			'dependency' => array('element' => 'title_s_color_o','value' => 'gradient'),
			"edit_field_class" => "vc_col-xs-6",
			"value" => '#2fcbce',
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
				'type' => 'dropdown',
				'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
				'param_name' => 'title_s_hover_style',
				'value' => array(
					__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
					__( 'Vertical', 'pt_theplus' ) => 'vertical',
					__( 'Diagonal', 'pt_theplus' ) => 'diagonal',
					__( 'Radial', 'pt_theplus' ) => 'radial',                                
				),
			'std'=>'horizontal',
			'dependency' => array('element' => 'title_s_color_o','value' => 'gradient'),
			"description" => "",
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),

							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Extra Title Custom font family', 'pt_theplus'),
									'param_name' => 'title_s_use_theme_fonts',
									 "value" => array(
										esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
										esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
									),
									'group' => esc_attr__('Styles', 'pt_theplus'),
									'dependency' => array(
									'element' => 'heading_style',
										'value' => array(
											'style_1',
										)
									),
									'std' =>  'custom-font-family',
									'group' => esc_attr__('Styles', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'title_s_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'title_s_use_theme_fonts',
										'value' => 'custom-font-family',
								),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'title_s_font_weight',
								'value' => __('600','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'title_s_use_theme_fonts',
										'value' => 'custom-font-family',
								),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'title_s_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'title_s_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Styles', 'pt_theplus'),	
							),	
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Tablet Responsive', 'pt_theplus'),
								'param_name' => 'tablet_content_title_s',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "tablet_title_s_size",
								"description" => "",
								'value' => '',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "tablet_title_s_line",
								"description" => "",
								'value' => '',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "tablet_title_s_letter_space",
								"description" => "",
								'value' => '',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Mobile Responsive', 'pt_theplus'),
								'param_name' => 'mobile_content_title_s',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "mobile_title_s_size",
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "mobile_title_s_line",
								"description" => "",
								'value' => '',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "mobile_title_s_letter_space",
								"description" => "",
								'value' => '',
								'dependency' => array(
									'element' => 'heading_style',
									 'value' => array(
									  'style_1',
									 )
								),
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Sub Title Settings', 'pt_theplus'),
								'param_name' => 'subtitle_effect',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							),
								
							array(
								"type" => "dropdown",
								"class" => "",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select font tag using this option.','pt_theplus').'</span></span>'.esc_html__('Font Tag', 'pt_theplus')),
								"param_name" => "subtitle_font",
								'value' => array(
									__('H1', 'pt_theplus') => 'h1',
									__('H2', 'pt_theplus') => 'h2',
									__('H3', 'pt_theplus') => 'h3',
									__('H4', 'pt_theplus') => 'h4',
									__('H5', 'pt_theplus') => 'h5',
									__('H6', 'pt_theplus') => 'h6',
									__('div', 'pt_theplus') => 'div',
									__('p', 'pt_theplus') => 'p'
								),
								'std' => 'h3',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size ', 'pt_theplus')),
								"param_name" => "subtitle_size",
								'value' => '',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "subtitle_line",
								'value' => '1.2',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
						   
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "subtitle_letter_spacing",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								"admin_label" => false
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select option SubTitle Text Transform using this option. Eg. UPPERCASE,Capitalize..','pt_theplus').'</span></span>'.esc_html__('Text Transform', 'pt_theplus'),
									'param_name' => 'subtitle_text_transform',
									 "value" => array(
										esc_html__("None", 'pt_theplus') => "none",
										esc_html__("Capitalize", 'pt_theplus') => "capitalize",
										esc_html__("Uppercase", 'pt_theplus') => "uppercase",
										esc_html__("Lowercase", 'pt_theplus') => "lowercase",
									),
									'std' =>  'capitalize',
									'group' => esc_attr__('Styles', 'pt_theplus'),
									"edit_field_class" => "vc_col-xs-4"	
							),
array(
		  "type"        => "dropdown",
		  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Title Color Options using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color Options', 'pt_theplus')),
		  "param_name"  => "sub_color_o",
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
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "sub_color",
								"value" => '#ccc',
								"edit_field_class" => "vc_col-xs-4",
								"description" => '',
								'dependency' => array('element' => 'sub_color_o','value' => 'solid'),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
array(
		   'type' => 'colorpicker',
		   'heading' => __( 'Color 1', 'pt_theplus' ),
		   'param_name' => 'sub_color1',  
			'dependency' => array('element' => 'sub_color_o','value' => 'gradient'),
		   "edit_field_class" => "vc_col-xs-6",
		   "value" => '#1e73be',
		   'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Color 2', 'pt_theplus' ),
			'param_name' => 'sub_color2',   
			'dependency' => array('element' => 'sub_color_o','value' => 'gradient'),
			"edit_field_class" => "vc_col-xs-6",
			"value" => '#2fcbce',
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),
		array(
				'type' => 'dropdown',
				'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
				'param_name' => 'sub_hover_style',
				'value' => array(
					__( 'Horizontal', 'pt_theplus' ) => 'horizontal',
					__( 'Vertical', 'pt_theplus' ) => 'vertical',
					__( 'Diagonal', 'pt_theplus' ) => 'diagonal',
					__( 'Radial', 'pt_theplus' ) => 'radial',                                
				),
			'std'=>'horizontal',
			'dependency' => array('element' => 'sub_color_o','value' => 'gradient'),
			"description" => "",
			'group' => esc_attr__('Styles', 'pt_theplus'),
		),


							 array(
								"type" => "dropdown",            
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose sub title alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Alignment ', 'pt_theplus')), 
								"param_name" => "sub_align",
								'value' => array(
									__('Left', 'pt_theplus') => 'text-left',
									__('Right', 'pt_theplus') => 'text-right',
									__('Center', 'pt_theplus') => 'text-center'
								),
								"edit_field_class" => "vc_col-xs-6",
								'std' => 'text-center',
								"description" => "",
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_2',
										'style_1',
										'style_4',
										'style_5',
										'style_7',
										'style_9'
									)
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Position', 'pt_theplus')), 
								"param_name" => "position",
								'value' => array(
									__('Before Title', 'pt_theplus') => 'before',
									__('After Title', 'pt_theplus') => 'after'
								),
								"edit_field_class" => "vc_col-xs-6",
								'std' => 'before',
								"description" => "",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
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
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Tablet Responsive', 'pt_theplus'),
								'param_name' => 'tablet_content_sub',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "tablet_sub_size",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "tablet_sub_line",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "tablet_sub_letter_space",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Mobile Responsive', 'pt_theplus'),
								'param_name' => 'mobile_content_sub',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "mobile_sub_size",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "mobile_sub_line",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "mobile_sub_letter_space",
								"description" => "",
								'value' => '',
								"group" => esc_html__('Styles', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Mobile Center Alignment', 'pt_theplus'),
								'param_name' => 'mobile_center_content',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Styles', 'pt_theplus'),
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1','style_2','style_4','style_5','style_7','style_9'
									)
								),
							),
							array(
								'type' => 'pt_theplus_checkbox',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('If you enable this option this title will be in center alignment in mobile devices for better look in responsive devices.','pt_theplus').'</span></span>'.esc_html__('Center Alignment In Mobile', 'pt_theplus')), 
								'param_name' => 'mobile_center_align',
								'description' => '',
								'edit_field_class' => 'vc_col-xs-6',
								'value' => 'off',
								'options' => array(
									'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No'
									)
								),
								'dependency' => array(
									'element' => 'heading_style',
									'value' => array(
										'style_1','style_2','style_4','style_5','style_7','style_9'
									)
								),
								"group" => esc_attr__('Styles', 'pt_theplus')
							),
							array(
								'type' => 'css_editor',
								'heading' => __('CSS box', 'pt_theplus'),
								'param_name' => 'css',
								'group' => __('Design Options', 'pt_theplus')
							),
							array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Animation Settings', 'pt_theplus'),
							'param_name' => 'annimation_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							),
							array(
								"type" => "dropdown",
								"heading" => __("Animated Effects", 'pt_theplus'),
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
								"param_name" => "animation_effects",
								"edit_field_class" => "vc_col-xs-6",
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
								"value" => '50',
								"edit_field_class" => "vc_col-xs-6",
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
								"edit_field_class" => "vc_col-xs-6",
								 "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
								"param_name" => "el_class",
								"value" => '',
								"description" => ""
							)
						)
					));
			}
		}
	}
	new ThePlus_heading_title;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_heading_title'))
	{
		class WPBakeryShortCode_tp_heading_title extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}