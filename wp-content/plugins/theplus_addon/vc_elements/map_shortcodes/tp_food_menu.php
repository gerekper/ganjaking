<?php
// Food Menu Elements
if(!class_exists("ThePlus_food_menu")){
	class ThePlus_food_menu{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_food_menu') );
			add_shortcode( 'tp_food_menu',array($this,'tp_food_menu_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_food_menu_scripts' ), 1 );
		}
		function tp_food_menu_scripts() {
			wp_register_style( 'theplus-food-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-food-menu-style.css', false, '1.0.0' );
		}
		function tp_food_menu_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'menu_style'    => 'style_1',
				
				'title'			 => 'Italian Pizza',
				'title_size'	=>'24px',
				'title_line'	=>'1.2',
				'title_space'	=>'2px',
				'title_color'	=>'#313131',
				'title_bg_color'	=>'#cccccc',
				'title_use_theme_fonts' 	=> 'custom-font-family',
				'title_font_family' 	=>'',
				'title_font_weight'		=>'600',
				'title_google_fonts'	=>'',				
				
				'title_tag' 	=> 'Pizza',
				'tag_size'	=>'12px',
				'tag_line'	=>'1.2',
				'tag_space'	=>'2px',
				'tag_color'	=>'#313131',
				'tag_bg_color'	=>'#f5f5f5',
				'tag_use_theme_fonts' 	=> 'custom-font-family',
				'tag_font_family' 	=>'',
				'tag_font_weight'		=>'600',
				'tag_google_fonts'	=>'',
				
				'price'			=> '$ 4.99',
				'price_size'	=>'18px',
				'price_line'	=>'1.2',
				'price_space'	=>'2px',
				'price_color'	=>'#313131',
				'price_use_theme_fonts' 	=> 'custom-font-family',
				'price_font_family' 	=>'',
				'price_font_weight'		=>'600',
				'price_google_fonts'	=>'',
				
				'desc_size'	=>'14px',
				'desc_line'	=>'1.2',
				'desc_space'	=>'0',
				'desc_color'	=>'#888888',
				'desc_use_theme_fonts' 	=> 'custom-font-family',
				'desc_font_family' 	=>'',
				'desc_font_weight'		=>'600',
				'desc_google_fonts'	=>'',
				
				'image_option'	=>	'',
				'img_shape'		=> 'none',
				
				'box_align'		=> 'text-left',
				'box_align_top'		=> 'bottom-left',
				'bg_options' 	=> 'bg-color',
				'bg_img'		=>'',
				'bg_color' 		=> '#e342aa',
				
				'bg_back_options' 	=> 'bg-color',
				'bg_back_img'		=>'',
				'bg_back_color'		=> '#ff214f',
				
				'border_height'		=> '1px',
				'border_color'		=> '#f5f5f5',
				'border_radius'		=> '2px',
				
				'border_style'		=> 'border-none',
				'bd_title_height'		=> '1px',
				'bd_title_color'		=> '#313131',
				
				'box_shadow'		=> '0px 0px 5px 0px rgba(181,175,181,1)',
								
				'animation_effects'=>'no-animation',
				'animation_delay'=>'50',
				'el_class' => '',
				'css' =>'',
		   ), $atts ) );
		   
		   $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,$el_class . vc_shortcode_custom_css_class( $css, ' ' ), 'tp_food_menu', $atts );
		  wp_enqueue_style( 'theplus-food-style');
		
		

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
				$title_css .='color:'.esc_attr($title_color).';';
			}
			if($menu_style =="style_2"){
			if($title_bg_color != "") {
				$title_css .='background:'.esc_attr($title_bg_color).';';
			}
			}
			if($title_size != "") {
				$title_css .='font-size:'.esc_attr($title_size).';';
			}
			if($title_line != "") {
				$title_css .='line-height:'.esc_attr($title_line).';';
			}
			if($title_space != "") {
				$title_css .='letter-spacing :'.esc_attr($title_space).';';
			}
			$title_css .= $title_font_family;
			$title_css .= '"';
			
		if($tag_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $tag_google_fonts );
			$tag_font_family = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
		}elseif($title_use_theme_fonts=='custom-font-family'){
			$tag_font_family='font-family:'.$tag_font_family.';font-weight:'.$tag_font_weight.';';
		}else{
			$tag_font_family='';
		}
		
		$tag_css = ' style="';
			
			if($tag_color != "") {
				$tag_css .='color:'.esc_attr($tag_color).';';
			}
			if($tag_bg_color != "") {
				$tag_css .='background-color:'.esc_attr($tag_bg_color).';';
			}
			if($tag_size != "") {
				$tag_css .='font-size:'.esc_attr($tag_size).';';
			}
			if($tag_line != "") {
				$tag_css .='line-height:'.esc_attr($tag_line).';';
			}
			if($tag_space != "") {
				$tag_css .='letter-spacing :'.esc_attr($tag_space).';';
			}
			$tag_css .= $tag_font_family;
			$tag_css .= '"';
	

	
		if($price_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $price_google_fonts );
			$price_font_family = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
		}elseif($title_use_theme_fonts=='custom-font-family'){
			$price_font_family='font-family:'.$price_font_family.';font-weight:'.$price_font_weight.';';
		}else{
			$price_font_family='';
		}
		
		$price_css = ' style="';
			
			if($price_color != "") {
				$price_css .='color:'.esc_attr($price_color).';';
			}
			if($price_size != "") {
				$price_css .='font-size:'.esc_attr($price_size).';';
			}
			if($price_line != "") {
				$price_css .='line-height:'.esc_attr($price_line).';';
			}
			if($price_space != "") {
				$price_css .='letter-spacing :'.esc_attr($price_space).';';
			}
			$price_css .= $price_font_family;
			$price_css .= '"';

			
			
		$style_class='';
			if($menu_style =="style_1"){
				$style_class = 'style-1';
			}else if($menu_style =="style_2"){
				$style_class = 'style-2';
			}else if($menu_style =="style_3"){
				$style_class = 'style-3';
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
			$description=$food_title=$food_price=$food_img = $food_tag =$food_flex_img='';
			
			
			if($desc_use_theme_fonts=='google-fonts'){
			$text_font_data = pt_plus_getFontsData( $desc_google_fonts );
			$desc_font_family = pt_plus_googleFontsStyles( $text_font_data );  
			$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($desc_use_theme_fonts=='custom-font-family'){
				$desc_font_family='font-family:'.$desc_font_family.';font-weight:'.$desc_font_weight.';';
			}else{
				$desc_font_family='';
			}
		
			if(isset($image_option) && !empty($image_option)){
			$img = wp_get_attachment_image_src($image_option, "full");
			$imgSrc = $img[0];
			$food_img = '<div class="food-img '.esc_attr($img_shape).'"> <img src="'.$imgSrc.'"> </div>';
			$food_flex_img = 'food-flex-img';
			}
			
			if(isset($bg_back_img) && !empty($bg_back_img)){
			$bg_back_img = wp_get_attachment_image_src($bg_back_img, "full");
			$img_bg_back_Src= $bg_back_img[0];			
			}else{$img_bg_back_Src = '';}

			if(isset($bg_img) && !empty($bg_img)){
			$bg_front_img = wp_get_attachment_image_src($bg_img, "full");
			$img_bg_Src = $bg_front_img [0];			
			}else{$img_bg_Src = '';}

			if(!empty($title_tag) ){
				$food_tag ='<h5 class="food-menu-tag" '.$tag_css.'>'.esc_html($title_tag).'</h5>';
			}
			if(!empty($title) ){
				$food_title ='<h3 class="food-menu-title" '.$title_css.'>'.esc_html($title).'</h3>';
			}
			if(!empty($price) ){
				$food_price ='<h4 class="food-menu-price" '.$price_css.'>'.esc_html($price).'</h4>';
			}
			
			
			if($content !=''){
				$content = wpb_js_remove_wpautop($content, true);
				$description='<div class="food-desc" > '.$content.' </div>';
				}
		
			$uid=uniqid('food_menu');
			
			if ($menu_style == 'style_1'){
				$box_align_1 = $box_align;
			}else{
				$box_align_1 = '';
			}
			
			if ($menu_style== 'style_2'){
				$box_align_top_1 = $box_align_top;
			}else{
				$box_align_top_1 = '';
			}
			
			$food_menu ='<div class="pt-plus-food-menu  '.esc_attr($box_align).' '.esc_attr($uid).'  food-menu-'.esc_attr($style_class).'" data-uid="'.esc_attr($uid).'" >';
			if ($menu_style == 'style_1'){
				$food_menu.='<div class="food-menu-box">';				
					$food_menu.= $food_tag;
					$food_menu.= $food_title;
					$food_menu.= $description;
					$food_menu.= $food_price;
				$food_menu.='</div>';
			}else if ($menu_style == 'style_2'){
				$food_menu.='<div class="food-menu-box '.esc_attr($box_align_top_1).'">';	
					$food_menu.='<div class="food-flipbox flip-horizontal flip-horizontal height-full">';
						$food_menu.='<div class="food-flipbox-holder height-full perspective bezier-1">';
							$food_menu.='<div class="food-flipbox-front bezier-1 no-backface origin-center">';
								$food_menu.='<div class="food-flipbox-content width-full">';
									$food_menu.= '<div class="food-menu-block">'.$food_tag.'</div>';
									$food_menu.= '<div class="food-menu-block">'.$food_title.'</div>';
									$food_menu.= $food_price;
								$food_menu.='</div>';
							$food_menu.='</div>';
							$food_menu.='<div class="food-flipbox-back fold-back-horizontal no-backface bezier-1 origin-center">';
								$food_menu.='<div class="food-flipbox-content width-full ">';
									$food_menu.='<div class="text-center">';
										$food_menu.= $description;		
									$food_menu.='</div>';
								$food_menu.='</div>';
							$food_menu.='</div>';
						$food_menu.='</div>';
					$food_menu.='</div>';
				$food_menu.='</div>';
			}else if ($menu_style == 'style_3'){
				$food_menu.='<div class="food-menu-box">';
					$food_menu.='<div class="food-menu-flex ">';
						$food_menu.='<div class="food-flex-line ">';
							$food_menu.='<div class="food-flex-imgs '.esc_attr($food_flex_img).'">';
								$food_menu.= $food_img;
							$food_menu.='</div>';		
							$food_menu.='<div class="food-flex-content">';
								$food_menu.= '<div class="food-menu-block">'.$food_tag.'</div>';
								$food_menu.='<div class="food-title-price">';
									$food_menu.= $food_title;
									$food_menu.= '<div class="food-menu-divider"><div class="menu-divider '.esc_attr($border_style).'"></div></div>';
									$food_menu.= $food_price;
								$food_menu.='</div>';
								$food_menu.= $description;
							$food_menu.='</div>';	
						$food_menu.='</div>';	
						
					$food_menu.='</div>';
				$food_menu.='</div>';
			}
			$food_menu.='</div>';
			
			
			$css_rule='';
			$css_rule .= '<style >';			
			 $css_rule .= '.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-1 .food-menu-box,.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-2 .food-menu-box .food-flipbox-front{background-image: url('.esc_js($img_bg_Src).');background-color: '.esc_js($bg_color).';}.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-2 .food-menu-box .food-flipbox-back {background-image: url('.esc_js($img_bg_back_Src).');background-color: '.esc_js($bg_back_color).';}.'.esc_js($uid).'.pt-plus-food-menu .food-menu-box .service-desc, .'.esc_js($uid).'.pt-plus-food-menu .food-menu-box .food-desc p{font-size:'.esc_js($desc_size).';line-height:'.esc_js($desc_line).';letter-spacing:'.esc_js($desc_space).';color:'.esc_js($desc_color).';'.esc_js($desc_font_family).'}.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-1 .food-menu-box,.'.esc_js($uid).'.pt-plus-food-menu .food-flipbox-back, .'.esc_js($uid).'.pt-plus-food-menu .food-flipbox-front,.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-3 .food-img img{border:'.esc_js($border_height).' solid '.esc_js($border_color).';border-radius:'.esc_js($border_radius).'}.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-menu-divider .menu-divider{border-width: '.esc_js($bd_title_height).'; border-color: '.esc_js($bd_title_color).';}.'.esc_js($uid).'.pt-plus-food-menu.food-menu-style-1 .food-menu-box {-webkit-box-shadow: '.esc_js($box_shadow).';-moz-box-shadow: '.esc_js($box_shadow).'; box-shadow: '.esc_js($box_shadow).';}';
			$css_rule .= '</style>';	
			
		   return $css_rule.$food_menu;
		}
		function init_tp_food_menu(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
						"name" => __("Food Menu", "pt_theplus"),
						"base" => "tp_food_menu",
						'icon'	=> 'tp-food-menu',
						"category" => __("The Plus", "pt_theplus"),
						"description" => esc_html__('Creative Menu Options', 'pt_theplus'),
						"params" => array(
							array(
									'type'        => 'radio_select_image',
									'heading' =>  esc_html__('Menu Style', 'pt_theplus'), 
									'param_name'  => 'menu_style',
									'admin_label' => true, 
									'simple_mode' => false,
									'value' => 'style_1',
									'options'     => array(
										'style_1' => array(
											'tooltip' => esc_attr__('Modern','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/food-menu/tp-foodmenu-1.jpg'
										),
										'style_2' => array(
											'tooltip' => esc_attr__('Simple','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/food-menu/tp-foodmenu-2.jpg'
										),
										'style_3' => array(
											'tooltip' => esc_attr__('Classic','pt_theplus'),
											'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/food-menu/tp-foodmenu-3.jpg'
										),
									),
								),
							array(
								"type" => "textfield",
								'heading' =>  esc_html__('Title', 'pt_theplus'), 
								"param_name" => "title",
								"value" => 'Italian Pizza',
								"admin_label" => true
								
							),
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Title Settings', 'pt_theplus'),
								'param_name' => 'title_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_size",
								'value' => '24px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_line",
								'value' => '1.2',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_space",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "title_color",
								"value" => '#313131',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')), 
								"param_name" => "title_bg_color",
								"value" => '#cccccc',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'dependency' => array(
										'element' => 'menu_style',
										'value' => 'style_2',
									),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
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
								'value' => '',
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
								"type" => "textfield",
								'heading' =>  esc_html__('Tag', 'pt_theplus'), 
								"param_name" => "title_tag",
								"value" => 'Pizza',
								
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Tag Settings', 'pt_theplus'),
								'param_name' => 'tag_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "tag_size",
								'value' => '12px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "tag_line",
								'value' => '1.2',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "tag_space",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "tag_color",
								"value" => '#313131',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')), 
								"param_name" => "tag_bg_color",
								"value" => '#f5f5f5',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
									'param_name' => 'tag_use_theme_fonts',
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
								'param_name' => 'tag_font_family',
								'value' => '',
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'tag_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'tag_font_weight',
								'value' => __('600','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'tag_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'tag_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'tag_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Styles', 'pt_theplus'),	
							),	
							
							
							
							array(
								"type" => "textfield",
								'heading' =>  esc_html__('Price', 'pt_theplus'), 
								"param_name" => "price",
								"value" => '$ 4.99',
								"admin_label" => true
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Price Settings', 'pt_theplus'),
								'param_name' => 'price_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "price_size",
								'value' => '18px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "price_line",
								'value' => '1.2',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "price_space",
								'value' => '2px',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "price_color",
								"value" => '#313131',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
									'param_name' => 'price_use_theme_fonts',
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
								'param_name' => 'price_font_family',
								'value' => '',
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'price_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
								'type' => 'textfield',
								'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
								'param_name' => 'price_font_weight',
								'value' => __('600','pt_theplus'),
								'edit_field_class' => 'vc_col-xs-6',
								'description' => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),	
								'dependency' => array(
										'element' => 'price_use_theme_fonts',
										'value' => 'custom-font-family',
									),
							),
							array(
									'type' => 'google_fonts',
									'param_name' => 'price_google_fonts',
									'value' => '',
									'settings' => array(
										'fields' => array(
											'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
											'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
										),
									),
									'dependency' => array(
										'element' => 'price_use_theme_fonts',
										'value' => 'google-fonts',
									),
									'group' => esc_attr__('Styles', 'pt_theplus'),	
							),
								
								
							array(
								"type" => "textarea_html",
								'heading' =>  esc_html__('Description', 'pt_theplus'), 
								"param_name" => "content",
								"value" => '',
								"description" => "",
								"admin_label" => true
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Description Settings', 'pt_theplus'),
								'param_name' => 'desc_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "desc_size",
								'value' => '14px',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "desc_line",
								'value' => '1.2',
								"description" => '',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "desc_space",
								'value' => '0',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select this as before or after to title text.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
								"param_name" => "desc_color",
								"value" => '#888888',
								"edit_field_class" => "vc_col-xs-6",
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							array(
									'type' => 'dropdown',
									'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
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
								'param_name' => 'desc_font_family',
								'value' => '',
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
								'type' => 'attach_image',
								'heading' => __('Image', 'pt_theplus'),
								'param_name' => 'image_option',
								'value' => '',
								'dependency' => array(
									'element' => 'menu_style',
									'value' => 'style_3',
								),
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Image Shape', 'pt_theplus'), 
								'param_name' => 'img_shape',
								'value' => array(
									__('None ', 'pt_theplus') => 'none',
									__('Rounded', 'pt_theplus') => 'img-rounded',
									__('Circle', 'pt_theplus') => 'img-circle',
									
								),
								'std' => 'none',
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_3"),
								),
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Background options', 'pt_theplus'), 
								'param_name' => 'bg_options',
								'value' => array(
									__('Background Color', 'pt_theplus') => 'bg-color',
									__('Background Image', 'pt_theplus') => 'bg-img',
								),
								'std' => 'bg-color',
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_1","style_2"),
								),
							),
							 array(
								'type' => 'attach_image',
								'heading' => __('Background Image', 'pt_theplus'),
								'param_name' => 'bg_img',
								'value' => '',
								'dependency' => array(
									'element' => 'bg_options',
									'value' => 'bg-img'
								),
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Background Color', 'pt_theplus'),
								'param_name' => 'bg_color',
								'value' => '#e342aa ',
								'dependency' => array(
									'element' => 'bg_options',
									'value' => 'bg-color'
								),
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Background Flip Back options', 'pt_theplus'), 
								'param_name' => 'bg_back_options',
								'value' => array(
									__('Background Color', 'pt_theplus') => 'bg-color',
									__('Background Image', 'pt_theplus') => 'bg-img',
								),
								'std' => 'bg-color',
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_2"),
								),
							),
							 array(
								'type' => 'attach_image',
								'heading' => __('Background Image', 'pt_theplus'),
								'param_name' => 'bg_back_img',
								'value' => '',
								'dependency' => array(
									'element' => 'bg_back_options',
									'value' => 'bg-img'
								),
							),
							array(
								'type' => 'colorpicker',
								'heading' => __('Background Color', 'pt_theplus'),
								'param_name' => 'bg_back_color',
								'value' => '#ff214f ',
								'dependency' => array(
									'element' => 'bg_back_options',
									'value' => 'bg-color'
								),
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Box Align', 'pt_theplus'), 
								'param_name' => 'box_align',
								'value' => array(
									__('Left', 'pt_theplus') => 'text-left',
									__('Center', 'pt_theplus') => 'text-center',
									__('Right', 'pt_theplus') => 'text-right',
								),
								'std' => 'text-left',
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_1"),
								),
							),
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Box Align', 'pt_theplus'), 
								'param_name' => 'box_align_top',
								'value' => array(
									__('Top Left', 'pt_theplus') => 'top-left',
									__('Top Right', 'pt_theplus') => 'top-right',
									__('Bottom Left', 'pt_theplus') => 'bottom-left',
									__('Bottom Right', 'pt_theplus') => 'bottom-right',

								),
								'std' => 'bottom-left',
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_2"),
								),
							),
							
							
							
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Border Settings', 'pt_theplus'),
								'param_name' => 'border_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
							),
							
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Border Height in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Border Height', 'pt_theplus')),
								"param_name" => "border_height",
								'value' => '1px',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose color by using this options.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
								"param_name" => "border_color",
								'value' => '#f5f5f5',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								"edit_field_class" => "vc_col-xs-6"
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Border Height in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Border Radius ', 'pt_theplus')),
								"param_name" => "border_radius",
								'value' => '2px',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false
							),
							
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Border Title Settings', 'pt_theplus'),
								'param_name' => 'border_title_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
								 'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_3"),
								),
							),
							array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Border styles by using this options.','pt_theplus').'</span></span>'.esc_html__('Border style', 'pt_theplus')),
								"param_name" => "border_style",
								"value" => array(
									__('None', 'pt_theplus') => 'border-none',
									__('Dashed', 'pt_theplus') => 'border-dashed',
									__('Solid', 'pt_theplus') => 'border-solid',
									),
								"std" => 'border-none',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_3"),
								),
								"admin_label" => false
							),
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Border Height in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Border Height', 'pt_theplus')),
								"param_name" => "bd_title_height",
								'value' => '1px',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_3"),
								),
							),
							array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose color by using this options.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
								"param_name" => "bd_title_color",
								'value' => '#313131',
								"description" => '',
								'group' => esc_attr__('Styles', 'pt_theplus'),
								"admin_label" => false,
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_3"),
								),
								"edit_field_class" => "vc_col-xs-6"
							),
							
							array(
								'type' => 'pt_theplus_heading_param',
								'text' => esc_html__('Box Shadow Settings', 'pt_theplus'),
								'param_name' => 'box_setting',
								'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
								 'group' => esc_attr__('Styles', 'pt_theplus'),
								 'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_1"),
								),
							),
							
							
							array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Border Height in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Box Shadow', 'pt_theplus')),
								"param_name" => "box_shadow",
								'value' => '0px 0px 5px 0px rgba(181,175,181,1)',
								"edit_field_class" => "vc_col-xs-6",
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'dependency' => array(
									'element' => 'menu_style',
									'value' =>  array("style_1"),
								),
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
	new ThePlus_food_menu;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_food_menu'))
	{
		class WPBakeryShortCode_tp_food_menu extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}