<?php
// Gallerys List Elements
if(!class_exists("ThePlus_gallery_list")){
	class ThePlus_gallery_list{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_gallery_list') );
			add_shortcode( 'tp_gallery_list',array($this,'tp_gallery_list_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_gallery_list_scripts' ), 1 );
		}
		function tp_gallery_list_scripts() {
			wp_register_style( 'theplus-gallery-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-gallery-style.css', false, '1.0.0' );
		}
		function tp_gallery_list_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'gallery_images' =>'',
				'gallery_style'=>'style-1',
				'layout'=>'grid',
				'animation_effects'=>'transition.fadeIn',
				'animation_delay'=>'50',
				'animated_column_list'=>'',
				'animation_stagger'=>'150',
				
				'desktop_column'=>'3',
				'tablet_column'=>'6',
				'mobile_column'=>'12',
				
				'title_font_size'=>'20px',
				'title_line_height'=>'25px',
				'title_letter_space'=>'0px',
				'title_color'=>'#fff',
				'title_hover_color'=>'#fff',
				'title_use_theme_fonts'=>'custom-font-family',
				'title_font_family'=>'',
				'title_font_weight'=>'700',
				'title_google_fonts'=>'',
				
				'desc_color'=>'#fff',
				'desc_font_size'=>'15px',
				'desc_line_height'=>'20px',
				
				
				'box_bg_color'=>'rgba(0,0,0,0.5)',
				'box_bg_hover_color'=>'rgba(0,0,0,0.7)',
				//'box_border_color'=>'#313131',
				//'box_border_hover_color'=>'#313131',
				'column_shadow'=>'0px 0px 2px 0px rgba(0,0,0,0.25)',
				'column_hover_shadow'=>'0px 2px 15px rgba(0,0,0,0.17)',
				
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
				
				'dots_border_color'=>'#000',
				'dots_bg_color'=>'#fff',
				'dots_active_border_color'=>'#000',
				'dots_active_bg_color'=>'#000',
				
				'arrow_bg_color'=>'#c44d48',
				'arrow_icon_color'=>'#fff',
				'arrow_hover_bg_color'=>'#fff',
				'arrow_hover_icon_color'=>'#c44d48',
				'arrow_text_color'=>'#fff',
				
				'grayscale_img'=>'off',
				'column_space' =>'',
				'column_space_pading' => '10px',
'parallax_column'=>'',
				'el_class' =>'',
				), $atts ) );
				
				
				wp_enqueue_style( 'theplus-gallery-style');
				wp_enqueue_script( 'prettyphoto' );
				wp_enqueue_style( 'prettyphoto' );
				$rand_no=rand(1000000, 1500000);
				
				$desktop_class='vc_col-md-'.esc_attr($desktop_column);
				$tablet_class='vc_col-sm-'.esc_attr($tablet_column);
				$mobile_class='vc_col-xs-'.esc_attr($mobile_column);
				
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
				$animated_attr='';
				$animated_attr .=' data-animate-type="'.esc_attr($animation_effects).'"';
				$animated_attr .=' data-animate-delay="'.esc_attr($animation_delay_time).'"';
				$animated_columns='';
				if($animated_column_list==''){
					$animated_columns='';
				}else if($animated_column_list=='columns'){
					if($layout=='grid' || $layout=='masonry' || $layout=='metro'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="columns"';
					}else{
						$animated_columns='';
					}
				}else if($animated_column_list=='stagger'){
					if($layout=='grid' || $layout=='masonry' || $layout=='metro'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="stagger"';
						$animated_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
					}else{
						$animated_columns='';
					}
				}
				$attr=$data_column='';
				
				$isotope=' list-isotope ';
				if($layout=='grid'){
					$attr .=' data-layout-type="fitRows" ';
					$isotope=' list-isotope ';
				}else if($layout=='masonry'){
					$attr .=' data-layout-type="masonry" ';
					$isotope=' list-isotope ';
				}else if($layout=='metro'){
					$attr .=' data-layout-type="metro" ';
					$isotope=' list-isotope-metro ';
					$attr .=' data-columns="'.esc_attr($desktop_column).'" ';
					$attr .=' data-pad="30px" ';
				}else if($layout=='carousel'){
					$isotope=' list-carousel-slick';
				}
				
				
			if($title_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $title_google_fonts );
				$title_style = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($title_use_theme_fonts=='custom-font-family'){
				$title_style='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
			}else{
				$title_style='';
			}
				
				$attr .=' data-id="gallery-'.esc_attr($rand_no).'"';
				$attr .=' data-style="gallery-'.esc_attr($gallery_style).'"';
				if($column_space == 'on'){
					$column_padding =$column_space_pading;	
				}else{
					$column_padding ='0px';	
				}
				
							
				if($layout=='carousel'){
					$attr .=' data-show_arrows="'.esc_attr($show_arrows).'"';
					$attr .=' data-show_dots="'.esc_attr($show_dots).'"';
					$attr .=' data-show_draggable="'.esc_attr($show_draggable).'"';
					$attr .=' data-slide_loop="'.esc_attr($slide_loop).'"';
					$attr .=' data-slide_autoplay="'.esc_attr($slide_autoplay).'"';
					$attr .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
					$attr .=' data-steps_slide="'.esc_attr($steps_slide).'"';
					$attr .=' data-carousel_column="'.esc_attr($carousel_column).'"';
					$attr .=' data-carousel_tablet_column="'.esc_attr($carousel_tablet_column).'"';
					$attr .=' data-carousel_mobile_column="'.esc_attr($carousel_mobile_column).'"';
					$attr .=' data-dots_style="slick-dots '.esc_attr($dots_style).'" ';
					$attr .=' data-arrows_style="'.esc_attr($arrows_style).'" ';
					$attr .=' data-arrows_position="'.esc_attr($arrows_position).'" ';
					
					$attr .=' data-dots_border_color="'.esc_attr($dots_border_color).'" ';
					$attr .=' data-dots_bg_color="'.esc_attr($dots_bg_color).'" ';
					$attr .=' data-dots_active_border_color="'.esc_attr($dots_active_border_color).'" ';
					$attr .=' data-dots_active_bg_color="'.esc_attr($dots_active_bg_color).'" ';
					
					$attr .=' data-arrow_bg_color="'.esc_attr($arrow_bg_color).'" ';
					$attr .=' data-arrow_icon_color="'.esc_attr($arrow_icon_color).'" ';
					$attr .=' data-arrow_hover_bg_color="'.esc_attr($arrow_hover_bg_color).'" ';
					$attr .=' data-arrow_hover_icon_color="'.esc_attr($arrow_hover_icon_color).'" ';
					$attr .=' data-arrow_text_color="'.esc_attr($arrow_text_color).'" ';
					
				}
				
				$arrow_class='';
				if($arrows_style=='style-4' || $arrows_style=='style-5'){
					$arrow_class=$arrows_position;
				}
				
				$gallery_listing = '<div id="pt-plus-gallery-list" class="gallery-list '.esc_attr($isotope).'  gallery-'.esc_attr($gallery_style).' '.esc_attr($animated_class).' gallery-'.esc_attr($rand_no).' '.esc_attr($arrow_class).' '.esc_attr($el_class).' " '.$animated_attr.' '.$attr.' >';
				
				$gallery_listing .= '<div class="post-inner-loop gallery-'.esc_attr($rand_no).' ">';
				$i=1;
				
				if($layout=='metro' || $layout=='carousel'){
						$desktop_class=$tablet_class=$mobile_class='';
				}
				if(!empty($gallery_images)){
				$image_ids=explode(',',$gallery_images);
				$pretty_rel_random = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']"';
				foreach( $image_ids as $image_id ){
					$images='';
					$attachment = get_post($image_id);
					if($attachment){
						$image_alt=get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
						$caption=$attachment->post_excerpt;
						$description=$attachment->post_content;
						$full_image=wp_get_attachment_image_src($image_id,'full');
						$title=$attachment->post_title;
						if(isset($layout) && $layout=='grid'){		
							$images=wp_get_attachment_image_src($image_id,'tp-image-grid');								
						}else if(isset($layout) && $layout=='masonry'){
							$images=wp_get_attachment_image_src($image_id,'full');						
						}else if(isset($layout) && $layout=='carousel'){
							$images=wp_get_attachment_image_src($image_id,'tp-image-grid');						
						}else{
							$images=wp_get_attachment_image_src($image_id,'full');						
						}
						$gallery_listing .= '<div class="grid-item  metro-item'.esc_attr($i).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.esc_attr($animated_columns).'" >';
							if(!empty($gallery_style)){
							ob_start();
							include THEPLUS_PLUGIN_PATH. 'vc_elements/gallery/gallery-'.$gallery_style.'.php';
							$gallery_listing .= ob_get_contents();
							ob_end_clean();
							}
						$gallery_listing .= '</div>';
					}
					$i++;
				}
				}
				
				$gallery_listing .= '</div>';
				
				$gallery_listing .= '</div>';
				wp_reset_postdata();
				$css_rule='';
				$css_rule .= '<style >';
					$css_rule .= '.gallery-'.esc_js($rand_no).'.gallery-list .post-inner-loop .grid-item{padding : '.esc_js($column_padding).'; }';
					$css_rule .= '.gallery-'.esc_js($rand_no).'.gallery-'.esc_js($gallery_style).' .grid-item .gallery-hover-content:before{background : '.esc_js($box_bg_hover_color).'; }';
					$css_rule .= '.gallery-'.esc_js($rand_no).'.blog-'.esc_js($gallery_style).' .gallery-style-content{';
					if($column_shadow!=''){
						$css_rule .= '-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';';
					}
					$css_rule .= '}';
					$css_rule .='.gallery-'.esc_js($gallery_style).' .grid-item:hover .gallery-style-content{';
					if($column_hover_shadow!=''){
						$css_rule .= '-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';';
					}
					$css_rule .= '}';
					if($gallery_style=='style-2' || $gallery_style=='style-4'){
						$css_rule .='.gallery-'.esc_js($gallery_style).'.gallery-style-2 .title-wrap .media-title,.gallery-'.esc_js($gallery_style).'.gallery-style-4 .title-wrap .media-title{color:'.esc_js($title_color).';font-size:'.esc_js($title_font_size).';line-height:'.esc_js($title_line_height).';letter-spacing:'.esc_js($title_letter_space).';'.$title_style.'}';
						$css_rule .='.gallery-'.esc_js($gallery_style).'.gallery-style-2 .grid-item:hover .title-wrap .media-title,.gallery-'.esc_js($gallery_style).'.gallery-style-4 .grid-item:hover .title-wrap .media-title{color:'.esc_js($title_hover_color).';}';
						$css_rule .='.gallery-'.esc_js($gallery_style).'.gallery-style-2 .title-wrap .media-description{color:'.esc_js($desc_color).';font-size:'.esc_js($desc_font_size).';line-height:'.esc_js($desc_line_height).';}';
					}
					if($grayscale_img=='on'){
						$css_rule .='.gallery-'.esc_js($gallery_style).' .grid-item .gallery-image img,.gallery-'.esc_js($gallery_style).' .grid-item .gallery-style-content{
filter: url(data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg"><filter id=…cale"><feColorMatrix type="saturate" values="0"/></filter></svg>#grayscale);-webkit-filter: grayscale(1);filter: grayscale(1);filter: gray;-webkit-transition: -webkit-filter .5s;transition: -webkit-filter .5s;}';
						$css_rule .='.gallery-'.esc_js($gallery_style).' .grid-item:hover .gallery-style-content,.gallery-'.esc_js($gallery_style).' .grid-item:hover .gallery-image img{-webkit-filter: grayscale(0);filter: grayscale(0);filter: normal;}';
					}
if($parallax_column=='on'){
		if($desktop_column=='6'){
			if($layout=='grid'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-6:nth-child(even) {padding-top: 10%;}}';
			}
			if($layout=='masonry'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-6:nth-child(2) {padding-top: 10%;}}';
			}
		}
		if($desktop_column=='4'){
			if($layout=='grid'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-4:nth-child(3n+2) {padding-top: 10%;}}';
			}
			if($layout=='masonry'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-4:nth-child(2) {padding-top: 10%;}}';
			}
		}
		if($desktop_column=='3'){
			if($layout=='grid'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-3:nth-child(even) {padding-top: 10%;}}';
			}
			if($layout=='masonry'){
				$css_rule .= '@media (min-width:768px){.gallery-'.esc_js($rand_no).'.gallery-list .grid-item.vc_col-md-3:nth-child(2),.portfolio-listing.portfolio-'.esc_js($portfolio_style).' .grid-item.vc_col-md-3:nth-child(4) {padding-top: 10%;}}';
			}
		}
	}
				$css_rule .= '</style>';
				return $css_rule.$gallery_listing;
		}
		function init_tp_gallery_list(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Gallery Listing", 'pt_theplus'),
					"base" => "tp_gallery_list",
					"icon" => "tp-gallery-list",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Various Listing and Carousel Options', 'pt_theplus'),
					"params" => array(
						array(
							"type"        => "attach_images",
							"heading"     => esc_html__( "Gallery Images", "pt_theplus" ),
							"param_name"  => "gallery_images",
							"value"       => '',
						),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Gallery Style ', 'pt_theplus'), 
								'param_name'  => 'gallery_style',
								'simple_mode' => false,
								"admin_label" => true,
								'value' => 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/gallery_style/1.png'
									),									
									
									'style-2' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/gallery_style/2.png'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/gallery_style/3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/gallery_style/2.png'
									),
								),
							),
					   array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Listing Layout', 'pt_theplus'), 
								'param_name'  => 'layout',
								'simple_mode' => false,
								"admin_label" => true,
								'value' => 'grid',
								'options'     => array(
									'grid' => array(
										'tooltip' => esc_attr__('Grid Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/grid.jpg'
									),
									'masonry' => array(
										'tooltip' => esc_attr__('Masonry Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/masonry.jpg'
									),
									'metro' => array(
										'tooltip' => esc_attr__('Metro Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/metro.jpg'
									),
									'carousel' => array(
										'tooltip' => esc_attr__('Carousel Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/carousel.jpg'
									),
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
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							"param_name" => "animation_effects",
							"admin_label" => false,
							"value" => array(
								 esc_html__( 'No-animation', 'pt_theplus' )             => 'no-animation',
								esc_html__( 'FadeIn', 'pt_theplus' )             => 'transition.fadeIn',
								esc_html__( 'FlipXIn', 'pt_theplus' )            => 'transition.flipXIn',
							   esc_html__( 'FlipYIn', 'pt_theplus' )            => 'transition.flipYIn',
							   esc_html__( 'FlipBounceXIn', 'pt_theplus' )      => 'transition.flipBounceXIn',
							   esc_html__( 'FlipBounceYIn', 'pt_theplus' )      => 'transition.flipBounceYIn',
							   esc_html__( 'SwoopIn', 'pt_theplus' )            => 'transition.swoopIn',
							   esc_html__( 'WhirlIn', 'pt_theplus' )            => 'transition.whirlIn',
							   esc_html__( 'ShrinkIn', 'pt_theplus' )           => 'transition.shrinkIn',
							   esc_html__( 'ExpandIn', 'pt_theplus' )           => 'transition.expandIn',
							   esc_html__( 'BounceIn', 'pt_theplus' )           => 'transition.bounceIn',
							   esc_html__( 'BounceUpIn', 'pt_theplus' )         => 'transition.bounceUpIn',
							   esc_html__( 'BounceDownIn', 'pt_theplus' )       => 'transition.bounceDownIn',
							   esc_html__( 'BounceLeftIn', 'pt_theplus' )       => 'transition.bounceLeftIn',
							   esc_html__( 'BounceRightIn', 'pt_theplus' )      => 'transition.bounceRightIn',
							   esc_html__( 'SlideUpIn', 'pt_theplus' )          => 'transition.slideUpIn',
							   esc_html__( 'SlideDownIn', 'pt_theplus' )        => 'transition.slideDownIn',
							   esc_html__( 'SlideLeftIn', 'pt_theplus' )        => 'transition.slideLeftIn',
							   esc_html__( 'SlideRightIn', 'pt_theplus' )       => 'transition.slideRightIn',
							   esc_html__( 'SlideUpBigIn', 'pt_theplus' )       => 'transition.slideUpBigIn',
							   esc_html__( 'SlideDownBigIn', 'pt_theplus' )     => 'transition.slideDownBigIn',
							   esc_html__( 'SlideLeftBigIn', 'pt_theplus' )     => 'transition.slideLeftBigIn',
							   esc_html__( 'SlideRightBigIn', 'pt_theplus' )    => 'transition.slideRightBigIn',
							   esc_html__( 'PerspectiveUpIn', 'pt_theplus' )    => 'transition.perspectiveUpIn',
							   esc_html__( 'PerspectiveDownIn', 'pt_theplus' )  => 'transition.perspectiveDownIn',
							   esc_html__( 'PerspectiveLeftIn', 'pt_theplus' )  => 'transition.perspectiveLeftIn',
							   esc_html__( 'PerspectiveRightIn', 'pt_theplus' ) => 'transition.perspectiveRightIn',
							),
							"edit_field_class" => "vc_col-xs-6",
							'std' => 'transition.fadeIn'
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
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('MUST : Select Animation Type from above options either It will show blank. Waypoint Based animations are scroll based, and Stagger based are one by one column animation.','pt_theplus').'</span></span>'.esc_html__('Column Load Animation', 'pt_theplus')), 
							"param_name" => "animated_column_list",
							"value" => array(
								esc_html__("Select Options", "pt_theplus") => "",
								esc_html__("Waypoint Based Animation", "pt_theplus") => "columns",
								esc_html__("Stagger Based Animation", "pt_theplus") => "stagger",
							),
							"edit_field_class" => "vc_col-xs-6",
							'description' => '',
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Value of Stagger delay in milisecond. 1 sec = 1000 Milisecond.','pt_theplus').'</span></span>'.esc_html__('Animation Stagger', 'pt_theplus')),
							"param_name" => "animation_stagger",
							"value" => '150',
							"edit_field_class" => "vc_col-xs-6",
							"description" => "",
							"dependency" => array(
								"element" => "animated_column_list",
								"value" => array(
									"stagger",
								)
							),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Extra Settings', 'pt_theplus'),
							'param_name' => 'extra_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Grayscale image blank and whilte by turning on this option.','pt_theplus').'</span></span>'.esc_html__('Grayscale Gallery', 'pt_theplus')), 
							'param_name' => 'grayscale_img',
							'description' => '',
							'value' => 'off',
							'options' => array(
							'on' => array(
							'label' => '',
							'on' => 'Yes',
							'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-12",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add space between your columns by turning on this option.','pt_theplus').'</span></span>'.esc_html__('Column Space Option', 'pt_theplus')), 
							'param_name' => 'column_space',
							'description' => '',
							'value' => 'off',
							'options' => array(
							'on' => array(
							'label' => '',
							'on' => 'Yes',
							'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-6",
							),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Column Space here in Pixels. e.g. 10px, 20px etc.','pt_theplus').'</span></span>'.esc_html__('Column Space', 'pt_theplus')), 
							'param_name' => 'column_space_pading',
							'description' => '',
							'value' => '10px',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
							"element" => "column_space",
							"value" => array("on"),
							),			
						),
						array(
						'type' => 'pt_theplus_checkbox',
						'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Odd even Up Down columns by turning on this option.','pt_theplus').'</span></span>'.esc_html__('Up Down Column', 'pt_theplus')), 
						'param_name' => 'parallax_column',
						'description' => '',
						'value' => 'off',
						'options' => array(
							'on' => array(
								'label' => '',
								'on' => 'Yes',
								'off' => 'No',
							),
						),
						"dependency" => array(
							"element" => "layout",
							"value" => array("grid","masonry"),
						),
						"edit_field_class" => "vc_col-xs-6",
						),		
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__(' You can add Extra Class here to use for Customization Purpose.', 'pt_theplus') . '</span></span>' . esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6'
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Columns Setting', 'pt_theplus'),
							'param_name' => 'columns_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Content', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Desktop Columns", 'pt_theplus'),
							"param_name" => "desktop_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2',
								'12 column' => '1'
							),
							'std' => '3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Tablet Columns", 'pt_theplus'),
							"param_name" => "tablet_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2'
							),
							'std' => '6',
							"edit_field_class" => "vc_col-xs-4",
							
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Mobile Columns", 'pt_theplus'),
							"param_name" => "mobile_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2'
							),
							'std' => '12',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Title Setting', 'pt_theplus'),
							'param_name' => 'title_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Gallery&#39;s Title&#39;s Font size for using this option.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "title_font_size",
							"value" => '20px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Gallery&#39;s Title&#39;s Line Height for using this option.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "title_line_height",
							"value" => '25px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Gallery&#39;s Title&#39;s Letter Spacing for using this option.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "title_letter_space",
							"value" => '0px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color', 'pt_theplus')),
							'param_name' => 'title_color',
							"description" => "",
							'value' => '#fff',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title hover using this option.','pt_theplus').'</span></span>'.esc_html__('Title Hover Color', 'pt_theplus')),
							'param_name' => 'title_hover_color',
							"description" => "",
							'value' => '#fff',
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
							'value' => __('700','pt_theplus'),
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
							'text' => esc_html__('Description Setting', 'pt_theplus'),
							'param_name' => 'description_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for content using this option.','pt_theplus').'</span></span>'.esc_html__('Description Color', 'pt_theplus')),
							'param_name' => 'desc_color',
							"description" => "",
							'value' => '#fff',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Gallery&#39;s Content Font size for using this option.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "desc_font_size",
							"value" => '15px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Gallery&#39;s Content Line Height for using this option.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "desc_line_height",
							"value" => '20px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Columns Style', 'pt_theplus'),
							'param_name' => 'column_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Background Color', 'pt_theplus'),
							'param_name' => 'box_bg_color',
							"description" => "",
							'value' => 'rgba(0,0,0,0.5)',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Background Hover Color', 'pt_theplus'),
							'param_name' => 'box_bg_hover_color',
							"description" => "",
							'value' => 'rgba(0,0,0,0.7)',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),
						/*array(
							'type' => 'colorpicker',
							'heading' => __('Border Color', 'pt_theplus'),
							'param_name' => 'box_border_color',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Border Hover Color', 'pt_theplus'),
							'param_name' => 'box_border_hover_color',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),*/
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Column Box Shadow ', 'pt_theplus')),
							"param_name" => "column_shadow",
							"value" => '0px 0px 2px 0px rgba(0,0,0,0.25)',
							"description" => "",
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Column Hover Box Shadow', 'pt_theplus')),
							"param_name" => "column_hover_shadow",
							"value" => '0px 2px 15px rgba(0,0,0,0.17)',
							"description" => "",
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Carousel Setting', 'pt_theplus'),
							'param_name'		=> 'carousel_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Carousel', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
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
								'7 column' => '7',
								'8 column' => '8',
								'9 column' => '9',
								'10 column' => '10',
								'11 column' => '11',
								'12 column' => '12',
								),
							'std' =>'4',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
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
								'7 column' => '7',
								'8 column' => '8',
								'9 column' => '9',
								'10 column' => '10',
								'11 column' => '11',
								'12 column' => '12',
								),
							'std' =>'3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
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
								'7 column' => '7',
								'8 column' => '8',
								'9 column' => '9',
								'10 column' => '10',
								),
							'std' =>'2',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								__("One By One slide", "pt_theplus") => "1",
								__("Column Slide", "pt_theplus") => "2",
							),    
							"std" =>'1',
							"description" => "", 
							  "edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
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
									'style-9' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-6.jpg'
									),
									'style-10' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-5.jpg'
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
								"element" => "layout",
								"value" => array("carousel"),
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
					)
				));
			}
		}
	}
	new ThePlus_gallery_list;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_gallery_list'))
	{
		class WPBakeryShortCode_tp_gallery_list extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}