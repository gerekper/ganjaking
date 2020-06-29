<?php
	// Accordion Elements
	VcShortcodeAutoloader::getInstance()->includeClass('WPBakeryShortCode_VC_Tta_Accordion');
if(!class_exists("ThePlus_tp_accordion")){
	class ThePlus_tp_accordion extends WPBakeryShortCode_VC_Tta_Accordion{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_accordion') );
			add_shortcode( 'tp_accordion',array($this,'tp_accordion_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_accordion_scripts' ), 1 );
		}
		function tp_accordion_scripts() {
			wp_register_style( 'theplus-tabs-tours', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-tabs-tours.css', false, '1.0.0' );
		}
		function tp_accordion_shortcode($atts,$content = null){
			
				$el_class = $css = $css_animation =$tab_justify=$tab_full_class= '';

				$atts = vc_map_get_attributes( 'tp_accordion', $atts );

				$this->resetVariables( $atts, $content );
				extract( $atts );
				$this->setGlobalTtaInfo();

	
				$this->enqueueTtaStyles();
				$this->enqueueTtaScript();

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
				wp_enqueue_style( 'theplus-tabs-tours');
				$id = uniqid();
				// It is required to be before tabs-list-top/left/bottom/right for tabs/tours
				$prepareContent = $this->getTemplateVariable( 'content' );

				$class_to_filter = $this->getTtaGeneralClasses();
				$class_to_filter .= ' '.$styles;
				$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
				if($styles!='style-3' && $tab_justify=='true'){
					$tab_full_class='tabs_full_justify';
				}
				$data_attr = '';

				$data_attr .=' data-layout="accordion" ';
				if ($this->layout =="accordion") {
					$uid=uniqid('tstp_accordion');
								
				if(!empty($active_border_display) && $active_border_display=='on' && !empty($accor_act_border_color) ) {
					$accor_act_border_color = $accor_act_border_color;
				}else{
					$accor_act_border_color =  "transparent" ;
				}
				if(!empty($deactive_border_display) && $deactive_border_display=='on' && !empty($accor_deac_border_color) ) {
					$accor_deac_border_color = $accor_deac_border_color;
				}else{
					$accor_deac_border_color =  "transparent";
				}
				$title_style='';
				if($title_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $title_google_fonts );
					$title_style = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($title_use_theme_fonts=='custom-font-family'){
					$title_style='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
				}else{
					$title_style='';
				}
				
				$data_attr .=' data-select_act_bg_option="'.esc_attr($select_act_bg_option).'" ';
				if(!empty($select_act_bg_option) && $select_act_bg_option=='gradient'){
					$accor_act_gradient_color = pt_plus_gradient_color($act_gradient_color1,$act_gradient_color2,$act_gradient_style);
					
				}
			
				$data_attr .=' data-select_deac_bg_option="'.esc_attr($select_deac_bg_option).'" ';
				if(!empty($select_deac_bg_option) && $select_deac_bg_option=='gradient'){
					$accor_deact_gradient_color = pt_plus_gradient_color($deac_gradient_color1,$deac_gradient_color2,$deac_gradient_style);
					
				}
				
				}
				$output = '<div class="pt_plus_tabs_block '.esc_attr($tab_full_class).' '.esc_attr($animated_class).' '.esc_attr($uid).'" id="tabid_' . esc_attr($id) . '" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" data-uid="'.esc_attr($uid).'" >';
				$output .= '<div ' . $this->getWrapperAttributes() . '>';
				$output .= $this->getTemplateVariable( 'title' );
				$output .= '<div class="' . esc_attr( $css_class ) . '">';
				$output .= $this->getTemplateVariable( 'tabs-list-top' );
				$output .= $this->getTemplateVariable( 'tabs-list-left' );
				$output .= '<div class="vc_tta-panels-container" >';
				$output .= $this->getTemplateVariable( 'pagination-top' );
				$output .= '<div class="vc_tta-panels">';
				$output .= $prepareContent;
				$output .= '</div>';
				$output .= $this->getTemplateVariable( 'pagination-bottom' );
				$output .= '</div>';
				$output .= $this->getTemplateVariable( 'tabs-list-bottom' );
				$output .= $this->getTemplateVariable( 'tabs-list-right' );
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				
				
					if($select_act_bg_option=='normal'){
						$act_bgcolor='background: '.$accor_act_bg_color.';';
					}else{
						$act_bgcolor= $accor_act_gradient_color;
					}
					if($select_deac_bg_option=='normal'){
						$deac_bgcolor='background: '.$tour_deac_bg_color.';';
					}else{
						$deac_bgcolor= $accor_deact_gradient_color;
					}
				
	
				$css_rule='';
				$css_rule .= '<style >';
					$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .'.esc_attr($styles).' .vc_tta-panels-container .vc_tta-panel.vc_active .vc_tta-panel-heading a{color: '.esc_js($accor_act_title_color).';}.'.esc_attr($uid).'.pt_plus_tabs_block .'.esc_attr($styles).' .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading a{color: '.esc_js($tour_deac_title_color).';}.'.esc_attr($uid).'.pt_plus_tabs_block .'.esc_attr($styles).' .vc_tta-panels-container .vc_tta-panel a .vc_tta-controls-icon{font-size: '.esc_js($accor_icon_size).';}.'.esc_attr($uid).'.pt_plus_tabs_block .'.esc_attr($styles).' .vc_tta-panels-container .vc_tta-panel a {font-size: '.esc_js($accor_title_size).' ;'.esc_js($title_style).'}';
					if($styles=='accordion-style-1'){
					$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel.vc_active .vc_tta-panel-heading{'.esc_js($act_bgcolor).'}.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading{'.esc_js($deac_bgcolor).'}.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1.vc_tta-o-no-fill .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-body{background: '.esc_js($accor_contetn_color).';}';
					if($accor_act_border_color!='transparent'){
						$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel.vc_active .vc_tta-panel-heading {border: 1px solid '.esc_js($accor_act_border_color).';}';
					}else{
						$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel.vc_active .vc_tta-panel-heading {border: 0px;}';
					}
					if($accor_deac_border_color!='transparent'){
						$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading {border: 1px solid '.esc_js($accor_deac_border_color).';}';
					}else{
						$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-1 .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading {border: 0px;}';
					}
					}
					if($styles=='accordion-style-2' || $styles=='accordion-style-3'){
					$css_rule .= '.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-2 .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading h4:after,.'.esc_attr($uid).'.pt_plus_tabs_block .accordion-style-3 .vc_tta-panels-container .vc_tta-panel .vc_tta-panel-heading h4:after{'.esc_js($act_bgcolor).'}';
					}
				$css_rule .= '</style>';
				
		
				return $css_rule.$output;
		}
		function init_tp_accordion(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					'name' => esc_html__('TP Accordion', 'pt_theplus'),
					'base' => 'tp_accordion',
					'icon' => 'tp-accordion',
					'is_container' => true,
					'show_settings_on_create' => true,
					'as_parent' => array(
						'only' => 'vc_tta_section'
					),
					"category" => esc_html__("The Plus", "pt_theplus"),
					'description' => esc_html__('Creative Collapsible Content Panels', 'pt_theplus'),
					'params' => array(
						array(
							'type' => 'dropdown',
							'param_name' => 'styles',
							'value' => array(
								'Style-1' => 'accordion-style-1',
								'Style-2' => 'accordion-style-2',
								'Style-3' => 'accordion-style-3'
							),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Accordion Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Styles', 'pt_theplus')),
							'description' => '',
							'std' => 'accordion-style-1'
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Accordion Font size Settings', 'pt_theplus'),
							'param_name' => 'tab_font_settings',
							"group" => esc_html__('Style', 'pt_theplus'),
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Accordion Title Size', 'pt_theplus')),
							'param_name' => 'accor_title_size',
							"value" => '16px',
							"group" => esc_html__('Style', 'pt_theplus'),
							"description" => '',
							"edit_field_class" => "vc_col-xs-4"
						),
						
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Accordion Icon Size', 'pt_theplus')),
							'param_name' => 'accor_icon_size',
							"value" => '14px',
							"group" => esc_html__('Style', 'pt_theplus'),
							"description" => '',
							"edit_field_class" => "vc_col-xs-4"
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
							'text' => esc_html__('Accordion Active Settings', 'pt_theplus'),
							'param_name' => 'active_settings',
							"group" => esc_html__('Style', 'pt_theplus'),
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion title using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Title Color', 'pt_theplus')),
							"param_name" => "accor_act_title_color",
							"value" => '#ff214f',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4"
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can style a tab in Border display or not.','pt_theplus').'</span></span>'.esc_html__('Border On/Off', 'pt_theplus')),
							'param_name' => 'active_border_display',
							'description' => '',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							
							"dependency" => array(
								"element" => "styles",
								"value" => array(
									'accordion-style-1'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							"group" => esc_html__('Style', 'pt_theplus'),
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion border using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Border Color', 'pt_theplus')),
							"param_name" => "accor_act_border_color",
							"value" => '#ff214f',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "active_border_display",
								"value" => array(
									'on'
								)
							),
						),
						array(
								"type" => "dropdown",
								"heading" => __("Select Background Option", "pt_theplus"),
								"param_name" => "select_act_bg_option",
								"value" => array(
									__("Normal color", "pt_theplus") => "normal",
									__("Gradient color", "pt_theplus") => "gradient",
								),
								"description" => "",
								"std" => 'normal',
								'group' => esc_attr__('Style', 'pt_theplus'),
								
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion Background using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Background Color', 'pt_theplus')),
							"param_name" => "accor_act_bg_color",
							"value" => '#fff',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4",
							'dependency' => array(
									'element' => 'select_act_bg_option',
									'value' => 'normal'
							),
						),
						array(
								'type' => 'colorpicker',
								'heading' => __('First Color', 'pt_theplus'),
								'param_name' => 'act_gradient_color1',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_act_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-4",
								"value" => '#1e73be'
								
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Second Color', 'pt_theplus'),
								'param_name' => 'act_gradient_color2',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_act_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-4",
								"value" => '#2fcbce'
								
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
								'param_name' => 'act_gradient_style',
								'value' => array(
									__('Horizontal', 'pt_theplus') => 'horizontal',
									__('Vertical', 'pt_theplus') => 'vertical',
									__('Diagonal', 'pt_theplus') => 'diagonal',
									__('Radial', 'pt_theplus') => 'radial'
								),
								'std' => 'horizontal',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
									'element' => 'select_act_bg_option',
									'value' => 'gradient'
								)
							),
						array(
							'type' => 'checkbox',
							'param_name' => 'no_fill',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By checking this option you can Make background transparent of accordion navigator bar.','pt_theplus').'</span></span>'.esc_html__('Transparent Navigator Bar?', 'pt_theplus')),
							'value' => array(
								'Transparent Navigator Bar?' => 'true'
							)
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion Content Background using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Content Background Color', 'pt_theplus')),
							"param_name" => "accor_contetn_color",
							"value" => '#ff214f',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4",
							'dependency' => array(
								'element' => 'no_fill',
								'value' => array(
									'true'
								)
							)
						),
						
						
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Accordion Deactive Settings', 'pt_theplus'),
							'param_name' => 'deactive_settings',
							"group" => esc_html__('Style', 'pt_theplus'),
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion title using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Title Color', 'pt_theplus')),
							"param_name" => "tour_deac_title_color",
							"value" => '#4d4d4d',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4"
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can style a deactivated tab in Border display or not.','pt_theplus').'</span></span>'.esc_html__('Border On/Off', 'pt_theplus')),
							'param_name' => 'deactive_border_display',
							'description' => '',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"dependency" => array(
								"element" => "styles",
								"value" => array(
									'accordion-style-1',
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							"group" => esc_html__('Style', 'pt_theplus'),
						),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion border using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Border Color', 'pt_theplus')),
							"param_name" => "accor_deac_border_color",
							"value" => '#4d4d4d',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "deactive_border_display",
								"value" => array(
									'on'
								)
							)
						),
						array(
								"type" => "dropdown",
								"heading" => __("Select Background Option", "pt_theplus"),
								"param_name" => "select_deac_bg_option",
								"value" => array(
									__("Normal color", "pt_theplus") => "normal",
									__("Gradient color", "pt_theplus") => "gradient",
								),
								"description" => "",
								"std" => 'normal',
								'group' => esc_attr__('Style', 'pt_theplus'),		
								"dependency" => array(
									"element" => "styles",
									"value" => array(
										'accordion-style-1',
									)
								),
							),
						array(
							"type" => "colorpicker",
							"group" => esc_html__('Style', 'pt_theplus'),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active Accordion background using this option.','pt_theplus').'</span></span>'.esc_html__('Accordion Background Color', 'pt_theplus')),
							"param_name" => "tour_deac_bg_color",
							"value" => '#ff214f',
							"description" => '',
							"edit_field_class" => "vc_col-xs-4",
							'dependency' => array(
									'element' => 'select_deac_bg_option',
									'value' => 'normal'
								),
						),
						array(
								'type' => 'colorpicker',
								'heading' => __('First Color', 'pt_theplus'),
								'param_name' => 'deac_gradient_color1',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_deac_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-4",
								"value" => '#1e73be'
								
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Second Color', 'pt_theplus'),
								'param_name' => 'deac_gradient_color2',
								'group' => esc_attr__('Style', 'pt_theplus'),
								'dependency' => array(
									'element' => 'select_deac_bg_option',
									'value' => 'gradient'
								),
								"edit_field_class" => "vc_col-xs-4",
								"value" => '#2fcbce'
								
							),
							array(
								'type' => 'dropdown',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
								'param_name' => 'deac_gradient_style',
								'value' => array(
									__('Horizontal', 'pt_theplus') => 'horizontal',
									__('Vertical', 'pt_theplus') => 'vertical',
									__('Diagonal', 'pt_theplus') => 'diagonal',
									__('Radial', 'pt_theplus') => 'radial'
								),
								'std' => 'horizontal',
								"description" => "",
								'group' => esc_attr__('Style', 'pt_theplus'),
								"edit_field_class" => "vc_col-xs-4",
								'dependency' => array(
									'element' => 'select_deac_bg_option',
									'value' => 'gradient'
								)
							),
						
						array(
							'type' => 'dropdown',
							'param_name' => 'spacing',
							'value' => array(
								__('None', 'pt_theplus') => '',
								'1px' => '1',
								'2px' => '2',
								'3px' => '3',
								'4px' => '4',
								'5px' => '5',
								'10px' => '10',
								'15px' => '15',
								'20px' => '20',
								'25px' => '25',
								'30px' => '30',
								'35px' => '35'
							),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can enter value to setup a gap in between two Accordion navigators.','pt_theplus').'</span></span>'.esc_html__('Accordion Navigator Spacing', 'pt_theplus')),
							'description' => '',
						),
						array(
							'type' => 'dropdown',
							'param_name' => 'c_align',
							'value' => array(
								__('Left', 'pt_theplus') => 'left',
								__('Right', 'pt_theplus') => 'right',
								__('Center', 'pt_theplus') => 'center'
							),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose Accordion Navigator position using these options.','pt_theplus').'</span></span>'.esc_html__('Accordion Navigator Position', 'pt_theplus')),
							'description' => '',
						),
						array(
							'type' => 'dropdown',
							'param_name' => 'autoplay',
							'value' => array(
								__('None', 'pt_theplus') => 'none',
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
								'5' => '5',
								'10' => '10',
								'20' => '20',
								'30' => '30',
								'40' => '40',
								'50' => '50',
								'60' => '60'
							),
							'std' => 'none',
						   'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can make tour accordions by selecting value in this option.(Note:Select auto rotate for tabs in seconds disabled by default).','pt_theplus').'</span></span>'.esc_html__('Autoplay', 'pt_theplus')),
						),
						array(
							'type' => 'checkbox',
							'param_name' => 'collapsible_all',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By Checking this option up you can collapse all accordions by not having any active accordion.','pt_theplus').'</span></span>'.esc_html__('Collapse All', 'pt_theplus')),
							'description' => '',
						),
						// Control Icons
						array(
							'type' => 'dropdown',
							'param_name' => 'c_icon',
							'value' => array(
								__('None', 'pt_theplus') => '',
								__('Chevron', 'pt_theplus') => 'chevron',
								__('Plus', 'pt_theplus') => 'plus',
								__('Triangle', 'pt_theplus') => 'triangle',
								__('Plus Round', 'pt_theplus') => 'plusround'
							),
							'std' => 'plus',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select icon for accordion by using these options.','pt_theplus').'</span></span>'.esc_html__('Accordion Icon', 'pt_theplus')),
							'description' => '',
						),
						array(
							'type' => 'dropdown',
							'param_name' => 'c_position',
							'value' => array(
								__('Left', 'pt_theplus') => 'left',
								__('Right', 'pt_theplus') => 'right'
							),
							'dependency' => array(
								'element' => 'c_icon',
								'not_empty' => true
							),
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Position for Accordion Icon Position by using these options.','pt_theplus').'</span></span>'.esc_html__('Accordion Icon Position', 'pt_theplus')),
							'description' => '',
						),
						// Control Icons END
						array(
							'type' => 'textfield',
							'param_name' => 'active_section',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter number of Accordion which you want to show as active initially.(Note: to have all sections closed on initial load enter non-existing number) e.g. 1,2,3 etc.','pt_theplus').'</span></span>'.esc_html__('Active Accordion', 'pt_theplus')),
							'value' => 1,
							'description' => '',
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
							"param_name" => "animation_effects",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
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
						array(
							'type' => 'css_editor',
							'heading' => __('CSS box', 'pt_theplus'),
							'param_name' => 'css',
							'group' => esc_html__('Design Options', 'pt_theplus')
						)
					),
					'js_view' => 'VcBackendTtaAccordionView',
					'custom_markup' => '
					<div class="vc_tta-container" data-vc-action="collapseAll">
					<div class="vc_general vc_tta vc_tta-accordion vc_tta-color-backend-accordion-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-o-shape-group vc_tta-controls-align-left vc_tta-gap-2">
					<div class="vc_tta-panels vc_clearfix {{container-class}}">
					{{ content }}
					<div class="vc_tta-panel vc_tta-section-append">
					<div class="vc_tta-panel-heading">
					<h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left">
					<a href="javascript:;" aria-expanded="false" class="vc_tta-backend-add-control">
					<span class="vc_tta-title-text">' . esc_html__('Add Section', 'pt_theplus') . '</span>
					<i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
					</a>
					</h4>
					</div>
					</div>
					</div>
					</div>
					</div>',
					'default_content' => '[vc_tta_section title="' . sprintf('%s %d', esc_html__('Section', 'pt_theplus'), 1) . '"][/vc_tta_section][vc_tta_section title="' . sprintf('%s %d', esc_html__('Section', 'pt_theplus'), 2) . '"][/vc_tta_section]'
				));
			}
		}
	}
	new ThePlus_tp_accordion;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_accordion'))
	{
		class WPBakeryShortCode_tp_accordion extends WPBakeryShortCode_VC_Tta_accordion
		{
			
		}
	}
}
