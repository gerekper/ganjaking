<?php
// Advanced Text Block Elements
if(!class_exists("ThePlus_adv_text_block")){
	class ThePlus_adv_text_block{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_adv_text_block') );
			add_shortcode( 'tp_adv_text_block',array($this,'tp_adv_text_block_shortcode'));
		}
		function tp_adv_text_block_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'desktop_text_color' =>'#888',
					'desktop_text_size' =>'14px',
					'desktop_text_line' =>'30px',
					'desktop_text_letter_space' =>'',
					'desktop_text_weight' =>'400',
					
					'content_use_theme_fonts'=>'custom-font-family',
					'content_font_family'=>'',
					'content_font_weight'=>'400',
					'content_google_fonts'=>'',
					
					'laptop_text_size' =>'',
					'laptop_text_line' =>'',
					'laptop_text_letter_space' =>'',
					
					'tablet_text_size' =>'14px',
					'tablet_text_line' =>'30px',
					'tablet_text_letter_space' =>'',
					
					'mobile_text_size' =>'14px',
					'mobile_text_line' =>'30px',
					'mobile_text_letter_space' =>'',
					
					'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					
					
					'magic_scroll' => 'off',
					'scroll_type'	=> 'position',
					'distance_scroll_x' => '0',
					'distance_scroll_y' => '50',
					'scale_scroll'=> '1',
					
					'tablet_hide' => 'off',					
					'desktop_hide' =>'off',
					'mobile_hide' => 'off',
					
					'el_class' =>'',
					'css_box'	=>'',
					), $atts ) );
					
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
					$content_style='';
				if($content_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $content_google_fonts );
					$content_style = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($content_use_theme_fonts=='custom-font-family'){
					if($content_font_family!=''){
						$content_style ='font-family:'.$content_font_family.';';
					}
					$content_style .='font-weight:'.$content_font_weight.';';
				}else{
					$content_style='';
				}
					
					$uid=uniqid('adv_text_block');
					
					
					$description=$magic_class=$parallax_scroll='';
					if($content !=''){
						$content = wpb_js_remove_wpautop($content, true);
						$description .=$content;
					}
					
					$css_class = vc_shortcode_custom_css_class( $css_box, ' ' );
					
					$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_class, ' ' ), "tp_adv_text_block", $atts );
					$magic_class = $magic_attr = $parallax_scroll = '';
						if (!empty($magic_scroll) && $magic_scroll == 'on') {
							$magic_attr .= ' data-scroll_type="' . esc_attr($scroll_type) . '" ';
							if(!empty($scroll_type) && $scroll_type== 'position' ){
								$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
								$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
								$parallax_scroll .= ' parallax-scroll ';
							}
							if(!empty($scroll_type) && $scroll_type== 'scale'){
								$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
								$parallax_scroll .= ' scale-scroll ';
							}
							if(!empty($scroll_type) && $scroll_type== 'both'){
								$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
								$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
								$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
								$parallax_scroll .= ' both-scroll ';
							}
							$magic_class .= ' magic-scroll ';
						}
						
					$text_block ='<div class="pt-plus-text-block-wrapper '.esc_attr($magic_class).' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'">';
						$text_block .='<div class="text_block_parallax '.esc_attr($parallax_scroll).'" '.$magic_attr.'>';
							$text_block .='<div class="pt_plus_adv_text_block  '.esc_attr($el_class).' '.esc_attr($uid).'  '.esc_attr($animated_class).' ' . esc_attr( $css_class ) . '" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
								$text_block .= '<div class="text-content-block">';
									$text_block .= $description;
								$text_block .= '</div>';
							$text_block .='</div>';
						$text_block .='</div>';
					$text_block .='</div>';
					$css_rule='';
					$css_rule='<style>';
					$css_rule .='.'.esc_js($uid).' .text-content-block,.'.esc_js($uid).' .text-content-block p{color:'.esc_js($desktop_text_color).';';
					if($desktop_text_size){
					    $css_rule .='font-size:'.esc_js($desktop_text_size).';';
					}
					if($desktop_text_line){
					$css_rule .='line-height:'.esc_js($desktop_text_line).';';
					}
					if($desktop_text_letter_space){
					$css_rule .='letter-spacing:'.esc_js($desktop_text_letter_space).';';
					}
					$css_rule .=$content_style;
		            $css_rule .='}';
					$css_rule .='@media (max-width:1200px){.'.esc_js($uid).' .text-content-block,.'.esc_js($uid).' .text-content-block p{';
					if($laptop_text_size){
					$css_rule .='font-size:'.esc_js($laptop_text_size).' !important;';
					}
					if($laptop_text_line){
					$css_rule .='line-height:'.esc_js($laptop_text_line).' !important;';
					}
					if($laptop_text_letter_space){
					$css_rule .='letter-spacing:'.esc_js($laptop_text_letter_space).';';
					}
		        	$css_rule .='}}';
					$css_rule .='@media (max-width:991px){.'.esc_js($uid).' .text-content-block,.'.esc_js($uid).' .text-content-block p{';
					if($tablet_text_size){
					$css_rule .='font-size:'.esc_js($tablet_text_size).' !important;';
					}
					if($tablet_text_line){
					$css_rule .='line-height:'.esc_js($tablet_text_line).' !important;';
					}
					if($tablet_text_letter_space){
					$css_rule .='letter-spacing:'.esc_js($tablet_text_letter_space).';';
					}
		            $css_rule .='}}';
					$css_rule .='@media (max-width:600px){.'.esc_js($uid).' .text-content-block,.'.esc_js($uid).' .text-content-block p{';
					if($mobile_text_size){
					$css_rule .='font-size:'.esc_js($mobile_text_size).' !important;';
					}
					if($mobile_text_line){
					$css_rule .='line-height:'.esc_js($mobile_text_line).' !important;';
					}
					if($mobile_text_letter_space){
					$css_rule .='letter-spacing:'.esc_js($mobile_text_letter_space).';';
					}
		            $css_rule .='}}';
					$css_rule .='</style>';
					return $css_rule.$text_block;
		}
		function init_tp_adv_text_block(){
			if(function_exists("vc_map"))
			{
					vc_map(array(
						"name" => esc_html__("Advance Text Block", "pt_theplus"),
						"base" => "tp_adv_text_block",
						"icon" => "tp-adv-text-block",
						"category" => esc_html__("The Plus", "pt_theplus"),
						'description' => esc_html__('Modern WYSIWYG Editor', 'pt_theplus'),
						"params" => array(
							array(
								"type" => "textarea_html",
								"heading" => esc_html__("Content", "pt_theplus"),
								"param_name" => "content",
								"admin_label" => true,
								"value" => __("<p>I am test text block. Click edit button to change this text.</p>", "pt_theplus"),
								"description" => ""
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Desktop Content Style', 'pt_theplus'),
								'param_name' => 'content_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_html__('Style', 'pt_theplus'),
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "desktop_text_size",
								"description" => "",
								"value" => __("14px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "desktop_text_line",
								"description" => "",
								"value" => __("30px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "desktop_text_letter_space",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
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
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
								'param_name' => 'content_font_family',
								'value' => "",
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Style', 'pt_theplus'),	
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
								'group' => esc_attr__('Style', 'pt_theplus'),	
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
									'group' => esc_attr__('Style', 'pt_theplus'),	
							),
						   
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "desktop_text_color",
								"description" => "",
								"value" => __("#888", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Laptop Content Style', 'pt_theplus'),
								'param_name' => 'laptop_content_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "laptop_text_size",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "laptop_text_line",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "laptop_text_letter_space",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Tablet Content Style', 'pt_theplus'),
								'param_name' => 'tablet_content_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "tablet_text_size",
								"description" => "",
								"value" => __("14px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "tablet_text_line",
								"description" => "",
								"value" => __("30px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "tablet_text_letter_space",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Mobile Content Style', 'pt_theplus'),
								'param_name' => 'mobile_content_style',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								"group" => esc_attr__('Style', 'pt_theplus')
							),
							array(
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"type" => "textfield",
								"param_name" => "mobile_text_size",
								"description" => "",
								"value" => __("14px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "mobile_text_line",
								"description" => "",
								"value" => __("30px", "pt_theplus"),
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing ', 'pt_theplus')),
								"param_name" => "mobile_text_letter_space",
								"description" => "",
								"value" => "",
								"group" => esc_html__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4"
							),
							array(
								'type' => 'pt_theplus_checkbox',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')), 
								'param_name' => 'magic_scroll',
								'description' => '',
								'value' => 'off',
								'options' => array(
									'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No'
									)
								),
								"edit_field_class" => "vc_col-xs-12"
							),
							array(
										'type' => 'dropdown',
										"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')), 
										'param_name' => 'scroll_type',
										'value' => array(
											__('Position', 'pt_theplus') => 'position',
											__('Scale', 'pt_theplus') => 'scale',
											__('Position and Scale', 'pt_theplus') => 'both',
										),
										'description' => '',
										'edit_field_class' => 'vc_col-xs-12',
										'std' => 'position',
										'dependency' => array(
											'element' => 'magic_scroll',
											'value' => array(
												'on'
											)
										)
							 ),
							array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
								'param_name' => 'distance_scroll_x',
								'value' => '0',
								'description' => '',
								
								'edit_field_class' => 'vc_col-xs-6',
								'dependency' => array(
									'element' => 'scroll_type',
									'value' => array(
										'position','both'
									)
								)
							),
							array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Distance', 'pt_theplus')),
								'param_name' => 'distance_scroll_y',
								'value' => '50',
								'description' => '',
								
								'edit_field_class' => 'vc_col-xs-6',
								'dependency' => array(
									'element' => 'scroll_type',
									'value' => array(
										'position','both'
									)
								)
							),
							 array(
								'type' => 'textfield',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of scale of section. e.g. 2 = 200%, 1.5 = 150%','pt_theplus').'</span></span>'.esc_html__('Scale Value', 'pt_theplus')),
								'param_name' => 'scale_scroll',
								'value' => '1',
								'description' => '',
								
								'edit_field_class' => 'vc_col-xs-6',
								'dependency' => array(
									'element' => 'scroll_type',
									'value' => array(
										'scale','both'
									)
								)
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
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
								"param_name" => "animation_delay",
								"value" => '50',
								'edit_field_class' => 'vc_col-sm-6',
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
							array(
								'type' => 'css_editor',
								'heading' => __('CSS box', 'pt_theplus'),
								'param_name' => 'css_box',
								'group' => __('Design Options', 'pt_theplus')
							)
						)
					));
			}
		}
	}
	new ThePlus_adv_text_block;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_adv_text_block'))
	{
		class WPBakeryShortCode_tp_adv_text_block extends WPBakeryShortCode {
			  protected function contentInline($atts, $content = null)
				{
					
				}
		}
	}
}	