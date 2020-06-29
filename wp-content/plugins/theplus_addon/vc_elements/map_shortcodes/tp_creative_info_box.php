<?php 
// Creative Info Box Elements
if(!class_exists("ThePlus_creative_info_box")){
	class ThePlus_creative_info_box{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_creative_info_box') );
			add_shortcode( 'tp_creative_info_box',array($this,'tp_creative_info_box_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_creative_info_box_scripts' ), 1 );
		}
		function tp_creative_info_box_scripts() {
			wp_register_script( 'icon_box', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/snap.svg-min.js',array(),'', true );//icon box js
			wp_register_script( 'pt-theplus-creative-infobox', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-creative-infobox.js',array(),VERSION_THEPLUS, true);	
		}
		function tp_creative_info_box_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
			  'info_box_img' =>'',
			  'info_box_title' => 'The Plus',
			  'info_box_sub'=> 'The Plus Info',
			  'info_link' =>'',
			  'info_box_button' =>'',
			  'info_box_style' => 'style-1',
			  
			  'title_size' =>'20px',
			  'title_height' =>'1.4',
			  'title_space' =>'1px',
			  'title_clr' => '#ff004b',
			  'title_use_theme_fonts'=>'custom-font-family',
			'title_font_family'=>'',
			'title_font_weight'=>'600',
			'title_google_fonts'=>'',
			  
			  'subtitle_size' =>'16px',
			  'subtitle_height' =>'1.4',
			  'subtitle_space' =>'1px',
			  'subtitle_clr' => '#d84a8c',
			  'subtitle_use_theme_fonts'=>'custom-font-family',
			'subtitle_font_family'=>'',
			'subtitle_font_weight'=>'400',
			'subtitle_google_fonts'=>'',
			
			  'btn_size' =>'16px',
			  'btn_weight' =>'400',
			  'btn_clr' =>'#121212',
			  'btn_bg' =>'#ffadc7',
			  'btn_space' =>'1px',
			  
			  'svg_bg' =>'#ffffff',
			  
			  'animation_effects'=>'no-animation',
				'animation_delay'=>'50',
				'el_class' =>'',
				), $atts ) );
				wp_enqueue_script( 'icon_box');
				wp_enqueue_script( 'pt-theplus-creative-infobox');
				$fea_img = wp_get_attachment_image_src($info_box_img, "full");
				$imgSrc = $fea_img[0];
				if($info_box_style=='style-1'){
					$info_box_style='pt-plus-info-box-1';
				}else if($info_box_style=='style-2'){
					$info_box_style='pt-plus-info-box-2';
				}else if($info_box_style=='style-3'){
					$info_box_style='pt-plus-info-box-3';
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
			/*---------$animated_class data-animation-speed="'.esc_attr($animation_speed).'" data-animation-delay="'.esc_attr($animation_delay_time).'" data-animation-position="'.esc_attr($animation_effects).'" data-animation-easing="'.esc_attr($animation_ease).'"-----------*/

			
		$button=$title=$subtitle='';
		if($info_box_title !=''){
			
			if($title_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $title_google_fonts );
			$title_style = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
		}elseif($title_use_theme_fonts=='custom-font-family'){
			$title_style='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
		}else{
			$title_style='';
		}
				$title_css = ' style="';
					if($title_clr != "") {
						$title_css .= 'color: '.esc_attr($title_clr).';';
					}	
					if($title_size != "") {
						$title_css .= 'font-size: '.esc_attr($title_size).';';
					}			
					if($title_height != "") {
						$title_css .= 'line-height: '.esc_attr($title_height).';';
					}
					$title_css .=$title_style;
					if($title_space != "") {
						$title_css .= 'letter-spacing: '.esc_attr($title_space).';';
					}
				$title_css .= '"';
				
				 $title = '<h2 class="info-title" '.$title_css.'> '.esc_html($info_box_title).' </h2>';
			}
			
		if($info_box_sub !=''){
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
					if($subtitle_clr != "") {
						$subtitle_css .= 'color: '.esc_attr($subtitle_clr).';';
					}	
					if($subtitle_size != "") {
						$subtitle_css .= 'font-size: '.esc_attr($subtitle_size).';';
					}
					
					if($subtitle_height != "") {
						$subtitle_css .= 'line-height: '.esc_attr($subtitle_height).';';
					}
					$subtitle_css .=$subtitle_font_family;
					if($subtitle_space != "") {
						$subtitle_css .= 'letter-spacing: '.esc_attr($subtitle_space).';';
					}
				$subtitle_css .= '"';
				
				 $subtitle = '<p class="info-subtitle" '.$subtitle_css.'> '.esc_html($info_box_sub).' </p>';
			}	
			
					
			   $info_link = ( '||' === $info_link ) ? '' : $info_link;
				$info_link= vc_build_link( $info_link);

				$a_href = $info_link['url'];
				$a_title = $info_link['title'];
				$a_target = $info_link['target'];
				$a_rel = $info_link['rel'];
				if ( ! empty( $a_rel ) ) {
					$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
				}
				
			if(!empty($info_box_button)){
		$btn_css = ' style="';
		if($btn_bg != "") {
			$btn_css .= 'background-color: '.esc_attr($btn_bg).';';
		}
		if($btn_clr != "") {
			$btn_css .='color:'.esc_attr($btn_clr).';';
		}	
		if($btn_size != "") {
			$btn_css .='font-size: '.esc_attr($btn_size).';';
		}
		if($btn_weight != "") {
			$btn_css .='font-weight: '.esc_attr($btn_weight).';';
		}
		if($btn_space != "") {
			$btn_css .='letter-spacing: '.esc_attr($btn_space).';';
		}
		
		$btn_css .= '"'; 
		
		$button ='<div class="creative-info-button"  title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' '.$btn_css.'>'.esc_html($info_box_button).'</div >';
	}
	
	$svg_css='';
		if(!empty($svg_bg)) {
				$svg_css .= 'fill: '.esc_attr($svg_bg).';';
		}
				
				$uid=uniqid('info_box');
			
				$output ='<div id="pt-plus-creative-info-box" class="pt-plus-creative-info-box  info_box '.esc_attr($info_box_style).' clearfix '.esc_attr($animated_class).' '.esc_attr($uid).' '.esc_attr($el_class).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'"  data-id="'.esc_attr($uid).'" >';
				if($info_box_style=='pt-plus-info-box-1'){
					$output .='<a href="'.esc_url($a_href).'" class="creative-link" data-path-hover="m 0,0 0,47.7775 c 24.580441,3.12569 55.897012,-8.199417 90,-8.199417 34.10299,0 65.41956,11.325107 90,8.199417 L 180,0 z">';
				}
				if($info_box_style=='pt-plus-info-box-2'){
					$output .='<a href="'.esc_url($a_href).'" class="creative-link" data-path-hover="M 0,0 0,38 90,58 180.5,38 180,0 z">';
				}
				if($info_box_style=='pt-plus-info-box-3'){
					$output .='<a href="'.esc_url($a_href).'" class="creative-link" data-path-hover="m 180,34.57627 -180,0 L 0,0 180,0 z">';
				}
				$output .='<figure class="creative-info-figure">';
					$output .='<img src="'.esc_url($imgSrc).'" alt="" />';
					if($info_box_style=='pt-plus-info-box-1'){
						$output .='<svg viewBox="0 0 180 320" preserveAspectRatio="none"><path d="m 0,0 0,171.14385 c 24.580441,15.47138 55.897012,24.75772 90,24.75772 34.10299,0 65.41956,-9.28634 90,-24.75772 L 180,0 0,0 z"/></svg>';
					}
					if($info_box_style=='pt-plus-info-box-2'){
						$output .='<svg viewBox="0 0 180 320" preserveAspectRatio="none"><path d="M 0 0 L 0 182 L 90 126.5 L 180 182 L 180 0 L 0 0 z "/></svg>';
					}
					if($info_box_style=='pt-plus-info-box-3'){
						$output .='<svg viewBox="0 0 180 320" preserveAspectRatio="none"><path d="M 180,160 0,218 0,0 180,0 z"/></svg> ';
					}		

					$output .='<figcaption class="creative-info-figca">';
						$output .=	$title;
						$output .= $subtitle;
						$output .=$button;
					$output .='</figcaption>';
				$output .='</figure>';

					$output .= '</a>';
				$output .= '</div>';	
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .= '.'.esc_js($uid).' svg *,.'.esc_js($uid).' svg path{'.esc_js($svg_css).'}';
			$css_rule .= '</style>';				
		   return $css_rule.$output;
		}
		function init_tp_creative_info_box(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Creative Info Box", "pt_theplus"),
					"base" => "tp_creative_info_box",
					"icon" => "tp-creative-info-box",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Modern Info box Styles', 'pt_theplus'),
					"params" => array(
						
						array(
							"type" => "dropdown",
							'heading' =>  esc_html__('Style', 'pt_theplus'), 
							"param_name" => "info_box_style",
							"description" => "",
							"value" => array(
								'Style 1' => 'style-1',
								'Style 2' => 'style-2',
								'Style 3' => 'style-3'
							),
							'std' =>'style-1',
							"admin_label" => true,
						),
						array(
							"type" => "attach_image",
							 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Creative Info Box  using this option. .jpg, .png, .gif images supported.','pt_theplus').'</span></span>'.esc_html__('Select Image', 'pt_theplus')),
							"param_name" => "info_box_img",
							"description" => ""
						),
						array(
							"type" => "textfield",
							"admin_label" => true,
							'heading' =>  esc_html__('Title', 'pt_theplus'),
							"param_name" => "info_box_title",
							"value" => 'The Plus',
							"edit_field_class" => 'vc_col-sm-6',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add sub title of creative info box using this option.','pt_theplus').'</span></span>'.esc_html__('Sub Title', 'pt_theplus')),
							"param_name" => "info_box_sub",
							"value" => 'The Plus Info',			
							"edit_field_class" => 'vc_col-sm-6',
							"description" => ""
						),
						array(
							"type" => "vc_link",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Button URL, Link Open Option and Follow-No Follow Option from this option.','pt_theplus').'</span></span>'.esc_html__('Button URL ', 'pt_theplus')),
							"param_name" => "info_link",
							"value" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can write title of button from here.','pt_theplus').'</span></span>'.esc_html__('Button Title', 'pt_theplus')),
							"param_name" => "info_box_button",
							"value" => '',			
							"edit_field_class" => 'vc_col-sm-6',
							"description" => ""
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Header Backgroung Options', 'pt_theplus'),
							'param_name' => 'header_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'Style'
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for Background header using this option.','pt_theplus').'</span></span>'.esc_html__('Color ', 'pt_theplus')),
							"param_name" => "svg_bg",
							"value" => '#ffffff',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Title Options', 'pt_theplus'),
							'param_name' => 'title_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'Style'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "title_size",
							"value" => '20px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height ', 'pt_theplus')),
							"param_name" => "title_height",
							"value" => '1.4',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "title_space",
							"value" => '1px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "title_clr",
							"value" => '#ff004b',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
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
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Sub Title Options', 'pt_theplus'),
							'param_name' => 'subtitle_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'Style'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "subtitle_size",
							"value" => '16px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "subtitle_height",
							"value" => '1.4',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "subtitle_space",
							"value" => '1px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "subtitle_clr",
							"value" => '#d84a8c',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
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
							'text' => esc_html__('Buttn Options', 'pt_theplus'),
							'param_name' => 'subtitle_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'Style'
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "btn_size",
							"value" => '16px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						 array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "btn_space",
							"value" => '1px',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Font Weight using this Option. E.g. 400, 700, etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							"param_name" => "btn_weight",
							"value" => '400',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							"param_name" => "btn_bg",
							"value" => '#ffadc7',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "btn_clr",
							"value" => '#121212',
							"description" => '',
							"edit_field_class" => 'vc_col-sm-6',
							"group" => 'Style'
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
							"edit_field_class" => 'vc_col-sm-6',
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
							"heading" => __("Animated Delay", 'pt_theplus'),
							"param_name" => "animation_delay",
							"edit_field_class" => 'vc_col-sm-6',
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
							"edit_field_class" => 'vc_col-sm-6',
							"value" => '',
							"description" => ""
						)
						
					)
				));
			}
		}
	}
	new ThePlus_creative_info_box;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_creative_info_box'))
	{
		class WPBakeryShortCode_tp_creative_info_box extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}


