<?php 
// Contact Form 7 Elements
if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
if(!class_exists("ThePlus_contact_form")){
	class ThePlus_contact_form{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_contact_form') );
			add_shortcode( 'tp_contact_form',array($this,'tp_contact_form_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_contact_form_scripts' ), 1 );
		}
		function tp_contact_form_scripts() {
			wp_register_style('theplus-cf7-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-cf7-style.css', false, '1.0.0' );
		}
		function tp_contact_form_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'form_id'=>'',
					'form_style'=>'style-1',
					'form_color'=>'#A8A8A8',
					'value_color'=>'#252525',
					'label_color'=>'#313131',
					'active_label_color'=>'#252525',
					'btn_use_theme_fonts'=>'custom-font-family',
					'btn_font_family'=>'',
					'btn_font_weight'=>'400',
					'btn_google_fonts'=>'',
					
					'letter_spacing'=>'1px',
					'button_padding'=> '18px',	
					'btn_text_color'=>'#fff',
					'btn_text_hvr_color' =>'#313131',
					'btn_bg_color'=>'#252525',
					'btn_bg_hvr_color' =>'#ffffff',
					'btn_border_color'=>'#252525',
					'btn_border_hvr_color' =>'#252525',
					
					'success_text_color'=>'#313131',
					'success_border_color'=>'#398f14',
					'validate_text_color'=>'#313131',
					'validate_border_color'=>'#f7e700',
					
					'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					
					'el_class' =>'',
					), $atts ) );
					wp_enqueue_style('theplus-cf7-style');
					$uid=uniqid("the_cf7");

				if($btn_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $btn_google_fonts );
					$fonts_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($btn_use_theme_fonts=='custom-font-family'){
					$fonts_family='font-family:'.$btn_font_family.';font-weight:'.$btn_font_weight.';';
				}else{
					$fonts_family='';
				}	
					
					$attr_class=$attr_data='';
					$attr_class .=$uid;
					$attr_class .=' cf7-'.esc_attr($form_style).' ';
					
					$attr_data .=' data-id="'.esc_attr($uid).'" ';
					$attr_data .=' data-style-radio-checkbox="radio-checkbox-1" ';
					$attr_data .=' data-style="'.esc_attr($form_style).'" ';
					
					
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
					
					$form_code= "[contact-form-7 id='$form_id']";
						$cform ='<div class="pt_plus_cf7_styles '.esc_attr($el_class).' '.$attr_class.' '.esc_attr($animated_class).'" '.$attr_data.' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
							$cform .=do_shortcode($form_code);
						$cform .='</div>';
					$css_rule='';
					$css_rule .= '<style >';
					$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' .input__field--'.esc_js($form_style).',.pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="date"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="email"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="number"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="password"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="search"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="tel"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="text"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' input[type="url"]:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' select:focus, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' textarea:focus{color: '.esc_js($value_color).' !important;}.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-file + .input__file_btn{color: '.esc_js($form_color).';border-color:'.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' .input__label-content--'.esc_js($form_style).',.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn{color: '.esc_js($label_color).' !important;}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-'.esc_js($form_style).' .input--filled .input__label-content--'.esc_js($form_style).',.cf7-'.esc_js($form_style).' .input__field--'.esc_js($form_style).':focus + .input__label--'.esc_js($form_style).' .input__label-content--'.esc_js($form_style).',.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 .input__label-content--style-4::after{color: '.esc_js($active_label_color).' !important;}.pt_plus_cf7_form .'.esc_js($uid).' label.input__radio_btn .toggle-button__icon,.pt_plus_cf7_form .'.esc_js($uid).' label.input__checkbox_btn .toggle-button__icon{background: '.esc_js($label_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .input__file_btn *{stroke: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-form-control-wrap,.pt_plus_cf7_form .'.esc_js($uid).' input.wpcf7-form-control.wpcf7-submit{letter-spacing:'.esc_js($letter_spacing).';'.esc_js($fonts_family).'}.pt_plus_cf7_form .'.esc_js($uid).' input.wpcf7-form-control.wpcf7-submit{padding:'.esc_js($button_padding).';color:'.esc_js($btn_text_color).';background: '.esc_js($btn_bg_color).';border-color:'.esc_js($btn_border_color).';}.pt_plus_cf7_form .'.esc_js($uid).' input.wpcf7-form-control.wpcf7-submit:hover{color:'.esc_js($btn_text_hvr_color).';background:'.esc_js($btn_bg_hvr_color).';border-color:'.esc_js($btn_border_hvr_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .cf7-style-file.input--filled .input__file_btn{background:'.esc_js($form_color).';color:'.esc_js($label_color).';border-color:'.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .cf7-style-file.input--filled .input__file_btn *{stroke:'.esc_js($label_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-mail-sent-ok{color:'.esc_js($success_text_color).';border: 2px solid '.esc_js($success_border_color).';}.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-validation-errors,.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-spam-blocked,.pt_plus_cf7_form .'.esc_js($uid).' .wpcf7-mail-sent-ng{color:'.esc_js($validate_text_color).';border: 2px solid '.esc_js($validate_border_color).';}';
					if($form_style=='style-1'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-1 .input__label--style-1::after,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-1 .input__label--style-1::before,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}';
					}
					if($form_style=='style-2'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-2 span.wpcf7-form-control-wrap.input--style-2{color: '.esc_js($value_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-2 .input__label--style-2::before,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-2 .input__label--style-2::before{border-top: 4px solid '.esc_js($form_color).';}';
					}
					if($form_style=='style-3'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-3 .input__label--style-3::before,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-3 .graphic--style-3 *{stroke: '.esc_js($form_color).';}';
					}
					if($form_style=='style-4'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 span.wpcf7-form-control-wrap.input--style-4{color: '.esc_js($value_color).';} .pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 .input__field--style-4{border-color: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 .input__field--style-4,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.input__field--style-4:focus + .input__field--style-4,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 .input--filled .input__field--style-4,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-4 .input__field--style-4:focus{ background-color: transparent; border-color: '.esc_js($form_color).' !important;}';
					}
					if($form_style=='style-5'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-5 .input__label--style-5::before,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-5 .input__label--style-5::after,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}';
					}
					if($form_style=='style-6'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-6 .input__label--style-6::before,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-6 .input__label--style-6::after{border-color:'.esc_js($form_color).' !important;}';
					}
					if($form_style=='style-7'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-7 .input__label--style-7::after,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-7 .input__label--style-7::before{border-color:'.esc_js($form_color).';}';
					}
					if($form_style=='style-8'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input__field--style-8,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input__label--style-8::after{color:'.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input__field--style-8:focus,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input--filled .input__field--style-8{-moz-box-shadow:0px 0px 0px 2px '.esc_js($form_color).';-webkit-box-shadow:0px 0px 0px 2px '.esc_js($form_color).';box-shadow:0px 0px 0px 2px '.esc_js($form_color).';background: transparent;}';
					}
					if($form_style=='style-9'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-9 .input__label--style-9::before,.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-9 .input__label--style-9::after,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input__label--style-8::after{color:'.esc_js($form_color).';}';
					}
					if($form_style=='style-10'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-10 .input__label--style-10::before,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-10 .input__label--style-10::after{color:'.esc_js($form_color).';}';
					}
					if($form_style=='style-11'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-11 .input__label--style-11::after{color:'.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-11 .graphic--style-11 *{stroke: '.esc_js($form_color).';}';
					} 
					if($form_style=='style-12'){
						$css_rule .= '.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-12 .input__label--style-12,.pt_plus_cf7_form .'.esc_js($uid).' .input__checkbox_btn .toggle-button__icon:after,.pt_plus_cf7_form .'.esc_js($uid).' .input__radio_btn .toggle-button__icon:after{background: '.esc_js($form_color).';}.pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-8 .input__label--style-8::after{color:'.esc_js($form_color).';}.input__field--style-12:focus + .input__label--style-12::before, .pt_plus_cf7_form .'.esc_js($uid).'.cf7-style-12 .input--filled .input__label--style-12::before{border-color: '.esc_js($form_color).';}.input__field--style-12:focus + .cf7-style-12 .input__label--style-12,.cf7-style-12 .input--filled .input__label--style-12{background:transparent;}';
					}
					$css_rule .= '</style>';
					return $css_rule.$cform;
		}
		function init_tp_contact_form(){
			if(function_exists("vc_map"))
			{
require_once(THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/vc_arrays.php');
				vc_map(array(
					"name" => __("TP Contact Form7", "pt_theplus"),
					"base" => "tp_contact_form",
					"icon" => "tp-contact-form",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Stylish Contact form7', 'pt_theplus'),
					"params" => array(
					array(
					'type' => 'dropdown',
					"heading" =>  __('<span class="pt_theplus-vc-toolip 	tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You need to create forms in Contact Form 7 plugin to have them here to choose from. Here is the list of all created contact form 7 forms.','pt_theplus').'</span></span>'.esc_html__('Select Contact Form', 'pt_theplus')),
					'param_name' => 'form_id',
					'value' => $contact_forms,
					'save_always' => true,
					'std' => '',
					'description' => '',
					),
					array(
							'type'        => 'radio_select_image',
							"heading" =>  esc_html__('Form Style', 'pt_theplus'),
							'param_name'  => 'form_style',
							'admin_label' => true,
							'simple_mode' => false,
							'value'		=> 'style-1',
							'options'     => array(
								'style-1' => array(
									'tooltip' => esc_attr__('Style-1','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-1.jpg'
								),
								'style-2' => array(
									'tooltip' => esc_attr__('Style-2','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-2.jpg'
								),
								'style-3' => array(
									'tooltip' => esc_attr__('Style-3','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-3.jpg'
								),
								'style-4' => array(
									'tooltip' => esc_attr__('Style-4','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-4.jpg'
								),
								'style-5' => array(
									'tooltip' => esc_attr__('Style-5','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-5.jpg'
								),
								'style-6' => array(
									'tooltip' => esc_attr__('Style-6','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-6.jpg'
								),
								'style-7' => array(
									'tooltip' => esc_attr__('Style-7','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-7.jpg'
								),
								'style-8' => array(
									'tooltip' => esc_attr__('Style-8','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-8.jpg'
								),
								'style-9' => array(
									'tooltip' => esc_attr__('Style-9','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-9.jpg'
								),
								'style-10' => array(
									'tooltip' => esc_attr__('Style-10','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-10.jpg'
								),
								'style-11' => array(
									'tooltip' => esc_attr__('Style-11','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-11.jpg'
								),
								'style-12' => array(
									'tooltip' => esc_attr__('Style-12','pt_theplus'),
									'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/contact-form/ts-contact-form-style-10.jpg'
								),
							),
							
						),
					
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the main form color, Which will be used in main parts of Form.','pt_theplus').'</span></span>'.esc_html__('Form Main Color', 'pt_theplus')),
					'param_name' => 'form_color',
					'value' => '#A8A8A8',
					"edit_field_class" => "vc_col-xs-6",
					'description' => '',
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color, Which will be used in all Typographic parts of Form.','pt_theplus').'</span></span>'.esc_html__('Typography Color', 'pt_theplus')),
					'param_name' => 'value_color',
					'value' => '#252525',
					"edit_field_class" => "vc_col-xs-6",
					'description' => '',
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the Main Lable Normal Color.','pt_theplus').'</span></span>'.esc_html__('Label Normal Color', 'pt_theplus')),
					'param_name' => 'label_color',
					"edit_field_class" => "vc_col-xs-6",
					'value' => '#313131',
					'description' => '',
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the Label Active Color.','pt_theplus').'</span></span>'.esc_html__('Active Label Color', 'pt_theplus')),
					'param_name' => 'active_label_color',
					'value' => '#252525',
					"edit_field_class" => "vc_col-xs-6",
					'description' => '',
					),
					array(
					'type' => 'pt_theplus_heading_param',
					'text' => esc_html__('Typhography Settings', 'pt_theplus'),
					'param_name' => 'typhography_settings',
					'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
					),	
					array(
							'type' => 'dropdown',
							'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Form Custom font family', 'pt_theplus'),
							'param_name' => 'btn_use_theme_fonts',
							 "value" => array(
								esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
								esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
							),
							'std' =>  'custom-font-family',
					),
					array(
						'type' => 'textfield',
						'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
						'param_name' => 'btn_font_family',
						'value' => "",
						'edit_field_class' => 'vc_col-xs-6',
						'description' => '',
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
							
					),
					array(
					'type' => 'pt_theplus_heading_param',
					'text' => esc_html__('Success Message Styling', 'pt_theplus'),
					'param_name' => 'success_settings',
					'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
					),
					array(
						'type' => 'colorpicker',
						"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Color', 'pt_theplus')),
						'param_name' => 'success_text_color',
						'value' => '#313131',
						'description' => '',
						"edit_field_class" => "vc_col-xs-6"
					),
					array(
						'type' => 'colorpicker',
						"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s Hover Text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Hover Color', 'pt_theplus')),
						'param_name' => 'success_border_color',
						'value' => '#398f14',
						'description' => '',
						"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'pt_theplus_heading_param',
					'text' => esc_html__('Validation Message Styling', 'pt_theplus'),
					'param_name' => 'validation_settings',
					'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
					),
					array(
						'type' => 'colorpicker',
						"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Color', 'pt_theplus')),
						'param_name' => 'validate_text_color',
						'value' => '#313131',
						'description' => '',
						"edit_field_class" => "vc_col-xs-6"
					),
					array(
						'type' => 'colorpicker',
						"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s Hover Text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Hover Color', 'pt_theplus')),
						'param_name' => 'validate_border_color',
						'value' => '#f7e700',
						'description' => '',
						"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'pt_theplus_heading_param',
					'text' => esc_html__('Button Settings', 'pt_theplus'),
					'param_name' => 'Button_settings',
					'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
					),
					array(
						"type" => "textfield",
						'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Button Padding using this option. Ex. 10px 18px.','pt_theplus').'</span></span>'.esc_html__('Button Inner Padding', 'pt_theplus')), 
						"param_name" => "button_padding",
						'value' => __('18px', 'pt_theplus'),
						'description' => '',
						"edit_field_class" => "vc_col-xs-6",
					),
					array(
					'type' => 'textfield',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Letter Spacing of Social Media Title using this option.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
					'param_name' => 'letter_spacing',
					'value' => __('1px', 'pt_theplus'),
					'description' => '',
					"edit_field_class" => "vc_col-xs-6",
					),
					
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Color', 'pt_theplus')),
					'param_name' => 'btn_text_color',
					'value' => '#ffffff',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s Hover Text color.','pt_theplus').'</span></span>'.esc_html__('Button Text Hover Color', 'pt_theplus')),
					'param_name' => 'btn_text_hvr_color',
					'value' => '#313131',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s background color.','pt_theplus').'</span></span>'.esc_html__('Button Background Color ', 'pt_theplus')),
					'param_name' => 'btn_bg_color',
					'value' => '#252525',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s background hover color.','pt_theplus').'</span></span>'.esc_html__('Button Background Hover Color ', 'pt_theplus')),
					'param_name' => 'btn_bg_hvr_color',
					'value' => '#ffffff',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s border color.','pt_theplus').'</span></span>'.esc_html__('Button Border Color ', 'pt_theplus')),
					'param_name' => 'btn_border_color',
					'value' => '#252525',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
					),
					array(
					'type' => 'colorpicker',
					"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This is the color for button&#39;s Border hover color.','pt_theplus').'</span></span>'.esc_html__('Button Border Hover Color ', 'pt_theplus')),
					'param_name' => 'btn_border_hvr_color',
					'value' => '#252525',
					'description' => '',
					"edit_field_class" => "vc_col-xs-6"
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
					"edit_field_class" => "vc_col-xs-6",
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
					"edit_field_class" => "vc_col-xs-6",
					"value" => '50',
					"description" => ""
					),
					array(
					'type' => 'pt_theplus_heading_param',
					'text' => esc_html__('Extra Settings', 'pt_theplus'),
					'param_name' => 'extra_settings',
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
	new ThePlus_contact_form;
	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_contact_form'))
	{
		class WPBakeryShortCode_tp_contact_form extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}
}	