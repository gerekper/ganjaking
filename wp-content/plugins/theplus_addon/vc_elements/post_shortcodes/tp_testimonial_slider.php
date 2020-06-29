<?php 
// Testimonial Slider Elements
if(!class_exists("ThePlus_testimonial_slider")){
	class ThePlus_testimonial_slider{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_testimonial_slider') );
			add_shortcode( 'tp_testimonial_slider',array($this,'tp_testimonial_slider_shortcode'));
		}
		
		function tp_testimonial_slider_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'testimonial_title' =>'Clients Feedback',  
					'title_font' =>'30px',
					'title_line_height' =>'1.4',
					'title_spacing' =>'1px',
					'ts_color' =>'#121212',
					'title_color'=> '#bd9128',
					'title_use_theme_fonts'=>'custom-font-family',
					'title_font_family'=>'',
					'title_font_weight'=>'400',
					'title_google_fonts'=>'',
					
					'back_color' =>'#121212',
					'border_color'=>'#f6f6f6',
					'back3_color' =>'#121212',
					'author_name' =>'#121212',
					'author_size' =>'18px',
					'author_line_height' =>'1.4',
					'author_spacing' =>'1px',
					
					'color'=> '#222',
					
					'mini_height'=>'300',
					'test_style' =>'style_1',
					'con_clr' =>'',
					'content_size' =>'24px',
					'content_line_height' =>'1.4',
					'content_spacing' =>'1px',
					'content_use_theme_fonts'=>'custom-font-family',
					'content_font_family'=>'',
					'content_font_weight'=>'400',
					'content_google_fonts'=>'',
					'bdr_clr' =>'#222222',
					
					'display_category' => '',
					'display_post'=>'-1',
					'order_by'=>'date',
					'post_sort'=>'DESC',
					
					'testimonial_align' => 'left',
					'web_color' =>'#121212',
					'web_size' =>'14px',
					'web_line_height' =>'1.4',
					'web_spacing' =>'1px',
					'desig_color' =>"#121212",
					'desig_line_height' =>'1.4',
					'desig_spacing' =>'1px',
					'web_style' =>'',
					'desig_font_color' =>'#000',
					'desig_size' =>'16px',
					
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
					'carousel_column'=>'1',
					'carousel_tablet_column'=>'1',
					'carousel_mobile_column'=>'1',
					
					'dots_border_color'=>'#000',
					'dots_bg_color'=>'#fff',
					'dots_active_border_color'=>'#000',
					'dots_active_bg_color'=>'#000',
					
					'arrow_bg_color'=>'#c44d48',
					'arrow_icon_color'=>'#fff',
					'arrow_hover_bg_color'=>'#fff',
					'arrow_hover_icon_color'=>'#c44d48',
					'arrow_text_color'=>'#fff',
					'el_class'=>'',
					
					'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					), $atts ) );
					$margin ="0";
					$rand_no=rand(1000000, 1500000);

					$text_alignment='';
					
					$style = ' style="';
					if($color != "") {
						$style .='color:'.esc_attr($color).';';
						if($test_style == 'style_7'){
								if($back_color != "") {
									$style .= 'background-color:'.esc_attr($back_color).';';
								}
								if($border_color != "") {
									$style .= 'border-color:'.esc_attr($border_color).';';
								}
							}
					}
					$style .= '";';
					
					if($mini_height!= "") {
						$height ='min-height:'.esc_attr($mini_height).'px;';
					}
					
					if($content_use_theme_fonts=='google-fonts'){
						$text_font_data = pt_plus_getFontsData( $content_google_fonts );
						$content_font_family = pt_plus_googleFontsStyles( $text_font_data );  
						$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
					}elseif($content_use_theme_fonts=='custom-font-family'){
						$content_font_family='font-family:'.$content_font_family.';font-weight:'.$content_font_weight.';';
					}else{
						$content_font_family='';
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
					$p_style = ' style="';
					if($con_clr != "") {
						$p_style .='color:'.esc_attr($con_clr).';';
					}
					if($content_size != "") {
						$p_style .='font-size:'.esc_attr($content_size).';';
					}
					if($content_line_height != "") {
						$p_style .='line-height:'.esc_attr($content_line_height).';';
					}
					if($content_spacing != "") {
						$p_style .='letter-spacing:'.esc_attr($content_spacing).';';
					}
					$p_style .=$content_font_family;
					$p_style .= '";';
					
					$icn_style = ' style="';
					if($bdr_clr != "") {
						$icn_style .='border-color:'.esc_attr($bdr_clr).';';
					}
					$icn_style .= '";';
					
					
					$title_style = ' style="';
					if($author_name != "") {
						$title_style .='color:'.esc_attr($author_name).';';
					}
					if($author_size != "") {
						$title_style .='font-size:'.esc_attr($author_size).';';
					}
					if($author_line_height != "") {
						$title_style .='line-height:'.esc_attr($author_line_height).';';
					}
					if($author_spacing != "") {
						$title_style .='letter-spacing:'.esc_attr($author_spacing).';';
					}
					$title_style .= $title_font_family;
					$title_style .= '";';
					
					$web_style = ' style="';
					if($web_color != "") {
						$web_style .='color:'.esc_attr($web_color).';';
					}
					if($web_size != "") {
						$web_style .='font-size:'.esc_attr($web_size).';';
					}
					if($web_line_height != "") {
						$web_style .='line-height:'.esc_attr($web_line_height).';';
					}
					if($web_spacing!= "") {
						$web_style .='letter-spacing:'.esc_attr($web_spacing).';';
					}
					$web_style .= '";';

					
					
					$ts_title = ' style="';
					if($title_font != "") {
						$ts_title .='font-size:'.esc_attr($title_font).';';
					}
					if($ts_color != "") {
						$ts_title .='color:'.esc_attr($ts_color).';';
					}
					if($title_spacing != "") {
						$ts_title .='letter-spacing:'.esc_attr($title_spacing).';';
					}
					if($title_line_height != "") {
						$ts_title .='line-height:'.esc_attr($title_line_height).';';
					}
					$ts_title .= $title_font_family;
					$ts_title .= '";';
					
					$desgnation_back = ' style="';
					if($desig_color != "") {
						$desgnation_back .='background:'.esc_attr($desig_color).';';
					}	
					$desgnation_back .= '";';
					
					$desg_font = ' style="';
					if($desig_size != "") {
						$desg_font .='font-size:'.esc_attr($desig_size).';';
					}
					if($desig_font_color != "") {
						$desg_font .='color:'.esc_attr($desig_font_color).';';
					}
					if($desig_spacing != "") {
						$desg_font .='letter-spacing:'.esc_attr($desig_spacing).';';
					}
					if($desig_line_height != "") {
						$desg_font .='line-height:'.esc_attr($desig_line_height).';';
					}
					$desg_font .= $title_font_family;
					$desg_font .= '";';
					
					if($test_style=="style_1"){
						$style_class="style-1" ;
					}else if($test_style=="style_2"){
						$style_class="style-2" ;
					}else if($test_style=="style_3"){
						$style_class="style-3" ;
					}else if($test_style=="style_4"){
						$style_class="style-4" ;
					}else if($test_style=="style_5"){
						$style_class="style-5" ;
					}else if($test_style=="style_6"){
						$style_class="style-6" ;
					}else if($test_style=="style_7"){
						$style_class="style-7" ;
					}

				if($testimonial_align=='left'){
				$offset_class ='vc_col-sm-6';
				}elseif($testimonial_align=='right'){
				$offset_class='vc_col-sm-offset-6 vc_col-sm-6';
				}elseif($testimonial_align=='center'){
				$offset_class='vc_col-sm-offset-3 vc_col-sm-6';
				}

				if($testimonial_align=='left'){
				$nav_offset ='vc_col-sm-offset-5 vc_col-sm-7 margin-right-test text-left';
				$title_align = 'text-left';
				}
				if($testimonial_align=='right'){
				$nav_offset ='vc_col-sm-7 margin-left-test text-right';
				$title_align = 'text-right';
				}
				if($testimonial_align=='center'){
				$nav_offset ='vc_col-sm-offset-3 vc_col-sm-6 margin-center-test text-center';
				$title_align = 'text-center';
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
				$post_name=pt_plus_testimonial_post_name();
				$taxonomy_name=pt_plus_testimonial_post_category();
					$args = array(
					'post_type' => $post_name,
					$taxonomy_name => $display_category,
					'posts_per_page' => $display_post,
					'orderby'	=>$order_by,
					'post_status' =>'publish',
					'order'	=>$post_sort
					);
					$testi_qry=new WP_Query($args);
					
					$attr='';
					
						$attr .=' data-id="tm-'.esc_attr($rand_no).'"';
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
						
					
					
						$arrow_class='';
					if($arrows_style=='style-4' || $arrows_style=='style-5'){
						$arrow_class=$arrows_position;
					}
					
						$bg_style6 = ' style="';
							if($test_style == 'style_6'){
								if($back_color != "") {
									$bg_style6 .= 'background-color:'.esc_attr($back_color).';';
								}
							}
						$bg_style6 .= '";';
								
					$testimonials ='<div class="pt-plus-testimonial-slide list-carousel-slick tm-'.esc_attr($rand_no).' '.esc_attr($arrow_class).' '.esc_attr($animated_class).' '.esc_attr($el_class).'" data-tmslide-unique="testi-'.esc_attr($rand_no).'"  data-testimonial-style="'.esc_attr($test_style).'" data-margin="'.esc_attr($margin).'" data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" '.$attr.' >';
					 
					if($test_style == 'style_5'){		
						 $testimonials .='<div class="vc_row">';
						   $testimonials .='<div class="vc_col-md-12 vc_col-sm-12">';
						 
						 $testimonials.='<div class="pt-plus-testi-nav nav-'.esc_attr($test_style).' '.esc_attr($arrow_class).'  pt-plus-testi-nav-'.esc_attr($rand_no).'" data-tmnav-unique="pt-plus-testi-nav-'.esc_attr($rand_no).'"   data-style="'.esc_attr($test_style).'">';
						 $testimonials .='<div class="testi-inner-loop">';
						
						if($testi_qry->have_posts()) :
								while($testi_qry->have_posts()) : $testi_qry->the_post(); 
									$testimonial_text = get_post_meta(get_the_id(), 'theplus_testimonial_author_text', true);
									$testimonial_website= get_post_meta(get_the_id(), 'theplus_testimonial_website_url', true);
									$post_bg = get_post_meta(get_the_id(), 'theplus_testimonial_bg_img', true);
									$test_designation = get_post_meta(get_the_id(), 'theplus_testimonial_designation', true);
									
								if ( has_post_thumbnail() ) {
									$thumbnail=get_the_post_thumbnail($testi_qry->ID, 'ts-image-grid', array('title' => ''));
								}else{
									$thumbnail ='';
								}
								
								
									$testimonials.='<a class="url" >';
										$testimonials.='<div class="pt-plus-testimonials-item ">';				 
											$testimonials.='<div class="testimonails-style-img  text-center ">';   
																		  $testimonials .= $thumbnail;
											 $testimonials.='</div>';
											 
										 $testimonials.='</div>';
									$testimonials.=' </a>';
								
								endwhile;
							endif;
							wp_reset_postdata();
								$testimonials.='</div>';
							$testimonials.='</div>';
						$testimonials.='</div>';
						$testimonials.='</div>';
							 }
					if($test_style== 'style_4'){
						  $testimonials .='<div class="vc_row">';
						   $testimonials .='<div class="'.esc_attr($offset_class).'">';
							 }
					if($test_style== 'style_4' || $test_style== 'style_5'){
						$testimonials .='<div class=" pt-plus-testimonial-slide-'.esc_attr($style_class).' testi-'.esc_attr($rand_no).'"  '.$style.'>';
						 $testimonials .='<div class="pt-plus-testi-nav-loop">';
					}else{
						
						$testimonials .='<div class="pt-plus-testimonial-slide-'.esc_attr($style_class).' testi-'.esc_attr($rand_no).'" '.$style.' >';
						$testimonials .='<div class="post-inner-loop">';
					}
						 
							if($testi_qry->have_posts()) :
							
								while($testi_qry->have_posts()) : $testi_qry->the_post(); 
									$testimonial_text = get_post_meta(get_the_id(), 'theplus_testimonial_author_text', true);
									$testimonial_website= get_post_meta(get_the_id(), 'theplus_testimonial_website_url', true);
									$post_bg = get_post_meta(get_the_id(), 'theplus_testimonial_bg_img', true);
									$test_designation = get_post_meta(get_the_id(), 'theplus_testimonial_designation', true);
									$main_css = ' style="';
									if($test_style == 'style_1' || $test_style == 'style_2' || $test_style == 'style_3' ){
										if($mini_height != "") {
											$main_css .='min-height:'.esc_attr($mini_height).'px;';
										}
									}
									if($test_style == 'style_4'){
										if($mini_height != "") {
											$main_css .='height:'.esc_attr($mini_height).'px;';
											
										}
										if($back_color != "") {
										$main_css .= 'background-color:'.esc_attr($back_color).';';
										}
									}
									
									if($test_style == 'style_1' || $test_style == 'style_2' || $test_style == 'style_3'){
										if($post_bg != "") {
											$main_css .= 'background:url('.esc_url($post_bg).');';
										}
									}
									if($test_style == 'style_3'){
										if($back_color != "") {
											$main_css .= 'background-color:'.esc_attr($back_color).';';
											}
									}
									if($post_bg == "") {
									/*	$main_css .= 'background-color:'.$back_color.';';*/
										}
									$main_css .= '";';
								if ( has_post_thumbnail() ) {
									$thumbnail=get_the_post_thumbnail($testi_qry->ID, 'thumbnail', array('title' => ''));
								}else{
									$thumbnail ='';
								}
							
							if($test_style!="style_6"){
							$testimonials.='<div class="pt-plus-testimonial-item pt-plus-testimonial-item-'.esc_attr($style_class).'" '.$main_css.'>';
							}else{
							$testimonials.='<div class="pt-plus-testimonial-item pt-plus-testimonial-item-'.esc_attr($style_class).' " "><div class="vc_col-md-10 vc_col-sm-10 vc_col-xs-12 test-equal-height" '.$bg_style6.' >';
							}
							
									if($test_style=="style_4"){
										$testimonials.='<div class="testi-title '.esc_attr($title_align).'" '.$ts_title.'>'.esc_html($testimonial_title).'</div>';
										
									}
									if($test_style=="style_3"){
										$testimonials.='<div class="vc_row ts-style-3" >';
										$testimonials.='<div class="content" >';
										$testimonials .= $thumbnail;
									}	
									if($test_style=="style_5"){
										$testimonials.='<div class="vc_row infor-client ">';
											$testimonials.='<div class="vc_col-md-4 vc_col-sm-5 vc_col-xs-12">';
												$testimonials.='<div class="client-name" '.$title_style.'>'.esc_html(get_the_title()).'</div>';
												$testimonials.='<div class="ts-desig" '.$desg_font.'>';
													$testimonials.= $test_designation;
												$testimonials.='</div>';
											$testimonials.='</div>';
											$testimonials.='<div class="vc_col-md-8 vc_col-sm-7 vc_col-xs-12">';
												 $testimonials.='<div class="content" '.$p_style.'>'.esc_html($testimonial_text).'</div>';
											$testimonials.='</div>';
										$testimonials.='</div>';
									}else{
											if($test_style!="style_3" && $test_style!="style_4" && $test_style !="style_6"){
												$testimonials.='<div class="vc_col-md-offset-1 vc_col-md-10 vc_col-sm-offset-1 vc_col-sm-10 vc_col-xs-12">';
											}
											if($test_style == "style_6"){
												$testimonials.='<div class="vc_row testimonials-6" >';
													$testimonials.='<div class="vc_col-md-2  vc_col-sm-2 vc_col-xs-12">';
														$testimonials .= $thumbnail;
													$testimonials.='</div>';
													
												if( $test_designation!=''){
													$desg=', <span class="clent-desig" '.$desg_font.'>'.esc_html($test_designation).'</span>';
													}else{
													$desg = '';
												}
													$testimonials.='<div class="vc_col-md-10  vc_col-sm-10 vc_col-xs-12">';
														$testimonials.='<div class="client-name" '.$title_style.'>'.esc_html(get_the_title()). $desg .' </div>';
														$testimonials.='<p class="testimonials-description '.esc_attr($text_alignment).'" '.$p_style.' >'.esc_html($testimonial_text).'</p>';
													$testimonials.='</div>';
											}
											if($test_style=="style_4"){
												$text_alignment=$title_align;
											}else{
												$text_alignment='';
											}
											if($test_style == "style_7"){
													$testimonials.='<div class="infor-client">';
														$testimonials.='<div class="icon-client">';
															$testimonials .= $thumbnail;
														$testimonials.='</div>';
													if( $test_designation!=''){
														$desg='<div class="clent-desig" '.$desg_font.'>'.esc_html($test_designation).'</div>';
													}else{
														$desg = '';
													}
													$testimonials.='<div class="client-name" '.$title_style.'>'.esc_html(get_the_title()). $desg .' </div>';
													$testimonials.='</div>';													
											}
											if($test_style != "style_6"){
											$testimonials.='<p class="testimonials-description  '.esc_attr($text_alignment).'" '.$p_style.' >'.esc_html($testimonial_text).'</p>';
											}
											if($test_style!="style_3" && $test_style!="style_4" && $test_style !="style_6"){
												$testimonials.='</div>';
											}
									}
									if($test_style !="style_6" && $test_style !="style_7"){
									$testimonials.='<div class="infor-client">';
									$testimonials.='<div class="icon-client">';
										if($test_style=="style_1"){	
											$testimonials .= $thumbnail;
										}
										if($test_style=="style_2"){
											if ( has_post_thumbnail() ) {
												$testimonials .='<div class="seprator sep-l" >';
												$testimonials .='<span class="title-sep sep-l" '.$icn_style.'></span>';                    
												$testimonials .='<div class="img-clint hvr-bob">';							
												$testimonials .= $thumbnail;
												$testimonials .='</div>';
												$testimonials .='<span class="title-sep sep-r" '.$icn_style.'></span>';
												$testimonials .='</div>';
											}	
										}
									$testimonials.='</div>';
									if( $test_designation!=''){
										$desg=', <span class="clent-desig" '.$desg_font.'>'.esc_html($test_designation).'</span>';
									}else{
										$desg = '';
									}
									if($test_style!= 'style_4' && $test_style!= 'style_5'){
										$testimonials.='<div class="client-name" '.$title_style.'>'.esc_html(get_the_title()). $desg .' </div>';
											
										if($testimonial_website != ''){
										$testimonials.='<div class="client-position"><a href="'.esc_url($testimonial_website).'" '.$web_style.'>'.esc_html($testimonial_website).'</a></div>';
										}
									}
								$testimonials.='</div>';
									}
								if($test_style=="style_3"){
											$testimonials.='</div>';
										$testimonials.='</div>';					
									}
								if($test_style=="style_6"){
								$testimonials.='</div>';
								$testimonials.='</div>';
								}
							$testimonials.='</div>';
								
								endwhile;
							endif;
						wp_reset_postdata();
							if($test_style== 'style_4' || $test_style== 'style_5'){
							$testimonials.='</div>';
							$testimonials.='</div>';
							}else{
							$testimonials.='</div>';
							$testimonials.='</div>';
							}
						
						/*--------------------------------------- style -4 -------------------------------------------------------------*/
						 if($test_style== 'style_4'){
						  $testimonials.='</div>';
						   $testimonials.='</div>';
						
						 $testimonials .='<div class="vc_row">';
						   $testimonials .='<div class="'.$nav_offset.'">';
						
						 $testimonials.='<div class="pt-plus-testi-nav testi-inner-loop nav-'.esc_attr($test_style).' '.esc_attr($arrow_class).'  pt-plus-testi-nav-'.esc_attr($rand_no).'" data-tmnav-unique="pt-plus-testi-nav-'.esc_attr($rand_no).'"  data-style="'.esc_attr($test_style).'">';
						if($testi_qry->have_posts()) :
								while($testi_qry->have_posts()) : $testi_qry->the_post(); 
									$testimonial_text = get_post_meta(get_the_id(), 'theplus_testimonial_author_text', true);
									$testimonial_website= get_post_meta(get_the_id(), 'theplus_testimonial_website_url', true);
									$post_bg = get_post_meta(get_the_id(), 'theplus_testimonial_bg_img', true);
									$test_designation = get_post_meta(get_the_id(), 'theplus_testimonial_designation', true);
									
								if ( has_post_thumbnail() ) {
									$thumbnail=get_the_post_thumbnail($testi_qry->ID, 'ts-image-grid', array('title' => ''));
								}else{
									$thumbnail ='';
								}
								
								
									$testimonials.='<a class="url">';
										$testimonials.='<div class="pt-plus-testimonials-item ">';				 
											 $testimonials.='<div class="img-client text-center" data-id="'.esc_attr(get_the_ID()).'">';
													$testimonials.='<div class="testimonails-style-img  text-center ">';   
																		  $testimonials .= $thumbnail;
													$testimonials.='</div>';   
												$testimonials.='<div class="testimonails-style-desg-clinet  text-center " '.$desgnation_back.'>';  
													 $testimonials.='<div class="client-name" '.$title_style.'>'.esc_html(get_the_title()).'</div>';
													 if($test_designation !=''){
														$testimonials.='<div class="ts-desig  text-center" '.$desg_font.'>'.esc_html($test_designation).'</div>';			 
													 }	
												$testimonials.='</div>';
														 
											 $testimonials.='</div>';
										 $testimonials.='</div>';
									$testimonials.=' </a>';
								endwhile;
							endif;
							wp_reset_postdata();
								$testimonials.='</div>';
							$testimonials.='</div>';
						$testimonials.='</div>';
							 }
						/*--------------------------------------- style -4 -------------------------------------------------------------*/	
					$testimonials.='</div>';	
					
					return $testimonials;
		}
		function init_tp_testimonial_slider(){
		$taxonomy_name=pt_plus_testimonial_post_category();
			if(function_exists("vc_map"))
			{
					$testimonial_post=pt_plus_get_option('post_type','testimonial_post_type');
						if((!isset($testimonial_post) || $testimonial_post=='') || (!empty($testimonial_post) && $testimonial_post=='disable')){
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
				vc_map( array(
					  "name" => __( "Testimonials", "pt_theplus" ),
					  "base" => "tp_testimonial_slider",
					  'icon'	=> 'tp-testimonials',
					  "description" => esc_html__('Client Words in Style', 'pt_theplus'),
					  "category" => __( "The Plus", "pt_theplus"),
					  "params" => array(
					  $custom_post_type,
					  array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Testimonials Style', 'pt_theplus'),
								'param_name'  => 'test_style',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> 'style_1',
								'options'     => array(
									'style_1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-1.jpg'
									),
									'style_2' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-2.jpg'
									),
									'style_3' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-3.jpg'
									),
									'style_4' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-4.jpg'
									),
									'style_5' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-5.jpg'
									),
									'style_6' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-6.jpg'
									),
									'style_7' => array(
										'tooltip' => esc_attr__('Style-7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/testimonials/testimonial-style-7.jpg'
									),
								),
								
							),
						array(
								"type" => "textfield",                
							   'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add title of Testimonial using this option','pt_theplus').'</span></span>'.esc_html__('Testimonial Title', 'pt_theplus')),
								"param_name" => "testimonial_title",
								"value" => 'Clients Feedback',
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
							),
						array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Testimonials Title Setting', 'pt_theplus'),
						   'param_name'  => 'title_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						   "dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
						  ),
						 array(
								"type" => "textfield",
								 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "title_font",
								"value" => '30px',
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
								'edit_field_class' => 'vc_col-sm-6',
							   "description" => __( "  ", 'pt_theplus' ),
							   "group" =>'Styles',
							),
						
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "title_line_height",
								"group" =>'Typography',
								"value" => '1.4',				
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
								'edit_field_class' => 'vc_col-sm-6',
								'group' => esc_attr__('Styles', 'pt_theplus'),
							   "description" => "",
							),

						array(
								"type" => "textfield",                
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "title_spacing",
								"group" =>'Typography',
								"value" => '1px',				
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								'edit_field_class' => 'vc_col-sm-4',
							   "description" => "",
							),			
						 array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"heading" => __("Testimonial Title Font Color", 'pt_theplus'),
								"param_name" => "ts_color",
								"value" => '#121212',
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
								'group' => esc_attr__('Styles', 'pt_theplus'),
								 "edit_field_class" => "vc_col-xs-4",
							),	
							  array(
								"type" => "dropdown",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Testimonial alignment Style from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Alignment ', 'pt_theplus')),
								"param_name" => "testimonial_align",
								'value' => array(
									__( 'Left', 'pt_theplus' ) => 'left',
									__( 'Right', 'pt_theplus' ) => 'right',
									__( 'Center', 'pt_theplus' ) => 'center',
								),
								'std'=> 'left',
								"dependency" => array(
									"element" => "test_style",
									"value" => "style_4",
								),
								"edit_field_class" => "vc_col-xs-4",
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
								"dependency" => array(
									"element" => "test_style",
									"value" => array("style_4","style_7")
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
							'value' => __('400','pt_theplus'),
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
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Author Setting', 'pt_theplus'),
						   'param_name'  => 'author_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						  ),
						  array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "author_size",
								"group" =>'Styles',
								"value" => '18px',
								"edit_field_class" => "vc_col-xs-6",
							),
						
						array(
								"type" => "textfield",  
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "author_line_height",
								"group" =>'Styles',
								"value" => '1.4',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "author_spacing",
								"group" =>'Styles',
								"value" => '1px',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),	
						 array(
								"type" => "colorpicker",
							   'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "author_name",
								"value" => '#121212',
								"description" => "",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							),
								array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Background Setting', 'pt_theplus'),
						   'param_name'  => 'background_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						   "dependency" => array(
									"element" => "test_style",
									"value" => array("style_3","style_4","style_6"),
								),
						  ),
						 array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
								"param_name" => "back_color",
								"value" => '#121212',
							   "description" => "",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							   "dependency" => array(
									"element" => "test_style",
									"value" => array("style_3","style_4","style_6","style_7"),
								),
							),
						array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for background using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
								"param_name" => "border_color",
								"value" => '#f6f6f6',
							   "description" => "",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							   "dependency" => array(
									"element" => "test_style",
									"value" => array("style_7"),
								),
							),
						array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Designation Setting', 'pt_theplus'),
						   'param_name'  => 'desgination_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						  ),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "desig_size",
								"group" =>'Styles',
								"value" => '16px',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "desig_line_height",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
								"value" => '1.4',
							   "description" => "",
							),
						array(
								"type" => "textfield",                
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "desig_spacing",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
								"value" => '1px',
							   "description" => "",
							),
						array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
								"param_name" => "desig_font_color",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
								"value" => '#000',
							   "description" => "",
							),
						array(
								"type" => "colorpicker",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for background color using this option.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
								"param_name" => "desig_color",
								"value" => '#121212',
							   "description" => "",
							   "group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							   "dependency" => array(
									"element" => "test_style",
									"value" => "style_4"
								),
							),
						array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Content Setting', 'pt_theplus'),
						   'param_name'  => 'content_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						  ),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "content_size",
								"group" =>'Styles',
								"value" => '24px',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "content_line_height",
								"group" =>'Styles',
								"value" => '1.4',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "content_spacing",
								"group" =>'Styles',
								"value" => '1px',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						 array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "con_clr",
							"value" => '#222', 
							"description" => "",
							"group" =>'Styles',
							"edit_field_class" => "vc_col-xs-6",
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
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'content_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styles', 'pt_theplus'),	
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
							'group' => esc_attr__('Styles', 'pt_theplus'),	
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
								'group' => esc_attr__('Styles', 'pt_theplus'),	
						),
						 array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Web Setting', 'pt_theplus'),
						   'param_name'  => 'web_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
						  ),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
								"param_name" => "web_size",
								"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
								"value" => '14px',
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
								"param_name" => "web_line_height",
								"group" =>'Styles',
								"value" => '1.4',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
								"param_name" => "web_spacing",
								"group" =>'Styles',
								"value" => '1px',
								"edit_field_class" => "vc_col-xs-6",
							   "description" => "",
							),	
							array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "web_color",
							"value" => '#121212',
							"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							"description" => ""
						 ),
								array(
						   'type'    => 'pt_theplus_heading_param',
						   'text'    => esc_html__('Border Setting', 'pt_theplus'),
						   'param_name'  => 'border_setting',
						   'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						   'group' => esc_attr__('Styles', 'pt_theplus'),
							"dependency" => array(
									"element" => "test_style",
									"value" => "style_2",
								),
						  ),
					   array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							"param_name" => "bdr_clr",
							"value" => '#222222', //Default Red color
							"group" =>'Styles',
								"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
									"element" => "test_style",
									"value" => "style_2",
								),
							"description" => ""
						 ),	 
						 array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Post Setting', 'pt_theplus'),
							'param_name'		=> 'post_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
								'type' => 'pt_theplus_taxonomy_multicheck',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose categories from which you want to show posts by marking one or multiple.','pt_theplus').'</span></span>'.esc_html__('Choose Categories', 'pt_theplus')),
								'param_name' => 'display_category',
								'taxonomy' => $taxonomy_name,
								'edit_field_class' => 'vc_column vc_col-sm-12 pt-plus-taxonomy-multicheck',
								'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							"admin_label" => true,
							"heading" => __("Maximum Posts", 'pt_theplus'),
							"param_name" => "display_post",
							"value" => '10',
							"description" => __("Please enter number of posts you want to display.", 'pt_theplus'),
							'group' => esc_attr__('Content', 'pt_theplus')
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
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Carousel Setting', 'pt_theplus'),
							'param_name'		=> 'carousel_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Carousel', 'pt_theplus'),			
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
							'std' =>'1',
							"edit_field_class" => "vc_col-xs-4",
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
							'std' =>'1',
							"edit_field_class" => "vc_col-xs-4",			 
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
								),
							'std' =>'1',
							"edit_field_class" => "vc_col-xs-4",			
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
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
						),
						array(
							  "type"        => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter speed of autoplay carousel functionality. e.g. 2000,3000 etc.','pt_theplus').'</span></span>'.esc_html__('Autoplay Speed', 'pt_theplus')),
							  "param_name"  => "autoplay_speed",
							  "value"       => '3000',
							  "edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
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
									'style-12' => array(
										'tooltip' => esc_attr__('Style-8','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-8.jpg'
									),
								),
								'group' => esc_attr__('Carousel', 'pt_theplus'),
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
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-6","style-7"),
							),
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Animation Settings', 'pt_theplus'),
						'param_name' => 'annimation_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						 array(
							  "type"        => "dropdown",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							  "param_name"  => "animation_effects",
							  "edit_field_class" => "vc_col-xs-6",
							  "admin_label" => false,
							  "value"       => array(
								__( 'No-animation', 'pt_theplus' )             => 'no-animation',
								__( 'FadeIn', 'pt_theplus' )             => 'transition.fadeIn',
								__( 'FlipXIn', 'pt_theplus' )            => 'transition.flipXIn',
							   __( 'FlipYIn', 'pt_theplus' )            => 'transition.flipYIn',
							   __( 'FlipBounceXIn', 'pt_theplus' )      => 'transition.flipBounceXIn',
							   __( 'FlipBounceYIn', 'pt_theplus' )      => 'transition.flipBounceYIn',
							   __( 'SwoopIn', 'pt_theplus' )            => 'transition.swoopIn',
							   __( 'WhirlIn', 'pt_theplus' )            => 'transition.whirlIn',
							   __( 'ShrinkIn', 'pt_theplus' )           => 'transition.shrinkIn',
							   __( 'ExpandIn', 'pt_theplus' )           => 'transition.expandIn',
							   __( 'BounceIn', 'pt_theplus' )           => 'transition.bounceIn',
							   __( 'BounceUpIn', 'pt_theplus' )         => 'transition.bounceUpIn',
							   __( 'BounceDownIn', 'pt_theplus' )       => 'transition.bounceDownIn',
							   __( 'BounceLeftIn', 'pt_theplus' )       => 'transition.bounceLeftIn',
							   __( 'BounceRightIn', 'pt_theplus' )      => 'transition.bounceRightIn',
							   __( 'SlideUpIn', 'pt_theplus' )          => 'transition.slideUpIn',
							   __( 'SlideDownIn', 'pt_theplus' )        => 'transition.slideDownIn',
							   __( 'SlideLeftIn', 'pt_theplus' )        => 'transition.slideLeftIn',
							   __( 'SlideRightIn', 'pt_theplus' )       => 'transition.slideRightIn',
							   __( 'SlideUpBigIn', 'pt_theplus' )       => 'transition.slideUpBigIn',
							   __( 'SlideDownBigIn', 'pt_theplus' )     => 'transition.slideDownBigIn',
							   __( 'SlideLeftBigIn', 'pt_theplus' )     => 'transition.slideLeftBigIn',
							   __( 'SlideRightBigIn', 'pt_theplus' )    => 'transition.slideRightBigIn',
							   __( 'PerspectiveUpIn', 'pt_theplus' )    => 'transition.perspectiveUpIn',
							   __( 'PerspectiveDownIn', 'pt_theplus' )  => 'transition.perspectiveDownIn',
							   __( 'PerspectiveLeftIn', 'pt_theplus' )  => 'transition.perspectiveLeftIn',
							   __( 'PerspectiveRightIn', 'pt_theplus' ) => 'transition.perspectiveRightIn',
							  ),
							  'std' =>'no-animation',
						),		
						array(
							  "type"        => "textfield",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
							  "param_name"  => "animation_delay",
							  "edit_field_class" => "vc_col-xs-6",
							  "value"       => '50',
							  "description" => "",
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Extra Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
						  "type" => "textfield",
						  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You need to enter this value in pixel when you use background Image in each testimonial.','pt_theplus').'</span></span>'.esc_html__('Minimum Background Height', 'pt_theplus')),
						  "param_name" => "mini_height",
						  "value" => __( "300", "pt_theplus" ),
						  'edit_field_class' => 'vc_col-sm-6',		  
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Extra Class here to use for Customization Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),
					  )
				   ) );
			}
		}
	}
	new ThePlus_testimonial_slider;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_testimonial_slider'))
	{
		class WPBakeryShortCode_tp_testimonial_slider extends WPBakeryShortCode {
		   protected function contentInline( $atts, $content = null ) {
			 
		 }
		}
	}
}