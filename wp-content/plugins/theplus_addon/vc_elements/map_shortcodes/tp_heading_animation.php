<?php 
// Heading Animation Elements
if(!class_exists("ThePlus_heading_animation")){
	class ThePlus_heading_animation{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_heading_animation') );
			add_shortcode( 'tp_heading_animation',array($this,'tp_heading_animation_shortcode'));
		}
		function tp_heading_animation_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				"prefix" => 'This Is Demo',
				"postfix" => '',
				"ani_title" => '',
				"anim_styles" => 'style-1',
				"heading_anim_color" => '#313131',
				"postfix_prifix_font_size" => '25px',
				"post_pre_height" => '29px',
				'heading_use_theme_fonts'=>'custom-font-family',
				'heading_font_family'=>'',
				'heading_font_weight'=>'400',
				'heading_google_fonts'=>'',
				
				
				"heading_text_align" => '',
				'anim_use_theme_fonts'=>'custom-font-family',
				'anim_font_family'=>'',
				'anim_font_weight'=>'400',
				'anim_google_fonts'=>'',
				"ani_color" => '#313131',
				"ani_size" => '27px',
				"ani_line_height" => '32px',
				"ani_bg_color" =>'#d3d3d3',
				"anit_bold" =>'true',
				"anit_uinderline"=> 'true',
				"anit_italic" => 'true',
				"an_spacing" => '1px',
				'animation_effects'=>'no-animation',
				  'animation_delay'=>'50',
				"el_class" =>'',
				), $atts ) );
				
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
				
				$head_style = $ani_title_bold = $ani_title_under = $ani_title_italic='';
				
				if($heading_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $heading_google_fonts );
				$heading_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($heading_use_theme_fonts=='custom-font-family'){
				$heading_font_family='font-family:'.$heading_font_family.';font-weight:'.$heading_font_weight.';';
			}else{
				$heading_font_family='';
			}

				$heading_title = 'style="';
				if($heading_anim_color != "") {
					$heading_title .='color: '.esc_attr($heading_anim_color).';';
				}
				if($postfix_prifix_font_size != "") {
					$heading_title .='font-size: '.esc_attr($postfix_prifix_font_size).';';
				}
				
				if($heading_text_align != "") {
					$heading_title .='text-align: '.esc_attr($heading_text_align).';';
				}	
				if($post_pre_height != "") {
					$heading_title .='line-height: '.esc_attr($post_pre_height).';';
				}	
				$heading_title .=$heading_font_family;
				$heading_title .= '"';
				
				if($anim_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $anim_google_fonts );
				$anim_font_family = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($anim_use_theme_fonts=='custom-font-family'){
				$anim_font_family='font-family:'.$anim_font_family.';font-weight:'.$anim_font_weight.';';
			}else{
				$anim_font_family='';
			}
				$heading_animation1 = 'style="';
				if($ani_color != "") {
					$heading_animation1 .='color: '.esc_attr($ani_color).';';
				}
				if($ani_size != "") {
					$heading_animation1 .='font-size: '.esc_attr($ani_size).';';
				}
				if($ani_line_height != "") {
					$heading_animation1 .='line-height: '.esc_attr($ani_line_height).';';
				}
				if($ani_bg_color != "") {
					$heading_animation1 .='background: '.esc_attr($ani_bg_color).';';
				}	
				if($an_spacing != "") {
					$heading_animation1 .='letter-spacing: '.esc_attr($an_spacing).';';
				}
				$heading_animation1 .=$anim_font_family;
				$heading_animation1 .= '"';	
				
				
				$heading_animation_style = 'style="';
				if($anit_bold == "true") {
					$heading_animation_style .='font-weight: bold;';
				}			
				if($anit_uinderline == "true") {
					$heading_animation_style .='text-decoration: underline;';
				}	
				if($anit_italic == "true") {
					$heading_animation_style .='font-style:italic;';
				}
				$heading_animation_style .= '"';
				$heading_animation_back = 'style="';
				if($ani_bg_color != "") {
					$heading_animation_back .='background: '.esc_attr($ani_bg_color).';';
				}
				$heading_animation_back .= '"';		
				
				
				// Order of replacement
				$order   = array("\r\n", "\n", "\r", "<br/>", "<br>");
				$replace = '|';
				
				// Processes \r\n's first so they aren't converted twice.
				$str = str_replace($order, $replace, $ani_title);
				
				$lines = explode("|", $str);
				
				$count_lines = count($lines);
				
				if($anit_bold == "true") {
					$ani_title_bold .='ani-bold ';
				}else{
					$ani_title_bold .='ani-b-normal ';
				}
				if($anit_uinderline == "true") {
					$ani_title_under .='ani-underline ';
				}else{
					$ani_title_under .='ani-u-normal ';
				}
				
				if($anit_italic == "true") {
					$ani_title_italic .='ani-italic ';
				}else{
					$ani_title_italic .='ani-i-normal ';
				}
				
				$background_css='';
					if(!empty($ani_color)) {
							$background_css .= 'background-color: '.esc_attr($ani_color).';';
					}

				$head_style = $ani_title_bold.$ani_title_under.$ani_title_italic;
				
				$uid=uniqid('heading-animation');
				
				$heading_animation ='<div class="pt-plus-heading-animation heading-animation head-anim-'.esc_attr($anim_styles).' '.esc_attr($animated_class).' '.esc_attr($el_class).' '.esc_attr($uid).'"  data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
				
				if ($anim_styles == 'style-1') {	
					$heading_animation .='<h1 class="pt-plus-cd-headline letters type" '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.$prefix.' </span>';	
					}
					$heading_animation .='<span class="cd-words-wrapper waiting" '.$heading_animation_back.'>';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= '<b '.$heading_animation1.'  class="is-visible '.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= '<b '.$heading_animation1.' class="'.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
						}
						$i++;
					}
					
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';		
					$heading_animation .='</span>';
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).' </span>';	
					}
					$heading_animation .='</h1>';
				}
				if ($anim_styles == 'style-2') {
					$heading_animation .='<h1 class="pt-plus-cd-headline rotate-1" '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.esc_html($prefix).' </span>';	
					}	
					$heading_animation .='<span class="cd-words-wrapper">';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= '<b '.$heading_animation1.' class="is-visible '.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= '<b '.$heading_animation1.' class="'.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
						}
						$i++;
					} 
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';
					$heading_animation .='</span>';	
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).' </span>';	
					}
					$heading_animation .='</h1>';	
				}
				if ($anim_styles == 'style-3') {
					$heading_animation .='<h1 class="pt-plus-cd-headline zoom" '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.esc_html($prefix).' </span>';	
					}	
					$heading_animation .='<span class="cd-words-wrapper">';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= ' <b '.$heading_animation1.' class="is-visible '.esc_attr($head_style).'">'.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= ' <b '.$heading_animation1.' class="'.esc_attr($head_style).'">'.strip_tags($line).'</b>';
						}
						$i++;
					}
					
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';
					$heading_animation .='</span>';
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).' </span>';	
					}		
					$heading_animation .='</h1>';	
				}
				if ($anim_styles == 'style-4') {
					$heading_animation .='<h1 class="pt-plus-cd-headline loading-bar " '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.esc_html($prefix).' </span>';	
					}
					$heading_animation .='<span class="cd-words-wrapper">';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= ' <b '.$heading_animation1.' class="is-visible '.esc_attr($head_style).'">'.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= ' <b '.$heading_animation1.' class="'.esc_attr($head_style).'">'.strip_tags($line).'</b>';
						}
						$i++;
					}
					
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';				
					$heading_animation .='</span>';	
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).'</span>';	
					}		
					$heading_animation .='</h1>';	
				}		
				if ($anim_styles == 'style-5') {
					$heading_animation .='<h1 class="pt-plus-cd-headline push" '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.esc_html($prefix).' </span>';	
					}
					$heading_animation .='<span class="cd-words-wrapper">';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= '<b '.$heading_animation1.' class="is-visible '.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= '<b '.$heading_animation1.' class="'.esc_attr($head_style).'"> '.strip_tags($line).'</b>';
						}
						$i++;
					}
					
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';
					$heading_animation .='</span>';	
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).' </span>';	
					}		
					$heading_animation .='</h1>';
				}
				if ($anim_styles == 'style-6') {
					$heading_animation .='<h1 class="pt-plus-cd-headline letters scale"" '.$heading_title.'>';
					if($prefix != ''){
						$heading_animation .='<span '.$heading_title.'>'.esc_html($prefix).' </span>';	
					}
					$heading_animation .='<span class="cd-words-wrapper style-6"  '.$heading_animation1.' >';
					$i=0;
					foreach($lines as $line)
					{
						if($i==0){
							
							$heading_animation .= '<b '.$heading_animation_style.' class="is-visible  '.esc_attr($head_style).'">'.strip_tags($line).'</b>';
							
							}else{
							$heading_animation .= '<b class="'.esc_attr($head_style).'" >'.strip_tags($line).'</b>';
						}
						$i++;
					}
					
					$strings = '['; 
					foreach($lines as $key => $line)  
					{ 
						$strings .= trim(htmlspecialchars_decode(strip_tags($line)));
						if($key != ($count_lines-1))
						$strings .= ','; 
					} 
					$strings .= ']';
						$heading_animation .='</span>';	
					if($postfix != ''){
						$heading_animation .='<span '.$heading_title.'> '.esc_html($postfix).' </span>';	
					}
					$heading_animation .='</h1>';	
				}
				$heading_animation .='</div>';
				
				$css_rule='';
				$css_rule .= '<style>';
				$css_rule .= '.'.esc_js($uid).' .pt-plus-cd-headline.loading-bar .cd-words-wrapper::after{'.esc_js($background_css).'}';
				$css_rule .= '</style>';
				return $css_rule.$heading_animation;
		}
		function init_tp_heading_animation(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Animated Text", "pt_theplus"),
					"base" => "tp_heading_animation",
					'icon' => 'tp-heading-animation',
					'description' => 'Animate your Words',
					"category" => __("The Plus", "pt_theplus"),
					"params" => array(
						array(
							'type' => 'dropdown',
							'heading' =>  esc_html__('Styles', 'pt_theplus'),
							'param_name' => 'anim_styles',
							'description' => '',
							'value' => array(
								__('Style-1', 'pt_theplus') => 'style-1',
								__('Style-2', 'pt_theplus') => 'style-2',
								__('style-3', 'pt_theplus') => 'style-3',
								__('style-4', 'pt_theplus') => 'style-4',
								__('style-5', 'pt_theplus') => 'style-5',
								__('style-6', 'pt_theplus') => 'style-6'
							),
							'admin_label' => true,
							'std' => 'style-1'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text, Which will be show before/ahead the Animated Text.','pt_theplus').'</span></span>'.esc_html__('Prefix Text', 'pt_theplus')),
							"param_name" => "prefix",
							
							"value" => __("This Is Demo", "pt_theplus"),
							"description" => ""
						),
						array(
							'type' => 'textarea',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Multiple Text by adding Enter in each line. You can use ctrl + Enter Or Cmnd + Enter to add new line in text area.','pt_theplus').'</span></span>'.esc_html__('Title of Heading Animation', 'pt_theplus')),
							'param_name' => 'ani_title',
							"value" => "",
							'description' => ""
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Text, Which will be show After/Behind the Animated Text.','pt_theplus').'</span></span>'.esc_html__('Postfix Text ', 'pt_theplus')),
							'param_name' => 'postfix',
							'admin_label' => true,
							"value" => "",
							'description' => '',
							
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select alignment of whole Section using this option.','pt_theplus').'</span></span>'.esc_html__('Content Alignment  ', 'pt_theplus')),
							'param_name' => 'heading_text_align',
							'value' => array(
								__('Left', 'pt_theplus') => 'left',
								__('Center', 'pt_theplus') => 'center',
								__('Right', 'pt_theplus') => 'right'
							),
							'description' => "",
							'group' => '',
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Prefix And Postfix Setting', 'pt_theplus'),
							'param_name' => 'pos_pre_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styles', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							'heading' => __('Prefix and Postfix', 'pt_theplus'),
							'param_name' => 'heading_anim_color',
							'value' => __('#313131','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							'param_name' => 'postfix_prifix_font_size',
							'value' => __('25px','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							'param_name' => 'post_pre_height',
							'value' => __('29px','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Heading Custom Font Family', 'pt_theplus'),
								'param_name' => 'heading_use_theme_fonts',
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
							'param_name' => 'heading_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'heading_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'heading_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'heading_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'heading_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'heading_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),	
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Animation Setting', 'pt_theplus'),
							'param_name' => 'pos_pre_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styles', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							'param_name' => 'ani_color',
							'value' => __('#313131','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							'param_name' => 'ani_size',
							'value' => __('27px','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						 array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add line height in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							'param_name' => 'ani_line_height',
							'value' => __('32px','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-4'
						),
						array(
							'type' => '',
							'heading' => __('Title Font Style', 'pt_theplus'),
							'param_name' => 'font_stye',
							'description' => '',
							'group' => __('Styles', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Underline', 'pt_theplus'),
							'param_name' => 'anit_uinderline',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'group' => __('Styles', 'pt_theplus'),
							"edit_field_class" => "vc_col-xs-4"
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Italic', 'pt_theplus'),
							'param_name' => 'anit_italic',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'group' => __('Styles', 'pt_theplus'),
							"edit_field_class" => "vc_col-xs-4"
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Bold', 'pt_theplus'),
							'param_name' => 'anit_bold',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'group' => __('Styles', 'pt_theplus'),
							"edit_field_class" => "vc_col-xs-4"
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							'param_name' => 'an_spacing',
							'value' => __('1px','pt_theplus'),
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-6'
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for animation background using this option.','pt_theplus').'</span></span>'.esc_html__('Animation Background Color', 'pt_theplus')),
							'param_name' => 'ani_bg_color',
							'value' => '#d3d3d3',
							'description' => '',
							'group' => __('Styles', 'pt_theplus'),
							'edit_field_class' => 'vc_col-sm-6'
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Animation Text font family', 'pt_theplus'),
								'param_name' => 'anim_use_theme_fonts',
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
							'param_name' => 'anim_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'anim_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'anim_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'anim_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'anim_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'anim_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),	
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
	new ThePlus_heading_animation;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_heading_animation'))
	{
		class WPBakeryShortCode_tp_heading_animation extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}