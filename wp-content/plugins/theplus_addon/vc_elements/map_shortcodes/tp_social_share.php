<?php
// Social Icons Elements
if(!class_exists("ThePlus_social_share")){
	class ThePlus_social_share{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_social_share') );
			add_shortcode( 'tp_social_share',array($this,'tp_social_share_shortcode'));
		}
		function tp_social_share_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				  'styles'=>'style-1',
				  'pt_plus_social_networks'=>'',
				  'alignment'=>'text-center',
				  'height_social' =>'50px',
				  'hover_animation' =>'hover-1',
				  'icon_size'=>'20px',
				  'text_size'=>'20px',
				  'text_letter_space'=>'0px',
				  'animation_effects'=>'no-animation',
				  'animation_delay'=>'50',
				  'el_class' =>'',
		   ), $atts ) );
		   
		  
		   $social_text=$icon=$link=$link_atts_title=$link_atts_url=$link_atts_target=$icon_html=$hover_style=$height_css=$height_li_css=$social_chaffle='';
		   
		   if($hover_animation == 'hover-1'){
				$social_animation ='social-faded';
			}else if($hover_animation == 'hover-2'){
				$social_animation = 'socail-chaffal';
				$social_chaffle = 'ts-chaffle';
			}else{
				$social_animation ='';
				$social_chaffle ='';
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
			
			if($styles == 'style-14' ){
				$height_css = ' style="';
					if($height_social!= "") {
						$height_css .= 'height: '.esc_attr($height_social).';';
					}
				$height_css .= '"';		
			}
			if($styles == 'style-15'){
				$height_li_css = ' style="';
					if($height_social!= "") {
						$height_li_css .= 'height: '.esc_attr($height_social).';';
					}
				$height_li_css .= '"';
			}
		   $css_loop='';
		   $social='<div class="pt_plus_social_list '.esc_attr($alignment).' '.esc_attr($el_class).' '.esc_attr($styles).' '.esc_attr($animated_class).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
				$social .='<ul class="social_list '.esc_attr($social_animation).'" '.$height_css.'>';
					if(isset($pt_plus_social_networks) && !empty($pt_plus_social_networks) && function_exists('vc_param_group_parse_atts')) {
						$pt_plus_social_networks = (array) vc_param_group_parse_atts( $pt_plus_social_networks);	
						foreach($pt_plus_social_networks as $network) {
							
						 $id=rand(1000,10000000);
							if(isset($network['pt_plus_social_icons']) && isset($network['social_url'])) {
								if(isset($network['pt_plus_social_icons'])) {
									$icon = $network['pt_plus_social_icons'];
								}
								if(isset($network['social_url'])) {
									$link = vc_build_link($network['social_url']);
								}
								if(isset($link['url']) && !empty($link['url'])) {
									$link_atts_url = 'href="'.esc_url($link['url']).'"';
								}
								if(isset($link['title']) && !empty($link['title'])) {
									$link_atts_title = 'title="'.esc_attr($link['title']).'"';
								}
								if(isset($link['target']) && !empty($link['target'])) {
									$link_atts_target = 'target="'.esc_attr($link['target']).'"';
								}
								if(isset($network['social_text']) && !empty($network['social_text']) && ($styles=='style-1' || $styles=='style-2' || $styles=='style-4' || $styles=='style-10' || $styles=='style-12' || $styles=='style-14' || $styles=='style-15')){
									$social_text='<span class="'.esc_attr($social_chaffle).'" data-lang="en">'.$network['social_text'].'</span>';
								}
								$icon_html = '<i class="fa '.esc_attr($icon).'"></i>';
								if($styles=='style-6'){
									$hover_style='<i class="social-hover-style"></i>';
								}
								if($styles=='style-9'){
									$hover_style = '<span class="line-top-left style-'.esc_attr($icon).'"></span><span class="line-top-center style-'.esc_attr($icon).'"></span><span class="line-top-right style-'.esc_attr($icon).'"></span><span class="line-bottom-left style-'.esc_attr($icon).'"></span><span class="line-bottom-center style-'.esc_attr($icon).'"></span><span class="line-bottom-right style-'.esc_attr($icon).'"></span>';
								}
								$border_hover_color=$icon_color=$icon_hover_color=$bg_color=$bg_hover_color=$border_color='';
								if(!empty($network['icon_color'])){
									$icon_color= $network['icon_color'];
								}
								if(!empty($network['icon_hover_color'])){
									$icon_hover_color= $network['icon_hover_color'];
								}
								if(!empty($network['bg_color'])){
									$bg_color= $network['bg_color'];
								}
								if(!empty($network['bg_hover_color'])){
									$bg_hover_color= $network['bg_hover_color'];
								}
								if(!empty($network['border_color'])){
									$border_color= $network['border_color'];
								}
								if(!empty($network['border_hover_color'])){
									$border_hover_color= $network['border_hover_color'];
								}
					if($styles=='style-1'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-1.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-1.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-1.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-2'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-2.social-'.esc_js($id).' a{color:'.esc_js($icon_color).';border-color:'.esc_js($icon_color).';}.pt_plus_social_list ul.social_list .style-2.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';}.pt_plus_social_list ul.social_list .style-2.social-'.esc_js($id).' a i.fa{font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-2.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-3'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-3.social-'.esc_js($id).'{background: '.esc_js($bg_color).';border-color:'.esc_js($border_color).';background-clip: content-box;}.pt_plus_social_list ul.social_list .style-3.social-'.esc_js($id).' a{color:'.esc_js($icon_color).';}.pt_plus_social_list ul.social_list .style-3.social-'.esc_js($id).':hover{background: '.esc_js($bg_hover_color).';border-color:'.esc_js($border_hover_color).';background-clip: content-box;}.pt_plus_social_list ul.social_list .style-3.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';}.pt_plus_social_list ul.social_list .style-3.social-'.esc_js($id).' a i.fa{font-size:'.esc_js($icon_size).';}';
				}else if($styles=='style-4'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-4.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';}.pt_plus_social_list ul.social_list .style-4.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-4.social-'.esc_js($id).' a i.fa{font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-4.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-5'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-5.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-5.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-5.social-'.esc_js($id).':hover a:before{background: '.esc_js($bg_hover_color).';}';
				}else if($styles=='style-6'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-6.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-6.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-6.social-'.esc_js($id).':hover a .social-hover-style{background: '.esc_js($bg_hover_color).';}';
				}else if($styles=='style-7'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-7.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-7.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';-webkit-box-shadow: 0px 0px 10px 0px '.esc_js($bg_hover_color).';-moz-box-shadow: 0px 0px 10px 0px '.esc_js($bg_hover_color).';box-shadow: 0px 0px 10px 0px '.esc_js($bg_hover_color).';}';
				}else if($styles=='style-8'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-8.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-8.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}';
				}else if($styles=='style-9'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-9.social-'.esc_js($id).' a{color:'.esc_js($icon_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-9.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';}.pt_plus_social_list ul.social_list .style-9.social-'.esc_js($id).':hover a span:before{background: '.esc_js($bg_hover_color).';}';
				}else if($styles=='style-10'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-10.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-10.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-10.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-11'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-11.social-'.esc_js($id).' a{color:'.esc_js($icon_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-11.social-'.esc_js($id).':hover a{color:'.esc_js($icon_hover_color).';}.pt_plus_social_list ul.social_list .style-11.social-'.esc_js($id).' a:before{-webkit-box-shadow: inset 0 0 0 70px '.esc_js($bg_color).';-moz-box-shadow: inset 0 0 0 70px '.esc_js($bg_color).';box-shadow: inset 0 0 0 70px '.esc_js($bg_color).';}.pt_plus_social_list ul.social_list .style-11.social-'.esc_js($id).':hover  a:before{-webkit-box-shadow: inset 0 0 0 4px '.esc_js($bg_hover_color).';-moz-box-shadow: inset 0 0 0 4px '.esc_js($bg_hover_color).';box-shadow: inset 0 0 0 4px '.esc_js($bg_hover_color).';}';
				}else if($styles=='style-12'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-12.social-'.esc_js($id).' a .fa{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';}.pt_plus_social_list ul.social_list .style-12.social-'.esc_js($id).' a {font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-12.social-'.esc_js($id).':hover a span{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-12.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-13'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';}.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).' a:after,.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).' a:before{border-color:'.esc_js($border_color).';}.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).':hover a:after,.pt_plus_social_list ul.social_list .style-13.social-'.esc_js($id).':hover a:before{border-color:'.esc_js($border_hover_color).';}';
				}else if($styles=='style-14'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-14.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-14.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-14.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}else if($styles=='style-15'){
					$css_loop.= '.pt_plus_social_list ul.social_list .style-15.social-'.esc_js($id).' a{background: '.esc_js($bg_color).';color:'.esc_js($icon_color).';border-color:'.esc_js($border_color).';font-size:'.esc_js($icon_size).';}.pt_plus_social_list ul.social_list .style-15.social-'.esc_js($id).':hover a{background: '.esc_js($bg_hover_color).';color:'.esc_js($icon_hover_color).';border-color:'.esc_js($border_hover_color).';}.pt_plus_social_list ul.social_list .style-15.social-'.esc_js($id).' a span{font-size:'.esc_js($text_size).';letter-spacing:'.esc_js($text_letter_space).';}';
				}
								$social .= '<li class="'.esc_attr($styles).'  social-'.esc_attr($icon).' social-'.esc_attr($id).'"  '.$height_li_css.'><a '.$link_atts_url.' '.$link_atts_title.' '.$link_atts_target.'>'.$icon_html.$social_text.$hover_style.'</a></li>';
							}
						}
					}
				$social .='</ul>';
			$social .='</div>';
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .=$css_loop;
			$css_rule .= '</style>';
		   return $css_rule.$social;
		}
		function init_tp_social_share(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Social Icons", 'pt_theplus'),
					"base" => "tp_social_share",
					"icon" => "tp-social-share",
					"category" => __("The Plus", "pt_theplus"),
					"description" => __('List your Social Accounts', 'pt_theplus'),
					"params" => array(
						array(
							'type'        => 'radio_select_image',
							'heading' =>  esc_html__('Social Icon Style ', 'pt_theplus'), 
							'param_name' => 'styles',
							'admin_label' => true,
							'simple_mode' => false,
							'value' => 'style-1',
							'options'     => array(
								'style-1' => array(
								'tooltip' => esc_attr__('Style-1','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-1.png'
								),
								'style-2' => array(
								'tooltip' => esc_attr__('Style-2','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-2.png'
								),
								'style-3' => array(
								'tooltip' => esc_attr__('Style-3','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-3.png'
								),
								'style-4' => array(
								'tooltip' => esc_attr__('Style-4','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-4.png'
								),
								'style-5' => array(
								'tooltip' => esc_attr__('Style-5','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-5.png'
								),
								'style-6' => array(
								'tooltip' => esc_attr__('Style-6','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-6.png'
								),
								'style-7' => array(
								'tooltip' => esc_attr__('Style-7','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-7.png'
								),
								'style-8' => array(
								'tooltip' => esc_attr__('Style-8','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-8.png'
								),
								'style-9' => array(
								'tooltip' => esc_attr__('Style-9','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-9.png'
								),
								'style-10' => array(
								'tooltip' => esc_attr__('Style-10','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-10.png'
								),
								'style-11' => array(
								'tooltip' => esc_attr__('Style-11','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-11.png'
								),
								'style-12' => array(
								'tooltip' => esc_attr__('Style-12','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-12.png'
								),
								'style-13' => array(
								'tooltip' => esc_attr__('Style-13','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-13.png'
								),
								'style-14' => array(
								'tooltip' => esc_attr__('Style-14','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-14.png'
								),
								'style-15' => array(
								'tooltip' => esc_attr__('Style-15','pt_theplus'),
								'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/soical-share/ts-social-style-15.png'
								),
							),
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Please Enter value of Section height. e.g. 50px, 300px.','pt_theplus').'</span></span>'.esc_html__('Section Height', 'pt_theplus')),
							"param_name" => "height_social",
							"value" => "50px",
							"description" => "",
							'dependency' => array(
								'element' => 'styles',
								'value' => array(
									'style-14',
									'style-15'
								)
							)
						),
						array(
							"type" => "dropdown",
							
							"heading" => __("Select hover style", "pt_theplus"),
							"param_name" => "hover_animation",
							"value" => array(
								__("Style 1", "pt_theplus") => "hover-1",
								__("Style 2", "pt_theplus") => "hover-2"
							),
							"description" => "",
							'dependency' => array(
								'element' => 'styles',
								'value' => array(
									'style-14',
									'style-15'
								)
							),
							"std" => 'hover-1'
						),
						array(
							'type' => 'param_group',
							"heading" => __("Add Social Icon", "pt_theplus"),			
							'param_name' => 'pt_plus_social_networks',
							'params' => array(
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Social Network from this option.','pt_theplus').'</span></span>'.esc_html__('Social Network Select', 'pt_theplus')),
									'param_name' => 'pt_plus_social_icons',
									'value' => array(
										esc_html__('None', 'pt_theplus') => 'none',
										esc_html__('Deviantart link', 'pt_theplus') => 'fa-deviantart',
										esc_html__('Digg link', 'pt_theplus') => 'fa-digg',
										esc_html__('Dribbble link', 'pt_theplus') => 'fa-dribbble',
										esc_html__('Dropbox link', 'pt_theplus') => 'fa-dropbox',
										esc_html__('Facebook link', 'pt_theplus') => 'fa-facebook',
										esc_html__('Flickr link', 'pt_theplus') => 'fa-flickr',
										esc_html__('Foursquare link', 'pt_theplus') => 'fa-foursquare',
										esc_html__('Google + link', 'pt_theplus') => 'fa-google-plus',
										esc_html__('Instagram link', 'pt_theplus') => 'fa-instagram',
										esc_html__('LastFM link', 'pt_theplus') => 'fa-lastfm',
										esc_html__('LinkedIN link', 'pt_theplus') => 'fa-linkedin',
										esc_html__('Pinterest link', 'pt_theplus') => 'fa-pinterest-p',
										esc_html__('RSS link', 'pt_theplus') => 'fa-rss',
										esc_html__('Tumblr link', 'pt_theplus') => 'fa-tumblr',
										esc_html__('Twitter link', 'pt_theplus') => 'fa-twitter',
										esc_html__('Vimeo link', 'pt_theplus') => 'fa-vimeo',
										esc_html__('Wordpress link', 'pt_theplus') => 'fa-wordpress',
										esc_html__('YouTube link', 'pt_theplus') => 'fa-youtube',
										esc_html__('Mail link', 'pt_theplus') => 'fa-envelope',
										esc_html__('Xing', 'pt_theplus') => 'fa-xing',
										esc_html__('Spotify', 'pt_theplus') => 'fa-spotify',
										esc_html__('Houzz', 'pt_theplus') => 'fa-houzz',
										esc_html__('Skype', 'pt_theplus') => 'fa-skype',
										esc_html__('Slideshare', 'pt_theplus') => 'fa-slideshare',
										esc_html__('Bandcamp', 'pt_theplus') => 'fa-bandcamp',
										esc_html__('Soundcloud', 'pt_theplus') => 'fa-soundcloud',
										esc_html__('Snapchat', 'pt_theplus') => 'fa-snapchat-ghost',
										esc_html__('Behance', 'pt_theplus') => 'fa-behance',
										esc_html__('Microsoft Windows', 'pt_theplus') => 'fa-windows',
										esc_html__('Video', 'pt_theplus') => 'fa-video-camera',
										esc_html__('TripAdvisor', 'pt_theplus') => 'fa-tripadvisor'
									)
									
								),
								array(
									'type' => 'vc_link',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add Social Network Profile URL using this option.','pt_theplus').'</span></span>'.esc_html__('Social Link', 'pt_theplus')),
									'param_name' => 'social_url',
									'description' => '',
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add title you want to show for social media using this option. E.g. FaceBook, Instagram, etc.','pt_theplus').'</span></span>'.esc_html__('Title Of Social Media', 'pt_theplus')),
									"description" => "",
									'param_name' => 'social_text',
									'admin_label' => true,
									'value' => ''
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
									'param_name' => 'icon_color',
									'value' => '#d3d3d3',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon hover color using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Hover Color', 'pt_theplus')),
									'param_name' => 'icon_hover_color',
									'value' => '#fff',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
									'param_name' => 'bg_color',
									'value' => '#404040',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background hover color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Hover Color', 'pt_theplus')),
									'param_name' => 'bg_hover_color',
									'value' => '#222222',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
									'param_name' => 'border_color',
									'value' => '#404040',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border hover color using this option.','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
									'param_name' => 'border_hover_color',
									'value' => '#222222',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6',
								),
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Social Media Icon Alignment using this option.','pt_theplus').'</span></span>'.esc_html__('Icons Alignment', 'pt_theplus')),
							'param_name' => 'alignment',
							'value' => array(
								esc_html__('Center', 'pt_theplus') => 'text-center',
								esc_html__('Left', 'pt_theplus') => 'text-left',
								esc_html__('Right', 'pt_theplus') => 'text-right'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'std' => 'text-center'
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size of Icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Font Size', 'pt_theplus')),
							'param_name' => 'icon_size',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => '20px',
							"description" => '',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size of Social Media Title using this option.','pt_theplus').'</span></span>'.esc_html__('Title Font Size', 'pt_theplus')),
							'param_name' => 'text_size',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => '20px',
							"description" => '',
							'dependency' => array(
								'element' => 'styles',
								'value' => array(
									'style-1',
									'style-2',
									'style-4',
									'style-10','style-12',
									'style-14',
									'style-15',
								)
							)
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing of Social Media Title using this option.','pt_theplus').'</span></span>'.esc_html__('Title Letter Spacing', 'pt_theplus')),
							'param_name' => 'text_letter_space',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => '0px',
							"description" => '',
							'dependency' => array(
								'element' => 'styles',
								'value' => array(
									'style-1',
									'style-2',
									'style-4',
									'style-10',
									'style-14',
									'style-15',
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
	new ThePlus_social_share;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_social_share'))
	{
		class WPBakeryShortCode_tp_social_share extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}

