<?php
	// Clients List Elements
if(!class_exists("ThePlus_tm_list")){
	class ThePlus_tm_list{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_tm_list') );
			add_shortcode( 'tp_tm_list',array($this,'tp_tm_list_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_tm_list_scripts' ), 1 );
		}
		function tp_tm_list_scripts() {
			wp_register_style( 'theplus-teammember-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-teammember-style.css', false, '1.0.0' );//theplus team member list
		}
		function tp_tm_list_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'tm_style' => 'style-1',
					'alignment_img' =>'img-left',
					'text_align' =>'text-left',
					'order_by'=>'date',
					'post_sort'=>'DESC',
					'tm_category' =>'',
					'desktop_column'=>'3',
					'tablet_column'=>'6',
					'mobile_column'=>'12',
					'display_post' => '10',
					
					'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					'animated_column_list'=>'',
					'animation_stagger'=>'150',
					
					'text_color'=> '#252525',
					'bg_color'=> '#eeeeee',
					'title_font_size'=>'25px',
					'title_line_height'=>'29px',
					'title_use_theme_fonts'=>'custom-font-family',
					'title_font_family'=>'',
					'title_font_weight'=>'400',
					'title_google_fonts'=>'',
					
					'desig_text_color'=>'#4d4d4d',
					'desig_font_size'=>'15px',
					'desig_line_height'=>'19px',
					'desig_use_theme_fonts'=>'custom-font-family',
					'desig_font_family'=>'',
					'desig_font_weight'=>'400',
					'desig_google_fonts'=>'',
					
					'content_text_color'=>'#4d4d4d',
					'content_font_size'=>'14px',
					'content_line_height'=>'24px',
					'icon_color' =>'#4d4d4d',
					'icon_hover_color'=>'#252525',
					'icn_bg_color' =>'#ff214f',
					'icn_hvr_bg_color' =>'#28e9ff',
					'box_bg_color'=> '#eeeeee',
					'hover_bg_color'=>'rgba(0,0,0,0.50)',
					
					'layout' =>'grid',
					
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
					
					'content_hover_effects' =>'', 
					'hover_shadow_color' => 'rgba(0, 0, 0, 0.6)',
					'box_shadow' =>'',
					'hvr_box_shadow' =>'',
					'bd_clr' =>'',
					'bd_hvr_clr' =>'',
					
					'desc_on' =>'on',
					
					'column_space' =>'',
					'column_space_pading' => '10px',
					'el_class'=>'',
					), $atts ) );
					
					wp_enqueue_style( 'theplus-teammember-style');
					$post_name=pt_plus_team_member_post_name();
					$taxonomy=pt_plus_team_member_post_category();
					$args = array(
						'post_type' => $post_name,
						$taxonomy => $tm_category,		
						'orderby'	=>$order_by,
						'order'	=>$post_sort,
						'post_status' =>'publish',
						'posts_per_page'=>$display_post
					);
					
					$rand_no=rand(1000000, 1500000);
					$your_query = new WP_Query( $args );
					
					
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
				$animated_attr .='data-animate-type="'.esc_attr($animation_effects).'"';
				$animated_attr .=' data-animate-delay="'.esc_attr($animation_delay_time).'"';
				$animated_columns='';
				if($animated_column_list==''){
					$animated_columns='';
				}else if($animated_column_list=='columns'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="columns"';
				}else if($animated_column_list=='stagger'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="stagger"';
						$animated_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
				}
					$image_align='';
					if($alignment_img=='img-left'){
						$image_align='img-left';
						}else{
						$image_align='img-right';
					}
					$align_text='';
					if($text_align=='text-left'){
						$align_text='text-left';
						}else{
						$align_text='text-right';
					}
					
				if($desig_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $desig_google_fonts );
					$desig_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($desig_use_theme_fonts=='custom-font-family'){
					$desig_font_family='font-family:'.$desig_font_family.';font-weight:'.$desig_font_weight.';';
				}else{
					$desig_font_family='';
				}
					
					$desig_color = ' style="';
					if($desig_text_color != "") {
						$desig_color .='color:'.esc_attr($desig_text_color).';';
					}
					if($desig_line_height != "") {
						$desig_color .='line-height:'.esc_attr($desig_line_height).';';
					}
					if($desig_font_size != "") {
						$desig_color .='font-size:'.esc_attr($desig_font_size).';';
					}
					$desig_color .=$desig_font_family;
					$desig_color .= '"';
					
					$content_color = ' style="';
					if($content_text_color != "") {
						$content_color .='color:'.esc_attr($content_text_color).';';
					}
					if($content_font_size != "" && $content_line_height!='') {
						$content_color .='font-size:'.esc_attr($content_font_size).';';
						$content_color .='line-height:'.esc_attr($content_line_height).';';
					}	
					$content_color .= '"';

					
					$hover_bg_css = ' style="';
					if($bg_color != "" && $tm_style=='style-2') {
						$hover_bg_css .='-webkit-box-shadow: 0px 0px 0px 13px '.esc_attr($bg_color).' inset;-moz-box-shadow: 0px 0px 0px 13px '.esc_attr($bg_color).' inset;box-shadow: 0px 0px 0px 13px '.esc_attr($bg_color).' inset;';
					}
					$hover_bg_css .= '"';
					

					
					
					$data_attr ='';
					if($layout=='grid'){
						$data_attr .=' data-layout-type="fitRows" ';
						$isotope=' list-isotope ';
					}else if($layout=='carousel'){
						$isotope=' list-carousel-slick';
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
					$data_attr .=' data-id="tm-'.esc_attr($rand_no).'"';;
					
							
					if($column_space == 'on'){
						$column_padding=$column_space_pading;
					}else{
						$column_padding="0px";	
					}
					if($layout=='carousel'){
						$data_attr .=' data-show_arrows="'.esc_attr($show_arrows).'"';
						$data_attr .=' data-show_dots="'.esc_attr($show_dots).'"';
						$data_attr .=' data-show_draggable="'.esc_attr($show_draggable).'"';
						$data_attr .=' data-slide_loop="'.esc_attr($slide_loop).'"';
						$data_attr .=' data-slide_autoplay="'.esc_attr($slide_autoplay).'"';
						$data_attr .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
						$data_attr .=' data-steps_slide="'.esc_attr($steps_slide).'"';
						$data_attr .=' data-carousel_column="'.esc_attr($carousel_column).'"';
						$data_attr .=' data-carousel_tablet_column="'.esc_attr($carousel_tablet_column).'"';
						$data_attr .=' data-carousel_mobile_column="'.esc_attr($carousel_mobile_column).'"';
						$data_attr .=' data-dots_style="slick-dots '.esc_attr($dots_style).'" ';
						$data_attr .=' data-arrows_style="'.esc_attr($arrows_style).'" ';
						$data_attr .=' data-arrows_position="'.esc_attr($arrows_position).'" ';
						
						$data_attr .=' data-dots_border_color="'.esc_attr($dots_border_color).'" ';
						$data_attr .=' data-dots_bg_color="'.esc_attr($dots_bg_color).'" ';
						$data_attr .=' data-dots_active_border_color="'.esc_attr($dots_active_border_color).'" ';
						$data_attr .=' data-dots_active_bg_color="'.esc_attr($dots_active_bg_color).'" ';
						
						$data_attr .=' data-arrow_bg_color="'.esc_attr($arrow_bg_color).'" ';
						$data_attr .=' data-arrow_icon_color="'.esc_attr($arrow_icon_color).'" ';
						$data_attr .=' data-arrow_hover_bg_color="'.esc_attr($arrow_hover_bg_color).'" ';
						$data_attr .=' data-arrow_hover_icon_color="'.esc_attr($arrow_hover_icon_color).'" ';
						$data_attr .=' data-arrow_text_color="'.esc_attr($arrow_text_color).'" ';
						
					}
					$arrow_class='';
					if($arrows_style=='style-4' || $arrows_style=='style-5'){
						$arrow_class=$arrows_position;
					}
					if ($content_hover_effects == "float_shadow" || $content_hover_effects == "grow_shadow" || $content_hover_effects == "shadow_radial") {
						$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
						$hover_attr .= ' data-hover_shadow="' . esc_attr($hover_shadow_color) . '" ';
						$hover_attr .= ' data-content_hover_effects="' . esc_attr($content_hover_effects) . '" ';
					}
					$hover_class='';
					if ($content_hover_effects == "grow") {
						$hover_class .= 'content_hover_grow';
					} elseif ($content_hover_effects == "push") {
						$hover_class .= 'content_hover_push';
					} elseif ($content_hover_effects == "bounce-in") {
						$hover_class .= 'content_hover_bounce_in';
					} elseif ($content_hover_effects == "float") {
						$hover_class .= 'content_hover_float';
					} elseif ($content_hover_effects == "wobble_horizontal") {
						$hover_class .= 'content_hover_wobble_horizontal';
					} elseif ($content_hover_effects == "wobble_vertical") {
						$hover_class .= 'content_hover_wobble_vertical';
					} elseif ($content_hover_effects == "float_shadow") {
						$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_float_shadow';
					} elseif ($content_hover_effects == "grow_shadow") {
						$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_grow_shadow';
					} elseif ($content_hover_effects == "shadow_radial") {
						$hover_class .= '' . esc_attr($hover_uniqid) . ' content_hover_radial';
					}	
					
				$tm_list='<div class="tm_list tm-'.esc_attr($rand_no).' '.esc_attr($isotope).' '.esc_attr($el_class).' '.esc_attr($animated_class).' '.esc_attr($arrow_class).'" '.$animated_attr.' data-random="tm-'.esc_attr($rand_no).'" '.$data_attr.'>';

					$tm_list .= '<div class="post-inner-loop tm-'.esc_attr($rand_no).' ">';
					while ( $your_query->have_posts() ) {
						$your_query->the_post();
						$team_designation= get_post_meta( get_the_ID(), 'theplus_tm_designation', true );
						$team_email=get_post_meta( get_the_ID(), 'theplus_tm_email', true );
						$team_phno=get_post_meta( get_the_ID(), 'theplus_tm_num', true );
						$team_des= get_post_meta( get_the_ID(), 'theplus_tm_short_desc', true );
						$team_fac= get_post_meta( get_the_ID(), 'theplus_tm_face_link', true );
						$team_goo= get_post_meta( get_the_ID(), 'theplus_tm_googgle_link', true );
						$team_insta= get_post_meta( get_the_ID(), 'theplus_tm_insta_link', true );
						$team_twit= get_post_meta( get_the_ID(), 'theplus_tm_twit_link', true );
						$team_linkd= get_post_meta( get_the_ID(), 'theplus_tm_linked_link', true );

					
					$tm_list .='<div  class="grid-item pt-plus-tm-'.esc_attr($tm_style).' '.esc_attr($desktop_class).' '.esc_attr($tablet_class).' '.esc_attr($mobile_class).' '.esc_attr($animated_columns).' '.esc_attr($hover_class).'">';
					if($tm_style=='style-1'){
						$tm_list .= '<a href="' . esc_url(get_permalink()) . '" class="list-tm-box">';
					}else{
						$tm_list .= '<div class="list-tm-box '.esc_attr($image_align).'">';
					}
							if($tm_style=='style-2'){
								$tm_list .='<div class="vc_col-md-6  tm-image-6 ">';
							}
									$featured_bgimage = '';
								if($tm_style=='style-2'){
									if (has_post_thumbnail()) {
											$full_image=get_the_post_thumbnail_url(get_the_ID(),'full');
											$featured_bgimage ='style="background:url('.esc_url($full_image).') #f7f7f7;"';	
									}else{ 
												$featured_bgimage = pt_plus_loading_image_grid('','background');
									}
								}
									$tm_list .='<div class="tm-featured-image " >';
									
									if($tm_style =='style-2'){
									$tm_list .='<div class="tm-bg-featured-image " '.$featured_bgimage.'></div>';
									}
									if($tm_style!='style-1'){
										$tm_list .='<a href="' . esc_url(get_permalink()) . '" >';
									}
								/*	if($tm_style !='style-2'){*/
									if ( has_post_thumbnail() ) {
									$featured_image=get_the_post_thumbnail_url(get_the_ID(),'full');
									
									$tm_list .='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
							
									}else{ 
											$tm_list .=pt_plus_loading_image_grid(get_the_ID());
									}
									/*}*/
									if($tm_style!='style-1'){
										$tm_list .='</a>';
									}
									$tm_list .="</div>";
							if($tm_style=='style-2'){	
								$tm_list .="</div>";
							}
						if($tm_style=='style-2'){
								$tm_list .='<div class="vc_col-md-6  tm-content-6">';
							}
						$tm_list .='<div class="tm-list-title '.esc_attr($align_text).'" >';
							$tm_title ='<div class="tm-list-content">';
								$tm_title .='<div class="as-title" >';
								if($tm_style!='style-1'){
									$tm_title .='<a href="' . esc_url(get_permalink()) . '" >'.esc_html(get_the_title()).'</a>';
								}else{
									$tm_title .=esc_html(get_the_title());
								}
								$tm_title .='</div>';
								if(!empty($team_designation) && ($tm_style=='style-1' || $tm_style=='style-2' || $tm_style=='style-4' || $tm_style=='style-5')){
									$tm_title .='<div class="as-des" '.$desig_color.'>'.esc_html($team_designation).'</div>';
								}
							$tm_title .="</div>";
						
								$tm_social ='<ul class="team-soicial">';	
									if(!empty($team_twit)){
										$tm_social .='<li class="twitter"><a class="" href="'.esc_url($team_twit).'#" title="Twitter">';
										if($tm_style=='style-3' || $tm_style=='style-5' || $tm_style=='style-2'){
											$tm_social .='<span class="b"><i class="fa fa-twitter"></i></span>';
											}else{
											$tm_social .='<span class="t"><i class="fa fa-twitter"></i></span>';
											$tm_social .='<span class="b"><i class="fa fa-twitter"></i></span>';
										}
										$tm_social .='</a></li>';
									}
									if(!empty($team_fac)){
										$tm_social .='<li class="facebook"><a class="" href="'.esc_url($team_fac).'" title="Facebook">';
										if($tm_style=='style-3' || $tm_style=='style-5' || $tm_style=='style-2'){
											$tm_social .='<span class="b"><i class="fa fa-facebook"></i></span>';
											}else{
											$tm_social .='<span class="t"><i class="fa fa-facebook"></i></span>';
											$tm_social .='<span class="b"><i class="fa fa-facebook"></i></span>';
										}
										$tm_social .='</a></li>';
									}
									if(!empty($team_goo)){
										$tm_social .='<li class="google"><a class="" href="'.esc_url($team_goo).'" title="Google">';
										if($tm_style=='style-3' || $tm_style=='style-5' || $tm_style=='style-2'){
											$tm_social .='<span class="b"><i class="fa fa-google-plus"></i></span>';
											}else{
											$tm_social .='<span class="t"><i class="fa fa-google-plus"></i></span>';
											$tm_social .='<span class="b"><i class="fa fa-google-plus"></i></span>';
										}
										$tm_social .='</a></li>';
									}
									if(!empty($team_linkd)){
										$tm_social .='<li class="linkedin"><a class="" href="'.esc_url($team_linkd).'" title="Linkdin">';
										if($tm_style=='style-3' || $tm_style=='style-5' || $tm_style=='style-2'){
											$tm_social .='<span class="b"><i class="fa fa-linkedin-square" aria-hidden="true" ></i></span>';
											}else{
											$tm_social .='<span class="t"><i class="fa fa-linkedin-square" aria-hidden="true" ></i></span>';
											$tm_social .='<span class="b"><i class="fa fa-linkedin-square" aria-hidden="true" ></i></span>';
										}
										$tm_social .='</a></li>';
									}
									if(!empty($team_insta)){
										$tm_social .='<li class="instagram"><a href="'.esc_url($team_insta).'" title="Instagram">';
										if($tm_style=='style-3' || $tm_style=='style-5' || $tm_style=='style-2'){
											$tm_social .='<span class="b"><i class="fa fa-instagram" aria-hidden="true"></i></span>';
											}else{
											$tm_social .='<span class="t"><i class="fa fa-instagram" aria-hidden="true"></i></span>';
											$tm_social .='<span class="b"><i class="fa fa-instagram" aria-hidden="true"></i></span>';
										}
										$tm_social .='</a></li>';
									}		
								$tm_social .="</ul>";
						
								if($tm_style=='style-1'){
									$tm_list .=$tm_title;
									$tm_list .='<div class="tm-list-hover-content" >';
										$tm_list .='<div class="as-title" >'.esc_html(get_the_title()).'</div>';
									$tm_list .="</div>";
								}
								if($tm_style=='style-2'){
									$tm_title_2 ='<div class="tm-list-content">';
										if(!empty($team_designation)){
											$tm_title_2 .='<div class="as-des" '.$desig_color.'>'.esc_html($team_designation).'</div>';
										}
										$tm_title_2 .='<div class="as-title" >';
											$tm_title_2 .=esc_html(get_the_title());
										$tm_title_2 .='</div>';
									$tm_title_2 .="</div>";
									$tm_list .=$tm_title_2;
									if($desc_on =='on'){
										if(!empty($team_des)){
											$tm_list .= '<div class="team-desc" '.$content_color.'>'.esc_html($team_des).'</div>';
										}
									}
									if(!empty($team_phno)){
										$tm_list .= '<div class="team-tel"><span class="team-tel-icon" '.$content_color.'><i class="fa fa-phone" aria-hidden="true"></i></span>  <a href="tel:'.esc_attr($team_phno).'" '.$content_color.'>'.esc_html($team_phno).'</a></div>';
									}
									if(!empty($team_email)){
										$tm_list .= '<div class="team-mail"><span class="team-mail-icon" '.$content_color.'><i class="fa fa-envelope" aria-hidden="true"></i>
				 </span><a href="mailto:'.esc_attr($team_email).'" '.$content_color.'>'.esc_html($team_email).'</a></div>';
									}
									
									$tm_list .= $tm_social;
								}
								if($tm_style=='style-3'){
									$tm_list .=$tm_title;
									$tm_list .='<div class="tm-list-hover-content">';						
										if(!empty($team_designation)){
											$tm_list .='<div class="as-des" '.$desig_color.'>'.esc_html($team_designation).'</div>';
										}
									$tm_list .="</div>";
									$tm_list .= $tm_social;
								}
							
								if($tm_style=='style-4'){
									$tm_list .=$tm_title;
									$tm_list .='<div class="tm-block-content">';
									if(!empty($team_email)){
										$tm_list .= '<div class="team-link"><a href="mailto:'.esc_attr($team_email).'" '.$content_color.'>'.esc_html__('Email : ','pt_theplus').esc_html($team_email).'</a></div>';
									}
									if(!empty($team_phno)){
										$tm_list .= '<div class="team-link"><a href="tel:'.esc_attr($team_phno).'" '.$content_color.'>'.esc_html__('Tel : ','pt_theplus').esc_html($team_phno).'</a></div>';
									}
									$tm_list .= $tm_social;
									$tm_list .="</div>";
								}
								
								if($tm_style=='style-5'){
									$tm_list .=$tm_title;
									if(!empty($team_phno)){
										$tm_list .= '<div class="team-tel"><span class="team-tel-icon" '.$content_color.'><i class="fa fa-phone" aria-hidden="true"></i></span>  <a href="tel:'.esc_attr($team_phno).'" '.$content_color.'>'.esc_html($team_phno).'</a></div>';
									}
									if(!empty($team_email)){
										$tm_list .= '<div class="team-mail"><span class="team-mail-icon" '.$content_color.'><i class="fa fa-envelope" aria-hidden="true"></i>
				 </span><a href="mailto:'.esc_attr($team_email).'" '.$content_color.'>'.esc_html($team_email).'</a></div>';
									}
									$tm_list .= $tm_social;
								}
							
						$tm_list .="</div>";
						if($tm_style=='style-2'){
						$tm_list .= '</div>';
						}
					if($tm_style=='style-1'){
						$tm_list .= '</a>';
					}else{
						$tm_list .= '</div>';
					}
					$tm_list .="</div>";
					}
					$tm_list .="</div>";
				$tm_list .="</div>";
					wp_reset_postdata();
					$css_rule='';
					$css_rule .= '<style >';
					$css_rule .= '.tm-'.esc_js($rand_no).' .post-inner-loop .grid-item{padding:'.esc_js($column_padding).';}';
					$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-1 .tm-list-hover-content .as-title,.tm-'.esc_js($rand_no).' .tm-list-content .as-title a,.tm-'.esc_js($rand_no).' .tm-list-content .as-title{color:'.esc_js($text_color).';font-size:'.esc_js($title_font_size).';line-height:'.esc_js($title_line_height).';'.esc_js($title_font_family).'}';
					if(!empty($bg_color)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-1 .list-tm-box .tm-list-content,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-1 .list-tm-box .tm-list-hover-content{background:'.esc_js($bg_color).';}.tm-'.esc_js($rand_no).' .pt-plus-tm-style-1 .list-tm-box:hover .tm-list-title:before{border-color:'.esc_js($bg_color).';}';
					}
					if(!empty($bd_hvr_clr)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box:hover {border: 1px solid '.esc_js($bd_hvr_clr).'}';
					}
					if(!empty($bd_clr)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box {border: 1px solid '.esc_js($bd_clr).'}';
					}
					if(!empty($hover_bg_color)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-1 .list-tm-box:hover .tm-list-title{ background: '.esc_js($hover_bg_color).';}';
					}
					if(!empty($box_bg_color)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-2 .list-tm-box,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .tm-list-title,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-4 .list-tm-box,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-5 .list-tm-box{background:'.esc_js($box_bg_color).';}.tm-'.esc_js($rand_no).' .pt-plus-tm-style-2 .tm-featured-image:before{ -webkit-box-shadow: 0px 0px 70px 200px '.esc_js($box_bg_color).'; -moz-box-shadow: 0px 0px 70px 200px '.esc_js($box_bg_color).'; box-shadow: 0px 0px 70px 200px '.esc_js($box_bg_color).';}';
					}
					if(!empty($icon_color)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-4 .team-soicial li a,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-5 .team-soicial li a,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-2 .team-soicial li a{color:'.esc_js($icon_color).';border-color:'.esc_js($icon_color).';}.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial{border-color:'.esc_js($icon_color).';}';
					}
					if(!empty($icon_hover_color)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li:hover a,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-2 .team-soicial li a:hover,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a:hover,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-4 .team-soicial li a:hover,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-5 .team-soicial li a:hover{color:'.esc_js($icon_hover_color).';border-color:'.esc_js($icon_hover_color).';}';
					}
					$css_rule .= '.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a span ,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-5 .team-soicial li a span {background:'.esc_js($icn_bg_color).';}.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a:hover span::after ,.tm-'.esc_js($rand_no).' .pt-plus-tm-style-5 .team-soicial li a:hover span {background:'.esc_js($icn_hvr_bg_color).';}.tm-'.esc_js($rand_no).' .pt-plus-tm-style-3 .team-soicial li a:hover span::after{z-index:1;background:'.esc_js($icn_hvr_bg_color).';}';
					if(!empty($box_shadow)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box {-webkit-box-shadow: '.esc_js($box_shadow).';-moz-box-shadow: '.esc_js($box_shadow).';box-shadow: '.esc_js($box_shadow).';}';
					}else{
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box {-webkit-box-shadow:none;-moz-box-shadow: none;box-shadow: none;}';
					}
					if(!empty($hvr_box_shadow)){
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box:hover {-webkit-box-shadow: '.esc_js($hvr_box_shadow).';-moz-box-shadow: '.esc_js($hvr_box_shadow).';box-shadow: '.esc_js($hvr_box_shadow).';}';
					}else{
						$css_rule .= '.tm-'.esc_js($rand_no).' .list-tm-box:hover {-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}';
					}
					$css_rule .= '</style>';
					return $css_rule.$tm_list;
		}
		function init_tp_tm_list(){
			if(function_exists("vc_map"))
			{
			$taxonomy=pt_plus_team_member_post_category();
				$teammember_post=pt_plus_get_option('post_type','team_member_post_type');
						if((!isset($teammember_post) || $teammember_post=='') || (!empty($teammember_post) && $teammember_post=='disable')){
							$custom_post_type=array(
									"type" => "pt_theplus_post_notice",
									"heading" => "",
									"param_name" => "custom_post_notice",
									"description" => "",
									"admin_label" => false,
									"show_notice" => 'yes',
									"value" => '',
								);
						}else{
							$custom_post_type=array(
									"type" => "pt_theplus_post_notice",
									"heading" => "",
									"param_name" => "custom_post_notice",
									"description" => "",
									"admin_label" => false,
									"show_notice" => '',
									"value" => '',
								);
						}
				vc_map(array(
					"name" => __("Team Member List", "pt_theplus"),
					"base" => "tp_tm_list",
					"icon" => "tp-tm-list",
					"description" => esc_html__('Various Listing and Carousel Options', 'pt_theplus'),
					"category" => __("The Plus", "pt_theplus"),
					"params" => array(
						$custom_post_type,
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Team Member Listing', 'pt_theplus'),
								'param_name'  => 'tm_style',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/team-member/style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/team-member/style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/team-member/style-3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/team-member/style-4.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/team-member/style-5.jpg'
									)
								),
								
							),
							array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Listing Layout', 'pt_theplus'),
								'param_name'  => 'layout',
								"admin_label" => true,
								'simple_mode' => false,
								'value' => 'grid',
								'options'     => array(
									'grid' => array(
										'tooltip' => esc_attr__('Grid Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/grid.jpg'
									),
									'carousel' => array(
										'tooltip' => esc_attr__('Carousel Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/carousel.jpg'
									),
								),
							),
							array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Image alignment Left or Roght.','pt_theplus').'</span></span>'.esc_html__('Alignment', 'pt_theplus')), 
							"param_name"  => "alignment_img",
							"admin_label" => false,
							"value"       => array(
								'Left' => 'img-left',
								'Right' => 'img-right',
								),
							'std' =>'image-left',
							"dependency" => array(
								"element" => "tm_style",
								"value" => array("style-2"),
							), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select Image alignment Left or Roght.','pt_theplus').'</span></span>'.esc_html__('Text Alignment', 'pt_theplus')), 
							"param_name"  => "text_align",
							"admin_label" => false,
							"value"       => array(
								'Left' => 'text-left',
								'Right' => 'text-right',
								),
							'std' =>'text-left',
							"dependency" => array(
								"element" => "tm_style",
								"value" => array("style-2"),
							), 
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Team Member Setting', 'pt_theplus'),
							'param_name'		=> 'team_member_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
							'type' => 'pt_theplus_taxonomy_multicheck',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose categories from which you want to show posts by marking one or multiple.','pt_theplus').'</span></span>'.esc_html__('Choose Categories', 'pt_theplus')),
							'param_name' => 'tm_category',
							'taxonomy' => $taxonomy,
							'edit_field_class' => 'vc_column vc_col-sm-12 pt_theplus-taxonomy-multicheck',
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Maximum Number of Posts using this option. Default Value is Unlimited. Don&#39;t Enter anything if you want to show all posts.','pt_theplus').'</span></span>'.esc_html__('Maximum Posts', 'pt_theplus')),
							"param_name" => "display_post",
							"value" => '10',
							'group' => esc_attr__('Content', 'pt_theplus'), 
							"description" => ""
						),
						array(
							"type" => "dropdown",
							"heading" => __("Order By", 'pt_theplus'),
							"param_name" => "order_by",
							"value" => array(
								'Date' => 'date',
								'Order by post ID' => 'ID',
								'Title' => 'title',
								'Last modified date' => 'modified',
								'Random order' => 'rand'
							),
							'std' => 'date',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => __("Sorting Order", 'pt_theplus'),
							"param_name" => "post_sort",
							"value" => array(
								'Descending' => 'DESC',
								'Ascending' => 'ASC'
							),
							'std' => 'DESC',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Content', 'pt_theplus')
						),	
						array(
							"type" => "dropdown",
							"heading" => __("Desktop Columns", 'pt_theplus'),
							"param_name" => "desktop_column",
							"admin_label" => true,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2',
								'12 column' => '1'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
								)
							),
							'std' => '3',
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => __("Tablet Columns", 'pt_theplus'),
							"param_name" => "tablet_column",
							"admin_label" => true,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
								)
							),
							'std' => '2',
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => __("Mobile Columns", 'pt_theplus'),
							"param_name" => "mobile_column",
							"admin_label" => true,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
								)
							),
							'std' => '1',
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Title Options', 'pt_theplus'),
							'param_name' => 'title_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'style',
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "text_color",
							"value" => '#252525',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							"param_name" => "bg_color",
							"value" => '#eeeeee',
							"description" => '',
							"dependency" => array(
								"element" => "tm_style",
								"value" => array("style-1"),
							), 
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "title_font_size",
							"value" => '25px',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "title_line_height",
							"value" => '29px',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6"
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
								'group' => esc_attr__('style', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'title_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'title_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('style', 'pt_theplus'),	
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
								'group' => esc_attr__('style', 'pt_theplus'),	
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Designation Options', 'pt_theplus'),
							'param_name' => 'desig_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('style', 'pt_theplus'),	
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font color using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "desig_text_color",
							"value" => '#4d4d4d',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "desig_font_size",
							"value" => '15px',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "desig_line_height",
							"value" => '19px',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Designation Custom font family', 'pt_theplus'),
								'param_name' => 'desig_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('style', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'desig_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'desig_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'desig_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('style', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'desig_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'desig_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'desig_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('style', 'pt_theplus'),	
						),
						 array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Content Options', 'pt_theplus'),
							'param_name' => 'content_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'style',
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-2","style-3","style-5"
								)
							)
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font color using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "content_text_color",
							"value" => '#4d4d4d',
							"description" => "",
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-2","style-3","style-5"
								)
							)
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "content_font_size",
							"value" => '14px',
							"description" => "",
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-2","style-3","style-5"
								)
							)
						),
						array(
							"type" => "textfield",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "content_line_height",
							"value" => '24px',
							"description" => "",
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-2","style-3","style-5"
								)
							)
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Social Icon Options', 'pt_theplus'),
							'param_name' => 'social_icon_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => 'style',
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
								   "style-2", "style-3","style-4","style-5"
								)
							)
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon color using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
							"param_name" => "icon_color",
							"value" => '#4d4d4d',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-2","style-3","style-4","style-5"
								)
							)
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon hover color using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Hover Color', 'pt_theplus')),
							"param_name" => "icon_hover_color",
							"value" => '#252525',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
								   "style-2","style-3","style-4","style-5"
								)
							)
						),
						 array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							"param_name" => "icn_bg_color",
							"value" => '#ff214f',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-3","style-5"
								)
							)
						),
						 array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Hover Background Color', 'pt_theplus')),
							"param_name" => "icn_hvr_bg_color",
							"value" => '#28e9ff',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									 "style-3","style-5"
								)
							)
						),
						
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Box Setting', 'pt_theplus'),
							'param_name'		=> 'boxshadow_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('style', 'pt_theplus'), 
						),
						
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							"param_name" => "box_bg_color",
							"value" => '#eeeeee',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Hover Background Color', 'pt_theplus')),
							"param_name" => "hover_bg_color",
							"value" => 'rgba(0,0,0,0.50)',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "tm_style",
								"value" => array(
									"style-1"
								)
							)
						),
						 array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							"param_name" => "bd_clr",
							"value" => '',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
						),
						 array(
							"type" => "colorpicker",
							"group" => 'style',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for hover background color using this option.','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
							"param_name" => "bd_hvr_clr",
							"value" => '',
							"description" => "",
							"edit_field_class" => "vc_col-xs-6",
						),
						
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Box Shadow ', 'pt_theplus')),
							'param_name' => 'box_shadow',
							'value' => '',
							'group' => 'style',
							'edit_field_class'	=> 'vc_col-sm-6',
							'description' =>'',

							),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Hover Box Shadow ', 'pt_theplus')),
							'param_name' => 'hvr_box_shadow',
							'edit_field_class'	=> 'vc_col-sm-6',
							'value' => '',
							'group' => 'style',
							'description' =>'',

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
							'description' => "",
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
							'description' => "",
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
							'description' => "",
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
							'description' => "",
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
							'description' => "",
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
							__("One Column", "pt_theplus") => "1",
							__("All Visible Columns", "pt_theplus") => "2",
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
									'style-10' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-5.jpg'
									),
									'style-9' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-6.jpg'
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
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can turn on or off description of team member','pt_theplus').'</span></span>'.esc_html__('Description ', 'pt_theplus')), 
							'param_name' => 'desc_on',
							'description' => '',
							'value' => 'on',
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
							"type" => "dropdown",
						   "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This Effects will be applied when you hover on this section.','pt_theplus').'</span></span>'.esc_html__('Content Hover Effects', 'pt_theplus')),
							"param_name" => "content_hover_effects",
							"value" => array(
								esc_html__('Select Hover Effect', 'pt_theplus') => '',
								esc_html__('Grow', 'pt_theplus') => 'grow',
								esc_html__('Push', 'pt_theplus') => 'push',
								esc_html__('Bounce In', 'pt_theplus') => 'bounce-in',
								esc_html__('Float', 'pt_theplus') => 'float',
								esc_html__('wobble horizontal', 'pt_theplus') => 'wobble_horizontal',
								esc_html__('Wobble Vertical', 'pt_theplus') => 'wobble_vertical',
								esc_html__('Float Shadow', 'pt_theplus') => 'float_shadow',
								esc_html__('Grow Shadow', 'pt_theplus') => 'grow_shadow',
								esc_html__('Shadow Radial', 'pt_theplus') => 'shadow_radial'
							),
							"description" => '',
						),
						array(
							'type' => 'colorpicker',
							'heading' => esc_html__('Shadow Color', 'pt_theplus'),
							'param_name' => 'hover_shadow_color',
							'value' => 'rgba(0, 0, 0, 0.6)',
							'edit_field_class' => 'vc_col-sm-6',
							'description' => '',
							'dependency' => array(
								'element' => 'content_hover_effects',
								'value' => array(
									'float_shadow',
									'grow_shadow',
									'shadow_radial'
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
							"value" => '50',
							"edit_field_class" => "vc_col-xs-6",
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
							'heading' => __('Columns between Space', 'pt_theplus'),
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
							'heading' => __('Columns Space', 'pt_theplus'),
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
							"type" => "textfield",
							
							"heading" => __("Extra Class Name", 'pt_theplus'),
							"param_name" => "el_class",
							"edit_field_class" => "vc_col-xs-6",
							"value" => '',
							"description" => ""
						)
					)
				));
			}
		}
	}
	new ThePlus_tm_list;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_tm_list'))
	{
		class WPBakeryShortCode_tp_tm_list extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}